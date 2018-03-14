<?php
//header('Content-Type: text/html; charset=utf-8'); 
header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFeClass.php");
include("../../classes/EstoqueClass.php");
include("../../classes/EstoqueEntradaClass.php");
include("../../classes/EstoqueSaidaClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/global.php");
include("../../classes/ContabilContasPagarClass.php");
include("../../classes/NfeItensClass.php");

$usuario = carregaUsuario();
$objNfe = new NFe();
$objNfeItens = new NfeItens();
$objPedido = new pedidosClass();
$objContasPagar = new ContabilContasPagarClass();

$objNfe->load($_FILES['nfe']['tmp_name']);

// consulta cnpj para ver se há projeto cadastrado
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultacnpjcpf') {
    $prestador = $objNfe->consultaPrestador($_REQUEST['projeto'], $_REQUEST['cnpjcpf']);
    if (count($prestador) == 0) {
        $retorno = array(
            'status' => FALSE,
            'msg' => utf8_encode('Não há cadastro do Prestador ou fornecedor. Cadastro não poderé ser salvo.')
        );
    } else {
        $retorno['dados'] = array(
            'status' => TRUE,
            'nome' => utf8_encode($prestador['c_razao']),
            'endereco' => utf8_encode($prestador['c_endereco']),
            'cnpjcpf' => utf8_encode($prestador['c_cnpj'])
        );
    }
    echo json_encode($retorno);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultapedidos') {
    $consultapedido = $objPedido->PedidoEnviados($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador']);
    echo '<option value="">Selecione</option>';
    foreach ($consultapedido as $key => $value) {
        $data = converteData($value['data'], 'd/m/Y');
        $total = number_format($value['total'], 2, ',', '.');
        echo "<option value = '$key'>Número.: $key Data.: $data  Total.: R$ $total</option>";
    }
    exit();
}

