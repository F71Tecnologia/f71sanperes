$(function(){
    $('.clk').click(function(){
        var key = $(this).data("key");
        $("." + key).toggle();
    });
    
    $(".arq").click(function(){
        var id = $(this).data("key");
        BootstrapDialog.show({
            title: 'Saída '+id,
            message: $('<div></div>').load('saida_detalhado.php?id='+id+'&darf=1')
        });
    });
    
    $("#vinc").click(function(){
        var id_pai = $("#id_saida_pai").val();
        var tipo_darf = $("#tp_darf").val();
        var id_saida = $(this).data("saida");        
        
        $.ajax({
            url:"vinculo_darf.php",
            type:"POST",
            dataType:"json",
            data:{
                id_pai:id_pai,
                tipo_darf:tipo_darf,
                id_saida:id_saida,
                method:"vincular"
            },
            success:function(data){
                if(data.status){
                    $(".occ").show();
                    $(".darf").hide();
                }
            }
        });        
    });
});