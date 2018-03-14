<?php

class PlanoContas extends ConsultasPContas {

    public $sequencial = 1;
    public $sequenciaLC2 = 1;
    public $prosoft_regiao;
    public $prosoft_projeto;
    public $lote;
    public $modo_lancamento;
    public $empresaP;
    public $datainicio;
    public $datafim;
    public $dtainicio;
    public $dtafim;
    public $lanc;

//    public function reiniciaSequencial() {
//        unset($this->sequencial, $this->sequencialc2);
//        $this->sequencial = 1;
//        $this->sequenciaLC2 = 1;
//    }

    public function __construct($prosoft_regiao, $prosoft_projeto, $lote, $datainicio, $datafim, $modo_lancamento) {
        $this->prosoft_regiao = $prosoft_regiao;
        $this->prosoft_projeto = $prosoft_projeto;
        $this->lote = $lote;
        $this->modo_lancamento = $modo_lancamento;
        $this->datainicio = $datainicio;
        $this->datafim = $datafim;
        $this->data_dia = substr($datainicio, 7, 2);
        $this->data_mes = substr($datainicio, 5, 2);
        $this->data_ano = substr($datafim, 0, 4);
        $this->lanc = $lanc;
    }

    public function prosoftfolhaPagamento() { // modo de lançamento tipo LC2  
        // -- FOLHA NORMAL -- 
        $qry_movimentos = "SELECT A.id_folha, A.ids_movimentos_estatisticas FROM rh_folha AS A WHERE A.projeto = '{$this->prosoft_projeto}' AND A.mes = '{$this->data_mes}' AND A.ano = '{$this->data_ano}' AND A.terceiro != 1";
        
        $result = mysql_query($qry_movimentos) or die("Movimentos" . mysql_error());
        
        $arr_movimento = mysql_fetch_assoc($result);
  //      print_array($arr_movimento);
       
        $qry_fl_mov = "SELECT cod_movimento, ROUND(SUM(valor_movimento),2) AS valor
                    FROM rh_movimentos_clt
                    WHERE id_movimento IN({$arr_movimento['ids_movimentos_estatisticas']})
                    GROUP BY cod_movimento";
        
        $result = mysql_query($qry_fl_mov) or die("A " . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $listaN[$row['cod_movimento']] = $row['valor'];
            
        }
        
        $qry_fl_pgto = "SELECT
                        DATE_FORMAT(LAST_DAY(CONCAT(A.ano,'-',A.mes,'-01')),'%d%m%Y') dtEscrituracao,
                        ROUND(SUM(A.sallimpo_real),2) - ROUND(SUM(A.a5035),2) '0001',  
                        ROUND(SUM(A.a5019),2) '5019',
                        ROUND(SUM(A.a5020),2) '5020', 
                        ROUND(SUM(A.a5021),2) '5021',
                        ROUND(SUM(A.a5035),2) '5035', 
                        ROUND(SUM(A.a5036),2) '5036', 
                        ROUND(SUM(A.a5037),2) '5037', 
                        ROUND(SUM(A.a5044),2) '5044', 
                        ROUND(SUM(A.a5049),2) '5049', 
                        ROUND(SUM(A.a6005),2) '6005', 
/*                        ROUND(SUM(A.a5012),2) '5012', 
                        ROUND(SUM(A.a6007),2) '6007', 
                        ROUND(SUM(A.a9000),2) '9000', 
                        ROUND(SUM(A.a9500),2) '9500', */
                        ROUND(SUM(A.a50222),2) '50222',  
                        ROUND(SUM(A.a7001),2) '7001', 
                        ROUND(SUM(A.a5022),2) '5022', 
                        ROUND(SUM(A.a50492),2) '50492',
                        /* ROUND(B.rendi_final - (B.descon_final + B.inss_ferias),2) 'n000', */
                        ROUND(SUM(A.valor_pago_ferias),2) '80020',
                        0.00 'n000',
                        0.00 'menos', 
                        0.00 'mais',
                        0.00 'feriaC',
                        0.00 'feriaD'
                        FROM rh_folha_proc A
                        INNER JOIN rh_folha B ON (B.id_folha = A.id_folha)
                        WHERE A.id_projeto = '{$this->prosoft_projeto}' AND A.mes = '{$this->data_mes}' AND A.`ano` = '{$this->data_ano}'";
                    
        $result = mysql_query($qry_fl_pgto) or die("B " . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $listaN['dtaEscrituracao'] = $row['dtEscrituracao'];
            $listaN['0001']     += $row['0001'];
            $listaN['5012']     += $row['5012'];
            $listaN['5019']     += $row['5019'];
            $listaN['5020']     += $row['5020'];
            $listaN['5021']     += $row['5021'];
            $listaN['5022']     += $row['5022'];
            $listaN['5035']     += $row['5035'];
            $listaN['5036']     += $row['5036'];
            $listaN['5037']     += $row['5037'];
            $listaN['80020']    += $row['80020'];
            $listaN['5044']     += $row['5044'];
            $listaN['5049']     += $row['5049'];
            $listaN['6005']     += $row['6005'];
            $listaN['6007']     += $row['6007'];
            $listaN['7001']     += $row['7001'];
            $listaN['9000']     += $row['9000'];
            $listaN['9500']     += $row['9500'];
            $listaN['50492']    += $row['50492'];
            $listaN['50222']    += $row['50222'];
            $listaN['n000']     = $row['n000'];
            $listaN['menos']    = $row['menos'];
            $listaN['mais']     = $row['mais'];
            $listaN['feriaC']   = $row['feriaC'];
            $listaN['feriaD']   = $row['feriaD'];

        }

        $qry_assoc_fls_pg = "
            SELECT A.id_codigo AS codigo, B.acesso AS acesso, B.nome AS nome, A.tipo AS tipo, C.id_empresa AS empresa, A.folha AS folha
            FROM contabil_folha_prosoft A
            INNER JOIN plano_de_contas B ON (A.id_plano_de_conta = B.id_plano_contas)
            INNER JOIN empresas_prosoft_assoc C ON (C.id_projeto = A.id_projeto)
            WHERE A.id_projeto = '{$this->prosoft_projeto}' AND A.folha = 'N' OR A.folha = 'F'
            ORDER BY A.tipo";

        $result = mysql_query($qry_assoc_fls_pg) or die("C " . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            if (!empty($listaN[$row['codigo']])) {
                $xN[] = $row;
                if ($row['tipo'] === 'C' && $row['folha'] === 'N') {
                    $arr_crdN[] = $listaN[$row['codigo']];
                } else if ($row['tipo'] === 'D' && $row['folha'] === 'N') {
                    $arr_debN[] = $listaN[$row['codigo']];
                } else if ($row['tipo'] === 'C' && $row['folha'] === 'F') {
                    $arr_crdF[] = $listaN[$row['codigo']];
                } else if ($row['tipo'] === 'D' && $row['folha'] === 'F') {
                    $arr_debF[] = $listaN[$row['codigo']];
                }
            }
        } 
       
        if(array_sum($arr_crdF) != array_sum($arr_debF)){
            $saldoF = array_sum($arr_debF) - array_sum($arr_crdF)  ;
            if($saldoF < 0 ) {
                $listaN['feriaD'] = number_format(abs($saldoF),2);
            } else if ($saldoF >= 0 ){
                $listaN['feriaC'] = number_format(abs($saldoF),2);
            }
        }
        
        $listaN['n000'] = (array_sum($arr_debN)) - (array_sum($arr_crdN));

        //
//        print_array($listaN);exit;
        
        foreach ($xN as $row) {
            if (!empty($listaN[$row['codigo']] && $listaN[$row['codigo']] != 0 )) {
                $lista_assoc[$row['folha']][] = array(
                    'empresa'           => $row['empresa'],
                    'codAcesso'         => $row['acesso'],
                    'DebitoCredito'     => $row['tipo'],
                    'historico'         => $row['nome'],
                    'valor'             => $listaN[$row['codigo']],
                    'dtEscrituracao'    => $listaN['dtaEscrituracao'],
                    'folha'             => $row['folha']
                );
            
            }
        }

        // -- RESCISÃO --
     $qry_rescisao = "SELECT * FROM rh_recisao WHERE id_projeto = '{$this->prosoft_projeto}' AND status != 0 AND (data_demi BETWEEN '{$this->datainicio}' AND '{$this->datafim}')";

        $result = mysql_query($qry_rescisao) or die("CONSULTA RESCISÃO" . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $lista_id[] = $row['id_recisao'];
            $lista_clt[] = $row['id_clt'];
        }

        $id_rescisao = implode(",", $lista_id);
        $id_clt = implode (",", $lista_clt); 

             $qry_valores1 = "SELECT 
                        0.00 AS 'r000', 
                        SUM(A.saldo_salario) AS 'r001', 
                        SUM(A.ferias_pr) AS 'r002', 
                        SUM(A.a480) AS 'r480',
                        SUM(A.a479) AS 'r479',
                        SUM(A.a477) AS 'r477',
                        SUM(A.ferias_vencidas) AS 'r003', 
                        SUM(A.umterco_fv) + SUM(A.umterco_fp) AS 'r004',
                        SUM(A.lei_12_506) AS '12506',
                        SUM(A.dt_salario) AS 'r005',
                        /* SUM(A.terceiro_ss) + SUM(ferias_aviso_indenizado) + SUM(umterco_ferias_aviso_indenizado)  AS 'r006', */
                        SUM(ferias_aviso_indenizado) AS 'r006',
                        SUM(if(motivo = 61, A.aviso_valor,0)) AS 'rc07',
                        SUM(A.insalubridade) AS 'r008',
                        SUM(A.arredondamento_positivo) AS 'r009', 
                        SUM(A.a480) AS 'r010',
                        SUM(A.previdencia_ss) AS 'r011', 
                        SUM(A.ir_ss) AS 'r012',
                        SUM(if(motivo = 65, A.aviso_valor,0)) AS 'rd07',
                        SUM(A.inss_dt) AS 'r013', 
                        SUM(A.ir_dt) AS 'r014'
                        FROM rh_recisao AS A
                        WHERE A.`status` != 0 AND A.id_projeto = '{$this->prosoft_projeto}' AND (data_demi BETWEEN '{$this->datainicio}' AND '{$this->datafim}')";

        $result = mysql_query($qry_valores1) or die("B " . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $listaR['dtaEscrituracao'] = $row['dtEscrituracao'];
            $listaR['r001'] = $row['r001'];
            $listaR['r002'] = $row['r002'];
            $listaR['r003'] = $row['r003'];
            $listaR['r004'] = $row['r004'];
            $listaR['r005'] = $row['r005'];
            $listaR['r006'] = $row['r006'];
            $listaR['rc07'] = $row['rc07'];
            $listaR['12506'] = $row['12506'];
            $listaR['r008'] = $row['r008'];
            $listaR['r480'] = $row['r480'];
            $listaR['r479'] = $row['r479'];
            $listaR['r477'] = $row['r477'];
            $listaR['r009'] = $row['r009'];
            $listaR['r010'] = $row['r010'];
            $listaR['r011'] = $row['r011'];
            $listaR['r012'] = $row['r012'];
            $listaR['rd07'] = $row['rd07'];
            $listaR['r013'] = $row['r013'];
            $listaR['r014'] = $row['r014'];
            $listaR['r000'] = $row['r000'];            
        }
        
        if (!empty($id_rescisao)) {
            $qry_valores2 = "SELECT id_mov, SUM(valor) AS valor FROM rh_movimentos_rescisao WHERE `status` != 0 AND valor > 0 AND id_rescisao IN($id_rescisao) GROUP BY id_mov";

            $result = mysql_query($qry_valores2) or die("RESCISÃO SEM  " . mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                $listaR[$row['id_mov']] = $row['valor'];
            }
        }

        if (!empty($id_clt)) {
            $qry_clt = "SELECT cod_movimento, SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE mes_mov = 16 AND id_clt IN({$id_clt}) GROUP BY cod_movimento";

            $result = mysql_query($qry_clt) or die("RESCISÃO SEM  " . mysql_error());

            while ($row = mysql_fetch_assoc($result)) {
                $listaR[$row['cod_movimento']] = $row['valor'];
            }
        }
        
          $qry_assoc_fl_pgto = "SELECT A.id_codigo AS codigo, B.acesso AS acesso, B.nome AS nome, A.tipo AS tipo, C.id_empresa AS empresa, A.folha AS folha, D.descricao AS historico
                            FROM contabil_folha_prosoft A
                            INNER JOIN plano_de_contas B ON (A.id_plano_de_conta = B.id_plano_contas)
                            INNER JOIN empresas_prosoft_assoc C ON (C.id_projeto =  '{$this->prosoft_projeto}')
                            LEFT JOIN contabil_historico_prosoft D ON (D.codigo = A.historico)
                            WHERE A.id_projeto = '{$this->prosoft_projeto}' AND A.folha = 'R'
                            ORDER BY A.tipo";

        $result = mysql_query($qry_assoc_fl_pgto) or die("E" . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            if (!empty($listaR[$row['codigo']])) {
                $xR[] = $row;
                if ($row['tipo'] == 'C' && $row['folha'] == 'R') {
                    $arr_crd[] = $listaR[$row['codigo']];
                } else if ($row['tipo'] == 'D' && $row['folha'] == 'R') {
                    $arr_deb[] = $listaR[$row['codigo']];
                }
            }
        }

        $listaR['r000'] = (array_sum($arr_deb)) - (array_sum($arr_crd));
        foreach ($xR as $row) {
            $hist_padrao = $row['historico']; 
            if (!empty($listaR[$row['codigo']] && $listaR[$row['codigo']] != 0 )) {
                if(empty($hist_padrao)) {
                    $hist_padrao = $row['nome'];
                    } else {
                    $hist_padrao;
                }
                $lista_assoc[$row['folha']][] = array(
                    'empresa'           => $row['empresa'],
                    'codAcesso'         => $row['acesso'],
                    'DebitoCredito'     => $row['tipo'],
                    'historico'         => $hist_padrao,
                    'valor'             => $listaR[$row['codigo']],
                    'dtEscrituracao'    => $listaN['dtaEscrituracao'],
                    'folha'             => $row['folha']
                );
            
            }
        }
        
//        if($_COOKIE['logado'] == 259){
//            print_array($lista_assoc);
//        }
        return $lista_assoc;
    }

