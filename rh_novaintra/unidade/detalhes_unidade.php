<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/UnidadeClass.php');

$unidade = $_REQUEST['unidade'];

$usuario = carregaUsuario();
$row = getUnidadeID($unidade);

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Detalhe Unidade");
$breadcrumb_pages = array("Gestão de Unidades"=>"index.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Detalhe Unidade</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Detalhe Unidade</small></h2></div>
                </div>
            </div>
            <form id="form1" method="post">
                <div class="panel panel-default">
                    <div class="panel-heading">Dados da Unidade</div>
                    <div class="panel-body">
                        <div class="col-xs-6 border-r">
                            <p><label class="controler-label">Regiao:</label> <?=$row['nome_regiao']?></p>
                            <p><label class="controler-label">Projeto:</label> <?=$row['nome_projeto']?></p>
                            <p><label class="controler-label">Nome:</label> <?=$row['unidade']?></p>
                            <p><label class="controler-label">Local:</label> <?=$row['local']?></p>
                            <p><label class="controler-label">Endereço:</label> <?=$row['endereco']?></p>
                            <p><label class="controler-label">Bairro:</label> <?=$row['bairro']?></p>
                            <p><label class="controler-label">Cidade:</label> <?=$row['cidade']?></p>
                            <p><label class="controler-label">UF:</label> <?=$row['uf']?></p>
                            <p><label class="controler-label">Ponto de referência:</label> <?=$row['ponto_referencia']?></p>
                            <p><label class="controler-label">CEP:</label> <?=$row['cep']?></p>
                        </div>
                        <div class="col-xs-6 border-l">
                            <p><label class="controler-label">Cód. Un. WEBSAASS:</label> <?=$row['cod_websaass']?></p>
                            <p><label class="controler-label">Cód. Serviço 1:</label> <?=$row['cod_servico1']?></p>
                            <?= ($row['id_coordenador']) ? "<p><label class='controler-label'>Coordenador:</label>".$row['id_coordenador']." - ".$row['nome_coordenador']."</p>" : ""; ?>
                            <p><label class="controler-label">Telefone:</label> <?=$row['tel']?></p>
                            <p><label class="controler-label">Telefone Recado:</label> <?=$row['tel2']?></p>
                            <p><label class="controler-label">Responsável:</label> <?=$row['responsavel']?></p>
                            <p><label class="controler-label">Celular do Responsável:</label> <?=$row['cel']?></p>
                            <p><label class="controler-label">Email do Responsável:</label> <?=$row['email']?></p>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" id="unidade" name="unidade" value="" />
                        <input type="hidden" id="home" name="home" value="" />
                        <input type="hidden" id="caminho" name="caminho" value="1" />
                        <button type="button" class="btn btn-default" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';"><i class="fa fa-reply"></i> Voltar</button>
                        <?php if($_COOKIE['logado'] != 395) { ?><button type="submit" class="btn btn-primary" value="Editar" name="editarUnidade" id="editarUnidade" data-type="editar" data-key="<?=$row['id_unidade']?>"><i class="fa fa-pencil"></i> Editar</button><?php } ?>
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#editarUnidade").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#unidade").val(key);
                        $("#form1").attr('action','form_unidade.php');
                        $("#form1").submit();
                    }
                });                                
            });
        </script>
    </body>
</html>