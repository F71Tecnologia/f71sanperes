<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}
include("../../conn.php");
include("../../wfunction.php");

$usuario = carregaUsuario();

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
    $dia = (isset($_REQUEST['dia']))?$_REQUEST['dia']:'0';
    
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
    
    mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user,motivo,obs2) VALUES ('$tipo','$id_clt','$data_cad', '$user_cad', '$motivo','$dia')");
    $id_doc = mysql_insert_id();
    if($tipo == 10){
    $acao = 2;
    }
    if($tipo == 9){
        echo "<script>location.href='suspencao.php?clt=$id_clt&pro=$id_projeto&id_reg=$id_regiao&data={$_REQUEST['data']}&dia=$dia&obs=$motivo'</script>";
    }
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}

$motivo = '';
$data_ad = date("d/m/Y");

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cadastro de Medidas Disciplinares");
$breadcrumb_pages = array(
    "Lista Projetos" => "../../ver2.php", 
    "Visualizar Projeto" => "../../ver2.php?projeto={$id_projeto}", 
    "Lista Participantes" => "../../bolsista2.php?projeto={$id_projeto}", 
    "Visualizar Participante" => "../ver_clt2.php?pro={$id_projeto}&clt={$id_clt}",
    "Medidas Disciplinares" => "advertencia2.php?pro={$id_projeto}&clt={$id_clt}"
); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Cadastro de Medidas Disciplinares</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
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
    <body>
        <?php if (empty($acao) && empty($id_doc)) { ?>
            <?php include("../../template/navbar_default.php"); ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS - <small>Cadastro de Medidas Disciplinares</small></h2></div>
                    </div>
                </div>
                <form action="" method="post" name="form1" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-heading">Dados da advertência</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-12">Motivo:</label>
                                <div class="col-md-12">
                                    <textarea class="form-control" rows="10" name="motivo"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Data da Medida Disciplinar:</label>
                                <div class="col-md-2">
                                    <input type="text" name="data" id="data_ad" class="form-control date hasDatepicker" value="<?= $data_ad ?>" />
                                </div>
                                <label class="col-md-3 control-label">Tipo de Medida Disciplinar:</label>
                                <div class="col-md-2">
                                    <select name="medida_disciplinar" id="medida_disciplinar" class="form-control">
                                        <option>--Selecione--</option>
                                        <option value="10">Advertência</option>
                                        <option value="9">Suspensão</option>
                                    </select>
                                </div>
                                <label class="col-md-1 control-label p_dia hidden">Dias:</label>
                                <div class="col-md-1 p_dia hidden">
                                    <input name="dia" type="text" id="dia" maxlength="2" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <input type="hidden" name="acao" id="acao" value="1" />
                            <input type="submit" class="btn btn-success" name="cadastrar" id="cadastrar" value="Cadastrar Medida Disciplinare">
                        </div>
                    </div>
                </form>
                <?php include_once '../../template/footer.php'; ?>
            </div>
        <?php }
        if (!empty($id_doc) && $acao == 2) {
            $regiao = $row_regiao['regiao'];

            $qr_advertencia = mysql_query("SELECT a.*, b.motivo, b.data FROM rh_clt as a INNER JOIN rh_doc_status as b ON a.id_clt = b.id_clt WHERE b.id_doc = $id_doc");
            $row = mysql_fetch_array($qr_advertencia);
            $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = {$row['id_curso']}") or die(mysql_error()); 
            $row_curso = mysql_fetch_assoc($qr_curso);

            $ano = substr($row['data'], 0, 4);
            $mes = intval(substr($row['data'], 5, 2));
            $dia = substr($row['data'], 8, 2);
            $meses = array('', 'JANEIRO', 'FEVEREIRO', 'MARÇO', 'ABRIL', 'MAIO', 'JUNHO', 'JULHO', 'AGOSTO', 'SETEMBRO', 'OUTUBRO', 'NOVEMBRO', 'DEZEMBRO');
            ?>
            <table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" bgcolor="#D6D6D6">
                        <span class="title">
                            <h3>Advert&ecirc;ncia</h3>
                        </span>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td align="center" bgcolor="#FFFFFF"> 
                        <blockquote class="no-border">
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
                                <span class="style2">
                                    <?=$row['nome']?><br />
                                    <?= $row['campo1'] ?><br />
                                    <?= $row_curso['nome'] ?>
                                </span>
                            </p>
                            <p>&nbsp;</p>
                            <p class="linha">____________________________________________<br />
                                Assinatura do Empregador </p>
                            <p class="linha">
                                <span class="linha"><br /></span>
                            </p>
                        </blockquote>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
            </table>
        <?php } ?>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#data_ad").mask("99/99/9999");
//                       $('#data_ad').datepicker({
//                            dateFormat: 'dd/mm/yy',
//                            changeMonth: true,
//                            changeYear: true
//                        }); 
                $("#medida_disciplinar").click(function(){
                    var valor = $(this).val();
                    if(valor == 9){
                        $(".p_dia").removeClass("hidden");
                        $(".p_dia").removeClass("hidden");
                    }else{
                        $(".p_dia").addClass("hidden");
                        $(".p_dia").addClass("hidden");
                    }
                });
            });
        </script>
    </body>
</html>