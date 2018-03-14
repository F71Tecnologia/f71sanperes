<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include ("../conn.php");
include ("../classes/funcionario.php");
include ("../classes_permissoes/regioes.class.php");
include ("../classes_permissoes/acoes.class.php");
include ("../wfunction.php");

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => " ../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Desprocessar Transferências");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;

    $id_projeto = $_REQUEST['projeto'];

    $arrcondicao = array();

    if (isset($_REQUEST['projeto']) && !empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') {
        $arrcondicao[] .= " id_projeto = '{$_REQUEST['projeto']}'";
    }
    if (isset($_REQUEST['cpf']) && !empty($_REQUEST['cpf'])) {
        $arrcondicao[] .= " a.cpf = '{$_REQUEST['cpf']}' ";
    }
    $condicao = (!empty($arrcondicao)) ? "WHERE " . implode(' AND ', $arrcondicao) : '';

    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
    $sql = "select a.id_clt,a.nome,a.cpf,b.unidade_de,b.unidade_para,b.id_unidade_de,b.id_unidade_para,b.motivo, 
                DATE_FORMAT( b.data_proc , '%m/%Y' ) AS `data_proc`, b.id_transferencia,
                DATE_FORMAT( b.criado_em , '%d/%m/%Y %T') AS `criado_em`,
                (select nome from curso where id_curso = b.id_curso_de) as curso_de,
                (select nome from curso where id_curso = b.id_curso_para) as curso_para,
                c.nome as usuario
                from rh_clt as a
                inner join rh_transferencias as b on (b.id_clt = a.id_clt)
                inner join funcionario as c on (b.id_usuario = c.id_funcionario)
                $condicao
                order by a.nome,b.id_transferencia DESC;";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$id_regiao'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Desprocessar Transferências</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href=" ../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href=" ../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href=" ../resources/css/main.css" rel="stylesheet" media="all">
        <link href=" ../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href=" ../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href=" ../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href=" ../css/progress.css" rel="stylesheet" type="text/css">
        <link href=" ../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href=" ../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Desprocessar Transferências</small></h2></div>

                    <div class="tab-content">                        
                        <div role="tabpanel" class="tab-pane active" id="lista">
                            <form class="form-horizontal" role="form" id="form-lista" method="post" autocomplete="off">

                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="categoria_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nome_centrocusto" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-9"><input type="text" name="cpf" id="cpf" class="form-control" placeholder="CPF do CLT" value="<?php echo $cpf; ?>"></div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">
                                        <input type="submit" name="gerar" value="Gerar" id="gerar"  class="btn btn-primary"/>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            
            
            <div class="row">
                <div class="col-lg-12">
                    <?php if (!empty($qr_relatorio) && isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                        <p style="text-align: right; margin-top: 20px">
                            <button type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>  
                        </p>
                        <table class="table table-striped table-hover" id="tbRelatorio">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center"><?= (!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                                </tr>
                                <tr>
                                    <th>COMPETÊNCIA</th>
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>DA UNIDADE</th>
                                    <!--th>FUNÇÃO DE ORIGEM</th-->
                                    <th>PARA UNIDADE</th>
                                    <!--th>FUNÇÃO DE DESTINO</th-->
                                    <!--th>MOTIVO</th-->
                                    <!--th>DATA DE CRIAÇÃO</th>   
                                    <th>USUÁRIO RESPONSÁVEL</th-->
                                    <th></th>   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['data_proc']; ?></td>
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td> <?php echo $row_rel['cpf']; ?></td>
                                        <td> <?php echo $row_rel['unidade_de']; ?></td>
                                        <!--td> <?php echo $row_rel['curso_de']; ?></td-->
                                        <td> <?php echo $row_rel['unidade_para']; ?></td>
                                        <!--td> <?php echo $row_rel['curso_para']; ?></td-->
                                        <!--td><?php echo $row_rel['motivo']; ?></td-->
                                        <!--td><?php echo $row_rel['criado_em']; ?></td>
                                        <td><?php echo $row_rel['usuario']; ?></td-->
                                        <td>
                                            <?php if($_COOKIE['logado'] != 395) { ?>
                                            <a href="rh_transferencia_desprocessar.php?clt=<?= $row_rel['id_clt'] ?>&AMP;tela=1" title="Desprocessar" class="btn btn-warning btn-sm">
                                                <i class="fa fa-search"></i>
                                                <!--img src="../imagens/icones/icon-view.gif" title="Visualizar" class="bt-image center-block" data-type="ver" data-key="1157"-->
                                            </a>
                                            <?php } ?>
                                        </td>                       
                                    </tr>                                
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><strong>TOTAL:</strong></td>
                                    <td><?php echo $num_rows ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        </div>
    </body>
</html>