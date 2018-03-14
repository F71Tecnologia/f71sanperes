<?php

/**
 * NESSA CLASSE SERA CENTRALIZADO TODOS OS TIPOS DE CALCULOS COMUM PARA AÇÕES 
 * DO RH COMO RESCISÃO, FERIAS E FOLHA AFIM DE QUE TODAS AS TELAS CITADAS ACIMA
 * UTILIZEM O MESMO METODO
 */
Class calculosComumClass{
    
    private $valorAdicionalNoturno;
    private $valorDsr;
    private $qntDomingos;
           
    /**
     * 
     */
    public function calcAdicionalNoturno($salario = null, $insalubridadePericulosidade = null, $horasMensais = null, $horasNoturna = null, $percent = 0.20)
    {
        
        $baseParaCalculo        = 0;
        $valorHora              = 0;
        
        /**
         * VERIFICAR SALARIO DIFERENTE DE 0 
         */
        if(!empty($salario)){
            $baseParaCalculo += $salario;
        }
        /*
         * INSALUBRIDADE OU PERICULOSIDADE DIFERENTE DE 0 
         */
        if(!empty($insalubridadePericulosidade)){
            $baseParaCalculo += $insalubridadePericulosidade;
        }
        
        /**
         * CALCULO DE VALOR POR HORA
         */
        if(!empty($horasMensais)){
            $valorHora = $baseParaCalculo/$horasMensais;
            if(!empty($valorHora)){
                $this->valorAdicionalNoturno = ($valorHora * $horasNoturna) *  $percent;
                $this->valorAdicionalNoturno = number_format($this->valorAdicionalNoturno,2,'.',',');
            }      
        }
        
        return $this->valorAdicionalNoturno;
        
    }   
    
    /**
     * TEM QUE MELHORAR 
     * ACRESCENTAR HORA EXTRA
     */
    public function calcDSR()
    {
        
        $Domingos = $this->qntDomingos;
        $feriados = 0;
        $diasUteis = (int) 30 - $Domingos;
        $diasDescanso = $Domingos + $feriados;
                
        if(!empty($this->valorAdicionalNoturno)){
            if(!empty($diasDescanso)){
                $this->valorDsr = ($this->valorAdicionalNoturno/30) * $diasDescanso;
                $this->valorDsr = number_format($this->valorDsr,2,'.',',');
            }
        }else{
            exit("<br /> Necessário execultar o metodo <b>(calcAdicionalNoturno)</b><br />");
        }    
        
        return $this->valorDsr;
        
    }
    
    /**
     * TOTAL DE DOMINGO NO MES
     * @param type $mes
     * @param type $ano
     * @return type
     */
    public function qntDomingos($mes,$ano)
    {
        
        $tot_dias   = cal_days_in_month(CAL_GREGORIAN, $mes, $ano); 
        $dataini    = strtotime("01"."-".$mes."-".$ano);
        $datafin    = strtotime($tot_dias."-".$mes."-".$ano);
        
        while($dataini <= $datafin){
            if((date("w",$dataini) == 0)) 
                ++$this->qntDomingos;
            $dataini += 86400;
        } 
        
        return $this->qntDomingos;
    }
    
}