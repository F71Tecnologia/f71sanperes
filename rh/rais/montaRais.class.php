<?php

Class montaRais {

    public $ano_base;
    public $sequencial;
    public $matriz;
    public $terceiroSal;
    public $cnpj;
    public $total_registros;
    private $tiposRescisao;
    private $tiposRescisaoRais;
    private $totalMais5SalMin = 0;
    private $totalMenos5SalMin = 0;
    private $salMin = 880.00;

   
    public function __construct($anoBase) {
        $this->ano_base = $anoBase;
        $sqlTpRescisao = "SELECT especifica,codigo,cod_rais FROM rhstatus WHERE cod_rais > 0";
        $resultTpRescisao = mysql_query($sqlTpRescisao);
        $this->tiposRescisao = array();
        $this->tiposRescisaoRais = array();
        while ($rowTpRescisao = mysql_fetch_assoc($resultTpRescisao)) {
            $this->tiposRescisao[] = $rowTpRescisao['codigo'];
            $this->tiposRescisaoRais[$rowTpRescisao['codigo']] = $rowTpRescisao['cod_rais'];
        }
    }

    public function consultaEmpresas($cnpj) {
        $return = montaQuery("rhempresa", "*", "id_empresa IN (" . implode(",", $cnpj) . ")", "nome", null, "array", false, "cnpj");
        return $return;
    }

    public function getProjetosByCNPJ($cnpj) {
        $rs = montaQuery("rhempresa", "id_projeto", "cnpj = '{$cnpj}'", "nome");
        $idsPro = array();
        foreach ($rs as $rhempresa) {
            $idsPro[] = $rhempresa['id_projeto'];
        }
        return $idsPro;
    }

    public function consultaEmpregado($idProjeto) {
        $query = "SELECT B.base_inss, C.valor AS salario_curso, A.valor_hora, B.salliquido, B.rend, B.desco, A.id_projeto, A.rh_sindicato, I.cnpj AS cnpj_sindical, A.id_clt, A.pis, A.nome, DATE_FORMAT(A.data_nasci, '%d%m%Y') AS data_nasci, A.nacionalidade, E.cod as grauEscolar, A.cpf, A.id_curso,
                    A.campo1, A.serie_ctps, DATE_FORMAT(A.data_entrada, '%d%m%Y') AS data_entrada, A.data_entrada AS data_entrada_banco , DATE_FORMAT(A.data_entrada, '%Y') AS ano_entrada , B.sallimpo, N.horas_semanais, D.cod as cbo, a5019 contribuicao_sindical,
                    J.mes, /*IF(MONTH(A.data_entrada) = B.mes AND YEAR(A.data_entrada) = B.ano,sallimpo_real, sallimpo)*/ B.salbase AS salario_base, B.salbase AS salbaseProc, J.terceiro, B.a8006, F.cod as etnia, G.cod as deficiencia, A.sexo,
                    J.tipo_terceiro, A.cod_pais_rais, A.dtChegadaPais
                    FROM rh_clt AS A
                    LEFT JOIN rh_folha_proc AS B ON (B.id_clt = A.id_clt AND B.status = '3' )
                    LEFT JOIN curso AS C ON (C.id_curso = A.id_curso)
                    LEFT JOIN rh_cbo AS D ON (D.id_cbo = C.cbo_codigo)
                    LEFT JOIN escolaridade AS E ON (E.id = A.escolaridade)
                    LEFT JOIN etnias AS F ON (F.id = A.etnia)
                    LEFT JOIN deficiencias AS G ON (G.id = A.deficiencia)
                    LEFT JOIN rhsindicato AS I ON (I.id_sindicato = A.rh_sindicato)
                    LEFT JOIN rh_folha AS J ON (J.id_folha = B.id_folha)
                    -- LEFT JOIN rh_recisao AS M ON (M.id_clt = A.id_clt)
                    LEFT JOIN rh_horarios AS N ON (N.id_horario = A.rh_horario)                       
                    WHERE J.ano = " . $this->ano_base . " AND A.id_projeto IN (" . implode(",", $idProjeto) . ") AND J.`status` = 3 AND (CAST(SUBSTRING(A.pis,1,3) AS SIGNED) < 109 OR CAST(SUBSTRING(A.pis,1,3) AS SIGNED) >= 120) AND (CAST(SUBSTRING(A.pis,1,3) AS SIGNED) != 168) AND (CAST(SUBSTRING(A.pis,1,3) AS SIGNED) != 167) AND (CAST(SUBSTRING(A.pis,1,3) AS SIGNED) != 267) AND (CAST(SUBSTRING(A.pis,1,3) AS SIGNED) != 268)
                    ORDER BY A.nome, J.mes ASC";
        
       
       
        
//print_r($query); exit;
        $result = mysql_query($query);
        
        return $result;
    }
    
    public function getEstatisticas($id_projetos, $ano_base){ 
        //RETORNA UMA ARRAY COM A SEGUINTE CONFIGURAÇÃO ** ARRAY[id_projeto] com todos os movimentos do ANO BASE
                $p=0;
                while($id_projetos[$p]){
                    
                    $id_projeto = $id_projetos[$p];
                    
                    $sql_estatistica = "SELECT ids_movimentos_estatisticas, mes FROM rh_folha A 
                                    WHERE status = 3 
                                    AND ano = {$ano_base}  
                                    AND projeto = {$id_projeto}"; 
                    
                    $id_estatistica_result = mysql_query($sql_estatistica);
                    
                    while ($mov_estatistica = mysql_fetch_assoc($id_estatistica_result)){   
                        $variavel = explode(',', $mov_estatistica['ids_movimentos_estatisticas']);
                        foreach ($variavel as $v){
                            $arrVariavel[]=$v;
                        }
                        
//                        $array_est[$mov_estatistica['mes']] = $mov_estatistica['ids_movimentos_estatisticas'];                          
//                        $array_est_projeto = implode(", ",$array_est);    
//                        $array_est_projeto = str_replace(", ,", ", ", $array_est_projeto);
                    } //fim while meses- projeto
                    
//                    $arr_full[$id_projeto] = $array_est_projeto;     
                    $arr_full[$id_projeto] = implode(',', $arrVariavel);     
                    
                    $p++;
                } // fim while projeto   
               
                
                return $arr_full;
            }
            
    public function setPAT(){
        
        $empregados = $this->matriz;
        $minx5 = $this->salMin*5;
        
        foreach ($empregados as $empregado){
            if($empregado["dados"]["salario_base"] < $minx5){
                $this->totalMenos5SalMin +=1;
            }else{
                $this->totalMais5SalMin +=1;
            }
        }
    }
            

    public function montaCabecalho($arquivo, $empresa) {
        /* Linha 1 */
        // MONTANDO O CABEÇALHO DO ARQUIVO
        $this->sequencial = 1;
        $sequencial = sprintf("%06s", $this->sequencial);
        fwrite($arquivo, $sequencial, 6);

        $this->cnpj = $empresa['cnpj'];

        $cnpj = RemoveCaracteres($empresa['cnpj']);
        $cnpj = substr($cnpj, 0, 14);
        $cnpj = sprintf("%014s", $cnpj);
        fwrite($arquivo, $cnpj, 14);

        $prefixo = '00';
        fwrite($arquivo, $prefixo, 2);

        $registro = '0';
        fwrite($arquivo, $registro, 1);

        $constante = '1';
        $constante = sprintf("%01s", $constante);
        fwrite($arquivo, $constante, 1);

//        $cpf = RemoveCaracteres($empresa['cpf']);
        $cpf = RemoveCaracteres($empresa['cnpj']);
        $cpf = substr($cpf, 0, 14);
        $cpf = sprintf("%014s", $cpf);
        fwrite($arquivo, $cpf, 14);

//        $tipo_inscricao = 4;
        $tipo_inscricao = 1;
        $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
        fwrite($arquivo, $tipo_inscricao, 1);

//        $nome = substr(RemoveAcentos(RemoveCaracteres($empresa['responsavel'])), 0, 40);
        $nome = substr(RemoveAcentos(RemoveCaracteres($empresa['razao'])), 0, 40);
        $nome = sprintf("%-40s", $nome);
        fwrite($arquivo, $nome, 40);

        $logradouro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['logradouro']))), 0, 40);
        $logradouro = sprintf("%-40s", $logradouro);
        fwrite($arquivo, $logradouro, 40);

        /*      if (!empty($empresa['numero'])){
          $numero = substr($empresa['numero'], 0, 6);
          $numero = sprintf("%06s", $numero);
          fwrite($arquivo, $numero, 6);
          } else {
          $numero = sprintf("% 6s", " ");
          fwrite($arquivo, $numero, 6);
          }

          if (!empty($empresa['complemento'])) {
          $complemento = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['complemento']))), 0, 21);
          $complemento = sprintf("%-21s", $complemento);
          fwrite($arquivo, $complemento, 21);
          } else {
          $complemento = sprintf("%-21s", " ");
          fwrite($arquivo, $complemento, 21);
          }
         */

        $numero = substr($empresa['numero'], 0, 6);
        $numero = sprintf("%06s", $numero);
        if ($empresa['numero'] == 0) {
            $numero = sprintf("% 6s", " ");
            fwrite($arquivo, $numero, 6);
        } else {
            fwrite($arquivo, $numero, 6);
        }

        if (($empresa['numero'] == 0) && (empty($empresa['complemento']))) {
            $complemento = sprintf("%-21s", "SN");
            fwrite($arquivo, $complemento, 21);
        } else {
            $complemento = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['complemento']))), 0, 21);
            $complemento = sprintf("%-21s", $complemento);
            fwrite($arquivo, $complemento, 21);
        }

        $bairro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['bairro']))), 0, 19);
        $bairro = sprintf("%-19s", $bairro);
        fwrite($arquivo, $bairro, 19);

        $cep = substr(RemoveCaracteres(RemoveEspacos($empresa['cep'])), 0, 8);
        $cep = sprintf("%08s", $cep);
        fwrite($arquivo, $cep, 8);

        //Código do Município
        $cod_municipio = substr(RemoveCaracteres($empresa['cod_municipio']), 0, 7);        
        $cod_municipio = sprintf("%07s", $cod_municipio);
        fwrite($arquivo, $cod_municipio, 7);

        $cidade = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['cidade']))), 0, 30);
        $cidade = sprintf("%-30s", $cidade);
        fwrite($arquivo, $cidade, 30);

        $uf = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['uf']))), 0, 2);
        $uf = sprintf("%02s", $uf);
        fwrite($arquivo, $uf, 2);

        $ddd_telefone = explode('(', $empresa['tel']);
        $ddd_telefone = substr(RemoveCaracteres(RemoveEspacos($ddd_telefone[1])), 0, 2);
        $ddd_telefone = sprintf("%02s", $ddd_telefone);
        fwrite($arquivo, $ddd_telefone, 2);

        $telefone = explode(')', $empresa['tel']);
        $telefone = substr(RemoveCaracteres(RemoveEspacos($telefone[1])), 0, 8);
        $telefone = sprintf("%09s", $telefone);
        fwrite($arquivo, $telefone, 9);

        $indicador_retificacao = '2';
        fwrite($arquivo, $indicador_retificacao, 1);

        $data_retificacao = substr(NULL, 0, 8);
        $data_retificacao = sprintf("%08s", $data_retificacao);
        fwrite($arquivo, $data_retificacao, 8);

        $data = date('dmY');
        fwrite($arquivo, $data, 8);

        $email_responsavel = substr($empresa['email'], 0, 45);
        $email_responsavel = sprintf("%-45s", $email_responsavel);
        fwrite($arquivo, $email_responsavel, 45);

        $nome_responsavel = substr(RemoveCaracteres(RemoveAcentos($empresa['responsavel'])), 0, 52);
        $nome_responsavel = sprintf("%-52s", $nome_responsavel);
        fwrite($arquivo, $nome_responsavel, 52);

        $espacos1 = NULL;
        $espacos1 = sprintf("%24s", $espacos1);
        fwrite($arquivo, $espacos1, 24);

        $cpf_responsavel = RemoveCaracteres($empresa['cpf']);
        $cpf_responsavel = substr($cpf_responsavel, 0, 11);
        $cpf_responsavel = sprintf("%011s", $cpf_responsavel);
        fwrite($arquivo, $cpf_responsavel, 11);

        $crea = NULL;
        $crea = substr($crea, 0, 12);
        $crea = sprintf("%012s", $crea);
        fwrite($arquivo, $crea, 12);

        $data_nascimento_responsavel = implode('', array_reverse(explode('-', $empresa['data_nasc'])));
        $data_nascimento_responsavel = substr($data_nascimento_responsavel, 0, 8);
        $data_nascimento_responsavel = sprintf("%08s", $data_nascimento_responsavel);
        fwrite($arquivo, $data_nascimento_responsavel, 8);

        $espacos2 = NULL;
        $espacos2 = sprintf("%159s", $espacos2);
        fwrite($arquivo, $espacos2, 159);

        fwrite($arquivo, "\r\n");

        /* Linha 2 */
        $this->sequencial++;
        $sequencial2 = sprintf("%06s", $this->sequencial);
        fwrite($arquivo, $sequencial2, 6);

        $cnpj2 = $empresa['cnpj'];
        $cnpj2 = RemoveCaracteres($cnpj2);
        $cnpj2 = substr($cnpj2, 0, 14);
        $cnpj2 = sprintf("%014s", $cnpj2);
        fwrite($arquivo, $cnpj2, 14);

        $prefixo2 = '00';
        fwrite($arquivo, $prefixo2, 2);

        $registro2 = '1';
        fwrite($arquivo, $registro2, 1);

        $nome2 = RemoveAcentos($empresa['razao']);
        $nome2 = substr($nome2, 0, 52);
        $nome2 = sprintf("%-52s", $nome2);
        fwrite($arquivo, $nome2, 52);

        $logradouro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['logradouro']))), 0, 40);
        $logradouro = sprintf("%-40s", $logradouro);
        fwrite($arquivo, $logradouro, 40);

        //       $numero = substr($empresa['numero'], 0, 6);
