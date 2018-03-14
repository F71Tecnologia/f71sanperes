<?php

/*
 * 
 * 
 * novo dao 
 * 
 * mudanças no banco
 *  rh_vt_matricula -> tipo_cartao, status, alterado_por, alterado_em
 * 
 */

abstract class IDao {

    private static $movimento;
    private static $id_user;
    private static $id_master;
    private static $id_regiao;
    private static $tipo_cartao_default = '05';
    public static $id_mov = ID_MOV_VT; // ID DO MOVIMENTO DE VALE TRANSPORTE TIPO DÈBITO - CONFIGURE NO vale_transporte/define.php;
    private $obj;
    private $sequencia;

    function __construct() {
        if (isset($_COOKIE['logado'])) {
            $result = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '{$_COOKIE['logado']}'");
            $row = mysql_fetch_assoc($result);
            

            self::$id_user = $row['id_funcionario'];
            self::$id_master = $row['id_master'];
            self::$id_regiao = $row['id_regiao'];
        } else {
            echo 'Erro! Você precisa estar logado para essa operação!';
            exit();
        }
    }

    // retorna o array da consulta query
    function mysqlQueryToArray($qr) {
        $arr = array();
        while ($res = mysql_fetch_assoc($qr)) {
            $arr[] = $res;
        }
        return $arr;
    }

    function getIdUser() {
        return self::$id_user;
    }

    function getIdMaster() {
        return self::$id_master;
    }

    function getIdRegiao() {
        return self::$id_regiao;
    }

    function arrayPaginas() {
        return array('00 - PEDIDOS REALIZADOS', '01 - GERAR PEDIDO', '02 - Exportação de Usuários', '03 - Gerenciar Usuários', '04 - Gerenciar Tarifas', '05 - Gerenciar Concessionárias', '06 - Dias Úteis x Função', '07 - Dias Úteis x Funcionário'); //, '07 - Dias Úteis x Usuários'
    }
    function getLinhas() {
        return array(0=> 'Não especificado',1=> ' Municipal',2=> 'Intermunicipal'); //, '07 - Dias Úteis x Usuários'
    }

