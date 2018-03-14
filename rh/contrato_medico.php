<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

if(isset($_GET['clt'])){

$id_trabalhador = $_GET['clt'];
$qr_trab = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_trabalhador'");

	
} else if(isset($_GET['autonomo'])) {
	
$id_trabalhador = $_GET['autonomo'];
$qr_trab = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_trabalhador'");	
	
}


$row_trab = mysql_fetch_assoc($qr_trab);

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_funcionario);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_func[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>

<style >
body{
	font-weight:430;	
	text-transform:none;
	font-family:Arial, Helvetica, sans-serif;
}
p{
	width:100%;
	height:auto;
	text-align:left;
	padding-left: 10px;
	font-size:14px;
	text-transform:none;
	line-height:2em; 
	  
}
h3{
	text-align:left;
	
	font-weight:bold;
	font-size:16px;
}

h4{
	text-align:left;
	font-size:14px;
}
ul li{
margin-left:20px;
list-style:none;	
}

</style>

<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
  
  <img src="../imagens/logomaster<?php echo $row_master['id_master'];?>.gif" style="margin-left:500px;"/>
  
  
  
  <p style="width:400px;height:auto;margin-left:500px;margin-bottom:30px;">  Contrato celebrado entre o <strong> <?php echo $row_master['razao']; ?> </strong> e a empresa <strong><?php echo $row_trab['e_empresa']?></strong> para presta��o de servi�os m�dicos.</p>
  </strong>
  
   <p> Em <?php echo $row_trab['data_cad'];?>  , nas instala��es de <strong><?php echo $row_master['razao']?></strong> , CNPJ <?php echo $row_master['cnpj']?>, situada na <?php echo $row_master['endereco']?> , doravante denominada <strong>CONTRATANTE</strong>, esta e a empresa <strong><?php echo $row_trab['e_empresa']?></strong>, situada na <?php echo $row_trab['e_endereco']?>, inscrita no CNPJ sob o n� <?php echo $row_trab['e_cnpj']?>, doravante denominada <strong>CONTRATADA</strong>, celebram o presente Contrato.</p>

<h3>CL�USULA PRIMEIRA � DA APROVA��O DA MINUTA</h3>
<p>A minuta deste Contrato foi aprovada pela Ger�ncia de Controle Interno do <?php echo $row_master['razao']?></p>

<h3>CL�USULA SEGUNDA � DA DELEGA��O DE COMPET�NCIA </h3>

<p>De acordo com as atribui��es do cargo em que � investida, o(a) Sr(a). <?php echo $row_master['responsavel']?>, CPF <?php echo $row_master['cpf']?>, Diretor do <?php echo $row_master['razao']?>, tem compet�ncia para assinar este contrato em nome da <strong>CONTRATANTE</strong>.
De acordo com o contrato social, o Sr. <?php echo $row_trab['nome'];?>, CPF n� <?php echo $row_trab['cpf'];?>, tem compet�ncia para assinar este contrato em nome da <strong>CONTRATADA</strong>. </p>

<h3>CL�USULA TERCEIRA � DO OBJETO</h3>
<p>O objeto deste CONTRATO � a presta��o de <strong>SERVI�OS M�DICOS </strong>pela <strong>CONTRATADA</strong>.</p>

<h4>Subcl�usula Primeira </h4>

<p>Os servi�os que constituem o objeto deste acordo ser�o prestado em conformidade com a legisla��o vigente nas instala��es da <strong>CONTRATANTE</strong>, sendo a <strong>CONTRATADA</strong> respons�vel pelo estabelecimento de hor�rios e rotinas de trabalho de forma satisfazer, a contento, �s solicita��es da <strong>CONTRATANTE</strong>.</p>

<h3>CL�USULA QUARTA � DOS DEVERES DAS PARTES</h3>

<p>I � s�o deveres da CONTRATADA:</p>
<ul>
	<li>
		<p>a)	Prestar os servi�os mencionados, atrav�s de s�cios ou empregados seus especialmente capacitados e habilitados para a respectiva fun��o;</p>
    </li>
    <li>    
		<p>b)	Arca exclusivamente com todos os encargos trabalhistas e previdenci�rios, bem como aqueles referentes a acidentes de trabalho, FGTS e PIS com respeito aos seus empregados eventualmente envolvidos na presta��o dos servi�os;</p>
    </li>
    <li>
		<p>c)	Respeitar e fazer com que seu pessoal respeite as normas de seguran�a do trabalho, disciplina e demais regulamentos em vigor quando em servi�o no estabelecimento da CONTRATANTE;</p>
