<?php
include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');


$regiao = mysql_real_escape_string($_GET['regiao']);
$link_master 	= $_GET['master'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-5589-1" />
<title>Untitled Document</title>
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript">

$(function(){
	
	
	$('.data_nasc').mask('99/99/9999');
	
	
	$('.c_tipo').change( function(){
		
		if($(this).val() == 3){
			$('#dependente').fadeIn();
		} else {
				$('#dependente').fadeOut();
		}
		
	});
	
	$('.adicionar').click( function() {
		
		$('.data_nasc').mask('99/99/9999');
	
	
	var campos = "<div><table style=\"background-color:  #EFEFEF;width:100%;\" class='relacao'><tr height='35'><td class='secao'> Nome:</td><td align='left'><input name='dependente_nome[]' type='text' style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='90' /></td></tr><tr height='35'><td class='secao'> Grau de Parentesco:</td><td align='left'><input name='dependente_parentesco[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='30' /></span></td></tr><tr height='35'><td class='secao'> Data de Nascimento:</td><td align='left'><input name='dependente_nascimento[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='10' class='data_nasc' /></span></td></tr> <tr height='35'><td colspan='2'>&nbsp;</td></tr><tr><td><a href='#' onclick='$(this).parent().parent().parent().remove(); return false;'>Remover</a</td></tr></table><br></div>";
		
		 $('#tabela_dependente').append(campos);
		
		
	});
	

	
});

function validaForm(){
d = document.form1;
if (d.endereco.value == ""){
alert("O campo Endereço deve ser preenchido!");
d.endereco.focus();
return false;
}
if (d.cnpj.value == ""){
alert("O campo CNPJ deve ser preenchido!");
d.cnpj.focus();
return false;
}
if (d.c_fantasia.value == ""){
alert("O campo Nome Fantasia deve ser preenchido!");
d.c_fantasia.focus();
return false;
}
if (d.c_razao.value == ""){
alert("O campo Razão Social deve ser preenchido!");
d.c_razao.focus();
return false;
}
if (d.c_endereco.value == ""){
alert("O campo Endereço deve ser preenchido!");
d.c_endereco.focus();
return false;
}
if (d.c_cnpj.value == ""){
alert("O campo CNPJ deve ser preenchido!");
d.c_cnpj.focus();
return false;
}
if (d.c_ie.value == ""){
alert("O campo  IE deve ser preenchido!");
d.c_ie.focus();
return false;
}
if (d.c_im.value == ""){
alert("O campo IM deve ser preenchido!");
d.c_im.focus();
return false;
}
if (d.c_responsavel.value == ""){
alert("O campo Responsavel deve ser preenchido!");
d.c_responsavel.focus();
return false;
}
if (d.c_rg.value == ""){
alert("O campo RG deve ser preenchido!");
d.c_rg.focus();
return false;
}
if (d.c_cpf.value == ""){
alert("O campo CPF deve ser preenchido!");
d.c_cpf.focus();
return false;
}
if (d.co_responsavel.value == ""){
alert("O campo Responsavel deve ser preenchido!");
d.co_responsavel.focus();
return false;
}
if (d.co_tel.value == ""){
alert("O campo Telefone deve ser preenchido!");
d.co_tel.focus();
return false;
}
if (d.co_municipio.value == ""){
alert("O campo Municipio deve ser preenchido!");
d.co_municipio.focus();
return false;
}
if (d.assunto.value == ""){
alert("O campo Assunto deve ser preenchido!");
d.assunto.focus();
return false;
}
if (d.data_proc.value == ""){
alert("O campo Data do Processo deve ser preenchido!");
d.data_proc.focus();
return false;
}
if (d.objeto.value == ""){
alert("O campo Objeto deve ser preenchido!");
d.objeto.focus();
return false;
}
if (d.especificacao.value == ""){
alert("O campo Especificação deve ser preenchido!");
d.especificacao.focus();
return false;
}
return true;   }
</script>
</head>

<body>
<div id="corpo">
	<div id="conteudo">
    
    <form action="prestadorservico.php" name="form1" id="form1" method="post" onSubmit="return validaForm()">
  
	<table id="cadastro"  class="relacao" style="margin-top:40px;">
	<tr class="titulo_tabela1">
		<td height="21" colspan="6" > DADOS DO PROJETO </td>
	</tr>
   <tr>
	   	<td width="19%" height="30">Projeto:</td>
	  	<td width="81%" height="30" colspan="6" align="left">
			<?php 
		    $result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'  AND (status_reg = '1' OR status_reg = '0')");
		    print "<select name='projeto'>";
		    while($row_projeto = mysql_fetch_array($result_projeto)){
		    print "<option value='$row_projeto[0]'>".htmlentities($row_projeto['nome'])."</option>";
		    }
		    print "</select>";
		    ?>
	     </td>
  </tr>
 
  <tr>
  <td height="31" colspan="6"  class="titulo_tabela1">DADOS DO CONTRATANTE</td>
  </tr>
  <tr>
	  <td class="secao"> Contratante:</td>
	  <td align="left" colspan="5">
	    <input name="contratante" type="text" id="contratante" 
	value="<?= $row_master['razao']?>" size="90"  />
	  </td>
	  </tr>
	  <tr>
	  <td height="35" class="secao_nova">Endere&ccedil;o:</td>
	  <td height="35" colspan="5" align="left">
	    <input name="endereco" type="text" id="endereco" size="90" 
	onfocus="document.all.endereco.style.background='#CCFFCC'" onBlur="document.all.endereco.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()"  class="validate[required]" value="<?= $row_master['endereco']?>"/>
	  </td>
  </tr>
  <tr>
  <td height="35" class="secao_nova">CNPJ:</td>
  <td height="35" colspan="5" align="left">
	    <input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;"
	onfocus="document.all.cnpj.style.background='#CCFFCC'"  value="<?=$row_master['cnpj']?>"
	onblur="document.all.cnpj.style.background='#FFFFFF'"
	onkeypress="formatar('##.###.###/####-##', this)" 
	onkeyup="pula(18,this.id,c_fantasia.id)" size="20" maxlength="18" />
  </td>
  </tr>
  <tr>
	  <td height="35"   class="secao">Responsavel:</td>
	  <td align="left">
	  <input name="responsavel" type="text" id="responsavel" value="<?=$row_master['responsavel']?>" size="40" />
	  </td>
	  <td >Estado civil:</td>
	  <td colspan="3" align="left"> <input name="civil" type="text" id="civil" value="<?=$row_master['civil']?>" size="20" />  </td>
  </tr>
  <tr>
	  <td height="35"  class="secao">Nacionalidade:</td>
	  <td align="left">
	  <input name="nacionalidade" type="text" id="nacionalidade" value="<?=$row_master['nacionalidade']?>" size="40" />
	  </td>
	  <td>
	  Forma&ccedil;&atilde;o: 
	  </td>
	  <td colspan="3 " align="left">
	  <input name="formacao" type="text" id="formacao" value="<?=$row_master['formacao']?>" size="20" />
	 </td>
  </tr>
  <tr>
	  <td height="35"  class="secao_nova">RG:</td>
	  <td align="left">  <input name="rg" type="text" id="rg" size="20" maxlength="14" value="<?=$row_master['rg']?>"/>  </td>
	  <td>CPF:</td>
	  <td colspan="3" align="left"> <input name="cpf" type="text" id="cpf" value="<?=$row_master['cpf']?>" size="20" /></td>
</tr>
  <tr>
  <td height="31" colspan="6" class="titulo_tabela1">DADOS DA EMPRESA CONTRATADA</td>
  </tr>
  <tr>  	
         
          <td class="secao">Tipo: </td>
          <td colspan="5" align="left" > <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="1"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
            style="background:#FFFFFF; text-transform:lowercase;" /> 
              <strong>1</strong> - Pessoa Jurídica<br>
            
           
            <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="2"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
            style="background:#FFFFFF; text-transform:lowercase;" /> <strong>2</strong> - Pessoa Jurídica - Cooperativa</br>
            
          
              <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="3"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
            style="background:#FFFFFF; text-transform:lowercase;" /> <strong>3</strong> - Pessoa Física</br>
            
           
               <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="4"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
            style="background:#FFFFFF; text-transform:lowercase;" /><strong>4</strong> - Pessoa Jurídica - Prestador de Serviço</br>
           
            
            <!---ADICIONADO  MAIS DOIS TIPOS DE PESSOA JURÍDICA  DIA 25/08/2011 ---->
            
            
            
            
               <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="5"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
            style="background:#FFFFFF; text-transform:lowercase;" /><strong>5</strong> - Pessoa Jurídica - Administradora</br>
            
             
               <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="6"   onfocus="document.all.c_site.style.background='#CCFFCC'"     onblur="document.all.c_site.style.background='#FFFFFF'"
            style="background:#FFFFFF; text-transform:lowercase; " /><strong>6</strong> - Pessoa Jurídica - Publicidade</br>
            
            
             
               <input name="c_tipo" type="radio" class="c_tipo" size="38"   value="7"   onfocus="document.all.c_site.style.background='#CCFFCC'"     
               onblur="document.all.c_site.style.background='#FFFFFF'"      style="background:#FFFFFF; text-transform:lowercase; " /><strong>7</strong> - Pessoa Jurídica Sem Retenção</br>
            </span>
          
        
          
          </td>
  
  </tr>
  
  <tr id="dependente" style="display:none;">  
          <td valign="top">
        
<span class="titulos" style="display:block; text-align:center;"> <strong>Dados do(s) Dependente(s): <br> <span class="adicionar" style="cursor:pointer"> <img src="../imagens/adicionar_dep.gif" width="36" height="26" title="Adicionar Dependente."/></span> </strong> </span>   
</td>	                       
      <td  colspan="6" id="tabela_dependente">        
          
     
            			
            
                <div id="tabela_dependente" style="background-color: #DEF;padding-top:10px;">
               	<table style="background-color:  #EFEFEF;width:100%;" class="relacao" >
                <tr height='35'>
                    <td  class="secao">Nome:</td>
                    <td align="left"> <input name='dependente_nome[]' type='text' style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='90' /></td>
                </tr>
                
                <tr height='35'>
                    <td class="secao">Grau de Parentesco: </td>
                    <td align="left"><input name='dependente_parentesco[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='30' /></td>
                </tr>
                
                <tr height='35'>
                    <td class="secao">Data de Nascimento:</td>
                    <td align="left"> <input name='dependente_nascimento[]' type='text'  style='background:#FFFFFF;' onchange='this.value=this.value.toUpperCase()' size='10'  class='data_nasc'/> </td>
                </tr> 
                
                <tr height='35'><td colspan='2'>&nbsp;</td></tr>
               
                
                
            </table>
          
          </div>
          
          </td>
          
          
          
  </tr>
  
  <tr>
  <td height="35" >Nome Fantasia:</td>
  <td colspan="5" align="left">
	  <input name="c_fantasia" type="text" id="c_fantasia" style="background:#FFFFFF;" 
	onfocus="document.all.c_fantasia.style.background='#CCFFCC'" 
	onblur="document.all.c_fantasia.style.background='#FFFFFF'" 
		onchange="this.value=this.value.toUpperCase()" size="90" />
	</td>
  </tr>
  <tr>
  <td height="35" >Raz&atilde;o Social:</td>
  <td colspan="5" align="left">
	  <input name="c_razao" type="text" id="c_razao" size="90" 
	onfocus="document.all.c_razao.style.background='#CCFFCC'" 
	onblur="document.all.c_razao.style.background='#FFFFFF'" 
	style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" />
  </td>
  </tr>
  <tr>
  <td height="35" >Endere&ccedil;o:?</td>
  <td colspan="5" align="left">
  <input name="c_endereco" type="text" id="c_endereco" size="90" 
onfocus="document.all.c_endereco.style.background='#CCFFCC'" 
onblur="document.all.c_endereco.style.background='#FFFFFF'" 
style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" />
 </td>
  </tr>
  <tr>
	  <td height="35" >CNPJ:</td>  
	  <td align="left">
		  <input name="c_cnpj" type="text" id="c_cnpj" 
		style="background:#FFFFFF; text-transform:uppercase;"
		onfocus="document.all.c_cnpj.style.background='#CCFFCC'" 
		onblur="document.all.c_cnpj.style.background='#FFFFFF'" 
		onkeyup="pula(18,this.id,c_ie.id)"
		onkeypress="formatar('##.###.###/####-##', this)" size="18" maxlength="18" />
	  </td>
	  <td>IE:</td>
	  <td align="left">  <input name="c_ie" type="text" id="c_ie" size="15" onFocus="document.all.c_ie.style.background='#CCFFCC'" onBlur="document.all.c_ie.style.background='#FFFFFF'" style="background:#FFFFFF;" /></td>
	  <td>CCM:</td>
	  <td align="left"><input name="c_im" type="text" id="c_im" size="15" onFocus="document.all.c_im.style.background='#CCFFCC'" onBlur="document.all.c_im.style.background='#FFFFFF'" style="background:#FFFFFF;" /></td>
   </tr>
  <tr>
  <td height="35" >Telefone:</td>
  <td  align="left">
  <input name='c_tel' type='text' id='c_tel' size='12' 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,c_fax.id)" 
