<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$update = false;

$tipos = array("" => "« Selecione »", "PRODUTO" => "PRODUTO", "SERVIÇO" => "SERVIÇO");
$patrimonio = array("" => "« Selecione »", "BEM DE CONSUMO" => "BEM DE CONSUMO", "BEM DURÁVEL" => "BEM DURÁVEL");

if (isset($_REQUEST['novoPedido']) && !empty($_REQUEST['novoPedido'])) {
    $campos = array(
        'solicitado_por',
        'solicitado_em',
        'tipo',
        'patrimonio',
        'urgencia',
        'destinatario',
        'descricao',
        'justificativa'
    );
    $valores = array(
        $usuario['id_funcionario'],
        date("Y-m-d H:i:s"),
        $_REQUEST['tipo'],
        $_REQUEST['patrimonio'],
        $_REQUEST['urgencia'],
        $_REQUEST['destinatario'],
        $_REQUEST['descricao'],
        $_REQUEST['justificativa']
    );

    sqlInsert("com_solicitacao", $campos, $valores);
    session_start();
    $_SESSION['MSG_MESSAGE'] = "Solicitação enviada com sucesso";
    header("Location: solicitacoes.php");
}

if (isset($_REQUEST['alterarPedido']) && !empty($_REQUEST['alterarPedido'])) {

    $campos = array(
        'tipo' => $_REQUEST['tipo'],
        'patrimonio' => $_REQUEST['patrimonio'],
        'urgencia' => $_REQUEST['urgencia'],
        'destinatario' => $_REQUEST['destinatario'],
        'descricao' => $_REQUEST['descricao'],
        'justificativa' => $_REQUEST['justificativa']
    );

    sqlUpdate("com_solicitacao", $campos, "id_solicitacao={$_REQUEST['id_solicitacao']}");

    session_start();
    $_SESSION['MSG_MESSAGE'] = "Alteração realizada com sucesso";
    header("Location: solicitacoes.php");
}

if (isset($_REQUEST['id_solicitacao']) && !empty($_REQUEST['id_solicitacao'])) {
    $rs = mysql_query("SELECT * FROM com_solicitacao WHERE id_solicitacao = {$_REQUEST['id_solicitacao']}");
    $row = mysql_fetch_assoc($rs);
    if ($row['urgencia']) {
        $urgencias = "checked=\"checked\"";
        $urgencian = "";
    } else {
        $urgencias = "";
        $urgencian = "checked=\"checked\"";
    }
    $update = true;
} else {
    $row['id_solicitacao'] = "";
    $row['tipo'] = "";
    $row['patrimonio'] = "";
    $row['descricao'] = "";
    $row['justificativa'] = "";
    $row['destinatario'] = "";
    $urgencias = "";
    $urgencian = "checked=\"checked\"";
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Compas da OS</title>

        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/datepicker.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />

    </head>

    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-compras-header"><h2><span class="glyphicon glyphicon-shopping-cart"></span> - COMPRAS E CONTRATOS <small> - Gestão de Compras da OS</small></h2></div>

            <form action="" method="post" name="form1" id="form1">
                
                <fieldset>
                    <legend>Dados</legend>
                    <input type="hidden" name="id_solicitacao" id="id_solicitacao" value="<?php echo $row['id_solicitacao'] ?>" />
                    <p><label class="first">Urgênte?</label>
                        <input type="radio" name="urgencia" id="urgencias" value="1" <?php echo $urgencias ?>/> <label for="urgencias">Sim</label>
                        <input type="radio" name="urgencia" id="urgencian" value="0" <?php echo $urgencian ?>/> <label for="urgencian">Não</label>
                    </p>
                    <div class="fleft" style="width: 44%">
                        <p><label class="first">Tipo:</label> <?php echo montaSelect($tipos, $row['tipo'], "id='tipo' name='tipo' class='validate[required]'") ?> </p>
                    </div>
                    <div class="fleft">
                        <p><label class="first-2">Integração ao Patrimônio:</label> <?php echo montaSelect($patrimonio, $row['patrimonio'], "id='patrimonio' name='patrimonio' class='validate[required]'") ?> </p>
                    </div>
                    <br class="clear" />
                    <p><label class="first">Descrição:</label> <textarea rows="5" cols="45" name="descricao" id="descricao" class="validate[required]"><?php echo $row['descricao'] ?></textarea> </p>
                    <p><label class="first">Justificativa:</label> <textarea rows="5" cols="45" name="justificativa" id="justificativa" class="validate[required]"><?php echo $row['justificativa'] ?></textarea> </p>
                    <p><label class="first">Destinatário:</label> <input name="destinatario" id="destinatario" value="<?php echo $row['destinatario'] ?>" /> <span class="example">Caso o pedido seja para uma Unidade ou outra pessoa</span> </p>
                </fieldset>

                <p class="controls">
                    <input type="button" name="voltar" id="voltar" value="Voltar" class="button btn btn-default" onclick="window.location = 'solicitacoes.php';" />
                    <?php if ($update) { ?>
                        <input type="submit" name="alterarPedido" id="alterarPedido" value="Alterar Pedido" class="button btn btn-success" />
                    <?php } else { ?>
                        <input type="submit" name="novoPedido" id="novoPedido" value="Solicitar" class="button btn btn-success" />
                    <?php } ?>
                </p>

            </form>
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>