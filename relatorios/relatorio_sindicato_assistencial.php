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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)$ACOES = new Acoes();
$ACOES = new Acoes();

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=relatorio_sindicato_assistencias.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relat�rio de Contribui��o Associativa</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $projeto = $_REQUEST['projeto'];

    $sql = "SELECT A.id_clt, A.nome, A.cpf, A.rh_sindicato, C.nome sindicato, B.valor
            FROM rh_clt A
            LEFT JOIN curso B ON A.id_curso = B.id_curso
            LEFT JOIN rhsindicato C ON A.rh_sindicato = C.id_sindicato
            WHERE id_projeto = $projeto AND (A.status < 60 OR A.status IN (70, 200)) AND A.isento_sindical_assistencial = 0 AND rh_sindicato > 0
            ORDER BY C.nome, A.nome";
    $query = mysql_query($sql);

    while ($row = mysql_fetch_assoc($query)) {
        $participantes[$row['rh_sindicato'] . ' - ' . $row['sindicato']][] = $row;
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relat�rio de Contribui��o Assistencial</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relat�rio de Contribui��o Assistencial</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relat�rio</div>
                            <div class="panel-body">
                                <div class="form-group" >
                                    <label for="select" class="col-sm-3 control-label hidden-print" >Regi�o</label>
                                    <div class="col-sm-3">
                                        <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                                    </div>

                                    <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                                    <div class="col-sm-3">
                                        <?php echo montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                                    </div>
                                </div>
                            </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span>Gerar</button>

                            <?php if (!empty($participantes) && (isset($_POST['gerar']))) { ?>
                                <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                        </div>
                    </div> 
                
                <?php if (!empty($participantes) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="tabela">
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                            <?php foreach ($participantes as $sindicato => $clt) {
                            ?>
                                <tr>
                                    <th class="text-center" colspan="4"><?=$sindicato?></th>
                                </tr>
                                <tr class="titulo">
                                    <th class="text-center">COD</th>
                                    <th class="text-center">CLT</th>
                                    <th class="text-center">CPF</th>
                                    <th class="text-center">SAL�RIO</th>
                                </tr>
                                <?php foreach ($clt as $key => $value) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $value['id_clt']; ?></td>
                                        <td><?= $value['nome']; ?></td>
                                        <td><?= $value['cpf']; ?></td>
                                        <td><?= 'R$' . number_format($value['valor'],2,',','.'); ?></td>
                                    </tr>       
                                <?php } ?>
                            <?php } ?>
                        </table>
                    </div>
                    <?php include('../template/footer.php'); ?>
            </div>
        <?php } ?>

    </form>

    <form style="display: none" action="exportTablePdf.php" method="post" id="formPdf">
        <input type="text" name="titlePdf" id="titlePdf" value=""/>
        <textarea name="tabelaPdf" id="tabelaPdf" value="" ></textarea>
    </form>

    <div class="clear"></div>
</div>
<script src="../js/jquery-1.10.2.min.js"></script>
<script src="../resources/js/bootstrap.min.js"></script>
<script src="../resources/js/tooltip.js"></script>
<script src="../resources/js/main.js"></script>
<script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>
<!--<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>-->
<script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../js/global.js" type="text/javascript"></script>

<script type="text/javascript" src="../resources/js/jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/jspdf.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/libs/base64.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/tableExport.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/jquery.base64.js"></script>

<script>
    $(function () {

        $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

        $("#exportarExcel").click(function () {
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