<?php

/*
 * PHP-DOC - Classe de data  
 *  
 * 16/06/2015
 * 
 * Classe de formatação de dados para exportação do PIS
 * 
 * Versão: 3.00.0000 - 00/00/0000 - Jacques - Classe para manipulação de datas
 * Versão: 3.01.0000 - 11/08/2015 - Jacques - Adicionada função para calculo de datas com dois formatos
 * Versão: 3.02.0000 - 14/09/2015 - Sinésio - Criação do método explodeDate().
 * Versão: 3.03.0000 - 23/09/2015 - Jacques - Adicionado método para converter formatação de data na codificação PHP para sintaxe SQL
 * versão: 3.04.0000 - 06/10/2015 - Jacques - Alterado a fórmula de cálculo do método diffInDays
 * Versão: 3.05.0000 - 13/10/2015 - Jacques - Implementado a possibilidade de uso de métodos encadeados e uso simplificado do set e get
 * Versão: 3.06.0000 - 27/10/2015 - Jacques - Implementado o método getNow que obtem a hora atual
 * Versão: 3.07.0000 - 11/11/2015 - Jacques - Implementado o método para retornar o mês por extenso
 * Versão: 3.08.4340 - 11/11/2015 - Jacques - Alteração do método daysInMonth para usar função nativa do PHP
 * Versão: 3.08.4340 - 11/01/2016 - Jacques - Estou tendo um bug em relação ao método get dessa classe. Quando seto o método para não retornar 
 *                                            retornar a data de 1970-01-01 para datas em branco, afeto os calculos de período aquisitivo e férias dobradas
 *                                            $this->value_return = isset($format) ?  empty($this->value) ? '' : gmdate($format,strtotime($this->value)) : $this->value;
 * Versão: 3.08.7144 - 29/02/2016 - Jacques - Adicionado métodos sumYear() e minusYear()
 * Versão: 3.08.0000 - 21/03/2016 - Jacques - Erro no método minusMonth que estava executando a operação em dias ao invéz de mês
 * 
 * @Jacques
 */


class DateClass {
    
    private $month = array( 
                            1 => "Janeiro", 
                            2 => "Fevereiro", 
                            3 => "Março", 
                            4 => "Abril", 
                            5 => "Maio", 
                            6 => "Junho",
                            7 => "Julho", 
                            8 => "Agosto", 
                            9 => "Setembro", 
                            10 => "Outubro", 
                            11 => "Novembro", 
                            12 => "Dezembro"
                           );
    
    private $value, $value_return;
    
    public function __construct() 
    {
        
    }

    /*
     * PHP-DOC - Define um valor de retorno caso a referência ao objeto seja feita em um procedimento de retorno de valor
     */
    public function __toString()
    {
        
        return (string)$this->value_return; 
        
    }
    
    /*
     * PHP-DOC - Define valores default para a classe data
     */
    public function setDefault(){
        
        $this->value = $this->value_return = '';
        
        return $this;        
        
    }
    
    /*
     * PHP-DOC - Define o valor de uma data, caso o parámetro seja vazio define o valor com a data corrente
     * 
     *           Necessário identificar tipo de data no Banco de Dados para formata-la (a implementar)
     */
    public function set($value){
        
//        $month = date("m",$value);
//
//        $day = date("d",$value);
//        
//        $year = date("Y",$value);
//        
//        if(!checkdate($month, $day, $year) && !empty($value)){
//
//            exit("Data inválida {$value}");
//        
//        }
        
        $value = is_null($value) ? "" : $value;
        
        $this->value = $value; 
        
        return $this;        
        
    } 

    /*
     * PHP-DOC - Define o valor de uma data no padrão antigo
     */
    public function setDate($value){
        
        $this->set($value);
        
        return $this;        
        
    } 
    
    /*
     * PHP-DOC - Obtem o valor de uma data
     */
    public function get($format, $value){
        
        if(!empty($value)){
            
            $this->value = $value;
            
        }
        
//        preg_replace("/[^0-9]/", "", $this->value);
        
//        $this->value_return = isset($format) ?  empty($this->value) ? '' : gmdate($format,strtotime($this->value)) : $this->value;    

        $this->value_return = isset($format) ?  gmdate($format,strtotime($this->value)) : $this->value;    
        
        return $this;        
        
    }
    
