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

if (isset($_REQUEST['gravar'])) {
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $id = $_REQUEST['id'];
    $pis = $_REQUEST['pis'];
    $data_pis = $_REQUEST['dataPis'];

    if ($tipo_contratacao == 2) {
        for ($cont2 = 0; !empty($pis[$cont2][0]); $cont2++) {
            $sql_update_pis = "UPDATE rh_clt
                                SET pis = '{$pis[$cont2]}', dada_pis = '{$data_pis[$cont2]}' 
                                WHERE id_clt = '{$id[$cont2]}' AND id_regiao = '{$id_regiao}' AND id_projeto = '{$id_projeto}' 
                                LIMIT 1 ";
            $qr_update_pis = mysql_query($sql_update_pis);
        }
    } else {
        for ($cont2 = 0; !empty($pis[$cont2][0]); $cont2++) {
            $sql_update_pis = "UPDATE autonomo
                                SET pis = '{$pis[$cont2]}', dada_pis = '{$data_pis[$cont2]}'
                                WHERE id_autonomo = '{$id[$cont2]}' AND id_regiao = '{$id_regiao}' AND id_projeto = '{$id_projeto}' AND tipo_contratacao = '{$tipo_contratacao}' 
                                LIMIT 1 ";
            $qr_update_pis = mysql_query($sql_update_pis);
        }
    }
}

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $contratacao = ($tipo_contratacao == "2") ? "clt" : "autonomo";

    if ($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT nome, date_format(dada_pis, '%d/%m/%Y') as data_pisbr, id_clt AS id, locacao
            FROM rh_clt
            WHERE id_regiao = '$id_regiao' AND status = '10' AND pis IN (0,'') ";
    } else {
        $str_qr_relatorio = "SELECT nome, date_format(dada_pis, '%d/%m/%Y') as data_pisbr, id_autonomo AS id, locacao
            FROM autonomo
            WHERE id_regiao = '$id_regiao' AND tipo_contratacao = '$tipo_contratacao' AND pis IN (0,'') AND status = '1' ";
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

        <title>:: Intranet :: Relat�rio de Participantes sem PIS </title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relat�rio de Participantes sem PIS</small></h2></div>
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

                        <div class="form-group" >    
                            <label for="select" class="col-sm-2 control-label hidden-print" >Tipo Contrata��o</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Participantes sem PIS')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
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
                    <table class="table table-striped table-hover text-sm valign-middle" id="tabela">

                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>PIS</th>
                                <th>DATA</th>
                                <th>UNIDADE</th>
                                <?php if ($optSel == 1 || $optSel == 2) { ?>
                                    <th>ARQUIVO</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['nome']; ?></td>
                            <input type="hidden" name="id[]" value="<?php echo $row_rel['id']; ?>"/>
                            <td><input class="form-control" type="text" name="pis[]"/></td>
                            <td><input class="form-control" type="date" name="dataPis[]"/></td>
                            <td><?php echo $row_rel['locacao']; ?></td>
                                <?php if ($optSel == 1) { ?>
                                <td>
                                    <a href="../rh/solicitacaoPisAut.php?pro=<?php echo $id_projeto ?>&id_reg=<?php echo $id_regiao; ?>&Aut=<?php echo $row_rel['id']; ?>" target="_blank">
                                        <img src="icones/icon-doc.gif"/>
                                    </a>
                                </td>
                                    <?php } else if ($optSel == 2) { ?>
                                <td>
                                    <a href="../rh/solicitacaopis.php?pro=<?php echo $id_projeto ?>&id_reg=<?php echo $id_regiao; ?>&clt=<?php echo $row_rel['id']; ?>" target="_blank">
                                        <img src="icones/icon-doc.gif"/>
                                    </a>
                                </td>
                            <?php } ?>
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
