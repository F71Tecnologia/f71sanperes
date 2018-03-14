<?
$CONT02 = $_REQUEST['cont02'];
$DATA = $_REQUEST['data'];
$regiao = $_REQUEST['regiao'];

include "../../../empresa.php";

//RECEBENDO AS MATRICULAS E COLOCA NO ARRAY
for ($i=0; $i<=$CONT02; $i++){
	$id_func[] = $_REQUEST['checkbox'.$i];
	$recarga[] = $_REQUEST['recarga'.$i];
}

//CONSTANTE DE IDENTIFICAÇÃO DO ARQUIVO
$CONSTANTE = "CADUSU";
//NÚMERO DA VERSÃO DO LAYOUT DO ARQUIVO
$VERSAO = '0301';
					
//OBTEM O CNPJ DA EMPRESA QUE ESTÁ NA CLASSE EMPRESA
$cnpj = new empresa();				
$cnpj = $cnpj -> cnpjEmpresa3();				
					
//RETIRANDO A FORMATACAO DO CNPJ
$remover01 = array(".", "-", "/");
$cnpj = str_replace($remover01, "", $cnpj);
					
//INSERE ZERO A DIREITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 14 DIGITOS
$CNPJ = sprintf("%014s",$cnpj);


$Nr_seq_reg01 = 1;
$Nr_seq_regF = sprintf("%05d", $Nr_seq_reg01);

$Tp_registro = '01'; //TIPO DO REGISTRO: 01 - HEADER DO ARQUIVO

$Nm_arquivo = $CONSTANTE; //CONSTANTE QUE IDENTIFICA O ARQUIVO
$Nm_arquivo = sprintf("% -6s", $Nm_arquivo);

$Nr_versao = '03.01'; //NUMERO DA VERSÃO DO LAYOUT DO ARQUIVO
$Nr_versao = sprintf("% -5s", $Nr_versao);

$Nr_doc_arq = $CNPJ; //NUMERO DO CNPJ DO COMPRADOR

$dataE = explode("-", $DATA);
$d=$dataE[2];
$m=$dataE[1];
$a=$dataE[0]; 
$data = $a.$m.$d;
					
//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 8 DIGITOS
$DATA = sprintf("%08d",$data);

$HORA = '1200'; 

