<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RpaAutonomo
 *
 * @author Ramon
 */
class RpaAutonomo {
    //put your code here
    
    public function getTotalRpaCompetencia($mes,$ano,$id_projeto){
        $dados = array();
        $sql = "SELECT 
                    COUNT(id_rpa) as totalQntRpa,
                    SUM(valor) as totalValorBase,
                    SUM(base_inss) as totalBaseInss,
                    SUM(valor_inss) as totalValorInss,
                    SUM(base_ir) as totalBaseIr,
                    SUM(valor_ir) as totalValorIr,
                    SUM(valor_liquido) as totalValorLiquido,
                    SUM(valor) as totalValorBase
                FROM rpa_autonomo 
                WHERE mes_competencia = '{$mes}'
                AND ano_competencia = '{$ano}'
                AND id_projeto_pag = '{$id_projeto}'";
        $rs = mysql_query($sql);
        while($row = mysql_fetch_assoc($rs)){
            $dados[] = $row;
        }
        
        return $dados;
    }
}
