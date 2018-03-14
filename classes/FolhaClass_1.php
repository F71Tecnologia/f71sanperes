<?php

/**
 * Description of InformeRendimentoClass
 * 
 * 
 * 
 * provisao_de_gastos.php
 * 
 * 00-00-0000
 * 
 * Versão: 3.0.0001 - 08/09/2015 - Jacques - Retirado as aspas simples do método getMultaFgts na passagem de parámetro do id_clt para tentar aferir erro na totalização de $total_multa_a_pagar
 *
 * @author Sinésio
 * 
 * STATUS DA NOVA RH_FOLHA_PROC 
 * 2 => ANDAMENTO 
 * 3 => FINALIZADA
 */ 

include_once("CalculoFolhaClass.php"); 
class Folha extends Calculo_Folha{

    private $dados;
    private $historico;
    private $cabecalho;
    private $sqlInjection;
    private $decimo_terceiro;
    private $id_pagina = 60;
    private $folha;
    private $participantesParaAtualizar;
    private $mes;
    private $ano;
    private $inicioFolha;
    private $finalFolha;
    private $arrayLicencas;
    private $arrayRescisao;
    
    /**
     * 
     * @param type $sqlInjection
     */
    public function __construct($sqlInjection = null){
        
        if(is_object($sqlInjection) && !empty($sqlInjection)){
            $this->sqlInjection = $sqlInjection;
        }
        
        /**
        * CARREGANDO TODOS OS 
        * TIPOS DE RESCISÃO
        */ 
        $this->setArrayRescisao($this->getStatusRescisao());
            
        /**
         * 
         */
        $this->setArrayLicenca($this->getStatusLicenca());
        
    }
    
    /**
     * METODO PARA TRAZER DADOS DA EMPRESA
     * 
     */
    public function getMaster($regiao) {
        $query = "SELECT * FROM regioes AS A LEFT JOIN master AS B ON(A.id_master = B.id_master) WHERE A.id_regiao = '{$regiao}'";
        $master = mysql_query($query) or die("Erro ao selecionar Master");
        return $master;
    }