    // FILTROS CLTS
//    function montaFiltroClts($array) {
//
//        $projeto = $array['projeto'];
//        $sql_transporte = (isset($array['transporte']) && $array['transporte'] == '1') ? ' A.transporte = ' . $array['transporte'] . ' AND ' : '';
//        $sql_limit = (isset($array['limit']) && is_numeric($array['limit'])) ? ' LIMIT ' . $array['limit'] : '';
//
//        $sql_matricula = '';
//        $pos_matricula = strpos($array['matricula'], ",");
//        if ($pos_matricula) {
//            $arr_matricula = explode(',', $array['matricula']);
//            $sql_matricula = '(';
//            $or = '';
//            foreach ($arr_matricula as $matricula) {
//                $sql_matricula .= (!empty($matricula)) ? $or . ' C.matricula LIKE "%' . $matricula . '%" OR A.id_clt LIKE "%' . $matricula . '%"  ' : '';
//                $or = ' OR ';
//            }
//            $sql_matricula .= ') AND ';
//        } else {
//            $sql_matricula = (isset($array['matricula']) && !empty($array['matricula'])) ? ' (C.matricula LIKE "%' . $array['matricula'] . '%" OR A.id_clt LIKE "%' . $array['matricula'] . '%" )  AND ' : '';
//        }
//
//        $sql_cpf = (isset($array['cpf']) && !empty($array['cpf'])) ? ' A.cpf LIKE "%' . $array['cpf'] . '%" AND ' : '';
//        $sql_nome = (isset($array['nome']) && !empty($array['nome'])) ? ' A.nome LIKE "' . $array['nome'] . '%"  AND ' : '';
//        $mes = (isset($array['mes']) && !empty($array['mes'])) ? $array['mes'] : '';
//        $ano = (isset($array['ano']) && !empty($array['ano'])) ? $array['ano'] : '';
//
//        $sql_data_entrada = '';
//        if (isset($array['data_entrada']) && !empty($array['data_entrada'])) {
//            $data_entrada = explode('_', $array['data_entrada']);
//            $sql_data_entrada = ' (YEAR(A.data_entrada)=' . $data_entrada[1] . ' AND MONTH(A.data_entrada)= ' . $data_entrada[0] . ') AND ';
//        }
//
//        $sql_limit = (isset($array['limit']) && !empty($array['limit'])) ? ' LIMIT ' . $array['limit'] : '';
//        $sql_novos_sem_matricula = (isset($array['somente_novos']) && !empty($array['somente_novos'])) ? ' AND (C.matricula IS NULL OR CHAR_LENGTH(C.matricula)<0)  ' : '';
//
//        $qnt = (isset($array['dias_uteis']) && is_numeric($array['dias_uteis']) && $array['dias_uteis'] != "" && $array['dias_uteis'] != 0) ? $array['dias_uteis'] : '0'; // consertar pra pegar somente se enviar os dias (FEITO)
//
//        $calcular_dias = isset($array['calcular_dias']) ? $array['calcular_dias'] : FALSE;
//
//        $campos_valores = '';
//        $campos_dias_uteis = '';
////        
//        if ($calcular_dias) {
//
//            $campos_valores = ', ROUND((valor1+valor2+valor3+valor4+valor5+valor6), 2) as valor_diario, ROUND((dias_uteis*(valor1+valor2+valor3+valor4+valor5+valor6)),2) as valor_total_clt ';
//
//            $campos_dias_uteis = ', IF(D.id_clt=A.id_clt, D.dias_uteis, IF(D.dias_uteis>0 AND ((D.sempre=1) OR (D.mes="' . $mes . '" AND D.ano="' . $ano . '")), IF(A.rh_horario=D.id_rh_horario,D.dias_uteis, IF(A.id_curso = D.id_curso AND D.id_rh_horario=0, D.dias_uteis,' . $qnt . ')) ,' . $qnt . ' ))  AS dias_uteis,IF(B.id_tarifa1 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa1),0) AS valor1,
//                                   IF(B.id_tarifa2 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa2),0) AS valor2,
//                                   IF(B.id_tarifa3 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa3),0) AS valor3,
//                                   IF(B.id_tarifa4 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa4),0) AS valor4,
//                                   IF(B.id_tarifa5 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa5),0) AS valor5,
//                                   IF(B.id_tarifa6 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa6),0) AS valor6,
//                                   B.id_tarifa1,B.id_tarifa2,B.id_tarifa3,B.id_tarifa4,B.id_tarifa5,B.id_tarifa6';
//            $campos_dias_uteis .= ', LPAD(H.mes,2,"0") AS mes_afastamento, H.ano AS ano_afastamento, H.data_ini AS data_afastamento_ini, H.data_fim AS data_afastamento_fim ';
//        
//            $join = 'LEFT JOIN rh_ferias AS H ON(A.id_clt=H.id_clt AND LPAD(H.mes,2,"0") =' . str_pad($mes,2,'0') . ' AND H.ano="' . $ano . '" AND H.`status`=1)';
//        }
//        $sql = 'SELECT *
//                ' . $campos_valores . '                
//                           FROM (
//                                   SELECT A.id_clt, I.nome AS nome_cbo, I.cod AS numero_cbo, A.id_curso, E.cbo_codigo, A.data_nasci, A.sexo, A.rg, A.orgao AS rg_orgao, A.cpf, A.tel_fixo AS telefone, A.email, IF(C.matricula IS NULL, A.id_clt, IF(CHAR_LENGTH(C.matricula)>0,C.matricula,A.id_clt)) AS matricula, IF(C.tipo_cartao IS NULL OR (CHAR_LENGTH(C.tipo_cartao)<=0), "' . self::$tipo_cartao_default . '", C.tipo_cartao)  AS tipo_cartao, A.id_projeto,A.nome, A.data_entrada, DATE_FORMAT(A.data_entrada,"%d/%m/%Y") AS data_entrada_f, B.cartao1,B.cartao2, A.transporte, E.nome AS nome_curso, F.nome AS nome_horario, A.id_regiao, A.status, G.especifica AS nome_status, D.id_vt_dias_uteis  '
//                . '                 ' . $campos_dias_uteis . '
//                                       FROM rh_clt AS A 
//                                        LEFT JOIN rh_vale AS B ON (A.id_clt = B.id_clt)
//                                        LEFT JOIN rh_vt_matricula  AS C ON (A.id_clt=C.id_clt AND C.status=1)
//                                        LEFT JOIN rh_vt_dias_uteis AS D ON (IF(D.id_clt>0,D.id_clt=A.id_clt, (A.id_regiao=D.id_regiao AND (A.id_curso=D.id_curso OR A.rh_horario=D.id_rh_horario)AND ((D.mes="' . $mes . '" AND D.ano="' . $ano . '") OR D.sempre=1)))  AND D.status=1    )
//                                        LEFT JOIN curso AS E ON (A.id_curso=E.id_curso)
//                                        LEFT JOIN rh_horarios AS F ON (A.rh_horario=F.id_horario)
//                                        LEFT JOIN rhstatus AS G ON(A.status=G.codigo) 
//                                        LEFT JOIN rh_cbo AS I ON(E.cbo_codigo=I.id_cbo) '.
//                                        $join
//                                        .' WHERE ' . $sql_data_entrada . ' ' . $sql_transporte . ' ' . $sql_matricula . ' ' . $sql_nome . ' ' . $sql_cpf . ' A.id_projeto = ' . $projeto . ' AND ( A.`status` IN(10,40) OR (A.status=200 AND DATE_FORMAT(A.data_demi,"%Y-%m")>"'.$ano.'-'.str_pad($mes,2,'0', STR_PAD_LEFT).'") )  AND (A.status_demi=0 OR A.status_demi IS NULL) ' . $sql_novos_sem_matricula . '
//                                        GROUP BY A.id_clt ORDER BY A.nome
//                           ) AS foo ' . $sql_limit;
//        echo '<!-- '.$sql.' -->';
//        return $sql;
//    }
    function montaFiltroClts($array) {

        $projeto = $array['projeto'];
        $sql_transporte = (isset($array['transporte']) && $array['transporte'] == '1') ? ' A.transporte = ' . $array['transporte'] . ' AND ' : '';
        $sql_limit = (isset($array['limit']) && is_numeric($array['limit'])) ? ' LIMIT ' . $array['limit'] : '';

        $sql_matricula = '';
        $pos_matricula = strpos($array['matricula'], ",");
        if ($pos_matricula) {
            $arr_matricula = explode(',', $array['matricula']);
            $sql_matricula = '(';
            $or = '';
            foreach ($arr_matricula as $matricula) {
                $sql_matricula .= (!empty($matricula)) ? $or . ' C.matricula LIKE "%' . $matricula . '%" OR A.id_clt LIKE "%' . $matricula . '%"  ' : '';
                $or = ' OR ';
            }
            $sql_matricula .= ') AND ';
        } else {
            $sql_matricula = (isset($array['matricula']) && !empty($array['matricula'])) ? ' (C.matricula LIKE "%' . $array['matricula'] . '%" OR A.id_clt LIKE "%' . $array['matricula'] . '%" )  AND ' : '';
        }

        $sql_cpf = (isset($array['cpf']) && !empty($array['cpf'])) ? ' A.cpf LIKE "%' . $array['cpf'] . '%" AND ' : '';
        $sql_nome = (isset($array['nome']) && !empty($array['nome'])) ? ' A.nome LIKE "' . $array['nome'] . '%"  AND ' : '';
        $mes = (isset($array['mes']) && !empty($array['mes'])) ? $array['mes'] : '';
        $ano = (isset($array['ano']) && !empty($array['ano'])) ? $array['ano'] : '';

        $sql_data_entrada = '';
        if (isset($array['data_entrada']) && !empty($array['data_entrada'])) {
            $data_entrada = explode('_', $array['data_entrada']);
            $sql_data_entrada = ' (YEAR(A.data_entrada)=' . $data_entrada[1] . ' AND MONTH(A.data_entrada)= ' . $data_entrada[0] . ') AND ';
        }

        $sql_limit = (isset($array['limit']) && !empty($array['limit'])) ? ' LIMIT ' . $array['limit'] : '';
        $sql_novos_sem_matricula = (isset($array['somente_novos']) && !empty($array['somente_novos'])) ? ' AND (C.matricula IS NULL OR CHAR_LENGTH(C.matricula)<0)  ' : '';

        $qnt = (isset($array['dias_uteis']) && is_numeric($array['dias_uteis']) && $array['dias_uteis'] != "" && $array['dias_uteis'] != 0) ? $array['dias_uteis'] : '0'; // consertar pra pegar somente se enviar os dias (FEITO)

        $calcular_dias = isset($array['calcular_dias']) ? $array['calcular_dias'] : FALSE;

        $campos_valores = '';
        $campos_dias_uteis = '';
//        
        if ($calcular_dias) {

            $campos_valores = ', ROUND((valor1+valor2+valor3+valor4+valor5+valor6), 2) as valor_diario, ROUND((valor1+valor2+valor3+valor4+valor5+valor6),2) as valor_total_clt ';

            $campos_dias_uteis = ', IF(B.id_tarifa1 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa1),0) AS valor1,
                                   IF(B.id_tarifa2 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa2),0) AS valor2,
                                   IF(B.id_tarifa3 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa3),0) AS valor3,
                                   IF(B.id_tarifa4 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa4),0) AS valor4,
                                   IF(B.id_tarifa5 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa5),0) AS valor5,
                                   IF(B.id_tarifa6 > 0,(SELECT CAST( REPLACE(valor, ",", ".") as decimal(13,2)) FROM rh_tarifas WHERE id_tarifas = B.id_tarifa6),0) AS valor6,
                                   B.id_tarifa1,B.id_tarifa2,B.id_tarifa3,B.id_tarifa4,B.id_tarifa5,B.id_tarifa6';
            $campos_dias_uteis .= ', LPAD(H.mes,2,"0") AS mes_afastamento, H.ano AS ano_afastamento, H.data_ini AS data_afastamento_ini, H.data_fim AS data_afastamento_fim ';

            $join = 'LEFT JOIN rh_ferias AS H ON(A.id_clt=H.id_clt AND LPAD(H.mes,2,"0") =' . str_pad($mes, 2, '0') . ' AND H.ano="' . $ano . '" AND H.`status`=1)';
        }
        $sql = 'SELECT *
                ' . $campos_valores . '                
                           FROM (
                                   SELECT A.id_clt,A.rh_horario, I.nome AS nome_cbo, I.cod AS numero_cbo, A.id_curso, E.cbo_codigo, A.data_nasci, A.sexo, A.rg, A.orgao AS rg_orgao, A.cpf, A.tel_fixo AS telefone, A.email, IF(C.matricula IS NULL, A.id_clt, IF(CHAR_LENGTH(C.matricula)>0,C.matricula,A.id_clt)) AS matricula, IF(C.tipo_cartao IS NULL OR (CHAR_LENGTH(C.tipo_cartao)<=0), "' . self::$tipo_cartao_default . '", C.tipo_cartao)  AS tipo_cartao, A.id_projeto,A.nome, A.data_entrada, DATE_FORMAT(A.data_entrada,"%d/%m/%Y") AS data_entrada_f, B.cartao1,B.cartao2, A.transporte, E.nome AS nome_curso, F.nome AS nome_horario, A.id_regiao, A.status, G.especifica AS nome_status  '
                . '                 ' . $campos_dias_uteis . '
                                       FROM rh_clt AS A 
                                        LEFT JOIN rh_vale AS B ON (A.id_clt = B.id_clt)
                                        LEFT JOIN rh_vt_matricula  AS C ON (A.id_clt=C.id_clt AND C.status=1)
                                        
                                        LEFT JOIN curso AS E ON (A.id_curso=E.id_curso)
                                        LEFT JOIN rh_horarios AS F ON (A.rh_horario=F.id_horario)
                                        LEFT JOIN rhstatus AS G ON(A.status=G.codigo) 
                                        LEFT JOIN rh_cbo AS I ON(E.cbo_codigo=I.id_cbo) ' .
                $join
                . ' WHERE ' . $sql_data_entrada . ' ' . $sql_transporte . ' ' . $sql_matricula . ' ' . $sql_nome . ' ' . $sql_cpf . ' A.id_projeto = ' . $projeto . ' AND ( A.`status` IN(10,40) OR (A.status=200 AND DATE_FORMAT(A.data_demi,"%Y-%m")>"' . $ano . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '") )  AND (A.status_demi=0 OR A.status_demi IS NULL) ' . $sql_novos_sem_matricula . '
                                        GROUP BY A.id_clt ORDER BY A.nome
                           ) AS foo ' . $sql_limit;
        echo '<!-- ' . $sql . ' -->';
        return $sql;
    }

    function calcularDiasUteisClt($ids_clt) {
        /* sobrescreve se tiver lançado pelo id_clt */
        $sql_dias = "SELECT * FROM rh_vt_dias_uteis WHERE id_clt IN(" . implode(',', $ids_clt) . ") AND status=1";
//        echo '<br> CLT: '.$sql_dias.'<br>';
        $result = mysql_query($sql_dias);
        while ($row = mysql_fetch_array($result)) {
            $arr_dias_clt[$row['id_clt']] = $row['dias_uteis'];
        }
        return $arr_dias_clt;
    }

    function calcularDiasUteisCbo($ids_cbo, $array) {
        /* sobrescreve se tiver lançado pelo CBO */
        $sql_c = "SELECT * FROM rh_vt_dias_uteis WHERE id_cbo IN(" . implode(',', array_keys($ids_cbo)) . ") AND status=1  ORDER BY sempre DESC;";
//        echo $sql_c.'<br>';
        $result = mysql_query($sql_c);
        while ($row = mysql_fetch_array($result)) {
            if ($row['sempre'] == 1 || ($row['mes'] == $array['mes'] && $row['ano'] == $array['ano'] )) {
                $arr_cbo[$row['id_cbo']] = $row['dias_uteis'];
            }
        }
        return $arr_cbo;
    }

    function calcularDiasUteisFuncao($ids_funcao, $array) {
        /* sobrescreve se tiver lançado pela Função */
        $sql_f = "SELECT * FROM rh_vt_dias_uteis WHERE id_curso IN(" . implode(',', array_keys($ids_funcao)) . ") AND status=1  ORDER BY sempre DESC;";
        $result = mysql_query($sql_f);
        if($_COOKIE['logado']==39){
//                        echo '<br>CURSO '.$sql_f.'<br>';
            }

        $arr_funcao = array();
        while ($row = mysql_fetch_array($result)) {
            
            if($_COOKIE['logado']==39){
//                echo '<br>';
//                echo '<br>';
//                echo $array['mes'].'/'.$array['ano'];
//                echo '<br>';
//                echo $row['mes'].'/'.$row['ano'];
//                echo '<br>';
//                print_r($row);
//                echo '<br>';
//                echo '<br>';
            }
            
            if (($row['sempre'] == 1) || ($row['mes'] == $array['mes'] && $row['ano'] == $array['ano'])) {
                $arr_funcao[$row['id_curso']] = $row['dias_uteis'];
            }
        }
        if($_COOKIE['logado']==39){
//            print_r($arr_funcao);
        }
        return $arr_funcao;
    }

    function calcularDiasUteisHorario($ids_horario, $array) {
        /* sobrescreve se tiver lançado pela Função */
        $sql_f = "SELECT * FROM rh_vt_dias_uteis WHERE id_rh_horario IN(" . implode(',', array_keys($ids_horario)) . ") AND status=1  ORDER BY sempre DESC;";
        $result = mysql_query($sql_f);
//        if($_COOKIE['logado']==39){
//            echo '<br>CURSO '.$sql_f.'<br>';
//        }
        while ($row = mysql_fetch_array($result)) {
            if ($row['sempre'] == 1 || ($row['mes'] == $array['mes'] && $row['ano'] == $array['ano'] )) {
                $arr_funcao[$row['id_curso']] = $row['dias_uteis'];
            }
        }
        return $arr_funcao;
    }

    function filtrarClts($array, $calcular_dias = 'todos') {




        // contém os funcionários do pedido
        $arr_clt = array();

        // arrays  =  contém os ids de campos do pedido
        $ids_cbo = array();
        $ids_funcao = array();
        $ids_horario = array();



        $sql = $this->montaFiltroClts($array);
        $qr = mysql_query($sql);

        while ($res = mysql_fetch_assoc($qr)) {
            $arr_clt[$res['id_clt']] = $res;
            $ids_cbo[$res['cbo_codigo']] = $res['dias_uteis'];
            $ids_funcao[$res['id_curso']] = $res['dias_uteis'];
            $ids_horario[$res['rh_horario']] = $res['dias_uteis'];
        }

        $ids_clt = array_keys($arr_clt);

        if ($calcular_dias == 'todos') {

            $arr_dias_clt = $this->calcularDiasUteisClt($ids_clt);

            $arr_cbo = $this->calcularDiasUteisCbo($ids_cbo, $array); // contém os cbos que estão com dias úteis para sobreescrever           


            $arr_funcao = $this->calcularDiasUteisFuncao($ids_funcao, $array); // contém as funções que estão com dias úteis para sobreescrever
            
//            if($_COOKIE['logado']==39){
//                print_r($ids_funcao);
//                echo '<br>';
//                print_r($arr_funcao);
//            }

            $arr_horario = $this->calcularDiasUteisHorario($ids_horario, $array); // contém os horários que estão com dias úteis para sobreescrever 
        } elseif ($calcular_dias == 'clt') {
            $arr_dias_clt = $this->calcularDiasUteisClt($ids_clt);
        }

        $arr = array();
        
//        $debug = $_COOKIE['logado'];
//        
//        var_dump($debug);
//        echo '<br>';
        
        foreach ($arr_clt as $res) {

            $arr_ini = explode('-', $res['data_afastamento_ini']);
            $arr_fim = explode('-', $res['data_afastamento_fim']);
            $arr_ini = array('dia' => $arr_ini[2], 'mes' => $arr_ini[1], 'ano' => $arr_ini[0]);
            $arr_fim = array('dia' => $arr_fim[2], 'mes' => $arr_fim[1], 'ano' => $arr_fim[0]);
            $res['dias_afastamento'] = count(CalendarioClass::getDias($arr_ini, $arr_fim, TRUE));

            // CBO
            $res['dias_uteis'] = (isset($arr_cbo[$res['cbo_codigo']]['dias_uteis']) && !empty($arr_cbo[$res['cbo_codigo']]['dias_uteis'])) ? $arr_cbo[$res['cbo_codigo']]['dias_uteis'] : $array['dias_uteis'];

            // FUNÇÂO
            
            $res['dias_uteis'] = (isset($arr_funcao[$res['id_curso']]) && !empty($arr_funcao[$res['id_curso']])) ? $arr_funcao[$res['id_curso']] : $res['dias_uteis'];
            // 431, 362
//            if($debug==39){
//                
//                echo '<br> CURSO '.$res['id_curso'];
//                echo '<br> F =>';
//                var_dump($arr_funcao[$res['id_curso']]);
////                var_dump($arr_funcao[$res['id_curso']]);
////                echo '<br>';
//            }

            //HORÁRIO
//            $res['dias_uteis'] = (isset($arr_horario[$res['rh_horario']]) && !empty($arr_horario[$res['rh_horario']])) ? $arr_horario[$res['rh_horario']] : $res['dias_uteis'];
//            if($debug==39){
//                echo '<br> H =>';
//                var_dump($arr_horario[$res['rh_horario']]);
//                print_r($arr_horario);
//            
////                exit($res['id_curso']);
//            }
            
            //CLT
            $res['dias_uteis'] = (isset($arr_dias_clt[$res['id_clt']]) && (!empty($arr_dias_clt[$res['id_clt']]) || $arr_dias_clt[$res['id_clt']] == 0)) ? $arr_dias_clt[$res['id_clt']] : $res['dias_uteis'];

//            if($debug==39){
//                echo '<br> CLT =>';
//                var_dump($arr_dias_clt[$res['id_clt']]);
//            
//               echo '<br>';
//            }

            // alteração feita em 10/04/2015 por Leonardo para consertar erro na quantidade de dias de afastamento e dias uteis
            $res['dias_afastamento'] = ($res['dias_afastamento'] > $res['dias_uteis'])?$res['dias_uteis']:$res['dias_afastamento'];
            
            $res['dias_trabalhados'] = ($res['dias_uteis'] - $res['dias_afastamento']);

            $res['valor_total'] = ($res['dias_trabalhados'] * $res['valor_diario']);

            $arr[] = $res;
        }
        return $arr;
    }

    function getRelacaoPedido($id_pedido, $calcular_desconto = FALSE) {

        $sql_join = '';
        $sql_campos = '';

        if ($calcular_desconto) {

            $sql_join = "LEFT JOIN (  SELECT id_clt   FROM rh_movimentos_clt AS T
                        WHERE T.mes_mov=@var_mes AND T.ano_mov=@var_ano AND id_mov='" . self::$id_mov . "' AND status = 1) AS G ON (A.id_clt=G.id_clt)";
            $sql_join .= "LEFT JOIN curso AS H ON(C.id_curso = H.id_curso)";

            $sql_campos = ', IF(G.id_clt IS NULL, "n","s") AS movimento_mes_existente';
            $sql_campos .= ', H.salario, (H.salario*0.06) AS desconto';
            $sql_campos .= ', (A.dias_uteis * A.vt_valor_diario) AS recarga_mes,  IF((A.dias_uteis * A.vt_valor_diario) >= (H.salario*0.06), (H.salario*0.06), (A.dias_uteis * A.vt_valor_diario)) AS desconto_folha';
        }

        //INNER JOIN rh_vt_matricula AS D ON A.id_clt=D.id_clt
        $sql = 'SELECT  B.id_vt_pedido ' . $sql_campos . ', C.id_regiao, F.id_projeto, F.nome AS nome_projeto, A.id_clt, IF(E.matricula IS NULL,A.id_clt ,E.matricula) AS matricula, C.nome AS nome, A.dias_uteis, A.vt_valor_diario,@var_mes := B.mes AS mes, @var_ano := B.ano AS ano, D.nome AS nome_projeto 
                FROM rh_vt_relatorio AS A 
                INNER JOIN rh_vt_pedido AS B ON A.id_vt_pedido = B.id_vt_pedido                
                LEFT JOIN rh_clt AS C ON A.id_clt = C.id_clt INNER JOIN projeto AS D ON B.projeto = D.id_projeto 
                LEFT JOIN rh_vt_matricula AS E ON (A.id_clt = E.id_clt AND E.`status`=1)
                LEFT JOIN projeto AS F ON(B.projeto=F.id_projeto)'
                . $sql_join .
                'WHERE B.id_vt_pedido=' . $id_pedido . ' ';
