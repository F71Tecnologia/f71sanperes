$(document).ready(function(){
    $('#bt-enviar').click(function(){
        console.log("oi");
        $('#formUpload').ajaxForm({
            uploadProgress: function(event, position, total, percentComplete) {
                $('progress').attr('value',percentComplete);
                $('#porcentagem').html(percentComplete+'%');
            },        
            success: function(data) {
                $('progress').attr('value','100');
                $('#porcentagem').html('100%');                
                if(data.sucesso == true){
                    $('#resposta').html('<img src="'+ data.msg +'" />');
                }
                else{
                    $('#resposta').html(data.msg);
                }                
            },
            error : function(){
                $('#resposta').html('Erro ao enviar requisição!!!');
            },
            dataType: 'json',
            url: '../adm/adm_prestador/actions.php?method=uploadDoc',
            resetForm: true
        }).submit();
    })
})