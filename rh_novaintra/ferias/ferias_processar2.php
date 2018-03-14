<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../empresa.php";
include "../../wfunction.php";
include_once('../../classes/FeriasProgramadasClass.php');
 
$count = count($_REQUEST['ano']);

$objFeriasProg = new FeriasProgramadasClass();



?>

<!--<!DOC?TYPE html>-->
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../../favicon.png" rel="shortcut icon">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/style-print.css" rel="stylesheet" media="all">
    </head>
    <body>
        <div class="container" id='popup' style="background-color: #fff">
    <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i>Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    <br> 
    
    
    <?php for($i=0; $i<$count; $i++){ ?>
    <?php $dados_ferias = $objFeriasProg->getFeriasProgramadasById($_REQUEST['id_ferias'][$i]); ?>
    <div class="text-center"><img src="../../imagens/logomaster1.gif"></div>
        <div style="border: 1px solid black; padding: 20px;">
                <table  id="popupaviso">

                    <tr>
                        <td colspan="4" class="text-center" style="border: 1px solid black">
                            <h3>AVISO DE GOZO DE FÉRIAS</h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 20px;">
                            <?php echo $dados_ferias["razao"] ?><br>
                            CNPJ:  <?php echo $dados_ferias["cnpj"] ?> 
                        </td>
                    </tr>
                    <tr >
                        <td colspan="4" style="border: 1px solid black; padding: 20px;">
                            Nome do Empregado(a): <?php echo $dados_ferias["nome"] ?>
                            <br><br>
                            Data de Admissão:  <?php echo $dados_ferias["data_entradaBR"] ?>
                            <br><br>
                            Antecipação da primeira parcela do 13º salário: <u><?php echo $dados_ferias["decimo_terceiro"] ?></u>
                            <br><br><br>
                            <p>O empregador, por meio do presente documento, em conformidade com o art. 135 da CLT,<br>
                                vem notificar o empregado, com antecedência de <?php echo $dados_ferias["dias_ferias"] . " (" . numero_extenso($dados_ferias["dias_ferias"]) ?>) dias,<br>
                                a concessão de suas férias relativas ao período aquisitivo de <u><?php echo $dados_ferias["aq_inicioBR"] ?></u> à <u><?php echo $dados_ferias["aq_fimBR"] ?></u>,
                                cujo período de<br> gozo será de <u><?php echo $dados_ferias["inicioBR"] ?></u> à <u><?php echo $dados_ferias["fimBR"] ?></u>. </p><br>
                            <p class="text-right">São Paulo, _____ de ________________ de <?php echo date('Y') ?>.</p>

                        <br>
                        <br>

                        <table class="table-condensed text-center" border="0">
                            <tbody>
                                <tr style="margin-bottom: -50px">
                                    <td></td>
                                    <td><img src="../../../../extranet/img/assinatura.jpg" style="width:400px; margin-bottom: -65px"></td>
                                </tr>        
                                <tr>
                                    <td>--------------------------------------------------------</td>
                                    <td>--------------------------------------------------------</td>
                                </tr>        
                                <tr>
                                    <td>Assinatura do Empregado</td>
                                    <td>Assinatura do Empregador</td>
                                </tr>  
                            </tbody>    
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>            
    </div>
                
    <div style="padding-top: 10px;"></div>
    <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