    /**
     * MÉTODO PARA GERA FICHA FINANCEIRA
     */
    public function getDadosClt($mes = null, $ano = null, $projeto = null) {
        $sql = mysql_query("SELECT A.id_clt, A.nome,CONCAT(A.endereco,A.numero,', ',A.bairro,', ',A.cidade,' - ',A.uf) as endereco,
            DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
            DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada,
            DATE_FORMAT(A.data_demi,'%d/%m/%Y') as data_demi,
            A.nacionalidade,A.naturalidade,A.tipo_conta,
            A.agencia, A.conta,C.nome as nome_banco, C.razao,
            A.matricula, IF(D.unidade_de != '', D.unidade_de, E.unidade) AS locacao,
            A.cpf,A.rg,A.titulo,A.campo1 as ctps, A.serie_ctps, A.pis,
            B.nome as nome_curso, FORMAT(B.salario,2) as salario
            FROM rh_clt as A 
            LEFT JOIN curso as B ON (B.id_curso = A.id_curso)
            LEFT JOIN bancos as C ON (A.banco = C.id_banco)
            LEFT JOIN (SELECT id_clt, unidade_para, unidade_de FROM rh_transferencias WHERE LAST_DAY(data_proc) > LAST_DAY('$ano-$mes-01') ORDER BY id_transferencia DESC LIMIT 1) as D ON (D.id_clt = A.id_clt)
            LEFT JOIN unidade AS E ON (E.id_unidade = A.id_unidade) 
            WHERE A.id_projeto = {$projeto}");

        return $sql;
    }
    
    public function getDadosClt2($mes = null, $ano = null, $projeto = null) {
        $sql = mysql_query("SELECT A.id_clt, A.nome,CONCAT(A.endereco,A.numero,', ',A.bairro,', ',A.cidade,' - ',A.uf) as endereco,
            DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
            DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada,
            DATE_FORMAT(A.data_demi,'%d/%m/%Y') as data_demi,
            A.nacionalidade,A.naturalidade,A.tipo_conta,
            A.agencia, A.conta,C.nome as nome_banco, C.razao,
            A.matricula, IF(D.unidade_de != '', D.unidade_de, E.unidade) AS locacao,
            A.cpf,A.rg,A.titulo,A.campo1 as ctps, A.serie_ctps, A.pis,
            B.nome as nome_curso, FORMAT(B.salario,2) as salario
            FROM rh_clt as A 
            LEFT JOIN curso as B ON (B.id_curso = A.id_curso)
            LEFT JOIN bancos as C ON (A.banco = C.id_banco)
            LEFT JOIN (SELECT id_clt, unidade_para, unidade_de FROM rh_transferencias WHERE LAST_DAY(data_proc) > LAST_DAY('$ano-$mes-01') ORDER BY id_transferencia DESC LIMIT 1) as D ON (D.id_clt = A.id_clt)
            LEFT JOIN unidade AS E ON (E.id_unidade = A.id_unidade) 
            WHERE A.id_projeto = {$projeto} AND ((YEAR(A.data_demi) = 2017) OR (A.data_demi IS NULL) OR (A.data_demi = '') OR (A.data_demi = '0000-00-00'))");

        return $sql;
    }

    /**
     * MÉTODO QUE RETORNA DADOS DO CLT EM UM ARRAY
     * @param type $clt
     * @param type $ano
     * @param type $meses
     */
    public function getFichaFinanceira($clt, $ano, $meses = null, $terceiro = null, $arrayPeriodo = array(), $websaass = 0) {
        
//        if($_COOKIE['logado'] == 179){
//            print_r($clt . " - " . $ano . " - " . $meses . " - " . $terceiro . " - " . $arrayPeriodo . " - " . $websaass);
//        }
        
        //MONTA CABEÇALHO
        $this->getCabecalho();
        $criteria = "";
        unset($this->dados);
        
        if(!empty($terceiro)){
            $criteria .= "B.terceiro = " . $terceiro . " AND "; 
        }
        
        if(count($arrayPeriodo) > 0){
            $criteria .= "A.id_clt = '{$clt}' AND DATE_FORMAT(A.data_proc,'%Y-%m') IN ('{$ano}-{$meses}')";
        }else{
        
            if (empty($meses)) {
                $camposFerias = "D.dias_ferias, D.total_liquido AS valor_ferias,
                D.ir AS ir_ferias, D.inss AS inss_ferias, A.valor_pago_ferias,
                D.dias_ferias, D.total_remuneracoes, D.total_liquido AS valor_ferias, D.pensao_alimenticia,
                D.ir AS ir_ferias, D.inss AS inss_ferias,";
                $criteria .= "A.id_clt = '{$clt}' AND A.ano = '{$ano}'";
                $ferias = "LEFT JOIN rh_ferias AS D ON(A.id_clt = D.id_clt AND A.ano = D.ano AND A.mes = LPAD(D.mes,2,0) AND D.status = 1)";
            } else {
                $camposFerias = "D.dias_ferias, D.total_liquido AS valor_ferias,
                D.ir AS ir_ferias, D.inss AS inss_ferias, A.valor_pago_ferias,
                D.dias_ferias, D.total_remuneracoes, D.total_liquido AS valor_ferias,D.pensao_alimenticia,
                D.ir AS ir_ferias, D.inss AS inss_ferias,";
                $criteria .= "A.id_clt = '{$clt}' AND A.ano = '{$ano}' AND A.mes = '{$meses}'";
                $ferias = "LEFT JOIN rh_ferias AS D ON(A.id_clt = D.id_clt AND LPAD(D.mes,2,0) = '{$meses}' AND D.ano = '{$ano}' AND D.status = 1)";
                $this->zeraArray();
            }
        }        
        
        $sqls = "SELECT A.id_folha, C.id_projeto, B.terceiro, A.id_clt, A.mes, A.ano, C.nome,
                 /*B.ids_movimentos_estatisticas, */
                IF(((MONTH(C.data_entrada) = A.mes AND YEAR(C.data_entrada) = A.ano) OR (A.a6005 != 0.00) OR (C.status > 60)) && (A.sallimpo_real = 0.00 OR A.sallimpo_real IS NULL), A.sallimpo, A.sallimpo_real) AS salario_base, A.sallimpo_real,
                A.salliquido,
                A.sallimpo,
                A.dias_trab AS dias_trabalhadas,
                A.quantidade_hora_medico_especialista,
                A.valor_fixo_plantao, A.quantidade_plantao,
                A.a6005 AS salario_maternidade,
                A.a5012 AS diferenca_salarial, A.a5029 AS decimo_terceiro, A.inss_dt, A.ir_dt, A.inss, A.t_inss, A.imprenda, A.ir_ferias, A.ir_rescisao, A.a5019 AS contribuicao_sindical, 
                A.a7001 AS vale_transporte, 
                A.a8080 AS hora_extra,
                A.a5049 AS DDIR,
                A.a5035,
                A.a5037 AS desconto_ferias,
                A.a9500 AS desconto, A.a5030 AS ir_sobre_decimo, A.a5031 AS inss_sobre_decimo,
                A.salfamilia AS salario_familia,
                B.terceiro, A.rend, A.status_clt, A.valor_rescisao, A.valor_pago_rescisao, E.total_deducao,
                A.inss_rescisao, A.a5037 AS ferias, 
                {$camposFerias} A.valor_pago_ferias,
                A.t_imprenda, if(A.status_clt = 70,A.sallimpo * 0.08,A.base_inss * 0.08) AS fgts, 
                (if(A.valor_pis != 0.00,A.valor_pis,(A.base_inss) * 0.01)) AS valor_pis, A.base_inss as base_pis,
                 B.ids_movimentos_estatisticas AS ids_movimentos_estatisticas,
                B.ids_movimentos_estatisticas AS ids_movimentos_folha,
                E.inss_ss AS inss_saldo_rescisao
                FROM rh_folha_proc AS A
                LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha)
                LEFT JOIN rh_clt   AS C ON(A.id_clt = C.id_clt)
                LEFT JOIN rh_recisao AS E ON(A.id_clt = E.id_clt AND A.mes = date_format(E.data_demi,'%m') AND E.`status` = 1)
                {$ferias}
                WHERE {$criteria} AND A.status = 3 
                ORDER BY A.mes";
        
           
                
        $sql = mysql_query($sqls) or die("Erro ao selecionar dados do clt");

        $count = 1;
        while ($rows = mysql_fetch_assoc($sql)) {
            
//            if($_COOKIE['logado'] == 179){
//                echo "<pre>";
//                    print_r($rows);
//                echo "</pre>";
//            }

            
            
            $mes = sprintf("%02d", $rows['mes']);

            if ($rows['terceiro'] == 2) {
                if (!empty($rows['salario_base'])) {

                    if($rows['quantidade_hora_medico_especialista'] > 0) {
                        $this->dados["0001"]['ref'] = $rows['quantidade_hora_medico_especialista'] . ' horas';
                        $this->dados["0001"]['nome'] = "SALARIO HORISTA";
                    } elseif($rows['quantidade_plantao'] > 0) {
                        $this->dados["0001"]['ref'] = "{$rows['valor_fixo_plantao']} X ".number_format($rows['quantidade_plantao'],0,'','')." P";
                        $this->dados["0001"]['nome'] = "SALARIO PLANTONISTA";
                    } else {
                        $this->dados["0001"]['ref'] = $rows['dias_trabalhadas'];
                        $this->dados["0001"]['nome'] = "SALARIO";
                    }

                    if ($rows['salario_base'] != 0.00 ) {
                        if ($rows['status_clt'] > 60 && $rows['status_clt'] != 80 && $rows['status_clt'] != 70 && $rows['status_clt'] != 69 && $rows['status_clt'] != 68 && $rows['status_clt'] != 67) {
                            $this->dados["0001"][$mes] = $rows['salario_base'] . " (Rescisão)";
                        } else {
                            $this->dados["0001"][$mes] = $rows['sallimpo_real'];
                        }
                    } else {
                        $this->dados["0001"][$mes] = 0.00;
                    }
                }
                $this->historico[$clt][$ano][$mes]["0001"] = $this->dados["0001"][$mes];
            }
            
            /**
             * PARA O WEBSAASS 
             * O QUE TEM QUE VIR É O SALARIO LIQUIDO 
             * 
             */
            if ($websaass) {
                $this->dados["0001L"]['nome'] = "SALARIO";
                $this->dados["0001L"]['ref']  = $rows['dias_trabalhadas'];
                $this->dados["0001L"][$mes]   = $rows['salliquido'];
            }
            //FÉRIAS
//            if (!empty($rows['ferias']) && $rows['ferias'] != 0.00) {
//                $this->dados["5037"]['nome'] = "FÉRIAS NO MÊS";
//                $this->dados["5037"][$mes] = $rows['ferias'];
//            }
//            
//            if (!empty($rows['valor_ferias']) && $rows['valor_ferias'] != 0.00) {
//                $this->dados["5070"]['nome'] = "FÉRIAS NO MÊS";
//                $this->dados["5070"][$mes] = $rows['valor_pago_ferias'];
//            }
//            EDITADO RENATO 04/03/2016
            //FÉRIAS
//            if (!empty($rows['ferias']) && $rows['ferias'] != 0.00) {
            if($rows['terceiro'] == 2) {
                if (!empty($rows['total_remuneracoes'])) {
                    $this->dados["5037"]['nome'] = "FÉRIAS NO MÊS";
    //                $this->dados["5037"][$mes] = $rows['ferias'];
                    $this->dados["5037"][$mes] = $rows['total_remuneracoes'];
                    $this->historico[$clt][$ano][$mes]["5037"] = $this->dados["5037"][$mes];

                }
            }
            
            if (!empty($rows['pensao_alimenticia'])) {
                $this->dados["80026"]['nome'] = "PENSÃO ALIMENTÍCIA SOBRE FÉRIAS";
//                $this->dados["80026"][$mes] = $rows['ferias'];
                $this->dados["80026"][$mes] = $rows['pensao_alimenticia'];
                $this->historico[$clt][$ano][$mes]["80026"] = $this->dados["80026"][$mes];
                
            }
            
            //DIREFENÇA SALARIAL
//            if (!empty($rows['diferenca_salarial']) && $rows['diferenca_salarial'] != 0.00) {
//                if ($mes != "11" && $mes != "12") {
//                    $this->dados["5012"]['nome'] = "DIFERENÇA SALARIAL";
//                    $this->dados["5012"][$mes] = $rows['diferenca_salarial'];
//                }
//            }

            //SALARIO MATERNIDADE
            if (!empty($rows['salario_maternidade']) && $rows['salario_maternidade'] != 0.00) {
                $this->dados["6005"]['nome'] = "SALARIO MATERNIDADE";
                $this->dados["6005"][$mes] = $rows['salario_maternidade'];
                $this->historico[$clt][$ano][$mes]["6005"] = $this->dados["6005"][$mes];
            }

            //CONTRIBUIÇÃO SINDICAL
            if (!empty($rows['contribuicao_sindical']) && $rows['contribuicao_sindical'] != 0.00) {
                $this->dados["5019"]['nome'] = "CONTRIBUIÇÃO SINDICAL";
                $this->dados["5019"][$mes] = $rows['contribuicao_sindical'];
                $this->historico[$clt][$ano][$mes]["5019"] = $this->dados["5019"][$mes];
            }

            //HORA EXTRA
            if (!empty($rows['hora_extra']) && $rows['hora_extra'] != 0.00) {
                $this->dados["8080"]['nome'] = "HORA EXTRA";
                $this->dados["8080"][$mes] = $rows['hora_extra'];
                $this->historico[$clt][$ano][$mes]["8080"] = $this->dados["8080"][$mes];
            }

            //VERIFICA VALOR 13°
            if ($rows['terceiro'] == 1 && $rows['salliquido'] > 0.00) { 
                if ($rows['mes'] == 11) {
                    $this->dados["5029"]['nome'] = "50% DECIMO TERCEIRO SALARIO";
//                    $this->dados["5029"][$mes] = $rows['decimo_terceiro'] + $rows['rend'];
                    $this->dados["5029"][$mes] = $rows['salliquido'] + $rows['rend'];
                } else {
                    $this->dados["5029"]['nome'] = "DECIMO TERCEIRO SALARIO";
//                    $this->dados["5029"][$mes] = $rows['decimo_terceiro'] + $rows['rend'] - $rows['inss_dt'] - $rows['ir_dt'];
                    $this->dados["5029"][$mes] = $rows['decimo_terceiro'];
                }
                $this->historico[$clt][$ano][$mes]["5029"] = $this->dados["5029"][$mes];
            } else if($rows['terceiro'] == 1 && $rows['mes'] == 12){
                $this->dados["5029"]['nome'] = "DECIMO TERCEIRO SALARIO";
                $this->dados["5029"][$mes] = $rows['decimo_terceiro'];
            }

            //IR SOBRE 13°
            if ($rows['terceiro'] == 1 && $rows['ir_sobre_decimo'] != 0.00) {
                $this->dados["5030"]['nome'] = "IR SOBRE 13°";
                $this->dados["5030"][$mes] = $rows['ir_sobre_decimo'];
                $this->historico[$clt][$ano][$mes]["5030"] = $this->dados["5030"][$mes];
            }

            //INSS SOBRE 13°
            if ($rows['terceiro'] == 1 && $rows['inss_sobre_decimo'] != 0.00) {
                $this->dados["5031"]['nome'] = "INSS SOBRE 13°";
                $this->dados["5031"][$mes] = $rows['inss_sobre_decimo'];
                $this->historico[$clt][$ano][$mes]["5031"] = $this->dados["5031"][$mes];
            }

            //VERIFICA VALOR INSS
            if (!empty($rows['inss']) && $rows['inss'] != 0.00) {
                $this->dados["5020"]['nome'] = "INSS";
                $this->dados["5020"]['ref'] = $rows['t_inss'] * 100 . "%";
                $this->dados["5020"][$mes] = $rows['inss'];
                $this->historico[$clt][$ano][$mes]["5020"] = $this->dados["5020"][$mes];
            } else {
                $this->dados["5035"]['nome'] = "INSS SOBRE FÉRIAS";
                $this->dados["5035"][$mes] = $rows['a5035'];
                $this->historico[$clt][$ano][$mes]["5035"] = $this->dados["5035"][$mes];
            }
            
            if ($rows['status_clt'] > 60 && $rows['status_clt'] != 80) {
                
                if($websaass == 1){
                    $this->dados["5020"]['nome'] = "INSS";
                    $this->dados["5020"][$mes] = 0; // Removi isso por que o inss de folha nao considera inss de rescisao $rows['inss_saldo_rescisao'];
                    $this->historico[$clt][$ano][$mes]["5020"] = $this->dados["5020"][$mes];
                }else{
                    if (!empty($rows['inss_rescisao'])) {
                        $this->dados["5020"]['nome'] = "INSS";
                        $this->dados["5020"][$mes] = $rows['inss_rescisao'];
                        $this->historico[$clt][$ano][$mes]["5020"] = $this->dados["5020"][$mes];
                    }
                }
                
                if (!empty($rows['desconto'])) {
                    $this->dados["9500"]['nome'] = "DESCONTO";
                    $this->dados["9500"][$mes] = $rows['total_deducao'] - $rows['inss_rescisao'];
                    $this->historico[$clt][$ano][$mes]["9500"] = $this->dados["9500"][$mes];
                }
                if (!empty($rows['rend'])) {
                    $this->dados[" - "]['nome'] = "RENDIMENTOS";
                    $this->dados[" - "][$mes] = $rows['rend'];
                    $this->historico[$clt][$ano][$mes][" - "] = $this->dados[" - "][$mes];
                }
            }

            //VERIFICA DDIR
//            if (!empty($rows['DDIR']) && $rows['DDIR'] != 0.00) {
//                $this->dados["5049"]['nome'] = "DDIR";
//                $this->dados["5049"][$mes] = $rows['DDIR'];
//            }
            
            if($websaass == 1){
                
                /*******************PESÃO************************/
                /**************FEITO EM 09/08/2016***************/
                /**************POR SINESIO LUIZ******************/
                
                $queryPensao = "SELECT A.id_clt, C.id_folha, D.nome as nome_projeto, A.nome, C.cod_mov, C.nome_mov, C.base, C.valor_mov,B.favorecido,B.cpf,B.agencia,B.conta,B.id_lista_banco,E.banco
                            FROM rh_clt AS A 
                            LEFT JOIN favorecido_pensao_assoc AS B ON(A.id_clt = B.id_clt) 
                            LEFT JOIN itens_pensao_para_contracheque AS C ON(C.cpf_favorecido = REPLACE(REPLACE(B.cpf,'.',''),'-','') AND C.`status` = 1) 
                            LEFT JOIN projeto AS D ON(A.id_projeto = D.id_projeto) 
                            LEFT JOIN listabancos AS E ON B.id_lista_banco = E.id_lista
                            LEFT JOIN rh_folha AS F ON(C.id_folha = F.id_folha)
                            WHERE A.pensao_alimenticia = 1 AND F.mes = {$mes} AND F.ano = {$ano} 
                            AND A.id_projeto = '{$rows['id_projeto']}' AND A.id_clt = '{$rows['id_clt']}'
                            AND C.id_folha = '{$rows['id_folha']}'";
                
                $sqlPensao = mysql_query($queryPensao) or die("Erro ao selecionar pensao");
                $valorPensao = 0;
                $cod_pensao = 0;
                while($rowsPensao = mysql_fetch_assoc($sqlPensao)){
                    $valorPensao += $rowsPensao['valor_mov'];
                    $cod_pensao = $rowsPensao['cod_mov'];
                }
                
                if (!empty($valorPensao) && $valorPensao != 0.00) {
                    $this->dados[$cod_pensao]['nome'] = "PENSAO";
                    $this->dados[$cod_pensao][$mes] = $valorPensao;
                    $this->historico[$clt][$ano][$mes][$cod_pensao] = $this->dados[$cod_pensao][$mes];
                }
                
                /****************************************************/
                
                $arrayNaoRescisao = array(61,64,66);
                //VERIFICA FGTS
                if (!empty($rows['fgts']) && $rows['fgts'] != 0.00) {
                    if(!in_array($rows['status_clt'], $arrayNaoRescisao)){
                        $this->dados["5023"]['nome'] = "FGTS";
                        $this->dados["5023"][$mes] = $rows['fgts'];
                        $this->historico[$clt][$ano][$mes]["5023"] = $this->dados["5023"][$mes];
                    }
                }

                //VERIFICA PIS
                if (!empty($rows['valor_pis']) && $rows['valor_pis'] != 0.00) {
                    if(!in_array($rows['status_clt'], $arrayNaoRescisao)){
                        $this->dados["5025"]['nome'] = "PIS";
                        $this->dados["5025"][$mes] = $rows['valor_pis'];
                        $this->historico[$clt][$ano][$mes]["5025"] = $this->dados["5025"][$mes];
                    }
                }
                
                //VERIFICA IR FERIAS
                if (!empty($rows['ir_ferias']) && $rows['ir_ferias'] != 0.00) {
                    $this->dados["5036"]['nome'] = "IR FERIAS";
                    $this->dados["5036"][$mes] = $rows['ir_ferias'];
                    $this->historico[$clt][$ano][$mes]["5036"] = $this->dados["5036"][$mes];
                }
            
                //VERIFICA IR RESCISAO
                if (!empty($rows['ir_rescisao']) && $rows['ir_rescisao'] != 0.00) {
                    $this->dados["80023"]['nome'] = "IR RESCISAO";
                    $this->dados["80023"][$mes] = $rows['ir_rescisao'];
                    $this->historico[$clt][$ano][$mes]["80023"] = $this->dados["80023"][$mes];
                }
            
            }
            
            //VERIFICA DESCONTO
            if (!empty($rows['desconto']) && $rows['desconto'] != 0.00) {
                $this->dados["9500"]['nome'] = "DESCONTO";
                $this->dados["9500"][$mes] = $rows['desconto'];
                $this->historico[$clt][$ano][$mes]["9500"] = $this->dados["9500"][$mes];
            }
            
            //VALE TRANSPORTE
            if (!empty($rows['vale_transporte']) && $rows['vale_transporte'] != 0.00) {
                $this->dados["7001"]['nome'] = "DESCONTO VALE TRANSPORTE";
                $this->dados["7001"]['ref'] = "6%";
                $this->dados["7001"][$mes] = $rows['vale_transporte'];
                $this->historico[$clt][$ano][$mes]["7001"] = $this->dados["7001"][$mes];
            }

            //IMPOSTO DE RENDA
            if (!empty($rows['imprenda']) && $rows['imprenda'] != 0.00) {
                $this->dados["5021"]['nome'] = "IMPOSTO DE RENDA";
                $this->dados["5021"]['ref'] = $rows['t_imprenda'] * 100 . "%";
                $this->dados["5021"][$mes] = $rows['imprenda'];
                $this->historico[$clt][$ano][$mes]["5021"] = $this->dados["5021"][$mes];
            }
            
            /**
             * 1 => FOLHA DE 13° TERCEIRO
             * 2 => FOLHA NORMAL
             */
//            if($rows['terceiro'] == 2){
//                //IMPOSTO DE RENDA SOBRE FÉRIAS
//                if (!empty($rows['ir_ferias']) && $rows['ir_ferias'] != 0.00) {
//                    $this->dados["5036"]['nome'] = "IR SOBRE FÉRIAS";
//                    $this->dados["5036"][$mes] = $rows['ir_ferias'];
//                }
//
//                //INSS SOBRE FÉRIAS
//                if (!empty($rows['inss_ferias']) && $rows['inss_ferias'] != 0.00) {
//                    $this->dados["5035"]['nome'] = "INSS SOBRE FÉRIAS";
//                    $this->dados["5035"][$mes] = $rows['inss_ferias'];
//                }
//            }
//
//            //FÉRIAS
////            if (!empty($rows['valor_ferias']) && $rows['valor_ferias'] != 0.00) {
////                $this->dados["50255"]['nome'] = "ADIANTAMENTO DE FÉRIAS";
////                $this->dados["50255"][$mes] = $rows['valor_ferias'];
////            }
            // EDITADO RENATO 04/03/2016
            if($rows['terceiro'] == 2){
                //IMPOSTO DE RENDA SOBRE FÉRIAS
                if (!empty($rows['ir_ferias']) && $rows['ir_ferias'] != 0.00) {
                    $this->dados["5036"]['nome'] = "IR SOBRE FÉRIAS";
                    $this->dados["5036"][$mes] = $rows['ir_ferias'];
                    $this->historico[$clt][$ano][$mes]["5036"] = $this->dados["5036"][$mes];
                }

                //INSS SOBRE FÉRIAS
                if (!empty($rows['inss_ferias'])) {
                    $this->dados["5035"]['nome'] = "INSS SOBRE FÉRIAS";
                    $this->dados["5035"][$mes] = $rows['inss_ferias'];
                    $this->historico[$clt][$ano][$mes]["5035"] = $this->dados["5035"][$mes];
                }
            }
            
            if($_COOKIE['debug'] == 666){
                echo "<br/><br/><br/>************* FERIAS - akkiiiiiii*********<br/><br/>";
                echo "if ({$rows['valor_ferias']} == 2) {";
                echo $this->dados["50255"]['nome'] .'= "ADIANTAMENTO DE FÉRIAS"';
                echo $this->dados["50255"][$mes].' = '.$rows['valor_ferias'];    
                echo $this->historico[$clt][$ano][$mes]["50255"].' = '.$this->dados["50255"][$mes];
            }
            
            //FÉRIAS
            if ($rows['valor_ferias'] > 0 && $rows['terceiro'] == 2) {
                if (!empty($rows['valor_ferias'])) {
                    $this->dados["50255"]['nome'] = "ADIANTAMENTO DE FÉRIAS";
                    $this->dados["50255"][$mes] = $rows['valor_ferias'];    
                    $this->historico[$clt][$ano][$mes]["50255"] = $this->dados["50255"][$mes];
                }
            }
            
            //SALÁRIO FAMÍLIA 
            if (!empty($rows['salario_familia']) && $rows['salario_familia'] != 0.00) {
                $this->dados["5022"]['nome'] = "SALÁRIO FAMÍLIA";
                $this->dados["5022"][$mes] = $rows['salario_familia'];
                $this->historico[$clt][$ano][$mes]["5022"] = $this->dados["5022"][$mes];
            }

            //VERIFICA MOVIMENTOS
//            echo "SELECT A.tipo_qnt,A.qnt, A.mes_mov, A.ano_mov, A.lancamento, A.id_folha, 
//                    A.cod_movimento, A.tipo_movimento, A.nome_movimento, A.valor_movimento, 
//                    B.percentual, if(A.tipo_qnt = 1,A.qnt_horas,B.percentual) as percentual
//                    FROM rh_movimentos_clt AS A
//                    LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)    
//                    WHERE id_movimento IN(" . $rows['ids_movimentos_estatisticas'] . ") AND id_clt = '{$clt}'";
            $ids_movimentos = ($rows['terceiro'] == 1 && $rows['salliquido'] == 0.00) ? $rows['ids_movimentos_folha'] : $rows['ids_movimentos_estatisticas'];
            $sql_movs = "SELECT A.tipo_qnt,A.qnt, A.qnt_horas, A.mes_mov, A.ano_mov, A.lancamento, A.id_folha, 
                    A.cod_movimento, A.tipo_movimento, A.nome_movimento, A.valor_movimento, 
                    if(A.tipo_qnt = 1,A.qnt_horas,B.percentual) as percentual
                    FROM rh_movimentos_clt AS A
                    LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)    
                    WHERE id_movimento IN(" . $ids_movimentos . ") AND id_clt = '{$clt}' ";
            if($_COOKIE['debug'] == 666){
                print_array('////////////////////////////////$sql_movs////////////////////////////////');
                print_array($sql_movs);
            }
            $sql_movs = mysql_query($sql_movs);
                    
//            if($_COOKIE['logado'] == 179){
//                    echo "<pre>";
//                        print_r("SELECT A.tipo_qnt,A.qnt, A.mes_mov, A.ano_mov, A.lancamento, A.id_folha, 
//                    A.cod_movimento, A.tipo_movimento, A.nome_movimento, A.valor_movimento, 
//                    B.percentual, if(A.tipo_qnt = 1,A.qnt_horas,B.percentual) as percentual
//                    FROM rh_movimentos_clt AS A
//                    LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)    
//                    WHERE id_movimento IN(" . $rows['ids_movimentos_estatisticas'] . ") AND id_clt = '{$clt}' ");
//                    echo "</pre>";
//                }        
                    
            while ($d = mysql_fetch_assoc($sql_movs)) {
            
//                if($_GET['debug'] == 666){
//                    echo "<pre>";
//                        print_r($d);
//                    echo "</pre>";
//                }
                
                //$mes = sprintf("%02d", $d['mes_mov']);
                $ref = "";
                $tipagem = "";
                //GAMBIARRAS PARA FUNCIONAR RÁPIDO
                if($d['cod_movimento'] == '9997'){
                    $ref = "5";
                }else if(($d['cod_movimento'] == '50249' || $d['cod_movimento'] == '50227' || $d['cod_movimento'] == '50252')){
                    if($d['tipo_qnt'] == 1){
                       $d['percentual'] =  substr($d['percentual'], 0, -3);
                       $tipagem = "Horas";
                    }else{
                       $tipagem = "Dias"; 
                    }
                    if(!empty($d['percentual']) && isset($d['percentual']) ){
                        $ref = $d['percentual'] . " " . $tipagem;
                    }
                    
                }else if($d['cod_movimento'] == '10011' || $d['cod_movimento'] == '10010'){
                    $ref = $d['qnt'];
                }else if($d['cod_movimento'] == '7013' || $d['cod_movimento'] == 80025){
//                    $ref = "90%";
                    $ref = $d['qnt_horas'];
                }else if($d['cod_movimento'] == '90052'){
                    $ref = "90%";
//                    $ref = $d['qnt_horas'];
                }else if($d['cod_movimento'] == '90053'){
                    $ref = "100%";
//                    $ref = $d['qnt_horas'];
                }
                else if($d['cod_movimento'] == '9000'){
                    $qr = "SELECT b.prcentagem_add_noturno FROM rh_clt AS a INNER JOIN rhsindicato AS b ON a.rh_sindicato = b.id_sindicato WHERE id_clt =$clt";
                    $add_not = mysql_fetch_assoc(mysql_query($qr));
                    $ref = ($add_not['prcentagem_add_noturno'] * 100) . "%";
                }else{
                    $ref = $d['percentual'];
                    if($ref != 0){
                        $ref = ($ref * 100) . "%";
                    }
//                    $ref = $d['qnt_horas'];
                }
                
                if($ref == "(NULL)" && $ref == 0){
                    $ref = "";
                }
                        
                
                if ($d['lancamento'] == '2') {
                    $this->dados[$d['cod_movimento']]['nome'] = $d['nome_movimento'];
                    $this->dados[$d['cod_movimento']]['ref'] = $ref;
                    if (!empty($meses)) {
                        $this->dados[$d['cod_movimento']][sprintf("%02d", $meses)] += $d['valor_movimento'];
                    } else {
                        $this->dados[$d['cod_movimento']][sprintf("%02d", $mes)] += $d['valor_movimento']; //$count
                    }
                                        
                } else {
                    
                    $this->dados[$d['cod_movimento']]['nome'] = $d['nome_movimento'];
                    $this->dados[$d['cod_movimento']]['ref'] = $ref;
                    $this->dados[$d['cod_movimento']][sprintf("%02d", $mes)] = $d['valor_movimento'];
                    
                }
                
                $this->historico[$clt][$ano][$mes][$d['cod_movimento']] = $d['valor_movimento'];
            }
            $count++;
        }
        
        if($_GET['debug'] == 666 || $_COOKIE['debug'] == 666){
            echo "<pre>";
                print_r($this->dados);
            echo "</pre>";
        }
                
    }


    /**
     * MÉTODO QUE MONTA CABEÇALHO
     * @return type
     */
    public function getCabecalho() {
        $this->cabecalho = array(
            "01" => "JAN",
            "02" => "FEV",
            "03" => "MAR",
            "04" => "ABR",
            "05" => "MAIO",
            "06" => "JUN",
            "07" => "JUL",
            "08" => "AGO",
            "09" => "SET",
            "10" => "OUT",
            "11" => "NOV",
            "12" => "DEZ");
        //,"13" => "1 PARC. 13°", "14" => "2 PARC. 13°", "15" => "INTE. 13°"
        return $this->cabecalho;
    }

    /**
     * 
     * @return type
     */
    public function getDadosFicha() {
        return $this->dados;
    }
    
