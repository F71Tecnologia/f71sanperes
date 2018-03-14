<?php
include_once('classes/LoginClass.php');
$acesso = new Login();

function printArr($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

//*Search by Formel* Linha alterada por suspeita de virus em 2014-09-09
setcookie("logado", "", time() - 3600);
include "conn.php";

if (empty($_REQUEST['login'])){

    if ($_REQUEST['logout']) {
        $acesso->setErro("Voc&ecirc; acabou de sair!");
    }
}else{
    $id = $_REQUEST['id'];
    
    switch ($id) {
        
        case 1:
            
            $login = $_REQUEST['login'];
            $senha = $_REQUEST['senha'];
    
            $acesso->getAcesso($login, $senha);
            $funcionario = $acesso->getDados();
            //printArr($funcionario);
            if($acesso->getErro() == ''){
                $acesso->getAcessoDias($funcionario['acesso_dias'],$funcionario['horario_inicio'], $funcionario['horario_fim']);

                if($acesso->getErro() == ''){
                    if($funcionario['alt_senha'] == 0){
                        setcookie("logado", $funcionario['id_funcionario'], 0);
                        $acesso->gravaSessao($funcionario);
                        $acesso->gravaLog($funcionario);
                        //echo "<script> location.href = 'index.php'; </script>";
                        header("Location: index.php");
                        exit;
                    }
                }
            }
        break;
        
        case 2:
            $id_user = $_REQUEST['id_user'];
            $login = $_REQUEST['login'];
            $senha_antiga = $_REQUEST['senha_antiga'];
            $senha_nova = $_REQUEST['senha_nova'];
            
            $acesso->getAcessoById($id_user);
            $funcionario = $acesso->getDados();
            $acesso->verificaSenha($login, $senha_nova, $senha_antiga);
            
            if($acesso->getErro() == ''){
                $acesso->atualizaSenha($senha_nova, $funcionario['id_funcionario']);
                setcookie("logado", $funcionario['id_funcionario']);
                $acesso->gravaSessao($funcionario);
                $acesso->gravaLog($funcionario);
                //echo "<script> location.href = 'index.php'; </script>";
                header("Location: index.php");
                exit;
            }
        break;
    }
}
?>
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
    <div style="margin: auto; width: 800px; height: 569px; border: 1px solid #CCC; background-image: url('imagens/abertura.jpg');">
        <form action="" method="post" name="form1">
            <table border="0" cellpadding="0" cellspacing="0" style="margin: 180px 95px 0 457px; width: 248px;">
                <tr>
                    <td height="44" valign="bottom" style="text-align: center;">
                        <span style="font-weight:bold; font-family:Arial, Helvetica, sans-serif; font-size:12px">Sistema de Gerenciamento</span>
                    </td>
                </tr>
                <?php if($funcionario['alt_senha'] == 1 AND $acesso->getErro() == ""){ ?>
                <tr>
                    <td height="20"><label style="color: #003366; font-family: Verdana, Geneva, sans-serif; font-weight: bold; font-size: 10px; margin-left: 20%;"> SENHA ANTIGA</label></td>
                </tr>
                <tr>
                    <td>
                        <input name="senha_antiga" type="password" id="senha_antiga"  style="background-color:#FFA6A8; margin-left: 20%; width: 60%;" />
                    </td>
                </tr>
                <tr>
                    <td height="20"><label style="color: #003366; font-family: Verdana, Geneva, sans-serif; font-weight: bold; font-size: 10px; margin-left: 20%;"> NOVA SENHA</label></td>
                </tr>
                <tr>
                    <td>
                        <input name="senha_nova" type="password" id="senha_nova"  style="background-color:#C0ACFB; margin-left: 20%; width: 60%;"/>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="linha" style="color: #F00;"><?php echo $acesso->getErro(); ?></span></td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <input type="submit" name="enviar2" id="enviar2" value="Acessar" />
                        <input name="id" type='hidden' id='id' value='2' />
                        <input name="login" type="hidden" id="login" value="<?= $funcionario['login'] ?>" />
                        <input name="id_user" type="hidden" id="id_user" value="<?= $funcionario['id_funcionario'] ?>" />
                    </td>
                </tr>
                <?php }else{ ?>
                <tr>
                    <td height="20"><label style="color: #003366; font-family: Verdana, Geneva, sans-serif; font-weight: bold; font-size: 10px; margin-left: 20%;"> LOGIN</label></td>
                </tr>
                <tr>
                    <td>
                        <input name="login" type="text" id="login"  tabindex="1" onfocus="this.style.background='#aeaeae'" onblur="this.style.background='#cccccc'" style="background:#aeaeae; margin-left: 20%; width: 60%;"/>
                    </td>
                </tr>
                <tr>
                    <td height="20"><label style="color: #003366; font-family: Verdana, Geneva, sans-serif; font-weight: bold; font-size: 10px; margin-left: 20%;"> SENHA</label></td>
                </tr>
                <tr>
                    <td>
                        <input name="senha" type="password" id="senha" tabindex="2" onfocus="this.style.background='#aeaeae'" onblur="this.style.background='#cccccc'" style="background:#aeaeae; margin-left: 20%; width: 60%;"/>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;"><span class="linha" style="color: #F00;"><?php echo $acesso->getErro(); ?></span></td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <input type="submit" name="enviar" id="enviar" value="Acessar" tabindex="3" />
                        <input name='id' type='hidden' id='id' value='1' />
                    </td>
                </tr>
                <?php } ?>
            </table>
        </form>
    </div>
</body>
</html>