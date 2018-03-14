<?php
if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{

include "conn.php";

$id_bolsista = $_REQUEST['bol'];
$tabela = $_REQUEST['tab'];
$id_regiao = $_REQUEST['regiao'];
$tipo = $_REQUEST['tipo'];

if ($tipo == 'clt'){
	$result_bolsista = mysql_query("SELECT * FROM rh_clt where id_clt = $id_bolsista", $conn);
	$row_bolsista = mysql_fetch_array($result_bolsista);
	
}else{
		$result_bolsista = mysql_query("SELECT * FROM autonomo where id_autonomo = $id_bolsista", $conn);
		$row_bolsista = mysql_fetch_array($result_bolsista);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>AVALIA&Ccedil;&Atilde;O PSICOL&Oacute;GICA</title>

<style type="text/css">

<!--

.style2 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 12px;

	font-weight: bold;

}

.style6 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 12px;

}

.style11 {font-weight: bold}

.style13 {font-weight: bold}

.style23 {font-weight: bold}



.style24 {

	font-size: 10px;

	font-weight: bold;

	color: #003300;

}

.style26 {

	color: #FFFFFF;

	font-size: 10px;

}

.style27 {color: #FFFFFF; }

.style35 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF;}

.style38 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF; font-size: 12px; font-weight: bold; }