//        $numero = sprintf("% 6s", $numero);
        //       fwrite($arquivo, $numero, 6);

        $numero = substr($empresa['numero'], 0, 6);
        $numero = sprintf("%06s", $numero);
        if ($empresa['numero'] == 0) {
            $numero = sprintf("% 6s", " ");
            fwrite($arquivo, $numero, 6);
        } else {
            fwrite($arquivo, $numero, 6);
        }

        if (($empresa['numero'] == 0) && (empty($empresa['complemento']))) {
            $complemento = sprintf("%-21s", "SN");
            fwrite($arquivo, $complemento, 21);
        } else {
            $complemento = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['complemento']))), 0, 21);
            $complemento = sprintf("%-21s", $complemento);
            fwrite($arquivo, $complemento, 21);
        }

        $bairro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['bairro']))), 0, 19);
        $bairro = sprintf("%-19s", $bairro);
        fwrite($arquivo, $bairro, 19);

        $cep = substr(RemoveCaracteres(RemoveEspacos($empresa['cep'])), 0, 8);
        $cep = sprintf("%08s", $cep);
        fwrite($arquivo, $cep, 8);

        $cod_municipio = substr(RemoveCaracteres($empresa['cod_municipio']), 0, 7);
        $cod_municipio = sprintf("%07s", $cod_municipio);
        fwrite($arquivo, $cod_municipio, 7);

        $cidade = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['cidade']))), 0, 30);
        $cidade = sprintf("%-30s", $cidade);
        fwrite($arquivo, $cidade, 30);

        $uf = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($empresa['uf']))), 0, 2);
        $uf = sprintf("%02s", $uf);
        fwrite($arquivo, $uf, 2);

        $ddd_telefone2 = explode('(', $empresa['tel']);
        $ddd_telefone2 = substr($ddd_telefone2[1], 0, 2);
        $ddd_telefone2 = sprintf("%02s", $ddd_telefone2);
        fwrite($arquivo, $ddd_telefone2, 2);

        $telefone2 = explode(')', $empresa['tel']);
        $telefone2 = substr(RemoveCaracteres(RemoveEspacos($telefone2[1])), 0, 8);
        $telefone2 = sprintf("%09s", $telefone2);
        fwrite($arquivo, $telefone2, 9);

        $email_responsavel2 = $empresa['email'];
        $email_responsavel2 = substr($email_responsavel2, 0, 45);
        $email_responsavel2 = sprintf("%-45s", $email_responsavel2);
        fwrite($arquivo, $email_responsavel2, 45);

        $cnae = $empresa['cnae'] . '00';
        $cnae = RemoveCaracteres($cnae);
        $cnae = sprintf("%07s", $cnae);
        fwrite($arquivo, $cnae, 7);

        //PQP, ESSA POHA MUDA TODA HORA, JA TINHA O CAMPO NATUREZA
        //AE UM CORNO COLOCLOU O CAMPO NAT_JURIDICA
        //SO PRA FUDER COM MINHA VIDA.
        //OLHA A GAMBI Q VOU TER Q FAZER
        if (!empty($empresa['natureza'])) {
            $natureza = $empresa['natureza'];
        } else {
            $natureza = $empresa['nat_juridica'];
        }

        $natureza = substr($natureza, 0, 4);
        $natureza = sprintf("%04s", $natureza);
        fwrite($arquivo, $natureza, 4);

        $proprietarios = $empresa['proprietarios'];
        $proprietarios = sprintf("%04s", $proprietarios);
        fwrite($arquivo, $proprietarios, 4);

        $data_base = '04';
        fwrite($arquivo, $data_base, 2);

        $tipo_inscricao = '1';
        fwrite($arquivo, $tipo_inscricao, 1);

        $tipo_rais = '0';
        fwrite($arquivo, $tipo_rais, 1);

        $zeros = '';
        $zeros = sprintf("%02s", $zeros);
        fwrite($arquivo, $zeros, 2);

        $matricula_cei = NULL;
        $matricula_cei = sprintf("%012s", $matricula_cei);
        fwrite($arquivo, $matricula_cei, 12);

        $ano_base_rais = $this->ano_base;
        fwrite($arquivo, $ano_base_rais, 4);

        $porte_empresa = '3';
        fwrite($arquivo, $porte_empresa, 1);

        $participacao_simples = '2';
        fwrite($arquivo, $participacao_simples, 1);

        $participacao_pat = '1';
        fwrite($arquivo, $participacao_pat, 1);

//        305 a 310 6 Número PAT-Trabalhadores que recebem até 5 Sal.Mínimos
        $tMenos5 = $this->totalMenos5SalMin;
        $tMenos5 = sprintf("%06s", $tMenos5);
        fwrite($arquivo, $tMenos5, 6);
        
       
//        311 a 316 6 Número PAT-Trabalhadores que recebem acima de 5 Sal.Mínimos
        $tMais5 = $this->totalMais5SalMin;
        $tMais5 = sprintf("%06s", $tMais5);
        fwrite($arquivo, $tMais5, 6);
         
         
//        317 a 319 3 Número Porcentagem de serviço próprio (%)
        $servicoProprio = null;
        $servicoProprio = sprintf("%03s", $servicoProprio);
        fwrite($arquivo, $servicoProprio, 3);
        
//        320 a 322 3 Número Porcentagem de administração de cozinhas (%)
        $cozinhas = null;
        $cozinhas = sprintf("%03s", $cozinhas);
        fwrite($arquivo, $cozinhas, 3);
        
//        323 a 325 3 Número Porcentagem de refeição-convênio (%)
        $refConvenio = 50;
        $refConvenio = sprintf("%03s", $refConvenio);
        fwrite($arquivo, $refConvenio, 3);
        
//        326 a 328 3 Número Porcentagem de refeições transportadas (%)
        $refTransp = null;
        $refTransp = sprintf("%03s", $refTransp);
        fwrite($arquivo, $refTransp, 3);
        
