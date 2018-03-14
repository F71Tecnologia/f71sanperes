<?php

function getSindicato($id_regiao, $semRegiao = false) {

    if ($semRegiao == true) {
        $sql = "SELECT *
            FROM rhsindicato
            WHERE status = '1'";
    } else {
        $sql = "SELECT *
                FROM rhsindicato
                -- WHERE id_regiao = '{$id_regiao}' AND status = '1'
                WHERE status = '1';";
    }
    $sindicato = mysql_query($sql) or die(mysql_error());
    return $sindicato;
}

function getRhClt($id_sindicato) {
    $sql = "SELECT *
            FROM rh_clt
            WHERE rh_sindicato = '{$id_sindicato}' AND (status < 60 OR status = 200)";
    $clt = mysql_query($sql);
    $tot = mysql_num_rows($clt);
    return $tot;
}

function getSindicatoID($id_sindicato) {
    $sql = "SELECT *, B.regiao AS nome_regiao, B.id_regiao AS reg_id
            FROM rhsindicato AS A
            LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
            WHERE A.status = 1 AND A.id_sindicato = '{$id_sindicato}'";
    $sindicato = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($sindicato);
    return $row;
}

function getSindicatoTotal($regiao, $nome) {
    $sql = "SELECT *
            FROM rhsindicato
            WHERE id_regiao = '{$regiao}'
            AND nome = '{$nome}'
            AND status = 1";
    $sindicato = mysql_query($sql) or die(mysql_error());
    return $sindicato;
}

function getFuncoesSindicato($id_sindicato, $group = null) {
    $_group = null;
    if ($group !== null) {
        $_group = " GROUP BY nome";
    }
    $sql = "SELECT id_curso,nome,valor,salario FROM curso WHERE id_sindicato = {$id_sindicato} $_group";
    $funcoes = mysql_query($sql) or die(mysql_error());
    return $funcoes;
}

function delSindicato($id_sindicato) {
    require_once('LogClass.php');
    $log = new Log();

    $antigo = $log->getLinha('rhsindicato', $id_sindicato);

    $sql = "UPDATE rhsindicato SET status = 0 WHERE id_sindicato = {$id_sindicato}";
    $qry = mysql_query($sql);
    $res = mysql_fetch_assoc($qry);

    $novo = $log->getLinha('rhsindicato', $id_sindicato);

    $usuario = carregaUsuario();

    //dados usuario para cadastro de log
    $acao = "Exclusão do Sindicato: ID" . $id_sindicato;
    $log->log('2', $acao, 'rhsindicato', $antigo, $novo);

    return $res;
}

