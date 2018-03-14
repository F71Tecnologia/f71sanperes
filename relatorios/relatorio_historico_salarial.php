<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";
include "../wfunction.php";
include "../classes/FuncoesClass.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();
$objFuncao = new FuncoesClass();

//GERAR TODOS NÃO FUNCIONOU
if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $ano = $_REQUEST['ano'];
    
    $result = $objFuncao->listFuncoesHistoricoSalarial($id_regiao,$ano);
}

$optAno = anosArray();

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Historico Salarial</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Histórico Salárial</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">

                        <input type="hidden" name="hide_projeto" value="<?php echo $projetoSel; ?>" />
                        <input type="hidden" name="hide_unidade" value="<?php echo $unidadeSel; ?>" />                        

                        <div class="form-group" >
                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?>
                            </div>
                            
                            <label for="select" class="col-sm-1 control-label hidden-print" >Ano</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optAno, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($result) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório Cargos e Salarios')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <button type="button" form="formPdf" name="pdf" data-title="Relatório de Histórico Salarial" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                            <?php } ?>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) {
                                ?>
                                <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>    
                        </div>
                    </div>
                
            </form>
                    
            <?php if (!empty($result) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
       
            <table class="table table-striped table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                <thead>
                    <tr>
                        <th>COD</th>
                        <th>FUNÇÃO</th>
                        <th>NIVEL</th>
                        <th>VALOR DE</th>
                        <th>VALOR PARA</th>
                        <th>DIFERENÇA</th>
                        <th>COMPETENCIA</th>
                        <th>MOTIVO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_rel = mysql_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $row_rel['id_curso'] ?></td>
                            <td><?php echo $row_rel['nome'] ?></td>
                            <td><?php echo $row_rel['letra'] . $row_rel['numero']; ?></td>
                            <td>R$ <?php echo number_format($row_rel['salario_antigo'],2,",",".") ?></td>
                            <td>R$ <?php echo number_format($row_rel['salario'],2,",",".") ?></td>
                            <td>R$ <?php echo number_format($row_rel['diferenca'],2,",",".") ?></td>
                            <td><?php echo $row_rel['competencia'] ?></td>
                            <td><?php echo $row_rel['motivo'] ?></td>
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
        
       
    </body>
</html>
