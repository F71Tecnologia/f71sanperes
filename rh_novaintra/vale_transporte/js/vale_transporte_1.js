$(function() {
    
    $('.colDir').on('change','#regiao',function(){
        var $this = $(this);
        var id_regiao = $this.val();
        $('.projeto').attr('disabled','disabled');
        $('.projeto').html('<option>carregando...</option>');
        $('div[id^=din_]').html('');
        $.post(window.location, {acao: 'get_projetos', id_regiao: id_regiao}, function(data) {
            var html = '<option value="-1" > Selecione </option>';
            for(i in data){
               html += '<option value="'+i+'" >'+i+' - '+data[i]+'</option>'; 
            }
            $('.projeto').html(html);
            $('.projeto').removeAttr('disabled');
        },'json');
        
        $('#curso_6').attr('disabled','disabled');    
        $('#curso_6').html('<option>carregando...</option>');        
        $.post(window.location, {acao: 'get_cursos', id_regiao: id_regiao}, function(data) {
            option = '<option value="todos">Todos os Cursos</option>';
            
            
            
            for (var i in data.cursos) {
                option += '<option value="' + data.cursos[i].chave + '">' + data.cursos[i].valor + '</option>';
            }
            $('#curso_6').removeAttr('disabled');
            $('#curso_6').html(option);
            $('#horario_6').html('<option value="todos">Todos os Horários</option>');
        }, 'json');       
        
        
    });
    
    $('.colDir').on('change','select[id^=regiao_]',function(){
        var $this = $(this);
        var arr = $this.attr('id').split('_');
        $('#projeto_'+arr[1]).html('<option>carregando...</option>')
        $.post(window.location, {acao: 'get_projetos', id_regiao: $this.val()}, function(data) {
            var html = '<option value="-1" > Selecione </option>';
            for(i in data){
               html += '<option value="'+i+'" >'+i+' - '+data[i]+'</option>'; 
            }
            $('#projeto_'+arr[1]).html(html);
        },'json');
    });
    
    
    
    $('.request_data').change(function() {
        $('.cp_data').attr('disabled', 'disabled')
        ano_base = $('#ano_1').val();
        mes_base = $('#mes_1').val();
        calcular_datas(mes_base, ano_base);
    });
    
    $('input.money').priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});

    $('input[id^=somente_novos]').click(function() {
        var $this = $(this);
        var arr = $this.attr('id').split('_');
        
        if ($this.is(':checked')) {
            $('#box_data_entrada_'+arr[2]).show();
            $('#meses_'+arr[2]).removeAttr('disabled');
            $('#anos_'+arr[2]).removeAttr('disabled');
        } else {
            $('#box_data_entrada_'+arr[2]).hide();
            $('#meses_'+arr[2]).attr('disabled', 'disabled');
            $('#anos_'+arr[2]).attr('disabled', 'disabled');
        }
    });
    

    /*******************************AÇÃO DE EXPORTAR ****************************/
    $(".exportar").click(function() {
        var id_vt = $(this).attr("data-key");
        $("#id_pedido").val(id_vt);
        $("#page_controller").attr({action: "includes/controlador.php"}).submit();
    });

    $('#curso_6').change(function() {
        id_curso = $('#curso_6').val();
        carregaHorariosByCurso(id_curso);
    });
    $('input[name=referencia]').click(function(){
        $this = $(this);
        key = $(this).attr('data-key');
        if($this.val()==1){
            $('#referencia_'+key).val('1');
            $('#mes_'+key).attr('disabled','disabled');
            $('#ano_'+key).attr('disabled','disabled');
            $('#mes_'+key).hide();
            $('#ano_'+key).hide();
        }else if($this.val()==2){
            $('#referencia_'+key).val('0');
            $('#mes_'+key).removeAttr('disabled');
            $('#ano_'+key).removeAttr('disabled');
            $('#mes_'+key).show();
            $('#ano_'+key).show();
        } 
    });
    
    $('input[id^=show_form_cad_]').click(function(){
       var $this = $(this);
       var arr = $this.attr('id').split('_');       
       
       $('#form_cad_'+arr[3]).toggle();
       if($('#form_cad_'+arr[3]).css('display')=='none'){
           $this.val('Novo Cadastro');
       }else{
           $this.val('Cancelar Novo Cadastro');
       }
    });
    
    $('fieldset[id^=form_cad_]').hide();
    
    get_table_0(); // carrega os pedidos via ajax;
    
    $('#cbo_6').change(function(){
        $this = $(this);
        $('#new_cursos').html('');
        carregaCursosByCBO($this.val());
    });
});

var obj_linhas = new Object();
obj_linhas = {1:'Municipal',2:'Intermunicipal'};
//
//ITEM 0
//
src_load = '../../imagens/carregando/ARROWS.gif';
src_load_h = '../../imagens/carregando/loading.gif';

function get_table_0() {
    $('#din_0').html('<p>carregando dados...</p><img src="' + src_load_h + '" />');
    id_projeto = $('#projeto_0').val();
    $.post(window.location, {acao: 'table_0', id_projeto: id_projeto}, function(data) {
        $('#din_0').html(data);
    });
}



