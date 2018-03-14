<?php 
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {
      include "../conn.php";
	  include "../classes/regiao.php";
	  $nomemes = new regiao();

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];
$tela = $_REQUEST['tela'];
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Ficha de Cadastro de Participantes</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<?php switch($tela) {
	case 1:
?>
<body>
    <div id="corpo">
         <div id="topo">
              <?php include "include/topo.php"; ?>
         </div>
         <div id="conteudo">
              <h1 style="margin:70px;"><span>RELATÓRIOS</span> FICHA DE CADASTRO DE PARTICIPANTES</h1>

<form action="fichadecadastro.php" method="post" name="form" style="margin-bottom:200px;">

        Selecione o Tipo de Contratação:
        <select name="tipo" id="tipo" class="campotexto">
           <option value="2">CLT</option>
           <option value="3">Colaborador</option>
           <option value="4">Autônomo / PJ</option>
        </select>

    <label>
      <input type="hidden" name="pro" id="pro" value="<?=$pro?>">
      <input type="hidden" name="reg" id="reg" value="<?=$id_reg?>">
      <input type="hidden" name="tela" id="tela" value="2">
      <input type="submit" name="button" id="button" class="botao" value="Gerar Ficha">
    </label>
   
</form>
</div>
<div id="rodape"></div>
</div>

<?php
break;
case 2: ?>

<body style="background-color:#FFF; margin-top:-60px;">

<?php $tipo = $_REQUEST['tipo'];

     if(empty($_REQUEST['pagina'])) {
	     $intervalo = "20";
	     $ini_atual = "0";
	     $fim_atual = $ini_atual + $intervalo;
	     $pagina = "1";
     } else {
	     $pagina = $_REQUEST['pagina'];
	     $intervalo = "20";
	     $ini_atual = $intervalo * $pagina - $intervalo;
	     $fim_atual = $ini_atual + $intervalo;
     }

     if($tipo == "1" or $tipo == "3" or $tipo == "4") {
	     $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad , '%d/%m/%Y')as sis_data_cadastro ,date_format(data_rg , '%d/%m/%Y')as data_rg, date_format(data_ctps , '%d/%m/%Y')as data_ctps, date_format(dada_pis , '%d/%m/%Y')as dada_pis FROM autonomo where tipo_contratacao != '2' AND id_projeto='$pro' AND status = '1' LIMIT $ini_atual,$intervalo");
	     $result_bol_g = mysql_query("SELECT id_autonomo FROM autonomo WHERE tipo_contratacao != '2' AND id_projeto = '$pro' AND status = '1'");
     } else {
		 $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad, '%d/%m/%Y')as sis_data_cadastro, date_format(data_rg , '%d/%m/%Y')as data_rg, date_format(data_ctps , '%d/%m/%Y')as data_ctps, date_format(dada_pis , '%d/%m/%Y')as dada_pis FROM rh_clt where id_projeto = '$pro' AND status < '60' ORDER BY nome LIMIT $ini_atual,$intervalo");
         $result_bol_g = mysql_query("SELECT id_clt FROM rh_clt WHERE id_projeto = '$pro' AND status < '60'");  
	 }

    while($row = mysql_fetch_array($result_bol)) {

	$result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = $row[id_curso]", $conn);
	$row_bol3 = mysql_fetch_array($result_bol3);

	$result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = $row[id_curso]", $conn);
	$row_bol2 = mysql_fetch_array($result_bol2);

	$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'", $conn);
	$row_reg = mysql_fetch_array($result_reg);

	$result_curso = mysql_query("Select * from  curso where id_curso = '$row[id_curso]'", $conn);
	$row_curso = mysql_fetch_array($result_curso);

	$result_pro = mysql_query("Select * from  projeto where id_projeto = '$pro'", $conn);
	$row_pro = mysql_fetch_array($result_pro);

	$result_vale = mysql_query("Select * from vale where id_bolsista = '$row[0]'", $conn);
	$row_vale = mysql_fetch_array($result_vale);


	$result_banco = mysql_query("Select * from bancos where id_banco = '$row[banco]'");
	$row_banco = mysql_fetch_array($result_banco);
	
	$result_depende = mysql_query ("SELECT *,date_format(data1, '%d/%m/%Y')as data1 ,date_format(data2, '%d/%m/%Y')as data2, date_format(data3, '%d/%m/%Y')as data3, date_format(data4, '%d/%m/%Y')as data4 ,date_format(data5, '%d/%m/%Y')as data5 FROM dependentes WHERE id_clt = '$row[0]' AND id_projeto = '$pro'", $conn);

	$row_depende = mysql_fetch_array($result_depende);	

	echo $row_depende['nome'];

	$dia = date('d');
	$mes = date('m');
	$ano = date('Y');

	if($row['tipo_contratacao'] == "1") {
		    $vinculo_cad = "Autônomo";
	} elseif($row['tipo_contratacao'] == "2") {
			$vinculo_cad = "CLT";
	} elseif($row['tipo_contratacao'] == "3") {
			$vinculo_cad = "Colaborador";
	} elseif($row['tipo_contratacao'] == "4") {
			$vinculo_cad = "Autônomo / PJ";
	}

	if($row['status'] == "1" or $row['status'] == "10") {
		$status_bol = "Ativo";
	} else {
		$status_bol = "<font color=red>Desativado</font>";
	}

	$nomemes -> MostraMes($mes);
	$mes = $nomemes;
	
	?>
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px; page-break-after:always; margin-top:70px;">
  <tr>
    <td>
      <?php $RE_coope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$row[id_cooperativa]'");
            $ROWcoope = mysql_fetch_array($RE_coope);
	         if($tipo == "2") { ?>
        <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
      <?php } else {
		 if(!empty($ROWcoope['foto'])) { ?>     
	    <img src='../cooperativas/logos/coop_<?=$ROWcoope['0'].$ROWcoope['foto']?>' alt='' width='120' height='86' /> 
      <?php } } ?>
    </td>
    <td align="center">
       <strong>FICHA DE CADASTRO</strong><br>
       <?php if($tipo == "2") { 
                 echo $row_master['razao'];
             } else {
	            echo $ROWcoope['fantasia'];
             } ?>
       <table width="272" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="103" height="22" class="top">C&Oacute;DIGO</td>
              <td width="103" class="top">STATUS</td>
              <td width="103" class="top">VINCULO</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?php print "$row[campo3]"; ?></b></td>
              <td align="center"><b><?php print "$status_bol"; ?></b></td>
              <td align="center"><b><?php print "$vinculo_cad"; ?></b></td>
            </tr>
        </table>
    </td>
    <td align="right">
          <?php if($row['foto'] == "1"){
	                   $nome_imagem = $id_reg."_".$pro."_".$row['0'].".gif"; 
                } else {
	                   $nome_imagem = 'semimagem.gif';
                } 
           print "<img src='../fotos/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>"; ?>
    </td>
  </tr>
  <tr>
    <td colspan="3">
       <table class="relacao" style="width:100%; margin-top:10px;">
          <tr class="secao_pai">
            <td colspan="6">
              <strong>DADOS DO PARTICIPANTE</strong>
            </td>
          </tr>
          <tr class="secao">
            <td colspan="5">Participante</td>
            <td width="18%">Data de Entrada</td>
          </tr>
          <tr>
            <td colspan="5"><b><?php print "$row[nome]"; ?></b></td>
            <td><b><?php print "$row[data_entrada]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="6">Endere&ccedil;o</td>
          </tr>
          <tr>
            <td colspan="6"><b><?php print "$row[endereco]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="2">Bairro</td>
            <td colspan="2">Cidade</td>
            <td width="9%">Estado</td>
            <td>CEP</td>
          </tr>
          <tr>
            <td colspan="2"><b><?php print "$row[bairro]"; ?></b></td>
            <td colspan="2"><b><?php print "$row[cidade]"; ?></b></td>
            <td><b><?php print "$row[uf]"; ?></b></td>
            <td><b><?php print "$row[cep]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="2">Estado Civil</td>
            <td>Naturalidade</td>
            <td>Nacionalidade</td>
            <td>Telefone</td>
            <td>Data de Nascimento</td>
          </tr>
          <tr>
            <td colspan="2"><b><?php print "$row[civil]"; ?></b></td>
            <td><b><?php print "$row[naturalidade]"; ?></b></td>
            <td><b><?php print "$row[nacionalidade]"; ?></b></td>
            <td><b><?php print "$row[tel_fixo]"; ?></b></td>
            <td><b><?php print "$row[data_nasci]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="2">C&uacute;tis</td>
            <td width="14%">Estatura</td>
            <td width="14%">Peso</td>
            <td>Cabelo</td>
            <td>Olhos</td>
          </tr>
          <tr>
            <td colspan="2"><b><?php print "$row[defeito]"; ?></b></td>
            <td><b><?php print "$row[altura]"; ?></b></td>
            <td><b><?php print "$row[peso]"; ?></b></td>
            <td><b><?php print "$row[cabelos]"; ?></b></td>
            <td><b><?php print "$row[olhos]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="2">RG</td>
            <td>Data de Expedi&ccedil;&atilde;o</td>
            <td>&Oacute;rg&atilde;o Expedidor</td>
            <td>CPF</td>
            <td>CTPS</td>
          </tr>
          <tr>
            <td colspan="2"><b><?php print "$row[rg]"; ?></b></td>
            <td><b><?php print "$row[data_rg]"; ?></b></td>
            <td><b><?php print "$row[orgao] / $row[uf_ctps]"; ?></b></td>
            <td><b><?php print "$row[cpf]"; ?></b></td>
            <td><b><?php print "$row[campo1]"; ?> / <?php print "$row[uf]"; ?> /
                <?php if($tipo == "1" or $tipo == "3") {
		                print "$row_abol[data_ctps]";
  		              } else {
		                print "$row[data_ctps]";
		              } ?>
            </b></td>
          </tr>
          <tr class="secao">
            <td colspan="2">Cart. de Habilita&ccedil;&atilde;o</td>
            <td>Titulo de Eleitor</td>
            <td>Zona</td>
            <td>Se&ccedil;&atilde;o</td>
            <td>Certificado de Reservista</td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
            <td><b><?php print "$row[titulo]"; ?></b></td>
            <td><b><?php print "$row[zona]"; ?></b></td>
            <td><b><?php print "$row[secao]"; ?></b></td>
            <td><b><?php print "$row[reservista]"; ?></b></td>
          </tr>
          <tr class="secao_pai">
            <td colspan="6">FILIA&Ccedil;&Atilde;O</td>
          </tr>
          <tr class="secao">
            <td colspan="5">Pai</td>
            <td>Nacionalidade</td>
          </tr>
          <tr>
            <td colspan="5"><b><?php print "$row[pai]"; ?></b></td>
            <td><b><?php print "$row[nacionalidade_pai]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="5">M&atilde;e</td>
            <td>Nacionalidade</td>
          </tr>
          <tr>
            <td colspan="5"><b><?php print "$row[mae]"; ?></b></td>
            <td><b><?php print "$row[nacionalidade_mae]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="4">Escolaridade</td>
            <td>PIS</td>
            <td>PIS Cadastrado em:</td>
          </tr>
          <tr>
            <td colspan="4">
               <?php $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row[escolaridade]' AND status = 'on'");
                     $escolaridade = mysql_fetch_assoc($qr_escolaridade);
					 print "$escolaridade[nome]"; ?>
            </td>
            <td><b>
            <?php if($tipo == "1" or $tipo == "3") {
		                print "$row_abol[pis]"; 
  		          } else {
		                print "$row[pis]";
		          } ?>
            </b></td>
            <td><b>
              <?php if($tipo == "1" or $tipo == "3") {
		                  print "$row_abol[dada_pis]"; 
  		            } else {
		                  print "$row[dada_pis]";
		            } ?>
            </b></td>
          </tr>
          <tr class="secao">
            <td colspan="6">Dependentes</td>
          </tr>
          <tr>
            <td colspan="6" class="secao"><b><?php print "$row_depende[nome1] - $row_depende[data1]"; ?> / <?php print "$row_depende[nome2] - $row_depende[data2]"; ?> / <?php print "$row_depende[nome3] - $row_depende[data3]"; ?> / <?php print "$row_depende[nome4] - $row_depende[data4]"; ?> / <?php print "$row_depende[nome5] - $row_depende[data5]"; ?></b></td>
          </tr>          
          <tr class="secao_pai">
            <td colspan="6">DADOS DA FUN&Ccedil;&Atilde;O E HOR&Aacute;RIOS</td>
          </tr>
          <tr class="secao">
            <td colspan="6">Projeto</td>
          </tr>
          <tr>
            <td colspan="6">
            <b><?php $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$_POST[pro]'"); $projeto = mysql_fetch_assoc($qr_projeto); echo "$projeto[nome]"; ?></b>
            </td>
          </tr>
          <tr class="secao">
            <td colspan="2">Curso</td>
            <td>Retirada</td>
            <td>Mensal</td>
            <td><span class="style1">Horas/Dia </span></td>
            <td><span class="style1">Dias Produ&ccedil;&atilde;o</span></td>
          </tr>
          <tr>
            <td colspan="2"><b><?php print "$row_curso[nome]"; ?></b></td>
            <td>R$<b> <?php print number_format("$row_curso[salario]",2,',','.'); ?></b></td>
            <td><b><?php print "$row_horario[horas_mes]"; ?></b></td>
            <td><b><?php print "$total"; ?></b></td>
            <td><b><?php print "$row_horario[dias_semana]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td>Conta</td>
            <td>Ag&ecirc;ncia</td>
            <td colspan="4">Banco</td>
          </tr>
          <tr>
            <td><b><?php print "$row[conta]"; ?></b></td>
            <td><b><?php print "$row[agencia]"; ?></b></td>
            <td colspan="4"><b><?php print "$row_banco[nome]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td>Qtd. de &ocirc;nibus / Valor Transporte</td>
            <td>Tipo</td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr>
            <td><?php print "$row_vale[qnt1] - R$ $row_vale[valor1] / $row_vale[qnt2] - R$ $row_vale[valor2] / $row_vale[qnt3] - R$ $row_vale[valor3]/ $row_vale[qnt4] - R$ $row_vale[valor4]"; ?></td>
            <td><b>
              <?php if($row_vale['tipo_vale'] == "1") { 
		                 $tipovale = "Cart&atilde;o"; 
		            }  else { 
		                 $tipovale = "Papel";
		            }
		            print "$tipovale"; ?>
            </b></td>
            <td colspan="4">&nbsp;</td>
          </tr>
          <tr class="secao">
            <td colspan="6">Hor&aacute;rios</td>
          </tr>
          <tr>
            <td colspan="6">
              
            DE SEGUNDA &Agrave; SEXTA DAS:&nbsp;______:_____ &Agrave;S ______:_____ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            HORAS&nbsp;/&nbsp;INTERVALO: ______:_____ &Agrave;S ______:_____ <br>
            HORAS S&Aacute;BADO DAS: ______:_____ &Agrave;S ______:_____               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            HORAS&nbsp;/ INTERVALO: ______:_____ &Agrave;S ______:_____</td>
         </tr>
         <tr class="secao">
            <td colspan="6">Observa&ccedil;&otilde;es</td>
          </tr>
          <tr>
            <td colspan="6"><p align="center"><br>
            _________________________________________________________________________________<br><br>
            __________________________________________________________________________________
        </p></td>
        </tr>
        <tr>
          <td colspan="6">
             <table cellpadding="0" cellspacing="0" border="0" style="font-size:12px; text-align:center; width:100%;">
                  <tr>
                    <td width="15%">
                        <br>_________________<br>DATA
                    </td>
                    <td width="35%">
                        <br>__________________________________<br>
                        ASSINATURA</td>
                    <td width="15%">
                        <br>_________________<br>DATA
                    </td>
                    <td width="35%">
                        <br>__________________________________<br>                        ASSINATURA 
                        <?=$ROWcoope['fantasia']?></td>
                  </tr>
               </table>
            </td>
         </tr>
       </table>
    </td>
  </tr>
</table>
      <!---Aqui a página é quebrada-->
      <?php } ?>
      </td>
  </tr>
