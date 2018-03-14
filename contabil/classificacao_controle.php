<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../conn.php");
include_once("../wfunction.php");

include_once("../classes/c_planodecontasClass.php");
include_once("../classes/c_classificacaoClass.php");
include_once("../classes/ContabilLancamentoClass.php");
include_once("../classes/ContabilLancamentoItemClass.php");
include_once("../classes/ContabilLoteClass.php");
include_once("../classes_permissoes/acoes.class.php");
require_once("../classes/ContabilContasSaldoClass.php");

include_once("../classes/global.php");

$ACOES = new Acoes();

$usuario = carregaUsuario();
$classificacao = new c_classificacaoClass();
$objLancamento = new ContabilLancamentoClass();
$objLancamentoItens = new ContabilLancamentoItemClass();
$objLote = new ContabilLoteClass();

///  Lnovo teste para saber se resolveu o erro_500
///  Lnovo teste para saber se resolveu o erro_500
///  Lnovo teste para saber se resolveu o erro_500
///  Lnovo teste para saber se resolveu o erro_500
///  Lnovo teste para saber se resolveu o erro_500

$projetosRegiao = implode(',', array_keys(getProjetos($usuario['id_regiao'])));

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'retornaConta') {
    $qry = "SELECT id_conta, acesso, descricao, classificador, nivel,
            IF(A.id_historico > 0,(SELECT texto FROM contabil_historico_padrao AS B WHERE B.id_historico = A.id_historico),'') AS historico 
            FROM contabil_planodecontas AS A 
            WHERE A.id_projeto = '{$_REQUEST['projeto']}' AND A.classificacao = 'A' AND A.status = 1 AND (A.acesso LIKE '%{$_REQUEST['codigo']}%' OR A.classificador LIKE '%{$_REQUEST['codigo']}%' OR A.descricao LIKE '%{$_REQUEST['codigo']}%')
            ORDER BY A.classificador, A.nivel ";
    $result = mysql_query($qry) or die(mysql_error());
    while ($row = mysql_fetch_array($result)) {
        $array['contas'][] = $row['classificador'] . ' | ' . $row['acesso'] . ' | ' . utf8_encode($row['descricao']) . ' | ' . $row['id_conta'] . ' | ' . utf8_encode($row['historico']);
    }
    echo json_encode($array);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancelarlote') {
    $status = $classificacao->cancelarLote($_REQUEST['projeto'], $_REQUEST['nrlote'], $usuario['id_funcionario']);
    echo json_encode(array('status' => $status));
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

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'add_lancamento') {
//    print_array($_REQUEST);
    if ($_REQUEST['projeto'] > 0 && $_REQUEST['mes'] > 0 && $_REQUEST['ano'] > 0 && !empty($_REQUEST['lote_numero'])) {
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
    $dados = array(
        'id_projeto' => (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? $_REQUEST['projeto'] : '',
        'mes' => (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : '',
        'ano' => (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : '',
        'status' => '1'
    );

    //$lancamento = $objLote->listaLotes($dados, $projetosRegiao);
    $lancamento = $objLancamento->listaLancamentos($dados);
    if (count($lancamento) > 0) {
        ?>
        <div class="container-fluid">

            <?php foreach ($lancamento as $k_data => $lan) { ?>
                <div class="row">
                    <div class="col-sm-12 col-xs well">
                        <h4><a href="#<?= $k_data ?>" class="inf"><?= ConverteData($k_data, 'd/m/Y') ?></a></h4>
                    </div>
                </div>
                <div class="row">
                    <div id="<?= $k_data ?>" class="col-xs-12 well oculto">
                        <?php foreach ($lan as $k_lan => $tipo) { ?>
                            <table class="table table-condensed valign-middle text text-sm">
                                <tbody>
                                    <tr id="tr<?= $k_lan ?>" class="active text-default">
                                        <td>Lançamento</td>
                                        <td  colspan="3"><?= $k_lan . " - " . $tipo['hist_lancamento'] ?></td>
                                        <td class="text-right">
                                            <button type="button"  class="btn btn-xs btn-info editar_lancamento_item" data-id="<?= $k_lan ?>" title="Editar"><i class="fa fa-pencil"></i></button>
                                            <button type="button"  class="btn btn-xs btn-danger exclui_lancamento" data-id="<?= $k_lan ?>" title="Excluir"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 10%;">&emsp;</td>
                                        <td style="width: 10%;">Acesso</td>
                                        <td style="width: 50%;">Descrição</td>
                                        <td style="width: 10%;">Classificação</td>
                                        <td style="width: 10%;" class="text-right">Valor R$</td>

                                    </tr>
                                    <?php foreach ($tipo['lan'] as $t => $row) { ?>
                                        <?php $class = ($row['tipo'] == 1) ? 'text-danger' : 'text-success' ?>
                                        <tr id="tr<?= $row['id_lancamento'] ?>" class="<?= $class ?>">
                                            <td class="text text-left"><?= $row['classificador'] ?>&emsp;</td>
                                            <td ><?= $row['acesso'] ?></td>
                                            <td ><?= $row['descricao'] ?></td>
                                            <td ><?= $row['tipo'] == 2 ? 'Devedora' : 'Credora' ?></td>
                                            <td class="text-right"><?= number_format($row['valor'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    <?php } else { ?>
        <div class="alert alert-warning">NÃO HÁ LANCAMENTOS PARA ESTE FILTRO!</div>
        <?php
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'form_conciliacao') {
    $objLancamento->getIdLancamento($_REQUEST['id']);
    $objLancamento->setStatus(1);
    $optTipo = array(1 => 'C', 2 => 'D');
    include_once 'form_conciliacao.php';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'exclui_lancamento') {
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
//            print_array($_REQUEST);
//            exit(); 
    // preenche os campos da tabela lancamento
    $objLancamento->setIdProjeto($_REQUEST['id_projeto']);
    $objLancamento->setIdUsuario($usuario['id_funcionario']);
    $objLancamento->setIdLote(0);
    $objLancamento->setContabil(1);
    $objLancamento->setHistorico(utf8_decode($_REQUEST['historico_lancamento']));
    $objLancamento->setStatus(1);
    $objLancamento->setDataLancamento(ConverteData($_REQUEST['data_lancamento'], 'Y-m-d'));

    if(isset($_REQUEST['id_lancamento'])){
        echo 'aqui 2';
        $objLancamento->setIdLancamento($_REQUEST['id_lancamento']);
    }
    
    $status = $objLancamento->salvar(); // salva
    $id = $objLancamento->getIdLancamento(); // substitui o idss




    for ($i = 0; $i < count($_REQUEST['valor']); $i++) {
        // preenchendo os campos dos itens do lancamento
        $objLancamentoItens->setIdLancamentoItens(checkEmpty($_REQUEST['id_lancamento_item'][$i]));
        $objLancamentoItens->setIdLancamento($id);
        $objLancamentoItens->setIdConta(checkEmpty($_REQUEST['id_conta'][$i]));
        $objLancamentoItens->setValor(checkEmpty(strToNum($_REQUEST['valor'][$i])));
        $objLancamentoItens->setTipo(checkEmpty($_REQUEST['tipo'][$i]));
        $objLancamentoItens->setHistorico(checkEmpty(utf8_decode($_REQUEST['historico_item'][$i])));
        $objLancamentoItens->setStatus(1);

        if(isset($_REQUEST['id_lancamento_itens'][$i])){
            echo 'aqui 1';
            $objLancamentoItens->setIdLancamentoItens($_REQUEST['id_lancamento_itens'][$i]);
        }

        $status = $status && $objLancamentoItens->salvar(); // salvando
        $objContas = new ContabilContasSaldoClass();
        $objContas->verificaSaldo2($objLancamentoItens->getIdLancamentoItens()); // verificando saldo
    }
//    }

    echo ($status) ? json_encode(array('status' => 'success', 'msg' => 'Salvo com sucesso.')) : json_encode(array('status' => 'danger', 'msg' => 'Erro ao salvar.'));
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'consultar_finalizados') {
    $dados = array(
        'id_projeto' => (!empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? $_REQUEST['projeto'] : '',
        'status' => '2'
    );

    $lancamento = $objLote->listaLotes($dados, $projetosRegiao);
    if (count($lancamento) > 0) {
        ?>
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
        <?php
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'ver_finalizado') {
    $objLote->setIdLote($_REQUEST['id']);
    $objLote->getById();
    $objLote->getRow();

    $objLancamento->setIdLote($_REQUEST['id']);
    $objLancamento->setStatus(1);
    $objLancamento->getIdLancamento();
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

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'consolta_lancamento'){
    $query1 = "SELECT * FROM contabil_lancamento WHERE id_lancamento = {$_REQUEST['id']};";
    $query2 = "SELECT a.*,b.descricao,b.classificador,b.acesso FROM contabil_lancamento_itens a
                LEFT JOIN contabil_planodecontas b ON a.id_conta = b.id_conta 
                WHERE id_lancamento = {$_REQUEST['id']};";
    $arr = [];
    $result1 = mysql_query($query1);
    while ($row1 = mysql_fetch_assoc($result1)){
        $row1['data_lancamento'] = ConverteData($row1['data_lancamento'],'d/m/Y');
        $row1['historico'] = utf8_encode($row1['historico']);
        $arr = $row1;
    }
    $result2 = mysql_query($query2);
    while ($row2 = mysql_fetch_assoc($result2)){
        $row2['historico'] = utf8_encode($row2['historico']);
        $row2['descricao'] = utf8_encode($row2['descricao']);
        $row2['valor'] = number_format($row2['valor'],2,'.',',');
        $arr['itens'][] = $row2;
    }
    echo json_encode($arr);
}

//------------------------------------------------------------------------------

function checkEmpty($var) {
    return (!empty($var)) ? $var : NULL;
}

function strToNum($string) {
    return str_replace(',', '.', str_replace('.', '', $string));
}
