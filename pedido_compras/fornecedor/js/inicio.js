$(document).ready(function () {
    /*
     * Aba de Consulta
     */
    $("#consulta").on('click', ".editar", function () {
        $("#method").val('editar');
        $("#id_fornecedor").val($(this).data('id'));
        $("#form1").submit();
    });
    $("#consulta").on('click', ".visualizar", function () {
        $("#method").val('visualizar');
        $("#id_fornecedor").val($(this).data('id'));
        $("#form1").submit();
    });
    $("#consulta").on('click', ".excluir", function () {
        var titulo = "Excluir Formecedor";
        var texto = "Deseja realmente excluir este fornecedor?";
        var $this = $(this);
        var id = $this.data('id');
        bootConfirm(texto, titulo, function (confirm) {
            if (confirm) {
                $.post('index.php', {method: 'excluir', id: id}, function (data) {
                    if (data.status == true) {
                        bootAlert(data.msg, titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });
                            $('#row-' + id).remove();
                        }, 'success');
                    }
                }, 'json');
            }
        }, 'danger');
    });

    $("#tab-consulta").click(function () {
        $("#consulta").html('<div class="text-center"><i class="fa fa-refresh fa-spin fa-5x"></i></div>');
        $.post('index.php', {method: 'consultar'}, function (data) {
            $("#consulta").html(data);
        });
    });

});