    /**
     * 
     * @return type
     */
    public function getHistoricoDeFolha() {
        return $this->historico;
    }
    
    /**
     * 
     * @param type $folha
     * @return type
     */
    public function getDadosFolhaById($folha, $status = null, $clt = null) {
        
        $_status = (!empty($status)) ? " AND A.status = {$status}" : ""; 
        $_clt = (!empty($clt)) ? " AND A.id_clt = {$clt}" : "";
        
        $query = "SELECT A.id_folha, A.id_clt, D.data_inicio, D.data_fim, A.id_projeto, A.nome, A.salbase, A.sallimpo, A.base_inss, A.valor_ferias, A.rend, A.base_irrf, if(dias_trab < 30, A.sallimpo_real,A.sallimpo) AS salario, C.nome AS funcao, DATE_FORMAT(B.data_entrada,'%d/%m/%Y') AS data_admissao, A.mes AS mes_proc, A.ano AS ano_proc,
                    A.a50222 AS DEP_SALARIO_FAMILIA, A.qnt_dependentes AS DEP_IRRF, A.inss, A.fgts, A.imprenda, A.imprenda, CONCAT(A.mes,'/',A.ano) AS mes_competencia, B.id_centro_custo, B.id_regiao
                    FROM rh_folha_proc AS A
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt )
                    LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
                    LEFT JOIN rh_folha AS D ON(A.id_folha = D.id_folha)
                    WHERE A.id_folha = '{$folha}' {$_status} {$_clt}";
        
        $clts = mysql_query($query) or die("Error ao seleciona clts por folha");
        return $clts;
    }

    /***
     * 
     */
    public function getVerificaDecimoByFolha($folha){
        $query = " SELECT D.terceiro
                        FROM rh_folha_proc AS A
                        LEFT JOIN rh_folha AS D ON(D.id_folha = A.id_folha)
                        WHERE A.id_folha = '{$folha}' limit 1";
        
        echo "<!--$query -->";
        $sql = mysql_query($query) or die("Erro ao verificar se é do tipo 13° ");
        $dados = mysql_fetch_assoc($sql);
        
        return $dados["terceiro"];
                        
    }
    
    /**
     * 
     * @param type $clt
     * @param type $folha
     * @return type
     */
    public function getDecimoTerceiroByClt($clt, $folha) {
        $query = "SELECT * FROM rh_folha_proc AS A  LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha) WHERE A.id_folha = '{$folha}' AND A.id_clt = '{$clt}' AND B.terceiro = '1'";
        $dados_decimo_terceiro = mysql_query($query) or die("Erro ao selecionar decimo terceiro");
        return $dados_decimo_terceiro;
    }

    /**
     * 
     * @param type $clt
     * @param type $folha
     * @return type
     */
    public function getValoresBase($clt, $folha) {
        $query = "SELECT A.base_inss, A.base_irrf, A.base_fgts, A.fgts FROM rh_folha_proc AS A WHERE A.id_folha = '{$folha}' AND A.id_clt = '{$clt}'";
        $valoresBase = mysql_query($query) or die("Erro ao selecionar Valores de base");
        return $valoresBase;
    }

    /**
     * 
     * @param type $folha
     * @return type
     */
    public function getTotaisBase($folha) {
        $query = "SELECT A.total_irrf, A.total_fgts, A.total_inss FROM rh_folha AS A WHERE A.id_folha = '{$folha}'";
        $totaisBase = mysql_query($query) or die("Erro ao selecionar totais de base");
        return $totaisBase;
    }

    /**
     * 
     * @param type $id_regiao
     * @param type $id_projeto
     * @return type
     */
    public static function getCursos($id_regiao, $id_projeto) {
        $query = "SELECT A.id_curso, A.nome, B.cod, A.salario, A.qnt_maxima, C.tipo_contratacao_nome
                FROM curso AS A
                LEFT JOIN rh_cbo AS B ON (A.cbo_codigo = B.id_cbo)
                LEFT JOIN tipo_contratacao AS C ON (A.tipo = C.tipo_contratacao_id)
                WHERE A.status = 1 AND A.id_regiao = '{$id_regiao}' AND A.campo3 = '{$id_projeto}' ORDER BY C.tipo_contratacao_nome";

        $curso = mysql_query($query) or die(mysql_error());
        return $curso;
    }

    /**
     * 
     * @return type
     */
    public function getMovCredito() {
        $query = "SELECT cod FROM rh_movimentos WHERE categoria = 'CREDITO'";
        $movimento = mysql_query($query) or die(mysql_error("erro de query"));
        return $movimento;
    }

    /**
     * 
     * @return type
     */
    public function getMovDebito() {
        $query = "SELECT cod FROM rh_movimentos WHERE categoria IN('DEBITO','DESCONTO')";
        $movimento = mysql_query($query) or die(mysql_error("erro de query"));
        return $movimento;
    }

    /**
     * 
     * @return type
     */
    public function getProjeto($master) {
        $query = "SELECT * FROM projeto WHERE id_master = '{$master}'";
        $projeto = mysql_query($query) or die("Erro de query");
        return $projeto;
    }


    /**
     * MÉTODO DE CRIAÇÃO DE LOG PARA INCLUSAO DE USUÁRIO NA FOLHA (TIPO = 1)
     * @param type $clts
     * @param type $folha
     * @param type $usuario
     */
    public function logIncluirNaFolha($clts = array(), $folha, $usuario) {
        
        if (is_array($clts) && !empty($clts)) {
            $values = "";
            //MONTANDO OS VALUES 
            foreach ($clts as $dados) {
                $values .= "('{$folha}','{$usuario}','{$dados}','1'),";
            }
            //REMOVENDO O ULTIMO CARACTER
            $values = substr($values, 0, -1);
            //QUERY PRONTA 
            $query = "INSERT INTO log_folha (id_folha,id_usuario,id_clt,tipo) VALUES {$values}";
            $sql = mysql_query($query);
        }
    }
    
    /**
     * 
     * @param type $clts
     * @param type $folha
     * @param type $usuario
     */
    public function logExcluirDaFolha($clts, $folha, $usuario) {
        //QUERY PRONTA 
        $query = "INSERT INTO log_folha (id_folha,id_usuario,id_clt,tipo) VALUES ('{$folha}','{$usuario}','{$clts}','2')";
        $sql = mysql_query($query);
       
    }
    
    /**
     * 
     * @param type $clts
     * @param type $folha
     * @param type $usuario
     */
    public function logDesfazerExcluirFolha($clts, $folha, $usuario){
        //QUERY PRONTA 
        $query = "INSERT INTO log_folha (id_folha,id_usuario,id_clt,tipo) VALUES ('{$folha}','{$usuario}','{$clts}','7')";
        $sql = mysql_query($query)  or die("Erro de exclusão");
    }
    
    /**
     * MÉTODO PARA GRAVAÇÃO DE LOG DE CRIAÇÃO DA FOLHA
     * @param type $folha
     * @param type $usuario
     */
    public function logCriarFolha($folha, $usuario){
        //QUERY PRONTA 
        $query = "INSERT INTO log_folha (id_folha,id_usuario,tipo) VALUES ('{$folha}','{$usuario}','3')";
        $sql = mysql_query($query) or die("Erro de cadastro");
    }
    
     /* 
     * @param type $folha
     * @param type $usuario
     */
    public function logExcluirFolhaAberta($folha, $usuario){
        //QUERY PRONTA 
        $query = "INSERT INTO log_folha (id_folha,id_usuario,tipo) VALUES ('{$folha}','{$usuario}','6')";
        $sql = mysql_query($query) or die("Erro de exclusão de folha");
    }
    
    /**
     * 
     * @param type $folha
     * @param type $usuario
     */
    public function logFecharFolha($folha, $usuario){
        //QUERY PRONTA 
        $query = "INSERT INTO log_folha (id_folha,id_usuario,tipo) VALUES ('{$folha}','{$usuario}','4')";
        $sql = mysql_query($query) or die("Erro de exclusão de folha");
    }
    
    /**
     * 
     * @param type $folha
     * @param type $usuario
     */
    public function logDesprocessaFolha($folha, $usuario){
        //QUERY PRONTA 
        $query = "INSERT INTO log_folha (id_folha,id_usuario,tipo) VALUES ('{$folha}','{$usuario}','5')";
        $sql = mysql_query($query) or die("Erro de exclusão de folha");
    }
    
    /**
     * RETORNA LISTA DE FOLHAS
     * @param type $objeto (STD_CLASS)
     * @return type
     */
    public function getListaFolhas($objeto){
                
        $arr[] = montaCriteriaSimples($objeto->ano, "A.ano");
        $arr[] = montaCriteriaSimples($objeto->id_projeto, "A.projeto");
        $arr[] = montaCriteriaSimples($objeto->id_regiao, "B.id_regiao");
        $arr[] = montaCriteriaSimples($objeto->mes, "A.mes");
        $arr[] = montaCriteriaSimples($objeto->status, "A.status");
        
        $_campos = "A.data_inicio, A.data_fim, A.clts AS quant_clt, B.id_projeto, A.status, A.id_folha, B.nome AS nome_projeto, C.nome AS criado_por, A.mes, A.ano,  CONCAT(date_format(A.data_inicio, '%d/%m/%Y'),' ATÉ ',date_format(A.data_fim, '%d/%m/%Y')) AS periodo, A.terceiro, A.tipo_terceiro";
        
        if(isset($objeto->campos) && !empty($objeto->campos)){
            $_campos = $objeto->campos;
        }
        
        
        $criteria = (array_filter($arr));
                        
        $query = "SELECT {$_campos}
                FROM rh_folha AS A
                LEFT JOIN projeto AS B ON(A.projeto = B.id_projeto)
                LEFT JOIN funcionario AS C ON(A.user = C.id_funcionario)
                WHERE ".implode(" AND ",$criteria)." 
                ORDER BY A.projeto, A.ano, A.mes";
        
        $sql = mysql_query($query) or die("Erro ao selecionar folhas finalizadas");
        
        while($linha = mysql_fetch_assoc($sql)){
                /*$dados[$linha['id_folha']] = array(
                    "folha" => $linha['id_folha'], 
                    "projeto" => $linha['nome_projeto'], 
                    "criado_por" => $linha['criado_por'],
                    "mes" => $linha['mes'],
                    "periodo" => $linha['periodo'],
                    "total_clts" => $linha['quant_clt']
                );*/
                $dados[$linha['id_folha']] = $linha;
            }
        
        return $dados;
    }
    
    /**
     * RETORNA FOLHAS FINALIZADAS
     * @param type $regiao
     * @return type
     */
    public function getAnosFolhaFinalizada($regiao,$status=3){
        $dados = array();
        $query = "SELECT B.ano
                FROM projeto AS A
                LEFT JOIN rh_folha AS B ON(A.id_projeto = B.projeto)
                WHERE A.id_regiao = '{$regiao}' AND B.`status` = {$status}
                GROUP BY B.ano";
        $sql = mysql_query($query) or die("Erro ao selecionar ano de folhas finalizada");        
        
        while($linha = mysql_fetch_assoc($sql)){
            $dados[$linha['ano']] = $linha['ano'];
        }
        
        return $dados;
    }
    
    
    /**
     * 
     * @param type $clt
     * @return type
     */
    public function getMultaFgts($clt){
        $query = "SELECT ROUND(fgts_valor * 0.50, 2) AS multa_fgts FROM (
                    SELECT SUM(if(A.a5031 != 0.00, A.a5031 , A.fgts)) AS fgts_valor
                    FROM rh_folha_proc AS A
                    WHERE A.id_clt = {$clt}
            ) AS tmp";
        $sql = mysql_query($query) or die("erro ao calcular multa de fgts");
        $dados = mysql_fetch_assoc($sql);
        
        return $dados['multa_fgts']; 
    }
    
    /**
     * 
     * @param type $clt
     * @return type
     */
    public function getInssARecolher($clt){
        $query = "SELECT ROUND(A.base_inss_ss + A.base_inss_13 * 0.20, 2) AS empresa, ROUND(A.base_inss_ss + A.base_inss_13 * 0.058, 2) AS terceiro
                    FROM rh_recisao AS A
                    WHERE A.id_clt = '{$clt}'";
        $sql = mysql_query($query) or die("erro ao calcular multa de fgts");
        $dados = mysql_fetch_assoc($sql);
        
        return $dados; 
    }
    
    /**
     * 
     */
    public function zeraArray() {
        $this->dados = array();
    }
    
    /**
     * 
     */
    public function getPermissoesFolha(){
        
        $_acoes = array('GERAR FOLHA CLT',
                        'VISUALIZAR FOLHA CLT',
                        'DELETAR FOLHA CLT',
                        'DESPROCESSAR FOLHA CLT',
                        'PROCESSAR  FOLHA CLT',
                        'PAGAMENTO EM LOTE',
                        'ATUALIZAR DADOS BANCÁRIOS',
                        'FINALIZAR FOLHA CLT');
        $OjbPermissoes = new BotoesClass();
        
        $rs = $OjbPermissoes->getAcoesPagina($this->id_pagina);
        while ($row = mysql_fetch_assoc($rs)) {
            echo "<pre>";
                print_r($row);
            echo "</pre>";
        }
        
        exit;
    }
    
    /**
     * 
     * @param type $folhas
     * @param type $movimentos
     * @return type
     */
    public function getFolhaComparada($folhas = array(), $movimentos = array()){
        
        if(is_array($folhas)){
            $folhas_itens = implode($folhas, ",");
        }
        
        if(is_array($movimentos)){
            $movimentos_itens = implode($movimentos, ",");
        }
        
        $dados = array();
        $query = "SELECT A.id_folha, A.cod_movimento, A.mes_mov, A.ano_mov, A.nome_movimento, C.nome, SUM(A.valor_movimento) AS total
            FROM rh_movimentos_clt AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
            WHERE A.id_folha IN({$folhas_itens}) AND A.cod_movimento IN({$movimentos_itens}) 
            GROUP BY A.id_folha, A.cod_movimento, B.id_curso
            ORDER BY A.id_folha, A.cod_movimento, A.mes_mov, A.ano_mov";
            
            //echo $query;
        
        $sql = mysql_query($query) or die("Erro ao selecionar folhas");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[$rows['id_folha']][$rows['ano_mov']][$rows['mes_mov']][$rows['cod_movimento']]["nome"] = $rows['nome_movimento']; 
                $dados[$rows['id_folha']][$rows['ano_mov']][$rows['mes_mov']][$rows['cod_movimento']]["cargo"][$rows['nome']] = $rows['total']; 
            }
        }
        
        return $dados; 
    }
    
    /**
     * 
     * @param type $regiao
     * @param type $projeto
     * @param type $quantidade
     * @return type
     */
    public function getUltimasFolhas($regiao, $projeto, $quantidade){
        $dados = array();
        $query = "SELECT A.id_folha, MONTH(A.data_inicio) AS mes, YEAR(A.data_inicio) AS ano
                FROM rh_folha AS A
                WHERE A.status = 3 AND A.regiao = '{$regiao}' AND A.projeto = '{$projeto}'
                GROUP BY A.id_folha
                ORDER BY A.id_folha DESC
                LIMIT {$quantidade}";
        
        $sql = mysql_query($query) or die("Erro ao selecionar folhas");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados["folha"][] = $rows['id_folha']; 
                $dados["mes"][] = $rows['mes']; 
                $dados["ano"][] = $rows['ano']; 
            }
        }
        
        return $dados; 
    }
    
    
    /**
     * 
     * @param type $percentual
     * @param type $tipoQnt
     * @param type $qnt
     * @param type $qnt_horas
     * @return type
     */
    public function verificaFrequenica($percentual,$tipoQnt =NULL, $qnt = NULL, $qnt_horas = NULL){
        

        $percent = (strstr($percentual, ".")) ? ($percentual * 100) . "%" : $percentual;
        $percent = ($percent == 0) ? $row_mov2['qnt'] : $percent;
        $percent = ($percent != '(NULL)') ? $percent : '-';

        switch($tipoQnt){
            case 1:      $tipo_quantidade = " horas";
                        $percent = substr($row_mov2['qnt_horas'], 0,5);
                break;
            case 2: $tipo_quantidade = " dias";
                     $percent = $row_mov2['qnt'];
                     break;
                 default : $tipo_quantidade = "";
        }
        
       return $percent.$tipo_quantidade;
    }
    
            
    /**
     *  
     * @param type $id_folha
     * @return type
     */
    public function getResumoPorMovimento($id_folha){
        
        $qr_folha = mysql_query("SELECT A.*, SUBSTR(A.data_fim,0,4) AS periodo_ano,  SUM(B.sallimpo_real), (A.total_inss) AS tot_inss, (A.inss_ferias) AS tot_inss_ferias,
                            (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = A.id_folha AND status = 3) AS total_clt,
                            (SELECT COUNT(id_clt) FROM rh_recisao WHERE DATE_FORMAT(data_demi, '%Y-%m') = CONCAT(A.ano,'-',A.mes) AND status = 1 AND total_liquido != 0 AND id_projeto = A.projeto) AS total_rescindidos,
                            (SELECT SUM(sallimpo_real) FROM rh_folha_proc WHERE id_folha = A.id_folha AND status = 3 AND status_clt NOT IN (60,61,62,63,64,65)) AS total_limpoB 
                            FROM rh_folha A 
                            INNER JOIN rh_folha_proc B ON (A.id_folha = B.id_folha)
                            WHERE A.id_folha = '{$id_folha}' AND A.status = '3'");
        $row_folha = mysql_fetch_assoc($qr_folha);        
        
        
        
        $qr_dados= mysql_query("SELECT 
                                    (SELECT SUM(a6005)  as total FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3) as total_maternidade,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND a6005 != '0.00') as total_trab_maternidade,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND status_clt = '10') as total_trab,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND valor_dt != '0.00') as total_13_trab,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND status_clt>=60 AND status_clt != 200) as total_rescindidos,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND valor_ferias != '0.00') as total_ferias,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND a5019 != '0.00') as total_clt_sindical,
                                    (SELECT COUNT(id_clt) FROM rh_folha_proc WHERE id_folha = {$id_folha} AND status = 3 AND a5022 != '0.00') as total_clt_familia
                                    ");
        $row_dados = mysql_fetch_assoc($qr_dados); 
            
         // Resumo por Movimento
        if($row_folha['mes'] <= '06' && $row_folha['ano'] <= "2016"){
        $movimentos_folha = array('0001'=> array('valor' => $row_folha['total_limpoB'], 'qnt'=> $row_dados['total_trab']), 
                                    '5029' => array('valor' => $row_folha['valor_dt'], 'qnt'=> $row_dados['total_13_trab']),
                                    '5037' => array('valor' => $row_folha['valor_ferias'], 'qnt'=> $row_dados['total_ferias']),
                                    '80020'=> array('valor' => $row_folha['valor_pago_ferias'], 'qnt'=>''),
                                    '4007' => array('valor' => $row_folha['valor_rescisao'], 'qnt'=> $row_dados['total_rescindidos']),
                                    '80021'=> array('valor' => $row_folha['valor_pago_rescisao'], 'qnt'=>''),
                                    '5020' => array('valor' => $row_folha['tot_inss'], 'qnt'=>''),
                                    '5035' => array('valor' => $row_folha['tot_inss_ferias'], 'qnt'=>''),
                                    '5031' => array('valor' => $row_folha['inss_dt'], 'qnt'=>''),
                                    '80022'=> array('valor' => $row_folha['inss_rescisao'], 'qnt'=>''),                    
                                    '5021' => array('valor' => $row_folha['total_irrf'], 'qnt'=>''),
                                    '5030' => array('valor' => $row_folha['ir_dt'], 'qnt'=>''),
                                    '5036' => array('valor' => $row_folha['ir_ferias'], 'qnt'=>''),
                                    '80023' =>array('valor' => $row_folha['ir_rescisao'], 'qnt'=>''),
                                    '5022' => array('valor' => $row_folha['total_familia'], 'qnt'=> $row_dados['total_clt_familia']),
                                    '5019' => array('valor' => $row_folha['total_sindical'], 'qnt'=> $row_dados['total_clt_sindical']),
                                    '7001' => array('valor' => $row_folha['total_vt'], 'qnt'=>''), 
                                    '8003' => array('valor' => $row_folha['total_vr'], 'qnt'=>''),
                                    '6005' => array('valor' => $row_dados['total_maternidade'], 'qnt'=> $row_dados['total_trab_maternidade'])  );
        }else{
            $movimentos_folha = array('0001'=> array('valor' => $row_folha['total_limpo'], 'qnt'=> $row_dados['total_trab']), 
                                    '5029' => array('valor' => $row_folha['valor_dt'], 'qnt'=> $row_dados['total_13_trab']),
                                    '5037' => array('valor' => $row_folha['valor_ferias'], 'qnt'=> $row_dados['total_ferias']),
                                    '80020'=> array('valor' => $row_folha['valor_pago_ferias'], 'qnt'=>''),
                                    '4007' => array('valor' => $row_folha['valor_rescisao'], 'qnt'=> $row_dados['total_rescindidos']),
                                    '80021'=> array('valor' => $row_folha['valor_pago_rescisao'], 'qnt'=>''),
                                    '5020' => array('valor' => $row_folha['tot_inss'], 'qnt'=>''),
                                    '5035' => array('valor' => $row_folha['tot_inss_ferias'], 'qnt'=>''),
                                    '5031' => array('valor' => $row_folha['inss_dt'], 'qnt'=>''),
                                    '80022'=> array('valor' => $row_folha['inss_rescisao'], 'qnt'=>''),                    
                                    '5021' => array('valor' => $row_folha['total_irrf'], 'qnt'=>''),
                                    '5030' => array('valor' => $row_folha['ir_dt'], 'qnt'=>''),
                                    '5036' => array('valor' => $row_folha['ir_ferias'], 'qnt'=>''),
                                    '80023' =>array('valor' => $row_folha['ir_rescisao'], 'qnt'=>''),
                                    '5022' => array('valor' => $row_folha['total_familia'], 'qnt'=> $row_dados['total_clt_familia']),
                                    '5019' => array('valor' => $row_folha['total_sindical'], 'qnt'=> $row_dados['total_clt_sindical']),
                                    '7001' => array('valor' => $row_folha['total_vt'], 'qnt'=>''), 
                                    '8003' => array('valor' => $row_folha['total_vr'], 'qnt'=>''),
                                    '6005' => array('valor' => $row_dados['total_maternidade'], 'qnt'=> $row_dados['total_trab_maternidade'])  );
        
        }
        
