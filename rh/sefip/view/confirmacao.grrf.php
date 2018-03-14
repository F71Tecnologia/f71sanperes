<?php
include "../../../conn.php";

if (isset($_GET['download']) && !empty($_GET['download'])) {
    $file = $_GET['download'];
    $dirFile = '../arquivos/grrf/' . $file;
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($dirFile));
    header('Content-Disposition: attachment; filename="GRRF.re"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$dirFile", "r");
    fpassthru($fp);
    fclose($fp);
    
    exit();
}

// Extrai o $_GET transformando-o em variavel
extract($_GET);

$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$qr_projeto = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_projeto = '$projeto'");
$qr_clt = mysql_query("SELECT id_clt,nome FROM rh_clt WHERE id_clt = '$clt'");

$sql_grrf = "SELECT * FROM grrf WHERE id_clt='$clt' AND mes='$mes' AND ano='$ano' AND `status`=1";
$qr = mysql_query($sql_grrf);
$row_grrf = mysql_fetch_array($qr);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title> GRRF </title>
        <link rel="stylesheet" type="text/css" href="../../../novoFinanceiro/style/form.css"/>
        <link rel="stylesheet" type="text/css" href="../../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>
        <script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="../../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
        <script type="text/javascript" src="../../../js/jquery.price_format.2.0.min.js"></script>
        
        <script type="text/javascript" >
            $(function(){
                $('.date').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
                $('input.money').priceFormat({ prefix: '', centsSeparator: ',', thousandsSeparator: '.' });
            });
        </script>
        <style type="text/css">
            body {
                font-family:Trebuchet MS, Helvetica, sans-serif;
                font-size:11px;
                margin:0px;
            }
        </style>
    </head>
    <body>
        <div>
            <?//= $sql_grrf ?>
            <?php //var_dump($row_grrf); ?>
            <form action="../corpo_grrf.php" method="post" name="form" target="_blank" id="grrf_<?= $clt; ?>" >
                <fieldset>
                    <legend>GRRF <?= @mysql_result($qr_clt, 0, 0) . ' - ' . @mysql_result($qr_clt, 0, 1); ?></legend>
                    <table>
                        <tr>
                            <td>Valor informado pela empresa</td>
                            <?php 
                            $valor_base_informado = !empty($row_grrf) ? $row_grrf['valor_informado_empresa'] : '0,00';
                            ?>
                            <td><input type="text" name="valor_base_informado" id="valor_base_informado_<?= $clt; ?>" class="money" value="<?= $valor_base_informado; ?>" /></td>
                        </tr>
                        <tr style="display:none">
                            <td>CBO</td>
                            <td><input type="text" name="cbo"  id="cbo_<?= $clt; ?>" value="" /></td>
                        </tr>
                        <tr>
                            <td>Data do recolhimento</td>
                            <td><input type="text" name="data" id="data_<?= $clt; ?>" class='date' value="<?= date('d/m/Y'); ?>" /></td>
                        </tr>
                        <tr>
                            <td>M&ecirc;s</td>
                            <td><?= $mes ?>/<?= $ano ?>
                                <input type="hidden" value="<?= $mes ?>" name="mes" id="mes_<?= $clt; ?>" />
                                <input type="hidden" value="<?= $ano ?>" name="ano" id="ano_<?= $clt; ?>" />
                            </td>
                        </tr>

                        <tr>
                            <td>CLt</td>
                            <td><?= @mysql_result($qr_clt, 0, 0) . ' - ' . @mysql_result($qr_clt, 0, 1); ?>
                                <input type="hidden" value="<?= @mysql_result($qr_clt, 0, 0) ?>" name="clt"  id="clt_<?= $clt; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Regi&atilde;o</td>
                            <td><?= @mysql_result($qr_regiao, 0, 0) . ' - ' . @mysql_result($qr_regiao, 0, 1); ?>
                                <input type="hidden" value="<?= @mysql_result($qr_regiao, 0, 0) ?>" name="regiao" id="regiao_<?= $clt ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>Projeto</td>
                            <td><?= @mysql_result($qr_projeto, 0, 0) . ' - ' . @mysql_result($qr_projeto, 0, 1); ?>
                                <input type="hidden" value="<?= @mysql_result($qr_projeto, 0, 0) ?>" name="projeto" id="projeto_<?= $clt ?>" /></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td >
                                <?php 
                                    $checked_mes = ($row_grrf['mes_anterior_rescisao']> 0) ? ' checked="checked" ' : '';
                                ?>
                                <label><input type="checkbox" <?= $checked_mes; ?>  name="mes_anterior"  id="mes_anterior_<?= $clt ?>" />Enviar mês anterior a rescisão</label>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input type="hidden" name="saldo_anterior" value="<?= $valor; ?>" />
                                <!-- <input type="submit" value="Gerar GRRF" /> -->
                                
                                <?php
                                $sql = "SELECT A.id_recisao, A.id_clt, A.motivo, A.nome, A.vinculo_id_rescisao, IF(A.rescisao_complementar=1,1,0) AS rescisao_complementar, A.total_liquido, DATE_FORMAT(A.data_proc, '%d/%m/%Y') AS data FROM rh_recisao AS A WHERE id_clt='$clt' AND A.status=1";
                                if($_COOKIE['debug'] == 666){
                                    echo '<br>////////////////////////$sql////////////////////////<br>';
                                    echo print_r($sql);
                                }
                                $result = mysql_query($sql);
                                $rescisoes = array();
                                while($row = mysql_fetch_array($result)){
                                    ?>
                                <p>   
                                    <?php if($row['motivo'] == 60 || $row['motivo'] == 65 || $row['motivo'] == 63) { ?>
                                    Tipo de rescião nã informada na GRRF
                                    <?php } else { ?>
                                    <input type="button" id="bt_gerar_grrf_<?= $row['id_clt']; ?>" value="Gerar GRRF <?= ($row['rescisao_complementar'] == 1) ? 'COMPLEMENTAR' : null ?>" onclick="gerar_grrf(<?= $row['id_clt']; ?>,<?= $row['id_recisao']; ?>, 0);" /> 
                                    <?php } ?>
                                    <br>
                                    Rescisão #<?= $row['id_recisao']; ?> - Valor: R$ <?= number_format($row['total_liquido'],2,',','.'); ?> - de <?= $row['data']; ?>
                                </p>
                                <?php } ?>
                                <!--<input type="button" id="bt_gerar_grrf_<?//= $clt; ?>" value="Gerar GRRF" onclick="gerar_grrf(<?//= $clt; ?>);" />-->
                                
                                    <span id="resp_<?= $clt; ?>">
                                        <?php if(!empty($row_grrf)){ ?>
                                                <a href="?download=<?= $row_grrf['id_clt']; ?>_<?= $row_grrf['id']; ?>.re" style="font-size: 18px;">Baixar arquivo</a>
                                        <?php 
                                        
                                        $log->gravaLog('teste', "teste");
                                        
                                        } ?>
                                    </span>
                                <div style="display:none">
                                    <?php
                                    $incidencias = json_decode($row_grrf['incidencias']);
                                    echo '<pre>';
                                    print_r($incidencias);
                                    echo '</pre>';
                                    ?>
                                </div>
                            </td>
                            
                        </tr>
                    </table>
                </fieldset>
                
                <script>
                   function gerar_grrf(id_clt, id_rescisao, tipo){
                       if(tipo==1){
                           url = 'corpo_grrf_complementar';
                       }else{
                           url = 'corpo_grrf';
                       }
                       
                       $('#resp_'+id_clt).html('');
                       $('#bt_gerar_grrf_'+id_clt).attr('disabled','disabled');
                       $('#bt_gerar_grrf_'+id_clt).val('Gerando...');
                       data = $('#data_'+id_clt).val();
                       mes = $('#mes_'+id_clt).val();
                       ano = $('#ano_'+id_clt).val();
                       regiao = $('#regiao_'+id_clt).val();
                       projeto = $('#projeto_'+id_clt).val();
                       clt = $('#clt_'+id_clt).val();
                       valor_base_informado = $('#valor_base_informado_'+id_clt).val();
                       cbo = $('#cbo_'+id_clt).val();
                       if($('#mes_anterior_'+id_clt).is(':checked')){
                           mes_anterior = 'on';
                       }else{
                           mes_anterior = '';
                       }
                       
                       data = {data:data,mes:mes,ano:ano,regiao:regiao,projeto:projeto,clt:clt, id_rescisao: id_rescisao, valor_base_informado:valor_base_informado,cbo:cbo,mes_anterior:mes_anterior};
                       $.post("../"+url+".php", data, function(data){
                           $('#bt_gerar_grrf_'+id_clt).removeAttr('disabled');
                            $('#bt_gerar_grrf_'+id_clt).val('Gerar GRRF');
                           $('#resp_'+id_clt).html(data);
                       });
                      
                       
                   }
                </script>
            </form>
        </div>
    </body>
</html>