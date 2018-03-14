<?php
//header('Content-Type: text/html; charset=utf-8');
header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFeClass.php");
include("../../classes/FornecedorClass.php");
include("../../classes/EstoqueClass.php");
include("../../classes/EstoqueEntradaClass.php");
include("../../classes/EstoqueSaidaClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/global.php");

$nfe = new NFe(); // instancia obj nfe
$pedido = new pedidosClass();

// consulta cnpj para ver se há projeto cadastrado
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultacnpjcpf') {
    $prestador = $nfe->consultaPrestador($_REQUEST['projeto'], $_REQUEST['cnpjcpf']);
    if (count($prestador) == 0) {
        $retorno = array(
            'status' => FALSE,
            'msg' => utf8_encode('Não há cadastro do Prestador ou fornecedor. Cadastro não poderé ser salvo.')
        );
    } else {
        $retorno = array(
            'status' => TRUE,
            'nome' => utf8_encode($prestador['c_razao']),
            'endereco' => utf8_encode($prestador['c_endereco']),
            'cnpjcpf' => utf8_encode($prestador['c_cnpj'])
        );
    }
    echo json_encode($retorno);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultacnpj') {
    $prestador = $nfe->consultaFornecedor($_REQUEST['projeto'], $_REQUEST['cnpj']);
    if (count($prestador) == 0) {
        $retorno = array(
            'status' => FALSE,
            'msg' => utf8_encode('Não há cadastro do fornecedor. Cadastro não poderé ser salvo.')
        );
    } else {
        $retorno = array(
            'status' => TRUE,
            'nome' => utf8_encode($prestador['razao']),
            'endereco' => utf8_encode($prestador['endereco']),
            'cnpj' => utf8_encode($prestador['cnpj'])
        );
    }
    echo json_encode($retorno);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultapedidos') {
    $pedido = new pedidosClass();
    $consultapedido = $pedido->PedidoEnviados($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador']);
    echo '<option value="">Selecione</option>';
    foreach ($consultapedido as $key => $value) {
        $data = converteData($value['dtpedido'], 'd/m/Y');
        $total = number_format($value['total'], 2, ',', '.');
        echo "<option value = '$key'>Número.: $key Data.: $data  Total.: R$ $total</option>";
    }
    exit();
}

$nfe->load($_FILES['nfe']['tmp_name']); // carrega xml para objeto
// trazer item para cadastro manual
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'itemIncluir') {
    $produtos = $nfe->consultarProduto($_REQUEST['id_prod']);
    ?>
    <tr id="tr-item-<?= $produtos['id_prod'] ?>" >
        <td><?= $produtos['id_prod'] . "-" . $produtos['xProd'] ?></td>
        <td><?= $produtos['NCM'] ?></td>
        <td><?= $produtos['uCom'] ?></td>
        <td><?= number_format($produtos['vUnCom'], 2, ',', '.') ?><input type="hidden" name="vUnCom[]" id="vUnCom-<?= $produtos['id_prod'] ?>" class="form-control money validate[required]" value="<?= number_format($produtos['vUnCom'], 2, ',', '.') ?>"></td>
        <td><input type="text" name="nLote[]" id="nLote-<?= $produtos['id_prod'] ?>" class="form-control"></td>
        <td><input type="text" name="dVal[]" id="dVal-<?= $produtos['id_prod'] ?>" class="form-control text-center data hasdatepicker"></td>
        <td><input type="text" class="form-control qtd-item validate[required]" name="qCom[]" data-id="<?= $produtos['id_prod'] ?>"></td>
        <td><input type="text" class="form-control" name="vProd[]" id="vProd-<?= $produtos['id_prod'] ?>" readonly=""></td>
        <td>
            <button type="button" class="btn btn-danger item-excluir" data-id="<?= $produtos['id_prod'] ?>">
                <i class="fa fa-times"></i> Exlcuir
            </buttom>
    </td>
    </tr>
    <?php
    exit();
}

