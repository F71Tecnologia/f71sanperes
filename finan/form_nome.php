<?php
session_start();
include("../conn.php");
include("../classes/SaidaClass.php");
include("../wfunction.php");

if($_REQUEST['method'] == 'verificaNome') {
//    $value = str_replace(['.', '-', '/'], '', $_REQUEST['value']);
    $value = str_replace(['.', '-', '/'], '', $_REQUEST['value']);
    $sql = "SELECT * FROM entradaesaida_nomes WHERE cpfcnpj = '{$_REQUEST['value']}' OR cpfcnpj = '{$value}'";
    $qry = mysql_query($sql);
    $array['status'] = mysql_num_rows($qry);
    echo json_encode($array);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">                
    </head>
    <body>
        <form action="" method="post" class="form-horizontal top-margin1" name="form2" id="form2">
            <input type="hidden" name="tipo_nome" id="tipo_nome" value="<?php echo $_REQUEST['tipo']; ?>" />
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="form-group">
                            <label for="mensagem" class="col-lg-3 control-label">CPF/CNPJ</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control validate[required]" id="cpf_cnpj" name="cpf_cnpj">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="mensagem" class="col-lg-3 control-label">Nome</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control validate[required]" id="nome_entsai" name="nome_entsai" value="">
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
                    <div class="alert alert-danger text-bolder hide" id="alert_nome">Registro já cadastrado!</div>
                    <input type="button" id="cad_nome" value="Cadastrar" class="btn btn-primary" />
                    <input type="hidden" name="cad_nome" value="Cadastrar" />
                </div>
            </div>
        </form>
        <script>
            $(function(){
               $("#form2").validationEngine({
                   promptPosition : "topRight"
               });
               
                $('body').on('click', '#cad_nome',function(){
                    var dados = $('#form2').serialize();
                    $.post("form_saida.php", dados, function(resultado){
                        console.log(resultado);
                        $('#tipo_nome').trigger('change');
                        $('.modal').modal('hide');
                        $('#nome').val(resultado);
                    });
                });
                
                $('body').on('blur', '#cpf_cnpj', function(){
                    $.post('form_nome.php', { method: 'verificaNome', value: $(this).val() }, function(data){
                        if(data.status){
                            $('#alert_nome').removeClass('hide');
                            $('#cad_nome').addClass('hide');
                            $('#nome_entsai, #especifica').prop('disabled', true);
                        } else {
                            $('#alert_nome').addClass('hide');
                            $('#cad_nome').removeClass('hide');
                            $('#nome_entsai, #especifica').prop('disabled', false);
                        }
                    }, 'json');
                });
            });
        </script>
    </body>
</html>