<?php
setcookie("logado", "", time() - 3600);
include "conn.php";

if (empty($_REQUEST['login'])) {

    if ($_REQUEST['b']) {
        $msg = "<span style='color:#F00; font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12px'>Login ou senha incorretos</span>";
    } elseif ($_REQUEST['entre']) {
        $msg = "<span style='color:#F00; font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12px'></span>";
    } elseif ($_REQUEST['logout']) {
        $msg = "<span style='color:#F00; font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12px'>Você acabou de sair!</span>";
    }

    print "

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>::: Intranet :::</title>
<style type='text/css'>
body {
	background:url('imagens/fundologin.gif');
}
</style>
</head>
<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
";
    ?>

    <table width="800" height="569" border="1" bordercolor="#CCCCCC" align="center" cellpadding="0" cellspacing="0" background="imagens/abertura.jpg">
        <tr>
            <td>
                <form action="login.php" method="post" name="form1">
                    <table width="360" border="0" align="right" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="19" valign="bottom">&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="28" valign="bottom">&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="44" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12px">Sistema de Gerenciamento</span></td>
                        </tr>
                        <tr>
                            <td height="25" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#003366" face="Verdana, Geneva, sans-serif" size="1"><strong>&nbsp;LOGIN</strong></font></td>
                        </tr>
                        <tr>
                            <td><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input name="login" type="text" id="login" size="20"  tabindex="1" 
                                           onfocus="this.style.background='#aeaeae'"  
                                           onblur="this.style.background='#cccccc'"  
                                           style='background:#aeaeae;'/>
                                </span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#003366" face="Verdana, Geneva, sans-serif" size="1"><strong>&nbsp;</strong></font><font color="#003366" face="Verdana, Geneva, sans-serif" size="1"><strong>SENHA</strong></font></td>
                        </tr>
                        <tr>
                            <td><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input name="senha" type="password" id="senha" size="21" tabindex="2" 
                                           onfocus="this.style.background='#aeaeae'"  
                                           onblur="this.style.background='#cccccc'"  
                                           style='background:#aeaeae;'/>
                                </span></td>
                        </tr>
                        <tr>
                            <td><span class="linha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $msg ?>
                                </span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                <input type="submit" name="enviar" id="enviar" value="Acessar" tabindex="3" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td><input name='id' type='hidden' id='id' value='1' /></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>

<?php
} else {

    $id = $_REQUEST['id'];

// CASO NÃO ESTEJA FAZENDO O UPDATE PARA A NOVA SENHA .. 
// VAI VERIFICAR SE A SENHA ESTÁ CORRETA OU SE VAI MUDAR A SENHA OU SE A SENHA ESTÁ ERRADA
    switch ($id) {
        case 1:

//------------------------------------------EFETUANDO A PESQUISA PARA LOGAR

            $login = $_REQUEST['login'];
            $senha = $_REQUEST['senha'];

            $qr_dias = mysql_query("SELECT acesso_dias FROM funcionario WHERE login = '$login' AND status_reg = '1'");
            $numLinhasQrDias = mysql_num_rows($qr_dias);
            
            if($numLinhasQrDias == 0){
                echo "Login ou senha incorretos!";
                exit();
            }
            
            if (@mysql_result($qr_dias, 0) != 7) {

                $hora_atual = date('H:i:s');
                $dias_semana = array('1', '2', '3', '4', '5');

                $qr_horario = mysql_query("SELECT * FROM funcionario WHERE login = '$login' AND horario_inicio <= '$hora_atual' AND horario_fim >= '$hora_atual'");
                $verifica_horario = mysql_num_rows($qr_horario);

                if (!in_array(date('w'), $dias_semana) or empty($verifica_horario)) {
                    echo 'Seu IP foi gravado!<br>Voc� n�o possui autoriza��o para acessos fora de seu hor�rio de trabalho.';
                    exit();
                }
            }

            $result = mysql_query("SELECT * FROM funcionario WHERE login = '$login' AND status_reg = '1' AND alt_senha = '1'");
            $result2 = mysql_query("SELECT * FROM funcionario where login = '$login' and senha = '$senha' and status_reg = '1' and alt_senha = '0'");

            $rowNum = mysql_num_rows($result);
            $rowNum2 = mysql_num_rows($result2);

            $row = mysql_fetch_array($result);
            $row2 = mysql_fetch_array($result2);

            //VERIFICANDO
            // SE O FUNCIONÁRIO ESTIVER MARCADO PARA ALTERAR A SENHA
            if ($rowNum != 0) {

                // $senhaAntiga = $row['senha'];
                // TELA DE ALTERAR A SENHA

                print "

                    <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml'>
                    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                    <title>::: Intranet :::</title>
                    <style type='text/css'>
                    <!--
                    body {
                            background:url(imagens/fundologin.gif)
                    }
                    -->
                    </style>
                    </head>

                    <body bgcolor='#459393' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>

                    ";
                ?>
                <table width="800" height="569" border="1" bordercolor="#CCCCCC" align="center" cellpadding="0" cellspacing="0" background="imagens/abertura.jpg">
                    <tr>
                        <td>
                            <div align="center" style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:14px; background-color:#FFF">ALTERANDO SENHA</div>
                            <div align="center" style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#F00; background-color:#FFF">POR MEDIDA DE SEGURANÇA SERÁ NECESSÁRIO TROCAR SUA SENHA A CADA 3 MESES</div>
                            <p>&nbsp;</p>
                            <form action="login.php" method="post" name="form1" id="form1">
                                <table width="360" border="0" align="right" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td height="19" valign="bottom">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="33" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12px">Sistema de Gerenciamento</span></td>
                                    </tr>
                                    <tr>
                                        <td height="20" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red" face="Verdana, Geneva, sans-serif" size="1"><strong>&nbsp;SENHA ANTIGA</strong></font></td>
                                    </tr>
                                    <tr>
                                        <td><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input name="senha_antiga" type="password" id="senha_antiga" size="20" style="background-color:#FFA6A8"/>
                                                <input name="login" type="hidden" id="login" value="<?= $row['login'] ?>" />
                                                <input name="iduser" type="hidden" id="iduser" value="<?= $row['0'] ?>" />

                                            </span></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="blue" face="Verdana, Geneva, sans-serif" size="1"><strong>&nbsp;NOVA SENHA</strong></font></td>
                                    </tr>
                                    <tr>
                                        <td><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input name="senha_nova" type="password" id="senha_nova" size="21"  style="background-color:#C0ACFB"/>
                                            </span></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php
                            if ($_GET['entre']) {
                                print "<script>location.href = 'login.php?entre=true';</script>";
                            }
                            ?>
                                            <?= $msg ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="hidden" name="id" id="id" value="2" />
                                            <input type="submit" name="enviar2" id="enviar2" value="Acessar" /></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </form></td>
                    </tr>
                </table>
                <BR />

                <?php
            } elseif ($rowNum2 != 0) { //FAZENDO LOGIN POIS JA FOI VERIFICADO E ESTÁ TUDO CORRETO
                $result = $row2['0'];

                setcookie("logado", $row2['0'], 0);
                


                //----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

                $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE login = '$_REQUEST[login]'");
                $funcionario = mysql_fetch_array($qr_funcionario);
                
                //GRAVANDO SESS�O
                $_SESSION['id_regiao'] = $funcionario['id_regiao'];
                $_SESSION['id_master'] = $funcionario['id_master'];
                $_SESSION['id_user'] = $funcionario['id_funcionario'];
                //GRAVANDO SESS�O
                
                $ip = $_SERVER['REMOTE_ADDR'];
                $data = date("d/m/Y H:i");
                $cabecalho = "($funcionario[0]) $funcionario[nome] às " . $data . "h (ip: $ip)";
                $local = "Login Principal";
                $local_banco = "Login Principal";
                $acao_banco = "Efetuando o Login na Intranet";

                mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
                VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die("Erro Inesperado<br><br>" . mysql_error());

                $arquivo = fopen("log/" . $funcionario[0] . ".txt", "a");
                fwrite($arquivo, "$cabecalho");
                fwrite($arquivo, "\r\n");
                fwrite($arquivo, "$local");
                fwrite($arquivo, "\r\n");
                fwrite($arquivo, "$acao_banco");
                fwrite($arquivo, "\r\n");
                fwrite($arquivo, "\r\n");
                fwrite($arquivo, "---------------------------------------------------------------");
                fwrite($arquivo, "\r\n");
                fwrite($arquivo, "\r\n");
                fclose($arquivo);

                //----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

                if ($_COOKIE['logado'] == 87) {

                    echo 'aqui';
                }




                print "<script> location.href = 'index.php'; </script>";
            } elseif ($rowNum == 0 and $rowNum2 == 0) {

                //-------------------------------- A SENHA ESTÁ INCORRETA

                print "<script> location.href = 'login.php?b=erro'; </script>";
            }// FINALIZANDO A PESQUISA SE A SENHA ESTA CORRETA SE ESTÁ MUDANDO A SENHA OU  SE A SENHA ESTÁ INCORRETA





            break;

        case 2:   // FAZENDO O UPDATE PARA A NOVA SENHA

            $iduser = $_REQUEST['iduser'];
            $login = $_REQUEST['login'];
            $senha_antiga = $_REQUEST['senha_antiga'];
            $senha_nova = $_REQUEST['senha_nova'];

           
            
            //VERIFICANDO SE A SENHA ANTIGA DIGITADA ESTÁ CORRETA
            $result = mysql_query("SELECT * FROM funcionario where login = '$login' and senha = '$senha_antiga' and status_reg = '1' and alt_senha = '1'");
            $Numrow = mysql_num_rows($result);

            
            if ($Numrow == 0) {

                print "

		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title>::: Intranet :::</title>
		<style type='text/css'>
		<!--
		body {
			background-color: #060;
		}
		-->
		</style></head>

		<body bgcolor='#459393' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
		";

                print "<script>
		alert (\"Sua senha antiga está incorreta!\"); 
		location.href = 'login.php?b=error';
		</script>
		";
                exit;
            }

            if ($senha_antiga == $senha_nova) {  // NOVA SENHA NÃO PODE SER IGUAL A SENHA ANTIGA
                print "

		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<title>::: Intranet :::</title>
		<style type='text/css'>
		<!--
		body {
			background-color: #060;
		}
		-->
		</style></head>

		<body bgcolor='#459393' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
		";

                print "<script>
		alert (\"A senha nova NÃO pode ser IGUAL a senha antiga!\"); 
		location.href = 'login.php?b=error';
		</script>
		";
            } else {
                // AGORA SIM.. TUDO CERTO.. JOGANDO A NOVA SENHA DENTRO DA TABELA FUNCIONARIO E FAZENDO LOGIN
                //FAZENDO UPDATES
                mysql_query("UPDATE funcionario SET senha = '$senha_nova', alt_senha = '0' WHERE id_funcionario = '$iduser'") or die("Erro " . mysql_error());

                setcookie("logado", $iduser);

                //----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
                $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[0]'");
                $row_user = mysql_fetch_array($result_user);

                $ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
                $local = "LOGIN PRINCIPAL";
                $horario = date('Y-m-d H:i:s');
                $acao = "EFETUANDO LOGIN NA INTRANET";

                mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao) 
	VALUES ('$row[0]','$row_user[id_regiao]','$row_user[tipo_usuario]',
	'$row_user[grupo_usuario]','$local','$horario','$ip','$acao')") or die("Erro Inesperado<br><br>" . mysql_error());

                //----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
                //REDIRECIONANDO PARA FAZER LOGIN NOVAMENTE
                print "<script>
		alert (\"Senha alterada com sucesso!\"); 
		location.href = 'login.php';
		</script>
		";
                //REDIRECIONANDO	
                //print "<script> location.href = 'index.php'; </script>";
            }


            break;
    } //FIM DO UPDATE PARA A NOVA SENHA (CASE)
} //FIM DO ARQUIVO
?>


</body>
</html>
