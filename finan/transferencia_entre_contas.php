<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/BancoClass.php");
require("../classes/LogClass.php");
include("../classes/c_planodecontasClass.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];
$log = new Log();

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;
$banco = new Banco();
$objPlanoContas = new c_planodecontasClass();

function getAssocBanco($id_banco) {
    $query = "SELECT * FROM contabil_contas_assoc_banco a
            INNER JOIN contabil_planodecontas b ON a.id_conta = b.id_conta
            INNER JOIN bancos c ON a.id_banco = c.id_banco
            WHERE id_banco = $id_banco";
    $result = mysql_query($query);
    return mysql_fetch_assoc($result);
}

if ($_REQUEST['method'] == 'changeBancos') {

    // carrega bancos de
    $banco_fin_z = $banco->getBancoProj($id_regiao, $_REQUEST['id_projeto']);
    foreach ($banco_fin_z as $value) {
        $arr_z[$value['id_banco']] = utf8_encode($value['id_banco'] . ' - ' . $value['nome'] . " - " . $value['agencia'] . " / " . $value['conta']);
    }

    if ($_REQUEST['tipo'] == '245/113' || $_REQUEST['tipo'] == '113/245') {//  aplicação , resgate 
        $bbb = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_de']} LIMIT 1;"));
        $banco_fin = $banco->getBancoProj($bbb['id_regiao'], $bbb['id_projeto']);
        foreach ($banco_fin as $value) {
            $arr[$value['id_banco']] = utf8_encode($value['id_banco'] . ' - ' . $value['nome'] . " - " . $value['agencia'] . " / " . $value['conta']);
        }
//        $bbb = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_de']} LIMIT 1;"));
//        $query = "SELECT * FROM bancos WHERE id_projeto = '{$bbb['id_projeto']}'";
//        $result = mysql_query($query);
//        while ($row = mysql_fetch_assoc($result)) {
//            $arr[$row['id_conta']] = utf8_encode($row['descricao']);
//        }
    } else if ($_REQUEST['tipo'] == '274/142' || $_REQUEST['tipo'] == '142/274') {// '244' // Emprestimo
        $arr = $banco->selectBancoMaster($usuario['id_master']);
        foreach ($arr as $key => $value) {
            $arr[$key] = utf8_encode($value);
        }
    } else if ($_REQUEST['tipo'] == '244/129') {// '129' // Pagamento
        $arr = $banco->selectBancoMaster($usuario['id_master']);
        foreach ($arr as $key => $value) {
            $arr[$key] = utf8_encode($value);
        }
    } else if ($_REQUEST['tipo'] == '273/136') { // '273' // Transferência
        $arr = $banco->selectBancoMaster($usuario['id_master']);
        foreach ($arr as $key => $value) {
            $arr[$key] = utf8_encode($value);
        }
    } else if ($_REQUEST['tipo'] == '249/13') { // '13' // Repasse
        $arr = $banco->selectBancoProjeto($usuario['id_master']);
        foreach ($arr as $key => $value) {
            $arr[$key] = utf8_encode($value);
        }
        
//        $bbb = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_de']} LIMIT 1;"));
//        $banco_fin = $banco->getBancoProj($bbb['id_regiao'], $bbb['id_projeto']);
//        foreach ($banco_fin as $value) {
//            $arr[$value['id_banco']] = utf8_encode($value['id_banco'] . ' - ' . $value['nome'] . " AG " . $value['agencia'] . "C/C " . $value['conta']);
//        }
    }
    exit(json_encode(array('banco_de' => $arr_z, 'banco_para' => $arr)));
}

if ($_REQUEST['method'] == 'transferencia_entre_contas') {
    // separa os tipos de transferencia enviados via post
    $arr_t = explode('/', $_REQUEST['tipo_transf']);

    // consulta o banco_de SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_para']} LIMIT 1;"
    $bancoDe = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_de']} LIMIT 1"));
    $bancoPara = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_para']} LIMIT 1;"));

    $nomes = mysql_fetch_assoc(mysql_query("SELECT id_nome FROM entradaesaida_nomes A WHERE A.id_entradasaida = '{$arr_t[0]}' AND A.id_projeto = '{$bancoDe['id_projeto']}'"));
    // muda formato do valor
    $valor = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor']));

    // consulta sub grupo do tipo    
    $id_grupo = ($arr_t[0] == 245) ? 59:'';

    // monta array de saida
    
    if ($arr_t[0] == '273'){
        $despesa = '273';
        $sub_grupo = '59';
    } elseif ($arr_t[0] == '244') {
        $despesa = '244';
        $sub_grupo = '58';
    } elseif ($arr_t[0] == '245') {
        $despesa = '245';
        $sub_grupo = '78';
//    } elseif ($arr_t[1] == '245') {
//        $despesa = '245';
//        $sub_grupo = '59';
    } elseif ($arr_t[0] == '274') {
        $despesa = '274';
        $sub_grupo = '77';
    } else {
    }
//    exit(print_array($nomes['id_nome'])); 
    $arraySaida = array (
                
        'id_regiao' => addslashes($bancoDe['id_regiao']),
        'id_projeto' => addslashes($bancoDe['id_projeto']),
        'id_banco' => addslashes($bancoDe['id_banco']),
        'id_user' => addslashes($usuario['id_funcionario']),
        'id_nome' => addslashes($nomes['id_nome']),
        'entradaesaida_subgrupo_id' => addslashes($sub_grupo),
        'nome' => addslashes($_REQUEST['historicoS']),
        'especifica' => addslashes($_REQUEST['historicoS']),
        'tipo' => addslashes($despesa),
        'valor' => addslashes($valor),
        'data_proc' => addslashes(date('Y-m-d H:i:s')),
        'data_vencimento' => addslashes(implode('-', array_reverse(explode('/', $_REQUEST['data'])))),
        'status' => addslashes(1),
        'n_documento' => addslashes($_REQUEST['n_documento']),
        'mes_competencia' => addslashes(substr($_REQUEST['data'], 3, 2)),
        'ano_competencia' => addslashes(substr($_REQUEST['data'], 6, 4))
    );
    // salva saida
    $keySaida = implode(',', array_keys($arraySaida));
    $valueSaida = implode("' , '", $arraySaida);
    $insertSaida = "INSERT INTO saida ($keySaida) VALUES ('$valueSaida');";
    mysql_query($insertSaida);
    if (mysql_errno())
        $erro[mysql_errno()] = mysql_error();

    // recupera id da saida
    $id_saida = mysql_insert_id();


    //LANÇAMETO CONTABIL
    $arrayLancamentoSaida = array(
        'id_saida' => $id_saida,
        'id_projeto' => $bancoDe['id_projeto'],
        'id_usuario' => $usuario['id_funcionario'],
        'data_lancamento' => implode('-', array_reverse(explode('/', $_REQUEST['data']))),
        'historico' => addslashes($_REQUEST['historicoS'])
    );
    $id_lancamento = $objPlanoContas->inserirLancamento($arrayLancamentoSaida);

    $array_itens[] = array(
        'id_lancamento' => $id_lancamento,
        'valor' => $valor,
        'documento' => $_REQUEST['n_documento'],
        'tipo' => 2,
        'id_banco' => $bancoDe['id_banco'],
    );
    $array_itens[] = array(
        'id_lancamento' => $id_lancamento,
        'id_tipo' => $despesa,
        'valor' => $valor,
        'documento' => $_REQUEST['n_documento'],
        'tipo' => 1,
        'id_projeto' => $bancoDe['id_projeto'],
        'banco' => $bancoPara['id_banco']
    );
    $objPlanoContas->inserirItensMovimentacaoFinanceira($array_itens);

    unset($array_itens);

    $log->gravaLog('Pagar Saida', 'Pagameto saída ' . $id_saida[$i] . ' saldo banco De: ' . $valor_banco . ' Prara: ' . $saldo_banco_final);

    // consulta banco para
    $bancoDe = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_de']} LIMIT 1"));
    $bancoPara = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco_para']} LIMIT 1;"));

    // monta array da entrada 
    if ( $arr_t[1] == '136') {
        $receita = '136';
    } else if ( $arr_t[1] == '129') {
        $receita = '129';
    } else if ( $arr_t[1] == '142') { 
        $receita = '142';
    } else if ( $arr_t[1] == '113') { 
        $receita = '113';
    } else if ( $arr_t[0] == '113') { 
        $receita = '113';
    }
    
    $arrayEntrada = array(
        'id_regiao' => addslashes($bancoPara['id_regiao']),
        'id_projeto' => addslashes($bancoPara['id_projeto']),
        'id_banco' => addslashes($bancoPara['id_banco']),
        'id_user' => addslashes($usuario['id_funcionario']),
        'nome' => addslashes($_REQUEST['historicoE']),
        'especifica' => addslashes($_REQUEST['historicoE']),
        'tipo' => addslashes($receita),
        'valor' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['valor']))),
        'data_proc' => addslashes(date('Y-m-d H:i:s')),
        'data_vencimento' => addslashes(implode('-', array_reverse(explode('/', $_REQUEST['data'])))),
        'status' => addslashes(1)
    );

        // salva entrada
        $keyEntrada = implode(',', array_keys($arrayEntrada));
        $valueEntrada = implode("' , '", $arrayEntrada);
        $insertEntrada = "INSERT INTO entrada ($keyEntrada) VALUES ('$valueEntrada');";
        mysql_query($insertEntrada);
        if (mysql_errno())
            $erro[mysql_errno()] = mysql_error();

        // recupera id da entrada
        $id_entrada = mysql_insert_id();

        //LANÇAMETO CONTABIL
        $arrayLancamentoEntrada = array(
            'id_entrada' => $id_entrada,
            'id_projeto' => $bancoPara['id_projeto'],
            'id_usuario' => $usuario['id_funcionario'],
            'data_lancamento' => implode('-', array_reverse(explode('/', $_REQUEST['data']))),
            'historico' => addslashes($_REQUEST['historicoE'])        );
        $id_lancamento = $objPlanoContas->inserirLancamento($arrayLancamentoEntrada);

        $array_itens[] = array(
            'id_lancamento' => $id_lancamento,
            'id_tipo' => $receita,
            'valor' => $valor,
            'documento' => addslashes($_REQUEST['n_documento']),
            'tipo' => 1,
            'id_projeto' => $bancoPara['id_projeto'],
            'banco' => $bancoDe['id_banco']
        );
        $array_itens[] = array(
            'id_lancamento' => $id_lancamento,
            'id_banco' => $bancoPara['id_banco'],
            'valor' => $valor,
            'documento' => addslashes($_REQUEST['n_documento']),
            'tipo' => 2
        );
        $objPlanoContas->inserirItensMovimentacaoFinanceira($array_itens);

        $log->gravaLog('Pagar Entrada', 'Pagameto Entrada ' . $id_entrada[$i] . ' saldo banco De: ' . $valor_banco . ' Prara: ' . $saldo_banco_final);

    if (count($erro) > 0)
        $_SESSION['erro'] = $erro;
    else
        $_SESSION['sucesso'][] = "Transferencia criada com sucesso.";
    header("Location: transferencia_entre_contas.php");
    exit;
}

