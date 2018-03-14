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

$result = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra where id_compra = '$id_compra'");
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
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script>
$(document).ready(function(){
	
	var opcao = $('#opcao').val();
	
	$("#sim").change(function(event){
	  event.preventDefault();
	  $("#mostrarnao").hide("slow");
	  event.preventDefault();
	  $("#mostrarsim").show(600);
	});
 
	$("#nao").change(function(event){
	  event.preventDefault();
	  $("#mostrarsim").hide("slow");
	  event.preventDefault();
	  $("#mostrarnao").show(600);
	});
	
});


function validaForm(){
           d = document.cadastro;
           if (d.cotacao.value == ""){
                     alert("Campo Cotação Prévia Obrigatório!");
                     d.cotacao.focus();
                     return false;
           }   
           if (d.docpreco.value == ""){
                     alert("Campo Documento do Preço Anexo Obrigatório!");
                     d.docpreco.focus();
                     return false;
           }   
           if (d.descricao.value == ""){
                     alert("Campo Descrição Obrigatório!");
                     d.descricao.focus();
                     return false;
           }   
           if (d.minutaedital.value == ""){
                     alert("Campo Minuta do Edital!");
                     d.minutaedital.focus();
                     return false;
           }   
           if (d.minutacontrato.value == ""){
                     alert("Campo Minuna do Contrato!");
                     d.minutacontrato.focus();
                     return false;
           }               
           return true;
} 
</script>

<body>
<div id="corpo">
	<div id="conteudo">
    
    
    
     <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
     <h3><img src="../imagensmenu2/compras.gif" alt="cotas" width="20" height="20" align="absmiddle" />LICITA&Ccedil;&Atilde;O</h3>
     <form name="cadastro" action="cadastrolicitacao.php" method="post" enctype="multipart/form-data" onSubmit="return validaForm()">
<table width="750" border="1" cellpadding="0"  cellspacing="0" class="relacao" style="border-collapse:collapse;">

 
      <tr>
        <td width="18%" height="40" valign="middle" class="secao">ATA DE LICITA&Ccedil;&Atilde;O:</td>
        <td height="40" colspan="3" align="left" valign="middle"><input type="file" name="ata" id="ata" /></td>
        </tr>
      <tr>
        <td height="40" colspan="2" valign="middle" bgcolor="#CCCCCC" align="center">ANEXAR PROPOSTAS DE FORNECEDORES:</td>
      </tr>
      <?php
	  $qry_forn = mysql_query("SELECT * FROM fornecedor_site WHERE id_compra='$id_compra'");
	  
	  while($dados_forn=mysql_fetch_assoc($qry_forn))
	  {
	   ?>
      <tr>
        <td height="40" valign="middle"  class="secao"><?= $dados_forn['razao'] ?> </td>
        <td height="40" colspan="3" align="left" valign="middle">
        <input type="hidden" name="fornecedor_site_id[]" value="<?=$dados_forn['fornecedor_site_id']?>" />        <input type="file" name="anexo_proposta[]" id="anexo_proposta" /></td>
      </tr>

   <? }?>
      
      <tr>

        <td height="40" align="center" colspan="4" valign="middle" bgcolor="#CCCCCC" >LICITAÇÃO:</td>
      </tr>
    
      <tr>
        <td height="40" valign="middle"  class="secao">HOUVE LICITAÇÃO?</td>
        <td height="40" colspan="3" align="left" valign="middle">
        <input type="radio" name="opcao" id="sim" value="1"/> HOUVE &nbsp;&nbsp;&nbsp;<input type="radio" name="opcao" id="nao" value="0"/> NÃO HOUVE</td>
      </tr>
      <tr><td colspan="2">
       <div id='mostrarsim' style="display:none">
      <table align="center">
      <tr>
      <td height="40" align="right" >
	  ESCOLHA O FORNECEDOR VENCEDOR:
      </td>
      <td height="40" >
		<select name="fornecedor_id" id="fornecedor_id">
        <option value=0>Selecione o Fornecedor Vencedor</option>
        <?
        $sql_forn="SELECT * FROM fornecedor_site WHERE id_compra='$id_compra' order by razao";
        $sql_result_forn=mysql_query($sql_forn);
        while ($dados_forn=mysql_fetch_array($sql_result_forn)){
          $fornecedor_id=$dados_forn["fornecedor_site_id"];
          $razao=$dados_forn["razao"];
         ?>
                   <option value="<? echo $dados_forn["fornecedor_site_id"];?>"><? echo $dados_forn["razao"];?></option>
         <? } ?>
        </select>
      </td>
      </tr>    
      </table>  
      </div>
      
      <div id='mostrarnao' style="display:none">
      <table align="center">
      <tr>
      <td height="40" align="right" >
      MOTIVO: 
      </td>
      <td height="40" >
      <textarea name="motivo" id="motivo" rows="5" cols="60"> </textarea>
      </td>
      </tr>  
      </table>
      </div>
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