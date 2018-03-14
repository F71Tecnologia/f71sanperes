<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";

$clt = $_REQUEST['clt'];
$id_reg = $_REQUEST['id_reg'];

$id_user = $_COOKIE['logado'];

$data = date('d/m/Y');

$qry_consulta = mysql_query("SELECT * FROM rh_doc_status WHERE id_clt='$clt' AND tipo='35'");
$cont_consulta = mysql_num_rows($qry_consulta);

if ($cont_consulta == 0 )
{
$insert_doc = mysql_query("INSERT INTO rh_doc_status (tipo, id_clt, data, id_user) VALUES ('35', '$clt', NOW(), '$_COOKIE[logado]')");
	
}

$result_clt = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM rh_clt where id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

if($row_clt['prazoexp'] == 1){
    $prazoExp = '30';
    $prazoPro = 60;
    $prazoExpExt = 'trinta';
    $prazoProrrogado = 'um preriodo de 60 (sessenta) dias';
}else if($row_clt['prazoexp'] == 2 OR $row_clt['prazoexp'] == ''){
    $prazoExp = '45';
    $prazoPro = 45;
    $prazoExpExt = 'quarenta e cinco';
    $prazoProrrogado = 'igual período';
}else if($row_clt['prazoexp'] == 3){
    $prazoExp = '60';
    $prazoPro = 30;
    $prazoExpExt = 'sessenta';
    $prazoProrrogado = 'um preriodo de 30 (trinta) dias';
}

//PEGA O CURSO DO CONTRATADO
$sql_transf = mysql_fetch_assoc(mysql_query("SELECT id_curso_de FROM rh_transferencias WHERE id_clt = $row_clt[id_clt] ORDER BY data_proc ASC LIMIT 1"));
if(!empty($sql_transf['id_curso_de'])){
    $idCurso = $sql_transf['id_curso_de'];
}else{
    $idCurso = $row_clt['id_curso'];
}
//$idCurso = $row_clt['id_curso'];

$result_curso = mysql_query("Select * from  curso where id_curso = '$idCurso'");
$row_curso = mysql_fetch_array($result_curso);

$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_proj = mysql_query("SELECT * FROM projeto WHERE id_projeto='$row_clt[id_projeto]' ");
$row_proj = mysql_fetch_assoc($result_proj);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master	 = mysql_fetch_assoc($qr_master);

$qr_ctps = mysql_query("SELECT * FROM controlectps WHERE id_user_cad = '$id_user' ") or die(mysql_error());
$row_ctps = mysql_fetch_assoc($qr_master);


$result_empresa = mysql_query("Select * from  rhempresa where id_empresa = '$row_clt[rh_vinculo]'");
$row_empresa = mysql_fetch_array($result_empresa);

$meses_pt = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

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

$data_entrada = explode("/",$row_clt['data_entrada']);
$dia_entrada = $data_entrada[0];
$mes_entrada = $data_entrada[1];
$ano_entrada = $data_entrada[2];
$data_final = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp - 1), $ano_entrada));
$data_incial_pro = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp), $ano_entrada));
$data_final_pro = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp+$prazoPro-1), $ano_entrada));
$data_final1 = explode("/",$data_final);
$dia_final = $data_final1[0];
$mes_final = $data_final1[1];
$ano_final = $data_final1[2];
$data_final2 = date("d/m/Y", mktime (0, 0, 0, $mes_final  , $dia_final + ($prazoExp - 1), $ano_final));

$id_curso = $row_curso['id_curso'];

$qrsalario = "select * from rh_salario where id_curso = '$id_curso' order by data desc limit 1";
$rssalario = mysql_query($qrsalario);
$salarioAntigo = mysql_fetch_array($rssalario);
$salario1 = $salarioAntigo['salario_novo'];
$totalHistorico = mysql_num_rows($rssalario);

if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1'){
    $salario1 = $salarioAntigo['salario_novo'];
} else {
    $salario1 = $salarioAntigo['salario_antigo'];
}
if($totalHistorico == 0){
                            $salario1 = $row_curso['salario'];
                        }

if($_COOKIE['logado'] != 87 and $row_clt['status'] == 10){
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '3' and id_clt = '$clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('3','$clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '3'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}


?>
<?php
function valor_extenso($valor=0, $maiusculas=false)
{
    // verifica se tem virgula decimal
    if (strpos($valor,",") > 0)
    {
      // retira o ponto de milhar, se tiver
      $valor = str_replace(".","",$valor);
 
      // troca a virgula decimal por ponto decimal
      $valor = str_replace(",",".",$valor);
    }
$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
"quatrilhões");
 
$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");
$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
"sete", "oito", "nove");
 
        $z=0;
 
        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
		$cont=count($inteiro);
		        for($i=0;$i<$cont;$i++)
                for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                $inteiro[$i] = "0".$inteiro[$i];
 
        $fim = $cont - ($inteiro[$cont-1] > 0 ? 1 : 2);
        for ($i=0;$i<$cont;$i++) {
                $valor = $inteiro[$i];
                $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
 
                $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&
$ru) ? " e " : "").$ru;
                $t = $cont-1-$i;
                $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                if ($valor == "000")$z++; elseif ($z > 0) $z--;
                if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
                if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
 
         if(!$maiusculas)
		 {
          return($rt ? $rt : "zero");
         } elseif($maiusculas == "2") {
          return (strtoupper($rt) ? strtoupper($rt) : "Zero");
         } else {
         return (ucwords($rt) ? ucwords($rt) : "Zero");
         }
 
}
?>
<HTML>
<TITLE>ACORDO PARA COMPENSAÇÃO DE HORAS DE TRABALHO</TITLE>
<link href="../net1.css" rel="stylesheet" type="text/css">
<HEAD>
<STYLE TYPE="text/css">
<!--
  TD { font-size: 10pt; font-family: sans-serif }
