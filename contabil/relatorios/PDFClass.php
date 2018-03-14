<?php

class PDF extends FPDF {

    public $master, $projeto, $regiao, $h_row, $cnpj, $data_geracao = TRUE, $data_ini, $data_fim, $titulo, $border;
    var $widths;
    var $aligns;

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



        $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 2, 1, 2.3);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(2.5, .5);
        $this->Cell(8.5, .5, $nome_regiao . $this->master['nome'], 0, 0, 'L');
        $this->Cell(6.5, .5, $this->titulo, 0, 0, 'R');
        $this->Ln();

        $this->Cell(2.5, .5);
        $this->SetFont('Arial', '', 8);
        $this->Cell(8.5, .5, 'CNPJ: ' . $this->cnpj, 0, 0, 'L');
        $this->Cell(6.5, .5, 'Período: ' . $this->data_ini . " à " . $this->data_fim, 0, 0, 'R');
        $this->Ln();

        $this->Cell(2.5, .5);
        $this->Cell(8.5, .5, 'C. Custo: ' . $nome_projeto, 0, 0, 'L');
        $this->Cell(6.5, .5, 'Gerado em: ' . date('d/m/Y H:i:s'), 0, 0, 'R');
        $this->Ln();

        $this->Cell(11, .5);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(6.5, .5, 'Página ' . $this->PageNo(), 0, 0, 'R');
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
        $this->Cell(7.25, .8, 'PAY ALL FAST 3.0 - F71 SISTEMAS WEB', 'T', 0, 'L');
        $this->Cell(7.25, .8, 'Módulo Contabilidade - versão 1.0', 'T', 0, 'L');
        $this->Cell(3, .8, 'Página ' . $this->PageNo(), 'T', 0, 'R');
    }

    function thead() {
//            $this->SetFont('Arial', '', 8);
//            $this->Cell(1.7, $this->h_row, 'DATA', '', 0, 'L');
//            $this->Ln();
//            $this->Cell(10, $this->h_row, 'HISTÓRICO', 'B', 0, 'J');
//            $this->Cell(2.5, $this->h_row, 'DÉBITO', 'B', 0, 'L');
//            $this->Cell(2.5, $this->h_row, 'CRÉDITO', 'B', 0, 'L');
//            $this->Cell(2.5, $this->h_row, '', 'B', 0, 'R');
//            $this->Ln(.75);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetBorders($b) {
        //Set the array of column widths
        $this->border = $b;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = .15 * $nb;

        //Issue a page break first if needed
        $this->CheckPageBreak($h);

        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $b = isset($this->border[$i]) ? $this->border[$i] : 0;
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
//                $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, $h, $data[$i], $b, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h * ($nb + 1));
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + .8 + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

}