onfocus="document.all.c_tel.style.background='#CCFFCC'" 
onblur="document.all.c_tel.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
</td>
<td class="secao">Fax:</td>
<td align="left">
  <input name="c_fax" type="text" id="c_fax" size="12" 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,c_email.id)" 
onfocus="document.all.c_fax.style.background='#CCFFCC'" 
onblur="document.all.c_fax.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
</td>
<td class="secao"> E-mail: </td>
<td align="left"> <input name="c_email" type="text" id="c_email" size="25" 
onfocus="document.all.c_email.style.background='#CCFFCC'" 
onblur="document.all.c_email.style.background='#FFFFFF'" 
style="background:#FFFFFF; text-transform:lowercase;" />
</td>


  </tr>
  <tr>
  <td height="35" class="secao">Responsavel:</td>
  <td align="left">
	  <input name="c_responsavel" type="text" id="c_responsavel" size="40"
	style="background:#FFFFFF;" 
	onfocus="document.all.c_responsavel.style.background='#CCFFCC'" 
	onblur="document.all.c_responsavel.style.background='#FFFFFF'" 
	onchange="this.value=this.value.toUpperCase()" />
   </td>
  <td class="secao">Estado civil:</td>
  <td colspan="3" align="left">
	  <input name="c_civil" type="text" id="c_civil" size="20"
	style="background:#FFFFFF;" 
	onfocus="document.all.c_civil.style.background='#CCFFCC'" 
	onblur="document.all.c_civil.style.background='#FFFFFF'" 
	onchange="this.value=this.value.toUpperCase()" />
    </td>
  </tr>
  <tr>
  <td height="35" class="secao">Nacionalidade:</td>
  <td align="left">
  <input name="c_nacionalidade" type="text" id="c_nacionalidade" size="40" 
