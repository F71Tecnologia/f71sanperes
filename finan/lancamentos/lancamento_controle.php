<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes_permissoes/acoes.class.php");
include_once("../../classes/FinanceiroFechamentoClass.php");
include_once ("classe/SaidaTravarLancamentoClass.php");
include_once ("classe/EntradaTravarLancamentoClass.php");
include_once("../../classes/global.php");

$ACOES = new Acoes();

$usuario = carregaUsuario();
$objLancamento = new FinanceiroFechamentoClass();
$objSaidaLancamento = new SaidaTravarLancamentoClass();
$objEntradaLancamento = new EntradaTravarLancamentoClass();

$projetosRegiao = implode(',', array_keys(getProjetos($usuario['id_regiao'])));

if (isset($_REQUEST['criar']) && $_REQUEST['criar'] == 'Criar') {

    $criar_lote = $classificacao->criarLote($_REQUEST['projeto'], $_REQUEST['lote'], $usuario['id_funcionario'], $_REQUEST['exercicio']);

//    header("Location: classificacao.php");
    if ($criar_lote) {
        echo json_encode(array('msg' => 'Salvo com sucesso', 'status' => 'success'));
    } else {
        echo json_encode(array('msg' => 'Erro ao Salvar', 'status' => 'Danger'));
    }

    exit;
}

if (isset($_REQUEST['filtrar']) && $_REQUEST['filtrar'] == 'Filtrar') {

    $lotecriado = $classificacao->lotesCriados($_REQUEST['projeto']);

    echo json_encode($lotecriado);
    exit;
}

if (isset($_REQUEST['save_simples'])) {

    $salvar = $classificacao->salvaLancamentoSimples($_REQUEST['lotes'], $_REQUEST['projetos'], $usuario['id_funcionario'], ConverteData($_REQUEST['data_lancaments'], 'Y-m-d'));

    // salvar conta devedora
    $item_lancamentoD = array(
        'id_lancamento' => $salvar['id_lancamento'],
        'id_conta' => $_REQUEST['contad_id'],
        'tipo' => $_REQUEST['tipo_2'],
        'valor' => str_replace(",", ".", str_replace(".", "", $_REQUEST['valor'])),
        'documento' => $_REQUEST['documento'],
        'historico' => $_REQUEST['historico']
    );
    $itens_lancamento_simplesD = $classificacao->preparaArrayItens($item_lancamentoD);

    // salvar conta credora
    $item_lancamentoC = array(
        'id_lancamento' => $salvar['id_lancamento'],
        'id_conta' => $_REQUEST['contac_id'],
        'tipo' => $_REQUEST['tipo_1'],
        'valor' => str_replace(",", ".", str_replace(".", "", $_REQUEST['valor']))
    );
    $itens_lancamento_simplesC = $classificacao->preparaArrayItens($item_lancamentoC);

    if ($resp['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">X</button>
            <p>Classificação salva</p>
        </div> 
    <?php } else {
        ?>
        <?php ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">X</button>
            <strong>Atenção </strong>' <?= $resp['msg'] ?> '</div>
        <?php
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancelarlote') {
    $status = $classificacao->cancelarLote($_REQUEST['projeto'], $_REQUEST['nrlote'], $usuario['id_funcionario']);
    echo json_encode(array('status' => $status));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'excluir_lancamento') {
    $objLancamento->setIdLancamento($_REQUEST['id']);
    $objLancamento->setStatus('0');
    $objLancamentoItens->excluirByLancamento($_REQUEST['id']);
    $retorno = ($objLancamento->inativa()) ? array('status' => 'success', 'msg' => 'Excluido com Sucesso!') : array('status' => 'danger', 'msg' => 'Erro ao Excluir!');
    echo json_encode($retorno);
}


if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'travar') {
    
    $inicio = $_REQUEST['ano_periodo']."-".$_REQUEST['mes_periodo']."-01"; 
    
    $objLancamento->setIdProjeto($_REQUEST['projeto']); 
    $objLancamento->setMesFechado($_REQUEST['mes_periodo']); 
    $objLancamento->setAnoFechado($_REQUEST['ano_periodo']); 
    $objLancamento->setUsuario($usuario['id_funcionario']); 
    $objLancamento->setStatus(1); 
    $objLancamento->setLancamento($_REQUEST['lancamento']); 
    $objLancamento->setFechadoEm(date('Y-m-d H:i:s'));
    
    $travar = $objLancamento->insert();

    $objSaidaLancamento->setIdProjeto($_REQUEST['projeto']);
    $objSaidaLancamento->setStatus(2);
    $objSaidaLancamento->setDataVencimento($inicio);
    
    $saida_travar = $objSaidaLancamento->updateTrava();

    $objEntradaLancamento->setIdProjeto($_REQUEST['projeto']);
    $objEntradaLancamento->setStatus(2);
    $objEntradaLancamento->setDataVencimento($inicio);

    $entrada_travar = $objEntradaLancamento->updateTrava();
    
//    header("Location: classificacao.php");
    if ($travar && $saida_travar && $entrada_travar) {
        echo json_encode(array('msg' => 'Salvo com sucesso', 'status' => 'success'));
    } else {
        echo json_encode(array('msg' => 'Erro ao Salvar', 'status' => 'Danger'));
    }
    exit;
}
    
   
//------------------------------------------------------------------------------

function checkEmpty($var) {
    return (!empty($var)) ? $var : NULL;
}

function strToNum($string) {
    return str_replace(',', '.', str_replace('.', '', $string));
}
