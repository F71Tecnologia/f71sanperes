<?php
// Iniciamos o "contador"
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
 

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/CalculoFolhaClass.php');

$objCalcFolha = new Calculo_Folha();


$id_clt = 4747;
$mes = 11;
$ano = 2014;
$salario_contratual = '';

$qr_folha = mysql_query("SELECT A.* FROM rh_folha as A 
INNER JOIN rh_folha_proc as B
ON A.id_folha  = B.id_folha
WHERE A.ano  = {$ano} AND B.id_clt  = {$id_clt} AND A.status = 3 AND B.status = 3;") or die(mysql_error());
$qntMeses =  mysql_num_rows($qr_folha);

while($folha = mysql_fetch_assoc($qr_folha)){    
    $qr_mov = mysql_query("SELECT id_movimento, id_mov, cod_movimento, nome_movimento,valor_movimento, tipo_movimento FROM rh_movimentos_clt
                            WHERE incidencia = '5020,5021,5023' AND id_clt = {$id_clt} AND tipo_movimento = 'CREDITO'
                            AND id_mov NOT IN(56,57,235)    
                            AND id_movimento IN({$folha['ids_movimentos_estatisticas']})") or die(mysql_error());
    while($row_mov = mysql_fetch_assoc($qr_mov)){
        
      $movimento[$folha['ano']][(int)$folha['mes']][$row_mov['id_movimento']]['id_mov'] = $row_mov['id_mov'];   
      $movimento[$folha['ano']][(int)$folha['mes']][$row_mov['id_movimento']]['cod'] = $row_mov['cod_movimento'];   
      $movimento[$folha['ano']][(int)$folha['mes']][$row_mov['id_movimento']]['nome'] = $row_mov['nome_movimento'];   
      $movimento[$folha['ano']][(int)$folha['mes']][$row_mov['id_movimento']]['valor'] = $row_mov['valor_movimento'];        
      
     $somatorio[$row_mov['id_mov']]['cod'] = $row_mov['cod_movimento']; 
     $somatorio[$row_mov['id_mov']]['id_mov'] = $row_mov['id_mov']; 
     $somatorio[$row_mov['id_mov']]['nome'] = $row_mov['nome_movimento']; 
     $somatorio[$row_mov['id_mov']]['valor'] += $row_mov['valor_movimento'];       
    }
}


foreach($somatorio as $id_mov => $dados){    
    $media_mensal = $dados['valor']/$qntMeses;
    $media = ($media_mensal/12) * $qntMeses;    
    
    $totalMedia[$id_mov]['cod'] = $dados['cod'];
    $totalMedia[$id_mov]['id_mov'] = $dados['id_mov'];
    $totalMedia[$id_mov]['nome'] = $dados['nome'];
    $totalMedia[$id_mov]['valor'] = $media;   
}


$teste = $objCalcFolha->getMediaMovimentos($id_clt, $mes, $ano, $qntMeses,1, 2);



echo '<pre>';
    print_r($somatorio);
    echo '<br>';
    print_r($teste['total_somatorio']);
echo '</pre>';



/*

echo '<table>';
    foreach ($movimento as $ano => $valor1){    
       foreach($valor1 as $mes => $mov){       
           echo '<tr>
                <td>'.mesesArray($mes).'</td>
               </tr>';
          foreach($mov as $id_movimento=> $valores){          
               echo '<tr>
                        <td>'.$id_movimento.'</td>
                        <td>'.$valores['cod'].'</td>
                        <td>'.$valores['nome'].'</td>
                        <td>'.$valores['valor'].'</td>
                    </tr>';          
          }
       }    
    }
echo '</table>';    
mysql_free_result($qr_mov) ;
unset($movimento,$totalMedia,$somatorio);
// Terminamos o "contador" e exibimos
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
// Exibimos uma mensagem
echo 'Elapsed time: ', $elapsed_time, ' secs. Memory usage: ', round(((memory_get_peak_usage(true) / 1024) / 1024), 2), 'Mb';*/
?>