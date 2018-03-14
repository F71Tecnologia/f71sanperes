<?php
date_default_timezone_set('America/Sao_Paulo');
require_once('../conn.php');
require_once('../wfunction.php');
require_once('../funcoes.php');  
 
/**
 * DATA 
 */
$data = date('d/m/Y H:i:s');


/**
 * NOVA QUERY 
 * FEITA POR SINESIO LUIZ
 * EM 05/02/2016
 */
$query1 = "SELECT * FROM (
            SELECT 
                    tmp1.id_ferias,
                    tmp1.id_recisao,
                    tmp1.id_evento, 
                    tmp1.id_regiao,
                    tmp1.id_projeto,
                    tmp1.nome_projeto,
                    tmp1.id_clt, 
                    tmp1.nome,
                    B.cod_status, 
                    B.nome_status,
                    B.`data`,
                    B.data_retorno,
                    tmp1.data_ini as inicio_ferias,
                    tmp1.data_fim as fim_ferias,
                    tmp1.status AS status_atual,

                    /*VERIFICANDO SE A DATA ATUAL ESTA ENTRE ALGUM EVENTO E NAO TEM RESCISAO COM STATUS 1*/
                    if(NOW() BETWEEN B.data AND B.data_retorno AND tmp1.id_recisao IS NULL,B.cod_status,
                            /*VERIFICANDO SE O EVENTO NAO TEM DATA DE RETORNO E NAO TEM RESCISAO COM STATUS 1*/
                            if(B.data_retorno = '0000-00-00' AND tmp1.id_recisao IS NULL,B.cod_status,

                            /*VERIFANDO SE TEM RESCISÃO*/
                            if(tmp1.id_recisao IS NOT NULL,tmp1.statusRescisao,

                            /*VERIFICANDO SE TEM FERIAS E NÃO TEM RESCISAO COM STATUS 1*/
                            if(tmp1.id_ferias IS NOT NULL AND tmp1.id_recisao IS NULL,40,10)))			

                    ) as novoStatus		

                    FROM (
                            SELECT E.id_ferias, E.data_ini, E.data_fim, C.id_recisao, B.id_evento, A.id_clt, D.id_regiao, D.id_projeto, D.nome as nome_projeto, A.nome, MAX(B.`data`) AS ultimoEvento,C.motivo as statusRescisao, A.status 
                                    FROM rh_clt AS A 
                                    LEFT JOIN rh_eventos AS B ON(A.id_clt = B.id_clt AND B.`status` = 1 AND B.cod_status != 10 AND B.cod_status != 0)
                                    LEFT JOIN rh_recisao AS C ON(A.id_clt = C.id_clt AND C.`status` = 1)
                                    LEFT JOIN projeto AS D ON(A.id_projeto = D.id_projeto)
                                    LEFT JOIN rh_ferias AS E ON(A.id_clt = E.id_clt AND E.`status` = 1 AND NOW() BETWEEN E.data_ini AND E.data_fim)
                            WHERE /*A.id_projeto = 3342  AND*/ B.cod_status IS NOT NULL
                            GROUP BY B.id_clt
                    ) as tmp1
                    LEFT JOIN rh_eventos AS B ON(tmp1.id_clt = B.id_clt AND tmp1.ultimoEvento = B.`data` AND B.`status` = 1 AND B.cod_status != 10 AND B.cod_status != 0)
            ) as tmp2 WHERE status_atual <> novoStatus AND status_atual != 200 /*AND id_regiao IN(44,45,48)*/ ORDER BY id_projeto,status_atual";

$result1 = mysql_query($query1);

if (mysql_num_rows($result1) > 0) {
    while ($row1 = mysql_fetch_array($result1)) {
        
        /**
         * FAZENDO UPDATE NO rh_clt
         */
        $query2 = "UPDATE rh_clt SET status={$row1['novoStatus']} WHERE id_clt = {$row1['id_clt']}";
        
        /**
         * GRAVANDO LOG DE CRON
         */
        $mensagem = "O CRON ALTEROU O STATUS DO CLT {$row1['id_clt']} - {$row1['nome']} (SAIU DE: {$row1['status_atual']}) (FOI PARA: {$row1['novoStatus']}) EM {$data}";    
        $query3 = "INSERT INTO log_cron (id_clt,descricao) VALUES ('{$row1['id_clt']}','{$mensagem}')";
        
        /**
        * ATUALIZANDO A TABELA DE RH_CLT
        * COM A DATA ATUAL DA AÇÃO DE 
        * FINALIZAR A FOLHA
        */
        onUpdate($row1['id_clt']);
        
        if (mysql_query($query2)) {
            mysql_query($query3);
        }        
    }
}else{
    
    /**
     * GRAVANDO LOG DE CRON
     */
    $mensagem = "O CRON NÃO ALTEROU NENHUM REGISTRO EM {$data}";    
    $query3 = "INSERT INTO log_cron (descricao) VALUES ('{$mensagem}')";
    mysql_query($query3);

}

/**
 * METODO DE DEBUG
 * @param type $text
 * @return boolean
 */
function showDebug($text) {
    $debug = TRUE;
    if ($debug) {
        echo $text . "\n\n<br>";
    }
    return TRUE;
}

 