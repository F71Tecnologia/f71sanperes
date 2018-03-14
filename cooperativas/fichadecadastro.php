<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
} else {

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];
$clt = $_REQUEST['bol'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '28' and id_clt = '$clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('28','$clt','$data_cad', '$user_cad')");
} else {
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '28'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad, '%d/%m/%Y')as data_cad,date_format(data_ctps, '%d/%m/%Y')as data_ctps,date_format(data_rg, '%d/%m/%Y')as data_rg FROM autonomo WHERE id_autonomo = '$clt' and status='1'");
$row = mysql_fetch_array($result_bol);

$result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso WHERE id_curso = '$row[id_curso]'");
$row_bol3 = mysql_fetch_array($result_bol3);

$result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso WHERE id_curso = '$row[id_curso]'");
$row_bol2 = mysql_fetch_array($result_bol2);

$result_reg = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row[id_regiao]'");
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$pro'");
$row_pro = mysql_fetch_array($result_pro);

$RE_coope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$row[id_cooperativa]'");
$ROWcoope = mysql_fetch_array($RE_coope);

if(empty($ROWcoope['foto'])) {
	$imagem = NULL;
} else {
	$imagem = "<img src='logos/coop_".$ROWcoope['0'].$ROWcoope['foto']."' alt='' width='120' height='86' />";
}

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '2' and id_clt = '$id_clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('2','$id_clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = 'id_clt' and tipo = '2'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS


/*
$result_abol = mysql_query("SELECT *,date_format(dada_pis, '%d/%m/%Y')as dada_pis ,date_format(data_ctps, '%d/%m/%Y')as data_ctps FROM a$tab WHERE id_bolsista = '$id_bol'");
$row_abol = mysql_fetch_array($result_abol);
*/

$result_vale = mysql_query("SELECT * FROM vale WHERE id_bolsista = '$row[id_antigo]' AND id_projeto = '$pro'");
$row_vale = mysql_fetch_array($result_vale);

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[banco]'");
$row_banco = mysql_fetch_array($result_banco);

