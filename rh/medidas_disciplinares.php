<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if (isset($_REQUEST['method']) && $_REQUEST['method'] != 'delete' && !isset($_REQUEST['id_clt'])) {
    header("Location: /intranet/rh/ver.php");
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include "../wfunction.php";

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'delete' && isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];

    $sql = "UPDATE rh_suspensao SET status = 0 WHERE id_suspensao = $id";
    $query = mysql_query($sql);

    if ($query) {
        echo json_encode(1);
    } else {
        echo json_encode(0);
    }
    exit();
}

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();

$id_clt = $_REQUEST['id_clt'];
$nome = montaQuery('rh_clt', ['nome'],"id_clt = $id_clt");

$sqlMedDis = "SELECT A.id_suspensao, A.id_clt, B.nome, C.nome curso, DATE_FORMAT(A.data, '%d/%m/%Y') data, A.dias, DATE_FORMAT(A.data_retorno, '%d/%m/%Y') data_retorno, A.tipo, A.motivo
                FROM rh_suspensao A
                LEFT JOIN rh_clt B ON A.id_clt = B.id_clt
                LEFT JOIN curso C ON B.id_curso = C.id_curso
                WHERE A.id_clt = $id_clt AND A.status = 1
                ORDER BY A.data DESC";
$queryMedDis = mysql_query($sqlMedDis);

while ($rowMedDis = mysql_fetch_assoc($queryMedDis)) {

    $arrMedDis[$rowMedDis['tipo']][$rowMedDis['id_suspensao']] = $rowMedDis;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Medidas Disciplinares</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="col-sm-12 page-header box-rh-header">
                <h2><span class="fa fa-users"></span> - Medidas Disciplinares - <?= $nome[1]['nome'] ?></h2>
            </div>
            <div class="col-sm-12">
                <table id="advertencias" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                    <thead>
                        <tr>
                            <th class="text-center">ADVERTÊNCIAS</th>
                            <th colspan="2" class="text-right"><button data-id="<?= $id_clt ?>" data-tipo="1" data-key="4" title="Nova Advertência" class="btn btn-success"><span class="fa fa-plus"></span> Nova Advertência</button></th>
                        </tr>
                        <tr class="bg-primary valign-middle">
                            <th>MOTIVO</th>
                            <th class="text-center" style="width: 79px;">DATA</th>
                            <th class="text-center" style="width: 90px;"></th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach ($arrMedDis[1] as $med => $dados) { ?>
                            <tr>
                                <td><?= $dados['motivo'] ?></td>
                                <td class="text-center"><?= $dados['data'] ?></td>
                                <td class="text-center">
                                    <button data-tipo="1" data-key="1" data-id="<?= $dados['id_suspensao'] ?>" data-id_clt="<?= $dados['id_clt'] ?>" title="Editar" class="btn btn-xs btn-warning"><span class="fa fa-pencil"></span></button>
                                    <button data-tipo="1" data-key="3" data-id="<?= $dados['id_suspensao'] ?>" title="Visualizar" class="btn btn-xs btn-primary"><span class="fa fa-search"></span></button>
                                    <button data-tipo="1" data-key="2" data-id="<?= $dados['id_suspensao'] ?>" title="Excluir" class="btn btn-xs btn-danger"><span class="fa fa-trash"></span></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-12">
                <table id="suspensao" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                    <thead>
                        <tr>
                            <th colspan="3" class="text-center">SUSPENSÕES</th>
                            <th colspan="2" class="text-right"><button data-id="<?= $id_clt ?>" data-tipo="2" data-key="4" title="Nova Suspensão" class="btn btn-success"><span class="fa fa-plus"></span> Nova Suspensão</button></th>
                        </tr>
                        <tr class="bg-primary valign-middle">
                            <th>MOTIVO</th>
                            <th class="text-center" style="width: 79px;">DATA</th>
                            <th class="text-center" style="width: 76px;">QNT. DIAS</th>
                            <th class="text-center" style="width: 112px;">DATA RETORNO</th>
                            <th class="text-center" style="width: 90px;"></th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach ($arrMedDis[2] as $med => $dados) { ?>
                            <tr>
                                <td><?= $dados['motivo'] ?></td>
                                <td><?= $dados['data'] ?></td>
                                <td class="text-center"><?= $dados['dias'] ?></td>
                                <td><?= $dados['data_retorno'] ?></td>
                                <td>
                                    <button data-tipo="2" data-key="1" data-id="<?= $dados['id_suspensao'] ?>" data-id_clt="<?= $dados['id_clt'] ?>" title="Editar" class="btn btn-xs btn-warning"><span class="fa fa-pencil"></span></button>
                                    <button data-tipo="2" data-key="3" data-id="<?= $dados['id_suspensao'] ?>" title="Visualizar" class="btn btn-xs btn-primary"><span class="fa fa-search"></span></button>
                                    <button data-tipo="2" data-key="2" data-id="<?= $dados['id_suspensao'] ?>" title="Excluir" class="btn btn-xs btn-danger"><span class="fa fa-trash"></span></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <form action="" method="post" id="editaMedida">
                <input type="hidden" id="id_clt" name="id_clt"/>
                <input type="hidden" id="id_advertencia" name="id_advertencia"/>
                <input type="hidden" id="id_suspensao" name="id_suspensao"/>
            </form>
            <?php include('../template/footer.php'); ?>
        </div>
        <div class="clear"></div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script>
            $(function () {

                function deletaMedidaDisciplinar(t, id) {
                    $.post('medidas_disciplinares.php',{method: 'delete', id: id}, function(data) {
                        if (data == 1) {
                            t.parent().parent().remove();
                        } else {
                            alert('Não foi possível remover esse item.');
                        }
                    }, 'json');
                }

                function visualizaMedidaDisciplinar (id,tipo) {
                    if (tipo == 1) {
                        $('#editaMedida').attr('action','../relatorios/carta_de_advertencia.php');
                        $('#id_advertencia').val(id);
                    } else if (tipo == 2) {
                        $('#editaMedida').attr('action','../relatorios/carta_de_suspensao.php');
                        $('#id_suspensao').val(id);
                    }
                    $('#editaMedida').submit();
                }
                
                function editaMedidaDisciplinar (id_clt, id, tipo) {
                    $('#id_clt').val(id_clt);
                    if (tipo == 1) {
                        $('#editaMedida').attr('action','../rh/config_advertencia.php');
                        $('#id_advertencia').val(id);
                    } else if (tipo == 2) {
                        $('#editaMedida').attr('action','../rh/config_suspensao.php');
                        $('#id_suspensao').val(id);
                    }
                    $('#editaMedida').submit();
                }

                function novaMedida (id, tipo) {
                    $('#id_clt').val(id);
                    if (tipo == 1) {
                        $('#editaMedida').attr('action','../rh/config_advertencia.php');
                    } else if (tipo == 2) {
                        $('#editaMedida').attr('action','../rh/config_suspensao.php');
                    }
                    $('#editaMedida').submit();
                }

                $('.btn').on('click', function () {
                    var t = $(this);
                    var tipo = t.data('tipo');
                    var id_clt = t.data('id_clt');
                    var id = t.data('id');
                    var key = t.data('key');
                    if (key == 1) {
                        editaMedidaDisciplinar(id_clt,id,tipo);
                    }
                    else if (key == 2) {
                        deletaMedidaDisciplinar(t, id);
                    }
                    else if (key == 3) {
                        visualizaMedidaDisciplinar(id,tipo);
                    }
                    else if (key == 4) {
                        novaMedida(id,tipo);
                    }
                });

            });
        </script>
    </body>
</html>