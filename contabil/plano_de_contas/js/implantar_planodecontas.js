$(document).ready(function () {
 
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    $("input[name='classificador']").mask('?9.99.99.99.99.99.99');

    $("body").on('click', '#exibe-contas', function(){
        $.post("saldo_contas_controle.php", {method: "implantarplanodecontas", id_projeto: $('#projeto').val()}, function (resultado) { 
            $("#lista-planos").html(resultado);
            $(".div-lista-planos").removeClass('hidden');
            $(".div-lista-filtro").addClass('hidden');
        });
    });

    $('body').on('click', '#implantar_saldo', function () {
        var titulo = 'PLANO DE CONTA - IMPLANTAÇÃO DE SALDO'
        var id_conta = $(this).data('id');
        var id_projeto = $(this).data('projeto');
        var buttons = [{
            label: 'Confirmar',
            cssClass: 'btn-success',
            action: function (dialog) {
                $.post('saldo_contas_controle.php', {method: 'salvar_ajuste_saldo', saldoconta: id_conta, saldovalor: $('#saldo_valor').val(), saldoprojeto: id_projeto, }, function (data) {
                console.log(data);
                    if (data == true) {
                        bootAlert('SALDO DA CONTA REALIZADA... ', titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });
                        }, 'success');
                        $('#exibe-contas').trigger('click');
                    } else {
                        bootAlert(data, titulo, null, 'danger');
                    }
                }, 'json');
            } 
        }, {
            label: 'Cancelar',
            action: function (dialog) {
                dialog.close();
            }
        }];
        $.post("saldo_contas_controle.php", {implantar_saldo: "implantar_saldo", id_conta: id_conta, id_projeto: id_projeto}, function (resultado) {
            bootDialog(resultado, titulo, buttons, 'success');
        });
    });

    $("body").on('click', '.back', function () {
        $(".div-lista-planos, .div-lista-filtro").addClass('hidden');
        $("."+$(this).data('show')).removeClass('hidden');
    });
});