function cadSindicato() {
    require_once('LogClass.php');
    $log = new Log();

    $usuario = carregaUsuario();
    $regiao = $usuario['id_regiao'];
    $id_user_cad = $usuario['id_funcionario'];
    $data_cad = date('Y-m-d');

    $nome = $_REQUEST['nome'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = soNumero($_REQUEST['cnpj']);
    $tel = soNumero($_REQUEST['tel']);
    $fax = soNumero($_REQUEST['fax']);
    $contato = $_REQUEST['contato'];
    $cel = soNumero($_REQUEST['cel']);
    $email = $_REQUEST['email'];
    $site = $_REQUEST['site'];
    $mes_desconto = $_REQUEST['mes_desconto'];
    $mes_dissidio = $_REQUEST['mes_dissidio'];
    $piso = str_replace(',', '.', str_replace(".", "", $_REQUEST['piso']));
    $multa = $_REQUEST['multa'];
    $ferias = $_REQUEST['ferias'];
    $fracao = $_REQUEST['fracao'];
    $decimo_terceiro = $_REQUEST['decimo_terceiro'];
    $recisao = $_REQUEST['recisao'];
    $pratonal = $_REQUEST['pratonal'];
    $evento = $_REQUEST['evento'];
    $entidade = $_REQUEST['entidade'];

    $insalubridade = $_REQUEST['insalubridade'];
    $periculosidade = $_REQUEST['periculosidade'];
    $adNoturno = $_REQUEST['adNoturno'];
    $hr_noturna = $_REQUEST['hr_noturna'];
    $prcentagem_add_noturno = $_REQUEST['prcentagem_add_noturno'];
    $contibuicao_assistencial = $_REQUEST['contribuicao_assistencial'];
    $auxilio_creche = $_REQUEST['creche'];
    $valor_base = str_replace(',', '.', str_replace('.', '', $_REQUEST['creche_base']));
    $creche_porcentagem = str_replace(',', '.', str_replace('.', '', $_REQUEST['creche_percentual']));
    $creche_idade = $_REQUEST['creche_idade'];
    $tempo_ad_tempo_servico = $_REQUEST['tempo_ad_tempo_servico'];
    $tipo_ad_tempo_servico = $_REQUEST['tipo_ad_tempo_servico'];
    $valor_fixo_ad_tempo_servico = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_fixo_ad_tempo_servico']));
    $porc_ad_tempo_servico = (str_replace(',', '.', str_replace('.', '', $_REQUEST['porc_ad_tempo_servico']))) / 100;

    // beneficios
    $valor_alimentacao = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_alimentacao']));
    $valor_refeicao = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_refeicao']));

    $sindicato = getSindicatoTotal($regiao, $nome);
    $total_sindicato = mysql_num_rows($sindicato);

    $cnpj_val = validarCNPJ($cnpj);


    $clausula_33 = $_REQUEST['clausula_33'];

    if ($total_sindicato != 0) {
        $_SESSION['MESSAGE'] = 'Já Existe um Sindicato ' . $nome . ' cadastrado nessa Região';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $regiao;
    } elseif ($cnpj_val == false) {
        $_SESSION['MESSAGE'] = 'Número de CNPJ inválido!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $regiao;
    } else {

        $insere_sindicato = mysql_query("INSERT INTO rhsindicato
               (id_regiao, id_user_cad, data_cad, nome, endereco ,cnpj ,tel ,fax ,contato , cel ,email ,site ,mes_desconto ,mes_dissidio ,piso ,multa ,ferias ,fracao ,decimo_terceiro ,recisao , pratonal, evento, entidade, insalubridade, periculosidade, adNoturno, hr_noturna , prcentagem_add_noturno, contribuicao_assistencial, creche, creche_base, creche_percentual, creche_idade, valor_alimentacao, valor_refeicao, clausula_33 ) 
        VALUES ('{$regiao}', '{$id_user_cad}', '{$data_cad}', '{$nome}', '{$endereco}', '{$cnpj}', '{$tel}', '{$fax}', '{$contato}', '{$cel}', '{$email}', '{$site}', '{$mes_desconto}', '{$mes_dissidio}', '{$piso}', '{$multa}', '{$ferias}', '{$fracao}', '{$decimo_terceiro}', '{$recisao}', '{$pratonal}', '{$evento}', '{$entidade}','$insalubridade', '$periculosidade', '$adNoturno' , '$hr_noturna' , '$prcentagem_add_noturno','$contibuicao_assistencial','$auxilio_creche','$valor_base','$creche_porcentagem','$creche_idade', '$valor_alimentacao', '$valor_refeicao', '$clausula_33')
                        ") or die(mysql_error());

        //dados usuario para cadastro de log
        $local = "2";
        $acao = "Cadastro do Sindicato: ID " . mysql_insert_id();

        $log->log($local, $acao, 'rhsindicato');

        if ($insere_sindicato) {
            $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';
            session_write_close();
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao cadastrar o sindicato ' . $nome;
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $regiao;
            session_write_close();
            header('Location: index.php');
        }
    }
}

function alteraSindicato() {
    require_once('LogClass.php');
    $log = new Log();

    $usuario = carregaUsuario();
    $regiao = $usuario['id_regiao'];
    $id_user_cad = $usuario['id_funcionario'];

    $nome = $_REQUEST['nome'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = soNumero($_REQUEST['cnpj']);
    $tel = soNumero($_REQUEST['tel']);
    $fax = soNumero($_REQUEST['fax']);
    $contato = $_REQUEST['contato'];
    $cel = soNumero($_REQUEST['cel']);
    $email = $_REQUEST['email'];
    $site = $_REQUEST['site'];
    $mes_desconto = $_REQUEST['mes_desconto'];
    $mes_dissidio = $_REQUEST['mes_dissidio'];
    $piso = str_replace(',', '.', str_replace(".", "", $_REQUEST['piso']));
    $multa = $_REQUEST['multa'];
    $ferias = $_REQUEST['ferias'];
    $fracao = $_REQUEST['fracao'];
    $decimo_terceiro = $_REQUEST['decimo_terceiro'];
    $recisao = $_REQUEST['recisao'];
    $pratonal = $_REQUEST['pratonal'];
    $evento = $_REQUEST['evento'];
    $entidade = $_REQUEST['entidade'];

    $id_sindicato = $_REQUEST['sindicato'];

    $insalubridade = $_REQUEST['insalubridade'];
    $periculosidade = $_REQUEST['periculosidade'];
    $adNoturno = $_REQUEST['adNoturno'];
    $hr_noturna = $_REQUEST['hr_noturna'];
    $prcentagem_add_noturno = $_REQUEST['prcentagem_add_noturno'];
    $contibuicao_assistencial = $_REQUEST['contribuicao_assistencial'];
    $auxilio_creche = $_REQUEST['creche'];
    $valor_base = str_replace(',', '.', str_replace('.', '', $_REQUEST['creche_base']));
    $creche_porcentagem = str_replace(',', '.', str_replace('.', '', $_REQUEST['creche_percentual']));
    $creche_idade = $_REQUEST['creche_idade'];

    // beneficios
    $valor_alimentacao = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_alimentacao']));
    $valor_refeicao = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_refeicao']));

    $clausula_33 = $_REQUEST['clausula_33'];
    $tempo_ad_tempo_servico = $_REQUEST['tempo_ad_tempo_servico'];
    $tipo_ad_tempo_servico = $_REQUEST['tipo_ad_tempo_servico'];
    $valor_fixo_ad_tempo_servico = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_fixo_ad_tempo_servico']));
    $porc_ad_tempo_servico = (str_replace(',', '.', str_replace('.', '', $_REQUEST['porc_ad_tempo_servico']))) / 100;

    $antigo = $log->getLinha('rhsindicato', $id_sindicato);

    $altera_sindicato = mysql_query("UPDATE rhsindicato SET tempo_ad_tempo_servico = '$tempo_ad_tempo_servico', tipo_ad_tempo_servico = '$tipo_ad_tempo_servico', valor_fixo_ad_tempo_servico = '$valor_fixo_ad_tempo_servico', porc_ad_tempo_servico = '$porc_ad_tempo_servico', nome = '$nome' ,endereco ='$endereco' ,cnpj = '$cnpj',tel ='$tel',
            fax = '$fax',contato = '$contato',cel = '$cel',email = '$email',site = '$site',mes_desconto = '$mes_desconto',
            mes_dissidio = '$mes_dissidio',piso = '$piso',multa = '$multa',ferias = '$ferias',fracao = '$fracao',
            decimo_terceiro = '$decimo_terceiro',recisao = '$recisao',pratonal = '$pratonal',evento = '$evento',
            entidade = '$entidade',  insalubridade = '$insalubridade', periculosidade = '$periculosidade', adNoturno = '$adNoturno', hr_noturna = '$hr_noturna', 
            contribuicao_assistencial = '$contibuicao_assistencial', creche = '$auxilio_creche', creche_base = '$valor_base', creche_percentual = '$creche_porcentagem',
            creche_idade = '$creche_idade', prcentagem_add_noturno = '$prcentagem_add_noturno', valor_alimentacao = '$valor_alimentacao', valor_refeicao = '$valor_refeicao', clausula_33  = '$clausula_33'
            WHERE id_sindicato = $id_sindicato") or die(mysql_error());

    $novo = $log->getLinha('rhsindicato', $id_sindicato);

    $log->log(2, "Alteração do Sindicato: ID " . $id_sindicato, 'rhsindicato', $antigo, $novo);

    if ($altera_sindicato) {
        $_SESSION['MESSAGE'] = 'Informações alteradas com sucesso!' . $id_regiao;
        $_SESSION['MESSAGE_COLOR'] = 'message-blue';
        $_SESSION['regiao'] = $regiao;
        header('Location: index.php');
    } else {
        $_SESSION['MESSAGE'] = 'Erro ao atualizar a unidade';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $regiao;
    }
}

?>
