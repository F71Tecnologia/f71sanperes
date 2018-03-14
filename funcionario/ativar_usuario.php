<?php
include('../conn.php');

$funcionario = $_REQUEST['funcionario'];

mysql_query("UPDATE funcionario SET status_reg = '1' WHERE id_funcionario = '$funcionario'");

?>
<html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    </head>
    <body>
        <p>Usuário Ativado com sucesso!</p>
    </body>
</html>
        