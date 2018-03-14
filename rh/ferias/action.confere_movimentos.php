<?php 
/*
 * PHP-DOC  
 * 
 * 16-11-2015 
 * 
 * Procedimentos para listar itens de movimento
 * 
 * Versão: 1.0.4129 - 16/11/2015 - Jacques - Alterado a query de consulta ao intervalo de movimento para calculo da média de salário variável.
 * 
 * Author: Não Definido
 */

error_reporting(-1);
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
	exit;
}
include('../../conn.php');
include('../../wfunction.php');


///CALCULO DE FÉRIAS
$id_clt = $_REQUEST['id_clt'];
$regiao = $_REQUEST['regiao'];
$dt_referencia = $_REQUEST['dt_referencia'];
$aquisitivo_ano_mes_ini = $_REQUEST['aquisitivo_ano_mes_ini'];
$aquisitivo_ano_mes_end = $_REQUEST['aquisitivo_ano_mes_end'];


$sQuery = "SELECT A.* FROM rh_folha as A
            INNER JOIN rh_folha_proc as B
            ON A.id_folha = B.id_folha
            WHERE A.regiao = $regiao AND A.status=3 
            AND B.status = 3 AND A.terceiro != 1
            AND CONCAT(A.ano,LPAD(A.mes,2,'00')) BETWEEN '{$aquisitivo_ano_mes_ini}' AND '{$aquisitivo_ano_mes_end}'
            AND B.id_clt = $id_clt                               
            ORDER BY A.ano DESC, A.mes DESC;    
                ";
//
//if($_COOKIE['logado'] == 275){
//    echo $sQuery;            
//}  

$qr_folha = mysql_query($sQuery) or die(mysql_error());

$insalubridade  = 0;
$periculosidade = 0;

while($row_folha = mysql_fetch_assoc($qr_folha)){      
    
    $ids_mov = $row_folha['ids_movimentos_estatisticas'];  

    $qr_movimento  = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_movimento IN($ids_mov) AND tipo_movimento = 'CREDITO'");
    while($row_mov = mysql_fetch_assoc($qr_movimento)){
        $movimentos_ano[$row_folha['mes']] = $row_folha['ano']; 
        //print_r($row_mov);
        
        if($row_mov['id_mov'] == 56){
            $insalubridade = 157.60;
        }
        
        if($row_mov['id_mov'] == 57){
            $periculosidade = 453.23;
        }
        
        if($row_mov['id_mov'] != 56 && $row_mov['id_mov'] != 57){
            //POG para acertar a insalubridade do tipo sempre
            if($row_mov['id_mov'] == 56 AND $row_folha['ano'] == 2012 AND $row_mov['valor_movimento'] == '135.60'){
               $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += 124.40;         
               $movimentos[$row_mov['nome_movimento']]                            += 124.40;  
            } else {
               $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += $row_mov['valor_movimento'];         
               $movimentos[$row_mov['nome_movimento']]         += $row_mov['valor_movimento']; 
            }
        }
    }
}

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <title></title>
    </head>
     <body id="page-rh-trans" class="novaintra">
        <div id="content">    
            
            <h3 style="text-align: left;">MOVIMENTOS</h3>   
            
         <table width="50%"cellpadding="0" cellspacing="0" border="0" class="grid">
        <?php
        foreach($movimentos_confere as $mes => $mov){
           
            if($mesAnt != $mes){ 
                echo '<tr class="titulo" height="60"><td colspan="2">'.mesesArray($mes).' / '.$movimentos_ano[$mes].'</td></tr>';             
            }
            
            foreach($mov as $nome_mov => $valor){
                
                echo '<tr>
                        <td>'.$nome_mov.'</td>
                        <td> R$ '.number_format($valor,2,',','.').'</td>
                     </tr>';
            }
            
            
       $mesAnt = $mes;    
        }        
        ?>   
        </table>
            <table ellpadding="0" cellspacing="0" border="0" class="grid" style="margin-top:10px;">
                <tr class="titulo"> 
                    <td colspan="2">Média dos movimentos</td>
                </tr>
                <tr>
                    <td>Movimento</td>
                    <td>Valor</td>
                </tr>    
                <?php
                foreach($movimentos as $nome_mov => $valor){  
                  
                  $totalizador += ($valor/12);
                  echo '<tr>
                        <td>'.$nome_mov.'</td>
                        <td> R$ '.number_format((($valor/12)),2,',','.').'</td>
                       </tr>';
                }
                
                
                
                ?>       
                <tr>
                    <td style="text-align: right; font-weight: bold;"> TOTAL MÉDIAS:</td>
                    <td>R$ <?php echo number_format($totalizador,2,',','.')?></td>
                </tr>
                <tr>
                    <td style="text-align: right; font-weight: bold;"> INSALUBRIDADE/PERICULOSIDADE:</td>
                    <td>R$ <?php echo number_format($insalubridade + $periculosidade,2,',','.')?></td>
                </tr>
                <tr>
                    <td style="text-align: right; font-weight: bold;"> TOTAL</td>
                    <td>R$ <?php echo number_format($totalizador + $insalubridade + $periculosidade,2,',','.')?></td>
                </tr>
            </table>
        </div>
    </body>
</html>