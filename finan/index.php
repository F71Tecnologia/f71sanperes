<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../funcoes.php");
include("../wfunction.php");
include("../classes/global.php");
include("../classes_permissoes/acoes.class.php");
include("../classes/BotoesClass.php");
include("../classes/BancoClass.php");
include("../classes/SaidaClass.php");
include("../classes/EntradaClass.php");
include("../classes/FinaceiroClass.php");
include("../classes/AbastecimentoClass.php");
include("../classes/ReembolsoClass.php");
include("../classes/ViagemClass.php");
include("../classes/CaixinhaClass.php");
include("../classes/NFSeClass.php");
//include("../classes/FolhaClass.php");
//include("../classes/EventoClass.php");

$acoes = new Acoes();
$global = new GlobalClass();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objBanco = new Banco();
$dadosBanco = $objBanco->getControleSaldo();
$dadosBanco2 = $objBanco->getControleSaldo();
while ($rowBanco2 = mysql_fetch_assoc($dadosBanco2)) {
    $arrayDadosBanco2[] = $rowBanco2['id_banco'];
}

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'], $dadosHeader['fullRootPath']);
$botoesMenu = $botoes->getBotoesMenuModulo(3);

//LISTA DE EVENTOS
//$objEvento = new Eventos();
//$listaEventos = $objEvento->listaEventos();
//$dadosEventos = $objEvento->getTerminandoEventos(date("Y-m-d"), $usuario['id_regiao'], null, null, 10);

