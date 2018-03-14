$(document).ready(function () {
    var optionsCadastro = {
        beforeSubmit: showRequest,
        success: showSuccessCadastro,
        dataType: 'json',
    };

    $('body').on('focus', '.money', function () {
        $(".money").maskMoney({thousands: '.', decimal: ','});
    });

    $("#form-cadastro").ajaxForm(optionsCadastro);      // add javaxForm
    $("#form-cadastro").validationEngine();     // add validation engine

    $('.tipo_produto').change(function () {
        var id_regiao = $("#regiao").val();
        var tipo = $(".tipo_produto:checked").val();
        $.post("methods_prod.php", {method: "carregaFornecedor", id_regiao: id_regiao, tipo: tipo}, function (data) {
            $("#fornecedor").html(data);
        });
    });

    $("#fornecedor,.tipo_produto").change(function () {
        var cnpj = $("#fornecedor").val();
        var tipo = $(".tipo_produto:checked").val();
        var id_prod = (typeof $("#id_prod").val() == 'undefined') ? 0 : $("#id_prod").val();
        console.log(id_prod);
        if(id_prod == 0){
            $.post("methods_prod.php", {method: "getFornecedorProjeto", cnpj: cnpj, tipo: tipo, id_prod: id_prod}, function (data) {
                $("#tab_forncecedor_produto_assoc tbody").html('');
                $.each(data, function (i, val) {
                    $("#tab_forncecedor_produto_assoc tbody").append(
                        $("<tr>").append(
                            $("<td>").append(
                                val.projeto_nome,
                                $("<input>", {type: 'hidden', name: 'id_assoc[' + i + ']', value: val.id_assoc}),
                                $("<input>", {type: 'hidden', name: 'id_fornecedor[' + i + ']', value: val.id_prestador})
                            ),
                            $("<td>", {colspan: 2}).append(
                                    $("<div>", {class: 'col-lg-12'}).append(
                                        $("<div>", {class: 'input-group'}).append(
                                        $("<span>", {class: 'input-group-addon'}).append('R$'),
                                        $("<input>", {type: 'text', class: 'form-control money input-sm valor', name: 'valor[' + i + ']'})
                                    )
                                )
                            )
                        )
                    );
                });
            }, 'json');
        }
        
    });

    $("#tab_forncecedor_produto_assoc").on('click', '.excluir', function () {
        var $this = $(this);
        var id = (typeof $this.data('id') !== 'undefined') ?  $this.data('id') : '';
        if (id === '') {
            $this.closest('tr').remove();
        } else {
            bootConfirm("Tem certeza que deseja excluir?", 'Excluindo...', function (confirm) {
                if (confirm) {

                    $.post('methods_prod.php', {method: 'excluir_assoc', id: id}, function (data) {
                        var status = (data.status) ? "sucess" : "danger";
                        if(data.status){
                            $this.closest('tr').remove();
                        }
                        bootAlert(data.msg, 'Excluindo...', null, status);
                    }, 'json');
                }
            }, "danger");
        }
    });
});


function showSuccessCadastro(data) {
    var status = (data.status == true) ? 'success' : 'danger';
    bootAlert(data.msg, 'Salvando...', function () {
        window.history.back();
    }, status);
}

function showRequest(formData, jqForm, options) {
    var BoolImportar = Boolean($("#importar").data("status"));
    if (!BoolImportar) {
        var teste = true;
        $('.valor').each(function () {
            teste = teste && ($(this).val() == '');
        });
        if (teste) {
            bootAlert('Preencha o valor comercial para pelo menos um projeto.', null, null, 'warning');
            return false;
        }
//        var valid = $("#form-xml").validationEngine('validate');
        var valid = $(jqForm).validationEngine('validate');
        return (valid == true) ? true : false;
    }
}