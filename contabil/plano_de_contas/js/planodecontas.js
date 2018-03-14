$(document).ready(function () {
     
    $(".valor").maskMoney({ allowNegative: true, thousands:'.', decimal:',' });
    
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    $('#regiao1,#regiao2,#regiao3,#regiao4').change(function () {
        var destino = $(this).data('for');
        $.post("../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $('#contbprojeto1,#contbprojeto2,#contbprojeto3,#contbprojeto3').change(function () {
        var destino = $(this).data('for');
        $.post("../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $("input[name='classificador']").mask('?9.99.99.99.99.99.99');
    $("input[name='conta_pai']").mask('?9.99.99.99.99.99.99');
    
    
    $("#ContaReferencial").keyup(function () {
        var contaReferencial = $('#ContaReferencial').val();
        $(this).after('<i class="fa fa-spinner fa-spin form-control-feedback" id="loading"></i>');
        $.post('planodecontas_controle.php', {method: 'classificador', contaReferencial: contaReferencial}, function (data) {
            $("#loading").remove();
            $("#contaReferencial").autocomplete({
                source: data.conta,
                minLength: 3,
                change: function (event, ui) {
                    if (event.type == 'autocompletechange') {
                        var array_item = $("#contaReferencial").val().split(' - ');
                        $("#contaReferencial").val(array_item[0]);
                        $("#texto_descricao").val(array_item[1]);
                    }
                }
            });
        }, 'json');
    });

    

    $("input[name='classificador']").keyup(function () {
        var classificador = $("input[name='classificador']");
        var descricao = $("input[name='descricao]'");
        var conta = $(this).val().replace(/\.__/g, '');
        var conta_pai = conta.substring(0, conta.length -3);
        var referencial = conta;
        $("#conta_pai").val(conta_pai);
        $("#conta_referencia").val(referencial);
            console.log(referencial);        
        $.post("planodecontas_controle.php", {method: "classificador", classificador: $(this).val()}, function (json) { 
            var html = "";            
            if (json.table != null) {
                $.each(json.table, function (key, value) {
                    var natureza = value.natureza;
                    var tipo = value.classificacao;
                    var projeto = value.id_projeto;
                    var font = "";

                    if(natureza == 2) {
                        natureza = "CREDORA";
                    } else if(natureza == 1) {
                        natureza = "DEVEDORA";
                    } else {
                        natureza = "";
                    }
                    if(tipo == "A"){
                        tipo = "ANALÍTICA";
                    } else if (tipo == "S") {
                        tipo = "SINTÉTICA";
                    } else { 
                        tipo = "";
                    }
                    if(projeto == 0 && tipo == "SINTÉTICA" ){
                        projeto = "SPED";
                        font = "text-default text-bold";
                    } else if (projeto == 0 && tipo == "ANALÍTICA" ){
                        projeto = "SPED";
                        font = "text-warning text-ms";
                    } else { 
                        font = "";
                    }
                    html += '<tr class="'+font+'">\n\
                        <td>' + value.classificador + '</td>\n\
                        <td class="text text-uppercase">' + value.descricao + '</td>\n\
                        <td>' + tipo + '</td>\n\
                        <td>' + projeto + '</td>\n\
                    </tr>';
                   
                });
                $("#ico_search").removeClass('fa-check text-success').addClass('fa-search'); 
                
            } else {
                html = '';
                $("#ico_search").removeClass('fa-search').addClass('fa-check text-success'); 
            }
            $("#planodecontas tbody").html(html);
        }, "json");
    });
    
    $('#form_nova_conta').ajaxForm({
        dataType:'json',
        clearForm: true,
        beforeSubmit: function(){
            return $("#form_nova_conta").validationEngine('validate');
        },
        success: function(data){
            var status = (data.status == true)?'success':'danger';
            bootAlert(data.msg,'Salvando',null,status);
        }
    });
    
    $('#form_planocontas_empresa').ajaxForm({
        success: function(data){
            $('#empreascontas').html(data);
            $("[data-toggle='tooltip']").tooltip(); 
        }
    });
    
    $('body').on('click', '#edita_conta', function(){
        var titulo = 'PLANO DE CONTA - EDIÇÃO'
        var id_conta = $(this).data('id');
        var id_projeto = $(this).data('projeto');
        var buttons = [{
            label: 'Confirmar',
            cssClass: 'btn-success',
            action: function (dialog) {
                $.post('planodecontas_controle.php', {method: 'alterar_contas', edita_id_conta: $('#edita_id_conta').val(), edita_classificador: $('#edita_classificador').val(), edita_pai: $('#edita_pai').val(), edita_reduzido: $('#edita_reduzido').val(), edita_nivel: $('#edita_nivel').val(), edita_descricao: $('#edita_descricao').val(), edita_natureza: $('#edita_natureza').val(), edita_tipo: $('#edita_tipo').val(), id_historico:$('#id_historico2').val()}, function (data) {
                    if (data == true) {
                        bootAlert('ALTERAÇÃO DA CONTA REALIZADA... ', titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });
                        }, 'success');
                        $('#empresa_planoconta').trigger('click');
                    } else {
                        bootAlert(data, titulo, null, 'danger');
                    }
                }, 'json');
            }
        }, {
            label: 'Cancelar',
            action: function (dialog) {
                dialog.close();
            }
        }];
        $.post("planodecontas_controle.php", {editar_conta: "editar_conta", id_conta: id_conta, id_projeto: id_projeto }, function (resultado) { 
            bootDialog(resultado, titulo, buttons, 'success');
            
        });       
    });

    $('body').on('click', '#cancela_conta', function(){
        var titulo = 'PLANO DE CONTA - CANCELAMENTO DA CONTA'
        var conta = $(this).data('cancelar_id');
        var classificador = $(this).data('classificador');
        var descricao = $(this).data('descricao');
        var buttons = [{
            label: 'Confirmar',
            cssClass: 'btn-danger',
            action: function (dialog) {
                $.post('planodecontas_controle.php', {method: 'cancelar_conta', conta: conta}, function (data) {
                    if (data == true) {
                        bootAlert('CONTA CANCELADA.', titulo, function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });
                        }, 'success');
                        $('#empresa_planoconta').trigger('click');
                    } else {
                        bootAlert('Erro ao Cancelar Conta', titulo, null, 'danger');
                    }
                }, 'json');
            }
        }, {
            label: 'Cancelar',
            action: function (dialog) {
                dialog.close();
            }
        }];
    
        var motivo = '<p><label>Deseja cancelar a Conta '+classificador+' - '+descricao+' ?</label></p>\n\
                    <textarea name="motivo" id="motivo" class="form-control" placeholder="Informe o motivo!" autofocus></textarea>';
        bootDialog(motivo, titulo, buttons, 'danger');
    });

    $('body').on('click', '.nivel', function(){
        var k1 = $(this).data('k1').toString();
//        var k2 = $(this).data('k2').toString();
//        var k3 = $(this).data('k3').toString();
//        var k4 = $(this).data('k4').toString();
//        var k5 = $(this).data('k5').toString();
//        var k6 = $(this).data('k6').toString();
//        var n = $(this).data('n').toString();
        $('.n'+k1).toggle();
    });
    
