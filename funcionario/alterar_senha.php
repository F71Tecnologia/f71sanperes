<?php
include('../conn.php');

$funcionario = $_REQUEST['funcionario'];

// GERANDO A SENHA ALEAT�RIA
$target = "%%%%";
$senha = "";
//$dig = "";
$consoantes = "bcdfghjkmnpqrstvwxyzbcdfghjkmnpqrstvwxyz";
$vogais = "aeiu";
$numeros = "123456789";
$a = strlen($consoantes) - 1;
$b = strlen($vogais) - 1;
$c = strlen($numeros) - 1;
for ($x = 0; $x <= strlen($target) - 1; $x++) {
  if (substr($target, $x, 1) == "@") {
    $rand = mt_rand(0, $c);
    $senha .= substr($numeros, $rand, 1);
  } elseif (substr($target, $x, 1) == "%") {
    $rand = mt_rand(0, $a);
    $senha .= substr($consoantes, $rand, 1);
  } elseif (substr($target, $x, 1) == "&") {
    $rand = mt_rand(0, $b);
    $senha .= substr($vogais, $rand, 1);
  } else {
    die("<b>Erro!</b><br><i>$target</i> � uma express�o inv�lida!<br><i>" . substr($target, $x, 1) . "</i> � um caract�r inv�lido.<br>");
  }
}
$senha = "net" . $senha;
// FIM

  mysql_query("UPDATE funcionario SET senha = '$senha', alt_senha = '1' WHERE id_funcionario = '$funcionario'") or die("Tela 25 <br> $mesnagem_erro<br><br>" . mysql_error());

?>
<html>
    <head>
         <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    </head>
    <body>
        <p>Senha alterada com sucesso, n�o esque�a de anotar e enviar para o usu�rio.</p>
        <p>Nova senha: <?php echo "<b>$senha</b>";?></p>
        
    </body>
</html>