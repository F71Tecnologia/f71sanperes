<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/CalculoFeriasClass.php');


$id_clt            = mysql_real_escape_string($_GET['clt']);
$objCalcFerias     = new Calculo_Ferias();


$qr_clt = mysql_query("SELECT B.salario FROM rh_clt as A
                        INNER JOIN curso as B
                        ON A.id_curso = B.id_curso
                        WHERE A.id_clt = $id_clt");
$row_clt = mysql_fetch_assoc($qr_clt);
   

  ///?VERIFICA OS PERIODOS DE FÉRIAS PROPORCIONAIS E VENCIDAS            
$periodosFerias = $objCalcFerias->getPeriodoFeriasRescisao($id_clt,$row_clt['data_entrada'], $row_clt['data_demi']);

  foreach ($periodosFerias['periodos_vencido'] as $periodo) {
        $faltasFeriasVencidas =  $objCalcFerias->getFaltasFeriasRescisao($id_clt, $periodo['inicio'], $periodo['fim'], 1);     
    }
  $faltasFeriasProporcionais =      $objCalcFerias->getFaltasFeriasRescisao($id_clt, $periodosFerias['periodo_proporcional']['inicio'], $periodosFerias['periodo_proporcional']['fim'], 1);     
  
?>
<table>
<tr>
    <td>NOME</td>
    <td>MES</td>
    <td>ANO</td>
    <td>VALOR </td>
    <td>QUANTIDADE </td>
</tr>
<?php
foreach($faltasFeriasVencidas['movimentos'] as $id_movimento => $mov){
    echo '<tr>';
        echo '<td>'.$mov['nome'].'</td>';
        echo '<td>'.$mov['mes'].'</td>';
        echo '<td>'.$mov['ano'].'</td>';
        echo '<td>'.number_format($mov['valor'],2,',','.').'</td>';
        echo '<td>'.$mov['quantidade'].'</td>';
    echo '</tr>';
}
?>
</table>
                         
