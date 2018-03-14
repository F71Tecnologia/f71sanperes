<?php

include_once('../../../../conn.php');

include_once('../../../../wfunction.php');

include_once("../../../classes/LogClass.php");

$log = new Log();



if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'editar_valor_saida'){

    $valor = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor']));

    $valor_antigo = mysql_result(mysql_query("SELECT valor FROM saida WHERE  id_saida = {$_REQUEST['id_saida']} LIMIT 1;"),0);



    $sql = "UPDATE saida SET valor = $valor WHERE id_saida = {$_REQUEST['id_saida']} LIMIT 1;";

    mysql_query($sql) OR die(mysql_error());

    $log->gravaLog('Saida', 'Edição da Saida: '.$id_saida . " - valor de {$valor_antigo} para {$valor} (PRESTADOR DE SERVIÇO)");

    exit;

}



if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_saida'){

    $sql = "UPDATE saida SET status = 0 WHERE id_saida = {$_REQUEST['id_saida']} LIMIT 1;";

    mysql_query($sql) OR die(mysql_error());

    $log->gravaLog('Saida', "Exclusão da Saida: {$_REQUEST['id_saida']} (PRESTADOR DE SERVIÇO)");

    exit;

}



//print_array($arrayBancos);

?>

<fieldset>

    <legend>Pagamentos</legend>

    <p>

        <label class="first" >Projeto</label>

        <?= $prestador['nome_projeto']; ?>

    </p>

    <p>

        <label class="first" >Prestador</label>

        <?= $prestador['nome_fantasia']; ?>

    </p>

</fieldset>

<br><br>

<div id="din_<?= $key; ?>">

    <?php include_once 'table_'.$key.'.php'; ?>

</div>

