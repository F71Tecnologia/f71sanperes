$(function() {
    //VALIDAÇÃO
    $("#form1").validationEngine();
    $("#data_emissao").datepicker();
    $("#valor_bruto").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
    
    //RECARREGA O TIPO
    var tipo = $("#subgrupo_selected").val();
    if((tipo != null) && (tipo != undefined) && (tipo != '')){
        get_tipo(tipo, $("#tipo_selected").val());
    }
    
    //RECARREGA REGIAO PRESTADOR
    var regiaoPrestadorSelected = $("#regiao_prestador_selected").val();
    if((regiaoPrestadorSelected != null) && (regiaoPrestadorSelected != undefined) && (regiaoPrestadorSelected != '')){
        getProjetoPrestador(regiaoPrestadorSelected, $("#projeto_prestador_selected").val());
    }
    
    //RECARREGA PRESTADOR
    var status = $("#status_selected").val();
    if((status != null) && (status != undefined) && (status != '')){
        getPrestador(status, $("#regiao_prestador_selected").val(), $("#projeto_prestador_selected").val(), $("#prestador_selected").val());
    }
    
    //RECARREGA PRESTADOR FILTRO
    var prestador_filtro = $("#prestador_filtro").val();
    if((prestador_filtro != null) && prestador_filtro != undefined && prestador_filtro != "" ) {
        getPrestadorFiltro($("#regiaoFiltro").val(), $("#projeto_filtro").val(), $("#prestador_filtro").val());
    }
    
    //CARREGA REGIAO
    $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(){
        if($("#acao [value='editar']")){
            var cad_projeto = $("#projeto_selected").val();
            $("#cad_projeto option[value='"+cad_projeto+"']").attr({selected:true});
        }
    }, "cad_projeto");
    
    //CARREGA REGIAO FILTRO
    $('#regiaoFiltro').ajaxGetJson("../../methods.php", {method: "carregaProjetos", request:"regiaoFiltro" }, function(){
        var projeto = $("#projeto_filtro").val();
        $("#projeto option[value='"+projeto+"']").attr({selected:true});
    }, "projeto");
    
    //CARREGA PRESTADOR/FORNECEDOR
    $("#projeto").change(function(){
        getPrestadorFiltro($("#regiaoFiltro").val(), $(this).val());
    });
    
    //CARREGA REGIAO DO PRESTADOR
    $('#cad_regiao_prestador').change(function(){
       var regiao = $(this).val();
       getProjetoPrestador(regiao, $("#regiao_prestador_selected").val());
    });

    //CARREGA TIPO
    $("#cad_subgrupo").change(function(){
       var subgrupo = $(this).val();
       if((subgrupo != null) && (subgrupo != undefined) && (subgrupo != '')){       
           get_tipo(subgrupo);
       }
    });
    
    //CARREGA PRESTADOR
    $("input[name='status']").change(function(){
       var status = $(this).val(); 
       if(status != null && status != undefined && status != ""){
           getPrestador(status, $("#cad_regiao_prestador").val(), $("#cad_projeto_prestador").val(), $("#prestador_selected").val());
       }
    });
    
    $("#cad_projeto_prestador").change(function(){
        var val = $(this).val();
        if(val != ''){
            $(".tipo_empresa").show();
            $("input[name='status']").attr({checked:false});
            $("#cad_prestador option[value='-1']").attr({selected:true});
        }else{
            $(".tipo_empresa").hide();
        }
    });
    
    //NOVO
    $("#novo").click(function(){
       $("#acao").val("cadastrar");
       $("#form1").attr({action:"notas_form.php"}).submit(); 
    });
    
    //EDITAR
    $(".edit_nota").click(function(){
       var nota = $(this).data("key");
       $("#acao").val("editar");
       $("#nota").val(nota);
       $("#form1").attr({action:"notas_form.php"}).submit();  
    });
    
    //REMOVER
    $(".remove_nota").click(function(){
        var nota = $(this).data("key");
        thickBoxConfirm("Remover Notas", "Deseja realmente remover essa nota?", 450, 250, function(data){
            if(data === true){
                removeNota(nota);
            }
        });
    });
    
    //CANCELAR
    $("#cancelar").click(function(){
        history.go(-1);
    });
    
    //CADASTRAR
    $("#cadastrar").click(function(){
        $("#form1").attr({action:"controller/notas.php"}).submit();
    });
    
    $("#editar").click(function(){
        $("#form1").attr({action:"controller/notas.php"}).submit();
    });
    
});

