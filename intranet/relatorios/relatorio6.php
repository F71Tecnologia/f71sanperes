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

$opt = array("1"=>"ASSEGURADOS ATIVOS", "0"=>"DESATIVADOS E NÃO ASSEGURADO");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_status = $_REQUEST['tipo'];

    $str_qr_relatorio = "SELECT A.nome,A.rg, A.cpf, date_format(A.data_nasci, '%d/%m/%Y') as data_nascibr,
        date_format(A.data_entrada, '%d/%m/%Y') AS data_entradabr, date_format(data_saida, '%d/%m/%Y') AS data_saidabr
        FROM autonomo AS A
        INNER JOIN apolice AS B
        ON B.id_apolice = A.apolice
        WHERE A.id_regiao = '$id_regiao' AND A.tipo_contratacao = '1' AND status = '{$tipo_status}' ";
    
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
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
        
        <title>:: Intranet :: Relatório de Assegurados</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Assegurados</small></h2></div>
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
                        
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Status</label>
                            <div class="col-sm-3">
                              <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                if($ACOES->verifica_permissoes(85)) { ?>
                            <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>
                    </div>
                
                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>IDENTIDADE</th>
                                <th>CPF</th>
                                <th>DATA NASCIMENTO</th>
                                <th>DATA ENTRADA</th>
                                <th>DATA SAIDA</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                            <tr class="<?php echo $class ?>">
                                <td><?php echo $row_rel['nome'] ?></td>
                                <td> <?php echo $row_rel['rg']; ?></td>
                                <td> <?php echo $row_rel['cpf']; ?></td>
                                <td> <?php echo $row_rel['data_nascibr']; ?></td>
                                <td> <?php echo $row_rel['data_entradabr']; ?></td>
                                <td> <?php echo $row_rel['data_saidabr']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <?php  } ?>
             
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
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");     
                
                $('#exportar').click(function(){
                   $('#form').attr('action','exporta_planilha_funcionarios.php');
                   $('#form').submit();
                });
                
                 $('.gera').click(function(){
                      $('#form').attr('action','');
                      if($(this).val() === 'Gerar de Todos Projetos'){
                          $('#projeto').val('-1');
                      }
                 });
            });
        </script>
        
    </body>
</html>
