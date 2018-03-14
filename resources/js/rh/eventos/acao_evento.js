/* 
 * JavaScript da página acao_eventos_novo.php
 */

// funcao para converter data
function converteData(data) {
    var dataArr = data.split('/');
    return dataArr[2] + "-" + dataArr[1] + "-" + dataArr[0];
}

function zerarStatus(id, clt) {
    $.post("index.php", {id: id, clt: clt, set_status: 'zerar', method: 'acao_evento'}, function (data) {
        console.log(data);
        if (data.status) {
            BootstrapDialog.show({
                title: 'Exclusão',
                message: 'Evento deletado com sucesso!',
                buttons: [{
                        label: "OK",
                        action: function (dialog) {
                            dialog.close();
                            window.location.reload();
                        }
                    }]
            });
        } else {
            BootstrapDialog.alert({
                title: 'Exclusão',
                message: 'Não foi possível deletar evento!',
                buttons: [{
                        label: "OK",
                        action: function (dialog) {
                            dialog.close();
                        }
                    }]
            });
        }
    }, 'json');
}

$(document).ready(function () {
    
    // ATIVA O TOOLTIP NOS LINKS
    $(".tip").tooltip();
    var url = "index.php";
    $("body").on("focusin", ".data", function () {
        $(".data").mask("99/99/9999");
        $('.data').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2005:c+1',
            beforeShow: function () {
                setTimeout(function () {
                    $('.ui-datepicker').css('z-index', 5010);
                }, 0);
            }
        });
    });
    
    // recupera o evento anterior e verifica se a data de encerramento é maior q a data de inicio deste
    $("#data").change(function () {
        var id_clt = $("#id_clt").val();
        var dataIniNovo = converteData($(this).val()); // converte a data para o formato em ingles
        $.post("../../methods.php", {method: 'verificaRetorno', id_clt: id_clt}, function (data) {
            console.log("data nova: " + dataIniNovo + "data retorno: " + data.dados.data_retorno);
            if (data.dados.data_retorno > dataIniNovo) {
                alert('ATENÇÂO: Data de início é anteriro à data de retorno do evento atual.');
            }
        }, 'json');
    });
    // habilita novo evento sem que o funcionário volte para atv normal
    var novo_evento = true;
    $("#novo_evento").click(function () {
        var $this = $(this);
        var id_clt = $("#id_clt").val();
        if (novo_evento) {
            var message = "<p class=\"text-justify\">Ao criar um novo evento para este funcionário, ele passará direto do evento atual para o novo evento sem passar por <strong>\"Atividade Normal\"</strong>.</p><p class=\"text-justify\">Deseja realmente criar um novo evento?</p>";
            var title = 'ATENÇÃO!';
            var callback = function (confirmacao) {
                if (confirmacao) {
                    $.post(url, {montaSelectEventos: true, method: 'acao_evento'}, function (data) {
                        $("#cel_evento").html(data);
                        //$("#dias").addClass("validate[required]");
                        novo_evento = false;
                        $this.html("Cancelar Novo Evento");
                    }, 'html');
                    $.post("../../methods.php", {method: 'verificaRetorno', id_clt: id_clt}, function (data) {
                        console.log(data);
                        //var nova_data = (data.dados.pericia == 1) ? data.dados.data_retornoBR : data.dados.data_retornoFinalBR;
                        var nova_data = data.dados.data_retornoBR;
                        // td isso é para colocar um dia a mais na data-
                        var arr_data = nova_data.split('/');
                        var dia = parseInt(arr_data[0]) + 1;
                        dia = ((dia < 10) ? "0" + dia : dia);
                        var mes = arr_data[1];
                        var ano = arr_data[2];
                        var nova_data = dia + "/" + mes + "/" + ano;
                        // fim do dia a mais na data--------------------

                        $("#data").val(nova_data);
//                        $("#data").attr('readonly','readonly');
//                        $("#data").prop('readonly',true);
                        
                        $("#data").css("display", "none");
                        $("#data").before("<div id='label-data' class='input-group-addon'>" + nova_data + "</div>");
                    }, 'json');
                }
            };
            bootConfirm(message, title, callback,'warning');
            
        } else {
            $("#cel_evento").html('<b>Atividade Normal</b><input type="hidden" name="evento" value="10">');
            $("#row_dias").css('display', 'none');
            $("#row_retorno").css('display', 'none');
            $("#row_retorno_final").css('display', 'none');
            $("#data_retorno").val('');
            //$("#dias").removeClass("validate[required]").val('');
            $("#data").val('');
            $("#observacao").val('');
            $("#label-data").remove();
            $("#data").css("display", "initial");
            novo_evento = true;
            $this.html("Novo Evento");
        }
    });
    // habilita o validate engine
    $("#form1").validationEngine();
    // UPLOAD DO ARQUIVO DE EVENTO
    $(".anexar").click(function () {
        var evento = $(this).data("id");
        $("#id_evento").val(evento); // muda o val do input #id_evento
        BootstrapDialog.show({title: "Anexar",
            message: $('<div></div>').load('../../rh/eventos/upload_arquivo.php')
        });
    });
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');
    $('#form_up_evento').validationEngine({promptPosition: "bottomLeft"});
    $('#form_up_evento').ajaxForm({
        clearForm: true,
        beforeSend: function () {
            status.empty();
            var percentVal = '0%';
            bar.width(percentVal);
            percent.html(percentVal);
        },
        uploadProgress: function (event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            $('progress').attr('value', percentComplete);
            $(".progress-bar span").css("width", percentComplete + "%");
            percent.html(percentVal);
        },
        success: function () {
            var percentVal = '100%';
            $('progress').attr('value', '100');
            $(".progress-bar span").css("width", "100%");
            percent.html(percentVal);
        },
        complete: function (xhr) {
            status.html(xhr.responseText);
            status.removeClass("hidden");
        }
    });
    // FIM DO UPLOAD DO ARQUIVO DE EVENTO


    // habilita e desabilita data final
    $("body").on("change", "#evento", function () {
        var evento = $(this).val();
        if (evento != 10) {
            $("#row_data").fadeIn();
            $("#row_dias").fadeIn();
            $("#row_obs").fadeIn();
            $("#row_retorno").fadeIn();
//            $.post("../../methods.php",
//                    {method: 'getPericia', evento: evento},
//            function (data) {
//                if (data.pericia == 1) {
//                    $("#row_retorno_final").css('display', 'none');
//                    $("#data_retorno").val('');
//                    $("#row_retorno").fadeIn();
//                } else {
//                    $("#row_retorno_final").fadeIn();
//                    $("#data_final").val('');
//                    $("#row_retorno").css('display', 'none');
//                }
//            }, 'json');
        }
    });
    // habilita os campos que devem ser exibidos no caso de o 
    // funcionario for voltar para atividade normal
    if ($("#status_clt").val() != 10) {
        $("#row_data").css("display", "block");
        $("#row_obs").css("display", "block");
    }


// excluir evento
    $(".excluir").click(function () {
        var $this = $(this);
        BootstrapDialog.show({
            type: 'type-danger',
            title: 'Excluir Evento',
            message: 'Deseja realmente excluir este evento?',
            buttons: [{
                    label: 'Sim',
                    cssClass: 'btn-danger',
                    action: function (dialog) {
                        var id_clt = $this.data("id-clt");
                        var id_evento = $this.data("id-evento");
                        console.log(id_clt + ' - ' + id_evento);
                        zerarStatus(id_evento, id_clt);
                        dialog.close();
                    }
                }, {
                    label: 'Não',
                    action: function (dialog) {
                        dialog.close();
                    }
                }]
        });
    });
    
    $(".link_go").click(function(){
        $("#id_evento").val($(this).data('id-evento'));
        $("#method").val($(this).data('method'));
        $("#form1").validationEngine('detach').submit();
    });
    
});


