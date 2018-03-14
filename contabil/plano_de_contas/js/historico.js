$(document).ready(function () {

    $('body').on('click',"#btn_add",function () {
        var html =
                $('<form>', {action: 'controle.php', id: 'cad_historico', method: 'post'}).append(
                    $('<textarea>', {class: 'form-control', id: 'texto', name: 'texto', placeholder: 'Digite o Histórico Padrão aqui.', col: 3})
                );
        var botoes = [
            {
                id: 'fechar',
                label: '<i class="fa fa-times"></i> Fechar',
                action: function (dialog) {
                    dialog.close();
                }
            },
            {
                id: 'salvar',
                label: '<i class="fa fa-floppy-o"></i> Salvar',
                cssClass: 'btn-primary',
                action: function (dialog) {
                    dialog.close();
                    Salvar();


                }
            }
        ];
        bootDialog(html, 'Cadastor de Histórico', botoes);
    });

    $('body').on('click',".btn_excluir",function () {
        var $this = $(this);
        var id = $this.data('id');
        bootConfirm('Deseja mesmo excluir esse Histórico?', 'Excluindo...', function (confirm) {
            if (confirm) {
                $.post('controle.php', {method: 'excluir', id: id}, function (data) {
                    if (data.status) {
                        bootAlert('Excluido com successo!', 'Excluindo...', null, 'success');
                        $this.closest('tr').remove();
                    } else {
                        bootAlert('Erro ao Excluir!', 'Excluindo...', null, 'danger');
                    }
                }, 'json');
            }
        }, 'danger');
    });

    $('body').on('click','.btn_editar',function () {
        var $this = $(this);
        var id = $this.data('id');
        $.post('controle.php', {method: 'getHistorico', id: id}, function (data) {
            var html =
                    $('<form>', {action: 'controle.php', id: 'cad_historico', method: 'post'}).append(
                        $('<input>', {class: 'form-control', id: 'id_historico', name: 'id_historico', value: data.dados.id_historico, type: 'hidden'}),
                        $('<textarea>', {class: 'form-control', id: 'texto', name: 'texto', placeholder: 'Digite o Histórico Padrão aqui.', col: 3}).append(data.dados.texto)
                    );
            var botoes = [
                {
                    id: 'fechar',
                    label: '<i class="fa fa-times"></i> Fechar',
                    action: function (dialog) {
                        dialog.close();
                    }
                },
                {
                    id: 'salvar',
                    label: '<i class="fa fa-floppy-o"></i> Salvar',
                    cssClass: 'btn-primary',
                    action: function (dialog) {
                        Salvar($this);
                        dialog.close();
                    }
                }
            ];
            bootDialog(html, 'Edição de Histórico', botoes);
        }, 'json');

    });

    $('body').on('click',".btn_vincular",function(){
        
    });
});

function Salvar($this) {
    $("#cad_historico").ajaxSubmit({
        dataType: 'json',
        data: {method: 'salvar'},
        forceSync: true,
        success: function (data, statusText, xhr, $form) {
            console.log(statusText);
            console.log(xhr);
            console.log($form);
            if (data.status) {
                bootAlert("Histórico salvo com o código: <strong>" + data.id + "</strong>", 'Savando...', null, 'success');

                if (typeof ($this) !== 'undefined') {
                    changeTr($this);
                } else {
                    putTr(data);
                }

            } else {
                bootAlert("Erro ao salvar!", 'Salvando...', null, 'danger');
            }
        }
    });
}

function putTr(data) {
    $("#no_info").remove();
    $("#tbl_historico tbody").append(
        $('<tr>').append(
            $('<td>', {class: 'text-center'}).append(data.id),
            $('<td>').append($("#texto").val()),
            $('<td>', {class: 'text-right'}).append(
                $('<button>', {class: 'btn btn-success btn_editar btn-xs', 'data-id': data.id}).append($('<i>', {class: 'fa fa-pencil'}), ' Editar'), " ",
                //$('<button>', {class: 'btn btn-info btn_vincular btn-xs', 'data-id': data.id}).append($('<i>', {class: 'fa fa-link'}), ' Vincular'), " ",
                $('<button>', {class: 'btn btn-danger btn_excluir btn-xs', 'data-id': data.id}).append($('<i>', {class: 'fa fa-trash'}), ' Excluir')
            )
        )
    );
}

function changeTr($this) {
    var texto = $("#texto").val();
    $this.closest('tr').find('td:eq(1)').html(texto);
}
