<?php



if(empty($_COOKIE['logado'])){



print "Efetue o Login<br><a href='login.php'>Logar</a> ";



}else{







include "conn.php";







$id_bol = $_REQUEST['bol'];



$id_bol3 = $_REQUEST['bol3'];



$id_bol2 = $_REQUEST['bol2'];



$tab = $_REQUEST['tab'];



$pro = $_REQUEST['pro'];



$id_reg = $_REQUEST['id_reg'];





$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida FROM $tab where id_bolsista = '$id_bol'", $conn);

$row = mysql_fetch_array($result_bol);





$result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = $row[id_curso]", $conn);

$row_bol3 = mysql_fetch_array($result_bol3);





$result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = $row[id_curso]", $conn);

$row_bol2 = mysql_fetch_array($result_bol2);





$result_reg = mysql_query("Select * from  regioes where id_regiao = $row[regiao]", $conn);

$row_reg = mysql_fetch_array($result_reg);





$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]", $conn);

$row_curso = mysql_fetch_array($result_curso);





$result_pro = mysql_query("Select * from  projeto where id_projeto = $pro", $conn);

$row_pro = mysql_fetch_array($result_pro);



$result_abol = mysql_query("Select * from  a$tab where id_bolsista = '$id_bol'", $conn);

$row_abol = mysql_fetch_array($result_abol);





$result_vale = mysql_query("Select * from vale where id_bolsista = '$id_bol'", $conn);

$row_vale = mysql_fetch_array($result_vale);



$result_banco = mysql_query("Select * from bancos where id_banco = $row[banco]", $conn);

$row_banco = mysql_fetch_array($result_banco);	



$result_depende = mysql_query ("SELECT *,date_format(data1, '%d/%m/%Y')as data1 ,date_format(data2, '%d/%m/%Y')as data2, date_format(data3, '%d/%m/%Y')as data3, date_format(data4, '%d/%m/%Y')as data4 ,date_format(data5, '%d/%m/%Y')as data5 FROM dependentes where id_bolsista = '$id_bol'", $conn);

$row_depende = mysql_fetch_array($result_depende);	



$dia = date('d');



$mes = date('n');



$ano = date('Y');



switch ($mes) {



case 1:



$mes = "Janeiro";



break;



case 2:



$mes = "Fevereiro";



break;



case 3:



$mes = "Março";



break;



case 4:



$mes = "Abril";



break;



case 5:



$mes = "Maio";



break;



case 6:



$mes = "Junho";



break;



case 7:



$mes = "Julho";



break;



case 8:



$mes = "Agosto";



break;



case 9:



$mes = "Setembro";



break;



case 10:



$mes = "Outubro";



break;



case 11:



$mes = "Novembro";



break;



case 12:



$mes = "Dezembro";



break;



}







?>



<html xmlns="undefined">



<head>



<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">



<title>CARTA DE REFER&Ecirc;NCIA</title>



<style>



<!--



p.MsoAcetate, li.MsoAcetate, div.MsoAcetate



	{font-size:8.0pt;



	font-family:"Tahoma","sans-serif";}



body {



	margin-left: 5px;



	margin-top: 0px;



	margin-right: 5px;



	margin-bottom: 0px;



}

.style9 {font-family: Arial, Helvetica, sans-serif}

.style12 {

	font-family: Arial, Helvetica, sans-serif;

	font-weight: bold;

	font-size: 10px;

}



-->



</style>



</head>



<body bgcolor="#FFFFFF" lang=PT-BR>



<table width="700" align="center">



  <tr>



    <td><table width="650" align="center">

      <tr>

        <td align="center" valign="middle"><div align="center"><span class="style9"><strong><strong><?  
		include 'empresa.php';
		$img = new empresa();
		$img -> imagem();
		?></strong> 
          <?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?>
        </strong></span></div></td>

        </tr>

      <tr>

        <td><p align="center" class="style9"><br>

          </p>

          <p align="center"><strong><br>

            <br>

            <br>

                <span class="style9">CARTA DE REFER&Ecirc;NCIA</span></strong></p>

          <p align="justify" class="style9"><br>

            <br>

              <br>

            Declaramos para os devidos fins, que o (a) Sr (a) <b><?php print "$row[nome]"; ?></b>,  

            portador(a) da CTPS / SERIE / EMISS&Atilde;O <b><?php print "$row[campo1]"; ?></b> / <b><?php print "$row[uf]"; ?></b>, foi nosso funcion&aacute;rio de <?php print "$row[data_entrada]"; ?> &agrave; <?php print "$row[data_saida]"; ?>, exercendo a  fun&ccedil;&atilde;o  

            de <b><?php print "$row_curso[nome]"; ?></b>, sendo que nada consta em  

            nossos arquivos que 

            desabone sua conduta profissional.</p>

          <p align="center" class="style9">&nbsp;</p>

          <p align="center" class="style9">&nbsp;</p>

          <p align="center" class="style9">Atenciosamente

            ,<br>

            <br>

            <br>

            </p>

          <p align="center" class="style9">&nbsp;</p>

          <p align="center" class="style9"><br>

          </p>

          <p align="center"><span class="style9"> _____________________________________________________<br>

              <strong><?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?></strong></span><br>

              <br>

              <br>

              <br>

            </p>

          <p align="center"><br>

            <br>

          </p></td>

        </tr>

      <tr>

        <td>
        <?php
$end = new empresa();
$end -> endereco('black','12px');
?>

          </p></td>

        </tr>

    </table>      </td>

  </tr>

</table>



</body>



</html>



<?php



}



?>