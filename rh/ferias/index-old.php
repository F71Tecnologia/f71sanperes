	<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}





include('../../conn.php');
include('../../funcoes.php');
include('../../classes/clt.php');
include('../../classes/calculos.php');

if(empty($_REQUEST['enc'])) {
	$tela = $_REQUEST['tela'];
} else {
	$enc  = str_replace('--','+',$_REQUEST['enc']);
	$link = decrypt($enc);
	list($regiao,$tela,$clt,$id_ferias) = explode('&',$link);
}

if($_GET['deletar'] == true) {
	$movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_ferias WHERE id_ferias = '".$_GET['id']."' LIMIT 1"),0);
	$total_movimentos = (int)count(explode(',',$movimentos));
	mysql_query("UPDATE rh_ferias SET status = '0' WHERE id_ferias = '".$_GET['id']."' LIMIT 1");
	mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '1' WHERE id_movimento IN('".$movimentos."') LIMIT ".$total_movimentos."");
       mysql_query("UPDATE rh_clt SET status = 10  WHERE id_clt = '$_GET[id_clt]' LIMIT 1");
}

$meses = array('', '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Mar&ccedil;o', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet :: F&eacute;rias</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<link href="../../favicon.ico" rel="shortcut icon" />
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript">

hs.graphicsDir = '../../images-box/graphics/'; 
hs.outlineType = 'rounded-white';
    
$(function() {
	// Calend�rio
	$('#data_inicio').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	// Exibe e Oculta a Div de Hist�rico
	$("#ver_historico").click(function() {
		$('#historico').toggle('fast');
	});
	
	
	
	
	
	
	
	$('select[name*=quantidade_dias]').change(function(){
	 var classe = $(this).find('option[selected]').attr('class');
	 
	  if(classe == 'oculta'){
		$('#periodo_abono').fadeOut();
	  }else if(classe == 'exibe'){
		  
		$('#periodo_abono').fadeIn();
	  }
   });
   
   
   
   
   $('a.regiao').click(function(){
	
	
	var id_link = $(this).attr('href');
	
	if(	$('#'+id_link).css('display')=='none' ) {
	
	
		$('#'+id_link).show();
		$('#'+id_link).css('width','100%');
		
		
		$('#verifica').val(id_link);
		
   } else {
	
		$('#'+id_link).hide();
		
   }
	
	
});
   
});
/* Exibe e Oculta a Div de Abono
$("#quantidade_dias").change(function() {
	$(this).find('.oculta').hide();
	
	$("#periodo_abono").css('display','block');
});*/

// Quando n�o seleciona um per�odo aquisitivo
function verifica_nulo() {
	
	var d = document.formp;
	var contaForm = d.elements.length - 3;
	var Yescheck = 0;
	var Nocheck = 0;
	
	for(i=0 ; i<=contaForm ; i++) {
		if (d.elements[i].id == "periodo_aquisitivo") {
			if (!d.elements[i].checked){
				Yescheck ++;
			} else {
				Nocheck++;
			}
		}
	}
	
	if(Nocheck == 0){
		alert ("Selecione um Per�odo Aquisitivo");
		return false;
	}
	
	return true;
}



// Verifica se a data � v�lida
function mascara_data(d){  
       var mydata = '';  
       data = d.value;  
       mydata = mydata + data;  
       if (mydata.length == 2){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 5){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 10){  

          verifica_data(d);  
         }  
      } 
           
         function verifica_data (d) {  

         dia = (d.value.substring(0,2));  
         mes = (d.value.substring(3,5));  
         ano = (d.value.substring(6,10));  
             
       situacao = "";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = "falsa";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = "falsa";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = "falsa";  
      }  
   
     if (d.value == "") {  
          situacao = "falsa";  
    }  

    if (situacao == "falsa") {  
       alert("Data digitada n�o valida, digite novamente!"); 
       d.value = "";  
       d.focus();  
    }	
}






</script>
<style>
body {
	background-color:#FAFAFA;
	text-align:center;
	margin:0px;
	
}
p {
	margin:0px;
}
#corpo {
	width:90%;
	background-color:#FFF;
	border-color:#09F;
	margin:0px auto;
	text-align:left;
	padding-top:20px;
	padding-bottom:10px;
}

.aviso {
	 width:45%;
	 height:auto;
	margin-left:40px;
	margin-top:30px;
	text-align: center;
	float:left;
	

	 	

	
}

.regiao{
	
	
	 background-color:   #F2F9FF;
	 color:#000;
	 text-decoration:none;
	 padding-left:5px;
	 border: 1px solid  #E1E1E1;
	 font-size:18px;
	 
	 font-family:Arial, Helvetica, sans-serif;
	 display:block;
	 cursor:pointer;
	 
}

.regiao:hover{
	
	
	 background-color:   #E4E4E4;
	
	 
}

.aberto {
	 background-color: #DEF;
	 color:#000;
	 text-decoration:none;
	 padding-left:5px;
	 border: 1px solid  #E1E1E1;
	 font-size:18px;
	 
	 font-family:Arial, Helvetica, sans-serif;
	 display:block;
	 cursor:pointer;
	
}


.titulo{
	background-color:#C99;
	font-size:16px;
	color:#FFF;
	padding-left: 10px;
	width:180px;
	margin:20px auto;
	
}
	
	
	
fieldset legend{
	
font-size:14px;
font-weight:bold;
	
}
</style>
</head>
<body>
<div id="corpo">
<?php 
// Tela 1 (Sele��o de participante)
switch($tela) {
	case 1:
?>
<div style="float:right">
<?php include('../../reportar_erro.php');?>
</div>
<div style="clear:right;"></div>

<div id="topo" style="width:95%; margin:0px auto;">
	<div style="float:left; width:25%;">
        <a href="../../principalrh.php?regiao=<?=$regiao?>">
            <img src="../../imagens/voltar.gif">
        </a>
    </div>
	<div style="float:left; width:50%; text-align:center; font-family:Arial; font-size:24px; font-weight:bold; color:#000;">
    	F&Eacute;RIAS</div>
	<div style="float:right; width:25%; text-align:right; font-family:Arial; font-size:12px; color:#333;">
    	<br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;
    </div>
	<div style="clear:both;"></div>
</div>



<!--F�RIAS A VENCER------->
      <div class="aviso" style="  background-color:#C4E1FF;">
      <fieldset style="background-color:#C4E1FF;">
                <legend>F&Eacute;RIAS A VENCER</legend>
                
            <?php include("ferias_vencer.php");?>
            
           
      </fieldset>
	</div>
    
    

<!--F�RIAS VENCIDAS----------->
<div  class="aviso" style=" background-color:#FF7575;">
            <fieldset style=" background-color:#FF7575;">
                <legend><span style="color:#FFF1EA;text-weight:bold;">F�RIAS  VENCIDAS</span> </legend>
                  
                         <?php include("ferias_vencida.php");?>
           </fieldset>
</div>


<div style="clear:left;"></div>

<?php 	 $total_clt = NULL;
	 $qr_projetos = mysql_query("SELECT A.*, B.cnpj FROM projeto as A
            INNER JOIN rhempresa as B 
            ON (A.id_regiao = B.id_regiao AND B.id_projeto = A.id_projeto)
            WHERE A.id_regiao = '$regiao' AND A.status_reg = '1' OR A.status_reg = '0' ORDER BY A.nome ASC"); while($projetos = mysql_fetch_assoc($qr_projetos)) {
        $REClts = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[id_projeto]' AND id_regiao = '$regiao' AND (status < '60' OR status = '200')  ORDER BY nome ASC");
		  	$numero_clts = mysql_num_rows($REClts);
		  	if(!empty($numero_clts)) {
			$total_clt++; ?>
          
          
          
          
    <table width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:20px;">
      <tr>
        <td colspan="7" class="show">
            &nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome']; ?> / CNPJ: <?php echo $projetos['cnpj'];?>
        </td>
      </tr>
    <tr class="novo_tr">
      <td width="5%">COD</td>
      <td width="35%">NOME</td>
      <td>VALOR</td>
      <td width="20%" align="center">DATA DE ENTRADA</td>
      <td width="20%" align="center">AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS</td>
      <td width="20%" align="center">VENC. DE F&Eacute;RIAS</td>
      
    </tr>
    <?php }
	
	while($row_clt10 = mysql_fetch_array($REClts)) {
		
	$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt10[id_clt]' AND status = '1' ORDER BY data_fim DESC");
	$ferias = mysql_fetch_assoc($qr_ferias);
    
	
	    if(empty($ferias['data_ini'])) { 
			$DataEntrada = $row_clt10['data_entrada2'];
		} else {
			$preview1 = explode('-',$ferias['data_fim']);
			$preview2 = $preview1[0];
			$preview3 = explode('/',$row_clt10['data_entrada2']);
			$DataEntrada = "$preview3[0]/$preview3[1]/$preview2";
		}

		$DataEntrada = explode('/', $DataEntrada);
		
		$F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
		$F_ini_E = explode('/',$F_ini);
		
		$F_fim = date('d/m/Y', mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1));
		
		$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1' ");
		$row_pro = mysql_fetch_array($result_pro);

	// Encriptografando a Vari�vel
	$link = encrypt("$regiao&2&$row_clt10[0]");
	$link2 = str_replace("+","--",$link);
	// -----------------------------
   ?>
   	 <tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
		<td><?=$row_clt10[0]?></td>
	    <td><a href='index.php?enc=<?=$link2?>'><?=$row_clt10['nome']?></a>
   		<?php if($row_clt10['status'] == '40') { 
   					echo '<span style="color:#069; font-weight:bold;">(Em F�rias)</span>';
   			   } elseif($row_clt10['status'] == '200') {
	   				echo '<span style="color:red; font-weight:bold;">(Aguardando Demiss�o)</span>';
   			   } ?></td>
   		<td>R$<?php 
   		$total_ferias = $ferias['total_liquido'];
   		$totalizador_ferias += $total_ferias;
   		echo number_format($total_ferias,2,',','.');
   		?>
   		</td>
   		<td align="center" class="style3"><?=$row_clt10['data_entrada2']?></td>
   		<td align="center" class="style3"><?=$F_ini?></td>
        <td align="center" class="style3"><?=$F_fim?></td>
     </tr>
   <?php } ?>
   
</table>


<?php } 

	// Se n�o tem nenhum CLT na regi�o
	if(empty($total_clt)) { ?>
    
        <META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?=$regiao?>&id=1">
        <p style="color:#C30; font-size:12px; font-weight:bold; margin:30px auto; width:50%; text-align:center;">
            Obs: A regi�o n�o possui participantes CLTs.
        </p>
      
	<?php } else { ?>
		<table width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center'>
			<tr>
		   		<td width="5%">&nbsp;</td>
		   		<td width="35%" align="right">TOTAL : </td>
		   		<td>R$ <?php echo number_format($totalizador_ferias,2,',','.'); ?></td>
		   		<td width="20%">&nbsp;</td> 
		   		<td width="20%">&nbsp;</td>
		   		<td width="20%">&nbsp;</td>
		   </tr>
		</table>
        <div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
            <a href="#corpo" title="Subir navega��o">Subir ao topo</a>
        </div>
    
    <?php }  ?>