style="background:#FFFFFF;" 
onfocus="document.all.c_nacionalidade.style.background='#CCFFCC'" 
onblur="document.all.c_nacionalidade.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
<td class="secao">Forma&ccedil;&atilde;o: </td>
<td  colspan="3" align="left">
  <input name="c_formacao" type="text" id="c_formacao" size="20" 
style="background:#FFFFFF;" 
onfocus="document.all.c_formacao.style.background='#CCFFCC'" 
onblur="document.all.c_formacao.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
  </tr>
  <tr>
	  <td height="35" class="secao">RG:</td>
	  <td align="left">
	  <input name="c_rg" type="text" id="c_rg" 
	onkeypress="formatar('##.###.###-##', this)" size="20" maxlength="14" 
	onfocus="document.all.c_rg.style.background='#CCFFCC'" 
	onblur="document.all.c_rg.style.background='#FFFFFF'" 
	style="background:#FFFFFF;" />
	  </td>
	 <td class="secao">CPF:</td>
	 <td  colspan="3" align="left">
	  <input name="c_cpf" type="text" id="c_cpf" 
	onkeypress="formatar('###.###.###-##', this)" size="20" maxlength="14" 
	onkeyup="pula(14,this.id,c_email2.id)" 
	onfocus="document.all.c_cpf.style.background='#CCFFCC'" 
	onblur="document.all.c_cpf.style.background='#FFFFFF'" 
	style="background:#FFFFFF;" />
	 </td>
	</tr>
	  <tr>
	  <td height="35" class="secao">E-mail: </td>
      <td align="left"> 
	  <input name="c_email2" type="text" id="c_email2" size="30" 
	onfocus="document.all.c_email2.style.background='#CCFFCC'" 
	onblur="document.all.c_email2.style.background='#FFFFFF'" 
	style="background:#FFFFFF; text-transform:lowercase;" />
	</td>
	<td class="secao">Site: </td>
	<td  colspan="4" align="left">
	  <input name="c_site" type="text" id="c_site" size="38" 
	onfocus="document.all.c_site.style.background='#CCFFCC'" 
	onblur="document.all.c_site.style.background='#FFFFFF'"
	style="background:#FFFFFF; text-transform:lowercase;" />
	 </td>
 </tr>
  
  <tr>
  <td height="25" class="titulo_tabela1"  colspan="6">DADOS DA PESSOA DE  CONTATO NA CONTRATADA</td>
  </tr>
  <tr>
  <td height="35" class="secao">Nome Completo:</td>
  <td  colspan="5">
    <input name="co_responsavel" type="text" id="co_responsavel" size="27"
