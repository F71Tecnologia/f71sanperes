<?php
include ("../../conn.php");
include ("../../wfunction.php");
include ("../../classes/SefipClass.php");
include ("../../classes/FolhaClass.php");
include ("../../classes/EventoClass.php");
include ('../../classes_permissoes/botoes.class.php');

$id_folha = $_REQUEST['folha'];

$sefip_ctr = new Folha();
$res_info = $sefip_ctr->getFolhaInfo($id_folha);

$projeto = $res_info['projeto'];
$regiao = $res_info['regiao'];
$mes = $res_info['mes'];
$ano = $res_info["ano"];
$terceiro = $res_info["folha_terceiro"];
$id_master = $res_info["id_master"];

$re = new SefipClass($mes, $ano, $id_master, $terceiro);
$evento = new Eventos();

if (empty($id_folha)) {
    $idFolhas = $re->getIdFolhas();
    $rowIdFolha = mysql_fetch_assoc($idFolhas);
    $id_folha = implode($rowIdFolha, ',');
}

$arrayMov = $re->montaArrayStatusCodMovimento();
$arrayRescisao = $re->montaArrayRescisao();

$nomeFile = normalizaNometoFile("SEFIP_" . $projeto . ".re");
$arquivo = fopen($nomeFile, "w");

$empregador = $re->getEmpregador($regiao);
$rowEmpregador = mysql_fetch_assoc($empregador);

$re->montaReg00($arquivo, $rowEmpregador);
if ($terceiro == 2) {    
    $salFamiMaternidade = $re->getSalMaternidade_Familia($id_folha);
    $rowSalFamiMaternidade = mysql_fetch_assoc($salFamiMaternidade);
    
//    echo "<pre>";
//    print_r($rowEmpregador);
//    echo "</pre>";
//    
//    exit();
    
    $re->montaReg10($arquivo, $rowEmpregador, $rowSalFamiMaternidade);
} else {
    $re->montaReg10($arquivo, $rowEmpregador);
}
$re->montaReg12($arquivo, $rowEmpregador);

$idsCltAnteriores = $re->getIdsCltAnteriores($regiao);
while ($rowIdsCltAnt = mysql_fetch_assoc($idsCltAnteriores)) {
    $arrayIdCltAnt[] = $rowIdsCltAnt['id_cltAnteriores'];
}

$re->setId_regiao($regiao);
$re->setId_projeto($projeto);
$empregado = $re->getEmpregado($id_folha);

//exit();

while ($rowEmpregado = mysql_fetch_assoc($empregado)) {
    
//    if($rowEmpregado['id_trab'] == 620){
//        echo "<pre>";
//        print_r($rowEmpregado);
//        echo "<pre>";
//        
//        exit();
//    }
    
    $arrayDadosBasicos[$rowEmpregado['id_trab']]['pis'] = $rowEmpregado['pislimpo'];
    $arrayDadosBasicos[$rowEmpregado['id_trab']]['data_entrada'] = $rowEmpregado['data_entrada'];
    $arrayDadosBasicos[$rowEmpregado['id_trab']]['nome'] = $rowEmpregado['nome'];
    $arrayDadosBasicos[$rowEmpregado['id_trab']]['campo1'] = $rowEmpregado['campo1'];
    $arrayDadosBasicos[$rowEmpregado['id_trab']]['serie_ctps'] = $rowEmpregado['serie_ctps'];
    $arrayDadosBasicos[$rowEmpregado['id_trab']]['id_trab'] = $rowEmpregado['id_trab'];
    
    $arrayReg30[$rowEmpregado['id_trab']]['data_nasci'] = $rowEmpregado['data_nasci'];
    $arrayReg30[$rowEmpregado['id_trab']]['cod'] = $rowEmpregado['cod'];
    $arrayReg30[$rowEmpregado['id_trab']]['base_inss_13_rescisao'] = $rowEmpregado['base_inss_13_rescisao'];
    $arrayReg30[$rowEmpregado['id_trab']]['base_inss'] = $rowEmpregado['base_inss'];
    $arrayReg30[$rowEmpregado['id_trab']]['decimo_terceiro'] = $rowEmpregado['decimo_terceiro'];
    $arrayReg30[$rowEmpregado['id_trab']]['status_clt'] = $rowEmpregado['status_clt'];
    $arrayReg30[$rowEmpregado['id_trab']]['ocorrencia'] = $rowEmpregado['ocorrencia'];
    $arrayReg30[$rowEmpregado['id_trab']]['desconto_inss'] = $rowEmpregado['desconto_inss'];
    $arrayReg30[$rowEmpregado['id_trab']]['tipo_desconto_inss'] = $rowEmpregado['tipo_desconto_inss'];
    $arrayReg30[$rowEmpregado['id_trab']]['valDescSegurado'] = $rowEmpregado['valDescSegurado'];
    $arrayReg30[$rowEmpregado['id_trab']]['mes'] = $rowEmpregado['mes'];
    $arrayReg30[$rowEmpregado['id_trab']]['ano'] = $rowEmpregado['ano'];
    $arrayReg30[$rowEmpregado['id_trab']]['data_inicio'] = $rowEmpregado['data_inicio'];
    $arrayReg30[$rowEmpregado['id_trab']]['data_final'] = $rowEmpregado['data_final'];
    $arrayReg30[$rowEmpregado['id_trab']]['data_demi'] = $rowEmpregado['data_demi'];
    $arrayReg30[$rowEmpregado['id_trab']]['valor_ferias'] = $rowEmpregado['valor_ferias'];
    $arrayReg30[$rowEmpregado['id_trab']]['categoria'] = $rowEmpregado['categoria'];        
    
    if ($terceiro == 2) {
        if (!empty($rowEmpregado['sefip_codigo'])) {
            $arrayReg13[$rowEmpregado['id_trab']]['sefip_codigo'] = $rowEmpregado['sefip_codigo'];
            $arrayReg13[$rowEmpregado['id_trab']]['sefip_valor'] = $rowEmpregado['sefip_valor'];
        }
    }

    if($rowEmpregado['categoria'] != "13"){
        $arrayReg14[$rowEmpregado['id_trab']]['endereco'] = $rowEmpregado['endereco'];
        $arrayReg14[$rowEmpregado['id_trab']]['bairro'] = $rowEmpregado['bairro'];
        $arrayReg14[$rowEmpregado['id_trab']]['cep'] = $rowEmpregado['cep'];
        $arrayReg14[$rowEmpregado['id_trab']]['cidade'] = $rowEmpregado['cidade'];
        $arrayReg14[$rowEmpregado['id_trab']]['uf'] = $rowEmpregado['uf'];
    }

    $parte = $rowEmpregado['parte'];
}