<p>d)	Comparecer, ou fazer-se substituir nos casos de necessidade de aus�ncia, aos atendimentos previamente marcados, ou ainda informar a necessidade futura de a aus�ncia, em tempo h�bil para que sejam tomadas as devidas provid�ncias; e </p>
	</li>
    <li>
	<p>e)	Atende � cliente de maneira cort�s, de forma a colaborar com a boa imagem da marca e elevar o nome da empresa, e valer-se dos melhores princ�pios de �ticas e t�cnicas da profiss�o, de maneira a  promover atendimentos com elevado grau de qualidade.</p>
    </li>
</ul>
<p>II � � dever da CONTRATANTE efetuar os pagamentos devidos � CONTRATADA na forma e datas ajustadas na CL�USULA OITAVA deste Contrato.</p>

<h3>CL�USULA QUINTA � DAS RESPONSABILIDADES</h3>
<p>Fica expressamente estabelecido que, por for�a deste Contrato, n�o se estabelece qualquer v�nculo empregat�cio entre a CONTRATANTE e os s�cios, empregados e/ou t�cnicos da CONTRATADA, sendo esta �ltima a empregadora do pessoal necess�rio � execu��o dos servi�os aqui contratados. Assim sendo, a CONTRATADA se obriga a manter a CONTRATANTE livre de todas e quaisquer reclama��es trabalhista, previdenci�rias e/ou reivindica��es de ordem social, obrigando-se, ainda a excepcionar a CONTRATANTE, em ju�zo ou fora dele, com rela��o a qualquer pretendido vinculo com essa �ltima. Ocorrendo qualquer reclama��o trabalhista contra a CONTRATANTE, a CONTRATADA se responsabilizar�, em ju�zo, pelo eventual direito do reclamante, pagando, ainda, todas as despesas que a CONTRATANTE incorrer para a defesa de seus interesses. Da mesma forma, todos os tributos (impostos, taxas, emolumentos, contribui��es fiscais ou paraf�scais) que forem devidos em decorr�ncia direta ou indireta deste contrato, ou de sua execu��o ser�o de responsabilidade exclusiva da CONTRATADA.</p>

<h3>CL�USULA SEXTA � DO PRE�O</h3>
<p>O pagamento dar-se-� em parcelas mensais e sucessivas, sendo seu valor mensal calculado de acordo com a produtividade observada dos colaboradores (s�cios ou funcion�rios) da CONTRATADA, e de acordo com os par�metros estabelecidos pela CONTRATANTE e/ou pelas seguradoras e operadoras de planos de sa�de dela conveniadas.</p>
<p>A produtividade, por colaborador respons�vel, ser� calculada em conformidade com os dados registrados no sistema de controle da CONTRATANTE que disponibilizar� um resumo mensal da produ��o aferida, a fim de respaldar a emiss�o dos t�tulos de cr�dito pertinentes pela CONTRATADA.</p>

<h3>CL�USULA D�TIMA � DO REAJUSTAMENTO</h3>
<p>O valor desse contrato poder� ser reajustado por livre negocia��o entre CONTRATANTE e CONTRATADO, desde que realinhados os par�metros estabelecidos pelas seguradoras e operadoras de planos de sa�de conveniadas.</p>

<h3>CL�USULA OITAVA � DO PAGAMENTO </h3>
<p>Afim de que todas as provid�ncias de faturamento possam ser levadas a termo, os pagamentos ser�o efetuados pela CONTRATANTE � CONTRATADA ate o dia 25 (vinte e cinco ) de cada m�s, referente � produtividade do terceiro m�s imediatamente anterior. Para tanto, a CONTRATADA dever� apresentar as notas fiscais devidas ate cinco dias corridos antes da citada data mensal.</p>

<h4>Subcl�usula primeira</h4>
<p>Os t�tulos de cobran�a dever�o ser encaminhados para pagamento ao endere�o <?php echo $row_trab['e_endereco']?>, <?php echo $row_trab['e_bairro']?>,  <?php echo $row_trab['e_cidade']?>, <?php echo $row_trab['e_estado']?></p>

<h4>Subcl�usula segunda</h4>
<p>Os pagamentos ser�o efetuados mediante dep�sito ou boleto banc�rio. Para tanto, a CONTRATADA dever� informar, no documento de cobran�a, o nome e o n�mero do banco, da ag�ncia e da conta corrente da empresa.</p>

<h4>Subcl�usula terceira</h4>
<p>Ser�o retidos, pela CONTRATANTE, os valores de impostos e contribui��es de acordo com as al�quotas previstas na legisla��o vigente, aplicadas ao valor bruto dos t�tulos de cr�dito emitidos. O recolhimento do ISS ser� a expensas da CONTRATADA e de sua responsabilidade.</p>

<h3>CL�USULA NONA � DOS PRAZOS</h3>
<p>O prazo de execu��o e vig�ncia do objetivo do presente contrato � de 12 ( doze ) meses e inicia �se na data de sua assinatura, sendo, ap�s este prazo, renovado por prazo indeterminado, desde que n�o haja manifesta��o em contr�rio por qualquer das parte. Independente dos prazos acima estabelecidos, o presente instrumento poder� ser interrompido a qualquer momento, observado o disposto na CL�USULA D�CIMA.</p>

