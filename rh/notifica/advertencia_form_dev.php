<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}
include "../../conn.php";
$id_clt = isset($_REQUEST['clt']) ? $_REQUEST['clt'] : NULL;
$id_doc = isset($_REQUEST['id_doc']) ? $_REQUEST['id_doc'] : NULL;
$id_regiao = isset($_REQUEST['id_reg']) ? $_REQUEST['id_reg'] : NULL;
$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : NULL;
$id_projeto = isset($_REQUEST['pro']) ? $_REQUEST['pro'] : NULL;

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_regiao' ");
$row_regiao = mysql_fetch_array($result_regiao);

if (empty($id_doc) && $acao == 1) {
    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data = isset($_POST['data']) ? $_POST['data'] : NULL;
    $motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;
    $data = explode('/', $data);
    $data_cad = $data[2] . '-' . $data[1] . '-' . $data[0];
    $tipo = $_REQUEST['medida_disciplinar'];
    
    $user_cad = $_COOKIE['logado'];

    $sql_select1 = "SELECT * FROM rh_doc_status WHERE tipo = '9' and id_clt = '$id_clt'";
    $result_verifica1 = mysql_query($sql_select1);
    if (mysql_num_rows($result_verifica1) >= 3) {
        echo '<script type="text/javascript"> alert("O funcionário já recebeu 3 ou mais suspensões!")</script>';
    }
    
    $sql_select = "SELECT * FROM rh_doc_status WHERE tipo = '10' and id_clt = '$id_clt'";
    $result_verifica = mysql_query($sql_select);
    if (mysql_num_rows($result_verifica) >= 2) {
        echo '<script type="text/javascript"> alert("O funcionário já recebeu 2 ou mais advertências!")</script>';
//        mysql_query("UPDATE rh_clt SET status = 17 where id_clt = '$id_clt' and id_regiao = '$id_regiao' and id_projeto = '$id_projeto'");
    }
    
    mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user,motivo) VALUES ('$tipo','$id_clt','$data_cad', '$user_cad', '$motivo')");
    $id_doc = mysql_insert_id();
    $acao = 2;
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}

$motivo = '';
$data_ad = date("d/m/Y");

