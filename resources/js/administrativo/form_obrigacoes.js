$(function() {
    $('#form_obrigacoes').validationEngine();
    var contupload  = 0;
    var validador1 = false;var validador2 = false;
    Dropzone.autoDiscover = false;
    var myDropzonePublicacao = new Dropzone("#anexo_publicacao",{
        url: "../actions/action_obrigacoes.php",
        addRemoveLinks : true,
        maxFilesize: 10,

        autoQueue: false,

        dictResponseError: "Erro no servidor!",
        dictCancelUpload: "Cancelar",
        dictFileTooBig: "Tamanho máximo: 10MB",
        dictRemoveFile: "Remover Arquivo",
        canceled: "Arquivo Cancelado",
        acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
        , init: function () {
            var totalFiles = 0,
                completeFiles = 0;
            this.on("addedfile", function (file) {
                totalFiles += 1;
                validador1 = "add";
            });
            this.on("removed file", function (file) {
                totalFiles -= 1;
            });
            this.on("complete", function (file) {
                completeFiles += 1;
                
                if (completeFiles === totalFiles) {
                    validador1 = true;
                    verificaUpload();
                }
            });
        }
    });

    var myDropzoneDocumento = new Dropzone("#anexo_documento",{
        url: "../actions/action_obrigacoes.php",
        addRemoveLinks : true,
        maxFilesize: 10,

        autoQueue: false,

        dictResponseError: "Erro no servidor!",
        dictCancelUpload: "Cancelar",
        dictFileTooBig: "Tamanho máximo: 10MB",
        dictRemoveFile: "Remover Arquivo",
        canceled: "Arquivo Cancelado",
        acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
        ,  init: function () {
            var totalFiles2 = 0,
                completeFiles2 = 0;
            this.on("addedfile", function (file) {
                totalFiles2 += 1;
                validador2 = "add";
            });
            this.on("removed file", function (file) {
                totalFiles2 -= 1;
            });
            this.on("complete", function (file) {
                completeFiles2 += 1;
                if (completeFiles2 === totalFiles2) {
                    validador2 = true;
                    verificaUpload();
                }
            });
        }
    });

    $("body").on('click', ".botaoSubmit", function(){
        var dados = $('#form_obrigacoes').serialize();
        cria_carregando_modal();
        $.post("../actions/action_obrigacoes.php", dados, function(resposta){
            console.log(resposta);
            myDropzonePublicacao.on('sending',function(file, xhr, formData) {
                formData.append("tipo", 1); // Append all the additional input data of your form here!
                formData.append("id_obrigacao", resposta); // Append all the additional input data of your form here!
                formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
            });

            myDropzonePublicacao.enqueueFiles(myDropzonePublicacao.getFilesWithStatus(Dropzone.ADDED));

            myDropzoneDocumento.on('sending',function(file, xhr, formData) {
                formData.append("tipo", 2); // Append all the additional input data of your form here!
                formData.append("id_obrigacao", resposta); // Append all the additional input data of your form here!
                formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
            });

            myDropzoneDocumento.enqueueFiles(myDropzoneDocumento.getFilesWithStatus(Dropzone.ADDED));

            if(validador1 == false && validador2 == false)
            {
                contupload = 1;
                verificaUpload();
            }
            else
            {
                if(validador1 != "add" || validador2 != "add")
                {
                    verificaUpload();
                }
                
            }
            
        });
    });
    
    function verificaUpload()
    {
        contupload++;
        if(contupload == "2")
        {
            remove_carregando_modal();

            bootDialog(
                'Obrigação Cadastrada Com Sucesso!', 
                'Obrigação Cadastrada!', 
                [{
                    label: 'Fechar',
                    action: function(){
                        window.location.href = "../obrigacoes";
                    }
                }], 
                'success'
            );
        }
    }
    
    $("body").on('change', "#periodo", function(){
        $("#oscip_data_inicio, #oscip_data_termino, #numero_periodo").val("");
        $("#div_dias, #div_periodo, .validade").hide();
        $("#oscip_data_inicio, #oscip_data_termino, #numero_periodo").removeClass("validate[required]");
        if($(this).val() == "Período"){ 
            $("#div_periodo, .validade").show();
            $("#oscip_data_inicio, #oscip_data_termino").addClass("validate[required]");
        } else if($(this).val() == "Dias" || $(this).val() == "Meses" || $(this).val() == "Anos"){
            $("#div_dias, .validade").show();
            $("#numero_periodo").addClass("validate[required]").prop("placeholder","Digite o Número de "+$(this).val());
        }
    });
    
    $("body").on('change', "#tipo_oscip", function(){
        var tipo = $(this).val();
        $("#resp_env_rec, #oscip_endereco").val("");
        $("#div_resp_env_rec, #div_oscip_endereco").hide();
        if(tipo == 14){
            cria_carregando_modal();
            $.post("../actions/action_obrigacoes.php", {action:'get_resposta', id:'Ofícios Recebidos'}, function(resposta){
                $("#resp_env_rec").html(resposta);
                $("#div_resp_env_rec").show();
                remove_carregando_modal();
            });
        } else if(tipo == 13){
            cria_carregando_modal();
            $.post("../actions/action_obrigacoes.php", {action:'get_resposta', id:'Ofícios Enviados'}, function(resposta){
                $("#resp_env_rec").html(resposta);
                $("#div_resp_env_rec").show();
                remove_carregando_modal();
            });
        } else if(tipo == 12){
            $("#div_oscip_endereco").show();
        }
    });
    //$("#tipo_oscip, #periodo").trigger('change');
});