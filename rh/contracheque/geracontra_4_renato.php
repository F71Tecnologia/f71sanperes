<?php
class ContraCheque {

    private $dados = array();
    
    public function getDadosContraCheque($id_folha, $id_clt, $ini, $ordenacao) {
        if(!empty($id_clt) AND $id_clt != 'todos')
            if(is_array($id_clt))
                $auxClt = " AND A.id_clt IN(".implode(',', $id_clt).") ";
            else
                $auxClt = " AND A.id_clt IN($id_clt) ";
        $query = "
        SELECT clt.*, D.id_curso, D.nome as nome_funcao, E.id_unidade, E.unidade as clt_unidade, J.razao rBanco, I.razao rEmpresa, I.cnpj, I.endereco, I.cep, I.tel, I.fax, H.id_master, H.nome nomeMaster
        FROM
            (SELECT A.*, B.banco, B.nome nomeClt, B.matricula, DATE_FORMAT(B.data_entrada, '%d/%m/%Y')as databr, DATE_FORMAT(B.data_nasci, '%m') as mes_nasci, B.agencia cltAgencia, B.conta cltConta,
                B.rg, B.campo1, B.serie_ctps, B.sexo, B.num_filhos, C.ids_movimentos_estatisticas, B.id_unidade, C.terceiro,
                (SELECT id_curso_de FROM rh_transferencias WHERE id_clt = B.id_clt AND id_curso_de <> id_curso_para AND data_proc >= C.data_inicio ORDER BY id_transferencia ASC LIMIT 1) AS curso_de,              
                (SELECT id_curso_para FROM rh_transferencias WHERE id_clt = B.id_clt AND id_curso_de <> id_curso_para AND data_proc <= C.data_inicio ORDER BY id_transferencia DESC LIMIT 1) AS curso_para, 
                (SELECT id_unidade_de FROM rh_transferencias WHERE id_clt = B.id_clt AND id_unidade_de <> id_unidade_para AND data_proc >= C.data_inicio ORDER BY id_transferencia ASC LIMIT 1) AS unidade_de,              
                (SELECT id_unidade_para FROM rh_transferencias WHERE id_clt = B.id_clt AND id_unidade_de <> id_unidade_para AND data_proc <= C.data_inicio ORDER BY id_transferencia DESC LIMIT 1) AS unidade_para    
            FROM rh_folha_proc A LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt) LEFT JOIN rh_folha C ON (A.id_folha = C.id_folha)
            WHERE A.id_folha = {$id_folha} AND A.status = 3 $auxClt ) clt
            LEFT JOIN curso D ON (IF(clt.curso_para IS NOT NULL,D.id_curso=clt.curso_para,IF(clt.curso_de IS NOT NULL,D.id_curso=clt.curso_de,D.id_curso=clt.id_curso)))
            LEFT JOIN unidade E ON (IF(clt.unidade_para IS NOT NULL,E.id_unidade = clt.unidade_para,IF(clt.unidade_de IS NOT NULL,E.id_unidade = clt.unidade_de,E.id_unidade=clt.id_unidade)))
            LEFT JOIN regioes F ON (F.id_regiao = clt.id_regiao)
            LEFT JOIN projeto G ON (G.id_projeto = clt.id_projeto)
            LEFT JOIN master H ON (H.id_master = F.id_master)
            LEFT JOIN rhempresa I ON (I.id_projeto = clt.id_projeto AND I.id_regiao = clt.id_regiao)
            LEFT JOIN bancos J ON (J.id_banco = clt.banco)
        ORDER BY $ordenacao clt.nome
        LIMIT $ini, 50";
        //exit($query);
        $sql = mysql_query($query) or die(mysql_error());

        return $sql;
    }
}

include "../../conn.php";
include "../../funcoes.php";

// RECEBENDO VARIAVEIS
$enc = $_REQUEST['enc'];
$enc = str_replace("--", "+", $enc);
$link = decrypt($enc);
$decript = explode("&", $link);
$regiao = $decript[0];
$clt = $decript[1];
$id_folha = $decript[2];

