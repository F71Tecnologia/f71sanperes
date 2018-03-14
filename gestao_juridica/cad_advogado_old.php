<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
include("../wfunction.php");
//include "../funcoes.php";
include "include/criptografia.php";

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$usuario = carregaUsuario();

$id_user = $_COOKIE['logado'];
$regiao = mysql_real_escape_string($_GET['regiao']);

if (isset($_POST['enviar'])) {

    $nome = mysql_real_escape_string($_POST['nome']);
    $oab = mysql_real_escape_string($_POST['oab']);
    $uf_oab = mysql_real_escape_string($_POST['uf_oab']);
    $rg = mysql_real_escape_string($_POST['rg']);
    $cpf = mysql_real_escape_string($_POST['cpf']);
    $email = mysql_real_escape_string($_POST['email']);
    $endereco = mysql_real_escape_string($_POST['endereco']);
    $tel = mysql_real_escape_string($_POST['telefone']);
    $cel = mysql_real_escape_string($_POST['cel']);
    $estagiario = mysql_real_escape_string($_POST['estagiario']);
    $uf_oab = mysql_real_escape_string($_POST['uf_oab']);



    $insert = mysql_query("INSERT INTO advogados (adv_nome, adv_oab,  adv_uf_oab, adv_rg, adv_cpf, adv_email, adv_endereco, adv_tel, adv_cel, adv_estagiario, adv_cad, adv_data_cad, adv_status)
						VALUES
						('$nome', '$oab','$uf_oab', '$rg', '$cpf', '$email', '$endereco', '$tel', '$cel', '$estagiario', '$_COOKIE[logado]', NOW(), '1') ") or die(mysql_error());

    if ($insert) {

        header("Location: index.php?regiao=$regiao");
    }
}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "24", "area" => "JURÍDICO", "ativo" => "Consultar Processos", "id_form" => "consulta_processo");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="../js/ramon.js"></script>
            <script type="text/javascript" src="../js/jquery-1.3.2.js"></script>

            <script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
            <script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine.js" ></script>
            <link href="../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

                <script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>

                <script type="text/javascript">
                $(function() {

                $('#cpf').mask('999.999.999-99');
                $('#telefone').mask('(99)9999-9999');
                $('#cel').mask('(99)9999-9999');

                $('#form1').validationEngine();


	
	
                });

                </script>


                <title>Gestão Jurídica</title>
                
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
      
        <div class="page-header box-juridico-header text-left"><h2><span class="glyphicon glyphicon-briefcase"></span> - Gestão Jurídica<small> - CADASTRAR ADVOGADO</small></h2></div>
            <table class="table" align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                <tr>
                    <td>

                        <?php
                        if (!empty($erros)) {
                            $erros = implode('<br>', $erros);
                            echo '<p style="background-color:#C30; padding:4px; color:#FFF;">' . $erros . '</p><p>&nbsp;</p>';
                        }
                        ?>
                    
                        <div class="panel panel-default">
                            
                            <div class="panel-heading"> DADOS</div>
                            <div id="corpo">
                                <div class="panel-body"> 
                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>?regiao=<?php echo $regiao ?>" class="form-horizontal" method="post" name="form1" 
                                  id="form1" enctype="multipart/form-data" onSubmit="return validaForm() ">

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Nome:</label>
                                        <div class="col-sm-4">
                                            <input name="nome" size="50" type="text" id="nome" class="validate[required] form-control" />                     
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">OAB:</label>
                                        <div class="col-sm-4">
                                            <input name="oab" size="15" type="text" id="oab" class="validate[required] form-control"/>                     
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">OAB UF:</label>
                                        <div class="col-sm-2">
                                             <select name="uf_oab" class="form-control">
                                                <?php
                                                $qr_uf = mysql_query("SELECT * FROM uf");
                                                while ($row_uf = mysql_fetch_assoc($qr_uf)):

                                                    echo '<option value="' . $row_uf['uf_id'] . '" >' . $row_uf['uf_sigla'] . ' </option>';

                                                endwhile;
                                                ?>               
                                            </select>                      
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">UF:</label>
                                        <div class="col-sm-2">
                                                <input name="uf" size="15" type="text" id="uf"  class="validate[required] form-control"/>          
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">RG:</label>
                                        <div class="col-sm-4">
                                                <input name="rg" size="15" type="text" id="rg"  class="validate[required] form-control"/>          
                                        </div>
                                    </div>

                                     <div class="form-group">
                                        <label class="col-sm-1 control-label">CPF:</label>
                                        <div class="col-sm-4">
                                                <input name="cpf" size="10" type="text" id="cpf" class="validate[required] form-control"/>         
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">E-mail:</label>
                                        <div class="col-sm-4">
                                               <input name="email" size="30" type="text" id="email" class="form-control"/>         
                                        </div>
                                    </div>

                                     <div class="form-group">
                                        <label class="col-sm-1 control-label">Endere&ccedil;o:</label>
                                        <div class="col-sm-4">
                                               <input name="endereco" size="50" type="text" id="endereco" class="validate[required] form-control"/>       
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Telefone:</label>
                                        <div class="col-sm-4">
                                               <input name="telefone" size="10" type="text" id="telefone" class="validate[required] form-control"/>     
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">CEL.:</label>
                                        <div class="col-sm-4">
                                              <input name="cel" size="10" type="text" id="cel" class="form-control"/>     
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-1 control-label">Estagi&aacute;rio:</label>
                                        <div class="col-sm-4">
                                             <input type="radio" name="estagiario" value="1" /> Sim <input name ="estagiario"type="radio" value="" /> N&atilde;o
                                        </div>
                                    </div>

                                <div class="panel-footer text-right">
                                   <input name="enviar" type="submit" value="CADASTRAR" class="btn btn-primary"/>
                                </div>

                            </form>
                        </div>
                    </div>

                    </td>
                </tr>

            </table>
        </div>
            <?php include_once '../template/footer.php'; ?>
        </div> <!-- fim container -->
    

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/financeiro/reembolso.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        
</body>
</html>
