$(document).ready(function () {
    $(".menu-left").click(function () {
        var id_projeto = $(this).data('id-projeto');
        var ano = $(".active a.ano").html();
//        alert("ID projeto: " + id_projeto + "\nANO: " + ano);
        getListaContra(id_projeto, ano);
        $(".menu-left").parent().removeClass("active");
        $(this).parent().addClass("active");
    });
    $(".ano").click(function () {
        var id_projeto = $(".active a.menu-left").data('id-projeto');
        var ano = $(this).html();
//        alert("ID projeto: " + id_projeto + "\nANO: " + ano);
        getListaContra(id_projeto, ano);
        $(".ano").parent().removeClass("active");
        $(this).parent().addClass("active");
    });

    $('.active .menu-left').trigger("click");
    
    $("body").on("click",".individual",function(){
        $("#id_folha").val($(this).data("folha")); // insere o valor do data-folha no input folha
        $("#form-oculto").attr("action","listaIndividual.php");
        $("#form-oculto").submit();
    });
    $("body").on("click",".todos",function(){
        $("#id_folha").val($(this).data("folha")); // insere o valor do data-folha no input folha
        $("#form-oculto").attr("action","listaTodos.php");
        $("#form-oculto").submit();
    });
});

function getListaContra(id_projeto, ano) {
    $.ajax({
        url: "listaContra.php",
        data: {id_projeto: id_projeto, ano: ano},
        success: function (data) {
            console.log(data);
            $("#listaContra").html(data);
        }, 
        beforeSend: function(){
            $("#listaContra").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
        dataType: "html"
    });
}