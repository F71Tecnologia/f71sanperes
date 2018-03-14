<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/RelatorioClass.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();
$ACOES = new Acoes();
$anos = anosArray();
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$arrMeses = ['1' => 'Janeiro', '2' => 'Fevereiro', '3' => 'Março', '4' => 'Abril', '5' => 'Maio', '6' => 'Junho', '7' => 'Julho', '8' => 'Agosto', '9' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=rel_media_movimento_participante.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relatório de Média de Movimentos por Participante</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaMovimentos') {
    $sqlMovs = "SELECT id_mov, cod, CONCAT(id_mov, ' - ', descicao) nome
                FROM rh_movimentos
                WHERE anobase = '$anoSel' || anobase = 0";

    $queryMovs = mysql_query($sqlMovs);
    $i = 0;
    while ($rows = mysql_fetch_assoc($queryMovs)) {
        $arrMovs[$rows['id_mov']] = utf8_encode($rows['nome']);
    }

    echo json_encode($arrMovs);
    exit();
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $mov = $_REQUEST['mov'];
    $ano = $_REQUEST['ano'];
    $projeto = $_REQUEST['projeto'];

    $sqlPart = "SELECT id_movimento, A.id_clt, B.nome, A.id_projeto, MONTH(data_movimento) mes, SUM(valor_movimento) total_mes
                FROM rh_movimentos_clt A
                LEFT JOIN rh_clt B ON A.id_clt = B.id_clt
                WHERE id_mov = $mov AND A.id_projeto = $projeto AND ano_mov = $ano AND mes_mov BETWEEN 1 AND 12
                GROUP BY id_clt, MONTH(data_movimento)
                ORDER BY nome ASC, id_clt DESC, mes_mov ASC";
    
    
    $queryPart = mysql_query($sqlPart);
    while ($row = mysql_fetch_assoc($queryPart)) {
        $arrPart[$row['id_clt']][$row['mes']] = $row;
        $arrPart[$row['id_clt']]['nome'] = $row['nome'];
    }
//    print_array($arrPart);
}

$sqlMovs = "SELECT id_mov, CONCAT(id_mov, ' - ', descicao) nome
            FROM rh_movimentos
            WHERE anobase = '$anoSel' || anobase = 0";

$queryMovs = mysql_query($sqlMovs);

while ($rows = mysql_fetch_assoc($queryMovs)) {
    $arrMovs[$rows['id_mov']] = $rows['nome'];
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$movSel = (isset($_REQUEST['mov'])) ? $_REQUEST['mov'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Média de Movimentos por Participante</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <style>
            th {
                text-align: center;
            }
        </style>

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Média de Movimentos por Participante</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >

                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div><br><br><br>

                            <label for="select" class="col-sm-3 control-label hidden-print" >Ano</label>
                            <div class="col-sm-2">
                                <?= montaSelect($anos, $anoSel, 'id="ano" name="ano" class="form-control"'); ?>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Movimentos</label>
                            <div class="col-sm-4">
<!--                                <select id="mov" name="mov" class="form-control">
                                    <?php foreach ($arrMovs AS $value) { ?>
                                        <option value="<?= $value['id_mov'] ?>"><?= $value['nome'] ?></option>
                                    <?php } ?>
                                </select>-->
                                <?= montaSelect($arrMovs, $movSel, 'id="mov" name="mov" class="form-control"'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <?php if (!empty($arrPart) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                        <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
                
                <?php if (!empty($arrPart) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="tabela">
                        <table class="table table-striped table-hover text-sm valign-middle table-bordered" id="tbRelatorio"> 
                            <thead>
                                <tr class="titulo">
                                    <th>CÓDIGO</th>
                                    <th>NOME</th>
                                    <th>JAN</th>
                                    <th>FEV</th>
                                    <th>MAR</th>
                                    <th>ABR</th>
                                    <th>MAI</th>
                                    <th>JUN</th>
                                    <th>JUL</th>
                                    <th>AGO</th>
                                    <th>SET</th>
                                    <th>OUT</th>
                                    <th>NOV</th>
                                    <th>DEZ</th>
                                    <th>MÉDIA</th>
                                </tr> 
                            </thead>
                            <tbody>
                            <?php foreach ($arrPart AS $key => $value) {
                            $total = 0; ?>
                                    <tr>
                                        <td><?= $key ?></td>
                                        <td><?= $value['nome'] ?></td>
                                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                                            <td>
                                                <?php
                                                echo 'R$ ' . number_format($value[$i]['total_mes'], 2, ',', '.');
                                                $total += $value[$i]['total_mes'];
                                                ?>
                                            </td>
                                    <?php } ?>
                                        <td><?= 'R$ ' . number_format($total / 12, 2, ',', '.') ?></td>
                                    </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
            <?php include('../template/footer.php'); ?>
            
        <?php } ?>

    </form>
    <div class="clear"></div>
</div>
<script src="../js/jquery-1.10.2.min.js"></script>
<script src="../resources/js/bootstrap.min.js"></script>
<script src="../resources/js/tooltip.js"></script>
<script src="../resources/js/main.js"></script>
<script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../js/global.js" type="text/javascript"></script>

<script>
    $(function () {
        $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

        $('body').on('change', '#ano', function () {
            var ano = $(this).val();
            var options = '';
            $.post('', {method: 'carregaMovimentos', ano: ano}, function (data) {
                console.log(data);
                $('#mov').attr('disabled', true);
                data = JSON.parse(data);
                $.each(data, function (index, value) {
                    options += '<option value="' + index + '">' + value + '</option>';
                })
                $('#mov').html(options);
                $('#mov').attr('disabled', false);
            });
        });

        $("#exportarExcel").click(function () {
            $("#relatorio_exp img:last-child").remove();

            var html = $("#tabela").html();

            $("#data_xls").val(html);
            $("#form").submit();
        });
    });
</script>
</body>
</html>

<!-- A -->