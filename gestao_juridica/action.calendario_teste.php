<?php
include ("include/restricoes.php");
include('../conn.php');




$mes = sprintf('%02s',$_GET['mes']);
$ano = $_GET['ano'];


$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario   = mysql_fetch_array($query_funcionario);

$qr_regiao = mysql_query("SELECT * FROM regioes 
						 INNER JOIN funcionario_regiao_assoc
						 ON funcionario_regiao_assoc.id_regiao = regioes.id_regiao
						 WHERE funcionario_regiao_assoc.id_master = '$row_funcionario[id_master]' 
						 AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' ");
while($row_regiao = mysql_fetch_assoc($qr_regiao)):


$regioes[] = $row_regiao['id_regiao'];


endwhile;

$regioes = implode(',', $regioes);
$total_dias = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);

$meses = array(  1 => 'Janeiro',
				 2 => 'Fevereiro',
				 3 => 'Mar&ccedil;o',
				 4 => 'Abril', 
				 5 => 'Maio', 
				 6 => 'Junho', 
				 7 => 'Julho', 
				 8 => 'Agosto', 
				 9 => 'Setembro', 
				 10 => 'Outubro', 
				 11 => 'Novembro',
				 12 => 'Dezembro');

////VERIFICANDO ANDAMENTO NO MêS
$qr_processo 		= mysql_query("SELECT  B.* ,
IF(andamento_data_movi = (SELECT MAX(andamento_data_movi) FROM proc_trab_andamento WHERE proc_id = A.proc_id),'1', '0') as ultimo_andamento,
MONTH(andamento_data_movi) as mes,
 YEAR(andamento_data_movi) as ano
FROM processos_juridicos AS A
INNER JOIN
proc_trab_andamento as B
ON A.proc_id = B.proc_id
where A.status = 1 AND andamento_status = 1 AND A.id_regiao IN($regioes) AND proc_status_id NOT IN(1,2) AND YEAR(andamento_data_movi) = $ano
ORDER BY B.andamento_data_movi DESC ") or die(mysql_error());
									


while($row_processo = mysql_fetch_assoc($qr_processo)):

                
             
		if($row_processo['ultimo_andamento'] == 1 and  $row_processo['andamento_realizado'] == 0){


                        if(($row_processo['proc_status_id'] == 8 or $row_processo['proc_status_id'] == 9 or $row_processo['proc_status_id'] == 11 or
                                $row_processo['proc_status_id'] == 12 or $row_processo['proc_status_id'] == 22 or $row_processo['proc_status_id'] == 25) )

                            {                    continue;   }


                        $dt_hoje_segundos 	   = mktime(0,0,0,date('m'), date('d'), date('Y'));	
                        $dt_andamento_segundos = explode('-',$row_processo['andamento_data_movi']);

                        $dt_vencimento 		   = mktime(0,0,0,$dt_andamento_segundos[1],$dt_andamento_segundos[2],$dt_andamento_segundos[0]);
                        $dt_andamento_segundos = mktime(0,0,0,$dt_andamento_segundos[1],$dt_andamento_segundos[2]-$dias_aviso,$dt_andamento_segundos[0]);


                        if($dt_hoje_segundos > $dt_vencimento and  $row_processo['proc_status_id'] != 1 ) {

                                $andamento_aviso_email['proc_id_expirados'][]    = $row_processo['proc_id'];	
                                $andamento_aviso_email['andamentos_expirados'][] = $row_processo['andamento_id'];		  	
                        }



                        if(($row_processo['mes'] == (int)$mes) and ($row_processo['ano'] == $ano)) {

                                        if($dt_hoje_segundos >= $dt_andamento_segundos and $dt_vencimento >= $dt_hoje_segundos and  $row_processo['proc_status_id'] != 1 ){

                                                $andamento_aviso_email['proc_id'][]   = $row_processo['proc_id'];
                                                $andamento_aviso_email['andamento'][] = $row_processo['andamento_id'];
                                        } 

                                        $andamento[$row_processo['andamento_data_movi']][] = $row_processo['andamento_id'];
                                        $realizado[$row_processo['andamento_data_movi']][]   = $row_processo['andamento_realizado'];
                        }
	
                }
endwhile;

////////VEIRIFICANDO AS NOTIFICAÇÕES
$qr_notificacoes = mysql_query("SELECT * FROM notificacoes WHERE MONTH(notificacao_data_limite) = $mes AND  notificacao_status = 1 AND notificacao_entregue = 0");
while($row_notificacao = mysql_fetch_assoc($qr_notificacoes)):

	$notificacoes[$row_notificacao['notificacao_data_limite']][] = $row_notificacao['notificacao_id'];

endwhile;




$total_dias = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);



