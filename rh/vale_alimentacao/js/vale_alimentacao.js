$(function () {
    $('.request_data').change(function () {
        ano = $('#ano_1').val();
        mes = $('#mes_1').val();

        $('#dataini_1').attr('disabled', 'disabled');
        $('#datafim_1').attr('disabled', 'disabled');
        $('#dias_uteis_1').attr('disabled', 'disabled');


        $.post(window.location, {acao: 'calcula_datas', ano: ano, mes: mes}, function (data) {
            $('#dataini_1').val(data.data_inicial);
            $('#datafim_1').val(data.data_final);
            $('#dias_uteis_1').val(data.dias_uteis);
            $('#dataini_1').removeAttr('disabled', 'disabled');
            $('#datafim_1').removeAttr('disabled', 'disabled');
            $('#dias_uteis_1').removeAttr('disabled', 'disabled');
        }, 'json');
    });
    $('input.money').priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
});

src_load = '../../imagens/carregando/ARROWS.gif';
src_load_h = '../../imagens/carregando/loading.gif';

function form1() {
    projeto = $('#projeto_1').val();
    ano = $('#ano_1').val();
    mes = $('#mes_1').val();
    data_inicial = $('#dataini_1').val();
    data_final = $('#datafim_1').val();
    dias_uteis = $('#dias_uteis_1').val();
    $('#din_1').carregando({text: 'carregando...<br>'});
    $.post(window.location, {acao: 'form1', projeto: projeto, ano: ano, mes: mes, data_inicial: data_inicial, data_final: data_final, dias_uteis: dias_uteis}, function (data) {
        $('#din_1').html(data);
    });
}

function form2() {
    $('#din_2').carregando({text: 'indo...<br>'});
    projeto = $('#projeto_2').val();
    cpf = $('#cpf_2').val();
    nome = $('#nome_2').val();
    alimentacao = $('#alimentacao_2').is(':checked');
    if ($('#box_data_entrada_2').css('display') == 'block') {
        mes = $('#mes_2').val();
        ano = $('#ano_2').val();
    } else {
        mes = '';
        ano = '';
    }
    $.post(window.location, {acao: 'form2', projeto: projeto, cpf: cpf, nome: nome, alimentacao: alimentacao, mes: mes, ano: ano}, function (data) {
        $('#din_2').html(data);
    });
}

function editar_2(id) {
    obj_alimentacao = {1: 'SIM', 0: 'NÃO'};
    $parent = $('#tr_2_' + id);
    console.log('#tr_2_' + id);
    $parent.children().eq(5).editaCampo({type: 'select', values: obj_alimentacao, controller: '#bts_controller_2'});
    $parent.children().eq(6).editaCampo({type: 'select', values: obj_relacao_tarifas, controller: '#bts_controller_2'});
//    $parent.children().eq(6).editaCampo({controller: '#bts_controller_2',id:'input_6_'+id});
    $('#input_2_' + id).priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
}
function salva_2() {
    var data_post = new Object();
    $("#table_2").find('tr.invalid').each(function (index) {
        $this = $(this);
        var obj = new Object();
        obj.id = $this.children().eq(0).children('input[type=checkbox]').attr('data-value');
        obj.solicitou_vale = $this.children().eq(5).children('select').val();
        obj.id_valor = $this.children().eq(6).children('select').val();
        data_post[index] = obj;
    });
    $('#din_2').carregando({text: 'carregando...<br>'});
//    console.log(data_post);

//    regiao = $('#regiao_3').val();
    $.post(window.location, {acao: 'salva_2', dados: data_post}, function (data) {
//        console.log(data);
        $('#din_2').html(data);
//        $('#din_2').find('.message-box').fadeOut();
    });

}

function form3() {
    $('#din_3').carregando({text: 'carregando...<br>'});
    regiao = $('#regiao_3').val();
    valor = $('#valor_3').val();
    $.post(window.location, {acao: 'form3', regiao: regiao, valor: valor}, function (data) {
        $('#din_3').html(data);
        $('#valor_3').val('');
    });
}

