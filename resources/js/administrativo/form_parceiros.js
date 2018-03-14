$(function() {
    $('#form_parceiros').validationEngine();
    
    $("#parceiro_cpf").mask("999.999.999-99");
    $("#parceiro_telefone").mask("(99) 99999-9999?");
    $("#parceiro_celular").mask("(99) 99999-9999?");
    $("#parceiro_cnpj").mask("99.999.999/9999-99");

    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone("#anexo_logo",{
        url: "../actions/action_parceiros.php",
        addRemoveLinks : true,
        maxFilesize: 10,
        maxFiles: 1,
        autoQueue: false,

        dictResponseError: "Erro no servidor!",
        dictCancelUpload: "Cancelar",
        dictFileTooBig: "Tamanho máximo: 10MB",
        dictRemoveFile: "Remover Arquivo",
        canceled: "Arquivo Cancelado",
        acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
//        , success: function(file, responseText){
//            console.log(responseText);
//            //$('.close').trigger('click');
//        }
    });

    $("body").on('click', ".botaoSubmit", function(){
        
        if($("#form_parceiros").validationEngine('validate')){
        
            var dados = $('#form_parceiros').serialize();
    //        cria_carregando_modal();
            $.post("../actions/action_parceiros.php", dados, function(resposta){
                console.log(resposta);
                console.log(resposta.status);
                console.log(resposta.id_parceiro);
                if(resposta.status == "1"){
                    myDropzone.on('sending',function(file, xhr, formData) {
                        formData.append("id_parceiro", resposta.id_parceiro); // Append all the additional input data of your form here!
                        formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
                    });

                    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));

                    remove_carregando_modal();

                    bootDialog(
                        'Dados Salvos Com Sucesso!', 
                        'Parceiro', 
                        [{
                            label: 'Fechar',
                            action: function(){ window.location.href = "../parceiros"; }
                        }], 
                        'success'
                    );
                }else{
                    remove_carregando_modal();

                    bootDialog(
                        'Erro ao salvar os dados! '+resposta.msg, 
                        'Parceiro', 
                        [{
                            label: 'Fechar'
                        }], 
                        'danger'
                    );
                }
            },'json');
        }
        
    });
    
    $("body").on('click', "#remover_logo", function(){
        var id_parceiro = $(this).data('key');
        console.log(id_parceiro);
        cria_carregando_modal();
        $.post("../actions/action_parceiros.php", {bugger:Math.random(), action:'remover_logo', id_parceiro:id_parceiro}, function(resposta){
            console.log(resposta);
            remove_carregando_modal();
            bootDialog(
                'Logo Removido!', 
                'Remoção de Logo', 
                [{
                    label: 'Fechar',
                    action: function(){ window.location.href = "../parceiros"; }
                }], 
                'success'
            );
    
        });
    });
});