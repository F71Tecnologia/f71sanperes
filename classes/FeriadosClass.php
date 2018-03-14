<?php

/**
 * Description of FeriadosClass
 *
 * @author Ramon Lima
 */
class FeriadosClass {
    //put your code here

    /**
     *
     * @param type $mes
     */
    public function getFeriados($mes = null) {
        $_mes = ($mes !== null) ? $mes : date('m');

        $sql = "SELECT *,DATE_FORMAT(data, '%d/%m') AS dataBr FROM rhferiados WHERE 
		MONTH(data) = '$_mes' AND status = '1' ORDER BY MONTH(data),DAY(data)";
        $result = mysql_query($sql);
        $feriados = array();
        while ($row = mysql_fetch_array($result)) {
            $feriados[] = $row;
        }

        return $feriados;
    }

    /*
     * Abaixo, seguem funcoes copiads do arquivo FeriadoClass.php, que na 
     * verdade não é uma classe, mas sim um arquivo de funções organizado.
     * 
     * OBS: nem todas as funções foram copiadas, pois algumas delas nada tinham 
     * a ver com a tabela rhferiados.
     * 
     */

    // antes getFeriado()
    public function getFeriadoAll() {
        $sql = "SELECT *, CONCAT(DATE_FORMAT(A.data, '%d/%m'),'/',EXTRACT(YEAR FROM CURDATE())) AS data_m, B.regiao AS nome_regiao
            FROM rhferiados AS A
            LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
            WHERE A.status = 1
            ORDER BY DATE_FORMAT(A.data, '%m/%d')";

        $result = mysql_query($sql) or die(mysql_error());
        $feriados = array();
        while ($row = mysql_fetch_array($result)) {
            $feriados[] = $row;
        }

        return $feriados;
    }

    public function getFeriadoID($id_feriado) {
        $sql = "SELECT *, CONCAT(DATE_FORMAT(A.data, '%d/%m'),'/',EXTRACT(YEAR FROM CURDATE())) AS data_m, B.regiao AS nome_regiao
            FROM rhferiados AS A
            LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
            WHERE A.status = 1 AND A.id_feriado = '{$id_feriado}'";
        $feriado = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($feriado);
        return $row;
    }

    public function getFeriadosProjetosAssoc ($id_feriado) {

        $sql = "SELECT A.id_projeto, B.nome
                FROM rhferiados_projetos_assoc A
                LEFT JOIN projeto B ON A.id_projeto = B.id_projeto
                WHERE id_feriado = $id_feriado AND STATUS = 1
                ORDER BY nome";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_assoc($query)) {

            $arr[$row['id_projeto']] = $row['id_projeto'] . ' - ' . $row['nome'];

        }

