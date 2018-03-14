<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/EntradaClass.php");
include("../classes/BancoClass.php");
include("../classes/LogClass.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$entrada_id = (isset($_REQUEST['entrada'])) ? $_REQUEST['entrada'] : $_SESSION['id_entrada'];

$log = new Log();
$entrada = new Entrada();
$global = new GlobalClass();
$banco = new Banco();

$regiao_selecionada = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$row = $entrada->getEntradaID($entrada_id);
$regiao_bd = $row['id_regiao'];
$projeto_bd = $row['id_projeto'];
$banco_bd = $row['id_banco'];
$tipo_bd = $row['entrada_tipo'];
$parceiro_bd = $row['parceiro_cod'];

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    echo $id_entrada = $entrada->cadEntrada($usuario['id_regiao']);
    $log->gravaLog('Cadastrar Entrada', 'Cadastro Entrada '.$id_entrada); exit;
}

//update
if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    echo $id_entrada = $entrada->alteraEntrada();
    $log->gravaLog('Editar Entrada', 'Edição Entrada '.$id_entrada); exit;
}

//para desaparecer com alguns inputs, quando for edição
$some = false;
$sqlB = "SELECT * FROM bancos";
$qryB = mysql_query($sqlB);
while ($rowB = mysql_fetch_assoc($qryB)) {
    $arrayBancos[$rowB['id_banco']] = $rowB['id_banco'] . ' - ' . $rowB['nome'];
    //$arrIdBanco[$rowB['id_projeto']] = $rowB['id_banco']; // tava dando merda, melhor comentar
}
//trata insert/update
if($entrada_id == ''){
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $projeto = montaSelect(getProjetos($regiao_selecionada),null, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'");
    $entrada_sel = montaSelect($arrayBancos,null, "id='banco' name='banco' class='form-control validate[required,custom[select]]'");
    $tipo = montaSelect($entrada->getTipo(),null, "id='tipo' name='tipo' class='form-control validate[required,custom[select]]'");
    $regiao_notas = montaSelect($entrada->getRegiaoNotas($regiao_selecionada, $id_master),null, "id='regiao' name='regiao' class='form-control'");
    $parceiro = montaSelect(array(""=>"« Selecione a Região »"), null, "id='parceiro' name='parceiro' class='form-control'");       
}else{
    $acao = 'Editar';
    $botao = 'Atualizar';    
    $tipo = montaSelect($entrada->getTipo(),$tipo_bd, "id='tipo' name='tipo' class='form-control validate[required,custom[select]]'");
    $regiao_notas = montaSelect($arrayBancos,$regiao_bd, "id='regiao' name='regiao' class='form-control'");
    $parceiro = montaSelect(array(""=>"« Selecione a Região »"), $parceiro_bd, "id='parceiro' name='parceiro' class='form-control'");
    $some = true;
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$acao de Entrada");
$breadcrumb_pages = array("Principal" => "index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?=$acao?> de Entrada <?=$entrada_id?></title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?=$acao?> de Entrada <?=$entrada_id?></small></h2></div>                                                                                      
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                <!--resposta de algum metodo realizado-->
                <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
                
                <input type="hidden" id="regiao_selecionada" name="regiao_selecionada" value="<?php echo $regiao_selecionada; ?>" />                
                <input type="hidden" id="id_nota" name="id_nota" value="" />
                <input type="hidden" id="hide_parceiro" name="hide_parceiro" value="<?php echo $parceiro_bd; ?>" />
                <input type="hidden" id="id_entrada" name="id_entrada" value="<?php echo $entrada_id; ?>" />
                <input type="hidden" id="banco_sel" name="banco_sel" value="<?php echo $_REQUEST['banco']; ?>" />
                <input type="hidden" id="mes_sel" name="mes_sel" value="<?php echo $_REQUEST['mes']; ?>" />
                <input type="hidden" id="ano_sel" name="ano_sel" value="<?php echo $_REQUEST['ano']; ?>" />
                <input type="hidden" name="home" id="home" value="" />
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold"><?=$acao?> de Entradas</div>
                    <div class="panel-body">

                        <?php if(!$some){ ?>
                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label">Projeto</label>
                                <div class="col-lg-9">
                                    <?php echo $projeto; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label">Conta para Crédito</label>
                                <div class="col-lg-9">
                                    <?=$entrada_sel?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label">Nome</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control validate[required]" id="nome" name="nome" value="<?=$row['nome']?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label">Descrição</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="especifica" name="especifica" value="<?=$row['especifica']?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label">Nº do Documento</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="n_documento" name="n_documento"
                                           value="<?= $row['numero_doc'] ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label">Tipo</label>
                                <div class="col-lg-9">
                                    <?=$tipo?>
                                </div>
                            </div>
                        </div>

                        <!--classe para exibir somente quando tipo for 12-->
                        <div class="minus">
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Região</label>
                                    <div class="col-lg-9">
                                        <?=$regiao_notas?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Parceiros</label>
                                    <div class="col-lg-9">      
                                        <input type="hidden" value="" id="alerta_par" />
                                        <?php echo $parceiro; ?>
                                        <div id="alerta_parceiro">
                                            <?=$global->getResposta('danger', 'Nenhuma nota cadastrada')?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="notas" class="col-lg-offset-2 col-lg-9">
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Subtipo</label>
                                    <div class="col-lg-4">
                                        <select id='subtipo' name='subtipo' class='form-control'>
                                            <option value="">« Selecione »</option>
                                            <option value="1" <?=selected('1', $row['subtipo'])?>>Doc</option>
                                            <option value="2" <?=selected('2', $row['subtipo'])?>>Ted</option>
                                            <option value="3" <?=selected('3', $row['subtipo'])?>>Cheque</option>
                                            <option value="4" <?=selected('4', $row['subtipo'])?>>Dinheiro</option>
                                            <option value="5" <?=selected('5', $row['subtipo'])?>>Transferência</option>
                                        </select>
                                    </div>
                                    <div id="num">
                                        <label for="mensagem" class="col-lg-1 control-label">Nº</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="n_subtipo" name="n_subtipo" value="<?=$row['n_subtipo']?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row">
                            <div class="form-group">
                                <?php if(!$some){ ?>
                                <!--<label for="mensagem" class="col-lg-2 control-label">Custo Adicional</label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="adicional" name="adicional" />
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>-->
                                <label for="mensagem" class="col-lg-2 control-label">Valor</label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control validate[required]" id="valor" name="valor">
                                        <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                    </div>
                                </div>
                                <?php } ?>
                                <label for="mensagem" class="col-lg-2 control-label">Data para crédito</label>
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control data validate[required]" placeholder="Selecione uma data" id="data_credito" name="data_credito" value="<?=(!empty($row['data_vencimento'])) ? converteData($row['data_vencimento'], "d/m/Y") : ''?>">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>                                
                            </div>
                        </div>
<!--
                        <div class="row">
                            <div class="form-group">
                                
                                <label for="mensagem" class="col-lg-2 control-label"></label>
                                <div class="col-lg-4">
                                    <div class="input-group">

                                    </div>
                                </div>
                            </div>                                
                        </div>-->

                        <div class="row">
                            <div class="form-group">
                                <label for="mensagem" class="col-lg-2 control-label"></label>
                                <div class="col-lg-9">
                                    <div id="dropzone" class="dropzone"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer text-right">
                        <input type="button" class="btn btn-primary botaoSubmit" value="<?=$botao?>" />
                        <input type="hidden" name="<?=strtolower($botao)?>" id="<?=strtolower($botao)?>" value="<?=$botao?>" />
                    </div>
                </div>
            </form>
            
            <!--<button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>-->
            
            <?php include("../template/footer.php"); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>        
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/main_bts.js"></script><!--TIRAR _BTS-->
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/entrada.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../methods.php", {method: "carregaParceiros"}, function(data){
                    var $this = $("#parceiro");
                    if($this.val() != "" && $this.val() != "-1"){
                        $('#parceiro').trigger("change");
                    }
                }, "parceiro");
                
                $("body").on('change', '#projeto', function(){
                    $.post("actions/action_entrada.php", {bugger:Math.random(), pega_id_banco:'pega_id_banco', id_projeto:$(this).val()}, function(resultado){
                        $("#banco").val(resultado);
                    });
                });
                
                $("#form1").validationEngine({promptPosition : "topRight"});
                $("#adicional, #valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $("#data_credito").mask("99/99/9999");
                
                //datepicker
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
                
                Dropzone.autoDiscover = false;
                var myDropzone = new Dropzone("#dropzone",{
                    url: "actions/action_entrada.php",
                    addRemoveLinks : true,
                    maxFilesize: 10,
                    
                    autoQueue: false,
                    
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 10MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                });
                
                $(".botaoSubmit").on('click', function(){
                    if ($("#form1").validationEngine('validate')) {
                        var dados = $('#form1').serialize();
                        cria_carregando_modal();
                        $.post("form_entrada.php", dados, function(resposta){

                            myDropzone.on('sending',function(file, xhr, formData) {
                                formData.append("id_entrada", resposta); // Append all the additional input data of your form here!
                                formData.append("upload_anexo", 'upload_anexo'); // Append all the additional input data of your form here!
                            });

                            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));

                            remove_carregando_modal();

                            bootDialog(
                                'Entrada Cadastrada Com Sucesso!', 
                                'Entrada Cadastrada!', 
                                [{
                                    label: 'Fechar',
                                    action: function(){
                                        window.location.href = "../finan";
                                    }
                                }], 
                                'success'
                            );

                        });
                    }
                });
                
            });
        </script>                                
    </body>
</html>