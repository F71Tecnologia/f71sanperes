    <?php
if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{

include "../conn.php";
include('../funcoes.php');

$link_enc = $_POST['link_enc'];
$id_saida = $_POST['saida'];

mysql_query("UPDATE saida SET impresso = 1, user_impresso = '$_COOKIE[logado]', data_impresso = NOW() WHERE id_saida = '$id_saida'");



$qr_funcionario = mysql_query("SELECT B.regiao as regiao,C.nome as nome_master,C.id_master, A.nome as nome_funcionario,
                               C.sigla,C.razao,C.endereco,C.cnpj, C.telefone
                                FROM funcionario as A
                                INNER JOIN regioes as B
                                ON A.id_regiao = B.id_regiao
                                INNER JOIN master as C
                                ON C.id_master = B.id_master
                                WHERE id_funcionario = '$_COOKIE[logado]'
                                ") or die (mysql_error());
$row_func = mysql_fetch_assoc($qr_funcionario);


$qr_saida = mysql_query("SELECT *,(CAST(REPLACE(valor, ',', '.') as decimal(13,2)) + CAST(REPLACE(adicional, ',', '.') as decimal(13,2))) as valtotal FROM saida WHERE id_saida = '$id_saida'") or die(mysql_error());
$row_saida = mysql_fetch_assoc($qr_saida);


$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_saida[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);






$qr_tipos = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row_saida[tipo]'") or die(mysql_error());
$row_tipo = mysql_fetch_assoc($qr_tipos);
$grupo    = $row_tipo['grupo'];




$array_entradasaida_nomes = array(10,20,40,50,60,70,80);
if(in_array($grupo,$array_entradasaida_nomes)){
    
 $qr_entradasaida_nome = mysql_query("SELECT * FROM  `entradaesaida_nomes` WHERE id_nome = '$row_saida[id_nome]' ORDER BY nome") ;  
 $row_entradasaida_nome = mysql_fetch_assoc($qr_entradasaida_nome);   

 $nome = $row_entradasaida_nome['nome'];
 $cpf_cnpj =  $row_entradasaida_nome['cpfcnpj'];
 $campo_tipo = 1;
 
}




//////////////////////
/////CREDOR//////////
/////////////////////
if($grupo == 30 or $row_saida['tipo'] == 32 or $row_saida['tipo'] == 132){
    
       
    if($row_saida['tipo_empresa'] == 1){
        
        $qr_prestador  = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$row_saida[id_prestador]'");
        $row_prestador = mysql_fetch_assoc($qr_prestador);   
        $nome =  $row_prestador['c_fantasia'];
        $cpf_cnpj =  $row_prestador['c_cnpj'];
        $campo_credor = 1;
        
    } elseif($row_saida['tipo_empresa'] == 2) {
        
        $qr_fornecedor  = mysql_query("SELECT * FROM fornecedores WHERE id_fornecedor = '$row_saida[id_fornecedor]'");
        $row_fornecedor = mysql_fetch_assoc($qr_fornecedor);   
        $nome           =  $row_fornecedor['razao'];
        $cpf_cnpj       =  $row_fornecedor['cnpj'];
        $campo_credor = 1;
    }
    
    
        
    
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CONTRATO DE PROCESSO</title>
<style type="text/css">
    table{
        width: 100%;
        border: collapse;    
        margin-top: 20px;
        font-size: 14px;
        border-collapse: collapse;
        
    }
    table tr{
        border: 1px solid #000;
        height: 40px;
    }
    table td{
       border: 1px solid #000;
       padding: 5px;
    }
    
    table.sem_borda{border: 0;  margin-top: 40px;}
    table.sem_borda tr{border: 0;  }
    table.sem_borda td{border: 0;  }
    
    
</style>
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="corpo">
  <div id="conteudo">   
    
<table>
    <img src="../imagens/logomaster<?php echo $row_func['id_master'];?>.gif"/>
    <h2><?php echo $row_func['razao']?></h2>
<tr>
       <td width="150"><strong>NOTA DE DÉBITO:</strong></td>
       <td width="150" align="left"><?php echo $id_saida;?> </td>
       <td width="150"><strong>DATA DE PAGAMENTO:</strong></td>
       <td width="130" align="left"><?php echo implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])));?></td>
