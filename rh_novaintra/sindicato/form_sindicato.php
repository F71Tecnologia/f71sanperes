<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/SindicatoClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $usuario['id_funcionario'];

if ($_REQUEST['sindicato'] != '') {
    $sindicato = $_REQUEST['sindicato'];
} elseif ($_SESSION['sindicato'] != '') {
    $sindicato = $_SESSION['sindicato'];
}

$row = getSindicatoID($sindicato);

//insert
if (isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar") {
    cadSindicato();
}

//update
if (isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar") {
    alteraSindicato();
}

$projeto_selecionado = $_REQUEST['hide_projeto'];
$regiao_edita = $row['id_regiao'];
$projeto_edita = $row['id_projeto'];

//trata insert/update
if ($sindicato == '') {
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
$qr_uf = mysql_query("SELECT * FROM uf");

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

$optAdTempoServico = [
    -1 => "-- Selecione o Tipo --",
    1 => "Valor Fixo",
    2 => "% Sobre Salário Base"];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$caminho = $_REQUEST['caminho'];
$breadcrumb_caminhos[0] = array("Gestão de RH" => "../index.php", "Sindicatos" => "index.php", "Detalhes de Sindicatos" => "detalhes_sindicato.php?sindicato=$sindicato");
$breadcrumb_caminhos[1] = array("Gestão de RH" => "../index.php", "Sindicatos" => "index.php");
$breadcrumb_config = array("nivel" => "", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "$acao de Sindicatos");
$breadcrumb_pages = $breadcrumb_caminhos[$caminho];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?= $acao ?> de Sindicatos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?= $acao ?> de Sindicatos</small></h2></div>
                </div>
            </div>
            <!--resposta de algum metodo realizado-->
            <?php if (!empty($_SESSION['MESSAGE'])) { ?>
                <div id="message-box" class="alert alert-info">
                    <?= $_SESSION['MESSAGE'] ?>
                </div>
            <?php } ?>
            <form action="" method="post" class="form-horizontal" name="form1" id="form1" autocomplete="off" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">Dados do Sindicato</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Nome</label>
                            <div class="col-lg-11">
                                <input type="text" name="nome" id="nome" class="form-control validate[required]" value="<?= $row['nome'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Endereço</label>
                            <div class="col-lg-11">
                                <input type="text" name="endereco" id="endereco" class="form-control validate[required]" value="<?= $row['endereco'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>CNPJ:</label>
                            <div class="col-lg-5">
                                <input type="text" name="cnpj" id="cnpj" class="form-control validate[required]" value="<?= $row['cnpj'] ?>" />
                            </div>
                            <label class='col-lg-1 control-label'>Contato:</label>
                            <div class="col-lg-5">
                                <input type="text" name="contato" id="contato" class="form-control validate[required]" size="30" value="<?= $row['contato'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Fax:</label>
                            <div class="col-lg-5">
                                <input type="text" name="fax" id="fax" class="form-control" value="<?= $row['fax'] ?>" />
                            </div>
                            <label class='col-lg-1 control-label'>Email:</label>
                            <div class="col-lg-5">
                                <input type="text" name="email" id="email" class="form-control validate[custom[email]]" size="30" value="<?= $row['email'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Celular:</label>
                            <div class="col-lg-5">
                                <input type="text" name="cel" id="cel" class="form-control" value="<?= $row['cel'] ?>" />
                            </div>
                            <label class='col-lg-1 control-label'>Site:</label>
                            <div class="col-lg-5">
                                <input type="text" name="site" id="site"class="form-control" value="<?= $row['site'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Telefone:</label>
                            <div class="col-lg-5">
                                <input type="text" name="tel" id="tel" class="form-control" value="<?= $row['tel'] ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t">Dados da Categoria</div>
                    <div class="panel-body">                     
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Mês de desconto:</label>
                            <div class="col-lg-5">
                                <?php echo montaSelect(mesesArray(), $row['mes_desconto'], "id='mes_desconto' name='mes_desconto' class='form-control'"); ?>
                            </div>
                            <label class='col-lg-1 control-label'>Piso Salarial:</label>
                            <div class="col-lg-5">
                                <input type="text" name="piso" id="piso" class="form-control" value="<?= number_format($row['piso'], 2, ',', '.') ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Féria(meses):</label>
                            <div class="col-lg-5">
                                <input type="text" name="ferias" id="ferias" class="form-control" value="<?= $row['ferias'] ?>" />
                            </div>
                            <label class='col-lg-1 control-label'>Entidade Sindical:</label>
                            <div class="col-lg-5">
                                <input type="text" name="entidade" id="entidade" class="form-control" placeholder="código" value="<?= $row['entidade'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>13(meses):</label>
                            <div class="col-lg-5">
                                <input type="text" name="decimo_terceiro" id="decimo_terceiro" class="form-control" value="<?= $row['decimo_terceiro'] ?>" />
                            </div>
                            <label class='col-lg-1 control-label'>Multa de FGTS:</label>
                            <div class="col-lg-5">
                                <input type="text" name="multa" id="multa" class="form-control" placeholder="%" value="<?= $row['multa'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Evento Relacionado:</label>
                            <div class="col-lg-5">
                                <select name="evento" id="evento" class="form-control">
                                    <option value="5019" <?= selected('5019', $row['evento']) ?>>CONTRIBUIÇÃO SINDICAL</option>
                                </select>
                            </div>
                            <label class='col-lg-1 control-label'>Fração:</label>
                            <div class="col-lg-5">
                                <input type="text" name="fracao" id="fracao" class="form-control" value="<?= $row['fracao'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Mês de dissídio:</label>
                            <div class="col-lg-5">
                                <?php echo montaSelect(mesesArray(), $row['mes_dissidio'], "id='mes_dissidio' name='mes_dissidio' class='form-control'"); ?>
                            </div>
                            <label class='col-lg-1 control-label'>Recisão:</label>
                            <div class="col-lg-5">
                                <input type="text" name="recisao" id="recisao" class="form-control" value="<?= $row['recisao'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Patronal:</label>
                            <div class="col-lg-5">
                                <select name="pratonal" id="pratonal" class="form-control">
                                    <option value="1" <?= selected('1', $row['pratonal']) ?>>SIM</option>
                                    <option value="2" <?= selected('2', $row['pratonal']) ?>>NÃO</option>
                                </select>
                            </div>
                        </div>
                        <!--                        <div class="form-group">
                                                    <label class='col-lg-1 control-label'>Patronal:</label>
                                                    <div class="col-lg-5">
                                                        <select name="pratonal" id="pratonal" class="form-control">
                                                            <option value="1" <?= selected('1', $row['pratonal']) ?>>SIM</option>
                                                            <option value="2" <?= selected('2', $row['pratonal']) ?>>NÃO</option>
                                                        </select>
                                                    </div>
                                                </div>-->
                    </div>
                    <div class="panel-body">  
                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Adicional Noturno:</label>
                            <div class="col-lg-5 selectpicher">
                                <select name="adNoturno" id="adNoturno" class="form-control">
                                    <option value="-1">Selecione</option>
                                    <option <?= selected('1', $row['adNoturno']) ?> value="1">Sim</option>
                                    <option <?= selected('0', $row['adNoturno']) ?> value="0">Não</option>
                                </select>
                            </div>
                        </div>

<!--                        <div class="form-group" id="add_noturno" style="display:none">
                            <label class='col-lg-1 control-label'>Horas Noturnas:</label>
                            <div class="col-lg-5 selectpicher">
                                <input type="text" name="hr_noturna" class="form-control" value="<?= $row['hr_noturna'] ?>">
                            </div>
                            <label class='col-lg-1 control-label'>Porcentagem Adicional Noturno:</label>
                            <div class="col-lg-5 selectpicher">
                                <input type="text" name="prcentagem_add_noturno" class="form-control" value="<?= $row['prcentagem_add_noturno'] ?>">
                            </div>
                        </div>-->


                        <div class="form-group">
                            <label class='col-lg-1 control-label text-sm'>Insalubridade:</label>
                            <div class="col-lg-5 selectpicher">
                                <select name="insalubridade" id="insalubridade" class="form-control">
                                    <option value="-1">Selecione</option>
                                    <option <?= selected('1', $row['insalubridade']) ?> value="1">Sim</option>
                                    <option <?= selected('0', $row['insalubridade']) ?> value="0">Não</option>
                                </select>
                            </div>

                            <label class='col-lg-1 control-label text-sm'>Periculosidade:</label>
                            <div class="col-lg-5 selectpicher">
                                <select name="periculosidade" id="periculosidade" class="form-control">
                                    <option value="-1">Selecione</option>
                                    <option <?= selected('1', $row['periculosidade']) ?> value="1">Sim</option>
                                    <option <?= selected('0', $row['periculosidade']) ?> value="0">Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class='col-lg-1 control-label'>Contribuição Assistencial:</label>
                            <div class="col-lg-5 selectpicher">
                                <select name="contribuicao_assistencial" id="contribuicao_assistencial" class="form-control">
                                    <option value="-1">Selecione</option>
                                    <option <?= selected('1', $row['contribuicao_assistencial']) ?> value="1">Sim</option>
                                    <option <?= selected('0', $row['contribuicao_assistencial']) ?> value="0">Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class='col-lg-1 control-label text-left'>Auxilio Creche:</label>
                            <div class="col-lg-2 selectpicher">
                                <select name="creche" id="creche" class="form-control">
                                    <option value="-1">Selecione</option>
                                    <option <?= selected('1', $row['creche']) ?> value="1">Sim</option>
                                    <option <?= selected('0', $row['creche']) ?> value="0">Não</option>
                                </select>
                            </div>
                        </div>    

                        <div class="form-group">    
                            <label class='col-lg-1 control-label '>Valor Creche:</label>
                            <div class="col-lg-3 selectpicher">
                                <input type="text" name="creche_base" class="form-control" value="<?= number_format($row['creche_base'], 2, ',', '.') ?>">
                            </div>
                            <label class='col-lg-1 control-label  text-sm'>Porcentagem Creche:</label>
                            <div class="col-lg-3 selectpicher">
                                <input type="text" name="creche_percentual" class="form-control" value="<?= str_replace('.', ',', $row['creche_percentual']) ?>">
                            </div>
                            <label class='col-lg-1 control-label '>Idade Máxima:</label>
                            <div class="col-lg-3 selectpicher">
                                <input type="text" name="creche_idade" class="form-control" value="<?= $row['creche_idade'] ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class='col-lg-1 control-label text-sm'>Valor Vale Refeição</label>
                            <div class="col-lg-3 selectpicher">
                                <input type="text" name="valor_refeicao" class="form-control money" value="<?= number_format($row['valor_refeicao'], 2, ',', '.') ?>">
                            </div>
                            <label class="col-lg-1 control-label text-sm">Valor Vale Alimentação</label>
                            <div class="col-lg-3 selectpicher">
                                <input type="text" name="valor_alimentacao" class="form-control money" value="<?= number_format($row['valor_alimentacao'], 2, ',', '.') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class='col-lg-1 control-label text-sm'>CLAUSULA 33ª CCT</label>
                            <div class="col-lg-3 selectpicher">
                                <select name="clausula_33" id="clausula_33" class="form-control">
                                    <option value="-1">Selecione</option>
                                    <option <?= selected('1', $row['clausula_33']) ?> value="1">Sim</option>
                                    <option <?= selected('0', $row['clausula_33']) ?> value="0">Não</option>
                                </select>
                            </div>                             
                        </div>
                        <div class="panel-heading border-t">
                            Adicional por Tempo de Serviço
                            <span style="display: block; font-style: italic; color: #b7b6b6;">Informe o tempo mínimo de serviço (em meses), bem como o fator de pagamento.</span>
                        </div>
                        <div class="panel-body">
                            <div id="boxAdTempoServico">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input type="number" id="tempo_ad_tempo_servico" name="tempo_ad_tempo_servico" class="form-control" placeholder="Quantidade de Meses" value="<?= $row['tempo_ad_tempo_servico'] ?>"/>
                                            <div class="input-group-addon"></div>
                                            <?= montaSelect($optAdTempoServico, $row['tipo_ad_tempo_servico'], "id='tipo_ad_tempo_servico' name='tipo_ad_tempo_servico' class='form-control'") ?>
                                            <div class="input-group-addon"></div>
                                            <input <?= ($row['tipo_ad_tempo_servico'] > 1) ? null : 'hide' ?> id="valor_fixo_ad_tempo_servico" name="valor_fixo_ad_tempo_servico" class="money form-control <?= ($row['tipo_ad_tempo_servico'] <= 1) ? null : 'hide' ?>" placeholder="Valor..." value="<?= number_format($row['valor_fixo_ad_tempo_servico'], 2, ',', '.') ?>">
                                            <input id="porc_ad_tempo_servico" name="porc_ad_tempo_servico" class="money form-control <?= ($row['tipo_ad_tempo_servico'] > 1) ? null : 'hide' ?>" placeholder="%" value="<?= number_format($row['porc_ad_tempo_servico'] * 100, 2, ',', '.') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" id="sindicato" name="sindicato" value="<?= $row['id_sindicato'] ?>" />
                        <input type="hidden" id="home" name="home" />
                        <input type="submit" class="btn btn-primary" name="<?= strtolower($botao) ?>" id="<?= strtolower($botao) ?>" value="<?= $botao ?>" />
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>


        <script>
            $(function () {
                
                $("#tipo_ad_tempo_servico").on("change", function () {
                   
                    var t = $(this);
                    var tipo = t.val();
//                    console.log(tipo);
                    if (tipo == 1) {
                        $('#valor_fixo_ad_tempo_servico').removeClass('hide');
                        $('#valor_fixo_ad_tempo_servico').attr('disabled',false);
                        $('#porc_ad_tempo_servico').val(null);
                        $('#porc_ad_tempo_servico').addClass('hide');
                    } else if (tipo == -1) {
                        $('#valor_fixo_ad_tempo_servico').val(null);
                        $('#valor_fixo_ad_tempo_servico').attr('disabled',true);
                        $('#valor_fixo_ad_tempo_servico').removeClass('hide');
                        $('#porc_ad_tempo_servico').val(null);
                        $('#porc_ad_tempo_servico').addClass('hide');
                    } else {
                        $('#valor_fixo_ad_tempo_servico').val(null);
                        $('#valor_fixo_ad_tempo_servico').addClass('hide');
                        $('#porc_ad_tempo_servico').removeClass('hide');
                    }
                   
                });
                
                $('#porc_ad_tempo_servico').on('blur', function () {
                    
                    var t = $(this);
                    var porc = t.val();
                    porc = porc.replace('.','').replace(',','.');
                    
                    if (porc > 100) {
                        
                        bootAlert("A porcentegem não pode ultrapssar 100%", "Dados Inválidos", function () {
                            t.val(null);
                        }, 'danger');
                    }
                    
                });
                
                //mascara                
                $("#tel, #cel, #fax").mask("(99)9999-9999?9");
                $("#cnpj").mask("99.999.999/9999-99");
                $("#piso,.money").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});

                //validation engine
                $("#form1").validationEngine({promptPosition: "topRight"});

                $("#adNoturno").change(function () {
                    var value = $("#adNoturno").val();
                    if (value == 1) {
                        $("#add_noturno").show();
                    } else {
                        $("#add_noturno").hide();
                    }
                });

                $("#adNoturno").trigger('change');

            });
        </script>
    </body>
</html>
