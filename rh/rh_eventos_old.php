<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../funcoes.php');

if(!empty($_REQUEST['enc'])) {

	$link = decrypt(str_replace('--','+',$_REQUEST['enc'])); 
	list($regiao,$tela,$clt) = explode('&',$link);

} else {

	$tela = $_REQUEST['tela'];

}

$meses = array('-','JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO');
?>

<html>
<head>
<title>:: Intranet :: Eventos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/ramon.js"></script>
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
	margin:0px auto;
	text-align:left;
	padding-top:20px;
	padding-bottom:10px;
}
td.escuro_claro {
	height:35px;
	text-align:center;
	background-color:#999;
}
td.escuro {
	height:35px;
	text-align:center;
	background-color:#333;
	color:#FFF;
}
td.claro {
	height:35px;
	text-align:center;
	background-color:#FFF;
}
.linhastabela1{
	border:solid 1px #FFF;
}
.linhastabela2{
	border:solid 1px #F00;
}
.style40 {
	font-family:Geneva, Arial, Helvetica, sans-serif;
}
</style>
<script type="text/javascript">
function fechar(obj) { 
	var fechar = document.getElementById(obj); 
	fechar.style.display = 'none';
}

function abrir(obj) { 
	var abrir = document.getElementById(obj); 
	if(abrir.style.display != 'none') { 
		abrir.style.display = 'none'; 
	} else { 
		abrir.style.display = ''; 
	} 
}
</script>
</head>
<body>
<div id="corpo">

    <div id="topo" style="width:95%; margin:0px auto; font-family:Arial;">
        <div style="float:left; width:25%;">
            <a href="../principalrh.php?regiao=<?=$regiao?>&id=1">
                <img src="../imagens/voltar.gif" border="0">
            </a>
        </div>
        <div style="float:left; width:50%; text-align:center; font-size:24px; font-weight:bold; color:#000;">
            EVENTOS
        </div>
        <div style="float:right; width:25%; text-align:right; font-size:12px; color:#333;">
            <br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;
        </div>
        <div style="clear:both;"></div>
    </div>
    
