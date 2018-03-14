$(document).ready(function () {
    //datepicker
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    $("#relatorio").click(function (){
        window.location.href = "finan_plano_contas_rel.php";
    });

//    $("#form_exportar").ajaxForm({
//        beforeSubmit: function () {
//            $("#resposta").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
//        },
//        clearForm:true,
//        success: function (data) {
//            $("#resposta").html(data);
//            $('input[type=text]').val('');
//            $('select').val(-1);
//        }
//    });
    
    $("#form_associacao_receita").ajaxForm({
        beforeSubmit: function () {
            $("#resposta").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        clearForm:true,
        success: function (data) {
            $("#resposta").html(data);
        }
    });
    
    $("#form_associacao_despesa").ajaxForm({
        beforeSubmit: function () {
            $("#resposta").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        clearForm:true,
        success: function (data) {
            $("#resposta").html(data);
        }
    });
    
    $("#form_associacao_empresa").ajaxForm({
        beforeSubmit: function () {
            $("#resposta").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        clearForm:true,
        success: function (data) {
            $("#resposta").html(data);
        }
    });
    
    $("#form_associacao_folha").ajaxForm({
        beforeSubmit: function () {
            $("#resposta").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        clearForm:true,
        success: function (data) {
            $("#resposta").html(data);
        }
    });
    
    $("#empresa, #empres1").change(function () {
        var destino = $(this).data('for');
        $.post("associacaocontas.php", {method: "carregaEmpresas", empresa: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $("#regiao1,#prosoft_regiao,#prosoft_regiao1,#prosoft_regia1,#folha_regiao,#prosoft_regiao2,#prosoft_regiao3,#prosoft_regiao4,#regiao5").change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $("#prosoft_projeto,#prosoft_projet1,#prosoft_projeto2,#folha_projeto,#prosoft_projeto4,#prosoft_relatorio").change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $("#provisao_folha").click(function(){
        var buttons=[{
                label: 'Confirmar',
                cssClass: 'btn-default',
                action: function(dialogRef){
                    dialogRef.SetClosable(true);
                }
            }, {
            label: 'Cancelar',
            cssClass: 'btn-default',
            action: function(dialogRef){
                dialogRef.setClosable(false);
            }
        }, {
            label: 'Sair',
            action: function(dialogRef){
                dialogRef.close();
            }
        }];
        //bootDialog($('#provisaofolha').html(), "Provisionar Folha de Pagamento", buttons, "success"); 
        
        
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
    
    $("#dtainicio").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $("#dtafim").datepicker("option", "minDate", selectedDate);
        }
    });

    $("#dtafim").datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $("#dtainicio").datepicker("option", "maxDate", selectedDate);
        }
    });
 
});