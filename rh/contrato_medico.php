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
  
  
  
  <p style="width:400px;height:auto;margin-left:500px;margin-bottom:30px;">  Contrato celebrado entre o <strong> <?php echo $row_master['razao']; ?> </strong> e a empresa <strong><?php echo $row_trab['e_empresa']?></strong> para prestação de serviços médicos.</p>
  </strong>
  
   <p> Em <?php echo $row_trab['data_cad'];?>  , nas instalações de <strong><?php echo $row_master['razao']?></strong> , CNPJ <?php echo $row_master['cnpj']?>, situada na <?php echo $row_master['endereco']?> , doravante denominada <strong>CONTRATANTE</strong>, esta e a empresa <strong><?php echo $row_trab['e_empresa']?></strong>, situada na <?php echo $row_trab['e_endereco']?>, inscrita no CNPJ sob o nº <?php echo $row_trab['e_cnpj']?>, doravante denominada <strong>CONTRATADA</strong>, celebram o presente Contrato.</p>

<h3>CLÁUSULA PRIMEIRA – DA APROVAÇÃO DA MINUTA</h3>
<p>A minuta deste Contrato foi aprovada pela Gerência de Controle Interno do <?php echo $row_master['razao']?></p>

<h3>CLÁUSULA SEGUNDA – DA DELEGAÇÃO DE COMPETÊNCIA </h3>

<p>De acordo com as atribuições do cargo em que é investida, o(a) Sr(a). <?php echo $row_master['responsavel']?>, CPF <?php echo $row_master['cpf']?>, Diretor do <?php echo $row_master['razao']?>, tem competência para assinar este contrato em nome da <strong>CONTRATANTE</strong>.
De acordo com o contrato social, o Sr. <?php echo $row_trab['nome'];?>, CPF nº <?php echo $row_trab['cpf'];?>, tem competência para assinar este contrato em nome da <strong>CONTRATADA</strong>. </p>

<h3>CLÁUSULA TERCEIRA – DO OBJETO</h3>
<p>O objeto deste CONTRATO é a prestação de <strong>SERVIÇOS MÉDICOS </strong>pela <strong>CONTRATADA</strong>.</p>

<h4>Subcláusula Primeira </h4>

<p>Os serviços que constituem o objeto deste acordo serão prestado em conformidade com a legislação vigente nas instalações da <strong>CONTRATANTE</strong>, sendo a <strong>CONTRATADA</strong> responsável pelo estabelecimento de horários e rotinas de trabalho de forma satisfazer, a contento, às solicitações da <strong>CONTRATANTE</strong>.</p>

<h3>CLÁUSULA QUARTA – DOS DEVERES DAS PARTES</h3>

<p>I – são deveres da CONTRATADA:</p>
<ul>
	<li>
		<p>a)	Prestar os serviços mencionados, através de sócios ou empregados seus especialmente capacitados e habilitados para a respectiva função;</p>
    </li>
    <li>    
		<p>b)	Arca exclusivamente com todos os encargos trabalhistas e previdenciários, bem como aqueles referentes a acidentes de trabalho, FGTS e PIS com respeito aos seus empregados eventualmente envolvidos na prestação dos serviços;</p>
    </li>
    <li>
		<p>c)	Respeitar e fazer com que seu pessoal respeite as normas de segurança do trabalho, disciplina e demais regulamentos em vigor quando em serviço no estabelecimento da CONTRATANTE;</p>
<p>d)	Comparecer, ou fazer-se substituir nos casos de necessidade de ausência, aos atendimentos previamente marcados, ou ainda informar a necessidade futura de a ausência, em tempo hábil para que sejam tomadas as devidas providências; e </p>
	</li>
    <li>
	<p>e)	Atende à cliente de maneira cortês, de forma a colaborar com a boa imagem da marca e elevar o nome da empresa, e valer-se dos melhores princípios de éticas e técnicas da profissão, de maneira a  promover atendimentos com elevado grau de qualidade.</p>
    </li>
</ul>
<p>II – É dever da CONTRATANTE efetuar os pagamentos devidos á CONTRATADA na forma e datas ajustadas na CLÁUSULA OITAVA deste Contrato.</p>

<h3>CLÁUSULA QUINTA – DAS RESPONSABILIDADES</h3>
<p>Fica expressamente estabelecido que, por força deste Contrato, não se estabelece qualquer vínculo empregatício entre a CONTRATANTE e os sócios, empregados e/ou técnicos da CONTRATADA, sendo esta última a empregadora do pessoal necessário á execução dos serviços aqui contratados. Assim sendo, a CONTRATADA se obriga a manter a CONTRATANTE livre de todas e quaisquer reclamações trabalhista, previdenciárias e/ou reivindicações de ordem social, obrigando-se, ainda a excepcionar a CONTRATANTE, em juízo ou fora dele, com relação a qualquer pretendido vinculo com essa última. Ocorrendo qualquer reclamação trabalhista contra a CONTRATANTE, a CONTRATADA se responsabilizará, em juízo, pelo eventual direito do reclamante, pagando, ainda, todas as despesas que a CONTRATANTE incorrer para a defesa de seus interesses. Da mesma forma, todos os tributos (impostos, taxas, emolumentos, contribuições fiscais ou parafíscais) que forem devidos em decorrência direta ou indireta deste contrato, ou de sua execução serão de responsabilidade exclusiva da CONTRATADA.</p>

<h3>CLÁUSULA SEXTA – DO PREÇO</h3>
<p>O pagamento dar-se-á em parcelas mensais e sucessivas, sendo seu valor mensal calculado de acordo com a produtividade observada dos colaboradores (sócios ou funcionários) da CONTRATADA, e de acordo com os parâmetros estabelecidos pela CONTRATANTE e/ou pelas seguradoras e operadoras de planos de saúde dela conveniadas.</p>
<p>A produtividade, por colaborador responsável, será calculada em conformidade com os dados registrados no sistema de controle da CONTRATANTE que disponibilizará um resumo mensal da produção aferida, a fim de respaldar a emissão dos títulos de crédito pertinentes pela CONTRATADA.</p>