style="background:#FFFFFF;" 
onfocus="document.all.co_responsavel.style.background='#CCFFCC'" 
onblur="document.all.co_responsavel.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
</tr>
<tr>
  <td class="secao">Telefone:</td>
  <td align="left">
  <input name='co_tel' type='text' id='co_tel' size='12' 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_fax.id)" 
onfocus="document.all.co_tel.style.background='#CCFFCC'" 
onblur="document.all.co_tel.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
</td>
 <td class="secao"> Fax:</td>
 <td  colspan="3" align="left">
  <input name="co_fax" type="text" id="co_fax" size="12" 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_civil.id)" 
onfocus="document.all.co_fax.style.background='#CCFFCC'" 
onblur="document.all.co_fax.style.background ='#FFFFFF'" 
style="background:#FFFFFF;" />
</td>
  </tr>
  <tr>
  
	  <td height="35" class="secao"> Email: </td>
	  <td  colspan="5">
		    <input name="co_email" type="text" id="co_email" size="30" 
		  onfocus="document.all.co_email.style.background='#CCFFCC'" 
		  onblur="document.all.co_email.style.background='#FFFFFF'" 
		  style="background:#FFFFFF; text-transform:lowercase;" />
	</td>
  </tr>
  <tr>
  <td height="35"  class="secao">Estado civil:</td>
  <td align="left">
    <input name="co_civil" type="text" id="co_civil" size="20"
