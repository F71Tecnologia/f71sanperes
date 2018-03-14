<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
include("../wfunction.php");
include("../classes/AdvogadosClass.php");
include "include/criptografia.php";

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$usuario = carregaUsuario();
$advogados = new AdvogadosClass;
$id_user   = $_COOKIE['logado'];
$id_adv = $_GET['id']; 
$regiao  = mysql_real_escape_string($_GET['regiao']);

$arrayAdv = $advogados->getAdvogado($id_adv);
if($arrayAdv['adv_estagiario'] == 1)
{
    $estagS = "CHECKED";
    $estagN = "";
}
else
{
    $estagS = "";
    $estagN = "CHECKED";
}

if(isset($_POST['atualizar'])){
    $update = $advogados->updateDados($_POST);
    if($update)
    {
        header("Location: index.php?regiao=$regiao");
    }
    
}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "24", "area" => "JUR&Iacute;DICO", "ativo" => "Edi&ccedil;&atilde;o de Advogados", "id_form" => "consulta_processo");
$breadcrumb_pages = array("Gest&atilde;o Jur&iacute;dica"=>"index.php"); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        
        <title>Gest&atilde;o Jur&iacute;dica</title>

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

            <div class="page-header box-juridico-header text-left"><h2><span class="glyphicon glyphicon-briefcase"></span> - Gest&atilde;o Jur&iacute;dica<small> - EDITAR ADVOGADO</small></h2></div>
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
                        <div class="panel-heading text-bold"> EDI&Ccedil;&Atilde;O DE DADOS</div>
                            <div class="panel-body"> 
                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>" class="form-horizontal" method="post" name="form1" 
                                      id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Nome: <span style="color:red;">*</span></label>
                                        <div class="col-sm-9">
                                            <input name="nome" size="50" type="text" id="nome" class="validate[required] form-control" value="<?php echo $arrayAdv['adv_nome']; ?>" />                     
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">E-mail: <span style="color:red;">*</span></label>
                                        <div class="col-sm-9">
                                            <input name="email" size="30" type="text" id="email" class="form-control" value="<?php echo $arrayAdv['adv_email'];?>"/>         
                                        </div>
                                    </div>
                                    
                                    <!-- Cep -->
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="cep">Cep: <span style="color:red;">*</span></label>  
                                      <div class="col-md-9">
                                          <input id="cep"  name="cep" type="text" placeholder="digite seu cep" class="form-control input-md formrhclt validate[required]" value="<?php echo $arrayAdv['adv_cep'];?>">
                                      </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Endere&ccedil;o: <span style="color:red;">*</span></label>
                                        <div class="col-sm-5">
                                            <input name="endereco" size="50" type="text" id="endereco" class="validate[required] form-control" value="<?php echo utf8_encode($arrayAdv['adv_endereco']); ?>"/>       
                                        </div>
                                        <label class="col-sm-2 control-label">N&deg;: <span style="color:red;">*</span></label>
                                        <div class="col-sm-2">
                                            <input name="numero" type="text" id="numero" class="validate[required] form-control" value="<?php echo utf8_encode($arrayAdv['adv_numero']); ?>"/>       
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Complemento:</label>
                                        <div class="col-sm-5">
                                            <input name="compl" type="text" id="compl" class="form-control" value="<?php echo utf8_encode($arrayAdv['adv_compl']); ?>"/>       
                                        </div>
                                        <label class="col-sm-2 control-label">Bairro: <span style="color:red;">*</span></label>
                                        <div class="col-sm-2">
                                            <input name="bairro" type="text" id="bairro" class="validate[required] form-control" value="<?php echo utf8_encode($arrayAdv['adv_bairro']); ?>"/>       
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        
                                        <label class="col-sm-2 control-label">Cidade: <span style="color:red;">*</span></label>
                                        <div class="col-sm-5">
                                            <input name="cidade" type="text" id="cidade" class="validate[required] form-control" value="<?php echo utf8_encode($arrayAdv['adv_cidade']); ?>"/>       
                                        </div>
                                        <label class="col-sm-2 control-label">UF: <span style="color:red;">*</span></label>
                                        <div class="col-sm-2">
                                            <select name="uf" type="text" id="uf"  class="validate[required] form-control">          
                                            <?php $advogados->MontaSelect($arrayAdv['adv_uf']);?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Telefone:</label>
                                        <div class="col-sm-4">
                                            <input name="telefone" size="10" type="text" id="telefone" class="validate[required] form-control" value="<?php echo $arrayAdv['adv_tel'];?>"/>     
                                        </div>

                                        <label class="col-sm-2 control-label">Celular:</label>
                                        <div class="col-sm-3">
                                            <input name="cel" size="10" type="text" id="cel" class="form-control" value="<?php echo $arrayAdv['adv_cel'];?>"/>     
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">CPF: <span style="color:red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input name="cpf" size="10" type="text" id="cpf" class="validate[required] form-control" value="<?php echo $arrayAdv['adv_cpf'];?>"/>         
                                        </div>
                                        
                                        <label class="col-sm-2 control-label">RG: <span style="color:red;">*</span></label>
                                        <div class="col-sm-3">
                                            <input name="rg" size="15" type="text" id="rg"  class="validate[required] form-control" value="<?php echo $arrayAdv['adv_rg']?>"/>          
                                        </div>

                                        
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">OAB: <span style="color:red;">*</span></label>
                                        <div class="col-sm-4">
                                            <input name="oab" size="15" type="text" id="oab" class="validate[required] form-control" value="<?php echo $arrayAdv['adv_oab'];?>"/>                     
                                        </div>

                                        <label class="col-sm-2 control-label">OAB UF:</label>
                                        <div class="col-sm-3">
                                            <select name="uf_oab" class="form-control">
                                                <?php
                                                $advogados->MontaSelect($arrayAdv['oab_uf_id']);
                                                ?>               
                                            </select>                      
                                        </div>
                                        
                                    </div>
      

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Estagi&aacute;rio:</label>
                                        <div class="col-sm-2">
                                            <div class="input-group">
                                                <label class="input-group-addon">
                                                    <input type="radio" name="estagiario" id="estagiarios" value="1" <?php echo $estagS;?> style="cursor:pointer" />
                                                </label>
                                                <label type="text" class="form-control" for="estagiarios" style="cursor:pointer">Sim</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <div class="input-group">
                                                <label class="input-group-addon">
                                                    <input name="estagiario" type="radio" id="estagiarion" value="2" <?php echo $estagN;?> style="cursor:pointer" />
                                                </label>
                                                <label type="text" class="form-control" for="estagiarion" style="cursor:pointer" >N&atilde;o</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="panel-footer text-right">
                                        <input name="atualizar" type="submit" value="SALVAR" class="btn btn-success"/>
                                    </div>
                                        <input name="id_adv" type="hidden" id="id_adv" class="validate[required] form-control" value="<?php echo $id_adv;?>"/>         
                                    </form>
                                </div>
                            </div>

                        </td>
                    </tr>

                </table>

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
        <script type="text/javascript" src="../js-mask/jquery.maskedinput.js" ></script>

        <script type="text/javascript">
            $(function () {

                $('#cpf').mask('999.999.999-99');
                $('#telefone').mask('(99)9999-9999');
                $('#cel').mask('(99)9999-9999');
                $('#cep').mask('99999-999');

                $('#form1').validationEngine();
                
                 function limpa_formulario_cep() {
                // Limpa valores do formulário de cep.
                $("#endereco").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#uf").val("");
                
                }

                //Quando o campo cep perde o foco.
                $("#cep").blur(function() {

                    //Nova variável "cep" somente com dígitos.
                    var cep = $(this).val().replace(/\D/g, '');

                    //Verifica se campo cep possui valor informado.
                    if (cep != "") {

                        //Expressão regular para validar o CEP.
                        var validacep = /^[0-9]{8}$/;

                        //Valida o formato do CEP.
                        if(validacep.test(cep)) {

                            //Preenche os campos com "..." enquanto consulta webservice.
                            $("#endereco").val("...");
                            $("#bairro").val("...");
                            $("#cidade").val("...");
                            $("#uf").val("...");
                            

                            //Consulta o webservice viacep.com.br/
                            $.getJSON("//viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                                if (!("erro" in dados)) {
                                    //Atualiza os campos com os valores da consulta.
                                    $("#endereco").val(dados.logradouro);
                                    $("#bairro").val(dados.bairro);
                                    $("#cidade").val(dados.localidade);
                                    $("#uf").val(dados.uf);
                                    
                                } //end if.
                                else {
                                    //CEP pesquisado não foi encontrado.
                                    limpa_formulario_cep();
                                    alert("CEP não encontrado.");
                                }
                            });
                        } //end if.
                        else {
                            //cep é inválido.
                            limpa_formulario_cep();
                            alert("Formato de CEP inválido.");
                        }
                    } //end if.
                    else {
                        //cep sem valor, limpa formulário.
                        limpa_formulario_cep();
                    }
                });

            });

        </script>


</body>
</html>
