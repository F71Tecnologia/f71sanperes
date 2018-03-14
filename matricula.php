<?php

if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "conn.php";



$id_bolsista = $_REQUEST['bol'];

$id_projeto = $_REQUEST['pro'];

$id_regiao = $_REQUEST['id_reg'];

$tabela = $_REQUEST['tab'];



$result_bol = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data_nasci2, date_format(data_entrada, '%d/%m/%Y')as data_entrada2 FROM $tabela where id_bolsista = '$id_bolsista'", $conn);

$row_bol = mysql_fetch_array($result_bol);



$result_tv = mysql_query("SELECT * FROM tvsorrindo where id_bolsista = '$id_bolsista' and id_projeto = '$id_projeto'", $conn);

$row_tv = mysql_fetch_array($result_tv);



$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'", $conn);

$row_pro = mysql_fetch_array($result_pro);



$id_curso = $row_bol['id_curso'];

$result_curso = mysql_query("SELECT *, date_format(inicio, '%d/%m/%Y')as inicio2, date_format(termino, '%d/%m/%Y')as termino2 FROM curso where id_curso = '$id_curso'", $conn);

$row_curso = mysql_fetch_array($result_curso);



$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'", $conn);

$row_reg = mysql_fetch_array($result_reg);





$data_hj = date('d/m/Y');



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

<html>

<head>

<title>:: Intranet ::</title>

<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>

<link href='../net.css' rel='stylesheet' type='text/css'>



<style type='text/css'>

<!--

.style4 {font-family: Arial, Helvetica, sans-serif}

