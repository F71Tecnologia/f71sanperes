<?php

class Movimentos {

    public $info_movimentos = array();
    private $id_clt;
    private $id_mov;
    private $cod_mov;
    private $mes;
    private $ano;
    private $id_regiao;
    private $id_projeto;
    private $tipo_quantidade;
    private $quantidade;
    private $qnt_horas;
    private $lancadoFolha;
    private $porcentagem;
    private $idFolha;
    private $legenda;
    private $faixa;
    private $TipoQnt = array(
        1 => 'Horas',
        2 => 'Dias'
    );
    private $mesesArray = array(
        1 => "Janeiro",
        2 => "Fevereiro",
        3 => "Março",
        4 => "Abril",
        5 => "Maio",
        6 => "Junho",
        7 => "Julho",
        8 => "Agosto",
        9 => "Setembro",
        10 => "Outubro",
        11 => "Novembro",
        12 => "Dezembro"
    );
    public $ids_movimentos_update_geral;
    public $ids_movimentos_estatisticas;

    public function setIdClt($id_clt) {
        $this->id_clt = $id_clt;
    }

    public function setIdMov($id_mov) {
        $this->id_mov = $id_mov;
    }

    public function setCodMov($cod_mov) {
        $this->cod_mov = $cod_mov;
    }

    public function setMes($mes) {
        $this->mes = $mes;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }

    public function setIdProjeto($projeto) {
        $this->id_projeto = $projeto;
    }

    public function setIdRegiao($regiao) {
        $this->id_regiao = $regiao;
    }

    public function setTipoQuantidade($tp_qnt) {
        $this->tipo_quantidade = $tp_qnt;
    }

    public function setQuantidade($qnt) {
        $this->quantidade = $qnt;
    }
    
    public function setQuantidadeHoras($qntHoras) {
        $this->qnt_horas = $qntHoras;
    }
    
    public function getQuantidadeHoras() {
        return $this->qnt_horas;
    }

    public function setLancadoPelaFolha($lancado) {
        $this->lancadoFolha = $lancado;
    }

    public function setPorcentagem($porcentagem) {
        $this->porcentagem = $porcentagem;
    }

    public function getPorcentagem() {
        return $this->porcentagem;
    }

    public function setIdFolha($idFolha) {
        $this->idFolha = $idFolha;
    }

    public function getIdFolha() {
        return $this->idFolha;
    }

    public function setLegenda($legenda) {
        $this->legenda = $legenda;
    }

    public function getLegenda() {
        return $this->legenda;
    }
    
    public function setFaixa($faixa){
        $this->faixa = $faixa;
    }
    
    public function getFaixa(){
        return $this->faixa;
    }

    /* Carrega todos os movimentos em um array para que possa 
     * ser utilizado pelos métodos sem necessidade de nova consulta.
     * Para melhor desempenho é recomendado chamar esse método antes de um loop.
     * @param type $anobase
     */

