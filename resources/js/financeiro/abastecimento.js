$(function() {
    
    $("#anual").click(function(){
        if($(this).is(":checked")){
            $(".datas").hide();
            $("#data_ini").val('');
            $("#data_fim").val('');
        }else{
            $(".datas").show();
        }
    });
    
    if($("#anual").is(":checked")){
        $(".datas").hide();
        $("#data_ini").val('');
        $("#data_fim").val('');
    }else{
        $(".datas").show();
    }
    
});