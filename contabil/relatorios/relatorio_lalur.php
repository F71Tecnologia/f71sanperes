<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../empresa.php");

$usuario = carregaUsuario(); 
$master = mysql_fetch_assoc(mysql_query("SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1;"));

// Configurações header para forçar o download
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
header ("Content-Disposition: attachment; filename=\"LALUR.xls\"" );
header ("Content-Description: PHP Generated Data" );

?>
<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
    <tr style="">
        <td colspan="2" style="text-align: left; border: none; background-color: #e8e8e8;"><?= $master['nome'] ?></td>
        <td colspan="3" style="text-align: right; border: none; background-color: #e8e8e8;"><?= $master['nome'] ?></td>
    </tr>
    <tr style="">
        <td colspan="5" style="text-align: center; font-weight: bold; border: none; background-color: #e8e8e8;">ASSESSORIA CONTÁBIL LTDA</td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: left; border: none; background-color: #e8e8e8;"><?= date("d/m/Y H:i:s") ?></td>
        <td colspan="2" style="text-align: right; border: none; background-color: #e8e8e8;">Folha</td>
        <td style="text-align: center; border: none; background-color: #e8e8e8;">1</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; text-align: center; border: none;">Emissão do LALUR</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; text-align: center; border: none;">PARTE A - REGISTRO DOS AJUSTES DO LUCRO LÍQUIDO DO EXERCÍCIO</td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; text-align: center; border: none;">ENCERRADO EM 31 DE DEZEMBRO DE <?= date("Y") ?></td>
    </tr>
    <tr>
        <td colspan="5" style="border: none;">&nbsp;</td>
    </tr>
    <tr>
        <td style="text-align: center; font-weight: bold;">DATA</td>
        <td style="text-align: center; font-weight: bold;">HISTÓRICO</td>
        <td style="text-align: right; font-weight: bold;">VALOR</td>
        <td style="text-align: right; font-weight: bold;">(+) ADIÇÕES</td>
        <td style="text-align: right; font-weight: bold;">(-) EXCLUSÕES</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">NATUREZA DOS AJUSTES</td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">TOTAL ADIÇÕES E EXCLUSÕES</td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">DEMONSTRATIVO DO LUCRO REAL</td>
        <td style="text-align: right; font-weight: bold; background-color: #e8e8e8;">(+) Adições</td>
        <td style="text-align: right; font-weight: bold; background-color: #e8e8e8;">(-) Exclusões</td>
        <td style="text-align: right; font-weight: bold; background-color: #e8e8e8;">Apuração</td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">RESULTADO DO EXERCÍCIO</td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">ADIÇÕES:</td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">TOTAL ADIÇÕES:</td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;">PREJUÍZO LIQUÍDO FISCAL</td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
        <td style="">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; background-color: #e8e8e8;"></td>
        <td style="text-align: left; font-weight: bold; background-color: #e8e8e8;"><?= $master['municipio'] ?>, 31 DE DEZEMBRO DE <?= date("Y") ?></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
        <td style="text-align: right; background-color: #e8e8e8;"></td>
    </tr>
    <tr>
        <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="5">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="background-color: #e8e8e8;">&nbsp;</td>
        <td style="text-align: center; font-weight: bold; background-color: #e8e8e8;"><?= $master['responsavel'] ?></td>
        <td colspan="3" style="text-align: center; font-weight: bold; background-color: #e8e8e8;"><?= $master['responsavel'] ?></td>

    </tr>
</table>