function visualizar_pedido(id) {
    $('#img_pedido_' + id).attr('src', src_load);
    $.post(window.location, {acao: 'relacao_pedido', id_vt_pedido: id}, function(data) {
        $('#tab0').hide();
        $('#din_0').html(data);
        $('#din_0').show();
        $('#img_pedido_' + id).attr('src', '../../imagens/file.gif');
    });
}
function desconto_folha(id) {
    $('#img_folha_' + id).attr('src', src_load);
    $.post(window.location, {acao: 'relacao_folha', id_vt_pedido: id}, function(data) {
        $('#tab0').hide();
        $('#din_0').html(data);
        $('#din_0').show();
        $('#img_folha_' + id).attr('src', '../../imagens/icon-filego.gif');
    });
}
function lancar_movimentos(id) {
    $('#bt_lancar_movimentos').val('processando...');
    $('#bt_lancar_movimentos').attr('disabled','disabled');
//    $('#img_folha_' + id).attr('src', src_load);
    $.post(window.location, {acao: 'lancar_movimentos', id_vt_pedido: id}, function(data) {
        $('#bts_controller_03').hide();
        $('#bts_controller_03').after(data);
        $('#bt_lancar_movimentos').val('Salvar');
        $('#bt_lancar_movimentos').removeAttr('disabled');
    });
}
function sobreescrever_movimentos(id) {
//    $('#img_folha_' + id).attr('src', src_load);
    $('#bt_sobreescrever_movimentos').val('processando...');
    $('#bt_sobreescrever_movimentos').attr('disabled','disabled');
    $('#bt_cancelar_sobreescrever_movimentos').attr('disabled','disabled');
    $.post(window.location, {acao: 'lancar_movimentos', id_vt_pedido: id, sobreescrever: 1}, function(data) {
        $('#box_confirmar_sobreescrita').remove();
        $('#bts_controller_03').hide();
        $('#bts_controller_03').after(data);
        $('#bt_lancar_movimentos').val('Salvar');
        $('#bt_lancar_movimentos').removeAttr('disabled');
        $('#bt_cancelar_sobreescrever_movimentos').removeAttr('disabled');
    });
}

function gerar_arquivo(id) {
    $('#img_arquivo_' + id).attr('src', src_load);
    $.post(window.location, {acao: 'gerar_arquivo', id_vt_pedido: id}, function(data) {
        $('#tab0').hide();
        $('#din_0').html(data);
        $('#din_0').show();
        $('#img_pedido_' + id).attr('src', '../../imagens/icon-download.png');
    });
}
function voltar_lista_pedido() {
    $('#din_0').hide();
    $('#item0 > table').show();
}
function deletar_pedido(id) {
    $('#img_del_pedido_' + id).attr('src', src_load);
    $.post(window.location, {acao: 'deletar_pedido', id_vt_pedido: id}, function(data) {
        alert('Pedido deletado com sucesso!');
        $('#tr_vt_pedido_' + data.id).fadeOut();
    }, 'json');
}

//
//ITEM 1
//

function form1() {
    $('#din_1').carregando({text: 'indo...<br>'});

    regiao = $('#regiao').val();
    projeto = $('#projeto_1').val();
    sobrescreve_cnpj = $('#sobrescreve_cnpj_1').is(':checked');
    ano_base = $('#ano_1').val();
    mes_base = $('#mes_1').val();
    data_inicial = $('#dataini_1').val();
    data_final = $('#datafim_1').val();
    dias_uteis = $('#dias_uteis_1').val();
    $.post(window.location, {acao: 'form_1',regiao: regiao, projeto: projeto, sobrescreve_cnpj: sobrescreve_cnpj, ano_base: ano_base, mes_base: mes_base, data_inicial: data_inicial, data_final: data_final, dias_uteis: dias_uteis}, function(data) {
        $('#din_1').html(data);
    });
}
function get_datas() {
    $('#content_feriados').hide();
    $.post(window.location.pathname, {
        mes_referencia: $('#mes_referencia').val(),
        ano_referencia: $('#ano_referencia').val(),
        acao: 'get_dias_mes'
    }, function(data) {
        $('#data_inicial').val(data.data_inicial);
        $('#data_final').val(data.data_final);
        $('#dias_uteis').val(data.dias_uteis);

        if (data.feriados.length > 0) {
            $('#content_feriados').show();
            $('#numero_feriados').html(data.feriados.length);
            feriados = '';
            for (var i in data.feriados) {
                feriados += data.feriados[i]['dia'] + '/' + data.feriados[i]['mes'] + '/' + data.feriados[i]['ano'] + ' - ' + data.feriados[i]['nome'] + ' <br>';
            }
            $('#dias_feriados').html(feriados);
            console.log(data);
        }
    }, 'json');
}
function reprocessar_pedido(id_pedido){
    
    console.log(id_pedido);
    
    $.post(window.location, {acao: 'reprocessar_pedido', id_pedido: id_pedido }, function(data){
        console.log(data);
    });
}
function finalizar_pedido() {
    $('#bt_finalizar_pedido').attr('disabled', 'disabled');
    $('#bt_finalizar_pedido').val('Finalizando...');
    regiao = $('#post_regiao_1').val();
    projeto = $('#post_projeto_1').val();
    cnpj = $('#post_cnpj_1').val();
    ano_base = $('#post_ano_base_1').val();
    mes_base = $('#post_mes_base_1').val();
    data_inicial = $('#post_data_inicial_1').val();
    data_final = $('#post_data_final').val();
    dias_uteis = $('#post_dias_uteis').val();
    

    $('#arquivo_txt').remove();
    $.post(window.location, {acao: 'fechar_pedido',regiao: regiao, projeto: projeto, cnpj: cnpj, ano_base: ano_base, mes_base: mes_base, data_inicial: data_inicial, data_final: data_final, dias_uteis: dias_uteis}, function(data) {

        alert('Pedio finalizado com sucesso!');
        $('#item0').html(data);
        $(".colDir > div").hide();
        $("#item0").show();
        $(".bt-menu").removeClass("aselected");
        $(".bt-menu[data-item=0]").addClass("aselected");
        $('#item0').html(data)
        $('#din_1').html('');
    }); //json
}


