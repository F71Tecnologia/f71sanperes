<?php

include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
//include('../../adm/include/criptografia.php');
include('../../classes/pdf/fpdf.php');
include('../../classes/pdf/font/helvetica.php');


function convertImage($imagem,$tipo){
    if($tipo == "gif"){
        $img = imagecreatefromgif($imagem.".".$tipo);
    }elseif($tipo == "png"){
        $img = imagecreatefrompng($imagem.".".$tipo);
    }
    
    $w = imagesx($img);
    $h = imagesy($img);
    $trans = imagecolortransparent($img);
    if ($trans >= 0) {
        $rgb = imagecolorsforindex($img, $trans);
        $oldimg = $img;
        $img = imagecreatetruecolor($w, $h);
        $color = imagecolorallocate($img, $rgb['red'], $rgb['green'], $rgb['blue']);
        imagefilledrectangle($img, 0, 0, $w, $h, $color);
        imagecopy($img, $oldimg, 0, 0, 0, 0, $w, $h);
    }
    imagejpeg($img, $imagem.".jpg");
    return $imagem.".jpg";
}

$pdf = new FPDF();

$id_oscip = $_GET['id'];
$tipo = $_GET['tipo'];

$ar_del = array();
$qr_anexos = mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='$id_oscip' AND tipo_anexo='$tipo' AND status=1  AND extensao != 'pdf' ORDER BY anexo_ordem ASC");
while ($row_anexo = mysql_fetch_assoc($qr_anexos)) {
    
    $pdf->AddPage('P', 'A4');
    $file = 'anexos_oscip/' . $row_anexo['id_anexo'] . '.' . $row_anexo['extensao'];
    if ($row_anexo['extensao'] == "gif" || $row_anexo['extensao'] == "png") {
        $file = convertImage('anexos_oscip/' . $row_anexo['id_anexo'], $row_anexo['extensao']);
        $ar_del[] = $file;
    }
    
    $pdf->Image($file, 0, 0, 210, 260);
}

if( count($ar_del) >= 1){
    foreach($ar_del as $val){
        unlink($val);
    }
}

$pdf->Output('teste.pdf', 'I');

?>