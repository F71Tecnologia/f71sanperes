<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include "../classes/RelatorioClass.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$relatorio = new Relatorio();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if (isset($_REQUEST['method'])) {
    $id = $_REQUEST['id'];
    switch ($_REQUEST['method']) {
        case 'excluir':
            $retorno = $relatorio->excluir($id);
            echo json_encode(array('status' => $retorno));
            exit();
        case 'habilitar':
            $dados = array('status' => '1');
            $retorno = $relatorio->editar($dados, $id);
            echo json_encode(array('status' => $retorno));

            exit();
        case 'desabilitar':
            $dados = array('status' => '0');
            $retorno = $relatorio->editar($dados, $id);
            echo json_encode(array('status' => $retorno));
            exit();
    }
}


$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
$sql = "SELECT *,
        (SELECT nome FROM grupo_relatorio WHERE grupo_relatorio.id_grupo = relatorios.id_grupo) as grupo
        FROM relatorios order by grupo,nome";
echo "<!-- {$sql} -->";
$qr_relatorio = mysql_query($sql) or die(mysql_error());
$num_rows = mysql_num_rows($qr_relatorio);

$qtd_hab = mysql_fetch_assoc(mysql_query("SELECT COUNT(id_relatorio) as hab FROM relatorios WHERE status = 1"));
$qtd_des = mysql_fetch_assoc(mysql_query("SELECT COUNT(id_relatorio) as des FROM relatorios WHERE status = 0"));

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Gestão de Relatório");
$breadcrumb_pages = array();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Relatórios</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Gestão de Relatórios</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Gestão de Relatórios</div>
                    <div class="panel-body">
                        <div class="panel-footer text-right hidden-print controls">
                            <a href="form_relatorio.php" class="btn btn-success"><span class="fa fa-plus"></span> Adicionar Novo</a>
                            <a href="listaGruposRelatorios.php" class="btn btn-primary"><span class="fa fa-file-text"></span> Gestão de Grupos de Relatórios</a>
                        
                                <?php if (!empty($qr_relatorio)) { ?>
                                <div style="text-align: right; margin-top: 20px">
                                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                </div>
                        </div>
             
                    <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                        <thead>
                            <tr>
                                <th colspan="7"><?= (!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr>                                
                                <th>MÓDULO</th>
                                <th>GRUPO</th>
                                <th>NOME</th>
                                <th>URL</th>
                                <th colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                $cor = ($row_rel['status']) ? '' : 'back-red';
                                ?>
                                <tr class="<?php echo $class . " " . $cor ?>">
                                    <td> <?php echo ($row_rel['id_modulo'] == 2) ? "Recursos Humanos" : "" ?></td>
                                    <td> <?php echo $row_rel['grupo']; ?></td>
                                    <td><?php echo $row_rel['nome'] ?></td>
                                    <td> <?php echo $row_rel['url']; ?></td>

                                    <td style="text-align:center;"> <a href="form_relatorio.php?id=<?php echo $row_rel['id_relatorio']; ?>"><span class="fa fa-edit" id="editarInf"></span></a></td>
                                    <td style="text-align:center;"> <a href="#" data-id="<?php echo $row_rel['id_relatorio']; ?>" class="excluir"><span class="fa fa-trash" id="editarInf"></span></a></td>
                                    <td style="text-align:center;"> 
                                        <?php if ($row_rel['status'] == 1) { ?>
                                        <a href="#" data-id="<?php echo $row_rel['id_relatorio']; ?>" class="desabilita"><span class="fa fa-minus-circle" id=""></span></a>
                                        <?php } else { ?> 
                                            <a href="#" data-id="<?php echo $row_rel['id_relatorio']; ?>" class="habilita"><span class="fa fa-check" id="editarInf"></span></a>
                                            <?php } ?>
                                    </td>

                                </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"><strong>TOTAL:</strong></td>
                                <td colspan="3" align="center"><?php echo $num_rows ?></td>
                            </tr>
                            <tr>
                                <td colspan="4"><strong>TOTAL HABILITADOS:</strong></td>
                                <td colspan="3" align="center"><?php echo $qtd_hab['hab'] ?></td>
                            </tr>
                            <tr>
                                <td colspan="4"><strong>TOTAL DESABILITADOS:</strong></td>
                                <td colspan="3" align="center"><?php echo $qtd_des['des'] ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php } ?>
                    </div>
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
                        $(document).ready(function () {
                            $(".excluir").click(function () {
                                if (confirm("Tem certeza que quer excluir permanentemente esse relatório? Essa ação não pode ser desfeita")) {
                                    var id = $(this).data("id");
                                    $.post('<?= $_SERVER['PHP_SELF'] ?>', {id: id, method: 'excluir'}, function (data) {
                                        if (data.status) {
                                            alert("Exlcuido com sucesso!");
                                            window.location.reload();
                                        } else {
                                            alert("Erro ao exlcuir.");
                                        }
                                    }, 'json');
                                }
                            });
                            $(".habilita").click(function () {
                                var id = $(this).data("id");
                                $.post('<?= $_SERVER['PHP_SELF'] ?>', {id: id, method: 'habilitar'}, function (data) {
                                    if (data.status) {
                                        alert("Habilitado com sucesso!");
                                        window.location.reload();
                                    } else {
                                        alert("Erro ao habilitar.");
                                    }
                                }, 'json');
                            });
                            $(".desabilita").click(function () {
                                var id = $(this).data("id");
                                $.post('<?= $_SERVER['PHP_SELF'] ?>', {id: id, method: 'desabilitar'}, function (data) {
                                    if (data.status) {
                                        alert("Desabilitado com sucesso!");
                                        window.location.reload();
                                    } else {
                                        alert("Erro ao desabilitar.");
                                    }
                                }, 'json');
                            });
                        });
</script>

</body>
</html>
