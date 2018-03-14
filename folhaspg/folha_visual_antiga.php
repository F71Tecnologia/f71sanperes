<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit();
}

include('../conn.php');
include('../funcoes.php');
include('../classes/cooperativa.php');

$id = $_REQUEST['id'];

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao) = explode('&', decrypt(str_replace('--','+',$_REQUEST['enc'])));
//

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$id_user = $_COOKIE['logado'];

$sql = "SELECT * FROM funcionario where id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$ano = date('Y');

$meses = array('ERRO','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
?>
<html>
<head>
<title>Folha de Pagamento Cooperado / Aut&ocirc;nomo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico" />
<link href="../net1.css" rel="stylesheet" type="text/css">
<link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
<script language="javascript">
function validaForm() {
d = document.form1;
if (d.data_ini.value == ""){
	alert("A data deve ser preenchida!");
	d.data_ini.focus();
	return false;
}
return true;   
}

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
       alert("Data digitada é inválida, digite novamente!"); 
       d.value = "";  
       d.focus();  
    }  
  
}

function confirm_entry(a,b){
	var Regiao = a;
	var Folha = b;
	
	input_box=confirm("Deseja realmente DELETAR?");
	
	if (input_box==true){ 
		// Output when OK is clicked
		// alert (\"You clicked OK\"); 
		location.href="folha.php?id=12&regiao=" + Regiao + "&folha=" + Folha;
		}else{
		// Output when Cancel is clicked
		// alert (\"You clicked cancel\");
	}

}
</script>
</head>
<?php 
// BLOQUEIO PARA FOLHAS E PESSOAL DA ADMINISTRAÇÃO 02-09-2010 - 16:20 - SJR
$bloqueio_administracao = array('5','9');
	  if(!in_array($id_user,$bloqueio_administracao)and $regiao==15) {
	       echo '<p>&nbsp;</p>
           Acesso somente para pessoas autorizadas.';
	       exit();
	  }

