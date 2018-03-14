function showSuccessXML(data, statusText, xhr, $form) {
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
    $(".loading").html("");
    $("#list-prod-import").html(data); // exibir resultados
}

function showRequest(formData, jqForm, options) {
    var BoolImportar = Boolean($("#importar").data("status"));
    if (!BoolImportar) {
//        var valid = $("#form-xml").validationEngine('validate');
        var valid = $(jqForm).validationEngine('validate');
        if (valid == true) {
            $(".loading").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
            return true;
        } else {
            return false;
        }
    }
} 

$(document).ready(function () {

    // options do ajaxForm -----------------------------------------

    var optionsXML = {
        beforeSubmit: showRequest,
        success: showSuccessXML
    };

    var optionConsulta = {
        beforeSubmit: showRequest,
        success: function (dados) {
            $(".loading").html('');
            $("#dados_consulta").html(dados);
        }
    };

    // form-xml ----------------------------------------------------------------
    $('#fornecedor').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaFornecedor", fornecedor: $(this).val()}, function (data) {
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
    });
    $("#list-prod-import").on('click', '.n', function () {
        var id = $(this).data('id');
        $(this).slideUp();
        $('.y[data-id=' + id + ']').slideDown();
        $(this).closest('tr').removeClass().addClass('danger');
        $("#ok-" + id).val(0);
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

    $("#xml-salvar").click(function () {
        $("#xml-salvar").data('status', true);
    });
    // fim usado no success do submit ------------------------------
    // fim acao nos botoes -----------------------------------------

    $("#form-xml").ajaxForm(optionsXML); // add javaxForm
    $("#form-xml").validationEngine(); // add validation engine

    $('body').on('click', '#tipo_todos', function () {
        var valor = $(this).val();
        $('.select_tipo').each(function (index, element) {
            $(this).val(valor);
        });
    });

    // fim do form-xml ---------------------------------------------

    // consultar ---------------------------------------------------
    $('#form-consulta').ajaxForm(optionConsulta);

    $('#regiao,.tipo_produto').change(function () {
        var id_regiao = $("#regiao").val();
        var tipo = $(".tipo_produto:checked").val();
        $.post("methods_prod.php", {method: "carregaFornecedor", id_regiao: id_regiao, tipo: tipo}, function (data) {
            $("#fornecedor").html(data);
        });
    });

    $('body').on('click', '.btn-excluir', function () {
        var $this = $(this);
        var id = $this.data('id');
        bootConfirm('Tem certeza que deseja excluir este produto?', 'Excluindo', function (confirm) {
            if (confirm) {
                $.post('methods_prod.php', {method: 'excluirProduto', id: id}, function (data) {
                    if (data.status == true) {
                        var status = 'success';
                        $this.closest('tr').remove();
                    } else {
                        var status = 'danger';
                    }
                    bootAlert(data.msg, 'Salvando', null, status);
                }, 'json');
            }
        }, 'danger');
    });

    $('body').on('click', '.assoc', function () {
        $.post('methods_prod.php', {method: 'prod_assoc', id: $(this).data('id')}, function (data) {
            bootAlert(data, 'Associoação de cProd aos Produtos', null, 'info');
        });
    });

    $('body').on('click', '.add_assoc', function () {
        $('#table_assoc').append(
                $('<tr>').append(
                $('<td>').append(
                $('<input>', {name: 'id_assoc[]', type: 'hidden', class: 'form-control', value: -1}),
                $('<span>', {class: "input-group"}).append(
                $('<input>', {name: "cProd[]", type: "text", class: "form-control", value: ''}),
                $('<span>', {class: "input-group-btn"}).append(
                $('<button>', {class: "btn btn-danger excluir_assoc", 'data-id': -1}).append($('<i>', {class: "fa fa-trash-o"}), ' Excluir')
                )
                )
                )
                )
                );
    });

    $('body').on('click', '.excluir_assoc', function () {
        var $this = $(this);
        if ($this.data('id') > 0) {
            bootConfirm('Tem certeza que deseja excluir?', 'Atenção', function (result) {
                if (result) {
                    $.post('methods_prod.php', {method: 'excluir_assoc', id: $this.data('id')}, function (data) {
                        if (data.status == true) {
                            bootAlert('Excluido com sucesso!', 'Exclusão', function () {
                                $this.closest('tr').remove();
                            }, 'success');
                        } else {
                            bootAlert('Erro ao excluir!', 'Exclusão', null, 'danger');
                        }
                    }, 'json');
                }
            }, 'danger');

        } else {
            $this.closest('tr').remove();
        }
    });

    $('body').on('click', '#salvar_assoc', function () {
        $('#form_assoc').ajaxSubmit({
            dataType: 'json',
            success: function (dados) {
                if (dados.status) {
                    bootAlert('Salvo com sucesso!', 'Salvar', function () {
                        $.each(BootstrapDialog.dialogs, function (id, dialog) {
                            dialog.close();
                        });
                    }, 'success');
                } else {
                    bootAlert('Erro ao salvar!', 'Atenção', null, 'danger');
                }
            }
        });
    });


});




