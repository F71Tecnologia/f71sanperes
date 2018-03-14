<?php

class montaCaged {

    public $mes;
    public $ano;
    public $projeto;
    public $id_master;
    public $sequencial;
    public $meseano;
    public $data_referencia;
    public $identificador;
    public $numidentificador;
    public $matriz;

    public function __construct($mes, $ano, $idMaster) {
        $this->mes = $mes;
        $this->ano = $ano;
        $this->data_referencia = $this->ano . '-' . $this->mes . '-01';
        $this->meseano = $this->ano . '-' . $this->mes;
        $this->id_master = $idMaster;
        
       

    }

    function limpaData($data) {
        return implode('', array_reverse(explode('-', $data)));
    }
    
    public function consultaTodosTrabalhadoresAdm() {
        $qr_todosTrabalhadoresAdm = "/**CONSULTA DOS ADMITIDOS**/
                                    SELECT * FROM
                                            (SELECT IF(temp.total > 0,'tem','nao tem') AS status_transf, A.id_clt,C.cbo_codigo, C.salario, D.horas_semanais, A.data_entrada, A.status_admi, A.data_demi, A.`status`, 
                                                F.id_regiao, F.regiao, B.id_projeto, E.cnpj, B.nome AS projeto_atual, A.nome,
                                                REPLACE(REPLACE(A.pis,'.',''), '-', '') AS pis_limpo,
                                                REPLACE(REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                IF(A.sexo = 'M', 1, 2) AS sexo,
                                                A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia, 
                                                REPLACE(A.cep, '-', '') as cep_limpo
                                            FROM rh_clt AS A 
                                            LEFT JOIN (SELECT COUNT(T.id_clt) AS total, T.id_clt FROM rh_transferencias AS T GROUP BY T.id_clt) AS temp ON (A.id_clt = temp.id_clt)
                                            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                                            LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao)
                                            LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                                            LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario)
                                            LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto)
                                            LEFT JOIN etnias AS G ON (A.etnia = G.id)
                                            WHERE MONTH(A.data_entrada) = '$this->mes' AND YEAR(A.data_entrada) = '$this->ano' AND B.id_master = '$this->id_master' GROUP BY A.id_clt
                                            HAVING status_transf = 'nao tem') AS ADM ORDER BY ADM.id_projeto;
                                            ";

        $result = mysql_query($qr_todosTrabalhadoresAdm);
        return $result;
    }
    
    public function consultaTodosTrabalhadoresTransfEntrada(){
        $qr_todosTrabalhadoresTransfEntrada = "/**CONSULTA TRANSFERECNIAS DE ENTRADA**/
                                                    SELECT temp.*,D1.id_regiao, D1.regiao,  C1.cnpj, B1.nome AS projeto_atual, A1.nome,
                                                            REPLACE(REPLACE(A1.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A1.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                            IF(A1.sexo = 'M', 1, 2) AS sexo,
                                                            A1.data_nasci, A1.escolaridade, A1.campo1, A1.serie_ctps, A1.uf_ctps, E1.cod AS etnia, A1.deficiencia, 
                                                            REPLACE(A1.cep, '-', '') as cep_limpo
                                                    FROM (SELECT A.id_clt,C.cbo_codigo,C.salario,D.horas_semanais, 
                                                                 if(MONTH(A.data_entrada)<= '$this->mes' AND YEAR(A.data_entrada)<='$this->ano', A.data_entrada, CONCAT(DATE_FORMAT(B.data_proc, '%Y-%m'),'-01')) AS data_entrada, 
                                                                 if(MONTH(A.data_entrada)<= '$this->mes' AND YEAR(A.data_entrada)<='$this->ano',A.status_admi,'70') AS status_admi,
                                                                 A.data_demi, A.status, B.data_proc,
                                                                 if(B.data_proc >= '$this->data_referencia', B.id_projeto_de, B.id_projeto_para) AS id_projeto
                                                         FROM rh_clt AS A
                                                         LEFT JOIN rh_transferencias AS B ON(A.id_clt = B.id_clt)
                                                         LEFT JOIN curso AS C ON(B.id_curso_de = C.id_curso)
                                                         LEFT JOIN rh_horarios AS D ON(C.id_horario = D.id_horario)
                                                         WHERE B.data_proc >= '$this->data_referencia' AND (A.data_entrada <= '$this->data_referencia' OR (A.data_entrada > '$this->data_referencia' AND A.data_entrada < DATE_ADD('$this->data_referencia', INTERVAL 1 MONTH))) GROUP BY A.id_clt) AS temp
                                                    LEFT JOIN rh_clt AS A1 ON (A1.id_clt = temp.id_clt)
                                                    LEFT JOIN projeto AS B1 ON (B1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN regioes AS D1 ON (D1.id_regiao = B1.id_regiao)
                                                    LEFT JOIN rhempresa AS C1 ON (C1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN etnias AS E1 ON (A1.etnia = E1.id)
                                                    WHERE B1.id_master = '$this->id_master' AND DATE_FORMAT(temp.data_entrada,'%Y-%m') = '$this->meseano'
                                                    ORDER BY temp.id_projeto, temp.data_proc;            
                                                    ";

        $result = mysql_query($qr_todosTrabalhadoresTransfEntrada);
        return $result;        
    }
    
    public function consultaTodosTrabalhadoresTransfSaida(){
        $qr_todosTrabalhadoresTransfSaida = "/**CONSULTA TRANSFERECNIAS DE SAIDA**/
                                                    SELECT temp.*, D1.id_regiao, D1.regiao, C1.cnpj,B1.nome AS projeto_atual, A1.nome,
                                                            REPLACE(REPLACE(A1.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A1.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                            IF(A1.sexo = 'M', 1, 2) AS sexo,
                                                            A1.data_nasci, A1.escolaridade, A1.campo1, A1.serie_ctps, A1.uf_ctps, E1.cod AS etnia, A1.deficiencia, 
                                                            REPLACE(A1.cep, '-', '') as cep_limpo
                                                    FROM (SELECT A.id_clt, C.cbo_codigo, C.salario,D.horas_semanais, 
                                                                 A.data_entrada, A.status_admi, 
                                                                 if(B.data_proc < A.data_demi OR A.data_demi IS NULL,CONCAT(DATE_FORMAT (DATE_ADD(B.data_proc, INTERVAL 1 MONTH), '%Y-%m'),'-01'),A.data_demi) AS data_demi, 
                                                                 if(B.data_proc < A.data_demi OR A.data_demi IS NULL, '80', A.status) AS status, 
                                                                 B.data_proc,
                                                                 if(B.data_proc < '$this->data_referencia', B.id_projeto_de, B.id_projeto_para) AS id_projeto
                                                           FROM rh_clt AS A
                                                           LEFT JOIN rh_transferencias AS B ON(A.id_clt = B.id_clt)
                                                           LEFT JOIN curso AS C ON(B.id_curso_de = C.id_curso)
                                                           LEFT JOIN rh_horarios AS D ON(C.id_horario = D.id_horario)
                                                           WHERE B.data_proc < '$this->data_referencia' AND (A.data_entrada <= '$this->data_referencia' OR (A.data_entrada > '$this->data_referencia' AND A.data_entrada < DATE_ADD('$this->data_referencia', INTERVAL 1 MONTH))) GROUP BY A.id_clt) AS temp
                                                    LEFT JOIN rh_clt AS A1 ON (A1.id_clt = temp.id_clt)
                                                    LEFT JOIN projeto AS B1 ON (B1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN regioes AS D1 ON (D1.id_regiao = B1.id_regiao)
                                                    LEFT JOIN rhempresa AS C1 ON (C1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN etnias AS E1 ON (A1.etnia = E1.id)
                                                    WHERE B1.id_master = '$this->id_master' AND DATE_FORMAT(temp.data_demi,'%Y-%m') = '$this->meseano'
                                                    ORDER BY temp.id_projeto, temp.data_proc;
                                                    ";

        $result = mysql_query($qr_todosTrabalhadoresTransfSaida);
        return $result;            
    }

    public function consultaTodosTrabalhadoresDemi() {
            $qr_todosTrabalhadoresDemi =   "/**CONSULTA DEMITIDOS**/
                                                SELECT * FROM
                                                    (SELECT IF(temp.total > 0,'tem','nao tem') AS status_transf, A.id_clt,C.cbo_codigo, C.salario, 
                                                            D.horas_semanais, A.data_entrada, A.status_admi, A.data_demi, A.`status`, F.id_regiao, F.regiao, B.id_projeto, E.cnpj, B.nome AS projeto_atual, A.nome,
                                                            REPLACE(REPLACE(A.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                            IF(A.sexo = 'M', 1, 2) AS sexo,
                                                            A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia, 
                                                            REPLACE(A.cep, '-', '') as cep_limpo
                                                    FROM rh_clt AS A 
                                                    LEFT JOIN (SELECT COUNT(T.id_clt) AS total, T.id_clt FROM rh_transferencias AS T GROUP BY T.id_clt) AS temp ON (A.id_clt = temp.id_clt)
                                                    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                                                    LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao)
                                                    LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                                                    LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario)
                                                    LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto)
                                                    LEFT JOIN etnias AS G ON (A.etnia = G.id)
                                                    WHERE MONTH(A.data_demi) = '$this->mes' AND YEAR(A.data_demi) = '$this->ano' AND B.id_master = '$this->id_master' GROUP BY A.id_clt
                                                    HAVING status_transf = 'nao tem') AS DEMI ORDER BY DEMI.id_projeto;
                                                    ";

            $result = mysql_query($qr_todosTrabalhadoresDemi);
            return $result;
    }

    public function consultaAlgunsTrabalhadoresAdm($idsclts) {
    $ids_clt = implode(',', $idsclts);
        $qr_algunsTrabalhadoresAdm = "/**CONSULTA DOS ADMITIDOS**/
                                                SELECT * FROM 
                                                        (SELECT IF(temp.total > 0,'tem','nao tem') AS status_transf, A.id_clt,C.cbo_codigo, C.salario, D.horas_semanais, A.data_entrada, A.status_admi, A.data_demi, A.`status`,
                                                            F.id_regiao, F.regiao, B.id_projeto, E.cnpj, B.nome AS projeto_atual, A.nome,
                                                            REPLACE(REPLACE(A.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo, IF(A.sexo = 'M', 1, 2) AS sexo, A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia,
                                                            REPLACE(A.cep, '-', '') AS cep_limpo
                                                        FROM rh_clt AS A
                                                        LEFT JOIN (SELECT COUNT(T.id_clt) AS total, T.id_clt FROM rh_transferencias AS T GROUP BY T.id_clt) AS temp ON (A.id_clt = temp.id_clt)
                                                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                                                        LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao)
                                                        LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                                                        LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario)
                                                        LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto)
                                                        LEFT JOIN etnias AS G ON (A.etnia = G.id)
                                                        WHERE MONTH(A.data_entrada) = '$this->mes' AND YEAR(A.data_entrada) = '$this->ano' AND B.id_master = '$this->id_master' AND A.id_clt NOT IN ($ids_clt)
                                                        GROUP BY A.id_clt
                                                        HAVING status_transf = 'nao tem') AS ADM
                                                        ORDER BY ADM.id_projeto;
                                                        ";

        $result = mysql_query($qr_algunsTrabalhadoresAdm);
        return $result;
    }
    
    public function consultaAlgunsTrabalhadoresTransfEntrada($idsclts){
        $ids_clt = implode(',', $idsclts);
        $qr_algunsTrabalhadoresTransfEntrada = "/**CONSULTA TRANSFERECNIAS DE ENTRADA**/
                                                    SELECT temp.*,D1.id_regiao, D1.regiao,C1.cnpj, B1.nome AS projeto_atual, A1.nome,
                                                            REPLACE(REPLACE(A1.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A1.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                            IF(A1.sexo = 'M', 1, 2) AS sexo,
                                                            A1.data_nasci, A1.escolaridade, A1.campo1, A1.serie_ctps, A1.uf_ctps, E1.cod AS etnia, A1.deficiencia, 
                                                            REPLACE(A1.cep, '-', '') as cep_limpo
                                                    FROM (SELECT A.id_clt, C.cbo_codigo, C.salario,D.horas_semanais, 
                                                                 if(MONTH(A.data_entrada)<= '$this->mes' AND YEAR(A.data_entrada)<='$this->ano', A.data_entrada, CONCAT(DATE_FORMAT(B.data_proc, '%Y-%m'),'-01')) AS data_entrada, 
                                                                 if(MONTH(A.data_entrada)<= '$this->mes' AND YEAR(A.data_entrada)<='$this->ano',A.status_admi,'70') AS status_admi,
                                                                 A.data_demi, A.status, B.data_proc,
                                                                 if(B.data_proc >= '$this->data_referencia', B.id_projeto_de, B.id_projeto_para) AS id_projeto
                                                         FROM rh_clt AS A
                                                         LEFT JOIN rh_transferencias AS B ON(A.id_clt = B.id_clt)
                                                         LEFT JOIN curso AS C ON(B.id_curso_de = C.id_curso)
                                                         LEFT JOIN rh_horarios AS D ON(C.id_horario = D.id_horario)
                                                         WHERE B.data_proc >= '$this->data_referencia' AND (A.data_entrada <= '$this->data_referencia' OR (A.data_entrada > '$this->data_referencia' AND A.data_entrada < DATE_ADD('$this->data_referencia', INTERVAL 1 MONTH))) GROUP BY A.id_clt) AS temp
                                                    LEFT JOIN rh_clt AS A1 ON (A1.id_clt = temp.id_clt)
                                                    LEFT JOIN projeto AS B1 ON (B1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN regioes AS D1 ON (D1.id_regiao = B1.id_regiao)
                                                    LEFT JOIN rhempresa AS C1 ON (C1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN etnias AS E1 ON (A1.etnia = E1.id)
                                                    WHERE B1.id_master = '$this->id_master' AND DATE_FORMAT(temp.data_entrada,'%Y-%m') = '$this->meseano' AND A1.id_clt NOT IN ($ids_clt)
                                                    ORDER BY temp.id_projeto, temp.data_proc;            
                                                    ";
//                $result = print_r($qr_algunsTrabalhadoresTransfEntrada);
//        exit;
        $result = mysql_query($qr_algunsTrabalhadoresTransfEntrada);
        return $result;        
    }
    
    public function consultaAlgunsTrabalhadoresTransfSaida($idsclts){
        $ids_clt = implode(',', $idsclts);
        $qr_algunsTrabalhadoresTransfSaida = "/**CONSULTA TRANSFERECNIAS DE SAIDA**/
                                                    SELECT temp.*, D1.id_regiao, D1.regiao, C1.cnpj,B1.nome AS projeto_atual, A1.nome,
                                                            REPLACE(REPLACE(A1.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A1.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                            IF(A1.sexo = 'M', 1, 2) AS sexo,
                                                            A1.data_nasci, A1.escolaridade, A1.campo1, A1.serie_ctps, A1.uf_ctps, E1.cod AS etnia, A1.deficiencia, 
                                                            REPLACE(A1.cep, '-', '') as cep_limpo
                                                    FROM (SELECT A.id_clt,C.cbo_codigo, C.salario,D.horas_semanais, 
                                                                 A.data_entrada, A.status_admi, 
                                                                 if(B.data_proc < A.data_demi OR A.data_demi IS NULL,CONCAT(DATE_FORMAT (DATE_ADD(B.data_proc, INTERVAL 1 MONTH), '%Y-%m'),'-01'),A.data_demi) AS data_demi, 
                                                                 if(B.data_proc < A.data_demi OR A.data_demi IS NULL, '80', A.status) AS status, 
                                                                 B.data_proc,
                                                                 if(B.data_proc < '$this->data_referencia', B.id_projeto_de, B.id_projeto_para) AS id_projeto
                                                           FROM rh_clt AS A
                                                           LEFT JOIN rh_transferencias AS B ON(A.id_clt = B.id_clt)
                                                           LEFT JOIN curso AS C ON(B.id_curso_de = C.id_curso)
                                                           LEFT JOIN rh_horarios AS D ON(C.id_horario = D.id_horario)
                                                           WHERE B.data_proc < '$this->data_referencia' AND (A.data_entrada <= '$this->data_referencia' OR (A.data_entrada > '$this->data_referencia' AND A.data_entrada < DATE_ADD('$this->data_referencia', INTERVAL 1 MONTH))) GROUP BY A.id_clt) AS temp
                                                    LEFT JOIN rh_clt AS A1 ON (A1.id_clt = temp.id_clt)
                                                    LEFT JOIN projeto AS B1 ON (B1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN regioes AS D1 ON (D1.id_regiao = B1.id_regiao)
                                                    LEFT JOIN rhempresa AS C1 ON (C1.id_projeto = temp.id_projeto)
                                                    LEFT JOIN etnias AS E1 ON (A1.etnia = E1.id)
                                                    WHERE B1.id_master = '$this->id_master' AND DATE_FORMAT(temp.data_demi,'%Y-%m') = '$this->meseano' AND A1.id_clt NOT IN ($ids_clt)
                                                    ORDER BY temp.id_projeto, temp.data_proc;
                                                    ";

        $result = mysql_query($qr_algunsTrabalhadoresTransfSaida);
        return $result;            
    }

    public function consultaAlgunsTrabalhadoresDemi($idsclts) {
        $ids_clt = implode(',', $idsclts);
            $qr_algunsTrabalhadoresDemi =   "/**CONSULTA DEMITIDOS**/
                                                SELECT * FROM
                                                    (SELECT IF(temp.total > 0,'tem','nao tem') AS status_transf, A.id_clt,C.cbo_codigo, C.salario, 
                                                            D.horas_semanais, A.data_entrada, A.status_admi, A.data_demi, A.`status`, F.id_regiao, F.regiao, B.id_projeto, E.cnpj, B.nome AS projeto_atual, A.nome,
                                                            REPLACE(REPLACE(A.pis,'.',''), '-', '') AS pis_limpo,
                                                            REPLACE(REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo,
                                                            IF(A.sexo = 'M', 1, 2) AS sexo,
                                                            A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia, 
                                                            REPLACE(A.cep, '-', '') as cep_limpo
                                                    FROM rh_clt AS A 
                                                    LEFT JOIN (SELECT COUNT(T.id_clt) AS total, T.id_clt FROM rh_transferencias AS T GROUP BY T.id_clt) AS temp ON (A.id_clt = temp.id_clt)
                                                    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                                                    LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao)
                                                    LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                                                    LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario)
                                                    LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto)
                                                    LEFT JOIN etnias AS G ON (A.etnia = G.id)
                                                    WHERE MONTH(A.data_demi) = '$this->mes' AND YEAR(A.data_demi) = '$this->ano' AND B.id_master = '$this->id_master' AND A.id_clt NOT IN ($ids_clt) GROUP BY A.id_clt
                                                    HAVING status_transf = 'nao tem') AS DEMI ORDER BY DEMI.id_projeto;
                                                    ";
//                $result = print_r($qr_algunsTrabalhadoresDemi);
//        exit;
            $result = mysql_query($qr_algunsTrabalhadoresDemi);
            return $result;
    }

    public function montaMatrizForm(array $dados, $tipo){
        $this->matriz[$dados['id_projeto']][$dados['id_clt']][$tipo]= $dados;//[$dados['cnpj']][$dados['projeto_atual']][$dados['id_clt']][$dados['nome']]=$dados['data_entrada'];
    }
    
    public function getMatrizForm(){
        return $this->matriz;
    }


    public function consultaTotalEstabelecimentos() {
        $qr_estabelecimentos = "SELECT COUNT(cnpj) AS estabelecimentos 
                                FROM (
                                SELECT B.cnpj FROM rh_clt AS A
                                LEFT JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto
                                )
                                WHERE A.id_clt IN (7145,7706,7740,7698,6938,5045,7706,7415,7728) GROUP BY B.cnpj) AS temp";

        $result = mysql_query($qr_estabelecimentos);
        return $result;
    }

    public function consultaEmpresa($idRegiaoTransf, $idProjetoTransf) {
        $qr_empresa = "SELECT cnpj, razao,  logradouro, numero, complemento, cep, tel, bairro, uf, email, cnae
                       FROM rhempresa 
                       WHERE id_regiao = '$idRegiaoTransf' AND id_projeto = '$idProjetoTransf'";
        $result = mysql_query($qr_empresa);
        return $result;
    }

    public function consultaTotalTrabPorProjeto($idsProjetoClt) {
        $qr_total_clt = "SELECT COUNT(qr_admitidos.id_clt) AS qnt, D.id_regiao AS id_regiao_transferencia, D.regiao AS nome_regiao, E.id_projeto AS id_projeto_transferencia,E.nome AS nome_projeto
                        FROM
                         (
                        SELECT A.id_curso,A.id_clt, A.id_regiao, A.id_projeto, 
                         A.nome AS nome_clt,A.data_entrada,A.status_demi,A.data_demi,
                         /*TRANSFERENCIAS*/
                         (
                        SELECT id_regiao_de
                        FROM rh_transferencias
                        WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc >= '$this->data_referencia'
                        ORDER BY id_transferencia ASC
                        LIMIT 1) AS regiao_de,
                         (
                        SELECT id_regiao_para
                        FROM rh_transferencias
                        WHERE id_clt = A.id_clt AND id_regiao_de <> id_regiao_para AND data_proc <= '$this->data_referencia'
                        ORDER BY id_transferencia DESC
                        LIMIT 1) AS regiao_para,
                         (
                        SELECT id_projeto_de
                        FROM rh_transferencias
                        WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc >= '$this->data_referencia'
                        ORDER BY id_transferencia ASC
                        LIMIT 1) AS projeto_de,
                         (
                        SELECT id_projeto_para
                        FROM rh_transferencias
                        WHERE id_clt = A.id_clt AND id_projeto_de <> id_projeto_para AND data_proc <= '$this->data_referencia'
                        ORDER BY id_transferencia DESC
                        LIMIT 1) AS projeto_para
                        FROM rh_clt AS A
                        INNER JOIN curso AS D ON D.id_curso = A.id_curso
                        INNER JOIN rhempresa AS E ON E.id_projeto = A.id_projeto
                        WHERE A.data_entrada < '$this->data_referencia' AND (A.data_demi > '$this->data_referencia' OR A.data_demi = '0000-00-00' OR A.data_demi IS NULL)) AS qr_admitidos
                        LEFT JOIN regioes AS D ON (IF(qr_admitidos.regiao_para IS NOT NULL,D.id_regiao = qr_admitidos.regiao_para, IF(qr_admitidos.regiao_de IS NOT NULL,D.id_regiao = qr_admitidos.regiao_de,D.id_regiao = qr_admitidos.id_regiao)))
                        LEFT JOIN projeto AS E ON (IF(qr_admitidos.projeto_para IS NOT NULL,E.id_projeto = qr_admitidos.projeto_para, IF(qr_admitidos.projeto_de IS NOT NULL,E.id_projeto = qr_admitidos.projeto_de,E.id_projeto = qr_admitidos.id_projeto)))
                        WHERE D.id_master = '$this->id_master' AND E.id_projeto IN($idsProjetoClt)";
        $result = mysql_query($qr_total_clt);
        return $result;
    }

    public function montaRegTipoA($arquivo, $master, $estabelecimentos, $idsclts) {

        $tipoRegistro = sprintf('%-01s', 'A');            // DEFINE O REGISTRO QUE SERÁ INFORMADO
        fwrite($arquivo, $tipoRegistro, 1);

        $layout = sprintf('%-05s', 'L2009');
        fwrite($arquivo, $layout, 5);

        $espacos1 = NULL;
        $espacos1 = sprintf("%3s", $espacos1);
        fwrite($arquivo, $espacos1, 3);

        $competencia = $this->mes . $this->ano;
        fwrite($arquivo, $competencia, 6);

        $alteracao = sprintf('%01s', '2');              // 1 - ND A ALTERAR    2 - ALTERAR OS DADOS CADASTRAIS
        fwrite($arquivo, $alteracao, 1);

        $this->sequencial = 1;
        $sequencial = sprintf("%05s", $this->sequencial);
        fwrite($arquivo, $sequencial, 5);

        $identificador = sprintf('%01s', '1');         // 1 - CNPJ    2 - CEI
        fwrite($arquivo, $identificador, 1);

        $numIdentificador = sprintf('%014s', RemoveCaracteres($master['cnpj'])); // NUMERO IDENTIFICADOR DO ESTABELECIMENTO      
        fwrite($arquivo, $numIdentificador);

        $razao = sprintf("%-35s", substr(RemoveAcentos(RemoveCaracteres($master['razao'])), 0, 35)); // NOME OU RAZÃO SOCIAL 35 POSIÇÕES
        fwrite($arquivo, $razao, 35);

        $endereco = sprintf("%-40s", substr(RemoveCaracteres($master['logradouro']), 0, 40)); // ENDEREÇO 40 POSIÇÕES
        fwrite($arquivo, $endereco, 40);

        $cep = sprintf('%08s', RemoveEspacos(RemoveCaracteres($master['cep'])));
        fwrite($arquivo, $cep, 8);

        $uf = sprintf('%-02s', $master['uf']); //UF DUAS POSIÇÕES 'RJ';
        fwrite($arquivo, $uf, 2);

        $ddd = sprintf('%04s', RemoveCaracteres(substr($master['telefone'], 0, 4))); // DDD COM 4 POSIÇÕES
        fwrite($arquivo, $ddd, 4);

        $telefone = sprintf('%08s', RemoveEspacos(RemoveCaracteres(substr($master['telefone'], 4))));
        fwrite($arquivo, $telefone, 8);

        $ramal = sprintf('%05s', '0');
        fwrite($arquivo, $ramal, 5);

        $totalEstabelecimentos = sprintf('%05s', $estabelecimentos);
        fwrite($arquivo, $totalEstabelecimentos, 5);

        $movimentos = sprintf('%05s', $idsclts); //  QUANTIDADE DE REGITRO TIPO C
        fwrite($arquivo, $movimentos, 5);

        $espacos2 = NULL;
        $espacos2 = sprintf("%92s", $espacos2);
        fwrite($arquivo, $espacos2, 92);

        fwrite($arquivo, "\r\n");
        $this->sequencial++;
    }

    public function montaRegTipoB($arquivo, $empresa, $totalDeEmpregados) {

        $tipoReg = sprintf('%-01s', 'B');
        fwrite($arquivo, $tipoReg, 1);

        $this->identificador = sprintf('%01s', 1);
        fwrite($arquivo, $this->identificador, 1);

        $this->numIdentificador = sprintf('%014s', RemoveCaracteres($empresa['cnpj']));
        fwrite($arquivo, $this->numIdentificador, 14);

        $sequencial = sprintf('%05s', $this->sequencial);
        fwrite($arquivo, $sequencial, 5);

        $primeira = sprintf('%01s', 2);          // 1 - PRIMEIRA DECLARAÇÃO     2 - JÁ INFORMADO
        fwrite($arquivo, $primeira, 1);

        $alteracao = sprintf('%01s', 2);         // 1 -NADA A ATUALIZAR; 2 - ALTERAR DADOS CADASTRAIS DO ESTABELECIMENTO; 3 - FECHAMENTO DO ESTABELECIMENTO 
        fwrite($arquivo, $alteracao, 1);

        $cep = sprintf('%08s', RemoveEspacos(RemoveCaracteres($empresa['cep'])));
        fwrite($arquivo, $cep, 8);

        $espacos3 = NULL;
        $espacos3 = sprintf("%5s", $espacos3);
        fwrite($arquivo, $espacos3, 5);

        $razao = sprintf('%-40s', substr(RemoveCaracteres($empresa['razao']), 0, 40));
        fwrite($arquivo, $razao, 40);

        $endereco = sprintf('%-40s', substr(RemoveEspacos(RemoveCaracteres($empresa['logradouro'])) . RemoveEspacos(RemoveCaracteres($empresa['numero'])) . RemoveEspacos(RemoveCaracteres($empresa['complemento'])), 0, 40));
        fwrite($arquivo, $endereco, 40);

        $bairro = sprintf('%-20s', substr($empresa['bairro'], 0, 20));
        fwrite($arquivo, $bairro, 20);

        $uf = sprintf('%-02s', $empresa['uf']);
        fwrite($arquivo, $uf, 2);

        $totalEmpregados = sprintf('%05s', $totalDeEmpregados);
        fwrite($arquivo, $totalEmpregados, 5);

        $porte = sprintf('%01s', '2');
        fwrite($arquivo, $porte, 1);

        $cnae = sprintf('%07s', RemoveCaracteres($empresa['cnae']));
        fwrite($arquivo, $cnae, 7);

        $ddd = sprintf('%04s', RemoveCaracteres(substr($empresa['tel'], 0, 4))); // DDD COM 4 POSIÇÕES
        fwrite($arquivo, $ddd, 4);

        $telefone = sprintf('%08s', RemoveEspacos(RemoveCaracteres(substr($empresa['tel'], 4))));
        fwrite($arquivo, $telefone, 8);

        $email = sprintf('%-50s', $empresa['email']);
        fwrite($arquivo, $email, 50);

        $espacos4 = NULL;
        $espacos4 = sprintf("%27s", $espacos4);
        fwrite($arquivo, $espacos4, 27);

        fwrite($arquivo, "\r\n");
        $this->sequencial++;
    }

    public function montaRegTipoC($arquivo, $clt, $tipo) {

        $tipoReg = sprintf('%-01s', 'C');
        fwrite($arquivo, $tipoReg, 1);

        fwrite($arquivo, $this->identificador, 1);
        fwrite($arquivo, $this->numIdentificador, 14);

        $sequencial = sprintf('%05s', $this->sequencial);
        fwrite($arquivo, $sequencial, 5);

        $pis = sprintf('%011s', substr($clt['pis_limpo'], 0, 11));
        fwrite($arquivo, $pis, 11);

        $sexo = sprintf('%-01s', $clt['sexo']);
        fwrite($arquivo, $sexo, 1);

        $dtNascimento = sprintf('%08s', $this->limpaData($clt['data_nasci']));
        fwrite($arquivo, $dtNascimento, 8);

        $grauInstrucao = sprintf('%02s', $clt['escolaridade']);
        fwrite($arquivo, $grauInstrucao, 2);

        $espacos5 = NULL;
        $espacos5 = sprintf("%4s", $espacos5);
        fwrite($arquivo, $espacos5, 4);

        $salario = sprintf('%08s', RemoveCaracteres($clt['salario']));
        fwrite($arquivo, $salario, 8);

        $horario = sprintf('%02s', 40);
        fwrite($arquivo, $horario, 2);
        
        $dtAdmissao = sprintf('%08s', $this->limpaData($clt['data_entrada']));
        fwrite($arquivo, $dtAdmissao, 8);
//        $dtAdmissao = sprintf('%08s', $this->limpaData($clt['data_entrada']));
//        $movimento = sprintf('%02s', $clt['status_admi']);

        // DAQUI
 //    $status_demi = array(60, 61, 62, 65, 66, 81, 100, 80, 63);
        $codigos_desligamento = array(61 => 31, 64 => 31, 60 => 32, 63 => 40, 65 => 40, 66 => 43, 101 => 50, 81 => 60);
         if ((strcmp($tipo, "DEMI") == 0) || (strcmp($tipo, "SAIDA") == 0)) {
            if($clt['status'] == 80){
                $cod_movimentacao = 80;
            }else {
                $cod_movimentacao = $codigos_desligamento[$clt['status']];
                if (empty($cod_movimentacao)) // qualquer outro tipo de desligamento que não seja os listados acima 
                    $cod_movimentacao = 31;  // será tratado como DISPENSA SEM JUSTA CAUSA 
            }
            $movimento = sprintf('%02s', $cod_movimentacao);
        }else {
            $movimento = sprintf('%02s', $clt['status_admi']);
        }
        fwrite($arquivo, $movimento, 2);

        $dia = explode("-", $clt['data_demi']);
        if ($this->mes == $dia[1] and $this->ano == $dia[0] and ((strcmp($tipo, "DEMI") == 0) || (strcmp($tipo, "SAIDA") == 0))) {
            $dia_saida = $dia[2];
            $desligamento = sprintf('%02s', $dia_saida);
        } else {
            $dia_saida = NULL;
            $desligamento = sprintf('%-2s', $dia_saida);
        }
        //    $dia_saida = ($this->mes == $dia[1] and $this->ano == $dia[0] and $clt['movimento'] == 'DEMITIDO') ? $dia[2] : NULL;
        //       $desligamento = sprintf('%-02s', $dia_saida);
        fwrite($arquivo, $desligamento, 2);
// até aqui
        
        $nome = sprintf('%-40s', substr($clt['nome'], 0, 40));
        fwrite($arquivo, $nome, 40);

        $ctps = sprintf("%08s", RemoveEspacos(RemoveCaracteres($clt['campo1'])));
        fwrite($arquivo, $ctps, 8);
        // VER ISSO
        $serieCtps = sprintf('%04s', substr(RemoveCaracteres($clt['serie_ctps']), -4));
        fwrite($arquivo, $serieCtps, 4);

        $espacos6 = NULL;
        $espacos6 = sprintf("%7s", $espacos6);
        fwrite($arquivo, $espacos6, 7);

        $raca = sprintf('%01s', substr($clt['etnia'], 1));
        fwrite($arquivo, $raca, 1);
        // verificando deficiencia
        if (empty($clt['deficiencia'])) {
            $deficiencia = sprintf('%01s', '2');
            $tipoDeficiencia = sprintf('%01s', '0');
        } else {
            $deficiencia = sprintf('%01s', '1');
            $qr_deficiencia = mysql_query("SELECT cod FROM deficiencias WHERE id = '{$clt['deficiencia']}'");
            $row_deficiencia = mysql_fetch_assoc($qr_deficiencia);
            $tipoDeficiencia = sprintf('%01s', $row_deficiencia['cod']);
        }

        fwrite($arquivo, $deficiencia, 1);

        $qr_cbo = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '{$clt['cbo_codigo']}'"); //falta isso
        $row_cbo = mysql_fetch_assoc($qr_cbo);
        $cbo = sprintf('%06s', RemoveCaracteres($row_cbo['cod']));
        fwrite($arquivo, $cbo, 6);

        $aprendiz = sprintf('%01s', '2');
        fwrite($arquivo, $aprendiz, 1);

        $ufCtps = sprintf('%-2s', $clt['uf_ctps']);
        fwrite($arquivo, $ufCtps, 2);

        fwrite($arquivo, $tipoDeficiencia, 1);

        $cpf = sprintf('%11s', $clt['cpf_limpo']);
        fwrite($arquivo, $cpf, 11);

        $cep = sprintf('%08s', $clt['cep_limpo']);
        fwrite($arquivo, $cep, 8);

        $espacos7 = NULL;
        $espacos7 = sprintf("%81s", $espacos7);
        fwrite($arquivo, $espacos7, 81);

        fwrite($arquivo, "\r\n");
        $this->sequencial++;
    }
  
}
?> 
