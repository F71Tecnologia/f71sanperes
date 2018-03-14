<?php

class HistoricoFinanceiro{
    
    function getCltsFerias($regiao) {
        if(!empty($_REQUEST['clt'])){
            $and = "WHERE A.id_clt = '{$_REQUEST['clt']}'";
        }else{
            $and = "WHERE A.regiao = '{$regiao}'";
        }
        
        $qry_clt = "SELECT A.id_ferias, C.nome AS nome_projeto, B.id_clt, B.matricula, B.nome AS nome_clt, B.cpf, CONCAT(A.mes, '/', A.ano) AS referencia
            FROM rh_ferias AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN projeto AS C ON(B.id_projeto = C.id_projeto)
            {$and} AND A.status = 1
            ORDER BY A.nome, A.ano, A.mes";
        $sql_clt = mysql_query($qry_clt) or die(mysql_error());                
        
        while($row_clt = mysql_fetch_assoc($sql_clt)){
            $clts[$row_clt['id_ferias']] = $row_clt;            
        }
        
//        print_array($clts);
        
        $dados = array(
            "clts"      => $clts
        );
        
        return $dados;
    }
    
    function getDadosFinanceirosFerias($id_ferias) {
        $qry = "
            SELECT A.dias_ferias, A.salario, A.salario_variavel, A.salario_extra, A.umterco, A.dias_abono_pecuniario, A.abono_pecuniario, A.umterco_abono_pecuniario, A.ir, A.inss, A.pensao_alimenticia,
                IF(
                    A.insalubridade_periculosidade > 0 ,
                    A.insalubridade_periculosidade,
                    (
                        SELECT m.valor_movimento
                        FROM rh_movimentos_clt m
                        WHERE 
                            m.status 
                            AND m.cod_movimento IN ('6006', '6007', '50251', '90080') 
                            AND (
                                m.id_ferias=A.id_ferias
                                OR (
                                    m.mes_mov=17 
                                    AND m.id_clt=A.id_clt 
                                    AND DATE_FORMAT(A.data_ini,'%Y%m') BETWEEN DATE_FORMAT(DATE_SUB(m.data_movimento, INTERVAL 1 MONTH),'%Y%m') AND DATE_FORMAT(DATE_ADD(m.data_movimento, INTERVAL 1 MONTH),'%Y%m')
                                    )
                                )
                        LIMIT 1
                    )
                ) insalubridade, 
                IF(
                    A.adiantamento13 > 0, 
                    A.adiantamento13,
                    (
                        SELECT m.valor_movimento
                        FROM rh_movimentos_clt m
                        WHERE 
                            m.status 
                            AND m.cod_movimento='80030' 
                            AND (
                                m.id_ferias=A.id_ferias
                                OR (
                                    m.mes_mov=17 
                                    AND m.id_clt=A.id_clt 
                                    AND DATE_FORMAT(A.data_ini,'%Y%m') BETWEEN DATE_FORMAT(DATE_SUB(m.data_movimento, INTERVAL 1 MONTH),'%Y%m') AND DATE_FORMAT(DATE_ADD(m.data_movimento, INTERVAL 1 MONTH),'%Y%m')
                                    )
                                )
                        LIMIT 1
                    )
                ) adiantamento13
            FROM rh_ferias AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            WHERE A.id_ferias = {$id_ferias} AND A.status = 1";
        $sql = mysql_query($qry) or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        
//        print_array($row);
        
        $dados = array();
        
        $dados[1] = array(
            "movimento" => "Salário",
            "qtd" => $row['dias_ferias']." dia(s)",
            "valor" => ($row['salario'] / 30) * $row['dias_ferias']
        );
        
        $dados[2] = array(
            "movimento" => "Salário Variável",
            "valor" => $row['salario_variavel']
        );
        
        $dados[3] = array(
            "movimento" => "Salário Extra",
            "valor" => $row['salario_extra']
        );
        
        $dados[4] = array(
            "movimento" => "Insalubridade",
            "valor" => $row['insalubridade']
        );
        
        $dados[5] = array(
            "movimento" => "1/3 Acréscimo Constitucional",
            "valor" => $row['umterco']
        );
        
        $dados[6] = array(
            "movimento" => "Abono Pecuniário",
            "qtd" => "{$row['dias_abono_pecuniario']} dia(s)",
            "valor" => $row['abono_pecuniario']
        );
        
        $dados[7] = array(
            "movimento" => "1/3 Abono Pecuniário",
            "valor" => $row['umterco_abono_pecuniario']
        );
        
        $dados[8] = array(
            "movimento" => "Adiantamento de 13º",
            "valor" => $row['adiantamento13']
        );
        
        $dados[9] = array(
            "movimento" => "IRRF",
            "valor" => $row['ir']
        );
        
        $dados[10] = array(
            "movimento" => "INSS",
            "valor" => $row['inss']
        );
        
        $dados[11] = array(
            "movimento" => "Pensão Alimentícia",
            "valor" => $row['pensao_alimenticia']
        );                
        
//        echo "<br>### MOVIMENTAÇÕES: ###";
//        print_array($dados);
        
        return $dados;
    }
    
}

?>