<!----------------------------------REGI�O 15 --------------------------------------------------------------->
<?php 
if($regiao == '15') :

$status_reg = array(1 => 'Ativas', 2 => 'Inativas');

	foreach($status_reg as $chave => $valor) {
					
				
		if($chave == '1') {			
			$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 1 AND status_reg = 1 ORDER BY regiao");
			?>
				<table width="95%" align='center' style="margin-top:5px;">
					  <tr class="titulo">
						<td><strong> Regi�es Ativas</strong></td>
					 </tr>
			   </table>			
		<?php	
		} else {
			
			$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 0 OR status_reg = 0 ORDER BY regiao");
			?>
				<table width="95%" align='center' style="margin-top:5px;">
					  <tr class="titulo">
						<td><strong>Regi�es Inativas</strong></td>
					 </tr>
			   </table>			
		<?php
		}
		
		while($row_regiao = mysql_fetch_assoc($qr_regioes)):
					
					$status++;				
					
					$mostrar_regiao=0;
										
					  $total_clt = NULL;
					  $qr_projetos = mysql_query("SELECT A.*, B.cnpj FROM projeto as A
                                                                    INNER JOIN rhempresa as B 
                                                                    ON (A.id_regiao = B.id_regiao AND B.id_projeto = A.id_projeto)
                                                                    WHERE A.id_regiao = '$regiao' AND A.status_reg = '1' OR A.status_reg = '0' ORDER BY A.nome ASC");
					  while($projetos = mysql_fetch_assoc($qr_projetos)) {
									  
						$REClts = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[id_projeto]' AND id_regiao = '$row_regiao[id_regiao]' AND (status < '60' OR status = '200')  ORDER BY nome ASC");
						$numero_clts = mysql_num_rows($REClts);
						
						 if(empty($numero_clts)) continue;
						 
						 if($mostrar_regiao == 0){
							 
							 $mostrar_regiao=1;
						?>
						
					<table width="95%" align='center' style="margin-top:0px;">
					  <tr>
						<td colspan="7">
						<a href="<?php echo $row_regiao['id_regiao']; ?>" class="regiao" onclick="return false" > <?php echo $row_regiao['regiao'];  ?></a>
						
						</td>
					 </tr>
				 
					 <tr id="<?php echo $row_regiao['id_regiao']; ?>" style="display:none;" >
						<td>
		
						
						<?php
						 }			
										if(!empty($numero_clts)) {
										$total_clt++; ?>
									  
								<table width="100%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:10px; ">
								  <tr>
									<td colspan="7" class="show" >
										&nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome']; ?> / CNPJ:  <?php echo $projetos['cnpj'];?> 
									</td>
								  </tr>
								<tr class="novo_tr">
								  <td width="5%">COD</td>
								  <td width="35%">NOME</td>
								  <td width="20%" align="center">DATA DE ENTRADA</td>
								  <td width="20%" align="center">AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS</td>
								  <td width="20%" align="center">VENC. DE F&Eacute;RIAS</td>
								</tr>
								<?php }
											
															while($row_clt10 = mysql_fetch_array($REClts)) {
																
															$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt10[id_clt]' AND status = '1'");
															$ferias = mysql_fetch_assoc($qr_ferias);
															
																if(empty($ferias['data_ini'])) { 
																		$DataEntrada = $row_clt10['data_entrada2'];
																	} else {
																	$preview1 = explode('-',$ferias['data_fim']);
																	$preview2 = $preview1[0];
																	$preview3 = explode('/',$row_clt10['data_entrada2']);
																	$DataEntrada = "$preview3[0]/$preview3[1]/$preview2";
																}
														
																$DataEntrada = explode('/', $DataEntrada);
																
																$F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
																$F_ini_E = explode('/',$F_ini);
																
																$F_fim = date('d/m/Y', mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1));
																
																$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1'");
																$row_pro = mysql_fetch_array($result_pro);
														
															// Encriptografando a Vari�vel
															$link = encrypt("$regiao&2&$row_clt10[0]");
															$link2 = str_replace("+","--",$link);
															// -----------------------------
														   ?>
                                                        
															 <tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
																<td><?=$row_clt10[0]?> </td>
																<td><a href='index.php?enc=<?=$link2?>'><?=$row_clt10['nome']?></a>
																<?php
																
																	  if($row_clt10['status'] == '40') { 
																			echo '<span style="color:#069; font-weight:bold;">(Em F�rias)</span>';
																	   } elseif($row_clt10['status'] == '200') {
																			echo '<span style="color:red; font-weight:bold;">(Aguardando Demiss�o)</span>';
																	   } ?></td>
																<td align="center" class="style3"><?=$row_clt10['data_entrada2']?></td>
																<td align="center" class="style3"><?=$F_ini?></td>
																<td align="center" class="style3"><?=$F_fim?></td>
															 </tr>
														   <?php } //CLT  ?>
                                                           
                                                 <tr style="backgorund-color:#FFF;">
                                                    <td colspan="7">&nbsp;</td>
                                              </tr>        
                                                           
										</table>
										<?php } //PROJETO
										
									  ?>
							</td>
						 </tr>
                   </table>
	<?php 	
				endwhile;
	echo ' <div style="width:auto; height:50px;"></div>';
	
	}//FIM LOOP


endif; //FIM REGIAO 15


?>
<!----------------------------------FIM  REGI�O 15 --------------------------------------------------------------->




<?php

// Tela 2 (Movimentos e Hist�rico de F�rias)
break;
case 2:

$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

// Encriptografando a Vari�vel
$tela  = 3;
$link  = encrypt("$regiao&$tela&$clt"); 
$link  = str_replace("+","--",$link);
$link2 = encrypt("$regiao&$clt"); 
$link2 = str_replace("+","--",$link2);
$tela3 = 1;
$link3 = encrypt("$regiao&$tela3&$clt"); 
$link3 = str_replace("+","--",$link3);

// Informa��es do CLT
$Clt = new clt();
$Clt -> MostraClt($clt);
$data_entrada = $Clt -> data_entrada;
$id_clt = $Clt -> id_clt;

// Verificando Per�odos Gozados
$qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = '1' ORDER BY id_ferias ASC");
$numero_periodos = mysql_num_rows($qr_periodos);
while($periodos = mysql_fetch_assoc($qr_periodos)) {
	$periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
}
$periodos_gozados[] = '';
$periodos_gozados[] = '';

// Verificando Per�odos Dispon�veis
list($ano_data_entrada,$mes_data_entrada,$dia_data_entrada) = explode('-', $data_entrada);
$quantidade_anos = date('Y') - $ano_data_entrada;
?>


<table cellpadding='8' cellspacing='8' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px; background-color:#f5f5f5;">

<tr>
	<td align="right"><?php include('../../reportar_erro.php');?></td>
