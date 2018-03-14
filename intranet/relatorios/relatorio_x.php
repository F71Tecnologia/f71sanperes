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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $condicao = (!isset($_REQUEST['todos_projetos'])) ? "(b.id_unidade_de = '$id_unidade' or b.id_unidade_para = '$id_unidade') and" : '';

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $sql = "SELECT id_clt,matricula,nome,pis,campo1 AS ctps,
            (SELECT nome FROM curso WHERE curso.id_curso = rh_clt.id_curso) AS funcao,
            (SELECT nome FROM projeto WHERE projeto.id_projeto = rh_clt.id_projeto) AS projeto,
            (SELECT unidade FROM unidade WHERE unidade.id_unidade = rh_clt.id_unidade) AS unidade,
            DATE_FORMAT(data_entrada, '%d/%m/%Y') AS data_entrada
            FROM rh_clt
            WHERE id_regiao = '{$id_regiao}' AND id_projeto = '{$id_projeto}' AND (STATUS <60 OR STATUS = 200)
            ORDER BY nome";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// array de anos //////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$arrayAnos[-1] = '« Selecione o Ano »';
for ($i = date('Y'); $i >= date('Y') - 10; $i--) {
    $arrayAnos[$i] = $i;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: CLT Por PIS, CTPS, Função...</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - CLT Por PIS, CTPS, Função...</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >

                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                    </div>

                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'CLT por PIS, CTPS, Função... ')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>    
                    <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">

                        <thead>
                            <tr>
                                <th colspan="8"><?= (!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr>
                                <th>MATRÍCULA</th>
                                <th>NOME</th>
                                <th>PIS</th>
                                <th>CTPS</th>
                                <th>FUNÇÃO</th>
                                <th>PROJETO</th>
                                <th>UNIDADE</th>
                                <th>DATA DE ADMISSÃO</th>  
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                <tr class="<?php echo $class ?>">
                                    <td align="center"><?php echo $row_rel['matricula'] ?></td>
                                    <td><?php echo $row_rel['nome'] ?></td>
                                    <td><?php echo $row_rel['pis']; ?></td>
                                    <td><?php echo $row_rel['ctps']; ?></td>
                                    <td> <?php echo $row_rel['funcao']; ?></td>
                                    <td align="center"> <?php echo $row_rel['projeto']; ?></td>
                                    <td align="center"> <?php echo $row_rel['unidade']; ?></td>
                                    <td align="center"> <?php echo $row_rel['data_entrada']; ?></td>                      
                                </tr>                                
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7"><strong>TOTAL:</strong></td>
                                <td align="center"><?php echo $num_rows ?></td>
                            </tr>
                        </tfoot>
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
                            });

                            $(document).ready(function () {
                                // instancia o validation engine no formulário
                                $("#form1").validationEngine();
                            });
                            checkDate = function (field) {
                                var date = field.val();
                                if (date == -1) {
                                    return 'Selecione uma Data';
                                }
                            };
</script>

</body>
</html>
