<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);
    
    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");    
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=participantes-va.xls");    
    
    echo "\xEF\xBB\xBF";    
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VA</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

error_reporting(E_ALL);

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/ValeTransporteClass.php");
include("../../../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objTransporte = new ValeTransporteClass();

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Admitidos");
$breadcrumb_pages = array("Gestão de RH" => "../../../rh/principalrh.php", "Benefícios" => "../");

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['salvar'])) {
    $regiao = $_REQUEST['regiao'];
    $id_tipo = $_REQUEST['id_tipo'];
    
    $result = $objTransporte->getListaClts($regiao, null, true);
    
    $lista = array();
    
    while ($row = mysql_fetch_assoc($result)) {
        $lista[] = $row;
    }
}

if (isset($_REQUEST['salvar'])) {
    $array_pedido = array(
        'mes' => $_REQUEST['mes'],
        'ano' => $_REQUEST['ano'],
        'projeto' => $_REQUEST['projeto'],
        'id_regiao' => $_REQUEST['regiao'],
        'user' => $usuario['id_funcionario'],
        'categoria_vale' => 1,
        'status' => 1
    );
    
    $id_pedido_salvo = $objAlimentaca->salvar($array_pedido);
    
    $qtd_zerado = 0;
    $qtd_ok = 0;
    
    foreach ($_REQUEST['id_clt'] as $key => $id_clt) {
        if ($_REQUEST['valor'][$key] > 0) {
            $qtd_ok++;
            
            $array_relatorio = array(
                'id_va_pedido' => $id_pedido_salvo,
                'id_clt' => $id_clt,
                'dias_uteis' => 30,
                'va_valor_mes' => $_REQUEST['valor'][$key]
            );
            
            $cad = $objAlimentacaItem->salvar($array_relatorio);                
        }else{
            $qtd_zerado++;
        }
    }
    
    if($cad){
        $msgYes = true;
        $msg_typeYes = "success";
        $msg_textYes = "{$qtd_ok} participante(s) cadastrado(s) com sucesso!";
    }
    
    if($qtd_zerado > 0){
        $msgNo = true;
        $msg_typeNo = "warning";
        $msg_textNo = "{$qtd_zerado} participante(s) não cadastrado(s) no PEDIDO, pois o valor está ZERADO";
    }
    
    if($msgYes){
        $tblImport = "hidden";
    }
}

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$opt_tipos = $_REQUEST['id_tipo'];
$opt_mes = isset($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$opt_ano = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Admitidos</small></h2></div>
                    
                    <!--
                    FORAM CRIADOS 2 ALERTS POIS VAO EXISTIR CASOS
                    DE TRAZER DOIS ALERTS AO MESMO TEMPO
                    -->
                    
                    <?php if($msgNo){ ?>
                    <div class="alert alert-<?php echo $msg_typeNo; ?>">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <p><?php echo $msg_textNo; ?></p>
                    </div>
                    <?php } ?>
                    
                    <?php if($msgYes){ ?>
                    <div class="alert alert-<?php echo $msg_typeYes; ?>">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <p><?php echo $msg_textYes; ?></p>
                    </div>
                    <?php } ?>
                    
                    <form method="post" action="#" id="form1" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-heading">Consulta Funcionários</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Região</label>
                                    <div class="col-lg-4 selectpicher">
                                        <?= montaSelect(getRegioes(), $regiao, 'name="regiao" id="regiao" class="form-control "'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-4 selectpicher">
                                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?=$projeto; ?>" />
                                        <?= montaSelect(getProjetos($regiao), $projeto, 'name="projeto" id="projeto" class="form-control "'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <div class="pull-left">
                                    Relatório para listar participantes sem valor de VT
                                </div>
                                <a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a>
                                <button type="submit" name="filtrar" value="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                        
                        <?php if (count($lista) > 0) { ?>                                                        
                            <div class="panel panel-default">
                                <div id="relatorio_exp">
                                    <table class="table table-striped text-sm" id="tbRelatorio">
                                        <thead>
                                            <tr>
                                                <th>Matrícula</th>
                                                <th>Nome</th>
                                                <th>Nascimento</th>
                                                <th>CPF</th>                                                
                                                <th>Cargo</th>
                                                <th>&emsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $tot_participantes = 0;
                                            $tot_participantes_com_valor = 0;
                                            $tot_valor = 0;
                                            $unidade = "";
                                            
                                            foreach ($lista as $key => $value) {
                                                
                                                $tot_participantes++;
                                                $tot_valor += $value['valor'];
                                                
                                                if($value['valor'] > 0){
                                                    $tot_participantes_com_valor++;
                                                }
                                                
                                                if($unidade != $value['id_unidade']){
                                                    $unidade = $value['id_unidade'];
                                                    echo "<tr><th colspan='8' class='text-center'>{$value['nome_unidade']}</th></tr>";
                                                }
                                            ?>
                                            <tr id="participante_<?php echo $value['id_clt']; ?>">                                                
                                                <td class="text-center">
                                                    <?= $value['matricula'] ?>                                                        
                                                    <input type="hidden" name="id_clt[]" value="<?= $value['id_clt'] ?>">
                                                </td>
                                                <td>
                                                    <?= $value['nome'] ?>                                                        
                                                </td>
                                                <td>
                                                    <?= converteData($value['data_nasci'], 'd/m/Y') ?>                                                        
                                                </td>
                                                <td>
                                                    <?= $value['cpf'] ?>
                                                </td>
                                                <td>
                                                    <?= $value['nome_funcao'] ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="javascript:;" class="btn btn-warning btn-xs edit_vt" data-toggle="tooltip" title="Editar VT" data-key="<?php echo $value['id_clt']; ?>"><i class="fa fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Total de Participantes: <strong><?php echo $tot_participantes; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <?php if (isset($_REQUEST['salvar'])) { ?>
                                <button type="submit" name="download" value="download" class="btn btn-info disabled"><i class="fa fa-download"></i> Download</button>                                
                                <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar</button>
                                <input type="hidden" id="data_xls" name="data_xls" value="">
                            <?php } ?>
                        
                        <?php }
                        
                        if((!empty($_REQUEST['filtrar'])) && (count($lista) == 0)){ ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <p>Nenhum cadastrado encontrado</p>
                        </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <?php include_once '../../../template/footer.php'; ?>
        </div><!-- /.container -->

        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/tooltip.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>                
        <!--<script src="../../../resources/js/rh/beneficios/vale_alimentacao.js" type="text/javascript"></script>-->
        <script>
            $(document).ready(function () {
                $("#regiao").ajaxGetJson("../../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
            
            $(function () {
                $("#exportarExcel").click(function () {
                    $("#relatorio_exp img:last-child").remove();
                    
                    var html = $("#relatorio_exp").html();
                    
                    $("#data_xls").val(html);
                    $("#form1").submit();
                });
                
                $(".edit_vt").click(function(){
                    var key = $(this).data("key");
                    
                    $.post("edit_vt.php", {id: key}, function(data){
                        bootDialog(data,'Alteração de VT', true);
                    });
                });
                
                $("#checkAll").click(function(){
                    if($("#checkAll").prop("checked")){
                        $(".chk").prop("checked", true);
                    }else{
                        $(".chk").prop("checked", false);
                    }
                });
            });
        </script>
    </body>
</html>