    public function registroLC1() {

        // -- DESPESAS E RECEITAS --
        $sql_lc1 = "SELECT DATE_FORMAT(A.data_vencimento, '%d%m%Y') AS dtEscrituracao, H.acesso AS cCredito, F.acesso AS cDebito, D.id_prestador_prosoft AS cdTerceiro, C.c_razao AS razao,
                    TRIM(A.nome) AS Historico1, CONCAT(C.c_razao,'. - ',TRIM(A.especifica)) AS historico, REPLACE(A.valor, '.', ',') AS vlrLancamento, A.n_documento AS nrDocumento, I.id_empresa AS empresa
                    FROM saida AS A
                    LEFT JOIN entradaesaida_plano_contas_assoc AS B ON (B.id_entradasaida = A.tipo AND B.id_projeto = A.id_projeto)
                    LEFT JOIN prestadorservico AS C ON(C.id_prestador = A.id_prestador) 
                    LEFT JOIN prestadorServ_prestadorPro_assoc AS D ON(D.id_prestador = C.id_prestador)
                    LEFT JOIN entradaesaida AS E ON(E.id_entradasaida = B.id_entradasaida)
                    LEFT JOIN plano_de_contas AS F ON(F.id_plano_contas = B.id_plano_contas)
                    LEFT JOIN prosoft_bancos_assoc AS G ON(G.id_banco = A.id_banco)
                    LEFT JOIN plano_de_contas AS H ON(H.id_plano_contas = G.id_plano_contas)
                    INNER JOIN empresas_prosoft_assoc I ON (I.id_projeto = A.id_projeto)
                    WHERE A.valor > '0,00' AND A.`status` = 2 AND (A.data_vencimento BETWEEN '{$this->datainicio}' AND '{$this->datafim}') AND A.id_projeto = '{$this->prosoft_projeto}'
                UNION ALL
                    SELECT DATE_FORMAT(A.data_vencimento, '%d%m%Y') AS dtEscrituracao, D.acesso AS cCredito, G.acesso AS cDebito, '' AS cdTerceiro, '' AS razao,
                    TRIM(A.especifica) AS historico, TRIM(A.nome) AS historico1, REPLACE(A.valor, '.', ',') AS vlrLancamento, '' AS nrDocumento, I.id_empresa AS empresa
                    FROM entrada AS A
                    LEFT JOIN entradaesaida AS B ON (B.id_entradasaida = A.tipo)
                    LEFT JOIN entradaesaida_plano_contas_assoc AS C ON (C.id_entradasaida = B.id_entradasaida AND C.id_projeto = A.id_projeto)
                    LEFT JOIN plano_de_contas AS D ON (D.id_plano_contas = C.id_plano_contas)
                    LEFT JOIN projeto AS E ON (E.id_projeto = A.id_projeto)
                    LEFT JOIN prosoft_bancos_assoc AS F ON(F.id_banco = A.id_banco)
                    LEFT JOIN plano_de_contas AS G ON(G.id_plano_contas = F.id_plano_contas)
                    INNER JOIN empresas_prosoft_assoc I ON (I.id_projeto = A.id_projeto)
                    WHERE A.valor > '0,00' AND A.`status` = 2 AND (A.data_vencimento BETWEEN '{$this->datainicio}' AND '{$this->datafim}') AND A.id_projeto = '{$this->prosoft_projeto}'
                    ORDER BY  dtEscrituracao ASC";

        $result = mysql_query($sql_lc1) or die('Erro registroLC1');

        while ($rowConsulta = mysql_fetch_assoc($result)) {
            $return[] = $rowConsulta;
        }
        return $return;
    }

