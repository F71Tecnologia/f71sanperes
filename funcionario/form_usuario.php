<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/global.php');

$usuario = carregaUsuario();

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "validaUser") {
    $ok = true;
    $msg = "Nome de usuário disponível";
    $verlogin = $_REQUEST['fieldValue'];
    $idLogin = $_REQUEST['fieldId'];
    $qr_login = mysql_query("SELECT login FROM funcionario WHERE login = '$verlogin'");
    $encontrado = mysql_num_rows($qr_login);
    if($encontrado > 0){
        $ok = false;
        $msg = "Já existe outro usuário com o nome digitado";
    }
    $return = array($idLogin, $ok, utf8_encode($msg));
    
    echo json_encode($return);
    exit();
}

if (isset($_REQUEST['funcionario']) && !empty($_REQUEST['funcionario'])) {
    $id_func = $_REQUEST['funcionario'];
    $qr_func = mysql_query("SELECT A.id_master, B.nome AS nome_master, A.tipo_usuario, A.horario_inicio, A.horario_fim, A.acesso_dias, A.nome, A.id_regiao, C.regiao, A.funcao, DATE_FORMAT(A.data_nasci, '%d/%m/%Y') as data_nasci, A.login, A.senha, A.alt_senha, A.user_cad, A.data_cad, A.nome1
                                FROM funcionario AS A
                                LEFT JOIN master AS B ON (B.id_master = A.id_master)
                                LEFT JOIN regioes AS C ON (C.id_regiao = A.id_regiao)
                                WHERE A.id_funcionario = {$id_func}");
    $funcionario = mysql_fetch_array($qr_func);

    $funcionarioAcesso = montaQueryFirst("funcionario_tipo", "id_funcionario_tipo, funcionario_tipo", "id_funcionario_tipo = {$funcionario['tipo_usuario']}");
    $emails = montaQuery("funcionario_email_assoc", "id_master, email, senha", "id_funcionario = {$id_func}", "id_master ASC");
} else {
    // SELECIONA TODAS AS REGIOES DE ACORDO COM A EMPRESA
    if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaRegioes") {
        $regioes = montaQuery("regioes", "id_regiao, regiao, sigla", "id_master='{$_REQUEST['master']}'");
        $arrayRegioes = array();
        foreach ($regioes as $regiao) {
            $arrayRegioes[$regiao['id_regiao']] = $regiao['id_regiao'] . ' - ' . utf8_encode($regiao['regiao']) . ' - ' . $regiao['sigla'];
        }
        echo json_encode($arrayRegioes);
        exit();
    }
}

//SELECIONA MASTER
$qr_master = mysql_query("SELECT id_master, nome FROM master WHERE status=1 AND email_servidor != '' ORDER BY id_master ASC");

//SELECIONA TODAS AS EMPRESAS PARA SELECT
$empresas = montaQuery("master", "id_master, nome", "status=1");
if (empty($funcionario)) {
    $arrayEmpresas = array(" " => "« Selecione »");
    //SELECT INICIADO REGIOES
    $arrayRegioes = array(" " => "Selecione a Empresa");
} else {
    $arrayEmpresas = array($funcionario['id_master'] => $funcionario['nome_master']);
    //SELECT INICIADO REGIOES 
    $arrayRegioes = array($funcionario['id_regiao'] => $funcionario['id_regiao'] . ' - ' . $funcionario['regiao']);
}
//MONTA MATRIZ DAS EMPRESAS
foreach ($empresas as $empresa) {
    if ($empresa['id_master'] != $funcionario['id_master']) {
        $arrayEmpresas[$empresa['id_master']] = $empresa['nome'];
    }
}

//SELECIONA TODOS OS TIPOS DE ACESSO
$acessos = montaQuery("funcionario_tipo", "id_funcionario_tipo, funcionario_tipo");
if (empty($funcionarioAcesso)) {
    $arrayAcesso = array(" " => "« Selecione »");
} else {
    $arrayAcesso = array($funcionarioAcesso['id_funcionario_tipo'] => $funcionarioAcesso['id_funcionario_tipo'] . " - " . $funcionarioAcesso['funcionario_tipo']);
}
// MONTA MATRIZ DE ACESSO
foreach ($acessos as $acesso) {
    if ($acesso['id_funcionario_tipo'] != $funcionarioAcesso['id_funcionario_tipo']) {
        $arrayAcesso[$acesso['id_funcionario_tipo']] = $acesso['id_funcionario_tipo'] . " - " . $acesso['funcionario_tipo'];
    }
}

