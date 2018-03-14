<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */ 

require_once ('../../../framework/vendor/dompdf/dompdf/autoload.inc.php');

use Dompdf\Dompdf;
$id_folha = $_REQUEST['id_folha'];
$cookie = $_COOKIE['logado'];
$html = file_get_contents("pdf_{$id_folha}_{$cookie}.html");

// instantiate and use the dompdf class
$dompdf = new Dompdf();
//$dompdf->loadHtml('hello world');
$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');
// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream();
?>