    public function montaCTA($arquivo, $pc) {

        $tipo = sprintf('%-03s', 'CTA');
        fwrite($arquivo, $tipo, 3);

        $ordem = sprintf('%5s', $this->sequencial);
        fwrite($arquivo, $ordem, 5);

        $filler = NULL;
        $filler = sprintf("%03s", $filler);
        fwrite($arquivo, $filler, 3);

        $codAcesso = sprintf('%-05s', RemoveCaracteresGeral($pc['acesso']));
        fwrite($arquivo, $codAcesso, 5);

        $classificador = sprintf("%-15s", RemoveCaracteresGeral($pc['classificador']));
        fwrite($arquivo, $classificador, 15);

        $nomeConta = sprintf("%-30s", RemoveCaracteresGeral($pc['nome']));
        fwrite($arquivo, $nomeConta, 30);

        $terceiros = sprintf('%1s', '1'); // 1-Não faz ref.; 2-Faz ref. opcional; 3-Faz ref. obrigatória; ????????
        fwrite($arquivo, $terceiros, 1);

        $cCustos = sprintf('%1s', '0'); // 0-Não; 1-Sim, com apropriação opcional; 2-Sim, com rateio; 3-Sim, com apropriação obrigatória; ??????
        fwrite($arquivo, $cCustos, 1);

//        $classificadorPE = sprintf('%20s', RemoveCaracteresGeral($pc['classificador']));  
//        fwrite($arquivo, $classificadorPE, 20);
//
//        $nomeContaPE = sprintf('%-80s', RemoveCaracteresGeral($pc['nome'])); 
//        fwrite($arquivo, $nomeContaPE, 80);

        $objConciliacao = sprintf('%1s', '0'); // 0-Não; 1-Sim; ?????
        fwrite($arquivo, $objConciliacao, 1);

        $filler = sprintf("%44s", $filler);
        fwrite($arquivo, $filler, 44);

        $this->sequencial++;
        fwrite($arquivo, "\r\n");
    }

