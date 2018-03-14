<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";

$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$ACOES = new Acoes();

///MASTER
$master = montaQuery('master', "id_master,razao", "status =1");
$optMaster = array();
foreach ($master as $valor) {
    $optMaster[$valor['id_master']] = $valor['id_master'] . ' - ' . $valor['razao'];
}
$masterSel = (isset($_REQUEST['master'])) ? $_REQUEST['master'] : $usuario['id_master'];
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

        <title>:: Intranet :: Busca por PIS</title>
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Busca por PIS</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Busca</div>
                    <div class="panel-body">
                        <div class="form-group" >

                            <label for="select" class="col-sm-2 control-label hidden-print" >Master</label>
                            <div class="col-sm-8">
                                <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print">Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'required[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'required[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print">PIS</label>
                            <div class="col-sm-4">
                                <input type="text" name="pis" class="required[custom] form-control" /><span class="loader"></span>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php If (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                            <button type="button" value="exportar para excel" onclick="tableToExcel('tbRelatorio', 'Busca por PIS')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php
                        ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>               
                </div>

                <?php
                If (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
                    $master = isset($_POST['master']) ? $_POST['master'] : NULL;
                    $projeto = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
                    $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
                    $pis = isset($_POST['pis']) ? $_POST['pis'] : NULL;

//                    $sql = "SELECT * FROM funcionario WHERE id_master = '$master' AND id_regiao = '$regiao' AND pis = '$pis' ";
                    $sql = "SELECT *, D.nome AS nome_func,D.endereco AS end_func,D.bairro AS bairro_func,D.cidade AS muni_func,  F.nome AS nome_projeto
                            FROM master AS A
                            INNER JOIN regioes AS B ON B.id_master = A.id_master
                            INNER JOIN projeto AS C ON C.id_regiao = B.id_regiao
                            INNER JOIN rh_clt AS D ON D.id_projeto = C.id_projeto
                            INNER JOIN curso AS E ON E.id_curso = D.id_curso
                            INNER JOIN projeto AS F ON F.id_projeto = C.id_projeto
                            WHERE D.pis='$pis' AND A.id_master = '$master' ";
                    if (!isset($_REQUEST['todos_projetos'])) {
                        $sql .= "AND C.id_projeto = '$projeto' ";
                    }
                    $sql .= "AND D.id_regiao = '$regiao'
                            LIMIT 10 ";
//                    echo $sql."<br>";
                    $result_pro = mysql_query($sql);
//                    $row_pro = mysql_fetch_array($result_pro);
//                    var_dump($row_pro);

                    if (mysql_num_rows($result_pro) > 0) {
                        ?>

                        <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>RG</th>
                                    <th>CPF</th>
                                    <th>PIS</th>
                                    <th>Endereço</th>
                                    <th>Bairro</th>
                                    <th>Cidade</th>
                                    <th>UF</th>
                                    <th>CEP</th>
                                    <th>Projeto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $_mes = "";

                                while ($row = mysql_fetch_assoc($result_pro)) {
                                    $data = explode("-", $row['data_nasci']);
                                    $diaAniversario = $data[2];

                                    if ($_mes != $row['mes']) {
                                        $_mes = $row['mes'];
                                        $mes_ext = strtoupper(mesesArray($row['mes']));

                                        if ($row['mes'] == 3) {
                                            $mes_ext = "MARÇO";
                                        }
                                        echo "<tr align='center'><td colspan='3' style='background: #F0F0F7'>" . $mes_ext . "</td><tr />";
                                    }
                                    ?>                                            
                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">       
                                        <td><?php echo $row['nome_func']; ?></td>
                                        <td><?php echo $row['cpf']; ?></td>
                                        <td><?php echo $row['rg']; ?></td>
                                        <td><?php echo $row['pis']; ?></td>
                                        <td><?php echo $row['end_func']; ?></td>
                                        <td><?php echo $row['bairro_func']; ?></td>
                                        <td><?php echo $row['muni_func']; ?></td>
                                        <td><?php echo $row['uf']; ?></td>
                                        <td><?php echo $row['cep']; ?></td>
                                        <td><?php echo $row['nome_projeto']; ?></td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <div id="message-box" class="alert alert-warning">
                            <span class="fa fa-exclamation-triangle"></span> Nenhum registro encontrado.
                        </div>
                        <?php
                    }
                    //echo '<br>'.$sql; 
                }
                ?>  
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
        <link href="../net1.css" rel="stylesheet" type="text/css" />





        <script>
                            $(function () {


                                $('#master').change(function () {
                                    var id_master = $(this).val();
                                    $('#regiao').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                                    $.ajax({
                                        url: '../action.global.php?master=' + id_master,
                                        success: function (resposta) {
                                            $('#regiao').html(resposta);
                                            $('#regiao').next().html('');
                                        }
                                    });

                                    $('#regiao').trigger('change')
                                });



                                $('#regiao').change(function () {
                                    var id_regiao = $(this).val();

                                    $('#projeto').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                                    $.ajax({
                                        url: '../action.global.php?regiao=' + id_regiao,
                                        success: function (resposta) {
                                            $('#projeto').html(resposta);
                                            $('#projeto').next().html('');
                                        }
                                    });


                                });

                                $('#master').trigger('change');

                            });

        </script>

    </body>
</html>
