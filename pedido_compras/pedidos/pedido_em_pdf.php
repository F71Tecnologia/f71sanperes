<?php
include('include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include "include/criptografia.php";
require('../../classes/pdf/fpdf.php');

//$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
//$row_func = mysql_fetch_assoc($qr_funcionario);
//$qr_master = mysql_query("SELECT * FROM master WHERE  id_master = '$row_func[id_master]'") or die(mysql_error());
//$row_master = mysql_fetch_assoc($qr_master);

$pedidoH = mysql_query ("SELECT A.id_pedido AS pedido, B.c_razao AS fornecedor, B.c_endereco AS endereco, B.c_cnpj AS cnpj,
                        C.nome AS `unidade`, C.endereco AS end_unidade, A.datadopedido AS datapedido
                        FROM pedidos AS A
                        LEFT JOIN prestadorservico AS B ON (B.id_prestador = A.id_prestador)
                        LEFT JOIN projeto AS C ON (C.id_projeto = A.id_projeto)
                        WHERE A.id_pedido = $id_pedido')");
//
//
//if(isset($_GET['pedido'])){
//    $id_pedido = mysql_real_escape_string($_GET['pedido']);
//    $qr_itenspedido = mysql_query("SELECT * FROM pedidos_itens WHERE id_pedido = '$id_pedido'");
//    $row_pedido  = mysql_fetch_assoc($qr_itenspedido);	
//
//    $tabela_processo = 'processo_interno';
//    $id_itens = "id_pedido = '$row_itens[id_item]'";
//}
 
$pdf = new FPDF();
	
$pdf->AddPage();	
	
	$pdf->Image('../imagens/logomaster'.$row_master['id_master'].'.gif',85,30);
	
	$pdf->SetFont('Arial','',12);
	
	$pdf->Text(70,160, 'Nº do processo: '.$row_processo['proc_interno_numero']);
	$pdf->Text(70,170, 'Atividade: '.$row_processo['proc_interno_atividade']);
	$pdf->Text(70,180, 'Nome: '.$row_processo['proc_interno_nome']);

$pdf->AddPage();

$pdf->Output('pedido_'.$row['id_pedido'].'.pdf', 'I');

