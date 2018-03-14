<?php
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

//TRABALHADOR CLT 
$qr_duplicado_clt= mysql_query("SELECT id_clt,
											nome, 
											campo3,
											rg,
											cpf,
											pis,
											titulo,
											serie_ctps,
											COUNT(nome) as total_nome, 
											COUNT(rg) as total_rg, 
											COUNT(cpf) as total_cpf ,
										    COUNT(serie_ctps) as total_ctps,
										    COUNT(pis) as total_pis,
										    COUNT(titulo) as total_titulo
											
											FROM rh_clt
											
											WHERE     status = '10' AND id_regiao ='$regiao' AND id_projeto ='$projeto' 
											GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");
											
			$num_duplicado = mysql_num_rows($qr_duplicado_clt);


//TRABALHADOR COOPERADO 
$qr_duplicado_cooperado = mysql_query("SELECT id_autonomo,
												     nome,
													 rg,
													 pis,
													 cpf,
													 serie_ctps,
													 titulo,
													 COUNT(nome) as total_nome, 
													COUNT(rg) as total_rg, 
													COUNT(cpf) as total_cpf ,
													COUNT(serie_ctps) as total_ctps,
													COUNT(pis) as total_pis,
													COUNT(titulo) as total_titulo						
													FROM autonomo
																								
													WHERE status = '1'  AND tipo_contratacao ='3' AND id_regiao ='$regiao' AND id_projeto ='$projeto' 
													GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");
											
			$num_cooperado= mysql_num_rows($qr_duplicado_cooperado);



//TRABALHADOR AUTONOMO
$qr_duplicado_autonomo = mysql_query("SELECT id_autonomo,
												     nome,
													 rg,
													 pis,
													 cpf,
													 serie_ctps,
													 titulo,
													 COUNT(nome) as total_nome, 
													COUNT(rg) as total_rg, 
													COUNT(cpf) as total_cpf ,
													COUNT(serie_ctps) as total_ctps,
													COUNT(pis) as total_pis,
													COUNT(titulo) as total_titulo						
													FROM autonomo
																								
													WHERE status = '1'  AND tipo_contratacao ='1' AND id_regiao ='$regiao' AND id_projeto ='$projeto' 
													GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");
											
			$num_duplicado_autonomo= mysql_num_rows($qr_duplicado_autonomo);



////TRABALHADOR AUTONOMO/PJ
$qr_duplicado_pj= mysql_query("SELECT id_autonomo,
												     nome,
													 rg,
													 pis,
													 cpf,
													 serie_ctps,
													 titulo,
													 COUNT(nome) as total_nome, 
													COUNT(rg) as total_rg, 
													COUNT(cpf) as total_cpf ,
													COUNT(serie_ctps) as total_ctps,
													COUNT(pis) as total_pis,
													COUNT(titulo) as total_titulo						
													FROM autonomo
																								
													WHERE status = '1'  AND tipo_contratacao ='4' AND id_regiao ='$regiao' AND id_projeto ='$projeto' 
													GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");											
			
$num_pj = mysql_num_rows($qr_duplicado_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes do Projeto em Ordem Alfabética</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO COM CADASTRO DUPLICADO EM ORDEM ALFAB&Eacute;TICA</strong><br>
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
              <td align="center"><b><?php echo $num_duplicado+$num_cooperado+$num_duplicado_autonomo+$num_pj; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    
	<?php
	
	//////////////////// AUTONOMO   ////////////////
	 if(!empty($num_duplicado_autonomo)) { ?>
   
      <div class="descricao">Relat&oacute;rio de Autonômos com cadastro duplicado do Projeto em Ordem Alfabética</div> 
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Cod.</td>
        <td>Nome</td>
        <td>Identidade(RG)</td>
        <td>CPF</td>
        <td>CTPS</td>
        <td>Título</td>
        <td>pis</td>
        
      </tr>

	    <?php 
			
			
			while ($row_duplicado_autonomo = mysql_fetch_assoc($qr_duplicado_autonomo)):
			
			 
			
			$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
			$row_atividade2 = mysql_fetch_array($result_atividade2);
			$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
			$row_banco2 = mysql_fetch_array($result_banco2); ?>
            
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
      
        <td><?= $row_duplicado_autonomo['campo3']?></td>
        <td>
			<?php if($row_duplicado_autonomo['total_nome'] > 1) echo $row_duplicado_autonomo['nome'];?>
        </td>
        <td>
			<?php if($row_duplicado_autonomo['total_rg'] > 1) echo $row_duplicado_autonomo['rg'];?>
        </td>
        <td>
			<?php if($row_duplicado_autonomo['total_cpf'] > 1) echo $row_duplicado_autonomo['cpf'];?>
        </td>
        <td>
			<?php if($row_duplicado_autonomo['total_ctps'] > 1) echo $row_duplicado_autonomo['serie_ctps'];?>
        </td>
         <td>
			<?php if($row_duplicado_autonomo['total_titulo'] > 1) echo $row_duplicado_autonomo['titulo'];?>
        </td>
        <td>
			<?php if($row_duplicado_autonomo['total_pis'] > 1) echo $row_duplicado_autonomo['pis'];?>
        </td>        
      </tr>
	  
<?php endwhile; ?>
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE AUTÔNOMOS: <?php echo $num_duplicado_autonomo; ?></td>
      </tr>
  </table>
  
     <?php }
	 
	 
	 ///////////////// CLT  //////////
	  ?>

    </td>
  </tr>
   <tr>
     <td colspan="3">
    
    <?php if(!empty($num_duplicado)) { ?>

      <div class="descricao">Relat&oacute;rio de CLTs do Projeto com cadastro duplicado em Ordem Alfabética</div> 
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Cod.</td>
        <td>Nome</td>
        <td>Identidade(RG)</td>
        <td>CPF</td>
        <td>CTPS</td>
        <td>Título</td>
        <td>pis</td>
        
      </tr>

	    <?php 
			
			
			while ($row_duplicado_clt = mysql_fetch_assoc($qr_duplicado_clt)):
			
			 
			
			$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
			$row_atividade2 = mysql_fetch_array($result_atividade2);
			$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
			$row_banco2 = mysql_fetch_array($result_banco2); ?>
            
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
      
        <td><?= $row_duplicado_clt['campo3']?></td>
        <td>
			<?php if($row_duplicado_clt['total_nome'] > 1) echo $row_duplicado_clt['nome'];?>
        </td>
        <td>
			<?php if($row_duplicado_clt['total_rg'] > 1) echo $row_duplicado_clt['rg'];?>
        </td>
        <td>
			<?php if($row_duplicado_clt['total_cpf'] > 1) echo $row_duplicado_clt['cpf'];?>
        </td>
        <td>
			<?php if($row_duplicado_clt['total_ctps'] > 1) echo $row_duplicado_clt['serie_ctps'];?>
        </td>
         <td>
			<?php if($row_duplicado_clt['total_titulo'] > 1) echo $row_duplicado_clt['titulo'];?>
        </td>
        <td>
			<?php if($row_duplicado_clt['total_pis'] > 1) echo $row_duplicado_clt['pis'];?>
        </td>        
      </tr>
	  
<?php endwhile; ?>

       <tr class="secao">
        <td colspan="10" align="center">TOTAL DE CLTS: <?php echo $num_duplicado; ?></td>
      </tr>
</table>

<?php } ?>

  </td>
   </tr>
   <tr>
  <td colspan="3">
  
  <?php
  ///////////////   COOPERADO //////////////////
  
   if(!empty($num_cooperado)) { ?>
  
      <div class="descricao">Relat&oacute;rio de Colaboradores do Projeto com cadastro duplicadoem Ordem Alfabética</div> 
     <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Cod.</td>
        <td>Nome</td>
        <td>Identidade(RG)</td>
        <td>CPF</td>
        <td>CTPS</td>
        <td>Título</td>
        <td>pis</td>
        
      </tr>

	    <?php 
			
											
			
			while ($row_duplicado_coop = mysql_fetch_assoc($qr_duplicado_cooperado)):
			
			 
			
			$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
			$row_atividade2 = mysql_fetch_array($result_atividade2);
			$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
			$row_banco2 = mysql_fetch_array($result_banco2); ?>
            
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
      
        <td><?= $row_duplicado_coop['campo3']?></td>
        <td>
			<?php if($row_duplicado_coop['total_nome'] > 1) echo $row_duplicado_coop['nome'].'('.$row_duplicado_coop['total_nome'].')';?>
        </td>
        <td>
			<?php if($row_duplicado_coop['total_rg'] > 1) echo $row_duplicado_coop['rg'];?>
        </td> 	
        <td>
			<?php if($row_duplicado_coop['total_cpf'] > 1) echo $row_duplicado_coop['cpf'];?>
        </td>
        <td>
			<?php if($row_duplicado_coop['total_ctps'] > 1) echo $row_duplicado_coop['serie_ctps'];?>
        </td>
         <td>
			<?php if($row_duplicado_coop['total_titulo'] > 1) echo $row_duplicado_coop['titulo'];?>
        </td>
        <td>
			<?php if($row_duplicado_coop['total_pis'] > 1) echo $row_duplicado_coop['pis'];?>
        </td>        
      </tr>
	  
<?php endwhile; ?>
      
      <tr class="secao">
        <td colspan="10" align="center">TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></td>
      </tr>
   </table>
   
   <?php } ?>
   
   </td>
   </tr>
   <tr>
  <td colspan="3">
  
  <?php if(!empty($num_pj)) { ?>

      <div class="descricao">Relat&oacute;rio de Autônomo / PJ do Projeto com cadastro duplicado em Ordem Alfabética</div> 
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Cod.</td>
        <td>Nome</td>
        <td>Identidade(RG)</td>
        <td>CPF</td>
        <td>CTPS</td>
        <td>Título</td>
        <td>pis</td>
        
      </tr>

	    <?php
			while ($row_duplicado_pj = mysql_fetch_assoc($qr_duplicado_pj)):
			
			 
			
	/*		$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
			$row_atividade2 = mysql_fetch_array($result_atividade2);
			$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
			$row_banco2 = mysql_fetch_array($result_banco2);*/ ?>
            
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
      
        <td><?= $row_duplicado_pj['campo3']?></td>
        <td>
			<?php if($row_duplicado_pj['total_nome'] > 1) echo $row_duplicado_pj['nome']?>
        </td>
        <td>
			<?php if($row_duplicado_pj['total_rg'] > 1) echo $row_duplicado_pj['rg'];?>
        </td>
        <td>
			<?php if($row_duplicado_pj['total_cpf'] > 1) echo $row_duplicado_pj['cpf'];?>
        </td>
        <td>
			<?php if($row_duplicado_pj['total_ctps'] > 1) echo $row_duplicado_pj['serie_ctps'];?>
        </td>
         <td>
			<?php if($row_duplicado_pj['total_titulo'] > 1) echo $row_duplicado_pj['titulo'];?>
        </td>
        <td>
			<?php if($row_duplicado_pj['total_pis'] > 1) echo $row_duplicado_pj['pis'];?>
        </td>        
      </tr>
	  
<?php endwhile; ?>
      
      <tr class="secao">
        <td colspan="10" align="center">TOTAL DE AUTÔNOMO / PJ: <?php echo $num_pj; ?></td>
      </tr>
   </table>
   
   <?php } ?>
  </td>
  </tr>
  </table>
  </body>
</html>
<?php } ?>