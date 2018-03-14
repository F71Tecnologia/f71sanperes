<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

include('../classes_permissoes/acoes.class.php');

$ACOES = new Acoes();

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user_pedido'");
$row_user = mysql_fetch_array($result_user);

if(empty($_REQUEST['pagina'])){

$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];
$id_user = $_COOKIE['logado'];

$result = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra2 where id_compra = '$id_compra'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
$row_user = mysql_fetch_array($result_user);

$result_user_logado = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'", $conn);
$row_user_logado = mysql_fetch_array($result_user_logado);



$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user_logado[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$data = date('d/m/Y');




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - GEST&Atilde;O DE COMPRAS</title>
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">

</head>
<script type="text/javascript" src="jquery-1.2.6.pack.js"></script>
<script type="text/javascript" src="jquery.FCKEditor.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $.fck.config = {path: '../fckeditor/'};
        $('textarea#texto_site').fck({height:700, width:900
});
    });
</script>
<script>
function validaForm(){
           d = document.cadastro;
           if (d.nedital.value == ""){
                     alert("O Número do Edital deve ser informado!");
                     d.nedital.focus();
                     return false;
           }               
           return true;
} 
</script>

<body>
<div id="corpo">
	<div id="conteudo">
    
    
    
     <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
     <h3><img src="../imagensmenu2/compras.gif" alt="cotas" width="20" height="20" align="absmiddle" />ANEXO CHAMAMENTO E EDITAL</h3>
     <form name="cadastro" action="cad_chamamento.php" method="post" enctype="multipart/form-data" onSubmit="return validaForm()">
<table width="750" border="1" cellpadding="0"  cellspacing="0" class="relacao" style="border-collapse:collapse;">

 
      <tr>
        <td width="18%" height="40" valign="middle" class="secao">N&ordm; do Edital:</td>
        <td height="40" colspan="3" align="left" valign="middle"><input type="text" name="n_edital" size="70" /></td>
        </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">N&ordm; do Processo Administrativo:</td>
        <td height="40" colspan="3" align="left" valign="middle" ><input type="text" name="n_proc_adm" size="70" /></td>
      </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">N&ordm; do Processo Licitat&oacute;rio:</td>
        <td height="40" colspan="3" align="left" valign="middle"><input type="text" name="n_proc_lic" size="70" /></td>
      </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">Anexo Edital:</td>
        <td height="40" align="left" colspan="3" valign="middle"><input type="file" name="up_edital" id="up_edital" /></td>
      </tr>
      <tr>
        <td height="40" valign="middle"  class="secao">Anexo Chamamento:</td>
        <td width="34%" height="40" colspan="3" align="left" valign="middle"><input type="file" name="up_chamamento" id="up_chamamento" /></td>
      </tr>
            <tr>
      <td class="secao">STATUS:</td>
      <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" checked="checked" name="status" value="1" />ABERTO &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="0" />FECHADO</td>
      </tr>
      
      <tr>

        <td height="40" align="center" colspan="4" ><center><font color=red><strong>TEXTO QUE SER&Aacute; EXIBIDO NO SITE:</strong></font></td>
      </tr>
      <tr>
      <td colspan="4">
      <textarea name="texto_site" rows="6" id="texto_site" cols="140"></textarea>
      </td>
      </tr>

      
      <input type="hidden" name="compra" value="<?php echo $id_compra; ?>" />
      <input type="hidden" name="regiao" value="<?php echo $regiao; ?>" />
      <input type="hidden" name="id_user" value="<?php echo $id_user; ?>" />

      
      
      <tr>
        <td height="40" valign="middle" align="center" colspan="4" class="secao"><input type="submit" name="Enviar" value="Enviar" /></td>

      </tr>
    </table>

    </form>

     
</div>
</div>
</body>
</html>
<?php
}

}

?>