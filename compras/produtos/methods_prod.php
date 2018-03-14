<?php
header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}
include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ProjetoClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/NFeClass.php");
include("../../classes/ContabilFornecedorClass.php");
include("../../wfunction.php");
include("../../classes/ProdutosClass.php");
include("../../classes/EstoqueClass.php");
include("../../classes/EstoqueEntradaClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/ProdutoFornecedorAssocClass.php");

$objFornecedor = new ContabilFornecedor();

$objProduto = new ProdutosClass();
$objProdFornecedor = new ProdutoFornecedorAssocClass();
$pedido = new pedidosClass();
$nfe = new NFe();
$nfe->load($_FILES['nfe']['tmp_name']);

$nfse = new NFSe();
$nfse->load($_FILES['nfse']['tmp_name']);

// MÉTODO PARA RETORNAR JSON COM OS ITENS DA TABELA Pedidos Incluir Item
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaItem') {
    $query = "SELECT xProd, id_prod 
        FROM nfe_produtos AS a 
        INNER JOIN produto_fornecedor_assoc AS b ON a.id_prod = b.id_produto 
        WHERE b.id_fornecedor = '{$_REQUEST['fornecedor']}' AND a.status = 1";
    $result = mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
        $array['prods'][] = $row['id_prod'] . ' - ' . utf8_encode($row['xProd']);
        $array['id_prods'][] = $row['id_prod'];
    }
    echo json_encode($array);
    exit();
}

// UPLOAD ----------------------------------------------------------------------
if (isset($_REQUEST['importar']) && $_REQUEST['importar'] == 'Importar') {
    if (isset($nfe->emit)) {
        ?>
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
        <h5>Importação dos produtos do arquivo XML para tabela DB</h5>
        <div style="overflow: auto;">
            <table class="table table-striped table-condensed table-hover valign-middle" >
                <thead>
                    <tr class="text text-sm text-info">
                        <th>Item</th>
                        <th>Descrição</th>
                        <th>EAN</th>
                        <th>NCM</th>
                        <th>Und</th>
                        <th class="text text-right">Valor R$</th>
                        <th><?= montaSelect(array(1 => 'Material Hospitalar', 2 => 'Medicamentos'), $value, "name='tipo_todos' id='tipo_todos' class='form-control input-sm'"); ?></th>
                        <th>&emsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nfe->det as $det) { ?>
                        <tr class="info text-sm text text-center">
                            <td><?= $det->attributes()->nItem ?></td>
                            <td class="text text-left"><?= to_iso_8859_1($det->prod->xProd) ?></td>
                            <td><?= $det->prod->cEAN ?></td>
                            <td><?= $det->prod->NCM ?></td>
                            <td><?= $det->prod->uCom ?></td>
                            <td class="text text-right"><?= number_format((float) $det->prod->vUnCom, 2, ',', '.'); ?>
                            </td>
                            <td>
                                <?= montaSelect(array(1 => 'Material Hospitalar', 2 => 'Medicamentos'), $value, "name='tipo[{$det->prod->cProd}]' class='select_tipo form-control input-sm'"); ?>
                            </td>
                            <td>
                                <a href="#" class="btn btn-success btn-xs y" data-id="<?= $det->attributes()->nItem ?>" style="display: none;">
                                    <i class="fa fa-check"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-xs n" data-id="<?= $det->attributes()->nItem ?>">
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

// SALVAR ----------------------------------------------------------------------
if (isset($_REQUEST['salvar-xml']) && $_REQUEST['salvar-xml'] == 'Salvar') {
    $emit_cnpj = (empty($nfe->emit->CNPJ)) ? $nfe->emit->CPF : $nfe->emit->CNPJ;
    $count = 0;
    $dado['cnpj'] = $nfe->emit->CNPJ;
    $emitente = $objFornecedor->consultar($dado);
    $emitente = $emitente[key($emitente)];
    foreach ($nfe->det as $det) {
        $dados = get_object_vars($det->prod);
        $dados['tipo'] = $_REQUEST['tipo'][$dados['cProd']];

        echo "<pre>";
        print_r(get_object_vars($det->prod));
        echo "</pre>";

        $result = $nfe->salvarProduto($dados, $emitente['id_fornecedor'], $emit_cnpj);
//        if (!$result['status']) {
//            $erros[] = $result['msg'];
//        } else {
//            $count++;
//        }
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

//    print_array($_REQUEST);
//    exit();

    $objProduto->setCProd($_REQUEST['cProd']);
    $objProduto->setXProd($_REQUEST['xProd']);
    $objProduto->setCEAN($_REQUEST['cEAN']);
    $objProduto->setNCM($_REQUEST['NCM']);
    $objProduto->setEXTIPI($_REQUEST['EXTIPI']);
    $objProduto->setUCom($_REQUEST['uCom']);
    $objProduto->setCEAN($_REQUEST['cEANTrib']);
    $objProduto->setUTrib($_REQUEST['uTrib']);
    $objProduto->setTipo($_REQUEST['tipo']);
    $objProduto->setEmitCnpj($_REQUEST['fornecedor']);
    $objProduto->setStatus('1');
    $objProduto->setCEANTrib($_REQUEST['cEANTrib']);
    if (isset($_REQUEST['id_prod']) && !empty($_REQUEST['id_prod'])) {
        $objProduto->setIdProd($_REQUEST['id_prod']);
    }

    $result = $objProduto->salvar();

    // salvando valores...
    // se salvou produto e tem ids de fornecedores no array
    if ($result === 1 && isset($_REQUEST['id_fornecedor']) && count($_REQUEST['id_fornecedor']) > 0) {
        foreach ($_REQUEST['id_fornecedor'] as $key => $value) { // para todos os ids
            if (!empty(str_to_float($_REQUEST['valor'][$key]))) { // ver se valor nao está vazio
                $objProdFornecedor->setIdFornecedor($value);
                $objProdFornecedor->setIdProduto($objProduto->getIdProd());
                $objProdFornecedor->setValorProduto(str_to_float($_REQUEST['valor'][$key]));
                $objProdFornecedor->setStatus(1);
                if (isset($_REQUEST['id_assoc']) && !empty($_REQUEST['id_assoc'][$key])) { // ver se id_assoc nao está vazio
                    $objProdFornecedor->setIdAssoc($_REQUEST['id_assoc'][$key]);
                }
                $objProdFornecedor->salvar(); // salva
                $objProdFornecedor->setDefault();
            }
        }
    }

    if ($result === 1) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Produtos cadastrados com sucesso.'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Erro ao salvar produto.'));
    }
    exit();
}

