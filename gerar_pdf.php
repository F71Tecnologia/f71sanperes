<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include 'fpdf/fpdf.htm'; 

$empresa = $_POST['Empresa']; 
$site = $_POST['Site']; 
$titulo = $_POST['Titulo']; 
$conteudo = $_POST['Conteudo']; 

//define as fontes atraves da pasta font 
define('FPDF_FONTPATH','fpdf/font/'); 

//instancia a classe FPDF passando a os parametros Orientação , Medida e Tipo de Folha; 
$pdf = new FPDF("P","mm","A5"); 

//fonte 
$pdf->SetFont('arial','',10); 

//posicao vertical no caso -1.. e o limite da margem 
$pdf->SetY("-2"); 


//::::::::::::::::::Cabecalho:::::::::::::::::::: 
//escreve o titulo.... 
//largura = 0 
//altura = 5 
//texto = $titulo 
//borda = 0 
//quebra de linha = 0 
//alinhamento = L (esqueda) 
$pdf->Cell(0,5,$titulo,0,0,'L'); 

//escreve o nome da empresa... 
//largura = 0 
//altura = 5 
//texto = $empresa 
//borda = 0 
//quebra de linha = 1 
// alinhamento = R (direita) 
$pdf->Cell(0,5,$empresa,0,1,'R'); 

//escreve uma linha... 
//largura = 0 
//altura = 0 
//texto = '' 
//borda = 1 
//quebra de linha = 1 
// alinhamento = L (esqueda) 
$pdf->Cell(0,0,'',1,1,'L'); 

//quebra de linha 
$pdf->Ln(8); 


//::::::::::::::::::Conteudo::::::::::::::::::::: 
//fonte 
$pdf->SetFont('times','',8); 

//posiciona verticalmente 
$pdf->SetY("20"); 

//posiciona horizontalmente 
$pdf->SetX("10"); 

//escreve o conteudo de novo.. 
$pdf->MultiCell(0,5,$conteudo,0,1,'J'); 



//::::::::::::::::::definindo o rodapé:::::::::::::::::::::: 
//posiciona verticalmente 
$pdf->SetY("185"); 

$pdf->SetFont('arial','',8); 

//data atual 
$data = date("d/m/Y"); 
$formtData="criado em ".$data; 

//imprime uma linha... 
//largura = 0 
//altura = 0 
//texto = '' 
//borda = 1 
//quebra de linha = 1 
// alinhamento = L (esqueda) 
$pdf->Cell(0,0,'',1,1,'L'); 

//imprime o nome do site... 
//largura = 0 
//altura = 4 
//texto = $site 
//borda = 0 
//quebra de linha = 0 
// alinhamento = C (centro) 
$pdf->Cell(0,4,$site,0,0,'C'); 

//imprime a data... 
//largura = 0 
//altura = 4 
//texto = $formtData 
//borda = 0 
//quebra de linha = 0 
// alinhamento = R (direita) 
$pdf->Cell(0,4,$formtData,0,0,'R'); 

//imprime o arquivo arquivo..(mostra o arquivo) 
$pdf->Output("arquivo","I"); 

/*:::::::::FUNCOES USADAS:::::::::: 
FPDF - construtor 
SetFont - define a fonte 
Cell - imprime uma célula 
SetY - define a posição de y 
SetX - define a posição de X 
MultiCell - imprime um texto com quebra de linha 
Output - salva ou envia o documento 
Ln - quebra de linha 
*/ 

}
?>