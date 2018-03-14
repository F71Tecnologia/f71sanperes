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
error_reporting(1);
include("../classes/global.php");
$global = new GlobalClass();
$usuario = carregaUsuario();
$REGIOES = new Regioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();
$ACOES = new Acoes();
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;

If (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $regiaosql = ($_REQUEST['regiao'] == '') ? '' : "AND A.id_regiao = $_REQUEST[regiao]";
    $projetosql = ($_REQUEST['projeto'] == '') ? '' : "AND A.id_projeto = $_REQUEST[projeto]";
    $sql = "SELECT A.id_clt, A.matricula, A.nome,B.nome as funcao, A.cpf,  A.pis , C.regiao as nome_regiao, D.nome as nome_projeto, A.id_regiao, A.id_projeto, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada
            FROM rh_clt as A
            INNER JOIN curso as B
            ON A.id_curso = B.id_curso
            INNER JOIN regioes as C
            ON C.id_regiao = A.id_regiao
            INNER JOIN projeto as D
            ON D.id_projeto = A.id_projeto
            WHERE   (A.status < 60 OR A.status = 200 OR A.status = 70) $regiaosql ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql .= "$projetosql ";
    }
    $sql .= "ORDER BY C.id_regiao,D.id_projeto, A.nome";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório para o Controle de Ponto</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório para o Controle de Ponto</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            
                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => "form-control")); ?><span class="loader"></span> 
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => "form-control")); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                    
                        <div class="panel-footer text-right hidden-print controls">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>       
                            <?php if (!empty($qr_relatorio) and isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Controle de Ponto')" value="Exportar para Excel" class="exportarExcel btn btn-success">Exportar para Excel</button>
                            <?php } ?>
                            <?php //permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if ($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Gerar de Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" value="Gerar" id="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>
                   </div> 
               
                <?php if (!empty($qr_relatorio) and isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?> 
                    <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered"> 
                            <thead>
                                <tr class="titulo">
                                    <td>Nome</td>
                                    <td>Função</td>
                                    <td>Data Adimissão</td>
                                    <td>CPF</td>
                                    <td>Unidade</td>
                                    <td>PIS</td>
                                    <td>Matrícula</td>
                                </tr> 
                            </thead>
                           
                            <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                                <tbody>
                                    <tr>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td><?php echo $row_rel['funcao'] ?></td>
                                        <td><?php echo $row_rel['data_entrada'] ?></td>
                                        <td><?php echo $row_rel['cpf'] ?></td>
                                        <td><?php echo $row_rel['nome_projeto'] ?></td>
                                        <td><?php echo $row_rel['pis'] ?></td>
                                        <td><?php echo $row_rel['matricula'] ?></td>
                                    </tr>       

                                <?php 
                                        $regiaoAnt = $row_rel['id_regiao'];
                                        $projetoAnt = $row_rel['id_projeto'];
                                        
                                } ?>

                            </tbody>
                        </table>
                        <?php include('../template/footer.php'); ?>
                    <?php } ?>
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
         <script>
            $(function() {
                $('#master').change(function() {
                    var id_master = $(this).val();
                    $('#regiao').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                    $.ajax({
                        url: '../action.global.php?master=' + id_master,
                        success: function(resposta) {
                            $('#regiao').html(resposta);
                            $('#regiao').next().html('');
                        }
                    });
                    $('#regiao').trigger('change')
                });
                $('#regiao').change(function() {
                    var id_regiao = $(this).val();
                    $('#projeto').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                    $.ajax({
                        url: '../action.global.php?regiao=' + id_regiao,
                        success: function(resposta) {
                            $('#projeto').html(resposta);
                            $('#projeto').next().html('');
                        }
                    });
                });
                $('#master').trigger('change');
            });
        </script>

    </body>
</html>
<!-- A -->