<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$master = $row_user['id_master'];

// SELECIONANDO AS REGIÕES CADASTRADAS NO BANCO
$sql = "SELECT * from regioes where id_master = '$row_user[id_master]'";
$result = mysql_query($sql, $conn);

$id_regiao = $_REQUEST['regiao'];
?>
<html>
<head><title>:: Intranet ::- CADASTRO DE FUN&Ccedil;&Otilde;ES(AUTONOMO)</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<script language="javascript" src="jquery-1.3.2.js"></script>

<script language="javascript" src='ajax.js' type='text/javascript'></script>
<script language="javascript" src='../js/ramon.js' type='text/javascript'></script>
<link href='../autocomp/css.css' type='text/css' rel='stylesheet'>
<script src='../jquery/jquery-1.4.2.min.js' type='text/javascript'></script>
<script src="../jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
<script src="../jquery/validationEngine/jquery.validationEngine-pt.js" type="text/javascript"></script>
<link  href="../jquery/validationEngine/validationEngine.jquery.css" type="text/css" rel="stylesheet"/>
<link  href="../adm/css/estrutura.css" type="text/css" rel="stylesheet"/>

<script src="ajax_cbo.js" type="text/javascript"></script>

<style>
#ajax{
	visibility:hidden;
	border:2px solid #CCCCCC;
	width:300px;
	position:absolute;
	background:#FFFFFF;
	font:8pt Tahoma, "Trebuchet MS", Arial;
	padding-bottom:37px;
	padding-bottom:35px;
	margin:9px 459px;
	*margin:24px 85px;
	top: 300px;
	left: 200px;
}

#ajax h3{font:bold 10pt "Trebuchet MS", Arial;margin:5px 10px 0}

#ajax small{margin:0 10px;position:relative;top:-3px;color:#666;display:block}
#ajax li a{display:block;padding:5px 4px 4px 22px;color:#000;text-decoration:none;background:#fff url('/img/topic_default.gif') 2px 2px no-repeat}
#ajax a:hover{color:#333333;text-decoration:none;background-color:#F5F5F5}
#ajax ul{margin:0 5px;padding:0;list-style:none}
#ajax #info{position:absolute;bottom:0;background:#ffe;padding:5px;text-align:center;font-size:7.5pt;border-top:1px solid #fc0;width:290px;*width:296px;}



#lista_cbo{
	
width:300px;
height:350px;
overflow:auto;
background-color: #FFF;
display:none;


	
}

a.resposta_cbo{

padding-left:5px;
padding-top:5px;
text-decoration:none;
padding-bottom:5px;
width:280px;
height:auto;
display:block;
color:#000;
	
}

a.resposta_cbo:hover{

background-color: #00569D;
color:#FFF;
font-weight:bold;

}

</style>