//
//ITEM 2
//

function form2() {
    $('#din_2').carregando({text: 'indo...<br>'});
    projeto = $('#projeto_2').val();
    tipo_registro = $('#tipo_registro_2').val();
    sobrescreve_cnpj = $('#sobrescreve_cnpj_2').is(':checked');
    transporte = $('#transporte_2').is(':checked');
    matricula = $('#matricula_2').val();
    cpf = $('#cpf_2').val();
    nome = $('#nome_2').val();
    if ($('#somente_novos_2').is(':checked')) {
        entrada = $('#mese_2').val() + '_' + $('#ano_2').val();
    } else {
        entrada = '';
    }
    $.post(window.location, {acao: 'form_2', projeto: projeto, tipo_registro: tipo_registro, sobrescreve_cnpj: sobrescreve_cnpj, transporte: transporte, matricula: matricula, cpf: cpf, nome: nome, data_entrada: entrada}, function(data) {
        $('#din_2').html(data);
    });
}
function exportar_clts() {

    projeto = $('#post_projeto_2').val();
    cnpj = $('#post_cnpj_2').val();
    transporte = $('#post_transporte_2').val();
    matricula = $('#post_matricula_2').val();
    cpf = $('#post_cpf_2').val();
    nome = $('#post_nome_2').val();
    tipo_registro = $('#post_tipo_registro_2').val();
    if ($('#somente_novos_2').is(':checked')) {
        entrada = $('#mese_2').val() + '_' + $('#ano_2').val();
    } else {
        entrada = '';
    }
    $('#baixar_txt_2').html('');
    $('#arquivo_txt').remove();

    $.post(window.location, {acao: 'exportar_clts', projeto: projeto, cnpj: cnpj, transporte: transporte, matricula: matricula, cpf: cpf, nome: nome, tipo_registro: tipo_registro, data_entrada: entrada}, function(data) {
        $('#baixar_txt_2').html(data);
//        $('#din_2').append(data.textarea); 
    });
}



//
//ITEM 3
//

function form3() {
    $('#din_3').carregando({text: 'indo...<br>'});
    projeto = $('#projeto_3').val();
    
    console.log(projeto);
    
    sobrescreve_cnpj = $('#sobrescreve_cnpj_3').is(':checked');
    transporte = $('#transporte_3').is(':checked');
    matricula = $('#matricula_3').val();
    somente_novos = $('#somente_novos_3').is(':checked');
    cpf = $('#cpf_3').val();
    nome = $('#nome_3').val();
     if ($('#somente_novos_3').is(':checked')) {
        entrada = $('#mese_3').val() + '_' + $('#ano_3').val();
    } else {
        entrada = '';
    }
    $.post(window.location, {acao: 'form_3', projeto: projeto, sobrescreve_cnpj: sobrescreve_cnpj, transporte: transporte, matricula: matricula, cpf: cpf, nome: nome, data_entrada: entrada}, function(data) {
        $('#din_3').html(data);
    });
}


// edição clts

obj_transporte = {1: 'SIM', 0: 'NÃO'};
obj_tipo_cartao = {'02': 'Rio Card do Comprador (Cartão Laranja)', '05': 'Bilhete Único Inter. do Usuário', '06': 'Bilhete Único Carioca do Usuário'};

function limpar_edicao_clts() {
    console.log('foi...');
    $("#table_3").find('tr.invalid').each(function(index) {
        $this = $(this);
        $this.children().eq(0).children().click();
    });
}


function salvar_edicao_clts() {
    $('#bt_limpar_edicao_clts').hide();
    $('#bt_salvar_edicao_clt').attr('disabled', 'disabled');
    $('#bt_salvar_edicao_clt').val('Processando...');
    var dataPost = new Object();

    $("#table_3").find('tr.invalid').each(function(index) {
        $this = $(this);
        var obj = new Object();
        obj.id_clt = $this.children().eq(0).children('input[type=checkbox]').attr('data-value');
        obj.matricula = $this.children().eq(2).children('input[type=text]').val();
        obj.transporte = $this.children().eq(6).children('select').val();
        obj.tipocartao = $this.children().eq(7).children('select').val();
        obj.cartao1 = $this.children().eq(8).children('input[type=text]').val();
        dataPost[index] = obj;
    });
    $.post(window.location, {acao: 'atualizar_clts', data: dataPost}, function(data) {
        $('#bt_limpar_edicao_clts').show();
        if (data.status) {
            fechar_edicao_clts_salvo();
            alert('Operação efetuada com sucesso!');
        } else {
            alert('Erro em atualizar os dados!');
            limpar_edicao_clts();
        }
        $('#bt_salvar_edicao_clt').val('Salvar');
        $('#bt_salvar_edicao_clt').removeAttr('disabled');
    }, 'json');
}
function fechar_edicao_clts_salvo() {
    $("#table_3").find('tr.invalid').each(function(index) {
        $this = $(this);

        $this.removeClass('invalid');
        $this.children().eq(0).children().removeAttr('checked');

        $this.children().eq(2).attr('data-value', $this.children().eq(2).children().val());
        $this.children().eq(2).html($this.children().eq(2).children().val());
        $this.children().eq(2).children().remove();

        $this.children().eq(6).attr('data-value', obj_transporte[$this.children().eq(6).children().val()]);
        $this.children().eq(6).html(obj_transporte[$this.children().eq(6).children().val()]);
        $this.children().eq(6).children().remove();

        $this.children().eq(7).attr('data-value', obj_tipo_cartao[$this.children().eq(7).children().val()]);
        $this.children().eq(7).html(obj_tipo_cartao[$this.children().eq(7).children().val()]);
        $this.children().eq(7).children().remove();

console.log($this.children().eq(8).children().val());
        $this.children().eq(8).attr('data-value', $this.children().eq(8).children().val());
        $this.children().eq(8).html($this.children().eq(8).children().val());
        $this.children().eq(8).children().remove();
    });
    $('#bts_controller_3').attr('data-flag', '0');
    $('#bts_controller_3').hide();
}

