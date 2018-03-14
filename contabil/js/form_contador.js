$(document).ready(function () {
    var optionsCadastro = {
        beforeSubmit: showRequest,
        success: showSuccessCadastro,
        dataType: 'json'
    };
    
    $("#cpf").mask("999.999.999-99");
    $("#telefone").mask("(99)9999-9999");
    $("#celular").mask("(99)99999-9999");
   
    $("#form-cadastro").ajaxForm(optionsCadastro);      // add javaxForm
    $("#form-cadastro").validationEngine();     // add validation engine

    $("#estado").change(function () {
        var estado = $("#estado option:selected").text();
        console.log(estado);
    });

    $('body').on('click', '#edita_contador', function(){
        var $this = $(this);
        var titulo = 'CADASTRO DE CONTADOR - EDIÇÃO'
        var id_contador = $(this).data('id');
        var buttons = [{
            label: 'Confirmar',
            cssClass: 'btn-success',
            action: function (dialog) {
                $.post('contador_controle.php', {method: 'alterar_contador', edita_id_contador: $('#edita_id_contador').val(), edita_cpf: $('#edita_cpf').val(), edita_crc_uf: $('#edita_crc_uf').val(), edita_crc: $('#edita_crc').val(), edita_crc_control: $('#edita_crc_control').val(), edita_nome: $('#edita_nome').val(), 
                        edita_profissional: $('#edita_profissional').val(), edita_telefone: $('#edita_telefone').val(), edita_celular: $('#edita_celular').val(), edita_email: $('#edita_email').val()}, function (data) {
                    if (data == true) {
                        bootAlert('ALTERAÇÃO REALIZADA... ', titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });
                        }, 'success');
                        var funcao = {1:'TÉCNICO CONTABIL',2:'CONTADOR'};
                        $this.closest('tr').find('td:eq(0)').html($('#edita_nome').val());
                        $this.closest('tr').find('td:eq(1)').html($('#edita_cpf').val());
                        $this.closest('tr').find('td:eq(2)').html($('#edita_crc_uf').val()+ " "+ $('#edita_crc').val()+"-"+$('#edita_crc_control').val());
                        $this.closest('tr').find('td:eq(3)').html(funcao[$('#edita_profissional').val()]);
//                        location.reload();
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
        $.post("contador_controle.php", {edita_contador: "edita_contador", id_contador: id_contador}, function (resultado) { 
            bootDialog(resultado, titulo, buttons, 'success');
            
        });       
    });

    $('body').on('click', '#cancelar_contador', function(){
        var titulo = 'GESTÃO CONTABIL - CANCELAMENTO DE CONTADOR';
        var contador = $(this).data('cancelar_id');
        var nome = $(this).data('nome');
        var buttons = [{
            label: 'Confirmar',
            cssClass: 'btn-danger',
            action: function (dialog) {
                $.post('contador_controle.php', {method: 'cancela_contador', contador: contador}, function (data) {
                    if (data == true) {
                        bootAlert('CONTADOR CANCELADO.', titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });
                        }, 'success');
                        $('#tr-' + contador).remove();
                    } else {
                        bootAlert('Erro ao Cancelar Conta', titulo, null, 'danger');
                    }
                }, 'json');
            }
        }, {
            label: 'Cancelar',
            action: function (dialog) {
                dialog.close();
            }
        }];
    
        
        bootDialog('Deseja cancelar o Contador ' + nome ,titulo, buttons, 'danger');
    });


});


function showSuccessCadastro(data) {
    var status = (data.status == true) ? 'success' : 'danger';
    var html = "<div class=\"alert alert-dismissable alert-" + status + "\">\n\
                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>\n"
            + data.msg +
            "\n</div>";
    $("#resp-cadastro").html(html);
}

function showRequest(formData, jqForm, options) {
//    var BoolImportar = Boolean($("#importar").data("status"));
//    if (!BoolImportar) {
////        var valid = $("#form-xml").validationEngine('validate');
        var valid = $(jqForm).validationEngine('validate');
        if (valid == true) {
            $(".loading").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
            return true;
        } else {
            return false;
        }
//    }
}