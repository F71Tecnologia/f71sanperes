<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();

//Pegando o id_regiao da pessoa logada
$funcionarios = new funcionario();
$regiao_selecionada = $funcionarios->id_regiao;

$arrProj = array('all' => "<< TODOS OS PROJETOS >>");
foreach ($optRegiao as $k => $proj) {
    $arrProj[$k] = $proj;
}
unset($arrProj['-1']);

//$ano = array("2013" => 2013, "2014" => 2014);
$ano = anosArray(null, null, array('' => "<< Ano >>"));

//Recebe o array para montar uma input select dos meses
$mes2 = mesesArray();
//SINDICATOS
$indice_sindicato = array();
$query_sindicato = "SELECT * FROM rhsindicato";
$sql = mysql_query($query_sindicato) or die("erro ao selecionar sindicato");
$indice_sindicato[9999] = "-- TODOS --";
while ($row_sindicato = mysql_fetch_assoc($sql)) {
    $indice_sindicato[$row_sindicato['id_sindicato']] = $row_sindicato['nome'];
}
//Recebe todos os nomes dos sindicatos para colocar numa input select
$sindicatoSelect = $indice_sindicato;

//CARGOS
$indice_cargo = array();
$query_cargo = "SELECT * FROM curso";
$sql_cargo = mysql_query($query_cargo) or die("erro ao selecionar cargo ou função");
while ($row_cargo = mysql_fetch_assoc($sql_cargo)) {
    $indice_cargo[$row_cargo['id_curso']] = $row_cargo['nome'];
}

$addsql = "";
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
if ($mesSel < 10 && $mesSel > 0) {
    $mesSel = "0" . $mesSel;
    $addsql .= " AND C.mes ='{$mesSel}'";
}

if ($anoSel != "") {
    $addsql .= " AND C.ano = '{$anoSel}'";
}

if ($projetoSel > 0) {
    $addsql .= " AND C.id_projeto = '{$projetoSel}'";
}