//        if($calcular_desconto){
//        }
//            echo $sql.'<br>';
        return $this->mysqlQueryToArray(mysql_query($sql));
    }

    function deletarPedido($id_vt_pedido) {
        $sql = 'UPDATE rh_vt_pedido SET `status`=0 WHERE id_vt_pedido=' . $id_vt_pedido . ' AND user=' . self::$id_user . '  LIMIT 1';
        return mysql_query($sql);
    }

    function getMovimentoVt() {
        $sql = 'SELECT id_mov, cod, descicao, categoria FROM rh_movimentos WHERE id_mov=' . self::$id_mov . ';';
        return mysql_fetch_assoc(mysql_query($sql));
    }

    function checarMovimentosVtLancados($id_pedido, $sobreescrever = FALSE) {
        $arr_relacao = $this->getRelacaoPedido($id_pedido, TRUE);

        if (!empty($arr_relacao)) {

            $arr_id_clt = array();

            foreach ($arr_relacao as $row) {
                $arr_id_clt[] = $row['id_clt'];
            }
            $ids_clt = implode(',', $arr_id_clt);

            $mes = $arr_relacao[0]['mes'];
            $ano = $arr_relacao[0]['ano'];

            $sql_select = "SELECT  A.id_movimento, A.id_mov, A.mes_mov, A.ano_mov, A.cod_movimento, A.tipo_movimento, A.nome_movimento, A.valor_movimento, A.id_clt, C.nome AS nome_clt, A.user_cad, B.nome AS lancado_por, DATE_FORMAT(A.data_movimento,'%d/%m/%Y') AS lancado_em FROM rh_movimentos_clt AS A
                            LEFT JOIN funcionario AS B ON(A.user_cad=B.id_funcionario)
                            LEFT JOIN rh_clt AS C ON(A.id_clt=C.id_clt)
                            WHERE A.mes_mov=$mes AND A.ano_mov=$ano AND A.id_mov=" . self::$id_mov . " AND A.status = 1 AND A.id_clt IN($ids_clt);";

            $result = mysql_query($sql_select);
            $arr = $this->mysqlQueryToArray($result);


            if ($sobreescrever && !empty($arr)) {

                $ids_movimentos_lancados = array();
                foreach ($arr as $row) {
                    $ids_movimentos_lancados[] = $row['id_movimento'];
                }
                if (!empty($ids_movimentos_lancados)) {
                    $ids_movimentos_lancados = implode(',', $ids_movimentos_lancados);
                    $sql_update_mov = "UPDATE rh_movimentos_clt SET status=0 WHERE id_movimento IN($ids_movimentos_lancados) AND mes_mov=$mes AND ano_mov=$ano AND id_mov=" . self::$id_mov . "; ";

                    mysql_query($sql_update_mov);

                    $result = mysql_query($sql_select);
                    return $this->mysqlQueryToArray($result);
                    exit();
                }
            }


            return $arr;
        }
    }

    function lancarMovimentosVt($id_pedido) {

        $arr_relacao = $this->getRelacaoPedido($id_pedido, TRUE);

        $movimento = $this->getMovimentoVt();


        if (!empty($movimento) && !empty($arr_relacao)) {

            $mes = $arr_relacao[0]['mes'];
            $ano = $arr_relacao[0]['ano'];
            $data_movimento = date('Y-m-d');

            $sql_cad_mov = "INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,id_folha,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,qnt,dt,status,status_folha,status_ferias,status_reg) VALUES ";

            foreach ($arr_relacao as $row) {
                $values .= "('{$row['id_clt']}','{$row['id_regiao']}','{$row['id_projeto']}','0','{$mes}','{$ano}','{$movimento['id_mov']}','{$movimento['cod']}','{$movimento['categoria']}','{$movimento['descicao']}','{$data_movimento}','" . self::$id_user . "','{$row['desconto_folha']}','','1',',,','','0','1','0','1','1'),";
            }
            $sql_cad_mov .= substr($values, 0, -1) . ';';

            mysql_query($sql_cad_mov);

            if (mysql_insert_id() > 0) {
                $sql_update = "UPDATE rh_vt_pedido SET status=2 WHERE id_vt_pedido=$id_pedido LIMIT 1;";
                mysql_query($sql_update);
                return $id_pedido;
            } else {
                return mysql_insert_id();
            }
        }
    }

    function logMovimento($arr) {

        $this->obj = new txt();

        $arr = array();

        for ($x = 0; $x < count($arr); $x++) {

//            $arr[$x]['id_regiao']
//            $arr[$x]['id_projeto']
//            $arr[$x]['mes']
//            $arr[$x]['ano']
//            $arr[$x]['id_mov']
//            $arr[$x]['cod']
//            $arr[$x]['categoria']
//            $arr[$x]['descicao']
//            $arr[$x]['data_movimento']
//            $arr[$x]['id_user']
//            $arr[$x]['desconto_folha']

            $this->obj->dados($this->obj->completa($arr[''], 5, '0', 'antes'));
//            $this->obj->filler(32);
            $this->obj->fechalinha('');

            $caminho = str_replace("classes", "arquivos", dirname(__FILE__)) . DIRECTORY_SEPARATOR;
            $arr_nome = array('PROJ.' . $id_projeto, 'DOC.' . $cnpj, 'PED.' . $id_vt_pedido, 'COMP.' . $ano . str_pad($mes, 2, '0'), 'USER.' . self::$id_user, 'PROC.' . date('Ymd.hi'));


            $nome_arquivo = implode('_', $arr_nome) . '.txt';

            $nome_download = implode('_', array('PEDIDO', '0100', $cnpj, date('Ymd'), date('hi'))) . '.txt';

            $caminho .= $nome_arquivo;

            $arquivos_gerados[$x]['download'] = $nome_arquivo;
            $arquivos_gerados[$x]['name_file'] = $nome_download;


            $fp = fopen($caminho, "a");
            fwrite($fp, $this->obj->arquivo);
            fclose($fp);
        }

        return array('arquivos' => $arquivos_gerados, 'info' => $info);
    }

    //ITEM 1    

    function finalizarPedido($array) {


        $sql = "INSERT INTO rh_vt_pedido (mes,ano,projeto,valor_total,user,status,arquivo,cnpj)  
           VALUES ('{$array['mes']}','{$array['ano']}','{$array['projeto']}',''," . self::$id_user . ",1,'','{$array['cnpj']}')";

        mysql_query($sql);

        $id_insert = mysql_insert_id();

        $sql = 'INSERT INTO rh_vt_relatorio (`id_vt_pedido`, `id_clt`,`dias_uteis`,`vt_valor_diario`) VALUES';
        foreach ($array['arr_cls'] as $row) {
            $valor_total_clt = ($row['valor_diario'] * $row['dias_trabalhados']);
            if ($valor_total_clt != 0) {
                $sql .= '("' . $id_insert . '","' . $row['id_clt'] . '","' . $row['dias_trabalhados'] . '","' . $row['valor_diario'] . '"),';
            }
        }
        $sql = substr($sql, 0, -1) . ';';
//        echo $sql.'<br>';
        mysql_query($sql);
    }

    function getCnpjPedido($id_pedido) {
        $sql = "SELECT REPLACE( REPLACE( REPLACE(REPLACE(cnpj,'-',''),'/',''),'.',''),',','') AS cnpj FROM rh_vt_pedido WHERE id_vt_pedido=$id_pedido";
        $result = mysql_query($sql);
        $arr = mysql_fetch_assoc($result);
        return $arr['cnpj'];
    }

    private function headerArquivo($cnpj) {

        $this->obj->dados($this->obj->completa($this->obj->limpar($this->sequencia), 5, '0', 'antes'));
        $this->obj->dados('01'); //01 = Header (tipo de arquivo)
        $this->obj->dados('PEDIDO'); //Referência do nome do arquivo
        $this->obj->dados('01.00'); //Versão do arquivo
        $this->obj->dados($this->obj->completa($cnpj, 14, '0', 'antes'));
        $this->obj->filler(32);
        $this->obj->fechalinha('');
    }

    private function footerArquivo($total_pedido) {
        $this->obj->dados($this->obj->completa($this->sequencia, 5, '0', 'antes'));
        $this->obj->dados('99');
        $this->obj->dados($this->obj->completa($this->obj->limpar($total_pedido), 10, '0', 'antes'));
        $this->obj->filler(17);
        $this->obj->fechalinha('');
    }

    function gerarArquivo($relacao) {

        $this->obj = new txt();


        $limite_clts = 200; // determina o máximo de clts em um arquivo de importação.
        $cont = 1;
        $key = 0;

        $id_vt_pedido = 0;

        $info = array();

        $arr = array();
        foreach ($relacao as $row) {
            $arr[$key][] = $row;
            if ($cont % $limite_clts == 0) {
                $key++;
            }
            $id_vt_pedido = $row['id_vt_pedido'];
            $id_projeto = $row['id_projeto'];
            $mes = $row['mes'];
            $ano = $row['ano'];


            $info['id_pedido'] = $id_vt_pedido;
            $info['id_projeto'] = $row['id_projeto'];
            $info['nome_projeto'] = $row['nome_projeto'];
            $info['ano'] = $row['ano'];
            $info['mes'] = $row['mes'];

            $cont++;
        }


        $cnpj = $this->getCnpjPedido($id_vt_pedido);

        $this->sequencia = 1;


        $arquivos_gerados = array();

        for ($x = 0; $x < count($arr); $x++) {

            $total_pedido = 0;

            $this->headerArquivo($cnpj);

            foreach ($arr[$x] as $row) {
                $this->sequencia++;
                $total_pedido += $row['recarga_mes'];

                $this->obj->dados($this->obj->completa($this->sequencia, 5, '0', 'antes'));
                $this->obj->dados($this->obj->completa('02', 2, '0', 'antes'));
                $this->obj->dados($this->obj->completa($row['matricula'], 15));
                $this->obj->dados($this->obj->completa($this->obj->limpar(number_format($row['recarga_mes'], 2, '', '')), 8, '0', 'antes'));
                $this->obj->filler(32);
                $this->obj->fechalinha('');
            }
            $this->sequencia++;
            $total_pedido = number_format($total_pedido, 2, '.', '');

            $this->footerArquivo($total_pedido);

            $caminho = str_replace("classes", "arquivos", dirname(__FILE__)) . DIRECTORY_SEPARATOR;
//            $caminho .= "PEDIDO_0100_{$qntFiles}" . $array['cnpj'] . '_' . date('Ymd') . '_' . date('hi') . '.txt';

            $arr_nome = array('PROJ.' . $id_projeto, 'DOC.' . $cnpj, 'PED.' . $id_vt_pedido, 'COMP.' . $ano . str_pad($mes, 2, '0'), 'USER.' . self::$id_user, 'PROC.' . date('Ymd.hi'));


            $nome_arquivo = implode('_', $arr_nome) . '.txt';

            $nome_download = implode('_', array('PEDIDO', '0100', $cnpj, date('Ymd'), date('hi'))) . '.txt';

            $caminho .= $nome_arquivo;

            $arquivos_gerados[$x]['download'] = $nome_arquivo;
            $arquivos_gerados[$x]['name_file'] = $nome_download;


//            $filename[$qntFiles] = "PEDIDO_0100_{$qntFiles}" . $cnpj_limpo . '_' . date('Ymd') . '_' . date('hi') . '.txt';

            $fp = fopen($caminho, "a");
            fwrite($fp, $this->obj->arquivo);
            fclose($fp);

//            echo '<pre>';
//            echo $this->obj->arquivo;
//            echo '</pre>';

            $this->obj->arquivo = '';
        }

        return array('arquivos' => $arquivos_gerados, 'info' => $info);
    }

    // ITEM 2

    function getTipoRegistro() {
        return array("02" => "Inclusão de Usuário", "03" => "Alteração de Usuário"); //,"04"=>"Pedido de Recarga"
    }

    private static $versao_arquivo_exportacao = '04.02';
    private static $limite_arquivo_exportacao = 200;

    private function cabecalhoArquivoExportacaoClts($obj, Array $array) {

        $cidades = '02'; //02=RJ
        $rederecarga = '01'; //01=ONIBUS
        $sequencia_header = 1;
        $tipo_cartao_padrao = '05'; // 05 - Bilhete Único.  OBS: Tipo de cartão padrão para os clts que não tenha esse campo cadastrado 
//        $array_cartao_tipos = array_keys($this->getTipoCartao());
        $obj->dados($obj->completa($obj->limpar($sequencia_header), 5, '0', 'antes'));
        $obj->dados('01'); //01 = Header (tipo de arquivo)
        $obj->dados('CADUSU'); //Referência do nome do arquivo
        $obj->dados(self::$versao_arquivo_exportacao); //Versão do arquivo
        $obj->dados($obj->completa($obj->limpar($array['cnpj']), 14, ' ', 'antes'));
        $obj->fechalinha();
    }

    private function rodapeArquivoExportacaoClts($obj, $sequencia) {
        $obj->dados($obj->completa($obj->limpar($sequencia), 5, '0', 'antes'));
        $obj->dados('99');
        $obj->fechalinha();
    }

    function salvaMatricula(Array $arr, $novo_usuario = FALSE) {
        // QUERY 1 - ATUALIZA MATRICULA DE STATUS=1 PARA 0 PARA STATUS=O CLT 
        $sql_del = 'UPDATE rh_vt_matricula SET `status`=0, alterado_por=' . self::$id_user . ' WHERE id_clt=' . $arr['id_clt'] . ';';
        // QUERY 2 - ADICIONA A NOVA MATRICULA PARA O CLT
        $sql_insert = 'INSERT INTO rh_vt_matricula(id_clt, matricula, tipo_cartao, status, alterado_por) VALUES(' . $arr['id_clt'] . ',"' . $arr['matricula'] . '","' . $arr['tipocartao'] . '","1","' . self::$id_user . '");';

//só cadastra a matricula caso o clt não existir em rh_vt_matricula
        if ($novo_usuario) {
            $sql = "SELECT * FROM rh_vt_matricula WHERE id_clt=$arr[id_clt];";
            $qr = mysql_query($sql);
            $num = mysql_num_rows($qr);
            if ($num <= 0) {
                mysql_query($sql_del);
                mysql_query($sql_insert);
            }
        } else {
            mysql_query($sql_del);
            mysql_query($sql_insert);
        }
    }

    function exportarClts($array, $cnpj = '') {

        $obj = new txt();
        $cnpj = $obj->limpar($cnpj);
        $cidades = "02";
        $rederecarga = "01";

//        Todos os campos numéricos devem ser alinhados à direita e completados com zeros à 
//        esquerda. 
// 
//        Exemplo: O Campo Nr_doc_comprd é numérico de 14 posições. 
//        56597564152  00056597564152 
// 
//        - Todos os campos com valores serão representados com 02 (duas) casas decimais, sem a 
//        vírgula, devem ser alinhados à direita e completados com zeros à esquerda. 
// 
//        Exemplo: O Campo Vl_uso_diário é numérico de 6 posições. 
//        R$ 3,85  000385 
// 
//        - Todos os campos alfa devem ser alinhados à esquerda e completados com espaços em 
//        branco à direita. 
// 
//        - Todos os campos que apresentarem o asterisco ( * ) são opcionais, e devem sem 
//        preenchidos com espaços em branco caso não tenham informações. 


        $sql = $this->montaFiltroClts($array);


        $res = mysql_query($sql);

        $quantidade_registros = mysql_num_rows($res);

        $tiporegistro = $array['tipo_registro']; // 02= inclusão de usuário 03 = Alteração de usuário
        $sequencia = 0;
        $contador_registros = 1;

        $array_arquivos == array();

        while ($clt = mysql_fetch_array($res)) {
            $contador_registros++;
            if (($sequencia % self::$limite_arquivo_exportacao) === 0 || $sequencia == 1) {
                $obj->arquivo = '';
                $this->cabecalhoArquivoExportacaoClts($obj, $array);
                $sequencia = 2;
            } else {
                $sequencia++;
            }


            $data_nascimento = date("d-m-Y", strtotime(str_replace("-", "-", $clt['data_nasci'])));

            $tipo_cartao_padrao = in_array($clt['tipo_cartao'], array_keys($this->getTipoCartao())) ? $clt['tipo_cartao'] : $tipo_cartao_padrao;

            $obj->dados($obj->completa($obj->limpar($sequencia), 5, '0', 'antes'));
            $obj->dados($obj->completa($obj->limpar($tiporegistro), 2));
            $obj->dados($obj->completa($clt['matricula'], 15, ' ', 'depois'));
            $obj->dados($obj->completa(preg_replace("/(  +)/i", " ", $obj->nome($clt['nome'])), 40, ' ', 'depois'));
            $obj->dados($obj->completa($obj->limpar($clt['cpf']), 11, ' ', 'depois'));
            $obj->dados($obj->completa(' ', 6)); //pode ser branco number_format($clt['valor_diario'], 2, '', '')
            $obj->dados($obj->completa($obj->limpar($cidades), 2));
            $obj->dados($obj->completa($obj->limpar($rederecarga), 2));
            $obj->dados($obj->completa($obj->limpar($clt['cartao1']), 13, ' ', 'depois'));
            $obj->dados($obj->completa($clt['tipo_cartao'], 2));
            $obj->dados($obj->completa($obj->limpar($data_nascimento), 8));
            $obj->dados($obj->completa($obj->limpar($clt['sexo']), 1));
            $obj->dados($obj->completa($obj->limpar($clt['rg']), 15, ' ', 'depois'));
            $obj->dados($obj->completa($obj->limpar($clt['rg_orgao']), 6, ' ', 'depois'));
            $obj->dados($obj->completa($obj->limpar($clt['telefone']), 13, ' ', 'depois'));
            $obj->dados($obj->completa($obj->nome($clt['email']), 60, ' ', 'depois'));
            $obj->fechalinha();

            if ((($sequencia + 1) % self::$limite_arquivo_exportacao) === 0 || ( $contador_registros == ($quantidade_registros + 1))) {
                $sequencia++;
                $this->rodapeArquivoExportacaoClts($obj, $sequencia);
                $array_arquivos[] = $obj->arquivo;
            }

            $this->salvaMatricula(array('id_clt' => $clt['id_clt'], 'matricula' => $clt['matricula'], 'tipo_cartao' => $clt['matricula']), TRUE);
        }

        $diretorio = 'arquivos/';

        if (!is_dir($diretorio)) {
            mkdir($diretorio);
        }

        $link = '';
        $arr = array();
        foreach ($array_arquivos as $key => $dados_arquivo) {

            /*
             *  nome: ?CADUSU?
             *  numero versão = 4 posições sem pontos
             *  cnpj = 14 posições
             *  data geraçao do arquivo = aaaammdd
             *  hora geração do arquivo = hhmm 
             *  OBS: TUDO SEPARADO POR UNDERSCORE "_" EX. ?CADUSU_0200_223334440000155_20050112_1525.txt?
             */

            $nome_file_save = 'CADUSU_' . self::$versao_arquivo_exportacao . "_" . $cnpj . "_" . date('Ymd_Hi') . '.txt';
            $nome_file_download = 'CADUSU_' . self::$versao_arquivo_exportacao . "_" . $cnpj . "_" . date('Ymd_Hi') . '.txt';

            //arquivo a salvar
            $caminho = $diretorio . $nome_file_save;


            if (file_exists($caminho))
                unlink($caminho);
            $fp = fopen($caminho, "a");
            $escreve = fwrite($fp, $dados_arquivo);
            fclose($fp);


            $arr['arquivos'][] = array('download' => $nome_file_save, 'name_file' => $nome_file_download);




//            $textarea = '<textarea id="arquivo_txt" style="width:100%; height: 500px; white-space: nowrap">' . $dados_arquivo . '</textarea>';
//            $link .= '&nbsp; <a href="?download=' . $nome_file_save . '&name_file=' . $nome_file_download . '" >Baixar Arquivo ' . ($key + 1) . '</a>';
        }
//        echo json_encode(array('textarea' => $textarea, 'link' => $link));
        return $arr;
        exit();
    }

    // ITEM 3

    function atualizarClts($dados) {

        /*
         * ATUALIZANDO OS DADOS UTILIZANDO 4 QUERYS IMPORTANTES
         */


        foreach ($dados as $clt) {

            $id_clt = trim($clt['id_clt']);
            $matricula = utf8_decode($clt['matricula']);
            $transporte = trim($clt['transporte']);
            $tipocartao = trim($clt['tipocartao']);
            $cartao1 = trim($clt['cartao1']);



            // QUERY 1 - ATUALIZA MATRICULA DE STATUS=1 PARA 0 PARA STATUS=O CLT 
            $sql_del = 'UPDATE rh_vt_matricula SET `status`=0, alterado_por=' . self::$id_user . ' WHERE id_clt=' . $id_clt . ';';
//            echo $sql_del.'<br>';
            mysql_query($sql_del);

            // QUERY 2 - ADICIONA A NOVA MATRICULA PARA O CLT
            $sql_insert = 'INSERT INTO rh_vt_matricula(id_clt, matricula, tipo_cartao, status, alterado_por) VALUES(' . $id_clt . ',"' . $matricula . '","' . $tipocartao . '","1","' . self::$id_user . '");';
//            echo $sql_insert.'<br>';
            mysql_query($sql_insert);

            // QUERY 3 - ATUALIZA A FLAG TRANSPORTE EM RH_CLT
            $sql_update_rh_clt = 'UPDATE rh_clt  SET `transporte`="' . $transporte . '" WHERE `id_clt`=' . $id_clt . ' LIMIT 1;';
//            echo $sql_update_rh_clt.'<br>';
            mysql_query($sql_update_rh_clt);

            // QUERY 4 - ATUALIZA O NÚMERO DO CARTÃO EM RH_VALE
            $sql_update_rh_vale = 'UPDATE rh_vale  SET `cartao1`="' . $cartao1 . '" WHERE `id_clt`=' . $id_clt . ' LIMIT 1;';
//            echo $sql_update_rh_vale.'<br>';
            mysql_query($sql_update_rh_vale);

//                echo $sql_del.'<br>';
//                echo $sql_insert.'<br>';
//                echo $sql_update_rh_clt.'<br>';
//                echo $sql_update_rh_vale.'<br><br><br>';
        }
//            exit('ok');
        return TRUE;
    }

    function getTipoCartao() {
//        return array(
//            "01" => "VT Rio Card ao Portador",
//            "02" => "VT Rio Card do Comprador",
//            "03" => "VT Rio Card do Comprador/Usuário",
//            "04" => "VT Rio Card Individual",
//            "05" => "Bilhete Único Inter. do Usuário",
//            "06" => "Bilhete Único Carioca do Usuário",
//            "07" => "VT Rio Card com Migração de Impressão/Personalizado"
//        );
        return array(
            "02" => "Rio Card do Comprador (Cartão Laranja)",
            "05" => "Bilhete Único Inter. do Usuário",
            "06" => "Bilhete Único Carioca do Usuário"
        );
    }

    //ITEM 4

    function getRegioesFuncionario() {
        $sql = "SELECT A.id_regiao,B.regiao FROM funcionario_regiao_assoc AS A
                                LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                                WHERE   id_funcionario = " . self::$id_user . " AND 
                                        A.id_master = " . self::$id_master . " ORDER BY A.id_regiao;";
        $query = mysql_query($sql);
        $regiao = array();
        while ($row = mysql_fetch_array($query)) {
            $regiao[$row['id_regiao']] = $row['id_regiao'] . ' - ' . $row['regiao'];
        }
        return $regiao;
    }

    function getItinerarios() {
        return array('IDA' => 'IDA', 'VOLTA' => 'VOLTA');
    }

    function salvarTarifa($dados) {
        $id_regiao = isset($dados['id_regiao']) ? $dados['id_regiao'] : ''; //novo
        $itinerario = isset($dados['itinerario']) ? $dados['itinerario'] : NULL;
        $descricao = isset($dados['descricao']) ? $dados['descricao'] : NULL;
        $id_concessionaria = isset($dados['id_concessionaria']) ? $dados['id_concessionaria'] : NULL;
        $linha = isset($dados['linha']) ? $dados['linha'] : NULL;
        $valor = isset($dados['valor']) ? str_replace('R$ ', '', str_replace(',', '.', $dados['valor'])) : NULL;

        $sql = 'INSERT INTO rh_tarifas (`tipo`,`valor`,`itinerario`,`descricao`,`id_concessionaria`, `id_user`, `data` , `id_regiao`, `linha`, `status_reg`) '
                . 'VALUES("CARTÃO","' . $valor . '","' . $itinerario . '","' . $descricao . '","' . $id_concessionaria . '","' . self::$id_user . '","' . date('Y-m-d') . '","' . $id_regiao . '","' . $linha . '",1);';

//        echo $sql.'<br>';
//        $sql = 'INSERT INTO rh_vt_tarifa (`tipo_tarifa`,`nome`,`valor`,`user`,`status`) VALUES(' . $tipo_tarifa . ',"' . $nome . '",' . $valor . ',' . self::$id_user . ',1);';
        mysql_query($sql);
        return mysql_insert_id();
    }

    function atualizarTarifa(Array $dados) {

        foreach ($dados as $dado) {
            $id_tarifa = isset($dado['id_tarifa']) ? $dado['id_tarifa'] : NULL;
            $id_regiao = isset($dado['id_regiao']) ? $dado['id_regiao'] : '';
            $itinerario = isset($dado['itinerario']) ? $dado['itinerario'] : NULL;
            $descricao = isset($dado['descricao']) ? $dado['descricao'] : NULL;
            $id_concessionaria = isset($dado['id_concessionaria']) ? $dado['id_concessionaria'] : NULL;
            $valor = isset($dado['valor']) ? str_replace('R$ ', '', str_replace(',', '.', $dado['valor'])) : NULL;
            $linha = isset($dado['linha']) ? $dado['linha'] : NULL;
            
            
            //id da tarifa, id de quem fez, valor antigo, valor novo, data e hora

            $sql_update = 'UPDATE rh_tarifas SET `tipo`="CARTÃO", `valor`="' . $valor . '", '
                    . '`itinerario`="' . $itinerario . '" , '
                    . '`descricao`="' . $descricao . '" , '
                    . '`id_concessionaria`="' . $id_concessionaria . '" , '
                    . '`id_user`=' . self::$id_user . ' , '
                    . '`data`="' . date('Y-m-d') . '", '
                    . '`id_regiao`=' . $id_regiao . ',`linha`=' . $linha . ', `status_reg`=1 WHERE id_tarifas=' . $id_tarifa . ' LIMIT 1';
            
            
            
//            echo $sql."\n";
            
            // rotina pra gravar log
            $qr_t = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas=$id_tarifa");
            $row_tarifa = mysql_fetch_array($qr_t);
            
            $dados_log = array();
            $dados_log['itinerario_antigo'] = $row_tarifa['itinerario'];
            $dados_log['itinerario_novo'] = $itinerario;
            $dados_log['descricao_antigo'] = $row_tarifa['descricao'];
            $dados_log['descricao_novo'] = $descricao;
            $dados_log['id_concessionaria_antigo'] = $row_tarifa['id_concessionaria'];
            $dados_log['id_concessionaria_novo'] = $id_concessionaria;
            $dados_log['valor_antigo'] = $row_tarifa['valor'];
            $dados_log['valor_novo'] = $valor;
            $dados_log['linha_antigo'] = $row_tarifa['linha'];
            $dados_log['linha_novo'] = $linha;
            $dados_log['alterado_por'] = self::$id_user;
            
            $campos = implode(',',array_keys($dados_log));
            $valores = implode(',',array_values($dados_log));
            
            $sql_log = "INSERT rh_tarifas_historico SET id_tarifas=$id_tarifa, ";
            foreach($dados_log as $campo=>$valor){
                $sql_log .= '`'.$campo.'`="'.$valor.'", ';
            }
            $sql_log = substr(trim($sql_log),0,-1);
            
//            echo $sql_log."\n";
            
            mysql_query($sql_update);
            mysql_query($sql_log);
            
//            exit();
            // fim da rotina pra gravar log
            
            
            
            
        }
        return TRUE;
    }

    function deletarTarifa($id_tarifa) {
        $sql = 'UPDATE rh_tarifas SET `status_reg`=0 WHERE id_tarifas=' . $id_tarifa . '  LIMIT 1';
        return mysql_query($sql);
    }

    function getTarifas($dados = array()) {
        $dados['id_regiao'] = isset($dados['id_regiao']) ? $dados['id_regiao'] : self::$id_regiao;
//        $sql = 'SELECT * FROM rh_vt_tarifa WHERE `status`=1 ORDER BY `id_vt_tarifa` DESC';
        $sql = 'SELECT A.*, B.nome AS nome_concessionaria, B.tipo AS tipo_concessionaria,'
                . '(SELECT COUNT(*) FROM rh_vale 
WHERE (id_tarifa1=A.id_tarifas OR id_tarifa2=A.id_tarifas OR id_tarifa3=A.id_tarifas OR id_tarifa4=A.id_tarifas OR id_tarifa5=A.id_tarifas OR id_tarifa6=A.id_tarifas)
AND status_reg=1) AS vinculos '
                . ' FROM rh_tarifas AS A LEFT JOIN rh_concessionarias AS B ON (A.id_concessionaria=B.id_concessionaria) '
                . 'WHERE A.`status_reg`=1 AND A.id_regiao=' . $dados['id_regiao'] . ' ORDER BY A.`id_tarifas` DESC';