<?php switch($tela) {
	     case 1:
	
	  $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
	  $result_clt10 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_regiao = '$regiao' and status = '10' order by nome");
	  $num_clt10 = mysql_num_rows($result_clt10);
      $result_clt10 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_regiao = '$regiao' and status = '10' order by nome");
      $num_clt10 = mysql_num_rows($result_clt10);
	  $i = '2'; ?>
 
<table width="95%" style="margin:20px auto;">
	<tr>
	  <td valign="top" width="25%">
		<a href="#" class="outro_link" onClick="fechar('evento2'); fechar('evento3'); fechar('evento4'); fechar('evento5'); fechar('evento6'); fechar('evento7'); fechar('evento8'); fechar('evento9'); fechar('evento10'); fechar('evento11'); fechar('evento12'); fechar('evento13'); fechar('evento14'); abrir('evento1');">10 - Atividade Normal (<?=$num_clt10?>)</a>
        
			<?php $result_rhstatus = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo != '10' AND codigo != '60' AND codigo != '61' AND codigo != '62' AND codigo != '110' ");
				  while($row_rhstatus = mysql_fetch_array($result_rhstatus)){
					
					$result_clt20 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') as data_entrada2, date_format(data_saida, '%d/%m/%Y') as data_saida2 FROM rh_clt WHERE id_regiao = '$regiao' AND status = '$row_rhstatus[codigo]' ORDER BY nome");
					$num_clt20 = mysql_num_rows($result_clt20); ?>
                    
		<a href="#" class="outro_link" onClick="fechar('evento1'); fechar('evento2'); fechar('evento3'); fechar('evento4'); fechar('evento5'); fechar('evento6'); fechar('evento7'); fechar('evento8'); fechar('evento9'); fechar('evento10'); fechar('evento11'); fechar('evento12'); fechar('evento13'); fechar('evento14'); abrir('evento<?php print $i++; ?>');">
			<?php echo "$row_rhstatus[codigo] - $row_rhstatus[especifica]"; ?> (<?=$num_clt20?>)
        </a>
        
	<?php } ?>

      </td>
      <td width="75%" valign="top">
      
	  <?php $result_rhstatus = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo != '10' AND codigo != '60' AND codigo != '61' AND codigo != '62' AND codigo != '110' "); ?>
       
       <div class="evento" id="evento1">
       		<b style="font-size:17px;">10 - Atividade Normal (<?=$num_clt10?>)</b>
            <div style="clear:both;">&nbsp;</div>
       
       <?php while ($projetos = mysql_fetch_assoc($qr_projetos)) {
	
			$result_clt10 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[id_projeto]' AND id_regiao = '$regiao' and status = '10' order by nome");
    		$num_clt10 = mysql_num_rows($result_clt10);
			
			if(!empty($num_clt10)) { ?>
            
     <table width="100%" bgcolor="#ffffff" align="center" cellpadding="2" cellspacing="0">
       <tr>
    	 <td colspan="3" class="show" style="border:0px;">&nbsp;<span style="color:#F90; font-size:32px;">&#8250;</span> <?php echo $projetos['nome']; ?>
         </td>
      </tr>
      <tr style="background-color:#036; color:#FFF;">
          <td width="7%" style="text-align:center;">COD</td>
          <td width="45%">NOME</td>
          <td width="48%">CARGO</td>
      </tr>
      
	<?php while($row_clt10 = mysql_fetch_array($result_clt10)) {
		
			  $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt10[id_curso]'");
			  $row_curso    = mysql_fetch_array($result_curso);

			  $link  = encrypt("$regiao&2&$row_clt10[0]");
			  $link2 = str_replace('+','---',$link); ?>
	
              <tr bgcolor="<?php if($linha++%2==0) { echo '#ECF2EC'; } ?>">
                 <td align="center"><?=$row_clt10['campo3']?></td>
                 <td><a href="rh_eventos.php?enc=<?=$link2?>"><?=$row_clt10['nome']?></a></td>
                 <td><?=$row_curso['nome']?></td>
              </tr>
   
   <?php } while($row_clt10 = mysql_fetch_array($result_clt10)) {
		
			  $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt10[id_curso]'");
			  $row_curso    = mysql_fetch_array($result_curso);
	
			  $link  = encrypt("$regiao&2&$row_clt10[0]");
			  $link2 = str_replace('+','---',$link); ?>
	
              <tr bgcolor="<?php if($linha++%2==0) { echo '#ECF2EC'; } ?>">
                 <td align="center"><?=$row_clt10['campo3']?></td>
                 <td><a href="rh_eventos.php?enc=<?=$link2?>"><?=$row_clt10['nome']?></a></td>
                 <td><?=$row_curso['nome']?></td>
              </tr>
   
   <?php } ?>
   
	</table>

<?php } } ?>
       </div>

       <?php $i = "2"; $b = "2";
	   while($row_rhstatus = mysql_fetch_array($result_rhstatus)){ 
    
	// Mostrando todos os outros status
	$result_clt20_total = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') as data_entrada2, date_format(data_saida, '%d/%m/%Y') as data_saida2 FROM rh_clt WHERE id_regiao = '$regiao' AND status = '$row_rhstatus[codigo]' ORDER BY nome");
	$num_clt20_total = mysql_num_rows($result_clt20_total);
	?>
    
    <div class="evento" id="evento<?php print $i++; ?>" style="display:none;">
    <b style="font-size:17px;">
		<?php echo "$row_rhstatus[codigo] - $row_rhstatus[especifica] ($num_clt20_total)"; ?>
    </b>
    <div style="clear:both;">&nbsp;</div>
       <?php if(empty($num_clt20_total)) { ?>
       Não há cadastrados neste setor!
       <?php } else {
	   $qr_projetos2 = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
	   while($projetos2 = mysql_fetch_assoc($qr_projetos2)) {
	$result_clt20 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') as data_entrada2, date_format(data_saida, '%d/%m/%Y') as data_saida2 FROM rh_clt WHERE id_projeto = '$projetos2[id_projeto]' AND  id_regiao = '$regiao' AND status = '$row_rhstatus[codigo]' ORDER BY nome");
	$num_clt20 = mysql_num_rows($result_clt20);
	if (!empty($num_clt20)) {
		  ?>
       <table width="100%" bgcolor="#ffffff" align="center" cellpadding="2" cellspacing="0">
    <tr>
    <td colspan="4" class="show" style="border:0px;">&nbsp;<span style="color:#F90; font-size:32px;">&#8250;</span> <?php echo $projetos2['nome']; ?>
    </td>
      </tr>
    <tr bgcolor="#003366">
      <td width="5%"><div align="center"><span class="linha" style="color:#FFF;">C&oacute;d</span></div></td>
      <td width="35%">&nbsp;<span class="linha" style="color:#FFF;">Nome</span></td>
      <td width="35%">&nbsp;<span class="linha" style="color:#FFF;">Cargo</span></td>
      <td width="25%">&nbsp;<span class="linha" style="color:#FFF;">Duração</span></td>
    </tr>
   
       <?php while ($row_clt20 = mysql_fetch_array($result_clt20)){
		$cont = "1";
		
		$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt20[id_curso]'");
		$row_curso = mysql_fetch_array($result_curso);
		
		$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt20[id_projeto]' AND status_reg = '1'");
		$row_pro = mysql_fetch_array($result_pro);
		
		$RE_event = mysql_query("SELECT date_format(data, '%d/%m/%Y')as data2,date_format(data_retorno, '%d/%m/%Y')as data_retorno2 FROM rh_eventos WHERE id_clt = '$row_clt20[0]'");
		$ROW_EV = mysql_fetch_array($RE_event); 
		
		if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }
		if($row_clt20['campo3'] == "INSERIR" ){ $color="#FB797C"; }else{ $color2=""; }
		if($row_clt20['locacao'] == "1 - A CONFIRMAR" ){ $color="#FB797C"; }else{ $color2=""; }
		if($row_clt20['foto'] == "1" ){$color="#FFFFCC"; }else{ $color2=""; }
	
		if($row_clt20['observacao'] <> "" ){
			$color="#EAEAEA"; 
			$obs="title=\"Observações: $row_clt20[observacao]\"";
		}else{ 
			$color2=""; 
			$obs="";
		}
		
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$link = encrypt("$regiao&2&$row_clt20[0]"); 
	$link2 = str_replace("+","---",$link);
	// -----------------------------
		
		print "<tr>
   <td><span class='style3' style='color:$cor_fonte'>$row_clt20[campo3]</span></td>
   <td><a href='rh_eventos.php?tela=2&enc=$link2' class=$link2 $obs>$row_clt20[nome]</a></td>
   <td><span class='style3' style='color:$cor_fonte'>$row_curso[nome]</span></td>
   <td><span class='style3' style='color:$cor_fonte'>$ROW_EV[data2] - $ROW_EV[data_retorno2]</span></td>
   </tr>";
   $cont ++; 
   
   } } }
		?>
       
</table>
<?php } ?>

       </div>
    <?php } ?>  
    
    </td>
    </tr>
    </table>

