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

if($_REQUEST['filtrar'])

//CONDIÇÕES PARA A LISTAGEM 
$qr = "SELECT A.*,B.nome,DATE_FORMAT(A.solicitado_em, '%d/%m/%Y') AS solicitado_embr FROM com_solicitacao AS A
        LEFT JOIN funcionario AS B ON (A.solicitado_por = B.id_funcionario)
        WHERE A.`status` = 1 ORDER BY solicitado_em DESC";
$rs = mysql_query($qr);
$total = mysql_num_rows($rs);

$statusR = (isset($_REQUEST['status']))?$_REQUEST['status']:"";
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
                <br/>
                <?php } ?>
                
                <fieldset>
                    <legend>Filtro</legend>
                    <p><label class="first"> Status:</label> <?php echo montaSelect($arStatus, $statusR, "id='status' name='status'") ?> </p>
                    <p class="controls"> <input type="button" id="filtrar" name="filtrar" value="Filtrar" /> </p>
                </fieldset>
                
                <?php if ($total > 0) { ?>
                
                <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Solicitações de Compras')" value="Exportar para Excel" class="exportarExcel"></p>
                <table id="tabela" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Data da Solicitação</th>
                            <th>Solicitado por</th>
                            <th>Pedido</th>
                            <th>Status</th>
                            <th>Urgente?</th>
                            <th colspan="3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysql_fetch_assoc($rs)){ ?>
                        <tr>
                            <td class="txcenter"><?php echo $row['id_solicitacao'] ?></td>
                            <td class="txcenter"><?php echo $row['solicitado_embr'] ?></td>
                            <td><?php echo $row['nome'] ?></td>
                            <td><?php echo $row['descricao'] ?></td>
                            <td class="txcenter"><?php echo $arStatus[$row['status']] ?></td>
                            <td class="txcenter"><?php echo ($row['urgencia']==0)?"não":"sim" ?></td>
                            <td class="txcenter"><img src="../imagens/arquivo.gif" class="btimage" data-action="det" data-key="<?php echo $row['id_solicitacao'] ?>" title="Acompanhamento Detalhado" alt="Acompanhamento Detalhado" /></td>
                            <td class="txcenter"><img src="../imagens/arquivo.gif" class="btimage" data-action="alt" data-key="<?php echo $row['id_solicitacao'] ?>" title="Alterar" alt="Alterar" /></td>
                            <td class="txcenter"><img src="../imagens/arquivo.gif" class="btimage" data-action="can" data-key="<?php echo $row['id_solicitacao'] ?>" title="Cancelar Solicitação" alt="Cancelar Solicitação" /></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                <div id='message-box' class='message-green'>
                    <p>Nenhuma solicitação encontrada</p>
                </div>
                <?php } ?>
                <p class="controls"> <input type="button" name="solicitar" id="solicitar" value="Solicitar" class="button" onclick="window.location='solicita.php'" />  </p>
            </div>
        </form>
    </body>
</html>