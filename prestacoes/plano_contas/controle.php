<?php

error_reporting(E_ALL);

include ("../../conn.php");
include ("ConsultasPContas.class.php");
include ("PlanoContas.class.php");
include ("../../classes/global.php");
include ("../../wfunction.php");

if (isset($_REQUEST["arquivo_gerar"])) {
   

    $prosoft_regiao     = $_REQUEST['prosoft_regiao'];
    $prosoft_projeto    = $_REQUEST['prosoft_projeto'];
    $lote               = $_REQUEST['lote'];
    $datainicio         = converteData($_REQUEST['datainicio']);
    $datafim            = converteData($_REQUEST['datafim']);
    $modo_lancamento    = 1;
   
    $txt = new PlanoContas($prosoft_regiao, $prosoft_projeto, $lote, $datainicio, $datafim, $modo_lancamento);
    
    $consultaLC1 = $txt->registroLC1();
    
    $nomeFile = NormalizaNometoFile("CTBLCTOS".$consultaLC1[0]['empresa']."DBCD".substr($datainicio, 5, 2).substr($datainicio, 0, 4).date('His').".txt");
    $arquivo = fopen($nomeFile, "w");
       
    foreach ($consultaLC1 as $rowConsulta) {
        $txt->montaLC1($arquivo, $rowConsulta);
    }
    
    fclose($arquivo);
//  BAIXA O ARQUIVO
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($nomeFile));
    header("Content-Disposition: attachment; filename=$nomeFile");
    flush();
    readfile($nomeFile); 

    exit;
}

if (isset($_REQUEST['provisao_folha']) && $_REQUEST['provisao_folha'] == 'Folha Pagamento') {    
    
    $prosoft_regiao     = $_REQUEST['prosoft_regiao'];
    $prosoft_projeto    = $_REQUEST['prosoft_projet1'];
    $lote               = $_REQUEST['lote'];
    $datainicio         = converteData($_REQUEST['dtainicio']);
    $datafim            = converteData($_REQUEST['dtafim']);
    $modo_lancamento    = 2;
    
    $txt = new PlanoContas($prosoft_regiao, $prosoft_projeto, $lote, $datainicio, $datafim, $modo_lancamento);
    
    $consultaLC2 = $txt->prosoftfolhaPagamento();
    
    $nomeFile = NormalizaNometoFile("CTBLCTOS".$consultaLC2[N][0]['empresa']."FPG".substr($datainicio, 5, 2).substr($datainicio, 0, 4).date('His').".txt");
    $arquivo = fopen($nomeFile, "w");
    $txt->montaPFP($arquivo, $consultaLC2);
 
    fclose($arquivo);

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($nomeFile));
    header("Content-Disposition: attachment; filename=$nomeFile");
    flush();
    readfile($nomeFile); 

    exit;
}
