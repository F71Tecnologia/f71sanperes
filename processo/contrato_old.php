<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../conn.php";



$regiao = $_REQUEST['regiao'];

$id_prestador = $_REQUEST['prestador'];



$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "3"){

print "

<script>

alert(\"Você não pode imprimir este CONTRATO DE PRESTAÇÃO DE SERVIÇOS sem ter impresso o MEMORANDO INTERNO\");

window.close();

</script>";

}else{



$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_prestador[id_projeto]'");

$row_projeto = mysql_fetch_array($result_projeto);





$data = date("d/m/Y");

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>CONTRATO DE PROCESSO</title>

<style type="text/css">

<!--

.style2 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 30px;

}

.style6 {

	font-size: 17px;

	font-weight: bold;

	color: #000000;

}

.style10 {

	font-size: 12px;

	font-family: Arial, Helvetica, sans-serif;

	font-weight: bold;

	color: #000000;

}

.style12 {

	font-size: 12px;

	font-weight: bold;

}

.style14 {font-size: 12px; font-family: Arial, Helvetica, sans-serif; }

.style15 {

	font-size: 12px;

	color: #000000;

}

.style17 {font-family: Arial, Helvetica, sans-serif; font-size: 30px; color: #000000; }

.style18 {font-size: 12px; font-family: Arial, Helvetica, sans-serif; color: #000000; }

-->

</style>

<link href="../net1.css" rel="stylesheet" type="text/css" />
</head>



<body>

<table width="95%" border="0" align="center" cellpadding="10" cellspacing="0" bordercolor="#003300" class="bordaescura1px">

  <tr>

    <td bgcolor="#FFFFFF"><center>
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?>
     <!-- <img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--><br />

        </center>

    </div>

      <p align="center" class="style2"><span class="style6">CONTRATO DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</span></p>

      <p align="center" class="style17"><span class="style12">Processo n. 

        <?=$row_prestador['numero']?>

      </span></p>

      <p align="justify" class="style18">Pelo  presente instrumento particular de presta&ccedil;&atilde;o de servi&ccedil;os, de um lado <strong>
<? 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?></strong>,  inscrito no CNPJ/MF sob o n&ordm; <strong><?=$row_prestador['cnpj']?></strong> situada Na <strong><?=$row_prestador['endereco']?></strong>, representado neste ato por <strong><?=$row_prestador['responsavel']?>, <?=$row_prestador['nacionalidade']?></strong>, <strong><?=$row_prestador['civil']?>, <?=$row_prestador['formacao']?></strong>, portador do RG n&ordm;. <strong><?=$row_prestador['rg']?>.</strong> e do CPF N.&ordm; <strong><?=$row_prestador['cpf']?></strong>, doravante denominado<strong> CONTRATANTE</strong> e de outro lado  a empresa <strong><?=$row_prestador['c_razao']?></strong>  sediada  &agrave; <strong><?=$row_prestador['c_endereco']?>.</strong>, inscrita no &nbsp;CNPJ sob n.&ordm; <strong><?=$row_prestador['c_cnpj']?></strong>, aqui  representada pelo Sr. <strong><?=$row_prestador['c_responsavel']?></strong>, <strong><?=$row_prestador['c_nacionalidade']?></strong>, <strong><?=$row_prestador['c_civil']?></strong>, portador da  c&eacute;dula de identidade RG n.&ordm; <strong><?=$row_prestador['c_rg']?></strong>, inscrito no CPF/MF sob  n.&ordm; <strong><?=$row_prestador['c_cpf']?></strong>, doravante denominado <strong>CONTRATADO</strong>, tem justo e contratado  o seguinte:</p>

      <p align="center" class="style10">      DO  OBJETO</p>

      <p align="justify" class="style18">Cl&aacute;usula  1&ordf; - O <strong>CONTRATANTE</strong> é um instituto, sem fins lucrativos e que tem por uma de  suas finalidades a elaboração e implantação de projetos na área de educação,  saúde, meio ambiente e assistência social, que visem a formação, habilitação,  capacitação e qualificação profissional de jovens, adultos e de profissionais.</p>

      <p align="justify" class="style18">Cl&aacute;usula  2&ordf; - O presente contrato refere-se especificamente <strong>

        <?=$row_prestador['especificacao']?></strong>.</p>

      <p align="justify" class="style18">Cl&aacute;usula 3&ordf; - Para cada servi&ccedil;o efetuado pelo <strong>CONTRATADO</strong>,  dever&aacute; ser confeccionado um Anexo que, rubricado pelas partes, passar&aacute; a  integrar o presente contrato.</p>

      <p align="justify" class="style18">Par&aacute;grafo &Uacute;nico &ndash; Em cada Anexo ser&atilde;o  discriminados pelo menos, o cliente, o valor do contrato, a forma de pagamento,  a participa&ccedil;&atilde;o, a forma e o valor da remunera&ccedil;&atilde;o do <strong>CONTRATADO</strong>.</p>

      <p align="center" class="style10"><u>OBRIGA&Ccedil;&Otilde;ES  DO CONTRATADO</u></p>

      <p align="justify" class="style18">Cl&aacute;usula 4&ordf; - O  <strong>CONTRATADO</strong> se obriga a prestar para o <strong>CONTRATANTE</strong>, servi&ccedil;os de <strong><?=$row_prestador['objeto']?></strong>,  bem como dar provimento t&eacute;cnico e operacional aos programas fornecidos visando  a participa&ccedil;&atilde;o consistente(s) do CONTRATANTE nestes projetos, sem exclus&atilde;o de  outras obriga&ccedil;&otilde;es discriminadas e especificadas em Anexos firmados pelas  partes.</p>

      <p align="justify" class="style18">Cl&aacute;usula  5&ordf; - O <strong>CONTRATADO</strong> dever&aacute; prestar todo trabalho at&eacute; a conclus&atilde;o final do  neg&oacute;cio. N&atilde;o &eacute; bastante a indica&ccedil;&atilde;o ou apresenta&ccedil;&atilde;o do cliente, devendo,  prestar seus servi&ccedil;os na integralidade, observando sempre as normas e crit&eacute;rios  do <strong>CONTRATANTE</strong>, salvo disposi&ccedil;&atilde;o ao contr&aacute;rio a crit&eacute;rio exclusivo do <strong>CONTRATANTE</strong>.<br />

        Ap&oacute;s  indica&ccedil;&atilde;o do cliente, o <strong>CONTRATADO</strong> ter&aacute; a prioridade para a concretiza&ccedil;&atilde;o do  contrato, pelo per&iacute;odo de 90 (noventa) dias contados a partir do anexo referenciado,  prorrogados se necess&aacute;rio, em comum acordo com o <strong>CONTRATANTE</strong>. </p>

      <p align="justify" class="style18">Cl&aacute;usula  6&ordf; - Fica vedado ao <strong>CONTRATADO</strong> a conclus&atilde;o direta dos neg&oacute;cios especificados  nos Anexos, inclusive ficando proibido o recebimento de quaisquer import&acirc;ncias,  a qualquer t&iacute;tulo.</p>

      <p align="justify" class="style18">Cl&aacute;usula  7&ordf; - O <strong>CONTRATADO</strong> concorda que toda transa&ccedil;&atilde;o ou neg&oacute;cio dever&aacute; ser efetuado  sob controle e responsabilidade do <strong>CONTRATANTE</strong>.</p>

      <p align="justify" class="style18">Cl&aacute;usula 8&ordf; - O <strong>CONTRATADO</strong> representar&aacute; o <strong>CONTRATANTE</strong>  perante os potenciais clientes, zelando pelo bom nome e de seus servi&ccedil;os,  abstendo-se de praticar qualquer ato que possa, de alguma maneira, lhe  prejudicar a boa reputa&ccedil;&atilde;o. </p>

      <p align="justify" class="style18">Cl&aacute;usula  9&ordf; - O <strong>CONTRATADO</strong> se obriga a fornecer, a qualquer momento, relat&oacute;rio do  andamento dos neg&oacute;cios, na sede do CONTRATANTE ou por correio eletr&ocirc;nico, no  prazo m&aacute;ximo de dez dias da solicita&ccedil;&atilde;o por escrito.</p>

      <p align="center" class="style10"><u>OBRIGA&Ccedil;&Otilde;ES  DO CONTRATANTE</u></p>

      <p align="justify" class="style18">Cl&aacute;usula 10&ordf; - O  <strong>CONTRATANTE</strong> fornecer&aacute; ao <strong>CONTRATADO</strong> todos os documentos necess&aacute;rios &agrave; execu&ccedil;&atilde;o  dos servi&ccedil;os ora contratados, bem como executar&aacute; ou mandar&aacute; executar, por sua  conta, todos os servi&ccedil;os necess&aacute;rios para obten&ccedil;&atilde;o ou cumprimento dos servi&ccedil;os.</p>

      <p align="justify" class="style18">Cl&aacute;usula  11&ordf; - O <strong>CONTRATANTE</strong> disponibilizar&aacute; gratuitamente, em sua sede, ao <strong>CONTRATADO</strong>,  local e condi&ccedil;&otilde;es para o desenvolvimento que melhor atenderem &agrave;s necessidades  dos servi&ccedil;os aqui contratados.</p>

      <p align="justify" class="style18">Cl&aacute;usula 12&ordf; - Todos os  tributos incidentes sobre os servi&ccedil;os captados pelo <strong>CONTRATADO</strong>, legalmente atribu&iacute;veis  ao <strong>CONTRATADO</strong>, ser&atilde;o pagos pelo <strong>CONTRATADO</strong>.</p>

      <p align="center" class="style18"><strong><u>      CONDI&Ccedil;&Otilde;ES  GERAIS</u></strong></p>

      <p align="justify" class="style18">Cl&aacute;usula 13&ordf; - Sobre  cada neg&oacute;cio concretizado, o <strong>CONTRATADO</strong> receber&aacute; uma remunera&ccedil;&atilde;o em horas/consultoria,  prestada sobre o fechamento do contrato com cada Cliente conforme discrimina&ccedil;&atilde;o  constante em cada Anexo  conforme estabelecido na Cl&aacute;usula 3&ordf;.</p>

      <p align="justify" class="style18">Par&aacute;grafo 1&ordm; - O  <strong>CONTRATADO</strong> receber&aacute; a remunera&ccedil;&atilde;o, proporcionalmente, em tantas parcelas  quantas forem as parcelas contratadas efetivamente recebidas pelo <strong>CONTRATANTE</strong>.</p>

      <p align="justify" class="style18">Par&aacute;grafo 2&ordm; - Se o  <strong>CONTRATADO</strong> trabalhar em conjunto com outro(s) parceiro(s), ser&atilde;o de sua inteira  responsabilidade os atos da media&ccedil;&atilde;o, bem como a divis&atilde;o da remunera&ccedil;&atilde;o, com  exce&ccedil;&atilde;o dos casos acordados entre as partes.</p>

      <p align="justify" class="style18">Par&aacute;grafo 3&ordm; - As  despesas efetuadas pelo <strong>CONTRATADO</strong>, na media&ccedil;&atilde;o dos neg&oacute;cios ou transa&ccedil;&otilde;es  ser&atilde;o de sua inteira responsabilidade e por sua conta, com exce&ccedil;&atilde;o &agrave;quelas  discriminadas em contrato ou acordado entre as partes.</p>

      <p align="justify" class="style18">Cl&aacute;usula 14&ordf; - Toda  remunera&ccedil;&atilde;o ser&aacute; paga mediante emiss&atilde;o de Nota Fiscal de Servi&ccedil;os, ficando o <strong>CONTRATADO</strong>,  integralmente, respons&aacute;vel por todos os custos, principalmente os tribut&aacute;rios  incidentes sobre os mesmos.</p>

      <p align="justify" class="style18">Cl&aacute;usula 15&ordf; - O  <strong>CONTRATADO</strong> n&atilde;o ter&aacute; v&iacute;nculo empregat&iacute;cio com o <strong>CONTRATANTE</strong>, n&atilde;o estando,  portanto, sujeito &agrave; obrigatoriedade de hor&aacute;rio de trabalho, local, sendo-lhe  livre o direito de exercer seu mister, quando quiser e onde quiser.</p>

      <p align="justify" class="style18">Cl&aacute;usula  16&ordf; - O prazo de vig&ecirc;ncia deste contrato &eacute; indeterminado, com in&iacute;cio na data da  assinatura da presente, facultada &agrave;s partes a sua rescis&atilde;o a qualquer tempo,  mediante notifica&ccedil;&atilde;o por escrito.</p>

      <p align="justify" class="style18">Par&aacute;grafo &Uacute;nico &ndash;  Em caso de rescis&atilde;o do presente contrato, ser&atilde;o consideradas finalizadas, para  efeito de remunera&ccedil;&atilde;o do <strong>CONTRATADO</strong>, todos os contratos intermediados, salvo  disposi&ccedil;&atilde;o em contr&aacute;rio, constantes dos Anexos ou de acordo firmado entre as partes. </p>

      <p align="justify" class="style18">Cl&aacute;usula 17&ordf; - As partes  obrigam-se a manter sigilo sobre as informa&ccedil;&otilde;es recebidas, restringindo o uso  das informa&ccedil;&otilde;es ao atendimento do objeto deste contrato. </p>

      <p align="justify" class="style18">Cl&aacute;usula  18&ordf; - A infra&ccedil;&atilde;o pelo <strong>CONTRATADO</strong> de qualquer Cl&aacute;usula ou condi&ccedil;&atilde;o do presente  contrato, dar&aacute; &agrave; <strong>CONTRATANTE</strong> o direito de consider&aacute;-lo rescindido com base em  fundamenta&ccedil;&atilde;o apresentada por escrito, independente de qualquer notifica&ccedil;&atilde;o ou  providencia judicial ou extrajudicial, especialmente se houver:</p>

    

      <ol class="style18"><li><p align="justify">transfer&ecirc;ncia pelo <strong>CONTRATADO</strong>, no todo ou  em parte, das obriga&ccedil;&otilde;es assumidas no presente instrumento, sem pr&eacute;via  autoriza&ccedil;&atilde;o escrita do <strong>CONTRATANTE</strong>.</p>

        </li>

        <li>

          <p align="justify">n&atilde;o cumprimento por qualquer uma das  partes, no prazo de 10 (dez) dias &uacute;teis, de qualquer notifica&ccedil;&atilde;o por escrito que  seja feita em assuntos relacionados ao presente contrato.</p>

        </li>

      </ol>

      <p align="justify" class="style18">Cl&aacute;usula  19&ordf; - Fica eleito o foro da Comarca de S&atilde;o Paulo, com exclus&atilde;o de qualquer  outro, por mais privilegiado que seja, como competente para apreciar todas as  quest&otilde;es decorrentes do presente contrato.<br />

        E assim, por concordarem com os termos  deste Contrato, firmam-no em duas vias, na presen&ccedil;a das testemunhas abaixo  identificadas e qualificadas.</p>

      <p align="left" class="style18">S&atilde;o  Paulo,<span class="style12">

        <?=$row_prestador['data_proc']?></span>.</p>

      <p align="center" class="style18"> _________________________<br />

        CONTRATANTE:<br />

        <strong>INSTITUTO  SORRINDO PARA A VIDA<br />

        <br />

        <br />

        </strong>_________________________<br />

        CONTRATADA:<br />

        <strong>

        <?=$row_prestador['c_razao']?>

        <br />

        <br />

          </strong></p>

      <p class="style18">TESTEMUNHAS:<br />

      </p>

      <p class="style14"><span class="style15">______________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_____________________________<br />

  Nome:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nome:&nbsp;&nbsp;<br />

        <br />

        Documento:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Documento: </span><br />

      </p>    </td>

  </tr>

</table>

</body>

</html>

<?php

if($row_prestador['imprimir'] == "3"){

$data_b = date("Y-m-d");

$id_user = $_COOKIE['logado'];



mysql_query("UPDATE prestadorservico SET imprimir = '4', contratado_por = '$id_user', contratado_em = '$data_b', acompanhamento = '3' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>

