<?php 
include ('../conn.php');

$qr_usuario = mysql_query("SELECT * FROM controlectps WHERE id_controle = '".$_GET['id']."' ");
$row_usuario = mysql_fetch_assoc($qr_usuario);

function formato_brasileiro($data){
    return implode('/', array_reverse(explode('-', $data)));
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: CTPS </title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/style-print.css" rel="stylesheet">
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
        <style>
/*            body{
                margin: 0;
                font-size: 14px;
                font-family: Arial, Helvetica, sans-serif;
            }
            ol{
                padding: 0;
            }
            table{
                width: 100%;
            }
            .pagina{
                width: 210mm;
                height: 297mm;

                page-break-after: always;
            }
            .table-borded {
                border-collapse: collapse;
            }
            .table-borded, .table-borded th, .table-borded td {
                border: 1px solid black;
            }
            .text-justify{
                text-align: justify;
            }
            .text-left{
                text-align: left;
            }
            .text-rigth{
                text-align: right;
            }
            .text-center{
                text-align: center;
            }
            .logo{
                display: block;
                margin: auto;
                height: 1.5cm;
            }
            .padding{
                display: inline-block;
                padding: 0 5px;
            }
            h1,h2,h3,h4,h5,h6{text-align: center;}
            h1{font-size: 1.5em !important;}
            h2{font-size: 1.4em !important;}
            h3{font-size: 1.3em !important;}
            h4{font-size: 1.2em !important;}
            h5{font-size: 1.1em !important;}
            h6{font-size: 1em !important;}
            
            @media screen {
                body{
                    background-color: #555;
                    margin-top: 80px;
                }
                .pagina{
                    background-color: #fff;
                    margin: 50px auto;
                    padding: 2cm;
                    box-shadow: 0 0 10px #000;
                    -moz-shadow: 0 0 10px #000;
                    -webkit-box-shadow: 0 0 10px #000;
                }
            }

            @media print{
                .pagina{padding: 0;}
                .no-print{
                    display: none;
                }
            }*/

            
        </style>
        <script>
//            $(document).ready(function () {
//                $("#imprimir").click(function () {
//                    window.print();
//                });
//                $("#voltar").click(function () {
//                    window.history.back();
//                });
//            });



        </script>
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn">Voltar</button>-->
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
               
            </div>
            <?php
            if ($row_evento['cod_status'] == 40) {

                list($ano_entrada, $mes_entrada, $dia_entrada) = explode('-', $row['data_entrada']);
                list($ano_ferias, $mes_ferias, $dia_ferias) = explode('-', $data);

                $aquisitivo_inicial = implode('/', array_reverse(explode('-', $row['data_entrada'])));
                $aquisitivo_final = date('d/m/Y', mktime('0', '0', '0', $mes_entrada, $dia_entrada, $ano_entrada + 1));

                $ferias_inicial = implode('/', array_reverse(explode('-', $data)));
                $ferias_final = $row_evento['data_retorno2']
                ?>

                
                <?php
                if (!empty($row_evento['obs'])) {
                    echo '<p>&nbsp;</p><p><strong>OBSERVA&Ccedil;&Otilde;ES</strong><br>' . $row_evento['obs'] . '</p>';
                }
            } else {
                ?>

                <br>
                <br>
                <br>
                <br>
                <p align="center"><img src="../imagens/logomaster1.gif"></p>
                <br>
                <br>
                <br>
                <br>
                <br><br>
                <p class="text-center"><strong>COMPROVANTE DE DEVOLUÇÃO DA CARTEIRA DO TRABALHO E PREVIDENCIA SOCIAL :</strong> </span></p>
                <br>
                <p class="text-center"><strong>ART. 29 E PARAGRAFOS 2&ordm; E 3&ordm; DA CLT, COM ALTERACAO DADA PELA LEI No 7.855 DE 24/10/1989</strong> </p>
                <p class="text-center">
                    <span class="padding"><strong>C.T.P.S No:</strong> <?php echo $row_usuario['numero'];  ?> </span>
                    <span class="padding"><strong>SÉRIE: </strong> <?php echo $row_usuario['serie'];  ?> </span>
                    <span class="padding"><strong>NOME DO EMPREGADO: </strong> <?php echo $row_usuario['nome']; ?> </span>
                </p>
                <p class="text-center"><strong>RECEBI A CARTEIRA DE TRABALHO E PREVIDENCIA SOCIAL ACIMA, COM AS DEVIDAS ANOTACOES DENTRO DO PRAZO DE 48 HORAS, DE ACORDO COM A LEI EM VIGOR.:</strong></p>
                <p class="text-center"><strong>SÃO PAULO, </strong> <?php echo formato_brasileiro($row_usuario['data_cad']); ?>  </p>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            <?php } ?>
            <br>
            <br>
            <br>
            <br>
            <p align="center">_____________________________________________________________</p>
            <p align="center"><?php echo $row_usuario['nome']; ?></p>
            <div align="center">
                <strong>
                   
            </div>
            <br>
            <br>
            <br>
            <br>
            <hr color="#333333">
            <br>
           
        </div>

    </body>
</html>