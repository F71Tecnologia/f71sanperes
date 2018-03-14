<?php
if(empty($_COOKIE['logado'])) {
print "<script>location.href = '../login.php?entre=true';</script>";
exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include_once "../../classes/LogClass.php";
include('../../classes/RescisaoClass.php');

$id_clt = mysql_real_escape_string($_GET['clt']);

/**
 * SINESIO LUIZ
 * A PEDIDO DO ITALO VOU COLOCAR A MEDIA SEMPRE 
 * PARA DIVIDIR POR 12
 * mysql_real_escape_string($_GET['m_trab'])
 * 10/01/2017
 */
$meses_trabalhados = 12;

$ano_atual = date('Y');
$rescisao = new Rescisao();


$qr_clt = mysql_query("SELECT B.salario FROM rh_clt as A
                        INNER JOIN curso as B
                        ON A.id_curso = B.id_curso
                        WHERE A.id_clt = $id_clt");
$row_clt = mysql_fetch_assoc($qr_clt);

/* $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
  FROM rh_folha as A
  INNER JOIN rh_folha_proc as B
  ON A. id_folha = B.id_folha
  WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2
  AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

  while($row_folha = mysql_fetch_assoc($qr_folha)){

  if(!empty($row_folha[ids_movimentos_estatisticas])){
  /**
 * SINÉSIO LUIZ - 01/07/2015
 * 
 */
//$qr_movimentos = mysql_query("SELECT *
//                            FROM rh_movimentos_clt
//                            WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt /*AND id_mov NOT IN(56,200,235,57,279,370) */");
//           if($_COOKIE['logado'] == 179){
//               echo "SELECT *
//                    FROM rh_movimentos_clt
//                    WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt AND id_mov NOT IN(56,200,57,235) ";
//           }
/* while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
  $movimentos[$row_mov['nome_movimento']] += $row_mov['valor_movimento'];
  }

  }
 */

//  if($_COOKIE['logado'] == 87){
/* echo '<pre>';
  print_r(array_sum($movimentos)/12);
  echo '</pre>';
 */
//echo $count_folha;
//}
// $count_folha++;
//}

$result_mov_medias = $rescisao->getMovimentosFixoParaMedia($id_clt, $meses_trabalhados);

//echo "<pre>";
//print_r($result_mov_medias['movimentos']);
//echo "</pre>";

$arrMeses = mesesArray();
$arrMeses['16'] = "Rescisão";
?>
<style>
    table{
        width: 500px;
        height: auto;
        font-size: 12px;

    }  
    .titulo{
        background-color:  #cccccc;
        text-align: center;
        font-weight: bold;

    }

    .linha_1{ background-color:  #f4f4f4;    }
    .linha_2{ background-color:   #e9e8e8;    }
</style>

<table>
    <tr class="linha_1">    
        <td>MESES TRABALHADOS:</td>
        <td colspan="2"><?php echo $meses_trabalhados; ?></td>
    </tr>

    <tr class="titulo">
        <td>COMPETENCIA</td> 
        <td>NOME</td> 
        <td>VALOR</td>
    </tr>
    <?php foreach ($result_mov_medias['movimentos'] as $mov) { $total += $mov['valor_movimento']; ?>

        <tr class="<?php echo ($i++ % 2 == 0) ? 'linha_1' : 'linha_2'; ?>">
            <td><?php echo $arrMeses[$mov['mes_mov']]; ?></td>        
            <td><?php echo $mov['nome_movimento']; ?></td>        
            <td>R$ <?php echo number_format($mov['valor_movimento'], 2, ',', '.'); ?></td>
        </tr>

    <?php } ?>
    <tr>
        <td  align="right"><strong>Total:</strong></td>
        <td>R$ <?php echo number_format($total, 2, ',', '.') ?></td>
    </tr>

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td  align="right"> <strong>Média: </strong></td>
        <td> <?php
    if (sizeof($result_mov_medias['movimentos']) > 0) {
        echo 'R$' . number_format(($total / $result_mov_medias['fator_divi']), 2, ',', '.');
        echo '    ('.number_format($total, 2, ',', '.') . ' / '. $result_mov_medias['fator_divi'] .')';
        
    } else {
        echo 'Sem movimentos para média.';
    }
    ?> <br>  

        </td>
    </tr>

</table>