    /*
     * PHP-DOC - Deprecated - Método para retornar uma data no formado antigo. 
     */
    public function getDate($format, $value){
        
        $this->get($format,$value);
        
        return $this;        
        
    }
    
    /*
     * PHP-DOC - Deprecated - Método para obter o valor de uma data no padrão antigo
     */
    public function getFmtDateConvSql($value){
        
        $patterns = array();
        $patterns[0] = '/d/';
        $patterns[1] = '/m/';
        $patterns[2] = '/y/';
        $patterns[3] = '/D/';
        $patterns[4] = '/M/';
        $patterns[5] = '/Y/';
        
        $replacements = array();
        $replacements[0] = '%d';
        $replacements[1] = '%m';
        $replacements[2] = '%y';
        $replacements[3] = '%D';
        $replacements[4] = '%M';
        $replacements[5] = '%Y';
        
        /*
         * Define o formato de data de Query Sql baseado no formato de data
         */
        $this->value_return = preg_replace($patterns, $replacements, $value);    
        
        return $this;        
        
    }
    
    /*
     *  PHP-DOC - Método que retorna o timestamp de uma data no formato DD/MM/AAAA passada como valor
     */
    public function geraTimeStamp($value) {
        
        $partes = explode('/', $value);
        
        $this->value_return = mktime(0, 0, 0, $partes[1], $partes[0], $partes[2]);
        
        return $this;        
        
        
    }    
    
    public function getMonthString($value){
        
        $this->value_return = !isset($value) ? str_replace('01/01/1970','erro',gmdate($format,strtotime($value))) : $date;        
        
        return $this;        
        
    }
    
