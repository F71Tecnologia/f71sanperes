<?php
require_once ('LogClass.php');


function getUnidade($id_regiao, $id_projeto) {
    $condicao = "";

    if (is_null($id_regiao)) {
        $condicao = "WHERE campo1 = '{$id_projeto}' AND status_reg = 1";
    } else {
        $condicao = "WHERE id_regiao = '{$id_regiao}' AND campo1 = '{$id_projeto}' AND status_reg = 1";
    }

    $sql = "SELECT *
            FROM unidade
            {$condicao}
            ORDER BY latitude, unidade";
    $unidade = mysql_query($sql) or die(mysql_error());
    return $unidade;
}

function getNomeUnidade($id_unidade) {
    $sql = "SELECT unidade FROM unidade WHERE id_unidade = '{$id_unidade}' LIMIT 1";
    $unidade = mysql_query($sql) or die(mysql_error());
    $unidade = mysql_fetch_assoc($unidade);
    return $unidade['unidade'];
}

function getUnidadeID($id_unidade) {
    $sql = "SELECT A.*, A.local AS local_uni, B.regiao AS nome_regiao, C.nome AS nome_projeto, D.nome nome_coordenador
            FROM unidade AS A
            LEFT JOIN regioes AS B ON B.id_regiao = A.id_regiao
            LEFT JOIN projeto AS C ON C.id_projeto = A.campo1
            LEFT JOIN rh_clt AS D ON A.id_coordenador = D.id_clt
            WHERE A.id_unidade = '{$id_unidade}'";
    $unidade = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($unidade);
    return $row;
}

function getUnidadeTotal($id_regiao, $id_projeto, $nome) {
    $sql = "SELECT *
            FROM unidade
            WHERE id_regiao = '{$id_regiao}'
            AND campo1 = '{$id_projeto}'
            AND unidade = '{$nome}'
            AND status_reg = 1
            ORDER BY unidade";
    $unidade = mysql_query($sql) or die(mysql_error());
    return $unidade;
}

function getRhClt($id_unidade) {
    $sql = "SELECT * FROM rh_clt WHERE id_unidade = {$id_unidade} AND (status < 60 OR status = 200)";
    $clt = mysql_query($sql);
    $tot = mysql_num_rows($clt);
    return $tot;
}

function alteraStatusUnidade($id_unidade, $usuario) {
    $sql = "UPDATE unidade SET status_reg = 0 WHERE id_unidade = {$id_unidade}";
    $qry = mysql_query($sql);
    $res = mysql_fetch_assoc($qry);
    
    return $res;
}

function getUnidadeSelect() {

    $sql = mysql_query("SELECT * FROM unidade");
    $unidades = array("-1" => "« Selecione »");
    while ($row = mysql_fetch_assoc($sql)) {
        $unidades[$row['id_unidade']] = $row['id_unidade'] . " - " . $row['unidade'];
    }

    return $unidades;
}

