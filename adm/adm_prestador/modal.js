/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
    $("#modal_voltar").on("click", function() {
        $("#modal_geral").animate({left: "0px"});
        $("#modal_lado2 div:first").html("");
    });

    $(".modal-bt").on("click", function() {
        var $this = $(this);
        var action = $this.data("act");
        var doc = $this.data("doc");
        var prest = $this.data("prest");

        //VER OU LISTAR
        if (action === "ver") {
            $.post('actions.php', {id_doc: doc, id_prestador: prest, method: "showDocs"}, function(data) {
                $("#modal_lado2 div:first").html(data);
                $("#modal_geral").animate({left: "-590px"});
                gridZebra("#tableDoc");
                acoesTela2();
            });
        }
        //ANEXAR NOVO DOCUMENTO
        if (action === "anexar") {
            $.post('actions.php', {id_doc: doc, id_prestador: prest, method: "novoDoc"}, function(data) {
                $("#modal_lado2 div:first").html(data);
                $("#modal_geral").animate({left: "-590px"});
                acoesTela2();
            });
        }
    });

    $("#modal_novodoc").on("click", function() {
        $("#upload_doc").removeClass("hidden");
    });


});

//ACOES DA 2 PARTE DO MODAL
var acoesTela2 = function() {
    $(".data").mask("99/99/9999");
    $(".modal-bt-det").click(function() {
        var tr = $(this).parents("tr");
        var id = $(this).data('key');
        var acao = $(this).data('type');
        var tipoDoc = $(this).data('tipo');
        var prest = $(this).data('prest');

        if (acao === "editar") {
            $(".marcado").removeClass("marcado");
            tr.addClass("marcado");
            var dt = $("span", tr).html();
            $("#edicao_data").removeClass("hidden");

            $("#id_edit").val(id);
            $("#edit_data").val(dt);
        } else {
            if (confirm("Essa acao e irreversivel, deseja realmente apagar esse documento?")) {
                $.post("actions.php", {id: id, method: "excluirDoc"}, function(data) {
                    if (data.status == "1") {
                        $(".modal-bt[data-prest=" + tipoDoc + "]").trigger("click");
                        $('.modal-bt[data-doc=' + tipoDoc + '][data-prest=' + prest + ']').trigger('click');

                        var trQtd = $('.modal-bt[data-doc=' + tipoDoc + '][data-prest=' + prest + ']').parent().prev();
                        var qtd = parseInt(trQtd.html());                       // pega o valor html da tag e transforma em inteiro
                        qtd = qtd - 1;                                          // soma o valor mais 1
                        trQtd.html(qtd);                                        // coloca o novo valor na tag
                        if(qtd === 0){
                            $('.modal-bt[data-doc=' + tipoDoc + '][data-prest=' + prest + ']').attr('src','../../img_menu_principal/anexar.png');
                            $('.modal-bt[data-doc=' + tipoDoc + '][data-prest=' + prest + ']').data('act','anexar');
                        }
                    }
                    ;
                }, "json");
            }
        }
    });

    $("#bt-cancel").click(function() {
        $("#edicao_data").addClass("hidden");
        $(".marcado").removeClass("marcado");
        $("#edit_data").val("");
    });

    $("#bt-cancelup").on("click", function() {
        $("#upload_doc").addClass("hidden");
        $("#nova_data").val("");
    });

    $("#bt-salvar").click(function() {
        showLoading($("#edit_data"), "../");
        var idEd = $("#id_edit").val();
        var novaDt = $("#edit_data").val();

        $.post("actions.php", {id: idEd, valor: novaDt, method: "editaDataDoc"}, function(data) {
            if (data.status != "1") {
                alert("Erro ao alterar a data");
            }

            removeLoading();
            $("#edicao_data").addClass("hidden");
            $(".marcado").removeClass("marcado");
            $(".modal-bt-det[data-key=" + idEd + "]").parents("tr").find("span").html(novaDt);
        }, "json");

    });

    $('#bt-enviar').on("click", function() {
        showLoading($("#nova_data"), "../");
        $("#form1").submit();

    });
}