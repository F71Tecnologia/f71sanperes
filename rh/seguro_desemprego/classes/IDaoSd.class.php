<?php

abstract class IDaoSd {

//    function getRelacao(Array $arr) {
//        $mes = isset($arr['mes']) ? $arr['mes'] : NULL;
//        $ano = isset($arr['ano']) ? $arr['ano'] : NULL;
//        $id_clt = isset($arr['id_clt']) ? $arr['id_clt'] : FALSE;
//        $tipo_cnpj = isset($arr['tipo_cnpj']) ? $arr['tipo_cnpj'] : FALSE;
//        $paginacao = isset($arr['pag']) ? $arr['pag'] : 0;
//        //$mes, $ano, $id_clt=FALSE
//        $and = '';
//        if ($id_clt) {
//            $and = ' AND D.id_clt=' . $id_clt;
//        }
//
//        if($tipo_cnpj=='master'){
//            $cnpj = "(SELECT cnpj FROM `master` WHERE id_master=C.id_master)AS cnpj ";
//        }else{
//            $cnpj = " C.cnpj ";
//        }
//        
//        $data_rescisao = $ano . '-' . $mes . '-01 12:00:00';
//        $sql_relaorio = "SELECT " .
//                '(SELECT A.salbase FROM rh_folha_proc AS A LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.mes = DATE_FORMAT("' . $data_rescisao . '","%m") AND A.ano = DATE_FORMAT("' . $data_rescisao . '","%Y")  AND A.status=3 AND B.terceiro=2 AND id_clt = D.id_clt ) AS sal_mes_rescisao,
//		 (SELECT A.salbase FROM rh_folha_proc AS A LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.mes = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 1 MONTH),"%m") AND A.ano = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 1 MONTH),"%Y")  AND A.status=3 AND B.terceiro=2 AND id_clt = D.id_clt ) AS sal_1_mes_anterior,
//		 (SELECT A.salbase FROM rh_folha_proc AS A LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.mes = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 2 MONTH),"%m") AND A.ano = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 2 MONTH),"%Y")  AND A.status=3 AND B.terceiro=2 AND id_clt = D.id_clt ) AS sal_2_mes_anterior,
//                 #(SELECT A.salbase FROM rh_folha_proc AS A LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.mes = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 3 MONTH),"%m") AND A.ano = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 3 MONTH),"%Y")  AND A.status=3 AND B.terceiro=2 AND id_clt = D.id_clt ) AS sal_3_mes_anterior,	 
//                 #(SELECT A.salbase FROM rh_folha_proc AS A LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.mes = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 4 MONTH),"%m") AND A.ano = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 4 MONTH),"%Y")  AND A.status=3 AND B.terceiro=2 AND id_clt = D.id_clt ) AS sal_4_mes_anterior,
//		 #(SELECT A.salbase FROM rh_folha_proc AS A LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.mes = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 5 MONTH),"%m") AND A.ano = DATE_FORMAT(DATE_SUB("' . $data_rescisao . '", INTERVAL 5 MONTH),"%Y")  AND A.status=3 AND B.terceiro=2 AND id_clt = D.id_clt ) AS sal_5_mes_anterior,'
//                . " C.nome AS nome_projeto, D.id_clt, C.id_projeto, $cnpj, D.nome, D.cpf, D.endereco, D.numero, D.bairro, D.complemento, D.cep, D.uf, D.mae, D.pis, D.campo1 AS numero_ctps, 
//                    D.serie_ctps, D.uf_ctps, F.cod AS cbo_codigo, F.nome AS nome_cbo, D.data_entrada, DATE_FORMAT(D.data_entrada, '%d%m%Y') AS data_admissao,D.data_demi, DATE_FORMAT(D.data_demi, '%d%m%Y') AS data_dispensa,
//                    IF(D.sexo='F',2,1) AS sexo, D.escolaridade, DATE_FORMAT(D.data_nasci, '%d%m%Y') AS data_nascimento, H.horas_semanais AS hora_semana, A.sal_base, PERIOD_DIFF(DATE_FORMAT(D.data_demi,'%Y%m'),DATE_FORMAT(D.data_entrada,'%Y%m')) AS meses_trabalhados, IF(A.aviso='indenizado','1' ,IF(A.aviso='trabalhado','1',' ')) AS aviso_codigo, A.aviso AS tipo_aviso,
//                    G.id_nacional AS id_banco, D.agencia, B.especifica
//                    FROM rh_recisao AS A
//                    LEFT JOIN rhstatus AS B ON A.motivo = B.codigo
//                    INNER JOIN projeto AS C ON C.id_projeto = A.id_projeto
//                    INNER JOIN rh_clt AS D ON D.id_clt = A.id_clt
//                    INNER JOIN curso AS E ON E.id_curso=A.id_curso
//                    LEFT JOIN rh_cbo AS F ON F.id_cbo = E.cbo_codigo
//                    LEFT JOIN bancos AS G ON G.id_banco=D.banco
//                    LEFT JOIN rh_horarios AS H ON (H.id_horario=D.rh_horario)
//                    WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano' AND A.motivo IN(61,66) AND A.status = 1 $and
//                    ORDER BY A.id_regiao,A.nome ASC LIMIT $paginacao,100";
//        
////        echo $sql_relaorio.'<br>';exit();
//        
//        $qr_relatorio = mysql_query($sql_relaorio) or die(mysql_error());
//        $dados = array();
//        $cnpjs = array();
//
//        while ($row = mysql_fetch_array($qr_relatorio)) {
//            $recebeu_6_meses = !empty($row['sal_1_mes_anterior']) && !empty($row['sal_2_mes_anterior']) && !empty($row['sal_3_mes_anterior']) && !empty($row['sal_4_mes_anterior']) && !empty($row['sal_5_mes_anterior']) ? '1' : '0';
//            $cnpj = $this->limpar($row['cnpj']);
//            $dados[$cnpj]['nome_projeto'] = $row['nome_projeto'];
//            $dados[$cnpj]['cnpj_projeto'] = $row['cnpj'];
//            $dados[$cnpj]['clts'][$row['id_clt']] = $row;
//            $dados[$cnpj]['clts'][$row['id_clt']]['recebeu_6_meses'] = $recebeu_6_meses;
//        }
//        return $dados;
//    }
//
    function getRelacao(Array $arr, $salarios = TRUE) {
        $mes = isset($arr['mes']) ? $arr['mes'] : NULL;
        $ano = isset($arr['ano']) ? $arr['ano'] : NULL;
        $id_clt = isset($arr['id_clt']) ? $arr['id_clt'] : FALSE;
        $tipo_cnpj = isset($arr['tipo_cnpj']) ? $arr['tipo_cnpj'] : FALSE;
//        $paginacao = isset($arr['pag']) ? $arr['pag'] : 0;
        //$mes, $ano, $id_clt=FALSE
        $and = '';
        if ($id_clt) {
            $and = ' AND D.id_clt=' . $id_clt;
        }

        if ($tipo_cnpj == 'master') {
            $cnpj = "(SELECT cnpj FROM `master` WHERE id_master=C.id_master)AS cnpj ";
        } else {
            $cnpj = " I.cnpj ";
        }

        $data_rescisao = $ano . '-' . $mes . '-01 12:00:00';
        $sql_relaorio = "SELECT 
                    C.nome AS nome_projeto, D.id_clt, C.id_projeto, $cnpj, D.nome, D.cpf, D.endereco, D.numero, D.bairro, D.complemento, D.cep, D.uf, D.mae, D.pis, D.campo1 AS numero_ctps, 
                    D.serie_ctps, D.uf_ctps, F.cod AS cbo_codigo, F.nome AS nome_cbo, D.data_entrada, DATE_FORMAT(D.data_entrada, '%d%m%Y') AS data_admissao,D.data_demi, DATE_FORMAT(D.data_demi, '%d%m%Y') AS data_dispensa,
                    IF(D.sexo='F',2,1) AS sexo, D.escolaridade, DATE_FORMAT(D.data_nasci, '%d%m%Y') AS data_nascimento, IF(H.horas_semanais IS NULL OR H.horas_semanais = 0 OR H.horas_semanais = '', E.hora_semana, H.horas_semanais) AS hora_semana, A.sal_base, PERIOD_DIFF(DATE_FORMAT(D.data_demi,'%Y%m'),DATE_FORMAT(D.data_entrada,'%Y%m')) AS meses_trabalhados, IF(A.aviso='indenizado','1' ,IF(A.aviso='trabalhado','2','1')) AS aviso_codigo, A.aviso AS tipo_aviso,
                    G.id_nacional AS id_banco, D.agencia, B.especifica, REPLACE(REPLACE(REPLACE(REPLACE(D.tel_fixo,'-',''),')',''),' ',''),'(','') AS tel_fixo
                    FROM rh_recisao AS A
                    LEFT JOIN rhstatus AS B ON A.motivo = B.codigo
                    INNER JOIN projeto AS C ON C.id_projeto = A.id_projeto
                    LEFT JOIN rhempresa AS I ON (I.id_projeto = C.id_projeto)
                    INNER JOIN rh_clt AS D ON D.id_clt = A.id_clt
                    INNER JOIN curso AS E ON E.id_curso=A.id_curso
                    LEFT JOIN rh_cbo AS F ON F.id_cbo = E.cbo_codigo
                    LEFT JOIN bancos AS G ON G.id_banco=D.banco
                    LEFT JOIN rh_horarios AS H ON (H.id_horario=D.rh_horario)
                    WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano' AND A.motivo IN(61,66,64) AND A.status = 1 $and AND A.id_regiao NOT IN (36) # para não trazer os testes
                    ORDER BY A.id_regiao,A.nome ASC ";

//        echo $sql_relaorio.'<br>';exit();

        $qr_relatorio = mysql_query($sql_relaorio) or die(mysql_error());
        $dados = array();
        $cnpjs = array();

        while ($row = mysql_fetch_assoc($qr_relatorio)) {
            $row = $this->converte_utf8($row);
            $cnpj = $this->limpar($row['cnpj']);
            $dados[$cnpj]['nome_projeto'] = $row['nome_projeto'];
            $dados[$cnpj]['cnpj_projeto'] = $row['cnpj'];
            $dados[$cnpj]['clts'][$row['id_clt']] = $row;
            $salario = $this->salario($row['id_clt'], $ano, $mes);
            foreach ($salario as $key => $value) {
                $dados[$cnpj]['clts'][$row['id_clt']]["sal_{$key}_mes_anterior"] = $value;
            }
            $recebeu_6_meses = (!empty($dados[$cnpj]['clts'][$row['id_clt']]['sal_1_mes_anterior']) && !empty($dados[$cnpj]['clts'][$row['id_clt']]['sal_2_mes_anterior']) && !empty($dados[$cnpj]['clts'][$row['id_clt']]['sal_3_mes_anterior']) && !empty($dados[$cnpj]['clts'][$row['id_clt']]['sal_4_mes_anterior']) && !empty($dados[$cnpj]['clts'][$row['id_clt']]['sal_5_mes_anterior']) ? '1' : '0');

            $dados[$cnpj]['clts'][$row['id_clt']]["sal_mes_rescisao"] = ($dados[$cnpj]['clts'][$row['id_clt']]["sal_0_mes_anterior"] >0)? $dados[$cnpj]['clts'][$row['id_clt']]["sal_0_mes_anterior"]:$row['sal_base'];
            unset($dados[$cnpj]['clts'][$row['id_clt']]["sal_0_mes_anterior"]);
            $dados[$cnpj]['clts'][$row['id_clt']]['recebeu_6_meses'] = $recebeu_6_meses;
        }
        return $dados;
    }

//    
    private function limpar($str) {
        $limpo = str_replace('.', '', $str);
        $limpo = str_replace('-', '', $limpo);
        $limpo = str_replace(')', '', $limpo);
        $limpo = str_replace('(', '', $limpo);
        $limpo = str_replace(',', '', $limpo);
        $limpo = str_replace('/', '', $limpo);
        $limpo = preg_replace("/( +)/i", " ", $limpo);
        $limpo = str_replace('\\', '', $limpo);
        return trim($limpo);
    }

