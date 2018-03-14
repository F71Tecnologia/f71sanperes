$(document).ready(function () {


    $("#id_regiao").change(function () {
        var $this = $(this);
        $this.after('<i class="fa fa-refresh fa-spin pull-right" style="margin: -25px"></i>');
        $.post('../../methods.php', {method: 'carregaProjetos', regiao: $this.val()}, function (dados) {
            $this.next().remove();
            $("#id_projeto").html(dados);
        });
    });

    $("#form_consulta").ajaxForm({
        success: function (data) {
            if (!empty(data)) {
                $("#tabela_prestadores").removeClass('hidden').html(data);
            } else {
                $("#tabela_prestadores").addClass('hidden').html('');
            }
        }
    });

    $("#tabela_prestadores").on('click', '.btn-imposto', function () {
        var $this = $(this);
        var id_prestador = $this.data('id');
        $.post('controle_impostos.php', {method: 'form_impostos', id_prestador: id_prestador}, function (data) {
            var botoes = [{
                    label: '<i class="fa fa-times"></i> Fechar',
                    action: function (dialog) {
                        dialog.close();
                    }
                }, {
                    label: '<i class="fa fa-floppy-o"></i> Salvar',
                    cssClass: 'btn-primary',
                    action: function (dialog) {
                        $("#form_impostos").ajaxSubmit({
                            dataType: 'json',
                            data: {method: 'salvar_impostos'},
                            beforeSubmit: function (arr, $form, options) {
//                                return $form.validationEngine('validate');
                            },
                            success: function (data) {

                                bootAlert(data.msg, 'Salvando Impostos', function () {
                                    BootstrapDialog.closeAll();
                                }, data.status);
                            }
                        });

                    }
                }];
            bootDialog(data, 'Gestão de Impostos', botoes);
        });
    });

    $("body").on('click', '#add_tr_imposto', function () {
        var clone = $("#tbl-impostos tbody tr:last").clone();
        clone.find('input').val('');
        clone.find('select').val('');
        $("#tbl-impostos tbody").append(clone);
    });

    $("body").on('click', '.btn_remove', function () {
        var $this = $(this);
        var id = $this.data('id');
        if (empty(id)) {
            console.log($('#tbl-impostos tbody tr').length);
            if ($('#tbl-impostos tbody tr').length > 1) {
                $this.closest('tr').remove();
            } else {
                $this.closest('tr').find('input').val('');
                $this.closest('tr').find('select').val('');
            }
        } else {
            bootConfirm('Tem certeza que quer excluir Esse imposto?', 'Excluindo...', function (confirm) {
                if (confirm) {
                    excluir_imposto(id,$this);
                }
            }, 'danger');
        }
    });

    $("body").on('focus', '.money', function () {
        $('.money').maskMoney({
            thousands: '.',
            decimal: ','
        });
    });

    if (parseInt($("#id_regiao").val()) > 0) {
        $("#id_regiao").trigger('change');
    }
});

function excluir_imposto(id, $this) {
    $.post('controle_impostos.php', {method: 'excluir_imposto', id_assoc: id}, function (data) {
        bootAlert(data.msg, 'Excluindo...', null, data.status);
        if (data.status === 'success') {
            $this.closest('tr').remove();
        }
    }, 'json');

}


function empty(mixed_var) {
    //  discuss at: http://phpjs.org/functions/empty/
    // original by: Philippe Baumann
    //    input by: Onno Marsman
    //    input by: LH
    //    input by: Stoyan Kyosev (http://www.svest.org/)
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Onno Marsman
    // improved by: Francesco
    // improved by: Marc Jansen
    // improved by: Rafal Kukawski
    //   example 1: empty(null);
    //   returns 1: true
    //   example 2: empty(undefined);
    //   returns 2: true
    //   example 3: empty([]);
    //   returns 3: true
    //   example 4: empty({});
    //   returns 4: true
    //   example 5: empty({'aFunc' : function () { alert('humpty'); } });
    //   returns 5: false

    var undef, key, i, len;
    var emptyValues = [undef, null, false, 0, '', '0'];

    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixed_var === emptyValues[i]) {
            return true;
        }
    }

    if (typeof mixed_var === 'object') {
        for (key in mixed_var) {
            // TODO: should we check for own properties only?
            //if (mixed_var.hasOwnProperty(key)) {
            return false;
            //}
        }
        return true;
    }

    return false;
}

