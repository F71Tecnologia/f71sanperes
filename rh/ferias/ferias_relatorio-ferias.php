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

$relatorio_ferias = $objFerias->relatorioFerias($id_regiao, $id_projeto, $funcao, $mesIni, $mesFim, $anoIni, $anoFim);

if (count($relatorio_ferias) > 0) {
    ?>
    <p class="text-right"><a class="btn btn-success" href="#" onclick="tableToExcel('tbRelatorio', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></p>
    <table class="table table-striped table-hover" id="tbRelatorio">
        <thead>
            <tr>
                <th style="width: 15%;">Projeto</th>
                <th style="width: 20%;">Nome</th>
                <th style="width: 15%;">Fun&ccedil;&atilde;o</th>
                <th style="width: 10%;">Sal&aacute;rio</th>
                <th style="width: 10%;" class="text-center">Data Adimiss&atilde;o</th>
                <th style="width: 10%;" class="text-center">Per&iacute;odos Gozados</th>
                <th style="width: 10%;" class="text-center">Per&iacute;odos Vencidos</th>
                <th style="width: 10%;" class="text-center">Per&iacute;odos N&atilde;o Gozados</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($relatorio_ferias as $row_ferias) { ?>
                <tr>
                    <td><?= utf8_encode($row_ferias['projeto_nome']); ?></td>
                    <td><?= utf8_encode($row_ferias['clt_nome']); ?></td>
                    <td><?= utf8_encode($row_ferias['funcao']); ?></td>
                    <td>R$ <span class="pull-right"><?= $row_ferias['salario']; ?></span></td>
                    <td class="text-center"><?= $row_ferias['data_entrada']; ?></td>
                    <td class="text-center"><?= $row_ferias['periodoGozado']; ?></td>
                    <td class="text-center"><?= $row_ferias['periodoVencido']; ?></td>
                    <td class="text-center"><?= $row_ferias['periodoNaoGozado']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div class="bs-callout bs-callout-info">
        <h4 class="text-info"><i class="fa fa-info-circle"></i> Aten&ccedil;&atilde;o!</h4>
        <p class="text-info">Nenhum CLT encontrado!</p>
    </div>
<?php } ?>