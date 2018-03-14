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
require_once "../classes/LogClass.php";
include "../classes/MovimentoClass.php";

/**
 * OBJETOS
 */
$usuario = carregaUsuario();
$objMovimentos = new Movimentos();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();
$optMeses = mesesArray();
$optAnos = anosArray(null, null, array('' => "<< Ano >>"));

$retornoMovimentos = $objMovimentos->getTodosMovimentos();
$optMovimentos = array();
foreach ($retornoMovimentos as $key => $values) {
    $optMovimentos[$key] = $values['descicao'];
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayMovimentos = array(6004, 7009, 50222);
    $movimentos = implode(",", $arrayMovimentos);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $id_movimento = $_REQUEST['movimento'];

    $mes = str_pad($_REQUEST['mes'], 2, "0", STR_PAD_LEFT);
    $ano = $_REQUEST['ano'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $competencia = $ano . "-" . $mes;
    $sql = "SELECT A.*, B.nome as nome_clt, C.unidade,C.id_unidade FROM rh_movimentos_clt AS A
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                    LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
                    WHERE A.id_regiao = '{$id_regiao}' AND A.mes_mov = '{$mes}'  
                 AND A.ano_mov = '{$ano}' AND A.cod_movimento = '{$id_movimento}' AND A.status IN(5)
                 AND DATE_FORMAT(B.data_entrada,'%Y-%m') <= '{$competencia}'
                 ORDER BY B.id_unidade,B.nome";

    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
$movimentosSel = (isset($_REQUEST['movimento'])) ? $_REQUEST['movimento'] : $movimentosSel;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Movimentos </title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório Por Movimentos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-6">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print" >Mês</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optMeses, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Ano</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="movimento" class="col-sm-3 control-label hidden-print" >Movimento</label>
                            <div class="col-sm-6">
                                <?php echo montaSelect($optMovimentos, $movimentosSel, array('name' => "movimento", 'id' => 'movimento', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                    </div>
                        
                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório por Movimentos" data-id="tabela" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
            </form>
            
            
            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                
                <table class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle" id="tabela">
                    <thead>
                        <tr>
                            <th colspan="4"><?php echo $projeto['nome'] ?></th>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>MOVIMENTO</th>
                            <th>VALOR</th>   
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalMovs = 0; $unidade=""; $totalMovsUni=0; ?>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>

                            <!-- VERIFICA A UNIDADE PARA PRINTAR O CABEÇALHO E RODAPE -->
                            <?php if($unidade != $row_rel['unidade']){ ?>
                                    
                                <!-- VERIFICA A UNIDADE PARA PRINTAR NO TOTALIZADOR -->
                                <?php if($unidade != ""){ ?>
                                    <tr class="info">
                                        <!--<td class="text-right" colspan="3"><?php echo $row_rel['unidade']; ?></td>-->
                                        <td class="text-right" colspan="3">Total:</td>
                                        <td class="text-center" ><?php echo "R$ " . number_format($totalMovsUni, 2, ',', '.'); ?></td>
                                    </tr>
                                <?php $totalMovsUni = 0;} ?>
                                <tr class="info">
                                        <td class="text-right" colspan="4"><?php echo $row_rel['unidade']; ?></td>
                                </tr>
                            <?php $unidade = $row_rel['unidade'];} ?>
                            
                            <!-- LINHAS COM OS REGISTROS -->
                            <tr>
                                <td> <?php echo $row_rel['id_clt']; ?></td>
                                <td> <?php echo $row_rel['nome_clt']; ?></td>
                                <td> <?php echo $row_rel['nome_movimento']; ?></td>
                                <td align="right"><?php echo "R$ " . number_format($row_rel['valor_movimento'], 2, ',', '.'); ?></td>                       
                            </tr>
                            
                            <!-- SOMANDO TOTALIZADORES -->
                            <?php $totalMovs += $row_rel['valor_movimento']; $totalMovsUni += $row_rel['valor_movimento']; ?>
                            
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        
                        <tr class="info">
                            <td class="text-right" colspan="3"><?php echo $unidade; ?></td>
                            <td class="text-center" ><?php echo "R$ " . number_format($totalMovsUni, 2, ',', '.'); ?></td>
                        </tr>
                        
                        <tr class="success">
                            <td class="text-right" colspan="3">Total Geral:</td>
                            <td class="text-center" ><?php echo "R$ " . number_format($totalMovs, 2, ',', '.'); ?></td>
                        </tr>
                    </tfoot>

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
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");


                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");
                <?php if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                    var tabela = $('#tabela').html();
                    var title = $('title').html();
                    $('#tabelaPdf').val(tabela);
                    $('#titlePdf').val(title);
                <?php } ?>
            });
        </script>

    </body>
</html>
