<?php
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";

include("../../classes_permissoes/regioes.class.php");
$obj_regiao = new Regioes();






//--VERIFICANDO MASTER -----------------
$id_user = $_COOKIE['logado'];
$REuser = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($REuser);
$tipo_user  = $row_user['tipo_usuario'];
$REMaster = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($REMaster);
// ---- FINALIZANDO MASTER -----------------
$regiao = $_REQUEST['regiao'];
$mes2 = date('F');
$dia_h = date('d');
$mes_h = date('m');
$ano = date('Y');
$mes_q_vem = $mes_h + 1;
$meses = array('Erro','Janeiro','Fevereiro','Mar?o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];
$data_hoje = "$dia_h/$mes_h/$ano";

list($regiao) = explode('&', decrypt(str_replace('--','+',$_REQUEST['enc'])));



//EMBELEZAMENTO
$bord = "style='border-bottom:#000 solid 1px; font-size: 12px; font-face:Arial;'";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>:: Financeiro ::</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- highslide -->
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<script type="text/javascript" >
	hs.graphicsDir = '../../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- highslide -->

<script>
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
<link href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>

<script type="text/javascript">
$(function(){
	
		$('#dt_termino').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	
	
	$('#dt_inicio').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	
	$('.ano').parent().next().hide();
	$(".ano").click(function(){
		$(this).parent().next().slideToggle();
		$('.ano').parent().next().hide();
	});
	$('.dataautonomos').parent().next().hide();
	$('.dataautonomos').click(function(){
		$(this).parent().next().slideToggle();
		$('.dataautonomos').parent().next().hide();
	});
	$('a.recisao').click(function(){
		$(this).next().toggle();		
	});
	
	
                            
                $('#tipo_anual').change(function(){ 
               
                    if($(this).val() == 'entrada') {
                        
                        $('#select_entrada').show();
                        $('#select_saida').hide();
                    
                    } else {
                        
                        $('#select_entrada').hide();
                        $('#select_saida').show();
                    
                    }
                });
				
				
			
    ////SELECIONAR REGI�O
	$('#select_regiao'). change(function(){
		
		var  valor = $(this).val();
		
		$.ajax({
			url: 'encriptar.php?encriptar='+valor,
			success: function(link_encriptado){
				
				location.href="rel_abastecimento.php?enc="+link_encriptado;	
				
				}
			});
		
		
		
		});

});
</script>
<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	margin:0px;
	background:#F0F0F0;
}
#baseCentral{
	width:980px;
	margin:0px auto;
}
#topo{
	position:fixed;
	top:0px;
	background-color:#FFF;
	z-index:1000;
	width:978px;
	height:135px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-left-style: solid;
	border-top-color: #666;
	border-right-color: #666;
	border-left-color: #666;
}
#conteudo {
	position:relative;	
	background-color:#FFF;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-right-color: #666;
	border-bottom-color: #666;
	border-left-color: #666;
	
}
*html #conteudo {
	top:0px;
}
<!--
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
	text-align: center;
}
.style6 {
	font-size: 14px;
	font-weight: bold;
	color: #FFFFFF;
}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style29 {color: #000000}
.style31 {	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
	color: #FF0000;
}
.style32 {font-size: 10px}
.style33 {font-family: Verdana, Arial, Sans-Serif}
.style27 {color:#FFF}
-->

#geral h1{
	font-size: 14px;
	color: #F00;
	font-variant: small-caps;
	text-decoration: none;
	margin: 0px;
	padding-top: 0px;
	padding-right: 0px;
	padding-bottom: 0px;
	padding-left: 30px;
}
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 	border-bottom:1px solid #ccc;
}
</style>
<link href="../../novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="baseCentral">
<div id="topo">
<?php 
	$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
	$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
	$id_master = @mysql_result($query_master,0);
?>
	<table width="980">
   
    	<tr>
            <td width="110" rowspan="3">
                  <img src="../../imagens/logomaster<?=$id_master?>.gif" width="110" height="79">
          </td>
          <td align="left" valign="top">
          	<br />
                Data:&nbsp;<strong><?=date("d/m/Y");?></strong>&nbsp;<br />
        voc&ecirc; est&aacute; visualizando a Regi&atilde;o:&nbsp;<strong><?=@mysql_result($query_regiao,0);?></strong></td>
        	<td>
            <br />
            
        <!--Controle de regiao -->
        <div>
        <form name="formRegiao" id="formRegiao" method="get">
        <table>
        <tr>
            <td><span style="color:#000">Regi&atilde;o</span></td>
            <td>
                <select name='select_regiao' class='campotexto' id='select_regiao' >                                                                                
		  <?php   //$obj_regiao->Select_permissao_relatorio($regioes); 
                                                            
                                                               $obj_regiao->Preenhe_select_por_master($id_master, $regiao);?>                                                        
                </select> 
                
            </td>
        </tr>
        </table>
        </form>
        </div>
           <!--Controle de regiao -->
       
            </td>
      </tr>          
    </table>


 <form action="../../financeiro/abastecimento.php" method="post" name="formabas" id="formabas">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"  id="conteudo"> 
	 
    <tr>
    	<td>&nbsp;</td>
    </tr>
  
    <tr>
    	<td><a href="../relatorios.php?enc=<?php echo $linkEnc;?>"> <img src="../../img_menu_principal/voltar.png" title="VOLTAR"/> </a></td>
    </tr>
    <tr bgcolor="#E8E8E8">
		<td> <p>&nbsp;&nbsp;<img src="../../imagensmenu2/c1.gif" alt="cobrinha" align="absmiddle" />&nbsp;<b>RELAT&Oacute;RIOS DE ABASTECIMENTOS</b></p></td>
    </tr>
	  <tr>
    <td>  
             <p align="center"><span class="style12 style29">
          <label>Marque para ver o relat&oacute;rio anual:&nbsp;
            <input type="checkbox" name="anotodo" id="anotodo" value="1" onclick="document.formabas.mes.style.display = (document.formabas.mes.style.display == 'none') ? '' : 'none' ;" />
          </label>
    </span>
    </p>
    </td>
    </tr>
    <tr>
    	<td align="center">In&iacute;cio: <input type="text" name="inicio" id="dt_inicio" /> Fim: <input type="text" name="fim" id="dt_termino"/></td>
    </tr>
    <tr>
    	<td align="center">&nbsp;</td>  
	</tr> 
    <tr>
      <td align="center">  
      <input name="periodos" type="hidden" value="1"/>
          <input type="submit" class="submit-go" value="Visualizar Relat&oacute;rio" />
    </td>
  </tr>
  
</table>

 </form>

</div>
	</div>



</body>
</html>
