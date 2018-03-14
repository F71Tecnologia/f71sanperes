<?php
include("../../../../conn.php");
include("../../../../wfunction.php");
include("../../../EduEventosClass.php");

$objetoEventos = new EduEventosClass();

//    $objetojuridico -> setIdProjeto(1000);
//    echo $objetojuridico -> getIdProjeto();
$data = $_REQUEST["ano"] . "-" . sprintf("%02s", $_REQUEST["mes"]) . "-" . sprintf("%02s", $_REQUEST["dia"]);


$arrayEventos = $objetoEventos->getListEvento($data);
//     print_r($arrayEventos);
//    echo $data;

foreach ($arrayEventos as $key => $row) { ?>
<table class="table table-condensed table-bordered table-striped text-sm">
    <thead>
        <tr>
            <td class="text-center ">ID</td>
            <td class="text-center ">Tipo</td>
            <td class="text-center ">Evento</td>
            <td class="text-center ">Data</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center "><?php echo $row["id_evento"] ?></td>
            <td class="text-center "><?php echo utf8_encode($row["tipo"]) ?></td>
            <td class="text-center "><?php echo utf8_encode($row['nome']) ?></td>
            <td class="text-center "><?php echo implode("/", array_reverse(explode("-", $row['data']))) ?></td>
        </tr>
    </tbody>
</table>
<?php } ?>

<script>
    $(document).ready(function () {

        $("body").on("click", "#realizado", function () {
//            return confirm("Definir como realizado?");
            new BootstrapDialog({
                nl2br: false,
                size: BootstrapDialog.SIZE_WIDE,
                type: 'type-success',
                title: 'EVENTOS',
                message: result,
                closable: true,
                buttons:
                        [{
                                label: 'Fechar',
                                action: function (dialog) {
                                    dialog.close();
                                    //window.location.reload();
                                }
                            }]
            }).open();

        });

    });
</script>

