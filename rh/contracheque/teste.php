<?php
include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";

// RECEBENDO VARIAVEIS
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc);
$decript = explode("&",$link);
$regiao 	= $decript[0];
$clt 		= $decript[1];
$id_folha 	= $decript[2];
//

$data = date('d/m/Y');
$ClassDATA = new regiao();
$ClassDATA -> RegiaoLogado();
$Clt = new clt();
$Curso = new tabcurso();

$REFolha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$RowFolha = mysql_fetch_array($REFolha);

$mes = $RowFolha['mes'];
$ano = $RowFolha['ano'];




////////REGIÃO
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto= '$RowFolha[projeto]'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

//////MASTER
$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);
    
//MÊs
$num_mes = sprintf('%02s',$mes);
$nome_mes = Junho; //mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$num_mes'"),0);






if($clt == "todos") {
	
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
define('FPDF_FONTPATH','../fpdf/font/');

$altura_celula  = 0.5;
$largura_celula = 13; 

$distancia = 2;


$pdf = new FPDF("L","cm","A4");

while($RowFolhaPro = mysql_fetch_array($REfolhaproc)) {

	
	
	
	
/////////////////////////////////////////////////////////////
//////////  CÁLCULO DE DEPENDENTES ///////////////////////////
////////////////////////////////////////////////////////////
$data_menor21 =  mktime(0,0,0, $mes, $dia, $ano - 21);
	
	
	
	$menor21 = mysql_query("SELECT * 	FROM dependentes 
							WHERE id_bolsista = '$RowFolhaPro[id_clt]'					
							AND id_regiao = '$regiao'
							 ") or die(mysql_error());
							
	$row_menor21 = mysql_fetch_assoc($menor21);
    
	if(mysql_num_rows($menor21)  != 0) {
		
		if($row_menor21['data1'] != '0000-00-00' ) { $filhos++; }
		if($row_menor21['data2'] != '0000-00-00' ) { $filhos++; }
		if($row_menor21['data3'] != '0000-00-00' ) { $filhos++; }
		if($row_menor21['data4'] != '0000-00-00' ) { $filhos++; }
		if($row_menor21['data5'] != '0000-00-00' ) { $filhos++; }
		if($row_menor21['data6'] != '0000-00-00' ) { $filhos++; }
			
		
		
		
		$data1 = explode('-', $row_menor21['data1']); 
		$data1 = @mktime(0,0,0,$data1[1], $data1[2], $data1[0]); 
		
		$data2 = explode('-', $row_menor21['data2']); 
		$data2 = @mktime(0,0,0,$data2[1], $data2[2], $data2[0]); 
		
		$data3 = explode('-', $row_menor21['data3']); 
		$data3 = @mktime(0,0,0,$data3[1], $data3[2], $data3[0]); 
		
		$data4 = explode('-', $row_menor21['data4']); 
		$data4 = @mktime(0,0,0,$data4[1], $data4[2], $data4[0]); 
		
		$data5 = explode('-', $row_menor21['data5']); 
		$data5 = @mktime(0,0,0,$data5[1], $data5[2], $data5[0]); 
		
		$data6 = explode('-', $row_menor21['data6']); 
		$data6 = @mktime(0,0,0,$data6[1], $data6[2], $data6[0]); 
	    
		
		
		
		if($data1 > $data_menor21 and $row_menor21['data1'] != '0000-00-00'){ $total_filhos_menor_21++;} 
		if($data2 > $data_menor21 and $row_menor21['data2'] != '0000-00-00'){ $total_filhos_menor_21++;} 
		if($data3 > $data_menor21 and $row_menor21['data3'] != '0000-00-00'){ $total_filhos_menor_21++;} 
		if($data4 > $data_menor21 and $row_menor21['data4'] != '0000-00-00'){ $total_filhos_menor_21++;} 
		if($data5 > $data_menor21 and $row_menor21['data5'] != '0000-00-00'){ $total_filhos_menor_21++;} 
		if($data6 > $data_menor21 and $row_menor21['data6'] != '0000-00-00'){ $total_filhos_menor_21++;} 	
	}
	if(empty($filhos)){ $filhos = 0;}
	if(empty($total_filhos_menor_21)){ $total_filhos_menor_21 = 0;}
	
///////////////////////////////////////////////////


$qr_clt= mysql_query("SELECT *, MONTH(data_entrada) as mes_adm, YEAR(data_entrada) as ano_adm FROM rh_clt WHERE id_clt = '$RowFolhaPro[id_clt]'");
$row_clt = mysql_fetch_assoc($qr_clt);

//BANCO
$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$RowFolhaPro[id_banco]'");
$banco    = mysql_fetch_assoc($qr_banco);	

	
/////FUNÇÃO
$qr_funcao  = mysql_query("SELECT * FROM  curso where id_curso = '$row_clt[id_curso]' ");
$row_funcao = mysql_fetch_assoc($qr_funcao); 	
	
	
	
//////////////////////////////////////////////////////////////////////
/////////////////////////INICIO DO PDF ///////////////////////////////
//////////////////////////////////////////////////////////////////////

$pdf->AddPage();
$pdf->SetFont('Arial','',10);
$pdf->SetTopMargin(1);

////LINHA 1
$pdf->Cell($largura_celula,$altura_celula,'Recibo de Pagamento de Salário',1,'0','C');

$pdf->Cell($distancia,$altura_celula,'',0,'0','C');

$pdf->Cell($largura_celula,$altura_celula,'Recibo de Pagamento de Salário',1,'0','C');

///LINHA 2
$pdf->SetFont('Arial','',8);
$pdf->Ln($altura_celula);
$pdf->Cell($largura_celula,$altura_celula,$row_master['razao'],1,'0','C');
$pdf->Cell($distancia,$altura_celula,'',0,'0','C');
$pdf->Cell($largura_celula,$altura_celula,$row_master['razao'],1,'0','C');
$pdf->Ln($altura_celula);


$pdf->SetFont('Arial','',10);
//LINHA 3
//$pdf->SetFont('Arial','',12);
$pdf->Cell($largura_celula,$altura_celula,'' ,1,'0','L');
$pdf->Text(1.2, 2.4,'C.N.P.J.: ');
$pdf->Text(2.8, 2.4,$row_master['cnpj']);
$pdf->Text(11, 2.4, $nome_mes.'/'.$ano );

$pdf->Cell($distancia,$altura_celula,'',0,'0','C');


$pdf->Cell($largura_celula,$altura_celula,'' ,1,'0','L');
$pdf->Text(1.3 + $largura_celula+ $distancia, 2.4,'C.N.P.J.: ');
$pdf->Text(2.9 + $largura_celula+ $distancia, 2.4,$row_master['cnpj']);
$pdf->Text(11  + $largura_celula+ $distancia , 2.4, $nome_mes.'/'.$ano );
$pdf->Ln($altura_celula);


$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');


$pdf->SetFont('Arial','',7);
$pdf->Text(1.2, 2.8,'COD/FUNCIONARIO ');
$pdf->Text(1.2, 3.2,$row_clt['matricula']);
$pdf->Cell(9.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(4.6, 2.8,'NOME FUNCIONARIO');
$pdf->SetFont('Arial','B',7);
$pdf->Text(4.6, 3.2,'VANESSA DE ALMEIDA MALDONADO');
$pdf->SetFont('Arial','',7);

$pdf->Cell($distancia,$altura_celula,'',0,'0','C');


$pdf->Text(3.2 + $largura_celula, 2.8,'COD/FUNCIONARIO ');
$pdf->Text(3.2 + $largura_celula, 3.2,$row_clt['matricula']);
$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(6.6 + $largura_celula, 2.8,'NOME FUNCIONARIO');

$pdf->SetFont('Arial','B',7);
$pdf->Text(6.6 + $largura_celula, 3.2,'VANESSA DE ALMEIDA MALDONADO');
$pdf->Cell(9.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->SetFont('Arial','',7);



///LINHA 4
$pdf->Ln($altura_celula + 0.3);
$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(1.2 , 3.6,'FORMA DE PAGAMENTO ');
$pdf->SetFont('Arial','',9);
$pdf->Text(1.2 , 4,'Transferência');
$pdf->SetFont('Arial','',7);

$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(4.7 , 3.6,'FILHOS');
$pdf->SetFont('Arial','',9);
$pdf->Text(4.7 , 4, $filhos);
$pdf->SetFont('Arial','',7);


$pdf->Cell(6,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(8.2 , 3.6,'DEPENDENTES');
$pdf->SetFont('Arial','',9);
$pdf->Text(8.2 , 4, $total_filhos_menor_21);
$pdf->SetFont('Arial','',7);


$pdf->Cell($distancia,$altura_celula,'',0,'0','C');



$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(3.2 + $largura_celula, 3.6,'FORMA DE PAGAMENTO ');
$pdf->SetFont('Arial','',9);
$pdf->Text(3.2 + $largura_celula, 4,'Transferência');
$pdf->SetFont('Arial','',7);

$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(6.7 + $largura_celula, 3.6,'FILHOS');
$pdf->SetFont('Arial','',9);
$pdf->Text(6.7 + $largura_celula, 4, $filhos);
$pdf->SetFont('Arial','',7);


$pdf->Cell(6,$altura_celula + 0.3,'' ,1,'0','L');  
$pdf->Text(10.2 + $largura_celula, 3.6,'DEPENDENTES'); 
$pdf->SetFont('Arial','',9);
$pdf->Text(10.2 + $largura_celula, 4, $total_filhos_menor_21);
$pdf->SetFont('Arial','',7);



////////LINHA 5
$pdf->Ln($altura_celula + 0.3);
$pdf->Cell(13,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(1.2 , 4.4,'FUNÇÃO');
$pdf->SetFont('Arial','',9);
$pdf->Text(1.2 , 4.8,  'ASSISTENTE DE RH');
$pdf->SetFont('Arial','',7);

$pdf->Cell($distancia,$altura_celula,'',0,'0','C');


$pdf->Cell(13,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(3.2 + $largura_celula, 4.4,'FUNÇÃO');
$pdf->SetFont('Arial','',9);
$pdf->Text(3.2 + $largura_celula, 4.8, 'ASSISTENTE DE RH');
$pdf->SetFont('Arial','',7);



if(empty($row_clt['nome_banco'])){
	
	$nome_banco = $banco['nome'];
} else {

$nome_banco = $row_clt['nome_banco'];	
}



///LINHA 6
$pdf->Ln($altura_celula + 0.3);
$pdf->Cell(5.2,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(1.2 , 5.2,'BANCO');
$pdf->SetFont('Arial','',9);
$pdf->Text(1.2 , 5.6, $nome_banco);
$pdf->SetFont('Arial','',7);

$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(6.4 , 5.2,'AGÊNCIA');
$pdf->SetFont('Arial','',9);
$pdf->Text(8.3 , 5.6, '08508');
$pdf->SetFont('Arial','',7);


$pdf->Cell(4.3,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(10 , 5.2,'CONTA');
$pdf->SetFont('Arial','',9);
$pdf->Text(12.2 , 5.6, '9634134');
$pdf->SetFont('Arial','',7);


$pdf->Cell($distancia,$altura_celula,'',0,'0','C');




$pdf->Cell(5.2,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(3.2 + $largura_celula, 5.2,'BANCO');
$pdf->SetFont('Arial','',9);
$pdf->Text(3.2 + $largura_celula, 5.6, $nome_banco);
$pdf->SetFont('Arial','',7);



$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(8.5 + $largura_celula, 5.2,'AGÊNCIA');
$pdf->SetFont('Arial','',9);
$pdf->Text(10.3+ $largura_celula, 5.6,'08508');
$pdf->SetFont('Arial','',7);


$pdf->Cell(4.3,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(12 + $largura_celula, 5.2,'CONTA');
$pdf->SetFont('Arial','',9);
$pdf->Text(14.2 + $largura_celula, 5.6,'9634134');
$pdf->SetFont('Arial','',7);


 


///LINHA 7
$pdf->Ln($altura_celula + 0.3);
$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(1.2 , 6,'CARTEIRA PROF.');
$pdf->Text(1.2 , 6.4, '49523');

$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(4.6 , 6,'NÚMERO DE SÉRIE');
$pdf->Text(4.6 , 6.4, 160);

$pdf->Cell(1.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(8.2 , 6,'UF');
$pdf->Text(8.2 , 6.4, 'RJ
');

$pdf->Cell(4.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(9.6 , 6,'CENTRO DE CUSTO');
$pdf->Text(9.6 , 6.4, $row_projeto['nome']);
 
  
$pdf->Cell($distancia,$altura_celula,'',0,'0','C');


$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(3.2  + $largura_celula, 6,'CARTEIRA PROF.');
$pdf->Text(3.2  + $largura_celula, 6.4, '49523');

$pdf->Cell(3.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(6.6  + $largura_celula, 6,'NÚMERO DE SÉRIE');
$pdf->Text(6.6  + $largura_celula, 6.4, 160);

$pdf->Cell(1.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(10.2  + $largura_celula, 6,'UF');
$pdf->Text(10.2  + $largura_celula, 6.4, 'RJ');

$pdf->Cell(4.5,$altura_celula + 0.3,'' ,1,'0','L');
$pdf->Text(11.6 + $largura_celula, 6,'CENTRO DE CUSTO');
$pdf->Text(11.6  + $largura_celula, 6.4, '');


///////////linha 8
$pdf->Ln($altura_celula + 0.3);
$pdf->SetFont('Arial','B',6);



$altura_mov = 0.6;
$pdf->Cell(1.3,$altura_mov,'CÓD.' ,1,'0','C');
$pdf->Cell(6.2,$altura_mov,'DESCRIÇÃO' ,1,'0','C');
$pdf->Cell(1.5,$altura_mov,'' ,1,'0','C');
$pdf->Cell(2,$altura_mov,'VENCIMENTOS' ,1,'0','C');
$pdf->Cell(2,$altura_mov,'DESCONTOS' ,1,'0','C');



$pdf->Cell($distancia,$altura_celula,'',0,'0','C');


$pdf->Cell(1.3,$altura_mov,'CÓD.' ,1,'0','C');
$pdf->Cell(6.2,$altura_mov,'DESCRIÇÃO' ,1,'0','C');
$pdf->Cell(1.5,$altura_mov,'' ,1,'0','C');
$pdf->Cell(2,$altura_mov,'VENCIMENTOS' ,1,'0','C');
$pdf->Cell(2,$altura_mov,'DESCONTOS' ,1,'0','C');


//////////////////EXIBINDO OS MOVIMENTOS

//salário base
// Se a Folha é nova...
if(date('Y-m-d') > date('2010-06-09')) {
    
    	if($RowFolhaPro['dias_trab'] < 30){
           
                
                 
                 if($RowFolhaPro['mes'] == $row_clt['mes_adm'] and $RowFolhaPro['ano_mov'] == $row_clt['ano_mov']){
                     
                       $salb      =   $RowFolhaPro['sallimpo_real'];
                       $dias_trab = '('.$RowFolhaPro['dias_trab'].' dias)';
                 } else {
                      $salb =   $RowFolhaPro['sallimpo'];
                 }
                 
                 
                 
           // $dias_trab = '('.$RowFolhaPro['dias_trab'].' dias)';
            
        } else {
        
            $salb = $RowFolhaPro['sallimpo']; 
            $dias_trab = '';
        }
	
} else {
    
    if($RowFolhaPro['dias_trab'] < 30){
           
                 $salb =   $RowFolhaPro['sallimpo_real'];
                 //$dias_trab = '('.$RowFolhaPro['dias_trab'].' dias)';
            
        } else {
        
            $salb = $RowFolhaPro['salbase'] - $RowFolhaPro['a6006']; 
            $dias_trab = '';
        }
       
}

$total_vencimentos = $salb;


////////////////////////////////////////
///////////SALÁRIO BASE ///////////////
///////////////////////////////////////

$pdf->SetFont('Arial','B',6);
$borda = 0;
$altura_mov = 0.4;
$pdf->Ln( 0.6);
$pdf->Cell(1.3,$altura_mov , '0001' ,$borda,'0','C');
$pdf->Cell(6.2,$altura_mov , 'SALÁRIO BASE '.$dias_trab ,$borda,'0','L');
$pdf->Cell(1.5,$altura_mov,'' ,$borda,'0','C');
$pdf->Cell(2,$altura_mov , $salb ,$borda,'0','R');
$pdf->Cell(2,$altura_mov , '' ,$borda,'0','C');


$pdf->Cell($distancia,$altura_celula,'',0,'0','C');

$pdf->Cell(1.3,$altura_mov, '0001' ,$borda,'0','C');
$pdf->Cell(6.2,$altura_mov, 'SALÁRIO BASE '.$dias_trab ,$borda,'0','L');
$pdf->Cell(1.5,$altura_mov,'' ,$borda,'0','C');
$pdf->Cell(2,$altura_mov, $salb ,$borda,'0','R');
$pdf->Cell(2,$$altura_mov, '' ,$borda,'0','C');
///////////////////////////////////////


$array_outros_movimentos = array(5060,5061,9999,5913,5912, 9997, 10008, 10009);


$qr_movimentos = mysql_query("SELECT DISTINCT (cod), descicao, categoria FROM rh_movimentos WHERE  cod NOT IN('0001','5024','9991','5044','5035','9996','6006','9000','9999','9998','5012','7003','5011','9099', '50221','8006', '8005', '50222', 6004, 5049,50241,50111,50221,9500, 50243, 50242) AND id_mov NOT IN(77,59)  ORDER BY cod ASC") or die(mysql_error());
while($row_mov = mysql_fetch_assoc($qr_movimentos)):
	
	$nome_campo      = 'a'.$row_mov['cod'];
	$categoria       = $row_mov['categoria'];
	$valor_movimento = $RowFolhaPro[$nome_campo];
	
		if($valor_movimento  != '0.00' and !in_array($row_mov['cod'], $array_outros_movimentos)){
                    
				if($categoria == 'CREDITO'){ 
						
					$vencimentos 		= number_format( $valor_movimento ,2,",",".");
					$desconto    		= '';
					$total_vencimentos +=  $valor_movimento;
				
				} else if($categoria == 'DEBITO'  or $categoria == 'DESCONTO'){
					
					$desconto 		= number_format( $valor_movimento ,2,",",".");
					$vencimentos  	 = '';
					$total_desconto +=  $valor_movimento ;	
				}
					
			
	
			
				$pdf->Ln( 0.4);
				$pdf->Cell(1.3,$altura_mov, $row_mov['cod'] ,$borda,'0','C');
				$pdf->Cell(6.2,$altura_mov, $row_mov['descicao'] ,$borda,'0','L');
				$pdf->Cell(1.5,$altura_mov,'' ,$borda,'0','C');
				$pdf->Cell(2,$altura_mov, $vencimentos,$borda,'0','R');
				$pdf->Cell(2,$altura_mov, $desconto ,$borda,'0','R');
				
				$pdf->Cell($distancia,$altura_celula,'',0,'0','C');
				
				$pdf->Cell(1.3,$altura_mov, $row_mov['cod'] ,$borda,'0','C');
				$pdf->Cell(6.2,$altura_mov, $row_mov['descicao'] ,$borda,'0','L');
				$pdf->Cell(1.5,$altura_mov,'' ,$borda,'0','C');
				$pdf->Cell(2,$altura_mov, $vencimentos,$borda,'0','R');
				$pdf->Cell(2,$altura_mov, $desconto ,$borda,'0','R');
					
		
		
		
	} 
	endwhile;
	
		
		if(!empty($RowFolhaPro['ids_movimentos'])) {
                    
                	
		$qr_movimento_clt = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  id_movimento IN($RowFolha[ids_movimentos_estatisticas]) AND id_clt = '$RowFolhaPro[id_clt]' ") or die(mysql_error());
		if(mysql_num_rows($qr_movimento_clt) != 0) {
			
                    
                
		while($row_mov2 = mysql_fetch_assoc($qr_movimento_clt)):
			
			if($row_mov2['tipo_movimento'] == 'CREDITO'){ 
			
				$vencimentos = number_format($row_mov2['valor_movimento'],2,",",".");
				$desconto    = '';
				$total_vencimentos += $row_mov2['valor_movimento'];
			
			} else if($row_mov2['tipo_movimento'] == 'DEBITO'  or $row_mov2['tipo_movimento'] == 'DESCONTO'){
				
			    $desconto = number_format($row_mov2['valor_movimento'],2,",",".");
				$vencimentos = '';
				$total_desconto += $row_mov2['valor_movimento'];
			}
			
			
			$altura_tabela_mov = $altura_tabela_mov +$altura_mov;
						
                        //FALTAS
                        if($row_mov2['cod_movimento'] == 8000){
                            $nome_movimento =  $row_mov2['nome_movimento'].' ('. $row_mov2['qnt'].' falta(s))';
                        } else {
                            $nome_movimento = $row_mov2['nome_movimento'];
                        }
                        
			$pdf->Ln( 0.4);
			$pdf->Cell(1.3 ,$altura_mov, $row_mov2['cod_movimento'],$borda,'0','C');
			$pdf->Cell(6.2,$altura_mov, $nome_movimento ,$borda,'0','L');
			$pdf->Cell(1.5,$altura_mov, '',$borda,'0','C');
			$pdf->Cell(2,$altura_mov, $vencimentos,$borda, '0','R');
			$pdf->Cell(2,$altura_mov, $desconto,$borda,'0','R');
			
			$pdf->Cell($distancia,$altura_celula,'',0,'0','C');
			
			$pdf->Cell(1.3 ,$altura_mov, $row_mov2['cod_movimento'],$borda,'0','C');
			$pdf->Cell(6.2,$altura_mov, $row_mov2['nome_movimento'] ,$borda,'0','L');
			$pdf->Cell(1.5,$altura_mov, '',$borda,'0','C');
			$pdf->Cell(2,$altura_mov, $vencimentos,$borda, '0','R');
			$pdf->Cell(2,$altura_mov, $desconto,$borda,'0','R');
			

			/*$pdf->Cell($distancia,$altura_celula,'',0,'0','C');
			
			$pdf->Text(1.3,$altura_celula + 0.3, $row_mov['cod'] ,0,'0','C');
			$pdf->Text(4.2,$altura_celula + 0.3, $row_mov['descicao'] ,0,'0','C');
			$pdf->Text(1.5,$altura_celula + 0.3, '' ,0,'0','C');	
			$pdf->Text(3,$altura_celula + 0.3, $vencimentos ,0,'0','C');
			$pdf->Text(3,$altura_celula + 0.3, $desconto,0,'0','C');
			*/
		
		
		endwhile;
		}
          }
			
		

/////////DESENHANDO FUNDO DA TABELA DE MOVIMENTOS
$altura_fundo =7.6;
$pdf->Rect(1,7.1,1.3,$altura_fundo);
$pdf->Rect(2.29,7.1,6.2,$altura_fundo);
$pdf->Rect(8.5,7.1,1.5,$altura_fundo);
$pdf->Rect(10,7.1,2,$altura_fundo);
$pdf->Rect(12,7.1,2,$altura_fundo);


$pdf->Rect(16,7.1,1.3,$altura_fundo);
$pdf->Rect(17.3,7.1,6.2,$altura_fundo);
$pdf->Rect(23.5,7.1,1.5,$altura_fundo);

$pdf->Rect(25,7.1,2,$altura_fundo);
$pdf->Rect(27,7.1,2,$altura_fundo);
////////////////


////////LIINHA TOTAIS
$pdf->Rect(6.5,14.7,3.8,0.4);
$pdf->Text(7.2,15, 'TOTAL DE RENDIMENTOS');

$pdf->Rect(10.3,14.7,3.7,0.4);	
$pdf->Text(11,15, 'TOTAL DE DESCONTOS');

$pdf->Rect(6.5,15.1,3.8,0.4);
$pdf->Text(9.2,15.4, number_format($total_vencimentos,2,',','.') );

$pdf->Rect(10.3,15.1,3.7,0.4);
$pdf->Text(13.1,15.4, number_format($total_desconto,2,',','.') );



$pdf->Rect(21.5,14.7,3.8,0.4);
$pdf->Text(22.2,15, 'TOTAL DE RENDIMENTOS');

$pdf->Rect(25.3,14.7,3.7,0.4);	
$pdf->Text(26,15, 'TOTAL DE DESCONTOS');

$pdf->Rect(21.5,15.1,3.8,0.4);
$pdf->Text(24.1,15.4, number_format($total_vencimentos,2,',','.') );

$pdf->Rect(25.3,15.1,3.7,0.4);
$pdf->Text(28.1,15.4, number_format($total_desconto,2,',','.') );
unset($vencimentos, $total_vencimentos, $desconto, $total_desconto, $linha);

///////////////////////////////////////
//////////////////////////////////////
///////// VALOR LíQUIDO /////////////
/////////////////////////////////////
$pdf->Text(12.3,15.9, 'VALOR LÍQUIDO');
$pdf->Rect(1,16,13,0.4);
$pdf->Text(13,16.3, number_format($RowFolhaPro['salliquido'],2,",","."));


$pdf->Text(27.3,15.9, 'VALOR LÍQUIDO');
$pdf->Rect(16,16,13,0.4);
$pdf->Text(28,16.3, number_format($RowFolhaPro['salliquido'],2,",","."));

////////////////////////////////////////

$y = 16.9;
$pdf->Rect(1,16.4,13,0.6);

$pdf->Text(1.2,$y, 'SALÁRIO BASE');
$pdf->Text(3.6,$y, 'SALÁRIO CONTRAT.');
$pdf->Text(7.87,$y, 'FGTS MÊS');
$pdf->Text(11,$y, 'BASE CÁLC. IRRF');


$y = 17.3;
$pdf->Rect(1,17,13,0.4);
$pdf->Text(1.9,$y, number_format($salb,2,",","."));
$pdf->Text(4.9,$y, number_format($RowFolhaPro['salbase'],2,",","."));
$pdf->Text(8.1,$y, number_format($RowFolhaPro['fgts'],2,",","."));
$pdf->Text(12,$y, number_format($RowFolhaPro['base_irrf'],2,",","."));

///////////////LADO DIREITO

$y = 16.9;
$pdf->Rect(16,16.4,13,0.6);

$pdf->Text(16.2,$y, 'SALÁRIO BASE ');
$pdf->Text(18.6,$y, 'SALÁRIO CONTRAT.');
$pdf->Text(22.2,$y, 'FGTS MÊS');
$pdf->Text(25,$y, 'BASE CÁLC. IRRF');


$y = 17.3;
$pdf->Rect(16,17,13,0.4);
$pdf->Text(16.9,$y, number_format($salb,2,",","."));
$pdf->Text(19.8,$y, number_format($RowFolhaPro['salbase'],2,",","."));
$pdf->Text(22.5,$y, number_format($RowFolhaPro['fgts'],2,",","."));
$pdf->Text(25.9,$y, number_format($RowFolhaPro['base_irrf'],2,",","."));

/////////////////
//////////////////DATA E ASSINATURA
$pdf->Rect(1,17.4,13,1.9);
$pdf->Rect(16,17.4,13,1.9);

$pdf->SetFontSize('9');



$y = 18.7;
$pdf->Text(1.3,17.8, 'Declaro ter recebido a importância líquida discriminada neste recibo');
$pdf->Text(1.3,$y, '____/____/_____');
$pdf->Text(2,$y+0.4, 'Data');

$pdf->Text(6,$y, '__________________________________________');
$pdf->Text(7.6,$y+0.4, 'Assinatura do funcionário');



$pdf->Text(16.2,17.8, 'Declaro ter recebido a importância líquida discriminada neste recibo');
$pdf->Text(16.3,$y, '____/____/_____');
$pdf->Text(17,$y+0.4, 'Data');

$pdf->Text(21,$y, '__________________________________________');
$pdf->Text(23,$y+0.4, 'Assinatura do funcionário');


unset($filhos, $total_filhos_menor_21);
}
	

$pdf->Output('as.pdf','I');
?>