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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
// PEGA O ID DO FUNCION�RIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMA��ES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//$usuario = carregaUsuario();
///REGIAO
$rsRegiao = montaQuery('regioes', "id_regiao, regiao", "id_master = $usuario[id_master]");
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



if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {

    $filtro = true;

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $cond_projeto = (isset($_REQUEST['todos_projetos'])) ? "" : " a.id_projeto = '$id_projeto' AND ";
//SELECIONANDO OS DADOS DO PROJETO
//    $query = "SELECT b.id_clt,b.nome AS nome_clt, DATE_FORMAT(a.pericia,'%d/%m/%Y') as pericia, a.ocorrencias,
//        concat(b.tel_cel, ' / ', b.tel_fixo, ' / ', b.tel_rec) as contato,
//        a.id_evento AS a_id_evento,a.data AS a_data,a.data_retorno AS a_data_retorno,
//        c.id_evento AS c_id_evento,c.data AS c_data,c.data_retorno AS c_data_retorno,
//        (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto) AS nome_projeto,
//        (SELECT nome FROM curso WHERE id_curso = b.id_curso) AS nome_curso,
//        DATE_FORMAT(a.data,'%d/%m/%Y') AS a_data_br,
//        DATE_FORMAT(a.data_retorno,'%d/%m/%Y') AS a_data_retorno_br,
//        DATE_FORMAT(c.data,'%d/%m/%Y') AS c_data_br,
//        DATE_FORMAT(c.data_retorno,'%d/%m/%Y') AS c_data_retorno_br
//        FROM rh_eventos AS a
//        INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt AND a.cod_status = 50)
//        LEFT JOIN rh_eventos AS c ON (b.id_clt = c.id_clt AND c.cod_status = 54)
//        WHERE $cond_projeto a.id_regiao = '$id_regiao' 
//        AND a.cod_status = 50
//        AND a.status = 1 
//        AND NOW() BETWEEN a.data AND a.data_retorno 
//        ORDER BY nome_projeto,b.nome;";
    
    $query = "SELECT a.id_clt, a.nome AS nome_clt, a.id_regiao, a.cpf, 
                concat(a.tel_cel, ' / ', a.tel_fixo, ' / ', a.tel_rec) as contato,
                (SELECT nome FROM curso WHERE id_curso = a.id_curso) AS nome_curso,
                (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto) AS nome_projeto,
                (SELECT especifica FROM rhstatus WHERE codigo = a.status) AS nome_status,
                (SELECT id_evento FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND STATUS = 1 ORDER BY id_evento DESC LIMIT 1) AS a_id_evento,
                (SELECT ocorrencias FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND STATUS = 1 ORDER BY id_evento DESC LIMIT 1) AS ocorrencias,
                (SELECT DATE_FORMAT(data, '%d/%m/%Y') FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND STATUS = 1 ORDER BY id_evento DESC LIMIT 1) AS a_data_br,
                (SELECT DATE_FORMAT(data_retorno, '%d/%m/%Y') FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND STATUS=1 ORDER BY id_evento DESC LIMIT 1) AS a_data_retorno_br
        FROM rh_clt a
        LEFT JOIN projeto b ON a.id_projeto = b.id_projeto
        WHERE $cond_projeto a.id_regiao = '{$id_regiao}' AND b.status_reg = '1' AND a.status = '20'
            AND (SELECT data_retorno FROM rh_eventos WHERE id_clt = a.id_clt AND cod_status=a.status AND STATUS=1 ORDER BY id_evento DESC LIMIT 1) = '0000-00-00'
        ORDER BY b.id_projeto, a.nome ASC";
    
    //echo "sql = [{$query}]<br/>\n";
    
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

        <title>:: Intranet :: Relat�rio de Licen�a Pelo INSS</title>

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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relat�rio <small> - Licen�a Pelo INSS</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relat�rio</div>
                        <div class="panel-body">
                            <div class="form-group" >
                                <label for="select" class="col-sm-2 control-label hidden-print" >Regi�o</label>
                                <div class="col-sm-4">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                </div>

                                <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                                <div class="col-sm-3">
                                    <?php echo montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                                </div>

                                <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                            </div>
                        </div>
                        
                        <div class="panel-footer text-right hidden-print">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                            <?php if (!empty($result) && (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Licen�a Pelo INSS')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                            <?php ///permiss�o para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                            if ($ACOES->verifica_permissoes(85)) {
                                ?>
                                <button type="submit" name="todos_projetos" id="todos_projetos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Todos os Projetos</button>
                            <?php } ?> 
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>               
                    </div> 

                    <?php if (!empty($result) && (isset($_POST['gerar']) || isset($_REQUEST['todos_projetos']))) { ?>
                        <table class="table table-striped table-hover table-bordered table-condensed text-sm valign-middle" id="tbRelatorio">
                            <thead>
                                <tr>
                                    <th colspan="6"><?php echo $projeto['nome'] ?></th>
                                </tr>
                                <tr>
                                    <th>UNIDADE</th>
                                    <th>NOME</th>
                                    <th>FUN��O</th>
                                    <th>EVENTO</th>   
                                    <th>DATA DE AFASTAMENTO</th>   
                                    <th>DATA DE RETORNO</th>
                                    <th>OCORR�NCIA</th>
                                    <th>CONTATO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_rel = mysql_fetch_assoc($result)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td align="center"><?php echo $row_rel['nome_projeto'] ?></td>
                                        <td> <?php echo $row_rel['nome_clt']; ?></td>
                                        <td align="center"> <?php echo $row_rel['nome_curso']; ?></td>
                                        <td align="center"> <?php echo $row_rel['a_id_evento']; ?></td>
                                        <td align="center"><?php echo $row_rel['a_data_br']; ?></td>                       
                                        <td align="center"><?php echo $row_rel['a_data_retorno_br']; ?></td>
                                        <td align="center"><?php echo $row_rel['ocorrencias']; ?></td>
                                        <td align="center"><?php echo $row_rel['contato']; ?></td>
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