<h3>CLÁUSULA DÉTIMA – DO REAJUSTAMENTO</h3>
<p>O valor desse contrato poderá ser reajustado por livre negociação entre CONTRATANTE e CONTRATADO, desde que realinhados os parâmetros estabelecidos pelas seguradoras e operadoras de planos de saúde conveniadas.</p>

<h3>CLÁUSULA OITAVA – DO PAGAMENTO </h3>
<p>Afim de que todas as providências de faturamento possam ser levadas a termo, os pagamentos serão efetuados pela CONTRATANTE à CONTRATADA ate o dia 25 (vinte e cinco ) de cada mês, referente à produtividade do terceiro mês imediatamente anterior. Para tanto, a CONTRATADA deverá apresentar as notas fiscais devidas ate cinco dias corridos antes da citada data mensal.</p>

<h4>Subcláusula primeira</h4>
<p>Os títulos de cobrança deverão ser encaminhados para pagamento ao endereço <?php echo $row_trab['e_endereco']?>, <?php echo $row_trab['e_bairro']?>,  <?php echo $row_trab['e_cidade']?>, <?php echo $row_trab['e_estado']?></p>

<h4>Subcláusula segunda</h4>
<p>Os pagamentos serão efetuados mediante depósito ou boleto bancário. Para tanto, a CONTRATADA deverá informar, no documento de cobrança, o nome e o número do banco, da agência e da conta corrente da empresa.</p>

<h4>Subcláusula terceira</h4>
<p>Serão retidos, pela CONTRATANTE, os valores de impostos e contribuições de acordo com as alíquotas previstas na legislação vigente, aplicadas ao valor bruto dos títulos de crédito emitidos. O recolhimento do ISS será a expensas da CONTRATADA e de sua responsabilidade.</p>

<h3>CLÁUSULA NONA – DOS PRAZOS</h3>
<p>O prazo de execução e vigência do objetivo do presente contrato é de 12 ( doze ) meses e inicia –se na data de sua assinatura, sendo, após este prazo, renovado por prazo indeterminado, desde que não haja manifestação em contrário por qualquer das parte. Independente dos prazos acima estabelecidos, o presente instrumento poderá ser interrompido a qualquer momento, observado o disposto na CLÁUSULA DÉCIMA.</p>

<h3>CLÁUSULA DÉCIMA – DA RESCISÃO, DA RESILIÇÃO E DO DISTRATO</h3>
<p>As interrupções de execução deste acordo poderão se dar por rescisão, resilição ou distrato, conforme estabelecido nas subcláusulas abaixo, não cabendo, em qualquer caso, nenhum tipo de indenização de parte a parte. Em todos os casos, deverá ser firmado o pertinente Termo de Rescisão, Resilição ou Distrato.</p>
<h4>Subcláusula primeira</h4>
<p>I-	Constituem motivos para a CONTRATANTE rescindir o presente acordo, independentemente de procedimento judicial;</p>
<ul>
	<li>
	<p>a)	Não cumprimento ou cumprimento irregular de cláusulas contratuais ou prazos constantes deste acordo;</p>
    </li>
    <li>
		<p>b)	A subcontratação total ou parcial do seu objeto;</p>
	</li>
    <li>
		<p>c)	A decretação de falência ou dissolução da sociedade; e</p>
       </li>
       <li>
			<p>d)	A ocorrência de caso fortuito ou de força maior, conforme regulado no código Civil, regularmente comprovado e impeditivo da execução do Contrato.</p>
	</li>
    <li>
		<p>II-	Constituem motivos para a CONTRATADA rescindir o presente acordo, independentemente de procedimento judicial;</p>
       </li>
       <li>
		<p>a)	Não pagamento, pela CONTRATANTE; E</p>
        </li>
        <li>
		<p>b)	A ocorrência de caso fortuito ou de força maior, conforme regulado no Código Civil, regularmente comprovado e impeditivo da execução do Contrato. </p>
</li>
</ul>
<h4>Subcláusula segunda</h4>
<p>Este contrato poderá, ainda, ser resilido por interesse de qualquer das partes e a qualquer momento, desde que através de comunicação expressa com antecedência mínima de 30 (trinta) dias, ou mesmo poderá ocorrer o seu distrato consensual.</p>
<h3>CLÁUSULA DÉCIMA PRIMEIRA – DAS DISPOSIÇÕES GERAIS</h3>

<p>Este contrato cancela e substitui qualquer outro anterior firmado entre a CONTRATANTE e a CONTRATADA, dando quitação rasa e plena ao mesmo, sem restarem quaisquer obrigações a cumprir de ambas as partes.</p>
<h3>CLÁUSULA DÉCIMA SEGUNDA – DO FORO</h3>
<p>Para resolver as divergências entre as partes, oriundas da execução do presente acordo, fica eleito o foro da cidade do Rio de Janeiro.</p>
<h3>CLÁUSULA DÉCIMA TERCEIRA- DOS ORIGINAIS</h3>
<p>Do presente acordo são extraídos os seguintes originais, de igual teor e conteúdo:</p>
<ul>
	<li><p>a)	Um para a CONTRATANTE; e</p></li>
	<li><p>b)	Um para a CONTRATADA;</p></li>
</ul>
<p>E por assim acordarem, as partes declaram aceitar todas as disposições estabelecidas neste contrato que, lido e achado conforme, vai assinado pelos representantes e testemunhas a seguir, a todo o ato presente.</p>

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