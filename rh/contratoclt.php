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

$result_curso_atual = mysql_query("Select * from  curso where id_curso = '{$row_clt['id_curso']}'");
$row_curso_atual = mysql_fetch_array($result_curso_atual);
$salarioAtual = $row_curso_atual['salario'];
$nomeCursoAtual = $row_curso_atual['nome'] . " " . $row_curso_atual['letra'] . " " . $row_curso_atual['numero'];

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


$result_empresa = mysql_query("Select * from  rhempresa where id_empresa = '$row_clt[rh_vinculo]'");
$row_empresa = mysql_fetch_array($result_empresa);

$meses_pt = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

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
$mes = "Mar�o";
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
$data_entrada = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + 90, $ano_entrada));
$data_entrada = explode("/",$data_entrada);
$dia_entrada = $data_entrada[0];
$mes_entrada = $data_entrada[1];
$ano_entrada = $data_entrada[2];
//$data_final = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + 44, $ano_entrada));
//$data_final1 = explode("/",$data_final);
//$dia_final = $data_final1[0];
//$mes_final = $data_final1[1];
//$ano_final = $data_final1[2];
//$data_final2 = date("d/m/Y", mktime (0, 0, 0, $mes_final  , $dia_final + 44, $ano_final));

$id_curso = $row_curso['id_curso'];

$qrsalario = "select * from rh_salario where id_curso = '$id_curso' order by data desc limit 1";
$rssalario = mysql_query($qrsalario);
$salarioAntigo = mysql_fetch_array($rssalario);
$salario1 = $salarioAntigo['salario_novo'];
//echo $salario1;
$totalHistorico = mysql_num_rows($rssalario);

if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1'){
    $salario1 = $salarioAntigo['salario_novo'];
} else {
    $salario1 = $salarioAntigo['salario_antigo'];
}
if($totalHistorico == 0){
                            $salario1 = $row_curso['salario'];
                        }
                        
                        
$salarioInicial = $salario1;
$nomeCursoInicial = $row_curso['nome'] . " " . $row_curso['letra'] . " " . $row_curso['numero'];


