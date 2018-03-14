<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("0" => "Todos", "1" => "Funcionários com Sindicato", "2" => "Funcionários sem Sindicato");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $sindicato = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));

    $str_qr_relatorio = "SELECT MAX(id_clt) AS id_clt, nome, DATE_FORMAT(data_nasci,'%d/%m/%Y') as data_nascibr, campo3, locacao
                        FROM rh_clt 
                        WHERE id_regiao = '$id_regiao' AND status < '60' ";

    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "GROUP BY cpf
                            ORDER BY nome";

    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
    $qtd_clt = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de CLTs para Impressão de Etiqueta em lote</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Relatório de CLTs para Impressão de Etiqueta em lote</small></h2></div>
            <form action="etiquetaLote.php" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >

                                <label for="select" class="col-sm-3 control-label hidden-print">Região</label>
                                <div class="col-sm-4">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[custom[select]] form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                </div>
                            </div>
                        </div>

                            <div class="panel-footer text-right hidden-print">
                                <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                    <button type="button" value="exportar para excel" onclick="tableToExcel('tbRelatorio', 'Impressão de Etiquetas em Lote')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
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
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <label class="input-group-addon">
                                        <input type="radio" value="Marcar Todos" name="marca"  onClick="MarcarTodosCheckbox();"/> 
                                    </label>
                                    <label class="form-control pointer">Selecionar Todos</label>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="input-group">
                                    <label class="input-group-addon">
                                        <input type="radio" value="Desmarcar" name="marca" onClick="Desmarcar();"  checked=""/> 
                                    </label>
                                    <label class="form-control pointer">Desmarcar Todos</label>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                            <thead>
                                
                                <tr>
                                <?php if (!isset($_REQUEST['todos_projetos'])) {
                                    echo "<th>SELECIONE</th>";
                                } ?>
                                    <th>COD.</th>
                                    <th>NOME</th>
                                    <th>UNIDADE</th>
                                    <th>DATA NASCIMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd" ?>
                                                                <tr class="<?php echo $class ?>">
                                    <?php if (!isset($_REQUEST['todos_projetos'])) {
                                        echo "<td align=\"center\"><input type=\"checkbox\" name=\"check_list[]\" value=\"{$row_rel['id_clt']}\" /></td>";
                                    } ?>
                                        <td><?php echo $row_rel['campo3'] ?></td>
                                        <td> <?php echo $row_rel['nome']; ?></td>
                                        <td> <?php echo $row_rel['locacao']; ?></td>
                                        <td><?php echo $row_rel['data_nascibr']; ?></td>
                                    </tr>                                
                        <?php } ?>
                            </tbody>
                        </table>
                        <div class=" hidden-print text-right controls">
                            <button type="submit" name="gerar_lote" id="gerar_lote" class="btn btn-success"><span class="fa fa-file-text-o"></span> Gerar Lote</button>
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
        <link href="../net1.css" rel="stylesheet" type="text/css" />

        <script>
                                    $(function () {
                                        $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                                        $('#gerar').click(function () {
                                            $("#form").attr('action', '');
                                            $("#form").submit();
                                        });
                                    });

                                    // função do botão que seleciona todos os check box
                                    function MarcarTodosCheckbox() {
                                        $("input[name='check_list[]']").each(function () {
                                            $(this).attr("checked", "checked");
                                        });
                                    }
                                    //função que desmarca todos
                                    function Desmarcar() {
                                        $("input[name='check_list[]']").each(function () {
                                            $(this).removeAttr("checked");
                                        });
                                    }
        </script>

    </body>
</html>