//        echo $sql.'<br>';
        return $this->mysqlQueryToArray(mysql_query($sql));
    }

    // retorna empresa do projeto
    function getEmpresaByProjeto($id_projeto = NULL) {
        $w = !is_null($id_projeto) ?  "A.id_projeto = {$id_projeto} AND " : ' ';
        $sql = "SELECT  A.id_projeto, A.nome AS nome_projeto, B.id_empresa, B.nome AS nome_empresa,  
            REPLACE( REPLACE( REPLACE(REPLACE(B.cnpj,'-',''),'/',''),'.',''),',','') AS cnpj_empresa,
            B.id_master, 
            REPLACE( REPLACE( REPLACE(REPLACE(C.cnpj,'-',''),'/',''),'.',''),',','') AS cnpj_master
            FROM projeto AS A 
            INNER JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto) 
            INNER JOIN `master` AS C ON(B.id_master=C.id_master)
            WHERE $w B.id_regiao = A.id_regiao";
        return mysql_fetch_assoc(mysql_query($sql));
    }

    // retorna o master
    function getMaster($id_master = '0') {
        $sql = "SELECT *, REPLACE( REPLACE( REPLACE(REPLACE(cnpj,'-',''),'/',''),'.',''),',','') AS cnpj from `master` WHERE id_master=$id_master";
        return mysql_fetch_assoc(mysql_query($sql));
    }

    function getProjetos($id_regiao, $encode = FALSE) { // item 1, item 2, item 3, item 4, item 5
//        $sql = 'SELECT * FROM projeto';
        $sql = "SELECT  A.id_projeto, A.nome AS nome_projeto, B.id_empresa, B.nome AS nome_empresa,  B.cnpj AS cnpj_empresa, A.cnpj  
                FROM projeto AS A 
                INNER JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto) 
                WHERE A.id_regiao='$id_regiao'";
