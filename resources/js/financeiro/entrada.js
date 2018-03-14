$(function() {
    
    $(".bt-image").on("click", function() {
        var action = $(this).data("action");
        var type = $(this).data("type");
        var key = $(this).data("key");
        var target = $(this).data("target");
        
        if(action === "editar"){
            //console.log(target);return false;
            $("#"+type).val(key);
            $("#form1").attr('action','../form_'+type+'.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }else if(action === "saida"){
            //console.log(target);return false;
            $("#"+type).val(key);
            $("#form1").attr('action','../form_'+type+'.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }else if(action === "saida_rh"){
            $("#saida").val(key);
            $("#form1").attr('action','../form_saida_rh.php');
            $("#form1").prop('target',target);
            $("#form1").submit();
        }
        $("#form1").attr('action','');
        $("#form1").prop('target','');
    });
        
    BootstrapDialog.confirm = function(message, callback) {
        new BootstrapDialog({
            title: 'Confirmação de Exclusão',
            message: message,
            closable: false,
            data: {
                'callback': callback
            },
            buttons: [{
                    label: 'Cancelar',
                    action: function(dialog) {
                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                        dialog.close();
                    }
                }, {
                    label: 'OK',
                    cssClass: 'btn-primary',
                    action: function(dialog) {
                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                        dialog.close();
                    }
                }]
        }).open();
    };
    
    $('#tipo').change(function() {
        var id_tipo = $(this).val();
        
        if (id_tipo == '12') {
            $(".minus").show();
        } else {
            $(".minus").hide();
        }
    });
        
    //trazer dados referente ao tipo 12 na edição de entrada
    var id_tipo = $("#tipo").val();    
    if (id_tipo == '12') {
        $(".minus").show();
    } else {
        $(".minus").hide();
    }
    
    $('#regiao').change(function() {
        var regiao = $(this).val();
        
        if (($('#tipo').val() == '12') && ($(this).val() == '-1')) {
            $('#alerta_parceiro').hide();
        }
        
        $.ajax({
            url: "parceiros_total.php",
            type: "POST",
            dataType: "json",
            data: {
                id: regiao,
                method: "total_parceiros"
            },
            success: function(data) {
                if ((data.total == 0) && (data.regiao != -1)) {
                    $('#parceiro').hide();
                    $('#alerta_parceiro').show();
                    $('#notas').hide();
                } else {
                    $('#parceiro').show();
                    $('#alerta_parceiro').hide();
                }
            }
        });
    });        
    
    if($("#hide_parceiro").val() >= 1){
        $('#parceiro').change();
    }
    
    $('#parceiro').change(function() {
        var parceiro = $(this).val();
        var id_entrada = $("#id_entrada").val();
        
        if (parceiro >= 1) {
            $('#notas').show();
            $.ajax({
                url: 'actions/combo.entradas.php',
                type: "POST",
                dataType: "json",
                data: {
                    notas: true,
                    id_parceiro: parceiro,                    
                    id_entrada: id_entrada
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    //console.log(XMLHttpRequest);
                    //console.log(textStatus);
                    console.log(errorThrown);
                },
                success: function(registros) {
                    console.log(registros);
                    var table = '';
                    
                    if (registros.tot_n_assoc > 0) {
                        
                        //não associadas
                        table += '\
                                <table class="table table-hover table-striped">';
                        table += '\
                                <thead>\n\
                                    <tr class="active">\n\
                                        <th colspan="5" class="text-center">Notas</th>\n\
                                    </tr>\n\
                                    <tr>\n\
                                        <td></td>\n\
                                        <td>Nº da nota</td>\n\
                                        <td>Data</td>\n\
                                        <td>Valor</td>\n\
                                        <td>Ver Nota</td>\n\
                                    </tr>\n\
                                </thead>\n\
                                <tbody>';                        
                        
                        $.each(registros.nao_associada, function(i, valor) {
                            if (valor.anexo != '') {
                                var link = '<a href="javascript:;" class="ver_arquivo" data-key="' + valor.id_notas + '">Ver</a>';
                            } else {
                                var link = '';
                            }
                            
                            table += '\
                                    <tr>\n\
                                        <td><input type="radio" name="radio_nota" value="' + valor.id_notas + '"/></td>\n\
                                        <td>' + valor.numero + '</td>\n\
                                        <td>' + valor.data_emissao + '</td>\n\
                                        <td>R$ ' + valor.valor + '</td>\n\
                                        <td align="center">' + link + '</td>\n\
                                    </tr>';
                        });
                        
                        table += '\
                                </tbody>\n\
                                </table>';
                    }
                    
                    if (registros.tot_assoc > 0) {
                        
                        //associadas
                        table += '\
                                <table class="table table-hover table-striped">';
                        table += '\
                                <thead>\n\
                                    <tr class="active">\n\
                                        <th colspan="7" class="text-center">Notas Associadas</th>\n\
                                    </tr>\n\
                                    <tr>\n\
                                        <th></th>\n\
                                        <th>Nº da Nota</th>\n\
                                        <th>Data</th>\n\
                                        <th>Valor</th>\n\
                                        <th>Entradas</th>\n\
                                        <th>Ver Entradas</th>\n\
                                        <th>Ver Notas</th>\n\
                                    </tr>\n\
                                </thead>\n\
                                <tbody>';                                                                
                        
                        $.each(registros.associada, function(i, valor) {
                            
                            if (valor.anexo != '') {
                                var link = '<a href="javascript:;" class="ver_arquivo" data-key="' + valor.id_notas + '">Ver Notas</a>';
                            } else {
                                var link = '';
                            }
                            
                            var ver_entrada = '<a href="javascript:;" class="ver_entrada" data-key="' + valor.id_notas + '" data-cod="' + valor.numero + '">Ver Entradas</a>';
                            
                            if (valor.checked == 1) {
                                var marcado = 'checked="checked"';
                            } else {
                                var marcado = '';
                            }
                            
                            table += '\
                                    <tr>\n\
                                        <td><input ' + marcado + ' type="radio" name="radio_nota" value="' + valor.id_notas + '" /></td>\n\
                                        <td>' + valor.numero + '</td>\n\
                                        <td>' + valor.data_emissao + '</td>\n\
                                        <td>' + valor.valor + '</td>\n\
                                        <td>' + valor.total_entrada + '</td>\n\
                                        <td>' + ver_entrada + '</td>\n\
                                        <td>' + link + '</td>\n\
                                    </tr>';
                        });
                        
                        table += '\
                                </tbody>\n\
                                </table>';
                    }
                    
                    $('#notas').html(table);
                }
            });
        }
    });
    
    $("#tipo_anual").change(function() {
        var tipo = $(this).val();
        console.log(tipo);
        if (tipo == 1) {
            $(".t_entrada").show();
            $(".t_saida").hide();
            $("#tipo_saida").val('-1');
            $('.banco').removeClass('col-lg-offset-1');
        } else if (tipo == 2) {
            $(".t_saida").show();
            $(".t_entrada").hide();
            $("#tipo_entrada").val('-1');
            $('.banco').removeClass('col-lg-offset-1');
        } else {
            $(".t_saida").hide();
            $(".t_entrada").hide();
            $("#tipo_entrada").val('-1');
            $("#tipo_saida").val('-1');
            $('.banco').addClass('col-lg-offset-1');
        }
    });
    
    $("#tipo_desc").change(function() {
        var tipo = $(this).val();
        
        if (tipo == "entrada") {
            $(".t_entrada").show();
            $(".t_saida").hide();
            $("#tipo_saida").val('-1');
        } else if (tipo == "saida") {
            $(".t_saida").show();
            $(".t_entrada").hide();
            $("#tipo_entrada").val('-1');
        } else {
            $(".t_saida").hide();
            $(".t_entrada").hide();
            $("#tipo_entrada").val('-1');
            $("#tipo_saida").val('-1');
        }
    });
    
    //oculta numero do subtipo
    if($("#subtipo").val() == 4){        
        $("#num").hide();
        $("#n_subtipo").val('');
    }
    
    $("#subtipo").change(function() {
        var subtipo = $(this).val();
        
        if (subtipo == 4) {
            $("#num").hide();
            $("#n_subtipo").val('');
        } else {
            $("#num").show();
        }
    });
    
    $("body").on("click", ".ver_entrada", function() {
        var id = $(this).data("key");
        var cod = $(this).data("cod");
        
        BootstrapDialog.show({
            title: 'Entrada(s) Relacionada(s) a nota ' + cod,
            message: $('<div></div>').load('ver_entradas.php?id=' + id)
        });
    });        
    
    $("body").on("click", ".ver_arquivo", function() {
        var id = $(this).data("key");
        var cod = $(this).data("cod");
        
        BootstrapDialog.show({
            title: 'Administração do Notas Fiscais',
            message: $('<div></div>').load('ver_arquivos.php?id=' + id)
        });
    });
    
    $('body').on("click", "#del", function(){
        var entrada = $(this).data("key");
        
        BootstrapDialog.confirm('Deseja realmente excluir essa entrada?', function(result) {
            if (result) {
                $.ajax({
                    url:"del_entrada.php",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:entrada,
                        method:"del_entrada"
                    },
                    success:function(data){
                        if(data.status){
                            $("#"+entrada).remove();
                        }
                    }
                });
            }
        });
    });
    
    $(".ver_entrada_file").click(function(){        
        var id_entrada = $(this).data("entrada");
        
        BootstrapDialog.show({
            title: 'Administração do Notas Fiscais',
            message: $('<div></div>').load('../ver_arquivos.php?id_entrada='+id_entrada)
        });
    });
    
    $(".comprovante").click(function(){
        var action = $(this).data("type");
        var key = $(this).data("key");
        var rescisao = $(this).data("rescisao");                
        
        if(action === "file"){
            if(rescisao != ""){
                $(this).attr('href','/intranet/rh/recisao/'+rescisao);
                $(this).attr('target','_blank');
                $(this).submit();
            }else{
                BootstrapDialog.show({
                    title: 'Comprovantes',
                    message: $('<div></div>').load('../ver_comprovantes.php?id_saida='+key+'&id=1')
                });
            }
        }else if(action === "file_pg"){
            BootstrapDialog.show({
                title: 'Comprovantes de Pagamento',
                message: $('<div></div>').load('../ver_comprovantes.php?id_saida='+key+'&id=2')
            });
        }
    });
});