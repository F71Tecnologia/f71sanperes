<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if (!isset($_REQUEST['id_clt'])) {
    header("Location: /intranet/rh/ver.php");
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$ACOES = new Acoes();

$id_clt = ($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : '';
$form_tipo = (!empty($_REQUEST['id_advertencia'])) ? 'editar' : 'gerar';
if (isset($_REQUEST['submit'])) {
    if (!$_REQUEST['data'] OR !$_REQUEST['motivo'] OR !$_REQUEST['alinea']) {
        $_SESSION['MESSAGE'] = 'Preencha todos campos requeridos!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
    }
    elseif ($_REQUEST['submit'] == 'gerar') {
        $data = implode('-', array_reverse(explode('/', $_REQUEST['data'])));
        $motivo = $_REQUEST['motivo'];
        $alineas = implode(",", $_REQUEST['alinea']);
        $insert = sqlInsert("rh_suspensao", ['id_clt','tipo','motivo','alinea','data','user_cad'], [$id_clt,1,$motivo,$alineas,$data,$usuario['id_funcionario']]);
        header("Location: ../relatorios/carta_de_advertencia.php?id_advertencia=$insert");
    }
    elseif ($_REQUEST['submit'] == 'editar') {
        $data = implode('-', array_reverse(explode('/', $_REQUEST['data'])));
        $motivo = $_REQUEST['motivo'];
        $alineas = implode(",", $_REQUEST['alinea']);
        $id_advertencia = $_REQUEST['id_advertencia'];
        $update = sqlUpdate("rh_suspensao", ['id_clt'=>$id_clt,'motivo'=>$motivo,'alinea'=>$alineas,'data'=>$data],"id_suspensao='$id_advertencia' $query_condition");
        header("Location: ../relatorios/carta_de_advertencia.php?id_advertencia=$id_advertencia");
    }
}
if (!empty($_REQUEST['id_advertencia'])) {
    $query_join = "LEFT JOIN rh_suspensao D ON A.id_clt = D.id_clt AND D.id_suspensao = '$_REQUEST[id_advertencia]'";
    $query_fields = ", D.*, D.data as suspensao_data";
}
$arrClt = montaQuery("rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade $query_join", "A.*, B.nome curso, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') data_entrada, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade $query_fields", "A.id_clt = $id_clt");

$projetoSel = $arrClt[1]['id_projeto'];
$sqlProjetos = "SELECT * FROM projeto WHERE id_regiao = {$usuario['id_regiao']} AND status_reg = 1";
$queryProjetos = mysql_query($sqlProjetos);
while ($rowProjetos = mysql_fetch_assoc($queryProjetos)) {
    $arrProjetos[$rowProjetos['id_projeto']] = $rowProjetos['nome'];
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Carta de Advert�ncia Disciplinar </title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/add-ons.min.css" rel="stylesheet">
        <style>
            select option {
                width: 100%;
                font-size:8pt;
            }
            textarea[name=motivo] {
                resize: none;
            }
            div#message-box:empty {
                display: none;
            }
        </style>
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Carta de Advert�ncia Disciplinar</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                <input type="hidden" name="id_advertencia" id="id_advertencia" value="<?= $_REQUEST['id_advertencia'] ?>">
                <div id="message-box" class="alert alert-warning <?php echo $_SESSION['MESSAGE_COLOR']; ?>"><?php echo $_SESSION['MESSAGE']; session_destroy(); ?></div>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Configura��es da Carta</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <input type="hidden" id="id_clt" name="id_clt" value="<?=$id_clt?>" />
                            <label for="select" class="col-sm-2 control-label hidden-print">Nome</label>
                            <div class="col-sm-4">
                                <input type="text" disabled class="form-control" value="<?=$arrClt[1]['nome']?>" />
                            </div>

                            <label for="projeto" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($arrProjetos, $projetoSel, array('disabled' => "", 'name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?> 
                            </div>
                        </div>
                        <div class="form-group">    
                            <label for="data" class="col-sm-2 control-label hidden-print">Data</label>
                            <div class="col-sm-2">
                                <input type="text" id="data" name="data" class="dataMask data form-control validate[required]" value="<?= (!empty($arrClt[1]['suspensao_data'])) ? implode('/', array_reverse(explode('-', $arrClt[1]['suspensao_data']))) : date('d/m/Y')?>"/>
                            </div>
                            <div class="col-sm-1"></div>
                            <label for="alinea" class="col-sm-2 control-label hidden-print" >Al�nea</label>
                            <div class="col-sm-4">
                                <select name="alinea[]" size="7" id="alinea" class="form-control validate[required]" multiple>
                                    <option value="a" <?= (!empty(preg_match('/a/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>A) Ato de improbidade</option>
                                    <option value="b" <?= (!empty(preg_match('/b/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>B) Incontin�ncia de conduta ou mau procedimento</option>
                                    <option value="c" <?= (!empty(preg_match('/c/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>C) Negocia��o habitual sem permiss�o do empregador</option>
                                    <option value="d" <?= (!empty(preg_match('/d/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>D) Condena��o criminal do empregado</option>
                                    <option value="e" <?= (!empty(preg_match('/e/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>E) Des�dia no desempenho das respectivas fun��es</option>
                                    <option value="f" <?= (!empty(preg_match('/f/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>F) Embriaguez habitual ou em servi�o</option>
                                    <option value="g" <?= (!empty(preg_match('/g/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>G) Viola��o de segredo da empresa</option>
                                    <option value="h" <?= (!empty(preg_match('/h/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>H) Ato de indisciplina ou de insubordina��o</option>
                                    <option value="i" <?= (!empty(preg_match('/i/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>I) Abandono de emprego</option>
                                    <option value="j" <?= (!empty(preg_match('/j/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>J) Ato lesivo contra qualquer pessoa</option>
                                    <option value="k" <?= (!empty(preg_match('/k/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>K) Ato lesivo contra o empregador e superiores hier�rquicos</option>
                                    <option value="l" <?= (!empty(preg_match('/l/', $arrClt[1]['alinea']))) ? 'selected' : '' ?>>L) Pr�tica constante de jogos de azar</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Motivo</label>
                            <div class="col-sm-9">
                                <textarea rows="5" name="motivo" id="motivo" class="form-control validate[required]"><?= $arrClt[1]['motivo'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print controls">
                        <button type="submit" name="submit" id="submit" value="<?= $form_tipo ?>" class="btn btn-primary"><span class="fa fa-filter"></span> <?= ucfirst($form_tipo) ?> Declara��o</button>
                    </div>
                </div>    
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        <script src="../js/jquery-1.8.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.validationEngine_2.6.2.js"></script>
        <script>
            $(function () {
                //validation engine
                $("#form").validationEngine({promptPosition : "topRight"});
                
                $(".dataMask").mask("99/99/9999");
            });
        </script>
    </body>
</html>