style="background:#FFFFFF;" 
onfocus="document.all.co_civil.style.background='#CCFFCC'" 
onblur="document.all.co_civil.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
<td class="secao"> Nacionalidade:</td>
<td  colspan="4" align="left">
  <input name="co_nacionalidade" type="text" id="co_nacionalidade" size="27" 
style="background:#FFFFFF;" 
onfocus="document.all.co_nacionalidade.style.background='#CCFFCC'" 
onblur="document.all.co_nacionalidade.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
<tr>
  <td height="35"  class="secao"> Data de Nascimento:</td>
  <td	colspan="5" align="left">
  <input name="co_data_nasc" type="text" id="co_data_nasc" size="27" 
style="background:#FFFFFF;" 
onfocus="document.all.co_data_nasc.style.background='#CCFFCC'" 
onblur="document.all.co_data_nasc.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()"  class='data_nasc' />
</td>
 
 </tr>
  
  
  
  
  <tr>
  <td height="25" colspan="6" bgcolor="#C9C9C9">Sócio 1</td>
  </tr>
  <tr>
  <td height="35"  class="secao">Nome Completo:</td>
  <td align="left">
    <input name="co_responsavel_socio1" type="text" id="co_responsavel_socio1" size="27"
style="background:#FFFFFF;" 
onfocus="document.all.co_responsavel_socio1.style.background='#CCFFCC'" 
onblur="document.all.co_responsavel_socio1.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
<td  class="secao">Telefone:</td>
<td align="left">
  <input name='co_tel_socio1' type='text' id='co_tel_socio1' size='12' 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_fax.id)" 
