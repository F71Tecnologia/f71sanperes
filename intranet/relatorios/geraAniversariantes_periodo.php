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

$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];
$mes_ini = $_REQUEST['mes_ini'];
$mes_fim = $_REQUEST['mes_fim'];

$meses = mesesArray(null);

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $filtro = true;
    $rs = montaQuery("rh_clt AS A LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)", "A.nome, A.data_nasci, A.locacao, MONTH(A.data_nasci) as mes, B.nome AS projeto", "MONTH(A.data_nasci) BETWEEN {$mes_ini} AND {$mes_fim} AND A.id_regiao = {$regiao} AND A.id_projeto = {$projeto} AND (A.status < 60 OR A.status = 200)", "MONTH(A.data_nasci), DAY(A.data_nasci)", null, false, false);
    $num_rows = mysql_num_rows($rs);
}

$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Aniversariantes do Mês</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Relatório de Aniversáriantes</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Aniversáriantes</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print">Mês inicial</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mes_ini, array('name' => "mes_ini", 'id' => 'mes_ini', 'class' => 'required[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-2 control-label hidden-print">Mês Final</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mes_fim, array('name' => "mes_fim", 'id' => 'mes_fim', 'class' => 'required[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>

                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                        <div class="panel-footer text-right hidden-print">
                            <?php if(isset($filtro) || (isset($_REQUEST['filtrar']))) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('tbRelatorio', 'Aniversariantes')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Todos os Projetos</button>
                            <button type="submit" name="filtrar" id="filtrar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>
                
                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        ?>
                        <table class="table table-striped table-hover table-bordered table-condensed text-sm valign-middle" id="tbRelatorio">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Dia do Aniversário</th>
                                    <th>Núcleo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $_mes = "";

                                while ($row = mysql_fetch_assoc($rs)) {
                                    $data = explode("-", $row['data_nasci']);
                                    $diaAniversario = $data[2];

                                    if ($_mes != $row['mes']) {
                                        $_mes = $row['mes'];
                                        $mes_ext = strtoupper(mesesArray($row['mes']));

                                        if ($row['mes'] == 3) {
                                            $mes_ext = "MARÇO";
                                        }
                                        echo "<tr align='center'><td colspan='3' style='background: #F0F0F7'>" . $mes_ext . "</td><tr />";
                                    }
                                    ?>                                            
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">                                                
                                        <td align='center'><?php echo $diaAniversario; ?></td>
                                        <td><?php echo $row['nome']; ?></td>
                                        <td align='center'><?php echo $row['projeto']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <br/>
                        <div id='message-box' class='alert alert-warning'>
                            <span class="fa fa-exclamation-triangle"></span> Nenhum registro encontrado
                        </div>
                        <?php
                    }
                }
                ?>

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


    </body>
</html>
