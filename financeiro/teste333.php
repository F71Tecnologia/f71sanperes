<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Testando</title>
<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	margin:0;
}
#topo{
	position:fixed;
	top:0px;
	background-color:#F60;
	z-index:1000;
	width:100%;
	height:120px;
}
#conteudo {
	position:relative; 
	top:80px;
}
*html #conteudo {
	top:0px;
}
</style>
</head>
<body>
<div id="topo">
<?php 
	$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
	$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
?>
	<table width="998">
    	<tr>
    	  <td width="110" rowspan="2">
                  <img src="../imagens/logomaster<?=@mysql_result($query_master,0)?>.gif" width="110" height="79"></td>
              <td align="left" valign="top"><br />
                Data:&nbsp;<strong><?=date("d/m/Y");?></strong>&nbsp;<br />
        voc&ecirc; est&aacute; visualizando a Regi&atilde;o:&nbsp;<strong><?=@mysql_result($query_regiao,0);?></strong></td>
        	<td><br />
            	                    <?php 
        $usuarios_permitidos = array('75','5','9','27','64','77');
        if(in_array($id_user,$usuarios_permitidos)):
        $qr_id_master = mysql_query("SELECT id_master FROM funcionario WHERE id_funcionario = '$id_user'");
        $id_master = mysql_result($qr_id_master,0);
        ?>
        <!--Controle de regiao -->
        <div>
        <form name="formRegiao" id="formRegiao" method="get">
        <table>
        <tr>
            <td><span style="color:#000">Região</span></td>
            <td><select name="regiao" onchange="MM_jumpMenu('parent',this,0)">
            <?php
            $qr_selecao_regiao = mysql_query("SELECT * FROM regioes WHERE status = '1' AND id_master = '$row_user[id_master]'");
            while($row_selecao_regiao = mysql_fetch_assoc($qr_selecao_regiao)){
                if($_GET['regiao'] == $row_selecao_regiao['id_regiao']) {
                print "<option selected=\"selected\" value=\"?regiao=$row_selecao_regiao[id_regiao]\">$row_selecao_regiao[id_regiao] - $row_selecao_regiao[regiao]</option>";
                } else {
                    print "<option value=\"?regiao=$row_selecao_regiao[id_regiao]\">$row_selecao_regiao[id_regiao] - $row_selecao_regiao[regiao]</option>";
                }
            }
            ?>
                </select>
            </td>
        </tr>
        </table>
        </form>
        </div>
           <!--Controle de regiao -->
        <?php endif;// Fim do if de controle de regiao ?>
            </td>
          </tr>
           
    </table>
</div>







<table width="100%" border="0" cellpadding="0" cellspacing="0"  id="conteudo"> 
  <tr>
    
    <td colspan="2" bgcolor="#FFFFFF"><div align="center" class="style3">
      <div align="center"><img src="imagensfinanceiro/relatafin.gif" alt="fornecedor" width="25" height="25" align="absmiddle">&nbsp;<span class="style31">RELATO&#769;RIOS FINANCEIROS</span></div>
    </div></td>
    
  </tr>
  <tr>
    <td height="26" align="center" valign="middle" bgcolor="#E8E8E8">      
    <div align="center" class="style6"> 
        <div align="left">&nbsp;&nbsp;<span class="style2"><img src="imagensfinanceiro/Add-25.png" alt="fornecedor" width="25" height="25" align="absmiddle">&nbsp;<span class="style3">ENTRADAS</span></span></div>
      </div></td>
    <td height="26" valign="middle" bgcolor="#E8E8E8"><div align="left"><span class="style2"> &nbsp;&nbsp;<img src="imagensfinanceiro/Delete-25.png" alt="fornecedor" width="25" height="25" align="absmiddle"><span class="style31">&nbsp;</span></span><span class="style3">SAI&#769;DAS</span></div></td>
  </tr>
  <tr>
    <td height="16" valign="top">
    <form action="relfinanceiro2.php" method="post" name="for1">
    &nbsp;&nbsp;&nbsp;&nbsp;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o Projeto:</span>
    <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] -  $row_projeto[nome] </option>";
}
print "</select>";
?>
    <br>
    &nbsp;&nbsp;&nbsp;&nbsp;<br>
    &nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar Entradas de 
    <input name="data_ini" type="text" id="data_ini" size="10" OnKeyUp="mascara_data(this)" maxlength='10' class='textarea2'>
    at&eacute; 
    <input name="data_fim" type="text" id="data_fim" size="10" OnKeyUp="mascara_data(this)" maxlength='10' class='textarea2'>
     </span><span class="style12 style29"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
     <input type="submit" value="Gerar">
