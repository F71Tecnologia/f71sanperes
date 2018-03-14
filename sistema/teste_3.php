<?php              
include('../conn.php');
include('../classes/CalculoFolhaClass.php');
include('../wfunction.php');
$objCalcFolha = new Calculo_Folha();
$clt = 7140;
$mes = '08';
$ano = 2014;

$teste = $objCalcFolha->getMediaMovimentos($clt,$mes, $ano,14);  
echo '<pre>';
     print_r($teste);
echo '</pre>';             


foreach($teste['movimentos'] as $ano_mov => $dados){

    echo '<h4>'.$ano_mov.'</h4>';
    echo '<table border="1">';
    foreach($dados  as $mes_mov => $movimentos){ 

    echo '<tr><td colspan="4">'.mesesArray($mes_mov).'</td></tr>';

        foreach($dados[$mes_mov] as $id_movimento => $mov){
            echo '<tr>';
                 echo '<td>'.$mov['codigo'].'</td>';
                echo '<td>'.$mov['nome'].'</td>';                      
                echo '<td>'.$mov['valor'].'</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
}
 ?>
