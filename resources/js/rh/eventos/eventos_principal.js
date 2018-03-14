$(document).ready(function () {
    $("#form1").validationEngine(); // add validation engine

    $(".page").click(function () { // quando clica na as setas de paginação
        var pagina = $(this).data("page");
        $("#paginacao").val(pagina);
        $("#form1").submit();
    });
    $(".inicial").click(function () { // quando clica no botão das iniciais
        var letra = $(this).html();
        $("#inicial").val(letra);
        $("#paginacao").val(1);
        $("#form1").submit();
    });

    $(".tab-status").click(function () { // abas
        var status = $(this).data("status");
        $("#rhstatus").val(status);
        $("#form1").submit();
    });

    $("#tudo").click(function () { // limpa a pesquisa por nome e recarrega com todos os dados
        $("#clt_nome").val("");
        $("#form1").submit();
    });
    $("#filtrar").click(function () { // limpa o input com a inicial para não dar erro na query
        $("#inicial").val('');
        $("#form1").submit();
    });

    $(".link_evento").click(function () {
        $("#clt").val($(this).data('clt'));
        $("#regiao").val($(this).data('regiao'));
        $("#method").val("acao_evento");
        $("#form1").attr("action", "index2.php");
        $("#form1").submit();
    });
});