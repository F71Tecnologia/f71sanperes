<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');

$usuarioW = carregaUsuario();

//INICIO DO CONTROLADOR
if(validate($_REQUEST['buscarsaida'])){
   
}

$qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$usuarioW['id_master']}";
$reMaster = mysql_query($qrMaster);
$roMaster = mysql_fetch_assoc($reMaster);

$meses = mesesArray(null);
$anos = anosArray(null, null);
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$bid_saidaR = (isset($_REQUEST['id_saida'])) ? $_REQUEST['id_saida'] : "";


?>
<html>
    <head>
        <title>:: Intranet :: ADMINISTRAÇÃO FINANCEIRA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/jquery.maskMOney_2.1.2.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {

                $("div[id!=item1]", ".colDir").hide();

                $(".bt-menu").click(function() {
                    var $bt = $(this);
                    var id = '#item' + $bt.attr("data-item");
                    $("div[id^=item]").hide();

                    $(id).show();
//                    console.log($(id));
                    $(".bt-menu").removeClass("aselected");
                    $bt.addClass("aselected");
                });
                
                $("#buscarsaida").click(function () {
                    var action = $(this).data("type");
                    var key = $("#item2 input:first").val();

                    if (action === "carregaSaida") {   
                        $.getJSON("actions.php", {method: "carregaSaida", idSaida: key}, function(result){                           
                      //      $("#item2").empty();
                            for(i = 0; i<result.length; i++){
                                var objeto = result[i];
                                alert('Id_saida: ' + objeto.id_saida);
                            }
                        });     
                    }
                });
                    
                
                
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <form method="post" name="form" id="form" action="">
            <div id="content">

                <div id="geral">
                    <div id="topo">
                        <div class="conteudoTopo">
                            <div class="imgTopo">
                                <img src="../imagens/logomaster<?php echo $usuarioW['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                            </div>
                            <h2><?php echo $roMaster['nome'] ?></h2>
                            <h3>ADMINISTRAÇÃO FINANCEIRA</h3>
                        </div> 
                    </div>
                    <div id="conteudo">
                        <div class="colEsq">
                            <div class="titleEsq">Ações</div>
                            <ul>
                                <li><a href="javascript:;" data-item="1" class="bt-menu aselected">Editar Saída</a></li>
                                <li><a href="javascript:;" data-item="2" class="bt-menu">Excluir Saída</a></li>
                                <li><a href="javascript:;" data-item="3" class="bt-menu">Editar Entrada</a></li>
                                <li><a href="javascript:;" data-item="4" class="bt-menu">Excluir Entrada</a></li>
                            </ul>
                        </div>
                        <div class="colDir">
                            <div id="item1">
                                <fieldset>
                                    <legend>Editar saída</legend>
                                    <p><label class="first">id_saida:</label> <input type="text" name="id_saida" id="id_saida" value="<?php echo $bid_saidaR ?>" size="8" class="textbox" /></p>
                                    <p class="controls"> <input type="submit" class="button" value="Buscar" name="buscarsaida" /></p>
                                </fieldset>
                            </div>
                            <div id="item2">
                                <h3>Excluir Saída</h3>
                                <fieldset>
                                    <legend>Buscar saída</legend>
                                    <p><label class="first">Número da saida:</label> <input type="text" name="id_saida" id="id_saida" value="<?php echo $bid_saidaR; ?>" size="8" class="textbox" /></p>
                                    <p class="controls"> <input type="button" name="buscarsaida" id ="buscarsaida" class="button" value="Buscar" data-type="carregaSaida" /></p>
                                </fieldset>                              
                            </div>
                            <div id="item3">
                                <h3>Desabilitar Empresa que não tem Contrato 333333333</h3>
                                <p><label>Tipo:</label> </p>
                            </div>
                            <div id="item4">
                                <h3>Recursos Humanos44444444444</h3>
                                <p><label>Tipo:</label>  </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>