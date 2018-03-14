<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php 
	include('../../classes_permissoes/botoes.class.php');
	$botao = new Botoes();
     
	  $pagina_atual = explode("/", $_SERVER['PHP_SELF']);
      $pagina_atual = $pagina_atual[4]; 
	  
	  $pagina = $_SERVER['PHP_SELF'];
	  ?>
<img style="position:relative; top:7px;" src="../../imagens/logomaster<?=$Master?>.gif" width="66" height="46">

               <span style="position:relative; margin-top:20px;">  <?php include('../../reportar_erro.php'); ?>   </span>		
                   
<ul id="projeto">
    <li><a href="../index.php?m=<?=$link_master?>">Página Inicial</a></li>
    
    <?php if($botao->verifica_permissao(89)) {  ?>
    
			<li><?php if($pagina_atual == "index.php") { ?><a class="ativo" href="#"><?php } else { ?><a href="index.php?m=<?=$link_master?>"><?php } ?>Total de Projetos</a></li>
            
    <?php } 
	if($botao->verifica_permissao(90)) {  ?>
     
    	<li><a href="../../projeto/cadastro_projeto.php?m=<?=$link_master?>&regiao=" target="_blank">Cadastro de Projeto</a></li>
    <?php } ?>
</ul>