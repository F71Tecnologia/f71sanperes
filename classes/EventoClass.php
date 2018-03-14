<?php

class Eventos {

    private $dados = array();
    //ID_STATUS QUE NÃO TEM RETORNO
    private $excessao_eventos = array("09", "10", "11", "12", "13", "15", "16", "18", "19", "22", "80");
    //EVENTOS QUE TEM DESCONTO
    private $eventos_com_desconto = array("20", "21", "30", "50", "54", "67", "68", "69", "80", "90", "70", "100");
    //EVENTOS 15 DIAS
    
    /**
     * 24/01/2017
     * A PEDIDO DO TIAGO, ESTOU REMOVENDO TODOS OS 
     * EVENTOS DE AUXILIO DOENÇA DA REGRA DOS 15 
     * DIAS 
     * @var type 
     */
    /**
    * @author Lucas Praxedes (31/01/2017)
    * A PEDIDO DO THIAGO, TODOS OS FUNCIONARIOS EM AUXÍLIO DOENÇA, NÃO ENTRAM NA REGRA DOS 15 DIAS
    */
//    private $codigos_15_dias = array("20", "21", "90");
    private $codigos_15_dias = array("20", "90");
    //DADOS
    public $array_dados = array();
    public $dias_evento;
    public $msg_15_dias;
    public $evento;
    public $sinaliza_evento;
    public $msg_evento;
    public $inicio;
    public $fim;
    public $listCLtsEventos;
    public $programadores = array(179,158,260,275,257,256);

    /**
     * MÉTODO QUE RECUPERA TODOS OS CLTS COM DATA DE RETORNO PARA O DIA EM QUESTÃO
     * @param type $data
     * @return type
     */
    public function getEventos($data) {

        $query = "SELECT * FROM (
                    SELECT A.id_evento, A.id_projeto, A.id_regiao, A.id_clt, A.nome_status, A.cod_status, A.id_status, A.data_retorno, B.nome AS nome_clt, B.status AS status_atual,
                    ADDDATE('{$data}', INTERVAL 1 DAY) AS data_novo_evento
                    FROM rh_eventos AS A
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                    WHERE data_retorno = '{$data}' AND A.id_status NOT IN(" . implode(',', $this->excessao_eventos) . ")
                  ) AS tmp WHERE status_atual != 10";

        $sql = mysql_query($query) or die("Error ao selecionar eventos");

