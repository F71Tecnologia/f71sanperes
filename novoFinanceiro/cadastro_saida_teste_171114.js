$(function() {

    function mostraLoading() {
        $('#base').fadeTo('fast', 0.5);
        $("#loading").fadeIn();
        $('.submit-go').attr('disabled', true);
    }

    function ocultaLoading() {
        $('#base').fadeTo('fast', 1.0);
        $("#loading").fadeOut();
        $('.submit-go').removeAttr('disabled');
    }

    function limpar(ID) {
        $(ID).find('input[type*=text]').val('');
    }

    function limita_caractere(campo, limite, muda_campo) {

        var tamanho = campo.val().length;

        if (tamanho == limite) {
            campo.next().focus();
            var valor = campo.val().substr(0, limite);
            campo.val(valor)
        }
    }

    function get_nomes(tipo_id) {

        $.post('actions/combo.nome.json.php', {tipo: tipo_id}, function(retorno) {
            $("#nome").removeAttr('disabled');
            $("#nome").html('<option value="" selected="selected">Selecione</option>');
            $.each(retorno, function(i, valor) {
                if (valor.id_nome == id_nome) {
                    selected = 'selected="selected"';
                } else {
                    selected = '';
                }
                $("#nome").append('<option value="' + valor.id_nome + '" ' + selected + ' >' + valor.nome + '</option>');
            });
            ocultaLoading();
            $("#nome").focus();
        },'json');
    }

    $("#tipo2").val($("#tipo").val());
    
    $("#tipo").change(function() {
        $("#tipo2").val($(this).val());
    });

    $('#data_vencimento, #dt_emissao_nf').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true
    });

    $('#regiao_banco').change(function() {
        $.post('actions/combo.projeto.json.php', {regiao: $(this).val()}, function(retorno) {
            $('#projeto').html('<option value="" >Selecione</option>');
            
            
            $.each(retorno, function(i, valor) {
                $('#projeto').append('<option value="' + valor.id_projeto + '" >' + valor.id_projeto + ' - ' + valor.nome + '</option>');
            });

            ocultaLoading();
            $('#projeto').focus();
            $('#banco').html('');
            $('#banco').attr('disabled', true);
        }, 'json');
        
        $("#regiao-prestador").val('');
    });


    $('#projeto').change(function() {
        var selected = '';
        $.post('actions/combo.bancos.json.php', {projeto: $(this).val(), regiao: $("#regiao_banco").val()}, function(retorno) {
            $('#banco').html('<option value="" >Selecione</option>');
            
            if(retorno.length === 1)
                selected = "selected='selected'";
            
            $.each(retorno, function(i, valor) {
                $('#banco').append('<option value="' + valor.id_banco + '" '+selected+' >' + valor.id_banco + ' - ' + valor.nome + '</option>');
            });

            ocultaLoading();
            $('#banco').attr('disabled', false);
            $('#banco').focus();
        }, 'json');
    });

    $("#form2").submit(function(e) {

        mostraLoading();
        e.preventDefault();
        var dados = $(this).serialize();
        var exp = window.hs.getExpander();

        if (exp) {
            exp.close();
        }

        $.post('actions/form.submit.nomes.php', dados, function(retorno) {
            $.each(retorno, function(i, valor) {
                $("#nome").append('<option selected="selected" value="' + valor.id_nome + '">' + valor.nome + '</option>');
                ocultaLoading();
                limpar("#form2");
            });
        },'json');
    });


    $("select[name=grupo]").change(function() {

        mostraLoading();
        var grupo_id = $(this).val();
        var subgrupo_id = $('#saida_subgrupo').val();
        var selected_tipo;
        $('#saida_subgrupo').val('');       

        if (grupo_id == '') {
            ocultaLoading();
            return false;
        }

        if (grupo_id > 4) {
            $(".nomes-cad").show();
            $("#bruto").parent().hide();
            $("#bruto").val('0,00');
            $('.prestador').hide();
            $('.fornecedor').hide();
            $('#campo_subgrupo').show();
        } else if (grupo_id <= 4) {
            $('.interno').hide();
            $(".nomes-cad").show();
            $("#bruto").parent().hide();
            $("#bruto").val('0,00');
            $('.prestador').hide();
            $('.fornecedor').hide();
            $('#campo_subgrupo').hide();
        }

        $("#tipo").html('<option value="">Carregando...</option>');
        $("#tipo").attr('disabled', 'disabled');
        $("#nome").html('<option value="" selected="selected">Selecione</option>');

        $.post('actions/combo.subgrupo.json.php', {grupo: grupo_id}, function(retorno) {
            $("#subgrupo").removeAttr('disabled');
            $("#subgrupo").html('<option value="" selected="selected">Selecione</option>');

            $.each(retorno, function(i, valor) {
                if (subgrupo_id == valor.id) {
                    selected_tipo = 'selected="selected"';
                    $("#tipo").attr('disabled', false);
                } else {
                    selected_tipo = '';
                }

                $("#subgrupo").append('<option value="' + valor.id + '"' + selected_tipo + '>' + valor.id_subgrupo + ' - ' + valor.nome + '</option>');

            });

            ocultaLoading();

            $("#subgrupo").trigger("change");
            
            $("#regiao-prestador").val($("#regiao_banco").val());
            $("#regiao-prestador").trigger("change");

        }, 'json');
    });

    $("#tipo").change(function() {

        mostraLoading();
        var grupo = $('#grupo').val();
        var subgrupo = $('#subgrupo').val();
        var id_nome = $('#id_nome').val();

        var selected;


        if (($(this).val() == '132') || (grupo == 30) || grupo == 80 || grupo == 20) {

            $('.interno').fadeIn('slow');
            $(".nomes-cad").fadeOut('slow');
            $("#bruto").parent().fadeIn('slow');
            $("#bruto").val('0,00');
        } else {
            $('.interno').hide();
            $("#bruto").parent().hide();
            $("#bruto").val('0,00');
            $('.nomes-cad').show();
        }

        $("#nome").html('<option value="">Carregando...</option>');
        $("#nome").attr('disabled', 'disabled');
        $.post('actions/combo.nome.json.php',
                {tipo: $(this).val()},
        function(retorno) {

            $("#nome").removeAttr('disabled');
            $("#nome").html('<option value="" selected="selected">Selecione</option>');
            $.each(retorno, function(i, valor) {

                if (valor.id_nome == id_nome) {
                    selected = 'selected="selected"';
                } else {
                    selected = '';
                }
                $("#nome").append('<option value="' + valor.id_nome + '" ' + selected + ' >' + valor.nome + '</option>');

            });

            ocultaLoading();

            $("#nome").focus();

        }, 'json');
    });


    $('#subgrupo').change(function() {

        var saida_tipo_id = $('#saida_tipo').val();
        var grupo_id = $('#grupo').val();
        var selected;

        $.post('actions/combo.tipo.php',
                {subgrupo: $(this).val(), grupo_id: grupo_id},
        function(retorno) {
            $("#tipo").removeAttr('disabled');
            $("#tipo").html('<option value="" selected="selected">Selecione</option>');

            $.each(retorno, function(i, valor) {

                if (saida_tipo_id == valor.id_entradasaida) {
                    selected = 'selected="selected"';
                } else {
                    selected = '';
                }
                $("#tipo").append('<option value="' + valor.id_entradasaida + '" ' + selected + ' >' + valor.id_entradasaida + ' - ' + valor.cod + ' - ' + valor.nome + '</option>');

            });
            ocultaLoading();
            $("#tipo").trigger("change");
        }, 'json');
    });


    $('#regiao-prestador').change(function() {

        var projeto_prestador = $('#prestador_pg_projeto').val();
        var projeto_fornecedor = $('#fornecedor_pg_projeto').val();

        if (projeto_prestador != '') {
            var projeto_id = projeto_prestador;
        }else{
            var projeto_id = projeto_fornecedor;
        }

        mostraLoading();
        $('.novoPrestador').attr('href', ' ../processo/prestadorservico.php?regiao=' + $(this).val() + '&id=1');


        $.post('actions/combo.projeto.json.php', {regiao: $(this).val()}, function(retorno) {
            $('#interno').html('<option value="" selected="selected" >Selecione</option>');
            $('#Projeto-prestador').html('<option value="">Selecione</option>');
            
            var selected = '';
            
            $.each(retorno, function(i, valor) {
                if (projeto_id === valor.id_projeto)
                    selected = 'selected="selected"';
                
                $('#Projeto-prestador').append('<option value="' + valor.id_projeto + '" ' + selected + ' >' + valor.id_projeto + ' - ' + valor.nome + '</option>');
            });

            ocultaLoading();
            $('#Projeto-prestador').focus();
            $("#Projeto-prestador").val($("#projeto").val());
            $("#Projeto-prestador").trigger('change');
        }, 'json');
        
    });


    $('#Projeto-prestador').change(function() {       

        mostraLoading();

        var regiao_id = $('#regiao-prestador').val();
        var projeto_id = $(this).val();
        var prestador_id = $('#prestador_id').val();
        var fornecedor_id = $('#fornecedor_id').val();
        var selected;

        ////CARREGANDO OS PRESTADORES
        $.post('actions/combo.prestador.json.php', {regiao: regiao_id, projeto: projeto_id}, function(retorno) {
            $('#interno').html('<option value="" selected="selected" >Selecione</option>');
            $.each(retorno, function(i, valor) {
                if (prestador_id == valor.id_prestador) {
                    selected = 'selected="selected"';
                } else {
                    selected = '';
                }
                $('#interno').append('<option value="' + valor.id_prestador + '" ' + selected + ' >' + valor.id_prestador + ' - ' + valor.numero + ' - ' + valor.c_fantasia + ' - ' + valor.c_cnpj + '</option>');
                $("select[name=prestador]").val($("#prestador_id").val());                
            });
            
            $('#interno').focus();
        }, 'json' );
        

        ///CARREGANDO OS FORNECEDORES(PRESTADORES INATIVOS)
        $.post('actions/combo.prestador_inativo.json.php',
                {regiao: regiao_id, projeto: projeto_id},
        function(retorno) {
            if (retorno != null) {
                $('#fornecedor').html('<option value="" selected="selected" >Selecione</option>');
                $.each(retorno, function(i, valor) {


                    if (prestador_id == valor.id_prestador) {
                    selected = 'selected="selected"';
                } else {
                    selected = '';
                }
                $('#fornecedor').append('<option value="' + valor.id_prestador + '" ' + selected + ' >' + valor.id_prestador + ' - ' + valor.numero + ' - ' + valor.c_fantasia + ' - ' + valor.c_cnpj + '</option>');
                $("select[name=prestador_inativo]").val($("#prestador_id").val());
                });
            }
        }, 'json');
        ocultaLoading();

    });

    $("a.highslide").click(function() {

        if ($("#tipo").val() != "") {
            hs.htmlExpand(this, {outlineType: 'rounded-white', wrapperClassName: 'draggable-header', contentId: 'cadastro_nomes'});
        } else {
            alert("Selecione um tipo primeiro!");
        }
    });


    $('#referencia').change(function() {

        var referencia = $(this).val();
        if (referencia == 2) {
            $('#campo_bens').show();
        } else {
            $('#campo_bens').hide();
            $('#bens').val('');
        }
    });


    $('#tipo_boleto').change(function() {
        var tipo = $(this).val();
        if (tipo == 1) {
            $('#campo_nosso_numero').hide();
            $('.campo_codigo_consumo').show()
            $('.campo_codigo_gerais').hide();
            limpa_cod_barra();
        } else {
            $('#campo_nosso_numero').show();
            $('.campo_codigo_gerais').show();
            $('.campo_codigo_consumo').hide();
            limpa_cod_barra();
        }
    });

    $('#codigo_barra_consumo1, #codigo_barra_consumo3,#codigo_barra_consumo5, #codigo_barra_consumo7 ').keyup(function() {
        limita_caractere($(this), 11, 1)
    });
    $('#codigo_barra_consumo2, #codigo_barra_consumo4, #codigo_barra_consumo6').keyup(function() {
        limita_caractere($(this), 1, 1)
    });

    $('#codigo_barra_consumo8').keyup(function() {

        if ($(this).val().length == 1) {
            $('#real').focus();
        }

    });

    $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5').keyup(function() {
        limita_caractere($(this), 5, 1)
    });
    $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function() {
        limita_caractere($(this), 6, 1)
    });
    $('#campo_codigo_gerais7').keyup(function() {
        limita_caractere($(this), 1, 1)
    });
    $('#campo_codigo_gerais8').keyup(function() {
        if ($(this).val().length == 14) {
            $('#real').focus();
        }
    });


    $('input[name=tipo_empresa]').change(function() {
        var tipo = $(this).val();
        if (tipo == 1) {
            $('.prestador').show();
            $('.fornecedor').hide();
            $("#fornecedor").val('');
        } else {
            $('.fornecedor').show();
            $('.prestador').hide();
            $("#interno").val('');
        }
    });

    $('#tipo_pagamento').change(function() {

        var tipo_pg = $(this).val();
        if (tipo_pg == '1') {
            $('#campo_boleto').show();
            //  $('.n_documento').hide();
            $('.link_nfe').hide();

        } else if (tipo_pg == 3) {

            //  $('.n_documento').show();  
            $('.link_nfe').show();
            $('#campo_boleto').hide();
            $('.campo_codigo_consumo').hide();
            $('.campo_codigo_gerais').hide();
            $('#campo_nosso_numero').hide();

        } else {
            $('#campo_boleto').hide();
            $('.campo_codigo_consumo').hide();
            $('.campo_codigo_gerais').hide();
            $('#campo_nosso_numero').hide();
            //   $('.n_documento').show();
            $('.link_nfe').hide();

            limpa_cod_barra();

        }
    });

    ////////////////////////////////////
    ///////////FUNÇÕES//////////////////
    //////////////////////////////////


    //////////////ANEXOS/////////////////
    $('li.excluir, li.excluir_pg').click(function() {

        var div = $(this);
        var id_arquivo = div.attr('value');
        var tipo_anexo = div.attr('rel');


        if (window.confirm('TEM CERTEZA QUE DESEJA DELETAR ESTE ANEXO?')) {
            $.post('actions/apaga.anexo.php',
                    {id: id_arquivo,
                        tipo_anexo: tipo_anexo},
            function(retono) {
                if (retono == '0') {

                    alert('Erro ao deletar anexo.');
                    return false;

                } else {
                    div.prev().fadeOut('slow');
                    div.remove();
                    //window.location.reload();
                }
            }
            );
        }
    });

    $('select[name=estorno]').change(function() {

        if ($(this).attr('checked') == false) {
            $('.descricao_estorno').fadeOut();
            $('.valor_estorno_parcial').fadeOut();

        } else if ($(this).val() == 1) {
            $('.descricao_estorno').fadeIn();
            $('.valor_estorno_parcial').fadeOut();
        } else if ($(this).val() == 2) {
            $('.descricao_estorno').fadeIn();
            $('.valor_estorno_parcial').fadeIn();
        } else if ($(this).val() == '') {
            $('.descricao_estorno').fadeOut();
            $('.valor_estorno_parcial').fadeOut();

        }

    });

    ///////////////////////////
    $('.campo_upload, .campo_upload_2').live('change', function() {

        var aviso = $(this).next();
        var arquivo = $(this);
        var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();

        if (extensao_arquivo != '.pdf' &&
                extensao_arquivo != '' &&
                extensao_arquivo != '.gif' &&
                extensao_arquivo != '.jpg' &&
                extensao_arquivo != '.jpeg' &&
                extensao_arquivo != '.png') {
            arquivo.css('background-color', ' #f96a6a')
                    .css('color', '#FFF');
            aviso.html('Arquivo inválido.');

        } else {
            arquivo.css('background-color', '#51b566')
                    .css('color', '#FFF');
            aviso.html('');
        }
    });


    $('.add_anexo').click(function() {
        var campo = "<div><input type=\"file\"  name=\"anexo_upload[]\" class=\"campo_upload\"/> <span class=\"aviso\"></span><a href=\"#\" onclick=\"$(this).parent().remove(); return false;\"> Excluir</a>  </div>";
        $("#inputs_1").append(campo);
        return false;
    });


    $('.add_anexo2').click(function() {
        var campo = "<div><input type=\"file\"  name=\"anexo_upload2[]\" class=\"campo_upload_2\"/> <span class=\"aviso\"></span><a href=\"#\" onclick=\"$(this).parent().remove(); return false;\"> Excluir</a>  </div>";
        $("#inputs_2").append(campo);
        return false;
    });


    $('.ver').click(function() {

        var tipo = parseInt($(this).attr('rel'));
        var elemento = $(this);

        switch (tipo) {
            case 1:
                $('.exibe_formulario').show();
                $('.exibe_anexo').hide();
                $('.exibe_comprovante').hide();
                $('.ver').removeClass('selecionado');
                elemento.addClass('selecionado');
                break;
            case 2:
                $('.exibe_formulario').hide();
                $('.exibe_anexo').show();
                $('.exibe_comprovante').hide();
                $('.ver').removeClass('selecionado');
                elemento.addClass('selecionado');
                break;
            case 3:
                $('.exibe_formulario').hide();
                $('.exibe_anexo').hide();
                $('.exibe_comprovante').show();
                $('.ver').removeClass('selecionado');
                elemento.addClass('selecionado');
                break;
        }
        return false;
    });

//    $('input[type=submit]').click(function() {
//        $(this).hide();
//        $('.aguarde').show();
//    });


    if($("#prestador_id").val() != ''){
        var status_prestador = $("#status_prestador").val();
        if(status_prestador == "ativo"){
            $("#Projeto-prestador").change();
            $("#ativo").attr("checked",true);
            $(".fornecedor").hide();
            $(".prestador").show();
        }else{
            $("#Projeto-prestador").change();
            $("#inativo").attr("checked",true);
            $(".prestador").hide();
            $(".fornecedor").show();
        }    
    }

});