<?php

/**
 * Description of ConsultasESocial
 *
 * @author Ramon Lima
 */
class ConsultasESocial {
    
    function limpaData($data) {
        $data = str_replace("'", "", explode('-', $data));
        $this->ano = $data[0];
        $this->mes = $data[1];
        return $this->ano . '-' . $this->mes;
    }
    
    public function consultas1000() {   //INFORMAÇÕES DO EMPREGADOR/CONTRIBUINTE
        $qrs1000 = "SELECT if(A.cnpj IS NULL OR A.cnpj = '', 2, 1) AS tpInscricao, 
                            if(A.cnpj IS NULL OR A.cnpj = '', A.cpf, A.cnpj) AS nrInscricao, 
                            if(A.cnpj IS NULL OR A.cnpj = '', A.responsavel, A.razao) AS nomeRazao, 
                            A.data_inicio, A.razao, A.nat_juridica, A.cnae, A.indCooperativa, A.indConstrutora, A.indDesFolha, A.indOptRegEletronico, A.aliquotaRat, A.tpProcessoRat, A.nrProcessoRat, 
                            A.fap, A.tpProcessoFap, A.nrProcessoFap, A.responsavel, A.cpf, A.tel, A.celular, A.fax, A.email, A.indSocioOstensivo, A.indSituacaoEspecial, A.classTrib, A.indAcordoIsencaoMulta, C.* 
                     FROM rhempresa AS A
                     LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                     LEFT JOIN empresa_isenta AS C ON (A.id_empresa = C.id_empresa)
                     WHERE A.id_master = $this->id_master AND B.administracao = 1 LIMIT 1;";
        $result = mysql_query($qrs1000) or die("ERRO: S1000");
        return $result;
    }
    
    public function consultas1010() {
        $qrs1010 = "SELECT A.id_mov, A.data_ini, A.data_fim, A.cod, A.descicao, 
                        if(A.categoria = 'CREDITO' OR A.categoria = 'ADICIONAL' , 'P', if(A.categoria = 'DEBITO','D','I')) AS indProvDesc,
                        C.repDSR, C.repDecTerceiro, C.repFerias, C.repRescisao, incidencia, C.cod_rubrica, A.fator
                    FROM rh_movimentos AS A
                    INNER JOIN movimentos_rubricas_assoc AS B ON (A.id_mov = B.id_mov)
                    LEFT JOIN tabela_rubricas AS C ON (C.id_rubrica = B.id_rubrica)
                    GROUP BY A.cod
                    ORDER BY C.cod_rubrica;";
        $result = mysql_query($qrs1010) or die("ERRO: S1010");
        return $result;
    }

    public function consultas1020() {
        $qrs1020 = "SELECT B.id_projeto, B.nome AS nomeSetor, B.inicio, B.termino, D.cod_tp_lotacao AS tpLotacao,C.cod_tp_logradouro AS tpLogradouro,
                        A.logradouro, A.numero, A.complemento, A.bairro, A.cep, A.cod_municipio, A.`uf`,A.fpas, A.terceiros, A.cnpj, A.razao
                    FROM rhempresa AS A
                    LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                    LEFT JOIN tipos_de_logradouro AS C ON (A.id_tp_logradouro = C.id_tp_logradouro)
                    LEFT JOIN tipos_de_lotacao AS D ON (A.id_tp_lotacao = D.id_tp_lotacao)
                    WHERE A.id_master = $this->id_master;";
        $result = mysql_query($qrs1020) or die("ERRO: 1020");
        return $result;
    }

    public function consultas1030() {
        $qrs1030 = "SELECT A.id_curso, A.nome, A.inicio, A.termino
                    FROM curso AS A
                    LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                    WHERE B.id_master = $this->id_master AND A.`status` = 1;";
        $result = mysql_query($qrs1030) or die("ERRO: S1030");
        return $result;
    }

    public function consultas1050() {
        $qrs1050 = "SELECT A.id_curso, A.nome, A.inicio, A.termino, B.id_horario, B.entrada_1 AS horaEntrada, B.saida_2 AS horaSaida, B.id_horario, B.horas_trabalho, 
                          B.saida_1 AS inicioIntervalo, B.entrada_2 AS terminoIntervalo
                   FROM curso AS A
                   LEFT JOIN rh_horarios AS B ON (B.funcao = A.id_curso)
                   LEFT JOIN regioes AS C ON (B.id_regiao = C.id_regiao)
                   WHERE C.id_master = $this->id_master AND A.`status` = 1 AND B.status_reg = 1
                   ORDER BY A.id_curso ASC;";
        $result = mysql_query($qrs1050) or die ("ERRO: S1050");
        return $result;
    }
    
    public function consultas1060() { //CONSULTA ESTABELECIMENTOS
        $qr_s1060 = "SELECT B.id_projeto, if(A.cnpj IS NULL OR A.cnpj = '', 4, 1) AS tpInscricao, 
                            A.cnpj AS nrInscricao, A.indConstrutora, A.indDesFolha,
                            A.data_cad, A.cnae, A.aliquotaRat, A.tpProcessoRat, A.nrProcessoRat, 
                            A.fap, A.tpProcessoFap, A.nrProcessoFap, A.fpas, A.terceiros  
                     FROM rhempresa AS A
                     LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                     WHERE A.id_master = $this->id_master AND B.administracao = 0;";
        $result = mysql_query($qr_s1060) or die ("ERRO: S1060");
        return $result;
    }
    
    public function consultas1070() { // CONSULTA PROCESSOS
        $qr_s1070 = "SELECT if(C.proc_tipo_id = 2, 1,2) AS tpProcesso, G.n_processo_numero, A.proc_vara_uf, B1.cod_municipio, A.proc_numero_vara, 
                            if(id_clt IS NULL AND id_autonomo IS NULL, 1,2) AS indAutoria
                    FROM processos_juridicos AS A
                    LEFT JOIN regioes AS B ON(A.id_regiao = B.id_regiao)
                    LEFT JOIN rhempresa AS B1 ON(A.id_projeto = B1.id_projeto)
                    LEFT JOIN processo_tipo AS C ON(A.proc_tipo_id = C.proc_tipo_id)
                    LEFT JOIN(SELECT * FROM proc_trab_andamento ORDER BY andamento_id DESC) AS D ON(A.proc_id = D.proc_id)
                    LEFT JOIN processo_status AS E ON(D.proc_status_id = E.proc_status_id)
                    LEFT JOIN processos_juridicos_nomes AS F ON(A.proc_id = F.proc_id)
                    LEFT JOIN n_processos AS G ON(A.proc_id = G.proc_id) 
                    WHERE B.id_master = $this->id_master AND A.`status` = 1
                    GROUP BY A.proc_id;";
        $result = mysql_query($qr_s1070) or die ("ERRO: S1070");
        return $result;
    }

