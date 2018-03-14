$(document).ready(function () {
    //datepicker
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    // carregando projetos -----------------------------------------------------
    $('#regiao1,#regiao2,#regiao3,#regiao4').change(function () {
        var destino = $(this).data('for');
        $.post("../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });
    $('#projeto1,#projeto2,#projeto3,#projeto3').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

});