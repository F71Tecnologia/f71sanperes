$(function(){
    $('.clk').click(function(){
        var key = $(this).data("key");
        $("." + key).toggle();
    });
    
    $(".arq").click(function(){
        var id = $(this).data("key");   
    
        $.ajax({
            type: 'POST',
            url: "saida_detalhado.php",
            data: { id:id },
            async:false
        }).done(function(resultado) {
            bootAlert(resultado, 'Saída '+id, null, 'primary');
        });
    });
});