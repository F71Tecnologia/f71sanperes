<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');

$usuario = carregaUsuario();
$arStatus = array("1"=>"Aberto","2"=>"Aprovado");
$message = false;
session_start();

if(isset($_SESSION['MSG_MESSAGE']) && !empty($_SESSION['MSG_MESSAGE'])){
    $message = $_SESSION['MSG_MESSAGE'];
    unset($_SESSION['MSG_MESSAGE']);
}

//CONDIÇÕES PARA A LISTAGEM
$where = "solicitado_por = {$usuario['id_funcionario']}";
$order = "solicitado_em DESC";
$rs = montaQuery("com_solicitacao","*,DATE_FORMAT(solicitado_em, '%d/%m/%Y') AS solicitado_embr",$where,$order);
$total = count($rs);
?>
<html>
    <head>
        <title>:: Intranet :: SOLICITAÇÕES DE COMPRA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#message-box").delay(4000).slideUp('slow');
                $(".btimage").click(function(){
                    var $this = $(this);
                    var acao = $this.data("action");
                    var key = $this.data("key");
                    if(acao=="det"){
                        thickBoxIframe("Detalhe", "popup.detalhe.php", {method:"detalhe", id:key}, 680, 400);
                    }else if(acao=="alt"){
                        $("#id_solicitacao").val(key);
                        $("#form1").attr("action","solicita.php");
                        $("#form1").submit();
                    }
                });
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
                        <p>Listagem de solicitações de compras</p>
                    </div>
                </div>
                <br class="clear">
                <?php if($message!==false){ ?>
                <div id='message-box' class='message-yellow'><p><?php echo $message ?></p></div>
                <?php } ?>
                <?php if ($total > 0) { ?>
                <br/>
                <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Solicitações de Compras')" value="Exportar para Excel" class="exportarExcel"></p>
                <table id="tabela" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data da Solicitação</th>
                            <th>Pedido</th>
                            <th>Status</th>
                            <th>Urgente?</th>
                            <th colspan="3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rs as $solicitacao){ ?>
                        <tr>
                            <td><?php echo $solicitacao['id_solicitacao'] ?></td>
                            <td><?php echo $solicitacao['solicitado_embr'] ?></td>
                            <td><?php echo $solicitacao['descricao'] ?></td>
                            <td><?php echo $arStatus[$solicitacao['status']] ?></td>
                            <td><?php echo ($solicitacao['urgencia']==0)?"não":"sim" ?></td>
                            <td class="txcenter"><img src="../imagens/arquivo.gif" class="btimage" data-action="det" data-key="<?php echo $solicitacao['id_solicitacao'] ?>" title="Acompanhamento Detalhado" alt="Acompanhamento Detalhado" /></td>
                            <td class="txcenter"><img src="../imagens/arquivo.gif" class="btimage" data-action="alt" data-key="<?php echo $solicitacao['id_solicitacao'] ?>" title="Alterar" alt="Alterar" /></td>
                            <td class="txcenter"><img src="../imagens/arquivo.gif" class="btimage" data-action="can" data-key="<?php echo $solicitacao['id_solicitacao'] ?>" title="Cancelar Solicitação" alt="Cancelar Solicitação" /></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                <div id='message-box' class='message-green'>
                    <p>Nenhuma solicitação encontrada</p>
                </div>
                <?php } ?>
                <input type="hidden" name="id_solicitacao" id="id_solicitacao" value="" />
                <p class="controls"> <input type="button" name="solicitar" id="solicitar" value="Nova Solicitação" class="button" onclick="window.location='solicita.php'" />  </p>
            </div>
        </form>
    </body>
</html>