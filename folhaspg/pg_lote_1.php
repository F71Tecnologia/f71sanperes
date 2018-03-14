<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include('../classes_permissoes/regioes.class.php');

$REGIAO = new Regioes();

if(!empty($_REQUEST['agencia'])){
	include "../conn.php";
	$nome = $_REQUEST['nome'];
	$cpf = $_REQUEST['cpf'];
	$banco = $_REQUEST['banco'];
	$ag = $_REQUEST['agencia'];
	$cc = $_REQUEST['conta'];
	
	$enc = $_REQUEST['enc'];
	$enc2 = str_replace("+","--",$enc);
	
	$clt = $_REQUEST['clt'];
	$id = $_REQUEST['id'];
	$id_folha = $_REQUEST['id_folha'];
	$tipopg = $_REQUEST['tipopg'];


	$tipo_conta = $_REQUEST['radio_tipo_conta'];
 	$RE_clt = mysql_query("SELECT * FROM folha_autonomo where id_autonomo = '$id' and status IN('3','4')") or die (mysql_error());
	
	$RowCLT = mysql_fetch_array($RE_clt);
	
	mysql_query("UPDATE autonomo SET nome='$nome', cpf='$cpf', banco='$banco',agencia='$ag', conta='$cc', tipo_conta='$tipo_conta', tipo_pagamento='$tipopg' WHERE id_autonomo = '$id'") or die (mysql_error());
	
	mysql_query("UPDATE folha_autonomo SET nome='$nome', cpf='$cpf', banco='$banco', agencia='$ag', conta='$cc', tipo_pg='$tipopg' WHERE id_autonomo = '$id' and id_folha = '$id_folha'") or die (mysql_error());
	
	/*
	print "<div style='backgroud:red'>";
	echo "NOME: ".$nome."<BR>";
	echo "CPF: ".$cpf."<BR>";
	echo "Banco: ".$banco."<BR>";
	echo "tipo: ".$radio_tipo_conta."<BR>";
	echo "AG:".$ag."<BR>";
	echo "CC:".$cc."<BR>";
	echo "CLT:".$clt."<BR>";
	echo "ID: ".$id."<BR>";
	echo "TIPO:".$tipo_conta."<BR>";
	echo "Tipo pg:".$tipopg."<BR>";
	echo "folha:".$id_folha."<BR>";
	echo "<a href='folha_banco.php?enc=$enc'>Continuar</a>";
	print "</div>";
	*/
	
	
	print"
	<script>
	location.href=\"ver_lista_banco.php?enc=$enc2\"
	</script>";
	
	
	exit;
	
}

include "../conn.php";
include "../funcoes.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
//MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

//$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and status = '3'");

//VERIFICANDO SE É FOLHA DE COOPERADO OU DE AUTONOMO
if($row_folha['contratacao'] == 1){
$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status IN('3','4') ORDER BY banco, nome");
}else{
$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha = '$folha' AND status IN('3','4') ORDER BY banco, nome");
}

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
$row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

$RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
$row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);


?>
<html>
<head>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>

<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<style>
.pagar{

text-decoration:none;
width:200px;
height:auto;
text-align:center;
border:2px solid  #737373;
background-color: #003D79;
color: #FFF;
padding:5px;
margin-bottom:20px;
font-weight:bold;
font-size:12px;
	
}

.pagar:hover{

background-color:#C0C0C0;
color:#000;
	
}

</style>