//        329 a 331 3 Número Porcentagem de cesta alimento (%)
        $cestaAlim = null;
        $cestaAlim = sprintf("%03s", $cestaAlim);
        fwrite($arquivo, $cestaAlim, 3);
        
//        332 a 334 3 Número Porcentagem de alimentação-convênio (%)
        $alimConv = 50;
        $alimConv = sprintf("%03s", $alimConv);
        fwrite($arquivo, $alimConv, 3);
        

        $indicator_encerramento = '2';
        fwrite($arquivo, $indicator_encerramento, 1);

        $data_encerramento = NULL;
        $data_encerramento = sprintf("%08s", $data_encerramento);
        fwrite($arquivo, $data_encerramento, 8);

        $contribuicao_associativa = NULL;
        $contribuicao_associativa = sprintf("%014s", $contribuicao_associativa);
        fwrite($arquivo, $contribuicao_associativa, 14);

        $contribuicao_associativa_centavos = NULL;
        $contribuicao_associativa_centavos = sprintf("%09s", $contribuicao_associativa_centavos);
        fwrite($arquivo, $contribuicao_associativa_centavos, 9);

        $contribuicao_sindical = NULL;
        $contribuicao_sindical = sprintf("%014s", $contribuicao_sindical);
        fwrite($arquivo, $contribuicao_sindical, 14);

        $contribuicao_sindical_centavos = NULL;
        $contribuicao_sindical_centavos = sprintf("%09s", $contribuicao_sindical_centavos);
        fwrite($arquivo, $contribuicao_sindical_centavos, 9);

        $contribuicao_assistencial = NULL;
        $contribuicao_assistencial = sprintf("%014s", $contribuicao_assistencial);
        fwrite($arquivo, $contribuicao_assistencial, 14);

        $contribuicao_assistencial_centavos = NULL;
        $contribuicao_assistencial_centavos = sprintf("%09s", $contribuicao_assistencial_centavos);
        fwrite($arquivo, $contribuicao_assistencial_centavos, 9);

        $contribuicao_confederativa = NULL;
        $contribuicao_confederativa = sprintf("%014s", $contribuicao_confederativa);
        fwrite($arquivo, $contribuicao_confederativa, 14);

        $contribuicao_confederativa_centavos = NULL;
        $contribuicao_confederativa_centavos = sprintf("%09s", $contribuicao_confederativa_centavos);
        fwrite($arquivo, $contribuicao_confederativa_centavos, 9);

        $atividade_ano_base = '1';
        fwrite($arquivo, $atividade_ano_base, 1);

        $indicador_centralizacao_pagamento = '2';
        fwrite($arquivo, $indicador_centralizacao_pagamento, 1);

        $cnpj_estabelecimento_centralizador = '';
        $cnpj_estabelecimento_centralizador = sprintf("%014s", $cnpj_estabelecimento_centralizador);
        fwrite($arquivo, $cnpj_estabelecimento_centralizador, 14);

        $indicador_empresa_filiada_sindicato = '2';
        fwrite($arquivo, $indicador_empresa_filiada_sindicato, 1);

        $tipo_sis_control_ponto = '04';
        fwrite($arquivo, $tipo_sis_control_ponto, 2);

        $espacos3 = NULL;
        $espacos3 = sprintf("%85s", $espacos3);
        fwrite($arquivo, $espacos3, 85);

        $exclusivo_empresa1 = '';
        $exclusivo_empresa1 = sprintf("%12s", $exclusivo_empresa1);
        fwrite($arquivo, $exclusivo_empresa1, 12);

        fwrite($arquivo, "\r\n");
        $this->sequencial++;
    }

    public function montaMatriz(array $dados) {
        // MONTANDO A MATRIZ COM OS DETALHES DO ARQUIVO

        $mes = $dados['mes'];
//        $salBaseRemunerecao = RemoveCaracteres(number_format($dados['salario_base'] + $dados['rend'],2,'.',''));
        $salBaseRemunerecao = RemoveCaracteres(number_format($dados['base_inss'],2,'.',''));
        $salBaseDecimo = RemoveCaracteres($dados['salbaseProc']);
        //    $afastamento = $dados['cod_status'] . $dados['data'] . $dados['data_retorno'];
        //  $afastamento = RemoveCaracteres($afastamento);
        //     $feriasIndenizadas = RemoveCaracteres($dados['um_avo_ferias_indenizadas']);

        if ($dados["terceiro"] == "1") {            
            if($dados["tipo_terceiro"] == 1){
                //primeira parcela
//                if($valor_adiantamento['valor'] > 0){
//                    $this->terceiroSal[$dados['id_clt']][$valor_adiantamento['mes']] = number_format($valor_adiantamento['valor'],2,'.','');
//                }else{                    
//                  $arrPP[$dados['id_clt']] = $dados['salliquido'];
                    $this->terceiroSal[$dados['id_clt']][$mes] = number_format($dados['salliquido'],2,'.','');
//                }
                
            } else if($dados["tipo_terceiro"] == 2){
                //segunda parcela
//                echo "{$dados['nome']} - {$dados['salbaseProc']} + {$dados['rend']} - {$dados['desco']}<br>";
                $this->terceiroSal[$dados['id_clt']][$mes] = number_format($dados['salbaseProc'] + $dados['rend'] - $dados['desco'],2,'.','');
            }else{                
                $this->terceiroSal[$dados['id_clt']][$mes] = round(($dados['salario_base']),0);
            }
        } else {
            //salario normal
            $this->matriz[$dados['id_clt']]['salario'][$mes] = round($salBaseRemunerecao, 0);
        }
        
        
        $this->matriz[$dados['id_clt']]['contribuicao_sindical'] += $dados['contribuicao_sindical'];
        
        $this->matriz[$dados['id_clt']]['dados'] = $dados;
        //  $this->matriz[$dados['id_clt']]['afastamento'][] = $afastamento;
        //  $this->matriz[$dados['id_clt']]['feriasIndenizada'] = $feriasIndenizadas;
        
        
        if (isset($this->terceiroSal[$dados['id_clt']]) && !empty($this->terceiroSal[$dados['id_clt']]))
            $this->matriz[$dados['id_clt']]['terceitoSal'][$mes] = $this->terceiroSal[$dados['id_clt']][$mes];
        
        $id_projeto = $this->matriz[$dados['id_clt']]['dados']['id_projeto'];
        
    }
    
    public function incluiHoraExtraMatriz($id_projetos, $array_est){
        // retorna uma array com as horas extras com a seguinte configuração 
        // $arr_hora_extra[id_projeto][$hora_ext['id_clt']][$hora_ext['mes_mov']]
         $p=0;
         //echo "Projetos: ";
         
          
                while($id_projetos[$p]){                    
                    $id_projeto = $id_projetos[$p];
                    
                    $sqlYear = "SELECT id_clt, mes_mov, qnt_horas 
                        FROM rh_movimentos_clt  
                        WHERE id_projeto = {$id_projeto} 
                        AND id_mov IN(286,152,287,364,405,406,428)                          
                        AND ano_mov = {$this->ano_base} 
                        AND id_movimento IN ({$array_est[$id_projeto]})";  
                                            
                        $hr = mysql_query($sqlYear);
                        
                        while($hora_ext = mysql_fetch_assoc($hr)){
                            $arr_hora_extra[$hora_ext['id_clt']][$hora_ext['mes_mov']] +=  $hora_ext['qnt_horas'];  
                        } 
                        $arr_hora_extra_final[$id_projeto] = $arr_hora_extra;
                        $p++;
                }
                return $arr_hora_extra_final;
        }
    
    public function incluiContribuicao($id_projetos, $array_est){
       $p=0;       
       while($id_projetos[$p]){      
           
            $id_projeto = $id_projetos[$p];

//            $sql= "SELECT id_clt, valor_movimento 
//                    FROM rh_movimentos_clt 
//                    WHERE id_projeto = {$id_projeto} 
//                    AND cod_movimento = 5019 
//                    AND ano_mov = {$this->ano_base} 
//                    AND id_movimento IN({$array_est[$id_projeto]})";
            
//            $sql = " SELECT id_clt, SUM(valor) as valor FROM (
//                    select id_clt, valor from rh_movimentos_rescisao where id_mov = 21 
//                    union 
//                    select id_clt, valor_movimento AS valor from rh_movimentos_clt where id_mov = 21  AND id_movimento IN({$array_est[$id_projeto]})
//                    ) AS t GROUP BY id_clt ";
            
            $sql = "SELECT id_clt, SUM(valor) AS valor FROM
                (SELECT id_clt, SUM(valor) AS valor
                FROM (
                SELECT id_clt, SUM(valor) AS valor
                FROM rh_movimentos_rescisao
                WHERE id_mov IN(21)
                GROUP BY id_clt, valor 
                UNION
                SELECT id_clt, SUM(valor_movimento) AS valor
                FROM rh_movimentos_clt A
                WHERE id_mov IN(21,375) AND id_movimento IN({$array_est[$id_projeto]}) AND (A.`status` = 5 OR (A.`status` = 1 AND A.mes_mov = 16))
                GROUP BY id_clt, valor_movimento 
                ) AS t
                GROUP BY id_clt, valor) final GROUP BY id_clt ";
                    
            $ctr = mysql_query($sql);
            
            while($contribuicao = mysql_fetch_assoc($ctr)){                
                            $arr_contribuicao[$id_projeto][$contribuicao["id_clt"]] = $contribuicao["valor"]; 
                        }
            $p++;
       }
//       
       return $arr_contribuicao;
       
    }
    
    public function incluiContribuicaoAss($id_projetos, $array_est){
       $p=0;       
       while($id_projetos[$p]){      
           
            $id_projeto = $id_projetos[$p];

//            $sql= "SELECT id_clt, valor_movimento 
//                    FROM rh_movimentos_clt 
//                    WHERE id_projeto = {$id_projeto} 
//                    AND cod_movimento = 5019 
//                    AND ano_mov = {$this->ano_base} 
//                    AND id_movimento IN({$array_est[$id_projeto]})";
            
            
            $sql = "SELECT id_clt, SUM(valor) AS valor FROM
                    (SELECT id_clt, SUM(valor) AS valor
                    FROM (
                    SELECT id_clt, SUM(valor) AS valor
                    FROM rh_movimentos_rescisao
                    WHERE id_mov IN(242)
                    GROUP BY id_clt, valor 
                    UNION
                    SELECT id_clt, SUM(valor_movimento) AS valor
                    FROM rh_movimentos_clt A
                    WHERE id_mov IN(242, 395, 413) AND id_movimento IN({$array_est[$id_projeto]}) AND (A.`status` = 5 OR (A.`status` = 1 AND A.mes_mov = 16))
                    GROUP BY id_clt, valor_movimento 
                    ) AS t
                    GROUP BY id_clt, valor) final GROUP BY id_clt ";
                    
                    
            
//            $sql = " SELECT id_clt, SUM(valor) as valor FROM (
//                    select id_clt, valor from rh_movimentos_rescisao where id_mov in(242, 395, 413)  
//                    union 
//                    select id_clt, valor_movimento AS valor from rh_movimentos_clt where id_mov in(242, 395, 413) AND id_movimento IN({$array_est[$id_projeto]})
//                    ) AS t GROUP BY id_clt ";
                    
            $ctr = mysql_query($sql);
            
            while($contribuicao = mysql_fetch_assoc($ctr)){                
                            $arr_contribuicao[$id_projeto][$contribuicao["id_clt"]] = $contribuicao["valor"]; 
                        }
            $p++;
       }    
       return $arr_contribuicao;
       
    }

    public function getMatriz() {
        return $this->matriz;
    }

    public function montaDetalhe($arquivo, $arr_hora_extra, $arr_contribuicao, $arr_contribuicaoAss) {
        /* Linha 3 */
        // MONTANDO DETALHE DO ARQUIVO
        
        $empregados = $this->matriz;
        $this->total_registros = count($empregados);  
        
       

        foreach ($empregados as $empregado) {
            //
            //   CASOS ESPECÍFICOS
            //
            
            
            //FRANKLIN PEREIRA  -- zerar setembro, pois está depois da data de desligamento (agosto)
            if($empregado['dados']['id_clt'] == 1987){
                $empregado['salario']['09'] = 0;                
            }
            
            //ACACIO COUTINHO  -- CORREÇÃO MAIO(SUBTRAÇÃO DO ADIANTAMENTO 13º)
            if($empregado['dados']['id_clt'] == 81){
                $empregado['salario']['05'] = 366099;
            }
            
            //JORGE LUIZ AMBROSIO DOS SANTOS - O base_inss do rh_folha_proc não somou o base_inss do rh_ferias
            if($empregado['dados']['id_clt'] == 106){
                $empregado['salario']['06'] = 289759;
            }
            
            //MARCELO MENEZES DE ANDRADE -- retirado o 1/3 abono, que está somando no base_inss
            if($empregado['dados']['id_clt'] == 4552){
                $empregado['salario']['10'] = 1512500;
            }
            
            //ELIANA APARECIDA LIMA LOBATO -- NÃO ENTROU VALOR DE FÉRIAS NO BASE_INSS
            if($empregado['dados']['id_clt'] == 658){
                $empregado['salario']['06'] = 822214;
            }
            
            //ELIANA PATRON CHAPIRA -- DEDUZIDO O VALOR DO ADIANTAMENTO DE 13º QUE ESTÁ SENDO SOMADO NO BASE_INSS INDEVIDAMENTE
            if($empregado['dados']['id_clt'] == 4280){
                $empregado['salario']['10'] = 1460074;
            }
            
            //print_r($this->sequencial);
            
            
            $sequencial3 = sprintf("%06s", $this->sequencial);
            fwrite($arquivo, $sequencial3, 6);

            $cnpj = RemoveCaracteres($this->cnpj);
            $cnpj = substr($cnpj, 0, 14);
            $cnpj = sprintf("%014s", $cnpj);
            //Inscrição CNPJ/CEI do 1º estabelecimento do arquivo
            fwrite($arquivo, $cnpj, 14);

            $prefixo = '00';
            //Prefixo do 1º estabelecimento do arquivo
            fwrite($arquivo, $prefixo, 2);

            $registro = '2';
            //Tipo do registro = 2
            fwrite($arquivo, $registro, 1);

            $pis = RemoveCaracteres($empregado['dados']['pis']);
            $pis = sprintf("%011s", $pis);
            //Código PIS/PASEP
            fwrite($arquivo, $pis, 11);

            $nome = RemoveAcentos(RemoveCaracteres($empregado['dados']['nome']));
            $nome = sprintf("%-52s", $nome);
            //Nome do Empregado
            fwrite($arquivo, $nome, 52);

            //Data de Nascimento (ddmmaaaa)
            fwrite($arquivo, $empregado['dados']['data_nasci'], 8);

//            if ($empregado_nacionalidade == 'BRASILEIRO' || $empregado_nacionalidade == 'BRASILEIRA') {
//                $empregado_nacionalidade = '10';
//                $empregado_ano_chegada = NULL;
//            }

            $empregado_nacionalidade = $empregado['dados']['cod_pais_rais'];
            $empregado_nacionalidade = sprintf("%02s", $empregado_nacionalidade);
            //Nacionalidade
            fwrite($arquivo, $empregado_nacionalidade, 2);

            $anoChegada = explode('-', $empregado['dados']['dtChegadaPais']);
            $empregado_ano_chegada = sprintf("%04s", $anoChegada[0]);
            //Ano de Chegada ao país (aaaa)
            fwrite($arquivo, $empregado_ano_chegada, 4);

            $instrucao = $empregado['dados']['grauEscolar'];
            $instrucao = sprintf("%02s", $instrucao);
            //Grau de Instrução (01 a 11)
            fwrite($arquivo, $instrucao, 2);

            $empregado_cpf = RemoveCaracteres($empregado['dados']['cpf']);
            $empregado_cpf = sprintf("%011s", $empregado_cpf);
            //CPF
            fwrite($arquivo, $empregado_cpf, 11);

            $empregado_ctps = $empregado['dados']['campo1'];            
            
            $empregado_ctps = RemoveCaracteres(RemoveEspacos($empregado_ctps));
            $empregado_ctps = sprintf("%08s", $empregado_ctps);
            //CTPS - (número)
            fwrite($arquivo, $empregado_ctps, 8);


            $empregado_ctps_serie = $empregado['dados']['serie_ctps'];
            $empregado_ctps_serie = str_replace(["A","a"] , "0", $empregado_ctps_serie);
            $empregado_ctps_serie = RemoveCaracteres($empregado_ctps_serie);
            $empregado_ctps_serie = sprintf("%05s", $empregado_ctps_serie);
            //CTPS - (série)
            fwrite($arquivo, $empregado_ctps_serie, 5);

            //Data de Admissão/Data da Transferência (ddmmaaaa)
            fwrite($arquivo, $empregado['dados']['data_entrada'], 8);

            $empregado_tipo_admissao = '2';
            $empregado_tipo_admissao = sprintf("%02s", $empregado_tipo_admissao);
            //Tipo de Admissão
            fwrite($arquivo, $empregado_tipo_admissao, 2);

            //------------------------------------------------------------------
            //$salario_anterior=0;
            
           //echo '<pre>' . var_export($empregado, true) . '</pre>';
            
//            $salario_anterior = $this->verificaSalarioContratual($empregado['dados']['id_clt'], $empregado['dados']['id_curso'], $empregado['dados']['ano_entrada']);
            //echo $empregado['dados']['id_clt'] . ' - ' . $salario_anterior.'<br>';
                        
//            $empregado_salario_contratual = (empty($salario_anterior)) ? $empregado['dados']['salario_curso'] : $salario_anterior;
            $empregado_salario_contratual = $empregado['dados']['salario_curso'];
            if($empregado['dados']['id_curso'] == '6580'){
//                $empregado_salario_contratual = '470208';
                $empregado_salario_contratual = $empregado['dados']['valor_hora'];
                
            }
            if($empregado['dados']['id_curso'] == '6894'){
                 $empregado_salario_contratual = '113176';
            }
            //------------------------------------------------------------------
//            $empregado_salario_contratual = $empregado['dados']['sallimpo'];
            
            
            $empregado_salario_contratual = RemoveCaracteres($empregado_salario_contratual);            
            $empregado_salario_contratual = sprintf("%09s", $empregado_salario_contratual);
            //Salário Contratual (Valor com centavos)
//              echo $empregado['dados']['id_clt'] . ' - ' .   $empregado_salario_contratual . "<br>";
            fwrite($arquivo, $empregado_salario_contratual, 9);
            
            
            $empregado_tipo_salario = '1';
            
            if($empregado['dados']['id_curso'] == '6580'){
                $empregado_tipo_salario = '5';                
            }
            if($empregado['dados']['id_curso'] == '6894'){
                 $empregado_tipo_salario = '7';
            }
            
            $empregado_tipo_salario = sprintf("%01s", $empregado_tipo_salario);
            //Tipo de Salário Contratual
            fwrite($arquivo, $empregado_tipo_salario, 1);
            
            
            $horas_semanais = $empregado['dados']['horas_semanais'];           
            
            $horas_semanais = sprintf("%02s", $horas_semanais);
            //Horas Semanais
            fwrite($arquivo, $horas_semanais, 2);

            $cbo = $empregado['dados']['cbo'];
            $cbo = RemoveCaracteres($cbo);
            $cbo = sprintf("%06s", $cbo);
            //CBO
            fwrite($arquivo, $cbo, 6);

            $vinculo = '10';
            $vinculo = sprintf("%02s", $vinculo);
            //Vínculo empregatício
            fwrite($arquivo, $vinculo, 2);
//            $sql_resc = "
//                SELECT A.data_demi, A.motivo, A.ferias_aviso_indenizado, A.aviso_valor, A.lei_12_506, A.saldo_salario, A.dt_salario, SUM(A.ferias_pr + A.ferias_vencidas + A.umterco_fv + A.umterco_fp + A.um_terco_ferias_dobro + A.umterco_ferias_aviso_indenizado + A.fv_dobro + IFNULL(B.valor,0)) ferias_indenizadas 
//                FROM rh_recisao AS A
//                LEFT JOIN (SELECT id_clt, SUM(valor) AS valor, id_rescisao FROM rh_movimentos_rescisao A WHERE id_clt = {$empregado['dados']['id_clt']} AND id_mov IN (387, 386)) AS B ON (A.id_recisao = B.id_rescisao)
//                WHERE A.id_clt = " . $empregado['dados']['id_clt'] . " AND year(A.data_demi) = " . $this->ano_base . " AND A.motivo IN (" . implode(",", $this->tiposRescisao) . ") AND A.status = 1;";
            $sql_resc = "
                SELECT A.valor_faltas, A.id_recisao, A.data_demi, A.motivo, SUM(A.ferias_aviso_indenizado) ferias_aviso_indenizado, (SUM(A.aviso_valor)+SUM(A.lei_12_506)) aviso_valor, SUM(A.lei_12_506) lei_12_506, SUM(A.saldo_salario + A.insalubridade) saldo_salario, SUM(A.dt_salario) dt_salario, SUM(A.terceiro_ss) as terceiro_ss, SUM(A.ferias_pr + A.ferias_vencidas + A.umterco_fv + A.umterco_fp + A.um_terco_ferias_dobro + A.umterco_ferias_aviso_indenizado + A.fv_dobro + A.ferias_aviso_indenizado) ferias_indenizadas
                FROM rh_recisao AS A
                LEFT JOIN (SELECT id_clt, SUM(valor) AS valor, id_rescisao FROM rh_movimentos_rescisao A WHERE id_clt = {$empregado['dados']['id_clt']} AND id_mov IN (387, 386)) AS B ON (A.id_recisao = B.id_rescisao)
                WHERE A.id_clt = " . $empregado['dados']['id_clt'] . " AND year(A.data_demi) = " . $this->ano_base . " AND A.motivo IN (" . implode(",", $this->tiposRescisao) . ") AND A.status = 1;";
                
//            if($_COOKIE['logado'] == 257) { echo "$sql_resc<br>";}
            //$sql_resc = "SELECT data_demi, motivo, ferias_aviso_indenizado, aviso_valor, lei_12_506 FROM rh_recisao WHERE id_clt = " . $empregado['dados']['id_clt'] . " AND year(data_demi) = " . $this->ano_base . " AND motivo IN (60,61,62,80,81,100) AND status = 1";
            $qr_rescisao = mysql_query($sql_resc) or die("erro: " . mysql_error());
            //   $qr_rescisao = mysql_query("SELECT data_demi, motivo FROM rh_recisao WHERE id_clt = " . $empregado['dados']['id_clt'] . " AND year(data_demi) = " . $this->ano_base . " AND motivo IN (60,61,62,80,81,100)") or die("erro: " . mysql_error());
            //echo $sql_resc.";\r\n";

            $rescisao = mysql_fetch_assoc($qr_rescisao);
            $verifica_rescisao = mysql_num_rows($qr_rescisao);            
            /**
             * INICIO MÉDIA RESCISÃO
             */
            $arrayMovMediasFeriasEDt = array(383, 384, 385, 386, 387, 388, 408, 409, 410, 66, 366, 396, 365, 364);

            if ($row_rescisao['rescisao_complementar'] == 0) {

                $sql_movTemp = "SELECT B.descicao, B.id_mov, A.valor,B.categoria, C.tipo_movimento, C.valor_movimento, B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                                FROM rh_movimentos_rescisao AS A
                                LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                                LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$empregado['dados']['id_clt']}' AND A.status = 1) AS C ON(B.id_mov = C.id_mov)
                                WHERE A.id_clt = '{$empregado['dados']['id_clt']}' 
                                AND A.id_mov IN(" . implode(',', $arrayMovMediasFeriasEDt) . ")
                                AND A.status = 1 GROUP BY A.id_mov";
               
                $qr_movimentosTemp = mysql_query($sql_movTemp) or die(mysql_error());
                
            }
            
            while ($row_movimentosTemp = mysql_fetch_assoc($qr_movimentosTemp)) {
                $arrTemp[] = $row_movimentosTemp;
                /* -------------BLOCO 13 SALARIO------------- */
                //MEDIA 13º PROJEÇAO AVISO PREVIO (junto com o campo 70)
                if ($row_movimentosTemp['id_mov'] == 410) {
                    $rescisao['terceiro_ss'] = $rescisao['terceiro_ss'] + $row_movimentosTemp['valor'];
                }

                //MEDIA SOBRE 13º SALARIO
                if ($row_movimentosTemp['id_mov'] == 384) {
                    $rescisao['dt_salario'] = $rescisao['dt_salario'] + $row_movimentosTemp['valor'];
                }

                /* -------------BLOCO FÉRIAS VENCIDAS------------- */
                //MEDIA SOBRE FERIAS INDENIZADAS
                if ($row_movimentosTemp['id_mov'] == 385) {
//                    $rescisao['ferias_vencidas'] = $rescisao['ferias_vencidas'] + $row_movimentosTemp['valor'];
                    $rescisao['ferias_indenizadas'] = $rescisao['ferias_indenizadas'] + $row_movimentosTemp['valor'];
                    
                }
                

                //MEDIA FERIAS PROJEÇAO AVISO PREVIO (junto com o campo 71)
                if ($row_movimentosTemp['id_mov'] == 408) {
//                    $rescisao['ferias_aviso_indenizado'] = $rescisao['ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
                    $rescisao['ferias_indenizadas'] = $rescisao['ferias_indenizadas'] + $row_movimentosTemp['valor'];
                    
                }

                //1/3 MEDIA FERIAS PROJEÇãO AVISO PREVIO (junto com o campo 75)
                if ($row_movimentosTemp['id_mov'] == 409) {
//                    $rescisao['umterco_ferias_aviso_indenizado'] = $rescisao['umterco_ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
                    $rescisao['ferias_indenizadas'] = $rescisao['ferias_indenizadas'] + $row_movimentosTemp['valor'];
                    
                }

                /* -------------BLOCO FÉRIAS PROPORCIONAIS------------- */
                //MEDIA SOBRE FERIAS PROPORCIONAIS
                if ($row_movimentosTemp['id_mov'] == 387) {
//                    $rescisao['ferias_pr'] = $rescisao['ferias_pr'] + $row_movimentosTemp['valor'];
                    $rescisao['ferias_indenizadas'] = $rescisao['ferias_indenizadas'] + $row_movimentosTemp['valor'];
                    
                }

                //1/3 DE MEDIA SOBRE FERIAS INDENIZADAS
                if ($row_movimentosTemp['id_mov'] == 386) {
//                    $rescisao['umterco_fp'] = $rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
                    $rescisao['ferias_indenizadas'] = $rescisao['ferias_indenizadas'] + $row_movimentosTemp['valor'];
                   
                }

                //1/3 DE MEDIA SOBRE FERIAS PROPORCIONAIS
                if ($row_movimentosTemp['id_mov'] == 388) {
//                    $rescisao['umterco_fp'] = $rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
                    $rescisao['ferias_indenizadas'] = $rescisao['ferias_indenizadas'] + $row_movimentosTemp['valor'];
                    
                }
                
                //MEDIA SOBRE FERIAS PROPORCIONAIS
                if ($row_movimentosTemp['id_mov'] == 383) {
                    $rescisao['aviso_valor'] = $rescisao['aviso_valor'] + $row_movimentosTemp['valor'];
                }
                
                //adicional noturno
                if ($row_movimentosTemp['id_mov'] == 66) {
                    $rescisao['saldo_salario'] = $rescisao['saldo_salario'] + $row_movimentosTemp['valor'];
                }
                
                //dsr sobre adicional noturno
                if ($row_movimentosTemp['id_mov'] == 366) {
                    $rescisao['saldo_salario'] = $rescisao['saldo_salario'] + $row_movimentosTemp['valor'];
                }
                //DSR SOBRE HORA EXTRA  - RESCISAO
                if ($row_movimentosTemp['id_mov'] == 365) {
                    $rescisao['saldo_salario'] = $rescisao['saldo_salario'] + $row_movimentosTemp['valor'];
                }
                
                // dif salarial
                if ($row_movimentosTemp['id_mov'] == 396) {
                    $rescisao['saldo_salario'] = $rescisao['saldo_salario'] + $row_movimentosTemp['valor'];
                }
                
                //HORA EXTRA 90% -  RESCISAO
                 if ($row_movimentosTemp['id_mov'] == 364) {
                    $rescisao['saldo_salario'] = $rescisao['saldo_salario'] + $row_movimentosTemp['valor'];
                }                
            }
            
            //subtração do valor das faltas do saldo_salario
            $rescisao['saldo_salario'] = $rescisao['saldo_salario'] - $rescisao['valor_faltas'];
            
            /////////////////////FIM MÉDIA RESCISÃO///////////////////////////
            
            if ($verifica_rescisao > 0 && $rescisao['data_demi'] != '') {
                $dia_mes_desligamento = substr(implode('', array_reverse(explode('-', $rescisao['data_demi']))), 0, 4);
                $mes_desligamento = substr($dia_mes_desligamento, 2, 4);

                //CODIGOS DE DESLIGAMENTO TRATAMENTO NO PHP
                //20. Rescisão com justa causa por iniciativa do empregado (60 FATOR empregado)
                //21. Rescisão sem justa causa por iniciativa do empregado (60 FATOR empregado)

                /* if ($rescisao['motivo'] == 60) {
                  $cod_desligamento = '10';
                  } elseif ($rescisao['motivo'] == 61) {
                  $cod_desligamento = '11';
                  } elseif ($rescisao['motivo'] == 62 or $rescisao['motivo'] == 100) {
                  $cod_desligamento = '12';
                  } elseif ($rescisao['motivo'] == 80) {
                  $cod_desligamento = '76';
                  } elseif ($rescisao['motivo'] == 81) {
                  $cod_desligamento = '60';
                  } */

                $cod_desligamento = $this->tiposRescisaoRais[$rescisao['motivo']];
            } else {
                $dia_mes_desligamento = '0';
                $mes_desligamento = '0'; //13
                $cod_desligamento = '0';
            }


            $cod_desligamento = sprintf("%02s", $cod_desligamento);
            //Código do desligamento
            fwrite($arquivo, $cod_desligamento, 2);

            $dia_mes_desligamento = sprintf("%04s", $dia_mes_desligamento);
            //Data do desligamento (ddmm)
            fwrite($arquivo, $dia_mes_desligamento, 4);

            $decTerceiro = '2';

            //Remuneração 12 MESES

            for ($f = 1; $f < 13; $f++) {
                $mes = sprintf('%02d', $f);
                if (!empty($empregado["salario"][$mes])) {
                    
                    if($mes == $mes_desligamento) {
                        $remuneracao = number_format($rescisao['saldo_salario'], 2, '', '');
                        $remuneracao = sprintf("%09s", $remuneracao);
                    } else {
                        $remuneracao = $empregado["salario"][$mes];
                        $remuneracao = sprintf("%09s", $remuneracao);
                    }
                    fwrite($arquivo, $remuneracao, 9);
                } else {
                    fwrite($arquivo, sprintf("%09s", "0"), 9);
                }
            }
            
//            echo "<pre>";
//                            print_r($rescisao);
//                            print_r($empregado);
//                            print_r($mes_desligamento);
//                        echo "</pre>";
            // 13º salario 
            $qryVerificaLei = "SELECT A.valor_movimento FROM rh_movimentos_clt AS A 
                                                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                                               WHERE A.cod_movimento = 80042 AND A.`status` > 0 AND A.valor_movimento > 0 AND A.id_clt = '{$empregado['dados']['id_clt']}'";
            $sqlVerificaLei = mysql_query($qryVerificaLei) or die('Erro ao verificar dados de lei');  
            
             
            
            $valorLei = 0;
            while($rowsLei = mysql_fetch_assoc($sqlVerificaLei)){
                $valorLei = $rowsLei['valor_movimento'];
            }
            
            $qry_verifica_adiantamento ="select *, B.mes from rh_movimentos_clt AS A
                                        left join rh_ferias AS B ON (A.id_clt = B.id_clt and B.ano =  A.ano_mov)
                                        where A.id_clt= '{$empregado['dados']['id_clt']}' and A.mes_mov = 17 and A.ano_mov = '{$this->ano_base}' and A.cod_movimento = 80030 and A.status <> 0 and B.status <> 0";
                     
            $sql_verifica_adiantamento = mysql_query($qry_verifica_adiantamento) or die('Erro ao verificar adiantamento');
            $valor_adiantamento = 0;
            while($rowsAdiantamento = mysql_fetch_assoc($sql_verifica_adiantamento)){
                $valor_adiantamento = ['mes' => $rowsAdiantamento['mes'], 'valor' => $rowsAdiantamento['valor_movimento']];
            }                  
            
            if($valor_adiantamento['valor'] > 0){
               
                $empregado['adiantamento'] = $valor_adiantamento ; 
            }           
            
           // consta adiantamento nas férias
           if (is_array($empregado['adiantamento'])) {
               
               fwrite($arquivo, sprintf("%09s", RemoveCaracteres($empregado['adiantamento']['valor'])), 9);
               fwrite($arquivo, sprintf("%02s", $empregado['adiantamento']['mes']), 2);
//               echo "---INCLUIU ADIANTAMENTO DE FERIAS". "<br>";
               if($mes_desligamento != 0){
                    //SE MES DE DESLIGAMENTO
                    $mes_desligamento = ($rescisao['motivo'] == 60) ? null : $mes_desligamento;
                    $valordecimofinal = ($rescisao['motivo'] == 60) ? null : number_format($rescisao['dt_salario'] + $rescisao['terceiro_ss'] + $valorLei, 2, '', '');

                    fwrite($arquivo, sprintf("%09s", RemoveCaracteres($valordecimofinal)), 9);
                    fwrite($arquivo, sprintf("%02s", $mes_desligamento), 2);
//                        echo "----INCLUIU MES RESCISÃO". "<br>";
                } else if (is_array($empregado['terceitoSal'])) {   
                    $mesComAdiantamento = 12; 
                    fwrite($arquivo, sprintf("%09s", RemoveCaracteres(end($empregado['terceitoSal']))), 9);
                    fwrite($arquivo, sprintf("%02s", $mesComAdiantamento), 2);
//                    echo "---INCLUIU MES 12 DE DECIMO COM ADIANTAMENTO". "<br>";
               }else{
                    fwrite($arquivo, sprintf("%011s", "0"), 11);
               }
            //sem adiantamento nas férias
           }else if (is_array($empregado['terceitoSal'])){
              $countDecimo = count($empregado['terceitoSal']);           
              if ($countDecimo == 1) {
                  
                  fwrite($arquivo, sprintf("%011s", "0"), 11);
//                  echo "----DEIXOU A PRIMEIRA PARCELA EM BRANCO". "<br>";
              }
              
              foreach ($empregado["terceitoSal"] as $key => $terceiro) {                   
                    
                    if($key == $mes_desligamento){
                        //SE MES DE DESLIGAMENTO
                        $mes_desligamento = ($rescisao['motivo'] == 60) ? null : $mes_desligamento;
                        $valordecimofinal = ($rescisao['motivo'] == 60) ? null : number_format($rescisao['dt_salario'] + $rescisao['terceiro_ss'] + $valorLei, 2, '', '');
                        
                        fwrite($arquivo, sprintf("%09s", RemoveCaracteres($valordecimofinal)), 9);
                        fwrite($arquivo, sprintf("%02s", $mes_desligamento), 2);
//                        echo "----INCLUIU MES RESCISÃO". "<br>";
                    } else {
                        
                        if($terceiro == '0' || $terceiro == '0.00' || $terceiro == ''){
                            $key = 0;
                        }
                        
                        fwrite($arquivo, sprintf("%09s", RemoveCaracteres($terceiro)), 9);
                        fwrite($arquivo, sprintf("%02s", $key), 2);
//                        echo "----INCLUIU 13º NORMAL". "<br>";
                    }                    
              }
           } else {
                if($mes_desligamento > 0 && $rescisao['dt_salario'] > 0){                    
                    if($rescisao['dt_salario'] > 0){
                        $dt_salario = number_format($rescisao['dt_salario'] + $rescisao['terceiro_ss'] + $valorLei, 2, '', '');
                    } 
                    fwrite($arquivo, sprintf("%020s", RemoveCaracteres($dt_salario)), 20);
                    fwrite($arquivo, sprintf("%02s", $mes_desligamento), 2);
//                    echo "-----INSERIU COM DESLIGAMENTO". "<br>";
                    
                } else {
                    fwrite($arquivo, sprintf("%022s", "0"), 22);
//                    echo "-----TUDO ZERADO". "<br>";
                }
            }
            
            

//            if (is_array($empregado['terceitoSal'])) {
//                $countDecimo = count($empregado['terceitoSal']);
//                echo "QUANTIDADE DE 13º : ".$countDecimo. "<br>";
//                if ($countDecimo == 1) {
//                    //se array tiver só um elemento, ou seja, sem adiantamento do 13º
//                    fwrite($arquivo, sprintf("%011s", "0"), 11);
//                }
//
//                foreach ($empregado["terceitoSal"] as $key => $terceiro) {
//                    echo "key de empregado['terceitoSal'] : ".$key. "<br>";
//                    echo "valor de empregado['terceitoSal'] : ".$terceiro. "<br>";
//                    if($key == $mes_desligamento){
//                        
//                        $mes_desligamento = ($rescisao['motivo'] == 60) ? null : $mes_desligamento;
//                        $valordecimofinal = ($rescisao['motivo'] == 60) ? null : number_format($rescisao['dt_salario'] + $rescisao['terceiro_ss'] + $valorLei, 2, '', '');
//                        fwrite($arquivo, sprintf("%09s", RemoveCaracteres($valordecimofinal)), 9);
//                        fwrite($arquivo, sprintf("%02s", $mes_desligamento), 2);
//                    } else {
//                        if ($terceiro[$key] == '0.00' || $terceiro[$key] == '') {
//                            
//                            $terceiro[$key] = $terceiro[$key-1];
//                            
//                            if($terceiro[$key] == "0" || $terceiro[$key] == ''){
//                                $key = "0";
//                            }
//                        }
//                        fwrite($arquivo, sprintf("%09s", RemoveCaracteres($terceiro[$key])), 9);
//                        fwrite($arquivo, sprintf("%02s", $key), 2);
//                    }
//                }
//                
//            } else {
//                if($mes_desligamento > 0 && $rescisao['dt_salario'] > 0){                    
//                    if($rescisao['dt_salario'] > 0){
//                        $dt_salario = number_format($rescisao['dt_salario'] + $rescisao['terceiro_ss'] + $valorLei, 2, '', '');
//                    } 
//                    fwrite($arquivo, sprintf("%020s", RemoveCaracteres($dt_salario)), 20);
//                    fwrite($arquivo, sprintf("%02s", $mes_desligamento), 2);
//                } else {
//                    fwrite($arquivo, sprintf("%022s", "0"), 22);
//                }
//            }
            
//            
            // até aqui
            
            $etnia = $empregado['dados']['etnia'];
            if(empty($etnia)){
                $etnia = '09';
            }
            $etnia = substr($etnia, 1, 2);
            $etnia = sprintf("%01s", $etnia);
            //Raça/Cor
            fwrite($arquivo, $etnia, 1);

            if (!empty($empregado['dados']['deficiencia'])) {
                $indicador_deficiencia = '1';
                $tipo_deficiencia = $empregado['dados']['deficiencia'];
            } else {
                $indicador_deficiencia = '2';
                $tipo_deficiencia = '0';
            }
            
            //            Indicador de Deficiência: 
            //                1 - sim
            //                2 - não
            $indicador_deficiencia = sprintf("%01s", $indicador_deficiencia);
            fwrite($arquivo, $indicador_deficiencia, 1);

            $tipo_deficiencia = sprintf("%01s", $tipo_deficiencia);
            //Tipo de Deficiência
            fwrite($arquivo, $tipo_deficiencia, 1);

            $indicador_alvara = 2;
            $indicador_alvara = sprintf("%01s", $indicador_alvara);
            //Indicador de Alvará
            fwrite($arquivo, $indicador_alvara, 1);

            // replicar isso
            $aviso_valor = $rescisao['aviso_valor'];
            $lei_12_506 = RemoveCaracteres($rescisao['lei_12_506']);
//            $aviso_previo_indenizado = $aviso_valor + $lei_12_506;           
            
                       
            $aviso_previo_indenizado = number_format($aviso_valor,2,".", "");
            $aviso_previo_indenizado = RemoveCaracteres($aviso_previo_indenizado);
            $aviso_previo_indenizado = sprintf("%09s", $aviso_previo_indenizado);
            //Aviso Prévio Indenizado (valor com centavos)
            
            if($mes_desligamento != 0){
                fwrite($arquivo, $aviso_previo_indenizado, 9);
            }else{
                $aviso_previo_indenizado = sprintf("%09s", 0);
                fwrite($arquivo, $aviso_previo_indenizado, 9);
            }
            
            

            $empregado_sexo = strtoupper($empregado['dados']['sexo']);
            if ($empregado_sexo == 'M') {
                $empregado_sexo = '1';
            } elseif ($empregado_sexo == "F") {
                $empregado_sexo = '2';
            }

            $empregado_sexo = sprintf("%01s", $empregado_sexo);
            
            //            Sexo: 
            //                1 - Masculino
            //                2 - Feminino
            fwrite($arquivo, $empregado_sexo, 1);

            /* Afastamentos */

//$qr_afastamentos = mysql_query("SELECT *, IF (year(data_retorno) > $this->ano_base,'$this->ano_base-12-31', data_retorno) AS data_retorno2 FROM rh_eventos WHERE cod_status IN (70,20,50,30,90) AND id_clt = " . $empregado['dados']['id_clt'] . " AND year(data) = '$this->ano_base' AND status = 1 ORDER BY id_evento DESC LIMIT 0,3") or die("erro: " . mysql_error());
            
            $qr_afastamentos = mysql_query("
            SELECT *, 
                IF (YEAR(data_retorno) > $this->ano_base,'$this->ano_base-12-31', IF(YEAR(data_retorno) = 0, '$this->ano_base-12-31', data_retorno)) AS data_retorno2,
                IF (YEAR(data) < $this->ano_base, '$this->ano_base-01-01',data) data2
            FROM rh_eventos 
            WHERE cod_status IN (80, 70,20,50,30,90,21,67) AND id_clt = " . $empregado['dados']['id_clt'] . " AND year(data) = '$this->ano_base' AND status = 1 
            ORDER BY id_evento DESC LIMIT 0,3") or die("erro: " . mysql_error());
            
            
//            while ($afastamentos = mysql_fetch_assoc($qr_afastamentos)) {
//                $afastamento_motivo[] = $afastamentos['cod_status'];
//                $afastamento_inicio[] = $afastamentos['data'];
//                $afastamento_final[] = $afastamentos['data_retorno2'];
//                // editado em 20/03/2015
//                $afastamento_dias[] = ($afastamentos['cod_status'] == 50) ? $afastamentos['dias'] : $this->calcDiasLicenca($afastamentos['data']);
//                // editado em 20/03/2015
//            }
            while ($afastamentos = mysql_fetch_assoc($qr_afastamentos)) {
                $afastamento_motivo[] = $afastamentos['cod_status'];
                $afastamento_inicio[] = $afastamentos['data2'];
                $afastamento_final[] = $afastamentos['data_retorno2'];
                // editado em 20/03/2015                
                $afastamento_dias[] = $this->calcDiasLicenca($afastamentos['data'], $afastamentos['data_retorno']);
                // editado em 20/03/2015
            }

            $total_dias = 0;
            for ($z = 0; $z <= 2; $z++) {
                
                $total_dias += $afastamento_dias[$z];
                if ($afastamento_motivo[$z] == 70) {
                    $afastamento_motivo_final[$z] = '10';
                } elseif ($afastamento_motivo[$z] == 20) {
                    $afastamento_motivo_final[$z] = '40';
                } elseif ($afastamento_motivo[$z] == 50) {
                    $afastamento_motivo_final[$z] = '50';
                } elseif ($afastamento_motivo[$z] == 30) {
                    $afastamento_motivo_final[$z] = '60';
                } elseif ($afastamento_motivo[$z] == 90) {
                    $afastamento_motivo_final[$z] = '70';
                }elseif ($afastamento_motivo[$z] == 21) {
                    $afastamento_motivo_final[$z] = '40';
                }elseif ($afastamento_motivo[$z] == 67) {
                    $afastamento_motivo_final[$z] = '70';
                }

                $afastamento_motivo_fin = sprintf("%02s", $afastamento_motivo_final[$z]);
                //Motivo do Primeiro Afastamento
                fwrite($arquivo, $afastamento_motivo_fin, 2);

                $afastamento_ini = substr(implode('', array_reverse(explode('-', $afastamento_inicio[$z]))), 0, 4);
                $afastamento_ini = sprintf("%04s", $afastamento_ini);
                
                //FRANKLIN PEREIRA
                if($empregado['dados']['id_clt'] == 1987){
                    $afastamento_ini = sprintf("%04s", 0);         
                }
                
                //Data Início do Primeiro Afastamento (ddmm)      
                fwrite($arquivo, $afastamento_ini, 4);

                //26-01-2016 (Renato) Pegar data do desligamento caso o final do afastamento seja maior que o desligamento
                if($afastamento_final[$z] > $rescisao['data_demi'] && $rescisao['data_demi'] != ''){
                    $afastamento_final[$z] = $rescisao['data_demi'];
                }

                $afastamento_fin = substr(implode('', array_reverse(explode('-', $afastamento_final[$z]))), 0, 4);
                $afastamento_fin = sprintf("%04s", $afastamento_fin);
                
                 //FRANKLIN PEREIRA
                if($empregado['dados']['id_clt'] == 1987){
                    $afastamento_fin = sprintf("%04s", 0);         
                }                
                
                //Data Final do Primeiro Afastamento (ddmm)
                fwrite($arquivo, $afastamento_fin, 4);
            }

            $afastamentoDias = RemoveCaracteres($total_dias);
            
            //FRANKLIN PEREIRA
                if($empregado['dados']['id_clt'] == 1987){
                    $afastamentoDias = 0;         
                }                
            
            //Quantidade Dias Afastamento
            fwrite($arquivo, sprintf("%03s", $afastamentoDias), 3); 
            
            
            if($mes_desligamento != 0){
                $rescisao['ferias_indenizadas'] = number_format($rescisao['ferias_indenizadas'], 2, ".","" );           
                $valor_ferias_indenizadas = RemoveCaracteres($rescisao['ferias_indenizadas']);            
                $valor_ferias_indenizadas = sprintf("%08s", $valor_ferias_indenizadas); 
            }else{
                $valor_ferias_indenizadas = 0;           
                $valor_ferias_indenizadas = sprintf("%08s", $valor_ferias_indenizadas);
            }
//           
            //Valor - férias indenizadas (com centavos)
            fwrite($arquivo, $valor_ferias_indenizadas, 8);

            $valor_banco_horas = NULL;
            $valor_banco_horas = sprintf("%08s", $valor_banco_horas);
            //Valor - banco de horas (com centavos)
            fwrite($arquivo, $valor_banco_horas, 8);

            $quantidade_meses_banco_horas = NULL;
            $quantidade_meses_banco_horas = sprintf("%02s", $quantidade_meses_banco_horas);
            //Quantidade de meses - banco de horas
            fwrite($arquivo, $quantidade_meses_banco_horas, 2);

            $valor_dissidio_coletivo = NULL;
            $valor_dissidio_coletivo = sprintf("%08s", $valor_dissidio_coletivo);
            //Valor - dissídio coletivo (com centavos)
            fwrite($arquivo, $valor_dissidio_coletivo, 8);

            $quantidade_meses_dissidio_coletivo = NULL;
            $quantidade_meses_dissidio_coletivo = sprintf("%02s", $quantidade_meses_dissidio_coletivo);
            //Quantidade de meses - dissídio coletivo
            fwrite($arquivo, $quantidade_meses_dissidio_coletivo, 2);

            $valor_gratificacoes = NULL;
            $valor_gratificacoes = sprintf("%08s", $valor_gratificacoes);
            //Valor - gratificações (com centavos)
            fwrite($arquivo, $valor_gratificacoes, 8);

            $quantidade_meses_gratificacoes = NULL;
            $quantidade_meses_gratificacoes = sprintf("%02s", $quantidade_meses_gratificacoes);
            //Quantidade de meses - gratificações
            fwrite($arquivo, $quantidade_meses_gratificacoes, 2);

            //multa rescisao
//            if($cod_desligamento == 11){
//                $qrescisao = "SELECT * FROM saida WHERE id_clt = {$empregado['dados']['id_clt']} AND tipo = 34 AND id_projeto = {$empregado['dados']['id_projeto']} AND status = 1 AND nome like '%FGTS%' LIMIT 1" ;
//                $resrec = mysql_query($qrescisao);
//                $line_rescisao = mysql_fetch_array($resrec);
//                $valor_multa_rescisao = RemoveCaracteres($line_rescisao['valor']);
//                $valor_multa_rescisao = str_replace(" ", "", $valor_multa_rescisao);
            
                $valor_multa_rescisao = NULL;
           
            //Valor - multa por rescisão sem justa causa (com centavos)
            $valor_multa_rescisao = sprintf("%08s", $valor_multa_rescisao);            
            fwrite($arquivo, $valor_multa_rescisao, 8);

            $cnpj_contribuicao_associativa1 = NULL;
            $cnpj_contribuicao_associativa1 = sprintf("%014s", $cnpj_contribuicao_associativa1);
            //CNPJ - contribuição associativa (1ª ocorrência)
            fwrite($arquivo, $cnpj_contribuicao_associativa1, 14);

            $valor_contribuicao_associativa1 = NULL;
            $valor_contribuicao_associativa1 = sprintf("%08s", $valor_contribuicao_associativa1);
            //Valor - contribuição associativa (1ª ocorrência) (com centavos)
            fwrite($arquivo, $valor_contribuicao_associativa1, 8);

            $cnpj_contribuicao_associativa2 = NULL;
            $cnpj_contribuicao_associativa2 = sprintf("%014s", $cnpj_contribuicao_associativa2);
            //CNPJ - contribuição associativa (2ª ocorrência)
            fwrite($arquivo, $cnpj_contribuicao_associativa2, 14);

            $valor_contribuicao_associativa2 = NULL;
            $valor_contribuicao_associativa2 = sprintf("%08s", $valor_contribuicao_associativa2);
            //Valor - contribuição associativa (2ª ocorrência) (com centavos)
            fwrite($arquivo, $valor_contribuicao_associativa2, 8);

            
            $cnpj_contribuicao_sindical = RemoveCaracteres($empregado['dados']['cnpj_sindical']);
            
            $cnpj_contribuicao_sindical = sprintf("%014s", $cnpj_contribuicao_sindical);
            // Contribuição Sindical -- cnpj
            fwrite($arquivo, $cnpj_contribuicao_sindical, 14);

            
            // contribuição sindical -- valor
            
            if(array_key_exists($empregado["dados"]["id_clt"], $arr_contribuicao[$empregado["dados"]["id_projeto"]]))
            {
               // echo $empregado["dados"]["id_clt"]." ESTÁ NA ARRAY ".$arr_contribuicao[$empregado["dados"]["id_projeto"]];
                $valor_sindicato = RemoveCaracteres(number_format($arr_contribuicao[$empregado["dados"]["id_projeto"]][$empregado["dados"]["id_clt"]], 2, '', ''));                
            }else{
                $valor_sindicato = null;
            }
            
            // contribuição sindical -- valor
            fwrite($arquivo, sprintf("%08s", $valor_sindicato), 8);
            
            

            $cnpj_contribuicao_assistencial = RemoveCaracteres($empregado['dados']['cnpj_sindical']);;
            $cnpj_contribuicao_assistencial = sprintf("%014s", $cnpj_contribuicao_assistencial);
            //CNPJ - contribuição assistencial
            fwrite($arquivo, $cnpj_contribuicao_assistencial, 14);
            
            $valor_contribuicao_assistencial = NULL;
            if(array_key_exists($empregado["dados"]["id_clt"], $arr_contribuicaoAss[$empregado["dados"]["id_projeto"]]))
            {
               // echo $empregado["dados"]["id_clt"]." ESTÁ NA ARRAY ".$arr_contribuicao[$empregado["dados"]["id_projeto"]];
                $valor_contribuicao_assistencial = RemoveCaracteres(number_format($arr_contribuicaoAss[$empregado["dados"]["id_projeto"]][$empregado["dados"]["id_clt"]], 2, '', ''));                
            }else{
                $valor_contribuicao_assistencial = null;
            }

            
            $valor_contribuicao_assistencial = sprintf("%08s", $valor_contribuicao_assistencial);
            //Valor - contribuição assistencial (com centavos)
            fwrite($arquivo, $valor_contribuicao_assistencial, 8);

            $cnpj_contribuicao_confederativa = NULL;
            $cnpj_contribuicao_confederativa = sprintf("%014s", $cnpj_contribuicao_confederativa);
            //CNPJ - contribuição confederativa
            fwrite($arquivo, $cnpj_contribuicao_confederativa, 14);

            $valor_contribuicao_confederativa = NULL;
            $valor_contribuicao_confederativa = sprintf("%08s", $valor_contribuicao_confederativa);
            //Valor - contribuição confederativa (com centavos)
            fwrite($arquivo, $valor_contribuicao_confederativa, 8);

            $empregado_cod_municipio = NULL;
            $empregado_cod_municipio = sprintf("%07s", $empregado_cod_municipio);
            //Município - local de trabalho
            fwrite($arquivo, $empregado_cod_municipio, 7);
            
            
            
            //Horas Extras   

             if(array_key_exists($empregado['dados']['id_clt'], $arr_hora_extra[$empregado['dados']['id_projeto']])){
                for($h= 1; $h<13; $h++){
                    $mes = sprintf('%02d', $h);
                    $horas_extras = $arr_hora_extra[$empregado['dados']['id_projeto']][$empregado['dados']['id_clt']][$h];
                   // echo "Incluiu {$horas_extras} no mês {$mes} para id_clt: {$empregado['dados']['id_clt']} <br>";
                   $horas_extras = sprintf("%03s", $horas_extras);  
                   fwrite($arquivo, $horas_extras, 3);                   
                    }
            }else{
                $horas_extras = NULL;
                $horas_extras = sprintf("%036s", $horas_extras);                
                fwrite($arquivo, $horas_extras, 36);
            }
            
            
            
            $empregado_indicador_filiado = '2';
            //            Indicador - empregado filiado a sindicato: 
            //                1 - Sim
            //                2 - Não
            fwrite($arquivo, $empregado_indicador_filiado, 1);

            $exclusivo_empresa2 = '';
            $exclusivo_empresa2 = sprintf("%12s", $exclusivo_empresa2);
            //Informação de uso exclusivo da empresa.
            fwrite($arquivo, $exclusivo_empresa2, 12);

            fwrite($arquivo, "\r\n");
            $this->sequencial++;

            unset($afastamento_motivo);
            unset($afastamento_motivo_final);
            unset($afastamento_inicio);
            unset($afastamento_final);
            unset($afastamento_dias);
            
        } 
//        if($_COOKIE['logado'] == 257) { exit; }
        return true;
    }

    public function montaRodape($arquivo) {
        /* Linha 4 */
        // MONTANDO O RODAPE DO ARQUIVO

        $empregado = $this->matriz;

        $sequencial4 = sprintf("%06s", $this->sequencial++);
        fwrite($arquivo, $sequencial4, 6);

        $cnpj = RemoveCaracteres($this->cnpj);
        $cnpj = substr($cnpj, 0, 14);
        $cnpj = sprintf("%014s", $cnpj);
        fwrite($arquivo, $cnpj, 14);

        $prefixo = '00';
        fwrite($arquivo, $prefixo, 2);

        $registro = '9';
        fwrite($arquivo, $registro, 1);

        $total_registros1 = '1';
        $total_registros1 = sprintf("%06s", $total_registros1);
        fwrite($arquivo, $total_registros1, 6);

        fwrite($arquivo, sprintf("%06s", $this->total_registros), 6);

        $espacos4 = NULL;
        $espacos4 = sprintf("%516s", $espacos4);
        fwrite($arquivo, $espacos4, 516);

        fwrite($arquivo, "\r\n");

        return true;
    }

    public function unsetMatriz() {
        unset($this->matriz);
    }

    public function verificaSalarioContratual($id_clt, $id_curso, $ano_contratacao) {
        $query_transf = "SELECT id_curso_de 
                            FROM rh_transferencias
                            WHERE id_curso_de != id_curso_para
                            AND id_clt = $id_clt
                            ORDER BY data_proc";
        
        $result_tranf = mysql_query($query_transf);
        $tranf = mysql_fetch_assoc($result_tranf);
        $curso = (empty($tranf['id_curso_de'])) ? $id_curso : $tranf['id_curso_de'];
        $query = "SELECT salario_antigo,salario_novo FROM rh_salario WHERE id_curso = {$curso} AND YEAR(data) >= '" . $ano_contratacao . "' AND status = 1 ORDER BY data, id_salario LIMIT 1";
//        echo $query;
        $result = mysql_query($query);
        $salario = mysql_fetch_assoc($result);
//        $salarioContratual = ($salario['salario_antigo'] <= 10) ? $salario['salario_novo'] : $salario['salario_antigo'];
        $salarioContratual = (!empty($salario['salario_novo'])) ? $salario['salario_novo'] : $salario['salario_antigo'];
        if(empty($salarioContratual)){
            $sqlCurso = "SELECT * FROM curso WHERE id_curso = {$tranf['id_curso_de']} LIMIT 1;";
            $qryCurso = mysql_query($sqlCurso);
            $rowCurso = mysql_fetch_assoc($qryCurso);
            $salarioContratual = $rowCurso['valor'];
        }
        return $salarioContratual;
    }

    public function calcDiasLicenca($data_ini, $data_retorno) {
        $time_ini = strtotime($data_ini);
        if($data_retorno > '2016-12-31' || $data_retorno == '0000-00-00'){
            $data_retorno = '2017-01-01';            
        }       
        $time_retorno = strtotime($data_retorno);
        $dias = (int) floor(($time_retorno - $time_ini) / (60 * 60 * 24)); 
        return $dias;
    }

    public function verificaValoresMes() {
        //$this->matriz[$dados['id_clt']]['salario'][$mes] = $salBaseRemunerecao;
        $empregados = $this->matriz;
        foreach ($empregados as $cpf => $empregado) {
            //rodando salarios do empregado
            $valorTotal = 0;
            foreach ($empregado['salario'] as $mes => $valor) {
                $valorTotal += intval($valor);
            }

            if ($valorTotal == 0) {
                unset($this->matriz[$cpf]);
            }
        }
    }

}
