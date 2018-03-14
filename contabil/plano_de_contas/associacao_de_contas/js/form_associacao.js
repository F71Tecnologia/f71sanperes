$(document).ready(function () {
    var optionsCadastro = {
        beforeSubmit: showRequest,
        success: showSuccessCadastro,
        dataType: 'json'
    };
    
    $("#form-cadastro").ajaxForm(optionsCadastro);      // add javaxForm
    $("#form-cadastro").validationEngine();     // add validation engine

    $('body').on('click', '#cancelar_assoc', function(){
        var titulo = 'GESTÃO CONTABIL CANCELAMENTO DA ASSOCIAÇÃO ENTRE AS CONTAS DO FINANCEIRO E DO  PLANO DE CONTA';
        var conta = $(this).data('cancelar_id');
        var nome = $(this).data('nome_id');
        var descricao = $(this).data('descricao');
        var buttons = [{
            label: 'Confirmar',
            cssClass: 'btn-danger',
            action: function (dialog) {
                $.post('contas_assoc_controle.php', {method: 'cancelar_assoc', id: conta, nome: nome, descricao :descricao}, function (data) {
                    if (data == true) {
                        bootAlert('ASSOCIAÇÃO DA CONTA', titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (conta, dialog) {
                                dialog.close();
                            });
                        }, 'success');
                        $('#tr-' + conta).remove();
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
        
        bootDialog('Deseja cancelar a associaçao : ' + nome + ' X ' + descricao, titulo, buttons, 'danger');
    });

    $('body').on('change', '#folha', function(){
        $.post('contas_assoc_controle.php',{method:'folha', folha:$('#folha').val()},function(data){
            $('#finan_despesa').html(data);
            
        });
    });
    
}); 
 
function exibe(id){
    if(document.getElementById(id).style.display=="none") {
        document.getElementById(id).style.display = "inline";
    } 
    else {
	document.getElementById(id).style.display = "none";
    }
} 

function showSuccessCadastro(data) {
    var status = (data.status == true) ? 'success' : 'danger';
    var html = "<div class=\"alert alert-dismissable alert-" + status + "\">\n\
                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>\n"
            + data.msg +
            "\n</div>";
    $("#resp-cadastro").html(html);
}

function showRequest(formData, jqForm, options) {
    var valid = $(jqForm).validationEngine('validate');
    if (valid == true) {
        $(".loading").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        return true;
    } else {
        return false;
    }
}