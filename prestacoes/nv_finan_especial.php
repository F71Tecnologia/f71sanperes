<?php
#error_reporting(E_ALL);
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('../classes/pdf/fpdf.php');
include('../classes/mpdf54/mpdf.php');
include('../classes/imageToPdf.php');
require('../classes/fpdfi/fpdi.php');
include('PrestacaoContas.class.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE«ALHO (TROCA DE MASTER E DE REGI’ES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"PrestaÁ„o de Contas", "ativo"=>"Gerar PDF Despesas","id_form"=>"form1");

$master = $usuario['id_master'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$historico = false;
$dataMesIni = date("Y-m")."-31";
$erros = 0;
$idsErros = array();
$msg = "";

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
                    $s = $this->getTemplatesize($tplidx); 
                    $this->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                    @$this->useTemplate($tplidx);
                }
            }
        }
    }
}

function normalizaNome($variavel){
    $variavel = strtoupper($variavel);
    if(strlen($variavel) > 200){
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /","_",$variavel);
    $nomearquivo = preg_replace("/[\/]/","",$nomearquivo);
    $nomearquivo = preg_replace("/[¡¿¬√]/i","A",$nomearquivo);
    $nomearquivo = preg_replace("/[·‡‚„™]/i","a",$nomearquivo);
    $nomearquivo = preg_replace("/[…» ]/i","E",$nomearquivo);
    $nomearquivo = preg_replace("/[ÈËÍ]/i","e",$nomearquivo);
    $nomearquivo = preg_replace("/[ÕÃŒ]/i","I",$nomearquivo);
    $nomearquivo = preg_replace("/[ÌÏÓ]/i","i",$nomearquivo);
    $nomearquivo = preg_replace("/[”“‘’]/i","O",$nomearquivo);
    $nomearquivo = preg_replace("/[ÛÚÙı∫]/i","o",$nomearquivo);
    $nomearquivo = preg_replace("/[⁄Ÿ€]/i","U",$nomearquivo);
    $nomearquivo = preg_replace("/[˙˘˚]/i","u",$nomearquivo);
    $nomearquivo = str_replace("«","C",$nomearquivo);
    $nomearquivo = str_replace("Á","c",$nomearquivo); 
    
    return $nomearquivo;
}

function copiarArquivo($file,$novoNome){
    $folderSave = dirname(__FILE__) . "/arquivos/";
    $extAr = explode(".", $file);
    $ext = end($extAr);
    if(is_file($file)){
        if(!copy($file, $folderSave.$novoNome.".".$ext))
            echo "erro ao copiar o arquivo de: {$file} <br/> PARA: ".$folderSave.$novoNome.".".$ext;exit;
    }else{
        echo "erro ao copiar o arquivo(n„o existe): {$file}";exit;
    }
    return true;
}

/**/

//----- CARREGA PROJETOS COM PRESTA«’ES FINALIZADAS NO MES SELECIONADO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "finalizados") {
    $return['status'] = 1;
    $qr_proFinalizado = mysql_query("SELECT A.id_prestacao,A.id_projeto,B.nome FROM prestacoes_contas AS A
                                LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                                WHERE   A.tipo = 'despesa' AND Year(data_referencia) = '{$_REQUEST['ano']}' AND 
                                        Month(data_referencia) = '{$_REQUEST['mes']}' AND A.erros = 0
                                ORDER BY B.nome ASC");
    $num_rows = mysql_num_rows($qr_proFinalizado);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_proFinalizado)) {
            $bancos[$row['id_prestacao']] = $row['id_projeto']." - ".utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Nao tem projeto finalizado na data selecionada";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}


/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['gerar']) && !empty($_REQUEST['gerar'])) {
    //error_reporting(E_ALL);
    error_reporting(0); 
    //echo "dddd";exit;
    $mes2d = sprintf("%02d",$_REQUEST['mes']); //mes com 2 digitos
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    
    $qr_proj = mysql_query("SELECT A.id_projeto,B.nome FROM prestacoes_contas AS A
                            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                            WHERE A.id_prestacao = {$_REQUEST['prestacao']}");
    $row_pro = mysql_fetch_assoc($qr_proj);
    
    //SOBROU O EXPORTAR, PEGANDO A PRESTA«√O SELECIONADA
    $qr = "SELECT D.nome AS folder, A.*,A.tipo AS nometipo, A.id_tipo AS Atipo,TRIM(A.razao) as razaook,TRIM(A.nome) as nomeok,
                B.data_vencimento, B.comprovante, B.nome as nomesaida, B.especifica,
                C.grupo
                FROM prestacoes_contas_desp AS A
                LEFT JOIN saida AS B ON (A.id_saida = B.id_saida)
                LEFT JOIN entradaesaida AS C ON (A.id_tipo = C.id_entradasaida)
                LEFT JOIN entradaesaida_subgrupo AS D ON(LEFT(A.despesa,5) = D.id_subgrupo)
                WHERE A.id_prestacao = '{$_REQUEST['prestacao']}' ORDER BY A.despesa";
               
    if (!extension_loaded('zip')) {
        echo "Nao esta habilitado php_zip.dll";
        exit;
    }
    
    $linkDownload = array();
    $arrayArquivos = array();
    $arrayFilesRemove = array();
    $linkDownloadPG = array();
    $arrayErros = array();
    $recisoes = array();
    $totalComprovante = 0;
    
    $dirComprovantes = dirname(dirname(__FILE__)) . "/comprovantes/";
    $msgErros = array();

    echo "<!-- ".$qr." -->";
    
    $result = mysql_query($qr);
    
    $matriz = array();
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
        
        $matriz[$row['id_saida']] = $row['folder'];
        
        $arquivosConcat = array();
        $descricao = "";
        $id_saida = $row['id_saida'];
        //echo $id_saida."<br/>";
        $especi = ($row['razaook']=="") ? $row['nomeok'] : $row['razaook'];
        $dtpagamento = str_replace("-", "", $row['data_vencimento']);
        $nomeEspecial = normalizaNome($especi);
        
        $nNome = "ANEXODESP";
        if($row['Atipo'] == 243){
            $nNome = "TARIFA";
        }elseif($row['Atipo'] == 154){
            $nNome = "REMESSAPGTO";
        }elseif($row['Atipo'] == 260){
            $nNome = "RPA";
        }elseif($row['Atipo'] == 170){
            $nNome = "RESCISAO";
        }
        
        $nomeFinal = $dtpagamento."_".$nomeEspecial."_".$row['id_prestacao_desp']."_".$id_saida."_".$nNome;
        $nomeFinalCp = $dtpagamento."_".$nomeEspecial."_".$row['id_prestacao_desp']."_".$id_saida."_COMPROVANTE";
        
        $count ++;
                
        /* VERIFICA SE TEM COMPROVANTE */
        if ($row['comprovante'] == 2) {
            $convertPDF = false;

            /* COMPROVANTE … DE RESCIS√O => GERAR PDF A PARTIR DE UM HTML */
            if ($row['Atipo'] == '170') {
                $qr_rescisao = mysql_query("SELECT rh_recisao.id_regiao,rh_recisao.id_clt, rh_recisao.id_recisao	 
                                    FROM (saida
                                    INNER JOIN pagamentos_especifico ON saida.id_saida = pagamentos_especifico.id_saida) 
                                    INNER JOIN rh_recisao ON rh_recisao.id_clt = pagamentos_especifico.id_clt  
                                    WHERE saida.id_saida =  '{$id_saida}' AND rh_recisao.status = '1' ");
                $num_rescisao = mysql_num_rows($qr_rescisao);
                $logUrl[$id_saida] = "geral";
                if (!empty($num_rescisao)) {

                    $row_recisao = mysql_fetch_array($qr_rescisao);
                    $link = str_replace('+', '--', encrypt("$row_recisao[0]&$row_recisao[1]&$row_recisao[2]"));
                    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/rh/recisao/nova_rescisao_2.php?enc=' . $link;
                    
                    $logUrl[$id_saida] = $url;
                    $descricao = "{$row_recisao[2]}_{$id_saida}.pdf";
                    $saveAS = dirname(__FILE__) . "/arquivos/" . $descricao;
                    $linkDownload[$id_saida][] = $descricao;
                    $linkDownloadRe[$id_saida][] = $nomeFinal.".pdf";
                    $arrayFilesRemove[] = $saveAS;
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_NOBODY, false);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $html = curl_exec($ch);
                    curl_close($ch);

                    $mpdf = new mPDF();
                    $mpdf->SetDisplayMode('fullpage');
                    $html = utf8_encode($html);
                    $stylesheet = file_get_contents('../rh/recisao/rescisao.css');
                    $mpdf->WriteHTML($stylesheet, 1);
                    $mpdf->WriteHTML($html);
                    $mpdf->Output($saveAS, "F");
                    unset($mpdf);
                    
                    $recisoes[$id_saida] = $url;
                }  else {
                    //GUIA DE MULTA RESCIS”RIA.
                    //RESOLVENDO O ANEXO DA GUIA DA MULTA RESCIS”RIA
                    
                    $query_anexo = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$id_saida'");
                    $row_anexo = mysql_fetch_assoc($query_anexo);
                    $descricao = $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file'];
                    //$descricao = $nomeFinal . $row_anexo['tipo_saida_file'];
                    
                    //COPIANDO ARQUIVO
                    //copiarArquivo($dirComprovantes.$descricao,$nomeFinal);
                    
                    $linkDownload[$id_saida][] = $descricao;
                    $linkDownloadRe[$id_saida][] = $nomeFinal . $row_anexo['tipo_saida_file'];
                    unset($query_anexo);
                    unset($row_anexo);
                }
            } else {
                #GERAR PDF APARTIR DE IMAGEM OU N√O GERAR POIS O ANEXO PODE SER UM PDF#
                $imgPdf = new imageToPdf();
                $query_anexo = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$id_saida'");

                /* QUANTIDADE DE ANEXOS DA SAIDA
                 * SE A SAÕDA TIVER MAIS DE 1 ANEXO */
                if (mysql_num_rows($query_anexo) > 1) {
                    //RODANDO CADA COMPROVANTE DESSA SAÕDA
                    while ($row_anexo = mysql_fetch_assoc($query_anexo)) {
                        //SE O COMPROVANTE N√O FOR PDF, VAMOS ENTRAR PRA GERAR A PARTIR DA IMAGEM
                        if ($row_anexo['tipo_saida_file'] != ".pdf" && $row_anexo['tipo_saida_file'] != "" && $row_anexo['tipo_saida_file'] != ".") {
                            $imgPdf->addFile($dirComprovantes . $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file']);
                            $convertPDF = true;
                        } else {
                            //VARIOS COMPROVANTES PDF SEPARADOS
                            $arquivosConcat[] = "../comprovantes/".$row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file'];
                            $arquivo = $id_saida."_v.pdf";
                            $arquivoRE = $nomeFinal."_v.pdf";
                            
                            //$arquivo = $nomeFinal."_v.pdf";

                            $linkDownload[$id_saida][1] = $arquivo;
                            $linkDownloadRe[$id_saida][1] = $arquivoRE;
                        }
                    }
                } else {
                    // A SAÕDA TEM APENAS 1 COMPROVANTE
                    $row_anexo = mysql_fetch_assoc($query_anexo);
                    /* APENAS 1 E N√O EH PDF */
                    if ($row_anexo['tipo_saida_file'] != ".pdf" && $row_anexo['tipo_saida_file'] != "" && $row_anexo['tipo_saida_file'] != ".") {
                        $imgPdf->addFile($dirComprovantes . $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file']);
                        $convertPDF = true;
                    } else {
                        $descricao = $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file'];
                        //$descricao = $nomeFinal . $row_anexo['tipo_saida_file'];
                        $linkDownload[$id_saida][] = $descricao;
                        $linkDownloadRe[$id_saida][] = $nomeFinal . $row_anexo['tipo_saida_file'];
                    }
                }

                //CONVERTENDO IMAGEM PARA PDF
                if ($convertPDF) {
                    $saveAS = dirname(__FILE__) . "/arquivos/{$id_saida}.pdf";
                    if ($imgPdf->generatePdf($saveAS)) {
                        $linkDownload[$id_saida][] = $id_saida . ".pdf";
                        $linkDownloadRe[$id_saida][] = $nomeFinal . ".pdf";
                        $arrayFilesRemove[] = $saveAS;
                        $descricao = $id_saida.".pdf";
                    } else {
                        echo $imgPdf->getError() . "<hr/>";
                    }

                    $msgErros[] = $imgPdf->getError($id_saida);
                }

                //CASO TENHA ARQUIVO PDF E IMAGEM GERADA PARA PDF
                //VAMOS CONCATENAR OS 2 PDF
                if(count($arquivosConcat) >= 1 && $convertPDF){
                    $arquivosConcat[] = $saveAS;
                    $linkDownload[$id_saida] = "";
                    $linkDownload[$id_saida][1] = $arquivo;
                    
                    $linkDownloadRe[$id_saida] = "";
                    $linkDownloadRe[$id_saida][1] = $arquivoRE;
                }

                //CONCATENANDO V¡RIOS ARQUIVOS PDF EM APENAS 1
                if(count($arquivosConcat) > 1){
                    $pdf = new concat_pdf();
                    $pdf->setFiles($arquivosConcat);
                    $pdf->concat();

                    $pdf->Output(dirname(__FILE__) . "/arquivos/".$arquivo, 'F');
                    $descricao = $arquivo;
                    $arrayFilesRemove[] = dirname(__FILE__) . "/arquivos/".$arquivo;
                    unset($arquivosConcat);
                    unset($pdf);
                }
                unset($saveAS);

            }

            $arrayArquivos[$id_saida] = 1;
            $totalComprovante++;
        } else {
            $arrayArquivos[$id_saida] = " sem comprovante";
            $arrayErros[] = $id_saida." sem comprovante";
        }
        
        
        //VERIFICANDO OS COMPROVANTES
        $qr_comp = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = {$id_saida}");
        $numcom = mysql_num_rows($qr_comp);
        if($numcom == 1){
            $row_co = mysql_fetch_assoc($qr_comp);
            $linkDownloadPG[$id_saida][] = $row_co['id_pg'] . "." . $id_saida . "_pg.pdf";
            $linkDownloadPGRe[$id_saida][] = $nomeFinalCp . ".pdf";
        }else{
            $arrayErros[] = "erro saida: {$id_saida}, qnt de comprovantes: {$numcom}";
        }
        
        $descricao = ($descricao=="")?"semarquivo.pdf":$descricao;
        
        unset($descricao);
        
    }
        
    //$numErros = count($arrayErros);
    $referencia = "{$_REQUEST['ano']}-{$_REQUEST['mes']}-01";
    
    $msg = "<br/><br/><h3>Referencia: $referencia</h3>";
    $msg .= "<br/><hr/><br/>";
    
    
    $nomePro = normalizaNome($row_pro['nome']);
    $nameZip = "arquivos/DESP_{$row_pro['id_projeto']}_{$nomePro}_{$mes2d}-{$_REQUEST['ano']}.zip";

    if(is_file($nameZip)){
        unlink($nameZip);
    }

    $zip = new ZipArchive();
    $zip->open($nameZip, ZIPARCHIVE::CREATE);
    foreach($linkDownload as $k => $down){
        $d = "arquivos/";
        $ArnovoNome = $linkDownloadRe[$k];
        $novoNome = current($ArnovoNome);
        if (substr_count(current($down), ".") >= 2) $d = "../comprovantes/";
        if(is_file($d.current($down))){
            $arquivo_final = normalizaNometoFile($matriz[$k]). "/" . $novoNome; 
            $zip->addFile($d.current($down),$arquivo_final);
        }else{
            $arrayErros[] = "arquivo n„o encontrado ".$d.current($down);
        }    
        //$zip->addFile($folder.$val,$val);
    }
    
    foreach($linkDownloadPG as $k => $down){
        $d = "arquivos/";
        $ArnovoNome = $linkDownloadPGRe[$k];
        $novoNome = current($ArnovoNome);
        if (substr_count(current($down), ".") >= 2) $d = "../comprovantes/";
        if(is_file($d.current($down))){
           $arquivo_final = normalizaNometoFile($matriz[$k]). "/" . $novoNome; 
           $zip->addFile($d.current($down),$arquivo_final);
        }else{
            $arrayErros[] = "arquivo n„o encontrado ".$d.current($down);
        }
    }
    $zip->close();
    
    
    //REMOVENDO ARQUIVOS GERADOS TEMPORARIOMENTE, POIS JA EST√O NO ZIP
    foreach($arrayFilesRemove as $remove){
        if(!unlink($remove)){
            $arrayErros[] = "impossivel remover o arquivo ".$remove;
        }
    }

    $msg .= "<p><a href='{$nameZip}'>Download do ZIP {$row_pro['id_projeto']} {$nomePro}</a></p><br/>";

    $msg .= "<p>Total de saÌdas com anexo: {$totalComprovante}</p><br/>";
    
    if(count($arrayErros)> 0 ){
        foreach($arrayErros as $v){
            $msg .= "<p>".$v."<p/>";
        }
    }
    //exit;
}

$meses = mesesArray(null);
$anos = anosArray(null, null,array("-1"=>"´ Selecione ª"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL¡RIO SELECIONADO */
$projR = (isset($_REQUEST['prestacao'])) ? $_REQUEST['prestacao'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: GERAR PDF DAS DESPESAS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.png" />
                       
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />                
        <!--<link href="../net1.css" rel="stylesheet" type="text/css" />VAI SAIR-->
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>
        
        <script>
            $(function(){
                $("#form1").validationEngine();
                
                $("#mes").change(function(){
                    buscar();
                }).trigger("change");
                
                $("#ano").change(function(){
                    buscar();
                });
                
            });
            
            
            var buscar = function(){
                var mes = $("#mes").val();
                var ano = $("#ano").val();
                if(mes !== "-1" && ano !== "-1"){
                    showLoading($("#ano"),"../");
                    $.post('finan_especial.php', { mes: $("#mes").val(), ano: $("#ano").val(), method: "finalizados" }, function(data) {
                        removeLoading();
                        if(data.status===1){
                            var opcao = "";
                            var selected = "";
                            for (var i in data.options){
                                selected = "";
                                if(i === $("#projSel").val()){
                                    selected = "selected=\"selected\" ";
                                }
                                opcao += "<option class='form-control' value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                            }
                            $("#prestacao").html(opcao);
                            $("#gerar").show('slow').removeClass('hide');
                        }
                    },"json");
                }
            }
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - PrestaÁ„o de Contas</h2></div>
        
            <div id="content">
                <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                    <input type="hidden" name="projSel" id="projSel" value="<?php echo $projR ?>" />
                    <input type="hidden" name="home" id="home" value="" />                                                           
                    
                    <fieldset>
                        <legend>GERAR PDF DAS DESPESAS</legend>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">MÍs</label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]] form-control'"); ?>
                                </div>
                                <div class="help-block">(MÍs de PrestaÁ„o Finalizada)</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect(array("-1" => "´ Selecione a Data ª"), null, "id='prestacao' name='prestacao' class='validate[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="pull-right">                                
                                <input type="submit" id="gerar" class="btn btn-primary button hide" value="Gerar" name="gerar" />
                            </div>
                        </div>
                    </fieldset>
                    
                    <?php echo $msg; ?>
                </form>
                <?php include_once '../template/footer.php'; ?>
            </div>
        </div>
    </body>
</html>