        return $arr;

    }

    public function getFeriadoTotal($id_regiao, $nome_feriado) {
        $sql = "SELECT *
            FROM rhferiados
            WHERE id_regiao = '{$id_regiao}'
            AND nome = '{$nome_feriado}'
            AND status = 1";
        $feriado = mysql_query($sql) or die(mysql_error());
        $total = mysql_num_rows($feriado);
        return $total;
    }

    public function alteraStatusFeriado($id_feriado, $usuario) {
        $sql = "UPDATE rhferiados SET status = 0 WHERE id_feriado = {$id_feriado}";
        $qry = mysql_query($sql);

        //dados usuario para cadastro de log
        $local = "Feriado";

        $acao = "Exclusão de Feriado: ID" . $id_feriado;

        $dados['usuario'] = $usuario;

        $this->cadLog($local, $acao, $dados);

        return $qry;
    }

    public function cadFeriado($dados) {
        $total_feriado = $this->getFeriadoTotal($dados['id_regiao'], $dados['nome_feriado']);
        $dados['id_projeto'] = array_unique($dados['id_projeto']);

        if($dados['tipo'] == "Nacional"){
            $dados['tipo_feriado'] = 1;
        }else if($dados['tipo'] == 'Estadual'){
            $dados['tipo_feriado'] = 2;
        }else{
            // Municipal
            $dados['tipo_feriado'] = 3;
        }

        asort($dados['id_projeto']);

//        print_array($dados['id_projeto']);
//        exit();

        if ($total_feriado != 0) {
            $_SESSION['MESSAGE'] = 'Já Existe um Feriado ' . $dados['nome_feriado'] . ' cadastrado nessa Região';
            $_SESSION['MESSAGE_COLOR'] = 'alert-warning';
            $_SESSION['regiao'] = $dados['id_regiao'];
        } else {
            $insere_feriado = mysql_query("INSERT INTO rhferiados (id_user_cad, data_cad, id_regiao, tipo, nome, data, movel, status, tipo_feriado, uf, cod_municipio) VALUES 
                            ('{$dados['id_usuario']}', '{$dados['data_cad']}', '{$dados['id_regiao']}', '{$dados['tipo']}', '{$dados['nome_feriado']}', '{$dados['data_feriado']}','{$dados['movel']}', '1', '{$dados['tipo_feriado']}', '{$dados['uf']}', '{$dados['municipio']}')
                            ") or die(mysql_error());

            $id = mysql_insert_id();

            foreach ($dados['id_projeto'] as $val) {
                $sqlProAssoc = "INSERT INTO rhferiados_projetos_assoc (id_feriado, id_projeto) VALUES ('$id', '$val')";
                $queryProAssoc = mysql_query($sqlProAssoc);
            }

            //dados usuario para cadastro de log
            $local = "Feriado";

            $acao = "Cadastro de Feriado: ID".$id;

            $insere_log = $this->cadLog($local, $acao, $dados);

            if ($insere_feriado && $insere_log) {
                $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
                $_SESSION['MESSAGE_COLOR'] = 'alert-success';
                session_write_close();
                header('Location: index.php');
            } else {
                $_SESSION['MESSAGE'] = 'Erro ao cadastrar o feriado ' . $dados['nome_feriado'];
                $_SESSION['MESSAGE_COLOR'] = 'alert-danger';
                $_SESSION['regiao'] = $id_regiao;
                session_write_close();
                header('Location: index.php');
            }
        }
    }

    public function alteraFeriado($dados) {

        $dados['id_projeto'] = array_unique($dados['id_projeto']);
        asort($dados['id_projeto']);

        if($dados['tipo'] == "Nacional"){
            $dados['tipo_feriado'] = 1;
        }else if($dados['tipo'] == 'Estadual'){
            $dados['tipo_feriado'] = 2;
        }else{
            // Municipal
            $dados['tipo_feriado'] = 3;
        }
        //dados usuario para cadastro de log
        $local = "Feriado";
      //  print_r($dados['municipio']); die();
        $altera_empresa = mysql_query("UPDATE rhferiados SET id_regiao = '{$dados['id_regiao']}', tipo = '{$dados['tipo']}', nome = '{$dados['nome_feriado']}', data = '{$dados['data_feriado']}', movel = '{$dados['movel']}',
          tipo_feriado = '{$dados['tipo_feriado']}', uf = '{$dados['uf']}', cod_municipio = '{$dados['municipio']}' WHERE id_feriado = '{$dados['id_feriado']}'") or die(mysql_error());

        $sqlRemoveProjetos = "UPDATE rhferiados_projetos_assoc SET status = 0 WHERE id_feriado = {$dados['id_feriado']}";
        $queryRemoveProjetos = mysql_query($sqlRemoveProjetos);

        foreach ($dados['id_projeto'] as $val) {
            if ($val > 0) {
                $sqlProAssoc = "INSERT INTO rhferiados_projetos_assoc (id_feriado, id_projeto) VALUES ('{$dados['id_feriado']}', '$val')";
                $queryProAssoc = mysql_query($sqlProAssoc);
            }
        }

        $acao = "Alteração do Feriado: ID" . $dados['id_feriado'];
        $insere_log = $this->cadLog($local, $acao, $dados);

        if ($altera_empresa && $insere_log) {
            $_SESSION['MESSAGE'] = 'Informações alteradas com sucesso!' . $id_regiao;
            $_SESSION['MESSAGE_COLOR'] = 'alert-success';
            $_SESSION['regiao'] = $id_regiao;
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao atualizar a unidade';
            $_SESSION['MESSAGE_COLOR'] = 'alert-danger';
            $_SESSION['regiao'] = $id_regiao;
        }
    }

    protected function cadLog($local,$acao,$dados) {
        //dados usuario para cadastro de log
        $ip = $_SERVER['REMOTE_ADDR'];
        $id_usuario = $dados['usuario']['id_funcionario'];
        $tipo_usuario = $dados['usuario']['tipo_usuario'];
        $grupo_usuario = $dados['usuario']['grupo_usuario'];
        $regiao = $dados['usuario']['id_regiao'];

        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                                ('{$id_usuario}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());

        return $insere_log;
    }

}
