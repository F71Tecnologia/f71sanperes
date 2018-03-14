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

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

///MASTER
$master = montaQuery('master', "id_master,razao", "status = 1");
$optMaster = array("-1"=>"« Selecione »");
foreach ($master as $valor) {
    $optMaster[$valor['id_master']] = $valor['id_master'] . ' - ' . $valor['razao'];
}

$masterSel = (isset($_REQUEST['master'])) ? $_REQUEST['master'] : $usuario['id_master'];
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;



If (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    
    $id_master = $_REQUEST['master'];
    $regiaosql = (empty($_REQUEST['regiao'])) ? '' : "AND B.id_regiao = $_REQUEST[regiao]";
    $projetosql = (empty($_REQUEST['projeto'])) ? '' : "AND C.id_projeto = $_REQUEST[projeto]";
    
    $sql = "SELECT A.id_master, A.razao, 
                                        B.id_regiao, 
                                        C.nome AS nome_projeto,
                                        D.id_projeto, D.id_clt, B.regiao, 
                                        D.nome AS nome_clt, DATE_FORMAT(D.data_nasci,'%d/%m/%Y') AS data_nasci, IF(D.mae = '','-',D.mae) AS mae, IF(D.pai = '','-',D.pai) AS pai,
                                        D.municipio_nasc, D.rg, DATE_FORMAT(D.data_rg,'%d/%m/%Y') AS data_rg, DATE_FORMAT(D.data_emissao,'%d/%m/%Y') AS data_emissao,
                                        D.cpf, CONCAT(D.endereco, ' - ',D.bairro,' - ',D.cidade,', CEP: ',D.cep) AS endereco, DATE_FORMAT(D.data_entrada,'%d/%m/%Y') AS data_entrada,
                                        D.orgao, D.conselho, 
                                        E.nome AS nome_curso,
                                        E.salario
                                    FROM master as A
                                    INNER JOIN regioes as B
                                    ON B.id_master = A.id_master
                                    INNER JOIN projeto as C
                                    ON C.id_regiao = B.id_regiao
                                    INNER JOIN rh_clt as D
                                    ON D.id_projeto = C.id_projeto
                                    INNER JOIN curso as E
                                    ON E.id_curso = D.id_curso
                                    WHERE A.id_master = '$id_master' $regiaosql ";
    if(!isset($_REQUEST['todos_projetos'])) {
        $sql .= "$projetosql  ";
    }
    $sql .= "AND B.status = 1 AND B.status_reg = 1 AND B.status = 1 AND B.status_reg = 1 
                                        AND D.status <60
                                    ORDER BY regiao,C.nome,D.nome;
                                    ";
    
    $qr_relatorio = mysql_query($sql);
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de CNES</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de CNES</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <div class="panel-body">
                            <div class="form-group" >

                                <label for="select" class="col-sm-2 control-label hidden-print" >Master</label>
                                <div class="col-sm-5">
                                  <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print" >Região</label>
                                <div class="col-sm-2">
                                  <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                </div>
                            </div>

                            <div class="form-group" >    
                                <label for="select" class="col-sm-2 control-label hidden-print" >Projeto</label>
                                <div class="col-sm-3">
                                  <?php echo montaSelect(array("0" => "« Projeto »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> <span class="loader"></span> 
                                </div>
                            </div>
                        </div>

                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                            <div class="panel-footer text-right hidden-print controls">
                                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório CNES')" value="Exportar para Excel" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <?php } ?>
                                <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                                    if($ACOES->verifica_permissoes(85)) { ?>
                                    <button type="submit" name="todos_projetos" value="gerar de todos os projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                                <?php } ?>
                                    <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                            </div>
                        </div>
                    
                    <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
            <table class="table table-striped table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                
                 <?php
                    while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

                        if ($row_rel['id_regiao'] != $regiaoAnt) {
                            echo '<tr><td colspan="14" height="50" class="regiao">' . $row_rel['regiao'] . '</td></tr>';
                        }
                        if ($row_rel['id_projeto'] != $projetoAnt) {
                            echo '<tr><td colspan="14" height="80" class="projeto">' . $row_rel['nome_projeto'] . '</td></tr>';
                            ?>           
                            <tr>
                                <td>Nome</td>
                                <td>Função</td>
                                <td>Salário</td>
                                <td>Data de nascimento</td>
                                <td>Mãe</td>
                                <td>Pai</td>
                                <td>Municipio de Nasc.</td>
                                <td>RG</td>
                                <td>DT expedição (RG)</td>
                                <td>Nº do conselho</td>
                                <td>DT de emissão</td>
                                <td>CPF</td>
                                <td>Endereço</td>
                                <td>Dt de contratação</td>
                            </tr>
                        <?php } ?>  

                        <tr>
                            <td><?php echo $row_rel['nome_clt'] ?></td>
                            <td><?php echo $row_rel['nome_curso'] ?></td>
                            <td><?php echo number_format($row_rel['salario'],2,',','.') ?></td>
                            <td><?php echo $row_rel['data_nasci'] ?></td>
                            <td><?php echo $row_rel['mae'] ?></td>
                            <td><?php echo $row_rel['pai'] ?></td>
                            <td><?php echo $row_rel['municipio_nasc'] ?></td>
                            <td><?php echo $row_rel['rg'] ?></td>
                            <td><?php echo $row_rel['data_rg'] ?></td>
                            <td><?php echo $row_rel['conselho'] ?></td>
                            <td><?php echo $row_rel['data_emissao'] ?></td>
                            <td><?php echo $row_rel['cpf'] ?></td>
                            <td><?php echo $row_rel['endereco'] ?></td>
                            <td><?php echo $row_rel['data_entrada'] ?></td>
                        </tr>

                        <?php
                        $regiaoAnt = $row_rel['id_regiao'];
                        $projetoAnt = $row_rel['id_projeto'];
                    } ?>
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
                $('#master').ajaxGetJson("../methods.php", {method: "carregaRegioes", default: 2}, null, "regiao");
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
        
    </body>
</html>
