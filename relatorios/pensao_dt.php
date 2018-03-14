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
    header("Content-Disposition: attachment; filename=pensoes_sobre_dt.xls");


    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Relatório de Pensões sobre 13º Salário</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}
$sqlFolha = "SELECT *
             FROM rh_folha A
             LEFT JOIN regioes B ON A.regiao = B.id_regiao
             WHERE A.regiao = 2 AND tipo_terceiro IN (1,2)";
$queryFolha = mysql_query($sqlFolha);
$arrFolha[] = '-- Selecione a Folha --';
while ($rowFolha = mysql_fetch_assoc($queryFolha)) {
    $arrFolha[$rowFolha['id_folha']] = $rowFolha['id_folha'] . ' - ' . $rowFolha['regiao'];
}
if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    extract($_REQUEST);

    $sql = "SELECT C.id_unidade, D.unidade, C.id_clt, C.nome clt, B.favorecido, B.cpf, B.conta, B.agencia,  B.id_lista_banco, E.banco, A.nome_mov, A.base, A.valor_mov
            FROM itens_pensao_para_contracheque AS A
            LEFT JOIN favorecido_pensao_assoc AS B ON (REPLACE(REPLACE(B.cpf,'.',''),'-','') = A.cpf_favorecido)
            LEFT JOIN rh_clt C ON C.id_clt = B.id_clt
            LEFT JOIN unidade D ON C.id_unidade = D.id_unidade
            LEFT JOIN listabancos E ON E.id_lista = B.id_lista_banco
            WHERE A.id_folha = '$folha' AND A.status = 1
            ORDER BY unidade ASC";
    $query = mysql_query($sql);

    while ($row = mysql_fetch_assoc($query)) {
        $participantes[] = $row;
    }
}
$folhaSel = (isset($_REQUEST['folha'])) ? $_REQUEST['folha'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Pensões sobre 13º Salário</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Pensões sobre 13º Salário</h2></div>

            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Folha</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($arrFolha, $folhaSel, array('name' => "folha", 'id' => 'folha', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>

                            <?php if (!empty($participantes) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" value="Exportar para Excel" class="exportarExcel btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>    
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                        </div>
                    </div> 
                
                <?php if (!empty($participantes) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="tabela">
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 

                            <tr>
                                <th>UNIDADE</th>
                                <th>NOME</th>
                                <th>BENEFICIÁRIO</th>
                                <th>CPF</th>
                                <th>CONTA</th>
                                <th>AGÊNCIA</th>
                                <th>BANCO</th>
                                <th>MOVIMENTO</th>
                                <th>BASE</th>
                                <th>VALOR</th>
                            </tr>
                            <?php foreach ($participantes as $key => $value) { 
                                $totalBase += $value['base'];
                                $totalValor += $value['valor_mov'];
                            ?>
                                <tr>
                                    <td><?= $value['id_unidade'] . ' - ' . $value['unidade'] ?></td>
                                    <td><?= $value['id_clt'] . ' - ' . $value['clt'] ?></td>
                                    <td><?= $value['favorecido'] ?></td>
                                    <td><?= $value['cpf'] ?></td>
                                    <td><?= $value['conta'] ?></td>
                                    <td><?= $value['agencia'] ?></td>
                                    <td><?= $value['banco'] ?></td>
                                    <td><?= $value['nome_mov'] ?></td>
                                    <td><?= 'R$ ' . number_format($value['base'], 2, ',', '.') ?></td>
                                    <td><?= 'R$ ' . number_format($value['valor_mov'], 2, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="8"></td>
                                    <td><strong><?='R$ ' . number_format($totalBase, 2, ',', '.')?></strong></td>
                                    <td><strong><?='R$ ' . number_format($totalValor, 2, ',', '.')?></strong></td>
                                </tr>
                        </table>
                    </div>
                    <?php include('../template/footer.php'); ?>
            </div>
        <?php } else if (empty($participantes) && isset($_POST['gerar'])) { ?>
            <div class="alert alert-dismissable alert-danger">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>Desculpe,</strong> não foram encontradas pensões nessa folha.
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

<!--    <script type="text/javascript" src="../resources/js/jspdf/libs/sprintf.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/jspdf.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/libs/base64.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/tableExport.js"></script>
    <script type="text/javascript" src="../resources/js/jspdf/jquery.base64.js"></script>-->

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