//        echo $sql.'<br>';
        $qr = mysql_query($sql);
        $projetos = array();
        while ($row = mysql_fetch_array($qr)) {
            $nome = ($encode) ? utf8_encode($row['id_projeto'] . ' - ' . $row['nome_projeto'] . ' ' . $row['cnpj_empresa']) : $row['id_projeto'] . ' - ' . $row['nome_projeto'] . ' ' . $row['cnpj_empresa'];
            $projetos[$row['id_projeto']] = $nome;
        }
        return $projetos;
    }

    function getCursosByRegiao($id_regiao, $encode = FALSE) {
        $sql = 'SELECT A.id_curso, A.nome FROM curso AS A WHERE id_regiao=' . $id_regiao . '  AND A.tipo=2 AND A.status=1 AND A.status_reg=1 ORDER BY A.nome';
        $qr = mysql_query($sql);
//        $cursos = array('todos' => 'Selecione um curso');
        $cursos = array();
        while ($row = mysql_fetch_array($qr)) {
            $cursos[$row['id_curso']] = ($encode) ? utf8_encode($row['id_curso'] . ' - ' . $row['nome']) : $row['id_curso'] . ' - ' . $row['nome'];
        }
        return $cursos;
    }

    function getCursosByCBO($id_cbo, $encode = FALSE) {
        $sql = 'SELECT A.id_curso, A.nome, B.id_regiao, B.regiao FROM curso AS A '
                . ' LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao) '
                . 'WHERE A.cbo_codigo=' . $id_cbo . '  AND A.tipo=2 AND A.status=1 AND A.status_reg=1 ORDER BY A.nome';
        $qr = mysql_query($sql);
//        exit($sql);
//        $cursos = array('todos' => 'Selecione um curso');
        $cursos = array();
        while ($row = mysql_fetch_array($qr)) {
            $cursos[$row['id_regiao']][$row['id_curso']] = ($encode) ? utf8_encode($row['id_curso'] . ' - ' . $row['nome']) : $row['id_curso'] . ' - ' . $row['nome'];
            $regioes[$row['id_regiao']] = ($encode) ? utf8_encode($row['regiao']) : $row['regiao'];
        }

        return array('cursos' => $cursos, 'regioes' => $regioes);
    }

    function getHorariosByCurso($id_curso, $encode = FALSE) {

        $sql = "SELECT * FROM rh_horarios WHERE funcao = $id_curso";

        $qr = mysql_query($sql);
        $horarios = array('todos' => ($encode) ? utf8_encode('Todos os Horários') : 'Todos os Horários');
        while ($row = mysql_fetch_array($qr)) {
            $horarios[$row['id_horario']] = ($encode) ? utf8_encode($row['id_horario'] . ' - ' . $row['nome']) : $row['id_horario'] . ' - ' . $row['nome'];
        }
        return $horarios;
    }

    function getPedidosAtivos($id_projeto = FALSE) {
        $w_projeto = ($id_projeto) ? ' AND A.projeto= "' . $id_projeto . '"' : '';
        $sql = 'SELECT A.id_vt_pedido, A.user, A.status, B.nome AS nome_funcionario, DATE_FORMAT(A.`data`, "%d/%m/%Y") AS criacao, A.mes, A.ano, A.valor_total, C.nome AS nome_projeto, A.projeto, (SELECT SUM((T.dias_uteis * T.vt_valor_diario)) FROM rh_vt_relatorio AS T WHERE T.id_vt_pedido=A.id_vt_pedido) AS valor_total FROM rh_vt_pedido AS A LEFT JOIN funcionario AS B ON (A.`user` = B.id_funcionario) LEFT JOIN projeto AS C ON (A.projeto = C.id_projeto) WHERE (A.status=1 OR A.status=2) ' . $w_projeto . '  ORDER BY ano,mes,id_vt_pedido DESC';
//        echo $sql.'<br>';
        return $this->mysqlQueryToArray(mysql_query($sql));
    }

    function getPedido($idVtPedido = NULL) {
        if ($idVtPedido) {
            $sql = 'SELECT A.id_vt_pedido, A.status, A.arquivo, B.nome AS nome_funcionario, DATE_FORMAT(A.`data`, "%d/%m/%Y") AS criacao, A.mes, A.ano, A.valor_total, C.nome AS nome_projeto  
                FROM rh_vt_pedido AS A 
                LEFT JOIN funcionario AS B ON (A.`user` = B.id_funcionario) 
                LEFT JOIN projeto AS C ON (A.projeto = C.id_projeto) 
                WHERE A.id_vt_pedido=' . $idVtPedido;
            return mysql_fetch_assoc(mysql_query($sql));
        } else {
            $sql = 'SELECT A.id_vt_pedido, A.status, A.arquivo, B.nome AS nome_funcionario, DATE_FORMAT(A.`data`, "%d/%m/%Y") AS criacao, A.mes, A.ano, A.valor_total, C.nome AS nome_projeto  
                FROM rh_vt_pedido AS A 
                LEFT JOIN funcionario AS B ON (A.`user` = B.id_funcionario) 
                LEFT JOIN projeto AS C ON (A.projeto = C.id_projeto);';
            return $this->mysqlQueryToArray(mysql_query($sql));
        }
    }

    function incluiPedidoFolha($idVtPedido, $idFuncionario = NULL, $and = '') {
        if ($idFuncionario) {
            $and .= " AND user='$idFuncionario' ";
        }
        $sql = 'UPDATE rh_vt_pedido SET `status`=2 WHERE id_vt_pedido=' . $idVtPedido . ' ' . $and . ' LIMIT 1';
        mysql_query($sql);
        return mysql_affected_rows();
    }

    function removePedidoFolha($idVtPedido, $idFuncionario = NULL, $and = '') {
        if ($idFuncionario) {
            $and .= " AND user='$idFuncionario' ";
        }
        $sql = 'UPDATE rh_vt_pedido SET `status`=1 WHERE id_vt_pedido=' . $idVtPedido . ' ' . $and . ' LIMIT 1';
        mysql_query($sql);
        return mysql_affected_rows();
    }

    //ITEM 5

    function getTiposConcessionarias($utf8_encode = FALSE) {
        $sql = 'SELECT * FROM rh_vt_tipo_concessionaria WHERE status_reg=1;';
        $qr = mysql_query($sql);
        $tipos_concessionarias = array();
        while ($res = mysql_fetch_array($qr)) {
            $nome = ($utf8_encode) ? utf8_encode($res['nome']) : $res['nome'];
            $tipos_concessionarias[$res['id_tipo_concessionaria']] = $nome;
        }
        return $tipos_concessionarias;
//        return array( 'NAO ESPECIFICADO', utf8_encode('ÔNIBUS'), utf8_encode('TRÊM'), utf8_encode('METRÔ'), 'BARCA');
    }

    private function queryConcessionarias($dados = array()) {
        $dados['id_regiao'] = isset($dados['id_regiao']) ? $dados['id_regiao'] : self::$id_regiao;
        $sql = 'SELECT A.*, B.nome AS tipo_concessionaria, C.id_regiao, C.regiao AS nome_regiao FROM rh_concessionarias AS A LEFT JOIN rh_vt_tipo_concessionaria AS B ON(A.`tipo`=B.`id_tipo_concessionaria`) LEFT JOIN regioes AS C ON(A.id_regiao=C.id_regiao)'
                . 'WHERE A.`status_reg`=1 AND A.id_regiao=' . $dados['id_regiao'] . ' ORDER BY A.`id_concessionaria` DESC';
//        echo $sql.'<br>';
        return mysql_query($sql);
    }

    function arrayConcessionarias($dados = array(), $utf8_decode = FALSE) {
        $qr = $this->queryConcessionarias($dados);
        $arr = array();
        while ($concessionaria = mysql_fetch_array($qr)) {
            $nome = ($utf8_decode) ? utf8_decode($concessionaria['nome']) : $concessionaria['nome'];
            $arr[$concessionaria['id_concessionaria']] = $nome;
        }
        return $arr;
////        return $this->mysqlQueryToArray(mysql_query($sql));
    }

    function getConcessionarias($dados = array()) {
        return $this->mysqlQueryToArray($this->queryConcessionarias($dados));
    }

    function salvarConcessionaria($dados) {
        $id_regiao = isset($dados['id_regiao']) ? $dados['id_regiao'] : ''; //novo
        $tipo = isset($dados['tipo']) ? $dados['tipo'] : NULL;
        $nome = isset($dados['nome']) ? $dados['nome'] : NULL;

        $sql = 'INSERT INTO rh_concessionarias (`id_regiao`,`id_user`,`nome`,`tipo`, `data`, `status_reg`) '
                . 'VALUES("' . $id_regiao . '", "' . self::$id_user . '" ,"' . $nome . '","' . $tipo . '","' . date('Y-m-d') . '",1);';
        mysql_query($sql);
        return mysql_insert_id();
    }

    function deletarConcessionaria($id_concessionaria) {
        $sql = 'UPDATE rh_concessionarias SET `status_reg`=0 WHERE id_concessionaria=' . $id_concessionaria . '  LIMIT 1';
        return mysql_query($sql);
    }

    function atualizarConcessionarias($dados) {

        $dados = isset($dados) ? $dados : array();

        foreach ($dados as $dado) {

            $id_concessionaria = isset($dado['id_concessionaria']) ? $dado['id_concessionaria'] : NULL;
            $id_regiao = isset($dado['id_regiao']) ? $dado['id_regiao'] : NULL;
            $nome = isset($dado['nome']) ? $dado['nome'] : NULL;
            $tipo = isset($dado['tipo']) ? $dado['tipo'] : NULL;

            $sql = 'UPDATE rh_concessionarias SET `id_regiao`="' . $id_regiao . '", '
                    . ' `nome`="' . $nome . '",  `tipo`="' . $tipo . '" '
                    . 'WHERE id_concessionaria=' . $id_concessionaria . '  LIMIT 1';
            return mysql_query($sql);
        }
    }

    // ITEM 6

    function getCBO($id_regiao = FALSE, $utf8_decode = FALSE) {

        $w = ($id_regiao) ? ' WHERE A.id_regiao= "' . $id_regiao . '"' : '';

        $sql = "SELECT A.id_curso, A.id_regiao, B.id_cbo, B.cod AS cbo, B.nome FROM curso AS A
                INNER JOIN rh_cbo AS B ON(A.cbo_codigo=B.id_cbo) $w GROUP BY B.id_cbo;";
        $qr = mysql_query($sql);

//        echo $sql.'<br>';

        $arr = array();
        while ($row = mysql_fetch_array($qr)) {
            $arr[$row['id_cbo']] = ($utf8_decode) ? utf8_encode($row['cbo'] . ' - ' . $row['nome']) : $row['cbo'] . ' - ' . $row['nome'];
        }
        return $arr;
    }

    function checkDiasUteis($dados, $mes, $ano, $sempre) {
        $w = ($sempre == 0) ? ' AND mes=' . $mes . ' AND ano=' . $ano : ' AND sempre=1';
        $sql = 'SELECT A.*, B.nome AS nome_curso, C.nome AS criado_por, DATE_FORMAT(A.criado_em, "%d/%m/%Y") AS criado_em_f
                    FROM rh_vt_dias_uteis AS A
                    LEFT JOIN curso AS B ON(A.id_curso=B.id_curso)
                    LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario) WHERE A.id_curso IN(' . implode(',', $dados['cursos']) . ') ' . $w . ' AND A.status=1';