// CONSULTAR -------------------------------------------------------------------
if (isset($_REQUEST['consultar']) && $_REQUEST['consultar'] == 'consultar') {
    $array = $objProduto->listaProdutos($_REQUEST['fornecedor'], $_REQUEST['tipo']);
    ?>
    <div class="panel panel-default">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Descrição</th>
    <!--                    <th>EAN</th>
                    <th>NCM</th>-->
                    <th style="width: 100px;">&emsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($array as $key => $value) { ?>
                    <tr>
                        <td class="text-center"><?= $value['id_prod'] ?></td>
                        <td><?= $value['xProd'] ?></td>
        <!--                        <td><?= $value['cEAN'] ?></td>
                        <td><?= $value['NCM'] ?></td>-->
                        <td class="text-right">
                            <a href="form_produto.php?id=<?= $value['id_prod'] ?>" class="btn btn-xs btn-success btn-editar" title="Editar"><i class="fa fa-pencil"></i></a>
                            <button type="button" class="btn btn-xs btn-info assoc" data-id="<?= $value['id_prod'] ?>" title="Associações de CProd"><i class="fa fa-link"></i></button>
                            <button type="button" class="btn btn-xs btn-danger btn-excluir" data-id="<?= $value['id_prod'] ?>" title="Excluir"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
    <?php
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaFornecedor") {
    if ($_REQUEST['tipo'] == 1) {
        $id_cnae = 601;
    } else if ($_REQUEST['tipo'] == 2) {
        $id_cnae = 599;
    }
    echo $query = "SELECT c_razao AS razao, REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'-',''),'/','') AS cnpj 
                FROM prestadorservico 
                WHERE prestador_tipo = 1 AND id_regiao = {$_REQUEST['id_regiao']} AND encerrado_em > CURDATE() AND id_cnae = {$id_cnae}
                GROUP BY REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'-',''),'/','')
                ORDER BY c_razao;";
    $fornecedor = "<option value='-1'>Selecione</option>";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        $fornecedor .= "<option value=\"{$row['cnpj']}\">" . mascara_string('##.###.###/####-##', $row['cnpj']) . " - " . utf8_encode($row['razao']) . "</option>";
    }
    echo $fornecedor;
    exit;
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluirProduto') {
    $objProduto->setIdProd($_REQUEST['id']);
    if ($objProduto->inativa()) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Excluido Com sucesso.'));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Erro ao excluir'));
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'getFornecedorProjeto') {
    if ($_REQUEST['tipo'] == 1) {
        $id_cnae = 601;
    } else if ($_REQUEST['tipo'] == 2) {
        $id_cnae = 599;
    }
    $id_produto = (isset($_REQUEST['id_produto']) && !empty($_REQUEST['id_produto'])) ? $_REQUEST['id_produto'] : 0;
    $query = "SELECT *,a.id_prestador,b.nome AS projeto_nome,a.prestador_tipo,a.id_cnae
                FROM prestadorservico AS a
                INNER JOIN projeto AS b ON a.id_projeto = b.id_projeto
                LEFT JOIN produto_fornecedor_assoc AS c on (a.id_prestador = c.id_fornecedor AND c.id_produto = {$id_produto} AND c.status = 1)
                WHERE a.prestador_tipo = 1 
                AND a.id_regiao = 45 
                AND a.encerrado_em > CURDATE()
                AND id_cnae = {$id_cnae}
                AND REPLACE(REPLACE(REPLACE(a.c_cnpj,'.',''),'-',''),'/','') = '{$_REQUEST['cnpj']}';";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        $array[] = array('id_prestador' => $row['id_prestador'], 'projeto_nome' => utf8_encode($row['projeto_nome']));
    }
    echo json_encode($array);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_assoc') {
    $objProdFornecedor->setIdAssoc($_REQUEST['id']);
    $array = ($objProdFornecedor->inativa()) ? array('status' => TRUE, 'msg' => 'Excluído com sucesso.') : array('status' => FALSE, 'msg' => 'Erro ao excluir. Tente novamente.');
    echo json_encode($array);
}

