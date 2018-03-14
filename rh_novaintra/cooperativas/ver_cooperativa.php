<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/cooperativa.php');
$usuario = carregaUsuario();

$cooperativa = cooperativa::getCoop($_REQUEST['cooperativa']);
$regiao = montaQueryFirst("regioes", "*", "id_regiao={$cooperativa['id_regiao']}");
$_SESSION['voltarCooperativa']['id_regiao'] = $cooperativa['id_regiao'];


if ($cooperativa['tipo'] == '1')
    $cooperativa['tipo'] = 'COOPERATIVA';
if ($cooperativa['tipo'] == '2')
    $cooperativa['tipo'] = 'PESSOA JURÍDICA';

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Detalhes Cooperativa");
$breadcrumb_pages = array("Cooperativas" => "cooperativa_nova2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Detalhes Cooperativa</title>
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
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Detalhes Cooperativa</small></h2></div>
                    <input type="hidden" name="home" id="home">
                </div><!-- /.col-xs-12 -->
            </div><!-- /.row -->
            <form id="form1" method="post" class="form-horizontal">
                <div class="panel panel-info">
                    <div class="panel-heading">Dados da Empresa Contratada</div>
                    <div class="panel-body">
                        <div class="form-group border-b">
                            <label class="col-xs-6 border-r">Tipo: <span class="text-normal"><?=$cooperativa['tipo']?></span></label>
                            <label class="col-xs-6 border-l">Razão Social: <span class="text-normal"><?=$cooperativa['nome']?></span></label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-6 border-r">Nome Fantasia: <span class="text-normal"><?=$cooperativa['fantasia']?></span></label>
                            <label class="col-xs-6 border-l">Endereço: <span class="text-normal"><?=$cooperativa['endereco']?></span></label>
                        </div><!-- /.col-xs-12 -->
                        <div class="form-group border-b">
                            <label class="col-xs-3 border-r">Bairro: <span class="text-normal"><?=$cooperativa['bairro']?></span></label>
                            <label class="col-xs-3 border-hr">CEP: <span class="text-normal"><?=$cooperativa['cooperativa_cep']?></span></label>
                            <label class="col-xs-3 border-hr">CNPJ: <span class="text-normal"><?=$cooperativa['cnpj']?></span></label>
                            <label class="col-xs-3 border-l">CNAE: <span class="text-normal"><?=$cooperativa['cooperativa_cnae']?></span></label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-3 border-r">Contato: <span class="text-normal"><?=$cooperativa['contato']?></span></label>
                            <label class="col-xs-3 border-hr">Cel: <span class="text-normal"><?=$cooperativa['cel']?></span></label>
                            <label class="col-xs-3 border-hr">Cidade: <span class="text-normal"><?=$cooperativa['cidade']?></span></label>
                            <label class="col-xs-3 border-l">UF: <span class="text-normal"><?=$cooperativa['cooperativa_uf']?></span></label>
                        </div><!-- /.col-xs-6 -->
                        <div class="form-group border-b">
                            <label class="col-xs-3 border-r">FPAS: <span class="text-normal"><?=$cooperativa['cooperativa_fpas']?></span></label>
                            <label class="col-xs-3 border-hr">Telefone: <span class="text-normal"><?=$cooperativa['tel']?></span></label>
                            <label class="col-xs-3 border-l">Fax: <span class="text-normal"><?=$cooperativa['fax']?></span></label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-6 border-r">E-mail: <span class="text-normal"><?=$cooperativa['email']?></span></label>
                            <label class="col-xs-6 border-l">Site: <span class="text-normal"><?=$cooperativa['site']?></span></label>
                        </div>
                    </div>
                </div><!-- /.note-info -->
                <div class="panel panel-default">
                    <div class="panel-heading">Dados dos Administradores</div>
                    <div class="panel-body">
                        <div class="col-xs-6 no-padding-l">
                            <div class="panel panel-warning">
                                <div class="panel-heading">Presidente</div>
                                <div class="panel-body">
                                    <div class="form-group border-b">
                                        <label class="col-xs-8 border-r">Nome: <span class="text-normal"><?=$cooperativa['presidente']?></span></label>
                                        <label class="col-xs-4 border-l">RG: <span class="text-normal"><?=$cooperativa['rgp']?></span></label>
                                    </div>
                                    <div class="form-group border-b">
                                        <label class="col-xs-8 border-r">Matricula: <span class="text-normal"><?=$cooperativa['matriculap']?></span></label>
                                        <label class="col-xs-4 border-l">CPF: <span class="text-normal"><?=$cooperativa['cpfp']?></span></label>
                                    </div>
                                    <div class="form-group border-b">
                                        <label class="col-xs-12">Endereço: <span class="text-normal"><?=$cooperativa['enderecop']?></span></label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.col-xs-6 -->
                        <div class="col-xs-6 no-padding-r">
                            <div class="panel panel-warning">
                                <div class="panel-heading">Diretor</div>
                                <div class="panel-body">
                                    <div class="form-group border-b">
                                        <label class="col-xs-8 border-r">Nome: <span class="text-normal"><?=$cooperativa['diretor']?></span></label>
                                        <label class="col-xs-4 border-l">RG: <span class="text-normal"><?=$cooperativa['rgd']?></span></label>
                                    </div>
                                    <div class="form-group border-b">
                                        <label class="col-xs-8 border-r">Matricula: <span class="text-normal"><?=$cooperativa['matriculad']?></span></label>
                                        <label class="col-xs-4 border-l">CPF: <span class="text-normal"><?=$cooperativa['cpfd']?></span></label>
                                    </div>
                                    <div class="form-group border-b">
                                        <label class="col-xs-12">Endereço: <span class="text-normal"><?=$cooperativa['enderecod']?></span></label>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.col-xs-6 -->
                        <div class="form-group border-b">
                            <label class="col-xs-6">Entidade Sindical Vinculada: <span class="text-normal"><?=$cooperativa['entidade']?></span></label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-6 border-r">Fundo reserva: <span class="text-normal"><?=$cooperativa['fundo']?></span></label>
                            <label class="col-xs-6 border-l">Taxa Administrativa: <span class="text-normal"><?=$cooperativa['taxa']?></span></label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-6 border-r">Quantidade de Parcelas: <span class="text-normal"><?=$cooperativa['parcelas']?></span></label>
                            <label class="col-xs-6 border-l">Bonificação: <span class="text-normal"><?=$cooperativa['bonificacao']?></span></label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-2 border-r">Logo: </label>
                            <label class="col-xs-10 border-l"><?php
                                if (isset($cooperativa['foto']) && !empty($cooperativa['foto'])) { ?>
                                    <img data-src="holder.js/140x140" class="img-thumbnail" src="<?="logos/coop_".$cooperativa['id_coop'].$cooperativa['foto']?>" style="width: 140px; height: 140px;">
                                    <!--img src="<?="logos/coop_" . $cooperativa['id_coop'] . $cooperativa['foto']; ?>" title="Logo da Cooperativa" alt="Logo da Cooperativa" style="min-width:100px; min-height: 100px;"-->
                                <?php }else{ ?>
                                    Não disponível.
                                <?php } ?>
                            </label>
                        </div>
                        <div class="form-group border-b">
                            <label class="col-xs-12">Realizador do Curso de Cooperativismo: <span class="text-normal"><?=$cooperativa['cursos']?></span></label>
                        </div>
                    </div>
                </div><!-- /.row -->
                <div class="panel panel-success">
                    <div class="panel-heading">Dados Bancários</div>
                    <div class="panel-body">
                        <div class="form-group border-b">
                            <label class="col-xs-12">Banco: 
                                <span class="text-normal"><?php
                                $result_banco = mysql_query("SELECT id_banco,nome FROM bancos where id_regiao = '$regiao'");
                                while ($row_banco = mysql_fetch_array($result_banco)) {
                                    if($cooperativa['id_banco'] == $row_banco['id_banco']){
                                        echo $row_banco['id_banco'].' - '.$row_banco['nome'];
                                    }
                                } ?>
                            </span></label>
                        </div>
                    </div><!-- /.col-xs-12 -->
                </div><!-- /.row -->
            </form>
            <?php include_once ('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>