</tr>
</table>
 
<table>   
   <tr>
       <td width="150"><strong>UNIDADE</strong></td>
       <td colspan="3" align="left"><?php echo $row_regiao['regiao']?></td>
   </tr>    
</table>
       
  <?php 
 if(!empty($campo_credor) ) {
 ?>   

            <table> 
                <tr>
                    <td width="150"><strong>CREDOR:</strong></td>
                    <td align="left"><?php echo $nome?></td>
                    <td width="170"><strong>CPF/CNPJ:</strong></td>
                    <td align="left"><?php echo $cpf_cnpj;?></td>
                </tr>   
            </table>
    
<?php }elseif(!empty ($campo_tipo)){ ?> 

                <table>  
                    <tr>
                        <td width="150" ><strong>TIPO:</strong></td>
                        <td align="left"><?php echo $nome?></td>        
                    </tr>   
                </table>
      
      <?php } ?>

 
      
<table>    
    <tr>
        <td width="150" ><strong>VALOR</strong></td>    
        <td colspan="4" align="left">R$ <?php echo number_format($row_saida['valtotal'],2,',','.'); ?></td>
    </tr>
</table>        
      
<table>    
    <tr>
        <td colspan="4" ><strong>DESCRIÇÃO DA DESPESA</strong></td>
    </tr>
    <tr height="100">
        <td colspan="4" align="left" valign="top"><?php echo $row_saida['especifica'];?></td> 
    </tr>
</table>  

<table>
    <tr>
        <td colspan="9"><strong>CLASSIFICAÇÃO DE DESPESAS</strong></td>
    </tr>
    <tr>
        <td><strong>Pessoal</strong></td>      
        <td><strong>Material de consumo</strong></td>
        <td><strong>Serviço de terceiros</strong></td>
        <td><strong>Taxas/Impostos</strong></td>
        <td><strong>Serviços Públicos</strong></td>
        <td><strong>Despesas Bancárias</strong></td>
        <td><strong>Outras Desp Operac</strong></td>
        <td><strong>Investimento</strong></td>
    </tr>
    <tr>
        <td><?php if($grupo == 10){ echo $row_tipo['cod'].' <br> '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 20){  echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 30){  echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 40){  echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 50){  echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 60){  echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 70){  echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>
        <td><?php if($grupo == 80){ echo $row_tipo['cod'].' <br>  '.$row_tipo['nome'];}  ?></td>  
    </tr>

    
    <?php
      $endereco = explode(',',$row_func['endereco']);
      $meses = array (1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");
	
        $hoje = getdate();
        $dia = $hoje["mday"];
        $mes = $hoje["mon"];
        $nomemes = $meses[$mes];
        $ano = $hoje["year"];    
      
    ?>
    
    
    
</table>
    
    <table class="sem_borda">
        <tr>
            <td width="280"></td>
            <td><strong><?php echo $endereco[3];?>, <?php echo $dia.' de '.$nomemes.' de '.$ano; ?></strong></td>
        </tr>
        <tr>
            <td colspan="2">____________________________________________________________</td>
        </tr>
        <tr>
            <td colspan="2"><strong><?php echo $row_func['razao']?></strong></td>
        </tr>
        
    </table>
    
    
    
    <table class="sem_borda">
        <tr>
            <td align="center">
                <strong>
                <?php 
                echo $endereco[0].', '.$endereco[1].', '.$endereco[2].', '.$endereco[3].', '.$endereco[4].',<br> CEP:'.formato_cep($endereco[5]);
                echo ', CNPJ: '.$row_func['cnpj']. ', Telefone: '.$row_func['telefone'];
                ?>
                </strong>
            </td>
        </tr>
        
    </table>
   
</body>

</html>

</div>
</div>
<?php

}
?>

