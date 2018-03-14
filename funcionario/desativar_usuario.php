<?php
include('../conn.php');

$funcionario = $_REQUEST['funcionario'];

mysql_query("UPDATE funcionario SET status_reg = '0' WHERE id_funcionario = '$funcionario'") or die ("Tela 25 <br> $mesnagem_erro<br><br>" . mysql_error());

?>
<html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    </head>
    <body>
        <p>Usuário desativado com sucesso!</p>
        <p>Para ativá-lo novamente entre em contato com o administrador do sistema.</p>
    </body>
</html>
        