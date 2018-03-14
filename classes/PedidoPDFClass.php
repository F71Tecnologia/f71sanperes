<?php

/*** Description of InformeRendimentoClass ** @author Ramon ***/
class PedidoPDFClass {
    
    public $pedido;
    public $Data_pedido;
    public $fornecedor;
    public $solicitante;
    public $pedido_obs;
    public $pedido_item;
    public $pedido_tot;
    public $fpdf;
    public $sqlFolha;
    public $fileName;
    public $folhasEnvolvidas=array();

    public function setPedido($pedido) {
        $this->pedido = $pedido;
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }
    
    public function setFornecedor($fornecedor){
        $this->fornecedor = $fornecedor;
        $qry = mysql_query("SELECT * FROM prestadorservico WEHRE id_prestador = '{$fornecedor}'");
    }
    
    public function iniciaFpdf() {
        define('FPDF_FONTPATH', '../rh/fpdf/font/');
        $this->fpdf = new FPDF("P", "cm", "A4");
        $this->fpdf->SetAutoPageBreak(true, 0.0);
        $this->fpdf->Open();
    }
    
    public function geraPdf() {
        
        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->Cell(10, 30, " ");
        $this->fpdf->Image('imagens/fundo_rendimento_2015_p1.gif', 0.5, 0.3, 20, 29, 'gif');

        $this->fpdf->SetXY(6.85, 2.1);
        $this->fpdf->Cell(0, 0, $this->Data_pedido, 0, 0, 'L');
        
        $this->fpdf->SetXY(1.6, 3.70);
        $this->fpdf->Cell(0, 0, ($this->pedido) ? $this->empresa['razao'] : $this->empresacoop['nome'], 0, 0, 'L');
        
        if($this->tipo==3){ $this->fpdf->SetFont('Arial', 'B', 10); }
        $this->fpdf->SetXY(14.8, 3.70);
        $this->fpdf->Cell(0, 0, ($this->tipo!=3) ? $this->empresa['cnpj'] : $this->empresacoop['cnpj'], 0, 0, 'L');

        $this->fpdf->SetXY(6.05, 5.20);
        $this->fpdf->Cell(0, 0, $this->participante['nome'], 0, 0, 'L');

        $this->fpdf->SetXY(1.6, 5.20);
        $cpf = preg_replace('/[^[:digit:]]/', '', $this->participante['cpf']);
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})/i', '$1.$2.$3-$4', $cpf);
        $this->fpdf->Cell(0, 0, $cpf, 0, 0, 'L');

        $this->fpdf->SetXY(1.6, 6.10);
        switch ($this->participante['tipo_contratacao']) {
            case 2:
                $this->fpdf->Cell(0, 0, "Rendimentos do trabalho assalariado", 0, 0, 'L');
                break;
            case 1:
            case 3:
                $this->fpdf->Cell(0, 0, "Rendimentos do trabalho sem vínculo empregatício", 0, 0, 'L');
                break;
        }

        $this->fpdf->SetXY(14.8, 7.50); //9.06 : 1,56
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->salario, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 8.44);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->inss, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 9.36);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 10.29);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->pensao_alimenticia, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 11.24);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->ir, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 12.85); //14.75 : 1,9
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 13.77);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->ajuda_custo, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 14.72);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 15.62);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 16.55);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 17.5);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->rescisao_ferias, 2, ",", ".") . "", 0, 0, 'L');
        
        //ABONO PECUNIARIO EM OUTROS RENDIMENTOS
        if($this->outros_rendimentos > 0){
            if($this->abono_pecuniario > 0){
                $this->fpdf->SetFont('Arial', 'B', 8);
                $this->fpdf->SetXY(5, 18.2);
                $this->fpdf->Cell(0, 0, "ABONO PECUNIARIO", 0, 0, 'L');
                $this->fpdf->SetFont('Arial', 'B', 10);
            }
        }
        
        $this->fpdf->SetXY(14.8, 18.5);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->outros_rendimentos, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 20); //22.1 : 2.1
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->salario13, 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 20.9);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->irdt, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 21.8);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        
        $this->fpdf->SetXY(13.2, 23.6);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 25.6);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 26.18);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 26.75);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 27.33);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 27.89);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 28.49);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        // NovaPagina
        $this->fpdf->SetAutoPageBreak(true, 0.0);
        
        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->Cell(10, 30, " ");
        $this->fpdf->Image('imagens/fundo_rendimento_2015_p2.gif', 0.5, 0.3, 20, 29, 'gif');
        
    }
    
    public function finalizaPdf(){
        $this->fpdf->Output($this->fileName);
        $this->fpdf->Close();
    }

    public function limpaVariaveis() {
        unset($this->pedido);
    }

    public function downloadFile() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload");
        header("Content-Length: " . filesize($this->fileName));
        header("Content-Disposition: attachment; filename={$this->fileName}");
        flush();

        readfile($this->fileName);
    }

    public function normalizaIdsEstatisticas($ids) {
        $ids_mo = "";
        $ids_mo = str_replace(',,,', ',', $ids);
        $ids_mo = str_replace(',,', ',', $ids_mo);

        if (substr($ids_mo, -1) == ",") {
            $ids_mo = substr($ids_mo, 0, -1);
        }
        if (substr($ids_mo, 0, 1) == ",") {
            $ids_mo = substr($ids_mo, 1);
        }
        return $ids_mo;
    }
    
}