function deletar_3(id) {
    $('#linkdel_3_' + id).carregando({'src': src_load});

    $.post(window.location, {acao: 'del_3', id: id}, function (data) {
        console.log(data);
        if (data.status) {
            $('#tr_3_' + id).fadeOut();
        } else {

        }
    }, 'json');
}
function get_table_3() {
    regiao = $('#regiao_3').val();
    $('#din_3').carregando({text: 'carregando...<br>'});
    $.post(window.location, {acao: 'get_table_3', regiao: regiao}, function (data) {
        $('#din_3').html(data);
    });
}
function editar_3(id) {
    $parent = $('#tr_3_' + id);
    $parent.children().eq(3).editaCampo({controller: '#bts_controller_3', id: 'input_3_' + id});
    $('#input_3_' + id).priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
}
function salva_3() {
    var data_post = new Object();
    $("#table_3").find('tr.invalid').each(function (index) {
        $this = $(this);
        var obj = new Object();
        obj.id = $this.children().eq(0).children('input[type=checkbox]').attr('data-value');
        obj.valor = $this.children().eq(3).children('input[type=text]').val();
        data_post[index] = obj;
    });
    $('#din_3').carregando({text: 'carregando...<br>'});
    console.log(data_post);

    regiao = $('#regiao_3').val();
    $.post(window.location, {acao: 'salva_3', dados: data_post, regiao: regiao}, function (data) {
        $('#din_3').html(data);
        $('#din_3').find('.message-box').fadeOut();
    });

}
function limpa_3() {

}

function fechar_pedido() {
    dataPost = new Object();
    dataPost.acao = 'fechar_pedido';
    dataPost.id_projeto = $('#projeto_pedido').val();
    dataPost.ano_pedido = $('#ano_pedido').val();
    dataPost.mes_pedido = $('#mes_pedido').val();
    dataPost.data_inicial_pedido = $('#data_inicial_pedido').val();
    dataPost.data_final_pedido = $('#data_final_pedido').val();
    dataPost.dias_uteis_pedido = $('#dias_uteis_pedido').val();
//    console.log(dataPost);
    $('#din_1').carregando({text: 'gravando...<br>'});
    $.post(window.location, dataPost, function (data) {
        $('#din_1').html('');
        $('#item0').html(data + '<div id="din_0"></div>');
        $('#nav').children().eq(0).children('a').click();
    });
}
function deletar_pedido(id) {
    $('#link_del_pedido_' + id).carregando({'src': src_load});
    $.post(window.location, {acao: 'deletar_pedido', id_pedido: id}, function (data) {
        if (data.status) {
            $('#tr_pedido_' + id).fadeOut();
        }
    }, 'json');
}
function visualizar_pedido(id) {
    $('#link_mov_pedido_' + id).carregando({'src': src_load});
    $.post(window.location, {acao: 'visualizar_pedido', id_pedido: id}, function (data) {
        $('#link_mov_pedido_' + id).carregando({'src': '../../imagens/file.gif'});
        $('#tab0').hide();
        $('#din_0').html(data);
    });
}
function arquivo_aelo(id) {
    $('#bt_arquivo_aelo').val('Gerando...');
    $('#bt_arquivo_aelo').attr('disabled', 'disabled');
    $('#table_fake').html('');
    $.post(window.location, {acao: 'arquivo_aelo', id_pedido: id}, function (data) {
//        $('#table_fake').html(data.dados);
        console.log(data.dados);
        $('#bt_arquivo_aelo').val('Criar Arquivo Exportação AELO');
        $('#bt_arquivo_aelo').removeAttr('disabled');
        window.location = '?download=' + data.name_file;
    }, 'json');
}
function voltar_lista_pedido() {
    $('#tab0').show();
    $('#din_0').html('');
}



//$('#del_dias_uteis_' + id).carregando({'src': src_load});
$.fn.carregando = function (options) {
    defaults = {src: '../../imagens/carregando/loading.gif', text: ''}
    var opts = $.extend(defaults, options);
    return $(this).html(opts.text + '<img src="' + opts.src + '" />');
}
$.fn.editaCampo = function (options) {
    $this = $(this);
    defaults = {type: 'input', value: $this.attr('data-value'), check: null, name: '', class: 'invalid', controller: false};
    var opts = $.extend(defaults, options);
    var resp = '';
    var flag = $(opts.controller).attr('data-flag');
    if ($this.children().attr('data-edit') == 1) {
        resp = opts.value;
        $this.parent().removeClass(opts.class);
        flag--;
    } else {
        flag++;
        if (opts.type === 'select') {
            var options = '';
            var i;
            for (i in opts.values) {
                ck = '';
                if (opts.value === opts.values[i]) {
                    ck = ' selected="selected" ';
                }
                options += '<option value="' + i + '" ' + ck + '>' + opts.values[i] + '</option>';
            }
            resp = '<select data-edit="1" id="' + opts.id + '" >' + options + '</select>';
        } else {
            resp = '<input data-edit="1" type="text" value="' + opts.value + '"  id="' + opts.id + '" >';
        }
        $this.parent().addClass(opts.class);
    }
    $(opts.controller).attr('data-flag', flag);
    if (flag <= 0) {
        $(opts.controller).hide();
    } else {
        $(opts.controller).show();
    }
    return $this.html(resp);
};



