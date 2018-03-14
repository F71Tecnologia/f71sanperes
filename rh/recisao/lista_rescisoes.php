<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../../login.php'>Logar</a>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include('../../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();
$ACOES = new Acoes();

$dt_ini = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini']:date("d/m/Y",  strtotime('-60 days'));
$dt_fim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim']:date("d/m/Y");
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

//if(isset($_REQUEST['visualizar']) && !empty($_REQUEST['visualizar'])){
    $filtroProjeto = null;
    $auxPesquisa = null;
    
    if ($_REQUEST['projeto'] != '-1' && $_REQUEST['projeto'] != '') {
        $filtroProjeto = " AND A.id_projeto = {$_REQUEST['projeto']}";
    }
    
    if(!empty($_REQUEST['pesquisa'])){
        $valorPesquisa = explode(' ',$_REQUEST['pesquisa']);
        foreach ($valorPesquisa as $valuePesquisa) {
            $pesquisa[] .= "A.nome LIKE '%".$valuePesquisa."%'";
        }
        $pesquisa = implode(' AND ',$pesquisa);
        $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
    }
    
    $regiao = (!empty(($_REQUEST['regiao']))) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
    
    $dt_iniCon = converteData($dt_ini);
    $dt_fimCon = converteData($dt_fim);
    
    $sql_demissao = "SELECT A.id_recisao,A.id_clt,A.nome,C.nome as projeto,
                            DATE_FORMAT(A.data_demi, '%d/%m/%Y') as data_demiBR, 
                            A.total_liquido 
                        FROM rh_recisao AS A
                        LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                        LEFT JOIN projeto AS C ON (B.id_projeto = C.id_projeto)
                        WHERE 
                            A.`status` = 1 AND
                            A.rescisao_complementar = 0 AND
                            A.id_regiao = {$regiao} AND
                            A.data_demi BETWEEN '{$dt_iniCon}' AND '{$dt_fimCon}'
                            $filtroProjeto $auxPesquisa
                    ORDER BY A.nome ASC;";
    echo "<!-- SQL Rescindidos: {$sql_demissao} -->";
    $rs_demissao = mysql_query($sql_demissao);
    $total_demi = mysql_num_rows($rs_demissao);
//}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Rescisções</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Rescisões</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
            
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                        
                        <div class="form-group datas">
                            <label for="data_ini" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Período</label>
                            <div class="col-lg-9">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="data_ini" id="data_ini" readonly="true" placeholder="Inicio" value="<?php echo $dt_ini ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="data_fim" id="data_fim" readonly="true" placeholder="Fim" value="<?php echo $dt_fim ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label hidden-print">Opcional</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="pesquisa" placeholder="Nome, Matricula, CPF" value="<?php echo $_REQUEST['pesquisa']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <button type="submit" name="visualizar" id="visualizar" value="visualizar" class="btn btn-success"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
            </form>
            
            <?php /*if(isset($_REQUEST['visualizar']) && !empty($_REQUEST['visualizar'])){*/ if($total_demi > 0){ ?>
            <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>        
            <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                <thead>
                    <tr>
                        <th></th>
                        <th>COD</th>
                        <th>NOME</th>
                        <th>PROJETO</th>
                        <th>DATA</th>
                        <th>RESCISÃO</th>
                        <th>COMPLEMENTAR</th>
                        <th>ADD</th>
                        <th>VALOR</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row_rel = mysql_fetch_assoc($rs_demissao)) { ?>
                    <tr>
                        <td>--</td>
                        <td><?php echo $row_rel['id_clt'] ?></td>
                        <td><?php echo $row_rel['nome'] ?></td>
                        <td><?php echo $row_rel['projeto'] ?></td>
                        <td><?php echo $row_rel['data_demiBR'] ?></td>
                        <td>--</td>
                        <td>--</td>
                        <td>--</td>
                        <td>R$ <?php echo $row_rel['total_liquido'] ?></td>
                        <td>
                            <?php if ($ACOES->verifica_permissoes(82)){ ?>
                            <a href="javascript:;" title="Desprocessar Rescisão" data-recisao="<?php echo $row_rescisao[0]; ?>" data-regiao="<?php echo $_GET['regiao']; ?>" data-clt="<?php echo $row_demissao[0]; ?>" class="remove_recisao"><i class="fa fa-trash btn btn-danger"></i></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php } else { ?>
                <div class="alert alert-dismissable alert-warning">
                    <!--button type="button" class="close" data-dismiss="alert">×</button-->
                    <strong>Ops!</strong> Nenhum registro encontrado para a busca selecionada.
                </div>
            <?php } //} ?>
            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script>
            $(function() {
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    /*yearRange: '2005:c+4'*/
                });
                
                //$("#form1").validationEngine();
                var id_destination = "projeto";
                
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

            });
        </script>
    </body>
</html>
