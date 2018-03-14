<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/ViagemClass.php");
include("../../classes/CaixinhaClass.php");
include("../../classes/global.php");
include("../../classes/ProjetoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objViagem = new ViagemClass();
$objCaixinha = new CaixinhaClass();
$objProjeto = new ProjetoClass();

$arrayProjetos = $objProjeto->getProjetosMaster();
$optProjetos[''] = '--SELECIONE--';
foreach ($arrayProjetos as $key => $value) {
    $optProjetos[$value['id_projeto']] = $value['nome'];
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'selectItem') {
    mysql_query("SET NAMES 'utf8'");
    $sql = "SELECT * FROM itens_despesas WHERE id = {$_REQUEST['id']} LIMIT 1;";
    $qry = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($qry);
    echo json_encode($row);
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getMeioTransporte') {
    mysql_query("SET NAMES 'utf8'");
    $sql = "SELECT * FROM meio_transporte WHERE id_tipo_meio_transporte = {$_REQUEST['id']} AND status = 1;";
    $qry = mysql_query($sql) or die(mysql_error());
    echo '<option value="">--Selecione--</option>';
    while($row = mysql_fetch_assoc($qry)) {
        $selected = ($row['id'] == $_REQUEST['selected']) ? 'SELECTED' : '';
        $desc = ($row['id_tipo_meio_transporte'] == 1) ? $row['linha'] : "{$row['modelo']} - {$row['placa']}";
        echo "<option value='{$row['id']}' $selected>{$desc}</option>";
    }
    echo '<option value="9999">Outro</option>';
    exit;
}

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
//    print_array($_REQUEST);
    $objViagem->cadViagem();    
}


if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Editar"){
//    print_array($_REQUEST); exit;
    $objViagem->editViagem();
}

if(isset($_REQUEST['i'])) {
    $rowViagem = $objViagem->getViagemById($_REQUEST['i']);
    $arrayItensViagem = $objViagem->getItensByIdViagem($_REQUEST['i']);
//    print_array($rowViagem);
}

$arrayItens = $objCaixinha->getItensDespesas();

