<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.f71iabassp.com/intranet/login.php?entre=true");
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
    header("Content-Disposition: attachment; filename=participantes-vr.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VR</title>";
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
include "../../../classes/LogClass.php";

$log = new Log();

function funcao_calc_dias($id_clt) {
    return 30;
}

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$objAlimentaca = new ValeAlimentacaoRefeicaoClass();
$objAlimentacaItem = new ValeAlimentacaoRefeicaoRelatorioClass();

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Novo Pedido");
$breadcrumb_pages = array("Gest�o de RH" => "../../../rh/principalrh.php", "Benef�cios" => "../", "Vale Refei��o" => "index.php");

if (isset($_REQUEST['filtrar'])) {
    $id_regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $id_tipo = $_REQUEST['id_tipo'];

    $result = $objAlimentacaItem->getListaCltsVR($id_regiao, $id_tipo);
    $resultS = $objAlimentacaItem->getSindicatos($id_regiao, "refeicao");

    $lista = array();
    $listaS = array();

    while ($row = mysql_fetch_assoc($result)) {
        $lista[] = $row;
    }

    while ($rowS = mysql_fetch_assoc($resultS)) {
        $listaS[] = $rowS;
    }
    
    //CLTS SEM FUN��O
    $cltsSemFuncao = $objAlimentacaItem->getCltsSemFuncao($id_regiao);
    $totSemFuncao = mysql_num_rows($cltsSemFuncao);
    
    //CLTS SEM UNIDADE
    $cltsSemUnidade = $objAlimentacaItem->getCltsSemUnidade($id_regiao);
    $totSemUnidade = mysql_num_rows($cltsSemUnidade);
    
    //CLTS SEM SINDICATO
    $cltsSemSindicato = $objAlimentacaItem->getCltsSemSindicato($id_regiao);
    $totSemSindicato = mysql_num_rows($cltsSemSindicato);
    
    //CLTS SEM HOR�RIO
    $cltsSemHorario = $objAlimentacaItem->getCltsSemHorario($id_regiao);
    $totSemHorario = mysql_num_rows($cltsSemHorario);
    
    //VERIFICA SE J� TEM PEDIDO
    $pedidosMes = $objAlimentacaItem->getPedidoNoMes($mes, $ano, $projeto, $id_regiao, 2);
    $totPedido = mysql_num_rows($pedidosMes);
    
    if(($totSemUnidade > 0) || ($totSemFuncao > 0) || ($totSemSindicato > 0) || ($totSemHorario > 0) || ($totPedido > 0)){
        $gera_disable = "disabled";
    }
}

if (isset($_REQUEST['salvar'])) {
    $array_pedido = array(
        'mes' => $_REQUEST['mes'],
        'ano' => $_REQUEST['ano'],
        'projeto' => $_REQUEST['projeto'],
        'id_regiao' => $_REQUEST['regiao'],
        'user' => $usuario['id_funcionario'],
        'categoria_vale' => 2, // 2 = refeicao
        'status' => 1,
        'data_entrega' => ConverteData($_REQUEST['data_entrega'], 'Y-m-d'),
        'data_credito' => ConverteData($_REQUEST['data_credito'], 'Y-m-d'),
    );

    $id_pedido_salvo = $objAlimentaca->salvar($array_pedido);
    $log->gravaLog('Benef�cios - Vale Refei��o', "Pedido Gerado: ID{$id_pedido_salvo}");

    $qtd_zerado = 0;
    $qtd_ok = 0;

    foreach ($_REQUEST['id_clt'] as $key => $id_clt) {
        if ($_REQUEST['valor'][$key] > 0) {
            $dias_uteis = funcao_calc_dias($id_clt);
            $valor = $_REQUEST['valor'][$key] / 30 * $dias_uteis;
            $qtd_ok++;

            $array_relatorio = array(
                'id_va_pedido' => $id_pedido_salvo,
                'id_clt' => $id_clt,
                'dias_uteis' => $dias_uteis,
                'va_valor_mes' => $valor
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

    if ($id_pedido_salvo) {
        $msgYes = true;
    }

    if ($qtd_zerado > 0) {
        $msgNo = true;
        $msg_typeNo = "warning";
        $msg_textNo = "{$qtd_zerado} participante(s) n�o cadastrado(s) no PEDIDO, pois o valor est� ZERADO";
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

$id_regiao  = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$opt_tipos  = $_REQUEST['id_tipo'];
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
        <title>:: Intranet :: Gest�o de RH</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Vale Refei��o</small></h2></div>

                    <!--
                    FORAM CRIADOS 2 ALERTS POIS VAO EXISTIR CASOS
                    DE TRAZER DOIS ALERTS AO MESMO TEMPO
                    -->

                    <?php if ($msgNo) { ?>
                        <div class="alert alert-<?php echo $msg_typeNo; ?>">
                            <button type="button" class="close" data-dismiss="alert">�</button>
                            <p><?php echo $msg_textNo; ?></p>
                        </div>
                    <?php } ?>

                    <?php if ($msgYes) { ?>
                        <div class="alert alert-<?php echo $msg_typeYes; ?>">
                            <button type="button" class="close" data-dismiss="alert">�</button>
                            <p><?php echo $msg_textYes; ?></p>
                        </div>
                    <?php } ?>

                    <form method="post" action="#" id="form1" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-heading">Consulta Funcion�rios</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Regi�o</label>
                                    <div class="col-lg-4 selectpicher">
                                        <?= montaSelect(getRegioes(), $id_regiao, 'name="regiao" id="regiao" class="form-control "'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-4 selectpicher">
                                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?= $projeto; ?>" />
                                        <?= montaSelect(getProjetos(), $projeto, 'name="projeto" id="projeto" class="form-control "'); ?>
                                    </div>
                                </div>
                                <!--                                <div class="form-group">
                                                                    <label class="col-lg-2 control-label">Tipo</label>
                                                                    <div class="col-lg-4 selectpicher">
                                <?= montaSelect($tipos, $opt_tipos, 'name="id_tipo" class="form-control "'); ?>
                                                                    </div>
                                                                </div>-->                                
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Compet�cia</label>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(mesesArray(), $opt_mes, 'name="mes" class="form-control "'); ?>
                                    </div>
                                    <div class="col-lg-2 selectpicher">
                                        <?= montaSelect(anosArray('2016', date('Y') + 1), $opt_ano, 'name="ano" class="form-control "'); ?>
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
                                <div class="pull-left">
                                    <span class="badge bg-info">FERIAS</span>
                                    <span class="badge bg-danger">FALTA</span> 
                                    <span class="badge bg-warning"> EVENTO</span>
                                </div>
                                <a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a>
                                <button type="submit" name="filtrar" value="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>

                        <?php if (count($listaS) > 0) { ?> 
                            <div class="alert alert-danger">
                                <h4>Sindicatos sem valor de refei��o:</h4>
                                <ul>
                                    <?php foreach ($listaS as $keyS => $valueS) { ?>
                                        <li><?php echo "{$valueS['id_sindicato']} - {$valueS['nome']}"; ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?> 

                        <?php if (count($lista) > 0) { ?>
                            <div class="panel panel-default">
                                <div id="relatorio_exp">
                                    <table class="table table-striped table-hover text-sm tablesorter" id="tbRelatorio">
                                        <thead>
                                            <tr>
                                                <th class="text-center <?php echo $tblImport; ?>">
                                                    <input type="checkbox" id="checkAll" data-name="ativo" checked>
                                                </th>
                                                <th>Matr�cula</th>
                                                <?php if($_COOKIE['logado'] == 353){ ?>
                                                <th>ID</th>
                                                <?php } ?>
                                                <th>Nome</th>
                                                <th>Situa��o</th>
                                                <th class="sorter-shortDate dateFormat-ddmmyyyy">Entrada</th>
                                                <th>Nascimento</th>
                                                <th>CPF</th>
                                                <th>Cargo</th>
                                                <?php if($_COOKIE['logado'] == 353){ ?>
                                                <th>Sindicato</th>
                                                <?php } ?>
                                                <?php if($_COOKIE['logado'] == 353){ ?>
                                                <th>Valor Cheio</th>
                                                <?php } ?>
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
                                                
                                                if ($_COOKIE['logado'] == 353) {
                                                    if ($value['id_clt'] == $_COOKIE['clt']) {
                                                        print_array($value);
                                                    }
                                                }
                                                
                                                //CONDI��O PARA M�DICOS
                                                $fnc = strtoupper(RemoveAcentos($value['nome_funcao']));

                                                if (($value['horas_semanais'] > 20 || $id_regiao == 1) || (strstr($fnc, "MEDICO"))) {

                                                    $debug = false;
                                                    $valor_antes = (float) $value['valor'];
                                                    
                                                    if($value['val_dia'] > 0){
                                                        $valor_cheio = $value['val_dia'];
                                                    }else{
                                                        $valor_cheio = $value['valor'];
                                                    }
                                                    
                                                    if ($debug) {
                                                        if ($_COOKIE['logado'] == 353) {
                                                            if ($value['id_clt'] == 3287) {
                                                                echo "ANTES: {$valor_antes}<br>";                                                                
                                                            }
                                                        }
                                                    }
                                                    
                                                    //VALOR FIXO
                                                    if($value['valor_vr_fixo'] > 0){
                                                        $value['valor'] = $value['valor_vr_fixo'];
                                                        
                                                    }else{
                                                        //PESSOAS QUE RECEBEM VR POR DIA �TIL
                                                        if($value['val_dia'] > 0){
                                                            $value['valor'] = $objAlimentaca->calculaVRDia($value['id_clt'], $value['val_dia'], $_REQUEST['ano'], $_REQUEST['mes'], $value['status'], $debug);
                                                        }else{
                                                            $value['valor'] = $objAlimentaca->calculaVR($value['id_clt'], $value['valor'], $_REQUEST['ano'], $_REQUEST['mes'], $value['status'], $debug);
                                                        }
                                                    }
                                                    
                                                    $faltasStatus = $objAlimentaca->status_falta;
                                                    $feriasStatus = $objAlimentaca->status_ferias;
                                                    $eventoStatus = $objAlimentaca->status_evento;
                                                    
                                                    if($value['id_clt'] == 4039){
                                                        $value['valor'] = 440;
                                                    }
                                                    
                                                    if ($value['valor'] > 0) {
                                                        $tot_participantes_com_valor++;
                                                    }
                                                    
                                                    $valor_depois = $value['valor'];                                                   

                                                    if (($faltasStatus) || ($feriasStatus) || ($eventoStatus)) {
                                                        $cor_texto = "text-danger";
                                                    }

                                                    $tot_participantes++;
                                                    $tot_valor += $value['valor'];

//                                                    if ($unidade != $value['id_unidade']) {
//                                                        $unidade = $value['id_unidade'];
//                                                        echo "<tr><th colspan='9' class='text-center'>{$value['nome_unidade']}</th></tr>";
//                                                    }
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
                                                        <?php if($_COOKIE['logado'] == 353){ ?>
                                                        <td>
                                                            <?= $value['id_clt'] ?>                                                    
                                                        </td>  
                                                        <?php } ?>
                                                        <td title="<?php echo $value['id_clt']; ?>">
                                                            <?= $value['nome'] ?> 
                                                            <?php if ($feriasStatus) { ?>
                                                                <span class="badge bg-info">FERIAS</span>
                                                            <?php } ?>
                                                            <?php if ($faltasStatus) { ?>
                                                                <span class="badge bg-danger">FALTA</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            if ($value['status'] != 10) {
                                                                echo "<span class='label label-warning'>{$value['nome_status']}</span>";
                                                            } else {
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
                                                        <?php if($_COOKIE['logado'] == 353){ ?>
                                                        <td>
                                                            <?= $value['sindicato'] ?>                                                    
                                                        </td>  
                                                        <?php } ?>
                                                        <?php if($_COOKIE['logado'] == 353){ ?>
                                                        <td>
                                                            <?= $valor_cheio ?>                                                    
                                                        </td>  
                                                        <?php } ?>
                                                        <td class="text-right action_val <?php echo $cor_texto ?>" data-idclt="<?= $value['id_clt'] ?>" >
                                                            <a href="javascript:;" id="<?= $value['id_clt'] ?>_span"><?= number_format($value['valor'], 2, ',', '.') ?></a>
                                                            <input type="hidden" name="valor[]" class="valor_msk" value="<?= $value['valor'] ?>" id="<?= $value['id_clt'] ?>_valor">
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    unset($cor_texto);
                                                }
                                            }
                                            ?>
<!--                                            <tr>
                                                <td colspan="1" class="text-right"></td>
                                                <td colspan="2" class="text-right">Participantes: <strong><?php echo $tot_participantes; ?></td>
                                                <td colspan="2" class="text-right">Com valor: <strong><?php echo $tot_participantes_com_valor; ?></td>
                                                <td colspan="4" class="text-right">Valor total: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></td>
                                            </tr>-->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-left">
                                    Total de Participantes: <strong><?php echo $tot_participantes; ?></strong> | 
                                    Com valor: <strong><?php echo $tot_participantes_com_valor; ?></strong> | 
                                    Valor total: <strong><?php echo number_format($tot_valor, 2, ',', '.'); ?></strong>
                                </div>
                                <?php if (!$msgYes) { ?>
                                    <div class="panel-footer text-right">                                    
                                        <button type="submit" name="salvar" value="salvar" class="btn btn-success <?php echo $gera_disable; ?>"><i class="fa fa-floppy-o"></i> Gerar Pedido</button>                                                 
                                    </div>
                                <?php } ?>                       
                            </div>
                            
                            <?php if(($totSemFuncao > 0)){ ?>
                            <div class="alert alert-danger">                                                                
                                <?php if($totSemFuncao > 0){ ?>
                                <h4>Existe(m) <?php echo $totSemUnidade; ?> Clt(s) sem Fun��o:</h4>
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
                                
                            <?php if(($totSemHorario > 0)){ ?>
                            <div class="alert alert-danger">
                                <?php if($totSemHorario > 0){ ?>
                                <h4>Existe(m) <?php echo $totSemHorario; ?> Clt(s) sem Hor�rio:</h4>
                                <ul>
                                    <?php while($resH = mysql_fetch_assoc($cltsSemHorario)) { ?>
                                        <li><?php echo "{$resH['nome']}"; ?></li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        
                            <?php if(($totPedido > 0)){ ?>
                            <div class="alert alert-danger">
                                <?php if($totPedido > 0){ ?>
                                <p>J� Existe pedido salvo para esse m�s</p>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            
                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar</button>
                            <input type="hidden" id="data_xls" name="data_xls" value="">
                                                        

                            <?php
                        }
                        
                        if (isset($_REQUEST['salvar'])) { ?>
                            <a href="../controle.php?id=<?= $id_pedido_salvo ?>&tipo=2" name="download" value="download" class="btn btn-info"><i class="fa fa-download"></i> Download</a>                                                                  
                        <?php }

                        if ((!empty($_REQUEST['filtrar'])) && (count($lista) == 0)) {
                            ?>
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">�</button>
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