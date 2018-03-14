<?php

$objClassificador = new c_classificacaoClass();
$arrayProjeto = $objClassificador->carregarProjeto($_REQUEST['projeto']);

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$result = mysql_query("SELECT * FROM regioes WHERE id_regiao = $id_regiao") or die('error regiao');
$regiao = mysql_fetch_assoc($result);

$xxx = mysql_query("SELECT * FROM contabil_centrodecusto WHERE classificador = '2.001';") or die('erro centro de custo: ' . mysql_error());
$centro_custo = mysql_fetch_assoc($xxx);

class realtorio_tributos extends PDF {

    function Header() {
        // se não tiver projeto       
        if (empty($this->projeto)) {
            $endereco = $this->projeto['endereco'];
            $nome_projeto = 'Consolidado';
            $nome_regiao = '';
        } else {
            $endereco = $this->projeto['endereco'];
            $nome_regiao = trim($this->regiao['regiao']) . ' - ';
            $nome_projeto = trim($this->projeto['projeto_nome']);
        }

        $nome_regiao = trim($this->regiao['regiao']) . ' - ';

        switch ($this->tipo_data) {
            case 1:
                $tipo_data = 'Por Data de Emissão';
                break;
            case 2:
                $tipo_data = 'Por Data de Pagamento';
                break;
        }

        $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 1, 1, 2.3);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(2.7, .5);
        $this->Cell(12.5, .5, $nome_regiao . $this->master['nome'], 0, 0, 'L');
        $this->Cell(12.5, .5, $this->titulo, 0, 0, 'R');
        $this->Ln();

        $this->Cell(2.7, .5);
        $this->SetFont('Arial', '', 8);
        $this->Cell(12.5, .5, 'CNPJ: ' . $this->cnpj, 0, 0, 'L');
        $this->Cell(12.5, .5, 'Gerado em: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
        $this->Ln();

        $this->Cell(2.7, .5);
        $this->Cell(12.5, .5, 'Projeto: ' . $nome_projeto, 0, 0, 'L');

        $this->Ln();

        $this->Cell(2.7, .5);
        $this->Cell(12.5, .5, "Período {$tipo_data}: " . $this->data_ini . " à " . $this->data_fim, 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(12.5, .5, 'Página ' . $this->PageNo(), 0, 0, 'R');
        $this->Ln(.75);

        $this->thead();
    }

    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-1);
        // Arial italic 8
        $this->SetFont('Arial', NULL, 6.5);
        $this->SetLineWidth(0);
        // Page number
//            $this->Cell(16, .8, 'F71 SISTEMAS WEB - módulo contábil', 'T', 0, 'L');
        $this->Cell(12.35, .8, 'PAY ALL FAST 3.0 - F71 SISTEMAS WEB', 'T', 0, 'L');
        $this->Cell(12.35, .8, 'Módulo Contabilidade - versão 1.0', 'T', 0, 'L');
        $this->Cell(3, .8, 'Página ' . $this->PageNo(), 'T', 0, 'R');
    }

    function thead() {
        $this->SetFont('Arial', '', 7);
        $this->setFillColor(51, 122, 183); // #337ab7
        $this->SetTextColor(255, 255, 255); // #337ab7
        $r = ['Itens', 'Nº', 'Nº', 'Data', 'Nota', 'Cod Serv', 'Valor', 'Iss Retido', 'Iss Retido', 'IRF-1708', 'PIS-Ret', 'COFINS-Ret', 'CSLL-Ret', 'Código', 'INSS-2100', 'INSS-2631', 'Soma', 'Valor'];
        $w = [.7, 1.35, 1.35, 5.4, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35];
        $a = ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'];
        $b = ['LTR', 'LTR', 'LTR', 1, 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR', 'LTR'];
        foreach ($r as $key => $txt) {
            $this->cell($w[$key], .5, $txt, $b[$key], null, $a[$key], 1);
        }
        $this->ln();

        $r = ['', 'Registro', 'Contrato', 'Emissão', 'Lacto Cont', 'Vencto', 'Pagto', 'Fiscal', 'Lei 116/03', 'Bruto', 'Emissão', 'Baixa', '', '', '', '', '5952', '', '', 'Retenções', 'Líquido'];
        $w = [.7, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35];
        $a = ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'];
        $b = ['LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR'];
        foreach ($r as $key => $txt) {
            $this->cell($w[$key], .5, $txt, $b[$key], null, $a[$key], 1);
        }
        $this->ln();
    }

}

$date = new DateTime();

$pdf = new realtorio_tributos('L', 'cm', 'A4');

/*
 * set variaveis
 */
$h_row = .2;
$pdf->data_ini = $_REQUEST['data_ini'];
$pdf->data_fim = $_REQUEST['data_fim'];
$pdf->master = $master;
$pdf->projeto = $arrayProjeto;
$pdf->regiao = $regiao;
$pdf->h_row = $h_row;
$pdf->titulo = 'Planilha de Apuração de Tributos Retidos';
$pdf->cnpj = $centro_custo['cnpj'];

$pdf->tipo_data = $_REQUEST['tipo_data'];


