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
$optMeses = mesesArray();

$optAnos = anosArray(null, null, array('' => "<< Ano >>"));

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayMovimentos = array(6004,7009,50222);
    $movimentos = implode(",", $arrayMovimentos);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $mes = str_pad($_REQUEST['mes'], 2, "0", STR_PAD_LEFT);
    $ano = $_REQUEST['ano'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
    $sql = "SELECT A.id_folha, B.id_projeto, D.nome AS nome_projeto, B.id_clt, B.nome, C.cod_movimento, C.tipo_movimento, C.nome_movimento, C.valor_movimento, C.`status`
            FROM rh_folha AS A
            LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
            LEFT JOIN rh_movimentos_clt  AS C ON (B.id_clt = C.id_clt AND A.id_folha = C.id_folha)
            LEFT JOIN projeto AS D ON (D.id_projeto = B.id_projeto)
            WHERE A.regiao = '{$id_regiao}' AND A.mes = '{$mes}' AND A.ano = '{$ano}' AND A.status = 3 AND B.status = 3 AND C.cod_movimento IN(6004,7009,50222,7012)
            ORDER BY D.nome, B.nome;";
//    $sql = "SELECT A.*, B.*, C.nome AS nome_projeto
//    FROM (SELECT id_clt, id_projeto, id_regiao, nome, mes, ano, status FROM rh_folha_proc WHERE mes = '{$mes}' AND ano = '{$ano}' AND id_regiao = '{$id_regiao}') AS A
//    LEFT JOIN (SELECT id_clt, cod_movimento, nome_movimento, valor_movimento, status FROM rh_movimentos_clt WHERE  cod_movimento IN({$movimentos})) AS B ON(A.id_clt = B.id_clt)
//    LEFT JOIN projeto AS C ON(A.id_projeto = C.id_projeto)
//    WHERE B.status = 1 AND (A.status < 60 || A.status = 200)
//    GROUP BY A.id_clt";
    
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

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Participantes Ativos </title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Participantes Ativos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group">
                            
                            <label for="select" class="col-sm-1 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                              <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group" >    
                            <label for="select" class="col-sm-1 control-label hidden-print" >Mês</label>
                            <div class="col-sm-2">
                              <?php echo montaSelect($optMeses, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
  
                            <label for="select" class="col-sm-1 control-label hidden-print" >Ano</label>
                            <div class="col-sm-2">
                              <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                            </div>
                        </div>
                                     
                            
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                            
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-primary" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        </div>
                    </div> 
                    
            <table class="table table-striped table-hover text-sm valign-middle" id="tabela">
                
                <thead>
                            <tr>
                                <th colspan="4"><?php echo $projeto['nome'] ?></th>
                            </tr>
                            <tr>
                                <th>PROJETO</th>
                                <th>NOME</th>
                                <th>MOVIMENTO</th>
                                <th>VALOR</th>   
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome_projeto'] ?></td>
                                <td> <?php echo $row_rel['nome']; ?></td>
                                <td> <?php echo $row_rel['nome_movimento']; ?></td>
                                <td align="center"><?php echo $row_rel['valor_movimento']; ?></td>                       
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    
                </table>
                <?php  } ?>
        </div>
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
           $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data){
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");
                
                
                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default:"2"}, null, "funcao");
            });
        </script>
        
    </body>
</html>
