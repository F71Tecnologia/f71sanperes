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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

$regiao = $usuario['id_regiao'];

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}

//getList
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "getList") {
    $cpf = $_REQUEST['cpf'];
    $qr_clt = mysql_query("SELECT A.*, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada, DATE_FORMAT(A.data_saida, '%d/%m/%Y') as data_saida, B.nome AS nome_projeto FROM rh_clt AS A LEFT JOIN projeto AS B ON B.id_projeto = A.id_projeto WHERE A.cpf = '{$cpf}' AND A.id_regiao IN (SELECT id_regiao FROM regioes WHERE id_master = {$usuario['id_master']})");
    $qr_autonomo = mysql_query("SELECT A.*, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada, DATE_FORMAT(A.data_saida, '%d/%m/%Y') as data_saida, B.nome AS nome_projeto FROM autonomo AS A LEFT JOIN projeto AS B ON B.id_projeto = A.id_projeto WHERE A.cpf = '{$cpf}' AND A.id_regiao IN (SELECT id_regiao FROM regioes WHERE id_master = {$usuario['id_master']})");
    $html = "";
    if (mysql_num_rows($qr_clt) > 1) {
        $html .= "<h2>CLT</h2>";
        $html .= '<table class="table table-condensed table-bordered table-hover text-sm valign-middle">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>PROJETO</th>";
        $html .= "<th>DATA DE ENTRADA</th>";
        $html .= "<th>DATA DE SAÍDA</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while ($row_clt = mysql_fetch_assoc($qr_clt)) {
            $html .= '<tr>';
            $html .= "<td> {$row_clt['id_clt']} </td>";
            $html .= "<td>{$row_clt['nome_projeto']}</td>";
            $html .= "<td>{$row_clt['data_entrada']}</td>";
            $html .= "<td>{$row_clt['data_saida']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
    }
    if (mysql_num_rows($qr_autonomo) > 1) {
        $html .= "<h2>AUTÔNOMO</h2>";
        $html .= '<table class="table table-condensed table-bordered table-hover text-sm valign-middle">';
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>#</th>";
        $html .= "<th>PROJETO</th>";
        $html .= "<th>DATA DE ENTRADA</th>";
        $html .= "<th>DATA DE SAÍDA</th>";
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        while ($row_autonomo = mysql_fetch_assoc($qr_autonomo)) {
            $html .= '<tr>';
            $html .= "<td>{$row_autonomo['id_autonomo']} </td>";
            $html .= "<td>{$row_autonomo['nome_projeto']}</td>";
            $html .= "<td>{$row_autonomo['data_entrada']}</td>";
            $html .= "<td>{$row_autonomo['data_saida']}</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
    }
    echo utf8_encode($html);
    exit;
}

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['todos_projetos'])) {
    $status = $_REQUEST['status'];
    $projeto = $_REQUEST['projeto'];

    $sql_clt = "SELECT id_clt FROM rh_clt ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql_clt .= "WHERE id_projeto = '{$projeto}'";
    }
    $qr_clt_projeto = mysql_query($sql_clt);
    $sql_autonomo = "SELECT id_autonomo FROM autonomo ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql_autonomo .= "WHERE id_projeto = '{$projeto}'";
    }
    $qr_autonomo_projeto = mysql_query($sql_autonomo);

    $contIDs = 0;
    $arrIDs = array();

    while ($row_clt_projeto = mysql_fetch_assoc($qr_clt_projeto)) {
        $arrIDs[$contIDs] = $row_clt_projeto;
        $contIDs++;
    }
    while ($row_autonomo_projeto = mysql_fetch_assoc($qr_autonomo_projeto)) {
        $arrIDs[$contIDs] = $row_autonomo_projeto;
        $contIDs++;
    }

    $total = count($arrIDs);

    $sql_duplicados = "SELECT id_clt,nome,cpf, COUNT(cpf) AS qnt1, '0' AS qnt2
                        FROM rh_clt
                        WHERE id_regiao IN (SELECT id_regiao FROM regioes WHERE id_master = {$usuario['id_master']})";
    if ($status == 1) {
        $sql_duplicados.= "AND (status < 60 OR status = 70 OR status = 200) ";
    } else if ($status == 2) {
        $sql_duplicados.= "AND status > 60 ";
    }
    $sql_duplicados .= "GROUP BY cpf

                        UNION

                        SELECT id_autonomo,nome,cpf, '0' AS qnt1, COUNT(cpf) AS qnt2
                        FROM autonomo
                        WHERE id_regiao IN (SELECT id_regiao FROM regioes WHERE id_master = {$usuario['id_master']}) ";

    if ($status == 1) {
        $sql_duplicados.= "AND status < 60 ";
    } else if ($status == 2) {
        $sql_duplicados.= "AND status > 60 ";
    }
    $sql_duplicados .= "GROUP BY cpf
                        ORDER BY nome";
    
//    echo $sql_duplicados;
//    exit();

    $qr_duplicados = mysql_query($sql_duplicados);
    $array_duplicados = array();
    $j = 1;
    $i = 0;

    while ($row_duplicados = mysql_fetch_assoc($qr_duplicados)) {
        for ($k = 0; $k < $total; $k++) {
            if (($row_duplicados['id_clt'] == $arrIDs[$k]['id_clt'] && empty($row_duplicados['id_autonomo'])) || ($row_duplicados['id_autonomo'] == $arrIDs[$k]['id_autonomo'] && empty($row_duplicados['id_clt']))) {
                if ($array_duplicados[$i]['cpf'] == $row_duplicados['cpf']) {
                    $array_duplicados[$i]['qnt1'] = $row_duplicados['qnt1'];
                } else {
                    $array_duplicados[$j] = $row_duplicados;
                    $j++;
                    $i++;
                }
            }
        }
    }
}
$arrStatus = array("0" => "Todos", "1" => "Ativos", "2" => "Desativados");

$statusR = (isset($_REQUEST['status'])) ? $_REQUEST['status'] : NULL;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Múltiplos Cadastros </title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Múltiplos Cadastros</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">

                        <div class="form-group" >    
                            <label for="select" class="col-sm-3 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>

                            <label for="select" class="col-sm-2 control-label hidden-print" >Status do Funcionário</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($arrStatus, $statusR, array('name' => "status", 'id' => 'status', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>

                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filtrar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <table class="table table-condensed table-bordered table-hover text-sm valign-middle" id="tabela">

                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Qtd CLT</th>
                                <th>Qtd Autonomo</th>
                                <th>Visualizar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($cont = 1; $cont < $j; $cont++) {
                                if ($array_duplicados[$cont]['qnt2'] > 1 || $array_duplicados[$cont]['qnt1'] > 1 || ($array_duplicados[$cont]['qnt2'] >= 1 && $array_duplicados[$cont]['qnt1'] >= 1)) {
                                    ?>
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td><?php echo $array_duplicados[$cont]['nome']; ?></td>
                                        <td><?php echo $array_duplicados[$cont]['cpf']; ?></td>
                                        <td><?php echo $array_duplicados[$cont]['qnt1']; ?></td>
                                        <td><?php echo $array_duplicados[$cont]['qnt2']; ?></td>
                                        <td class="center"><img src="../imagens/icones/icon-docview.gif" title="Documentos" class="bt-image" data-cpf="<?php echo $array_duplicados[$cont]['cpf']; ?>" /></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
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
        <script>
            $(function () {
                $(".bt-image").on("click", function () {
                    var cpf = $(this).data("cpf");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio50.php", {cpf: cpf, method: "getList"}, "625-not", "500");
                });
            });
        </script>

    </body>
</html>
