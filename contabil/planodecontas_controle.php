<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../conn.php");
include_once("../wfunction.php");
include_once("../classes/c_planodecontasClass.php");
include_once("../classes/ContabilHistoricoClass.php");
include_once("../classes/global.php");

$usuario = carregaUsuario();
$planodecontas = new c_planodecontasClass();

// consulta conta para ver se há cadastrado da conta
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificador') {
    $classificador = str_replace(array('.', '_'), '', $_REQUEST['classificador']);
    $plc_classificador = $planodecontas->retorna($classificador);

    echo json_encode($plc_classificador);
    exit();
}

// alteração das contas EMPRESAS
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'alterar_contas') {
    $planodeconta_idconta = $_REQUEST['edita_id_conta'];
    $classificador = implode('.', array_filter(explode('.', $_REQUEST['edita_classificador'])));
    $historico = ($_REQUEST['id_historico'] > 0) ? $_REQUEST['id_historico'] : 0;
    $alterarcao_conta = $planodecontas->alteracao($_REQUEST['edita_id_conta'], $classificador, $_REQUEST['edita_reduzido'], addslashes($_REQUEST['edita_descricao']), $_REQUEST['edita_natureza'], $_REQUEST['edita_tipo'], $historico, str_replace(',', '.', str_replace('.', '', $_REQUEST['edita_saldo'])));

    echo $alterarcao_conta;
    exit();
}

if (isset($_REQUEST['novaconta']) && $_REQUEST['novaconta'] == 'Salvar') {
    $conta_pai = implode('.', array_filter(explode('.', $_REQUEST['conta_pai'])));
    $busca_contapai = $planodecontas->retorna_conta_pai($conta_pai,$_REQUEST['projeto']);
    $conta_pai = $busca_contapai[0]['id_conta'];
    $classificador = implode('.', array_filter(explode('.', $_REQUEST['classificador'])));
    $saldo_inicial = str_replace(',', '.', str_replace('.', '', $_REQUEST['saldo_inicial']));
    $historico = ($_REQUEST['id_historico'] > 0) ? $_REQUEST['id_historico'] : 0;
    $pl_ctas_novaconta = $planodecontas->novaconta($_REQUEST['codigo'], $conta_pai, $classificador, $_REQUEST['tipo'], addslashes($_REQUEST['descricao']), $_REQUEST['natureza'], $_REQUEST['projeto'], $saldo_inicial, $historico);
    
    echo $pl_ctas_novaconta;
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancelar_conta') {
    $cancelar_conta = $planodecontas->cancelar($_REQUEST['conta'], $_REQUEST['motivo']);

    echo $cancelar_conta;
    exit();
}