onfocus="document.all.co_tel_socio1.style.background='#CCFFCC'" 
onblur="document.all.co_tel_socio1.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
</td>
<td  class="secao">Fax:</td>
 <td align="left"> 
 		<input name="co_fax_socio1" type="text" id="co_fax_socio1" size="12" 
		onkeypress="return(TelefoneFormat(this,event))" 
		onkeyup="pula(13,this.id,co_civil.id)" 
		onfocus="document.all.co_fax_socio1.style.background='#CCFFCC'" 
		onblur="document.all.co_fax_socio1.style.background='#FFFFFF'" 
		style="background:#FFFFFF;" />
 </td>
  </tr>
    <tr>
	  <td height="35"  class="secao">Email: </td>
	  <td colspan="5" align="left">
		  <input name="co_email_socio1" type="text" id="co_email_socio1" size="30" 
		onfocus="document.all.co_email_socio1.style.background='#CCFFCC'" 
		onblur="document.all.co_email_socio1.style.background='#FFFFFF'" 
		style="background:#FFFFFF; text-transform:lowercase;" />
	</td>
  </tr>
  <tr>
  <td height="35"  class="secao">Estado civil:</td>
  <td  align="left">
    <input name="co_civil_socio1" type="text" id="co_civil_socio1" size="20"
style="background:#FFFFFF;" 
onfocus="document.all.co_civil_socio1.style.background='#CCFFCC'" 
onblur="document.all.co_civil_socio1.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
<td  class="secao"> Nacionalidade:</td>
<td colspan="3" align="left"> <input name="co_nacionalidade_socio1" type="text" id="co_nacionalidade_socio1" size="27" 
style="background:#FFFFFF;" 
onfocus="document.all.co_nacionalidade_socio1.style.background='#CCFFCC'" 
onblur="document.all.co_nacionalidade_socio1.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
<tr>
  <td height="35"  class="secao"> Data de Nascimento:</td>
  <td colspan="5" align="left">
	  <input name="co_data_nasc_socio1" type="text" id="co_data_nasc_socio1" size="27" 
	style="background:#FFFFFF;" 
	onfocus="document.all.co_data_nasc_socio1.style.background='#CCFFCC'" 
	onblur="document.all.co_data_nasc_socio1.style.background='#FFFFFF'" 
	onchange="this.value=this.value.toUpperCase()"  class='data_nasc' />
  </td>
  </tr>

  <tr>
  <td height="35"  class="secao">Município: </td>
  <td colspan="5" align="left">
  <input name="co_municipio_socio1" type="text" id="co_municipio_socio1" size="30" 
onfocus="document.all.co_municipio_socio1.style.background='#CCFFCC'" 
onblur="document.all.co_municipio_socio1.style.background='#FFFFFF'" 
style="background:#FFFFFF; text-transform:lowercase;" /></td>
  </tr>
  
  
  
  
   <tr>
  <td height="25" colspan="6" bgcolor="#C9C9C9">Sócio 2</span></div></td>
  </tr>
  <tr>
  <td height="35"  class="secao">Nome Completo:</td>
  <td align="left">
    <input name="co_responsavel_socio2" type="text" id="co_responsavel_socio2" size="27"
