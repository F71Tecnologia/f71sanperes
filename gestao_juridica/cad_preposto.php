<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";
include("../wfunction.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_user   = $_COOKIE['logado'];
$regiao  = mysql_real_escape_string($_GET['regiao']);

if(isset($_POST['enviar'])){
	
$nome = mysql_real_escape_string($_POST['nome']);	

$rg = mysql_real_escape_string($_POST['rg']);
$cpf = mysql_real_escape_string($_POST['cpf']);		
$email = mysql_real_escape_string($_POST['email']);	
$endereco = mysql_real_escape_string($_POST['endereco']);
$tel = mysql_real_escape_string($_POST['telefone']);		
$cel = mysql_real_escape_string($_POST['cel']);	

	
$insert = mysql_query("INSERT INTO prepostos (prep_nome, prep_rg, prep_cpf, prep_email, prep_endereco, prep_tel, prep_cel, prep_cad, prep_data_cad, prep_status)
						VALUES
						('$nome', '$rg', '$cpf', '$email', '$endereco', '$tel', '$cel', '$_COOKIE[logado]', NOW(), '1') ") or die(mysql_error());
						
if($insert) {
	
header("Location: index.php?regiao=$regiao");

}

}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "24", "area" => "JURÍDICO", "ativo" => "Consultar Processos", "id_form" => "consulta_processo");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<!--<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>

<script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>-->


<title>INTRANET - CADASTRAR PREPOSTO</title>

 <!--bootstrap -->
    <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
    <link href="../resources/css/main.css" rel="stylesheet" media="screen">
    <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    <link href="../css/progress.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">
        
</head>
<body>
    <div class="text-left">
    <?php include("../template/navbar_default.php"); ?>
    </div>
        <div class="container">
            
            
                <div class="page-header box-juridico-header text-left" ><h2><span class="glyphicon glyphicon-briefcase"></span> - Gestão Jurídica<small> - CADASTRAR PREPOSTO</small></h2></div>
                    <table  width="100%" class="table" >
<!--                        <table align="center" width="100%" cellspacing="0" cellpadding="12" class="table" style="font-size:13px; line-height:22px;">-->
                        
                        <tr>
                          <td>
                            <?php if(!empty($erros)) {
                                                      $erros = implode('<br>', $erros);
                                                      echo '<p style="background-color:#C30; padding:4px; color:#FFF;">'.$erros.'</p><p>&nbsp;</p>';
                                              } ?>


                        <div class="panel panel-default">
                            <div class="panel-heading text-bold">Dados</div>
                           
                                <div class="panel-body">
                                    <form action="<?php echo $_SERVER['PHP_SELF']?>?regiao=<?php echo $regiao;?>" method="post" name="form1" id="form1" class="form-horizontal" enctype="multipart/form-data" onSubmit="return validaForm()">

                                        <table class="table" align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Nome:</label>
                                            <div class="col-sm-5">
                                                <input name="nome" size="50" type="text" id="nome" class="validate[required] form-control" />                     
                                            </div>

                                            <label class="col-sm-1 control-label">E-mail:</label>
                                            <div class="col-sm-2">
                                                <input name="email" size="30" type="text" id="email" class="form-control"/>         
                                            </div>
                                        </div>
                                            
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Endere&ccedil;o:</label>
                                            <div class="col-sm-8">
                                                <input name="endereco" size="50" type="text" id="endereco" class="validate[required] form-control"/>       
                                            </div>
                                        </div>
                                            
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Telefone:</label>
                                            <div class="col-sm-3">
                                                <input name="telefone" size="10" type="text" id="telefone" class="validate[required] form-control"/>     
                                            </div>

                                            <label class="col-sm-1 control-label">Celular:</label>
                                            <div class="col-sm-3">
                                                <input name="cel" size="10" type="text" id="cel" class="form-control"/>     
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">RG:</label>
                                            <div class="col-sm-3">
                                                <input name="rg" size="15" type="text" id="rg"  class="validate[required] form-control"/>          
                                            </div>

                                            <label class="col-sm-1 control-label">CPF:</label>
                                            <div class="col-sm-3">
                                                <input name="cpf" size="10" type="text" id="cpf" class="validate[required] form-control"/>         
                                            </div>
                                        </div>

                                            

                                        <div class="panel-footer text-right">
                                           <input name="enviar" type="submit" value="CADASTRAR" class="btn btn-primary"/>
                                        </div>

                                        </table>
                                    </form>

                                </div> <!-- fim panel body-->
                            </td>
                          </tr>

                    </table>
                    <div class="text-left">
                        <?php include("../template/footer.php"); ?>
                    </div>
                </div>
            
        </div><!-- Fim do container -->
    
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/financeiro/reembolso.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js-mask/jquery.maskedinput.js" ></script>
        
        <script type="text/javascript">
            $(function() {

            $('#cpf').mask('999.999.999-99');
            $('#telefone').mask('(99)9999-9999');
            $('#cel').mask('(99)9999-9999');


                    $('#form1').validationEngine();
                    $('input[name=tipo]').change(function(){

                            var tipo = $(this).val();

                            if(tipo == 1) {

                                    $('#oab').fadeIn();

                            } else {
                                    $('#oab').fadeOut();
                            }



                    });

            });

        </script>
        
</body>
</html>
