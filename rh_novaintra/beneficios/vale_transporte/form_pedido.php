<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}
#teste
if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);
    
    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=participantes-vt.xls");
    
    echo "\xEF\xBB\xBF";    
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VT</title>";
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

include "../../../classes/LogClass.php";
$log = new Log();

function funcao_calc_dias($id_clt){
    return 30;
}

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objTransporte = new ValeTransporteClass();

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Novo Pedido");
$breadcrumb_pages = array("Gestão de RH" => "../../../rh/principalrh.php", "Benefícios" => "../", "Vale Transporte" => "index.php");

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['salvar'])) {
    $id_regiao = $_REQUEST['id_regiao'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    
    $result = $objTransporte->getListaClts($id_regiao);    
    
    $lista = array();
    
    while ($row = mysql_fetch_assoc($result)) {
        $lista[] = $row;
    }
    
    //VERIFICA SE JÁ TEM PEDIDO
    $pedidosMes = $objTransporte->getPedidoNoMes($mes, $ano, $id_regiao);
    $totPedido = mysql_num_rows($pedidosMes);
    
    if($totPedido > 0){
        $gera_disable = "disabled";
    }
}

if (isset($_REQUEST['salvar'])) {
    $array_pedido = array(
        'mes' => $_REQUEST['mes'],
        'ano' => $_REQUEST['ano'],
        'projeto' => $_REQUEST['id_projeto'],
        'id_regiao' => $_REQUEST['id_regiao'],
        'user' => $usuario['id_funcionario'],
        'status' => 1
    );
    
    $id_pedido_salvo = $objTransporte->salvar($array_pedido);
    $log->gravaLog('Benefícios - Vale Transporte', "Pedido Gerado: ID{$id_pedido_salvo}");
    
    $qtd_zerado = 0;
    $qtd_ok = 0;
    
    $objTransporte->setTable("rh_vt_relatorio");
    $objTransporte->setIdTable("id_vt_relatorio");
    
    foreach ($_REQUEST['ativo'] as $key => $id_clt) {                
        if ($_REQUEST['valor'][$key] > 0) {
            $dias_uteis = $_REQUEST['dias_vt'][$key];
            $valor = $_REQUEST['valor'][$key];
            $qtd_ok++;
            
            $array_relatorio = array(
                'id_vt_pedido' => $id_pedido_salvo,
                'id_clt' => $id_clt,
                'dias_uteis' => $dias_uteis,
                'vt_valor_diario' => $valor
            );
            
            $cad = $objTransporte->salvar($array_relatorio);
        }else{
            $qtd_zerado++;
        }
    }
    
    if($cad){
        $msgYes = true;
        $msg_typeYes = "success";
        $msg_textYes = "{$qtd_ok} participante(s) cadastrado(s) com sucesso!";
    }
    
    if($id_pedido_salvo){
        $msgYes = true;
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

if (isset($_REQUEST['download'])) {
    // download do file
}

$id_regiao = $_REQUEST['id_regiao'];
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Vale Transporte</small></h2></div>
                    
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
                                        <?= montaSelect(getRegioes(), $id_regiao, 'name="id_regiao" class="form-control "'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Competêcia</label>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(mesesArray(), $opt_mes, 'name="mes" class="form-control "'); ?>
                                    </div>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(anosArray('2016', date('Y') + 1), $opt_ano, 'name="ano" class="form-control "'); ?>
                                    </div>
                                </div>
                                <?php if(isset($_REQUEST['filtrar'])){ ?>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Dias Úteis</label>
                                    <div class="col-lg-4 selectpicher">
                                        <?php 
                                        echo $objTransporte->diasUteis($_REQUEST['mes'], $_REQUEST['ano']) . " dias";
                                        
                                        $feriados = $objTransporte->getFeriados($_REQUEST['mes'], $_REQUEST['ano'], false, true);
                                        
                                        if($feriados == 1){
                                            echo " ({$feriados} feriado)";
                                        }elseif($feriados > 1){
                                            echo " ({$feriados} feriados)";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="panel-footer text-right">
                                <div class="pull-left">
                                    <span class="badge bg-info">FERIAS</span>
                                    <span class="badge bg-danger">FALTA</span> 
                                    <span class="badge bg-warning"> EVENTO</span>
                                </div>
                                <a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a>                                
                                <button type="submit" name="filtrar" value="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                        
                        <?php if (count($lista) > 0) { ?>
                            <div class="panel panel-default">
                                <div id="relatorio_exp">
                                    <table class="table table-striped table-hover text-sm tablesorter">
                                        <thead>
                                            <tr>
                                                <th class="text-center <?php echo $tblImport; ?>">
                                                    <input type="checkbox" id="checkAll" checked data-name="ativo">
                                                </th>
                                                <th>Matrícula</th>
                                                <th>Nome</th>
                                                <th>Situação</th>
                                                <th class="sorter-shortDate dateFormat-ddmmyyyy">Entrada</th>
                                                <th class="sorter-shortDate dateFormat-ddmmyyyy">Nascimento</th>
                                                <th>CPF</th>                                                
                                                <th>Cargo</th>                                                
                                                <th>Dias</th>                                                
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $tot_participantes = 0;
                                            $tot_participantes_com_valor = 0;
                                            $tot_valor = 0;
                                            $unidade = "";
                                            
                                            foreach ($lista as $key => $value) {
                                                if($_COOKIE['logado'] == 353){
//                                                    print_array($value);
                                                }
                                                
                                                $debug = false;                                                                                                                                                
                                                $valor_antes = (float) $value['valor'];
                                                
                                                if($debug){
                                                    if($_COOKIE['logado'] == 158){
                                                        if($value['id_clt'] == 2071){
                                                            echo "ANTES: {$valor_antes}<br>";
                                                        }
                                                    }
                                                }
                                                
                                                $value['valor'] = $objTransporte->calculaVT($value['id_clt'], $_REQUEST['ano'], $_REQUEST['mes'], $debug);
                                                
                                                if($value['nome_funcao'] == "Estagiário"){
                                                    $value['valor'] = $objTransporte->calculaVTEstagiario($value['id_clt'], $_REQUEST['ano'], $_REQUEST['mes'], $debug);
                                                }
                                                
                                                $faltasStatus = $objTransporte->status_falta;
                                                $feriasStatus = $objTransporte->status_ferias;
                                                $eventoStatus = $objTransporte->status_evento;
                                                
                                                if($value['valor'] > 0){
                                                    $tot_participantes_com_valor++;
                                                }
                                                
                                                $valor_depois = $value['valor'];
                                                
                                                if($debug){
                                                    if($_COOKIE['logado'] == 158){
                                                        if($value['id_clt'] == 2071){
                                                            echo "ANTES: {$valor_depois}<br>";
                                                            echo "DEPOIS: {$valor_depois}<br>";
                                                        }
                                                    }
                                                }
                                                
                                                if(($faltasStatus) || ($feriasStatus) || ($eventoStatus)){
                                                    $cor_texto = "text-danger";
                                                }
                                                
                                                $tot_participantes++;
                                                $tot_valor += $value['valor'];
                                                
//                                                if($unidade != $value['id_unidade']){
//                                                    $unidade = $value['id_unidade'];
//                                                    echo "<tr><th colspan='9' class='text-center'>{$value['nome_unidade']}</th></tr>";
//                                                }
                                            ?>
                                            
                                            <tr>
                                                <td class="text-center <?php echo $tblImport; ?>">
                                                    <?php if(!$msgYes){ ?>
                                                    <input type="checkbox" name="ativo[]" value="<?= $value['id_clt'] ?>" checked class="chk">
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= $value['matricula'] ?>                                                    
                                                    <input type="hidden" name="id_clt[]" value="<?= $value['id_clt'] ?>">
                                                </td>
                                                <td title="<?php echo $value['id_clt']; ?>">
                                                    <?= $value['nome'] ?> 
                                                    <?php if($feriasStatus){ ?>
                                                    <span class="badge bg-info">FERIAS</span>
                                                    <?php } ?>
                                                    <?php if($faltasStatus){ ?>
                                                    <span class="badge bg-danger">FALTA</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if($value['status'] != 10){
                                                        echo "<span class='label label-warning'>{$value['nome_status']}</span>";
                                                    }else{
                                                        echo $value['nome_status'];
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= converteData($value['data_entrada'], 'd/m/Y') ?>                                                    
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
                                                <td>
                                                    <?php echo $objTransporte->diasvt; ?>
                                                </td>                                                
                                                <td class="text-right <?php echo $cor_texto; ?>">
                                                    <?= number_format($value['valor'], 2, ',', '.') ?>
                                                    <input type="hidden" name="valor[]" value="<?= $value['valor'] ?>">
                                                    <input type="hidden" name="dias_vt[]" value="<?= $objTransporte->diasvt ?>">
                                                </td>
                                            </tr>
                                            <?php
                                            unset($cor_texto);
                                            } ?>                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-left">
                                    Participantes: <strong><?php echo $tot_participantes; ?></strong> | 
                                    Com valor: <strong><?php echo $tot_participantes_com_valor; ?></strong> | 
                                    Valor total: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></strong>
                                </div>
                                <?php if(!$msgYes){ ?>
                                <div class="panel-footer text-right">                                    
                                    <!--<button type="button" name="cria_mov_vt" value="criar" class="btn btn-warning disabled" data-toggle="tooltip" data-placement="top" title="Desconto de Vale Transporte"><i class="fa fa-play-circle-o"></i> Criar Movimentos</button>-->
                                    <button type="submit" name="salvar" value="salvar" class="btn btn-success <?php echo $gera_disable; ?>"><i class="fa fa-floppy-o"></i> Salvar</button>                                    
                                </div>
                                <?php } ?>
                            </div>
                        
                            <?php if(($totPedido > 0)){ ?>
                            <div class="alert alert-danger">
                                <?php if($totPedido > 0){ ?>
                                <p>Já Existe pedido salvo para esse mês</p>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            
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
        <script src="../../../jquery/tablesorte/jquery.tablesorter.js"></script>
        <script src="../../../js/global.js"></script>
        <!--<script src="../../../resources/js/rh/beneficios/vale_alimentacao.js" type="text/javascript"></script>-->
        
        <script>
           $(function () {
                $("#exportarExcel").click(function (e) {
                    $("#relatorio_exp img:last-child").remove();
                    
                    var html = $("#relatorio_exp").html();                                        
                    
                    $("#data_xls").val(html); 
                    $("#form1").submit();
                });
                
                $("#checkAll").click(function(){
                    if($("#checkAll").prop("checked")){
                        $(".chk").prop("checked", true);
                    }else{
                        $(".chk").prop("checked", false);
                    }
                });
                
                $("table").tablesorter({
                    dateFormat : "mmddyyyy", // set the default date format

                    // or to change the format for specific columns, add the dateFormat to the headers option:
                    headers: {
                            0: { sorter: "shortDate" } //, dateFormat will parsed as the default above
                            // 1: { sorter: "shortDate", dateFormat: "ddmmyyyy" }, // set day first format; set using class names
                            // 2: { sorter: "shortDate", dateFormat: "yyyymmdd" }  // set year first format; set using data attributes (jQuery data)
                    }
                });
            });
        </script>
    </body>
</html>