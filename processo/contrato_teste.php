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

alert(\"Voc� n�o pode imprimir este CONTRATO DE PRESTA��O DE SERVI�OS sem ter impresso o MEMORANDO INTERNO\");

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

ul{
list-style:none;	
}

</style>

<link href="../net1.css" rel="stylesheet" type="text/css" />
</head>



<body>

<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" >

  <tr>

    <td bgcolor="#FFFFFF"><center>
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();

$nomEmp= new empresa();
?>
     <!-- <img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--><br />

        </center>

    </div>

     <h3 align="center"> CONTRATO DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</h3>

      <p align="right" class="style17"><span class="style12">Processo n. 
        <?=$row_prestador['numero']?>
      </span></p>



<p> Pelo presente instrumento particular de presta��o de servi�os, de um lado <?php
$nomEmp -> nomeEmpresa(); 
 ?>, associa��o civil sem fins lucrativos, com sede <?=$row_prestador['endereco']?>, inscrita no CNPJ/MF sob o n� <?=$row_prestador['cnpj']?>, neste ato por seu representante legal, <?=$row_prestador['responsavel']?>, <?=$row_prestador['nacionalidade']?>,   <strong><?=$row_prestador['civil']?>, <?=$row_prestador['formacao']?></strong>, portador da C�dula de Identidade n.� <?=$row_prestador['rg']?>, inscrito no CPF/MF sob o n.� <strong>
<?=$row_prestador['cpf']?>
</strong>, doravante denominada CONTRATANTE; e, de outro lado, <strong><?=$row_prestador['c_razao']?></strong>, sociedade simples, com sede na <strong><?=$row_prestador['c_endereco']?>.</strong>, inscrita no  CNPJ sob n.� <strong><?=$row_prestador['c_cnpj']?></strong>, neste ato representada pelo(a) <strong><?=$row_prestador['c_responsavel']?></strong>, <strong><?=$row_prestador['c_nacionalidade']?></strong>,  <strong><?=$row_prestador['c_civil']?></strong>, <strong>
<?=$row_prestador['cpf']?>
</strong>, portador da c�dula de identidade RG n.� <strong><?=$row_prestador['c_rg']?></strong>, inscrito no CPF/MF sob n.� 
<strong><?=$row_prestador['c_cpf']?></strong>, doravante denominada CONTRATADA, tem justo e contratado o seguinte:</p>



<h3>1.	INTERPRETA��O</h3>

<ul>
 <li> <p>1.1	Para os fins do presente Contrato:</p>
 	  <p><strong>"Lei Aplic�vel"</strong> significa, em rela��o a qualquer procedimento, obriga��o, responsabilidade e/ou circunst�ncia, todas as leis, normas e padr�es estabelecidos por �rg�o governamental ou regulador e geralmente adotados para o setor, bem como os princ�pios de auto-regulamenta��o regionais, nacionais ou internacionais, se aplic�veis a tais procedimentos, obriga��es, responsabilidades e/ou circunst�ncias;</p>

		<p><strong>"Dia �til" </strong>significa todos os dias da semana, exceto s�bado e domingo, nos quais operem os bancos em Rio de Janeiro ou na regi�o objeto da presta��o de servi�os;</p>

		<p><strong>"Remunera��o"</strong> significa os valores devidos � CONTRATADA pela CONTRATANTE, de acordo com o disposto na Cl�usula 6;</p>

		<p><strong>"Boas Pr�ticas"</strong> significa, em rela��o a determinado procedimento e sob qualquer circunst�ncia, o exerc�cio da boa t�cnica, dilig�ncia, prud�ncia, experi�ncia, per�cia, previsibilidade e crit�rio de decis�o e julgamento que se esperaria de uma pessoa h�bil e experiente no que tange ao cumprimento da Lei Aplic�vel em tais circunst�ncias ou em eventos semelhantes;</p>

		<p><strong>"Padr�o Adequado" </strong>significa, em respeito aos procedimentos para a presta��o de Servi�os em qualquer circunst�ncia, o padr�o vislumbrado para a presta��o desses Servi�os, tendo-se utilizado as Boas Pr�ticas com esse prop�sito espec�fico;</p>

		<p><strong>"Tributo" </strong>significa qualquer tributo, o que inclui, entre outros, impostos, taxas, tarifas, contribui��es sociais e outras exa��es, bem como todas as respectivas reten��es ou dedu��es, acrescidas de quaisquer multas, penalidades ou juros devidos por inadimpl�ncia ou atraso no pagamento;
		</p>
        </li>
      <li>
      	<p>1.2	Ademais, no que tange ao presente Contrato:</p>
        <ul>
       		 <li>
		        <p>(a)	as refer�ncias ao presente instrumento ou a qualquer outro documento correlato aludir�o ao presente Contrato, suas premissas e anexos, ou a outro documento aplic�vel, conforme eventualmente aditado e/ou alterado, sob qualquer pretexto;</p>
		        
		        <p>(b)	as refer�ncias a qualquer das partes abranger�o e aludir�o, no que for aplic�vel, a seus respectivos sucessores legais, cession�rios ou benefici�rios, conforme o caso;</p>
		        
		        <p>(c)	as refer�ncias a premissas, cl�usulas, Anexos e seus dispositivos aludir�o �queles aqui contidos, respectivamente;</p>
		        
		        <p> (d)	as refer�ncias a determinada legisla��o aludir�o tamb�m a eventuais aditivos, altera��es ou prorroga��es da mesma, assim como a qualquer legisla��o a ela subordinada;</p>
		
				<p>(e)	as refer�ncias a "pessoa" abranger�o qualquer pessoa f�sica ou jur�dica, sociedade por a��es ou quotas, sociedade em comandita, joint venture, associa��o, organiza��o, institui��o, trust ou reparti��o, com ou sem personalidade jur�dica distinta;</p>

<p align="right" style="font-weight:bold;">1/5</p>
		

				<p>(f)	as refer�ncias a determinado g�nero estendem-se a todos os g�neros, ao passo que as refer�ncias no singular estendem-se ao plural e vice-versa; e</p>
		
				<p>(g)	os cabe�alhos s�o aqui inseridos apenas para conveni�ncia, n�o devendo ser considerados na interpreta��o do presente Contrato.</p>     
               </li>
         </ul> 
      </li>      
      </ul>
      
      <h3>2.	PRAZO DE VIG�NCIA</h3>
      <ul>
      	<li>
        	 <p>2.1	O presente Contrato entrar� em pleno vigor na data de sua assinatura, assim permanecendo pelo prazo estabelecido no <strong>Anexo I � Do Prazo de Vig�ncia</strong>.</p>
             <p>2.2	O presente Contrato poder� ser renovado por per�odos iguais e sucessivos mediante acordo por escrito entre as partes.</p>
                         
        </li>
      </ul>
      
    <h3>SERVI�OS</h3>
    <ul> 
    	<li>
        	<p>3.1	A CONTRATADA prestar� servi�os descritos no <strong>Anexo II � Dos Servi�os </strong>do presente Contrato, doravante denominados <strong>"Servi�os"</strong>, observadas as Boas Pr�ticas e, ainda, as disposi��es contidas no presente instrumento e os termos da Lei Aplic�vel.</p>
            <ul>
            	<li>
           			 <p>3.1.1	A presta��o dos Servi�os ora aven�ada n�o tem sob forma nenhuma ou sob qualquer pretexto car�ter de exclusividade, podendo a CONTRATANTE utilizar-se de outras prestadoras de servi�os a seu exclusivo crit�rio.</p>
                </li>               
                
            </ul>
        </li>
        <li>
        <p>3.2	Sem preju�zo �s demais obriga��es assumidas pela CONTRATADA neste instrumento, esta prestar� os Servi�os em atendimento ao Padr�o Adequado, devotando-lhes os mais altos padr�es de t�cnica, zelo, especifica��es, padr�es e crit�rios de qualidade, prioridade, aten��o e tempestividade. </p>

		<p>3.3	A CONTRATADA far� com que as obriga��es por ela ora assumidas sejam atendidas por uma equipe suficiente (com n�mero de integrantes adequado) e devidamente qualificada, treinada e capacitada para esse prop�sito espec�fico.</p>

		<p>3.4	A CONTRATADA abster-se-� da pr�tica de atos que possam ocasionar qualquer esp�cie de dano ou preju�zo aos neg�cios ou � reputa��o da CONTRATANTE e/ou seus administradores.</p>

        </li>
    </ul>
    
    <h3>4.	OBRIGA��ES DA CONTRATADA</h3>
    
    <ul>	
    	<li>
        <p>4.1	Al�m das obriga��es assumidas em outras cl�usulas do Contrato, a CONTRATADA, neste ato, compromete-se e obriga-se a:</p>
        
        <ul>
        	<li>
            <p>(a)	fornecer m�o-de-obra especializada, altamente qualificada, bem como todos equipamentos e materiais necess�rios para a realiza��o dos Servi�os;

</p>
			<p>(b)	prestar os Servi�os de acordo com as melhores t�cnicas profissionais e dentro dos mais elevados padr�es de conduta �tica, moral e profissional, conforme pol�ticas adotadas pela CONTRATANTE, obedecendo todas as normas t�cnicas pertinentes aos Servi�os, bem como as normas de seguran�a do trabalho, assumindo, neste ato, total e integral responsabilidade pelos Servi�os;</p>

			<p>(c)	responsabilizar-se integralmente pela conduta, freq��ncia e pontualidade de seus funcion�rios envolvidos diretamente na execu��o dos Servi�os, podendo a CONTRATANTE exigir substitui��es de funcion�rios a seu exclusivo crit�rio;	</p>

			<p>(d)	afastar ou substituir o seu funcion�rio, cuja conduta ou presen�a seja considerada pela CONTRATANTE inconveniente, imediatamente ap�s o recebimento de comunica��o da CONTRATANTE, devendo providenciar sua imediata substitui��o por outro profissional devidamente qualificado e capacitado para a presta��o dos Servi�os;</p>

			<p>(e)	indenizar a CONTRATANTE por quaisquer danos ou preju�zos e responsabilizar-se integralmente por, mas n�o se limitando a, quaisquer obriga��es e indeniza��es, perdas e danos, lucros cessantes, preju�zos de quaisquer esp�cies, ou sob quaisquer t�tulos, perdas de neg�cios, perda, avaria, danifica��o parcial ou total ou extravio de mercadorias, produtos, equipamentos, documentos, defeitos, ou quaisquer outros danos diretos, indiretos, acidentais, especiais, conseq�enciais ou punitivos, decorrentes direta ou indiretamente, da presta��o dos Servi�os; </p>
            
            
           <p> (f)	n�o fazer uso do nome, marca ou qualquer outra propriedade intelectual da CONTRATANTE em qualquer material de divulga��o, promo��o ou propaganda pessoal ou de terceiros, salvo mediante expressa autoriza��o;</p> 


		
		<p align="right" style="font-weight:bold;">2/5</p>
    <p>&nbsp;</p>
			<p>(g)	prestar contas � CONTRATANTE das atividades desenvolvidas na presta��o dos Servi�os, sempre que solicitado pela CONTRATANTE; </p>

			<p>(h)	efetuar os competentes seguros de vida, objetivando a integral cobertura securit�ria na ocorr�ncia de um eventual sinistro envolvendo seus empregados e/ou prepostos alocados para a presta��o dos Servi�os;
</p>

			<p>(i)	comprovar o atendimento de todas as exig�ncias legais trabalhistas, previdenci�rias, securit�rias ou de outra natureza com rela��o aos funcion�rios que prestar�o os Servi�os, sempre que requisitado pela CONTRATANTE;</p>

			<p>(j)	responsabilizar-se moral e materialmente pelos seus empregados, prepostos e/ou terceiros sob sua responsabilidade.</p>
            </li>
        </ul>        
        </li>
        
        <li>
        	<p>4.2	Todos e quaisquer encargos decorrentes de condena��o judicial, sejam trabalhistas, previdenci�rios e/ou fundi�rios, pertinentes aos profissionais que venham a ser indicados pela CONTRATADA para execu��o dos Servi�os, ser�o de exclusiva responsabilidade da CONTRATADA, n�o respondendo a CONTRATANTE por tais encargos, sequer em car�ter subsidi�rio, ficando certo que, entre o pessoal da CONTRATADA e a CONTRATANTE n�o h� e n�o haver� nenhuma rela��o ou v�nculo trabalhista.</p>

			<p>4.3	A CONTRATADA garante, desde j�, os Servi�os por ela prestados em raz�o deste contrato, quanto � qualidade, desempenho e funcionalidade, bem como contra todo e qualquer defeito, obrigando-se desde j� a refazer, reparar ou repor toda a execu��o dos Servi�os, assim como danos e preju�zos deles decorrentes.</p>
        </li>
        
    </ul>
    
    
    <h3>5.	OBRIGA��ES DA CONTRATANTE</h3>
    
    <ul>
    	<li>
        	<p>(a)	pagar a CONTRATADA pela presta��o dos Servi�os os valores acordados e estipulados no Anexo III � Da Remunera��o; e</p>

			<p>(b)	n�o deslocar os funcion�rios da CONTRATADA para execu��o de servi�os fora do local estabelecido no presente Contrato sem o consentimento expresso e pr�vio da CONTRATADA.</p>

        </li>
    </ul>
    
    <h3>6.	REMUNERA��O</h3>
    
    <ul>
    	<li>
        <p>6.1	Os pre�os dos Servi�os, durante a vig�ncia deste instrumento, ser�o aqueles indicados no Anexo III � Da Remunera��o, inclusos todos os Tributos incidentes e decorrentes deste Contrato.</p>
     	 <p> 6.2	Os pre�os dos Servi�os conforme mencionado no Anexo III baseiam-se na legisla��o vigente na data da assinatura deste instrumento, computando todos os tributos incidentes � �poca. Fica expressamente estabelecido que quaisquer aumentos, redu��es, modifica��es, cria��es, extin��es ou isen��es de tributos, decorrentes de altera��es introduzidas na legisla��o federal, estadual ou municipal, a partir da data da assinatura deste instrumento e durante o per�odo de sua vig�ncia, desde que acordado entre CONTRATANTE e CONTRATADA, poder�o ensejar uma majora��o ou redu��o proporcional dos pre�os, a partir da data da vig�ncia das novas disposi��es legais. Os eventuais ajustes de pre�os visar�o sempre restabelecer o equil�brio econ�mico-financeiro do contrato, tomando-se por base a data de sua assinatura.</p>

		<p>6.3	O prazo para pagamento das faturas pela CONTRATANTE � de 30 (trinta) dias, a contar da emiss�o da fatura pela CONTRATADA, contanto que tal fatura seja encaminhada � CONTRATANTE em at� 10 (dez) dias contados da sua emiss�o.</p>

		<p>6.4	A comprova��o da ocorr�ncia dos eventos geradores de pagamento dever� ser efetuada atrav�s da apresenta��o dos documentos que demonstrem a efetiva realiza��o da presta��o dos Servi�os em conformidade com o disposto neste instrumento, devidamente aprovados pela CONTRATANTE.</p>

		<p>6.5	Na hip�tese de atraso injustificado pela CONTRATANTE no pagamento das faturas emitidas pela CONTRATADA, poder� esta �ltima cobrar multa � taxa de 2% (dois por cento) bem como juros de 1% ao m�s "pro rata die" at� o efetivo pagamento dos valores em atraso.</p>

		<p>6.6	A CONTRATANTE n�o ser� respons�vel por qualquer outro pagamento alheio aos valores constantes do Anexo III, ou a qualquer outro t�tulo, por for�a do aqui disposto.</p>
        </li>
    </ul>
    
    <h3>7.	RESCIS�O</h3>
    <ul>	
    	<li>7.1	� facultado a qualquer das Partes contratantes declarar rescindido o presente Contrato, por justa causa, mediante aviso escrito � outra Parte, na ocorr�ncia de qualquer dos seguintes eventos:</li>
	        <ul>
	        	<li>
                
             
	            	<p>(a)	em caso de viola��o de quaisquer termos e condi��es aqui contidos, se a Parte infratora (que deu causa � viola��o) n�o san�-la dentro do per�odo de 15 (quinze) dias corridos a contar de notifica��o escrita da outra Parte nesse sentido;</p> 
	                        <p align="right" style="font-weight:bold;">3/5</p> 
                
                     
      
                     
                     
                     <p>(b)	em caso de fal�ncia, recupera��o judicial, extrajudicial ou liquida��o de qualquer das Partes; e</p>
	
  
    
					<p>(c)	em caso de for�a maior, se esta estender-se por per�odo superior a 60 (sessenta) dias a contar da apresenta��o de aviso escrito acerca desse evento.</p> 
	            </li>
	        </ul>
            
            
        </li>
        
        <li>
      	 <p> 7.2	N�o obstante o disposto acima, ficar� facultado � CONTRATANTE declarar rescindido o presente Contrato, a seu exclusivo crit�rio, mediante aviso escrito nesse sentido, enviado � CONTRATADA com 30 (trinta) dias de anteced�ncia, sem que caibam qualquer indeniza��o � CONTRATADA. Ser�o quitados apenas os valores referentes a Servi�os j� prestados, observadas todas as disposi��es deste Contrato.</p>
         
         <p>7.3	Rescindido o presente Contrato por qualquer motivo, incumbe � CONTRATADA, se assim o exigir a CONTRATANTE, a ado��o de todas as provid�ncias que venham a ser necess�rias para que os Servi�os continuem a ser disponibilizados � CONTRATANTE por outro prestador que esta indicar, assim que vi�vel e com a menor interrup��o poss�vel nos neg�cios da CONTRATANTE. </p>

		 <p>7.4	A rescis�o do presente Contrato n�o afetar� os direitos ou responsabilidades de quaisquer das Partes contratantes se constitu�dos antes de tal expira��o ou rescis�o, nem afetar� as demais disposi��es cuja subsist�ncia seja aqui expressamente prevista ou implicitamente necess�ria. </p>
        </li>       
        
    </ul>
    
    <h3>8.	SIGILO E DIVULGA��O DE INFORMA��ES</h3>
    <ul>
    	<li>
        <p>8.1	As Partes comprometem-se, individualmente, a abster-se de divulgar e tomar todas as provid�ncias razoavelmente necess�rias para impedir a que seus diretores, representantes, empregados e agentes que divulguem, direta ou indiretamente, informa��es relacionadas aos neg�cios, atividades ou m�todos operacionais da outra Parte, salvo em atendimento a disposi��o legal nesse sentido (e, nesses casos, restringindo-se � exata medida exigida por lei). </p>
        </li>
    </ul>
    
    
     <h3>9.	DECLARA��ES COMPLEMENTARES</h3>
    <ul>
    	<li>
        <p>9.1	A CONTRATADA compromete-se a celebrar e formalizar todos os instrumentos, registros, certifica��es e demais documentos, bem como praticar todos os atos (e instar terceiros a tamb�m assim proceder, dentro de sua compet�ncia), conforme eventualmente necess�rio para levar a pleno vigor e efeito os prop�sitos do presente Contrato.  </p>
        <p>9.2	A CONTRATADA neste ato declara � CONTRATANTE expressamente possuir todas as licen�as, inscri��es, certifica��es, registros e tudo o mais exigido pela Lei Aplic�vel � plena, completa e satisfat�ria presta��o dos Servi�os, isentando a CONTRATANTE por qualquer questionamento, danos, indeniza��es, multas, preju�zos e/ou pagamentos efetuados pelas as autoridades ou �rg�o competentes relacionados � presta��o dos Servi�os.</p>
        
        <p>9.3	As partes se comprometem a respeitar e observar as condi��es comerciais espec�ficas estabelecidas no<strong> Anexo IV � Das Condi��es Comerciais.</strong></p>       
        
        </li>
    </ul>
    
    
    <h3>10.	DISPOSI��ES GERAIS</h3>
    
    <ul>
    	<li>
        <p>10.1	A CONTRATADA declara e reconhece que a CONTRATANTE � uma associa��o civil sem fins lucrativos e que tem por finalidade a elabora��o e implanta��o de projetos na �rea de educa��o, sa�de, meio ambiente e assist�ncia social, que visem � forma��o, habilita��o, capacita��o e qualifica��o profissional de jovens, adultos e de profissionais.</p>

		<p>10.2	� vedado �s Partes ceder, transferir ou onerar quaisquer de seus direitos ou obriga��es oriundos do presente Contrato, salvo mediante o consentimento pr�vio e por escrito da outra Parte. </p>

		<p>10.3	Fica desde j� facultado � CONTRATADA subcontratar quaisquer obriga��es por ela ora assumidas, se houver motivos para crer que tais obriga��es ser�o cumpridas em estrita observ�ncia aos crit�rios aqui previstos, mediante pr�vio consentimento da CONTRATANTE. </p>
        </li>
        <ul>
        	<li>
            <p>10.3.1	Na hip�tese de subcontrata��o das obriga��es ora atinentes � CONTRATADA: </p>

			<p>(a)	a CONTRATADA obter� do subcontratado seu compromisso em cumprir as obriga��es que seriam de outra forma exig�veis da pr�pria CONTRATADA, caso as correspondentes atividades n�o tivessem sido por ela subcontratadas;</p>

			<p>(b)	a CONTRATADA providenciar� para que a CONTRATANTE possua direito de a��o diretamente contra o subcontratado, caso este n�o cumpra suas obriga��es perante a CONTRATADA (e, se poss�vel, fazer com que a CONTRATANTE seja parte integrante de eventual instrumento de subcontrata��o); e</p>

			<p>(c)	a CONTRATADA permanecer� respons�vel por quaisquer atos ou omiss�es do subcontratado, como se tais atos ou omiss�es lhe fossem diretamente atribu�veis nos termos do presente Contrato.</p>

            </li>
        </ul>
        
       
         <p align="right" style="font-weight:bold;">4/5</p> 
        <p>10.4	O presente Contrato constitui o acordo integral entre as Partes relativamente � presta��o dos Servi�os, substituindo quaisquer outros acordos anteriormente firmados entre as Partes no que tange ao objeto do presente instrumento. No caso de diverg�ncias entre o disposto neste instrumento e em qualquer de seus Anexos, prevalecer�o as disposi��o deste instrumento sobre aquelas contidas nos Anexos.</p>
        
        
           
        
       <p>10.5	Qualquer altera��o �s disposi��es aqui contidas apenas ser� v�lida quando efetuada por escrito e assinada pelas Partes contratantes. Para os fins ora propostos, entende-se por "altera��o" qualquer altera��o, aditivo, cancelamento ou substitui��o das disposi��es aqui contidas, a qualquer t�tulo. </p>

 		<p>10.6	A omiss�o ou o n�o exerc�cio tempestivo de quaisquer direitos, poderes ou privil�gios ora atribu�veis � CONTRATANTE ou � CONTRATADA n�o prejudicar� o seu posterior exerc�cio nem constituir� uma ren�ncia aos mesmos; da mesma forma, o exerc�cio individual ou parcial de tais direitos, poderes ou privil�gios, bem como a omiss�o nesse sentido, em qualquer circunst�ncia, n�o impedir� o exerc�cio ulterior desses ou outros direitos, poderes ou privil�gios, sob qualquer pretexto. </p>

		 <p>10.7	Se qualquer disposi��o ora aven�ada for considerada ilegal, inv�lida ou inexeq��vel, no todo ou em parte, o presente Contrato permanecer� v�lido e eficaz com as demais disposi��es nele contidas e, ainda, com a parcela das cl�usulas que n�o tenham sido assim prejudicadas. </p>

		 <p>10.8	Quaisquer avisos, notifica��es ou outros comunicados consoante os termos aqui dispostos ser�o realizados por escrito e entregues em m�os, por carta registrada, ou por transmiss�o fac-s�mile, enviados aos endere�os da CONTRATANTE e da CONTRATADA indicados no pre�mbulo do presente Contrato, ou a outro endere�o que a Parte destinat�ria venha a previamente comunicar � outra. </p>

		 <p>10.9	Este Contrato obriga as partes e seus sucessores a qualquer t�tulo.</p>

		 <p>10.10	Os signat�rios deste Contrato declaram, sob as penas da lei, que se encontram investidos dos competentes poderes de ordem legal e societ�ria para representar e assinar o presente instrumento, motivo pelo qual assegurar�o, em qualquer hip�tese e situa��o, a veracidade da presente declara��o. </p>
        
    </ul>
    
    <h3>11.	JURISDI��O E FORO</h3>
    <ul>
    	<li>
       <p>11.1	Fica eleito o foro da Comarca da Capital do Estado do Rio de Janeiro para dirimir qualquer lit�gio oriundo do presente instrumento com a rejei��o de qualquer outro por mais privilegiado que seja.</p>

		<p>E por estarem assim justas e contratadas as partes, firmam o presente instrumento em 3 (tr�s) vias de igual teor e forma, na presen�a de duas testemunhas.</p>        

        
        </li>
    </ul>
    
    
    <p style="margin-top:40px;text-align:center;">
   	
		<?php
		$qr_regiao = mysql_query("SELECT * FROM regioes WHERER id_regiao = '$row_prestador[id_regiao]'");
		$row_master = mysql_fetch_assoc($qr_regiao);
		
		
		
		$qr_master = mysql_query("SELECT * FROM master WHERE id_master= '$row_regiao[id_master]'");
		$row_master = mysql_fetch_assoc($qr_projeto);
		
		
		echo  $row_master['municipio'].', '.date('d').' de '.date('m').' de '.date('Y');	?> 
        
    </p>    
  



<table width="100%" style="margin-top:70px;">
	<tr>
    	<td align="center"><strong>CONTRATANTE</strong></td>
        <td align="center"><strong>CONTRATADA</strong></td>
    </tr>
    <tr>
    	<td><?php 
		$nomEmp= new empresa();
		echo  $nomEmp -> nomeEmpresa();?></td>
        <td><?php echo $row_prestador['c_razao'];?></td>
    </tr>
</table>

<table width="100%" style="margin-top:70px;">
	<tr>
		<td>____________________________________</td>
		<td>____________________________________</td>
	</tr>
	<tr>
		<td>Nome:</td>
		<td>Nome:</td>
	</tr>
	<tr>
		<td>RG:</td>
		<td>RG:</td>
	</tr>
</table>



 <p align="right" style="font-weight:bold;margin-top:100px;">5/5</p> 
 </td>
  </tr>
 </table> 
</body>

</html>

<?php

if($row_prestador['imprimir'] == "3"){

$data_b = date("Y-m-d");

$id_user = $_COOKIE['logado'];



//mysql_query("UPDATE prestadorservico SET imprimir = '4', contratado_por = '$id_user', contratado_em = '$data_b', acompanhamento = '3' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>