if (isset($_REQUEST['empresa_planoconta'])) {
    $filtrar_planocontas = $planodecontas->getPlanoFull($_REQUEST['projeto']); ?>

    <p class="text-right"><button type="button" class="btn btn-success hidden-print" onclick="tableToExcel('tbRelatorio', 'Plano de Contas')"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
    <table id="tbRelatorio" class="table table-condensed table-striped text text-sm">
        <thead>
            <tr>
                <th>Id</th>
                <th>Classificador</th>
                <th>Id Pai</th>
                <th>Código</th>
                <th>Descrição</th>
                <th class="text text-center">Tipo</th>
                <th class="text text-center">Natureza</th>
                <th class="text text-center">Nível</th>
                <th colspan="2"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            $k = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
            foreach ($filtrar_planocontas as $value) {
                if ($value['tipo'] === 'S') {
                    $tipo = 'SINTÉTICA';
                } elseif ($value['tipo'] === 'A') {
                    $tipo = 'ANALÍTICA';
                } else {
                    $tipo = '';
                }
                if ($value['natureza'] === 'C') {
                    $natureza = 'CREDORA';
                } elseif ($value['natureza'] === 'D') {
                    $natureza = 'DEVEDORA';
                } else {
                    $natureza = '';
                }
                ?>
                <tr id="tr-<?= $value['id_conta'] ?>" >
                    <td><?= $value['id_conta'] ?></td>
                    <td><?= $value['classificador'] ?></td>
                    <td class="text-center"><?= $value['conta_superior'] ?></td>
                    <td><?= $value['cod_reduzido'] ?></td>
                    <td><?= $value['descricao'] ?></td>
                    <td class="text text-center"><?php echo $tipo ?></td>
                    <td class="text text-center"><?php echo $natureza ?></td>
                    <td class="text text-center"><?= $value['nivel'] ?></td>                
                    <td class="text text-right hidden-print">
                        <?php if ($value['sped'] == 0) { ?>
                            <button type="button" class="btn btn-warning btn-xs" id="edita_conta" name="edita_conta" value="<?= $value['id_conta'] ?>" data-id="<?= $value['id_conta'] ?>" data-projeto="<?= $value['id_projeto'] ?>" title="Editar" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                            <button type="button" class="btn btn-danger btn-xs" id="cancela_conta" name="cancela_conta" data-cancelar_id="<?= $value['id_conta'] ?>" data-descricao="<?= $value['descricao'] ?>" data-classificador="<?= $value['classificador'] ?>" title="Excluir" data-toggle="tooltip">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button> 
                        <?php } ?>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php
    exit(); 
}

if (isset($_REQUEST['editar_conta'])) {
    $editar_contas = $planodecontas->retorno($_REQUEST['id_conta'], $_REQUEST['id_projeto']);
    $editar_contas = $editar_contas[0];

    $objHistorico = new ContabilHistoricoPadraoClass();

    $objHistorico->listarHistoricos();
    $optHistorico[-1] = 'Selecione';
    while ($objHistorico->getRow()) {
        $optHistorico[$objHistorico->getIdHistorico()] = utf8_encode($objHistorico->getTexto());
    }
    ?>

    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 label-control text text-sm">Classificação</label>
            <div class="col-lg-5">
                <input type="hidden" id="edita_id_conta" name="edita_id_conta" value="<?= $editar_contas['id_conta'] ?>">
                <input type="text" value="<?= $editar_contas['classificador'] ?>" id="edita_classificador" name="edita_classificador" class="form-control input-sm">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Código Reduzido</label>
            <div class="col-lg-5">
                <input type="text" value="<?= $editar_contas['cod_reduzido'] ?>" id="edita_reduzido" name="edita_reduzido" class="form-control input-sm">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Descrição</label>
            <div class="col-lg-8">
                <input type="text" value="<?= utf8_decode($editar_contas['descricao']) ?>" class="form-control input-sm" id="edita_descricao" name="edita_descricao">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Natureza</label>
            <div class="col-lg-4">
                <select class="form-control input-sm" id="edita_natureza" name="edita_natureza">
                    <option value="C" <?php if ($editar_contas['natureza'] == "C") { echo "selected"; } ?> > CREDORA </option>
                    <option value="D" <?php if ($editar_contas['natureza'] == "D") { echo "selected"; } ?> > DEVEDORA </option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Tipo</label>
            <div class="col-lg-4">
                <select class="form-control input-sm" id="edita_tipo" name="edita_tipo">
                    <option value="A" <?php if ($editar_contas['tipo'] == "A") { echo "selected"; } ?> > ANALÍTICA </option>
                    <option value="S" <?php if ($editar_contas['tipo'] == "S") { echo "selected"; } ?> > SINTÉTICA </option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="" class="col-lg-3 control-label text text-sm">Histórico Padrão</label>
            <div class="col-lg-9">
                <?= montaSelect($optHistorico, $editar_contas['id_historico'], 'name="id_historico2" id="id_historico2" class="form-control input-sm"') ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Saldo R$</label>
            <div class="col-lg-4"> 
                <?php if($editar_contas['saldo'] < 0 ) { $text = "text-danger"; } ?>
                <input readonly type="text" value="<?= number_format($editar_contas['saldo'],2,',','.') ?>" class="form-control text-right <?= $text ?> input-sm" id="edita_saldo" name="edita_saldo">
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () { 
            $("input[name='edita_classificador']").mask('?9.99.99.99.99.99.99');
        })
    </script>

<?php exit(); }

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'implantarplanodecontas') {
    $arrayContas = $planodecontas->ImplantarContasSPED($_REQUEST['id_projeto']);
    if (count($arrayContas) > 0) { ?>
        <legend>Selecione as contas que serão implantadas</legend>
        <form id="form-lista-planos">
            <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                <tr>
                    <td class="text-center" width="5%"><input type="checkbox" id="checkAll" data-name="id_conta"></td>
                    <td colspan="2">Selecionar todos</td>
                </tr>
                <?php foreach ($arrayContas as $value)  { ?>              
                    <tr>
                        <td class="text-center" width="5%"><input type="checkbox" name="id_conta[]" value="<?= $value['id_conta'] ?>" <?= ($value['tipo'] == 'A') ? "":"disabled" ?>></td>
                        <td width="20%"><?= $value['classificador'] ?></td>
                        <td width=""><?= utf8_decode($value['descricao']) ?></td>
                    </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="method" value="implatacao_planodecontas_editavel">
            <input type="hidden" name="id_projeto" value="<?= $_REQUEST['id_projeto'] ?>">
        </form>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhuma conta encontrada neste projeto!</div>
    <?php }
    
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'implatacao_planodecontas_editavel') {
    if (isset($_REQUEST['id_conta'])) {
        $arrayProjetos = $planodecontas->Projetos();
        unset($arrayProjetos[$_REQUEST['id_projeto']]);
        $arrayContas = $planodecontas->ImplantarContasSPED($_REQUEST['id_projeto'], $_REQUEST['id_conta']);
        if (count($arrayContas) > 0) { ?>
            <legend>Selecione o projeto, altere e salve as contas</legend>
            <form id="frm-implantacao-plano-editavel" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-1 text-sm control-label">Projeto</label>
                    <div class="col-sm-5"><?= montaSelect($arrayProjetos, null, "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'"); ?></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="col-md3">Classificador</th>
                                    <th class="col-md-2">Acesso</th>
                                    <th class="col-md-6">Descrição</th>
                                    <th>Natureza</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arrayContas as $value) { ?>
                                    <tr>
                                        <td><input readonly class="form-control input-sm classificador" type="text" name="id_conta[<?= $value['id_conta'] ?>][classificador]" value="<?= $value['classificador'] ?>"></td>
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][cod_reduzido]" value="<?= $value['cod_reduzido'] ?>"></td>
                                            <input type="hidden" name="id_conta[<?= $value['id_conta'] ?>][conta_superior]" value="<?= $value['conta_superior'] ?>">
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][descricao]" value="<?= utf8_decode($value['descricao']) ?>"></td>
                                        <td>
                                            <select class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][natureza]">
                                                <option value="D" <?= ($value['natureza'] == 'D') ? 'SELECTED' : null ?>>D</option>
                                                <option value="C" <?= ($value['natureza'] == 'C') ? 'SELECTED' : null ?>>C</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][tipo]">
                                                <option value="A" <?= ($value['tipo'] == 'A') ? 'SELECTED' : null ?>>A</option>
                                                <option value="S" <?= ($value['tipo'] == 'S') ? 'SELECTED' : null ?>>S</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="method" value="implatacao_planodecontas_salvar">
                    </div>
                </div>
            </form>
            <script>
                $(document).ready(function () {
                    $(".classificador").mask('?9.99.99.99.99.99.99');
                })
            </script>
        <?php } else { ?>
            <div class="alert alert-warning">Nenhuma conta selecionada!</div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-warning">Nenhuma conta selecionada!</div>
        <?php
    }
    exit();
}
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'implatacao_planodecontas_salvar') {
    foreach ($_REQUEST['id_conta'] as $key => $value) {
        
        $return = $planodecontas->InplantarNovaConta($value['cod_reduzido'], $value['conta_superior'], $value['classificador'], $value['tipo'], addslashes($value['descricao']), $value['natureza'], $_REQUEST['id_projeto']);
       // print_array($return);
        $retorno = json_decode($return, true);
        if (!$retorno['status']) {
            $erro[] = $value['classificador'];
        }
    }
    if (count($erro) > 0) {
        echo implode(', ', $erro);
    } else {
        echo 'sucesso';
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificadores_projeto') {
    $arrayContas = $planodecontas->contasProjeto($_REQUEST['id_projeto']);
    if (count($arrayContas) > 0) { ?>
        <legend>Selecione as contas que serão importadas</legend>
        <form id="form-lista-planos">
            <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                <tr>
                    <td class="text-center" width="5%"><input type="checkbox" id="checkAll" data-name="id_conta"></td>
                    <td colspan="2">Selecionar todos</td>
                </tr>
                <?php foreach ($arrayContas as $value) { ?>
                    <tr>
                        <td class="text-center" width="5%"><input type="checkbox" name="id_conta[]" value="<?= $value['id_conta'] ?>"></td>
                        <td width="20%"><?= $value['classificador'] ?></td>
                        <td width=""><?= utf8_decode($value['descricao']) ?></td>
                    </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="method" value="classificadores_projeto_editavel">
            <input type="hidden" name="id_projeto" value="<?= $_REQUEST['id_projeto'] ?>">
        </form>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhuma conta encontrada neste projeto!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'classificadores_projeto_editavel') {
    if (isset($_REQUEST['id_conta'])) {
        $arrayProjetos = $planodecontas->Projetos();
        unset($arrayProjetos[$_REQUEST['id_projeto']]);
        $arrayContas = $planodecontas->contasProjeto($_REQUEST['id_projeto'], $_REQUEST['id_conta']);
        if (count($arrayContas) > 0) {
            ?>
            <legend>Selecione o projeto, edite as contas e salve</legend>
            <form id="form-lista-planos-editavel" class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-1 text-sm control-label">Projeto</label>
                    <div class="col-sm-5"><?= montaSelect($arrayProjetos, null, "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'"); ?></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="col-md3">Classificador</th>
                                    <th class="col-md-2">Acesso</th>
                                    <th class="col-md-6">Descrição</th>
                                    <th>Natureza</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arrayContas as $value) { ?>
                                    <tr>
                                        <td><input readonly class="form-control input-sm classificador" type="text" name="id_conta[<?= $value['id_conta'] ?>][classificador]" value="<?= $value['classificador'] ?>"></td>
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][cod_reduzido]" value="<?= $value['cod_reduzido'] ?>"></td>
                                            <input type="hidden" name="id_conta[<?= $value['id_conta'] ?>][conta_superior]" value="<?= $value['conta_superior'] ?>">
                                        <td><input class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][descricao]" value="<?= utf8_decode($value['descricao']) ?>"></td>
                                        <td>
                                            <select class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][natureza]">
                                                <option value="D" <?= ($value['natureza'] == 'D') ? 'SELECTED' : null ?>>D</option>
                                                <option value="C" <?= ($value['natureza'] == 'C') ? 'SELECTED' : null ?>>C</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control input-sm" type="text" name="id_conta[<?= $value['id_conta'] ?>][tipo]">
                                                <option value="A" <?= ($value['tipo'] == 'A') ? 'SELECTED' : null ?>>A</option>
                                                <option value="S" <?= ($value['tipo'] == 'S') ? 'SELECTED' : null ?>>S</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="method" value="salvar_plano_contas">
                    </div>
                </div>
            </form>
            <script>
                $(document).ready(function () {
                    $(".classificador").mask('?9.99.99.99.99.99.99');
                })
            </script>
        <?php } else { ?>
            <div class="alert alert-warning">Nenhuma conta selecionada!</div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-warning">Nenhuma conta selecionada!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar_plano_contas') {
    foreach ($_REQUEST['id_conta'] as $key => $value) {
        
        $return = $planodecontas->novaconta($value['cod_reduzido'], $value['conta_superior'], $value['classificador'], $value['tipo'], addslashes($value['descricao']), $value['natureza'], $_REQUEST['id_projeto']);
       // print_array($return);
        $retorno = json_decode($return, true);
        if (!$retorno['status']) {
            $erro[] = $value['classificador'];
        }
    }
    if (count($erro) > 0) {
        echo implode(', ', $erro);
    } else {
        echo 'sucesso';
    }
    exit();
}
