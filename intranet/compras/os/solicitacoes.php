<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$arStatus = array("1" => "Aberto", "2" => "Aprovado");
$message = false;
session_start();

if (isset($_SESSION['MSG_MESSAGE']) && !empty($_SESSION['MSG_MESSAGE'])) {
    $message = $_SESSION['MSG_MESSAGE'];
    unset($_SESSION['MSG_MESSAGE']);
}

//CONDIÇÕES PARA A LISTAGEM
$where = "solicitado_por = {$usuario['id_funcionario']}";
$order = "solicitado_em DESC";
$rs = montaQuery("com_solicitacao", "*,DATE_FORMAT(solicitado_em, '%d/%m/%Y') AS solicitado_embr", $where, $order);
$total = count($rs);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Compas da OS</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-compras-header"><h2><span class="glyphicon glyphicon-shopping-cart"></span> - COMPRAS E CONTRATOS <small> - Minhas solicitações</small></h2></div>

            <?php if ($message !== false) { ?>
                <div id='message-box' class='message-yellow'><p><?php echo $message ?></p></div>
            <?php } ?>


            <?php if ($total > 0) { ?>
                <!--p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Solicitações de Compras')" value="Exportar para Excel" class="exportarExcel"></p-->
                <table class="table table-striped table-hover table-condensed table-bordered" id="tabela">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data da Solicitação</th>
                            <th>Pedido</th>
                            <th>Status</th>
                            <th>Urgente?</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rs as $solicitacao) { ?>
                            <tr>
                                <td><?php echo $solicitacao['id_solicitacao'] ?></td>
                                <td><?php echo $solicitacao['solicitado_embr'] ?></td>
                                <td><?php echo $solicitacao['descricao'] ?></td>
                                <td><?php echo $arStatus[$solicitacao['status']] ?></td>
                                <td><?php echo ($solicitacao['urgencia'] == 0) ? "não" : "sim" ?></td>
                                <td class="text-center">
                                    <!--button class="btn btn-xs btn-success" data-action="det" data-key="<?php echo $solicitacao['id_solicitacao'] ?>" title="Acompanhamento Detalhado" alt="Acompanhamento Detalhado"> <i class="fa fa-search"></i></button>
                                    <button class="btn btn-xs btn-warning" data-action="alt" data-key="<?php echo $solicitacao['id_solicitacao'] ?>" title="Alterar" alt="Alterar" > <i class="fa fa-pencil"></i></button-->
                                    <button class="btn btn-xs btn-danger" data-action="can" data-key="<?php echo $solicitacao['id_solicitacao'] ?>" title="Cancelar Solicitação" alt="Cancelar Solicitação" > <i class="fa fa-trash"></i></button></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div id='message-box' class='message-green'>
                    <p>Nenhuma solicitação encontrada</p>
                </div>
            <?php } ?>

                <button type="button" name="solicitar" id="solicitar" value="Nova Solicitação" class="btn btn-success pull-right" onclick="window.location = 'solicita.php'" > <i class="fa fa-plus"></i> Nova Solicitação </button>

            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="id_solicitacao" id="id_solicitacao" value="" />
            </form>

            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(function () {
                $("#message-box").delay(4000).slideUp('slow');
                $(".btimage").click(function () {
                    var $this = $(this);
                    var acao = $this.data("action");
                    var key = $this.data("key");
                    if (acao === "det") {
                        thickBoxIframe("Detalhe", "popup.detalhe.php", {method: "detalhe", id: key}, 680, 400);
                    } else if (acao === "alt") {
                        $("#id_solicitacao").val(key);
                        $("#form1").attr("action", "solicita.php");
                        $("#form1").submit();
                    }
                });
            });
        </script>
    </body>
</html>