$(function(){
    $('#formPatrimonio').validationEngine();
    $("#formPatrimonio").ajaxForm({
        beforeSend: function(e) { if(!$('#formPatrimonio').validationEngine('validate')) { return false; } },
        success: function (data) {
            $("#id_patrimonio").val(data);
            $("#salvar").removeClass('btn-primary').addClass('btn-warning').html('<i class="fa fa-save"></i> Editar');
            $('.start').trigger('click');            
            bootAlert('Dados salvos com sucesso!', '', null, 'success');
        }
    });

    $('body').on('click', '.addEditPatrimonio', function(){
//                console.log($(this).data('id'));
        $.post("ctrl/patrimonio.php", {method: "add_edit", id: $(this).data('id')}, function (data) {
            $("#formPatrimonio").html(data);
        });
    });

    $('body').on('click', '.deletaFoto', function(){
        var id_foto = $(this).data('id');    //                console.log($(this).data('id'));
        bootConfirm(
            'Excluir Foto?', 
            'Excluir Foto', 
            function(data){
                if(data){
                    $.post("ctrl/patrimonio.php", {method: "del_foto", id: id_foto}, function (res) {
                        $("#f"+id_foto).remove();
                    });
                }
            }, 
            'warning'
        );
    });

    $('body').on('click', '.deletarPatrimonio', function(){
        var id_patrimonio = $(this).data('id');    //                console.log($(this).data('id'));
        bootConfirm(
            'Excluir Patrimonio?', 
            'Excluir Patrimonio', 
            function(data){
                if(data){
                    $.post("ctrl/patrimonio.php", {method: "del_patrimonio", id: id_patrimonio}, function (res) {
                        $("#p"+id_patrimonio).remove();
                    });
                }
            }, 
            'warning'
        );
    });
});