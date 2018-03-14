
$(function () {
    
    var datas_horas_id      = new Array();
    var datas_add           = new Array();
    var horas_add           = new Array();
    var count_datas_horas   = -1;
    var id_clt              = $('#cltSelected').val();
    var mes                 = $('#mes').val();
    var ano                 = $('#ano').val();
    
    $(".verDatasHoras").on('click', function () {

        var t = $(this);
        
        var id_movimento = t.data('id');
        var id_mov = t.data('mov');
        var title = "DATAS/HORAS REFERENTES AO MOVIMENTO<br><small><i>Atenção! Ao pressionar o botão OK, todas as alterações realizadas serão salvas e página será recarregada.</i></small>";
        var form;
        var hora;
        
        if (id_mov == 236) {
            hora =  "<span class='input-group-addon'><i class='fa fa-clock-o'></i></span>" +
                    "<input type='text' placeholder='___:__' class='form-control qnt_atraso_falta' />";
        } else if (id_mov == 232 || id_mov == 293) {
            hora = '';
        }
        
        form =  "<div class='dataHora' style='margin-bottom:10px'>" +
                    "<div class='panel panel-default oldDataHora' style='margin-bottom:10px'>" +
                        "<div class='panel-heading'>" +
                            "DATAS/HORAS LANÇADAS" +
                        "</div>" +
                        "<div class='panel-body'>" +
                            "<div class='row'>" +
                            "</div>" +
                        "</div>" +
                    "</div>" +
                    "<div class='newDataHora' style='margin-bottom:10px'>" +
                        "<div class='row' style='margin-bottom:10px'>" +
                            "<div class='col-sm-12'>" +
                                "<div class='input-group'>" +
                                "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                                "<input type='hidden' class='id_mov' val='" + id_movimento + "' />" +
                                "<input type='text' placeholder='__/__/____' class='form-control data_atraso_falta' />" +
                                hora +
                                "<span data-mov='" + id_mov + "' data-id='" + id_movimento + "' class='pointer input-group-addon add_data_hora_exists'><i class='success fa fa-plus'></i></span>" +
                                "</div>" +
                            "</div>" +
                        "</div>" +
                        "<div class='row addDataHora' style='margin-bottom:10px'>" +
                        "</div>" +
                    "</div>" +
                "</div>";
        form = $(form);
        form.find(".qnt_atraso_falta").mask("999:99");
        form.find(".data_atraso_falta").mask("99/99/9999");
        
        $.post("../methods.php", {method: "getDatasHorasFaltasAtrasos", id_movimento: id_movimento}, function (data) {
            
            form.find('.oldDataHora').find('.panel-body').find('.row').append(data);
            bootConfirm(form, title, function (result) {

                if (result) {
                    
                    var hor_mili = 0;
                    var min_mili = 0;
                    var mili = 0;
                    var total_mili = 0;
                    var i = 0;
                    var dias = 0;
                    var qnt_dt;
                    var qnt_hr;
                    var qnt_hr_array;
                    var qnt;
                    var new_datas_horas = new Array();
                    
                    $("body .dt_falta_atraso").each(function () {

                        qnt_dt = $(this).val();
                        datas_add.push(qnt_dt);
                        total_mili += qnt_dt;
                    });

                    $("body .hr_falta_atraso").each(function () {

                        var qnt_hr = $(this).val();
                        var qnt_hr_array = qnt_hr.split(":");

                        hor_mili = qnt_hr_array[0] * 60 * 60 * 1000;
                        min_mili = qnt_hr_array[1] * 60 * 1000;
                        mili = hor_mili + min_mili;
                        total_mili += msToHM(mili);
                        horas_add.push(msToHM(mili));

                    });
                    
                    if (datas_horas_id > 0 || horas_add.length > 0 || datas_add.length > 0) {
                        
                        new_datas_horas.push(datas_add);
                        new_datas_horas.push(horas_add); 
                        
                        $.post("../methods.php",{
                            method: "updateDatasHorasFaltasAtrasos", 
                            remove: datas_horas_id, 
                            add: new_datas_horas,
                            id_clt: id_clt,
                            id_mov: id_mov,
                            id_movimento: id_movimento,
                            mes: mes,
                            ano: ano,
                            qnt: total_mili
                        }, function (data) {
                            
                            $('body').remove();
                            window.location.href = window.location.href;
                                                            
                            datas_horas_id = new Array();
                            datas_add = new Array();
                            horas_add = new Array();
                        });
                    }
                    
                    datas_horas_id = new Array();
                    datas_add = new Array();
                    horas_add = new Array();
                    
                } else {
                    datas_horas_id = new Array();
                    datas_add = new Array();
                    horas_add = new Array();
                }

            }, "danger");

        });

    });
    
    $('body').on('click', '.add_data_hora_exists', function () {
        
        var t = $(this);
        var id_mov = t.data('mov');
        var data_input = t.parent().find('.data_atraso_falta');
        var qnt_horas_input = t.parent().find('.qnt_atraso_falta');
        var data = data_input.val();
        var qnt_horas = qnt_horas_input.val();
        var div = t.parent().parent().parent().parent().find(".addDataHora");
        var hora;
        
        if (data != '' && qnt_horas != '') {
            if (id_mov == 236) {
                hora =  "<span class='input-group-addon'><i class='fa fa-clock-o'></i></span>" +
                        "<input type='text' readonly placeholder='___:__' class='form-control hr_falta_atraso' value='" + qnt_horas + "' />";
            } else if (id_mov == 232 || id_mov == 293) {
                hora = '';
            }

            var input =
    //                    "<div class='row' style='margin-bottom:10px'>" +
                            "<div style='margin-bottom:5px' class='col-sm-12'>" +
                                "<div class='input-group'>" +
                                    "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                                    "<input type='text' readonly placeholder='__/__/____' class='form-control dt_falta_atraso' value='" + data + "'/>" +
                                    hora +
                                    "<span class='pointer input-group-addon rm_data_hora'><i class='danger fa fa-minus'></i></span>" +
                                "<div>" +
                            "<div>";
    //                    "<div>";
            div.append(input);
            
            data_input.val(null);
            qnt_horas_input.val(null);
        }
    });
    
    $("body").on('click', ".rm_data_hora", function () {
        
        var t = $(this);
        t.parent().parent().remove();

    });
    
    $("body").on('click', ".rem_data_hora", function () {
        count_datas_horas++;
        var t = $(this);
        var id_movimento = t.data('id');
        
        datas_horas_id.push(id_movimento);
        t.parent().parent().remove();

    });

    $(".open_calendar").on("click", function () {

        var t = $(this);
        var id = t.data('key');
        var mov = t.data("key");
        var title;
        var form;
        var arr_hr = new Array();
        var arr_dt = new Array();
        var countDias = 0;

        if (id == 236) {

            title = "Selecione às Datas e a Quantidade de Horas/Dia";
            form = "<div class='boxHorasDias' style='margin-bottom:10px'>";
            form += "<div class='row' style='margin-bottom:10px'>" +
                    "<div class='col-sm-8'>" +
                    "<div class='input-group'>" +
                    "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                    "<input type='hidden' class='id_mov' val='" + id + "' />" +
                    "<input type='text' placeholder='__/__/____' class='form-control data_atraso_falta' />" +
                    "<span class='input-group-addon'><i class='fa fa-clock-o'></i></span>" +
                    "<input type='text' placeholder='___:__' class='form-control qnt_atraso_falta' />" +
                    "<span data-id='" + id + "' class='pointer input-group-addon add_data_hora'><i class='success fa fa-plus'></i></span>" +
                    "</div>" +
                    "</div>" +
                    "</div>";

            $("body .horas_falta_atraso_236").each(function () {
                arr_hr.push($(this).val());
            });
            $("body .datas_falta_atraso_236").each(function () {
                arr_dt.push($(this).val());
            });
            if (arr_hr.length > 0) {
                for (i = 0; i < arr_hr.length; i++) {
                    form +=
                            "<div class='row' style='margin-bottom:10px'>" +
                            "<div class='col-sm-8'>" +
                            "<div class='input-group'>" +
                            "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                            "<input type='text' readonly placeholder='__/__/____' class='form-control dt_falta_atraso' value='" + arr_dt[i] + "'/>" +
                            "<span class='input-group-addon'><i class='fa fa-clock-o'></i></span>" +
                            "<input type='text' readonly placeholder='___:__' class='form-control hr_falta_atraso' value='" + arr_hr[i] + "' />" +
                            "<span class='pointer input-group-addon remove_data_hora'><i class='danger fa fa-minus'></i></span>" +
                            "</div>" +
                            "</div>" +
                            "</div>";
                }
                
            }
            form += "</div>";

            $("body .horas_falta_atraso_236").remove();
            $("body .datas_falta_atraso_236").remove();

            form = $(form);
            form.find(".qnt_atraso_falta").mask("999:99");
            form.find(".data_atraso_falta").mask("99/99/9999");

            bootConfirm(form, title, function () {

                var hor_mili = 0;
                var min_mili = 0;
                var mili = 0;

                $("body .dt_falta_atraso").each(function () {

                    var qnt_dt = $(this).val();
                    var html = "<input type='hidden' class='datas_falta_atraso_" + id + "' name='datas_falta_atraso[" + id + "][]' value='" + qnt_dt + "' />";

                    t.after(html);

                });

                $("body .hr_falta_atraso").each(function () {

                    var qnt_hr = $(this).val();
                    var qnt_hr_array = qnt_hr.split(":");

                    hor_mili = qnt_hr_array[0] * 60 * 60 * 1000;
                    min_mili = qnt_hr_array[1] * 60 * 1000;
                    mili += hor_mili + min_mili;

                    var html = "<input type='hidden' class='horas_falta_atraso_" + id + "' name='horas_falta_atraso[" + id + "][]' value='" + qnt_hr + "' />";

                    t.after(html);
                });

                $("." + id).val(msToHM(mili)).trigger('change');

            }, "danger");

        } else if (id == 232 || id == 293) {

            title = "Selecione às Datas das Faltas";
            form = "<div class='boxHorasDias' style='margin-bottom:10px'>";
            form += "<div class='row' style='margin-bottom:10px'>" +
                    "<div class='col-sm-8'>" +
                    "<div class='input-group'>" +
                    "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                    "<input type='hidden' class='id_mov' val='" + id + "' />" +
                    "<input type='text' placeholder='__/__/____' class='form-control data_atraso_falta' />" +
                    "<span data-id='" + id + "' class='pointer input-group-addon add_data_hora'><i class='success fa fa-plus'></i></span>" +
                    "</div>" +
                    "</div>" +
                    "</div>";

            $("body .datas_falta_atraso_" + id).each(function () {
                arr_dt.push($(this).val());
            });
            if (arr_dt.length > 0) {
                for (i = 0; i < arr_dt.length; i++) {
                    form +=
                            "<div class='row' style='margin-bottom:10px'>" +
                            "<div class='col-sm-8'>" +
                            "<div class='input-group'>" +
                            "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                            "<input type='text' readonly placeholder='__/__/____' class='form-control dt_falta_atraso' value='" + arr_dt[i] + "'/>" +
                            "<span class='pointer input-group-addon remove_data_hora'><i class='danger fa fa-minus'></i></span>" +
                            "</div>" +
                            "</div>" +
                            "</div>";
                }
                ;
            }
            form += "</div>";

            $("body .datas_falta_atraso_" + id).remove();

            form = $(form);
            form.find(".data_atraso_falta").mask("99/99/9999");

            bootConfirm(form, title, function () {

                $("body .dt_falta_atraso").each(function () {

                    var qnt_dt = $(this).val();
                    countDias++;
                    var html = "<input type='hidden' class='datas_falta_atraso_" + id + "' name='datas_falta_atraso[" + id + "][]' value='" + qnt_dt + "' />";

                    t.after(html);

                });

                $("." + id).val(countDias).trigger('change');
            }, "danger");
        }



    });

    $("body").on("click", ".remove_data_hora", function () {

        var t = $(this);
        t.parent().parent().parent().remove();

    });

    $("body").on("click", ".add_data_hora", function () {

        var t = $(this);
        var data_input = t.parent().find('.data_atraso_falta');
        var qnt_horas_input = t.parent().find('.qnt_atraso_falta');
        var data = data_input.val();
        var qnt_horas = qnt_horas_input.val();
        var id = t.data('id');

        if (id == 236) {
            if (data != '' && qnt_horas != '') {
                var input =
                        "<div class='row' style='margin-bottom:10px'>" +
                        "<div class='col-sm-8'>" +
                        "<div class='input-group'>" +
                        "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                        "<input type='text' readonly placeholder='__/__/____' class='form-control dt_falta_atraso' value='" + data + "'/>" +
                        "<span class='input-group-addon'><i class='fa fa-clock-o'></i></span>" +
                        "<input type='text' readonly placeholder='___:__' class='form-control hr_falta_atraso' value='" + qnt_horas + "' />" +
                        "<span class='pointer input-group-addon remove_data_hora'><i class='danger fa fa-minus'></i></span>" +
                        "<div>" +
                        "<div>" +
                        "<div>";
                data_input.val(null);
                qnt_horas_input.val(null);
                t.parent().parent().parent().after(input);
            }
        } else if (id == 232 || id == 293) {
            if (data != '' && qnt_horas != '') {
                var input =
                        "<div class='row' style='margin-bottom:10px'>" +
                        "<div class='col-sm-8'>" +
                        "<div class='input-group'>" +
                        "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                        "<input type='text' readonly placeholder='__/__/____' class='form-control dt_falta_atraso' value='" + data + "'/>" +
                        "<span class='pointer input-group-addon remove_data_hora'><i class='danger fa fa-minus'></i></span>" +
                        "<div>" +
                        "<div>" +
                        "<div>";
                data_input.val(null);
                t.parent().parent().parent().after(input);
            }
        }
    });

    $('.span-dsr').click(function () {
        var that = $(this);
        var mes = $("input[name='mesMov']").val();
        var ano = $("input[name='anoMov']").val();
        var clt = $("input[name='cltSelected']").val();

        $.ajax({
            url: "action_calcula_movimento.php",
            type: "POST",
            dataType: "json",
            data: {
                mes: mes,
                ano: ano,
                id_clt: clt,
                mov_valor: that.parent().parent().find('.cred').val(),
                method: "calculaDSR"
            },
            success: function (res) {
                bootAlert("<b>Valor do DSR: R$" + res + "</b>", "Cálculo DSR");
            }
        });
    });

    $('.sonumeros').keypress(function (event) {
        var tecla = (window.event) ? event.keyCode : event.which;
        if ((tecla > 47 && tecla < 58))
            return true;
        else {
            if (tecla != 8)
                return false;
            else
                return true;
        }
    });

    $('.hora_mask').mask("999:99");

    $('#form').validationEngine();

    $('.cred,.desc').priceFormat({
        prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '.'
    });

    $('.mov').click(function () {
        $('.mov').css('border-color', '#E2E2E2')
        $(this).css('border-color', '#9bd4f8')
    })

    $('.excluir').click(function () {

        var clt = $(this).data("clt");
        var id_movimento = $(this).attr('rel');
        var linha = $(this).parent().parent();

        BootstrapDialog.confirm('Excluir este movimento?', 'Confirmação de Exclusão', function (result) {
            if (result) {
                $.post('rh_movimentos_3.php', {excluir: 1, id_movimento: id_movimento, id_clt: clt},
                        function (data) {
                            linha.fadeOut();
                        })
            }
        });

        return false;
    });

    $('.tipo_qnt').change(function () {

        var elemento = $(this);
        var div = elemento.parent().parent().find('.calculo');
        if (elemento.val() == 1) {
            div.mask('99:99')
            div.val('');
        } else if (elemento.val() == 2) {
            div.unmask('99:99')
            div.val('');
        }

    });

    $(".calculo").change(function () {
        var quant = $(this).val();
        var elemento = $(this).parent().parent();
        var key = $(this).data("key");
        var tipo_contagem = elemento.find('.tipo_qnt').val();
        var id_clt = $('#clt').val();

//                    console.log("quant:" + quant + ", elemento:" + elemento + ", key:" + key + ", tipo_contagem:" + tipo_contagem + ", id_clt:" + id_clt);

        $.post('action_calcula_movimento.php', {
            id_clt: id_clt,
            id_mov: key,
            tipo_qnt: tipo_contagem,
            qnt: quant
        }, function (data) {
//                        console.log(data);
            $(".result_" + key).val(parseFloat(data).formatMoney("2", ",", "."));
        });
    });

    //                $(".aux-distancia").blur(function(){
    //                    var salbase = $("#salario_base").val();
    //                    var auxDistancia = $(this).val();
    //                    var minAuxilio = salbase * 0.25;
    //                    minAuxilio = minAuxilio.toFixed(2);
    //                    auxDistancia = auxDistancia.replace(".", "");
    //                    auxDistancia = auxDistancia.replace(",", ".");
    //                   
    //                    if(minAuxilio < auxDistancia){
    //                        $(".aux-distancia").parents("fieldset").css({background:"#999"});
    //                        //console.log("Valor esta abaixo dos 25%, obrigatório para o auxílio destância");
    //                    }
    //                    
    //                });        

    $(".result_60").click(function () {
        $("#tooltip_mov").html("\
                            <a href='#' class='tooltip'>\n\
                                Tooltip\n\
                                <span>\n\
                                    <img class='callout' src='../imagens/callout.gif' />\n\
                                    <strong>Most Light-weight Tooltip</strong><br />\n\
                                    This is the easy-to-use Tooltip driven purely by CSS.\n\
                                </span>\n\
                            </a>"
                );
    });

    /**
     * ABRINDO MODAL
     * @returns {Boolean}
     */
    $("input[type='text'").click(function () {

        //FLAG PARA CHAMAR MODAL
        var flag = $(this).attr("data-modal");
        //MES SELECIONADO NO GRUPO CREDITO
        var mesSelected = $(".mesMovCredito :selected").val();
        $("input[name='mesMov']").val(mesSelected);
        //ANO SELECIONADO NO GRUPO CREDITO
        var anoSelected = $(".anoMovCredito :selected").val();
        $("input[name='anoMov']").val(anoSelected);
        //MODAL COM FORM DE CADASTRO
        if (flag == 1) {
            thickBoxModal("Lançar Movimento", "#modal_reembolso_faltas", 280, 500);
        }
    });

    /**
     * CADASTRO
     * @returns {Boolean}
     */
    $(".cadValorDiasReembolso").click(function () {
        var quant = $("input[name='quant_dias_reembolso']").val();
        var valor = $("input[name='valor_dias_reembolso']").val();
        var regiao = $("input[name='regiaoMov']").val();
        var projeto = $("input[name='projetoMov']").val();
        var mes = $("input[name='mesMov']").val();
        var ano = $("input[name='anoMov']").val();
        var clt = $("input[name='cltSelected']").val();

        if ($("#formCadMov").validationEngine('validate')) {
            $.ajax({
                url: "",
                type: "POST",
                dataType: "json",
                data: {
                    qnt: quant,
                    valor: valor,
                    regiao: regiao,
                    projeto: projeto,
                    mes: mes,
                    ano: ano,
                    clt: clt,
                    method: "cadMovReembolso"
                },
                success: function (data) {
                    if (data.status) {
                        history.go();
                    }
                }
            });
        }

    });
    //**********************************************************************//

    $('.open_info').click(function () {

        var elemento = $(this).closest('tr');
        var key = $(this).data("key");
        var tipo_contagem = elemento.find('.tipo_qnt').val();
        var quant = elemento.find('.calculo').val();
        var id_clt = $('#clt').val();

        //                    console.log("quant:" + quant + ", elemento:" + elemento + ", key:" + key + ", tipo_contagem:" + tipo_contagem + ", id_clt:" + id_clt);

        $.post('action_calcula_movimento.php', {
            id_clt: id_clt,
            id_mov: key,
            tipo_qnt: tipo_contagem,
            qnt: quant,
            just_info: true
        }, function (data) {
            bootDialog(data, "Cálculo Detalhado");
        }, 'json');

    });

});


function auxDistancia(fiel, rules, i, options) {
    var salbase = $("#salario_base").val();
    var auxDistancia = fiel.val();
    var minAuxilio = salbase * 0.25;
    minAuxilio = minAuxilio.toFixed(2);
    auxDistancia = auxDistancia.replace(".", "");
    auxDistancia = auxDistancia.replace(",", ".");
    if (parseFloat(auxDistancia) < parseFloat(minAuxilio)) {
        return options.allrules.auxDistancia.alertText;
    }
}