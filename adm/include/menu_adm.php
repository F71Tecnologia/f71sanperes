<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php $pagina_atual = explode("/", $_SERVER['PHP_SELF']);
      $pagina_atual = $pagina_atual[4]; 
	  
	  	  $pagina_suporte = $_SERVER['PHP_SELF'];
	  ?>



    <img style="position:relative; top:7px; float:right;" src="../../imagens/logomaster<?=$Master?>.gif" width="66" height="46">	
   
          <span style="position:relative; margin-top:00px;float:right;"> <?php include('../include/reportar_erro.php'); ?> </span>		
          
         
          
          
<ul id="projeto" style="margin-top:10px;">
    <li><a href="../index.php?m=<?=$link_master?>">Página Inicial</a></li>
    <li><?php if($pagina_atual == "index.php") { ?><a class="ativo" href="#"><?php } else { ?><a href="index.php?m=<?=$link_master?>"><?php } ?>Gerenciar documentos</a></li>
    <li><a href="cadastro.php?m=<?=$link_master?>">Cadastro de Documento</a></li>
</ul>