<?php              
include('../conn.php');

$inicio = '2014-07-03';
$fim    = '2014-07-31';

 $dias_evento =  abs((int)floor((strtotime($inicio) - strtotime($fim)) / 86400)) ;
 
 $total = 30 - $dias_evento;
 echo $total;


$id_regiao = 45;
$tipo_contratacao = 2;

$qr_dependentes = mysql_query("SELECT A.*, B.nome as nome_clt FROM dependentes  as A 
                               INNER JOIN rh_clt as B
                               ON A.id_bolsista = B.id_clt
                               WHERE A.id_regiao = $id_regiao AND A.contratacao= $tipo_contratacao AND A.id_bolsista  = 3778") or die(mysql_error());
while($row_dep = mysql_fetch_assoc($qr_dependentes)){
    
  $REGISTRO[$row_dep['id_bolsista']]['nome'] = $row_dep['nome_clt'];  
  
  for($i=1;$i<=6;$i++){ 
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['nome']                    = $row_dep['nome'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['data']                    = $row_dep['data'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['local_nascimento']        = $row_dep['local_nasc'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['cartorio']                = $row_dep['cartorio'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['n_registro']              = $row_dep['n_registro'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['n_livro']                 = $row_dep['n_livro'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['n_folha']                 = $row_dep['n_folha'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['n_recebimento_certidao']  = $row_dep['recebimento_certidao'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['data_baixa']              = $row_dep['local_nasc'.$i];  
    $REGISTRO[$row_dep['id_bolsista']]['dep'.$i]['deficiencia']             = $row_dep['portador_def'.$i];   
  }
  
  
  
  if($row_dep['ddir_pai'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['pai']['nome']; }
  if($row_dep['ddir_mae'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['mae']['nome']; }
  if($row_dep['ddir_conjuge'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['conjuge']['nome']; }
  if($row_dep['ddir_avo_h'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['avo_h']['nome']; }
  if($row_dep['ddir_avo_m'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['avo_m']['nome']; }
  if($row_dep['ddir_bisavo_h'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['avo_bisavo_h']['nome']; }
  if($row_dep['ddir_bisavo_m'] == 1){ $REGISTRO[$row_dep['id_bolsista']]['avo_bisavo_m']['nome']; }
    
}
 
echo '<pre>';
print_R($REGISTRO);
echo '</pre>';
?> 