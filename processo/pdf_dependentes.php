<?php

 include('include/restricoes.php');
  include('../classes/pdf/fpdf.php');
  
 $id_prestador = $_GET['prestador'];
  
  if(!empty($id_prestador)) {
	  
	  $qr_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$id_prestador' ;");
	  $row_prestador = mysql_fetch_assoc($qr_prestador);
  }
  
  
  define('FPDF_FONTPATH','font/');
  
  $fonte_titulo = 9;



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

$pdf->SetMargins(5, 5, 5);



//escreve no pdf largura,altura,conteudo,borda,quebra de linha,alinhamento
$pdf->Cell(0,5,$titulo,10,0,'C');
$pdf->Ln(8);

$pdf->SetFont('arial','I',  $fonte_titulo);
$sub_titulo = utf8_decode("IMPOSTO DE RENDA NA FONTE");

$pdf->Cell(0,5,$sub_titulo,0,1,'C');


//dados do responsável
$altura = 0;
$largura = 70;
$linha = 5;
$linha_2= 5;


$pdf->Ln(1);
$pdf->Cell(0,0,'',1,1,'L');
$pdf->Ln($linha);

//CAMPO NOME

$pdf->SetFont('arial','B',  $fonte_titulo);
$pdf->Cell(15,$altura,'NOME: ',0,0,'L');

$pdf->SetFont('arial','',  $fonte_titulo);
$pdf->Cell($largura ,$altura,utf8_decode($row_prestador['c_responsavel']),0,0,'L');

//CAMPO NOME



//CAMPO RG

$pdf->Ln(6);

$pdf->SetFont('arial','B',  $fonte_titulo);
$pdf->Cell(8,$altura,'RG:',0,0,'L');

$pdf->SetFont('arial','',  $fonte_titulo);
$pdf->Cell($largura,$altura,utf8_decode($row_prestador['c_rg']),0,0,'L');

//CAMPO RG





//CAMPO MATRÍCULA

$pdf->SetFont('arial','B',  $fonte_titulo);

$pdf->Cell(65,$altura,utf8_decode('MATRÍCULA:'),0,0,'R');


$pdf->SetFont('arial','',  $fonte_titulo);
$pdf->Cell(21,$altura,utf8_decode($row_prestador['id_prestador']),0,0,'L');

//CAMPO MATRÍCULA


//CAMPO ESTADO CIVIL
$pdf->Ln(6);
$pdf->SetFont('arial','B',  $fonte_titulo);

$pdf->Cell(28,$altura,utf8_decode('ESTADO CIVIL:'),0,0,'L');

$pdf->SetFont('arial','',  $fonte_titulo);
$pdf->Cell(30,$altura,utf8_decode($row_prestador['c_civil']),0,0,'L');

//CAMPO ESTADO CIVIL


//CAMPO DATA DE NASCIMENTO

$pdf->SetFont('arial','B',  $fonte_titulo);

$pdf->Cell(85,$altura,utf8_decode('DATA DE NASCIMENTO:'),0,0,'R');

$pdf->SetFont('arial','',  $fonte_titulo);
$pdf->Cell(43,$altura,utf8_decode(implode('/',array_reverse(explode('-',$row_prestador['c_data_nascimento'])))),0,0,'L');

//CAMPO DATA DE NASCIMENTO


//CAMPO FUNÇÃO
$pdf->Ln(6);
$pdf->SetFont('arial','B',  $fonte_titulo);
$pdf->Cell(16,$altura,utf8_decode('FUNÇÃO:'),0,0,'L');

$pdf->SetFont('arial','',  $fonte_titulo);

$pdf->Cell(47,$altura,$row_prestador['especificacao'],0,0,'L');

//CAMPO FUNÇÃO


$pdf->Ln($linha);
$pdf->Cell(0,0,'',1,1,'L');


$pdf->SetFont('arial','BIU',  $fonte_titulo);

$pdf->Ln(8);
$pdf->Cell(0,0,utf8_decode('Podem ser Dependentes, para efeito do Imposto de Renda : '),0,0,'C');

$pdf->SetFont('arial','',9);

$pdf->Ln(8);
$pdf->Cell($largura,$altura,utf8_decode('1-		Companheiro (a) com quem o contribuinte tenha filho ou viva há mais de 5 anos, ou cônjuge;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('2-		Filho (a) ou enteado (a) até 21 anos de idade, ou, em qualquer idade, quando incapacitado física ou mentalmente para o trabalho;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('3-		Filho (a) ou enteado(a) universitário ou cursando escola técina de segundo grau, até 24 anos ;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('4-		Irmão (ã), neto (a) ou bisneto(a), sem arrimo dos pais, de quem o contribuinte detenha a guarda judicial, até 21 anos, ou em qualquer idade, '),0,0,'L');

$pdf->Ln(5);
$pdf->Cell($largura,$altura,utf8_decode('	    quando incapacitado física ou mentalmente para o trabalho;'),0,0,'L');


$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('5-		Irmão (ã), neto (a) ou bisneto (a), sem arrimo dos pais, com idade de 21 anos até 24 anos, se ainda estiver cursando estabelecimento  '),0, 1 ,'L');

$pdf->Ln(5);
$pdf->Cell($largura,$altura,utf8_decode('	    de ensino superior ou escola técnica de segundo grau, desde que o contribuinte tenha detido sua guarda judicial até os 21 anos;'),0, 1 ,'L');



$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('6-		Pais, avós e bisavós que, em 2007, tenham recebido rendimentos , tributáveis ou não, até R$ 14.992,32;'),0,0,'L');


$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('7-		Menor pobre até 21 anos que o contribuinte crie e eduque e de quem detenha a guarda judicial;'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('8-		Pessoa absolutamente incapaz, da qual o contribuinte seja tutor ou curador.'),0,0,'L');

$pdf->SetFont('arial','BIU',10);
$pdf->Ln(8);
$pdf->Cell($largura,$altura,utf8_decode('ATENÇÃO:'),0,0,'L');
$pdf->Ln(7);


$pdf->SetFont('arial','',9);
$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('Filho de pais separados :'),0,0,'L');



