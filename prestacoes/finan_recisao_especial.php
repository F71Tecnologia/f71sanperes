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

$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$master = $usuario['id_master'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$historico = false;
$dataMesIni = date("Y-m") . "-31";
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
            $ext = end(explode(".", $file));
            if (is_file($file) && $ext == "pdf") {
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

function normalizaNome($variavel) {
    $variavel = strtoupper($variavel);
    if (strlen($variavel) > 200) {
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /", "_", $variavel);
    $nomearquivo = preg_replace("/[\/]/", "", $nomearquivo);
    $nomearquivo = preg_replace("/[¡¿¬√]/i", "A", $nomearquivo);
    $nomearquivo = preg_replace("/[·‡‚„™]/i", "a", $nomearquivo);
    $nomearquivo = preg_replace("/[…» ]/i", "E", $nomearquivo);
    $nomearquivo = preg_replace("/[ÈËÍ]/i", "e", $nomearquivo);
    $nomearquivo = preg_replace("/[ÕÃŒ]/i", "I", $nomearquivo);
    $nomearquivo = preg_replace("/[ÌÏÓ]/i", "i", $nomearquivo);
    $nomearquivo = preg_replace("/[”“‘’]/i", "O", $nomearquivo);
    $nomearquivo = preg_replace("/[ÛÚÙı∫]/i", "o", $nomearquivo);
    $nomearquivo = preg_replace("/[⁄Ÿ€]/i", "U", $nomearquivo);
    $nomearquivo = preg_replace("/[˙˘˚]/i", "u", $nomearquivo);
    $nomearquivo = str_replace("«", "C", $nomearquivo);
    $nomearquivo = str_replace("Á", "c", $nomearquivo);

    return $nomearquivo;
}

function copiarArquivo($file, $novoNome) {
    $folderSave = dirname(__FILE__) . "/arquivos/";
    $extAr = explode(".", $file);
    $ext = end($extAr);
    if (is_file($file)) {
        if (!copy($file, $folderSave . $novoNome . "." . $ext))
            echo "erro ao copiar o arquivo de: {$file} <br/> PARA: " . $folderSave . $novoNome . "." . $ext;exit;
    }else {
        echo "erro ao copiar o arquivo(n„o existe): {$file}";
        exit;
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
            $bancos[$row['id_prestacao']] = $row['id_projeto'] . " - " . utf8_encode($row['nome']);
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

    $mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
    $qr_rescisao = mysql_query("SELECT A.*, B.nome AS nome_projeto 
                                    FROM rh_recisao AS A
                                    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                                WHERE MONTH(A.data_demi) = '{$mes2d}' AND YEAR(A.data_demi) = '{$_REQUEST['ano']}' AND A.id_regiao = '{$regiao}'  AND status = '1'");
    $num_rescisao = mysql_num_rows($qr_rescisao);
    
    if (!empty($num_rescisao)) {
        
        while ($row_recisao = mysql_fetch_array($qr_rescisao)) {
            
        $id_recisao = $row_recisao['id_recisao'];

        $caminho = $row_recisao['id_regiao']."&".$row_recisao['id_clt']."&".$row_recisao['id_recisao'];
        $link = str_replace('+', '--', encrypt($caminho));
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/rh/recisao/nova_rescisao_2.php?enc=' . $link;


            $logUrl[$id_recisao] = $url;
            $descricao = "{$row_recisao[2]}_{$row_recisao['id_projeto']}_{$mes2d}_{$_REQUEST['ano']}.pdf";
            $descNormalizado = normalizaNome($descricao);

            $saveAS = dirname(__FILE__) . "/arquivos/" . $descNormalizado;

            $linkDownload[$id_recisao]['nome'] = $descNormalizado;
            $linkDownload[$id_recisao]['projeto'] = normalizaNome($row_recisao['nome_projeto']);
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

            $stylesheet = fopen('../rh/recisao/rescisao_novo.css'); //file_get_contents
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->WriteHTML($html, 0);
            $mpdf->Output($saveAS, "F");
            unset($mpdf);
            
            $recisoes[$id_recisao] = $url;
            $arrayArquivos[$id_recisao] = 1;
            $totalComprovante++;
            
           
        }
    }
    
    
    $nomePro = normalizaNome($row_recisao['nome']);
    $nameZip = "arquivos/RECISAO_{$mes2d}_{$_REQUEST['ano']}.zip";
    
    if (is_file($nameZip)) {
        unlink($nameZip);
    }
    
    $zip = new ZipArchive();
    
    $pasta_caminho = "";
    foreach ($linkDownload as $k => $down) {
        $d = dirname(__FILE__) . "/arquivos/";
        $novoNome = $down['nome'];
        if (is_file($d . $down['nome']))
            $zip->addFile($d . $down['nome'], $down['projeto'] . "/" . $novoNome);
        else
            $arrayErros[] = "arquivo n„o encontrado " . $d . current($down);
        //$zip->addFile($folder.$val,$val);
    }

    $zip->close();
    
    
    //REMOVENDO ARQUIVOS GERADOS TEMPORARIOMENTE, POIS JA EST√O NO ZIP
    foreach($arrayFilesRemove as $remove){
        if(!unlink($remove)){
            $arrayErros[] = "impossivel remover o arquivo ".$remove;
        }
    }
    
    
    $msg .= "<p><a href='{$nameZip}'>Download do ZIP {$row_recisao['id_projeto']} {$nomePro}</a></p><br/>";
    $msg .= "<p>Total de saÌdas com anexo: {$totalComprovante}</p><br/>";

    if (count($arrayErros) > 0) {
        foreach ($arrayErros as $v) {
            $msg .= "<p>" . $v . "<p/>";
        }
    }
    //exit;
}

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "´ Selecione ª"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL¡RIO SELECIONADO */
$projR = (isset($_REQUEST['prestacao'])) ? $_REQUEST['prestacao'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<html>
    <head>
        <title>:: Intranet :: GERAR PDF DAS DESPESAS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
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
                                opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                            }
                            $("#prestacao").html(opcao);
                            $("#gerar").show('slow').removeClass('hidden');
                        }
                    },"json");
                }
            }
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="projSel" id="projSel" value="<?php echo $projR ?>" />
                <h2>GERAR ARQUIVO ZIP DE RECIS’ES</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p id="mensal" ><label class="first">MÍs:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mÍs da prestaÁ„o finalizada) </p>
                    <p class="controls">
                        <input type="submit" id="gerar" class="button hidden" value="Gerar" name="gerar" />
                    </p>
                </fieldset>

                <?php echo $msg; ?>
            </form>
        </div>
    </body>
</html>