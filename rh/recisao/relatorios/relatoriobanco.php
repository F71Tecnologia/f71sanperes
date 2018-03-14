<?
if(empty($_COOKIE['logado'])) {
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

$result_relat = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY matricula ASC");
$num_relat = mysql_num_rows($result_relat);



?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes do Projeto por Idade</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO POR IDADE</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?php echo $num_clt+$num_cooperado+$num_autonomo+$num_pj; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
       <?php if(!empty($num_relat)) { ?>
 
      <div class="descricao">Relatório de Autonômos do Projeto por Idade</div>
      
   <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td>TIPO</td>
			<td>MATRICULA</td>
			<td>CPF</td>
			<td>FILIAL</td>
			<td>CONTROLE</td>
			<td>CAPACIDADE CIVIL</td>
			<td>TIPO MOVIMENTO</td>
			<td>NOME FUNCIONARIO</td>
			<td>ENDERECO RESIDENCIAL</td>
			<td>NUMERO</td>
			<td>COMPLEMENTO</td>
			<td>BAIRRO</td>
			<td>CEP</td>
			<td>SUFIXO</td>
			<td>FONE(DDD)</td>
			<td>FONE NUMERO</td>
			<td>FAX(DDD)</td>
			<td>FAX NUMERO</td>
			<td>ENDEREÇO PARA CORRESPONDÊNCIA</td>
			<td>NUMERO DO ENDERECO</td>
			<td>COMPLEMENTO</td>
			<td>BAIRRO</td>
			<td>CEP</td>
			<td>SUFIXO</td>
			<td>CODIGO DE OCUPACAO</td>
			<td>DATA DE NASCIMENTO</td>
			<td>NATURALIDADE</td>
            
            <td>UF DE NASCIMENTO</td>
			<td>SEXO</td>
			<td>NOME DO PAI</td>
			<td>NOME DA MÃE</td>
			<td>BRASILEIRO/ESTRANGEIRO</td>
			<td>NACIONALIDADE</td>
			<td>ESTADO CIVIL</td>
			<td>TIPO DOCUMENTO</td>
			<td>NÚMERO DOCUMENTO</td>
			<td>DATA EMISSÃO</td>
			<td>ORGÃO EMISSOR</td>
			<td>NOME EMPRESA</td>
			<td>CARGO</td>
			<td>RENDA</td>
			<td>TEMPO DE SERVIÇO</td>
			<td>ENDERECO DA EMPRESA</td>
			<td>NUMERO CEP</td>
			<td>SUFIXO CEP</td>
			<td>NOME CONJUGÊ</td>

        </tr>
		<?php while($dados_relat = mysql_fetch_assoc($result_relat)) {
			
			
			$sql2 = "SELECT * FROM curso WHERE id_curso=".$dados_relat['id_curso'];
			$qry2 = mysql_query($sql2);
			$dados2 = mysql_fetch_assoc($qry2);		
			
			
			$cpf1 = str_replace(".","",$dados_relat['cpf']);
			$cpfcerto = str_replace("-","",$cpf1);
			
			
			 ?>
        
        
        
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td>1</td>
		   <td><?=$dados_relat['matricula']?></td>
		   <td><?=substr($cpfcerto, 0, 9);?></td>
		   <td>0000</td>
		   <td><?=substr($cpfcerto, 9, 2);?></td>
		   <td>1</td>
           <td>1</td>
           <td><?=$dados_relat['nome']?></td>
           <td><?=$dados_relat['endereco']?></td>
		   <td><?=$dados_relat['numero']?></td>
		   <td><?=$dados_relat['complemento']?></td>
		   <td><?=$dados_relat['bairro']?></td>
		   <td><?=substr($dados_relat['cep'], 0, 5);?></td>
		   <td><?=substr($dados_relat['cep'], 6, 8);?></td>
		   <td><?=substr($dados_relat['tel_fixo'], 1, 2);?></td>
		   <td><?=substr($dados_relat['tel_fixo'], 4, 4);?><?=substr($dados_relat['tel_fixo'], 9, 4);?></td>
           <td>00000000</td>
           <td>00000000</td>
           <td><?=$dados_relat['endereco']?></td>
		   <td><?=$dados_relat['numero']?></td>
           <td><?=$dados_relat['complemento']?></td>
		   <td><?=$dados_relat['bairro']?></td>
		   <td><?=substr($dados_relat['cep'], 0, 5);?></td>
		   <td><?=substr($dados_relat['cep'], 6, 8);?></td>
		   <td>CBO</td>
           <td><?=substr($dados_relat['data_nasci'], 8, 2);?><?=substr($dados_relat['data_nasci'], 5, 2);?><?=substr($dados_relat['data_nasci'], 0, 4);?></td>
           <td><?=$dados_relat['naturalidade']?></td>
           
           
           <td><?=$dados_relat['uf_rg']?></td>
           <td><? if ($dados_relat['sexo'] == M){echo "1";}else{echo "2";} ?></td>
		   <td><?=$dados_relat['pai']?></td>
		   <td><?=$dados_relat['mae']?></td>
           <td>1</td>
           <td>1</td>

		   <td><?=$dados_relat['civil']?></td>
           <td>RG</td>
           <td><?=$dados_relat['rg']?></td>
           <td><?=substr($dados_relat['data_rg'], 8, 2);?><?=substr($dados_relat['data_rg'], 5, 2);?><?=substr($dados_relat['data_rg'], 0, 4);?></td>           
           <td><?=$dados_relat['orgao']?></td>
           <td>INSTITUTO LAGOS RIO</td>
           <td><?=$dados2['nome']?></td>
           <td><?=$dados2['salario']?></td>
		   <td>0001</td>
           <td>Rua Tenente Braulio, 51 – Mutondo – São Gonçalo</td>
           <td>24450</td>
           <td>240</td>
           <td> </td>
                               
           
     	    </tr>
        <?php 
		 } 
		 }
		 }?>
        
 
</table>
</body>
</html>
