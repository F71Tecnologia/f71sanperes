<?php
class calculo_rescisao extends calculos {

    
    
    public $aviso;
    public $dispensa;
    public $qnt_meses_fp; //quantidade de meses da férias proporcionais
    
    
    public function ferias_proporcionais($dt_ini, $dt_fim){        

            list($ano_inicio,$mes_inicio,$dia_inicio) = explode('-',$dt_ini);
            list($ano_final,$mes_final,$dia_final)    = explode('-',$dt_fim);    

            $dt_inicio_seg     = mktime(0,0,0,$mes_inicio,$dia_inicio, $ano_inicio );
            $dt_fim_seg        = mktime(0,0,0,$mes_final,$dia_final, $ano_final );   
            $diferenca_meses   =  ($dt_fim_seg - $dt_inicio_seg)/2592000; 

            for($i=0;$i<$diferenca_meses;$i++){   

               if($i == 0){
                $data_1         =  mktime(0,0,0,($mes_inicio + $i),$dia_inicio, $ano_inicio );
               } else {
                $data_1         =  mktime(0,0,0,($mes_inicio + $i),1, $ano_inicio );       
               }    

                $ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, date('m',$data_1), date('y',$data_1));  
                $data_2         =  mktime(0,0,0,($mes_inicio + $i), $ultimo_dia_mes, $ano_inicio );

                if($data_2 >= $dt_fim_seg){   $data_2 = $dt_fim_seg;  }

                $dias_trab = ($data_2 - $data_1)/86400;

                if($dias_trab >=14){
                    $total_meses +=1;          
                 }   
                //debug periodos
               // echo date('d/m/Y',$data_1).' - '.date('d/m/Y',$data_2).' = '.round($dias_trab).' dias.'.'<br>';    
            }
              if($aviso == 'indenizado'  and $total_meses != 12 and $dispensa != 65 and $dispensa != 63 and $dispensa != 64) {
		$meses_ativo_fp += 1;                
	}        
            
            
            $this->qnt_meses_fp = $total_meses; 
    }
        

}


?>