// importar XML ----------------------------------------------------------------
if (isset($_REQUEST['importar']) && $_REQUEST['importar'] == 'Visualizar NFe') {
    ?>

    <div class="alert alert-dismissable alert-info">
        <p class="text text-info text-semibold">Observações Complementares</p>
        <p><?= $nfe->infAdic->infCpl ?></p>        
    </div>
    <table class="table table-condensed table-striped">
        <thead>
            <tr class="text text-sm">
                <th colspan="2">Fornecedor</th>
                <th class="text-center">Emissão</th>
                <th class="text-center">Número NFe</th>
                <th class="text-right">Valor R$</th>
            </tr>
        </thead>
        <tbody class="text text-semibold text-info">
            <tr>
                <td><?= $nfe->emit->xNome ?> </td>
                <td><?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?></td>
                <td class="text-center"><?php echo implode('/', array_reverse(explode('-', $nfe->ide->dEmi))); ?></td>
                <td class="text-center"><?= $nfe->ide->nNF ?></td>
                <td class="text-right"><?= number_format((float) $nfe->total->ICMSTot->vNF, 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td colspan="5"><?= $nfe->emit->enderEmit->xLgr . ", " . $nfe->emit->enderEmit->nro . " " . $nfe->emit->enderEmit->xCpl ?> </td>
            </tr>
        </tbody>
    </table>
    </div>
    <input type="hidden" name="nameFile" value="<?= $nfe->savedFile ?>"/>
    <input type="hidden" name="cnpj-nfe" id="cnpj-nfe" value="<?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?>">
    <?php
    exit();
}

// SALVAR XML NO BANCO DE DADOS ------------------------------------------------
if (isset($_REQUEST['salvar']) && $_REQUEST['salvar'] == 'Salvar') {

    $array = $nfe->nfe_xml_to_array();

    $resp = $nfe->salvarNFe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array);

    if ($resp['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal Salva com Sucesso.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp['msg'] ?>
        </div>
        <?php
    }
}

if (isset($_REQUEST['conferirPedido']) && $_REQUEST['conferirPedido'] == 'Conferir NFe') {
    $array_nfe = $nfe->nfe_xml_to_array();
    $array_pedido = $pedido->PedidoEnviados($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $_REQUEST['pedidoss'], TRUE);
    $array_pedido = $array_pedido[$_REQUEST['pedidoss']];
    $arr_errados = array();

    $itens_nfe = $nfe->agrupaItens($array_nfe['det']);
    $itens_pedido = $array_pedido['itens'];

    $arr_errados = $nfe->validaNFE($itens_nfe, $itens_pedido);


    if (!empty($arr_errados['itens_errados'])) {
        ?>
        <table class="table table-hover table-bordered table-condensed">
            <thead>
                <tr>
                    <th class="text-center text-semibold text-warning bg-warning" colspan="5">NFe <?= $array_nfe['nNF'] ?> sem Conformidade com o Pedido</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text text-center text-semibold">
                    <td>Produto</td>
                    <td>Quantidade</td>
                    <td>Valor R$</td>
                    <td colspan="2">Divergência</td>
                </tr>
            </tbody>
            <tbody class="bg-warning text-sm text-semibold text-danger">
                <?php
                foreach ($arr_errados['itens_errados'] as $value) {
                    $vlr = number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") - number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".");
                    $qntd = (float) $value['NFE']['qCom'] - (float) $value['pedido']['qCom']
                    ?>
                    <tr>
                        <td><?= $value['pedido']['xProd'] ?></td>
                        <td class="text-center"><?= (float) $value['pedido']['qCom'] ?></td>
                        <td class="text-right"><?= number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".") ?></td>
                        <td class="text-center"><?= $qntd ?></td>
                        <td class="text-right"><?= number_format((float) $vlr, 3, ",", ".") ?></td>
            <!--                    <td class="tr-bg-warning text-center"><?= (float) $value['NFE']['qCom'] ?></td>
                        <td class="tr-bg-warning text-right"><?= number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") ?></td>
                        <td class="tr-bg-info text-center"><?= (float) $value['pedido']['qCom'] ?></td>
                        <td class="tr-bg-info text-right"><?= number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".") ?></td>-->
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>

            </tfoot>
        </table>
        <?php
    }
    if (!empty($arr_errados['itens_ped_falt'])) { ?>
        <table class="table table-hover table-bordered table-condensed">
            <thead>
                <tr>
                    <th class="text-center text-semibold text-warning bg-warning" colspan="5">NFe <?= $array_nfe['nNF'] ?> - itens faltando</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text text-center text-semibold">
                    <td>Produto</td>
                    <td>Quantidade</td>
                    <td>Valor R$</td>
                </tr>
            </tbody>
            <tbody class="bg-warning text-sm text-semibold text-danger">
                <?php foreach ($arr_errados['itens_ped_falt'] as $value) { ?>
                    <tr>
                        <td><?= $value['xProd'] ?></td>
                        <td class="text-center"><?= (float) $value['qCom'] ?></td>
                        <td class="text-right"><?= number_format((float) ($value['vUnCom']), 3, ",", ".") ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>

            </tfoot>
        </table>
        <?php
    }
    if (!empty($arr_errados['itens_nfe_extra'])) { ?>
        <table class="table table-hover table-bordered table-condensed">
            <thead>
                <tr>
                    <th class="text-center text-semibold text-warning bg-warning" colspan="5">NFe <?= $array_nfe['nNF'] ?> - Itens sobrando</th>
                </tr>
            </thead>
            <tbody>
                <tr class="text text-center text-semibold">
                    <td>Produto</td>
                    <td>Quantidade</td>
                    <td>Valor R$</td>
                </tr>
            </tbody>
            <tbody class="bg-warning text-sm text-semibold text-danger">
                <?php foreach ($arr_errados['itens_nfe_extra'] as $value) { ?>
                    <tr>
                        <td><?= $value['xProd'] ?></td>
                        <td class="text-center"><?= (float) $value['qCom'] ?></td>
                        <td class="text-right"><?= number_format((float) ($value['vUnCom']), 3, ",", ".") ?></td>

                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>

            </tfoot>
        </table>
        <?php
    }
    if(empty($arr_errados['itens_errados']) && empty($arr_errados['itens_nfe_extra']) && empty($arr_errados['itens_ped_falt'])){
        ?>
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <p>Não há divergencia entre pedido e NFE.</p>
    </div>
        <?php
    }
}

// SALVAR CADASTRO MANUAL NO BANCO DE DADOS ------------------------------------
if (isset($_REQUEST['cadastro-salvar']) && $_REQUEST['cadastro-salvar'] == 'Salvar') {

    $prestador = $nfe->consultaPrestador($_REQUEST['projeto'], $_REQUEST['prestador']);

    $array = array(// array com os dados da nota inseridos pelo usuário
        'id_regiao' => $_REQUEST['regiao'],
        'id_projeto' => $_REQUEST['projeto'],
        'Id' => $_REQUEST['chaveacesso'],
        'nNF' => $_REQUEST['numeronf'],
//        'emit_CNPJ' => $_REQUEST['cnpjcpf'],
        'emit_CNPJ' => str_replace('/', '', str_replace('-', '', str_replace('.', '', $prestador['c_cnpj']))),
        'dEmi' => converteData($_REQUEST['dt_emissao_nf']),
        'natOp' => $_REQUEST['cfop'],
        'vNF' => 0
    );

    // recupera todos os itens para o array
    for ($i = 0; $i < count($_REQUEST['id_prod']); $i++) {
        $array['det'][$i] = array(
            'nItem' => $i + 1,
            'vUnCom' => str_replace(',', '.', str_replace('.', '', $_REQUEST['vUnCom'][$i])),
            'qCom' => str_replace(',', '.', str_replace('.', '', $_REQUEST['qCom'][$i])),
            'id_prod' => $_REQUEST['id_prod'][$i],
            'cProd' => $_REQUEST['cProd'][$i],
            'nLote' => $_REQUEST['nLote'][$i],
            'dVal' => converteData($_REQUEST['dVal'][$i]),
            'vProd' => str_replace(',', '.', str_replace('.', '', $_REQUEST['vProd'][$i])),
            'id_prestador' => $_REQUEST['id_prestador']
        );
        $array['vNF'] += str_replace(',', '.', str_replace('.', '', $_REQUEST['vProd'][$i]));
    }

    $resp = $nfe->salvarNFe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array, TRUE);

    if ($resp['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal Salva com Sucesso.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp['msg'] ?>
        </div>
        <?php
    }
}
