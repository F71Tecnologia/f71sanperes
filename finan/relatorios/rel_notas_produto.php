<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}
error_reporting(E_ALL);
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFeClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objNFe = new NFe();
$global = new GlobalClass();

$id_projeto = $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];
$id_prestador = $_REQUEST['prestador'];


if (isset($_REQUEST['filtrar'])) {
    $filtro = true;
    if ($id_projeto !== "-1") {
        $x_prod = $id_projeto;
    }

    $arrayNFe = $objNFe->consultaNFeNaoPagas($x_prod);

    $arr_where[] = ($id_projeto > 0) ? "id_projeto = $id_projeto" : '';
    $arr_where[] = ($id_regiao > 0) ? "id_regiao = $id_regiao" : '';

    $where = implode(' AND ', array_filter($arr_where));

    $sqlB = "SELECT * FROM bancos WHERE $where";
    $qryB = mysql_query($sqlB);
    while ($rowB = mysql_fetch_assoc($qryB)) {
        $arrayBancos[$rowB['id_banco']] = $rowB['id_banco'] . ' - ' . $rowB['nome'];
        $arrIdBanco[$rowB['id_projeto']] = $rowB['id_banco'];
    }
}

// preencher select dos prestadores
$query = "SELECT c_razao AS razao, REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '') AS cnpj #,encerrado_em
            FROM prestadorservico 
            -- WHERE encerrado_em > CURDATE()
            GROUP BY REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '')
            ORDER BY c_razao";
$result = mysql_query($query);
$prestadores['-1'] = "« TODOS »";
while ($row = mysql_fetch_array($result)) {
    $prestadores[$row['cnpj']] = mascara_string('##.###.###/####-##', $row['cnpj']) . " - {$row['razao']}";
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['gerar_saida']) && $_REQUEST['gerar_saida'] == 'gerar_saida') {

    $data_vencimento = converteData($_REQUEST['data_vencimento']);

    foreach ($_REQUEST['nfe'] as $key_id => $id_nfe) {

        $arrayDados = $objNFe->consultaNFeNaoPagas($id_projeto, $id_nfe);
        $arrayDados = $arrayDados[$id_nfe];

        print_array($arrayDados);
        print_array($usuario);

        $query_z = "SELECT nome FROM entradaesaida WHERE id_entradasaida = {$arrayDados['id_tipo_entradasaida']}";
        $tipo_row = mysql_fetch_assoc(mysql_query($query_z));

        // montando array da saida
        $array = array(
            'id_regiao' => addslashes($arrayDados['id_regiao']),
            'id_projeto' => addslashes($arrayDados['id_projeto']),
            'id_banco' => addslashes($_REQUEST['banco'][$key_id]),
            'id_user' => addslashes($usuario['id_funcionario']),
            'nome' => addslashes($arrayDados['emit_xNome'] . ' - Projeto: ' . $arrayDados['dest_xNome']),
            'especifica' => addslashes('COMPRA DE ' . $tipo_row['nome'] . ' - COMP ' . converteData($arrayDados['dEmi'], 'm') . '/' . converteData($arrayDados['dEmi'], 'Y') . ' - NF ' . $arrayDados['nNF']),
            'tipo' => addslashes($arrayDados['id_tipo_entradasaida']),
            'valor' => addslashes(number_format($arrayDados['vNF'], 2, '.', '')),
            'valor_bruto' => addslashes(number_format($arrayDados['vNF'], 2, '.', '')),
            'data_proc' => addslashes(date('Y-m-d')),
            'data_vencimento' => addslashes($data_vencimento),
            'comprovante' => addslashes(2),
            'status' => addslashes(1),
            'id_prestador' => addslashes($arrayDados['id_prestador']),
            'nome_prestador' => addslashes($arrayDados['emit_xNome']),
            'cnpj_prestador' => addslashes($arrayDados['emit_CNPJ']),
            'n_documento' => addslashes($arrayDados['nNF']),
            'mes_competencia' => addslashes(converteData($arrayDados['dEmi'], 'm')),
            'ano_competencia' => addslashes(converteData($arrayDados['dEmi'], 'Y')),
            'dt_emissao_nf' => addslashes($arrayDados['dEmi']),
            'entradaesaida_subgrupo_id' => addslashes($arrayDados['id_subgrupo_entradasaida']),
            'tipo_empresa' => 1
        );

        print_array($array);
        exit();
        
        // insert da saida  
        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);
        $insert = "INSERT INTO saida ($keys) VALUES ('$values');";
        mysql_query($insert);
        if (mysql_errno())
            $erro[mysql_errno()] = mysql_errno();
        $id_saida = mysql_insert_id();

        // insert da nfe
        $insert_assoc = "INSERT INTO nfe_saidas (`id_nfe`, `id_saida`) VALUES ('$id_nfe', '$id_saida');";
        mysql_query($insert_assoc);
        if (mysql_errno())
            $erro[mysql_errno()] = mysql_errno();

        // update do status na nfe
//        $update = "UPDATE nfe SET status = 2 WHERE id_nfe = '$id_nfe';";
//        mysql_query($update);
//        if (mysql_errno())
//            $erro[mysql_errno()] = mysql_errno();
        // criando lancamento
        $query_lanc = "INSERT INTO contabil_lancamento (id_projeto,id_usuario,id_saida,data_lancamento,historico,contabil) 
                                    VALUES ('{$arrayDados['id_projeto']}','{$usuario['id_funcionario']}','{$id_saida}','{$data_vencimento}','" . addslashes($tipo_row['nome']) . "',1);";
        $lancamento_assoc = mysql_query($query_lanc) or die(mysql_error());
        if (mysql_errno())
            $erro[mysql_errno()] = mysql_errno();
    }


    $erro = implode(', ', $erro);
    if ($erro) {
        $msg = "e=$erro";
    } else {
        $msg = "s";
    }
    header("Location: rel_notas_produto.php?$msg");
    exit;
}

