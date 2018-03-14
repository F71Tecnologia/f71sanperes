<?php
header("Location: /intranet");
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include "../../classes/uploadfile.php";
include("../../classes/ProjetoClass.php");
include("../../classes/CaixinhaClass.php");
include("../../classes/CaixinhaAnexosClass.php");
//include("../../classes/SaidaClass.php");
$objCaixinha = new CaixinhaClass();
$objProjeto = new ProjetoClass();
//$saida = new Saida();
$objCaixinhaAnexos = new CaixinhaAnexosClass();
$usuario = carregaUsuario();

$arrayProjetos = $objProjeto->getProjetosMaster();
$optProjetos[''] = '--SELECIONE--';
foreach ($arrayProjetos as $key => $value) {
    $optProjetos[$value['id_projeto']] = $value['nome'];
}

if($_REQUEST['id_caixinha']){
    $objCaixinha->setIdCaixinha($_REQUEST['id_caixinha']);
    $objCaixinha->getById();
    $objCaixinha->getRow();
    
    $objCaixinhaAnexos->setIdCaixinha($objCaixinha->getIdCaixinha());
    $objCaixinhaAnexos->getByIdCaixinha();
}

/**
 * Pega o saldo atual
 */
if($_REQUEST['action'] && $_REQUEST['action'] == 'getSaldo'){
    
//    print_array($_REQUEST);
    $objCaixinha->setIdCaixinha($_REQUEST['id_caixinha']);
    $objCaixinha->setIdProjeto($_REQUEST['id_projeto']);
    $objCaixinha->setIdTipo($_REQUEST['id_tipo']);
    $saldo = $objCaixinha->getSaldoCaixinhasByMes();
    $valor = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor']));
    $diferenca = ($saldo - $valor);
    
    $array = array('valor' => $valor, 'saldo' => $saldo, 'diferenca' => $diferenca);
//    echo json_encode($array);
    echo $diferenca;
    exit;
}

/**
 * UPLOAD DE ARQUIVOS
 */
if($_REQUEST['action'] && $_REQUEST['action'] == 'upload_anexo'){

    $id_caixinha = $_REQUEST['id_caixinha'];
        
    $diretorio = "anexo";

    $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
    $upload->arquivo($_FILES[file]);
    $upload->verificaFile();

    $uni = uniqid();
    
    $insert = "INSERT INTO caixinha_anexos (id_caixinha, nome, extensao) VALUES ('$id_caixinha','{$id_caixinha}_{$uni}.{$upload->extensao}', '{$upload->extensao}');";
    mysql_query($insert)or die(mysql_error());
    $id = mysql_insert_id();

    $upload->NomeiaFile("{$id_caixinha}_{$uni}");
    $upload->Envia();
    exit;
}

/**
 * DELETAR UPLOAD
 */
if($_REQUEST['action'] && $_REQUEST['action'] == 'deleteAnexo'){

    $id = $_REQUEST['id'];
    $objCaixinhaAnexos->setIdAnexo($id);
    if($objCaixinhaAnexos->deleta()){
        echo "Anexo excluido com Sucesso!";
    } else {
        echo "Erro ao excluir o Anexo!";
    }
    exit;
}

