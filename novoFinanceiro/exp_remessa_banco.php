<?php

/* 
 * 
 */

include ('../classes/Cnab240RemClass.php'); 
include ("include/restricoes.php");
include ("../conn.php");

$objCnab240 = new CNAB240();

$objCnab240->setUser('JACQUES');
$objCnab240->setIdsSaidas('118101');
$objCnab240->setPath('/home/ispv/public_html/intranet/novoFinanceiro/arquivos_cnab240/');


if($objCnab240->RunRemessa()){;
    $objCnab240->OutPutRemessa();
}
else {
    echo $objCnab240->getError();
}


?>