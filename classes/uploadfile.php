<?php

class UploadFile {
    private $arquivo, $tipos, $pasta, $nome;
    public $erro,$extensao;
    // define a pasta e o tipo do arquivo
    function UploadFile($pasta,$tipos = array('gif')){
        if(!is_dir($pasta)) {
            mkdir($pasta);
        }
        $this->pasta = $pasta;
        $this->tipos = $tipos;
    }

    // pega o arquivo
    function arquivo($arquivo){
        $this->arquivo = $arquivo;
    }

    // verifica a estensao
    function verificaFile(){
        $arquivo = strtolower($this->arquivo['name']);
        $arquivo = explode('.', $arquivo);
        $tipo_arquivo = count($arquivo)-1;
        $tipo_arquivo = $arquivo[$tipo_arquivo];
        foreach($this->tipos as $ext){
            if($tipo_arquivo == $ext){
                $this->extensao = $ext;
            }
        }
        if(empty($this->extensao)){
            $this->erro = 1;
        }
    }
    // Cria uma subpasta
    function Subpasta($nome){
        if(!is_dir($this->pasta.'/'.$nome)) {
            mkdir($this->pasta.'/'.$nome, 0777);
        }
        chmod($this->pasta.'/'.$nome, 0777);
        $this->pasta = $this->pasta.'/'.$nome;
    }
    // nomeia a imagem
    function NomeiaFile($nome){
        $this->nome = $nome;
    }
    // envia
    function Envia(){
        return move_uploaded_file($this->arquivo['tmp_name'], $this->pasta.'/'.$this->nome.".".$this->extensao);
        // ../obrigacoes/anexos_oscip/41.pdf
    }


}

?>