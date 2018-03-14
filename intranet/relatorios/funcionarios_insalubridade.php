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

$regioesOp = getRegioes();

$regiaoSel = $_REQUEST['regiao'];
$projetoSel = $_REQUEST['projeto'];

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['todos_projetos'])) {
    if (isset($_REQUEST['filtrar'])) {
        $projeto = " AND A.id_projeto = $projetoSel ";
    }
    $sql = "SELECT A.id_clt, A.nome, CONCAT(B.id_unidade, ' - ', B.unidade) AS unidade, C.nome AS curso, CONCAT(A.status, ' - ', D.especifica) AS status
	FROM rh_clt AS A
		LEFT JOIN unidade AS B ON (B.id_unidade = A.id_unidade)
		LEFT JOIN curso AS C ON (C.id_curso = A.id_curso)
		LEFT JOIN rhstatus AS D ON (D.codigo = A.status)
	WHERE A.insalubridade = 1 AND A.status < 60 $projeto
	ORDER BY A.nome ASC;";
    $query = mysql_query($sql);
    while($row = mysql_fetch_assoc($query)){
        $array[$row['unidade']][] = $row;
    }
//    echo '<pre>';
//    print_r($array);
//    echo '</pre>';
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Funcionários com Insalubridade</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Relatório de Funcionários com Insalubridade</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Funcionários</div>
                    <div class="panel-body">
                        <div class="form-group" >

                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                               <?php echo montaSelect($regioesOp, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>

                             <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                 <?php echo montaSelect(array("-1" => "Selecione"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                    
                        <div class="panel-footer text-right hidden-print">
                            <?php if(isset($_REQUEST['filtrar'])) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('tbRelatorio', 'Funcionários com Insalubridade')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <button type="submit" name="filtrar" id="filtrar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>               
                    </div> 
                
                
                    <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                        <?php foreach($array AS $unidade => $arrayClt) { ?>
                            <thead>
                                <tr>
                                   <th colspan="4"></th>
                                </tr>
                                <tr>
                                   <th colspan="4" class="text-center"><?= $unidade ?></th>
                                </tr>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">NOME</th>
                                    <th class="text-center">FUNÇÂO</th>
                                    <th class="text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($arrayClt AS $key => $clt) { ?>
                                <tr>
                                    <th class="text-center"><?= $clt['id_clt'] ?></th>
                                    <th class="text-center"><?= $clt['nome'] ?></th>
                                    <th class="text-center"><?= $clt['curso'] ?></th>
                                    <th class="text-center"><?= $clt['status'] ?></th>
                                </tr>
                            <?php } ?>
                            </tbody>
                        <?php } ?>
                    </table>
                
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
                    $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                });
        </script>
      
    </body>
</html>
