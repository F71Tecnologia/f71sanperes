<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include('../conn.php');
include('../wfunction.php');
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$roMaster = montaQueryFirst("master", "nome", "id_master = '{$usuario['id_master']}'");  //"SELECT * FROM master WHERE id_master = '{$usuario['id_master']}'"
$meses = mesesArray(null);
$ano = anosArray(null);
$optRegiao = getRegioes();
$ACOES = new Acoes();

$mesSelect = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSelect = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;


$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];


if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || isset($_REQUEST['todos_projetos'])) {
    $filtro = true;



    if ($_REQUEST['tipo'] == "entrada") {

        $sqlTec = "SELECT
            A.id_clt,
            A.cpf, 
            A.nome, 
            A.email, 
            DATE_FORMAT(A.data_nasci, '%d/%m/%Y') as data_nasci, 
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada, 
            A.tel_fixo, 
            A.tel_cel, 
            A.tel_rec, 
            A.locacao,
            A.id_curso,
            A.status,
            A.data_saida,
            B.campo2
            FROM  rh_clt A 
            INNER JOIN curso B on (B.id_curso = A.id_curso)
            LEFT JOIN rh_recisao C on (C.id_clt = A.id_clt AND C.status = 1)
            WHERE Month(A.data_entrada) = {$_REQUEST['mes']} AND Year(A.data_entrada) = {$_REQUEST['ano']} ";

        if (!isset($_REQUEST['todos_projetos'])) {
            $sqlTec .= "AND A.id_projeto = {$projeto} ";
        }
        $sqlTec .= "AND A.id_regiao = {$regiao}
            ORDER BY A.nome";

        $qrTec = mysql_query($sqlTec);
        $num_rows = mysql_num_rows($qrTec);
    } else if ($_REQUEST["tipo"] == "saida") {
        $sqlTec = "SELECT
            A.id_clt,
            A.cpf, 
            A.nome, 
            A.email, 
            DATE_FORMAT(A.data_nasci, '%d/%m/%Y') as data_nasci, 
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada, 
            A.tel_fixo, 
            A.tel_cel, 
            A.tel_rec, 
            A.locacao,
            A.id_curso,
            A.status,
            A.data_saida,
            B.campo2
            FROM  rh_clt A 
            INNER JOIN curso B on (B.id_curso = A.id_curso)
            LEFT JOIN rh_recisao C on (C.id_clt = A.id_clt AND C.status = 1)
            WHERE Month(A.data_saida) = {$_REQUEST['mes']} AND Year(A.data_saida) = {$_REQUEST['ano']} ";
        if (!isset($_REQUEST['todos_projetos'])) {
            $sqlTec .= "AND A.id_projeto = {$projeto} ";
        }
        $sqlTec .= "AND A.id_regiao = {$regiao}
            ORDER BY A.nome";
        $qrTec = mysql_query($sqlTec);
        $num_rows = mysql_num_rows($qrTec);
    }
    echo "<!-- $sqlTec -->";
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relátorio de Entrada e Saída por Periodo</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--                <link href="../net1.css" rel="stylesheet" type="text/css" />-->
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Relatório de Entrada e Saída por Período</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Dados</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-4 control-label hidden-print" >Mês</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mesSelect, "id='mes' name='mes' class='required[custom[select]] validate[required] form-control'") ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print" >Ano</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($ano, $anoSelect, "id='ano' name='ano' class='required[custom[select]] validate[required] form-control'") ?><span class="loader"></span> 
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print" >Região</label>
                            <div class="col-sm-5">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-4 control-label hidden-print" >Tipo</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect(array(null => "Selecione", 'entrada' => "Entrada", 'saida' => "Saida"), null, array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div> 

                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>

                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>

                    <div class="panel-footer text-right hidden-print controls">
                        <?php if (isset($filtro) && (isset($_REQUEST['filtrar']))) { ?>
                            <button type="button" onclick="tableToExcel('tabela', 'Relatório de Entrada e Saída por Período')" class="btn btn-success" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <?php } ?>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="Filtrar de Todos Projetos" id="todos_projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Todos os Projetos</button>
                        <?php } ?>
                        <button type="submit" name="filtrar" id="gerar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div> 


                <?php
                if ($filtro) {
                    if ($num_rows > 0) {
                        $count = 0;
                        ?>  
                        <table class="table table-striped table-condensed table-bordered text-sm valign-middle" id="tbRelatorio">
                            <thead>
                                <tr>
                                    <th>CPF</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Data de Nascimento</th>
                                    <th>Telefone</th>
                                    <th>Celular</th>
                                    <th>Tel. Recado</th>
                                    <th>Cargo</th>
                                    <th>Nucleo</th>
                                    <th>Data Admissão</th>
                                    <th>Data Demissão</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysql_fetch_array($qrTec)) { ?>

                                    <tr class="<?php echo ($count++ % 2 == 0) ? "odd" : "even" ?>">
                                        <td align="center"><?php echo $row['cpf']; ?><input type="hidden" name="clt[]" value="<?php echo $row['id_clt']; ?>"/></td>
                                        <td align="center"><?php echo RemoveAcentos($row['nome']); ?></td>
                                        <td align="center"><?php echo $row['email']; ?></td>
                                        <td align="center" class="DATA1"><?php echo $row['data_nasci']; ?></td>
                                        <td align="center"><?php echo $row['tel_fixo']; ?></td>
                                        <td align="center"><?php echo $row['tel_cel']; ?></td>
                                        <td align="center"><?php echo $row['tel_rec']; ?></td>
                                        <td align="center"><?php echo RemoveAcentos($row['campo2']); ?></td>
                                        <td align="center"><?php echo RemoveAcentos($row['locacao']); ?></td>
                                        <td align="center"><?php echo $row['data_entrada']; ?></td>
                                        <td align="center"><?php echo ($row['data_saida'] != "0000-00-00" ) ? date("d/m/Y", strtotime(str_replace("-", "/", $row['data_saida']))) : "00-00-0000"; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                            <?php } else { ?>
                        <br/>
                        <div id='message-box' class='alert alert-warning'>
                            <span class="fa fa-exclamation-triangle"></span> Nenhum registro encontrado
                        </div>
                        <?php
                    }
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
        
        <script>
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
    </body>
</html>
