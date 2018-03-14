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
$mes = date('m');
$ano = date('Y');

$meses = array('-', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
$nomeMes = $meses[$mes];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../net1.css" rel="stylesheet" type="text/css"/>
        <title>Evento: <?= $row_evento['nome_status'] ?></title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <style type="text/css">
            body {
                background-color:#FFF;
            }
            .bordas {
                border:2px solid #000;
            }
            .fonteforte {
                font:Verdana, Geneva, sans-serif; 
                font-size:14px; 
                font-weight:bold;
            }
            .fontenormal {
                font:Verdana, Geneva, sans-serif; 
                font-size:14px; 
            }
            .printButtons{
                display: inline-block;
                background-color: #CCCCCC;
                border-radius: 3px;
                padding: 5px 10px;
                cursor: pointer;
                text-decoration: none;
                color: #000;
            }
            .printButtons:hover{
                background-color: #BBB;
            }
            @media print {
                .printButtons { display:none; }
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.imprime').click(function() {
                    print();
                });
            });
        </script>
    </head>
    <!body onLoad="javascript: window.print();"-->
    <body>
        <center>
            <table width="800" border="0" cellspacing="5" cellpadding="0" class="bordaescura1px">
                <tr>
                    <td width="693" height="149" align="center" valign="middle" bgcolor="#CCCCCC">
                        <?php
                        include('../../empresa.php');
                        $img = new empresa();
                        $img->imagem();
                        ?>
                        <br />
                        <br />
                        <strong><?= $row_pro['nome'] ?></strong>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
                        
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
                            <p><strong>PERÍODO DE AQUISIÇÃO</strong><br />
                                O período de aquisição originador do presente direito é de: <?= $aquisitivo_inicial ?> a <?= $aquisitivo_final ?>.</p>
                            <p>&nbsp;</p>
                            <p><strong>PERÍODO DE GOZO DE FÉRIAS</strong><br />
                                O período de gozo das férias será de: <?= $ferias_inicial ?> a <?= $ferias_final ?>.</p>

                            <?php
                            if (!empty($row_evento['obs'])) {
                                echo '<p>&nbsp;</p><p><strong>OBSERVA&Ccedil;&Otilde;ES</strong><br />' . $row_evento['obs'] . '</p>';
                            }
                        } else {
                            ?>

                            <br />
                            <br /><br />
                            <p align="center"><b><u>DECLARA&Ccedil;&Atilde;O</u></b></p>
                            <br />
                            <br />
                            <br /><br />
                            <span class="fonteforte">&nbsp;&nbsp;&nbsp;&nbsp;PARTICIPANTE:</span>&nbsp;<span class="fontenormal"><?= $row['nome'] ?></span>
                            <br />
                            <br />
                            <span class="fonteforte">&nbsp;&nbsp;&nbsp;&nbsp;STATUS:</span>&nbsp;<span class="fontenormal">
                                <?= $row_evento['nome_status'] ?>
                            </span> <br />
                            <br />
                            <span class="fonteforte">&nbsp;&nbsp;&nbsp;&nbsp;DATA ALTERA&Ccedil;&Atilde;O:</span>&nbsp;<span class="fontenormal">
                                <?= $row_evento['data2'] ?>
                            </span>  &nbsp;&nbsp;&nbsp;&nbsp;<span class="fonteforte">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DATA PREVISTA PARA RETORNO:</span>&nbsp;<span class="fontenormal">
                                <?= $row_evento['data_retorno2'] ?>
                            </span><br />
                            <br />
                            <span class="fonteforte">&nbsp;&nbsp;&nbsp;&nbsp;OBSERVA&Ccedil;&Otilde;ES:</span>&nbsp;<span class="fontenormal">
                                <?= $row_evento['obs'] ?>
                            </span> 
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                        <?php } ?>
                        <p class="style1"><?= '&nbsp;&nbsp;&nbsp;&nbsp;' . $row_reg['regiao'] . ', ' . $dia . ' de ' . $nomeMes . ' de ' . $ano . '.' ?></p>
                        <br />
                        <br />
                        <br />
                        <br />
                        <p align="center">_____________________________________________________________</p>
                        <div align="center">
                            <strong>
                                <?php echo $row_empresa['razao'] ?>
                        </div>
                        </p>
                        <br />
                        <hr color="#333333" />
                        <br />
                        <?php echo $row_empresa['razao'] ?>
                    </td>
                </tr>
            </table>
            <?php
// ENCRIPTOGRAFANDO A VARIAVEL
            $link = encrypt("$regiao&1&$clt");
            $link = str_replace("+", "---", $link);
            ?>
            <br/>
            <a href="#"class="printButtons" onclick="window.close()"> Fechar Janela </a>
            &nbsp;
            <a href="#" class="printButtons imprime"> Imprimir </a>
        </center>
    </body>
</html>