$nome_pagina = "Movimentação entre Contas / Projetos";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-financeiro-header"><h2><h2><?php echo $icon['4'] ?> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if (isset($_SESSION['sucesso'])) { ?>
                <div class="alert alert-success">
                    <ul>
                        <?php foreach ($_SESSION['sucesso'] as $key => $value) { ?>
                            <li><?= $value ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if (isset($_SESSION['erro'])) { ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($_SESSION['erro'] as $key => $value) { ?>
                            <li>Código do erro: <?= $key ?><!-- <?= $value ?> --></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php unset($_SESSION['erro'], $_SESSION['sucesso']) ?>
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Projeto</label>
                            <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Tipo de Movimentação</label>
                            <div class="col-xs-6">
                                <select class="form-control validate[required,custom[select]]" name="tipo_transf" id="tipo_transf">
                                    <option value="-1">Selecione</option>
                                    <option value="245/113">APLICAÇÃO</option>
                                    <option value="113/245">RESGATE DA APLICAÇÃO</option>
                                    <option value="274/142">EMPRÉSTIMO ENTRE PROJETOS</option>
                                    <option value="244/129">PAGAMENTO DE EMPRESTIMO</option>
                                    <option value="273/136">TRANSFERÊNCIA ENTRE CONTA</option>
                                    <option value="249/13">TRANSFERÊNCIA ENTRE PROJETOS REPASSE</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label"></label>
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <div class="input-group-addon">De</div>
                                    <?= montaSelect($banco->selectBancoMaster($usuario['id_master']), null, "id='banco_de' name='banco_de' class='form-control validate[required,custom[select]]'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label"></label>
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <div class="input-group-addon">Para</div>
                                    <?= montaSelect($banco->selectBancoMaster($usuario['id_master']), null, "id='banco_para' name='banco_para' class='form-control montaHistorico validate[required,custom[select]]' disabled") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Valor</label>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-money"></i></div>
                                    <input type="text" class="form-control money validate[required]" maxlength="16" name="valor" id="valor">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Data</label>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar-o"></i></div>
                                    <input type="text" class="form-control data validate[required]" name="data" id="data">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Documento</label>
                            <div class="col-xs-3">
                                <input type="text" class="form-control" name="n_documento" id="n_documento">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Histórico</label>
                            <div class="col-xs-8"> <?php echo $id_nome
                                    ?>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-list"></i></div>
                                    <textarea type="text" class="form-control validate[required]" name="historicoS" id="historicoS" rows="2" placeholder="saída"></textarea>
                                    <textarea type="text" class="form-control validate[required]" name="historicoE" id="historicoE" rows="2" placeholder="entrada"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="method" value="transferencia_entre_contas">
                        <button type="button" name="transferir" id="confirmar_transferencia" class="btn btn-primary"><i class="fa fa-gear"></i> Transferir</button>
                    </div>
                </div>
            </form>
            <!--<button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>-->
            <?php include("../template/footer.php"); ?>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>        
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script>

            function optionSelect(id_select, json, selected) {
                $('#' + id_select).html($('<option>', {value: -1}).text('-- Selecione --'));
                console.log($('#' + id_select).html());
                $.each(json, function (i, v) {
                    $('#' + id_select).append($('<option>', {value: i}).text(v));
                });
                $('#' + id_select).val(selected);
            }

            $(function () {

                $("#form1").validationEngine({promptPosition: "topRight"});

                $("html").on('change', '#projeto,#banco_de,#tipo_transf', function () {
                    var id_projeto = $('#projeto').val();
                    var id_banco_de = $('#banco_de').val();
                    var id_banco_para = $('#banco_para').val();
                    var tipo = $('#tipo_transf').val();
                    $.post('#', {method: 'changeBancos', tipo: tipo, banco_de: id_banco_de, id_projeto: id_projeto}, function (data) {
                        optionSelect('banco_de', data.banco_de, id_banco_de);
                        optionSelect('banco_para', data.banco_para, id_banco_para);
                        if (id_banco_de > 0) {
                            $('#banco_para option[value="' + id_banco_de + '"]').prop('disabled', true);
                            $("#banco_para").removeProp('disabled');
                        } else {
                            $("#banco_para").val(-1).prop('disabled', true);
                            $("#banco_para option").removeProp('disabled');
                        }
                    }, 'json');
                });

                $("html").on('click', '#confirmar_transferencia', function () {
                    bootConfirm('Confirmar Transferência?', 'Confirmação',
                            function (data) {
                                if (data == true) {
                                    $("#form1").submit();
                                }
                            },
                            'warning');
                });

                $(".money").focusin(function () {
                    $(".money").maskMoney({thousands: '.', decimal: ',', affixesStay: false});
                });

                $('.montaHistorico').change(function () {
                    var id_tipo = $('#tipo_transf').val();
                    var tipo = $('#tipo_transf option:selected').text();
                    var banco_de = $('#banco_de option:selected').text();
                    var banco_para = $('#banco_para option:selected').text();
                    var banco_para_val = $('#banco_para option:selected').val();
                    console.log(banco_para_val);
                    console.log(id_tipo);
                    if (id_tipo == '273/136') {
                        $("#historicoE").val(tipo + ' DE ' + banco_de) ;
                        $("#historicoS").val(tipo + ' PARA ' + banco_para) ;
                    } else if (id_tipo == '245/113') {
                        $("#historicoE").val(tipo) ;
                        $("#historicoS").val(tipo) ;
//                    } else if (id_tipo == '273/136' && banco_para_val == '105') { // RATEIO DE DESPESAS OPERACIONAIS - C/C 18.881-6 UPA MARECHAL HERMES
//                        $("#historicoE").val(tipo + ' DE ' + banco_de) ;
//                        $("#historicoS").val(tipo + ' PARA ' + banco_para) ;
                    } else if (id_tipo == '113/245') {
                        $("#historicoE").val(tipo) ;
                        $("#historicoS").val(tipo) ;
                    } else if (id_tipo == '274/142') {
                        $("#historicoE").val(tipo + ' DE ' + banco_de) ;
                        $("#historicoS").val(tipo + ' PARA ' + banco_para) ;
                    } else if (id_tipo == '244/129') {
                        $("#historicoE").val('RECEBIMENTO REFERENTE EMPRÉSTIMO FEITO À ' + banco_de);
                        $("#historicoS").val(tipo + ' FEITO À ' + banco_para);
                    } else if (id_tipo == '249/13') {
                        $("#historicoE").val('REPASSE REFERENTE DESPESAS ADMINISTRATIVAS DE ' + banco_de);
                        $("#historicoS").val(tipo + ' PARA ' + banco_para);
                    }
                });

            });
        </script>                                
    </body>
</html>