<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
<script language='javascript'>
$(function(){

$('#data').datepicker({
		changeMonth: true,
	    changeYear: true
	});

	
$('#regiao').change(function(){
	
	
	var id_regiao = $(this).val();
	$.ajax({		
		url : 'actions/dados_gera_saida.php?regiao='+id_regiao,
		success :function(resposta){
				$('#projeto').html(resposta);	
			}
		});
	
	});	
	

$('#projeto').change(function(){
	
	
	var id_projeto = $(this).val();
	$.ajax({
		
		url : 'actions/dados_gera_saida.php?projeto='+id_projeto,
		success :function(resposta){
			
				$('#banco').html(resposta);	
			}
		
		});
	
	
	});	
	
	
	
$('.marca_todos').change(function(){	
	var checked = $(this).attr('checked');
	
      
	if(checked) {		
	$('input[name=trabalhador[]]').attr('checked',true);		
	}else {
	$('input[name=trabalhador[]]').attr('checked',false);
	}
	
});


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
});
</script>
</head>
<body>
    <form name="form" method="post" action="actions/gerar_saida_folha_lote.php" />
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" class="show">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="17%" height="100" align="center"><span class="texto10"><img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="58%"><span class="texto10">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="25%">
            <span class="texto10">
            Data de Processamento: <br>
            <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
      </tr>
      
      
    </table>
      <br />
      <span class="title">Lista de Participantes - 
      <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <span class="title"><br />
    </span>
    <?
		//VERIFICA SE A FOLHA JÁ FOI FINALIZADA
		$resultStatusFolha = mysql_query("SELECT status FROM folhas WHERE id_folha = $folha");
		$rowStatusFolha = mysql_fetch_array($resultStatusFolha);
		if ($rowStatusFolha[0] == '4'){
				print "<span style='color:red; font-family:verdana, areal'> <strong>FINALIZADA</strong> </span>";
		}
	?>
    <br/>
    <br/>
	
      <table width="90%" border="1" align="center" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border: #000 1px solid;">
        <tr>
          <td><input type="checkbox" class="marca_todos" /></td>
          <td width="10%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="29%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>
          <td width="13%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Banco</td>
          <td width="10%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Agência</td>
          <td width="10%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Conta</td>
          <td width="12%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">CPF</td>
          <td width="10%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Tipo de conta</td>
          <td width="19%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">L&iacute;q.</td>
          <td  width="19%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Enviar p/ o Financeiro</td>
        </tr>
        <?php
          $cont = "0";
		  while($row = mysql_fetch_array($result_folha_pro)){
		  
		  $REparti = mysql_query("SELECT * FROM autonomo where id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);	 
		
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  $id_banco = $row['banco'];
		  $resultNomeBanco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$id_banco'");
		  $rowNomeBanco = mysql_fetch_array($resultNomeBanco);
		  $nomeBanco = $rowNomeBanco[0];
		  if($nomeBanco == NULL){
			  $nomeBanco = $rowP['nome_banco'];
		  }
		  
		  $agencia = $row['agencia'];
		  $conta = $row['conta'];
		  if ($rowP['tipo_conta'] == 'corrente'){
		  		$tipoConta = 'Conta Corrente';
		  }else if ($rowP['tipo_conta'] == 'salario'){
					$tipoConta = 'Conta Salario';
		  }else{
		  		$tipoConta = '&nbsp;';
		  }
		  $cpf = $row['cpf'];
		  $valor = number_format($row['salario_liq'],2,".","");

		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 0px; height:35px;'";
		  //-----------------

		$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
	     $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
	  	  $pgEmCheque = $rowTipoPg[0];		  
		  
		  $resultCheque = mysql_query("SELECT tipo_pg, banco FROM folha_autonomo WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo] and status = '3'");
		  $rowTipoPg = mysql_fetch_array($resultCheque);
		  
		  if ($rowTipoPg[0] == $pgEmCheque){
			  $option ="<option value='$pgEmCheque'>Cheque</option><option value='$row_TipoDepo[0]' >Depósito</option>";		  	
		  }else{
		  		$option ="<option value='$row_TipoDepo[0]' >Depósito</option><option value='$pgEmCheque'>Cheque</option>";
		  }
		  
			$alink = "<a href='#' onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', 
			wrapperClassName: 'draggable-header',headingText: '$nomeT' } )\" class='highslide' style='color=#000000'>";
			
			$NovoBanco = "";
			$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and id_projeto = '$row_projeto[0]'");
			$result_bancoAUT = mysql_query("SELECT nome FROM bancos where id_banco = '$row[banco]'");
			$RowBancAUT = mysql_fetch_array($result_bancoAUT);
			
			$N = $row['id_autonomo'];
			$NovoBanco .="<select name='banco' id='banco$N' style='display:'>";
			$NovoBanco .= "<option value='00'>SELECIONE</option>";
			while ($row_banco = mysql_fetch_array($result_banco)){	
				$NovoBanco .= "<option value='$row_banco[0]'>".$row_banco['nome']."</option>";	
			}
			$NovoBanco .= "</select>";
			
			$divTT = "<div class='highslide-maincontent'>
			<form action='ver_lista_banco.php' method='post' name='form1'>
			<table width='526' border='0' cellspacing='0' cellpadding='0'>
			
			<tr>
			    <td align='center' bgcolor='#f0f0f0' colspan='4'>
				<div style='font-size:12px'>
				$RowBancAUT[0]&nbsp;&nbsp;<input type='checkbox' name='mudbanco' value='1' 
				onClick=\"document.getElementById('banco$N').style.display = (document.getElementById('banco$N').style.display == 'none') ? '' : 'none'  \";> Alterar
				</div>
				</td>
			  </tr>
			
			
			   <tr>
			    <td align='right' bgcolor='#f0f0f0'>Nome</td>
			    <td>&nbsp;<input name='nome' type='text' size='25' id='nome' value='$rowP[nome]'/>&nbsp;</td>
			    <td align='right' bgcolor='#f0f0f0'>CPF</td>
			    <td>&nbsp;<input name='cpf' type='text' size='15' maxlength='14' id='cpf' value='$rowP[cpf]'/></td>
			  </tr>
			  
			  <tr>
			    <td align='right' bgcolor='#f0f0f0'>Bancos:</td>
			    <td>&nbsp;$NovoBanco
				</td>
			    <td align='right' bgcolor='#f0f0f0'>Tipo de PG:</td>
			    <td>&nbsp;
				<select name='tipopg' id='tipopg'>
				$option
				</select></td>
			  </tr>
			  
			  <tr>
			    <td align='right' bgcolor='#f0f0f0'>Agencia</td>
			    <td>&nbsp;<input name='agencia' type='text' size='15' maxlength='10' id='agencia' value='$rowP[agencia]'/>&nbsp;</td>
			    <td align='right' bgcolor='#f0f0f0'>Conta</td>
			    <td>&nbsp;<input name='conta' type='text' size='15' maxlength='10' id='conta' value='$rowP[conta]'/></td>
			  </tr>
			  
			  <tr>
			    <td align='right' bgcolor='#f0f0f0'>Tipo de Conta</td>
			    <td colspan='2'>&nbsp;
				<label><input type='radio' name='radio_tipo_conta' value='salario' $checkedSalario>Conta Salário </label>
				&nbsp;&nbsp;
				<label><input type='radio' name='radio_tipo_conta' value='corrente' $checkedCorrente>Conta Corrente </label></td>
			  </tr>
			  <tr>
			    <td colspan='3' align='center'><input type='submit' value='Enviar' /></td>
			  </tr>
			  
			</table>			
			<input type='hidden' name='id_folha' value='$folha'>
			</form>
			</div>";

		  ?>
		  <tr height='20' class='linhadois' bgcolor=<?php echo $color; ?> >
          <td><input type="checkbox" name="trabalhador[]" value="<?php echo $row['id_folha_pro'] ?>"/> </td>
          <td align='center' valign='middle' <?php echo $bord ?> >&nbsp;<?php echo $rowP['campo3'] ?></td>
          <td align='lefth' valign='middle' <?php  echo $bord ?>>&nbsp; <?php echo $alink.' '.$nomeT ?> </a> <?php echo $divTT;?></td>
          <td align='right' valign='middle'<?php  echo $bord ?>>>&nbsp;<?php echo $nomeBanco ?></td>
          <td align='right' valign='middle'<?php  echo $bord ;?>>&nbsp;<?php echo $agencia ?></td>
          <td align='right' valign='middle'<?php  echo $bord; ?>>&nbsp;<?php echo $conta ?></td>
		  <td align='right' valign='middle'<?php  echo $bord; ?>>&nbsp;<?php echo $cpf  ?></td>
          <td align='right' valign='middle'<?php  echo $bord; ?>>&nbsp;<?php echo $tipoConta ?></td>
          <td align='right' valign='middle'<?php  echo $bord; ?>>&nbsp;<?php echo $valor ?></td>
		  <td align='center' valign='middle' <?php  echo $bord; ?>>
          
          <?php if($row['vinculo_financeiro'] == 1 ) {?>
          
          <img src="../imagens/bolha2.png" width="18" height="18" title="ENCAMINHADO PARA O FINANCEIRO"/>
          
          <?php } else { ?>
          
          <a href="actions/gerar_saida_folha.php?id_trab=<?php echo $rowP['id_autonomo'];?>&tipo=autonomo&folha=<?php echo $row_folha['id_folha'];?>"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><img src="../imagens/bolha1.png" width="18" height="18" title="ENVIAR PARA O FINANCEIRO"/></a>
          
          <?php } ?>
          
           </td>
		  </tr>
          <?php
		  unset($checkedSalario);
		  unset($checkedCorrente);
		  unset($tipoConta);
		  unset($option);
	  

		  $cont ++;
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_liq = $TOsal_liq + $sal_liq; 
		  $sal_liqT = "";
		  
		  }
		
		
		//-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		  $TOsal_liqF = number_format($row_folha['total_liqui'],2,",",".");
		?>
        <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td colspan="5"></td>
          <td align="right">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
          <td></td>
        </tr>
      </table>
      <br />
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
      

    <tr height="110">
    	<td  colspan="2" align="center" valign="middle">            
                        <table border="0" style="font-size:12px;height:200px;">

                            <tr>
                                <td> <strong>REGIÃO:</strong> </td>
                                        <td>
                                        <select name="regiao" id="regiao">
                                <?php        
                                        $REGIAO->Preenhe_select_sem_master();
                                        ?>        
                                </select>
                                        </td>

                                </tr>

                            <tr>
                                <td> <strong>PROJETO:</strong> </td>
                                <td>
                                <select name="projeto" id="projeto"></select>        
                                </td>
                            </tr> 
                            <tr>
                                <td><strong>BANCO:</strong></td>
                                <td>
                                <select name="banco" id="banco">

                                </select>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>DATA DE VENCIMENTO:</strong></td>
                                <td><input type="text" name="data_vencimento" id="data"/></td>
                            </tr>


                            <tr>
                                <td><strong>VALOR:</strong></td>
                                <td>R$ <?php echo $row_folha['total_liqui'];?></td>
                            </tr>

                            <tr>
                              <td colspan="2" align="center">
                                  <input name="enc" type="hidden" value="<?php echo $_REQUEST['enc']?>"/>
                                      <input type="submit" value="CONFIRMAR" name="confirmar"/>
                              </td>
                            </tr>
                        </table>
            

	  	</td>
   </tr>
	  
	 
     
      
        <tr>
          <td height="36" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="show">TOTALIZADORES</td>
        </tr>
        <tr>
          <td width="46%" height="30" align="right" valign="middle" bgcolor="#f0f0f0" class="secao"><span class="linha">L&iacute;quido:</span></td>
          <td width="54%" height="30" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> &nbsp;&nbsp;<span class="style23">
            <?=$TOsal_liqF?>
          </span></span></td>
        </tr>
        <tr>
          <td height="30" align="right" valign="middle" bgcolor="#f0f0f0" class="secao"><span class="linha">Funcion&aacute;rios Listados:</span></td>
          <td height="30" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=$cont?></td>
        </tr>
      </table>
      <br>            
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
//$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt =encrypt("$regiao&$folha");
$linkvolt = str_replace("+","--",$linkvolt);
// -----------------------------

if($row_folha['contratacao'] == 1){
	$link = "ver_folha.php?enc=$linkvolt&tela=1";
}else{
	$link = "ver_folhacoop.php?enc=$linkvolt";
}

?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='<?=$link?>' class="botao">VOLTAR</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</form>
</body>
</html>