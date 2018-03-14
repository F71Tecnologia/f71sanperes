$(document).ready(function () {
    

    $('.ExibirLightBox').hover(function() {
        $(".lightbox").css("display", "block");
    })
    
    $('.money').maskMoney({allowNegative: true, thousands: ',', decimal: '.', affixesStay: false});
    
    $("body").on('click', '.radio', function(){
        $('#titulo').val($(this).data('titulo'));
    });
    
    
        $("body").on('click', '#imprimirPDF', function(){
            console.log('ok até aqui')
        });


    $("body").on('click', '#filtrar', function(){
        var serialize = $('#form_provisao').serialize() + '&method=filtrar';
        if($('#form_provisao').serialize() != '') {
            cria_carregando_modal();
            $.post("", serialize, function (resultado) { 
                $("#lista-folhas").html(resultado);
                $(".div-2").removeClass('hidden');
                $(".div-1").addClass('hidden');
                remove_carregando_modal();
                $('body').animate({
                    scrollTop: $(".panel-default").offset().top
                }, 1000);
            });
        }
    });
    
    $("body").on('click', '#exibe', function(){
        var serialize = $('#form_provisao').serialize() + '&method=lista_clt';
        if($('#form_provisao').serialize() != '') {
            cria_carregando_modal();
            $.post("", serialize, function (resultado) { 
                $("#lista-clts").html(resultado);
                $(".div-3").removeClass('hidden');
                $(".div-2").addClass('hidden');
                remove_carregando_modal();
                $('body').animate({
                    scrollTop: $(".panel-default").offset().top
                }, 1000);
            });
        }
    });
    
    $("body").on('click', '.back', function(){
        $(".div-1, .div-2, .div-3").addClass('hidden');
        $("#"+$(this).data('lista')).html("");
        $("."+$(this).data('show')).removeClass('hidden');
    });
    
    $("body").on('click', '.projeto_provisao_head', function(){
//        $(".projeto_provisao_body").addClass('hidden');
//        $("#"+$(this).data('projeto')).removeClass('hidden');
        $('#'+$(this).data('projeto')).toggle();
    });
    
    $("body").on('click', '#salvar', function(){
        cria_carregando_modal();
        $(this).addClass('hidden');
    });
    
    $("body").on('click', '.detalhar', function(){
        var id_provisao = $(this).data('key');
        $("body").append(
            $("<form>", { action: 'folha_de_provisao_detalhe.php', method: 'post', id: 'detalhar_provisao' }).append(
                $("<input>", { type: 'hidden', name: 'id_provisao', value: id_provisao })
            )
        );
        $('#detalhar_provisao').submit();
    });
    
    $("body").on('click', '.deletar', function(){
        var id_provisao = $(this).data('key');
        bootConfirm('Confirmação de exclusão de provisão!', 'Deletar', function(data){
            if(data) {
                $.post("", {bugger:Math.random(), method:'deletar', id_provisao:id_provisao}, function(resultado){
                    if(resultado){
                        bootAlert('Provisão deletada com sucesso!', 'Exclusão', function () { location.reload();}, 'success' );
                    } else {
                        bootAlert('Erro ao deletar a Provisão!', 'Exclusão', null, 'danger' );
                    }
                });
            }
        }, 'warning');
    });
    
    $("body").on('click', '.ano_folha', function(){
        $('.'+$(this).data('key')).toggle();
    });
});