<?php
session_start();
include("../conn.php");
include("../classes/SaidaClass.php");
include("../wfunction.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">                
    </head>
    <body>
        <form action="" method="post" class="form-horizontal top-margin1" name="form2" id="form2">
            
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group">
                            <label for="mensagem" class="col-lg-3 control-label">Razão Social</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control validate[required]" id="c_razao" name="c_razao" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="mensagem" class="col-lg-3 control-label">CNPJ</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control validate[required]" id="cnpj" name="cnpj">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="mensagem" class="col-lg-3 control-label">Descrição</label>
                            <div class="col-lg-8">
                                <textarea class="form-control validate[required]" id="especifica" name="especifica" cols="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="button" id="cad_nome" value="Cadastrar" class="btn btn-primary" />
                    <input type="hidden" name="cad_prestador" value="Cadastrar" />
                </div>
            </div>
        </form>
        <script>
            $(function(){
                
                $("#cnpj").mask("99.999.999/9999-99");
                
               $("#form2").validationEngine({
                   promptPosition : "topRight"
               });
               
               $('#form2').on('click', '#cad_nome',function(){
                   var dados = $('#form2').serialize();
                   $.post("form_saida.php", dados, function(resultado){
                       console.log(resultado);
                       $('#projeto_prestador').trigger('change');
                       $('.modal').modal('hide');
                       $('#nome').val(resultado);
                   });
               });
            });
        </script>
    </body>
</html>