</tr>
  <tr>
    <td>
     <div id="tela2">
     
       <span style="font-size:10px;">
           <?=$clt.' - '.$row_clt['nome']?>
       </span>
       
    <?php
	
	
	 // Verificando n�mero de periodos disponiveis
	/*$periodos_disponiveis = 0; 
	for($a=0; $a<$quantidade_anos; $a++) { 
       $aquisitivo_inicio = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada, $ano_data_entrada + $a));
	   $aquisitivo_final = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada - 1, $ano_data_entrada + $a + 1)); $periodo_aquisitivo = $aquisitivo_inicio.'/'.$aquisitivo_final;
	 
	   if(!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final <= date('Y-m-d')) {
	   	    $periodos_disponiveis++;
	   }
	} ?>
	
	<?php // Se n�o tem per�odos disponiveis e n�o tem hist�rico de f�rias
	if(empty($periodos_disponiveis) and empty($numero_periodos)) { ?>
      <META HTTP-EQUIV=Refresh CONTENT="1; URL=/intranet/rh/ferias/index.php?enc=<?=$link3?>">
      <p style="color:#C30; font-size:12px; font-weight:bold; margin-top:10px;">
               Obs: candidato(a) n&atilde;o possui per&iacute;odo aquisitivo a f�rias
      </p>
      
	<?php // Se n�o tem per�odos disponiveis mas tem hist�rico de f�rias
	} elseif(empty($periodos_disponiveis) and !empty($numero_periodos)) { ?>
      <p style="color:#C30; font-size:12px; font-weight:bold; margin-top:10px; margin-bottom:10px;">
               Obs: candidato(a) n&atilde;o possui per&iacute;odo aquisitivo a f�rias
      </p>
      <a class="botao" style="margin:10px auto;" href="index.php?enc=<?=$link3?>">Voltar</a>
      <a id="ver_historico" class="botao" style="margin:10px auto;" href="#">Ver hist&oacute;rico de f&eacute;rias</a>
      
    <?php // Se tem periodos disponiveis
	} else { */?> 
         
       <p>&nbsp;</p>
            J� lan&ccedil;ou os movimentos do candidato neste m�s?
       <p>&nbsp;</p>
       
        <a class="botao" style="margin:10px auto;" href="index.php?enc=<?=$link?>">Sim, prosseguir</a>
        <a class="botao" style="margin:10px auto;" href="../rh_movimentos.php?tela=2&ferias=true&enc=<?=$link2?>">N�o, inserir movimentos</a>
        <a class="botao" style="margin:10px auto;" href="index.php?enc=<?=$link3?>">Cancelar</a>
        
        <?php if(!empty($numero_periodos)) { ?>
        	<a id="ver_historico" class="botao" style="margin:10px auto;" href="#">Ver hist&oacute;rico de f&eacute;rias</a>
        <?php } ?>

   <?php // } // Fim das condi��es de subtelas
 
 // Tela de Hist�rico
 $qr_historico     = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = '1' ORDER BY data_fim DESC") or die(mysql_error());		 
 $numero_historico = mysql_num_rows($qr_historico);
 
 
 if(!empty($numero_historico)) { ?>
   	   <div id="historico" style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px; background-color:#eee; display:none; padding:10px;">
			<?php while($historico = mysql_fetch_assoc($qr_historico)) {
				    $margem++;
					$id_ferias 				= $historico['id_ferias'];
					$mes					= $historico['mes'];
					$ano					= $historico['ano'];
					$data_aquisitivo_inicio = implode('/', array_reverse(explode('-', $historico['data_aquisitivo_ini'])));
					$data_aquisitivo_fim 	= implode('/', array_reverse(explode('-', $historico['data_aquisitivo_fim'])));
					$data_ferias_inicio 	= implode('/', array_reverse(explode('-', $historico['data_ini'])));
					$data_ferias_fim 		= implode('/', array_reverse(explode('-', $historico['data_fim'])));
					$data_publicacao		= implode('/', array_reverse(explode('-', substr($historico['data_proc'],0,10))));
					$qr_funcionario 		= mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$historico[user]'");
					$autor					= @mysql_result($qr_funcionario,0);
					
					// Encriptografando a Vari�vel
					$link_relatorio = encrypt("$regiao&$id_clt&$id_ferias"); 
					$link_relatorio = str_replace('+', '--', $link_relatorio);
					// --------------------------- ?>
                    <table cellspacing="0" cellpadding="2" align="center" style="font-size:12px; width:70%; <?php if($margem != $numero_historico) { echo 'margin-bottom:20px;'; } ?>">
                      <tr>
                        <td rowspan="3">
                      		<a href="ferias.php?enc=<?=$link_relatorio?>" title="Gerar Relat�rio"><img src="../../imagens/pdf.gif" alt="Gerar Relat�rio"></a>
                        </td>
                        <td colspan="2"><?php echo '('.$id_ferias.') '.$meses[$mes].' / '.$ano; ?></td>
                        <td rowspan="3">
							<?php if(in_array($_COOKIE['logado'],array('5','9','68','75','82',87))) { ?>
                            	<a href="index.php?deletar=true&id=<?php echo $id_ferias; ?>&enc=<?php echo $_GET['enc']; ?>&id_clt=<?php echo $id_clt;?>" title="Desprocessar F�rias" onclick="return window.confirm('Voc� tem certeza que quer desprocessar esta f�rias?');"><img src="../imagensrh/deletar.gif" /></a>
							<?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td width="110"><b>Per�odo Aquisitivo:</b></td>
                        <td><?=$data_aquisitivo_inicio?> <i>�</i> <?=$data_aquisitivo_fim?></td>
                      </tr>
                      <tr>
                        <td width="110"><b>Per�odo de F�rias:</b></td>
						<td><?=$data_ferias_inicio?> <i>�</i> <?=$data_ferias_fim?></td>
                      </tr>
                    </table>
			<?php } ?>
       </div>
 <?php } ?>
        
       </div>
    </td>
  </tr>
</table>










<?php
// Tela 3 (Per�odos Aquisitivos)
break;
case 3:

$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

// Encriptografando a Vari�vel
$tela = 4;
$link = encrypt("$regiao&$tela&$clt"); 
$link = str_replace("+","--",$link);
$tela2 = 2;
$link2 = encrypt("$regiao&$tela2&$clt"); 
$link2 = str_replace("+","--",$link2);

// Informa��es do CLT
$Clt = new clt();
$Clt -> MostraClt($clt);
$data_entrada = $Clt -> data_entrada;
$id_clt = $Clt -> id_clt;

// Verificando Per�odos Gozados
$qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = '1' ORDER BY id_ferias ASC ") or die(mysql_error());
while($periodos = mysql_fetch_assoc($qr_periodos)) {
	$periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
}
$periodos_gozados[] = '';
$periodos_gozados[] = '';

// Verificando Per�odos Dispon�veis
list($ano_data_entrada,$mes_data_entrada,$dia_data_entrada) = explode('-', $data_entrada);
$quantidade_anos = date('Y') - $ano_data_entrada;
//$quantidade_anos = 1;
?>

<table border='0' cellpadding='8' cellspacing='8' bgcolor='#f5f5f5' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px;">
 <tr>
	<td align="right"><?php include('../../reportar_erro.php');?></td>
</tr>
 
  <tr>
    <td>
     <div id="tela2">
     
       <span style="font-size:10px;">
           <?=$clt.' - '.$row_clt['nome']?>
       </span>
       
       <p>&nbsp;</p>
       Selecione um Per�odo Aquisitivo:
       <p>&nbsp;</p>
       
    <form action="index.php" method="get" onSubmit="return verifica_nulo()" name="formp" id="formp">
    	<?php for($a=0; $a<$quantidade_anos; $a++) {      
    		  	$aquisitivo_inicio = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada, $ano_data_entrada + $a));
				$aquisitivo_final = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada - 1, $ano_data_entrada + $a + 1));
				$periodo_aquisitivo = $aquisitivo_inicio.'/'.$aquisitivo_final;
	
	
	
				// Se o per�odo j� foi adquirido e n�o foi gozado, prossegue...
				//if(!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final <= date('Y-m-d')) { ?>
           
        <label style="font-weight:normal; margin-bottom:5px;">
        	<input type="radio" name="periodo_aquisitivo" id="periodo_aquisitivo" value="<?=$periodo_aquisitivo?>"
			
			<?php // Caso venha da Tela 4 vem selecionado o per�odo aquisitivo
				  if($_GET['periodo_aquisitivo'] == $periodo_aquisitivo) { echo " checked"; } ?>
             > 
			<?php
			
			echo implode('/', array_reverse(explode('-', $aquisitivo_inicio))).' - '.date('d/m/Y', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada - 1, $ano_data_entrada + $a + 1)); ?>
        </label>
        <br>
          
      <?php } //} ?>
        
        <p>&nbsp;</p>
        <input type="submit" value="Prosseguir" class="botao" style="margin:10px auto;">
        <input type="button" value="Voltar" onClick="javascript:location.href = 'index.php?enc=<?=$link2?>'" class="botao" style="margin:10px auto;">
        <input type="hidden" name="enc" value="<?=$link?>">
        <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
    </form>
       
       </div>
    </td>
  </tr>
</table>










<?php
// Tela 4 (Data da entrada de f�rias e Quantidade de dias das f�rias)
break;
case 4:

$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