    public function montaTRC($arquivo, $pc) {

        $tipo = sprintf('%-03s', 'TRC');
        fwrite($arquivo, $tipo, 3);

        $ordem = sprintf('%-05s', $this->sequencial);
        fwrite($arquivo, $ordem, 5);

        $filler = NULL;
        $filler = sprintf("%-03s", $filler);
        fwrite($arquivo, $filler, 3);

        $tpTerceiro = sprintf('%1s', 'J');  // J-Jurídica; F-Física; L-Livre; B-Banco; ????
        fwrite($arquivo, $tpTerceiro, 1);

        $cnpj = sprintf("%14", RemoveCaracteresGeral($pc['cnpj']));
        fwrite($arquivo, $cnpj, 14);

        $nome = sprintf("%-60s", RemoveCaracteresGeral($pc['nome']));
        fwrite($arquivo, $nome, 60);

        $apelido = sprintf('%-20s', RemoveCaracteresGeral($pc['apelido']));
        fwrite($arquivo, $apelido, 20);

        $tpLogradouro = sprintf('%-10s', $pc['descricao_tp_logradouro']);
        fwrite($arquivo, $tpLogradouro, 10);

        $nomeLogradouro = sprintf('%-10s', $pc['c_enndereco']);
        fwrite($arquivo, $nomeLogradouro, 10);

        $numero = sprintf('%-10s', $pc['c_numero']);
        fwrite($arquivo, $numero, 10);

        $complemento = sprintf('%-20s', $pc['c_complemento']);
        fwrite($arquivo, $complemento, 20);

        $cep = sprintf('%-9s', $pc['c_cep']);
        fwrite($arquivo, $cep, 9);

        $bairro = sprintf('%-30s', $pc['c_bairro']);
        fwrite($arquivo, $bairro, 30);

        $municipio = sprintf('%-30s', $pc['municipio']);
        fwrite($arquivo, $municipio, 30);

        $uf = sprintf('%-2s', $pc['c_uf']);
        fwrite($arquivo, $uf, 2);

        $data = sprintf('%-8s', $pc['contratado_em']);
        fwrite($arquivo, $data, 8);

        $telDDD = sprintf('%-5s', substr(RemoveCaracteresGeral($pc['c_tel']), 0, 2));
        fwrite($arquivo, $telDDD, 5);

        $telNr = sprintf('%-10s', substr(RemoveCaracteresGeral($pc['c_tel']), 2));
        fwrite($arquivo, $telNr, 10);

        $faxDDD = sprintf('%-5s', substr(RemoveCaracteresGeral($pc['c_fax']), 0, 2));
        fwrite($arquivo, $faxDDD, 5);

        $faxNr = sprintf('%-10s', substr(RemoveCaracteresGeral($pc['c_fax']), 2));
        fwrite($arquivo, $faxNr, 10);

        $email = sprintf('%-50s', $pc['c_email']);
        fwrite($arquivo, $email, 50);

//        $homePage = sprintf('%-60s', $pc['homepage']);
//        fwrite($arquivo, $homePage);

        if ($tpTerceiro == 'J') {
            // Dados PJ
            $inscricaoEstadual = sprintf('%-20s', $pc['c_ie']);
            fwrite($arquivo, $inscricaoEstadual, 20);

            $inscricaoMunicipal = sprintf('%-20s', $pc['c_im']);
            fwrite($arquivo, $inscricaoMunicipal, 20);

//            $codAtivEconomica = sprintf('%-10s', $pc['codAtivEconomica']);
//            fwrite($arquivo, $codAtivEconomica,10);
        } else {
            // Dados PF
            $nrRG = sprintf('%-18s', $pc['rg']);
            fwrite($arquivo, $nrRG, 18);

//            $orgaoEmissor = sprintf('%-5s', $pc['emissor']);
//            fwrite($arquivo, $orgaoEmissor,5);
//
//            $codAtivEconomica = sprintf('%-10s', $pc['codAtivEconomica']);
//            fwrite($arquivo, $codAtivEconomica,10);
//
//            $dataEmissao = sprintf('%-10s', $pc['dataEmissao']);
//            fwrite($arquivo, $dataEmissao,10);
//
//            $sexo = sprintf('%1s', $pc['sexo']); // 0-Masc.; 1-Fem.;
//            fwrite($arquivo, $sexo,1);
//
//            $estadoCivil = sprintf('%1s', $pc['sexo']); // 0-Solteiro; 1-Casado; 2-Amasiado; 3-Desquitado, 4-Divorciado, 5-Viúvo
//            fwrite($arquivo, $estadoCivil,1);
        }

        $filler = sprintf("%92s", $filler);
        fwrite($arquivo, $filler, 92);

        $this->sequencial++;
        fwrite($arquivo, "\r\n");
    }

