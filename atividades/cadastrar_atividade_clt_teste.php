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

$ano_atual = date('Y');
$qr_ir = mysql_query("SELECT * , MIN( v_ini )
					FROM rh_movimentos
					WHERE cod = '5021'
					AND anobase = '$ano_atual'");

$row_ir = mysql_fetch_assoc($qr_ir);


$id_regiao = $_REQUEST['regiao'];
?>
<html>
<head><title>:: Intranet ::- CADASTRO DE FUN&Ccedil;&Otilde;ES(CLT)</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<script language="javascript" src="jquery-1.3.2.js"></script>
<script language="javascript" src='../js/ramon.js' type='text/javascript'></script>
<script language="javascript" src='ajax.js' type='text/javascript'></script>

<script src='../jquery/jquery-1.4.2.min.js' type='text/javascript'></script>
<script src="../jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>

<script type="text/javascript" src="../jquery/jquery-autocomplete/lib/jquery.js"></script>
<script type="text/javascript" src="../jquery/jquery-autocomplete/lib/jquery.bgiframe.min.js"></script>
<script type="text/javascript" src="../jquery/jquery-autocomplete/lib/jquery.ajaxQueue.js"></script>
<script type="text/javascript" src="../jquery/jquery-autocomplete/lib/thickbox-compressed.js"></script>
<script type="text/javascript" src="../jquery/jquery-autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery/jquery-autocomplete/jquery.autocomplete.css"/>
<link rel="stylesheet" type="text/css" href="../jquery/query-autocomplete/lib/thickbox.css"/>


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
	top: 265px;
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

        		 <div class="right"><?php include('../reportar_erro.php'); ?></div>
       			 <div class="clear"></div>

        		<img src="../imagens/logomaster<?php echo $master?>.gif"/>
				<h3>CADASTRO DE FUNÇÕES <br>(CLT)</h3>



            <form action='../cadastro2_teste.php' method='post' name='form1' id='form1' onSubmit="return validaForm()">
            <table  border='0' cellpadding='0' cellspacing='0'  align='center' class="relacao" bgcolor="#E5E5E5">

            <tr class="titulo_tabela1">
              <td  colspan='4'>Dados da FUNÇÃO</td>
            </tr>
            <tr>
            <td class="secao" align="right">Projeto:</td>
                <td  colspan="3"  align="left">
                <select name='projeto'>
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
              		<input name='atividade' type='text' class='campotexto' id='atividade' size='60'
                    onFocus="document.all.atividade.style.background='#CCFFCC'"
                    onBlur="document.all.atividade.style.background='#FFFFFF'"
                    style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" />
                </td>

            <tr>
            <td  width='15%' class="secao" align="right" valign="top">CBO:</td>

            <td width='85%'  align="left">


            <input type='text' name='pesquisa_usuario' SIZE='100' id='pesquisa_usuario' autocomplete='off'
            style='background:#FFFFFF;'  class="validate[required]" > <!---onKeypress="searchSuggest();"-->
            <div id="lista_cbo"></div>




            <input type='hidden' name='id_cbo' id='id_cbo' maxlength='6'>

            </tr>
            <tr>
            <td colspan='4'><div id='ajax'></div></td>
            </tr>

            <tr>
            <td width='15%' class="secao" align="right">Salário:</td>
            <td width='85%'  align="left" colspan='3'>

            <input name='salario' type='text' class='campotexto' id='salario' size='20'
            onFocus="document.all.salario.style.background='#CCFFCC'"

            style='background:#FFFFFF;' OnKeyDown="FormataValor(this,event,17,2)"/>

           &nbsp;&nbsp;
          <span class='style39' id='resultado'></span>
            </tr>

            <tr>
              <td class="secao" align="right">Mês Abono:</td>
              <td align="left" colspan='4'>

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

             <tr>
              <td class="secao" align="right">Quantidade máxima de contratação:</td>
              <td colspan='3' align="left">
              		<input name='qnt_maxima' type='text' class='campotexto' id='qnt_maxima' size='50'
                    onFocus="document.all.qnt_maxima.style.background='#CCFFCC'"
                    onBlur="document.all.qnt_maxima.style.background='#FFFFFF'"
                    style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" />
                </td>

            <tr>
             <tr  class="titulo_tabela1">
            <td colspan="4">	DADOS DO HOR&Aacute;RIO</td>
          </tr>

          <tr>
              <td width="14%"  class="secao">Nome do horário</td>
              <td height="35" colspan="5">
                <input name="nome_horario" type="text" id="nome_horario" size="80" class="warningmsg"
                     onChange="this.value=this.value.toUpperCase()"></td>
          </tr>

            <tr>
                  <td  class="secao">Observa&ccedil;&otilde;es:</td>
                  <td  colspan="5">
                    <input name="obs" type="text" id="obs" size="80" class="okmsg" onChange="this.value=this.value.toUpperCase()"></td>
                </tr>


               <tr>
                  <td  class="secao">Preenchimento:</td>
                      <td colspan="3">
                              <table width="100%" border="1" class="relacao">
                                <tr>
                                  <td align="center" bgcolor="#CCCCCC" ><strong>Entrada </strong></td>
                                  <td align="center" bgcolor="#CCCCCC" ><strong>Sa&iacute;da almo&ccedil;o</strong></td>
                                  <td align="center" bgcolor="#CCCCCC" ><strong>Retorno almo&ccedil;o</strong></td>
                                  <td align="center" bgcolor="#CCCCCC" ><strong>Sa&iacute;da</strong></td>
                               </tr>
                                <tr>
                                  <td height="26" align="center" bgcolor="#CCCCCC"><input name="entrada1" type="text" id="entrada1" size="10" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida1.id)" maxlength="8"
            onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                                  <td align="center" bgcolor="#CCCCCC"><input name="saida1" type="text" id="saida1" size="10" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,entrada2.id)" maxlength="8"
            onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                                  <td align="center" bgcolor="#CCCCCC"><input name="entrada2" type="text" id="entrada2" size="10" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida2.id)" maxlength="8"
            onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                                  <td align="center" bgcolor="#CCCCCC"><input name="saida2" type="text" id="saida2" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,hora_mes.id)" size="10" maxlength="8"
            onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                                </tr>
                              </table>
                      </td>
                </tr>

               <tr>
                	<td  class="secao">Horas M&ecirc;s:</td>
                    <td align="left" colspan="3">
                    <input name="hora_mes" type="text" id="hora_mes" size="10"
                      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
               </tr>
               <tr>
                    <td class="secao">Dias M&ecirc;s:</td>
                    <td align="left" colspan="3">
                      <input name="dias_mes" type="text" size="10"
                      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                </tr>

             <tr>

                  <td width="15%">Dias Semana:</td>
                  <td colspan="3" align="left">
                    <input name="dias_semana" type="text" id="dias_semana" size="10"
                      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
            </tr>
             <tr>
             <td width="7%">Folgas:</td>
              <td align="left" valign="middle" colspan="3">
                <label>
                  <input name="folga1" type="checkbox" id="folga1" value="1">
                  S&aacute;bado</label>
                /
                <label>
                  <input name="folga2" type="checkbox" id="folga2" value="2">
                  Domingo</label>
                /
                <label>
                  <input name="folga3" type="checkbox" id="folga3" value="5">
                  Plantonista</label>
                    </td>
            </tr>

         </table>

          <table  border='0' cellpadding='0' cellspacing='0'  align='center' class="relacao" bgcolor="#E5E5E5">

              <br>
              <div align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo">
                  <tr>
                    <td width="15%" align="right"><span class="secao">SELECIONE:</span></td>
                    <td width="85%"><span class="secao"> &nbsp;&nbsp;
                      <input name="arquivo" type="file" id="arquivo" size="60" />
                    </span></td>
                  </tr>
                </table>
                <p><br>

                  <input type="submit" name="gerar" id="gerar" value="CADASTRAR">
                    <input name='contratacao' type='hidden' id='contratacao' value='2' />
                    <input type='hidden' name='id_cadastro' value='12'>
                    <input type='hidden' name='regiao' value='<?=$id_regiao?>'>

                  <br>
                  <br>
                </p>
              </div>
          </td>
          </tr>
        </table>

            </form>

            <script>

            function calc(){

            var total = "0"
            var valor = document.form1.salario.value
            valor = valor.replace( ".", "" );
            valor = valor.replace( ".", "" );
            valor = valor.replace( ",", "." );
            total = valor*12;

            if (total >= <?php echo $row_ir['v_ini']*12;?>){

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

            if (  d.pesquisa_usuario.value == "" ){
            alert("O campo CBO deve ser preenchido!");
            d.pesquisa_usuario.focus();
            return false;
            }


			if (  d.mes_abono.value == "" ){
            alert("O campo MÊS ABONO deve ser preenchido!");
            d.mes_abono.focus();
            return false;
            }

            if (d.enquadramento.value == ""){
            alert("ATENÇÃO, se o tipo de contratação for CLT é nescessário calcular o SALÁRIO!");
            d.salario.focus();
            return false;
            }
			 if (d.qnt_maxima.value == ''){
            alert("O campo \"Quantidade máxima de contratação\" deve ser preenchido!");
            d.qnt_maxima.focus();
            return false;
            }


			 if (d.nome_horario.value == ''){
            alert("O campo NOME DO HORÁRIO deve ser preenchido");
            d.nome_horario.focus();
            return false;
            }

			 if (d.entrada1.value == ''){
            alert("O campo ENTRADA deve ser preenchido");
            d.entrada1.focus();
            return false;
            }

			 if (d.saida1.value == ''){
            alert("O campo SAÍDA ALMOÇO deve ser preenchido");
            d.saida1.focus();
            return false;
            }


			 if (d.entrada2.value == ''){
            alert("O campo RETORNO ALMOÇO deve ser preenchido");
            d.entrada2.focus();
            return false;
            }

			 if (d.saida2.value == ''){
            alert("O campo SAÍDA deve ser preenchido");
            d.saida2.focus();
            return false;
            }

			 if (d.hora_mes.value == ''){
            alert("O campo HORA MÊS deve ser preenchido");
            d.hora_mes.focus();
            return false;
            }


			 if (d.dias_mes.value == ''){
            alert("O campo DIAS MÊS deve ser preenchido");
            d.dias_mes.focus();
            return false;
            }

		    if (d.dias_semana.value == ''){
            alert("O campo DIAS SEMANA deve ser preenchido");
            d.dias_semana.focus();
            return false;
            }

		    }
            </script>

			</div>
            </div>
</body>
</html>
<?php
}
?>
