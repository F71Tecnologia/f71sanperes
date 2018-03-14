<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

if (!empty($_REQUEST['data_xls'])) {
    
    $dados = utf8_encode($_REQUEST['data_xls']);
    
    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");    
    header("Content-type: application/vnd.ms-excel");
//    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=observacoes-de-participantes.xls");
    
 
    echo "\xEF\xBB\xBF";    
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>RELATÓRIO DE OBSERVAÇÕES DE PARTICIPANTES</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
    
}
 
include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$opt = array("2" => "CLT", "1" => "Autônomo", "3" => "Cooperado", "4" => "Autônomo/PJ", "5"=>"Estagiário");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    
    if($tipo_contratacao == "2"){
        $contratacao = "clt" ;
    } elseif($tipo_contratacao == "5") {
        $contratacao = "estagiario" ;
    } else {
        $contratacao = "autonomo";
    }

    if ($tipo_contratacao == 2) {

        $str_qr_relatorio = "SELECT *, A.nome, A.endereco enderecoClt, B.nome AS nome_curso, B.salario,
            A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.agencia_dv, A.conta, A.conta_dv,E.unidade as unidade_clt,
            date_format(A.data_demi, '%d/%m/%Y')as data_saidabr,
            date_format(A.data_nasci, '%d/%m/%Y')as data_nascibr,
            date_format(A.data_escola, '%d/%m/%Y')as data_escolabr,
            D.nome as nome_etnia,
            F.nome as nome_sindicato,
            date_format(A.data_ctps, '%d/%m/%Y')as data_ctpsbr,
            date_format(A.dada_pis, '%d/%m/%Y')as data_pisbr,
            date_format(A.data_rg, '%d/%m/%Y')as data_rgbr,
            date_format(A.data_entrada, '%d/%m/%Y')as data_entradabr,
            date_format(A.data_exame, '%d/%m/%Y')as data_examebr,
            A.campo1 as numero_ctps
            FROM rh_clt AS A
            LEFT JOIN curso AS B
            ON B.id_curso = A.id_curso
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            LEFT JOIN etnias AS D
            ON D.id = A.etnia
            LEFT JOIN unidade AS E
            ON E.id_unidade = A.id_unidade
            LEFT JOIN rhsindicato AS F
            ON F.id_sindicato = A.rh_sindicato
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' AND (A.status < 60 OR A.status = 200 OR A.status = 70) ";
    } elseif($tipo_contratacao == 5) {
        $str_qr_relatorio = "SELECT *, A.nome, A.atividade AS nome_curso, A.salario,E.unidade as unidade_clt,
            A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta,
            date_format(A.data_saida, '%d/%m/%Y')as data_saidabr,
            date_format(A.data_nasci, '%d/%m/%Y')as data_nascibr,
            date_format(A.data_escola, '%d/%m/%Y')as data_escolabr,
            D.nome as nome_etnia,
            date_format(A.data_ctps, '%d/%m/%Y')as data_ctpsbr,
            date_format(A.dada_pis, '%d/%m/%Y')as data_pisbr,
            date_format(A.data_rg, '%d/%m/%Y')as data_rgbr,
            date_format(A.data_entrada, '%d/%m/%Y')as data_entradabr,
            date_format(A.data_exame, '%d/%m/%Y')as data_examebr
            FROM estagiario AS A
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            LEFT JOIN etnias AS D
            ON D.id = A.etnia
            LEFT JOIN unidade AS E
            ON E.id_unidade = A.id_unidade
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao'";
    } else {
        $str_qr_relatorio = "SELECT *, A.nome, B.nome AS nome_curso, B.salario,E.unidade as unidade_clt,
            A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta,
            date_format(A.data_saida, '%d/%m/%Y')as data_saidabr,
            date_format(A.data_nasci, '%d/%m/%Y')as data_nascibr,
            date_format(A.data_escola, '%d/%m/%Y')as data_escolabr,
            D.nome as nome_etnia,
            date_format(A.data_ctps, '%d/%m/%Y')as data_ctpsbr,
            date_format(A.dada_pis, '%d/%m/%Y')as data_pisbr,
            date_format(A.data_rg, '%d/%m/%Y')as data_rgbr,
            date_format(A.data_entrada, '%d/%m/%Y')as data_entradabr,
            date_format(A.data_exame, '%d/%m/%Y')as data_examebr
            FROM autonomo AS A
            INNER JOIN curso AS B
            ON B.id_curso = A.id_curso 
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            LEFT JOIN etnias AS D
            ON D.id = A.etnia
            LEFT JOIN unidade AS E
            ON E.id_unidade = A.id_unidade
            LEFT JOIN rhsindicato AS F
            ON F.id_sindicato = A.rh_sindicato
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao'";
    }
    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY A.nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}
