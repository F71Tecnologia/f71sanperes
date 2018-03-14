<?php 
include "../../../conn.php";
// Extrai o $_GET transformando-o em variavel
extract($_GET);

$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$qr_projeto = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_projeto = '$projeto'");
$qr_clt = mysql_query("SELECT id_clt,nome FROM rh_clt WHERE id_clt = '$clt'");
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
<script type="text/javascript" >
$(function(){
	$('.date').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
				});
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
<form action="../corpo_grrf.php" method="post" name="form" target="_blank" >
	<fieldset>
    	<legend>GRRF <?=@mysql_result($qr_clt,0,0) .' - '. @mysql_result($qr_clt,0,1);?></legend>
        <table>
        	<tr>
            	<td>Data do recolhimento</td>
                <td><input type="text" name="data" class='date' /></td>
            </tr>
            <tr>
            	<td>M&ecirc;s</td>
                <td><?=$mes?>/<?=$ano?>
                	<input type="hidden" value="<?=$mes?>" name="mes" />
                    <input type="hidden" value="<?=$ano?>" name="ano" />
                </td>
            </tr>
            
            <tr>
            	<td>CLt</td>
                <td><?=@mysql_result($qr_clt,0,0) .' - '. @mysql_result($qr_clt,0,1);?>
                	<input type="hidden" value="<?=@mysql_result($qr_clt,0,0)?>" name="clt" />
                </td>
            </tr>
            <tr>
            	<td>Regi&atilde;o</td>
                <td><?=@mysql_result($qr_regiao,0,0) .' - '. @mysql_result($qr_regiao,0,1);?>
                	<input type="hidden" value="<?=@mysql_result($qr_regiao,0,0)?>" name="regiao" />
                </td>
            </tr>
            <tr>
              <td>Projeto</td>
              <td><?=@mysql_result($qr_projeto,0,0) .' - '. @mysql_result($qr_projeto,0,1);?>
              <input type="hidden" value="<?=@mysql_result($qr_projeto,0,0)?>" name="projeto" /></td>
            </tr>
            <tr>
            	<td>salario anterior</td>
                <td>
                
                <?php 
				
				$mes_anterior = sprintf('%02d',$mes - 1);
				
				$qr_folha  = mysql_query("SELECT sallimpo_real, ids_movimentos FROM rh_folha_proc WHERE id_clt = '$clt' AND mes = '$mes_anterior' AND ano = '$ano' AND status = '3'");
				$row_folha = mysql_fetch_assoc($qr_folha);
				$valor     = $row_folha['sallimpo_real'];
				
				// Movimentos
				$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt
													  WHERE id_movimento IN(".$row_folha['ids_movimentos'].")");
				while($row_movimento = @mysql_fetch_array($qr_movimentos)) {
							  
					// Acrescenta os Movimentos que Incidem em FGTS
					$incidencias = explode(',', $row_movimento['incidencia']);
								  
					foreach($incidencias as $incidencia) {
					
						if($incidencia == 5023) { // FGTS
							if($row_movimento['tipo_movimento'] == 'CREDITO') {
								$valor += $row_movimento['valor_movimento'];
							} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
								$valor -= $row_movimento['valor_movimento'];
							}
						}
						
									  
					}
						  
				} 
				// Fim dos Movimentos
				
			echo "R$ ".number_format($valor,2,',','.');
				
				?>
                </td>
            </tr>
            <tr>
            	<td colspan="2" align="center"><input type="submit" value="Gerar GRRF" /></td>
            </tr>
        </table>
    </fieldset>
</form>
</div>
</body>
</html>