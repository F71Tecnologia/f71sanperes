<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

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
<head><title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<script language="javascript" src="jquery-1.3.2.js"></script>
<script src='ajax.js' type='text/javascript'></script>
<script language="javascript" src='js/ramon.js' type='text/javascript'></script>
<link href='autocomp/css.css' type='text/css' rel='stylesheet'>
<script src='jquery/jquery-1.4.2.min.js' type='text/javascript'></script>
<script src="jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
<script src="jquery/validationEngine/jquery.validationEngine-pt.js" type="text/javascript"></script>
<link  href="jquery/validationEngine/validationEngine.jquery.css" type="text/css" rel="stylesheet"/>
<link  href="adm/css/estrutura.css" type="text/css" rel="stylesheet"/>
<script type="text/javascript">
$(function() {
	
	//$('#form1').validationEngine();
	
});

</script>

</head>
<body>
	<div id="corpo">
    	<div id="conteudo">  
        		 <div class="right"><?php include('reportar_erro.php'); ?></div>
       			 <div class="clear"></div>
                 
        		<img src="imagens/logomaster<?php echo $master?>.gif"/>
				<h3>CADASTRO DE ATIVIDADESS</h3>
              
                
                
            <form action='cadastro2.php' method='post' name='form1' id='form1' onSubmit="return validaForm()">
            <table  border='0' cellpadding='0' cellspacing='0'  align='center' class="relacao" bgcolor="#E5E5E5">
          
            <tr class="titulo_tabela1">
              <td  colspan='4'>Dados do Atividade</td>
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
              <td class="secao" align="right">Tipo Contratação:</td>
              <td colspan='3' align="left">
                    <label class='style39'><input name='contratacao' type='radio' id='contratacao' value='1' 
                onClick="document.all.tabelaclt.style.display = 'none'; document.all.tabelaoutros.style.display = ''; document.all.tabelacbo.style.display = 'none';"/> Autônomo</label><br>   
                    <label class='style39'><input name='contratacao' type='radio' id='contratacao' value='2' 
                onClick="document.all.tabelaclt.style.display = ''; document.all.tabelaoutros.style.display = 'none'; document.all.tabelacbo.style.display = '';"/> CLT</label><br>
                    <label class='style39'><input name='contratacao' type='radio' id='contratacao' value='3' 
                onClick="document.all.tabelaclt.style.display = 'none'; document.all.tabelaoutros.style.display = ''; document.all.tabelacbo.style.display = '';"/> Cooperado</label>
                </td>
            </tr>
            </table>
            
            <br>
            
            
            
            
            
            
            
            
            
            
            
            <table border='0' align='center' cellpadding='0' cellspacing='2' id='tabelacbo' style='display:NONE' class="relacao"  bgcolor="#E5E5E5">
            <tr class="titulo_tabela1">
            <td colspan='2' >CBO</td>
            </tr>
            <tr>
            <td  width='15%'class="secao" align="right">CBO:</td>
            
            <td width='85%'  align="left">
            &nbsp;&nbsp;
            
            <input type='text' name='pesquisa_usuario' SIZE='30' id='pesquisa_usuario' autocomplete='off' 
            onFocus="document.all.pesquisa_usuario.style.background='#CCFFCC'"
            onBlur="document.all.pesquisa_usuario.style.background='#FFFFFF'"
            style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" class="validate[required]">
            
            &nbsp;&nbsp;
            
            <a href='#' onClick="searchSuggest();"><span class='style39'>Procurar</span></a>
            
            <input type='hidden' name='id_cbo' id='id_cbo' maxlength='6'>
            
            </tr>
            <tr>
            <td colspan='2'><div id='ajax'></div></td>
            </tr>
            
            </table>
            
            <br>
            
            <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' id='tabelaclt' style='display:NONE' class="relacao"  bgcolor="#E5E5E5">
            
            <tr class="titulo_tabela1">
            <td colspan='2' >SALÁRIO</td>
            </tr>
            <tr>
            <td width='15%' class="secao" align="right">Salário:</td>
            <td width='85%'  align="left">
            &nbsp;&nbsp; 
            <input name='salario' type='text' class='campotexto' id='salario' size='20' 
            onFocus="document.all.salario.style.background='#CCFFCC'"
            onBlur="document.all.salario.style.background='#FFFFFF'" 
            style='background:#FFFFFF;' OnKeyDown="FormataValor(this,event,17,2)"/>
            &nbsp;&nbsp; 
            <a href='#' onClick="calc();"><span class='style39'>Calcular</span></a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span class='style39' id='resultado'></span>
            </tr>
            
            <tr>
              <td height='30' class="secao" align="right">Mês Abono:</td>
              <td align="left">&nbsp;&nbsp;
               
                <select name='mes_abono' id='mes_abono' class='campotexto'>
                  <option value='01'>Janeiro</option>
                  <option value='02'>Fevereiro</option>
                  <option value='03'>Março</option>
                  <option value='04' selected>Abril</option>
                  <option value='05'>Maio</option>
                  <option value='06'>Junho</option>
                  <option value='07'>Julho</option>
                  <option value='08'>Agosto</option>
                  <option value='09'>Setembro</option>
                  <option value='10'>Outubro</option>
                  <option value='11'>Novembro</option>
                  <option value='12'>Dezembro</option>
                </select>
                &nbsp;&nbsp;
                <input type='hidden' name='enquadramento' id='enquadramento' class='campotexto'>
                </td>
            </tr>
            
            </table>
            
            <br>
            
            <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' id='tabelaoutros'  style='display:none' class="relacao"  bgcolor="#E5E5E5">
            <tr class="titulo_tabela1" >
              <td colspan='4' >DADOS COMPLEMENTARES DA ATIVIDADES</td>
            </tr>
            <tr>
              <td class="secao" align="right">Projeto:</td>
              <td   colspan='3'  align="left"  >
              &nbsp;&nbsp; <select id=tipo name=tipo class='campotexto'>
              <option value=SOE>SOE</option>
              <option value=LATINO>LATINO</option>
              </select>
            </tr>
            <tr>
            <td class="secao" align="right">Nome do Atividade:</td>
            <td colspan='3'   align="left">
            &nbsp;&nbsp; 
            <input name='nome' type='text' class='campotexto' id='nome' size='50' 
            onFocus="document.all.nome.style.background='#CCFFCC'"
            onBlur="document.all.nome.style.background='#FFFFFF'" 
            onChange="this.value=this.value.toUpperCase()"
            style='background:#FFFFFF;' />
            </td>
            </tr>
            <tr>
            <td class="secao" align="right">Área:</td>
            <td colspan='3'  align="left">
            &nbsp;&nbsp; 
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
            &nbsp;&nbsp; 
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
            &nbsp;&nbsp; 
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
            &nbsp;&nbsp; 
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
            <div class='style39'>&nbsp;&nbsp; 
            <input name='valor' type='text' id='valor' size='11' class='campotexto' maxlength='13'
            OnKeyDown="FormataValor(this,event,17,2)"
            onFocus="document.all.valor.style.background='#CCFFCC'" 
            onBlur="document.all.valor.style.background='#FFFFFF'" 
            style='background:#FFFFFF;'>
            &nbsp;&nbsp;&nbsp;&nbsp;
            Parcelas:&nbsp;&nbsp;
            <input name='parcelas' type='text' id='parcelas' size='10' class='campotexto' maxlength='13'
            onFocus="document.all.parcelas.style.background='#CCFFCC'" 
            onBlur="document.all.parcelas.style.background='#FFFFFF'" 
            style='background:#FFFFFF;'>  </div>
            </td>
            </tr>
            <tr>
              <td class="secao" align="right"> Quota:</td>
              <td colspan='3'   align="left"><div class='style39'>&nbsp;&nbsp;
                <input name='quota' type='text' id='quota' size='11' class='campotexto' maxlength='13'
            OnKeyDown="FormataValor(this,event,17,2)"
            onFocus="this.style.background='#CCFFCC'" 
            onBlur="this.style.background='#FFFFFF'" 
            style='background:#FFFFFF;'>
              &nbsp;&nbsp;&nbsp;&nbsp;
                Parcelas das Quotas:&nbsp;&nbsp;
                <input name='p_quotas' type='text' id='p_quotas' size='10' class='campotexto' maxlength='13'
                onFocus="this.style.background='#CCFFCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'>
              </div></td>
            </tr>
            <tr>
            <td class="secao" align="right">Descrição:</td>
            <td colspan='3'   align="left" >
            &nbsp;&nbsp; 
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
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>
            
            
            <script>
            
            function calc(){
            
            var total = "0"
            var valor = document.form1.salario.value
            valor = valor.replace( ".", "" );
            valor = valor.replace( ".", "" );
            valor = valor.replace( ",", "." );
            total = valor*12;
            
            if (total >= 15764.28){
            
            msg = "Declarante de IR, pois o salário anual é: "+total+"!";
            document.all.enquadramento.value = '1';
            
            } else {
            
            msg = "NÃO Declarante de IR, pois o salário anual é: "+total+"!";
            document.all.enquadramento.value = '0';
            
            }
            
            document.getElementById('resultado').innerText=msg;
            
            }
            
            
            function validaForm(){
            
            d = document.form1;
            
            if (document.form1.contratacao[1].checked && d.enquadramento.value == ""){
            alert("ATENÇÃO, se o tipo de contratação for CLT é nescessário calcular o SALÁRIO!");
            d.salario.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.atividade.value == ""){
            alert("O campo Atividade deve ser preenchido!");
            d.atividade.focus();
            return false;
            }
            
            if (!document.form1.contratacao[1].checked && d.pesquisa_usuario.value == ""  && !(document.form1.contratacao[0].checked) ){
            alert("O campo CBO deve ser preenchido!");
            d.pesquisa_usuario.focus();
            return false;
            }
            
            if ( !document.form1.contratacao[2].checked && d.pesquisa_usuario.value == "" && !(document.form1.contratacao[0].checked)){
            alert("O campo CBO deve ser preenchido!");
            d.pesquisa_usuario.focus();
            return false;
            }
            
            
            if (!document.form1.contratacao[1].checked && d.nome.value == ""){
            alert("O campo Nome deve ser preenchido!");
            d.nome.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.area.value == ""){
            alert("O campo Área deve ser preenchido!");
            d.area.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.local.value == ""){
            alert("O campo Local deve ser preenchido!");
            d.local.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.ini.value == ""){
            alert("O campo Inicio deve ser preenchido!");
            d.ini.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.fim.value == ""){
            alert("O campo Término deve ser preenchido!" + d.valor.value);
            d.fim.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.valor.value == "" ){
            alert("O campo Valor deve ser preenchido!");
            d.valor.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.valor.value < "1" ){
            alert("O campo Valor não pode ser Zero!");
            d.valor.focus();
            return false;
            }
            
            if (!document.form1.contratacao[1].checked && d.parcela.value == "" ){
            alert("O campo Parcela deve ser preenchido!");
            d.parcela.focus();
            return false;
            }
            if (!document.form1.contratacao[1].checked && d.descricao.value == "" ){
            alert("O campo Descrição deve ser preenchido!");
            d.descricao.focus();
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
