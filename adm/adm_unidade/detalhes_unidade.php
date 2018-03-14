<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/UnidadeClass.php');

$unidade = $_REQUEST['unidade'];

$usuario = carregaUsuario();
$row = getUnidadeID($unidade);

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
        <title>:: Intranet :: Unidades</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="unidades.css" rel="stylesheet" type="text/css" /> 
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#editarUnidade").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#unidade").val(key);
                        $("#form1").attr('action','form_unidade.php');
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
                        <h2>Unidade <?php echo $row['unidade']; ?></h2>
                    </div>
                </div>
                           
                <input type="hidden" id="unidade" name="unidade" value="" />
                <fieldset>
                    <legend>Dados da Unidade</legend>
                    <div class="colunaEsq">
                        <p><label class='first'>Regiao:</label> <?php echo $row['nome_regiao']; ?></p>
                        <p><label class='first'>Projeto:</label> <?php echo $row['nome_projeto']; ?></p>
                        <p><label class='first'>Nome:</label> <?php echo $row['unidade']; ?></p>
                        <p><label class='first'>Local:</label> <?php echo $row['local']; ?></p>
                        <p><label class='first'>Endereço:</label> <?php echo $row['endereco']; ?></p>
                        <p><label class='first'>Bairro:</label> <?php echo $row['bairro']; ?></p>
                        <p><label class='first'>Cidade:</label> <?php echo $row['cidade']; ?></p>
                        <p><label class='first'>UF:</label> <?php echo $row['uf']; ?></p>
                        <p><label class='first'>Ponto de referência:</label> <?php echo $row['ponto_referencia']; ?></p>                        
                        <p><label class='first'>CEP:</label> <?php echo $row['cep']; ?></p>
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>Telefone:</label> <?php echo $row['tel']; ?></p>
                        <p><label class='first'>Telefone Recado:</label> <?php echo $row['tel2'];; ?></p>
                        <p><label class='first'>Responsável:</label> <?php echo $row['responsavel']; ?></p>                        
                        <p><label class='first'>Celular do Responsável:</label> <?php echo $row['cel']; ?></p>                      
                        <p><label class='first'>Email do Responsável:</label> <?php echo $row['email']; ?></p>                        
                    </div>
                </fieldset>
                
                <p class="controls">
                    <input type="submit" class="button bt-image" value="Editar" name="editarUnidade" id="editarUnidade" data-type="editar" data-key="<?php echo $row['id_unidade']; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
            </form>
        </div>
    </body>
</html>