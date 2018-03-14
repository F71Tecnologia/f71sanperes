<html>
    <head>
        <link href="css/dropzone.css" type="text/css" rel="stylesheet" />
        <script src="jquery-1.8.3.min.js"></script>
        <script src="dropzone.min.js"></script>
        <script>
            $(document).ready(function(){
                
                var id_entrada = "2224";
                var number = 0;
                
                Dropzone.autoDiscover = false;
                
                $("#dropzone").dropzone({
                    url: "upload.php?id_entrada="+id_entrada,
                    addRemoveLinks : true,
                    maxFilesize: 10,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 10MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF',
                    removedfile: function(file, serverFileName){
                        var name = $(".dz-filename span").text();
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "upload.php?delete=true",
                            data: {
                                filename: name
                            },
                            success: function(data){                                
                                if(data.res == true){
                                    var element;
                                    (element = file.previewElement) != null ? element.parentNode.removeChild(file.previewElement) : false;
                                }
                            }
                        });
                    },
                    complete: function(){
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "upload.php",
                            async: true,
                            data:{
                                method: "traz_id"
                            },
                            success: function(data){
                                number++;
                                console.log(number);
                                //console.log(data/*.previewElement*/);
                                $(".dz-filename span").text(data.nome);
                                $(".dz-details img").attr("id",number);
                            }
                        });
                    }
//                    accept: function(){
//                        $.ajax({
//                            type: "POST",
//                            dataType: "json",
//                            url: "upload.php",
//                            async: true,
//                            data:{
//                                method: "traz_id"
//                            },
//                            success: function(data){
//                                number++;
//                                //console.log(data/*.previewElement*/);
//                                console.log(number);
//                                //$(".dz-filename span 2").text(data.aut);
//                                //$(".dz-details img").attr("id",data.nome);
//                            }
//                        });
//                    }
                });
            });                        
        </script>
    </head>
    
    <body>
        <div id="dropzone" class="dropzone">
            <input type="hidden" value="" id="name_arquivo" />
        </div>
    </body>
</html>