$nome_pagina = "Solicitação de Viagem";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Módulo Viagem" => "index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: <?php echo $nome_pagina ?></title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <style>
            .label-button { width: 100%; }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?php echo $nome_pagina ?></small></h2></div>                                                                                      
            <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <?php if(isset($_SESSION['regiao'])){ ?>
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-dismissable alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>                                
                
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="">Projeto:</label>
                                    <div class="" id="">
                                        <?php echo montaSelect($optProjetos, $rowViagem['id_projeto'], 'class="form-control input-sm validate[required]" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="">Funcionário</label>
                                    <div class="input-group">
                                        <div class='input-group-addon'><input type="radio" id="fun1" name="funcionario" class="validate[required]" value="1" <?php echo ($rowViagem['id_user'] > 0 || !isset($_REQUEST['i'])) ? 'CHECKED' : '' ?> /></div>
                                        <label class="form-control input-sm" for='fun1'>Sim</label>
                                        <div class='input-group-addon'><input type="radio" id="fun2" name="funcionario" class="validate[required]" value="2"  /></div>
                                        <label class="form-control input-sm" for='fun2'>Não</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="">Nome / Razão</label>
                                    <input type="text" class="form-control input-sm validate[required]" id="nome_razao" name="nome_razao" style="display: none;" value="<?php echo $rowViagem['nome'] ?>" />
                                    <?php echo montaSelect($objViagem->getCltR(),"{$rowViagem['id_user']}///{$rowViagem['nome']}", "id='user' name='user' class='form-control input-sm validate[required,custom[select]]' style='/* display: none; */'"); ?>
                                </div>
                                <div class="col-sm-3">
                                    <label class="">Período da Viagem</label>
                                    <div class="input-group">
                                        <input type="text" class="data form-control input-sm validate[required]" id="data_ini" name="data_ini" placeholder="Inicio" value="<?php echo $rowViagem['data_ini'] ?>" />
                                        <div class="input-group-addon">até</div>
                                        <input type="text" class="data form-control input-sm validate[required]" id="data_fim" name="data_fim" placeholder="Fim" value="<?php echo $rowViagem['data_fim'] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <label class="">Origem</label>
                                    <input type="text" class="form-control input-sm validate[required]" id="origem" name="origem" value="<?php echo $rowViagem['origem'] ?>" />
                                </div>
                                <div class="col-sm-3">
                                    <label class="">Destino</label>
                                    <input type="text" class="form-control input-sm validate[required]" id="destino" name="destino" value="<?php echo $rowViagem['destino'] ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <label class="">Trajeto da Viagem</label>
                                    <input type="text" class="form-control input-sm validate[required]" id="trajeto" name="trajeto" value="<?php echo $rowViagem['trajeto'] ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <label class="">Meio de Transporte</label>
                                    <?php echo montaSelect(['' => '--Selcione--', 1 => 'Ônibus', 2 => 'Carro', 3 => 'Moto'], $rowViagem['id_tipo_meio_transporte'], "id='tipo_meio_transporte' data-transporte='" . $rowViagem['id_meio_transporte'] . "' name='tipo_meio_transporte' class='form-control input-sm validate[required]'"); ?>
                                </div>
                                <div class="col-sm-2">
                                    <label>Cadastrado</label>
                                    <?php echo montaSelect([], null, "id='meio_transporte' name='meio_transporte' class='form-control input-sm validate[required]'"); ?>
                                </div>
                                <div class="col-sm-2 carro">
                                    <label class="carro">Modelo</label>
                                    <input type="text" id='modelo' name='modelo' class='carro form-control input-sm validate[required]' value="<?php echo $rowViagem['modelo'] ?>" />
                                </div>
                                <div class="col-sm-2 carro">
                                    <label class="carro">Placa</label>
                                    <input type="text" id='placa' name='placa' class='carro form-control input-sm validate[required]' value="<?php echo $rowViagem['placa'] ?>" />
                                </div>
                                <div class="col-sm-2 onibus">
                                    <label class="onibus">Linha</label>
                                    <input type="text" id='linha' name='linha' class='onibus form-control input-sm validate[required]' value="<?php echo $rowViagem['linha'] ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label class="">Objetivo da Viagem</label>
                                    <textarea class="form-control input-sm validate[required]" rows="4" id="textArea" name="descricao"><?php echo $rowViagem['descricao'] ?></textarea>
                                </div>
                            </div>
                            <div class="panel panel-default panel-itens">
                                <div class="panel-heading">DESPESAS DE VIAGEM <button type="button" class="btn btn-xs btn-success pull-right addItem"><i class="fa fa-plus"></i></button></div>
                                <?php $c = 0; foreach ($arrayItensViagem as $key => $value) { $c++; ?>
                                    <div class="panel-footer no-padding-vr despesas">
                                        <div class="form-group">
                                            <div class="col-sm-3">
                                                <label class="control-label">Item</label>
                                                <?php echo montaSelect($arrayItens, $value['id_item'], 'class="form-control input-sm input-sm item" data-key="'.$c.'" name="item[' . $c . '][id_item]"') ?>
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Unidade</label>
                                                <input type="text" class="form-control input-sm input-sm" id="unidade<?php echo $c ?>" disabled="disabled" value="UND">
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Valor Unitário</label>
                                                <input type="text" class="form-control input-sm input-sm" id="valor_unitario<?php echo $c ?>" disabled="disabled" value="0,00">
                                            </div>
                                            <div class="col-sm-1">
                                                <label class="control-label">Qtd</label>
                                                <input type="text" class="form-control input-sm input-sm qtd" data-key="<?php echo $c ?>" name="item[<?php echo $c ?>][qtd]" value="<?php echo $value['qtd'] ?>">
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">Valor</label>
                                                <input type="text" class="form-control input-sm input-sm valorT" id="valor<?php echo $c ?>" name="item[<?php echo $c ?>][valor]" readonly="readonly" value="0,00">
                                            </div>
                                            <div class="col-sm-1">
                                                <label class="control-label label-button">&nbsp;</label>
                                                <button type="button" class="btn btn-md btn-danger delItem"><i class="fa fa-trash-o"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <label class="input-group-addon text-bold">Valor Total:</label>
                                        <input type="text" class="form-control input-sm validate[required]" READONLY id="valor" name="valor" />
                                    </div>
                                </div>
                            </div>
                            <hr class="panel-wide">
                            
                            <h6 class="text-light-gray text-bold text-sm form-group-margin" style="margin:20px 0 10px 0;">DADOS BANCÁRIOS PARA O DEPÓSITO</h6>
                            
                            <div class="form-group">
                                
                                <div class="col-sm-4">
                                    <label class="">Banco</label>
                                    <input type="text" class="form-control input-sm" id="banco" name="banco" value="<?php echo $rowViagem['banco'] ?>" />
                                </div>
                                <div class="col-sm-4">
                                    <label class="">Agência</label>
                                    <input type="text" class="form-control input-sm" id="agencia" name="agencia" value="<?php echo $rowViagem['agencia'] ?>" />
                                </div>
                                <div class="col-sm-4">
                                    <label class="">Conta</label>
                                    <input type="text" class="form-control input-sm" id="conta" name="conta" value="<?php echo $rowViagem['conta'] ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="">Nome</label>
                                    <input type="text" class="form-control input-sm" id="nomefavo" placeholder="Favorecido" name="nomefavo" value="<?php echo $rowViagem['favorecido'] ?>" />
                                </div>
                                <div class="col-sm-4">
                                    <label class="">CPF</label>
                                    <input type="text" class="form-control input-sm" id="cpf" name="cpf" placeholder="CPF/CNPJ" value="<?php echo $rowViagem['cpf'] ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <?php if(isset($_REQUEST['i']) && $_REQUEST['i'] > 0) { ?><input type="hidden" name="id_viagem" value="<?php echo $rowViagem['id_viagem'] ?>"><?php } ?>
                            <div class="col-sm-6 text-left"><button type="button" class="btn btn-default" id="volta_index" name="voltar"><span class="fa fa-reply"></span>&nbsp;Voltar</button></div>
                            <div class="col-sm-6 text-right"><input type="submit" class="btn btn-primary" name="cadastrar" id="cadastrar" value="<?php echo (isset($_REQUEST['i']) && $_REQUEST['i'] > 0) ? 'Editar' : 'Cadastrar' ?>" /></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </fieldset>
            </form>
            <?php include("../../template/footer.php"); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript" ></script>
        <script src="../../resources/js/financeiro/reembolso.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script>
        function somaValor(){
            var valor_total = parseFloat(0);
            $('.valorT').each(function (index, value) { 
                if(value.value){
                    valor_total += parseFloat(value.value.replace(/\./g, '').replace(/\,/g, '.'));
                }
            });

            valor_total = number_format(valor_total.toFixed(2), 2, ',', '.');
            $('#valor').val(valor_total);
        }

        $("#valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
        $("#form1").validationEngine({promptPosition : "topRight"});

        /* Adiciona Item de despesa */
        var i = 0;
        $('body').on('click', '.addItem', function(){
            i = $('.despesas').length + 1;
            $('.panel-itens').append(
                $('<div>', { class: "panel-footer no-padding-vr despesas" }).append(
                    $('<div>', { class: "form-group" }).append(
                        $('<div>', { class: "col-sm-3" }).append(
                            $('<label>', { class: "control-label", html: "Item" }),
                            $('<?php echo montaSelect($arrayItens, null, 'class="form-control input-sm input-sm item"') ?>').attr('data-key', i).attr('name', "item[" + i + "][id_item]")
                        ),
                        $('<div>', { class: "col-sm-2" }).append(
                            $('<label>', { class: "control-label", html: "Unidade" }),
                            $('<input>', { type: "text", class: "form-control input-sm input-sm", id: "unidade" + i, disabled: true, value: "UND" })
                        ),
                        $('<div>', { class: "col-sm-2" }).append(
                            $('<label>', { class: "control-label", html: "Valor Unitário" }),
                            $('<input>', { type: "text", class: "form-control input-sm input-sm valor_unitario", id: "valor_unitario" + i, 'data-key': i, /*disabled: true,*/ value: "0,00" }).maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','})
                        ),
                        $('<div>', { class: "col-sm-1" }).append(
                            $('<label>', { class: "control-label", html: "Qtd" }),
                            $('<input>', { type: "text", class: "form-control input-sm input-sm qtd", id: "qtd" + i, 'data-key': i, name: "item[" + i + "][qtd]", value: "1" })
                        ),
                        $('<div>', { class: "col-sm-2" }).append(
                            $('<label>', { class: "control-label", html: "Valor" }),
                            $('<input>', { type: "text", class: "form-control input-sm input-sm valorT", id: "valor" + i, name: "item[" + i + "][valor]", readonly: true, value: "0,00" })
                        ),
                        $('<div>', { class: "col-sm-1" }).append(
                            $('<label>', { class: "control-label label-button", html: "&nbsp;" }),
                            $('<button>', {  type: "button", class: "btn btn-sm btn-danger delItem" }).append(
                                $('<i>', { class: "fa fa-trash-o" })
                            )
                        )
                    )
                )
            )
        });

        /* Deleta Item de despesa */
        $('body').on('click', '.delItem', function(){
            $(this).parent().parent().parent().remove();
        });

        /* Altera os valores fixos para cada item */
        $('body').on('change', '.item', function(){
            var $this = $(this);
            $.post('', { method: 'selectItem', id: $this.val() }, function(data){
                $('#unidade'+$this.data('key')).val(data.unidade);
//                $('#valor_unitario'+$this.data('key')).val(number_format(data.valor_unitario, 2, ',', '.'));
                $('.qtd').trigger('keyup');
            }, 'json');
        });

        /* Calucula o valor ao preencher a quantidade */
        $('body').on('keyup', '.qtd, .valor_unitario', function(){
            var $this = $(this);
            var valor_unitario = parseFloat($('#valor_unitario'+$this.data('key')).val().replace(/\./g, '').replace(/\,/g, '.'));
            var qtd = parseFloat($('#qtd'+$this.data('key')).val().replace(/\./g, '').replace(/\,/g, '.'));
            var valor = (valor_unitario * qtd);
            valor = number_format(valor.toFixed(2), 2, ',', '.');
            $('#valor'+$this.data('key')).val(valor).maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});;
            somaValor();
        });

        /*  */
        $('.carro, .onibus').hide();
        $('body').on('change', '#tipo_meio_transporte', function(){
            $this = $(this);
            $.post('', { method: 'getMeioTransporte', id: $this.val(), selected: $this.data('transporte')}, function(result){
                $('#meio_transporte').html(result);
                cadTransporte();
            });
        });
        
        $('body').on('change', '#meio_transporte', function(){
            cadTransporte();
        });
        
        $('#tipo_meio_transporte').trigger('change');
        $('.item').trigger('change');
        function cadTransporte () {
//            console.log($('#tipo_meio_transporte').val(), $('#meio_transporte').val());
            $('.carro, .onibus').hide();
            if($('#meio_transporte').val() === '9999') {
                if($('#tipo_meio_transporte').val() === '1') {
                    $('.onibus').show();
                } else if($('#tipo_meio_transporte').val() === '2' || $('#tipo_meio_transporte').val() === '3') {
                    $('.carro').show();
                }
            }
        }
        </script>
    </body>
</html>