//        $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos WHERE cod IN('0001','5029','5037', '5037','4007', '4007','5020', '5031', '4007','5021', '5030', '5036', '4007','5022', '5019', '7001', '8003', '6005', 
//                                                                                  '80020','80021','80022','80023') GROUP BY cod");
        $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos 
                                    WHERE cod IN('0001','5029','5037','4007','5020','5031','5021','5030',
                                    '5035','5036','5022','5019','7001','8003','6005','80020','80021','80022','80023','90017')
                                    GROUP BY cod");
        while($mov = mysql_fetch_assoc($qr_movimentos)){
            
            if($movimentos_folha[$mov['cod']]['valor'] == '0.00') continue;
            
            if($mov['cod'] != 5020 and $mov['cod'] != 5021){
                $percent = (strstr($mov['percentual'], ".")) ? ($mov['percentual'] * 100) . "%" : $mov['percentual'];
                $percent = ($percent == 0) ? $row_mov2['qnt'] : $percent;
                $percent = ($percent != '(NULL)') ? $percent : '-';
                $movimento[$mov['cod']]['percentual'] = $percent;
            }
            
            $nome = ($mov['cod'] == '0001')?'SALARIO':$mov['descicao'];
            
            
           $movimento[$mov['cod']]['nome']  = $nome;
           $movimento[$mov['cod']]['tipo']  = $mov['categoria'];
           $movimento[$mov['cod']]['qnt']   = $movimentos_folha[$mov['cod']]['qnt'];      
           $movimento[$mov['cod']]['valor'] = $movimentos_folha[$mov['cod']]['valor'];
           unset($percent);
        }
            
        $qrMov = mysql_query("SELECT A.cod_movimento, A.tipo_movimento, A.nome_movimento, SUM(valor_movimento) as valor,
            SUM(qnt)as qnt,SEC_TO_TIME(SUM(TIME_TO_SEC(qnt_horas))) as qnt_horas, B.percentual
            FROM rh_movimentos_clt as A
            INNER JOIN rh_movimentos as B ON A.id_mov = B.id_mov
            WHERE id_movimento IN({$row_folha['ids_movimentos_estatisticas']})GROUP BY A.id_mov");
            
//        if($_COOKIE['logado'] == 179 || $_COOKIE['logado'] == 353){
//            echo    "SELECT A.cod_movimento, A.tipo_movimento, A.nome_movimento, SUM(valor_movimento) as valor,
//            SUM(qnt)as qnt,SEC_TO_TIME(SUM(TIME_TO_SEC(qnt_horas))) as qnt_horas, B.percentual
//            FROM rh_movimentos_clt as A
//            INNER JOIN rh_movimentos as B ON A.id_mov = B.id_mov
//            WHERE mes_mov != 16 AND id_movimento IN({$row_folha['ids_movimentos_estatisticas']})GROUP BY A.id_mov";
//        }
        
        while($mov = mysql_fetch_assoc($qrMov)){
           
           /* $percent = (strstr($mov['percentual'], ".")) ? ($mov['percentual'] * 100) . "%" : $mov['percentual'];
            $percent = ($percent == 0) ? $row_mov2['qnt'] : $percent;
            $percent = ($percent != '(NULL)') ? $percent : '-';
           */
           $horas = explode(':', $mov['qnt_horas']); 
           $horas = ($mov['qnt_horas'] != '00:00:00')? $horas[0].':'.$horas[1].' horas' : '' ;
           $movimento[$mov['cod_movimento']]['nome'] = $mov['nome_movimento'];
           $movimento[$mov['cod_movimento']]['tipo'] = $mov['tipo_movimento'];
           $movimento[$mov['cod_movimento']]['qnt']   = $mov['qnt'];
           $movimento[$mov['cod_movimento']]['qnt_horas'] = $horas;
           $movimento[$mov['cod_movimento']]['percentual'] = $percent;
           if($row_folha['id_folha'] == 104 && $mov['cod_movimento'] == 5050){
               $movimento[$mov['cod_movimento']]['valor'] = '1966287.28';
           }else{
               $movimento[$mov['cod_movimento']]['valor'] = $mov['valor'];
           }
        }
        
       
            
        return $movimento;
    }
    
    
    /**
     * Metodo para verificar se o CLT vai entrar na folha
     * @param type $status_clt
     * @param type $dataInicioFolha
     * @param type $dataFimFolha
     * @param type $dataEntrada
     * @param type $data_demissao
     * @param type $folhaTerceiro
     * @return string
     */
    public function verificaInsereFolha($status_clt, $dataInicioFolha,$dataFimFolha, $dataEntrada,$data_demissao,$folhaTerceiro) {
        
        list($dt_ini['ano'], $dt_ini['mes'], $dt_ini['dia']) = explode("-",$dataInicioFolha);
        list($dt_entrada2['ano'], $dt_entrada2['mes'], $dt_entrada2['dia']) = explode("-",$dataEntrada);
        
       $dia_15 = (cal_days_in_month(CAL_GREGORIAN, $dt_ini['mes'], $dt_ini['ano']) == 31 )? 16 :15;
        
          switch ($status_clt) {
			  case 10:
                          case 20:    
                          case 21: 
                          case 70: 
                          case 30: 
                          case 50:    
			  	$ferias = "";
				$insere = "sim";
			  break;
			  case 40:
			        $ferias = 1;
				$insere = "sim";
			  break;		 
			  
			  case 200:
			    #AGUARDANDO DEMISSAO SÓ VAI ENTRAR SE A DEMISSÃO FOR DEPOIS DO MES DA FOLHA	
				# SE 2009-10-01 MAIOR Q 2009-06-01 E 10 NAO FOR IGUAL A 06 = TRUE
				# SE 2009-06-05 MAIOR Q 2009-06-01 E 06 NAO FOR IGUAL A 06 = FALSE
				 if($data_demissao >= $dataInicioFolha 
                                    or $data_demissao == '0000-00-00'  
                                    or empty($data_demissao))
                                {
					$ferias = "";
					$insere = "sim";
				}else{
					$ferias = "";
					$insere = "nao";
				}
			  break;
                          
			  case 60: 
                          case 61:
                          case 62: 
                          case 81:
                          case 63: 
                          case 101:
                          case 64:
                          case 65: 
                          case 66:
			  if($folhaTerceiro != 1 ){                                     
                                        # VERIFICA SE SAIU NO MESMO MES DA FOLHA PARA GRAVAR STATUS DE RESCISÃO NESSA FOLHA
                                       if($data_demissao >= $dataInicioFolha){
                                                $ferias = 2;
                                                $insere = "sim";                                                
                                        }else{
                                                $ferias = "";
                                                $insere = "nao";                              
                                        }
                              } else {
                                  $insere = 'nao';
                              }
			  break;
                        default :
                          $fora_da_folha = 'sim';
                        break;
		  }
                  
                # SE 2009-06-25 MAIOR Q 2009-06-01 E SE 06 = 06 = TRUE
		  # SE 2009-06-25 MAIOR Q 2009-06-01 E SE 07 = 06 = FALSE
		  if($dataEntrada >= $dataInicioFolha and $dataEntrada <= $dataFimFolha){	                           
                        if($dt_entrada2['mes'] == $dt_ini['mes'] and $folhaTerceiro == 1){ ////CONDIÇÃO DOS 15 DIAS PARA A FOLHA DE DÉCIMO terceiro                        
                                
                            if($dt_entrada2['dia'] <= $dia_15 ){       
                                    $insere = "sim";
                                } else {
                                    $insere ='nao';
                               }                          
                                                      
                        }else { $insere = "sim"; }
                        
                  }        
                  
         $resultado['ferias'] = $ferias;
         $resultado['insere'] = $insere;
         $resultado['fora_da_folha'] = $fora_da_folha;
         return $resultado;
         
                  
		  
    }
    
    /**
     * 
     * @param type $folha
     * @return type
     */
    public function getFolhaInfo($folha) {
        $qry = "SELECT A.regiao, A.projeto, A.mes, A.ano, A.terceiro AS folha_terceiro, C.id_master
            FROM rh_folha AS A
            LEFT JOIN regioes AS B ON(B.id_regiao = A.regiao)
            LEFT JOIN projeto AS C ON(C.id_projeto = A.projeto)
            WHERE A.id_folha = '{$folha}'";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        return $res;
    }

    /**
     * NUMERO DE FALTAS E DIAS QUE O FUNCIONARIO FALTOU
     * @param type $clt
     * @return string
     */
    public function getObsDeFaltaNoContraCheque($clt, $mes, $ano){
        $retorno = "Faltas no(s) dia(s): ";
        $query = "SELECT A.obs
                    FROM rh_movimentos_clt AS A
                    WHERE A.id_clt = '{$clt}' AND A.id_mov IN (232,293) AND A.mes_mov = {$mes} AND A.ano_mov = {$ano} AND A.status = 5";
                    
//        if($_COOKIE['logado'] == 179){
//            echo $query;
//        }           
                    
        $sql = mysql_query($query);                    
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $retorno .= $rows['obs'] . ", ";
            }
        }
        
        //$retorno = substr($retorno, $start)
        
        return $retorno;
    }
    
    /**
     * TOTALIZADOR DA FOLHA
     * ESSE METODO UTILIZA AS MESMAS CONSULTAS DO QUE É PASSADO PARA A CONTABILIDADE
     * @return type
     */
    public function totalizador($folha) {
        
        /**
         * DADOS DA FOLHA
         */
        $dadosFolha = $this->getFolhaInfo($folha);
        $competencia = $dadosFolha['ano'].'-'.$dadosFolha['mes'];
        
        /**
         * DADOS DA RESCISÃO
         */
        $queryRescisoes = "SELECT *
            FROM rh_recisao AS A
            WHERE DATE_FORMAT(A.data_demi,'%Y-%m') = '2015-07' 
            AND A.id_projeto = '3302' AND A.`status` = 1";
        $sqlRescisoes   = mysql_query($queryRescisoes) or die("Erro query");
        $dados      = array();
        
        
        if($sqlRescisoes){
            while($rows = mysql_fetch_assoc($sqlRescisoes)){
                /**
                 * VERBAS RESCISÓRIA
                 * CAMPOS DA TABELA rh_recisao
                 */
                
                /**
                 * MOVIMENTOS FIXOS DE CREDITO
                 */
                
                /**
                 * SALDO DE SALARIO
                 */
                $dados[$folha]['rescisao']['saldo_salario']['verba']  =  "Saldo de Salário";
                $dados[$folha]['rescisao']['saldo_salario']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['saldo_salario']['valor'] += $rows['saldo_salario'];
                
                /**
                 * COMISSÃO
                 */
                $dados[$folha]['rescisao']['comissao']['verba']  =  "Comissão";
                $dados[$folha]['rescisao']['comissao']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['comissao']['valor'] += $rows['comissao'];
                
                /**
                 * INSALUBRIDADE
                 */
                $dados[$folha]['rescisao']['insalubridade']['verba']  = "Insalubridade";
                $dados[$folha]['rescisao']['insalubridade']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['insalubridade']['valor'] += $rows['insalubridade'];
                
                
                /**
                 * MULTA 480
                 */
                $dados[$folha]['rescisao']['multa_480']['verba']     = "Multa Art. 480, § 8º/CLT";
                $dados[$folha]['rescisao']['multa_480']['tipo']      = "CREDITO";
                $dados[$folha]['rescisao']['multa_480']['valor']    += $rows['a480'];
                
                
                /**
                 * MULTA 479
                 */
                $dados[$folha]['rescisao']['multa_479']['verba']      = "Multa Art. 479, § 8º/CLT";
                $dados[$folha]['rescisao']['multa_479']['tipo']       = "CREDITO";
                $dados[$folha]['rescisao']['multa_479']['valor']     += $rows['a479'];
                
                
                /**
                 * MULTA 477
                 */
                $dados[$folha]['rescisao']['multa_477']['verba']      = "Multa Art. 477, § 8º/CLT";
                $dados[$folha]['rescisao']['multa_477']['tipo']       = "CREDITO";
                $dados[$folha]['rescisao']['multa_477']['valor']     += $rows['a477'];
                
                
                /**
                 * SALARIO FAMILIA  
                 */
                $dados[$folha]['rescisao']['salario_familia']['verba']  = "SALÁRIO FAMÍLIA";
                $dados[$folha]['rescisao']['salario_familia']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['salario_familia']['valor'] += $rows['sal_familia'];
                
                
                /**
                 * DECIMO TERCEIRO
                 */
                $dados[$folha]['rescisao']['dt_salario']['verba']  = "DECIMO_TERCEIRO";
                $dados[$folha]['rescisao']['dt_salario']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['dt_salario']['valor'] += $rows['dt_salario'];
                
                
                /**
                 * FERIAS PROPORCIONAIS
                 */
                $dados[$folha]['rescisao']['ferias_pr']['verba']  = "FÉRIAS PROPORCIONAIS";
                $dados[$folha]['rescisao']['ferias_pr']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['ferias_pr']['valor'] += $rows['ferias_pr'];
                
                
                /**
                 * FERIAS VENCIDAS
                 */
                $dados[$folha]['rescisao']['ferias_vencidas']['verba']  = "FÉRIAS VENCIDAS";
                $dados[$folha]['rescisao']['ferias_vencidas']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['ferias_vencidas']['valor'] += $rows['ferias_vencidas'];
                
                                
                /**
                 * TERÇO CONSTITUCIONAL DE FERIAS
                 */
                $dados[$folha]['rescisao']['terco_cons_ferias']['verba']  = "TERÇO CONSTITUCIONAL DE FÉRIAS";
                $dados[$folha]['rescisao']['terco_cons_ferias']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['terco_cons_ferias']['valor'] += $rows['umterco_fv'];
                
                                
                /**
                 * 13° SALÁRIO AVISO PREVIO INDENIZADO
                 */
                $dados[$folha]['rescisao']['dt_aviso_indenizado']['verba']  = "13° SALÁRIO (AVISO-PRÉVIO INDENIZADO)";
                $dados[$folha]['rescisao']['dt_aviso_indenizado']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['dt_aviso_indenizado']['valor'] += $rows['terceiro_ss'];
                
                
                /**
                 * FÉRIAS AVISO PREVIO INDENIZADO 1/12
                 */
                $dados[$folha]['rescisao']['ferias_aviso_indenizado']['verba']  = "FÉRIAS (AVISO-PRÉVIO INDENIZADO 1/12 AVOS)";
                $dados[$folha]['rescisao']['ferias_aviso_indenizado']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['ferias_aviso_indenizado']['valor'] += $rows['ferias_aviso_indenizado'];
                
                
                /**
                 * FÉRIAS DOBRO
                 */
                $dados[$folha]['rescisao']['ferias_dobro']['verba']  = "FÉRIAS DOBRO";
                $dados[$folha]['rescisao']['ferias_dobro']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['ferias_dobro']['valor'] += $rows['fv_dobro'];
                
                
                /**
                 * 1/3 FÉRIAS DOBRO
                 */
                $dados[$folha]['rescisao']['um_terco_ferias_dobro']['verba']  = "1/3 FÉRIAS EM DOBRO";
                $dados[$folha]['rescisao']['um_terco_ferias_dobro']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['um_terco_ferias_dobro']['valor'] += $rows['um_terco_ferias_dobro'];
                
                
                /**
                 * 1/3 FÉRIAS (AVISO PRÉVIO INDENIZADO
                 */
                $dados[$folha]['rescisao']['umterco_ferias_aviso_indenizado']['verba']  = "1/3 FÉRIAS (AVISO PRÉVIO INDENIZADO";
                $dados[$folha]['rescisao']['umterco_ferias_aviso_indenizado']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['umterco_ferias_aviso_indenizado']['valor'] += $rows['umterco_ferias_aviso_indenizado'];
                
                                
                /**
                 * AJUSTE DE SALDO DEVEDOR
                 */
                $dados[$folha]['rescisao']['ajuste_devedor']['verba']  = "AJUSTE DE SALDO DEVEDOR";
                $dados[$folha]['rescisao']['ajuste_devedor']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['ajuste_devedor']['valor'] += $rows['arredondamento_positivo'];
                
                
                /**
                 * MOVIMENTOS DE DEBITO
                 */
                
                /**
                 * ADIANTAMENTO SALARIAL
                 */
                $dados[$folha]['rescisao']['adiantamento']['verba']  = "ADIANTAMENTO SALARIAL";
                $dados[$folha]['rescisao']['adiantamento']['tipo']   = "DEBITO";
                $dados[$folha]['rescisao']['adiantamento']['valor'] += $rows['adiantamento'];
                
                /**
                 * 12_506
                 */
                $dados[$folha]['rescisao']['lei_12_506']['verba']  = "Lei 12.506";
                $dados[$folha]['rescisao']['lei_12_506']['tipo']   = "CREDITO";
                $dados[$folha]['rescisao']['lei_12_506']['valor'] += $rows['lei_12_506'];
                
                /**
                 * MOVIMENTOS FIXOS DE DEBITO
                 */
                
                /**
                 * PREVIDÊNCIA SOCIAL
                 */
                $dados[$folha]['rescisao']['inss']['verba']  = "Previdência Social";
                $dados[$folha]['rescisao']['inss']['tipo']   = "DEBITO";
                $dados[$folha]['rescisao']['inss']['valor'] += $rows['inss_ss'];
                
                /**
                 * PREVIDÊNCIA SOCIAL 13 SALARIO
                 */
                $dados[$folha]['rescisao']['inss_dt']['verba']  = "Previdência Social - 13º Salário";
                $dados[$folha]['rescisao']['inss_dt']['tipo']   = "DEBITO";
                $dados[$folha]['rescisao']['inss_dt']['valor'] += $rows['inss_dt'];
                
                /**
                 * IRRF
                 */
                $dados[$folha]['rescisao']['irrf']['verba']  = "IRRF";
                $dados[$folha]['rescisao']['irrf']['tipo']   = "DEBITO";
                $dados[$folha]['rescisao']['irrf']['valor'] += $rows['ir_ss'];
                
                /**
                 * IRRF 13° SALARIO
                 */
                $dados[$folha]['rescisao']['irrf_dt']['verba']  = "IRRF sobre 13º Salário";
                $dados[$folha]['rescisao']['irrf_dt']['tipo']   = "DEBITO";
                $dados[$folha]['rescisao']['irrf_dt']['valor'] += $rows['ir_dt'];
                
                /**
                 * IRRF FÉRIAS
                 */
                $dados[$folha]['rescisao']['irrf_ferias']['verba']  = "IRRF Férias";
                $dados[$folha]['rescisao']['irrf_ferias']['tipo']   = "DEBITO";
                $dados[$folha]['rescisao']['irrf_ferias']['valor'] += $rows['ir_ferias'];
                
                /**
                 * AVISO PREVIO
                 */
                if($rows['fator'] == 'empregado' && $rows['aviso'] == 'PAGO pelo '){
                    $dados[$folha]['rescisao']['aviso_indenizado_empregado']['verba']  = "Aviso-Prévio Indenizado (Empregado)";
                    $dados[$folha]['rescisao']['aviso_indenizado_empregado']['tipo']   = "DEBITO";
                    $dados[$folha]['rescisao']['aviso_indenizado_empregado']['valor'] += $rows['aviso_valor'];
                }else{
                    $dados[$folha]['rescisao']['aviso_indenizado_empregador']['verba']  = "Aviso-Prévio Indenizado (Empregador)";
                    $dados[$folha]['rescisao']['aviso_indenizado_empregador']['tipo']   = "CREDITO";
                    $dados[$folha]['rescisao']['aviso_indenizado_empregador']['valor'] += $rows['aviso_valor'];
                }
                
                /**
                 * MOVIMENTOS DA RESCISÃO
                 */
                $queryMovimentoRescisao = "SELECT * FROM rh_movimentos_clt AS A WHERE 
                    A.id_clt = '{$rows['id_clt']}' AND A.mes_mov = 16 AND A.status = 1";
                $sqlMovimentoRescisao = mysql_query($queryMovimentoRescisao) or die("Erro");
                while($rowss = mysql_fetch_assoc($sqlMovimentoRescisao)){
                    $dados[$folha]['rescisao'][$rowss['id_mov']]['verba']  = $rowss['nome_movimento'];
                    $dados[$folha]['rescisao'][$rowss['id_mov']]['tipo']   = $rowss['tipo_movimento'];
                    $dados[$folha]['rescisao'][$rowss['id_mov']]['valor'] += $rowss['valor_movimento'];
                }
                
            }
            
            return $dados;
        }
    }
    
    /**
     * 
     * FEITO POE: SINESIO LUIZ
     * EM: 10/03/2016
     * EMPRESA: F71 DESENVOLVIMENTOS WEB
     * TELEFONE DE CONTATO: (21) 98594-6352
     * 
     * COMECEI DAQUI PARA BAIXO 
     * NOVOS METODOS QUE VÃO IMPACTAR DIRETAMENTE NA 
     * NOVA FOLHA DE PAGAMENTO ORIENTADA A OBJETOS
     *  
     */
    
    /**
     * SET FOLHA
     * @param type $folha
     */
    public function setFolha($folha){
        $this->folha = $folha;        
    }
    
    /**
     * GET FOLHA
     * @return type
     */
    public function getFolha(){
        return $this->folha;
    }
    
    /**
     * 
     * @param type $participantes
     */
    public function setParticipantesParaAtualizar($participantes = array()){
         $this->participantesParaAtualizar  = $participantes;
    }
    
    /**
     * 
     * @return type
     */
    public function getParticipantesParaAtualizar(){
        return $this->participantesParaAtualizar;
    }
    
    /**
     * 
     * @param type $mes
     */
    public function setMesFolha($mes){
        $this->mes = $mes;
    }
    
    /**
     * 
     * @return type
     */
    public function getMesFolha(){
        return $this->mes;
    }
    
    /**
     * 
     * @param type $ano
     */
    public function setAnoFolha($ano){
        $this->ano = $ano;
    }
    
    /**
     * 
     * @return type
     */
    public function getAnoFolha(){
        return $this->ano;
    }    
    
    /**
     * 
     * @param type $inicioFolha
     */
    public function setInicioFolha($inicioFolha){
        $this->inicioFolha = $inicioFolha;
    }
    
    /**
     * 
     * @return type
     */
    public function getInicioFolha(){
        return $this->inicioFolha;
    }
    
    /**
     * 
     * @param type $finalFolha
     */
    public function setFinalFolha($finalFolha){
        $this->finalFolha = $finalFolha;
    }
    
    /**
     * 
     */
    public function getFinalFolha(){
        return $this->finalFolha;
    }
    
    /**
     * 
     * @param type $arrayLicenca
     */
    public function setArrayLicenca($arrayLicenca){
        $this->arrayLicencas = $arrayLicenca;
    }
    
    /*
     * 
     */
    public function getArrayLicenca(){
        return $this->arrayLicencas;       
    }
    
    /**
     * 
     * @param type $arrayRescisao
     */
    public function setArrayRescisao($arrayRescisao){
        $this->arrayRescisao = $arrayRescisao;
    }
    
    /**
     * 
     */
    public function getArrayRescisao(){
       return $this->arrayRescisao; 
    }    
    
    /**
     * 
     * @param type $objeto (STD_CLASS)
     */
    public function criaFolha($objeto, $debug = false){
        $retorno = array("status" => false);
        //TRATAMENTOS 
        $data_atual  = date("Y-m-d");
        $data_inicio = date("Y-d-m", str_replace("/", "-", strtotime($objeto->setDataInicio)));
        $data_fim    = $this->ultimoDiaDoMes($objeto->setMes,$objeto->setAno);
        
        //CAMPOS
        $campos = "parte,data_proc,mes,ano,data_inicio,data_fim,regiao,projeto,terceiro,tipo_terceiro,user,data_ultima_atualizacao,status";
        $query = "INSERT INTO rh_folha ({$campos}) VALUES ('1',
                                                           '{$data_atual}',
                                                           '{$objeto->setMes}',
                                                           '{$objeto->setAno}',
                                                           '{$data_inicio}',
                                                           '{$data_fim}',
                                                           '{$objeto->setRegiao}',
                                                           '{$objeto->setProjeto}',
                                                           '{$objeto->setTerceiro}',
                                                           '{$objeto->setTipoDecimo}',
                                                           '{$_COOKIE['logado']}',
                                                           '{$objeto->setDataUltimaAtualizacao}',    
                                                           '2')";
                                                           
        if($debug){
            $this->getDebug($query);
            exit();
        }
                                                         
        try{
          //EXECUTA A QUERY
          $sql = mysql_query($query) or die("ERRO AO ABRIR FOLHA");                                                     
          
          //RECUPERA O ULTIMO ID_FOLHA
          $folha = mysql_insert_id();
          
          ////CRIA LOG DE CRIAÇÃO DE FOLHA
          $this->logCriarFolha($folha, $objeto->user);
            
          $retorno = array("status" => true, "ultimoId" => $folha);
          
        }catch(Exception $e){
           echo $e->getMessage(); 
        }        
        
        return $retorno;
        
    }
    
    /**
     * METODO PARA DESATIVAR EM RH_FOLHA COLOCANDO STATUS PARA 0
     * @param type $folha
     */
    public function desativaFolha($folha, $projeto = null){
        
        $retorno = false;
        
        if(isset($projeto) && !empty($projeto)){
            $queryUpdateDataUltimaAtualizacao = "UPDATE rh_clt SET data_ultima_atualizacao  = '' WHERE id_projeto = '{$projeto}'";
            mysql_query($queryUpdateDataUltimaAtualizacao) or die("Erro ao zerar datas");
        }
        
        $queryExcluirFolha = "UPDATE rh_folha SET status = 0 WHERE id_folha = '{$folha}'";
        $sqlExcluirFolha = mysql_query($queryExcluirFolha) or die("Erro ao desativar Folha");
        if($sqlExcluirFolha){
            if($this->desativaParticipantesFolhaProc($folha)){
                $retorno = true;
            }
        }
        
        return $retorno;
    }
    
    /**
     * METODO PARA DESPROCESSAR A FOLHA
     */
    public function desprocessaFolha($folha){
        $retorno = false;
        
        $queryExcluirFolha = "UPDATE rh_folha SET status = 2 WHERE id_folha = '{$folha}'";
        $sqlExcluirFolha = mysql_query($queryExcluirFolha) or die("Erro ao desativar Folha");
        if($sqlExcluirFolha){
            if($this->desprocessaParticipantesFolhaProcPara($folha)){
                $retorno = true;
            }
        }
        
        return $retorno;
    }
    
    
    /**
     * METODO PARA DESATIVAR EM RH_FOLHA_PROC COLOCANDO STATUS PARA 0
     * @param type $folha
     */
    public function desativaParticipantesFolhaProc($folha){
        $retorno = false;
        
        $queryExcluirFolha = "UPDATE rh_folha_proc SET status = 0 WHERE id_folha = '{$folha}'";
        $sqlExcluirFolha = mysql_query($queryExcluirFolha) or die("Erro ao desativar Folha");
        if($sqlExcluirFolha){
            $retorno = true;
        }
        
        return $retorno;
    }
    
    /**
     * METODO PARA DESPROCESSAR EM RH_FOLHA_PROC COLOCANDO STATUS PARA 1
     * @param type $folha
     */
    public function desprocessaParticipantesFolhaProcPara($folha){
        $retorno = false;
        
        $queryExcluirFolha = "UPDATE rh_folha_proc SET status = 1 WHERE id_folha = '{$folha}'";
        $sqlExcluirFolha = mysql_query($queryExcluirFolha) or die("Erro ao desativar Folha");
        if($sqlExcluirFolha){
            $retorno = true;
        }
        
        return $retorno;
    }
    
    /**
     * METODO QUE VAI GRAVAR OS PARTICIPANTES 
     * NA FOLHA PROC 
     */
    public function insertParticipantesFolha($clt,$mes,$ano,$debug = false){
        
        /**
         * INICIONADO VARIAVEIS
         */
        $dataAtual   = date("Y-m-d");
        $retortno    = false;
        $logado      = $_COOKIE['logado'];
        $mes         = str_pad($mes, 2 , "0", STR_PAD_LEFT);
        $ultimaFolha = $this->getFolha();
        
        /**
         * CAMPOS QUE VEM DO $clt QUE VEM DO PARAMETRO
         * CONCATENADO COM OS VALORES DO $campoExtra
         */
        //print_r($clt);
        $campos = array("id_clt","id_projeto","id_regiao","nome","id_curso","tipo_pagamento","rh_horario","status_real_time","id_folha","data_proc","user_proc","mes","ano");
        
        /**
         * CAMPOS NO FOLHA_PROC 
         */
        $campos_rh_folha_proc = array("id_clt","id_projeto","id_regiao","nome","id_curso","tipo_pg","id_horario","status_clt","id_folha","data_proc","user_proc","mes","ano","status");
        
        /**
         * UTILIZEI ESSE ARRAY PARA MANDAR PARA MONTASTRING 
         * INFORMAÇÕES QUE NÃO TENHO NO rh_clt E QUE SÃO 
         * IGUIS PARA TODOS OS PARTICIPANTES       
         */
        $camposExtra = array($ultimaFolha,$dataAtual,$_COOKIE['logado'],$mes,$ano,2);
        
        /**
         * ESSE METODO VAI MONTAR OS VALUES QUE SERÃO INSERIDOS
         * no rh_folha_proc AFIM DE RETORNAR UMA UNICA STRING
         */
        $valuesRows = $this->montaString($campos,$clt,$camposExtra);
        
        /**
         * AQUI COMEÇA DE FATO O INSERT NO rh_folha_proc
         */
        $query = "INSERT INTO rh_folha_proc (".  implode(",", $campos_rh_folha_proc). ") VALUES {$valuesRows}";
                          
        if($debug){
            $this->getDebug($query);
        }
        
        if(mysql_query($query)){
            
            /**
             * METODO DE CALCULO QUE É 
             * CHAMADO A PRIMEIRA VEZ 
             * QUANDO A FOLHA É CRIADA
             */
            $participantes = $this->atualizarTodosNaFolha();
            $this->calculoFolha(true);            
            
            $retortno = true;
        }
        
        return $retortno;
    }
    
    /**
     * NOVA FOLHA
     * METODO RETORNA TODOS OS PARTICIPANTES EM RH_FOLHA_PROC POR FOLHA
     * @param type $folha
     */
    public function getParticipantesPorFolha(){
        
        $dados = array();
        $folha = $this->getFolha();
        
        $query = "SELECT A.id_clt, A.nome,  DATE_FORMAT(B.data_entrada,'%d/%m/%Y') as dataEntrada, A.status_clt,
                        A.dias_trab, A.meses, A.salbase, A.sallimpo, A.rend, A.desco, A.inss, A.imprenda, A.salfamilia, 
                        A.salliquido, A.base_inss, CONCAT((A.t_inss * 100),'%') as aliquota, A.imprenda, CONCAT(ROUND(A.t_imprenda * 100,1),'%') AS t_imprenda,
                        A.d_imprenda, A.qnt_dependente_irrf, A.valor_deducao_dep_ir_total, A.valor_deducao_dep_ir_fixo, A.base_irrf, A.novo_em_folha, A.possui_faltas
                    FROM rh_folha_proc AS A 
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                    WHERE A.id_folha = '{$folha}' 
                    ORDER BY A.nome";
                    
        $sql = mysql_query($query) or die("Erro ao selecionar participantes da folha");            
        if(mysql_num_rows($sql) > 0){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[] = $rows;
            }
        }
        
        return $dados;
    }
    
    /**
     * NOVA FOLHA
     * METODO QUE VERIFICA QUANTIDADE DE PARTICIPANTES 
     * NA FOLHA ABERTA OU FECHADA
     * @param type $id_folha
     */
    public function verificaParticipantesNoFolhaProc($folha){
        $retorno = 0;
        $query = "SELECT COUNT(A.id_clt) as total FROM rh_folha_proc AS A 
                    WHERE A.id_folha = '{$folha}' GROUP BY A.id_folha";    
                          
        $sql = mysql_query($query) or die("Erro ao verificar rh_folha_proc");            
        if(mysql_num_rows($sql) > 0){
            $total = mysql_fetch_assoc($sql);
        }
        
        $retorno = $total['total'];
        
        return $retorno;
    }
    
    /**
     * NOVA FOLHA
     * ESSE METODO APENAS TRAZ O TOTAL DE PARTICIPANTES
     * GRAVADO NO rh_folha_proc
     * @param type $id_folha
     */
    public function verificaTotalGravadoEmFolha($folha){
        $retorno = 0;
        $query = "SELECT A.clts FROM rh_folha as A  
                    WHERE A.id_folha = '{$folha}'";
         
        $sql = mysql_query($query) or die("Erro ao verificar rh_folha");
        $qnt = mysql_fetch_assoc($sql);
        if(!empty($qnt['clts']) && $qnt['clts'] > 0){
            $retorno = $qnt['clts'];
        }
        
        return $retorno;
    }
    
    /**
     * ESSE METODO VERIFICA SE A DATA GRAVADA 
     * NO CADASTRO DO CLT NO CAMPO data_ultima_atualizacao
     * É MAIOR QUE A DATA GRAVADA NO RH_FOLHA
     * @param type $folha
     */
    public function participantesParaAtualizarNaFolha(){
        $folha = $this->getFolha();
        
        $participantes = array();
        $novaData = "";
        $queryVerifica = "SELECT A.id_clt, A.nome, A.data_entrada, B.salbase, B.sallimpo,B.rend,B.desco,B.inss,B.imprenda,B.salfamilia, 
                                 A.data_ultima_atualizacao, (B.sallimpo + B.rend - B.desco - B.inss - B.imprenda + B.salfamilia) as total,
                                 A.status as status_atual_clt
                            FROM rh_clt AS A
                            LEFT JOIN rh_folha_proc AS B ON(A.id_clt = B.id_clt)
                            LEFT JOIN rh_folha AS C ON(B.id_folha = C.id_folha)
                            WHERE A.data_ultima_atualizacao > C.data_ultima_atualizacao AND C.id_folha = '{$folha}'
                            ORDER BY A.data_ultima_atualizacao";

        $sqlVerifica = mysql_query($queryVerifica) or die("Erro ao selecionar participantes com datas maior que data gravada no rh_folha");
        if(mysql_num_rows($sqlVerifica) > 0){
            while($rows = mysql_fetch_assoc($sqlVerifica)){
                $participantes[$rows['id_clt']] = array("nome" => $rows['nome'], "data_entrada" => $rows['data_entrada'], "ultimaAtualizacao" => $rows['data_ultima_atualizacao'], "status_atual_clt" =>  $rows['status_atual_clt']);
                /**
                 * ESSA VARIÁVEL VAI SAIR DO WHILE SEMPRE COM A MAIOR
                 * DATA VINDA NO RESULTADO, POIS A MESMA VAI SER
                 * GRAVADA NO rh_folha.
                 */
                $novaData = $rows['data_ultima_atualizacao'];
            }
            
            /**
             * GRAVANDO DATA DE ULTIMA ATUALIZAÇÃO EM rh_folha
             */
            //$this->updateDataUltimaAtualizacao($folha, $novaData);
            
            /**
            * PARTICIPANTES PARA ATUALIZAR
            */
            $this->setParticipantesParaAtualizar($participantes);
            
        }
        
        return $participantes;
    }
    
    /**
     * ESSE METODO ATUALIZA TODOS OS CLT
     * SÓ É CHAMADO QUANDO CRIA FOLHA
     * @param type $folha
     */
    public function atualizarTodosNaFolha(){
        
        $folha = $this->getFolha();
        $participantes = array();
        $novaData = "";
        $queryVerifica = "SELECT A.nome, A.id_clt, A.data_entrada, C.data_ultima_atualizacao, A.status as status_atual_clt
                            FROM rh_clt AS A
                            LEFT JOIN rh_folha_proc AS B ON(A.id_clt = B.id_clt)
                            LEFT JOIN rh_folha AS C ON(B.id_folha = C.id_folha)
                            WHERE C.id_folha = '{$folha}'";

        $sqlVerifica = mysql_query($queryVerifica) or die("Erro ao selecionar participantes com datas maior que data gravada no rh_folha");
        if(mysql_num_rows($sqlVerifica) > 0){
            while($rows = mysql_fetch_assoc($sqlVerifica)){
                $participantes[$rows['id_clt']] = array("nome" => $rows['nome'], "data_entrada" => $rows['data_entrada'], "status_atual_clt" =>  $rows['status_atual_clt']);
            }
        }
        
        /**
        * PARTICIPANTES PARA ATUALIZAR
        */
        $this->setParticipantesParaAtualizar($participantes);
        
        return $participantes;
    }
        
    /**
     * METODO PARA ATUALIZAR O CAMPO data_ultima_atualizacao EM RH_FOLHA
     * @param type $folha
     * @param type $novaData
     */
    public function updateDataUltimaAtualizacao($folha, $novaData){
        $retorno = false;
        
        if(!empty($novaData)){
            $queryAtualiza = "UPDATE rh_folha
                                SET data_ultima_atualizacao = '{$novaData}' 
                                WHERE id_folha = '{$folha}'";
            $sqlAtualiza = mysql_query($queryAtualiza) or die("Erro ao atualizar ultima data de atualização em rh_folha");
            if($sqlAtualiza){
                $retorno = true;
            }
        }
        
        return $retorno;
    }
    
    /**
     * NOVA FOLHA
     * METODO PARA ATUALIZAR QUANTIDADE DE 
     * PARTICIPANTES NA FOLHA ABERTA
     * @param type $id_folha
     */
    public function atualizaQntClt(){
        
        $retorno = 0;
        $folha = $this->getFolha();
        
        $totalCltEmFolhaProc = $this->verificaParticipantesNoFolhaProc($folha);
        $totalGravadoEmFolha = $this->verificaTotalGravadoEmFolha($folha);
        if($totalGravadoEmFolha != $totalCltEmFolhaProc){
            $queryUpdate = "UPDATE rh_folha SET clts = '{$totalCltEmFolhaProc}' 
                                WHERE id_folha = '{$folha}'";
            $sqlUpdate = mysql_query($queryUpdate) or die("rro ao executar update");                    
            if($sqlUpdate){
               $retorno = 1; 
            }
        }
        
        return $retorno;
    }
    
    /**
     * NOVA FOLHA
     * METODO A FOLHA RH_FOLHA
     * @param type $id_folha
     * @param type $tipoRetorno 0 => resorce, 1 => array
     * @return type
     */
    public function getFolhaById($folha = null, $campos = array('*'),$tipoRetorno = 0){
        
        if($folha == null){
            $folha = $this->getFolha();
        }
        
        if(is_array($folha) && !empty($folha)){
            $id_folha = implode(",", $folha);
        }else{
            $f = array($folha);
            $id_folha = implode(",", $f);
        }
        
        $query = "SELECT " . implode(",", $campos) . " 
            FROM rh_folha AS A 
            LEFT JOIN projeto AS B ON(A.projeto = B.id_projeto)
            LEFT JOIN funcionario AS C ON(A.user = C.id_funcionario)
            WHERE id_folha IN({$id_folha})";
                                  
        $sql = mysql_query($query) or die("Erro ao selecionar folha");
        
        if($tipoRetorno == 0){
            return $sql;
        }else{
            $array = array();
            while($rows = mysql_fetch_assoc($sql)){
                $array = $rows;
            }
            return $array;
        }       
        
    }
    
    /**
     * METODO PRINCIPAL DA NOVA FOLHA,
     * ESSE VAI CHAMAR TODOS OS OUTROS
     * METODOS DE CALCULOS
     * @param type $participantesParaAtualizar
     */
    public function calculoFolha($atualizaTudo = false){
        /**
         * ATUALIZANDO SALARIO BASE
         */
        $this->updateSalarioBase($atualizaTudo);
        
        /**
         * ATUALIZANDO DIAS TRABALHADOS
         */
        $this->updateDiasTrabalhados($atualizaTudo);                          
        
        /**
         * ATUALIZANDO TOTAL DE 
         * CREDITO E DEBITO
         */
        $this->updateMovimentos($atualizaTudo);
        
        /**
         * ATUALIZNADO INSS
         */
        $this->updateInss($atualizaTudo);
        
        /**
         * ATUALIZANDO IRRF
         */
        $this->updateIrrf($atualizaTudo);
        
        /**
         * ATUALIZANDO LIQUIDO
         */
        $this->updateSalarioLiquido($atualizaTudo);
        
        /**
         * FLAGANDO NOVO CLT 
         */
        $this->updateNovoClt($atualizaTudo);
        
        /**
         * ATUALIZANDO STATUS DO 
         * CLT NA FOLHA PROC
         */
        $this->updateNovoStatusEmFolha($atualizaTudo);
        
        /**
        *FLAGANDO CLT COM FALTAS
        */
        $this->updateFaltasClt($atualizaTudo);
        
        /**
        * PARTICIPANTES PARA ATUALIZAR
        */
        $obj = $this->getParticipantesParaAtualizar();
        $ultimaAtualizacao = "";
        foreach ($obj as $k => $v){
            $ultimaAtualizacao = $v['ultimaAtualizacao'];
        }        
        
        /**
         * ATUALIZANDO
         */                          
        $this->updateDataUltimaAtualizacao($this->getFolha(), $ultimaAtualizacao);
    }
    
    /**
     * METODO QUE ATUALIZA O SALARIO BASE DOS 
     * CLTS NO CAMPO salBase do rh_folha_proc
     * @param type $clts
     */
    public function updateSalarioBase($atualizaTudo = false){
        $retorno = false;
        $participantes = array();
        $folha = $this->getFolha();
        
        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        }
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */
        
        $clts = $this->getParticipantesParaAtualizar();
        
        if(!empty($clts)){
            foreach ($clts as $ids => $dados){
                $participantes[] = $ids;
            }             
                        
            try{
                /**
                 * ARRAY DE SALARIO PARA FAZER UPDATE
                 */
                $clts = $this->getSalarioBase($participantes);
                foreach ($clts as $keys => $values){
                    $query = "UPDATE rh_folha_proc SET salBase = '{$values}' 
                                WHERE id_folha = '{$folha}' AND id_clt  = '{$keys}'";
                    $sql = mysql_query($query) or die("Erro ao atualizar salario Base");            
                    
                }
                
            }catch(Exception $e){
                echo $e->getMessage("ERRO EM getSalarioBase"); 
            }  
        }
        
        return $retorno;
    }
    
    /*
     * ARRAY DE SALARIO BASE
     */
    public function getSalarioBase($clts = array()){
        $participantes = array();
        if(!empty($clts)){
            $querySalBase = "SELECT A.id_clt, B.salario FROM rh_clt AS A 
                        LEFT JOIN curso AS B ON(A.id_curso  = B.id_curso)
                        WHERE A.id_clt IN(" .  implode(",", $clts). ");";
            $sqlSalBase = mysql_query($querySalBase) or die("Erro ao selecionar salario base");
            while($rows = mysql_fetch_assoc($sqlSalBase)){
                $participantes[$rows['id_clt']] = $rows['salario'];
            }
        }
        
        return $participantes;
    }
    
    /**
     * DIAS TRABALHADOS
     */
    public function updateDiasTrabalhados($atualizaTudo = false){
        
        /**
         * INCLUDES 
         */
        include_once("EventoClass.php");
        
        /**
         * INSTANCIAS
         */
        $evento = new Eventos();
        
        /**
         * 
         */
        $retorno        = false;
        $folha          = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        $inicioFolha    = $this->getInicioFolha();
        $finalFolha     = $this->getFinalFolha();
        $mes_referente  = $anoCompetencia . "-" . $mesCompetencia;
        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        }
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */
        //$participantesParaAtualizar[183] = array("nome" => "ADAISE DOS SANTOS", "data_entrada" => "2016-06-05", "status_atual_clt" => "61");
        if(!empty($participantesParaAtualizar)){
                       
            try{
                
               
               foreach ($participantesParaAtualizar as $ids => $dados){
                   
                   $base = 0;
                   $dias = $this->getDiasTrabalhados($inicioFolha, $finalFolha, $dados['data_entrada']);
                   
                   /**
                    * AQUI VOU VERIFICAR TUDO QUE PODE 
                    * ALTERAR OS DIAS TRABALHADOS DO CLT
                    */
                    $dadosEventos = $evento->validaEventoForFolha($ids, $mes_referente, $inicioFolha, $finalFolha);
                    $dias = $dias - $dadosEventos['dias_evento'];
                    
                    
                    /**
                     * VERIFICANDO CASOS QUE É NECESSÁRIO 
                     * ZERAR OS DIAS TRABALHADOS 
                     * COMO POR EXEMPLO - RESCISAO
                     */
                    if(in_array($dados['status_atual_clt'],$this->getArrayRescisao())){
                        $dias = 0;
                    }
                                                             
                   /**
                    * VALOR DO SALARIO CONTRATUAL
                    */
                    $salario = $this->getSalarioBase(array($ids));    
                    
                    /**
                     * CALCULO DE SALARIO BASE      
                     */
                    $base  = $this->salarioBase($salario[$ids], $dias); 
                
                    $query = "UPDATE rh_folha_proc SET dias_trab = '{$dias}', sallimpo = '{$base}' 
                                WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}'";
                    $sql = mysql_query($query) or die("Erro ao atualizar salario Base");            
                    if($sql){
                        $retorno = true;
                    }
                }   
                
            }catch(Exception $e){
                echo $e->getMessage("ERRO EM getSalarioBase"); 
            }  
        }
                          
        return $retorno;
    }    
    
    /**
     * METODO PARA ATUALIZAR RENDIMENTOS E DESCONTOS
     * @param type $folha
     * @param type $participantesParaAtualizar
     */
    public function updateMovimentos(){
        $retorno = false;
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */
        
        if(!empty($participantesParaAtualizar)){
                       
            try{
               foreach ($participantesParaAtualizar as $ids => $dados){
                    $query = "SELECT A.categoria,SUM(IFNULL(B.valor_movimento,0)) AS total
                                FROM rh_movimentos AS A
                                LEFT JOIN rh_movimentos_clt AS B ON(A.id_mov = B.id_mov AND B.id_clt = '{$ids}' AND B.`status` = 1 AND B.mes_mov = {$mesCompetencia} AND B.ano_mov = {$anoCompetencia})
                                LEFT JOIN rh_folha_proc AS C ON(B.id_clt = C.id_clt AND B.mes_mov = C.mes AND B.ano_mov = C.ano AND C.id_folha = '{$folha}')
                                WHERE A.categoria IN('CREDITO','DEBITO') 
                            GROUP BY A.categoria";
                   
                    $sql = mysql_query($query) or die("Erro ao selecionar rendimentos e descontos");       
                    
                    while($rows = mysql_fetch_assoc($sql)){
                        /**
                         * ATUALIZANDO TOTAL DE RENDIMENTO EM rh_folha_proc
                         */
                        if($rows['categoria'] == "CREDITO"){
                            if($rows['total'] != 0.00){
                                $qryUpdate = "UPDATE rh_folha_proc SET rend = '{$rows['total']}' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}';";
                            }else{
                                $qryUpdate = "UPDATE rh_folha_proc SET rend = '0.00' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}';";
                            }                           
                            
                            mysql_query($qryUpdate) or die("Erro ao fazer update de rendimento ou desconto");
                        }
                        
                        /**
                         * ATUALIZANDO TOTAL DE DESCONTO EM rh_folha_proc
                         */
                        if($rows['categoria'] == "DEBITO"){
                            if($rows['total'] != 0.00){
                                $qryUpdate = "UPDATE rh_folha_proc SET desco = '{$rows['total']}' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}';";
                            }else{
                                $qryUpdate = "UPDATE rh_folha_proc SET desco = '0.00' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}';";
                            }
                            
                            mysql_query($qryUpdate) or die("Erro ao fazer update de rendimento ou desconto");                            
                        }
                    }
                    
               }
               
               $retorno = true;
            }catch(Exception $e){
                echo $e->getMessage("ERRO EM getSalarioBase"); 
            } 
        }
        
        return $retorno;
        
    }
    
    /**
     * 
     * @param type $folha
     * @param type $participantesParaAtualizar
     */
    public function updateInss($atualizaTudo = false){
        $retorno = false;
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        
        /**
         * CARREGANDO A TABELA DE IMPOSTOS
         */
        $this->CarregaTabelas($anoCompetencia);        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        }                          
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */
        
        if(!empty($participantesParaAtualizar)){
            try{
               foreach ($participantesParaAtualizar as $ids => $dados){
                   
                    /**
                    * CALCULANDO BASE DE INSS 
                    */
                    $base_inss = $this->calculaBaseINSS($ids);
                          
                    /**
                     * UPDATE NA BASE DE INSS DO CLT 
                     */
                    $this->updateBaseInss($ids, $base_inss);
                    $inss = $this->getCalcInss($base_inss, 2);
                    
                    $query = "UPDATE rh_folha_proc SET inss = '{$inss['valor_inss']}', t_inss = '{$inss['percentual']}' WHERE id_clt = '{$ids}' AND id_folha = '{$folha}'";
                                              
                    $sql = mysql_query($query) or die("Erro ao atualizar INSS");
                    
               }
               $retorno = true;
            }catch(Exception $e){
                echo $e->getMessage("ERRO EM getSalarioBase"); 
            } 
        }
        
        return $retorno;
    }
    
    /**
     * ATUALIZANDO BASE DE INSS 
     * DO CLT 
     * @param type $id_clt
     * @param type $folha
     */
    public function updateBaseInss($id_clt, $valor){
        $retorno = false;
        $folha = $this->getFolha();
        
        $query = "UPDATE rh_folha_proc SET base_inss  = '{$valor}' WHERE id_clt = '{$id_clt}' AND id_folha = '{$folha}'";
        $sql = mysql_query($query) or die("erro ao atualizar base de inss");
        
        if($sql){
            $retorno = true;
        }
        
        return $retorno;
    }
    
    /**
     * METODO QUE RETORNA O INSS 
     * DO CLT EM QUESTÃO
     * @param type $clt
     */
    public function getInss($clt){
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        $inss = 0;
        /**
         *   
         */
        $query = "SELECT inss
                    FROM rh_folha_proc AS A
                    WHERE A.id_clt = '{$clt}' AND A.`status` = 2
                    AND A.id_folha = '{$folha}'";
        
        $sql = mysql_query($query) or die("Erro para mostrar INSS");            
        while($rows = mysql_fetch_assoc($sql)){
            $inss = $rows['inss'];
        }
        
        return $inss;
    }

    /**
     * 
     * @param type $folha
     * @param type $participantesParaAtualizar
     */
    public function updateIrrf($atualizaTudo = false){
        $retorno = false;
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        
        /**
         * CARREGANDO A TABELA DE IMPOSTOS
         */
        $this->CarregaTabelas($anoCompetencia);        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        }  
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */        
        if(!empty($participantesParaAtualizar)){
            try{
                foreach ($participantesParaAtualizar as $ids => $dados){
                          
                    /**
                     * METODO QUE BUSCA INSS GRAVADO
                     * NO RH_FOLHA_PROC DO CLT 
                     */      
                    $inssDescontado = $this->getInss($ids);
                   
                    /**
                    * CALCULANDO BASE DE IRRF 
                    */
                    $base_irrf = $this->calculaBaseIRRF($ids, $inssDescontado);
                          
                    /**
                     * UPDATE NA BASE DE INSS DO CLT 
                     */
                    $irrf = $this->getCalcIrrf($base_irrf, $ids, 2);
                    
                    $query = "UPDATE rh_folha_proc SET imprenda = '{$irrf['valor_irrf']}', 
                                    t_imprenda = '{$irrf['percentual_irrf']}', 
                                    d_imprenda = '{$irrf['valor_parcela_deducao_irrf']}', 
                                    base_irrf = '{$irrf['base_calculo_irrf']}',
                                    qnt_dependente_irrf = '{$irrf['qnt_dependente_irrf']}',
                                    valor_deducao_dep_ir_fixo = '{$irrf['valor_deducao_dep_ir_fixo']}',
                                    valor_deducao_dep_ir_total = '{$irrf['valor_deducao_dep_ir_total']}'    
                                    WHERE id_clt = '{$ids}' AND id_folha = '{$folha}'";
                    
                    $sql = mysql_query($query) or die("Erro ao atualizar IRRF");
                    
                }
                
                $retorno = true;
                
            }catch(Exception $e){
                echo $e->getMessage("ERRO EM getSalarioBase"); 
            } 
        }
        
        return $retorno;
        
    }
    
    /**
     * ATUALIZANDO BASE DE INSS 
     * DO CLT 
     * @param type $id_clt
     * @param type $folha
     */
    public function updateBaseIrrf($id_clt, $valor){
        $retorno = false;
        $folha = $this->getFolha();
        
        $query = "UPDATE rh_folha_proc SET base_inss  = '{$valor}' WHERE id_clt = '{$id_clt}' AND id_folha = '{$folha}'";
        $sql = mysql_query($query) or die("erro ao atualizar base de inss");
        
        if($sql){
            $retorno = true;
        }
        
        return $retorno;
    }
    
    /**
     * 
     * @param type $folha
     * @param type $participantesParaAtualizar
     */
    public function updateSalarioLiquido($atualizaTudo = false){
        $retorno = false;
        $folha = $this->getFolha();
        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        } 
        
        if(!empty($participantesParaAtualizar)){
            try{
                foreach ($participantesParaAtualizar as $ids => $dados){
                    $queryUpdateLiquido = "SELECT A.id_clt, A.nome, B.salbase, B.sallimpo,B.rend,B.desco,B.inss,B.imprenda,B.salfamilia,
                                                (B.sallimpo + B.rend - B.desco - B.inss - B.imprenda + B.salfamilia) as total
                                                FROM rh_clt AS A
                                                LEFT JOIN rh_folha_proc AS B ON(A.id_clt = B.id_clt)
                                                LEFT JOIN rh_folha AS C ON(B.id_folha = C.id_folha)
                                                WHERE A.id_clt = '{$ids}' AND C.id_folha = '{$folha}'  
                                                ORDER BY A.nome";
                    $sqlUpdateLiquido   = mysql_query($queryUpdateLiquido) or die("Erro ao selecionar participantes");            
                    while($rows = mysql_fetch_assoc($sqlUpdateLiquido)){
                        $qryUpdate = "UPDATE rh_folha_proc SET salliquido = '{$rows['total']}' WHERE id_folha = '{$folha}' AND id_clt  = '{$rows['id_clt']}'";
                        mysql_query($qryUpdate) or die("Erro ao fazer update em folha_proc");

                    }                
                }
                
                $retorno = true;

            }  catch (Exception $e){
                echo $e->getMessage();
            }
        }
        
        return $retorno;
    }
    
    /**
     * ATUALIZANDO CAMPO NOVO EM FOLHA
     * SE A DATA DE ENTRADA DO CLT FOR 
     * IGUAL A DATA DE INICIO DA FOLHA
     * @param type $id_clt
     * @param type $dataAdmissao
     */
    public function updateNovoClt($atualizaTudo = false){
        $retorno = false;
        $folha = $this->getFolha();
        $inicioFolha = $this->getInicioFolha();
        $finalFolha = $this->getFinalFolha();
        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        } 
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */ 
        if(!empty($participantesParaAtualizar)){
            try{
                foreach ($participantesParaAtualizar as $ids => $dados){
                    $competenciaEntrada = date("m-Y",str_replace("/", "-", strtotime($dados['data_entrada'])));
                    $competenciaInicioFolha = date("m-Y",str_replace("/", "-", strtotime($inicioFolha)));
                    
                    if($competenciaEntrada == $competenciaInicioFolha){
                        $qryUpdate = "UPDATE rh_folha_proc SET novo_em_folha = '1' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}'";
                        mysql_query($qryUpdate) or die("Erro ao fazer update em folha_proc");
                    }
                }
                $retorno = true;
            }  catch (Exception $e){
                echo $e->getMessage();
            }
        }
        
        return $retorno;
        
    }
    
    /**
     * METODO PARA ATUALIZAR A LINHA
     * DO RH_FOLHA_PROC QUANDO O CLT
     * SOFRER ALGUM TIPO DE ATUALIZAÇÃO
     * @param type $atualizaTudo
     */
    public function updateNovoStatusEmFolha($atualizaTudo = false){
        $retorno = false;
        $folha = $this->getFolha();
        $inicioFolha = $this->getInicioFolha();
        $finalFolha = $this->getFinalFolha();
        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        } 
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */ 
        if(!empty($participantesParaAtualizar)){
            try{
                foreach ($participantesParaAtualizar as $ids => $dados){
                    $qryUpdate = "UPDATE rh_folha_proc SET status_clt = '{$dados['status_atual_clt']}' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}'";
                    mysql_query($qryUpdate) or die("Erro ao fazer update em folha_proc");
                }
                
                $retorno = true;
            }  catch (Exception $e){
                echo $e->getMessage();
            }
        }
        
        return $retorno;
    }
    
    /**
     * METODO QUE TRAZ TODOS 
     * OS TIPOS DE LICENÇA
     * @param type $atualizaTudo
     */
    public function getStatusLicenca() {
        $dados = array();
        try {
            $query = "SELECT codigo FROM rhstatus AS A WHERE A.tipo = 'licenca'";
            $sql = mysql_query($query) or die("Erro ao selecionar codigo em rhstatus");
            
            while($rows = mysql_fetch_assoc($sql)){
                $dados[] = $rows['codigo'];
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        return $dados;
    }
    
    /**
     * METODO QUE TRAZ TODOS 
     * OS TIPOS DE RESCISAO
     * @param type $atualizaTudo
     */
    public function getStatusRescisao() {
        $dados = array();
        try {
            $query = "SELECT codigo FROM rhstatus AS A WHERE A.tipo = 'recisao'";
            $sql = mysql_query($query) or die("Erro ao selecionar codigo em rhstatus");
            
            while($rows = mysql_fetch_assoc($sql)){
                $dados[] = $rows['codigo'];
            }
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        return $dados;
    }
    
    /**
     * UPDATE NO CAMPO POSSUI_FALTAS
     * @param type $atualizaTudo
     * @return type
     */
    public function updateFaltasClt($atualizaTudo = false){
        $retorno = false;
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        
        if($atualizaTudo){
            $participantesParaAtualizar = $this->atualizarTodosNaFolha();
        }else{
            $participantesParaAtualizar = $this->getParticipantesParaAtualizar();
        } 
        
        /**
         * COPULANDO ARRAY PARA TESTE
         * $participantesParaAtualizar[10] = array("nome" => "ALEXANDRE GARCIA D AUREA", "data_entrada" => "2016-06-05");
         */ 
        if(!empty($participantesParaAtualizar)){
            try{
                foreach ($participantesParaAtualizar as $ids => $dados){
                    $queryVerificaFaltas = "SELECT A.valor_movimento
                                            FROM rh_movimentos_clt AS A
                                            WHERE A.id_clt = '{$ids}' AND A.`status` = 1 AND A.id_mov IN(232)
                                            AND A.mes_mov = '{$mesCompetencia}' AND A.ano_mov = '{$anoCompetencia}'";
                    $sqlVerificaFaltas = mysql_query($queryVerificaFaltas); 
                    if(mysql_num_rows($sqlVerificaFaltas) > 0){
                        $qryUpdate = "UPDATE rh_folha_proc SET possui_faltas = '1' WHERE id_folha = '{$folha}' AND id_clt  = '{$ids}'";
                        mysql_query($qryUpdate) or die("Erro ao fazer update em folha_proc");
                    }
                }
                
                $retorno = true;
                
            }  catch (Exception $e){
                echo $e->getMessage();
            }
        }
        
        return $retorno;
    }
    
    /**
     * VERIFICA BASE PARA CALCULO
     * INSS, IRRF, FGTS
     */
    public function calculaBaseINSS($id_clt){
        
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        $base = $this->getSalLimpo($id_clt);
        
        
        $query = "SELECT A.mes_mov, A.id_mov, A.ano_mov, A.tipo_movimento,
                if((A.tipo_movimento = 'CREDITO'), SUM(A.valor_movimento),0) as credito,
                if((A.tipo_movimento = 'DEBITO'), SUM(A.valor_movimento),0) as debito		 
        FROM rh_movimentos_clt AS A
        LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
        WHERE A.mes_mov = {$mesCompetencia} AND A.ano_mov = {$anoCompetencia} 
        AND A.id_clt = '{$id_clt}' AND B.incidencia_inss = 1 AND A.`status` = 1 
        GROUP BY A.tipo_movimento";
        
        $sql = mysql_query($query) or die("Erro ao selecionar Movimentos para base de INSS");
        while($rows = mysql_fetch_assoc($sql)){ 
            if($rows['tipo_movimento'] == "CREDITO"){
                $base += $rows['credito'];
            }
            
            if($rows['tipo_movimento'] == "DEBITO"){
                $base -= $rows['debito'];
            }
        }    
                    
        return $base; 
                            
    }
    
    /**
     * VERIFICA BASE PARA CALCULO
     * INSS, IRRF, FGTS
     */
    public function calculaBaseIRRF($id_clt, $inssDescontado){
        
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        $base = $this->getSalLimpo($id_clt);
        
        
        $query = "SELECT A.mes_mov, A.id_mov, A.ano_mov, A.tipo_movimento,
                if((A.tipo_movimento = 'CREDITO'), SUM(A.valor_movimento),0) as credito,
                if((A.tipo_movimento = 'DEBITO'), SUM(A.valor_movimento),0) as debito		 
        FROM rh_movimentos_clt AS A
        LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
        WHERE A.mes_mov = {$mesCompetencia} AND A.ano_mov = {$anoCompetencia} 
        AND A.id_clt = '{$id_clt}' AND B.incidencia_irrf = 1 AND A.`status` = 1 
        GROUP BY A.tipo_movimento";
        
        $sql = mysql_query($query) or die("Erro ao selecionar Movimentos para base de IRRF");
        while($rows = mysql_fetch_assoc($sql)){ 
            if($rows['tipo_movimento'] == "CREDITO"){
                $base += $rows['credito'];
            }
            
            if($rows['tipo_movimento'] == "DEBITO"){
                $base -= $rows['debito'];
            }
        }    
        
        /**
         * DESCONTANDO INSS 
         * DA BASE DE IRRF
         */
        $base = $base - $inssDescontado;
        
        return $base; 
                            
    }
   
    /**
     * ESSA COLUNA NO BANCO DE DEDOS 
     * É A QUE GUARDA O VALOR DO SALARIO
     * DEPOIS DO CALCULO DE DIAS.
     */
    public function getSalLimpo($clt){
        $folha = $this->getFolha();
        $mesCompetencia = $this->getMesFolha();
        $anoCompetencia = $this->getAnoFolha();
        
        $salario = 0;
        $qyr = "SELECT A.id_clt, A.id_folha, A.sallimpo  FROM rh_folha_proc AS A
                WHERE A.id_clt = '{$clt}' AND A.id_folha = '{$folha}' 
                AND A.mes = {$mesCompetencia} AND A.ano = {$anoCompetencia}";
                
                
                
        $sql = mysql_query($qyr) or die("Erro ao selecionar Salario limpo");
        while($rows = mysql_fetch_assoc($sql)){
            $salario = $rows['sallimpo'];
        }
        
        return $salario;
    }
    
    /**
     * CALCULO DE SALARIO BASE
     */
    public function salarioBase($salario, $dias){
        $base = ($salario/30) * $dias;
        return $base;
    }
    
    /**
     * ESSE METODO VAI PASSAR PARA O WFUNCTION
     */
    public function ultimoDiaDoMes($mes,$ano){
        $dia = date("t", mktime(0,0,0,$mes,'01',$ano));
        $novaData = $ano."-". str_pad($mes, 2, "0", STR_PAD_LEFT) ."-".$dia;
        return $novaData;
    }
    
        
    /**
     * NOVA FOLHA
     * ESSE METODO VAI SER RESPONSAVEL EM, RECEBER UM ARRAY DE DADOS 
     * E MONTAR UMA STRING SEPARADA POR VIRGULA... 
     * EX: ('Sinesio luiz', 'masculino', 'mesquita', 'rj') 
     * @param type $result
     */
    public function montaString($campos = array(), $array, $camposExtra = array()){
        $string = "";
        if(is_array($campos) && !empty($campos)){
            foreach ($array['dados'] as $k => $v){
                $string .= "(";
                    foreach ($v as $key => $values){
                        if(in_array($key, $campos)){
                            $string .=  "'" . utf8_encode($values) . "',";
                        }
                    }
                    
                    if(is_array($camposExtra) && !empty($camposExtra)){
                        foreach ($camposExtra as $key => $values){
                            $string .=  "'" . utf8_encode($values) . "',";
                        }
                    }
                    
                $string = substr($string,0,-1);
                $string .= "),";
            }
            
            $string = substr($string,0,-1);
            //echo $string;
            return $string;
        }        
    }    
    
    /**
     * MÉTODO DE DEBUG
     * @param type $objet
     */
    public function getDebug($objet){
        echo "<pre>";
            print_r($objet);
        echo "</pre>";
    }
    
    /*
     * MÉTODO QUE RETORNA
     * TOTALIZADORES DA FOLHA DE PGT
     */
    public function getTotalizadoresFP($id, $status) {
        $qry = "SELECT *
            FROM rh_folha AS A
            WHERE A.id_folha = '{$id}' AND A.status = '{$status}'";
        
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        //FOLHA
        $qry_folha = "SELECT SUM(A.base_inss) AS base_inss, SUM(A.inss) AS inss
            FROM rh_folha_proc AS A
            WHERE A.id_folha = '{$id}' AND A.status = '{$status}'";
        $sql_folha = mysql_query($qry_folha) or die(mysql_error());
        $res_folha = mysql_fetch_assoc($sql_folha);
        
        //FERIAS
        $qry_ferias = "SELECT SUM(A.base_inss) AS soma, SUM(A.inss) AS inss
            FROM rh_ferias AS A
            WHERE A.projeto = '{$res['projeto']}' AND A.regiao = '{}' AND '{$row_folha['ano']}-{$row_folha['mes']}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = {$row_folha['mes']}
            ORDER BY id_ferias DESC";
        $sql_ferias = mysql_query($qry_ferias) or die(mysql_error());
        $res_ferias = mysql_fetch_assoc($sql_ferias);
        
        $dados = array(
            "base_inss" => $res_folha['base_inss']
        );
        
        return $dados;
    }
    
    /*
     * MÉTODO QUE RETORNA
     * TOTALIZADORES DA FOLHA PARA CONFERENCIA COM O SEFIP GUIA
     */
    public function getConferenciaFolhaSefipGuia($mes, $ano, $arrayProjeto = array(), $select = 1) {
        $mes2 = $mes - 1;
        $ano2 = $ano;
        if($mes2 == 0) {
            $mes2 = 12;
            $ano2--;
        }
        $sqlIds = "SELECT group_concat(id_folha) id_folha, group_concat(projeto) id_projeto FROM rh_folha WHERE ano = {$ano} AND mes = {$mes} AND status = 3 AND projeto IN (".implode(',', $arrayProjeto).");";
        if($_COOKIE['debug'] == 666){
            print_array('////////////////////////////////$sqlIds////////////////////////////////');
            print_array($sqlIds);
        }
        $sqlIds = mysql_fetch_assoc(mysql_query($sqlIds));
        $sqlIds2 = "SELECT group_concat(id_folha) id_folha, group_concat(projeto) id_projeto FROM rh_folha WHERE ano = {$ano2} AND mes = {$mes2} AND status = 3 AND projeto IN (".implode(',', $arrayProjeto).");";
        if($_COOKIE['debug'] == 666){
            print_array('////////////////////////////////$sqlIds2////////////////////////////////');
            print_array($sqlIds2);
        }
        $sqlIds2 = mysql_fetch_assoc(mysql_query($sqlIds2));
    //    print_array($sqlIds);
        if($sqlIds['id_folha']) { 
            $sql = "
            SELECT 
                A.id_regiao,
                COUNT(A.id_clt) 'PARTICIPANTES', 
                SUM(A.salliquido) 'LIQUIDO',
                SUM(A.base_inss) + (SELECT IF(SUM(G.valor)>0,SUM(G.valor),0) FROM rpa_autonomo AS G LEFT JOIN autonomo AS H ON(G.id_autonomo = H.id_autonomo) WHERE H.id_projeto = A.id_projeto AND H.status_reg = 1 AND G.mes_competencia = {$mes} AND G.ano_competencia = {$ano} AND H.pis NOT IN (SELECT pis FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.id_folha = A.id_folha)) 'BASE DE INSS SEFIP', 
                SUM(IFNULL(A.base_inss,0) + IFNULL(C.base_inss_13,0)) 'BASE DE INSS',
                SUM(IFNULL(A.inss,0)) + SUM(IFNULL(B.inss,0)) + SUM(IFNULL(C.inss_dt,0)) + SUM(IFNULL(C.inss_ss,0)) 'INSS', 
                (SUM(A.base_inss) + SUM(C.base_inss_13)) * 0.2 'INSS (EMPRESA)',
                ((SUM(A.base_inss) + SUM(C.base_inss_13)) * 0.2) + (SELECT IFNULL(SUM(G.valor),0) * 0.2 FROM rpa_autonomo AS G LEFT JOIN autonomo AS H ON(G.id_autonomo = H.id_autonomo) WHERE H.id_projeto = A.id_projeto AND H.status_reg = 1 AND G.mes_competencia = {$mes} AND G.ano_competencia = {$ano} AND H.pis NOT IN (SELECT pis FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.id_folha = A.id_folha)) 'INSS (EMPRESA) + AUTONOMO',
                (SUM(A.base_inss) + SUM(C.base_inss_13)) * 0.02 'INSS (RAT)',
                (SUM(A.base_inss) + SUM(C.base_inss_13)) * 0.058 'INSS (TERCEIROS)',
                -- SUM(A.base_irrf) - SUM(A.a5049) 'BASE DE IRRF', 
                F.base_irrf - F.a5049 'BASE DE IRRF - MES ANTERIOR', 
                -- SUM(A.imprenda) + SUM(A.ir_dt) + SUM(A.ir_rescisao) + SUM(A.ir_ferias) 'IRRF',
                F.imprenda + F.ir_dt + F.ir_rescisao + F.ir_ferias 'IRRF - MES ANTERIOR',
                SUM(A.a5049) 'DDIR',
                -- SUM(F.a5049) 'DDIR - MES ANTERIOR',
                SUM(A.base_inss) - SUM(IF(A.status_clt = 61 || A.status_clt = 64 || A.status_clt = 66, A.base_inss, 0)) + SUM(IF(A.status_clt = 70, A.sallimpo, 0)) 'BASE DE FGTS',
                SUM(A.base_inss) - SUM(IF(A.status_clt = 61 || A.status_clt = 64 || A.status_clt = 66, A.base_inss, 0)) + SUM(IF(A.status_clt = 70, A.sallimpo, 0)) 'BASE DE PIS',
                (SUM(A.base_inss) - SUM(IF(A.status_clt = 61 || A.status_clt = 64 || A.status_clt = 66, A.base_inss, 0)) + SUM(IF(A.status_clt = 70, A.sallimpo, 0))) * 0.01 'VALOR PIS',
                (SUM(A.base_inss) - SUM(IF(A.status_clt = 61 || A.status_clt = 64 || A.status_clt = 66, A.base_inss, 0)) + SUM(IF(A.status_clt = 70, A.sallimpo, 0))) * 0.08 'FGTS',
                -- (SUM(A.base_inss * 0.08) - SUM(IF(A.status_clt = 61 || A.status_clt = 64 || A.status_clt = 66, A.base_inss, 0) * 0.08) + SUM(IF(A.status_clt = 70, A.sallimpo, 0) * 0.08)) 'FGTS2',
                SUM(A.a6005) AS 'SALARIO MATERNIDADE',
                E.base_inss AS 'BASE INSS AUTONOMO', 
                E.inss AS 'DESC INSS AUTONOMO',
                E.base_ir AS 'BASE IR AUTONOMO',
                E.ir AS 'DESC IR AUTONOMO',
                -- G.base_ir AS 'BASE IR AUTONOMO - MES ANTERIOR',
                -- G.ir AS 'DESC IR AUTONOMO - MES ANTERIOR',
                E.iss AS 'DESC ISS AUTONOMO'
            FROM rh_folha_proc A
            LEFT JOIN rh_ferias B ON (A.id_clt = B.id_clt AND A.id_projeto = B.projeto AND CONCAT(A.ano,'-',A.mes) BETWEEN DATE_FORMAT(B.data_ini, '%Y-%m') AND DATE_FORMAT(B.data_fim, '%Y-%m') AND B.status = 1 AND MONTH(B.data_ini) = A.mes)
            LEFT JOIN (SELECT SUM(R.base_inss_ss) base_inss_ss, SUM(R.dt_salario) dt_salario, R.id_clt, SUM(R.base_inss_13) base_inss_13, SUM(R.inss_dt) inss_dt, SUM(R.inss_ss) inss_ss, R.motivo, R.percentual_inss_13 FROM rh_recisao R WHERE R.id_projeto IN ({$sqlIds['id_projeto']}) AND MONTH(R.data_demi) = {$mes} AND YEAR(R.data_demi) = {$ano} AND R.status = 1 AND R.rescisao_complementar = 0 GROUP BY R.id_clt) C ON (A.id_clt = C.id_clt)
            LEFT JOIN rh_inss_outras_empresas AS D ON (D.id_clt = A.id_clt AND D.status = 1 AND CONCAT({$ano},'-',{$mes},'-01') BETWEEN D.inicio AND D.fim)
            LEFT JOIN (SELECT SUM(base_inss) AS base_inss, SUM(inss) AS inss, SUM(base_ir) AS base_ir, SUM(ir) AS ir, SUM(iss) AS iss, id_projeto FROM (
            SELECT 
                SUM(ra.valor) AS base_inss, 
                IF(SUM(ra.valor_inss) < CAST((SELECT teto FROM rh_movimentos WHERE cod IN(50241) AND anobase = {$ano} AND IFNULL(ra.valor,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,4)),SUM(ra.valor_inss), CAST((SELECT teto FROM rh_movimentos WHERE cod IN(50241) AND anobase = {$ano} AND IFNULL(ra.valor,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,4)) ) AS inss,
                SUM(ra.base_ir) AS base_ir,
                SUM(ra.valor_ir) AS ir,
                SUM(ra.valor_iss) AS iss,
                id_projeto
                FROM rpa_autonomo AS ra 
                LEFT JOIN autonomo AS au ON(ra.id_autonomo = au.id_autonomo) 
                WHERE au.id_projeto IN ({$sqlIds['id_projeto']}) AND au.status_reg = 1 AND ra.mes_competencia = {$mes} AND ra.ano_competencia = {$ano}
                AND au.pis NOT IN (SELECT pis FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.id_folha IN ({$sqlIds['id_folha']}))
                GROUP BY au.id_autonomo
                ) AS aut
                GROUP BY id_projeto) E ON (E.id_projeto = A.id_projeto)
            LEFT JOIN (SELECT id_projeto, SUM(a5049) a5049, SUM(base_irrf) base_irrf, SUM(imprenda) imprenda, SUM(ir_dt) ir_dt, SUM(ir_rescisao) ir_rescisao, SUM(ir_ferias) ir_ferias FROM rh_folha_proc WHERE id_folha IN ({$sqlIds2['id_folha']}) GROUP BY id_projeto) F ON (F.id_projeto = A.id_projeto)
                LEFT JOIN (SELECT 
                SUM(ra.base_ir) AS base_ir,
                SUM(ra.valor_ir) AS ir,
                id_projeto
                FROM rpa_autonomo AS ra 
                LEFT JOIN autonomo AS au ON(ra.id_autonomo = au.id_autonomo) 
                WHERE au.id_projeto IN ({$sqlIds['id_projeto']}) AND au.status_reg = 1 AND ra.mes_competencia = {$mes2} AND ra.ano_competencia = {$ano2}
                AND au.pis NOT IN (SELECT pis FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.id_folha IN ({$sqlIds2['id_folha']}))
                GROUP BY au.id_projeto) G ON (G.id_projeto = A.id_projeto)
            WHERE A.id_folha IN ({$sqlIds['id_folha']}) AND A.status = 3
            GROUP BY A.id_regiao";
            if($_COOKIE['debug'] == 666){
                print_array('////////////////////////////////$sql////////////////////////////////');
                print_array($sql);
            }
            $qry = mysql_query($sql) or die("ERRO: " . mysql_error());
            $num = mysql_num_rows($qry);
            while($row = mysql_fetch_assoc($qry)){
                $id_regiao = $row['id_regiao'];
                unset($row['id_regiao']);
                foreach ($row as $key => $value) {
                    $array[$key][$id_regiao] = $value;
                }
            }
        }
        if($_COOKIE['debug'] == 666){
            print_array('////////////////////////////////$array////////////////////////////////');
            print_array($array);
        }
        return $array;
    }
    
    /*
     * MÉTODO QUE RETORNA
     * ARRAY PARA CONFERENCIA DO INSS DA FOLHA
     */
    public function getConferenciaFolhaInss($mes, $ano, $arrayProjeto = array()) {
        
        $sqlIds = "SELECT group_concat(id_folha) id_folha, group_concat(projeto) id_projeto FROM rh_folha WHERE ano = {$ano} AND mes = {$mes} AND status = 3 AND projeto IN (".implode(',', $arrayProjeto).");";
        if($_COOKIE['debug'] == 666){
            print_array('////////////////////////////////$sqlIds////////////////////////////////');
            print_array($sqlIds);
        }
        $sqlIds = mysql_fetch_assoc(mysql_query($sqlIds));
        
        if($sqlIds['id_folha']) { 
            
            $sql = "
            SELECT 
                A.id_regiao, A.id_clt ID_CLT, A.nome NOME,
                SUM(A.salliquido) 'LIQUIDO',
                SUM(IFNULL(A.base_inss,0) + IFNULL(C.base_inss_13,0)) 'BASE DE INSS',
                SUM(IFNULL(A.inss,0)) + SUM(IFNULL(B.inss,0)) + SUM(IFNULL(C.inss_dt,0)) + SUM(IFNULL(C.inss_ss,0)) 'INSS', 
                IFNULL(SUM(IF(IF(IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0) < 0,0,IF(IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0))),0) + IFNULL(SUM(IF(IF(IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0) < 0,0,IF(IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0))),0) 'INSS2',
                IFNULL(SUM(
                    IF(IF(IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0) < 0,0,IF(IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0))
                ),0) 'INSS_F',
                CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IF(C.base_inss_ss > 0,IFNULL(C.base_inss_ss,0),IFNULL(A.base_inss,0)) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) 'P_F',
                IFNULL(SUM(
                    IF(IF(IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0) < 0,0,IF(IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) > 570.88, 570.88,IFNULL(C.base_inss_13,0) * CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2))) - IFNULL(D.desconto,0))
                ),0) 'INSS_R',
                CAST((SELECT percentual FROM rh_movimentos WHERE cod IN(5020) AND anobase = A.ano AND IFNULL(C.base_inss_13,0) BETWEEN v_ini AND v_fim) AS DECIMAL(10,2)) 'P_R'
            FROM rh_folha_proc A
            LEFT JOIN rh_ferias B ON (A.id_clt = B.id_clt AND A.id_projeto = B.projeto AND CONCAT(A.ano,'-',A.mes) BETWEEN DATE_FORMAT(B.data_ini, '%Y-%m') AND DATE_FORMAT(B.data_fim, '%Y-%m') AND B.status = 1 AND MONTH(B.data_ini) = A.mes)
            LEFT JOIN (SELECT SUM(R.base_inss_ss) base_inss_ss, SUM(R.dt_salario) dt_salario, R.id_clt, SUM(R.base_inss_13) base_inss_13, SUM(R.inss_dt) inss_dt, SUM(R.inss_ss) inss_ss, R.motivo, R.percentual_inss_13 FROM rh_recisao R WHERE R.id_projeto IN ({$sqlIds['id_projeto']}) AND MONTH(R.data_demi) = {$mes} AND YEAR(R.data_demi) = {$ano} AND R.status = 1 GROUP BY R.id_clt) C ON (A.id_clt = C.id_clt)
            LEFT JOIN rh_inss_outras_empresas AS D ON (D.id_clt = A.id_clt AND D.status = 1 AND CONCAT({$ano},'-',{$mes},'-01') BETWEEN D.inicio AND D.fim)
            WHERE A.id_folha IN ({$sqlIds['id_folha']}) AND A.status = 3
            GROUP BY A.id_clt
            ORDER BY A.nome";
            if($_COOKIE['debug'] == 666){
                print_array('////////////////////////////////$sql////////////////////////////////');
                print_array($sql);
            }
            $qry = mysql_query($sql) or die("ERRO: " . mysql_error());
            $num = mysql_num_rows($qry);
            while($row = mysql_fetch_assoc($qry)){
                foreach ($row as $key => $value) {
                    $array[$row['ID_CLT']][$key] = $value;
                }
            }
        }
        if($_COOKIE['debug'] == 666){
            print_array('////////////////////////////////$array////////////////////////////////');
            print_array($array);
        }
        return $array;
    }
    
}
