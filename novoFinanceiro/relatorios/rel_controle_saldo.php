<?php
include ("../include/restricoes.php");;
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
echo $regiao = $_REQUEST['regiao'];
$mes2 = date('F');
$dia_h = date('d');
$mes_h = date('m');
$ano = date('Y');
$mes_q_vem = $mes_h + 1;
$meses = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];
$data_hoje = "$dia_h/$mes_h/$ano";

list($regiao) = explode('&', decrypt(str_replace('--','+',$_REQUEST['enc'])));


//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);


//EMBELEZAMENTO
$bord = "style='border-bottom:#000 solid 1px; font-size: 12px; font-face:Arial;'";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>:: Financeiro ::</title>

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
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<link href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="../../js/global.js"></script>

<script type="text/javascript">
$(function(){
	
	
	
	
	$('.date').datepicker({
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
				
				///SELECIONAR REGIÃO
	$('#select_regiao'). change(function(){
		
		var  valor = $(this).val();
		$.ajax({
			url: 'encriptar.php?encriptar='+valor,
			success: function(link_encriptado){
				
				location.href="rel_controle_saldo.php?enc="+link_encriptado;	
				
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
	height:auto;
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
	/*border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-right-color: #666;
	border-bottom-color: #666;
	border-left-color: #666;
	*/
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

<div>
<?php 

	$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
	$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
	$id_master = @mysql_result($query_master,0);
?>
	<table width="980" bgcolor="#FFFFFF" >
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
               <!------ Visualizando Regiões --------->
              <select name='select_regiao' class='campotexto' id='select_regiao' >                                                                                
        <?php //$obj_regiao->Select_permissao_relatorio($regioes); 
                         $obj_regiao->Preenhe_select_por_master($id_master, $regiao); ?>                                                      
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



<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"  id="conteudo"> 
	 
    <tr>
    	<td>&nbsp;</td>
    </tr>
  
    <tr>
    	<td><a href="../relatorios.php?enc=<?php echo $linkEnc;?>"> <img src="../../img_menu_principal/voltar.png" title="VOLTAR"/> </a><p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('conteudo', 'Relat�rio')" value="Exportar para Excel" class="exportarExcel"></p></td>
    </tr>
  <tr>
	<td height="32" colspan="2" bgcolor="#E8E8E8"><div align="left"><span class="style9">&nbsp;&nbsp;<span class="style2"><img src="../../financeiro/imagensfinanceiro/contas.gif" alt="contas" width="25" height="25" align="absmiddle" />&nbsp;</span></span><span class="style3"> &nbsp;CONTROLE DE SALDOS</span></div></td>
</tr>
<tr>
<td colspan="2"><br>

<?php
$array_status = array(1 =>'Ativo' , 2 => 'Inativo');
foreach($array_status as $status => $nome_status) {
	
	if($status == 1) {
			  $RERegioes = mysql_query("SELECT * FROM regioes Where id_master = '$row_master[0]' and status='1' and status_reg =1");
	} else {
			 $RERegioes = mysql_query("SELECT * FROM regioes Where id_master = '$row_master[0]' and status='0' OR status_reg =0");
	}
	

?>
   <table width="95%" border="0" align="center" style="margin-top:10px;">
   <tr bgcolor="#F3F3F3">
   	<td align="center"><h2 style="text-transform:uppercase;font-weight:100;color:#666"><?php echo $nome_status;?> </h2></td>
   </tr>
  
   </table>

    <table width="95%" border="0" align="center" cellpadding="2" cellspacing="1" >
      <tr class="linha_um"> 
            <td width="5%" bgcolor="#333333"><div align="center" class="style27">COD</div></td>
            <td width="25%" bgcolor="#333333"><div align="center" class="style27">BANCO</div></td>
            <td width="9%" bgcolor="#333333"><div align="center" class="style27">AG</div></td>
            <td width="10%" bgcolor="#333333"><div align="center" class="style27">CC</div></td>
            <td width="30%" bgcolor="#333333"><div align="center" class="style27">PROJETO</div></td>
            <td width="17%" bgcolor="#333333"><div align="center" class="style27">SALDO PARCIAL </div></td>
           <td width="17%" bgcolor="#333333"><div align="center" class="style27">QUANT. SAIDAS HOJE</div></td>

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
		  if($id_user == '64' or $id_user == '87' or $id_user == '5' or $id_user == '9' or $id_user == '27' or $id_user == '77' or $id_user == '75'){
			
			  
			  while($RowRegioes = mysql_fetch_array($RERegioes)){
				  $REBancos = mysql_query("SELECT * FROM bancos where id_regiao = '$RowRegioes[0]' and interno ='1' AND status_reg = '1'");
				  $NumBancos = mysql_num_rows($REBancos);
				  
				  if($NumBancos != 0){
				  //S� VAI PRINTAR ESSAS INFORMA��ES SE A REGI�O SELECIONADA TIVER DIFERENTE DE 0
				  echo "<tr bgcolor='#666666'>";
				  echo "<td colspan='7' width='5%' align='center' $bord><div style='font-size:14px; color:#FFF'><b>$RowRegioes[regiao]</b></div></td>";
				  echo "</tr>";
				  
				  while($RowBancos = mysql_fetch_array($REBancos)){
					  
					  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
					  // verificando se existem saidas confirmadas hoje
					  $qr_saidas = mysql_query("SELECT * 
												FROM  `saida` 
												WHERE id_banco =  '$RowBancos[id_banco]'
												AND DAY( data_pg ) =  '".date("d")."'
												AND MONTH( data_pg ) =  '".date("m")."'
												AND YEAR( data_pg ) =  '".date("Y")."'
												AND status = '2';");
						$quant = @mysql_num_rows($qr_saidas);
					if(empty($quant)){
						$color = "#FFB09D";
					}
					$quant = NULL;
					$qr_saidas_hj = mysql_query("SELECT * FROM `saida` WHERE id_banco = '$RowBancos[id_banco]' AND DAY(data_vencimento) =  '".date("d")."' AND MONTH(data_vencimento) = '".date("m")."' AND YEAR(data_vencimento) = '".date("Y")."' AND status = '1'");
			  		$saidas_hoje = @mysql_num_rows($qr_saidas_hj);
					
						  $REProjeto = mysql_query("SELECT * FROM projeto where id_projeto = '$RowBancos[id_projeto]' ");
						  $RowProjeto = mysql_fetch_array($REProjeto);
			  
						  $ValorBanc = str_replace(",", ".", $RowBancos['saldo']);
			  			  $ValorBancF = number_format($ValorBanc,2,",",".");
						  
						  echo "<tr bgcolor='$color'>";
						  echo "<td width='5%' $bord>$div $RowBancos[id_banco]</div></td>";
						  echo "<td width='25%' $bord>$div $RowBancos[nome] 
						  <a href='../view/controle.saldo.php?id_banco=$RowBancos[id_banco]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe', width: 540 } )\" \">
				   <img src=\"../image/seta.gif\" />
				   		</a></div></td>";
						  echo "<td width='9%' $bord>$div $RowBancos[agencia]</div></td>";
						  echo "<td width='10%' $bord>$div $RowBancos[conta]</div></td>";
						  echo "<td width='30%' $bord>$div $RowProjeto[nome]&nbsp;</div></td>";
						  echo "<td width='17%' $bord>$div $ValorBancF </div></td>";
						  echo "<td width='17%' $bord>$div $saidas_hoje </div></td>";
						  echo "</tr>";
		  
						  $cont ++;
				  	  }
				    
			
				  }// S� VAI RODAR ISSO AE EM CIMA, SE TIVER BANCO NA REGIAO
				  
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
				  echo "<td width='5%' $bord>$div $RowBanc[id_banco] 
				  		
				   </div></td>";
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
  	<td colspan="4">&nbsp;</td>
  </tr>

</table>

</div>
	</div>



</body>
</html>
