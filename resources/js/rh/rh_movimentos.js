$(document).ready(function () {
    $("body").on('click', ".lancar-movimentos", function () {
        var id_clt = $(this).data('id-clt');
        $("#id_clt").val(id_clt);
        $("#form1").attr("action", "rh_movimentos_1.php");
        $("#form1").submit();
    });
    
    $('#form1').validationEngine();
    
    $('.cred,.desc').priceFormat({
        prefix: '',
        centsSeparator: ',',
        thousandsSeparator: '.'
    });

    $('.mov').click(function(){
        $('.mov').css('border-color','#E2E2E2');
        $(this).css('border-color','#9bd4f8');
    });

    $('.excluir').click(function(){
        var id_movimento = $(this).attr('rel');
        var linha = $(this).parent().parent();

        if(confirm("Excluir este movimento?")){
            $.post('rh_movimentos_1.php',{ excluir : 1, id_movimento: id_movimento },
            function(data){
                linha.fadeOut();
            });
        }
        return false;
    });

    $('.tipo_qnt').change(function(){

            var elemento = $(this);
            var div =  elemento.parent().parent().find('.calculo');
            if(elemento.val() == 1){
                div.mask('99:99')
                div.val('');
            }else if(elemento.val() == 2){
                div.unmask('99:99')
                div.val('');
            }
        });

    $(".calculo").change(function(){
            var quant          = $(this).val();
            var elemento       = $(this).parent().parent();
            var key            = $(this).data("key");
            var tipo_contagem  = elemento.find('.tipo_qnt').val();
            var id_clt = $('#clt').val();
            
            $.post('action_calcula_movimento.php', {id_clt: id_clt, id_mov :key, tipo_qnt: tipo_contagem, qnt: quant  }, function(data){
                $(".result_" + key).val(parseFloat(data).formatMoney("2",",","."));
            });
       });

    function auxDistancia(fiel, rules, i, options){
        var salbase = $("#salario_base").val();
        var auxDistancia = fiel.val();
        var minAuxilio = salbase * 0.25;
        minAuxilio = minAuxilio.toFixed(2);
        auxDistancia = auxDistancia.replace(".", "");
        auxDistancia = auxDistancia.replace(",", ".");
        if(parseFloat(auxDistancia) < parseFloat(minAuxilio)){
            return options.allrules.auxDistancia.alertText;
        }
    }
            
});