function cadUnidade($id_regiao, $usuario) {
    $log = new Log();
    
    // variaveis recuperadas        
    $id_regiao = $_REQUEST['regiao_selecionada'];
    $nome = acentoMaiusculo($_REQUEST['nome_unidade']);
    $local_uni = acentoMaiusculo($_REQUEST['local']);
    $tel = $_REQUEST['tel'];
    $tel2 = $_REQUEST['tel_recado'];
    $responsavel = acentoMaiusculo($_REQUEST['nome_responsavel']);
    $cel = $_REQUEST['cel_resp'];
    $email = $_REQUEST['email_responsavel'];
    $projeto = $_REQUEST['projeto'];
    $endereco = acentoMaiusculo($_REQUEST['endereco']);
    $bairro = acentoMaiusculo($_REQUEST['bairro']);
    $cidade = acentoMaiusculo($_REQUEST['cidade']);
    $cep = $_REQUEST['cep'];
    $id_coordenador = $_REQUEST['id_coordenador'];
    $cod_websaass = $_REQUEST['cod_websaass'];
    $cod_servico1 = $_REQUEST['cod_servico1'];
    $cod_servico2 = $_REQUEST['cod_servico2'];
    $uf = $_REQUEST['uf'];
    $ponto_referencia = acentoMaiusculo($_REQUEST['referencia']);

    /**
     * Algoritmo para gerar o valor único do codigo_sodexo para a unidade.
     */
    $sqlMatSod = "SELECT MAX(codigo_sodexo) FROM unidade";
    $queryMatSod = mysql_query($sqlMatSod);
    $valMatSod = mysql_result($queryMatSod, 0);
    $valMatSod += 1;

    $unidade = getUnidadeTotal($id_regiao, $projeto, $nome);
    $total_unidade = mysql_num_rows($unidade);

    if ($total_unidade != 0) {
        $_SESSION['MESSAGE'] = 'Já Existe uma Unidade ' . $nome . ', cadastrada nesse Projeto!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
    } elseif (($nome == '')) {
        $_SESSION['MESSAGE'] = "Verifique o campo Nome, ele não pode ser vazio";
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
    } else {
        $insere_unidade = mysql_query("INSERT INTO unidade (id_regiao,unidade,local,tel,tel2,responsavel,cel,email,campo1, endereco, bairro, cidade, cep, uf, ponto_referencia, cod_websaass, cod_servico1, cod_servico2, id_coordenador, codigo_sodexo) VALUES
          ('{$id_regiao}','{$nome}','{$local_uni}','{$tel}','{$tel2}','{$responsavel}','{$cel}','{$email}','{$projeto}','{$endereco}','{$bairro}','{$cidade}','{$cep}', '{$uf}', '{$ponto_referencia}', '{$cod_websaass}', '{$cod_servico1}', '{$cod_servico2}', '{$id_coordenador}', '{$valMatSod}')") or die(mysql_error());

        $id_unidade = mysql_insert_id();
        $log->log('2', "Unidade ID $id_unidade cadastrada.",'unidade');
        
        if (count($_REQUEST['id_funcao']) > 0) {
            foreach ($_REQUEST['id_funcao'] as $key => $value) {
                $insert = "INSERT INTO vagas_por_unidade (`id_unidade`, `id_funcao`, `qnt`, `criado_em`, `criado_por`) VALUES ('{$id_unidade}', '{$_REQUEST['id_funcao'][$key]}', '{$_REQUEST['qnt'][$key]}', NOW(), '{$_COOKIE['logado']}')";
                mysql_query($insert);
                $ultimo_id = mysql_insert_id();
                $log->log('2',"Vaga ID $ultimo_id registrada para a unidade $id_unidade",'vagas_por_unidade');
            }
        }
        
//        foreach ($_REQUEST['id_funcao'] as $key => $value) {
//            $array_insert[] = "('{$id_unidade}', '{$_REQUEST['id_funcao'][$key]}', '{$_REQUEST['qnt'][$key]}', NOW(), '{$_COOKIE['logado']}')";
//        }
//        if (count($_REQUEST['id_funcao']) > 0) {
//            $insert = "INSERT INTO vagas_por_unidade (`id_unidade`, `id_funcao`, `qnt`, `criado_em`, `criado_por`) VALUES " . implode(', ', $array_insert);
//            mysql_query($insert);
//        }
        
        if ($insere_unidade) {
            $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';
            $_SESSION['regiao'] = $id_regiao;
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao cadastrar a unidade ' . $nome;
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
            header('Location: index.php');
        }
    }
}

function alteraUnidade($usuario) {
    $log = new Log();
    
    // variaveis recuperadas
    $id_regiao = $_REQUEST['regiao_edita'];
    $id_projeto = $_REQUEST['projeto_edita'];
    $nome = acentoMaiusculo($_REQUEST['nome_unidade']);
    $local_uni = acentoMaiusculo($_REQUEST['local']);
    $tel = $_REQUEST['tel'];
    $tel2 = $_REQUEST['tel_recado'];
    $responsavel = acentoMaiusculo($_REQUEST['nome_responsavel']);
    $cel = $_REQUEST['cel_resp'];
    $email = $_REQUEST['email_responsavel'];
    $endereco = acentoMaiusculo($_REQUEST['endereco']);
    $bairro = acentoMaiusculo($_REQUEST['bairro']);
    $cidade = acentoMaiusculo($_REQUEST['cidade']);
    $cep = $_REQUEST['cep'];
    $id_coordenador = $_REQUEST['id_coordenador'];
    $cod_websaass = $_REQUEST['cod_websaass'];
    $cod_servico1 = $_REQUEST['cod_servico1'];
    $cod_servico2 = $_REQUEST['cod_servico2'];
    $uf = $_REQUEST['uf'];
    $ponto_referencia = acentoMaiusculo($_REQUEST['referencia']);
    $id_unidade = $_REQUEST['unidade'];

    if (($nome == '')) {
        $_SESSION['MESSAGE'] = "Verifique o campo Nome, ele não pode ser vazio";
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
    } else {

        $antigo = $log->getLinha('unidade', $id_unidade);
        $altera_unidade = mysql_query("UPDATE unidade SET unidade = '{$nome}', local = '{$local_uni}', tel = '{$tel}', tel2 = '{$tel2}', responsavel = '{$responsavel}',
                        cel = '{$cel}', email = '{$email}', endereco ='{$endereco}', bairro='{$bairro}',cidade='{$cidade}' ,
                        cep ='{$cep}', uf ='{$uf}', ponto_referencia = '{$ponto_referencia}', cod_websaass = '{$cod_websaass}', cod_servico1 = '{$cod_servico1}', cod_servico2 = '{$cod_servico2}', id_coordenador = '{$id_coordenador}' WHERE id_unidade = '{$id_unidade}' LIMIT 1") or die(mysql_error());
        $novo = $log->getLinha('unidade', $id_unidade);

        $log->log('2', "Unidade ID $id_unidade alterada com sucesso", 'unidade', $antigo, $novo);

        $sql_delete = "DELETE FROM vagas_por_unidade WHERE id_unidade = '{$id_unidade}'";
        mysql_query($sql_delete);
        $log->log('2', "Vagas da unidade $id_unidade deletadas", 'vagas_por_unidade');
        
        if (count($_REQUEST['id_funcao']) > 0) {
            foreach ($_REQUEST['id_funcao'] as $key => $value) {
                $insert = "INSERT INTO vagas_por_unidade (`id_unidade`, `id_funcao`, `qnt`, `criado_em`, `criado_por`) VALUES ('{$id_unidade}', '{$_REQUEST['id_funcao'][$key]}', '{$_REQUEST['qnt'][$key]}', NOW(), '{$_COOKIE['logado']}')";
                mysql_query($insert);
                $ultimo_id = mysql_insert_id();
                $log->log('2',"Vaga ID $ultimo_id registrada para a unidade $id_unidade",'vagas_por_unidade');
            }
        }
//        if(count($_REQUEST['id_funcao']) > 0){
//            $insert = "INSERT INTO vagas_por_unidade (`id_unidade`, `id_funcao`, `qnt`, `criado_em`, `criado_por`) VALUES " . implode(', ', $array_insert);
//            mysql_query($insert);
//        }

        if ($altera_unidade) {
            $_SESSION['MESSAGE'] = 'Informações alteradas com sucesso!';
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';
            $_SESSION['regiao'] = $id_regiao;
            $_SESSION['projeto'] = $id_projeto;
            session_write_close();
            header('Location: index.php');
        } else {

            $_SESSION['MESSAGE'] = 'Erro ao atualizar a unidade';
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
            $_SESSION['projeto'] = $id_projeto;
            session_write_close();
            header('Location: index.php');
        }
    }
}
?>