style="background:#FFFFFF;" 
onfocus="document.all.co_responsavel_socio2.style.background='#CCFFCC'" 
onblur="document.all.co_responsavel_socio2.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
  </td>
  <td  class="secao">Telefone:</td>
  <td   align="left">
  <input name='co_tel_socio2' type='text' id='co_tel_socio2' size='12' 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_fax.id)" 
onfocus="document.all.co_tel_socio2.style.background='#CCFFCC'" 
onblur="document.all.co_tel_socio2.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
  </td>
  <td class="secao">Fax:</td>
  <td align="left">
  <input name="co_fax_socio2" type="text" id="co_fax_socio2" size="12" 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_civil.id)" 
onfocus="document.all.co_fax_socio2.style.background='#CCFFCC'" 
onblur="document.all.co_fax_socio2.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
  </td>
  </tr>
  <tr>
  <td height="35" class="secao" >Estado civil:</td>
  <td align="left">
    <input name="co_civil_socio2" type="text" id="co_civil_socio2" size="20"
style="background:#FFFFFF;" 
onfocus="document.all.co_civil_socio2.style.background='#CCFFCC'" 
onblur="document.all.co_civil_socio2.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
  <td class="secao">Nacionalidade:</td>
  <td colspan="5" align="left">
  <input name="co_nacionalidade_socio2" type="text" id="co_nacionalidade_socio2" size="27" 
style="background:#FFFFFF;" 
onfocus="document.all.co_nacionalidade_socio2.style.background='#CCFFCC'" 
onblur="document.all.co_nacionalidade_socio2.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />

</td>
<tr>
  <td height="35" class="secao" > Data de Nascimento:</td>
  <td colspan="5" align="left">
	  <input name="co_data_nasc_socio2" type="text" id="co_data_nasc_socio2" size="27" 
	style="background:#FFFFFF;" 
	onfocus="document.all.co_data_nasc_socio2.style.background='#CCFFCC'" 
	onblur="document.all.co_data_nasc_socio2.style.background='#FFFFFF'" 
	onchange="this.value=this.value.toUpperCase()"  class='data_nasc' />
 </td>
  </tr>
  
  <tr>
  <td height="35"  class="secao">Email: </td>
  <td colspan="5" align="left">
  <input name="co_email_socio2" type="text" id="co_email_socio2" size="30" 
onfocus="document.all.co_email_socio2.style.background='#CCFFCC'" 
onblur="document.all.co_email_socio2.style.background='#FFFFFF'" 
style="background:#FFFFFF; text-transform:lowercase;" />
</td>
  </tr>
  <tr>
  <td height="35" class="secao" >Município:</td>
  <td colspan="5" align="left"> 
	  <input name="co_municipio_socio2" type="text" id="co_municipio_socio2" size="30" 
	onfocus="document.all.co_municipio_socio2.style.background='#CCFFCC'" 
	onblur="document.all.co_municipio_socio2.style.background='#FFFFFF'" 
	style="background:#FFFFFF; text-transform:lowercase;" />
	</td>
  </tr>
  
  
  <tr>
  <td height="29" colspan="6"  class="titulo_tabela1">DADOS BANCÁRIOS</td>
  </tr>
  <tr>
  <tr>
  <td height="44"  class="secao">Nome do banco:</td>
  <td colspan="5" align="left">
  <input name="co_nome_banco" type="text" id="co_nome_banco" size="20" 
style="background:#FFFFFF;" 
onfocus="document.all.co_nome_banco.style.background='#CCFFCC'" 
onblur="document.all.co_nome_banco.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</td>
  </tr>
  
  
  <tr>
  <td height="35"  class="secao">Agência:</td>
  <td align="left">
    <input name="co_agencia" type="text" id="co_agencia" size="20"
style="background:#FFFFFF;" 
onfocus="document.all.co_agencia.style.background='#CCFFCC'" 
onblur="document.all.co_agencia.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
  </td>
  <td  class="secao">Conta:</td>
  <td colspan="3" align="left">
  <input name="co_conta" type="text" id="co_conta" size="27" 
