<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ESocialClass
 *
 * @author Renato
 */
class ESocialClass {
    
    private $arquivo;
    private $path;
    private $name;
    private $id_projeto;
    
    public function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }
    
    public function getIdProjeto() {
        return $this->id_projeto;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function setPath($path) {
        $this->path = $path;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function __construct($path = 'arquivos', $projeto = null) {
        $this->setIdProjeto($projeto);
        $this->verificaPasta($path);
    }
    
    /**
     * VERIFICA DIRETORIO DA PASTA
     * @param type $path
     */
    function verificaPasta($path) {
        
        /* PASTA */
        $diretorio = $path;
        if (!file_exists("$diretorio")) {
            mkdir($diretorio, 755);
        }
        
        /* ANO */
        $diretorio = "$diretorio/" . date('Y');
        if (!file_exists("$diretorio")) {
            mkdir($diretorio, 755);
        }
        
        /* MES */
        $diretorio = "$diretorio/" . date('m');
        if (!file_exists("$diretorio")) {
            mkdir($diretorio, 755);
        }
        
        $this->setPath("$diretorio/" . date('Ymd') . '_' . $this->getIdProjeto() . '.txt');
    }
    
    /**
     * PEGA TODOS OS PARTICIPANTES ATIVOS
     * @param type $id_regiao
     */
    function getCltAtivos() {
        $auxProjeto = ($this->getIdProjeto() > 0) ? " AND id_projeto = {$this->getIdProjeto()} " : null;
        $sql = "SELECT id_clt, nome, cpf, pis, data_nasci FROM rh_clt WHERE status NOT IN (SELECT codigo FROM rhstatus WHERE tipo = 'recisao') AND status_reg = 1 $auxProjeto";
        return $sql;
    }
    
    /**
     * CRIA O ARQUIVO PARA ESCRITA
     */
    function openArquivo(){
        $this->arquivo = fopen($this->path, "w");
    }
    
    /**
     * FECHA O ARQUIVO ABERTO
     */
    function closeArquivo(){
        fclose($this->arquivo);
    }
        
    /**
     * MONTA LINHAS DO ARQUIVO TXT
     * LINHA DE VALIDAÇÃO DOS DADOS DOS CLTs COM A BASE DO INSS
     * @param type $dados
     */
    function montaLinhaValidacao($dados) {

        $array['cpf'] = sprintf("%011s", str_replace(array('-','/','.'), '', $dados['cpf'])); //11 Numérico
        $array['pis'] = sprintf("%011s", str_replace(array('-','/','.'), '', $dados['pis'])); //11 Numérico
        $array['nome'] = sprintf("%s", RemoveAcentos($dados['nome'])); //60 Alfanumérico sem acentuação e sem uso de caracteres especiais*, não importando maiúscula ou minúscula. 
//        $array['nome'] = sprintf("%60s", RemoveAcentos($dados['nome'])); //60 Alfanumérico sem acentuação e sem uso de caracteres especiais*, não importando maiúscula ou minúscula. 
        $array['data_nasci'] = sprintf("%08s", implode('', array_reverse(explode('-',$dados['data_nasci'])))); //8 Data (DDMMAAAA)

        if($_GET['debug']){
            echo implode(';', $array) . '<br>';
        } else {
            fwrite($this->arquivo, implode(';', $array), 93);
            fwrite($this->arquivo, "\r\n");  
        }
    }
    
    /**
     * FORÇA O DOWNLOAD DO ARQUIVO
     */
    public function downloadArquivo(){
        if(file_exists($this->path)){
            $fileName = basename($this->path);
            header("Content-Type: application/force-download");
            header("Content-type: application/octet-stream;");
            header("Content-Length: ".filesize($this->path));
            header("Content-disposition: attachment; filename={$fileName}");
            header("Pragma: no-cache");
            header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
            header("Expires: 0");
            readfile($this->path);
            flush();
            exit;
        }
        return false;
    }
}
