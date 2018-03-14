<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include("../../classes/FeriasClass.php");
include('../../wfunction.php');


$objFerias = new Ferias();


$mesIni = $_REQUEST['mesIni'];
$mesFim = $_REQUEST['mesFim'];
$anoIni = $_REQUEST['anoIni'];
$anoFim = $_REQUEST['anoFim'];
$id_projeto = $_REQUEST['projeto'];
$id_regiao = $_REQUEST['regiao'];
$funcao = $_REQUEST['funcao'];
$usuario = carregaUsuario();

$relatorio_ferias = $objFerias->relatorioFerias($id_regiao, $id_projeto, $funcao, $mesIni, $mesFim, $anoIni, $anoFim);

$query = "SELECT id_unidade,unidade FROM unidade";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $row_unidades[$row['id_unidade']] = $row['unidade'];
}

foreach ($relatorio_ferias as $key => $value) {
    $novo_arr[$value['id_unidade']]['clts'][] = $value;
    $novo_arr[$value['id_unidade']]['nome'] = $row_unidades[$value['id_unidade']];
}

//$arrayDiasAviso = array(1 => array(10,30,40),2 => array(40,60,90));
$arrayAvisos = array_filter($objFerias->getAvisosFerias($usuario['id_master'], $arrayDiasAviso, $usuario['id_regiao'], false, "C"));
$dadosAvisos = $arrayAvisos[$usuario['id_regiao']];
//print_array();

if (count($relatorio_ferias) > 0) {
    ?>

    <?php foreach ($novo_arr as $key => $row_unidade) { ?>

        <p class="text-right"><a class="btn btn-success" href="#" onclick="tableToExcel('tbRelatorioFeriasF<?= $key ?>', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></p>

        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo utf8_encode($row_unidade['nome']); ?>
            </div>
            <table class="table table-striped table-hover table-condensed table-bordered" id="tbRelatorioFeriasF<?= $key ?>">
                <thead>
                    <tr class="bg-primary valign-middle">
                        <th style="width: 15%;">Projeto</th>
                        <th style="width: 20%;">Nome</th>
                        <th style="width: 15%;">Fun&ccedil;&atilde;o</th>
        <!--                <th style="width: 10%;">Sal&aacute;rio</th>-->
                        <th style="width: 10%;" class="text-center">Data Admiss&atilde;o</th>
                        <th style="width: 10%;" class="text-center">Per&iacute;odos Gozados</th>
                        <th style="width: 10%;" class="text-center">Per&iacute;odos Vencidos</th>
                        <th style="width: 10%;" class="text-center">Per&iacute;odos N&atilde;o Gozados</th>
                        <th style="width: 10%;" class="text-center">M&ecirc;s limite para Agendamento</th>
                        <th style="width: 10%;" class="text-center">Agendamento de F&eacuterias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($row_unidade['clts'] as $row_ferias) { ?>
                        <tr class="valign-middle">
                            <td><?= utf8_encode($row_ferias['projeto_nome']); ?></td>
                            <td><?= utf8_encode($row_ferias['clt_nome']); ?></td>
                            <td><?= utf8_encode($row_ferias['funcao']); ?></td>
                            <!--<td>R$ <span class="pull-right"><?= $row_ferias['salario']; ?></span></td>-->
                            <td class="text-center"><?= $row_ferias['data_entrada']; ?></td>
                            <td class="text-center"><?= $row_ferias['periodoGozado']; ?></td>
                            <td class="text-center"><?= $row_ferias['periodoVencido']; ?></td>
                            <td class="text-center"><?= $row_ferias['periodoNaoGozado']; ?></td>
                            <td class="text-center"><?= $objFerias->getLimiteConcessivo($row_ferias['id_clt']) ?></td>
                            <td class="text-center"><?= $row_ferias['inicioBR']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="bs-callout bs-callout-info">
        <h4 class="text-info"><i class="fa fa-info-circle"></i> Aten&ccedil;&atilde;o!</h4>
        <p class="text-info">Nenhum CLT encontrado!</p>
    </div>
<?php } ?>