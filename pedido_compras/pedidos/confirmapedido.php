<?php

echo 'sdasdasdasdasd';
error_reporting(E_ALL);

//header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
echo '1';
include("../../wfunction.php");
echo '2';
include("../../classes/pedidosClass.php");
echo '3';
include("../../classes/NFeClass.php");
echo '4';
include("../../classes/EstoqueEntradaClass.php");
include("../../classes/EstoqueSaidaClass.php");
echo '5';
//include("../../");
include("email.php");
echo '5.5';
include("../../classes/global.php");
echo '6';
include("../../classes/pdf/fpdf.php");
echo '8';
//error_reporting(E_ALL);
//$nfe = new NFe();
$pedido = new pedidosClass();
$usuario = carregaUsuario();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'Detalhes') {
    $dados['id_pedido'] = $_REQUEST['id_pedido'];
    $lista = $pedido->consultaPedido($dados, TRUE);
    $lista = $lista[key($lista)];
//   print_array($lista);
    ?>

    <form action="confirmapedido.php" method="post" id="form-confirmacao">
        <div class="panel panel-default">
            <div class="panel-body text-info text-bold">
                <div class="text-left">Data do Pedido: <?= converteData($lista['dtpedido'], "d/m/Y") ?></div> 
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body bg-info text text-sm text-info text-bold">
                <div class="row">
                    <div class="col-md-8"><?= utf8_encode($lista['upa']) . " (" . $lista['upa_cnpj'] . ")" ?></div> 
                    <div class="col-md-4 text text-right">Total R$ <span id="total"><?= number_format($lista['total'], 2, ',', '.'); ?></span></div> 
                </div>        
            </div>        
        </div>
        <div class="panel panel-default">
            <div class="panel-body bg-info text text-sm text-info text-bold">
                <input type="hidden" name="fornecedor" class="fornecedor" id="xfornecedor" value="<?= $lista['id_fornecedor'] ?>">
                <div><?= utf8_encode($lista['razao'] . " (" . mascara_string("##.###.###/####-##", $lista['razao_cnpj']) . ") <br/>" . $lista['razao_endereco']) ?></div> 
            </div>        
        </div>
        <div class="form-group">
            <button type="button" class="btn btn-block btn-info btn-xs" id="item_incluir"><i class="fa fa-edit"></i> Incluir Item</button><?= $value[''] ?>
        </div>
        <div class="form-group" id="incluirItem" style="display:none;">
            <table class="table table-condensed table-striped text-sm" id="tab_incluir_item">
                <thead>
                    <tr class="text text-sm text-info">
                        <th>Produto Descrição</th>
                        <th>Quantidade</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    <tr class="valign-middle">
                <input id="item" name="item" type="hidden">
                <td><input  type="text"  id="descricao_item" name="descricao_item" class="form-control" ></td>
                <td class="col-xs-2"><input type="text" id="qtd_item" class="form-control text text-center"></td>
                <td class="col-xs-1"><button type="button" class="btn btn-block btn-xs btn-info" id="incluirnopedido"><i class="fa fa-plus"></i>  Incluir</button></td>
                </tr>
                </tbody>
            </table>                
            <div class="loading_item" id="resp_form_inserir_item"></div>

        </div>
        <div class="form-group">
            <table class="table table-striped table-condensed text-sm" id="tab_inclui_produtos">
                <thead>
                    <tr>
                        <td colspan="5" class="text-info">Dados dos Produtos</td>
                    </tr>
                    <tr class="text text-center">
                        <td class="text text-left">Descri&ccedil;&atilde;o</td>
                        <td>Unidade</td>
                        <td>Valor</td>
                        <td style="width: 120px;">Quantidade</td>
                        <td style="width: 120px;">Total R$</td>
                    </tr>
                </thead>
                <tbody id="itens_do_pedido"> 
                    <?php foreach ($lista['itens'] as $det) { ?>
                        <tr id="tr-item-<?= $det['id_prod'] ?>" class="valign-middle">
                            <td><?= utf8_encode($det['xProd']) ?>
                                <input type="hidden" value="<?= $det['id_item'] ?>" name="id_item[]">
                                <input type="hidden" value="<?= $det['id_prod'] ?>" name="id_prod[]">
                            </td>
                            <td class="text text-center"><?= $det['uCom'] ?></td>
                            <?php
                            $x = explode('.', $det['vUnCom']);
                            if (strlen($x[1]) == 3) {
                                $x = $det['vUnCom'] = number_format((float) $det['vUnCom'], 3, ',', '.');
                            } else {
                                $x = $det['vUnCom'] = number_format((float) $det['vUnCom'], 2, ',', '.');
                            }
                            ?>
                            <td class="text text-right"><?= $x ?><input type="hidden" name="vUnCom[]" id="vUnCom-<?= $det['id_prod'] ?>" value="<?= $x ?>" ></td>
                            <td class="text text-right"><input type="text" name="qCom[]" value="<?= $det['qCom'] ?>"class="item_qtde form-control text-center text-sm" id="qCom-<?= $det['id_prod'] ?>" data-id="<?= $det['id_prod'] ?>" size="8" maxlength="12"></td>
                            <td class="text-right"><input type="text" name="vProd[]" id="vProd-<?= $det['id_prod'] ?>" class="vlrTotal form-control text-right text-sm" readonly="" value="<?= number_format((float) $det['vProd'], 2, ',', '.'); ?>"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-xs-3 col-xs-offset-6">
                <button type="button" class="btn btn-block btn-danger btn-sm pedido-cancelar" data-id="<?= $_REQUEST['id_pedido'] ?>"><i class="fa fa-times"></i> Cancelar o Pedido</button><?= $value[''] ?>
            </div>
            <div class="col-xs-3">
                <button type="button" class="btn btn-block btn-info btn-sm btn-confirmaOk" data-id="<?= $_REQUEST['id_pedido'] ?>" onclick="confirmaOK(<?= $_REQUEST['id_pedido'] ?>);"><i class="fa fa-check"></i> Confirmar o Pedido</button>
                <input type="hidden" name="method" value="confirmarpedidoOk">
                <input type="hidden" name="id" value="<?= $_REQUEST['id_pedido'] ?>">
                <input type="hidden" name="conferido" value="<?= $usuario['id_funcionario'] ?>">
            </div>
        </div>


    </td>

    </form>
    <div id="cancelamento" style="display:none"></div>
    <div id="cancelar-pedido" class="loading"></div>
    <div id="confirmaOk" class="loading"></div>

    <?php
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'Enviar') {

$from = $_SESSION['email'];
$dados['id_pedido'] = $_REQUEST['id_pedido'];
$upa = $_REQUEST['upa'];
$para = $_REQUEST['email']; //
$pedidos = $pedido->pedidosEnviados($dados, TRUE);
$pedido->alteraStatus($_REQUEST['id_pedido']);

$path = "pdf/PED{$dados['id_pedido']}.pdf";
//define the receiver of the email 

$to = $para;
//define the subject of the email 
$subject = 'PEDIDO ' . $upa;
//create a boundary string. It must be unique 
//so we use the MD5 algorithm to generate a random hash 
$random_hash = md5(date('r', time()));
//define the headers we want passed. Note that they are separated with \r\n 
$headers = "From: {$from}\r\nReply-To: {$from}";
$headers .= "\r\nBcc: {$from}";
$headers .= "\r\nDisposition-Notification-To: {$from}";
//add boundary string and mime type specification 
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
//add boundary string and mime type specification 
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";
//read the atachment file contents into a string,
//encode it with MIME base64,
//and split it into smaller chunks
$attachment = chunk_split(base64_encode(file_get_contents($path)));
//define the body of the message. 
ob_start(); //Turn on output buffering 
?> 
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<?php 
if($pedidos['tipo'] == 185) {
    $tipo = "Medicamentos";
}else if($pedidos['tipo'] == 267) {
    $tipo = "Material Hospitalar";
}
?>

<h2>Solicitação de <?= $tipo ?></h2>
<br>
<p>Segue em anexo pedido referente à <strong><?= $upa ?></strong>.</p>
<br>
<br>

<?php if (file_exists($usuario['id_funcionario'] . ".jpg")) { ?>
<p><img src="http://f71lagos.com/intranet/compras/pedidos/<?= $usuario['id_funcionario'] . ".jpg" ?>" alt="Assinatura"/></p>
<?php } else if($usuario['id_funcionario'] == 174){ ?>
<address>
Controladoria Interna de Farmácia<br/>
Gerente farmaceutica<br/>
Fernanda Rodrigues<br/>
<img src="http://f71lagos.com/intranet/imagens/logomaster<?= $usuario['id_master'] ?>.gif"/>
</address>
<?php } else if($usuario['id_funcionario'] == 284){ ?>
<address>
Leandro Alves<br/>
Gerente de Almoxarifado<br/>
Instituto dos Lagos Rio<br/>
Tel: (21) 2702-1381 / 98861-7527<br/>
<img src="http://f71lagos.com/intranet/imagens/logomaster<?= $usuario['id_master'] ?>.gif"/>
</address>
<?php } ?>


--PHP-alt-<?php echo $random_hash; ?>-- 

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/pdf; name="PED<?= $dados['id_pedido'] ?>.pdf"
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?> 
--PHP-mixed-<?php echo $random_hash; ?>-- 

<?php
//copy current buffer contents into $message variable and delete current output buffer 
$message = ob_get_clean();
//send the email 
$mail_sent = mail($to, $subject, $message, $headers);
//if the message is sent successfully print "Mail sent". Otherwise print "Mail failed" 
echo $mail_sent ? "E-Mail enviado $to" : "Falha no Envio do E-mail";

exit();
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'sem_enviar'){

    $dados['id_pedido'] = $_REQUEST['id_pedido'];
    $pedidos = $pedido->pedidosEnviados($dados, TRUE);
    $return = $pedido->alteraStatus($_REQUEST['id_pedido'])?array('status'=>TRUE):array('status'=>FALSE);
    echo json_encode($return);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancelapedido') {
    $status = $pedido->cancelar_pedido($_REQUEST['id'], $usuario['id_funcionario'], $_REQUEST['motivo_cancelamento']);
    echo json_encode(array('status' => $status));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'reabrirpedido') {
    $status = $pedido->reabrirPedidos($_REQUEST['id'], $_REQUEST['motivo_reabertura'], $usuario['id_funcionario']);
    echo json_encode(array('status' => $status));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'confirmarpedidoOk') {
    $array = $pedido->preparaArrayItens($_REQUEST['qCom'], $_REQUEST['vProd'], $_REQUEST['id_prod'], $_REQUEST['id_item']);
    $status1 = $pedido->confirmaOk($_REQUEST['id'], $array, $usuario['id_funcionario']);
    if ($status1) {
        $dados['id_pedido'] = $_REQUEST['id'];
        $pedidos = $pedido->pedidosConfirmados($dados, TRUE);
        $pedido->iniciaFpdf();
        $pedido->setFileName("PED" . $_REQUEST['id'] . ".pdf");
        $pedido->geraPdf();
        $pedido->finalizaPdf();
        $pedido->limpaVariaveis();
        echo json_encode(array('nomeFile' => 'pdf/' . $pedido->nomeFile, 'status' => TRUE));
    } else {
        echo json_encode(array('status' => $status1));
    }
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaFornecedor") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $rs = $pedido->selectFornecedorContrato($_REQUEST['projeto'], $_REQUEST['tipo']);
    $fornecedor = "";
    foreach ($rs as $k => $val) {
        $fornecedor .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $fornecedor;
    exit;
}

// Incluir itens no pedido depois de sua solicitação ou reabertura do cancelamento
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'itemIncluir') {
    $produtos = $pedido->consultarProduto($_REQUEST['id_prod'], $_REQUEST['id_prestador']);
    ?>
    <tr id="tr-item-<?= $produtos['id_prod'] ?>" class="valign-middle">
        <td>
            <?= $produtos['xProd'] ?>
            <input type="hidden" value="<?= $produtos['id_prod'] ?>" name="id_prod[]">
        </td>
        <td class='text text-center'><?= $produtos['uCom'] ?></td>
        <?php
        $x = explode('.', $produtos['vUnCom']);
        if (strlen($x[1]) == 3) {
            $x = number_format((float) $produtos['vUnCom'], 3, ',', '.');
        } else {
            $x = number_format((float) $produtos['vUnCom'], 2, ',', '.');
        }
        ?>
        <td class="text text-right"><?= $x ?><input type="hidden" name="vUnCom[]" id="vUnCom-<?= $produtos['id_prod'] ?>" value="<?= $x ?>" ></td>
        <td class="text text-right"><input type="text" name="qCom[]" value="<?= $_REQUEST['qtd_item'] ?>" class="form-control text-center text-sm item_qtde" id="qCom-<?= $produtos['id_prod'] ?>" data-id="<?= $produtos['id_prod'] ?>" size="8" maxlength="12"></td>
        <td class="text-right"><input type="text" name="vProd[]" id="vProd-<?= $produtos['id_prod'] ?>" class="vlrTotal form-control text-right text-sm" readonly="" value="<?= number_format($produtos['vUnCom'] * $_REQUEST['qtd_item'], 2, ',', '.'); ?>"></td>
    </tr>
    <?php
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'getIdFornecedor') {
    $query = "SELECT prestadorservico.id_contabil_fornecedor as id_fornecedor FROM prestadorservico WHERE id_prestador = {$_REQUEST['id_contrato']}";
    $retorno = mysql_fetch_assoc(mysql_query($query));
    echo json_encode($retorno);
}    