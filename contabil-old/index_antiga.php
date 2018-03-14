<?php 
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";



$id_user   = $_COOKIE['logado'];
$regiao    = $_GET['regiao'];
$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];
$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
$id_master         = @mysql_result($query_master,0);


/*
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
// -----------------------------*/


/*Resumo*/



/*Resumo*/

// Bloqueio Administração
echo bloqueio_administracao($regiao);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o Cont&aacute;bil</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript">
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
            	<td width="11%" height="81" align="center"> <img src="../imagens/logomaster<?=$id_master?>.gif" width="110" height="79"></td>
                <td width="36%" align="left" valign="top">
                	<br />
                  <span>Gest&atilde;o Contábil</span><br />
                  <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                  <strong>Data:</strong> <?php echo date('d/m/Y'); ?><br />
                  <strong>Regiao:</strong> <?php echo $rw_regiao[1]; ?></td>
                <td width="53%" align="right"><table width="0" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                   <td align="left" width="60" style="margin-right:10px;"><?php include('../reportar_erro.php'); ?></td>
                    <td>
				<?php // Visualizando Regiões
                      if($tipo_user == '1' or $tipo_user == '4') : ?>
                    <span id="labregiao1">
                    <select name="regiao" class="campotexto" id="regiao" onchange="MM_jumpMenu('parent',this,0)">
                        <option value="">- Selecione -</option>
                        <optgroup label="Regiões em Funcionamento">
                
                <?php
                // Acesso a Administração
                $ids_administracao = array('5','9','27','28','33','64','71','77','24','82','87');
                $ids_sistema = array('9','68','75','87');
                
                if(in_array($id_user,$ids_administracao)) {
                    $acesso_administracao = true;
                }
                if(in_array($id_user,$ids_sistema)) {
                    $acesso_sistema = true;
                }
                //
                
                    $qr_regioes_ativas = mysql_query("SELECT * FROM regioes WHERE id_master = '$id_master' AND status = '1' AND status_reg=1");
                    while($row_regiao = mysql_fetch_array($qr_regioes_ativas)) {
                        
                        if($regiao == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        }
                        
                        if(($row_regiao['id_regiao'] == '15' and isset($acesso_administracao)) or
                           ($row_regiao['id_regiao'] != '15')) {
                        
                        if(($row_regiao['id_regiao'] == '36' and isset($acesso_sistema)) or 
                           ($row_regiao['id_regiao'] != '36')) { ?>
                
                                <option value="?regiao=<?=$row_regiao['id_regiao'];?>&id=<?=$id_master;?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                    <?php } } } ?>
                    
                </optgroup>
                <optgroup label="Regiões Desativadas">
                
                <?php // Acesso a Regiões Desativadas
                $ids_desativadas = array('1','5','9','27','57','64','68','51','77','75','87');
                
                if(in_array($id_user,$ids_desativadas)) {
                    
                    $qr_desativadas = mysql_query("SELECT * FROM regioes WHERE id_master = '$id_master' AND status = '0' or status_reg ='0'");
                    while($row_regiao = mysql_fetch_array($qr_desativadas)) {
                        
                        if($regiao_usuario == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        } ?>
                        
                        <option value="?regiao=<?=$row_regiao['id_regiao']?>&id=<?=$id_master?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                <?php } } ?>
                
                </optgroup>
                </select>
                </span>
                <?php endif; // Fim de Regiões?>
                   </td>
                   
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
            	DEMONSTRATIVOS
            </li>
          <li><a href="../relatorios/consolidacao_folha.php?tela=1&regiao=<?=$regiao?>" ><img src="../imagens/consolida.gif" /></a></li>
            <li><a href="#" target="_blank"><img src="../imagens/plano_contas.gif" /></a></li>
              <li><a href="ficha_financeira.php?m=<?php echo $link_master;?>&regiao=<?=$regiao?>" ><img src="../imagens/ficha_financeira.gif"/></a></li>
      
           
        </ul>
        <div class="clear"></div>
		<ul>
        	<li class="titulos">
            	FINANCEIROS
            </li>
          
        </ul>
        <div class="clear"></div>
		<ul>
        	<li class="titulos">
            CONTÁBEIS
        
            
        </ul>
    </div>
    <div class="clear"></div>
   <div id="resumo">
   		       
       
        
   </div>
</div>
</body>
</html>