$pdf->setMargins(1, 1, 1);
$pdf->AddPage();
$pdf->SetAutoPageBreak(1, 1.5);
$pdf->SetFont('Arial', '', 7);

$pdf->SetLineWidth(0);

//----linhas--------------------------------------------------------------------
$i = 1;
foreach ($prestador as $k => $v) {
    if (count($v['nf']) > 0) {
        $pdf->SetFont('Arial', 'B', 7);
        $col1 = $v['dados']['c_razao'] . ' - ' . $v['dados']['c_cnpj'] . ' - ' . $v['dados']['c_cidade'] . ' - ' . $v['dados']['c_uf'];
        $col2 = ($v['dados']['c_uf'] != $mun) ? number_format($v['dados']['ret_iss'], 2, ',', '.') : '-';
        $col3 = ($v['dados']['c_uf'] == $mun) ? number_format($v['dados']['ret_iss'], 2, ',', '.') : '-';
        $col4 = number_format($v['dados']['ret_ir'], 2, ',', '.');
        $col5 = number_format($v['dados']['ret_pis'], 2, ',', '.');
        $col6 = number_format($v['dados']['ret_cofins'], 2, ',', '.');
        $col7 = number_format($v['dados']['ret_csll'], 2, ',', '.');
        $col8 = number_format($v['dados']['ret_pis_cofins_csll'], 2, ',', '.');
        $col9 = '-';
        $col10 = number_format($v['dados']['ret_inss'], 2, ',', '.');
        $r = [$col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col10, '', ''];
        $w = [12.85, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35];
        $a = ['L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'];
        $pdf->setFillColor(217, 239, 247); // #d9edf7

        foreach ($r as $key => $txt) {
            $pdf->cell($w[$key], .5, $txt, 1, null, $a[$key], 1);
        }
        $pdf->ln();

        $mun = 'GO';

        foreach ($v['nf'] as $k_nf => $nfse) {
            $pdf->SetFont('Arial', 'B', 8);
            $soma_retencoes = $nfse['ValorIss'] + $nfse['ValorIr'] + $nfse['ValorPis'] + $nfse['ValorCofins'] + $nfse['ValorCsll'] + $nfse['ValorPisCofinsCsll'] + $nfse['ValorInss'];


            $col1 = $i;
            $col2 = $nfse['id_nfse'];
            $col3 = $v['dados']['numero'];
            $col4 = (!empty($nfse['DataEmissao'])) ? converteData($nfse['DataEmissao'], 'd/m/Y') : '-';
            $col5 = (!empty($nfse['data_lancamento'])) ? converteData($nfse['data_lancamento'], 'd/m/Y') : '-';
            $col6 = (!empty($nfse['data_vencimento'])) ? converteData($nfse['data_vencimento'], 'd/m/Y') : '-';
            $col7 = (!empty($nfse['data_pg'])) ? converteData($nfse['data_pg'], 'd/m/Y') : '-';
            $col8 = $nfse['Numero'];
            $col9 = $nfse['CodigoTributacaoMunicipio']; //. ' - ' . $nfse['descricao']        ;
            $col10 = number_format($nfse['ValorServicos'], 2, ',', '.');
            $col11 = ($v['dados']['c_uf'] != $mun) ? number_format($nfse['ValorIss'], 2, ',', '.') : '-';
            $col12 = ($v['dados']['c_uf'] == $mun) ? number_format($nfse['ValorIss'], 2, ',', '.') : '-';
            $col13 = number_format($nfse['ValorIr'], 2, ',', '.');
            $col14 = number_format($nfse['ValorPis'], 2, ',', '.');
            $col15 = number_format($nfse['ValorCofins'], 2, ',', '.');
            $col16 = number_format($nfse['ValorCsll'], 2, ',', '.');
            $col17 = number_format($nfse['ValorPisCofinsCsll'], 2, ',', '.');
            $col18 = number_format(0, 2, ',', '.');
            $col19 = number_format($nfse['ValorInss'], 2, ',', '.');
            $col20 = number_format($soma_retencoes, 2, ',', '.');
            $col21 = number_format($nfse['ValorLiquidoNfse'], 2, ',', '.');

            $r = [$col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col10, $col11, $col12, $col13, $col14, $col15, $col16, $col17, $col18, $col19, $col20, $col21];
            $w = [.7, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35, 1.35];
            $a = ['C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'];

            foreach ($r as $key => $txt) {
                if ($key == 7) {
                    $pdf->SetFont('Arial', '', 5.75);
                } else {
                    $pdf->SetFont('Arial', '', 7);
                }
                $pdf->cell($w[$key], .5, $txt, 1, null, $a[$key]);
            }
            $pdf->ln();

            $col1 = 'Histórico:';
            $col2 = $nfse['historico'];

            $r = [$col1, $col2];
            $w = [1.7, 26];
            $a = ['L', 'J'];
            foreach ($r as $key2 => $txt) {
                $pdf->cell($w[$key2], .5, $txt, 1, null, $a[$key2]);
            }
            $pdf->ln();
            $i++;
        }
    }
}
//----linhas--------------------------------------------------------------------






$pdf->Output('relatorio_tributos_' . $date->getTimestamp() . '.pdf', 'D');
exit();

