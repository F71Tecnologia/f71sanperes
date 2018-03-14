ss<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include "../../../classes/LogClass.php";
$log = new Log();

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
include("../../../classes/ValeAlimentacaoRefeicaoClass.php");
include("../../../classes/ValeAlimentacaoRefeicaoRelatorioClass.php");
include("../../../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objAlimentaca = new ValeAlimentacaoRefeicaoClass();
$objAlimentacaItem = new ValeAlimentacaoRefeicaoRelatorioClass();

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Novo Pedido");
$breadcrumb_pages = array("Gestão de RH" => "../../../rh/principalrh.php", "Benefícios" => "../", "Vale Alimentação" => "index.php");


$idVale = $_REQUEST['idVale'];
echo $idVale;
$result = $objAlimentacaItem->getListaCltsVAInclusao($idVale, $notIn);
print_array($result);
$resultS = $objAlimentacaItem->getSindicatos($regiao, "alimentacao");

if(isset($_REQUEST['salvar'])){
    $result = $objAlimentacaItem->getParticipantes($id_pedido, $id_unidade);
}

$lista = array();
$listaS = array();

while ($row = mysql_fetch_assoc($result)) {
    $lista[] = $row;
}

while ($rowS = mysql_fetch_assoc($resultS)) {
    $listaS[] = $rowS;
}

//CLTS SEM FUNÇÃO
$cltsSemFuncao = $objAlimentacaItem->getCltsSemFuncao($regiao);
$totSemFuncao = mysql_num_rows($cltsSemFuncao);

//CLTS SEM UNIDADE
$cltsSemUnidade = $objAlimentacaItem->getCltsSemUnidade($regiao);
$totSemUnidade = mysql_num_rows($cltsSemUnidade);

//CLTS SEM SINDICATO
$cltsSemSindicato = $objAlimentacaItem->getCltsSemSindicato($regiao);
$totSemSindicato = mysql_num_rows($cltsSemSindicato);

if(($totSemUnidade > 0) || ($totSemFuncao > 0) || ($totSemSindicato > 0)){
    $gera_disable = "disabled";
}


if (isset($_REQUEST['salvar'])) {
    $array_pedido = array(
        'mes' => $_REQUEST['mes'],
        'ano' => $_REQUEST['ano'],
        'projeto' => $_REQUEST['projeto'],
        'id_regiao' => $_REQUEST['regiao'],
        'user' => $usuario['id_funcionario'],
        'categoria_vale' => 1,
        'status' => 1,
        'data_entrega' => ConverteData($_REQUEST['data_entrega'],'Y-m-d'),
        'data_credito' => ConverteData($_REQUEST['data_credito'],'Y-m-d'),
    );
    
    $id_pedido_salvo = $objAlimentaca->salvar($array_pedido);
    $log->gravaLog('Benefícios - Vale Alimentação', "Pedido Gerado: ID{$id_pedido_salvo}");
    
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
        } else {
            $qtd_zerado++;
        }
    }
    
    if ($cad) {
        $msgYes = true;
        $msg_typeYes = "success";
        $msg_textYes = "{$qtd_ok} participante(s) cadastrado(s) com sucesso!";
    }
    
    if ($qtd_zerado > 0) {
        $msgNo = true;
        $msg_typeNo = "warning";
        $msg_textNo = "{$qtd_zerado} participante(s) não cadastrado(s) no PEDIDO, pois o valor está ZERADO";
    }
    
    if ($msgYes) {
        $tblImport = "hidden";
    }
}

if (isset($_REQUEST['download'])) {
// download do file
}

$query = "SELECT * FROM rh_va_tipos WHERE status = 1;";
$result = mysql_query($query);