$handle = fopen ($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt", "a");

fwrite($handle, $Nr_seq_regF, 5);
fwrite($handle, $Tp_registro, 2);
fwrite($handle, $Nm_arquivo, 6);
fwrite($handle, $Nr_versao, 5);
fwrite($handle, $Nr_doc_arq, 14);				
fwrite($handle, "\r\n");//QUEBRA DE LINHA

$Nr_seq_reg02 = $Nr_seq_reg01;
$quant = count ($id_func);
for($i=0; $i<$quant; $i++){	
	$result = mysql_query("SELECT id_clt, nome, cpf, data_nasci, sexo, rg, orgao, tel_fixo FROM rh_clt WHERE id_clt = '$id_func[$i]'");	
	$rowUsuario = mysql_fetch_array($result);
	
	$Nr_seq_reg02 = $Nr_seq_reg02+1;
	$Nr_seq_reg02F = sprintf("%05d", $Nr_seq_reg02);
	
	$Tp_registro = '02';

	$Nr_matricula = $rowUsuario['id_clt'];
	$Nr_matriculaF = sprintf("% -15s", $Nr_matricula);
	
	$Nm_usuario = $rowUsuario['nome'];
	$Nm_usuarioF = sprintf("% -40s", $Nm_usuario);
	
	$Nr_CPF = $rowUsuario['cpf'];
	$remover02 = array(".", "-", "/");
	$Nr_CPF = str_replace($remover02, "", $Nr_CPF);
	$Nr_CPFF = sprintf("%011s",$Nr_CPF);
	
	$Vl_uso_diario = '100';
	$Vl_uso_diarioF = sprintf("%06d", $Vl_uso_diario);
	
	$recarga = $recarga[$i];
	$codio=explode ("-", $recarga);
	$Cd_cidade = $codio[0];
	$Cd_rede_recarga = $codio[1];
	
	$Nr_cartao = ' ';
	$Nr_cartaoF = sprintf("% 13s", $Nr_cartao);
	
	$Cd_impressao = '04';
	
	$Dt_nascimento = $rowUsuario['data_nasci'];
	$dataN = explode("-",$Dt_nascimento);
	$d = $dataN[2];
	$m = $dataN[1];
	$a = $dataN[0];
	$data_nasci = $d.$m.$a;
	$Dt_nascimentoF = sprintf("%08d",$data_nasci);
	
	$Tp_sexo = $rowUsuario['sexo'];
	
	$Tx_doc_ident = $rowUsuario['rg'];
	$remover01 = array(".", "-", "/");
	$Tx_doc_ident = str_replace($remover01, "", $Tx_doc_ident);
	$Tx_doc_identF = sprintf("% -15s",$Tx_doc_ident);

	$Sg_orgao_emissor = $rowUsuario['orgao'];
	$Sg_orgao_emissorF = sprintf("% -6s", $Sg_orgao_emissor);
		
	$Nr_tel_contato = $rowUsuario['tel_fixo'];
	$remover03 = array(".", "-", "/","(",")");
	$Nr_tel_contato = str_replace($remover03, "", $Nr_tel_contato);
	$Nr_tel_contatoSemDDD = substr($Nr_tel_contato,2);
	$Nr_tel_contatoF = sprintf ("% -10s", $Nr_tel_contatoSemDDD);
	
	$ddd = substr($Nr_tel_contato, 0, 2);
	$Nr_ddd_telF = sprintf ("%03s", $ddd);

	$Tx_email = 'sorrindo@sorrindo.org.br';	
	$Tx_emailF = sprintf("% -60s", $Tx_email);
	
	$handle = fopen ($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt", "a");
	fwrite($handle, $Nr_seq_reg02F, 5);
	fwrite($handle, $Tp_registro, 2);
	fwrite($handle, $Nr_matriculaF, 15);	
	fwrite($handle, $Nm_usuarioF, 40);
	fwrite($handle, $Nr_CPFF, 11);
	fwrite($handle, $Vl_uso_diarioF, 6);
	fwrite($handle, $Cd_cidade, 2);
	fwrite($handle, $Cd_rede_recarga, 2);
	fwrite($handle, $Nr_cartaoF, 13);
	fwrite($handle, $Cd_impressao, 2);
	fwrite($handle, $Dt_nascimentoF, 8);
	fwrite($handle, $Tp_sexo, 1);
	fwrite($handle, $Tx_doc_identF, 15);
	fwrite($handle, $Sg_orgao_emissorF, 6);
	fwrite($handle, $Nr_ddd_telF, 3);
	fwrite($handle, $Nr_tel_contatoF, 10);
	fwrite($handle, $Tx_emailF, 60);	
	fwrite($handle, "\r\n");
	
}

//TRAILLER
$Nr_seq_reg02 = $Nr_seq_reg02+1;
$Nr_seq_reg03 = $Nr_seq_reg02 ;
$Nr_seq_reg03F = sprintf("%05d",$Nr_seq_reg03);

$Tp_registro = 99;
$Tp_registroF = sprintf("%02d", $Tp_registro);

$handle = fopen ($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt", "a");

fwrite($handle, $Nr_seq_reg03F, 5);
fwrite($handle, $Tp_registroF, 2);
fclose($handle);
?>

<?
//FUNCIONÁRIOS EXCLUIDOS
$CONT03 = $_REQUEST['cont03'];
//APAGAR
print $CONT03.'<br>';

for ($i2=0; $i2<=$CONT03;$i2++){
	$id_func2[] = $_REQUEST['checkbox2'.$i2];
}

echo "<script> location.href=\"../../rh_valerelatorios.php?regiao=$regiao\";</script>";

?>