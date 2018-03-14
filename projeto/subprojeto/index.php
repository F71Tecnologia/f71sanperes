<?php
include('include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');


$projeto = $_GET['id'];
$regiao = $_GET['regiao'];

$query = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row = mysql_fetch_assoc($query);


if (isset($_POST['enviar'])) {
    header("Location:renovacao.php?m=$link_master&id=$row[id_projeto]&regiao=$row[id_regiao]&tp=$_POST[termos]");
}
?>
<html>
    <head>
        <title>:: Intranet :: Edi&ccedil;&atilde;o de Projeto</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="../js/ramon.js"></script>
        <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
        <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="../jquery/priceFormat.js"></script>
        <script type="text/javascript">



            /*function validaForm(){
     
             var termo0 = document.form1.termos_0.checked;
             var termo1 = document.form1.termos_1.checked;
             var termo2 = document.form1.termos_2.checked;
             var termo3 = document.form1.termos_3.checked;
             var aviso  = document.getElementById('alert');
     
             if(termo0 == false && termo1 == false && termo2 == false && termo3 == false) {
     
             aviso.innerHTML = 'Escolha o tipo de documento.';
             return false
             } else {
             return true;
             }
     
             }*/

        </script>
    </head>
    <body>
        <div id="corpo">
            <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                <tr>
                    <td>
                        <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
                            <h2 style="float:left; font-size:18px;">
                                RENOVAÇÕES E PRORROGAÇÕES DO <span class="projeto"> PROJETO</span>
                            </h2>

                            </p>
                            <div class="clear"></div>
                        </div>
                        <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                            <div id="alert" style="background-color:#FF5959;color:#FFF;font-weight:bold;padding-left:3px;"></div>
                            <table cellpadding="0" align="center" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="6">RENOVAÇÕES E PRORROGAÇÕES</td>
                                </tr>
                                <tr>
                                    <td height="31" class="secao">Tipo de documento:</td>
                                    <td colspan="5" rowspan="2">
                                        <p>
                                            <label>
                                                <input type="radio" name="termos" value="APOSTILAMENTO" id="termos_0"  class="validate[required] radio">
                                                Apostilamento</label>
                                            <br>
                                            <label>
                                                <input type="radio" name="termos" value="TERMO DE PARCERIA " id="termos_1"  class="validate[required] radio">
                                                Termo de Parceria </label>
                                            <br>
                                            <label>
                                                <input type="radio" name="termos" value="TERMO ADITIVO" id="termos_2" class="validate[required] radio">
                                                Termo Aditivo </label>
                                            <br>          
                                            <input type="radio" name="termos" value="NOVO CONV&Ecirc;NIO" id="termos_3" class="validate[required] radio">
                                            Novo Conv&ecirc;nio
                                            </label>
                                            <br>
                                            <input type="radio" name="termos" value="CONTRATO DE GESTÃO" id="termos_4" class="validate[required] radio">
                                            Contrato de gestão
                                            </label>
                                        </p></td>
                                </tr>
                                <tr>
                                    <td height="22" class="secao">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td class="secao">&nbsp;</td>
                                    <td colspan="5" align="right"><input name="enviar" type="submit" id="enviar" value="OK"/></td>
                                </tr>
                                <tr>
                                </tr>
                            </table>
                        </form></td>
                </tr>
            </table>

        </div>
    </body>
</html>