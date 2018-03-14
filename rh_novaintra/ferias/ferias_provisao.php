<?php
session_start();

if (!isset($_COOKIE['logado'])){
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
//include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/CalculoFolhaClass.php');
include('../../classes/FeriasProvisaoClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];

/**
* OBJETO FOLHA
*/
$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));

$objProvisaoFerias = new FeriasProvisao();

$container_full = true;

$filtro = false;


/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])){
    $projetoR = $_REQUEST['projeto'];
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['projeto']) && isset($_SESSION['regiao'])){
    $projetoR = $_SESSION['projeto'];
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['projeto_select']) && isset($_SESSION['regiao_select'])){
    $projetoR = $_SESSION['projeto_select'];
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Provisão de Férias");
$breadcrumb_pages = array("Gestão de RH"=>"../../rh/principalrh.php");
$query = mysql_query("select * from regioes where status = 1");

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso']))){
    $filtro = true;

    $dados_clt = $objProvisaoFerias->getClts($id_regiao, $objCalcFolha);
    $clts = $dados_clt['clts'];
    $stringIds = $dados_clt['stringIds'];
    $totalClts = $dados_clt['total'];

    $dadosPeriodo = $objProvisaoFerias->getPeriodosAquisitivos($stringIds, $clts);


    $regiaoR = $id_regiao;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Provisão de Férias</title>
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
        <link href="../../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Provisão de Férias</small></h2></div>
                </div>
            </div>
            <!--resposta de algum metodo realizado-->
            <?php
            if (!empty($_SESSION['MESSAGE'])){
                ?>
                <div id="message-box" class="alert alert-dismissable alert-warning <?= $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?=
                    $_SESSION['MESSAGE'];
                    session_destroy();
                    ?>
                </div>
            <?php } ?>
            <form id="form1" class="form-horizontal" method="post" action="ferias_provisao.php">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-xs-1">Região:</label>
                            <div class="col-xs-5">
                                <select id="região" name="regiao" class="form-control">
                                    <?php
                                        while($row = mysql_fetch_assoc($query)){
                                            echo "<option value='{$row["id_regiao"]}' ". ($row["id_regiao"] = $id_regiao ? "selected" : "") .">" . $row["id_regiao"] . " - " .$row["regiao"]."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?= $projetoR ?>" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?= $regiaoR ?>" />
                        <input type="hidden" name="unidade" id="unidade" value="" />
                        <input type="hidden" name="home" id="home" value="" />
                        <button type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" onclick="" />Filtrar</button>
                    </div>
                </div>
            </form>
            <?php
            if ($filtro){
                if ($totalClts > 0){
                    ?>
                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
                    <br><br>
                    <table id="tbRelatorio" class="table table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <!--<th>#</th>-->
                                <!--<th>Região</th>-->
                                <th>Matricula</th>
                                <th>Nome</th>
                                <!--<th class="bg-danger">DATA ENTRADA</th>-->
                                <th>Ini Periodo Aq.</th>
                                <th>Fim Periodo Aq.</th>
                                <th>Dt Hoje</th>
                                <th>Férias Venc.</th>
                                <th>Férias Dob.</th>
                                <th>Ferias Prop.</th>
                                <th>Salário</th>
                                <th>Movimentações de Adicionais e Variáveis</th>
                                <th>Base de Calculo para Férias</th>
                                <th>Provisáo de Ferias + 1/3 ACM. MES ANT.</th>
                                <th>Prov.INSS Ferias (27,8) ACM.MES ANT</th>
                                <th>FGTS S/Férias ACM.MES ANT</th>
                                <th>Provisao Total do Mës - MES ANT</th>
                                <th>Provisáo de Ferias + 1/3 - ACM MES</th>
                                <th>Prov.INSS Ferias (27,8) - ACM MES</th>
                                <th>FGTS S/Férias - ACM MES</th>
                                <th>Provisao Total do Mës - ACM</th>
                                <th>Provisáo de Ferias + 1/3 - MES</th>
                                <th>Prov.INSS Ferias (27,5) - MES</th>
                                <th>FGTS S/Férias - MES</th>
                                <th>Provisao Total do Mës</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($clts as $clt){
                                $tot = 0;

    //                            print_array($dadosPeriodo[$clt['id_clt']]);

                                $ultimo_periodo = count($dadosPeriodo[$clt['id_clt']]['periodos']);
                                $dadosUltimoPeriodo = $dadosPeriodo[$clt['id_clt']]['periodos'][$ultimo_periodo];

                                $salario = $clt['salario'];

                                foreach ($dadosPeriodo[$clt['id_clt']]['periodos'] as $periodos) {
                                    $tot++;

    //                                print_array($dadosPeriodo);

                                    $data_aquisitivo_inicio = converteData($periodos['data_aquisitivo_ini'], "d/m/Y");
    //                                $data_aquisitivo_final = date('d/m/Y', strtotime($periodos['data_aquisitivo_fim']. ' + 1 days'));
                                    $data_aquisitivo_final = converteData($periodos['data_aquisitivo_fim'], "d/m/Y");
                                    $avos_ferias_vencidas = ($periodos['vencido']) ? 12 : 0;
                                    $avos_ferias_dobro = ($periodos['em_dobro']) ? 12 : 0;
                                    $data_hj = date('Y-m-d');
                                    $data_hjF = date('d/m/Y');
                                    $data_entrada = converteData($clt['data_entrada'], "d/m/Y");

                                    if($tot == $ultimo_periodo){
                                        $avos_ferias_proporcional = $objProvisaoFerias->Calc_qnt_meses_13_ferias($periodos['data_aquisitivo_ini'], $data_hj, NULL, $clt['data_entrada'], $data_hj);

                                        $lista_movimentos = $objProvisaoFerias->calcBase($clt, $periodos['data_aquisitivo_ini'], $data_hj);
                                        $movimentos = $lista_movimentos['valor_total'];
    //                                    $valor_base = ($movimentos / $avos_ferias_proporcional) + $salario;
                                        $valor_base = ($movimentos + $salario);
                                        $legenda_valor_base = formataMoeda($movimentos, 1)." + ".formataMoeda($salario, 1);
                                    }else{
                                        $avos_ferias_proporcional = 0;

                                        $lista_movimentos = $objProvisaoFerias->calcBase($clt, $periodos['data_aquisitivo_ini'], $periodos['data_aquisitivo_fim']);
                                        $movimentos = $lista_movimentos['valor_total'];
    //                                    $valor_base = ($movimentos / $avos_ferias_vencidas) + $salario;
                                        $valor_base = ($movimentos + $salario);
                                        $legenda_valor_base = formataMoeda($movimentos, 1)." + ".formataMoeda($salario, 1);
                                    }

    //                                print_array($lista_movimentos);

                                    if($avos_ferias_proporcional < 0){
                                        $avos_ferias_proporcional = 0;
                                    }

                                    //Provisáo de Ferias + 1/3 ACM. MES ANT
                                    $prov_ferias_mais_umterco_mes_anterior = (($valor_base / 12) * ($avos_ferias_vencidas + $avos_ferias_proporcional - 1) * 1.3333);
                                    $legenda_prov_ferias_mais_umterco_mes_anterior = "((".formataMoeda($valor_base, 1)." / 12) * ({$avos_ferias_vencidas} + {$avos_ferias_proporcional} - 1) * 1.3333)";

                                    //Prov.INSS Ferias (27,8) ACM.MES ANT
                                    $prov_inss_ferias_mes_anterior = $prov_ferias_mais_umterco_mes_anterior * 0.278;
                                    $legenda_prov_inss_ferias_mes_anterior = formataMoeda($prov_ferias_mais_umterco_mes_anterior, 1)." * 0.278";

                                    //FGTS S/Férias ACM.MES ANT
                                    $fgts_sem_ferias_mes_anterior = $prov_ferias_mais_umterco_mes_anterior * 0.08;
                                    $legenda_fgts_sem_ferias_mes_anterior = formataMoeda($prov_ferias_mais_umterco_mes_anterior, 1)." * 0.08";

                                    //Provisao Total do Mës - MES ANT
                                    $provisao_total_mes_anterior = ($prov_ferias_mais_umterco_mes_anterior + $prov_inss_ferias_mes_anterior + $fgts_sem_ferias_mes_anterior);
                                    $legenda_provisao_total_mes_anterior = "(".formataMoeda($prov_ferias_mais_umterco_mes_anterior, 1)." + ".formataMoeda($prov_inss_ferias_mes_anterior, 1)." + ".formataMoeda($fgts_sem_ferias_mes_anterior, 1).")";

                                    //Provisáo de Ferias + 1/3 - ACM MES
                                    $prov_ferias_mais_umterco_mes = (($valor_base / 12) * ($avos_ferias_vencidas + $avos_ferias_proporcional) * 1.3333);
                                    $legenda_prov_ferias_mais_umterco_mes = "((".formataMoeda($valor_base, 1)." / 12) * (".$avos_ferias_vencidas." + ".$avos_ferias_proporcional.") * 1.3333)";

                                    //Prov.INSS Ferias (27,8) - ACM MES
                                    $prov_inss_ferias_mes = $prov_ferias_mais_umterco_mes * 0.278;
                                    $legenda_prov_inss_ferias_mes = formataMoeda($prov_ferias_mais_umterco_mes, 1)." * 0.278";

                                    //FGTS S/Férias - ACM MES
                                    $fgts_sem_ferias_mes = $prov_ferias_mais_umterco_mes * 0.08;
                                    $legenda_fgts_sem_ferias_mes = formataMoeda($prov_ferias_mais_umterco_mes, 1)." * 0.08";

                                    //Provisao Total do Mës - ACM
                                    $provisao_total_mes = ($prov_ferias_mais_umterco_mes + $prov_inss_ferias_mes + $fgts_sem_ferias_mes);
                                    $legenda_provisao_total_mes = "(".formataMoeda($prov_ferias_mais_umterco_mes, 1)." + ".formataMoeda($prov_inss_ferias_mes, 1)." + ".formataMoeda($fgts_sem_ferias_mes, 1).")";

                                    //Provisáo de Ferias + 1/3 - MES
                                    $provisao_ferias_mais_umterco = ($prov_ferias_mais_umterco_mes - $prov_ferias_mais_umterco_mes_anterior);
                                    $legenda_provisao_ferias_mais_umterco = "(".formataMoeda($prov_ferias_mais_umterco_mes, 1)." - ".formataMoeda($prov_ferias_mais_umterco_mes_anterior, 1).")";

                                    //Prov.INSS Ferias (27,5) - MES
                                    $prov_inss_ferias = ($prov_inss_ferias_mes - $prov_inss_ferias_mes_anterior);
                                    $legenda_prov_inss_ferias = "(".formataMoeda($prov_inss_ferias_mes, 1)." - ".formataMoeda($prov_inss_ferias_mes_anterior, 1).")";

                                    //FGTS S/Férias - MES
                                    $fgts_sem_ferias = ($fgts_sem_ferias_mes - $fgts_sem_ferias_mes_anterior);
                                    $legenda_fgts_sem_ferias = "(".formataMoeda($fgts_sem_ferias_mes, 1)." - ".formataMoeda($fgts_sem_ferias_mes_anterior, 1).")";

                                    //Provisao Total do Mës
                                    $prov_total_mes = ($provisao_ferias_mais_umterco + $prov_inss_ferias + $fgts_sem_ferias);
                                    $legenda_prov_total_mes = "(".formataMoeda($provisao_ferias_mais_umterco, 1)." + ".formataMoeda($prov_inss_ferias, 1)." + ".formataMoeda($fgts_sem_ferias, 1).")";
                            ?>
                            <tr>
                                <!--<td><?php echo $clt['id_clt']; ?></td>-->
                                <!--<td><?php echo $clt['id_regiao']; ?></td>-->
                                <td><?php echo $clt['matricula']; ?></td>
                                <td><?php echo $clt['nome']; ?></td>
                                <!--<td><?php echo $data_entrada; ?></td>-->
                                <td><?php echo $data_aquisitivo_inicio; ?></td>
                                <td><?php echo $data_aquisitivo_final; ?></td>
                                <td><?php echo $data_hjF; ?></td>
                                <td><?php echo $avos_ferias_vencidas; ?></td>
                                <td><?php echo $avos_ferias_dobro; ?></td>
                                <td><?php echo $avos_ferias_proporcional; ?></td>
                                <td><?php echo formataMoeda($salario, 1); ?></td>
                                <td style="font-size: 10px">
                                    <?php
                                    foreach ($lista_movimentos['movimentos'] as $mov => $valor){
                                        if($valor > 0){
                                            echo $mov.": ".formataMoeda($valor, 1)."<br>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td title="<?php echo $legenda_valor_base; ?>"><?php echo formataMoeda($valor_base, 1); ?></td>
                                <td title="<?php echo $legenda_prov_ferias_mais_umterco_mes_anterior; ?>"><?php echo formataMoeda($prov_ferias_mais_umterco_mes_anterior, 1); ?></td>
                                <td title="<?php echo $legenda_prov_inss_ferias_mes_anterior; ?>"><?php echo formataMoeda($prov_inss_ferias_mes_anterior, 1); ?></td>
                                <td title="<?php echo $legenda_fgts_sem_ferias_mes_anterior; ?>"><?php echo formataMoeda($fgts_sem_ferias_mes_anterior, 1); ?></td>
                                <td title="<?php echo $legenda_provisao_total_mes_anterior; ?>"><?php echo formataMoeda($provisao_total_mes_anterior, 1); ?></td>
                                <td title="<?php echo $legenda_prov_ferias_mais_umterco_mes; ?>"><?php echo formataMoeda($prov_ferias_mais_umterco_mes, 1); ?></td>
                                <td title="<?php echo $legenda_prov_inss_ferias_mes; ?>"><?php echo formataMoeda($prov_inss_ferias_mes, 1); ?></td>
                                <td title="<?php echo $legenda_fgts_sem_ferias_mes; ?>"><?php echo formataMoeda($fgts_sem_ferias_mes, 1); ?></td>
                                <td title="<?php echo $legenda_provisao_total_mes; ?>"><?php echo formataMoeda($provisao_total_mes, 1); ?></td>
                                <td title="<?php echo $legenda_provisao_ferias_mais_umterco; ?>"><?php echo formataMoeda($provisao_ferias_mais_umterco, 1); ?></td>
                                <td title="<?php echo $legenda_prov_inss_ferias ?>"><?php echo formataMoeda($prov_inss_ferias, 1); ?></td>
                                <td title="<?php echo $legenda_fgts_sem_ferias ?>"><?php echo formataMoeda($fgts_sem_ferias, 1); ?></td>
                                <td title="<?php echo $legenda_prov_total_mes ?>"><?php echo formataMoeda($prov_total_mes, 1); ?></td>
                            </tr>
                            <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                    <?php
                } else{
                    ?>
                    <div class="col-xs-12">
                        <div class="alert alert-dismissable alert-warning">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>Nenhum registro encontrado!</strong>
                        </div>
                    </div>
                    <br><br>
                    <?php
                }
            }
            ?>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
                $(function () {
//                    $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                    $(".bt-image").on("click", function () {
                        var action = $(this).data("type");
                        var key = $(this).data("key");
                        var emp = $(this).parents("tr").find("td:first").next().html();
                        var clt = $(this).data("clt");

                        if (action === "visualizar") {
                            $("#unidade").val(key);
                            $("#form1").attr('action', 'detalhes_unidade.php');
                            $("#form1").submit();
                        } else if (action === "editar") {
                            $("#unidade").val(key);
                            $("#form1").attr('action', 'form_unidade.php');
                            $("#form1").submit();
                        } else if (action === "excluir") {

                            if (clt != 0) {
                                bootAlert("Unidade não pode ser excluida, pois existe CLT vinculada a mesma", "Exclusão de Unidade", null, 'danger');
                            } else {
                                bootConfirm("Você deseja realmente excluir esta unidade?", "Exclusão de Unidade", function (data) {
                                    if (data) {
                                        if (data == true) {
                                            $("#" + key).remove();
                                            $.ajax({
                                                url: "del_unidade.php?id=" + key
                                            });
                                        }
                                    }
                                }, 'warning');
                            }
                        }
                    });

                    $("#novaUnidade").click(function () {
                        $("#form1").attr('action', 'form_unidade.php');
                        $("#form1").submit();
                    });
                });
        </script>
    </body>
</html>