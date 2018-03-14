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

$anos = anosArray();
$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=participantes-adiantamento-dt.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>RELATÓRIO DE PARTICIPANETE COM ADIANTAMENTO DE 13º</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}
if (isset($_REQUEST['gerar'])) {

    $ano = $_REQUEST['ano'];
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];

    $sql = "SELECT A.id_clt, A.id_regiao, A.nome, B.valor_movimento, DATE_FORMAT(C.data_ini, '%d/%m/%Y') data_ini, DATE_FORMAT(C.data_fim, '%d/%m/%Y') data_fim, B.status
            FROM rh_clt A
            LEFT JOIN rh_movimentos_clt B ON A.id_clt = B.id_clt
            LEFT JOIN rh_ferias C ON A.id_clt = C.id_clt
            WHERE B.status = 1 AND C.parcela = 1 AND B.cod_movimento IN (80030,5027) AND B.ano_mov = '$ano' AND YEAR(C.data_ini) = $ano AND B.mes_mov = 17 AND A.id_regiao = $regiao AND A.id_projeto = $projeto AND (A.status < 60 OR A.status IN (70,200)) AND C.status = 1 AND C.mes > 0 AND C.ano > 0 AND C.total_liquido > 0
            ORDER BY A.nome ASC";
    $query = mysql_query($sql);
    while ($row = mysql_fetch_assoc($query)) {
        $arr[] = $row;
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Participantes com Adiantamento de 13º</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Participantes com Adiantamento de 13º</h2></div>
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
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="select" class="col-sm-3 control-label hidden-print" >Ano</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect($anos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>
                            </div>
                        </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (isset($_POST['gerar'])) { ?>
                                <!--<button name="pdf" id="pdf" value="pdf" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>-->    
                                <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>
                    </div> 
      
                <?php if (!empty($arr) && isset($_POST['gerar'])) { ?>
                    <div id="tabela">
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                            <thead>
                                <tr class="titulo">
                                    <th>CÓDIGO</th>
                                    <th>NOME</th>
                                    <th>INICIO DAS FÉRIAS</th>
                                    <th>FIM DAS FÉRIAS</th>
                                    <th>VALOR DO ADIANTAMENTO</th>

                                </tr> 
                            </thead>

                            <?php
                            foreach ($arr as $key => $value) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                ?>
                                <tbody>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $value['id_clt']; ?></td>
                                        <td> <?php echo $value['nome']; ?></td>
                                        <td> <?php echo $value['data_ini']; ?></td>
                                        <td> <?php echo $value['data_fim']; ?></td>
                                        <td> <?php echo 'R$ ' . number_format($value['valor_movimento'], 2, ',', '.'); ?></td>
                                    </tr>       
                                <?php } ?>
                            </tbody>
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
            $(".bt-image").on("click", function () {
                var id = $(this).data("id");
                var contratacao = $(this).data("contratacao");
                var nome = $(this).parents("tr").find("td:first").html();
                thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
            });
        });
        $(function () {
            $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

//                $('#pdf').on('click', function(){
//                    var tabela = $('#tbRelatorio').html();
//                    localStorage['tabela'] = tabela;
//                    console.log(localStorage['tabela']);
////                    location.href="exportTablePdf.php";
//                });
                                    });
</script>
<?php if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
    <script>
        $("#exportarExcel").click(function () {
            $("#relatorio_exp img:last-child").remove();

            var html = $("#tabela").html();

            $("#data_xls").val(html);
            $("#form").submit();
        });

    </script>
<?php } ?>
</body>
</html>

<!-- A -->