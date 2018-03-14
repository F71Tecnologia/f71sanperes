$(document).ready(function () {
    $('body').on('click', '.btn_associacao', function () {
        var $this = $(this);
        var btn = [{
                label: 'cancelar',
                action: function (dialog) {
                    dialog.close();
                }
            }, {
                label: '<i class="fa fa-floppy-o"></i> Salvar',
                cssClass: 'btn-info',
                action: function (dialog) {
                    salvar(dialog,$this);
                }
            }];
        var id_clt = $(this).data('id_clt');
        var id_unidade = $('#id_unidade').val();
        $.post('form_assoc.php', {id_clt: id_clt, id_unidade: id_unidade}, function (data) {
            bootDialog(data, 'Formulário de Associação', btn, 'info');
        });
    });

    $('body').on('change', '#portal', function () {
        var valor = $(this).val();
        if (valor == 1) {
            $("#modulos").fadeIn();
        } else {
            $("#modulos").fadeOut();
        }
    });

});

function salvar(dialog,$this) {
    $("#form_assoc").ajaxSubmit({
        dataType: 'json',
        data: {method:'salvar'},
        success: function (data) {
            var style = (data.status) ? 'success' : 'danger';
            bootAlert(data.msg, 'Salvando...', function () {
                if (style) {
                    $.each(BootstrapDialog.dialogs, function (id, dialog) {
                        dialog.close();
                    });
                }
            }, style);
        }
    });
}
