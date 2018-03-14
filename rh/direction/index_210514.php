<?php
include('../../conn.php');
include('../../wfunction.php');
$usuario = carregaUsuario();

$sql = mysql_query("SELECT A.nome AS nome_clt, A.endereco AS endereco_clt, A.numero AS num_clt, A.complemento AS complem_clt, 
                    A.bairro AS bairro_clt, A.cidade AS cidade_clt, A.uf AS uf_clt, A.cep AS cep_clt, A.tel_cel AS cel_clt, 
                    A.email AS email_clt, B.unidade, B.tel AS tel_uni, B.endereco AS endereco_uni, B.bairro AS bairro_uni, 
                    B.cidade AS cidade_uni, B.uf AS uf_uni, B.cep AS cep_uni, B.ponto_referencia AS ref_uni
                    FROM rh_clt AS A
                    LEFT JOIN unidade AS B ON(A.id_unidade = B.id_unidade)
                    WHERE A.id_clt = 8114
                    ORDER BY B.id_unidade DESC
                    LIMIT 25") or die(mysql_error());
$row = mysql_fetch_array($sql);

function removeGeral($variavel) {
    $variavel = strtolower($variavel);
    $variavel = str_replace('(', "", $variavel);
    $variavel = str_replace(')', "", $variavel);
    $variavel = str_replace('-', '', $variavel);
    $variavel = str_replace('/', '', $variavel);
    $variavel = str_replace(":", "", $variavel);
    $variavel = str_replace(",", " ", $variavel);
    $variavel = str_replace('.', '', $variavel);
    $variavel = str_replace(";", "", $variavel);
    $variavel = str_replace("\"", "", $variavel);
    $variavel = str_replace("\'", "", $variavel);
    $variavel = str_replace("  ", "", $variavel);
    $variavel = str_replace("∫", "", $variavel);
    $variavel = str_replace("™", "", $variavel);
    $variavel = str_replace("andar", "", $variavel);
    $variavel = str_replace("sala", "", $variavel);
    $variavel = str_replace("sl", "", $variavel);
    $variavel = str_replace("apartamento", "", $variavel);
    $variavel = str_replace("apto", "", $variavel);
    $variavel = str_replace("apt", "", $variavel);
    $variavel = str_replace("ap", "", $variavel);    
    $variavel = str_replace("lote", "", $variavel);
    $variavel = str_replace("lt", "", $variavel);
    $variavel = str_replace("quadra", "", $variavel);    
    $variavel = str_replace("qd", "", $variavel);
    $variavel = str_replace("casa", "", $variavel);    
    $variavel = str_replace("cs", "", $variavel);
    $variavel = str_replace("bloco", "", $variavel);    
    $variavel = str_replace("bl", "", $variavel);
    $variavel = str_replace("fds", "", $variavel);
    return preg_replace('/([0-9]{1,})/i', "", $variavel);
}

$partida = trim(removeGeral($row['endereco_clt'])) . ", {$row['cidade_clt']} - {$row['uf_clt']}";

$destino = trim(removeGeral($row['endereco_uni'])) . ", {$row['cidade_uni']} - {$row['uf_uni']}";

$array = explode(" ", $partida);
$new_array = array();

$array2 = explode(" ", $destino);
$new_array2 = array();

foreach($array as $val){
    if($val != "" && $val != "" && $val != "" && $val != "" && $val != "" && $val != "" && $val != "" && $val != "fds" && $val != ""){
        array_push($new_array, $val);
    }
}

foreach($array2 as $val){
    if($val != "lote" && $val != "qd" && $val != "cs" && $val != "ap" && $val != "bl" && $val != "apt" && $val != "casa" && $val != "fds" && $val != "apto"){
        array_push($new_array2, $val);
    }
}

echo "<pre>";
print_r($new_array2);
echo "</pre>";

$partida = trim(implode(" ", $new_array));
$destino = trim(implode(" ", $new_array2));
?>

<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: Mapa do Funcion·rio</title>
        <meta charset="iso-8859-1" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>        
        
        <style type="text/css">
            body{height: 100%; margin: 0; padding: 0}
            * { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px }
            #site { width: 100%; margin: 20px auto 0 auto }
            h1 { text-align: center; font-size: 18px }
            #mapa { width: 600px; height: 400px; }
            /*#trajeto-texto { width: 300px; height: 400px; float: right; overflow: scroll; background-color: #999999; font-size: 10px; }*/
            html { height: 100% }
        </style>        
    </head>
    
    <body class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h1>Mapa do funcion·rio ***</h1>
                    </div>
                </div>
                
                <br/>
<!--                <fieldset>
                    <legend>Dados</legend>
                    <table>
                        <tr><th colspan="2">Pessoa</th></tr>
                        <tr><td>Nome:</td><td><?php echo $row["nome"] ?></td></tr>
                        <tr><td>Endere√ßo:</td><td><?php echo $row["endereco"] ?></td></tr>
                        <tr><th colspan="2">Unidade</th></tr>
                        <tr><td>Endere√ßo:</td><td> <?php echo $row["endereco_u"]; echo $row["cidade_u"]. " ".$row["bairro_u"]." ".$row["regiao"]; ?> </td></tr>
                        <tr><td>CEP:</td><td><?php echo $row["cep_u"] ?></td></tr>
                        <tr><td>CEP:</td><td></td></tr>
                    </table>
                </fieldset>-->
                <br />
                
                <?php 
                echo "ORIGEM: {$partida}<br />";
                echo "DESTINO: {$destino}"; 
                ?>
                
                <br /><br />
                
                <div id="mapa">
                    <script type="text/javascript">
                        $(document).ready(function(){
                            getRota("<?php print_r($partida); ?>", "<?php print_r($destino); ?>");                            
                        });
                    </script>
                </div>
            </form>
        </div>
        <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
        <script src="js/mapa.js"></script>        
    </body>
</html>