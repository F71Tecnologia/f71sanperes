/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {

    $(".bt-box").click(function () {
        var id = $(this).data('key');
        $("#menu-padrao").hide('slow');
        $("#low-menu").removeClass("hide").show('slow');
        $("div[name^='modulo']").addClass('hide');
        $("div[name='modulo_" + id + "']").removeClass("hide");
        $(".bs-glyphicons-list").show();
    });
    
    $(".return_principal").on("click", function(){
        var key = $(this).data("key");
        
        $("#home").val(key);
        $("#form1").unbind();
        $("#form1").attr('action','../index_bts.php');//TIRAR _BTS
        $("#form1").submit();
    });
    
    var home = $("#home").val();
    if(home != ""){
        var id = home;
        $("#menu-padrao").hide('slow');
        $("#low-menu").removeClass("hide").show('slow');
        $("div[name^='modulo']").addClass('hide');
        $("div[name='modulo_" + id + "']").removeClass("hide");
        $(".bs-glyphicons-list").show();
    }        
    
    $("#volta-principal").click(function () {
        $("#menu-padrao").removeClass('hide').show('slow');
        $("#low-menu").hide('slow');
        $("div[name^='modulo']").addClass('hide');
    });
    
    $("#anexo_cad").click(function () {
        if ($(this).is(":checked")) {
            $("#file_cadsuporte").show();
        } else {
            $("#file_cadsuporte").hide();
            $("#file_cadsuporte input").val('');
        }
    });

    $("#volta_index").click(function () {
        var nivel = $(this).data('nivel');
        var _nivel = "";
        if (nivel !== "undefined") {
            for (var i = 0; i < nivel; i++) {
                _nivel += "../";
            }
        }
        //console.log(nivel);
        //console.log(_nivel, 'nivel');
        $(window.location).attr('href', _nivel + 'index.php');
    });

    //acao de clique ao voltar
//    var volta = $("#volta").val();      
//    if(volta != ''){
//        $("#filtrar").trigger("click");
//    }

    $('#low-menu').tooltip();

    if ($("#msg").val() != '') {
        $(".msg_cadsuporte").show();
    }

    /*$(".bs-glyphicons-list li").click(function(){
     var url = $(this).data('url');
     var key = $(this).data('key');
     
     document.location = url;
     
     });*/

    // Botão VOLTAR AO TOPO
    // Determinamos quando aparece ou desaparece 
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {// Se estivermos 100px ou mais abaixo da página, o botão aparece 
            $('#top').fadeIn();
        } else {
            $('#top').fadeOut(); // Caso contrário, desaparece 
        }
    });

    // O que acontece quando é clicado 
    $('#top').click(function () { // Quando o botão é clicado 
        $("html, body").animate({scrollTop: 0}, 'slow'); // A nossa página vai fazer "scrollTop" a uma velocidade de 600 
        return false;
    });
    // Fim do Botão VOLTAR AO TOPO

    // coloca datepicker em qualquer input com classe .data
    if (typeof $.datepicker === 'object') {
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
    }
    // coloca marcara em qualquer input com a classe .data
    if (typeof $.mask === 'object') {
        $(".data").mask("99/99/9999", {placeholder: " "});
    }

});

// BootstrapDialog -------------------------------------------------------------
if (typeof BootstrapDialog === 'function') {
    /**
     * Alert window
     * 
     * @param {type} message
     * @param {type} title
     * @param {type} callback
     * @param {type} type
     * @returns {undefined}
     */
    BootstrapDialog.alert = function (message, title, callback, type) {
        if (typeof title === 'undefined' || title === '' || title === null) {
            title = 'Alerta';
        }
        if (typeof type === 'undefined' || type === '' || type === null) {
            type = 'primary';
        }
        new BootstrapDialog({
            type: 'type-' + type,
            title: title,
            message: message,
            data: {
                'callback': callback
            },
            closable: false,
            buttons: [{
                    label: 'OK',
                    action: function (dialog) {
                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                        dialog.close();
                    }
                }]
        }).open();
    };

    /**
     * Confirm window
     * 
     * @param {type} message
     * @param {type} tyle
     * @param {type} callback
     * @param {type} type
     * @returns {undefined}
     */
    BootstrapDialog.confirm = function (message, title, callback, type) {
        if (typeof title === 'undefined' || title === '' || title === null) {
            title = 'Confirmação';
        }
        if (typeof type === 'undefined' || type === '' || type === null) {
            type = 'primary';
        }
        new BootstrapDialog({
            title: title,
            message: message,
            closable: false,
            type: 'type-' + type,
            data: {
                'callback': callback
            },
            buttons: [{
                    label: 'Cancelar',
                    action: function (dialog) {
                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                        dialog.close();
                    }
                }, {
                    label: 'OK',
                    cssClass: 'btn-' + type,
                    action: function (dialog) {
                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                        dialog.close();
                    }
                }]
        }).open();
    };

    /*
     * alias para alert
     */
    var bootAlert = function (message, title, callback, type) {
        BootstrapDialog.alert(message, title, callback, type);
    };

    /*
     * alias para Confirm
     */
    var bootConfirm = function (message, title, callback, type) {
        BootstrapDialog.confirm(message, title, callback, type);
    };

    /*
     * alias para Dialog
     */
    var bootDialog = function (message, title, buttons, type) {
        if (typeof title === 'undefined' || title === '' || title === null) {
            title = 'Mensagem';
        }
        if (typeof type === 'undefined' || type === '' || type === null) {
            type = 'primary';
        }
        BootstrapDialog.show({
            nl2br: false,
            title: title,
            message: message,
            type: 'type-' + type,
            buttons: buttons
        });
    };

    $('.sonumeros').keypress(function (event) {
        var tecla = (window.event) ? event.keyCode : event.which;
        if ((tecla > 47 && tecla < 58))
            return true;
        else {
            if (tecla != 8)
                return false;
            else
                return true;
        }
    });

    // funcao para converter data do formato br para o americano
    var converteData = function (data, separador_atual) {
        if (separador_atual == '') {
            separador_atual = '/';
        }
        var dataArr = data.split(separador_atual);
        var separador = ((separador_atual === '/')?'-':'/');
        return dataArr[2] + separador + dataArr[1] + separador + dataArr[0];
    }
}