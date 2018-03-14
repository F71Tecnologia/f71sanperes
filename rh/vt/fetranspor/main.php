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
<title>::FETRANSPOR::</title>
<link href="../../../net1.css" rel="stylesheet" type="text/css" />
</head>

<body class="">

<div align="center">
	<div align="center" style="width:98%; background:#FFF">
		<div align="center">
    		<?php
					include "../../../empresa.php";
					$imgCNPJ = new empresa();
					$imgCNPJ -> imagemCNPJ();
					
					//CONSTANTE PARA NOMENCLATURA DA EMPRESA FETRANSPOR
					$CONSTANTE = "PEDIDO";
					//VERSAO DO ARQUIVO PARA NOMENCLATURA DA EMPRESA FETRANSPOR
					$VERSAO = '0100';
					
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
					$data = $a.$m.$d;
					
					//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 8 DIGITOS
					$DATA = sprintf("%08d",$data);
					
					//HORA PARA NOMENCLATURA DA EMPRESA FETRANSPOR
					//$HORA = date('Hi');
					$minuto = sprintf("%03s",$REGIAO);
					$HORA = '1'.$minuto;
					$arquivo = $CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt";
					$pdf = $CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".pdf";
					$pdfPapel = 'PAPEL_'.$CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".pdf";
					//ESSA CONDIÇÃO É EXECUTADA CASO EXISTA NO SERVIDOR UM ARQUIVO COM O MESMO NOME DO QUE ESTÁ SENDO GERADO
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
                                                print "</body>";
                                                print "</html>";
						print '<br><br>';
						exit;
						
					} 
			?>  
            <div>
            	<br>
            	<span class="igreja">GERANDO PEDIDO DE RECARGA DO <b>RIO CARD</b> COM AS SEGUINTES INFORMAÇÕES</span><br><br>
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
				//REGISTRO HEADER
				
				$Nr_seq_reg = '1';
				$Nr_seq_regF = sprintf("%05s", $Nr_seq_reg);
				$Tp_registro = '01'; //TIPO DO REGISTRO: 01 - HEADER DO ARQUIVO
				$Nm_arquivo = $CONSTANTE; //CONSTANTE QUE IDENTIFICA O ARQUIVO
				$Nm_arquivo = sprintf("% -6s", $Nm_arquivo);
				$Nr_versao = '01.00'; //NUMERO DA VERSÃO DO LAYOUT DO ARQUIVO (VERSAO=02.00)
				$Nr_versao = sprintf("% -5s", $Nr_versao);
				$Nr_doc_arq = $CNPJ; //NUMERO DO CNPJ DO COMPRADOR				
				
				//GRAVANDO DADOS NO ARQUIVO TEXTO
				$handle = fopen ($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt", "a");						
				
				fwrite($handle, $Nr_seq_regF, 5);
				fwrite($handle, $Tp_registro, 2);
				fwrite($handle, $Nm_arquivo, 6);
				fwrite($handle, $Nr_versao, 5);
				fwrite($handle, $Nr_doc_arq, 14);				
				fwrite($handle, "\r\n");//QUEBRA DE LINHA

				echo "<div align='center'>";
				//BUSCANDO DADOS PESSOAIS DE CADA FUCNIOÁRIO
				
				print "<div style='font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 10px; color: #000; height:20px; display:table-cell;background:#CCC'>";										
				print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'><b>ID</b></div>";								
				print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'><b>CPF</b></div>";						
				print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'><b>NOME</b></div>";
				print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'><b>CÓDIGO</b></div>";
				print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'><b>ITINERÁRIO</b></div>";
			//	print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'><b>CÓDIGO DO PRODUTO</b></div>";
				print "<div style='width:10%; float:left; display:table-cell; line-height:20px;'><b>VALOR</b></div>";
				print "<div style='width:10%; float:left; display:table-cell; line-height:20px;'><b>QUANTIDADE</b></div>";
				print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'><b>VALOR PARCIAL</b></div>";				
				//print "</div>";

				$linha = 0;
				require_once("fpdf/fpdf.php");

				define('FPDF_FONTPATH','fpdf/font/');
				$pdf= new FPDF("P","cm","A4");
				$pdf->SetAutoPageBreak(true,1); //Reduz a tolerancia da margem inferior
				$pdf->Open();
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(5,30,'');
				$pdf->SetXY(1,1);
				
				$pdf->MultiCell(19, 0.5,"PEDIDO DE RECARGA DO CARTÃO RIO CARD",0,'C');
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
				$Nr_seq_reg02 = 1;
				for ($c=0; $c<$quant; $c++){
						$resultCLT = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$REGIAO' AND id_clt = '$arrayId_func[$c]'");
						$rowCLT= mysql_fetch_array($resultCLT);
						
						//VINCULO DA TABELA rh_clt COM A TABLELA rhempresa
						$vinculo_tb_rh_clt_e_rhempresa = $rowCLT['rh_vinculo'];

						$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
						$row_empresa = mysql_fetch_array($result_empresa);

//SELECIONA SOMENTE OS VALORES REFERENTES A CARTÃO
						$result02 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA' AND id_func = '$arrayId_func[$c]' AND tipo='CARTÃO'");
						//VALE DO TIPO PAPEL
						$result03 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_reg = '$REGIAO' AND mes= '$MES_REFERENCIA' AND id_func = '$arrayId_func[$c]' AND tipo='PAPEL'");	
						
						$cont = 0;
						while ($row03= mysql_fetch_array($result03)){
							$id_func03 = $row03['id_func'];
							$nome03 = $row03['nome'];
							$cpf03 = $rowCLT['cpf'];
							$codigo03[]=$row03['id_func'];
							$quantidade03 = $row03['quantidade'];
							$quantidade03 = $quantidade03*2;
							$valorVale03 = $row03['valor'];
							$valorParcial03 = $valorVale03 * $quantidade03;
							$arrayValorTotal03[] = $valorParcial03;	
							$codigo03[]=$row03['id_func'];
						
						$valorTotal03 = @array_sum($arrayValorTotal03);
						//if ($valorTotal != ''){							
							$pdf03->Ln(0.03);
							$pdf03->SetX(3.5);
							$pdf03->Cell(2, 0.5, $id_func03,1,'L',1);
							$pdf03->Cell(3, 0.5, $cpf03,1,'L',1);
							$pdf03->Cell(7, 0.5, $nome03,1,'L',1);
							$valorTotal03 = number_format($valorParcial03,2,",",".");
							$pdf03->Cell(2, 0.5,"R$ ".$valorTotal03,1,'L',1);						
							$pdf03->Ln();						
						//}
						}

						//VALE DO TIPO CARTÃO
						while ($row= mysql_fetch_array($result02)){	
					
								$cpf = $rowCLT['cpf'];
								$valorUnitario = $row['valor'];
								//RETIRANDO OS PONTOS E OS TRAÇOS DO CPF E VALOR
								$remover = array(".", "-", "/");
								$cpf = str_replace($remover, "", $cpf);
								//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 11 DIGITOS
								$CPF = sprintf("%011s", $cpf);
								
								$nome = $row['nome'];
								$NOME = sprintf("% -60s", $nome);
								
								//FORMATANDO O VALOR PARA DUAS CASAS DECIMAIS SEM O PONTO "."
								$VALORUNITARIO = number_format($valorUnitario,2,"","");
								$VALORUNITARIO = sprintf("%06s", $VALORUNITARIO);
																
								$quantidade = $row['quantidade'];
								//INSERE ZERO A DIRITA CASO O CONTEÚDO DA VARIÁVEL NÃO TENHA 6 DIGITOS															
								
								$quantidade = $quantidade*2;
								$QUANTIDADE = sprintf("%06d", $quantidade);				
								
								if(($cont % 2)==0){ 
									$backgound = 'background:#ECF2EC';
								}else{ $backgound = 'background:#DDDDFD'; }
								
								//EXIBINDO O CONTEÚDO QUE SERÁ GRAVADO NO ARQUIVO NA TELA
								print "<div style='font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 10px; color: #000; $backgound; height:20px; display:table-cell'> ";
								$id_func = $row['id_func'];
								print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'> $id_func </div>";
								$cpf = $rowCLT['cpf'];
								print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'> $cpf </div>";
								$nome = $row['nome'];
								print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'> $nome </div>";
								
								print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'> $row[codigo] </div>";
								print "<div style='width:20%; float:left; display:table-cell; line-height:20px;'> $row[itinerario]</div>";						
							//	print "<div style='width:12%; float:left; display:table-cell; line-height:20px;'> $codigoProduto </div>";
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
								$valorParcialF = number_format($valorParcial,2,",",".");
								print "<div style='width:5%; float:left; display:table-cell; line-height:20px;'> $valorParcialF </div>";								
								print "</div>";
																
								$cont = $cont+1;
								
								$registros = $registros + 1;
								$codigo[]=$row['id_func'];														
						}
					
						$valorTotal = @array_sum($arrayValorTotal);
						if ($valorTotal != ''){							
							$pdf->Ln(0.03);
							$pdf->SetX(3.5);
							$pdf->Cell(2, 0.5, $id_func,1,'L',1);
							$pdf->Cell(3, 0.5, $cpf,1,'L',1);
							$pdf->Cell(7, 0.5, $nome,1,'L',1);
							$valorTotal = number_format($valorTotal,2,",",".");
							$pdf->Cell(2, 0.5,"R$ ".$valorTotal,1,'L',1);						
							$pdf->Ln();						
							print "<div style='font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 11px; color: #000; height:20px; display:table-cell' align='right; border:solid'>";						
							print "<div style='width:90%; float:left; text-align:right; line-height:20px;'><b>TOTAL:</b></div>";																		
							print "<div style='width:8%;  float:left; text-align:right; line-height:20px;'><b>$valorTotal</b></div>";
						}
						print "</div>";

						//ARMAZENA OS TOTAIS PARA QUE SEJA SOMADOS E INSERIDOS NO FIM DO ARQUIVO TEXTO
						$valor2 = str_replace(".","",$valorTotal);
						$valorTotal = str_replace(",",".",$valor2);								
						$arrayVALORTOTAL[] = $valorTotal;

						$Nr_seq_reg02 = $Nr_seq_reg02+1;
						$Nr_seq_reg02 = sprintf("%05d", $Nr_seq_reg02);
						
						$Tp_registro = '02';
				
						$Nr_matricula = $arrayId_func[$c];
						$Nr_matricula = sprintf("% -15s", $Nr_matricula);

						$valorTotal = number_format($valorTotal,2,"","");
						$Vl_carga = sprintf("%08d",$valorTotal);

						$handle = fopen ($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt", "a");
						fwrite($handle, $Nr_seq_reg02, 5);
						fwrite($handle, $Tp_registro, 2);						
						fwrite($handle, $Nr_matricula, 15);
						fwrite($handle, $Vl_carga, 8);													
						fwrite($handle, "\r\n");//QUEBRA DE LINHA NO ARQUIVO TXT												
						
						unset ($valorTotal);
						unset ($arrayValorTotal);
						unset ($valorParcial);	
				}
				echo "</div>";
			/*	
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
				*/
			
				$VALOR_TOTAL03 = @array_sum($arrayValorTotal03);
				$pdf03->SetY(3.1);
				$valorTotal03=number_format($VALOR_TOTAL03,2,",",".");
				$pdf03->MultiCell(19, 0.5,"Total: "."R$ ".$valorTotal03 ,0,'C');
				$pdf03->Ln();
				
				//FECHANDO O ARQUIVO TEXTO E GERANDO A TRILAHA DE FIM DE ARQUIVO				
				$pdf03->Output("PAPEL_".$CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".pdf");
				$pdf03->Close();
				
				
				$VALOR_TOTAL = @array_sum($arrayVALORTOTAL);
				$pdf->SetY(3.1);
				$valorTotal=number_format($VALOR_TOTAL,2,",",".");
				$pdf->MultiCell(19, 0.5,"Total: "."R$ ".$valorTotal ,0,'C');
				$pdf->Ln();
				//FECHANDO O ARQUIVO TEXTO E GERANDO A TRILAHA DE FIM DE ARQUIVO				
				$pdf->Output($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".pdf");
				$pdf->Close();
				
				$PRODUTO = sprintf("%02s", $PRODUTO);
				$QUANTIDADE_TOTAL = sprintf("%06d", $QUANTIDADE_TOTAL);
				//FORMATANDO O VALOR PARA DUAS CASAS DECIMAIS SEM O PONTO "."
				$VALOR_TOTAL = number_format($VALOR_TOTAL,2,"","");
				$VALOR_TOTAL = sprintf("%010d",$VALOR_TOTAL);								

				$Nr_seq_reg03 = $Nr_seq_reg02+1;
				$Nr_seq_reg03 = sprintf("%05d",$Nr_seq_reg03);
				$Tp_registro = '99'; //TIPO DO REGISTRO: 99 - FIM DE ARQUIVO
				$Vl_pedido_total = $VALOR_TOTAL; //VALOR TOTAL DO PEDIDO
				
				$handle = fopen ($CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".txt", "a");
				
								//fwrite($handle, $Nr_seq_reg02, 5);
				fwrite($handle, $Nr_seq_reg03, 5);
				fwrite($handle, $Tp_registro, 2); //PRODUTO					
				$Qt_registros =$registros + 2;
				$Qt_registros = sprintf("%06d",$Qt_registros);
				fwrite($handle, $Vl_pedido_total, 10); //QUANTIDADE TOTAL
				fclose($handle);
				print "<script>location.href=".$CONSTANTE."_".$VERSAO."_".$CNPJ."_".$DATA."_".$HORA.".pdf"."</script>";
		 ?></div>
</div>
</body>
</html>