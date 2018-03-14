<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos_caged' . DIRECTORY_SEPARATOR . $_REQUEST['download'];
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

include("../../conn.php");
include("../../wfunction.php");
include('../../classes/global.php');

$meses = mesesArray();
$anos = anosArray();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$sql = "SELECT nome FROM `master` WHERE id_master = {$usuario['id_master']} AND status=1";
$result = mysql_query($sql);
$master = mysql_fetch_array($result);

//$sql = "SELECT * FROM `master` WHERE status=1";
//$result = mysql_query($sql);
//$array_master = array();
//while($resp = mysql_fetch_array($result)){
//    $array_master[$resp['id_master']] = $resp['razao'];
//}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "CAGED");
$breadcrumb_pages = array("Principal RH" => "../../rh/principalrh.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: CAGED</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="glyphicon glyphicon-user"></span> - Recursos Humanos<small> - CAGED</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php echo $master['nome']; ?> <input type="hidden" id="master" name="master" value="<?=$usuario['id_master'];?>"></div>
                    <div class="panel-body">
                        
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto: </label>
                            <div class="col-lg-9">
                               <?php echo montaSelect(GlobalClass::carregaProjetos($usuario['id_master'],array('-1'=>'- Todos -'), false), $usuario['id_projeto'], array('name' => "projeto", 'id' => 'projeto', 'class'=>'form-control')) ;   ?>
                               <?php // echo montaSelect(array('-1'=>'- Todos -'), $usuario['id_projeto'], array('name' => "projeto", 'id' => 'projeto', 'class'=>'form-control')) ;   ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Tipo: </label>
                            <div class="col-lg-9">
                                <?php echo montaSelect(array('2'=>'Envio Mensal','4'=>'Acerto Mensal','1'=>'Envio Diario','3'=>'Acerto Diario'), NULL, array('name' => 'tipo_competencia', 'id' => 'tipo_competencia', 'class'=>'form-control')); ?>
                                <?php // echo montaSelect(array('2'=>'Envio Mensal','4'=>'Acerto Mensal'), NULL, array('name' => 'tipo_competencia', 'id' => 'tipo_competencia', 'class'=>'form-control')); ?>
                            </div>
                        </div>
                        
                        <div class="c_tipo_1 c_tipo_3 form-group" data-att="">
                            <label for="select" class="col-lg-2 control-label">Data: </label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="dataIni" id="competencia" readonly="true" placeholder="Data" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="c_tipo_2 c_tipo_4 form-group">
                            <label for="select" class="col-lg-2 control-label">Competência: </label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group">
                                    <?php echo montaSelect($meses, date('m'), array('name' => 'mes', 'id' => 'mes', 'class' => 'input form-control')); ?>
                                    <span class="input-group-addon de">de</span>
                                    <?php echo montaSelect($anos, date('Y'), array('name' => "ano", 'id' => 'ano', 'class' => 'input form-control')); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div id="msg_load" style="float: right;display: block;background: #FBD3B1;width: 233px;padding-left: 10px;" ></div>
                        
                        <!--p class="c_tipo_1 c_tipo_3"><label class="first">Competência:</label> <input type="text" name="data_inicial" id="competencia" class="date_f" value="<?= date('d/m/Y'); ?>"></p>
                        <p class="c_tipo_2 c_tipo_4"><label class="first">Mês:</label> </p>
                        <p class="c_tipo_2 c_tipo_4"><label class="first">Ano:</label> </p>
                        <p><label class="first" style="margin-left: 72px;"><input type="checkbox" id="acerto" />Acerto</label></p>
                        <p><label class="first" style="margin-left: 108px;"><input type="checkbox" id="reprocessar_totalizadores" />Reprocessar Totalizadores</label> <br> <small style="margin-left: 143px;">(Isto pode demorar o processamento)</small></p>-->
                        
                        <!--br class="clear"/>
                        <div id="msg_load" style="float: right;display: block;background: #FBD3B1;width: 233px;padding-left: 10px;" ></div>
                        <p class="controls" style="margin-top: 10px;">
                            <input type="button" value="Gerar" onclick="gerar_caged()" id="bt_gerar"/>
                        </p-->
                        
                    </div>
                    <div class="panel-footer text-right">
                        <button type="button" onclick="gerar_caged()" name="filtrar" id="bt_gerar" class="btn btn-primary filt_anual" ><i class="fa fa-save"></i> Gerar </button>
                    </div>
                </div>
            </form>
            <div id="resp"></div>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="....//js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            function gerar_caged(){
                $('#msg_load').html('<div id="din_load" style="font-style: italic;"></div>');
                window.setTimeout(function(){
                    $('#din_load').html('Ainda trabalhando por favor aguarde<span id="msg_carregando">...</span>');
                },5000);
                
                setInterval(function(){ 
                    if($('#msg_carregando').text().length>=3){ 
                        $('#msg_carregando').text('');
                    }
                    $('#msg_carregando').text($('#msg_carregando').text()+'.');
                            
                },2000 );
                
                
                master = $('#master').val();
                
                
                // TIPOS DE ENVIO //
                // 1 = Envio Diário
                // 2 = Envio Mensal
                // 3 = Acerto Diário
                // 4 = Acerto Mensal
                
                if($('#tipo_competencia').val()==1){
                    arr_data = $('#competencia').val().split('/');
                    dia = arr_data[0];
                    mes = arr_data[1];
                    ano = arr_data[2];
                    acerto = 'off';
                }else if($('#tipo_competencia').val()==2){
                    mes = $('#mes').val();
                    ano = $('#ano').val();
                    dia= 'false';
                    acerto = 'off';
                }else if($('#tipo_competencia').val()==3){
                    arr_data = $('#competencia').val().split('/');
                    dia = arr_data[0];
                    mes = arr_data[1];
                    ano = arr_data[2];
                    acerto = 'on';
                }else if($('#tipo_competencia').val()==4){
                    mes = $('#mes').val();
                    ano = $('#ano').val();
                    dia= 'false';
                    acerto = 'on';
                }
                
                $('#link_download').remove();
                $('.message-box').remove();
                $('#bt_gerar').attr('disabled','disabled');
                $('#bt_gerar').html('gerando arquivo... por favor aguarde...');
                
                var projeto = $('#projeto').val();                
                $.post('controlador.php',{master: master, mes: mes, ano: ano, dia: dia, acerto: acerto, projeto: projeto},function(data){
                    
                    $('#bt_gerar').removeAttr('disabled');
                    $('#bt_gerar').html('<i class="fa fa-save"></i> Gerar');
                    
                    var erros = '';
                    var erros_qnt = 0;
                    for (var key1 in data.erros) {
                            for (var key2 in data.erros[key1]) {
                                for (var key3 in data.erros[key1][key2]) {
                                    erros += '<p style="font-weight: normal">ERRO DE '+key1+' -> '+data.erros[key1][key2][key3]+'</p>';
                                    
                                }
                                erros_qnt++;
                            }
                    }
                    link = '?download='+data.name_file+'&name_file=CAGED.txt';
                    if(erros_qnt>0){
                        $('#resp').append('<a class="btn btn-success" id="link_download" href="'+link+'" title="Baixar Arquivo" ><i class="fa fa-download"></i> Baixar arquivo</a><br><br><div class="message-yellow message-box" ><div class="alert alert-danger"><h4> FORAM ENCONTRADOS '+erros_qnt+' ERROS.</h4>'+erros+'</div></div>');
                    }else{
                        $('#resp').append('<a class="btn btn-success" id="link_download" href="'+link+'" title="Baixar Arquivo" ><i class="fa fa-download"></i> Baixar arquivo</a>');
                    }
                    if(data.download){
                        window.location = link;
                    }else{
                        $('#link_download').remove();
                    }
                    $('#din_load').remove();
                },'json');
            }
            
            $(function(){
                $('#tipo_competencia').change(function(){
                    k = $(this).val();
                    $('div[class^=c_tipo_]').hide();
                    $('.c_tipo_'+k).show();
                });
                $('.c_tipo_1').hide();
                
            });
        </script>
    </body>
</html>