// Encriptografando a Vari�vel
$tela = 5;
$link = encrypt("$regiao&$tela&$clt"); 
$link = str_replace("+","--",$link);
$tela2 = 3;
$link2 = encrypt("$regiao&$tela2&$clt"); 
$link2 = str_replace("+","--",$link2);

// Informa��es do CLT
$Clt = new clt();
$Clt -> MostraClt($clt);
$data_entrada = $Clt -> data_entrada;
$id_clt = $Clt -> id_clt;

// Verificando o Per�odo Aquisitivo
$periodo_aquisitivo = explode('/', $_REQUEST['periodo_aquisitivo']);
$aquisitivo_ini = $periodo_aquisitivo[0];
$aquisitivo_end = $periodo_aquisitivo[1];
$preview_ini = explode('-', $aquisitivo_ini);
$preview_fim = explode('-', $aquisitivo_end);
$dia_ini = $preview_ini[2];
$mes_ini = $preview_ini[1];
$ano_ini = $preview_ini[0];
$dia_fim = $preview_fim[2];
$mes_fim = $preview_fim[1];
$ano_fim = $preview_fim[0];

$data_limite = date('d/m/Y', mktime('0','0','0', $mes_fim, $dia_fim - 1, $ano_fim + 1));
$data_dobrada = date('d/m/Y', mktime('0','0','0', $mes_fim, $dia_fim, $ano_fim + 1));
$data_corrente_real = implode('/', array_reverse(explode('-', $aquisitivo_end)));

if(!empty($_GET['data_inicio'])) {
	$data_corrente = $_GET['data_inicio'];
} else {
	$data_corrente = implode('/', array_reverse(explode('-', $aquisitivo_end)));
}

// Buscando Faltas
$falta_aquisitivo_ini = explode('-', $aquisitivo_ini);
$falta_aquisitivo_end = explode('-', $aquisitivo_end);

if($falta_aquisitivo_ini[1] == 12) {
	$limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
}

if($falta_aquisitivo_end[1] == 1) {
	$limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
}

$qr_faltas1 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
$row_faltas1 = mysql_fetch_array($qr_faltas1);

$qr_faltas2 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
$row_faltas2 = mysql_fetch_array($qr_faltas2);

if(!isset($_GET['despreza_faltas'])) {
	
	$faltas = $row_faltas1['faltas'] + $row_faltas2['faltas'];
	$faltas_real = $row_faltas1['faltas'] + $row_faltas2['faltas'];
	
	if($faltas <= 5) {
		$qnt_dias = 30;
	} elseif($faltas >= 6 and $faltas <= 14) {
		$qnt_dias = 24;
	} elseif($faltas >= 15 and $faltas <= 23) {
		$qnt_dias = 18;
	} elseif($faltas >= 24 and $faltas <= 32) {
		$qnt_dias = 12;
	} elseif($faltas > 32) {
		$qnt_dias = 0;
	}

} else {
	
	$faltas = 0;
	$faltas_real = $row_faltas1['faltas'] + $row_faltas2['faltas'];
	$qnt_dias = 30;
	
}

$update_movimentos_clt = '0';

$qr_novo_faltas1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
while($row_novo_faltas1 = mysql_fetch_assoc($qr_novo_faltas1)) {
	$update_movimentos_clt .= ','.$row_novo_faltas1['id_movimento'];
}

$qr_novo_faltas2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
while($row_novo_faltas2 = mysql_fetch_assoc($qr_novo_faltas2)) {
	$update_movimentos_clt .= ','.$row_novo_faltas2['id_movimento'];
}
//-----------------------------------------------------
?>
<script language="javascript">
function verifica_tudo() {	

	var d = document.getElementById('data_inicio');
			
	if(d.value == ""){
	  alert("Data In�cio n�o pode estar vazia");
	  d.value = "<?=$data_corrente?>";
	  d.focus();
	  return false;
	}
	
	var d = document.getElementById('data_inicio');
	var datacorrente = "<?=$data_corrente_real?>";
	var data1 = datacorrente;
	var data2 = d.value;
			
	/*if ( parseInt( data2.split( "/" )[2].toString() + data2.split( "/" )[1].toString() + data2.split( "/" )[0].toString() ) < parseInt( data1.split( "/" )[2].toString() + data1.split( "/" )[1].toString() + data1.split( "/" )[0].toString() ) ){
	  alert("F�rias s� pode ter in�cio a partir de <?=$data_corrente_real?>");
	  d.value = "<?=$data_corrente_real?>";
	  d.focus();
	  return false;
	}
*/
	return true;

}

function verifica_dobradas() {
	
	var d = document.getElementById('data_inicio');
	var r = document.getElementById('ferias_dobradas');
	var datacorrente = "<?=$data_limite?>";
	var data1 = datacorrente;
	var data2 = d.value;
			
	if ( parseInt( data2.split( "/" )[2].toString() + data2.split( "/" )[1].toString() + data2.split( "/" )[0].toString() ) > parseInt( data1.split( "/" )[2].toString() + data1.split( "/" )[1].toString() + data1.split( "/" )[0].toString() ) ) {
		r.style.display = ''
	} else {
		r.style.display = 'none'
	}

}
</script>
<table border='0' cellpadding='8' cellspacing='8' bgcolor='#f5f5f5' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px;">
 <tr>
	<td align="right"><?php include('../../reportar_erro.php');?></td>
</tr>
 
  <tr>
    <td>
     <div id="tela2">
     
       <span style="font-size:10px;">
           <?=$clt." - ".$row_clt['nome']?>
       </span>
       
       <p>&nbsp;</p>
       		Per�odo Aquisitivo:
       <br>
       
       <span style="font-weight:normal;">
	       <?php echo implode('/', array_reverse(explode('-', $aquisitivo_ini))).' - '.implode('/', array_reverse(explode('-', $aquisitivo_end))); ?>
       </span>
       
       <p>&nbsp;</p>
       
    <form action="index.php?enc=<?=$link?>" method="post" onSubmit="return verifica_tudo()">
        Data de In&iacute;cio das F&eacute;rias:
        
        <input name="data_inicio" type="text" size="8" value="<?=$data_corrente?>" maxlength="10" style="font-weight:normal;" 
         	   onKeyUp="mascara_data(this)" id="data_inicio" onChange="verifica_dobradas()">
               
         <span style="font-weight:normal; font-style:italic; color:#C30;
			 <?php if(!isset($_GET['dobradas'])) {
                        echo 'display:none;';
             } ?>
         " id="ferias_dobradas">
         <br>
         <br>
         	(<strong>f&eacute;rias dobradas</strong> a partir de <b><?=$data_dobrada?></b>)
         </span>
         
         <?php if(!empty($faltas) or (isset($_GET['faltas']) and !empty($_GET['faltas']))) { ?>
            <br>
            <br>
            <?php if(!isset($_GET['despreza_faltas'])) { ?>
  <span style="font-weight:normal; font-style:italic; color:#C30;">(<strong><?=$faltas?> faltas</strong> no per�odo)</span>
			<br>
            <br>
            <?php } ?>
    <label id="periodo_faltas">   
       <?php $url_atual = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER ['REQUEST_URI'];
			 $url_nova  = str_replace('&despreza_faltas=true', '', $url_atual); 
			 
			 if(!isset($_GET['despreza_faltas'])) { ?>
             	<a onClick="javascript:window.location='<?=$url_atual?>&despreza_faltas=true'" href="#">Clique aqui para desconsiderar as faltas no per�odo</a>
             <?php } else { ?>
             	<a onClick="javascript:window.location='<?=$url_nova?>'" href="#">Clique aqui para reconsiderar as faltas no per�odo</a>
             <?php } ?>
    </label>
    	<?php } ?>
         
         <br>
         <br>Quantidade de Dias:
         
         	<?php // Considerarando Per�odo de Faltas (Trabalhando Normalmente)
				  if(!isset($_GET['despreza_faltas'])) { ?>
               
                      
        		<select name="quantidade_dias" >
                <?php // Pr�-selecionado quando volta da tela 5
					  if(isset($_GET['quantidade_dias'])) {
							$pre_selected = $_GET['quantidade_dias'];
					  
					  // Sen�o, seleciona pela Quantidade de Dias
					  } else {
							$pre_selected = $qnt_dias;
					  }
				      
					  // In�cio do Loop (de 1 Dia a Quantidade de Dias)
				      for($a=1; $a<=$qnt_dias; $a++) {
						  
							// Executa a Sele��o
							if($a == $pre_selected) {
								$selected = " selected";
							}
							
							// Oculta a div de abono quando � o m�ximo de dias
							if($a == $qnt_dias) {
								$script = " class='oculta'";
							}
							
							// Exibe a div de abono quando n�o � o m�ximo de dias
							if($a != $qnt_dias) {
								$script = " class='exibe'";
							}
							
							// Exibindo os Options
							echo "<option value='".sprintf('%02d', $a)."'$selected$script>$a</option>";
							
							// Resetando a vari�vel $selected para o pr�ximo loop
							unset($selected);
							
					  } ?>
        		</select>
                
                
                <?php // Se Desconsiderar Per�odo de Faltas
					  } else { ?>
                      
                      
                <select name="quantidade_dias" >
                <?php // Pr�-selecionado quando volta da tela 5
					  if(isset($_GET['quantidade_dias'])) {
							$pre_selected = $_GET['quantidade_dias'];
					  
					  // Sen�o, seleciona pela Quantidade de Dias
					  } else {
							$pre_selected = $qnt_dias;
					  }
				      
					  // In�cio do Loop (de 1 Dia a 30 Dias)
				      for($a=1; $a<=$qnt_dias; $a++) {
						  
							// Executa a Sele��o
							if($a == $pre_selected) {
								$selected = " selected";
							}
							
							// Oculta a div de abono quando � o m�ximo de dias
							if($a == $qnt_dias) {
								$script = " class='oculta'";
							}
							
							// Exibe a div de abono quando n�o � o m�ximo de dias
							if($a != $qnt_dias) {
								$script = " class='exibe'";
							}
							
							// Exibindo os Options
							echo "<option value='".sprintf('%02d', $a)."'$selected$script>$a</option>";
							
							// Resetando a vari�vel $selected para o pr�ximo loop
							unset($selected);
							
					  } ?>
        		</select>
                
                
                <?php // Terminando Sele��o de Quantidade de Dias
					  } ?>
                
                
    <label id="periodo_abono" style="
			<?php if(isset($_GET['quantidade_dias']) and $_GET['quantidade_dias'] != $qnt_dias) {
				  		echo 'display:block;';
				  } else { 
				        echo 'display:none';
				  } ?>
        ">
        
        <br>Considerar Per&iacute;odo de Abono:
        
       <input type="checkbox" name="periodo_abono" value="1" 
	   	    <?php if(!isset($_GET['periodo_abono']) or (isset($_GET['periodo_abono']) and !empty($_GET['periodo_abono']))) { 
		   				echo 'checked';
		  		  } ?> 
          >
    </label>
        <br><br>
        <input type="submit" value="Prosseguir" class="botao" style="margin:10px auto;">
        <input type="button" value="Voltar" onClick="javascript:location.href = 'index.php?enc=<?=$link2?>&periodo_aquisitivo=<?=$_REQUEST['periodo_aquisitivo']?>'" class="botao" style="margin:10px auto;">
        <input type="hidden" name="direito_dias" value="<?=$qnt_dias?>" />
        <?php if(isset($_GET['despreza_faltas'])) { ?>
        <input type="hidden" name="despreza_faltas" value="1" />
        <?php } ?>
        <input type="hidden" name="periodo_aquisitivo" value="<?=$_REQUEST['periodo_aquisitivo']?>" />
        <input type="hidden" name="faltas" value="<?=$faltas?>" />
        <input type="hidden" name="faltas_real" value="<?=$faltas_real?>" />
        <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
        <input type="hidden" name="update_movimentos_clt" value="<?=$update_movimentos_clt?>" />
    </form>
       
       </div>
    </td>
  </tr>
