<?php 
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";

$sql = str_replace("\'","'",$_POST['sql']);

$qr_relatorio = mysql_query($sql);
$row_relatorio = mysql_fetch_assoc($qr_relatorio);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Visualizar Impress&atilde;o</title>
</head>
<style type="text/css">
body {
	background-color: #EEE;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-align: center;
	margin: 0px;
	text-transform:uppercase;
}
.base {
	width: 988px;
	margin-top: 0px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
	text-align: left;
	background-color: #FFF;
	border: 1px solid #CCC;
	padding-top: 80px;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
}
.base br {
	font-style: italic;
}
.base span {
	font-style: italic;
}
h1 {
	margin: 0px;
	font-size: 16px;
	font-style: italic;
}
.base table{
	padding:3px;
	font-size:10px;
}
.base table tr.cabecalho {
	background-color:#CCC !important;
	color:#000 !important;
	text-align:left !important;
}

.base table tr:hover {
	background-color:#666;
	color:#FFF;
}


.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}

#topo {
	width: 100%;
	position: fixed;
	z-index: 100;
	height: 80px;
	top:0px;
	text-align:center;	
}
#dentro_topo{
	font-size:12px;
	text-align:left;
	width:998px;
	height:80px;
	background-color:#FFF;
	border-top-width: 1px;
	border-right-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-top-color: #CCC;
	border-right-color: #CCC;
	border-left-width: 1px;
	border-left-style: solid;
	border-left-color: #CCC;
	margin-top: 0px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
}

</style>

<body>
<div id="topo">
	<div id="dentro_topo">
    	<img src="../../imagens/logomaster<?=$row_relatorio['id_master']?>.gif" width="110" height="79">
    </div>
</div>
<div class="base">
  <table width="100%" cellpadding="0" cellspacing="1">
    <tbody>
      <tr class="cabecalho">
      	<th width="5%">ID</th>
        <th width="12%">Nome</th>
        <th width="13%">Descri&ccedil;&atilde;o</th>
        <th width="3%">Banco</th>
        <th width="3%">Ag. / CC</th>
        <th width="6%">Regiao</th>
        <th width="12%">Projeto</th>
        <th width="6%">Data</th>
        <th width="6%">Criado Por</th>
        <th width="6%">Pago Por</th>
        <th width="9%">Valor</th>
       
      </tr>
      <?php 
	  
	  do{
	 
	  ?>
      <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
        <td><?=(empty($row_relatorio['id_saida'])) ? $row_relatorio['id_entrada'] : $row_relatorio['id_saida']; ?></td>
        <td><?=$row_relatorio['nome'] ?></td>
         <td><?=$row_relatorio['especifica'] ?></td>
         <td><?=$row_relatorio['id_banco'] ?></td>
         <? $qrybanco = mysql_query("select * from bancos where id_banco = $row_relatorio[id_banco]"); 
		 $rowbanco = mysql_fetch_assoc ($qrybanco);?>
        <td><?=$rowbanco['agencia'] ?> / <?=$rowbanco['conta'] ?></td>
        <td><?=$row_relatorio['id_regiao'].' - '.$row_relatorio['regiao'] ?></td>
        <td><?=$row_relatorio['id_projeto'].' - '.$row_relatorio['nome_projeto']?></td>
        <td><?=implode('/',array_reverse(explode('-',$row_relatorio['data_vencimento'])));?></td>
                <td>
        	<?php 
			$qr_funcionario = mysql_query("SELECT id_funcionario, nome FROM funcionario WHERE id_funcionario = '$row_relatorio[id_user]'");
			$rw_funcionario = mysql_fetch_array($qr_funcionario);
			$partes_nome = explode(' ',$rw_funcionario[1]);
			echo $rw_funcionario[0]; //.' - '.$partes_nome[0].' '.$partes_nome[1]; 
			?>
        </td>
        <td>
        	<?php 
			$qr_funcionario = mysql_query("SELECT id_funcionario, nome FROM funcionario WHERE id_funcionario = '$row_relatorio[id_userpg]'");
			$rw_funcionario = mysql_fetch_array($qr_funcionario);
			$partes_nome = explode(' ',$rw_funcionario[1]);
			echo $rw_funcionario[0]; //.' - '.$partes_nome[0].' '.$partes_nome[1]; 
			?>
        </td>
        <td>
        	<?php
			$valor = (float) str_replace(',','.',$row_relatorio['valor']); 
			$adicional = (float) str_replace(',','.',$row_relatorio['adicional']); 
			$total = $valor + $adicional;
			?>
        	R$ <?=number_format($total,2,',','.')?>
        </td>
        
      </tr>
      <?php }while($row_relatorio = mysql_fetch_assoc($qr_relatorio));?>
    </tbody>
  </table>
  <br />
</div>

</body>
</html>