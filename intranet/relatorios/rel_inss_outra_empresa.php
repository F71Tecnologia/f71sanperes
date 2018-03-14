<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
} 
 
include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include("../wfunction.php");
include "../classes_permissoes/acoes.class.php";

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$ACOES = new Acoes();

///MASTER
$master = montaQuery('master', "id_master,razao", "status =1");
$optMaster = array();
foreach ($master as $valor) {
    $optMaster[$valor['id_master']] = $valor['id_master'] . ' - ' . $valor['razao'];
}
$masterSel = (isset($_REQUEST['master'])) ? $_REQUEST['master'] : $usuario['id_master'];



If (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {

    $id_master = $_REQUEST['master'];
    $regiaosql = ($_REQUEST['regiao'] == '') ? '' : "AND A.id_regiao = $_REQUEST[regiao]";
    $projetosql = ($_REQUEST['projeto'] == '') ? '' : "AND A.id_projeto = $_REQUEST[projeto]";
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $dt_referencia = $ano . '-' . $mes . '-01';
    $sql = "SELECT A.id_clt, A.nome,A.desconto_inss, UPPER(A.tipo_desconto_inss) as tipo_desconto_inss,A.valor_desconto_inss,A.id_projeto, A.id_regiao,A.desconto_outra_empresa,
                                B.regiao as nome_regiao, C.nome as nome_projeto, D.nome as funcao
                                FROM rh_clt as A
                                INNER JOIN regioes as B
                                ON B.id_regiao = A.id_regiao
                                INNER JOIN projeto as C
                                ON C.id_projeto = A.id_projeto
                                INNER JOIN curso as D
                                ON D.id_curso = A.id_curso
                                WHERE A.desconto_inss = 1 AND A.status = '10' ";
    if (!isset($_REQUEST['todos_projetos'])) {
        $sql .= "$projetosql  ";
    }
    $sql .= "$regiaosql ORDER BY A.id_regiao,A.nome ";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
}

if($_COOKIE['logado'] == 87){
    echo '<pre>';
    echo $sql;
    echo '</pre>';
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de INSS em Outra Empresa</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de INSS em Outra Empresa</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Master</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div></div>
                        <div class="form-group" >    
                            <label for="select" class="col-sm-4 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                              <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div></div>
                         <div class="form-group" >   
                            <label for="select" class="col-sm-4 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                               <?php echo montaSelect(utf8_encode($optProjeto), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                        
                    <div class="panel-footer text-right hidden-print controls">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'INSS Outras Empresas')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                        ?>                                
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                <table class="table table-striped table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                <?php
                    while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

                        $cor = ($row_rel['status_ferias'] == 'VENCIDA') ? '#f84949' : '';

                        if ($row_rel['id_projeto'] != $projetoAnt) {
                            echo '<tr><td colspan="5" class="projeto text-center text-lg">' . $row_rel['nome_projeto'] . '</td></tr>';
                ?>  
                <thead>
                    <tr>
                        <td>Unidade</td>
                        <td>Nome</td>
                        <td>Função</td>
                        <td>Tipo de desconto</td>
                        <td>Valor</td>
                    </tr>
                </thead>
                    <?php } ?>
                <tbody>
                    <tr>
                        <td align="center"><?php echo $row_rel['nome_projeto'] ?></td>
                        <td><?php echo $row_rel['nome'] ?></td>
                        <td><?php echo $row_rel['funcao'] ?></td>
                        <td align="center"><?php echo $row_rel['tipo_desconto_inss'] ?></td>
                        <td align="center"><?php echo number_format($row_rel['desconto_outra_empresa'], 2, ',', '.') ?></td>
                    </tr>
                    <?php
                        $regiaoAnt = $row_rel['id_regiao'];
                        $projetoAnt = $row_rel['id_projeto'];
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