.style5 {color: #FF0000}

-->

</style>

</head>



<body bgcolor='#FFFFFF' leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>

<table width='100%' height='100%' border='0' cellpadding='0' cellspacing='0'>

  <tr>

    <td align='center' valign='top'><table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>

        <tr align='center' valign='top'> 

          <td width='20' rowspan='2'> <div align='center'></div></td>

          <td align='left'> 

            <table width='100%' border='0' cellspacing='0' cellpadding='0'>

              <tr> 

                <td>

				<table width="100%" border="0" cellpadding="0" cellspacing="0">

                    <tr>

                      <td width="19%" align="left" valign="middle">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?>
                      <!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' />--></td>

                      <td width="62%" align="center" valign="middle"><font size="4">CONTRATO 

                        DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS EDUCACIONAIS 

                        </font> 

                        <p><font size="3">Matr&uacute;cula de N&ordm;. <font color="#FF0000"><b><?php print "$row_tv[matricula]"; ?></b></font><br>

                          </font> </p></td>

                      <td width="19%" align="right" valign="middle"><img src='imagens/cobrinha.gif' width='99' height='123'></td>

                    </tr>

				</table>&nbsp;</td>

              </tr>

            </table>          

            <blockquote> 

              <p align='justify' class='style13'>Pelo presente instrumento 

                particular o SISTEMA OBJETIVO DE ENSINO LTDA &#8211; S.O.E., pessoa 

                jur&iacute;dica de direito privado,inscrita no CNPJ/MF sob o n&ordm; 

                03.635.819/0001-13, com sede a Rua Barcelos Domingos, n&ordm;174, 

                grupo - 201 - Campo Grande - Rio de Janeiro - RJ, entidade mantenedora 

                do INSTITUTO LATINO DE CI&Ecirc;NCIA E TECNOLOGIA, neste ato representado 

                por seu Presidente, Engo. Stephane Janosch, doravante denominada 

                apenas CONTRATADA, e o <font color="#FF0000"><b><?php print "$row_bol[nome]"; ?></b></font>, 

                portador da carteira de identidade n&ordm; <font color="#FF0000"><b><?php print "$row_bol[rg]"; ?></b></font> 

                - <font color="#FF0000"><b><?php print "$row_bol[orgao]"; ?></b></font>, 

                portador do CPF <font color="#FF0000"><b><?php print "$row_bol[cpf]"; ?></b></font> 

                estudante do Curso de <font color="#FF0000"><b><?php print "$row_curso[nome]"; ?></b></font>, 

                a ser iniciado em <font color="#FF0000"><b><?php print "$row_curso[inicio2]"; ?></b></font>, 

                com seu t&eacute;rmino em <font color="#FF0000"><b><?php print "$row_curso[termino2]"; ?></b></font>, 

                parcelado em <font color="#FF0000"><b><?php print "$row_curso[parcelas]"; ?></b></font> 

                vezes com seu custo total de <font color="#FF0000"><b>R$ 

				<?php

								

				$valor_total = $row_curso['parcelas'] * $row_curso['salario'] ;

				print "$valor_total"; 

				

				?></b></font><span class="style5">,00</span>                ao final qualificado, doravante denominado CONTRATANTE, t&ecirc;m 

                justo e contratado o seguinte.

				<br>

				<br>CL&Aacute;USULA PRIMEIRA - As 

                partes acima identificadas celebram o presente contrato de Presta&ccedil;&atilde;o 

                de Servi&ccedil;os Educacionais sob a &eacute;gide do disposto 

                na legisla&ccedil;&atilde;o aplic&aacute;vel e mediante as cl&aacute;usulas 

                e condi&ccedil;&otilde;es a seguir especificadas a cujo cumprimento 

                se obrigam mutuamente. 

				<br><br>CL&Aacute;USULA SEGUNDA - O objeto 

                deste contrato &eacute; a presta&ccedil;&atilde;o de servi&ccedil;os 

                educacionais pela CONTRATADA, ao aluno indicado como<br>

                CONTRATANTE.<br>

                Par&aacute;grafo Primeiro: Para tanto, a CONTRATADA declara-se 

                como estabelecimento particular de ensino, com personalidade jur&iacute;dica, 

                o qual tem como preocupa&ccedil;&atilde;o b&aacute;sica oferecer 

                &agrave; comunidade, forma&ccedil;&atilde;o e orienta&ccedil;&atilde;o 

                profissional de alta qualidade, contando para isso com experiente 

                quadro de profissionais, instala&ccedil;&otilde;es adequadas e 

                moderna tecnologia educacional. O presente contrato &eacute; celebrado 

                sob a &eacute;gide dos artigos 206 e 242 da Constitui&ccedil;&atilde;o 

                Federal, pelo disposto na Lei n&ordm;9.870/99, C&oacute;digo Civil 

                Brasileiro, Lei n&ordm; 8.078/90, bem como todo e qualquer fundamento 

                legal que possa reger a mat&eacute;ria.<br>

                Par&aacute;grafo Segundo: A matr&iacute;cula para todos os cursos 

                oferecidos pela CONTRATADA ser&aacute; realizada em data prevista 

                no calend&aacute;rio acad&ecirc;mico, previamente divulgado pela 

                CONTRATADA.<br>

                Par&aacute;grafo Terceiro: A configura&ccedil;&atilde;o formal 

                do ato de matr&iacute;cula se concretiza pelo preenchimento e 

                assinatura do formul&aacute;rio pr&oacute;prio fornecido pela 

                CONTRATADA denominado &#8220;Formul&aacute;rio de Matr&iacute;cula&#8221;, 

                al&eacute;m da apresenta&ccedil;&atilde;o de todos os documentos 

                necess&aacute;rios solicitados, os quais passar&atilde;o a integrar 

                este contrato.<br>

                Par&aacute;grafo Quarto: A matr&iacute;cula s&oacute; estar&aacute; 

                assegurada ap&oacute;s a certifica&ccedil;&atilde;o por parte 

                da tesouraria da CONTRATADA, de que o CONTRATANTE quitou suas 

                obriga&ccedil;&otilde;es financeiras, sejam as previstas para 

                o ato da matr&iacute;cula, sejam as decorrentes de presta&ccedil;&otilde;es 

                de per&iacute;odos anteriores vencidas e n&atilde;o pagas, ficando 

                garantida &agrave; CONTRATADA a possibilidade de negar confirma&ccedil;&atilde;o 

                &agrave; matr&iacute;cula caso a quita&ccedil;&atilde;o n&atilde;o 

                se concretize por completo, como na hip&oacute;tese de emiss&atilde;o 

                de cheques sem provis&atilde;o de fundos ou qualquer outra.<br>

                Par&aacute;grafo Quinto: Ocorrendo cancelamento de matr&iacute;cula 

                por solicita&ccedil;&atilde;o do aluno, seja ele classificado 

                em Processo Seletivo ou matriculado diretamente, a CONTRATADA 

                se reserva o direito de devolver ao mesmo, 50% (cinq&uuml;enta) 

                por cento do valor pago no ato da matr&iacute;cula, para custear 

                as despesa inerentes ao processo de matr&iacute;cula.<br>

                Par&aacute;grafo Sexto: Reserva-se a CONTRATADA ao direito de, 

                no prazo de at&eacute; 15 (quinze) dias antes do in&iacute;cio 

                de cada per&iacute;odo letivo, cancelar qualquer turma cujo n&uacute;mero 

                de alunos n&atilde;o for suficiente para garantir o equil&iacute;brio 

                econ&ocirc;mico-financeiro e conseq&uuml;ente capacidade de cumprimento 

                das obriga&ccedil;&otilde;es engajadas, ficando assegurado ao 

                aluno afastado por tal hip&oacute;tese o direito de ocupar uma 

                vaga em outra turma do mesmo per&iacute;odo e curso, em qualquer 

                turno, desde que existentes.

				<br><br>CL&Aacute;USULA TERCEIRA - As 

                aulas poder&atilde;o ministradas em salas de aula ou local que 

                a CONTRATADA indicar, tendo em vista a natureza<br>

                dos conte&uacute;dos e as t&eacute;cnicas pedag&oacute;gicas que 

                se fizerem necess&aacute;rias.

				<br><br>CL&Aacute;USULA QUARTA - &Eacute; 

                de inteira responsabilidade da CONTRATADA a orienta&ccedil;&atilde;o 

                t&eacute;cnica sobre a presta&ccedil;&atilde;o dos servi&ccedil;os 

                educacionais<br>

                no que se refere &agrave; marca&ccedil;&atilde;o de datas para 

                as provas de aproveitamento, fixa&ccedil;&atilde;o de carga hor&aacute;ria, 

                indica&ccedil;&atilde;o de professores, orienta&ccedil;&atilde;o<br>

                pedag&oacute;gica, al&eacute;m de outras provid&ecirc;ncias que 

                as atividades docentes exigem, obedecendo ao seu exclusivo crit&eacute;rio, 

                sem inger&ecirc;ncia do<br>

                CONTRATANTE.

				<br><br>CL&Aacute;USULA QUINTA - Como 

                contrapresta&ccedil;&atilde;o pelos servi&ccedil;os educacionais 

                a serem prestados por for&ccedil;a deste contrato o CONTRATANTE<br>

                declara-se ciente dos valores fixados neste contrato, com sua 

                forma de pagamento detalhada no formul&aacute;rio de matr&iacute;cula, 

                quantificados com base nos planejamentos pedag&oacute;gicos econ&ocirc;mico-financeiros 

                da CONTRATADA, o qual procedeu com a obrigat&oacute;ria compatibiliza&ccedil;&atilde;o 

                dos pre&ccedil;os com os custos. Na superveni&ecirc;ncia de altera&ccedil;&atilde;o 

                legislativa que implique comprovado aumento de custos ou redu&ccedil;&atilde;o 

                de receitas da CONTRATADA, os valores das parcelas da semestralidade 

                poder&atilde;o ser revistos, de modo a manter o equil&iacute;brio 

                econ&ocirc;mico-financeiro do contrato.

				<br><br>CL&Aacute;USULA SEXTA - O pagamento 

                das obriga&ccedil;&otilde;es financeiras comprovar-se-&aacute; 

                mediante a apresenta&ccedil;&atilde;o de recibo que individualize 

                a<br>

                obriga&ccedil;&atilde;o paga.

				<br><br>CL&Aacute;USULA S&Eacute;TIMA 

                - O CONTRATANTE reconhece e aceita as condi&ccedil;&otilde;es 

                de pagamento das mensalidades estabelecidas neste<br>

                contrato para o curso escolhido, conforme tabela de valores e 

                vencimentos constantes no Formul&aacute;rio de Matr&iacute;cula, 

                anexa a este documento, da qual declara haver tomado conhecimento 

                pr&eacute;vio, obrigando-se &agrave; pontual quita&ccedil;&atilde;o 

                das parcelas ali discriminadas, nos locais indicados pela<br>

                CONTRATADA.<br>

                Par&aacute;grafo Primeiro: Para os pagamentos efetuados pontualmente 

                at&eacute; o dia 10 (dez) de cada m&ecirc;s, poder&aacute; ser 

                concedido desconto de at&eacute; 10% (dez) por cento do valor 

                da parcela paga, em ato de liberalidade da CONTRATADA, o qual 

                poder&aacute; ser suspenso a qualquer tempo, ficando certo que 

                ser&aacute; automaticamente desconsiderado, independentemente 

                de pr&eacute;vio aviso, no caso de atraso do pagamento.<br>

                Par&aacute;grafo Segundo: O pagamento efetuado ap&oacute;s a data 

                do vencimento, al&eacute;m de ser exigido em sua integralidade, 

                sem o eventual desconto previsto no par&aacute;grafo anterior, 

                ser&aacute; acrescido de multa de 2% (dois) por cento sobre o 

                valor da presta&ccedil;&atilde;o em atraso, sem preju&iacute;zo 

                da atualiza&ccedil;&atilde;o monet&aacute;ria de acordo com o 

                IGPM, al&eacute;m de juros de 2% (dois) por cento ao m&ecirc;s.<br>

                Par&aacute;grafo Terceiro: O n&atilde;o comparecimento do CONTRATANTE 

                aos atos escolares ora contratados n&atilde;o o exime do pagamento 

                das mensalidades, tendo em vista a disponibilidade do servi&ccedil;o 

                e garantia de vaga que lhe s&atilde;o fornecidas por ocasi&atilde;o 

                da contrata&ccedil;&atilde;o.<br>

                Par&aacute;grafo Quarto: Em caso de inadimplemento a mora decorrer&aacute; 

                ex-re por serem as obriga&ccedil;&otilde;es positivas e l&iacute;quidas 

                na data dos seus vencimentos, permitindo que a CONTRATADA possa 

                cobrar o d&eacute;bito vencido e encargos atrav&eacute;s da respectiva 

                execu&ccedil;&atilde;o, valendo este contrato como t&iacute;tulo 

                de cr&eacute;dito extrajudicial tipificado na &uacute;ltima figura 

                do inciso II do art. 585 do CPC, podendo a CONTRATADA promover 

                o registro do d&eacute;bito em cadastros de prote&ccedil;&atilde;o 

                ao cr&eacute;dito e/ou encaminh&aacute;-los a protesto, a seu 

                exclusivo crit&eacute;rio.<br>

                Par&aacute;grafo Quinto: A suspens&atilde;o ou interrup&ccedil;&atilde;o 

                do pagamento s&oacute; ocorrer&aacute; por expressa e escrita 

                comunica&ccedil;&atilde;o da op&ccedil;&atilde;o por rescindir 

                o contrato, manifestada pelo CONTRATANTE com anteced&ecirc;ncia 

                m&iacute;nima de 30 (trinta) dias.<br>

                Par&aacute;grafo Sexto: Em caso de inadimpl&ecirc;ncia a CONTRATADA 

                poder&aacute; optar:<br>

                a) Pela rescis&atilde;o contratual, independente de poder exigir 

                o d&eacute;bito vencido e o devido no m&ecirc;s da efetiva&ccedil;&atilde;o 

                da rescis&atilde;o;<br>

                b) Pela emiss&atilde;o de letra de c&acirc;mbio, desde j&aacute; 

                autorizada, pelo valor da(s) parcela(s) vencida(s) conforme crit&eacute;rio 

                previsto no par&aacute;grafo primeiro desta cl&aacute;usula e 

                apresentado para aceite na forma do cap&iacute;tulo III da lei 

                uniforme, aprovada pelo Decreto Legislativo n&ordm; 54/64, arts. 

                21 e seguintes, conforme previs&atilde;o do artigo 7&ordm; do 

                C&oacute;digo de Defesa do Consumidor.<br>

                c) Independentemente da ado&ccedil;&atilde;o das medidas acima, 

                a CONTRATADA poder&aacute; contratar, ap&oacute;s 10 (dez) dias 

                decorridos do vencimento, empresa especializada para proceder 

                &agrave; cobran&ccedil;a do d&eacute;bito de forma amig&aacute;vel 

                e/ou judicial, cabendo ao CONTRATANTE arcar com as despesas e 

                honor&aacute;rios da&iacute; decorrentes.<br>

                Par&aacute;grafo S&eacute;timo: A CONTRATADA, a seu exclusivo 

                crit&eacute;rio, poder&aacute; instituir outros descontos nos 

                valores das presta&ccedil;&otilde;es mensais. Esses descontos, 

                contudo, sempre que aplicados, constituir&atilde;o ato de mera 

                liberalidade podendo ser a qualquer tempo suspensos ou revogados, 

                ficando certo que ser&atilde;o automaticamente desconsiderados, 

                independentemente de pr&eacute;vio aviso, no caso de atraso do 

                pagamento das mensalidades.

				<br><br>CL&Aacute;USULA OITAVA - Em caso 

                de transfer&ecirc;ncia do aluno para outra institui&ccedil;&atilde;o, 

                ser&atilde;o devidas as parcelas vencidas at&eacute; o m&ecirc;s 

                do desligamento.<br>

                Par&aacute;grafo Primeiro: Os valores da contrapresta&ccedil;&atilde;o 

                acima pactuada satisfazem exclusivamente &agrave; presta&ccedil;&atilde;o 

                de servi&ccedil;os decorrentes da carga hor&aacute;ria constante 

                da estrutura curricular do curso contratado, aprovada pelos &oacute;rg&atilde;os 

                p&uacute;blicos competentes e de acordo com a matr&iacute;cula 

                realizada pelo CONTRATANTE.<br>

                Par&aacute;grafo Segundo: N&atilde;o est&atilde;o inclu&iacute;dos 

                neste contrato, quaisquer servi&ccedil;os especiais e opcionais 

                efetivamente prestados ao aluno tais como: segunda chamada de 

                provas e testes, declara&ccedil;&otilde;es, atividades extracurriculares, 

                disciplinas optativas, segunda via de hist&oacute;rico, segunda 

                via de carn&ecirc; ou identidade escolar, certificado de conclus&atilde;o 

                de curso, transfer&ecirc;ncia, curso de f&eacute;rias, os quais 

                ser&atilde;o cobrados &agrave; parte.

				<br><br>CL&Aacute;USULA NONA - O cancelamento 

                ou trancamento de matr&iacute;cula, atendidos os prazos regimentais, 

                ser&aacute; feito atrav&eacute;s de requerimento por escrito do 

                CONTRATANTE ou seu respons&aacute;vel, e ser&aacute; deferido 

                se o mesmo estiver em dia com o pagamento das parcelas at&eacute; 

                a data do requerimento, condi&ccedil;&atilde;o tamb&eacute;m necess&aacute;ria 

                para emiss&atilde;o de qualquer documento oficial.<br>

                Par&aacute;grafo &Uacute;nico: A simples aus&ecirc;ncia de matr&iacute;cula 

                no per&iacute;odo letivo subseq&uuml;ente ao ajustado no presente 

                contrato ser&aacute; interpretada como cancelamento e desist&ecirc;ncia 

                da vaga por parte do CONTRATANTE, acaso n&atilde;o comunicado 

                o desejo em proceder com o trancamento da mesma.

				<br><br>CL&Aacute;USULA D&Eacute;CIMA 

                - A assinatura do presente contrato gera obriga&ccedil;&otilde;es 

                entre as partes apenas em rela&ccedil;&atilde;o ao per&iacute;odo 

                letivo aqui indicado, inexistindo qualquer obriga&ccedil;&atilde;o 

                da CONTRATADA em firmar novos contratos em favor do CONTRATANTE 

                para per&iacute;odos posteriores, notadamente caso este n&atilde;o 

                tenha cumprido rigorosamente as cl&aacute;usulas do presente contrato, 

                inclusive de pagamento.

				<br><br>CL&Aacute;USULA D&Eacute;CIMA 

                PRIMEIRA - Poder&aacute; a CONTRATADA rescindir o presente contrato 

                caso o CONTRATANTE, por qualquer motivo, comprometa o bom nome 

                ou a reputa&ccedil;&atilde;o do primeiro, ou ainda no caso de 

                indisciplina, devendo ser aplicada a pena de cancelamento de matr&iacute;cula 

                prevista no regimento da CONTRATADA.

				<br><br>CL&Aacute;USULA D&Eacute;CIMA 

                SEGUNDA - Ao firmar o presente, o CONTRATANTE declara ter conhecimento 

                pr&eacute;vio do Regimento Interno da<br>

                CONTRATADA, que passa a fazer parte integrante deste contrato, 

                submetendo-se &agrave;s suas disposi&ccedil;&otilde;es, bem como 

                &agrave;s demais obriga&ccedil;&otilde;es constantes da legisla&ccedil;&atilde;o 

                aplic&aacute;vel &agrave; &aacute;rea de ensino.

				<br><br>CL&Aacute;USULA D&Eacute;CIMA 

                TERCEIRA - Este contrato entra em vigor quando o CONTRATANTE cumprir 

                totalmente os seguintes requisitos:<br>

                a) Apresentar documenta&ccedil;&atilde;o completa exigida no &#8220;Formul&aacute;rio 

                de Matr&iacute;cula&#8221;;<br>

                b) Comprovar o pagamento da 1&ordf; presta&ccedil;&atilde;o;<br>

                Par&aacute;grafo &Uacute;nico: O n&atilde;o cumprimento de qualquer 

                das exig&ecirc;ncias acima, implica na invalida&ccedil;&atilde;o 

                do presente contrato.

				<br><br>CL&Aacute;USULA D&Eacute;CIMA 

                QUARTA &#8211; A CONTRATADA n&atilde;o se responsabilizar&aacute;, 

                em qualquer hip&oacute;tese, pelos pertences individuais do CONTRATANTE, 

                os quais dever&atilde;o comparecer &agrave;s atividades acad&ecirc;micas 

                apenas com os objetos indispens&aacute;veis ao cumprimento de 

                suas atividades, devendo ser orientados a zelar pela guarda dos 

                mesmos, especialmente quando se encontrarem nas &aacute;reas de 

                circula&ccedil;&atilde;o da institui&ccedil;&atilde;o. A mesma 

                aus&ecirc;ncia de responsabilidade vigorar&aacute; em rela&ccedil;&atilde;o 

                aos ve&iacute;culos e pertences do CONTRATANTE mantidos no estacionamento 

                de uso geral, oferecido por medida de liberalidade nas imedia&ccedil;&otilde;es 

                da sede da CONTRATADA. 

				<br><br>CL&Aacute;USULA D&Eacute;CIMA 

                QUINTA - As partes elegem o Foro de(o) <font color="#FF0000"><?php print "$row_reg[regiao]"; ?> 

                </font> para dirimir toda e qualquer a&ccedil;&atilde;o com base 

                no presente contrato.

				<br><br>

                E por estarem as partes de acordo com os termos do presente Contrato, 

                assinam o mesmo em 02 (duas) vias de igual teor e forma, para 

              um s&oacute; efeito legal juntamente com 02 (duas) testemunhas.</font></p>

              <br><br>

                <font color="#FF0000"><?php print "$row_reg[regiao], $dia de $mes de $ano"; ?></font></p>

              <p></p>

              <p><br>

                ________________________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________________________________________<br>

                ASSINATURA CONTRATANTE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ASSINATURA 

                CONTRATADO</p>

              <p></p>

              <p> ________________________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;__________________________________________<br>

                ASSINATURA TESTEMUNHA 1 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ASSINATURA 

                TESTEMUNHA 2</p>

              <p><br>

                Nome:_________________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nome:_____________________________________</p>

              <span class='style4'>

              <hr>

              <strong>

              <center>
<?php
$end = new empresa();
$end -> endereco('black','13px');
?>
                <font size="1"><br>

              <BR>

                </font>
              </center>
              </strong></span><BR>

            </blockquote>

          </td>

          <td width='20' rowspan='2'>&nbsp;</td>

        </tr>

        

        <tr> 

          <td bgcolor='#8FC2FC' class='igreja' height='12'> 

            <div align='center'></div></td>

        </tr>

      </table>

    </td>

  </tr>

</table>

</body>

</html>

<?php

}

?>