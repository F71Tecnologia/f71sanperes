<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include "../../classes_permissoes/acoes.class.php";

$Func = new funcionario();
$ACOES = new Acoes();


$id_user = $_COOKIE['logado'];


$sql = "SELECT * FROM funcionario WHERE id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$ano = date("Y");
	
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

$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);


// Id da Folha
$enc   = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
$folha = $enc[1];



$qr_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

$qr_folha_Proc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha'");





//////////////////////////////////////
if(isset($_POST['importar'])){
	
	
		$arquivo = $_FILES['arquivo_ponto'];
		
		
		
		if($arquivo['type'] != 'text/plain'){
			
		echo '<script> alert("Formato de arquivo inválido")</script>';
		
			
		} else {
			
			
		
		
			
			
			
			
			
			
		 if(move_uploaded_file($arquivo['tmp_name'], 'arquivos_ponto/'.$folha.'.txt')) 
		 {
			
				
			///////VERIFICANDO SE O ARQUIVO PERTENCE AO MASTER ATUAL
		/*	$arquivo = fopen('arquivos_ponto/'.$folha.'.txt');

			 while (!feof($arquivo)) {
				 
			        $buffer 			= fgets($arquivo);    	
					$registro 			= substr($buffer,9,1);		
							
					
					///////////DAODS DA EMPRESA REGISTRO 2
					if($registro == 1){
					$cnpj = substr($buffer,11,14);
					
					
					//verifica CNPJ
					
					if($cnpj != $row_master['cnpj']){
					
							echo $cnpj;
					
						
					}
						
					}
					
			 }
			 */
			
			
			
			
			
			header("Location: teste_ponto.php?enc= $_REQUEST[enc]");
			exit;
		 }
		
			
			
			
		}
		
}
//////////////////////////////////////////////////////









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
                                  
    	  <br /><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" width="110" height="79"/>
              
			<h3> FOLHA DE PAGAMENTO - <?php echo $row_regiao['regiao']?>
            		<br>
                    FOLHA: <?php echo $row_folha['id_folha']?>
                    <br>
                    <?php echo $row_folha['mes'].'/'.$row_folha['ano'];?>
            
            </h3>
      
     <br>
     <form name="form" action="importar_ponto.php?enc=<?php echo $_REQUEST['enc']; ?>" enctype="multipart/form-data" method="post">
      <table class="relacao">
      <tr>
      		<td colspan="2" class="titulo_tabela1">IMPORTAÇÂO DO ARQUIVO DE PONTO</td>
      </tr>
      <tr>
      	<td class="secao_nova">ARQUIVO: </td>
      	<td ><input type="file" name="arquivo_ponto"/></td>
      </tr>
      <tr>	
      	<td colspan="2">&nbsp;</td>
      </tr>
      <tr>
      	<td colspan="2"><input type="submit" name="importar" value="Importar"/></td>
      </tr>
      
	  
	</table>
    </form>
      
      
      
     
      </div>      
</div>
</body>
</html>