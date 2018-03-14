<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');

$enc = str_replace('--', '+', $_REQUEST['enc']);
$link = decrypt($enc);

list($regiao, $clt, $id_evento, $data) = explode('&', $link);

$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row = mysql_fetch_array($qr_clt);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row[id_projeto]'");
$row_pro = mysql_fetch_array($qr_projeto);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row[id_regiao]'");
$row_reg = mysql_fetch_array($qr_regiao);

$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($qr_curso);

$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$row[id_projeto]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$qr_eventos = mysql_query("SELECT nome_status, cod_status, 
                            date_format(data, '%d/%m/%Y') AS data2,
                            date_format(data_retorno, '%d/%m/%Y') AS data_retorno2, obs,data_retorno
                            FROM rh_eventos WHERE id_evento = '$id_evento'");
$row_evento = mysql_fetch_array($qr_eventos);

$dia = date('d');
$mes = (int) date('m');
$ano = date('Y');

$meses = array('-', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

$nomeMes = $meses[$mes];
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Relatório de Termo de Vale Transporte em Lote </title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../resources/css/style-print.css" rel="stylesheet">
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
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
                <?php
                include('../../empresa.php');
                $img = new empresa();
                $img->imagem();
                ?>
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

                <p><strong>AVISO PRÉVIO DE FÉRIAS       </strong></p>
                <p>&nbsp;</p>
                <p>Comunicação ao Sr.(ª) <?= $row['nome'] ?>
                </p>
                <p>&nbsp;</p>
                <p>O <strong><?php echo $row_empresa['razao'] ?></strong> vem, através do presente, notificar o 
                    <?= $row['nome'] ?>
                    , com antecedência de 30 (trinta) dias, nos termos do art. 135 da Consolidação das Leis do Trabalho, que concederá férias no período abaixo determinado.</p>
                <p>&nbsp;</p>
                <p><strong>PERÍODO DE AQUISIÇÃO</strong><br>
                    O período de aquisição originador do presente direito é de: <?= $aquisitivo_inicial ?> a <?= $aquisitivo_final ?>.</p>
                <p>&nbsp;</p>
                <p><strong>PERÍODO DE GOZO DE FÉRIAS</strong><br>
                    O período de gozo das férias será de: <?= $ferias_inicial ?> a <?= $ferias_final ?>.</p>

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
                <p align="center"><b><u>DECLARA&Ccedil;&Atilde;O</u></b></p>
                <br>
                <br>
                <br>
                <br>
                <br><br>
                <p class="text-center"><strong>PARTICIPANTE:</strong> <?= $row['nome'] ?></span></p>
                <br>
                <p class="text-center"><strong>STATUS:</strong> <?= $row_evento['nome_status'] ?></p>
                <p class="text-center">
                    <span class="padding"><strong>DATA ALTERA&Ccedil;&Atilde;O:</strong> <?= $row_evento['data2'] ?></span>
                    <span class="padding"><strong>DATA PREVISTA PARA RETORNO:</strong> <?= $row_evento['data_retorno2'] ?></span>
                </p>
                <p class="text-center"><strong>OBSERVA&Ccedil;&Otilde;ES:</strong> <?= $row_evento['obs'] ?></p>
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
            <p class="style1"><?= '&nbsp;&nbsp;&nbsp;&nbsp;' . $row_reg['regiao'] . ', ' . $dia . ' de ' . $nomeMes . ' de ' . $ano . '.' ?></p>
            <br>
            <br>
            <br>
            <br>
            <p align="center">_____________________________________________________________</p>
            <div align="center">
                <strong>
                    <?php echo $row_empresa['razao'] ?>
            </div>
            <br>
            <br>
            <br>
            <br>
            <hr color="#333333">
            <br>
            <?php echo $row_empresa['razao'] ?>
        </div>

    </body>
</html>