<?php
break;
case 2:
//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&1&$row_clt10[0]");
$link2 = str_replace("+","---",$link);
// -----------------------------

//SELECIONANDO OS DADOS DO CLT
$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

//SELECIONANDO O CURSO
$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

if($row_clt['status'] != 10){
	
	// Selecionando o status
	$qr_ferias = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$row_clt[0]'");
	$ferias = mysql_fetch_assoc($qr_ferias);
	
	if($ferias['status'] == "40") {
		$qr_ferias2 = mysql_query("SELECT * , date_format(data_retorno, '%d/%m/%Y') as data_retorno FROM rh_ferias WHERE id_clt = '$row_clt[0]' ORDER BY id_ferias DESC");
		$ferias2 = mysql_fetch_assoc($qr_ferias2);
		$msg = "<br><font color=#FF0000>Em Férias</font><br><b>Data de Retorno:</b> ".$ferias2['data_retorno'];
	} else {
	
	$RE_MAXevent = mysql_query("SELECT MAX(id_status)as id FROM rh_eventos WHERE id_clt = '$row_clt[0]'");
	$ID_MAXEvent = mysql_fetch_array($RE_MAXevent);

	$RE_Events = mysql_query("SELECT nome_status,cod_status,date_format(data, '%d/%m/%Y')as data2,date_format(data_retorno, '%d/%m/%Y')as data_retorno2 FROM rh_eventos WHERE id_status = '$ID_MAXEvent[id]'");
	$ROW_Event = mysql_fetch_array($RE_Events);

	$msg = "<br><font color=#FF0000>".$ROW_Event['nome_status']."</font><br><b>Data de Retorno:</b> ".$ROW_Event['data_retorno2'];
}
}