function editao_vt_clt(id) {
    $parent = $('#tr_' + id);
    $parent.children().eq(2).editaCampo({controller: '#bts_controller_3'});
    $parent.children().eq(6).editaCampo({type: 'select', values: obj_transporte, controller: '#bts_controller_3'});
    $parent.children().eq(7).editaCampo({type: 'select', values: obj_tipo_cartao, controller: '#bts_controller_3'});
    $parent.children().eq(8).editaCampo({controller: '#bts_controller_3'});
    console.log('novo');
}

//
//ITEM 4
//

function form4() {

    $('#din_4').carregando({text: 'indo...<br>'});

    id_regiao = $('#regiao').val();
    itinerario = $('#tipo_4').val();
    descricao = $('#descricao_4').val();
    concessionaria = $('#concessionaria_4').val();
    linha = $('#linha_4').val();
    valor = $('#valor_4').val();
    $.post(window.location, {acao: 'form_4', id_regiao: id_regiao, itinerario: itinerario, descricao: descricao, concessionaria: concessionaria,linha: linha, valor: valor}, function(data) {
        $('#din_4').html(data);
        $("#din_4 > .message-box").fadeOut(3000);
    });
}
function get_table_4() {
    $('#din_4').html('<p>carregando dados...</p><img src="' + src_load_h + '" />');
    $('#concessionaria_4').html('<option>carregando...<option>');
    $('#concessionaria_4').attr('disabled', 'disabled');
    id_regiao = $('#regiao').val();
    $.post(window.location, {acao: 'table_4', id_regiao: id_regiao}, function(data) {
        $('#din_4').html(data);
    });
    $.post(window.location, {acao: 'get_concessionarias', id_regiao: id_regiao}, function(data) {
        var option = '<option value="0" >NÃO ESPECIFICADO</option>';
        for (i in data.concessionarias) {
            option += '<option value="' + i + '" >' + data.concessionarias[i] + '</option>';
        }
        $('#concessionaria_4').html(option);
        $('#concessionaria_4').removeAttr('disabled');
    }, 'json');
}

obj_tipos_tarifas = {'IDA': 'IDA', 'VOLTA': 'VOLTA'};


