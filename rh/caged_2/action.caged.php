<?php
include "../../conn.php";
include "../../funcoes.php";
include("../../wfunction.php");


$mes = $_POST['mes'];
$ano = $_POST['ano'];
  

$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$_SESSION[id_master]'");
while($row_regioes = mysql_fetch_assoc($qr_regioes)):

$regioes[] = $row_regioes['id_regiao'];
endwhile;
$regioes = implode(',' , $regioes);


$tipo_admissao = array(
    10 => "Primeiro emprego",
    20 => "Reemprego",
    25 => "Contrato por prazo determinado",
    35 => "Reintegra&ccedil;&atilde;o",
    70 => "Transferência da entrada"
);



$qr_trabalhadores = mysql_query("SELECT *,   IF( MONTH(data_demi) = '$mes' AND YEAR(data_demi) = '$ano','DEMITIDO(S)','') as movimento
                                FROM rh_clt
                                WHERE YEAR(data_demi) = '2012' AND MONTH(data_demi) = '$mes' AND STATUS IN('60','61','62','81','100','80','63') AND id_regiao IN($regioes)

                                UNION

                                SELECT *, IF( MONTH(data_entrada) = '$mes' AND YEAR(data_entrada) = '$ano','ADMITIDO(S)','') as movimento
                                FROM rh_clt
                                WHERE YEAR(data_entrada) = '$ano' AND MONTH(data_entrada) = '$mes' AND (STATUS != '60' OR STATUS != '61' OR STATUS != '62' OR STATUS != '81' OR STATUS != '100' OR STATUS != '80' OR STATUS != '63') AND id_regiao IN($regioes)
                                ORDER BY  movimento,nome ASC") or die(mysql_error());

if(mysql_num_rows($qr_trabalhadores) == 0){
    
    echo '<h3>Nenhum trabalhador encontrado!</h3>';
    exit;
}

echo '<table>';
    echo '<thead>';
        echo '<tr>';
            echo '<td>'.  htmlentities(mesesArray($mes)).' / '.$ano.'</td>';
        
        echo '</tr>';        
    echo '</thead>';

while($row_trab = mysql_fetch_assoc($qr_trabalhadores)){
    

$qr_tipodemi = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$row_trab[status]';");
 $tipo_demi = mysql_result($qr_tipodemi,0);                               
			
				
    
    
    
    
  
     
    if($row_trab['movimento'] != $movimentoAnt){
        echo '<tr><td colspan="6">'.$row_trab['movimento'].'</td></tr>';
        $movimentoAnt = $row_trab['movimento'];
    }
    
    echo '<tr>';
    echo '<td>'.$row_trab['id_clt'].'</td>';
    echo '<td>'.htmlentities($row_trab['nome']).'</td>';
    echo '<td>'.$row_trab['nome_regiao'].'</td>';
    echo '<td>'.$row_trab['nome_projeto'].'</td>';
    echo '<td>'.(in_array($row_trab['status_admi'], $tipo_admissao))? $tipo_admissao[$row_trab['status_admi']].'</td>' : $tipo_demi.'</td>';
    echo '<td>'.$row_trab['data_admissao'].'</td>';
    echo '</tr>';
    
    
}

echo '</table>';
?>
