$(document).ready(function () {

    add_masks();

    // preenche dados do endereco a partir do cep
    $('body').on('blur', '.cep', function () {

        $this = $(this);
        $this.after('<i class="fa fa-spinner fa-spin form-control-feedback" id="img_load_cep"></i>');
        $('#c_id_tp_logradouro, #c_endereco, #c_bairro, #c_uf, #c_cidade').prop('readonly', true);
        $('#id_tp_logradouro, #endereco, #bairro, #uf, #cidade').prop('readonly', true);

        var cep = $this.val();
        $.post('../../busca_cep.php', {cep: cep, id_municipio: 1, municipios: 1}, function (data) {

            $('#img_load_cep').remove();

            if (data.cep == '') {
                bootAlert('Cep não encontrado!', null, null, 'warning');
                $('#c_id_tp_logradouro, #c_endereco, #c_bairro, #c_uf, #c_cidade').removeProp('readonly').val('');
                $('#id_tp_logradouro, #endereco, #bairro, #uf, #cidade').removeProp('readonly').val('');
            } else {
                $("#c_cidade,#cidade").autocomplete({source: data.municipios,
                    change: function (event, ui) {
                        if (event.type == 'autocompletechange') {
                            var valor_municipio = ui.item.value.split(')-');
                            $('#c_cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                            $('#cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                            $('#c_cidade').val(valor_municipio[1].trim());
                            $('#cidade').val(valor_municipio[1].trim());
                        }
                    }
                });
                $('#c_id_tp_logradouro').val(data.id_tp_logradouro);
                var logradouro = data.logradouro.split('-');
                $('#c_endereco,#endereco').val(logradouro[0]);
                $('#c_bairro,#bairro').val(data.bairro);
                $('#c_uf,#uf').val(data.uf);
                $('#c_cidade,#cidade').val(data.cidade);
                $('#c_cod_cidade,#cod_cidade').val(data.id_municipio);
            }
        }, 'json');
    });


    // valida cnpj
    $("#cnpj").change(function () {
        var $this = $(this);
        testandoCNPJ($this.val());
    });

    $('body').on('change', 'input[name=matriz_filial]', function () {
        var $this = $(this);
        if ($this.val() == '1') {
            $("#label_matriz").addClass('hidden');
            $("#id_matriz").addClass('hidden').val('-1');
        } else {
            $("#label_matriz").removeClass('hidden');
            $("#id_matriz").removeClass('hidden');
        }
    });

    var optionsSubmit = {
        beforeSubmit: function () {
            return $("#form_empresa").validationEngine('validate');
        },
        dataType: 'json',
        success: function (data) {
            bootAlert(data.msg, 'Salvando', function () {
                $.each(BootstrapDialog.dialogs, function (id, dialog) {
                    dialog.close();
                    window.location.href = 'index.php';
                });
            }, data.status);
            $("#id_empresa").append('<option value="' + data.option.id_empresa + '" selected>' + data.option.razao_empresa + '</option>');
        },
        error: function (dialog) {
            bootAlert('<h4>Houve um erro ao salvar!</h4>', '<i class="fa fa-exclamation-triangle fa-lg"></i> Atenção', null, 'danger');
        }
    };
    $("#form_empresa").ajaxForm(optionsSubmit);

        //SOCIOS E DEPENDENTES
        
        
    $('body').on('click', '.adicionar_socio', function () {
        var clone = $('#tr_socio').clone();
        clone.find('.form-control').val('');
        $('.body_socio').append(clone);
        add_masks();
    });

    $('body').on('click', '.adicionar_dependente', function () {
        var clone = $('#tr_dependente').clone();
        clone.find('.form-control').val('');
        $('.body_dependente').append(clone);
        add_masks();
    });

    $('body').on('click', '.btn-remove-socio', function () {
        if (typeof $(this).data('id') !== 'undefined') {
            //console.log('teste');
            var id_socio = $(this).data('id');
            bootConfirm('Deseja realmente excluir?', 'Excluindo', function (confirm) {
                if (confirm) {
                    $.post('../actions/action_prestadores.php', {action: 'remover_socio', id_socio: id_socio}, function (data) {
                        //console.log('teste2');
                        bootAlert(data.msg, 'Removendo Sócio', null, data.status);
                    }, 'json');
                }
            }, 'danger');
        }
        if ($('.body_socio tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            $(this).closest('tr').find('input').val('');
            $(this).closest('tr').find('select').val('');
        }
    });

    $('body').on('click', '.btn-remove-dependente', function () {
        //console.log('depentende: ' + $('.body_dependente tr').length);

        if (typeof $(this).data('id') !== 'undefined') {
            var id_dependente = $(this).data('id');
            bootConfirm('Deseja realmente excluir?', 'Excluindo', function (confirm) {
                if (confirm) {
                    $.post('../actions/action_prestadores.php', {action: 'remover_dependente', id_dependente: id_dependente}, function (data) {
                        bootAlert(data.msg, 'Removendo dependente', null, data.status);
                    }, 'json');
                }
            }, 'danger');
        }
        if ($('.body_dependente tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            $(this).closest('tr').find('input').val('');
            $(this).closest('tr').find('select').val('');
        }
    });

});

function testandoCNPJ(cnpj) {
    if (!validarCNPJ(cnpj)) {
        bootAlert('<h4 class="text-warning">CNPJ inválido!</h4><p>Digite o CNPJ correto para salvar Empresa.</p>', null, null, 'warning');
        return false;
    }
    return true;
}

function add_masks() {
    $(".valor").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});
    $(".cep").mask("99999-999", {placeholder: " "});
    $(".telefone").brTelMask();
    $('.cpf').mask('999.999.999-99', {placeholder: " "});
    $('.cnpj').mask('99.999.999/9999-99', {placeholder: " "});
}