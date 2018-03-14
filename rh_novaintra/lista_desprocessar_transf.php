<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$id_regiao = $usuario['id_regiao'];

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
    
    $arrcondicao[] .= " a.id_regiao = '{$usuario['id_regiao']}' ";
    
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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Lista Transferências");
//$breadcrumb_pages = array("Gestão de RH"=>"../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Lista Transferências</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Lista Transferências</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <form  name="form" action="" class="form-horizontal" method="post" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Projeto:</label>
                            <div class="col-lg-10">
                                <?=montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control'))?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">CPF do CLT:</label>
                            <div class="col-lg-10">
                                <input type="text" name="cpf" id="cpf" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="home" id="home" />
                        <input type="submit" class="btn btn-primary" name="gerar" value="Gerar" id="gerar"/>
                    </div><!-- /.col-lg-12 -->
                </div>
            </form>
            <?php if (!empty($qr_relatorio) && isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                <div class="col-lg-12 note note-warning">
                    <h3 class="col-lg-9"><?=(!isset($_REQUEST['todos_projetos'])) ? $projeto['nome'] : 'TODOS OS PROJETOS'?></h3>
                    <div class="col-lg-3"><button type="button" class="btn btn-success pull-right" onclick="tableToExcel('tbRelatorio', 'Transferencias')"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button></div>
                </div>
                <table id="tbRelatorio" class="table table-hover table-condensed table-bordered">
                    <thead>
                        <tr class="bg-primary valign-middle">
                            <th>NOME</th>
                            <th>CPF</th>
                            <!--th>UNIDADE DE ORIGEM</th>
                            <th>FUNÇÃO DE ORIGEM</th>
                            <th>UNIDADE DE DESTINO</th>
                            <th>FUNÇÃO DE DESTINO</th-->
                            <th>MOTIVO</th>
                            <th style="width: 10%;">COMPETÊNCIA</th>   
                            <th style="width: 10%;">DATA DE CRIAÇÃO</th>   
                            <th>USUÁRIO RESPONSÁVEL</th>   
                            <th></th>   
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>
                            <tr class="valign-middle">
                                <td><?=$row_rel['nome']?></td>
                                <td align="center"> <?=$row_rel['cpf']?></td>
                                <!--td align="center"> <?=$row_rel['unidade_de']?></td>
                                <td> <?=$row_rel['curso_de']?></td>
                                <td align="center"> <?=$row_rel['unidade_para']?></td>
                                <td> <?=$row_rel['curso_para']?></td-->
                                <td align="center"><?=$row_rel['motivo']?></td>                       
                                <td align="center"><?=$row_rel['data_proc']?></td>                       
                                <td align="center"><?=$row_rel['criado_em']?></td>                       
                                <td align="center"><?=$row_rel['usuario']?></td>                       
                                <td align="center">
                                    <a href="rh_transferencia_desprocessar.php?clt=<?=$row_rel['id_clt']?>&AMP;tela=1" title="Desprocessar" class="btn btn-xs btn-primary">
                                        <i title="Visualizar" class="bt-image fa fa-search" data-type="ver" data-key="1157"></i>
                                    </a>
                                </td>                       
                            </tr>                                
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr class="tr-bg-activeg">
                            <td colspan="6"><strong>TOTAL:</strong></td>
                            <td align="center"><?=$num_rows?></td>
                        </tr>
                    </tfoot>
                </table>
            <?php } ?>
            <?php include_once ('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>