//    $("#novaconta").on('click', '.novaconta', function () {
//        console.log($(this).data('id'));
//        var titulo = 'CONTA CLASSIFICAÇÃO CONTABIL';
//        var id = $(this).data('id');
//        var classificador = $(this).data('classificador');
//        var tipo = $(this).data('tipo');
//        var descricao = $(this).data('descricao');
//        var natureza = $(this).data('natureza');
//        $.post('planodecontas_controle.php', {method: 'Salvar', id: id, classificador: classificador, tipo: tipo, decricao: descricao, natureza: natureza}, function (data) {
//            if (data.status) {
//                var html = '<p>Conta salva no Plano de Contas!</p><p><a href="' + data.nomeFile + '" target="_blank" class="btn btn-success"><i class="fa fa-plus"></i> Salvar</a></p>';
//                bootAlert(html, titulo, function () {
//                    $.each(BootstrapDialog.dialogs, function (id, dialog) {
//                        dialog.close();
//                    });
//                }, 'success');
////                $('#tr-' + id).remove();
//
//            } else {
//                bootAlert('Erro ao confirmar o conta.', titulo, null, 'danger');
//            }
//        }, 'json');
//    });
//    
//    

$('body').on('click','#print',function(){
    var proj  = $("#projeto").val();
    window.location.href = 'planodecontas_controle.php?projeto='+proj+"&filtrar=Imprimir";
});

 $('body').on('click', '.vincular', function () {
           var RecebeIdPlanoConta= camposMarcados = new Array();
            $("input[type=checkbox][name='IdPlano[]']:checked").each(function(){
    camposMarcados.push($(this).val());
            });
            //alert(RecebeIdPlanoConta); return false;
            if(RecebeIdPlanoConta==''){
                alert('Por favor selecione uma conta');
                return false; 
            }
            $('#IdPlanoConta').val(RecebeIdPlanoConta);
    });
     $('body').on('click', '.listaSpeed', function () {
         //alert('opa');return false;
          var recebeIdReferencia= ($(this).data('id'));
          var Id_PlanoConta= $('#IdPlanoConta').val();
          if(recebeIdReferencia != '1' && recebeIdReferencia != '2' && recebeIdReferencia != '3' && recebeIdReferencia != '4' && recebeIdReferencia != '5' ){
                $.post('planodecontas_controle.php', {method: 'referencia_conta', idContaPlano:Id_PlanoConta, classifica:recebeIdReferencia}, function (dados) {
                    alert(dados);return false;
                    if(dados== 1){
                        
                        alert('Referência realizada com Sucesso!');
                         window.location.href = 'ContaReferencia.php';
                        
                    }
                });
           }    
     })


});