</table>










<?php
// Tela 5 (C�lculo das F�rias e Resumo do Pagamento)
break;
case 5:

// Chamando a Classe C�lculos
$Calc = new calculos();
//--------------------------------

// Encriptografando o Bot�o Voltar
$tela = 4;
$link = encrypt("$regiao&$tela&$clt");
$link = str_replace("+","--",$link);
//--------------------------------------

// Verificando se Desprezou Faltas
if(!empty($_POST['despreza_faltas'])) {
	$despreza_faltas = "&despreza_faltas=true";
} else {
	$despreza_faltas = NULL;
}
//---------------------------------------------

// Chamando a Vari�vel de Updates de Movimentos
$update_movimentos_clt = $_POST['update_movimentos_clt'];
//--------------------------------------

// Formatando a Data de In�cio de F�rias
$data_inicio = implode('-', array_reverse(explode('/', $_POST['data_inicio'])));
//-------------------------------------

// Calculando o Fim e Retorno de F�rias
$quantidade_dias = $_POST['quantidade_dias'];
$dataE = explode('-', $data_inicio);
$anoE = $dataE[0];
$mesE = $dataE[1];
$diaE = $dataE[2];
$data_fim = date("Y-m-d", mktime(0,0,0, $mesE, $diaE + $quantidade_dias - 1, $anoE));
$data_retorno = date("Y-m-d", mktime(0,0,0, $mesE, $diaE + $quantidade_dias, $anoE));
//-----------------------------

// Selecionando os Dados do CLT
$qr_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($qr_clt);
//---------------------

// Selecionando o Curso
$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_array($qr_curso);
//-------------------------------

// Definindo o Per�odo Aquisitivo
$periodo_aquisitivo = explode('/', $_POST['periodo_aquisitivo']);
$aquisitivo_ini = $periodo_aquisitivo[0];
$aquisitivo_end = $periodo_aquisitivo[1];
//----------------

// Verificando F�rias Dobradas e Definindo Sal�rio Base
$preview = explode('-', $aquisitivo_end);
$verifica_dobrado = date('Y-m-d', mktime(0,0,0, $preview[1] , $preview[2], $preview[0] + 1));
			
if($verifica_dobrado <= $data_inicio) {
	$salario_base = $row_curso['salario'] * 2;
	$ferias_dobradas = "sim";
	$link_dobradas = "&dobradas=true";
} else {
	$salario_base = $row_curso['salario'];
	$ferias_dobradas = "nao";
	$link_dobradas = NULL;
}
//---------------------------

// Definindo Sal�rio Vari�vel
$variavel_aquisitivo_ini = explode('-', $aquisitivo_ini);
$variavel_aquisitivo_end = explode('-', $aquisitivo_end);

if($variavel_aquisitivo_ini[1] == 12) {
	$limite_variavel1 = "mes_mov = '$variavel_aquisitivo_ini[1]'";
} else {
	$limite_variavel1 = "mes_mov >= '$variavel_aquisitivo_ini[1]'";
}

if($variavel_aquisitivo_end[1] == 1) {
	$limite_variavel2 = "mes_mov = '$variavel_aquisitivo_ini[1]'";
} else {
	$limite_variavel2 = "mes_mov <= '$variavel_aquisitivo_ini[1]'";
}

// Lan�amentos
$qr_variavel1 = mysql_query("SELECT SUM(valor_movimento) AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]' AND lancamento != 2");
$row_variavel1 = mysql_fetch_array($qr_variavel1);

$qr_variavel2 = mysql_query("SELECT SUM(valor_movimento) AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]' AND lancamento != 2");
$row_variavel2 = mysql_fetch_array($qr_variavel2);

$variavel = $row_variavel1['credito'] + $row_variavel2['credito'];
//
/*
// Lan�amentos SEMPRE	
$qr_variavel_sempre1 = mysql_query("SELECT valor_movimento AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]' AND lancamento = '2'");
while($row_variavel_sempre1 = mysql_fetch_array($qr_variavel_sempre1)) {
	$variavel_sempre += $row_variavel_sempre1['credito'];
}

$qr_variavel_sempre2 = mysql_query("SELECT valor_movimento AS credito FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]' AND lancamento = '2'");
while($row_variavel_sempre2 = mysql_fetch_array($qr_variavel_sempre2)) {
	$variavel_sempre += $row_variavel_sempre2['credito'];
}

$variavel_sempre *= 12;
//

$variavel += $variavel_sempre;
			
if(!empty($variavel)) {
	$salario_variavel = $variavel / 12;
}



	//ALTERADO
	if( in_array($regiao,array('28','32')) and  ($preview[0] == '2010' or $preview[0] == '2011')  and empty($salario_variavel)) {
	$salario_variavel = 109;

	}
	////////
  
  */      
        
        //////////////////////////////////////////////////////////////
/////CALCULANDO A M�DIAS DE RENDIMENTOS DOS �LTIMOS 6 MESES
///////////////////////////////////////////////////////////////

$qr_folha = mysql_query("select A.* FROM rh_folha as A
                                INNER JOIN rh_folha_proc as B
                                ON A.id_folha = B.id_folha
                                WHERE A.regiao = '$regiao' AND A.status=3 
                                AND B.status = 3 AND A.terceiro != 1
                                 AND A.data_inicio BETWEEN DATE_SUB(NOW(), INTERVAL 13 MONTH) AND NOW()
                                AND B.id_clt = '$clt';") or die(mysql_error());
while($row_folha = mysql_fetch_assoc($qr_folha)){      
    
    $ids_mov = $row_folha['ids_movimentos_estatisticas'];  

    $qr_movimento  = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_movimento IN($ids_mov) AND tipo_movimento = 'CREDITO'");
    while($row_mov = mysql_fetch_assoc($qr_movimento)){
 
       //POG para acertar a insalubridade do tipo sempre
         if($row_mov['id_mov'] == 56 AND $row_folha['ano'] == 2012 AND $row_mov['valor_movimento'] == '135.60'){
             
           $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += 124.40;         
           $movimentos[$row_mov['nome_movimento']]                             += 124.40;  
        
         } else {
            $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += $row_mov['valor_movimento'];         
            $movimentos[$row_mov['nome_movimento']]         += $row_mov['valor_movimento']; 
         }
}
    
}

foreach($movimentos as $nome_mov => $valor){    
    $salario_variavel += ($valor/12);
}
//////FIM CALCULO DA M�DIA

        
        
$qr_novo_variavel1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]'");
while($row_novo_variavel1 = mysql_fetch_assoc($qr_novo_variavel1)) {
	$update_movimentos_clt .= ','.$row_novo_variavel1['id_movimento'];
}

