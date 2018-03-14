<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/formato_data.php');
include('../include/criptografia.php');


$acesso_excluir = array(9,5,257);
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Parceiros</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 

<script>
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';

</script>



</head>
<body>
<div id="corpo"> 
    <div id="menu" class="parceiro"> 
       <?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo" style="text-transform:uppercase;">
    <h1 class="titulo">Total de Parceiros</h1>
    <table width="96%" cellpadding="4" cellspacing="1" class="relacao">
       <tr class="secao">
           <td width="27%">&nbsp; </td>
           <td width="31%">Nome</td>
           <td width="10%">Editar</td>
		   
           <?php 
		  if(in_array($_COOKIE['logado'],$acesso_excluir)) {
		   ?>
           <td width="11%">Excluir</td>
           <?php
		  }
		   ?>
           
		   <td width="21%">Última Edição</td>
       </tr>
       <?php // Listando os Parceiros
             $qr_parceiros = mysql_query("SELECT * FROM parceiros INNER JOIN regioes ON parceiros.id_regiao =  regioes.id_regiao WHERE parceiros.parceiro_status = '1' AND  regioes.id_master = '$Master' ORDER BY parceiro_id ");
             while($row_parceiro = mysql_fetch_assoc($qr_parceiros)) { 
			 if($row_parceiro['id_regiao']=='15' or $row_parceiro['id_regiao']=='36' or $row_parceiro['id_regiao']=='37' ) continue;   // condição para não mostrar as regiões 15,36,37
			 
			 ?>
             <tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                 <td width="27%"><img src="<?php echo 'logo/'.$row_parceiro['parceiro_logo']; ?>" width="100" height="100" id="logomarca" /></td>
                 <td width="31%"><?php echo $row_parceiro['parceiro_nome']; ?></td>
                 <td width="10%"><a  href="edicao.php?id=<?php echo $row_parceiro['parceiro_id']; ?>&m=<?php echo $_GET['m']; ?>"> <img src="../../imagens/editar_projeto.png"/></a></td>
               
               	  <?php 
				  if(in_array($_COOKIE['logado'],$acesso_excluir)) {
				   ?>
               
                 <td width="11%"><a   href="exclusao.php?id=<?php echo $row_parceiro['parceiro_id']; ?>&m=<?php echo $_GET['m']; ?>" 
				 
				 onclick="return window.confirm('Deseja excluir o parceiro: <?php echo $row_parceiro['parceiro_nome']; ?>?');"> <img src="../../imagens/lixo.gif" width="25" heigth="25"/>
                 </a></td>
				 <?php } ?>
                 
                 
				 <td>
				 
				 
				 <?php 
				 $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_parceiro[parceiro_id_atualizacao]'");
				 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
				$data=date('d/m/Y',strtotime($row_parceiro['parceiro_atualizacao']));
				
				$nome=explode(' ',$row_funcionario['nome']);
				 
				 if($row_parceiro['parceiro_atualizacao']!='0000-00-00 00:00:00'){
					 
				 echo 'Editado por: '.$nome[0].' em '.$data; 
			 }else {
							    
                          $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_parceiro[parceiro_autor]'");
                          $row_funcionario=mysql_fetch_assoc($qr_funcionario);
                          $nome=explode(' ',$row_funcionario['nome']);
						  
                          
							  echo 'Cadastrado por: '.$nome[0].' em '.date('d/m/Y',strtotime($row_parceiro['parceiro_data']));
						  }
				 ?>
				 
				 </td>
             </tr>
       <?php } ?>
    </table>
    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('include/rodape.php'); ?>
    </div>
</div>
</body>
</html>