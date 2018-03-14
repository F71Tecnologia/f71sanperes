<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../classes/funcionario.php");
include '../classes_permissoes/regioes.class.php';
include("../wfunction.php");
include('../classes/global.php');
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];
$mes = $_REQUEST['mes'];
$ACOES = new Acoes();

$opt = array("2" => "CLT", "1" => "Autônomo", "3" => "Cooperado", "4" => "Autônomo/PJ");


if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $contratacao = ($tipo_contratacao == "2") ? "clt" : "autonomo";

    if ($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT A.nome, B.nome AS nome_curso, B.salario, A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta, date_format(A.data_demi, '%d/%m/%Y')as data_saidabr
            FROM rh_clt AS A
            LEFT JOIN curso AS B
            ON B.id_curso = A.id_curso
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' AND A.status >= '60' ";
    } else {
        $str_qr_relatorio = "SELECT A.nome, B.nome AS nome_curso, B.salario, A.cpf, A.rg, A.pis, C.nome AS nome_banco, A.agencia, A.conta, date_format(A.data_saida, '%d/%m/%Y')as data_saidabr
            FROM autonomo AS A
            INNER JOIN curso AS B
            ON B.id_curso = A.id_curso 
            LEFT JOIN bancos AS C
            ON C.id_banco = A.banco
            WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '$tipo_contratacao' AND A.status != '1'";
    }
    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY A.nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
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

        <title>:: Intranet :: Relatório de Participantes Desligados do Projeto em Ordem Alfabetica</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Participantes Desligados do Projeto em Ordem Alfabetica</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">

                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        
                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label hidden-print">Tipo Contratação:</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php  if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>               
                    </div> 
                
             <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
            <table class="table table-striped table-bordered table-condensed table-hover text-sm valign-middle" id="tbRelatorio">
                <thead>
                    <tr>
                        <th>NOME</th>
                        <th>ATIVIDADE</th>
                        <th>SALÁRIO</th>
                        <th>CPF</th>
                        <th>RG</th>
                        <th>PIS</th>
                        <th>BANCO</th>
                        <th>AGÊNCIA</th>
                        <th>C.C.</th>
                        <th>DATA SAÍDA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                        $class = ($cont++ % 2 == 0) ? "even" : "odd"; ?>
                        <tr class="<?php echo $class ?>">
                            <td><?php echo $row_rel['nome'] ?></td>
                            <td> <?php echo $row_rel['nome_curso']; ?></td>
                            <td align="center"><?php echo number_format($row_rel['salario'], 2, ',', '.'); ?></td>
                            <td> <?php echo $row_rel['cpf']; ?></td>
                            <td> <?php echo $row_rel['rg']; ?></td>
                            <td> <?php echo $row_rel['pis']; ?></td>
                            <td> <?php echo $row_rel['nome_banco']; ?></td>
                            <td> <?php echo $row_rel['agencia']; ?></td>
                            <td> <?php echo $row_rel['conta']; ?></td>
                            <td> <?php echo $row_rel['data_saidabr']; ?></td>
                        </tr>                                
    <?php } ?>
                </tbody>
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
<link href="../net1.css" rel="stylesheet" type="text/css" />

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
                            });
</script>


</body>


</html>
