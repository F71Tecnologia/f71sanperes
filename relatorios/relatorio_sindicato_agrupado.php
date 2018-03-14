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
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();
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

    $str_qr_relatorio = "SELECT D.nome as unidade, A.nome, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as dt_admissao,  E.nome as funcao,/*E.letra as funcao_letra, E.numero as funcao_numero,*/ F.nome as sindicato, E.salario 
                            FROM rh_clt as A
                            LEFT JOIN projeto as D
                            ON D.id_projeto = A.id_projeto
                            INNER JOIN curso as E
                            ON E.id_curso = A.id_curso
                            LEFT JOIN rhsindicato as F
                            ON F.id_sindicato = A.rh_sindicato
                            WHERE A.status IN('$status')
                            AND A.id_regiao = '$id_regiao' ";
    if ($sindicato == 2) {
        $str_qr_relatorio .= "AND A.rh_sindicato = 0 ";
    } else if ($sindicato == 1) {
        $str_qr_relatorio .= "AND A.rh_sindicato <> 0 ";
    }
    $str_qr_relatorio .= "ORDER BY F.nome, A.nome";
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

        <title>:: Intranet :: Relatório de Sindicato Agrupado</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Sindicato Agrupado</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                        <!-- <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Região:</label> <?php //echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao'));  ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php //echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto'));  ?> </p>
                        <p><label class="first">Tipo Contratação:</label> <?php //echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo'));  ?> </p>
                            -->
                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print">Sindicato</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => "form-control")); ?><span class="loader"></span>
                            </div>

                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success">Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Sindicatos Agrupados" data-id="tabela" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button> 
                            <input type="hidden" id="data_xls" name="data_xls" value="">
                        <?php } ?>
                            
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar de Todos os Projetos</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span>Gerar</button>
                    </div>
                </div> 

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>

                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                            $class = ($cont++ % 2 == 0) ? "even" : "odd" ?>
                            <?php if ($auxSind != $row_rel['sindicato']) { ?>
                                <thead>
                                    <tr>
                                        <th colspan="4" style="text-align: center"><?php echo $row_rel['sindicato'] ?></th>
                                    </tr>
                                    <tr>
                                        <th>NOME</th>
                                        <th>FUNÇÃO</th>
                                        <th>DATA DE ADMISSÃO</th>   
                                        <th>SINDICATO</th>
                                    </tr>
                                </thead>
                                <?php $auxSind = $row_rel['sindicato']; } ?>

                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['funcao'] . " " . $row_rel["funcao_letra"] . $row_rel["funcao_numero"]; ?></td>
                                <td align="center"><?php echo $row_rel['dt_admissao']; ?></td>                       
                                <td><label title="<?php echo $row_rel['sindicato']; ?>"><?php echo substr($row_rel['sindicato'], 0, strpos($row_rel['sindicato'], " ")); ?></label></td>
                            </tr>                                
                    <?php } ?>
                    </table>

                <?php } ?>
                <?php include('../template/footer.php'); ?>
            </form>
            <form style="display: none" action="exportTablePdf.php" method="post" id="formPdf">
                <input type="text" name="titlePdf" id="titlePdf" value=""/>
                <textarea name="tabelaPdf" id="tabelaPdf" value="" ></textarea>
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
            $(function () {
                $(".bt-image").on("click", function () {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
           
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");

                 <?php if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                    var tabela = $('#tabela').html();
                    var title = $('title').html();
//                    console.log(tabela);
                    $('#tabelaPdf').val(tabela);
                    $('#titlePdf').val(title);
                 <?php } ?>
            });
        </script>

    </body>
</html>
<!-- A -->