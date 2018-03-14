<?php
include('../../conn.php');

include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/acoes.class.php";



function formato_data_txt($data){
	
	$dia = substr($data,0,2);
	$mes = substr($data,2,2);
	$ano = substr($data,4,4);
	
	
	return $ano.'-'.$mes.'-'.$dia;
	
}


function formato_hora($hora){
	
	$H = substr($hora,0,2);
	$M = substr($hora,2,2);
	
	return $H.':'.$M;
	
	
}



$qr_funcionario  = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_assoc($qr_funcionario);

$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$row_funcionario[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);
 

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];

$link = "0";
$enc = "0";
$decript = "0";
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

// Id da Folha
$enc   = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$folha = $enc[1];





$qr_folha_proc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' ORDER BY nome");






$arquivo = fopen('arquivos_ponto/949.txt', 'r');

 while (!feof($arquivo)) {
	 
        $buffer 			= fgets($arquivo);    	
		$registro 			= substr($buffer,9,1);		
				
		/*
		///////////DAODS DA EMPRESA REGISTRO 2
		if($registro == 1){
		$cnpj = substr($buffer,11,14);
		
		
		//verifica CNPJ
		
		if($cnpj != $row_master['cnpj']){
		
				echo $cnpj;
		
			
		}
			
		}*/
		
		
		
		
		
		
		
		
		
	
		
		
		
		
		////REGISTRO DE MARCAÇÃO DE PONTO: REGISTRO 3
		if($registro == 3) {
			$data_marcacao 		= formato_data_txt(substr($buffer,10,8));
			$horario_marcacao 	=  formato_hora(substr($buffer, 18,4));
			$pis 				= substr($buffer, 22,12);
			
			$FUNCIONARIOS[]   = $pis; 
			$DATA[$pis][] 	  = $data_marcacao; 
			$HORARIOS[$pis][] = $horario_marcacao; 
	    }
		
				
		
    }
	
	$FUNCIONARIOS = array_unique($FUNCIONARIOS);
	
	foreach($FUNCIONARIOS  as $pis){
	
		
		
			
		for($i = 0; $i < sizeof($DATA[$pis]);$i++ ){
			
			$hora_entrada 	= substr($HORARIOS[$pis][$i], 0,2);
			$minuto_entrada = substr($HORARIOS[$pis][$i], 3,2);			
			list($ano_entrada,$mes_entrada,$dia_entrada) = explode('-',$DATA[$pis][$i]);		
			$data_segundos_entrada = @mktime($hora_entrada,$minuto_entrada,0,$mes_entrada, $dia_entrada, $ano_entrada);
			
			
			$hora_saida	  = substr($HORARIOS[$pis][$i+1], 0,2);
			$minuto_saida = substr($HORARIOS[$pis][$i+1], 3,2);			
			list($ano_saida,$mes_saida,$dia_saida) = explode('-',$DATA[$pis][$i+1]);		
			$data_segundos_saida = @mktime($hora_saida,$minuto_saida,0,$mes_saida, $dia_saida, $ano_saida);
			$i = $i+2;
		
		
			$TOTAL_HORAS[$pis]['horas_trabalhadas'] += (int)($data_segundos_saida - $data_segundos_entrada)/3600;
			
			//echo (int)$TOTAL_HORAS[$pis].' Período: '.date('d/m/Y',$data_segundos_entrada).' - '.date('d/m/Y', $data_segundos_saida).'<br>';
			
			
		}
			
		
		$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE pis = $pis") or die(mysql_error());
		$clt    = mysql_fetch_assoc($qr_clt);
		
		$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$clt[id_curso]'");
		$curso 	  =  mysql_fetch_assoc($qr_curso);
		
			
		$NOME[$pis]      = $clt['nome'];	
		$ATIVIDADE[$pis] = $curso['nome'];
				
		$HORARIO_ATIVIDADE[$pis] =  $curso['hora_mes'];
		
		$diferenca = $TOTAL_HORAS[$pis]['horas_trabalhadas'] - $HORARIO_ATIVIDADE[$pis] ;
		
		
		
		if($TOTAL_HORAS[$pis]['horas_trabalhadas'] < $HORARIO_ATIVIDADE[$pis] ) {
		
			$TOTAL_HORAS[$pis]['hora_desconto'] = $diferenca;
			
		} elseif($TOTAL_HORAS[$pis]['horas_trabalhadas'] > $HORARIO_ATIVIDADE[$pis] ){
			
			$TOTAL_HORAS[$pis]['hora_extra'] = $diferenca;
		
			
		}
		
		
		
		
		
		
		
	}
	
	
	
	