if(isset($_POST['salvar'])) {
    
    if($_REQUEST['saldo']) $saldo = str_replace(',', '.', str_replace('.', '', $_REQUEST['saldo']));
    $objCaixinha->setIdProjeto($_REQUEST['id_projeto']);
    $objCaixinha->setIdUnidade($_REQUEST['id_unidade']);
    $objCaixinha->setData(implode('-', array_reverse(explode('/',$_REQUEST['data']))));
    $objCaixinha->setTipo($_REQUEST['tipo']);
    $objCaixinha->setDescricao(utf8_decode($_REQUEST['descricao']));
    $objCaixinha->setSaldo($saldo);
    $objCaixinha->setDataCad(date('Y-m-d'));
    $objCaixinha->setUserCad($usuario['id_funcionario']);
    $objCaixinha->setStatus(1);
    $objCaixinha->setIdTipo($_REQUEST['id_tipo']);
    $objCaixinha->setIdItem($_REQUEST['id_item']);
    $objCaixinha->insert();
    echo $objCaixinha->getIdCaixinha();
//    header('Location: index.php');
    exit;
} else if(isset($_POST['editar'])) {
    
    $objCaixinha->setIdCaixinha($_REQUEST['id_caixinha']);
    $objCaixinha->setIdProjeto($_REQUEST['id_projeto']);
    $objCaixinha->setIdUnidade($_REQUEST['id_unidade']);
    $objCaixinha->setData(implode('-', array_reverse(explode('/',$_REQUEST['data']))));
    $objCaixinha->setTipo($_REQUEST['tipo']);
    $objCaixinha->setDescricao(utf8_decode($_REQUEST['descricao']));
    $objCaixinha->setSaldo(str_replace(',', '.', str_replace('.', '', $_REQUEST['saldo'])));
    $objCaixinha->setIdTipo($_REQUEST['id_tipo']);
    $objCaixinha->setIdItem($_REQUEST['id_item']);
    $objCaixinha->update();
    echo $objCaixinha->getIdCaixinha();
//    header('Location: index.php');
    exit;
} 

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'inativar') {
    $objCaixinha->setIdCaixinha($_REQUEST['id_caixinha']);
    if($objCaixinha->inativa()) echo '1';
    exit;
}

