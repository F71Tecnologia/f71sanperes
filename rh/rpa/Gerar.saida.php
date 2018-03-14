<?php 
include "../../conn.php";
$id_rpa = $_GET['ID'];

$qr_rpa = mysql_query("SELECT rh_rpa.valor, rh_rpa.irrf, rh_rpa.inss,
								rh_rpa.data,
								autonomo.id_autonomo,
								autonomo.nome 
								FROM rh_rpa INNER JOIN autonomo 
								ON rh_rpa.id_autonomo = autonomo.id_autonomo
								WHERE id_rpa = '$id_rpa'");
$row_rpa = mysql_fetch_array($qr_rpa);
$Total_liquido = $row_rpa['valor']-($row_rpa['irrf']+$row_rpa['inss']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Cadastro de saida</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../novoFinanceiro/style/form.css"/>
</head>
<body>

<form method="post" action="actions/gerar.saida.php" name="form" id="form" >
	<fieldset>
    	<legend>Gerar Saida</legend>
        <div>
        	<table align="center">
            	<tr>
            	  <td colspan="2">Recibo de pagamento Autonomo - (<?=$row_rpa['id_autonomo']?>) <?=$row_rpa['nome']?></td>
           	  </tr>
            	<tr>
            	  <td width="118" align="right">Data Vencimento: </td>
            	  <td width="102">
           	      <input name="data" type="text" disabled id="data" value="<?=implode('/',array_reverse(explode('-',$row_rpa['data'])))?>">
                  </td>
          	  	</tr>
            	<tr>
                	<td align="right">Valor:</td>
                    <td>
                    <input name="valor_real" type="text" disabled value="<?=number_format($Total_liquido,2,',','.')?>" id="valor_real">
                    <input type="hidden" name="valor" value="<?=$Total_liquido?>" />
                    <input type="hidden" name="rpa" value="<?=$id_rpa?>" />
                    </td>
                </tr>
            	<tr>
               	  	<td colspan="2" align="center">&nbsp;</td>
                </tr>
            	<tr>
            	  <td colspan="2" align="center"><input type="submit"  value="Gerar saida" class="submit-go" /></td>
          	  </tr>
           	</table>
        </div>
    </fieldset>
</form>
<div id="fileQueue"></div>
</body>
</html>