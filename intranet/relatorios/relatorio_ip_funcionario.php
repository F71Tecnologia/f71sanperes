<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//$optRegiao = getRegioes();
$ACOES = new Acoes();

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "« Selecione o ano »"));

$id_regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $id_regiao;
$mesRI = (isset($_REQUEST['mes_inicio'])) ? $_REQUEST['mes_inicio'] : date('m');
$anoRI = (isset($_REQUEST['ano_inicio'])) ? $_REQUEST['ano_inicio'] : date('Y');
$mesRF = (isset($_REQUEST['mes_fim'])) ? $_REQUEST['mes_fim'] : date('m');
$anoRF = (isset($_REQUEST['ano_fim'])) ? $_REQUEST['ano_fim'] : date('Y');

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Log de Acesso Por IPS");
$breadcrumb_pages = array();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Log de Acesso Por IPS </title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Log de Acesso Por PIS</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Início</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mesRI, "id='mes_inicio' name='mes_inicio' class='form-control validate[custom[select]]'") ?>
                            </div>
                            <div class="col-sm-2">  
                                <?php echo montaSelect($anos, $anoRI, "id='ano_inicio' name='ano_inicio' class='form-control validate[custom[select]]'") ?>
                            </div>    
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Fim</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mesRF, "id='mes_fim' name='mes_fim' class='form-control validate[custom[select]]'") ?>
                            </div>
                            <div class="col-sm-2">
                                <?php echo montaSelect($anos, $anoRF, "id='ano_fim' name='ano_fim' class='form-control validate[custom[select]]'") ?>
                            </div>
                        </div>


                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>

                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-check"></span> Gerar</button>
                            </div>
                        </div> 
                        
                    <?php if(isset($_REQUEST['mes_inicio'])){
                $mesRINome = mesesArray($mesRI);
                $mesRFNome = mesesArray($mesRF);
                $queryAcesso = "SELECT id_user, ip, DATE_FORMAT(horario, '%d/%m/%Y %H:%i:%s') horario FROM log WHERE local = 'Login Principal' AND (MONTH(horario) >= {$mesRI} AND YEAR(horario) >= {$anoRI}) AND (MONTH(horario) <= {$mesRF} AND YEAR(horario) <= {$anoRF});";
                $queryAcesso = mysql_query($queryAcesso);
                while($rowAcesso = mysql_fetch_assoc($queryAcesso)){
                    //$myArray[$rowAcesso[id_user]][$rowAcesso[ip]]++;
                    $myArray[$rowAcesso[id_user]][$rowAcesso[ip]][] = $rowAcesso[horario];
                }
                $tabela = "<thead><tr><th colspan='2' style='width: 100%;'>Período de {$mesRINome} de {$anoRI} à {$mesRFNome} de {$anoRF} <label class='ExibirEsconderTudo noprint' style='float: right; margin-right: 20px; text-decoration: underline; cursor: pointer;'>Exibir/Esconder Tudo</label></th><tr><td colspan='2'></td></tr>";
                $query = mysql_query("SELECT id_funcionario, nome FROM funcionario A WHERE A.status_reg = 1 ORDER BY nome;");
                while($rowFunc = mysql_fetch_assoc($query)){
                    $linha = "";
                    if(count($myArray[$rowFunc[id_funcionario]]) > 0){
                        foreach($myArray[$rowFunc['id_funcionario']] AS $key => $v){
                            $qtd = count($v);
                            $data = ($qtd == 1) ? " - $v[0]" : '';
                            $linha .= "<tr class='hid {$rowFunc['id_funcionario']}'><td>{$key} <span class='blue'>{$data}</span></td><td>{$qtd} vez(es) acessado(s)</td></tr>";
                        }
                        $tabela .= "
                        <thead>
                            <tr>
                                <th style='width: 80%;'>{$rowFunc['nome']}</th>
                                <th style='width: 20%;'>
                                    ".count($myArray[$rowFunc['id_funcionario']])." Ip(s)
                                    <label class='func' data-id='{$rowFunc['id_funcionario']}' style='float: right; margin-right: 20px; text-decoration: underline; cursor: pointer;'>Exibir/Esconder</label>
                                </th>
                            </tr>
                        </thead>
                        <tr class='hid {$rowFunc['id_funcionario']}'>
                            <td colspan='2'></td>
                        </tr>
                        {$linha}
                        <td colspan='2'></td>";
                    }
                }
                ?>


                        <table class="table table-striped table-hover text-sm valign-middle" id="tabela">
                            <?php echo $tabela; ?>    
                        </table>
                    <?php } ?>
                </div>
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function () {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                $('.func').click(function () {
                    var id = $(this).data('id');
                    $('.' + id).toggle();
                });

                $('.ExibirEsconderTudo').click(function () {
                    $('.hid').toggle();
                });
            });
        </script>

    </body>
</html>