    public function montaLC1($arquivo, $pc) {

        $tipo = sprintf('%3s', 'LC1');
        fwrite($arquivo, $tipo, 3);

        $ordem = sprintf('%05s', $this->sequencial);
        fwrite($arquivo, $ordem, 5);

        $filler = null;
        $filler = sprintf('%3s', $filler);
        fwrite($arquivo, $filler, 3);

        $modLancamento = $this->modo_lancamento;
        $modLancamento = sprintf('%1s', $modLancamento);  // 1-Simples; 2-Detalhado; ????
        fwrite($arquivo, $modLancamento, 1);

        $dtEscrituracao = sprintf($pc['dtEscrituracao']);
        fwrite($arquivo, $dtEscrituracao, 8);

        $nrDoc = sprintf('%-10s', $pc['nrDocumento']);
        fwrite($arquivo, $nrDoc, 10);

        $nrLote = NULL;
        $nrLote = sprintf('%05s', $this->lote);
        fwrite($arquivo, $nrLote, 5);

        $origemLancamento = NULL; // $pc['origemLancamento']
        $origemLancamento = sprintf('%-30s', $origemLancamento);
        fwrite($arquivo, $origemLancamento, 30);

        $qdtContas = NULL; //
        $qdtContas = sprintf('%3s', $qdtContas);
        fwrite($arquivo, $qdtContas, 3);

        $cdCodAcesso = sprintf('%5s', RemoveCaracteres($pc['cDebito']));
        fwrite($arquivo, $cdCodAcesso, 5);

        if (empty($pc['cdTerceiro'])) {
            $cdTerceiro = NULL;
            $cdTerceiro = sprintf('%14s', $cdTerceiro);
        } else {
            $cdTerceiro = $pc['cdTerceiro'];
            $cdTerceiro = sprintf('%06s', $cdTerceiro);
            $cdTerceiro = $cdTerceiro . sprintf('%-8s', " ");
        }
        fwrite($arquivo, $cdTerceiro, 14);

        $cdCcusto = " ";
        $cdCcusto = sprintf('%5s', $cdCcusto);
        fwrite($arquivo, $cdCcusto, 5);

        $cCredito = sprintf('%5s', RemoveCaracteres($pc['cCredito']));
        fwrite($arquivo, $cCredito, 5);

        $ccTerceiro = " ";
        $ccTerceiro = sprintf('%14s', $ccTerceiro);
        fwrite($arquivo, $ccTerceiro, 14);

        $crCcusto = " ";
        $crCcusto = sprintf('%5s', $crCcusto);
        fwrite($arquivo, $crCcusto, 5);

        $valorLancamento = sprintf(STR_PAD(str_replace(",", ".", $pc['vlrLancamento']), 16, 0, STR_PAD_LEFT));
        fwrite($arquivo, $valorLancamento, 16);

        $historico = $pc['historico'];
        if ($pc['historico'] == "") {
            $historico = $pc['Historico1'];
        }
        $historico = sprintf('%-240s', $historico);
        fwrite($arquivo, $historico, 240);

        $icDebito = sprintf('%-1s', " "); // " " - Não conciliado; C - Conciliado; P - Pendente;
        fwrite($arquivo, $icDebito, 1);

        $icCredito = sprintf('%-1s', " "); // " " - Não conciliado; C - Conciliado; P - Pendente;
        fwrite($arquivo, $icCredito, 1);

        $filler1 = null;
        $filler1 = sprintf('%74s', " ");
        fwrite($arquivo, $filler1, 74);

        $this->sequencial++;
        fwrite($arquivo, "\r\n");
    }

