<?php
include ("include/restricoes.php");

include ("../conn.php");
include ("../funcoes.php");
include ("../wfunction.php");
include ("../classes/global.php");

$usuario = carregaUsuario();

?>

<html>
    <head>
        <title>:: Intranet :: DESPESAS REALIZADAS</title>
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
        
        <script src="../js/global.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                $("#projeto").ajaxGetJson("../methods.php", {method: "carregaBancos"}, null, "banco");
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                <div class="fleft">
                    <h2>Financeiro - Cadastro de Notas Fiscais</h2>
                    <p>Cadastro de Notas Fiscais para alimentar o Financeiro</p>
                </div>
            </div>
            <form action="" method="post" name="form1" id="form1">
                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), null, "id='regiao' name='regiao' class='validate[custom[select]]'") ?></p> 
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), null, "id='projeto' name='projeto'") ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione o Projeto »"), null, "id='banco' name='banco'") ?></p>
                    

                    <p class="controls">
                        <input type="submit" class="button" value="Filtrar" name="filtrar" />
                    </p>
                </fieldset>

            </form>
        </div>
    </body>
</html>