$qr_novo_variavel2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND tipo_movimento = 'CREDITO' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]'");
while($row_novo_variavel2 = mysql_fetch_assoc($qr_novo_variavel2)) {
	$update_movimentos_clt .= ','.$row_novo_variavel2['id_movimento'];
}
//--------------------




// Definindo Vari�veis
$salario_contratual = number_format($row_curso['salario'],2,".","");
$quantidade_dias_calc = 30;
// $quantidade_dias_calc = cal_days_in_month(CAL_GREGORIAN, $mesE, $anoE);
$salario = ($salario_base / $quantidade_dias_calc) * $quantidade_dias;
$valor_dia = ($salario_base + $salario_variavel) / $quantidade_dias_calc;
$valor_total = $valor_dia * $quantidade_dias;
// $um_terco = ((($salario_base + $salario_variavel) / 30) * $quantidade_dias) / 3;
// $um_terco = $valor_total / 3;
$um_terco = ($salario + $salario_variavel) / 3;
$remuneracao_calc = $valor_total + $um_terco;
//-------------------

// Base para INSS / IRRF / FGTS
$calc_inss_irrf_fgts = ((($row_curso['salario'] + $salario_variavel) / $quantidade_dias_calc) * $quantidade_dias) + (((($row_curso['salario'] / $quantidade_dias_calc) * $quantidade_dias) + $salario_variavel) / 3);

// Verificando Faltas
if(!empty($_POST['faltas'])) {
	$faltas = $_POST['faltas'];
	$link_faltas = "&faltas=$_POST[faltas]";
} else {
	$faltas = 0;
	$link_faltas = "&faltas=$_POST[faltas_real]";
}
//---------------------------------------------

// Verificando Abono Pecuni�rio (Venda de Dias)
if(isset($_POST['periodo_abono'])) {
	$dias_abono_pecuniario = $_POST['direito_dias'] - $_POST['quantidade_dias'];
	$link_abono = "&periodo_abono=$_POST[periodo_abono]";
} else {
	$dias_abono_pecuniario = 0;
	$link_abono = "&periodo_abono=0";
}

if(isset($_POST['periodo_abono']) and !empty($dias_abono_pecuniario)) {
	$abono_pecuniario = $valor_dia * $dias_abono_pecuniario;
	$umterco_abono_pecuniario = $abono_pecuniario / 3;
}
//---------------------------------------

// Verificando a Data de In�cio de F�rias
if(empty($_POST['data_inicio'])) {
	echo "<script language='JavaScript'>location.href='index.php?enc=$link&periodo_aquisitivo=$_POST[periodo_aquisitivo]&data_inicio=$_POST[data_inicio]&quantidade_dias=$_POST[quantidade_dias]$link_abono$link_dobradas&data=nulo';
		  </script>";
    exit;
}
//----------------


$data_calc = date('Y').'-01-01'; //USADA PARA C�LCULO DE INSS E IRRF DE ACORDO COM O ANO DE PORCESSAMENTO DAS F�RIAS
                                 //ESSA CONDI��O PARA QUANDO O PER�ODO DE F�RIAS FOR NO ANO SEGUINTE, CALCULAR COM A TABELA DO ANO VIGENTE

if($row_clt['desconto_inss'] != 1){		
// Calculando INSS
    
 $BASE_INSS = $calc_inss_irrf_fgts;   
 
$Calc -> MostraINSS($BASE_INSS,$data_calc);
$inss = $Calc -> valor;
$porcentagem_inss = $Calc -> percentual;
}
//--------------
			
// Calculando IR
$BASE_IRRF = $BASE_INSS - $inss;
$Calc -> MostraIRRF($BASE_IRRF,$clt,$regiao,$data_calc);
$ir = $Calc -> valor;

if($ir !=0){
    $PERCENTUAL_IRRF        = $Calc->percentual;
    $VALOR_DDIR             = $Calc->valor_deducao_ir_total;
    $QNT_DEPENDENTES_IRRF   = $Calc->total_filhos_menor_21;
    $PARCELA_DEDUCAO_IRRF   = $Calc->valor_fixo_ir;
} else {
    $BASE_IRRF = 0;
}


//----------------

// Calculando FGTS
$fgts = $calc_inss_irrf_fgts * 0.08;
//----------------------------
			
// Buscando Pens�o Alimenticia
$pensao_aquisitivo_ini = explode('-', $aquisitivo_ini);
$pensao_aquisitivo_end = explode('-', $aquisitivo_end);

if($pensao_aquisitivo_ini[1] == 12) {
	$limite_pensao1 = "mes_mov = '$pensao_aquisitivo_ini[1]'";
} else {
	$limite_pensao1 = "mes_mov >= '$pensao_aquisitivo_ini[1]'";
}

if($pensao_aquisitivo_end[1] == 1) {
	$limite_pensao2 = "mes_mov = '$pensao_aquisitivo_ini[1]'";
} else {
	$limite_pensao2 = "mes_mov <= '$pensao_aquisitivo_ini[1]'";
}

$qr_pensao1 = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = '$pensao_aquisitivo_ini[0]' AND id_mov IN('54','63') ORDER BY id_movimento DESC");
$row_pensao1 = mysql_fetch_array($qr_pensao1);
$numero_pensao1 = mysql_num_rows($qr_pensao1);

$qr_pensao2 = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = '$pensao_aquisitivo_end[0]' AND id_mov IN('54','63') ORDER BY id_movimento DESC");
$row_pensao2 = mysql_fetch_array($qr_pensao2);
$numero_pensao2 = mysql_num_rows($qr_pensao2);

$numero_pensao = $numero_pensao1 + $numero_pensao2;

if(!empty($numero_pensao)) {
	
	if(!empty($numero_pensao2)) {
		$tipo_pensao = $row_pensao2['id_mov'];
	} else {
		$tipo_pensao = $row_pensao1['id_mov'];
	}

	if($tipo_pensao == "54") {
		$ps = 0.15;
	} elseif($tipo_pensao == "63") {
		$ps = 0.30;
	}
	
	$pensao_alimenticia = number_format($remuneracao_calc * $ps,2,".","");

}

$qr_novo_pensao1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = '$pensao_aquisitivo_ini[0]' AND id_mov IN('54','63')");
while($row_novo_pensao1 = mysql_fetch_assoc($qr_novo_pensao1)) {
	$update_movimentos_clt .= ','.$row_novo_pensao1['id_movimento'];
}

$qr_novo_pensao2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = '$pensao_aquisitivo_end[0]' AND id_mov IN('54','63')");
while($row_novo_pensao2 = mysql_fetch_assoc($qr_novo_pensao2)) {
	$update_movimentos_clt .= ','.$row_novo_pensao2['id_movimento'];
}
//---------------------

// Calculando Vari�veis
$remuneracao_base = number_format($salario + $salario_variavel + $abono_pecuniario,2,".","");
$total_remuneracoes = number_format($valor_total + $um_terco + $abono_pecuniario + $umterco_abono_pecuniario,2,".","");
$total_descontos = number_format($pensao_alimenticia + $inss + $ir,2,".","");
$total_liquido = number_format($total_remuneracoes - $total_descontos,2,".","");
//----------------------------

// Calculando Meses Diferentes
$dias_mes = cal_days_in_month(CAL_GREGORIAN, $mesE, $anoE);
$dias_ferias1 = $dias_mes - $diaE + 1;
$dias_ferias2 = $quantidade_dias - $dias_ferias1;

$valor_total1 = $dias_ferias1 * $valor_dia;
$acrescimo_constitucional1 = $valor_total1 / 3;
$total_remuneracoes1 = $valor_total1 + $acrescimo_constitucional1 + $abono_pecuniario + $umterco_abono_pecuniario;

$valor_total2 = $dias_ferias2 * $valor_dia;
$acrescimo_constitucional2 = $valor_total2 / 3;
$total_remuneracoes2 = $valor_total2 + $acrescimo_constitucional2;
//-------------------------
		



