<?php
if (empty($_COOKIE['logado'])) {
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

$result_proj = mysql_query("SELECT * FROM projeto WHERE id_projeto='$row_clt[id_projeto]' ");
$row_proj = mysql_fetch_assoc($result_proj);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);
$row3 = mysql_fetch_array($row_master);

$result_empresa = mysql_query("Select * from  rhempresa where id_empresa = '$row_clt[rh_vinculo]'");
$row_empresa = mysql_fetch_array($result_empresa);

$meses_pt = array('Erro', 'Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

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

if ($_COOKIE['logado'] != 87 and $row_clt['status'] == 10) {
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data_cad = date('Y-m-d');
    $user_cad = $_COOKIE['logado'];

    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '3' and id_clt = '$clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);
    if ($num_row_verifica == "0") {
        mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('3','$clt','$data_cad', '$user_cad')");
    } else {
        mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '3'");
    }
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}
?>
<?php

function valor_extenso($valor = 0, $maiusculas = false) {
    // verifica se tem virgula decimal
    if (strpos($valor, ",") > 0) {
        // retira o ponto de milhar, se tiver
        $valor = str_replace(".", "", $valor);

        // troca a virgula decimal por ponto decimal
        $valor = str_replace(",", ".", $valor);
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

    $z = 0;

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    $cont = count($inteiro);
    for ($i = 0; $i < $cont; $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < $cont; $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                $ru) ? " e " : "") . $ru;
        $t = $cont - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                    ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    if (!$maiusculas) {
        return($rt ? $rt : "zero");
    } elseif ($maiusculas == "2") {
        return (strtoupper($rt) ? strtoupper($rt) : "Zero");
    } else {
        return (ucwords($rt) ? ucwords($rt) : "Zero");
    }
}
?>
<HTML>
    <TITLE>Contrato de Trabalho em Lote</TITLE>
    <HEAD>
        <link href="../net1.css" rel="stylesheet" type="text/css">
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
    <!--BODY onload="print();"-->
    <BODY LEFTMARGIN=0 TOPMARGIN=0>

        <?php
        if (!empty($_POST['check_list'])) {
            $cont = 0;
            foreach ($_POST['check_list'] as $check) {
                $cont++;

                $qrlote = "select * from rh_clt where id_clt = $check";
                $rslote = mysql_query($qrlote);

                while ($row = mysql_fetch_array($rslote)) {
                    $result = $row['id_regiao'];
                    $curso = $row['id_curso'];
                    $dataNova = date('d/m/Y', strtotime($row['data_entrada']));
                    $timestamp = strtotime($row['data_entrada'] . '+89 days');
                    $qrlote2 = "select * from curso where id_curso = '$curso'";
                    $rslote2 = mysql_query($qrlote2);
                    $row2 = mysql_fetch_array($rslote2);
                    $row_master2 = mysql_query("Select * from  rhempresa where id_regiao = '$result'");
                    $row3 = mysql_fetch_array($row_master2);
                    $row_master1 = mysql_query("Select * from  master where nome = '{$row3['nome']}'");
                    $row4 = mysql_fetch_assoc($row_master1);

                    $id_curso = $row2['id_curso'];
                    $qrsalario = "SELECT * FROM rh_salario WHERE id_curso = '$id_curso' order by data desc limit 1";
                    $rssalario = mysql_query($qrsalario);
                    $salarioAntigo = mysql_fetch_array($rssalario);
                    $totalHistorico = mysql_num_rows($rssalario);

                    $prazo = $row['prazoexp'];

                    if ($prazo == '1') {
                        $data_entrada = date("d/m/Y", strtotime(str_replace("-", "/", $row['data_entrada'] . '+29 days')));
                        $timestamp = strtotime($data_entrada . '+60 days');
                        $data_entrada = date("d/m/Y", strtotime(str_replace("/", "/", $data_entrada)));
                        $dataProrrog = date('d/m/Y', $timestamp);
                    } elseif ($prazo == '2') {
                        $data_entrada = date("d/m/Y", strtotime(str_replace("-", "/", $row['data_entrada'] . '+44 days')));
                        $timestamp = strtotime($data_entrada . '+45 days');
                        $data_entrada = date("d/m/Y", strtotime(str_replace("/", "/", $data_entrada)));
                        $dataProrrog = date('d/m/Y', $timestamp);
                    } elseif ($prazo == '3') {
                        $data_entrada = date("d/m/Y", strtotime(str_replace("-", "/", $row['data_entrada'] . '+59 days')));
                        $timestamp = strtotime($data_entrada . '+30 days');
                        $data_entrada = date("d/m/Y", strtotime(str_replace("/", "/", $data_entrada)));
                        $dataProrrog = date('d/m/Y', $timestamp);
                    }

                    if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1') {
                        $salario1 = $salarioAntigo['salario_novo'];
                    } else {
                        $salario1 = $salarioAntigo['salario_antigo'];
                    }
                    if ($totalHistorico == 0) {
                        $salario1 = $row2['salario'];
                    }
                    ?>


                    <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF"  >
                        <TR><TD HEIGHT=20 ></TD></tr>
                        <TR VALIGN=TOP><TD WIDTH=296></TD><TD WIDTH=600 HEIGHT=22 ALIGN=CENTER STYLE="font-size: 16pt; font-family: Bookman Old Style; color: #000000; font-weight: 400;">CONTRATO DE TRABALHO</TD><TD WIDTH=300></TD></tr>
                    </TABLE>
                    <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" >
                        <TR VALIGN=TOP><TD WIDTH=62></TD><TD WIDTH=677 HEIGHT=876><DIV>
                                </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
                                    <div align="justify" id="BORDERFONT">
                                        <p>
                                        <p><FONT STYLE="font-size: 8pt;">
                                            Entre o 
                                            <?= $row4['razao']; ?>, com sede situada na <?= $row4['endereco']; ?>, inscrito no CNPJ/MF sob o n� <?= $row4['cnpj']; ?>, por interm�dio de seu representante legal, <?= $row4['responsavel']; ?>, <?= $row4['nacionalidade']; ?> , <?= $row4['civil']; ?>, <?= $row4['formacao']; ?>, portadora do RG n.� <?= $row4['rg']; ?>, inscrito no CPF sob o n.� <?= $row4['cpf']; ?>
                                            , portador doravante designada, simplesmente EMPREGADORA e de outro lado <?= $row['nome']; ?>, residente e domiciliado na <?= $row['endereco'] . ", " . $row['numero'] . ", " . $row['complemento'] . " - " . $row['bairro'] . " - " . $row['cidade'] . " - " . $row['uf'] . ", " . $row['cep']; ?>, portador da CTPS n�  <?= $row['campo1'] . " / " . $row['serie_ctps'] . " - " . $row['uf_ctps'] ?>, RG n� <?= $row['rg']; ?> e CPF <?= $row['cpf']; ?> a seguir chamado apenas de EMPREGADO, � celebrado o presente CONTRATO DE TRABALHO, que ter� vig�ncia a partir da data de in�cio da presta��o de servi�os a seguir especificadas:
                                            <BR>
                                            <BR>
                                            1 - Fica o EMPREGADO admitido no quadro de funcion�rios da EMPREGADORA para exercer as fun��es de <?= $row2['nome'] ?> mediante a remunera��o de: R$ <?php echo $salario1; ?> (<?php echo valor_extenso(number_format($salario1, 2, ',', '')); ?>) por M�s.
                                            <BR>  <BR>      
                                            2- O Hor�rio de trabalho ser� aquele anotado na ficha de registro do EMPREGADO, sendo que eventual altera��o na jornada de trabalho por m�tuo consenso, n�o inovar� esse ajuste, permanecendo sempre �ntegra a obriga��o do EMPREGADO de cumprir o hor�rio contratualmente estabelecido, observado o limite legal.
                                            <BR><BR>
                                            3 - Obriga-se tamb�m o EMPREGADO a prestar servi�os em horas extraordin�rias, sempre que for determinado pela EMPREGADORA, na forma prevista em lei. Na hip�tese desta faculdade pela EMPREGADORA, o EMPREGADO receber� as horas extraordin�rias com acr�scimo legal, salvo a ocorr�ncia de compensa��o, com a consequente redu��o da jornada de trabalho do outro dia.
                                            <BR><BR>
                                            3.1. As horas extraordin�rias eventualmente trabalhadas, ser�o pagas desde que, sua realiza��o tenha sido previamente solicitada e autorizada, por escrito pelo Gestor da Divis�o, para o Departamento de Recursos Humanos.
                                            <br/><br/>
                                            4 - O EMPREGADO exercer� as fun��es objeto deste Contrato nas instala��es da EMPREGADORA, localizadas na cidade de RIO DE JANEIRO. Entretanto, o EMPREGADO concorda em viajar pelo Brasil ou ao exterior, de acordo com as necessidades da EMPREGADORA, desde que tais viagens n�o impliquem em mudan�a de seu domic�lio. 
                                            <BR><BR>
                                            5- O EMPREGADO concorda que, na hip�tese de estar temporariamente sem atividades a exercer dentro dos limites de seu cargo, fica expressamente ajustado que a EMPREGADORA pode, a seu exclusivo crit�rio, transferi-lo, pelo per�odo em que essas condi��es perdurarem, para outra fun��o, desde que compat�vel com a sua qualifica��o t�cnica e sem diminui��o da remunera��o.
                                            <BR><BR>
                                            6- Nos termos do que disp�e o par�grafo primeiro do artigo 469 da Consolida��o das Leis de Trabalho, o EMPREGADO acatar� determina��o emanada da EMPREGADORA para a presta��o de servi�os tanto na localidade de celebra��o do CONTRATO DE TRABALHO, como em qualquer outra cidade, capital ou vila do territ�rio nacional, quando esta decorra de real necessidade de servi�o, quer essa transfer�ncia seja transit�ria, quer seja definitiva. 
                                            <BR><BR>
                                            7- Em caso de dano causado pelo EMPREGADO fica a EMPREGADORA autorizada a efetivar o desconto da import�ncia correspondente ao preju�zo, o qual far�, com fundamento no par�grafo primeiro do artigo 462 da Consolida��o das Leis de Trabalho, j� que expressamente prevista em contrato.
                                            <BR><BR>
                                            8 - O presente contrato viger� durante 90 (noventa) dias, com in�cio em <?php echo $dataNova; ?> e t�rmino em <?php echo date('d/m/Y', $timestamp); ?> sendo celebrado para as partes verificarem reciprocamente, a conveni�ncia ou n�o de se vincularem em car�ter definitivo a um contrato de trabalho. A Empresa passando a conhecer as aptid�es do EMPREGADO verificando se o ambiente e os m�todos de trabalho atendem � sua conveni�ncia.
                                            <BR><BR>
                                            9 - Fica estabelecido que, findo o prazo baixo, este contrato poder� ser prorrogado ou rescindido, independente de aviso pr�vio, o qual se acha convencionado no presente ajuste, nada podendo ser reclamado fora do presente acordo ap�s o prazo fixado para o mesmo.
                                            <BR><BR>
                                            10 -  Na hip�tese desde ajuste transformar-se em contrato de prazo indeterminado, pelo decurso de tempo, continuar�o em plena vig�ncia todas as clausulas, enquanto durarem as rela��es do EMPREGADO com a EMPREGADORA.
                                            <BR><BR>
                                            11 -  Quando da rescis�o do v�nculo empregat�cio, por qualquer motivo, o EMPREGADO dever� devolver de imediato todos os bens (tais como celular, computador, etc.) e documentos pertencentes � EMPREGADORA que estiverem em sua posse, posse de quaisquer de seus representantes e controlados, de natureza confidencial ou n�o, sendo que a partir de tal rescis�o nenhum desses documentos ou anota��es dever�o ser utilizadas pelo EMPREGADO.
                                            <BR><BR>
                                            12 - E por estarem assim justas e contratadas, as partes assinaram o presente Contrato em duas vias, juntamente com as duas testemunhas abaixo assinadas, a tudo presentes.
                                            </font></p>
                                        <br/>
                                        <br/>
                                        <br/>
                                        <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
                                            </font></p>
                                        <FONT STYLE=" font-family: Arial; color: #000000; font-size: 9pt; font-weight: bold;">
                                        <?php
                                        $dataCompleta = explode("/", $dataNova);
                                        $dia = $dataCompleta[0];
                                        $mes = $dataCompleta[1];
                                        $ano = $dataCompleta[2];

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
                                        ?>
                                        <p align="right" ><b><?php echo $row4['municipio'] . ", " . $dia . " de " . $mes . " de " . $ano . "."; ?></b></p>
                                        </font>
                                        <table width="100%" border="0" >
                                            <tr>
                                                <td align="center">____________________________________</td>
                                                <td align="center">____________________________________</td>
                                            </tr>
                                            <tr class="linha">
                                                <td align="center" class="linha"><strong><?= $row4['razao']; ?></strong></td>
                                                <td align="center" class="linha"><strong>
                                                        &nbsp;<?= $row['nome'] ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td align="center"><strong>____________________________________</strong></td>
                                                <td align="center"><strong>____________________________________</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
                                                <td class="linha"><strong>&nbsp;&nbsp;&nbsp;Testemunha<br>
                                                        &nbsp;&nbsp;&nbsp;RG:</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center" class="linha"><br/><br/><h3><b>Termo de Prorroga��o</b></h3><br></td>
                                            </tr>
                                            <tr>
                                                <td class="linha" colspan="2">
                                                    <span>Por m�tuo acordo entre as partes, fica o presente contrato de experi�ncia, que deveria vencer em <?= $data_entrada; ?>,  prorrogado at� <? echo $dataProrrog; ?> </span><br/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center">____________________________________</td>
                                                <td align="center">____________________________________</td>
                                            </tr>
                                            <tr class="linha">
                                                <td align="center" class="linha"><strong><?= $row4['razao']; ?></strong></td>
                                                <td align="center" class="linha"><strong>
                                                        &nbsp;<?= $row['nome'] ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td align="center"><strong>____________________________________</strong></td>
                                                <td align="center"><strong>____________________________________</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="linha"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:</strong></td>
                                                <td class="linha"><strong>&nbsp;&nbsp;&nbsp;Testemunha<br>
                                                        &nbsp;&nbsp;&nbsp;RG:</strong></td>
                                            </tr>
                                        </table>
                                    </DIV>
                                </div>
                            </TD><TD WIDTH=55 bgcolor="#FFFFFF"></TD>
                    </TABLE>
                    <?php
                }
                if ($cont == 1) {
                    echo '<p style="page-break-before: always;">&nbsp;-</p>';
                    $cont = 0;
                }
            }
        }
        ?>

    </BODY>
</HTML>