var removeNota = function(nota){
    $.ajax({
       url:"controller/notas.php",
       type:"POST",
       dataType:"json",
       data:{
           acao:"excluir",
           nota:nota
       },
       success: function(data){
           if(data.status){
               $("tr[data-key='"+nota+"']").remove();
           }
       }
    });
}


var get_tipo = function(tipo, tipoSelected){
    $.ajax({
        url:"",
        type:"POST",
        dataType:"json",
        data:{
           method:"carregaTipo",
           subgrupo:tipo
        },
        success: function(data){
            if(data.status){
                var html = "";
                $.each(data.dados,function(k, v){
                    $.each(v, function(key, value){
                        html += "<option value='"+key+"'>"+value+"</option>";
                    });
                });
                $("#cad_tipo").html(html);
            }
            
            $("#cad_tipo option[value='"+tipoSelected+"']").attr({selected:true});
        }
    });    
}

var getProjetoPrestador = function(regiaoPrestador, regiaoPrestadorSelected, localParaResultado){
    var local = "";
    if(localParaResultado != "" && localParaResultado != undefined){
        local = localParaResultado;
    }else{
        local = "#cad_projeto_prestador";
    }
    $.ajax({
        url:"controller/notas.php",
        type: 'POST',
        dataType:"json",
        data:{
           method:"carregaProjeto",
           regiao:regiaoPrestador
        },
        success: function(data){
            if(data.status){
                var html = "<option value=''>« Selecione o projeto »</option>";
                 $.each(data.dados,function(k, v){
                     $.each(v, function(key, value){
                         html += "<option value='"+k+"'>"+value+"</option>";
                     });
                 });
                 $(local).html(html);
            }
            
            $(local + " option[value='"+regiaoPrestadorSelected+"']").attr({selected:true});
        }
     });
}

var getPrestador = function(status, regiaoSelected, projetoSelected, prestadorSelected){
    $.ajax({
        url:"",
        type:"POST",
        dataType:"json",
        data:{
            method:"carregaPrestador",
            regiao:regiaoSelected,
            projeto:projetoSelected,
            status:status
        },
        success:function(data){
            if(data.status){
                var html = "";
                $.each(data.dados,function(k, v){
                    html += "<option value='"+v.id_prestador+"'>"+v.nome+"</option>";
                });
                
                $("#cad_prestador").show().html("<option value='-1'>« Selecione »</option>");
                $("#cad_prestador").append(html);
            }
            
            $("#cad_prestador option[value='"+prestadorSelected+"']").attr({selected:true});
        }
    });
}

var getPrestadorFiltro = function(regiaoSelected, projetoSelected, ultimoSelecionado){
    var prestadorSelected = "";
    if(ultimoSelecionado != "" && ultimoSelecionado != undefined){
        prestadorSelected  = ultimoSelecionado;
    }else{
        prestadorSelected  = "-1";
    }
    $.ajax({
        url:"",
        type:"POST",
        dataType:"json",
        data:{
            method:"carregaPrestador",
            regiao:regiaoSelected,
            projeto:projetoSelected,
            status:1
        },
        success:function(data){
            if(data.status){
                var html = "";
                $.each(data.dados,function(k, v){
                    html += "<option value='"+v.id_prestador+"'>"+v.nome+"</option>";
                });
                
                $("#prestador").show().html("<option value='-1'>Selecione o prestador</option>");
                $("#prestador").append(html);
            }
            
            $("#prestador option[value='"+prestadorSelected+"']").attr({selected:true});
        }
    });
}