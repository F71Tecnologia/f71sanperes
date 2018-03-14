<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../conn.php";

$projet = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];

$result_clt = mysql_query("SELECT id_clt,nome,cpf FROM rh_clt WHERE id_projeto = '{$projet}' AND status = 10");

$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);

$meses_pt = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

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

$data_entrada = explode("/", $row_clt['data_entrada']);
$dia_entrada = $data_entrada[0];
$mes_entrada = $data_entrada[1];
$ano_entrada = $data_entrada[2];
$data_final = date("d/m/Y", mktime(0, 0, 0, $mes_entrada, $dia_entrada + 44, $ano_entrada));
$data_final1 = explode("/", $data_final);
$dia_final = $data_final1[0];
$mes_final = $data_final1[1];
$ano_final = $data_final1[2];
$data_final2 = date("d/m/Y", mktime(0, 0, 0, $mes_final, $dia_final + 44, $ano_final));


if ($_COOKIE['logado'] != 87) {
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
<HTML>
    <TITLE>DECLARAÇÃO DE INSS</TITLE>
    <link href="../net1.css" rel="stylesheet" type="text/css">
    <HEAD>
        <STYLE>
            p.quebra    { page-break-before: always }
        </STYLE>
    </HEAD>
    <BODY LEFTMARGIN=0 TOPMARGIN=0>
        <br/>
        <?php
        while ($row_clt = mysql_fetch_array($result_clt)) {
            $qr_folha = mysql_query("SELECT mes,ano,inss FROM rh_folha_proc WHERE id_clt = {$row_clt['id_clt']} AND status = 3 ORDER BY ano DESC ,mes DESC LIMIT 0,1");
            $toFolha = mysql_num_rows($qr_folha);

            if ($toFolha > 0) {
                $row_folha = mysql_fetch_assoc($qr_folha);
                ?>


                <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
                    <TR><TD HEIGHT=20></TD>
                    <TR VALIGN=TOP>
                        <TD height="22" colspan="3" align="center" valign="middle"> <strong> 
                                <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
                        </TD>
                    <TR VALIGN=TOP><TD WIDTH=296></TD><TD WIDTH=198 HEIGHT=22 ALIGN=CENTER STYLE="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold;"><br/>DECLARAÇÃO<br/><br/></TD><TD WIDTH=300></TD>
                </TABLE>
                <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
                    <TR><TD HEIGHT=25></TD>
                    <TR VALIGN=TOP><TD WIDTH=62></TD><TD WIDTH=677 HEIGHT=876><DIV>
                            </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
                                <div align="justify" style="padding-right: 40px;">
                                    <p>
                                    <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
                                        <br>
                                        <br>
                                        Declaro que o Sr. <?= $row_clt['nome']; ?>, portador do CPF n° <?= $row_clt['cpf']; ?>, e funcionário do Instituto Data Rio de Administração Pública, CNPJ <?= $row_master['cnpj']; ?>, 
                                        sob regime de CLT recolheu o valor de R$ <?php echo number_format($row_folha['inss'], 2, ",", ".") ?> relativa a contribuição Previdenciária no mês de <?php echo $meses_pt[(int) $row_folha['mes']] ?> de <?php echo $row_folha['ano'] ?>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                    <center>
                                        ____________________________________<br/>
                                        Instituto Data Rio de Administração Pública
                                    </center>

                                    <br>
                                    <br>
                                    <br>


                                    <hr>

                                    <br>
                                    <br>
                                    <br>

                                    <p style="font-size: 14pt; font-family: Arial; color: #000000; font-weight: bold; text-align: center;">DECLARAÇÃO</p>

                                    <br>
                                    <br>

                                    Eu <?= $row_clt['nome']; ?>, CPF <?= $row_clt['cpf']; ?>, declaro, sob as penas da lei, estar dispensado(a) da retenção da contribuição de INSS, 
                                    conforme determina a Lei nº 10.666, de 08 de maio de 2003 e a Instrução Normativa nº 89, de 11 de junho de 2003, do 
                                    Instituto Nacional do Seguro Social - INSS, uma vez que já sofro retenção mensal pelo teto máximo de contribuição, conforme declaração acima.
                                    <br/><br/>
                                    Declaro, que, ocorrendo qualquer alteração na contribuição, bem como ausência de recolhimento, estarei comunicando a essa empresa em tempo hábil para providências, 
                                    bem como estarei disponibilizando até a data do pagamento mensal o comprovante de retenção.
                                    Afirmo estar ciente de sou responsável pela complementação da contribuição até o limite máximo, na hipótese de, por qualquer razão, deixar de 
                                    receber remuneração ou esta for inferior à indicada nesta declaração (art. 24, § 1º / INSS / 89/2003).
                                    <br/><br/>
                                    Estou ciente, que as conseqüências pela não comunicação prevista no parágrafo anterior serão de minha inteira responsabilidade, 
                                    facultando à empresa a proceder a retenção prevista.

                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <center>
                                        _________________________________________<br/>
                                        Assinatura do Funcionário
                                    </center>
                                    </font>

                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>


                                    <p style="text-align: center; font-size: 9pt; font-weight: normal;">
                                        IDR - Instituto Data Rio<br/>
                                        Rua Dom Walmor, 388 - lojas 9 e 10 - Centro - Nova Iguaçu / RJ - CEP: 26215-219<br/>
                                        Tel: (21) 2667-0465 / (21) 2667-2948 / (21) 2656-7597<br/>
                                        www.institutodatario.com.br</p>



                                    </td>
                                    </tr>
                                    </table>
                                    <br/>
                                    <br/>
                                    <p class="quebra">&nbsp;</p>
    <?php } } ?>
                            </BODY>
                            </HTML>

