$(function() {
    
    $(".bt-image").on("click", function() {
        var action = $(this).data("type");
        var key = $(this).data("key");
        var emp = $(this).parents("tr").find("td:first").next().html();
        var clt = $(this).data("clt");

        if(action === "visualizar") {
            $("#banco").val(key);
            $("#form1").attr('action','detalhes_banco.php');
            $("#form1").submit();
        }else if(action === "editar"){
            $("#banco").val(key);
            $("#form1").attr('action','form_banco.php');
            $("#form1").submit();
        }
    });
    
    $("#novoBanco").click(function(){
        $("#form1").attr('action','form_banco.php');
        $("#form1").submit();
    });
    
    $("#editarBanco").click(function(){
        var action = $(this).data("type");
        var key = $(this).data("key");

        if (action === "editar") {
            $("#banco").val(key);
            $("#form1").attr('action','form_banco.php');
            $("#form1").submit();
        }
    });
    
});