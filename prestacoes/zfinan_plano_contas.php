<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include '../conn.php';
include '../wfunction.php';



if(isset($_REQUEST['methods']) && $_REQUEST['methods'] == 'Cadastrados'){
    $qrAssoc = "SELECT id_plano_contas,id_entradasaida FROM entradaesaida_plano_contas_assoc;";
    $result = mysql_query($qrAssoc);
    $row_cnt = mysql_num_rows($result);
    $array = array();
    while ($row = mysql_fetch_assoc($result)) {
        $array[] = $row['id_plano_contas'] .'-'. $row['id_entradasaida'];
    }
    
    echo json_encode($array);
    exit();
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'cadastrar'){
    $idES = $_REQUEST['idES'];
    $idPC = $_REQUEST['idPC'];

    if(!empty($idES) && $idES != 0 && !empty($idPC) && $idPC != 0 ){
        $qr = "SELECT * FROM entradaesaida_plano_contas_assoc WHERE id_plano_contas = $idPC AND id_entradasaida =  $idES;";
        $nr = mysql_num_rows(mysql_query($qr));
        if($nr==0){
            $qr = "INSERT INTO entradaesaida_plano_contas_assoc (id_plano_contas, id_entradasaida) VALUES ($idPC,$idES);";
            mysql_query($qr) or die ("ERRO AO GRAVAR NA TABELA entradaesaida_plano_contas_assoc");
        }
    }
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'editar'){
    $idES = $_REQUEST['idES'];
    $idPC = $_REQUEST['idPC'];
       
    if(!empty($idES) && $idES != 0 && !empty($idPC) && $idPC != 0 ){
        $qr = "SELECT * FROM entradaesaida_plano_contas_assoc WHERE id_plano_contas = $idPC;";
        $nr = mysql_num_rows(mysql_query($qr));
        if($nr > 0){
             $qr = "UPDATE entradaesaida_plano_contas_assoc SET id_entradasaida= $idES WHERE id_plano_contas = $idPC LIMIT 1;";
             mysql_query($qr) or die ("ERRO AO ALTERAR A TABELA entradaesaida_plano_contas_assoc");
        }
    }
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'remover'){
    $idES = $_REQUEST['idES'];
    $idPC = $_REQUEST['idPC'];
       
    if(empty($idES) && !empty($idPC) && $idPC != 0 ){
        $qr = "SELECT * FROM entradaesaida_plano_contas_assoc WHERE id_plano_contas = $idPC;";
        $nr = mysql_num_rows(mysql_query($qr));
        if($nr > 0){
             $qr = "DELETE FROM entradaesaida_plano_contas_assoc WHERE  id_plano_contas = $idPC LIMIT 1;";
             mysql_query($qr) or die ("ERRO AO DELETAR NA TABELA entradaesaida_plano_contas_assoc");
        }
    }
    exit;
}


$usuario = carregaUsuario();
$master = $usuario['id_master'];

$qrPlanoContas = "SELECT * FROM plano_de_contas;";
$qrEntradaSaida = "SELECT id_entradasaida, cod, nome FROM entradaesaida WHERE grupo >= 10;";

$palnoContas = mysql_query($qrPlanoContas);
$entradaSaida = mysql_query($qrEntradaSaida);


?>
<html>
    <head>
        <title>:: Intranet :: FINANCEIRO - RECURSOS HUMANOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <!--<script src="../js/global.js" type="text/javascript"></script>-->
        <script>
            $(function(){
                $.post("zfinan_plano_contas.php",{methods: "Cadastrados"}, function(data) {
                   if(data){
                        $.each(data,function( index, value ) {
                            $("#assoc"+value.split("-")[0]).val(value.split("-")[1]);
                        });
                   }
                 }, 'json');
                
                
                var idEsVelho;
                $(".eS").focusin(function (){
                    idEsVelho =  $(this).val();
                });
                
                $(".eS").focusout(function (){
                    var idPc = $(this).data("type");
                    var idEs = $(this).val();
                    if(idEsVelho == ''){
                        $.post( "zfinan_plano_contas.php",{method: "cadastrar", idES: idEs, idPC: idPc});
                    }else if(idEsVelho != '' && idEs != '' && idEsVelho != idEs){
                        $.post( "zfinan_plano_contas.php",{method: "editar", idES: idEs, idPC: idPc});
                    }else if(idEsVelho != '' && idEs == ''){
                        $.post( "zfinan_plano_contas.php",{method: "remover", idES: idEs, idPC: idPc});
                    } 
                });
                
                $("#relatorio").click(function (){
                    window.location.href = "zfinan_plano_contas_rel.php";
                });
                 
            });
        </script>
        <style>
            .dir{
                    width: 45%;
                    max-height: 1600px;
                    margin-left: 975px;
                    overflow: auto;
                }
            .esq{
                    margin: 0 25px 0 0;
                    width: 45%;
                    max-height: 1600px;
                    float: left;
                    text-align: right;
                    overflow: auto;
                }
        </style>
    </head>
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;" />
                    <div class="fleft">
                        <h2>Relacione</h2>
                    </div>
                </div>
                <br class="clear"/>
                <div class="esq">
                    <table  border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Nome</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            while ($rowES = mysql_fetch_assoc($entradaSaida)) {?>
                                <tr class="secao">
                                    <td class="secao"><?php echo $rowES['id_entradasaida']?></td>
                                    <td class="secao"><?php echo $rowES['cod']?></td>
                                    <td class="secao"><?php echo strtoupper($rowES['nome']);?></td>
                                </tr>
                        <?php }
                        ?>
                        </tbody>
                    </table> 
                </div>
                <div class="dir">                    
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" hidth="3027px" class="grid">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nome</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($rowPC = mysql_fetch_assoc($palnoContas)) {?>
                            <tr class="secao">
                                <td class="secao"><?php echo $rowPC['classificador']?></td>
                                <td class="secao"><?php echo strtoupper($rowPC['nome']);?></td>
                                <td><input type="text" size="5" id="assoc<?php echo $rowPC['id_plano_contas']?>" data-type = "<?php echo $rowPC['id_plano_contas']?>" class ="eS" name="idES[]"/></td>
                            </tr>
                    <?php } ?>
                        </tbody>
                    </table>       
                </div>
                <br class="clear"/>
                <p class="controls"> 
                    <input type="button" class="button" value="Ver Relarório" name="relatorio" id="relatorio" />
                </p>
            </form>
        </div>  
    </body>
</html>