function salvar_edicao_tarifas() {



    id_regiao = $('#regiao').val();

    var dataPost = new Object();

    $("#table_4").find('tr.invalid').each(function(index) {
        $this = $(this);
        var obj = new Object();
        obj.id_tarifa = $this.children().eq(0).children('input[type=checkbox]').attr('data-value');
        obj.id_regiao = id_regiao;
        obj.itinerario = $this.children().eq(2).children('select').val();
        obj.descricao = $this.children().eq(3).children('input[type=text]').val();
        obj.id_concessionaria = $this.children().eq(4).children('select').val();
        obj.valor = $this.children().eq(5).children('input[type=text]').val();
        obj.linha = $this.children().eq(6).children('select').val();
        dataPost[index] = obj;
    });


    $.post(window.location, {acao: 'atualizar_tarifas', data: dataPost, id_regiao: id_regiao}, function(data) {
        if (data.status) {
            obj_concenssionarias = data.concessionarias;

            $("#table_4").find('tr.invalid').each(function(index) {
                $this = $(this);
                console.log(index);

                $this.removeClass('invalid');
                $this.children().eq(0).children().removeAttr('checked');

                $this.children().eq(2).attr('data-value', $this.children().eq(2).children().val());
                $this.children().eq(2).html($this.children().eq(2).children().val());
                $this.children().eq(2).children().remove();

                $this.children().eq(3).attr('data-value', $this.children().eq(3).children().val());
                $this.children().eq(3).html($this.children().eq(3).children().val());
                $this.children().eq(3).children().remove();

                $this.children().eq(4).attr('data-value', obj_concenssionarias[$this.children().eq(4).children().val()]);
                $this.children().eq(4).html(obj_concenssionarias[$this.children().eq(4).children().val()]);
                $this.children().eq(4).children().remove();

                $this.children().eq(5).attr('data-value', $this.children().eq(5).children().val());
                $this.children().eq(5).html($this.children().eq(5).children().val());
                $this.children().eq(5).children().remove();
                
                $this.children().eq(6).attr('data-value', obj_linhas[$this.children().eq(6).children().val()]);
                $this.children().eq(6).html(obj_linhas[$this.children().eq(6).children().val()]);
                $this.children().eq(6).children().remove();
                console.log('ok 2...');
            });

            alert('Operação efetuada com sucesso!');
        } else {
            alert('Erro em atualizar os dados!');
//            limpar_edicao_tarifas();
        }
    }, 'json');


}
function limpar_edicao_tarifas() {
    $("#table_4").find('tr.invalid').each(function(index) {
        $this = $(this);
        console.log(index);

        $this.removeClass('invalid');
        $this.children().eq(0).children().removeAttr('checked');

        $this.children().eq(2).attr('data-value', $this.children().eq(2).children().val());
        $this.children().eq(2).html($this.children().eq(2).children().val());
        $this.children().eq(2).children().remove();

        $this.children().eq(3).attr('data-value', $this.children().eq(3).children().val());
        $this.children().eq(3).html($this.children().eq(3).children().val());
        $this.children().eq(3).children().remove();

        $this.children().eq(4).attr('data-value', obj_concenssionarias[$this.children().eq(4).children().val()]);
        $this.children().eq(4).html(obj_concenssionarias[$this.children().eq(4).children().val()]);
        $this.children().eq(4).children().remove();

        $this.children().eq(5).attr('data-value', $this.children().eq(5).children().val());
        $this.children().eq(5).html($this.children().eq(5).children().val());
        $this.children().eq(5).children().remove();
        
//        $this.children().eq(6).attr('data-value', $this.children().eq(6).children().val());
//        $this.children().eq(6).html($this.children().eq(6).children().val());
//        $this.children().eq(6).children().remove();
        console.log('ok!! ...');
    });
}
function editar_tarifa(id) {
    $('#table_4').find('input[type=checkbox]').attr('disabled', 'disabled');

    var obj_concenssionarias = new Object();
    var flag_edita_tarifa = '0';

    if ($('#tr_vt_tarifa_' + id).children().eq(0).children('input').is(':checked')) {
        console.log('indo post...')
        id_regiao = $('#regiao').val();
        $.post(window.location, {acao: 'get_concessionarias', id_regiao: id_regiao}, function(data) {
            obj_concenssionarias = data.concessionarias;
            flag_edita_tarifa = '1';
        }, 'json');
    } else {
        console.log('sem post!!!')
        flag_edita_tarifa = 1;
    }

    var_edicao_tarifa = setInterval(function() {
        if (flag_edita_tarifa == '1') {
            $parent = $('#tr_vt_tarifa_' + id);
            $parent.children().eq(2).editaCampo({type: 'select', values: obj_tipos_tarifas, id: 'tipo_tarifa_' + id, controller: '#bts_controller_4'});
            $parent.children().eq(3).editaCampo({id: 'nome_tarifa_' + id, controller: '#bts_controller_4'});
            $parent.children().eq(4).editaCampo({type: 'select', values: obj_concenssionarias, id: 'tipo_concenssionaria_' + id, controller: '#bts_controller_4'});
            $parent.children().eq(5).editaCampo({id: 'valor_tarifa_' + id, controller: '#bts_controller_4'});
            $parent.children().eq(6).editaCampo({type: 'select', values: obj_linhas, id: 'linha_' + id, controller: '#bts_controller_4'});            
            $('#valor_tarifa_' + id).priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
            $('#table_4').find('input[type=checkbox]').removeAttr('disabled');
            clearInterval(var_edicao_tarifa);
        }
    }, 99);
}

// fim da edição de tarifas


function deletar_tarifa(id) {
    $('#del_tarifa_' + id).carregando({'src': src_load});
//    $('#img_tarifa_' + id).attr('src', src_load);
    $.post(window.location, {acao: 'deletar_tarifa', id_tarifa: id}, function(data) {
        if (data.status) {
            $('#tr_vt_tarifa_' + id).fadeOut();
        } else {
            alert('Erro em deletar tarifa!');
        }
    }, 'json');
}



// ITEM 5

function form5() {

    $('#din_5').carregando({text: 'indo...<br>'});

    id_regiao = $('#regiao').val();
    tipo = $('#tipo_5').val();
    nome = $('#nome_5').val();

    $.post(window.location, {acao: 'form_5', id_regiao: id_regiao, tipo: tipo, nome: nome}, function(data) {
        $('#din_5').html(data.table_5);
        console.log(data.obj_concessionarias);
        $("#din_5 > .message-box").fadeOut(3000);
        
        $('#concessionaria_4').html('');
        
        for(i in data.obj_concessionarias){
            var id = data.obj_concessionarias[i].id_concessionaria;
            var nome = data.obj_concessionarias[i].nome;
            var options = '<option value="'+id+'" >'+nome+'</option>';
            $('#concessionaria_4').append(options);
        }
    },'json');
}
function get_table_5() {
    $('#din_5').html('<p>carregando dados...</p><img src="' + src_load_h + '" />');
    var id_regiao = $('#regiao').val();
    $.post(window.location, {acao: 'table_5', id_regiao: id_regiao}, function(data) {
        $('#din_5').html(data);
    });
}

