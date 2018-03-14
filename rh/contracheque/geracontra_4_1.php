<?php

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";

// RECEBENDO VARIAVEIS
$enc = $_REQUEST['enc'];
$enc = str_replace("--", "+", $enc);
$link = decrypt($enc);
$decript = explode("&", $link);
$regiao = $decript[0];
$clt = $decript[1];
$id_folha = $decript[2];
//

$data = date('d/m/Y');
$ClassDATA = new regiao();
$ClassDATA->RegiaoLogado();
$Clt = new clt();
$Curso = new tabcurso();

$REFolha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$RowFolha = mysql_fetch_array($REFolha);

$mes = $RowFolha['mes'];
$ano = $RowFolha['ano'];




/////// REGIÃO
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

/// PROJETO
$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto= '$RowFolha[projeto]'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

//////MASTER
$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


//////EMPRESA
$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_projeto = '$RowFolha[projeto]' AND id_regiao = '$RowFolha[regiao]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);


//MÊs
$num_mes = sprintf('%02s', $mes);
$nome_mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$num_mes'"), 0);

$mes_ar = array(
    "01" => "jan",
    "02" => "fev",
    "03" => "mar",
    "04" => "abr",
    "05" => "mai",
    "06" => "jun",
    "07" => "jul",
    "08" => "ago",
    "09" => "set",
    "10" => "out",
    "11" => "nov",
    "12" => "dez"
);

if ($clt == "todos") {
    $ini = $_REQUEST['ini'];
    $fim = $_REQUEST['fim'];
    $REfolhaproc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status = '3'  ORDER BY nome LIMIT $ini,50") or die(mysql_error());
    $NumRegistros = mysql_num_rows($REfolhaproc);
    $nomearquivo = "contracheques_clt.pdf";
} else {
    $REfolhaproc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_clt = '$clt' AND id_folha = '$id_folha'");
    $nomearquivo = "contracheque_unico_clt.pdf";
}

require("../fpdf/fpdf.php");
define('FPDF_FONTPATH', '../fpdf/font/');

$altura_celula = 0.5;
$largura_celula = 13;

$distancia = 2;

$pdf = new FPDF("L", "cm", "A4");

