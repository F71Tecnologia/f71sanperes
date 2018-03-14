<?php

/* 
 * 
 */

include ('../classes/Cnab240Class.php'); 
include ("include/restricoes.php");
include ("../conn.php");


if(isset($_FILES['file']['name'])){

    $source_file = $_FILES['file']['tmp_name'];
    
        
    $_UP['pasta'] = '/tmp/'.date('Y/m');

    // Pasta onde o arquivo oriundo de uma post vai ser salvo
    $_UP['pasta_tmp_url'] = "/tmp/".md5(time()).'.txt';

    // Tamanho máximo do arquivo (em Bytes)
    $_UP['tamanho'] = 245 * 245; // 60kb 

    // Array com as extensões permitidas
    $_UP['extensoes'] = array('txt');
    

    $objCnab240 = new CNAB240();

    $objCnab240->setUser('JACQUES');
    $objCnab240->setFile($source_file);
    
    if($objCnab240->RunRetorno()){;
        //$objCnab240->OutPutRemessa();
    }
    else {
        echo $objCnab240->getError();
    }
    
}

?>

<form id="arquivo" name="arquivo" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<label for="name">Arquivo retorno:</label>
<input type="file" name="file" /><br/>
<br/>
<br/>
<input type="submit"   value="Processar Retorno"/>
</form>

