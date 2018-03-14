$(document).ready(function () {

    function showSuccessPedido(data) {
        console.log(data.itens);
        if (typeof data.itens != 'undefined') {
            $("#tab-produtos").removeClass("hide");
            var html = "";
            $.each(data.itens, function (i, item) {
                html += "<tr id=\"tr-item-" + data.itens[i].id_prod + "\">";
                html += "<td class=\"text text-center\">" + data.itens[i].cProd + "<input type=\"hidden\" name=\"idProd[]\" value=\"" + data.itens[i].id_prod + "\"  </td>";
                html += "<td>" + data.itens[i].xProd + "</td>";
                html += "<td class=\"text text-center\">" + data.itens[i].uCom + "</td>";
                html += "<td class=\"text text-right\">" + number_format(data.itens[i].vUnCom, 2, ',', '.') + "<input type=\"hidden\" name=\"vUnCom[]\" id=\"vUnCom-" + data.itens[i].id_prod + "\" class=\"form-control money\" value=" + number_format(data.itens[i].vUnCom, 2, ',', '.') + " ></td>";
                html += "<td><input type=\"text\" class=\"form-control text text-center item_qtde\" name=\"qtde[]\"  data-id=" + data.itens[i].id_prod + " size=\"12\" maxlength=\"12\"></td>";
                html += "<td><input type=\"text\" name=\"vProd[]\" id=\"vProd-" + data.itens[i].id_prod + "\" size=\"12\" class=\"text form-control text-right\" readonly> </td>";
                html += "</tr>";
            });
            $("#tab-produtos tbody").html(html);
        } else if (data.msg) {
            bootAlert('Sem Dados!!!');
        }
    }

    var optionsCadastroPed = {
        success: showSuccessPedido,
        dataType: 'json'
    };

    $("#tab-produtos").on('blur', ".item_qtde", function () {
        var id_prod = $(this).data('id');
        var qtde = parseFloat($(this).val().replace(',', '.'));
        var vUni = parseFloat($("#vUnCom-" + id_prod).maskMoney('unmasked')[0]);
        var valor = (qtde * vUni).toFixed(2);
        valor = number_format(valor, 2, ',', '.');
        $("#vProd-" + id_prod).val(valor);
    });

    $("#gerarpedido").click(function () {
        $('#form-pedido').ajaxFormUnbind();
        $('#form-pedido').attr('action', 'gerarpedido.php');
        $('#form-pedido').submit();
    });

    $("form-enviarPedido").ajaxForm(optionsXML);
    $("#form-pedido").ajaxForm(optionsXML);
    
    $("#form-enviarPedido").ajaxForm({
        beforeSubmit: function () {
            $("#enviarPedido").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            $("#enviarPedido").html(data);
        }
    });

    $("#form-confirmaPedido").ajaxForm({
        beforeSubmit: function () {
            $("#confirmarPedido").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            $("#confirmarPedido").html(data);
        }
    });

    $("#confirmarPedido").on('click', '.pedidos-detalhes', function () {
        console.log($(this).data('id'));
        $.post("confirmapedido.php", {id_pedido: $(this).data('id'), method: 'Detalhes'}, function (dados) {
            BootstrapDialog.show({
                size: 'size-wide',
                nl2br: false,
                title: 'Detalhes do Pedido',
                message: dados
            });
        });
    });

    $("#enviarPedido").on('click', '.enviar-pedido', function () {
        console.log($(this).data('id'));
        $.post("confirmapedido.php", {id_pedido: $(this).data('id'), method: 'Enviar'}, function (dados) {
            BootstrapDialog.show({
                size: 'size-wide',
                nl2br: false,
                title: 'Enviando Pedido',
                message: dados
            });
        });
    });

    $("#cancelar-pedido").on('click', '.pedido-cancelar', function () {
        console.log($(this).data('id'));
        var titulo = 'Cancelar Pedido!';
        var id = $(this).data('id');
        bootConfirm('Deseja prosseguir com o cancelamento ?', titulo, function (status) {
            if (status) {
                $.post('confirmapedido.php', {method: 'cancelapedido', id: id}, function (data) {
                    if (data.status) {
                        bootAlert('Pedido cancelado !', titulo, null, 'success');
                        $('#tr-' + id).remove();
                    } else {
                        bootAlert('Erro ao Cancelar a nota.', titulo, null, 'danger');
                    }
                }, 'json');
            }
        }, 'danger');
    });

    // fim do form-xml ---------------------------------------------
});            