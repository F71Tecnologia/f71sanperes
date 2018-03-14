<?php
$pagina_atual = explode("/", $_SERVER['PHP_SELF']);
$pagina_atual = $pagina_atual[4];

$pagina = $_SERVER['PHP_SELF'];
?>
<img style="float:right;" src="../imagens/logomaster<?= $Master ?>.gif" width="66" height="46">

<span style="position:relative;float:right;"> 
    <?php include('../reportar_erro.php'); ?> 
</span>			

<ul id="projeto">
    <li><a href="index.php?m=<?= $link_master ?>">Página Inicial</a></li>
</ul>