// Formata��o para exibi��o
$aquisitivo_iniT           = implode('/', array_reverse(explode('-', $aquisitivo_ini)));
$aquisitivo_endT		   = implode('/', array_reverse(explode('-', $aquisitivo_end)));
$data_inicioT			   = implode('/', array_reverse(explode('-', $data_inicio)));
$data_fimT			  	   = implode('/', array_reverse(explode('-', $data_fim)));
$data_retornoT			   = implode('/', array_reverse(explode('-', $data_retorno)));
$salario_contratualT 	   = number_format($salario_contratual,2,",","");
$salarioT 			       = number_format($salario,2,",","");
$salario_variavelT 	       = number_format($salario_variavel,2,",","");
$remuneracao_baseT 	       = number_format($remuneracao_base,2,",","");
$um_tercoT 			       = number_format($um_terco,2,",","");
$valor_diaT 	           = number_format($valor_dia,2,",","");
$valor_totalT       	   = number_format($valor_total,2,",","");
$inssT 			 = number_format($inss,2,",","");
$irT 				       = number_format($ir,2,",","");
$fgtsT                     = number_format($fgts,2,",","");
$pensao_alimenticiaT       = number_format($pensao_alimenticia,2,",","");
$total_remuneracoesT	   = number_format($total_remuneracoes,2,",","");
$total_descontosT          = number_format($total_descontos,2,",","");
$total_liquidoT            = number_format($total_liquido,2,",","");
$abono_pecuniarioT    	   = number_format($abono_pecuniario,2,",","");
$umterco_abono_pecuniarioT = number_format($umterco_abono_pecuniario,2,",","");
//-----------------------------------------
?>

<table width="95%" bgcolor="#ffffff" align="center" cellspacing="0">
<tr>
	<td align="right"><?php include('../../reportar_erro.php');?></td>
</tr>
  <tr>
    <td>
     <div align="center" style="font-family:Arial; font-size:18px; color:#FFF; background:#036">
          <?php echo $clt." - ".$row_clt['nome']; ?>
     </div>
     <div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:4px;">
          <?php echo "<b>Unidade:</b> ".$row_clt['locacao']."<br><b>Atividade:</b> ".$row_curso['nome']."<br><b>Sal�rio Contratual:</b> R$ ".$salario_contratualT; ?>
     </div>
    </td>
  </tr>
  <tr bgcolor="#cccccc" class="linha">
    <td height="112" align="center" valign="middle" bgcolor="#F7F7F7">
       <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" id="form1">
         <br>
         <table width="60%" cellspacing="0" cellpadding="2" style="border:solid 1px #ccc; line-height:24px;">
           <tr>
             <td height="40" colspan="4" bgcolor="#CCCCCC">
               <div align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold;">Resumo do Per&iacute;odo de F&eacute;rias</div>
             </td>
           </tr>
           <?php if($verifica_dobrado <= $data_inicio) { ?>
                <tr>
                  <td colspan="4" align="center">
                	<span class="linha" style="color:#C30;">(F�rias Dobradas)</span></td>
                </tr>
           <?php } ?>
           <tr>
             <td width="20%" colspan="2">
             		<div align="right" class="linha">Per�odo Aquisitivo:</div>
             </td>
             <td width="80%" colspan="2">
                    &nbsp;<?php echo $aquisitivo_iniT.' � '.$aquisitivo_endT; ?>
             </td>
           </tr>
           <?php if(!empty($faltas)) { ?>
           <tr>
             <td colspan="2">
             		<div align="right" class="linha">Faltas no Per&iacute;odo:</div>
             </td>
             <td colspan="2">
             		&nbsp;<?=$faltas?> dias
             </td>
           </tr>
           <?php } ?>
           <tr>
             <td colspan="2">
             		<div align="right" class="linha">Per&iacute;odo de F&eacute;rias:</div>
             </td>
             <td colspan="2">
             	    &nbsp;<?php echo $data_inicioT.' � '.$data_fimT; ?>
             </td>
           </tr>
           <tr>
             <td colspan="2">
             		<div align="right" class="linha">Quantidade de Dias:</div>
             </td>
             <td colspan="2">
             		&nbsp;<?=$quantidade_dias?> dias</td>
           </tr>
           <tr>
             <td colspan="2">
             		<div align="right" class="linha">Data de Retorno:</div>
             </td>
             <td colspan="2">
             	   &nbsp;<?php echo $data_retornoT; ?>
             </td>
           </tr>
           <?php if(!empty($dias_abono_pecuniario)) { ?>
           <tr>
             <td colspan="2">
             		<div align="right" class="linha">Dias de Abono Pecuni&aacute;rio:</div>
             </td>
             <td colspan="2">
                    &nbsp;<?=$dias_abono_pecuniario?> dias
             </td>
           </tr>
           <?php } ?>
           <tr>
             <td height="40" colspan="4" bgcolor="#CCCCCC">
               <div align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold;">Resumo do Pagamento de F&eacute;rias</div>
             </td>
           </tr>      
           <tr>
             <td width="30%">
             		<div align="right" class="linha">Sal&aacute;rio:</div>
             </td>
             <td width="17%">
             	R$ <?=$salarioT?>
             </td>
             <td width="30%">
             		<div align="right" class="linha">Sal&aacute;rio Vari&aacute;vel:</div>
                       
             </td>
             <td width="23%">
             	R$ <?=$salario_variavelT?>  
                <a href="action.confere_movimentos.php?id_clt=<?php echo $clt;?>&regiao=<?php echo $regiao;?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe'})">ver</a>            
                       
             </td>
           </tr>
           <tr>
             <td>
             	<div align="right" class="linha">1/3 do Sal&aacute;rio: </div>
             </td>
             <td>
             	R$ <?=$um_tercoT?>
             </td>
             <?php if(!empty($dias_abono_pecuniario)) { ?>
             <td>
                <div align="right" class="linha">Abono Pecuni&aacute;rio:</div>
             </td>
             <td>
                R$ <?=$abono_pecuniarioT?>
             </td>
           </tr>   
           <tr>    
             <td>
             	<div align="right" class="linha">1/3 Abono Pecuni&aacute;rio:</div>
             </td>
             <td>
                R$ <?=$umterco_abono_pecuniarioT?>
             </td>
             <td>
             	<div align="right" class="linha">Remunera&ccedil;&otilde;es:</div>
             </td>
             <td>
             	R$ <?=$total_remuneracoesT?>
             </td>
           </tr>
           <?php } else { ?> 
           <td>
             	<div align="right" class="linha">Remunera&ccedil;&otilde;es:</div>
             </td>
             <td>
             	R$ <?=$total_remuneracoesT?>
             </td>
           </tr>
           <?php } ?>
           <tr>
             <td>
             	<div align="right" class="linha">INSS: </div>
             </td>
             <td>
             	R$ <?=$inssT?>
               </td>
             <td>
             	<div align="right" class="linha">IRRF:</div>
             </td>
             <td>
                R$ <?=$irT?>
             </td>
           </tr>
           <tr>
             <td>
               <div align="right" class="linha">Pens&atilde;o Aliment&iacute;cia:</div>
             </td>
             <td>
                R$ <?=$pensao_alimenticiaT?>
       		 </td>
             <td>
             	<div align="right" class="linha">Descontos:</div>
             </td>
             <td>
               R$ <?=$total_descontosT?>
              </td>
           </tr>
           <tr>
             <td height="40" colspan="4" align="center" bgcolor="#CCCCCC">
             	<span class="linha">L&Iacute;QUIDO A RECEBER:&nbsp;</span> R$ <?=$total_liquidoT?>
             </td>
           </tr>
         </table>
         
        <br>
        <br>
                
                <input type="hidden" name="tela" value="6" />
                <input type="hidden" name="id_clt" value="<?=$clt?>" />
                <input type="hidden" name="nome" value="<?=$row_clt['nome']?>" />
                <input type="hidden" name="regiao" value="<?=$regiao?>" />
                <input type="hidden" name="projeto" value="<?=$_POST['projeto']?>" />
                <input type="hidden" name="mes" value="<?php echo substr($_POST['data_inicio'], 3, 2); ?>" />
                <input type="hidden" name="ano" value="<?php echo substr($_POST['data_inicio'], 6, 4); ?>" />
                <input type="hidden" name="data_aquisitivo_ini" value="<?=$aquisitivo_ini?>" />
                <input type="hidden" name="data_aquisitivo_fim" value="<?=$aquisitivo_end?>" />
                <input type="hidden" name="data_inicio" value="<?=$data_inicio?>">
                <input type="hidden" name="data_fim" value="<?=$data_fim?>">
                <input type="hidden" name="data_retorno" value="<?=$data_retorno?>">
                <input type="hidden" name="salario" value="<?=$row_curso['salario']?>" />
                <input type="hidden" name="salario_variavel" value="<?=$salario_variavel?>" />
                <input type="hidden" name="remuneracao_base" value="<?=$remuneracao_base?>" />
                <input type="hidden" name="dias_ferias" value="<?=$quantidade_dias?>" />
                <input type="hidden" name="valor_dias_ferias" value="<?=$valor_dia?>" />
                <input type="hidden" name="valor_total_ferias" value="<?=$valor_total?>" />
                <input type="hidden" name="umterco" value="<?=$um_terco?>" />
                <input type="hidden" name="total_remuneracoes" value="<?=$total_remuneracoes?>" />
                <input type="hidden" name="pensao_alimenticia" value="<?=$pensao_alimenticia?>" />
                <input type="hidden" name="inss" value="<?=$inss?>" />
                <input type="hidden" name="inss_porcentagem" value="<?=$porcentagem_inss?>" />
                <input type="hidden" name="ir" value="<?=$ir?>" />
                <input type="hidden" name="fgts" value="<?=$fgts?>" />
                <input type="hidden" name="total_descontos" value="<?=$total_descontos?>" />
                <input type="hidden" name="total_liquido" value="<?=$total_liquido?>" />
                <input type="hidden" name="abono_pecuniario" value="<?=$abono_pecuniario?>" />
                <input type="hidden" name="umterco_abono_pecuniario" value="<?=$umterco_abono_pecuniario?>" />
                <input type="hidden" name="dias_abono_pecuniario" value="<?=$dias_abono_pecuniario?>" />
                <input type="hidden" name="faltas" value="<?=$faltas?>">
                <input type="hidden" name="faltasano" value="<?=$faltasano?>" />
                <input type="hidden" name="dias_mes" value="<?=$dias_mes?>" />
                <input type="hidden" name="dias_ferias1" value="<?=$dias_ferias1?>" />
                <input type="hidden" name="dias_ferias2" value="<?=$dias_ferias2?>" />
                <input type="hidden" name="valor_total_ferias1" value="<?=$valor_total1?>" />
                <input type="hidden" name="acrescimo_constitucional1" value="<?=$acrescimo_constitucional1?>" />
                <input type="hidden" name="total_remuneracoes1" value="<?=$total_remuneracoes1?>" />
                <input type="hidden" name="valor_total_ferias2" value="<?=$valor_total2?>" />
                <input type="hidden" name="acrescimo_constitucional2" value="<?=$acrescimo_constitucional2?>" />
                <input type="hidden" name="total_remuneracoes2" value="<?=$total_remuneracoes2?>" />
                <input type="hidden" name="ferias_dobradas" value="<?=$ferias_dobradas?>" />
                <input type="hidden" name="user" value="<?=$_COOKIE['logado']?>" />
                <input type="hidden" name="base_inss" value="<?=$BASE_INSS?>" />
                <input type="hidden" name="base_irrf" value="<?=$BASE_IRRF?>" />
                <input type="hidden" name="percentual_irrf" value="<?=$PERCENTUAL_IRRF?>" />
                <input type="hidden" name="valor_ddir" value="<?=$VALOR_DDIR?>" />
                <input type="hidden" name="qnt_dependente_irrf" value="<?=$QNT_DEPENDENTES_IRRF?>" />
                <input type="hidden" name="parcela_deducao_irrf" value="<?=$PARCELA_DEDUCAO_IRRF?>" />
                <input type="hidden" name="status" value="1" />
                <input type="hidden" name="update_movimentos_clt" value="<?=$update_movimentos_clt?>" />
             <div style="width:220px; margin:0px auto;">
                <input type="submit" value="Concluir" class="botao" style="width:100px; float:left;">
                <input type="button" value="Voltar" class="botao" style="width:100px; float:left;" onClick="javascript:location.href = 'index.php?enc=<?=$link?>&periodo_aquisitivo=<?=$_POST['periodo_aquisitivo']?>&data_inicio=<?=$_POST['data_inicio']?>&quantidade_dias=<?=$_POST['quantidade_dias']?><?=$link_abono?><?=$link_faltas?><?=$link_dobradas?><?=$despreza_faltas?>'">
             </div>
        </form>
      </td>
    </tr>
    <tr>
      <td colspan="4" bgcolor="#F7F7F7">&nbsp;</td>
    </tr>
