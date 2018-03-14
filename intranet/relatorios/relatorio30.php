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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$optRegiao = getRegioes();
$ACOES = new Acoes();


$opt = array("2" => "CLT", "1" => "Aut�nomo", "3" => "Cooperado", "4" => "Aut�nomo/PJ");
if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $contratacao = ($tipo_contratacao == "2") ? "clt" : "autonomo";

    if ($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT *, date_format(dada_pis, '%d/%m/%Y') as data_pisbr
            FROM rh_clt
            WHERE id_regiao = '$id_regiao' 
            AND status < '60' ";
    } else {
        $str_qr_relatorio = "SELECT *, date_format(dada_pis, '%d/%m/%Y') as data_pisbr
            FROM autonomo 
            WHERE id_regiao = '$id_regiao' 
            AND tipo_contratacao = '$tipo_contratacao'
            AND status = '1' ";
        if ($contratacao != 3) {
            $str_qr_relatorio .= "AND status = '1' ";
        }
    }

    if (!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND id_projeto = '$id_projeto' ";
    }

    $str_qr_relatorio .= "ORDER BY nome";

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

        <title>:: Intranet :: Relat�rio de Participantes por PIS </title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Participantes por PIS</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relat�rio</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Regi�o</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group">    
                            <label for="select" class="col-sm-2 control-label hidden-print" >Tipo Contrata��o</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php
                        ///permiss�o para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tabela">

                        <thead>
                            <tr>
                                <th>C�DIGO</th>
                                <th>MATRICULA</th>
                                <th>NOME</th>
                                <th>PIS</th>
                                <th>DATA</th>
                                <th>LOCA��O</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['campo3'] ?></td>
                                    <td> <?php echo $row_rel['matricula']; ?></td>
                                    <td> <?php echo $row_rel['nome']; ?></td>
                                    <td><?php
                                        if (!empty($row_rel['pis'])) {
                                            echo $row_rel['pis'];
                                        } else {
                                            echo "N�O TEM";
                                        }
                                        ?></td>
                                    <td> <?php echo $row_rel['data_pisbr']; ?></td>
                                    <td> <?php echo $row_rel['locacao']; ?></td>
                                </tr>
                            <?php } ?>
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
                                        var id = $(this).data("id");
                                        var contratacao = $(this).data("contratacao");
                                        var nome = $(this).parents("tr").find("td:first").html();
                                        thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                                    });
                                });
                                $(function () {
                                    $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                                });
</script>

</body>
</html>