switch($id) {
case 9:
?>
<script type="text/javascript">
$(function() {
	$('#data_ini').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});
</script>
<style type="text/css">
body {
	margin:10px;
}
.secao {
	text-align:right;
	font-weight:bold;
}
.secao_pai {
	font-weight:bold; 
	background-color:#555; 
	color:#FFF; 
	text-align:center; 
	padding:8px;
}
td.sub_secao {
	text-align:center;
	font-weight:bold;
	font-style:italic;
	background-color:#FAFAFA;
	font-size:13px;
}
td.sub_secao span {
	
}
</style>
<table width="90%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="center" style="border:1px solid #BBB;">
  <tr>
    <td width="3%">&nbsp;</td>
    <td width="94%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" colspan="3">

          <table width="90%" border="0" style="margin-bottom:10px; margin-bottom:20px;">
           <tr>
           	<td align="right"><?php include('../reportar_erro.php');?></td>
           </tr>
           
           <tr>
            <td align="center" class="show">
              <br /><img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" width="110" height="79">
              <br /><br />GERENCIAMENTO DE FOLHA DE PAGAMENTO COOPERADO / AUTÔNOMO<br /><br />
            </td>
           </tr>
          </table>
  <?php
	$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '1'"));
	if($verifica_acoes != 0) {
	 ?>
  
  
  
  <table width="90%" border="0" cellspacing="0" cellpadding="8" bgcolor="#FAFAFA">
    <tr>
      <td class="secao_pai">
         REGI&Atilde;O DE <?php echo strtoupper(strtr($row_regiao['regiao'] ,"áéíóúâêôãõàèìòùç","ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ")); ?>
      </td>
    </tr>
    <tr>
      <td align="center">
      
  <form action="folha.php" method="post" name="form1" onSubmit="return validaForm()" target="iframe1">
  <table width="90%" cellspacing="0" cellpadding="8" style="border:solid 1px #DDD; background-color:#EEE; margin-top:10px;">
    <tr>
      <td width="31%" class="secao">Projeto:</td>
      <td width="69%">
      <select name="id_projeto" id="id_projeto" class='campotexto'>
        <?php $result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");
			  $i = "0";
				while($row_pro = mysql_fetch_array($result_pro)) {
					echo "<option value='$row_pro[0]'>$row_pro[nome]</option>";
					$projetos_regi[$i] = $row_pro[0];
					$i ++;
				} ?>
      </select>
      </td>
    </tr>
    <tr>
       <td class="secao">M&ecirc;s de Refer&ecirc;ncia:</td>
       <td>
        <select name="mes" id="mes" class='campotexto' >
          <?php
		  	$mesNow = date('m');
			$result_meses = mysql_query("SELECT * FROM ano_meses");
			while($row_meses = mysql_fetch_array($result_meses)){
				if($mesNow == $row_meses['num_mes']){
					echo "<option value=$row_meses[num_mes] selected>$row_meses[nome_mes]</option>";
				} else {
					echo "<option value=$row_meses[num_mes]>$row_meses[nome_mes]</option>";
				}
					
			}
			
			$link = "folha.php?id=10&id_projeto=$id_projeto&regiao=$regiao"; ?>
        </select> de <select id="ano" name="ano" class="campotexto">
            <?php
            for($i=2007; $i<=2012; $i ++) {
				if($i == date('Y')){
					echo '<option value="'.$i.'" selected>'.$i.'</option>';
				} else {
					echo '<option value="'.$i.'" >'.$i.'</option>';
				}
			}
            ?>
        </select>
        </td>
      </tr>
      <tr>
       <td class="secao">In&iacute;cio da Folha:</td>
       <td>
        <input name="data_ini" type="text" id="data_ini" size="11" class="campotexto" maxlength="10"
               onkeyup="mascara_data(this)" />
       </td>
    </tr>
    <tr>
      <td class="secao">Quantidade de Dias:</td>
      <td>
      <input name="qnt_dias" type="text" class="campotexto" id="qnt_dias" size="2" value="30" />
      </td>
    </tr>
    <tr>
      <td class="secao">Tipo de Folha:</td>
      <td>
        <label>
          <input name="contratacao" type="radio" value="1" checked /> Aut&ocirc;nomo
        </label>
        &nbsp;&nbsp;
        <label>
          <input type="radio" value="3" name="contratacao" /> Cooperado
        </label>
        &nbsp;&nbsp;
        <label>
          <input type="radio" value="4" name="contratacao" /> 
          Aut&ocirc;nomo PJ
        </label>
      </td>
    </tr>
    <tr>
      <td class="secao">Cooperativa:</td>
      <td>
      <?php
      $cooperativas = new cooperativa();
	  $cooperativas -> SelectCooperativa($regiao,"coop");
      ?>
      </td>
    </tr>
    <tr>
      <td class="secao">Adiantamento:</td>
      <td>
      <label>
      	<input type="radio" value="1" name="adiantamento"/> sim
      </label>
      &nbsp;&nbsp;
      <label>
      	<input type="radio" value="0" name="adiantamento" checked="checked" /> n&atilde;o
      </label>
      </td>
    </tr>
    <tr>
      <td class="secao">Abono Natalino:</td>
      <td>
      <label>
        <input type="radio" value="1" name="terceiro" onClick="document.all.linhatipo.style.display=''" /> sim
      </label>
      &nbsp;&nbsp;
     <label>
      <input type="radio" value="0" name="terceiro" checked="checked" onClick="document.all.linhatipo.style.display='none'" />
      n&atilde;o
     </label>
       </td>
    </tr>
    <tr style="display:none;" id="linhatipo">
      <td class="secao">Tipo de Pagamento:</td>
      <td>
        <select name="tipo_terceiro" id="tipo_terceiro">
          <option value="1">PRIMEIRA PARCELA</option>
          <option value="2">SEGUNDA PARCELA</option>
          <option value="3" selected="selected">INTEGRAL</option>
        </select>
        <span id="spanteste"></span>
        </td>
     </tr>
     <tr>
        <td colspan="2" align="center">
    <input type="submit" name="Submit2" value="GERAR FOLHA" />
    <input type="hidden" name="id" value="10">
    <input type="hidden" name="regiao" value="<?=$regiao?>">
       </td>
     </tr>
    </table>
  </form>
  
  	</td>
   </tr>
   <tr>
     <td align="center"><iframe src="<?=$link?>" name="iframe1" width="90%" height="80" scrolling="no" frameborder="0" id="iframe1"></iframe></td>
   </tr>
  </table>
  
  
  <?php }  /// FIM VERIFICA PERMISSAO GERAR FOLHA ?>
  <table width="90%" align="center" style="border:solid 1px #777; margin-top:50px;">
    <tr>
      <td class="secao_pai">FOLHAS AUTÔNOMOS</td>
    </tr>
    <tr>
      <td align="center">
      
		<table width="100%" border="0" cellspacing="0" cellpadding="1" style="font-size:12px; line-height:32px;">
			
	  <?php $cores = array('#E3ECE3','#ECF2EC','#ECF3F6','#F5F9FA','#FEF9EB','#FFFDF7');
			$cor_um = -2;
			$cor_dois = 0;
			$cont_divisor = 0;
			
	  		$result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");
			$numero_pro = mysql_num_rows($result_pro);
			while($row_pro = mysql_fetch_assoc($result_pro)) {
				
				$cor_um += 2;
				$cor_dois = $cor_um+1;
				$cont_divisor++;
				
		   $result_folhas = mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,
												    date_format(data_fim, '%d/%m/%Y') AS data_fim
												    FROM folhas WHERE regiao = '$regiao' 
													AND projeto = '$row_pro[id_projeto]'
												    AND status != '0' AND contratacao = '1' ORDER BY projeto, ano, mes ASC");
	 		$numero_folhas = mysql_num_rows($result_folhas);
			if(!empty($numero_folhas)) { ?>
            
            <tr>
              <td colspan="6" class="sub_secao">
			  	<span><?=$row_pro['nome']?></span>
              </td>
            </tr>
            
            <?php while($row_folhas = mysql_fetch_array($result_folhas)) {
				
				      if($ultimo_ano != $row_folhas['ano']) { ?>
            
            <tr>
              <td colspan="6" style="background-color:#fff; font-weight:bold; font-size:14px;"><?php echo $row_folhas['ano']; ?></td>
            </tr>
            
			<?php }
			
			$ultimo_ano	= $row_folhas['ano'];
			
			// Encriptografando a Variável
			$linkreg = encrypt("$regiao&$row_folhas[0]&2");
			$linkreg = str_replace("+","--",$linkreg);
			//

			$mes_int = (int)$row_folhas['mes'];
			$nome_mes = $meses[$mes_int];
			
			if($row_folhas['status'] == 1) {
				$bt1 = "<a href='folha2.php?m=1&enc=$linkreg'>
					        <img src='../rh/folha/imagens/profolha.gif' border='0' alt='PROCESSAR'>
					    </a>";
				$bt2 = "<a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
						    <img src='../rh/folha/imagens/delfolha.gif' border='0' alt='DELETAR'>
						</a>";
			} elseif($row_folhas['status'] == 2) {
				$bt1 = "<a href='sintetica.php?enc=$linkreg'>
							<img src='../rh/folha/imagens/verfolha.gif' border='0' alt='VER'>
						</a>";
				$bt2 = "<a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
			  				<img src='../rh/folha/imagens/delfolha.gif' border='0' alt='DELETAR' title='Deletar Folha' >
						</a>";
			} else {
				$bt1 = "<a href='ver_folha.php?enc=$linkreg'>
					   	    <img src='../rh/folha/imagens/verfolha.gif' border='0' alt='VER'>
					    </a>";
				$bt2 = NULL;
				/*$bt2 = '<a href="recisao.php?deletar=true&id='.$row_rescisao[0].'&regiao='.$_GET['regiao'].'&id_clt='.$row_demissao[0].'" title="Desprocessar Folha" onclick="return window.confirm(\'Você tem certeza que quer desprocessar esta folha?\');">
			  				<img src="../rh/imagensrh/deletar.gif" alt="Desprocessar">
						</a>';*/
			}
			
			if($row_folhas['terceiro'] == 1) {
				$tipo_terceiro = array('', 'Primeira Parcela', 'Segunda Parcela', 'Integral');
				$mensagem = 'Abono Natalino '.$tipo_terceiro[$row_folhas['tipo_terceiro']];
			} else {
				$mensagem = "$nome_mes / $row_folhas[ano]";
			}
			
			$resultFuncionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row_folhas[user]'");
			$rowFuncionario = mysql_fetch_array($resultFuncionario); ?>
         
            <tr bgcolor="<?php if($cor++%2==0) { echo "$cores[$cor_um]"; } else { echo "$cores[$cor_dois]"; } ?>" height="34">
			  <td width="5%" align="center">
			  <?php 			   
					///permissão para VER FOLHA
					$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '2'"));
					if($verifica_acoes != 0) {
					 echo $bt1;
					}	  
			  
			  ?>              
              </td>
			  <td width="26%"><?='('.$row_folhas['id_folha'].') <b>'.$mensagem.'</b>'?></td>
	   		  <td width="26%">Gerado por <b><?=$rowFuncionario['nome1']?></b></td>
              <td width="22%"><?=$row_folhas['data_inicio'].' à '.$row_folhas['data_fim']?></td>
			  <td width="17%">Participantes: <b><?=$row_folhas['participantes']?></b></td>
			  <td width="4%" align="center">
               <?php 
			  			///permissão para DELETAR folha
						$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '3'"));
						if($verifica_acoes != 0) {
						 echo $bt2;
						}					
						
						if ($row_folhas['status'] == 3){
							  
								///permissão para DESPROCESSAR folha
								$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '7'"));
								if($verifica_acoes != 0) {
								?>	
									   <a href="desprocessar.php?folha=<?php echo $row_folhas['id_folha']; ?>&tipo_contratacao=1" title="Desprocessar Folha" onClick="return window.confirm('Você tem certeza que quer desprocessar esta folha?');"><img src="../rh/imagensrh/deletar.gif" /></a>
									
							  <?php } 
						 }
							  ?>
						</td>
            </tr>
			 
            <?php } if($cont_divisor != $numero_pro) { ?>
               
            <tr>
             <td colspan="6" bgcolor="#FAFAFA">&nbsp;</td>
            </tr>
			
            <?php } } } ?>
		
			</table>
		  </td>
       </tr>
    </table>
    
    
    
    
    
    
    
    
    
    
    
    <table width="90%" align="center" style="border:solid 1px #777; margin-top:50px;">
    <tr>
      <td class="secao_pai">FOLHAS COOPERADOS</td>
    </tr>
    <tr>
      <td align="center">
      
		<table width="100%" border="0" cellspacing="0" cellpadding="1" style="font-size:12px; line-height:32px;">
			
	  <?php $cores = array('#E3ECE3','#ECF2EC','#ECF3F6','#F5F9FA','#FEF9EB','#FFFDF7');
			$cor_um = -2;
			$cor_dois = 0;
			$cont_divisor = 0;
			
	  		$result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao'");
			$numero_pro = mysql_num_rows($result_pro);
			while($row_pro = mysql_fetch_assoc($result_pro)) {
				
				$cor_um += 2;
				$cor_dois = $cor_um+1;
				$cont_divisor++;
				
		   $result_folhas = mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,
												    date_format(data_fim, '%d/%m/%Y') AS data_fim
												    FROM folhas WHERE regiao = '$regiao' 
													AND projeto = '$row_pro[id_projeto]'
												    AND status != '0' AND contratacao = '3' ORDER BY projeto, ano, mes ASC");
	 		$numero_folhas = mysql_num_rows($result_folhas);
			if(!empty($numero_folhas)) { ?>
            
            <tr>
              <td colspan="6" class="sub_secao">
			  	<span><?=$row_pro['nome']?></span>
              </td>
            </tr>
            
            <?php while($row_folhas = mysql_fetch_array($result_folhas)) {
				
				      if($ultimo_ano != $row_folhas['ano']) { ?>
            
            <tr>
              <td colspan="6" style="background-color:#fff; font-weight:bold; font-size:14px;"><?php echo $row_folhas['ano']; ?></td>
            </tr>
            
			<?php }
			
			$ultimo_ano	= $row_folhas['ano'];
			
			// Encriptografando a Variável
			$linkreg = encrypt("$regiao&$row_folhas[0]&2");
			$linkreg = str_replace("+","--",$linkreg);
			//

			$mes_int = (int)$row_folhas['mes'];
			$nome_mes = $meses[$mes_int];
			
			if($row_folhas['status'] == 1) {
				$bt1 = "<a href='folha2.php?m=1&enc=$linkreg'>
					        <img src='../rh/folha/imagens/profolha.gif' border='0' alt='PROCESSAR'>
					    </a>";
				$bt2 = "<a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
						    <img src='../rh/folha/imagens/delfolha.gif' border='0' alt='DELETAR'>
						</a>";
			} elseif($row_folhas['status'] == 2) {
				$bt1 = "<a href='sinteticacoo.php?enc=$linkreg'>
							<img src='../rh/folha/imagens/verfolha.gif' border='0' alt='VER'>
						</a>";
				$bt2 = "<a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
			  				<img src='../rh/folha/imagens/delfolha.gif' border='0' alt='DELETAR'  title='Deletar Folha'>
						</a>";
			} else {
				$bt1 = "<a href='ver_folhacoop.php?enc=$linkreg'>
					   	    <img src='../rh/folha/imagens/verfolha.gif' border='0' alt='VER'>
					    </a>";
				$bt2 = NULL;
			}
			
			if($row_folhas['terceiro'] == 1) {
				$tipo_terceiro = array('', 'Primeira Parcela', 'Segunda Parcela', 'Integral');
				$mensagem = 'Abono Natalino '.$tipo_terceiro[$row_folhas['tipo_terceiro']];
			} else {
				$mensagem = "$nome_mes / $row_folhas[ano]";
			}
			
			$resultFuncionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row_folhas[user]'");
			$rowFuncionario = mysql_fetch_array($resultFuncionario); ?>
         
            <tr bgcolor="<?php if($cor++%2==0) { echo "$cores[$cor_um]"; } else { echo "$cores[$cor_dois]"; } ?>" height="34">
			  <td width="5%" align="center">
			  <?php 
			 		///permissão para VER folha
					$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '2'"));
					if($verifica_acoes != 0) {
					 echo $bt1;
					}			
			  ?>
              </td>
			  <td width="26%"><?='('.$row_folhas['id_folha'].') <b>'.$mensagem.'</b>'?></td>
	   		  <td width="26%">Gerado por <b><?=$rowFuncionario['nome1']?></b></td>
              <td width="22%"><?=$row_folhas['data_inicio'].' à '.$row_folhas['data_fim']?></td>
			  <td width="17%">Participantes: <b><?=$row_folhas['participantes']?></b></td>
			  <td width="4%" align="center">
              <?php 
			   			///permissão para DELETAR folha
						$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '3'"));
						if($verifica_acoes != 0) {
						 echo $bt2;
						}					
						
						if ($row_folhas['status'] == 3){
							  
								///permissão para DESPROCESSAR folha
								$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '7'"));
								if($verifica_acoes != 0) {
								?>	
									   <a href="desprocessar.php?folha=<?php echo $row_folhas['id_folha']; ?>&tipo_contratacao=3" title="Desprocessar Folha" onClick="return window.confirm('Você tem certeza que quer desprocessar esta folha?');"><img src="../rh/imagensrh/deletar.gif" /></a>
									
							  <?php } 
						 }
							  ?>
								
			  
              </td>
              
            </tr>
			 
            <?php } if($cont_divisor != $numero_pro) { ?>
               
            <tr>
             <td colspan="6" bgcolor="#FAFAFA">&nbsp;</td>
            </tr>
			
            <?php } } } ?>
		
			</table>
		  </td>
       </tr>
    </table>
    
  
    
  <table width="90%" align="center" style="border:solid 1px #777; margin-top:50px;">
    <tr>
      <td class="secao_pai">FOLHAS AUTÔNOMOS / PJ</td>
    </tr>
    <tr>
      <td align="center">
      
		<table width="100%" border="0" cellspacing="0" cellpadding="1" style="font-size:12px; line-height:32px;">
			
	  <?php $cores = array('#E3ECE3','#ECF2EC','#ECF3F6','#F5F9FA','#FEF9EB','#FFFDF7');
			$cor_um = -2;
			$cor_dois = 0;
			$cont_divisor = 0;
			
	  		$result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");
			$numero_pro = mysql_num_rows($result_pro);
			while($row_pro = mysql_fetch_assoc($result_pro)) {
				
				$cor_um += 2;
				$cor_dois = $cor_um+1;
				$cont_divisor++;
				
		   $result_folhas = mysql_query("SELECT * , date_format(data_inicio, '%d/%m/%Y') AS data_inicio,
												    date_format(data_fim, '%d/%m/%Y') AS data_fim
												    FROM folhas WHERE regiao = '$regiao' 
													AND projeto = '$row_pro[id_projeto]'
												    AND status != '0' AND contratacao = '4' ORDER BY projeto, ano, mes ASC");
	 		$numero_folhas = mysql_num_rows($result_folhas);
			if(!empty($numero_folhas)) { ?>
            
            <tr>
              <td colspan="6" class="sub_secao">
			  	<span><?=$row_pro['nome']?></span>
              </td>
            </tr>
            
            <?php while($row_folhas = mysql_fetch_array($result_folhas)) {
				
				      if($ultimo_ano != $row_folhas['ano']) { ?>
            
            <tr>
              <td colspan="6" style="background-color:#fff; font-weight:bold; font-size:14px;"><?php echo $row_folhas['ano']; ?></td>
            </tr>
            
			<?php }
			
			$ultimo_ano	= $row_folhas['ano'];
			
			// Encriptografando a Variável
			$linkreg = encrypt("$regiao&$row_folhas[0]&2");
			$linkreg = str_replace("+","--",$linkreg);
			//

			$mes_int = (int)$row_folhas['mes'];
			$nome_mes = $meses[$mes_int];
			
			if($row_folhas['status'] == 1) {
				$bt1 = "<a href='folha2.php?m=1&enc=$linkreg'>
					        <img src='../rh/folha/imagens/profolha.gif' border='0' alt='PROCESSAR'>
					    </a>";
				$bt2 = "<a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
						    <img src='../rh/folha/imagens/delfolha.gif' border='0' alt='DELETAR'>
						</a>";
			} elseif($row_folhas['status'] == 2) {
				$bt1 = "<a href='sinteticacoo.php?enc=$linkreg'>
							<img src='../rh/folha/imagens/verfolha.gif' border='0' alt='VER'>
						</a>";
				$bt2 = "<a href='#' onClick='confirm_entry($regiao,$row_folhas[0])'>
			  				<img src='../rh/folha/imagens/delfolha.gif' border='0' alt='DELETAR'  title='Deletar Folha'>
						</a>";
			} else {
				$bt1 = "<a href='ver_folhacoop.php?enc=$linkreg'>
					   	    <img src='../rh/folha/imagens/verfolha.gif' border='0' alt='VER'>
					    </a>";
				$bt2 = NULL;
			}
			
			if($row_folhas['terceiro'] == 1) {
				$tipo_terceiro = array('', 'Primeira Parcela', 'Segunda Parcela', 'Integral');
				$mensagem = 'Abono Natalino '.$tipo_terceiro[$row_folhas['tipo_terceiro']];
			} else {
				$mensagem = "$nome_mes / $row_folhas[ano]";
			}
			
			$resultFuncionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row_folhas[user]'");
			$rowFuncionario = mysql_fetch_array($resultFuncionario); ?>
         
            <tr bgcolor="<?php if($cor++%2==0) { echo "$cores[$cor_um]"; } else { echo "$cores[$cor_dois]"; } ?>" height="34">
			  <td width="5%" align="center">
			    <?php 
			
					///permissão para VER FOLHA
					$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '2'"));
					if($verifica_acoes != 0) {
					echo $bt1;
					}
				
			  
			   ?>		 
              </td>
			  <td width="26%"><?='('.$row_folhas['id_folha'].') <b>'.$mensagem.'</b>'?></td>
	   		  <td width="26%">Gerado por <b><?=$rowFuncionario['nome1']?></b></td>
              <td width="22%"><?=$row_folhas['data_inicio'].' à '.$row_folhas['data_fim']?></td>
			  <td width="17%">Participantes: <b><?=$row_folhas['participantes']?></b></td>
			  <td width="4%" align="center">
              
               <?php 
			 ///permissão para DELETAR folha
						$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '3'"));
						if($verifica_acoes != 0) {
						 echo $bt2;
						}					
						
						if ($row_folhas['status'] == 3){
							  
								///permissão para DESPROCESSAR folha
								$verifica_acoes  = mysql_num_rows($qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '7'"));
								if($verifica_acoes != 0) {
								?>	
									   <a href="desprocessar.php?folha=<?php echo $row_folhas['id_folha']; ?>&tipo_contratacao=4" title="Desprocessar Folha" onClick="return window.confirm('Você tem certeza que quer desprocessar esta folha?');"><img src="../rh/imagensrh/deletar.gif" /></a>
									
							  <?php } 
						 }
							  ?>
									
						
			 </td>
            </tr>
			 
            <?php } if($cont_divisor != $numero_pro) { ?>
               
            <tr>
              <td colspan="6" bgcolor="#FAFAFA">&nbsp;</td>
            </tr>
			
            <?php } } } ?>
		
			</table>
		  </td>
       </tr>
    </table>
    

	 
  </td>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><img src="../imagens/carregando/loading.gif" style="display:none"></td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php
break;

// --------------------------- CALCULANDO AS DATAS PARA GERAR A FOLHA --------------------------
case 10:

if(!empty($_REQUEST['data_ini'])) {
	
print "
<style type='text/css'>
body {
	background-color:#FFF;
	margin:0px;
	font-size:13px;
}
</style>";

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$mes_pagamento = $_REQUEST['mes'];
$ano_pagamento = $_REQUEST['ano'];
$data_ini = $_REQUEST['data_ini'];
$contratacao = $_REQUEST['contratacao'];
$coop = $_REQUEST['coop'];

$qnt_dias = $_REQUEST['qnt_dias'];
$qnt_dias1 = "$qnt_dias" - "1";

$terceiro = $_REQUEST['terceiro'];
$tipo_terceiro = $_REQUEST['tipo_terceiro'];

$data = explode("/",$data_ini);

$dia = $data['0'];
$mes = $data['1'];
$ano = $data['2'];

$data_ini_adianta = "01/$mes/$ano";

$data_fim = date("d/m/Y", mktime(0, 0, 0, $mes, $dia+$qnt_dias1, $ano));

$meses = array('ERRO','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$mes_int = (int)$mes;
$nome_mes = $meses[$mes_int];

if(empty($_REQUEST['adiantamento'])) {
$adiantamento = "0";
$link157 = "

Data Inicial: $data_ini &nbsp;&nbsp;- &nbsp;&nbsp;Data Final: $data_fim <br>
Folha referente ao m&ecirc;s: $mes_pagamento - $nome_mes <br>
<form action='folha.php' method='post' name='form1' target='_parent'>
<input type='hidden' name='id' value='11' id='id'>
<input type='hidden' name='id_projeto' value='$id_projeto' id='id_projeto'>
<input type='hidden' name='regiao' value='$regiao' id='regiao'>
<input type='hidden' name='data_ini' value='$data_ini' id='data_ini'>
<input type='hidden' name='data_fim' value='$data_fim' id='data_fim'>
<input type='hidden' name='qnt_dias' value='$qnt_dias' id='qnt_dias'>
<input type='hidden' name='mes_pagamento' value='$mes_pagamento' id='mes_pagamento'>
<input type='hidden' name='ano_pagamento' value='$ano_pagamento' id='ano_pagamento'>
<input type='hidden' name='terceiro' value='$terceiro' id='terceiro'>
<input type='hidden' name='tipo_terceiro' value='$tipo_terceiro' id='tipo_terceiro'>
<input type='hidden' name='contratacao' value='$contratacao' id='contratacao'>
<input type='hidden' name='coop' value='$coop' id='coop'>
<br>
<input type='submit' name='Submit' value='Continuar' class='campotexto' />
</form>
";

} else {
	
$adiantamento = $_REQUEST['adiantamento'];
$link157 = "
Data Inicial: $data_ini_adianta &nbsp;&nbsp; - &nbsp;&nbsp;Data Final: $data_fim <br>
Folha referente ao m&ecirc;s: $mes_pagamento - $nome_mes<br>

<form action='adiantamento.php' method='post' name='form1' target='_parent'>

<input type='hidden' name='id' value='11' id='id'>
<input type='hidden' name='projeto' value='$id_projeto' id='id_projeto'>
<input type='hidden' name='regiao' value='$regiao' id='regiao'>
<input type='hidden' name='data_ini' value='$data_ini' id='data_ini'>
<input type='hidden' name='data_fim' value='$data_fim' id='data_fim'>
<input type='hidden' name='qnt_dias' value='$qnt_dias' id='qnt_dias'>
<input type='hidden' name='mes_pagamento' value='$mes_pagamento' id='mes_pagamento'>
<input type='hidden' name='ano_pagamento' value='$ano_pagamento' id='ano_pagamento'>
<input type='hidden' name='terceiro' value='$terceiro' id='terceiro'>
<input type='hidden' name='tipo_terceiro' value='$tipo_terceiro' id='tipo_terceiro'>
<input type='hidden' name='contratacao' value='$contratacao' id='contratacao'>

<input type='hidden' name='coop' value='$coop' id='coop'>

<br>
<input type='submit' name='Submit' value='Continuar' class='campotexto' />
</form>
";
}

print "<table width='100%'><tr><td align='center'>";
print "<div style='font-size:15px;'><b> $link157 </b></div>";
print "</td></tr></table>";

} else {
	
print "
<style type='text/css'>
body {
	background-color:#FAFAFA;
	margin:0px;
}
</style>";

}

break;
case 11: // INSERINDO OS DADOS DA FOLHA NA TABELA FOLHAS

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$qnt_dias = $_REQUEST['qnt_dias'];
$mes_pagamento = $_REQUEST['mes_pagamento'];
$ano_pagamento = $_REQUEST['ano_pagamento'];
$terceiro = $_REQUEST['terceiro'];
$tipo_terceiro = $_REQUEST['tipo_terceiro'];
$contratacao = $_REQUEST['contratacao'];

$coop = $_REQUEST['coop'];

$id_user = $_COOKIE['logado'];
$parte = "1";


/* /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
 /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/ */


function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$data_iniF = ConverteData($data_ini);
$data_fimF = ConverteData($data_fim);

$data_proc = date("Y-m-d");

if($contratacao == 3){
	$result = mysql_query("SELECT * FROM folhas where projeto = '$id_projeto' and mes = '$mes_pagamento' AND terceiro != '1' and contratacao = '$contratacao'
	and coop = '$coop' and (status = '1' or status = '2')");
	$con_result = mysql_num_rows($result);
}else{
	$result = mysql_query("SELECT * FROM folhas where projeto = '$id_projeto' and mes = '$mes_pagamento' AND terceiro != '1' and contratacao = '$contratacao'  
	and (status = '1' or status = '2')");
	$con_result = mysql_num_rows($result);
}
	
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkVolta = encrypt("$regiao&$regiao"); 
$linkVolta = str_replace("+","--",$linkVolta);
// -----------------------------	
	
	
/*if($con_result >= 1){
	print "<script>
	alert(\"Existe uma folha em aberto desse projeto! FINALIZE para continuar!\");
	location.href=\"folha.php?id=9&enc=$linkVolta\"
	</script>";
	exit;
}else{*/

$RSverificaAUT = mysql_query("SELECT * FROM autonomo where id_projeto = '$id_projeto' and status = '1' and tipo_contratacao = '$contratacao'");
$row_verificaAUT = mysql_num_rows($RSverificaAUT);

if($contratacao == 1){
	$RSverificaAUTProc = mysql_query("SELECT * FROM folha_autonomo WHERE projeto = '$id_projeto' AND mes = '$mes_pagamento' 
	AND status = '3' AND terceiro != '1' AND ano = '$ano_pagamento'");
	$row_verificaAUTProc = mysql_num_rows($RSverificaAUTProc);
}else{
	$RSverificaAUTProc = mysql_query("SELECT * FROM folha_cooperado WHERE projeto = '$id_projeto' AND mes = '$mes_pagamento' AND status = '3' AND 		
	terceiro != '1' AND ano = '$ano_pagamento'");
	$row_verificaAUTProc = mysql_num_rows($RSverificaAUTProc);
}


	//if($row_verificaAUT > $row_verificaAUTProc){
	
	$result_max = mysql_query("SELECT MAX(parte) FROM folhas where projeto = '$id_projeto' and mes = '$mes_pagamento' and status = '3' AND terceiro != '1'
	and contratacao = '$contratacao'");
	$row_max = mysql_fetch_array($result_max);

	$parte = $row_max['0'] + 1;
	
	$mes = sprintf("%02d",$mes);
	
	mysql_query("INSERT INTO `folhas`
	(parte,contratacao,coop,mes,ano,qnt_dias,data_inicio,data_fim,regiao,projeto,terceiro,tipo_terceiro,user,data_proc,status,status_reg)
	VALUES ('$parte','$contratacao','$coop','$mes_pagamento','$ano_pagamento','$qnt_dias','$data_iniF','$data_fimF','$regiao','$id_projeto','$terceiro',
	'$tipo_terceiro','$id_user','$data_proc','1','1')") or die ("Erro ".mysql_error());
	
	$folha = mysql_insert_id();
	
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkcontinue = encrypt("$regiao&$folha&2"); 
	$linkcontinue = str_replace("+","--",$linkcontinue);
	// -----------------------------	
	
	
	print "<script>
	location.href=\"folha2.php?m=1&enc=$linkcontinue\"
	</script>";
	
	/*}else{
		
	print "<script>
	alert(\"Todos os Participantes deste projeto ja estão na(s) outra(s) folha(s) FINALIZADA(s) ou Não Existe participante ATIVO no projeto! \");
	location.href=\"folha.php?id=9&enc=$linkVolta\"
	</script>";
	exit;
}*/



//}

break;
case 12: // DELETANDO FOLHA


$folha = $_REQUEST['folha'];
$regiao = $_REQUEST['regiao'];


//SELECIONANDO A FOLHA PARA SABER O TIPO DE CONTRATACAO
$RE = mysql_query("SELECT contratacao FROM folhas WHERE id_folha = '$folha'");
$Row = mysql_fetch_array($RE);

//DELETANDO DA TABELA FOLHA_AUTONOMO / FOLHA_COOPERADO ..
if($Row['contratacao'] == 1){
	mysql_query("DELETE FROM folha_autonomo WHERE id_folha = '$folha'");
}else{
	mysql_query("DELETE FROM folha_cooperado WHERE id_folha = '$folha'");
}

//DELETANDO A FOLHA DA TABELA FOLHAS
mysql_query("DELETE FROM folhas WHERE id_folha= '$folha'");


//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkreg = encrypt("$regiao&$regiao"); 
$linkreg = str_replace("+","--",$linkreg);
// -----------------------------

print "
<script>
location.href=\"folha.php?id=9&enc=$linkreg\"
</script>";

}
?>