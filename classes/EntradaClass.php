<?php

require_once('c_planodecontasClass.php');
require_once('ApiClass.php');

class Entrada extends c_planodecontasClass {

    private $api;
    private $masterApi = 1;

    /**
     * 
     */
    public function __construct() {

        $this->api = new ApiClass();
    }

    /**
     * 
     * @param type $id_ent_sai
     * @return type
     */
    public function getEntradaSaida($id_ent_sai) {
        $tipo = ($_REQUEST['tipo'] == "entrada") ? 1 : 0;
        $nome_tipo = $_REQUEST['tipo'];
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $tipo_data = $_REQUEST['tipodata'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];

        if ($id_ent_sai != '-1') {
            $where = ($id_ent_sai == "todos") ? "AND B.tipo = {$tipo}" : "AND B.id_entradasaida = {$id_ent_sai}";
        }

        $sql = "SELECT B.*
            FROM {$nome_tipo} AS A
            LEFT JOIN entradaesaida AS B ON(B.id_entradasaida = A.tipo)
            WHERE A.id_projeto = {$projeto} AND A.id_banco = {$banco} AND MONTH(A.{$tipo_data}) = {$mes} AND YEAR(A.{$tipo_data}) = {$ano} AND A.status = 2 {$where}
            GROUP BY B.nome
            ORDER BY A.tipo, A.{$tipo_data} DESC";

        $res = mysql_query($sql) or die(mysql_error());
        return $res;
    }

