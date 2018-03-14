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

alert(\"Você não pode imprimir este CONTRATO DE PRESTAÇÃO DE SERVIÇOS sem ter feito a ABERTURA DE PROCESSO!\");

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
$meses = array("","janeiro","fevereiro","março","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");

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
         pessoa jurídica de direito privado, inscrito no CNPJ sob o nº <?php echo$row_master['cnpj']; ?>,
         localizado na <?php echo $row_master['endereco']; ?> doravante denominado <strong>CONTRATANTE</strong>, e de outro lado,
         <?php echo $row_prestador['c_razao']; ?>, pessoa jurídica de direito privado,
         inscrito no CNPJ sob o nº <?php echo $row_prestador['c_cnpj']; ?>, com sede na <?php echo $row_prestador['c_endereco'];?>, 
         doravante denominada <strong>CONTRATADA</strong>;
     </p>
     
     <p>
         Firmam entre si, o presente contrato de prestação de serviços, mediante as seguintes cláusulas e condições:
     </p>


    <h3>Cláusula Primeira - OBJETO</h3>

    <ul>
        <li>
            <p>
                <strong>1.1 - </strong> O objeto do presente Contrato refere-se à disponibilização de mão de obra especializada na área médica para prestação de plantões
                médicos na <?php echo $row_projeto['nome']; ?>, em parceira com a Prefeitura de <?php echo $row_projeto['cidade']; ?> 
            </p>
        </li>   
    </ul>
      
    <h3>Cláusula Segunda - PRAZO</h3>
      <ul>
      	<li>
            <p>
                <strong>2.1 - </strong> O presente Contrato será por prazo indeterminado,
                iniciando sua vigência a partir do dia <?php echo $dia; ?> de <?php echo $meses[$mes]; ?> de <?php echo $ano; ?>,
                podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja prévia comunicação expressa,
                com antecedência mínima de 30 (trinta) dias.
            </p>
        </li>
      </ul>
      
    <h3>Cláusula Terceira - DA PRESTAÇÃO DE SERVIÇOS</h3>
    <ul> 
    	<li>
            <p>
                <strong>3.1 - </strong> A <strong>CONTRATADA</strong> prestará os serviços objeto do presente Contrato de forma autônoma e
                sem qualquer vínculo de natureza trabalhista, previdenciária e tributária;
            </p>
        </li>
        <li>
            <p>
                <strong>3.2 - </strong> A <strong>CONTRATADA</strong> deverá prestar os serviços objeto da presente contratação, 
                dentro das dependências da <strong>CONTRATANTE</strong>, em caráter não habitual.
            </p>
        </li>
    </ul>
    
    <h3>Cláusula Quarta - REMUNERAÇÃO</h3>
    
    <ul>	
    	<li>
            <p>
                <strong>4.1 - </strong>	Em remuneração pelos serviços profissionais ora contratados, será devida a importância fixa mensal de
                R$ <?php echo number_format($row_prestador['valor'], 2, ',', '.') ?> 
                (<?php echo valor_extenso(number_format($row_prestador['valor'],2,',',''));  ?> reais) a serem pagos pagos até o dia 05 (cinco) de cada mês,
                via depósito em conta corrente de titularidade da <strong>CONTRATADA</strong>, cujos dados são os seguintes:
            </p>        
        </li>
        <li>
            <strong>BANCO:</strong> <?php echo $row_prestador['nome_banco']?>
        </li>
        <li>
            <strong>Agência:</strong> <?php echo $row_prestador['agencia']?>
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
                <strong>Parágrafo Primeiro </strong> - Para que a <strong>CONTRATANTE</strong> possa proceder ao pagamento da remuneração prevista na cláusula 5.1,
                a <strong>CONTRATADA</strong> deverá encaminhar à <strong>CONTRATANTE</strong> a respectiva Nota Fiscal de Prestação de Serviços,
                acompanhada de relatório detalhado contendo a descrição dos serviços prestados, até o dia 02 (dois) de cada mês.
            </p>
        </li>
    </ul>
    
    
    <h3>Cláusula Quinta - REAJUSTE ANUAL</h3>
    
    <ul>
    	<li>
        	<p>
                    <strong>5.1</strong> - Decorrido um prazo de 12 (doze) meses da presente contratação e,
                    havendo interesse da <strong>CONTRATANTE</strong> na continuidade da prestação dos serviços da <strong>CONTRATADA</strong>,
                    o valor da remuneração mensalmente recebida deverá ser reajustado anualmente, tendo como índice indexador o IPCA,
                    ou qualquer outro que vier oficialmente a substituí-lo. A substituição do índice indexador do reajuste em questão
                    deverá ser alvo da elaboração de Aditivo contratual devidamente assinado pelas Partes.
                </p>
        </li>
    </ul>
    
    <h3>Cláusula Sexta - TRIBUTOS</h3>
    
    <ul>
    	<li>
            <p>
                <strong>6.1</strong> - Todos os tributos federais, estaduais ou municipais que incidam sobre a prestação dos serviços objeto do presente Contrato,
                serão de exclusiva responsabilidade da <strong>CONTRATADA</strong>,
                cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela <strong>CONTRATANTE</strong>.
            </p>
        </li>
    </ul>
    
    <h3>Cláusula Sétima - RESPONSABILIDADE CIVIL</h3>
    <ul>	
    	<li>
            <p>
                <strong>7.1</strong> - A <strong>CONTRATADA</strong> assume integral responsabilidade, independente de culpa,
                por todas e quaisquer perdas e danos que seus sócios,
                empregados e prepostos e demais trabalhadores por ela contratados para a prestação dos serviços causarem,
                voluntária ou involuntariamente, à <strong>CONTRATANTE</strong>, bem como aos seus empregados e quaisquer terceiros lesados,
                até o integral ressarcimento pelas perdas e danos causados.
            </p>
        </li>
    </ul>
    
    <h3>Cláusula Oitava - CONFIDENCIALIDADE</h3>
    <ul>
    	<li>
            <p>
                <strong>8.1</strong> - A <strong>CONTRATADA</strong>, por si, por seus prepostos e empregados,
                obriga-se a manter absoluto sigilo,
                durante toda a vigência do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento,
                sobre todas as informações confidenciais, de uso exclusivo da <strong>CONTRATANTE</strong>,
                obtidas em razão do exercício direto ou indireto de suas atividades.
            </p>
        </li>
        <li>
            <p>
                <strong>8.2</strong> - Para os fins do termo mencionado na cláusula anterior,
                <i>"Informação Confidencial"</i> significa qualquer informação relacionada aos projetos e estudos da <strong>CONTRATANTE</strong>,
                incluindo, sem se limitar a: pesquisas, relatórios, 
                avaliações e pareceres elaborados com base em qualquer Informação tida como confidencial pela <strong>CONTRATANTE</strong>, 
                senhas, estratégias, segredos comerciais e propriedade intelectual, 
                os quais a <strong>CONTRATADA</strong> possa ter acesso por e-mail, carta, correspondência, 
                telefone, <i>conference call</i> ou em reuniões e encontros realizados em nome da <strong>CONTRATANTE</strong>.
            </p>
        </li>
        <li>
            <p>
                <strong>8.3</strong> - A <strong>CONTRATADA</strong> concorda que todos os segredos e
                informações confidenciais aos quais tenha tido acesso, 
                em razão da prestação dos serviços ora contratados, são de propriedade da <strong>CONTRATANTE</strong>, 
                obrigando-se a devolvê-las imediatamente à <strong>CONTRATANTE</strong>, quando da rescisão do presente Contrato.
            </p>
        </li>
        <li>
            <p>
                <strong>8.4</strong> - Caso a <strong>CONTRATADA</strong> descumpra a obrigação elencada na cláusula sétima,
                arcará com uma multa indenizatória em favor da <strong>CONTRATANTE</strong>, cujo valor será apurado pela <strong>CONTRATANTE</strong>,
                no momento do conhecimento da infração, a seu exclusivo critério.
            </p>
        </li>
    </ul>
    
    
     <h3>Cláusula Nona - RESCISÃO</h3>
    <ul>
    	<li>
            <p>
                <strong>9.1</strong> - A <strong>CONTRATANTE</strong> poderá rescindir o presente Contrato nas seguintes hipóteses:
            </p>
        </li>
        <ul>
            <li>
                <p>
                    <strong>9.1.1</strong> - desídia da <strong>CONTRATADA</strong> no cumprimento das obrigações assumidas para com a <strong>CONTRATANTE</strong> e terceiros;
                </p>
                <p>
                    <strong>9.1.2</strong> - caso a <strong>CONTRATADA</strong>ong pratique atos que atinjam a imagem comercial da <strong>CONTRATANTE</strong> perante terceiros;
                </p>
                <p>
                    <strong>9.1.3</strong> - caso a <strong>CONTRATADA</strong> desrespeite as cláusulas previstas no presente contrato;
                </p>
                <p>
                    <strong>9.1.4</strong> - a qualquer tempo e por qualquer motivo,
                    desde que comunique a <strong>CONTRATADA</strong> de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.
                </p>
            </li>
        </ul>
        <li>
            <p>
                <strong>9.2</strong> - A <strong>CONTRATADA</strong> poderá rescindir o presente Contrato nas seguintes circunstâncias:
            </p>
        </li>
        <ul>
            <li>
                <p>
                    <strong>9.2.1</strong> - quando a <strong>CONTRATANTE</strong> exigir da <strong>CONTRATADA</strong>
                    atividade que exceda a prestação dos serviços objeto do presente contrato;
                </p>
                <p>
                    <strong>9.2.2</strong> - caso a <strong>CONTRATANTE</strong> descumpra quaisquer das cláusulas previstas no presente Contrato;
                </p>
                <p>
                    <strong>9.2.3</strong> - caso haja decretação de falência, concordata, insolvência ou recuperação judicial da <strong>CONTRATANTE</strong>;
                </p>
                <p>
                    <strong>9.2.4</strong> - por motivos de força maior que inviabilizem a continuidade da prestação dos serviços em questão;
                </p>
                <p>
                    <strong>9.2.5</strong> - a qualquer tempo e por qualquer motivo, desde que comunique a <strong>CONTRATANTE</strong> de tal intenção,
                    por escrito, com antecedência mínima de 30 (trinta) dias.
                </p>
            </li>
        </ul>
        <li>
            <p>
                <strong>9.3</strong> - A rescisão do presente Contrato não extingue os direitos e obrigações que as Partes tenham entre si e perante terceiros,
                adquiridas anteriormente.
            </p>
        </li>
    </ul>


    <h3>Cláusula Décima - INDEPENDÊNCIA ENTRE AS PARTES</h3>
    
    <ul>
    	<li>
            <p>
                <strong>10.1</strong> - A <strong>CONTRATADA</strong> é a única responsável pelas reclamações trabalhistas,
                previdenciárias, fiscais e securitárias, incluindo-se aquelas decorrentes de modificações na legislação em vigor,
                relativamente aos seus empregados e prepostos, ou terceiros por ela contratados,
                envolvidos direta ou indiretamente na prestação dos serviços objeto do presente Contrato.
            </p>
        </li>
    </ul>            
    
    <h3>Cláusula Décima Primeira - DISPOSIÇÕES GERAIS</h3>
    
    <ul>
    	<li>
            <p>
                <strong>11.1 - Notificações: </strong> Todas as notificações e
                comunicações relativas a este Contrato serão feitas através dos gestores das Partes e 
                enviadas para os endereços indicados no preâmbulo do presente Contrato.
            </p>
        </li>
        <li>
            <p>
                <strong>11.2 - Novação: </strong> O não exercício, pelas Partes,
                de quaisquer dos direitos ou prerrogativas previstos neste Contrato,
                ou mesmo na legislação aplicável, será tido como ato de mera liberalidade,
                não constituindo alteração ou novação das obrigações ora estabelecidas,
                cujo cumprimento poderá ser exigido a qualquer tempo,
                independentemente de comunicação prévia à Parte.
            </p>
        </li>
        <li>
            <p>
                <strong>11.3 - Caso Fortuito e Força Maior: </strong> Nenhuma das Partes será responsável por 
                descumprimento de suas obrigações contratuais em conseqüência de caso fortuito ou força maior,
                nos termos da legislação em vigor, devendo, para tanto,
                comunicar a ocorrência de tal fato de imediato à outra Parte e informar os efeitos danosos do evento.
            </p>
            <p>
                Constatada a ocorrência de caso fortuito ou de força maior, ficarão suspensas,
                enquanto essa perdurar, as obrigações que as Partes ficarem impedidas de cumprir.
            </p>
        </li>
        <li>
            <p>
                <strong>11.4 - Subcontratação e Cessão: </strong> É vedado à <strong>CONTRATADA</strong> a subcontratação ou cessão,
                total ou parcial, dos direitos e obrigações oriundos e/ou decorrentes deste Contrato,
                inclusive seus créditos, sem a prévia e expressa autorização da <strong>CONTRATANTE</strong>.
            </p>
        </li>
        <li>
            <p>
                <strong>11.5 - Aditivos: </strong> Este Contrato só poderá ser alterado,
                em qualquer de suas disposições, mediante a celebração, 
                por escrito, de termo aditivo contratual assinado por ambas as Partes.
            </p>
        </li>
    </ul>
    
    <h3>Cláusula Décima Segunda - FORO</h3>
    
    <ul>
    	<li>
            <p>
                <strong>12.1</strong> - Elegem as partes o Foro da Comarca do Rio de Janeiro, 
                Estado do Rio de janeiro, 
                para dirimir quaisquer controvérsias relacionadas ao presente Contrato, 
                prevalecendo este sobre qualquer outro, por mais privilegiado que seja.
            </p>
            <p>
                E por estarem assim justos e contratados, 
                assinam o presente em duas vias de igual forma e teor, 
                na presença de duas testemunhas, 
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

