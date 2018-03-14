$(document).ready(function () {
    
//    $("#projeto").change(function(){
//        $.post("../../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
//            $("#prestador").html(data);
//        });
//    });
    
    $("#data_ini").datepicker({
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            $("#data_fim").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#data_fim").datepicker({
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            $("#data_ini").datepicker("option", "maxDate", selectedDate);
        }
    });

    $('#id_regiao').change(function () {
        $.post("../../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#projeto").html(data);
        });
    });

    $('.btn_excluir').click(function () {
        var $this = $(this);
        var id = $this.data('id');
        var titulo = "Exclusão";
        bootConfirm("Tem certeza que deseja Excluir?", titulo, function (confirm) {
            $.post(window.location.href, {method: 'excluir', id: id}, function (data) {
                bootAlert(data.msg, titulo, null, data.status);
                if (data.status === 'success') {
                    $this.closest('tr').remove();
                }
            }, 'json');
        }, 'danger');

    });

    $(".btn_detalhes").click(function () {
        $.post('nfse_visualizacao.php', {id: $(this).data('id')}, function (data) {
            BootstrapDialog.show({
                nl2br: false,
                title: 'Detalhes da Nota Fiscal de Serviço',
                message: data,
                type: 'type-info',
                size: 'size-wide',
                buttons: [
                    {
                        id: 'fechar',
                        label: '<i class="fa fa-times"></i> Fechar',
                        action: function (dialog) {
                            dialog.close();
                        }
                    }
                ]
            });
        });
    });

});