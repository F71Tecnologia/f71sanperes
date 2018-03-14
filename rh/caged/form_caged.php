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

$meses = mesesArray();
$anos = anosArray();

$usuario = carregaUsuario();

$sql = "SELECT * FROM `master` WHERE status=1";
$result = mysql_query($sql);
$array_master = array();
while($resp = mysql_fetch_array($result)){
    $array_master[$resp['id_master']] = $resp['razao'];
}

?>
<html>
    <head>
        <title>:: Intranet :: CAGED</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
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
                mes = $('#mes').val();
                ano = $('#ano').val();
                if($('#acerto').is(':checked')){
                    acerto = 'on';
                }else{
                    acerto = 'off';
                }
                $('#link_download').remove();
                $('.message-box').remove();
                $('#bt_gerar').attr('disabled','disabled');
                $('#bt_gerar').val('gerando arquivo... por favor aguarde...');
                $.post('controlador.php',{master: master, mes: mes, ano: ano, acerto: acerto},function(data){
                    
                    
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
                        $('#form').append('<a id="link_download" href="'+link+'" title="Baixar Arquivo" ><h4>Baixar arquivo</h4></a><div class="message-yellow message-box" ><h4> FORAM ENCONTRADOS '+erros_qnt+' ERROS.</h4>'+erros+'</div>');
                    }else{
                        $('#form').append('<a id="link_download" href="'+link+'" title="Baixar Arquivo" ><h4>Baixar arquivo</h4></a>');
                    }
                    window.location = link;
                    $('#din_load').remove();
                },'json');
            }
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
                        <p><label class="first">Master:</label> <?php echo montaSelect($array_master, $usuario['id_master'], array('name' => "master", 'id' => 'master')); ?> </p>
                        <p><label class="first">Mês:</label> <?php echo montaSelect($meses, date('m'), array('name' => 'mes', 'id' => 'mes')); ?></p>
                        <p><label class="first">Ano:</label> <?php echo montaSelect($anos, date('Y'), array('name' => "ano", 'id' => 'ano')); ?></p>
                        <p><label class="first" style="margin-left: 72px;"><input type="checkbox" id="acerto" />Acerto</label></p>
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