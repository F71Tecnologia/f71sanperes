<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClass.php');
include('../../classes/LogClass.php');
include("../../classes_permissoes/acoes.class.php");

$acoes = new Acoes();
$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$arrSindicatos = GlobalClass::carregaSindicatosByRegiao($id_regiao);
$arrHoristaPlantonista = array('0' => 'Não', '1' => 'Sim');

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "alteraSalario") {
    $id_curso = $_REQUEST['id_curso'];
    $data_cad = date('Y-m-d');
    $salario_antigo = $_REQUEST['salario_antigo'];
    $salario_novo = $_REQUEST['salario_new'];
    $diferenca = $_REQUEST['difere'];
    $motivo = utf8_decode($_REQUEST['motivo']);

    mysql_query("INSERT INTO rh_salario (id_curso,data,salario_antigo,salario_novo,diferenca,user_cad,motivo,status) VALUES 
    ('$id_curso','$data_cad','$salario_antigo','$salario_novo','$diferenca','$id_usuario','{$motivo}','1')") or die(mysql_error());

    mysql_query("UPDATE curso SET salario = '$salario_novo', valor = '$salario_novo' WHERE id_curso = '$id_curso' LIMIT 1") or die(mysql_error());

    $return = array('status' => 1);
    //$return = $_REQUEST;
    $return['valor'] = "R$ " . number_format($_REQUEST['salario_new'], 2, ",", ".");
    //"R$ ".number_format($_REQUEST['salario_new'],2,",",".");

    onUpdateCltsByCurso($id_curso);

    echo json_encode($return);
    exit;
}

$sql = FuncoesClass::getRhHorario($_REQUEST['curso']);
$total_horario = mysql_num_rows($sql);

$row = FuncoesClass::getCursosID($_REQUEST['curso']);

//$tipo_diretor

$altera_funcao = FuncoesClass::alteraFuncao($usuario, $id_regiao, $id_usuario);
onUpdateCltsByCurso($_REQUEST['curso']);

//dados para voltar no index com select preenchido
$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$sql_departamento = "SELECT * FROM setor ORDER BY nome";
$sql_departamento = mysql_query($sql_departamento);
$arrayDepartamentos[0] = 'Selecione';
while ($row_departamento = mysql_fetch_assoc($sql_departamento)) {
    $arrayDepartamentos[$row_departamento['id_setor']] = $row_departamento['nome'];
}
if ($regiao_selecionada == '') {
    $_SESSION['regiao_select'];
    $_SESSION['projeto_select'];
    session_write_close();
} else {
    $_SESSION['regiao_select'] = $regiao_selecionada;
    $_SESSION['projeto_select'] = $projeto_selecionado;
    session_write_close();
}

$caminho = (!empty($_REQUEST[caminho])) ? $_REQUEST[caminho] : 0;

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_caminhos[0] = array("Gestão de Funções" => "index.php");
$breadcrumb_caminhos[1] = array("Gestão de Funções" => "index.php", "Detalhe de Função" => "detalhes_curso.php?curso={$_REQUEST['curso']}");
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Editar Função " . $row['nome_funcao']);
$breadcrumb_pages = $breadcrumb_caminhos[$caminho];

/**
 * FUNÇÃO PARA FAZER UPDATE
 * @param type $id_curso
 */
function onUpdateCltsByCurso($id_curso) {
    /**
     * FAZENDO CONSULTA PARA SABER QUAIS 
     * SÃO OS CLTs QUE VÃO SOBRE O  UPDATE 
     * NA COLUNA DATA_ULTIMO_UPDATE
     * 67,68,69
     * RESCISAO INDIRETA COM AFASTAMENTO, RESCISAO INDIRETA SEM AFASTAMENTO, LICENÇA SEM VENCIMENTO 
     * (RESPECTIVAMENTE)
     */
    $queryVerificaClts = "SELECT * FROM rh_clt AS A WHERE A.id_curso = '{$id_curso}' AND ((A.status < 60 || A.status = 200) || A.status IN(67,68,69))";
    $sqlVerificaClts = mysql_query($queryVerificaClts) or die("Erro ao selecionar clts");
    if (mysql_num_rows($sqlVerificaClts) > 0) {
        while ($rows = mysql_fetch_assoc($sqlVerificaClts)) {
            /**
             * ATUALIZANDO A TABELA DE RH_CLT
             * COM A DATA ATUAL DA AÇÃO DE 
             * FINALIZAR A FOLHA
             */
            onUpdate($rows['id_clt']);
        }
    }
}

$opt_ad_cargo_confianca = [
    '-1' => "« Selecione »",
    '1' => "1 - Valor Fixo",
    '2' => "2 - Porcentagem"];

$tipo_ad_cargo_confianca = -1;
if ($row['valor_ad_cargo_confianca'] > 0) {
    $tipo_ad_cargo_confianca = 1;
} else if ($row['percentual_ad_cargo_confianca'] > 0) {
    $tipo_ad_cargo_confianca = 2;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Editar Função <?= $row['nome_funcao'] ?></title>
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
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <style>

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
            .bt-image{                
                cursor: pointer;
            }
            .some_insa, #hide_noturno, .hide_noturno{
                display: none;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Editar Função <?= $row['nome_funcao'] ?></small></h2></div>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
                    <input type="hidden" name="id_curso" id="id_curso" value="<?= $row['id_curso'] ?>" />
                    <input type="hidden" name="regiao" id="regiao" value="<?= $row['id_regiao'] ?>" />
                    <input type="hidden" name="projeto" id="projeto" value="<?= $row['campo3'] ?>" />
                    <input type="hidden" name="id_cbo" id="id_cbo" value="<?= $row['cbo_codigo'] ?>" />
                    <input type="hidden" name="contratacao_curso" id="contratacao_curso" value="<?= $row['tipo'] ?>" />
                    <div class="col-xs-12 form_funcoes">
                        <div class="panel panel-default">
                            <div class="panel-heading">Dados da Função</div>
                            <div class="panel-body">
                                <fieldset id="func1">
                                    <div class="form-group">
                                        <label for="departamento" class="col-xs-2 control-label">Departamento:</label>
                                        <div class="col-xs-10">
                                            <?= montaSelect($arrayDepartamentos, $row['id_departamento'], 'name="departamento" id="departamento" class="form-control validate[required,custom[select]] departamento"'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome_funcao" class="col-xs-2 control-label">Nome da Função:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="nome_funcao" id="nome_funcao" value="<?= $row['nome_funcao'] ?>" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="sindicato" class="col-lg-2 control-label">Sindicato:</label>
                                        <div class="col-lg-10">
                                            <?php $sindRO = ($row['tipo_diretor'] > 1) ? 'readonlySelect' : null ; ?>
                                            <?= montaSelect($arrSindicatos, $row['id_sindicato'], "name='sindicato' id='sindicato' class='$sindRO sindicatos form-control'") ?>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="form-group">
                                        <label for="horista_plantonista" class="col-xs-2 control-label">Função para Horista ?</label>
                                        <div class="col-xs-10">
                                            <?= montaSelect($arrHoristaPlantonista, $row['horista_plantonista'], "name='horista_plantonista' id='horista_plantonista' class='validate[required,custom[select]] form-control'") ?>
                                        </div>
                                    </div>
                                    -->
                                    <div class="form-group">
                                        <label for="area" class="col-xs-2 control-label">Área:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="area" id="area" value="<?= $row['area'] ?>" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="cbo" class="col-xs-2 control-label">Nome do CBO:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="cbo" id="cbo" value="<?= $row['nome_cbo'] ?>" class="form-control validate[required]" placeholder="Ex: Assistente administrativo  - 4110.10" />
                                            <span id="selection"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label">
                                            <?php 
                                                if($row['horista_plantonista'] == 0){
                                                   echo "Salário:";
                                                }else{
                                                    echo "Salário/Hora";
                                                }
                                            ?>
                                            
                                        </label>
                                        <div class="col-xs-4 control-label text-left">
                                            <?php if ($acoes->verifica_permissoes(84)) { ?>
                                                <span id='textVal'><?= formataMoeda($row['salario']) ?></span>
                                                <img src="../../imagens/icones/icon-edit.gif" title="Editar Valor" class="edita_valor bt-image" data-type="salario" data-key="<?= $row['id_curso'] ?>" data-toggle="modal" data-target="#box_salario" />
                                                <?php
                                            } else {
                                                echo formataMoeda($row['valor']);
                                            }
                                            ?>
                                        </div>
                                        <label for="mes_abono" class="col-xs-2 control-label">Mês Abono:</label>
                                        <div class="col-xs-4">
                                            <?= montaSelect(mesesArray(), $row['mes_abono'], "id='mes_abono' name='mes_abono' class='form-control'"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="qtd_contratacao" class="col-xs-2 text-right no-margin-b">Qtd. Máxima de Contratação:</label>
                                        <div class="col-xs-4">
                                            <input type="number" name="qtd_contratacao" id="qtd_contratacao" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $row['qnt_maxima'] ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="hora_semana" class="col-xs-2 control-label">Horas Semanais:</label>
                                        <div class="col-xs-4">
                                            <input type="number" name="hora_semana" id="hora_semana" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $row['hora_semana'] ?>" />
                                        </div>
                                        <label for="horas" class="col-xs-2 control-label">Horas Mensais:</label>
                                        <div class="col-xs-4">
                                            <input type="number" name="horas" id="horas" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $row['hora_mes'] ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="gratificacao_funcao" class="col-xs-2 control-label">Gratificação por Função:</label>
                                        <div class="col-xs-4">
                                            <input type="text" name="gratificacao_funcao" id="gratificacao_funcao" class="form-control" value="<?= number_format($row['gratificacao_funcao'],2,',','.'); ?>" placeholder="Valor..."/>
                                        </div>
                                        <label for="quebra_caixa" class="col-xs-2 control-label">Quebra de Caixa:</label>
                                        <div class="col-xs-4">
                                            <input type="text" name="quebra_caixa" id="quebra_caixa" class="form-control valor" value="<?= number_format($row['quebra_caixa'],2,',','.'); ?>" placeholder="Valor..."/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_ad_cargo_confianca" class="col-xs-2 control-label">Adicional por Cargo de Confiança:</label>
                                        <div class="col-xs-2">
                                            <input type="checkbox" name="tipo_cargo_confianca" value="1" <?= ($tipo_ad_cargo_confianca == 1) ? "checked" : null?> /> Valor 
                                            <br>
                                            <input type="checkbox" name="tipo_cargo_confianca" value="2" <?= ($tipo_ad_cargo_confianca == 2) ? "checked" : null?> /> Percentual
                                        </div>
                                        <div class="col-xs-2 <?= ($tipo_ad_cargo_confianca != 1) ? "hide" : null; ?>">
                                            <input type="text" id="valor_ad_cargo_confianca" name="valor_ad_cargo_confianca" class="form-control valor" placeholder="Valor..." value="<?=number_format($row['valor_ad_cargo_confianca'],2,'.','');?>" />
                                        </div>
                                        <div class="col-xs-2 <?= ($tipo_ad_cargo_confianca != 2) ? "hide" : null; ?>">
                                            <input type="text" id="percentual_ad_cargo_confianca" name="percentual_ad_cargo_confianca" class="form-control valor" placeholder="Percentual..." value="<?=number_format($row['percentual_ad_cargo_confianca'] * 100,2,'.','');?>" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="risco_vida" class="col-xs-2 text-right no-margin-b">Risco de Vida 30%:</label>
                                        <div class="col-xs-4">
                                            <input type="checkbox" name="risco_vida" id="risco_vida" value="1" <?= ($row['risco_vida']) ? "checked" : null; ?> />
                                        </div>
                                    </div>
                                    <!--                                    <div class="form-group">
                                                                            <label for="nome" class="col-xs-2 text-right no-margin-b">SOBRE AVISO</label>
                                                                            <div class="col-xs-1">
                                                                                <input type="radio" name="sobre_aviso" id="sobre_aviso" <?php
                                    if ($row['sobre_aviso'] == '0') {
                                        echo "checked";
                                    }
                                    ?> value="0" />Não
                                                                            </div>
                                                                            <div class="col-xs-1">
                                                                                <input type="radio" name="sobre_aviso" id="sobre_aviso" <?php
                                    if ($row['sobre_aviso'] == '1') {
                                        echo "checked";
                                    }
                                    ?> value="1" />Sim
                                                                            </div>
                                                                        </div>-->
                                    <?php if ($row['tipo'] == 2) { ?>
                                        <div class="form-group">
                                            <label for="nome" class="col-xs-2 control-label">Adicional:</label>
                                            <div class="col-xs-2">
                                                <input type="radio" name="periculosidade" value="0" id="none" <?php
                                                if ($row['tipo_insalubridade'] == '0' AND $row['periculosidade_30'] == '0') {
                                                    echo "checked";
                                                }
                                                ?> /> <label for="none" class="reset">Nenhum</label>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="radio" name="periculosidade" value="0" id="insal" <?php
                                                if ($row['tipo_insalubridade'] != '0') {
                                                    echo "checked";
                                                }
                                                ?> /> <label for="insal" class="reset">Insalubridade</label>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="radio" name="periculosidade" value="1" id="peric" <?php
                                                if ($row['periculosidade_30'] == '1') {
                                                    echo "checked";
                                                }
                                                ?> /> <label for="peric" class="reset">Periculosidade 30%</label>
                                            </div>
                                        </div>
                                        <div class="form-group some_insa">
                                            <label for="insalubridade" class="col-xs-2 control-label">Insalubridade:</label>
                                            <div class="col-xs-10">
                                                <select name="insalubridade" id="insalubridade" class="form-control">
                                                    <option value="-1">« Selecione »</option>
                                                    <option value="1" <?= selected(1, $row['tipo_insalubridade']); ?>>Insalubridade 20%</option>
                                                    <option value="2" <?= selected(2, $row['tipo_insalubridade']); ?>>Insalubridade 40%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group some_insa">
                                            <label for="qtd_salarios" class="col-xs-2 control-label">Quantidade de Salários:</label>
                                            <div class="col-xs-10">
                                                <input type="number" name="qtd_salarios" id="qtd_salarios" class="form-control" maxlength="4" value="<?= $row['qnt_salminimo_insalu'] ?>" />
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                    <!--
                                        <div class="form-group">
                                            <label for="nome" id="p_valor_hora" class="col-xs-2 control-label">Valor Hora:</label>
                                            <div class="col-xs-10" id="p_valor_hora">
                                                <input type="text" name="valor_hora_cooperado" id="valor_hora" class="form-control money" value="<?= $row['valor_hora'] ?>" />
                                            </div>
                                        </div>
                                    -->
                                    <?php } ?>
                                    <div class="form-group">
                                        <label for="descricao" class="col-xs-2 control-label">Descrição:</label>
                                        <div class="col-xs-10">
                                            <textarea name="descricao" id="descricao" class="form-control"><?= $row['descricao'] ?></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" class="btn btn-default" />
                                <input type="submit" name="atualizar" id="atualizar" value="Atualizar" class="btn btn-primary" />
                            </div>
                        </div>
                    </div><!--form_funcoes-->
                </form>
            </div>
            <div class="modal fade" id="box_salario" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <form action="" method="post" name="form2" id="form2" autocomplete="off" class="form-horizontal">
                        <div class="modal-content">
                            <div class="modal-header bg-primary" id="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Alteração Salarial</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <h2 class="col-xs-12" id="erro2"></h2>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3">Salário Antigo:</label>
                                    <div class="col-xs-9">
                                        <?= formataMoeda($row['salario']) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Salário Novo: R$ </label>
                                    <div class="col-xs-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="salario_novo" id="salario_novo">
                                            <span class="input-group-addon pointer"><i class="fa fa-calculator"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3">Diferença:</label>
                                    <div class="col-xs-9">
                                        R$: <strong id="diferenca"></strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3">Motivo:</label>
                                    <div class="col-xs-9">
                                        <textarea class="form-control" name="motivo"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="id_curso" id="id_curso" value="<?= $row['id_curso'] ?>" />
                                <input type="hidden" name="salario_antigo" id="salario_antigo" value="<?= $row['salario'] ?>" />
                                <input type="hidden" name="salario_new" id="salario_new" value="" />
                                <input type="hidden" name="difere" id="difere" value="" />

                                <input type="button" class="btn btn-primary" name="altera_salario" id="altera_salario" value="Atualizar" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <!--script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script-->
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        <script>
                                    $(function () {
                                        
                                        $('input[name=tipo_cargo_confianca]').on('click', function() {
                                            
                                            var t = $(this);
                                            var val = t.val();
                                            
                                            if (t.is(':checked') && val == 1) {
                                                $('#valor_ad_cargo_confianca').parent().removeClass('hide');
                                                $('#percentual_ad_cargo_confianca').parent().addClass('hide');
                                                $('input[name=tipo_cargo_confianca][value=2]').prop({checked:false});
                                                $('#percentual_ad_cargo_confianca').val(null);
                                            } else if (t.is(':checked') && val == 2) {
                                                $('#percentual_ad_cargo_confianca').parent().removeClass('hide');
                                                $('#valor_ad_cargo_confianca').parent().addClass('hide');
                                                $('input[name=tipo_cargo_confianca][value=1]').prop({checked:false});
                                                $('#valor_ad_cargo_confianca').val(null);
                                            } else {
                                                $('#valor_ad_cargo_confianca').parent().addClass('hide');
                                                $('#percentual_ad_cargo_confianca').parent().addClass('hide');
                                                $('#valor_ad_cargo_confianca').val(null);
                                                $('#percentual_ad_cargo_confianca').val(null);
                                            }
                                            
                                        });
                                        
                                        $('#percentual_ad_cargo_confianca').on('blur', function() {
                                            var t = $(this);
                                            var val = t.val();
                                            console.log(val);
                                            if (val > 100) {
                                                bootAlert('A porcentagem não pode ultrapassar 100%.','Valor Inválido',null,'danger');
                                                t.val(0);
                                            }
                                        });
                                        
                                        $('.valor').maskMoney({thousands: '', decimal: '.'});
                                        
                                        var diretor_empregado = $('#diretor_empregado');
                                        var diretor_nao_empregado = $('#diretor_nao_empregado');
                                        var proprietario_ou_socio = $('#proprietario_ou_socio');
                                        var socio_cotista = $('#socio_cotista');

//                                        if(diretor_nao_empregado.is(':checked') || proprietario_ou_socio.is(':checked') || socio_cotista.is(':checked')){
//                                            $('#cbo').prop('disabled', true);
//                                            $('#cbo').removeClass('validate[required]');
//                                        }

                                        //mascara
                                        $("#data_ini").mask("99/99/9999");
                                        $("#data_fim").mask("99/99/9999");
                                        $("#salario, #valor, #quota, #salario_novo, #gratificacao_funcao").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});
                                        $(".entrada, .ida_almoco, .volta_almoco, .saida").mask("99:99");

                                        //autocomplete
                                        $("#cbo").autocomplete("lista_cbo.php", {
                                            width: 600,
                                            matchContains: false,
                                            minChars: 3,
                                            selectFirst: false
                                        });

                                        //validation engine
                                        $("#form1").validationEngine({promptPosition: "topRight"});

                                        //oculta/exibe dados do CLT
                                        window.func2 = $("#func2").clone();
                                        $('#contratacao').change(function () {
                                            if (($(this).val() == "1") || ($(this).val() == "3")) {
                                                $("#func2").remove();
                                            } else if ($(this).val() == "2") {
                                                if (!$("div.form_funcoes fieldset#func2").length) {
                                                    var fieldset = $(document.createElement('fieldset')).append(window.func2.html()).prop('id', 'func2');
                                                    $("#func1").after(fieldset);
                                                }
                                            }
                                        });

                                        //chickbox
                                        $(".bt-image").on("click", function () {
                                            var action = $(this).data("type");

                                            var txtVal = $("#textVal").html();
                                            $("#salario_antigo").html("#salario_new");
                                            $(".valorForm").html(txtVal);
                                            $("#salario_novo").val("");
                                            $("#diferenca").html("");
                                            $("#erro2").html("");
//                    
//                    if (action === "salario") {
//                        //thickBoxIframe("Alteração Salarial", "altera_salario.php", {curso: key, method: "getDocs"}, "360-not", "240");
//                        thickBoxModal("Alteração Salarial", "#box_salario", "240", "360", null, null).css({display: "block"});
//                    }
                                        });

                                        //calculo de diferença salarial
                                        $(".fa-calculator").click(function () {
                                            var antigo = $('#salario_antigo').val();
                                            var novo = $('#salario_novo').val().replace('.', '');
                                            novo = novo.replace(',', '.');
                                            var total = (parseFloat(novo) - parseFloat(antigo)).toFixed(2);

                                            $("#diferenca").html(total);
                                            $("#difere").val(total);
                                            $("#salario_new").val(novo);
                                            $("#salario").val(novo);
                                        });

                                        $("#altera_salario").click(function () {
                                            var novo = $('#salario_novo').val().replace('.', '');
                                            novo = novo.replace(',', '.');
                                            var data = $("#form2").serialize();

                                            if ((novo === 0) || (novo === '')) {
                                                $("#erro2").html('<strong>Preencha o Salário Novo</strong>').css({color: "#F00"});
                                            } else if ($("#difere").val() === '') {
                                                $("#erro2").html('<strong>Calcule a diferença</strong>').css({color: "#F00"});
                                            } else {
                                                $.post('edit_curso.php?method=alteraSalario&' + data, null, function (data) {
                                                    if (data.status == 1) {
                                                        $('#textVal').html(data.valor);
                                                        //$(".ui-icon-closethick").trigger("click");
                                                        $('#box_salario').modal('toggle');
                                                    }
                                                }, 'json');
                                            }
                                        });

                                        //clona o fieldset de horario
                                        $("#add_hor").click(function () {
                                            var clone = $('.form_funcoes .horario:last').clone(false);
                                            var next_position = parseFloat(clone.attr('data-position')) + 1;
                                            clone.attr('data-position', next_position);
                                            clone.find("*[id]").andSelf().each(function () {
                                                $(this).attr("id", $(this).attr("id") + next_position);
                                            });

                                            clone.find(".check_not").each(function () {
                                                $(this).attr({name: "noturno[" + next_position + "]"});
                                            });

                                            clone.find('.config_noturno').hide();
                                            clone.find('.n_nao').prop('checked', true);

                                            $('.form_funcoes .horario:last').after(clone);
                                            var p = $(this).prev().attr("data-position");
                                            if (p == next_position) {
                                                $("fieldset[data-position = " + next_position + "] .check[value=1]").attr({name: "folga[" + next_position + "][0]"});
                                                $("fieldset[data-position = " + next_position + "] .check[value=2]").attr({name: "folga[" + next_position + "][1]"});
                                                $("fieldset[data-position = " + next_position + "] .check[value=5]").attr({name: "folga[" + next_position + "][2]"});
                                            }

                                            clone.find(".limpa").val("");
                                            clone.find(".check").prop('checked', false);
                                            clone.find(".horas_noturno").val("");

                                            $('.form_funcoes .horario:last').addClass("del");
                                            $(".del .del_hor").css({display: 'block'});

                                            $(".entrada, .ida_almoco, .volta_almoco, .saida")
                                                    .unmask() //Desabilita a máscara. Se não fizer isso dá problema
                                                    .mask("99:99"); //Habilita novamente, pegando todos os campos criados 

                                            $("body").on('click', ".check_not", function () {
                                                var hide_noturno = $(this).parent().parent().parent().next();
                                                var noturno = $(this).val();

                                                if (noturno == 1) {
//                                                    $(".por_noturno").show();
                                                    hide_noturno.show();
                                                } else {
                                                    hide_noturno.children().val('');
//                                                    $(".por_noturno").hide();
                                                    hide_noturno.hide();
                                                }
                                            });

                                            $(".del_hor").on('click', function () {
                                                $(this).parents("fieldset").remove();
                                            });
                                        });

                                        $(".del_hor").on('click', function () {
                                            $(this).parents("fieldset").remove();
                                        });

                                        //trata insalubridade/periculosidade
                                        $("#insal").click(function () {
                                            $(".some_insa").show();
                                            $("#insalubridade").addClass("validate[custom[select]]");
                                            $("#qtd_salarios").addClass("validate[required,custom[onlyNumberSp]]");
                                        });

                                        $("#peric, #none").click(function () {
                                            $(".some_insa").hide();
                                            $("#insalubridade").removeClass("validate[custom[select]]");
                                            $("#qtd_salarios").removeClass("validate[required,custom[onlyNumberSp]]");
                                        });

                                        if ($("#insal").is(':checked')) {
                                            $(".some_insa").show();
                                        }

                                        $("body").on('click', ".check_not", function () {
                                            var hide_noturno = $(this).parent().parent().parent().next();
                                            var noturno = $(this).val();

                                            if (noturno == 1) {
//                                                $(".por_noturno").removeClass('hide');
                                                hide_noturno.show();
                                            } else {
                                                hide_noturno.children().val('');
//                                                $(".por_noturno").addClass('hide');
//                            $(".por_noturno").hide();
                                                hide_noturno.hide();
                                            }
                                        });

                                        $(".n_sim").each(function () {
                                            if ($(".n_sim").is(':checked')) {
                                                $(this).parent().parent().parent().next().show();
                                            }
                                        });

                                    });
        </script>
    </body>
</html>