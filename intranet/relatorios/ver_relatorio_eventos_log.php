<?php
include("../conn.php");
include("../wfunction.php");
include("../classes/EventoClass.php");
include("../classes/CltClass.php");
include "../classes/LogClass.php";
$log = new Log();

$clt = $_REQUEST['id'];


$query = mysql_query("SELECT B.nome_status_de, B.nome_status_para, B.obs_de,obs_para,A.nome,
DATE_FORMAT(B.data_de,'%d/%m/%Y') AS data_de, 
DATE_FORMAT(B.data_retorno_de, '%d/%m/%Y') AS data_retorno_de, 
DATE_FORMAT(B.data_para,'%d/%m/%Y') AS data_para, 
DATE_FORMAT(B.data_retorno_para, '%d/%m/%Y') AS data_retorno_para
FROM rh_eventos_log AS B
LEFT JOIN rh_clt AS A ON (A.id_clt = B.id_clt)
WHERE B.id_evento_log = '$clt'");

echo "<!-- $sql -->";

$row_log = mysql_fetch_array($query);



?>

<input type="hidden" name="cod_clt" id="cod_clt" value="<?php echo $clt; ?>"/>

<div class="panel panel-default">
    <div class="panel-heading"><strong><?php echo $row_log['nome']; ?></strong></div>
    <div class="panel-body">        
        <div class="eventos_log">
            <?php for($resp = 0; $resp < 1; $resp++){ ?>              

            <table class="table table-striped table-bordered table-hover text-sm valign-middle">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">Status</th> 
                        <th colspan="2" class="text-center">Obs </th>
                        <th colspan="2" class="text-center">Data</th>
                        <th colspan="2" class="text-center">Data Retorno</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td>De:</td>
                        <td>Para:</td>
                        
                        <td>De:</td>
                        <td>Para:</td>
                        
                        <td>De:</td>
                        <td>Para:</td>
                        
                        <td>De:</td>
                        <td>Para:</td>
                    </tr>
                    <tr class="text-center">
                        <td><?php echo $row_log['nome_status_de'];?></td>
                        <td><?php echo $row_log['nome_status_para'];?></td>
                        <td><?php echo $row_log['obs_de'];?></td>
                        <td><?php echo $row_log['obs_para'];?></td>
                        <td><?php echo $row_log['data_de'];?></td>
                        <td><?php echo $row_log['data_para'];?></td>
                        <td><?php echo $row_log['data_retorno_de'];?></td>
                        <td><?php echo $row_log['data_retorno_para'];?></td>
                    </tr>
                </tbody>
            </table>
            <?php } ?>
           
        </div>
    </div>
</div>