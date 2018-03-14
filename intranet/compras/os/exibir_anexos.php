<?php
include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');

include('../../funcoes.php');
include('../../adm/include/criptografia.php');


$id_compra = $_REQUEST['compra'];
$id_anexo = $_REQUEST['id'];
$tipo = $_REQUEST['tp'];

$tabela = 'anexo_compra';
?>



<div id="paginacao">
    <?php
    $prox_pag = 0;
    $i = 0;

    ////ANEXO ANTERIOR
    $id_pg_anterior = @mysql_result(mysql_query("SELECT anexo_id FROM $tabela WHERE id_compra = '$id_compra'   AND  anexo_status = 1 AND anexo_id < '$id_anexo'  AND fornecedor='$tipo' ORDER BY anexo_ordem DESC;"), 0);

    if ($id_pg_anterior != 0) {
        echo '<a href="exibir_anexos.php?id=' . $id_pg_anterior . '&tp=' . $tipo . '&compra=' . $id_compra . '" class="pg"> << Anterior  </a>';
    }
    ///////////////////	
    ///PEGANDO  O TOTAL DE ANEXOS
    $qr_anexos = mysql_query("SELECT * FROM $tabela WHERE id_compra='$id_compra'  AND fornecedor='$tipo' AND anexo_status='1' ");
    while ($row_anexo = mysql_fetch_assoc($qr_anexos)):

        $i++;



        if ($prox_pg == 1) {
            $proximo = '<a href="exibir_anexos.php?id=' . $row_anexo['anexo_id'] . '&tp=' . $tipo . '&compra=' . $id_compra . '" class="pg"> Próximo >> </a>';
            $prox_pg = 0;
        }



        if ($id_anexo == $row_anexo['anexo_id']) {
            $prox_pg = 1;
            $color_num_pagina = 'color:#F7F7F7; font-size:22px;';
            $imagem = '<img  width="672" height="950" src="anexo_compras/' . $row_anexo['anexo_id'] . '.' . $row_anexo['anexo_extensao'] . '"/>';
        } else {
            $color_num_pagina = '';
        }



        echo '<a href="exibir_anexos.php?id=' . $row_anexo['anexo_id'] . '&tp=' . $tipo . '&compra=' . $id_compra . '" class="pg"> <span style="' . $color_num_pagina . '">' . $i . '</span></a>';


    endwhile;

    echo $proximo;
    ?>

    <table align="center">
        <tr>
            <td align="center">
                <?php echo $imagem; ?>
            </td>
        </tr>
    </table>
    