<?php

class RegrasESocial {

    public $id_master;   
    public $iniValidade;
    public $fimValidade;
    public $tpevento;


    public function __construct($idMaster, $iniValidade, $fimValidade, $tpevento) {
        $this->id_master = $idMaster;
        $this->iniValidade = $iniValidade;
        $this->fimValidade = $fimValidade;
        $this->tpevento = $tpevento;
    }
                                                    
    public function regraExisteInfoEmpregador() {
        $qrRegra= "SELECT * FROM log_e_social WHERE evento LIKE '%s1000%' AND ini_validade <=$this->iniValidade AND (fim_validade IS NULL OR fim_validade >= $this->iniValidade) AND id_master = $this->id_master;";
        $result = mysql_query($qrRegra);
        return $result;            
    }
    
    public function regraInfoEmpPeriodoConflitante() {
        $qrRegra = "SELECT *
                    FROM log_e_social
                    WHERE evento LIKE '%s1000%' AND ini_validade <= $this->iniValidade AND (fim_validade IS NULL OR fim_validade >= $this->iniValidade) 
                    AND tp_evento = '$this->tpevento' AND id_master = $this->id_master AND id_regiao IS NULL AND id_projeto IS NULL;";
        $result = mysql_query($qrRegra) or die('aqui');
        return $result; 
    }
    
    public function regraInfoEmpPermiteExclusao() {
        $qrRegra = "SELECT * FROM log_e_social WHERE evento LIKE '%200%' AND evento LIKE '%s1000%' AND ini_validade <=$this->iniValidade AND (fim_validade IS NULL OR fim_validade >= '$this->iniValidade') AND tp_evento = '$this->tpevento' AND id_master = $this->id_master";
        $result = mysql_query($qrRegra);
        return $result;
    }
    
    public function regraInfoEmpValidaDtInicial() {
              
        $qrRegra = "SELECT DATE_FORMAT(B.inicio,'%d/%m/%Y') AS inicio
                    FROM `master` AS A
                    INNER JOIN projeto AS B ON(A.id_master = B.id_master)
                    WHERE B.id_master = $this->id_master AND B.administracao = 1 AND B.inicio <= $this->iniValidade;";
        $result = mysql_query($qrRegra);
        return $result;

    }
    
    public function regraInfoEmpValidaEndEstabs() {
        
        $qrRegra = "SELECT A.cep, B.nome
                    FROM rhempresa AS A
                    LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                    WHERE A.id_master = $this->id_master;";
        $result = mysql_query($qrRegra);
        /*
        * Verifica Cep
        * Descricao: Verifica se um CEP eh Valido
        * Autor:    Antônio Norival Ribeiro Passos
        * Contato: tonhopassos@gmail.com
        * Data: 01/02/2010
        * Modificacao: 01/02/2011
        * Versao: 1.0.0.0
        * Licenca: Copyright (C) 2011
        */
        while ($row = mysql_fetch_array($result)) {
            // retira espacos em branco
            $cep = RemoveCaracteres($row['cep']);
            $cep = substr($cep, 0,5)."-".substr($cep, 5,8);
            // expressao regular para avaliar o cep
            $avaliaCep = ereg("^[0-9]{5}-[0-9]{3}$", $cep); 

            // verifica o resultado
            if(!$avaliaCep) {            
                echo "<h3>CEP do projeto ".$row['nome']." não é válido</h3>";
            }
        }
    }
    
    public function regraTabGeralExisteRegistroExcAlter($codEvento,$fimValidadeN, $iniValidadeN) {
        $qrRegra = "SELECT * FROM log_e_social WHERE evento LIKE '%$codEvento%' AND ini_validade <= $iniValidadeN AND (fim_validade <= $fimValidadeN OR fim_validade IS NULL) AND id_master =  $this->id_master AND (tp_evento = 'alterado' OR tp_evento = 'inclusao');";
//        print_r($qrRegra); exit;
        $result = mysql_query($qrRegra);
        return $result;
    }
    
    public function regraTabGeralInclusaoPeriodoConflitante($codEvento, $projeto='NULL') {
        $qrRegra = "SELECT * FROM log_e_social WHERE evento LIKE '%$codEvento%' AND ini_validade <=$this->iniValidade AND (fim_validade IS NULL OR fim_validade >= $this->iniValidade) AND id_master = $this->id_master AND id_projeto = $projeto AND tp_evento = 'inclusao';";
        $result = mysql_query($qrRegra);
        return $result;
        
    }
    
    public function regraTabGeralAlteracaoPeriodoConflitante($codEvento,$fimValidadeN) {
        $qrRegra = "SELECT * FROM log_e_social WHERE evento LIKE '%$codEvento%' AND ini_validade <=$fimValidadeN AND (fim_validade <= $fimValidadeN) AND id_master = $this->id_master AND tp_evento = 'inclusao';";
        
        $result = mysql_query($qrRegra);
        return $result;
        
    }


    
}
?> 
