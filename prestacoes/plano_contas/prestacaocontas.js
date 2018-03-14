$(document).ready(function () {
    //datepicker
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    // carregando projetos -----------------------------------------------------
    $("#prosoft_regiao,#prosoft_regiao1,#prosoft_regiao2").change(function () {
        console.log('teste');
        var destino = $(this).data('for');
        $.post("http://www.f71lagos.com/intranet/methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $("#prosoft_projeto,#prosoft_projeto1,#prosoft_projeto2,#prosoft_projeto3").change(function () {
        var destino = $(this).data('for');
        $.post("http://www.f71lagos.com/intranet/methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $("#datainicio").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $("#datafim").datepicker("option", "minDate", selectedDate);
        }
    });

    $("#datafim").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $("#datainicio").datepicker("option", "maxDate", selectedDate);
        }
    });
 
});