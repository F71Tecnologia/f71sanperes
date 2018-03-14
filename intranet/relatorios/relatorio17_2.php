<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include("../conn.php");
include("../classes/regiao.php");
include("../classes/projeto.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$optRegiao = getRegioes();$ACOES = new Acoes();

$opt = array("2"=>"CLT","1"=>"Autônomo","3"=>"Cooperado","4"=>"Autônomo/PJ");

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    $str_qr_relatorio = "SELECT A.id_autonomo, A.nome, SUM(B.quota) AS soma_quota, COUNT(id_folha_pro) AS count_quota 
                        FROM autonomo AS A
                        LEFT JOIN folha_cooperado AS B
                        ON B.id_autonomo = A.id_autonomo
                        WHERE B.quota != '0.00'
                        AND A.id_regiao = '{$id_regiao}' ";
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "GROUP BY A.id_autonomo
                        ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
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
        
        <title>:: Intranet :: Relatório de Quotas Pagas</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
         <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span>Informação Bancária dos Participantes</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório de Quotas Pagas</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Região</label>
                            <div class="col-sm-5">
                               <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Projeto</label>
                            <div class="col-sm-5">
                               <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?> <span class="loader"></span>
                            </div>
                        </div>
                       </div>
                    
                    <div class="panel-footer text-right hidden-print">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar Para Excel</button>
                        <?php }?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                       if($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Gerar Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary" ><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form> 
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                
                <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle table-bordered"> 
                        <thead>
                            <tr>
                                <th colspan="4"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>NOME</th>
                                <th>TOTAL PAGO</th>
                                <th>QUANTIADE DE PARCELAS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo "R$ ".number_format($row_rel['soma_quota'],2,',','.'); ?></td>
                                <td align="center"><?php echo $row_rel['count_quota']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
            
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
                $(".bt-image").on("click", function() {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
        
    </body>
</html>
