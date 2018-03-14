<?php
header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
include('../../conn.php');
include('../../classes/global.php');
include("../../classes/ProjetoClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/NFeClass.php");
include('../../wfunction.php');
include("../../classes/EstoqueClass.php");
include("../../classes/EstoqueEntradaClass.php");

$nfe = new NFe();
$nfe->load($_FILES['nfe']['tmp_name']);

$nfse = new NFSe();
$nfse->load($_FILES['nfse']['tmp_name']);

// MÉTODO PARA RETORNAR JSON COM OS ITENS DA TABELA nfeprodutos
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaItem') {
    $query = "SELECT xProd, id_prod FROM nfe_produtos WHERE id_prestador = '{$_REQUEST['prestador']}' AND status = 1";
//    echo $query; exit();
    $result = mysql_query($query);
//    echo mysql_num_rows($result);
    while ($row = mysql_fetch_assoc($result)) {
        $array['prods'][] = $row['id_prod'] . ' - ' . utf8_encode($row['xProd']);
        $array['id_prods'][] = $row['id_prod'];
    }
    echo json_encode($array);
    exit();
}

// UPLOAD ----------------------------------------------------------------------
if (isset($_REQUEST['importar']) && $_REQUEST['importar'] == 'Importar') {
    if (isset($nfe->emit)) { ?>
        <table class="table table-striped table-condensed" style="overflow: auto;">
            <thead>
                <tr class="text text-sm">
                    <th style="width: 30%;">Nome Fantasia</th>
                    <th style="width: 50%;">Razao Social</th>
                    <th style="width: 20%;">CNPJ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $nfe->emit->xFant ?></td>
                    <td><?= $nfe->emit->xNome ?></td>
                    <td><?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    if (isset($nfe->det)) {
        ?>
        <h4>produtos para importação</h4>
        <div style="overflow: auto;">
            <table class="table table-striped table-condensed table-hover" >
                <thead>
                    <tr class="text text-sm">
                        <th style="width: 3%;">N&ordm;</th>
                        <th style="width: 32%;">Descrição</th>
                        <th style="width: 10%;">Cod Produto</th>
                        <th style="width: 13%;">Cod EAN</th>
                        <th style="width: 10%;">Cod NCM</th>
                        <th style="width: 7%;">Gênero</th>
                        <th style="width: 10%;">Und</th>
                        <th style="width: 10%;">Valor R$</th>
                        <th style="width: 3%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nfe->det as $det) { ?>
                        <tr class="info">
                            <td><?= $det->attributes()->nItem ?></td>
                            <td><?= to_iso_8859_1($det->prod->xProd) ?></td>
                            <td><?= $det->prod->cProd ?></td>
                            <td><?= $det->prod->cEAN ?></td>
                            <td><?= $det->prod->NCM ?></td>
                            <td><?= $det->prod->genero ?></td>
                            <td><?= $det->prod->uCom ?></td>
                            <td class="text text-right"><?= number_format((float) $det->prod->vUnCom, 2, ',', '.'); ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-success btn-xs btn-block y" data-id="<?= $det->attributes()->nItem ?>" style="display: none;">
                                    <i class="fa fa-check"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-xs btn-block n" data-id="<?= $det->attributes()->nItem ?>">
                                    <i class="fa fa-times"></i>
                                </a>
                                <input type="hidden" name="ok-<?= $det->attributes()->nItem ?>" id="ok-<?= $det->attributes()->nItem ?>" value="1">
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="namefile" id="namefile" value="<?= $nfe->savedFile; ?>">
        <?php
    }
    exit();
}
if (isset($_REQUEST['importar1']) && $_REQUEST['importar1'] == 'Importar') {
    if (isset($nfse->PrestadorServico)) { ?>
        <h4>Prestador de Serviço</h4>
        <table class="table" style="overflow: auto;">
            <thead>
                <tr>
                    <th style="width: 40%;">Nome Fantasia</th>
                    <th style="width: 40%;">Razao Social</th>
                    <th style="width: 20%;">CNPJ/CPF</th>
                </tr>
            </thead>
            <tbody>
                <tr class="active">
                    <td><?= $nfse->PrestadorServico->NomeFantasia ?></td>
                    <td><?= $nfse->PrestadorServico->RazaoSocial ?></td>
                    <td><?= mascara_string("##.###.###/####-##", $nfse->PrestadorServico->IdentificacaoPrestador->Cnpj ) ?></td>
                </tr>
            </tbody>
        </table>
        <?php
    }
    if (isset($nfse->Servico)) { ?>
        <p>Discriminação do Serviço</p>
        <h4><?= to_iso_8859_1($nfse->Servico->Discriminacao) ?></h4>
        <div style="overflow: auto;">
            <table class="table table-striped table-hover">
                <thead>
                    <tr><td colspan="6">Impostos Retidos<td></tr>
                    <tr>
                        <th style="width: 16%;">COFINS</th>
                        <th style="width: 16%;">CSLL</th>
                        <th style="width: 16%;">INSS</th>
                        <th style="width: 16%;">IRPJ</th>
                        <th style="width: 16%;">PIS</th>
                        <th style="width: 20%;">Outras</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nfse->Servico->Valores as $valores) { ?>
                        <tr class="success text-left">
                            <td>R$ <?= number_format((float)$valores->ValorCofins ,2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorCsll, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorInss, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorIr, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorPis, 2, ',', '.') ?></td>
                            <td>R$</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="namefile" id="namefile" value="<?= $nfse->savedFile; ?>">
        <?php
    }
    exit();
}

// SALVAR ----------------------------------------------------------------------
if (isset($_REQUEST['salvar-xml']) && $_REQUEST['salvar-xml'] == 'Salvar') {
    echo 'esse é ceto';
    exit();
    $emit_cnpj = (empty($nfe->emit->CNPJ))?$nfe->emit->CPF:  $nfe->emit->CNPJ;
    $count = 0;
    foreach ($nfe->det as $det) {
        $dados = get_object_vars($det->prod);
        print_r($dados);
        exit();
        $result = $nfe->salvarProduto($dados, $emit_cnpj);
        if (!$result['status']) {
            $erros[] = $result['msg'];
        } else {
            $count++;
        }
    }

    if ($count > 0) {
        ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <?= "$count produtos cadastrados com sucesso." ?>
        </div>
        <?php
    }

    if (!empty($erros)) {
        ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <h4>Atenção - Produtos já cadastrados!</h4>
            <ul>
                <?php foreach ($erros as $msg) { ?>
                    <li><?= to_iso_8859_1($msg); ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php
    }

    exit();
}

// SALVAR CADASTRO -------------------------------------------------------------
if (isset($_REQUEST['cadastro-salvar'])) {
    $dados = array(
        'cProd' => $_REQUEST['cProd'],
        'xProd' => $_REQUEST['xProd'],
        'cEAN' => $_REQUEST['cEAN'],
        'NCM' => $_REQUEST['NCM'],
        'EXTIPI' => $_REQUEST['EXTIPI'],
        'uCom' => $_REQUEST['uCom'],
        'vUnCom' => $_REQUEST['vUnCom'],
        'cEANTrib' => $_REQUEST['cEANTrib'],
        'uTrib' => $_REQUEST['uTrib'],
    );
    $result = $nfe->salvarProduto($dados, $_REQUEST['prestador']);

    if ($result['status']) {
        ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p>Produtos cadastrados com sucesso.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p><?= $result['msg'] ?></p>
        </div>
        <?php
    }
    exit();
}
    