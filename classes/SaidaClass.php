<?php
require_once('c_planodecontasClass.php');
//class Saida extends c_planodecontasClass {

//require_once('LukeContabilPlanodeContasClass.php');
require_once('ApiClass.php');
//require_once('c_planodecontasClass.php');

class Saida extends c_planodecontasClass {

    private $master = 1;
    private $api;
    
    public function __construct() {
        $this->api = new ApiClass();
    }
    
    public function getSaida() {
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $dataIni = converteData($_REQUEST['data_ini']);
        $dataFim = converteData($_REQUEST['data_fim']);

        if ($banco == 'todos') {
            $result = "SELECT *, A.nome AS saida_nome, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data2, B.nome AS tipo_saida, C.nome AS banco_nome, C.agencia AS banco_agencia, C.conta AS banco_conta, D.nome AS nomeCadastrou, E.nome AS nomePagou
                FROM saida AS A
                LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
                LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                LEFT JOIN funcionario AS D ON (A.id_user = D.id_funcionario)
                LEFT JOIN funcionario AS E ON (A.id_userpg = E.id_funcionario)
                WHERE A.data_proc >= '{$dataIni} 00:00:00' AND A.data_proc <= '{$dataFim} 23:59:59' AND A.id_projeto = '{$projeto}' AND A.status = '2'
                ORDER BY A.data_vencimento";
        } else {
            $result = "SELECT *, A.nome AS saida_nome, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data2, B.nome AS tipo_saida, C.nome AS banco_nome, C.agencia AS banco_agencia, C.conta AS banco_conta, D.nome AS nomeCadastrou, E.nome AS nomePagou
                FROM saida AS A
                LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
                LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                LEFT JOIN funcionario AS D ON (A.id_user = D.id_funcionario)
                LEFT JOIN funcionario AS E ON (A.id_userpg = E.id_funcionario)
                WHERE A.data_proc >= '{$dataIni}' AND A.data_proc <= '{$dataFim}' AND A.id_projeto = '{$projeto}' AND A.status = '2' AND A.id_banco = '{$banco}'
                ORDER BY A.data_vencimento";
        }

        $saida = mysql_query($result) or die(mysql_error());
        return $saida;
    }