//        echo $sql.'<br>';
        $qr = mysql_query($sql);
        return (mysql_num_rows($qr) > 0) ? $this->mysqlQueryToArray($qr) : array();
    }

    function salvarDiasUteis(Array $dadosArray) {
        foreach ($dadosArray as $dados) {
            
            $dados['id_clt'] = isset($dados['id_clt']) ? $dados['id_clt'] : '0';
            $dados['sempre'] = isset($dados['sempre']) ? $dados['sempre'] : '0';
            $dados['cbo'] = isset($dados['cbo']) ? $dados['cbo'] : '0';
            $dados['curso'] = (isset($dados['curso']) && $dados['curso'] == 'todos') ? '0' : $dados['curso'];
            $dados['horario'] = (isset($dados['horario']) && $dados['horario'] == 'todos') ? '0' : $dados['horario'];
            
            $print = FALSE;
            
            if ($dados['curso'] != 0) {
                $sql_r = "SELECT id_regiao FROM curso WHERE id_curso='".$dados['curso']."'";
                $res_r = mysql_fetch_array(mysql_query($sql_r));
                $dados['id_regiao'] = $res_r['id_regiao'];            
            
                $w = ($dados['sempre']!=0) ? ' sempre=1 ' : ' mes="' . $dados['mes'] . '" AND  ano="' . $dados['ano'] . '" ';
                $sql = 'UPDATE rh_vt_dias_uteis SET `status`=0, editado_por='.self::$id_user.'  WHERE id_curso="' . $dados['curso'] . '" AND '.$w;
                
                if($print){
                    echo $sql.'<br>';                    
                }
                mysql_query($sql);
            }else{
                $dados['id_regiao'] = isset($dados['id_regiao']) ? $dados['id_regiao'] : '0';
            }
            
            if ($dados['id_clt'] != 0) {
                $sql = 'UPDATE rh_vt_dias_uteis SET `status`=0, editado_por='.self::$id_user.' WHERE id_clt="' . $dados['id_clt'] . '";';
                if($print){
                    echo $sql.'<br>';
                }
                mysql_query($sql);
            }
            
            $sql = 'INSERT INTO rh_vt_dias_uteis(id_clt, sempre, `id_regiao`,  `id_curso`, `id_rh_horario`,`dias_uteis`, `mes`, `ano`, `user`, `criado_em`, `editado_por`, `alterado_em`, `status`) ' .
                    ' VALUES ("' . $dados['id_clt'] . '","' . $dados['sempre'] . '","' . $dados['id_regiao'] . '","' . $dados['curso'] . '","' . $dados['horario'] . '","' . $dados['dias_uteis'] . '","' . $dados['mes'] . '","' . $dados['ano'] . '","' . self::$id_user . '", NOW(), "0", NOW(), "1")';
            if($print){
                echo $sql.'<br><br><br>';
            }

            mysql_query($sql);
        }
        return TRUE;
    }

    function getDiasUteisAtivos($dados = array()) {
        $condicao_regiao = isset($dados['id_regiao']) ? ' AND (A.id_regiao=' . $dados['id_regiao'] . '  OR  A.id_cbo >0 )' : '';
        $sql = "SELECT A.id_vt_dias_uteis,A.dias_uteis,A.sempre , A.mes,A.ano,B.id_regiao, E.cod, A.id_cbo, E.nome AS nome_cbo,  "
                . "B.regiao, C.id_curso, C.nome AS nome_curso, D.id_horario, D.nome AS nome_horario,  IF(A.id_cbo>0,(SELECT COUNT(curso.id_curso) FROM curso WHERE curso.cbo_codigo=A.id_cbo),0) AS cursos_inclusos "
                . "FROM `rh_vt_dias_uteis` AS A LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao) LEFT JOIN curso AS C ON(A.id_curso=C.id_curso) LEFT JOIN rh_horarios AS D ON(A.id_rh_horario=D.id_horario) LEFT JOIN rh_cbo AS E ON(A.id_cbo=E.id_cbo) WHERE A.`status`=1 $condicao_regiao";

//        echo $sql.'<br>';

        return $this->mysqlQueryToArray(mysql_query($sql));
    }

    function deletarDiasUteis($id_dias_uteis) {
        $sql = 'UPDATE rh_vt_dias_uteis SET `status`=0 WHERE id_vt_dias_uteis=' . $id_dias_uteis . '  LIMIT 1';
        return mysql_query($sql);
    }

    function deletarDiasUteisClt($id_clt) {
        $sql = 'UPDATE rh_vt_dias_uteis SET `status`=0 WHERE id_clt=' . $id_clt . ';';
        return mysql_query($sql);
    }

}
