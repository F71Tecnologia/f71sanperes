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
header ("Content-Disposition: attachment; filename=\"MAPA DE APURAÇÃO FEDERAL.xls\"" );
header ("Content-Description: PHP Generated Data" );

?>
<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
    <tr style="">
        <td colspan="2" rowspan="5" style="text-align: center; vertical-align: middle; border: none; background-color: #CCC;"><img src="http://f71lagos.com/intranet/imagens/logomaster<?= $usuario['id_master'] ?>.gif"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; font-weight: bold; border: none;"><u>Mês</u></td>
        <td style="text-align: center; font-weight: bold; border: none;">&nbsp;</td>
        <td style="text-align: center; font-weight: bold; border: none;"><u>Ano (Exercício)</u></td>
        <td style="text-align: center; font-weight: bold; border: none;">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; font-weight: bold; background-color: #DCE6F1; border: 1px #000 solid;">&nbsp;</td>
        <td style="text-align: center; font-weight: bold; border: none;">&nbsp;</td>
        <td style="text-align: center; font-weight: bold; background-color: #DCE6F1; border: 1px #000 solid;">&nbsp;</td>
        <td style="text-align: center; font-weight: bold; border: none;">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; font-weight: bold; border: none;"><u>Trimestre</u></td>
        <td colspan="3" style="text-align: center; font-weight: bold; border: none;">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; font-weight: bold; background-color: #DCE6F1; border: 1px #000 solid;">&nbsp;</td>
        <td colspan="3" style="text-align: center; font-weight: bold; border: none;">&nbsp;</td>
    </tr>
    <tr style="">
        <td colspan="6" style="text-align: center; font-weight: bold; background-color: #366092; color: #FFF;">MAPA DE APURAÇÃO DE IMPOSTOS  E CONTRIBUIÇÕES FEDERAIS</td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">RECEITA BRUTA</td>
        <td style="text-align: left; border: none;">(+)               R$</td>
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">DEDUÇÕES I.RENDA</td>
        <td style="text-align: center; border: none; border-right: 1px #000 solid;">(-)                R$</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">01</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Receita 1,6%</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">18</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">IRRF sobre Receita Bruta</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">02</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Receita 8%</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">19</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">IRRF sobre Receita Financeira</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">03</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Receita 32%</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">20</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Deduções do I.Renda</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">04</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Serviço 8%</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">21</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Compensações do I.Renda</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">05</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Serviço 16%</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">22</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Compensações de Adicional de I.Renda</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">06</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Serviço 32%</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">23</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Dev.Exp. e de Venda Fim Esp.Exp(8%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;"></td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">24</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Dev.Exp. e de Venda Fim Esp.Exp(32%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">VALORES SOMENTE PARA CONTRIBUIÇÃO SOCIAL</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">25</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Anulação Serv (8%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">07</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Serviço 12% (CSSL)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">26</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Anulação Serv (16%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">08</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Serviço 32% (CSSL)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">27</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Anulação Serv (32%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;"></td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">DEDUÇÕES CONTRIBUIÇÃO SOCIAL</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">OUTRAS RECEITAS</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">28</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Compensação de Contribuição Social</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">09</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Ganho de Capital</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">29</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Contribuição Social Ret.Fonte</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">10</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Outras Receitas(PIS,COFINS,CSLL,IRPJ)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">30</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Dev/Anulação Serv (12%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">11</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Outras Receitas (PIS)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">31</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Anulação Serv.(32%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">12</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Outras Receitas (COFINS)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">DEDUÇÕES DO PIS</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">13</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Outras Receitas (CSLL, IRPJ)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">32</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Dedução de Pis</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">14</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Renda Variável</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">33</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Isento de Pis</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">15</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Lucro inflacionado</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">34</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Pis Ret. Na Fonte</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">16</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Aliquota do Lucro Inflacionado</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td colspan="2" style="text-align: center; border: none; background-color: #FFFF00; border-left: 1px #000 solid; border-right: 1px #000 solid;">DEDUÇÕES DO COFINS</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">17</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Poupança</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">35</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Dedução de COFINS</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: center; border: none; border-left: 1px #000 solid; border-right: 1px #000 solid;">&nbsp;</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">36</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Isento de COFINS</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: center; border: none; border-left: 1px #000 solid; border-right: 1px #000 solid;">&nbsp;</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">37</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">COFINS Ret. Na Fonte</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td colspan="2" style="text-align: center; border: none; border-left: 1px #000 solid; border-right: 1px #000 solid;">&nbsp;</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">38</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Compras</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td colspan="6" style="text-align: center; font-weight: bold; background-color: #366092; color: #FFF; border: 1px #000 solid;">DEMONSTRATIVO</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">39</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Faturamento (01+02+03+04+05+06)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">66</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo PIS Ret. Fonte/Ded. Anterior</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">40</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Lucro Presumido((01*1,6%)+(02+04)-23-25)*8%)+</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">67</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">PIS Devido (64-65-66)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;"></td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">((05-26)*16%)+((03+06)-24-27)*32%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">68</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo PIS Ret. Fonte/Ded. a Transportar</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">41</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Base do I.Renda(40+09+10+13+15+17)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">69</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo do PIS anteriores</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">42</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Valor do I.Renda (41*15%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">70</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo do PIS do mês (- R$ 10,00)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">43</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">IRRF (18+19)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">71</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">PIS a Pagar</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">44</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Deduções/Compensações (20+21)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">72</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Base de Cálculo para COFINS (39+10+12-36)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">45</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo IRRF/Compensa e ded. Anterior</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">73</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Valor do COFINS sobre Fatur. (72*3%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">46</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda devido (42-43-44-45)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">74</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">COFINS Ret. Na Fonte/Ded. (35+37)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">47</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo IRRF/Compensa e ded.  a transportar</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">75</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo COFINS Ret.fonte/Ded.anterior</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">48</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo de I.Renda anteriores</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">76</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">COFINS  Devida (73-74-75)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">49</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo de I.Renda do trimestre (-R$ 10,00)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">77</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo COFINS Ret.fonte/Ded.a transportar</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">50</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">I.Renda a Pagar</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">78</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo do COFINS anteriores</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">51</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda Trimestral</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">79</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo do COFINS anteriores</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">52</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Contr.Social s/Fatur(((01+02+07)-30*12%)+(((03+08)-(31*32%))))</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">80</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">COFINS a Pagar</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">53</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Base para Contrib.Social (52+09+10+13+14)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">81</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Lucro do mês (41+14)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">54</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Valor da Contribuição Social (53*9%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">82</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Excesso (Maior que R$ 20.000,00)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">55</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Contrib. Social Ret.fonte / Compens. (28+29)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">83</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Adicional I.Renda Mensal (82*10%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">56</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo Contrib.Social Ret.fonte/Compesação anterior</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">84</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Lucro Trimestral</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">57</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Contribuição Social Devida (54-55-56)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">85</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Excesso (Maior que R$ 60.000,00)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">58</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Saldo Contrib.Social Ret.fonte/Compesação a transportar</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">86</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Adic. I.Renda Trimestral a Pagar ((85*10%)-22)</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">59</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo de Contribuição Social anteriores</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">87</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda sobre Renda Variável (14*15%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">60</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Residuo de Contrib.Social do Trimestre (-R$ 10,00)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">88</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda retido s/renda variável</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">61</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Contribuição Social a Pagar</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">89</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Vlr a pagar de I.Renda s/renda var. (87-88)</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">62</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Contribuição Social Trimestral</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">90</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda s/renda variável trimestral</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">63</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Base de Calculo para PIS (39+10+11-33)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">91</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda Inflacionado</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">64</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">Valor do PIS s/ Fatur.  (63*0,65%)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">92</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">I.Renda Inflacionado trimestral</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">65</td>
        <td style="text-align: left; border: none; border-right: 1px #000 solid;">PIS Ret Fonte/Ded. (32+34)</td>
        <td style="text-align: right; border: none; border-right: 1px #000 solid;"></td>
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">93</td>
        <td style="text-align: left; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Lucro dos sócios(41-42-54-64-73-83)</td>
        <td style="text-align: right; border: 1px #000 solid; color: #FF0000; font-weight: bold;"></td>
    </tr>
    <tr style="">
        <td colspan="6" style="text-align: center; border: none; border-top: 1px #000 solid;">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none;">&nbsp;</td>
        <td colspan="2" style="text-align: center; background-color: #FFFF00; border: 1px #000 solid; border-right: 1px #000 solid; border-right: 1px #000 solid; color: #FF0000; font-weight: bold;">Recolhimentos</td>
        <td rowspan="8" style="text-align: right; border: none;">&nbsp;</td>
        <td rowspan="6" colspan="2" style="text-align: right; border: none; border-bottom: 1px #000 solid;">&nbsp;</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none;">&nbsp;</td>
        <td style="text-align: center; background-color: #366092; border-left: 1px #000 solid; border: 1px #000 solid; color: #FFF; font-weight: bold;">Darf</td>
        <td style="text-align: center; background-color: #366092; border-left: 1px #000 solid; border: 1px #000 solid; color: #FFF; font-weight: bold;">Codigo</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid; border-top: 1px #000 solid;">50</td>
        <td style="text-align: left; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">IRPJ a Pagar</td>
        <td style="text-align: center; background-color: #FFFF00; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">2089</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">61</td>
        <td style="text-align: left; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Contribuição Social a Pagar (CSLL)</td>
        <td style="text-align: center; background-color: #FFFF00; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">2372</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">71</td>
        <td style="text-align: left; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">PIS s/Faturamento a Pagar</td>
        <td style="text-align: center; background-color: #FFFF00; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">8109</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">80</td>
        <td style="text-align: left; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">COFINS a Pagar</td>
        <td style="text-align: center; background-color: #FFFF00; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">2172</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid;">86</td>
        <td style="text-align: left; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Adic. I.Renda Trimestral a Pagar</td>
        <td style="text-align: center; background-color: #FFFF00; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">2089</td>
        <td colspan="2" style="text-align: center; border: none; font-weight: bold;">Rogério Assis</td>
    </tr>
    <tr style="">
        <td style="text-align: center; border: none; border-left: 1px #000 solid; border-bottom: 1px #000 solid;">89</td>
        <td style="text-align: left; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">Vlr a pagar de I.Renda s/renda var.</td>
        <td style="text-align: center; background-color: #FFFF00; border-left: 1px #000 solid; border: 1px #000 solid; color: #FF0000; font-weight: bold;">6015</td>
        <td colspan="2" style="text-align: center; border: none; background-color: #CCC;">Contador - CRC - 7074512587-6-RJ</td>
    </tr>
</table>