$result_depende = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1, date_format(data2, '%d/%m/%Y') AS data2, date_format(data3, '%d/%m/%Y') AS data3, date_format(data4, '%d/%m/%Y') AS data4, date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes WHERE id_bolsista = '$row[id_autonomo]' AND nome1 != '' AND id_regiao = '$id_reg' AND id_projeto = '$pro' ORDER BY nome");
$row_depende = mysql_fetch_array($result_depende);	

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
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Ficha de Cadastro Cooperado</title>
<link href="../relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px; margin-top:30px; margin-bottom:10px;">
  <tr>
    <td>
       <?=$imagem?>
    </td>
    <td align="center">
       <strong>FICHA DE CADASTRO</strong><br>
       <?=$ROWcoope['fantasia']?>
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
          <?php if($row['foto'] == "1") {
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
            <td colspan="5">
              <strong>DADOS DO PARTICIPANTE</strong>
            </td>
          </tr>
          <tr class="secao">
            <td colspan="4">Participante</td>
            <td width="18%">Data de Entrada</td>
          </tr>
          <tr>
            <td colspan="4"><b><?php print "$row[nome]"; ?></b></td>
            <td><b><?php print "$row[data_entrada]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="4">Endere&ccedil;o</td>
            <td>CEP</td>
          </tr>
          <tr>
            <td colspan="4"><b><?php print "$row[endereco]"; ?>, <?php print "$row[bairro]"; ?>, <?php print "$row[cidade]"; ?> - <?php print "$row[uf]"; ?></b></td>
            <td><b><?php print "$row[cep]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td>Estado Civil</td>
            <td>Naturalidade</td>
            <td>Nacionalidade</td>
            <td width="9%">Telefone</td>
            <td>Data de Nascimento</td>
          </tr>
          <tr>
            <td><b><?php print "$row[civil]"; ?></b></td>
            <td><b><?php print "$row[naturalidade]"; ?></b></td>
            <td><b><?php print "$row[nacionalidade]"; ?></b></td>
            <td><b><?php print "$row[tel_fixo]"; ?></b></td>
            <td><b><?php print "$row[data_nasci]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="3">Escolaridade</td>
            <td>PIS</td>
            <td>PIS Cadastrado em:</td>
          </tr>
          <tr>
            <td colspan="3">
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
            <td>C&uacute;tis</td>
            <td width="14%">Estatura</td>
            <td width="14%">Peso</td>
            <td>Cabelo</td>
            <td>Olhos</td>
          </tr>
          <tr>
            <td><b><?php print "$row[defeito]"; ?></b></td>
            <td><b><?php print "$row[altura]"; ?></b></td>
            <td><b><?php print "$row[peso]"; ?></b></td>
            <td><b><?php print "$row[cabelos]"; ?></b></td>
            <td><b><?php print "$row[olhos]"; ?></b></td>
          </tr>
          <tr>
            <td colspan="5">
                <table cellpadding="0" cellspacing="0" border="0" style="font-size:12px; width:713px; margin-left:-5px;">
                   <tr class="secao">
                      <td width="10%">RG</td>
                      <td width="10%">Expedi&ccedil;&atilde;o</td>
                      <td width="10%">&Oacute;rg&atilde;o</td>
                      <td width="10%">CPF</td>
                      <td width="20%">CTPS</td>
                      <td width="10%">Habilita&ccedil;&atilde;o</td>
                      <td width="10%">Titulo</td>
                      <td width="5%">Zona</td>
                      <td width="5%">Se&ccedil;&atilde;o</td>
                      <td width="10%">Reservista</td>
                   </tr>
                   <tr>
                     <td><b><?php print "$row[rg]"; ?></b></td>
                     <td><b><?php print "$row[data_rg]"; ?></b></td>
                     <td><b><?php print "$row[orgao] / $row[uf_ctps]"; ?></b></td>
                     <td><b><?php print "$row[cpf]"; ?></b></td>
                     <td><b><?php print "$row[campo1]"; ?> / <?php print "$row[uf]"; ?> /
                            <?php if($tipo == "1" or $tipo == "3") {
		                              print "$row_abol[data_ctps]";
  		                          } else {
		                              print "$row[data_ctps]";
		                          } ?></b></td>
                     <td>&nbsp;</td>
                     <td><b><?php print "$row[titulo]"; ?></b></td>
                     <td><b><?php print "$row[zona]"; ?></b></td>
                     <td><b><?php print "$row[secao]"; ?></b></td>
                     <td><b><?php print "$row[reservista]"; ?></b></td>
                  </tr>
                </table>
             </td>
           </tr>
          <tr class="secao_pai">
            <td colspan="5">FILIA&Ccedil;&Atilde;O</td>
          </tr>
          <tr class="secao">
            <td colspan="4">Pai</td>
            <td>Nacionalidade</td>
          </tr>
          <tr>
            <td colspan="4"><b><?php print "$row[pai]"; ?></b></td>
            <td><b><?php print "$row[nacionalidade_pai]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="4">M&atilde;e</td>
            <td>Nacionalidade</td>
          </tr>
          <tr>
            <td colspan="4"><b><?php print "$row[mae]"; ?></b></td>
            <td><b><?php print "$row[nacionalidade_mae]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td colspan="5">Dependentes</td>
          </tr>
          <tr>
            <td colspan="5"><b>
			<?php if(!empty($row_depende['nome1'])) { 
			         print "$row_depende[nome1] - $row_depende[data1]"; } 
				  if(!empty($row_depende['nome2'])) { 
				     print " / $row_depende[nome2] - $row_depende[data2]"; } 
			      if(!empty($row_depende['nome3'])) { 
				     print " / $row_depende[nome3] - $row_depende[data3]"; } 
				  if(!empty($row_depende['nome4'])) { 
				     print " / $row_depende[nome4] - $row_depende[data4]"; } 
				  if(!empty($row_depende['nome5'])) { 
				     print " / $row_depende[nome5] - $row_depende[data5]"; } ?></b></td>
          </tr>
          <tr class="secao_pai">
            <td colspan="5">DADOS DA FUN&Ccedil;&Atilde;O E HOR&Aacute;RIOS</td>
          </tr>
          <tr class="secao">
            <td colspan="5">Projeto</td>
          </tr>
          <tr>
            <td colspan="5">
            <b><?php $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$_GET[pro]'"); $projeto = mysql_fetch_assoc($qr_projeto); echo "$projeto[nome]"; ?></b>
            </td>
          </tr>
          <tr class="secao">
            <td>Curso</td>
            <td>Retirada</td>
            <td>Mensal</td>
            <td><span class="style1">Horas/Dia </span></td>
            <td><span class="style1">Dias Produ&ccedil;&atilde;o</span></td>
          </tr>
          <tr>
            <td><b><?php print "$row_curso[nome]"; ?></b></td>
            <td>R$ <b><?php print number_format("$row_curso[salario]",'2',',','.'); ?></b></td>
            <td><b><?php print "$row_horario[horas_mes]"; ?></b></td>
            <td><b><?php print "$total"; ?></b></td>
            <td><b><?php print "$row_horario[dias_semana]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td>Conta</td>
            <td>Ag&ecirc;ncia</td>
            <td colspan="3">Banco</td>
          </tr>
          <tr>
            <td><b><?php print "$row[conta]"; ?></b></td>
            <td><b><?php print "$row[agencia]"; ?></b></td>
            <td colspan="3"><b><?php print "$row_banco[nome]"; ?></b></td>
          </tr>
          <tr class="secao">
            <td>Qtd. de &ocirc;nibus / Valor Transporte</td>
            <td>Tipo</td>
            <td colspan="3">&nbsp;</td>
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
            <td colspan="3">&nbsp;</td>
          </tr>
          <tr class="secao">
            <td colspan="5">Hor&aacute;rios</td>
          </tr>
          <tr>
            <td colspan="5">
            DE SEGUNDA &Agrave; SEXTA DAS:&nbsp;______:_____ &Agrave;S ______:_____ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            HORAS&nbsp;/&nbsp;INTERVALO: ______:_____ &Agrave;S ______:_____ <br>
            HORAS S&Aacute;BADO DAS: ______:_____ &Agrave;S ______:_____               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            HORAS&nbsp;/ INTERVALO: ______:_____ &Agrave;S ______:_____</td>
         </tr>
         <tr class="secao">
            <td colspan="5">Observa&ccedil;&otilde;es</td>
          </tr>
          <tr>
            <td colspan="5"><p align="center"><br>
            _________________________________________________________________________________<br><br>
            __________________________________________________________________________________
        </p></td>
        </tr>
        <tr>
          <td colspan="5">
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
</body>
</html>
<?php } ?>