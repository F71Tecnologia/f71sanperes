<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include('../../funcoes.php');
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];


// Buscando a Folha
if(isset($_REQUEST['enc'])){
    list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
} else {
    $regiao = $_REQUEST['id_regiao'];
    $folha = $_REQUEST['id_folha'];
}
$link_voltar = 'ver_folha.php?enc=' . $_REQUEST['enc'];

$qr_folha = mysql_query("select A.id_folha, B.nome as nome_projeto, DATE_FORMAT(A.data_inicio,'%d/%m/%Y') as data_inicio, 
                        DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim, C.nome_mes, A.ids_movimentos_estatisticas
                        FROM rh_folha as A 
                        INNER JOIN projeto as B ON B.id_projeto = A.projeto
                        INNER JOIN ano_meses as C ON C.num_mes = A.mes
                        WHERE A.id_folha = $folha");
$row_folha = mysql_fetch_assoc($qr_folha);

//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

///SELECT MOVIMENTOS DE CRÉDITO
$mov_cred = montaQuery('rh_movimentos', "id_mov, descicao", "(mov_lancavel = 1 AND categoria = 'CREDITO') ", 'id_mov');
$opt_mov_cre = array();
$opt_mov_cred[''] = "Selecione...";
$opt_mov_cred['215'] = "SALÁRIO LÍQUIDO";

foreach ($mov_cred as $valor) {
    $opt_mov_cred[$valor['id_mov']] = $valor['id_mov'] . ' - ' . $valor['descicao'];
}

$moc_credSel = (isset($_REQUEST['mov_cred'])) ? $_REQUEST['mov_cred'] : '';

///SELECT MOVIMENTOS DE DÉBITO
$mov_deb = montaQuery('rh_movimentos', "id_mov, descicao", "mov_lancavel = 1 AND categoria IN('DEBITO', 'DESCONTO')", 'id_mov');
$opt_mov_deb = array();
$opt_mov_deb[''] = "Selecione...";

foreach ($mov_deb as $valor) {
    $opt_mov_deb[$valor['id_mov']] = $valor['id_mov'] . ' - ' . $valor['descicao'];
}

$moc_debSel = (isset($_REQUEST['mov_deb'])) ? $_REQUEST['mov_deb'] : '';

if (isset($_POST['gerar'])) {

    $id_mov_cred = $_POST['mov_cred'];
    $id_mov_deb = $_POST['mov_deb'];
    
    $auxCredito = $auxDebito = null;

    if (!empty($id_mov_cred) and !empty($id_mov_deb)) {
        $ids_mov = $id_mov_cred . ',' . $id_mov_deb;
    } elseif (!empty($id_mov_cred)) {
        $ids_mov = $id_mov_cred;
    } elseif (!empty($id_mov_deb)) {
        $ids_mov = $id_mov_deb;
    }
    
    if(!empty($id_mov_cred)){
        $auxCredito = ",
        (SELECT descicao FROM rh_movimentos WHERE id_mov = '$id_mov_cred') as nome_credito,    
        (SELECT valor_movimento FROM rh_movimentos_clt WHERE id_clt = folha.id_clt AND id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND id_mov = '$id_mov_cred' AND status = 5) as credito";
    }
    if (!empty($id_mov_deb)) {
        $auxDebito = ",
        (SELECT descicao FROM rh_movimentos WHERE id_mov = '$id_mov_deb') as nome_debito,
        (SELECT valor_movimento FROM rh_movimentos_clt WHERE id_clt = folha.id_clt AND id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND id_mov = '$id_mov_deb' AND status = 5) as debito";
    }
    
    $qr_folha_proc = "
    SELECT A.*, B.nome funcao FROM (
        SELECT folha.*,
            (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=folha.id_clt AND id_curso_de <> id_curso_para AND data_proc > CONCAT(ano,'-',mes,'-','28') ORDER BY id_transferencia ASC LIMIT 1) AS cursoDe, 
            (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=folha.id_clt AND id_curso_de <> id_curso_para AND data_proc < CONCAT(ano,'-',mes,'-','28') ORDER BY id_transferencia DESC LIMIT 1) AS cursoPara 
            $auxCredito $auxDebito
        FROM rh_folha_proc as folha WHERE id_folha = $row_folha[id_folha] ) AS A
        LEFT JOIN curso B ON (IF(A.cursoPara IS NOT NULL,A.cursoPara,IF(A.cursoDe IS NOT NULL,A.cursoDe, A.id_curso)) = B.id_curso)
    ORDER BY A.nome";
    $qr_folha_proc = mysql_query($qr_folha_proc) or die(mysql_error());
}
?>
<html>
    <head>
        <title>Gerar IRRF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        
        
        <style media="print">
            form{ visibility: hidden;}
            .link_voltar{ visibility: hidden;}            
        </style>
    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <div class="link_voltar"><a href="<?php echo $link_voltar; ?>" title="Voltar para a folha"> <img src="../../imagens/back.png" width="30" height="30"/> </a> </div>
                <img src="../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Relatório de movimentos</h2>  
                    <p><strong><?php echo $row_folha['nome_mes']; ?></strong></p>
                    <p><strong>Folha:</strong> <?php echo $row_folha['id_folha']; ?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio']; ?> a  <?php echo $row_folha['data_fim']; ?> </p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>Dados</legend>
                    <div class="fleft">
                        <p><label class="first">Crédito:</label> <?php echo montaSelect($opt_mov_cred, $moc_credSel, array('name' => "mov_cred", 'id' => 'mov_cred')); ?></p>
                        <p><label class="first">Débito:</label>  <?php echo montaSelect($opt_mov_deb, $moc_debSel, array('name' => "mov_deb", 'id' => 'mov_deb')); ?></p>

                    </div>

                    <br class="clear"/>                
                    <p class="controls" style="margin-top: 10px;">
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
            <div class="clear"></div>

            <?php if (isset($_POST['gerar'])) { ?>

                <?php
                $contador = 0;
                while ($row_folha_proc = mysql_fetch_assoc($qr_folha_proc)) {

                    $contador++;

                    if ($id_mov_cred == 215) {
                        $credito = $row_folha_proc['salliquido'];
                        $nome_credito = 'SALÁRIO';
                    } else {
                        $credito = $row_folha_proc['credito'];
                        $nome_credito = $row_folha_proc['nome_credito'];
                    }

                        if ($contador == 1) {
                        ?>  <br/><br/>
                            <a style="display: inline" class="botao btn btn-success" href="#" onclick="tableToExcel('tbRelatorio', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a>
                            <a style="display: inline" type="button" name="pdf" data-title="Relatório de Movimentos" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="botao btn btn-success"><i class="fa fa-file-pdf-o"></i> Gerar PDF</a>
                            <table border="0" cellpadding="0" cellspacing="0" id="tbRelatorio" class="grid" width="60%" align="center">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Função</th>
                                        <?php if(!empty($id_mov_cred)){ ?><th><?php echo $nome_credito; ?></th><?php } ?>
                                        <?php if(!empty($id_mov_deb)){ ?><th><?php echo $row_folha_proc['nome_debito'] ?></th><?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                        <?php
                        }
                        ?>
                        <tr class="<?php echo ($contador %2) ? "even":"odd"; ?>"> 
                            <td><?php echo $row_folha_proc['nome']; ?></td>
                            <td><?php echo $row_folha_proc['funcao']; ?></td>
                            <?php if(!empty($id_mov_cred)){ ?><td class="txright"><?php echo $credito; ?></td><?php } ?>
                            <?php if(!empty($id_mov_deb)){ ?><td class="txright"><?php echo $row_folha_proc['debito']; ?></td><?php } ?>
                        </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!--<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>-->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
    </body>
</html>