    public function getSaidaID($id_saida) {
        $qry = "SELECT A.*, DATE_FORMAT(A.dt_emissao_nf, '%d/%m/%Y') AS dt_emissao_nf_br, E.id_subgrupo subgrupo, E.nome AS nomeSubGrupo, CONCAT(F.id_grupo, ' | ', F.nome_grupo) AS nomeGrupo, CONCAT(A.tipo, ' | ', D.nome) AS nomeTipo,
            B.c_razao AS prestador_nome, B.c_cnpj AS prestador_cnpj, B.id_regiao AS prestador_id_regiao, B.id_projeto AS prestador_id_projeto,
            C.nome AS fornecedor_nome, C.cnpj AS fornecedor_cnpj, C.id_regiao AS fornecedor_id_regiao, C.id_projeto AS fornecedor_id_projeto, G.id_saida_pai AS id_saida_agrupamento
            FROM saida AS A
            LEFT JOIN prestadorservico AS B ON (A.id_prestador = B.id_prestador)
            LEFT JOIN fornecedores AS C ON (C.id_fornecedor = A.id_fornecedor)
            LEFT JOIN entradaesaida D ON (D.id_entradasaida = A.tipo)
            LEFT JOIN entradaesaida_subgrupo E ON (E.id_subgrupo = SUBSTR(D.cod, 1, 5))
            LEFT JOIN entradaesaida_grupo AS F ON(F.id_grupo = E.entradaesaida_grupo)
            LEFT JOIN saida_agrupamento_assoc AS G ON (A.id_saida = G.id_saida)
            WHERE A.id_saida = '{$id_saida}'";
        $sql = mysql_query($qry) or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getSaidaByID($id_saida) {
        $qry = "SELECT * FROM saida WHERE id_saida = '{$id_saida}'";
        $sql = mysql_query($qry) or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getSaidaBrGaap($data,$idSaida=0) {
        $_data = converteData($data);
        $_idSaida = "";
        if($idSaida > 0){
            $_idSaida = " AND id_saida >= {$idSaida}";
        }
        $sql = "SELECT F.cnpj, E.classificador as conta_contabil, E.descricao, C.id_nacional, C.nome AS banco_nome,
                        C.agencia,'' as digAg,C.conta, '' as digConta, A.saldo_anterior, 
                        '' as OperacaoID,'' as ClassifPagto,'' as TipoFornecedor,'' as DocTipo, 
                        '' as numoperacao, A.nosso_numero, A.data_vencimento as data, CAST( REPLACE(A.valor, ',', '.') as decimal(13,2))
                FROM saida AS A
                LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
                LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                LEFT JOIN contabil_contas_assoc_banco AS D ON(C.id_banco = D.id_banco)
                LEFT JOIN planodecontas AS E ON(D.id_conta = E.id_conta)
                LEFT JOIN rhempresa AS F ON (A.id_regiao = F.id_regiao)
                WHERE A.data_vencimento = '{$_data}' AND A.STATUS = '2' $_idSaida
                ORDER BY A.data_vencimento";

        $result = mysql_query($sql);
        $saida = $this->api->montaRetorno($result);
        return $saida;
    }

    public function getSaidaRel($regiao = null) {
        $banco = $_REQUEST['banco'];
        $dataIni = $_REQUEST['ano'] . "-" . $_REQUEST['mes'] . "-01";
        $dataFim = $_REQUEST['ano'] . "-" . $_REQUEST['mes'] . "-31 23:59:59";

//        A.ano_competencia = {$_REQUEST['ano']} AND A.mes_competencia = {$_REQUEST['mes']}
        if ($banco != 'todos') {
            $result = "SELECT A.*, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data_vencimento, A.tipo, CAST(
                REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor_nf, CAST(
                REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS saida_adicional, A.nome AS saida_nome, CONCAT(C.nome1, ' ', DATE_FORMAT(A.data_proc,'%d/%m/%Y %H:%s:%i')) AS func_nome,
                B.agencia AS banco_agencia, B.conta AS banco_conta, D.nome AS tipo_saida, D.grupo AS grupo_saida,
                F.nome_grupo AS grupo_saida, E.nome AS subgrupo_saida, CONCAT(G.nome1, ' ', DATE_FORMAT(A.data_pg,'%d/%m/%Y'), ' ', A.hora_pg) AS funcpg_nome, H.id_bordero, H.numero_cheque,
                IF(A.tipo = 327, CONCAT(TIMESTAMPDIFF(MONTH, I.contratado_em, A.data_vencimento), '/', TIMESTAMPDIFF(MONTH, I.contratado_em,I.encerrado_em)),A.n_documento) AS n_documento, 
                IF(A.data_edicao > '0000-00-00', CONCAT(J.nome1, ' ', DATE_FORMAT(A.data_edicao,'%d/%m/%Y %H:%s:%i')), '') AS func_editou
                FROM saida AS A
                LEFT JOIN bancos AS B ON(B.id_banco = A.id_banco)
                LEFT JOIN funcionario AS C ON(C.id_funcionario = A.id_user)
                LEFT JOIN entradaesaida AS D ON (A.tipo = D.id_entradasaida)
                LEFT JOIN entradaesaida_subgrupo AS E ON(E.id_subgrupo = SUBSTR(D.cod, 1, 5))
                LEFT JOIN entradaesaida_grupo AS F ON(F.id_grupo = E.entradaesaida_grupo)
                LEFT JOIN (SELECT A1.id_bordero, A1.id_saida, B1.numero_cheque, B1.id_funcionario FROM bordero_saidas A1 LEFT JOIN bordero B1 ON (A1.id_bordero = B1.id) WHERE B1.pago = 1 AND B1.status = 1 AND A1.status = 1) AS H ON (A.id_saida = H.id_saida)
                LEFT JOIN funcionario AS G ON(G.id_funcionario = IF(A.id_userpg, A.id_userpg, H.id_funcionario))
                LEFT JOIN prestadorservico I ON (A.id_prestador = I.id_prestador)
                LEFT JOIN funcionario AS J ON(J.id_funcionario = A.id_user_editou)
                WHERE A.data_vencimento >= '{$dataIni}' AND A.data_vencimento <= '{$dataFim}' AND A.id_banco = '{$banco}' AND A.status = '2'
                ORDER BY A.data_vencimento, A.id_banco";
        } else {
            $auxRegiao = ($regiao) ? " AND A.id_regiao = '{$regiao}'" : null;
            $result = "SELECT A.*, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data_vencimento, A.tipo, CAST(
                REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor_nf, CAST(
                REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS saida_adicional, A.nome AS saida_nome, CONCAT(C.nome1, ' ', DATE_FORMAT(A.data_proc,'%d/%m/%Y %H:%s:%i')) AS func_nome,
                B.agencia AS banco_agencia, B.conta AS banco_conta, D.nome AS tipo_saida, D.grupo AS grupo_saida,
                F.nome_grupo AS grupo_saida, E.nome AS subgrupo_saida, CONCAT(G.nome1, ' ', DATE_FORMAT(A.data_pg,'%d/%m/%Y'), ' ', A.hora_pg) AS funcpg_nome, H.id_bordero, H.numero_cheque,
                IF(A.tipo = 327, CONCAT(TIMESTAMPDIFF(MONTH, I.contratado_em, A.data_vencimento), '/', TIMESTAMPDIFF(MONTH, I.contratado_em,I.encerrado_em)),A.n_documento) AS n_documento, 
                IF(A.data_edicao > '0000-00-00', CONCAT(J.nome1, ' ', DATE_FORMAT(A.data_edicao,'%d/%m/%Y %H:%s:%i')), '') AS func_editou
                FROM saida AS A
                LEFT JOIN bancos AS B ON(B.id_banco = A.id_banco)
                LEFT JOIN funcionario AS C ON(C.id_funcionario = A.id_user)
                LEFT JOIN entradaesaida AS D ON (A.tipo = D.id_entradasaida)
                LEFT JOIN entradaesaida_subgrupo AS E ON(E.id_subgrupo = SUBSTR(D.cod, 1, 5))
                LEFT JOIN entradaesaida_grupo AS F ON(F.id_grupo = D.grupo)
                LEFT JOIN (SELECT A1.id_bordero, A1.id_saida, B1.numero_cheque, B1.id_funcionario FROM bordero_saidas A1 LEFT JOIN bordero B1 ON (A1.id_bordero = B1.id) WHERE B1.pago = 1 AND B1.status = 1 AND A1.status = 1) AS H ON (A.id_saida = H.id_saida)
                LEFT JOIN funcionario AS G ON(G.id_funcionario = IF(A.id_userpg, A.id_userpg, H.id_funcionario))
                LEFT JOIN prestadorservico I ON (A.id_prestador = I.id_prestador)
                LEFT JOIN funcionario AS J ON(J.id_funcionario = A.id_user_editou)
                WHERE A.data_vencimento >= '{$dataIni}' AND A.data_vencimento <= '{$dataFim}' $auxRegiao AND A.status = '2'
                ORDER BY A.data_vencimento, A.id_banco ASC";
        }
        if($_COOKIE['debug'] == 666) { print_array($result); }
        $saida = mysql_query($result) or die(mysql_error());
        return $saida;
    }

    public function getDetalhado($completeWhere) {

        $qrBase = "SELECT A.id_grupo,B.id as idsub,A.nome_grupo,B.id_subgrupo,B.nome AS subgrupo,C.cod,C.nome,C.id_entradasaida,
            COUNT(D.id_saida) AS qnt,
            SUM(CAST( REPLACE(D.valor, ',', '.') as decimal(13,2))) as total
            FROM entradaesaida_grupo AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
            LEFT JOIN entradaesaida AS C ON (C.cod LIKE CONCAT(B.id_subgrupo,'%'))
            LEFT JOIN (SELECT id_saida,tipo,
                                IF(estorno = 2, CAST((REPLACE(valor, ',', '.') - REPLACE(valor_estorno_parcial, ',', '.')) as DECIMAL(13,2)) , valor) as valor 
                                FROM saida WHERE $completeWhere) AS D ON (D.tipo=C.id_entradasaida)
            WHERE C.grupo >= 5";

        return $qrBase;
    }

    public function Detalhamento($completeWhere) {

        $qrBase1 = "SELECT A.tipo AS tipo, DATE_FORMAT(A.data_vencimento, '%d%m%Y') AS dtEscrituracao, A.valor AS valor, E.nome, C.c_razao, A.especifica, A.nome, 
                        COUNT(A.id_saida) AS qnt, SUM(CAST(REPLACE(A.valor, ',','.') AS decimal(13,2))) as total 
                        FROM saida AS A 
                        LEFT JOIN prestadorservico AS C ON(C.id_prestador = A.id_prestador) 
                        LEFT JOIN entradaesaida AS E ON(E.id_entradasaida = A.tipo)
                        LEFT JOIN entradaesaida_grupo AS G ON(G.id_grupo = E.grupo)
                        LEFT JOIN entradaesaida_subgrupo AS S ON(G.id_grupo = S.entradaesaida_grupo) 
                        WHERE A.valor > '0,00' AND $completeWhere
                        UNION ALL 
                        SELECT A.tipo AS tipo, DATE_FORMAT(A.data_vencimento, '%d%m%Y') AS dtEscrituracao, A.valor AS valor, '', '', A.especifica, A.nome, 
                        COUNT(A.id_entrada) AS qnt, SUM(CAST(REPLACE(A.valor, ',','.') AS decimal(13,2))) as total 
                        FROM entrada AS A 
                        LEFT JOIN entradaesaida AS B ON (B.id_entradasaida = A.tipo) 
                        LEFT JOIN projeto AS E ON (E.id_projeto = A.id_projeto) 
                        LEFT JOIN entradaesaida_grupo AS G ON(G.id_grupo = B.grupo)
                        LEFT JOIN entradaesaida_subgrupo AS S ON(S.entradaesaida_grupo = G.id_grupo) 
                        WHERE A.valor > '0,00' AND $completeWhere";
        return $qrBase1;
    }

    public function getDespesas($codigo, $where) {

        $ex = explode(".", $codigo);
        if (count($ex) == 3) {
            $cod = "cod = '{$codigo}'";
        } elseif (count($ex) == 2) {
            $cod = "B.id_subgrupo = '{$codigo}";
        } else {
            $cod = "A.id_grupo = '" . str_replace("0", "", $codigo) . "0'";
        }

        $qr = "SELECT D.*, CAST(
            REPLACE(D.valor, ',', '.') AS DECIMAL(13,2)) AS cvalor, DATE_FORMAT(data_vencimento, \"%d/%m/%Y\") AS dataBr,D.tipo
            FROM entradaesaida_grupo AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
            LEFT JOIN entradaesaida AS C ON (C.cod LIKE CONCAT(B.id_subgrupo,'%'))
            INNER JOIN (
            SELECT *
            FROM saida
            WHERE {$where}) AS D ON (D.tipo=C.id_entradasaida)
            WHERE $cod 
            ORDER BY C.cod";
        $result = mysql_query($qr);
        return $result;
    }

    public function getSaidaFile($id_saida) {
//        $qry = "SELECT * FROM saida_files WHERE id_saida = '{$id_saida}'";
        $qry = "SELECT D.*, B.id_regiao, B.id_clt, B.id_recisao, B.data_proc
                                        FROM saida AS A
                                        LEFT JOIN pagamentos_especifico AS C ON (A.id_saida = C.id_saida)
                                        LEFT JOIN saida_files AS D ON (A.id_saida = D.id_saida)
                                        LEFT JOIN rh_recisao AS B ON (C.id_clt = B.id_clt AND D.rescisao_complementar = B.rescisao_complementar AND B.`status` = 1)
                                        WHERE A.id_saida = '{$id_saida}' AND (id_saida_file > 0 OR id_recisao > 0);";
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getSaidaFilePg($id_saida) {
        $qry = "SELECT * FROM saida_files_pg WHERE id_saida = {$id_saida}";
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function dataVencimento($id_saida) {
        $qry = "SELECT *,DATE_FORMAT(data_vencimento, '%d/%m/%Y') as data_vencimentobr FROM saida WHERE id_saida = {$id_saida}";
        $sql = mysql_query($qry) or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getSaidaErro() {
        $arrayStatus = array(10, 20, 30, 40, 50, 51, 52);
        $status = implode(",", $arrayStatus);
        $id_regiao = $_REQUEST['regiao'];
        $id_projeto = $_REQUEST['projeto'];

        $qry = "SELECT D.nome as unidade, A.nome, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as dt_admissao,  E.nome as funcao, F.especifica
            FROM rh_clt as A
            LEFT JOIN projeto as D ON (D.id_projeto = A.id_projeto)
            INNER JOIN curso as E ON (E.id_curso = A.id_curso)
            LEFT JOIN rhstatus AS F ON (F.codigo = A.status)
            WHERE A.status IN({$status})
            AND A.id_regiao = '{$id_regiao}' AND A.id_projeto = '{$id_projeto}' ORDER BY A.nome";
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getFechamentoDetalhado() {
        $tipo = $_REQUEST['tipo'];
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $tipo_data = $_REQUEST['tipodata'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];

        $qry = "SELECT *, COUNT(A.tipo) AS tot, B.nome AS nome_tipo, SUM(REPLACE(A.valor, ',', '.')) as valor_tot, A.tipo AS tipo_id
            FROM {$tipo} AS A
            LEFT JOIN entradaesaida AS B ON(B.id_entradasaida = A.tipo)
            WHERE A.id_projeto = {$projeto} AND A.id_banco = {$banco} AND MONTH(A.{$tipo_data}) = {$mes} AND YEAR(A.{$tipo_data}) = {$ano} AND A.status = 2
            GROUP BY A.tipo
            ORDER BY A.tipo ASC";
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getMesFechamentoAnual() {
        $tipo = $_REQUEST['tipo'];
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $tipo_data = $_REQUEST['tipodata'];
        $ano = $_REQUEST['ano'];

        $qry = "SELECT MONTH(A.{$tipo_data}) AS mes_desc
            FROM {$tipo} AS A
            LEFT JOIN entradaesaida AS B ON(B.id_entradasaida = A.tipo)
            WHERE A.id_projeto = {$projeto} AND A.id_banco = {$banco} AND YEAR(A.{$tipo_data}) = {$ano} AND A.status = 2
            GROUP BY MONTH(A.{$tipo_data})
            ORDER BY MONTH(A.{$tipo_data}), A.tipo ASC";
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getFechamentoAnual($mes) {
        $tipo = $_REQUEST['tipo'];
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $tipo_data = $_REQUEST['tipodata'];
        $ano = $_REQUEST['ano'];

        $qry = "SELECT A.tipo, B.nome AS nome_tipo, SUM(REPLACE(A.valor, ',', '.')) as valor_tot, MONTH(A.{$tipo_data}) AS mes_ref, SUM(REPLACE(A.adicional, ',', '.')) as tot_adicional
            FROM {$tipo} AS A
            LEFT JOIN entradaesaida AS B ON(B.id_entradasaida = A.tipo)
            WHERE A.id_projeto = {$projeto} AND A.id_banco = {$banco} AND YEAR(A.{$tipo_data}) = {$ano} AND MONTH(A.$tipo_data) = {$mes} AND A.status = 2
            GROUP BY A.tipo, MONTH(A.{$tipo_data})
            ORDER BY MONTH(A.{$tipo_data}), A.tipo ASC";
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getValidacaoAnexo($id_regiao) {
        $projeto = $_REQUEST['projeto'];
        $dataIni = converteData($_REQUEST['data_ini']);
        $dataFim = converteData($_REQUEST['data_fim']);

        $qry = "SELECT A.id_saida AS saida_id, A.tipo, A.nome AS saida_nome, A.especifica AS saida_especifica, CAST(
            REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS saida_adicional, CAST(
            REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS saida_valor, A.comprovante AS saida_comprovante,
            DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') saida_datarecibemento, B.nome AS subgrupo_nome,
            C.nome_grupo AS grupo_nome, D.nome AS entradasaida_nome, E.conta AS banco_conta, E.agencia AS banco_agencia,
            F.nome AS funcionario_nome, G.nome AS funcionariopg_nome, A.data_vencimento
            FROM saida AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON(B.id = A.entradaesaida_subgrupo_id)
            LEFT JOIN entradaesaida_grupo AS C ON(C.id_grupo = B.entradaesaida_grupo)
            LEFT JOIN entradaesaida AS D ON(D.id_entradasaida = A.tipo)
            LEFT JOIN bancos AS E ON(E.id_banco = A.id_banco)
            LEFT JOIN funcionario AS F ON(F.id_funcionario = A.id_user)
            INNER JOIN funcionario AS G ON(G.id_funcionario = A.id_userpg)
            WHERE A.id_regiao = {$id_regiao} AND A.id_projeto = {$projeto} AND E.status_reg = 1 AND A.data_vencimento BETWEEN '{$dataIni}' AND '{$dataFim}'
            ORDER BY A.data_vencimento";

        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getBuscarLancamentoAgrupado($id_regiao = null) {
//        print_array($_REQUEST);;
        $ano = $_REQUEST['ano'];
//        $limite = $_REQUEST['limit'];

        $condicao = "";
        $condicao .= ($_REQUEST['id_banco']) ? " AND A.id_banco = '{$_REQUEST['id_banco']}'" : '';
        $condicao .= ($_REQUEST['grupo'] == "todos") ? "" : " AND F.grupo = '{$_REQUEST['grupo']}'";
        $condicao .= ($_REQUEST['subgrupo'] == "") ? "" : " AND F.cod LIKE '{$_REQUEST['subgrupo']}%'";
        $condicao .= ($_REQUEST['tipo'] == "") ? "" : " AND A.tipo = '{$_REQUEST['tipo']}'";
        $condicao .= ($_REQUEST['status'] == 't') ? " AND A.status IN (1,2)" : " AND A.status IN ({$_REQUEST['status']})";

        $qry = "SELECT A.id_saida AS saida_id, A.nome AS saida_nome, A.tipo, A.especifica AS saida_especifica, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS saida_vencimento, CAST(
            REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS saida_valor, D.id_banco AS banco_id,
            D.nome AS banco_nome, C.id_regiao AS regiao_id, C.regiao AS regiao_nome,
            B.id_projeto AS projeto_id, B.nome AS projeto_nome, A.n_documento, D.conta, D.agencia
            FROM saida AS A
            LEFT JOIN projeto AS B ON(B.id_projeto = A.id_projeto)
            LEFT JOIN regioes AS C ON(C.id_regiao = A.id_regiao)
            LEFT JOIN bancos AS D ON(D.id_banco = A.id_banco)
            LEFT JOIN saida_files AS E ON(E.id_saida = A.id_saida)
            LEFT JOIN entradaesaida AS F ON(F.id_entradasaida = A.tipo)
            WHERE 
                data_vencimento BETWEEN '".implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento_ini'])))."' AND '".implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento_fim'])))."'
                {$condicao}
            GROUP BY A.id_saida";

        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }
    
    public function getBuscarLancamento($id_regiao = null) {
        $ano = $_REQUEST['ano'];
//        $limite = $_REQUEST['limit'];

        $condicao = "";
        $condicao .= ($_REQUEST['projeto'] == "todos") ? "" : " AND B.id_projeto = '{$_REQUEST['projeto']}'";
//        $condicao .= ($_REQUEST['mes'] == "todos") ? "" : " AND MONTH(A.data_vencimento) = '{$_REQUEST['mes']}'";
        
        
        $condicao .= ($_REQUEST['data_ini']) ? " AND A.data_vencimento >= '" . implode('-', array_reverse(explode('/', $_REQUEST['data_ini']))) . "'" : "";
        $condicao .= ($_REQUEST['data_fim']) ? " AND A.data_vencimento <= '" . implode('-', array_reverse(explode('/', $_REQUEST['data_fim']))) . "'" : "";
        
        $condicao .= ($_REQUEST['id_saida'] == "") ? "" : " AND A.id_saida IN({$_REQUEST['id_saida']})";
        $condicao .= ($_REQUEST['nome'] == "") ? "" : " AND (A.nome LIKE '%{$_REQUEST['nome']}%' OR A.especifica LIKE '%{$_REQUEST['nome']}%')";
        $condicao .= ($_REQUEST['grupo'] == "todos") ? "" : " AND F.grupo = '{$_REQUEST['grupo']}'";
        $condicao .= ($_REQUEST['subgrupo'] == "") ? "" : " AND F.id_subgrupo = '{$_REQUEST['subgrupo']}'";
        $condicao .= ($_REQUEST['tipo'] == "") ? "" : " AND A.tipo = '{$_REQUEST['tipo']}'";
        $condicao .= ($id_regiao) ? " AND A.id_regiao = '{$id_regiao}'" : "";
        $condicao .= ($_REQUEST['status'] == 't') ? " AND A.status IN (1,2)" : " AND A.status IN ({$_REQUEST['status']})";
        $condicao .= ($_REQUEST['cheque']) ? " AND A.id_tipo_pag_saida IN (8, 15)" : null;
        $condicao .= ($_REQUEST['n_bordero']) ? " AND G.id_bordero = {$_REQUEST['n_bordero']}" : null;
        $condicao .= ($_REQUEST['n_documento']) ? " AND A.n_documento = '{$_REQUEST['n_documento']}'" : null;

        $qry = "SELECT A.id_saida AS saida_id, A.nome AS saida_nome, A.tipo, A.especifica AS saida_especifica, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS saida_vencimento, CAST(
            REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS saida_valor, D.id_banco AS banco_id,
            D.nome AS banco_nome, C.id_regiao AS regiao_id, C.regiao AS regiao_nome,
            B.id_projeto AS projeto_id, B.nome AS projeto_nome, G.id_bordero,
            IF(A.tipo = 327, CONCAT(TIMESTAMPDIFF(MONTH, H.contratado_em, A.data_vencimento), '/', TIMESTAMPDIFF(MONTH, H.contratado_em,H.encerrado_em)),A.n_documento) AS n_documento
            FROM saida AS A
            LEFT JOIN projeto AS B ON(B.id_projeto = A.id_projeto)
            LEFT JOIN regioes AS C ON(C.id_regiao = A.id_regiao)
            LEFT JOIN bancos AS D ON(D.id_banco = A.id_banco)
            LEFT JOIN saida_files AS E ON(E.id_saida = A.id_saida)
            LEFT JOIN entradaesaida AS F ON(F.id_entradasaida = A.tipo)
            LEFT JOIN bordero_saidas AS G ON (A.id_saida = G.id_saida)
            LEFT JOIN prestadorservico H ON (A.id_prestador = H.id_prestador)
            WHERE 1 {$condicao}
            GROUP BY A.id_saida
            ORDER BY A.id_projeto, A.id_saida";
//        print_array($qry);
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getEntradaSaida($id_regiao) {
        $ano = $_REQUEST['ano'];
        $limite = $_REQUEST['limit'];
        $tabela = ($_REQUEST['lancamento'] == 1) ? 'entrada' : 'saida';

        $condicao = "";
        $condicao .= ($_REQUEST['data_ini']) ? " AND A.data_vencimento >= '" . implode('-', array_reverse(explode('/', $_REQUEST['data_ini']))) . "'" : "";
        $condicao .= ($_REQUEST['data_fim']) ? " AND A.data_vencimento <= '" . implode('-', array_reverse(explode('/', $_REQUEST['data_fim']))) . "'" : "";
        
        $condicao .= ($_REQUEST['id_saida'] == "") ? "" : " AND A.id_saida IN({$_REQUEST['id_saida']})";
        $condicao .= ($_REQUEST['nome'] == "") ? "" : " AND (A.nome LIKE '%{$_REQUEST['nome']}%' OR A.especifica LIKE '%{$_REQUEST['nome']}%')";
        $condicao .= ($_REQUEST['grupo'] == "todos") ? "" : " AND F.grupo = '{$_REQUEST['grupo']}'";
        $condicao .= ($_REQUEST['subgrupo'] == "") ? "" : " AND F.id_subgrupo = '{$_REQUEST['subgrupo']}'";
        $condicao .= ($_REQUEST['tipo'] == "") ? "" : " AND A.tipo = '{$_REQUEST['tipo']}'";
        $condicao .= ($_REQUEST['projeto']) ? " AND A.id_projeto = '{$_REQUEST['projeto']}'" : null;
//        $condicao .= ($id_regiao) ? " AND A.id_regiao = '{$id_regiao}'" : "";
        $condicao .= ($_REQUEST['status'] == 't') ? " AND A.status IN (1,2)" : " AND A.status IN ({$_REQUEST['status']})";
//        $condicao .= ($_REQUEST['cheque']) ? " AND A.id_tipo_pag_saida IN (8, 15)" : null;
//        $condicao .= ($_REQUEST['n_bordero']) ? " AND G.id_bordero = {$_REQUEST['n_bordero']}" : null;
        $condicao .= ($_REQUEST['n_documento']) ? " AND A.n_documento = '{$_REQUEST['n_documento']}'" : null;
        $condicao .= ($_REQUEST['bordero']) ? " AND G.id_bordero = '{$_REQUEST['bordero']}'" : null;
        
        
        
//        $condicao = "";
//        $condicao .= ($_REQUEST['projeto'] == "todos") ? "" : " AND B.id_projeto = '{$_REQUEST['projeto']}'";
//        $condicao .= ($_REQUEST['mes'] == "todos") ? "" : " AND MONTH(A.data_vencimento) = '{$_REQUEST['mes']}'";
//        $condicao .= ($_REQUEST['nome_'] == "") ? "" : " AND A.nome LIKE '%{$_REQUEST['nome_']}%' OR A.especifica LIKE '%{$_REQUEST['nome_']}%'";
//        $condicao .= ($_REQUEST['grupo'] == "todos") ? "" : " AND F.grupo = '{$_REQUEST['grupo']}'";
//        $condicao .= ($_REQUEST['subgrupo'] == "") ? "" : " AND F.cod LIKE '{$_REQUEST['subgrupo']}%'";
//        $condicao .= ($_REQUEST['tipo'] == "") ? "" : " AND A.tipo = '{$_REQUEST['tipo']}'";

        if ($_REQUEST['lancamento'] == 1) {
            $condicao .= ($_REQUEST['id_entrada'] == "") ? "" : " AND A.id_entrada IN({$_REQUEST['id_entrada']})";
        } else {
            $condicao .= ($_REQUEST['id_saida'] == "") ? "" : " AND A.id_saida IN({$_REQUEST['id_saida']})";
        }

        $estorno = ($tabela == 'saida') ? ', A.estorno, A.n_documento' : '';
        
        $qry = "SELECT A.id_{$tabela} AS saida_id, A.nome AS saida_nome, A.especifica AS saida_especifica, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS saida_vencimento, CAST(
            REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS saida_valor, D.id_banco AS banco_id,
            D.nome AS banco_nome, C.id_regiao AS regiao_id, C.regiao AS regiao_nome,
            B.id_projeto AS projeto_id, B.nome AS projeto_nome, A.status, G.id_bordero {$estorno}
            FROM {$tabela} AS A
            LEFT JOIN projeto AS B ON(B.id_projeto = A.id_projeto)
            LEFT JOIN regioes AS C ON(C.id_regiao = A.id_regiao)
            LEFT JOIN bancos AS D ON(D.id_banco = A.id_banco)
            LEFT JOIN entradaesaida AS F ON(F.id_entradasaida = A.tipo)
            LEFT JOIN (SELECT A2.id_bordero, A2.id_{$tabela} FROM bordero A1 LEFT JOIN bordero_{$tabela}s A2 ON (A1.id = A2.id_bordero AND A2.status = 1) WHERE A1.status = 1 AND A1.pago = 1) G ON (A.id_{$tabela} = G.id_{$tabela})
            WHERE 1 /*A.id_regiao = {$id_regiao} AND YEAR(A.data_vencimento) = {$ano} AND (A.status = 2 OR A.status = 1)*/ {$condicao}
            GROUP BY A.id_{$tabela}";
            if($_COOKIE['debug'] == 666) {
                print_array($qry);
            }
        $sql = mysql_query($qry) or die(mysql_error());
        return $sql;
    }

    public function getEntradaSaidaId($id, $table) {
        $qry = "SELECT *, 
                    CAST( REPLACE(A.valor, ',', '.') as decimal(13,2)) as valor_f,
                    CAST( REPLACE(B.saldo, ',', '.') as decimal(13,2)) as saldo_f
            FROM {$table} AS A
            LEFT JOIN bancos AS B ON(A.id_banco = B.id_banco)
            WHERE A.id_{$table} = '{$id}'";
        $sql = mysql_query($qry) or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getGrupo($default = null) {
        $sql = mysql_query("SELECT *
                            FROM entradaesaida_grupo
                            WHERE id_grupo >= 10");
        if ($default == null) {
            $grupo = array("todos" => "Todos os Grupos");
        } else {
            $grupo = $default;
        }

        while ($rst = mysql_fetch_assoc($sql)) {
            $grupo[$rst['id_grupo']] = trim($rst['id_grupo']) . " - " . trim($rst['nome_grupo']);
        }
        return $grupo;
    }

    public function getGrupoBd($id_subgrupo) {
        $sql = mysql_query("SELECT * FROM entradaesaida_subgrupo WHERE id_subgrupo = {$id_subgrupo}") or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getNF() {
        $projeto = $_REQUEST['projeto'];
        $regiao = $_REQUEST['regiao'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];

        $result = "SELECT DATE_FORMAT(A.dt_emissao_nf, '%d/%m/%Y') AS dt_emissao_nfbr,
                A.dt_emissao_nf, A.n_documento, A.valor AS valor_bruto, A.tipo_nf,
                B.assunto,B.c_fantasia,A.status
                FROM saida AS A
                LEFT JOIN prestadorservico AS B ON (B.id_prestador = A.id_prestador)
                WHERE A.id_regiao = '{$regiao}' AND A.id_projeto ='{$projeto}' AND A.tipo = 213 AND A.status IN (1,2) AND 
                A.mes_competencia = {$mes} AND A.ano_competencia = {$ano}
                ORDER BY A.dt_emissao_nf";

        $nf = mysql_query($result) or die(mysql_error());
        return $nf;
    }

    public function getReferencia($default = null) {
        $sql = mysql_query("SELECT *
                            FROM tipos_referencia
                            WHERE status = 1");
        if ($default == null) {
            $ref = array("todos" => "Todos");
        } else {
            $ref = $default;
        }

        while ($rst = mysql_fetch_assoc($sql)) {
            $ref[$rst['id_referencia']] = $rst['descricao'];
        }
        return $ref;
    }

    public function getBens($default = null) {
        $sql = mysql_query("SELECT *
                            FROM tipos_bens
                            WHERE status = 1");
        if ($default == null) {
            $ref = array("todos" => "Todos");
        } else {
            $ref = $default;
        }

        while ($rst = mysql_fetch_assoc($sql)) {
            $ref[$rst['id_bens']] = $rst['descricao'];
        }
        return $ref;
    }

    public function getTipoPg($default = null) {
        $sql = mysql_query("SELECT *
                            FROM tipos_pag_saida");
        if ($default == null) {
            $ref = array("todos" => "Todos");
        } else {
            $ref = $default;
        }

        while ($rst = mysql_fetch_assoc($sql)) {
            $ref[$rst['id_tipo_pag']] = str_pad($rst['id_tipo_pag'], 2, "0", STR_PAD_LEFT) . " - " . $rst['descricao'];
        }
        return $ref;
    }

    public function getTipoBoleto($default = null) {
        $sql = mysql_query("SELECT *
                            FROM tipo_boleto");
        if ($default == null) {
            $ref = array("todos" => "Todos");
        } else {
            $ref = $default;
        }

        while ($rst = mysql_fetch_assoc($sql)) {
            $ref[$rst['id_tipo']] = $rst['nome'];
        }
        return $ref;
    }

    public function verificaSaidaRescisao($id_saida) {
        $sql_saida = mysql_query("SELECT * FROM saida AS A WHERE A.id_saida = {$id_saida}");
        $row_saida = mysql_fetch_assoc($sql_saida);

        $sql_saida_file = mysql_query("SELECT * FROM saida_files WHERE id_saida = {$id_saida} AND multa_rescisao = 1");
        $tot_saida_file = mysql_num_rows($sql_saida_file);

        if (($row_saida['tipo'] == 51 || $row_saida['tipo'] == 170) & $tot_saida_file == 0) {
            $sql_rescisao_comp = mysql_query("SELECT rescisao_complementar FROM saida_files WHERE id_saida = {$id_saida}");
            $row_rescisao_comp = mysql_fetch_assoc($sql_rescisao_comp);

            if ($row_rescisao_comp['rescisao_complementar'] == 1) {
                $sql_rescisao = mysql_query("SELECT C.id_regiao, A.id_clt, B.id_rescisao, C.data_proc, B.id_saida
                        FROM saida AS A
                        LEFT JOIN pagamentos_especifico AS B ON (A.id_saida = B.id_saida)
                        LEFT JOIN rh_recisao AS C ON (B.id_rescisao = C.id_recisao)
                        WHERE A.id_saida = {$id_saida} AND C.status = 1");
            } else {
                $sql_rescisao = mysql_query("SELECT B.id_regiao, B.id_clt, B.id_recisao, B.data_proc
                        FROM saida AS A
                        LEFT JOIN pagamentos_especifico AS C ON (A.id_saida = C.id_saida)
                        LEFT JOIN saida_files AS D ON (A.id_saida = D.id_saida)
                        LEFT JOIN rh_recisao AS B ON (C.id_clt = B.id_clt AND D.rescisao_complementar = B.rescisao_complementar)
                        WHERE A.id_saida = {$id_saida} AND B.status = 1");
            }

            $tot_rescisao = mysql_num_rows($sql_rescisao);

            if (!empty($tot_rescisao)) {
                $row_recisao = mysql_fetch_array($sql_rescisao);
                $link = str_replace('+', '--', encrypt("{$row_recisao[0]}&{$row_recisao[1]}&{$row_recisao[2]}"));

                if (substr($row_recisao['data_proc'], 0, 10) >= '2013-04-04') {
                    $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                } else {
                    $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                }
            }

            $link_final = $link_nova_rescisao;
        } else {
            $link_final = '';
        }

        return $link_final;
    }

    public function getLancamento() {
        $projeto = $_REQUEST['projeto'];
        $lancamento = $_REQUEST['lancamento'];

        if ($lancamento == 1) {
            $sql = "SELECT A.id_saida AS saida_id, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS saida_vencimento, A.nome AS saida_nome, A.tipo, 
                A.especifica AS saida_especifica, CAST(
                REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS saida_adicional, CAST(
                REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS saida_valor,
                B.agencia AS banco_agencia, B.conta AS banco_conta, C.nome AS entsai_nome,
                D.nome_grupo AS grupo_nome, E.nome AS func_nome
                FROM saida AS A
                LEFT JOIN bancos AS B ON(A.id_banco = B.id_banco)
                LEFT JOIN entradaesaida AS C ON(A.tipo = C.id_entradasaida)
                LEFT JOIN entradaesaida_grupo AS D ON(C.grupo = D.id_grupo)
                LEFT JOIN funcionario AS E ON(E.id_funcionario = A.id_user)
                WHERE A.id_projeto = {$projeto} AND A.status = 1
                ORDER BY A.data_vencimento";
        } else {
            $sql = "SELECT A.id_saida AS saida_id, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS saida_vencimento, A.nome AS saida_nome, A.tipo,
                A.especifica AS saida_especifica, CAST(
                REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS saida_adicional, CAST(
                REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS saida_valor,
                B.agencia AS banco_agencia, B.conta AS banco_conta, C.nome AS entsai_nome,
                D.nome_grupo AS grupo_nome, E.nome AS func_nome
                FROM saida AS A
                LEFT JOIN bancos AS B ON(A.id_banco = B.id_banco)
                LEFT JOIN entradaesaida AS C ON(A.tipo = C.id_entradasaida)
                LEFT JOIN entradaesaida_grupo AS D ON(C.grupo = D.id_grupo)
                LEFT JOIN funcionario AS E ON(E.id_funcionario = A.id_user)
                WHERE A.id_projeto = {$projeto} AND A.status = 1 AND A.data_vencimento > CURRENT_DATE()
                ORDER BY A.data_vencimento";
        }

        $res = mysql_query($sql) or die(mysql_error());
        return $res;
    }

    public function getNomeEntSai($id_nome) {
        $sql = mysql_query("SELECT *
            FROM entradaesaida_nomes
            WHERE id_nome = {$id_nome}") or die(mysql_error());

        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getFornecedor($id_fornecedor) {
        $sql = mysql_query("SELECT A.nome, A.cnpj, B.nome AS nome_projeto, C.regiao AS nome_regiao
                FROM fornecedores AS A
                INNER JOIN projeto AS B ON B.id_projeto = A.id_projeto
                INNER JOIN regioes AS C ON C.id_regiao = A.id_regiao
                WHERE id_fornecedor = '{$id_fornecedor}'") or die(mysql_error());

        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getPrestador($id_prestador) {
        $sql = mysql_query("SELECT A.c_razao, A.c_cnpj, B.nome AS nome_projeto, C.regiao AS nome_regiao
                FROM prestadorservico AS A
                INNER JOIN projeto AS B ON B.id_projeto = A.id_projeto
                INNER JOIN regioes AS C ON C.id_regiao = A.id_regiao
                WHERE id_prestador = '{$id_prestador}'") or die(mysql_error());

        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getProjetoRegiao($id_regiao, $id_projeto) {
        $sql = mysql_query("SELECT A.nome, B.regiao
            FROM projeto AS A
            LEFT JOIN regioes AS B ON(A.id_regiao = B.id_regiao)
            WHERE A.id_projeto = '{$id_projeto}' AND B.id_regiao = '{$id_regiao}'") or die(mysql_error());

        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public static function getSaidasHoje() {
        $usuario = carregaUsuario();
        $sql = mysql_query("SELECT COUNT(id_saida) AS total
            FROM saida            
            WHERE status = 1 AND id_regiao = {$usuario['id_regiao']} AND data_vencimento = CURRENT_DATE()");
        $tot = mysql_result($sql, 0);
        return $tot;
    }

    public static function getSaidasAmanha() {
        $usuario = carregaUsuario();
        $sql = mysql_query("SELECT COUNT(id_saida) AS total
            FROM saida            
            WHERE status = 1 AND id_regiao = {$usuario['id_regiao']} AND data_vencimento = CURRENT_DATE() + 1");
        $tot = mysql_result($sql, 0);
        return $tot;
    }

    public static function getSaidasMes() {
        $usuario = carregaUsuario();
        $sql = mysql_query("SELECT COUNT(id_saida) AS total
            FROM saida
            WHERE status = 1 AND id_regiao = {$usuario['id_regiao']} AND data_vencimento > CURRENT_DATE() + 1 AND MONTH(data_vencimento) = MONTH(CURRENT_DATE)");
        $tot = mysql_result($sql, 0);
        return $tot;
    }

    public static function getSaidasBanco($id_banco, $status, $mes = null, $ano = null, $descricao = null, $id_projeto = null) {
        if (!empty($descricao)) {
            $valorDescricao = explode(' ', $descricao);
            foreach ($valorDescricao as $value) {
                $ar_pesquisa[] .= "nome LIKE '%" . $value . "%'";
            }
            $nome_pesquisa = implode(' AND ', $ar_pesquisa);
            $auxDescricao = " AND $nome_pesquisa ";
        }

        $auxProjeto = (!empty($id_projeto)) ? " AND id_projeto = '$id_projeto'" : '';
        $auxMes = (!empty($mes)) ? " AND MONTH(data_vencimento) = '$mes'" : '';
        $auxAno = (!empty($ano)) ? " AND YEAR(data_vencimento) = '$ano'" : '';
        $usuario = carregaUsuario();

        $sql = "SELECT *, DATE_FORMAT(data_vencimento,'%d/%m/%Y') AS saida_vencimento FROM saida WHERE status = $status /*AND id_regiao = {$usuario['id_regiao']}*/ AND id_banco = '$id_banco' /*AND YEAR(data_vencimento) >= (YEAR(NOW()) - 1)*/ $auxProjeto $auxMes $auxAno $auxDescricao ORDER BY id_saida";
//        if($_COOKIE['logado'] == 257){ echo $sql; }
        $sql = mysql_query($sql)or die(mysql_errno());
        return $sql;
    }

    public function cadNome() {
        $nome = $_REQUEST['nome_entsai'];
        $cpf = $_REQUEST['cpf_cnpj'];
        $desc = $_REQUEST['especifica'];
        $tipo = $_REQUEST['tipo_nome'];

        $qry = "INSERT INTO entradaesaida_nomes (id_entradasaida, nome, cpfcnpj, descricao) VALUES ({$tipo}, '{$nome}', '{$cpf}', '{$desc}')";
        $sql = mysql_query($qry) or die(mysql_error());

        if ($sql) {
            $_SESSION['MESSAGE'] = "Nome <strong>{$nome}</strong> cadastrado com sucesso!";
            $_SESSION['MESSAGE_TYPE'] = 'info';
        } else {
            $_SESSION['MESSAGE'] = "Erro ao cadastrar Nome {$nome}!";
            $_SESSION['MESSAGE_TYPE'] = 'warning';
        }
        return mysql_insert_id();
    }

    public function cadSaida() {
        $id_regiao = $_REQUEST['regiao'];
        $id_projeto = $_REQUEST['projeto'];
        $id_banco = $_REQUEST['banco'];
        $nome = $_REQUEST['nome'];
        if($_COOKIE['debug'] == 667) { print_array("A"); }
        $sai = new Saida();

        $descricao = $_REQUEST['descricao'];
        $tipo = $_REQUEST['tipo'];
        $data_proc = date('Y-m-d H:i:s');
        $data_vencimento = ($_REQUEST['data_vencimento'] != '') ? ConverteData($_REQUEST['data_vencimento']) : '';
        $nosso_numero = $_REQUEST['nosso_numero'];
        $tipo_boleto = ($_REQUEST['tipo_boleto'] != "-1") ? $_REQUEST['tipo_boleto'] : 0;
        $cod_barra_consumo = implode("", $_REQUEST['barra_consumo']);
        $cod_barra_geral = implode("", $_REQUEST['barra_geral']);
        $referencia = ($_REQUEST['referencia'] != '-1') ? $_REQUEST['referencia'] : 0;
        $bens = ($referencia == 1) ? 0 : (($_REQUEST['bens'] != '-1') ? $_REQUEST['bens'] : 0);
        $tipo_pg = ($_REQUEST['tipo_pg'] != '-1') ? $_REQUEST['tipo_pg'] : 0;
        $grupo = $_REQUEST['grupo'];
        $subgrupo = $_REQUEST['subgrupo'];

        $n_documento = $_REQUEST['n_documento'];
        $link_nfe = $_REQUEST['link_nfe'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];
        $dt_emissao_nf = (!empty($_REQUEST['dt_emissao_nf'])) ? ConverteData($_REQUEST['dt_emissao_nf']) : '';
        $tipo_nf = ($_REQUEST['tipo_nf'] != '-1') ? $_REQUEST['tipo_nf'] : 0;
        $caixinha = ($_REQUEST['caixinha']) ? 1 : 0;
        $estorno = $_REQUEST['estorno'];
        $descricao_estorno = $_REQUEST['descricao_estorno'];
        $valor_estorno_parcial = str_replace(",", ".", str_replace('.', '', $_POST['valor_estorno_parcial'])); //o outro campo valor, esta como varchar, esse esta como decimal, precisa remover a virgula e por 1 ponto
        //$id_nome = ($_REQUEST['nome'] != '-1') ? $_REQUEST['nome'] : 0;
        $id_fornecedor = ($_REQUEST['fornecedor'] != '-1') ? $_REQUEST['fornecedor'] : 0;
        $tipo_de_nota = $_REQUEST['tipo_de_nota'];
        $chave_nota = $_REQUEST['chave_nota'];
        $agencia_cheque = $_POST['agencia_cheque'];
        $conta_cheque = $_POST['conta_cheque'];
        
        $valor = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_liquido']));
        $adicional = str_replace(",", ".", str_replace('.', '', $_REQUEST['adicional']));
        $valor_bruto = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_bruto']));
        $valor_multa = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_multa']));
        $valor_juros = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_juros']));
        $valor_tx_expediente = str_replace(",", ".", str_replace('.', '', $_REQUEST['taxa_expediente']));
        $valor_ir = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_ir']));
        $desconto = str_replace(",", ".", str_replace('.', '', $_POST['desconto']));
        
        if($_REQUEST['viagem']) {
            $id_viagem = ($_REQUEST['id_viagem']) ? $_REQUEST['id_viagem'] : 0;
        } else {
            $id_viagem = 0;
        }
        
        /* Condição para quando for editar, caso seja selecionado um outro tipo de pagamento que não seja boleto,
          limpar os campos referente a boletos na tabela */
        switch ($tipo_pg) {
            case 1:
                if ($tipo_boleto == 1) {
                    $cod_barra_geral = '';
                    $nosso_numero = '';
                } else {
                    $cod_barra_consumo = '';
                }
                break;

            default: $nosso_numero = '';
                $cod_barra_consumo = '';
                $cod_barra_geral = '';
                $tipo_boleto = 0;
        }
        if($_COOKIE['debug'] == 667) { print_array("B"); }
        //trata subgrupo
        switch ($grupo) {
            case 1:
            case 2:
            case 3:
            case 4: $subgrupo = 0;
                break;
        }

        //prestador                       
        $tipo_empresa = ($_REQUEST['tipo_empresa'] != '') ? $_REQUEST['tipo_empresa'] : 0;
        $regiao_prestador = ($_REQUEST['regiao_prestador'] != '-1') ? $_REQUEST['regiao_prestador'] : '';
        $projeto_prestador = ($_REQUEST['projeto_prestador'] != '-1') ? $_REQUEST['projeto_prestador'] : '';

        $reg = $sai->getProjetoRegiao($id_regiao, $id_projeto);
//        $dados_reg_pro = ' - Regiao: ' . $reg['regiao'] . ', Projeto - ' . $reg['nome'];
        $dados_reg_pro = 'Projeto: ' . $reg['nome'];

        //trata prestador/fornecedor
        if ($tipo_empresa == 1) {
            if($_COOKIE['debug'] == 667) { print_array("B1"); }
            $id_prestador = ($_REQUEST['prestador'] != '-1') ? $_REQUEST['prestador'] : 0;
            $prestador = $sai->getPrestador($id_prestador);
            $nome_prestador = $prestador['c_razao'];
            $cnpj_prestador = $prestador['c_cnpj'];
//            $nome_prestador = $prestador['c_razao'] . ' - Regiao: ' . $prestador['nome_regiao'] . ', Projeto: ' . $prestador['nome_projeto'];
            $nome_contabil = substr($prestador['c_razao'], 0, 26) . ' (' . $prestador['c_cnpj'] . ') ';
        } elseif ($tipo_empresa == 2) {
            if($_COOKIE['debug'] == 667) { print_array("B2"); }
            $id_prestador = ($_REQUEST['prestador_inativo'] != '-1') ? $_REQUEST['prestador_inativo'] : 0;
            $prestador = $sai->getPrestador($id_prestador);
            $nome_prestador = $prestador['c_razao'];
            $cnpj_prestador = $prestador['c_cnpj'];
//            $nome_prestador = $prestador['c_razao'] . ' - Regiao: ' . $prestador['nome_regiao'] . ', Projeto: ' . $prestador['nome_projeto'];
            $nome_contabil = substr($prestador['c_razao'], 0, 26) . ' (' . $prestador['c_cnpj'] . ') ';
        } else {
            if($_COOKIE['debug'] == 667) { print_array("B3"); }
            $id_prestador = ($_REQUEST['prestador_outros'] != '-1') ? $_REQUEST['prestador_outros'] : 0;
            $prestador = $sai->getPrestador($id_prestador);
            $nome_prestador = $prestador['c_razao'];
            $cnpj_prestador = $prestador['c_cnpj'];
//            $nome_prestador = $prestador['c_razao'] . ' - Regiao: ' . $prestador['nome_regiao'] . ', Projeto: ' . $prestador['nome_projeto'];
            $nome_contabil = substr($prestador['c_razao'], 0, 26) . ' (' . $prestador['c_cnpj'] . ') ';
        }
        if($_COOKIE['debug'] == 667) { print_array("C"); }
        if (!empty($id_prestador)) {
            $nome_c['nome'] = $nome_prestador;
            $histcontabil = addslashes($nome_contabil);
        } elseif (!empty($id_fornecedor)) {
            $nome_c['nome'] = $nome_fornecedor;
            $histcontabil = addslashes($nome_contabil);
        }

        $qryNomeTipo = "SELECT * FROM entradaesaida WHERE id_entradasaida = $tipo LIMIT 1;";
        $rowNomeTipo = mysql_fetch_assoc(mysql_query($qryNomeTipo));
        $nomeTipo = $rowNomeTipo['nome'];
        if($_COOKIE['debug'] == 667) { print_array("D"); }
        if (($nome != '' and is_numeric($nome)) AND empty($id_prestador)) {
            //$nome_c = $sai->getNomeEntSai($nome);


            if ($_REQUEST['tipo_nome'] == 'clt') {
                $qry = mysql_query("SELECT id_clt, nome FROM rh_clt WHERE id_clt = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_autonomo = $id_coop = '';
                $id_nome = $id_clt = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'cooperado') {
                $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE tipo_contratacao = '3' AND id_autonomo = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_autonomo = $id_clt = '';
                $id_nome = $id_coop = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'autonomo') {
                $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE tipo_contratacao = '1' AND id_autonomo = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_coop = $id_clt = '';
                $id_nome = $id_autonomo = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'pj') {
                $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE tipo_contratacao = '4' AND id_autonomo = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_nome = $id_coop = $id_clt = '';
                $id_autonomo = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'outro') {
                $nome_c = $sai->getNomeEntSai($nome);
                $id_nome = $nome;
                $id_autonomo = $id_coop = $id_clt = '';
            }
//            $nome = $nome_c['nome'] . $nomeTipo . $dados_reg_pro;
            
        }
        if($_COOKIE['debug'] == 667) { print_array("E"); }
        $nome = "{$nomeTipo} - {$nome_c['nome']} - {$dados_reg_pro}";
        $histcontabil = $nome_c['nome'] . $nomeTipo;
//        echo $nome;exit;
        
        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $local = "Cadastro de Saida";
        $ip = $_SERVER['REMOTE_ADDR'];
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];

//        $ar = array(
//            "ID REGIAO" => $id_regiao,
//            "ID PROJETO" => $id_projeto,
//            "ID BANCO" => $id_banco,
//            "ID USUARIO" => $id_usuario,
//            "NOME" => $nome,
//            "ID NOME" => $id_nome,
//            "DESCRICAO" => $descricao,
//            "TIPO" => $tipo,
//            "VALOR LIQUIDO" => $valor,
//            "DATA PROC" => $data_proc,
//            "DATA VENCIMENTO" => $data_vencimento,
//            "NOSSO NUMERO" => $nosso_numero,
//            "TIPO BOLETO" => $tipo_boleto,
//            "COD BARRA CONSUMO" => $cod_barra_consumo,
//            "COD BARRA GERAL" => $cod_barra_geral,
//            "REFERENCIA" => $referencia,
//            "BENS" => $bens,
//            "TIPO DE PGT" => $tipo_pg,
//            "GRUPO" => $grupo,
//            "SUBGRUPO" => $subgrupo,                                                
//            "Nº DOCUMENTO" => $n_documento,
//            "LINK NFE" => $link_nfe,
//            "MES REF." => $mes,
//            "ANO REF." => $ano,
//            "VALOR BRUTO" => $valor_bruto,
//            "DATA EMISSAO NF" => $dt_emissao_nf,
//            "TIPO NF" => $tipo_nf,
//            "ESTORNO" => $estorno,
//            "DESC ESTORNO" => $descricao_estorno,
//            "VALOR PARCIAL ESTORNO" => $valor_estorno_parcial,
//            "TIPO EMPRESA" => $tipo_empresa,
//            "ID PRESTADOR" => $id_prestador,
//            "REGIAO PRESTADOR" => $regiao_prestador,
//            "PROJETO PRESTADOR" => $projeto_prestador,
//            "NOME PRESTADOR" => $nome_prestador,
//            "CNPJ PRESTADOR" => $cnpj_prestador,
//            "ID FORNECEDOR" => $id_fornecedor,
//            "NOME FORNECEDOR" => $nome_fornecedor,
//            "CNPJ FORNECEDOR" => $cnpj_fornecedor
//        );
//        echo "<pre>";
//        print_r($ar);
//        echo "</pre>";

        $descricao = utf8_decode($descricao);
        $nome = addslashes($nome);
        $descricao = addslashes($descricao);
        $historico = addslashes($histcontabil . ' ' . $descricao);

        if(!in_array($_REQUEST['tipo'], [407]) && $id_viagem == 0){
            $sql = mysql_query(
                "SELECT dt_emissao_nf, n_documento, nome_prestador, id_projeto, tipo FROM saida WHERE tipo = '{$tipo}' AND n_documento = '{$n_documento}' AND nome_prestador = '{$nome_prestador}' AND id_projeto = '{$id_projeto}' AND status > 0"
            );

            $erroSaida['error'] = 1;
    //        while($row = mysql_fetch_assoc($sql)){
    //            if(
    //                    $row['n_documento'] == $n_documento 
    ////                    && $row['dt_emissao_nf'] == $dt_emissao_nf 
    //                    && $row['tipo'] == $tipo 
    //                    && $row['nome_prestador'] == $nome_prestador 
    //                    && $row['id_projeto'] == $id_projeto){
    //                return json_encode($erroSaida);
    //            }
    //        }
            if(mysql_num_rows($sql) > 0) {
                return json_encode($erroSaida);
            }
        }
        
        $tabela = ($id_viagem) ? 'saida_viagem' : 'saida';
        
        if($_COOKIE['debug'] == 667) { print_array("F"); }
        if ($tipo != 0) {
            $qry_saida = "INSERT INTO {$tabela} (
                    id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor,
                    data_proc, data_vencimento, status, comprovante, nosso_numero, tipo_boleto, cod_barra_consumo, cod_barra_gerais, id_referencia,
                    id_bens, id_tipo_pag_saida, entradaesaida_subgrupo_id,
                    id_prestador, nome_prestador, cnpj_prestador, n_documento, link_nfe, mes_competencia, ano_competencia, valor_bruto, dt_emissao_nf, tipo_nf, caixinha,
                    id_clt, id_autonomo, id_coop, valor_multa, valor_juros, valor_ir, taxa_expediente, tipo_de_nota, chave_nota, desconto, agencia_cheque, conta_cheque, id_viagem                    
                ) VALUES (
                    '{$id_regiao}', '{$id_projeto}', '{$id_banco}', '{$id_usuario}', '{$nome}', '{$id_nome}', '{$descricao}', '{$tipo}', '{$adicional}', '{$valor}',
                    '{$data_proc}', '{$data_vencimento}', 1, 2, '{$nosso_numero}', {$tipo_boleto}, '{$cod_barra_consumo}', '{$cod_barra_geral}', '{$referencia}',
                    '{$bens}', '{$tipo_pg}', '{$subgrupo}',
                    '{$id_prestador}', '{$nome_prestador}', '{$cnpj_prestador}', '{$n_documento}', '{$link_nfe}', '{$mes}', '{$ano}', '{$valor_bruto}', '{$dt_emissao_nf}', '{$tipo_nf}', '{$caixinha}', 
                    '{$id_clt}', '{$id_autonomo}', '{$id_coop}', '{$valor_multa}', '{$valor_juros}', '{$valor_ir}', '{$valor_tx_expediente}', '{$tipo_de_nota}', '{$chave_nota}', '{$desconto}', '{$agencia_cheque}', '{$conta_cheque}', '{$id_viagem}'
                    
                )";
                if($_COOKIE['debug'] == 666) { print_array($qry_saida);exit; }
            $insert_saida = mysql_query($qry_saida) or die(mysql_error());
            $id_saida = mysql_insert_id();
            
//            if($tipo == 327){
//                $nome2 = $nome . ' '. $this->parcelaAluguel($id_saida);
//                $up = mysql_query("UPDATE saida SET nome = '$nome2' WHERE id_saida = {$id_saida} LIMIT 1");
//            }
            
            if($valor_ir > 0){
                
                $data_vencimento_ir = date('Y-m-d', strtotime("+1 month", strtotime(date('Y-m-20', strtotime($data_vencimento)))));
                $qry_saida_ir = "INSERT INTO saida (
                    id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor,
                    data_proc, data_vencimento, status, comprovante, nosso_numero, tipo_boleto, cod_barra_consumo, cod_barra_gerais, id_referencia,
                    id_bens, id_tipo_pag_saida, entradaesaida_subgrupo_id,
                    id_prestador, nome_prestador, cnpj_prestador, n_documento, link_nfe, mes_competencia, ano_competencia, valor_bruto, dt_emissao_nf, tipo_nf, caixinha,
                    id_clt, id_autonomo, id_coop, valor_multa, valor_juros, id_saida_pai
                ) VALUES (
                    '{$id_regiao}', '{$id_projeto}', '{$id_banco}', '{$id_usuario}', 'IR - {$nome}', '{$id_nome}', 'IR - {$descricao}', 350, '{$adicional}', '{$valor_ir}',
                    '{$data_proc}', '{$data_vencimento_ir}', 1, 2, '{$nosso_numero}', {$tipo_boleto}, '{$cod_barra_consumo}', '{$cod_barra_geral}', '{$referencia}',
                    '{$bens}', '{$tipo_pg}', 25,
                    '{$id_prestador}', '{$nome_prestador}', '{$cnpj_prestador}', '{$n_documento}', '{$link_nfe}', '{$mes}', '{$ano}', '{$valor_ir}', '{$dt_emissao_nf}', '{$tipo_nf}', '{$caixinha}', 
                    '{$id_clt}', '{$id_autonomo}', '{$id_coop}', '{$valor_multa}', '{$valor_juros}', '{$id_saida}'
                )";

                $insert_saida_ir = mysql_query($qry_saida_ir) or die(mysql_error());
            }
            
//            $acao = "{$usuario['nome']} cadastrou a saida {$id_saida}";
//            
//            $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
//                ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
            // Prestadores de servicos OBS. existe desde antes da LAGOS
            if (($tipo == '32' or $tipo == '132' or $grupo == 30 or $grupo == 80 or $grupo == 20) and ! empty($id_prestador)) {
                if ($tipo == '132') {
                    $tipo_prestador = 'NOTA';
                } elseif ($tipo == '32') {
                    $tipo_prestador = 'FOLHA';
                }

                $query_prestador = mysql_query("SELECT MAX(parcela) FROM prestador_pg WHERE id_prestador = '{$id_prestador}'");
                $prestador = mysql_result($query_prestador, 0);
                $prestador = $prestador + 1;

                mysql_query("INSERT INTO prestador_pg (id_prestador, id_regiao,	id_saida, tipo,	valor, data, documento,	parcela, gerado, status_reg, comprovante)
                VALUES ({$id_prestador}, {$id_regiao}, {$id_saida}, '{$tipo_prestador}', '{$valor}', '{$data_credito2}', '{$descricao}', {$prestador}, 1, 1, 0)");
            }

            if ($insert_saida && $insere_log) {
                $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
                $_SESSION['MESSAGE_TYPE'] = 'info';
            } else {
                $_SESSION['MESSAGE'] = 'Erro ao cadastrar!';
                $_SESSION['MESSAGE_TYPE'] = 'danger';
            }
        }

        //LANÇAMETO CONTABIL CASO TRAVA CONTABIL = "0"

        $sqlBanco = "SELECT adiantamento FROM bancos WHERE id_banco = '{$id_banco}' LIMIT 1;";
        $qryBanco = mysql_query($sqlBanco);
        $rowBanco = mysql_fetch_assoc($qryBanco);
        
        $qry_contabil = "SELECT * FROM contabil_lancamento WHERE MONTH(data_lancamento) = MONTH('$data_vencimento') AND YEAR(data_lancamento) = YEAR('$data_vencimento') AND id_projeto = '{$id_projeto}'";

        $result = mysql_query($qry_contabil)or die('' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $trava = $row['trava_contabil'];
        }

        if ($trava == 0 && $rowBanco['adiantamento'] == 0) {
            $array_lancamento = array('id_saida' => $id_saida, 'id_projeto' => $id_projeto, 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => $data_vencimento, 'historico' => $historico);
            $id_lancamento = $this->inserirLancamento($array_lancamento);
        }

        foreach ($_REQUEST['unidades'] as $key => $value) {
            $valor_uni = str_replace(',', '.', str_replace('.', '', $value['valor']));
            $ins = mysql_query("INSERT INTO saida_unidade (id_saida, id_projeto, id_unidade, valor, percentual, id_grupo, id_subgrupo, id_tipo, status) VALUES ('$id_saida', '{$value['id_projeto']}', '{$value['id_unidade']}', '{$valor_uni}', '{$value['percent']}', '{$value['id_grupo']}', '{$value['id_subgrupo']}', '{$value['id_tipo']}', 1);");
//            if($caixinha){
//                $insC = "INSERT INTO caixinha (id_projeto, id_regiao, id_unidade, id_saida, data, tipo, descricao, saldo, id_grupo, id_subgrupo, id_tipo, data_cad, user_cad) VALUES ('{$value['id_projeto']}', '{$id_regiao}', '{$value['id_unidade']}', '{$id_saida}', '{$data_vencimento}', 2, 'SAIDA FINANCEIRO: {$id_saida}', '{$valor_uni}', '{$value['id_grupo']}', '{$value['id_subgrupo']}', '{$value['id_tipo']}', NOW(), {$_COOKIE['logado']});";
//                mysql_query($insC) or die("ERRO CAIXINHA: ".mysql_error());
//            }
        }

        if ($caixinha) {
            $valor = str_replace(",", ".", $valor);
            $insC = "INSERT INTO caixinha (id_projeto, id_regiao, id_unidade, id_saida, data, tipo, descricao, saldo, id_grupo, id_subgrupo, id_tipo, data_cad, user_cad) VALUES ('{$id_projeto}', '{$id_regiao}', '{$value['id_unidade']}', '{$id_saida}', '{$data_vencimento}', 2, 'SAIDA FINANCEIRO: {$id_saida}', '{$valor}', '{$grupo}', '{$subgrupo}', '{$tipo}', NOW(), {$_COOKIE['logado']});";
            mysql_query($insC) or die("ERRO CAIXINHA: " . mysql_error());
        }

        return $id_saida;
    }

    public function editSaida() {
        //print("<pre>");print_r($_REQUEST);print("</pre>");DIE();
        $id_regiao = $_REQUEST['regiao'];
        $id_projeto = $_REQUEST['projeto'];
        $id_banco = $_REQUEST['banco'];
        $nome = $_REQUEST['nome'];

        $sai = new Saida();

        $descricao = $_REQUEST['descricao'];
        $tipo = $_REQUEST['tipo'];
//        $data_proc = date('Y-m-d H:i:s');
        $data_edicao = date('Y-m-d H:i:s');
        $data_vencimento = ($_REQUEST['data_vencimento'] != '') ? ConverteData($_REQUEST['data_vencimento']) : '';
        $nosso_numero = $_REQUEST['nosso_numero'];
        $tipo_boleto = ($_REQUEST['tipo_boleto'] != "-1") ? $_REQUEST['tipo_boleto'] : 0;
        $cod_barra_consumo = implode("", $_REQUEST['barra_consumo']);
        $cod_barra_geral = implode("", $_REQUEST['barra_geral']);
        $referencia = ($_REQUEST['referencia'] != '-1') ? $_REQUEST['referencia'] : 0;
        $bens = ($referencia == 1) ? 0 : (($_REQUEST['bens'] != '-1') ? $_REQUEST['bens'] : 0);
        $tipo_pg = ($_REQUEST['tipo_pg'] != '-1') ? $_REQUEST['tipo_pg'] : 0;
        $grupo = $_REQUEST['grupo'];
        $subgrupo = $_REQUEST['subgrupo'];

        $n_documento = $_REQUEST['n_documento'];
        $link_nfe = $_REQUEST['link_nfe'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];
        $dt_emissao_nf = (!empty($_REQUEST['dt_emissao_nf'])) ? ConverteData($_REQUEST['dt_emissao_nf']) : '';
        $tipo_nf = ($_REQUEST['tipo_nf'] != '-1') ? $_REQUEST['tipo_nf'] : 0;
        $caixinha = ($_REQUEST['caixinha']) ? 1 : 0;
        $estorno = $_REQUEST['estorno'];
        $descricao_estorno = $_REQUEST['descricao_estorno'];
        $valor_estorno_parcial = str_replace(",", ".", str_replace('.', '', $_POST['valor_estorno_parcial'])); //o outro campo valor, esta como varchar, esse esta como decimal, precisa remover a virgula e por 1 ponto
        //$id_nome = ($_REQUEST['nome'] != '-1') ? $_REQUEST['nome'] : 0;
        $id_fornecedor = ($_REQUEST['fornecedor'] != '-1') ? $_REQUEST['fornecedor'] : 0;
        $tipo_de_nota = $_REQUEST['tipo_de_nota'];
        $chave_nota = $_REQUEST['chave_nota'];
        $agencia_cheque = $_POST['agencia_cheque'];
        $conta_cheque = $_POST['conta_cheque'];
        
        $valor = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_liquido']));
        $adicional = str_replace(",", ".", str_replace('.', '', $_REQUEST['adicional']));
        $valor_bruto = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_bruto']));
        $valor_multa = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_multa']));
        $valor_juros = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_juros']));
        $valor_tx_expediente = str_replace(",", ".", str_replace('.', '', $_REQUEST['taxa_expediente']));
//        $valor_ir = str_replace(",", ".", str_replace('.', '', $_REQUEST['valor_ir']));
        $desconto = str_replace(",", ".", str_replace('.', '', $_POST['desconto']));
        
        if($_REQUEST['viagem']) {
            $id_viagem = ($_REQUEST['id_viagem']) ? $_REQUEST['id_viagem'] : 0;
        } else {
            $id_viagem = 0;
        }
        
        /* Condição para quando for editar, caso seja selecionado um outro tipo de pagamento que não seja boleto,
          limpar os campos referente a boletos na tabela */
        switch ($tipo_pg) {
            case 1:
                if ($tipo_boleto == 1) {
                    $cod_barra_geral = '';
                    $nosso_numero = '';
                } else {
                    $cod_barra_consumo = '';
                }
                break;

            default: $nosso_numero = '';
                $cod_barra_consumo = '';
                $cod_barra_geral = '';
                $tipo_boleto = 0;
        }

        //trata subgrupo
        /*
          switch ($grupo) {
          case 1:
          case 2:
          case 3:
          case 4: $subgrupo = 0;
          break;
          } */

        $row_saida = $sai->getSaidaID($_REQUEST['id_saida']);

        //prestador                       
        $tipo_empresa = ($_REQUEST['tipo_empresa'] != '') ? $_REQUEST['tipo_empresa'] : 0;
        $regiao_prestador = ($_REQUEST['regiao_prestador'] != '-1') ? $_REQUEST['regiao_prestador'] : '';
        $projeto_prestador = ($_REQUEST['projeto_prestador'] != '-1') ? $_REQUEST['projeto_prestador'] : '';

        $reg = $sai->getProjetoRegiao($id_regiao, $id_projeto);
//        $dados_reg_pro = ' - Regiao: ' . $reg['regiao'] . ', Projeto - ' . $reg['nome'];
        $dados_reg_pro = 'Projeto: ' . $reg['nome'];

        //trata prestador/fornecedor
        if ($tipo_empresa == 1) {
            $id_prestador = ($_REQUEST['prestador'] != '-1') ? $_REQUEST['prestador'] : 0;
        } elseif ($tipo_empresa == 2) {
            $id_prestador = ($_REQUEST['prestador_inativo'] != '-1') ? $_REQUEST['prestador_inativo'] : 0;
        } else {
            $id_prestador = ($_REQUEST['prestador_outros'] != '-1') ? $_REQUEST['prestador_outros'] : 0;
        }

        //if($_REQUEST['status_prestador'] != "outros") {
        if (isset($id_prestador) && !empty($id_prestador)) {

            $prestador = $sai->getPrestador($id_prestador);
            $nome_prestador = $prestador['c_razao'];
            $cnpj_prestador = $prestador['c_cnpj'];
//            $nome_prestador = $prestador['c_razao'] . ' - Regiao: ' . $prestador['nome_regiao'] . ', Projeto: ' . $prestador['nome_projeto'];
        } else {

            $nome_prestador = "";
            $cnpj_prestador = "";
            $nome_prestador = "";
        }


        if (!empty($id_prestador)) {
//            $nome = $nome_prestador;
            $nome_c['nome'] = $nome_prestador;
        } elseif (!empty($id_fornecedor)) {
//            $nome = $nome_fornecedor;
            $nome_c['nome'] = $nome_fornecedor;
        }

        $qryNomeTipo = "SELECT * FROM entradaesaida WHERE id_entradasaida = $tipo LIMIT 1;";
        $rowNomeTipo = mysql_fetch_assoc(mysql_query($qryNomeTipo));
        $nomeTipo = $rowNomeTipo['nome'];

        if (($nome != '' and is_numeric($nome)) AND empty($id_prestador)) {
            //$nome_c = $sai->getNomeEntSai($nome);


            if ($_REQUEST['tipo_nome'] == 'clt') {
                $qry = mysql_query("SELECT id_clt, nome FROM rh_clt WHERE id_clt = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_autonomo = $id_coop = '';
                $id_nome = $id_clt = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'cooperado') {
                $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE tipo_contratacao = '3' AND id_autonomo = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_autonomo = $id_clt = '';
                $id_nome = $id_coop = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'autonomo') {
                $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE tipo_contratacao = '1' AND id_autonomo = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_coop = $id_clt = '';
                $id_nome = $id_autonomo = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'pj') {
                $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE tipo_contratacao = '4' AND id_autonomo = $nome LIMIT 1;");
                $row_nome = mysql_fetch_assoc($qry);
                $nome_c = $row_nome;
                $id_nome = $id_coop = $id_clt = '';
                $id_autonomo = $nome;
            } else if ($_REQUEST['tipo_nome'] == 'outro') {
                $nome_c = $sai->getNomeEntSai($nome);
                $id_nome = $nome;
                $id_autonomo = $id_coop = $id_clt = '';
            }
        }
        $nome = "{$nomeTipo} - {$nome_c['nome']} - {$dados_reg_pro}";
//echo $nome;exit;
        $arraySaidaRh = array(171, 168, 167, 169, 156, 76, 51, 170);
        //$arraySaidaRh = array(156, 76, 51, 170, 171);
        if (in_array($tipo, $arraySaidaRh) AND ! empty($descricao) AND $row_saida['id_nome'] == 0) {
            $nome = utf8_decode($descricao);
        }

        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $local = "Edição de Saída";
        $ip = $_SERVER['REMOTE_ADDR'];
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];

        if ($tipo != 0) {
            $id_saida = $_REQUEST['id_saida'];

            $query_saida = "SELECT * FROM saida WHERE id_saida = {$id_saida}";
            $rs_saida = mysql_query($query_saida);
            $row_saida = mysql_fetch_assoc($rs_saida);

//            $array_grupo_prestadores = array("FOLHA" => 1, "RESERVA" => 2, "MATERIAL DE CONSUMO" => 20, "SERVIÇOS DE TERCEIROS" => 30, "INVESTIMENTOS" => 80);

//            if (!in_array($grupo, $array_grupo_prestadores)) {
//                unset($id_fornecedor, $nome_fornecedor, $cnpj_fornecedor, $id_prestador, $nome_prestador, $cnpj_prestador, $tipo_empresa);
//            }

//            switch ($grupo) {
//                case 10: 
//                    
//                    $nome = (!empty($_REQUEST['nome_saida'])) ? $_REQUEST['nome_saida'] : $sai->getNomeEntSai($nome); 
//                    break;
//            }
//
//            if($tipo == 155){
//                $nome = $row_saida['nome'];
//            }
            //ALTERAÇÃO PARA QUANDO ATUALIZAR A RESCISÃO NÃO SUMIR O NOME
//            if ($tipo == 170) {
//                $qr_resc_nome = mysql_query("
//                    SELECT 
//                        b.id_clt, b.nome, c.regiao, d.nome as projeto, 
//                        (SELECT  nome_mes FROM ano_meses WHERE num_mes = MONTH(b.data_demi)) as mes, YEAR(b.data_demi) as ano
//                    FROM saida as a
//                        INNER JOIN rh_clt as b ON a.id_clt = b.id_clt
//                        INNER JOIN regioes as c ON c.id_regiao = b.id_regiao
//                        INNER JOIN projeto as d ON b.id_projeto = d.id_projeto
//                    WHERE id_saida = '$id_saida'");
//                $row_resc = mysql_fetch_assoc($qr_resc_nome);
//                $nome = '(' . $row_resc['id_clt'] . ') ' . $row_resc['nome'] . ', REGIÃO: ' . $row_resc['regiao'] . ' - PROJETO: ' . $row_resc['projeto'] . ' RESCISÃO ' . $row_resc['mes'] . '/' . $row_resc[ano];
//            }

            $alteraValor = "";
            if (isset($valor) && !empty($valor) && $row_saida['status'] == 1)
                $alteraValor = ", valor = '{$valor}'";

            if (empty($valor_estorno_parcial))
                $valor_estorno_parcial = 0;

            $descricao = utf8_decode($descricao);
            $nome = addslashes($nome);
            $descricao = addslashes($descricao);
            if (isset($_REQUEST['tipo_nome'])) {
                if ($_REQUEST['tipo_nome'] == 'outro') {
                    $id_nome = $_REQUEST['nome_pessoa'];
                }
            }
            $qry_saida = "UPDATE saida SET 
                    id_regiao = '{$id_regiao}', id_projeto = '{$id_projeto}', id_banco = '{$id_banco}', id_user_editou = '{$id_usuario}', nome = '{$nome}', id_nome = '{$id_nome}', especifica = '{$descricao}', tipo = '{$tipo}', adicional = '{$adicional}', valor = '{$valor}',
                    data_edicao = NOW(), data_vencimento = '{$data_vencimento}', nosso_numero = '{$nosso_numero}', tipo_boleto = '{$tipo_boleto}', cod_barra_consumo = '{$cod_barra_consumo}', cod_barra_gerais = '{$cod_barra_geral}', id_referencia = '{$referencia}',
                    id_bens = '{$bens}', id_tipo_pag_saida = '{$tipo_pg}', entradaesaida_subgrupo_id = '{$subgrupo}',
                    id_prestador = '{$id_prestador}', nome_prestador = '{$nome_prestador}', cnpj_prestador = '{$cnpj_prestador}', n_documento = '{$n_documento}', link_nfe = '{$link_nfe}', mes_competencia = '{$mes}', ano_competencia = '{$ano}', valor_bruto = '{$valor_bruto}', dt_emissao_nf = '{$dt_emissao_nf}', tipo_nf = '{$tipo_nf}', caixinha = '{$caixinha}',
                    estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = '$valor_estorno_parcial',
                    id_clt = '{$id_clt}', id_autonomo = '{$id_autonomo}', id_coop = '{$id_coop}', valor_multa = '{$valor_multa}', valor_juros = '{$valor_juros}', valor_ir = '{$valor_ir}', tipo_de_nota = '$tipo_de_nota', chave_nota = '$chave_nota', desconto = '{$desconto}', agencia_cheque = '{$agencia_cheque}', conta_cheque = '{$conta_cheque}', id_viagem = '{$id_viagem}'
                WHERE id_saida = {$id_saida}
                LIMIT 1";
            $update_saida = mysql_query($qry_saida) or die(mysql_error());
            
            if($id_viagem) {
                
                $sql = "SELECT * FROM saida WHERE id_saida = {$id_saida} LIMIT 1;";
                $qry = mysql_query($sql);
                $row = mysql_fetch_assoc($qry);
                
                //mysql_query("UPDATE saida SET status = 0 WHERE id_saida = {$id_saida} LIMIT 1;");
                
                unset($row['id_saida']);
                $keys = implode(',', array_keys($row));
                $values = implode("' , '", $row);

                mysql_query("INSERT INTO saida_viagem ($keys) VALUES ('$values');");
                
            } 
            
//            if($tipo == 327){
//                $nome2 = $nome . ' '. $this->parcelaAluguel($id_saida);
//                $up = mysql_query("UPDATE saida SET nome = '$nome2' WHERE id_saida = {$id_saida} LIMIT 1");
//            }

            foreach ($_REQUEST['unidades'] as $key => $value) {
                $valor_uni = str_replace(',', '.', str_replace('.', '', $value['valor']));
                if ($value['id_assoc']) {
                    $up = mysql_query("UPDATE saida_unidade SET percentual = {$value['percent']}, valor = {$valor_uni}, id_grupo = '{$value['id_grupo']}', id_subgrupo = '{$value['id_subgrupo']}', id_tipo = '{$value['id_tipo']}' WHERE id_saida = {$id_saida} AND id_assoc = {$value['id_assoc']};") or die("ERRO AO ATUALIZAR UNIDADE: " . mysql_error());
//                    if($caixinha){
//                        $query = mysql_query("SELECT id_caixinha FROM caixinha WHERE id_saida = {$id_saida} AND id_unidade = {$value['id_unidade']} AND id_tipo = {$value['id_tipo']} AND status = 1;");
//                        $nRow = mysql_num_rows($query);
//                        if($nRow > 0){ 
//                            $upC = "UPDATE caixinha SET saldo = {$valor_uni}, id_grupo = '{$value['id_grupo']}', id_subgrupo = '{$value['id_subgrupo']}', id_tipo = '{$value['id_tipo']}' WHERE id_saida = {$id_saida} AND id_unidade = {$value['id_unidade']} AND id_tipo = {$value['id_tipo']} AND status = 1;";
//                            mysql_query($upC) or die("ERRO CAIXINHA: ".mysql_error());
//                        } else { 
//                            $insC = "INSERT INTO caixinha (id_projeto, id_regiao, id_unidade, id_saida, data, tipo, descricao, saldo, id_grupo, id_subgrupo, id_tipo, data_cad, user_cad, status) VALUES ('{$value['id_projeto']}', '{$id_regiao}', '{$value['id_unidade']}', '{$id_saida}', '{$data_vencimento}', 2, 'SAIDA FINANCEIRO: {$id_saida}', '{$valor_uni}', '{$value['id_grupo']}', '{$value['id_subgrupo']}', '{$value['id_tipo']}', NOW(), {$_COOKIE['logado']}, 1);";
//                            mysql_query($insC) or die("ERRO CAIXINHA: ".mysql_error());
//                        }
//                    } else {
//                        $insC = "UPDATE caixinha SET status = 0 WHERE id_saida = '{$id_saida}'";
//                        mysql_query($insC) or die("ERRO UP CAIXINHA: ".mysql_error());
//                    }
                } else {
                    //                echo "INSERT INTO saida_unidade (id_saida, id_projeto, id_unidade, valor, percentual, id_grupo, id_subgrupo, id_tipo, status) VALUES ($id_saida, {$value['id_projeto']}, {$value['id_unidade']}, {$valor_uni}, {$value['percent']}, '{$value['id_grupo']}', '{$value['id_subgrupo']}', '{$value['id_tipo']}', 1);";
                    $ins = mysql_query("INSERT INTO saida_unidade (id_saida, id_projeto, id_unidade, valor, percentual, id_grupo, id_subgrupo, id_tipo, status) VALUES ('$id_saida', '{$value['id_projeto']}', '{$value['id_unidade']}', '{$valor_uni}', '{$value['percent']}', '{$value['id_grupo']}', '{$value['id_subgrupo']}', '{$value['id_tipo']}', 1);") or die("ERRO AO INSERIR UNIDADE: " . mysql_error());
//                    if($caixinha){
//                        $insC = "INSERT INTO caixinha (id_projeto, id_regiao, id_unidade, id_saida, data, tipo, descricao, saldo, id_grupo, id_subgrupo, id_tipo, data_cad, user_cad, status) VALUES ('{$value['id_projeto']}', '{$id_regiao}', '{$value['id_unidade']}', '{$id_saida}', '{$data_vencimento}', 2, 'SAIDA FINANCEIRO: {$id_saida}', '{$valor_uni}', '{$value['id_grupo']}', '{$value['id_subgrupo']}', '{$value['id_tipo']}', NOW(), {$_COOKIE['logado']}, 1);";
//                        mysql_query($insC) or die("ERRO CAIXINHA: ".mysql_error());
//                    } else {
//                        $insC = "UPDATE caixinha SET status = 0 WHERE id_saida = '{$id_saida}'";
//                        mysql_query($insC) or die("ERRO UP CAIXINHA: ".mysql_error());
//                    }
                }
            }

            if ($caixinha) {
                $valor = str_replace(",", ".", $valor);
                $insC = "INSERT INTO caixinha (id_projeto, id_regiao, id_unidade, id_saida, data, tipo, descricao, saldo, id_grupo, id_subgrupo, id_tipo, data_cad, user_cad, status) VALUES ('{$id_projeto}', '{$id_regiao}', '{$value['id_unidade']}', '{$id_saida}', '{$data_vencimento}', 2, 'SAIDA FINANCEIRO: {$id_saida}', '{$valor}', '{$grupo}', '{$subgrupo}', '{$tipo}', NOW(), {$_COOKIE['logado']}, 1);";
                mysql_query($insC) or die("ERRO CAIXINHA: " . mysql_error());
            } else {
                $insC = "UPDATE caixinha SET status = 0 WHERE id_saida = '{$id_saida}'";
                mysql_query($insC) or die("ERRO UP CAIXINHA: " . mysql_error());
            }

//            $acao = "{$usuario['nome']} editou a saida {$id_saida}";
//            $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
//                ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
        }
        return $id_saida;
    }

    function cadReembolsoSaida() {

        $regiao = $_REQUEST['regiao'];
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $nome = utf8_decode($_REQUEST['nome']);
        $especifica = utf8_decode($_REQUEST['especifica']);
        $tipo = $_REQUEST['tipo'];
//        $valor = str_replace(".", "", $_REQUEST['valor']);
        $valor = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor']));
//        $adicional = str_replace(".", "", $_REQUEST['adicional']);
        $adicional = str_replace(',', '.', str_replace('.', '', $_REQUEST['adicional']));
        $data_credito = $_REQUEST['data_credito'];
        $comprovante = $_REQUEST['comprovante'];
        $id_reembolso = $_REQUEST['reembolso'];
        $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
        $data_credito2 = ConverteData($data_credito);
        /* VERIFICA O BANCO E A REGIÃO */
        $result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$banco'");
        $row_banco = mysql_fetch_array($result_banco);
        if ($row_banco['id_regiao'] != $regiao) {
            $regiao = $row_banco['id_regiao'];
        }

        mysql_query("
        INSERT INTO saida (
            id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento,comprovante,tipo_arquivo,id_reembolso
        ) VALUES (
            '$regiao','$projeto','$banco',$_COOKIE[logado],'$nome','$especifica','$tipo','$adicional','$valor',NOW(),'$data_credito2','$comprovante','$tipo_arquivo','$id_reembolso')") or die(mysql_error());
        return mysql_insert_id();
    }

    function editaSaidaRh() {
        $id_saida = $_REQUEST['id_saida'];

        $descricao = $_REQUEST['descricao'];
        $data_vencimento = ($_REQUEST['data_vencimento'] != '') ? ConverteData($_REQUEST['data_vencimento']) : '';
        $estorno = $_REQUEST['estorno'];
        $descricao_estorno = $_REQUEST['descricao_estorno'];
        $valor_estorno_parcial = str_replace(",", ".", str_replace('.', '', $_POST['valor_estorno_parcial'])); //o outro campo valor, esta como varchar, esse esta como decimal, precisa remover a virgula e por 1 ponto

        $descricao = utf8_decode($descricao);

        $qry_saida = "UPDATE saida SET
                especifica = '{$descricao}', data_vencimento = '{$data_vencimento}',
                estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = '$valor_estorno_parcial'
            WHERE id_saida = {$id_saida}
            LIMIT 1";
        $update_saida = mysql_query($qry_saida) or die(mysql_error());
    }

    public function editValorEntradaSaida() {
        $valor_novo = str_replace(".", "", $_REQUEST['valor_novo']);
        $valor_antigo = str_replace(".", "", $_REQUEST['valor_antigo']);
        $saldo_novo = str_replace(".", "", $_REQUEST['saldo_novo']);
        $saldo_antigo = str_replace(".", "", $_REQUEST['saldo_antigo']);
        $id_lancamento = $_REQUEST['id_lancamento'];
        $status_lancamento = $_REQUEST['status_lancamento'];
        $tabela = $_REQUEST['tipo_lancamento'];
        $banco = $_REQUEST['banco'];
        $tipo_lancamento = ($tabela == 'saida') ? '1' : '2';

        $sql_valorLancamento = mysql_query("UPDATE {$tabela} SET valor = '{$valor_novo}' WHERE id_{$tabela} = '{$id_lancamento}'") or die("ERRO sql_valorLancamento");

        // SOMENTE MEXE NO SALDO, QND FOR LANÇAMENTO PAGO
        if ($status_lancamento == 2) {
            $sql_saldoBanco = mysql_query("UPDATE bancos SET saldo = '{$saldo_novo}' WHERE id_banco = '{$banco}'") or die("ERRO sql_saldoBanco");
        }

        $sql_gravaHistorico = mysql_query("INSERT INTO historico_finan (acao, id_lancamento, tipo_lancamento, valor_antigo, valor_novo, saldo_antigo, saldo_novo, id_user, data_cad) VALUES (1, {$id_lancamento}, {$tipo_lancamento}, '{$valor_antigo}', '{$valor_novo}', '{$saldo_antigo}', '{$saldo_novo}', {$_COOKIE['logado']}, NOW())") or die("ERRO sql_gravaHistorico");
    }

    public function excluiEntradaSaida() {
        $valor = str_replace(".", "", $_REQUEST['valor']);
        $saldo_novo = str_replace(".", "", $_REQUEST['saldo_novo']);
        $saldo_antigo = str_replace(".", "", $_REQUEST['saldo_antigo']);
        $id_lancamento = $_REQUEST['id_lancamento'];
        $status_lancamento = $_REQUEST['status_lancamento'];
        $tabela = $_REQUEST['tipo_lancamento'];
        $banco = $_REQUEST['banco'];
        $tipo_lancamento = ($tabela == 'saida') ? '1' : '2';

        $sql_valorLancamento = mysql_query("UPDATE {$tabela} SET status = 0 WHERE id_{$tabela} = '{$id_lancamento}'") or die("ERRO sql_valorLancamento");

        // SOMENTE MEXE NO SALDO, QND FOR LANÇAMENTO PAGO
        if ($status_lancamento == 2) {
            $sql_saldoBanco = mysql_query("UPDATE bancos SET saldo = '{$saldo_novo}' WHERE id_banco = '{$banco}'") or die("ERRO sql_saldoBanco");
        }

        $sql_gravaHistorico = mysql_query("INSERT INTO historico_finan (acao, id_lancamento, tipo_lancamento, valor_antigo, saldo_antigo, saldo_novo, id_user, data_cad) VALUES (2, {$id_lancamento}, {$tipo_lancamento}, '{$valor}', '{$saldo_antigo}', '{$saldo_novo}', {$_COOKIE['logado']}, NOW())") or die("ERRO sql_gravaHistorico");
    }

    public function voltaEntradaSaida() {
        $valor = str_replace(".", "", $_REQUEST['valor']);
        $saldo_novo = str_replace(".", "", $_REQUEST['saldo_novo']);
        $saldo_antigo = str_replace(".", "", $_REQUEST['saldo_antigo']);
        $id_lancamento = $_REQUEST['id_lancamento'];
        $status_lancamento = $_REQUEST['status_lancamento'];
        $tabela = $_REQUEST['tipo_lancamento'];
        $banco = $_REQUEST['banco'];
        $tipo_lancamento = ($tabela == 'saida') ? '1' : '2';

        $sql_valorLancamento = mysql_query("UPDATE {$tabela} SET status = 1 WHERE id_{$tabela} = '{$id_lancamento}'") or die("ERRO sql_valorLancamento");
        if($tabela == 'saida'){ 
//            echo "UPDATE bordero_saidas SET status = 0 WHERE id_saida = '{$id_lancamento}'";exit;
            $sql_statusBordero = mysql_query("UPDATE bordero_saidas SET status = 0 WHERE id_saida = '{$id_lancamento}'") or die("ERRO sql_statusBordero");
        }
        // SOMENTE MEXE NO SALDO, QND FOR LANÇAMENTO PAGO
        if ($status_lancamento == 2) {
            $sql_saldoBanco = mysql_query("UPDATE bancos SET saldo = '{$saldo_novo}' WHERE id_banco = '{$banco}'") or die("ERRO sql_saldoBanco");
        }

        $sql_gravaHistorico = mysql_query("INSERT INTO historico_finan (acao, id_lancamento, tipo_lancamento, valor_antigo, saldo_antigo, saldo_novo, id_user, data_cad) VALUES (3, {$id_lancamento}, {$tipo_lancamento}, '{$valor}', '{$saldo_antigo}', '{$saldo_novo}', {$_COOKIE['logado']}, NOW())") or die("ERRO sql_gravaHistorico");
    }
    
    /**
     * MÉTODO PARA CALCULAR IRRF 
     * @param type $base
     * @param type $idclt
     * @param type $idprojeto
     * @param type $data
     * @param type $tipo
     */
    function calculaIR($valor_bruto, $id_prestador) {
        
        $sqlPrestador = "SELECT * FROM prestadorservico WHERE id_prestador = $id_prestador LIMIT 1";
        $qryPrestador = mysql_query($sqlPrestador);
        $rowPrestador = mysql_fetch_assoc($qryPrestador);
        
        if($rowPrestador['prestador_tipo'] != 3){
            $array['status']  = 0;
        } else {

//            $base = $valor_bruto = str_replace('.', ',', str_replace('.', '', $valor_bruto));
            $base = $valor_bruto = $valor_bruto;

            $total_filhos_menor_21 = 0;
            $sql_dependentes = "SELECT COUNT(*) FROM prestador_dependente WHERE prestador_id = '$id_prestador'";
            $qry_dependentes = mysql_query($sql_dependentes);
            while($row_dependentes = mysql_fetch_assoc()){
                $total_filhos_menor_21++;
            }

            if($total_filhos_menor_21 > 0) {
    //	    echo "SELECT * FROM rh_movimentos WHERE cod = '5049' AND anobase = YEAR(CURDATE()) AND CURDATE() BETWEEN data_ini AND data_fim";
                $result_deducao_ir = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5049' AND anobase = YEAR(CURDATE()) AND CURDATE() BETWEEN data_ini AND data_fim");
                $row_deducao_ir = mysql_fetch_array($result_deducao_ir);	
                $valor_deducao_ir = $total_filhos_menor_21 * $row_deducao_ir['fixo'];                 
                $base -= $valor_deducao_ir;               

                $valor_deducao_ir_total = $valor_deducao_ir;
                $valor_deducao_ir_fixo  = $row_deducao_ir['fixo'];
                $total_filhos_menor_21  = $total_filhos_menor_21;

            } else {

                $valor_deducao_ir_total = 0;
                $valor_deducao_ir_fixo  = 0;
                $total_filhos_menor_21  = 0;

            }

            $data_atual = $data;

            $sql_IR = "SELECT * FROM rh_movimentos WHERE cod = '5021' AND v_ini <= '$base' AND v_fim >= '$base' AND anobase = YEAR(CURDATE()) AND CURDATE() BETWEEN data_ini AND data_fim";
            $result_IR = mysql_query($sql_IR);
            $row_IR = mysql_fetch_assoc($result_IR);	
            $valor_IR = ($base * $row_IR['percentual']) - $row_IR['fixo']; 
            
            $valor_IR = ($valor_IR < 10) ? 0.00 : $valor_IR;

            $array['bruto']    = $valor_bruto;
            $array['base']    = $base;
            $array['ir']         = $valor_IR;
            $array['liquido']    = $valor_bruto - $valor_IR;
            $array['percentual'] = $row_IR['percentual']*100;
            $array['valor_deducao_ir']    = $valor_deducao_ir_fixo;
            $array['total_filhos_menor_21'] = $total_filhos_menor_21;
            $array['status']  = 1;
        }
        return $array;
    }
    
    function getContasPagar($saida = null, $entrada = null) {
        
        $condicao = [];
        $condicao[] = ($_REQUEST['data_vencimento_ini']) ? "A.data_vencimento >= '".implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento_ini'])))."'" : '';
        $condicao[] = ($_REQUEST['data_vencimento_fim']) ? "A.data_vencimento <= '".implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento_fim'])))."'" : '';
        $condicao[] = ($_REQUEST['id_projeto']) ? "A.id_projeto = '{$_REQUEST['id_projeto']}'" : '';
        $condicao[] = ($_REQUEST['id_banco']) ? "A.id_banco = '{$_REQUEST['id_banco']}'" : '';
//        $condicao[] = ($_REQUEST['grupo'] == "todos") ? "" : " AND F.grupo = '{$_REQUEST['grupo']}'";
//        $condicao[] = ($_REQUEST['subgrupo'] == "") ? "" : " AND F.cod LIKE '{$_REQUEST['subgrupo']}%'";
        $condicao[] = ($_REQUEST['tipo'] == "t" || !$_REQUEST['tipo']) ? "" : "A.tipo = '{$_REQUEST['tipo']}'";
        $condicao[] = "A.status = '1'";
        
        $condicao = array_filter($condicao);
        
        if($_REQUEST['agrupamento'] == 1) {
            $agrupamento = [", CONCAT(A.id_projeto, ' - ', B.nome) AS nomeAgrupamento, A.id_projeto AS idAgrupamento", 'A.id_projeto,'];
        } else if($_REQUEST['agrupamento'] == 2) {
            $agrupamento = [", DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS nomeAgrupamento, A.data_vencimento AS idAgrupamento", null];
        } else if($_REQUEST['agrupamento'] == 3) {
            $agrupamento = [", D.nome AS nomeAgrupamento, D.id_entradasaida AS idAgrupamento", "D.nome,"];
        } else if($_REQUEST['agrupamento'] == 4) {
//            $agrupamento = [", IF(F.nome IS NULL, E.c_razao, F.nome) AS nomeAgrupamento, IF(F.nome IS NULL, E.c_cnpj, F.cpfcnpj) AS idAgrupamento", "IF(F.nome IS NULL, E.c_razao, F.nome),"];
        }
        $agrupamento = array_filter($agrupamento);
        
//        if($saida) { $tabela[] = "SELECT id_saida, data_pg, data_vencimento, nome, especifica, REPLACE(valor, ',','.') AS valor, 's' tipo, id_projeto, n_documento, tipo AS id_entradasaida, id_prestador FROM saida WHERE " . implode(' AND ', $condicao); }
        
        $sql = "
        SELECT A.id_saida, A.nome, REPLACE(A.valor, ',','.') AS valor, A.n_documento, 
        DATE_FORMAT(A.data_proc, '%d/%m/%Y') AS cadastro,
        DATE_FORMAT(A.dt_emissao_nf, '%d/%m/%Y') AS emissao,
        DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS vencto,
        B.nome AS nomeProjeto, D.nome AS nomeDespesa, E.c_razao
        $agrupamento[0]
        FROM saida A
        LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
        LEFT JOIN entradaesaida D ON (A.tipo = D.id_entradasaida)
        LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
        LEFT JOIN entradaesaida_nomes F ON (A.id_nome = F.id_nome)
        WHERE " . implode(' AND ', $condicao) . "
        ORDER BY $agrupamento[1] A.data_vencimento ASC, A.tipo";
//        print_array($sql);
        return mysql_query($sql);
    }
    
    function getContasPagas($saida = null, $entrada = null) {
        
        $condicao = [];
        $condicao[] = ($_REQUEST['data_vencimento_ini']) ? "A.data_vencimento >= '".implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento_ini'])))."'" : '';
        $condicao[] = ($_REQUEST['data_vencimento_fim']) ? "A.data_vencimento <= '".implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento_fim'])))."'" : '';
        $condicao[] = ($_REQUEST['id_projeto']) ? "A.id_projeto = '{$_REQUEST['id_projeto']}'" : '';
        $condicao[] = ($_REQUEST['id_banco']) ? "A.id_banco = '{$_REQUEST['id_banco']}'" : '';
//        $condicao[] = ($_REQUEST['grupo'] == "todos") ? "" : " AND F.grupo = '{$_REQUEST['grupo']}'";
//        $condicao[] = ($_REQUEST['subgrupo'] == "") ? "" : " AND F.cod LIKE '{$_REQUEST['subgrupo']}%'";
        $condicao[] = ($_REQUEST['tipo'] == "t" || !$_REQUEST['tipo']) ? "" : "A.tipo = '{$_REQUEST['tipo']}'";
        $condicao[] = "A.status = '2'";
        
        $condicao = array_filter($condicao);
        
        if($_REQUEST['agrupamento'] == 1) {
            $agrupamento = [", CONCAT(A.id_projeto, ' - ', B.nome) AS nomeAgrupamento, A.id_projeto AS idAgrupamento", 'A.id_projeto,'];
        } else if($_REQUEST['agrupamento'] == 2) {
            $agrupamento = [", DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS nomeAgrupamento, A.data_vencimento AS idAgrupamento", null];
        } else if($_REQUEST['agrupamento'] == 3) {
            $agrupamento = [", D.nome AS nomeAgrupamento, D.id_entradasaida AS idAgrupamento", "D.nome,"];
        } else if($_REQUEST['agrupamento'] == 4) {
            $agrupamento = [", IF(F.nome IS NULL, E.c_razao, F.nome) AS nomeAgrupamento, IF(F.nome IS NULL, E.c_cnpj, F.cpfcnpj) AS idAgrupamento", "IF(F.nome IS NULL, E.c_razao, F.nome),"];
        }
        $agrupamento = array_filter($agrupamento);
        
//        if($saida) { $tabela[] = "SELECT id_saida, data_pg, data_vencimento, nome, especifica, REPLACE(valor, ',','.') AS valor, 's' tipo, id_projeto, n_documento, tipo AS id_entradasaida, id_prestador FROM saida WHERE " . implode(' AND ', $condicao); }
        
        $sql = "
        SELECT A.id_saida, A.nome, REPLACE(A.valor, ',','.') AS valor, A.n_documento, 
        DATE_FORMAT(A.data_proc, '%d/%m/%Y') AS cadastro,
        DATE_FORMAT(A.dt_emissao_nf, '%d/%m/%Y') AS emissao,
        DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS vencto,
        B.nome AS nomeProjeto, D.nome AS nomeDespesa, E.c_razao
        $agrupamento[0]
        FROM saida A
        LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
        LEFT JOIN entradaesaida D ON (A.tipo = D.id_entradasaida)
        LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
        LEFT JOIN entradaesaida_nomes F ON (A.id_nome = F.id_nome)
        WHERE " . implode(' AND ', $condicao) . "
        ORDER BY $agrupamento[1] A.data_vencimento ASC, A.tipo";
//        print_array($sql);
        return mysql_query($sql);
    }
    
    public function getSaidasAgrupadas($id_saida){
        
        $sql = "SELECT B.id_saida,B.nome,B.especifica,B.valor,DATE_FORMAT(B.data_vencimento, '%d/%m/%Y') as vencimento,B.n_documento,
                        CONCAT(mes_competencia,'/',ano_competencia) as comp,
                        A.criado_em,A.criado_por, C.nome1 
                    FROM saida_agrupamento_assoc AS A
                    LEFT JOIN saida AS B ON (A.id_saida = B.id_saida)
                    LEFT JOIN funcionario AS C ON (A.criado_por = C.id_funcionario)
                    WHERE A.id_saida_pai = '{$id_saida}';";
                    
        return mysql_query($sql);
        
    }

    public function parcelaAluguel ($id_saida) {
        $sql = "SELECT A.id_saida, A.nome, especifica, A.data_vencimento, B.contratado_em, B.encerrado_em, 
        CONCAT(timestampdiff(month, B.contratado_em, A.data_vencimento), '/', timestampdiff(month, B.contratado_em,B.encerrado_em)) parcela
        FROM saida A 
        LEFT JOIN prestadorservico B ON (A.id_prestador = B.id_prestador)
        WHERE A.id_saida = '{$id_saida}'";
        $qry = mysql_query($sql);
        $row = mysql_fetch_assoc($qry);
        return $row['parcela'];
    }

    public function gerarEstorno ($id_saida) {
        $sql = "SELECT * FROM saida WHERE id_saida = '{$id_saida}' LIMIT 1;";
        $qry = mysql_query($sql);
        $row = mysql_fetch_assoc($qry);
        
        $sqlFile = "SELECT * FROM saida_files WHERE id_saida = '{$id_saida}'";
        $qryFile = mysql_query($sqlFile);
        while($rowFile = mysql_fetch_assoc($qryFile)){
            
            $arrayFile[] = $rowFile;
//            print_array($arrayFile);
        }
        
        /**
         * Gravando o esstorno na saida
         */
        $update = "UPDATE saida SET estorno = 1 WHERE id_saida = '{$id_saida}' LIMIT 1;";
        $ok = mysql_query($update);
        if(!$ok) {
            return json_encode(['status' => 0, 'msg' => 'Erro ao estornar a saída!']);
        }
        
        /**
         * Cadastrando a nova saida
         */
        $row['status'] = 1;
        $row['id_saida_estorno'] = $id_saida;
        $row['id_user'] = $_COOKIE['logado'];
        unset($row['id_saida']);
        
        $keysS = implode(',', array_keys($row));
        $valuesS = implode("', '", $row);

        $insertS = "INSERT INTO saida ($keysS) VALUES ('" . $valuesS . "');";
        $ok = mysql_query($insertS) or die('Erro' . mysql_error());
        if(!$ok) {
            return json_encode(['status' => 0, 'msg' => 'Erro ao criar a saida!']);
        }
        
        $id_saida_nova = mysql_insert_id();
        
        foreach ($arrayFile as $key => $value) {
            $ar = $value;
            $ar['id_saida'] = $id_saida_nova;
            unset($ar['id_saida_file']);
            
            $kFile = implode(',', array_keys($ar));
            $vFile = implode("', '", $ar);

            $insFile = "INSERT INTO saida_files ($kFile) VALUES ('" . $vFile . "');";
            mysql_query($insFile) or die('Erro' . mysql_error());
            $new_id_file = mysql_insert_id();
            $arquivo = "../../comprovantes/{$value['id_saida_file']}.{$value['id_saida']}.pdf";
            if(file_exists($arquivo)){
                copy($arquivo, "../../comprovantes/{$new_id_file}.{$id_saida_nova}.pdf");
            }
        }
        
        /**
         * Cadastrando a nova entrada
         */
        $array['status'] = 1;
        $array['id_saida_estorno'] = $id_saida;
        $array['id_banco'] = $row['id_banco'];
        $array['valor'] = str_replace(',', '.', $row['valor']);
        $array['id_regiao'] = $row['id_regiao'];
        $array['id_projeto'] = $row['id_projeto'];
        $array['id_user'] = $_COOKIE['logado'];
        $array['nome'] = 'ESTORNO - ' . $row['nome'];
        $array['especifica'] = 'ESTORNO - ' . $row['especifica'];
        $array['tipo'] = 27;
        $array['data_proc'] = date("Y-m-d H:i:s");
        $array['data_vencimento'] = date("Y-m-d H:i:s");
//        $array['data_vencimento'] = $row['data_vencimento'];
        
        $keysE = implode(',', array_keys($array));
        $valuesE = implode("', '", $array);

        $insert = "INSERT INTO entrada ($keysE) VALUES ('" . $valuesE . "');";
        mysql_query($insert) or die('Erro' . mysql_error());
        $id_entrada_nova = mysql_insert_id();
        if(!$ok) {
            return json_encode(['status' => 0, 'msg' => 'Erro ao criar a entrada!']);
        }
        
        $usuario = carregaUsuario();
        
        $acao = "Estorno da saída ({$id_saida}), Criação da entrada ({$id_entrada_nova}) e saída ({$id_saida_nova})";
        $sqlLog = "INSERT INTO new_log 
                   (id_user, id_regiao, id_local, tabela, tipo_user, grupo_user, horario, ip, acao) 
                   VALUES 
                   ('{$usuario['id_funcionario']}','{$usuario['id_regiao']}','3', 'saida', '{$usuario['tipo_usuario']}','{$usuario['grupo_usuario']}',NOW(),'{$_SERVER['REMOTE_ADDR']}','$acao')";
        $queryLog = mysql_query($sqlLog);
        
        return json_encode(['status' => 1, 'msg' => 'Saida estonada com sucesso!']);
    }

    public function getAdiantamentoByPrestador ($id_prestador) {
        $sql = "SELECT * FROM saida A WHERE A.id_prestador = '{$id_prestador}' AND tipo IN (SELECT id_entradasaida FROM entradaesaida WHERE adiantamento = 1) AND status = 2";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)) {
            $array[] = $row;
        }
        return $array;
    }
}
?>