while ($RowFolhaPro = mysql_fetch_array($REfolhaproc)) {

    /////////////////////////////////////////////////////////////
    //////////  CÁLCULO DE DEPENDENTES ///////////////////////////
    ////////////////////////////////////////////////////////////
    $data_menor21 = mktime(0, 0, 0, $mes, $dia, $ano - 21);
    $menor21 = mysql_query("SELECT * 	FROM dependentes 
							WHERE id_bolsista = '$RowFolhaPro[id_clt]'					
							AND id_regiao = '$regiao'
							 ") or die(mysql_error());

    $row_menor21 = mysql_fetch_assoc($menor21);

    if (mysql_num_rows($menor21) != 0) {
        if ($row_menor21['data1'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data2'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data3'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data4'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data5'] != '0000-00-00') {
            $filhos++;
        }
        if ($row_menor21['data6'] != '0000-00-00') {
            $filhos++;
        }

        $data1 = explode('-', $row_menor21['data1']);
        $data1 = @mktime(0, 0, 0, $data1[1], $data1[2], $data1[0]);

        $data2 = explode('-', $row_menor21['data2']);
        $data2 = @mktime(0, 0, 0, $data2[1], $data2[2], $data2[0]);

        $data3 = explode('-', $row_menor21['data3']);
        $data3 = @mktime(0, 0, 0, $data3[1], $data3[2], $data3[0]);

        $data4 = explode('-', $row_menor21['data4']);
        $data4 = @mktime(0, 0, 0, $data4[1], $data4[2], $data4[0]);

        $data5 = explode('-', $row_menor21['data5']);
        $data5 = @mktime(0, 0, 0, $data5[1], $data5[2], $data5[0]);

        $data6 = explode('-', $row_menor21['data6']);
        $data6 = @mktime(0, 0, 0, $data6[1], $data6[2], $data6[0]);

        if ($data1 > $data_menor21 and $row_menor21['data1'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data2 > $data_menor21 and $row_menor21['data2'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data3 > $data_menor21 and $row_menor21['data3'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data4 > $data_menor21 and $row_menor21['data4'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data5 > $data_menor21 and $row_menor21['data5'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
        if ($data6 > $data_menor21 and $row_menor21['data6'] != '0000-00-00') {
            $total_filhos_menor_21++;
        }
    }
    if (empty($filhos)) {
        $filhos = 0;
    }
    if (empty($total_filhos_menor_21)) {
        $total_filhos_menor_21 = 0;
    }

    ///////////////////////////////////////////////////

    $qr_clt = mysql_query("SELECT *,DATE_FORMAT(data_entrada, '%d/%m/%Y') as databr, DATE_FORMAT(data_nasci, '%m') as mes_nasci FROM rh_clt WHERE id_clt = '$RowFolhaPro[id_clt]'");
    $row_clt = mysql_fetch_assoc($qr_clt);

    //////////////////////////// AIVERSÁRIO /////////////////////////////
    if ($num_mes == $row_clt['mes_nasci']) {
        $obs = "A EMPRESA $row_master[nome] DESEJA-LHE UM FELIZ ANIVERSÁRIO!";
    } else {
        $obs = '';
    }

    //BANCO
    $qr_banco = mysql_query("SELECT razao FROM bancos WHERE id_banco = '{$row_clt['banco']}'");
    $banco = mysql_fetch_assoc($qr_banco);

    /////FUNÇÃO
    $qr_funcao = mysql_query("SELECT * FROM  curso where id_curso = '$row_clt[id_curso]' ");
    $row_funcao = mysql_fetch_assoc($qr_funcao);
    
    //////////////////////////////////////////////////////////////////////
    /////////////////////////INICIO DO PDF ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTopMargin(1);

    ////LINHA 1
    $pdf->Cell($largura_celula, 2.1, null, 1, '0', 'C');
    $pdf->Text(3.5, 1.6, $row_empresa['razao']);
    $pdf->Text(3.5, 2, "CNPJ: " . $row_empresa['cnpj']);
    $pdf->Text(3.5, 2.4, $row_empresa['endereco']);
    $pdf->Text(3.5, 2.8, "CEP: " . $row_empresa['cep']);
    $pdf->Text(5.8, 2.8, "Tel: " . $row_empresa['tel'] . " / " . $row_empresa['fax']);
    $pdf->Image('../../imagens/logomaster' . $row_master['id_master'] . '.gif', 1.1, 1.1, 2.3, 1.8, 'gif');

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, 2.1, null, 1, '0', 'C');
    $pdf->Text(3.5 + $largura_celula + $distancia, 1.6, $row_empresa['razao']);
    $pdf->Text(3.5 + $largura_celula + $distancia, 2, "CNPJ: " . $row_empresa['cnpj']);
    $pdf->Text(3.5 + $largura_celula + $distancia, 2.4, $row_empresa['endereco']);
    $pdf->Text(3.5 + $largura_celula + $distancia, 2.8, "CEP: " . $row_empresa['cep']);
    $pdf->Text(5.8 + $largura_celula + $distancia, 2.8, "Tel: " . $row_empresa['tel'] . " / " . $row_empresa['fax']);
    $pdf->Image('../../imagens/logomaster' . $row_master['id_master'] . '.gif', 1.1 + $largura_celula + $distancia, 1.1, 2.3, 1.8, 'gif');

    $pdf->Ln(2.1);

    $altura_atual = 2.8;
    $altura_celula_n = $altura_celula + 0.3;

    ///LINHA 2
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln($altura_celula);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

    $pdf->Line(2.9, $altura_atual + 0.8, 2.9, $altura_atual + 1.6);
    $pdf->Line(11.7, $altura_atual + 0.8, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Mês:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $mes_ar[$num_mes] . '/' . $ano);
    $pdf->Text(3.1, 1.1 + $altura_atual, 'Nome:');
    $pdf->Text(3.1, 1.5 + $altura_atual, $row_clt['nome']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Cod Funcionário:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row_clt['matricula']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

    $pdf->Line(2.9 + $largura_celula + $distancia, $altura_atual + 0.8, 2.9 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(11.7 + $largura_celula + $distancia, $altura_atual + 0.8, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Mês:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $mes_ar[$num_mes] . '/' . $ano);
    $pdf->Text(3.1 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Nome:');
    $pdf->Text(3.1 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['nome']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Cod Funcionário:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['matricula']);


    $altura_atual += 0.8;

    //LINHA 3
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7, $altura_atual + 0.8, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Cargo:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row_funcao['nome']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Data de Admissão:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row_clt['databr']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7 + $largura_celula + $distancia, $altura_atual + 0.8, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Cargo:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_funcao['nome']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Data de Admissão:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['databr']);


    $altura_atual += 0.8;

    //LINHA 4
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7, 0.8 + $altura_atual, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Unidade:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row_clt['locacao']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'PIS:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row_clt['pis']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Unidade:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['locacao']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'PIS:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['pis']);


    $altura_atual += 0.8;

    //LINHA 5
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7, 0.8 + $altura_atual, 3.7, $altura_atual + 1.6);
    $pdf->Line(7.3, 0.8 + $altura_atual, 7.3, $altura_atual + 1.6);
    $pdf->Line(11.7, 0.8 + $altura_atual, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'CPF:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row_clt['cpf']);
    $pdf->Text(4, 1.1 + $altura_atual, 'RG:');
    $pdf->Text(4, 1.5 + $altura_atual, $row_clt['rg']);
    $pdf->Text(7.5, 1.1 + $altura_atual, 'Carteira de Trabalho:');
    $pdf->Text(7.5, 1.5 + $altura_atual, $row_clt['campo1']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Série:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row_clt['serie_ctps']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 3.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(7.3 + $largura_celula + $distancia, 0.8 + $altura_atual, 7.3 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(11.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'CPF:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['cpf']);
    $pdf->Text(4 + $largura_celula + $distancia, 1.1 + $altura_atual, 'RG:');
    $pdf->Text(4 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['rg']);
    $pdf->Text(7.5 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Carteira de Trabalho:');
    $pdf->Text(7.5 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['campo1']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Série:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['serie_ctps']);


    $altura_atual += 0.8;

    //LINHA 6
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7, 0.8 + $altura_atual, 3.7, $altura_atual + 1.6);
    $pdf->Line(11.7, 0.8 + $altura_atual, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Banco:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $banco['razao']);
    $pdf->Text(4, 1.1 + $altura_atual, 'Agência:');
    $pdf->Text(4, 1.5 + $altura_atual, $row_clt['agencia']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Conta corrente:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row_clt['conta']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 3.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(11.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Banco:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $banco['razao']);
    $pdf->Text(4 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Agência:');
    $pdf->Text(4 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['agencia']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Conta corrente:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row_clt['conta']);

    //LINHA 7 (CABEÇALHO MOVIMENTOS)
    $pdf->Ln($altura_celula + 0.3);
    $pdf->SetFont('Arial', 'B', 6);

    $pdf->Line(2.3, 1.6 + $altura_atual, 2.3, $altura_atual + 2);
    $pdf->Line(8.5, 1.6 + $altura_atual, 8.5, $altura_atual + 2);
    $pdf->Line(10, 1.6 + $altura_atual, 10, $altura_atual + 2);
    $pdf->Line(12, 1.6 + $altura_atual, 12, $altura_atual + 2);

    $pdf->Cell($largura_celula, $altura_celula_n - 0.4, '', 1, '0', 'L');
    $pdf->Text(1.3, 1.9 + $altura_atual, 'Código');
    $pdf->Text(4.6, 1.9 + $altura_atual, 'Descrição');
    $pdf->Text(8.7, 1.9 + $altura_atual, 'Frequência');
    $pdf->Text(10.4, 1.9 + $altura_atual, 'Vencimento');
    $pdf->Text(12.4, 1.9 + $altura_atual, 'Descontos');

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Line(2.3 + $largura_celula + $distancia, 1.6 + $altura_atual, 2.3 + $largura_celula + $distancia, $altura_atual + 2);
    $pdf->Line(8.5 + $largura_celula + $distancia, 1.6 + $altura_atual, 8.5 + $largura_celula + $distancia, $altura_atual + 2);
    $pdf->Line(10 + $largura_celula + $distancia, 1.6 + $altura_atual, 10 + $largura_celula + $distancia, $altura_atual + 2);
    $pdf->Line(12 + $largura_celula + $distancia, 1.6 + $altura_atual, 12 + $largura_celula + $distancia, $altura_atual + 2);

    $pdf->Cell($largura_celula, $altura_celula_n - 0.4, '', 1, '0', 'L');
    $pdf->Text(1.3 + $largura_celula + $distancia, 1.9 + $altura_atual, 'Código');
    $pdf->Text(4.6 + $largura_celula + $distancia, 1.9 + $altura_atual, 'Descrição');
    $pdf->Text(8.7 + $largura_celula + $distancia, 1.9 + $altura_atual, 'Frequência');
    $pdf->Text(10.4 + $largura_celula + $distancia, 1.9 + $altura_atual, 'Vencimento');
    $pdf->Text(12.4 + $largura_celula + $distancia, 1.9 + $altura_atual, 'Descontos');

    //////////////////EXIBINDO OS MOVIMENTOS
    //
    $row_falta = FALSE;

    if (!empty($RowFolha['ids_movimentos_estatisticas'])) {
        //verifica_falta
        $qr_movimento_clt = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  id_movimento IN($RowFolha[ids_movimentos_estatisticas]) AND id_clt = '$RowFolhaPro[id_clt]' AND id_mov  IN(62)") or die(mysql_error());
        $row_falta = mysql_fetch_assoc($qr_movimento_clt);
    }

    $salb = $RowFolhaPro['sallimpo_real'] + $row_falta['valor_movimento'];
    $total_vencimentos = $salb;

    $diasTrab = $RowFolhaPro['dias_trab'];

    ////////////////////////////////////////
    ///////////SALÁRIO BASE ///////////////
    ///////////////////////////////////////

    $pdf->SetFont('Arial', 'B', 6);
    $borda = 0;
    $altura_mov = 0.4;
    $cod_salario = '0001';
    $nome_salario = 'SALÁRIO BASE';

    if ($RowFolha['terceiro'] != 1 and $salb != '0.00') {



        $pdf->Ln(0.6);
        $pdf->Cell(1.3, $altura_mov, $cod_salario, $borda, '0', 'C');
        $pdf->Cell(6.2, $altura_mov, $nome_salario, $borda, '0', 'L');
        $pdf->Cell(1.5, $altura_mov, $diasTrab, $borda, '0', 'C');
        $pdf->Cell(2, $altura_mov, number_format($salb, 2, ',', '.'), $borda, '0', 'R');
        $pdf->Cell(2, $altura_mov, '', $borda, '0', 'C');

        $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

        $pdf->Cell(1.3, $altura_mov, $cod_salario, $borda, '0', 'C');
        $pdf->Cell(6.2, $altura_mov, $nome_salario, $borda, '0', 'L');
        $pdf->Cell(1.5, $altura_mov, $diasTrab, $borda, '0', 'C');
        $pdf->Cell(2, $altura_mov, number_format($salb, 2, ',', '.'), $borda, '0', 'R');
        $pdf->Cell(2, $$altura_mov, '', $borda, '0', 'C');
    }
    ///////////////////////////////////////

    $array_outros_movimentos = array(5060, 5061, 9999, 5913, 5912, 9997, 10008, 10009, 10007, 10006, 10005, 10004, 10003, 10002, 10001, 10000, 6008, 8080, 5049);

    $qr_movimentos = mysql_query("SELECT DISTINCT (cod), descicao, categoria FROM rh_movimentos WHERE mov_lancavel !=1  AND id_mov NOT IN(77) AND cod NOT IN(0001,9991,50241,9996,5024,50250,5044)  ORDER BY cod ASC") or die(mysql_error());
    while ($row_mov = mysql_fetch_assoc($qr_movimentos)):

        $nome_campo = 'a' . $row_mov['cod'];
        $categoria = $row_mov['categoria'];
        $valor_movimento = $RowFolhaPro[$nome_campo];
        $referencia = "";

        if ($valor_movimento != '0.00' and ! in_array($row_mov['cod'], $array_outros_movimentos)) {
            switch ($row_mov['cod']) {
                case '5029': $total_decimo = $valor_movimento;
                    $total_vencimentos += $total_decimo;
                    //echo $total_decimo;
                    break;
                default :
                    if ($row_mov['cod'] == '5031') {
                        $fgts_13 = $valor_movimento;
                    }
                    if ($row_mov['cod'] == '5020') {
                        $referencia = ($RowFolhaPro["t_inss"] * 100) . "%";
                    }
                    if ($row_mov['cod'] == '5021') {
                        $referencia = ($RowFolhaPro["t_imprenda"] * 100) . "%";
                    }
                    if ($row_mov['cod'] == '5030') {
                        $irrf_13 = $valor_movimento;
                    }
                    if ($row_mov['cod'] == '6006') {
                        $referencia = "20%";
                        $nao_exibe_insalubridade = 1;
                    }
                    if ($row_mov['cod'] == '7001') {
                        $referencia = "6%";
                    }

                    if ($categoria == 'CREDITO') {
                        $vencimentos = number_format($valor_movimento, 2, ",", ".");
                        $desconto = '';
                        $total_vencimentos += $valor_movimento;
                    } else if ($categoria == 'DEBITO' or $categoria == 'DESCONTO') {
                        $desconto = number_format($valor_movimento, 2, ",", ".");
                        $vencimentos = '';
                        $total_desconto += $valor_movimento;
                    }

                    $pdf->Ln(0.4);
                    $pdf->Cell(1.3, $altura_mov, $row_mov['cod'], $borda, '0', 'C');
                    $pdf->Cell(6.2, $altura_mov, $row_mov['descicao'], $borda, '0', 'L');
                    $pdf->Cell(1.5, $altura_mov, $referencia, $borda, '0', 'C');
                    $pdf->Cell(2, $altura_mov, $vencimentos, $borda, '0', 'R');
                    $pdf->Cell(2, $altura_mov, $desconto, $borda, '0', 'R');

                    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

                    $pdf->Cell(1.3, $altura_mov, $row_mov['cod'], $borda, '0', 'C');
                    $pdf->Cell(6.2, $altura_mov, $row_mov['descicao'], $borda, '0', 'L');
                    $pdf->Cell(1.5, $altura_mov, $referencia, $borda, '0', 'C');
                    $pdf->Cell(2, $altura_mov, $vencimentos, $borda, '0', 'R');
                    $pdf->Cell(2, $altura_mov, $desconto, $borda, '0', 'R');

                    //EXIBINDO O VALOR DAS FÉRIAS DESCONTANDO 
                    if ($row_mov['cod'] == '5037') {

                        $total_desconto += $valor_movimento;
                        $pdf->Ln(0.4);
                        $pdf->Cell(1.3, $altura_mov, $row_mov['cod'], $borda, '0', 'C');
                        $pdf->Cell(6.2, $altura_mov, $row_mov['descicao'], $borda, '0', 'L');
                        $pdf->Cell(1.5, $altura_mov, $referencia, $borda, '0', 'C');
                        $pdf->Cell(2, $altura_mov, '', $borda, '0', 'R');
                        $pdf->Cell(2, $altura_mov, number_format($valor_movimento, 2, ',', '.'), $borda, '0', 'R');

                        $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

                        $pdf->Cell(1.3, $altura_mov, $row_mov['cod'], $borda, '0', 'C');
                        $pdf->Cell(6.2, $altura_mov, $row_mov['descicao'], $borda, '0', 'L');
                        $pdf->Cell(1.5, $altura_mov, $referencia, $borda, '0', 'C');
                        $pdf->Cell(2, $altura_mov, '', $borda, '0', 'R');
                        $pdf->Cell(2, $altura_mov, number_format($valor_movimento, 2, ',', '.'), $borda, '0', 'R');
                    }
            }
        }
    endwhile;


    if (!empty($RowFolhaPro['ids_movimentos'])) {

        $qr_movimento_clt = mysql_query("SELECT A.*,B.percentual FROM rh_movimentos_clt AS A LEFT JOIN rh_movimentos AS B ON (A.id_mov=B.id_mov) WHERE A.id_movimento IN($RowFolha[ids_movimentos_estatisticas]) AND A.id_clt = '$RowFolhaPro[id_clt]' ") or die(mysql_error());
        //echo "SELECT A.*,B.percentual FROM rh_movimentos_clt AS A LEFT JOIN rh_movimentos AS B ON (A.id_mov=B.id_mov) WHERE A.id_movimento IN($RowFolha[ids_movimentos_estatisticas]) AND A.id_clt = '$RowFolhaPro[id_clt]'";
        if (mysql_num_rows($qr_movimento_clt) != 0) {

            while ($row_mov2 = mysql_fetch_assoc($qr_movimento_clt)):

                if ($row_mov2['cod_movimento'] == '6006' and $nao_exibe_insalubridade == 1)
                    continue;

                if ($row_mov2['tipo_movimento'] == 'CREDITO') {

                    $vencimentos = number_format($row_mov2['valor_movimento'], 2, ",", ".");
                    $desconto = '';
                    $total_vencimentos += $row_mov2['valor_movimento'];
                    $total_decimo += $row_mov2['valor_movimento'];
                } else if ($row_mov2['tipo_movimento'] == 'DEBITO' or $row_mov2['tipo_movimento'] == 'DESCONTO') {


                    $desconto = number_format($row_mov2['valor_movimento'], 2, ",", ".");
                    $vencimentos = '';
                    $total_desconto += $row_mov2['valor_movimento'];
                }

                $percent = (strstr($row_mov2['percentual'], ".")) ? ($row_mov2['percentual'] * 100) . "%" : $row_mov2['percentual'];
                $percent = ($percent == 0) ? $row_mov2['qnt'] : $percent;
                $percent = ($percent != '(NULL)') ? $percent : '-';



                $tipo_quantidade = "";
                if ($row_mov2['tipo_qnt'] == 1) {
                    $tipo_quantidade = "horas";
                } else if ($row_mov2['tipo_qnt'] == 2) {
                    $tipo_quantidade = "dias";
                } else {
                    $tipo_quantidade = "";
                }

                if ($row_mov2['cod_movimento'] == "9997") {
                    $qr = "SELECT COUNT(data) as total FROM ano WHERE YEAR(data) = {$ano} AND MONTH(data) = {$mes} AND fds = 1 AND nome = 'Domingo'";
                    $percent = mysql_result(mysql_query($qr), 0);
                }

                if ($RowFolha['terceiro'] != 1) {
                    $altura_tabela_mov = $altura_tabela_mov + $altura_mov;

                    $pdf->Ln(0.4);
                    $pdf->Cell(1.3, $altura_mov, $row_mov2['cod_movimento'], $borda, '0', 'C');
                    $pdf->Cell(6.2, $altura_mov, $row_mov2['nome_movimento'], $borda, '0', 'L');
                    $pdf->Cell(1.5, $altura_mov, $percent . " " . $tipo_quantidade, $borda, '0', 'C');
                    $pdf->Cell(2, $altura_mov, $vencimentos, $borda, '0', 'R');
                    $pdf->Cell(2, $altura_mov, $desconto, $borda, '0', 'R');


                    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

                    $pdf->Cell(1.3, $altura_mov, $row_mov2['cod_movimento'], $borda, '0', 'C');
                    $pdf->Cell(6.2, $altura_mov, $row_mov2['nome_movimento'], $borda, '0', 'L');
                    $pdf->Cell(1.5, $altura_mov, $percent . " " . $tipo_quantidade, $borda, '0', 'C');
                    $pdf->Cell(2, $altura_mov, $vencimentos, $borda, '0', 'R');
                    $pdf->Cell(2, $altura_mov, $desconto, $borda, '0', 'R');
                }
            endwhile;
        }
    }



    /////caso SEJA Décimo terceiro
    if ($RowFolha['terceiro'] == 1) {
        $altura_tabela_mov = $altura_tabela_mov + $altura_mov;

        $pdf->Ln(0.4);
        $pdf->Cell(1.3, $altura_mov, '5029', $borda, '0', 'C');
        $pdf->Cell(6.2, $altura_mov, 'DÉCIMO TERCEIRO', $borda, '0', 'L');
        $pdf->Cell(1.5, $altura_mov, '', $borda, '0', 'C');
        $pdf->Cell(2, $altura_mov, number_format($total_decimo, 2, ',', '.'), $borda, '0', 'R');
        $pdf->Cell(2, $altura_mov, '', $borda, '0', 'R');

        $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

        $pdf->Cell(1.3, $altura_mov, '5029', $borda, '0', 'C');
        $pdf->Cell(6.2, $altura_mov, 'DÉCIMO TERCEIRO', $borda, '0', 'L');
        $pdf->Cell(1.5, $altura_mov, '', $borda, '0', 'C');
        $pdf->Cell(2, $altura_mov, number_format($total_decimo, 2, ',', '.'), $borda, '0', 'R');
        $pdf->Cell(2, $altura_mov, '', $borda, '0', 'R');
    }


    /////////DESENHANDO FUNDO DA TABELA DE MOVIMENTOS
    $altura_fundo = 6.7;
    $altura_atual += 2;
    $pdf->Rect(1, $altura_atual, 1.3, $altura_fundo);
    $pdf->Rect(2.29, $altura_atual, 6.2, $altura_fundo);
    $pdf->Rect(8.5, $altura_atual, 1.5, $altura_fundo);
    $pdf->Rect(10, $altura_atual, 2, $altura_fundo);
    $pdf->Rect(12, $altura_atual, 2, $altura_fundo);

    $pdf->Rect(16, $altura_atual, 1.3, $altura_fundo);
    $pdf->Rect(17.3, $altura_atual, 6.2, $altura_fundo);
    $pdf->Rect(23.5, $altura_atual, 1.5, $altura_fundo);
    $pdf->Rect(25, $altura_atual, 2, $altura_fundo);
    $pdf->Rect(27, $altura_atual, 2, $altura_fundo);

    ////////////////
    ////////LIINHA TOTAIS
    $altura_ag = 13.9;

    $pdf->Rect(1, 14.7, 13, 0.8);
    $pdf->Line(8.5, 0.8 + $altura_ag, 8.5, $altura_ag + 1.6);
    $pdf->Line(12, 0.8 + $altura_ag, 12, $altura_ag + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_ag, 'Valor Bruto:');
    $pdf->Text(3.4, 1.5 + $altura_ag, "R$ " . number_format($total_vencimentos, 2, ',', '.'));
    $pdf->Text(8.7, 1.1 + $altura_ag, 'Total dos Descontos:');
    $pdf->Text(9.5, 1.5 + $altura_ag, "R$ " . number_format($total_desconto, 2, ',', '.'));
    $pdf->Text(12.1, 1.1 + $altura_ag, 'Valor Líquido:');
    $pdf->Text(12.2, 1.5 + $altura_ag, "R$ " . number_format($RowFolhaPro['salliquido'], 2, ",", "."));

    $pdf->Rect(16, 14.7, 13, 0.8);
    $pdf->Line(8.5 + $largura_celula + $distancia, 0.8 + $altura_ag, 8.5 + $largura_celula + $distancia, $altura_ag + 1.6);
    $pdf->Line(12 + $largura_celula + $distancia, 0.8 + $altura_ag, 12 + $largura_celula + $distancia, $altura_ag + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_ag, 'Valor Bruto:');
    $pdf->Text(3.4 + $largura_celula + $distancia, 1.5 + $altura_ag, "R$ " . number_format($total_vencimentos, 2, ',', '.'));
    $pdf->Text(8.7 + $largura_celula + $distancia, 1.1 + $altura_ag, 'Total dos Descontos:');
    $pdf->Text(9.5 + $largura_celula + $distancia, 1.5 + $altura_ag, "R$ " . number_format($total_desconto, 2, ',', '.'));
    $pdf->Text(12.1 + $largura_celula + $distancia, 1.1 + $altura_ag, 'Valor Líquido:');
    $pdf->Text(12.2 + $largura_celula + $distancia, 1.5 + $altura_ag, "R$ " . number_format($RowFolhaPro['salliquido'], 2, ",", "."));

    /* LINHA DETALHE DE CALULO */
    if ($RowFolha['terceiro'] == 1) {
        $salario_base = $total_decimo;
        $fgts = $fgts_13;
        $ir = (!empty($irrf_13)) ? $salario_base - $fgts_13 : '0';
    } else {
        $salario_base = $RowFolhaPro['sallimpo'];
        $fgts = $RowFolhaPro['fgts'];
        $ir = $RowFolhaPro['base_irrf'];
    }

    $pdf->Ln($altura_celula + 0.7);
    $altura_ag += 1.2;

    $pdf->Rect(1, 15.7, 13, 0.8);
    $pdf->Line(4, 0.6 + $altura_ag, 4, $altura_ag + 1.4);
    $pdf->Line(8.5, 0.6 + $altura_ag, 8.5, $altura_ag + 1.4);
    $pdf->Line(11.3, 0.6 + $altura_ag, 11.3, $altura_ag + 1.4);
    $pdf->Text(1.2, 0.9 + $altura_ag, 'Salário Base:');
    $pdf->Text(1.5, 1.3 + $altura_ag, "R$ " . number_format($RowFolhaPro['salbase'], 2, ',', '.'));
    $pdf->Text(4.3, 0.9 + $altura_ag, 'Salário:');
    $pdf->Text(4.6, 1.3 + $altura_ag, "R$ " . number_format($salario_base, 2, ',', '.'));
    $pdf->Text(8.7, 0.9 + $altura_ag, 'FGTS:');
    $pdf->Text(9.2, 1.3 + $altura_ag, "R$ " . number_format($fgts, 2, ',', '.'));
    $pdf->Text(11.5, 0.9 + $altura_ag, 'Base Calculo IRRF:');
    $pdf->Text(11.8, 1.3 + $altura_ag, "R$ " . number_format($ir, 2, ",", "."));

    $pdf->Rect(16, 15.7, 13, 0.8);
    $pdf->Line(8.5 + $largura_celula + $distancia, 0.6 + $altura_ag, 8.5 + $largura_celula + $distancia, $altura_ag + 1.4);
    $pdf->Line(11.3 + $largura_celula + $distancia, 0.6 + $altura_ag, 11.3 + $largura_celula + $distancia, $altura_ag + 1.4);
    $pdf->Text(1.2 + $largura_celula + $distancia, 0.9 + $altura_ag, 'Salário Base:');
    $pdf->Text(1.5 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($RowFolhaPro['salbase'], 2, ',', '.'));
    $pdf->Text(4.3 + $largura_celula + $distancia, 0.9 + $altura_ag, 'Salário:');
    $pdf->Text(4.6 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($salario_base, 2, ',', '.'));
    $pdf->Text(8.7 + $largura_celula + $distancia, 0.9 + $altura_ag, 'FGTS:');
    $pdf->Text(9.2 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($fgts, 2, ',', '.'));
    $pdf->Text(11.5 + $largura_celula + $distancia, 0.9 + $altura_ag, 'Base Calculo IRRF:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($ir, 2, ",", "."));

    $pdf->Ln($altura_celula + 0.7);


    /////////////////
    //////////////////DATA E ASSINATURA
    $pdf->Rect(1, 16.8, 13, 1.9);
    $pdf->Rect(16, 16.8, 13, 1.9);

    $pdf->SetFontSize('9');

    $y = 18.2;
    $pdf->Text(1.3, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
    $pdf->Text(1.3, $y, '____/____/_____');
    $pdf->Text(2, $y + 0.4, 'Data');

    $pdf->Text(6, $y, '__________________________________________');
    $pdf->Text(7.6, $y + 0.4, 'Assinatura do funcionário');

    $pdf->Text(16.2, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
    $pdf->Text(16.3, $y, '____/____/_____');
    $pdf->Text(17, $y + 0.4, 'Data');

    $pdf->Text(21, $y, '__________________________________________');
    $pdf->Text(23, $y + 0.4, 'Assinatura do funcionário');

    // obs
    $pdf->SetFontSize('6');
    $pdf->Rect(1, 19, 13, 0.8);
    $pdf->Rect(16, 19, 13, 0.8);
    $pdf->Text(1.3, 19.5, $obs);
    $pdf->Text(16.3, 19.5, $obs);


    unset($filhos, $total_filhos_menor_21, $total_decimo, $ir, $total_desconto, $total_vencimentos);
}


$pdf->Output('as.pdf', 'I');
?>