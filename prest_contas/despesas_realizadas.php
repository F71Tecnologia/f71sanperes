<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include('../funcoes.php');
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/SaidaClass.php");
include("../classes/global.php");
include('../classes/pdf/fpdf.php');
include('../classes/mpdf54/mpdf.php');
include('../classes/imageToPdf.php');
require('../classes/fpdfi/fpdi.php');
include('../prestacoes/PrestacaoContas.class.php'); #mudar essa classe de diretorio

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

//ARRAY DE SAIDAS QUE NÃO SERA ENVIADO O COMPROVANTE
$saida_rpas = array(84938,84937,84936,84933,84931,84930,84928,84927,84926,84924,84923,84922,84919,
87847,87851,87850,87848,87845,87844,87846,87870,87856,87855,87853,87877,87861,87857,87869,
87876,87938,87867,87874,87866,87864,87863,87872,87879,87858,87860,87878,87871,
87880,87881,87882,87883,87884,87885,87886,87888,87890,87891,87892,87893,87895,87897,87899,
87900,87901,87902,87903,87905,87906,87955,87957,87961,87962,84935,84934,84932,84929,84925,
84921,84917,89778,89784,89785,89782,89372,89373,89374,89375,89376,89377,89378,89379,89380,89381,89382,
89383,89384,89385,89386,89387,89388,89390,89392,89393,89396,89459,89400,89402,89404,89406,89408,89410,
89412,89414,89417,89418,89419,89422,89423,89424,89425,89426,89427,89428,89429,89431,89434,89436,89458,
90372,90373,90374,90375,90376,90378,90380,90382,90383);

$master = $usuario['id_master'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$historico = false;
$dataMesIni = date("Y-m")."-31";
$erros = 0;
$idsErros = array();

class concat_pdf extends FPDI {
    var $files = array(); 

    function setFiles($files) {
        $this->files = $files;
    }

    function concat() {
        foreach ($this->files AS $file) {
            $ext = end(explode(".",$file));
            if(is_file($file) && $ext == "pdf"){
                $pagecount = $this->setSourceFile($file);
                for ($i = 1; $i <= $pagecount; $i++) {
                    $tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);  //AKI
                    $this->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                    @$this->useTemplate($tplidx);
                }
            }
        }
    }
}

