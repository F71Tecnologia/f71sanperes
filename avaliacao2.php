<?php

$id_projeto = $_REQUEST['tab'];
$id_bolsista = $_REQUEST['bol'];
$regiao = $_REQUEST['regiao'];
$tipo = $_REQUEST['tipo'];


include "conn.php";

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\">
<style type='text/css' media='print'> 
.noprint
{ 
   display: none; 
} 
</style>

<style type='text/css'>
<!--
.dragme{position:relative;}

h1 { page-break-after: always }

#apDiv1 {
	position:absolute;
	width:400px;
	height:44px;
	z-index:1;
	left: 9px;
	top: 16px;
}

-->
</STYLE>


<script type=\"text/javascript\">
<!-- This script and many more are available free online at -->
<!-- Created by: elouai.com -->
<!-- Início

var ie=document.all;
var nn6=document.getElementById&&!document.all;
var isdrag=false;
var x,y;
var dobj;

function movemouse(e)
{
  if (isdrag)
  {
    dobj.style.left = nn6 ? tx + e.clientX - x : tx + event.clientX - x;
    dobj.style.top  = nn6 ? ty + e.clientY - y : ty + event.clientY - y;
    return false;
  }
}

function selectmouse(e)
{
  var fobj       = nn6 ? e.target : event.srcElement;
  var topelement = nn6 ? \"HTML\" : \"BODY\";
  while (fobj.tagName != topelement && fobj.className != \"dragme\")
  {
    fobj = nn6 ? fobj.parentNode : fobj.parentElement;
  }

  if (fobj.className==\"dragme\")
  {
    isdrag = true;
    dobj = fobj;
    tx = parseInt(dobj.style.left+0);
    ty = parseInt(dobj.style.top+0);
    x = nn6 ? e.clientX : event.clientX;
    y = nn6 ? e.clientY : event.clientY;
    document.onmousemove=movemouse;
    return false;
  }

}

document.onmousedown=selectmouse;
document.onmouseup=new Function(\"isdrag=false\");
//  Fim -->

</script>


</head><body bgcolor='#D7E6D5'>";

if ($tipo == 'clt'){
$result_avaliado = mysql_query("SELECT * FROM rh_clt where id_psicologia = '1' and id_clt='$id_bolsista'");

}else{
$result_avaliado = mysql_query("SELECT * FROM autonomo where id_psicologia = '1' and id_autonomo='$id_bolsista'");
}

while($row_avaliado = mysql_fetch_array($result_avaliado)){

$psicologia = explode(",", $row_avaliado['psicologia']);

$radio1 = $psicologia[6];
$radio1 = $psicologia[5];
$radio2 = $psicologia[4];
$radio3 = $psicologia[3];
$radio4 = $psicologia[2];
$radio5 = $psicologia[1];
$radio6 = $psicologia[0];

$obs = $row_avaliado['obs'];

$total = $radio1+$radio2+$radio3+$radio4+$radio5+$radio6;

if ($total >= 6 && $total <= 11){

$msg_total = $total." O candidato apresenta resultados insuficientes";

} else if($total >= 12 && $total <= 17){

$msg_total = $total." Candidato com desempenho regular";

} else if($total >= 18 && $total <= 20){

$msg_total = $total." Candidato com desempenho bom";

} else if($total >= 21 && $total <= 24){

$msg_total = $total." Candidato com desempenho exelente";

}



include "include_avaliacao.php";

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '1'", $conn);
$row_pro = mysql_fetch_array($result_pro);

$result_curso = mysql_query("Select * from curso where id_curso = $row_avaliado[id_curso]", $conn);
$row_curso = mysql_fetch_array($result_curso);

$result2 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo 
WHERE id_psicologia = '1' and tipo_contratacao != '2' and id_projeto = '$id_projeto' ORDER BY nome") or die(mysql_error());

$data_hj = date('d/m/Y');

print "
<table width='100%' height='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
    <td align='center' valign='top'><table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
        <tr align='center' valign='top'>
          <td width='20' rowspan='2'> <div align='center'></div></td>
          <td align='left'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td align='center'><br>
                  <span class='style4'><img src='imagens/certificadosrecebidos.gif' width='120' height='86' align='middle'>
				  <strong>RESULTADOS DA AVALI&Ccedil;&Atilde;O DE DESEMPENHO INDIVIDUAL</strong></span></td>
              </tr>
            </table>
            <blockquote>
               <center><font face='arial' size='3' color='red'>&nbsp;Projeto: $row_pro[nome]</font></center><BR><BR>
               <font face='arial' size='2' color='red'>Nome:&nbsp; <strong>$row_avaliado[nome]  &nbsp;-&nbsp;RG: $row_avaliado[rg]</strong></font><br><br>
			    <font face='arial' size='2' color='red'> Unidade:&nbsp; <strong>$row_avaliado[locacao]</strong></font><br><br>
              
			  <font face='arial' size='2' color='red'>1. Compet&ecirc;ncia  Demonstrada</font><BR>
              <font face='arial' size='1' color='black'>$msg1</font>
              
			  <font face='arial' size='2' color='red'>             
			   <br><br> 2.  Iniciativa Para o Desenvolvimento Profissional</font><br>
               <font face='arial' size='1' color='black'>$msg2</font>
               
			   <font face='arial' size='2' color='red'>              
			    <br><br>3.  Potencial Para Promo&ccedil;&atilde;o</font><br>
               <font face='arial' size='1' color='black'>$msg3</font>
               
			   <font face='arial' size='2' color='red'>               
			    <br><br>4.  Resultados e Contribui&ccedil;&atilde;o&nbsp;</font><br>
               <font face='arial' size='1' color='black'>$msg4</font>
               
			   <font face='arial' size='2' color='red'>              
			    <br><br>5.  Solu&ccedil;&atilde;o de Problemas</font><br>
               <font face='arial' size='1' color='black'>$msg5</font>
               
			   <font face='arial' size='2' color='red'>              
			    <br><br>6.  Desenvolvimento Profissional &nbsp;                &nbsp;</font><br>
               <font face='arial' size='1' color='black'>$msg6</font><br><br><br>
              
			   <font face='arial' size='2' color='red'>OBSERVA&Ccedil;&Otilde;ES:</font><BR><BR>
               <font face='arial' size='2' color='red'><strong>$obs</strong></font><br><br>
			   <font face='arial' size='2' color='red'></font>
			   
              </p>
              <p class='style4'>&nbsp;</p>
			  
			  
			  
              <table width='80%' border='0' cellspacing='0' cellpadding='0' align='center'>
              	<tr>
              		<td width='50%' align='center'> <img src='imagens/assinaturafatima.gif' width='121' height='150'></td>
              		<td width='50%' align='center'>_______________________________________________<br>COORDENADOR RESPONSÁVEL</td>
              	</tr>
              </table>
              <br>
		 
		 
		 
              <hr align='center'>
              <div align='center'><strong>INSTITUTO  &ldquo;SORRINDO PARA A VIDA&rdquo; - C.G.C. 06.888.897/0001-18</strong><br>
                Av. S&atilde;o Luiz, 112 - 18&ordm;. andar - Cj 1802 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Rua da Assembleia,   10 - Cj 2617- Centro &nbsp;&nbsp;&nbsp;<BR>
                S&atilde;o Paulo - SP -   CEP 01046-000   - (11) 3255-6959&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;Rio de Janeiro - RJ - CEP 20011-901 - (21) 2252-8901<BR>
              </div>

              <div align='center'><BR>
                <BR>
                </p>
                </div>
            </blockquote>          </td>
          <td width='20' rowspan='2'>&nbsp;</td>
        </tr>

        <tr>
          <td bgcolor='#8FC2FC' class='igreja' height='12'>
            <div align='center'></div></td>
        </tr>
      </table>    </td>
  </tr>
</table>
<BR><BR>
<div id='apDiv1' class='dragme' >
<span id=teste class='noprint'>";
echo "<div style='cursor: move;'>Mover</div>";
echo "<a href='#' onClick=\"tabelassa.style.display=(tabelassa.style.display == 'none') ? '' : 'none' ;\">Esconder</a><br>";
echo "<table id='tabelassa' background='imagens/trans.png'>";
while($rowTab2 = mysql_fetch_array($result2)){
	echo "<tr><td>";
	echo "<a href='avaliacao2.php?bol=$rowTab2[0]&tab=$id_projeto&regiao=$regiao' style='text-decoration:none'>".$rowTab2['nome']."</a>";
	echo "</td></tr>";
}
echo "</table>";

print "</span></div>";



echo "<h1><!---Aqui a página é quebrada--> </h1>";

print "<br><center><br><a href='bolsista_class.php?id=2&projeto=$id_projeto&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a><center>";
}


/*
<table border='0' width='326' cellpadding='0' cellspacing='0' id='ttdiv' style='border: solid 1px #000; display:none'>
 <tr>
  <td><span style='font-size:13px' id='spantt'></span></td>
 </tr>
</table>
*/
?>