$arrayDiasSemana = array(" " => "« Selecione »", "5" =>"De segunda a sexta-feira", "6" => "De segunda-feira a sábado", "7" => "De segunda-feira a domingo");

if(empty($funcionario)){
     $arrayDiasDaSemana = array(" " => "« Selecione »");   
}else{
    switch ($funcionario['acesso_dias']){
        case "5": $arrayDiasDaSemana = array("5" =>"De segunda a sexta-feira");
            break;
        case "6" : $arrayDiasDaSemana = array("6" =>"De segunda-feira a sábado");
            break;
        case "7": $arrayDiasDaSemana = array("7" =>"De segunda-feira a domingo");
    }
}

foreach ($arrayDiasSemana as $key => $value) {
  if($funcionario['acesso_dias']!= $key){
      $arrayDiasDaSemana[$key] = $value;
    
    }  
}



if (isset($_REQUEST['cadastrar'])) {

    $id_master = $_REQUEST['master'];
    $tipo_usuario = $_REQUEST['tipo_usuario'];
    $nome = $_REQUEST['nome'];
    $id_regiao = $_REQUEST['id_regiao'];
    $funcao = $_REQUEST['funcao'];
    $data_nasci = implode("-", array_reverse(explode("/", $_REQUEST['data_nasci'])));
    $login = $_REQUEST['login'];
    $senha = $_REQUEST['senha'];
    $alt_senha = 1;
    $user_cad = $usuario['id_funcionario'];
    $data_cad = date('Y-m-d');
    $nome1 = $_REQUEST['nome1'];
    $horario_ini = $_REQUEST['horario_ini'];
    $horario_fim = $_REQUEST['horario_fim'];
    $acesso_dias = $_REQUEST['acesso_dias'];
    

//    CADASTRANDO USUÁRIO
    $insert_usuario = mysql_query("INSERT INTO funcionario (id_master,
                                    tipo_usuario,
                                    horario_inicio,
                                    horario_fim,
                                    acesso_dias,
                                    nome,
                                    id_regiao,
                                    funcao,
                                    data_nasci,
                                    login,
                                    senha, 
                                    alt_senha, 
                                    user_cad, 
                                    data_cad,
                                    nome1
                                    ) VALUES (
                                    '$id_master',
                                    '$tipo_usuario',
                                    '$horario_ini',
                                    '$horario_fim',
                                    '$acesso_dias',
                                    'TESTE - $nome',
                                    '$id_regiao',
                                    '$funcao',
                                    '$data_nasci',
                                    '$login',
                                    '$senha',
                                    '$alt_senha', 
                                    '$user_cad', 
                                    '$data_cad', 
                                    '$nome1'
                                    )");

    $id_usuario = mysql_insert_id();

//CADASTRANDO E-MAIL DO USUÁRIO
    $array_master_email = $_REQUEST['master_email'];
    $array_email = $_REQUEST['email'];
    $array_senha = $_REQUEST['senha_email'];
    
    $cont = 0;
    if(!empty($array_master_email)){    
        foreach ($array_master_email AS $idMaster) {
            while (empty($array_email[$cont])){
                 $cont++;
            }
            $sql[] = "( '" . $idMaster . "', '" . $id_usuario . "', '" . $array_email[$cont] . "','" . $array_senha[$cont] . "')";
            $sql2[] = "( '" . $idMaster . "', '" . $id_usuario . "' )";
            $sql = implode(',', $sql);
            $sql2 = implode(',', $sql2);
            mysql_query("INSERT INTO funcionario_email_assoc (id_master, id_funcionario, email, senha) VALUES $sql");
            mysql_query("INSERT INTO funcionario_master (id_master, id_funcionario) VALUES $sql2");
            unset($sql);
            unset($sql2);
            $cont++;
            
        }
    }
    
    header("Location: http://www.netsorrindo.com/intranet/funcionario/");
}