/*
 print_R(array_unique($FUNCIONARIOS));
 echo '<br>';
 print_R($DATA['132111239600']);
 echo '<br>';
 print_R($HORARIOS['132111239600']);
 */

?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha de Pagamento</title>
<link rel="shortcut icon" href="../../favicon.ico" />
<link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../js/abas_anos.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function() {
	$('#data_ini').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	
});

function MM_preloadImages() {
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}


	
	

</script>
<style>

fieldset {
padding:2px;
margin-top:30px;

}
table.folhas {
	width:100%;
	font-weight:bold;
	margin:0px auto;
	font-size:11px;
	text-align:center;
}
</style>
</head>
</head>
<body onLoad="MM_preloadImages('imagens/processar2.gif')">
<div id="corpo">
	<div id="conteudo">
           
        
        <div style="float:right;"> <?php include('../../reportar_erro.php');?> </div>       
        <div class="right"></div>             
                                  
    	  <br /><img src="../../imagens/logomaster<?=$row_funcionario['id_master']?>.gif" width="110" height="79"/>
              
			<h3> FOLHA DE PAGAMENTO - <?php echo $row_regiao['regiao']?>
            		<br>
                    FOLHA: <?php echo $row_folha['id_folha']?>
                    <br>
                    <?php echo $row_folha['mes'].'/'.$row_folha['ano'];?>
            
            </h3>
      
     <br>
     
     
     <table class="relacao" border="0">
     <tr class="secao_nova"> 
     	<td>NOME</td>
        <td>PIS</td>
        <td>PONTO</td>
		<td>Horas Trabalhadas</td>
		<td>Horas Desconto</td>
		<td>Horas Extras</td>
     </tr>
		     
     
     <?php	print_r($TOTAL_HORAS);
     while($row_participantes = mysql_fetch_assoc($qr_folha_proc)) :
	
	$pis =trim( mysql_result(mysql_query("SELECT pis FROM rh_clt WHERE id_clt = '$row_participantes[id_clt]'"), 0)); 
	
	$CLTS_FOLHA[] =  $pis;
	
	if(!in_array($pis, $FUNCIONARIOS)) { $linha =  'style="background-color:#FFB9B9"'; } else { $linha=''; }
	

	
	
	 ?>	 
	 <tr class="linha_um " height="40" <?php echo $linha; ?>>
     	<td><?php echo $row_participantes['nome'];?></td>
        <td><?php echo $pis;?></td>        
        <td><?php echo (int)$TOTAL_HORAS[$pis]['horas_trabalhadas'];?> / <?php  echo (int)$HORARIO_ATIVIDADE[$pis]; ?></td>
      	<td><?php echo (int)$TOTAL_HORAS[$pis]['hora_desconto']; ?></td>
        <td><?php echo (int)$TOTAL_HORAS[$pis]['hora_extra'];?></td>
        
     </tr>	 
		 
	 <?php endwhile; ?>
     </table>
   
   
   
   
   
   
   
   
   
   
   
   
   
     <table  class="relacao"> 
   		<tr class="titulo_tabela1">
        	<td>NOME</td>
            <td>PIS</td>
            <td>ATIVIDADE</td>           
			<td>Horas Trabalhadas</td>
			<td>Horas Desconto</td>
			<td>Horas Extras</td>
        </tr>  
	  <?php
	  foreach($FUNCIONARIOS as $pis){
	    
		//if(!in_array($CLTS_FOLHA, $pis){ $linha = 'style="background-color:#FFA8A8"' ; }
		
		
		
		
	  ?>
	  <tr>
      	<td><?php echo $NOME[$pis]; ?></td>
        <td><?php echo $pis;?></td>
        <td><?php echo $ATIVIDADE[$pis];?></td>        
        <td><?php echo (int)$TOTAL_HORAS[$pis]['horas_trabalhadas'];?> / <?php  echo (int)$HORARIO_ATIVIDADE[$pis]; ?></td>
      	<td><?php echo (int)$TOTAL_HORAS[$pis]['hora_desconto']; ?></td>
        <td><?php echo (int)$TOTAL_HORAS[$pis]['hora_extra'];?></td>
      </tr>
	  		  
	  
	<?php    }  ?>  
	        
     </table>
     
     
      </div>      
</div>
</body>
</html>