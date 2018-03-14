<?php
session_start();

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}



include('../conn.php');
include('../funcoes.php');

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
$sql_evento = "SELECT nome_status, cod_status, 
                date_format(data, '%d/%m/%Y') AS data2,
                date_format(data_retorno, '%d/%m/%Y') AS data_retorno2, obs,data_retorno, dias
                FROM rh_eventos WHERE id_evento = '$id_evento'";
//echo $sql_evento."<br>";
$qr_eventos = mysql_query($sql_evento);
$row_evento = mysql_fetch_array($qr_eventos);

$dia = date('d');
$mes = date('m');
$ano = date('Y');

$meses = array('-', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
$nomeMes = $meses[$mes];
?>
<?php
// ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&1&$clt");
$link = str_replace("+", "---", $link);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />

        <title>Evento: <?= $row_evento['nome_status'] ?></title>
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js-mask/jquery.maskedinput.js" type="text/javascript"></script>
        <script src="../js-mask/jquery.validate.js" type="text/javascript"></script>
        <script src="../js-mask/valid.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(function() {
                $.mask.definitions['~'] = "[+-]";
                $('.data').datepicker();
                $("input").blur(function() {
                    $("#info").html("Unmasked value: " + $(this).mask());
                }).dblclick(function() {
                    $(this).unmask();
                });
            });
        </script>

        <!--script type="text/javascript">
        $(document).ready(function() {
        
        DAY = 1000 * 60 * 60  * 24;
        
        data1 = "03/08/2012 00:00";
        data2 = "05/08/2012 23:00";
        
        var nova1 = data1.toString().split('/');
        Nova1 = nova1[1]+"/"+nova1[0]+"/"+nova1[2];
        
        var nova2 = data2.toString().split('/');
        Nova2 = nova2[1]+"/"+nova2[0]+"/"+nova2[2];
        
        d1 = new Date(Nova1)
        d2 = new Date(Nova2)
        
        days_passed = Math.round((d2.getTime() - d1.getTime()) / DAY)
        
        alert(days_passed);
        
        });
        </script-->
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
            @media print {
                .printButtons { display:none; }
            }
            .printButtons{
                background-color: #CCCCCC;
                border-radius: 3px;
                padding: 5px;
                cursor: pointer;
                text-decoration: none;
                color: #000;
            }
            .imprime{
                background-color: #CCCCCC;
                border-radius: 3px;
                padding: 5px;
                cursor: pointer;
                text-decoration: none;
                color: #000;
            }
            #submit{
                margin-left:  90%; 
                color: #000;
                background-color: #CCCCCC;
            }
            label{
                color: red;
            }
            input{
                border-radius: 5px;
            }
            .ui-datepicker{
                font-size: 12px;
            }
        </style>
    </head>
    <!--body onLoad="javascript: window.print();"-->
    <body>
        <center>
            <form action="upd_form_evento.php" method="post" id="formEdit">
                <table width="800" border="0" cellspacing="5" cellpadding="0" class="bordaescura1px">
                    <tr>
                        <td width="693" height="149" align="center" valign="middle" bgcolor="#CCCCCC">
                            <?php
                            include('../empresa.php');
                            $img = new empresa();
                            $img->imagem();
                            ?>
                            <br />
                            <br />
                            <strong><?= $row_pro['nome'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td  valign="top">
                            <table>
                                <tr>
                                    <td><span class="fonteforte">&nbsp;Participante:</span></td>
                                    <td><span class="fontenormal"><input type="text" value="<?= $row['nome'] ?>" disabled name="nome"></span></td>
                                </tr>
                                <tr>
                                    <td><span class="fonteforte">&nbsp;Ocorrência:</span></td>
                                    <td><span class="fontenormal">
                                            <input type="hidden" name="ocorrencia" value="<?= $row_evento['cod_status'] ?>" />
                                            <?php
                                            $qrsqlst = 'select * from rhstatus where codigo=' . $row_evento['cod_status'];
                                            $sqlst = mysql_query($qrsqlst);
                                            $row1 = mysql_fetch_array($sqlst);
                                            echo $row1['especifica'];
                                            ?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <td><span class="fonteforte">&nbsp;Data alteração:</span></td>
                                    <td><span class="fontenormal">
                                            <input type="text" name="dataAlt" id="dataAlt" class="data" value="<?= $row_evento['data2'] ?>"/>
                                        </span></td>
                                </tr>
                                <tr>
                                    <td><span class="fonteforte">&nbsp;Data prevista para retorno:</span></td>
                                    <td>
                                        <?php if ($row_evento['cod_status'] != 10) { ?>
                                            <span class="fontenormal">
                                                <input type="text" name="dataRet" id="dataRet" class="data" value="<?= $row_evento['data_retorno2'] ?>"/>
                                            </span> <a href="javascript:;" onclick="calcular_datas()" >calcular datas</a>
                                            <?php
                                        } else {
                                            echo $row_evento['data_retorno'];
                                            echo "<input type=\"hidden\" name=\"dataRet\" id=\"dataRet\" class=\"data\" value=\"{$row_evento['data_retorno2']}\"/>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fonteforte">&nbsp;Qt. dias:</span></td>
                                    <td><span class="fontenormal">
                                            <?php if ($row_evento['cod_status'] != 10) { ?>
                                                <input type="number" min="0" size="3" maxlength="3" name="qt_dias" id="qt_dias" value="<?php echo $row_evento['dias']; ?>"/>
                                                <?php
                                            } else {
                                                echo $row_evento['dias'];
                                                echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"qt_dias\" id=\"qt_dias\" value=\"{$row_evento['dias']}\"/>";
                                            }
                                            ?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <td><span class="fonteforte">&nbsp;Observações:</span></td>
                                    <td><textarea name="obs" cols="20" rows="3" id="obs" style=" width: 200px; height:100px;"><?= $row_evento['obs'] ?></textarea></td>
                                </tr>
                            </table>
                            <br/>
                            <input type="text" value="<?php echo $id_evento; ?>" name="id_evento" hidden=""/>
                            <input type="hidden" name="acao" value="atualizar_evento"/>
                            <input type="button" value="Enviar" onclick="atualizar_evento()"/>
                            <p class="style1"><?= '&nbsp;&nbsp;&nbsp;&nbsp;' . $row_reg['regiao'] . ', ' . $dia . ' de ' . $nomeMes . ' de ' . $ano . '.' ?></p>
                            <div align="center">
                                <?php echo $row_empresa['razao'] ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
            <script>
                function calcular_datas() {
                    $('#qt_dias').attr('disabled', 'disabled');
                    $('#qt_dias').val('calculando...');
                    $.post('upd_form_evento.php', {acao: 'calcular_datas', dataAlt: $('#dataAlt').val(), dataRet: $('#dataRet').val()},
                    function(data) {
                        $('#qt_dias').val(data);
                        $('#qt_dias').removeAttr('disabled');
                    });
                }
                function atualizar_evento() {
                    $.post('upd_form_evento.php', $('#formEdit').serialize(),
                            function(data) {
                                alert(data.resp);
                                if (data.status) {
                                    //$('body').html('');
                                    window.location.href = 'rh_eventos.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$row_evento[cod_status]")) ?>';
                                } else {
                                }
                            }, 'json');
                }
            </script>

            <br/>
            <a href="rh_eventos.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$row_evento[cod_status]")) ?>" class="printButtons"> Voltar </a>
        </center>
    </body>
</html>