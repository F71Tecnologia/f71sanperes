<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../conn.php";
include('../funcoes.php');
include('../wfunction.php');


$regiao = $_REQUEST['regiao'];
$id_prestador = $_REQUEST['prestador'];



$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "1"){

print "

<script>

alert(\"Voc� n�o pode imprimir este CONTRATO DE PRESTA��O DE SERVI�OS sem ter feito a ABERTURA DE PROCESSO!\");

window.close();

</script>";

}else{



$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_prestador[id_projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);


$data = date("d/m/Y");
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_prestador[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master= ".$row_regiao['id_master']);
$row_master = mysql_fetch_assoc($qr_master);

$dia = substr($row_prestador['contratado_em'], 8,2);
$mes = intval(substr($row_prestador['contratado_em'], 5,2));
$ano = substr($row_prestador['contratado_em'], 0,4);
$meses = array("","janeiro","fevereiro","mar�o","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");

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
echo '<img src="../imagens/logomaster'.$row_master['id_master'].'.gif"/>'; 

?>
     <!-- <img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--><br />

        </center>

    </div>

     <h3 align="center"> CONTRATO DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</h3>

      <p align="right" class="style17">
          <span class="style12">Processo n. <?php echo $row_prestador['numero']; ?> </span>
      </p>



     <p> 
         Pelo presente instrumento particular, de um lado <strong><?php echo $row_master['razao']; ?></strong>, 
         pessoa jur�dica de direito privado, inscrito no CNPJ sob o n� <?php echo$row_master['cnpj']; ?>,
         localizado na <?php echo $row_master['endereco']; ?> doravante denominado <strong>CONTRATANTE</strong>, e de outro lado,
         <?php echo $row_prestador['c_razao']; ?>, pessoa jur�dica de direito privado,
         inscrito no CNPJ sob o n� <?php echo $row_prestador['c_cnpj']; ?>, com sede na <?php echo $row_prestador['c_endereco'];?>, 
         doravante denominada <strong>CONTRATADA</strong>;
     </p>
     
     <p>
         Firmam entre si, o presente contrato de presta��o de servi�os, mediante as seguintes cl�usulas e condi��es:
     </p>


    <h3>Cl�usula Primeira - OBJETO</h3>

    <ul>
        <li>
            <p>
                <strong>1.1 - </strong> O objeto do presente Contrato refere-se � disponibiliza��o de m�o de obra especializada na �rea m�dica para presta��o de plant�es
                m�dicos na <?php echo $row_projeto['nome']; ?>, em parceira com a Prefeitura de <?php echo $row_projeto['cidade']; ?> 
            </p>
        </li>   
    </ul>
      
    <h3>Cl�usula Segunda - PRAZO</h3>
      <ul>
      	<li>
            <p>
                <strong>2.1 - </strong> O presente Contrato ser� por prazo indeterminado,
                iniciando sua vig�ncia a partir do dia <?php echo $dia; ?> de <?php echo $meses[$mes]; ?> de <?php echo $ano; ?>,
                podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja pr�via comunica��o expressa,
                com anteced�ncia m�nima de 30 (trinta) dias.
            </p>
        </li>
      </ul>
      
    <h3>Cl�usula Terceira - DA PRESTA��O DE SERVI�OS</h3>
    <ul> 
    	<li>
            <p>
                <strong>3.1 - </strong> A <strong>CONTRATADA</strong> prestar� os servi�os objeto do presente Contrato de forma aut�noma e
                sem qualquer v�nculo de natureza trabalhista, previdenci�ria e tribut�ria;
            </p>
        </li>
        <li>
            <p>
                <strong>3.2 - </strong> A <strong>CONTRATADA</strong> dever� prestar os servi�os objeto da presente contrata��o, 
                dentro das depend�ncias da <strong>CONTRATANTE</strong>, em car�ter n�o habitual.
            </p>
        </li>
    </ul>
    
    <h3>Cl�usula Quarta - REMUNERA��O</h3>
    
    <ul>	
    	<li>
            <p>
                <strong>4.1 - </strong>	Em remunera��o pelos servi�os profissionais ora contratados, ser� devida a import�ncia fixa mensal de
                R$ <?php echo number_format($row_prestador['valor'], 2, ',', '.') ?> 
                (<?php echo valor_extenso(number_format($row_prestador['valor'],2,',',''));  ?> reais) a serem pagos pagos at� o dia 05 (cinco) de cada m�s,
                via dep�sito em conta corrente de titularidade da <strong>CONTRATADA</strong>, cujos dados s�o os seguintes:
            </p>        
        </li>
        <li>
            <strong>BANCO:</strong> <?php echo $row_prestador['nome_banco']?>
        </li>
        <li>
            <strong>Ag�ncia:</strong> <?php echo $row_prestador['agencia']?>
        </li>
        <li>
            <strong>Conta Corrente:</strong> <?php echo $row_prestador['conta']?>
        </li>
        <li>
            <strong>Titular:</strong> <?php echo $row_prestador['c_razao']?>
        </li>
        <li>
            <strong>CNPJ:</strong> <?php echo $row_prestador['c_cnpj']?>
        </li>
        <li>
            <p>
                <strong>Par�grafo Primeiro </strong> - Para que a <strong>CONTRATANTE</strong> possa proceder ao pagamento da remunera��o prevista na cl�usula 5.1,
                a <strong>CONTRATADA</strong> dever� encaminhar � <strong>CONTRATANTE</strong> a respectiva Nota Fiscal de Presta��o de Servi�os,
                acompanhada de relat�rio detalhado contendo a descri��o dos servi�os prestados, at� o dia 02 (dois) de cada m�s.
            </p>
        </li>
    </ul>
    
    
    <h3>Cl�usula Quinta - REAJUSTE ANUAL</h3>
    
    <ul>
    	<li>
        	<p>
                    <strong>5.1</strong> - Decorrido um prazo de 12 (doze) meses da presente contrata��o e,
                    havendo interesse da <strong>CONTRATANTE</strong> na continuidade da presta��o dos servi�os da <strong>CONTRATADA</strong>,
                    o valor da remunera��o mensalmente recebida dever� ser reajustado anualmente, tendo como �ndice indexador o IPCA,
                    ou qualquer outro que vier oficialmente a substitu�-lo. A substitui��o do �ndice indexador do reajuste em quest�o
                    dever� ser alvo da elabora��o de Aditivo contratual devidamente assinado pelas Partes.
                </p>
        </li>
    </ul>
    
    <h3>Cl�usula Sexta - TRIBUTOS</h3>
    
    <ul>
    	<li>
            <p>
                <strong>6.1</strong> - Todos os tributos federais, estaduais ou municipais que incidam sobre a presta��o dos servi�os objeto do presente Contrato,
                ser�o de exclusiva responsabilidade da <strong>CONTRATADA</strong>,
                cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela <strong>CONTRATANTE</strong>.
            </p>
        </li>
    </ul>
    
    <h3>Cl�usula S�tima - RESPONSABILIDADE CIVIL</h3>
    <ul>	
    	<li>
            <p>
                <strong>7.1</strong> - A <strong>CONTRATADA</strong> assume integral responsabilidade, independente de culpa,
                por todas e quaisquer perdas e danos que seus s�cios,
                empregados e prepostos e demais trabalhadores por ela contratados para a presta��o dos servi�os causarem,
                volunt�ria ou involuntariamente, � <strong>CONTRATANTE</strong>, bem como aos seus empregados e quaisquer terceiros lesados,
                at� o integral ressarcimento pelas perdas e danos causados.
            </p>
        </li>
    </ul>
    
    <h3>Cl�usula Oitava - CONFIDENCIALIDADE</h3>
    <ul>
    	<li>
            <p>
                <strong>8.1</strong> - A <strong>CONTRATADA</strong>, por si, por seus prepostos e empregados,
                obriga-se a manter absoluto sigilo,
                durante toda a vig�ncia do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento,
                sobre todas as informa��es confidenciais, de uso exclusivo da <strong>CONTRATANTE</strong>,
                obtidas em raz�o do exerc�cio direto ou indireto de suas atividades.
            </p>
        </li>
        <li>
            <p>
                <strong>8.2</strong> - Para os fins do termo mencionado na cl�usula anterior,
                <i>"Informa��o Confidencial"</i> significa qualquer informa��o relacionada aos projetos e estudos da <strong>CONTRATANTE</strong>,
                incluindo, sem se limitar a: pesquisas, relat�rios, 
                avalia��es e pareceres elaborados com base em qualquer Informa��o tida como confidencial pela <strong>CONTRATANTE</strong>, 
                senhas, estrat�gias, segredos comerciais e propriedade intelectual, 
                os quais a <strong>CONTRATADA</strong> possa ter acesso por e-mail, carta, correspond�ncia, 
                telefone, <i>conference call</i> ou em reuni�es e encontros realizados em nome da <strong>CONTRATANTE</strong>.
            </p>
        </li>
        <li>
            <p>
                <strong>8.3</strong> - A <strong>CONTRATADA</strong> concorda que todos os segredos e
                informa��es confidenciais aos quais tenha tido acesso, 
                em raz�o da presta��o dos servi�os ora contratados, s�o de propriedade da <strong>CONTRATANTE</strong>, 
                obrigando-se a devolv�-las imediatamente � <strong>CONTRATANTE</strong>, quando da rescis�o do presente Contrato.
            </p>
        </li>
        <li>
            <p>
                <strong>8.4</strong> - Caso a <strong>CONTRATADA</strong> descumpra a obriga��o elencada na cl�usula s�tima,
                arcar� com uma multa indenizat�ria em favor da <strong>CONTRATANTE</strong>, cujo valor ser� apurado pela <strong>CONTRATANTE</strong>,
                no momento do conhecimento da infra��o, a seu exclusivo crit�rio.
            </p>
        </li>
    </ul>
    
    
     <h3>Cl�usula Nona - RESCIS�O</h3>
    <ul>
    	<li>
            <p>
                <strong>9.1</strong> - A <strong>CONTRATANTE</strong> poder� rescindir o presente Contrato nas seguintes hip�teses:
            </p>
        </li>
        <ul>
            <li>
                <p>
                    <strong>9.1.1</strong> - des�dia da <strong>CONTRATADA</strong> no cumprimento das obriga��es assumidas para com a <strong>CONTRATANTE</strong> e terceiros;
                </p>
                <p>
                    <strong>9.1.2</strong> - caso a <strong>CONTRATADA</strong>ong pratique atos que atinjam a imagem comercial da <strong>CONTRATANTE</strong> perante terceiros;
                </p>
                <p>
                    <strong>9.1.3</strong> - caso a <strong>CONTRATADA</strong> desrespeite as cl�usulas previstas no presente contrato;
                </p>
                <p>
                    <strong>9.1.4</strong> - a qualquer tempo e por qualquer motivo,
                    desde que comunique a <strong>CONTRATADA</strong> de tal inten��o, por escrito, com anteced�ncia m�nima de 30 (trinta) dias.
                </p>
            </li>
        </ul>
        <li>
            <p>
                <strong>9.2</strong> - A <strong>CONTRATADA</strong> poder� rescindir o presente Contrato nas seguintes circunst�ncias:
            </p>
        </li>
        <ul>
            <li>
                <p>
                    <strong>9.2.1</strong> - quando a <strong>CONTRATANTE</strong> exigir da <strong>CONTRATADA</strong>
                    atividade que exceda a presta��o dos servi�os objeto do presente contrato;
                </p>
                <p>
                    <strong>9.2.2</strong> - caso a <strong>CONTRATANTE</strong> descumpra quaisquer das cl�usulas previstas no presente Contrato;
                </p>
                <p>
                    <strong>9.2.3</strong> - caso haja decreta��o de fal�ncia, concordata, insolv�ncia ou recupera��o judicial da <strong>CONTRATANTE</strong>;
                </p>
                <p>
                    <strong>9.2.4</strong> - por motivos de for�a maior que inviabilizem a continuidade da presta��o dos servi�os em quest�o;
                </p>
                <p>
                    <strong>9.2.5</strong> - a qualquer tempo e por qualquer motivo, desde que comunique a <strong>CONTRATANTE</strong> de tal inten��o,
                    por escrito, com anteced�ncia m�nima de 30 (trinta) dias.
                </p>
            </li>
        </ul>
        <li>
            <p>
                <strong>9.3</strong> - A rescis�o do presente Contrato n�o extingue os direitos e obriga��es que as Partes tenham entre si e perante terceiros,
                adquiridas anteriormente.
            </p>
        </li>
    </ul>


    <h3>Cl�usula D�cima - INDEPEND�NCIA ENTRE AS PARTES</h3>
    
    <ul>
    	<li>
            <p>
                <strong>10.1</strong> - A <strong>CONTRATADA</strong> � a �nica respons�vel pelas reclama��es trabalhistas,
                previdenci�rias, fiscais e securit�rias, incluindo-se aquelas decorrentes de modifica��es na legisla��o em vigor,
                relativamente aos seus empregados e prepostos, ou terceiros por ela contratados,
                envolvidos direta ou indiretamente na presta��o dos servi�os objeto do presente Contrato.
            </p>
        </li>
    </ul>            
    
    <h3>Cl�usula D�cima Primeira - DISPOSI��ES GERAIS</h3>
    
    <ul>
    	<li>
            <p>
                <strong>11.1 - Notifica��es: </strong> Todas as notifica��es e
                comunica��es relativas a este Contrato ser�o feitas atrav�s dos gestores das Partes e 
                enviadas para os endere�os indicados no pre�mbulo do presente Contrato.
            </p>
        </li>
        <li>
            <p>
                <strong>11.2 - Nova��o: </strong> O n�o exerc�cio, pelas Partes,
                de quaisquer dos direitos ou prerrogativas previstos neste Contrato,
                ou mesmo na legisla��o aplic�vel, ser� tido como ato de mera liberalidade,
                n�o constituindo altera��o ou nova��o das obriga��es ora estabelecidas,
                cujo cumprimento poder� ser exigido a qualquer tempo,
                independentemente de comunica��o pr�via � Parte.
            </p>
        </li>
        <li>
            <p>
                <strong>11.3 - Caso Fortuito e For�a Maior: </strong> Nenhuma das Partes ser� respons�vel por 
                descumprimento de suas obriga��es contratuais em conseq��ncia de caso fortuito ou for�a maior,
                nos termos da legisla��o em vigor, devendo, para tanto,
                comunicar a ocorr�ncia de tal fato de imediato � outra Parte e informar os efeitos danosos do evento.
            </p>
            <p>
                Constatada a ocorr�ncia de caso fortuito ou de for�a maior, ficar�o suspensas,
                enquanto essa perdurar, as obriga��es que as Partes ficarem impedidas de cumprir.
            </p>
        </li>
        <li>
            <p>
                <strong>11.4 - Subcontrata��o e Cess�o: </strong> � vedado � <strong>CONTRATADA</strong> a subcontrata��o ou cess�o,
                total ou parcial, dos direitos e obriga��es oriundos e/ou decorrentes deste Contrato,
                inclusive seus cr�ditos, sem a pr�via e expressa autoriza��o da <strong>CONTRATANTE</strong>.
            </p>
        </li>
        <li>
            <p>
                <strong>11.5 - Aditivos: </strong> Este Contrato s� poder� ser alterado,
                em qualquer de suas disposi��es, mediante a celebra��o, 
                por escrito, de termo aditivo contratual assinado por ambas as Partes.
            </p>
        </li>
    </ul>
    
    <h3>Cl�usula D�cima Segunda - FORO</h3>
    
    <ul>
    	<li>
            <p>
                <strong>12.1</strong> - Elegem as partes o Foro da Comarca do Rio de Janeiro, 
                Estado do Rio de janeiro, 
                para dirimir quaisquer controv�rsias relacionadas ao presente Contrato, 
                prevalecendo este sobre qualquer outro, por mais privilegiado que seja.
            </p>
            <p>
                E por estarem assim justos e contratados, 
                assinam o presente em duas vias de igual forma e teor, 
                na presen�a de duas testemunhas, 
                para que possa produzir todos os seus efeitos de direito.
            </p>
        </li>
    </ul>
    
    
    <p style="margin-top:40px;text-align:center;">
   	
	<?php
            echo  $row_master['municipio'].', '.date('d').' de '.$meses[date('n')].' de '.date('Y');	
        ?> 
        
    </p>    
  



<table width="100%" style="margin-top:70px;">
	<tr>
    	<td align="center"><strong>CONTRATANTE</strong></td>
        <td align="center"><strong>CONTRATADA</strong></td>
    </tr>
    <tr>
    	<td align="center"><?php 
		echo $row_master['razao']?></td>
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



<p style="margin-top:475px;">&nbsp;</p>
<table width="100%"><tr><td align="right"><b>6/6</b></td></tr></table>         
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