foreach ($arrayReg13 as $key13 => $dadosEmpregado) {
    $re->montaReg13($arquivo, $rowEmpregador, $arrayDadosBasicos[$key13], $dadosEmpregado);
}

foreach ($arrayReg14 as $key14 => $dadosEmpregado) {
    if(!in_array($key14,$arrayIdCltAnt)){
        $re->montaReg14($arquivo, $rowEmpregador, $arrayDadosBasicos[$key14], $dadosEmpregado); // OPCIONAL
    }
}

foreach ($arrayReg30 as $key30 => $dadosEmpregado) {

//    echo $arrayDadosBasicos[$key30]['nome'].": ".$dadosEmpregado['base_inss']."<br>";

    if ($terceiro == 2) {
        if ($mes == 11 || $mes = 12) {
            $decimo_terceiro = $re->getDecimoTerceiroMes($key30, $projeto);
            $rowDecimoTerceiro = mysql_fetch_assoc($decimo_terceiro);
        }
    }
    
    //$re->montaReg20($arquivo, $tomadorServ);  // NÃO É USADO
    //$re->montaReg21($arquivo, $tomadorServ); // NÃO É USADO

    $dias_trab = $re->getDiasTrabalhadosByAno($key30, $projeto);
    $rowDiasTrab = mysql_fetch_assoc($dias_trab);

    $re->montaReg30($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $dadosEmpregado, $arrayRescisao, $rowDiasTrab['dias_trab'], $rowDecimoTerceiro);           
    
    if ($terceiro == 2) {
        
        if(($dadosEmpregado['status_clt'] >= 60 && $dadosEmpregado['status_clt'] <= 66) || ($dadosEmpregado['status_clt'] == 81) || ($dadosEmpregado['status_clt'] == 101) ){
            $codMov = $dadosEmpregado['status_clt'];
            $dataMov = $dadosEmpregado['data_demi'];
        }
        
        $data_demi = date("m/Y", str_replace("/", "-", strtotime($dadosEmpregado['data_demi'])));
        $data_evento = $dadosEmpregado['mes']."/".$dadosEmpregado['ano'];
        
//        echo $arrayDadosBasicos[$key30]['nome'].": ".$data_evento."<br>";
        
//        echo "<pre>";
//        print_r($dadosEmpregado);
//        echo "</pre>";                
        
        $dados = $evento->validaEventoForFolha($key30, "{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}", $dadosEmpregado['data_inicio'], $dadosEmpregado['data_final']);
        if (!empty($dados)) {
            $codMov = $dados['cod_evento'];
            $dataMov = $dados['dt_inicio'];
        }
        
        unset($dados);
        
        if($data_evento == $data_demi){
            if (!empty($codMov)) {
                foreach ($arrayMov as $key => $value) {
                    if ($codMov == $key) {
                        $codMov = $value;
                        $re->montaReg32($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $codMov, $dataMov);
                        break;
                    }
                }
            }
            
        }elseif($data_evento != $data_demi && $dadosEmpregado['status_clt'] < 60){
            if (!empty($codMov)) {
                foreach ($arrayMov as $key => $value) {
                    if ($codMov == $key) {
                        $codMov = $value;
                        $re->montaReg32($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $codMov, $dataMov);
                        break;
                    }
                }
            }
        }
        
        unset($codMov, $dataMov, $rowDecimoTerceiro);
    }
}
$re->montaReg90($arquivo);
//exit;
fclose($arquivo);

//if(!$btn->verifica_permissao(196)){
//    $re->gravaLog($id_regiao,$id_projeto,$id_folha,$usuario,$parte);
//}
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
//}
?>