    public function consultas1100() {//CONSULTA RH_FOLHA
        $qr_s1100 = "SELECT A.terceiro, C.id_projeto,
                    if(C.cnpj IS NULL OR C.cnpj = '', 2, 1) AS tpInscricao, if(C.cnpj IS NULL OR C.cnpj = '',C.cpf, C.cnpj) AS nrInscricao, C.responsavel, C.cpf, C.tel, C.fax, C.email
                    FROM rh_folha AS A
                    LEFT JOIN rhempresa AS C ON (A.projeto = C.id_projeto)
                    WHERE A.status=3 AND A.mes = '$this->mes' AND A.`ano` = '$this->ano' AND C.id_master =  $this->id_master";
        
        $result = mysql_query($qr_s1100) or die ("ERRO: S1100");
        return $result;
    }

    public function consultas1200() {
        $qr_s1200 = "SELECT A.id_clt AS id_trab, A.cpf, A.pis, A.nome, A.data_nasci, C.cod AS codCbo, A.id_projeto, 
                    if(D.cnpj IS NULL OR D.cnpj = '', 2, 1) AS tpInscricao, 
                    if(D.cnpj IS NULL OR D.cnpj = '', D.cpf, D.cnpj) AS nrInscricao, 
                    A.status_demi, A.trabalha_outra_empresa, A.salario_outra_empresa, A.desconto_outra_empresa, A.matricula, A.data_entrada, B.salario, E.codigo AS codCateg, E.grupo,
                    F.id_folha, G.terceiro, tipo_insalubridade
                    FROM rh_clt AS A 
                    LEFT JOIN curso AS B ON (B.id_curso = A.id_curso)
                    LEFT JOIN rh_cbo AS C ON (C.id_cbo = B.cbo_codigo)
                    LEFT JOIN rhempresa AS D ON (D.id_projeto = A.id_projeto)
                    LEFT JOIN categorias_trabalhadores AS E ON (E.id_categoria_trab = A.tipo_contratacao)
                    LEFT JOIN rh_folha_proc AS F ON (F.id_clt = A.id_clt AND F.status = '3')
                    LEFT JOIN rh_folha AS G ON (G.id_folha = F.id_folha)
                    WHERE D.id_master = $this->id_master AND F.mes = '$this->mes' AND F.ano = '$this->ano' AND G.status=3
                    ORDER BY A.id_projeto LIMIT 100; ";
        $result = mysql_query($qr_s1200) or die ("ERRO: S1200");
        return $result;
    }

    public function consultas2100($projeto, $regiao) {
        $qr_s2100 = "SELECT A.id_clt, A.cpf, A.pis, A.nome AS nomeTrab, A.sexo, C.cod AS racaCor, D.cod_estado_civil,E.cod AS grauInstrucao, A.data_nasci, I.cod_1 AS codMunicipioNasc, A.uf_nasc, A.mae, A.pai, 
                    A.campo1 AS nrCtps, A.serie_ctps, A.uf_ctps, A.rg, A.orgao, A.data_emissao, A.endereco, A.numero, A.complemento, A.bairro, A.cep, J.cod_1 AS codMunicipioEnd ,A.uf AS ufEnd, A.dtChegadaPais, 
                    if(A.deficiencia = 1, 'S', 'N') AS defFisica,                      
                    if(A.deficiencia = 3, 'S', 'N') AS defVisual,                      
                    if(A.deficiencia = 2, 'S', 'N') AS defAuditiva,                    
                    if(A.deficiencia = 4, 'S', 'N') AS defMental,                      
                    if(A.deficiencia = 7, 'S', 'N') AS defIntelectual,                 
                    if(A.deficiencia = 6, 'S', 'N') AS reabilitado, 
                    A.deficiencia,
                    A.tel_fixo, A.tel_cel, A.email, A.matricula, A.data_entrada,       
                    if(A.status_admi = 10, 'S', 'N') AS indPrimeiroEmprego,            
                    F.codigo AS codCateg, A.id_curso, H.cod AS codCbo, G.salario, G.descricao,  A.tipo_contratacao, A.id_projeto, K.cod_pais AS cod_pais_nasc, L.cod_pais AS cod_pais_nacionalidade,
                    M.cnpj AS cnpjSindTrabalhador, N.id_horario, N.horas_semanais, if(N.folga = 5, 2, 1) AS tpJornada, N.folga, N.obs AS descTpJornada,
                    if(O.cnpj IS NULL OR O.cnpj = '', 2, 1) AS tpInscricao, 
                    if(O.cnpj IS NULL OR O.cnpj = '', O.cpf, O.cnpj) AS nrInscricao
                    FROM rh_clt AS A                                                   
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)            
                    LEFT JOIN etnias AS C ON (C.id = A.etnia)                          
                    LEFT JOIN estado_civil AS D ON (D.id_estado_civil = A.id_estado_civil)
                    LEFT JOIN escolaridade AS E ON (E.id = A.`escolaridade`)           
                    LEFT JOIN categorias_trabalhadores AS F ON (F.id_categoria_trab = A.tipo_contratacao)
                    LEFT JOIN curso AS G ON (G.id_curso = A.id_curso)                  
                    LEFT JOIN rh_cbo AS H ON (H.id_cbo = G.cbo_codigo)                 
                    LEFT JOIN municipios AS I ON (A.id_municipio_nasc = I.id_municipio)
                    LEFT JOIN municipios AS J ON (A.id_municipio_end = J.id_municipio)
                    LEFT JOIN paises AS K ON (A.id_pais_nasc = K.id_pais)
                    LEFT JOIN paises AS L ON (A.id_pais_nacionalidade = L.id_pais)
                    LEFT JOIN rhsindicato AS M ON (A.rh_sindicato = M.id_sindicato)
                    LEFT JOIN rh_horarios AS N ON (A.rh_horario = N.id_horario)
                    LEFT JOIN rhempresa AS O ON (B.id_projeto = O.id_projeto)
                    WHERE B.id_master = $this->id_master AND (A.status_demi = 0 OR A.status_demi IS NULL) AND A.`status` < 60 AND A.id_projeto = $projeto AND A.id_regiao = $regiao ORDER BY id_clt DESC;";
        
