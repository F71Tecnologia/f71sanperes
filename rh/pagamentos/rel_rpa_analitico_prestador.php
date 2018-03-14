<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
error_reporting(E_ALL);
/*
 * LAST UPDATE
 * RAMON LIMA
 * 11/04/2013
 */

include('../../conn.php');
include("../../funcoes.php");
include("../../wfunction.php");
$lista = false;
$usuario = carregaUsuario();
$mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
$ano = $_REQUEST['ano'];
$id_regiao = $_REQUEST['regiao'];

$usuario = carregaUsuario();

function normalizaNome($variavel) {
    $variavel = strtoupper($variavel);
    if (strlen($variavel) > 200) {
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /", "_", $variavel);
    $nomearquivo = preg_replace("/[\/]/", "", $nomearquivo);
    $nomearquivo = preg_replace("/[ÁÀÂÃ]/i", "A", $nomearquivo);
    $nomearquivo = preg_replace("/[áàâãª]/i", "a", $nomearquivo);
    $nomearquivo = preg_replace("/[ÉÈÊ]/i", "E", $nomearquivo);
    $nomearquivo = preg_replace("/[éèê]/i", "e", $nomearquivo);
    $nomearquivo = preg_replace("/[ÍÌÎ]/i", "I", $nomearquivo);
    $nomearquivo = preg_replace("/[íìî]/i", "i", $nomearquivo);
    $nomearquivo = preg_replace("/[ÓÒÔÕ]/i", "O", $nomearquivo);
    $nomearquivo = preg_replace("/[óòôõº]/i", "o", $nomearquivo);
    $nomearquivo = preg_replace("/[ÚÙÛ]/i", "U", $nomearquivo);
    $nomearquivo = preg_replace("/[úùû]/i", "u", $nomearquivo);
    $nomearquivo = str_replace("Ç", "C", $nomearquivo);
    $nomearquivo = str_replace("ç", "c", $nomearquivo);

    return $nomearquivo;
}

/* $qr_rpa = mysql_query(" SELECT A.*, B.nome, B.cpf, TRIM(C.nome) as nome_projeto, C.id_projeto, B.agencia,B.conta,E.id_saida,G.id_saida_file, G.tipo_saida_file,
  IF(B.banco != '9999', (SELECT razao FROM bancos WHERE id_banco = B.banco), B.nome_banco) as banco
  FROM  rpa_autonomo as A
  INNER JOIN autonomo as B ON (A.id_autonomo = B.id_autonomo)
  INNER JOIN projeto as C ON (C.id_projeto = B.id_projeto)
  INNER JOIN regioes as D ON (D.id_regiao = B.id_regiao)
  INNER JOIN rpa_saida_assoc as E ON (E.id_rpa = A.id_rpa)
  INNER JOIN saida as F ON (F.id_saida = E.id_saida)
  INNER JOIN saida_files AS G ON (F.id_saida = G.id_saida)
  WHERE  A.mes_competencia = '{$mes2d}' AND A.ano_competencia = '{$ano}' AND D.id_regiao = {$usuario['id_regiao']} AND F.status IN(1,2)
  AND F.estorno =0
  GROUP BY A.id_rpa
  ORDER BY B.id_projeto,B.nome"); */
$qr_rpa = mysql_query("SELECT A.*,G.id_saida, B.c_fantasia,  C.nome as nome_projeto,C.id_projeto, B.conta, B.conta_dv, B.agencia, B.agencia_dv, B.nome_banco, B.c_cpf
                        FROM  rpa_autonomo as A
                        LEFT JOIN prestadorservico as B ON (A.id_prestador = B.id_prestador)
                        LEFT JOIN projeto as C ON (C.id_projeto = B.id_projeto)
                        LEFT JOIN regioes as D ON (D.id_regiao = B.id_regiao)
                        LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa = A.id_rpa)
                        LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
                        LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
                        WHERE A.mes_competencia = '$mes2d' AND A.ano_competencia = '$ano' AND D.id_regiao = '{$usuario['id_regiao']}'
                        GROUP BY B.id_prestador
                        ORDER BY B.id_projeto,B.c_fantasia");
//D.id_master = $usuario[id_master]

if (isset($_REQUEST['gerarRPA_Recibo'])) {
    $name = "rpa_" . $mes2d . "_{$ano}.zip";
    $pathFile = '../zips_rpa/' . $name;
    $zip = new ZipArchive();
    while ($row_rpa = mysql_fetch_assoc($qr_rpa)) {
        //echo $row_rpa['nome_projeto'] . '/' . $row_rpa['nome'].'.pdf';
        if ($zip->open($pathFile, ZipArchive::CREATE) === true) {
            $zip->addFile('../../comprovantes/' . $row_rpa['id_saida_file'] . '.' . $row_rpa['id_saida'] . $row_rpa['tipo_saida_file'], normalizaNome($row_rpa['nome_projeto']) . '/' . normalizaNome($row_rpa['c_fantasia']) . '.pdf');
        }
    }
    $zip->close();

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($pathFile));
    header("Content-Disposition: attachment; filename={$name}");
    flush();

    readfile($pathFile);
    exit;
}