if($_COOKIE['logado'] != 87  and $row_clt['status'] == 10){
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
$singular = array("centavo", "real", "mil", "milh�o", "bilh�o", "trilh�o", "quatrilh�o");
$plural = array("centavos", "reais", "mil", "milh�es", "bilh�es", "trilh�es",
"quatrilh�es");
 
$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");
$u = array("", "um", "dois", "tr�s", "quatro", "cinco", "seis",
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
<TITLE>CONTRATO DE TRABALHO</TITLE>
<HEAD>
    <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="../resources/css/style-print.css" rel="stylesheet">
    <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
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
body { margin-top: 0px;}

.salario1 { display: none; }

@page {
    margin-top: 20px;
    margin-bottom: 20px;
}
@media print {
    #check {display: none;}
}

    .pagina {
        height: auto;
    }
</STYLE>
<script>
$(function(){
    $("#inicial").click(function(){
        if($(this).attr('checked') == 'checked'){
            $(".salario1").show();
            $(".salario2").hide();
        }
    });
    $("#atual").click(function(){
        if($(this).attr('checked') == 'checked'){
            $(".salario2").show();
            $(".salario1").hide();
        }
    });
})
</script>
</HEAD>
<BODY LEFTMARGIN=0 TOPMARGIN=0>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="text-center">
            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
            <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
        </div>
    </div>
</nav>
<div class="pagina">

    <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="" class="bordaescura1px" style="border-bottom: 0px;">
        <TR><!--<TD HEIGHT=20></TD>
<TR VALIGN=TOP>-->
        <TR VALIGN=TOP>
            <TD WIDTH="33%" ALIGN=CENTER style="height: 72px;"><img style="max-width: 138px; max-height: 70px;" src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/></TD>
            <TD WIDTH="33%" ALIGN=CENTER STYLE="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold; padding-top: 25px;">Contrato de Trabalho</TD>
            <TD WIDTH="33%" ALIGN=CENTER style="padding-top: 50px;"><span id="check" style="display: none;"><input type="radio" name="salario" id="inicial">Inicial<input type="radio" name="salario" CHECKED id="atual">Atual</span></TD>
    </TABLE>
    <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="" class="bordaescura1px" style="border-top: 0px;">
        <!--<TR><TD HEIGHT=25></TD>-->
        <TR VALIGN=TOP><!--<TD WIDTH=62></TD>--><TD colspan="3" WIDTH=677 HEIGHT=876><!--<DIV>
    <hr>
  </DIV>--><DIV ALIGN=LEFT class="linha" STYLE=" font-family: Arial; color: #000000; font-size: 10pt; padding: 6px 6px 0px 6px;">
                    <div align="justify">
                        <p>
                        <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
                                Entre o
                                <?=$row_master['razao'];?>, associa��o civil sem fins lucrativos, com sede situada na <?="{$row_proj['endereco']}, {$row_proj['bairro']}, {$row_proj['cidade']}, {$row_proj['estado']}";?>, inscrito no CNPJ/MF sob o n� <?=$row_empresa['cnpj'];?>, por interm�dio de seu representante legal, <?=$row_master['responsavel'];?>, <?=$row_master['nacionalidade'];?> , <?=$row_master['civil'];?>, <?=$row_master['formacao'];?>, portador da C�dula de Identidade n.� <?=$row_master['rg'];?>, inscrito no CPF sob o n.� <?=$row_master['cpf'];?>
                                , portador doravante designada, simplesmente EMPREGADORA e de outro lado <?=$row_clt['nome']; ?>, residente e domiciliado na <?=$row_clt['endereco'].", ".$row_clt['numero'].", ".$row_clt['complemento']." - ".$row_clt['bairro']." - ".$row_clt['cidade']." - ".$row_clt['uf'].", ".$row_clt['cep']; ?>, portador da CTPS n�  <?=$row_clt['campo1']." / ".$row_clt['serie_ctps']." - ".$row_clt['uf_ctps']?>, RG n� <?=$row_clt['rg']; ?> e CPF/MF <?=$row_clt['cpf']; ?> a seguir chamado apenas de EMPREGADO, � celebrado o presente CONTRATO DE TRABALHO, que ter� vig�ncia a partir da data de in�cio da presta��o de servi�os abaixo apontada, de acordo com as condi��es a seguir especificadas:
                                <BR>
                                <BR>
                                &nbsp;1 - Fica o EMPREGADO admitido no quadro de funcion�rios da EMPREGADORA para exercer as fun��es de <?php echo "<label class='salario1'>$nomeCursoInicial</label><label class='salario2'>$nomeCursoAtual</label>"; ?> mediante a remunera��o de: R$
                                <?php
                                if($id_curso === '6580'){
                                    //Setando valor hora da fun��o plantonista horista
                                    echo "<label class='salario1'>97,96</label><label class='salario2'>97,96</label>";
                                }elseif($id_curso === '6894' ){
                                    //Setando valor plant�o para fun��o plantonista
                                    echo "<label class='salario1'>1.131,76</label><label class='salario2'>1.131,76</label>";
                                }else{
                                    echo "<label class='salario1'>$salarioInicial</label><label class='salario2'>$salarioAtual</label>";
                                }
                                ?>
                                (<?php
                                if($id_curso === '6580'){
                                    //Setando valor hora da fun��o plantonista horista
                                    echo "<label class='salario1'>".valor_extenso(number_format('97.96',2,',',''))."</label><label class='salario2'>".valor_extenso(number_format('97.96',2,',',''))."</label>";
                                }elseif($id_curso === '6894'){
                                    //Setando valor plant�o para fun��o plantonista
                                    echo "<label class='salario1'>".valor_extenso(number_format('1131.76',2,',',''))."</label><label class='salario2'>".valor_extenso(number_format('1131.76',2,',',''))."</label>";
                                }else{
                                    echo "<label class='salario1'>".valor_extenso(number_format($salarioInicial,2,',',''))."</label><label class='salario2'>".valor_extenso(number_format($salarioAtual,2,',',''))."</label>";
                                }
                                ?>)
                                <?php
                                if($id_curso === '6580'){
                                    //Setando valor hora da fun��o plantonista horista
                                    echo "por Hora.";
                                }elseif($id_curso === '6894'){
                                    //Setando valor plant�o para fun��o plantonista
                                    echo "por Plant�o.";
                                }else{
                                    echo "por M�s.";
                                }
                                ?>
                                <BR>  <BR>
                                2- O Hor�rio de trabalho ser� aquele anotado na ficha de registro do EMPREGADO, sendo que eventual altera��o na jornada de trabalho por m�tuo consenso, n�o inovar� esse ajuste, permanecendo sempre �ntegra a obriga��o do EMPREGADO de cumprir o hor�rio contratualmente estabelecido, observado o limite legal.
                                <BR><BR>
                                3 - O EMPREGADO exercer� as fun��es objeto deste Contrato nas instala��es da EMPREGADORA, localizadas na cidade de <?=$row_proj['regiao']; ?>. Entretanto, o EMPREGADO concorda em viajar pelo Brasil ou ao exterior, de acordo com as necessidades da EMPREGADORA, desde que tais viagens n�o impliquem em mudan�a de seu domic�lio.
                                <BR><BR>
                                4 - O EMPREGADO concorda que, na hip�tese de estar temporariamente sem atividades a exercer dentro dos limites de seu cargo, fica expressamente ajustado que a EMPREGADORA pode, a seu exclusivo crit�rio, transferi-lo, pelo per�odo em que essas condi��es perdurarem, para outra fun��o, desde que compat�vel com a sua qualifica��o t�cnica e sem diminui��o da remunera��o.
                                <BR><BR>
                                5- Nos termos do que disp�e o par�grafo primeiro do artigo 469 da Consolida��o das Leis de Trabalho (�CLT�), o EMPREGADO acatar� determina��o emanada da EMPREGADORA para a presta��o de servi�os tanto na localidade de celebra��o do CONTRATO DE TRABALHO, como em qualquer outra cidade, capital ou vila do territ�rio nacional, quando esta decorra de real necessidade de servi�o,quer essa transfer�ncia seja transit�ria, quer seja definitiva.
                                <BR><BR>
                                6- No ato da assinatura desse contrato, o EMPREGADO recebe o Regulamento Interno da Empresa cujas cl�usulas fazem parte do contrato de trabalho, e a viola��o de qualquer uma delas implicar� em san��o, cuja grada��o depender� da gravidade da mesma, podendo culminar com a rescis�o do contrato.
                                <BR><BR>
                                7- Em caso de dano causado pelo EMPREGADO fica a EMPREGADORA autorizada a efetivar o desconto da import�ncia correspondente ao preju�zo, o qual far�, com fundamento no par�grafo primeiro do artigo 462 da Consolida��o das Leis de Trabalho, j� que expressamente prevista em contrato.
                                <BR><BR>
                                8 - O presente Contrato poder� ser rescindido por qualquer uma das partes, com ou sem justa causa, sem preju�zo da aplica��o das consequ�ncias previstas neste Contrato. Consideram-se justa causa, para efeitos desse Contrato, os casos previstos n�o somente nos artigos 482 e 483 da CLT, bem como o descumprimento de quaisquer das condi��es aqui estipuladas. Exceto em caso de rescis�o por justa causa, tanto a EMPREGADORA como o EMPREGADO dever�o conceder aviso-pr�vio de rescis�o com 30 (trinta) dias de anteced�ncia.
                                <BR><BR>
                                9 - Quando da rescis�o do v�nculo empregat�cio, por qualquer motivo, o EMPREGADO dever� devolver de imediato todos os bens (tais como celular, computador, etc.) e documentos pertencentes � EMPREGADORA que estiverem em sua posse, posse de quaisquer de seus representantes e controlados, de natureza confidencial ou n�o, sendo que a partir de tal rescis�o nenhum desses documentos ou anota��es dever�o ser utilizadas pelo EMPREGADO.
                                <BR><BR>
                                10 - Qualquer notifica��o, nos termos deste Contrato, dever� ser feita por escrito e ser� considerada adequadamente entregue se for entregue pessoalmente ou enviada pelo correio ao endere�o do EMPREGADO mencionado acima ou, quando endere�ada � Empresa, for entregue ou enviada pelo correio � sede da mesma.
                                <BR><BR>
                                11 - Cada uma das cl�usulas deste Contrato � exequ�vel e se uma ou mais cl�usulas forem declaradas inv�lidas, as demais cl�usulas permanecer�o vigentes.
                                <BR><BR>
                                12 - O n�o cumprimento, a qualquer tempo, de qualquer das cl�usulas aqui contidas, ou se o seu cumprimento n�o for a qualquer tempo exigido da outra Parte, tal fato n�o ser� interpretado como uma ren�ncia ao cumprimento de tal cl�usula ou afetar� a validade do presente Contrato, no todo ou em parte, nem prejudicar� o direito das Partes de posteriormente exigir o cumprimento de todas e quaisquer disposi��es ora aven�adas.
                                <BR><BR>
                                13 - E por estarem assim justas e contratadas, as partes assinaram o presente Contrato em duas vias, juntamente com as duas testemunhas abaixo assinadas, a tudo presentes.

                            </font></p>
                        <!--<p>&nbsp;</p>-->
                        <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
                            </font></p>
                        <FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
                            <p align="center"><?php print "$row_proj[nome], $dia_entrada de ".$meses_pt[(int)$mes_entrada]." de $ano_entrada."; //print "$row_reg[regiao], $dia de $mes de $ano."; ?></p>
                        </font>
                        <table width="100%" border="0" >
                            <tr>
                                <td align="center">____________________________________</td>
                                <td align="center">____________________________________</td>
                            </tr>
                            <tr class="linha">
                                <td align="center" class="linha"><strong><?= $row_master['razao'];
                                        ?></strong></td>
                                <td align="center" class="linha"><strong>
                                        &nbsp;<?=$row_clt['nome']?></strong></td>
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
                                <td align="center"><strong>____________________________________</strong></td>
                                <td align="center"><strong>____________________________________</strong></td>
                            </tr>
                            <tr>
                                <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
                                <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
                            </tr>
                            <tr><td style="height: 40px;">&nbsp;</td></tr>
                        </table>
                        <!--<p align="center" class="linha">&nbsp;</p>-->
                    </DIV>
                    <!--<p>&nbsp;</p>
                    <p><span class="linha"><BR>
                    </span></p>-->
                </div>
</DIV><!--<DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
  </DIV>
  <DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
  </DIV>--></TD><!--<TD WIDTH=55 bgcolor="#FFFFFF"></TD>-->
</TABLE>
</div>
</BODY>
</HTML>