function deletar_concessionaria(id) {
    $('#del_concessionaria_' + id).carregando({'src': src_load});
    $.post(window.location, {acao: 'deletar_concessionaria', id_concessionaria: id}, function(data) {
        if (data.status) {
            $('#tr_vt_concessionaria_' + id).fadeOut();
            $('#concessionaria_4').find('option[value='+id+']').remove()
        } else {
            alert('Erro em deletar tarifa!');
        }
    }, 'json');
}
function editar_concessionaria(id) {
    $parent = $('#table_4').find('#tr_' + id);
    $parent.children().eq(2).editaCampo({controller: '#bts_controller_5'});
    $parent.children().eq(3).editaCampo({type: 'select', values: obj_transporte, controller: '#bts_controller_5'});
}
function editar_concessionaria(id) {
    $('#table_5').find('input[type=checkbox]').attr('disabled', 'disabled');
    var obj_tipos_concessionarias = new Object();
    var flag_edita_concessionaria = '0';

    if ($('#tr_vt_concessionaria_' + id).children().eq(0).children('input').is(':checked')) {
        $.post(window.location, {acao: 'get_tipos_concessionarias'}, function(data) {
            console.log(data.tipos_concessionarias);
            obj_tipos_concessionarias = data.tipos_concessionarias;
            flag_edita_concessionaria = '1';
        }, 'json');
    } else {
        console.log('sem post!!!')
        flag_edita_concessionaria = 1;
    }

    teste_edicao_concessionaria = setInterval(function() {
        if (flag_edita_concessionaria == '1') {

            $parent = $('#tr_vt_concessionaria_' + id);

            $parent.children().eq(2).editaCampo({controller: '#bts_controller_5'});
            $parent.children().eq(3).editaCampo({type: 'select', values: obj_tipos_concessionarias, controller: '#bts_controller_5'});

            $('#table_5').find('input[type=checkbox]').removeAttr('disabled');
            clearInterval(teste_edicao_concessionaria);
        }
    }, 99);
}
function limpar_edicao_concessionaria() {
    $("#table_5").find('tr.invalid').each(function(index) {
        $this = $(this);
        $this.removeClass('invalid');
        $this.children().eq(0).children().removeAttr('checked');

        $this.children().eq(2).children().remove();
        $this.children().eq(2).html($this.children().eq(2).attr('data-value'));

        $this.children().eq(3).children().remove();
        $this.children().eq(3).html($this.children().eq(3).attr('data-value'));
    });
    $('#bts_controller_5').attr('data-flag', '0');
    $('#bts_controller_5').hide();
}
function salvar_edicao_concessionaria() {

    var dataPost = new Object();

    id_regiao = $('#regiao').val();

    $("#table_5").find('tr.invalid').each(function(index) {
        $this = $(this);
        var obj = new Object();
        obj.id_concessionaria = $this.children().eq(0).children('input[type=checkbox]').attr('data-value');
        obj.id_regiao = id_regiao;
        obj.nome = $this.children().eq(2).children('input[type=text]').val();
        obj.tipo = $this.children().eq(3).children('select').val();
        dataPost[index] = obj;
    });
    $.post(window.location, {acao: 'atualizar_concessionaria', data: dataPost}, function(data) {
        if (data.status) {
            $("#table_5").find('tr.invalid').each(function(index) {
                $this = $(this);

                $this.removeClass('invalid');
                $this.children().eq(0).children().removeAttr('checked');

                $this.children().eq(2).attr('data-value', $this.children().eq(2).children().val());
                $this.children().eq(2).html($this.children().eq(2).children().val());
                $this.children().eq(2).children().remove();

                $this.children().eq(3).attr('data-value', data.tipos_concessionarias[$this.children().eq(3).children().val()]);
                $this.children().eq(3).html(data.tipos_concessionarias[$this.children().eq(3).children().val()]);
                $this.children().eq(3).children().remove();

            });

            $('#bts_controller_5').attr('data-flag', '0');
            $('#bts_controller_5').hide();

            alert('Operação efetuada com sucesso!');
            
            for(i in dataPost){
//                console.log(dataPost[i].id_concessionaria);
//                console.log(dataPost[i].nome);
                $('#concessionaria_4').find('option[value='+dataPost[i].id_concessionaria+']').text(dataPost[i].nome);
            }
        } else {
            alert('Erro em atualizar os dados!');
//            limpar_edicao_clts();
        }
    }, 'json');
}

// item 6

//function form6() {
//
//    $('#din_6').carregando({text: 'indo...<br>'});
//
//    id_regiao = $('#regiao').val();
//    
//    if($('#tipo_fdias').val()==1){
//        cbo = $('#cbo_6').val();
//        curso = '';
//        horario = '';
//    }else{
//        cbo = '';
//        
//        curso = $('#curso_6').val();
//        horario = $('#horario_6').val();
//    }
//    var mes = '';
//    var ano = '';
//    var sempre = '0';
//    
//    if($('#referencia_6').val()==1){
//        sempre = 1;
//    }else{
//        mes = $('#mes_6').val();
//        ano = $('#ano_6').val();
//    }
//    
//    dias_uteis = $('#dias_uteis_6').val();
//
//    $.post(window.location, {acao: 'form_6', id_regiao: id_regiao,cbo: cbo, curso: curso, horario: horario, mes: mes, ano: ano, dias_uteis: dias_uteis, sempre: sempre}, function(data) {
//        $('#din_6').html(data);
//        $("#din_6 > .message-box").fadeOut(7000);
//    });
//}
function form6(cursos) {
    
    $('#new_cursos option').attr('selected','selected');

    $('#din_6').carregando({text: 'indo...<br>'});

    id_regiao = $('#regiao').val();
    
    if($('#tipo_fdias').val()==1){
        curso = '';
        horario = '';
        if (cursos === undefined) {
        var sobreescrever = 0;
            cursos = $('#new_cursos').val();
        }else{
            var sobreescrever = 1;
        }
    }else{
        curso = $('#curso_6').val();
        horario = $('#horario_6').val();
    }
    var mes = '';
    var ano = '';
    var sempre = '0';
    
    if($('#referencia_6').val()==1){
        sempre = 1;
    }else{
        mes = $('#mes_6').val();
        ano = $('#ano_6').val();
    }
    
    dias_uteis = $('#dias_uteis_6').val();
    
    
    
    console.log(cursos);

    $.post(window.location, {acao: 'form_6', id_regiao: id_regiao,curso: curso, cursos: cursos, horario: horario, mes: mes, ano: ano, dias_uteis: dias_uteis, sempre: sempre, sobreescrever: sobreescrever}, function(data) {
        $('#din_6').html(data);
//        $("#din_6 > .message-box").fadeOut(7000);
    });
}