$nome_pagina = "Lançamento Caixinha";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
$breadcrumb_pages = array("MOVIMENTAÇÃO DE CAIXA" => "../caixinha");

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
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
        <!--<link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">-->
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro <small> - <?= $nome_pagina ?></small></h2></div>
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="" id="">
                                        <?php echo montaSelect($optProjetos, $objCaixinha->getIdProjeto(), 'class="form-control input-sm validate[required]" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">Data:</div>
                                    <div class="" id="">
                                        <input type="text" class="data form-control input-sm validate[required]" id="data" name="data" value="<?= $objCaixinha->getData('d/m/Y') ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Item Despesa:</div>
                                    <div class="" id="">
                                        <?php echo montaSelect($objCaixinha->getItensDespesas(), $objCaixinha->getIdItem(), 'class="form-control input-sm validate[required]" id="id_item" name="id_item"') ?>
                                    </div>
                                </div>
<!--                            </div>
                            <div class="form-group">-->
                                <div class="col-sm-2">
                                    <div class="text-bold">Valor:</div>
                                    <div class="" id="">
                                        <input type="text" class="valor form-control input-sm validate[required]" id="saldo" name="saldo" value="<?= number_format($objCaixinha->getSaldo(), 2, ',', '.') ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">Saldo:</div>
                                    <div class="text-bold" id='text-saldo'>Saldo:</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="text-bold">Descrição:</div>
                                    <div class="">
                                        <textarea class="form-control input-sm validate[required]" rows="3" id="descricao" name="descricao"><?= $objCaixinha->getDescricao() ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <h4 class="text-bold">Anexos:</h4>
                            <div class="form-group">
                                <?php while ($objCaixinhaAnexos->getRow()) { ?>
                                <div class="col-xs-2 margin_b5 <?= $objCaixinhaAnexos->getIdAnexo() ?>">
                                    <div class="thumbnail">
                                        <a href="anexo/<?= $objCaixinhaAnexos->getNome() ?>" target="_blank">
                                            <img class="h-100" src="../../imagens/icons/att-<?= $objCaixinhaAnexos->getExtensao() ?>.png">
                                        </a>
                                        <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexo" style="width: 100%;" data-key="<?= $objCaixinhaAnexos->getIdAnexo() ?>"> Deletar</span>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div id="dropzone" class="dropzone" style="height: 250px!important; min-height: 250px!important;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <?php if($objCaixinha->getIdCaixinha()){ ?><input type="hidden" name="id_caixinha" value="<?= $objCaixinha->getIdCaixinha() ?>"><?php } ?>
                            <input type="hidden" name="tipo" value="1">
                            <input type="hidden" name="subgrupo_bd" id="subgrupo_bd" value="<?php echo $subgrupo_bd; ?>" />
                            <input type="hidden" name="tipo_bd" id="tipo_bd" value="<?php echo $tipo_bd; ?>" />
                            <input type="hidden" name="<?= ($objCaixinha->getIdCaixinha()) ? 'editar' : 'salvar' ?>">
                            <button type='button' class="btnSubmit btn btn-primary hide"><i class="fa fa-filter"></i> SALVAR</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
        $(function(){
            $('#form1').validationEngine();
            
            function pegaSaldo(){
                if($('#id_projeto').val() > 0) {
//                    console.log("result");
                    $.post("", {bugger:Math.random(), action:'getSaldo', valor:$('#saldo').val() , id_unidade:$('#id_unidade').val(), id_projeto:$('#id_projeto').val(), id_tipo:$('#id_tipo').val(), id_caixinha: '<?= $objCaixinha->getIdCaixinha() ?>'}, function(result){
//                        console.log(result);
                        $('#text-saldo').html('R$ ' + number_format(result,2,',', '.'));
                        if(result >= 0){
                            $('.btnSubmit').removeClass('hide');
                        } else {
                            bootAlert('O Saldo não pode ser inferior a R$ 0,00','Alerta',null,'danger');
                            $('.btnSubmit').addClass('hide');
                        }
                    }); 
                } else {
                    $('#text-saldo').html('R$ ' + number_format(0.00,2,',', '.'));
                    $('.btnSubmit').addClass('hide');
                }
            }
            pegaSaldo();
            $('#saldo').keyup(function(){
                pegaSaldo();
            });
            $('body').on('change', '#id_projeto', function(){
                pegaSaldo();
            });
            
            var myDropzone = new Dropzone("#dropzone",{
                url: "form_caixinha.php",
                addRemoveLinks : true,
                maxFilesize: 20,
                //envio automatico
                autoQueue: false,
                dictResponseError: "Erro no servidor!",
                dictCancelUpload: "Cancelar",
                dictFileTooBig: "Tamanho máximo: 20MB",
                dictRemoveFile: "Remover Arquivo",
                canceled: "Arquivo Cancelado",
                acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
//                    , success: function(file, responseText){
//                        console.log(responseText);
//                        //$('.close').trigger('click');
//                    }
//                , totaluploadprogress: function(p){
//                    if(p >= 100) {
//                        bootDialog(
//                            'Caixinha Cadastrada Com Sucesso!', 
//                            'Cadastro de Caixinha!', 
//                            [{
//                                label: 'Fechar',
//                                action: function(){
////                                    window.location.href = "../caixinha";
//                                }
//                            }], 
//                            'success'
//                        );
//                    }
//                }
            });
            
            $(".btnSubmit").on('click', function(){
                if ($("#form1").validationEngine('validate')) {
                    var dados = $('#form1').serialize();
                    $.post("", dados, function(resposta){

                        myDropzone.on('sending',function(file, xhr, formData) {
                            formData.append("id_caixinha", resposta); // Append all the additional input data of your form here!
                            formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
                        });

                        myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));

                        remove_carregando_modal();
                        bootAlert('Caixinha salvo!', 'SALVO', function(){ window.location='form_caixinha.php?id_caixinha=' + resposta; }, 'success');
                        
                    }); 
                }
            });
            
            $("body").on('click', ".deleteAnexo", function(){
                var id = $(this).data("key");
                bootConfirm("Deseja Excluir este Anexo?","Excluir Anexo", function(data){
                    if(data == true){
                        $.post("", {bugger:Math.random(), id:id, action:'deleteAnexo'}, function(resultado){
                            cria_carregando_modal();
                            bootAlert( resultado, 'Exclusão de Anexo', function(){ $('.'+id).remove(); }, 'info');
                            if(resultado){
                                remove_carregando_modal();
                            }
                        });
                    }
                },"warning");
            }); 
        });
        </script>
    </body>
</html>