    /*
     * PHP-DOC - Obtem a data e hora atual
     */    
    public function now($value) {
        
        $this->value_return = $this->value = date(empty($value) ? "YmdHis" : $value, mktime()); 
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Obtem a data e hora atual
     */
    public function getNow($value){
        
        return $this->now()->get($value);
        
    }
    
    /*
     * Calcula a diferença em dias entre duas datas passadas por valor ou entre a data do objeto e o valor
     */
    public function diffInDays($value1,$value2){

        if(empty($value2)) {
            
            $value2 = $this->get();
            
        }
        
        /*
         * Calcula a diferença em segundos entre as datas
         */
        $diferenca = strtotime($value1) - strtotime($value2);

        /*
         * Calcula e retorna a diferença em dias
         */
        $this->value_return = floor($diferenca / (60 * 60 * 24));     
        
//        echo '<pre>';
//        echo "value1 = {$value1}";
//        echo "value2 = {$value2}";
//        echo '</pre>';
        
        return $this;  
        
    }
    
    
    /*
     *  PHP-DOC - Determinando um intervalo entre duas datas utilizado nas rotinas antigas
     * 
     *  Formato: dd/mm/aaaa
     * 
     */
    
    public function calculaIntervalo($data1,$data2='',$type){
        
//            // Cacula a diferença de datas no formato yyyy/mm/dd
//            // Define os valores a serem usados
//            $data_inicial = '2009-03-23';
//            $data_final = '2009-11-04';
//            // Usa a função strtotime() e pega o timestamp das duas datas:
//            $time_inicial = strtotime($data_inicial);
//            $time_final = strtotime($data_final);
//            // Calcula a diferença de segundos entre as duas datas:
//            $diferenca = $time_final - $time_inicial; // 19522800 segundos
//            // Calcula a diferença de dias
//            $dias = (int)floor( $diferenca / (60 * 60 * 24)); // 225 dias
//            // Exibe uma mensagem de resultado:
//            echo "A diferença entre as datas ".$data_inicial." e ".$data_final." é de <strong>".$dias."</strong> dias";
        

        // Cacula a diferença de datas no formato dd/mm/aaaa
        // se data2 for omitida, o calculo sera feito ate a data atual
        $data2 = $data2=='' ? date("d/m/Y",mktime()) : $data2;

        // separa as datas em dia,mes e ano
        list($dia1,$mes1,$ano1) = explode("/",$data1);
        list($dia2,$mes2,$ano2) = explode("/",$data2);

        // so lembrando que o padrao eh MM/DD/AAAA
        $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
        $timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

        // calcula a diferenca em timestamp
        $dif_data = ($timestamp1 > $timestamp2) ? ($timestamp1 - $timestamp2) : ($timestamp2 - $timestamp1);

        // Calcula a diferença de segundos entre as duas datas:
        $dif_seg = $timestamp2 - $timestamp1; // resultado em segundos

        // Calcula a diferença de dias
        $dias = (int)floor( $dif_seg / (60 * 60 * 24)); // descobre a diferença em dias            

        // retorna o calculo em ano, mes, dia ou dias totais
        $dif['ano'] = (date("Y",$dif_data)-1970);
        $dif['mes'] = (date("m",$dif_data)-1);
        $dif['dia'] = (date("d",$dif_data)-1);
        $dif['dias'] = $dias;

        $this->value_return = $dif[$type];

        return $this;  

    }    
    
    /*
     * PHP-DOC - Método para separar dia, mês e ano em um vetor
     */
    public function explodeDate($value,&$array){
        
        if(empty($value)){
            
            $value = $this->value;
                    
        }
        
        $separador = preg_replace("/[0-9]/","", $value); 
        
        if(empty($separador[0])){
            
            $array = array(
                          substr($value, 1, 4),
                          substr($value, 5, 2),
                          substr($value, 7, 2),
                          );
        }
        else {

            $array = explode($separador[0],$value);
         
        }    
        
        return $this;  
        
    }
    
    /*
     * PHP-DOC - Calcula o número de dias no mês a partir de uma data 
     */
    function daysInMonth($value) 
    { 
       
        if(!empty($value)){
            
            $this->set($value);
            
        }

        /*
         * Deprecated - Modelo de calculo usado no sistema antigo com bug no calculo 
         */
//        $this->value_return = $this->get('m') == 2 ? ($this->get('Y') % 4 ? 28 : ($this->get('Y') % 100 ? 29 : ($this->get('Y') % 400 ? 28 : 29))) : (($this->get('m') - 1) % 7 % 2 ? 30 : 31);        

        /*
         * Calcula o número de dias no mês 
         */
        
        $this->value_return = cal_days_in_month(CAL_GREGORIAN,$this->get('m')->val(), $this->get('Y')->val());
        
        return $this;
        
    }     

    /*
     * Executa a soma de dias a data corrente da classe
     */
    function sumDays($value = 1){
        
        $this->value_return = $this->value = date('Y-m-d', strtotime("+{$value} days",strtotime($this->value)));        
        
        return $this;
        
    }
    
    /*
     * Executa a subtração de dias a data corrente da classe
     */
    function minusDays($value = 1){

        $this->value_return = $this->value = date('Y-m-d', strtotime("-{$value} days",strtotime($this->value)));        
        
        return $this;
        
    }
    
    
    /*
     * Executa a soma de meses
     */
    function sumMonth($value = 1){

        $this->value_return = $this->value = date('Y-m-d', strtotime("+{$value} month",strtotime($this->value)));        
        
        return $this;
        
    }
    
    /*
     * Executa a subtração de meses
     */
    function minusMonth($value = 1){

        $this->value_return = $this->value = date('Y-m-d', strtotime("-{$value} month",strtotime($this->value)));        
        
        return $this;
        
    }
    
    /*
     * Executa a soma de anos a data corrente da classe
     */
    function sumYear($value = 1){
        
        $this->value_return = $this->value = date('Y-m-d', strtotime("+{$value} year",strtotime($this->value)));        
        
        return $this;
        
    }
    
    /*
     * Executa a subtração de anos a data corrente da classe
     */
    function minusYear($value = 1){

        $this->value_return = $this->value = date('Y-m-d', strtotime("-{$value} year",strtotime($this->value)));        
        
        return $this;
        
    }
    
    
    /*
     * Retorna o valor do mês passado como inteiro em string
     */
    function stringMonth($value){
        
        
        if(empty($value)) {
            
            $this->value_return = $this->month[$this->get('m')];
            
        }
        else {
            
            $this->value_return = $this->month[$value];
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name isEmpty
     * 
     * @internal - ATENÇÃO - Esse método não foi testado ainda! Método para verificar se o valor da data é vazio ou nulo.
     */
    public function isEmpty(){
        
        if($this->getData()==NULL || empty($this->getData()) || $this->getData()=='19700101'){
            
            $this->value_return = '1';
        
        }
        else {
            
            $this->value_return = '0';
            
        }
        
        return $this;
        
    }
    
    
    /*
     * Executa o retorno do valor da classe ao invez de retornar a própria classe
     */
    public function val($value){
        
        return isset($value) ?  gmdate($value,strtotime($this->value_return)) : $this->value_return; 
        
    }
    

}

?>