    public function carregaMovimentos($anobase) {


        $query = "SELECT id_mov,cod, descicao,categoria, faixa, v_ini, v_fim, percentual, fixo, piso, teto, anobase, incidencia_inss, incidencia_irrf,incidencia_fgts
                                    FROM rh_movimentos 
                                    WHERE (cod IN(0001,5020,5021,5022,5049,50241,6007) AND anobase = '$anobase') OR anobase = 0";

        $qr_impostos = mysql_query($query) or die(mysql_error($query));

        while ($row_mov = mysql_fetch_assoc($qr_impostos)) {
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['id_mov'] = $row_mov['id_mov'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['cod'] = $row_mov['cod'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['descicao'] = $row_mov['descicao'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['categoria'] = $row_mov['categoria'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['v_ini'] = $row_mov['v_ini'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['v_fim'] = $row_mov['v_fim'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['percentual'] = $row_mov['percentual'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['fixo'] = $row_mov['fixo'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['piso'] = $row_mov['piso'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['teto'] = $row_mov['teto'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['anobase'] = $row_mov['anobase'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['incidencia_inss'] = $row_mov['incidencia_inss'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['incidencia_irrf'] = $row_mov['incidencia_irrf'];
            $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['incidencia_fgts'] = $row_mov['incidencia_fgts'];
        }

        $this->info_movimentos = $RH_MOVIMENTOS;

        //print_r($query);
    }

    /* Verifica existencia do movimento
     * @param type $tipo_lancamento 1 - mensal(padrão), 2- SEMPRE, 1,2 - os dois tipos
     * @param type $status 
     * @param type $lancado_folha  Movimento lançado pelo sistema na folha de pagamento
     * @return type
     */

    public function verificaMovimento($tipo_lancamento = 1, $status = 1, $lancado_folha = 0) {

        $campo_lancadoFolha = ($lancado_folha == 1) ? 'AND lancado_folha = 1' : '';

        $sqlMensal = "(mes_mov ={$this->mes} AND ano_mov = '{$this->ano}' AND lancamento = 1  AND status = $status $campo_lancadoFolha )";
        $sqlSempre = "(lancamento = 2 AND status = {$status})";


        if ($tipo_lancamento === '1,2' or $tipo_lancamento === '2,1') {
            $criteria = $sqlSempre . ' OR ' . $sqlMensal . ' OR '  . '( importacao = 1 )' ;
        } elseif ($tipo_lancamento == 1) {
            $criteria = $sqlMensal;
        } elseif ($tipo_lancamento == 2) {
            $criteria = $sqlSempre;
        }
        
        //echo "SELECT * FROM rh_movimentos_clt WHERE id_clt = {$this->id_clt} AND id_mov IN({$this->id_mov}) AND ( {$criteria} )";
        
        $verifica_mov = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = {$this->id_clt} AND id_mov IN({$this->id_mov}) AND ( {$criteria} )");
        $row = mysql_fetch_assoc($verifica_mov);
        $resultado['id_movimento'] = $row['id_movimento'];
        $resultado['valor_movimento'] = $row['valor_movimento'];
        $resultado['num_rows'] = mysql_num_rows($verifica_mov);
        $resultado['sql'] = $sql;

        return $resultado;
    }

    /* Grava o movimento na tabela rh_movimentos_clt
     * @param type $valor_mov
     * @param type $tipo_lancamento
     * @return type
     */

    public function insereMovimento($valor_mov, $tipo_lancamento = 1) {

        $INFO_MOV = $this->info_movimentos[$this->cod_mov][1];
        $incidencia[] = ($INFO_MOV['incidencia_inss'] == 1) ? '5020' : '';
        $incidencia[] = ($INFO_MOV['incidencia_irrf'] == 1) ? '5021' : '';
        $incidencia[] = ($INFO_MOV['incidencia_fgts'] == 1) ? '5023' : '';
        $incidencia = implode(',', $incidencia);

        $sql = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,tipo_qnt,qnt,qnt_horas, status, status_reg, lancado_folha, id_folha, legenda ) 
                              VALUES 
                              ('{$this->id_clt}','{$this->id_regiao}','{$this->id_projeto}','{$this->mes}','{$this->ano}','{$INFO_MOV['id_mov']}','{$INFO_MOV['cod']}','{$INFO_MOV['categoria']}','{$INFO_MOV['descicao']}',NOW(),'{$_COOKIE[logado]}','{$valor_mov}','{$INFO_MOV['percentual']}','{$tipo_lancamento}','{$incidencia}','{$this->tipo_quantidade}','{$this->quantidade}','$this->qnt_horas',1, 1, '{$this->lancadoFolha}', '$this->idFolha', '$this->legenda')";

        if (empty($INFO_MOV)) {
            $dadosLog = array('id_movimento' => $this->id_mov, 'cod_movimento' => $this->cod_mov, 'descricao' => "Movimento nao encontrado na variavel this->info_movimentos: metodo insereMovimento()");
            $this->gravaLogMovimento($dadosLog);
        }

//            if($_COOKIE['logado'] == 179){
//                echo "<pre>";
//                    print_r($sql);
//                echo "</pre>";
//                exit();
//            }    

        mysql_query($sql) or die(mysql_error());
        $this->limpaVariaveis();


        $resultado['insert_id'] = mysql_insert_id();
        return $resultado;
    }

    /* Atualiza o valor do movimento.
     * Usado nos lançamentos de movimento feito na folha de pagamento
     * @param type $id_movimento
     * @param type $valor
     */

    public function updateValorPorId($id_movimento, $valor) {
        if (!empty($valor)) {
            if (!empty($this->tipo_quantidade)) {
                $setQuantidade = ", tipo_qnt = '{$this->tipo_quantidade}', qnt = '{$this->quantidade}', qnt_horas = '{$this->qnt_horas}'";
            }
            mysql_query("UPDATE rh_movimentos_clt SET valor_movimento = '{$valor}',legenda = '{$this->legenda}' $setQuantidade WHERE id_movimento = '{$id_movimento}'  LIMIT 1");
            $this->limpaVariaveis();
        }
    }

    /* VErifica se o movimento ja foi lançado e insere ou atualiza conforme necessidade
     * Ainda não usado
     * @param type $valor
     * @param type $tipo_lançamento
     */

    public function verificaInsereAtualizaFolha($valor, $tipo_lancamento = 1) {
                    
        $verifica = $this->verificaMovimento($tipo_lancamento, 1, 1);

        if (empty($verifica['num_rows'])) {
            //echo "<br>INSERT<br>" ;
            $this->insereMovimento($valor);
        } else {
            //echo "<br> UPDATE <br>";
            $this->updateValorPorId($verifica['id_movimento'], $valor);
        }
    }

    /**
     * MUDAR O STATUS O MOVIMENTO DO CLT 
     */
    public function removeMovimento($clt, $idFolha, $id_mov = array()) {
        $retorno = false;

        $criterio = " ";
        if (!empty($id_mov)) {
            $criterio = " AND id_mov IN( " . implode(',', $id_mov) . " ) ";
        }

        $query = "UPDATE rh_movimentos_clt SET status = 0 WHERE id_clt = '{$clt}' AND id_folha = '{$idFolha}' $criterio ";
        $sql = mysql_query($query) or die("Erro ao remover movimento");
        if ($sql) {
            $retorno = true;
        }

        return $retorno;
    }

    /*
     * Limpa as variaveis para 
     * não ter problemas em um loop.
     */

    public function limpaVariaveis() {
        $this->id_clt = NULL;
        $this->id_mov = NULL;
        $this->cod_mov = NULL;
        $this->mes = NULL;
        $this->ano = NULL;
        $this->id_regiao = NULL;
        $this->id_projeto = NULL;
        $this->tipo_quantidade = NULL;
        $this->quantidade = NULL;
        $this->legenda = NULL;
        $this->idFolha = NULL;
    }

    /**
     * 
     * @param type $mes
     * @param type $ano
     * @param type $tipo_lancamento
     */
    public function verificaCompetencia($mes, $ano, $tipo_lancamento) {
        if ($mes == 13) {
            $competencia = "13º Primeira Parcela";
        } elseif ($mes == 14) {
            $competencia = "13º Segunda Parcela";
        } elseif ($mes == 15) {
            $competencia = "13º Integral";
        } elseif ($mes == 16) {
            $competencia = "Rescisão";
        } elseif ($tipo_lancamento == 2) {
            $competencia = "SEMPRE";
        } else {
            $competencia = $this->mesesArray[(int) $mes] . '/' . $ano;
        }
        return $competencia;
    }

    public function getMovimentosLancadosPorClt($id_clt) {

        $qr_mov = mysql_query("SELECT a.*, b.incide_dsr
                FROM rh_movimentos_clt a
                LEFT JOIN rh_movimentos b ON 
                a.id_mov = b.id_mov
                WHERE id_clt = $id_clt AND STATUS = '1' AND lancamento = '2' UNION
                SELECT a.*, b.incide_dsr
                FROM rh_movimentos_clt a
                LEFT JOIN rh_movimentos b ON 
                a.id_mov = b.id_mov
                WHERE id_clt = $id_clt AND STATUS = '1' AND lancamento = '1'");

        while ($row_mov = mysql_fetch_assoc($qr_mov)) {

            $tipo_movimento = ($row_mov['tipo_movimento'] == 'DESCONTO' or $row_mov['tipo_movimento'] == 'DEBITO') ? 'DEBITO' : 'CREDITO';
            $competencia = $this->verificaCompetencia($row_mov['mes_mov'], $row_mov['ano_mov'], $row_mov['lancamento']);

            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['id_mov'] = $row_mov['id_mov'];
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['id_movimento'] = $row_mov['id_movimento'];
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['nome'] = $row_mov['nome_movimento'];
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['valor'] = $row_mov['valor_movimento'];
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['competencia'] = $competencia;
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['tipo'] = $tipo;
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['incidencia'] = ($row_mov['incidencia'] == '5020,5021,5023') ? 'INSS,IRRF,FGTS' : '';
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['obs'] = $row_mov['obs'];
            $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['incide_dsr'] = $row_mov['incide_dsr'];

            if ((!empty($row_mov['qnt']) and $row_mov['qnt'] != '(NULL)') or ( !empty($row_mov['qnt_horas']) and $row_mov['qnt_horas'] != '00:00:00')) {

                if ($row_mov['tipo_qnt'] == 1) {
                    $qnt = substr($row_mov['qnt_horas'], 0, 5);
                } else if ($row_mov['tipo_qnt'] == 2) {
                    $qnt = $row_mov['qnt'];
                }

                $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['tipo_qnt'] = $this->TipoQnt[$row_mov['tipo_qnt']];
                $mov_lancado[$tipo_movimento][$row_mov['id_movimento']]['qnt'] = $qnt;
            } else {
                $qnt = '';
            }
        }
        return $mov_lancado;
    }

    public function getTodosMovimentos() {
        $qr_mov = mysql_query("SELECT * FROM rh_movimentos GROUP BY cod");
        while ($row = mysql_fetch_assoc($qr_mov)) {
            $linhas[$row['cod']] = $row;
        }
        return $linhas;
    }

    /**
     * Método para pegar os movimentos lançado para o clt dentro da folha,
     * 
     * 
     * @param type $clt
     * @param type $regiao
     * @param type $mes
     * @param type $ano
     * @param type $dias_ferias
     * @param type $dias
     * @param type $sinaliza_evento
     * @return type
     */
    public function getMovimentosFolhaAberta($clt, $regiao, $mes, $ano, $dias_ferias, $dias, $sinaliza_evento, $naoLancarMovimentos = 0) {

        if (empty($dias_ferias)) {
            unset($dias_ferias);
        }
        if (empty($sinaliza_evento)) {
            unset($sinaliza_evento);
        }

        if (((isset($dias_ferias) and $dias_ferias <= 30) or ! isset($dias_ferias)) and $regiao == 48) {// POG PARA VIAMÃO
            if ($dias_ferias == 30) {
                $condicao = ' AND A.id_mov NOT IN(56,235) ';
            } else {
                $condicao = '';
            }
        }

        //echo "<br>**************************QUERY MOVIMENTOS ABERTO****************************<br>";
        if ($naoLancarMovimentos == 1) {

            $qryMovimentos = "SELECT 
                                    B.categoria as tipo_movimento, 
                                    A.id_movimento, 
                                    A.id_mov, 
                                    A.lancamento, 
                                    A.valor_movimento, 
                                    A.incidencia, 
                                    A.cod_movimento, 
                                    A.tipo_qnt, 
                                    A.qnt, 
                                    A.qnt_horas, 
                                    A.nome_movimento,
                                    B.incidencia_inss, 
                                    B.incidencia_irrf, 
                                    B.incidencia_fgts,
                                    B.incide_dsr
                               FROM rh_movimentos_clt AS A
                                       LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                               WHERE (A.id_clt = '{$clt}' AND A.status = '1' AND A.lancamento IN(1) AND A.mes_mov = '{$mes}' AND A.ano_mov = '{$ano}' AND A.cod_movimento = '80092' AND A.id_mov != '0'  {$condicao} )
                               ORDER BY A.nome_movimento";
        } else {

            $qryMovimentos = "SELECT 
                                    B.categoria as tipo_movimento, 
                                    A.id_movimento, 
                                    A.id_mov, 
                                    A.lancamento, 
                                    A.valor_movimento, 
                                    A.incidencia, 
                                    A.cod_movimento, 
                                    A.tipo_qnt, 
                                    A.qnt, 
                                    A.qnt_horas, 
                                    A.nome_movimento,
                                    B.incidencia_inss, 
                                    B.incidencia_irrf, 
                                    B.incidencia_fgts,
                                    B.incide_dsr
                               FROM rh_movimentos_clt AS A
                                       LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                               WHERE (A.id_clt = '{$clt}' AND A.status = '1' AND A.lancamento IN(1) AND A.mes_mov = '{$mes}' AND A.ano_mov = '{$ano}' AND A.cod_movimento != '8000' AND A.id_mov != '0'  {$condicao} )
                                      || 
                                      (A.id_clt = '{$clt}' AND A.status = '1' AND A.lancamento IN(2))
                               ORDER BY A.nome_movimento";
        }

        $qr_movimentos = mysql_query($qryMovimentos) or die('Erro ao selecionar movimentos');

        /**
         * 
         */
        while ($row_movimento = mysql_fetch_array($qr_movimentos)) {

//            if($_COOKIE['logado'] == 179){
//                echo "<pre>";
//                    echo "<br>********************* MOVIMENTOS *************************<br>";
//                    print_r($row_movimento);
//                    echo "<br>********************* FIM MOVIMENTOS *************************<br>";
//                echo "</pre>";
//            }


            $tipoMOV = $row_movimento['tipo_movimento'];
            $id_movimento = $row_movimento['id_movimento'];

            /**
             * 21/06/2017
             * SINESIO LUIZ 
             * COMPOSIÇÃO DOS TOTAIS DE  
             * CREDITO E DEBITO NA FOLHA
             * 
             */
            if ($row_movimento['tipo_movimento'] == 'CREDITO') {

                /**
                 * IDs UPDATE  
                 */
                if ($row_movimento['lancamento'] == 1) {
                    $this->ids_movimentos_update_geral[] = $row_movimento['id_movimento'];
                }

                /*
                 * IDs ESTATISTICAS
                 */
                $this->ids_movimentos_estatisticas[] = $row_movimento['id_movimento'];
                $ids_movimentos_parcial[] = $row_movimento['id_movimento'];
                $ids_movimentos_update_individual[] = $row_movimento['id_movimento'];

                /**
                 * 
                 */
                $total_rendimentos += $row_movimento['valor_movimento'];
            } elseif ($row_movimento['tipo_movimento'] == 'DEBITO') {

                /**
                 * IDs UPDATE  
                 */
                if ($row_movimento['lancamento'] == 1) {
                    $this->ids_movimentos_update_geral[] = $row_movimento['id_movimento'];
                }

                /*
                 * IDs ESTATISTICAS
                 */
                $this->ids_movimentos_estatisticas[] = $row_movimento['id_movimento'];
                $ids_movimentos_parcial[] = $row_movimento['id_movimento'];
                $ids_movimentos_update_individual[] = $row_movimento['id_movimento'];

                /**
                 * 
                 */
                $total_descontos += $row_movimento['valor_movimento'];
            }

//            if($_COOKIE['logado'] == 179){
//                 echo "<pre>";
//                     echo "<br>********************* IDs ESTATISTICAS *************************<br>";
//                         print_r($this->ids_movimentos_estatisticas);
//                         print_r($this->ids_movimentos_update_geral);
//                     echo "<br>********************* FIM MOVIMENTOS *************************<br>";
//                 echo "</pre>";
//            }  

            /**
             * Acrescenta os Movimentos 
             * nas Bases de INSS e IRRF
             */
            $incidencias = explode(',', $row_movimento['incidencia']);

            //echo "Incide INSS: " . $row_movimento['incidencia_inss'] . "<br>";        
            //if (in_array(5020, $incidencias)) { // INSS	
            if ($row_movimento['incidencia_inss'] == 1) {
                if ($tipoMOV == 'CREDITO') {
                    $base_inss += $row_movimento['valor_movimento'];
                } elseif ($tipoMOV == 'DEBITO') {
                    $base_inss -= $row_movimento['valor_movimento'];
                }
            }

            //echo "Incide IRRF: " . $row_movimento['incidencia_irrf'] . "<br>";
            //if (in_array(5021, $incidencias)) { // IRRF
            if ($row_movimento['incidencia_irrf'] == 1) {
                if ($tipoMOV == 'CREDITO') {
                    $base_irrf += $row_movimento['valor_movimento'];
                } elseif ($tipoMOV == 'DEBITO') {
                    $base_irrf -= $row_movimento['valor_movimento'];
                }
            }

            //echo "Incide FGTS: " . $row_movimento['incidencia_fgts'] . "<br>";
            //if (in_array(5023, $incidencias)) { // FGTS
            if ($row_movimento['incidencia_fgts'] == 1) {
                if ($tipoMOV == 'CREDITO') {
                    $base_fgts += $row_movimento['valor_movimento'];
                } elseif ($tipoMOV == 'DEBITO') {
                    $base_fgts -= $row_movimento['valor_movimento'];
                }
            }

//            print_array([$row_movimento['id_movimento'],$row_movimento['incide_dsr']]);

            if ($row_movimento['incide_dsr'] == 1) {
                if ($tipoMOV == 'CREDITO') {
                    $base_dsr += $row_movimento['valor_movimento'];
                }
            }

            // Salário Família Mês Anterior
            if ($row_movimento['cod_movimento'] == '50220') {
                $familia_mes_anterior = $row_movimento['valor_movimento'];
            }


            //Verificando o tipo de quan
            if (!empty($row_movimento['tipo_qnt'])) {
                $tipoQnt = $row_movimento['tipo_qnt'];
                switch ($tipoQnt) {
                    case 1: $qnt = substr($row_movimento['qnt_horas'], 0, 5);
                        break;
                    case 2: $qnt = $row_movimento['qnt'];
                        break;
                }
                $quantidade = $qnt . ' ' . $this->tipoQnt[$tipoQnt];
            } else {
                $quantidade = '';
            }

            $movimentos[$tipoMOV][$id_movimento]['nome'] = $row_movimento['nome_movimento'];
            $movimentos[$tipoMOV][$id_movimento]['cod_mov'] = $row_movimento['cod_movimento'];
            $movimentos[$tipoMOV][$id_movimento]['tipoQnt'] = $tipoQnt;
            $movimentos[$tipoMOV][$id_movimento]['qnt'] = $qnt;
            $movimentos[$tipoMOV][$id_movimento]['quantidade'] = $quantidade;
            $movimentos[$tipoMOV][$id_movimento]['valor'] = $row_movimento['valor_movimento'];
            $movimentos[$tipoMOV][$id_movimento]['incide_dsr'] = $row_movimento['incide_dsr'];
        }

        $resultado['familia_mes_anterior'] = number_format($familia_mes_anterior, 2, '.', '');
        $resultado['ids_movimentos_update_individual'] = $ids_movimentos_update_individual;
        $resultado['ids_movimentos_parcial'] = $ids_movimentos_parcial;
        $resultado['base_inss'] = number_format($base_inss, 2, '.', '');
        $resultado['base_irrf'] = number_format($base_irrf, 2, '.', '');
        $resultado['base_fgts'] = number_format($base_fgts, 2, '.', '');
        $resultado['base_dsr'] = number_format($base_dsr, 2, '.', '');
        $resultado['total_rendimento'] = number_format($total_rendimentos, 2, '.', '');
        $resultado['total_desconto'] = number_format($total_descontos, 2, '.', '');
        $resultado['movimentos'] = $movimentos;


        return $resultado;
    }

    /**
     * 
     * @param type $master
     * @param type $regiao
     * @param type $projeto
     * @return type
     */
    public function getListaClts($master, $regiao, $projeto) {
        if (!empty($master)) {
            $auxQuery .= " AND B.id_master = $master ";
        }
        if (!empty($regiao)) {
            $auxQuery .= " AND A.id_regiao = $regiao ";
        }
        if (!empty($projeto)) {
            $auxQuery .= " AND A.id_projeto = $projeto ";
        }
        $query = "
        SELECT A.nome, A.id_clt, A.id_projeto, A.status, 
            DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nasciBR,
            DATE_FORMAT(A.data_rg, '%d/%m/%Y') AS data_rgBR,
            DATE_FORMAT(A.data_escola, '%d/%m/%Y') AS data_escolaBR,
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradaBR,
            DATE_FORMAT(A.data_saida, '%d/%m/%Y') AS data_saidaBR,
            DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data_exameBR,
            DATE_FORMAT(A.dada_pis, '%d/%m/%Y') AS data_pisBR,
            DATE_FORMAT(A.data_ctps, '%d/%m/%Y') AS data_ctpsBR,
            DATE_FORMAT(A.data_cad, '%d/%m/%Y') AS data_cadBR,
            DATE_FORMAT(A.dataalter, '%d/%m/%Y') AS dataalterBR,
            DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demiBR,
            B.nome AS projeto_nome,
            C.nome AS curso_nome,
            D.unidade AS unidade_nome
        FROM 
            rh_clt A 
            LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
            LEFT JOIN curso C ON (A.id_curso = C.id_curso)
            LEFT JOIN unidade D ON (A.id_unidade = D.id_unidade)
        WHERE (A.status < 60 OR A.status = 200) AND B.status_reg = 1 $auxQuery
        ORDER BY projeto_nome,nome";
        $query = mysql_query($query) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $array[$row[id_projeto]][] = $row;
        }
        return $array;
    }

    /**
     *  categoria = credito ou debito 
     */
    public function selectCategoria($todos = null) {
        $sql = mysql_query("SELECT categoria
                    FROM rh_movimentos
                    WHERE categoria = 'CREDITO' OR categoria = 'DEBITO'
                    GROUP BY categoria");

        if ($todos == NULL) {
            $categoria = array("-1" => "« Todas as Categorias »");
        } else {
            $categoria = array("-1" => "« Selecione »");
        }

        while ($rst = mysql_fetch_assoc($sql)) {
            $categoria[$rst['categoria']] = $rst['categoria'];
        }
        return $categoria;
    }

    /**
     * 
     * @return type
     */
    public function selectIncidencia() {
        $sql = mysql_query("SELECT incidencia
                FROM rh_movimentos
                WHERE incidencia != ''
                GROUP BY incidencia
                ORDER BY incidencia");

        $categoria = array("-1" => "« Selecione »");

        while ($rst = mysql_fetch_assoc($sql)) {
            $categoria[$rst['incidencia']] = $rst['incidencia'];
        }
        return $categoria;
    }

    /**
     * 
     * @param type $categoria
     * @param type $movimento
     * @param type $ano
     * @return type
     */
    public function getMovimentos($categoria, $movimento, $ano) {
        if ($categoria == '-1') {
            $where = "WHERE (categoria = 'CREDITO' OR categoria = 'DEBITO')";
        } else {
            $where = "WHERE categoria = '{$categoria}'";
        }

        if (!empty($movimento)) {
            $and = "AND descicao LIKE '%{$movimento}%'";
        } else {
            $and = "";
        }

        if (!empty($ano)) {
            $and_ano = "AND (anobase = '{$ano}' OR anobase = 0)";
        } else {
            $and_ano = "";
        }

        $sql = "SELECT *
            FROM rh_movimentos {$where} {$and} {$and_ano} /*GROUP BY descicao*/ ORDER BY descicao";
        $qry = mysql_query($sql) or die("Erro getMovimentos");

        return $qry;
    }

    /**
     * 
     * @param type $id_mov
     * @return type
     */
    public function getMovimentoId($id_mov) {
        $sql = "SELECT *
            FROM rh_movimentos
            WHERE id_mov = {$id_mov}";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);

        return $res;
    }

    /**
     * 
     * @param type $cod_mov
     * @param type $ano
     * @return type
     */
    public function getfaixasMovimento($cod_mov, $ano) {

        if (!empty($ano)) {
            $and_ano = "AND anobase = {$ano}";
        } else {
            $and_ano = "AND anobase = YEAR(NOW()) AND NOW() BETWEEN data_ini AND data_fim";
        }

        $sql = "SELECT *
            FROM rh_movimentos
            WHERE cod = {$cod_mov} {$and_ano}";
        $qry = mysql_query($sql);

        return $qry;
    }

    /**
     * 
     * @return type
     */
    public function cadMovimento() {
        $log = new Log();

        $nome_mov = strtoupper($_REQUEST['nome']);
        $categoria_mov = $_REQUEST['categoria_mov'];
        $incidencia_mov = ($_REQUEST['incidencia_mov'] == '-1') ? '' : $_REQUEST['incidencia_mov'];
        $incide_inss = ($_REQUEST['incide_inss'] == '') ? 0 : $_REQUEST['incide_inss'];
        $incide_fgts = ($_REQUEST['incide_fgts'] == '') ? 0 : $_REQUEST['incide_fgts'];
        $incide_irrf = ($_REQUEST['incide_irrf'] == '') ? 0 : $_REQUEST['incide_irrf'];
        $incide_aviso_previo = ($_REQUEST['incide_aviso_previo'] == '') ? 0 : $_REQUEST['incide_aviso_previo'];
        $mov_lancavel = ($_REQUEST['mov_lancavel'] == '') ? 0 : $_REQUEST['mov_lancavel'];
        $campo_rescisao = $_REQUEST['campo_rescisao'];
        $codigoESocial = $_REQUEST['esocial_codigo'];
        $sindicatos = $_REQUEST['sindicato'];

        $percentual1 = $_REQUEST['porcentagem1'];
        $percentual2 = $_REQUEST['porcentagem2'];
        $tipo_lancamento = $_REQUEST['tipo_lancamento'];
        $ignorar_rubrica = $_REQUEST['ignorar_rubrica'];
        $incide_base_ferias_media = ($_REQUEST['incide_base_ferias_media'] == '') ? 0 : $_REQUEST['incide_base_ferias_media'];
        $incide_base_13_media = ($_REQUEST['incide_base_13_media'] == '') ? 0 : $_REQUEST['incide_base_13_media'];
        $incide_dsr = ($_REQUEST['incide_dsr'] == '') ? 0 : $_REQUEST['incide_dsr'];

        /*
         * 19/10/16
         * by: Max
         * ALTERADA A QUERY
         * POIS ESTAVA DUPLICANDO COD
         * A QUERY DEU PROBLEMA POIS O COD É VARCHAR
         */

        //consulta ultimo cod para dar auto incremento
        $sql_ultimoCod = mysql_query("SELECT cod FROM rh_movimentos ORDER BY id_mov DESC LIMIT 1") or die(mysql_error());
        /**
         * $sql_ultimoCod = mysql_query("SELECT CAST(cod AS INT) AS cod FROM rh_movimentos ORDER BY cod DESC LIMIT 1") or die(mysql_error());
         * GORDIN, TIVE QUE TIRAR POR QUE DEU PROBLEMA NA LAGOS ... ACABEI FAZENDO A CONVERSÃO NO php MESMO
         */
        $res_ultimoCod = mysql_result($sql_ultimoCod, 0);
        $cod_mov = (int) $res_ultimoCod + 1;

        //consulta ultimo id do campo_rescisao para auto incremento
//        if ($campo_rescisao == 1) {
            $sql_ultimoResc = mysql_query("SELECT MAX(campo_rescisao) FROM rh_movimentos") or die(mysql_error());
            $res_ultimoResc = mysql_result($sql_ultimoResc, 0);
            $campo_rescisaoResc = $res_ultimoResc + 1;
//        } else {
//            $campo_rescisaoResc = 0;
//        }

        //VERIFICA SE JA EXISTE O MOVIMENTO
        $sql_movExist = mysql_query("SELECT * FROM rh_movimentos WHERE descicao = '{$nome_mov}'");
        $res_movExist = mysql_num_rows($sql_movExist);

        if ($res_movExist > 0) {
            $_SESSION['MESSAGE'] = 'Já existe o movimento ' . $nome_mov;
            $_SESSION['MESSAGE_TYPE'] = 'danger';
        } else {
            $insere = mysql_query("INSERT INTO rh_movimentos (faixa, codigo_esocial,cod, descicao, categoria, incidencia, user_cad, mov_lancavel, campo_rescisao, incidencia_inss, incidencia_fgts, incidencia_irrf, percentual, percentual2, tipo_qnt_lancavel, ignorar_rubrica, incide_base_ferias_media, incide_base_13_media, incide_dsr, incide_aviso_previo) values 
            ('1', '{$codigoESocial}','{$cod_mov}', '{$nome_mov}', '{$categoria_mov}', '{$incidencia_mov}', '{$id_usuario}', '{$mov_lancavel}', '{$campo_rescisaoResc}', '{$incide_inss}', '{$incide_fgts}', '{$incide_irrf}', '{$percentual1}', '{$percentual2}', {$tipo_lancamento}, {$ignorar_rubrica}, {$incide_base_ferias_media}, {$incide_base_13_media}, {$incide_dsr}, {$incide_aviso_previo})") or die(mysql_error());

            $id_ultimo_movimento = mysql_insert_id();

            $log->log(2, "Movimento ID $id_ultimo_movimento cadastrado.", "rh_movimentos");

            foreach ($sindicatos as $sindicato) {

                $sqlMovSind = "INSERT INTO rh_movimentos_sindicatos_assoc (id_mov, cnpj_sindicato) VALUES ('{$id_ultimo_movimento}','{$sindicato}')";
                $queryMovSind = mysql_query($sqlMovSind);
                $idMovSind = mysql_insert_id();

                $log->log(2, "Movimento ID $id_ultimo_movimento associado ao sindicato ID $sindicato", "rh_movimentos_sindicatos_assoc");
            }

            if ($insere) {
                $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
                $_SESSION['MESSAGE_TYPE'] = 'info';
            } else {
                $_SESSION['MESSAGE'] = 'Erro ao cadastrar a entrada ' . $nome_mov;
                $_SESSION['MESSAGE_TYPE'] = 'danger';
            }

            return $id_ultimo_movimento;
        }
    }

    /**
     * 
     * @param type $id_mov
     */
    public function alteraMovimento($id_mov) {

        $log = new Log();

        $nome_mov = strtoupper($_REQUEST['nome']);
        $categoria_mov = $_REQUEST['categoria_mov'];
        $incidencia_mov = ($_REQUEST['incidencia_mov'] == '-1') ? '' : $_REQUEST['incidencia_mov'];
        $incide_inss = $_REQUEST['incide_inss'];
        $incide_fgts = $_REQUEST['incide_fgts'];
        $incide_irrf = $_REQUEST['incide_irrf'];
        $mov_lancavel = ($_REQUEST['mov_lancavel'] == '') ? 0 : $_REQUEST['mov_lancavel'];
        $campo_rescisao = $_REQUEST['_campo_resc'];
        $sindicatos = $_REQUEST['sindicato'];
        $codigoESocial = $_REQUEST['esocial_codigo'];
        foreach ($sindicatos as $value) {
            $sind[$value] = $value;
        }
        ksort($sind);

        $percentual1 = $_REQUEST['porcentagem1'];
        $percentual2 = $_REQUEST['porcentagem2'];
        $tipo_lancamento = $_REQUEST['tipo_lancamento'];
        $ignorar_rubrica = $_REQUEST['ignorar_rubrica'];
        $incide_base_ferias_media = $_REQUEST['incide_base_ferias_media'];
        $incide_base_13_media = $_REQUEST['incide_base_13_media'];
        $incide_dsr = $_REQUEST['incide_dsr'];
        $incide_aviso_previo = $_REQUEST['incide_aviso_previo'];
        

        //consulta ultimo cod para dar auto incremento
        $sql_ultimoCod = mysql_query("SELECT cod FROM rh_movimentos ORDER BY id_mov DESC LIMIT 1") or die(mysql_error());
        $res_ultimoCod = mysql_result($sql_ultimoCod, 0);
        $cod_mov = $res_ultimoCod + 1;

        //consulta ultimo id do campo_rescisao para auto incremento
        if ($campo_rescisao == 0) {
            $sql_ultimoResc = mysql_query("SELECT MAX(campo_rescisao) FROM rh_movimentos") or die(mysql_error());
            $res_ultimoResc = mysql_result($sql_ultimoResc, 0);
            $campo_rescisaoResc = $res_ultimoResc + 1;
        } else {
            $campo_rescisaoResc = $_REQUEST['_campo_resc'];
        }

        $antigo = $log->getLinha(rh_movimentos, $id_mov);

        $insere = mysql_query("UPDATE rh_movimentos SET faixa = '1', codigo_esocial = '{$codigoESocial}', descicao = '{$nome_mov}', categoria = '{$categoria_mov}', incidencia = '{$incidencia_mov}', user_alter = '{$id_usuario}', ultima_alter = NOW(), mov_lancavel = '{$mov_lancavel}', incidencia_inss = '{$incide_inss}', incidencia_fgts = '{$incide_fgts}', incidencia_irrf = '{$incide_irrf}', campo_rescisao = '{$campo_rescisaoResc}',
            percentual = '{$percentual1}', percentual2 = '{$percentual2}', tipo_qnt_lancavel = '{$tipo_lancamento}', ignorar_rubrica = '{$ignorar_rubrica}', incide_base_ferias_media = '{$incide_base_ferias_media}', incide_base_13_media = '{$incide_base_13_media}', incide_dsr = '{$incide_dsr}', incide_aviso_previo = '{$incide_aviso_previo}'
            WHERE id_mov = '{$id_mov}'") or die(mysql_error());

        $novo = $log->getLinha(rh_movimentos, $id_mov);

        $log->log(2, "Movimento ID $id_mov atualizado.", "rh_movimentos", $antigo, $novo);

        $sqlVerMovs = "SELECT * FROM rh_movimentos_sindicatos_assoc WHERE id_mov = $id_mov AND status = 1 ORDER BY id_sindicato";
        $queryVerMovs = mysql_query($sqlVerMovs);
        $arrVerMovs = array();

        while ($rowVerMovs = mysql_fetch_assoc($queryVerMovs)) {
            $arrVerMovs[$rowVerMovs['id_sindicato']] = $rowVerMovs['id_sindicato'];
        }

        if (!in_array('99', $sind)) {
            if (!empty(array_diff($sind, $arrVerMovs)) || !empty(array_diff($arrVerMovs, $sind))) {
                $sqlRemSind = "UPDATE rh_movimentos_sindicatos_assoc SET status = 0 WHERE status = 1 AND id_mov = $id_mov";
                $queryRemSind = mysql_query($sqlRemSind);

                foreach ($sind as $key => $sindicato) {
                    if ($key >= 1) {
                        $sqlSindicato = "INSERT INTO rh_movimentos_sindicatos_assoc (id_mov, cnpj_sindicato) VALUE ('{$id_mov}','{$sindicato}')";
//                echo "<br>";
                        $querySindicato = mysql_query($sqlSindicato);

//                echo mysql_insert_id();
                        $log->log(2, "Sindicato ID $sindicato associado ao movimento ID $id_mov.", "rh_movimentos_sindicatos_assoc");
                    }
                }
            }
        } else {
            $listaSindicatos = $this->carregaSindicatosAgrupado();

            foreach ($listaSindicatos as $key => $sindicato) {

                if (!in_array($key, $arrVerMovs) && $key > '99') {
                     $sqlSindicato = "INSERT INTO rh_movimentos_sindicatos_assoc (id_mov, cnpj_sindicato) VALUE ('{$id_mov}','{$key}')";
//                echo "<br>";
                    $querySindicato = mysql_query($sqlSindicato);

//                echo mysql_insert_id();
                    $log->log(2, "Sindicato ID $sindicato associado ao movimento ID $id_mov.", "rh_movimentos_sindicatos_assoc");
                }
            }
        }

        if ($insere) {
            $_SESSION['MESSAGE'] = 'Informações alteradas com sucesso!';
            $_SESSION['MESSAGE_TYPE'] = 'info';
            session_write_close();
            header('Location: gestao_movimentos.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao editar a entrada ' . $nome_mov;
            $_SESSION['MESSAGE_TYPE'] = 'danger';
        }
    }

    /**
     * 
     * @return type
     */
    public function getTetoInss() {
        $data_hj = date("Y-m-d");

        $qry = "SELECT teto
            FROM rh_movimentos
            WHERE cod = '5020' AND '{$data_hj}' BETWEEN data_ini AND data_fim LIMIT 1";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_result($sql, 0);

        return $res;
    }

    /**
     * ESSE METODO VAI 
     * TRAZER TODOS OS MOVIMENTOS 
     * AGRUPADO POR CLT 
     * NECESSÁRIO SETAR O ID DA FOLHA
     * NO INICIO DO ARQUIVO
     */
    public function movimentosFolhaGrupyClt() {

        try {
            $qry = "SELECT 
                        A.id_clt, 
                        A.tipo_movimento, 
                        A.cod_movimento, 
                        A.nome_movimento, 
                        A.valor_movimento,
                        A.legenda
                    FROM rh_movimentos_clt as A WHERE A.id_folha = '{$this->getIdFolha()}' AND 
                    A.`status` > 0 AND A.tipo_movimento NOT IN('ENCARGOS')";

            $sql = mysql_query($qry) or die('Erro ao selecionar movimentos ');
            $arrayMovs = array();
            if (mysql_num_rows($sql) > 0) {
                while ($rows = mysql_fetch_assoc($sql)) {
                    $arrayMovs[$rows['id_clt']][$rows['tipo_movimento']][$rows['cod_movimento']]['cod'] = $rows['cod_movimento'];
                    $arrayMovs[$rows['id_clt']][$rows['tipo_movimento']][$rows['cod_movimento']]['nome'] = $rows['nome_movimento'];
                    $arrayMovs[$rows['id_clt']][$rows['tipo_movimento']][$rows['cod_movimento']]['valor'] = $rows['valor_movimento'];
                    $arrayMovs[$rows['id_clt']][$rows['tipo_movimento']][$rows['cod_movimento']]['legenda'] = $rows['legenda'];
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $arrayMovs;
    }

    /**
     * Gravar Log de tentativa frustrada na manipulação de dados na tabela movimentos
     * @param array $dados (id_movimento=>?,cod_movimento=>?,descricao=>?)
     * @return boolean true
     */
    private function gravaLogMovimento($dados) {
        $dados['data'] = date("Y-m-d H:i:s");
        $dados['id_funcionario'] = $_COOKIE['logado'];

        $campos = array_keys($dados);

        $sqlIn = "INSERT INTO rh_movimentos_log (" . implode(",", $campos) . ") 
                    VALUES ('" . implode("','", $dados) . "');";
        mysql_query($sqlIn);
        return true;
    }

    /**
     * 
     * @return type
     */
    public function verCodESocial() {

        $sql = "SELECT * FROM e_social_03 ORDER BY nome";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_assoc($query)) {

            $arr[$row['id']] = $row;
        }

        return $arr;
    }

    /**
     * 
     * @param type $cod
     * @return type
     */
    public function getESocialByCod($cod) {

        $sql = "SELECT * FROM e_social_03 WHERE codigo = $cod";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_assoc($query)) {

            $arr[$row['codigo']]['codigo'] = $row['codigo'];
            $arr[$row['codigo']]['nome'] = $row['nome'];
            $arr[$row['codigo']]['descricao'] = $row['descricao'];
        }

        return $arr;
    }

    public function getESocialByCodAjax($cod) {

        $sql = "SELECT * FROM e_social_03 WHERE codigo = $cod";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_assoc($query)) {

            $arr[$row['codigo']]['codigo'] = $row['codigo'];
            $arr[$row['codigo']]['nome'] = utf8_encode($row['nome']);
            $arr[$row['codigo']]['descricao'] = utf8_encode($row['descricao']);
        }

        return $arr;
    }

    public static function carregaSindicatosAgrupado($default = array("-1" => "« Selecione »", "99" => "Todos")) {

        $qr_sindicato = mysql_query("SELECT *
            FROM rhsindicato AS A
            WHERE A.status = 1 AND A.cnpj > 0
            ORDER BY A.nome");
        $sindicatos = $default;
        while ($row_sindicato = mysql_fetch_assoc($qr_sindicato)) {
            $sindicatos[$row_sindicato['cnpj']] = $row_sindicato['nome'];
        }
        return $sindicatos;
    }
    
    public static function getDatasHorasFaltasAtrasos($id_movimento) {
        
        $sql = "SELECT *,
                    DATE_FORMAT(`data`, '%d/%m/%Y') data_ref,
                    DATE_FORMAT(`qnt_horas`, '%H:%i') horas_ref,
                    TIME_TO_SEC(qnt_horas) total_seconds
                FROM faltas_atrasos_datas_assoc
                WHERE id_movimento = $id_movimento AND status = 1
                ORDER BY `data`";
        $query = mysql_query($sql);
        while ($row = mysql_fetch_assoc($query)) {
            $arr[] = $row;
        }
        
        return $arr;
        
    }
    
    public static function removeDatasHorasFaltasAtrasos($ids) {
        
        $ids = implode(',',$ids);
        $sql = "UPDATE faltas_atrasos_datas_assoc SET status = 0 WHERE id IN ($ids)";
        $query = mysql_query($sql);
        
        if (mysql_affected_rows() > 0) {
            return 1;
        }
        
        return 0;
        
    }
    
    public static function addDatasHorasFaltasAtrasos(
            $id_clt,
            $id_mov,
            $id_movimento,
            $mes,
            $ano,
            $datas_falta_atraso = array(), 
            $horas_falta_atraso = array()) {
        
        $k = 0;
        foreach ($datas_falta_atraso as $data) {

            $data = implode('-',array_reverse(explode('/',$data)));
            $hora = implode('-',array_reverse(explode('/',$horas_falta_atraso[$k])));
            
            $valuesDatasHoras = "   INSERT INTO faltas_atrasos_datas_assoc (id_clt, id_mov, mes, ano, id_movimento, data, qnt_horas)
                                    VALUES 
                                    ('$id_clt', '$id_mov', '$mes', '$ano', '$id_movimento', '$data', '$hora')";
            $queryDatasHoras = mysql_query($valuesDatasHoras);
            
            $k++;

        }
        
        return 1;
    }
    
    public static function updateDatasHorasFaltasAtrasos ($id_movimento, $valor, $qnt, $tipo_quantidade) {
        
        $column = ($tipo_quantidade == 1) ? 'qnt_horas' : 'qnt';
                
        $sql = "UPDATE rh_movimentos_clt SET valor_movimento = '$valor', $column = '$qnt' WHERE id_movimento = $id_movimento";
        $query = mysql_query($sql);
        
    }

}
