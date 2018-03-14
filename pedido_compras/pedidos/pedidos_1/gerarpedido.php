<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../conn.php");
include("../classes/global.php");
include("../classes/pedidosClass.php");
include("../classes/NFeClass.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$global = new GlobalClass();

$objNFe= new NFe();

$prestador = $objNFe->consultaPrestador($_REQUEST['projeto1'],$_REQUEST['prestador1']);
$projeto = $objNFe->consultarProjeto($_REQUEST['projeto1']);

$breadcrumb_config = array("nivel" => "../", "key_btn" => "35", "area" => "Gestão de Compras e Contratos", "ativo" => "Gestão de Pedidos", "id_form" => "form-Pedido");

?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../favicon.png">

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Compras e Contratos <small>- Confirmar Pedido</small></h2>
                    </div>
                    <div class="form-group">
                        <label class="text-sm">Fornecedor</label>
                        <p>
                            <label class="text-normal"><?= $prestador['c_razao'] ?></label>
                            <label class="right"><?= $prestador['c_cnpj'] ?></label>
                        </p>
                        <p><label class="col-lg-8 label-control"><?= $prestador['c_endereco']; ?></label></p>
                        <p><label class="col-lg-8 label-control"><?= $prestador['c_tel']; ?></label></p>
                    </div>
                    <div class="form-group">
                        <p><label class="text-default"><?= $projeto['nome']." ".$projeto['cnpj'] ; ?> </label></p>
                        <p><label class="text-default"><?= $projeto['endereco']." ".$projeto['complemento']." ".$projeto['bairro'] ; ?> </label></p>
                    </div>
                        
                        <table class="table table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Und</th>
                                    <th>Valor R$</th>
                                    <th>Quantidade</th>
                                    <th>Total R$</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($_REQUEST['idProd']); $i++) {
                                    if ($_REQUEST['qtde'][$i] > 0) {
                                        $produto = $objNFe->consultarProduto($_REQUEST['idProd'][$i]); ?>
                                        <tr>
                                            <td><?= $produto['xProd'] ?><input type="hidden" name="xProd[]" value="<?= $_REQUEST['id_prod'][$i] ?>"></td>
                                            <td class="text-center"><?= $produto['uCom'] ?><input type="hidden" name="uCom[]" value="<?= $_REQUEST['id_prod'][$i] ?>"></td>
                                            <td class="text-right"><?= $_REQUEST['vUnCom'][$i] ?><input type="hidden" name="vUnCom[]" value="<?= $_REQUEST['id_prod'][$i] ?>"></td>
                                            <td class="text-right ><?= $_REQUEST['qtde'][$i] ?><input type="hidden" name="qtde[]" value="<?= $_REQUEST['id_prod'][$i] ?>"></td>
                                            <td class="text-right"><?= $_REQUEST['vProd'][$i] ?><input type="hidden" name="vProd[]" value="<?= $_REQUEST['id_prod'][$i] ?>"></td>
                                        </tr>
                                    <?php }
                                } ?>
                            </tbody>
                        </table>
                </div><!-- col-lg-12 -->

            </div><!-- row -->


            <?php include_once '../template/footer.php'; ?>

        </div><!-- container -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    </body>
</html>