<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php $pagina_atual = explode('/', $_SERVER['PHP_SELF']);
      $pagina_atual = $pagina_atual[4]; 
	  	  $pagina = $_SERVER['PHP_SELF'];
	  
	  include('../../classes_permissoes/botoes.class.php');
	$botao = new Botoes();
	  ?>
<img style="position:relative; top:7px;" src="../../imagens/logomaster<?=$Master?>.gif" width="66" height="46">

         <span style="position:relative; margin-top:30px;">   <a href="../../box_suporte.php?&regiao=<?php echo $regiao;?>&pagina=<?php echo $pagina;?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" ><img src="../../imagens/suporte.gif"  width="55" height="55"/></a></span>	
         
<ul id="contrato">
     <li><a href="../index.php?m=<?=$link_master?>">Página Inicial</a></li>
     
     
       <?php if($botao->verifica_permissao(82)) {  ?>
        <!--<li><?php if($pagina_atual == 'index.php') { ?><a class="ativo" href="#"><?php } else { ?><a href="index.php?m=<?=$link_master?>"><?php } ?>Obriga&ccedil;&otilde;es do Projeto</a></li>-->
      <?php } ?>
     
     <!--<li><?php if($pagina_atual == 'pre_cadastro.php') { ?><a class="ativo" href="#"><?php } else { ?><a href="pre_cadastro.php?m=<?=$link_master?>"><?php } ?>Cadastrar Obriga&ccedil;&otilde;es do Projeto</a></li>-->
      
       <?php if($botao->verifica_permissao(94)) {  ?>
     			<li><?php if($pagina_atual == 'dados_oscip.php') { ?><a class="ativo" href="#"><?php } else { ?><a href="dados_oscip.php?m=<?=$link_master?>"><?php } ?>Obriga&ccedil;&otilde;es da Instituição</a></li>
      <?php } ?>
      
	  
	  <?php if($botao->verifica_permissao(95)) {  ?>
    			 <li><?php if($pagina_atual == 'cadastro_oscip.php') { ?><a class="ativo" href="#"><?php } else { ?><a href="cadastro_oscip.php?m=<?=$link_master?>"><?php } ?>Cadastrar Obriga&ccedil;&otilde;es da Instituição</a></li>
     <?php } ?>
     
     
</ul>