$data = date('d/m/Y');
?>
	<div align="center" style="font-family:Arial; font-size:18px; color:#FFF; background:#036; margin:20px auto; width:95%;">
    	<?=$row_clt['campo3']." - ".$row_clt['nome'];?>
	</div>
	<div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:4px;">
		<?="<b>Unidade:</b> ".$row_clt['locacao']."<br><b>Atividade:</b> ".$row_curso['nome']."<br><b>Salário:</b> R$ ".$row_curso['salario']?>
    	<?=$msg?>
	</div>
<br>
<?php 
$data_retorno = implode("-", array_reverse(explode("/", $ferias2['data_retorno'])));
if($ferias['status'] == "40" and (date('Y-m-d') <= $data_retorno)) { ?>
<div style="background-color:#C66; border:1px solid #C63; padding:4px; color:#FFF; text-align:center;">
O candidato se encontra em período de férias.
</div>
<br>
<?php } ?>
<table width="95%" bgcolor="#f5f5f5" align="center">
  <tbody>
    <tr bgcolor="#cccccc" class="linha">
      <td height="163" align="center" valign="middle" bgcolor="#F7F7F7">
        <form action="rh_eventos.php" method="post" name="form1" id="form1" onSubmit="return ValidaForm()">
        <table width="38%" border="0" cellspacing="0" cellpadding="0" style="border:solid 1px #ddd;">
        <tr bgcolor="#EAEAEA">
              <td height="33" bgcolor="#EAEAEA"><div align="right" class="linha">Ocorr&ecirc;ncias: </div></td>
              <td bgcolor="#EAEAEA">&nbsp;&nbsp;
                <select name="movimento" id="movimento" class="campotexto" style="width:190px;">
                  <?php				  
$result_rhstatus = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo != '10' AND codigo != '60' AND codigo != '61' AND codigo != '62' AND codigo != '110'");
while($row_rhstatus = mysql_fetch_array($result_rhstatus)){
	if($ROW_Event['cod_status'] == $row_rhstatus['codigo']){
		echo "<option value='$row_rhstatus[codigo]' selected>$row_rhstatus[especifica]</option>";
	}else{
			//VERIFICA SE O FUNCIOÁRIO É SO SEXO MASCULINO OU FEMININO E ELIMITA A OPÇÃO DE LICENSA PATERNIDADE PARA AS MULHERES E MATERNIDADE PARA OS HOMENS
			if (($row_clt['sexo'] == 'm') or ($row_clt['sexo'] == 'M')){
				if ($row_rhstatus['codigo'] == '50'){				
					$row_rhstatus['codigo'] = '';
					$row_rhstatus['especifica'] = '';
				}				
			}else if (($row_clt['sexo'] == 'f') or ($row_clt['sexo'] == 'F')){
				if ($row_rhstatus['codigo'] == '51'){				
					$row_rhstatus['codigo'] = '';
					$row_rhstatus['especifica'] = '';										
				}
			}
			//IMPRIME AS OPÇÕES CASO NENHUMA DELAS TENHA VALOR VAZIO
			if (($row_rhstatus['codigo']!='') or ($row_rhstatus['especifica'] != '')) {
				echo "<option value='$row_rhstatus[codigo]'>$row_rhstatus[especifica]</option>";
			}
	}
}
echo "<option value='10'>ATIVIDADE NORMAL</option>";