if(isset($_REQUEST['check_list']) && !empty($_REQUEST['check_list']))
    $clt = $_REQUEST['check_list'];

$id_folha = (!empty($_REQUEST['idFolhaSelec']) ? $_REQUEST['idFolhaSelec'] : $id_folha);

$data = date('d/m/Y');
$ContraCheque = new ContraCheque();

$mes_ar = array("01" => "jan", "02" => "fev", "03" => "mar", "04" => "abr", "05" => "mai", "06" => "jun", "07" => "jul", "08" => "ago", "09" => "set", "10" => "out", "11" => "nov", "12" => "dez");

$ini = $_REQUEST['ini'];
$fim = $_REQUEST['fim'];

require("../fpdf/fpdf.php");
define('FPDF_FONTPATH', '../fpdf/font/');
$altura_celula = 0.5;
$largura_celula = 13;
$distancia = 2;

$pdf = new FPDF("L", "cm", "A4");
$ordenacao = ' E.unidade, ';
if (is_array($clt) OR $clt == 'todos') {
    $queryContraCheque = $ContraCheque->getDadosContraCheque($id_folha, $clt, $ini, $ordenacao);
    $NumRegistros = mysql_num_rows($queryContraCheque);
    $nomearquivo = "contracheques_clt.pdf";
} else {
    $queryContraCheque = $ContraCheque->getDadosContraCheque($id_folha, $clt, $ini, $ordenacao);
    $nomearquivo = "contracheque_unico_clt.pdf";
}