$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => "Principal");
//$breadcrumb_pages = array("Lista Projetos" => "ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Financeira</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
        <style>
            .alert-float {
                display: inline-block;
                position: fixed;
                right: 20px;
                bottom: 40%;
                color: #555;
                text-decoration: none;
                font-size: 25px;
                opacity: 0.7;
                /*display: none;*/
                -webkit-transition: .150s;
                transition: .150s;
            }
            .modal-dialog  {
                position: relative;
                overflow-y: auto;
                width: 70%;
            }     
            .text-negative:before {
                font-weight: bold;
                color: #e14430;
                content: "-";
            } 

            .inner-addon { 
                position: relative; 
            }

            .inner-addon .glyphicon {
                position: absolute;
                padding: 10px;
                pointer-events: none;
            }

            .left-addon .glyphicon  { left:  0px;}
            .right-addon .glyphicon { right: 0px;}

            .left-addon input  { padding-left:  30px; }
            .right-addon input { padding-right: 30px; }      
            
            .hide_rows {
                display: none;
            }
        </style>
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - FINANCEIRO</h2></div>
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#avisos" data-toggle="tab" class="text-uppercase">Principal<i class="abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm"></i></a></li>
                            <?php
                            foreach ($botoesMenu as $k => $btMenu) {

                                if ($k == 57) {
                                    ?>
                                    <li><a href="#compras" data-toggle="tab" id="tabCompras" class="text-uppercase">Compras<i class="abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm"></i></a></li>
                                <?php } else if ($k == 58) { ?>
                                    <li style="display:none;"><a href="#servicos" data-toggle="tab" id="tabServicos" class="text-uppercase">Serviços<i class="abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm"></i></a></li>
                                <?php } else { ?>
                                    <li><a href="#<?php echo $k ?>" data-toggle="tab" class="text-uppercase"><?php echo $btMenu ?><?= ($k != 21) ? '<i class="abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm"></i>' : '' ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                            <li>
                                <a href="#conciliacao" id="conciliacao-tab" data-toggle="tab" class="text-uppercase">Conciliação<i id="i_conciliacao"></i></a>
                            </li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <div class="tab-pane" id="<?php echo $k ?>">
                                    <div class="detalhes-modulo">
                                        <?php echo $botoes->getHtmlBotoesModulo($k, 4) ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="tab-pane active" id="avisos">
                                <input type="hidden" id="upload_anexo_itens_viagem">
                                <?php include("include_principal.php") ?>
                            </div>
                            <div class="tab-pane" id="compras">
                                <div class="col-lg-12">
                                    <div class="panel panel-default">
                                        <legend style="padding: 10px;">Autorização de Fornecimento</legend>  
                                        <div class="note note-info" id="noteCompras">
                                            <h4 class="note-title">Carregando Ordens de compras</h4>
                                            Aguarde, as ordens estão sendo carregadas...
                                        </div>
                                        <div class="panel-body" id="tableCompras" style="display: none;">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <th class="col-lg-1" style="text-align: center;">Nº Pedido</th>
                                                            <th class="col-lg-2" style="text-align: center;">Projeto</th>
                                                            <th class="col-lg-4" style="text-align: center;">Justificativa</th>
                                                            <th class="col-lg-2" style="text-align: center;">Fornecedor</th>
                                                            <th class="col-lg-1" style="text-align: center;">Valor</th>
                                                            <th class="col-lg-1" style="text-align: center;">Data do Pedido</th>
                                                            <th class="col-lg-1" class="col-lg-1" style="text-align: center;"><i class="fa fa-search"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="lista-aguardando-pagamento">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="conciliacao">
                                <div class="bs-component">
                                    <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                                        <li class="active"><a href="#conciliacao-completa" id="tab-conciliacao-completa" data-toggle="tab" class="text-uppercase">Conciliado<i class="abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm"></i></a></li>
                                        <li><a href="#conciliacao-parcial" id="tab-conciliacao-parcial" data-toggle="tab" class="text-uppercase">Conciliação Parcial<i id="i_conciliacao"></i></a></li>
                                        <li><a href="#conciliacao-inexistente-sistema" id="tab-conciliacao-inexistente-sistema" data-toggle="tab" class="text-uppercase">Sem Conciliação no Sistema<i id="i_conciliacao"></i></a></li>
                                        <li><a href="#conciliacao-inexistente-banco" id="tab-conciliacao-inexistente-banco" data-toggle="tab" class="text-uppercase">Sem Conciliação no Banco<i id="i_conciliacao"></i></a></li>
                                    </ul>
                                    <div id="tab-conciliacao" class="tab-content">
                                        <div class="panel panel-default margin_b10">
                                            <div class="panel-heading text-bold">Filtro <label class="text-sm"> - Último Movimento Importado: <span id="data_ultimo_movimento">apurando ...</span></label></div>
                                            <div class="panel-body">   
                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-lg-5">
                                                            <select id="projeto" name="projeto" class="form-control validate[required,custom[select]]">
                                                            </select>
                                                        </div>                                                        
                                                        <div class="col-lg-1">
                                                            <label class="text-sm">Data</label>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <input type="text" class="form-control data input-sm" name="data-conciliacao" id="data-conciliacao" value="<?php echo date('d/m/Y') ?>">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <button class="btn btn-sm btn-primary" id="conciliacao-filtro"><i class="fa fa-filter"></i></button>
                                                        </div> 
                                                        <div class="col-lg-2">
                                                            <div id="controle-saldo">
                                                                <div class="inner-addon left-addon">
                                                                    <i class="glyphicon glyphicon-plus"></i>
                                                                    <input type="text" id="valor_banco_credito_total" class="form-control input-sm currency text-right"  placeholder=""  value="" disabled="disabled"/>
                                                                </div>
                                                                <div class="inner-addon left-addon">
                                                                    <i class="glyphicon glyphicon-minus"></i>
                                                                    <input type="text" id="valor_banco_debito_total" class="form-control input-sm currency text-right text-danger text-negative"  placeholder=""  value=""  disabled="disabled"/>
                                                                </div>
                                                                <div class="inner-addon left-addon">
                                                                    <i class="glyphicon glyphicon-usd"></i>
                                                                    <input type="text" id="valor_banco_saldo" class="form-control input-sm currency text-right"  placeholder=""  value=""  disabled="disabled"/>
                                                                </div>
                                                            </div>    
'                                                        </div>
                                                    </div>    
                                                </div>                                                
                                            </div>
                                        </div>

                                        <div class="panel panel-default margin_b10">
                                            <div class="panel-heading text-bold">Ações</div>
                                            <div class="panel-body">   
                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-lg-2">
                                                            <button class="btn btn-sm btn-primary" id="conciliar-registros" style="visibility: hidden" data-tab_active="tab-conciliacao-completa"><i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;Conciliar&nbsp;</button>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <button class="btn btn-sm btn-primary " id="incluir-registros" style="visibility: hidden" data-tab_active="tab-conciliacao-completa"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Incluir Entradas&nbsp;</button>
                                                        </div>
                                                        <div class="col-lg-4">
                                                        </div>
                                                    </div>
                                                </div>   
                                            </div>    
                                        </div>    

                                        <div class="tab-pane active" id="conciliacao-completa">
                                            <table id="table-completa" class="table table-striped table-hover table-bordered dataTable table-conciliacao">
                                                <tr valign="top">
                                                    <th scope="row" class="text-center">Código</th>
                                                    <th scope="row" class="text-center">Nome</th>
                                                    <th scope="row" class="text-center">N.Documento</th>
                                                    <th scope="row" class="text-center">Especificação</th>
                                                    <th scope="row" class="text-center">Pagamento</th>
                                                    <th scope="row" class="text-left">Valor</th>
                                                    <th scope="row" class="text-center"><i class="fa fa-clone"></i></th>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="conciliacao-parcial">
                                            <table id="table-parcial" class="table table-striped table-hover table-bordered dataTable table-conciliacao">
                                                <thead>

                                                    <tr valign="top">
                                                        <th colspan="7" scope="row" class="text-center">Sistema</th>
                                                        <th colspan="6" scope="row" class="text-center">Extrato Banco</th>
                                                    </tr>
                                                    <tr valign="top">

                                                        <th scope="row" class="text-center"><input type="checkbox" class="checkbox-conciliacao-todos"/><span id="totais"></span></th>
                                                        <th scope="row" class="text-center">Código</th>
                                                        <th scope="row" class="text-center">N.Documento</th>
                                                        <th scope="row" class="text-center">Nome</th>
                                                        <th scope="row" class="text-center">Especificação</th>
                                                        <th scope="row" class="text-center">Pagamento</th>
                                                        <th scope="row" class="text-center">Valor</th>

                                                        <th scope="row" class="text-center">Código</th>
                                                        <th scope="row" class="text-center">N.Documento</th>
                                                        <th scope="row" class="text-center" colspan="2">Histórico</th>
                                                        <th scope="row" class="text-center">Pagamento</th>
                                                        <th scope="row" class="text-center">Valor</th>
                                                        <th scope="row" class="text-center"><i class="fa fa-clone"></i></th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>                                                    
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="conciliacao-inexistente-sistema">
                                            <table id="table-inexistente-sistema" class="table table-striped table-hover table-bordered dataTable table-conciliacao">
                                                <tr valign="top">
                                                    <th scope="row" class="text-center">Código</th>
                                                    <th scope="row" class="text-center">Nome</th>
                                                    <th scope="row" class="text-center">N.Documento</th>
                                                    <th scope="row" class="text-center">Especificação</th>
                                                    <th scope="row" class="text-center">Pagamento</th>
                                                    <th scope="row" class="text-center">Valor</th>
                                                    <th scope="row" class="text-center"><i class="fa fa-clone"></i></th>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="conciliacao-inexistente-banco">
                                            <table id="table-inexistente-banco" class="table table-striped table-hover table-bordered dataTable table-conciliacao">
                                                <tr valign="top">
                                                    <th scope="row" class="text-center"><input type="checkbox" class="checkbox-inclusao-todos"/></th>                                                    
                                                    <th scope="row" class="text-center">Código</th>
                                                    <th scope="row" class="text-center">N.Documento</th>
                                                    <th scope="row" class="text-center" colspan="2">Histórico</th>
                                                    <th scope="row" class="text-center">Pagamento</th>
                                                    <th scope="row" class="text-center">Valor</th>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-info alert-float hide" style="width: auto !important;"></div>
            <div class="modal fade" id="myModalAnexo">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header btn btn-primary" style="width:100%">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <p class="modal-title">Anexar Arquivo Retorno Padrão Cnab240</p>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <div id="dropzoneRemessa" class="dropzone margin_b15" style="min-height: 150px;"></div>
                            </div>

                            <div class="clear"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>            
            <?php include('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <!-- Código de Widget para loading e overlay-->
        <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/src/loadingoverlay.min.js" type="text/javascript" ></script>
        <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/extras/loadingoverlay_progress/loadingoverlay_progress.min.js" type="text/javascript" ></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js" type="text/javascript" ></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/financeiro/index.js?<?= date("Ymdhis") ?>"></script>
        <script src="../resources/js/date.js"></script>
        <script src="../resources/js/dataTables.1.10.16.min.js"></script>
        <script src="../resources/js/dataTables.bootstrap.1.10.16.min.js"></script>
        <script src="../js/jquery.form.js"></script>


        <script>
            $(function () {
                var url = '/intranet/';
                contaPedidos();

                $('#tabCompras').on('click', function () {
                    montaPedidos();
                });
                /* Adiciona Item de despesa */
                var i = 0;
                $('body').on('click', '.addItem', function () {
                    var token = (new Date().getTime()).toString(16);
                    i++;
                    $('.panel-itens').append(
                            $('<div>', {class: "panel-footer no-padding-vr"}).append(
                            $('<div>', {class: "form-group"}).append(
                            $('<div>', {class: "col-sm-3"}).append(
                            $('<label>', {class: "control-label", html: "Item"}),
                            $('<?php echo montaSelect($arrayItens, null, 'class="form-control input-sm input-sm item"') ?>').attr('data-key', i).attr('name', "item[" + i + "][id_item]")
                            ),
                            $('<div>', {class: "col-sm-3"}).append(
                            $('<label>', {class: "control-label", html: "Nota Fiscal"}),
                            $('<input>', {type: "text", class: "form-control input-sm input-sm", required: true, id: "nota_fiscal" + i, name: "item[" + i + "][nota_fiscal]", placehold: "Nota Fiscal"}),
                            $('<input>', {type: "hidden", id: "token" + i, name: "item[" + i + "][token]", value: token})
                            ),
                            $('<div>', {class: "col-sm-2"}).append(
                            $('<label>', {class: "control-label", html: "Valor"}),
                            $('<input>', {type: "text", class: "form-control input-sm input-sm valorT", required: true, id: "valor" + i, name: "item[" + i + "][valor]", value: "0,00"}).maskMoney({prefix: '', allowNegative: true, thousands: '.', decimal: ','})
                            ),
                            $('<div>', {class: "col-sm-3"}).append(
                            $('<label>', {class: "control-label", html: "&nbsp;", style: "width: 100%;"}),
                            $('<button>', {type: "button", class: "btn btn-sm btn-info anexoItem", 'data-token': token}).append(
                            $('<i>', {class: "fa fa-upload"})
                            )
                            ),
                            $('<div>', {class: "col-sm-1 text-right"}).append(
                            $('<label>', {class: "control-label", html: "&nbsp;", style: "width: 100%;"}),
                            $('<button>', {type: "button", class: "btn btn-sm btn-danger delItem"}).append(
                            $('<i>', {class: "fa fa-trash-o"})
                            )
                            ),
                            $('<div>', {class: "clearfix"})
                            )
                            )
                            )
                });

                /* Deleta Item de despesa */
                $('body').on('click', '.delItem', function () {
                    $(this).parent().parent().parent().remove();
                });

                $(".valores").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});



                /**
                 * 
                 * @type Dropzone
                 * UPALOAD DA ANEXO VIAGEM
                 */
                Dropzone.autoDiscover = false;
                Dropzone.options.myAwesomeDropzone = false;
                var myDropzoneItensAcertoViagem = new Dropzone('#upload_anexo_itens_viagem', {// Make the whole body a dropzone
                    url: "../uploadfy/scripts/uploadify.php", // Set the url
                    //                maxFiles: 1,
                    acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf",
                    autoQueue: true, // Make sure the files aren't queued until manually added
                    clickable: '#upload_anexo_itens_viagem', // Define the element that should be used as click trigger to select files.
                    init: function () {
                        DropZone = this;
                        $("#removeAllImages").click(function () {
                            DropZone.removeAllFiles();
                        })
                    },
                    sending: function (file, xhr, formData) {
                        formData.append("action", 'upload_anexo_itens_viagem'); // Append all the additional input data of your form here!
                    }
                });

                $('body').on('click', '.anexoItem', function () {
                    $this = $(this);
//                    console.log($this.parent().parent().find('.item').val()); return false;
                    myDropzoneItensAcertoViagem.on('sending', function (file, xhr, formData) {
                        formData.append("id_viagem", $('#id_viagem').val()); // Append all the additional input data of your form here!
                        formData.append("tipo_anexo", $('#tipo_anexo').val()); // Append all the additional input data of your form here!
                        formData.append("token", $this.data('token')); // Append all the additional input data of your form here!
                        formData.append("id_item", $this.parent().parent().find('.item').val()); // Append all the additional input data of your form here!
                    });

                    myDropzoneItensAcertoViagem.on('complete', function (progress) {
                        if ($this.parent().find('.verAnexoItensViagem').length == 0) {
                            $this.parent().append(
                                    $('<button>', {type: "button", class: "btn btn-sm btn-default verAnexoItensViagem", 'data-id_viagem': $('#id_viagem').val(), 'data-item': $this.parent().parent().find('.item').val()}).append(
                                    $('<i>', {class: "fa fa-paperclip"})
                                    )
                                    );
                        }
                    });

                    $('#upload_anexo_itens_viagem').trigger('click');
                });


                jQuery.extend(jQuery.fn.dataTableExt.oSort, {
                    "date-uk-pre": function (a) {
                        var ukDatea = a.split('/');
                        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                    },

                    "date-uk-asc": function (a, b) {
                        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
                    },

                    "date-uk-desc": function (a, b) {
                        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
                    }
                });

                jQuery.extend(jQuery.fn.dataTableExt.oSort, {
                    "currency-pre": function (a) {
                        a = (a === "-") ? 0 : a.replace(/[^\d\-\.]/g, "");
                        a = (a === "-") ? 0 : a.replace(/[^\d\-\,]/g, "");
                        return parseFloat(a);
                    },

                    "currency-asc": function (a, b) {
                        return a - b;
                    },

                    "currency-desc": function (a, b) {
                        return b - a;
                    }
                });

<?php foreach ($arrayDadosBanco2 as $key => $value) { ?>
                    var table<?php echo $value ?> = $("#example<?php echo $value ?>").DataTable({
                        "createdRow": function (row, data, index) {
                            if ($(row).find('.type').html() == 'entrada') {
                                $(row).addClass('info');
                            } else if ($(row).find('.type').html() == 'remessa') {
                                $(row).addClass('warning');
                            }
                        },
                        ajax: {
                            url: "carrega_saidas.php",
                            type: "post",
                            data: function (d) {
                                d.id_banco = '<?php echo $value ?>',
                                        d.data_ini = $('#filtroDataIni').val(),
                                        d.data_fim = $('#filtroDataFim').val(),
                                        d.id_projeto = $('#id_projeto').val()
                            },
                            dataType: "json"
                        },
                        "responsive": true,
                        order: [[6, "asc"]],
                        aoColumns: [
                            {"bSortable": false},
                            {"bSortable": false},
                            {"bSortable": false},
                            null,
                            null,
                            null,
                            {"sType": "date-uk"},
                            {"sType": "currency"},
                            {"bSortable": false},
                            {"bSortable": false},
                            {"bSortable": false}
                        ],
                        columnDefs: [
                            {className: "text-left", targets: [4]},
                            {className: "text-right", targets: [7]},
                            {className: "text-center", targets: "_all"}
                        ],
                        "lengthMenu": [[10, 25, 50, 75, 100, 500, -1], [10, 25, 50, 75, 100, 500, "Todos"]],
                        "fnDrawCallback": function () {
                            $("[data-toggle='tooltip']").tooltip();
                        },
                        language: {
                            "decimal": ",",
                            "emptyTable": "Nenhuma informação encontrada",
                            "info": "Mostrando _START_ à _END_ de _TOTAL_",
                            "infoEmpty": "Mostrando 0 à 0 de 0",
                            "infoFiltered": "(filtrando de _MAX_)",
                            "infoPostFix": "",
                            "thousands": ".",
                            "lengthMenu": "Mostrar _MENU_ resultados",
                            "loadingRecords": "Carregando...",
                            "processing": "Processando...",
                            "search": "Procurar:",
                            "zeroRecords": "Nenhum resultado encontrado",
                            "paginate": {
                                "first": "Primeiro",
                                "last": "Último",
                                "next": "Próximo",
                                "previous": "Anterior"
                            },
                            "aria": {
                                "sortAscending": ": activate to sort column ascending",
                                "sortDescending": ": activate to sort column descending"
                            }
                        }
                    });
<?php } ?>

                $('body').on('click', '#btnFiltro', function () {
                    somatorio('.saidas_check', '.alert-float');
<?php foreach ($arrayDadosBanco2 as $key => $value) { ?>
                        $('#example<?php echo $value ?> tbody').html(
                                '<tr class="odd"><td valign="top" colspan="10" class="dataTables_empty">Carregando</td></tr>'
                                );
                        table<?php echo $value ?>.ajax.reload();
<?php } ?>
                    somatorio('.saidas_check', '.alert-float');
                });
//                $('#btnFiltro').trigger('click');

                function somatorio(campos, result) {
                    var valor = 0.00;
                    $(campos).each(function (index, value) {
                        if ($(value).data('val') && $(value).prop('checked')) {
                            valor += parseFloat($(value).data('val'));
                        }
                    });

                    if (valor > 0) {
                        $(result).removeClass('hide');
                        $(result).html(number_format(valor, 2, ',', '.'));
                    } else {
                        $(result).addClass('hide');
                    }
                }

                $('body').on('click', '.saidas_check', function () {
                    somatorio('.saidas_check', '.alert-float');
                });

                ////DROPZONE PEDIDO DE COMPRAS
                Dropzone.autoDiscover = false;

                var myDropzoneAnexo = new Dropzone("#dropzoneRemessa", {
                    url: "/intranet/?class=financeiro/cnab240/remessa&method=uploadFileAndRun",
                    maxFiles: 10,
                    maxFilesize: 1,
                    //autoProcessQueue: true,
                    uploadMultiple: false,
                    parallelUploads: 1,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 1MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.ret,.RET',
                    //addRemoveLinks : true,
                    init: function () {

                        this.on('addedfile', function (file) {

                            console.log('addedfile');

                        });

                        this.on('sending', function (file, xhr, formData) { // Append all the additional input data of your form here!

                            console.log('sending');

                        });

                        this.on('success', function (file, data) {

                            var msg = '';

                            var json_data = JSON.parse(data);

                            console.log(data);

                            if (json_data.status) {

                                $.each(json_data['ocorrencias'], function (key_campo, value_campo) {

                                    $.each(value_campo, function (key_codigo, value_codigo) {

                                        $.each(value_codigo, function (key_descricao, value_descricao) {

                                            //msg += key_campo+' - '+key_codigo+' - '+key_descricao+'<br>';
                                            msg += key_descricao + '<br>';

                                        });

                                    });

                                });

                                bootAlert(msg, 'Status da operação', null, 'primary');

                            } else {

                                console.log('not success');

                            }

                            //                    //$('.close').trigger('click');


                        });

                        this.on('drop', function (file) {

                            console.log('drop');

                        });

                        this.on('error', function (file) {

                            console.log('error');

                        });

                    }
                });

                myDropzoneAnexo.enqueueFiles(myDropzoneAnexo.getFilesWithStatus(Dropzone.ADDED));

                $('body').on('click', '#banco_secundario', function () {
                    if ($('.banco_secundario').hasClass('hide')) {
                        $('.banco_secundario').removeClass('hide');
                    } else {
                        $('.banco_secundario').addClass('hide');
                    }
                });
                function montaPedidos() {
                    $.post(
                            url,
                            {
                                class: 'financeiro/compras/processar',
                                method: 'listaPedidos',
                                status: 0
                            }, function (data) {
                        if (data == '') {
                            $('#lista-aguardando-pagamento').html(data);
                            $('#noteCompras').html('Não existem Ordens de compras');
                        } else {
                            $('#noteCompras').hide();
                            $('#tableCompras').show();
                            $('#lista-aguardando-pagamento').html(data);
                            $('.abrePedido').on('click', function () {
                                BootstrapDialog.show({
                                    title: 'Autorização de Pedidos',
                                    message: $('<div class="text-center"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>').load('/intranet/?class=financeiro/compras/processar&method=modalAutorizacao&idPedido=' + $(this).attr('id'))
                                });
                            });
                        }
                    });
                }

                function contaPedidos() {
                    $.post(
                            url,
                            {
                                class: 'financeiro/compras/processar',
                                method: 'contaPedidos',
                                status: 0
                            }, function (data) {
                        var badge = '<span class="badge" style="margin-left:5px;">' + data + '</span>';
                        $('#tabCompras').append(badge);
                    });
                }
            });
        </script>
    </body>
</html>
