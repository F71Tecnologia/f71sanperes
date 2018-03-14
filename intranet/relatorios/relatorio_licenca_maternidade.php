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

$filtro = false;

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$usuario = carregaUsuario();


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





if (isset($_REQUEST['gerar']) || isset($_REQUEST['gerar_todos'])) {

    $filtro = true;
    
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];

    $cond_projeto = (isset($_REQUEST['gerar_todos'])) ? "" : " a.id_projeto = '$id_projeto' AND ";
//SELECIONANDO OS DADOS DO PROJETO
    $query = "SELECT b.id_clt,b.nome AS nome_clt,
        a.id_evento AS a_id_evento,a.data AS a_data,a.data_retorno AS a_data_retorno,
        c.id_evento AS c_id_evento,c.data AS c_data,c.data_retorno AS c_data_retorno,
        (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto) AS nome_projeto,
        (SELECT nome FROM curso WHERE id_curso = b.id_curso) AS nome_curso,
        DATE_FORMAT(a.data,'%d/%m/%Y') AS a_data_br,
        DATE_FORMAT(a.data_retorno,'%d/%m/%Y') AS a_data_retorno_br,
        DATE_FORMAT(c.data,'%d/%m/%Y') AS c_data_br,
        DATE_FORMAT(c.data_retorno,'%d/%m/%Y') AS c_data_retorno_br
        FROM rh_eventos AS a
        INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt AND a.cod_status = 50)
        LEFT JOIN rh_eventos AS c ON (b.id_clt = c.id_clt AND c.cod_status = 54)
        WHERE $cond_projeto a.id_regiao = '$id_regiao' 
        AND a.cod_status = 50
        AND a.status = 1 
        AND NOW() BETWEEN a.data AND a.data_retorno 
        ORDER BY nome_projeto,b.nome;";
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
        
        <title>:: Intranet :: Relatório de Licença Maternidade</title>
        
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
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> -  Licença Maternidade</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-2 control-label hidden-print" >Região</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            
                             <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                             
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        </div>
                    </div>
                        
                        <div class="panel-footer text-right hidden-print">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            <?php if (isset($_REQUEST['gerar']) || isset($_REQUEST['gerar_todos'])) { ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Consolidado Pessoal')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <?php } ?>
                                <button type="submit" name="gerar_todos" id="gerar_todos" value="Gerar de Todos os Projetos" class="btn btn-warning"><span class="fa fa-filter"></span> Filtrar Todos os Projetos</button>
                                <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                        </div>               
                    </div>
            <?php 
            if($filtro){
                if ($total != 0) { ?>
            <table class="table table-striped table-bordered table-hover text-sm valign-middle" id="tbRelatorio">
                 <thead>
                     <tr>
                         <th rowspan="2">Unidade</th>
                         <th rowspan="2">Nome</th>
                         <th rowspan="2">Função</th>
                         <th colspan="2">Licença Maternidade</th>
                         <th colspan="2">Atestado Amamentação</th>
                         <th rowspan="2">Exame Retorno as Atividades</th>
                     </tr>
                     <tr>
                         <th>Início</th>
                         <th>Término</th>                      
                         <th>Início</th>
                         <th>Término</th>                      
                     </tr>
                 </thead>
                <tbody>
                    <?php while ($row = mysql_fetch_assoc($result)) { ?>
                          <tr>
                              <td><?php echo $row['nome_projeto'] ?></td>
                              <td><?php echo $row['nome_clt'] ?></td>
                              <td><?php echo $row['nome_curso'] ?></td>
                              <td align="center"><?= (!empty($row['a_data_br'])) ? $row['a_data_br'] : "-" ?></td>               
                              <td align="center"><?= (!empty($row['a_data_retorno_br'])) ? $row['a_data_retorno_br'] : "-" ?></td>                     
                              <td align="center"><?= (!empty($row['c_data_br'])) ? $row['c_data_br'] : "-" ?></td>               
                              <td align="center"><?= (!empty($row['c_data_retorno_br'])) ? $row['c_data_retorno_br'] : "-" ?></td>                                        
                              <td align="center">
                                  <?php
                                  $retorno = (!empty($row['c_data_retorno'])) ? $row['c_data_retorno'] : $row['a_data_retorno'];
                                  $data = explode('-', $retorno);
                                  echo date("d/m/Y", mktime(0, 0, 0, $data[1], $data[2] + 1, $data[0]));
                                  ?>
                              </td>                     
                          </tr>
                      <?php } ?>  
                </tbody>
                <tfoot>
                        <tr>
                            <td colspan="7" class="txright"><strong>Total:</strong></td>
                            <td align="center"><?php echo $total ?></td>
                        </tr>
                    </tfoot>
            </table>
             
                   <?php }else{ ?>
                <div id="message-box" class="alert alert-danger">
                    <span class="fa fa-exclamation-triangle"></span>Nenhum registro encontrado
                </div>
            <?php  }
             }?>
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
            $(function (){

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

            });
        </script>
        
    </body>
</html>