function carregaCursosByRegiao(id_regiao) {
    $('#curso_6').attr('disabled','disabled');
    $('#horario_6').attr('disabled','disabled');
    $('#curso_6').html('<option>carregando...</option>');
    $('#horario_6').html('<option>carregando...</option>');
    $.post(window.location, {acao: 'get_cursos', id_regiao: id_regiao}, function(data) {
        option = '<option value="todos">Todos os Cursos</option>';
        
        console.log(data);
        
        for (var i in data.cursos) {
            option += '<option value="' + data.cursos[i].chave + '">' + data.cursos[i].valor + '</option>';
        }
        $('#curso_6').removeAttr('disabled');
        $('#horario_6').removeAttr('disabled','disabled');
        $('#curso_6').html(option);
        $('#horario_6').html('<option value="todos">Todos os Horários</option>');
    }, 'json');
}
function carregaHorariosByCurso(id_curso) {
    $('#horario_6').attr('disabled','disabled');
    $('#horario_6').html('<option>carregando...</option>');
    $.post(window.location, {acao: 'get_horarios', id_curso: id_curso}, function(data) {
        option = '';
        for (var i in data.horarios) {
            option += '<option value="' + data.horarios[i].chave + '">' + data.horarios[i].valor + '</option>';
        }
        $('#horario_6').removeAttr('disabled');
        $('#horario_6').html(option);
    }, 'json');
}
function carregaCursosByCBO(id_cbo) {
    $('#cursoscbo').html('<option>carregando...</option>');    
    $.post(window.location, {acao: 'get_cursos', id_cbo: id_cbo}, function(data) {        
        option = '';
        
        console.log(data.regioes);
        
        for (var x in data.regioes) {
             option += '<optgroup data-id="'+x+'" label="REGIÃO ' + x + ' - '+data.regioes[x] + '">';
            for (var i in data.cursos[x]) {
                option += '<option value="' + i + '" selected="selected" >' + data.cursos[x][i] + '</option>';
            }
        }
        $('#cursoscbo').html(option);
    }, 'json');
}

function deletar_dias_uteis(id) {
    $('#del_dias_uteis_' + id).carregando({'src': src_load});
    $.post(window.location, {acao: 'deletar_dias_uteis', id_dias_uteis: id}, function(data) {
        if (data.status) {
            $('#tr_dias_uteis_' + id).fadeOut();
        } else {
            alert('Erro em deletar registro!');
        }
    }, 'json');
}

function get_table_6() {
    $('#din_6').html('<p>carregando dados...</p><img src="' + src_load_h + '" />');
    id_regiao = $('#regiao').val();
    $.post(window.location, {acao: 'table_6', id_regiao: id_regiao}, function(data) {
        $('#din_6').html(data);
    });
}

// item 7

function form7() {
    $('#din_7').carregando({text: 'indo...<br>'});
    projeto = $('#projeto_7').val();
    sobrescreve_cnpj = $('#sobrescreve_cnpj_7').is(':checked');
    transporte = $('#transporte_7').is(':checked');
    matricula = $('#matricula_7').val();
    somente_novos = $('#somente_novos_3').is(':checked');
    cpf = $('#cpf_7').val();
    nome = $('#nome_7').val();
     if ($('#somente_novos_7').is(':checked')) {
        entrada = $('#mese_7').val() + '_' + $('#ano_7').val();
    } else {
        entrada = '';
    }
    $.post(window.location, {acao: 'form_7', projeto: projeto, sobrescreve_cnpj: sobrescreve_cnpj, transporte: transporte, matricula: matricula, cpf: cpf, nome: nome, data_entrada: entrada}, function(data) {
        $('#din_7').html(data);
    });
}