table {
	font-family: Arial, Helvetica, sans-serif;
}
table {
	font-size: 9px;
}
-->
</STYLE>
</HEAD>
<BODY LEFTMARGIN=0 TOPMARGIN=0>
<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
<TR><TD HEIGHT=20></TD>
<TR VALIGN=TOP>
<TD height="22" colspan="3" align="center" valign="middle"> <strong> 
		<img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
</TD>
  <TR VALIGN=TOP><TD WIDTH=296></TD><TD WIDTH=250 HEIGHT=22 ALIGN=CENTER STYLE="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold;">ACORDO PARA COMPENSAÇÃO DE HORAS DE TRABALHO</TD><TD WIDTH=300></TD>
</TABLE>
<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px" style="margin-top:200px">
  <TR><TD HEIGHT=25></TD>
  <TR VALIGN=TOP><TD WIDTH=62></TD><TD WIDTH=677 HEIGHT=876><DIV>
    <hr>
  </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
    <div align="justify">
      <p>
      <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
          
       ART. 29 E PARAGRAFOS 2o E 3o DA CLT, COM ALTERACAO DADA PELA LEI No 7.855 DE 24/10/1989

C.T.P.S No 	SERIE: 33352 
NOME DO EMPREGADO: DENIZE LAIS VOLCOV
RECEBEMOS A CARTEIRA DE TRABALHO E PREVIDENCIA SOCIAL ACIMA, PARA AS ANOTACOES NECESSARIAS E QUE SERA DEVOLVIDA DENTRO DE 48 HORAS, DE ACORDO COM A LEI EM VIGOR.

        
   
<!--        Pelo presente instrumento particular e na melhor forma de direito, os abaixo assinados, 
        <?=$row_master['razao'];?>, sediada na  <?="{$row_proj['endereco']}, {$row_proj['bairro']},
            {$row_proj['cidade']}, {$row_proj['estado']}";?>, inscrito no CNPJ/MF sob o nº <?=$row_empresa['cnpj'];?>, 
            denominada Empregadora, e a ser (a). <?=$row_master['responsavel'];?>, <?=$row_master['nacionalidade'];?> ,
        <?=$row_master['civil'];?>, <?=$row_master['formacao'];?>, portador da Cédula de Identidade n.º <?=$row_master['rg'];?>,
        inscrito no CPF sob o n.º <?=$row_master['cpf'];?>
        , portador doravante designada, simplesmente EMPREGADORA e de outro lado <?=$row_clt['nome']; ?>, residente e domiciliado na <?=$row_clt['endereco'].", ".$row_clt['numero'].", ".$row_clt['complemento']." - ".$row_clt['bairro']." - ".$row_clt['cidade']." - ".$row_clt['uf'].", ".$row_clt['cep']; ?>, portador da CTPS n°  <?=$row_clt['campo1']." / ".$row_clt['serie_ctps']." - ".$row_clt['uf_ctps']?>, RG n° <?=$row_clt['rg']; ?> e CPF/MF <?=$row_clt['cpf']; ?> a seguir chamado apenas de EMPREGADO, é celebrado o presente CONTRATO DE EXPERI&Ecirc;NCIA, que terá vigência a partir da data de início da prestação de serviços abaixo apontada, de acordo com as condições a seguir especificadas:-->
        <BR>
          <BR>   
        
        </font></p>
      <p>&nbsp;</p>
      <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
      </font></p>
      <FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
      <p align="center"><?php list($dia_entrada,$mes_entrada,$ano_entrada) = explode('/',$row_clt['data_entrada']); print "$row_proj[nome], $dia_entrada de ".$meses_pt[(int)$mes_entrada]." de $ano_entrada."; //print "$row_reg[regiao], $dia de $mes de $ano."; ?></p>
      </font>
        <table width="100%" border="0" >
          <tr>
<!--            <td align="center">____________________________________</td>-->
            <td align="center">____________________________________</td>
          </tr>
          <tr class="linha">
<!--            <td align="center" class="linha"><strong><?= $row_master['razao'];
?></strong></td>-->
<!--            <td align="center" class="linha"><strong>DENIZE LAIS VOLCOV
</strong></td>-->
            <td align="center" class="linha"><strong>
              &nbsp;<?=$row_master['razao']?></strong></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
<!--          <tr>
            <td align="center"><strong>____________________________________</strong></td>
            <td align="center"><strong>____________________________________</strong></td>
          </tr>
          <tr>
            <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
            <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;responsável quando for menor <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong></td>-->
          </tr>
        </table>
        <p align="center" class="linha">&nbsp;</p>
</DIV>
      <p>&nbsp;</p>
      <p><span class="linha"><BR>
      </span></p>
    </div>
  </DIV><DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
  </DIV>
  <DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
  </DIV></TD><TD WIDTH=55 bgcolor="#FFFFFF"></TD>
</TABLE>
</BODY>
</HTML>
