<?php
 
require_once("../../conn.php");
require_once("../../wfunction.php");
require_once("../../classes/BotoesClass.php");
require_once("../../classes/BancoClass.php");
require_once("../../classes/global.php");
require_once("../../classes/c_classificacaoClass.php");
require_once("../../classes/c_planodecontasClass.php");
require_once("../../classes/ContabilLancamentoClass.php");
require_once("../../classes/ContabilLoteClass.php");
require_once("../../classes/pdf/fpdf.php");
include_once("../../classes/ContabilLancamentoItemClass.php");

$usuario = carregaUsuario(); 
 
$projeto = 3313; //$_REQUEST['projeto'];
$mes = 12;//$_REQUEST['mes'];
$mes_ini = 'Outubro'; // 
$mes_fim = 'Dezembro'; //
$ano = 2015; //$_REQUEST['ano'];

$sqlEmpresa = "SELECT * FROM rhempresa WHERE id_projeto = {$projeto} LIMIT 1";
$rowEmpresa = mysql_fetch_assoc(mysql_query($sqlEmpresa));

$objClassificador = new c_classificacaoClass();
$objPlanoContas = new c_planodecontasClass();
$objLancamentoItens = new ContabilLancamentoItemClass();

class PDF extends FPDF {
    // Page header
    function Header() {
        require_once("../../conn.php");
        require_once("../../wfunction.php");
        
        $usuario = carregaUsuario(); 
        
        $sqlMaster = "SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1;";
        $qryMaster = mysql_query($sqlMaster);
        $rowMaster = mysql_fetch_assoc($qryMaster);

        // Logo
        //$this->Image('logo.png',10,6,30);
        // Arial bold 15
        $this->SetFont('Arial','',9);
//        $this->Cell(9.5,.4,"CONTABILIDADE",0,0,'L');
        $this->Cell(9.5,.4,"Raz�o Social: {$rowMaster['razao']}",0,0,'L');
        $this->Cell(9.5,.4,"Emiss�o: ".date('d/m/Y'),0,0,'R');
        // Line break
        $this->Ln();
        $this->Cell(9.5,.4,"CNPJ: {$rowMaster['cnpj']}",0,0,'L');
        $this->Cell(9.5,.4,"P�gina: ".$this->PageNo(),0,0,'R');
        // Line break
        $this->Ln();
        $this->Cell(19,.1,null,"T",0,'L');
        // Line break
        $this->Ln();
        $this->Cell(19,.5,null,"T",0,'L');
        // Line break
        $this->Ln();
        // Title
        //$this->Cell(30,10,'Title',1,0,'C');
        
    }

