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
$optRegiao = getRegioes();
$ACOES = new Acoes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//$usuario = carregaUsuario();
///REGIAO
$rsRegiao = montaQuery('regioes', "id_regiao, regiao", "id_regiao = {$usuario['id_regiao']}");
$optRegiao = array('' => 'Selecione...');
foreach ($rsRegiao as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $_REQUEST['reg'];

///PROJETO
$id_regiao_query = (isset($_POST['regiao'])) ? $_POST['regiao'] : $usuario['id_regiao'];
$rsProjeto = montaQuery('projeto', "id_projeto, nome", "id_regiao = " . $id_regiao_query);
$optProjeto = array('' => 'Selecione...');
foreach ($rsProjeto as $valor) {
    $optProjeto[$valor['id_projeto']] = $valor['id_projeto'] . ' - ' . $valor['nome'];
}
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $_REQUEST['reg'];


$sqlEventos = " SELECT a.cod_status, a.nome_status
                FROM rh_eventos AS a
                INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt)
                LEFT JOIN rh_eventos AS c ON (b.id_clt = c.id_clt)
                WHERE a.id_regiao = $id_regiao_query AND a.status = 1 AND (NOW() BETWEEN a.data AND a.data_retorno || NOW() >= a.data) AND a.cod_status > 10
                GROUP BY a.nome_status
                ORDER BY a.nome_status";
$queryEventos = mysql_query($sqlEventos);
$arrEventos[-1] = '-- Todos --';
while ($rowEventos = mysql_fetch_assoc($queryEventos)) {
    $arrEventos[$rowEventos['cod_status']] = $rowEventos['nome_status'];
    $ev .= $rowEventos['cod_status'] . ',';
}
$size = strlen($ev);
$ev = substr($ev, 0,$size-1);
$eventoSel = (isset($_REQUEST['evento'])) ? $_REQUEST['evento'] : null;

if (isset($_REQUEST['gerar']) || isset($_REQUEST['gerar_todos'])) {

    $filtro = true;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $cond_projeto = (isset($_REQUEST['gerar_todos'])) ? "" : " a.id_projeto = '$id_projeto' AND ";
    
    if ($_REQUEST['evento'] == '-1') {
        $cond_evento = "IN ($ev)";
    } else {
        $cond_evento = "= {$_REQUEST['evento']}";
    }
    
    $query = "SELECT * FROM (SELECT b.id_clt,b.nome AS nome_clt, a.id_evento AS a_id_evento,a.data AS a_data,a.data_retorno AS a_data_retorno, c.id_evento AS c_id_evento,c.data AS c_data,c.data_retorno AS c_data_retorno, (
            SELECT nome
            FROM projeto
            WHERE id_projeto = a.id_projeto) AS nome_projeto, (
            SELECT unidade
            FROM unidade
            WHERE id_unidade = b.id_unidade) AS nome_unidade, (
            SELECT nome
            FROM curso
            WHERE id_curso = b.id_curso) AS nome_curso, (
            SELECT letra
            FROM curso
            WHERE id_curso = b.id_curso) AS letra_curso, (
            SELECT numero
            FROM curso
            WHERE id_curso = b.id_curso) AS numero_curso, DATE_FORMAT(a.data,'%d/%m/%Y') AS a_data_br, DATE_FORMAT(a.data_retorno,'%d/%m/%Y') AS a_data_retorno_br, DATE_FORMAT(c.data,'%d/%m/%Y') AS c_data_br, DATE_FORMAT(c.data_retorno,'%d/%m/%Y') AS c_data_retorno_br, a.nome_status
            FROM rh_eventos AS a
            INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt)
            LEFT JOIN rh_eventos AS c ON (b.id_clt = c.id_clt)
            WHERE a.cod_status $cond_evento AND a.id_regiao = $id_regiao_query AND a.status = 1 AND (NOW() BETWEEN a.data AND a.data_retorno || NOW() >= a.data) AND a.cod_status > 10
            ORDER BY nome_projeto,b.nome, a.id_evento DESC) AS eventos
            GROUP BY id_clt";
    
   $result = mysql_query($query);

    $total = mysql_num_rows($result);
    $qr_regiao = mysql_query("SELECT * FROM  regioes WHERE id_regiao='$regiao' ");
    $row_reg = mysql_fetch_assoc($qr_regiao);
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Eventos</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Eventos</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >

                            <label for="select" class="col-sm-1 control-label hidden-print" >Região</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>

                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                            <label for="select" class="col-sm-1 control-label hidden-print">Evento</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($arrEventos, $eventoSel, array('name' => "evento", 'id' => 'evento', 'class' => 'validate[required] form-control')); ?>
                            </div>

                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>
                        
                        <div class="panel-footer text-right hidden-print">
                            <?php if (!empty($query) && (isset($_POST['gerar']) || (isset($_REQUEST['todos_projetos'])))) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                                <button type="button" form="formPdf" name="pdf" data-title="Relatório Licença INSS" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                            <?php } ?>
                            
                            <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) {
                            ?>
                                <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                            <?php } ?> 
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>               
                    </div> 

                    <?php if (!empty($query) && (isset($_POST['gerar']) || (isset($_REQUEST['todos_projetos'])))) { ?>
                        <table class="table table-striped table-condensed table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                            <thead>
<!--                                <tr>
                                    <th colspan="6"><?php echo $row_rel['nome_projeto'] ?></th>
                                </tr>-->
                                <tr>
                                    <th>PROJETO</th>
                                    <th>UNIDADE</th>
                                    <th>NOME</th>
                                    <th>FUNÇÃO</th>
                                    <th>EVENTO</th>   
                                    <th>DATA DE AFASTAMENTO</th>   
                                    <th>DATA DE RETORNO</th>   
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row_rel = mysql_fetch_assoc($result)) {
//                                $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                ?>
                                    <tr class="text-center">
                                        <td><?php echo $row_rel['nome_projeto'] ?></td>
                                        <td><?php echo $row_rel['nome_unidade'] ?></td>
                                        <td> <?php echo $row_rel['nome_clt']; ?></td>
                                        <td> <?php echo $row_rel['nome_curso'] . ' ' . $row_rel['letra_curso'] . ' ' . $row_rel['numero_curso']; ?></td>
                                        <td> <?php echo $row_rel['nome_status']; ?></td>
                                        <td> <?php echo $row_rel['a_data_br']; ?></td>                       
                                        <td> <?php echo $row_rel['a_data_retorno_br']; ?></td>                       
                                    </tr>  
                                    <?php } ?>
                            </tbody>

                        </table>
                <?php } ?>
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
