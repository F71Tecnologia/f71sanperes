<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FeriadosClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objFeriado = new FeriadosClass();

$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$listaRegioes = getRegioes();


if ($_REQUEST['feriado'] != '') {
    $feriado = $_REQUEST['feriado'];
} elseif ($_SESSION['feriado'] != '') {
    $feriado = $_SESSION['feriado'];
}

$row = $objFeriado->getFeriadoID($feriado);
$projetos = $objFeriado->getFeriadosProjetosAssoc($feriado);

//insert
if (isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar") {
    $dados['usuario'] = $usuario;
    $dados['id_regiao'] = $id_regiao;
    $dados['id_usuario'] = $dados['usuario']['id_funcionario'];
    $dados['data_cad'] = date('Y-m-d');
    $dados['nome_feriado'] = acentoMaiusculo($_REQUEST['nome_feriado']);
    $dados['data_feriado'] = ($_REQUEST['data_feriado'] != '') ? converteData($_REQUEST['data_feriado']) : '';
    $dados['tipo'] = $_REQUEST['tipo_feriado'];
    $dados['movel'] = $_REQUEST['movel'];
    $dados['id_projeto'] = $_REQUEST['projeto_feriado'];
    $dados['uf'] = $_REQUEST['uf'];
    $dados['municipio'] = $_REQUEST['municipio'];

//    print_array($dados['id_projeto']);
//    exit();
    $objFeriado->cadFeriado($dados);
    $regiao_selecionada = $_SESSION['regiao'];
} else {
    $regiao_selecionada = $_REQUEST['hide_regiao'];
}

//update
if (isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar") {
    $dados['usuario'] = $usuario;
    $dados['id_regiao'] = $id_regiao;
    $dados['id_usuario'] = $usuario['id_funcionario'];
    $dados['nome_feriado'] = acentoMaiusculo($_REQUEST['nome_feriado']);
    $dados['data_feriado'] = ($_REQUEST['data_feriado'] != '') ? converteData($_REQUEST['data_feriado']) : '';
    $dados['tipo'] = $_REQUEST['tipo_feriado'];
    $dados['movel'] = $_REQUEST['movel'];
    $dados['id_feriado'] = $_REQUEST['feriado'];
    $dados['id_projeto'] = $_REQUEST['projeto_feriado'];
    $dados['uf'] = $_REQUEST['uf'];
    $dados['municipio'] = $_REQUEST['municipio'];
    $objFeriado->alteraFeriado($dados);
}

$projeto_selecionado = $_REQUEST['hide_projeto'];
$regiao_edita = $row['id_regiao'];
$projeto_edita = $row['id_projeto'];

//trata insert/update
if ($feriado == '') {
    $regiao = $regiao_selecionada;
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $projeto = montaSelect(getProjetos($regiao), null, "id='projeto' name='projeto'");
} else {
    $regiao = $regiao_edita;
    $acao = 'Edição';
    $botao = 'Atualizar';
    $projeto = $row['id_projeto'] . " - " . $row['nome_projeto'];


}

//trazer todos os ufs
$ufOpt = ['Selecione'];
$uf = montaQuery("uf","uf_sigla,uf_sigla as nome");
foreach($uf as $val){
    $ufOpt[$val["uf_sigla"]] = $val["nome"];
}
//dados para voltar no index com select preenchido
$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

if ($regiao_selecionada == '') {
    $_SESSION['regiao_select'];
    $_SESSION['projeto_select'];
    session_write_close();
} else {
    $_SESSION['regiao_select'] = $regiao_selecionada;
    $_SESSION['projeto_select'] = $projeto_selecionado;
    session_write_close();
}

