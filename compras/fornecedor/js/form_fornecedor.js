$(document).ready(function () {
    $("#cpf").mask("999.999.999-99", {placeholder: " "});
    $("#cnpj").mask("99.999.999/9999-99", {placeholder: " "});
    $("#cep").mask("99999-999", {placeholder: " "});
    $(".tel").brTelMask();

    $(".tel").trigger('focus'); // força mascara quando for o formulario de edicao

    $("#site").change(function () {
        var $this_val = $(this).val();
        if (!XOR($this_val.indexOf('http://') === -1, $this_val.indexOf('https://') === -1) && $this_val.length > 0) {
            $(this).val('http://' + $this_val);
        }
    });

    $("#form-fornecedor").ajaxForm({
        beforeSubmit: function (formData, jqForm, options) {
            var t1 = testandoCNPJ($("#cnpj").val());
            var t2 = jqForm.validationEngine('validate');
            return t1 && t2;
        },
        success: function (data) {
            var type = (data.status === true)?'success':'danger';
            bootAlert(data.msg, "Salvando...", function () {
                if (typeof $('#id_fornecedor_form').val() !== 'undefined') {
                    window.location.href = 'index.php';
                }
            },type);

        },
        clearForm: true,
        dataType: 'json'
    });// add javaxForm
    $("#form-fornecedor").validationEngine();// add validation engine


    // preenche dados do endereco a partir do cep
    $('#cep').blur(function () {

        var $this = $(this);
        var cep_atual = $this.val();
        $this.after('<i class="fa fa-refresh fa-spin form-control-feedback" id="img_load_cep"></i>');
        $('#endereco').attr('disabled', 'disabled');
        $('#bairro').attr('disabled', 'disabled');
        $('#uf').attr('disabled', 'disabled');
        $('#mun').attr('disabled', 'disabled');
        $('#cod_ibge').attr('disabled', 'disabled');

        var cep = $this.val();
        $.post('../../busca_cep.php', {cep: cep, id_municipio: 1, municipios: 1}, function (data) {

            $('#endereco').removeAttr('disabled');
            $('#bairro').removeAttr('disabled');
            $('#uf').removeAttr('disabled');
            $('#mun').removeAttr('disabled');
            $('#cod_ibge').removeAttr('disabled');
            $('#img_load_cep').remove();

            if (data.cep == '') {
                bootAlert('Cep não encontrado!');
            } else {
                $("#mun").autocomplete({source: data.municipios,
                    change: function (event, ui) {
                        if (event.type == 'autocompletechange') {
                            var valor_municipio = ui.item.value.split(')-');
                            $('#cod_ibge').val(valor_municipio[0].trim().substring(1, 5));
                            $('#mun').val(valor_municipio[1].trim());
                        }
                    }
                });
                var logradouro = data.logradouro.split(' - ');
                $('#endereco').val(logradouro[0]);
                $('#bairro').val(data.bairro);
                $('#uf').val(data.uf);
                $('#mun').val(data.cidade);
                $('#cod_ibge').val(data.id_municipio);

                if (data.cep != cep_atual) {
                    $('#numero').val('');
                    $('#complemento').val('');
                }
            }

        }, 'json');

    });


    // valida cnpj
    $("#cnpj").change(function () {
        var $this = $(this);
        testandoCNPJ($this.val());
    });

    /* carrega municípios para o campo cidade */
    $('#uf').change(function () {
        var uf = $('#uf').val();
        $('#cidade').val('');
        $('#cod_ibge').val('');
        $.post('../../busca_cep.php', {uf: uf, municipios: 1}, function (data) {
            console.log(data.municipios);
            $("#mun").autocomplete({source: data.municipios,
                change: function (event, ui) {
                    console.log('aloha');
                    if (event.type == 'autocompletechange') {
                        var valor_municipio = ui.item.value.split(')-');
                        $('#cod_ibge').val(valor_municipio[0].trim().substring(1, 5));
                        $('#mun').val(valor_municipio[1].trim());
                    }
                }
            });

        }, 'json');
    });

});

function testandoCNPJ(cnpj) {
    if (!validarCNPJ(cnpj)) {
        bootAlert('<h4 class="text-warning">CNPJ inválido!</h4><p>Digite o CNPJ correto para salvar Fornecedor.</p>', null, null, 'warning');
        return false;
    }
    return true;
}