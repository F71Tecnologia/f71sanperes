<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Financeiro</title>
        <script src="../resources/js/dropzone.js"></script>
        <script>
            $(function() {

                $('#mydropzone').dropzone({
                    url: "/upload",
                    maxFilesize: 20,
                    paramName: "uploadfile",
                    maxThumbnailFilesize: 5,
                    init: function() {

                        this.on('success', function(file, json) {
                        });

                        this.on('addedfile', function(file) {

                        });

                        this.on('drop', function(file) {
                            alert('file');
                        });
                    }
                });
            });

            $(document).ready(function() {});
            });
        </script>
        <style>
            @import url('//cdnjs.cloudflare.com/ajax/libs/dropzone/3.8.4/css/dropzone.css');
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3>DropzoneJS Upload Example - http://www.dropzonejs.com/</h3>  
                </div>
            </div><!--/row-->
            <hr>
            <div>         
                <form action="upload.php" class="dropzone dz-clickable" id="mydropzone"><div class="dz-default dz-message"><span>Drop files here to upload</span></div></form>                
            </div>
        </div>
    </body>
</html>

