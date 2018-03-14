<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/EmpresaClass.php');

$empresa = $_REQUEST['empresa'];

$usuario = carregaUsuario();
$row = getEmpresaID($empresa);

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Detalhes Empresa");
$breadcrumb_pages = array("Gestão de RH" => "../../principalrh.php", "Empresas" => "index2.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Detalhes Empresa</title>
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
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Empresa Detalhes Empresa</small></h2></div>
                </div>
            </div>
            <form id="form1" class="form-horizontal">
                <div class="note">
                    <legend>Dados da Empresa <?=$row['nome']?></legend>
                    <div class="form-group">
                        <label class="col-xs-2">Regiao:</label> 
                        <div class="col-xs-4"><?="{$row['id_regiao']} - {$row['nome_regiao']}"?></div>
                        <label class="col-xs-2">Responsável:</label>
                        <div class="col-xs-4"><?=$row['responsavel']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Projeto:</label>
                        <div class="col-xs-4"><?="{$row['id_projeto']} - {$row['nome_projeto']}"?></div>
                        <label class="col-xs-2">CPF:</label>
                        <div class="col-xs-4"><?=$row['cpf']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Nome Fantasia:</label>
                        <div class="col-xs-4"><?=$row['nome']?></div>
                        <label class="col-xs-2">Cód. Acidentes de Trabalho:</label>
                        <div class="col-xs-4"><?=$row['acid_trabalho']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Razão Social :</label>
                        <div class="col-xs-4"><?=$row['razao']?></div>
                        <label class="col-xs-2">Atividade:</label>
                        <div class="col-xs-4"><?=$row['atividade']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Endereço:</label>
                        <div class="col-xs-4"><?=$row['endereco']?></div>
                        <label class="col-xs-2">Grupo:</label>
                        <div class="col-xs-4"><?=$row['grupo']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Inscrição Municipal:</label>
                        <div class="col-xs-4"><?=$row['im']?></div>
                        <label class="col-xs-2">Proprietários:</label>
                        <div class="col-xs-4"><?=$row['proprietarios']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Inscriçao Estadual:</label>
                        <div class="col-xs-4"><?=$row['ie']?></div>
                        <label class="col-xs-2">Familiares:</label>
                        <div class="col-xs-4"><?=$row['familiares']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">CNPJ:</label>
                        <div class="col-xs-4"><?=$row['cnpj']?></div>
                        <label class="col-xs-2">Tipo de Pagamento:</label>
                        <div class="col-xs-4"><?=$row['tipo_pg']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Tipo de CNPJ:</label>
                        <div class="col-xs-4"><?=$row['tipo_cnpj']?></div>
                        <label class="col-xs-2">Ano do 1º Exercício:</label>
                        <div class="col-xs-4"><?=$row['ano']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Telefone:</label>
                        <div class="col-xs-4"><?=$row['tel']?></div>
                        <label class="col-xs-2">Fax:</label>
                        <div class="col-xs-4"><?=$row['fax']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Email:</label>
                        <div class="col-xs-4"><?=$row['email']?></div>
                        <label class="col-xs-2">Site:</label>
                        <div class="col-xs-4"><a href="<?=$row['site']?>" target="_blank"><?=$row['site']?></a></div>
                    </div>
                </div>
                <div class="note note-info">
                    <legend>Dados do FGTS</legend>
                    <div class="form-group">
                        <label class="col-xs-2">CNPJ Matriz:</label>
                        <div class="col-xs-4"><?=$row['cnpj_matriz']?></div>
                        <label class="col-xs-2">Banco:</label>
                        <div class="col-xs-4"><?=$row['banco']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Agência:</label>
                        <div class="col-xs-4"><?=$row['agencia']?></div>
                        <label class="col-xs-2">Conta:</label>
                        <div class="col-xs-4"><?=$row['conta']?></div>
                    </div>
                </div>
                <div class="note note-warning">
                    <legend>Dados do INSS</legend>
                    <div class="form-group valign-middle">
                        <label class="col-xs-2">FPAS:</label>
                        <div class="col-xs-4"><?=$row['fpas']?></div>
                        <label class="col-xs-2">PAT:</label>
                        <div class="col-xs-4"><?=$row['pat']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Tipo:</label>
                        <div class="col-xs-4"><?=$row['tipo_fpas']?></div>
                        <label class="col-xs-2">% Empresa:</label>
                        <div class="col-xs-4"><?=$row['p_empresa'] * 100 . "%"; ?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Porte:</label>
                        <div class="col-xs-4"><?=$row['porte']?></div>
                        <label class="col-xs-2">% Acidente de Trabalho:</label>
                        <div class="col-xs-4"><?=$row['p_acid_trabalho'] * 100 . "%"; ?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Natureza Jurídica:</label>
                        <div class="col-xs-4"><?=$row['natureza']?></div>
                        <label class="col-xs-2">% Prolabora / Autônomo:</label>
                        <div class="col-xs-4"><?=$row['p_prolabora'] * 100 . "%"; ?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Capital Social:</label>
                        <div class="col-xs-4"><?=formataMoeda($row['capital']); ?></div>
                        <label class="col-xs-2">% Terceiros:</label>
                        <div class="col-xs-4"><?=$row['p_terceiros'] * 100 . "%"; ?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Início das Atividades:</label>
                        <div class="col-xs-4"><?=$row['data_inicio']?></div>
                        <label class="col-xs-2">Cód. Terceiros:</label>
                        <div class="col-xs-4"><?=$row['terceiros']?></div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-2">Simples:</label>
                        <div class="col-xs-4"><?=$row['simples']?></div>
                        <label class="col-xs-2">% Isen. Emp. Filantrópicas:</label>
                        <div class="col-xs-4"><?=$row['p_filantropicas'] * 100 . "%"; ?></div>
                    </div>
                </div>
                <div class="note text-right">
                    <input type="hidden" id="empresa" name="empresa" value="" />
                    <input type="submit" class="btn btn-primary" value="Editar" name="editarEmpresa" id="editarEmpresa" data-type="editar" data-key="<?=$row['id_empresa']?>" />
                    <input type="button" class="btn btn-default" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#editarEmpresa").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#empresa").val(key);
                        $("#form1").attr('action','form_empresa2.php');
                        $("#form1").append($('<input>',{name : 'caminho',type: 'hidden',value: 1}));
                        $("#form1").submit();
                    }
                });                                
            });
        </script>
    </body>
</html>