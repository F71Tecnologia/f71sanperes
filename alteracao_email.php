<?php
include('conn.php');
include "funcoes.php";
ini_set('display_errors', '0');

$qr_funcionario = mysql_query("SELECT * FROM funcionario  WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_funcionario);

$msg = $_GET['msg'];
$menssagem = array(1 => '** Para que o novo sistema de e-mail funcione perfeitamente,<br> é necessário cadastrar seu e-mail na Intranet.');


if (isset($_POST['enviar'])) {
    /*
    $email = $_POST['email'] . '@sorrindo.org';
    $senha = $_POST['senha'];
    $servidor = '{mail.sorrindo.org:143/novalidate-cert}INBOX';
    $mbox = imap_open($servidor, $email, $senha);
    $erro = imap_last_error();

    if (empty($erro)) {

        mysql_query("UPDATE funcionario SET email_login = '$email', email_senha = '$senha' WHERE id_funcionario = '$_COOKIE[logado]' ");

        /////ENVIA AS TAREFAS PARA O E-MAIL DO USUÁRIO
        $apelido_usuario = $row_func['nome1'];
        $qr_tarefas = mysql_query("SELECT *, date_format(data_entrega, '%d/%m/%Y')as data_entrega FROM tarefa where usuario = '$apelido_usuario' and tipo_tarefa < '5' and status_reg = '1' AND tarefa_todos = '0' ORDER BY id_tarefa DESC") or die(mysql_error());
        while ($row_tarefa = mysql_fetch_assoc($qr_tarefas)):

            $destino = $email;
            $assunto = $row_tarefa['tarefa'];
            $menssagem = strip_tags($row_tarefa['descricao']);
            $headers = 'From: INTRANET';
            mail($destino, $assunto, $menssagem, $headers);

        endwhile;
        ////////////////////////////////////////////
        header('Location: index.php');
    } else {
        $menssagem[3] = 'E-mail e/ou senha inválido(s)';
    }*/
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>::Intranet::</title>
    </head>

    <body>
        <form name="form" action="alteracao_email.php?msg=3" method="post">
            <table align="center" style="background-color:#EEE">
                <tr>
                    <td  align="center" colspan="2"><span style="color:#000;font-size:14px;"><?php echo $menssagem[$msg] ?></span></td>
                </tr>
                <tr>

                    <td align="right">E-mail:</td>
                    <td><input type="text" name="email" value="" size="16"/><span style="font-style:italic;color:#666;">@sorrindo.org</span></td>
                </tr>
                <tr>
                    <td align="right"> Senha: </td>
                    <td> <input  type="password" name="senha" /> </td>
                </tr>
                <tr align="center">

                    <td colspan="2"><input type="submit" name="enviar"  value="Enviar"/></td>
                </tr>
            </table>
        </form>
    </body>
</html>
