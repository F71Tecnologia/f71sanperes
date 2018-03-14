<?php 

include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Contratos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="corpo">
    <div id="menu" class="contrato">
       <?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">
    <h1><span>Rela&ccedil;&atilde;o de Projetos para Cadastro de Obriga&ccedil;&atilde;o</span></h1>
    <table cellspacing="0" cellpadding="4" class="relacao">
        <tr class="secao">
          <td width="209">Projeto</td>
          <td width="138">Região</td>
          <td width="69" align="center">Cadastro</td>
        </tr>
        
 <?php // Listando os Projetos
       $qr_projetos = mysql_query("SELECT * FROM projeto WHERE status_reg = '1'");
       while($projeto = mysql_fetch_assoc($qr_projetos)) {
              
           // Filtrando Projetos Ativos
           $qr_status_regiao = mysql_query("SELECT * FROM regioes WHERE id_master = '$Master' AND id_regiao = '$projeto[id_regiao]' AND status = '1'");
		   $row_regiao	     = mysql_fetch_assoc($qr_status_regiao);
           $status_regiao    = mysql_num_rows($qr_status_regiao);
         
               if(!empty($status_regiao)) { 
			   
			   	$aberto++; ?>
        
                <tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                  <td><?php echo $projeto['nome']; ?></td>
                  <td><?php echo $row_regiao['regiao']; ?></td>
                  <td align="center"><a href="cadastro.php?m=<?=$link_master?>&projeto=<?php echo $projeto['id_projeto']; ?>&aberto=<?php echo $aberto; ?>">cadastrar</a></td>
                </tr>
                
                <?php } } ?>
        
    </table>
    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('include/rodape.php'); ?>
    </div>
</div>
</body>
</html>