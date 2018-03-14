<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/MovimentoClass.php');
include("../../classes/LogClass.php");
include('../../funcoes.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//array de programadores
$func_f71 = array('255', '258', '256', '259', '260', '158', '257', '179');

$movimento = new Movimentos();
$global = new GlobalClass();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Cadastro de Movimentos");
$breadcrumb_pages = array("Gestão de Movimentos" => "gestao_movimentos.php");

$id_mov = $_REQUEST['id'];

$row = $movimento->getMovimentoId($id_mov);
$arrESocial = $movimento->verCodESocial();
$arrSindicatos = $movimento->carregaSindicatosAgrupado();

$selectESocial[-1] = "« Selecione »";
foreach ($arrESocial as $value) {

    $selectESocial[$value['codigo']] = $value['nome'];
}

if (isset($_REQUEST['sind'])) {
    $arrSindicatos = $movimento->carregaSindicatosAgrupado();
    $return = '<div class="row" style="margin-top:5px"><div class="input-group col-lg-8 col-lg-offset-2">';
    $return .= montaSelect($arrSindicatos, $_REQUEST['option'], "name='sindicato[]' class='validate[required,custom[select]] sindicatos form-control' style='pointer-events: none; touch-action: none;' readonly");
    $return .= '<span class="loadSind input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span><span class="remSindicato input-group-addon"><i class="danger fa fa-minus"></i></span></div></div>';

//    $return = '<div class="row" style="margin-top:5px"><div class="input-group col-lg-8 col-lg-offset-2">';
//    $return .= montaSelect($arrSindicatos, null, "name='sindicato[]' class='validate[required,custom[select]] sindicatos form-control'");
//    $return .= '<span class="loadSind input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span><span class="input-group-addon addSindicato"><i class="success fa fa-plus"></i></span><span class="remSindicato input-group-addon"><i class="danger fa fa-minus"></i></span></div></div>';

    echo $return;
    exit();
}

if (isset($_REQUEST['codESocial'])) {

    $return = $movimento->getESocialByCodAjax($_REQUEST['codESocial']);

    echo json_encode($return);
    exit();
}

//insert
if (isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar") {
    $movimento->cadMovimento();
}

if (isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar") {
    $movimento->alteraMovimento($id_mov);
}

if ($id_mov == "") {
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $categoria_mov = montaSelect($movimento->selectCategoria(1), null, "id='categoria_mov' name='categoria_mov' class='form-control validate[required,custom[select]]'");
    $incidencia_mov = montaSelect($movimento->selectIncidencia(), null, "id='incidencia_mov' name='incidencia_mov' class='form-control'");
} else {
//    $sqlVerMovs = "SELECT * FROM rh_movimentos_sindicatos_assoc WHERE id_mov = $id_mov AND status = 1 ORDER BY id_sindicato";
    $sqlVerMovs = "SELECT * FROM rh_movimentos_sindicatos_assoc WHERE id_mov = $id_mov AND status = 1 /*ORDER BY id_sindicato*/";
    $queryVerMovs = mysql_query($sqlVerMovs);
    while ($rowVerMovs = mysql_fetch_assoc($queryVerMovs)) {
        $arrVerMovs[$rowVerMovs['cnpj_sindicato']] = $rowVerMovs['cnpj_sindicato'];
    }

    $acao = 'Edição';
    $botao = 'Atualizar';
    $categoria_mov = montaSelect($movimento->selectCategoria(1), $row['categoria'], "id='categoria_mov' name='categoria_mov' class='form-control validate[required,custom[select]]'");
    $incidencia_mov = montaSelect($movimento->selectIncidencia(), $row['incidencia'], "id='incidencia_mov' name='incidencia_mov' class='form-control'");
    $textESocial = $movimento->getESocialByCod($row['codigo_esocial']);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Movimentos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Movimentos</small></h2></div>

                    <form action="" method="post" id="form_mov" class="form-horizontal top-margin1" enctype="multipart/form-data">
                        <!--resposta de algum metodo realizado-->
                        <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>

                        <input type="hidden" id="regiao_selecionada" name="regiao_selecionada" value="<?php echo $regiao_selecionada; ?>" />                
                        <input type="hidden" id="id_nota" name="id_nota" value="" />
                        <input type="hidden" id="hide_parceiro" name="hide_parceiro" value="<?php echo $parceiro_bd; ?>" />
                        <input type="hidden" id="id_entrada" name="id_entrada" value="<?php echo $entrada_id; ?>" />
                        <input type="hidden" id="banco_sel" name="banco_sel" value="<?php echo $_REQUEST['banco']; ?>" />
                        <input type="hidden" id="mes_sel" name="mes_sel" value="<?php echo $_REQUEST['mes']; ?>" />
                        <input type="hidden" id="ano_sel" name="ano_sel" value="<?php echo $_REQUEST['ano']; ?>" />
                        <input type="hidden" name="home" id="home" value="" />

                        <div class="panel panel-default">
                            <div class="panel-heading text-bold"><h4><?= $acao ?> de Movimento</h4></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control validate[required]" id="nome" name="nome" value="<?php echo $row['descicao']; ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">                                                                                
                                        <label for="categoria_lista" class="col-lg-2 control-label">Categoria:</label>
                                        <div class="col-lg-4">
                                            <?php echo $categoria_mov; ?>
                                        </div>
                                        <label for="categoria_lista" class="col-lg-2 control-label">Incidência:</label>
                                        <div class="col-lg-3">
                                            <?php echo $incidencia_mov; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="categoria_lista" class="col-lg-2 control-label">Incide em:</label>
                                        <div class="col-lg-9">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_inss" value="1"
                                                <?php
                                                if ($row['incidencia_inss'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                INSS
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_fgts" value="1"
                                                <?php
                                                if ($row['incidencia_fgts'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                FGTS
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_irrf" value="1"
                                                <?php
                                                if ($row['incidencia_irrf'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                IRRF
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_base_ferias_media" value="1"
                                                <?php
                                                if ($row['incide_base_ferias_media'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                Média para Férias
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_base_13_media" value="1"
                                                <?php
                                                if ($row['incide_base_13_media'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                Média para 13º
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_aviso_previo" value="1"
                                                <?php
                                                if ($row['incide_aviso_previo'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                Média para Aviso Prévio
                                            </label>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="incide_dsr" value="1"
                                                <?php
                                                if ($row['incide_dsr'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> />
                                                DSR
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label for="mov_lancavel" class="col-sm-2 control-label">Movimento Lançavel:</label>
                                        <div class="col-sm-4 text-left">
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="mov_lancavel1" name="mov_lancavel" value="1"
                                                    <?php
                                                    if ($row['mov_lancavel'] == '1') {
                                                        echo "checked";
                                                    }
                                                    ?> />
                                                    Sim
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="mov_lancavel2" name="mov_lancavel" value="0"
                                                    <?php
                                                    if ($row['mov_lancavel'] != '1') {
                                                        echo "checked";
                                                    }
                                                    ?> />
                                                    Não
                                                </label>
                                            </div>
                                        </div>

                                        <!--Visivel so para programadores-->
                                        <?php
//                                        if (in_array($usuario['id_funcionario'], $func_f71)) {

                                            if ($id_mov != "") {
//                                                if ($row['campo_rescisao'] == 0) {
                                                    ?>
<!--                                                    <label for="campo_rescisao" class="col-sm-2 control-label">Campo Rescisão:</label>
                                                    <div class="col-sm-3 text-left">
                                                        <div class="radio radio-inline">
                                                            <label>                                                                    
                                                                <input type="radio" id="campo_rescisao1" name="campo_rescisao" value="1"
                                                                <?php
//                                                                if ($row['campo_rescisao'] != '0') {
//                                                                    echo "checked";
//                                                                }
                                                                ?> />
                                                                Sim
                                                            </label>
                                                        </div>
                                                        <div class="radio radio-inline">
                                                            <label>
                                                                <input type="radio" id="campo_rescisao2" name="campo_rescisao" value="0"
                                                                <?php
//                                                                if ($row['campo_rescisao'] == '0') {
//                                                                    echo "checked";
//                                                                }
                                                                ?> />
                                                                Não
                                                            </label>
                                                        </div>
                                                    </div>-->
                                                <?php // } else { ?>
                                                    <label for="campo_rescisao" class="col-sm-2 control-label">Campo Rescisão:</label>
                                                    <div class="col-sm-3 text-left">
                                                        <span class="label label-info"><?php echo $row['campo_rescisao']; ?></span>
                                                    </div>
                                                    
                                                    <input type="hidden" name="_campo_resc" value="<?php echo $row['campo_rescisao']; ?>" />
                                                    <?php
//                                                }
                                            }
//                                        }
                                        ?>
                                    </div>
                                </div>      
                                <div class="row">
                                    <div class="form-group">
                                        <label for="porcentagem1" class="col-lg-2 control-label">Porcentagem 1:</label>
                                        <div class="col-lg-4">
                                            <input type="number" step="0.01" min="0" class="form-control" id="porcentagem1" name="porcentagem1" value="<?php echo $row['percentual']; ?>" placeholder="Ex 0,05" />
                                        </div>
                                        <label for="porcentagem2" class="col-lg-2 control-label">Porcentagem 2:</label>
                                        <div class="col-lg-3">
                                            <input type="number" step="0.01" min="0" class="form-control" id="porcentagem2" name="porcentagem2" value="<?php echo $row['percentual2']; ?>" placeholder="Ex 0,05" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="tipo_lancamento" class="col-sm-2 control-label">Tipo de Lançamento:</label>
                                        <div class="col-sm-4 text-left">
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="tipo_lancamento1" class="validate[required]" name="tipo_lancamento" value="0"
                                                    <?php
                                                    if ($row['tipo_qnt_lancavel'] == '0') {
                                                        echo "checked";
                                                    }
                                                    ?> />
                                                    Valor
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="tipo_lancamento2" class="validate[required]" name="tipo_lancamento" value="1"
                                                    <?php
                                                    if ($row['tipo_qnt_lancavel'] == '1') {
                                                        echo "checked";
                                                    }
                                                    ?> />
                                                    Dias/horas
                                                </label>
                                            </div>
                                        </div>                                       
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="ignorar_rubrica" class="col-sm-2 control-label">Ignorar Rúbrica:</label>
                                        <div class="col-sm-4 text-left">
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="ignorar_rubrica1" name="ignorar_rubrica" value="1"
                                                    <?php
                                                    if ($row['ignorar_rubrica'] == '1') {
                                                        echo "checked";
                                                    }
                                                    ?> />
                                                    Sim
                                                </label>
                                            </div>
                                            <div class="radio radio-inline">
                                                <label>
                                                    <input type="radio" id="ignorar_rubrica2" name="ignorar_rubrica" value="0"
                                                    <?php
                                                    if ($row['ignorar_rubrica'] != '1') {
                                                        echo "checked";
                                                    }
                                                    ?> />
                                                    Não
                                                </label>
                                            </div>
                                        </div>                                       
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="esocial" class="col-lg-2 control-label">Código E-Social:</label>
                                        <div class="col-lg-8">
                                            <?= montaSelect($selectESocial, $row['codigo_esocial'], "id='esocial_codigo' name='esocial_codigo' class='validate[required,custom[select]] form-control'") ?>

                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="esocial_descricao" class="col-lg-2 control-label">Descrição do Código:</label>
                                        <div class="col-lg-8">
                                            <textarea disabled id="esocial_descricao" class="form-control" rows="4"><?= $textESocial[$row['codigo_esocial']]['descricao'] ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div id="boxSindicato">
                                    <div class="row">
                                        <label class="col-lg-2 control-label">Sindicatos:</label>
                                        <div class="input-group col-lg-8">
                                            <?= montaSelect($arrSindicatos, null, "name='sindicato[]' class='sindicatosSelect form-control'") ?>
                                            <span class="loadSind input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span>
                                            <span class="input-group-addon addSindicato"><i class="success fa fa-plus"></i></span>
                                            <span class="input-group-addon remTodosSind"><i class="danger fa fa-close"></i></span>
                                        </div>
                                    </div>
                                    <?php
                                    $i = 0;
                                    foreach ($arrVerMovs as $value) {
                                        ?>
                                        <div class="row" style="margin-top:5px">
                                            <div class="input-group col-lg-8 col-lg-offset-2">
                                                <?= montaSelect($arrSindicatos, $value, "name='sindicato[]' class=' sindicatos form-control' style='pointer-events: none; touch-action: none;' readonly") ?>
                                                <span class="loadSind input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span>
                                                <span class="input-group-addon remSindicato"><i class="danger fa fa-minus"></i></span>
                                            </div>
                                        </div>
                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="panel-footer text-right">
                                <input type="submit" class="btn btn-primary botaoSubmit" name="<?php echo strtolower($botao); ?>" value="<?= $botao ?>" />
                                <input type="hidden" name="<?= strtolower($botao) ?>" id="<?= strtolower($botao) ?>" value="<?= $botao ?>" />
                            </div>
                        </div>
                    </form>

                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->

            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <!--<script src="../../js/jquery-1.10.2.min.js"></script>-->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script>

            $(function () {

                $('#esocial_codigo').on('change', function () {

                    var codESocial = $(this).val();
                    console.log(codESocial);
                    if (codESocial != -1) {
                        $.post('', {codESocial: codESocial}, function (data) {

                            data = JSON.parse(data);
                            $('#esocial_descricao').html(data[codESocial].descricao);

                        });
                    } else {
                        $('#esocial_descricao').html('');
                    }
                });

                $(document).on('click', '.addSindicato', function () {
                    var value = $(this).parent().find('.sindicatosSelect').val();

                    if (value !== '-1') {

                        var t = $(this);
                        t.parent().find('.loadSind').removeClass('hide');

                        $.post('', {
                            sind: 1,
                            option: value
                        }, function (data) {
                            $('#boxSindicato').append(data);
                            t.parent().find('.loadSind').addClass('hide');
                        });
                        $(this).parent().find('.sindicatosSelect').val('-1');
                    }
                });

                $(document).on('click', '.remSindicato', function () {

                    var t = $(this);

                    t.parent().parent().remove();

                });


                $(document).on('click', '.remTodosSind', function () {

                    bootConfirm('Deseja excluir todos os sindicatos?', 'Atenção', function (result) {
                        if (result) {
                            $('.sindicatos').parent().parent().remove();
                        }
                    }, 'danger');
                });

                $("#form_mov").validationEngine({promptPosition: "topRight"});

            });

        </script>
    </body>
</html>