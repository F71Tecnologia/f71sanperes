<?php

header("Content-type: image/png");

if($_GET[arquivo] == "desempenho"){


for ($i=0;$i<=2-1;$i++):
  $vetor[nome][$i]=$_GET[nome.($i+1)];
  $vetor[qtd][$i]=$_GET[qtd.($i+1)];
endfor;


}else{

for ($i=0;$i<=$_POST[total_gra]-1;$i++):
  $vetor[nome][$i]=$_POST[nome.($i+1)];
  $vetor[qtd][$i]=$_POST[qtd.($i+1)];
endfor;



}



//---------------------------------------------------- //
// ----------Desenvolvido por Guilherme Mota---------- //
// --------------------13/05/2004--------------------- //
// --- ------Gerador de grafico 3D dinamico----------- //
// -Otima demonstraçao de uso da biblioteca GD do PHP- //
// --------------------------------------------------- //

// -------------------------------------------------------------------- //
//  Este programa gera um grafico a partir de um vetor multidimensional //
//  * Os parametros  =>    $vetor[nome] ------------------------------- //
//  * As quantidades =>    $vetor[qtd] -------------------------------- //
//  Altura e largura da imagem gerada sao definidos dinamicamente de    //
//  acordo com o numero de parametros passados  ----------------------- //
// -------------------------------------------------------------------- //

// ----------------------------------------------------- //
//    Se houver qualquer duvida, basta enviar um e-mail  //
// para gui.mota@ig.com.br que terei prazer em responder //
// ----------------------------------------------------- //

// ------------------------------------------------------------------ //
// Copia totalmente permitida desde que mantenha os creditos do autor //
// ------------------------------------------------------------------ //

grafico_gui($vetor);

function grafico_gui($vetor){

//Define a quantidade de parametros
$tamanho = count($vetor[qtd]);

//define o maior parametro
$maior = 0;
$total = 0;
for($i=0;$i<$tamanho;$i++):
  if ($vetor[qtd][$i]>$maior):
    $maior=$vetor[qtd][$i];
	$vetor_maior=$i;
  endif;
  $total = $vetor[qtd][$i]-$total;
endfor;

//Calcula a altura e largura ideais
$largura= 50*$tamanho+100;
if ($largura<350)
  $largura=350;
$altura= 20*$tamanho+280;

//Cria a imagem
$img = imagecreate($largura,$altura);

//Define cores
$fundo = imagecolorallocate($img,230,239,248);
$vermelho = imagecolorallocate($img,255,0,0);
$branco = imagecolorallocate($img,255,255,255);
$corret = imagecolorallocate($img,51,153,255);
$cinza = imagecolorallocate($img,100,100,100);
$azul = imagecolorallocate($img,0,153,255);
$azulescuro = imagecolorallocate($img,102,153,204);
$preto = imagecolorallocate($img,0,0,0);

//Define o numero à esquerda
$numero_esquerda = ($maior/4);


//Define a altura certa para os retangulos
for($i=0;$i<$tamanho;$i++):
  $var[$i] = 180 - (($vetor[qtd][$i]*150)/$maior);
endfor;

//Gera as linhas intermediarias
imagefilledrectangle($img,80,30,$largura-60,31,$azulescuro);
imagefilledrectangle($img,80,68,$largura-60,69,$azulescuro);
imagefilledrectangle($img,80,104,$largura-60,105,$azulescuro);
imagefilledrectangle($img,80,142,$largura-60,143,$azulescuro);

//   Gera os triangulos pequenos que ligam os retangulos principais
// com os retangulos sombreados para formar uma imagem 3D.
$r=160;
$s=165;
for ($i=0;$i<$tamanho;$i++):

if ($vetor[qtd][$i]!=0):
$values_cima = array(
  0  => $r,    			// x1
  1  => $var[$i],    		// y1
  2  => $s,   			// x2
  3  => $var[$i]-5,	     	// y2
  4  => $r+5,    		// x3
  5  => $var[$i],   		// y3
);

$values_baixo = array(
  0  => $r+31,    		// x1
  1  => 181,    		// y1
  2  => $r+31,   		// x2
  3  => 176,    		// y2
  4  => $r+35,    		// x3
  5  => 176,   			// y3
);
$r=$r+50;
$s=$s+50;
imagefilledpolygon($img, $values_baixo, 3, $cinza );
imagefilledpolygon($img, $values_cima, 3, $cinza );
endif;
endfor;

//Gera os retangulos principais e sombreados
$x1=160;
$x2=190;
for ($i=0;$i<$tamanho;$i++):
  if ($vetor[qtd][$i]!=0):
    imagefilledrectangle($img,$x1+5,$var[$i]-5,$x2+5,176,$cinza);
	imagefilledrectangle($img,$x1,$var[$i],$x2,180,$corret);
  endif;
  $x1=$x1+50;
  $x2=$x2+50;
endfor;

//Gera as linhas principais
imagefilledrectangle($img,80,181,$largura-20,183,$preto);
imagefilledrectangle($img,78,00,80,180,$preto);

//Gera o numero do parametro
$v=172;
for($i=0;$i<$tamanho;$i++):
  imagestring($img, 2, $v, 185, $i+1, $preto);
  $v=$v+50;
endfor;

//Gera os numeros das linhas intermediarias
imagestring($img, 2, 05, 24, $numero_esquerda*4, $preto);
imagestring($img, 2, 05, 61, $numero_esquerda*3, $preto);
imagestring($img, 2, 05, 96, $numero_esquerda*2, $preto);
imagestring($img, 2, 05, 135, $numero_esquerda, $preto);

//Gera o nome dos parametros
$alt=225;
for($i=0;$i<$tamanho;$i++):
  imagestring($img, 3, 05, $alt, ($i+1)." - ", $corret);
  imagestring($img, 3, 25, $alt, " ".$vetor[nome][$i] , $preto);
  $alt=$alt+13;
endfor;
imagefilledrectangle($img,20,$alt+10,$largura-20,$alt+11,$cinza);

//Gera resumo embaixo da imagem
imagestring($img, 3, 05, $alt+16, "Diferença: ", $corret);
imagestring($img, 3, 80, $alt+16, $total, $preto);
//imagestring($img, 3, 05, $alt+29, "Análise:", $corret);
//imagestring($img, 3, 15, $alt+42, $vetor[nome][$vetor_maior], $preto);


//Numero de qtd de cada filme
$num=87;
for($i=0;$i<$tamanho;$i++):
  for($j=0;$j<=9;$j++):
    if ($vetor[qtd][$i]==$j):
      $vetor[qtd][$i]="0".$vetor[qtd][$i];
    endif;
  endfor;
  if ($vetor[qtd][$i]!=0):
    imagestring($img,3,$num,$var[$i]-18,$vetor[qtd][$i],$preto);
    else:
      imagestring($img,3,$num,168,$vetor[qtd][$i],$preto);
  endif;
  $num = $num+50;
endfor;




imagepng($img);
imagedestroy($img);


}

?>

