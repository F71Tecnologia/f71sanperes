<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/c_planodecontasClass.php");
include_once("../../classes/c_classificacaoClass.php");
include_once("../../classes/ContabilLancamentoClass.php");
include_once("../../classes/ContabilLancamentoItemClass.php");
include_once("../../classes/ContabilLoteClass.php");
include_once("../../classes_permissoes/acoes.class.php");
require_once("../../classes/ContabilContasSaldoClass.php");

include_once("../../classes/global.php");

$ACOES = new Acoes();

$usuario = carregaUsuario();
$classificacao = new c_classificacaoClass();
$objLancamento = new ContabilLancamentoClass();
$objLancamentoItens = new ContabilLancamentoItemClass();
$objLote = new ContabilLoteClass();
$objContas = new ContabilContasSaldoClass();

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

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'retornaConta') {
    $qry = "SELECT id_conta, cod_reduzido, descricao, classificador, nivel, 
            IF(a.id_historico > 0,(SELECT texto FROM contabil_historico_padrao AS b WHERE b.id_historico = a.id_historico),'') AS historico 
            FROM contabil_planodecontas AS a 
            WHERE id_projeto = '{$_REQUEST['projeto']}' AND nivel > 3 AND a.status = 1 AND (cod_reduzido LIKE '%{$_REQUEST['codigo']}%' OR classificador LIKE '%{$_REQUEST['codigo']}%' OR descricao LIKE '%{$_REQUEST['codigo']}%' OR cod_reduzido LIKE '%{$_REQUEST['codigo']}%')
            ORDER BY classificador, nivel";
    $result = mysql_query($qry) or die(mysql_error());
    while ($row = mysql_fetch_array($result)) {
        $array['contas'][] = $row['classificador'] . ' - ' . $row['cod_reduzido'] . ' - ' . utf8_encode($row['descricao']). ' - ' . utf8_encode($row['historico']).' - '.$row['id_conta'];
    }
    echo json_encode($array);
    exit();
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

if (isset($_REQUEST['save_multiplos'])) {
    $salvar = $classificacao->salvaLancamentoMultiplos($_REQUEST['lotem'], $_REQUEST['projetom'], $usuario['id_funcionario'], ConverteData($_REQUEST['data_lancamentm'], 'Y-m-d'));
    // salvar multiplos lançamentos contas credora e devedora
    foreach ($_REQUEST['tid_conta'] as $key => $value) {
        $item_lancamentos = array(
            'id_lancamento' => $salvar['id_lancamento'],
            'id_conta' => $_REQUEST['tid_conta'][$key],
            'tipo' => $_REQUEST['tconta_tipo'][$key],
            'valor' => str_replace(",", ".", str_replace(".", "", $_REQUEST['tvalor_m'][$key])),
            'documento' => $_REQUEST['tdocumento'][$key],
            'historico' => $_REQUEST['thistorico'][$key]
        );
        $itens_lancamento_simples = $classificacao->preparaArrayItens($item_lancamentos);
    }

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

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'consultar_lancamentos') {
    $proj = (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? $_REQUEST['projeto'] : '';
    echo $proj;
    $lista = $classificacao->lotesCriados($proj);
    foreach ($lista as $values) {
        if ($values['importacao'] == 0) {
            $disable = "";
            $btn = "btn-info";
        } else if ($values['importacao'] == 1) {
            $disable = "disabled";
            $btn = "btn-default";
        }
        ?>
        <tr id="<?= $values['nrlote'] ?>">
            <td><?= $values['lote'] ?> </td>
            <td><?= converteData($values['lote_data'], "d/m/Y") ?> </td>
            <td class="text text-uppercase"><?= utf8_decode($values['nome_projeto']) ?> </td>
            <td class="text text-right">
                <button data-nrprojeto="<?= $values['nrprojeto'] ?>" data-nrlote="<?= $values['nrlote'] ?>" data-nome_projeto="<?= $values['nome_projeto'] ?>" data-lote="<?= $values['lote'] ?>" class="btn btn-xs btn-info manual" type="button" style="width: 90px"><i class="fa fa-list-alt"></i> Manual</button>
                <button data-nrprojeto="<?= $values['nrprojeto'] ?>" data-nrlote="<?= $values['nrlote'] ?>" data-nome_projeto="<?= $values['nome_projeto'] ?>" data-lote="<?= $values['lote'] ?>" class="btn btn-xs <?= $btn ?> importacao" type="button" style="width: 90px" <?= $disable ?>><i class="fa fa-stack-overflow"></i> Importação</button>
                <button data-nrprojeto="<?= $values['nrprojeto'] ?>" data-nrlote="<?= $values['nrlote'] ?>" data-nome_projeto="<?= $values['nome_projeto'] ?>" data-lote="<?= $values['lote'] ?>" class="btn btn-xs btn-danger excluir" type="button"><i class="fa fa-trash-o"></i></button>
            </td>
        </tr>
        <?php
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'add_lote') {
//    print_array($_REQUEST);
    if($_REQUEST['projeto'] > 0 && $_REQUEST['mes'] > 0 && $_REQUEST['ano'] > 0 && !empty($_REQUEST['lote_numero']) ) {
        $objLote->setIdProjeto($_REQUEST['projeto']);
        $objLote->setMes($_REQUEST['mes']);
        $objLote->setAno($_REQUEST['ano']);
        $objLote->setUsuarioCriacao($_COOKIE['logado']);
        $objLote->setDataCriacao(date('Y-m-d H:i:s'));
        $objLote->setTipo($_REQUEST['tipo']);
        $objLote->setLoteNumero(utf8_decode($_REQUEST['lote_numero']));
        $objLote->setStatus(1);
        echo $objLote->verificaLote();
    } else {
        echo 0;
    }
    exit;
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'consultar_conciliacao') {
    print_array($_REQUEST);
    $dados = array(
        'id_projeto' => (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? $_REQUEST['projeto'] : '',
        'mes' => (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : '',
        'ano' => (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : '',
        'status' => '1'
    );
    
    $lancamento = $objLote->listaLotes($dados, $projetosRegiao);
    
    if(count($lancamento) > 0){ ?>
        <table class="table table-striped table-condensed table-hover valign-middle text text-sm">
            <thead>
                <tr>
                    <th>Lote</th>
                    <th>Exerc&iacute;cio</th>
                    <th>Data do Lote</th>
                    <!--<th>Data do Lan&ccedil;amento</th>-->
                    <th>Projeto</th>
                    <th class="text text-right">&emsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lancamento as $row) { ?>
                    <tr id="tr<?= $row['id_lote'] ?>">
                        <td class="lote"><?= $row['lote_numero'] ?></td>
                        <td class="lote"><?= $row['exercicio'] ?></td>
                        <td><?= ConverteData($row['criacao_lote'], 'd/m/Y') ?></td>
                        <!--<td><?= ConverteData($row['data_lancamento'], 'd/m/Y') ?></td>-->
                        <td class="projeto"><?= $row['nome_projeto'] ?></td>
                        <td class="text-right">
                            <button type="button" class="btn btn-info btn-xs btn_verificar" data-id="<?= $row['id_lote'] ?>"><i class="fa fa-edit"></i> Verificar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-warning">NENHUM LOTE ENCONTRADO PARA ESTE FILTRO!</div>
    <?php }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'form_conciliacao') {
    $objLote->setIdLote($_REQUEST['id']);
    $objLote->getById();
    $objLote->getRow();

    $objLancamento->setIdLote($_REQUEST['id']);
    $objLancamento->setStatus(1);
    $objLancamento->setContabil(1);
    $objLancamento->getLancamentos();
    $optTipo = array(1 => 'C', 2 => 'D');
    include_once 'form_conciliacao.php';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'excluir_lancamento') {
    $objLancamento->setIdLancamento($_REQUEST['id']);
    $objLancamento->setStatus('0');
    $objLancamentoItens->excluirByLancamento($_REQUEST['id']);
    $retorno = ($objLancamento->inativa()) ? array('status' => 'success', 'msg' => 'Excluido com Sucesso!') : array('status' => 'danger', 'msg' => 'Erro ao Excluir!');
    echo json_encode($retorno);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'excluir_lancamento_item') {
    $objLancamentoItens->setIdLancamentoItens($_REQUEST['id']);
    $retorno = ($objLancamentoItens->inativa()) ? array('status' => 'success', 'msg' => 'Excluido com Sucesso!') : array('status' => 'danger', 'msg' => 'Erro ao Excluir!');
    echo json_encode($retorno);
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'salvar_conciliacao') {
//    print_array($_REQUEST);exit;
    foreach ($_POST['id_lancamento'] as $key => $val) {
        $id = $val;
        // conulta dados do lote
        $objLote->setIdLote($_POST['id_lote']);
        $objLote->getById();
        $objLote->getRow();

        if (strpos($val, 't') !== FALSE) { // !== para verificar o tipo da variavel tambem pois o strpos está retornando int 0 que é interpetado como FALSE no condicional comum
            
            // preenche os campos da tabela lancamento
            $objLancamento->setIdProjeto($objLote->getIdProjeto());
            $objLancamento->setIdUsuario($usuario['id_funcionario']);
            $objLancamento->setIdLote($_POST['id_lote']);
            $objLancamento->setContabil(1);
            $objLancamento->setHistorico($_POST['historico'][$key]);
            $objLancamento->setStatus(1);
            $objLancamento->setDataLancamento(ConverteData($_POST['data_lancamento'][$key], 'Y-m-d'));

            $objLancamento->salvar(); // salva
            $id = $objLancamento->getIdLancamento(); // substitui o idss
//            print_array($_POST['valor'][$val]);
        }

        $status = TRUE;
        $val2 = str_replace('t','',$val);
        for ($i = 0; $i < count($_POST['valor'][$val2]); $i++) {
            $objLancamentoItens->setIdLancamentoItens(checkEmpty($_POST['id_lancamento_item'][$val2][$i]));
            $objLancamentoItens->setIdLancamento($id);
            $objLancamentoItens->setIdConta(checkEmpty($_POST['id_conta'][$val2][$i]));
            $objLancamentoItens->setValor(checkEmpty(strToNum($_POST['valor'][$val2][$i])));
            $objLancamentoItens->setTipo(checkEmpty($_POST['tipo'][$val2][$i]));
            $objLancamentoItens->setHistorico(checkEmpty(utf8_decode($_POST['historico_item'][$val2][$i])));
            $objLancamentoItens->setStatus(1);

            $status = $status && $objLancamentoItens->salvar();
            $objContas->verificaSaldo2($objLancamentoItens->getIdLancamentoItens());
        }
    }

    if ($status && isset($_POST['finalizar'])) {
//        print_array($objLote);exit;
        $objLote->setIdLote($_POST['id_lote']);
        $objLote->setStatus(2);
        $objLote->update();
        echo ($status) ? json_encode(array('status' => 'success', 'msg' => 'FINALIZA&Ccedil;&Atilde;O realizada.', 'id' => $_POST['id_lote'])) : json_encode(array('status' => 'danger', 'msg' => 'Erro ao salvar.'));
        exit();
    }

    echo ($status) ? json_encode(array('status' => 'success', 'msg' => 'Salvo com sucesso.')) : json_encode(array('status' => 'danger', 'msg' => 'Erro ao salvar.'));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'consultar_finalizados') {
    $dados = array(
        'id_projeto' => (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? $_REQUEST['projeto'] : '',
        'lote_numero' => (!empty($_REQUEST['lote'])) ? $_REQUEST['lote'] : '',
        'status' => '2'
    );

    $lancamento = $objLote->listaLotes($dados, $projetosRegiao);
    if(count($lancamento) > 0){ ?>
        <table class="table table-striped table-condensed table-hover valign-middle text text-sm">
            <thead>
                <tr>
                    <th>Lote</th>
                    <th>Exerc&iacute;cio</th>
                    <th>Data do Lote</th>
                    <!--<th>Data do Lan&ccedil;amento</th>-->
                    <th>Projeto</th>
                    <th class="text text-right">&emsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lancamento as $row) { ?>
                    <tr id="tr<?= $row['id_lote'] ?>">
                        <td class="lote"><?= $row['lote_numero'] ?></td>
                        <td class="lote"><?= $row['exercicio'] ?></td>
                        <td><?= ConverteData($row['criacao_lote'], 'd/m/Y') ?></td>
                        <!--<td><?= ConverteData($row['data_lancamento'], 'd/m/Y') ?></td>-->
                        <td class="projeto"><?= $row['nome_projeto'] ?></td>
                        <td class="text-right">
                            <button type="button" class="btn btn-info btn-xs btn_visualizar" data-id="<?= $row['id_lote'] ?>"><i class="fa fa-search"></i> Visualizar</button>
                            <?php if ($ACOES->verifica_permissoes(116)) { ?>
                                <button type="button" class="btn btn-warning btn-xs btn_reabrir" data-id="<?= $row['id_lote'] ?>"><i class="fa fa-external-link-square"></i> Reabrir</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-warning">NENHUM LOTE ENCONTRADO PARA ESTE FILTRO!</div>
    <?php }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'ver_finalizado') {
    $objLote->setIdLote($_REQUEST['id']);
    $objLote->getById();
    $objLote->getRow();

    $objLancamento->setIdLote($_REQUEST['id']);
    $objLancamento->setStatus(1);
    $objLancamento->getLancamentos();
    $optTipo = array(1 => 'C', 2 => 'D');
    include_once 'ver_finalizado.php';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'reabrir') {
    $objLote->setIdLote($_REQUEST['id']);
    $objLote->getById();
    $objLote->getRow();
    $objLote->setStatus(1);
    $status = $objLote->update();

    echo ($status) ? json_encode(array('status' => 'success', 'msg' => 'Lote Reaberto com successo.', 'id' => $_POST['id_lote'])) : json_encode(array('danger' => 'success', 'msg' => 'Erro ao reabrir.'));
    exit();
}

//------------------------------------------------------------------------------

function checkEmpty($var) {
    return (!empty($var)) ? $var : NULL;
}

function strToNum($string) {
    return str_replace(',', '.', str_replace('.', '', $string));
}