    public function montaPFP($arquivo, $pc) {

        $x = array('F', 'N', 'R');

        foreach ($pc as $x => $dado) {

            $tipo = sprintf('%3s', 'LC1');
            fwrite($arquivo, $tipo, 3);

            $ordemP = sprintf('%05s', $this->sequencial);
            fwrite($arquivo, $ordemP, 5);

            $filler = null;
            $filler = sprintf('%3s', $filler);
            fwrite($arquivo, $filler, 3);

            $modLancamento = 2;
            $modLancamento = sprintf('%1s', $modLancamento);  // 2-Detalhado; 
            fwrite($arquivo, $modLancamento, 1);

            $dtEscrituracao = sprintf($dado[0]['dtEscrituracao']);
            fwrite($arquivo, $dtEscrituracao, 8);

            $nrDoc = sprintf('%-10s', ' ');
            fwrite($arquivo, $nrDoc, 10);

            $nrLote = NULL;
            $nrLote = sprintf('%05s', $this->lote);
            fwrite($arquivo, $nrLote, 5);

            $origemLancamento = NULL; // $pc['origemLancamento']
            $origemLancamento = sprintf('%-30s', $origemLancamento);
            fwrite($arquivo, $origemLancamento, 30);

            $qdtContas = count($dado); //
            $qdtContas = sprintf('%3s', $qdtContas);
            fwrite($arquivo, $qdtContas, 3);

            fwrite($arquivo, "\r\n");

            foreach ($dado as $linha) {

                $tipo = sprintf('%-03s', 'LC2');
                fwrite($arquivo, $tipo, 3);

                $ordem = sprintf('%05s', $ordemP);
                fwrite($arquivo, $ordem, 5);

                $partidaNr = sprintf("%03s", $this->sequenciaLC2);
                fwrite($arquivo, $partidaNr, 3);

                $DebitoCredito = sprintf('%1s', $linha['DebitoCredito']);
                fwrite($arquivo, $DebitoCredito, 1);

                $codAcesso = sprintf('%5s', $linha['codAcesso']);
                fwrite($arquivo, $codAcesso, 5);

                if (count($pc['codTerceiro']) == 6) {
                    $codTerceiro = sprintf('%-14s', $linha['codTerceiro'] . 'L');
                } else {
                    $codTerceiro = sprintf('%-14s', $linha['codTerceiro']);
                }

                fwrite($arquivo, $codTerceiro, 14);

                $codCusto = sprintf('%5s', $dado['codCusto']);
                fwrite($arquivo, $codCusto, 5);

                $valor = sprintf('%16s', number_format($linha['valor'],2,'.',''));
                fwrite($arquivo, $valor, 16);

                $historico = sprintf('%-240s', $linha['historico']);
                fwrite($arquivo, $historico, 240);

                $indConciliacao = sprintf('%-1s', " "); // " " - Não conciliado; C - Conciliado; P - Pendente;
                fwrite($arquivo, $indConciliacao, 1);

                $filler = sprintf("%49s", NULL);
                fwrite($arquivo, $filler, 74);

                $this->sequenciaLC2++;
                fwrite($arquivo, "\r\n");
            }
            $this->sequencial++;
            $this->sequenciaLC2 = 1;
        }
    }

