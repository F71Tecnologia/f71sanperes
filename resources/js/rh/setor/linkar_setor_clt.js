$(function() {
    
    $('#form').validationEngine();
    
    $('body').on('change', '#projeto, #curso', function(){
        cria_carregando_modal();
        $.post("action/action_linkar_setor_clt.php", { bugger:Math.random(), action:'get_clt', projeto:$('#projeto').val(), curso:$('#curso').val()}, function(r1){
            $("#clts").html(r1);
            remove_carregando_modal();
        });
        $.post("action/action_linkar_setor_clt.php", { bugger:Math.random(), action:'get_curso', projeto:$('#projeto').val(), curso:$('#curso').val()}, function(r2){
            $("#curso").html(r2);
        });
    });
    
});