$pdf->Ln($linha);

$pdf->SetFont('arial','B',9);
$pdf->Cell(9,$altura,utf8_decode('I-'),0,0,'L');

$pdf->SetFont('arial','',9);
$pdf->Cell($largura,$altura,utf8_decode('O contribuinte pode considerar como dependentes os filhos que ficarem sob sua guarda, em cumprimento de decisão judicial '),0,0,'L');


$pdf->Ln($linha_2);
$pdf->Cell($largura,$altura,utf8_decode('           ou acordo homologado  judicialmente. Nesse caso, deve oferecer à tributação, na sua declaração os rendimentos recebidos pelos '),0,0,'L');

$pdf->Ln($linha_2);
$pdf->Cell($largura,$altura,utf8_decode('           filhos, inclusive a importância recebida do ex-cônjuge a título de pensão alimentícia;'),0,0,'L');



$pdf->Ln($linha);

$pdf->SetFont('arial','B',9);
$pdf->Cell(9,$altura,utf8_decode('II-'),0,0,'L');

$pdf->SetFont('arial','',9);
$pdf->Cell($largura,$altura,utf8_decode('O responsável pelo pagamento da pensão alimentícia pode deduzir o valor efetivamente pago a este título, sendo vedada a '),0,0,'L');


$pdf->Ln($linha_2);
$pdf->Cell($largura,$altura,utf8_decode('           dedução do valor correspondente ao dependente, exceto no caso de separação judicial ocorrida em 2007, quando, podem ser  '),0,0,'L');

$pdf->Ln($linha_2);
$pdf->Cell($largura,$altura,utf8_decode('           deduzidos nesse ano, os valores relativos a dependente e a pensão alimentícia.'),0,0,'L');




$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('O fato de os dependentes receberem no ano-calendário rendimentos tributáveis ou não, não descaracteriza essa condição, desde que tais  '),0,0,'L');

$pdf->Ln($linha_2);
$pdf->Cell($largura,$altura,utf8_decode('rendimentos sejam somados aos do declarante.'),0,0,'L');

$pdf->Ln($linha);
$pdf->Cell($largura,$altura,utf8_decode('( Lei nº 9.250, de 1995, art 35; RIR/1999, art.77, § 1º; IN SRF nº 15, de 2001, art. 38 ) '),0,0,'L');

//Tabela DADOS DO DEPEDENTE

$pdf->SetFont('arial','B',10);
$pdf->Ln(10);
$pdf->Cell(0,$altura,utf8_decode('DADOS DO DEPENDENTE'),0,0,'C');
$pdf->Rect(6,179,200,7,'d');


$pdf->Ln(7);
$pdf->Cell(100,$altura,utf8_decode('NOME'),0,0,'C');
$pdf->Rect(6,186,200,7,'D');
$pdf->Rect(6,186,100,7,'D');

$pdf->Cell(50,$altura,utf8_decode('GRAU DE PARENTESCO'),0,0,'C');
$pdf->Rect(106,186,50,7,'D');


$pdf->Cell(45,$altura,utf8_decode('DATA DE NASCIMENTO'),0,0,'R');
$pdf->Rect(246,186,50,7,'D');



//linha com os dados

$linha_add = 0;

$pdf->SetFont('arial','',10);
$qr_dependete =  mysql_query("SELECT * FROM prestador_dependente WHERE prestador_id = '$id_prestador' AND prestador_dep_status ='1'");
while($row_dependente = mysql_fetch_assoc($qr_dependete)):


$cont++;	


	
	
	
$pdf->Rect(6,192.9+$linha_add,200,7,'D');

$pdf->Ln(7);
$pdf->Cell(100,$altura,utf8_decode( $row_dependente['prestador_dep_nome'] ),0,0 ,'C');
$pdf->Rect(6,192.9+$linha_add,100,7,'D');

$pdf->Cell(50,$altura,utf8_decode($row_dependente['prestador_dep_parentesco']),0,0,'C');
$pdf->Rect(106,192.9+$linha_add,50,7,'D');

$pdf->Cell(50,$altura,utf8_decode(implode('/',array_reverse(explode('-',$row_dependente['prestador_dep_data_nasc'])))),0,0,'C');
$pdf->Rect(246,192.9+$linha_add,50,7,'D');






$linha_add += 7;

endwhile;







//imprime a saida do arquivo..
$pdf->Output($id_prestador.".pdf","I");



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