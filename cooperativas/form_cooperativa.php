<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/global.php');
include('../classes/cooperativa.php');

$usuario = carregaUsuario();

if (isset($_REQUEST['cooperativa']) && !empty($_REQUEST['cooperativa'])) {
    $cooperativa = cooperativa::getCoop($_REQUEST['cooperativa']);
    $regiao = montaQueryFirst("regioes", "*", "id_regiao={$cooperativa['id_regiao']}");

    $_SESSION['voltarCooperativa']['id_regiao'] = $cooperativa['id_regiao'];
} else {

    $cooperativa['id_regiao'] = $_REQUEST['regiao'];
    $_SESSION['voltarCooperativa']['id_regiao'] = $_REQUEST['regiao'];
}


// array com tipos de de cooperativa para select
$arrTipos = array(
    "1" => 'COOPERATIVA',
    "2" => 'PESSOA JUR&Iacute;DICA'
);

// salvar dados
if (isset($_REQUEST['par']) && !empty($_REQUEST['par']) && $_REQUEST['par'] == 'salvar') {
    $alert = coopInserir();
}
?>
<html>
    <head>
        <title>:: Intranet :: Cooperativa de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../cooperativa.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>


        <script>
            jQuery.fn.brTelMask = function() {

                return this.each(function() {
                    var el = this;
                    $(el).focus(function() {
                        $(el).mask("(99) 9999-9999?9", {placeholder: " "});
                    });

                    $(el).focusout(function() {
                        var phone, element;
                        element = $(el);
                        element.unmask();
                        phone = element.val().replace(/\D/g, '');
                        if (phone.length > 10) {
                            element.mask("(99) 99999-999?9");
                        } else {
                            element.mask("(99) 9999-9999?9");
                        }
                    });
                });
            };

            $(document).ready(function() {
                $(".data").mask("99/99/9999");
                $(".cep").mask('99999-999');
                $(".cnpj").mask('99.999.999/9999-99');
                $(".rg").mask('99.999.999-9');
                $(".cpf").mask('999.999.999-99')
                $(".tel").brTelMask(); // mascara para telefone com 9 digitos

                $("#form1").validationEngine();


                $("#bt-cadastrar").click(function() {
                    $("#form1").submit();
                });

<?php echo (isset($alert)) ? $alert : ''; ?>

            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
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
                        <h2>CADASTRO DE COOPERATIVA DE TRABALHO E PESSOA JURÍDICA</h2>
                    </div>
                </div>

                <fieldset>
                    <legend>Dados da Empresa Contratada</legend>
                    <p>
                        <label class='first' for="tipo">Tipo:</label>
                        <?php echo montaSelect($arrTipos, $cooperativa['tipo'], " id='tipo' name='tipo' class='validate[required]'"); ?>
                    </p>
                    <p>
                        <label class='first' for="nome">Razão Social:</label>
                        <input name="nome" type="text" id="nome" value="<?php echo $cooperativa['nome'] ?>" size="108" class="validate[required]">   
                    </p>
                    <p>
                        <label class='first' for="fantasia">Nome Fantasia:</label>
                        <input name="fantasia" type="text" id="fantasia" size="108" value="<?php echo $cooperativa['fantasia'] ?>" class="validate[required]">
                    </p>
                    <p>
                        <label class='first'>Endereço:</label>
                        <input name="endereco" type="text" id="endereco" size="108" value="<?php echo $cooperativa['endereco'] ?>" class="validate[required]">
                    </p>
                    <div class="colEsq">
                        <p>
                            <label class='first'>Bairro:</label>
                            <input type="text" name="bairro" id="bairro" value="<?php echo $cooperativa['bairro'] ?>" size=40" />
                        </p>
                        <p>
                            <label class='first'>CEP:</label>
                            <input type="text" name="cep" id="cep" class="cep" value="<?php echo $cooperativa['cooperativa_cep'] ?>" size="17" />
                        </p>
                        <p>
                            <label class='first'>CNPJ:</label>
                            <input type="text" name="cnpj" id="cnpj" class="cnpj validate[required]" value="<?php echo $cooperativa['cnpj'] ?>" size="17" />
                        </p>
                        <p>
                            <label class='first'>CNAE:</label>
                            <input type="text" name="cnae" id="cnae" value="<?php echo $cooperativa['cooperativa_cnae'] ?>" size="17" />
                        </p>
                        <p>
                            <label class='first'>Contato:</label>
                            <input type="text" name="contato" id="contato" value="<?php echo $cooperativa['contato'] ?>" size="40" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Cel:</label>
                            <input type="text" name="cel" id="cel" class="tel" value="<?php echo $cooperativa['cel'] ?>" size="17" />
                        </p>
                    </div>
                    <div class="colDir">
                        <p>
                            <label class='first'>Cidade:</label>
                            <input type="text" name="cidade" id="cidade" value="<?php echo $cooperativa['cidade'] ?>" size="25" />
                        </p>
                        <p>
                            <label class='first'>UF:</label>
                            <?php echo selectUF($cooperativa['cooperativa_uf'], 'name="uf" id="uf"') ?>
                        </p>
                        <p>
                            <label class='first'>FPAS:</label>
                            <input type="text" name="fpas" id="fpas"  value="<?php echo $cooperativa['cooperativa_fpas'] ?>" size="17" />
                        </p>
                        <p class="first">&nbsp;</p>
                        <p>
                            <label class='first'>Telefone:</label>
                            <input type="text" name="tel" id="tel" class="tel" value="<?php echo $cooperativa['tel'] ?>" size="17" />
                        </p>
                        <p><label class='first'>Fax:</label>
                            <input type="text" name="fax" id="fax" class="tel" value="<?php echo $cooperativa['fax'] ?>" size="17" />
                        </p>
                    </div>

                    <p class="clear">
                        <label class='first'>E-mail:</label>
                        <input type="text" name="email" id="email" value="<?php echo $cooperativa['email'] ?>" size="108"  />
                    </p>

                    <p>
                        <label class='first'>Site:</label>
                        <input type="text" name="site" id="site" value="<?php echo $cooperativa['site'] ?>" size="108" />
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Dados dos Administradores</legend>

                    <fieldset>
                        <legend>Presidente</legend>
                        <div class="colEsq">
                            <p>
                                <label class="first" for="presidente">Nome:</label>
                                <input type="text" name="presidente" id="presidente" value="<?php echo $cooperativa['presidente'] ?>" size="40" class="validate[required]">
                            </p>
                            <p>
                                <label class="first" for="rgp">RG:</label>
                                <input type="text" name="rgp" id="rgp" class="rg validate[required]" value="<?php echo $cooperativa['rgp'] ?>" size="17">
                            </p>
                        </div>
                        <div class="colDir">
                            <p>
                                <label class="first" for="matriculap">Matricula:</label>
                                <input type="text" name="matriculap" id="matriculap" value="<?php echo $cooperativa['matriculap'] ?>" size="17">
                            </p>
                            <p>
                                <label class="first" for="cpfp">CPF:</label>
                                <input type="text" name="cpfp" id="cpfp" class="cpf validate[required]" value="<?php echo $cooperativa['cpfp'] ?>" size="17">
                            </p>
                        </div>
                        <p class="clear">
                            <label class="first" for="enderecop">Endereço:</label>
                            <input type="text" name="enderecop" id="enderecop" value="<?php echo $cooperativa['enderecop'] ?>" size="108">
                        </p>
                    </fieldset>

                    <fieldset>
                        <legend>Diretor</legend>
                        <div class="colEsq">
                            <p>
                                <label class="first" for="Diretor">Nome:</label>
                                <input type="text" name="diretor" id="diretor" value="<?php echo $cooperativa['diretor'] ?>" size="40" class="validate[required]">
                            </p>
                            <p>
                                <label class="first" for="rgd">RG:</label>
                                <input type="text" name="rgd" id="rgd" class="rg validate[required]" value="<?php echo $cooperativa['rgd'] ?>" size="17">
                            </p>
                        </div>
                        <div class="colDir">
                            <p>
                                <label class="first" for="matriculad">Matricula:</label>
                                <input type="text" name="matriculad" id="matriculap" value="<?php echo $cooperativa['matriculad'] ?>" size="17">
                            </p>
                            <p>
                                <label class="first" for="cpfd">CPF:</label>
                                <input type="text" name="cpfd" id="cpfd" class="cpf validate[required]" value="<?php echo $cooperativa['cpfd'] ?>" size="17">
                            </p>
                        </div>
                        <p class="clear">
                            <label class="first" for="enderecod">Endereço:</label>
                            <input type="text" name="enderecod" id="enderecod" value="<?php echo $cooperativa['enderecod'] ?>" size="108">
                        </p>
                    </fieldset>

                    <p>
                        <label class="first" for="entidade">Entidade Sindical Vinculada:</label>
                        <input type="text" name="entidade" id="entidade" value="<?php echo $cooperativa['entidade'] ?>" size="108">
                    </p>

                    <div class="colEsq">
                        <p>
                            <label class="first" for="fundo">Fundo reserva:</label>
                            <input type="text" name="fundo" id="fundo" value="<?php echo $cooperativa['fundo'] ?>">
                        </p>
                        <p>
                            <label class="first" for="taxa">Taxa Administrativa:</label>
                            <input type="text" name="taxa" id="taxa" value="<?php echo $cooperativa['taxa'] ?>">
                        </p>
                    </div>

                    <div class="colDir">
                        <p>
                            <label class="first" for="parcela">Quantidade de Parcelas:</label>
                            <input type="text" name="parcela" id="parcela" value="<?php echo $cooperativa['parcelas'] ?>">
                        </p>
                        <p>
                            <label class="first" for="bonificacao">Bonificação:</label>
                            <input type="text" name="bonificacao" id="bonificacao" value="<?php echo $cooperativa['bonificacao'] ?>">
                        </p>
                    </div>

                    <p class="clear">
                        <label for="foto" class="first">Logo:</label>
                        <input name="foto" type="file" id="foto" size="35" >
                        <br>
                        <?php if (isset($cooperativa['foto']) && !empty($cooperativa['foto'])) { ?>
                            <img src="<?php echo "logos/coop_" . $cooperativa['id_coop'] . $cooperativa['foto']; ?>" title="Logo da Cooperativa" alt="Logo da Cooperativa" style="min-width:100px; min-height: 100px;">
                        <?php } ?>
                    </p>
                    <p>
                        <label for="cursos" class="first">Realizador do Curso de Cooperativismo:</label>
                        <input name="cursos" type="text" id="cursos" size="108"  value="<?php echo $cooperativa['cursos'] ?>">
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Dados Bancários</legend>
                    <p>
                        <label class="first" class="banco">Banco:</label>
                        <?php
                        $listaBancos = montaQuery('bancos', '*', "id_regiao ='$regiao'", NULL, NULL, NULL);
                        while ($row = mysql_fetch_array($listaBancos)) {
                            $arrayBancos[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
                        }
                        echo montaSelect($arrayBancos, $cooperativa['id_banco'], "name='banco' class='campotexto' id='banco' class='validate[required]'");
                        ?>
                    </p>
                </fieldset>

                <input type="hidden" name="id_coop" id="id_coop" value="<?php echo $cooperativa['id_coop'] ?>">
                <input type="hidden" name="id_regiao" id="id_regiao" value="<?php echo $cooperativa['id_regiao'] ?>">
                <input type="hidden" name="par" id="par" value="salvar">

                <p class="controls">
                    <input type="submit" value="Salvar" id="bt-cadastrar" name="bt-cadastrar">
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'cooperativa_nova.php';" />
                </p>
            </form>

            <div id="resp"></div>
        </div>
    </body>
</html>

<?php

function coopInserir() {
    $alert = '';
    $dados = array(
        'id_coop' => $_REQUEST['id_coop'],
        'id_regiao' => $_REQUEST['id_regiao'],
        'tipo' => $_REQUEST['tipo'],
        'nome' => $_REQUEST['nome'],
        'fantasia' => $_REQUEST['fantasia'],
        'endereco' => $_REQUEST['endereco'],
        'bairro' => $_REQUEST['bairro'],
        'cooperativa_cep' => $_REQUEST['cep'],
        'cnpj' => $_REQUEST['cnpj'],
        'cooperativa_cnae' => $_REQUEST['cnae'],
        'contato' => $_REQUEST['contato'],
        'cel' => $_REQUEST['cel'],
        'cidade' => $_REQUEST['cidade'],
        'cooperativa_uf' => $_REQUEST['uf'],
        'cooperativa_fpas' => $_REQUEST['fpas'],
        'tel' => $_REQUEST['tel'],
        'fax' => $_REQUEST['fax'],
        'email' => $_REQUEST['email'],
        'site' => $_REQUEST['site'],
        'presidente' => $_REQUEST['presidente'],
        'rgp' => $_REQUEST['rgp'],
        'enderecop' => $_REQUEST['enderecop'],
        'matriculap' => $_REQUEST['matriculap'],
        'cpfp' => $_REQUEST['cpfp'],
        'diretor' => $_REQUEST['diretor'],
        'rgd' => $_REQUEST['rgd'],
        'cpfd' => $_REQUEST['cpfd'],
        'enderecod' => $_REQUEST['enderecod'],
        'matriculad' => $_REQUEST['matriculad'],
        'entidade' => $_REQUEST['entidade'],
        'fundo' => $_REQUEST['fundo'],
        'taxa' => $_REQUEST['taxa'],
        'cursos' => $_REQUEST['cursos'],
        'parcelas' => $_REQUEST['parcelas'],
        'id_banco' => $_REQUEST['banco']
    );

    $arquivo = isset($_FILES['foto']) ? $_FILES['foto'] : FALSE;

//AQUI TEM FOTO
    if ($arquivo['error'] == 0) {

//aki a imagem nao corresponde com as extenções especificadas
        if ($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/pjpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/jpe") {
            $alert .= "alert('ATENÇÃO: Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png. \\n Tipo do arquivo enviado: $arquivo[type]');\n";

//aqui o arquivo é realente de imagem e vai ser carregado para o servidor
        } else {

            $arr_basename = explode(".", $arquivo['name']);
            $file_type = $arr_basename[1];

            if ($arquivo['type'] == "image/gif") {
                $dados['foto'] = ".gif";
            } elseif ($arquivo['type'] == "image/jpe" or $arquivo['type'] == "image/pjpeg") {
                $dados['foto'] = ".jpg";
            } elseif ($arquivo['type'] == "image/x-png") {
                $dados['foto'] = ".png";
            }
        }

// FAZENDO O INSERT DO CADASTRO QUE TENHA FOTO
        $id_cooperativa = cooperativa::save($dados);

// Resolvendo o nome e para onde o arquivo será movido
        $diretorio = "logos/";

        $nome_tmp = "coop_" . $id_cooperativa . $dados['foto'];
        $nome_arquivo = "$diretorio$nome_tmp";

        move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");

        if ($id_cooperativa) { // testa se os dados foram salvos 
            $alert .= "alert('ATENÇÃO: dados salvos com sucesso!');\n";
        } else {
            $alert .= "alert('ATENÇÃO: não foi possível salvar as informações. Tente novamente mais tarde.');\n";
        }
    } else {
        // AKI TERMINA A FERIFICAÇÃO SE O ARQUIVO FOI SELECIONADO ANTERIRMENTE
        //FAZENDO O INSERT DO CADASTRO SEM FOTO
        $alert .= "alert('ATENÇÃO: dados salvos com sucesso!')";
        $id_cooperativa = cooperativa::save($dados);
        if ($id_cooperativa) { // testa se os dados foram salvos 
        } else {
            $alert .= "alert('ATENÇÃO: não foi possível salvar as informações. Tente novamente mais tarde.');\n";
        }
    }

    return $alert;
}
