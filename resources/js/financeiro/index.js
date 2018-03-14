$(function () {
    
    var $tipos = Array();
    var $conciliacao = Array();

    function saveToDisk(fileURL, fileName) {
        if (!window.ActiveXObject) {
            var save = document.createElement('a');
            save.href = fileURL;
            save.target = '_blank';
            save.download = fileName || 'unknown';

            var evt = new MouseEvent('click', {
                'view': window,
                'bubbles': true,
                'cancelable': false
            });
            save.dispatchEvent(evt);

            (window.URL || window.webkitURL).revokeObjectURL(save.href);
        } else if (!!window.ActiveXObject && document.execCommand) {
            var _window = window.open(fileURL, '_blank');
            _window.document.close();
            _window.document.execCommand('SaveAs', true, fileName || fileURL)
            _window.close();
        }
    }
    
    /**
     * A função executa a soma da classe de valores de uma tabela
     * 
     * @param {type} $campos
     * @param {type} $campo_total
     * @return {undefined}
     */
    function somatorio($campos, $campo_total, $id_projeto) {
        
        var $valor_total = 0.00;
        
        $($campos).each(function ($index, $value) { 
            
            if($($value).data('id_projeto')==$id_projeto || $id_projeto==0) $valor_total += parseFloat($($value).data('lancamento_valor'));
                
        });

        $($campo_total).val(number_format($valor_total, 2, ',', '.'));
        
    }    
    
    $.getJSON('/intranet/',
        {
        class: "financeiro/cnab240/remessa",
        method: "tiposQuePodemSerIncluidos"
        }
    )
    .done(function($response) {  

        $.each($response,function($k, $v) {

            $tipos.push($v);
            
        }); 

    })
    .fail(function (data) {

    })
    .always(function () {

    });                 
    
    
    $.getJSON('/intranet/',
        {
        class: "financeiro/cnab240/remessa",
        method: "ultimoMovimentoImportado"
        }
    )
    .done(function($response) {  
        
        $("#data_ultimo_movimento").html($response.data_fmt);

        $("#data-conciliacao").val($response.data_fmt);
       
    })
    .fail(function (data) {

    })
    .always(function () {

    });
    
    
    $('#projeto').load('/intranet/?class=financeiro/cnab240/remessa&method=getOptProjetos')
    

    $("body").on('click', '.saidas_chkbox_group', function () {

        var $class = $(this).data('class');

        $($class).attr("checked", $(this).is(":checked") ? 1 : 0);

    });


    $(".valor").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});

    /*CONTROLE DE COMBUSTIVEL*/

    $('body').on('click', '.liberarCombustivel', function () {
        var idCombustivel = $(this).data('key');
        $('.combustivel').addClass('hidden');
        $('#' + idCombustivel).removeClass('hidden');
        cria_carregando_modal();
        $.post("actions/action_combustivel.php", {bugger: Math.random(), action: 'form_liberar_combustivel', id: idCombustivel}, function (resultado) {
            bootDialog(
                    resultado,
                    'Liberar Combustível',
                    [
                        {label: 'Cancelar', cssClass: 'btn-danger', action: function (dialog) {
                                dialog.close();
                            }},
                        {label: 'Enviar', cssClass: 'btn-primary',
                            action: function (dialog) {
                                var dados = $("#form_combustivel").serialize();
                                cria_carregando_modal();
                                $.post('actions/action_combustivel.php',
                                        dados,
                                        function (retorno) {
                                            if (retorno == 1) {
                                                var msg = 'Combustível Aprovado';
                                                var link = "../frota/printcombustivel.php?com=" + idCombustivel;
                                            } else {
                                                var msg = 'Combustível Recusado';
                                                var link = "index.php";
                                            }
                                            bootAlert(msg, msg, function () {
                                                window.location.href = link;
                                            }, 'warning');
                                            remove_carregando_modal();
                                        }
                                );
                                dialog.close();
                            }
                        }],
                    'info'
                    );
            remove_carregando_modal();
        });
    });

    /*CONTROLE DE REEMBOLSO*/

    $("body").on('click', ".verReembolso", function () {
        var idReembolso = $(this).data("idreembolso");
        cria_carregando_modal();
        $.post("actions/action_reembolso.php", {bugger: Math.random(), action: 'verReembolso', reembolso: idReembolso}, function (resultado) {
            bootDialog(
                    resultado,
                    'Confirmar Solicitação de Reembolso ' + idReembolso,
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }],
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    /*CONTROLE DE VIAGEM*/

    $("body").on('click', ".verViagem", function () {
        var id = $(this).data("id");
        cria_carregando_modal();
        $.post("/intranet/finan/actions/action_viagem.php", {bugger: Math.random(), action: 'ver', id: id}, function (resultado) {
            resultado = $(resultado);
            resultado.find('.data').mask("99/99/9999", {placeholder: " "}).datepicker();;
            bootDialog(
                    resultado,
//                'Confirmar Reembolso '+idReembolso, 
                    'Detalhe da Viagem ' + id,
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }],
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    $("body").on('click', ".AprovarViagem", function(){
        var id = $(this).data("key");
        var id_banco = $('#id_banco_viagem').val();
        var data_pag_viagem = $('#data_pag_viagem').val();
        cria_carregando_modal();
        $.post("/intranet/finan/actions/action_viagem.php", {bugger:Math.random(), action:'aprovar', id:id, id_banco:id_banco, data_vencimento:data_pag_viagem}, function(resultado){
//            console.log(resultado);
            bootAlert(
                resultado.msg, 
                'Aprovação', 
                function (dialog) {
                    if(resultado.status == 1){
                        window.location.reload();
                    }
                },
                (resultado.status == 1) ? 'success': 'danger'
            );
            if(resultado){
                remove_carregando_modal();
            }
        }, 'json');
    });
    
    $("body").on('click', ".aprovarAcerto", function(){
        var id = $(this).data("key");
        var id_banco = $('#id_banco_viagem').val();
        var data_pag_viagem = $('#data_pag_viagem').val();
        cria_carregando_modal();
        $.post("/intranet/finan/actions/action_viagem.php", {bugger:Math.random(), action:'aprovarAcerto', id:id, id_banco:id_banco, data_vencimento:data_pag_viagem }, function(resultado){
//            console.log(resultado);
            bootAlert(
                resultado.msg, 
                'Aprovação', 
                function (dialog) {
                    if(resultado.status == 1){
                        window.location.reload();
                    }
                },
                (resultado.status == 1) ? 'success': 'danger'
            );
            if(resultado){
                remove_carregando_modal();
            }
        }, 'json');
    });

    $("body").on('click', ".recusarAcerto", function () {
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("/intranet/finan/actions/action_viagem.php", {bugger: Math.random(), action: 'recusarAcerto', id: id}, function (resultado) {
//            console.log(resultado);
            bootAlert(
                    resultado.msg,
                    'Aprovação',
                    function (dialog) {
                        if (resultado.status == 1) {
                            window.location.reload();
                        }
                    },
                    (resultado.status == 1) ? 'success' : 'danger'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        }, 'json');
    });

    $("body").on('click', ".pagarPeloCaixinha", function () {
        var id = $(this).data("key");
        $this = $(this);
        bootConfirm('Pagar saída pelo caixinha?', 'Confirmação', function (data) {
            if (data) {
                cria_carregando_modal();
                $.post("/intranet/finan/actions/action_finan.php", {bugger: Math.random(), action: 'pagarPeloCaixinha', id: $this.data("key"), tipo: $this.data("tipo")}, function (resultado) {
                    bootAlert(
                            resultado.msg,
                            'Pagamento pelo caixinha',
                            function (dialog) {
                                if (resultado.status == 1) {
                                    window.location.reload();
                                }
                            },
                            (resultado.status == 1) ? 'success' : 'danger'
                            );
                    if (resultado) {
                        remove_carregando_modal();
                    }
                }, 'json');
            }
        }, 'warning');
    });
    
    $("body").on('click', ".conciliar", function () {
        $this = $(this);
//        cria_carregando_modal();
        $.post("/intranet/finan/actions/action_finan.php", {bugger: Math.random(), action: 'formConciliar', id: $this.data("key")}, function (resultado) {
            new BootstrapDialog({
                nl2br: false,
                size: 'size-wide',
                title: "<strong>Conciliar Saída</strong>",
                message: resultado,
                closable: false,
                type: 'type-warning',
                buttons: [
                    {
                        label: 'Cancelar',
                        action: function (dialog) {
                            dialog.close();
                        }
                    }, {
                        label: 'CONFIRMAR',
                        cssClass: 'btn-warning',
                        action: function (dialog) {
                            var array = [];
//                            console.log($('.radioConciliar').length);
                            $('.radioConciliar').each(function(i,v){
                                if($(v).prop('checked')){
                                    array.push($(v).val());
//                                    console.log(array);
                                }
                            });
                            $.post("actions/action_finan.php", {bugger: Math.random(), action: 'conciliar', saida: $('#adiantamento-saida').val(), adiantamento: array}, function (data) {
//                                console.log(data); return false;
                                if(data.status){
                                    bootAlert(data.msg, 'Confirmação', function(){ $('#btnFiltro').trigger('click'); }, 'success');
                                    dialog.close();
                                } else {
                                    bootAlert(data.msg, 'Confirmação', null, 'warning');
                                }
                            }, 'json');
                        }
                    }]
            }).open();
        }, 'html');
    });

    function somaValor() {
        var valor_total = valor_devolver = valor_pagar = parseFloat(0);
        var valorSolicitado = parseFloat($('#valorSolicitado').val().replace(/\./g, '').replace(/\,/g, '.'));

        $('.valorT').each(function (index, value) {
            if (value.value) {
                valor_total += parseFloat(value.value.replace(/\./g, '').replace(/\,/g, '.'));
            }
        });

        if ((valor_total - valorSolicitado) >= 0) {
            valor_pagar = Math.abs(valor_total - valorSolicitado);
        } else {
            valor_devolver = Math.abs(valor_total - valorSolicitado);
        }

        valor_total = number_format(valor_total.toFixed(2), 2, ',', '.');
        valor_devolver = number_format(valor_devolver.toFixed(2), 2, ',', '.');
        valor_pagar = number_format(valor_pagar.toFixed(2), 2, ',', '.');

        $('#valor').val(valor_total);
        $('#devolver').val(valor_devolver);
        $('#pagar').val(valor_pagar);
    }

    /**
     * Função que atualiza os valores totais de débito e crédito
     * 
     * @access   public
     * @function atualizaTotais
     * @param    integer
     * 
     * @return   void;
     */    
    function atualizaTotais() {
        
        var $saldo_valor = 'N/D';
        var $msg = '';
        
        var $id_projeto = parseInt($('#projeto option:selected').val());
        var $data_fmt = $("#data-conciliacao").val(); 
        var $data = new String($data_fmt);
        
        $data = $data.replace("/", "").replace("/", "");

        var $dia = $data.substr(0, 2);
        var $mes = $data.substr(2, 2);
        var $ano = $data.substr(4, 4);

        var $data = $ano+'-'+$mes+'-'+$dia;          
        
        
        somatorio('.valor_banco_credito', '#valor_banco_credito_total', $id_projeto);
        
        somatorio('.valor_banco_debito', '#valor_banco_debito_total', $id_projeto);
        
        console.log($conciliacao);
        
        if($id_projeto > 0) {

            var $id_nacional = $conciliacao.data_ext.bancos[$id_projeto].id_nacional;
            var $agencia     = $conciliacao.data_ext.bancos[$id_projeto].agencia;
            var $conta       = $conciliacao.data_ext.bancos[$id_projeto].conta;

            if($id_nacional !== null && $agencia !== null && $conta !== null) {
                
                if($conciliacao.data_ext.saldos && $conciliacao.data_ext.saldos.group[$data][$id_nacional][$agencia][$conta])  $saldo_valor = number_format(parseFloat($conciliacao.data_ext.saldos.group[$data][$id_nacional][$agencia][$conta].saldo_valor), 2, ',', '.');

            }
            else {

                $msg += "O banco relacionado ao projeto "+$conciliacao.data_ext.bancos[$id_projeto].nome+":<br/>";

                if($id_nacional == null) $msg += "<br/>Não possui código nacional cadastrato";
                if($agencia == null) $msg += "<br/>Não possui código de agência cadastrada";
                if($conta == null) $msg += "<br/>Não possui código de conta cadastrada";

            }  
            
        }
        else {
            
            if("data_ext.saldos" in $conciliacao) $saldo_valor =  number_format(parseFloat($conciliacao.data_ext.saldos.general.saldo_valor), 2, ',', '.');

        }     
        
        $('#valor_banco_saldo').val($saldo_valor);

        
    }

    $("body").on('click', ".acertoViagem", function () {
        var id = $(this).data("key");

        $.post("/intranet/finan/actions/action_viagem.php", {bugger: Math.random(), action: 'formAcerto', id: id}, function (resultado) {
            html = $(resultado);
            html.find('.valores').maskMoney({prefix: '', allowNegative: true, thousands: '.', decimal: ','});
            new BootstrapDialog({
                size: BootstrapDialog.SIZE_WIDE,
                nl2br: false,
                type: 'type-info',
                title: 'Acerto de Viagem',
                message: html,
                closable: false,
//                onshow: function(dialog) { somaValor(); },
                buttons: [
                    {
                        label: 'Finalizar Acerto',
                        action: function (dialog) {
//                            somaValor();
                            $('#cadAcerto').trigger('click');
                        }
                    },
                    {
                        label: 'Fechar',
                        action: function (dialog) {
                            dialog.close();
                            i = 0;
                        }
                    }
                ]
            }).open();
        }, 'html');
    });

    $('body').on('keyup', '.valorT', function () {
        somaValor();
    });


    $("body").on('click', ".RecusarViagem", function () {
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("/intranet/finan/actions/action_viagem.php", {bugger: Math.random(), action: 'recusar', id: id}, function (resultado) {
//            console.log(resultado);
            bootAlert(
                    'Viagem Recusada',
                    'Recusada',
                    function (dialog) {
//                    console.log(resultado);
                        window.location.reload();
                    },
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    $("body").on('click', "#gravarReembolsoSaida", function () {
//        var idReembolso = $(this).data("idreembolso");
        var dados = $('#formReembolso').serialize();
//        console.log(dados); return false;
        cria_carregando_modal();
        $.post("/intranet/finan/actions/action.saida.php", dados, function (resultado) {
//            console.log(resultado);
            bootDialog(
                    resultado,
                    'Cadastro de Saída',
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                window.location.reload();
                            }
                        }],
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    $("body").on('click', '.recusarReembolso', function () {
        var idReembolso = $(this).data("key");
        $.post("actions/action_reembolso.php", {bugger: Math.random(), action: 'recusar_reembolso', reembolso: idReembolso}, function (resultado) {
            bootDialog(
                    resultado,
                    "RECUSA DE Reembolso!",
                    [{
                            label: 'Fechar',
                            action: function () {
                                $('.modal').modal('hide');
                                //$('.reembolso'+idReembolso).remove();
                                window.location.reload();
                            }
                        }],
                    'danger'
                    );
        });
    });

    $("body").on('click', '.liberarReembolso', function () {
        $(this).parent().parent().parent().parent().toggle();
        $(".formLiberarReembolso").toggle();
    });

    $('body').on("change", '#projeto', function () {
        var projeto = $(this).val();
        var banco = $("#bancoSel").val();
        $.post("actions/action_reembolso.php", {bugger: Math.random(), action: 'load_bancos', projeto: projeto, banco: banco}, function (resultado) {
            $('#banco').html(resultado);
        });
    });

    $('body').on("change", '#tipo_servico', function () {
        $this = $(this);
        cria_carregando_modal();
        var projeto = $this.val();
        var banco = $("#bancoSel").val();
        var $saidas = $('.saidas_check').serializeArray();
        var $operacao = 'C';
        var $servico = $this.val();
        var $id_nacional = $this.closest('.panel-banco').find('.load_table_financeiro').data('id_nacional');

        $.post('/intranet/',
                {
                    class: "financeiro/cnab240/remessa",
                    method: "getOptForma",
                    id_nacional: $id_nacional,
                    operacao: $operacao,
                    servico: $servico
                },
                function ($data) {

                    remove_carregando_modal();
                    $this.closest('.panel-banco').find('.tipo_forma').html("<span class='col-sm-2 col-md-2 col-lg-2' style='padding:8px;'>Forma:</span>");
                    $this.closest('.panel-banco').find('.tipo_forma').append($data);


                });

    });

    $('body').on("change", '#tipo_forma', function () {
        $this = $(this);
        $this.closest('.panel-banco').find('#gerar_remessa').removeAttr('disabled');

    });

    /*ENTRADAS E SAIDAS*/
//    var id_banco_salvo = '';
//    $("body").on('click', ".load_table_financeiro, .reload", function(){ 
//        var id_regiao = $(this).data("regiao");
//        if($(this).data("key")) { 
//            id_banco = id_banco_salvo = $(this).data("key");
//        } else { 
//            id_banco = id_banco_salvo;
//        }
////        console.log("A", id_banco, id_banco_salvo); return false;
//        var reload = $(this).data("reload");
//        if($('#'+id_banco).html() == '' || reload == 'reload'){
//            console.log($('#filtroDataFim').val(), $('#filtroDataIni').val())
//            cria_carregando_modal();
//            $.post("actions/action_finan.php", {bugger:Math.random(), id_regiao:id_regiao, id_banco:id_banco, action:'load_table_financeiro', data_ini: $('#filtroDataIni').val(), data_fim: $('#filtroDataFim').val(), tipo: $('#filtroTipo').val() }, function(resultado){
//                $('#'+id_banco).html(resultado);
//                $('.loaded'+id_banco).addClass("fa fa-check");
//                $('.reload'+id_banco).addClass("fa fa-refresh");
//                $("[data-toggle='tooltip']").tooltip(); 
//                remove_carregando_modal();
//                if(reload == 'reload'){
//                    $('#'+id_banco).addClass('in').css('height','auto');
//                }
//            });
//        }
//    });   

    $("body").on('click', ".linkpagina", function () {
        var pagDestino = $(this).data("pagdestino");
        var id_regiao = $(this).data("regiao");
        var reload = $(this).data("reload");
        cria_carregando_modal();
        $.post("actions/action_finan.php", {pagDestino: pagDestino, bugger: Math.random(), id_regiao: id_regiao, id_banco: id_banco, action: 'load_table_financeiro'}, function (resultado) {
            $('#' + id_banco).html(resultado);
            console.log(id_banco);
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".imprimirNota", function () {
        $(this).addClass('btn-success');
    });

    $("body").on('click', ".duplicarSaida", function () {
        var id = $(this).data('key');
        cria_carregando_modal();
        $.post("actions/action.saida.php", {bugger: Math.random(), id: id, action: 'form_duplicar'}, function (resultado) {
            new BootstrapDialog({
                nl2br: false,
                size: 'size-wide',
                title: "<strong>Duplicar</strong> Saída " + id,
                message: resultado,
                closable: false,
                type: 'type-warning',
                buttons: [{
                        label: 'Cancelar',
                        action: function (dialog) {
                            dialog.close();
                        }
                    }, {
                        label: 'CONFIRMAR',
                        cssClass: 'btn-warning',
                        action: function (dialog) {
                            var dados = $('#form_duplicar').serialize();
                            cria_carregando_modal();
                            $.post("actions/action.saida.php", dados, function (resposta) {
                                bootAlert('Saída Duplicada!', 'Saída Duplicada!', function () {
                                    dialog.close();
                                    window.location.reload();
                                }, 'success');
                                remove_carregando_modal();
                            });
                        }
                    }]
            }).open();
//            bootConfirm(
//                resultado, 
//                "<strong>Duplicar</strong> Saída "+id,
//                function(dialog){
//                    if(dialog == true){
//                        var dados = $('#form_duplicar').serialize();
//                        cria_carregando_modal();
//                        $.post("actions/action.saida.php", dados, function(resposta){
//                            bootAlert('Saída Duplicada!', 'Saída Duplicada!', null, 'success');
//                            remove_carregando_modal();
//                        });
//                    }
//                },
//                'warning'
//            );
            remove_carregando_modal();
        });
    });
    
    
    $('.checkbox-conciliacao-todos').click(function(e){
        
        var table= $(e.target).closest('table');
        
        $('td input:checkbox',table).prop('checked',this.checked);
        
    });    
    
    $('.checkbox-inclusao-todos').click(function(e){
        
        var table= $(e.target).closest('table');
        
        $('td input:checkbox',table).prop('checked',this.checked);
        
    });     
    
    $("body").on('click', '#tab-conciliacao-completa', function () {
       
        $("#controle-saldo").css("visibility", "hidden");
        
        $("#incluir-registros").css("visibility", "hidden");
        
        $("#conciliar-registros").css("visibility", "hidden");
        
        $("#conciliar-registros").data("tab_active","tab-conciliacao-completa");

    });    

    $("body").on('click', '#tab-conciliacao-parcial', function () {
        
        $("#controle-saldo").css("visibility", "hidden");
        
        $("#incluir-registros").css("visibility", "hidden");
        
        $("#conciliar-registros").css("visibility", "");
        
        $("#conciliar-registros").data("tab_active","tab-conciliacao-parcial");
        
        $("#conciliar-registros").html('<i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;Conciliar Visualmente&nbsp;');

    });    
    
    $("body").on('click', '#tab-conciliacao-inexistente-sistema', function () {
        
        $("#controle-saldo").css("visibility", "hidden");
        
        $("#incluir-registros").css("visibility", "hidden");
        
        $("#conciliar-registros").css("visibility", "hidden");
        
        $("#conciliar-registros").data("tab_active","tab-conciliacao-inexistente-sistema");

    });    

    $("body").on('click', '#tab-conciliacao-inexistente-banco', function () {
        
        $("#controle-saldo").css("visibility", "");
        
        $("#incluir-registros").css("visibility", "");
        
        $("#conciliar-registros").css("visibility", "");
        
        $("#conciliar-registros").data("tab_active","tab-conciliacao-inexistente-banco");
        
        $("#conciliar-registros").html('<i class="fa fa-handshake-o" aria-hidden="true"></i>&nbsp;Encontrar Conciliações&nbsp;');

    });    
    
    $('#tab-conciliacao-inexistente-banco').trigger('click');    
    
    /**
     * Evento para incluir os movimentos de créditos
     * 
     * @access public
     * @method incluir-registros
     * @param
     * 
     * @return JSON;
     */
    $('body').on('click', '#incluir-registros', function ($e) {
        
        var data_array = new Array();
        var $url = '/intranet/';
        var $status = 1;

        $e.preventDefault();
        
        $('#i_conciliacao').addClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
        
        $(".checkbox-inclusao:checked").each(function(){

            var $item = {};
            
            $item['empresa_inscricao_tipo'] = $(this).data('empresa_inscricao_tipo');
            $item['empresa_inscricao_numero'] = $(this).data('empresa_inscricao_numero');
            $item['lancamento_id'] = $(this).data('lancamento_id');
            $item['lancamento_documento'] = $(this).data('lancamento_documento');
            $item['lancamento_historico'] = $(this).data('lancamento_historico');
            $item['lancamento_data'] = $(this).data('lancamento_data');
            $item['lancamento_valor'] = $(this).data('lancamento_valor');
            
            data_array.push($item);

        });
        
        
        if(data_array.length==0) {
            
            $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
            
            bootAlert('Nenhum registro selecionado, você precisa definir pelo menos um para inclusão', 'AVISO', null, 'warning');
            
            return 0;
            
        }    
        
        bootConfirm(
                "Confirma a inclusão de "+data_array.length+" registro(s) em entradas/saídas",
                "ATENÇÃO",
                function (dialog) {
                    
                    if (dialog == true) {
                        
                        var $serialized = JSON.stringify(data_array);     
                        var $ok = 1;

                        cria_carregando_modal();
        
                        $.post(
                            $url,
                            {
                                class: "financeiro/cnab240/remessa",
                                method: "incluirEntradasSaidas",
                                serialized: $serialized
                            },
                            function ($response) {
                                

                            }
                        , "json")
                        .done(function ($response) {


                        })
                        .fail(function ($response) {
                            
                            $ok = 0;
                            
                        })
                        .always(function ($response) {

                            var $msg = '';

                            //$('#conciliacao-filtro').trigger('click');        

                            $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
                            
                            $('#table-inexistente-banco').find('.linhas').remove();
                            
                            $('.checkbox-inclusao-todos').prop('checked',false);
                            
                            remove_carregando_modal();   
                            
                            if($ok) {
                                
                                $.each($response.data,function($k, $v) {

                                    $msg += '<br>' + $v.message;

                                });          

                            }
                            else {
                                
                                $msg = 'Ouve um erro no processamento de inclusão das conciliações'
                                
                            }
                            
                            bootAlert($msg, 'AVISO', null, (($response.status) ? 'success' : 'warning'));


                        }); 
                
                    }
                    else {
                        
                        $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');

                    }
                },
                'warning'
                );        
        
    }); 
    
    /**
     * Evento para marcar os registros conciliados manualmente
     * 
     * @access public
     * @method conciliacao-registros
     * @param
     * 
     * @return JSON;
     */
    $('body').on('click', '#conciliar-registros', function ($e) {
        
        $e.preventDefault();
        
        $('#i_conciliacao').addClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
        
        cria_carregando_modal();        
        
        var $url = '/intranet/';
        var $data_fmt = $("#data-conciliacao").val(); 
        var $data = new String($data_fmt);

        $data = $data.replace("/", "").replace("/", "");        

        var $dia = $data.substr(0, 2);
        var $mes = $data.substr(2, 2);
        var $ano = $data.substr(4, 4);

        var $data = $ano+'-'+$mes+'-'+$dia;   
        
        switch($(this).data('tab_active')){
            case 'tab-conciliacao-parcial':  
                
                var data_array = new Array();
                
                $(".checkbox-conciliacao-parcial:checked").each(function(){

                    var $item = {};
                    
                    $item['id_conciliacao'] = $(this).data('id_conciliacao');
                    $item['id_entrada_saida'] = $(this).data('id_entrada_saida');

                    data_array.push($item);

                });
                
                if(data_array.length==0) {

                    remove_carregando_modal();       

                    $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');

                    bootAlert('Nenhum registro selecionado, você precisa definir pelo menos um para a conciliação', 'AVISO', null, 'warning');

                    return 0;

                }                 
                
                var $serialized = JSON.stringify(data_array);     
                
                $.post(
                    $url,
                    {
                        class: "financeiro/cnab240/remessa",
                        method: "processarConciliacaoVisual",
                        serialized: $serialized
                    },
                    function ($response) {
                        
                        if($response.status) $('#conciliacao-filtro').trigger('click'); 

                        $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
                        
                        var $msg = '';
                        
                        $.each($response.data_ext,function($k, $v) {

                            $('#linha-'+$k).addClass('warning');

                        });                         

                        $.each($response.data,function($k, $v) {

                            $msg += '<br>' + $v.message;

                        });  

                        bootAlert($msg, 'AVISO', null, ($response.status ? 'success' : 'warning'));
                        
                        remove_carregando_modal();                            
                        
                    }
                , "json")
                .done(function ($data) {


                })
                .fail(function ($data) {

                    $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');

                    bootAlert('Não foi possível executar a rotina de conciliação visual', 'ATENÇÃO', null, 'warning');

                    remove_carregando_modal();                            
                    
                })
                .always(function () {

                });
            
                break;
            case 'tab-conciliacao-inexistente-banco':  
            
                    $.post(
                        $url,
                        {
                            class: "financeiro/cnab240/remessa",
                            method: "conciliar",
                            data: $data
                        },
                        function ($response) {
                            
                            console.log($response);
                            
                            if($response.status) {
                                
                                $('#conciliacao-filtro').trigger('click');
                                
                                var $msg = ''; 
                                
                                $.each($response.data, function($key, $value) {                                
                                    
                                    $msg += $value.message;
                                    
                                });
                                
                                bootAlert($msg, 'SUCESSO', null, 'info');
                                
                            }
                            else {
                                
                                bootAlert('Erro no processamento da conciliação', 'ATENÇÃO', null, 'warning');
                                
                            }
                            

                        }
                    , "json")
                    .done(function ($data) {

                    })
                    .fail(function ($data) {
                        
                        bootAlert('Ouve um erro no processamento', 'ATENÇÃO', null, 'warning');

                    })
                    .always(function () {
                        
                        $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
                
                        remove_carregando_modal();

                    });
                
                break;
            default:
                
                $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
                
                remove_carregando_modal();                
            
        
        }
        
    }); 
    
//    var $table_conciliacao = $("#xxxxx").DataTable( {
//        "createdRow": function ( row, data, index ) {
//            if($(row).find('.type').html() == 'entrada'){
//                $(row).addClass('info');
//            } else if($(row).find('.type').html() == 'remessa'){
//                $(row).addClass('warning');
//            }
//        },
//        ajax: {
//            url: "carrega_saidas.php",
//            type: "post",
//            data: function(d){
//                d.id_banco = '<?php echo $value ?>',
//                d.data_ini = $('#filtroDataIni').val(),
//                d.data_fim = $('#filtroDataFim').val(),
//                d.id_projeto = $('#id_projeto').val()
//            },
//            dataType: "json"
//        },
//        "responsive": true,
//        order: [[ 6, "asc" ]],
//        aoColumns: [
//            { "bSortable": false },
//            { "bSortable": false },
//            { "bSortable": false },
//            null,
//            null,
//            null,
//            { "sType": "date-uk" },
//            { "sType": "currency" },
//            { "bSortable": false },
//            { "bSortable": false },
//            { "bSortable": false }
//        ], 
//        columnDefs: [
//            { className: "text-left", targets: [4]},
//            { className: "text-right", targets: [7]},
//            { className: "text-center", targets: "_all"}
//        ],
//        "lengthMenu": [ [10, 25, 50, 75, 100, 500, -1], [10, 25, 50, 75, 100, 500, "Todos"] ],
//        "fnDrawCallback": function(){
//            $("[data-toggle='tooltip']").tooltip();
//        },
//        language: {
//            "decimal":        ",",
//            "emptyTable":     "Nenhuma informação encontrada",
//            "info":           "Mostrando _START_ à _END_ de _TOTAL_",
//            "infoEmpty":      "Mostrando 0 à 0 de 0",
//            "infoFiltered":   "(filtrando de _MAX_)",
//            "infoPostFix":    "",
//            "thousands":      ".",
//            "lengthMenu":     "Mostrar _MENU_ resultados",
//            "loadingRecords": "Carregando...",
//            "processing":     "Processando...",
//            "search":         "Procurar:",
//            "zeroRecords":    "Nenhum resultado encontrado",
//            "paginate": {
//                "first":      "Primeiro",
//                "last":       "Último",
//                "next":       "Próximo",
//                "previous":   "Anterior"
//            },
//            "aria": {
//                "sortAscending":  ": activate to sort column ascending",
//                "sortDescending": ": activate to sort column descending"
//            }
//        }
//    });

    /**
     * O evento no objeto projeto permite a exibição apenas dos registros definidos no projeto
     * 
     * @access public
     * @event #projeto
     * @param
     * 
     * @return JSON;
     */
    $('body').on('change', '#projeto', function ($e) {
        
        var $id_projeto = 0;
        var $projeto_sel = parseInt($('#projeto option:selected').val());
        var $table = {};
        
        $table['table-completa'] = $("#table-completa").find('.linhas');
        $table['table-parcial'] = $("#table-parcial").find('.linhas');
        $table['table-inexistente-sistema'] = $("#table-inexistente-sistema").find('.linhas');
        $table['table-inexistente-banco'] = $("#table-inexistente-banco").find('.linhas');
        
        $.each($table, function($key, $value) {
            
            console.log($value);

            $.each($value, function($k, $v) {

                $id_projeto = $($v).data('id_projeto');

                if($projeto_sel == $id_projeto ||  $projeto_sel==0 ) {

                    $($v).removeClass('hide_rows');

                }
                else {

                    $($v).addClass('hide_rows');

                }

            });    
            
        });
        
        atualizaTotais();

    }); 
    
    /**
     * Evento para executar a listagem das conciliações
     * 
     * @access public
     * @event conciliacao-filtro
     * @param
     * 
     * @return JSON;
     */
    $('body').on('click', '#conciliacao-filtro', function ($e) {
        
//        $table_conciliacao.destroy();
//        $table_conciliacao.ajax.reload();

        var $url = '/intranet/?'+Math.floor(Math.random() * 1000);
        var $data_fmt = $("#data-conciliacao").val(); 
        var $data = new String($data_fmt);
        var $msg = '';

        $data = $data.replace("/", "").replace("/", "");

        var $dia = $data.substr(0, 2);
        var $mes = $data.substr(2, 2);
        var $ano = $data.substr(4, 4);

        var $data = $ano+'-'+$mes+'-'+$dia;    
        
        $('.checkbox-inclusao').prop('checked',false);
        
        $('#i_conciliacao').addClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
        
        $.LoadingOverlay("show", {
                            fade: [2000, 1000]
                        });

        $('.table-conciliacao').find('.linhas').remove();
        
        $.post($url,
            {
            class: "financeiro/cnab240/remessa",
            method: "saldo",
            saldo_data: $data
            }
        ,
        function ($response) {
            
        }
        , "json")   
        .done(function ($response) {
            
            console.log('.done');
        
            $conciliacao = $response;
            
            if(!$response.status) {
                
                $.each($response.data, function (i, item) {

                     $msg += '<br>' + (i + 1) + ' - ' + item.message;

                });

            }
            
    
        })
        .fail(function ($response) {
            
            console.log('.fail');
            
        })
        .always(function () {

            console.log('.always');
    
        });        
        
        $.post($url,
            {
            class: "financeiro/cnab240/remessa",
            method: "listaConciliacao",
            data: $data
            }
        ,
        function (data) {

        }
        , "json")        
        .done(function($response) {  
            
            var $class = '';
            var $id_projeto = parseInt($('#projeto option:selected').val());
            
            if(!jQuery.isEmptyObject($response)) {
                
                $.each($response.data, function($k, $v) {

                    $class = ($id_projeto == $v.id_projeto ||  $id_projeto==0 ? '' : ' hide_rows ');
                    
                    switch($v.flag_conciliacao){
                        case '0':  
                            $("#table-inexistente-sistema").append('<tr role="row" class="odd linhas '+$class+'" data-id_projeto="'+$v.id_projeto+'"><td class="text-right">'+$v.id_entrada_saida+'</td><td>'+$v.nome+'</td><td>'+$v.n_documento+'</td><td>'+$v.especifica+'</td><td class="text-center">'+$v.data_pagamento_fmt+'</td><td class="text-right currency '+($v.lancamento_tipo=='C'?'':'text-danger text-negative')+'">'+number_format($v.valor, 2, ',', '.')+'</td><td class="text-center">'+($v.id_entrada_saida_pai > 0 ? '<i class="fa fa-clone"></i>' : '')+'</td></tr>');
                            break;
                        case '1': 
                            $("#table-completa").append('<tr role="row" class="odd linhas '+$class+'" data-id_projeto="'+$v.id_projeto+'"><td class="text-right">'+$v.id_entrada_saida+'</td><td>'+$v.nome+'</td><td>'+$v.n_documento+'</td><td>'+$v.especifica+'</td><td class="text-center">'+$v.data_pagamento_fmt+'</td><td class="text-right currency '+($v.lancamento_tipo=='C'?'':'text-danger text-negative')+'">'+number_format($v.valor, 2, ',', '.')+'</td><td class="text-center">'+($v.id_entrada_saida_pai > 0 ? '<i class="fa fa-clone"></i>' : '')+'</td></tr>');
                            break;
                        case '2': 
                        case '3': 
                        case '4': 
                        case '5': 
                        case '6': 
                        case '7': 
                        case '8': 
                        case '9': 
                            $("#table-parcial").append('<tr id="linha-'+$v.id_entrada_saida+'" role="row" class="odd linhas '+$class+'" data-id_projeto="'+$v.id_projeto+'"><td class="text-center"><input type="checkbox" class="checkbox-conciliacao-parcial" data-id_entrada_saida="'+$v.id_entrada_saida+'" data-id_conciliacao="'+$v.lancamento_id+'"/></td><td class="text-right">'+$v.id_entrada_saida+'</td><td class="text-right">'+$v.n_documento+'</td><td>'+$v.nome+'</td><td>'+$v.especifica+'</td><td class="text-center">'+$v.data_pagamento_fmt+'</td><td class="text-right currency '+($v.lancamento_tipo=='C'?'':'text-danger text-negative')+'">'+number_format($v.valor, 2, ',', '.')+'</td><td class="text-right">'+$v.lancamento_id+'</td><td class="text-right">'+$v.lancamento_documento+'</td><td colspan="2">'+$v.lancamento_historico+'</td><td class="text-center">'+$v.lancamento_data_fmt+'</td><td class="text-right currency '+($v.lancamento_tipo=='C'?'':'text-danger text-negative')+'">'+number_format($v.lancamento_valor, 2, ',', '.')+'</td><td class="text-center">'+($v.id_entrada_saida_pai > 0 ? '<i class="fa fa-clone"></i>' : '')+'</td></tr>');
                            break;
                        case '99': 
                            $("#table-inexistente-banco").append('<tr role="row" class="odd linhas '+$class+($v.lancamento_tipo=='C'?' valor_banco_credito ':' valor_banco_debito ')+'" data-id_projeto="'+$v.id_projeto+'" data-lancamento_valor="'+$v.lancamento_valor+'"><td class="text-center">'+($.inArray($.trim($v.lancamento_historico),$tipos) >= 0?'<input type="checkbox" class="checkbox-inclusao" data-lancamento_id="'+$v.lancamento_id+'" data-empresa_inscricao_tipo="'+$v.empresa_inscricao_tipo+'" data-empresa_inscricao_numero="'+$v.empresa_inscricao_numero+'" data-lancamento_documento="'+$v.lancamento_documento+'" data-lancamento_historico="'+$v.lancamento_historico+'" data-lancamento_data="'+$v.lancamento_data+'" data-lancamento_valor="'+$v.lancamento_valor+'"/>':'')+'</td><td class="text-right">'+$v.lancamento_id+'</td><td>'+$v.lancamento_documento+'</td><td colspan="2">'+$v.lancamento_historico+'</td><td class="text-center">'+$v.lancamento_data_fmt+'</td><td class="text-right currency  '+($v.lancamento_tipo=='C'?'':'text-danger text-negative')+'">'+number_format($v.lancamento_valor, 2, ',', '.')+'</td></tr>');
                            break;
                    }

                });
                
            }
            
        })
        .fail(function (data) {
            
            
        })
        .always(function () {
            
            atualizaTotais();
            
            $('#i_conciliacao').removeClass('abaLoading fa fa-spinner fa-pulse fa-3x fa-fw text-sm');
    
            $.LoadingOverlay('hide');
            
            if($msg.trim()) bootAlert($msg, 'AVISO', null, 'warning');                

        });

    });
    

    /**
     * Evento para gerar arquivo de remessa eletrônica 
     * 
     * @access public
     * @method gerar_remessa
     * @param
     * 
     * @return JSON;
     */
    $('body').on('click', '#gerar_remessa', function ($e) {

        $e.preventDefault();

        var $msgS = '', $msg = '';
        $('.saidas_check:checked').each(function () {
            $msgS += '<tr class="valign-middle"><td style="width:10%;">' + $(this).data('id') + '</td><td style="width:70%;">' + $(this).data('nome') + '</td><td style="width:20%;">' + $(this).data('valor') + '</td>';
        });

        if ($msgS != '') {

            $msg = "<table class='table table-bordered table-condensed text-sm'>";
            if ($msgS != '') {
                $msg += "<tr class='valign-middle'><td colspan='3' class='success'>Saidas</td></tr>" + $msgS;
            }
            $msg += "</table>";

            var $saidas = $(this).closest('.panel-banco').find('.saidas_check').serializeArray();
            var $forma = $(this).closest('.panel-banco').find('.tipo_forma option:selected').text();
            var $tipo_servico = $(this).closest('.panel-banco').find('#tipo_servico').val();
            var $tipo_forma = $(this).closest('.panel-banco').find('#tipo_forma').val();
            var $id_banco = $(this).closest('.panel-banco').find('.load_table_financeiro').data('key');
            var $id_nacional = $(this).closest('.panel-banco').find('.load_table_financeiro').data('id_nacional');
            var $agencia = $(this).closest('.panel-banco').find('.load_table_financeiro').data('agencia');
            var $conta = $(this).closest('.panel-banco').find('.load_table_financeiro').data('conta');
            var $operacao = 'C';

            console.log($forma, $tipo_servico, $tipo_forma, $id_banco, $id_nacional, $agencia, $conta, $saidas);

            BootstrapDialog.show({
                type: BootstrapDialog.TYPE_INFO,
                title: "REMESSA ELETRÔNICA para " + $forma + " da Ag.: " + $agencia + " / CC: " + $conta,
                message: $('<div id="modal"></div>').html("<div id='mensagem'>" + $msg + "</div>"),
                closable: false,
                buttons: [{
                        label: "Gerar Arquivo e Baixar",
                        cssClass: "btn-primary",
                        hotkey: 13,
                        autospin: true,
                        action: function (dialog) {

                            dialog.enableButtons(false);
                            dialog.setClosable(false);
                            dialog.getModalBody().html("");

                            setTimeout(function () {

                                $.post(
                                    '/intranet/',
                                    {
                                        class: "financeiro/cnab240/remessa",
                                        method: "gerar",
                                        id_banco: $id_banco,
                                        id_nacional: $id_nacional,
                                        saidas: $saidas,
                                        agencia: $agencia,
                                        conta: $conta,
                                        tipo_servico: $tipo_servico,
                                        tipo_forma: $tipo_forma,
                                        operacao: $operacao
                                    },
                                    function (data) {

                                        console.log(data);

                                        var $msg = '';

                                        if (data.status) {

                                            $.each($saidas, function (indexS, valueS) {

                                                $(".saida" + valueS['value']).remove();

                                            });

                                            saveToDisk(data.url, data.file);

                                        } else {

                                            $.each(data.data, function (i, item) {

                                                $msg += '<br>' + (i + 1) + ' - ' + item.message;

                                            });

                                            bootAlert($msg, 'Error', null, 'warning');

                                        }

                                        dialog.close();

                                    }
                                    , "json")
                                    .done(function () {
                                    })
                                    .fail(function (data) {

                                        dialog.close();

                                        bootAlert('Erro na geração do arquivo', 'Error', null, 'warning');

                                    })
                                    .always(function () {
                                });

                            }, 1000);
                        }
                    },
                    {
                        label: "Fechar",
                        cssClass: "btn-primary",
                        action: function (dialog) {
                            dialog.close();
                        }
                    }]

            });


        } else {
            bootAlert("Selecione pelo menos um registro", 'Gerar Remessa!', null, 'warning');
        }
    });
    
    /**
     * Método para processar arquivo de retorno de remessa
     * 
     * @access public
     * @method processar_retorno
     * @param
     * 
     * @return JSON;
     */
    $("body").on('click', "#processar_retorno", function () {
        
        var id = $(this).data("key");
        
        $("#myModalAnexo").modal("show");
        
        
    });       

    /**
     * Método utilizado para exibir as ocorrências de um determinado registro
     * 
     * @access public
     * @method exibirOcorrencias
     * @param
     * 
     * @return JSON;
     */
    $("body").on('click', ".showOcorrencias", function () {

        var id = $(this).data("key");

        cria_carregando_modal();

        $.post(
                '/intranet/',
                {
                    class: "financeiro/cnab240/remessa",
                    method: "showOcorrencias",
                    saidas: id
                },
                function (data) {

                    alert();
                    //console.info(data);

                    var msg = '';

                    if (data.status) {

                        $.each(data['ocorrencias']['dados'], function (key_campo, value_campo) {

                            console.log(value_campo);

                            $.each(value_campo, function (key_codigo, value_itens) {

                                console.log(value_itens);

                                msg += '<tr class="valign-middle"><td>' + value_itens['arquivo'] + '</td><td>' + value_itens['campo'] + '</td><td>' + value_itens['codigo'] + '</td><td>' + value_itens['descricao'] + '</td></tr>';

                            });
                        });

                        bootAlert('<table class="table table-bordered table-condensed text-sm"><tr><td>ARQUIVO</td><td>CAMPO</td><td>CÓDIGO</td><td>DESCRIÇÃO</td></tr>' + msg + '</table>', 'Ocorrências Geradas para Saída Cód. ' + id, null, 'primary');

                    }



                }
        , "json");

        remove_carregando_modal();

    });

    $('body').on('click', '#print_all', function () {
        console.log("A");
        $('#form').submit();
//        var idSaida = $('.saidas_check').serializeArray();
//        $.post("solicitacao_pagamento.php", {bugger:Math.random(), id:idSaida }, function(resultado){
//            
//        });
    });

    $('body').on('click', '#group_all', function () {
        var checkbox = $('.saidas_check:checked');
        if (checkbox.length > 1) {

            var html = "<div><form action=\"\" class=\"form-horizontal\"><div class=\"form-group\"> " +
                    "<label for=\"tipo\" class=\"col-sm-4 control-label\">Descrição:</label>" +
                    "<div class=\"col-sm-8\"> <input type=\"text\" id=\"desc_group\" class=\"form-control\" /> " +
                    "</div> </div>";
            html += "<div class=\"form-group\"> " +
                    "<label for=\"tipo\" class=\"col-sm-4 control-label\">Data Vencimento:</label>" +
                    "<div class=\"col-sm-5\"> <input type=\"text\" id=\"data_group\" class=\"form-control\" /> " +
                    "</div></div></form></div>";

            bootConfirm(
                    html,
                    "Agrupamento",
                    function (dialog) {
                        if (dialog == true) {
                            var descricao = $("#desc_group").val();
                            var data_grou = $("#data_group").val();

                            var idSaida = $('.saidas_check').serializeArray();
                            $.post("/intranet/finan/actions/action_agrupamento.php", {bugger: Math.random(), id: idSaida, desc: descricao, vencimento: data_grou, method: 'agrupar'}, function (resultado) {
                                //alert(resultado); return false;
                                var idAgrupamento= resultado;
                                $("#recebeparaprint").val(resultado);
                                //alert(teste); return false;
                                if(idAgrupamento){
                                    
 bootConfirm( "Gostaria de Imprimir relatorio deste agrupamento ?",
                    "Imprimir agrupamento:",
                    function (dialog) {
                        if (dialog == true) {
                           //window.location("relatorio_agrupamento.php");
                            //window.location.href ='relatorio_agrupamento.php?id_agrupamento='+idAgrupamento ;
                            window.open ('relatorio_agrupamento.php?id_agrupamento='+idAgrupamento,'_blank');
                        }
                    },
                    'warning'
                    );
                                }
                                
                                
                                if (resultado.status == "0") {
                                    bootAlert(resultado.msg, "Atenção", null, "danger");
                                } else {
                                    bootAlert("Pagamentos agrupados com sucesso", "Sucesso", function () {
                                       // location.reload();
                                    }, "success");
                                }
                            }, 'json');
                        }
                    },
                    'warning'
                    );

            $("body").on("blur", "#desc_group", function () {
                $("#data_group").mask("99/99/9999", {placeholder: " "}).datepicker();
            });
            $("body").on("click", "#data_group", function () {
                $("#data_group").mask("99/99/9999", {placeholder: " "}).datepicker();
            });


        } else {
            bootAlert("Selecione mais de uma saída por favor!", "Atenção", null, "danger");
        }
    });

    $('body').on('click', '#Pagar_all', function () {
        var msgS = '', msgE = '', msg = '';
        $('.saidas_check:checked').each(function () {
            msgS += '<tr class="valign-middle"><td style="width:10%;">' + $(this).data('id') + '</td><td style="width:70%;">' + $(this).data('nome') + '</td><td style="width:20%;">' + $(this).data('valor') + '</td>';
        });
        $('.entradas_check:checked').each(function () {
            msgE += '<tr class="valign-middle"><td style="width:10%;">' + $(this).data('id') + '</td><td style="width:70%;">' + $(this).data('nome') + '</td><td style="width:20%;">' + $(this).data('valor') + '</td>';
        });

        if (msgS != '' || msgE != '') {
            msg = "<table class='table table-bordered table-condensed text-sm'>";
            if (msgS != '') {
                msg += "<tr class='valign-middle'><td colspan='3' class='success'>Saidas</td></tr>" + msgS;
            }
            if (msgE != '') {
                msg += "<tr class='valign-middle'><td colspan='3' class='info'>Entradas</td></tr>" + msgE;
            }
            msg += "<tr class='valign-middle'><td colspan='3'>Nova Data de Vencimento: <input type='text' id='new_date' class='data form-control'><label class='text-danger'>Deixe em branco para não alterar.</label></td></tr>";
            msg += "</table>";
            msg = $(msg);
            msg.find('.data').datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, yearRange: '2005:c+1'});
            bootConfirm(
                    msg,
                    "Você tem certeza que deseja <strong>PAGAR</strong> os selecionados:",
                    function (dialog) {
                        if (dialog == true) {
                            var idSaida = $('.saidas_check').serializeArray();
                            var idEntrada = $('.entradas_check').serializeArray();
                            if (msgS != '') {
                                $.post("actions/action.saida.php", {bugger: Math.random(), id: idSaida, action: 'pagar', new_date: $('#new_date').val()}, function (resultado) {
                                    bootAlert(resultado, 'Saida(s) Paga(s)!', null, 'success');
                                    //bootAlert('Saida(s) Paga(s)!', 'Saida(s) Paga(s)!', null, 'success');
                                    $.each(idSaida, function (indexS, valueS) {
                                        $(".saida" + valueS['value']).remove();
                                    });
                                });
                            }
                            if (msgE != '') {
                                $.post("actions/action_entrada.php", {bugger: Math.random(), id: idEntrada, action: 'pagar', new_date: $('#new_date').val()}, function (resultado) {
                                    bootAlert(resultado, 'Entrada(s) Paga(s)!', null, 'success');
                                    //bootAlert('Entrada(s) Paga(s)!', 'Entrada(s) Paga(s)!', null, 'success');
                                    $.each(idEntrada, function (indexE, valueE) {
                                        $(".entrada" + valueE['value']).remove();
                                    });
                                });
                            }
                        }
                    },
                    'warning'
                    );
        } else {
            bootAlert("Selecione pelo menos uma Saida/Entrada", 'Pagar Selecionados!', null, 'warning');
        }
    });

    $('body').on('click', "#Deletar_all", function () {
        var msgS = '', msgE = '', msg = '';
        $('.saidas_check:checked').each(function () {
            msgS += '<tr class="valign-middle"><td style="width:10%;">' + $(this).parent().next().next().text() + '</td><td style="width:70%;">' + $(this).parent().next().next().next().text() + '</td><td style="width:20%;">' + $(this).parent().next().next().next().next().next().text() + '</td>';
        });
        $('.entradas_check:checked').each(function () {
            msgE += '<tr class="valign-middle"><td style="width:10%;">' + $(this).parent().next().next().text() + '</td><td style="width:70%;">' + $(this).parent().next().next().next().text() + '</td><td style="width:20%;">' + $(this).parent().next().next().next().next().next().text() + '</td>';
        });

        if (msgS != '' || msgE != '') {
            msg = "<table class='table table-bordered table-condensed text-sm'>";
            if (msgS != '') {
                msg += "<tr class='valign-middle'><td colspan='3' class='success'>Saidas</td></tr>" + msgS;
            }
            if (msgE != '') {
                msg += "<tr class='valign-middle'><td colspan='3' class='info'>Entradas</td></tr>" + msgE;
            }
            msg += "</table>";
            bootConfirm(
                    msg,
                    "Você tem certeza que deseja <strong>DELETAR</strong> os selecionados:",
                    function (dialog) {
                        if (dialog == true) {
                            var idSaida = $('.saidas_check').serializeArray();
                            var idEntrada = $('.entradas_check').serializeArray();
                            if (msgS != '') {
                                $.post("actions/action.saida.php", {bugger: Math.random(), id: idSaida, action: 'deletar'}, function (resultado) {
//                                bootAlert(resultado, 'Saida(s) Deletada(s)!', null, 'success');
                                    $.each(idSaida, function (index, value) {
                                        $(".saida" + value['value']).remove();
                                    });
                                });
                            }
                            if (msgE != '') {
                                $.post("actions/action_entrada.php", {bugger: Math.random(), id: idEntrada, action: 'deletar'}, function (resultado) {
//                                bootAlert(resultado, 'Entrada(s) Deletada(s)!', null, 'success');
                                    $.each(idEntrada, function (index, value) {
                                        $(".entrada" + value['value']).remove();
                                    });
                                });
                                bootAlert('Saida(s)/Entrada(s) Deletada(s)!', 'Saida(s)/Entrada(s) Deletada(s)!', null, 'success');
                            }
                        }
                    },
                    'warning'
                    );
        } else {
            bootAlert("Selecione pelo menos uma Saida/Entrada", 'Deletar Selecionados!', null, 'warning');
        }
    });

    $("body").on('click', ".detalheSaida", function () {
        var id = $(this).data("id");
        var tipo = $(this).data("tipo");
        cria_carregando_modal();
        $.post("detalhes_saida.php", {bugger: Math.random(), id: id, tipo: tipo}, function (resultado) {
            bootAlert(resultado, 'Detalhe da ' + tipo, null, 'primary');
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".editar_entrada", function () {
        var id = $(this).data("id");
        var tipo = $(this).data("tipo");
        cria_carregando_modal();
        $.post("actions/editar_entrada.php", {bugger: Math.random(), id: id, tipo: tipo}, function (resultado) {
            bootDialog(
                    resultado,
                    'Editar Entrada',
                    [{
                            label: 'Cancelar',
                            cssClass: 'btn-danger',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }, {
                            label: 'Atualizar',
                            cssClass: 'btn-success',
                            action: function (dialog) {
                                var dados = $("#form_editar_entrada").serialize();
                                cria_carregando_modal();
                                $.post('actions/action_entrada.php',
                                        dados,
                                        function (retorno) {
                                            if (retorno == '1') {
                                                bootAlert('Erro ao atualizar a base de dados\n Por favor contate o setor de T.I', 'Erro!', null, 'danger');
                                            } else {
                                                bootAlert('Entrada Atualizada com Sucesso', 'Sucesso!', null, 'success');
                                            }
                                            remove_carregando_modal();
                                        }
                                );
                                dialog.close();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".editar_saida", function () {
        var id = $(this).data("key");
        var url = $(this).data("url");
        var action = $(this).data("action");
        cria_carregando_modal();
        $.post(url, {bugger: Math.random(), id: id, action: action}, function (resultado) {
            bootDialog(
                    resultado,
                    'Editar Saída',
                    [{
                            label: 'Cancelar',
                            cssClass: 'btn-danger',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }, {
                            label: 'Atualizar',
                            cssClass: 'btn-success',
                            action: function (dialog) {
                                var dados = $("#form_editar_saida_data").serialize();
                                cria_carregando_modal();
                                $.post('actions/action.saida.php',
                                        dados,
                                        function (retorno) {
                                            if (retorno == '1') {
                                                bootAlert('Saída Atualizada com Sucesso', 'Sucesso!', null, 'success');
                                            } else {
                                                bootAlert('Erro ao atualizar a base de dados\n Por favor contate o setor de T.I:' + retorno, 'Erro!', null, 'danger');
                                            }
                                            remove_carregando_modal();
                                        }
                                );
                                dialog.close();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".anexarEntrada", function () {
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action_entrada.php", {bugger: Math.random(), id: id, gerenciar_anexo: 'gerenciar_anexo'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Gerenciar Anexo Entrada ' + id,
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".anexarSaida", function () {
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action.saida.php", {bugger: Math.random(), id: id, action: 'verAnexos'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Ver Anexos Saida: ' + id,
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".pagarSaida", function () {
        var id = $(this).data("key");
        $('.saidas_check, .entradas_check').prop('checked', false);
        $(this).parent().parent().find('.saidas_check').prop('checked', true);
        $('#Pagar_all').trigger('click');
//        bootConfirm(
//            "Deseja <strong>Pagar</strong> a Saída "+id+"?",
//            'Pagar Saída', 
//            function(dialog){
//                if(dialog == true){
//                    cria_carregando_modal();
//                    $.post("actions/action.saida.php", {bugger:Math.random(), id:id, action:'pagar'}, function(resultado){
//                        bootAlert(resultado,'Saída Paga',null,'success');
//                        $('.saida'+id).remove();
//                        remove_carregando_modal();
//                    });
//                }
//            },
//            'warning'
//        );
    });

    $("body").on('click', ".deletarSaida", function () {
        var id = $(this).data("key");
        bootConfirm(
                "Deseja <strong>DELETAR</strong> a Saída " + id + "?",
                'Deletar Saída',
                function (dialog) {
                    if (dialog == true) {
                        cria_carregando_modal();
                        $.post("actions/action.saida.php", {bugger: Math.random(), id: id, action: 'deletar'}, function (resultado) {
                            bootAlert('Saida Deletada!', 'Saida Deletada!', null, 'success');
                            $('.saida' + id).remove();
                            remove_carregando_modal();
                        });
                    }
                },
                'warning'
                );
    });

    $("body").on('click', ".deletarEntrada", function () {
        var id = $(this).data("key");
        bootConfirm(
                "Deseja <strong>DELETAR</strong> a Entrada " + id + "?",
                'Deletar Entrada',
                function (dialog) {
                    if (dialog == true) {
                        cria_carregando_modal();
                        $.post("actions/action_entrada.php", {bugger: Math.random(), id: id, action: 'deletar'}, function (resultado) {
                            bootAlert(resultado/*'Entrada Deletada!'*/, 'Entrada Deletada!', null, 'success');
                            $('.entrada' + id).remove();
                            remove_carregando_modal();
                        });
                    }
                },
                'warning'
                );
    });

    $("body").on('click', ".verNotas", function () {
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action_entrada.php", {bugger: Math.random(), id: id, ver_notas: 'ver_notas'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Visualizar Notas: ' + id,
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".verComprovante", function () {
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action.saida.php", {bugger: Math.random(), id: id, action: 'verComprovante'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Visualizar Comprovante: ' + id,
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".pagarEntrada", function () {
        var id = $(this).data("key");
        $('.saidas_check, .entradas_check').prop('checked', false);
        $(this).parent().parent().find('.entradas_check').prop('checked', true);
        $('#Pagar_all').trigger('click');
//        bootConfirm(
//            "Deseja Pagar a Entrada "+id+"?",
//            'Pagar Entrada', 
//            function(dialog){
//                if(dialog == true){
//                    $.post("actions/action_entrada.php", {bugger:Math.random(), id:id, action:'pagar'}, function(resultado){
//                        bootAlert(resultado,'title',null,'success');
//                        $('.entrada'+id).remove();
//                    });
//                }
//            },
//            'warning'
//        );
    });

    $('body').on('click', ".deleteAnexoEntrada", function () {
        var idFileEntrada = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action_entrada.php", {bugger: Math.random(), id: idFileEntrada, deleteAnexoEntrada: 'deleteAnexoEntrada'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Exclusão de Anexo',
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                $('.' + idFileEntrada).remove();
                                dialog.close();
                            }
                        }],
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    $("body").on('click', ".deleteAnexoSaida", function () {
        var idFileSaida = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action.saida.php", {bugger: Math.random(), id: idFileSaida, action: 'deleteAnexoSaida'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Exclusão de Anexo',
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                $('.' + idFileSaida).remove();
                                dialog.close();
                            }
                        }],
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    $("body").on('click', ".deleteComprovanteSaida", function () {
        var idFileSaida = $(this).data("key");
        cria_carregando_modal();
        $.post("actions/action.saida.php", {bugger: Math.random(), id: idFileSaida, action: 'deleteComprovanteSaida'}, function (resultado) {
            bootDialog(
                    resultado,
                    'Exclusão de Comprovante',
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                $('.' + idFileSaida).remove();
                                dialog.close();
                            }
                        }],
                    'info'
                    );
            if (resultado) {
                remove_carregando_modal();
            }
        });
    });

    $('.abaLoading').remove();

    $('body').on('click', '.parcelarSaida', function () {
        var id = $(this).data("key");
        $.post("actions/action.saida.php", {bugger: Math.random(), id: id, action: 'getSaidaDados'}, function (resultado) {
            console.log(resultado);
            var formulario = $('<form>', {method: 'post', action: 'actions/action.saida.php?action=gerarParcelas', id: 'form_parcela'}).append(
                    $('<div>', {'class': 'row'}).append(
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Número de Parcelas'),
                    $('<input>', {'type': 'text', 'class': 'form-control', id: 'n_parcelas', name: 'n_parcelas'})
                    ),
                    ),
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Total Liquido'),
                    $('<div>', {'class': 'input-group'}).append(
                    $('<span>', {'class': 'input-group-addon'}).text('R$'),
                    $('<input>', {'type': 'text', 'class': 'form-control', id: 'valor_liquido', nome: 'valor_liquido', readonly: 'readonly'}).val(number_format(resultado.valor, 2, ',', '.')),
                    $('<input>', {'type': 'hidden', 'class': 'form-control', id: 'id_saida', name: 'id_saida'}).val(id),
                    $('<input>', {'type': 'hidden', 'class': 'form-control', id: 'data_parcelado', nome: 'data_parcelado'}).val(converteData(resultado.data_vencimento, '-'))
                    )
                    ),
                    ),
                    ),
                    $('<div>', {'id': 'parcelas'})
                    );

            BootstrapDialog.show({
                nl2br: false,
                title: 'Parcelas',
                message: formulario,
                type: 'type-info',
                size: 'size-wide',
                closable: false,
                buttons: [
                    // btn fechar
                    {
                        id: 'fechar',
                        label: '<i class="fa fa-times"></i> Fechar',
                        cssClass: 'btn-default btn-sm',
                        action: function (dialog) {
                            dialog.close();
                        }
                    },
                    // btn confirmar
                    {
                        id: 'salvar',
                        label: '<i class="fa fa-check"></i> Confirma',
                        cssClass: 'btn-sm btn-info',
                        action: function (dialog) {
                            $('#form_parcela').ajaxSubmit({
                                dataType: 'json',
                                beforeSubmit: function () {
                                    return somaValorUnidades(null);
                                },
                                success: function (data) {
                                    if (data.status) {
                                        bootAlert('Parcelado com sucesso!', 'Atenção', function () {
                                            location.reload();
                                        }, 'success');
                                    } else {
                                        bootAlert('Erro ao salvar!', 'Atenção', function () {
                                            location.reload();
                                        }, 'danger');
                                    }

                                }
                            });
                            dialog.close();
                        }
                    }]
            });
        }, 'json');

    });

    function prepara_parcelas_post(id_nfse) {
        var div = $('#parcelas_' + id_nfse);
        console.log(div.html());
        console.log(div);
        div.html('');
        $(".procentagem_parcela").each(function (i, v) {
            console.log('procentagem_parcela: ' + $(v).val());
            div.append($('<input>', {type: 'hidden', name: 'porcentagem_p[' + id_nfse + '][' + i + ']', id: 'porcentagem_p_' + id_nfse, value: $(v).val()}));
        });
        $(".valor_parcela").each(function (i, v) {
            console.log('valor_parcela: ' + $(v).val());
            div.append($('<input>', {type: 'hidden', name: 'valor_p[' + id_nfse + '][' + i + ']', id: 'valor_p_' + id_nfse, value: $(v).val()}));
        });
        $(".data_parcela").each(function (i, v) {
            console.log('data_parcela: ' + $(v).val());
            div.append($('<input>', {type: 'hidden', name: 'data_p[' + id_nfse + '][' + i + ']', id: 'data_p_' + id_nfse, value: $(v).val()}));
        });
        console.log(div.html());
    }

    $('body').on('keyup', '#n_parcelas', function () {
        var n_parcelas = parseInt($('#n_parcelas').val());
        var valor_liquido = parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.'));
        var pc = 100 / n_parcelas;
        var v = valor_liquido / n_parcelas;
        $('#parcelas').html('');
        var html = null;
        var data_parcela = $('#data_parcelado').val();
        for (var i = 0; i < n_parcelas; i++) {
            if (i > 0) {
                data_parcela = calc_data_parcela(data_parcela);
            }

            var texto_parcela = "(" + (i + 1) + "a. Parcela)";
            html =
                    $('<div>', {'class': 'row'}).append(
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Porcentagem ' + texto_parcela),
                    $('<div>', {'class': 'input-group'}).append(
                    $('<input>', {'type': 'text', 'class': 'form-control input-sm procentagem_parcela', id: 'porcentagem_' + i, name: 'porcentagem[]', 'data-key': i}),
                    $('<span>', {'class': 'input-group-addon'}).text('%')
                    )
                    )
                    ),
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Valor ' + texto_parcela),
                    $('<div>', {'class': 'input-group'}).append(
                    $('<span>', {'class': 'input-group-addon'}).text('R$'),
                    $('<input>', {'type': 'text', 'class': 'form-control input-sm valor_parcela', id: 'valor_' + i, name: 'valor[]', 'data-key': i}),
                    )
                    )
                    ),
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Data ' + texto_parcela),
                    $('<input>', {'type': 'text', 'class': 'form-control input-sm data_parcela', id: 'data_' + i, name: 'data[]'})
                    )
                    ),
                    );

            html.find('.valor_parcela').maskMoney({allowNegative: true, thousands: '.', decimal: ','});
            html.find('#valor_' + i).maskMoney('mask', v);
            html.find('.procentagem_parcela').maskMoney({allowNegative: true, thousands: '.', decimal: ','});
            html.find('#porcentagem_' + i).maskMoney('mask', pc);
            html.find('.data_parcela').mask("99/99/9999");
            html.find('#data_' + i).val(data_parcela);
            $('#parcelas').append(html);
        }

    });

    $('body').on('keyup', '.valor_parcela, .procentagem_parcela', function () {
        var key = $(this).data('key');
        var valor_liquido = parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.'));
        var valor_uni = parseFloat($('#valor_' + key).val().replace(/\./g, '').replace(/\,/g, '.'));
        var percent_uni = parseFloat($('#porcentagem_' + key).val().replace(/\./g, '').replace(/\,/g, '.'));
        console.log(percent_uni);
        if ($(this).prop('id') == 'valor_' + key) {
            var percent = ((valor_uni * 100) / valor_liquido);
//                        console.log(percent.toFixed(2));
            if (percent > 0) {
                $('#porcentagem_' + key).val(percent.toFixed(2));
            } else {
                $('#porcentagem_' + key).val(0);
            }
        } else if ($(this).prop('id') == 'porcentagem_' + key) {
            console.log('valor_liquido' + valor_liquido);
            console.log('percent_uni' + percent_uni);
            console.log('(percent_uni / 100)' + (percent_uni / 100));
            var valor = (valor_liquido * (percent_uni / 100));
            console.log(valor.toFixed(2));
            valor = number_format(valor.toFixed(2), 2, ',', '.');
            $('#valor_' + key).val(valor).maskMoney({thousands: '.', decimal: ','});
            ;
        }
        somaValorUnidades(key);
    });

    function calc_data_parcela(data_anterior) {
        if (data_anterior == '') {
            return '';
        }
        var arr = data_anterior.split('/');
        var data = new Date(arr[2], arr[1] - 1, arr[0]);
        data.setMonth(data.getMonth() + 1);
        var pad = "00";
        var diaX = data.getDate() + '';
        var dia = pad.substring(0, pad.length - diaX.length) + diaX;
        var mesX = data.getMonth() + 1 + '';
        var mes = pad.substring(0, pad.length - mesX.length) + mesX;
        return [dia, '/', mes, '/', data.getFullYear()].join('');
    }

    var totalRateio = parseFloat(0);
    function somaValorUnidades(key) {
        var valor_liquido = parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.'));
        var valor_total = parseFloat(0);
        var valor_uni;
        $('.valor_parcela').each(function (index, value) {
            if (value.value) {
                valor_uni = parseFloat(value.value.replace(/\./g, '').replace(/\,/g, '.'));
                //                        console.log(valor_uni);
                valor_total = valor_total + valor_uni;
            }
        });
        totalRateio = valor_total.toFixed(2);
        if (valor_liquido < valor_total.toFixed(2)) {
            bootAlert('A SOMA DOS VALORES DAS PARCELAS (' + valor_total.toFixed(2) + ') ESTÁ MAIOR QUE O VALOR LIQUIDO (' + valor_liquido.toFixed(2) + ')', 'SOMA DOS VALORES DAS PARCELAS', null, 'danger');
            $('#valor_' + key + ', #porcentagem_' + key).val('');
            return false;
        }
        return true;
    }

//    $('body').on('change', '#filtroTipo', function(){
//        $this = $(this);
//        if($this.val() == 't'){
//            $('.trsaida').show();
//        } else {
//            $('.trsaida').hide();
//            $('.'+$this.val()).show();
//        }
//        
//        
//    });

});