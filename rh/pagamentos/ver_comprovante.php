<?php 
include "../../conn.php";
include "../../funcoes.php";


$mes = $_GET['mes'];
$ano = $_GET['ano'];
$clt = $_GET['id_clt'];
$tipo = $_GET['tipo']; // 1 - FÉRIAS, 2 - RECISÂO

$projeto = $_GET['projeto'];
$regiao = $_GET['regiao'];
$ferias = $_GET['ferias'];


$query_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($query_projeto);
$query_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$projeto'");
$row_regiao = mysql_fetch_assoc($query_regiao);
$query_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_assoc($query_clt);
$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'");
$mes_nome = @mysql_result($query_mes,0);




//LISTANDO SAÍDAS 

$query_saida = mysql_query("SELECT PG.id_saida,B.nome, DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento, IF(B.data_pg = NULL,'',DATE_FORMAT(B.data_pg, '%d/%m/%Y' )) as data_pg, B.valor,
C.nome as nome_banco, C.conta, C.agencia,B.status
FROM pagamentos_especifico AS PG
INNER JOIN saida as B 
 ON PG.id_saida = B.id_saida
 INNER JOIN bancos as C
 ON C.id_banco = B.id_banco
WHERE B.status != '0' AND PG.mes = '$mes' AND PG.ano = '$ano' AND PG.id_clt = '$clt' AND (B.tipo = '51' OR B.tipo = '170')") or die(mysql_error());





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Detalhes da Rescis&atilde;o</title>

<link rel="stylesheet" type="text/css" href="../../novoFinanceiro/style/form.css"/>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<style type="text/css">
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
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

.tabela{
    width: 800px;
    margin: 10px;
    
}

.campos{
    background-color:    #c8c8c8;
    text-align: center;
}

.titulo{
    background-color:  #c9c9c9;
    width:100px;
    padding:3px;
    text-align: center;
    margin:10px;
}

</style>
</head>
<body>
    <h2>RESCISÃO - <?php echo $row_clt['nome'];?> </h2>
    
     <table class="tabela">
         <tr class="campos">
             <td>COD. da Saída</td>
             <td>Nome</td>
             <td>Data de vencimento</td>
             <td>Data de PG</td>
             <td>VAlor</td>
             <td>Ver</td>
         </tr>  
         
         
  <?php  while($row_saida = mysql_fetch_assoc($query_saida)){
      
      $qr_rescisao = mysql_query("SELECT rh_recisao.id_regiao,rh_recisao.id_clt, rh_recisao.id_recisao	 
                                    FROM (saida
                                    INNER JOIN pagamentos_especifico ON saida.id_saida = pagamentos_especifico.id_saida) 
                                    INNER JOIN  rh_recisao ON rh_recisao.id_clt = pagamentos_especifico.id_clt  
                                    WHERE saida.id_saida =  '$row_saida[id_saida]' AND rh_recisao.status = '1' ");
   
            $row_recisao = mysql_fetch_array($qr_rescisao);
            $link = str_replace('+','--',encrypt("$row_recisao[0]&$row_recisao[1]&$row_recisao[2]"));
            $link = "http://".$_SERVER['HTTP_HOST']."/intranet/rh/recisao/nova_rescisao_2.php?enc=".$link; 
            
            
     $query_anexo2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_saida[id_saida]'");

            
      ?>
         <tr class="linha_um">
             <td align="center"><?php echo $row_saida['id_saida'];?></td>        
             <td><?php echo $row_saida['nome'];?></td>
             <td align="center"><?php echo $row_saida['data_vencimento'];?></td>
             <td align="center"><?php echo $row_saida['data_pg'];?></td>
             <td align="center">R$ <?php echo $row_saida['valor'];?></td>        
            <td align="center"><a href="<?php echo $link;?>" target="_blank">rescisão</a></td>
         </tr> 
<?php
  } ?>
    </table>    
    
</body>
</html>