echo '<div id="nome_mes"><h3>'.$meses[(int)$mes].' / '.$ano.'</h3></div>';	
echo '<div style="clear:left;"></div>';


echo '<div id="seta_esquerda">  </div>';	

echo '<table  class="calendario">';
echo '<tr class="nome_dias">
			<td>D</td>
			<td>S</td>
			<td>T</td>
			<td>Q</td>
			<td>Q</td>
			<td>S</td>
			<td>S</td>
		</tr>';
		
$primeiro_dia = date('w',strtotime($ano.'-'.$mes.'-01'));		
$dias 		  = 0;

for($i = 0; $i < 6; $i++){
	
	
	if($dias == $total_dias) { continue; } 	
	
	 echo '<tr class="semana">';
	 
	 		for($j=0; $j <7; $j++){
						
					if($j < $primeiro_dia and $i ==0) {
					
						echo '<td></td>';
					
					} else {
						if($dias == $total_dias) {echo '<td></td>'; continue; } 	
						$dias++;
						
						///verificando processos
						$data   		  	  = $ano.'-'.$mes.'-'.sprintf('%02s',$dias);	
						$data_segundos    	  = mktime(0,0,0,$mes, $dias, $ano);	
						$dt_hoje_segundos 	  = mktime(0,0,0,date('m'), date('d'), date('Y'));			
						
						$class = 'dia'; 
						
						
						
						if(sizeof($andamento[$data]) >0) {								
							foreach($andamento[$data] as $andamento_id){ $andamentos_input[] ='<input type="hidden" name="andamento_id[]" class="andamento_id" value="'.$andamento_id.'"/>'; }
							$class = 'aviso';
							
							
								if($data_segundos < $dt_hoje_segundos and in_array(0,$realizado[$data])){ 	$class2 = ' expirado'; 	}
							
							
							
									if(@!in_array(0,$realizado[$data])){
										
										 $class2 = 'aviso_verde';
										 
										}
						} 
								
						if(sizeof($notificacoes[$data]) >0) {								
							foreach($notificacoes[$data] as $notificacao_id){ $notificacao_input[] = '<input type="hidden" name="notificacao_id[]" class="notificacao_id" value="'.$notificacao_id.'"/>'; 	}							$class = 'aviso';
						}
						
						if($data == date('Y-m-d') ) {
							$class2 = ' hoje';
						}
						
						
						
												
						echo '<td class="'.$class.' '.$class2.'">';
							
							if(isset($andamentos_input)) { echo implode(' ', $andamentos_input) ; }
							if(isset($notificacao_input)){ echo implode(' ', $notificacao_input);  }
								
							if($class == 'aviso' or $class == 'aviso hoje' or $class == 'aviso_verde')	
							 echo '<div class="marcador"><img src="../img_menu_principal/pin.png" width="15" height="23"/></div>';	
							 echo '<input type="hidden" name="data" class="data" value="'.$data.'"/>';
							 echo $dias;
							 
							 unset($andamentos_input, $notificacao_input,$class,$class2);
						echo '</td>';		
					}
			}
			
	 echo '</tr>';
 }


?>
</table>



<div id="seta_direita">  </div>

<div id="aviso_calen"></div>

<div style="clear:left;"></div>

<input type="hidden"  id="mes_calendario" value="<?php echo (int)$mes ?>"/>
<input type="hidden"  id="ano_calendario" value="<?php echo $ano; ?>"/>
