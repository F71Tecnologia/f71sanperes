<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php $pagina_atual = explode('/', $_SERVER['PHP_SELF']);
      $pagina_atual = $pagina_atual[4]; ?>
<img style="position:relative; top:7px;" src="../../imagens/logomaster<?=$Master?>.gif" width="66" height="46">
<ul id="parceiro">
    <li><a href="../index.php?m=<?=$link_master?>">Página Inicial</a></li>
    <li><?php if($pagina_atual == 'index.php') { ?><a class="ativo" href="#"><?php } else { ?><a href="index.php?m=<?=$link_master?>"><?php } ?>Total de Parceiros</a></li>
    <li><?php if($pagina_atual == 'cadastro.php') { ?><a class="ativo" href="#"><?php } else { ?><a href="cadastro.php?m=<?=$link_master?>"><?php } ?>Cadastro de Parceiro</a></li>
</ul>