<input type="hidden" value="1" name="id">
<input type="hidden" value="<?=$regiao;?>" name="regiao">
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
    </form>   
    </td>
    <td height="16" valign="top"><form action="relfinanceiro2.php" method="post" name="for2">
  &nbsp;&nbsp;&nbsp;&nbsp;<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o Projeto:</span>
  <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome] </option>";
}
print "</select>";
?>
  <br>
  &nbsp;&nbsp;&nbsp;&nbsp;<br>
  &nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar Sa&iacute;das de
  <input name="data_ini2" type="text" id="data_ini2" size="10" OnKeyUp="mascara_data(this)" maxlength='10' class='textarea2'>
    at&eacute;
  <input name="data_fim2" type="text" id="data_fim2" size="10" OnKeyUp="mascara_data(this)" maxlength='10' class='textarea2'>
  </span><span class="style12 style29"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
  <input type="submit" value="Gerar">
  <input type="hidden" value="2" name="id2">
  <input type="hidden" value="<?=$regiao;?>" name="regiao3">
  <br>
    </form></td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" valign="top"><br>
<hr>
<form action="relfinanceiro2.php" method="post" name="for5">
  <span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="relfinanceiro2.php?id=5&regiao=<?=$regiao;?>&select=1" class="style12 style29" style="TEXT-DECORATION: none;"></a>Selecione o Projeto:</span>
  <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome] </option>";
}
print "</select>";
?>
  <br>
  <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="relfinanceiro2.php?id=5&regiao=<?=$regiao;?>&select=1" class="style12 style29" style="TEXT-DECORATION: none;">
<label>
Visualizar lan&ccedil;amentos n&atilde;o pagos&nbsp;&nbsp;
<input type="radio" name="select" id="select" value="1">
</label>
<br>
</a>
<br><span class="style12 style29">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label>
Visualizar lan&ccedil;amentos futuros&nbsp;&nbsp;
<input type="radio" name="select" id="select" value='2'>
</label></span>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="Gerar">
<input type="hidden" value="5" name="id">
<input type="hidden" value="<?=$regiao;?>" name="regiao2">
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
</form>
</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top" bgcolor="#E8E8E8">
    <div align="left">&nbsp;&nbsp;<span class="style2"><img src="imagensfinanceiro/caixa.gif" alt="fornecedor" width="25" height="25" align="absmiddle">&nbsp;<span class="style3">CAIXA</span></span></div></td>
  </tr>
    <tr>
    <td height="18" colspan="2" valign="top">
    <form action="relfinanceiro2.php" method="post" name="for3">
      &nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o Projeto:</span>
<?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto' class='textarea2'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]> $row_projeto[0] - $row_projeto[nome] </option>";
}
print "</select>";
?>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Visualizar Entradas de
<input name="data_ini" type="text" id="data_ini" size="10" OnKeyUp="mascara_data(this)" maxlength='10' class='textarea2'>
at&eacute;
<input name="data_fim" type="text" id="data_fim" size="10" OnKeyUp="mascara_data(this)" maxlength='10' class='textarea2'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
<input type="submit" value="Gerar">
<input type="hidden" value="3" name="id">
<input type="hidden" value="<?=$regiao;?>" name="regiao">
<br><br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
    </form></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top" bgcolor="#E8E8E8">
    <div align="left">&nbsp;&nbsp;<span class="style9">&nbsp;<span class="style2"><img src="imagensfinanceiro/Add-25.png" alt="fornecedor" width="25" height="25" align="absmiddle"><span class="style31"><img src="imagensfinanceiro/Delete-25.png" alt="fornecedor" width="25" height="25" align="absmiddle"></span>&nbsp;</span></span><span class="style3"> ENTRADAS E SAI&#769;DAS</span></div></td>
  </tr>
  <tr>
    <td height="32" colspan="2" align="left" valign="middle">&nbsp;</td>
  </tr>
  <tr>
    <td height="32" colspan="2" align="left" valign="middle">
    <form action="relfinanceiro2.php" method="post" name="for4">
      &nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione a Conta:</span>
