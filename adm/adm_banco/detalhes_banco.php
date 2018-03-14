<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/BancoClass.php');

$banco_fin = $_REQUEST['banco'];

$usuario = carregaUsuario();
$row = getBancoID($banco_fin);

//trata local
$local_banco = $row['interior'];
if($local_banco == 1){
    $loc = 'INTERNO';
}else{
    $loc = 'EXTERNO';
}

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();
?>
<html>
    <head>
        <title>:: Intranet :: Bancos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="bancos.css" rel="stylesheet" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#editarBanco").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#banco").val(key);
                        $("#form1").attr('action','form_banco.php');
                        $("#form1").submit();
                    }
                });                                
            });
        </script>
        
        <style>
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 1000px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Banco <?php echo $row['nome']; ?></h2>
                        <img class="img_banco" src='../../imagens/bancos/<?php echo $row['id_nacional']; ?>.jpg' />
                    </div>
                </div>
                
                <input type="hidden" id="banco" name="banco" value="" />
                <fieldset>
                    <legend>Dados do Banco</legend>
                    <div class="colunaEsq">
                        <p><label class='first'>Regiao:</label> <?php echo $row['id_regiao'] . " - " . $row['nome_regiao']; ?></p>
                        <p><label class='first'>Projeto:</label> <?php echo $row['id_projeto'] . " - " . $row['nome_projeto']; ?></p>
                        <p><label class='first'>Nome:</label> <?php echo $row['nome']; ?></p>
                        <p><label class='first'>Local:</label> <?php echo $loc; ?></p>
                        <p><label class='first'>Banco:</label> <?php echo $row['razao']; ?></p>
                        <p><label class='first'>Localidade:</label> <?php echo $row['localidade']; ?></p>                                                
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>Endereço:</label> <?php echo $row['endereco']; ?></p>
                        <p><label class='first'>Conta Corrente:</label> <?php echo $row['conta']; ?></p>
                        <p><label class='first'>Agência:</label> <?php echo $row['agencia'];; ?></p>
                        <p><label class='first'>Gerente:</label> <?php echo $row['gerente']; ?></p>                        
                        <p><label class='first'>Telefone:</label> <?php echo $row['tel']; ?></p>                                              
                    </div>
                </fieldset>
                
                <p class="controls">
                    <input type="submit" class="button bt-image" value="Editar" name="editarBanco" id="editarBanco" data-type="editar" data-key="<?php echo $row['id_banco']; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
            </form>
        </div>
    </body>
</html>