if (isset($_REQUEST['editar'])) {

    $id_master = $_REQUEST['master'];
    $tipo_usuario = $_REQUEST['tipo_usuario'];
    $nome = $_REQUEST['nome'];
    $id_regiao = $_REQUEST['id_regiao'];
    $funcao = $_REQUEST['funcao'];
    $data_nasci = implode("-", array_reverse(explode("/", $_REQUEST['data_nasci'])));
    $login = $_REQUEST['login'];
    $user_cad = $usuario['id_funcionario'];
    $data_cad = date('Y-m-d');
    $nome1 = $_REQUEST['nome1'];
    $horario_ini = $_REQUEST['horario_ini'];
    $horario_fim = $_REQUEST['horario_fim'];
    $acesso_dias = $_REQUEST['acesso_dias'];
    
    
    

//EDITANDO USUÁRIO
    $editar_usuario = mysql_query("UPDATE funcionario 
                                    SET id_master = '$id_master',
                                    tipo_usuario = '$tipo_usuario',
                                    horario_inicio = '$horario_ini',
                                    horario_fim = '$horario_fim',
                                    acesso_dias = '$acesso_dias',
                                    nome = 'EDITADO - $nome',
                                    id_regiao = '$id_regiao',
                                    funcao = '$funcao',
                                    data_nasci = '$data_nasci',
                                    login = '$login',
                                    user_cad = '$user_cad', 
                                    data_cad = '$data_cad',
                                    nome1 = '$nome1'
                                    WHERE id_funcionario = '$id_func'
                                    LIMIT 1");

//EDITANDO E-MAIL DO USUÁRIO
    $array_master_email = $_REQUEST['master_email'];
    $array_email = $_REQUEST['email'];
    $array_senha = $_REQUEST['senha_email'];

    $cont = 0;
    if(!empty($array_master_email)){
        foreach ($array_master_email as $idMaster) {
            $qr_consulta_master = mysql_query("SELECT id_master FROM funcionario_email_assoc WHERE id_funcionario = '{$id_func}' AND id_master = '{$idMaster}' ORDER BY id_master ASC");
            $nr_linhas = mysql_num_rows($qr_consulta_master);
            if ($nr_linhas == 1 && !empty($array_email[$cont])){
                mysql_query("UPDATE funcionario_email_assoc SET email = '{$array_email[$cont]}', senha = '{$array_senha[$cont]}' WHERE id_master = '{$idMaster}' AND id_funcionario = '{$id_func}' LIMIT 1");
            }else if(!empty ($array_email)){ 
                mysql_query("INSERT INTO funcionario_email_assoc (id_master, id_funcionario, email, senha) VALUES ({$idMaster}, {$id_func}, {$array_email[$cont]}, {$array_senha[$cont]})");
                mysql_query("INSERT INTO funcionario_master (id_master, id_funcionario) VALUES ({$idMaster}, {$id_func})");
            }
            $cont++;  
        }   
    }
    
    header("Location: http://www.netsorrindo.com/intranet/funcionario/");
    
}
?>
<html>
    <head>
        <title>:: Intranet :: Gestor de Funcionários</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#form1").validationEngine();
                $("#horario_ini").mask('99:99');
                $("#horario_fim").mask('99:99');
                $("#data_nasci").mask('99/99/9999');
                

                $('.master_email').change(function() {
                    var master_id = $(this).val();
                    if (this.checked === true) {
                        $('.master_' + master_id).css('display', 'block');
                        $('.master_' + master_id + ' p:last input:first').focus();
                        $('.master_' + master_id + ' p:last input:first').attr('class', "validate[required,custom[email]]" );
                    } else {
                        $('.master_' + master_id + ' p:last input:first').removeAttr("class");
                        $('.master_' + master_id + ' p:last input:first').val('');
                        $('.master_' + master_id + ' p:last input:last').val('');
                        $('.master_' + master_id).hide();
                    }
                });


                $("#master").ajaxGetJson("../methods.php", {method: "carregaRegioes", default: "3"}, null, "id_regiao");
                    
                    
                

            });

        </script>
        <style>
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
                min-height: 100%;
                border-right: none;
            }
            fieldset{
                margin-top: 10px;
                max-height: 300px;

            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Cadastro de Usuário para Acesso a Intranet</h2>
                    </div>
                </div>
                <fieldset>
                    <legend>Dados do Projeto</legend>
                    <p><label class="first">Empresa:</label><?php echo montaSelect($arrayEmpresas, null, "name='master' id='master' class='validate[required]' style='width: 300px;'"); ?></p>
                    <p><label class="first">Região:</label><?php echo montaSelect($arrayRegioes, null, "id='id_regiao' name='id_regiao' class='validate[required]' style='width: 300px;'") ?></p>
                </fieldset>
                <fieldset>
                    <legend>Dados do Usuário</legend>
                    <div class="colEsq">
                        <p><label class='first'>Nome Completo:</label><input type="text" name="nome" id="nome" value="<?php echo strtoupper($funcionario['nome']); ?>" size="48" class="validate[required]" /></p>
                        <p><label class='first'>Nome para exibição:</label><input type="text" name="nome1" id="nome1" value="<? echo strtoupper($funcionario['nome1']); ?>" size="20" class="validate[required]" /></p>
                        <p><label class='first'>Função:</label><input type="text" name="funcao" id="funcao" value="<? echo strtoupper($funcionario['funcao']); ?>" size="20" class="validate[required]" /></p>
                    </div>
                    <p><label class='first'>Data de Nascimento:</label><input type="text" name="data_nasci" id="data_nasci" value="<?php echo $funcionario['data_nasci']; ?>" size="10" class="validate[required,custom[dateBr]]" /></p>
                </fieldset>
                <fieldset>
                    <legend>Informações de Login</legend>
                    <div class="colEsq">
                        <p><label class='first'>Login:</label><input type="text" name="login" id="login" value="<?php echo $funcionario['login']; ?>" size="20" class="validate[required,ajax[ajaxUser]]"/></p>
                        <?php
                        if (!empty($funcionario['login'])) {
                            $tituloSenha = 'Senha';
                            $senha = $funcionario['senha'];
                        } else {
                            $tituloSenha = 'Senha Padrão';
                            $senha = '123456';
                        }
                        ?>
                        <p><label class='first'><?php echo $tituloSenha; ?></label><input type="text" name="login" id="login" value="<?php echo $senha; ?>" size="10" disabled = "disabled"/></p>
                        <p><label class='first'>Início do horário de Acesso:</label><input type="text" name="horario_ini" id="horario_ini" value="<?php echo $funcionario['horario_inicio']; ?>" size="8" class="validate[required]"/></p>
                        <p><label class='first'>Dias de acesso na semana:</label>
                            <?php echo montaSelect($arrayDiasDaSemana, null, "name='acesso_dias' id='acesso_dias' class='validate[required]'")?>
                       </p>
                        
                    </div>
                    <p><label class='first'>Tipo de Usuário:</label><?php echo montaSelect($arrayAcesso, null, "id='tipo_usuario' name='tipo_usuario' class='validate[required]' style='width: 150px;'") ?></p>
                    <br/>
                    <br/>
                    <p><label class='first'>Fim do horário de Acesso:</label><input type="text" name="horario_fim" id="horario_fim" value="<?php echo $funcionario['horario_fim']; ?>" size="8" class="validate[required]"/></p>
                </fieldset>
                <fieldset>
                    <legend>Informações de email</legend>
                    <?php
                    while ($row_master = mysql_fetch_assoc($qr_master)) {
                        $marcador = '';
                        $display = 'none';
                        foreach ($emails as $email) {
                            if ($row_master['id_master'] == $email['id_master']) {
                                $marcador = 'checked="true"';
                                $display = 'block';
                                $email_ = $email['email'];
                                $senha_ = $email['senha'];
                            }
                        }
                        ?> 
                    <p><label class='first'><?php echo $row_master['nome']; ?> </label><input type="checkbox" name="master_email[]" value="<?php echo $row_master['id_master']; ?>" class="master_email" <?php echo $marcador; ?>/><p>
                        <p><div class="master_<?php echo $row_master['id_master']; ?>" style="display:<?php echo $display; ?>">    
                            <p><label class='first'>E-mail:</label><input type="text" name="email[]" value="<?php echo $email_; ?>"/><label class='first'>Senha:</label><input type="password" name="senha_email[]" class="senha" value="<?php echo $senha_; ?>"/></p>
                        </div><p>                    
                        <?php
                            unset($email_);
                            unset($senha_);
                    }
                    ?>



                </fieldset>
                <p class="controls"> 
                    <?php
                    if (isset($_REQUEST['funcionario']) && !empty($_REQUEST['funcionario'])) {
                        echo '<input type="submit" name="editar" value="Editar" />';
                    } else {
                        echo '<input type="submit" name="cadastrar" value="Cadastrar" />';
                    }
                    ?>
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /> 
                </p>
            </form>
        </div>
    </body>
</html>