$nome_pagina = 'NFe Liberadas';
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if (isset($_GET['s'])) { ?><div class="alert alert-dismissable alert-success text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Saídas Geradas com Sucesso!</div><?php } ?>
            <?php if (isset($_GET['e'])) { ?><div class="alert alert-dismissable alert-danger text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Erro: <?= $_GET['e'] ?>. Entre em contato com o suporte.</div><?php } ?>
            <form action="rel_notas_produto.php" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => "« TODOS »")), $id_projeto, "id='projeto' name='projeto' class='form-control'"); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <!--                            <label for="select" class="col-lg-2 control-label">Prestador</label>
                                                        <div class="col-lg-9">
                            <?php echo montaSelect($prestadores, $id_prestador, "id='prestador' name='prestador' class='form-control'"); ?>
                                                        </div>-->
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <!--<input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />-->
                        <button type="submit" name="filtrar"  id="filt" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>

                <?php
                if ($filtro) {
                    if (count($arrayNFe) > 0) {
                        ?> 
                        <p><span style="background-color: rgb(252, 248, 227); border: 1px solid rgb(244, 176, 79);">&emsp;</span> Falta associação de Subgrupo e Tipo de Saída</p>
                        <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                            <thead>
                                <tr class="bg-primary">
                                    <th class="text-center">#</th>
                                    <th>Projeto</th>
                                    <th style="width: 210px;">Banco</th>
                                    <th >Fronecedor</th>
                                    <th>N&ordm; NFe</th>
                                    <!--<th style="width: 17%">Discriminação</th>-->
        <!--                                    <th class="text-center">PIS / COFINS / CSLL</th>
                                    <th class="text-center">IR</th>
                                    <th class="text-center">INSS</th>
                                    <th class="text-center">ISS</th>-->
                                    <th class="text-center">Valor (R$)</th>
                                    <!--<th class="text-center"><i class="fa fa-file-pdf-o"></i></th>-->
                                    <!--<th class="text-center"><i class="fa fa-print"></i></th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arrayNFe as $id_nfe => $value) { ?>
                                    <tr class="<?= (!empty($value['id_tipo_entradasaida']) && !empty($value['id_subgrupo_entradasaida'])) ? "" : "warning" ?>">
                                        <td class="text-center">
                                            <?php $tem_tipo = (!empty($value['id_tipo_entradasaida']) && !empty($value['id_subgrupo_entradasaida'])) ? "1" : "0" ?>
                                            <input type="checkbox" name="nfe[<?= $value['id_nfe']; ?>]" value="<?= $value['id_nfe']; ?>" class="check_nfe" data-validacao="<?= $tem_tipo ?>" data-cod-serv="<?= $value['CodigoTributacaoMunicipio'] ?>" data-prestador="<?= $value['id_prestador'] ?>">
                                        </td>
                                        <td><?= $value['dest_xNome']; ?></td>
                                        <td><?= montaSelect($arrayBancos, $arrIdBanco[$value['id_projeto']], 'name="banco[' . $value['id_nfe'] . ']" class="validate[required,custom[select]] form-control input-sm"') ?></td>
                                        <td><?= $value['emit_xNome']; ?></td>
                                        <td><?= $value['nNF']; ?></td>
                                        <!--<td><?= $value['Discriminacao']; ?></td>-->
            <!--                                        <td class="text-right"><?= number_format(($value['ValorPis'] + $value['ValorCofins'] + $value['ValorCsll']), 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorIr'], 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorInss'], 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorIss'], 2, ',', '.'); ?></td>-->
                                        <td class="text-right"><?= number_format($value['vNF'], 2, ',', '.'); ?></td>
                                        <!--<td class="text-right"><a class="btn btn-xs btn-default" href="../../compras/notas_fiscais/nfe_anexos/<?= $value['id_projeto'] ?>/<?= $value['arquivo_pdf'] ?>" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i></a></td>-->
                                        <!--<td class="text-right"><a class="btn btn-xs btn-success" href="https://notacarioca.rio.gov.br/contribuinte/notaprint.aspx?nf=<?= $value['Numero'] ?>&cod=<?= str_replace(array('-'), '', $value['CodigoVerificacao']) ?>&inscricao=<?= $value['inscricao_municipal'] ?>" target="_blank"><i class="fa fa-print"></i></a></td>-->
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <div class="panel panel-default">
                            <div class="panel-footer text-right">
                                <div class=" col-xs-2 col-xs-offset-6">
                                    <label class="control-label">Data de Vencimento</label>
                                </div>
                                <div class=" col-xs-2">
                                    <input class="form-control data validate[required]" name="data_vencimento" id="data_vencimento">
                                </div>
                                <!--<div class=" col-xs-2">
                                <!--<label class="control-label">Banco</label>
                            </div>
                            <div class=" col-xs-4">
                                <!--<?= montaSelect($arrayBancos, null, 'name="banco" class="validate[required,custom[select]] form-control"') ?>
                            </div>-->
                                <div class="col-xs-2">
                                    <button type="submit" class="btn btn-pa-purple btn-block" name="gerar_saida" value="gerar_saida"><i class="fa fa-gears"></i> Gerar Saída</button>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>

                        <div class="clear"></div>

                    <?php } else { ?>
                        <div class="alert alert-danger top30">
                            Nenhum registro encontrado
                        </div>
                        <?php
                    }
                }
                ?>
            </form>
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function () {
                var link = 'rel_notas_produto_controle.php';
                $(".check_nfe").change(function () {
                    var $this = $(this);
                    var validacao = $this.data('validacao');

                    if ($this.prop('checked') && validacao == 0) {
                        var codigo_servico = $this.data('cod-serv');
                        var id_prestador = $this.data('prestador');
                        console.log(codigo_servico + " " + id_prestador);
                        $.post(link, {method: 'form_subgrupo', codigo_servico: codigo_servico, id_prestador: id_prestador}, function (data) {
                            BootstrapDialog.show({
                                nl2br: false,
                                title: 'Informe os campos abaixo',
                                message: data,
                                type: 'type-warning',
                                buttons: [
                                    {
                                        label: '<i class="fa fa-times"></i> Fechar',
                                        action: function (dialogRef) {
                                            $this.prop('checked', false);
                                            dialogRef.close();
                                        }
                                    },
                                    {
                                        label: '<i class="fa fa-floppy-o"></i> Salvar',
                                        cssClass: 'btn-warning',
                                        action: function (dialogRef) {
                                            if (enviar()) {
                                                $this.closest('tr').removeClass('warning');
                                                dialogRef.close();
                                            }
                                        }
                                    }
                                ],
                                closable: false
                            });
                        });

                    }
                });

                $("#form1").validationEngine({promptPosition: "topRight"});

                $("#filt").click(function () {
                    $("#data_vencimento").removeClass('validate[required]');
                });

//                $("#projeto").change(function(){
//                    $.post(window.location.href,{method:'carregaPrestadores',projeto:$(this).val()},function(data){
//                        $("#prestador").html(data);
//                    });
//                });

                $('body').on('change', '#subgrupo', function () {
                    $.post(link, {method: 'getTipo', id_sub: $(this).val()}, function (data) {
                        $("#tipo").html(data);
                    });
                });

            });

            function enviar() {
                var retorno = true;
                $("#form_subgrupo").ajaxSubmit({
                    beforeSubmit: function () {
                        return $("#form_subgrupo").validationEngine('validate');
                    },
                    data: {'method': 'salvar'},
                    dataType: 'json',
                    success: function (dados) {
                        if (dados.status === true) {
                            bootAlert("Salvo com Sucesso!", "Salvar", null, 'success');

                        } else {
                            bootAlert("Erro ao Salvar!", "Salvar", null, 'danger');
                            retorno = false;
                        }

                    }
                });
                return retorno;
            }
        </script>
    </body>
</html>
