<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";
include_once('../../classes/ArquivoTxtBancoClass.php');
$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

if(isset($_GET['delArq']) AND !empty($_GET['delArq'])){
    $ArquivoTxtBancoClass->deletarRegistro($_GET['delArq']);
    header("Location: arquivo_banco_rescisao.php");exit;
}else if(isset($_GET['arq'])){
    $download = $ArquivoTxtBancoClass->downloadArquivo($_GET['arq']);
}

$arrayArquivos = $ArquivoTxtBancoClass->getRegistrosRescisao($usuario['id_regiao']);
//echo "<pre>"; print_r($arrayArquivos); echo '</pre>';
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Arquivos de Banco para Rescisão");
$breadcrumb_pages = array("Gestão de RH" => "../", "Rescisão" => "index.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Arquivos de Banco para Rescisão</title>
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
        <style>
            .hid { display: none; }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form name="form" action="" method="post" id="form1" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Arquivos de Banco para Rescisão</small></h2></div>
                </div>
            </div>
            <?php //if(isset($_REQUEST['mes_inicio'])){
                $count = 0;
                if(is_array($arrayArquivos)){
                    foreach ($arrayArquivos as $key => $value) {
                        if($value['tipo_conta'] == 'c'){$tipoConta = 'CORRENTE';}
                        if($value['tipo_conta'] == 's'){$tipoConta = 'SALARIO';}
                        if($auxNomeArquivo != $value['nome_arquivo']){
                            $count++;
                            $nomeArquivo = explode('/', $value['nome_arquivo']);
                            $linha .= "
                            <thead>
                                <tr class='info valign-middle'>
                                    <th width='30%'>{$nomeArquivo[2]}</th>
                                    <th width='25%'>Pago em: {$value['data']}</th>
                                    <th width='15%' class='text-center'><a class='arquivo btn btn-xs btn-default' data-id='{$count}' href='javascript:void(0);'><i class='fa fa-search'></i></a></th>
                                    <th width='15%' class='text-center'><a href='?arq=".md5($key)."' class='btn btn-xs btn-success'><i class='fa fa-download'></i></a></th>
                                    <th width='15%' class='text-center'><a class='link btn btn-xs btn-danger' href='?delArq=".md5($value['nome_arquivo'])."'><i class='fa fa-trash-o'></i></a></th>
                                </tr>
                                <tr class='hid {$count} valign-middle'>
                                    <th colspan='2'>NOME</th>
                                    <th>BANCO</th>
                                    <th>VALOR</th>
                                    <th>TIPO CONTA</th>
                                </tr>
                            </thead>";
                            $auxNomeArquivo = $value['nome_arquivo'];
                        }
                        $linha .= "
                        <tr class='hid {$count} valign-middle'>
                            <td colspan='2'>{$value['nome']}</td>
                            <td>{$value['razao']}</td>
                            <td>".number_format($value['total_liquido'],2,',','.')."</td>
                            <td>{$tipoConta}</td>
                        </tr>";
                    }
                }else{
                    $linha = "
                    <tr>
                        <td>Nenhum Arquivo Encontrado!</td>
                    </tr>";
                }
                ?>
                <!--<table class="table table-bordered table-striped table-header table-action" style="width: 95%; margin: 5% 2.5% 0% 2.5%;">-->
                <table class="table table-bordered table-striped table-condensed table-hover">
                    <?php echo $linha; ?>
                </table>
            <?php //} ?>
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
            $(function(){
                $('.arquivo').click(function(){
                    var id = $(this).data('id');
                    $('.'+id).toggle();
                });
                
                $('.link').click(function(e){
                    e.preventDefault();
                    var targetUrl = $(this).attr("href");
                    bootConfirm('Deseja realmente excluir o arquivo?', 'Excluir', 
                    function(data){
                        if(data == true){
                            window.location.href = targetUrl;
                        }
                    }, 'danger');
                });
                <?php if(!$download AND isset($_GET['arq'])){ ?>
//                    thickBoxAlert('Erro','Arquivo não encontrado!','auto','auto',null);
                    bootAlert('Arquivo não encontrado!', 'Erro', null, 'info');
                <?php } ?>
            });
        </script>
    </body>
</html>