<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";
require('../classes/pdf/fpdf.php');

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_funcionario);
$qr_master = mysql_query("SELECT * FROM master WHERE  id_master = '$row_func[id_master]'") or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);



if(isset($_GET['clt'])){

$id_clt = mysql_real_escape_string($_GET['clt']);
$qr_trabalhador = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
$row_trab  = mysql_fetch_assoc($qr_trabalhador);	

$tabela_processo = 'processos_interno';
$id_trab 		 = "id_clt = '$row_trab[id_clt]'";
}




$pdf = new FPDF();

////ABERTURA DE PROCESSO
$qr_processo 			   = mysql_query("SELECT * FROM $tabela_processo WHERE $id_trab");
$row_processo 			   = mysql_fetch_assoc($qr_processo);
$verifica_abertura_processo = mysql_num_rows($qr_processo);
if($verifica_abertura_processo != 0) {
	
	$pdf->AddPage();	
	
	$pdf->Image('../imagens/logomaster'.$row_master['id_master'].'.gif',85,30);
	
	$pdf->SetFont('Arial','',12);
	
	
	$pdf->Text(70,160, 'Nº do processo: '.$row_processo['proc_interno_numero']);
	$pdf->Text(70,170, 'Atividade: '.$row_processo['proc_interno_atividade']);
	$pdf->Text(70,180, 'Nome: '.$row_processo['proc_interno_nome']);

}
$pdf->AddPage();





$pdf->Output();


?>