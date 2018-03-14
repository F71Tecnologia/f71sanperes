<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
error_reporting(E_ALL);

include('../conn.php');
include("../wfunction.php");
include('../classes/global.php');
include('../classes/FolhaClass.php');

$objFolha = new Folha();
$lista = false;
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//VERIFICA INFORMAÇÃO DE POST
if (validate($_REQUEST['filtrar'])) {
    $lista = true;
    $projeto = $_REQUEST['projeto'];
    $dataIni = $_REQUEST['dataIni'];
    $dataFim = $_REQUEST['dataFim'];
    $dataIni = (!empty($dataIni)) ? ConverteData($dataIni) : '';
    $dataFim = (!empty($dataFim)) ? ConverteData($dataFim) : '';

    if ($projeto == "-1") {
        $condProjeto = "AND p.id_regiao  = '{$usuario['id_regiao']}'";
    } else {
        $condProjeto = "AND p.id_projeto  = '{$projeto}'";
        $regiaoR = $projeto;
    }

    $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome, f.data_inicio, f.total_liqui, f.clts, f.total_limpo
              FROM rh_folha AS f
              INNER JOIN projeto p ON f.projeto = p.id_projeto
              WHERE (f.status = '3' OR f.status = '2') AND p.id_master = {$usuario['id_master']} AND  f.data_inicio BETWEEN '$dataIni' AND '$dataFim' AND p.id_regiao != 36 $condProjeto
              ORDER BY f.regiao, f.projeto, f.data_inicio";

    $result = mysql_query($query);
}
$breadcrumb_config = array("nivel" => "../", "key_btn" => "2", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Relatório Gerencial");
$breadcrumb_pages = array("Principal RH" => "../rh/principalrh.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Relatório Gerencial</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="glyphicon glyphicon-user"></span> - Recusos Humanos<small> - Relatório Gerencial</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading">Relatório Gegencial</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto: </label>
                            <div class="col-lg-9">
                                <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => "« Todos os Projetos »")), $regiaoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'") ?>
                            </div>
                        </div>
                        <div class="form-group datas">
                            <label for="select" class="col-lg-2 control-label">Período: </label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="dataIni" id="data_ini" readonly="true" placeholder="Inicio" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="dataFim" id="data_fim" readonly="true" placeholder="Fim" value="<?php echo $dataFim; ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary filt_anual" />
                    </div>
                </div>
            </form>

            <?php if ($lista) { ?>
                <?php
                if (mysql_num_rows($result) == 0) {
                    echo "<div id='message-box' class='message-red'>Nenhum registro encontrado para o filtro selecionado.</div>";
                } else {
                    $arrayFolha = array();
                    $arrayTotal = array();
                    while ($row_folha = mysql_fetch_assoc($result)) {
                        $arrayFolha[$row_folha['projeto']][] = $row_folha;
                    }
                    ?>
                    <p id="excel" style="text-align: right; margin-top: 20px">
                        <button type="button" class="btn btn-success" onclick="tableToExcel('tbRelatorio', 'Relatório')" ><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </p>
                    <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle' id="tbRelatorio">
                        <?php
                        foreach ($arrayFolha as $projeto => $arrayValue) {
                            foreach ($arrayValue as $value) {
                                if ($projeto != $projetoAnterior) {
                                    ?>
                                    <thead>
                                        <tr>
                                            <th colspan="10" class="text-center"><?= $value['nome']; ?></th>
                                        </tr>
                                        <tr>
                                            <th>ID folha</th>
                                            <th>Mês/Ano</th>
                                            <th>Salário Bruto</th>
                                            <th>Salário Liquido</th>
                                            <th>Qtd. Funcionário</th>
                                            <th>GPS</th>
                                            <th>FGTS</th>
                                            <th>PIS</th>
                                            <th>IR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $projetoAnterior = $projeto;
                                    }

                                    $sql = "SELECT (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 1 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS gps, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 2 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS fgts, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 3 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS pis, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 4 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS ir, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 5 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS transporte, (
                                                SELECT B.valor
                                                FROM pagamentos AS A
                                                LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                                WHERE A.id_folha = {$value['id_folha']} AND A.tipo_pg = 6 AND A.tipo_contrato_pg = 1 AND B.`status`= 2
                                                ORDER BY data_proc DESC
                                                LIMIT 1) AS sodexo";

                                    $query_controle = mysql_query($sql);
                                    $row_controle = mysql_fetch_assoc($query_controle);
                                    $tipos = array("1" => "gps", "2" => "fgts", "3" => "pis", "4" => "ir", "5" => "transporte", "6" => "sodexo");
                                    ?> 
                                    <tr>

                                        <td><span class="dados"><?= $value['id_folha'] ?></span></td>
                                        <?php
                                        if ($value['terceiro'] == '1') {
                                            if ($value['tipo_terceiro'] == 3) {
                                                $decimo3 = " - 13ª integral";
                                            } else {
                                                $decimo3 = " / 13ª ({$value['tipo_terceiro']}ª) Parcela";
                                            }
                                            ?> 
                                        <?php
                                        }
                                        $dt = explode('-', $value['data_inicio']);
                                        $mes = mesesArray($dt[1]);
                                        $ano = $dt[0];

                                        $total_credito = $total_debito = 0;
                                        $movimentos = $objFolha->getResumoPorMovimento($value['id_folha']);
                                        foreach ($movimentos as $cod => $valor) {

                                            if ($valor['tipo'] == 'CREDITO') {
                                                $rendimento = $valor['valor'];
                                                $desconto = '';
                                                $total_credito += $valor['valor'];
                                            } else {
                                                $rendimento = '';
                                                $desconto = $valor['valor'];
                                                $total_debito += $valor['valor'];
                                            }
                                        }
                                        ?>

                                        <td><?= $mes . '/' . $ano . $decimo3 ?></td> 
                                        <td><?= formataMoeda($total_credito) ?></td> 
                                        <?php $total_liqui += $total_credito; ?>
                                        <td><?= formataMoeda($total_credito - $total_debito) ?></td> 
                                        <?php $total_limpo += ($total_credito - $total_debito); ?>
                                        <td><?= $value['clts'] ?></td> 
                                        <?php $total_clt += $value['clts']; ?>
                                        <?php
                                        for ($i = 1; $i <= 4; $i++) {
                                            if (!empty($row_controle[$tipos[$i]])) {
                                                $arrayTotal[$i] = $row_controle[$tipos[$i]] + $arrayTotal[$i];
                                                ?>      

                                                <td><?= formataMoeda($row_controle[$tipos[$i]]); ?></td> 

                                            <?php } else { ?>
                                                <td><? echo '----'; ?></td> 
                                                <?php
                                            }
                                        }
                                        unset($decimo3);
                                        ?>    
                                    </tr>             
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                        <tr class="subtitulo">
                            <td colspan="2">Totalizador:</td>
                            <td><center><?= formataMoeda($total_liqui); ?></center></td>
                            <td><center><?= formataMoeda($total_limpo); ?></center></td>
                            <td><center><?= $total_clt; ?></center></td>
                            <td><center><?= formataMoeda($arrayTotal[1]); ?></center></td>
                            <td><center><?= formataMoeda($arrayTotal[2]); ?></center></td>
                            <td><center><?= formataMoeda($arrayTotal[3]); ?></center></td>
                            <td><center><?= formataMoeda($arrayTotal[4]); ?></center></td>
                        </tr>
                    <?php }  ?>
                    </tbody>
                </table>
            <?php } ?>
            <?php include('../template/footer.php'); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/financeiro/entrada.js"></script>
        <script src="../js/global.js"></script>       
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>