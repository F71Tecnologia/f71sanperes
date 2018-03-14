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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$caminho = $_REQUEST['caminho'];

if($caminho == 0){
    $acao = "Cadastrar";
 } else if($caminho == 1) {
    $acao = "Editar"; 
 }

$breadcrumb_caminhos[0] = array("Gest�o de RH" => "../", "Cooperativas" => "cooperativa_nova2.php");
$breadcrumb_caminhos[1] = array("Gest�o de RH" => "../", "Cooperativas" => "cooperativa_nova2.php");
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"$acao Cooperativa");
$breadcrumb_pages = $breadcrumb_caminhos[$caminho];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Detalhes Cooperativa</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Detalhes Cooperativa</small></h2></div>
                    <input type="hidden" name="home" id="home">
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="row">
                <form class="form-horizontal" method="post" id="form1">
                    <div class="col-lg-12">
                        <fieldset>
                            <legend>Dados da Empresa Contratada</legend>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="tipo">Tipo:</label>
                                    <div class="col-lg-10"><?=montaSelect($arrTipos, $cooperativa['tipo'], " id='tipo' name='tipo' class='form-control validate[required]'")?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label no-padding" for="nome">Raz�o Social:</label>
                                    <div class="col-lg-10"><input name="nome" type="text" id="nome" value="<?=$cooperativa['nome']?>" class="form-control validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label no-padding" for="fantasia">Nome Fantasia:</label>
                                    <div class="col-lg-10"><input name="fantasia" type="text" id="fantasia" value="<?=$cooperativa['fantasia']?>" class="form-control validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Endere�o:</label>
                                    <div class="col-lg-10"><input name="endereco" type="text" id="endereco" value="<?=$cooperativa['endereco']?>" class="form-control validate[required]"></div>
                                </div>
                            </div><!-- /.col-lg-12 -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Bairro:</label>
                                    <div class="col-lg-8"><input type="text" name="bairro" id="bairro" value="<?=$cooperativa['bairro']?>" class="form-control" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">CEP:</label>
                                    <div class="col-lg-8"><input type="text" name="cep" id="cep" class="form-control cep" value="<?=$cooperativa['cooperativa_cep']?>" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">CNPJ:</label>
                                    <div class="col-lg-8"><input type="text" name="cnpj" id="cnpj" class="form-control cnpj validate[required]" value="<?=$cooperativa['cnpj']?>" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">CNAE:</label>
                                    <div class="col-lg-8"><input type="text" name="cnae" id="cnae" value="<?=$cooperativa['cooperativa_cnae']?>" class="form-control" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Contato:</label>
                                    <div class="col-lg-8"><input type="text" name="contato" id="contato" value="<?=$cooperativa['contato']?>" class="form-control validate[required]" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Cel:</label>
                                    <div class="col-lg-8"><input type="text" name="cel" id="cel" class="form-control tel" value="<?=$cooperativa['cel']?>" /></div>
                                </div>
                            </div><!-- /.col-lg-6 -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Cidade:</label>
                                    <div class="col-lg-8"><input type="text" name="cidade" id="cidade" value="<?=$cooperativa['cidade']?>" class="form-control" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">UF:</label>
                                    <div class="col-lg-8"><?=selectUF($cooperativa['cooperativa_uf'], 'name="uf" id="uf" class="form-control"')?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">FPAS:</label>
                                    <div class="col-lg-8"><input type="text" name="fpas" id="fpas"  value="<?=$cooperativa['cooperativa_fpas']?>" class="form-control" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Telefone:</label>
                                    <div class="col-lg-8"><input type="text" name="tel" id="tel" class="form-control tel" value="<?=$cooperativa['tel']?>" /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Fax:</label>
                                    <div class="col-lg-8"><input type="text" name="fax" id="fax" class="form-control tel" value="<?=$cooperativa['fax']?>" /></div>
                                </div>
                            </div><!-- /.col-lg-6 -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">E-mail:</label>
                                    <div class="col-lg-10"><input type="text" name="email" id="email" value="<?=$cooperativa['email']?>" class="form-control"  /></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Site:</label>
                                    <div class="col-lg-10"><input type="text" name="site" id="site" value="<?=$cooperativa['site']?>" class="form-control" /></div>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="col-lg-12">
                            <legend>Dados dos Administradores</legend>

                            <fieldset class="col-lg-offset-1 col-lg-5">
                                <legend>Presidente</legend>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="presidente">Nome:</label>
                                    <div class="col-lg-10"><input type="text" name="presidente" id="presidente" value="<?=$cooperativa['presidente']?>" class="form-control validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="rgp">RG:</label>
                                    <div class="col-lg-10"><input type="text" name="rgp" id="rgp" class="form-control rg validate[required]" value="<?=$cooperativa['rgp']?>"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="matriculap">Matricula:</label>
                                    <div class="col-lg-10"><input type="text" name="matriculap" id="matriculap" value="<?=$cooperativa['matriculap']?>" class="form-control"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="cpfp">CPF:</label>
                                    <div class="col-lg-10"><input type="text" name="cpfp" id="cpfp" class="form-control cpf validate[required]" value="<?=$cooperativa['cpfp']?>"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="enderecop">Endere�o:</label>
                                    <div class="col-lg-10"><input type="text" name="enderecop" id="enderecop" value="<?=$cooperativa['enderecop']?>" class="form-control"></div>
                                </div>
                            </fieldset>

                            <fieldset class="col-lg-offset-1 col-lg-5">
                                <legend>Diretor</legend>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="Diretor">Nome:</label>
                                    <div class="col-lg-10"><input type="text" name="diretor" id="diretor" value="<?=$cooperativa['diretor']?>" class="form-control validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="rgd">RG:</label>
                                    <div class="col-lg-10"><input type="text" name="rgd" id="rgd" class="form-control rg validate[required]" value="<?=$cooperativa['rgd']?>"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="matriculad">Matricula:</label>
                                    <div class="col-lg-10"><input type="text" name="matriculad" id="matriculap" value="<?=$cooperativa['matriculad']?>" class="form-control"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="cpfd">CPF:</label>
                                    <div class="col-lg-10"><input type="text" name="cpfd" id="cpfd" class="form-control cpf validate[required]" value="<?=$cooperativa['cpfd']?>"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label" for="enderecod">Endere�o:</label>
                                    <div class="col-lg-10"><input type="text" name="enderecod" id="enderecod" value="<?=$cooperativa['enderecod']?>" class="form-control"></div>
                                </div>
                            </fieldset>
                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="entidade">Entidade Sindical Vinculada:</label>
                                <div class="col-lg-10"><input type="text" name="entidade" id="entidade" value="<?=$cooperativa['entidade']?>" class="form-control"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="fundo">Fundo reserva:</label>
                                <div class="col-lg-4"><input type="text" name="fundo" id="fundo" value="<?=$cooperativa['fundo']?>" class="form-control"></div>
                                <label class="col-lg-2 control-label" for="parcela">Qtd. Parcelas:</label>
                                <div class="col-lg-4"><input type="text" name="parcela" id="parcela" value="<?=$cooperativa['parcelas']?>" class="form-control"></div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="taxa">Taxa Administrativa:</label>
                                <div class="col-lg-4"><input type="text" name="taxa" id="taxa" value="<?=$cooperativa['taxa']?>" class="form-control"></div>
                                <label class="col-lg-2 control-label" for="bonificacao">Bonifica��o:</label>
                                <div class="col-lg-4"><input type="text" name="bonificacao" id="bonificacao" value="<?=$cooperativa['bonificacao']?>" class="form-control"></div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="foto">Logo:</label>
                                <div class="col-lg-10"><input name="foto" type="file" id="foto" class="form-control"></div>
                            </div>
                            <div class="form-group">
                                <?php if (isset($cooperativa['foto']) && !empty($cooperativa['foto'])) { ?>
                                    <div class="col-lg-offset-2">
                                        <img data-src="holder.js/140x140" class="img-thumbnail" src="<?="logos/coop_".$cooperativa['id_coop'].$cooperativa['foto']?>" style="width: 140px; height: 140px;">
                                        <!--img src="<?="logos/coop_" . $cooperativa['id_coop'] . $cooperativa['foto']; ?>" title="Logo da Cooperativa" alt="Logo da Cooperativa" style="min-width:100px; min-height: 100px;"-->
                                    </div>    
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label" for="cursos">Realizador do Curso de Cooperativismo:</label>
                                <div class="col-lg-10"><input name="cursos" type="text" id="cursos" class="form-control"  value="<?=$cooperativa['cursos']?>"></div>
                            </div>
                        </fieldset>

                        <fieldset class="col-lg-12">
                            <legend>Dados Banc�rios</legend>
                            <div class="form-group">
                                <label class="col-lg-2 control-label" class="banco">Banco:</label>
                                <div class="col-lg-10">
                                    <?php
                                    $listaBancos = montaQuery('bancos', '*', "id_regiao ='$regiao'", NULL, NULL, NULL);
                                    while ($row = mysql_fetch_array($listaBancos)) {
                                        $arrayBancos[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
                                    }
                                    echo montaSelect($arrayBancos, $cooperativa['id_banco'], "name='banco' id='banco' class='form-control validate[required]'");
                                    ?>
                                </div>
                            </div>
                        </fieldset>

                        <input type="hidden" name="id_coop" id="id_coop" value="<?=$cooperativa['id_coop']?>">
                        <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$cooperativa['id_regiao']?>">
                        <input type="hidden" name="par" id="par" value="salvar">
                        <input type="hidden" name="home" id="home" value="">

                        <div class="col-lg-offset-11 col-lg-1">
                            <input type="submit" class="btn btn-primary" value="Salvar" id="bt-cadastrar" name="bt-cadastrar">
                            <!--input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'cooperativa_nova.php';" /-->
                        </div>
                    </div>
                </form>
            </div>
            <?php include_once ('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
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

//aki a imagem nao corresponde com as exten��es especificadas
        if ($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/pjpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/jpe") {
            $alert .= "alert('ATEN��O: Tipo de arquivo n�o permitido, os �nicos padr�es permitidos s�o .gif, .jpg , .jpeg ou .png. \\n Tipo do arquivo enviado: $arquivo[type]');\n";

//aqui o arquivo � realente de imagem e vai ser carregado para o servidor
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

// Resolvendo o nome e para onde o arquivo ser� movido
        $diretorio = "logos/";

        $nome_tmp = "coop_" . $id_cooperativa . $dados['foto'];
        $nome_arquivo = "$diretorio$nome_tmp";

        move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");

        if ($id_cooperativa) { // testa se os dados foram salvos 
            $alert .= "alert('ATEN��O: dados salvos com sucesso!');\n";
        } else {
            $alert .= "alert('ATEN��O: n�o foi poss�vel salvar as informa��es. Tente novamente mais tarde.');\n";
        }
    } else {
        // AKI TERMINA A FERIFICA��O SE O ARQUIVO FOI SELECIONADO ANTERIRMENTE
        //FAZENDO O INSERT DO CADASTRO SEM FOTO
        $alert .= "alert('ATEN��O: dados salvos com sucesso!')";
        $id_cooperativa = cooperativa::save($dados);
        if ($id_cooperativa) { // testa se os dados foram salvos 
        } else {
            $alert .= "alert('ATEN��O: n�o foi poss�vel salvar as informa��es. Tente novamente mais tarde.');\n";
        }
    }

    return $alert;
} ?>
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

                <?=(isset($alert)) ? $alert : ''; ?>
            });
        </script>