<h3>CL�USULA D�CIMA � DA RESCIS�O, DA RESILI��O E DO DISTRATO</h3>
<p>As interrup��es de execu��o deste acordo poder�o se dar por rescis�o, resili��o ou distrato, conforme estabelecido nas subcl�usulas abaixo, n�o cabendo, em qualquer caso, nenhum tipo de indeniza��o de parte a parte. Em todos os casos, dever� ser firmado o pertinente Termo de Rescis�o, Resili��o ou Distrato.</p>
<h4>Subcl�usula primeira</h4>
<p>I-	Constituem motivos para a CONTRATANTE rescindir o presente acordo, independentemente de procedimento judicial;</p>
<ul>
	<li>
	<p>a)	N�o cumprimento ou cumprimento irregular de cl�usulas contratuais ou prazos constantes deste acordo;</p>
    </li>
    <li>
		<p>b)	A subcontrata��o total ou parcial do seu objeto;</p>
	</li>
    <li>
		<p>c)	A decreta��o de fal�ncia ou dissolu��o da sociedade; e</p>
       </li>
       <li>
			<p>d)	A ocorr�ncia de caso fortuito ou de for�a maior, conforme regulado no c�digo Civil, regularmente comprovado e impeditivo da execu��o do Contrato.</p>
	</li>
    <li>
		<p>II-	Constituem motivos para a CONTRATADA rescindir o presente acordo, independentemente de procedimento judicial;</p>
       </li>
       <li>
		<p>a)	N�o pagamento, pela CONTRATANTE; E</p>
        </li>
        <li>
		<p>b)	A ocorr�ncia de caso fortuito ou de for�a maior, conforme regulado no C�digo Civil, regularmente comprovado e impeditivo da execu��o do Contrato. </p>
</li>
</ul>
<h4>Subcl�usula segunda</h4>
<p>Este contrato poder�, ainda, ser resilido por interesse de qualquer das partes e a qualquer momento, desde que atrav�s de comunica��o expressa com anteced�ncia m�nima de 30 (trinta) dias, ou mesmo poder� ocorrer o seu distrato consensual.</p>
<h3>CL�USULA D�CIMA PRIMEIRA � DAS DISPOSI��ES GERAIS</h3>

<p>Este contrato cancela e substitui qualquer outro anterior firmado entre a CONTRATANTE e a CONTRATADA, dando quita��o rasa e plena ao mesmo, sem restarem quaisquer obriga��es a cumprir de ambas as partes.</p>
<h3>CL�USULA D�CIMA SEGUNDA � DO FORO</h3>
<p>Para resolver as diverg�ncias entre as partes, oriundas da execu��o do presente acordo, fica eleito o foro da cidade do Rio de Janeiro.</p>
<h3>CL�USULA D�CIMA TERCEIRA- DOS ORIGINAIS</h3>
<p>Do presente acordo s�o extra�dos os seguintes originais, de igual teor e conte�do:</p>
<ul>
	<li><p>a)	Um para a CONTRATANTE; e</p></li>
	<li><p>b)	Um para a CONTRATADA;</p></li>
</ul>
<p>E por assim acordarem, as partes declaram aceitar todas as disposi��es estabelecidas neste contrato que, lido e achado conforme, vai assinado pelos representantes e testemunhas a seguir, a todo o ato presente.</p>

<p>&nbsp;</p>
<?php 
$data = explode('-',$row_trab['data_cad']);
$data[1] = sprintf('02%',$data[1]);


$mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$data[1]'"),0);

?>

<p style="margin-left:400px;">Rio de Janeiro, em <?php echo  $data[2]?> de <?php echo strtolower($mes); ?> de <?php echo  $data[0]?>
</p>

<table style="font-size:12px;margin-left:400px;margin-top:80px">
	<tr>
    	<td><strong><?php echo $row_master['razao'];?></strong></td>
     </tr>
     <tr>
        <td><?php echo $row_master['responsavel'];?></td>
    </tr>
    <tr height="60">
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td><strong><?php echo $row_trab['e_empresa'];?></strong></td>
        </tr>
    <tr>
        <td><?php echo $row_trab['nome'];?></td>
    </tr>
</table>

<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

<p>
Testemunhas:</p>
<p>&nbsp;</p>

<table width="100%">
	
	<tr>
    	<td align="left">__________________________________________________</td>
        <td align="left">__________________________________________________ </td>
    </tr>
    <tr>
    	<td align="left">NOME:</td>
        <td align="left">NOME:</td>
    </tr>
    <tr>
    	<td align="left">CPF</td>
        <td align="left">CPF</td>
     </tr>

</table>


    
    
    
   </div>
   <div class="rodape2">
  
          
   </div>
 </div>
</body>
</html>