.style39 {color: #FFFFFF; font-size: 12px; font-family: Arial, Helvetica, sans-serif;}

.style40 {

	color: #FF0000

}

-->

</style>

<script>

<!--

function valida(){

       if (!document.form1.radio1[0].checked && !document.form1.radio1[1].checked && !document.form1.radio1[2].checked && !document.form1.radio1[3].checked) {

	   alert ("Escolha uma 1 - Competência Demonstrada");

	   return false;

	   }else{

         if (document.form1.radio1[0].checked) {

            valor1 = 4;

         }

         if (document.form1.radio1[1].checked) {

           valor1 = 3;

         }

         if (document.form1.radio1[2].checked) {

            valor1 = 2;

         }

         if (document.form1.radio1[3].checked) {

            valor1 = 1;

         }

      

	  

	  }





       if (!document.form1.radio2[0].checked && !document.form1.radio2[1].checked && !document.form1.radio2[2].checked && !document.form1.radio2[3].checked) {

	   alert ("Escolha uma 2 - Iniciativa Para o Desenvolvimento Profissional");

	   return false;

	   }else{

        if (document.form1.radio2[0].checked) {

            valor2 = 4;

         }

         if (document.form1.radio2[1].checked) {

           valor2 = 3;

         }

         if (document.form1.radio2[2].checked) {

            valor2 = 2;

         }

         if (document.form1.radio2[3].checked) {

            valor2 = 1;

         }



      }



       if (!document.form1.radio3[0].checked && !document.form1.radio3[1].checked && !document.form1.radio3[2].checked && !document.form1.radio3[3].checked) {

	   alert ("Escolha uma 3 - Potencial Para Promoção");

	   return false;

	   }else{

        if (document.form1.radio3[0].checked) {

            valor3 = 4;

         }

         if (document.form1.radio3[1].checked) {

           valor3 = 3;

         }

         if (document.form1.radio3[2].checked) {

            valor3 = 2;

         }

         if (document.form1.radio3[3].checked) {

            valor3 = 1;

         }



       }





       if (!document.form1.radio4[0].checked && !document.form1.radio4[1].checked && !document.form1.radio4[2].checked && !document.form1.radio4[3].checked) {

	   alert ("Escolha uma 4 - Resultados e Contribuição");

	   return false;

	   }else{

        if (document.form1.radio4[0].checked) {

            valor4 = 4;

         }

         if (document.form1.radio4[1].checked) {

           valor4 = 3;

         }

         if (document.form1.radio4[2].checked) {

            valor4 = 2;

         }

         if (document.form1.radio4[3].checked) {

            valor4 = 1;

         }



       }





       if (!document.form1.radio5[0].checked && !document.form1.radio5[1].checked && !document.form1.radio5[2].checked && !document.form1.radio5[3].checked) {

	   alert ("Escolha uma 5 - Solução de Problemas ");

	   return false;

	   }else{

        if (document.form1.radio5[0].checked) {

            valor5 = 4;

         }

         if (document.form1.radio5[1].checked) {

           valor5 = 3;

         }

         if (document.form1.radio5[2].checked) {

            valor5 = 2;

         }

         if (document.form1.radio5[3].checked) {

            valor5 = 1;

         }



       }





       if (!document.form1.radio6[0].checked && !document.form1.radio6[1].checked && !document.form1.radio6[2].checked && !document.form1.radio6[3].checked) {

	   alert ("Escolha uma 6 - Desenvolvimento Profissional");

	   return false;

	   }else{

        if (document.form1.radio6[0].checked) {

            valor6 = 4;

         }

         if (document.form1.radio6[1].checked) {

           valor6 = 3;

         }

         if (document.form1.radio6[2].checked) {

            valor6 = 2;

         }

         if (document.form1.radio6[3].checked) {

            valor6 = 1;

         }



       }



total=valor1+valor2+valor3+valor4+valor5+valor6;



if (total >= 6 && total <= 11){

msg = "Pontuação: "+total+" O candidato apresenta resultados insuficientes";

} else if(total >= 12 && total <= 17){

msg = "Pontuação: "+total+" Candidato com desempenho regular";

} else if(total >= 18 && total <= 20){

msg = "Pontuação: "+total+" Candidato com desempenho bom";

} else if(total >= 21 && total <= 24){

msg = "Pontuação: "+total+" Candidato com desempenho exelente";

}

document.getElementById("resultado2").innerText=msg;
}

//-->

</script>

<link href="net1.css" rel="stylesheet" type="text/css" />
</head>



<body leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" marginheight=\"0\">

<form action="cadastro2.php" method="post" class="style6" name='form1' onSubmit="return valida()">

  <table width="86%" align="center" cellspacing="5" bgcolor="#999999" class="bordaescura1px">

    <tr> 

      <td height="25" colspan="3" align="center" valign="middle"><div align="left"> 

          <p class="style2">AVALIA&Ccedil;&Atilde;O DE DESEMPENHO<br />

            <span class="style26">SELECIONE OS CRIT&Eacute;RIOS PARA O CANDIDATO 

            SELECIONADO</span></p>

          <div align="center"><font color="#FFFFFF" size="+2"><b><?php print "$row_bolsista[nome] - $row_bolsista[locacao]";?></b></font> 

          </div>

        </div>

        <div align="center"><br>

        </div></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style38">&nbsp;</td>

      <td width="69%" bgcolor="#99CC99" class="style35">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="2" valign="top" class="style11"> <p class="style38"> 1. Compet&ecirc;ncia 

          Demonstrada&nbsp;</p></td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="590">

          <tr> 

            <td width="2%" valign="top" class="style35"> <p align="center"> 

                <input type="radio" name="radio1" value="4" id="radio1[0]"/>

                4 </p></td>

            <td width="98%" valign="top" class="style35"><p>Os conhecimentos, 

                habilidades e compet&ecirc;ncia demonstrados pelo funcion&aacute;rio 

                no per&iacute;odo foram reconhecidos por pelo menos um Diretor, 

                Gerente ou Supervisor de outra &aacute;rea. O reconhecimento pode 

                ter vindo na forma de elogio, coment&aacute;rio sobre um trabalho 

                realizado, solicita&ccedil;&atilde;o para que o funcion&aacute;rio 

                fizesse determinada tarefa ou qualquer fato que indicasse o <u>reconhecimento 

                da compet&ecirc;ncia do avaliado por pessoas de fora do seu departamento</u>. 

              </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="590">

          <tr> 

            <td width="2%" valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio1" value="3" id="radio1[1]"/>

                3 </p></td>

            <td width="98%" valign="top" class="style35"><p>Os conhecimentos, 

                habilidades e compet&ecirc;ncia demonstrados pelo funcion&aacute;rio 

                no per&iacute;odo foram reconhecidos pelos colegas da sua equipe.&nbsp; 

                O reconhecimento pode ter vindo na forma de elogio ou coment&aacute;rios 

                sobre a qualidade do servi&ccedil;o ou das tarefas realizadas 

                pelo funcion&aacute;rio. O <u>reconhecimento da compet&ecirc;ncia 

                do funcion&aacute;rio ficou restrito ao seu departamento</u>. 

              </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="590">

          <tr> 

            <td width="2%" valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio1" value="2" id="radio1[2]"/>

                2 </p></td>

            <td width="98%" valign="top" class="style35"><p>S&oacute; voc&ecirc; 

                como superior imediato teve condi&ccedil;&otilde;es de avaliar 

                os conhecimentos, habilidades e compet&ecirc;ncia demonstrados 

                pelo funcion&aacute;rio no per&iacute;odo. O funcion&aacute;rio 

                ainda n&atilde;o teve oportunidade de realizar tarefas que demonstrassem 

                para outras pessoas a sua compet&ecirc;ncia. O <u>reconhecimento 

                da compet&ecirc;ncia do funcion&aacute;rio ficou restrito a seu 

                superior imediato</u>. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="590">

          <tr> 

            <td width="2%" valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio1" value="1" id="radio1[3]"/>

                1 </p></td>

            <td width="98%" valign="top" class="style35"><p>O funcion&aacute;rio 

                demonstrou compet&ecirc;ncia apenas para cumprir sua rotina, dentro 

                do que era esperado. O funcion&aacute;rio n&atilde;o realizou 

                tarefas ou <u>n&atilde;o teve a iniciativa de a&ccedil;&otilde;es 

                que demonstrassem claramente sua compet&ecirc;ncia</u>. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style38">&nbsp;</td>

      <td bgcolor="#99CC99" class="style35">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="2" valign="top"><span class="style38"><em>2. Iniciativa Para 

        o Desenvolvimento Profissional&nbsp;</em></span></td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio2" value="4" id="radio2[0]"/>

                4 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio esteve <u>sempre</u> 

                procurando adquirir novos conhecimentos e habilidades e se desenvolver 

                profissionalmente. Isso ficou comprovado pelos coment&aacute;rios 

                fundamentados do funcion&aacute;rio sobre leituras e pesquisas 

                que realizou no per&iacute;odo, pelos cursos que realizou por 

                iniciativa pr&oacute;pria, pelo aproveitamento e assimila&ccedil;&atilde;o 

                de conte&uacute;dos de treinamentos realizados ou patrocinados 

                pela empresa. Pode-se dizer que o funcion&aacute;rio <u>cuida 

                do seu pr&oacute;prio desenvolvimento profissional</u>, independente 

                do apoio da empresa. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio2" value="3" id="radio2[1]"/>

                3 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>&agrave;s 

                vezes</u> tomou a iniciativa para melhorar sua compet&ecirc;ncia 

                profissional. Isso ficou comprovado pelo grande interesse demonstrado 

                pelo funcion&aacute;rio em determinado assunto relacionado com 

                seu trabalho ou sua evolu&ccedil;&atilde;o profissional. Pode-se 

                dizer que o funcion&aacute;rio <u>responde bem &agrave;s oportunidades 

                que a empresa oferece</u> ou sugest&otilde;es de outras pessoas 

                sobre o seu desenvolvimento profissional. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio2" value="2" id="radio2[2]"/>

                2 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>s&oacute;</u> 

                tomou a iniciativa de aprender coisas novas <u>quando</u> apareceu 

                uma oportunidade &uacute;nica ou <u>recebeu alguma sugest&atilde;o 

                do superior ou outras pessoas</u> para aproveitar determinada 

                oportunidade de fazer um curso fora, participar de um treinamento 

                interno, ler um livro ou aprender novas tarefas. O funcion&aacute;rio 

                responde bem &agrave;s oportunidades de desenvolvimento profissional 

                apenas quando recebe um leve &ldquo;empurr&atilde;o&rdquo; do 

                superior ou outras pessoas. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio2" value="1" id="radio2[3]"/>

                1 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>s&oacute;</u> 

                tomou a iniciativa para adquirir novos conhecimentos ou habilidades 

                <u>quando</u> fortemente <u>incentivado, ou direta ou indiretamente 

                pressionado pelo superior ou outras pessoas</u> para realizar 

                algum curso ou aprender novas tarefas. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style38">&nbsp;</td>

      <td bgcolor="#99CC99" class="style35">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="2" valign="top"><span class="style38"><em> 3. Potencial Para 

        Promo&ccedil;&atilde;o&nbsp;</em></span></td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio3" value="4" />

                4 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>est&aacute; 

                plenamente capacitado</u> a ser promovido para um cargo maior. 

                Isso foi comprovado pelo n&uacute;mero de vezes que o funcion&aacute;rio 

                realizou tarefas que exigiam conhecimentos e habilidades acima 

                daquelas exigidas pelo seu cargo. Por exemplo, ele pode ter realizado 

                tarefas importantes, de responsabilidade do seu superior imediato 

                com o mesmo grau de compet&ecirc;ncia.&nbsp; Al&eacute;m dos aspectos 

                t&eacute;cnicos, o funcion&aacute;rio tem perfil para o cargo 

                do seu superior imediato e &eacute; reconhecido como o sucessor 

                natural deste. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio3" value="3" />

                3 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>precisar&aacute; 

                de pouco treinamento ou orienta&ccedil;&atilde;o</u> para ser 

                promovido. No per&iacute;odo ele realizou algumas das tarefas 

                de responsabilidade de seu superior com grau de compet&ecirc;ncia 

                satisfat&oacute;rio, ou realizou tarefas que exigiam conhecimentos 

                acima dos exigidos para o seu cargo.&nbsp; O funcion&aacute;rio 

                &eacute; reconhecido como tendo potencial para um dia suceder 

                seu superior imediato ou ocupar um cargo maior em outra &aacute;rea. 

              </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio3" value="2" />

                2 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>depender&aacute; 

                de treinamento espec&iacute;fico</u> para ser promovido. O funcion&aacute;rio 

                ainda n&atilde;o demonstrou estar preparado para tarefas que exijam 

                conhecimentos superiores aos exigidos pelo seu cargo.&nbsp; O 

                funcion&aacute;rio ainda n&atilde;o demonstrou que tem o estilo 

                e o potencial necess&aacute;rio para uma promo&ccedil;&atilde;o 

                para um cargo maior. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio3" value="1" />

                1 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>precisar&aacute;</u>, 

                al&eacute;m do treinamento espec&iacute;fico, <u>de uma prepara&ccedil;&atilde;o 

                especial</u> ou <u>amadurecimento profissional</u> para ser promovido.&nbsp; 

                No momento, n&atilde;o existem elementos que garantam que o funcion&aacute;rio 

                tem potencial para ser promovido para um cargo maior. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style38">&nbsp;</td>

      <td bgcolor="#99CC99" class="style35">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="2" valign="top"><span class="style38"><em> 4. Resultados e 

        Contribui&ccedil;&atilde;o&nbsp;</em></span></td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio4" value="4" />

                4 </p></td>

            <td valign="top" class="style35"><p>Para conseguir os resultados esperados, 

                o funcion&aacute;rio <u>dependeu essencialmente do seu pr&oacute;prio 

                esfor&ccedil;o e compet&ecirc;ncia</u>. A participa&ccedil;&atilde;o 

                de outras pessoas foi apenas subsidi&aacute;ria, dentro dos padr&otilde;es 

                normais do trabalho em equipe. O funcion&aacute;rio foi facilmente 

                reconhecido como uma das pessoas que <u>fizeram a diferen&ccedil;a</u> 

                no grupo. Foi um dos principais destaques na equipe. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio4" value="3" />

                3 </p></td>

            <td valign="top" class="style35"><p>Para conseguir os resultados esperados, 

                o funcion&aacute;rio em alguns momentos <u>dependeu de uma aten&ccedil;&atilde;o 

                especial de outras pessoas</u> para ajud&aacute;-lo a completar 

                seu trabalho. Foi poss&iacute;vel reconhecer o m&eacute;rito da 

                sua participa&ccedil;&atilde;o no resultado do trabalho, mesmo 

                no contexto de um trabalho de equipe.&nbsp; Em v&aacute;rios momentos 

                sua atua&ccedil;&atilde;o foi destacada. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio4" value="2" />

                2 </p></td>

            <td valign="top" class="style35"><p>Para conseguir os resultados esperados, 

                o funcion&aacute;rio <u>dependeu de significativa participa&ccedil;&atilde;o 

                da equipe</u> como um todo.&nbsp; O m&eacute;rito de sua participa&ccedil;&atilde;o 

                n&atilde;o ficou evidente dentro da equipe.&nbsp; Mesmo sem se 

                destacar, foi importante como membro da equipe. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio4" value="1" />

                1 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>n&atilde;o 

                conseguiu apresentar os resultados esperados</u>, mesmo tendo 

                recebido ajuda dos demais membros da equipe.&nbsp; Ou s&oacute; 

                conseguiu apresentar os resultados esperados com a ajuda de outras 

                pessoas da equipe e <u>constante orienta&ccedil;&atilde;o superior</u>. 

              </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style38">&nbsp;</td>

      <td bgcolor="#99CC99" class="style35">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="2" valign="top"><span class="style38"><em> 5. Solu&ccedil;&atilde;o 

        de Problemas&nbsp;</em></span></td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio5" value="4" />

                4 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio conseguiu 

                resolver os problemas mais complexos que surgiram, cuja solu&ccedil;&atilde;o 

                exigiu um grau de racioc&iacute;nio l&oacute;gico ou abstrato 

                claramente mais alto do que aquele exigido pelo seu cargo. O racioc&iacute;nio 

                exercido envolveu alguma criatividade ou uma abordagem original 

                para encontrar a melhor solu&ccedil;&atilde;o para esses problemas. 

                O funcion&aacute;rio <u>soube resolver os problemas mais complexos</u> 

                que surgiram, <u>praticamente sem orienta&ccedil;&atilde;o superior</u>. 

              </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio5" value="3" />

                3 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>conseguiu 

                resolver os problemas mais complexos</u> que surgiram, descritos 

                no item anterior, <u>com alguma ajuda e orienta&ccedil;&atilde;o 

                do seu superior imediato</u>.&nbsp; O funcion&aacute;rio conseguiu 

                demonstrar habilidades para analisar e encontrar a solu&ccedil;&atilde;o 

                para alguns dos problemas mais complexos, por&eacute;m a participa&ccedil;&atilde;o 

                do seu superior foi decisiva na escolha das alternativas de solu&ccedil;&atilde;o 

                analisadas. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio5" value="2" />

                2 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>conseguiu 

                resolver os problemas de rotina</u>, contando com o <u>apoio do 

                superior quando necess&aacute;rio</u>. Em algumas situa&ccedil;&otilde;es, 

                o funcion&aacute;rio precisou fazer escolhas entre alternativas 

                de solu&ccedil;&atilde;o j&aacute; conhecidas, mas sem necessidade 

                de uma reflex&atilde;o mais profunda. As habilidades de racioc&iacute;nio 

                demonstradas pelo funcion&aacute;rio s&atilde;o compat&iacute;veis 

                com os requisitos do seu cargo, ou seja, s&atilde;o habilidades 

                que constam do perfil do seu cargo. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style38">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio5" value="1" />

                1 </p></td>

            <td valign="top" class="style35"><p>O funcion&aacute;rio <u>soube 

                resolver apenas os problemas de rotina</u>, com precedentes bem 

                conhecidos e contando com o <u>apoio constante do superior imediato</u>. 

                As habilidades demonstradas pelo funcion&aacute;rio n&atilde;o 

                foram suficientes para solucionar os problemas que sa&iacute;ram 

                da estrita rotina do seu cargo. As habilidades de an&aacute;lise 

                e julgamento demonstradas pelo funcion&aacute;rio foram apenas 

                para fazer escolhas &oacute;bvias, em situa&ccedil;&otilde;es 

                com precedentes bem conhecidos. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style38">&nbsp;</td>

      <td bgcolor="#99CC99" class="style35">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="2" valign="top"><span class="style38"><em> 6. Desenvolvimento 

        Profissional &nbsp;</em></span></td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio6" value="4"  />

                4 </p></td>

            <td valign="top" class="style35"><p><u>Foram decisivos</u> para melhorar 

                a sua compet&ecirc;ncia e desempenho.&nbsp; Os novos conhecimentos 

                e habilidades <u>tiveram reflexos positivos vis&iacute;veis na 

                qualidade ou resultados do trabalho</u> do funcion&aacute;rio.&nbsp; 

                Os resultados do trabalho do funcion&aacute;rio foram claramente 

                superiores &agrave;queles apresentados antes da aquisi&ccedil;&atilde;o 

                dos novos conhecimentos e habilidades. A diferen&ccedil;a de &ldquo;antes&rdquo; 

                e &ldquo;depois&rdquo;, em termos de desempenho do funcion&aacute;rio, 

                s&atilde;o percebidas mesmo numa an&aacute;lise superficial. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style39">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio6" value="3" />

                3 </p></td>

            <td valign="top" class="style35"><p><u>Contribu&iacute;ram</u> para 

                melhorar a compet&ecirc;ncia e desempenho do funcion&aacute;rio.&nbsp; 

                <u>Tiveram alguns reflexos positivos na qualidade ou resultados 

                do trabalho</u> do funcion&aacute;rio.&nbsp; Os resultados do 

                trabalho do funcion&aacute;rio foram ligeiramente superiores &agrave;queles 

                apresentados antes da aquisi&ccedil;&atilde;o dos novos conhecimentos 

                e habilidades.&nbsp; Para notar a melhora no desempenho do funcion&aacute;rio, 

                &eacute; necess&aacute;ria uma an&aacute;lise mais atenta e detalhada 

                do trabalho realizado no per&iacute;odo.&nbsp; Geralmente s&oacute; 

                o superior do funcion&aacute;rio conseguiria notar essa melhora 

                de desempenho. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style35">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td height="47" valign="top" class="style35"><p align="center"> 

                <input type="radio" name="radio6" value="2" />

                2 </p></td>

            <td valign="top" class="style35"><p><u>Contribu&iacute;ram</u> para 

                melhorar a compet&ecirc;ncia do funcion&aacute;rio, <u>por&eacute;m 

                n&atilde;o tiveram reflexos significativos na qualidade ou resultados 

                do trabalho</u> do funcion&aacute;rio.&nbsp; O desempenho do funcion&aacute;rio 

                ficou dentro dos padr&otilde;es j&aacute; apresentados por ele 

                anteriormente. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" class="style35">&nbsp;</td>

      <td class="style13"><table border="1" cellspacing="0" cellpadding="0" width="0">

          <tr> 

            <td valign="top" class="style35"><p align="center"> 

                <input name="radio6" type="radio" value="1" />

                1 </p></td>

            <td valign="top" class="style35"><p><u>Praticamente n&atilde;o contribu&iacute;ram</u> 

                para a qualidade ou resultados do trabalho do funcion&aacute;rio 

                ou para melhorar a compet&ecirc;ncia e desempenho do funcion&aacute;rio. 

                O desempenho do funcion&aacute;rio ficou abaixo do esperado, ou 

                foi minimamente aceit&aacute;vel. </p></td>

          </tr>

        </table></td>

    </tr>

    <tr> 

      <td colspan="2" bgcolor="#99CC99" class="style11">&nbsp;</td>

      <td bgcolor="#99CC99" class="style13">&nbsp;</td>

    </tr>

    <tr> 

      <td colspan="3" valign="top" class="style23"><center><input type="button" value="Calcular Pontuação Final" onclick="valida()"><br><br> 

    <span id="resultado2" class="style27">Pontuação:</span></center><br></td>

    </tr>

    <tr> 

      <td width="18%" valign="top" class="style23"><div align="right" class="style27">OBSERVA&Ccedil;&Otilde;ES:</div></td>

      <td colspan="2"><strong> 

        <textarea name="descricao" cols="51" rows="5"></textarea>

        </strong></td>

    </tr>

    <tr> 

      <td valign="top"><strong></strong></td>

      <td colspan="2">     
      <input type="hidden" name="id_cadastro" value="10">
      
      <input type="hidden" name="id_regiao" value="<?php echo $id_regiao; ?>">
      <input type="hidden" name="id_projeto" value="<?php echo $tabela; ?>"> 
      <input type="hidden" name="bolsista" value="<?php echo $id_bolsista; ?>">
      <input type="hidden" name="tipo" value="<?php echo $tipo; ?>"> 

      </td>

    </tr>

    <tr> 

      <td colspan="3" valign="top" bgcolor="#99CC99"><div align="center" class="style24">OBS: 

          verifique todas as informa&ccedil;&otilde;es antes de postar a mensagem, 

          caso corretas clique em enviar </div></td>

    </tr>

  </table>

<div align="center"><br>

    <input type="submit" name="salvar" value="GERAR AVALIAÇÃO"/>

     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

     <input type="reset" name="limpar" value="REFAZER TUDO"/>

	 <br><br><br>

	 <a href='<?php print "bolsista_class.php?id=2&projeto=$tabela&regiao=$id_regiao";?>'><img src='imagens/voltar.gif' border=0></a>

</div>

</form>

</body>

</html>

<?php



}

?>