    // Page footer
    function Footer()
    {
//        // Position at 1.5 cm from bottom
//        $this->SetY(-15);
//        // Arial italic 8
//        $this->SetFont('Arial','I',8);
//        // Page number
//        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
    
//    function mov_numerica (){
//        // calcula o numero de publica��o pegando como base a data de abertura da empresa
//        $this->mn = 2016 - 2012;
//        
//    }
}


//Primeiro Lan�amento do Ano
$PLancamento = mysql_fetch_assoc(mysql_query("SELECT *, DATE_FORMAT(data_lancamento, '%d/%m/%Y') data_lancamento_br FROM contabil_lancamento WHERE YEAR(data_lancamento) = {$ano} ORDER BY data_lancamento ASC, id_lancamento ASC LIMIT 1;"));
$dataPLancamento = explode('-',$PLancamento['data_lancamento']);
$diaPLancamento = $dataPLancamento[2];
$mesPLancamento = $dataPLancamento[1];


$pdf = new PDF('P','cm','A4');
$pdf->AliasNbPages();
$pdf->AliasSelPage();
$pdf->AliasSelPageText('000pld000');

############################################################################TERMO DE ABRETURA##############################################################

$pdf->AddPage();
$pdf->SetFont('Arial','B',20);
$pdf->Cell(19,1.3,"Termo de Abertura",0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',12);
$pdf->Cell(19,.8,"EXERC�CIO DE {$ano}",0,0,'C');
$pdf->Ln();
$pdf->Cell(19,.8,null,0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',10);
$texto = "Este Livro Cont�m {nb} Folhas Numeradas de 1 a {nb}, emitidas por processo eletr�nico de dados, de acordo com a lei n�mero 8.981/95, art. 45, par�grafo �nico e servir� de Livro Di�rio de movimenta��o num�rica 4 da empresa o numero NIRE: 00.0.0000000-0, em 31/05/2012. Pela Junta Comercial do Estado de S�o Paulo. Do per�odo de 01 de Janeiro de " .$ano. " a 31 de dezembro de $ano. Com folhas numeradas apenas no averso.";
$pdf->MultiCell(19, .5, $texto,0,'L');
$pdf->Ln();
$pdf->Cell(19,.8,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Empresa: ".$rowEmpresa['razao'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Titular: ".$rowEmpresa['responsavel'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Endere�o: ".$rowEmpresa['endereco']."   CEP: ".$rowEmpresa['cep'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"CNPJ/MF: ".$rowEmpresa['cnpj'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Inscri��o Estadual: ".$rowEmpresa['ie'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Inscri��o Municipal: ".$rowEmpresa['im'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,$rowEmpresa['cidade']. " 01 de Outubro de 2015",0,0,'R');
$pdf->Ln();
$pdf->Cell(19,5,null,0,0);
$pdf->Ln();
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "DONATO LAVIANO NETO",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['responsavel'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CONTADOR",0,0,'C');
$pdf->Cell(9.5, .5, "S�CIO ADMINISTRADOR",0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CRC/RJ 068540-0",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['cpf'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "815.497.507-63",0,0,'C');
$pdf->Cell(9.5, .5, null,0,0,'C');
$pdf->Ln();


############################################################################TERMO DE ABRETURA##############################################################
############################################################################LIVRO DIARIO###################################################################



$pdf->AddPage();
$arrayLancamentos = $objLancamentoItens->getLivroDiario($projeto, $ano.'-01-01', $ano.'-12-31', 3);
//print_array($arrayLancamentos);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(19,.3,"Livro Di�rio",0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
$pdf->Cell(19,.8,"Outubro /2015 � Dezembro /2015 ",0,0,'C');
//$pdf->Cell(19,.8,"M�s: " . mesesArray($mes) . "/{$ano}",0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',0.1);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(19,.01,"000pld000",0,0,'R');
$pdf->Ln();
$pdf->SetTextColor(0);

$pdf->SetFont('Arial','B',7);
$pdf->Cell(2.5,.8,"DATA",'B',0,'C');
$pdf->Cell(3.5,.8,"CONTRAPARTIDA",'B',0,'L');
$pdf->Cell(7,.8,"HIST�RICO",'B',0,'L');
$pdf->Cell(5,.8,"VALOR",'B',0,'R');
$pdf->Cell(1,.8,"C/D",'B',0,'C');
$pdf->Ln();

foreach ($arrayLancamentos as $key => $row_lanc) { 
    $pdf->SetFont('Arial','',6.6);
    $pdf->Cell(2.5,.4,$row_lanc['data_lancamento'],'B',0,'C');
    $pdf->Cell(3.5,.4,$row_lanc['classificador'],'B',0,'L');
    $pdf->Cell(7,.4, substr($row_lanc['descricao'],0,45),'B',0,'L');
    $pdf->Cell(5,.4,number_format($row_lanc['valor'], 2, ',', '.'),'B',0,'R');
    $pdf->Cell(1,.4,($row_lanc['tipo'] == 2) ? 'D' : 'C','B',0,'C');
    $pdf->Ln();
}

############################################################################LIVRO DIARIO###################################################################
############################################################################BALAN�O########################################################################

$pdf->AddPage();
$arrayBalanco = $objClassificador->balanco($projeto, $ano, true, 3);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(19,.3,"Balan�o",0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',8);
//$pdf->Cell(19,.8,"M�s: " . mesesArray($mes) . "/{$ano}",0,0,'C');
$pdf->Cell(19,.8,"Outubro /2015 � Dezembro /2015 ",0,0,'C');
$pdf->Ln();

foreach ($arrayBalanco as $key2 => $array2) {
    if(!empty($array2['descricao'])) {
        $pdf->SetFont('Arial','B',12);
        $pdf->SetLineWidth(.05);
        $pdf->Cell(12,.8,$array2['descricao'],'TB',0);
        $pdf->Cell(7,.8,null,'TB',0,'R');
        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->SetLineWidth(0);
    }
    foreach ($array2['array'] as $key3 => $array3) { //print_array($array3);
        if(!empty($array3['descricao'])) {
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(.5,.5,null,0,0);
            $pdf->Cell(11.5,.5,$array3['descricao'],0,0);
            $pdf->Cell(7,.5,null,0,0,'R');
            $pdf->Ln();
            $pdf->SetFont('Arial','',10);
        }
        foreach ($array3['array'] as $key4 => $array4) { //print_array($array4);
            if(!empty($array4['descricao'])) {
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(1,.5,null,0,0);
                $pdf->Cell(11,.5,$array4['descricao'],0,0);
                $pdf->Cell(7,.5,null,0,0,'R');
                $pdf->Ln();
                $pdf->SetFont('Arial','',10);
            }
            foreach ($array4['array'] as $key5 => $array5) { //print_array($array5);
                if(!empty($array5['descricao'])) {
                    $pdf->Cell(1.5,.5,null,0,0);
                    $pdf->Cell(10.5,.5,$array5['descricao'],0,0);
                    $pdf->Cell(7,.5,($array5['saldoAtual'] < 0) ? '(' . number_format($array5['saldoAtual']*-1,2,',','.') . ')' : number_format($array5['saldoAtual'],2,',','.'),0,0,'R');
                    $pdf->Ln();
                }
                foreach ($array5['array'] as $key6 => $array6) { //print_array($array6);
                    if(!empty($array6['descricao'])) {
                        $pdf->Cell(1.5,.5,null,0,0);
                        $pdf->Cell(10.5,.5,$array6['descricao'],0,0);
                        $pdf->Cell(7,.5,($array6['saldoAtual'] < 0) ? '(' . number_format($array6['saldoAtual']*-1,2,',','.') . ')' : number_format($array6['saldoAtual'],2,',','.'),0,0,'R');
                        $pdf->Ln();
                    }
                }
            }
            $pdf->SetLineWidth(.05);
            $pdf->Cell(12,.5,null,0,0);
            $pdf->Cell(7,.5,($array4['saldoAtual'] < 0) ? '(' . number_format($array4['saldoAtual']*-1,2,',','.') . ')' : number_format($array4['saldoAtual'],2,',','.'),'T',0,'R');
            $pdf->Ln();
            $pdf->SetLineWidth(0);
        }
    }
    $pdf->SetLineWidth(.05);
    $pdf->Cell(8,.5,null,0,0,'R');
    $pdf->Cell(4,.5,"TOTAL DO {$array2['descricao']}:","TBL",0,'R');
    $pdf->Cell(7,.5,($array2['saldoAtual'] < 0) ? '(' . number_format($array2['saldoAtual']*-1,2,',','.') . ')' : number_format($array2['saldoAtual'],2,',','.'),"TBR",0,'R');
    $pdf->Ln();
    $pdf->SetLineWidth(0);
    $pdf->Cell(19,.5,null,0,0);
    $pdf->Ln();
    $pdf->Ln();
    
}
$pdf->SetFont('Arial','B',8);
$pdf->Ln();
$pdf->Cell(19,5,null,0,0);
$pdf->Ln();
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "DONATO LAVIANO NETO",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['responsavel'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CONTADOR",0,0,'C');
$pdf->Cell(9.5, .5, "S�CIO ADMINISTRADOR",0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CRC/RJ 068540-0",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['cpf'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "815.497.507-63",0,0,'C');
$pdf->Cell(9.5, .5, null,0,0,'C');
$pdf->Ln();

############################################################################BALAN�O########################################################################
############################################################################D R E##########################################################################

$pdf->AddPage();
$arrayDRE = $objClassificador->dre($projeto, $ano, null, null, null, true, 3);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(19,.3,"Demonstrativo de Resultado do Exerc�cio",0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',9);
//$pdf->Cell(19,.8,"M�s: " . mesesArray($mes) . "/{$ano}",0,0,'C');
$pdf->Cell(19,.8,"Outubro /2015 � Dezembro /2015 ",0,0,'C');
$pdf->Ln();

    foreach($arrayDRE as $key => $arr_tipos) { 
        $total = 0;
        foreach ($arr_tipos as $conta_pai3 => $pai3) {   
            $subtotal = 0;
            foreach ($pai3['array'] as $conta_pai2 => $pai2) {
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetLineWidth(0);
                $pdf->Cell(10, .4, $pai2['descricao'],0,0,'L');
                $pdf->SetLineWidth(0);
                $pdf->Ln();
                foreach ($pai2['array'] as $conta_pai1 => $pai1) {
                    $subtotal1 = 0;
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(1, .4, null, 0);
                    $pdf->Cell(18, .4, $pai1['descricao'],0,0,'L');
                    $pdf->SetLineWidth(0);
                    $pdf->Ln();                    
                    foreach ($pai1['array'] as $key => $contas) {
                        $conta_pai_atual = '';
                        if ($conta_pai == $conta_pai_atual) {
                            $subtotal += $contas['total'];
                            $subtotal1 += $contas['total'];
                            $total += $contas['total'];
                        } else {
                            $subtotal = $contas['total'];
                            $subtotal1 += $contas['total'];
                            $total += $contas['total'];
                            $conta_pai_atual = $conta_pai;
                        }
                        $pdf->SetFont('Arial', '', 9);
                        $pdf->cell(2, .4);
                        $pdf->Cell(10, .4, $contas['descricao'],0,0,'L');
                        $pdf->Cell(7, .4, $contas['total'] < 0 ? '(' . number_format($contas['total'] * -1, 2, ',', '.') . ')' : number_format($contas['total'], 2, ',', '.'),0,0,'R');
                        $pdf->Ln();
                    }
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->Cell(12, .4, 'SUB TOTAL R$',0,0,'R' );
                    $pdf->Cell(7, .4, $subtotal1 < 0 ? '(' . number_format($subtotal1 * -1, 2, ',', '.') . ')' : number_format($subtotal1, 2, ',', '.'),0,0,'R');
                    $pdf->SetLineWidth(0);
                    $pdf->Ln();                    
                }
            }
            $pdf->Cell(10, .6, $pai3['descricao'],0,0,'R');                
            $pdf->Cell(2, .6, 'TOTAL R$','',0,'R');                
            $pdf->Cell(7, .6, ($subtotal < 0) ? '(' . number_format($subtotal * -1, 2, ',', '.') . ')' : number_format($subtotal, 2, ',', '.'), 'T', 0, 'R');
            $pdf->Ln();
            $pdf->SetLineWidth(0);
            if($contas['tipo'] == 1) {
                $somaR +=$subtotal;
            } elseif ($contas['tipo'] == 2) {
                $somaD +=$subtotal;
            }
            $saldo = $somaD + $somaR;
        }
        $pdf->Cell(19, .6, ($total < 0) ? '(' . number_format($total * -1, 2, ',', '.') . ')' : number_format($total, 2, ',', '.'), 'B', 0, 'R');
        $pdf->Ln(1.5);
    }
    $pdf->SetLineWidth(0);
    $pdf->Cell(10,.6,'SALDO DO EXERC�CIO','LTB',0,'C' );
    $pdf->Cell(9, .6, $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.'), 'TBR', 0, 'R');
    $pdf->Ln();
    $pdf->SetLineWidth(0);
    $pdf->Cell(19,.5,null,0,0);
    $pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->Ln();
$pdf->Cell(19,5,null,0,0);
$pdf->Ln();
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "DONATO LAVIANO NETO",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['responsavel'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CONTADOR",0,0,'C');
$pdf->Cell(9.5, .5, "S�CIO ADMINISTRADOR",0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CRC/RJ 068540-0",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['cpf'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "815.497.507-63",0,0,'C');
$pdf->Cell(9.5, .5, null,0,0,'C');
$pdf->Ln();
     
#############################################################################D R E#########################################################################
############################################################################PLANO DE CONTAS################################################################

$pdf->AddPage();
$arrayPlano = $objPlanoContas->getPlanoFull($projeto);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(19,.3,"Plano de Contas",0,0,'C');
$pdf->Ln();

foreach ($arrayPlano as $key => $value) {
    if($value['sped'])
        $pdf->SetFont('Arial','B',8);
    else
        $pdf->SetFont('Arial','',8);
    
    $esp = ($value['nivel']*0.4);
    $pdf->Cell($esp,.8,null,0,0);
    $textoPlano = (strlen($value['classificador'] . ' - ' . $value['descricao']) > 90) 
            ? substr($value['classificador'] . ' - ' . $value['descricao'], 0, 90).'...'
            : $value['classificador'] . ' - ' . $value['descricao'];
    $pdf->Cell(19-$esp,.8,$textoPlano,0,0);
    $pdf->Ln();
}

############################################################################PLANO DE CONTAS################################################################
############################################################################TERMO DE ENCERRAMENTO##########################################################

$pdf->AddPage();
$pdf->SetFont('Arial','B',30);
$pdf->Cell(19,1.3,"Termo de Encerramento",0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',12);
$pdf->Cell(19,.8,"EXERC�CIO DE {$ano}",0,0,'C');
$pdf->Ln();
$pdf->Cell(19,.8,null,0,0,'C');
$pdf->Ln();
$pdf->SetFont('Arial','',10);
$texto = "Este Livro Cont�m {nb} Folhas Numeradas de 1 a {nb}, emitidas por processo eletr�nico de dados, de acordo com a lei n�mero 8.981/95, art. 45, par�grafo �nico e servir� de Livro Di�rio de movimenta��o num�rica 4 da empresa o numero NIRE: 00.0.0000000-0, em 31/05/2012. Pela Junta Comercial do Estado de S�o Paulo. Do per�odo de 01 de Janeiro de " .$ano. " a 31 de dezembro de $ano. Com folhas numeradas apenas no averso.";
$pdf->MultiCell(19, .5, $texto,0,'L');
$pdf->Ln();
$pdf->Cell(19,.8,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Empresa: ".$rowEmpresa['razao'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Titular: ".$rowEmpresa['responsavel'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Endere�o: ".$rowEmpresa['endereco']."   CEP: ".$rowEmpresa['cep'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"CNPJ/MF: ".$rowEmpresa['cnpj'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Inscri��o Estadual: ".$rowEmpresa['ie'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,"Inscri��o Municipal: ".$rowEmpresa['im'],0,0,'L');
$pdf->Ln();
$pdf->Cell(19,.4,null,0,0);
$pdf->Ln();
$pdf->Cell(19,.6,$rowEmpresa['cidade'] . ", 31 de Dezembro de " .$ano,0,0,'R');
$pdf->Ln();
$pdf->Cell(19,5,null,0,0);
$pdf->Ln();
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Cell(8.5, .5, null,'B',0,'C');
$pdf->Cell(.5, .5, null,0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "DONATO LAVIANO NETO",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['responsavel'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CONTADOR",0,0,'C');
$pdf->Cell(9.5, .5, "S�CIO ADMINISTRADOR",0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "CRC/RJ 068540-0",0,0,'C');
$pdf->Cell(9.5, .5, $rowEmpresa['cpf'],0,0,'C');
$pdf->Ln();
$pdf->Cell(9.5, .5, "815.497.507-63",0,0,'C');
$pdf->Cell(9.5, .5, null,0,0,'C');
$pdf->Ln();

$pdf->Output();