</table>








<?php
// Tela 6 (Lan�ando no Banco de Dados e Redirecionando para a p�gina PDF)
break;
case 6:

$id_clt = $_POST['id_clt'];
$nome = $_POST['nome'];
$regiao = $_POST['regiao'];
$projeto = $_POST['projeto'];
$mes = $_POST['mes'];
$ano = $_POST['ano'];
$data_aquisitivo_ini = $_POST['data_aquisitivo_ini'];
$data_aquisitivo_fim = $_POST['data_aquisitivo_fim'];
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];
$data_retorno = $_POST['data_retorno'];
$salario = $_POST['salario'];
$salario_variavel = $_POST['salario_variavel'];
$remuneracao_base = $_POST['remuneracao_base'];
$dias_ferias = $_POST['dias_ferias'];
$valor_dias_ferias = $_POST['valor_dias_ferias'];
$valor_total_ferias = $_POST['valor_total_ferias'];
$umterco = $_POST['umterco'];
$total_remuneracoes = $_POST['total_remuneracoes'];
$pensao_alimenticia = $_POST['pensao_alimenticia'];
$inss = $_POST['inss'];
$inss_porcentagem = substr($_POST['inss_porcentagem'], 2, 2);
$ir = $_POST['ir'];
$fgts = $_POST['fgts'];
$total_descontos = $_POST['total_descontos'];
$total_liquido = $_POST['total_liquido'];
$abono_pecuniario = $_POST['abono_pecuniario'];
$umterco_abono_pecuniario = $_POST['umterco_abono_pecuniario'];
$dias_abono_pecuniario = $_POST['dias_abono_pecuniario'];
$faltas = $_POST['faltas'];
$faltasano = $_POST['faltasano'];
$dias_mes = $_POST['dias_mes'];
$dias_ferias1 = $_POST['dias_ferias1'];
$dias_ferias2 = $_POST['dias_ferias2'];
$valor_total_ferias1 = $_POST['valor_total_ferias1'];
$acrescimo_constitucional1 = $_POST['acrescimo_constitucional1'];
$total_remuneracoes1 = $_POST['total_remuneracoes1'];
$valor_total_ferias2 = $_POST['valor_total_ferias2'];
$acrescimo_constitucional2 = $_POST['acrescimo_constitucional2'];
$total_remuneracoes2 = $_POST['total_remuneracoes2'];
$diasmes = $_POST['diasmes'];
$ferias_dobradas = $_POST['ferias_dobradas'];
$user = $_POST['user'];
$status = $_POST['status'];
$update_movimentos_clt = $_POST['update_movimentos_clt'];


$BASE_INSS              = $_POST['base_inss'];
$BASE_IRRF               = $_POST['base_irrf'];
$PERCENTUAL_IRRF        = $_POST['percentual_irrf'];
$VALOR_DDIR             = $_POST['valor_ddir'];
$QNT_DEPENDENTES_IRRF   = $_POST['qnt_dependete_irrf'];
$PARCELA_DEDUCAO_IRRF     = $_POST['parcela_deducao_irrf'];
// Update no Movimentos CLT

mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '0' WHERE id_movimento IN($update_movimentos_clt)");

// Fim do Update nos Movimentos

// Inserindo nas F�rias


// Inserindo nas F�rias



mysql_query("
INSERT INTO rh_ferias
(id_clt,nome,regiao,projeto,mes,ano,data_aquisitivo_ini,data_aquisitivo_fim,data_ini,data_fim,data_retorno,salario,salario_variavel,remuneracao_base,dias_ferias,valor_dias_ferias,valor_total_ferias,umterco,total_remuneracoes,pensao_alimenticia,inss,inss_porcentagem,ir,fgts,total_descontos,total_liquido,abono_pecuniario,umterco_abono_pecuniario,dias_abono_pecuniario,faltas,faltasano,diasmes,ferias_dobradas,valor_total_ferias1,valor_total_ferias2,acrescimo_constitucional1,acrescimo_constitucional2,total_remuneracoes1,total_remuneracoes2,movimentos,user,data_proc,status, base_inss,base_irrf, percentual_irrf,valor_ddir, qnt_dependente_irrf, parcela_deducao_irrf ) 
VALUES 
('$id_clt','$nome','$regiao','$projeto','$mes','$ano','$data_aquisitivo_ini','$data_aquisitivo_fim','$data_inicio','$data_fim','$data_retorno','$salario','$salario_variavel','$remuneracao_base','$dias_ferias','$valor_dias_ferias','$valor_total_ferias','$umterco','$total_remuneracoes','$pensao_alimenticia','$inss','$inss_porcentagem','$ir','$fgts','$total_descontos','$total_liquido','$abono_pecuniario','$umterco_abono_pecuniario','$dias_abono_pecuniario','$faltas','$faltasano','$diasmes','$ferias_dobradas','$valor_total_ferias1','$valor_total_ferias2','$acrescimo_constitucional1','$acrescimo_constitucional2','$total_remuneracoes1','$total_remuneracoes2','$update_movimentos_clt','$user',NOW(),'$status', '$BASE_INSS','$BASE_IRRF', '$PERCENTUAL_IRRF', '$VALOR_DDIR', '$QNT_DEPENDENTES_IRRF', '$PARCELA_DEDUCAO_IRRF')") or die (mysql_error());

$id_ferias = mysql_insert_id();

mysql_query("UPDATE rh_clt SET status='40' WHERE id_clt = '$id_clt'");

mysql_query("INSERT INTO rh_eventos (id_clt, id_regiao, id_projeto, nome_status, cod_status, id_status, data, data_retorno, dias, obs, status, status_reg) VALUES ('$id_clt', '$regiao', '$projeto', 'F�rias', '40', '1', '$data_ini', '$data_retorno', '$dias_ferias', NULL, '1', '1')");

// Encriptografando a Vari�vel
$link = encrypt("$regiao&$id_clt&$id_ferias&0"); 
$link = str_replace("+","--",$link);
// -----------------------------

print "<script>location.href = 'ferias.php?enc=$link';</script>";
break;
}
?>
</div>
</body>
</html>