<?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and interno ='1' AND status_reg = '1'");
print "<select name='banco' class='textarea2'>";
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
}
print "</select>";
?><br>
&nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Ou marque aqui para exibir todas as contas:</span>
<input type="checkbox" name="todas_contas" id="todas_contas" value="1">
&nbsp;&nbsp;&nbsp;&nbsp;<br>
 &nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style12 style29">Selecione o m&ecirc;s e o ano de refer&ecirc;ncia:&nbsp;&nbsp;&nbsp; 
<select name="mes" id="mes" class='textarea2'>
  <option value="01">Janeiro</option>
  <option value="02">Fevereiro</option>
  <option value="03">Mar&ccedil;o</option>
  <option value="04">Abril</option>
  <option value="05">Maio</option>
  <option value="06">Junho</option>
  <option value="07">Julho</option>
  <option value="08">Agosto</option>
  <option value="09">Setembro</option>
  <option value="10">Outubro</option>
  <option value="11">Novembro</option>
  <option value="12">Dezembro</option>
</select>
</span>
 <span class="style12 style29">
 &nbsp;&nbsp;&nbsp;&nbsp;
 <select name="ano" id="ano" class='textarea2'>
   <option>2005</option>
   <option>2006</option>
   <option>2007</option>
   <option>2008</option>
   <option selected>2009</option>
   <option>2010</option>
   <option>2011</option>
   <option>2012</option>
   <option>2013</option>
   <option>2014</option>
   </select>
 </span>
 <label></label>
 <span class="style12 style29"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
 <input type="submit" value="Gerar">
 <input type="hidden" value="4" name="id">
 <input type="hidden" value="<?=$regiao;?>" name="regiao">
 <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</form>
    <hr>
<div align="left" style="background-color:#FFF">
  <form action="abastecimento.php" method="post" name="formabas">
  <p><span class="style31">&nbsp;&nbsp;<img src="../imagensmenu2/c1.gif" alt="cobrinha" width="20" height="14" align="absmiddle">&nbsp;RELAT&Oacute;RIOS DE ABASTECIMENTOS</span></p>
  <p align="center"><span class="style12 style29">
    <label>Marque para ver o relat&oacute;rio anual:&nbsp;
      <input type="checkbox" name="anotodo" id="anotodo" value="1" onClick="document.formabas.mes.style.display = (document.formabas.mes.style.display == 'none') ? '' : 'none' ;"></label>
    <br>
    <br>
    &nbsp;
      <select name="mes" id="mes" class='textarea2'>
        <option value="01">Janeiro</option>
        <option value="02">Fevereiro</option>
        <option value="03">Mar&ccedil;o</option>
        <option value="04">Abril</option>
        <option value="05">Maio</option>
        <option value="06">Junho</option>
        <option value="07">Julho</option>
        <option value="08">Agosto</option>
        <option value="09">Setembro</option>
        <option value="10">Outubro</option>
        <option value="11">Novembro</option>
        <option value="12">Dezembro</option>
      </select>
&nbsp;&nbsp;&nbsp;&nbsp;
<select name="ano" id="ano" class='textarea2'>
  <option>2005</option>
  <option>2006</option>
  <option>2007</option>
  <option>2008</option>
  <option selected>2009</option>
  <option>2010</option>
  <option>2011</option>
  <option>2012</option>
  <option>2013</option>
  <option>2014</option>
</select>
<br>
<br>
</span>
    <input type="submit" value="Visualizar Relat&oacute;rio">
    <br>
  </p>
  </form></div>
<hr>
<p><br>
</p>
<div align="left" style="background-color:#FFF">
  <p><span class="style31">&nbsp;&nbsp;<img src="../imagensmenu2/gestao.gif" alt="cobrinha" width="20" height="22" align="absmiddle">&nbsp;RELAT&Oacute;RIOS DE FECHAMENTO</span></p>
  <p align="center">
