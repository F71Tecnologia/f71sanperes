<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/c_planodecontasClass.php");
include_once("../../classes/ContabilHistoricoClass.php");
include_once("../../classes/global.php");

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

if (isset($_REQUEST['implantar_saldo'])) {
    $implantar_saldo = $planodecontas->retornoContaSaldo($_REQUEST['id_conta'], $_REQUEST['id_projeto']);
    $implantar_saldo = $implantar_saldo[0]; 
    ?>
    
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 label-control text text-sm">Classificação</label>
            <div class="col-lg-5">
                <input type="hidden" id="saldo_conta" name="implantar" value="<?= $implantar_saldo['id_conta'] ?>">
                <input readonly type="text" value="<?= $implantar_saldo['classificador'] ?>" id="saldo_classificador" name="saldo_classificador" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Código Acesso</label>
            <div class="col-lg-5">
                <input readonly type="text" value="<?= $implantar_saldo['cod_reduzido'] ?>" id="edita_reduzido" name="edita_reduzido" class="form-control input-sm text-center">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Descrição</label>
            <div class="col-lg-8">
                <input readonly type="text" value="<?= utf8_decode($implantar_saldo['descricao']) ?>" class="form-control input-sm" id="edita_descricao" name="edita_descricao">
            </div>
        </div>
    </div>
    <div class="form-group">        
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Projeto</label>
            <div class="col-lg-8"> 
                <input readonly type="text" value="<?= $implantar_saldo['nomeprojeto'] ?>" class="form-control input-sm">
                <input type="hidden" value="<?= $implantar_saldo['projeto'] ?>" class="form-control input-sm" id="saldo_projeto" name="saldo_projeto">
            </div>
        </div>
    </div>
    <hr><hr>
    <div class="form-group">
        <div class="row">
            <label class="col-lg-3 control-label text text-sm">Valor R$</label>
            <div class="col-lg-5">
                <input type="text" name="saldo_valor" id="saldo_valor" value="<?= $implantar_saldo['saldo'] ?>" maxlength="14" class="form-control text-right money"/>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $("input[name='edita_classificador']").mask('?9.99.99.99.99.99.99');
            $("#saldo_data").mask('99/99/9999');
            $('.data').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '2005:c+1'
            });
            
            $(".money").focusin(function(){
                $(".money").maskMoney({thousands: '.', decimal: ',', affixesStay: false, allowNegative: true });
            });
           
        });
    </script>

<?php exit(); }

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'implantarplanodecontas') {
    $arraySaldoContas = $planodecontas->contasSaldo($_REQUEST['id_projeto']);
    if (count($arraySaldoContas) > 0) { ?>
        <div class="col-sm-5"><label><?= $arraySaldoContas[2]['nome'] ?></label></div>
        <div class="text-right"><label> <?= $arraySaldoContas[2]['dataAtual'] ?></label></div>
        <hr>
        <form id="form-lista-planos">
            <table class="table table-condensed table-bordered table-condensed table-striped text-sm valign-middle">
                <thead class="text-sm">
                    <tr>
                        <th>CLASSIFICADOR</th>
                        <th class="text-center">ACESSO</th>
                        <th>DESCRIÇÃO</th>
                        <th colspan="2" class="text-right">SALDO R$</th>
                    </tr>
                </thead>
               <?php foreach ($arraySaldoContas as $value) {
                    $cor = $value['saldo'] < 0 ? "text-danger" : "text-info" ; ?>
                    <tr>
                        <td width="16%"><?= $value['classificador'] ?></td>
                        <td width="10%" class="text-center"><?= $value['id_conta'] ?></td>
                        <td width=""><?= utf8_decode($value['descricao']) ?></td>
                        <td class="text-right <?= $text.' '. $cor ?>"><?= ($value['saldo'] < 0) ? "(" . number_format($value['saldo'] * -1, 2, ',', '.') . ")" : number_format($value['saldo'], 2, ',', '.') ?></td>
                        <td class="text-center"><button type="button" class="btn btn-default btn-xs" id="implantar_saldo" name="implantar_saldo" value="<?= $value['id_conta'] ?>" data-id="<?= $value['id_conta'] ?>" data-projeto="<?= ($_REQUEST['id_projeto']) ?>" title="Implantar" data-toggle="tooltip"><i class="fa fa-usd"></i></button></td>
                    </tr>
                <?php } ?>
            </table>
            <input type="hidden" name="method" value="classificadores_saldo_implantar">
            <input type="hidden" name="id_projeto" value="<?= $_REQUEST['id_projeto'] ?>">
        </form>
    <?php } else { ?>
        <div class="alert alert-warning">Nenhuma conta encontrada neste projeto!</div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar_ajuste_saldo') {
    $return = $planodecontas->novoSaldo($_REQUEST['saldoprojeto'], $_REQUEST['saldoconta'], $_REQUEST['saldovalor']);
    
    echo $return;
//    print_array($retorno);
    exit();

    
}

