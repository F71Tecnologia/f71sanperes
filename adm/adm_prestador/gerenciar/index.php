<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../../conn.php');
include('../../../wfunction.php');
include('funcoes.php');

$acao = isset($_POST['acao']) ? $_POST['acao'] : NULL;
switch ($acao) {
    case 'lancar_pagamentos':
        $id_prestador = isset($_POST['id']) ? $_POST['id'] : NULL;
        $valor = isset($_POST['valor_2']) ? str_replace('R$ ', '', str_replace(',', '.', $_POST['valor_2'])) : NULL;
        $data = isset($_POST['data_2']) ? $_POST['data_2'] : NULL;
        $data = implode('-', array_reverse(explode('/', $data)));
        $documento = isset($_POST['documento_2']) ? $_POST['documento_2'] : NULL;

        $sql = "INSERT INTO prestador_pg(`id_prestador`,`id_regiao`,id_saida`,`tipo`,`valor`,`data`,`documento`,`parcela`,`gerado`,`status_reg`,`comprovante`)" .
            " VALUES('$id_prestador',`id_regiao`,id_saida`,`tipo`,'$valor','$data','$documento',`parcela`,`gerado`,`status_reg`,`comprovante`)";
        echo $sql . "<br>";
        break;
    default:
        break;
}




$id_prestador = isset($_GET['id']) ? $_GET['id'] : NULL;
$prestador = getPrestador($id_prestador);

$pagamentos = getPagamentos($id_prestador);


/*
 * -----------------------------------------------------------------------------
 * provisionamento de pagamento de prestadores
 * por: Leonardo
 * em: 05/12/2016
 * -----------------------------------------------------------------------------
 */
$prest_periodo = getPeriodoContrato($id_prestador);
$arr_datas = createDateRangeArray($prest_periodo['contratado_em'], $prest_periodo['encerrado_em']);

$tot_contrato_previsto = 0;
$tot_pago_periodo = 0;
$tot_pago_periodo = 0;
$tot_a_pago_periodo = 0;
$tot_a_pagar_previsto = 0;

foreach ($arr_datas as $data) {
    $valor = (float) str_replace(',', '.', $prest_periodo['valor']);

    $mes = converteData($data, 'm');
    $ano = converteData($data, 'Y');
    $arr_pg = getPagametosByCompetencia($prest_periodo['id_prestador'], $mes, $ano);


    $tot_contrato_previsto += $valor;
    $tot_tespendido += $arr_pg['total']; // faz somatorio da dívida do mes
    $tot_pago_periodo += $arr_pg['total_pago']; // faz somatorio do que foi pago
    $tot_a_pago_periodo += $arr_pg['total_a_pagar']; // faz somatorio do que não foi pago
    $tot_a_pagar_previsto += $valor + $arr_pg['total_a_pagar'] - $arr_pg['total_pago']; // valores não pagos são provisionados


    // seta status como nao havendo saida para a competencia
    $status = "Sem pagamentos gerados para a Competência";
    $class = "";
    if ($arr_pg['qtd_saidas'] > 0) { // se query retorna qtd saida maior que 0
        $status = "Não Pago"; // seta como nao pago como default
        $class = "danger";
        if ($arr_pg['total_pago'] > 0 && $arr_pg['total_a_pagar'] == 0) { // se foi pago e não está devendo nada
            $status = "Pago"; // seta como pago
            $class = "success";
        } elseif ($arr_pg['total_pago'] > 0 && $arr_pg['total_a_pagar'] > 0) { // se foi pago mas ainda falta saidas para pagar (impostos)
            $status = "Parcialmente Pago"; // seta como parcialmente pago
            $class = "warning";
        }
    }


    $arr_tabela[] = array(
        'competencia' => ConverteData($data, 'm/Y'),
        'valor_contrato' => number_format($prest_periodo['valor'], 2, ',', '.'),
        'valor_tespendido' => number_format($arr_pg['total'], 2, ',', '.'),
        'valor_pago' => number_format($arr_pg['total_pago'], 2, ',', '.'),
        'valor_a_pagar' => number_format($arr_pg['total_a_pagar'], 2, ',', '.'),
        'provisao_acumulada' => number_format($tot_a_pagar_previsto, 2, ',', '.'),
        'status' => $status,
        'class' => $class,
        'pg' => $arr_pg['qtd_saidas']
    );
}
// -----------------------------------------------------------------------------

