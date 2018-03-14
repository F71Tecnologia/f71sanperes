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

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);


$clt = $_GET['id'];
$id_reg = $_GET['regiao'];
$pro = $_GET['projeto'];

$result_bol = mysql_query("SELECT A.id_regiao,A.id_projeto,A.id_clt, A.nome, A.endereco, A.numero, A.complemento, A.bairro, A.cidade, A.uf, A.nacionalidade, A.tel_fixo, A.tel_cel, A.tel_rec, A.foto, A.sexo, date_format(A.data_nasci, '%d/%m/%Y') AS data_nasci, A.naturalidade, A.civil,
                                A.cpf, A.rg, A.uf_rg, date_format(A.data_rg, '%d/%m/%Y') AS data_rg, A.orgao, A.pai, A.nacionalidade_pai, A.mae, A.nacionalidade_mae, A.campo1, A.serie_ctps, A.uf_ctps,date_format(A.data_ctps, '%d/%m/%Y') AS data_ctps,
                                A.titulo, A.zona, A.secao, A.reservista, A.escolaridade, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada, A.rh_sindicato, A.fgts, A.agencia, A.conta, A.pis,date_format(A.dada_pis, '%d/%m/%Y') AS data_pis, 
                                IF(C.id_transferencia IS NULL, A.id_curso, C.id_curso_de) AS id_curso, IF(C.id_transferencia IS NULL, A.rh_horario, C.id_horario_de) AS rh_horario,
				IF(C.id_unidade_de IS NULL, A.id_unidade,C.id_unidade_de) AS id_unidade
                                FROM rh_clt AS A
                                LEFT JOIN (
                                        SELECT *
                                        FROM rh_transferencias
                                        WHERE id_clt = $clt
                                        ORDER BY id_transferencia ASC
                                        LIMIT 1) AS C ON (A.id_clt = C.id_clt)
                                WHERE A.id_clt = $clt");
$row = mysql_fetch_array($result_bol);
$data_entrada = implode('-', array_reverse(explode('/', $row['data_entrada'])));

$qr_unidade = "SELECT * FROM unidade WHERE id_unidade = {$row['id_unidade']}";
$unidade_ini = mysql_fetch_assoc(mysql_query($qr_unidade));
$qr_curso = "SELECT * FROM curso WHERE id_curso = {$row['id_curso']}";
$curso_ini = mysql_fetch_assoc(mysql_query($qr_curso));


$pro = $row['id_projeto'];
$id_reg = $row['id_regiao'];

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


$result_reci = mysql_query("SELECT *,date_format(data_demi, '%d/%m/%Y') AS data_demibr FROM rh_recisao WHERE id_clt = $clt  AND status = 1");
$row_reci = mysql_fetch_array($result_reci);
$row_demi = array();
if ($row_reci['data_demi'] == "") {
    $row_demi['data_saida'] = "";
    $row_demi['homologacao'] = "";
    $row_demi['desligamento'] = "";
    $row_demi['local_homolog'] = "";
} else {
    $row_demi['data_saida'] = $row_reci['data_demibr'];
    $row_demi['homologacao'] = "";
    $qr_status = mysql_query("SELECT * FROM rhstatus WHERE codigo = '{$row['status']}'");
    $row_status = mysql_fetch_assoc($qr_status);
    $row_demi['desligamento'] = $row_status['especifica'];
    $row_demi['local_homolog'] = "";
}

//    $result_horario = mysql_query("Select *, date_format(entrada_1, '%H:%i') as entrada_1br, date_format(saida_1, '%H:%i') as saida_1br, date_format(entrada_2, '%H:%i') as entrada_2br, date_format(saida_2, '%H:%i') as saida_2br from rh_horarios where funcao = '{$row['id_curso']}' AND id_horario = '{$row['rh_horario']}'");
// echo "<!-- Select *, date_format(entrada_1, '%H:%i') as entrada_1br, date_format(saida_1, '%H:%i') as saida_1br, date_format(entrada_2, '%H:%i') as entrada_2br, date_format(saida_2, '%H:%i') as saida_2br from rh_horarios where id_horario = '{$row['rh_horario']}' -->";
$result_horario = mysql_query("Select *, date_format(entrada_1, '%H:%i') as entrada_1br, date_format(saida_1, '%H:%i') as saida_1br, date_format(entrada_2, '%H:%i') as entrada_2br, date_format(saida_2, '%H:%i') as saida_2br from rh_horarios where id_horario = '{$row['rh_horario']}'");
$row_horario = mysql_fetch_array($result_horario);

/* CONTRIBUÍÇÃO SINDICAL */
$qr_cont_sind = mysql_query("SELECT A.id_clt,A.mes,A.ano,A.data_proc,A.a5019,B.rh_sindicato,C.nome FROM rh_folha_proc AS A
LEFT JOIN rh_clt AS B ON (A.id_clt=B.id_clt)
LEFT JOIN rhsindicato AS C ON (B.rh_sindicato=C.id_sindicato)
WHERE A.id_clt = '{$clt}' AND A.a5019 != 0
UNION ALL
SELECT a.id_clt,a.mes_mov AS mes,a.ano_mov AS ano, a.data_movimento AS data_proc, a.valor_movimento AS a5019, b.rh_sindicato, c.nome
FROM rh_movimentos_clt AS a
INNER JOIN rh_clt AS b ON a.id_clt = b.id_clt
INNER JOIN rhsindicato as c ON b.rh_sindicato = c.id_sindicato 
WHERE  a.id_clt = '{$clt}' AND a.id_mov IN(21,375) #AND a.status_folha = 1 # esse status_folha está 0 em todos os movimentos desse tipo
UNION ALL
SELECT a.id_clt,DATE_FORMAT(d.data_movimento,'%m') AS mes,a.ano_mov AS ano, d.data_movimento AS data_proc, a.valor AS a5019, b.rh_sindicato, c.nome
FROM rh_movimentos_rescisao AS a
INNER JOIN rh_clt AS b ON a.id_clt = b.id_clt
INNER JOIN rhsindicato AS c ON b.rh_sindicato = c.id_sindicato
INNER JOIN rh_movimentos_clt d ON a.id_movimento = d.id_movimento
WHERE a.id_clt = '{$clt}' AND a.id_mov IN(21,375) 
ORDER BY data_proc DESC;");


/* FÉRIAS */
$query_ferias = "SELECT *, date_format(data_aquisitivo_ini, '%d/%m/%Y') as data_aquisitivo_ini, date_format(data_aquisitivo_fim, '%d/%m/%Y') as data_aquisitivo_fim, date_format(data_ini, '%d/%m/%Y') as data_ini, date_format(data_fim, '%d/%m/%Y') as data_fim, date_format(data_proc, '%d/%m/%Y') as data_proc FROM rh_ferias WHERE id_clt = '$clt' AND status = 1";

$qr_ferias = mysql_query($query_ferias);
$objFerias = new Calculo_Ferias();
$objFerias->setIdClt($clt);
$periodos_gozados = $objFerias->getPeriodosGozados2();
$periodos_disponiveis = $objFerias->getPeriodoAquisitivo($data_entrada, NULL, 1, $periodos_gozados);
/**
 * ESSES SÃO OS PERIODOS AQUISITIVOS QUE FORAM TIRADOS DO FUNCIONÁRIO POR QUANTIDADE DE DIAS DE 
 * LICENÇA MAIOR QUE 30 DENTRO DO PERIODO.
 */
$p = $objFerias->getPeriodosNegados();

/**
 * MOTIVOS PELO O QUAL FOI DESCONSIDERADO FÉRIAS EM DOBRO PARA O FUNCIONARIO
 */
$qry_motivos_ferias_dobro = "SELECT A.*, B.nome, DATE_FORMAT(A.criado_em,'%d/%m/%Y %H:%i:%s') AS data_br
                                FROM his_ferias_dobro_canceladas AS A
                                LEFT JOIN funcionario AS B ON(A.criado_por = B.id_funcionario)
                                WHERE A.id_clt = '{$clt}'";
$sql_motivos_ferias_dobro = mysql_query($qry_motivos_ferias_dobro) or die("Erro ao selecionar motivo de ferias em dobro ter sido desconsiderada");
$motivos = array();
while ($rows_motivos = mysql_fetch_assoc($sql_motivos_ferias_dobro)) {
    $motivos["nome"] = $rows_motivos['nome'];
    $motivos["criado_em"] = $rows_motivos['data_br'];
    $motivos["motivo"][] = $rows_motivos['motivo'];
}


/* AFASTAMENTOS */
$qr_afasta = mysql_query("SELECT *, IF(cod_status IN (50, 30, 67, 69, 80),DATE_FORMAT(data, '%d/%m/%Y'),DATE_FORMAT(DATE_ADD(DATA, INTERVAL 15 DAY), '%d/%m/%Y')) AS databr, DATE_FORMAT(data_retorno, '%d/%m/%Y') AS data_retornobr FROM rh_eventos WHERE cod_status NOT IN (10,40,60,61,62,81,200,63,101,64,65,66) AND id_clt = {$clt}");
/* DEPENTES */
$qr_depen = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1br, date_format(data1, '%d/%m/%Y') AS data1br, date_format(data2, '%d/%m/%Y') AS data2br, date_format(data3, '%d/%m/%Y') AS data3br, date_format(data4, '%d/%m/%Y') AS data4br, date_format(data5, '%d/%m/%Y') AS data5br, date_format(data6, '%d/%m/%Y') AS data6br FROM dependentes 
                                WHERE id_bolsista = {$clt} AND (nome1!='' or nome2!='' or nome3!= '' or nome4 != '' or nome5 != '' or nome6 != '')");
$row_depen = mysql_fetch_assoc($qr_depen);

/* EXAMES */
$qr_exame = mysql_query("SELECT documento,date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status AS A
                                LEFT JOIN rh_documentos AS B ON (B.id_doc=A.tipo)
                                WHERE A.id_clt = {$clt} AND A.tipo IN (1,13) ORDER BY data DESC");

/* TRANSFERENCIAS DE UNIDADES */
$qr_transf = mysql_query("SELECT A.id_transferencia, B.nome AS de, C.nome AS para, A.motivo, date_format(A.data_proc, '%d/%m/%Y') AS data FROM rh_transferencias AS A 
                                LEFT JOIN projeto AS B ON (A.id_projeto_de = B.id_projeto)
                                LEFT JOIN projeto AS C ON (A.id_projeto_para = C.id_projeto) 
                                WHERE id_clt = {$clt} AND id_projeto_de <> id_projeto_para");

/* ALTERAÇÃO DE FUNÇÕES */
$qr_alt_funcao = mysql_query("SELECT A.id_transferencia, B.nome AS de, C.nome AS para,D.cod AS cbo_de,E.cod AS cbo_para, A.motivo, date_format(A.data_proc, '%d/%m/%Y') AS data 
                                    FROM rh_transferencias AS A 
                                    LEFT JOIN curso AS B ON (A.id_curso_de = B.id_curso)
                                    LEFT JOIN curso AS C ON (A.id_curso_para = C.id_curso)
                                    LEFT JOIN rh_cbo AS D ON (B.cbo_codigo = D.id_cbo)
                                    LEFT JOIN rh_cbo AS E ON (A.id_curso_para = E.id_cbo)
                                    WHERE id_clt = {$clt} AND B.nome <> C.nome");

/* ALTERAÇÃO DE SALÁRIO */
$qr_transf_sal = "SELECT id_curso_de, id_curso_para, data_proc FROM rh_transferencias WHERE id_curso_de <> id_curso_para AND id_clt = '$clt' AND status = 1";
$qr_transf_sal = mysql_query($qr_transf_sal);
$array = array();
while ($row_transf_sal = mysql_fetch_assoc($qr_transf_sal)) {
    $array[] = $row_transf_sal;
}
if (!empty($row_reci['data_demi'])) {
    $auxDemi = " AND data < '{$row_reci['data_demi']}' ";
}
if (!empty($array)) {
    for ($i = 0; $i <= count($array); $i++) {
        if ($i == 0) {
            $qr_alt_salario_or[] = "id_curso = '{$array[$i]['id_curso_de']}' AND data < '{$array[$i]['data_proc']}' AND data> '$data_entrada' AND status = 1";
        } elseif ($i == count($array) - 1) {
            $qr_alt_salario_or[] = "id_curso = '{$array[$i]['id_curso_de']}' AND data < '{$array[$i]['data_proc']}' AND data> '{$array[$i - 1]['data_proc']}' AND status = 1";
        } elseif ($i == count($array)) {
            $qr_alt_salario_or[] = "id_curso = '{$array[$i - 1]['id_curso_para']}' AND data> '{$array[$i - 1]['data_proc']}' AND status = 1 $auxDemi";
        } else {
            $qr_alt_salario_or[] = "id_curso = '{$array[$i]['id_curso_de']}' AND data < '{$array[$i]['data_proc']}' AND data> '{$array[$i - 1]['data_proc']}' AND status = 1";
        }
    }
    $qr_alt_salario[] = "(SELECT 'A' ordem, salario_antigo, salario_novo, data, motivo FROM rh_salario WHERE " . implode(" OR ", $qr_alt_salario_or) . " GROUP BY DATE_FORMAT(data,'%Y-%m'))";
} else {
    $qr_alt_salario[] = "(SELECT 'A' ordem, salario_antigo, salario_novo, data, motivo FROM rh_salario WHERE  id_curso = {$row['id_curso']} AND data> '$data_entrada' AND status = 1 $auxDemi GROUP BY DATE_FORMAT(data,'%Y-%m'))";
}
$qr_alt_salario[] = "(SELECT 'B' ordem, null, sallimpo salario_novo, (SELECT MAX(data_proc) FROM rh_transferencias WHERE id_clt = rh_folha_proc.id_clt AND MONTH(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.mes AND YEAR(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.ano AND id_curso_de <> id_curso_para) data, '' AS motivo FROM rh_folha_proc WHERE id_clt = {$row['id_clt']} AND status = 3 GROUP BY sallimpo ORDER BY data_proc LIMIT 1,100)";
//    $qr_alt_salario[] = "(SELECT 'B' ordem, null, sallimpo salario_novo,  z.data , z.motivo FROM rh_folha_proc LEFT JOIN (SELECT MAX(data_proc) AS data, motivo FROM rh_transferencias WHERE id_curso_de <> id_curso_para ) AS z ON (id_clt = rh_folha_proc.id_clt AND MONTH(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.mes AND YEAR(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.ano ) WHERE id_clt = {$row['id_clt']} AND status = 3 GROUP BY sallimpo ORDER BY data_proc LIMIT 1,100)";
$qr_alt_salario = "SELECT *, date_format(data, '%d/%m/%Y') AS dataBR FROM (SELECT * FROM (" . implode(' UNION ', $qr_alt_salario) . ") A ORDER BY ordem, data DESC) B WHERE data IS NOT NULL GROUP BY salario_novo ORDER BY data";
//    print_r($qr_alt_salario); exit;
//if($_COOKIE['logado'] == 349){
//    echo $qr_alt_salario;
//}

$qr_alt_salario = mysql_query($qr_alt_salario);

$result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = '{$row['id_curso']}'");
$row_bol3 = mysql_fetch_array($result_bol3);

$result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = '{$row['id_curso']}'");
$row_bol2 = mysql_fetch_array($result_bol2);

$result_reg = mysql_query("Select * from  regioes where id_regiao = '{$row['id_regiao']}'");
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("SELECT A.salario_antigo AS salario, B.nome
                                FROM rh_salario AS A
                                LEFT JOIN curso AS B ON (A.id_curso = B.id_curso)
                                WHERE A.id_curso = '{$row['id_curso']}' AND A.data>=  '$data_entrada'
                                LIMIT 1;");
$nrRow = mysql_num_rows($result_curso);
if ($nrRow != 0) {
    $row_curso = mysql_fetch_array($result_curso);
} else {
    $result_curso = mysql_query("SELECT A.salario AS salario, A.nome
                                FROM curso AS A
                                WHERE A.id_curso = '{$row['id_curso']}'
                                LIMIT 1;");
    $row_curso = mysql_fetch_array($result_curso);
}

$result_pro = mysql_query("Select * from  projeto where id_projeto = $pro");
$row_pro = mysql_fetch_array($result_pro);

$result_empresa = mysql_query("Select * from  rhempresa where id_projeto = '$pro'");
$row_empresa = mysql_fetch_array($result_empresa);

$total = "$row_horario[horas_mes]" / "$row_horario[dias_semana]";

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '2' and id_clt = '$clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if ($num_row_verifica == "0") {
    // mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('2','$clt','$data_cad', '$user_cad')");
} else {
    //  mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '2'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result_vale = mysql_query("Select * from vale where id_bolsista = '$row[id_antigo]' AND id_projeto = '$pro'");
$row_vale = mysql_fetch_array($result_vale);

$result_banco = mysql_query("Select * from bancos where id_banco = '$row[banco]'");
$row_banco = mysql_fetch_array($result_banco);

if ($row['id_banco'] == "") {
    $banco = $row['nome_banco'];
} else {
    $banco = $row_banco['nome'];
}

$result_depende = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1, date_format(data2, '%d/%m/%Y') AS data2, date_format(data3, '%d/%m/%Y') AS data3, date_format(data4, '%d/%m/%Y') AS data4, date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes WHERE id_bolsista = '$row[id_clt]' AND nome1 != '' AND id_regiao = '$id_reg' AND id_projeto = '$pro' ORDER BY nome");
$row_depende = mysql_fetch_array($result_depende);

$meses = array("1" => "Janeiro", "2" => "Fevereiro", "3" => "Março", "4" => "Abril", "5" => "Maio", "6" => "Junho", "7" => "Julho", "8" => "Agosto", "9" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
$mes = $meses[date('n')];
?>


<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Ficha de Anotações</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <style>
            .text-center {
                text-align: center;
            }
            p {
                margin: 0 0 10px;
            }
        </style>
        <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/print.css" rel="stylesheet">
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
            <div class="print-pager print-v" style="font-size: 10px;">
                <p class="text-center"><img class="center" src="../imagens/logomaster1.gif" alt="logo" style="width:130px;heigth:80px"></p>
                <h4 class="text-center">INSTITUTO DE ATENÇÃO BÁSICA E AVANÇADA Á SAÚDE</h4>
                <!--Página: 1 de 1-->
                <h5 class="text-center">FICHA DE ANOTAÇÕES</h5>
                <p class="text-center">(PORTARIA 41 DE 28/03/2007 DO MTB)</p>
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <strong>EMPREGADOR:</strong>&emsp; <?php echo $row_master['razao']; ?>
                            </td>
                            <td colspan="2">
                                <strong>CNPJ:</strong>&emsp;
                                <?php echo $row_empresa['cnpj']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>ENDEREÇO:</strong>&emsp;
                                <?php echo $row_pro['endereco']; ?>
                            </td>
                            <td>
                                <strong>BAIRRO:</strong>&emsp;
                                <?php echo $row_pro['bairro']; ?>
                            </td>
                            <td>
                                <?php echo $row_pro['cidade']; ?> - <?php echo $row_pro['estado']; ?>
                            </td>
                            <td>
                                <strong>CEP:</strong>&emsp;
                                <?php echo $row_pro['cep']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <tbody>
                        <tr>
                            <td colspan="4">
                                <strong>Empregado:</strong>&emsp;
                                <?php echo $row['nome'] ?>
                            </td>
                            <td>
                                <strong>Contrato Matrícula:</strong>&emsp;
                                <?php echo $row['campo3'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>CAR.PROF.(NR./SERIE):</strong>&emsp;
                                <?php echo $row['campo1'] . '/' . $row['serie_ctps'] ?>
                            </td>
                            <td>
                                <strong>R.G.:</strong>&emsp;
                                <?php echo $row['rg'] ?>
                            </td>
                            <td>
                                <strong>ESTADO EMISSOR:</strong>&emsp;
                                <?php echo $row['uf_rg'] ?>
                            </td>
                            <td>
                                <strong>DATA ADMISSÂO:</strong>&emsp;
                                <?php echo $row['data_entrada'] ?>
                            </td>
                            <td>
                                <strong>PIS:</strong>&emsp;
                                <?php echo $row['pis'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5"><strong> Rescisão com último dia efetivamente trabalhado em</strong> <?php echo (!empty($row_demi['data_saida'])) ? $row_demi['data_saida'] : '00/00/0000' ?> <strong>e data de saída projetada</strong> <?php echo (!empty($row_demi['homologada'])) ? $row_demi['homologada'] : '00/00/0000' ?></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" colspan="8">Contribuições sindicais</th>
                        </tr>
                        <tr>
                            <th>Ano Ref.</th>
                            <th>Mês/Ano</th>
                            <th>Sindicato</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysql_num_rows($qr_cont_sind) > 0) {
                            while ($rowCSind = mysql_fetch_assoc($qr_cont_sind)) {
                                ?>
                                <tr>
                                    <td><?php echo $rowCSind['ano'] ?></td>
                                    <td><?php echo $rowCSind['mes'] . "/" . $rowCSind['ano'] ?></td>
                                    <td><?php echo $rowCSind['nome'] ?></td>
                                    <td><?php echo number_format($rowCSind['a5019'], 2, ',', '.') ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" colspan="4">Alterações funcionais</th>
                        </tr>
                        <tr>
                            <th>Data</th>
                            <th>Motivo</th>
                            <th>Local de Trabalho</th>
                            <th>Cargo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo converteData($row['data_entrada'], 'd/m/Y') ?></td>
                            <td>Contratação</td>
                            <td><?php echo $unidade_ini['unidade'] ?></td>
                            <td><?php echo $curso_ini['nome'] ?></td>
                        </tr>
                        <?php
                        if (mysql_num_rows($qr_alt_funcao) > 0) {
                            while ($row_alt_func = mysql_fetch_assoc($qr_alt_funcao)) {
                                ?>
                                <tr>
                                    <td><?php echo $row_alt_func['data'] ?></td>
                                    <td><?php echo (empty($row_alt_func['motivo'])) ? "-" : $row_alt_func['motivo']; ?></td>
                                    <td><?php echo '-' ?></td>
                                    <td><?php echo "DE: " . $row_alt_func['de'] . ", PARA: " . $row_alt_func['para'] ?></td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" colspan="6">Alterações Salariais</th>
                        </tr>
                        <tr>
                            <th>Data</th>	
                            <th>Salário Motivo</th>
                            <th>Alterações salariais</th>
                            <th>Data</th>
                            <th>Salário Motivo</th>
                            <th>Alterações salariais</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $clts_na_mao = [
                            710 => [
                                'data' => 'Abril/2016',
                                'motivo' => 'Espontâneo',
                                'valor' => 'R$ 3.887,84'
                            ],
                            3749 => [
                                'data' => 'Julho/2016',
                                'motivo' => 'Espontâneo',
                                'valor' => 'R$ 6.760,00'
                            ],
                        ];
                        if (in_array($row['id_clt'], array_keys($clts_na_mao))) {
                            ?>
                            <tr>
                                <td><?= $clts_na_mao[$row['id_clt']]['data'] ?></td>
                                <td><?= $clts_na_mao[$row['id_clt']]['motivo'] ?></td>
                                <td><?= $clts_na_mao[$row['id_clt']]['valor'] ?></td>
                                <?php if (mysql_num_rows($qr_alt_salario) == 0) { ?>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                                <?php
                            }
                        }
                        if (mysql_num_rows($qr_alt_salario) > 0) {

                            while ($row_alt_salario = mysql_fetch_assoc($qr_alt_salario)) {
                                ?>
                                <?php echo ($i % 2 == 0) ? '<tr>' : ''; ?>
                            <td><?= $row_alt_salario['dataBR'] ?></td>
                            <td><?= (empty($row_alt_salario['motivo'])) ? '-' : $row_alt_salario['motivo'] ?></td>
                            <td><?= number_format($row_alt_salario['salario_novo'], 2, ',', '.') ?></td>
                            <?php
                            echo ($i % 2 == 1) ? '</tr>' : '';
                            $i++;
                            echo $i;
                        }
                        if ($i % 2 == 1) {
                            ?>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" colspan="4">Ferias</th>
                        </tr>
                        <tr>
                            <th>Período aquisitivo</th>
                            <th>Período de gozo</th>	
                            <th>Período aquisitivo</th>
                            <th>Período de gozo</th>	
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysql_num_rows($qr_ferias) > 0) {
                            $i = 0; // contador para definir o fim da linha da tabela
                            // while que traz as ferias gozadas
                            while ($row_ferias = mysql_fetch_array($qr_ferias)) {
                                $u = explode('/', $row_ferias['data_aquisitivo_fim']);
                                echo ($i % 2 == 0) ? '<tr>' : '';
                                ?>
                            <td><?php echo $row_ferias['data_aquisitivo_ini'] . " - " . $row_ferias['data_aquisitivo_fim'] ?></td>
                            <td><?php echo $row_ferias['data_ini'] . " - " . $row_ferias['data_fim'] ?></td>
                            <?php
                            echo ($i % 2 == 1) ? '</tr>' : '';
                            $i++;
                        }
                        // for que exibe os periodos aquisitivos
                        for ($j = $u[2]; $j < date('Y') + 1; $j++) {
                            echo ($i % 2 == 0) ? '<tr>' : '';
                            ?>
                            <td><?php
                                echo date("d/m/Y", mktime(0, 0, 0, $u[1], $u[0] + 1, $u[2]));
                                echo ' - ';
                                echo date("d/m/Y", mktime(0, 0, 0, $u[1], $u[0], $u[2] + 1));
                                ?>
                            </td>
                            <td>-</td>
                            <?php
                            echo ($i % 2 == 1) ? '</tr>' : '';
                            $i++;
                        }
                        // if que completa com uma linha em branco caso seja impar
                        if ($i % 2 == 1) {
                            ?>
                            <td>-</td>
                            <td>-</td>
                            </tr>
                            <?php
                        }
                    } else {
                        $u = explode('/', $row['data_entrada']); // quando nao tem feiras data é a contratacao
                        $i = 0; // contador para definir o fim da linha da tabela
                        // for que exibe os periodos aquisitivos
                        for ($j = $u[2]; $j < date('Y') + 1; $j++) {
                            echo ($i % 2 == 0) ? '<tr>' : '';
                            ?>
                            <td><?php
                                echo date("d/m/Y", mktime(0, 0, 0, $u[1], $u[0] + 1, $u[2]));
                                echo ' - ';
                                echo date("d/m/Y", mktime(0, 0, 0, $u[1], $u[0], $u[2] + 1));
                                ?>
                            </td>
                            <td>-</td>
                            <?php
                            echo ($i % 2 == 1) ? '</tr>' : '';
                            $i++;
                        }
                        // completa com uma linha em branco caso seja imptar
                        if ($i % 2 == 1) {
                            ?>
                            <td>-</td>
                            <td>-</td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th class="text-center" colspan="6">Afastamentos legais</th>
                        </tr>
                        <tr>
                            <th>Inicio</th>
                            <th>Retorno</th>
                            <th>Motivo</th>	
                            <th>Inicio</th>
                            <th>Retorno</th>
                            <th>Motivo</th>	
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysql_num_rows($qr_afasta) > 0) {
                            $i = 0;

                            while ($row_afasta = mysql_fetch_array($qr_afasta)) {
                                echo ($i % 2 == 0) ? '<tr>' : '';
                                ?>

                                <?php
                                // Subtrai 15 dias nas datas a partir de Janeiro/2017
                                if ($row_afasta['databr'] > '01-01-2017') {
                                    $row_afasta['databr'] = converteData($row_afasta['databr'], 'Y-m-d');
                                    $date = new DateTime($row_afasta['databr']);
                                    $date->sub(new DateInterval('P15D'));
                                    $row_afasta['databr'] = $date->format('d/m/Y');
                                }
                                ?>
                            <td><?php echo $row_afasta['databr'] ?></td>
                            <td><?php echo $row_afasta['data_retornobr'] ?></td>
                            <td><?php echo empty($row_afasta['nome_status']) ? "-" : $row_afasta['nome_status']; ?></td>
                            <?php
                            echo ($i % 2 == 1) ? '</tr>' : '';
                            $i++;
                        }
                        if ($i % 2 == 1) {
                            ?>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>


                <br>
                <br>
                <p class="text-justify">
                    O presente documento substitui as anotações na Carteira de Trabalho e Previdência Social fazendo parte integrante dela.
                    Conforme Art. 29 da CLT, as anotações poderão ser feitas mediante o uso de carimbo ou etiqueta gomada, bem como de qualquer meio mecânico ou eletrônico de impressão, desde que autorizado pelo empregador ou seu representante legal.

                </p>
                <br>
                <br>
                <br>
                <br>
                <div class="row">
                    <div class="col-xs-offset-6 col-xs-6">
                        <img src="../imagens/asshasbc.jpg" style="z-index: -20; width: 150px; margin: -210px 0 -180px;">
                        <p class="text-center">__________________________________________________</p>
                        <p class="text-center">Recursos Humanos</p>
                    </div>
                </div>
            </div>                
        </div>
    </body>
</html>