// trazer item para cadastro manual
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'itemIncluir') {
    $produtos = $objNfe->consultarProduto($_REQUEST['id_prod']);
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
if (isset($_REQUEST['importar']) && $_REQUEST['importar'] == 'Visualizar') {
    $cnpj_proj = $objNfe->dest->CNPJ;
    $query = "SELECT id_projeto FROM rhempresa WHERE REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') = '$cnpj_proj'";
    $proj = mysql_fetch_assoc(mysql_query($query));
    $array_nfe = $objNfe->nfe_xml_to_array();

    // verificacao de cadastro NFe (NFe  já foi cadastrda?) --------------------
    $resposta = $objNfe->verificarNFe($array_nfe['Id']);
    if (!$resposta['status']) {
        echo json_encode(array('status' => FALSE, 'msg' => utf8_encode("<h4 class='text-danger'>Nota Fiscal Já cadastrada!</h4> <p> <strong>Id da NFe:</strong> {$array_nfe['Id']}</p>")));
        exit();
    }
    // fim da verificacao do cadastro da NFe -----------------------------------
    // verificacao se projeto da nota eh o correto -----------------------------
    $query = "SELECT IF(COUNT(id_pedido) = 1,TRUE,FALSE) AS retorno FROM pedidos AS a
            INNER JOIN rhempresa AS b ON a.id_projeto = b.id_projeto
            WHERE a.id_pedido = {$_REQUEST['pedidoss']} AND REPLACE(REPLACE(REPLACE(b.cnpj,'.',''),'-',''),'/','') = '{$array_nfe['dest_CNPJ']}'";

    $retorno = mysql_fetch_assoc(mysql_query($query));
    if ($retorno['retorno'] == FALSE) {
        echo json_encode(array('status' => FALSE, 'msg' => 'O Projeto da NF diverge do informado no pedido.'));
        exit();
    }
    // verificacao se projeto da nota eh o correto -----------------------------
    // verificacao se prestador da nota eh o correto ---------------------------
    $query = "SELECT IF(COUNT(id_pedido) = 1,TRUE,FALSE) AS retorno
            FROM pedidos AS a
            INNER JOIN prestadorservico AS b ON a.id_prestador = b.id_prestador
            WHERE a.id_pedido = {$_REQUEST['pedidoss']} AND REPLACE(REPLACE(REPLACE(b.c_cnpj,'.',''),'-',''),'/','') = '{$array_nfe['emitente']}';";

    $retorno = mysql_fetch_assoc(mysql_query($query));
    if ($retorno['retorno'] == FALSE) {
        echo json_encode(array('status' => FALSE, 'msg' => 'O Fornecedor da NF diverge do informado no pedido.'));
        exit();
    }
    //  verificacao se prestador da nota eh o correto --------------------------
    // 
    // 
    // CONFERIR NOTA COM O PEDIDO ----------------------------------------------
    $itens_pedido = $objPedido->consultarItem("A.id_pedido = {$_REQUEST['pedidoss']} AND A.entregue IN(0,1)", $_REQUEST['fornecedor']);
    $itens_nfe = $objNfe->agrupaItens($array_nfe['det']);
    $validacao = $objNfe->validaNFE($itens_nfe, $itens_pedido);

    // produtos não vinculados -------------------------------------------------
    foreach ($objNfe->arrayItensPedFalta as $value) {
        $list .= $value['id_prod'] . ",";
    }

    $list = substr($list, 0, -1);

    $query2 = "SELECT * 
                FROM pedidos_itens AS a
                INNER JOIN nfe_produtos as b ON a.id_prod = b.id_prod
                WHERE a.id_pedido = {$_REQUEST['pedidoss']} AND a.id_prod  IN($list);";

//    echo (isset($_COOKIE['teste'])) ? $query2 : '';
    $result1 = mysql_query($query2);
    $optProdutos[0] = 'Selecione Produto';
    while ($row = mysql_fetch_assoc($result1)) {
        $optProdutos[$row['id_prod']] = $row['xProd'];
    }
    // produtos nao vinculados -------------------------------------------------
    // 
    // trexo com a impressao HTML
    ob_start();
    include 'nfe_lista_conferencia_pedido.php';
    $html = ob_get_contents();
    ob_end_clean();

    $retorno['dados'] = array(// dados da nota para visualizacao
        "cnpj_f" => (string) $objNfe->emit->CNPJ,
        "cnpj_forn" => (string) mascara_string("##.###.###/####-##", $objNfe->emit->CNPJ),
        "fornecedor" => (string) $objNfe->emit->xNome,
        "emissao" => (string) date("d/m/Y", strtotime($objNfe->ide->dhEmi)),
        "nf_nr" => (string) $objNfe->ide->nNF,
        "valor_nf" => (string) number_format((float) $objNfe->total->ICMSTot->vNF, 2, ',', '.'),
        "cnpj_c" => (string) $objNfe->dest->CNPJ,
        "cnpj_clie" => (string) mascara_string("##.###.###/####-##", $objNfe->dest->CNPJ),
        "cliente" => (string) $objNfe->dest->xNome,
        "end_cliente" => utf8_encode((string) $objNfe->dest->enderDest->xLgr),
        'tabela' => utf8_encode($html)
    );

    $retorno['settings']['erase_form'] = FALSE;

    echo json_encode($retorno);
    exit();
}

// SALVAR XML NO BANCO DE DADOS ------------------------------------------------
if (isset($_REQUEST['salvar']) && $_REQUEST['salvar'] == 'Aceitar NFe') {
    $id_projeto = $_REQUEST['projeto'];
    $id_fornecedor = $_REQUEST['fornecedor'];
    $id_pedido = $_REQUEST['pedidoss'];

    $arr_nfe = $objNfe->nfe_xml_to_array(); // converte xml para array
    //
    // verificacao de cadastro NFe (NFe  já foi cadastrda?) --------------------
    $resposta = $objNfe->verificarNFe($arr_nfe['Id']);
    if (!$resposta['status']) {
        echo json_encode(array('status' => FALSE, 'msg' => utf8_encode("Nota Fiscal Já cadastrada = NFe$Id")));
        exit();
    }
    // fim da verificacao do cadastro da NFe -----------------------------------
    // 
    // verificacao se prestador da nota eh o correto ---------------------------
    $query = "SELECT IF(COUNT(id_pedido) = 1,TRUE,FALSE) AS retorno
            FROM pedidos AS a
            INNER JOIN prestadorservico AS b ON a.id_prestador = b.id_prestador
            WHERE a.id_pedido = {$id_pedido} AND REPLACE(REPLACE(REPLACE(b.c_cnpj,'.',''),'-',''),'/','') = '{$arr_nfe['emitente']}';";

    $retorno = mysql_fetch_assoc(mysql_query($query));
    if ($retorno['retorno'] == FALSE) {
        echo json_encode(array('status' => FALSE, 'msg' => 'O Fornecedor da NF diverge do informado no pedido.'));
        exit();
    }
    // verificacao se prestador da nota eh o correto ---------------------------
    //  
    // verificacao se projeto da nota eh o correto -----------------------------
    $query = "SELECT IF(COUNT(id_pedido) = 1,TRUE,FALSE) AS retorno FROM pedidos AS a
            INNER JOIN rhempresa AS b ON a.id_projeto = b.id_projeto
            WHERE a.id_pedido = {$id_pedido} AND REPLACE(REPLACE(REPLACE(b.cnpj,'.',''),'-',''),'/','') = '{$arr_nfe['dest_CNPJ']}'";

    $retorno = mysql_fetch_assoc(mysql_query($query));
    if ($retorno['retorno'] == FALSE) {
        echo json_encode(array('status' => FALSE, 'msg' => 'O Projeto da NF diverge do informado no pedido.'));
        exit();
    }
    // verificacao se prestador da nota eh o correto ---------------------------
    //
    // query para pegar dados do projeto
    $qr_projeto = "SELECT a.id_regiao,a.id_projeto,b.nome AS nome_projeto,c.regiao AS nome_regiao
                    FROM rhempresa AS a
                    INNER JOIN projeto AS b ON a.id_projeto = b.id_projeto
                    INNER JOIN regioes AS c ON b.id_regiao = c.id_regiao 
                    WHERE REPLACE(REPLACE(REPLACE(a.cnpj,'-',''),'/',''),'.','') = '{$arr_nfe['dest_CNPJ']}'";
    $projeto = mysql_fetch_assoc(mysql_query($qr_projeto));
    $arr_pedidos = $objPedido->consultaPedidoById($id_pedido);

    // fim da query para pegar dados do projeto --------------------------------
    // 
    // salvar NFe --------------------------------------------------------------
    $array_salvar = array(
        'id_regiao' => $arr_pedidos['id_regiao'],
        'id_projeto' => $id_projeto,
        'Id' => str_replace('NFe', '', $arr_nfe['Id']),
        'versao' => $arr_nfe['versao'],
        'cUF' => $arr_nfe['cUF'],
        'cNF' => $arr_nfe['cNF'],
        'natOp' => $arr_nfe['natOP'],
        'indPag' => $arr_nfe['indPad'],
        'mod' => $arr_nfe['mod'],
        'serie' => $arr_nfe['serie'],
        'nNF' => $arr_nfe['nNF'],
        'dEmi' => $arr_nfe['dEmi'],
        'dSaiEnt' => $arr_nfe['dSaiEnt'],
        'hSaiEnt' => $arr_nfe['hSaiEnt'],
        'tpNF' => $arr_nfe['tpNF'],
        'cMunFG' => $arr_nfe['cMunFG'],
        'tpImp' => $arr_nfe['tpImp'],
        'tpEmis' => $arr_nfe['tpEmis'],
        'cDV' => $arr_nfe['cDV'],
        'tpAmb' => $arr_nfe['tpAmb'],
        'finNFe' => $arr_nfe['finNFe'],
        'procEmi' => $arr_nfe['procEmi'],
        'verProc' => $arr_nfe['verProc'],
        'dhCont' => $arr_nfe['dhCont'],
        'xJust' => $arr_nfe['xJust'],
        'emitente' => $id_fornecedor,
        'IE' => $arr_nfe['IE'],
        'IEST' => $arr_nfe['IEST'],
        'IM' => $arr_nfe['IM'],
        'CNAE' => $arr_nfe['CNAE'],
        'CRT' => $arr_nfe['CRT'],
//    'destinatario' => $arr_nfe['Id'],
        'retirada' => $arr_nfe['retirada'],
        'entrega' => $arr_nfe['entrega'],
        'vBC' => $arr_nfe['vBC'],
        'vICMS' => $arr_nfe['vICMS'],
        'vBCST' => $arr_nfe['vBCST'],
        'vST' => $arr_nfe['vST'],
        'vProd' => $arr_nfe['vProd'],
        'vFrete' => $arr_nfe['vFrete'],
        'vSeg' => $arr_nfe['vSeg'],
        'vDesc' => $arr_nfe['vDesc'],
        'vII' => $arr_nfe['vII'],
        'vIPI' => $arr_nfe['vIPI'],
        'vPIS' => $arr_nfe['vPIS'],
        'vCOFINS' => $arr_nfe['vCOFINS'],
        'vOutro' => $arr_nfe['vOutro'],
        'vNF' => $arr_nfe['vNF'],
        'modFrete' => $arr_nfe['modFrete'],
        'infAdFisco' => $arr_nfe['infAdFisco'],
        'nFat' => $arr_nfe['nFat'],
        'status' => 1,
        'xPed' => $arr_nfe['xPed'],
        'id_pedido' => $id_pedido,
    );

    $id_nfe = $objNfe->salvarNFe($array_salvar);
    if ($id_nfe) {
        foreach ($arr_nfe['det'] as $key => $value) {
            $arr_iped = $objPedido->getProdutoByCProdCNPJ($value['cProd'], $arr_nfe['emitente']);
            $item = array(
                'id_nfe' => $id_nfe,
                'id_produto' => $arr_iped['id_prod'],
                'nItem' => $value['nItem'],
                'CFOP' => $value['CFOP'],
                'qCom' => $value['qCom'],
                'vProd' => $value['vProd'],
                'qTrib' => $value['qTrib'],
                'vUnCom' => $value['vunCom'],
                'vFrete' => $value['vFrete'],
                'vSeg' => $value['vSeg'],
                'vDesc' => $value['vDesc'],
                'vOutros' => $value['vOutros'],
                'indTot' => $value['indTot'],
                'status' => 1,
                'nLote' => $value['nLote'],
                'qLote' => $value['qLote'],
                'dFab' => $value['dFab'],
                'dVal' => $value['dVal'],
                'vPMC' => $value['vPMC'],
            );
            $objNfeItens->salvar($item);
            unset($item);
            unset($arr_iped);
            unset($value);
        }

        // fim salvar NFe ----------------------------------------------------------

        $status_pedido = NULL;
        $html = "";

        // verificando status do pedido ----------------------------------------
        $itens_nfe = $objNfe->agrupaItens($arr_nfe['det']);
        $itens_pedido2 = $objPedido->consultarItem("id_pedido = '{$id_pedido}'", $id_fornecedor);
        $validacao = $objNfe->validaNFE($itens_nfe, $itens_pedido2);

        if ($validacao === TRUE) {
            $objPedido->atualizaStatusFinalizado($id_pedido);
            $status_pedido = 5;
        } else if ($validacao === FALSE) { // temque ser (bool) FALSE pq se for NULL é erro
            $objPedido->atualizaStatusAberto($id_pedido);
            $status_pedido = 4;
        }
        // fim verificando status do pedido ------------------------------------
        // atualizando status dos itens ----------------------------------------
        $itens_pedido1 = $objPedido->consultarItem(array('id_pedido' => $id_pedido), $id_fornecedor);
        foreach ($itens_pedido1 as $value) {
            $objPedido->incrementaQtdRecebida($value['id_item'], $id_nfe);
        }
        // fim atualizando status dos itens ------------------------------------
        // gerando saída -------------------------------------------------------

        /*
         * Gerar saidas não foi implementado ainda pois será verificado a 
         * possibilidade de criar uma tela no financeiro para avisar que existe 
         * nota para pagamento.
         */

//        $ano = date('Y', strtotime($arr_nfe['dEmi']));
//        $mes = date('m', strtotime($arr_nfe['dEmi']));
//
//        $qr_prestador = "SELECT id_prestador,c_razao AS razao, REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'-',''),'/','') AS cnpj, id_cnae 
//                        FROM prestadorservico
//                        WHERE id_projeto = {$id_projeto} AND id_prestador = $id_fornecedor";
//
//        echo $qr_prestador;
//        $result = mysql_query($qr_prestador) OR die($qr_prestador . " " . mysql_error());
//        $prestador = mysql_fetch_assoc($result);
//
//        if ($prestador['id_cnae'] == 599) {
//            $especifica = "Compra de Medicamentos - NFe: {$arr_nfe['nNF']}";
//        } else if ($prestador['id_cnae'] == 601) {
//            $especifica = "Compra de Materiais Hospitalares - NFe: {$arr_nfe['nNF']}";
//        } else {
//            $especifica = "Compra de Produtos - NFe: {$arr_nfe['nNF']}";
//        }
//
//        $array_saida = array(
//            'id_regiao' => $projeto['id_regiao'],
//            'id_projeto' => $projeto['id_projeto'],
//            'id_user' => $usuario['id_funcionario'],
//            'nome' => "{$prestador['razao']} - Regiao: {$projeto['nome_regiao']} - Projeto: {$projeto['nome_projeto']}",
//            'especifica' => $especifica,
//            'tipo' => $prestador['id_cnae'],
//            'valor' => (float) $arr_nfe['vNF'],
//            'valor_bruto' => (float) $arr_nfe['vNF'],
//            'id_prestador' => $prestador['id_prestador'],
//            'nome_prestador' => $prestador['razao'],
//            'cnpj_prestador' => $prestador['cnpj'],
//            'mes_competencia' => $mes,
//            'ano_competencia' => $ano
//        );
        // fim gerando saída ---------------------------------------------------

        $html .= "<div class='alert alert-dismissable alert-success'>
                    <button type='button' class='close' data-dismiss='alert'>×</button>
                    <h4>Salvo!</h4>
                    <p>Nota Fiscal Salva com Sucesso.</p>
                  </div>";
        $erase_form = TRUE;
    } else {
        $html .= "<div class='alert alert-dismissable alert-danger'>
                    <button type='button' class='close' data-dismiss='alert'>×</button>
                    <strong>Atenção ...!</strong> {$resp['msg']}
                  </div>";
        $erase_form = FALSE;
    }
    $retorno['dados'] = array('tabela' => utf8_encode($html));
    $retorno['settings'] = array('erase_form' => $erase_form, 'pedido_status' => $status_pedido);
    echo json_encode($retorno);
}

// SALVAR CADASTRO MANUAL NO BANCO DE DADOS ------------------------------------
if (isset($_REQUEST['cadastro-salvar']) && $_REQUEST['cadastro-salvar'] == 'Salvar') {

    //$prestador = $objNfe->NFeConsultaFornecedor($_REQUEST['projeto'], $_REQUEST['prestador']);

    $array = array(// array com os dados da nota inseridos pelo usuário
        'id_regiao' => $_REQUEST['id_regiao'],
        'id_projeto' => $_REQUEST['id_projeto'],
        'Id' => str_replace(' ', '', $_REQUEST['chaveacesso']),
        'nNF' => $_REQUEST['numeronf'],
        'emit_CNPJ' => $_REQUEST['razao_cnpj'],
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

    print_array($array);


    $resp = $objNfe->salvarNFe($_REQUEST['projeto'], $array, TRUE, $id_pedido);

    if ($resp['status']) {
        $html .= "<div class='note note-success'>
                    <h4 class='note-title'>Nota Fiscal Salva com Sucesso.</h4>
                </div>";
        $erase_form = TRUE;
    } else {
        $html .= "<div class='note note-danger'>
           <h4 class='note-title'> Atenção ...!<?= {$resp['msg']} ?>
        </div>";
        $erase_form = FALSE;
    }
    $html = utf8_encode($html);
    $retorno['dados'] = array('tabela' => $html);
    $retorno['settings'] = array('erase_form' => $erase_form);
    echo json_encode($retorno);
}

// abre o formulário para importação das NFe
if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'open_form_importacao_nfe') {
    $dados['id_pedido'] = $_REQUEST['id_pedido'];
    $lista = $objNfe->consultaNFe($dados);
    $id_fornecedor = $_REQUEST['id_fornecedor'];
    $id_projeto = $_REQUEST['id_projeto'];
    require_once 'form_importacao_nfe.php';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'vincular_produto') {
    $id_prod = $_REQUEST['id_prod'];
    $id_cProd = $_REQUEST['cProd'];

    $query = "INSERT INTO nfe_produtos_assoc(id_produto,cProd) VALUES ($id_prod,$id_cProd);";
    $status = mysql_query($query);
    echo json_encode(array('status' => $status));
}

// acao do botao finaliza pedido
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'finalizar_pedido'){
    $id_pedido = addslashes($_REQUEST['id_pedido']);
    
    // POSTERGADO PARA IMPLEMENTACOES FUTURAS...
//    $desconto = addslashes($_REQUEST['desconto']);
//    $descricao_desconto = addslashes($_REQUEST['descricao_desconto']);
    if(!empty($id_pedido)){
        $result = $objPedido->atualizaStatusFinalizado($id_pedido);
    }
    
    echo json_encode(array('status' =>$result));
    exit();
}

/*
 * Campos Necessários:
 * id_regiao, id_projeto, id_user, nome, especifica,
 * tipo, valor, valor_bruto, id_prestador, nome_prestador,
 * cnpj_prestador, mes_competencia, ano_competencia
 */

function gerarSaida($array) {
    foreach ($array as $key => $value) {
        $arr_campos[] = $key;
        $arr_valores[] = $value;
    }
    $str_campos = implode(',', $arr_campos);
    $str_valores = implode(',', $arr_valores);
    $query = "INSERT INTO saida ($str_campos,data_cad) VALUES ($str_valores,NOW())";
    return mysql_query($query) or die("Erro ao gerar saída. Query: $query Detalhe do Erro: " . mysql_error());
}

function getIdPrestadorServico($id_fornecedor, $id_projeto, $tipo) {
    $query = "SELECT id_prestador FROM prestadorservico WHERE id_fornecedor = $id_fornecedor AND id_projeto = $id_projeto";
    $result = mysql_query($query);
    $retorno = mysql_fetch_assoc($result);
    return $retorno['id_prestador'];
}
