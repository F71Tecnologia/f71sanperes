<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_bolsista = $_REQUEST['bol'];
$id_projeto = $_REQUEST['pro'];
$id_regiao = $_REQUEST['id_reg'];
$tabela = $_REQUEST['tab'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '18' and id_clt = '$id_bolsista'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('18','$id_bolsista','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bolsista' and tipo = '18'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result_bol = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data2, date_format(data_entrada, '%d/%m/%Y')as data_entrada2 FROM autonomo where id_autonomo = '$id_bolsista'", $conn);
$row_bol = mysql_fetch_array($result_bol);

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'", $conn);
$row_pro = mysql_fetch_array($result_pro);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("Select * from curso where id_curso = $row_bol[id_curso]", $conn);
$row_curso = mysql_fetch_array($result_curso);


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

setlocale(LC_MONETARY, 'pt_BR');
$valor_curso_f = number_format($row_curso['salario'],2,",",".");

//<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">

include "empresa.php";
$img= new empresa();

$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$id_regiao' AND id_projeto = '$id_projeto'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

print"
<html>
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='net1.css' rel='stylesheet' type='text/css'>
<style type='text/css' media='print'> 
.print2
{ 
   display: none; 
} 
</style>
<style type='text/css'>
<!--
.style1 {color: #FF0000;
	font-weight: bold;}
.style5 {font-size: 12px}
.style6 {font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;}
.style7 {color: #FF0000}
.style11 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
.style13 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style15 {color: #FF0000; font-weight: bold; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style16 {font-size: 11px}
-->
</style>
<script>
var tam=12;
	function mudaFonte(tipo)
	{
		if (tipo==\"mais\"){
    		if (tam < 24){
    		    tam+=2
    		}
    	}
		if (tipo==\"menos\"){
		    if (tam>10){
		        tam-=2
		    }
		}	
		if ((tam < 24) && (tam>=10))
			document.getElementById('conteudo').style.fontSize=tam + \"px\";
	}
	resize();
</script>
</head>

<body>
<center><div id='conteudo'>
<table bgcolor='#FFFFFF' border='0' cellpadding='5' cellspacing='0' width='70%' class='bordaescura1px' >
  <col class='xl65' width='87' style=' width:65pt' />
  <col class='xl65' width='361' style=' width:271pt' />
  <col class='xl65' width='87' style=' width:65pt' />
  <col class='xl65' width='296' style=' width:222pt' />
  <col class='xl65' width='87' style=' width:65pt' />
  <col class='xl65' width='361' style=' width:271pt' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' span='2' style='display:none; ' />
  <col class='xl65' width='0' span='2' style='display:none; ' />
  <col class='xl65' width='0' style='display:none;' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' span='18' style='display:none; ' />
  <tr>
    <td colspan='5'
  align='center' valign='middle'><img src='";$img->imagem2(); print "' width='120' height='86' align='left' />
      <p ><img src='imagens/cobrinha.gif' width='99' height='123' align='right' /><b>".$row_empresa['razao']."</p>
      <p ><span class='style7'><font size=3>$row_pro[nome]</font></span></p>
      <p>TERMO DE COMPROMISSO DE BOLSA-AUX&Iacute;LIO</p></b></td>
  </tr>
  <tr>
    <td width='316'><span class='style13'>UNIDADE CEDENTE (INSTITUI&Ccedil;&Atilde;O DE ENSINO)</span></td>
    <td colspan='2'><span class='style13'>UNIDADE CONCEDENTE</span></td>
    <td width='311' colspan='2'><span class='style13'>BOLSISTA</span></td>
  </tr>
  <tr>
    <td></td>
    <td colspan='2'></td>
    <td colspan='2'></td>
  </tr>
  <tr>
    <td><span class='style11'>Raz&atilde;o Social:&nbsp;</span><span class='style13'>SOE &ndash; Sistema Objetivo de Ensino</span><span class='style13'><strong><br />
    CNPJ:&nbsp;</strong></span><span class='style13'>03.635.819/0001-13<br />
    </span><span class='style13'><strong>Endere&ccedil;o:</strong></span><span class='style13'> Rua Olinda Elis, 278 &ndash; Campo Grande - RJ<br />
    </span><span class='xl74 style13 style5' style='height:12.0pt'><strong>Certifica&ccedil;&atilde;o:</strong></span><span class='style13'> Portaria E/SADE/AUT n. &ordm; 120 de 29/11/02</span></td>
    <td colspan='2'><span class='style13'><strong>Raz&atilde;o Social:&nbsp;</strong></span><span class='style13'>".$row_empresa['nome']."</span><span class='style13'><strong><br />
    CNPJ:&nbsp;</strong></span><span class='style13'>06.888.897/0001-18<br />
    </span><span class='style13'><strong>Endere&ccedil;o:</strong></span><span class='style13'> S&atilde;o Luis, 112 - 18&deg; Andar &ndash; Cj. 1802&nbsp; - S&atilde;o Paulo</span><span class='style13'><strong><br />
    Certifica&ccedil;&atilde;o:</strong></span><span class='style13'> OSCIP N.&ordm; 08026.012349/2004-40&nbsp;&nbsp;&nbsp;</span></td>
    <td colspan='2'><span class='style13'>
	<strong>Nome:</strong></span><span class='style15'> $row_bol[nome]</span><span class='style13'>
	<strong><br>Endere&ccedil;o:</strong></span><span class='style15'> $row_bol[endereco]</span><span class='style13'>
	<strong><br>Telefone:</strong></span><span class='style15'> $row_bol[tel_fixo]</span><span class='style13'>
	<strong><br>Nascimento:<span class='style15'>$row_bol[data2]
	</span><br>CPF:<span class='style1'> $row_bol[cpf]</span><br />
    RG:</strong></span><span class='style13'><strong><span class='style1'> $row_bol[rg] </span><br />
    </strong><br />
    </span></td>
  </tr>


  <tr>
    <td colspan='5'></td>
  </tr>
  <tr>
    <td colspan='5'><span class='style5'></span><span class='style5'></span></td>
  </tr>
  <tr>
    <td colspan='5'><p align='justify' class='style13'>
	  Celebram o presente <strong>TERMO DE BOLSA-AUX&Iacute;LIO</strong>, conforme condi&ccedil;&otilde;es a
        seguir:</p>
      <p align='justify' class='style13'><u>Cl&aacute;usula 1&ordm;</u>. A Bolsa ter&aacute; a dura&ccedil;&atilde;o de at&eacute; um ano, tendo a
        quita&ccedil;&atilde;o de todas as despesas a partir do dia <span class='style1'>  $row_bol[data_entrada2]</span>, podendo ser
        rescindida ou suspensa atrav&eacute;s de simples comunica&ccedil;&atilde;o. Se o contrato for
        suspenso a sua retomada dar-se-&aacute; tamb&eacute;m, atrav&eacute;s de simples comunica&ccedil;&atilde;o;</p>
      <p align='justify' class='style13'><u>Cl&aacute;usula 2&ordm;.</u> O pagamento da <strong>BOLSA-AUX&Iacute;LIO</strong> ser&aacute; realizado nas
        instala&ccedil;&otilde;es do <span class='style1'> $row_bol[localpagamento]</span>, liquidando-se apenas as obriga&ccedil;&otilde;es vencidas, tendo em
        vista que o <strong>BOLSISTA </strong>n&atilde;o ter&aacute; v&iacute;nculo empregat&iacute;cio com a <strong>CONCEDENTE</strong> ou com o(a) <span class='style1'> $row_bol[locacao]</span>, em raz&atilde;o deste Termo de compromisso</span><span class='style13'> OSCIP N.&ordm; 08026.012349/2004-40&nbsp;&nbsp;&nbsp;</span></td>
    <td colspan='2'></td>
  </tr>


  <tr>
    <td colspan='5'></td>
  </tr>
  <tr>
    <td colspan='5'><span class='style5'></span><span class='style5'></span></td>
  </tr>
  <tr>
    <td colspan='5'><p align='justify' class='style13'>Celebram o presente <strong>TERMO ;
      <br><br>
	  <u>Cl&aacute;usula 3&ordm;</u>. O per&iacute;odo previsto de <strong>BOLSA-AUX&Iacute;LIO</strong> poder&aacute; ser
        prorrogado, mediante entendimento entre as partes contratantes;
      <br>
	  <u>Cl&aacute;usula 4&ordm;</u>. A Bolsa poder&aacute; cessar mediante simples aviso,
        escrito, por quaisquer das partes, n&atilde;o cabendo indeniza&ccedil;&otilde;es; este ato dever&aacute;
        ser formalizado atrav&eacute;s de documento complementar a este termo de
        compromisso;Os seguintes fatos importar&atilde;o na rescis&atilde;o do contrato de <strong>BOLSA-AUX&Iacute;LIO</strong>:&middot;&nbsp; O abandono ou interrup&ccedil;&atilde;o do curso pelo aluno <strong>(BOLSISTA)</strong>, trancamento de matr&iacute;cula, conclus&atilde;o do curso;&middot;&nbsp;O n&atilde;o cumprimento de quaisquer das cl&aacute;usulas previstas
        neste Instrumento Jur&iacute;dico;&middot;&nbsp; A simples comunica&ccedil;&atilde;o do <strong>CEDENTE</strong> e/ou <strong>CONCEDENTE</strong> ao <strong>BOLSISTA</strong> sobre o encerramento antecipado da <strong>BOLSA-AUX&Iacute;LIO</strong> por motivo do n&atilde;o
        aproveitamento do educando no programa, conforme a expectativa inicial;A simples comunica&ccedil;&atilde;o de suspens&atilde;o tempor&aacute;ria da <strong>BOLSA-AUX&Iacute;LIO</strong>,
        podendo ser retomada as condi&ccedil;&otilde;es iniciais tamb&eacute;m atrav&eacute;s de simples
        comunica&ccedil;&atilde;o;Por ocasi&atilde;o do t&eacute;rmino da <strong>BOLSA-AUX&Iacute;LIO</strong>, a <strong>CONCEDENTE</strong> fornecer&aacute;
        ao <strong>BOLSISTA</strong>, em forma de avalia&ccedil;&atilde;o, o resultado de seu aproveitamento;
      <br>
	  <u>Cl&aacute;usula 5&ordm;</u>. A <strong>CONCEDENTE</strong> objetivar&aacute; estabelecer as condi&ccedil;&otilde;es
        b&aacute;sicas para a consecu&ccedil;&atilde;o da <strong>BOLSA-AUX&Iacute;LIO</strong> do aluno, que dever&aacute;
        necessariamente ser de interesse curricular, integrando e complementando, na
        pr&aacute;tica, o ensino ministrado e, ainda, acrescentar capacita&ccedil;&atilde;o e
        aperfei&ccedil;oamento t&eacute;cnico, cultural e social ao <strong>BOLSISTA</strong>;
      <br>
	  <u>Cl&aacute;usula 6&ordm;</u>. O <strong>BOLSISTA</strong> desempenhar&aacute; suas atividades respeitando
        os hor&aacute;rios limites de lei, n&atilde;o podendo o hor&aacute;rio destinado &agrave; <strong>BOLSA-AUX&Iacute;LIO</strong> coincidir com o hor&aacute;rio escolar;
      <br>
	  <u>Cl&aacute;usula 7&ordm;</u>. A freq&uuml;&ecirc;ncia do <strong>BOLSISTA</strong> ser&aacute; demonstrada por
        qualquer modalidade de controle adotada pela <strong>CONCEDENTE</strong>;
      <br>
	  <u>Cl&aacute;usula 8&ordm;</u>. O <strong>BOLSISTA</strong> receber&aacute; como <strong>BOLSA-AUX&Iacute;LIO</strong> e pela
        complementa&ccedil;&atilde;o educacional, o valor refer&ecirc;ncia igual a <span class='style1'> 
		$valor_curso_f</span> mensais,
        ou, se for o caso, o valor concernente ao n&uacute;mero de horas de BOLSA-AUX&Iacute;LIO
        efetivamente cumpridas. O pagamento ser&aacute; efetivado no 30&ordm; dia do m&ecirc;s
        subseq&uuml;ente ao vencido;
      <br>
	  <u>Cl&aacute;usula 9&ordm;</u>. O valor da <strong>BOLSA-AUX&Iacute;LIO</strong> poder&aacute; variar, conforme o
        desempenho do <strong>BOLSISTA</strong>, que ser&aacute; avaliado periodicamente pela <strong>CONCEDENTE</strong>;
      <br>
	  <u>Cl&aacute;usula 10&ordm;</u>. A import&acirc;ncia referente &agrave; <strong>BOLSA-AUX&Iacute;LIO</strong>, por n&atilde;o
        ter natureza salarial, n&atilde;o estar&aacute; sujeita a qualquer tributa&ccedil;&atilde;o conforme a
        Lei n&ordm; 8.859/94;
     <br>
	  <u>Cl&aacute;usula 11&ordm;</u>. <strong>O BOLSISTA</strong> se obriga a cumprir fielmente a
        programa&ccedil;&atilde;o da bolsa e orienta&ccedil;&otilde;es do coordenador e/ou supervisor designado
        pela <strong>CONCEDENTE</strong>, bem como as normas e regulamento interno da <strong>CONCEDENTE</strong>,
        salvo impossibilidade da qual a mesma ser&aacute; previamente informada;
      <br>
	  <u>Cl&aacute;usula 12&ordm;</u>. As atividades do <strong>BOLSISTA</strong> poder&atilde;o ser alteradas
        com o progresso das atividades, do curr&iacute;culo escolar e do resultado das
        avalia&ccedil;&otilde;es, objetivando, sempre, a compatibiliza&ccedil;&atilde;o e a complementa&ccedil;&atilde;o do
        curso que ora o BOLSISTA est&aacute; matriculado;
      <br>
	  <u>Cl&aacute;usula 13&ordm;</u>. No per&iacute;odo de vig&ecirc;ncia do presente Termo de
        compromisso, o <strong>BOLSISTA</strong> ter&aacute; cobertura e Acidentes Pessoais proporcionada
        pela <strong>CONCEDENTE</strong> nas condi&ccedil;&otilde;es estipuladas na ap&oacute;lice com condi&ccedil;&otilde;es b&aacute;sicas
        elencadas no final deste instrumento;
		<br>
		<u>Cl&aacute;usula 14&ordm;</u>. Ser&aacute; emitido para o <strong>BOLSISTA</strong>, como parte
        integrante e obrigat&oacute;ria deste Instrumento, o respectivo Certificado
        Individual de Seguro de Acidentes Pessoais;
      <br>
	  <u>Cl&aacute;usula 15&ordm;</u>. O <strong>BOLSISTA</strong> se compromete a zelar pelos
        instrumentos, equipamentos, materiais e instala&ccedil;&otilde;es de propriedade da <strong>CONCEDENTE</strong>, do Poder P&uacute;blico ou de terceiros que lhe forem confiados em raz&atilde;o
        da bolsa, reservando-se &agrave; <strong>CONCEDENTE</strong> o direito de responsabilizar o <strong>BOLSISTA</strong> pelos danos que por ele forem causados por dolo, neglig&ecirc;ncia, imprud&ecirc;ncia ou
        imper&iacute;cia;
      <br><u>Cl&aacute;usula 16&ordm;</u>. Qualquer disputa decorrente deste contrato dever&aacute;
        ser submetida &agrave; aprecia&ccedil;&atilde;o do Conselho de Arbitragem a ser escolhido pelas
        partes e obedecer&aacute;, quanto aos dispositivos legais interferentes, a seguinte
        preval&ecirc;ncia hier&aacute;rquica: Constitui&ccedil;&atilde;o Federal, Lei n&ordm;. 9.307/96 e, na ordem
        de sua indica&ccedil;&atilde;o, as leis indicadas na Conven&ccedil;&atilde;o Arbitral das partes;
      <BR>
	  <u>Cl&aacute;usula 17&ordm;</u>. As custas do procedimento arbitral ser&atilde;o, a
        princ&iacute;pio, de responsabilidade do Solicitante, sem que isso signifique
        absolutamente nenhum privil&eacute;gio em preju&iacute;zo do Solicitado. Caso a senten&ccedil;a
        arbitral homologue o acordo, a parte Solicitada ressarcir&aacute; a Solicitante da
        metade das custas. Se a parte Solicitante sair vencedora na lide, &agrave; parte
        Solicitada caber&aacute; ressarci-la do total das custas;
		<br>
		<u>Cl&aacute;usula 18&ordm;</u>. O presente contrato cont&eacute;m a totalidade do acordo
        entre as partes com respeito ao seu objeto e deixa sem efeito qualquer outro
        acordo, seja expresso ou oral, assim como todas as demais comunica&ccedil;&otilde;es
        existentes entre as partes relacionadas com o objeto do presente contrato. E,
        por estarem de acordo com os termos do presente instrumento, as partes o
        assinam a presente via, na presen&ccedil;a de duas testemunhas, para todos os fins e
        efeitos de direito.</p>
      <p class='style15'>$row_reg[regiao], $dia de $mes de $ano. </p></td>
  </tr>
  <tr>
    <td colspan='5'><span class='style16'><br />
      <br />
    </span></td>
  </tr>
  <tr>
    <td><div align='center'><span class='style13'>_______________________________________________</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>____________________________________________</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>______________________________________________</span></div></td>
  </tr>
  <tr>
    <td height='27'><div align='center'><span class='style13'>CEDENTE</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>CONCEDENTE</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>$row_bol[nome]</span></div></td>
  </tr>
  <tr>
    <td colspan='5'></td>
  </tr>
  <tr>
    <td colspan='5'></td>
  </tr>


  <tr>
    <td colspan='5'></td>
  </tr>
</table>
<table width='70%' bgcolor='#FFFFFF' class='bordaescura1px'>
  <tr>
    <td><p align='center' class='style13'><br />
        <br />
        ______________________________________________<br />
      TESTEMUNHA</p>
      <p align='center' class='style13'>NOME:_________________________________</p>
      <p align='center' class='style13'>RG:____________________________________</p>
    <p align='center' class='style13'>CPF:&nbsp;__________________________________</p></td>
    <td><p align='center' class='style13'><br />
        <br />
        ______________________________________________<br />
      TESTEMUNHA</p>
      <p align='center' class='style13'>NOME:_________________________________</p>
      <p align='center' class='style13'>RG:____________________________________</p>
    <p align='center' class='style13'>CPF:&nbsp;__________________________________</p></td>
  </tr>
</table>
<table width='60' border='0' cellspacing='0' cellpadding='0'>
        <tr>
          <td width='30'>
		  <a href=\"javascript:mudaFonte('menos');\" style='font-weight: bold; text-decoration: none; color: #000000;  font-size:12px; text-align: right;'>A- </a></td>
          <td width='30'><a href=\"javascript:mudaFonte('mais');\" style='font-weight: bold; text-decoration: none; color: #000000;  font-size:16px; text-align: right;'> A+</a></td>
        </tr>
    </table>
</div>
</center>
</body>
</html>

";

}
?>