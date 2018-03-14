<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";

//só o mês de maio
$saida_rpas = array(84937,84936,84931,84930,84928,84927,84926,84924,84923,
84922,84919,87851,87850,87848,87845,87846,87870,87856,87855,87853,87861,87857,87869,
87938,87874,87866,87864,87872,87879,87878,87871,87880,87881,87883,87884,87885,87886,
87888,87890,87891,87892,87893,87895,87897,87899,87900,87901,87902,87903,87905,87955,
87961,87962,84935,84934,84932,84925,84921,84917,89373,89374,89375,89376,89377,89378,
89379,89380,89381,89382,89384,89385,89387,89390,89393,89396,89459,89400,89402,89406,
89408,89410,89412,89417,89418,89419,89422,89423,89424,89425,89426,89427,89428,89429,
89431,89434,89436,90372,90373,90374,90376,90378,90380,90383);


$qrSaida = "SELECT *,DATE_FORMAT(data_vencimento, '%d/%m/%Y') as data_vencimentobr FROM saida WHERE id_saida = {$_REQUEST['id_saida']}";
$resultS = mysql_query($qrSaida);
$saida = mysql_fetch_assoc($resultS);

if(in_array($_REQUEST['id_saida'], $saida_rpas)){
    $qrFiles = "SELECT A.id_pg AS id_saida_file, A.id_saida, A.tipo_pg AS tipo_saida_file FROM saida_files_pg AS A WHERE id_saida =  '{$_REQUEST['id_saida']}'";
}else{
    $qrFiles = "SELECT * FROM saida_files WHERE id_saida = '{$_REQUEST['id_saida']}'";
}

//$qrFiles = "SELECT * FROM saida_files WHERE id_saida = {$_REQUEST['id_saida']}";
$resultF = mysql_query($qrFiles);

$darf = false;
$pai = "";
if(validate($_REQUEST['darf'])){
    $darf = true;
    $pai = $saida['id_saida_pai'];
    $prestador = $saida['id_prestador'];
    $rsSaidas = montaQuery("saida","*","id_prestador={$prestador} AND darf IS NULL");
    $saidas = array();
    foreach ($rsSaidas as $rssaida){
        $saidas[$rssaida['id_saida']] = $rssaida['id_saida']." - ".$rssaida['especifica']." - R$ ".$rssaida['valor'];
    }
}

$tiposDarf = array("1708"=>"1708","5902"=>"5902");

ob_start();
?>

<div id="popup-content">
    <h2>Saída: <?php echo $_REQUEST['id_saida'] ?></h2>
    <input type="hidden" name="idsaida" id="idsaida" value="<?php echo $_REQUEST['id_saida'] ?>" />
    <fieldset>
        <legend>Dados</legend>
        <p><label class="first">Data Pagamento:</label> <?php echo $saida['data_vencimentobr'] ?></p>
        <p><label class="first">Valor Pago:</label> <?php echo $saida['valor'] ?></p>
        <p><label class="first">Descrição:</label> <?php echo $saida['especifica'] ?></p>
    </fieldset>
    
    <?php if($darf){ ?>
    <fieldset>
        <legend>Vincular DARF</legend>
        <p><label class="first">Saída:</label> <?php echo montaSelect($saidas, $pai, "id='id_saida_pai' name='id_saida_pai' style='width: 280px;'") ?></p>
        <p><label class="first">Tipo da DARF:</label> <?php echo montaSelect($tiposDarf, null, "id='tp_darf' name='tp_darf' style='width: 100px;'") ?></p>
        <p class="controls"> <input type="button" class="bt-vincular" id="vincular" value="Vincular" onclick="vincular()" /></p>
    </fieldset>
    <?php } ?>
    
    <fieldset style="padding-left: 12px;">
        <legend>Anexos</legend>
        <?php 
        while($row = mysql_fetch_assoc($resultF)){
            if(in_array($row['id_saida'], $saida_rpas)){
                $nome = $row['id_saida_file'].".".$row['id_saida']."_pg".$row['tipo_saida_file'];
            }else{
                $nome = $row['id_saida_file'].".".$row['id_saida'].$row['tipo_saida_file'];
            }
            if($saida['tipo'] != 170){
                echo "<p><a href='../../comprovantes/{$nome}' target='_blank'>{$nome}</a></p>";
            }else{
                $link = encrypt('ID='.$row['id_saida'].'&tipo=0');
                echo "<p><a href='../view/comprovantes.php?{$link}' target='_blank'>{$nome}</a></p>";
            }
        } 
        ?>
    </fieldset>
    
</div>

<?php 
$html = ob_get_contents();
ob_clean();
echo utf8_encode($html);
?>