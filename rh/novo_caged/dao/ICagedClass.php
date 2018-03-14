<?php

abstract class ICagedClass {

    public $ano;
    public $mes;
    
    
    public $relacao = array();
    public $empresas = array();
    private $total_movimentos = array();
    public $dados_competencia = array();
    public $contador = array();
    public $sql_transferidos = '';
    public $sql_admitidos = '';
    public $sql_demitidos = '';
    public $arr = array();
    public $mascara_competencia;
    public $erros = array();
    
    function __construct($competencia) {
        $tipo = explode('-', $competencia);
        if(count($tipo)==3){
            $this->mascara_competencia = '%Y-%m-%d';
        }else{
            $this->mascara_competencia = '%Y-%m';
        }
    }

    function getAdmitidos($id_master, $filtro_ano, $filtro_mes, $projeto = null) {
        $filtro_data = ( ($filtro_ano) && ($filtro_mes)) ? " AND DATE_FORMAT(A.data_entrada,'%Y-%m') = '{$filtro_ano}-{$filtro_mes}'" : "";
        $filtro_projeto = ($projeto != "-1") ? " AND A.id_projeto = '{$projeto}'" : "";

        $this->sql_admitidos = "SELECT B.id_projeto, C.cnpj,
                        REPLACE(
                        REPLACE(
                        REPLACE(C.cep,'.',''),'/',''),'-','') AS cep_empresa, C.razao AS razao_empresa, C.endereco AS endereco_empresa, C.bairro AS bairro_empresa, C.uf AS uf_empresa,
                        REPLACE(
                        REPLACE(
                        REPLACE(C.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, C.cnae, C.tel AS tel_empresa,C.email AS email_empresa, B.nome, 'admissao' AS tipo, A.id_clt, A.nome AS nome_funcionario,
                        REPLACE(
                        REPLACE(
                        REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status, A.status_admi, DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador,
                        REPLACE(
                        REPLACE(
                        REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo, A.etnia, IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia) AS deficiencia,

                        IF( (SELECT @var_id_curso:=id_curso_de
                        FROM rh_transferencias
                        WHERE id_clt=A.id_clt
                        ORDER BY rh_transferencias.id_transferencia ASC
                        LIMIT 1) IS NOT NULL,@var_id_curso, D.id_curso) AS id_curso,

                        IF(IF((
                        SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia
                        FROM rh_salario AS G
                        WHERE G.status=1 AND G.id_curso=D.id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<= DATE_FORMAT(A.data_entrada,'%Y-%m-%d')
                        ORDER BY G.data DESC
                        LIMIT 1), @var_salario_competencia, (
                        SELECT @var_salario_competencia:=I.salario_antigo
                        FROM rh_salario AS I
                        WHERE DATE_FORMAT(I.data,'%Y-%m-%d')> DATE_FORMAT(A.data_entrada,'%Y-%m-%d') AND I.id_curso=D.id_curso AND I.status=1
                        ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC
                        LIMIT 1)), @var_salario_competencia,D.salario) AS salario_competencia,

                        A.data_entrada AS data_proc, DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_proc_f, DATE_FORMAT(A.data_entrada,'{$this->mascara_competencia}') AS data_competencia, E.cod AS cbo, IF(F.horas_semanais = 0 OR F.horas_semanais IS NULL OR F.horas_semanais = '', D.hora_semana, F.horas_semanais) AS hora_semana
                        FROM rh_clt AS A
                        LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                        LEFT JOIN rhempresa AS C ON(B.id_projeto=C.id_projeto)
                        LEFT JOIN curso AS D ON(D.id_curso= A.id_curso)
                        LEFT JOIN rh_cbo AS E ON(E.id_cbo=D.cbo_codigo)
                        LEFT JOIN rh_horarios AS F ON(F.id_horario=A.rh_horario)
                        WHERE C.cnpj IS NOT NULL AND B.id_master='{$id_master}' {$filtro_data} {$filtro_projeto};";
        
        $result = mysql_query($this->sql_admitidos);
        
        $arr_admitidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_admitidos[] = $resp;
            $this->arr[$resp['data_competencia']][$resp['tipo']][$resp['id_clt']] = $resp['id_clt'];
        }
        return $arr_admitidos;
    }
    
    // gamby -------------------------------------------------------------------
    function getAdmitidosDiario($id_master, $projeto) {
        $filtro_projeto = ($projeto != "-1") ? " AND A.id_projeto = '{$projeto}'" : "";

        $this->sql_admitidos = "SELECT B.id_projeto, C.cnpj,
                        REPLACE(
                        REPLACE(
                        REPLACE(C.cep,'.',''),'/',''),'-','') AS cep_empresa, C.razao AS razao_empresa, C.endereco AS endereco_empresa, C.bairro AS bairro_empresa, C.uf AS uf_empresa,
                        REPLACE(
                        REPLACE(
                        REPLACE(C.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, C.cnae, C.tel AS tel_empresa,C.email AS email_empresa, B.nome, IF(DATE_FORMAT(A.data_entrada,'%Y-%m') < '2016-03','admissao','entrada') AS tipo, A.id_clt, A.nome AS nome_funcionario,
                        REPLACE(
                        REPLACE(
                        REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status, A.status_admi, DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f, A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador,
                        REPLACE(
                        REPLACE(
                        REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo, A.etnia, IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia) AS deficiencia,

                        IF( (SELECT @var_id_curso:=id_curso_de
                        FROM rh_transferencias
                        WHERE id_clt=A.id_clt
                        ORDER BY rh_transferencias.id_transferencia ASC
                        LIMIT 1) IS NOT NULL,@var_id_curso, D.id_curso) AS id_curso,

                        IF(IF((
                        SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia
                        FROM rh_salario AS G
                        WHERE G.status=1 AND G.id_curso=D.id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<= DATE_FORMAT(A.data_entrada,'%Y-%m-%d')
                        ORDER BY G.data DESC
                        LIMIT 1), @var_salario_competencia, (
                        SELECT @var_salario_competencia:=I.salario_antigo
                        FROM rh_salario AS I
                        WHERE DATE_FORMAT(I.data,'%Y-%m-%d')> DATE_FORMAT(A.data_entrada,'%Y-%m-%d') AND I.id_curso=D.id_curso AND I.status=1
                        ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC
                        LIMIT 1)), @var_salario_competencia,D.salario) AS salario_competencia,

                        A.data_entrada AS data_proc, DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_proc_f, DATE_FORMAT(A.data_entrada,'{$this->mascara_competencia}') AS data_competencia, E.cod AS cbo, IF(F.horas_semanais = 0 OR F.horas_semanais IS NULL OR F.horas_semanais = '', D.hora_semana, F.horas_semanais) AS hora_semana
                        FROM rh_clt AS A
                        LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                        LEFT JOIN rhempresa AS C ON(B.id_projeto=C.id_projeto)
                        LEFT JOIN curso AS D ON(D.id_curso= A.id_curso)
                        LEFT JOIN rh_cbo AS E ON(E.id_cbo=D.cbo_codigo)
                        LEFT JOIN rh_horarios AS F ON(F.id_horario=A.rh_horario)
                        WHERE C.cnpj IS NOT NULL AND B.id_master='$id_master' AND A.seguro_desemprego = 1 {$filtro_projeto};";
						
        $result = mysql_query($this->sql_admitidos);
        
        $arr_admitidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_admitidos[] = $resp;
            $this->arr[$resp['data_competencia']][$resp['tipo']][$resp['id_clt']] = $resp['id_clt'];
        }
        return $arr_admitidos;
    }
    // gamby -------------------------------------------------------------------
    
    function getTransferidos($id_master, $filtro_ano, $filtro_mes, $projeto = null) {        
        $filtro_data = ( ($filtro_ano) && ($filtro_mes)) ? "  AND IF(B.id_transferencia IS NULL, DATE_FORMAT(A.data_entrada,'%Y-%m'), DATE_FORMAT(B.data_proc,'%Y-%m')) = '{$filtro_ano}-{$filtro_mes}'" : "";
        $filtro_projeto = ($projeto != "-1") ? " AND A.id_projeto = '{$projeto}'" : "";
        
        //pegando os transferidos
        $this->sql_transferidos = "
                SELECT 
                    C.id_projeto, D.cnpj,
                   REPLACE(
                   REPLACE(
                   REPLACE(D.cep,'.',''),'/',''),'-','') AS cep_empresa, D.razao AS razao_empresa, D.endereco AS endereco_empresa, D.bairro AS bairro_empresa, D.uf AS uf_empresa,
                   REPLACE(
                   REPLACE(
                   REPLACE(D.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, D.cnae, D.tel AS tel_empresa,D.email AS email_empresa, C.nome AS nome_projeto, IF(B.id_projeto_para=C.id_projeto,'entrada','saida') AS tipo, A.id_clt, 

                   A.nome AS nome_funcionario, 
                   REPLACE(REPLACE(REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status,  A.status_admi,
                   DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f,
                    A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador, REPLACE(REPLACE(REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo, A.etnia, 
                     IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia)  AS deficiencia, IF(B.id_projeto_para=C.id_projeto, B.id_curso_para, B.id_curso_de) AS id_curso,
                     
                     IF(
	                      IF( (SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia FROM rh_salario AS G 
	                                WHERE G.status=1 AND G.id_curso=E.id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<=DATE_FORMAT(B.data_proc,'%Y-%m-%d') ORDER BY G.data DESC LIMIT 1),
									@var_salario_competencia,
					                            (SELECT @var_salario_competencia:=I.salario_antigo FROM rh_salario AS I WHERE  DATE_FORMAT(I.data,'%Y-%m-%d')>DATE_FORMAT(B.data_proc,'%Y-%m-%d') AND I.id_curso=E.id_curso AND I.status=1 ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC LIMIT 1)
								 ), @var_salario_competencia ,E.salario) 
							 
							 AS salario_competencia


                   ,IF(B.id_transferencia IS NULL, A.data_entrada, B.data_proc) AS data_proc, DATE_FORMAT(IF(B.id_transferencia IS NULL, A.data_entrada, B.data_proc),'%d%m%Y') AS data_proc_f, IF(B.id_transferencia IS NULL, DATE_FORMAT(A.data_entrada,'%Y-%m'), DATE_FORMAT(B.data_proc,'{$this->mascara_competencia}')) AS data_competencia, F.cod AS cbo, 
                   IF(G.horas_semanais = 0 OR G.horas_semanais IS NULL OR G.horas_semanais = '', E.hora_semana, G.horas_semanais) AS hora_semana, A.data_entrada
                   FROM rh_clt AS A
                   INNER JOIN (
                   SELECT B.id_transferencia, 
                    B.id_projeto_de, B.id_projeto_para, B.data_proc, B.id_clt,  B.id_curso_para, B.id_curso_de
                   FROM rh_transferencias AS B
                   WHERE (SELECT REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') AS cnpj FROM rhempresa WHERE id_projeto=B.id_projeto_de)!=
						 (SELECT REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') AS cnpj FROM rhempresa WHERE id_projeto=B.id_projeto_para) AND B.`status`=1) AS B ON(B.id_clt=A.id_clt)
                   LEFT JOIN projeto AS C ON(B.id_projeto_de=C.id_projeto OR B.id_projeto_para = C.id_projeto)
                   LEFT JOIN rhempresa AS D ON(C.id_projeto=D.id_projeto)
                   LEFT JOIN curso AS E ON(E.id_curso=IF(B.id_projeto_para=C.id_projeto, B.id_curso_para, B.id_curso_de))
                   LEFT JOIN rh_cbo AS F ON(F.id_cbo=E.cbo_codigo)
                   LEFT JOIN rh_horarios AS G ON(G.id_horario=A.rh_horario)
                   WHERE D.cnpj IS NOT NULL AND C.id_master='$id_master' {$filtro_data} {$filtro_projeto};";

        $result = mysql_query($this->sql_transferidos);

        $arr_transferidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_transferidos[] = $resp;
            $this->arr[$resp['data_competencia']][$resp['tipo']][$resp['id_clt']] = $resp['id_clt'];
        }
        return $arr_transferidos;
    }

    function getDemitidos($id_master, $filtro_ano, $filtro_mes, $projeto = null) {
        $filtro_data = ( ($filtro_ano) && ($filtro_mes)) ? " AND DATE_FORMAT(A.data_demi,'%Y-%m') = '{$filtro_ano}-{$filtro_mes}'" : "";
        $filtro_projeto = ($projeto != "-1") ? " AND A.id_projeto = '{$projeto}'" : "";
        
        // pegando os demitidos
        $this->sql_demitidos = "SELECT B.id_projeto, C.cnpj,
                   REPLACE(
                   REPLACE(
                   REPLACE(C.cep,'.',''),'/',''),'-','') AS cep_empresa, C.razao AS razao_empresa, C.endereco AS endereco_empresa, C.bairro AS bairro_empresa, C.uf AS uf_empresa,
                   REPLACE(
                   REPLACE(
                   REPLACE(C.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo, C.cnae, C.tel AS tel_empresa,C.email AS email_empresa, B.nome, 'demissao' AS tipo, A.id_clt, 

                   A.nome AS nome_funcionario, 
                   REPLACE(REPLACE(REPLACE(A.pis,'.',''),'/',''),'-','') AS pis_limpo, IF(A.sexo='M',1,2) AS sexo, DATE_FORMAT(A.data_nasci,'%d%m%Y') AS data_nasci_f, A.data_nasci, A.escolaridade, A.status,  A.status_admi,
                    DATE_FORMAT(A.data_entrada,'%d%m%Y') AS data_entrada_f, DATE_FORMAT(A.data_saida,'%d%m%Y') AS data_saida_f,
                    A.campo1 AS numero_ctps, A.serie_ctps, A.uf_ctps, A.cep AS cep_trabalhador, REPLACE(REPLACE(REPLACE(A.cpf,'.',''),'/',''),'-','') AS cpf_limpo , A.etnia, 
                      IF(CHAR_LENGTH(A.deficiencia)<=0,2,A.deficiencia)  AS deficiencia, D.id_curso,
                      
                       IF( (SELECT @var_id_curso:=id_curso_de
                        FROM rh_transferencias
                        WHERE id_clt=A.id_clt
                        ORDER BY data_proc DESC 
                        LIMIT 1) IS NULL,@var_id_curso, @var_id_curso:=D.id_curso) AS id_curso,
                      
                      
                       IF( (SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia FROM rh_salario AS G 
                                WHERE G.status=1 AND G.id_curso=@var_id_curso AND DATE_FORMAT(G.data,'%Y-%m-%d')<=DATE_FORMAT(A.data_saida,'%Y-%m-%d') ORDER BY G.data DESC LIMIT 1),
											@var_salario_competencia,
							                            IF((SELECT @var_salario_competencia:=I.salario_antigo FROM rh_salario AS I WHERE  DATE_FORMAT(I.data,'%Y-%m-%d')>DATE_FORMAT(A.data_saida,'%Y-%m-%d') AND I.id_curso=@var_id_curso AND I.status=1 ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC LIMIT 1),
																 @var_salario_competencia,D.salario)
										 ) AS salario_competencia


						, A.data_demi AS data_proc, DATE_FORMAT( A.data_demi,'%d%m%Y') AS data_proc_f, DATE_FORMAT( A.data_demi,'{$this->mascara_competencia}') AS data_competencia, E.cod AS cbo, 
                                                IF(F.horas_semanais = 0 OR F.horas_semanais IS NULL OR F.horas_semanais = '', D.hora_semana, F.horas_semanais) AS hora_semana
                   FROM rh_clt AS A
                   LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                   LEFT JOIN rhempresa AS C ON(B.id_projeto=C.id_projeto) 
                   LEFT JOIN curso AS D ON( D.id_curso= A.id_curso)
                   LEFT JOIN rh_cbo AS E ON(E.id_cbo=D.cbo_codigo)
                   LEFT JOIN rh_horarios AS F ON(F.id_horario=A.rh_horario)
						 WHERE C.cnpj IS NOT NULL  AND A.status_demi=1 AND B.id_master='$id_master' {$filtro_data} {$filtro_projeto};";

        $result = mysql_query($this->sql_demitidos);

        $arr_demitidos = array();
        while ($resp = mysql_fetch_array($result)) {
            $arr_demitidos[] = $resp;
            $this->arr[$resp['data_competencia']][$resp['tipo']][$resp['id_clt']] = $resp['id_clt'];
        }
        return $arr_demitidos;
    }

    function carregaRelacao($array_completo, $competencia) {
        
       
        $arr_tot_mes = array();

        foreach ($array_completo as $resp) {
            $cnpj = $resp['cnpj_limpo'];
            
            if($_COOKIE['debug']){
                if($_COOKIE['debug_clt'] == $resp['id_clt']){
                    print_array($resp);
                }
            }
                    
            $this->empresas[$cnpj]['cep_empresa'] = $resp['cep_empresa'];
            $this->empresas[$cnpj]['razao_empresa'] = $resp['razao_empresa'];
            $this->empresas[$cnpj]['endereco_empresa'] = $resp['endereco_empresa'];
            $this->empresas[$cnpj]['bairro_empresa'] = $resp['bairro_empresa'];
            $this->empresas[$cnpj]['uf_empresa'] = $resp['uf_empresa'];
            $this->empresas[$cnpj]['cnae'] = $resp['cnae'];
            $this->empresas[$cnpj]['tel_empresa'] = $resp['tel_empresa'];
            $this->empresas[$cnpj]['email_empresa'] = $resp['email_empresa'];
            $this->empresas[$cnpj][$resp['tipo']][$resp['data_competencia']][] = $resp['id_clt'];
            
            $this->relacao[$cnpj][$resp['data_competencia']][] = $resp;
            
            $this->total_movimentos[$resp['data_competencia']] += 1;
//            $arr_tot_mes[$cnpj][$resp['data_competencia']][$resp['tipo']] += 1;
            $arr_data_competencia = explode('-',$resp['data_competencia']); //alterado para caso for tipo diario

            $arr_tot_mes[$cnpj][$arr_data_competencia[0].'-'.$arr_data_competencia[1]][$resp['tipo']] += 1;
            
//            $arr_tot_mes['09652823000338'][$arr_data_competencia[0].'-'.$arr_data_competencia[1]][$resp['tipo']] += 1;
            
            if($arr_data_competencia[0] <= 0){
                $this->erros[strtoupper($resp['tipo'])][$resp['id_clt']] = 'Erro nos dados. Data de competência do funcionário inválida ou zerada. #'.$resp['id_clt'].' - '.$resp['nome_funcionario'].'. PROJETO: '.$resp['nome'];
            }
        }
//        echo $this->sql_admitidos."<br><br><br>";
//        echo $this->sql_demitidos."<br><br><br>";
//        echo $this->sql_transferidos."<br><br><br>";
//        echo '<pre>';
//        print_r($this->relacao);
//        echo '</pre>';
//        exit();

            //carrega totalizadores
            foreach ($arr_tot_mes as $cnpj => $val_competencia) {
                ksort($val_competencia);
                $anterior = 0;
                foreach ($val_competencia as $data => $tipos) {
                    $total = ($anterior + $tipos['admissao'] + $tipos['entrada']) - ($tipos['saida'] + $tipos['demissao']);
                    $arr_resumo[$cnpj][$data]['ultimo_dia'] = $total;
                    $arr_resumo[$cnpj][$data]['admissao'] = $tipos['admissao'];
                    $arr_resumo[$cnpj][$data]['entrada'] = $tipos['entrada'];
                    $arr_resumo[$cnpj][$data]['saida'] = $tipos['saida'];
                    $arr_resumo[$cnpj][$data]['demissao'] = $tipos['demissao'];
                    $anterior = $total;
                }


                $keys_data = array_keys($arr_resumo[$cnpj]);



                $arr_data_inicial = explode('-', $keys_data[0]);
                $arr_data_final = explode('-', $keys_data[(count($keys_data) - 1)]);

                $mes_inicial = $arr_data_inicial[1];
                $ano_inicial = $arr_data_inicial[0];
                $ano_final = $arr_data_final[0];

//                if ($ano_inicial > 0) {
                    $arr_mes_total = array();
                    $ultimo_dia = 0;
                    $arr_ultimo_dia = array();
                    $cont = 0;
                    for ($a = $ano_inicial; $a <= $ano_final; $a++) {
                        for ($m = 1; $m <= 12; $m++) {
                            $data_competencia = $a . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);

                            if (isset($arr_resumo[$cnpj][$data_competencia]['ultimo_dia'])) {
                                $ultimo_dia = $arr_resumo[$cnpj][$data_competencia]['ultimo_dia'];
                            }
                            $arr_ultimo_dia[$cont] = $ultimo_dia;
                            $this->dados_competencia[$cnpj][$data_competencia]['primeiro_dia'] = ($arr_ultimo_dia[($cont-1)]) ? $arr_ultimo_dia[($cont-1)] : 0;
                            $this->dados_competencia[$cnpj][$data_competencia]['ultimo_dia'] = $ultimo_dia;
    //                        $this->dados_competencia[$cnpj][$data_competencia]['primeiro_dia'] = $ultimo_dia[($cont-1)];
                            $this->dados_competencia[$cnpj][$data_competencia]['admissao'] = ($arr_resumo[$cnpj][$data_competencia]['admissao']) ? $arr_resumo[$cnpj][$data_competencia]['admissao'] : 0;
                            $this->dados_competencia[$cnpj][$data_competencia]['entrada'] = ($arr_resumo[$cnpj][$data_competencia]['entrada']) ? $arr_resumo[$cnpj][$data_competencia]['entrada'] : 0;
                            $this->dados_competencia[$cnpj][$data_competencia]['saida'] = ($arr_resumo[$cnpj][$data_competencia]['saida']) ? $arr_resumo[$cnpj][$data_competencia]['saida'] : 0;
                            $this->dados_competencia[$cnpj][$data_competencia]['demissao'] = ($arr_resumo[$cnpj][$data_competencia]['demissao']) ? $arr_resumo[$cnpj][$data_competencia]['demissao'] : 0;
                            $cont++;
                        }
                    }
//                } else {
//                    $this->erros['COMPETENCIA'] = 'Erro nos dados. Data de competência do clt inválida ou zerada';
//                }
            }

        //carrega o retorno para a relação
        $arr = array();
//        echo $competencia;
        foreach ($this->relacao as $cnpj => $row) {
            if (isset($row[$competencia])) {
                $arr[$cnpj] = $row[$competencia];
            }
        }
//        var_dump($competencia);
//        echo '</br>';
//        echo '<pre>';
//        print_r($arr);
//        echo '</pre>';
//        echo '</br>';
//        exit();


        return $arr;
    }
    
    function getPrimeiroDia($cnpj,$data_competencia){
        return $this->dados_competencia[$cnpj][$data_competencia]['primeiro_dia'];
    }
    
    function getPrimeiroDiaNew($cnpj, $data_competencia){
        $sql = "SELECT COUNT(id_clt) AS total
            FROM (
            SELECT A.id_clt, A.nome, A.data_entrada, C.data_demi, C.`status` AS status_rescisao
            FROM rh_clt AS A
            LEFT JOIN rhempresa AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN rh_recisao AS C ON(A.id_clt = C.id_clt AND C.`status` = 1)
            WHERE A.data_entrada <= '{$data_competencia}-01' AND
            REPLACE(
            REPLACE(
            REPLACE(B.cnpj, '.', ''), '/',''), '-', '') = '{$cnpj}'
            ) AS tmp
            WHERE status_rescisao IS NULL OR data_demi > '{$data_competencia}-01'";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);
        
        return $res['total'];
    }

    function getMaster($id_master) {
        $sql = "SELECT A.*, REPLACE(REPLACE(REPLACE(A.cnpj,'.',''),'/',''),'-','') AS cnpj_limpo,"
                . "REPLACE(REPLACE(REPLACE(A.telefone,'.',''),'/',''),'-','') AS telefone_limpo FROM `master` AS A WHERE A.id_master='$id_master'";
        $result = mysql_query($sql);
        $master = array();
        while ($row = mysql_fetch_array($result)) {
            $master = $row;
        }
        return $master;
    }

    /*
     * $competencia = '0000-00' //ano mes
     */

    function getTotalMovimentos($competencia) {
        return isset($this->total_movimentos[$competencia]) ? $this->total_movimentos[$competencia] : 0;
    }

    function getTotalUltimoDia($cnpj, $data_competencia) {
        return (count($this->contador[$cnpj][$data_competencia]['entrada']) + count($this->contador[$cnpj][$data_competencia]['admissao'])) - count($this->contador[$cnpj][$data_competencia]['saida']);
    }

    //USADO PARA PEGAR O NOME DO CURSO CASO ERRO
    function getCurso($id_curso) {
        $sql = "SELECT A.id_curso, A.nome AS nome_curso, A.hora_semana, A.hora_semana, B.regiao AS nome_regiao, C.nome AS nome_projeto FROM curso AS A
                LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao)
                LEFT JOIN projeto AS C ON(A.campo3=C.id_projeto)
                WHERE id_curso='$id_curso'";
        $result = mysql_query($sql);
        return mysql_fetch_array($result);
    }

    function getCodigosDesligamento() {
        return array('61' => '31', '64' => '31', '60' => '32', '63' => '40', '65' => '40', '66' => '43', '101' => '50', '81' => '60');
    }

    function getArrayUF() {
        return array("AC", "AL", "AM", "AP", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RO", "RS", "RR", "SC", "SE", "SP", "TO");
    }

    function getCodigosEtnias() {
        return array('1', '2', '4', '6', '8', '9');
    }

}
