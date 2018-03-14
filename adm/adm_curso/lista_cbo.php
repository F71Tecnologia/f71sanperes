<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php
include('../../conn.php');

// auto complete
$q = strtolower($_GET["q"]);

mysql_query("SET NAMES 'utf8'");

$sql = "SELECT * FROM rh_cbo WHERE nome LIKE '%$q%'";
$rsd = mysql_query($sql);
$tot = mysql_num_rows($rsd);

//trata navegador, pois no safari, deu erro de caracter e nos outros não
$lista_navegadores = array('MSIE', 'Firefox', 'Chrome', 'Safari');
$navegador_usado = $_SERVER['HTTP_USER_AGENT'];

foreach($lista_navegadores as $valor_verificar){
    if(strrpos($navegador_usado, $valor_verificar)){
        $navegador = $valor_verificar;
    }
}

if($navegador == 'Safari'){
    if($tot != 0){
        while($rs = mysql_fetch_array($rsd)) {
            $nome = utf8_decode($rs['nome']);
            echo $nome. " - " . $rs['cod'] . "\n";
        }
    }else{
        echo "Nenhum registro encontrado";
    }
}else{
    if($tot != 0){
        while($rs = mysql_fetch_array($rsd)) {            
            echo $rs['nome']. " - " . $rs['cod'] . "\n";
        }
    }else{
        echo "Nenhum registro encontrado";
    }
}
?>