        return $sql;
    }

    /**
     * MÉTODO QUE RETORNO O EVENTO COM DATA DE REFERENCIA DENTRO DO PERIODO DE INICIO E TERMINO
     * UTILIZADO PELA FOLHA DE PAGAMENTO, ENTÃO CUIDADO QUANDO FOR MEXER NESSA PORRA
     * @param type $cls
     * @param type $mes_referente
     */
    public function getEventoForFolha($clt, $mes_referente, $dataIniUltimoEvento = '2016-01', $array = false) {

        $competenciaIniEvento = date("Y-m", strtotime(str_replace("/", "-", $dataIniUltimoEvento)));
        $competenciaIniEvento = $competenciaIniEvento . '-17';

        if ($competenciaIniEvento <= '2015-06-17') {
            $dias_inss = 30;
        } else {
            $dias_inss = 15;
        }

        $query = "SELECT *,MONTH(data_retorno) as mes_retorno, MONTH(data_retorno) as mes_inicio,
            DATE_ADD(data, INTERVAL {$dias_inss} DAY) AS 15_dias,
            MONTH(data) as mes_inicio_evento,
            MONTH(DATE_ADD(data, INTERVAL {$dias_inss} DAY)) as mes_15_dias,
            DATE_FORMAT(data,'%d/%m/%Y') as data_inicioBR,
            DATE_FORMAT(data_retorno,'%d/%m/%Y') as data_retornoBR,
            DATE_FORMAT(DATE_ADD(data, INTERVAL {$dias_inss} DAY),'%d/%m/%Y') as data_15_diasBR 
            FROM rh_eventos AS A WHERE A.id_clt = '{$clt}' AND A.cod_status != 10 AND 
            (((A.data_retorno  = '0000-00-00' OR A.data_retorno IS NULL OR A.data_retorno = '' OR DATE_FORMAT(A.data_retorno,'%Y-%m') > '{$mes_referente}') AND DATE_FORMAT(A.data,'%Y-%m') <= '{$mes_referente}') OR 
            (DATE_FORMAT(A.data,'%Y-%m') = '{$mes_referente}' OR DATE_FORMAT(A.data_retorno,'%Y-%m') = '{$mes_referente}') OR (A.cod_status = 50 AND DATE_FORMAT(A.data_retorno,'%Y-%m') = '{$mes_referente}')) 
            AND A.status = '1' ORDER BY A.id_evento";


        $retorno = mysql_query($query);

        if ($array) {

            while ($rows = mysql_fetch_assoc($retorno)) {
                $dados[$rows['id_evento']] = array(
                    "id_clt" => $rows['id_clt'],
                    "id_regiao" => $rows['id_regiao'],
                    "id_projeto" => $rows['id_projeto'],
                    "nome_status" => $rows['nome_status'],
                    "cod_status" => $rows['cod_status'],
                    "data_inicio_sql" => $rows['data'],
                    "data_inss_sql" => $rows['15_dias'],
                    "data_retorno_sql" => $rows['data_retorno'],
                    "data_inicio" => $rows['data_inicioBR'],
                    "data_inss" => $rows['data_15_diasBR'],
                    "data_retorno" => $rows['data_retornoBR']
                );
            }

            return $dados;
        }

        return $retorno;
    }

    /**
     * MÉTODO PARA VALIDAR EVENTO PARA FOLHA
     */
    public function validaEventoForFolha($clt, $mes_referente, $data_inicio_folha, $data_final_folha) {

        /**
         * CONSULTA O ULTIMO
         * EVENTO
         */
        $ultimo_evento = $this->lastEventoFolha($clt);

        if (!empty($ultimo_evento['num_rows'])) {

            if ($ultimo_evento['dados']['cod_status'] == 10) {

                /**
                 * VERIFICA SE O ULTIMO EVENTO É ATIVIDADE NORMAL
                 */
                $result_evento = $this->verificaAtividadeNormalNoMes($data_inicio_folha, $data_final_folha, $ultimo_evento['dados']);
                return $result_evento;
            } else {

                /**
                 * VERIFICA SE O EVENTO ESTA NO INICIO OU FINAL DA FOLHA
                 */
                $result_evento = $this->getEventosInicioFimDaFolha($clt, $mes_referente, $data_inicio_folha, $data_final_folha, $ultimo_evento['dados']['data']);
                if($_COOKIE['debug'] == 345){
                    echo "<pre>";
                        print_r($result_evento);
                    echo "</pre>";
                }
                return $result_evento;
            }
        }
    }

    /**
     * MÉTODO VALIDA EVENTO COM INÍCIO NO MEIO DA FOLHA
     */
    public function getEventosInicioFimDaFolha($clt, $mes_referente, $data_inicio_folha, $data_final_folha, $dataIniUltimoEvento) {

        //EVENTOS
        $eventos = $this->getEventoForFolha($clt, $mes_referente, $dataIniUltimoEvento);

        if ($_COOKIE['debug'] == 345) {
            echo "<pre>";
            echo "Licença: ";
            print_r($eventos);
            echo "</pre>";
        }

        $num_eventos = mysql_num_rows($eventos);
        $total_dias_eventos = 0;

        while ($dados = mysql_fetch_assoc($eventos)) {

            if (!empty($num_eventos) && in_array($dados['cod_status'], $this->eventos_com_desconto)) {

                /**
                 * STATUS QUE A DATA DE INICIO TEM QUE SER A MESMA DO EVENTO
                 * MATERNIDADE(50), SERVIÇO MILITAR(30), RESCISAO INDIRETA COM AFASTAMENTO (67), LICENÇA SEM VENCIMENTO(69)
                 */
                
                /**
                 * @author Lucas Praxedes (31/01/2017)
                 * A PEDIDO DO THIAGO, TODOS OS FUNCIONARIOS EM AUXÍLIO DOENÇA, NÃO ENTRAM NA REGRA DOS 15 DIAS
                 */
                if ($dados['cod_status'] == 50 || $dados['cod_status'] == 30 || $dados['cod_status'] == 67 || $dados['cod_status'] == 69 || $dados['cod_status'] == 80 || $dados['cod_status'] == 21) {
                    $dataInicio = $dados['data'];
                } else {
                    if ($clt == 2952 && $mes_referente == '2016-06') {
                        $dataInicio = $dados['data'];
                    } else {
                        $dataInicio = $dados['15_dias'];
                        //$dataInicio = $dados['data'];
                    }
                }

                $dataInicioSemInss = $dados['data'];
                $dataRetorno = $dados['data_retorno'];

                /**
                 * DATA INICIO
                 */
                $dt_inicio = $dataInicio;

                /**
                 * SE O FIM DO EVENTO FOR ANTES DO FIM DA FOLHA	
                 */
                $dt_fim = ($dataRetorno < $data_final_folha && $dataRetorno != "0000-00-00") ? $dataRetorno : $data_final_folha;

                if (($dataInicio < $data_inicio_folha && $dataRetorno > $data_final_folha) || ($dataInicio < $data_inicio_folha && $dataRetorno == "0000-00-00")) {
                    $dias_evento_demonstrativo = 30;
                    $dias_evento = 30;
                }

                /*
                 * DIAS DE EVENTOS INICIALMENTE 
                 */
                if ($dataInicio >= $data_inicio_folha && $dataInicio <= $data_final_folha) {

                    //$dias_evento = 0;
                    $dias_trab_mes = date("d", strtotime(str_replace("/", "-", $dataInicio))) - 1;
                    $dias_evento += 30 - $dias_trab_mes;
                    $dias_evento_demonstrativo = 30 - $dias_trab_mes;

                    if ($dados['cod_status'] == 50) {
                        $dias_evento = 30 - $dias_trab_mes;
                    }
                }

                if ($dataRetorno >= $data_inicio_folha && $dataRetorno <= $data_final_folha) {

                    if (!empty($dias_evento)) {
                        $dias_trab_mes = date("d", strtotime(str_replace("/", "-", $dataRetorno)));
                        $dias_evento -= 30 - $dias_trab_mes;
                    } else {
                        //$dias_evento = 0;
                        $dias_trab_mes = date("d", strtotime(str_replace("/", "-", $dataRetorno)));
                        $dias_evento += $dias_trab_mes;
                        $dias_evento_demonstrativo = $dias_trab_mes;

                        if ($dados['cod_status'] == 50) {
                            $dias_evento = $dias_trab_mes;
                        }
                    }
                }

                $total_dias_eventos += $dias_evento;

                /**
                 * VARIÁVEL AINDA VOU DESCUBRIR PARA QUE SERVE
                 */
                $evento = TRUE;

                /**
                 * VARIÁVEL PARA COLORIR O NOME DO CLT
                 */
                $sinaliza_evento = TRUE;

                $competenciaIniEvento = date("Y-m", strtotime(str_replace("/", "-", $dataIniUltimoEvento)));
                $competenciaIniEvento = $competenciaIniEvento . '-17';

                if ($competenciaIniEvento <= '2015-06-17') {
                    $dias_inss = 30;
                } else {
                    $dias_inss = 15;
                }

                /*
                 * COMPARA DATA DO EVENTO COM DATAS DA FOLHA (INICIO E TERMINO)
                 */
                if ($dataInicioSemInss >= $data_inicio_folha && $dataInicioSemInss <= $data_final_folha) {

                    /*
                     * MÉTODO VERIFICA QUINZE DIAS
                     * CONDIÇÃO PARA OS 15 PRIMEIROS DIAS DE LICENÇA MÉDICA
                     */

                    if (in_array($dados['cod_status'], $this->codigos_15_dias)) {
                        //Se os primeiros 15 dias não ultrapassarem a data de término da folha
                        if ($dados['15_dias'] <= $data_final_folha) {
                            $dt_inicio = $dados['15_dias'];
                        } else {
                            $dias_evento = 0;
                        }

                        $msg_15_dias = ' <br /> PAGANDO OS PRIMEIROS ' . $dias_inss . ' DIAS DE LICENÇA  ATÉ ' . $dados['data_15_diasBR'];
                    }


                    $msg_evento .= "<br>" . $dados['data_inicioBR'] . ' a ' . $dados['data_retornoBR'] . " ({$dados['nome_status']} - {$dias_evento_demonstrativo} dias)" . $msg_15_dias;
                    $evento = TRUE;
                    $sinaliza_evento = TRUE;
                } else if ($dataRetorno >= $data_inicio_folha && $dataRetorno <= $data_final_folha) {

                    /**
                     * VERIFICA QUINZE DIAS
                     */
                    if (in_array($dados['cod_status'], $this->codigos_15_dias)) {
                        if ($dados['15_dias'] > $data_inicio_folha) {
                            $dt_inicio = $dados['15_dias'];
                            $msg_15_dias = ' <BR>PAGANDO OS PRIMEIROS ' . $dias_inss . ' DIAS DE LICENÇA  ATÉ ' . $dados['data_15_diasBR'];
                        } else {
                            $dt_inicio = $data_inicio_folha;
                        }
                    } else {
                        /*
                         * SE O INICIO DO EVENTO FOR DEPOIS DO INICIO DA FOLHA  
                         */
                        $dt_inicio = ($dados['data'] > $data_inicio_folha) ? $dados['data'] : $data_inicio_folha;
                    }

                    $this->listCLtsEventos[] = $dados;

                    $dt_fim = $dados['data_retorno'];
                    $msg_evento .= "<br>" . $dados['data_inicioBR'] . ' a ' . $dados['data_retornoBR'] . " ({$dados['nome_status']}  - {$dias_evento_demonstrativo} dias)" . $msg_15_dias;
                    $evento = TRUE;
                    $sinaliza_evento = TRUE;
                } else if ($dataInicioSemInss <= $data_inicio_folha && ($dataRetorno >= $data_final_folha || $dataRetorno == "0000-00-00")) {

                    /**
                     * CONDIÇÃO PARA OS 15 PRIMEIROS DIAS DE LICENÇA MÉDICA
                     */
                    if (in_array($dados['cod_status'], $this->codigos_15_dias)) {
                        if ($dataInicio > $data_inicio_folha) {
                            $dt_inicio = $dados['15_dias'];
                            $msg_15_dias = ' <br />PAGANDO OS PRIMEIROS ' . $dias_inss . ' DIAS DE LICENÇA  ATÉ ' . $dados['data_15_diasBR'];
                        } else {
                            $dias_evento = 30;
                        }
                        $dt_fim = $data_final_folha;
                    } else {
                        $dias_evento = 30;
                    }

                    $msg_evento .= "<br>" . $dados['data_inicioBR'] . ' a ' . $dados['data_retornoBR'] . " ({$dados['nome_status']}  - {$dias_evento_demonstrativo} dias)" . $msg_15_dias;
                    $evento = TRUE;
                    $sinaliza_evento = TRUE;
                }

                $resultado['cod_evento'] = $dados['cod_status'];
                $resultado['dt_inicio'] = $dt_inicio;
                $resultado['dt_fim'] = $dt_fim;
                $resultado['dias_evento'] = $dias_evento;
                $resultado['msg_15_dias'] = $msg_15_dias;
                $resultado['msg_evento'] = $msg_evento;
                $resultado['cod_status'] = $dados['cod_status'];
                $resultado['evento'] = $dados['cod_status'];
                $resultado['sinaliza_evento'] = $sinaliza_evento;
                $resultado['total_eventos'] = $total_dias_eventos;
            }
        }


        return $resultado;
    }

    /**
     * Método para verificar Atividade Normal
     * 
     * @param type $data_inicio_folha
     * @param type $data_final_folha
     * @param type $dados
     * @return string
     */
    public function verificaAtividadeNormalNoMes($data_inicio_folha, $data_final_folha, $dados) {

        if ($dados['data'] > $data_inicio_folha && $dados['data'] < $data_final_folha) {

            $dt_inicio = $data_inicio_folha;
            $dt_fim = $dados['data'];

            $dias_evento = abs((int) floor((strtotime($dt_inicio) - strtotime($dt_fim)) / 86400));
            if ($dias_evento != 0) {
                $sinaliza_evento = TRUE;
            }

            $resultado['dt_inicio'] = $dt_inicio;
            $resultado['dt_fim'] = $dt_fim;
            $resultado['dias_evento'] = $dias_evento;
            $resultado['sinaliza_evento'] = $sinaliza_evento;
            $resultado['msg_evento'] = 'RETORNOU DIA ' . $dados['data_inicioBR'];
            return $resultado;
        }
    }

    /**
     * Lista os eventos do sistema
     * @return array
     */
    public function listaEventos() {
        $query = "SELECT id_status,especifica,codigo FROM rhstatus";
        $result = mysql_query($query) or die("Error ao selecionar eventos");
        $rows = array();
        while ($row = mysql_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 
     * @param type $codigoEvento
     * @param type $regiao
     * @param type $projeto
     * @return type
     */
    public function contaCltPorEvento($codigoEvento, $regiao, $projeto = null) {
        $criteria = "";
        if (!empty($projeto)) {
            $criteria = " AND id_projeto = {$projeto}";
        }

        $sql = "SELECT COUNT(id_clt) as total FROM rh_clt WHERE status = {$codigoEvento} AND id_regiao = {$regiao} {$criteria}";
        $result = mysql_query($sql) or die("Error ao contar os clts com eventos {$codigoEvento}");
        $total = null;
        while ($row = mysql_fetch_assoc($result)) {
            $total = $row['total'];
        }
        return $total;
    }

    /**
     * MÉTODO QUE INSERE UM NOMO EVENTO DE VOLTA DE STATUS NORMAL PARA O USUÁRIO
     * @param type $data
     */
    //    public function criaEventoDoCron($data) {
    //        //MÉTODO PARA VERIFICAR CLTS COM TERMINO DE EVENTO
    //        $eventos = $this->getEventos($data);
    //        //CLTS COM MOVIMENTO JÁ LANÇADOS
    //        $clts = $this->verificaEventoLancado($data);
    //
    //        //CAMPOS
    //        $campos = "id_clt,id_regiao,id_projeto,nome_status,cod_status,id_status,data,data_retorno,dias,obs,status,status_reg,gerado_por";
    //        while ($linha = mysql_fetch_assoc($eventos)) {
    //            $this->dados[] = $linha['id_clt'];
    //            //GRAVANDO NOVO EVENTO
    //            $query = "INSERT INTO rh_eventos ({$campos}) VALUES ('{$linha['id_clt']}','{$linha['id_regiao']}','{$linha['id_projeto']}','Atividade Normal','10','1','{$linha['data_novo_evento']}','0000-00-00',0,'CLT voltado para atividade normal pelo CRON no servidor',1,1,2)";
    //            if (!in_array($linha['id_clt'], $clts)) {
    //                $sql = mysql_query($query) or die("Erro ao inserir evento do CRON na tabela");
    //                if ($sql) {
    //                    //MÉTODO PARA ATUALIZAR O STATUS DO CLT PARA ATIVIDADE NORMAL 
    //                    $this->atualizaStatusClt($linha['id_clt']);
    //                }
    //            }
    //        }
    //
    //    }

    /**
     * MÉTODO PARA VERIFICAR SE O LANÇAMENTO DE RETORNO JÁ EXISTE
     * @param type $data
     * @return type
     */
    public function verificaEventoLancado($data) {
        $clts = array();
        $query = "SELECT * FROM (
                    SELECT *, ADDDATE('{$data}', INTERVAL 1 DAY) AS data_posterior
                    FROM rh_eventos
                    WHERE cod_status = 10
                 ) AS tmp WHERE data = data_posterior";
        $sql = mysql_query($query) or die("Erro ao verificar evento");
        if (mysql_num_rows($sql) > 0) {
            while ($rows = mysql_fetch_assoc($sql)) {
                $clts[] = $rows['id_clt'];
            }
        }

        return $clts;
    }

    /**
     * MÉTODO PARA ALTERAR STATUS DO CLT
     * @param type $clt
     */
    public function atualizaStatusClt($clt, $status) {
        //UPDATE
        $query = "UPDATE rh_clt SET status = '{$status}' WHERE id_clt = '{$clt}'";
        $sql = mysql_query($query);
    }

    /**
     * MÉTODO PARA RETORNA TODOS CLTS E AUTONOMOS COM DATA DE RETORNO DENTRO DE 15 DIAS
     * @param type $data
     * @param type $regiao
     * @param type $projeto
     * @return string
     */
    public function getTerminandoEventos($data, $regiao, $projeto = null, $clt = null, $dias = 15) {

        $criteria = "";

        if (isset($projeto) && !empty($projeto)) {
            $criteria = " AND A.id_projeto = '{$projeto}'";
        }

        if (isset($clt) && !empty($clt)) {
            $criteria = " AND A.id_clt = '{$clt}'";
        }

        $query = "SELECT *, DATEDIFF(IF(pericia = 1,data_retorno,data_retorno_final), '2014-10-07') AS diferenca_dias FROM (	
                    SELECT A.id_evento, A.id_projeto, A.id_regiao, A.id_clt, A.nome_status, A.cod_status, A.id_status,A.data_retorno,A.data_retorno_final,A.status,
                    DATE_FORMAT(A.data_retorno, '%d/%m/%Y') AS dt_retorno, 
                    DATE_FORMAT(A.data_retorno_final, '%d/%m/%Y') AS dt_final, 
                    SUBDATE(A.data_retorno, INTERVAL {$dias} DAY) AS inicio_aviso,
                    B.nome AS nome_clt, B.status AS status_atual,C.regiao, D.nome AS nome_projeto,
                    (SELECT prorrogavel FROM rhstatus WHERE A.cod_status = codigo) AS prorrogavel,
                    (SELECT pericia FROM rhstatus WHERE A.cod_status = codigo) AS pericia
                    FROM rh_eventos AS A
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                    LEFT JOIN regioes AS C ON(A.id_regiao = C.id_regiao)
                    LEFT JOIN projeto AS D ON(A.id_projeto = D.id_projeto)
                    WHERE A.id_regiao = '{$regiao}' $criteria 
                 ) AS tmp WHERE (('{$data}' BETWEEN inicio_aviso AND data_retorno) OR ('{$data}' > data_retorno)) AND cod_status != 40 AND status_atual != 10 AND status != 0 AND cod_status != 10 AND cod_status != 0 AND status_atual != 200 AND status_atual NOT IN(60,61,62,63,64,65,66,81,101)";

//        echo "<!--" . $query . "-->";

        $sql = mysql_query($query) or die("Erro ao selecionar eventos com datas próximas de termino");
        $dados = array();
        while ($linha = mysql_fetch_assoc($sql)) {

            //0 = JÁ PASSARAM DA DATA, 1 PARA OS QUE ESTÃO NO DIA, 2 OS QUE ESTAM NO PRAZO 
            $index = "";
            if ($linha['diferenca_dias'] < 0) {
                $index = 0;
            } else if ($linha['diferenca_dias'] == 0) {
                $index = 1;
            } else {
                $index = 2;
            }

            $this->array_dados[$index][$linha['cod_status']][] = array(
                "data_retorno" => $linha['dt_retorno'],
                "nome_clt" => $linha['nome_clt'],
                "id_evento" => $linha['id_evento'],
                "prorrogavel" => $linha['prorrogavel'],
                "pericia" => $linha['pericia'],
                "data_final" => $linha['dt_final'],
                "projeto" => $linha['nome_projeto'],
                "dias_restantes" => $linha['diferenca_dias'],
            );

            $dados[] = array(
                "id_evento" => $linha['id_evento'],
                "nome_clt" => $linha['nome_clt'],
                "status_de" => $linha['nome_status'],
                "cod_status" => $linha['cod_status'],
                "status_para" => "Atividade Normal",
                "regiao" => $linha['regiao'],
                "projeto" => $linha['nome_projeto'],
                "dias_restantes" => $linha['diferenca_dias'],
                "data_retorno" => $linha['dt_retorno'],
                "data_final" => $linha['dt_final'],
                "status_atual" => $linha['status_atual'],
                "prorrogavel" => $linha['prorrogavel'],
                "pericia" => $linha['pericia'],
            );
        }

        return $dados;
    }

    /**
     * 
     * @param type $nova_data
     * @param type $motivo
     */
    public function prorrogaEvento($id_evento, $data_retorno, $data_prorrogada, $prorrogado_por, $prorrogado_em, $motivo) {

        $return = array("status" => false);
        $motivo = utf8_encode($motivo);
        $data_inicio = $this->getData($id_evento);
        $dias = $this->nova_qnt_dias($data_inicio['data'], $data_prorrogada);

        $dados = $this->getEventoById($id_evento);

        // comentado enquanto não normaliza o sistema!!
//        $data_modificada = ($dados['pericia'] == 1) ? "data_retorno" : "data_retorno_final"; // verificação para determinar o campo que é modificado na tabela
        $data_modificada = "data_retorno"; // verificação para determinar o campo que é modificado na tabela. provisório enquanto não normaliza

        $query = "UPDATE rh_eventos SET {$data_modificada} = '{$data_prorrogada}', obs = '{$motivo}', dias = '{$dias}' WHERE id_evento = '{$id_evento}'";
        $sql = mysql_query($query) or die('Erro ao prorrogar evento');

        //Criação de Log na Tabela "Log"

        $charset = mysql_set_charset('latin1');
        $f = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "Evento ID{$id_evento} foi prorrogado";
        $now = date("Y-m-d H:i:s");
        $sqlLog = "INSERT INTO log 
        (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
        VALUES 
        ('$f[id_funcionario]', '$f[id_regiao]', '$f[tipo_usuario]', '$f[grupo_usuario]', 'Eventos', NOW(), '$ip', '$acao')";
        mysql_query($sqlLog);

        //CRIA UM LOG DO UPDATE DE EVENTO
        $this->criaLog($id_evento, $prorrogado_por, 4, array("nome_status" => $dados['nome_status'], "cod_status" => $dados['cod_status'], "id_status" => $dados['id_status'], "data" => $dados['data'], "data_retorno" => $dados['data_retorno'], "dias" => $dados['dias'], "obs" => $dados['obs']));

        if ($sql) {
            $return = array("status" => true);
        }

        return $return;
    }

    /**
     * 
     * @param type $id_evento
     * @return type
     */
    public function getData($id_evento) {
        $dados = array();
        $query = "SELECT data FROM rh_eventos WHERE id_evento = '{$id_evento}'";
        $sql = mysql_query($query) or die("Erro ao selecionar data do evento");
        while ($linha = mysql_fetch_assoc($sql)) {
            $dados = array("data" => $linha['data']);
        }

        return $dados;
    }

    public function getDataRetorno($id_evento) {
        $dados = array();
        $query = "SELECT data_retorno FROM rh_eventos WHERE id_evento = '{$id_evento}'";
        $sql = mysql_query($query) or die("Erro ao selecionar data de retorno do evento");
        while ($linha = mysql_fetch_assoc($sql)) {
            $dados = array("data_retorno" => $linha['data_retorno']);
        }

        return $dados;
    }

    // funcao para grardar o log de exclusao
    public function criaLog($id_evento, $id_funcionario, $tipo, $evento_atual) {
        //GRAVANDO LOG DE EVENTO
        $query = $this->cadLogEventos($id_evento, $id_funcionario, $tipo, $evento_atual);

        //echo $query;
        if (mysql_query($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getEventoById($id) {
        $query = "SELECT *, DATE_FORMAT(data, '%d/%m/%Y') AS data_br, DATE_FORMAT(data_retorno, '%d/%m/%Y') AS data_retorno_br, DATE_FORMAT(data_retorno_final,'%d/%m/%Y') AS data_retorno_final_br,"
                . "(SELECT pericia FROM rhstatus WHERE codigo = cod_status) AS pericia "
                . "FROM rh_eventos WHERE id_evento = '{$id}'";
        $result = mysql_query($query) or die(mysql_error());
        $return = mysql_fetch_assoc($result);
        return $return;
    }

    public function geraTimestamp($data) {
        $partes = explode('-', $data);
        return mktime(0, 0, 0, $partes[1], $partes[2], $partes[0]);
    }

    public function nova_qnt_dias($data_inicial, $data_final) {
        $time_inicial = $this->geraTimestamp($data_inicial);
        $time_final = $this->geraTimestamp($data_final);
        $diferenca = $time_final - $time_inicial; // 19522800 segundos
        $dias = (int) floor($diferenca / (60 * 60 * 24));
        return $dias;
    }

    public function cadLogEventos($id_evento, $id_funcionario, $tipo, $evento_atual = array()) {

        //RETORNO DADOS DO EVENTO ATUALIZADO
        $evento_novo = $this->getEventoById($id_evento);


        //CALCULA NOVA QUANTIDADE DE DIAS
        $dias = $this->nova_qnt_dias($evento_novo['data'], $evento_novo['data_retorno']);

        //PARA FUNCIONAR CERTO NO EXCLUIR
        if ($tipo == 3) {
            $id_evento = $evento_atual['id_evento'];
            if (empty($evento_novo)) {
                $clt = mysql_fetch_assoc(mysql_query("SELECT data_entrada FROM rh_clt WHERE id_clt = '{$evento_atual["id_clt"]}'"));
                $evento_novo = array(
                    'nome_status' => 'Atividade Normal',
                    'cod_status' => '10',
                    'id_status' => '1',
                    'data' => $clt['data_entrada'],
                    'data_retorno' => '0000-00-00',
                    'dias' => '0',
                    'obs' => '',
                    'id_clt' => $evento_atual["id_clt"]
                );
            }
        }

        // QUANDO FOR VOLTAR PARA ATIVIDADE NORMAL
        if ($dias < 0) {
            $dias = 0;
        }

        //CADASTRA UM NOVO LOG
        $obs = utf8_encode($evento_novo["obs"]);
        $query = "INSERT INTO rh_eventos_log (id_evento,id_funcionario,data_mod,tipo,nome_status_de,cod_status_de,id_status_de,data_de,data_retorno_de,dias_de,obs_de,nome_status_para,cod_status_para,id_status_para,data_para,data_retorno_para,dias_para,obs_para,id_evento_anterior,id_clt)
                    VALUES ('{$id_evento}','{$id_funcionario}',NOW(),'{$tipo}','{$evento_atual['nome_status']}','{$evento_atual['cod_status']}','{$evento_atual['id_status']}','{$evento_atual['data']}','{$evento_atual['data_retorno']}','{$evento_atual['dias']}','{$evento_atual['obs']}','{$evento_novo["nome_status"]}','{$evento_novo["cod_status"]}','{$evento_novo["id_status"]}','{$evento_novo["data"]}','{$evento_novo["data_retorno"]}','{$dias}','{$obs}','{$evento_atual['id_evento']}','{$evento_novo['id_clt']}')";

        return $query;
    }

    public function getStatusByCod($codigo) {
        $query = "SELECT * FROM rhstatus WHERE codigo = '{$codigo}'";
        $sql = mysql_query($query) or die("Erro so selecionar status");
        $dados = mysql_fetch_assoc($sql);

        return $dados;
    }

    /**
     * MÉTODO PARA CADASTRAR UM NOVO EVENTO
     * @param type $status -- NOVO STATUS
     * @param type $dados_eventos_usado -- ARRAY COM OS DADOS DA REGIAO, PROJETO, CLT...
     * @return boolean
     */
    public function cadEvento($status, $dados_eventos_usado = array()) {
        $retorno = array("status" => false);
        if (!empty($dados_eventos_usado) && is_array($dados_eventos_usado)) {

            // verifica se clt está em evento
            $teste_em_evento = $this->testeCltEmEvento($dados_eventos_usado['id_clt'], $dados_eventos_usado['data']);
            if ($teste_em_evento) {
                $retorno['msg'] = 'Erro ao salvar. Verifique as datas do evento!';
                return $retorno;
            }

            // verifica se clt está com férias agendadas
//            $teste_em_ferias = $this->testeCltEmFerias($dados_eventos_usado['id_clt'], $dados_eventos_usado['data']);
//            if ($teste_em_ferias) {
//                $retorno['msg'] = 'CLT com férias agendadas.';
//                return $retorno;
//            }
            // pega evento anterior
            $row_evento = $this->lastEvento($dados_eventos_usado["id_clt"]);
            if ($row_evento['num_rows'] == 0) {
                $clt = mysql_fetch_assoc(mysql_query("SELECT data_entrada, status FROM rh_clt WHERE id_clt = '{$dados_eventos_usado["id_clt"]}'"));
                $row_evento = array(
                    'nome_status' => 'Atividade Normal',
                    'cod_status' => '10',
                    'id_status' => '1',
                    'data' => $clt['data_entrada'],
                    'data_retorno' => '0000-00-00',
                    'dias' => '0',
                    'obs' => '',
                );
            } else {
                $row_evento = $row_evento['dados'];
            }

            // verifica se evento anterior tem data retorno
            if ($row_evento['data_retorno'] == '0000-00-00' && $row_evento['cod_status'] != 10) { //ver com o sinésio e atividade normal vai precisar de data_retorno a partir de agora
                $retorno['msg'] = 'CLT em evento n&atilde;o finalizado. Favor inserir a Data de Retorno no evento anterior.';
                return $retorno;
            }

            $dados_status = $this->getStatusByCod($status);
            $query = "INSERT INTO rh_eventos (id_clt,id_regiao,id_projeto,nome_status,cod_status,id_status,data,data_retorno,dias,obs,status,status_reg,gerado_por,data_retorno_final) VALUES (
                        '{$dados_eventos_usado["id_clt"]}','{$dados_eventos_usado["id_regiao"]}','{$dados_eventos_usado["id_projeto"]}','{$dados_status["especifica"]}','{$status}','{$dados_status["id_status"]}','{$dados_eventos_usado["data"]}','{$dados_eventos_usado["data_retorno"]}','{$dados_eventos_usado["dias"]}','{$dados_eventos_usado["obs"]}','1','1','{$_COOKIE["logado"]}','{$dados_eventos_usado['data_retorno_final']}')";
            $sql = mysql_query($query);
            if ($sql) {
                $ultimo_evento = mysql_insert_id();
                $this->criaLog($ultimo_evento, $_COOKIE["logado"], 1, $row_evento);

                $query_atualiza_status = "UPDATE rh_clt SET status = '{$status}' WHERE id_clt = '{$dados_eventos_usado["id_clt"]}'";
                $sql_atualiza_status = mysql_query($query_atualiza_status) or die("Erro ao atualizar status do clt");
                
                $verFunc = "SELECT id_clt
                            FROM rh_clt
                            WHERE id_clt = '{$dados_eventos_usado["id_clt"]}'";
                
                $queryVerFunc = mysql_query($verFunc);
                $numVerFunc = mysql_num_rows($queryVerFunc);

                if ($numVerFunc == 1) {
                    $sqlDesFunc = "UPDATE funcionario SET status_reg = 0 WHERE id_clt = '{$dados_eventos_usado["id_clt"]}'";
                    $queryDesFunc = mysql_query($sqlDesFunc);
                    echo mysql_affected_rows();
                }
            }

            //ZERA OS CAMPOS DE data_saida, data_aviso, data_demi, status_demi DO RH_CLT 
            //QUANDO SAIR DE AGUARDANDO DEMISSAO PARA ATIVIDADE NORMAL
            if ($status == 10 AND $clt['status'] == 200) {
                $sql_zera_campos_demissao = "UPDATE rh_clt SET data_saida = '', data_aviso = '', data_demi = '', status_demi = '' WHERE id_clt = '{$dados_eventos_usado["id_clt"]}' LIMIT 1";
                $sql_zera_campos_demissao = mysql_query($sql_zera_campos_demissao);
            }

            if ($sql_atualiza_status) {
                $retorno = array("status" => true, "ultimo_id" => $ultimo_evento);
            }
        }

        return $retorno;
    }

    /*
     * MÉTODO PARA EDITAR EVENTO EXISTENTE
     */

    public function editarEvento($id_evento, $dados_evento = array()) {
        $retorno = array('status' => false);
        if (!empty($dados_evento) && is_array($dados_evento)) {

            // pega os valores atuis do banco para salvar posteriormente no log
            $evento_atual = $this->getEventoById($id_evento);

            $dados_status = $this->getStatusByCod($dados_evento['cod_status']);
            $query = "UPDATE rh_eventos set 
                        nome_status ='{$dados_status['especifica']}', 
                        cod_status = '{$dados_evento['cod_status']}', 
                        id_status = '{$dados_status['id_status']}', 
                        data = '{$dados_evento['data']}', 
                        data_retorno = '{$dados_evento['data_retorno']}', 
                        dias = '{$dados_evento['dias']}', 
                        obs = '{$dados_evento['obs']}',
                        data_retorno_final = '{$dados_evento['data_retorno_final']}'
                        WHERE id_evento = '$id_evento' LIMIT 1";
//            echo $query;
            $sql = mysql_query($query) or die(mysql_error());

            if ($sql) {
                $charset = mysql_set_charset('latin1');
                $f = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));
                $ip = $_SERVER['REMOTE_ADDR'];
                $acao = "Edição do Evento ID{$id_evento}";
                $now = date("Y-m-d H:i:s");
                $sqlLog = "INSERT INTO log 
        (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
        VALUES 
        ('$f[id_funcionario]', '$f[id_regiao]', '$f[tipo_usuario]', '$f[grupo_usuario]', 'Eventos', NOW(), '$ip', '$acao')";
                mysql_query($sqlLog);

                $log = $this->criaLog($id_evento, $_COOKIE["logado"], 2, $evento_atual);
            }
            if ($log) {
                $retorno = array('status' => true);
            }
        }
        return $retorno;
    }

    /*
     * MÉTIDO PARA MUDAR STATUS PARA 0 (REMOVER EVENTO)
     */

    public function removeEvento($id_evento, $id_clt) {
        if (is_numeric($id_evento) && is_numeric($id_clt)) {

            // pega os valores atuis do banco para salvar posteriormente no log
            $evento_atual = $this->getEventoById($id_evento);

            $sql = "UPDATE rh_eventos SET status = 0 WHERE id_evento = '$id_evento'";

            if (mysql_query($sql)) {
                $row_evento = $this->lastEvento($id_clt);
                if ($row_evento['num_rows'] == 0) {
                    $status = 10;
                } else {
                    $hj = date('Y-m-d');
                    $status = (($hj >= $row_evento['data'] && $hj < $row_evento['data_retorno']) || ($hj >= $row_evento['data'] && $row_eventos['data_retorno'] == '0000-00-00')) ? $row_evento['dados']['cod_status'] : 10;
                }

                $status_log = $this->criaLog($row_evento['id_evento'], $_COOKIE["logado"], 3, $evento_atual);
                $this->atualizaStatusClt($id_clt, $status);
                return $status_log;
            } else {
                return false;
            }
        }
    }

    /*
     * MÉTODO PARA RETORNAR ÚLTIMO EVENTO 
     */

    public function lastEvento($id_clt) {
        $ultimo_evento = mysql_query("SELECT *,
            DATE_FORMAT(data, '%d/%m/%Y') as data_inicioBR, 
            DATE_FORMAT(data_retorno, '%d/%m/%Y') AS data_retornoBR, 
            DATE_FORMAT(data_retorno_final, '%d/%m/%Y') AS data_retornoFinalBR, 
            (SELECT pericia FROM rhstatus WHERE codigo=cod_status) AS pericia 
            FROM rh_eventos WHERE id_clt = '{$id_clt}' AND status = 1 ORDER BY data DESC,id_evento DESC LIMIT 1");
        $row_evento = mysql_fetch_assoc($ultimo_evento);
        $num_rows = mysql_num_rows($ultimo_evento);
        if ($num_rows == 0) {
            return array('num_rows' => $num_rows);
        } else {
            return array("num_rows" => $num_rows, "dados" => $row_evento);
        }
    }

    /*
     * MÉTODO PARA RETORNAR ÚLTIMO EVENTO 
     */

    public function lastEventoFolha($id_clt) {
        $ultimo_evento = mysql_query("SELECT *,
            DATE_FORMAT(data, '%d/%m/%Y') as data_inicioBR, 
            DATE_FORMAT(data_retorno, '%d/%m/%Y') AS data_retornoBR, 
            DATE_FORMAT(data_retorno_final, '%d/%m/%Y') AS data_retornoFinalBR, 
            (SELECT pericia FROM rhstatus WHERE codigo=cod_status) AS pericia 
            FROM rh_eventos WHERE id_clt = '{$id_clt}' AND status = 1 AND cod_status != 10 ORDER BY id_evento DESC LIMIT 1");

        if ($_COOKIE['logado'] == 179) {
            echo "SELECT *,
            DATE_FORMAT(data, '%d/%m/%Y') as data_inicioBR, 
            DATE_FORMAT(data_retorno, '%d/%m/%Y') AS data_retornoBR, 
            DATE_FORMAT(data_retorno_final, '%d/%m/%Y') AS data_retornoFinalBR, 
            (SELECT pericia FROM rhstatus WHERE codigo=cod_status) AS pericia 
            FROM rh_eventos WHERE id_clt = '{$id_clt}' AND status = 1 AND cod_status != 10 ORDER BY id_evento DESC LIMIT 1";
        }

        $row_evento = mysql_fetch_assoc($ultimo_evento);
        $num_rows = mysql_num_rows($ultimo_evento);
        if ($num_rows == 0) {
            return array('num_rows' => $num_rows);
        } else {
            return array("num_rows" => $num_rows, "dados" => $row_evento);
        }
    }

    /**
     * RETORNA TODOS OS STATUS 
     */
    public function getStatus() {
        $dados = array();
        $query = "SELECT * FROM rhstatus";
        $sql = mysql_query($query) or die("Erro ao selecionar status");
        while ($linha = mysql_fetch_assoc($sql)) {
            $dados[$linha['codigo']] = $linha['especifica'];
        }

        return $dados;
    }

    /*
     * METODO PARA CALCULAR DATA DE RETORNO A PARTIR DO NÚMERO DE DIAS
     */

    public function calculaNovoRetorno($id, $dias) {
        $evento = $this->getEventoById($id);
//        $data = ($evento['pericia'] == 1) ? explode("-", $evento['data_retorno']) : explode("-", $evento['data_retorno_final']); // comentado até normalizar o bd.
        $data = (isset($evento['data_retorno']) && $evento['data_retorno'] != '') ? explode("-", $evento['data_retorno']) : explode("-", $evento['data_retorno_final']); //provisório enquanto o bd nao está normalizado
        $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[2] + $dias, $data[0] + $ano));
        return $newData;
        return date('d/m/Y', srttotime("+{$dias} days", strtotime($evento['data_retorno'])));
    }

    public function nova_data_retorno($data, $dias) {
        $data = explode("-", $data);
        $newData = date("d/m/Y", mktime(0, 0, 0, $data[1] + $meses, $data[2] + $dias, $data[0] + $ano));
        return $newData;
        return date('d/m/Y', srttotime("+{$dias} days", strtotime($evento['data_retorno'])));
    }

    /*
     * METODO PARA VERIFICAR QUANTOS DIAS O FUNCIONARIO ESTÁ EM EVENTOS DE UM 
     * MESMO TIPO, SENDO ESSES EVENTOS SEGUIDOS
     */

    public function getEventosSeguidos($id_clt, $cod_status, $cols_retorno = null) {
        $query = "SELECT `data`,data_retorno,dias FROM rh_eventos WHERE id_clt = '{$id_clt}' AND cod_status = '{$cod_status}' AND status = 1 ORDER BY `data` ASC";
        $result = mysql_query($query);
        $data_ant = '0000-00-00';
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $return['data_retorno'] = $row['data_retorno'];
            if (($row['data'] <= $data_ant)) {
                $return['soma'] += $row['dias'];
            } else {
                $return['data'] = $row['data'];
                $return['soma'] = $row['dias'];
            }
            $data_ant = $row['data_retorno'];
        }
        return $return;
    }

    public function getListCLtsEventos() {
        return $this->listCLtsEventos;
    }

    /*
     * METODO PARA VERIFICAR SE TIPO DE EVENTO TEM PERICIA
     */

    public function getPericia($codigo_status) {
        $query = "SELECT pericia FROM rhstatus WHERE codigo={$codigo_status}";
        $result = mysql_query($query);
        return mysql_fetch_assoc($result);
    }

    /*
     * METODO PARA VERIFICAR SE TIPO DE EVENTO TEM PERICIA
     */

    public function getProrrogavel($codigo_status) {
        $query = "SELECT prorrogavel FROM rhstatus WHERE codigo={$codigo_status}";
        $result = mysql_query($query);
        return mysql_fetch_assoc($result);
    }

    /*
     * METODO PARA LISTAR O HISTORICO DE EVENTOS DE UM CL
     */

    public function historico($clt, $regiao) {
        $query = "SELECT *, 
                                DATE_FORMAT( data_retorno,'%d/%m/%Y') as data_retorno_br,
                                DATE_FORMAT(data_retorno_final, '%d/%m/%Y') as data_retorno_final_br,
                                DATE_FORMAT( data,'%d/%m/%Y') as data_br,
                                (SELECT prorrogavel FROM rhstatus WHERE codigo = cod_status) AS prorrogavel
                                FROM rh_eventos 
                                WHERE id_clt = '$clt' 
                                AND id_regiao = '$regiao'  
                                AND status = '1' 
                                AND cod_status != 40
                                ORDER BY data DESC, id_evento DESC";
        echo "<!-- $query -->";
        $qr_historico_eventos = mysql_query($query)or die(mysql_error());
        while ($row_evento = mysql_fetch_assoc($qr_historico_eventos)) {
            $eventos[] = $row_evento;
        }
        return $eventos;
    }

    public function verificaCltStatus($id_clt, $data) {
        // verifica se clt está em evento
        $query_teste = "SELECT cod_status FROM rh_eventos
                    WHERE id_clt = '{$id_clt}' 
                    AND '$data' <= data
                    AND status = 1;";
        $resp = mysql_query($query_teste);
        $dados = mysql_fetch_assoc($resp);
        $num_rows = mysql_num_rows($resp);
        return array('num_rows' => $num_rows, 'cod_status' => $dados['cod_status']);
    }

    public function listarCltEmEvento($id_regiao, $id_projeto, $rhstatus, $nome = null, $inicial = null, $pagina = 1, $soEmEventos = FALSE) {
        /*
         * flag para verificar se é uma consulta por nome.
         * Habilita/desabilita funções na tela e condições na query.
         */
        $consultaByNome = (isset($nome) && !empty($nome)) ? TRUE : FALSE;

        $posicao = ($pagina - 1) * 100; // usado no limit da lista de funcionários
        // condicao para letra inicial do nome
        $cond_iniciaol = (isset($inicial) && !empty($inicial)) ? "AND a.nome LIKE '" . mysql_real_escape_string(trim($inicial)) . "%'" : "";

        // condicao para nome ou parte dele
        $cond_nome = (isset($nome) && !empty($nome)) ? "AND a.nome LIKE '%" . mysql_real_escape_string(trim($nome)) . "%'" : "";

        // condicao para projeto
        $cond_proj = (isset($id_projeto) && !empty($id_projeto) && $id_projeto != '-1') ? "AND b.id_projeto = {$id_projeto}" : "";

        // condicao para rhstatus
        $cond_status = ($consultaByNome) ? "AND a.status NOT IN(40,90,60,101,81,62,61,64,66,65,63)" : "AND a.status = '{$rhstatus}'"; // se tiver nome, é pra pegar todos os status
        // se for consulta por nome, limpa a consulta por inicial
        $cond_iniciaol = ($consultaByNome) ? "" : $cond_iniciaol;

        // se for consulta por nome ou só em eventos, limpa o limit
        $limit = ($consultaByNome || $soEmEventos) ? "" : "LIMIT {$posicao},100";

        // se for só em eventos muda a condicao do status
        $cond_status = ($soEmEventos) ? "AND a.status NOT IN(10,40,90,60,101,81,62,61,64,66,65,63,200)" : $cond_status;

        // condição de datas que só vale se status nao for 10
        $datas = ($rhstatus != 10) ? ",(SELECT date_format(data, '%d/%m/%Y')  FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND status=1 ORDER BY id_evento DESC LIMIT 1) AS data,
                             (SELECT date_format(data_retorno, '%d/%m/%Y')  FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND status=1 ORDER BY id_evento DESC LIMIT 1) AS data_retorno,
                             (SELECT date_format(data_retorno_final, '%d/%m/%Y')  FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND status=1 ORDER BY id_evento DESC LIMIT 1) AS data_retorno_final,
                             (SELECT pericia FROM rhstatus WHERE codigo = a.status) AS pericia" : "";

        $query_participantes = "SELECT b.id_projeto, b.nome AS nome_projeto, a.id_curso, a.id_clt, a.status, a.nome,a.id_regiao,a.cpf,
                             (SELECT nome FROM curso WHERE id_curso = a.id_curso) as curso,
                             (SELECT especifica FROM rhstatus WHERE codigo = a.status) AS nome_status
                             $datas
                            FROM rh_clt a
                            LEFT JOIN projeto b
                            ON a.id_projeto = b.id_projeto
                            WHERE a.id_regiao = '{$id_regiao}'
                             AND b.status_reg = '1'
                             $cond_status
                             $cond_iniciaol
                             $cond_nome
                             $cond_proj
                            ORDER BY b.id_projeto, a.nome ASC
                            $limit";

//        if($_COOKIE['logado']){
//            echo $query_participantes;
//        }

        $qr_participantes = mysql_query($query_participantes);

        $total_participantes = mysql_num_rows($qr_participantes);
        while ($row_participante = mysql_fetch_assoc($qr_participantes)) {
            $participantes[$row_participante['id_projeto']][] = $row_participante;
        }
        return $participantes;
    }

    /*
     * Metodo para testar se clt esta em evento
     */

    public function testeCltEmEvento($id_clt, $data) {
        $query_teste = "SELECT * FROM rh_eventos
                    WHERE id_clt = '{$id_clt}' 
                    AND ('$data' < data_retorno OR '$data' < data) 
                    AND status = 1;";
//        echo $query_teste;
        $resp = mysql_query($query_teste) or die("Erro ao verificar se h&aacute; evento para CLT. ERRO:" . mysql_error());
        return (mysql_num_rows($resp) > 0) ? TRUE : FALSE;
    }

    /*
     * metodo para testar se clt esta em ferias ou tem feiras agendadas
     */

    public function testeCltEmFerias($id_clt, $data) {
        $query_teste = "SELECT * FROM rh_ferias
                    WHERE id_clt = '{$id_clt}' 
                    AND '$data' <= data_fim
                    AND status = 1;";
//        echo $query_teste;
        $resp = mysql_query($query_teste) or die("Erro ao verificar se h&aacute; f&eacute;rias para CLT. ERRO:" . mysql_error());
        return (mysql_num_rows($resp) > 0) ? TRUE : FALSE;
    }

    /*
     * MÉTODO QUE INDICA SE O FUNCIONÁRIO POSSUI OU NÃO MAIS DE 30 DIAS DE RETORNO DE LICENÇA MATERNIDADE PARA PODER SER RESCINDIDO.
     */

    public function rescisaoPosMaternidade($id_clt) {
        $query = "SELECT if (DATEDIFF(now(),data_retorno) < 30, 'N', 'S') as indicativo
                  FROM rh_eventos
                  WHERE id_clt = '{$id_clt}' AND status = 1 AND cod_status = 50
                  ORDER BY id_evento DESC
                  LIMIT 1";

        $resp = mysql_query($query) or die("Erro rescisaoPosMaternidade. ERRO:" . mysql_error());
        $row = mysql_fetch_assoc($resp);
        return $row;
    }
    
    /**
     * METODO PARA VERIFICAR PERIODO DE EVENTOS 
     * 
     * @param type $dataInicio
     * @param type $dataFinal
     */
    public function verificaPeriodoDeEvento($dataInicio, $dataFinal, $idClt){
        
        $mesesTotalEmEvento = 0;
        
        $query = "SELECT *,  date_format(A.data_retorno, '%d/%m/%Y') as data_retornoBR, date_format(A.data, '%d/%m/%Y') as data_iniBR 
                    FROM rh_eventos AS A
                    WHERE (A.`data` >= '{$dataInicio}' AND A.data_retorno <= '{$dataFinal}') 
                    AND A.`status` > 0 AND A.id_clt = '{$idClt}'";        
        
//        echo $query = "SELECT *,  date_format(A.data_retorno, '%d/%m/%Y') as data_retornoBR
//                    FROM rh_eventos AS A
//                    WHERE ((A.data BETWEEN '{$dataInicio}' AND '{$dataFinal}') OR (A.data_retorno BETWEEN '{$dataInicio}' AND '{$dataFinal}')) 
//                    AND A.`status` > 0 AND A.id_clt = '{$idClt}'";
                    
        $sql = mysql_query($query) or die('ERRO AO SELECIONAR EVENTOS POR PERIODO');
        while($rows = mysql_fetch_assoc($sql)){
                         
//            $dataInicio = date("d/m/Y", strtotime(str_replace("/", "-", $dataInicio)));
            $dataInicio = $rows['data_iniBR'];
            $dataFinal = $rows['data_retornoBR'];
             
            
            /**
            * SINESIO LUIZ
            * 15/02/2017
            * Usa a função criada e pega 
            * o timestamp das duas datas:
            */
            $time_inicial = geraTimestamp($dataInicio);
            $time_final = geraTimestamp($dataFinal);

           /**
            * SINESIO LUIZ
            * 15/02/2017
            * Calcula a diferença de 
            * segundos entre as duas datas:
            */
            $diferencaDiasEventos = $time_final - $time_inicial; // 19522800 segundos

           /**
            * SINESIO LUIZ
            * 15/02/2017
            * Calcula a diferença de dias
            */
            $diasTotalEmEvento = (int)floor( $diferencaDiasEventos / (60 * 60 * 24)); // 225 dias
            $mesesTotalEmEvento += $diasTotalEmEvento;
//                    
            
        } 
        
        return $mesesTotalEmEvento;
        
    }
    
    public function mesesEmEvento($ultimo_event) {
        
//        foreach($ultimo_event['dados'] as $key => $dadosEventos){
            $data_inicial_evento = $ultimo_event['dados']['data'];
            $data_final_evento   = $ultimo_event['dados']['data_retorno'];

            $begin = new DateTime($data_inicial_evento);
            $end = new DateTime($data_final_evento);
            $end = $end->modify( '+1 day' ); 

            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($begin, $interval ,$end);
            
            //DEBUG
            if(in_array($_COOKIE['logado'], $this->programadores)){
                echo "<br>===================================DATAS PARA CALCULO DE AVOS 13º PROP=========================================<br>"; 
            }

            foreach($daterange as $date_evento){
                $count_evento++;

                if($mes_atual != $date_evento->format("m")){
                    $mes_atual = $date_evento->format("m");
                    $count_evento = 0;
                }
                
                //DEBUG
                if(in_array($_COOKIE['logado'], $this->programadores)){
                   echo $date_evento->format("d-m-Y") . "<br>";
                }

                if($count_evento == 14){
                    //DEBUG
                    if(in_array($_COOKIE['logado'], $this->programadores)){
                        echo "+ 1 mês...<br>";
                    }
                    $m_eventos++;
                }
            }
            
            //DEBUG
            if(in_array($_COOKIE['logado'], $this->programadores)){
                echo "<br> Meses: " . $m_eventos  . "<br><br>";
            }
//        }
            
        //debug periodos
        if(in_array($_COOKIE['logado'], $this->programadores)){
           echo $data_inicial_evento . ' - ' . $data_final_evento . '<br>';
           echo "Avos de Eventos Décimo Terceiro Proporcional: " . round($m_eventos) . "<br>";
           echo "<br>=======================================================================================================<br>"; 
        }
        
        return $m_eventos;
    }

}
