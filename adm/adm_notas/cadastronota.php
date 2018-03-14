<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/regiao.php");
include("../../classes/BancoClass.php");
include("../../classes/BotoesClass.php");
include("../../classes/ProjetoClass.php");
include("../../classes/EntradaClass.php");
include("../../classes_permissoes/acoes.class.php");



$ACOES = new Acoes();
$PROJETO = new ProjetoClass();
$BANCO = new Banco();
$regioes = new Regiao();

$usuario = carregaUsuario();


$ArrayBanco = $BANCO->selectBanco($usuario['id_regiao']);


//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];
$objArrayRegiaoAt = $regioes->getRegioesAtivas($master);
$objArrayRegiaoIn = $regioes->getRegioesInativas($master);

$objArrayProjeto = $PROJETO->getProjetosMaster($master);



$nome_pagina = "Cadastro de Notas Fiscas";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Painel"=>"../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>:: Intranet :: <?= $nome_pagina ?></title>
    <link href="../../favicon.png" rel="shortcut icon" />
    <!-- Bootstrap -->
    <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
    <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">


</head>
<body>
<?php include("../../template/navbar_default.php"); ?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - <?php echo $nome_pagina; ?></small></h2></div>
        </div>
    </div>
    <form action="" method="post" id="form_obrigacoes" class="form-horizontal top-margin1" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-heading text-bold"><?php echo $nome_pagina; ?></div>
            <div class="panel-body bloco_obrigacoes">
                <!-- Input do Numero da Nota Fiscal-->

                <div class="form-group">
                    <label class="control-label col-sm-2">Nº da Nota Fiscal:</label>
                    <div class="col-sm-4">
                        <input name="n_nota" id="n_nota" type="text" class="form-control validate[required]" value="">

                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-2">Nota de Empenho:</label>
                    <div class="col-sm-4">
                        <input name="nota_empenho" id="nota_empenho" type="text" class="form-control" value="">
                    </div>
                </div>

                <!-- Select dos Parceiros Operacionais-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Parceiro Operacional:</label>
                    <div class="col-sm-4">
                        <select name="parceiro" id="parceiro" class="selectpicker form-control validate[required]">
                            <option>SELECIONE UM PARCEIRO</option>

                            <optgroup label="Regi&otilde;es Ativas">
                                <?php
                                foreach ($objArrayRegiaoAt as $arrayregselect => $value){ ?>
                                <optgroup label="<?php echo $arrayregselect." - ".$value;?>">
                                    <?php
                                    $objArrayRegePar = $regioes->getParceiros($arrayregselect);
                                    foreach ($objArrayRegePar as $arrayparselect => $value2){
                                        echo '<option value="'.$arrayparselect.'">'.$value2.'</option>';
                                    }
                                    }?>

                                </optgroup>
                                <optgroup label=""></optgroup>

                                <optgroup label="Regi&otilde;es Inativas">
                                    <?php
                                    foreach ($objArrayRegiaoIn as $arrayregselectin => $valuein){ ?>
                                    <optgroup label="<?php echo $arrayregselectin." - ".$valuein;?>">
                                        <?php
                                        $objArrayRegeParIn = $regioes->getParceiros($arrayregselectin);
                                        foreach ($objArrayRegeParIn as $arrayparselectin => $value2in){
                                            echo '<option value="'.$arrayparselectin.'">"'.$value2in."</option>";
                                        }
                                        }?>

                                    </optgroup>
                        </select>
                    </div>
                    <label class="control-label col-sm-2">Projeto:</label>
                    <div class="col-sm-4">
                        <select name="parceiro" id="projeto" class="form-control validate[required]">
                            <option>SELECIONE UM PROJETO</option>
                            <optgroup label="Projetos Ativos"></optgroup>
                            <?php
                            foreach ($objArrayProjeto as $arrayprojetos){
                                echo '<option value="'.$arrayprojetos['id_projeto'].'">'.$arrayprojetos['id_projeto'].' - '.$arrayprojetos['nome'].'</option>';
                            } ?>
                        </select>
                    </div>
                </div>

                <!-- Select dos Tipos de Contrato-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Tipo de Contrato:</label>
                    <div class="col-sm-6">
                        <select name="contrato" id="contrato" class="form-control validate[required]">
                            <optgroup label="Projetos Ativos"></optgroup>
                            <!--<optgroup label="REGIÕES INATIVAS"></optgroup>-->
                        </select>
                    </div>
                </div>

                <!-- Input da data de emissão e Ano da competência-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Data de Emiss&atilde;o:</label>
                    <div class="col-sm-4">
                        <input name="data_emissao" id="data_emissao" type="text" class="form-control data validate[required] datepicker" value="">
                    </div>
                    <label class="control-label col-sm-2">Ano da Compet&ecirc;ncia:</label>
                    <div class="col-sm-4">
                        <?php echo montaSelect(anosArray(), date('Y'), 'name="ano_competencia" id="ano_competencia" class="form-control validate[required]"') ?>
                    </div>
                </div>

                <!-- Input da Descrição-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Descri&ccedil;&atilde;o:</label>
                    <div class="col-sm-10">
                        <input name="descricao" id="descricao" type="text" class="form-control" value="">
                    </div>
                </div>

                <!-- Input do Valor-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Valor:</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input name="valor" id="valor" type="text" class="form-control validate[required]" value="">
                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                        </div>
                    </div>

                    <label class="control-label col-sm-2">Valor ISS:</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input name="valor_iss" id="valor_iss" type="text" class="form-control" value="0">
                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                        </div>
                    </div>
                </div>

                <!-- Select do Tipo de Nota-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Tipo:</label>
                    <div class="col-sm-6">
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="1">1- Nota</option>
                            <option value="2">2 - Carta de medi&ccedil;&atilde;o</option>
                        </select>
                    </div>
                </div>

                <!-- Radio da opcao Criar Entrada no Financeiro-->
                <div class="form-group">
                    <label class="control-label col-sm-2">Criar Entrada no Financeiro:</label>
                    <div class="col-sm-6" style="margin-top:10px;">
                        <input type="checkbox" name="entrada" id="entrada">
                    </div>
                </div>

                <!-- Radio da opcao Criar Entrada no Financeiro-->
                <div class="criaentfinan">
                    <div class="form-group" id="bloco_banco">
                        <label class="control-label col-sm-2">Banco:</label>
                        <div class="col-sm-4">
                            <select name="banco" id="banco" class="form-control">
                                <?php
                                foreach ($ArrayBanco as $arraySelectBanco => $valueBanco) {
                                    echo "<option value='$arraySelectBanco'>" . $valueBanco . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <label class="control-label col-sm-2">Data de Vencimento:</label>
                        <div class="col-sm-4">
                            <input name="data_entrada" id="data_entrada" type="text" class="form-control data">
                        </div>
                    </div>
                </div>

                <!-- Input button para carregar arquivo-->
                <div class="form-group">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <div id="dropzone" class="dropzone"></div>
                    </div>

                </div>
                <!-- Div para o concluir-->
                <div class="form-group">
                    <div class="col-sm-6 pull-right">
                        <input type="hidden" name="action" value="upload_notas">
                        <input type="hidden" name="id_clt" id="id_clt" value="<?php echo $usuario['id_clt'];?>"/>
                        <input type="button" name="enviar" id="enviar" value="Enviar" class="btn btn-success" />
                    </div>

                </div>


                <div class="clear"></div>
            </div>
        </div>
        <?php include("../../template/footer.php"); ?>
</div>
</form>

<script type="text/javascript" src="/intranet/js/jquery-1.3.2.js?tag_rev"></script>
<script type="text/javascript" src="/intranet/js/jquery-1.11.1.min.js?tag_rev"></script>
<script type="text/javascript" src="/intranet/js/jquery-ui-1.9.2.custom.min.js?tag_rev"></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>
<script type="text/javascript" src="../../jquery/mascara/jquery-maskedinput-1.4.1.js" ></script>
<script src="../../js/jquery.validationEngine-2.6.js"></script>
<script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
<script src="../../resources/js/bootstrap.min.js"></script>
<script src="../../resources/js/dropzone.js" type="text/javascript"></script>
<script src="../../resources/js/bootstrap-dialog.min.js"></script>
<script src="../../resources/js/tooltip.js"></script>
<script src="../../resources/js/main.js"></script>
<script src="../../js/global.js"></script>
<script>
    $(function() {

        // Só aceita números
        $('#n_documento').keypress(function(evt){
            var theEvent = evt || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode( key );
            var regex = /\D/g;
            if( regex.test(key) ) {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
            }
        });

        $('#data_emissao').mask('99/99/9999');
        $('#data_entrada').mask('99/99/9999');
        $('#valor').priceFormat();
        $('#valor_iss').priceFormat();
        $(".criaentfinan").hide();
        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone("#dropzone",{
            url: "../../uploadfy/scripts/uploadify.php",
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
        $('body').on('click', '.stat', function(){
            var $this = $(this);
            var key = $this.data('key');
//
//                    $(".panel-1, .panel-2, .panel-3, .panel-4").addClass('hide');
//                    $(".panel-"+key).removeClass('hide');

            $(".panel-1, .panel-2, .panel-3, .panel-4").slideUp();
            $( ".seta" ).removeClass( "fa-arrow-circle-up" ).addClass( "fa-arrow-circle-down" );
            if($('.panel-'+key).css('display') == 'none'){
                $('.panel-'+key).slideDown();
                $this.find( ".seta" ).removeClass( "fa-arrow-circle-down" ).addClass( "fa-arrow-circle-up" );
            } else {
                $this.find( ".seta" ).removeClass( "fa-arrow-circle-up" ).addClass( "fa-arrow-circle-down" );
            }
        });

        $('body').on('click', '.entregar', function(){
            var rota = $(this).data('rota');

            var html =
                $('<form>', { class: 'form-horizontal', id: 'form-entregar', method: 'post', action: 'cad_rota.php' }).append(
                    $('<div>', { class: 'col-md-offset-1 col-md-10' }).append(
                        $('<div>', { class: 'form-group' }).append(
                            $('<label>', { text: 'Km Final' }),
                            $('<input>', { type: 'text', class: 'form-control', name: 'km' }),
                            $('<input>', { type: 'hidden', name: 'enviar4' }),
                            $('<input>', { type: 'hidden', name: 'rota', value: rota })
                        )
                    ),
                    $('<div>', { class: 'clear' })
                )

            bootConfirm(
                html,
                'Entregar',
                function(data){
                    if(data == true){
                        $('#form-entregar').submit();
                    }
                },
                'info'
            );
        });

        $('#projeto').change(function () {

            var id_projeto = $(this).val();

            $('#contrato').html('<option value="">Carregando...</option>');

            $.ajax({
                'url': 'actions/combo.subprojeto.json.php',
                'data': {'id_projeto': id_projeto},
                'success': function (resposta) {

                    $('#contrato').html('');



                    $.each(resposta, function (i, valor) {

                        $('#contrato').append('<optgroup label="' + i + '">');

                        $.each(valor, function (chave, registro) {

                            $('#contrato').append('<option value="' + registro.tipo + '_' + registro.id_subprojeto + '" > Contrato Nº: <b>' + registro.numero_contrato + '</b> Inicio : ' + registro.inicio + ' - Fim : ' + registro.termino + '</option>');
                        });

                        $('#contrato').append('</optgroup>');

                    });



                },
                'dataType': 'json'
            });
        });

        $('#entrada').click(function () {
            $('.criaentfinan').fadeToggle("slow");
            if($(this).prop("checked")){
                $("#banco").addClass("validate[required,custom[select]]");
                $("#data_entrada").addClass("validate[required]");
            }else{
                $("#banco").removeClass("validate[required,custom[select]]");
                $("#data_entrada").removeClass("validate[required]");
            }
        });

        $("#enviar").on('click', function(){

            if ($("#form_obrigacoes").validationEngine('validate')) {
                if( myDropzone.files == ""){
                    bootAlert('Por favor, anexe um arquivo para finalizar o cadastro', 'Erro no Cadastro', '', 'info');
                    return false;
                }
                var entrada = $("#entrada").is(":checked");
                // if(entrada){ entrada = 0;}else{ entrada = 1;}
                cria_carregando_modal();
                // $.post("../../uploadfy/scripts/uploadify.php", $('#form_obrigacoes').serialize(), function(resposta){


                myDropzone.on('sending',function(file, xhr, formData) {
                    formData.append("n_nota", $("#n_nota").val()); // Append all the additional input data of your form here!
                    formData.append("nota_empenho", $("#nota_empenho").val()); // Append all the additional input data of your form here!
                    formData.append("parceiro", $("#parceiro").val()); // Append all the additional input data of your form here!
                    formData.append("descricao", $("#descricao").val()); // Append all the additional input data of your form here!
                    formData.append("data_emissao", $("#data_emissao").val()); // Append all the additional input data of your form here!
                    formData.append("valor", $("#valor").val()); // Append all the additional input data of your form here!
                    formData.append("valor_iss", $("#valor_iss").val()); // Append all the additional input data of your form here!
                    formData.append("projeto", $("#projeto").val()); // Append all the additional input data of your form here!
                    formData.append("ano_competencia", $("#ano_competencia").val()); // Append all the additional input data of your form here!
                    formData.append("contrato", $("#contrato").val()); // Append all the additional input data of your form here!
                    formData.append("data_entrada", $("#data_entrada").val()); // Append all the additional input data of your form here!
                    formData.append("entrada", entrada); // Append all the additional input data of your form here!
                    formData.append("banco", $("#banco").val()); // Append all the additional input data of your form here!
                    formData.append("action", 'upload_notas'); // Append all the additional input data of your form here!
                });

                myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));

                remove_carregando_modal();

                bootDialog(
                    'Nota Cadastrada Com Sucesso!',
                    'Nota Cadastrada!',
                    [{
                        label: 'Fechar',
                        action: function(){
                            window.location.href = "cadastronota.php";
                        }
                    }],
                    'success'
                );

                //});
            }
        });

        /*Dropzone.autoDiscover = false;
       var myDropzone = new Dropzone("#dropzone",{
           url: "../../uploadfy/scripts/uploadify.php",
           addRemoveLinks : true,
           maxFilesize: 10,

           autoQueue: false,

           dictResponseError: "Erro no servidor!",
           dictCancelUpload: "Cancelar",
           dictFileTooBig: "Tamanho máximo: 10MB",
           dictRemoveFile: "Remover Arquivo",
           canceled: "Arquivo Cancelado",
           acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
       });*/


        $("#form_obrigacoes").validationEngine();



    });
</script>
</body>
</html>