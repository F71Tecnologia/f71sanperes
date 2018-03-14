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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$ClasReg = new regiao();
$ClasPro = new projeto();

#SELECIONANDO O MASTAR PARA CARREGAR A IMAGEM
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

#RECEBENDO VARIAVEIS DO GET
$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];
$data_hoje = date('d/m/Y');

#CLASSE PEGANDO OS DADOS DO PROJETO
$ClasPro->MostraProjeto($projeto);
$nome_pro = $ClasPro->nome;

#CLASSE PEGANDO O NOME DA REGIAO
$ClasReg->MostraRegiao($regiao);
$nome_regiao = $ClasReg->regiao;

#SELECIONANDO AS LOCAÇÕES
$relocacao = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$regiao' AND campo1 = '$projeto'") or die(mysql_error());
$num_locacao = mysql_num_rows($relocacao);

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '{$regiao}'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}


if(isset($_POST['gerar'])){
    
    
    $projeto = $_POST['projeto'];    
    
    $qr_rel = mysql_query("SELECT C.nome as funcao, A.id_clt, COUNT(id_clt) as quantidade FROM rh_clt as A
                            INNER JOIN projeto as B
                            ON A.id_projeto = B.id_projeto
                            INNER JOIN curso as C
                            ON A.id_curso = C.id_curso
                            WHERE A.id_regiao = $regiao AND A.id_projeto = $projeto AND (A.status < 60 OR A.status = 70) AND C.id_curso NOT IN(1374,1369,2057,1379)
                            GROUP BY A.id_curso 
                            ORDER BY C.nome");

//    echo "SELECT C.nome as funcao, A.id_clt, COUNT(id_clt) as quantidade FROM rh_clt as A
//                            INNER JOIN projeto as B
//                            ON A.id_projeto = B.id_projeto
//                            INNER JOIN curso as C
//                            ON A.id_curso = C.id_curso
//                            WHERE A.id_regiao = $regiao AND A.id_projeto = $projeto AND A.status < 60
//                            GROUP BY A.id_curso 
//                            ORDER BY C.nome";
    $qr_loc = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
    $row_loc = mysql_fetch_assoc($qr_loc);
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "ativo" => "Atividade por Lotação", "id_form" => "form");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Atividades por Lotação</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Atividades por Lotação</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($projetosOp, $projeto, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                         <?php if(isset($_POST['gerar'])){ ?>
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Atividade por Lotação')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                         <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button
                    </div>
                </div>
            </div>
            </form>
            <?php if(isset($_POST['gerar'])){ ?>

            <table class="table table-striped table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                <thead>
                    <tr>
                        <th>Atividade</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row_rel = mysql_fetch_assoc($qr_rel)){  $total += $row_rel['quantidade']; ?>
                    <tr>
                        <td><?php echo $row_rel['funcao']?></td>
                        <td><?php echo $row_rel['quantidade']?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right">TOTAL DE PROFISSIONAIS: <?= $total ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <?php } ?>
            
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
    </body>
</html>
