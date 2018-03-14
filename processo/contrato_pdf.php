<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../conn.php";



$regiao = $_REQUEST['regiao'];

$id_prestador = $_REQUEST['prestador'];



$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "1"){

print "

<script>

alert(\"Você não pode imprimir este CONTRATO DE PRESTAÇÃO DE SERVIÇOS sem ter feito a ABERTURA DE PROCESSO!\");

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
p{ text-align:justify
	}


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



<p> Pelo presente instrumento particular de prestação de serviços, de um lado <strong><?php echo $row_prestador['contratante']?></strong>, associação civil sem fins lucrativos, com sede <strong><?=$row_prestador['endereco']?></strong>, inscrita no CNPJ/MF sob o nº <?=$row_prestador['cnpj']?>, neste ato por seu representante legal, <strong><?=$row_prestador['responsavel']?></strong>, <strong><?=$row_prestador['nacionalidade']?></strong>,   <strong><?=$row_prestador['civil']?>, <?=$row_prestador['formacao']?></strong>, portador da Cédula de Identidade n.º <strong><?=$row_prestador['rg']?></strong>, inscrito no CPF/MF sob o n.º <strong>
<strong><?=$row_prestador['cpf']?></strong>
</strong>, doravante denominada CONTRATANTE; e, de outro lado, <strong><?=$row_prestador['c_razao']?></strong>, sociedade simples, com sede na <strong><?=$row_prestador['c_endereco']?>.</strong>, inscrita no  CNPJ sob n.º <strong><?=$row_prestador['c_cnpj']?></strong>, neste ato representada pelo(a) <strong><?=$row_prestador['c_responsavel']?></strong>, <strong><?=$row_prestador['c_nacionalidade']?></strong>,  <strong><?=$row_prestador['c_civil']?></strong>, <strong>
<?=$row_prestador['cpf']?>
</strong>, portador da cédula de identidade RG n.º <strong><?=$row_prestador['c_rg']?></strong>, inscrito no CPF/MF sob n.º 
<strong><?=$row_prestador['c_cpf']?></strong>, doravante denominada <strong>CONTRATADA</strong>, tem justo e contratado o seguinte:</p>



<h3>1.	INTERPRETAÇÃO</h3>

<ul>
 <li> <p>1.1	Para os fins do presente Contrato:</p>
 	  <p><strong>"Lei Aplicável"</strong> significa, em relação a qualquer procedimento, obrigação, responsabilidade e/ou circunstância, todas as leis, normas e padrões estabelecidos por órgão governamental ou regulador e geralmente adotados para o setor, bem como os princípios de auto-regulamentação regionais, nacionais ou internacionais, se aplicáveis a tais procedimentos, obrigações, responsabilidades e/ou circunstâncias;</p>

		<p><strong>"Dia Útil" </strong>significa todos os dias da semana, exceto sábado e domingo, nos quais operem os bancos em Rio de Janeiro ou na região objeto da prestação de serviços;</p>

		<p><strong>"Remuneração"</strong> significa os valores devidos à <strong>CONTRATADA</strong> pela <strong>CONTRATANTE</strong>, de acordo com o disposto na Cláusula 6;</p>

		<p><strong>"Boas Práticas"</strong> significa, em relação a determinado procedimento e sob qualquer circunstância, o exercício da boa técnica, diligência, prudência, experiência, perícia, previsibilidade e critério de decisão e julgamento que se esperaria de uma pessoa hábil e experiente no que tange ao cumprimento da Lei Aplicável em tais circunstâncias ou em eventos semelhantes;</p>

		<p><strong>"Padrão Adequado" </strong>significa, em respeito aos procedimentos para a prestação de Serviços em qualquer circunstância, o padrão vislumbrado para a prestação desses Serviços, tendo-se utilizado as Boas Práticas com esse propósito específico;</p>

		<p><strong>"Tributo" </strong>significa qualquer tributo, o que inclui, entre outros, impostos, taxas, tarifas, contribuições sociais e outras exações, bem como todas as respectivas retenções ou deduções, acrescidas de quaisquer multas, penalidades ou juros devidos por inadimplência ou atraso no pagamento;
		</p>
        </li>
      <li>
      	<p>1.2	Ademais, no que tange ao presente Contrato:</p>
        <ul>
       		 <li>
		        <p>(a)	as referências ao presente instrumento ou a qualquer outro documento correlato aludirão ao presente Contrato, suas premissas e anexos, ou a outro documento aplicável, conforme eventualmente aditado e/ou alterado, sob qualquer pretexto;</p>
		        
		        <p>(b)	as referências a qualquer das partes abrangerão e aludirão, no que for aplicável, a seus respectivos sucessores legais, cessionários ou beneficiários, conforme o caso;</p>
		        
		        <p>(c)	as referências a premissas, cláusulas, Anexos e seus dispositivos aludirão àqueles aqui contidos, respectivamente;</p>
		        
		        <p> (d)	as referências a determinada legislação aludirão também a eventuais aditivos, alterações ou prorrogações da mesma, assim como a qualquer legislação a ela subordinada;</p>
		
				<p>(e)	as referências a "pessoa" abrangerão qualquer pessoa física ou jurídica, sociedade por ações ou quotas, sociedade em comandita, joint venture, associação, organização, instituição, trust ou repartição, com ou sem personalidade jurídica distinta;</p>

				<p>&nbsp;</p>
                
                      <table width="100%"><tr><td align="right"><b>1/5</b></td></tr></table> 

                
      <p>(f)	as referências a determinado gênero estendem-se a todos os gêneros, ao passo que as referências no singular estendem-se ao plural e vice-versa; e os apenas para conveniência, não devendo ser considerados na interpretação do presente Contrato.</p>
                
    
               </li>
         </ul> 
      </li>      
      </ul>
      
      <h3>2.	PRAZO DE VIGÊNCIA</h3>
      <ul>
      	<li>
        	 <p>2.1	O presente Contrato entrará em pleno vigor na data de sua assinatura, assim permanecendo pelo prazo estabelecido no <strong>Anexo I – Do Prazo de Vigência</strong>.</p>
             <p>2.2	O presente Contrato poderá ser renovado por períodos iguais e sucessivos mediante acordo por escrito entre as partes.</p>
                         
        </li>
      </ul>
      
    <h3>SERVIÇOS</h3>
    <ul> 
    	<li>
        	<p>3.1	A <strong>CONTRATADA</strong> prestará serviços descritos no <strong>Anexo II – Dos Serviços </strong>do presente Contrato, doravante denominados <strong>"Serviços"</strong>, observadas as Boas Práticas e, ainda, as disposições contidas no presente instrumento e os termos da Lei Aplicável.</p>
            <ul>
            	<li>
           			 <p>3.1.1	A prestação dos Serviços ora avençada não tem sob forma nenhuma ou sob qualquer pretexto caráter de exclusividade, podendo a <strong>CONTRATANTE</strong> utilizar-se de outras prestadoras de serviços a seu exclusivo critério.</p>
                </li>               
                
            </ul>
        </li>
        <li>
        <p>3.2	Sem prejuízo às demais obrigações assumidas pela <strong>CONTRATADA</strong> neste instrumento, esta prestará os Serviços em atendimento ao Padrão Adequado, devotando-lhes os mais altos padrões de técnica, zelo, especificações, padrões e critérios de qualidade, prioridade, atenção e tempestividade. </p>

		<p>3.3	A <strong>CONTRATADA</strong> fará com que as obrigações por ela ora assumidas sejam atendidas por uma equipe suficiente (com número de integrantes adequado) e devidamente qualificada, treinada e capacitada para esse propósito específico.</p>

		<p>3.4	A <strong>CONTRATADA</strong> abster-se-á da prática de atos que possam ocasionar qualquer espécie de dano ou prejuízo aos negócios ou à reputação da <strong>CONTRATANTE</strong> e/ou seus administradores.</p>

        </li>
    </ul>
    
    <h3>4.	OBRIGAÇÕES DA CONTRATADA</h3>
    
    <ul>	
    	<li>
        <p>4.1	Além das obrigações assumidas em outras cláusulas do Contrato, a <strong>CONTRATADA</strong>, neste ato, compromete-se e obriga-se a:</p>
        
        <ul>
        	<li>
            <p>(a)	fornecer mão-de-obra especializada, altamente qualificada, bem como todos equipamentos e materiais necessários para a realização dos Serviços;

</p>
			<p>(b)	prestar os Serviços de acordo com as melhores técnicas profissionais e dentro dos mais elevados padrões de conduta ética, moral e profissional, conforme políticas adotadas pela CONTRATANTE, obedecendo todas as normas técnicas pertinentes aos Serviços, bem como as normas de segurança do trabalho, assumindo, neste ato, total e integral responsabilidade pelos Serviços;</p>

			<p>(c)	responsabilizar-se integralmente pela conduta, freqüência e pontualidade de seus funcionários envolvidos diretamente na execução dos Serviços, podendo a <strong>CONTRATANTE</strong> exigir substituições de funcionários a seu exclusivo critério;	</p>

			<p>(d)	afastar ou substituir o seu funcionário, cuja conduta ou presença seja considerada pela <strong>CONTRATANTE</strong> inconveniente, imediatamente após o recebimento de comunicação da <strong>CONTRATANTE</strong>, devendo providenciar sua imediata substituição por outro profissional devidamente qualificado e capacitado para a prestação dos Serviços;</p>

			<p>(e)	indenizar a <strong>CONTRATANTE</strong> por quaisquer danos ou prejuízos e responsabilizar-se integralmente por, mas não se limitando a, quaisquer obrigações e indenizações, perdas e danos, lucros cessantes, prejuízos de quaisquer espécies, ou sob quaisquer títulos, perdas de negócios, perda, avaria, danificação parcial ou total ou extravio de mercadorias, produtos, equipamentos, documentos, defeitos, ou quaisquer outros danos diretos, indiretos, acidentais, especiais, conseqüenciais ou punitivos, decorrentes direta ou indiretamente, da prestação dos Serviços; </p>
            
            
           <p> (f)	não fazer uso do nome, marca ou qualquer outra propriedade intelectual da CONTRATANTE em qualquer material de divulgação, promoção ou propaganda pessoal ou de terceiros, salvo mediante expressa autorização;</p> 

			<p>(g)	prestar contas à <strong>CONTRATANTE</strong> das atividades desenvolvidas na prestação dos Serviços, sempre que solicitado pela CONTRATANTE; </p>
			
           <table width="100%"><tr><td align="right"><b>2/5</b></td></tr></table> 


			<p>(h)	efetuar os competentes seguros de vida, objetivando a integral cobertura securitária na ocorrência de um eventual sinistro envolvendo seus empregados e/ou prepostos alocados para a prestação dos Serviços;
</p>

			<p>(i)	comprovar o atendimento de todas as exigências legais trabalhistas, previdenciárias, securitárias ou de outra natureza com relação aos funcionários que prestarão os Serviços, sempre que requisitado pela <strong>CONTRATANTE</strong>;</p>

			<p>(j)	responsabilizar-se moral e materialmente pelos seus empregados, prepostos e/ou terceiros sob sua responsabilidade.</p>
            </li>
        </ul>        
        </li>
        
        <li>
        	<p>4.2	Todos e quaisquer encargos decorrentes de condenação judicial, sejam trabalhistas, previdenciários e/ou fundiários, pertinentes aos profissionais que venham a ser indicados pela CONTRATADA para execução dos Serviços, serão de exclusiva responsabilidade da <strong>CONTRATADA</strong>, não respondendo a <strong>CONTRATANTE</strong> por tais encargos, sequer em caráter subsidiário, ficando certo que, entre o pessoal da <strong>CONTRATADA</strong> e a <strong>CONTRATANTE</strong> não há e não haverá nenhuma relação ou vínculo trabalhista.</p>

			<p>4.3	A <strong>CONTRATADA</strong> garante, desde já, os Serviços por ela prestados em razão deste contrato, quanto à qualidade, desempenho e funcionalidade, bem como contra todo e qualquer defeito, obrigando-se desde já a refazer, reparar ou repor toda a execução dos Serviços, assim como danos e prejuízos deles decorrentes.</p>
        </li>
        
    </ul>
    
    
    <h3>5.	OBRIGAÇÕES DA CONTRATANTE</h3>
    
    <ul>
    	<li>
        	<p>(a)	pagar a CONTRATADA pela prestação dos Serviços os valores acordados e estipulados no Anexo III – Da Remuneração; e</p>

			<p>(b)	não deslocar os funcionários da <strong>CONTRATADA</strong> para execução de serviços fora do local estabelecido no presente Contrato sem o consentimento expresso e prévio da <strong>CONTRATADA</strong>.</p>

        </li>
    </ul>
    
    <h3>6.	REMUNERAÇÃO</h3>
    
    <ul>
    	<li>
        <p>6.1	Os preços dos Serviços, durante a vigência deste instrumento, serão aqueles indicados no Anexo III – Da Remuneração, inclusos todos os Tributos incidentes e decorrentes deste Contrato.</p>
     	 <p> 6.2	Os preços dos Serviços conforme mencionado no Anexo III baseiam-se na legislação vigente na data da assinatura deste instrumento, computando todos os tributos incidentes à época. Fica expressamente estabelecido que quaisquer aumentos, reduções, modificações, criações, extinções ou isenções de tributos, decorrentes de alterações introduzidas na legislação federal, estadual ou municipal, a partir da data da assinatura deste instrumento e durante o período de sua vigência, desde que acordado entre <strong>CONTRATANTE</strong> e <strong>CONTRATADA</strong>, poderão ensejar uma majoração ou redução proporcional dos preços, a partir da data da vigência das novas disposições legais. Os eventuais ajustes de preços visarão sempre restabelecer o equilíbrio econômico-financeiro do contrato, tomando-se por base a data de sua assinatura.</p>

		<p>6.3	O prazo para pagamento das faturas pela <strong>CONTRATANTE</strong> é de 30 (trinta) dias, a contar da emissão da fatura pela <strong>CONTRATADA</strong>, contanto que tal fatura seja encaminhada à <strong>CONTRATANTE</strong> em até 10 (dez) dias contados da sua emissão.</p>

		<p>6.4	A comprovação da ocorrência dos eventos geradores de pagamento deverá ser efetuada através da apresentação dos documentos que demonstrem a efetiva realização da prestação dos Serviços em conformidade com o disposto neste instrumento, devidamente aprovados pela <strong>CONTRATANTE</strong>.</p>

		<p>6.5	Na hipótese de atraso injustificado pela <strong>CONTRATANTE</strong> no pagamento das faturas emitidas pela <strong>CONTRATADA</strong>, poderá esta última cobrar multa à taxa de 2% (dois por cento) bem como juros de 1% ao mês "pro rata die" até o efetivo pagamento dos valores em atraso.</p>

		<p>6.6	A <strong>CONTRATANTE</strong> não será responsável por qualquer outro pagamento alheio aos valores constantes do Anexo III, ou a qualquer outro título, por força do aqui disposto.</p>
        </li>
    </ul>
    
    <h3>7.	RESCISÃO</h3>
    <ul>	
    	<li>7.1	É facultado a qualquer das Partes contratantes declarar rescindido o presente Contrato, por justa causa, mediante aviso escrito à outra Parte, na ocorrência de qualquer dos seguintes eventos:</li>
	        <ul>
	        	<li>
                
             
	            	<p>(a)	em caso de violação de quaisquer termos e condições aqui contidos, se a Parte infratora (que deu causa à violação) não saná-la dentro do período de 15 (quinze) dias corridos a contar de notificação escrita da outra Parte nesse sentido;</p> 
                
                     
                         
                     
                     <p>(b)	em caso de falência, recuperação judicial, extrajudicial ou liquidação de qualquer das Partes; e</p>
                     <p>&nbsp;</p>
	
             <table width="100%"><tr><td align="right"><b>3/5</b></td></tr></table> 

    
					<p>(c)	em caso de força maior, se esta estender-se por período superior a 60 (sessenta) dias a contar da apresentação de aviso escrito acerca desse evento.</p> 
	            </li>
	        </ul>
            
            
        </li>
        
        <li>
      	 <p> 7.2	Não obstante o disposto acima, ficará facultado à <strong>CONTRATANTE</strong> declarar rescindido o presente Contrato, a seu exclusivo critério, mediante aviso escrito nesse sentido, enviado à CONTRATADA com 30 (trinta) dias de antecedência, sem que caibam qualquer indenização à CONTRATADA. Serão quitados apenas os valores referentes a Serviços já prestados, observadas todas as disposições deste Contrato.</p>
         
         <p>7.3	Rescindido o presente Contrato por qualquer motivo, incumbe à <strong>CONTRATADA</strong>, se assim o exigir a <strong>CONTRATANTE</strong>, a adoção de todas as providências que venham a ser necessárias para que os Serviços continuem a ser disponibilizados à <strong>CONTRATANTE</strong> por outro prestador que esta indicar, assim que viável e com a menor interrupção possível nos negócios da <strong>CONTRATANTE</strong>. </p>

		 <p>7.4	A rescisão do presente Contrato não afetará os direitos ou responsabilidades de quaisquer das Partes contratantes se constituídos antes de tal expiração ou rescisão, nem afetará as demais disposições cuja subsistência seja aqui expressamente prevista ou implicitamente necessária. </p>
        </li>       
        
    </ul>
    
    <h3>8.	SIGILO E DIVULGAÇÃO DE INFORMAÇÕES</h3>
    <ul>
    	<li>
        <p>8.1	As Partes comprometem-se, individualmente, a abster-se de divulgar e tomar todas as providências razoavelmente necessárias para impedir a que seus diretores, representantes, empregados e agentes que divulguem, direta ou indiretamente, informações relacionadas aos negócios, atividades ou métodos operacionais da outra Parte, salvo em atendimento a disposição legal nesse sentido (e, nesses casos, restringindo-se à exata medida exigida por lei). </p>
        </li>
    </ul>
    
    
     <h3>9.	DECLARAÇÕES COMPLEMENTARES</h3>
    <ul>
    	<li>
        <p>9.1	A <strong>CONTRATADA</strong> compromete-se a celebrar e formalizar todos os instrumentos, registros, certificações e demais documentos, bem como praticar todos os atos (e instar terceiros a também assim proceder, dentro de sua competência), conforme eventualmente necessário para levar a pleno vigor e efeito os propósitos do presente Contrato.  </p>
        <p>9.2	A <strong>CONTRATADA</strong> neste ato declara à <strong>CONTRATANTE</strong> expressamente possuir todas as licenças, inscrições, certificações, registros e tudo o mais exigido pela Lei Aplicável à plena, completa e satisfatória prestação dos Serviços, isentando a <strong>CONTRATANTE</strong> por qualquer questionamento, danos, indenizações, multas, prejuízos e/ou pagamentos efetuados pelas as autoridades ou órgão competentes relacionados à prestação dos Serviços.</p>
        
        <p>9.3	As partes se comprometem a respeitar e observar as condições comerciais específicas estabelecidas no<strong> Anexo IV – Das Condições Comerciais.</strong></p>       
        
        </li>
    </ul>
    
    
    <h3>10.	DISPOSIÇÕES GERAIS</h3>
    
    <ul>
    	<li>
        <p>10.1	A <strong>CONTRATADA</strong> declara e reconhece que a <strong>CONTRATANTE</strong> é uma associação civil sem fins lucrativos e que tem por finalidade a elaboração e implantação de projetos na área de educação, saúde, meio ambiente e assistência social, que visem à formação, habilitação, capacitação e qualificação profissional de jovens, adultos e de profissionais.</p>

		<p>10.2	É vedado às Partes ceder, transferir ou onerar quaisquer de seus direitos ou obrigações oriundos do presente Contrato, salvo mediante o consentimento prévio e por escrito da outra Parte. </p>

		<p>10.3	Fica desde já facultado à CONTRATADA subcontratar quaisquer obrigações por ela ora assumidas, se houver motivos para crer que tais obrigações serão cumpridas em estrita observância aos critérios aqui previstos, mediante prévio consentimento da <strong>CONTRATANTE</strong>. </p>
        </li>
        <ul>
        	<li>
            <p>10.3.1	Na hipótese de subcontratação das obrigações ora atinentes à <strong>CONTRATADA</strong>: </p>

			<p>(a)	a <strong>CONTRATADA</strong> obterá do subcontratado seu compromisso em cumprir as obrigações que seriam de outra forma exigíveis da própria <strong>CONTRATADA</strong>, caso as correspondentes atividades não tivessem sido por ela subcontratadas;</p>

			<p>(b)	a <strong>CONTRATADA</strong> providenciará para que a <strong>CONTRATANTE</strong> possua direito de ação diretamente contra o subcontratado, caso este não cumpra suas obrigações perante a <strong>CONTRATADA</strong> (e, se possível, fazer com que a CONTRATANTE seja parte integrante de eventual instrumento de subcontratação); e</p>

			<p>(c)	a <strong>CONTRATADA</strong> permanecerá responsável por quaisquer atos ou omissões do subcontratado, como se tais atos ou omissões lhe fossem diretamente atribuíveis nos termos do presente Contrato.</p>

            </li>
        </ul>
        
       
             <table width="100%"><tr><td align="right"><b>4/5</b></td></tr></table> 
             
             <p>&nbsp;</p>

        <p>10.4	O presente Contrato constitui o acordo integral entre as Partes relativamente à prestação dos Serviços, substituindo quaisquer outros acordos anteriormente firmados entre as Partes no que tange ao objeto do presente instrumento. No caso de divergências entre o disposto neste instrumento e em qualquer de seus Anexos, prevalecerão as disposição deste instrumento sobre aquelas contidas nos Anexos.</p>
        
        
           
        
       <p>10.5	Qualquer alteração às disposições aqui contidas apenas será válida quando efetuada por escrito e assinada pelas Partes contratantes. Para os fins ora propostos, entende-se por "alteração" qualquer alteração, aditivo, cancelamento ou substituição das disposições aqui contidas, a qualquer título. </p>

 		<p>10.6	A omissão ou o não exercício tempestivo de quaisquer direitos, poderes ou privilégios ora atribuíveis à CONTRATANTE ou à CONTRATADA não prejudicará o seu posterior exercício nem constituirá uma renúncia aos mesmos; da mesma forma, o exercício individual ou parcial de tais direitos, poderes ou privilégios, bem como a omissão nesse sentido, em qualquer circunstância, não impedirá o exercício ulterior desses ou outros direitos, poderes ou privilégios, sob qualquer pretexto. </p>

		 <p>10.7	Se qualquer disposição ora avençada for considerada ilegal, inválida ou inexeqüível, no todo ou em parte, o presente Contrato permanecerá válido e eficaz com as demais disposições nele contidas e, ainda, com a parcela das cláusulas que não tenham sido assim prejudicadas. </p>

		 <p>10.8	Quaisquer avisos, notificações ou outros comunicados consoante os termos aqui dispostos serão realizados por escrito e entregues em mãos, por carta registrada, ou por transmissão fac-símile, enviados aos endereços da <strong>CONTRATANTE</strong> e da <strong>CONTRATADA</strong> indicados no preâmbulo do presente Contrato, ou a outro endereço que a Parte destinatária venha a previamente comunicar à outra. </p>

		 <p>10.9	Este Contrato obriga as partes e seus sucessores a qualquer título.</p>

		 <p>10.10	Os signatários deste Contrato declaram, sob as penas da lei, que se encontram investidos dos competentes poderes de ordem legal e societária para representar e assinar o presente instrumento, motivo pelo qual assegurarão, em qualquer hipótese e situação, a veracidade da presente declaração. </p>
        
    </ul>
    
    <h3>11.	JURISDIÇÃO E FORO</h3>
    <ul>
    	<li>
       <p>11.1	Fica eleito o foro da Comarca da Capital do Estado do Rio de Janeiro para dirimir qualquer litígio oriundo do presente instrumento com a rejeição de qualquer outro por mais privilegiado que seja.</p>

		<p>E por estarem assim justas e contratadas as partes, firmam o presente instrumento em 3 (três) vias de igual teor e forma, na presença de duas testemunhas.</p>        

        
        </li>
    </ul>
    
    
    <p style="margin-top:40px;text-align:center;">
   	
	<?php
		$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_prestador[id_regiao]'");
		$row_regiao = mysql_fetch_assoc($qr_regiao);
		
	$meses = array("janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
	$mes = str_replace("0","",date('m')) - 1;
	
		
		
		$qr_master = mysql_query("SELECT * FROM master WHERE id_master= ".$nomEmp ->id_user);
		$row_master = mysql_fetch_assoc($qr_master);
		
		
		echo  $row_master['municipio'].', '.date('d').' de '.$meses[$mes].' de '.date('Y');	?> 
        
    </p>    
  



<table width="100%" style="margin-top:70px;">
	<tr>
    	<td align="center"><strong>CONTRATANTE</strong></td>
        <td align="center"><strong>CONTRATADA</strong></td>
    </tr>
    <tr>
    	<td align="center"><?php 
		$nomEmp= new empresa();
		echo  $nomEmp -> nomeEmpresa();?></td>
        <td align="center"><?php echo $row_prestador['c_razao'];?></td>
    </tr>
</table>

<table width="100%" style="margin-top:70px;">
	<tr>
		<td align="center">________________________________________</td>
		<td align="center">________________________________________</td>
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


<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
             <table width="100%"><tr><td align="right"><b>5/5</b></td></tr></table> 
 </td>
  </tr>
 </table> 
</body>

</html>

<?php

if($row_prestador['imprimir'] == "1"){

$data_b = date("Y-m-d");

$id_user = $_COOKIE['logado'];



mysql_query("UPDATE prestadorservico SET imprimir = '2', contratado_por = '$id_user', contratado_em = '$data_b', acompanhamento = '3' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>