if (empty($acao) && empty($id_doc)) {
    ?>
    <html>
        <head>
            <title>:: Intranet :: Medidas Disciplinares</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <link rel="shortcut icon" href="../../favicon.ico" />
            <link href="../../net1.css" rel="stylesheet" type="text/css" />
            <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
            <link href="../../favicon.ico" rel="shortcut icon" />
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
            <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>


            <script>
                $(function() {
                    $("#data_ad").mask("99/99/9999");
//                       $('#data_ad').datepicker({
//                            dateFormat: 'dd/mm/yy',
//                            changeMonth: true,
//                            changeYear: true
//                        }); 
                });
            </script>
            <style>
                .data{width: 80px;}
                .colEsq{
                    float: left;
                    width: 55%;
                    margin-top: -10px;
                }
                fieldset{
                    margin-top: 10px;
                }
                fieldset legend{
                    font-family: 'Exo 2', sans-serif;
                    font-size: 16px!important;
                    font-weight: bold;
                }
                .first{
                    vertical-align: 0!important;
                }
                .first-2{
                    vertical-align: 0!important;
                }
            </style>
        </head>
        <body class="novaintra">
            <div id="content">
                <form action="" method="post" name="form1">
                    <div id="head">
                        <img src="../../imagens/logomaster<?php echo $row_regiao['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                        <div class="fleft">
                            <h2>Medidas Disciplinares</h2>
                        </div>
                    </div>
                    <fieldset>
                        <legend>Dados da advertência</legend>
                        <p><label class='first'>Motivo:</label><textarea style="width: 96%; height: 450px; margin-left: 2%;" name="motivo"></textarea></p>
                        <p><label class='first'>Data da Medida Disciplinar:</label><input type="text" name="data" id="data_ad" class="date hasDatepicker" value="<?= $data_ad ?>" /></p>
                        <p>
                            <label class="first">Tipo de Medida Disciplinar:</label>
                            <select name="medida_disciplinar" id="medida_disciplinar">
                                <option>--Selecione--</option>
                                <option value="10">Advertência</option>
                                <option value="9">Suspensão</option>
                            </select>
                            </p>
                        <p class="controls">
                            <input type="submit" name="cadastrar" id="cadastrar" value="Cadastrar Medida Disciplinare">
                        </p>
                    </fieldset>
                    <input type="hidden" name="acao" id="acao" value="1" />
                </form>
            </div>
        </body>
    </html>
<?php
}
if (!empty($id_doc) && $acao == 2) {
    $regiao = $row_regiao['regiao'];

    $qr_advertencia = mysql_query("SELECT a.*, b.motivo, b.data FROM rh_clt as a INNER JOIN rh_doc_status as b ON a.id_clt = b.id_clt WHERE b.id_doc = $id_doc");
    $row = mysql_fetch_array($qr_advertencia);

    $ano = substr($row['data'], 0, 4);
    $mes = intval(substr($row['data'], 5, 2));
    $dia = substr($row['data'], 8, 2);
    $meses = array('', 'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO', 'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO');
    ?>
    <html>
        <head>
            <title>:: Intranet :: Medidas Disciplinares</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <link rel="shortcut icon" href="../../favicon.ico" />
            <link href="../net1.css" rel="stylesheet" type="text/css" />
            <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
            <link href="../favicon.ico" rel="shortcut icon" />
            <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
            <script src="../js/global.js" type="text/javascript"></script>
            <style>
                .data{width: 80px;}
                .colEsq{
                    float: left;
                    width: 55%;
                    margin-top: -10px;
                }
                fieldset{
                    margin-top: 10px;
                }
                fieldset legend{
                    font-family: 'Exo 2', sans-serif;
                    font-size: 16px!important;
                    font-weight: bold;
                }
                .first{
                    vertical-align: 0!important;
                }
                .first-2{
                    vertical-align: 0!important;
                }
            </style>
        </head>
        <body class="novaintra">
            <div id="content">
                <table width="700" border="0" align="center" cellpadding="5" cellspacing="0" class="bordaescura1px">
                    <tr>
                        <td width="680" colspan="2" align="center" bgcolor="#FFFFFF" class="campotexto">
                            <img src="../../imagens/logomaster<?php echo $row_regiao['id_master']; ?>.gif"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" bgcolor="#D6D6D6"><span class="campotexto"><strong><br />
                                </strong></span><span class="title"><strong>Advert&ecirc;ncia</strong></span><span class="campotexto"><strong><br />
                                    <br />
                                </strong></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" bgcolor="#FFFFFF"> <blockquote>
                                <p class="linha">&nbsp;</p>
                                <p class="style2"><?php print "$regiao, $dia DE $meses[$mes] DE $ano"; ?></p>
                                <p class="linha">A(o) Sr.(a) <span class="style2"><?= $row['nome'] ?></span></p>
                                <p class="linha">Pela presente fica  V.S&ordf;. advertido,em raz&atilde;o da(s) irregularidade(s) abaixo discriminada(s): <br> <br><?= $row['motivo']; //$motivo;  ?><br />
                                </p>
                                <p align="justify" class="linha">Esclarecemos que a  reitera&ccedil;&atilde;o no cometimento de irregularidades autorizam a rescis&atilde;o do 
                                    contrato de trabalho  por justa causa, raz&atilde;o pela qual esperamos que V.S&ordf;. procure evitar 
                                    a reincid&ecirc;ncia em  procedimentos an&aacute;logos, para que n&atilde;o tenhamos, no futuro, de tomar as 
                                    en&eacute;rgicas medidas  que nos s&atilde;o facultadas por lei.</p>
                                <p>&nbsp;</p>
                                <p class="linha">Ciente  ____/____/______</p>
                                <p>&nbsp;</p>
                                <p class="linha"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;____________________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br />
                                    <span class="style2"><?= $row['nome'] ?><br />
    <?= $row['ctps'] ?><br />
    <?= $row_curso['nome'] ?></span></p>
                                <p>&nbsp;</p>
                                <p class="linha">____________________________________________<br />
                                    Assinatura do Empregador </p>
                                <p class="linha"><span class="linha"><br />
                                    </span> </p>
                            </blockquote>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </body>
    </html>
<?php } ?>