<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("2" => "CLT", "1" => "Autônomo", "3" => "Cooperado", "4" => "Autônomo/PJ");

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "getList") {
    $id_curso = $_REQUEST['id_curso'];
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $sql_clt = "SELECT nome
                FROM rh_clt
                WHERE id_curso = '{$id_curso}'
                AND id_regiao = '{$id_regiao}' 
                AND id_projeto = '{$id_projeto}'
                AND status < '60'
                ORDER BY nome";
    $result_clt = mysql_query($sql_clt);

    $html .= "<div class='panel panel-info'>";
    $html .= "<div class='panel-heading'> <h3 class='text-center'>CLT</h3> </div>";
    $html .= "<table class='table table-striped table-condensed table-bordered text-sm valign-middle'>";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>NOME</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    while ($row_clt = mysql_fetch_assoc($result_clt)) {
        $html .= '<tr>';
        $html .= "<td> {$row_clt['nome']} </td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";

    $sql_autonomo = "SELECT nome 
                        FROM autonomo
                        WHERE id_curso = '{$id_curso}'
                        AND id_regiao = '{$id_regiao}' 
                        AND status = '1'
                        id_projeto = '{$id_projeto}'
                        ORDER BY nome";
    $result_autonomo = mysql_query($sql_autonomo);

    $html .= "<div class='pane panel-info'>";
    $html .= "<div class='panel-heading'> <h3 class='text-center'>AUTÔNOMO</h3> </div>";
    $html .= '<table class="table table-striped table-condensed table-bordered text-sm valign-middle">';
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th>NOME</th>";
    $html .= "</tr>";
    $html .= "</thead>";
    $html .= "<tbody>";
    while ($row_autonomo = mysql_fetch_assoc($result_autonomo)) {
        $html .= '<tr>';
        $html .= "<td> {$row_autonomo['nome']} </td>";
        $html .= "</tr>";
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";

    echo utf8_encode($html);
    exit;
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));

    $str_qr_relatorio = "SELECT A.id_curso, A.nome, B.id_projeto, B.nome as nome_projeto
            FROM curso AS A
            LEFT JOIN projeto AS B
            ON B.id_projeto = A.campo3
            WHERE A.id_regiao = '$id_regiao' ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.campo3 = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY A.nome ";

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

        <title>:: Intranet :: Relatório de Atividades por Lotação</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">


    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Atividades por Lotação</small></h2></div>
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
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>  
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Atividade Por Lotação Detalhada')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div> 
                

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>  
                    <table class="table table-striped table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                        <thead>
                            <tr>
                                <th>COD.</th>
                                <th>ATIVIDADE</th>
                                <th>QUANTIDADE</th>
                                <th>VISUALIZAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $sql_qtd_clt = "SELECT count(id_clt) FROM rh_clt WHERE id_curso = {$row_rel['id_curso']} AND id_regiao = '{$id_regiao}' AND id_projeto = '{$row_rel['id_projeto']}' AND status < '60' ";
                                $result_qtd_clt = mysql_query($sql_qtd_clt);
                                $qtd_clt = mysql_fetch_row($result_qtd_clt);
                                $sql_qtd_autonomo = "SELECT count(id_autonomo) FROM autonomo WHERE id_curso = {$row_rel['id_curso']} AND id_regiao = '{$id_regiao}' AND id_projeto = '{$row_rel['id_projeto']}' AND status = '1' ";
                                $result_qtd_autonomo = mysql_query($sql_qtd_autonomo);
                                $qtd_autonomo = mysql_fetch_row($result_qtd_autonomo);
                                $total = $qtd_clt[0] + $qtd_autonomo[0];
                                if ($total > 0) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['id_curso']; ?></td>
                                        <td><?php echo $row_rel['nome'] . " - " . $row_rel['nome_projeto'] ?></td>
                                        <td> <?php echo $total; ?></td>
                                        <td class="text-center"><img src="../imagens/icones/icon-docview.gif" title="Visualizar Participantes" class="bt-image" data-id_curso="<?php echo $row_rel['id_curso']; ?>" data-projeto="<?php echo $row_rel['id_projeto']; ?>" data-regiao="<?php echo $id_regiao; ?>"/></td>
                                    </tr>                                
                                <?php }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">TOTAL DE PROFISSIONAIS: <?= $total ?></td>
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
                var id_curso = $(this).data("id_curso");
                var projeto = $(this).data("projeto");
                var regiao = $(this).data("regiao");
                var nome = $(this).parents("tr").find("td:first").next().html();
                thickBoxIframe(nome, "relatorio19.php", {id_curso: id_curso, projeto: projeto, regiao: regiao, method: "getList"}, "625-not", "500");
            });
               
            $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
       });
</script>

</body>
</html>
