<?php
include "conn.php";
include "funcoes.php";
if(empty($_COOKIE['logado3'])){
	header('location: login_rh.php?entre=true');
}

$id_user = $_COOKIE['logado'];
$regiao = $_GET['regiao'];
$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);
$query_funcionario = mysql_query("SELECT id_funcionario, nome FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario = mysql_fetch_array($query_funcionario);
$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
$id_master = @mysql_result($query_master,0);



//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkfo = encrypt("$regiao&1"); 
$linkfo = str_replace("+","--",$linkfo);
// -----------------------------

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkevento = encrypt("$regiao"); 
$linkevento = str_replace("+","--",$linkevento);

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkferias = encrypt("$regiao&1"); 
$linkferias = str_replace("+","--",$linkferias);
// -----------------------------


/*Resumo*/
$result_cont_total_geral = mysql_query("SELECT id_clt FROM rh_clt where id_regiao = '$regiao'");
$row_cont_total_geral = mysql_num_rows($result_cont_total_geral);

$result_sexo_m = mysql_query("SELECT * FROM rh_clt where sexo = 'M' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_m = mysql_num_rows($result_sexo_m);

$result_sexo_f = mysql_query("SELECT * FROM rh_clt where sexo = 'F' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_f = mysql_num_rows($result_sexo_f);

$data_antiga = date("Y-m-d", mktime (0, 0, 0, $mes  , $dia - 90, $ano));
/*Resumo*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o de RH</title> 
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" >
$(function(){
	$('#botoes ul li img').fadeTo('fast', 0.7).hover(function(){$(this).fadeTo('fast', 1.0)},function(){$(this).fadeTo('fast', 0.7)});
	$("#resumo table tr:odd").addClass('linha_dois');
	$("#resumo table tr:even").addClass('linha_um');
	$('#resumo').find('table').find('tr:first').addClass('titulo_table');
});

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}
</script>
<link rel="stylesheet" type="text/css" href="novoFinanceiro/style/form.css"/>
<style type="text/css">
body{
	text-align:center; background-color:#F2F2F2; font-family:Arial, Helvetica, sans-serif;  margin:0px;
}

#base {
	text-align:left;
	width:998px;
	background-color:#FFF;
	overflow:hidden;
	position: relative;
	margin-top: 80px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-right-color: #CCC;
	border-bottom-color: #CCC;
	border-left-color: #CCC;
}

#botoes {
	padding: 15px;
}
#botoes img{
	border:none;
}
#botoes ul{
	margin:0px 10px; padding: 0px; list-style-type:none !important;
}
#botoes ul li{
	float:left; 
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	margin:2px;
}

#botoes ul li:hover{
	background-color:#EBEBEB;
}
#botoes ul li.titulos{
	height:30px;
	padding-top: 10px;
	text-align:center;
	background-color:#C1C1C1;
	float:none;
	-moz-border-radius: 8px;
	border-radius: 8px;
	text-shadow: #6374AB;
	font-weight: bold;
	text-shadow: #FFF 1px 1px 1px;
}
.clear{
	clear:both;
}

#topo {
	width: 100%;
	position: fixed;
	z-index: 100;
	height: 80px;
	top:0px;
	text-align:center;	
}
#dentro_topo{
	font-size:12px;
	text-align:left;
	width:998px;
	height:80px;
	background-color:#FFF;
	border-top-width: 1px;
	border-right-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-top-color: #CCC;
	border-right-color: #CCC;
	border-left-width: 1px;
	border-left-style: solid;
	border-left-color: #CCC;
	margin-top: 0px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
}
span{
	font-weight:bold;
}
span.nome{
	color:#F00000;
}

#resumo {
	text-align:center; margin:10px;
}

#resumo table {
	text-align:left;
	border:solid #999 1px;
	margin:10px auto;
	width:100%;
	font-size:12px;
}
.linha_um {
 background-color:#FFF;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}
.linha_um:hover , .linha_dois:hover{
	background-color:#666;
	color:#FFF;
}

.titulo_table {
	font-size:13px;
	text-align:center;
	background-color:#A7A7A7;
	text-shadow: #6374AB;
	font-weight:bold;
}

</style>
</head>
<body>
<div id="topo">
	<div id="dentro_topo">
		<table width="100%">
        	<tr>
            	<td width="11%" height="81" align="center"> <img src="imagens/logomaster<?=$id_master?>.gif" width="110" height="79"></td>
                <td width="36%" align="left" valign="top">
                	<br />
                  <span>Gest&atilde;o de Recursos Humanos</span><br />
                  <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                  14/10/2010<br />
                  Regiao: <?php echo $rw_regiao[1]; ?></td>
                <td width="53%" align="right"><table width="0" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>Regiao: </td>
                    <td>
                    <?php 
					 $usuarios_permitidos = array('75','5','9','27','64','77');
					 if(in_array($id_user,$usuarios_permitidos)):
					?>
                      <select name="regiao" id="regiao" onchange="MM_jumpMenu('parent',this,0)">
                      <?php
                      $query_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE status = '1' AND id_master = '$id_master'");
					  while($row_regiao = mysql_fetch_array($query_regiao)){
						  if($regiao == $row_regiao[0]){
							  $selected = 'selected="selected"';
						  }else{
						  	 $selected = '';
						  }
						  echo '<option '.$selected.' value="?regiao='.$row_regiao[0].'">'.$row_regiao[0].' - '.$row_regiao[1].'</option>';
					  }
					  ?>
                      </select>
                      <?php endif;?>
                   </td>
                    <td>&nbsp;</td>
                  </tr>
              </table></td>
            </tr>
        </table>
	</div>
</div>
<div id="base">
  <div id="botoes">
    	<ul>
        	<li class="titulos">
            	EMPRESA
            </li>
            <li>
                <a href="#" onClick="MM_openBrWindow('rh/rh_empresa.php?id=1&regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')">
                	<img src="rh/imagensrh/dadosempresa.gif" />
                </a>
            </li>
            <li><a href="#" onClick="MM_openBrWindow('rh/rh_feriados.php?id=1&amp;regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')">
            	<img src="rh/imagensrh/feriados.gif" />
                </a>
           	</li>
          <li><a href="rh/rh_impostos.php?id=1&amp;regiao=<?=$regiao?>"><img src="rh/imagensrh/taxas.gif" /></a></li>
            <li><a href="#" onClick="MM_openBrWindow('rh/rh_sindicatos.php?id=1&regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')" >
            	<img src="rh/imagensrh/sindicatos.gif" width="150" height="40" />
                </a>
            </li>
            <li><a href="rh/rh_horarios.php?regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/horarios.gif" width="150" height="40" /></a></li>
			<li><a href="rh/rh_telavale.php?regiao=<?=$regiao?>  target="_blank""><img src="rh/imagensrh/vale.gif" width="150" height="40" /></a></li>
           
            <?php 
		$filtro_user = array(75,9,33,77,5,68,82);
		if(in_array($id_user,$filtro_user)){ ?>
        <li>
   				 <a href="rh/pagamentos/index.php?id=<?=$_GET['id']?>&regiao=<?=$regiao?>"><img src="rh/imagensrh/pagamentos.png" /></a>
         </li>
        <?php }?>
            
           
        </ul>
        <div class="clear"></div>
		<ul>
        	<li class="titulos">
            	FUNCION&Aacute;RIOS
            </li>
            <li><a href="rh/clt.php?regiao=<?=$regiao?>"><img src="rh/imagensrh/edicao.gif" /></a></li>
            <li><a href="rh/rh_eventos.php?enc=<?=$linkevento?>"><img src="rh/imagensrh/eventos.gif" /></a></li>
            <li><a href="rh/rh_movimentos.php?regiao=<?=$regiao?>&tela=1"><img src="rh/imagensrh/movimentos.gif" /></a></li>
            <li><a href="rh/contracheque/solicita.php?id=1&enc=<?=$linkfo?>"  target="_blank"><img src="rh/imagensrh/contracheques.gif" /></a></li>
            <li><a href="rh/recisao/recisao.php?regiao=<?=$regiao?>"><img src="rh/imagensrh/rescisao.gif" /></a></li>
            <li><a href="rh/ferias/index.php?tela=1&enc=<?=$linkferias?>"><img src="rh/imagensrh/ferias.gif" /></a></li>
            <li><a onClick="MM_openBrWindow('rh/notifica/avisos.php?regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')"><img src="rh/notifica/imagens/avisos.gif" /></a></li>
        </ul>
        <div class="clear"></div>
		<ul>
        	<li class="titulos">
            	FOLHA, RELAT&Oacute;RIOS e IMPOSTOS
            </li>
            <li><a href="rh/folha/folha.php?tela=1&enc=<?=$linkfo?>" target="_blank"><img src="rh/imagensrh/situacoes.gif" /></a></li>
            <li><a href="rh/pis/index.php?regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/pis.gif" /></a></li>
            <li><a href="rh/rais/index.php?id=1&regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/ponto.gif" /></a></li>
            <li><a href="rh/sefip/index.php?regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/sefip.gif" /></a></li>
            <li><a href="rh/ir/index.php?regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/irrf.gif" /></a></li>
            <li><a href="rendimento/index2.php?id_reg=<?=$regiao?>&tela=1" target="_blank"><img src="rh/imagensrh/informe_rendimento.gif" /></a></li>
            <li><a href="rh/caged/"><img src="rh/imagensrh/bt_caged.png" /></a></li>
        </ul>
    </div>
    <div class="clear"></div>
   <div id="resumo">
   		<table border="0" align="center" cellpadding="2" cellspacing="1" >
        	<tr>
            	<td>CONTROLE DE PARTICIPANTES NA REGIÃO ATÉ A DATA ATUAL</td>
            </tr>
            <tr>
            	<td>Total de participantes: <?=$row_cont_total_geral?></td>
            </tr>
        </table>
        
        <table border="0" align="center" cellpadding="2" cellspacing="1">
        	<tr>
            	<td colspan="2">CONTROLE DE FUNCIONÁRIOS POR SITUAÇÃO ATUAL</td>
            </tr>
            <?php 
				$result_rhstatus = mysql_query("SELECT * FROM rhstatus where status_reg = '1'");
				while($row_rhstatus = mysql_fetch_array($result_rhstatus)):
				$result_cont_status = mysql_query("SELECT id_clt FROM rh_clt where status = '$row_rhstatus[codigo]' and id_regiao = '$regiao'");
				$row_cont_status = mysql_num_rows($result_cont_status);
			?>
            <tr>
            	<td><?php echo "($row_rhstatus[codigo]) $row_rhstatus[especifica]";?></td>
                <td  align="center"><?php echo $row_cont_status;?></td>
            </tr>
            <?php endwhile;?>
        </table >
       	<table border="0" align="center" cellpadding="2" cellspacing="1">
        	<tr>
            	<td colspan="2">CONTROLE DE FUNCIONÁRIOS ATIVOS POR SEXO</td>
            </tr>
            <tr>
            	<td>Homens</td>
                <td  align="center"><?=$row_cont_sexo_m?></td>
            </tr>
            <tr>
            	<td>Mulheres</td>
                <td  align="center"><?=$row_cont_sexo_f?></td>
            </tr>
        </table>
        <table border="0" align="center" cellpadding="2" cellspacing="1">
        	<tr>
            	<td colspan="2">CONTROLE DE FUNCIONÁRIOS EM EXPERIÊNCIA</td>
            </tr>
            <tr>
            	<td>Funcionário em experiência</td>
                <td align="center"><?php
				 $result_data_entrada = mysql_query("SELECT id_clt FROM rh_clt WHERE data_entrada > '$data_antiga' AND id_regiao = '$regiao'");
				 $row_datas = mysql_num_rows($result_data_entrada);
				 print $row_datas;
 				 ?>
                 </td>
            </tr>
        </table>
   </div>
</div>
</body>
</html>