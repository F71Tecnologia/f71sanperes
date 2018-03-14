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
        $.post("../../../methods.php", {method: "carregaProjetos", regiao: $(this).val(), default: 2}, function (data) {
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

    $(".alter_status").click(function () {
        var $this = $(this);
        var status_old = $this.data('status_old');
        if (status_old >= 4) {
            verifica_saidas($this);
        } else {
            alter_status($this);
        }
    });

    function verifica_saidas($this) {
        var id = $this.data('id');
        $.post(window.location.href, {method: 'verifica_saidas', id: id}, function (saida) {
            if (typeof saida == 'object') {
                var st = percorre_saidas(saida);
                if (st == true) {
                    bootAlert('Nota não poderá ser excluida.<br><strong>Motivo:</strong> Existem saidas pagas para essa nota.', 'Atenção', null, 'danger');
                } else {
                    var msg = 'Esta nota possui saídas. Deseja realmente alterar o status dela? As saídas lançadas serão <strong class="text-danger">escluídas</strong>!';
                    alter_status($this, msg);
                }
            } else {
                alter_status($this);
            }
        }, 'json');
    }

    function alter_status($this, msg_confirmacao) {
        if (typeof msg_confirmacao == 'undefined') {
            var text = $this.text();
            msg_confirmacao = 'Deseja realmente alterar o status para <strong>' + text + '</strong>?';
        }

        var id = $this.data('id');
        var status_new = $this.data('status_new');
        var status_old = $this.data('status_old');

        bootConfirm(msg_confirmacao, 'Atenção!', function (confirm) {
            if (confirm) {
                var remove_saidas = status_old >= 4 ? 1 : 0;
                $.post(window.location.href, {method: 'alter_status', id: id, status: status_new, remove_saidas: remove_saidas}, function (retorno) {
                    bootAlert(retorno.msg, 'Atenção!', null, retorno.status);
                    if (retorno.status == 'success') {
                        location.reload();
                    }

                }, 'json');
            }
        }, 'warning');
    }

    function percorre_saidas(saida) {
        var status = false;
        $.each(saida, function (i, v) {
            console.log(v.status);
            if (v.status == 2) {
                status = true;
            }
            console.log(v.status);
            console.log(status);
        });
        return status;
    }


});