function editao_clt_dias(id) {
    $parent = $('#tr_' + id);
    $parent.children().eq(9).editaCampo({controller: '#bts_controller_3'});
}
function salvar_clts_dias() {
    $('#bt_limpar_clts_dias').hide();
    $('#bt_salvar_clts_dias').attr('disabled', 'disabled');
    $('#bt_salvar_clts_dias').val('Processando...');
    var dataPost = new Object();

    $("#table_7").find('tr.invalid').each(function(index) {
        $this = $(this);
        var obj = new Object();
        obj.id_clt = $this.children().eq(0).children('input[type=checkbox]').attr('data-value');
        obj.dias_uteis = $this.children().eq(9).children('input[type=text]').val();
        dataPost[index] = obj;
    });
    
    
    console.log(dataPost);
    
    var projeto = $('#projeto_7').val();
    var matricula = $('#matricula_7').val();
    var cpf = $('#cpf_7').val();
    var nome = $('#nome_7').val();
    var transporte = $('#transporte_2').is(':checked');
    
    if ($('#somente_novos_7').is(':checked')) {
        entrada = $('#mese_7').val() + '_' + $('#ano_7').val();
    } else {
        entrada = '';
    }
    
    $.post(window.location, {acao: 'salvar_dias_clt', data: dataPost,projeto: projeto, matricula: matricula,cpf: cpf, nome: nome, data_entrada: entrada, transporte: transporte}, function(data) {
//        $('#bt_limpar_edicao_clts').show();
//        if (data.status) {
////            fechar_edicao_clts_salvo();
//            alert('Operação efetuada com sucesso!');
//        } else {
//            alert('Erro em atualizar os dados!');
////            limpar_edicao_clts();
//        }
        $('#din_7').html(data);
        $('#bt_salvar_clts_dias').val('Salvar');
        $('#bt_salvar_clts_dias').removeAttr('disabled');
    });
}
function deletar_dias_clt(id) {
    var link = $('#del_dias_uteis_' + id).parent();
    link.carregando({'src': src_load});
    $.post(window.location, {acao: 'deletar_dias_uteis', id_clt: id}, function(data) {
        if (data.status) {
            alert('Registro deletado com sucesso!');
            link.parent().prev().prev().html('');
            link.remove();
        } else {
            alert('Erro em deletar registro!');
        }
    }, 'json');
}
function limpar_clts_dias() {
    $("#table_7").find('tr.invalid').each(function(index) {
        $this = $(this);
        $this.removeClass('invalid');
        $this.children().eq(0).children().removeAttr('checked');

        $this.children().eq(2).children().remove();
        $this.children().eq(2).html($this.children().eq(2).attr('data-value'));

        $this.children().eq(6).children().remove();
        $this.children().eq(6).html($this.children().eq(6).attr('data-value'));

        $this.children().eq(7).children().remove();
        $this.children().eq(7).html($this.children().eq(7).attr('data-value'));

        $this.children().eq(8).children().remove();
        $this.children().eq(8).html($this.children().eq(8).attr('data-value'));
    });
    $('#bts_controller_3').attr('data-flag', '0');
    $('#bts_controller_3').hide();
}


function mudatipo_diasuteis(){
    var val = $('#tipo_fdias').val();
    
    console.log('FOI...');
    console.log(val);
    if(val==1){
        $('.box_tp6_1').show();
        $('.box_tp6_2').hide();        
    }else{
        $('.box_tp6_2').show();
        $('.box_tp6_1').hide();
    }
}

//
// FUNÇÕES DIVERSAS
//


$.fn.carregando = function(options) {
    defaults = {src: '../../imagens/carregando/loading.gif', text: ''}
    var opts = $.extend(defaults, options);
    return $(this).html(opts.text + '<img src="' + opts.src + '" />');
}

$.fn.editaCampo = function(options) {
    $this = $(this);
    defaults = {type: 'input', value: $this.attr('data-value'), check: null, name: '', class: 'invalid', controller: false};
    var opts = $.extend(defaults, options);
    var resp = '';
    var flag = $(opts.controller).attr('data-flag');
    if ($this.children().attr('data-edit') == 1) {
        console.log(opts.value);
        resp = opts.value;
        $this.parent().removeClass(opts.class);
        flag--;
    } else {
        console.log(' t 2');
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

function calcular_datas(mes_base, ano_base){
    $.post(window.location, {acao: 'calcula_datas', mes_base: mes_base, ano_base: ano_base}, function(data) {
        $('#dataini_1').val(data.inicial.dia+'/'+data.inicial.mes+'/'+data.inicial.ano);
        $('#datafim_1').val(data.final.dia+'/'+data.final.mes+'/'+data.final.ano);
        $('#dias_uteis_1').val(data.total_dias_uteis);
        $('.cp_data').removeAttr('disabled');
        console.log(data);
    }, 'json');
}

function cancelaEvento(e) {
    e.preventDefault();
    console.log('Evento cancelado...');
}
function mudar_aba(id) {
    $(".colDir > div").hide();
    $("#item" + id).show();
    $(".bt-menu").removeClass("aselected");
    $('.bt-menu[data-item=' + id + ']').addClass("aselected");
}

function add_fn_cbo(acao){
    
    var arr = new Array();
    var arrM = new Array();
    
    if(acao==1){
        $('#new_cursos').find('option:selected').each(function(i){
            var $this = $(this);
            if($this.parent().children('option').length>1){
                $this.remove();
            }else{
                $this.parent().remove();
            }
        });        
    }else if(acao==2){
        
        $('#cursoscbo').find('option:selected').each(function(i){
            var $this = $(this);
            var id = $this.parent().attr('data-id');
            arrM[id] = $this.parent().attr('label');
            arr[id+'_'+$this.val()] = $this.text();
        });
        
        var optgroup = '';

        for(i in arrM){
            optgroup += '<optgroup label="'+arrM[i]+'" id="opt_'+i+'"></optgroup>';
        }

        $('#new_cursos').html(optgroup);

        for(i in arr){
            keys = i.split('_');
            $('#opt_'+keys[0]).append('<option value="'+keys[1]+'" selected="selected" >'+arr[i]+'</option>');
        }
    }
    
//    console.log(arrM);
//    console.log(arr);
   
}