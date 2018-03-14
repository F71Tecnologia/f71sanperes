$(document).ready(function () {
       
    function showSuccessPedido(data) {
        console.log(data.itens);
        if (typeof data.itens != 'undefined') {
            $("#tab-produtos").removeClass("hide");
            var html = "";
            $.each(data.itens, function (i, item) {
                html += "<tr id=\"tr-item-" + data.itens[i].id_prod + "\">";
                html += "<td class=\"text text-center\">" + data.itens[i].cProd + "<input type=\"hidden\" name=\"idProd[]\" value=\""+data.itens[i].id_prod+ "\"  </td>";
                html += "<td>" + data.itens[i].xProd + "</td>";
                html += "<td class=\"text text-center\">" + data.itens[i].uCom + "</td>";
                html += "<td class=\"text text-right\">" + number_format(data.itens[i].vUnCom, 2, ',', '.') + "<input type=\"hidden\" name=\"vUnCom[]\" id=\"vUnCom-" + data.itens[i].id_prod + "\" class=\"form-control money\" value=" + number_format(data.itens[i].vUnCom, 2, ',', '.') + " ></td>";
                html += "<td><input type=\"text\" class=\"form-control text text-center item_qtde\" name=\"qtde[]\"  data-id=" + data.itens[i].id_prod + " size=\"12\" maxlength=\"12\"></td>";
                html += "<td><input type=\"text\" name=\"vProd[]\" id=\"vProd-" + data.itens[i].id_prod + "\" size=\"12\" class=\"text form-control text-right\" readonly> </td>";
                html += "</tr>";
            });
            $("#tab-produtos tbody").html(html);
        } else if (data.msg) {
            bootAlert('Sem Dados!!!');
        }
    }

    var optionsCadastroPed = {
        success: showSuccessPedido,
        dataType: 'json'
    };
            
//            $("#tb-produtos").on('blur', ".item_qtde", function () {
//                var id_prod = $(this).data('id');
//                var qtd = parseFloat($(this).val().replace(',', '.'));
//                var vUni = parseFloat($("#vUnCom-" + id).maskMoney('unmasked')[0]);
//                var valor = (qtd * vUni).toFixed(2);
//                valor = number_format(valor,2,',','.');
//                $("#vProd-" + id).val(valor);
//            });             

    function showSuccessCadastro1(data) {
        $("#resp-cadastro1").html(data);
    }
    
    function showSuccess(data, statusText, xhr, $form) {
        var BoolImportar = Boolean($("#xml-importar").data('status'));
        var BoolSalvar = Boolean($("#xml-salvar").data('status'));
        if (BoolImportar) {
            $("#select-regiao-projeto").removeClass('hidden');
            $("#xml-salvar").prop('disabled', false);
            $("#importar").data('status', false);
        }
        if (BoolSalvar) {
            $("#select-regiao-projeto").addClass('hidden');
            $("#xml-salvar").prop('disabled', true);
            $("#form-xml").each(function () {
                this.reset();
            });
            $("#xml-salvar").data('status', false);
        }
        $("#list-prod-import").html(data); // exibir resultados
    }
            
    function showSuccess1(data, statusText, xhr, $form) {
        var BoolImportar = Boolean($("#xml-importar1").data('status'));
        var BoolSalvar = Boolean($("#xml-serv-salvar").data('status'));
        if (BoolImportar) {
            $("#select-regiao-projeto1").removeClass('hidden');
            $("#xml-serv-salvar").prop('disabled', false);
            $("#importar-serv").data('status', false);
        }
        if (BoolSalvar) {
            $("#select-regiao-projeto1").addClass('hidden');
            $("#xml-serv-salvar").prop('disabled', true);
            $("#form1-xml").each(function () {
                this.reset();
            });
            $("#xml-serv-salvar").data('status', false);
        }
        $("#list-serv-import").html(data); // exibir resultados
    }

    $(document).ready(function () {

                // options do ajaxForm -----------------------------------------
                var optionsCadastroPed = {
                    beforeSubmit: showRequest,
                    success: showSuccessPedido,
                    resetForm: true,
                    dataType: 'json'
                };
                var optionsCadastro1 = {
                    beforeSubmit: showRequest1,
                    success: showSuccessCadastro1,
                    resetForm: true
                };
                var optionsXML = {
                    beforeSubmit: showRequest,
                    success: showSuccess
                };
                var optionsXML1 = {
                    beforeSubmit: showRequest1,
                    success: showSuccess1
                };

                // form-cadastro-pedido ---------------------------------------
                $("#vUnCom").maskMoney({thousands: '.', decimal: ','});
                $("#form-pedido").ajaxForm(optionsCadastroPed);// add javaxForm
                $("#form-pedido").validationEngine();// add validation engine
                // fim do form-cadastro ----------------------------------------

                $('#regiao1,#regiao2,#regiao3,#regiao4').change(function () {
                    var destino = $(this).data('for');
                    $.post("http://www.f71lagos.com/intranet/methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
                        $("#" + destino).html(data);
                    });
                });

                $('#projeto1,#projeto2,#projeto3,#projeto4').change(function () {
                    var destino = $(this).data('for');
                    $.post("http://www.f71lagos.com/intranet/methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
                        $("#" + destino).html(data);
                    });
                });
                // acao nos cones de marcar V ou X -----------------------------
                $("#list-prod-import").on('click', '.y', function () {
                    var id = $(this).data('id');
                    $(this).slideUp();
                    $('.n[data-id=' + id + ']').slideDown();
                    $(this).closest('tr').removeClass().addClass('success');
                    $("#ok-" + id).val(1);
//                    console.log('y ' + $("#ok-" + id).val());
                });
                $("#list-prod-import").on('click', '.n', function () {
                    var id = $(this).data('id');
                    $(this).slideUp();
                    $('.y[data-id=' + id + ']').slideDown();
                    $(this).closest('tr').removeClass().addClass('danger');
                    $("#ok-" + id).val(0);
//                    console.log('n ' + $("#ok-" + id).val());
                });
                // -------------------------------------------------------------                

                // acoes nos botoes --------------------------------------------
                $("#xml-cancelar").click(function () {
                    $("#list-prod-import").html('');
                });
                // usado no success do submit ----------------------------------
                $("#xml-importar").click(function () {
                    $("#xml-importar").data('status', true);
                });
                $("#xml-importar1").click(function () {
                    $("#xml-importar1").data('status', true);
                });
                $("#xml-salvar").click(function () {
                    $("#xml-salvar").data('status', true);
                });
                $("#xml-serv-salvar").click(function () {
                    $("#xml-serv-salvar").data('status', true);
                });
                // fim usado no success do submit ------------------------------
                // fim acao nos botoes -----------------------------------------

                $("#form-xml").ajaxForm(optionsXML); // add javaxForm
                $("#form-xml").validationEngine(); // add validation engine

                $("#form1-xml").ajaxForm(optionsXML1); // add javaxForm
                $("#form1-xml").validationEngine(); // add validation engine

                // fim do form-xml ---------------------------------------------
            });

            function showRequest(formData, jqForm, options) {
                var BoolImportar = Boolean($("#importar").data("status"));
                if (!BoolImportar) {
                    var valid = $("#form-xml").validationEngine('validate');
                    if (valid == true) {
                        $("#list-prod-import").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
                        return true;
                    } else {
                        return false;
                    }
                }
            }
            function showRequest1(formData, jqForm, options) {
                var BoolImportar = Boolean($("#importar1").data("status"));
                if (!BoolImportar) {
                    var valid = $("#form1-xml").validationEngine('validate');
                    if (valid == true) {
                        $("#list-serv-import").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
                        return true;
                    } else {
                        return false;
                    }
                }
            }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //datepicker
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    // carregando projetos -----------------------------------------------------
    $('#regiao1,#regiao2,#regiao3,#regiao4').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });
    $('#projeto1,#projeto2,#projeto3,#projeto3').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });
    // -------------------------------------------------------------------------

    // mascaras e formatações --------------------------------------------------
    $('body').on('focus', '.money', function () {
        $('.money').maskMoney({allowNegative: true, thousands: '.', decimal: ',', affixesStay: false});
    });
    $("#dt_emissao_nf, #data_vencimento").mask("99/99/9999");
    $("#cnpj").mask("99.999.999/9999-99");
    // -------------------------------------------------------------------------

    // form1 -------------------------------------------------------------------
    $("#chaveacesso").blur(function () {
        var chave = $(this).val().replace(/ /g, "");
        var cnpj = chave.substring(6, 20);
        $("#cnpj").val(cnpj);
        var numeronf = chave.substring(25, 34);
        $("#numeronf").val(numeronf);
        $("#cnpj").trigger('blur');
    });

    $("#form1").validationEngine({promptPosition: "topRight"});

    $("#form1").ajaxForm({
        beforeSubmit: showRequest,
        success: function (data) {
            $("#resp_form_cad").html(data);
            $("#cod-item").val('');
            $("#tb-itens").html('');
            $("#endeforn").html('');
            $("#fornecedor").html('');
        },
        resetForm: true
    });

    $("#item").keypress(function () {
        var prestador = $('#prestador1').val();
        $.post('../produtos/methods.php', {method: 'carregaItem',prestador:prestador}, function (data) {
            $("#item").autocomplete({
                source: data.prods,
                minLength: 3,
                change: function (event, ui) {
                    if (event.type == 'autocompletechange') {
                        var array_item = $("#item").val().split(' - ');
                        $("#cod-item").val(array_item[0]);
                        $("#item").val('');
                        $(".loading").html('');
                    }
                }
            });
        }, 'json');
    });

    $("#item-incluir").click(function () {
        var id_prod = $("#cod-item").val();
        if (id_prod !== '') {
            $.post("NFe_atualiza_tab.php", {id_prod: id_prod, method: 'itemIncluir'}, function (dados) {
                $("#tb-itens").append(dados);
                $("#cod-item").val('');
            });
        }
    });

    $("#projeto1").change(function () {
        $("#cnpj").trigger('blur');
    });
    
    $("#projeto2").change(function () {
        $("#prestador2").trigger('blur');
    });

    $('#cnpj').blur(function () {
        if ($(this).val() != '') {
            $.post('NFe_atualiza_tab.php', {cnpjcpf: $(this).val(), projeto: $('#projeto1').val(), method: "consultacnpjcpf"}, function (data) {
                if (data.status) {
                    $('#fornecedor').html(data.nome);
                    $('#endeforn').html(data.endereco);
                } else {
                    $('#fornecedor').html("<span class=\"text-danger\">" + data.msg + "</span>");
                    $('#endeforn').html('');
                }
            }, "json");
        }
    });

    $('#prestador2').change(function () {
        if ($(this).val() != '') {
            $.post('NFe_atualiza_tab.php',  {cnpjcpf: $(this).val(), projeto: $('#projeto2').val(), method: "consultacnpjcpf"}, function (data) {
                var cnpj_nota = $("#cnpj-nfe").val();
                console.log(data.cnpjcpf+" == "+cnpj_nota);
                if(data.cnpjcpf != cnpj_nota){
                    bootAlert("<span class=\"text-warning\">Prestador Selecionado diferente da Nota Fiscal!</span>","Atenção",null,'warning');
                }
            }, "json");
        }
    });

    $("#tb-itens").on('click', '.item-excluir', function () {
        var id = $(this).data('id');
        $("#tr-item-" + id).remove();
    });

    $("#tb-itens").on('blur', ".qtd-item", function () {
        var id = $(this).data('id');
        var qtd = parseFloat($(this).val().replace(',', '.'));
        var vUni = parseFloat($("#vUnCom-" + id).maskMoney('unmasked')[0]);
        var valor = (qtd * vUni).toFixed(2);
        valor = number_format(valor,2,',','.');
        $("#vProd-" + id).val(valor);
    });    // fim do form1 -----------------------------------------------------

    // form2 -------------------------------------------------------------------
    $("#limpa").click(function () {
        $("#tabela").html('');
        $("#habilitar").addClass('hidden');
    });
    $("#form2").ajaxForm({
        beforeSubmit: showRequest,
        success: function (data) {
            if ($("#salvar").prop('disabled')) {
                $("#habilitar").removeClass('hidden');
                $("#salvar").prop('disabled', false);
            } else {
                $("#habilitar").addClass('hidden');
                $("#salvar").prop('disabled', true);
                $("#form2").each(function () {
                    this.reset();
                });
            }
            $("#tabela").html(data);
        }
    });    // fim do form2 -----------------------------------------------------

    // form3 -------------------------------------------------------------------
    $("#form3").ajaxForm({
        beforeSubmit: function () {
            $("#visualizar-NFe").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            $("#visualizar-NFe").html(data);
        }
    });
    
    $("#visualizar-NFe").on('click', '.nfe-detalhes', function () {
        console.log($(this).data('id'));
        $.post("visualizar_XML.php", {id_nfe: $(this).data('id'), projeto: $("#projeto3").val(), regiao: $("#regiao3").val(), method: 'Detalhes'}, function (dados) {
            BootstrapDialog.show({
                size: 'size-wide',
                nl2br: false,
                title: 'Detalhes da Nota Fiscal',
                message: dados,
            });
        });
    });

    $("#visualizar-NFe").on('click', '.nfe-cancelar', function () {
        console.log($(this).data('id'));
        var titulo = 'Cancelar Nota Fiscal!';
        var id = $(this).data('id');
        bootConfirm('Deseja prosseguir com o cancelamento ?', titulo, function (status) {
            if(status){
                $.post('visualizar_XML.php',{method:'cancelar',id:id},function(data){
                    if(data.status){
                        bootAlert('Cancelado com sucesso!',titulo,null,'success');
                        $('#tr-'+id).remove();
                    }else{
                        bootAlert('Erro ao Cancelar a nota.',titulo,null,'danger');
                    }
                },'json');
            }
        }, 'danger');
    });
});     //fim do form3 -------------------------------------------------------------

function formata_mascara(src, mascara) {
    var campo = src.value.length;
    var saida = mascara.substring(0, 1);
    var texto = mascara.substring(campo);
    if (texto.substring(0, 1) != saida) {
        src.value += texto.substring(0, 1);
    }
}

function showRequest(formData, jqForm, options) {
//    var valid = $("#form2").validationEngine('validate');
    var valid = jqForm.validationEngine('validate');
    if (valid == true) {
//        $(".loading").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        return true;
    } else {
        return false;
    }
}
