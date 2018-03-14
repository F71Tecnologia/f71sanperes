<?php
include('../../conn.php');
if($_REQUEST['recisao_coletiva'] != 1){
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=true';</script>";
        exit;
    }
}
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/MovimentoClass.php');
include('../../classes/CltClass.php');
include('../../classes/RescisaoClass.php');
include('../../classes/CalculoFolhaClass.php');
  
$usuario = carregaUsuario();
$rescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();
$objClt = new CltClass();


$sql_tipo_dispensa = "SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY codigo ASC";
$qr_dispensa = mysql_query($sql_tipo_dispensa);
$arr_tipo_dispensa = array('-1'=>'« Selecione »');
while($row = mysql_fetch_array($qr_dispensa)){
    $arr_tipo_dispensa[$row['codigo']] = $row['codigo'].' - '.$row['especifica'];
}

$arr_fator = array('-1'=>'« Selecione »', 'empregado'=>'Empregado', 'empregador'=>'Empregador');
$arr_aviso_previo = array('-1'=>'« Selecione »', 'indenizado'=>'Indenizado', 'trabalhado'=>'Trabalhado');

list($regiao, $id_clt) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

$checaRescisao = $rescisao->getRescisaoByClt($id_clt);

if(mysql_num_rows($checaRescisao)>0){
    exit('Já existe uma rescisão para este funcionário.');
}

//$clt = $objClt->getDadosClt($id_clt);
$clt = $objClt->carregaClt($id_clt);



if(isset($_GET['print'])){
    echo '<pre>';
    print_r($clt);
    echo '</pre>';
}




        

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Intranet :: Rescis&atilde;o</title>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <!-- NOVO -->
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/datepicker.css" rel="stylesheet" media="screen">  
        <!-- FIM DO NOVO -->
        
        <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/global.js"></script>
        
        <script type="text/javascript">

            $(function() {
                $("#diastrab").mask("99");
                $("#faltas").mask("99");
                $("#previo").mask("99");
                
                $('#dias_aviso').hide();
                $('#aviso_previo').change(function(e){
                    $this = $(this);
                    if($this.val()=='indenizado'){
                        $('#dias_aviso_label').text('indenização');
                        $('#dias_aviso').show();
                    }else if($this.val()=='trabalhado'){
                        $('#dias_aviso_label').text('trabalho');
                        $('#dias_aviso').show();
                    }else{
                        $('#previo').val('');
                        $('#dias_aviso').hide();
                    }
                });
            });
                
        </script>
    </head>
    <body class='novaintra' cz-shortcut-listen="true">
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - RESCISÃO</h2></div>                                                                                      
            <form action="controlador.php" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <!--resposta de algum metodo realizado-->
                <fieldset>
                    <legend>DADOS DA RESCISÃO</legend>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="diastrab" class="col-lg-2 control-label">
                                        <div class="thumbnail" style="width: 82px; float: right;">
                                                <img src="<?= $clt->getFoto('../../'); ?>" alt="<?= $clt->nome; ?>" />
                                        </div>
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="funcionario"  disabled="disabled"  name="funcionario" value="<?= $clt->id_clt.' - '.$clt->nome; ?>">
                                            <br>
                                        <input type="text" class="form-control" id="funcionario"  disabled="disabled"  name="funcionario" value="<?= $clt->id_curso.' - '.$clt->nome_curso; ?>">
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="form-group">
                                    <label for="dispensa" class="col-lg-2 control-label">Tipo de Dispensa:</label>
                                    <div class="col-lg-9">
                                        <?= montaSelect($arr_tipo_dispensa, NULL, ' id="dispensa" name="dispensa" class="form-control validate[required,custom[select]]" '); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="fator" class="col-lg-2 control-label">Fator:</label>
                                    <div class="col-lg-9">
                                        <?= montaSelect($arr_fator, NULL, ' id="fator" name="fator" class="form-control validate[required,custom[select]]" '); ?>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="dias_trabalhados" class="col-lg-2 control-label">Dias de Saldo do Salário:</label>
                                    <div class="col-lg-2">
                                        <input type="text" class="form-control" id="dias_trabalhados" name="dias_trabalhados">
                                    </div>
                                </div>
                            </div>                            
                            <div class="row">
                                <div class="form-group">
                                    <label for="remuneracao_rescisorios" class="col-lg-2 control-label">Remuneração para Fins Rescisórios:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control validate[required] money" id="remuneracao_rescisorios" name="remuneracao_rescisorios">
                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>        
<!--                            <div class="row">
                                <div class="form-group">
                                    <label for="faltas" class="col-lg-2 control-label">Quantidade de Faltas:</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="faltas" name="faltas">
                                    </div>
                                </div>
                            </div>-->
                            <div class="row">
                                <div class="form-group">
                                    <label for="aviso_previo" class="col-lg-2 control-label">Aviso prévio:</label>
                                    <div class="col-lg-4">
                                        <?= montaSelect($arr_aviso_previo, NULL, ' id="aviso_previo" name="aviso_previo" class="form-control validate[required,custom[select]]" '); ?>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="dias_aviso">
                                <div class="form-group">                                    
                                    <label for="dias" class="col-lg-2 control-label">Dias de <span id="dias_aviso_label"></span>:</label>
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" id="dias" name="dias">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="data_aviso" class="col-lg-2 control-label">Data do Aviso:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control validate[required] date_f" id="data_aviso" name="data_aviso">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="devolucao_credito" class="col-lg-2 control-label">Devolução de Crédito Indevido:</label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control money" id="devolucao_credito" name="devolucao_credito">
                                            <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">                            
                            <input type="submit" class="btn btn-primary" id="cadastrar" />
                            <input type="hidden" name="acao" id="acao" value="calcular_rescisao" />
                            <input type="hidden" name="id_clt" id="id_clt" value="<?= $id_clt ?>" />
                        </div>
                    </div>
                </fieldset>
            </form>
            <button type="button" class="btn btn-default" onclick="window.history.go(-1)" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>            
        </div>
    </body>
</html>