</table>
<?php

$total_registros = mysql_num_rows($result_bol_g);
$total_registros2 = mysql_num_rows($result_bol);

$cauculo_paginas = $total_registros / $intervalo;
$qnt_paginas = round($cauculo_paginas);
$t = $qnt_paginas * $intervalo;

if($total_registros > $t ){
	$qnt_paginas = $qnt_paginas + 1;
}

$fim_atual_f = $fim_atual - 1;
$fim_atual_f2 = $ini_atual + $total_registros2;

print "<div id='navegacao'>
           <table width='720' border='0' cellspacing='0' cellpadding='0' align='center'>
  <tr>
    <td align='left'>Mostrando Registros de $ini_atual - $fim_atual_f2 no total de $total_registros em $qnt_paginas páginas</td>
  </tr>
  <tr>
    <td align='left'>";

$resultado_pag = $pagina - 3;
$final_de_paginas = $qnt_paginas - 9;

if($qnt_paginas <= 10){ 

 $pagina_i = 1; 
 $ultima_pagina = $qnt_paginas;
 
} else {
	
  if($resultado_pag >= $final_de_paginas){
	  $antes = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=1&tela=2&tipo=$tipo' title=\"Primeira Página\"><<</a>";
      $pagina_i = $final_de_paginas; 
      $ultima_pagina = $pagina_i + 10 - 1;
	  
  } else {
	  
	  if($pagina == "1" or $pagina == "2" or $pagina == "3" or $pagina == "4"){
		  
	      $pagina_i = 1; 
          $ultima_pagina = $pagina_i + 10 - 1;
    	  $depois = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=$qnt_paginas&tela=2&tipo=$tipo' title=\"Ultima Página\">>></a>";
		  
		} elseif($pagina >= 5) {
			
		 $antes = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=1&tela=2&tipo=$tipo' title=\"Primeira Página\"><<</a>";
         $pagina_i = $pagina - 3; 
         $ultima_pagina = $pagina_i + 10 - 1;
		 $depois = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=$qnt_paginas&tela=2&tipo=$tipo' title=\"Ultima Página\">>></a>";
		 
    	}
  
  }
}
$i = $pagina_i;
print "$antes";
for ($i = $i; $i <= $ultima_pagina; $i++) {
    
	if($i == $pagina){
	print " $i ";
	} else {
	print " <a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=$i&tela=2&tipo=$tipo'>$i</a> ";
	}
}

print "$depois</td>
  </tr>
</table>
</div>
";
?>

<?php
break;
}
}
?>
</body>
</html>