if(isset($_REQUEST['projeto'])){
    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $nome_projeto = projetosId($_REQUEST['projeto']);
    $master_projeto = masterId($nome_projeto['id_master']);
    $regiao = $nome_projeto['id_regiao'];
    $bancoSave = $_REQUEST['banco'];
    $mes2d = sprintf("%02d",$_REQUEST['mes']); //mes com 2 digitos
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $mesShow = mesesArray($_REQUEST['mes']) . "/" .$_REQUEST['ano'];    
    $filtro = true;
    
    $historico = false;        
    
    if ( (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) ||  (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) ) {
        
        /*RECUPERANDO OS PROJETOS JA FINALIZADOS*/
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
        $qr_verifica = PrestacaoContas::getQueryVerifica("despesa", $dataMesRef, $dataMesIni);
        
        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();
        
        while($rowVeri = mysql_fetch_assoc($rs_verifica)){
            //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS
            if($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco){
                $btexportar = false;
                $projetosFaltante[$contErro]['nome'] = $rowVeri['projeto'];
                $projetosFaltante[$contErro]['banco'] = " Banco: ".$rowVeri['id_banco']." AG: ".$rowVeri['agencia']." CC: ".$rowVeri['conta'];
                $contErro ++;
            }elseif($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['id_banco'] == $id_banco){  //VERIFICA SE O ATUAL ESTÁ FINALIZADO
                $btfinalizar = false;
            }

            //VERIFICA SE SÓ TEM 1 E SE JA FOI FINALIZADO
            if($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null){
                $btfinalizar=false;
            }
            
            //PRESTAÇÕES FINALIZADAS PARA A EXPORTAÇÃO
            if($rowVeri['gerado_embr'] != null && $rowVeri['administracao'] == "0"){
                $finalizados[] = $rowVeri['id_prestacao'];
            }
            
            //CASO A PESQUISADA ESTIVER FINALIZADA, PEGA DO HISTÓRICO
            if($rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null && $rowVeri['id_banco'] == $id_banco){
                $historico = $rowVeri['id_prestacao'];
            }
        }
    }
    
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];    
    
    $whereData = "month(data_vencimento) = {$mes} AND year(data_vencimento) = {$ano}";
    $completeWhere = $whereData." AND id_banco={$id_banco} AND status = 2 AND estorno IN (0,2)";
    
    $result_det = $saida->getDetalhado($completeWhere);
    $qr = $result_det." GROUP BY C.id_entradasaida ORDER BY C.cod";
    $result1 = mysql_query($qr);
    $total_detalhado = mysql_num_rows($result1);
    
    $qr_totais = $result_det." GROUP BY A.id_grupo";
    $result_totais = mysql_query($qr_totais);
    $totais = array();
    while ($row_total = mysql_fetch_assoc($result_totais)) {
        $totais[$row_total['id_grupo']] = $row_total['total'];
    }
    
    $qr_subtotais = $result_det." GROUP BY B.id";
    $result_subtotais = mysql_query($qr_subtotais);
    $subtotais = array();
    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
        $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
    }
    
    $qt_totalfinal = "SELECT SUM(CAST(
            REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
            FROM ({$result_det}) as q";
    
    $result_totalfinal = mysql_query($qt_totalfinal);
    $row_totalfinal = mysql_fetch_assoc($result_totalfinal);
    
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = $_REQUEST['projeto'];
$bancoR = $_REQUEST['banco'];
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Prestação de Contas</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <fieldset>
                    <legend>Despesas Realizadas</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Banco</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect($global->carregaBancosByRegiao($usuario['id_regiao'], array("-1" => "Selecione"), null), $bancoR, "id='banco' name='banco' class='required[custom[select]] form-control'"); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Mês</label>
                        <div class="col-lg-4">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <?php echo montaSelect(mesesArray(),$mesR, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                <span class="input-group-addon">Ano</span>
                                <?php echo montaSelect(AnosArray(null,null),$anoR, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />
                        </div>
                    </div>
                </fieldset>
            
            <?php
            if ($filtro) {
                if ($total_detalhado > 0) {
            ?>
            
            <input type="hidden" name="where" id="where" value="<?php echo $completeWhere ?>" />
            <input type="hidden" name="vars" id="vars" value="<?php echo "{$mes}_{$ano}_{$id_banco}" ?>" />
            
            <div class="alert alert-dismissable alert-warning">                
                <strong>Unidade Gerenciada: </strong> <?php echo $nome_projeto['nome']; ?>
                <strong class="borda_titulo">O responsável: </strong> <?php echo $master_projeto['nome']; ?>
                <strong class="borda_titulo">Mês Referente: </strong> <?php echo $mesShow; ?>
            </div>
            
            <table class='table table-hover'>
                <thead>                    
                    <tr>
                        <th colspan="3" class="text-center fundo_titulo">Despesas realizadas</th>
                    </tr>
                    <tr class="active">
                        <th>Código</th>
                        <th>Despesa</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php    
                    $antesGrupo = "";
                    $antesSubGrupo = "";
                    $i = 0;
                    while ($row = mysql_fetch_assoc($result1)) {
                        
                        if ($antesGrupo != $row['id_grupo']) {
                            $antesGrupo = $row['id_grupo'];
                    ?>
                    
                    <tr class='active'>
                        <td>0<?php echo str_replace("0", "", $row['id_grupo']) ?></td>
                        <td><?php echo $row['nome_grupo']; ?></td>
                        <td><?php echo formataMoeda($totais[$row['id_grupo']]); ?></td>
                    <tr>
                    
                    <?php
                        }
                        if ($antesSubGrupo != $row['id_subgrupo']) {
                            $antesSubGrupo = $row['id_subgrupo'];
                    ?>
                    
                    <tr class='active'>
                        <td><span class='artificio1'></span><?php echo $row['id_subgrupo']; ?></td>
                        <td><?php echo $row['subgrupo']; ?></td>
                        <td class='txright'><?php echo formataMoeda($subtotais[$row['idsub']]); ?></td>
                    <tr>
                    
                    <?php } ?>
                    
                    <tr>                                                
                        <td><span class='artificio2'></span><?php echo $row['cod']; ?></td>                                                
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo formataMoeda($row['total']); ?></td>
                    </tr>
                    
                    <?php
                    if($row['total'] != ""){
                        $res = $saida->getDespesas($row['cod'], $completeWhere);
                        $tot = mysql_num_rows($res);
                    ?>
                    <tr id="tbl<?php echo $i++; ?>" class="occ <?php echo str_replace(".", "", $row['cod']); ?>">
                        <td colspan="3">
                            <table class='table table-bordered'>
                                <tbody>
                                    <?php
                                    while ($rowd = mysql_fetch_assoc($res)) {
                                        
                                        $comprovante = "-";
                                        if($rowd['comprovante'] == 2){
                                            $comprovante = "<a class='btn btn-xs btn-danger btn-outline arq' data-key='".str_replace(".", "", $rowd['id_saida'])."'><span class='fa fa-paperclip'></span></a>";
                                        }
                                        
                                        $especifica = ($rowd['especifica'] == "") ? "-" : $rowd['especifica'];
                                        
                                        if($rowd['estorno'] == 2){
                                            $valor = "R$".number_format($rowd['cvalor'],2,",",".")." - ".number_format($rowd['valor_estorno_parcial'],2,",",".");
                                        }else{
                                            $valor = "R$".number_format($rowd['cvalor'],2,",",".");
                                        }
                                    ?>
                                    <tr class="active">
                                        <td><?php echo $rowd['id_saida']; ?></td>
                                        <td><?php echo $rowd['nome']; ?></td>
                                        <td><?php echo $especifica; ?></td>
                                        <td><?php echo $valor; ?></td>
                                        <td><?php echo $rowd['dataBr']; ?></td>
                                        <td class="text-center"><?php echo $comprovante; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php }} ?>
                </tbody>
                
                <tfoot>
                    <tr class="info">
                        <td></td>
                        <td></td>
                        <td><strong>Total: </strong><?php echo formataMoeda($row_totalfinal['total']); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
        
        </form>
            
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/financeiro/detalhado.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>