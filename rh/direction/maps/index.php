<?php

$partida = "26113-630";
//$partida = "rua curitiba, belford roxo - rj";

//$partida ="26127-170";
$destino ="24724-600";

//$destino = "avenida nossa senhora de copacabana, rj";

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <title>Rotas</title>
        <link rel="stylesheet" type="text/css" href="css/estilo.css">
    </head>
    <body>
    	<div id="site">
            <div id="mapa"></div>
            <!--<div id="trajeto-texto"></div>-->
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script src="js/mapa.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                getRota("<?php echo $partida ?>", "<?php echo $destino; ?>");
            });
        </script>
    </body>
</html>