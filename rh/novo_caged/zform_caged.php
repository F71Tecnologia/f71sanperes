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

include "../../conn.php";
include "../../wfunction.php";
include('../../classes/global.php');

$meses = mesesArray();
$anos = anosArray();

$usuario = carregaUsuario();



$sql = "SELECT nome FROM `master` WHERE id_master = {$usuario['id_master']} AND status=1";
$result = mysql_query($sql);
$master = mysql_fetch_array($result);
//while($resp = mysql_fetch_array($result)){
//    $array_master[$resp['id_master']] = $resp['razao'];
//}

//$sql = "SELECT * FROM `master` WHERE status=1";
//$result = mysql_query($sql);
//$array_master = array();
//while($resp = mysql_fetch_array($result)){
//    $array_master[$resp['id_master']] = $resp['razao'];
//}

//
//if(isset($_REQUEST['method']) && $_REQUEST['method']=='buscaProjeto'){
//    $idMaster = $_REQUEST['master'];
//    $array_projetos = GlobalClass::carregaProjetos($idMaster);    
//    echo utf8_encode(json_encode($array_projetos));
//    exit();
//
//}
?>
<html>
    <head>
        <title>:: Intranet :: CAGED</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
         <link href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
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
                $('#bt_gerar').val('gerando arquivo... por favor aguarde...');
                
                var projeto = $('#projeto').val();                
                $.post('zcontrolador.php',{master: master, mes: mes, ano: ano, dia: dia, acerto: acerto, projeto: projeto},function(data){
                    
                    
                    $('#bt_gerar').removeAttr('disabled');
                    $('#bt_gerar').val('Gerar');
                    
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
                        $('#form').append('<a id="link_download" href="'+link+'" title="Baixar Arquivo" ><h4 style="display: inline;"><br><br>Baixar arquivo<br><br></h4></a><div class="message-yellow message-box" ><h4> FORAM ENCONTRADOS '+erros_qnt+' ERROS.</h4>'+erros+'</div>');
                    }else{
                        $('#form').append('<a id="link_download" href="'+link+'" title="Baixar Arquivo" ><h4 style="display: inline;"><br><br>Baixar arquivo<br><br></h4></a>');
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
                    $('p[class^=c_tipo_]').hide();
                    $('.c_tipo_'+k).show();
                });
                $('.c_tipo_2').hide();
                
               
            });
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>CAGED</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>
                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <p><label class="first">Master:</label><label><?php echo $master['nome']; ?></label><input type="hidden" id="master" name="master" value="<?=$usuario['id_master'];?>"></p>
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetos($usuario['id_master']), $usuario['id_projeto'], array('name' => "projeto", 'id' => 'projeto')) ;   ?></p>
                        <p><label class="first">Tipo:</label> <?php echo montaSelect(array('1'=>'Envio Diario','2'=>'Envio Mensal','3'=>'Acerto Diario','4'=>'Acerto Mensal'), NULL, array('name' => 'tipo_competencia', 'id' => 'tipo_competencia')); ?></p>
                        <p class="c_tipo_1 c_tipo_3"><label class="first">Competência:</label> <input type="text" name="data_inicial" id="competencia" class="date_f" value="<?= date('d/m/Y'); ?>"></p>
                        <p class="c_tipo_2 c_tipo_4"><label class="first">Mês:</label> <?php echo montaSelect($meses, date('m'), array('name' => 'mes', 'id' => 'mes')); ?></p>
                        <p class="c_tipo_2 c_tipo_4"><label class="first">Ano:</label> <?php echo montaSelect($anos, date('Y'), array('name' => "ano", 'id' => 'ano')); ?></p>
<!--                        <p><label class="first" style="margin-left: 72px;"><input type="checkbox" id="acerto" />Acerto</label></p>
                        <p><label class="first" style="margin-left: 108px;"><input type="checkbox" id="reprocessar_totalizadores" />Reprocessar Totalizadores</label> <br> <small style="margin-left: 143px;">(Isto pode demorar o processamento)</small></p>-->
                    </div>
                    <br class="clear"/>
                    <div id="msg_load" style="float: right;display: block;background: #FBD3B1;width: 233px;padding-left: 10px;" ></div>
                    <p class="controls" style="margin-top: 10px;">
                        <input type="button" value="Gerar" onclick="gerar_caged()" id="bt_gerar"/>
                    </p>
                </fieldset>
            </form>
        </div>
    </body>
</html>