<?php 
// include('sintetica/cabecalho_folha.php'); 
require('../../conn.php');
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../funcoes.php');
include('../../wfunction.php');
list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));



// Consulta da RegiÃ£o
$qr_folha = mysql_query("SELECT A.mes, A.ano, DATE_FORMAT(data_inicio, '%d/%m/%Y') as data_inicio_br, 
                        DATE_FORMAT(data_fim, '%d/%m/%Y') as data_fim_br,
                        DATE_FORMAT(data_proc, '%d/%m/%Y') as data_proc_br,
                        B.regiao as nome_regiao,
                        C.nome as nome_projeto,
                        C.id_master,
                        D.nome1 as gerado_por
                        FROM rh_folha as A
                        LEFT JOIN regioes as B
                        ON A.regiao = B.id_regiao
                        LEFT JOIN projeto as C
                        ON A.projeto = C.id_projeto
                        LEFT JOIN funcionario as D
                        ON D.id_funcionario = A.user
                        WHERE id_folha = '$folha'");

$row_folha_ =  mysql_fetch_assoc($qr_folha);


if(isset($_REQUEST['atualizar'])){    
    
  
    $enc = $_POST['enc'];
    $ids_folha_proc = implode(',',$_POST['id_folha_proc']);
    $array_id_clt = $_POST['id_clt'];
    
    $qr_folha = mysql_query("SELECT id_clt, id_folha_proc, id_banco, conta, agencia,tipo_pg FROM rh_folha_proc WHERE id_folha_proc IN($ids_folha_proc)") or die(mysql_error());
    while($row_folha = mysql_fetch_assoc($qr_folha)){ 
        
        $qr_clt  = mysql_query("SELECT banco, agencia, conta,id_clt, tipo_pagamento FROM  rh_clt WHERE id_clt =".$row_folha['id_clt']) or die(mysql_error());
        $row_clt = mysql_fetch_assoc($qr_clt);
        
        if($row_folha['id_banco'] != $row_clt['banco'] or $row_folha['conta'] != $row_clt['conta'] or $row_folha['agencia'] != $row_clt['agencia']
           or $row_clt['tipo_pagamento'] != $row_folha['tipo_pg']){         
         
          $update = mysql_query("UPDATE rh_folha_proc SET id_banco = '$row_clt[banco]', agencia = '$row_clt[agencia]', conta= '$row_clt[conta]' , tipo_pg = '$row_clt[tipo_pagamento]'
               WHERE id_folha_proc = '$row_folha[id_folha_proc]' AND id_clt = '$row_clt[id_clt]' LIMIT 1; ") or die(mysql_error());
        }
    }   
    
    //header("Location: confere_banco.php?enc=$enc");
 
} 


///// CONFERIR BANCO
$qr_banco = mysql_query("select B.id_clt, A.id_folha_proc, B.nome as nome_clt , E.tipopg as tipopg_clt, C.nome as banco_clt, B.banco as id_banco_clt,B.nome_banco, B.conta as conta_clt,B.conta_dv, B.agencia as agencia_clt,B.agencia_dv, F.tipopg as tipo_pg_folha, A.id_banco as id_banco_folha, A.conta as conta_banco_folha, A.agencia as agencia_banco_folha, IF(B.conta != A.conta,'1','') as conta_diferente FROM rh_folha_proc as A LEFT JOIN rh_clt as B ON B.id_clt = A.id_clt LEFT JOIN bancos as C ON C.id_banco = B.banco LEFT JOIN bancos as D ON D.id_banco = A.id_banco LEFT JOIN tipopg as E ON E.id_tipopg = B.tipo_pagamento LEFT JOIN tipopg as F ON F.id_tipopg = A.tipo_pg WHERE A.id_folha = '$folha' AND A.id_regiao = '$regiao' AND (B.conta != A.conta OR B.banco != A.id_banco OR B.agencia != A.agencia OR B.tipo_pagamento != A.tipo_pg)") or die(mysql_error());


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de CLT (<?=$folha?>)</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon">
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
        .col_dados{ background-color: #c7d3f9; text-align: center; }
        .col_clt{ background-color:  #dcf0ee; text-align: center;}
        .col_folha{ background-color:  #f7ead7; text-align: center;}        
</style>
</head>
<body>

    
 <form name="form" action="" method="post">
<div id="corpo">
   
    <table cellspacing="4" cellpadding="0" id="topo"  > 
      <tr>
        <td width="15%" rowspan="3" valign="middle" align="center">
          <img src="../../imagens/logomaster<?=$row_folha_['id_master'];?>.gif" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
            <b><?=$row_folha_['nome_projeto'].' ('.mesesArray($row_folha_['mes']).')'?></b>
        </td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha_['data_inicio_br'].' &agrave; '.$row_folha_['data_fim_br']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.$row_folha_['nome_regiao']?></td>
        <td width="20%">&nbsp;</td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha_['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=$row_folha_['gerado_por'];?></td>
        <td><b>Folha:</b> <?=$folha?></td> 
      </tr>
    </table>
     
    <h3>Atualização de dados bancários</h3>
    

<table width="300"  id="folha"> 
    
    <tr>
        <td colspan="2" align="center" class="col_dados">DADOS</td>   
        <td colspan="7" align="center" class="col_clt">CLT</td>
        <td colspan="5" align="center" class="col_folha">FOLHA</td>
    </tr>
    <tr class="secao2">
        <td class="col_dados">COD</td>
        <td class="col_dados">NOME</td>
        <td class="col_clt">Nº Banco CLT</td>
        <td class="col_clt">Tipo PG CLT</td>
        <td class="col_clt">Banco CLT</td> 
        <td class="col_clt">Conta CLT</td>              
        <td class="col_clt">Conta DV CLT</td>              
        <td class="col_clt">Agencia CLT</td>
        <td class="col_clt">Agencia DV CLT</td>
        <td  class="col_folha">Tipo PG folha</td>
        <td  class="col_folha">NºBanco folha</td>
        <td  class="col_folha">Nome Banco folha</td>
        <td  class="col_folha">Conta Banco folha</td>
        <td  class="col_folha">Agência Banco folha</td>
    </tr>
<?php
while($row = mysql_fetch_assoc($qr_banco)){
?>    
    <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td>
            <?php echo $row['id_clt'];?>
            <input type="hidden" name="id_folha_proc[]" value="<?php echo $row['id_folha_proc'];?>"/>
            <input type="hidden" name="id_clt[]" value="<?php echo $row['id_clt']; ?>"/>
        </td>
        <td><?php echo $row['nome_clt'];?></td>
        <td><?php echo $row['id_banco_clt'];?></td>
        <td><?php echo $row['tipopg_clt'];?></td>
        <td><?php echo $row['nome_banco'];?></td>
        <td><?php echo $row['conta_clt'];?></td> 
        <td><?php echo $row['conta_dv'];?></td> 
        <td><?php echo $row['agencia_clt'];?></td> 
        <td><?php echo $row['agencia_dv'];?></td> 
        <td><?php echo $row['tipo_pg_folha'];?></td> 
        <td><?php echo $row['id_banco_folha'];?></td>
        <td><?php echo $row['nome_banco_folha'];?></td>
        <td><?php echo $row['conta_banco_folha'];?></td>
        <td><?php echo $row['agencia_banco_folha'];?></td>
    </tr>  
 <?php   
}
?>
    <tr height="150">
        <td align="center" colspan="9">
            <input type="submit" name="atualizar" value="ATUALIZAR" style="height: 50px; width: 100px; "/>
            <input type="hidden" name="enc" value="<?php echo $_REQUEST['enc'];?> "/>
        </td>
    </tr>    
</table>    

    
</div>
</form>
</body>
</html>