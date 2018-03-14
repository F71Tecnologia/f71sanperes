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

    $sql = "SELECT A.id_bolsista, B.nome nome_clt, A.nome, DATE_FORMAT(IF(A.dn IS NULL,'0000-00-00',A.dn), '%d/%m/%Y') dn, ddir FROM (
                SELECT dpp.id_bolsista, B.pai nome, B.data_nasc_pai dn, ddir_pai ddir FROM dependentes dpp LEFT JOIN rh_clt B ON dpp.id_bolsista = B.id_clt WHERE dpp.ddir_pai = 1 AND B.pai != '' AND dpp.contratacao = 2 UNION
                SELECT dpp.id_bolsista, B.mae nome, B.data_nasc_mae dn, ddir_mae ddir FROM dependentes dpp LEFT JOIN rh_clt B ON dpp.id_bolsista = B.id_clt WHERE dpp.ddir_mae = 1 AND B.mae != '' AND dpp.contratacao = 2 UNION
                SELECT dpp.id_bolsista, B.nome_conjuge nome, B.data_nasc_conjuge dn, ddir_conjuge ddir FROM dependentes dpp LEFT JOIN rh_clt B ON dpp.id_bolsista = B.id_clt WHERE dpp.ddir_conjuge = 1 AND B.nome_conjuge != '' AND dpp.contratacao = 2 UNION
                SELECT dp1.id_bolsista, dp1.nome1 nome, dp1.data1 dn, dp1.nao_ir_filho1 ddir FROM dependentes dp1 WHERE (dp1.nao_ir_filho1 = 0 || dp1.nao_ir_filho1 IS NULL) AND dp1.nome1 != '' AND dp1.data1 != '0000-00-00' AND dp1.contratacao = 2 UNION
                SELECT dp2.id_bolsista, dp2.nome2 nome, dp2.data2 dn, dp2.nao_ir_filho2 ddir FROM dependentes dp2 WHERE (dp2.nao_ir_filho2 = 0 || dp2.nao_ir_filho2 IS NULL) AND dp2.nome2 != '' AND dp2.data2 != '0000-00-00' AND dp2.contratacao = 2 UNION
                SELECT dp3.id_bolsista, dp3.nome3 nome, dp3.data3 dn, dp3.nao_ir_filho3 ddir FROM dependentes dp3 WHERE (dp3.nao_ir_filho3 = 0 || dp3.nao_ir_filho3 IS NULL) AND dp3.nome3 != '' AND dp3.data3 != '0000-00-00' AND dp3.contratacao = 2 UNION
                SELECT dp4.id_bolsista, dp4.nome4 nome, dp4.data4 dn, dp4.nao_ir_filho4 ddir FROM dependentes dp4 WHERE (dp4.nao_ir_filho4 = 0 || dp4.nao_ir_filho4 IS NULL) AND dp4.nome4 != '' AND dp4.data4 != '0000-00-00' AND dp4.contratacao = 2 UNION
                SELECT dp5.id_bolsista, dp5.nome5 nome, dp5.data5 dn, dp5.nao_ir_filho5 ddir FROM dependentes dp5 WHERE (dp5.nao_ir_filho5 = 0 || dp5.nao_ir_filho5 IS NULL) AND dp5.nome5 != '' AND dp5.data5 != '0000-00-00' AND dp5.contratacao = 2 UNION
                SELECT dp6.id_bolsista, dp6.nome6 nome, dp6.data6 dn, dp6.nao_ir_filho6 ddir FROM dependentes dp6 WHERE (dp6.nao_ir_filho6 = 0 || dp6.nao_ir_filho6 IS NULL) AND dp6.nome6 != '' AND dp6.data6 != '0000-00-00' AND dp6.contratacao = 2) A
            LEFT JOIN rh_clt B ON A.id_bolsista = B.id_clt
            WHERE B.id_regiao = $regiao AND B.id_projeto = $projeto AND (B.status < 60 || B.status = 200 || B.status = 70) AND B.imposto_renda != 'não'
            ORDER BY A.id_bolsista";
    
    if($_COOKIE['logado'] == 179){
        echo "QUERY::: " . $sql . "<br>";
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

        <title>:: Intranet :: Relatório de Dependentes de IRRF</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Dependentes de IRRF</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-1 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?><span class="loader"></span>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div><br><br><br>
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>

                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span>Gerar</button>
                            <?php if (!empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success">Exportar para Excel</button></p>    
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                        </div>
                    </div> 
                </div>
                <?php if (!empty($arr) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                    <div id="tabela">
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                            <thead>
                                <tr class="titulo">
                                    <th class="text-center">COD. CLT</th>
                                    <th class="text-center">NOME CLT</th>
                                    <th class="text-center">DEPENDENTE</th>
                                    <th class="text-center">NASCIMENTO</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <?php
                                foreach ($arr as $key => $value) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd";
                                ?>
                                    <tr class="<?php echo $class ?>">
                                        <td class="text-center"><?php echo $value['id_bolsista']; ?></td>
                                        <td> <?php echo $value['nome_clt']; ?></td>
                                        <td> <?php echo $value['nome']; ?></td>
                                        <td> <?php echo $value['dn']; ?></td>
                                    </tr>       
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php include('../template/footer.php'); ?>
            </div>
        <?php } ?>

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