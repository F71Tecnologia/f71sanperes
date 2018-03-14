<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{





?><html>

<head>

<title>:: Intranet ::</title>

<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>

<link href='../net.css' rel='stylesheet' type='text/css'>



<style type='text/css'>

<!--

.style4 {font-family: Arial, Helvetica, sans-serif}

.style5 {color: #FF0000}

.style6 {

	font-family: Arial, Helvetica, sans-serif;

	color: #FF0000;

	font-weight: bold;

}

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

                <td><br>

                  <span class='style4'>
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src='../imagens/certificadosrecebidos.gif' width='120' height='86' align='middle'>--><strong>TERMO DE COMPROMISSO DE BOLSA-AUX&Iacute;LIO</strong></span></td>

              </tr>

            </table>          

            <blockquote>

              <p align='justify' class='style4'>CEDENTE:  SOE &ndash; SISTEMA OBJETIVO DE ENSINO, CNPJ &ndash; 03.635.819/0001-13, devidamente  autorizada a funcionar como institui&ccedil;&atilde;o de ensino pela Portaria E/SADE/AUT n. &ordm;  120 de 29 de outubro de 2002, sediado na Rua Olinda Elis, 278 &ndash;&nbsp; Campo Grande - RJ, conforme Termo de conv&ecirc;nio  datado de 10 de janeiro de 2006;&nbsp;&nbsp;&nbsp;&nbsp; Apresentam-se como CEDENTE EUCACIONAL:<br>

                CONCEDENTE:  O INSTITUTO SORRINDO PARA A VIDA (Organiza&ccedil;&atilde;o da Sociedade Civil de Interesse  P&uacute;blico &ndash; OSCIP), pessoa jur&iacute;dica de direito privado, sem fins lucrativos,  estabelecida na estabelecida na Rua Jo&atilde;o Caetano n&ordm; 359- Centro, Itabora&iacute;-Rio  de Janeiro, CNPJ/MF n&deg; 06.888.897/0001-18 e como BOLSISTA, <span class='style5'><strong>&lt;NOME_bolsista&gt;</strong></span>,  estudante, <strong>residente &aacute; </strong><span class='style5'><strong>&lt;Endere&ccedil;o_bolsista&gt;</strong></span> <strong>Tel</strong>.&nbsp;<span class='style5'><strong>&lt;tel_bolsista&gt;</strong></span>, <strong>nascido em <span class='style5'>&lt;nascimento&gt;</span>.</strong> &nbsp;portador  da c&eacute;dula de identidade <strong>Rg <span class='style5'>&lt;identidade&gt; </span></strong>, inscrito no <strong>CPF</strong> <strong>n&deg;</strong><span class='style5'><strong> &lt;CPF&gt; </strong></span>, celebram o presente <strong>TERMO DE BOLSA-AUX&Iacute;LIO</strong>, conforme condi&ccedil;&otilde;es a seguir:<br>

                1. A Bolsa ter&aacute; a dura&ccedil;&atilde;o de 1 (um) ano podendo ser  rescindida ou suspensa atrav&eacute;s de simples comunica&ccedil;&atilde;o. Se o contrato for  suspenso a sua retomada dar-se-&aacute; tamb&eacute;m, atrav&eacute;s de simples comunica&ccedil;&atilde;o. <br>

                A BOLSA-AUX&Iacute;LIO ser&aacute; realizada nas instala&ccedil;&otilde;es da  PREFEITURA MUNICIPAL DE ITABORA&Iacute;, local em que o Bolsista desempenhar&aacute; as  atividades relacionadas ao curso ministrado pela Concedente, que complementar&atilde;o  e aperfei&ccedil;oar&atilde;o seu conhecimento;<br>

                A Bolsa poder&aacute; cessar mediante simples aviso, escrito,  por quaisquer das partes, n&atilde;o cabendo indeniza&ccedil;&otilde;es a qualquer delas,  liquidando-se apenas as obriga&ccedil;&otilde;es vencidas, tendo em vista que o Bolsista n&atilde;o ter&aacute; v&iacute;nculo empregat&iacute;cio com a  Concedente ou com a PREFEITURA MUNICIPAL DE ITABORA&Iacute;, em raz&atilde;o deste Termo de  Compromisso;<br>

                O per&iacute;odo previsto de BOLSA-AUX&Iacute;LIO poder&aacute; ser  prorrogado, mediante entendimento entre as partes contratantes, prorroga&ccedil;&atilde;o  esta que dever&aacute; ser formalizada atrav&eacute;s de documento complementar a este termo  de compromisso; <br>

                Os seguintes fatos importar&atilde;o na rescis&atilde;o do  contrato de BOLSA-AUX&Iacute;LIO:<br>

  &nbsp;O abandono ou  interrup&ccedil;&atilde;o do curso pelo aluno (Bolsista), trancamento de matr&iacute;cula, conclus&atilde;o  do curso;<br>

  &nbsp;O n&atilde;o  cumprimento de quaisquer das cl&aacute;usulas previstas neste Instrumento Jur&iacute;dico;<br>

  &nbsp;A simples  comunica&ccedil;&atilde;o do cedente e/ou concedente ao Bolsista sobre o encerramento  antecipado do estagi&aacute;rio por motivo do n&atilde;o aproveitamento do educando no  programa, conforme a expectativa inicial;<br>

                A simples comunica&ccedil;&atilde;o de suspens&atilde;o tempor&aacute;ria da  BOLSA-AUX&Iacute;LIO, podendo ser retomada as condi&ccedil;&otilde;es iniciais tamb&eacute;m atrav&eacute;s de  simples comunica&ccedil;&atilde;o;<br>

                Por  ocasi&atilde;o do t&eacute;rmino da BOLSA-AUX&Iacute;LIO, a Concedente fornecer&aacute; ao Bolsista, em  forma de avalia&ccedil;&atilde;o, o resultado de seu aproveitamento; <br>

                2. A Concedente objetivar&aacute; estabelecer as condi&ccedil;&otilde;es b&aacute;sicas  para a consecu&ccedil;&atilde;o da BOLSA-AUX&Iacute;LIO do aluno, que deve necessariamente ser de  interesse curricular, integrando e complementando, na pr&aacute;tica, o ensino  ministrado e, ainda, acrescentar capacita&ccedil;&atilde;o e aperfei&ccedil;oamento t&eacute;cnico,  cultural e social ao Bolsista.<br>

                3.  O Bolsista desempenhar&aacute; suas atividades respeitando os hor&aacute;rios limites de lei,  n&atilde;o podendo o hor&aacute;rio destinado &agrave; BOLSA-AUX&Iacute;LIO coincidir com o hor&aacute;rio  escolar.<br>

                3.1 A freq&uuml;&ecirc;ncia do Bolsista ser&aacute; demonstrada por qualquer  modalidade de controle adotada pela Concedente, sendo que a sua assiduidade ao  curso ministrado, garantir&aacute; um abono ao final do ano.<br>

                4.  O bolsista receber&aacute; como BOLSA-AUX&Iacute;LIO e pela complementa&ccedil;&atilde;o educacional, o  valor refer&ecirc;ncia igual a <strong>R$ <span class='style5'>&lt;SALARIO_BOLSISTA&gt;</span> (reais) </strong>mensais,  ou, se for o caso, o valor concernente ao n&uacute;mero de horas de BOLSA-AUX&Iacute;LIO  efetivamente cumpridas. O pagamento ser&aacute; efetivado no 10&ordm; dia &uacute;til do m&ecirc;s  subseq&uuml;ente ao vencido.<br>

                4.1  O valor da BOLSA-AUX&Iacute;LIO poder&aacute; variar, conforme o desempenho do bolsista, que  ser&aacute; avaliado periodicamente pela Concedente.<br>

                4.2 A import&acirc;ncia referente &agrave; BOLSA-AUX&Iacute;LIO, por n&atilde;o ter  natureza salarial, n&atilde;o estar&aacute; sujeita a qualquer tributa&ccedil;&atilde;o conforme a Lei n. &ordm;  8.859/94.<br>

                5. O Bolsista se obriga a cumprir  fielmente a programa&ccedil;&atilde;o da bolsa e orienta&ccedil;&otilde;es do coordenador e/ou supervisor  designado pela Concedente, bem como as normas e regulamento interno da  Concedente, salvo impossibilidade da qual a mesma ser&aacute; previamente informada.<br>

                6. As atividades do Bolsista poder&atilde;o ser alteradas  com o progresso das atividades, do curr&iacute;culo escolar e do resultado das  avalia&ccedil;&otilde;es, objetivando, sempre, a compatibiliza&ccedil;&atilde;o e a complementa&ccedil;&atilde;o do curso  que ora o bolsista est&aacute; matriculado.<br>

                7.  No per&iacute;odo de vig&ecirc;ncia do presente Termo de Compromisso, o Bolsista ter&aacute;  cobertura e Acidentes Pessoais proporcionada pela Concedente nas condi&ccedil;&otilde;es  estipuladas na ap&oacute;lice com condi&ccedil;&otilde;es b&aacute;sicas elencadas no final deste  instrumento.<br>

                7.1  Ser&aacute; emitido para o Bolsista, como parte integrante e obrigat&oacute;ria deste Instrumento,  o respectivo Certificado Individual de Seguro de Acidentes Pessoais.<br>

                8.  O Bolsista se compromete a zelar pelos instrumentos, equipamentos, materiais e  instala&ccedil;&otilde;es de propriedade da Concedente, do Poder P&uacute;blico ou de terceiros que  lhe forem confiados em raz&atilde;o da bolsa, reservando-se &agrave; Concedente o direito de  responsabilizar o bolsista pelos danos que por ele forem causados por dolo,  neglig&ecirc;ncia, imprud&ecirc;ncia ou imper&iacute;cia.<br>

                9.  Qualquer disputa decorrente deste contrato dever&aacute; ser submetida &agrave; aprecia&ccedil;&atilde;o do  Conselho de Arbitragem a ser escolhido pelas partes e obedecer&aacute;, quanto aos  dispositivos legais interferentes, a seguinte preval&ecirc;ncia hier&aacute;rquica:  Constitui&ccedil;&atilde;o Federal, Lei n&ordm;. 9.307/96 e, na ordem de sua indica&ccedil;&atilde;o, as leis  indicadas na Conven&ccedil;&atilde;o Arbitral das partes.<br>

                9.1 As custas do procedimento arbitral ser&atilde;o, a princ&iacute;pio, de responsabilidade  do Solicitante, sem que isso signifique absolutamente nenhum privil&eacute;gio em  preju&iacute;zo do Solicitado. Caso a senten&ccedil;a arbitral homologue o acordo, a parte  Solicitada ressarcir&aacute; a Solicitante da metade das custas. Se a parte  Solicitante sair vencedora na lide, &agrave; parte Solicitada caber&aacute; ressarci-la do  total das custas. <br>

                O presente contrato cont&eacute;m a totalidade do acordo  entre as partes com respeito ao seu objeto e deixa sem efeito qualquer outro  acordo, seja expresso ou oral, assim como todas as demais comunica&ccedil;&otilde;es  existentes entre as partes relacionadas com o objeto do presente contrato. E,  por estarem de acordo com os termos do presente instrumento, as partes o assinam  a presente via, na presen&ccedil;a de duas testemunhas, para todos os fins e efeitos  de direito.</p>

              <p class='style4'>CONDI&Ccedil;&Otilde;ES B&Aacute;SICAS DA AP&Oacute;LICE DE SEGUROS <span class='style5'><strong>&lt;Num_apolice&gt;</strong></span>  DO <span class='style5'><strong>&lt;BANCO_SEGURO&gt; </strong></span>SEGUROS</p>

              <p class='style6'>&lt;local&gt; &lt;data&gt; </p>

              <p class='style4'>&nbsp; ________________________ &nbsp;&nbsp;&nbsp;  _________________________&nbsp;&nbsp;&nbsp;&nbsp;____________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br>

&nbsp;              CEDENTE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONCEDENTE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BOLSISTA&nbsp; </p>

              <span class='style4'><br>

              <hr>
<?php
$end = new empresa();
$end -> endereco('black', '13px');
?>
              <strong></strong></span><span class='style4'><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>

              </span><BR>

            <BR>

          </p>

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