    /**
     * 
     * @param type $id_ent_sai
     * @return type
     */
    public function getEntradaSaidaMes($id_ent_sai) {
        $tipo = $_REQUEST['tipo'];
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $tipo_data = $_REQUEST['tipodata'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST['ano'];

        $sql = "SELECT A.nome AS nome_item, A.especifica, B.nome AS pago_por, DATE_FORMAT(A.data_pg, '%d/%m/%Y') AS pago_em, REPLACE(A.valor, ',', '.') AS valor_item
            FROM {$tipo} AS A
            LEFT JOIN funcionario AS B ON(B.id_funcionario = A.id_userpg)
            WHERE A.id_projeto = {$projeto} AND A.id_banco = {$banco} AND MONTH(A.{$tipo_data}) = {$mes} AND YEAR(A.{$tipo_data}) = {$ano} AND A.status = 2 AND A.tipo = {$id_ent_sai}
            ORDER BY A.{$tipo_data} DESC";

        $res = mysql_query($sql) or die(mysql_error());
        return $res;
    }

    /**
     * 
     * @param type $id_banco
     * @param type $tipo
     * @param type $id_ent_sai
     * @return type
     */
    public function getEntradaSaidaBanco($id_banco, $tipo, $id_ent_sai) {
        $ano = $_REQUEST['ano'];

        if ($id_ent_sai == "todos") {
            $and = "";
        } else {
            $and = "AND A.tipo = {$id_ent_sai}";
        }

        $sql = "SELECT *, B.nome AS nome_tipo, COUNT(A.tipo) AS tipo_total, MONTH(A.data_pg) AS mes_tipo, SUM(REPLACE(valor, ',', '.')) as valor_tot
            FROM {$tipo} AS A
            LEFT JOIN entradaesaida AS B ON (B.id_entradasaida = A.tipo)
            WHERE A.id_banco = {$id_banco} AND YEAR(A.data_vencimento) = {$ano} AND A.status = 2 {$and}
            GROUP BY MONTH(A.data_vencimento), B.id_entradasaida
            ORDER BY A.data_pg";

        $res = mysql_query($sql) or die(mysql_error());
        return $res;
    }

    /**
     * METODO DO WEBSERVECE
     * @return int
     */
    public function getEntrada() {

        /**
         * VARIÁVEIS LOCAIS 
         */
        $entrada = array();
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $dataIni = converteData($_REQUEST['data_ini']);
        $dataFim = converteData($_REQUEST['data_fim']);

        /**
         * TIPOS PERMITIDOS 
         * PARA VISUALIZAÇÃO DO 
         * WEBSERVECE
         */
        if (WEBSERVICE === true) {
            $campos = "D.cnpj AS cnpjPontoVistoria,A.valor AS valorTotalNF,A.valor_iss AS valorISS,'' AS caminho,'' AS extensao, A.status";
            $condData = "A.data_vencimento BETWEEN '{$dataIni}' AND '{$dataFim}' ";
            $banco = "todos";
        } else {
            $campos = "*, A.nome AS entrada_nome, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data2, B.nome AS tipo_entrada, C.nome AS banco_nome, C.agencia AS banco_agencia, C.conta AS banco_conta";
            $condData = "A.data_proc >= '{$dataIni} 00:00:00' AND A.data_proc <= '{$dataFim} 23:59:59' AND STATUS = '2'";
        }

        /**
         * QUERY
         */
        if ($banco == 'todos') {
            $query = "SELECT {$campos}
                    FROM entrada AS A
                    LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
                    LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                    LEFT JOIN rhempresa AS D ON (A.id_regiao = D.id_regiao)
                    WHERE {$condData} AND A.id_projeto = '{$projeto}'
                    ORDER BY A.data_vencimento";
        } else {
            $query = "SELECT {$campos}
                    FROM entrada AS A
                    LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
                    LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                    LEFT JOIN rhempresa AS D ON (A.id_regiao = D.id_regiao)
                    WHERE {$condData} AND A.id_projeto = '{$projeto}' AND A.id_banco = '{$banco}'
                    ORDER BY A.data_vencimento";
        }

        /**
         * RESULT
         */
        $sql = mysql_query($query) or die(mysql_error());

        /**
         * RETORNO DO WEBSERVECE
         */
        if (WEBSERVICE === true) {
            $entrada = $this->api->montaRetorno($sql);
        }

        return $entrada;
    }

    /**
     * METODO DO WEBSERVECE
     * 
     */
    public function getRecTarifaria() {
        $receitaTarifaria = array();

        try {

            /**
             * TIPOS PERMITIDOS 
             * PARA VISUALIZAÇÃO DO 
             * WEBSERVECE
             */
            if (WEBSERVICE === true) {
                $campos = "A.id_regiao, B.cnpj, A.valor, '' as valorTotalISS, '' as caminhoArquivoDMS, '' as extensao";
            } else {
                $campos = " * ";
            }

            $query = "SELECT {$campos} FROM entrada AS A 
                            LEFT JOIN rhempresa AS B ON(A.id_projeto = B.id_projeto)
                      WHERE  A.`status` = 2";

            $sql = mysql_query($query) or die("Erro ao selecioanar Receita Tarifaria.");
            $receitaTarifaria = $this->api->montaRetorno($sql);
        } catch (Exception $e) {
            echo $e->getMessage('Erro ao selecionar Entidades Auxiliares');
        }

        return $receitaTarifaria;
    }

    /**
     * 
     * @param type $entrada_id
     * @return type
     */
    public function getEntradaID($entrada_id) {
        $qry = "SELECT A.*, D.parceiro_id AS parceiro_cod, A.tipo AS entrada_tipo
            FROM entrada AS A
            LEFT JOIN notas_assoc AS B ON(A.id_entrada = B.id_entrada)
            LEFT JOIN notas AS C ON(C.id_notas = B.id_notas)
            LEFT JOIN parceiros AS D ON(D.parceiro_id = C.id_parceiro)
            WHERE A.id_entrada = '{$entrada_id}'";
        $sql = mysql_query($qry) or die(mysql_error());
        $row = mysql_fetch_assoc($sql);
        return $row;
    }

    public function getArquivos2($regiao) {

        $dataIni = $_REQUEST['ano'] . "-" . $_REQUEST['mes'] . "-01";
        $dataFim = $_REQUEST['ano'] . "-" . $_REQUEST['mes'] . "-31 23:59:59";

        $sql = "SELECT C.id_file, C.tipo, C.id_notas, B.id_entrada
            FROM notas AS A
            LEFT JOIN notas_assoc AS B ON(A.id_notas = B.id_notas)
            LEFT JOIN notas_files AS C ON(A.id_notas = C.id_notas)
            WHERE B.id_entrada IN (SELECT id_entrada FROM entrada WHERE C.id_notas > 0 AND data_vencimento >= '{$dataIni}' AND data_vencimento <= '{$dataFim}' AND id_regiao = '{$regiao}' AND status = '2')";

        $result = mysql_query($sql) or die(mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $array[$row['id_entrada']][$row['id_file']] = $row;
        }
        if ($_COOKIE['debug'] == 666) {
            echo $sql . '<br>';
            print_array($array);
        }
        return $array;
    }

    /**
     * 
     * @param type $id_parceiro
     * @return type
     */
    public function getNotas($id_parceiro) {
        $sql = "SELECT *, DATE_FORMAT(A.data_emissao, '%d/%m/%Y') AS data_emi
            FROM notas AS A
            INNER JOIN notas_assoc AS B ON (B.id_notas = A.id_notas)
            WHERE A.id_parceiro = {$id_parceiro} AND A.status = '1'
            GROUP BY (A.id_notas)";

        $notas = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($notas);
        return $row;
    }

    /**
     * 
     * @param type $projeto
     * @return type
     */
    public function getValorNotasProjeto($projeto) {
        $sql = "SELECT SUM(REPLACE(A.valor,',','.')) valor FROM 
        (notas B INNER JOIN notas_assoc C ON B.id_notas = C.id_notas) 
        INNER JOIN entrada A
        ON C.id_entrada = A.id_entrada
        WHERE B.id_projeto = '$projeto' AND B.status = 1 AND B.tipo_contrato = '$projeto' AND A.status IN(1,2) AND B.tipo_contrato2 = 'projeto'";
        $sql = mysql_query($sql) or die(mysql_error());
        $result = mysql_fetch_assoc($sql);
        return $result['valor'];
    }

    /**
     * 
     * @param type $nota
     * @param type $ano
     * @return type
     */
    public function getValorNotasAno($nota, $ano) {
        $sql = "SELECT SUM(REPLACE(A.valor,',','.')) valor FROM 
        (notas B INNER JOIN notas_assoc C ON B.id_notas = C.id_notas) 
        INNER JOIN entrada A
        ON C.id_entrada = A.id_entrada
        WHERE B.id_notas = '$nota' AND YEAR(data_emissao) = '$ano' AND A.status = 2;";
        $sql = mysql_query($sql) or die(mysql_error());
        $result = mysql_fetch_assoc($sql);
        return $result['valor'];
    }

    /**
     * 
     * @param type $id_regiao
     * @return type
     */
    public function getParceiros($id_regiao) {
        if ($id_regiao == 37) {
            $sql = "SELECT parceiro_id, parceiro_nome
                FROM parceiros";
        } else {
            $sql = "SELECT parceiro_id, parceiro_nome
                FROM parceiros
                WHERE id_regiao = '{$id_regiao}'";
        }

        $parceiro = mysql_query($sql) or die(mysql_error());
        return $parceiro;
    }

    /**
     * 
     * @param type $id_regiao
     * @param type $id_master
     * @return string
     */
    public function getRegiaoNotas($id_regiao, $id_master) {
        if ($id_regiao == 37) {
            $sql = mysql_query("SELECT *
                FROM regioes
                WHERE status_reg = '1' AND STATUS = '1' AND id_regiao NOT IN(43,36)");
        } else {
            $sql = mysql_query("SELECT *
                FROM regioes
                WHERE status_reg = '1' AND STATUS = '1' AND id_master = '{$id_master}'");
        }

        $reg = array("-1" => "« Selecione »");
        while ($rst = mysql_fetch_assoc($sql)) {
            $reg[$rst['id_regiao']] = $rst['id_regiao'] . " - " . $rst['regiao'];
        }
        return $reg;
    }

    /**
     * 
     * @param type $id_entrada
     * @return type
     */
    public function getNotasEntrada($id_entrada) {
        $sql = "SELECT *, A.status AS status_entrada, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS data_nf, CAST(
            REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor_nf, C.id_banco AS banco_id, C.nome AS banco_nome
            FROM entrada AS A
            LEFT JOIN notas_assoc AS B ON(B.id_entrada = A.id_entrada)
            LEFT JOIN bancos AS C ON(C.id_banco = A.id_banco)
            WHERE B.id_entrada = '{$id_entrada}' AND A.status IN (2,1)";
        $result = mysql_query($sql) or die(mysql_error());
        return $result;
    }

    /**
     * 
     * @param type $id_nota
     * @return type
     */
    public function getEntradaNotas($id_nota) {
        $sql = "SELECT *, A.status AS status_entrada, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS data_nf, CAST(
            REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor_nf, C.id_banco AS banco_id, C.nome AS banco_nome
            FROM entrada AS A
            LEFT JOIN notas_assoc AS B ON(B.id_entrada = A.id_entrada)
            LEFT JOIN bancos AS C ON(C.id_banco = A.id_banco)
            WHERE B.id_notas = '{$id_nota}' AND A.status IN (2,1)";
        $result = mysql_query($sql) or die(mysql_error());
        return $result;
    }

    /**
     * 
     * @param type $id_nota
     * @param type $id_entrada
     * @return type
     */
    public function getArquivos($id_nota, $id_entrada = null) {
        if ($id_entrada == null) {
            $sql = "SELECT *
                FROM notas_files
                WHERE id_notas = {$id_nota} AND status = 1";
        } else {
            $sql = "SELECT C.id_file, C.tipo, C.id_notas
                FROM notas AS A
                LEFT JOIN notas_assoc AS B ON(A.id_notas = B.id_notas)
                LEFT JOIN notas_files AS C ON(A.id_notas = C.id_notas)
                WHERE B.id_entrada = {$id_entrada}
                GROUP BY C.id_file";
        }

        $result = mysql_query($sql) or die(mysql_error());
        return $result;
    }

    /**
     * 
     * @return string
     */
    public function getTipo() {
        $sql = mysql_query("SELECT *
                            FROM entradaesaida
                            WHERE cod LIKE '321%' OR grupo = 5
                            ORDER BY nome");
        $tipo = array("-1" => "« Selecione »");
        while ($rst = mysql_fetch_assoc($sql)) {
            $tipo[$rst['id_entradasaida']] = $rst['cod'] . " - " . $rst['nome'];
        }
        return $tipo;
    }

    /**
     * 
     * @return string
     */
    public function getTipoEntrada() {
        $sql = mysql_query("SELECT *
                        FROM entradaesaida
                        WHERE tipo = 1 ORDER BY id_entradasaida");
        $tipo = array("-1" => "« Selecione »", "todos" => "Todos");
        while ($rst = mysql_fetch_assoc($sql)) {
            $tipo[$rst['id_entradasaida']] = $rst['id_entradasaida'] . " - " . $rst['nome'];
        }
        return $tipo;
    }

    /**
     * 
     * @return string
     */
    public function getTipoSaida() {
        $sql = mysql_query("SELECT *
                        FROM entradaesaida
                        WHERE tipo = 0 ORDER BY id_entradasaida");
        $tipo = array("-1" => "« Selecione »", "todos" => "Todos");
        while ($rst = mysql_fetch_assoc($sql)) {
            $tipo[$rst['id_entradasaida']] = $rst['id_entradasaida'] . " - " . $rst['nome'];
        }
        return $tipo;
    }

    /**
     * 
     * @param type $regiao
     * @return type
     */
    public function getEntradaRel($regiao) {
        $banco = $_REQUEST['banco'];
        $auxBanco = ($_REQUEST['banco'] > 0) ? "AND A.id_banco IN ({$_REQUEST['banco']})" : '';
//        $dataIni = ConverteData($_REQUEST['inicio'], 'Y-m-d');
//        $dataFim = ConverteData($_REQUEST['final'], 'Y-m-d');       
        $dataIni = $_REQUEST['ano'] . "-" . $_REQUEST['mes'] . "-01";
        $dataFim = $_REQUEST['ano'] . "-" . $_REQUEST['mes'] . "-31 23:59:59";
        $auxProjeto = ($_REQUEST['id_projeto'] > 0) ? "AND A.id_regiao IN ({$_REQUEST['id_projeto']})" : '';
//        if($banco != 'todos'){
//            $result = "SELECT *, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data_vencimento, CAST(
//                REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor_nf, CAST(
//                REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS entrada_adicional, A.nome AS entrada_nome, C.nome1 AS func_nome,
//                B.agencia AS banco_agencia, B.conta AS banco_conta, D.nome AS tipo_entrada, A.tipo AS tipo
//                FROM entrada AS A
//                LEFT JOIN bancos AS B ON(B.id_banco = A.id_banco)
//                LEFT JOIN funcionario AS C ON(C.id_funcionario = A.id_user)
//                LEFT JOIN entradaesaida AS D ON (A.tipo = D.id_entradasaida)
//                WHERE A.data_vencimento >= '{$dataIni}' AND A.data_vencimento <= '{$dataFim}' AND A.id_banco = '{$banco}' AND A.id_regiao = '{$regiao}' AND A.status = '2'
//                ORDER BY A.data_vencimento, A.id_banco";
//        }else{
        $result = "SELECT *, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data_vencimento, CAST(
                REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor_nf, CAST(
                REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)) AS entrada_adicional, A.nome AS entrada_nome, C.nome1 AS func_nome,
                B.agencia AS banco_agencia, B.conta AS banco_conta, D.nome AS tipo_entrada, A.tipo AS tipo
                FROM entrada AS A
                LEFT JOIN bancos AS B ON(B.id_banco = A.id_banco)
                LEFT JOIN funcionario AS C ON(C.id_funcionario = A.id_user)
                LEFT JOIN entradaesaida AS D ON (A.tipo = D.id_entradasaida)
                WHERE A.data_vencimento >= '{$dataIni}' AND A.data_vencimento <= '{$dataFim}'
                AND A.status IN (1,2) $auxProjeto $auxBanco
                ORDER BY A.data_vencimento, A.id_banco ASC";
//        }

        $entrada = mysql_query($result) or die(mysql_error());
        return $entrada;
    }

    /**
     * 
     * @param type $id
     * @return boolean
     */
    public function delEntrada($id) {
        $local = "Financeiro - Cadastros";
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = date('Y-m-d H:i:s');
        $usuario = carregaUsuario();
        $user = $usuario['id_funcionario'];
        $acao = "Entrada Excluida: ID" . $id;
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao = $usuario['id_regiao'];

        $sql = "DELETE FROM notas_assoc WHERE id_entrada = {$id}";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);

        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                                            ('{$user}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());

        if ($insere_log) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $regiao
     * @return type
     */
    public function cadEntrada($regiao) {
        $projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
        $nome = utf8_decode($_REQUEST['nome']);
        $especifica = utf8_decode($_REQUEST['especifica']);
        $tipo = $_REQUEST['tipo'];
        $adicional = str_replace(".", "", $_REQUEST['adicional']);
        $valor = str_replace("R$ ", "", str_replace(",", ".", str_replace(".", "", $_REQUEST['valor'])));
        $valor_iss = (isset($_REQUEST['valor_iss'])) ? str_replace("R$ ", "", str_replace(",", ".", str_replace(".", "", $_REQUEST['valor_iss']))) : 0;
        $data_proc = date('Y-m-d H:i:s');
        list($dia, $mes, $ano) = explode('/', $_REQUEST['data_credito']);
        $data_credito = converteData($_REQUEST['data_credito']);
        $subtipo = $_REQUEST['subtipo'];
        $n_subtipo = $_REQUEST['n_subtipo'];
        $num_doc = $_REQUEST['n_documento'];
        $regiao_sel = ($regiao == '-1') ? "" : $regiao;

        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $local = "Financeiro - Cadastros";
        $ip = $_SERVER['REMOTE_ADDR'];
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];

        $insere = mysql_query("INSERT INTO entrada(id_regiao, id_projeto, id_banco, id_user, nome, especifica, tipo, adicional, valor, valor_iss, data_proc, data_vencimento, subtipo, n_subtipo, numero_doc) values 
	    ('{$regiao}', '{$projeto}', '{$banco}', '{$id_usuario}', '{$nome}', '{$especifica}', '{$tipo}', '{$adicional}', '{$valor}', '{$valor_iss}', '{$data_proc}', '{$data_credito}', '{$subtipo}', '{$n_subtipo}', '{$num_doc}')") or die(mysql_error());
        $id_ultima_entrada = mysql_insert_id();
        $acao = "Entrada Cadastrada: ID{$id_ultima_entrada}";

        if ($_REQUEST['radio_nota'] != "" && $tipo == 12) {
            $id_nota = $_REQUEST['radio_nota'];
            $insere_nota = mysql_query("INSERT INTO notas_assoc (id_notas, id_entrada) VALUES ('{$id_nota}', '{$id_ultima_entrada}')") or die(mysql_error());
        }

        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
          ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());

        if ($insere && $insere_log) {
            $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';
            $_SESSION['MESSAGE_TYPE'] = 'info';
            $_SESSION['regiao'] = $regiao;
            $_SESSION['pausa'] = 'pausar';
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao cadastrar a entrada ' . $nome;
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $regiao;
            $_SESSION['pausa'] = 'pausar';
        }

        $sqlBanco = "SELECT adiantamento FROM bancos WHERE id_banco = '{$banco}' LIMIT 1;";
        $qryBanco = mysql_query($sqlBanco);
        $rowBanco = mysql_fetch_assoc($qryBanco);

        //LANÇAMETO CONTABIL
        if ($rowBanco['adiantamento'] == 0) {
            $array_lancamento = array('id_entrada' => $id_ultima_entrada, 'id_projeto' => $projeto, 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => date("Y-m-d"), 'historico' => $especifica);
            $id_lancamento = $this->inserirLancamento($array_lancamento);
        }
        return $id_ultima_entrada;
    }

    /**
     * 
     * @return type
     */
    public function alteraEntrada() {
        $nome = utf8_decode($_REQUEST['nome']);
        $especifica = utf8_decode($_REQUEST['especifica']);
        $tipo = $_REQUEST['tipo'];
        $data_credito = converteData($_REQUEST['data_credito']);
        $subtipo = $_REQUEST['subtipo'];
        $n_subtipo = $_REQUEST['n_subtipo'];
        $id_entrada = $_REQUEST['id_entrada'];
        $id_nota = $_REQUEST['radio_nota'];

        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $local = "Financeiro - Cadastros";
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "Entrada Editada: ID{$id_entrada}";
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];

        if ($tipo != 12) {
            $subtipo = '';
            $n_subtipo = '';
            $id_nota = '';
        }

        $qry = "UPDATE entrada SET nome = '{$nome}', 
                tipo = '{$tipo}', especifica = '{$especifica}', data_vencimento = '{$data_credito}',
                subtipo = '{$subtipo}', n_subtipo = '{$n_subtipo}' 
                WHERE id_entrada = '{$id_entrada}' LIMIT 1";
        $altera = mysql_query($qry) or die(mysql_error());

        mysql_query("DELETE FROM notas_assoc WHERE id_entrada = '{$id_entrada}'");

        if ($id_nota != "") {
            mysql_query("INSERT INTO notas_assoc (id_notas, id_entrada) VALUES ('{$id_nota}', '{$id_entrada}')");
        }

        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
          ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());

        if ($altera && $insere_log) {
            $_SESSION['MESSAGE'] = 'Informações atualizadas com sucesso!';
            $_SESSION['MESSAGE_TYPE'] = 'info';
            $_SESSION['regiao'] = 'regiao';
            $_SESSION['pausa'] = 'pausar';
            $_SESSION['banco'] = $_REQUEST['banco_sel'];
            $_SESSION['mes'] = $_REQUEST['mes_sel'];
            $_SESSION['ano'] = $_REQUEST['ano_sel'];
            session_write_close();
            return $id_entrada;
            //header('Location: relatorios/rel_entrada_saida.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao atualizar a entrada ' . $id_entrada;
            $_SESSION['MESSAGE_TYPE'] = 'warning';
            $_SESSION['regiao'] = 'regiao';
            $_SESSION['pausa'] = 'pausar';
            $_SESSION['banco'] = $_REQUEST['banco_sel'];
            $_SESSION['mes'] = $_REQUEST['mes_sel'];
            $_SESSION['ano'] = $_REQUEST['ano_sel'];
            session_write_close();
            //header('Location: relatorios/rel_entrada_saida.php');
        }
    }

    /**
     * 
     * @return type
     */
    public function getBuscaEntrada() {
        $usuario = carregaUsuario();

        $auxProjeto = (!empty($_REQUEST['projeto'])) ? " AND A.id_projeto = {$_REQUEST['projeto']} " : null;
        $auxBanco = (!empty($_REQUEST['banco'])) ? " AND A.id_banco = {$_REQUEST['banco']} " : null;
        $auxTipo = (!empty($_REQUEST['tipo']) AND $_REQUEST['tipo'] > 0) ? " AND A.tipo = {$_REQUEST['tipo']}" : null;
        $auxDataIni = (!empty($_REQUEST['data_ini'])) ? " AND A.data_vencimento >= '" . converteData($_REQUEST['data_ini']) . "' " : null;
        $auxDataFim = (!empty($_REQUEST['data_fim'])) ? " AND A.data_vencimento <= '" . converteData($_REQUEST['data_fim']) . "' " : null;
        $auxIdEntrada = (!empty($_REQUEST['id_entrada'])) ? " AND A.id_entrada IN({$_REQUEST['id_entrada']}) " : null;
        if (!empty($_REQUEST['nome'])) {
            $nome = explode(' ', $_REQUEST['nome']);
            foreach ($nome as $value) {
                $arrayNome[] = " A.nome LIKE '%$value%' ";
                $arrayEspecifica[] = " A.especifica LIKE '%$value%' ";
            }
            $auxNomeEspecifica = " AND ((" . implode(" AND ", $arrayNome) . ") OR (" . implode(" AND ", $arrayEspecifica) . ")) ";
        }

        $result = "
        SELECT A.*, A.nome AS nome_entrada, DATE_FORMAT(A.data_vencimento,'%d/%m/%Y') AS data2, B.nome AS tipo_entrada, C.nome AS nome_banco, C.id_banco AS id_banco, D.nome nome_projeto
        FROM entrada AS A
        LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
        LEFT JOIN projeto AS D ON (A.id_projeto = D.id_projeto)
        LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
        WHERE A.status = '2' $auxProjeto $auxBanco $auxTipo $auxDataIni $auxDataFim $auxIdEntrada $auxNomeEspecifica
        ORDER BY D.nome, A.data_vencimento, A.id_entrada";
        if($_COOKIE['debug'] == 666) { print_array($result); }
        $entrada = mysql_query($result) or die(mysql_error());
        return $entrada;
    }

    public function getDadosOxxy($data) {

        error_reporting(E_ALL);

        $usuario = carregaUsuario();
        $user = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao = $usuario['id_regiao'];

        if ($data == 'auto') {
            $_data = date('Y-m-d', strtotime('-1 day'));
        } else {
            $_data = date("Y-m-d", strtotime(str_replace("/", "-", $data)));
        }

        $cnpjMask = "%s%s.%s%s%s.%s%s%s/%s%s%s%s-%s%s";

        function format($mask, $string) {
            return vsprintf($mask, str_split($string));
        }
            
            $sql = "SELECT id_arquivo FROM entrada_oxxy e inner join entrada_complemento_oxxy as eco "
                    . "ON eco.id_entrada = e.id_entrada where eco.data_emissao_nf >= $_data";

            $arquivos = [];

            while ($row = mysql_fetch_assoc($sql)) {
                $arquivos[] = $row['id_arquivo'];
            }

            $pagesLeft = 1;

            $page = 1;

            $dados = [];

            while ($pagesLeft > 0) {
                
                $protocolo = array(1 => "http://", 2 => "https://");
                $servico = "wssanperes.sispevi.com.br";
//        $servico = "ws.sanperes.sispevi.com.br";
                $request = "/financeiro.svc/ConsultarDadoComplementar?";
//            $request = "/Financeiro.svc/Consultar?";
                $param = "data={$_data}&pageNum=" . $page;

                $url = $protocolo[2] . $servico . $request . $param;
                $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36";

                $path = __DIR__ . "/curl_cookies/";

                if (!is_dir($path)) {
                    if (!mkdir($path, 0777)) {
                        exit("erro ao criar a pasta de cookie: {$path}");
                    }
                }

                $ckfile = tempnam($path, "cookie.txt");

                $doc = new DOMDocument();
                $doc->load($url);

                $xml = $doc->getElementsByTagName("DadoComplementarDTO");

                foreach ($xml as $item) {

                    $IdArquivo = $item->getElementsByTagName("IdArquivo")->item(0)->nodeValue;
                    if ($item->getElementsByTagName("NumeroNotaFiscal")->item(0)->nodeValue) {
                        if (!in_array($IdArquivo, $arquivos)) {

                            $AgCcDig = $item->getElementsByTagName("AgCcDig")->item(0)->nodeValue;

                            $CodTransicao = $item->getElementsByTagName("CodTransicao")->item(0)->nodeValue;

                            $CodigoBarrasBoleto = $item->getElementsByTagName("CodigoBarrasBoleto")->item(0)->nodeValue;

                            $DataPagamentoBoleto = $item->getElementsByTagName("DataPagamentoBoleto")->item(0)->nodeValue;

                            $FormaArrecadacao = $item->getElementsByTagName("FormaArrecadacao")->item(0)->nodeValue;

                            $FormaPagamento = $item->getElementsByTagName("FormaPagamento")->item(0)->nodeValue;

                            $nrs = $item->getElementsByTagName("NRS")->item(0)->nodeValue;

                            $ValorRecebido = $item->getElementsByTagName("ValorRecebido")->item(0)->nodeValue;

                            $ValorTarifa = $item->getElementsByTagName("ValorTarifa")->item(0)->nodeValue;

                            $dataCadastroArquivo = $item->getElementsByTagName("DataCadastroArquivo");

                            $CodAgArrecadora = $item->getElementsByTagName("CodAgArrecadora")->item(0)->nodeValue;

                            $cnpjUnidadeCreditada = format($cnpjMask, $item->getElementsByTagName("CnpjUnidadeCreditada")->item(0)->nodeValue);

                            //Consulta id da regiao e do projeto vinculado ao cnpj
                            $query = mysql_query("SELECT id_regiao, id_projeto from rhempresa where cnpj='{$cnpjUnidadeCreditada}'");

                            $idRegiaoProjeto = mysql_fetch_assoc($query);

                            $dados[$IdArquivo] = array('id_arquivo' => $IdArquivo, 'ag_cc_dig' => $AgCcDig,
                                'cod_transicao' => $CodTransicao, 'codigo_barras' => $CodigoBarrasBoleto, 'data_pagamento' => $DataPagamentoBoleto,
                                'forma_arrecadacao' => $FormaArrecadacao, 'forma_pagamento' => $FormaPagamento, 'nrs' => $nrs,
                                'valor_recebido' => $ValorRecebido, 'valor_tarifa' => $ValorTarifa, 'data_cadastro' => $dataCadastroArquivo,
                                'cod_ag_arrecadora' => $CodAgArrecadora);

                            $data_pg = explode('T', $DataPagamentoBoleto);

                            $idRegiao = $idRegiaoProjeto['id_regiao'];
                            $idProjeto = $idRegiaoProjeto['id_projeto'];

                            mysql_query("INSERT INTO entrada_oxxy (id_regiao, id_projeto, id_banco, ag_cc_dig, cod_ag_arrecadora, cod_transicao, codigo_barras, id_user, data_pg, hora_pg, 
                    forma_arrecadacao, forma_pagamento, nrs, valor, data_proc, data_vencimento, id_arquivo, status)
                    VALUES('{$idRegiao}','{$idProjeto}','2','{$AgCcDig}','{$CodAgArrecadora}','{$CodTransicao}','{$CodigoBarrasBoleto}',"
                                            . "'{$usuario['id_funcionario']}','{$data_pg[0]}','{$data_pg[1]}','{$FormaArrecadacao}','{$FormaPagamento}','{$nrs}',"
                                            . "'{$ValorRecebido}','{$DataPagamentoBoleto}','{$data_pg[0]}','{$IdArquivo}','2')") or die(mysql_error());

                            $idEntrada = mysql_insert_id();

                            //Dados A partir daqui são inseridos somente na tabela complementar
                            $aliquotaNotaFiscal = $item->getElementsByTagName("AliquotaNotaFiscal")->item(0)->nodeValue;

                            $chassi = $item->getElementsByTagName("Chassi")->item(0)->nodeValue;

                            $classificacao = $item->getElementsByTagName("Classificacao")->item(0)->nodeValue;

                            $cnpjUnidadeEmissaoNotaFiscal = format($cnpjMask, $item->getElementsByTagName("CnpjUnidadeEmissaoNotaFiscal")->item(0)->nodeValue);

                            $codigoVerificacaoNotaFiscal = $item->getElementsByTagName("CodigoVerificacaoNotaFiscal")->item(0)->nodeValue;

                            $dataCredito = str_replace('T', ' ', $item->getElementsByTagName("DataCredito")->item(0)->nodeValue);

                            $dataEmissaoNotaFiscal = str_replace('T', ' ', $item->getElementsByTagName("DataEmissaoNotaFiscal")->item(0)->nodeValue);

                            $dataGeracaoBoleto = str_replace('T', ' ', $item->getElementsByTagName("DataGeracaoBoleto")->item(0)->nodeValue);

                            $dataVencimentoBoleto = str_replace('T', ' ', $item->getElementsByTagName("DataVencimentoBoleto")->item(0)->nodeValue);

                            $idArquivoStatus = $item->getElementsByTagName("IdArquivoStatus")->item(0)->nodeValue;

                            $numeroNotaFiscal = $item->getElementsByTagName("NumeroNotaFiscal")->item(0)->nodeValue;

                            $placa = $item->getElementsByTagName("Placa")->item(0)->nodeValue;

                            $tipoVeiculo = $item->getElementsByTagName("TipoVeiculo")->item(0)->nodeValue;

                            $valorBoleto = $item->getElementsByTagName("ValorBoleto")->item(0)->nodeValue;

                            $valorNotaFiscal = $item->getElementsByTagName("ValorNotaFiscal")->item(0)->nodeValue;

                            $dadosComplementares[] = array('AliquotaNotaFiscal' => $aliquotaNotaFiscal, 'Chassi' => $chassi,
                                'Classificacao' => $classificacao, 'CnpjUnidadeCreditada' => $cnpjUnidadeCreditada,
                                'CnpjUnidadeEmissaoNotaFiscal' => $cnpjUnidadeEmissaoNotaFiscal, 'CodigoVerificacaoNotaFiscal' => $codigoVerificacaoNotaFiscal,
                                'DataCredito' => $dataCredito, 'DataEmissaoNotaFiscal' => $dataEmissaoNotaFiscal, 'DataGeracaoBoleto' => $dataGeracaoBoleto,
                                'DataVencimentoBoleto' => $dataVencimentoBoleto, 'IdArquivoStatus' => $idArquivoStatus, 'NumeroNotaFiscal' => $numeroNotaFiscal,
                                'Placa' => $placa, 'TipoVeiculo' => $tipoVeiculo, 'ValorBoleto' => $valorBoleto, 'ValorNotaFiscal' => $valorNotaFiscal);

                            $dados[$IdArquivo]['Dados Complementares'] = $dadosComplementares[0];

                            mysql_query("INSERT INTO entrada_complemento_oxxy (id_entrada, aliq_nf, chassi, classificacao, cnpj_unid_credito, cnpj_unid_emissao_nf, codigo_verif_nf, 
                        data_credito, data_emissao_nf, data_geracao_boleto, data_vencimento_boleto, id_arquivo_status, num_nf, placa, tipo_veiculo, 
                         valor_boleto, valor_nf)
                    VALUES({$idEntrada}, {$aliquotaNotaFiscal}, '{$chassi}', '{$classificacao}', '{$cnpjUnidadeCreditada}', '{$cnpjUnidadeEmissaoNotaFiscal}', '{$codigoVerificacaoNotaFiscal}',
                        '{$dataCredito}', '{$dataEmissaoNotaFiscal}', '{$dataGeracaoBoleto}', '{$dataVencimentoBoleto}', {$idArquivoStatus}, '{$numeroNotaFiscal}', "
                                            . "'{$placa}', '{$tipoVeiculo}', {$valorBoleto}, {$valorNotaFiscal})") or die(mysql_error());
                        }
                    }
                }

                if ($xml->length == 0) {
                    $pagesLeft = 0;
                } else {
                    $page = $page + 1;
                }
            }

        return $dados;
    }

    public function getEntradaBrGaap($comp) {
        /**
         * VARIÁVEIS LOCAIS 
         */
        $entrada = array();

        $campos = "D.cnpj AS cnpj,A.data_vencimento AS data, '' AS tarifa, A.valor";
        $condData = "DATE_FORMAT(A.data_vencimento, '%Y-%m') = '{$comp}' ";

        $query = "SELECT {$campos}
                    FROM entrada AS A
                    LEFT JOIN entradaesaida AS B ON (A.tipo = B.id_entradasaida)
                    LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                    LEFT JOIN rhempresa AS D ON (A.id_regiao = D.id_regiao)
                    WHERE {$condData} AND D.id_master = '{$this->masterApi}'
                    ORDER BY A.data_vencimento";

        /**
         * RETORNO DO WEBSERVECE
         */
        $consulta = mysql_query($query);
        $entrada = $this->api->montaRetorno($consulta);
        return $entrada;
    }

}

?>
