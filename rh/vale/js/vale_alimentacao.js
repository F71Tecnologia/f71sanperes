$(function() {
    
    /** add class para post do formulário dinâmico **/
    $('fieldset').find('input:not([type=button])').each(function(i) {
        var $this = $(this);
        $this.addClass('form_data');
    });
    $('fieldset').find('select').each(function(i) {
        var $this = $(this);
        $this.addClass('form_data');
    });
    /** fim add class para post do formulário dinâmico **/
    
    $('#form1').click(function(){
        $(this).postForm({content: '#din_1', acao: 'gerar_relacao_clt_pedido'}, function(data){
            $('#din_1').html(data.html);
        },'json' );
    });
    $('#form2').click(function(){
        $(this).postForm({content: '#din_2'}, function(data){
            $('#din_2').html(data.html);
        },'json');
    });
    $('#form4').click(function(){
        $(this).postForm({content: '#din_4'}, function(data){
            $('#din_4').html(data);
        } );
    });
    /* carrega pedidos */
    $('#projeto_0').change(function(){
        getTable('0');
    });
    $('.colDir').on('click','a[id^=ver_pedido_]', function(){
        $this = $(this);
        var arr = $this.attr('id').split('_');
        var id = arr[2];
        $this.carregando({'load': 1});
        $.post(window.location,{acao: 'relacao_clt_pedido', id_pedido: id },function(data){
            $('#din_0').html(data.html);
        },'json');
    });
//    $('.colDir').on('click','a[id^=exportar_pedido_]', function(){
//        
//        $('.form_dias_uteis').hide();
//        $('.form_exportar_pedido').show();
//        
//        $this = $(this);
//        var arr = $this.attr('id').split('_');
//        var id = arr[2];
//        var data_entrega = $('#data_entrega').val();
//        var data_credito = $('#data_credito').val();
//        
//        $('#din_modal').html('');
//        $this.carregando({'load': 1,'text':'Processando...'});
//        $('.din_exportacao').html('');
//        
//        $('#deb').remove();
//        
//        
//        $.post(window.location,{acao: 'relacao_clt_pedido', id_pedido: id, exportar_pedido: 1,data_entrega: data_entrega, data_credito: data_credito },function(data){
//            $this.after('<br><textarea id="deb" style="width: 100%; height: 300px"  wrap=OFF>'+data.html+'</textarea><br>');
////            $('#din_0').html(data.html);
//        },'json');
//    });
    $('.colDir').on('click','a[id^=link_del_pedido_]', function(){
        if(confirm('Deseja realmente deletar este pedido?')){
            $this = $(this);
            var arr = $this.attr('id').split('_');
            var id = arr[3];
            $this.carregando({'load': 1});
            $.post(window.location,{acao: 'deletar_pedido', id_pedido: id },function(data){
                if(data.status){                    
                    $('#tr_pedido_'+id).fadeOut();
                }else{
                    alert('Erro em deletar o pedido!');
                }
            },'json');
        }
    });
    $('.colDir').on('click','#cria_pedido', function(){
        
        $('#projeto_0').val($('#projeto_1').val());
        
        dataForm = eval('['+$('#relacao_pedido').attr('data-form')+']');
        
        data = new Object();
        data.form_data = dataForm[0];
        data.acao = 'cria_pedido';
        
        $.post(window.location,data, function(data){
            if(data.pedido>0){
                $('#nav').children().eq(0).children().click();
                $('#din_0').html(data.html);
                $('#tr_pedido_'+data.pedido+' td').css('background','#FFF4CC');
                
                console.log(data.projetos);
                
                var option = '';
                for(p in data.projetos){
                    
                    var selected = '';
                    if(p==data.id_projeto){
                        selected = ' selected="selected" ';
                    }
                    
                    option += '<option val="'+p+'" '+selected+' >'+data.projetos[p]+'</option>';
                }
                
                $('#regiao_0').val(data.regiao);
                $('#projeto_0').html(option);
                
            }else{
                alert('Erro!');
            }
        },'json');
        
    });
    $('.colDir').on('click','#exporta_usuario', function(){
        $(this).postForm({content: '#din_4'}, function(data){
            $('#din_4').html(data);
        } );
    });
    
    $('#get_valores_diarios').change(function(){
        var $this = $(this);
        
        $this.postForm({content: '#din_3',dataPost:{regiao:$this.val()}}, function(data){
            $('#din_3').html(data);
            $('#nome_regiao_3').html($('#get_valores_diarios > option:selected').text());
        } );
    });
    $('#cadastrar_valor_diario').click(function(){
        $(this).postForm({content: '#din_3',dataPost:{regiao:$('#get_valores_diarios').val()}}, function(data){
            $('#din_3').html(data);
        } );
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
    
    $('.colDir').on('change','select[id^=regiao_]',function(){
        
        var $this = $(this);
        
      
        
//        $('.colDir').find('select[id^=regiao_]').each(function(i) { 
//            var select = $(this);
//            
//            var arr = select.attr('id').split('_');
//            k = arr[1];
//            
//            if(select.attr('id')!=$this.attr('id')){
//                select.val('-1');
//                $('#din_'+k).html('');
//                $('#projeto_'+k).html('<option> SELECIONE </option>');
//            }
//        });
        
        
        
        $('.load_projetos').remove();
        
        $this.after('<img src="../../imagens/carregando/ARROWS.gif" width="16" height="16" border="0" class="load_projetos" style="margin: 0px 0px 0 8px" >');
        
        var arr = $this.attr('id').split('_'); 
        
        $('#din_'+arr[1]).html('');
        
        $('#projeto_'+arr[1]).html('<option>carregando...</option>');
        $.post(window.location, {acao: 'get_projetos', id_regiao: $this.val()}, function(data) {
            $('.load_projetos').remove();
            var html = '<option value="-1" > Selecione </option>';
            for(i in data){
               html += '<option value="'+i+'" >'+data[i]+'</option>'; 
            }
            $('#projeto_'+arr[1]).html(html);
        },'json');
    });
    
    $('.colDir').on('click','a[id^=exclui_valor_diario]',  function(){
        var $this = $(this);
        var arr = $this.attr('id').split('_');
        var id = arr[3];
        var vinculos = arr[4];
        console.log(vinculos);
        
        if(vinculos<=0){
            if(confirm('Deseja realmente excluir este valor diário?')){
                $this.carregando({'load': 1});
                $.post(window.location, {acao: 'exclui_valor_diario', id: id}, function(data){
                    $('#3_'+id).delay(1000).fadeOut();
                    alert('Valor diário deletado com sucesso!');
                },'json');
            }
        }else{
            alert('Não pode possuir vínculos!');
        }
    });
    
    $('.request_data').change(function() {
        
        $('.load_data').remove();
        
        $this = $(this);
        
        $this.after('<img src="../../imagens/carregando/ARROWS.gif" width="16" height="16" border="0" class="load_data" style="margin: 0px 0px 0 8px" >');
        
        ano = $('#ano_1').val();
        mes = $('#mes_1').val();

        $('#dataini_1').attr('disabled','disabled');
        $('#datafim_1').attr('disabled','disabled');
        $('#dias_uteis_1').attr('disabled','disabled');
        

        $.post(window.location, {acao: 'calcula_datas', ano_base: ano, mes_base: mes}, function(data) {
            $('.load_data').remove();
            $('#dataini_1').val(data.inicial);
            $('#datafim_1').val(data.final);
            $('#dias_uteis_1').val(data.total_dias_uteis);            
            $('#dataini_1').removeAttr('disabled','disabled');
            $('#datafim_1').removeAttr('disabled','disabled');
            $('#dias_uteis_1').removeAttr('disabled','disabled');
        }, 'json');
    });
    $('input.money').priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
    
    $('.colDir').on('click', '.grid > tbody > tr > td input[type=checkbox]', function() {
        

        
        var $input = $(this);
        var $tr = $input.parent().parent();
//        var arr = $input.val().split('_');
        var arr = $tr.attr('id').split('_');
        
        
        
        
        obj_relacao_tarifas = eval('['+$('#table_'+arr[0]).attr('data-tarifas')+']');
        
        var sel = $('#table_'+arr[0]).find('tr[class=selected]').length;        
        
        if($tr.hasClass('selected')){
            
            if(sel==1){
                $('#box_acao_'+arr[0]).remove();
                $('#box_acaopri_'+arr[0]).show();
            }
            
            $tr.removeClass('selected');
            switch (arr[0]){
                case '1' :
                    console.log('ok 1');
                    break;
                case '2' :
                    var $tdValor = $('#td_valor_'+arr[1]);
                    var key = $tdValor.attr('data-key');
                    var val = $tdValor.children().children('option[value='+key+']').text();
                    if(val==""){
                        val = 'R$ 0,00';
                    }
                    $tdValor.html(val);
                    
                    var $tdMatricula = $('#td_matricula_'+arr[1]);
                    var matricula = $tdMatricula.attr('data-key');
                    console.log('cancelando =>'+matricula);
                    $tdMatricula.html(matricula);
                    
                    break;
                case '3' :
                    var $tdValor = $('#td_valor_tarifa_'+arr[1]);
                    var valor = $tdValor.attr('data-valor');
                    $tdValor.html('R$ '+valor);
                    break;
            }
        }else{
            
            if(sel==0){
                $('#table_'+arr[0]).after('<div style="text-align: right;" id="box_acao_'+arr[0]+'"><br><input type="button" value="Cancelar"  id="cancelar_table_'+arr[0]+'"  />&nbsp;<input type="button" value="Salvar Alterações" id="acao_table_'+arr[0]+'" /></div>');
                $('#box_acaopri_'+arr[0]).hide();
            }
            
            $tr.addClass('selected');
            switch (arr[0]){
                case '1' :
                    console.log('ok 1');
                    break;
                case '2' :
                    
                    var $tdValor = $('#td_valor_'+arr[1]);
                    var key = $tdValor.attr('data-key');                    
                    $tdValor.html(createSelect(obj_relacao_tarifas[0],key));
                    
                    var $tdMatricula = $('#td_matricula_'+arr[1]);
                    var matricula = $tdMatricula.attr('data-key');
                    $tdMatricula.html(createInput(matricula));
                    console.log('ok 2');
                    break;
                case '3' :
                    var $tdValor = $('#td_valor_tarifa_'+arr[1]);
                    var valor = $tdValor.attr('data-valor');                    
                    $tdValor.html(createInput('R$ '+valor,' class="money_f" '));
                    $('input.money_f').priceFormat({prefix: 'R$ ', centsSeparator: ',', thousandsSeparator: '.'});
                    break;
            }
        }
        
        
        
    });
    
    $('.colDir').on('click','input[id^=cancelar_table]',function(){
       var $this = $(this);
       var id = $this.attr('id').split('_');
       id = id[2];
       
       $('#table_'+id).find('tr[class=selected]').each(function(i) {
           var $this = $(this);
           $this.children().eq(0).children('input[type=checkbox]').click();
       });
    });
    $('.colDir').on('click','input[id^=acao_table]',function(){
        var $this = $(this);
        var key = $this.attr('id').split('_');
        key = key[2];
        
        
        var dataPost = new Object();
        var dataTarifa = new Object();
        var dataMatricula = new Object();
        $('#table_'+key).find('tr[class=selected]').each(function(i) {
           var $this = $(this);
           id = $this.children().eq(0).children('input[type=checkbox]').val();
           
           dataTarifa[id] = getValorAcao(key,id);
           
           dataMatricula[id] = $('#td_matricula_'+id).children().val();
           
           dataPost.tarifas = dataTarifa;
           dataPost.matriculas = dataMatricula;
           
       });
       
       arr_acao = new Array();
       arr_acao[2] = 'atualiza_clt_tarifa';
       arr_acao[3] = 'atualizar_valor_diario';
       
       acao = arr_acao[key];
       
       regiao = getRegiao(key);
       
       
       var tipoVale = $('#tipo_vale').val();
       var catVale = $('#cat_vale').val();
       $.post(window.location,{cat_vale: catVale, tipo_vale: tipoVale, acao: acao, form_data: dataPost, regiao: regiao}, function(data){
           callbackAcao(key, data, dataPost);
           $('.message-box').delay(1000).fadeTo("slow", 0);
       },'json');
     });
});

var getTable = function getTable(key){
    var $this = $('#projeto_'+key);
    $this.postForm({content: '#din_0',acao:'carrega_pedidos'}, function(data){
        $('#din_0').html(data.html);
    },'json');
}

var callbackAcao = function callbackAcao(key, data, dataPost){
    console.log('CALLBACK '+key+', '+data+', '+dataPost)
   var valor = '0';
   console.log(key);
    switch (key){
        case '2':
            if(data.status){               
               $('#box_acao_2').after(createMsg('Alterações efetuadas com sucesso!','blue'));               
               for(i in dataPost['tarifas']){
                   
                 $('#td_valor_'+i).attr('data-key',dataPost['tarifas'][i]);
                 $('#td_matricula_'+i).attr('data-key',dataPost['matriculas'][i]);
                 $('#'+key+'_'+i).children().eq(0).children('input[type=checkbox]').click();
               }
           }else{
               $('#box_acao_2').after(createMsg('Erro na atualização.','red'));
           }
        break;
        case '3':
            $('#din_'+key).html(data.relacao);
        break;
        default :
            alert('sem callback!');
            break;
    }
    return valor;
    
}
var getValorAcao = function getValorAcao(key,id){
   var valor = '0';
    switch (key){
        case '2':
            valor = $('#td_valor_'+id).children('select').val();
        break;
        case '3':
            valor = $('#td_valor_tarifa_'+id).children('input').val();
        break;
    }
    return valor;
    
}

var getRegiao = function getRegiao(key){
   var regiao = '0';
    switch (key){
        case '3':
            regiao = $('#get_valores_diarios').val();
        break;
    }
    return regiao;
    
}

var createSelect = function createSelect(obj, selected){
    var s = '<select>';
    for(i in obj){
        var attr = (selected==i) ? ' selected="selected" ' : '';
        s += '<option value="'+i+'" '+attr+'>'+obj[i]+'</option>';
    }
    s += '</select>';
    return s;
}
var createInput = function createInput(val, attr){
    return '<input type="text" value="'+val+'" '+attr+' />';
}
var createMsg = function createMsg(msg, color){
    return '<br><div class="message-box message-'+color+'" ><p>'+msg+'</p></div>';
}
var editarDiasUteis = function editarDiasUteis(idClt, diasUteis, competencia){
    
    $('.form_exportar_pedido_1').hide();
    
    var nome = $('#tr_'+idClt).find('td[data-ref=nome]').html();
    
    $('#formDiasUteis').show();
    $('#formDiasUteis').css({ margin: '40px'});
    
    $('#inputIdClt').val(idClt);
    $('#inputCompetencia').val(competencia);
    $('#inputNome').val(nome);
    $('#inputDias').val(diasUteis);
    
}
var gravarDiasUteis = function gravarDiasUteis(){
    
    
    dataForm = eval('['+$('#relacao_pedido').attr('data-form')+']');    
    var dias = $('#inputDias').val();
    var idClt = $('#inputIdClt').val();
    var competencia = $('#inputCompetencia').val();
    
    $.post(window.location, {id_clt: idClt, dias: dias,competencia: competencia, acao:'salvar_dias_uteis_clt', form_data: dataForm[0]}, function(data){
        if(data.html!='' && data.status){
            $('#din_1').html(data.html);
            $('#inputIdClt').val('');
            $('#inputCompetencia').val('');
            $('#inputNome').val('');
            $('#inputDias').val('');
            $('.modal').modal('hide');
        }else{
            alert('Erro em editar os dias úteis')
        }
    },'json');
}

$.fn.postForm = function(options, fnCallBack, type) {
    
    if(typeof type === 'undefined'){
        type = 'html';
    }
    console.log(type);
    
    var $this = $(this);
    
    var acao = $this.attr('id');
    
    var d = new Object(); 
    d = {tipo_vale: tipoVale, acao: acao }
    
    var tipoVale = $('#tipo_vale').val();
    var catVale = $('#cat_vale').val();
    form_data = new Object();
    form_data = {tipo_vale: tipoVale,cat_vale:catVale}
    
    $this.parent().parent().find('.form_data').each(function(i) {
        var $thisData = $(this);
        if($thisData.attr('type')=='checkbox'){
            form_data[$thisData.attr('id')] = $thisData.is(':checked');
        }else{
            form_data[$thisData.attr('id')] = $thisData.val();
        }
    });    
    
    d.form_data = form_data;    
    
    var d = $.extend(d, options.dataPost, {acao : options.acao});
    
    defaults = {request: window.location, src: '../../imagens/carregando/loading.gif', text: 'carregando...' }
    var opts = $.extend(defaults, options);
    
    $(opts.content).html(opts.text + '<img src="' + opts.src + '" class="img_carregando" />');    
    
    console.log('DADOS DO POST: '+acao);
    console.log(d);
    
    $.post(opts.request, d,function(data){
        fnCallBack(data);
    }, type);
    
    return $this;
}

$.fn.carregando = function(options) {
    
    arrLoad = new Array();
    arrLoad[1] = '../../imagens/carregando/ARROWS.gif';
    arrLoad[2] = '../../imagens/carregando/loading.gif';    
    
    var src = arrLoad[1];
    
    defaults = {load: 1, text: ''}
    var opts = $.extend(defaults, options);
    return $(this).html(opts.text + '<img src="' + arrLoad[opts.load] + '" />');
}

var modalExportarPedido = function modalExportarPedido(id){
    
    var tipo_vale = $('#tipo_vale').val();
    
    $('#din_modal').html('');
    $('.form_dias_uteis').hide();
    
    
    console.log('.form_exportar_pedido_'+tipo_vale);
    
    $('.form_exportar_pedido_'+tipo_vale).show();
    $('#inputIdPedido').val(id);
    $('#myModal').modal('show');
    
    
    $('.date_exp').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true
    });

    
 }