</head>
<body>
	<div id="corpo">
    	<div id="conteudo">  
           <span style="float:left;"><br><a href='../index.php?regiao=<?php echo $id_regiao;?>' class='link'><img src='../imagens/voltar.gif' border=0></a>
        </span>
        <span style="clear:left;"></span>
        
        		 <div class="right"><?php include('../reportar_erro.php'); ?> </div>
       			 <div class="clear"></div>
                 
        		<img src="../imagens/logomaster<?php echo $master?>.gif"/>
				<h3>CADASTRO DE FUNÇÕES <br>(AUTONOMO)</h3>
              
                
                
            <form action='../cadastro2.php' method='post' name='form1' id='form1' onSubmit="return validaForm()">
            <table  border='0' cellpadding='0' cellspacing='0'  align='center' class="relacao" bgcolor="#E5E5E5">
          
            <tr class="titulo_tabela1">
              <td  colspan='4'>Dados da FUNÇÃO</td>
            </tr>
            <tr>
            <td class="secao" align="right">Projeto:</td>
                <td  colspan="3"  align="left">
                <select name='projeto' >
					<?php                    
                    if($id_user == '93') { //BLOQUEIO USUÁRIO CADASTRADOR ITABORAÍ
                            
                            $result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao' AND status_reg = '1' and id_projeto = '3295' ");
                            $row_pro = mysql_fetch_array($result_pro);
                		   print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
					
                        } else {
                            
						$result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao' AND status_reg = '1' ");	
						while ($row_pro = mysql_fetch_array($result_pro)){
							
						print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
                    }
                        }
                    ?>
                    </select>
                    </td>
            </tr>
            <tr>
              <td class="secao" align="right">Nome da Atividade:</td>
              <td colspan='3' align="left">
              		<input name='atividade' type='text' class='campotexto' id='atividade' size='50' 
                    onFocus="document.all.atividade.style.background='#CCFFCC'"
                    onBlur="document.all.atividade.style.background='#FFFFFF'" 
                    style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" />
                </td>
            </tr>
            
        
           
            <tr>
              <td class="secao" align="right">Projeto:</td>
              <td   colspan='3'  align="left"  >
              <select id=tipo name=tipo class='campotexto'>
              <option value=SOE>SOE</option>
              <option value=LATINO>LATINO</option>
              </select>
            </tr>
           
            <tr>
            <td class="secao" align="right">Área:</td>
            <td colspan='3'  align="left">
            <input name='area' type='text' class='campotexto' id='area' size='40' 
            onFocus="document.all.area.style.background='#CCFFCC'"
            onBlur="document.all.area.style.background='#FFFFFF'" 
            onChange="this.value=this.value.toUpperCase()"
            style='background:#FFFFFF;' />
            </td>
            </tr>
            <tr>
            <td class="secao" align="right">Local:</td>
            <td colspan='3'    align="left">
            <input name='local' type='text' class='campotexto' id='local' size='40' 
            onFocus="document.all.local.style.background='#CCFFCC'"
            onBlur="document.all.local.style.background='#FFFFFF'" 
            onChange="this.value=this.value.toUpperCase()"
            style='background:#FFFFFF;' />
            </td>
            </tr>
            <tr>
            <td class="secao" align="right">Inicio:</td>
            <td colspan='3'  align="left" >
            <input name='ini' type='text' id='ini' size='12' class='campotexto' maxlength='10'
            onKeyUp="mascara_data(this); pula(10,this.id,fim.id)"
            onFocus="document.all.ini.style.background='#CCFFCC'" 
            onBlur="document.all.ini.style.background='#FFFFFF'" 
            style='background:#FFFFFF;'>
            </td>
            </tr>
            <tr>
            <td class="secao" align="right">Final:</td>
            <td colspan='3'  align="left"> 
            <input name='fim' type='text' id='fim' size='12' class='campotexto' maxlength='10'
            onKeyUp="mascara_data(this); pula(10,this.id,nome.id)"
            onFocus="document.all.fim.style.background='#CCFFCC'" 
            onBlur="document.all.fim.style.background='#FFFFFF'" 
            style='background:#FFFFFF;'>
            </td>
            </tr>
            <tr>
            <td class="secao" align="right">Valor:</td>
            <td colspan='3'   align="left" >
            <div class='style39'>
            <input name='valor' type='text' id='valor' size='11' class='campotexto' maxlength='13'
            OnKeyDown="FormataValor(this,event,17,2)"
            onFocus="document.all.valor.style.background='#CCFFCC'" 
            onBlur="document.all.valor.style.background='#FFFFFF'" 
            style='background:#FFFFFF;'>
           
            </td>
            </tr>
              <tr>
              <td class="secao" align="right">Quantidade máxima de contratação:</td>
              <td colspan='3' align="left">
              		<input name='qnt_maxima' type='text' class='campotexto' id='qnt_maxima' size='50' 
                    onFocus="document.all.qnt_maxima.style.background='#CCFFCC'"
                    onBlur="document.all.qnt_maxima.style.background='#FFFFFF'" 
                    style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" />
                </td>
           
            <tr>
            
            <tr>
            <td class="secao" align="right">Descrição:</td>
            <td colspan='3'   align="left" >
            <textarea name='descricao' cols='35' rows='5' class='campotexto'  id='descricao'
            onFocus="document.all.descricao.style.background='#CCFFCC'" 
            onBlur="document.all.descricao.style.background='#FFFFFF'" 
            onChange="this.value=this.value.toUpperCase()"
            style='background:#FFFFFF;'></textarea>
            </td>
            </tr>
            </table>
            
            <br>
            
            <center>
            <input type='submit' name='Submit' value='CADASTRAR'>
            </center>
            <input type='hidden' name='id_cadastro' value='12'>
            <input type='hidden' name='regiao' value='<?=$id_regiao?>'>
            <input name='contratacao' type='hidden' id='contratacao' value='1'/> 
            </form>
            
          
            <script>
          
            
            
            function validaForm(){
            
            d = document.form1;
            
           
          
			
			
            if ( d.atividade.value == ""){
            alert("O campo Atividade deve ser preenchido!");
            d.atividade.focus();
            return false;
            }
            
            if ( d.pesquisa_usuario.value == ""  ){
            alert("O campo CBO deve ser preenchido!");
            d.pesquisa_usuario.focus();
            return false;
            }
      
	  		 if ( d.area.value == ""  ){
            alert("O campo ÁREA deve ser preenchido!");
            d.area.focus();
            return false;
            }
			
			 if ( d.local.value == ""  ){
            alert("O campo LOCAL deve ser preenchido!");
            d.area.focus();
            return false;
            }
			
			
			 if ( d.ini.value == ""  ){
            alert("O campo INÍCIO deve ser preenchido!");
            d.ini.focus();
            return false;
            }
			
			if ( d.fim.value == ""  ){
            alert("O campo FINAL deve ser preenchido!");
            d.fim.focus();
            return false;
            }
			
			if ( d.valor.value == ""  ){
            alert("O campo VALOR deve ser preenchido!");
            d.valor.focus();
            return false;
            }		
		
			
			 if (d.qnt_maxima.value == ''){
            alert("O campo \"Quantidade máxima de contratação\" deve ser preenchido!");
            d.qnt_maxima.focus();
            return false;
            }
            
            
            return true;   }
            </script>

			</div>
            </div>
</body>
</html>
<?php
}
?>
