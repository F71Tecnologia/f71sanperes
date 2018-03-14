<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../conn.php");
include("../classes/global.php");
include("../classes/ProjetoClass.php");
include("../admin/prestadores/MunicipiosClass.php");
include("../classes/ContabilContadorClass.php");
include("../wfunction.php");

$objContador = new ContabilContadorClass();
$objMunicipio = new MunicipiosClass();
$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'gestao';

$global = new GlobalClass();

$sql_uf = "SELECT uf_id, uf_sigla FROM uf";
$opc['-1'] = 'UF';
$return = mysql_query($sql_uf);

while ($row = mysql_fetch_assoc($return)) {
    $opc[$row['uf_sigla']] = $row['uf_sigla'];
}

$estado = "";
    foreach ($opc as $k => $val) {
        $estado .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Contador", "id_form" => "form-cadastro");
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
                    <div class="page-header box-contabil-header">
                        <h2><span class="glyphicon glyphicon-briefcase"></span> - Contabilidade <small>- Contador</small></h2>
                    </div>
                    <div class="bs-component" role="tablist">
                        <form action="contador_controle.php" method="post" class="form-horizontal" id="form-cadastro" enctype="multipart/form-data">
                            <input type="hidden" name="home" id="home" value="">
                            <fieldset>
                                <label class="label-control">CADASTRO</label>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Nome</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control validate[required]" id="nomeContador" name="nomeContador" value="<?= $objContador->getNome() ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">CRC</label>
                                            <div class="col-md-5">
                                                <div class="input-group"> 
                                                    <select class="text-center form-control validate[required]" id="estadoCRC" name="estadoCRC" >                                                  
                                                        <?= $estado ?>
                                                    </select>
                                                    <div class="input-group-addon">/</div>
                                                    <input type="text" class="form-control validate[required]" id="numeroCRC" name="numeroCRC" value="<?= $objContador->getCrc() ?>">
                                                    <div class="input-group-addon">-</div>
                                                    <select class="text-center form-control validate[required]"  id="controleCRC" name="controleCRC" >
                                                        <option value="O" selected>O</option>
                                                        <option value="P">P</option>
                                                        <option value="E">E</option>
                                                        <option value="K">K</option>
                                                        <option value="F">F</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <label class="col-md-1 control-label">CPF</label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" id="cpf" name="cpf" value="<?= $objContador->getCpf() ?>">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Telefone</label>
                                            <div class="col-lg-3">
                                                <input type="text" class="form-control" id="telefone" name="telefone" placeholder="" value="<?= $objContador->getTelComercial() ?>">
                                            </div>
                                            <label class="col-lg-3 control-label">Celular</label>
                                            <div class="col-lg-3">
                                                <input type="text" class="form-control" id="celular" name="celular" placeholder="" value="<?= $objContador->getTelCelular() ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-lg-2 control-label">e-mail</label>
                                            <div class="col-md-9">
                                                <input type="text" class="text lowercase form-control" id="email" name="email" placeholder="" value="<?= $objContador->getEmail() ?>">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label">Profissional</label>
                                            <div class="col-lg-4">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="tipo" id="optionsRadios1" value="1" <?= ($objContador->getProfissional() == 1) ? 'checked' : '' ?>>
                                                        Técnico Contabilidade
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="tipo" id="optionsRadios1" value="2" <?= ($objContador->getProfissional() == 2) ? 'checked' : '' ?>>
                                                        Contador
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <button type="button" class="btn btn-default" onclick="window.history.back();"><i class="fa fa-reply"></i> Voltar</button>
                                        <?php if (empty($objContador->getIdContador())) { ?>
                                            <button type="reset" class="btn btn-warning"><i class="fa fa-eraser"></i> Limpar</button>
                                        <?php } ?>
                                        <button type="submit" name="cadastro-salvar" value="Cadastrar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                                    </div>
                                </div>
                            </fieldset>
                            <div id="resp-cadastro"></div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include_once '../template/footer.php'; ?>

        </div><!-- container -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/form_contador.js" type="text/javascript"></script>
    </body>
</html>