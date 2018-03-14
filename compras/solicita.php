<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');

$usuario = carregaUsuario();
$update = false;

$tipos = array(""=>"« Selecione »","PRODUTO"=>"PRODUTO","SERVIÇO"=>"SERVIÇO");
$patrimonio = array(""=>"« Selecione »","BEM DE CONSUMO"=>"BEM DE CONSUMO","BEM DURÁVEL"=>"BEM DURÁVEL");

if(isset($_REQUEST['novoPedido']) && !empty($_REQUEST['novoPedido'])){
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

if(isset($_REQUEST['alterarPedido']) && !empty($_REQUEST['alterarPedido'])){
    
    $campos = array(
        'tipo'=>$_REQUEST['tipo'],
        'patrimonio'=>$_REQUEST['patrimonio'],
        'urgencia'=>$_REQUEST['urgencia'],
        'destinatario'=>$_REQUEST['destinatario'],
        'descricao'=>$_REQUEST['descricao'],
        'justificativa'=>$_REQUEST['justificativa']
    );
    
    sqlUpdate("com_solicitacao", $campos, "id_solicitacao={$_REQUEST['id_solicitacao']}");
    
    session_start();
    $_SESSION['MSG_MESSAGE'] = "Alteração realizada com sucesso";
    header("Location: solicitacoes.php");
}

if(isset($_REQUEST['id_solicitacao']) && !empty($_REQUEST['id_solicitacao'])){
    $rs = mysql_query("SELECT * FROM com_solicitacao WHERE id_solicitacao = {$_REQUEST['id_solicitacao']}");
    $row = mysql_fetch_assoc($rs);
    if($row['urgencia']){
        $urgencias="checked=\"checked\"";
        $urgencian="";
    }else{
        $urgencias="";
        $urgencian="checked=\"checked\"";
    }
    $update=true;
}else{
    $row['id_solicitacao'] = "";
    $row['tipo'] = "";
    $row['patrimonio'] = "";
    $row['descricao'] = "";
    $row['justificativa'] = "";
    $row['destinatario'] = "";
    $urgencias="";
    $urgencian="checked=\"checked\"";
}

?>
<html>
    <head>
        <title>:: Intranet :: SOLICITAÇÕES DE COMPRA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        
        <script src="../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#form1").validationEngine();
            });
        </script>
    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1" id="form1">
            <div id="content">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Compras - Solicitações</h2>
                        <p>Formulário para solicitação de compras</p>
                    </div>
                </div>
                <br class="clear">
                <br/>
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
                    <p><label class="first">Descrição:</label> <textarea rows="5" cols="45" name="descricao" id="descricao" class="validate[required]"><?php echo $row['descricao']?></textarea> </p>
                    <p><label class="first">Justificativa:</label> <textarea rows="5" cols="45" name="justificativa" id="justificativa" class="validate[required]"><?php echo $row['justificativa']?></textarea> </p>
                    <p><label class="first">Destinatário:</label> <input name="destinatario" id="destinatario" value="<?php echo $row['destinatario']?>" /> <span class="example">Caso o pedido seja para uma Unidade ou outra pessoa</span> </p>
                </fieldset>
                
                <p class="controls">
                    <input type="button" name="voltar" id="voltar" value="Voltar" class="button" onclick="window.location='solicitacoes.php';" />
                    <?php if($update){ ?>
                        <input type="submit" name="alterarPedido" id="alterarPedido" value="Alterar Pedido" class="button" />
                    <?php }else{ ?>
                        <input type="submit" name="novoPedido" id="novoPedido" value="Solicitar" class="button" />
                    <?php } ?>
                </p>
            </div>
        </form>
    </body>
</html>