$sindSel = (isset($_REQUEST['sindicato'])) ? $_REQUEST['sindicato'] : null;
if (isset($_REQUEST['gerar'])) {

//    $sql = "SELECT B.id_clt, B.nome, C.nome AS nome_curso, C.id_curso, D.id_sindicato, A.sallimpo, A.a5019, A.mes, A.ano, E.valor_movimento, if(A.a5019 = '0.00' && E.valor_movimento IS NULL, 'n','s') AS contribuinte
//                FROM rh_folha_proc AS A
//                LEFT JOIN rh_clt AS B ON(B.id_clt = A.id_clt)
//                LEFT JOIN curso AS C ON(C.id_curso = B.id_curso)
//                LEFT JOIN rhsindicato AS D ON(B.rh_sindicato = D.id_sindicato)
//                LEFT JOIN (SELECT valor_movimento,id_clt,cod_movimento,mes_mov FROM rh_movimentos_clt WHERE cod_movimento = 5019) AS E ON(B.id_clt = E.id_clt AND E.mes_mov = A.mes)
//                WHERE A.status = 3 AND D.id_sindicato = '{$sindSel}' {$addsql} AND A.status_clt != 67
//                ORDER BY contribuinte, A.mes, B.rh_sindicato, B.nome";

    echo $sql = "SELECT B.id_clt, B.nome, D.nome nome_curso, D.id_curso, B.rh_sindicato, C.sallimpo, C.a5019, C.mes, C.ano, A.valor_movimento, IF(C.a5019 = '0.00' && A.valor_movimento IS NULL, 'n','s') AS contribuinte
                    FROM rh_movimentos_clt AS A 
                    LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                    LEFT JOIN rh_folha_proc AS C ON (A.id_clt = C.id_clt AND C.mes = 03 AND C.ano = 2017)
                    LEFT JOIN curso AS D ON (B.id_curso = D.id_curso)
                    WHERE A.cod_movimento = 5019 AND rh_sindicato = $sindSel $addsql AND A.`status` = 5";

    echo "<!-- $sql -->";
    $qr = mysql_query($sql);
    $sindicato = array();
    while ($rows = mysql_fetch_assoc($qr)) {
        if ($rows['contribuinte'] == "s") {
            $id_sindicato_val = $rows['id_sindicato'];
        } else {
            $id_sindicato_val = $rows['id_sindicato'];
        }

        $sindicato[$rows['contribuinte']][$rows['mes']][$id_sindicato_val][$rows['id_clt']]['nome'] = $rows['nome_sindicato'];
        $sindicato[$rows['contribuinte']][$rows['mes']][$id_sindicato_val][$rows['id_clt']]['nome_clt'] = $rows['nome'];
        $sindicato[$rows['contribuinte']][$rows['mes']][$id_sindicato_val][$rows['id_clt']]['nome_curso'] = $rows['nome_curso'];
        $sindicato[$rows['contribuinte']][$rows['mes']][$id_sindicato_val][$rows['id_clt']]['salario_base'] = $rows['sallimpo'];
        $sindicato[$rows['contribuinte']][$rows['mes']][$id_sindicato_val][$rows['id_clt']]['contribuicao'] = ($rows['a5019'] != "0.00" ? $rows['a5019'] : $rows['valor_movimento']);
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Contribuição Sindical</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


        <script>
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

            });
        </script>

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Contribuição Sindical </h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <input type="hidden" name="hide_sindicato" id="hide_sindicato" value="<?php echo $sindSel ?>" />

                        <div class="form-group" >

                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Compet&ecirc;ncia:</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($mes2, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>

                            <div class="col-sm-3">
                                <?php echo montaSelect($ano, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Sindicato:</label>
                            <div class="col-sm-9">
                                <?php echo montaSelect($sindicatoSelect, $sindSel, array('name' => "sindicato", 'id' => 'sindicato', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <button type="submit" name="gerar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 


                <?php if (!empty($sindicato) && isset($_REQUEST['gerar'])) { ?>
                    <?php $i = 1; ?>
                    <table class="table table-striped table-hover text-sm valign-middle table-bordered" id="tbRelatorio">
                        <?php foreach ($sindicato["s"] as $mes => $sindicatos) { ?>

                            <thead>
                                <?php if ($i == 1) { ?>
                                    <tr>
                                        <th colspan="6" style="background: #0078FF; color: #fff;">RELATÓRIO DE CONTRIBUINTES SINDICAIS</th>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th colspan="6" style="background: #ccc; text-align: left; "><?php echo mesesArray($mes) . "/" . $anoSel; ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $sindic = ""; ?>
                                <?php foreach ($sindicatos as $id_sindicato => $id_curso) { ?>
                                    <?php if ($id_sindicato != $sindic) { ?>
                                        <?php $sindic = $id_sindicato; ?>
                                        <tr>
                                            <td colspan="6" style="background: #ccc; text-align: left; padding-left: 30px; font-weight: bold;"> » <?php echo $indice_sindicato[$id_sindicato]; ?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php $curso_id = ""; ?>    
                                    <tr>
                                        <td style="text-align: left;  font-weight: bold; padding-left: 90px;">Nome</td>
                                        <td style="text-align: left;  font-weight: bold; padding-left: 90px;">Cargo</td>
                                        <td style="text-align: left;  font-weight: bold;">Salário Base</td>
                                        <td style="text-align: left;  font-weight: bold;">Contribuição</td>
                                    </tr>    
                                    <?php foreach ($id_curso as $id_cur => $dados) { ?>
                                        <tr>
                                            <td style="background: #fff; text-align: left; padding-left: 90px;"><?php echo $dados["nome_clt"]; ?></td>
                                            <td style="background: #fff; text-align: left; padding-left: 90px;"><?php echo $dados["nome_curso"]; ?></td>
                                            <td style="background: #fff; text-align: left;">R$ <?php
                                                echo number_format($dados["salario_base"], '2', ',', '.');
                                                $total_salario += $dados["salario_base"];
                                                ?></td>
                                            <td style="background: #fff; text-align: left;">R$ <?php
                                                echo number_format($dados["contribuicao"], '2', ',', '.');
                                                $total_contribuicao += $dados["contribuicao"];
                                                ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                            <?php $i++; ?>
                        <?php } ?>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                            </tr>    
                            <tr style="background: #666">
                                <td colspan="2" style="text-align: right; font-weight: bold; color: #fff">TOTAL: </td>
                                <td style="font-weight: bold; padding-left: 90px; color: #fff">R$ <?php echo number_format($total_salario, 2, ',', '.'); ?></td>
                                <td style="font-weight: bold; padding-left: 90px; color: #fff">R$ <?php echo number_format($total_contribuicao, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>

                    <?php
                    $i = 1;
                    $total_salario = 0;
                    $total_contribuicao = 0;
                    ?>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto; margin-top: 50px;"> 
                        <?php foreach ($sindicato["n"] as $mes => $sindicatos) { ?>
                            <thead>
                                <?php if ($i == 1) { ?>
                                    <tr>
                                        <th colspan="6" style="background: red; color: #fff;">RELATÓRIO DE NÃO CONTRIBUINTES SINDICAIS</th>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th colspan="6" style="background: #ccc; text-align: left;  "><?php echo mesesArray($mes) . "/" . $anoSel; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sindic = ""; ?>
                                <?php foreach ($sindicatos as $id_sindicato => $id_curso) { ?>
                                    <?php if ($id_sindicato != $sindic) { ?>
                                        <?php $sindic = $id_sindicato; ?>
                                        <tr>
                                            <td colspan="6" style="background: #f8f8f8; text-align: left; padding-left: 30px; font-weight: bold;"> » <?php echo $indice_sindicato[$id_sindicato]; ?></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;  font-weight: bold; padding-left: 90px;">Nome</td>
                                            <td style="text-align: left;  font-weight: bold; padding-left: 90px;">Cargo</td>
                                            <td style="text-align: left;  font-weight: bold;">Salário Base</td>
                                            <td style="text-align: left;  font-weight: bold;">Contribuição</td>
                                        </tr>    
                                    <?php } ?>
                                    <?php $curso_id = ""; ?>    
                                    <?php foreach ($id_curso as $id_cur => $curso) { ?>
                                        <?php if ($id_cur != $curso_id) { ?>
                                            <?php $curso_id = $id_cur; ?>
                                                                            <!--<tr>
                                                                                <td colspan="6" style="background: #f1f1f1; text-align: left; padding-left: 60px; font-weight: bold;"> » <?php //echo $indice_cargo[$id_cur];       ?></td>
                                                                            </tr>-->
                                            <?php
                                            $contforeach = 1;
                                        }
                                        ?>
                                        <?php
                                        foreach ($curso as $k => $dados) {
                                            switch ($contforeach) {
                                                case 1: $contforeach++;
                                                    break;

                                                case 2: echo '<tr>
                                            <td style="background: #fff; text-align: left; padding-left: 90px;">' . $dados . '</td>';
                                                    $contforeach++;
                                                    break;

                                                case 3: echo '<td style="background: #fff; text-align: left; padding-left: 90px;">' . $dados . '</td>';
                                                    $contforeach++;
                                                    break;

                                                case 4: echo '<td style="background: #fff; text-align: left; padding-left: 90px;">R$ ' . number_format($dados, '2', ',', '.') . '</td>';
                                                    $total_salario += $dados;
                                                    $contforeach++;
                                                    break;

                                                case 5: echo '<td style="background: #fff; text-align: left; padding-left: 90px;">R$ ' . number_format($dados, '2', ',', '.') . '</td></tr>';
                                                    $total_contribuicao += $dados;
                                                    $contforeach++;
                                                    break;
                                            }
                                            ?>    

                                                                                                                                            <!--     <tr>
                                                                                                                                                <td style="background: #fff; text-align: left; padding-left: 90px;"><?php //echo $dados;       ?></td>
                                                                                                                                                <!--<td style="background: #fff; text-align: left; padding-left: 90px;">R$ <?php
                                            //echo number_format($dados["salario_base"], '2', ',', '.');
                                            //$total_salario += $dados["salario_base"]; 
                                            ?></td>
                                                                                                                                                <td style="background: #fff; text-align: left; padding-left: 90px;">R$ <?php
                                            //echo number_format($dados["contribuicao"], '2', ',', '.');
                                            //$total_contribuicao += $dados["contribuicao"]; 
                                            ?></td>
                                                                                                                                            </tr>-->
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                            <?php $i++; ?>
                        <?php } ?>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                            </tr>    
                            <tr style="background: #666">
                                <td colspan="2" style="text-align: right; font-weight: bold; color: #fff">TOTAL: </td>
                                <td style="font-weight: bold; padding-left: 90px; color: #fff">R$ <?php echo number_format($total_salario, 2, ',', '.'); ?></td>
                                <td style="font-weight: bold; padding-left: 90px; color: #fff">R$ <?php echo number_format($total_contribuicao, 2, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>  

            </form>

            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>

        <script>
            $(function () {
                $(".bt-image").on("click", function () {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaSindicatos"}, null, "sindicato");
            });
        </script>
    </body>
</html>
<!-- (A) -->