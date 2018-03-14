<?php

/**
 * Description of imageToPdf
 *
 * @author Ramon
 */
class imageToPdf {

    //put your code here

    private $files = array();
    private $extensoes = array("jpg", "gif", "png");
    private $extensoesConvert = array("gif", "png");
    private $jpgImages = array();
    private $erros = array(
        "1"=>"Extensão não suportada",
        "2"=>"Arquivo não encontrado",
        "3"=>"Extensão não suportada para conversão",
        "4"=>"Não foi possivel converter a imagem para JPG",
        "5"=>"Nenhuma imagem selecionada para gerar JPG",
        "6"=>"Erro ao criar o PDF");
    private $erroLog = array();

    public function __construct($debug=false) {
        /*if ($debug) {
            error_reporting(E_ALL);
        }*/
    }

    public function addFile($filename) {
        $ex = $this->getExtensionImage($filename);
        if (in_array($ex, $this->extensoes)) {
            if (is_file($filename)) {
                $this->files[] = $filename;
            }else{
                $this->erroLog[2][] = $filename;
            }
        }else{
            $this->erroLog[1][] = $filename;
        }
    }

    public function getExtensionImage($filename) {
        $arFile = explode(".", $filename);
        $extension = end($arFile);
        return $extension;
    }

    public function convertToJpg() {
        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                $tipo = $this->getExtensionImage($file);
                
                if (in_array($tipo, $this->extensoesConvert)) {

                    if ($tipo == "gif") {
                        $img = imagecreatefromgif($file);
                    } elseif ($tipo == "png") {
                        $img = imagecreatefrompng($file);
                    }
                    /*
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
                    */
                    if (imagejpeg($img, $file . ".jpg")) {
                        $this->jpgImages[] = $file . ".jpg";
                    } else {
                        $this->erroLog[4][] = $file;
                        return false;
                    }
                }elseif($tipo == "jpg"){
                    $this->jpgImages[] = $file;
                }else{
                    $this->erroLog[3][] = $file;
                }
            }
        }else{
            $this->erroLog[5][] = "nenhum arquivo";
        }
    }
    
    public function generatePdf($saveAS) {
        $pdf = new FPDF();
        $pdf->AddPage('P', 'A4');
        
        $this->convertToJpg();
        
        $totalPag = count($this->jpgImages);
        if ($totalPag > 1) {
            $i = 1;
            foreach ($this->jpgImages as $foo) {
                $pdf->Image($foo, 0, 0, 210, 260);
                ($i++ < $totalPag) ? $pdf->AddPage() : null; //Para a ultima pagina não vir em branco
            }
        }elseif($totalPag == 1){
            $pdf->Image($this->jpgImages[0], 0, 0, 210, 260);
        }
        
        $pdf->Output($saveAS, 'F');
        //REMOVENDO AS IMAGENS JPG GERADAS
        if ($totalPag > 1) {
            foreach ($this->jpgImages as $foo) {
                unlink($foo);
            }
        }
        
        if (is_file($saveAS) && count($this->erroLog)==0)
            return true;
        else
            $this->erroLog[6][] = "Erro ao criar o PDF";
            return false;
    }
    
    public function getError(){
        $html = null;
        if(count($this->erroLog) > 1){
            foreach($this->erroLog as $k => $val){
                $html .= "ERRO: " . $this->erros[$k] . ": ";
                if(is_array($val) && count($val)>1){
                    foreach($val as $foo){
                        $html .= $foo."<br/>";
                    }
                }else{
                    $html .= current($val)."<br/>";
                }
            }
        }
        return $html;
    }

}

?>
