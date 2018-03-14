<?
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../../../conn.php";

$ACAO = $_REQUEST['acao'];
$ID_PROTOCOLO = $_REQUEST['id_protocolo'];
$MES_REFERENCIA = $_REQUEST['mes_referencia'];
$DATA = $_REQUEST['data'];
$REGIAO = $_REQUEST['regiao'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>::Vale Express::</title>
<link href="../../../net1.css" rel="stylesheet" type="text/css" />
</head>

<body class="">

<div align="center">
	<div align="center" style="width:90%; background:#FFF">
		<div align="center">
    		<?php
					include "../../../empresa.php";
					$imgCNPJ = new empresa();
					$imgCNPJ -> imagemCNPJ();
					
					$ANO = date('Y');	
					$arquivo = "ve".$REGIAO.$MES_REFERENCIA.$ANO.".txt";
					$pdf = "ve".$REGIAO.$MES_REFERENCIA.$ANO.".pdf";
					$pdfPapel = "PAPEL_ve".$REGIAO.$MES_REFERENCIA.$ANO.".pdf";
					if (file_exists($arquivo)){
						print "<br><br>";
						print "<span class='igreja'><strong>ESTE ARQUIVO JÁ FOI GERADO!</strong></span>";
						print '<br><br>';
						
						print "<table>";
						print "<tr>";
						print "<td><a href=download.php?file=$arquivo><img src='../imagens/download2.ico' border='0' alt='Download do Arquivo'></a></td>";
						print "<td></td>";
						print "<td><a href=$pdf target='_blank'><img height='48' width='48' src='../imagens/visualizar.png' border='0' alt='Visualizar do Arquivo PDF'></a></td>";
						print "<td></td>";
						print "<td><a href=$pdfPapel target='_blank'><img height='48' width='48' src='../imagens/visualizar.png' border='0' alt='Visualizar do Arquivo PDF'></a></td>";						
						print "<td></td>";
						print "<td><a href=#><img height='48' width='48' src='../imagens/pagar.png' border='0' alt='Solicitar Pagamento'></a></td>";
						print "</tr>";
						print "<tr>";
						print "<td><span class='igreja'>DOWNLOAD DO ARQUIVO TXT</span></td>";
						print "<td width='60px'></td>";
						print "<td><span class='igreja'>RELATÓRIO PDF DE VALES TIPO CARTÃO</span></td>";
						print "<td width='60px'></td>";
						print "<td><span class='igreja'>RELATÓRIO PDF DE VALES TIPO PAPEL</span></td>";						
						print "<td width='60px'></td>";
						print "<td><span class='igreja'>SOLICITAR PAGAMENTO</span></td>";
						print "</tr>";						
						print "</table>";
						print '<br><br>';
						exit;
						
					}
			?>  
            <div>
            	<br><span class="igreja">GERANDO <b>VALE EXPRESS</b> COM AS SEGUINTES INFORMAÇÕES</span><br><br>
            </div>       
         </div>
         <?php
		 		//RETIRA DA TABELA, O CÓDIGO DOS FUNCIOÁRIOS QUE SERÃO EMITIDOS VALE
		 		$resultRhValeRRelatorio = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA' ORDER BY nome");				
				
				//RETIRA OS CÓDIGO DUPLICADOS
		 		while ($rowFuncionarios = mysql_fetch_array($resultRhValeRRelatorio)){
						$arrayId_func[] = $rowFuncionarios['id_func'];
		 		}

				//RETIRA OS IDS REPETIDOS
				$result = array_unique($arrayId_func);
				//CONTA OS RESTANTES
				$quant = count($result);

				//BUSCA O ANO EXATO DE QUANDO O PROTOCOLO DA TABELA rh_vale_relatorio FOI CRIADO E A REGIAO
				$resultAno = mysql_query("SELECT * FROM rh_vale_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA'");
				$rowAnoRegiao = mysql_fetch_array($resultAno);
				$ANO = $rowAnoRegiao['ano'];
				$REGIAO = $rowAnoRegiao['id_reg'];
				
				for($i=0; $i<$quant; $i++){
					$arrayId_func[$i] = current($result);
					next($result);
				}
				
				

				$PRODUTO = '01'; //VALOR FIXO PARA O SISTEMA DA VALE EXPRESS CONCLUA QUE A LINHA SE REFERE A UMA EMPRESA
				
				//OBTEM O CNPJ DA EMPRESA QUE ESTÁ NA CLASSE EMPRESA
				$cnpj = new empresa();				
				$cnpj = $cnpj -> cnpjEmpresa3();				
				//RETIRANDO A FORMATACAO DO CNPJ
				$remover01 = array(".", "-", "/");
				$cnpj = str_replace($remover01, "", $cnpj);
				//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 14 DIGITOS
				$CNPJ = sprintf("%014s",$cnpj);
				
				$dataE = explode("-", $DATA);
				$d=$dataE[2];
				$m=$dataE[1];
				$a=$dataE[0]; 
				$data = $d.$m.$a;
				//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 8 DIGITOS
				$DATA = sprintf("%08d",$data);
					
				//GRAVANDO DADOS NO ARQUIVO TEXTO
				$handle = fopen ("ve".$REGIAO.$MES_REFERENCIA.$ANO.".txt", "a");						
				
				fwrite($handle, $PRODUTO, 2); //PRODUTO
				fwrite($handle, $CNPJ, 14); //CNPJ
				fwrite($handle, $DATA, 8); //DATA
				fwrite($handle, "\r\n");//QUEBRA DE LINHA

				echo "<div align='center'>";
				//BUSCANDO DADOS PESSOAIS DE CADA FUCNIOÁRIO
				
				print "<div style='font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 10px; color: #000; height:20px; display:table-cell;background:#CCC'>";										
				print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'><b>ID</b></div>";								
				print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'><b>CPF</b></div>";						
				print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'><b>NOME</b></div>";
				print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'><b>CÓDIGO</b></div>";
				print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'><b>ITINERÁRIO</b></div>";
				print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'><b>CÓDIGO DO PRODUTO</b></div>";
				print "<div style='width:10%; float:left; display:table-cell; line-height:20px;'><b>VALOR</b></div>";
				print "<div style='width:10%; float:left; display:table-cell; line-height:20px;'><b>QUANTIDADE</b></div>";
				print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'><b>VALOR PARCIAL</b></div>";				
				print "</div>";

				$linha = 0;
				require_once("fpdf/fpdf.php");

				define('FPDF_FONTPATH','fpdf/font/');
				$pdf= new FPDF("P","cm","A4");
				$pdf->SetAutoPageBreak(true,1); //Reduz a tolerancia da margem inferior
				$pdf->Open();
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(5,30,'');
				$pdf->SetXY(1,1);
				
				$pdf->MultiCell(19, 0.5,"PEDIDO DE RECARGA DO CARTÃO VALE EXPRESS",0,'C');
				$pdf->Ln();

				//VALE DO TIPO PAPEL
				$pdf03= new FPDF("P","cm","A4");
				$pdf03->SetAutoPageBreak(true,1); //Reduz a tolerancia da margem inferior
				$pdf03->Open();
				$pdf03->SetFont('Arial','B',9);
				$pdf03->Cell(5,30,'');
				$pdf03->SetXY(1,1);
				
				$pdf03->MultiCell(19, 0.5,"PEDIDO DE VALE TRANSPORTE TIPO PAPEL",0,'C');
				$pdf03->Ln();
				
				//SELECIONA O PERÍODO DO VALE TRANSPORTE
				$resultPeriodo03 = mysql_query("SELECT date_format(data_ini,'%d/%m/%Y') AS data_inicial, date_format(data_fim,'%d/%m/%Y') AS data_final FROM rh_vale_protocolo WHERE id_protocolo = '$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes = '$MES_REFERENCIA' AND ano = '$ANO'");
				$rowPer03 = mysql_fetch_array($resultPeriodo03);
				$pdf03->MultiCell(19, 0.5,"Período: ".$rowPer03['data_inicial']." a ".$rowPer03['data_final'],0,'C');
				$pdf03->Ln(0.04);				

				//IMPRIME O NOME DA REGIAO
				$resultRegiao03 = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$REGIAO'");
				$rowRegiao03 = mysql_fetch_array($resultRegiao03);
				$pdf03->MultiCell(19, 0.5,"Região: ".$rowRegiao03['regiao'],0,'C');
				$pdf03->Ln();
				$pdf03->Ln();
				$pdf03->SetX(3.5);				
				$pdf03->Cell(2, 0.5,"ID",1,0,'C');
				$pdf03->Cell(3, 0.5,"CPF",1,0,'C');
				$pdf03->Cell(7, 0.5,"NOME",1,0,'C');
				$pdf03->Cell(2, 0.5,"VALOR",1,0,'C');
				$pdf03->Ln();				

				//SELECIONA O PERÍODO DO VALE TRANSPORTE
				$resultPeriodo = mysql_query("SELECT date_format(data_ini,'%d/%m/%Y') AS data_inicial, date_format(data_fim,'%d/%m/%Y') AS data_final FROM rh_vale_protocolo WHERE id_protocolo = '$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes = '$MES_REFERENCIA' AND ano = '$ANO'");
				$rowPer = mysql_fetch_array($resultPeriodo);
				$pdf->MultiCell(19, 0.5,"Período: ".$rowPer['data_inicial']." a ".$rowPer['data_final'],0,'C');
				$pdf->Ln(0.04);				

				//IMPRIME O NOME DA REGIAO
				$resultRegiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$REGIAO'");
				$rowRegiao = mysql_fetch_array($resultRegiao);
				$pdf->MultiCell(19, 0.5,"Região: ".$rowRegiao['regiao'],0,'C');
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetX(3.5);				
				$pdf->Cell(2, 0.5,"ID",1,0,'C');
				$pdf->Cell(3, 0.5,"CPF",1,0,'C');
				$pdf->Cell(7, 0.5,"NOME",1,0,'C');
				$pdf->Cell(2, 0.5,"VALOR",1,0,'C');
				$pdf->Ln();
				
				for ($c=0; $c<$quant; $c++){
						$resultCLT = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$REGIAO' AND id_clt = '$arrayId_func[$c]'");
						$rowCLT= mysql_fetch_array($resultCLT);
						
						//VINCULO DA TABELA rh_clt COM A TABLELA rhempresa
						$vinculo_tb_rh_clt_e_rhempresa = $rowCLT['rh_vinculo'];

						$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
						$row_empresa = mysql_fetch_array($result_empresa);

						$result02 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA' AND id_func = '$arrayId_func[$c]' AND tipo='CARTÃO'");	
						
						//VALE DO TIPO PAPEL
						$result03 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA' AND id_func = '$arrayId_func[$c]' AND tipo='PAPEL'");						
						$cont = 0;
						while ($row03= mysql_fetch_array($result03)){
							$codigo03[]=$row03['id_func'];
							$quantidade03 = $row03['quantidade'];
							$quantidade03 = $quantidade03*2;
							$valorVale03 = $row03['valor'];
							$valorParcial03 = $valorVale03 * $quantidade03;
							$arrayValorTotal03[] = $valorParcial03;	
							$codigo03[]=$row03['id_func'];
						}
						
					$cont = 0;
						while ($row= mysql_fetch_array($result02)){
								$PRODUTO = '02'; //VALOR FIXO PARA O SISTEMA DA VALE EXPRESS CONCLUA QUE A LINHA SE REFERE A UM FUNCIONÁRIO
								$cpf = $rowCLT['cpf'];
								$valorUnitario = $row['valor'];
								//RETIRANDO OS PONTOS E OS TRAÇOS DO CPF E VALOR
								$remover = array(".", "-", "/");
								$cpf = str_replace($remover, "", $cpf);
								//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 11 DIGITOS
								$CPF = sprintf("%011s", $cpf);
								//FORMATANDO O VALOR PARA DUAS CASAS DECIMAIS SEM O PONTO "."
								$VALORUNITARIO = number_format($valorUnitario,2,"","");
								$VALORUNITARIO = sprintf("%07s", $VALORUNITARIO);
								
								$resultCodigo = mysql_query("SELECT codigo FROM rh_tarifas WHERE id_tarifas = '$row[codigo]'");
								$rowCodigo = mysql_fetch_array($resultCodigo);
								$codigoProduto = $rowCodigo['codigo'];								
								
								//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 4 DIGITOS
								$CODIGO = sprintf("%04d", $codigoProduto);
								
								$quantidade = $row['quantidade'];
								//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 6 DIGITOS
								$quantidade = $quantidade*2;
								$QUANTIDADE = sprintf("%06d", $quantidade);				
								
								if(($cont % 2)==0){ 
									$backgound = 'background:#ECF2EC';
								}else{ $backgound = 'background:#DDDDFD'; }

								//EXIBINDO O CONTEÚDO QUE SERÁ GRAVADO NO ARQUIVO NA TELA
								print "<div style='font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 10px; color: #000; $backgound; height:20px; display:table-cell'> ";		
								
								print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'> $row[id_func] </div>";								
								print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'> $rowCLT[cpf] </div>";								
								print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'> $row[nome]</div>";
								print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'> $row[codigo] </div>";
								print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'> $row[itinerario] </div>";						
								print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'> $codigoProduto </div>";
								$valor = $row['valor'];
								$valor = number_format($valor,2,",",".");
								print "<div style='width:10%; float:left; display:table-cell; line-height:20px;'> $valor </div>";
								print "<div style='width:10%; float:left; display:table-cell; line-height:20px;'> $quantidade </div>";
								
								//ARMAZENA A QUANTIDADE TOTAL DE VALES PARA TODOS OS FUNCIONÁRIOS
								$arrayQuantidade[] = $quantidade;
								//VALOR DE CADA VALE MULTIPLICADO PELA QUANTIDADE, FORMANDO ASSIM O VALOR PARCIAL DE CADA VALE
								$valorVale = $row['valor'];
								$valorParcial = $valorVale * $quantidade;
								$arrayValorTotal[] = $valorParcial;
								$valorParcial = number_format($valorParcial,2,",",".");
								print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'> $valorParcial </div>";								
								print "</div>";
								
								$cont = $cont+1;
								
						$handle = fopen ("ve".$REGIAO.$MES_REFERENCIA.$ANO.".txt", "a");
						//DADOS DO FUNCIONÁRIO
						fwrite($handle, $PRODUTO, 2); //PRODUTO
						fwrite($handle, $CPF, 11); //CPF
						fwrite($handle, $CODIGO, 4); //CÓDIGO
						fwrite($handle, $QUANTIDADE, 6); //QUANTIDADE
						fwrite($handle, $VALORUNITARIO, 7); //VALOR UNITÁRIO
						fwrite($handle, "\r\n");//QUEBRA DE LINHA NO ARQUIVO TXT														
						
						$codigo[]=$row['id_func'];
						}
						$valorTotal = @array_sum($arrayValorTotal);
						print "<div style='font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 11px; color: #000; height:20px; display:table-cell' align='right; border:solid'>";
						
						print "<div style='width:90%; float:left; text-align:right; line-height:20px;'><b>TOTAL:</b></div>";					
						$valorTotal = number_format($valorTotal,2,",",".");
						print "<div style='width:8%;  float:left; text-align:right; line-height:20px;'><b>$valorTotal</b></div>";	
						
						//DEFINE QUANTOS ELEMENTOS O ARRAY POSSUY
						$contCodigo = @count($codigo);
						//RETIRA OS ELEMENTOS REPETIDOS DO ARRAY
						$codigoAUX = @array_unique($codigo);
						//CONTA QUANTOS ELEMENTOS RESTARAM NO ARRAY
						$quantCodigoAUX = @count($codigoAUX);
						//ORGANIZA OS ELEMENTOS DO ARRAY
						for($i=0; $i<$quantCodigoAUX; $i++){
							$codigoAUX2[$i] = @current($codigoAUX);
							@next($codigoAUX);
						}
						$cont = 0;
						$quantidade= 0;
						for ($i=0; $i<$quantCodigoAUX; $i++){	
							$resultTotais = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA'  AND id_func ='$codigoAUX2[$i]' AND tipo='CARTÃO' ORDER BY codigo");
						$row = mysql_fetch_array($resultTotais);
						}
						$resultCLT = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$REGIAO' AND id_clt = '$row[id_func]'");
						$rowCLT= mysql_fetch_array($resultCLT);
						$pdf->Ln(0.03);
						$pdf->SetX(3.5);
						$pdf->Cell(2, 0.5, $row['id_func'],1,'L',1);
						$pdf->Cell(3, 0.5, $rowCLT['cpf'],1,'L',1);
						$pdf->Cell(7, 0.5, $row['nome'],1,'L',1);
						
						$pdf->Cell(2, 0.5,"R$ ".$valorTotal,1,'L',1);
						$pdf->Ln();
						print "</div>";
						
						//ARMAZENA OS TOTAIS PARA QUE SEJA SOMADOS E INSERIDOS NO FIM DO ARQUIVO TEXTO
						$valor2 = str_replace(".","",$valorTotal);
						$valorTotal = str_replace(",",".",$valor2);								
						$arrayVALORTOTAL[] = $valorTotal;
						
						unset ($valorTotal);
						unset ($arrayValorTotal);
						unset ($valorParcial);								
						//echo "<br>";
				}
				echo "</div>";
				
				//VALE TIPO PAPEL
				$valorTotal03 = @array_sum($arrayValorTotal03);
				//DEFINE QUANTOS ELEMENTOS O ARRAY POSSUY
				$contCodigo03 = @count($codigo03);
				//RETIRA OS ELEMENTOS REPETIDOS DO ARRAY
				$codigoAUX03 = @array_unique($codigo03);
				//CONTA QUANTOS ELEMENTOS RESTARAM NO ARRAY
				$quantCodigoAUX03 = @count($codigoAUX03);				
				//ORGANIZA OS ELEMENTOS DO ARRAY
				for($i03=0; $i03<=$quantCodigoAUX03; $i03++){							
					$codigoAUX203[$i03] = @current($codigoAUX03);
					@next($codigoAUX03);
				}
				$cont03 = 0;
				$quantidade03= 0;
				for ($i03=0; $i03<$quantCodigoAUX03; $i03++){
					$resultTotais03 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA'  AND id_func ='$codigoAUX203[$i03]' AND tipo='PAPEL' ORDER BY codigo");
				$row03 = mysql_fetch_array($resultTotais03);
						}
				$resultCLT03 = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$REGIAO' AND id_clt = '$row03[id_func]'");
				$rowCLT03= mysql_fetch_array($resultCLT03);
					
				$pdf03->Ln(0.03);
				$pdf03->SetX(3.5);
				$pdf03->Cell(2, 0.5, $row03['id_func'],1,'L',1);
				$pdf03->Cell(3, 0.5, $rowCLT03['cpf'],1,'L',1);
				$pdf03->Cell(7, 0.5, $row03['nome'],1,'L',1);
				$valorTotal03=number_format($valorTotal03,2,",",".");		
				$pdf03->Cell(2, 0.5,"R$ ".$valorTotal03,1,'L',1);
				$pdf03->Ln();

				$PRODUTO = '99'; //VALOR FIXO PARA O SISTEMA DA VALE EXPRESS TERMINA O ARQUIVO TEXTO E INSERER OS VALORES TOTAIS E QUANTIDADES TOTAIS
				$QUANTIDADE_TOTAL = @array_sum($arrayQuantidade);
				
				$VALOR_TOTAL03 = @array_sum($arrayValorTotal03);
				$pdf03->SetY(3.1);
				$valorTotal03=number_format($VALOR_TOTAL03,2,",",".");
				$pdf03->MultiCell(19, 0.5,"Total: "."R$ ".$valorTotal03 ,0,'C');
				$pdf03->Ln();
				//FECHANDO O ARQUIVO TEXTO E GERANDO A TRILAHA DE FIM DE ARQUIVO				
				$pdf03->Output("PAPEL_ve".$REGIAO.$MES_REFERENCIA.$ANO.".pdf");
				$pdf03->Close();


				//FECHANDO O ARQUIVO TEXTO E GERANDO A TRILAHA DE FIM DE ARQUIVO
				$PRODUTO = '99'; //VALOR FIXO PARA O SISTEMA DA VALE EXPRESS TERMINA O ARQUIVO TEXTO E INSERER OS VALORES TOTAIS E QUANTIDADES TOTAIS
				$QUANTIDADE_TOTAL = @array_sum($arrayQuantidade);
				$VALOR_TOTAL = @array_sum($arrayVALORTOTAL);
				
				$pdf->SetY(3.1);
				$valorTotal=number_format($VALOR_TOTAL,2,",",".");
				$pdf->MultiCell(19, 0.5,"Total: "."R$ ".$valorTotal ,0,'C');
				$pdf->Ln();
				//FECHANDO O ARQUIVO TEXTO E GERANDO A TRILAHA DE FIM DE ARQUIVO				
				$pdf->Output("ve".$REGIAO.$MES_REFERENCIA.$ANO.".pdf");
				$pdf->Close();
				
				$PRODUTO = sprintf("%02s", $PRODUTO);
				$QUANTIDADE_TOTAL = sprintf("%06d", $QUANTIDADE_TOTAL);
				//FORMATANDO O VALOR PARA DUAS CASAS DECIMAIS SEM O PONTO "."
				$VALOR_TOTAL = number_format($VALOR_TOTAL,2,"","");
				$VALOR_TOTAL = sprintf("%010d",$VALOR_TOTAL);								
		
				$handle = fopen ("ve".$REGIAO.$MES_REFERENCIA.$ANO.".txt", "a");
				fwrite($handle, $PRODUTO, 2); //PRODUTO
				fwrite($handle, $QUANTIDADE_TOTAL, 6); //QUANTIDADE TOTAL
				fwrite($handle, $VALOR_TOTAL, 10); //VALOR TOTAL
				fclose($handle);
				
		 ?>
	</div>
</div>
</body>
</html>