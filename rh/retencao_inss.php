<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include("../classes/ValeTransporteClass.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

/**
 * @author Lucas Praxedes Serra (17/01/2017 - 15:36)
 * A PEDIDO DA PATY, AGORA É POSSÍVEL GERAR A CARTA DE RETENÇÃO MESMO QUE A FOLHA ESTEJA ABERTA
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'verFolha') {
    $idClt = $_REQUEST['id'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    
    if ($mes <= 12) {
        $sqlVerFolha = "SELECT *
                        FROM rh_folha_proc A
                        LEFT JOIN rh_folha B ON A.id_folha = B.id_folha
                        WHERE B.terceiro = 2 AND A.mes = $mes AND A.ano = $ano AND id_clt = $idClt LIMIT 1";
        $queryVerFolha = mysql_query($sqlVerFolha);
        $numVerFolha = mysql_num_rows($queryVerFolha);

        if ($numVerFolha) {
            $arrFolha = mysql_fetch_assoc($queryVerFolha);
            if ($arrFolha['status'] != 3) {
                echo json_encode('2');
            } else {
                echo json_encode('3');
            }
        } else {
            echo json_encode('1');
        }
    } else if ($mes == 16) {
        echo json_encode('3');
    }
    exit();
}

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Relatório de Participantes");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php");

$objTransporte = new ValeTransporteClass();

// nomes FUNCIONARIOS
$query = "SELECT * FROM ano_meses  ORDER BY num_mes";
$result = mysql_query($query);
$optFuncionarios[-1] = 'Selecione';
while ($row2 = mysql_fetch_assoc($result)) {
    $optFuncionarios[$row2['num_mes']] = $row2['nome_mes'];
}
$optFuncionarios[13] = '13º - Integral';
$optFuncionarios[15] = '13º - 2ª Parcela';
$optFuncionarios[16] = 'Férias';
        
 $id = $_GET['id'];
 $bol = $_GET['bol'];
?>
<!doctype html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <title>Relatório Log de Usuários</title>
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.10.2.min.js"></script>
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="<?= ($container_full) ? 'container-full' : 'container' ?>">
            <form action="retencao_inss_carta.php" method="post" class="form-horizontal top-margin1" name="form1" id="form1">                                                
                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Retenção INSS</div>
                    <div class="panel panel-body">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Mês</label>
                            <div class="col-sm-5">
                                <?= montaSelect($optFuncionarios, null, 'name="num_mes" id="num_mes" class="form-control validate[required,custom[select]]"'); ?>
                            </div>
                            <label for="" class="col-sm-2 control-label">Ano</label>
                            <div class="col-sm-2">
                                <select name="ano" id="ano" class="form-control">
                                   <option value="2012">2012</option>
                                   <option value="2013">2013</option>
                                   <option value="2014">2014</option>
                                   <option value="2015">2015</option>
                                   <option value="2016">2016</option>
                                   <option value="2017">2017</option>
                                   <option value="2018">2018</option>
                               </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />
                        <input type="hidden" name="id_clt_ret_inss" id="id_clt_ret_inss" value="<?php echo $id;?>" class="btn btn-primary" />
                        <input type="hidden"  name="id_bol_ret_inss" id="id_bol_ret_inss" value="<?php echo $bol;?>" class="btn btn-primary" />
                        <input type="hidden"  name="tipoFolha" id="tipoFolha" value="" class="btn btn-primary" />
                    </div>
                </div>
            </form>
            <?php include('../template/footer.php'); ?>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script>
        
            $(function(){
                
                $('#form1').submit(function(){
                    var mes = $('#num_mes').val();
                    var ano = $('#ano').val();
                    var id = $('#id_clt_ret_inss').val();
                    var r;
                    $.ajax({
                        url:'',
                        method:'post',
                        dataType:'json',
                        data: {method:'verFolha',id:id,mes:mes,ano:ano},
                        success: function (data) {
                            console.log(data);
                            if (data == 1) {
                                alert('Não existe folha na competência selecionada.');
                            } else if (data == 2) {
                                $('#tipoFolha').val(data);
                                alert('Você está gerando o documento baseado em uma folha aberta, os valores mostrados estão sujeitos a alteração até que a folha seja fechada.');
                            } else if (data == 3) {
                                $('#tipoFolha').val(data);
                            }
                            r = data;
                        }
                    });
                    console.log(r);
                    if (r == 1) {
                        return false;
                    } else if (r == 2) {
                        return false;
                    } else if (r == 3) {
                        return false;  
                    }
                });
            });
        
        </script>
    </body>
</html>
