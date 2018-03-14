
/**
 * Script .js de rotinas globais da intranet
 * 
 * @file      global.js
 * @license   F71
 * @link      http://www.f71lagos.com/intranet/js/global.js
 * @copyright 2016 F71
 * @author    <Indefinido>
 * @package   global
 * @access    public
 * 
 * @version: 3.0.09987 - 11/11/2016 - Jacques - Adição de chamada dos script jquery.blockUI.js e isOnLine.js para monitoramento de conexão com a internet 
 * 
 * 
 */

$(function () {
    
    $.getScript( "/intranet/js/jquery.blockUI.js" );   
    //$.getScript( "/intranet/js/isOnLine.js" );

    /* Brazilian initialisation for the jQuery UI date picker plugin. */
    /* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
    if ($.isFunction($.fn.datepicker)) {
        jQuery(function ($) {
            $.datepicker.regional['pt-BR'] = {
                closeText: 'Fechar',
                prevText: '&#x3c;Anterior',
                nextText: 'Pr&oacute;ximo&#x3e;',
                currentText: 'Hoje',
                monthNames: ['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                dayNames: ['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado'],
                dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                dayNamesMin: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 0,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
        });
    }
    if (jQuery("body").attr("data-type")) {
        var idshow = jQuery("#abashow").val();
        jQuery(".colDir > div").hide();
        jQuery("#item" + idshow).show();

        if (idshow > 1) {
            jQuery(".bt-menu").removeClass("aselected");
            jQuery(".bt-menu[data-item=" + idshow + "]").addClass("aselected");
        }

        jQuery(".bt-menu").click(function () {
            var $bt = jQuery(this);
            var id = '#item' + $bt.attr("data-item");
            jQuery("div[id^=item]").hide();

            jQuery(id).show();
            jQuery(".bt-menu").removeClass("aselected");
            $bt.addClass("aselected");
        });
    }
    ;

    jQuery("body").on('click', '#checkAll,.checkAll', function () {
        var attrName = $(this).attr('data-name');
        var attrType = $(this).attr('data-type');
        var type = "name";
        if(attrType!=null){
            type = "attrType";
        }
        
        if ($(this).is(":checked")) {
            jQuery(":checkbox["+type+"^=" + attrName + "]").prop("checked", true);
        } else {
            jQuery(":checkbox["+type+"^=" + attrName + "]").prop("checked", false);
        }
    });
//    Necessário:
//  /js/jquery.price_format.2.0.min.js
    if ($.isFunction($.fn.priceFormat)) {
        $('input.money').priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
    }

//    Necessário:
//      jquery/datepicker-lite/jquery-ui-1.8.4.custom.css
//      jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js
    if ($.isFunction($.fn.datepicker)) {
        $('.date_f').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        });
    }

    $('.bt-troca-regiao').click(function () {
        var regiao = $(this).data('key');
        var regiao_de = $("#regiao-ativa").data('key');
        var baseUrl = location.protocol+"//"+location.host+"/intranet/";
        $.post(baseUrl + "methods.php", {method: "trocaRegiao", regiao: regiao, regiao_de: regiao_de}, function (data) {
            if (data.status == "1") {
                location.href = '';
            }
        }, "json");
    });

    $('.bt-troca-master').click(function () {
        var master = $(this).data('key');
        var master_de = $("#master-ativo").data('key');
        var baseUrl = location.protocol+"//"+location.host+"/intranet/";
        $.post(baseUrl + "methods.php", {method: "trocaMaster", master: master, master_de: master_de}, function (data) {
            if (data.status == "1") {
                location.href = '';
            }
        }, "json");
    });



});

$(document).ready(function () {
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
});

var thickBoxAlert = function (title, text, width, height, callback) {
    thickBox(title, "<p>" + text + "</p>", width, height, callback, "");
}

var thickBoxConfirm = function (title, text, width, height, callback, optionesc) {
    thickBox(title, "<p>" + text + "</p>", width, height, callback, "confirm", optionesc);
}

/**
 * param string title
 */

var thickBoxModal = function (title, objeto, height, width, callback, onload) {

    if (callback == undefined) {
        callback = function () {
        };
    }
    if (onload == undefined) {
        onload = function () {
        };
    }

    $(objeto).dialog({
        height: height,
        width: width,
        modal: true,
        closeOnEscape: true,
        close: callback,
        open: onload,
        title: title,
        position: "top"
                //resizable: false
    });

}

var thickBoxIframe = function (title, url, data, width, height, callback, asynchro) {
    $("#thickBox-iframe").remove();
    var d = document.createElement("div");
    $(d).attr("id", "thickBox-iframe");
    $(d).css("thickBox");
    $(d).attr("title", title);

    $(d).html("Aguarde carregando...");

    if (asynchro == "" || asynchro == "undefined") {
        asynchro = false;
    }

    //PARA TRAVAR E NÃO DEIXAR AUMENTAR O MODAL, PASSE COMO PARAMETRO WIDTH = '600-not'
    //DESSA FORMA A FUNÇÃO IRA TRAVAR O RESIZABLE PARA FALSE
    var resizable = false;
    if (typeof width === "number") {
        resizable = true;
    } else {
        resizable = false;
        width = width.replace(/[^0-9]/g, "");
    }

    $.ajax({
        url: url,
        data: data,
        dataType: "html",
        type: "post",
        cache: false,
        async: asynchro,
        beforeSend: function () {
            $("body").append(d);
            $("#thickBox-iframe").dialog({
                resizable: resizable,
                height: height,
                width: width,
                modal: true,
                position: {
                    my: "top",
                    at: "top",
                    of: window
                },
                show: {
                    effect: 'drop',
                    direction: "up"
                },
                close: callback
            });
        },
        success: function (back) {
            $(d).html(back);
        }
    });
}

var thickBox = function (title, text, width, height, callback, type, optionesc) {
    $("#dialog-confirm").remove();
    var d = document.createElement("div");
    $(d).attr("id", "dialog-confirm");
    $(d).css("thickBox");
    $(d).attr("title", title);
    $(d).html(text);
    $("body").append(d);
    var buttonConf = "";
    var callbackClose = "";

    if (optionesc != undefined && optionesc != "") {
        optionesc = false;
    } else {
        optionesc = true;
    }

    if (typeof (callback) !== 'function') {
        callback = function () {
        };
    }

    if (type == "confirm") {
        buttonConf = {
            "Sim": function () {
                $(this).dialog("close");
                if (typeof (callback) == 'function')
                    callback(true);
            },
            "Não": function () {
                $(this).dialog("close");
                if (typeof (callback) == 'function')
                    callback(false);
            }
        };
        callbackClose = function () {
        };
    } else {
        callbackClose = callback;
    }


    $("#dialog-confirm").dialog({
        resizable: true,
        height: height,
        width: width,
        modal: true,
        show: {
            effect: 'drop',
            direction: "up"
        },
        buttons: buttonConf,
        close: callbackClose(),
        closeOnEscape: optionesc
    });
}

var thickBoxClose = function (obj) {
    if (typeof (obj) != "undefined") {
        $(obj).dialog('close');
    } else {
        //$(".ui-dialog").dialog('close');
        $('.ui-dialog-content').dialog('close');
    }
}
//var thickBoxClose = function() {
//    $(".thickBox").dialog('close');
//}

var showLoading = function (obj, root) {
    var d = document.createElement("img");
    $(d).attr("id", "showLoading");
    $(d).attr("src", "http://f71lagos.com/intranet/imagens/loading.gif");
    $(d).css("margin", "0 10px");
    obj.after(d);
}

var removeLoading = function () {
    $("#showLoading").remove();
}

var tableToExcel = (function () {
    var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="Content-Tipe" content="application/excel; charset=UTF-8"></head><body><table>{table}</table></body></html>'
            , base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }
    , format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) {
            return c[p];
        })
    }
    , removeImage = function (val) {
        var t = val.replace(/<img([^>]+)>/g, "");
        var n = t.replace(/<a([^>]+)>/g, "");
        return n
    }
    return function (table, name, img) {
        if (!table.nodeType)
            table = document.getElementById(table)
        //var copia = table.clone();
        //copia.remove(".hidden");
        //table.find("tr.hidden").remove();
        //console.log(table);
        
        var ctx = {
            worksheet: name || 'Worksheet',
            table: (img == true) ? table.innerHTML : removeImage(table.innerHTML)
        }
        window.location.href = uri + base64(format(template, ctx))
    }
})();

