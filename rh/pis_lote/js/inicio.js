$(document).ready(function () {
    $("#form-atualizar").ajaxForm({
        beforeSubmit: function () {
            $("#resp-autalizar").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            $("#resp-autalizar").html(data);
        }
    });
    $("body").on('click','.ver_info_erro',function(){
        var lista = $(this).data('erros');
        $.post(window.location.href,{method:'carrega_erros',lista_erros:lista},function(data){
            var html = '<table class="table table-bordered">';
            html += '<thead><tr>';
            html += '<th>#</th>';
            html += '<th>Gravidade</th>';
            html += '<th>Descrição</th>';
            html += '<th>Ação</th>';
            html += '<th>Registro / Posição / Campo</th>';
            html += '</tr></thead><tbody>';
            $.each(data.erros, function(key, value){
                html+='<tr>';
               html += '<td>' + value['id'] + '</d>';
               html += '<td>' + value['gravidade'] + '</d>';
               html += '<td>' + value['descricao'] + '</d>';
               html += '<td>' + value['acao'] + '</d>';
               html += '<td>' + value['registro_posicao_campo'] + '</d>';
               html += '</tr>';
            });
            html +='</tbody></table>';
            bootAlert(html,'Mensagens de Erro',null,'warning');
        },'json');
        
    });
});