if (isset($_REQUEST['gerarRPA'])) {
    $name = "rpa_" . $mes2d . "_{$ano}.zip";
    $pathFile = '../zips_rpa/' . $name;
    $zip = new ZipArchive();
    while ($row_rpa = mysql_fetch_assoc($qr_rpa)) {
//        echo '<a href="../../autonomo/arquivo_rpa_pdf/' . $row_rpa['id_rpa'] . '_' . $row_rpa['id_autonomo'] . '.pdf">link</a><br>';
        if ($zip->open($pathFile, ZipArchive::CREATE) === true) {
            $zip->addFile('../../autonomo/arquivo_rpa_pdf/' . $row_rpa['id_rpa'] . '_' . $row_rpa['id_prestador'] . '.pdf', normalizaNome($row_rpa['nome_projeto']) . '/' . normalizaNome($row_rpa['c_fantasia']) . '.pdf');
        }
    }
    $zip->close();

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($pathFile));
    header("Content-Disposition: attachment; filename={$name}");
    flush();

    readfile($pathFile);
    exit;
}
?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/thickbox/thickbox.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>        
    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1" action="<?php $_SERVER["PHP_SELF"]; ?>">
            <input type="hidden" name="mes" value="<?php echo $mes2d; ?>" />
            <input type="hidden" name="ano" value="<?php echo $ano; ?>" />
            <div id="content">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório Analítico - RPA Prestador</h2>
                        <p></p>
                    </div>
                </div>
                <br class="clear">

                <br/>            
                <br/><br/>
                <?php
                $contagem = mysql_num_rows($qr_rpa);
                if ($contagem != 0) {
                    ?>
                    <p class="controls"> 
                        <input type="submit" name="gerarRPA" value="Gerar PDF de RPAs" id="gerarRpa" class="button" />
                        
                        <!--<input type="submit" name="gerarRPA_Recibo" value="Gerar Recibos de RPAs" id="gerarRPA_Recibo" class="button" />-->
                        
                        <input type="button" onclick="tableToExcel('tableRpa', 'RPA Analitico')" value="Exportar para Excel" class="exportarExcel">
                    </p>
                    <br/>

                    <table width="100%" cellspacing="0" cellpadding="0" class="grid" id="tableRpa">

                        <thead>
                        <th>CONTAGEM</th>
                        <th>Nº SAÍDA</th>
                        <th>Nº RPA</th>
                        <th>UNIDADE</th>
                        <th>NOME</th>
                        <th>CPF</th>
                        <th>BANCO</th>
                        <th>AGENCIA</th>
                        <th>AGENCIA DV</th>
                        <th>CONTA</th>
                        <th>CONTA dv</th>
                        <th>HORA MÊS</th>
                        <th>VALOR BRUTO</th>
                        <th>INSS</th>
                        <th>IR</th>
                        <th>ISS</th>
                        <th>VALOR LÍQUIDO</th>

                        </thead>


                        <?php
                        $i = 0;
                        while ($row_rpa = mysql_fetch_assoc($qr_rpa)) {

                            if ($row_rpa['id_projeto'] != $projetoAnt and ! empty($projetoAnt)) {
                                echo'<tr height="40" style="background-color: #c8ebf9">
                                                    <td colspan="8" align="right" style="font-weight:bold;">SUBTOTAIS:</td>
                                                    <td align="center"> R$ ' . number_format($subtotal_bruto, 2, ',', '.') . '</td>
                                                    <td align="center"> R$ ' . number_format($subtotal_inss, 2, ',', '.') . '</td>
                                                    <td align="center"> R$ ' . number_format($subtotal_irrf, 2, ',', '.') . '</td>
                                                    <td align="center"> R$ ' . number_format($subtotal_iss, 2, ',', '.') . '</td>
                                                    <td align="center"> R$ ' . number_format($subtotal_liquido, 2, ',', '.') . '</td>
                                                 </tr>';
                                unset($subtotal_bruto, $subtotal_inss, $subtotal_irrf, $subtotal_liquido, $subtotal_iss);
                            }



                            echo '<tr>';
                            echo '<td>'. ++$i  .'</td>';
                            echo '<td>' . $row_rpa['id_saida'] . '</td>';
                            echo '<td>' . $row_rpa['id_rpa'] . '</td>';
                            //echo '<td>' . $row_rpa['id_saida'] . ' <input type="hidden" name="id_autonomo[]" value="' . $row_rpa['id_autonomo'] . '"/> </td>';
                            echo '<td>' . $row_rpa['nome_projeto'] . '</td>';
                            echo '<td>' . $row_rpa['c_fantasia'] . '</td>';
                            echo '<td>' . $row_rpa['c_cpf'] . '</td>';
                            echo '<td>' . $row_rpa['nome_banco'] . '</td>';
                            echo '<td>' . $row_rpa['agencia'] . '</td>';
                            echo '<td>' . $row_rpa['agencia_dv'] . '</td>';
                            echo '<td>' . $row_rpa['conta'] . '</td>';
                            echo '<td>' . $row_rpa['conta_dv'] . '</td>';
                            echo '<td align="center">' . $row_rpa['hora_mes'] . '</td>';
                            echo '<td align="center">' . number_format($row_rpa['valor'], 2, ',', '.') . '</td>';
                            echo '<td align="center">' . number_format($row_rpa['valor_inss'], 2, ',', '.') . '</td>';
                            echo '<td align="center">' . number_format($row_rpa['valor_ir'], 2, ',', '.') . '</td>';
                            echo '<td align="center">' . number_format($row_rpa['valor_iss'], 2, ',', '.') . '</td>';
                            echo '<td align="center">' . number_format($row_rpa['valor_liquido'], 2, ',', '.') . '</td>';
                            echo '</tr>';

                            $subtotal_bruto += $row_rpa['valor'];
                            $subtotal_inss += $row_rpa['valor_inss'];
                            $subtotal_irrf += $row_rpa['valor_ir'];
                            $subtotal_iss += $row_rpa['valor_iss'];
                            $subtotal_liquido += $row_rpa['valor_liquido'];

                            $totalizador_bruto += $row_rpa['valor'];
                            $totalizador_inss += $row_rpa['valor_inss'];
                            $totalizador_irrf += $row_rpa['valor_ir'];
                            $totalizador_iss += $row_rpa['valor_iss'];
                            $totalizador_liquido += $row_rpa['valor_liquido'];
                            $projetoAnt = $row_rpa['id_projeto'];
                        }
                        
                        echo'<tr height="40" style="background-color: #c8ebf9">
                                <td colspan="12" align="right" style="font-weight:bold;">QUANTIDADE DE RPAS:</td>
                                <td align="center">'. $contagem .'</td>
                            </tr>';
                        
                        echo'<tr height="40" style="background-color: #c8ebf9">
                                <td colspan="12" align="right" style="font-weight:bold;">SUBTOTAIS:</td>
                                <td align="center"> R$ ' . number_format($subtotal_bruto, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($subtotal_inss, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($subtotal_irrf, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($subtotal_iss, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($subtotal_liquido, 2, ',', '.') . '</td>

                             </tr>';
                        echo'<tr height="40" style="background-color: #c8ebf9">
                                <td colspan="12" align="right" style="font-weight:bold;">TOTAIS:</td>
                                <td align="center"> R$ ' . number_format($totalizador_bruto, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($totalizador_inss, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($totalizador_irrf, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($totalizador_iss, 2, ',', '.') . '</td>
                                <td align="center"> R$ ' . number_format($totalizador_liquido, 2, ',', '.') . '</td>

                             </tr>';

                        echo '</table>';
                    }
                    ?>
                </table>
            </div>

        </form>
    </body>
</html>