while ($row1 = mysql_fetch_assoc($result)) {
    $tipos[$row1['id_va_tipos']] = $row1['nome_tipo'];
}

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$opt_tipos = $_REQUEST['id_tipo'];
$data_entrega = $_REQUEST['data_entrega'];
$data_credito = $_REQUEST['data_credito'];
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Vale Alimentação</small></h2></div>

                    <!--
                    FORAM CRIADOS 2 ALERTS POIS VAO EXISTIR CASOS
                    DE TRAZER DOIS ALERTS AO MESMO TEMPO
                    -->

                    <?php if ($msgNo) { ?>
                        <div class="alert alert-<?php echo $msg_typeNo; ?>">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <p><?php echo $msg_textNo; ?></p>
                        </div>
                    <?php } ?>

                    <?php if ($msgYes) { ?>
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
                                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?= $projeto; ?>" />
                                        <?= montaSelect(getProjetos($regiao), $projeto, 'name="projeto" id="projeto" class="form-control "'); ?>
                                    </div>
                                </div>
                                <!--                                <div class="form-group">
                                                                    <label class="col-lg-2 control-label">Tipo</label>
                                                                    <div class="col-lg-4 selectpicher">
                                <?= montaSelect($tipos, $opt_tipos, 'name="id_tipo" class="form-control "'); ?>
                                                                    </div>
                                                                </div>                                -->
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Competêcia</label>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(mesesArray(), $opt_mes, 'name="mes" class="form-control "'); ?>
                                    </div>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(anosArray('2016', date('Y')), $opt_ano, 'name="ano" class="form-control "'); ?>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Data de Entrega</label>
                                    <div class="col-lg-2 selectpicher">
                                        <input type="text" name="data_entrega" class="form-control data" value="<?= $data_entrega ?>">
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Data para Credito</label>
                                    <div class="col-lg-2 selectpicher">
                                        <input type="text" name="data_credito" class="form-control data" value="<?= $data_credito ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a>
                                <button type="submit" name="filtrar" value="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>

                        <?php if (count($listaS) > 0) { ?> 
                            <div class="alert alert-danger">
                                <h4>Sindicatos sem valor de alimentação:</h4>
                                <ul>
                                    <?php foreach ($listaS as $keyS => $valueS) { ?>
                                        <li><?php echo "{$valueS['id_sindicato']} - {$valueS['nome']}"; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?> 
                                                    
                            <div class="panel panel-default">
                                <div id="relatorio_exp">
                                    <table class="table table-striped text-sm tablesorter" id="tbRelatorio">
                                        <thead>
                                            <tr>
                                                <th class="text-center <?php echo $tblImport; ?>">
                                                    <input type="checkbox" id="checkAll" data-name="ativo" checked>
                                                </th>
                                                <th>Matrícula</th>
                                                <th>Nome</th>
                                                <th class="sorter-shortDate dateFormat-ddmmyyyy">Entrada</th>
                                                <th>Nascimento</th>
                                                <th>CPF</th>
                                                <th>Unidade</th>
                                                <th>Cargo</th>
                                                <th>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $tot_participantes = 0;
                                            $tot_participantes_com_valor = 0;
                                            $tot_valor = 0;

                                            foreach ($lista as $key => $value) {

                                                $tot_participantes++;
                                                $tot_valor += $value['valor'];

                                                if ($value['valor'] > 0) {
                                                    $tot_participantes_com_valor++;
                                                }                                                
                                                
                                                ?>
                                                <tr>
                                                    <td class="text-center <?php echo $tblImport; ?>">
                                                        <?php if (!$msgYes) { ?>
                                                            <input type="checkbox" name="ativo[]" value="<?= $value['id_clt'] ?>" checked class="chk">
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= $value['matricula'] ?>                                                        
                                                        <input type="hidden" name="id_clt[]" value="<?= $value['id_clt'] ?>">
                                                    </td>
                                                    <td title="<?= $value['id_clt'] ?>">
                                                        <?= $value['nome'] ?>                                                        
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
                                                        <?= $value['nome_unidade'] ?>                                                        
                                                    </td>
                                                    <td>
                                                        <?= $value['nome_funcao'] ?>                                                        
                                                    </td>
                                                    <td class="text-right action_val" data-idclt="<?= $value['id_clt'] ?>">
                                                        <a href="javascript:;" id="<?= $value['id_clt'] ?>_span"><?= number_format($value['valor'], 2, ',', '.') ?></a>
                                                        <input type="hidden" name="valor[]" class="valor_msk" value="<?= $value['valor'] ?>" id="<?= $value['id_clt'] ?>_valor">
                                                    </td>
                                                </tr>
                                            <?php } ?>
<!--                                            <tr>
                                                <td colspan="1" class="text-right"></td>
                                                <td colspan="2" class="text-right">Total de Participantes: <strong><?php echo $tot_participantes; ?></td>
                                                <td colspan="2" class="text-right">Com valor: <strong><?php echo $tot_participantes_com_valor; ?></td>
                                                <td colspan="4" class="text-right">Valor total: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></td>
                                            </tr>-->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-footer text-right">
                                    <div class="pull-left">
                                        Total de Participantes: <strong><?php echo $tot_participantes; ?></strong> | 
                                        Com valor: <strong><?php echo $tot_participantes_com_valor; ?></strong> | 
                                        Valor total: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></strong>
                                    </div>
                                    <?php if (!$msgYes) { ?>
                                        <button type="submit" name="salvar" value="salvar" class="btn btn-success <?php echo $gera_disable; ?>"><i class="fa fa-floppy-o"></i> Salvar</button>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <?php if(($totSemFuncao > 0)){ ?>
                            <div class="alert alert-danger">                                                                
                                <?php if($totSemFuncao > 0){ ?>
                                <h4>Existe(m) <?php echo $totSemUnidade; ?> Clt(s) sem Função:</h4>
                                <ul>
                                    <?php while($resF = mysql_fetch_assoc($cltsSemFuncao)) { ?>
                                        <li><?php echo "{$resF['nome']}"; ?></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?> 
                            </div>
                            <?php } ?>
                            
                            <?php if(($totSemUnidade > 0)){ ?>
                            <div class="alert alert-danger">     
                                <?php if($totSemUnidade > 0){ ?>
                                <h4>Existe(m) <?php echo $totSemUnidade; ?> Clt(s) sem Unidade:</h4>
                                <ul>
                                    <?php while($resU = mysql_fetch_assoc($cltsSemUnidade)) { ?>
                                        <li><?php echo "{$resU['nome']}"; ?></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </div>
                            <?php } ?>
                                
                            <?php if(($totSemSindicato > 0)){ ?>
                            <div class="alert alert-danger">
                                <?php if($totSemSindicato > 0){ ?>
                                <h4>Existe(m) <?php echo $totSemSindicato; ?> Clt(s) sem Sindicato:</h4>
                                <ul>
                                    <?php while($resS = mysql_fetch_assoc($cltsSemSindicato)) { ?>
                                        <li><?php echo "{$resS['nome']}"; ?></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </div>
                            <?php } ?>

                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar</button>
                            <input type="hidden" id="data_xls" name="data_xls" value="">                                                    

                        <?php
                        
                        if (isset($_REQUEST['salvar'])) { ?>
                            <a href="../controle.php?id=<?= $id_pedido_salvo ?>&tipo=1" name="download" value="download" class="btn btn-info"><i class="fa fa-download"></i> Download</a>                                                                  
                        <?php                         
                        }
                        
                        if ((!empty($_REQUEST['filtrar'])) && (count($lista) == 0)) {
                            ?>
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
        <script src="../../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../../jquery/tablesorte/jquery.tablesorter.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>                
        <!--<script src="../../../resources/js/rh/beneficios/vale_alimentacao.js" type="text/javascript"></script>-->
        <script>
            $(document).ready(function () {
                $("#regiao").ajaxGetJson("../../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });

            $(function () {                
                $(".valor_msk").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'', decimal:'.'});
                
                $("table").tablesorter({
                    dateFormat : "mmddyyyy", // set the default date format

                    // or to change the format for specific columns, add the dateFormat to the headers option:
                    headers: {
                            0: { sorter: "shortDate" } //, dateFormat will parsed as the default above
                            // 1: { sorter: "shortDate", dateFormat: "ddmmyyyy" }, // set day first format; set using class names
                            // 2: { sorter: "shortDate", dateFormat: "yyyymmdd" }  // set year first format; set using data attributes (jQuery data)
                    }
                });
                
                $("#exportarExcel").click(function () {
                    $("#relatorio_exp img:last-child").remove();

                    var html = $("#relatorio_exp").html();

                    $("#data_xls").val(html);
                    $("#form1").submit();
                });

                $("#checkAll").click(function () {
                    if ($("#checkAll").prop("checked")) {
                        $(".chk").prop("checked", true);
                    } else {
                        $(".chk").prop("checked", false);
                    }
                });
                
                $(".action_val").click(function (){
                    var id_clt = $(this).data("idclt");
                    var valor = $("#"+id_clt+"_valor");
                    var valor_txt = $("#"+id_clt+"_span");
                    
                    $(valor).attr("type", "text");
                    $(valor_txt).attr("class", "hidden");
                });                                
            });
        </script>
    </body>
</html>