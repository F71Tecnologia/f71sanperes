<?php
include ("include/restricoes.php");
include('../conn.php');
include('../upload/classes.php');



$id_andamento = mysql_real_escape_string($_GET['id']);
$qr_processo2 = mysql_query("SELECT * FROM  proc_trab_andamento WHERE  andamento_id = '$id_andamento' AND andamento_status = 1");
$row_processo2 = mysql_fetch_assoc($qr_processo2);
?>
   
  <link href="../../../rh/css/estrutura_participante.css" rel="stylesheet" type="text/css"> 
   
   
   <table width="100%">
   <tr class="secao">
   	<td> Parcela</td>
    <td> Data de pagamento</td>
    <td> Valor</td>
   </tr>
   
   
   
   
     <?php  for($i=0;$i<$row_processo2['andamento_parcelas'];$i++) : 
             
                list($ano, $mes,$dia) = explode('-',$row_processo2['andamento_data_pg']);
                
                $data = mktime(0,0,0,$mes+$i,$dia,$ano);
                ?>
                <tr <?php if(($i % 2) == 0){ echo 'bgcolor="#f3f3f3"'; } else { echo 'bgcolor="#fafafa"'; }?>>
                        <td> <?php echo $i+1;?>&ordf; parcela</td>
                        <td> <?php echo date('d/m/Y',$data);?> </td>
                        <td><?php echo 'R$ '.number_format($row_processo2['andamento_valor'],2,',','.'); ?></td>
                                                                
                    </tr>
                    
                    
            <?php
            endfor;
            ?>
    </table>
                                
                                
                                
                                
                                
        