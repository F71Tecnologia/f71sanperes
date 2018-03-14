$(document).ready(function () {

    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    $("input[name='classificador']").mask('?9.99.99.99.99.99.99');

    $("input[name='classificador']").keyup(function () {
        var classificador = $("input[name='classificador']");
        var descricao = $("input[name='descricao]'");
        $.post("planodecontas_controle.php", {method: "classificador", classificador: $(this).val()}, function (json) { 
            var html = "";
            if (json != null) {
                $.each(json, function (key, value) {
                    var natureza = value.natureza;
                    var tipo = value.tipo;

                    if(natureza == "C") {
                        natureza = "CREDORA"
                    } else if(natureza == "D") {
                        natureza = "DEVEDORA"
                    } else {
                        natureza = "";
                    };
                    if(tipo == "A"){
                        tipo = "ANALÍTICA"
                    } else if (tipo == "S") {
                        tipo = "SINTÉTICA"
                    } else { tipo = "";
                    }
                    html += '<tr>\n\
                        <td>' + value.classificador + '</td>\n\
                        <td class="text text-uppercase">' + value.descricao + '</td>\n\
                        <td>' + tipo + '</td>\n\
                    </tr>';
                });
                $("#ico_search").removeClass('fa-check text-success').addClass('fa-search'); 
            } else {
                html = '';
                $("#ico_search").removeClass('fa-search').addClass('fa-check text-success'); 
            }
            $("#planodecontas tbody").html(html);
        }, "json");
    });
    
    $("body").on('click', '#exibe-contas', function(){
        $.post("planodecontas_controle.php", {method: "classificadores_projeto", id_projeto: $('#id_projeto').val()}, function (resultado) { 
            $("#lista-planos").html(resultado);
            $(".div-lista-planos").removeClass('hidden');
            $(".div-lista-filtro").addClass('hidden');
        });
    });
   
    $("body").on('click', '#exibe-contas-editavel', function(){
        $.post("planodecontas_controle.php", $('#form-lista-planos').serialize(), function (resultado) { 
            $("#lista-planos-editavel").html(resultado);
            $(".div-lista-planos-editavel").removeClass('hidden');
            $(".div-lista-planos").addClass('hidden');
        });
    });
    
    $("body").on('click', '#salvar-plano-contas', function(){
        if($('#form-lista-planos-editavel #id_projeto').val() > 0){
            $.post("planodecontas_controle.php", $('#form-lista-planos-editavel').serialize(), function (resultado) { 
                if(resultado == 'sucesso'){
                    bootAlert("Contas cadastradas com sucesso","",function(){location.reload();},"success");
                } else {
                    bootAlert("Contas com erro ou já cadastrada:<br>"+resultado,"Erro",null,"danger");
                }
            });
        }
    });
    
    $("body").on('click', '.back', function(){
        $(".div-lista-planos-editavel, .div-lista-planos, .div-lista-filtro").addClass('hidden');
        $("."+$(this).data('show')).removeClass('hidden');
    });
});