        $result = mysql_query($qr_s2100) or die ("ERRO S2100");
        return $result;
    }

    public function consultas2200($regiao,$projeto) {
        $qr_s2200 = "SELECT A.id_clt, A.cpf, A.pis, A.nome AS nomeTrab, A.sexo, C.cod AS racaCor, D.cod_estado_civil,E.cod AS grauInstrucao, A.data_nasci, I.cod_1 AS codMunicipioNasc, A.uf_nasc, A.mae, A.pai, 
                    A.campo1 AS nrCtps, A.serie_ctps, A.uf_ctps, A.rg, A.orgao, A.data_emissao, A.endereco, A.numero, A.complemento, A.bairro, A.cep, J.cod_1 AS codMunicipioEnd ,A.uf AS ufEnd, A.dtChegadaPais, 
                    if(A.deficiencia = 1, 'S', 'N') AS defFisica,                      
                    if(A.deficiencia = 3, 'S', 'N') AS defVisual,                      
                    if(A.deficiencia = 2, 'S', 'N') AS defAuditiva,                    
                    if(A.deficiencia = 4, 'S', 'N') AS defMental,                      
                    if(A.deficiencia = 7, 'S', 'N') AS defIntelectual,                 
                    if(A.deficiencia = 6, 'S', 'N') AS reabilitado, 
                    A.deficiencia,
                    A.tel_fixo, A.tel_cel, A.email, A.matricula, A.data_entrada,       
                    if(A.status_admi = 10, 'S', 'N') AS indPrimeiroEmprego,            
                    F.codigo AS codCateg, A.id_curso, H.cod AS codCbo, G.salario, G.descricao,  A.tipo_contratacao, A.id_projeto, K.cod_pais AS cod_pais_nasc, L.cod_pais AS cod_pais_nacionalidade,
                    M.cnpj AS cnpjSindTrabalhador, N.id_horario, N.horas_semanais, if(N.folga = 5, 2, 1) AS tpJornada, N.folga, N.obs AS descTpJornada,
						  if(O.cnpj IS NULL OR O.cnpj = '', 2, 1) AS tpInscricao, 
                    if(O.cnpj IS NULL OR O.cnpj = '', O.cpf, O.cnpj) AS nrInscricao
                    FROM rh_clt AS A                                                   
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)            
                    LEFT JOIN etnias AS C ON (C.id = A.etnia)                          
                    LEFT JOIN estado_civil AS D ON (D.id_estado_civil = A.id_estado_civil)
                    LEFT JOIN escolaridade AS E ON (E.id = A.`escolaridade`)           
                    LEFT JOIN categorias_trabalhadores AS F ON (F.id_categoria_trab = A.tipo_contratacao)
                    LEFT JOIN curso AS G ON (G.id_curso = A.id_curso)                  
                    LEFT JOIN rh_cbo AS H ON (H.id_cbo = G.cbo_codigo)                 
                    LEFT JOIN municipios AS I ON (A.id_municipio_nasc = I.id_municipio)
                    LEFT JOIN municipios AS J ON (A.id_municipio_end = J.id_municipio)
                    LEFT JOIN paises AS K ON (A.id_pais_nasc = K.id_pais)
                    LEFT JOIN paises AS L ON (A.id_pais_nacionalidade = L.id_pais)
                    LEFT JOIN rhsindicato AS M ON (A.rh_sindicato = M.id_sindicato)
                    LEFT JOIN rh_horarios AS N ON (A.rh_horario = N.id_horario)
                    LEFT JOIN rhempresa AS O ON (B.id_projeto = O.id_projeto)
                    WHERE A.data_entrada = CURDATE() AND B.id_master= $this->id_master AND A.id_regiao = $regiao AND A.id_projeto = $projeto;";
        
        $result = mysql_query($qr_s2200) or die("ERRO S2200");
        return $result;
    }

    public function consultas2220($id_clts) {
        $qr_s2220 = "SELECT A.id_clt, A.cpf, A.pis, A.nome AS nomeTrab, A.sexo, C.cod AS racaCor, D.cod_estado_civil,E.cod AS grauInstrucao, A.data_nasci, I.cod_1 AS codMunicipioNasc, A.uf_nasc, A.mae, A.pai, 
                    A.campo1 AS nrCtps, A.serie_ctps, A.uf_ctps, A.rg, A.orgao, A.data_emissao, A.endereco, A.numero, A.complemento, A.bairro, A.cep, J.cod_1 AS codMunicipioEnd ,A.uf AS ufEnd, A.dtChegadaPais, K.cod_pais AS cod_pais_nasc, L.cod_pais AS cod_pais_nacionalidade,
                    if(A.deficiencia = 1, 'S', 'N') AS defFisica,                      
                    if(A.deficiencia = 3, 'S', 'N') AS defVisual,                      
                    if(A.deficiencia = 2, 'S', 'N') AS defAuditiva,                    
                    if(A.deficiencia = 4, 'S', 'N') AS defMental,                      
                    if(A.deficiencia = 7, 'S', 'N') AS defIntelectual,                 
                    if(A.deficiencia = 6, 'S', 'N') AS reabilitado, 
                    A.deficiencia,
                    A.tel_fixo, A.tel_cel, A.email
                    FROM rh_clt AS A                                                   
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)            
                    LEFT JOIN etnias AS C ON (C.id = A.etnia)                          
                    LEFT JOIN estado_civil AS D ON (D.id_estado_civil = A.id_estado_civil)
                    LEFT JOIN escolaridade AS E ON (E.id = A.`escolaridade`)           
                    LEFT JOIN categorias_trabalhadores AS F ON (F.id_categoria_trab = A.tipo_contratacao)
                    LEFT JOIN curso AS G ON (G.id_curso = A.id_curso)                  
                    LEFT JOIN rh_cbo AS H ON (H.id_cbo = G.cbo_codigo)                 
                    LEFT JOIN municipios AS I ON (A.id_municipio_nasc = I.id_municipio)
                    LEFT JOIN municipios AS J ON (A.id_municipio_end = J.id_municipio)
                    LEFT JOIN paises AS K ON (A.id_pais_nasc = K.id_pais)
                    LEFT JOIN paises AS L ON (A.id_pais_nacionalidade = L.id_pais)
                    WHERE A.id_clt IN ($id_clts) AND B.id_master=$this->id_master;";

        $result = mysql_query($qr_s2220) or die ("ERRO: S2220");
        return $result;
    }

    public function consultas2240($id_clts) {
        $qr_s2240 = "SELECT A.id_clt, A.cpf, A.pis, A.matricula,        
                    F.codigo AS codCateg, A.id_curso, H.cod AS codCbo, G.salario, G.descricao,  A.tipo_contratacao, A.id_projeto,
                    M.cnpj AS cnpjSindTrabalhador, N.id_horario, N.horas_semanais, if(N.horas_semanais <> 40 AND N.horas_semanais <> 44, 2, 1) AS tpJornada, N.folga, N.obs AS descTpJornada,
                    if(O.cnpj IS NULL OR O.cnpj = '', 2, 1) AS tpInscricao, 
                    if(O.cnpj IS NULL OR O.cnpj = '', O.cpf, O.cnpj) AS nrInscricao
                    FROM rh_clt AS A                                                   
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)                       
                    LEFT JOIN categorias_trabalhadores AS F ON (F.id_categoria_trab = A.tipo_contratacao)
                    LEFT JOIN curso AS G ON (G.id_curso = A.id_curso)                  
                    LEFT JOIN rh_cbo AS H ON (H.id_cbo = G.cbo_codigo)                 
                    LEFT JOIN rhsindicato AS M ON (A.rh_sindicato = M.id_sindicato)
                    LEFT JOIN rh_horarios AS N ON (A.rh_horario = N.id_horario)
                    LEFT JOIN rhempresa AS O ON (B.id_projeto = O.id_projeto)
                    WHERE A.id_clt IN ($id_clts) AND B.id_master=$this->id_master;";

        $result = mysql_query($qr_s2240) or die ("ERRO: S2240");
        return $result;
    }
    
    public function consultas2320($id_clts) {
       $qr_s2320 = "SELECT A.id_clt AS id_trab, A.data_entrada, A.cpf, A.pis, A.matricula, A.nome,
                    B.cod_esocial AS codMotAfastamento, C.`data`, C.dias, C.id_evento
                    FROM 
                    rh_clt AS A
                    LEFT JOIN rhstatus AS B ON (A.`status` = B.codigo)
                    LEFT JOIN (
                    SELECT *
                    FROM rh_eventos
                    ORDER BY id_evento DESC) AS C ON (A.`status` = C.cod_status AND A.id_clt = C.id_clt)
                    WHERE A.id_clt IN ($id_clts) AND B.cod_esocial IS NOT NULL AND B.motivo = 'afastamento'
                    GROUP BY A.id_clt;";
       $result = mysql_query($qr_s2320) or die ("ERRO: S2320");
       return $result;
    }
    
    public function consultas2325($id_clts) {
        $data = explode('-', $this->limpaData($this->iniValidade));
        $qr_s2325 = "SELECT A.id_clt AS id_trab,B.id_evento_log,
                    (SELECT cpf FROM rh_clt WHERE id_clt = A.id_clt) AS cpf,
                    (SELECT pis FROM rh_clt WHERE id_clt = A.id_clt) AS pis,
                    (SELECT matricula FROM rh_clt WHERE id_clt = A.id_clt) AS matricula,
                    C.cod_esocial AS codMotAfastamentoAnterior, B.nome_status_de, B.data_de, D.cod_esocial AS codMotAfastamentoNovo, B.nome_status_para, 
                    DATE_FORMAT(B.data_mod,'%Y-%m-%d') AS data_mod, B.tipo, IF (B.id_status_de = B.id_status_para, 'N', 'S') AS indEfeitoRetroativo
                    FROM rh_eventos AS A
                    INNER JOIN rh_eventos_log AS B ON (A.id_evento = B.id_evento)
                    LEFT JOIN rhstatus AS C ON (B.id_status_de = C.id_status)
                    LEFT JOIN rhstatus AS D ON (B.id_status_para = D.id_status)
                    WHERE A.id_clt IN ($id_clts) AND D.cod_esocial IS NOT NULL AND C.cod_esocial IS NOT NULL AND D.motivo = 'afastamento' 
                    AND D.motivo = C.motivo AND MONTH(B.data_mod) = '{$data['1']}' AND YEAR(B.data_mod) = '{$data['0']} AND D.cod_esocial != C.cod_esocial'
                    ORDER BY B.id_evento_log DESC;";
//        print_r($data); exit;
       $result = mysql_query($qr_s2325) or die ("ERRO: S2325");
       return $result;
    }
    
    public function consultas2330($id_clts) {
        $qr_s2330 = "SELECT A.id_clt AS id_trab, A.cpf, A.pis, A.matricula, A.nome,
                     B.cod_esocial AS codMotAfastamento, B.especifica, C.`data`, C.data_retorno, C.id_evento, A.id_projeto
                    FROM 
                    rh_clt AS A
                    LEFT JOIN rhstatus AS B ON (A.`status` = B.codigo)
                    LEFT JOIN (
                    SELECT *
                    FROM rh_eventos
                    ORDER BY id_evento DESC) AS C ON (A.`status` = C.cod_status AND A.id_clt = C.id_clt)
                    WHERE A.id_clt IN ($id_clts) AND B.cod_esocial IS NOT NULL AND B.motivo = 'afastamento'
                    GROUP BY A.id_clt;";
        
        $result = mysql_query($qr_s2330) or die ("ERRO: S2330");
        return $result;  
    }
    
    public function consultas2360($id_clts) {
        $qr_s2360 = "SELECT A.id_clt, A.matricula, A.pis, A.cpf, A.data_entrada, 
                    IF(B.periculosidade_30 = 1, '02',NULL) AS tpCondicaoPer, 
                    IF(B.tipo_insalubridade != 0 AND B.tipo_insalubridade IS NOT NULL, '01',NULL) AS tpCondicaoIns
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON (A.id_curso = B.id_curso)
                    WHERE A.id_clt IN ($id_clts);";
        
        $result = mysql_query($qr_s2360) or die ("ERRO: S2360");
        return $result; 
    }
    
    public function consultas2365($id_clts) {
        $data = $this->limpaData($this->iniValidade);
        $qr_s2365 = "SELECT A.id_clt, A.id_projeto, A.matricula, A.pis, A.cpf, A.data_entrada, A.id_curso, B.id_curso_de, 
                    IF(C.periculosidade_30 = 1, '02', NULL) AS tpCondicaoPer_de, 
                    IF(C.tipo_insalubridade != 0 AND C.tipo_insalubridade IS NOT NULL, '01', NULL) AS tpCondicaoIns_de, 
                    DATE_FORMAT(B.data_proc,'%Y-%m-%d') AS data_proc, B.id_curso_para, 
                    IF(D.periculosidade_30 = 1, '02', NULL) AS tpCondicaoPer_para, 
                    IF(D.tipo_insalubridade != 0 AND D.tipo_insalubridade IS NOT NULL, '01', NULL) AS tpCondicaoIns_para
                    FROM rh_clt AS A
                    LEFT JOIN rh_transferencias AS B ON (A.id_clt = B.id_clt)
                    LEFT JOIN curso AS C ON (B.id_curso_de = C.id_curso)
                    LEFT JOIN curso AS D ON (A.id_curso = D.id_curso)
                    WHERE A.`status` = 10 AND DATE_FORMAT(B.data_proc,'%Y-%m') = '$data' AND A.id_clt IN ($id_clts)
                    GROUP BY A.id_clt
                    ORDER BY A.id_clt, B.id_transferencia DESC;";
        $result = mysql_query($qr_s2365) or die ("ERRO: S2365");
        return $result; 
    }
    
    public function consultas2600() {
        $qr_s2600 = "SELECT A.id_autonomo,  A.cpf, A.pis, G.cod AS racaCor, A.nome, A.sexo, A.data_nasci, A.pai, A.mae, A.endereco, A. numero, A.complemento, A.bairro, A.cep, A.uf AS ufEnd,
                    if(A.deficiencia = 1, 'S', 'N') AS defFisica,                      
                    if(A.deficiencia = 3, 'S', 'N') AS defVisual,                      
                    if(A.deficiencia = 2, 'S', 'N') AS defAuditiva,                    
                    if(A.deficiencia = 4, 'S', 'N') AS defMental,                      
                    if(A.deficiencia = 7, 'S', 'N') AS defIntelectual,                 
                    if(A.deficiencia = 6, 'S', 'N') AS reabilitado, 
                    A.deficiencia, A.tel_fixo, A.tel_cel, A.email, 
                    if(C.cnpj IS NULL OR C.cnpj = '', 2, 1) AS tpInscricao, 
                    if(C.cnpj IS NULL OR C.cnpj = '', C.cpf, C.cnpj) AS nrInscricao,
                    A.e_dataemissao AS dtInicioOgmo, D.codigo AS codCateg, A.data_entrada, A.id_curso,
                    E.cod AS codCbo, F.salario, F.descricao, H.cod_estado_civil, I.cod AS grauInstrucao,
                    A.campo1 AS nrCtps, A.serie_ctps, A.uf_ctps, A.rg, A.orgao, A.data_emissao
                    FROM autonomo AS A
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)
                    LEFT JOIN rhempresa AS C ON (C.id_projeto = B.id_projeto)
                    LEFT JOIN categorias_trabalhadores AS D ON (D.id_categoria_trab= A.id_categoria_trab)
                    LEFT JOIN curso AS F ON (F.id_curso = A.id_curso)
                    LEFT JOIN rh_cbo AS E ON (E.id_cbo = F.cbo_codigo)
                    LEFT JOIN etnias AS G ON (G.id = A.etnia)
                    LEFT JOIN estado_civil AS H ON (H.id_estado_civil = A.civil)
                    LEFT JOIN escolaridade AS I ON (I.id = A.escolaridade)
                    WHERE B.id_master = $this->id_master AND A.status = 1;";
