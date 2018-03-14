<?php 
include('../conn.php');
include('../classes/global.php');
include('../classes/clt.php');
include("../classes/FeriasClass.php");
include('../wfunction.php');
include('../funcoes.php');
include('../upload/classes.php');
include('../classes/funcionario.php');
include('../classes/formato_data.php');
include('../classes/formato_valor.php');
include('../classes/EventoClass.php');
include('../classes_permissoes/acoes.class.php');
include ("../classes/LogClass.php");

$id = $_POST['id_clt_ret_inss'];
$bol = $_POST['id_bol_ret_inss'];
$ano = $_POST['ano'];
$mes = $_POST['num_mes'];
//$mes_select = $mes - 1;
$mes_select = $mes;

$tipoFerias = 0;
if ($mes == 16) {
    $tipoFerias = 1;

}

if ($mes == 13) {
    $tipoTerceiro = 3;
} else if ($mes == 15) {
    $tipoTerceiro = 2;
} 
/**
 * @author Lucas Praxedes Serra (17/01/2017 - 15:36)
 * A PEDIDO DA PATY, AGORA É POSSÍVEL GERAR A CARTA DE RETENÇÃO MESMO QUE A FOLHA ESTEJA ABERTA
 */
if(!empty($id)){
    if ($mes <= 12) {
        $sql_inss = "SELECT a.nome,a.pis,a.cpf,a.id_clt, c.nome AS nome_funcao,(b.inss + b.inss_ferias) inss, b.base_inss,b.inss_ferias, f.status, b.base_inss_atualizacao
                        FROM rh_clt AS a
                        INNER JOIN rh_folha_proc AS b ON a.id_clt = b.id_clt AND b.status != 0
                        INNER JOIN rh_folha AS f ON b.id_folha = f.id_folha AND b.status != 0
                        INNER JOIN curso AS c ON a.id_curso = c.id_curso 
                        WHERE a.id_clt = {$id} AND f.terceiro = 2 AND f.mes = {$mes}  AND f.ano = {$ano}
                        ORDER BY f.ano,f.mes DESC LIMIT 1";
        $qr_inss = mysql_query($sql_inss);
        $row = mysql_fetch_assoc($qr_inss);
    } else if ($mes == 16) {
        $sql_inss = "SELECT a.nome,a.pis,a.cpf,a.id_clt, c.nome AS nome_funcao
                        FROM rh_clt AS a
                        INNER JOIN curso AS c ON a.id_curso = c.id_curso 
                        WHERE a.id_clt = {$id}";
        $qr_inss = mysql_query($sql_inss);
        $row = mysql_fetch_assoc($qr_inss);
    } else if ($mes == 13 || $mes == 15) {
        $sql_inss = "SELECT a.nome,a.pis,a.cpf,a.id_clt, c.nome AS nome_funcao,b.inss_dt inss, b.base_inss,b.inss_ferias, f.status, b.base_inss_atualizacao
                        FROM rh_clt AS a
                        INNER JOIN rh_folha_proc AS b ON a.id_clt = b.id_clt AND b.status != 0
                        INNER JOIN rh_folha AS f ON b.id_folha = f.id_folha AND b.status != 0
                        INNER JOIN curso AS c ON a.id_curso = c.id_curso 
                        WHERE a.id_clt = {$id} AND f.terceiro = 1 AND f.tipo_terceiro = $tipoTerceiro AND f.ano = $ano
                        ORDER BY f.ano,f.mes DESC LIMIT 1";
        $qr_inss = mysql_query($sql_inss);
        $row = mysql_fetch_assoc($qr_inss);
    }
    
    if ($_COOKIE['debug'] == 666) {
        echo '/////////////////QUERY CLT//////////////////';
        print_array($sql_inss);
    }
    
    if($tipoFerias){
        //VERIFICA SE ESTÁ DE FÉRIAS
        $sqlFerias = "SELECT *, DATE_FORMAT(data_fim, '%d-%m-%Y') AS data_fim, DATE_FORMAT(data_ini, '%d-%m-%Y') AS data_ini, DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(data_fim, INTERVAL 1 MONTH)), 1), '%d-%m-%Y') AS data_ini2, DATE_FORMAT(LAST_DAY(data_ini), '%d-%m-%Y') AS ultimo_dia
                      FROM rh_ferias A
                      WHERE id_clt = {$id} AND YEAR(data_ini) = {$ano} AND STATUS = 1
                      ORDER BY id_ferias DESC";
        if ($_COOKIE['debug'] == 666) {
            echo '/////////////////QUERY FÉRIAS//////////////////';
            print_array($sqlFerias);
        }
        $query_ferias = mysql_query($sqlFerias);
        $ver_ferias = mysql_fetch_array($query_ferias);
        $inss = $ver_ferias['inss'];
        $row['base_inss'] = $ver_ferias['base_inss'];
        
        while ($rowFerias = mysql_fetch_array($query_ferias)) {
            $inss += $rowFerias['inss'];
            $row['base_inss'] += $rowFerias['base_inss'];
        }
        
    } else {
        if ($row['status'] != 3) {
            $baseInss = $row['base_inss_atualizacao'];
            $sqlInss = "SELECT v_ini, v_fim, percentual, piso, teto FROM rh_movimentos WHERE cod = 5020 AND anobase = $ano";
            $queryInss = mysql_query($sqlInss);
            while ($rowInss = mysql_fetch_assoc($queryInss)) {
                if ($baseInss >= $rowInss['v_ini'] && $baseInss <= $rowInss['v_fim']) {
                    $inss = $baseInss * $rowInss['percentual'];
                    
                    if ($inss < $rowInss['piso']) {
                        $inss = $rowInss['piso'];
                    } else if ($inss > $rowInss['teto']) {
                        $inss = $rowInss['teto'];
                    }
                }
            }
            $row['base_inss'] = $baseInss;
        } else {
            $inss = $row['inss'];
        }
    }
}
if(!empty($bol)){
   $qr_inss = mysql_query(" SELECT a.nome,a.pis,a.cpf,a.id_autonomo, c.nome AS nome_funcao,(b.inss + b.inss_ferias) inss, b.base_inss,b.inss_ferias
            FROM autonomo AS a
            INNER JOIN rh_folha_proc AS b ON a.id_autonomo = b.id_clt AND b.status != 0
            INNER JOIN rh_folha AS f ON b.id_folha = f.id_folha AND b.status != 0
            INNER JOIN curso AS c ON a.id_curso = c.id_curso 
            WHERE a.id_autonomo = {$bol} AND f.terceiro = 2 AND f.mes = {$mes}  AND f.ano = {$ano}
            ORDER BY f.ano,f.mes DESC LIMIT 1");
    $row = mysql_fetch_assoc($qr_inss);
    $inss = $row['inss'];
}



switch ($mes_select) {
        case 1:    $mes_selec = "Janeiro";     break;
        case 2:    $mes_selec = "Fevereiro";   break;
        case 3:    $mes_selec = "Março";       break;
        case 4:    $mes_selec = "Abril";       break;
        case 5:    $mes_selec = "Maio";        break;
        case 6:    $mes_selec = "Junho";       break;
        case 7:    $mes_selec = "Julho";       break;
        case 8:    $mes_selec = "Agosto";      break;
        case 9:    $mes_selec = "Setembro";    break;
        case 10:    $mes_selec = "Outubro";     break;
        case 11:    $mes_selec = "Novembro";    break;
        case 12:    $mes_selec = "Dezembro";    break; 
        case 13:    $mes_selec = "13º - Integral";    break; 
        case 15:    $mes_selec = "13º - Segunda Parcela";    break; 
        case 16:    $mes_selec = "Férias";    break; 
 }
 
 switch (date("m")) {
        case "01":    $mes_atual = "Janeiro";     break;
        case "02":    $mes_atual = "Fevereiro";   break;
        case "03":    $mes_atual = "Março";       break;
        case "04":    $mes_atual = "Abril";       break;
        case "05":    $mes_atual = "Maio";        break;
        case "06":    $mes_atual = "Junho";       break;
        case "07":    $mes_atual = "Julho";       break;
        case "08":    $mes_atual = "Agosto";      break;
        case "09":    $mes_atual = "Setembro";    break;
        case "10":    $mes_atual = "Outubro";     break;
        case "11":    $mes_atual = "Novembro";    break;
        case "12":    $mes_atual = "Dezembro";    break; 
 }

?>
<!DOCTYPE html>
    <html lang="pt">
        <head>
            <title>:: Intranet :: Retenção INSS</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link rel="shortcut icon" href="../favicon.ico">
            <style>
                * { margin: 0; padding: 0; }
            </style>
            <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
            <link href="../resources/css/style-print.css" rel="stylesheet">
            <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
            <script src="../resources/js/print.js" type="text/javascript"></script>
            
        </head>
        <body>
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="text-center"> 
                        <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
                    </div>
                </div>
            </nav>
            <div class="pagina">
                <br>
                <br>
                <div class="text-center">
                    <img src="../imagens/logomaster1.gif" alt="logo"><br>
                    <span class="text-sm">São Paulo</span>
                </div>

                <br>
                <br>
                <h4 class="text-center">DECLARAÇÃO DE OPÇÃO DE RETENÇÃO DE INSS</h4>

                <p class="text-center"><strong>Competência <?= $mes_selec ?> / <?php echo $ano; ?></strong></p>
                <br>
                <br>
                <p class="text-justify">
                    Em atendimento ao disposto na Instrução Normativa INSS/DC 087, de 27 de 
                    março de 2003, Lei 10.666 de 8 de  maio de 2003 e Decreto 4.729 de 9 de 
                    junho de 2003,
                </p>
                <p class="text-justify">
                    eu , <strong><?= $row['nome'] ?></strong>, inscrito no 
                    INSS sob o nº <strong><?= $row['pis'] ?></strong>, CPF nº 
                    <strong><?= $row['cpf'] ?></strong>, declaro que
                    o INSTITUTO DE ATENÇÃO BÁSICA E AVANÇADA À SAÚDE-IABAS, inscrita no 
                    CNPJ/MF sob o nº 09.652.823/0003-38, efetuará a retenção e o 
                    recolhimento da contribuição previdenciária individual até o limite de 
                    contribuição vigente, atualmente de R$ 
                    <strong><?= number_format($inss,2,',','.') ?></strong> 
                    (<strong><?= valor_extenso($inss,2,',','.') ?></strong>) sobre a base de R$ 
                    <strong><?= number_format($row['base_inss'],2,',','.') ?></strong> 
                    (<strong><?= valor_extenso($row['base_inss']) ?></strong>), relativa ao 
                    exercício do cargo de <strong><?= $row['nome_funcao'] ?></strong>.
                </p>
                <p class="text-justify">
                    Responsabilizo-me e comprometo-me a informar qualquer alteração referente a esta declaração e estarei ciente de que deverei renová-la a cada seis meses, ou quando houver modificação nos valores da tabela de retenção do INSS.
                    Esta declaração está assinada por mim e pela empresa que efetuará a retenção do INSS devido. 
                </p>
                <br>
                <br>
                <p class="text-right">São Paulo, <?= date("d") ?> de <?= $mes_atual ?> de  <?= $ano ?>. </p>

                <br>
                <br>
                <br>

                <div class="row">
                    <div class="col-xs-6 text-center">
                        <br>
                        ________________________________<br>
                        <span class="text-sm"><?= $row['nome'] ?></span>
                    </div>
                    <div class="col-xs-6 text-center">
                        <img src="../imagens/asshasbc.jpg" style="z-index: -1; width: 180px; margin: -200px 0 -200px;">  

                        ________________________________<br>
                        <span class="text-sm">INSTITUTO DE ATENÇÃO BÁSICA E AVANÇADA À SAÚDE-IABAS</span>
                    </div>
                </div>
            </div>
        </body>
    </html>