if(isset($_REQUEST['regiao'])){
    $where = (isset($_REQUEST['projeto'])) ? " WHERE id_regiao = {$_REQUEST['regiao']} AND id_projeto = {$_REQUEST['projeto']}" : " WHERE id_regiao = {$_REQUEST['regiao']} ";
    $where .= " AND (status < 60 OR status IN (67,68,200))
            AND observacao <> ''";
}
//if(isset($_REQUEST['projeto'])){
//    $proj = ($_REQUEST['projeto'] != -1) ? " WHERE id_projeto = '{$_REQUEST['projeto']}' " : null;
//}

//$proj = (isset($_REQUEST['projeto'])) ? " WHERE id_projeto = '{$_REQUEST['projeto']}' " : null;
$sql = "SELECT 
        id_clt,
        id_regiao AS regiao,
        (SELECT nome FROM projeto P WHERE P.id_projeto = R.id_projeto) AS projeto,
        id_projeto, 
        nome,
        cpf,
        observacao FROM rh_clt R
        {$where}                           
        ORDER BY nome";
$qr_relatorio = mysql_query($sql) or die($sql);
$qr_relatorio = mysql_query($sql) or die(mysql_error());
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;

$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Observações de Participantes</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Observações de Participantes </h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao' , 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                    </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <button type="button" form="formPdf" name="pdf" data-title="Relatório de Participantes do Projeto" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                            
                            
                                <button type="submit" name="gerar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div> 
               
                
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="relatorio_exp" class="table-responsive">
                        <table class="table table-striped table-hover text-sm valign-middle table-bordered" id="tbRelatorio">
                            <thead>
                                    <tr class="titulo">
                                        <th>COD.</th>
                                        <th>PROJETO</th>
                                        <th>NOME</th>
                                        <th>CPF</th>
                                        <th>OBSERVAÇÃO</th>
                                        
                                    </tr>
                            </thead>
                                <tbody>
                            <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"; ?>

                                
                                        <tr>
                                            <td><?php echo $row_rel['id_clt'] ?></td>
                                            <td><?php echo $row_rel['projeto'] ?></td>
                                            <td> <?php echo $row_rel['nome']; ?></td>
                                           <td><?php echo $row_rel['cpf'] ?></td>
                                            <td><?php echo $row_rel['observacao'] ?></td>
                                            
                                        </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
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
        <script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>
        <script>
            $(function () {
                $(".bt-image").on("click", function () {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });

               $("#exportarExcel").click(function (e) {                   
                   $("#relatorio_exp img:last-child").remove();

                   var html = $("#relatorio_exp").html();

                   $("#data_xls").val(html); 
                   $("#form").submit();
               });
            });
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
            
            $("#exportarExcel").click(function (e) {
	
                $("#relatorio_exp img:last-child").remove();

                var html = $("#relatorio_exp").html();

                $("#data_xls").val(html); 
                $("#form").submit();

        }); 
        </script>

    </body>
</html>
<!-- A -->