var exportarPedido = function exportarPedido(id){
        var id = $('#inputIdPedido').val();
        $('.form_exportar_pedido').hide();
        $('#din_modal').carregando({'load': 2,'text':'Processando...'});
        var data_entrega = $('#inputDataEntrega').val();
        var data_credito = $('#inputDataCredito').val();
        $.post(window.location,{acao: 'relacao_clt_pedido', id_pedido: id, exportar_pedido: 1,data_entrega: data_entrega, data_credito: data_credito },function(data){            
            $('#myModal').modal('hide');
//            console.log('?download='+data.html.download+'&name_file='+data.html.name_file+'&tipo='+data.html.tipo);
            window.location = '?download='+data.html.download+'&name_file='+data.html.name_file+'&tipo='+data.html.tipo;
        },'json');
 }

//function arquivo_aelo(id){
//    $('#bt_arquivo_aelo').val('Gerando...');
//    $('#bt_arquivo_aelo').attr('disabled','disabled');
//    $('#table_fake').html('');
//    $.post(window.location,{acao:'arquivo_aelo', id_pedido: id},function(data){
//        $('#table_fake').html(data.dados);
//        $('#bt_arquivo_aelo').val('Criar Arquivo Exportação AELO');
//        $('#bt_arquivo_aelo').removeAttr('disabled');
//        window.location = '?download='+data.name_file;
//    },'json');
//}