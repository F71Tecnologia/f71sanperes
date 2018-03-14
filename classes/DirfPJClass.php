<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DirfPJClass
 *
 * @author Ramon Lima
 */
class DirfPJ {
    //put your code here
    
    
    public function getDados($master,$anoBase){
        
        $sql = "SELECT A.id_prestador,A.id_regiao,A.id_projeto,C.nome,A.c_razao,A.c_cnpj,A.numero,B.id_saida,B.valor,B.data_pg,B.especifica,B.tipo,B.tipo_boleto,
                    DATE_FORMAT(data_pg, '%d/%m/%Y') AS data_pgBR
                    FROM prestadorservico AS A
                    LEFT JOIN (
                            SELECT id_saida,valor,id_prestador,data_pg,especifica,tipo,tipo_boleto 
                            FROM saida 
                            WHERE YEAR(data_pg) = {$anoBase} 
                            AND tipo_boleto = 1 
                            AND id_prestador != ''
                            -- AND especifica LIKE '%IR%'
                            ) 
                    AS B ON (A.id_prestador = B.id_prestador)
                    LEFT JOIN projeto AS C ON (A.id_projeto = C.id_projeto)

                    WHERE A.id_regiao IN (SELECT id_regiao FROM regioes WHERE id_master = {$master})
                    AND B.id_saida IS NOT NULL

                    ORDER BY A.c_razao,B.data_pg;";
        $re = mysql_query($sql);
        return $re;
    }
    
}
