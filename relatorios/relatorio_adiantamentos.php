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
$anoAtual = date('Y');
for($i = 2009; $i <= $anoAtual;$i++){
    $optAno[$i] = $i;
}
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();
$ACOES = new Acoes();

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    
    $ano = $_REQUEST['ano'];
    if(!isset($_REQUEST['todos_projetos'])) {
        $projeto = " AND A.projeto = {$_REQUEST['projeto']} ";
    }
    
    $sql = "SELECT A.id_clt, A.nome, CONCAT(D.nome,' ',D.letra,D.numero) AS funcao, C.data_movimento, C.valor_movimento
            FROM rh_ferias A 
                LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
                LEFT JOIN rh_movimentos_clt C ON A.id_clt=C.id_clt AND C.status AND C.cod_movimento='80030' AND C.id_clt=A.id_clt
                LEFT JOIN curso D ON (D.id_curso = B.id_curso)
            WHERE  A.status AND C.valor_movimento > 0 $projeto AND A.ano = $ano AND (B.status < 60 OR B.status = 200 OR B.status = 70)
            GROUP BY A.id_clt
            ORDER BY C.data_movimento";
    $query = mysql_query($sql);
  
    while($rows = mysql_fetch_assoc($query)){
        $arr[] = $rows;
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório por Adiantamento de 13º</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório por Adiantamento de 13º</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >

                            <!--<input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                            <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                            <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                            <p><label class="first">Tipo Contratação:</label> <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo')); ?> </p>
                            -->
                                <label for="select" class="col-sm-3 control-label hidden-print" >Região</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao' , 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span> 
                                </div>
                            </div>
                                
                            <div class="form-group">
                                <label for="select" class="col-sm-3 control-label hidden-print">Competência</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect($optAno, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?><span class="loader"></span>
                                </div>

                                <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            </div>
                        </div>
                       

                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($query) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success">Exportar para Excel</button>
                                <button type="button" form="formPdf" name="pdf" data-title="Adiantamento do 13º" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>

                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) { ?>
                                <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar de Todos os Projetos</button>
                            <?php } ?>
                                <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>
                    </div>
                     
                
                    <?php if (!empty($query) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>

                        <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered"> 
                                <thead>
                                    <tr class="titulo">
                                        <th>CÓDIGO</th>
                                        <th>NOME</th>
                                        <th>FUNÇÃO</th>
                                        <th>DATA DO MOVIMENTO</th>
                                        <th>VALOR DO ADIANTAMENTO</th>
                                    </tr> 
                                </thead>

                                <?php foreach($arr AS $key => $value) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                                    <tbody>
                                        <tr class="<?php echo $class ?>">
                                            <td><?php echo $value['id_clt'] ?></td>
                                            <td> <?php echo $value['nome']; ?></td>
                                            <td> <?php echo $value['funcao']; ?></td>
                                            <td> <?php echo $value['data_movimento']; ?></td>
                                            <td style="text-align:center"> <?php echo formataMoeda($value['valor_movimento'], 'R$'); ?></td>
                                        </tr>       

                                    <?php } ?>

                                </tbody>
                            </table>

                            <?php include('../template/footer.php'); ?>

                        <?php } ?>

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
            $(function() {
                $(".bt-image").on("click", function() {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
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
<!-- A -->