    private function salario($id_clt, $ano, $mes) {
        $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $ano = str_pad($ano, 4, '0', STR_PAD_LEFT);
        $query = "SELECT /*a.id_folha,a.ano,a.mes,*/b.sallimpo
                    FROM rh_folha AS a
                    INNER JOIN rh_folha_proc AS b ON (a.id_folha = b.id_folha)
                    WHERE CONCAT(a.ano,'-',a.mes) <= '{$ano}-{$mes}' AND
                    b.status=3  AND a.terceiro=2 AND id_clt = {$id_clt} ORDER BY a.ano DESC,a.mes DESC LIMIT 6";
        $resp = mysql_query($query);
        $i = 0;
        while ($row = mysql_fetch_assoc($resp)) {
            $return[$i] = $row['sallimpo'];
            $i++;
        }
        return $return;
    }

    /* converte campos de um array em utf8 */

    private function converte_utf8($array) {
        foreach ($array as $key => $value) {
            $retorno[$key] = utf8_encode($value);
        }
        return $retorno;
    }

    public function listar($arr) {
        $sql = 'SELECT A.id_clt, A.nome, DATE_FORMAT(data_adm,"%d/%m/%Y") AS data_adm_f, DATE_FORMAT(data_demi,"%d/%m/%Y") AS data_demi_f,B.nome AS nome_projeto, A.`status` 
            FROM rh_recisao AS A 
            LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
            WHERE A.motivo IN(61,66,64) ' . $arr['cond_projeto'] . ' ' . $arr['cond_regiao'] . 
            ' AND DATE_FORMAT(A.data_demi,"%Y-%m")="' . $arr['anoSel'] . '-' . $arr['mesSel'] . 
            '" AND A.status=1 
            AND A.id_regiao NOT IN (36) # para não trazer testes
            ORDER BY nome_projeto,nome;';

        $result = mysql_query($sql);
        $dados = array();
        while ($row = mysql_fetch_array($result)) {
            $dados[] = $row;
        }
        
        return $dados;
    }

}