<A onClick="MM_openBrWindow('../relsdescritivodetalhado.php?regiao=<?=$regiao;?>','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><IMG alt="rel" src="../imagens/ver_detalhado.gif" align="middle" border="0"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<A onClick="MM_openBrWindow('../reldescritivo.php?regiao=<?=$regiao;?>','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><IMG alt="rel" src="../imagens/ver_descritivo.gif" align="middle" border="0"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A onClick="MM_openBrWindow('../reldescritivoanual.php?regiao=<?=$regiao;?>','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><IMG alt="rel" src="../imagens/ver_anual.gif" align="middle" border="0"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A onClick="MM_openBrWindow('../reldesempenho.php?regiao=<?=$regiao;?>&id=1','','scrollbars=yes,resizable=yes,width=770,height=550')" href="#"><IMG alt="rel" src="../imagens/ver_desempenho.gif" align="middle" border="0"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
&nbsp;<br>
  </p>
  </div>
    </form>
<hr>
<?php 
// Relatorio financeiro por grupo criado por maikom 
// filtro de usuarios
$usuarios = array('5','77','9','64','27','75');
if(in_array($id_user,$usuarios)):?>
<div id="geral">
	<h1>RELATORIO GERENCIAL</h1>
    <form action="../novoFinanceiro/relatorio.gerencial.php" method="get" name="relatorio">
    <table align="center">
    	<tr>
        	<td align="center">&nbsp;</td>
            <td align="center">M&ecirc;s</td>
            <td align="center">Ano</td>
            <td align="center">Projeto</td>
            </tr>
        <tr>
        	<td>&nbsp;</td>
            <td>
              <select name="mes" id="mes">
              <?php
			  $query_mes = mysql_query("SELECT * FROM  ano_meses ORDER BY num_mes");
			  while($row_mes = mysql_fetch_assoc($query_mes)){
				  if($row_mes['num_mes'] == date('m'))
				  	echo '<option value="'.$row_mes['num_mes'].'" selected="selected">'.$row_mes['nome_mes'].'</option>';
				  else
				  	echo '<option value="'.$row_mes['num_mes'].'" >'.$row_mes['nome_mes'].'</option>';
			  }
			   ?>
              </select>
            </td>
            <td>
              <select name="ano" id="ano">
              <?php 
				$ano = array(2008,2009,2010,2011,2012);
				foreach($ano as $an){
					if($an == date('Y'))
						echo '<option value="'.$an.'" selected="selected">'.$an.'</option>';
					else
					 	echo '<option value="'.$an.'" >'.$an.'</option>';
				}
			  ?>
              </select>
            </td>
            <td>
              <select name="projeto" id="projeto">
              <?php 
			  $query_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
				while($row_projeto = mysql_fetch_array($query_projeto)){
					echo '<option value="'.$row_projeto[0].'">'.$row_projeto[0].' - '.$row_projeto['nome'].'</option>';
				}
				?>
              </select>
            </td>
            </tr>
        <tr>
        	<td colspan="4" align="center"><input type="submit" name="button" id="button" value="       GERAR RELATORIO       "></td>
            </tr>
    </table>
    </form>
</div>
<?php endif; ?>
<?php
//BLOQUEIO PAULO MONTEIRO SJR 16-03 - 17hs
if($id_user != '73') {
?>
        
<div align="left" style="background-color:#FFF"><span class="style9">&nbsp;&nbsp;<span class="style2"><img src="../imagensfinanceiro/contas.gif" alt="contas" width="25" height="25" align="absmiddle" />&nbsp;</span></span><span class="style3"> CONTROLE DE SALDOS</span></div>
    <br>
    <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1" >
      <tr class="linha_um"> 
            <td width="5%" bgcolor="#333333"><div align="center" class="style27">CÓD</div></td>
            <td width="25%" bgcolor="#333333"><div align="center" class="style27">BANCO</div></td>
            <td width="9%" bgcolor="#333333"><div align="center" class="style27">AG</div></td>
            <td width="10%" bgcolor="#333333"><div align="center" class="style27">CC</div></td>
            <td width="30%" bgcolor="#333333"><div align="center" class="style27">PROJETO</div></td>
            <td width="17%" bgcolor="#333333"><div align="center" class="style27">SALDO PARCIAL </div></td>
        </tr>
		  <?php
		  $cont = "0";
		  $div = "<div align='center' class='style24'>";
		  //1 - ramon
		  //5 - fabio
		  //9 - sabino
		  //27 - silvania
		  //32 - renato
		  //75 -  Maikom james
		  //$id_user == '1' or 
		  if($id_user == '64' or $id_user == '5' or $id_user == '9' or $id_user == '27' or $id_user == '77' or $id_user == '75'){
			  $RERegioes = mysql_query("SELECT * FROM regioes Where id_master = '$row_master[0]' and status='1'");
			  
			  while($RowRegioes = mysql_fetch_array($RERegioes)){
				  $REBancos = mysql_query("SELECT * FROM bancos where id_regiao = '$RowRegioes[0]' and interno ='1' AND status_reg = '1'");
				  $NumBancos = mysql_num_rows($REBancos);
				  
				  if($NumBancos != 0){
				  //SÓ VAI PRINTAR ESSAS INFORMAÇÕES SE A REGIÃO SELECIONADA TIVER DIFERENTE DE 0
				  echo "<tr bgcolor='#666666'>";
				  echo "<td colspan='6' width='5%' align='center' $bord><div style='font-size:14px; color:#FFF'><b>$RowRegioes[regiao]</b></div></td>";
				  echo "</tr>";
				  
				  while($RowBancos = mysql_fetch_array($REBancos)){
					  
					  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
						  $REProjeto = mysql_query("SELECT * FROM projeto where id_projeto = '$RowBancos[id_projeto]' AND status_reg = '1'");
						  $RowProjeto = mysql_fetch_array($REProjeto);
			  
						  $ValorBanc = str_replace(",", ".", $RowBancos['saldo']);
			  			  $ValorBancF = number_format($ValorBanc,2,",",".");
						  
						  echo "<tr bgcolor='$color'>";
						  echo "<td width='5%' $bord>$div $RowBancos[id_banco]</div></td>";
						  echo "<td width='25%' $bord>$div $RowBancos[nome]</div></td>";
						  echo "<td width='9%' $bord>$div $RowBancos[agencia]</div></td>";
						  echo "<td width='10%' $bord>$div $RowBancos[conta]</div></td>";
						  echo "<td width='30%' $bord>$div $RowProjeto[nome]&nbsp;</div></td>";
						  echo "<td width='17%' $bord>$div $ValorBancF </div></td>";
						  echo "</tr>";
		  
						  $cont ++;
				  	  }
				    
			
				  }// SÓ VAI RODAR ISSO AE EM CIMA, SE TIVER BANCO NA REGIAO
				  
			  }
			  
		  }else{
			  $REBanc = mysql_query("SELECT * FROM bancos where id_regiao='$regiao' and interno ='1' AND status_reg = '1'");
			  while($RowBanc = mysql_fetch_array($REBanc)){
				  
				  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
				  $REProjeto = mysql_query("SELECT * FROM projeto where id_projeto = '$RowBanc[id_projeto]' AND status_reg = '1'");
				  $RowProjeto = mysql_fetch_array($REProjeto);
			  
				  $ValorBanc = str_replace(",", ".", $RowBanc['saldo']);
				  $ValorBancF = number_format($ValorBanc,2,",",".");
						  
				  echo "<tr bgcolor='$color'>";
				  echo "<td width='5%' $bord>$div $RowBanc[id_banco]</div></td>";
				  echo "<td width='25%' $bord>$div $RowBanc[nome]</div></td>";
				  echo "<td width='9%' $bord>$div $RowBanc[agencia]</div></td>";
				  echo "<td width='10%' $bord>$div $RowBanc[conta]</div></td>";
				  echo "<td width='30%' $bord>$div $RowProjeto[nome]&nbsp;</div></td>";
				  echo "<td width='17%' $bord>$div $ValorBancF </div></td>";
				  echo "</tr>";
		  
				  $cont ++;
			  }
		  }
		  
		  
		  ?>
      </table>  
	  <?php 
	  }  
		  ?>
	  
    <br></td>
  </tr>
  
  <tr>
    <td width="499"></td>
    <td></td>
  </tr>
  <tr>
    <td align="center" valign="middle" class="style3"></td>
    <td width="499" align="center" valign="middle" class="style3"></td>
  </tr>
  <tr valign="top">
    <td height="18" colspan="4" bgcolor="#999999">
<?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table> 
</body>
</html>