// associacao de produtos ------------------------------------------------------
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'prod_assoc') {
    $query = "SELECT * FROM nfe_produtos_assoc WHERE id_produto = {$_REQUEST['id']} AND status = 1";
    $result = mysql_query($query);
    while ($row1 = mysql_fetch_assoc($result)) {
        $assocs[] = $row1;
    }
    ?>
        <form method="post" action="methods_prod.php" id="form_assoc">
        <input type="hidden" name="id_produto" value="<?= $_REQUEST['id'] ?>">
        <div class="panel panel-info">
            <div class="panel-body text-right">
                <button type="button" class="btn btn-success add_assoc"><i class="fa fa-plus"></i> Nova Associação</button> 
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>cProd</th>
                    </tr>
                </thead>
                <tbody id="table_assoc">
                    <?php foreach ($assocs as $value) { ?>
                        <tr>
                            <td>
                                <input name="id_assoc[]" type="hidden" class="form-control" value="<?= $value['id_assoc'] ?>">
                                <div class="input-group">
                                    <input name="cProd[]" type="text" class="form-control" value="<?= $value['cProd'] ?>">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-danger excluir_assoc" data-id="<?= $value['id_assoc'] ?>"><i class="fa fa-trash-o"></i> Excluir</button>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="panel-footer text-right">
                <input type="hidden" name="method" value="salvar_assoc">
                <button type="button" class="btn btn-primary" id="salvar_assoc"><i class="fa fa-floppy-o"></i> Salvar</button>
            </div>
        </div>
    </form>
    <?php
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_assoc') {
    $result = excluir_assoc($_REQUEST['id']);
    echo json_encode(array('status' => $result));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar_assoc') {
    foreach ($_REQUEST['cProd'] as $key => $value) {
        $array = array(
            'id_produto' => $_REQUEST['id_produto'],
            'cProd' => $value
        );
        if ($_REQUEST['id_assoc'][$key] > 0) {
            $array['id_assoc'] = $_REQUEST['id_assoc'][$key];
        }
        $result[] = salvar_assoc($array);
    }
    echo json_encode(array('status'=> !in_array(FALSE, $result)));
}

// associacao de produtos ------------------------------------------------------
// -----------------------------------------------------------------------------
function str_to_float($string) {
    return str_replace('@', ',', str_replace(',', '.', str_replace('.', '@', $string)));
}

// ---- funcoes do assoc (um dia criar uma classe) ------------------------------
function salvar_assoc($array) {
    if (isset($array['id_assoc'])) {
        $r = update_assoc($array);
    } else {
        $r = insert_assoc($array);
    }
    return $r;
}

function insert_assoc($array) {
    $campos = implode(',', array_keys($array));
    $valores = implode('\',\'', array_values($array));
    $query = "INSERT INTO nfe_produtos_assoc ($campos) VALUES ('$valores')";
    return mysql_query($query) or die($query . ' ' . mysql_error());
}

function update_assoc($array) {
    $id = $array['id_assoc'];
    unset($array['id_assoc']);
    foreach ($array as $key => $value) {
        $arr[] = "$key = '$value'";
    }
    $edit = implode(',', $arr);
    $query = "UPDATE nfe_produtos_assoc SET $edit WHERE id_assoc = $id";
    return mysql_query($query) or die($query . ' ' . mysql_error());
}

function excluir_assoc($id) {
    $query = "UPDATE nfe_produtos_assoc SET status = 0 WHERE id_assoc = {$_REQUEST['id']}";
    return mysql_query($query) or die($query . ' ' . mysql_error());
}

// ---- funcoes do assoc (um dia criar uma classe ------------------------------