Number.prototype.formatMoney = function (c, d, t) {
    var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

//FUNÇÃO PARA EFEITO ZEBRA
var gridZebra = function (table) {
    $(table + ' tbody tr:odd').addClass('odd');
    $(table + ' tbody tr:even').addClass('even');
};

//FUNÇÃO PARA VALIDAR CNPJ
function validarCNPJ(cnpj) {

    cnpj = cnpj.replace(/[^\d]+/g, '');

    if (cnpj == '')
        return false;

    if (cnpj.length != 14)
        return false;

    // Elimina CNPJs invalidos conhecidos
    if (cnpj == "00000000000000" ||
            cnpj == "11111111111111" ||
            cnpj == "22222222222222" ||
            cnpj == "33333333333333" ||
            cnpj == "44444444444444" ||
            cnpj == "55555555555555" ||
            cnpj == "66666666666666" ||
            cnpj == "77777777777777" ||
            cnpj == "88888888888888" ||
            cnpj == "99999999999999")
        return false;

    // Valida DVs
    tamanho = cnpj.length - 2
    numeros = cnpj.substring(0, tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
        return false;

    return true;

}

//FUNÇÃO PARA VALIDAR CPF
function validaCpf(str) {
    str = str.replace('.', '');
    str = str.replace('.', '');
    str = str.replace('-', '');

    cpf = str;
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11)
        return false;
    for (i = 0; i < cpf.length - 1; i++)
        if (cpf.charAt(i) != cpf.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
        }
    if (!digitos_iguais) {
        numeros = cpf.substring(0, 9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--)
            soma += numeros.charAt(10 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;
        numeros = cpf.substring(0, 10);
        soma = 0;
        for (i = 11; i > 1; i--)
            soma += numeros.charAt(11 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
            return false;
        return true;
    }
    else
        return false;
}

//FUNÇÃO EXTESÃO JQUERY PARA SELECT BOX DINAMICOS, AINDA DA PRA MELHORAR
(function ($) {
    $.fn.ajaxGetJson = function (url, data, callback, id_destination) {
        var callbackGlobal = function (data, id_destination) {
            //console.log(data, 'data');
            //console.log(id_destination, 'destina');
            removeLoading();
            $("#" + id_destination).html(data);
            var selected = $("input[name=hide_" + id_destination + "]").val();
            if (selected !== undefined && selected !== "") {
                $("#" + id_destination).val(selected);
            }
        }

        if (typeof (callback) !== 'function') {
            callback = function (data) {
                callbackGlobal(data, id_destination);
            };
        }

        $(this).bind("change", function () {
            var $this = $(this);
            var name = $(this).attr('name');

            data[name] = $this.val();
            if ($this.val() !== "-1" && $this.val() !== " ") {
                showLoading($this);

                $.ajax({
                    url: url,
                    data: data,
                    dataType: "html",
                    type: "post",
                    cache: false,
                    async: true,
                    success: function (back) {
                        callbackGlobal(back, id_destination);
                        callback(back);
                    }
                });

                //$.post(url, data, callback , "html"); //NÃO QUERIA FAZER ISSO :(, MAS COMO TUDO EH PRA ONTEM!
            }
        }).trigger("change");

        return true;
    };
})(jQuery);

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k).toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/*
 * Função com mascara para telefone
 * Autor: Leonardo
 * data: 06/05/2015
 * @returns {undefined}
 */
jQuery.fn.brTelMask = function () {

    return this.each(function () {
        var el = this;
        $(el).focus(function () {
            $(el).mask("(99) 9999-9999?9", {placeholder: " "});
        });

        $(el).focusout(function () {
            var phone, element;
            element = $(el);
            element.unmask();
            phone = element.val().replace(/\D/g, '');
            if (phone.length > 10) {
                element.mask("(99) 99999-999?9");
            } else {
                element.mask("(99) 9999-9999?9");
            }
        });
    });
};



// funcao XOR
function XOR(a, b) {
    return (a || b) && !(a && b);
}

// ao digitar, permite so numero
function SomenteNumero(e){
    var tecla=(window.event)?event.keyCode:e.which;   
    if((tecla>47 && tecla<58)) return true;
    else{
        if (tecla==8 || tecla==0) return true;
        else  return false;
    }
}

function str_pad(input, pad_length, pad_string, pad_type) {
    //  discuss at: http://phpjs.org/functions/str_pad/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Michael White (http://getsprink.com)
    //    input by: Marco van Oort
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //   example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
    //   returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
    //   example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
    //   returns 2: '------Kevin van Zonneveld-----'

    var half = '',
      pad_to_go;

    var str_pad_repeater = function(s, len) {
      var collect = '',
        i;

      while (collect.length < len) {
        collect += s;
      }
      collect = collect.substr(0, len);

      return collect;
    };

    input += '';
    pad_string = pad_string !== undefined ? pad_string : ' ';

    if (pad_type !== 'STR_PAD_LEFT' && pad_type !== 'STR_PAD_RIGHT' && pad_type !== 'STR_PAD_BOTH') {
      pad_type = 'STR_PAD_RIGHT';
    }
    if ((pad_to_go = pad_length - input.length) > 0) {
      if (pad_type === 'STR_PAD_LEFT') {
        input = str_pad_repeater(pad_string, pad_to_go) + input;
      } else if (pad_type === 'STR_PAD_RIGHT') {
        input = input + str_pad_repeater(pad_string, pad_to_go);
      } else if (pad_type === 'STR_PAD_BOTH') {
        half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
        input = half + input + half;
        input = input.substr(0, pad_length);
      }
    }

    return input;
}