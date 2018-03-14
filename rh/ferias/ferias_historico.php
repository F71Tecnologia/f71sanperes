<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

//ARRAY DE FUNCIONARIO DA F71
//$func_f71 = array('255', '258', '256', '259', '260', '158', '257', '179');

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/clt.php');
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_clt = $_REQUEST['id_clt'];

$feriasObj = new Ferias();

$feriasObj->calcFerias->setIdClt($id_clt);

$listaFeria = $feriasObj->calcFerias->getFeriasPorClt();
?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>Per&iacute;odo Aquisitivo</th>
            <th>Per&iacute;odo De F&eacute;rias</th>
            <th class="text-center">PDF</th>
            <th class="text-center">Excluir</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listaFeria['registros'] as $row_ferias) { ?>
            <tr>
                <td><?= "{$row_ferias['data_aquisitivo_iniBR']} &agrave; {$row_ferias['data_aquisitivo_fimBR']}" ?></td>
                <td><?= "{$row_ferias['data_iniBR']} &agrave; {$row_ferias['data_fimBR']}" ?></td>
                <td class="text-center">
                    <a href="ferias_pdf.php?id_clt=<?= $row_ferias['id_clt'] ?>&id_regiao=<?= $row_ferias['regiao'] ?>&id_ferias=<?= $row_ferias['id_ferias'] ?>" class="btn btn-default btn-xs" title="Ver PDF" target="_blank">
                        <img src="../../imagens/icons/att-pdf.png" style="width: 1.5em; height: 1.5em;" alt="Ver PDF"></a>
                </td>
                <td class="text-center">
                    <a href="#" class="btn btn-danger btn-xs" title="Excluir F&eacute;rias"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