    public function montaLC2($arquivo, $pc) {

        $tipo = sprintf('%-03s', 'LC2');
        fwrite($arquivo, $tipo, 3);

        $ordem = sprintf('%05s', $this->sequencial);
        fwrite($arquivo, $ordem, 5);

//        $ordem = sprintf('%-05s', $ordemLC1);
//        fwrite($arquivo, $ordem, 5);

        $partidaNr = sprintf("%03s", $this->sequencial);
        fwrite($arquivo, $partidaNr, 3);

        $DebitoCredito = sprintf('%1s', $pc['DebitoCredito']);
        fwrite($arquivo, $DebitoCredito, 1);

        $codAcesso = sprintf('%5s', $pc['codAcesso']);
        fwrite($arquivo, $codAcesso, 5);

        if (count($pc['codTerceiro']) == 6) {
            $codTerceiro = sprintf('%-14s', $pc['codTerceiro'] . 'L');
        } else {
            $codTerceiro = sprintf('%-14s', $pc['codTerceiro']);
        }
        fwrite($arquivo, $codTerceiro, 14);

        $codCusto = sprintf('%5s', $pc['codCusto']);
        fwrite($arquivo, $codCusto, 5);

        $valor = sprintf('%16s', $pc['valor']);
        fwrite($arquivo, $valor, 16);

        $historico = sprintf('%-240s', $pc['historico']);
        fwrite($arquivo, $historico, 240);

        $indConciliacao = sprintf('%-1s', " "); // " " - Não conciliado; C - Conciliado; P - Pendente;
        fwrite($arquivo, $indConciliacao, 1);

        $filler = sprintf("%49s", NULL);
        fwrite($arquivo, $filler, 74);

        $this->sequencial++;
        fwrite($arquivo, "\r\n");
    }