$impostoRetido = getImpostoRetido($id_prestador);

$usuario = carregaUsuario();
$usuario['id_projeto'] = $usuario['id_regiao']; // from hell!!!

$arr_paginas = array('Gerenciamento de Processo', 'Contrato e Anexos', 'Pagamentos', 'Imposto Retido');
$regioes = getRegioesFuncionario();
$meses = mesesArray();
$anos = array('2014' => '2014', '2015' => '2015', '2016' => '2016'); //$anos = anosArray();
$aba = isset($_REQUEST['abashow']) ? $_REQUEST['abashow'] : '0';
?>
<html>
<head>
    <title>:: Intranet :: GERENCIAR PRESTADOR DE SERVIÇO</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link href="../../favicon.ico" rel="shortcut icon"/>
    <link href="../../../net1.css" rel="stylesheet" type="text/css" />
    <link href="../../../favicon.ico" rel="shortcut icon" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="../../../resources/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
    <link href="../../../resources/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <script src="../../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../../../resources/js/bootstrap.js" type="text/javascript"></script>
    <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
    <script src="../../../resources/js/main.js"></script>
    <!--<script src="js/jquery.maskedinput.js" type="text/javascript"></script>-->
    <script type="text/javascript" src="../../../js/jquery.price_format.2.0.min.js"></script>
    <script src="../../../js/global.js" type="text/javascript"></script>

    <!--<link rel="stylesheet" type="text/css" href="../../../novoFinanceiro/style/form.css"/>-->
    <!--<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>-->
    <link rel="stylesheet" type="text/css" href="../../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>
    <script type="text/javascript" src="../../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
    <script>
        function form2() {
            $('#form_acao').val('lancar_pagamentos');
            $('#page_controller').submit();
        }
        $(document).ready(function(){
            $("#tb_provisionamento").hide();
            $("#tb_historico").hide();
            $("#btn_provisionamento").click(function(){
                $("#tb_provisionamento").slideDown(1000);
                $("#tb_historico").slideUp(1000);
            });
            $("#btn_historico").click(function(){
                $("#tb_historico").slideDown(1000);
                $("#tb_provisionamento").slideUp(1000);
            });
        });
    </script>

    <style>
        .text-right{
            text-align: right !important;
        }
        .success{
            background-color: #dff0d8 !important;
            color: #3c763d !important;
        }
        .danger{
            background-color: #f2dede !important;
            color: #a94442 !important;
        }
        .warning{
            background-color: #fcf8e3 !important;
            color: #8a6d3b !important;
        }
    </style>

    <!--<script src="js/vale_alimentacao.js" type="text/javascript"></script>-->
</head>
<body class="novaintra" data-type="adm">
<form method="post" id="page_controller">
    <input type="hidden" name="abashow" value="<?= $aba; ?>" id="abashow" />
    <input type="hidden" name="acao" value="" id="form_acao" />
    <input type="hidden"  name="id" value="<?= $id_prestador; ?>"/>
    <div id="content">
        <div id="geral">
            <div id="topo">
                <div class="conteudoTopo">
                    <div class="imgTopo">
                        <img src="../../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    </div>
                    <h2>Gerenciar Prestador de Serviço</h2>
                </div>
            </div>

            <div id="conteudo">
                <div class="colEsq">
                    <div class="titleEsq">Menu</div>
                    <ul>
                        <?php foreach ($arr_paginas as $key => $pagina) { ?>
                            <li><a href="javascript:;" onclick="$('#abashow').val(<?= $key ?>)" data-item="<?= $key ?>" class="bt-menu <?= ($pagina_ativa == $key) ? ' aselected ' : ''; ?>"><?= $pagina; ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="colDir" id="teste1">
                    <div>processando os dados...</div>
                    <div style="background: url(../../imagens/carregando/loading.gif) no-repeat; width: 220px; height:19px;"></div>
                    <?php foreach ($arr_paginas as $key => $value) { ?>
                        <div id="item<?= $key ?>" style="display: none;" >
                            <?php
                            $file = 'includes/item_' . $key . '.php';
                            if (is_file($file)) {
                                include_once $file;
                            } else {
                                echo 'Erro 404. Página não encontrada!';
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</form>
</body>
</html>