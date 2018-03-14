<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

function printArr($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}
include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$optProjetos = getProjetos($usuario['id_regiao']);
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//SELECIONANDO OS DADOS DO RELATÓRIO

if($_REQUEST['projeto'] > 0 && isset($_REQUEST['gerar'])){
    $selProjeto = $_REQUEST['projeto'];
    $auxProjeto = " AND A.id_projeto = '{$_REQUEST['projeto']}' ";
    $order = "nome,tipo,data_final1";
} else {
    $order = "tipo,data_final1,nome";
}
$qr = "SELECT *,
IF(data_final1 < CURDATE(), 1, 2) AS tipo,
DATEDIFF(data_final1,CURDATE()) AS dias1,
DATEDIFF(data_final2,CURDATE()) AS dias2
FROM (
SELECT A.id_projeto,A.id_regiao,A.nome,B.nome AS funcao,DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_br,
        DATE_ADD(A.data_entrada,INTERVAL 44 DAY) AS data_final1,
        DATE_ADD(A.data_entrada,INTERVAL 89 DAY) AS data_final2,
        DATE_FORMAT(DATE_ADD(A.data_entrada,INTERVAL 44 DAY), '%d/%m/%Y') AS data_final1_br,
        DATE_FORMAT(DATE_ADD(A.data_entrada,INTERVAL 89 DAY), '%d/%m/%Y') AS data_final2_br,
        C.nome AS projeto
        FROM rh_clt AS A
        LEFT JOIN curso AS B ON (A.id_curso=B.id_curso)
        LEFT JOIN projeto AS C ON (A.id_projeto=C.id_projeto)
        WHERE C.id_master = {$usuario['id_master']} AND A.id_regiao = '{$usuario['id_regiao']}' $auxProjeto
        AND A.status NOT IN (SELECT codigo FROM  rhstatus WHERE tipo = 'recisao')
        HAVING 
        data_final1 BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY) OR 
        data_final2 BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 45 DAY)) AS tab ORDER BY $order";
$result = mysql_query($qr);
echo "<!-- \r\n $qr \r\n-->";
$total = mysql_num_rows($result);
$count = 0;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Funcionários em Periodo de Experiência</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
         <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span><?= (isset($_REQUEST['gerar'])) ? $optProjetos[$selProjeto] : 'Todos os Projetos' ?> <small>- Relatório de Funcionários em Experiência</small></h2></div>
            
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                <?php montaSelect(getRegioes(), $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?>
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optProjetos, $selProjeto, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> 
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php if(isset($_POST['gerar'])){ ?>
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Funcionários em Experiência')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button>
                        <?php } ?>
                        <button type="submit" name="todos_projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Gerar Todos os Projetos</button>
                        <button type="submit" name="gerar" id="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form>
            
                <?php if(isset($_POST['gerar'])){ ?>
                    <table id="tbRelatorio" class="table table-condensed table-bordered table-striped table-hover text-sm valign-middle"> 
                        <thead>
                            <tr>
                                <th>Projeto</th>
                                <th>Nome</th>
                                <th>Função</th>
                                <th>Data Entrada</th>
                                <th>Término 45 dias</th>
                                <th>Término 90 dias</th>
                                <th>OBS</th>
                            </tr>
                        </thead>
                         <tbody>
                    <?php
                    while ($row = mysql_fetch_assoc($result)) {
                        $obs = "";
                        if ($row['tipo'] == 1) {
                            if ($row['dias2'] == 0)
                                $obs = "<span style='color:red;'>VENCE HOJE</span> os 90 dias de experiencia";
                            else
                                $obs = "falta(m) {$row['dias2']} dia(s) para vencer os 90 dias de experiencia";
                        }else {
                            if ($row['dias1'] == 0)
                                $obs = "<span style='color:red;'>VENCE HOJE</span> o periodo de experiencia de 45 dias";
                            else
                                $obs = "falta(m) {$row['dias1']} dia(s) para vencer o periodo de experiencia de 45 dias";
                        }
                        ?>
                        <tr class="<?php echo $count++ % 2 ? "even" : "odd" ?> ">
                            <td><?php echo $row['projeto'] ?></td>
                            <td><?php echo $row['nome'] ?></td>
                            <td><?php echo $row['funcao'] ?></td>
                            <td><?php echo $row['data_br'] ?></td>
                            <td><?php echo $row['data_final1_br'] ?></td>
                            <td><?php echo $row['data_final2_br'] ?></td>
                            <td><?php echo $obs ?></td>
                        </tr>
                <?php } ?>
                </tbody>
                    </table>
            
            <?php } ?>
            
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

                $('#projeto').change(function () {
                    $.post("<?= $_SERVER['PHP_SELF'] ?>", {method: 'getClt', projeto: $("#projeto").val()}, function (data) {
                        $("#sclt").html(data);
                    });
                });
                $('#projeto').trigger('change');


                $(".detalhes").click(function () {
                    var id_clt = $(this).data('id-clt');
                    var mes = $(this).data('mes');
                    var ano = $(this).data('ano');
                    var projeto = $(this).data('projeto');
                    $.post('relatorio_transferencia_detalhe.php', {id_clt: id_clt, mes: mes, ano: ano, projeto: projeto, detalhes: true}, function (data) {
                        thickBoxAlert('Detalhes', data, 900, 500);
                    });
                });
            });
        </script>
        
    </body>
</html>