//        print_r($qr_s2600);exit;
        $result = mysql_query($qr_s2600) or die ("ERRO: S2600");
        return $result;
    }

    public function consultas2620($id_autonomos) {
        $qr_s2620 = "SELECT A.id_autonomo, A.nome, A.cpf, A.pis, 
                    IF(C.cnpj IS NULL OR C.cnpj = '', 2, 1) AS tpInscricao, 
                    IF(C.cnpj IS NULL OR C.cnpj = '', C.cpf, C.cnpj) AS nrInscricao,
                    D.codigo AS codCateg, A.data_entrada, A.id_curso, E.cod AS codCbo, F.salario, F.descricao
                    FROM autonomo AS A
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)
                    LEFT JOIN rhempresa AS C ON (C.id_projeto = B.id_projeto)
                    LEFT JOIN categorias_trabalhadores AS D ON (D.id_categoria_trab= A.id_categoria_trab)
                    LEFT JOIN curso AS F ON (F.id_curso = A.id_curso)
                    LEFT JOIN rh_cbo AS E ON (E.id_cbo = F.cbo_codigo)
                    WHERE B.id_master = $this->id_master AND A.id_autonomo IN($id_autonomos) AND A.status = 1;";
//        print_r($qr_s2620);exit;
        $result = mysql_query($qr_s2620) or die ("ERRO: S2620");
        return $result;
    }

    public function consultas2680($id_autonomos) {
        $qr_s2680 = "SELECT A.id_autonomo, A.cpf, A.pis, A.data_saida,A.data_entrada,
                    IF(C.cnpj IS NULL OR C.cnpj = '', 2, 1) AS tpInscricao, 
                    IF(C.cnpj IS NULL OR C.cnpj = '', C.cpf, C.cnpj) AS nrInscricao,
                    D.codigo AS codCateg
                    FROM autonomo AS A
                    LEFT JOIN projeto AS B ON (B.id_projeto = A.id_projeto)
                    LEFT JOIN rhempresa AS C ON (C.id_projeto = B.id_projeto)
                    LEFT JOIN categorias_trabalhadores AS D ON (D.id_categoria_trab= A.id_categoria_trab)
                    WHERE B.id_master = $this->id_master AND A.id_autonomo IN($id_autonomos) AND A.status = 0;";
//        print_r($qr_s2680); exit;
        $result = mysql_query($qr_s2680) or die ("ERRO: S2680");
        return $result;
    }
    
    public function consultas2800P1($id_clts) {
        $qr_s2800 = "SELECT A.nome,A.id_clt AS id_trab,A.cpf, A.pis, A.matricula,
                    IF(B.cnpj IS NULL,2, 1) AS tpInscricao,IF(B.cnpj IS NULL, B.cpf, B.cnpj) AS nrInscricao, C.tipo_insalubridade
                    FROM rh_clt AS A
                    LEFT JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto)
                    LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                    WHERE A.id_clt IN($id_clts);";
        $result = mysql_query($qr_s2800) or die ("ERRO: S2800 P1");
        return $result;
    }
    
    public function consultas2800P2($id_clt) { // TRANSFERENCIA
        $data = $this->limpaData($this->iniValidade);
        $qr_s2800 = "SELECT B.cnpj,
                    IF(B.id_master = C.id_master, '11', '12') AS motDesligamento
                    FROM rh_transferencias AS A
                    LEFT JOIN rhempresa AS B ON (A.id_projeto_para = B.id_projeto)
                    LEFT JOIN rhempresa AS C ON (A.id_projeto_de = B.id_projeto)
                    WHERE A.id_clt = $id_clt AND DATE_FORMAT(A.criado_em,'%Y-%m') = '$data'
                    ORDER BY A.id_transferencia DESC 
                    LIMIT 1;";
        $result = mysql_query($qr_s2800) or die ("ERRO: S2800P2");
        return $result;
    }
    
    public function consultas2800P3($id_clt) { // RESCISAO
        $data = $this->limpaData($this->iniValidade);
        $qr =  "SELECT C.data_demi, C.data_fim_aviso,
                IF(C.aviso = 'indenizado', 'S', IF(C.aviso = 'trabalhado', 'N', NULL)) AS indPagtoAPI, 
                C.sal_base,C.previdencia_ss,C.base_inss_ss, C.base_irrf_ss, C.base_fgts_ss, C.total_rendimento, 
                C.total_deducao, C.total_liquido, C.inss_ss, B.cod_esocial
                FROM rh_clt AS A
                LEFT JOIN rh_recisao AS C ON (A.id_clt = C.id_clt)
                LEFT JOIN rhstatus AS B ON (A.`status` = B.codigo)
                WHERE C.id_clt = $id_clt AND C.`status` = 1 AND DATE_FORMAT(C.data_proc,'%Y-%m') = '$data' AND C.rescisao_complementar = 0;";
        
        $result = mysql_query($qr);
        return $result;
    
    }
   
    public function consultas2820($id_clts) {
        $qr_s2820 = "SELECT A.id_clt, A.cpf, A.pis, A.matricula, A.id_projeto, IF(B.cnpj IS NULL OR B.cnpj = '', 2, 1) AS tpInscricao, IF(B.cnpj IS NULL OR B.cnpj = '', B.cpf, B.cnpj) AS nrInscricao
                   FROM rh_clt AS A 
                   LEFT JOIN rhempresa AS B ON (B.id_projeto = A.id_projeto) 
                   WHERE B.id_master = $this->id_master  AND A.id_clt IN($id_clts);";

        $result = mysql_query($qr_s2820) or die ("ERRO: S2820");
        return $result;
    }

    public function consultas2400($id_clt) {
        $data = $this->limpaData($this->iniValidade);
        $qr_s2400 = "SELECT A.id_clt AS id_trab, A.cpf, A.pis, A.matricula, A.data_entrada, A.data_aviso, A.data_demi, D.codAvicoPre, C.obs, B.id_recisao, B.data_proc
                    FROM rh_clt AS A
                    LEFT JOIN rh_recisao AS B ON (A.id_clt = B.id_clt)
                    LEFT JOIN rescisao_avisoPrevio_assoc AS C ON (C.id_rescisao = B.id_recisao)
                    LEFT JOIN tipo_aviso_previo AS D ON (C.id_tpAvisoPre = D.id_avisoPre)
                    WHERE A.id_clt = $id_clt AND A.`status` > 60 AND B.`status` = 1 AND C.`status` = 1 AND DATE_FORMAT(B.data_proc,'%Y-%m') = '$data'
                    ORDER BY B.id_recisao DESC
                    LIMIT 1;";
        
        $result = mysql_query($qr_s2400) or die ("ERRO: S2400");
        return $result;
    }

    public function consultas2405($id_clt) {
        $data = $this->limpaData($this->iniValidade);
        $qr_s2405 = "SELECT A.id_clt AS id_trab, A.cpf, A.pis, A.matricula, A.data_entrada, A.data_aviso, A.data_demi, D.codAvicoPre, C.obs, B.id_recisao, B.data_proc
                    FROM rh_clt AS A
                    LEFT JOIN rh_recisao AS B ON (A.id_clt = B.id_clt)
                    LEFT JOIN rescisao_avisoPrevio_assoc AS C ON (C.id_rescisao = B.id_recisao)
                    LEFT JOIN tipo_aviso_previo AS D ON (C.id_tpAvisoPre = D.id_avisoPre)
                    WHERE A.id_clt = $id_clt AND A.`status`= 200 AND B.`status` = 0 AND C.`status` = 0 AND DATE_FORMAT(B.data_proc,'%Y-%m') = '$data'
                    ORDER BY B.id_recisao DESC
                    LIMIT 1;";
        $result = mysql_query($qr_s2405) or die ("ERRO: S2405");
        return $result;
    }
    
    public function consultaBeneficiarioPJ($id_projeto) {
        $dtIni = str_replace("'", "", $this->iniValidade);
        $date = new DateTime("$dtIni");
        $date->sub(new DateInterval( "P1M" ));
        $dataAnterior = $date->format("Y-m");
              
        $qr="SELECT A.id_prestador, C.c_razao AS nome, C.c_cnpj AS cnpj, A.valor AS ir, A.mes_competencia, A.ano_competencia
            FROM saida  AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON (A.entradaesaida_subgrupo_id = B.id)
            LEFT JOIN prestadorservico AS C ON (A.id_prestador = C.id_prestador)
            WHERE B.entradaesaida_grupo = 30 AND A.id_projeto = $id_projeto AND A.tipo_nf = 1 AND DATE_FORMAT(A.data_vencimento,'%Y-%m') = '$dataAnterior' AND A.`status` = 2
            ORDER BY C.id_prestador;";
        $result = mysql_query ($qr) or die("ERRO: Consulta Beneficiário PJ");
        return $result;
    }
    
    public function BuscaPrestContaTerceiro($id_prestador, $mes, $ano,$id_projeto) {
        $qr = "SELECT B.id_prestacao, C.mes_atual
               FROM projeto AS A
               LEFT JOIN prestacoes_contas AS B ON (A.id_projeto=B.id_projeto AND tipo = 'terceiro' AND status = 1 AND data_referencia = '$ano'-'$mes'-01' AND erros = 0)
               LEFT JOIN prestacoes_contas_terceiro AS C ON (B.id_prestacao = C.id_prestacao)
               WHERE A.inicio < '$ano'-'$mes'-31' AND A.prestacontas = 1 AND A.id_projeto = $id_projeto AND C.id_prestador = $id_prestador AND C.prestacontas = 1;";
        $result = mysql_query ($qr) or die("ERRO: Busca Prestações de Conta Terceiro");
        return $result;
    }

    public function consultaBeneficiario($id_projeto) {
        $dtIni = str_replace("'", "", $this->iniValidade);
        $date = new DateTime("$dtIni");
        $date->sub(new DateInterval( "P1M" ));
        $dataAnterior = $date->format("Y-m-d");
        $data = explode('-', $dataAnterior); 
        
        $qr =  "SELECT * FROM (
                SELECT A.mes, A.ano, A.data_proc AS dtPagto, B.salario_liq AS vlrRendTrib, B.irrf AS vlrIRRF, IF(A.terceiro = 0,'N', 'S') AS indDecTerceiro,  B.nome,IF(B.cpf IS NULL OR B.cpf = '', 1, 2) AS tpInscricaoBeneficiario, 
                IF (B.cpf IS NULL OR B.cpf = '', '', B.cpf) AS nrInscricaoBeneficiario, 
                B.id_autonomo AS id_trab, A.projeto, 
                IF(C.cnpj IS NULL OR C.cnpj = '', 2, 1) AS tpInscricao, 
                IF(C.cnpj IS NULL OR C.cnpj = '', C.cpf, C.cnpj) AS nrInscricao
                FROM folhas AS A
                LEFT JOIN folha_cooperado AS B ON (A.id_folha = B.id_folha)
                LEFT JOIN rhempresa AS C ON (A.projeto = C.id_projeto)
                WHERE A.mes = '{$data[1]}' AND A.ano = '{$data[0]}' AND A.contratacao = '3' AND A.projeto = $id_projeto AND A.status = 3 AND B.`status` = 3
                UNION
                SELECT A.mes, A.ano, A.data_proc AS dtPagto, IF(B.status_clt=50, (B.sallimpo+B.salbase),B.salbase) AS vlrRendTrib, B.a5021 AS vlrIRRF, IF(A.terceiro = 2,'N', 'S') AS indDecTerceiro, B.nome, IF(B.cpf IS NULL OR B.cpf = '', 1, 2) AS tpInscricaoBeneficiario, 
                IF (B.cpf IS NULL OR B.cpf = '', '', B.cpf) AS nrInscricaoBeneficiario, 
                B.id_clt AS id_trab, A.projeto, 
                IF(C.cnpj IS NULL OR C.cnpj = '', 2, 1) AS tpInscricao, 
                IF(C.cnpj IS NULL OR C.cnpj = '', C.cpf, C.cnpj) AS nrInscricao
                FROM rh_folha AS A
                LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
                LEFT JOIN rhempresa AS C ON (A.projeto = C.id_projeto)
                WHERE A.mes = '{$data[1]}' AND A.ano = '{$data[0]}' AND A.projeto = $id_projeto AND A.status = 3 AND B.`status` = 3) AS temp
                GROUP BY REPLACE(REPLACE(nrInscricaoBeneficiario,'-',''), '.','');";

//        $qr_clt = "SELECT IF(D.cpf IS NULL OR D.cpf = '', 1, 2) AS tpInscricaoBeneficiario, IF (D.cpf IS NULL OR D.cpf = '',  '', D.cpf) AS nrInscricaoBeneficiario, D.id_clt, D.id_projeto, IF(E.cnpj IS NULL OR E.cnpj = '', 2, 1) AS tpInscricao, IF(E.cnpj IS NULL OR E.cnpj = '', E.cpf, E.cnpj) AS nrInscricao
//                    FROM rh_folha AS A
//                    INNER JOIN regioes AS B ON (B.id_regiao = A.regiao)
//                    INNER JOIN rh_folha_proc AS C ON (C.id_folha = A.id_folha)
//                    INNER JOIN rh_clt AS D ON (D.id_clt = C.id_clt)
//                    LEFT JOIN rhempresa AS E ON (E.id_projeto = D.id_projeto)
//                    WHERE B.id_master = $this->id_master AND A.status = 3 AND C.status = 3 AND ((A.mes = 12 AND A.ano = $ano_ini AND A.terceiro = 2) OR ((A.mes <> 12 AND A.ano = $ano_fim) OR (A.mes = 12 AND A.ano = $ano_fim AND A.terceiro = 1)) AND YEAR(A.data_proc) = $ano_fim)
//                    GROUP BY D.cpf
//                    ORDER BY D.id_projeto, REPLACE(REPLACE(D.cpf,'-',''), '.','') ASC";
//        print_r($qr); exit;
        $result = mysql_query($qr) or die ("ERRO: Consulta Beneficiário");
        return $result;
    }

    public function consultaCnaeRatFap($cnaePreponderante) {   //INFORMAÇÕES DO EMPREGADOR/CONTRIBUINTE
        $qrCnaeRat = "SELECT aliquota_rat, percentual_fap FROM empresa_rat_fap
                    WHERE cnae = '$cnaePreponderante';";
        $result = mysql_query($qrCnaeRat) or die ("ERRO: Consulta Rat e Fap");
        return $result;
    }
     
    //RETORNA TODOS OS IDS DO MESMO CPF
    public function verificaDuplicidade($cpf) {
        $result = array();
        $result['rs'] = montaQuery("rh_clt", "id_clt, id_projto", "REPLACE(REPLACE(cpf,'.',''),'-','')  = '{$cpf}'");
        $result['total'] = count($result['rs']);
        return $result;
    }

    function mostraFolhasDIRFF($projeto) {
        $dtIni = str_replace("'", "", $this->iniValidade);
        $date = new DateTime("$dtIni");
        $date->sub(new DateInterval( "P1M" ));
        $dataAnterior = $date->format("Y-m-d");
        $data = explode('-', $dataAnterior); 
        $qr_folhas = mysql_query("SELECT id_folha,mes,ano,ids_movimentos_estatisticas,terceiro
                        FROM rh_folha
                        WHERE projeto = '$projeto' AND status = 3 AND mes = '{$data[1]}' AND ano = '{$data[0]}'") or die ("ERRO: Mostra Folha DIRFF");
//        $this->ids_movimentos_folhas = "";
//        $this->idsEstatisticas = array();
        $this->folhasEnvolvidas = array();
//        $this->idsEstatisticasMes = array();
        while ($row_folha = mysql_fetch_assoc($qr_folhas)) {
//            $this->ids_movimentos_folhas .= $row_folha['ids_movimentos_estatisticas'] . ",";
//            $this->idsEstatisticas[$row_folha['id_folha']] = $row_folha['ids_movimentos_estatisticas'];
//            $this->idsEstatisticasMes[$row_folha['id_folha']]['mes'] = $row_folha['mes'];
//            $this->idsEstatisticasMes[$row_folha['id_folha']]['flag'] = $row_folha['terceiro'];
            $this->folhasEnvolvidas[] = $row_folha['id_folha'];
        }
        
    }

    public function consultaAjudaDeCusto($id_clt, $ids_movimentos) {
        $qr_ajudaDeCusto = "SELECT cod_movimento, valor_movimento as valor
                            FROM rh_movimentos_clt
                            WHERE cod_movimento IN('50111', '5011') AND id_clt = $id_clt AND id_movimento IN ('{$ids_movimentos}');
                            -- RIDAC";
        $result = mysql_query($qr_ajudaDeCusto) or die ("ERRO: Consulta ajuda de custo");
        return $result;
    }

    public function consultaRescisao($id_clt, $mes, $ano) {
        $qr_rescisao = "SELECT (aviso_valor+ferias_vencidas+umterco_fv+ferias_pr+umterco_fp+um_terco_ferias_dobro+fv_dobro+ferias_aviso_indenizado) AS valor
                        FROM rh_recisao
                        WHERE id_clt = $id_clt AND MONTH(data_demi) = '{$mes}' AND YEAR(data_demi) = '{$ano}' AND status = 1;
                        -- RIIRP";
        $result = mysql_query($qr_rescisao) or die ("ERRO: Consulta Rescisão");
        return $result;
    }

    public function consultaCodRescisao() {
        //CÓDIGOS DE RESCISÃO PARA NÃO PEGAR A RESCISÃO DO CARA PELA FOLHA, E SIM PELA PARTE DE RESCISÃO
        $qr_codigos = "SELECT codigo FROM rhstatus WHERE tipo = 'recisao'";
        $rs_codigos = mysql_query($qr_codigos) or die("ln 290: " . mysql_error());
        $codigosRes = "";
        while ($rowCodigos = mysql_fetch_assoc($rs_codigos)) {
            $codigosRes .= $rowCodigos['codigo'] . ",";
        }
        
//        print_r($codigosRes); exit;
        return $codigosRes = substr($codigosRes, 0, -1);
    }

    public function consultaSalario($ids_clts, $folhasEnvolvidas, $codigosRes) {
        $data = $this->limpaData($this->iniValidade);
        $data_calendario = explode('-', $data);      
        $ano_anterior = $data_calendario[0] - 1;
        $qr_sal = "SELECT *,IF(status_clt=50, (sallimpo+salbase),salbase) AS salbaseCorreto FROM  (
                        SELECT	A.id_folha,A.mes,A.ano,A.projeto,B.id_clt,B.status_clt,
                                IF(A.ano={$ano_anterior},0,CAST(A.mes AS signed)) as mesEdit,
                                sallimpo,sallimpo_real,salbase,inss,a5049,a5021,
                                A.ids_movimentos_estatisticas
                            FROM  rh_folha as A
                      INNER JOIN  rh_folha_proc as B ON (B.id_folha = A.id_folha)
                           WHERE  B.id_clt IN ({$ids_clts}) AND A.status = 3 
                             AND  A.id_folha IN ('$folhasEnvolvidas') 
                             AND  A.terceiro = 2 AND B.status = 3 AND B.status_clt NOT IN ({$codigosRes})
                        GROUP BY  A.mes
                             ) AS temp;";
        
        $result = mysql_query($qr_sal);
        return $result;
    }

    public function consultaDeducoesDep($id_clt, $movimentos) {
        $qr_dedDep = "SELECT id_movimento,valor_movimento
                    FROM rh_movimentos_clt
                    WHERE id_clt IN ($id_clt) AND cod_movimento IN (6004,7009,50222) AND id_movimento IN('$movimentos');";

        $result = mysql_query($qr_dedDep);
        return $result;
    }

    public function consultaDependentes($id_clt, $id_projeto) {
        $qr_dependentes = "SELECT A.nome1, A.data1,
                            IF(A.data1 IS NULL OR A.data1 = '0000-00-00','', IF(A.data1 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR) OR A.portador_def1 = 1,'S','N')) as depIR1,
                            IF(A.data1 IS NULL OR A.data1 = '0000-00-00','',IF(A.data1 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 'S','N')) as depSF1,
                            A.nome2, A.data2,
                            IF(A.data2 IS NULL OR A.data2 = '0000-00-00','', IF(A.data2 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR) OR A.portador_def2 = 1,'S','N')) as depIR2,
                            IF(A.data2 IS NULL OR A.data2 = '0000-00-00','',IF(A.data2 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 'S','N')) as depSF2,
                            A.nome3, A.data3, 
                            IF(A.data3 IS NULL OR A.data3 = '0000-00-00','', IF(A.data3 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR) OR A.portador_def3 = 1,'S','N')) as depIR3,
                            IF(A.data3 IS NULL OR A.data3 = '0000-00-00','',IF(A.data3 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 'S','N')) as depSF3,
                            A.nome4, A.data4, 
                            IF(A.data4 IS NULL OR A.data4 = '0000-00-00','', IF(A.data4 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR) OR A.portador_def4 = 1,'S','N')) as depIR4,
                            IF(A.data4 IS NULL OR A.data4 = '0000-00-00','',IF(A.data4 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 'S','N')) as depSF4,
                            A.nome5, A.data5,
                            IF(A.data5 IS NULL OR A.data5 = '0000-00-00','', IF(A.data5 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR) OR A.portador_def5 = 1,'S','N')) as depIR5,
                            IF(A.data5 IS NULL OR A.data5 = '0000-00-00','',IF(A.data5 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 'S','N')) as depSF5,
                            A.nome6, A.data6,
                            IF(A.data6 IS NULL OR A.data6 = '0000-00-00','',IF(A.data6 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR) OR A.portador_def6 = 1,'S','N')) as depIR6,
                            IF(A.data6 IS NULL OR A.data6 = '0000-00-00','',IF(A.data1 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 'S','N')) as depSF6
                            FROM dependentes AS A
                            LEFT JOIN rh_clt AS B ON (B.id_clt = A.id_bolsista)
                            WHERE A.id_bolsista = $id_clt AND A.id_projeto = $id_projeto;";

        $result = mysql_query($qr_dependentes);
        return $result;
    }

    public function consultaTpContrato($id_clt) {
        $qr_tpContrato = "SELECT if((DATEDIFF(data_final1,CURDATE())) < 0 AND (DATEDIFF(data_final2,CURDATE()) < 0)  AND (tipo_contrato != '3'), 1,2) AS tpContrato, 
                        if((DATEDIFF(data_final1,CURDATE())) > 0 AND (DATEDIFF(data_final2,CURDATE()) > 0), data_final2,if((DATEDIFF(data_final1,CURDATE())) < 0 AND (DATEDIFF(data_final2,CURDATE()) < 0)  AND (tipo_contrato = '3'), '9999-99-99','')) AS dtTermino
                        FROM (
                        SELECT A.tipo_contrato ,DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_br,
                                DATE_ADD(A.data_entrada,INTERVAL 45 DAY) AS data_final1,
                                DATE_ADD(A.data_entrada,INTERVAL 90 DAY) AS data_final2
                                FROM rh_clt AS A
                                WHERE A.id_clt = $id_clt
                        ) AS tab;";

        $result = mysql_query($qr_tpContrato);
        return $result;
    }

    public function consultaTransferencia($id_clt) {
        $qrTransferencia = "SELECT *
                            FROM
                            (
                            SELECT IF(COUNT(A.id_clt)>0,'2','1') AS satus_transf
                            FROM rh_clt AS A
                            INNER JOIN (
                            SELECT B.id_transferencia, 
                             B.id_projeto_de, B.id_projeto_para, B.data_proc, B.id_clt
                            FROM rh_transferencias AS B
                            WHERE (
                            SELECT
                            REPLACE(
                            REPLACE(
                            REPLACE(cnpj,'.',''),'/',''),'-','') AS cnpj
                            FROM rhempresa
                            WHERE id_projeto=B.id_projeto_de)!=
                             (
                            SELECT
                            REPLACE(
                            REPLACE(
                            REPLACE(cnpj,'.',''),'/',''),'-','') AS cnpj
                            FROM rhempresa
                            WHERE id_projeto=B.id_projeto_para) AND B.`status`=1) AS B ON(B.id_clt=A.id_clt)
                            WHERE A.id_clt = $id_clt
                            ) AS temp";
        $result = mysql_query($qrTransferencia);
        return $result;
    }

    public function consultaIncidencia($codMov, $tipo = null) {
        $criterio = null;
        if (!empty($tipo)) {
            $criterio = " AND C.tipo = '{$tipo}'";
        }

        $qrIncidencia = "SELECT A.id_mov, A.descicao, C.cod_incid, C.descricao, C.tipo
                         FROM rh_movimentos AS A
                         RIGHT JOIN movimentos_incidencia_assoc AS B ON (A.id_mov = B.id_mov)
                         LEFT JOIN cod_incid_tributaria AS C ON (B.id_cod_incid_tributaria = C.id_cod_incid_tributaria)
                         WHERE A.cod = '$codMov' {$criterio};";
        $result = mysql_query($qrIncidencia);
        return $result;
    }

    public function consultaSoftwareHouse() {
        $qrSH = "SELECT cnpj, razao, responsavel, tel, cod_municipio, uf, email FROM rhempresa WHERE id_master = 8 AND status = 1 LIMIT 1;";
        $result = mysql_query($qrSH);
        return $result;
    }
    
    public function consultaIdFolha($id_clt) {
        $data = explode("-", $this->limpaData($this->iniValidade));
        $qr =  "SELECT F.id_folha
                FROM rh_folha AS F
                LEFT JOIN rh_folha_proc AS H ON (F.id_folha = H.id_folha)
                LEFT JOIN projeto AS G ON (G.id_projeto = F.projeto)
                WHERE F.`status` = '3' AND F.ano = '{$data[0]}' AND F.mes = '{$data[1]}' AND G.id_master = $this->id_master AND H.id_clt IN ($id_clt)";
        $result = mysql_query($qr);
        return $result;                    

    }
    
    public function consultaFGTSAnt($id_clt) {
        $dtIni = str_replace("'", "", $this->iniValidade);
        $date = new DateTime("$dtIni");
        $date->sub(new DateInterval( "P1M" ));
        $dataAnterior = $date->format("Y-m-d");
        $data = explode('-', $dataAnterior); 
        
        $qr =  "SELECT H.sallimpo AS bcFgtsMesAnt
                FROM rh_folha AS F
                LEFT JOIN projeto AS G ON (F.projeto = G.id_projeto)
                RIGHT JOIN rh_folha_proc AS H ON (F.id_folha = H.id_folha)
                WHERE F.`status` = '3' AND F.mes = '{$data[1]}' AND F.ano = '{$data[0]}' AND H.id_clt = $id_clt;";
        
       $result = mysql_query($qr);
       return $result;
    }
    
    public function consultaStatusSaida($idFolha){
        $result = montaQueryFirst("saida", "status", "nome LIKE '%$idFolha%' AND tipo = 167 AND STATUS != 0");
        return $result;
    }
    
    public function consultas1330($id_projeto) {
        $data = $this->limpaData($this->iniValidade);
        $qr =  "SELECT B.c_cnpj AS cnpjCooperativa, A.serie, A.numero_documento, A.data_emissao_nf, A.valor_bruto_nf
                FROM notas_fiscais AS A
                LEFT JOIN prestadorservico AS B ON (A.id_prestador = B.id_prestador)
                LEFT JOIN saida AS C ON (A.id_saida = C.id_saida)
                WHERE DATE_FORMAT(C.data_pg,'%Y-%m') = '$data' AND A.`status` = 2 AND A.id_projeto = $id_projeto;";
        
        $result = mysql_query($qr);
        return $result;
    }
    
    public function consultas1340($param) {
        
    }
   
}
