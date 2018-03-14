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

$result_clt = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM rh_clt where id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

$result_curso = mysql_query("Select * from  curso where id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master	 = mysql_fetch_assoc($qr_master);


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
$data_final = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + 44, $ano_entrada));
$data_final1 = explode("/",$data_final);
$dia_final = $data_final1[0];
$mes_final = $data_final1[1];
$ano_final = $data_final1[2];
$data_final2 = date("d/m/Y", mktime (0, 0, 0, $mes_final  , $dia_final + 44, $ano_final));


if($_COOKIE['logado'] != 87 ){
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
<HTML>
<TITLE>CONTRATO DE TRABALHO</TITLE>
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
  <TR VALIGN=TOP><TD WIDTH=296></TD><TD WIDTH=198 HEIGHT=22 ALIGN=CENTER STYLE="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold;">Contrato de Trabalho</TD><TD WIDTH=300></TD>
</TABLE>
<TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
  <TR><TD HEIGHT=25></TD>
  <TR VALIGN=TOP><TD WIDTH=62></TD><TD WIDTH=677 HEIGHT=876><DIV>
    <hr>
  </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
    <div align="justify">
      <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">Entre a firma <span class="style2"><?=$row_empresa['nome']?></span>, com sede   no endereço <span class="style2"><?=$row_empresa['endereco']?></span>, </FONT><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">    portador  doravante      designada, simplesmente       EMPREGADOR     e <span class="style2"><?=$row_clt['nome']?></span>,  da   CTPS  nº
      <span class="style2"><?=$row_clt['campo1']." / ".$row_clt['serie_ctps']." - ".$row_clt['uf_ctps']?></span>, a   seguir    chamado    apenas    de     EMPREGADO,   e    celebrado    o    presente  CONTRATO DE   EXPERIÊNCIA,   que  terá&nbsp;&nbsp;&nbsp;vigência   a   partir da data   de  início  da prestação de serviços,  de acordo com as condições a seguir especificadas :<BR>
        <BR>
        &nbsp;1   -  Fica o EMPREGADO  admitido  no  quadro de  funcionários da  EMPREGADORA   para   exercer as funções de <span class="style2"><?=$row_curso['nome']?></span> mediante a remuneração de: <span class="style2">R$ <?=$row_curso['salario']?></span> por  Mês.<BR>
        <BR>
        &nbsp;2   -  O Horário  de trabalho será&nbsp;anotado na sua ficha de registro e a eventual  jornada  de  trabalho por determinação&nbsp;da  EMPREGADORA , não inovará&nbsp;&nbsp;esse ajuste ,  permanecendo sempre integra a  obrigação do EMPREGADO&nbsp;de cumprir o horário que   lhe for determinado ,  observando o limite legal.<BR>
        <BR>
        &nbsp;3   -  Obriga-se também o EMPREGADO a prestar serviços em horas extraordinárias,sempre que lhe for determinado&nbsp;pela EMPREGADORA ,  na  forma  prevista  em  Lei.  Na  hipótese  desta  faculdade pela  EMPREGADORA  o&nbsp;EMPREGADO receberá&nbsp;as horas  extraordinárias  com o acréscimo legal ,  salvo a ocorrência de compensação,&nbsp;com   a  consequente redução da jornada  de trabalho em outro dia.<BR>
        <BR>
        &nbsp;4   -  Aceita o EMPREGADO ,  expressamente , a  condição de prestar serviços em qualquer  dos turnos de  trabalho,&nbsp;isto é&nbsp;,  tanto  durante o  dia como a  noite , desde  que  sem  simultaneidade ,observadas as  prescrições  legais &nbsp;reguladoras do  assunto , quanto à&nbsp;remuneração.<BR>
        <BR>
        &nbsp;5   -  Fica disposto nos termos do que dispõe o parágrafo primeiro do artigo 469, da Consolidação das leis de trabalho, &nbsp;que o EMPREGADO acatará&nbsp;&nbsp;emanada da  EMPREGADORA para a prestação de serviços tanto na  localidade&nbsp;&nbsp;de celebração  do  CONTRATO DE TRABALHO, como em  qualquer outra cidade,  capital ou vila do  território&nbsp;&nbsp;nacional, quer essa  transferência seja transitória, quer seja  definitiva.<BR>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<BR>
        &nbsp;6   -  No ato  da  assinatura  desse  contrato, o   EMPREGADO  recebe  o  Regulamento  Interno  da  Empresa  cujas cláusulas  fazem   parte  do  contrato  de   trabalho,  e a violação de  qualquer uma delas implicará&nbsp;&nbsp;em   sanção, cuja  graduação dependerá&nbsp;da  gravidade  da mesma, culminado com a rescisão  do contrato.<BR>
        &nbsp;&nbsp;&nbsp;&nbsp;<BR>
        &nbsp;7   -  Em caso  de  dano causado pelo EMPREGADO, fica a EMPREGADORA ,autorizada a efetivar o  desconto  da &nbsp;&nbsp;importância correspondente   ao  prejuízo, o qual fará, com fundamento  no  parágrafo  único do  artigo 462  da &nbsp;consolidação  das Leis de Trabalho, já&nbsp;que  essa possibilidade fica  expressamente  prevista em contrato.<BR>
        <BR>
        &nbsp;8   -  O   presente   contrato   vigirá&nbsp;&nbsp;&nbsp;durante 45 dias,   com   início   em  <span class="style2">
		<?=$row_clt['data_entrada']?></span>&nbsp;e t&eacute;rmino em <span class="style2">
		<?=$data_final?>
		</span> sendo celebrado para  as  partes verificarem  reciprocamente,  a  conveniência  ou não  de  se   vincularem   em  &nbsp;caráter  definitivo a um  contrato de trabalho. A  empresa   passando a conhecer  as aptidões  do   EMPREGADO  &nbsp;verificando se  o ambiente e os métodos de trabalho  atendem à&nbsp;sua conveniência.<BR>
        <BR>
        &nbsp;9  -  Fica estabelecido que, findo o prazo acima, este contrato  poderá&nbsp;ser  prorrogado ou rescindido,  independente &nbsp;de  aviso  prévi , o  qual  já&nbsp;se  acha  convencionado  no presente ajuste, nada podendo ser reclamado  fora do  &nbsp;presente  acordo e  após o prazo fixado para o mesmo.<BR>
        <BR>
        10 -  Na   hipótese  deste  ajuste  transformar-se   em  contrato   de  prazo   indeterminado,  pelo  descurso  de   tempo, &nbsp;continuarão   em   plena   vigência   as   cláusulas de 1  ( um ) a 7 ( sete ),  enquanto   durarem   as   relações do  &nbsp;EMPREGADO  com a   EMPREGADORA, e  as segundas  com o  EMPREGADO, que  dela  dará&nbsp;&nbsp;o  competente&nbsp;&nbsp;recibo.</FONT></p>
      <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</FONT><FONT class="style5" STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><span class="style2"><?php list($dia_entrada,$mes_entrada,$ano_entrada) = explode('/',$row_clt['data_entrada']); print "$row_reg[regiao], $dia_entrada de ".$meses_pt[(int)$mes_entrada]." de $ano_entrada."; //print "$row_reg[regiao], $dia de $mes de $ano."; ?></span></FONT><BR>
        <BR>
      </p>
      <p>&nbsp;</p>
      
      <table width="100%" border="0" >
        <tr>
          <td align="center"><strong>_______________________________</strong></td>
          <td align="center"><strong>&nbsp;____________________________________</strong></td>
        </tr>
        <tr>
          <td align="center" class="linha"><strong>Testemunha</strong></td>
          <td align="center" class="linha"><strong><?=$row_clt['nome']?></strong></td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><strong>_______________________________</strong></td>
          <td align="center"><strong>____________________________________</strong></td>
        </tr>
        <tr>
          <td align="center" class="linha"><strong>Testemunha</strong></td>
          <td align="center" class="linha"><strong>
            <?php 
echo $row_master['razao'];
?>
          </strong></td>
        </tr>
      </table>

      <p align="center" class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
      <p align="center">&nbsp;</p>
      <p align="center" class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
      &nbsp; &nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></p>
      <DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
        <hr>
        <p align="center"><BR>
          <strong class="campotexto">TERMO DE PRORROGA&Ccedil;&Atilde;O</strong>       </p>
        <p align="justify"><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"> Por m&uacute;tuo acordo entre as partes , fica o presente contrato de experi&ecirc;ncia, que deveria vencer em <span class="style2"><?=$data_final?></span>, 
          prorrogado at&eacute;&nbsp;&nbsp;<span class="style2"><?=$data_final2?></span>. </FONT><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></p>
        <p align="center">&nbsp;</p>
        <table width="100%" border="0" >
          <tr>
            <td align="center"><strong>_______________________________</strong></td>
            <td align="center"><strong>&nbsp;____________________________________</strong></td>
          </tr>
          <tr class="linha">
            <td align="center" class="linha"><strong>&nbsp;Testemunha</strong></td>
            <td align="center" class="linha"><strong>
              <?=$row_clt['nome']?>
            </strong></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><strong>_______________________________</strong></td>
            <td align="center"><strong>____________________________________</strong></td>
          </tr>
          <tr>
            <td align="center" class="linha"><strong>Testemunha</strong></td>
            <td align="center" class="linha"><strong>
              <?php 
echo $row_master['razao'];
?>
            </strong></td>
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