?>
                </select></td>
            </tr>
            <tr>
              <td height="33" bgcolor="#EAEAEA"><div align="right" class="linha">Data: </div></td>
              <td bgcolor="#EAEAEA">&nbsp;&nbsp;<strong><span class="style40">
                <input name="data" type="text" id="data" size="11" class="campotexto"
                onFocus="this.style.background='#CCFFCC'" 
                onBlur="this.style.background='#FFFFFF'"
                onKeyUp="mascara_data(this)" maxlength="10" style="background='#FFFFFF'">
              </span></strong></td>
            </tr>
            <tr>
              <td width="27%" height="33" bgcolor="#EAEAEA"><div align="right" class="linha">Dias: </div></td>
              <td width="73%" bgcolor="#EAEAEA">&nbsp;&nbsp;

              <input name="dias" type="text" class="campotexto" id="dias" size="3" maxlength="3"
                onFocus="this.style.background='#CCFFCC'" 
                onBlur="this.style.background='#FFFFFF'" style="background='#FFFFFF'"/></td>
            </tr>
            <tr>
              <td height="31" colspan="2" align="center" valign="middle" bgcolor="#EAEAEA"><div align="center" class="linha">Observa&ccedil;&atilde;o:</div></td>
            </tr>
            <tr>
              <td height="31" colspan="2" align="center" valign="middle" bgcolor="#EAEAEA">
              <textarea name="obs" cols="30" rows="5" class="campotexto" id="obs" style="background='#FFFFFF'" onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"></textarea>
              </td>
            </tr>
            <tr>
              <td height="31" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC">
                <input type="hidden" name="regiao" value="<?=$regiao?>" />
                <input type="hidden" name="clt" value="<?=$clt?>" />
                <input type="hidden" name="salario" value="<?=$row_curso['salario']?>" />
                <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
                <input type="hidden" name="tela" value="3" />
              <input type="submit" class="botao" value="Enviar"></td>
            </tr>
          </table>
<script language="javascript">
		  
function ValidaForm(){
	d = document.form1;

	if (d.data.value == "" ){
		alert("O campo Data deve ser preenchido!");
		d.data.focus();
		return false;
	}
	if (d.dias.value == "" ){
		alert("O campo Dias deve ser preenchido!");
		d.dias.focus();
		return false;
	}
	
	return true;   
}	
		  
		  
		  </script>
        </form>
        </td>
    </tr>
  </tbody>
</table>
<br />
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&1"); 
$linkvolt = str_replace("+","---",$linkvolt);
// -----------------------------

break;
case 3:  //----OCORRENCIAS

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$clt = $_REQUEST['clt'];

$movimento = $_REQUEST['movimento'];
$data = $_REQUEST['data'];
$qntdias = $_REQUEST['dias'];
$obs = $_REQUEST['obs'];

/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/


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
 return "0";
 }
}

$data = ConverteData($data);

$dataE = explode("-",$data);
$anoE = $dataE[0];
$mesE = $dataE[1];
$diaE = $dataE[2];

$data_re = date("Y-m-d", mktime(0,0,0,$mesE, $diaE + $qntdias, $anoE));

$RSClt = mysql_query("SELECT id_clt,id_curso FROM rh_clt WHERE id_clt = '$clt'");
$RowCLT = mysql_fetch_array($RSClt);

$RSStatus = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$movimento'");
$RowStatus = mysql_fetch_array($RSStatus);


mysql_query("INSERT INTO rh_eventos(id_clt,id_regiao,id_projeto,nome_status,cod_status,id_status,data,data_retorno,dias,obs)
VALUES ('$clt','$regiao','$projeto','$RowStatus[especifica]','$movimento','$RowStatus[0]','$data','$data_re','$qntdias','$obs')");

$id_evento = mysql_insert_id();

mysql_query("UPDATE rh_clt SET status = '$movimento' WHERE id_clt = '$clt' LIMIT 1");


//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt&$id_evento&$data"); 
$link = str_replace("+","---",$link);
// ----------------------------- ?>

<script>
location.href="form_evento.php?enc=<?=$link?>"
</script>

<?php
break;
}
?>
</div>
</body>
</html>