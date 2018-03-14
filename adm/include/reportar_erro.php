<?php
	  $pagina = $_SERVER['PHP_SELF'];
?>

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 
<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>


 <a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/intranet/box_suporte.php?&regiao=<?php echo $regiao;?>&pagina=<?php echo $pagina;?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" ><img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/intranet/imagens/suporte.gif"  width="55" height="55"/></a>