$listaProjetos = getProjetos($regiao_selecionada);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "$acao de Feriado");
$breadcrumb_pages = array("Feriados" => "index.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>:: Intranet :: <?= $acao ?> de Feriado</title>
    <link href="../../favicon.png" rel="shortcut icon" />

    <!-- Bootstrap -->
    <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
    <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
    <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    <link href="../../css/progress.css" rel="stylesheet" type="text/css">
    <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
</head>
<body>
<?php include("../../template/navbar_default.php"); ?>
<div class="container">
    <form action="#" method="post" name="form1" id="form1" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - <?= $acao ?> de Feriado</small></h2></div>
            </div>
        </div>
        <input type="hidden" id="feriado" name="feriado" value="<?php echo $row['id_feriado']; ?>" />
        <!--resposta de algum metodo realizado-->
        <?php if (isset($_SESSION['MESSAGE'])) { ?>
            <div class="alert <?= $_SESSION['MESSAGE_COLOR'] ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?php
                switch ($_SESSION['MESSAGE_COLOR']) {
                    case 'alert-warning':
                    case 'alert-danger': echo '<i class="fa fa-warning"></i> ';
                        break;
                    case 'alert-success':
                    case 'alert-info':
                    default: echo '<i class="fa fa-info-circle"></i> ';
                        break;
                }
                echo $_SESSION['MESSAGE'];
                session_destroy();
                ?>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label for="nome_feriado" class="col-lg-2 control-label">Nome do Feriado:</label>
                    <div class="col-lg-9">
                        <input type="text" name="nome_feriado" id="nome_feriado" size="108" class="form-control validate[required]" value="<?php echo $row['nome']; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="data_feriado" class="col-lg-2 control-label">Data:</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input type="text" name="data_feriado" id="data_feriado" size="30" value="<?php echo ($row['data'] != '') ? date('d/m/Y', strtotime($row['data'])) : ""; ?>" class="form-control data validate[required,custom[dateBr]]" />
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    <label class="col-lg-1 control-label">Tipo:</label>
                    <div class="col-lg-1">
                        <div class="radio">
                            <label for="tipo_feriado1">
                                <input type="radio" name="tipo_feriado" id="tipo_feriado1" value="Nacional" <?= ($row['tipo'] == 'Nacional') ? "checked" : '' ?> /> Nacional
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="radio">
                            <label for="tipo_feriado2">
                                <input type="radio" name="tipo_feriado" id="tipo_feriado2" value="Estadual" <?= ($row['tipo'] == 'Estadual') ? "checked" : "" ?> /> Estadual
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="radio">
                            <label for="tipo_feriado3">
                                <input type="radio" name="tipo_feriado" id="tipo_feriado3" value="Municipal" <?= ($row['tipo'] == 'Municipal') ? "checked" : "" ?> /> Municipal
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="movel" class="col-lg-2 control-label">Festa móvel:</label>
                    <div class="col-lg-9">
                        <div class="checkbox">
                            <label for="movel">
                                <input type="checkbox" name="movel" id="movel" value="1" <?php echo ($row['movel'] == '1') ? 'checked' : ''; ?> />
                                Marque se o feriádo for móvel
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="div-estado">
                    <label class="col-lg-2 control-label">Estado:</label>
                    <div class="col-lg-9">
                        <?= montaSelect($ufOpt, $row['uf_sigla'], array('id' => 'uf', 'name' => 'uf', 'class' => 'form-control')) ?>
                    </div>
                </div>
                <div class="form-group" id="div-municipio">
                    <label class="col-lg-2 control-label">Municipio:</label>
                    <div class="col-lg-9">
                        <?= montaSelect(null, null, array('id' => 'municipio', 'name' => 'municipio', 'class' => 'form-control')) ?>
                    </div>
                </div>
<!--                <div class="form-group">-->
<!--                    <label class="col-lg-2 control-label">Região:</label>-->
<!--                    <div class="col-lg-9">-->
<!--                        <?= montaSelect($listaRegioes, $row['id_regiao'], array('id' => 'regiao', 'name' => 'regiao', 'class' => 'form-control')) ?>
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="form-group projetos_box">-->
<!--                    <label class="col-lg-2 control-label">Projeto:</label>-->
<!--                    <div class="col-lg-9">-->
<!--                        <div class="input-group">-->
<!--                            <?//= montaSelect(null, null, array('class' => 'form-control', 'id' => 'projeto_feriado', 'name' => 'projeto_feriado[]')) ?>
<!--                            <span class="input-group-addon success pointer add_projeto"><i class="fa fa-plus"></i></span>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
                <?php foreach ($projetos as $id_projeto => $nome_projeto) { ?>
                    <div class="form-group lista_projetosBox">
                        <div class="col-lg-offset-2 col-lg-9">
                            <div class="input-group">
                                <?= montaSelect([$id_projeto => $nome_projeto],null,'readonly class="form-control readonlySelect" name="projeto_feriado[]"');?>
                                <span class="input-group-addon danger pointer rem_projeto"><i class="fa fa-minus"></i></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div><!-- /.panel-body -->
            <div class="panel-footer">
                <div class="col-xs-6 text-left">
                    <a class="btn btn-default" id="voltar"  href="index.php" ><i class="fa fa-reply"></i> Voltar</a>
                </div>
                <div class="col-xs-6 text-right">
                    <input type="submit" class="btn btn-primary" name="<?= strtolower($botao) ?>" id="<?= strtolower($botao) ?>" value="<?= $botao ?>" />
                </div>
                <div class="clear"></div>
            </div>
        </div><!-- /.painel-default -->
    </form>
    <?php include_once '../../template/footer.php'; ?>
</div><!-- /.container -->
<script src="../../js/jquery-1.10.2.min.js"></script>
<script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../../js/jquery.validationEngine-2.6.js"></script>
<script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
<script src="../../js/jquery.maskedinput.min.js" type="text/javascript" ></script>
<script src="../../js/global.js" type="text/javascript"></script>
<script src="../../resources/js/bootstrap.min.js"></script>
<script src="../../resources/js/main.js" type="text/javascript"></script>

<script src="../../resources/js/rh/feriados/form_feriado_novo.js"></script>
<script>

    $(function () {

        $('#div-estado, #div-municipio').hide();

        if($("#tipo_feriado2").is(':checked')){
            $('#div-estado').show();
        }

        if($("#tipo_feriado3").is(':checked')){
            $('#div-estado').show();
            $('#div-municipio').show();
        }

        $("#tipo_feriado1").click(function(){
            $('#div-estado').hide();
            $('#div-municipio').hide();
            $('#uf, #municipio').prop('disabled', true);
        });

        $("#tipo_feriado2").click(function(){
            $('#div-estado').show();
            $('#div-municipio').hide();
            $('#uf').prop('disabled', false);
            $('#municipio').prop('disabled', true);
        });

        $("#tipo_feriado3").click(function(){
            $('#div-estado').show();
            $('#div-municipio').show();
            $('#uf').prop('disabled', false);
            $('#municipio').prop('disabled', false);
        });

      //  $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto_feriado");
        $('#uf').ajaxGetJson("../../methods.php", {method: "carregaMunicipio", feriado:"feriado"}, null, "municipio");

        $('body').on('click', '.add_projeto', function () {

            var id;
            var html;

            id = $('#projeto_feriado').val();
            if (id > 0) {
                $.post('../../methods.php', {method: "carregaProjetoById", id_projeto: id}, function (data) {

                    html = '<div class="form-group lista_projetosBox">' +
                        '<div class="col-lg-offset-2 col-lg-9">' +
                        '<div class="input-group">' +
                        '<select readonly class="form-control readonlySelect" name="projeto_feriado[]">' +
                        data +
                        '</select>' +
                        '<span class="input-group-addon danger pointer rem_projeto"><i class="fa fa-minus"></i></span>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    $('.projetos_box').after(html);
                    $('#projeto_feriado').val(-1);

                });
            }
        });

        $('body').on('click', '.rem_projeto', function () {

            $(this).parent().parent().parent().remove();

        });

    });

</script>

</body>
</html>