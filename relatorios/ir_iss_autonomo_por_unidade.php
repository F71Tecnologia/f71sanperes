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

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();
$ACOES = new Acoes();
if (!empty($_REQUEST['data_xls'])) {

    $dados = $_REQUEST['data_xls'];

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
//    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=ir_iss_autonomo_por_unidade.xls");


    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relatório de IR e ISS de Autônomo</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    extract($_REQUEST);
    
    $inicio = implode('-', array_reverse(explode('/', $_REQUEST['inicio'])));
    $final = implode('-', array_reverse(explode('/', $_REQUEST['final'])));

    $sql = "SELECT A.valor, B.inss, A.valor_ir, A.valor_iss, A.ano_competencia, A.mes_competencia, G.id_saida, B.nome, B.id_unidade, I.unidade, B.cpf,B.pis,E.nome AS funcao
            FROM rpa_autonomo AS A
            LEFT JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
            LEFT JOIN projeto AS C ON (C.id_projeto = B.id_projeto)
            LEFT JOIN regioes AS D ON (D.id_regiao = B.id_regiao)
            LEFT JOIN curso AS E ON (B.id_curso = E.id_curso)
            LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa = A.id_rpa)
            LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
            LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
            LEFT JOIN unidade AS I ON (B.id_unidade = I.id_unidade)
            WHERE CONCAT(A.ano_competencia, '-', A.mes_competencia, '-01') BETWEEN '$inicio' AND '$final' 
            AND D.id_regiao = '$regiao'
            -- GROUP BY B.id_autonomo
            ORDER BY 
            -- B.id_unidade, 
            A.ano_competencia, A.mes_competencia, B.nome";
    $query = mysql_query($sql) or die(mysql_error());

    while ($row = mysql_fetch_assoc($query)) {
        $participantes[] = $row;
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de IR e ISS de Autônomo</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">-->
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />-->
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Encargos de Autônomo</h2></div>

            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <div class="col-sm-3">
                                <label for="select" class="control-label hidden-print" >Região</label>
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
<!--                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print" >Mes</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(mesesArray(), $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control')); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print" >Ano</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(anosArray(), $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group">-->
                            <div class="col-sm-5">
                                <label class="control-label hidden-print" >Periodo</label>
                                <div class="input-group">
                                    <input type="text" class="form-control data" name="inicio" value="<?= (empty($_REQUEST['inicio'])) ? date('d/m/Y') : implode('/', array_reverse(explode('-', $_REQUEST['inicio']))) ?>">
                                    <div class="input-group-addon">até</div>
                                    <input type="text" class="form-control data" name="final" value="<?= (empty($_REQUEST['final'])) ? date('d/m/Y') : implode('/', array_reverse(explode('-', $_REQUEST['final']))) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($participantes) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" value="Exportar para Excel" class="exportarExcel btn btn-success">Exportar para Excel</button> 
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span>Gerar</button>
                            
                        </div>
                    </div> 
                
                <?php if (!empty($participantes) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="tabela">
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 

                            <tr>
                                <th>COMPETENCIA</th>
                                <th>NOME</th>
                                <th>VALOR</th>
                                <th>INSS</th>
                                <th>ISS</th>
                                <th>IR</th>
                            </tr>
                            <?php foreach ($participantes as $key => $value) { 
                                $totalIss += $value['valor_iss'];
                                $totalInss += $value['valor_inss'];
                                $totalIr += $value['valor_ir'];
//                                if($auxUnidade != $value['id_unidade']){
//                                    echo "<tr class='info'><td colspan='6'>{$value['id_unidade']} - {$value['unidade']}</td></tr>";
//                                    $auxUnidade = $value['id_unidade'];
//                                }
                            ?>
                                <tr>
                                    <td><?= mesesArray($value['mes_competencia']) . '/' . $value['ano_competencia'] ?></td>
                                    <td><?= $value['nome'] ?></td>
                                    <td><?= 'R$ ' . number_format($value['valor'], 2, ',', '.') ?></td>
                                    <td><?= 'R$ ' . number_format($value['valor_inss'], 2, ',', '.') ?></td>
                                    <td><?= 'R$ ' . number_format($value['valor_iss'], 2, ',', '.') ?></td>
                                    <td><?= 'R$ ' . number_format($value['valor_ir'], 2, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="3"></td>
                                    <td><strong><?='R$ ' . number_format($totalInss, 2, ',', '.')?></strong></td>
                                    <td><strong><?='R$ ' . number_format($totalIss, 2, ',', '.')?></strong></td>
                                    <td><strong><?='R$ ' . number_format($totalIr, 2, ',', '.')?></strong></td>
                                </tr>
                        </table>
                    </div>
                    <?php include('../template/footer.php'); ?>
            </div>
        <?php } ?>
        <div class="clear"></div>
    </div>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/tooltip.js"></script>
    <script src="../resources/js/main.js"></script>
    <script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
    <script src="../js/global.js" type="text/javascript"></script>

    <script type="text/javascript" src="../resources/js/jspdf/libs/sprintf.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/jspdf.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/libs/base64.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/tableExport.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/jquery.base64.js"></script>

    <script>
        $(function () {
            $(".exportarExcel").click(function () {
                $("#tabela img:last-child").remove();

                var html = $("#tabela").html();

                $("#data_xls").val(html);
                $("#form1").submit();
            });
        });
    </script>

</body>
</html>

<!-- A -->