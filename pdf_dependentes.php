<?php

 include('include/restricoes.php');
  include('../classes/pdf/fpdf.php');
  
  
  define('FPDF_FONTPATH','font/');




//instancia a classe.. P=Retrato, mm =tipo de medida utilizada no casso milimetros, tipo de folha =A4
$pdf= new FPDF("P","mm","A4");

//define a fonte a ser usada
$pdf->SetFont('arial','BI',15);

//define o titulo
//$pdf->SetTitle("Testando PDF com PHP !",TRUE);

//assunto
//$pdf->SetSubject("assunto deste artigo!");

// posicao vertical no caso -1.. e o limite da margem
$pdf->SetY("-1");
$titulo=utf8_decode("DECLARAÇÃO DE DEPENDENTES");



//escreve no pdf largura,altura,conteudo,borda,quebra de linha,alinhamento
$pdf->Cell(0,5,$titulo,10,0,'C');
$pdf->Ln(8);

$pdf->SetFont('arial','I',10);
$sub_titulo = utf8_decode("IMPOSTO DE RENDA NA FONTE");

$pdf->Cell(0,5,$sub_titulo,0,1,'C');


//dados do responsável
$altura = 0;
$largura = 70;
$linha = 7;


$pdf->Ln(1);
$pdf->Cell(0,0,'',1,1,'L');
$pdf->Ln($linha);
$pdf->Cell($largura ,$altura,'NOME:',0,0,'L');
$pdf->Cell($largura,$altura,'RG:',0,0,'R');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('FUNÇÃO:'),0,0,'L');
$pdf->Cell($largura,$altura,utf8_decode('MATRÍCULA:'),0,0,'R');

$pdf->Ln($linha);
$pdf->Cell(0,0,'',1,1,'L');


$pdf->SetFont('arial','BI',10);

$pdf->Ln(8);
$pdf->Cell(0,0,utf8_decode('Podem ser Dependentes, para efeito do Imposto de Renda : '),0,0,'C');

$pdf->SetFont('arial','',8);

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('1-	Companheiro (a) com quem o contribuinte tenha filho ou viva há mais de 5 anos, ou cônjuge;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('2-	Filho (a) ou enteado (a) até 21 anos de idade, ou, em qualquer idade, quando incapacitado física ou mentalmente para o trabalho;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('3-	Filho (a) ou enteado(a) universitário ou cursando escola técina de segundo grau, até 24 anos ;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('4-	Irmão (ã), neto (a) ou bisneto(a), sem arrimo dos pais, de quem o contribuinte detenha a guarda judicial, até 21 anos, ou em qualquer idade, quando incapacitado física ou mentalmente para o trabalho;'),0,0,'L');


$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('5-	Irmão (ã), neto (a) ou bisneto (a), sem arrimo dos pais, com idade de 21 anos até 24 anos, se ainda estiver cursando estabelecimento de ensino superior ou escola técnica de segundo grau, desde que o contribuinte tenha detido sua guarda judicial até os 21 anos;'),0,0,'L');


$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('6-	Pais, avós e bisavós que, em 2007, tenham recebido rendimentos , tributáveis ou não, até R$ 14.992,32;'),0,0,'L');


$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('7-	Menor pobre até 21 anos que o contribuinte crie e eduque e de quem detenha a guarda judicial;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('8-	Pessoa absolutamente incapaz, da qual o contribuinte seja tutor ou curador.'),0,0,'L');

$pdf->SetFont('arial','BIU',10);
$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('ATENÇÃO:'),0,0,'L');











//imprime a saida do arquivo..
$pdf->Output("arquivo.pdf","I");

/*
agora imaginem que estes dados viessem do banco de dados ?
que maravilha hein ! seus artigos convertidos em pdf dinamicamente hein?

REFERENCIAS :

FPDF - >Esta é o construtor da classe. Ele permite que seja definido o formato da página, a orientação e a unidade de medida usada em todos os métodos (exeto para tamanhos de fonte).

utilizacao : FPDF([string orientation [, string unit [, mixed format]]])

SetFont -> Define a fonte que será usada para imprimir os caracteres de texto. É obrigatória a chamada, ao menos uma vez, deste método antes de imprimir o texto ou o documento resultante não será válido.

utilizacao : SetFont(string family [, string style [, float size]])

SetTitle - >Define o título do documento.

utilizacao : SetTitle(string title)

SetSubject -> Define o assunto do documento

utilizacao : SetSubject(string subject)

SetX - >Define a abscissa da posição corrente. Se o valor passado for negativo, ele será relativo à margem direita da página.

utilizacao : SetX(float x)

SetY - > Move a abscissa atual de volta para margem esquerda e define a ordenada. Se o valor passado for negativo, ele será relativo a margem inferior da página.

utilizacao : SetY(float y)

Cell - > Imprime uma célula (área retangular) com bordas opcionais, cor de fundo e texto. O canto superior-esquerdo da célula corresponde à posição atual. O texto pode ser alinhado ou centralizado. Depois de chamada, a posição atual se move para a direita ou para a linha seguinte. É possível pôr um link no texto.

Se a quebra de página automática está habilitada e a pilha for além do limite, uma quebra de página é feita antes da impressão.

utilizacao - >Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])

Ln - > Faz uma quebra de linha. A abscissa corrente volta para a margem esquerda e a ordenada é somada ao valor passado como parâmetro.

utilizacao ->Ln([float h])

MultiCell - > Este método permite imprimir um texto com quebras de linha. Podem ser automática (assim que o texto alcança a margem direita da célula) ou explícita (através do caracter n). Serão geradas tantas células quantas forem necessárias, uma abaixo da outra.

O texto pode ser alinhado, centralizado ou justificado. O bloco de células podem ter borda e um fundo colorido.

utilizacao : MultiCell(float w, float h, string txt [, mixed border [, string align [, int fill]]])

Image ->Coloca uma imagem na página - tipos suportados JPG PNG

utilizacao : Image(string file, float x, float y [, float w [, float h [, string type [, mixed link]]]])

Bom mais uma vez.. agradeço se for útil..

qualquer dúvida: alexandre.etf@gmail.com !
*/



?>