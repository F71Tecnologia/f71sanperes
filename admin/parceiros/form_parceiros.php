<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/ParceiroClass2.php");

$usuario = carregaUsuario();
$objParceiro = new ParceiroClass();

$objParceiro->setDefault();
$objParceiro->setIdParceiro($_REQUEST['id_parceiro']);
if(!empty($objParceiro->getIdParceiro())){
    if($objParceiro->select()){
        $objParceiro->getRow();
    } else{
        echo $objParceiro->getError();
        exit;
    }
    $action = array('editar_parceiro','Editar Parceiro');
} else { 
    $action = array('cadastrar_parceiro','Cadastrar Parceiro');
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$action[1]);
$breadcrumb_pages = array("Principal" => "../index.php", "Gestão de Parceiros"=>"index.php"); 

$sql = "SELECT uf_sigla AS uf, uf_id AS id FROM uf";
$qr_estado = mysql_query($sql) or die(mysql_error());
$num_estado = mysql_num_rows($qr_estado);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?=$action[1]?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - <?=$action[1]?></small></h2></div>
                </div>
            </div>
            <form action="" method="post" id="form_parceiros" class="form-horizontal top-margin1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-body bloco_obrigacoes">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Nome</label>
                            <div class="col-sm-4"><input name="parceiro_nome" id="parceiro_nome" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroNome()?>"></div>
                            <label class="control-label col-sm-1">CNPJ</label>
                            <div class="col-sm-4"><input name="parceiro_cnpj" id="parceiro_cnpj" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroCnpj()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">CCM</label>
                            <div class="col-sm-4"><input name="parceiro_ccm" id="parceiro_ccm" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroCcm()?>"></div>
                            <label class="control-label col-sm-1">I.E.</label>
                            <div class="col-sm-4"><input name="parceiro_ie" id="parceiro_ie" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroIe()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Endereço</label>
                            <div class="col-sm-7"><input name="parceiro_endereco" id="parceiro_endereco" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroEndereco()?>"></div>
                            <label class="control-label col-sm-1">Estado</label>
                            <div class="col-sm-1">
                                <select name="parceiro_estado" id="parceiro_estado" class="form-control no-padding-r validate[required]">
                                     <?php while ($estado = mysql_fetch_assoc($qr_estado)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                        <option value="<?php echo $estado['id'];?>"><?php echo $estado['uf'];?></option>
                                
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Bairro</label>
                            <div class="col-sm-4"><input name="parceiro_bairro" id="parceiro_bairro" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroBairro()?>"></div>
                            <label class="control-label col-sm-1">Cidade</label>
                            <div class="col-sm-4"><input name="parceiro_cidade" id="parceiro_cidade" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroCidade()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Contato</label>
                            <div class="col-sm-4"><input name="parceiro_contato" id="parceiro_contato" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroContato()?>"></div>
                            <label class="control-label col-sm-1">CPF</label>
                            <div class="col-sm-4"><input name="parceiro_cpf" id="parceiro_cpf" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroCpf()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Telefone</label>
                            <div class="col-sm-4"><input name="parceiro_telefone" id="parceiro_telefone" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroTelefone()?>"></div>
                            <label class="control-label col-sm-1">Celular</label>
                            <div class="col-sm-4"><input name="parceiro_celular" id="parceiro_celular" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroCelular()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Email</label>
                            <div class="col-sm-9"><input name="parceiro_email" id="parceiro_email" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroEmail()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Banco</label>
                            <div class="col-sm-4"><input name="parceiro_banco" id="parceiro_banco" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroBanco()?>"></div>
                            <label class="control-label col-sm-1">Agência</label>
                            <div class="col-sm-4"><input name="parceiro_agencia" id="parceiro_agencia" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroAgencia()?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Conta</label>
                            <div class="col-sm-4"><input name="parceiro_conta" id="parceiro_conta" type="text" class="form-control validate[required]" value="<?=$objParceiro->getParceiroConta()?>"></div>
                        </div>
                    </div>
                    <div class="panel-footer bloco_obrigacoes">
                        <div class="form-group">
                            <?php if(!empty($objParceiro->getParceiroLogo())){ ?>
                            <div class="col-sm-6">
                                <h4>Logo</h4>
                                <hr>
                                <!--div class="thumbnail" style="min-height: 250px!important; background-image: url('/intranet/adm/adm_parceiros/logo/<?=$objParceiro->getParceiroLogo()?>'); background-repeat: no-repeat; background-position: center;">
                                    <span class="btn-danger badge pointer fa fa-trash-o pull-right" style="margin-right: 1%; margin-top: 1%;"> Remover</span>
                                </div-->
                                <div class="thumbnail display-table-cem" style="min-height: 250px!important;">
                                    <img src="/intranet/adm/adm_parceiros/logo/<?=$objParceiro->getParceiroLogo()?>" style="height: 160px; position: absolute; top: 33%; left: 22%; z-index: 1;">
                                    <span class="btn-danger badge pointer fa fa-trash-o pull-right" id="remover_logo" data-key="<?=$objParceiro->getIdParceiro()?>" style="z-index: 10; right: 1%; top: 1%;"> Remover</span>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="col-sm-6 <?= (!empty($objParceiro->getParceiroLogo())) ? 'hide' : '' ?>">
                                <h4>Anexar Logo</h4>
                                <hr>
                                <div id="anexo_logo" class="dropzone" style="min-height: 250px!important;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer no-padding-hr bloco_obrigacoes">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="btn btn-primary botaoSubmit"><i class="fa fa-save"></i> Salvar</button>
                            <?=(!empty($objParceiro->getIdParceiro()))?'<input type="hidden" name="id_parceiro" value="'.$objParceiro->getIdParceiro().'" />':''?>
                            <?=(!empty($objParceiro->getIdParceiro()))?'<input type="hidden" name="id_regiao" value="'.$objParceiro->getIdRegiao().'" />':''?>
                            <input type="hidden" name="action" value="<?=$action[0]?>" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
            <?php include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/administrativo/form_parceiros.js"></script>
    </body>
</html>
