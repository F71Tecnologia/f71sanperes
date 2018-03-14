<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {
	
include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Assegurados</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE ASSEGURADOS</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">TOTAL DE ASSEGURADOS</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?=$num_row?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
          <?php $result_apolice = mysql_query("SELECT * FROM apolice where id_regiao = '$regiao'");
                while($row_apolice = mysql_fetch_array($result_apolice)) { ?>
  <div class="descricao" style="text-align:center;">ASSEGURADOS ATIVOS</div>
  
  <?php $result_bolsista = mysql_query("SELECT *,date_format(data_nasci, '%d/%m/%Y') AS data_nasci ,date_format(data_entrada, '%d/%m/%Y') AS data_entrada FROM autonomo WHERE id_regiao ='$row_apolice[id_regiao]' AND apolice = '$row_apolice[id_apolice]' AND id_projeto = '$row_pro[0]' AND status = '1' AND tipo_contratacao = '1' ORDER BY nome");
        $num_row = mysql_num_rows($result_bolsista);
		if(!empty($num_row)) { ?>
        
  <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
     <tr class="secao_pai" align="center">
       <td colspan="5">
       <?php echo "$row_apolice[banco] - Apólice: $row_apolice[apolice]"; ?>
       </td>
     </tr>
     <tr class="secao">
       <td width="38%">Nome</td>  
       <td width="16%">Identidade</td>  
       <td width="16%">CPF</td>
       <td width="15%">Data Nascimento</td>
       <td width="15%">Data Entrada</td>
     </tr>

      <?php while($row_bolsista = mysql_fetch_array($result_bolsista)) { ?>
              
    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
       <td><?=$row_bolsista['nome']?></td>
       <td><?=$row_bolsista['rg']?></td>
       <td><?=$row_bolsista['cpf']?></td>
       <td><?=$row_bolsista['data_nasci']?></td>
       <td><?=$row_bolsista['data_entrada']?></td>
    </tr>

    <?php } ?>
    
      <tr class="secao">
        <td colspan="5" align="center">TOTAL: <?=$num_row?></td>
      </tr>
    </table>
    
    <?php } else { ?>
    
    <table class="relacao" width="100%" cellpadding="0" cellspacing="0">
      <tr class="secao">
        <td colspan="5" align="center">Nenhum Participante</td>
      </tr>
    </table>
    
    <?php } ?>
       
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <div class="descricao" style="text-align:center;">DESATIVADOS E NÃO SEGURADOS</div>
   
   <?php $result_bolsista2 = mysql_query("SELECT *,date_format(data_nasci, '%d/%m/%Y') as data_nasci ,date_format(data_saida, '%d/%m/%Y') as data_saida FROM autonomo where apolice = '$row_apolice[id_apolice]' and status = '0' and tipo_contratacao = '1' and id_projeto = '$row_pro[0]' ORDER BY nome");
         $num_row2 = mysql_num_rows($result_bolsista2);
		 if(!empty($num_row2)) { ?>
   
  <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
       <td width="38%">Nome</td>
       <td width="16%">Identidade</td>
       <td width="16%">CPF</td>
       <td width="15%">Data Nascimento</td>
       <td width="15%">Data de Saída</td>
     </tr>

     <?php while($row_bolsista2 = mysql_fetch_array($result_bolsista2)) { ?>
            
     <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td><?=$row_bolsista2['nome']?></td>
        <td><?=$row_bolsista2['rg']?></td>
        <td><?=$row_bolsista2['cpf']?></td>
        <td><?=$row_bolsista2['data_nasci']?></td>
        <td><?=$row_bolsista2['data_saida']?></td>
     </tr>

   <?php } ?>
   
      <tr class="secao">
        <td colspan="5" align="center">TOTAL: <?php echo $num_row2; ?> </td>
      </tr>
    </table>
    
    <?php } else { ?>
    
    <table class="relacao" width="100%" cellpadding="0" cellspacing="0">
      <tr class="secao">
        <td colspan="5" align="center">Nenhum Participante</td>
      </tr>
    </table>
    
   <?php } } ?> 

    </td>
  </tr>
</table>
</body>
</html>
<?php } ?>