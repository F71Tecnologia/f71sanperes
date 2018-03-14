<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $tipo = $_REQUEST['tipo'];
    if ($tipo == 1) {
    $sql = "SELECT A.id_clt id, A.nome, C.nome curso, B.nome deficiencia
            FROM rh_clt A
            LEFT JOIN deficiencias B ON A.deficiencia = B.id
            LEFT JOIN curso C ON A.id_curso = C.id_curso
            WHERE A.id_regiao = $regiao AND A.id_projeto = $projeto AND A.deficiencia IN (1,2,3,4,5,6,7)";
    } else if ($tipo == 2) {
    $sql = "SELECT A.id_dependentes id, A.nome1 nome, DATE_FORMAT(data1, '%d/%m/%Y') dn
            FROM dependentes A
            WHERE portador_def1 = 1 AND id_regiao = $regiao AND id_projeto = $projeto
            UNION
            SELECT A.id_dependentes, A.nome2 nome, DATE_FORMAT(data2, '%d/%m/%Y') dn
            FROM dependentes A
            WHERE portador_def2 = 1 AND id_regiao = $regiao AND id_projeto = $projeto
            UNION
            SELECT A.id_dependentes, A.nome3 nome, DATE_FORMAT(data3, '%d/%m/%Y') dn
            FROM dependentes A
            WHERE portador_def3 = 1 AND id_regiao = $regiao AND id_projeto = $projeto
            UNION
            SELECT A.id_dependentes, A.nome4 nome, DATE_FORMAT(data4, '%d/%m/%Y') dn
            FROM dependentes A
            WHERE portador_def4 = 1 AND id_regiao = $regiao AND id_projeto = $projeto
            UNION
            SELECT A.id_dependentes, A.nome5 nome, DATE_FORMAT(data5, '%d/%m/%Y') dn
            FROM dependentes A
            WHERE portador_def5 = 1 AND id_regiao = $regiao AND id_projeto = $projeto
            UNION
            SELECT A.id_dependentes, A.nome6 nome, DATE_FORMAT(data6, '%d/%m/%Y') dn
            FROM dependentes A
            WHERE portador_def6 = 1 AND id_regiao = $regiao AND id_projeto = $projeto";
    }
    

    $query = mysql_query($sql);
    while ($row = mysql_fetch_assoc($query)) {
        $arr[] = $row;
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Deficiência</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Deficiência</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-3 control-label hidden-print" >Tipo</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione o Tipo »", "1" => "Funcionário", "2" => "Dependente"), $tipo, array('name' => "tipo", 'id' => 'tipo', 'class' => 'form-control')); ?>
                            </div>
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success">Exportar para Excel</button>    
                                <button type="button" form="formPdf" name="pdf" data-title="Participantes com Deficiência" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button> 
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span>Gerar</button>
                        </div>
                    </div> 
                <?php if (!empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) {?>
                   
                        <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered"> 
                            <thead>
                                <?php if ($tipo == 1) { ?>
                                <tr class="titulo">
                                    <th class="text-center">COD</th>
                                    <th class="text-center">NOME</th>
                                    <th class="text-center">FUNÇÃO</th>
                                    <th class="text-center">DEFICIÊNCIA</th>
                                </tr>
                                <?php } else if ($tipo == 2) { ?>
                                <tr class="titulo">
                                    <th class="text-center">COD</th>
                                    <th class="text-center">NOME</th>
                                    <th class="text-center">DATA DE NASCIMENTO</th>
                                </tr>
                                <?php }?>
                            </thead>
                            <tbody>
                                <?php
                                if ($tipo == 1) {
                                foreach ($arr as $key => $value) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td class="text-center"><?php echo $value['id']; ?></td>
                                        <td> <?php echo $value['nome']; ?></td>
                                        <td> <?php echo $value['curso']; ?></td>
                                        <td> <?php echo $value['deficiencia']; ?></td>
                                    </tr>       
                                <?php }
                                } else if ($tipo == 2) {
                                foreach ($arr as $key => $value) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";    
                                ?>
                                    <tr class="<?php echo $class ?>">
                                        <td class="text-center"><?php echo $value['id']; ?></td>
                                        <td> <?php echo $value['nome']; ?></td>
                                        <td> <?php echo $value['dn']; ?></td>
                                    </tr>    
                                <?php } }?>
                            </tbody>
                        </table>
                    
                <?php } else if (empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div class="alert alert-dismissable alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>Desculpe,</strong> não encontramos nenhum registro.
                    </div>
                    <?php } ?>
                    <?php include('../template/footer.php'); ?>
                    </div>
                

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

<script type="text/javascript" src="../resources/js/jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/jspdf.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/libs/base64.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/tableExport.js"></script>
<script type="text/javascript" src="../resources/js/jspdf/jquery.base64.js"></script>

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

//                $('#pdf').on('click', function(){
//                    var tabela = $('#tbRelatorio').html();
//                    localStorage['tabela'] = tabela;
//                    console.log(localStorage['tabela']);
////                    location.href="exportTablePdf.php";
//                });
                                    });
</script>
<?php if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
    <script>
        var tabela = $('#tabela').html();
        var title = $('title').html();
        //                    console.log(tabela);
        $('#tabelaPdf').val(tabela);
        $('#titlePdf').val(title);
    </script>
<?php } ?>
</body>
</html>

<!-- A -->