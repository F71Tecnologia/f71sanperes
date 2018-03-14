<?php 
// include('sintetica/cabecalho_folha.php'); 
require('../conn.php');
include('../classes/calculos.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../classes/valor_proporcional.php');
include('../funcoes.php');
include('../wfunction.php');


list($nulo,$folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

$tipo_contratacao = $_REQUEST['tp_contrato'];

switch($tipo_contratacao){
    
    case 1: $tabela = 'folha_autonomo';
      break;
      case 2: $tabela = 'folha_cooperado';
          break;
  
}


// Consulta da RegiÃ£o
$qr_folha = mysql_query("SELECT A.mes, A.ano, DATE_FORMAT(data_inicio, '%d/%m/%Y') as data_inicio_br, 
                        DATE_FORMAT(data_fim, '%d/%m/%Y') as data_fim_br,
                        DATE_FORMAT(data_proc, '%d/%m/%Y') as data_proc_br,
                        B.regiao as nome_regiao,
                        C.nome as nome_projeto,
                        C.id_master,
                        D.nome1 as gerado_por
                        FROM folhas as A
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
    $ids_folha_pro = implode(',',$_POST['id_folha_pro']);
    $array_id_aut = $_POST['id_aut'];
    $tabela = $_POST['tabela'];
    
    
       
    
    
    $qr_folha = mysql_query("SELECT id_autonomo, id_folha_pro, banco, conta, agencia,tipo_pg FROM $tabela WHERE id_folha_pro IN($ids_folha_pro) ") or die(mysql_error());
   
    
   while($row_folha = mysql_fetch_assoc($qr_folha)){ 
       
       
      
     
        $qr_autonomo = mysql_query("SELECT banco, agencia, conta,id_autonomo, tipo_pagamento FROM  autonomo WHERE id_autonomo =".$row_folha['id_autonomo']) or die(mysql_error());
        $row_autonomo = mysql_fetch_assoc($qr_autonomo);
       
        if($row_folha['banco'] != $row_autonomo['banco'] or $row_folha['conta'] != $row_autonomo['conta'] or $row_folha['agencia'] != $row_autonomo['agencia']
           or $row_autonomo['tipo_pagamento'] != $row_folha['tipo_pg']){         
         
          $update = mysql_query("UPDATE $tabela SET banco = '$row_autonomo[banco]', agencia = '$row_autonomo[agencia]', conta= '$row_autonomo[conta]' , tipo_pg = '$row_autonomo[tipo_pagamento]'
               WHERE id_folha_pro = '$row_folha[id_folha_pro]' AND id_autonomo = '$row_autonomo[id_autonomo]' LIMIT 1; ") or die(mysql_error());
        }
    
    }   
 
    //header("Location: confere_banco.php?enc=$enc");
 
} 





///// CONFERIR BANCO
$qr_banco = mysql_query("SELECT A.id_folha_pro, A.id_folha, A.id_autonomo, 
                        A.nome as nome_autonomo, 
                        A.banco as banco_folha, 
                        E.nome as nome_banco_folha,
                        A.agencia as agencia_folha,
                        A.conta as conta_folha,
                        C.tipopg as  tipo_pg_folha, 
                        
                        B.banco as banco_aut,
                        F.nome as nome_banco_aut,
                        B.agencia as agencia_aut,
                        B.conta as conta_aut,
                        D.tipopg as tipo_pg_aut                      

                       
                        FROM $tabela as A 
                        INNER JOIN autonomo as B
                        ON A.id_autonomo = B.id_autonomo
                        LEFT JOIN tipopg as C
                            ON(A.tipo_pg = C.id_tipopg )
                        LEFT JOIN tipopg as D
                            ON(B.tipo_pagamento = D.id_tipopg )
                        LEFT JOIN bancos as E
                            ON(A.banco = E.id_banco )
                        LEFT JOIN bancos as F
                            ON(B.banco = F.id_banco )
                        WHERE A.id_folha = '$folha'
                        AND (B.conta != A.conta OR B.banco != A.banco OR B.agencia != A.agencia OR B.tipo_pagamento != A.tipo_pg)
;") or die(mysql_error());


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de CLT (<?=$folha?>)</title>
<link href="../rh/folha/sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../favicon.ico" rel="shortcut icon">
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
          <img src="../imagens/logomaster<?=$row_folha_['id_master'];?>.gif" width="110" height="79">
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
        <td colspan="5" align="center" class="col_clt">AUTÔNOMO</td>
        <td colspan="5" align="center" class="col_folha">FOLHA</td>
    </tr>
    <tr class="secao2">
        <td class="col_dados">COD</td>
        <td class="col_dados">NOME</td>        
        <td class="col_clt">Tipo PG AUT</td>
        <td class="col_clt">Nº Banco AUT</td>
        <td class="col_clt">Banco AUT</td> 
        <td class="col_clt">Conta AUT</td>              
        <td class="col_clt">Agencia AUT</td>
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
            <?php echo $row['id_autonomo'];?>
            <input type="hidden" name="id_folha_pro[]" value="<?php echo $row['id_folha_pro'];?>"/>
            <input type="hidden" name="id_autonomo[]" value="<?php echo $row['id_autonomo']; ?>"/>
        </td>
        <td><?php echo $row['nome_autonomo'];?></td>        
        <td><?php echo $row['tipo_pg_aut'];?></td>
        <td><?php echo $row['banco_aut'];?></td>
        <td><?php echo $row['nome_banco_aut'];?></td>
        <td><?php echo $row['conta_aut'];?></td> 
        <td><?php echo $row['agencia_aut'];?></td> 
        <td><?php echo $row['tipo_pg_folha'];?></td> 
        <td><?php echo $row['banco_folha'];?></td>
        <td><?php echo $row['nome_banco_folha'];?></td>
        <td><?php echo $row['conta_folha'];?></td>
        <td><?php echo $row['agencia_folha'];?></td>
    </tr>  
 <?php   
}
?>
    <tr height="150">
        <td align="center" colspan="9">
            <input type="submit" name="atualizar" value="ATUALIZAR" style="height: 50px; width: 100px; "/>
            <input type="hidden" name="enc" value="<?php echo $_REQUEST['enc'];?> "/>
            <input type="hidden" name="tabela" value="<?php echo $tabela;?> "/>
        </td>
    </tr>    
</table>    

    
</div>
</form>
</body>
</html>