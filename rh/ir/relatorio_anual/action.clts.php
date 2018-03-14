<?php  
include "../../../conn.php";
$id_regiao = mysql_real_escape_string($_GET['regiao']);
$id_clt    = mysql_real_escape_string($_GET['id_clt']);
$ano 	   = mysql_real_escape_string($_GET['ano']);

echo '<table width="100%" style="font-size:12px;">';
$meses = array(01 => 'Janeiro', 02 => 'Fevereiro', 03 => 'Março', 04 => 'Abril', 05 => 'Maio', 06 => 'Junho', 07 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
$qr_folha = mysql_query("SELECT * FROM rh_folha 
                                            INNER JOIN rh_folha_proc 
                                            ON rh_folha.id_folha = rh_folha_proc.id_folha
                                            WHERE rh_folha.status = '3' AND rh_folha.regiao = '$id_regiao' AND  rh_folha.ano = '$ano' AND id_clt = '$id_clt' ORDER BY rh_folha.mes ASC") or die(mysql_error()); 
                    $numero_folha = mysql_num_rows($qr_folha);
                    					
					  echo '<tr><td></td></tr><tr><td>';
                        while($row_folha = mysql_fetch_assoc($qr_folha)):
						
					$cont++;
						$mes = (int)$row_folha['mes'];
						
						echo $meses[$mes].' ('.$row_folha['id_folha'].') R$ '.number_format($row_folha['a5021'],2,',','.').'<br>';	
						if($cont ==  4) {
							echo '</td><td>';	
						$cont = 0;} 
						
                       ?>
                             
                         
                        
                        <?php
                        endwhile;
						
						if($cont < 3) {
							echo '</td>';
						}
                    echo '</tr>';
					
		echo '</table>';
					

exit();