style="background:#FFFFFF;" 
onfocus="document.all.co_conta.style.background='#CCFFCC'" 
onblur="document.all.co_conta.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />

</td>
<tr>
  
  
  
  <tr>
  <td height="29" colspan="6"  class="titulo_tabela1">OBJETO DO CONTRATO</td>
  </tr>
  <tr>
  <td height="44" class="secao" colspan="2">Munic&iacute;pio onde ser&aacute; executado o servi&ccedil;o:</td>
  <td colspan="4" align="left">
  <input name="co_municipio" type="text" id="co_municipio" size="20" 
style="background:#FFFFFF;" 
onfocus="document.all.co_municipio.style.background='#CCFFCC'" 
onblur="document.all.co_municipio.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
 </td>
  </tr>
  <tr>
  <td height="44"  class="secao" colspan="2">Assunto:</td>
  <td colspan="4" align="left">
	  <input name="assunto" type="text" id="assunto" size="20" 
	style="background:#FFFFFF;" 
	onfocus="document.all.assunto.style.background='#CCFFCC'" 
	onblur="document.all.assunto.style.background='#FFFFFF'" 
	onchange="this.value=this.value.toUpperCase()" />
    </td>
    </tr>
    <tr>
    <td  class="secao" colspan="2">Data do Processo:</td>
    <td colspan="4" align="left">
	  <input name="data_proc" type="text" id="data_proc" size="10" 
	onkeyup="mascara_data(this)" maxlength="10"
	onfocus="document.all.data_proc.style.background='#CCFFCC'" 
	onblur="document.all.data_proc.style.background='#FFFFFF'" 
	style="background:#FFFFFF;" />
  </td>
  </tr>
  <tr>
  <td height="102" colspan="6" align="center">
    
      <label>
        <textarea name="objeto" id="objeto" cols="45" rows="5" 
onfocus="document.all.objeto.style.background='#CCFFCC'" 
onblur="document.all.objeto.style.background='#FFFFFF'" 
style="background:#FFFFFF;"
onchange="this.value=this.value.toUpperCase()"></textarea>
        </label>
   </td>
  </tr>
  <tr>
  <td height="27" colspan="6" class="titulo_tabela1">ESPECIFICA&Ccedil;&Atilde;O DO TIPO DE SERVI&Ccedil;O A SER PRESTADO</td>
  </tr>
  <tr>
  <td height="102" colspan="6" align="center">
      <label>
        <textarea name="especificacao" id="especificacao" cols="45" rows="5" 
onfocus="document.all.especificacao.style.background='#CCFFCC'" 
onblur="document.all.especificacao.style.background='#FFFFFF'" 
style="background:#FFFFFF;"
onchange="this.value=this.value.toUpperCase()"></textarea>
     </label>
    </td>
  </tr>
  <tr style="display:">
  <td height="46"  >ANEXO I &ndash;  VALOR R$</td>
  <td>
  <input name="valor" type="text" id="valor" size="20" 
onkeydown="FormataValor(this,event,20,2)" 
onfocus="document.all.valor.style.background='#CCFFCC'" 
onblur="document.all.valor.style.background='#FFFFFF'" 
style="background:#FFFFFF;"/>
</td>
<td  class="secao">DATA:</td>
		<td colspan="4" align="left">
	 <input name="data_inicio" type="text" id="data_inicio" size="10" 
	onkeyup="mascara_data(this)" maxlength="10"
	onfocus="document.all.data_inicio.style.background='#CCFFCC'" 
	onblur="document.all.data_inicio.style.background='#FFFFFF'" 
	style="background:#FFFFFF;" />
      </td>
</tr>
  <tr>
    <td height="46" colspan="6" align="center" valign="middle" >
  <input type="hidden" name="id" value="2">
  <input type="hidden" name="regiao" value="<?=$regiao?>">
  <label>
    <input type="submit" name="Submit" id="button" value="Cadastrar">
  </label>
      </td>
  </tr>
</table>
    
 
  </form>
  
    
    
    
    
    </div>
</div>

</body>
</html>