while ($row = mysql_fetch_array($queryContraCheque)) {
    
    $mes = $row['mes'];
    $ano = $row['ano'];

    //MÊs
    $num_mes = sprintf('%02s', $mes);
    $nome_mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$num_mes'"), 0);

    /////////////////////////////////////////////////////////////
    //////////  CÁLCULO DE DEPENDENTES ///////////////////////////
    ////////////////////////////////////////////////////////////
    $data_menor21 = mktime(0, 0, 0, $mes, $dia, $ano - 21);
    $menor21 = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$row[id_clt]' AND id_regiao = '$regiao'") or die(mysql_error());

    $row_menor21 = mysql_fetch_assoc($menor21);

    if (mysql_num_rows($menor21) != 0) {
        if ($row_menor21['data1'] != '0000-00-00') { $filhos++; }
        if ($row_menor21['data2'] != '0000-00-00') { $filhos++; }
        if ($row_menor21['data3'] != '0000-00-00') { $filhos++; }
        if ($row_menor21['data4'] != '0000-00-00') { $filhos++; }
        if ($row_menor21['data5'] != '0000-00-00') { $filhos++; }
        if ($row_menor21['data6'] != '0000-00-00') { $filhos++; }

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

        if ($data1 > $data_menor21 and $row_menor21['data1'] != '0000-00-00') { $total_filhos_menor_21++; }
        if ($data2 > $data_menor21 and $row_menor21['data2'] != '0000-00-00') { $total_filhos_menor_21++; }
        if ($data3 > $data_menor21 and $row_menor21['data3'] != '0000-00-00') { $total_filhos_menor_21++; }
        if ($data4 > $data_menor21 and $row_menor21['data4'] != '0000-00-00') { $total_filhos_menor_21++; }
        if ($data5 > $data_menor21 and $row_menor21['data5'] != '0000-00-00') { $total_filhos_menor_21++; }
        if ($data6 > $data_menor21 and $row_menor21['data6'] != '0000-00-00') { $total_filhos_menor_21++; }
    }
    if (empty($filhos)) { $filhos = 0; }
    if (empty($total_filhos_menor_21)) { $total_filhos_menor_21 = 0; }

    //////////////////////////////////////////////////////////////////////
    /////////////////////////INICIO DO PDF ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTopMargin(1);

    ////LINHA 1
    $pdf->Cell($largura_celula, 2.1, null, 1, '0', 'C');
    $pdf->Text(3.5, 1.6, $row['rEmpresa']);
    $pdf->Text(3.5, 2, "CNPJ: " . $row['cnpj']);
    $pdf->Text(3.5, 2.4, $row['endereco']);
    $pdf->Text(3.5, 2.8, "CEP: " . $row['cep']);
    $pdf->Text(5.8, 2.8, "Tel: " . $row['tel'] . " / " . $row['fax']);
    $pdf->Image('../../imagens/logomaster' . $row['id_master'] . '.gif', 1.1, 1.1, 2.3, 1.8, 'gif');

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, 2.1, null, 1, '0', 'C');
    $pdf->Text(3.5 + $largura_celula + $distancia, 1.6, $row['rEmpresa']);
    $pdf->Text(3.5 + $largura_celula + $distancia, 2, "CNPJ: " . $row['cnpj']);
    $pdf->Text(3.5 + $largura_celula + $distancia, 2.4, $row['endereco']);
    $pdf->Text(3.5 + $largura_celula + $distancia, 2.8, "CEP: " . $row['cep']);
    $pdf->Text(5.8 + $largura_celula + $distancia, 2.8, "Tel: " . $row['tel'] . " / " . $row['fax']);
    $pdf->Image('../../imagens/logomaster' . $row['id_master'] . '.gif', 1.1 + $largura_celula + $distancia, 1.1, 2.3, 1.8, 'gif');

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
    $pdf->Text(3.1, 1.5 + $altura_atual, $row['nome']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Cod Funcionário:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row['matricula']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');

    $pdf->Line(2.9 + $largura_celula + $distancia, $altura_atual + 0.8, 2.9 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(11.7 + $largura_celula + $distancia, $altura_atual + 0.8, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Mês:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $mes_ar[$num_mes] . '/' . $ano);
    $pdf->Text(3.1 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Nome:');
    $pdf->Text(3.1 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['nome']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Cod Funcionário:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['matricula']);

    $altura_atual += 0.8;

    //LINHA 3
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7, $altura_atual + 0.8, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Cargo:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row['nome_funcao']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Data de Admissão:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row['databr']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7 + $largura_celula + $distancia, $altura_atual + 0.8, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Cargo:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['nome_funcao']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Data de Admissão:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['databr']);

    $altura_atual += 0.8;

    //LINHA 4
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7, 0.8 + $altura_atual, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Unidade:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row['clt_unidade']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'PIS:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row['pis']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(11.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Unidade:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['clt_unidade']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'PIS:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['pis']);

    $altura_atual += 0.8;

    //LINHA 5
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7, 0.8 + $altura_atual, 3.7, $altura_atual + 1.6);
    $pdf->Line(7.3, 0.8 + $altura_atual, 7.3, $altura_atual + 1.6);
    $pdf->Line(11.7, 0.8 + $altura_atual, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'CPF:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row['cpf']);
    $pdf->Text(4, 1.1 + $altura_atual, 'RG:');
    $pdf->Text(4, 1.5 + $altura_atual, $row['rg']);
    $pdf->Text(7.5, 1.1 + $altura_atual, 'Carteira de Trabalho:');
    $pdf->Text(7.5, 1.5 + $altura_atual, $row['campo1']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Série:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row['serie_ctps']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 3.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(7.3 + $largura_celula + $distancia, 0.8 + $altura_atual, 7.3 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(11.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'CPF:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['cpf']);
    $pdf->Text(4 + $largura_celula + $distancia, 1.1 + $altura_atual, 'RG:');
    $pdf->Text(4 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['rg']);
    $pdf->Text(7.5 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Carteira de Trabalho:');
    $pdf->Text(7.5 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['campo1']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Série:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['serie_ctps']);

    $altura_atual += 0.8;

    //LINHA 6
    $pdf->Ln($altura_celula + 0.3);

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7, 0.8 + $altura_atual, 3.7, $altura_atual + 1.6);
    $pdf->Line(11.7, 0.8 + $altura_atual, 11.7, $altura_atual + 1.6);
    $pdf->Text(1.2, 1.1 + $altura_atual, 'Banco:');
    $pdf->Text(1.2, 1.5 + $altura_atual, $row['rBanco']);
    $pdf->Text(4, 1.1 + $altura_atual, 'Agência:');
    $pdf->Text(4, 1.5 + $altura_atual, $row['cltAgencia']);
    $pdf->Text(11.8, 1.1 + $altura_atual, 'Conta corrente:');
    $pdf->Text(11.8, 1.5 + $altura_atual, $row['cltConta']);

    $pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');

    $pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
    $pdf->Line(3.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 3.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Line(11.7 + $largura_celula + $distancia, 0.8 + $altura_atual, 11.7 + $largura_celula + $distancia, $altura_atual + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Banco:');
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['rBanco']);
    $pdf->Text(4 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Agência:');
    $pdf->Text(4 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['cltAgencia']);
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.1 + $altura_atual, 'Conta corrente:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.5 + $altura_atual, $row['cltConta']);

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
    $row_falta = FALSE;

    if (!empty($row['ids_movimentos_estatisticas'])) {
        //verifica_falta
        $qr_movimento_clt = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  id_movimento IN($row[ids_movimentos_estatisticas]) AND id_clt = '$row[id_clt]' AND id_mov  IN(62)") or die(mysql_error());
        $row_falta = mysql_fetch_assoc($qr_movimento_clt);
    }

    $salb = $row['sallimpo_real'] + $row_falta['valor_movimento'];
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

    if ($row['terceiro'] != 1 and $salb != '0.00') {

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

    $qr_movimentos = mysql_query("SELECT DISTINCT (cod), descicao, categoria FROM rh_movimentos WHERE mov_lancavel !=1  AND id_mov NOT IN(77, 254,257, 228) AND cod NOT IN(0001,9991,50241,9996,5024,50250,5044,10010,10011)  ORDER BY cod ASC") or die(mysql_error());
    while ($row_mov = mysql_fetch_assoc($qr_movimentos)):

        $nome_campo = 'a' . $row_mov['cod'];
        $categoria = $row_mov['categoria'];
        $valor_movimento = $row[$nome_campo];
        $referencia = "";
       
        if ($valor_movimento != '0.00' && $valor_movimento != 0 && !in_array($row_mov['cod'], $array_outros_movimentos)) {
            switch ($row_mov['cod']) {
                case '5029': $total_decimo = $valor_movimento;
                    $total_vencimentos += $total_decimo;
                    break;
                default :
                    if ($row_mov['cod'] == '5031') { $fgts_13 = $valor_movimento; }
                    if ($row_mov['cod'] == '5020') { $referencia = ($row["t_inss"] * 100) . "%"; }
                    if ($row_mov['cod'] == '5021') { $referencia = ($row["t_imprenda"] * 100) . "%"; }
                    if ($row_mov['cod'] == '5030') { $irrf_13 = $valor_movimento; }
                    if ($row_mov['cod'] == '6006') { $referencia = "20%"; $nao_exibe_insalubridade = 1; }
                    if ($row_mov['cod'] == '7001' and $valor_movimento == $total_6_porcento) { $referencia = "6%"; }
                    
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
    unset($vencimentos, $desconto, $referencia, $percent);
    
    if (!empty($row['ids_movimentos'])) {

        $qr_movimento_clt = mysql_query("SELECT A.*,B.percentual FROM rh_movimentos_clt AS A LEFT JOIN rh_movimentos AS B ON (A.id_mov=B.id_mov) WHERE A.id_movimento IN($row[ids_movimentos_estatisticas]) AND A.id_clt = '$row[id_clt]' ") or die(mysql_error());
                
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
                    $percent = substr($row_mov2['qnt_horas'], 0,5);
                } else if ($row_mov2['tipo_qnt'] == 2) {
                    $tipo_quantidade = "dias";
                    $percent = $row_mov2['qnt'];
                } else {
                    $tipo_quantidade = "";
                }

                if ($row_mov2['cod_movimento'] == "9997") {
                    $qr = "SELECT COUNT(data) as total FROM ano WHERE YEAR(data) = {$ano} AND MONTH(data) = {$mes} AND fds = 1 AND nome = 'Domingo'";
                    $percent = mysql_result(mysql_query($qr), 0);
                }

                if ($row['terceiro'] != 1) {
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
    if ($row['terceiro'] == 1) {
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
    $pdf->Text(12.2, 1.5 + $altura_ag, "R$ " . number_format($row['salliquido'], 2, ",", "."));

    $pdf->Rect(16, 14.7, 13, 0.8);
    $pdf->Line(8.5 + $largura_celula + $distancia, 0.8 + $altura_ag, 8.5 + $largura_celula + $distancia, $altura_ag + 1.6);
    $pdf->Line(12 + $largura_celula + $distancia, 0.8 + $altura_ag, 12 + $largura_celula + $distancia, $altura_ag + 1.6);
    $pdf->Text(1.2 + $largura_celula + $distancia, 1.1 + $altura_ag, 'Valor Bruto:');
    $pdf->Text(3.4 + $largura_celula + $distancia, 1.5 + $altura_ag, "R$ " . number_format($total_vencimentos, 2, ',', '.'));
    $pdf->Text(8.7 + $largura_celula + $distancia, 1.1 + $altura_ag, 'Total dos Descontos:');
    $pdf->Text(9.5 + $largura_celula + $distancia, 1.5 + $altura_ag, "R$ " . number_format($total_desconto, 2, ',', '.'));
    $pdf->Text(12.1 + $largura_celula + $distancia, 1.1 + $altura_ag, 'Valor Líquido:');
    $pdf->Text(12.2 + $largura_celula + $distancia, 1.5 + $altura_ag, "R$ " . number_format($row['salliquido'], 2, ",", "."));

    /* LINHA DETALHE DE CALULO */
    if ($row['terceiro'] == 1) {
        $salario_base = $total_decimo;
        $fgts = $fgts_13;
        $ir = (!empty($irrf_13)) ? $salario_base - $fgts_13 : '0';
    } else {
        $salario_base = $row['sallimpo'];
        $fgts = $row['fgts'];
        $ir = $row['base_irrf'];
    }

    $pdf->Ln($altura_celula + 0.7);
    $altura_ag += 1.2;

    $pdf->Rect(1, 15.7, 13, 0.8);
    $pdf->Line(4, 0.6 + $altura_ag, 4, $altura_ag + 1.4);
    $pdf->Line(8.5, 0.6 + $altura_ag, 8.5, $altura_ag + 1.4);
    $pdf->Line(11.3, 0.6 + $altura_ag, 11.3, $altura_ag + 1.4);
    $pdf->Text(1.2, 0.9 + $altura_ag, 'Salário Base Contrib. INSS:');
    $pdf->Text(1.5, 1.3 + $altura_ag, "R$ " . number_format($row['salbase'], 2, ',', '.'));
    $pdf->Text(4.3, 0.9 + $altura_ag, 'Salário Base: ');
    $pdf->Text(4.6, 1.3 + $altura_ag, "R$ " . number_format($salario_base, 2, ',', '.'));
    $pdf->Text(8.7, 0.9 + $altura_ag, 'FGTS:');
    $pdf->Text(9.2, 1.3 + $altura_ag, "R$ " . number_format($fgts, 2, ',', '.'));
    $pdf->Text(11.5, 0.9 + $altura_ag, 'Base Calculo IRRF:');
    $pdf->Text(11.8, 1.3 + $altura_ag, "R$ " . number_format($ir, 2, ",", "."));

    $pdf->Rect(16, 15.7, 13, 0.8);
    $pdf->Line(8.5 + $largura_celula + $distancia, 0.6 + $altura_ag, 8.5 + $largura_celula + $distancia, $altura_ag + 1.4);
    $pdf->Line(11.3 + $largura_celula + $distancia, 0.6 + $altura_ag, 11.3 + $largura_celula + $distancia, $altura_ag + 1.4);
    $pdf->Text(1.2 + $largura_celula + $distancia, 0.9 + $altura_ag, 'Salário Base Contrib. INSS:');
    $pdf->Text(1.5 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($row['salbase'], 2, ',', '.'));
    $pdf->Text(4.3 + $largura_celula + $distancia, 0.9 + $altura_ag, 'Salário Base: ');
    $pdf->Text(4.6 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($salario_base, 2, ',', '.'));
    $pdf->Text(8.7 + $largura_celula + $distancia, 0.9 + $altura_ag, 'FGTS:');
    $pdf->Text(9.2 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($fgts, 2, ',', '.'));
    $pdf->Text(11.5 + $largura_celula + $distancia, 0.9 + $altura_ag, 'Base Calculo IRRF:');
    $pdf->Text(11.8 + $largura_celula + $distancia, 1.3 + $altura_ag, "R$ " . number_format($ir, 2, ",", "."));

    $pdf->Ln($altura_celula + 0.7);

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

    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////// saudacao /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    
    $saudacao = '';
    
    /////////////////////////// DIA DAS MÃES ///////////////////////////////////
    if ($num_mes + 1 == 5 && $row['sexo'] == 'F' && !empty($row['num_filhos'])) { $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ DIA DAS MÃES!"; }
    /////////////////////////// DIA DOS PAIS ///////////////////////////////////
    if ($num_mes + 1 == 8 && $row['sexo'] == 'M' && !empty($row['num_filhos'])) { $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ DIA DAS PAIS!"; }
    ////////////////////////////////// NATAL ///////////////////////////////////
    if ($num_mes + 1 == 12 && $row['terceiro'] != 1) { $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ NATAL E UM PRÓSPERO ANO NOVO!"; }
    //////////////////////////// AIVERSÁRIO ////////////////////////////////////
    if (sprintf('%02s', $num_mes + 1) == $row['mes_nasci'] || ($num_mes == 12 && $row['mes_nasci'] == 1)) {
        /////////////////////////// DIA DAS MÃES ///////////////////////////////
        if ($num_mes + 1 == 5 && $row['sexo'] == 'F' && !empty($row['num_filhos'])) {
            $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ ANIVERSÁRIO E FELIZ DIA DAS MÃES!";
        } elseif
        /////////////////////////// DIA DOS PAIS ///////////////////////////////
        ($num_mes + 1 == 8 && $row['sexo'] == 'M' && !empty($row['num_filhos'])) {
            $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ ANIVERSÁRIO E FELIZ DIA DAS PAIS!";
        } elseif
        ////////////////////////////////// NATAL ///////////////////////////////
        ($num_mes + 1 == 12 && $row['terceiro'] != 1) {
            $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ ANIVERSÁRIO, UM FELIZ NATAL E UM PRÓSPERO ANO NOVO!";
        } elseif ($row['terceiro'] != 1) {
            /////////////////////////////// apenas ainversário /////////////////////
            $saudacao = "A EMPRESA $row[nomeMaster] DESEJA-LHE UM FELIZ ANIVERSÁRIO!";
        }
    }

    $pdf->SetFontSize('6');
    $pdf->Rect(1, 19, 13, 0.8);
    $pdf->Rect(16, 19, 13, 0.8);
    $pdf->Text(1.3, 19.5, $saudacao);
    $pdf->Text(16.3, 19.5, $saudacao);

    unset($filhos, $total_filhos_menor_21, $total_decimo, $ir, $total_desconto, $total_vencimentos);
}

$pdf->Output('as.pdf', 'I');
?>