    public function montaSLD($arquivo, $ordemLC1, $pc) {

        $tipo = sprintf('%-03s', 'SLD');
        fwrite($arquivo, $tipo, 3);

        $ordem = sprintf('%-05s', $ordemLC1);
        fwrite($arquivo, $ordem, 5);

        $filler = NULL;
        $filler = sprintf("%3s", $filler);
        fwrite($arquivo, $filler, 3);

        $codAcesso = sprintf('%5s', $pc['codAcesso']);
        fwrite($arquivo, $codAcesso, 5);

        if (count($pc['codTerceiro']) == 6) {
            $codTerceiro = sprintf('%-14s', $pc['codTerceiro'] . 'L');
        } else {
            $codTerceiro = sprintf('%-14s', $pc['codTerceiro']);
        }
        fwrite($arquivo, $codTerceiro, 14);

        $codCusto = sprintf('%5s', $pc['codCusto']);
        fwrite($arquivo, $codCusto, 5);

        $vSaldoIni = sprintf('%-16s', $pc['valor']);
        fwrite($arquivo, $vSaldoIni, 16);

        $indSaldoIni = sprintf('%-1s', "D"); // D - Débito; C - Credito;
        fwrite($arquivo, $indSaldoIni, 1);

        $filler = sprintf("%49s", NULL);
        fwrite($arquivo, $filler, 74);

        foreach ($array as $key => $value) {
            $movimento = sprintf("%16", $value);
            fwrite($arquivo, $movimento, 16);
        }

        fwrite($arquivo, "\r\n");
    }

}
