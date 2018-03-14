<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

//ARRAY DE FUNCIONARIO DA F71
//$func_f71 = array('255', '258', '256', '259', '260', '158', '257', '179');

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/clt.php');
include("../../classes/CalculoFeriasClass.php");
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_clt = $_REQUEST['id_clt'];

$feriasObj = new Ferias();
$calcFeriasObj = new Calculo_Ferias();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$qr_clt = "SELECT a.id_clt,a.nome,a.id_projeto,a.id_unidade,
    date_format(a.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(a.data_saida, '%d/%m/%Y') AS data_saida2, 
    (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto) AS nome_projeto, 
    (SELECT unidade FROM unidade WHERE id_unidade = a.id_unidade) AS nome_unidade,
    b.id_curso,b.nome AS nome_curso, b.salario
    FROM rh_clt AS a
    INNER JOIN curso AS b ON (a.id_curso = b.id_curso)
    WHERE a.id_clt = '$id_clt'";
//echo $qr_clt;
$result_clt = mysql_query($qr_clt);
$row_clt = mysql_fetch_assoc($result_clt);

// Informações do CLT
$Clt = new clt();
$Clt->MostraClt($id_clt);
$data_entrada = $Clt->data_entrada;
$status_clt = $Clt->status;

//$id_clt = $Clt->id_clt;

$calcFeriasObj->setIdClt($id_clt);


// calculo de ferias
$periodos_gozados = $calcFeriasObj->getPeriodosGozados();
$periodos_disponiveis = $calcFeriasObj->getPeriodoAquisitivo($data_entrada, $periodos_gozados);
$dadosFerias = $calcFeriasObj->getFeriasPorClt();

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lancamento", "ativo"=>"Lançamento de Férias");
$breadcrumb_pages = array("Gestão de RH"=>"../", "Férias"=>"../ferias/");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Férias</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <h3>Férias <small>Lançamento de Férias</small></h3>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-lg-3">
                    <ul class="nav nav-pills nav-stacked" role="tablist">
                        <li role="presentation" class="active li-step" id="li-step-1"><a class="rh" href="#">1 - Movimentos</a></li>
                        <li role="presentation" class="li-step" id="li-step-2"><a class="rh" href="#">2 - Período Aquisitivo</a></li>
                        <li role="presentation" class="li-step" id="li-step-3"><a class="rh" href="#">3 - Data de Início e Duração</a></li>
                        <li role="presentation" class="li-step" id="li-step-4"><a class="rh" href="#">4 - Resumo</a></li>
                    </ul>
                </div><!-- /.col-lg-3 -->
                <div class="col-lg-9">
                    <form action="#" method="post" role="form" id="form-lancamento">
                        <input type="hidden" name="home" id="home" value="" />
                        <h4>(<?= $row_clt['id_clt'] ?>) <?= $row_clt['nome'] ?></h4>
                        <input type="hidden" name="id_clt" id="id_clt" value="<?= $row_clt['id_clt'] ?>">
                        <input type="hidden" name="projeto" id="projeto" value="<?= $row_clt['id_projeto'] ?>">



                        <!-- step-1 - Movimentos -->
                        <div class="step" id="step-1">
                            <div class="panel panel-default">
                                <?php if ($status_clt != 10) { ?>
                                    <div class="panel-body">

                                        <div class="bs-callout bs-callout-danger">
                                            <h4 class="text-danger"><i class="fa fa-info-circle"></i> Atenção!</h4>
                                            <p class="text-danger">O CLT está em Evendo. Não pode ser cadastrado férias para este CLT.</p>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button id="cancel" class="btn btn-default cancel" type="button"><i class="fa fa-reply"></i> Volar</button>
                                    </div>
                                    <?php
                                    // Se não tem períodos disponiveis e não tem histórico de férias
                                } else if (count($periodos_disponiveis) == 0 and count($periodos_gozados) >= 0) {
                                    ?>
                                    <div class="panel-body">
                                        <div class="bs-callout bs-callout-info">
                                            <h4 class="text-info">ATENÇÃO!</h4>
                                            <p class="text-info">Funcionário(a) não possui período aquisitivo a férias.</p>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button id="cancel" class="btn btn-default cancel" type="button"><i class="fa fa-reply"></i> Volar</button>
                                    </div>
                                    <?php
                                    // Se não tem períodos disponiveis mas tem histórico de férias
                                } else {
                                    ?> 
                                    <div class="panel-body">
                                        <label>Já lançou os movimentos do funcionário neste mês?</label>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <button id="cancel" class="btn btn-default cancel" type="button">Cancelar</button>
                                        <button id="nao" class="btn btn-default" type="button">Não, inserir movimentos</button>
                                        <button id="sim" class="btn btn-default next" type="button" data-next-step="2">Sim, prosseguir</button> 
                                    </div>
                                <?php } ?>

                            </div>
                        </div><!-- /#step-1 --> 

                        <!-- step-2 - Período Aquisitivo -->
                        <div class="step hidden" id="step-2">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Período Aquisitivo:</label>
                                        <?php
                                        $count = 0;
                                        foreach ($periodos_disponiveis as $periodo) {
                                            ?>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="periodo_aquisitivo" id="periodo_aquisitivo<?= $count; ?>" value="<?= $periodo['inicio'] . '/' . $periodo['fim'] ?>" class="validate[required]">
                                                    <?php echo converteData($periodo['inicio'], 'd/m/Y') . ' à ' . converteData($periodo['fim'], 'd/m/Y'); ?>
                                                </label>
                                            </div>
                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="panel-footer text-right">
                                    <button class="btn btn-default back" type="button" data-back-step="1"><i class="fa fa-arrow-left"></i> Voltar</button>
                                    <button class="btn btn-default next" type="button" data-next-step="3">Prosseguir <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>

                        </div><!-- /#step-2 --> 

                        <!-- step-3 - Data de Início e Duração -->
                        <div class="step hidden" id="step-3">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Período Aquisitivo:</label>
                                        <p id="p_aquisitivo"></p>
                                    </div>
                                    <div class="form-group">
                                        <label>Data de Início das férias:</label>
                                        <div class="input-group">
                                            <input type="text" name="data_ini" id="data_ini" class="form-control data validate[required,custom[dateBr]]">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>

                                    <div class="alert alert-danger alerta-dobrada" role="alert">
                                        <i class="fa fa-warning"></i> <strong>Férias em dobro</strong> a partir de <strong class="ini-ferias-dobro"></strong>.
                                    </div>

                                    <div id="tem-faltas" class="form-group hidden">
                                        <label class="text-danger">
                                            <span id="qtd-faltas"></span> faltas no período. Desconsiderar Faltas?
                                        </label>
                                        <div class="radio">
                                            <label for="despreza_faltas-s"><input type="radio" name="despreza_faltas" value="1" id="despreza_faltas-s" disabled> Sim</label>
                                        </div>
                                        <div class="radio">
                                            <label for="despreza_faltas-n"><input type="radio" name="despreza_faltas" value="0" id="despreza_faltas-n" checked disabled> Não</label>
                                        </div>
                                    </div>                                    

                                    <div class="form-group">
                                        <label>Duração:</label>
                                        <div class="input-group">
                                            <select name="dias" id="dias" class="form-control validate[required]">
                                            </select>
                                            <div class="input-group-addon">dias</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer text-right">
                                    <button class="btn btn-default back" type="button" data-back-step="2"><i class="fa fa-arrow-left"></i> Voltar</button>
                                    <button class="btn btn-default next" type="button" data-next-step="4">Prosseguir <i class="fa fa-arrow-right"></i></button>
                                </div>
                                <input type="hidden" name="direito_dias" id="direito_dias" value="">
                                <input type="hidden" name="faltas" id="faltas" value="">
                                <input type="hidden" name="faltas_real" id="faltas_real" value="">
                                <input type="hidden" name="update_movimentos_clt" id="update_movimentos_clt" value="">
                            </div>
                        </div><!-- /#step-3 --> 

                        <!-- step-4 - Resumo -->
                        <div class="step hidden" id="step-4">
                            <div class="panel panel-default">
                                <div class="panel-body">

                                    <div class="alert alert-danger alerta-dobrada" role="alert">
                                        <i class="fa fa-warning"></i> <strong>Férias em dobro</strong>.
                                    </div>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center" id="tb-nome-clt">Locação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="width: 50%;" class="text-right text-bold">Projeto:</td>
                                                <td style="width: 50%;" id="tb-unidade"><?= "{$row_clt['id_projeto']} - {$row_clt['nome_projeto']}" ?></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 50%;" class="text-right text-bold">Unidade:</td>
                                                <td style="width: 50%;" id="tb-unidade"><?= "{$row_clt['id_unidade']} - {$row_clt['nome_unidade']}" ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">Atividade:</td>
                                                <td id="tb-atividade"><?= "{$row_clt['id_curso']} - {$row_clt['nome_curso']}" ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">Salário Contratual:</td>
                                                <td>
                                                    R$ <span id="tb-salario-contratual"><?= number_format($row_clt['salario'], 2, ',', '.') ?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="text-center">Resumo do Período de Férias</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td style="width: 50%;" class="text-right text-bold">Período Aquisitivo:</td>
                                                <td style="width: 50%;" id="tb-aquisitivo"></td>
                                            </tr>

                                            <tr class="hidden">
                                                <td class="text-right text-bold">Faltas no Per&iacute;odo:</td>
                                                <td><span id="tb-faltas"></span> dias</td>
                                            </tr>

                                            <tr>
                                                <td class="text-right text-bold">Período de Férias:</td>
                                                <td id="tb-periodo-ferias"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">Quantidade de Dias:</td>
                                                <td><span id="tb-dias"></span> dias</td>
                                            </tr>
                                            <tr class="tr-abono hidden">
                                                <td class="text-right text-bold">
                                                    Dias de Abono Pecuni&aacute;rio:
                                                </td>
                                                <td>
                                                    <span id="tb-dias-abono"></span> dias
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">Data de Retorno:</td>
                                                <td id="tb-retorno"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th colspan="4" class="text-center">Resumo do Pagamento de Férias</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="width: 25%;" class="text-right text-bold">Salário:</td>
                                                <td style="width: 25%;">
                                                    R$ <span id="tb-salario"></span>
                                                </td>
                                                <td style="width: 25%;" class="text-right text-bold">Salário Variável:</td>
                                                <td style="width: 25%;">
                                                    R$ <span id="tb-salario-variavel"></span> <a href="#" class="pull-right">Ver <i class="fa fa-external-link-square"></i></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">1/3 do Salário:</td>
                                                <td>
                                                    R$ <span id="tb-1-3-salario"></span>
                                                </td>
                                                <td class="text-right text-bold">Remuneração:</td>
                                                <td>
                                                    R$ <span id="tb-remuneracao"></span>
                                                </td>
                                            </tr>
                                            <tr class="tr-abono hidden">    
                                                <td class="text-right text-bold">
                                                    Abono Pecuni&aacute;rio:
                                                </td>
                                                <td>
                                                    R$ <span id="tb-pecuniario"></span>
                                                </td>
                                                <td class="text-right text-bold">
                                                    1/3 Abono Pecuni&aacute;rio:
                                                </td>
                                                <td>
                                                    R$ <span id="tb-1-3-pecuniario"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">INSS:</td>
                                                <td>
                                                    R$ <span id="tb-inss"></span>
                                                </td>
                                                <td class="text-right text-bold">IRRF:</td>
                                                <td>
                                                    R$ <span id="tb-irrf"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-right text-bold">Pensão Alimentícia:</td>
                                                <td>
                                                    R$ <span id="tb-pensao"></span>
                                                </td>
                                                <td class="text-right text-bold">Descontos:</td>
                                                <td>
                                                    R$ <span id="tb-desconto"></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-right text-bold">Liquido a Receber:</td>
                                                <td colspan="2" class="text-bold">R$ <span id="tb-liquido"></span></td>
                                            </tr>
                                        </tfoot>
                                    </table>


                                </div>
                                <div class="panel-footer text-right">
                                    <button class="btn btn-default back" type="button" data-back-step="3"><i class="fa fa-arrow-left"></i> Voltar</button>
                                    <button class="btn btn-primary" type="submit">Concluir</button>
                                </div>
                            </div>
                        </div><!-- /#step-4 --> 

                    </form>
                </div><!-- /.col-lg-9 -->
            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script src="../../js/jquery.maskedinput.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>

        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/ferias/ferias_lancamento.js"></script>
    </body>
</html>