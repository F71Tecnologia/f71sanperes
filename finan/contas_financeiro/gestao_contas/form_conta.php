<?php

// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../../login.php?entre=true';</script>";
}

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/BotoesClass.php");
include("../../../classes/EntradaESaidaGrupo.php");
include("../../../classes/EntradaESaidaSubGrupo.php");
include("../../../classes/EntradaESaida.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$global = new GlobalClass();

$objConta = new EntradaESaidaClass();
$objGrupo = new EntradaESaidaGrupoClass();
$objSubGrupo = new EntradaESaidaSubGrupoClass();

if(isset($_REQUEST['salvar'])){
    $objConta->setCod(addslashes($_REQUEST['cod']));
    $objConta->setNome(addslashes($_REQUEST['nome']));
    $objConta->setDescricao(addslashes($_REQUEST['descricao']));
//    $objConta->setTipo(addslashes($_REQUEST['cod']));
    $objConta->setGrupo(addslashes($_REQUEST['grupo']));
    $objConta->setIdSubgrupo(addslashes($_REQUEST['id_subgrupo']));
    $objConta->setIdUser(addslashes($usuario['id_funcionario']));
    $objConta->setDataCad(date('Y-m-d H:i:s'));
    $objConta->setStatus(1);
    
    if(isset($_REQUEST['id_entradasaida'])){
        $objConta->setIdEntradasaida(addslashes($_REQUEST['id_entradasaida']));
        $result = $objConta->update();
    }else{
        $result = $objConta->insert();
    }
    
    if($result){
        $alert = [
            'status' => 'alert-success',
            'mensagem' => 'Salvo com sucesso!'
        ];
    }else{
        $alert = [
            'status' => 'alert-danger',
            'mensagem' => 'Erro ao salvar!'
        ];
    }
    
}


if (isset($_REQUEST['id'])) {
    $id = addslashes($_REQUEST['id']);
    $objConta->setIdEntradasaida($id);
    $objConta->getById();
    $objConta->getRow();
}


$nome_pagina = 'Cadastro de Contas';
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../../index.php", "Contas Financeiro" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if(isset($result)){ ?>
            <div class="alert <?= $alert['status'] ?>">
                <?= $alert['mensagem'] ?>
            </div>
            <?php } ?>

            <form class="form-horizontal" action="#" id="form1" method="post">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <?php if (!empty($objConta->getIdEntradasaida())) { ?>
                            <input type="hidden" class="form-control" id="id_entradasaida" name="id_entradasaida" value="<?= $objConta->getIdEntradasaida() ?>" >
                        <?php } ?>

                        <div class="form-group">
                            <label for="cod" class="col-sm-2 control-label">
                                Código</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="cod" name="cod" value="<?= $objConta->getCod() ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nome" class="col-sm-2 control-label">
                                Nome</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="nome" name="nome" value="<?= $objConta->getNome() ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descricao" class="col-sm-2 control-label">
                                Descrição</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="descricao" name="descricao" value="<?= $objConta->getDescricao() ?>" >
                            </div>
                        </div>
<!--                        <div class="form-group">
                            <label for="tipo" class="col-sm-2 control-label">
                                tipo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="tipo" name="tipo" value="<?= $objConta->getTipo() ?>" >
                            </div>
                        </div>-->
                        <div class="form-group">
                            <label for="grupo" class="col-sm-2 control-label">
                                Grupo</label>
                            <div class="col-sm-9">
                                <?= montaSelect($objGrupo->getSelect(), $objConta->getGrupo(), ' class="form-control" id="grupo" name="grupo"'); ?>
                            </div>
                        </div>
                        <!--                        <div class="form-group">
                                                    <label for="extra" class="col-sm-2 control-label">
                                                        extra</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="extra" name="extra" value="<?= $objConta->getExtra() ?>" >
                                                    </div>
                                                </div>-->
                        <div class="form-group">
                            <label for="id_subgrupo" class="col-sm-2 control-label">
                                Subgrupo</label>
                            <div class="col-sm-9">
                                <?= montaSelect($objSubGrupo->getSelect(), $objConta->getIdSubgrupo(), 'class="form-control" id="id_subgrupo" name="id_subgrupo"'); ?>
                            </div>
                        </div>
                        <!--                        <div class="form-group">
                                                    <label for="codigof71" class="col-sm-2 control-label">
                                                        codigof71</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="codigof71" name="codigof71" value="<?= $objConta->getCodigof71() ?>" >
                                                    </div>
                                                </div>-->
                        <!--                        <div class="form-group">
                                                    <label for="faturamento" class="col-sm-2 control-label">
                                                        faturamento</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" class="form-control" id="faturamento" name="faturamento" value="<?= $objConta->getFaturamento() ?>" >
                                                    </div>
                                                </div>-->

                    </div>
                    <div class="panel-footer text-right">
                        <a href="index.php" class="btn btn-default"><i class="fa fa-reply"></i> Voltar</a>
                        <button type="submit" name="salvar" value="1" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                    </div>
                </div>
            </form>

            <?php include('../../../template/footer.php'); ?>
        </div>

        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../resources/dropzone/dropzone.js"></script>
        <script src="../../../js/jquery.form.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="rel_notas_liberadas.js" type="text/javascript"></script>

    </body>
</html>
