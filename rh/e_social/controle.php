<?php
session_start();
include ("../../conn.php");
include ("../../wfunction.php");
include('../../classes/global.php');
include ("./ConsultasESocial.class.php");
include ("./ESocial.class.php");
include ("./RegrasESocial.class.php");
include ("../../classes/calculos.php");
include ("../../classes/FolhaClass.php");
include ("../../classes/FeriasClass.php");
include ("../../classes/RescisaoClass.php");

//error_reporting(E_ALL);



//// formata nome 
//function formataNome($str){
//    
//  $meuArray = array('.', '-', ')', '(', ',', '/(  +)/i', '/', '\\');
//  $tamanho = count($meuArray);          
//  for($i=0;  $i<= $tamanho; $i++){
//      $str = str_replace("{$meuArray[$i]}",'',$str);
//  }
//  
//  
//  return $str;
//}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "validaCad"){
        
    $id_master = $_REQUEST["idMaster"];
    $iniValidade = $_REQUEST['dtInicio'];
    $iniValidade = (!empty($iniValidade)) ? "'" . ConverteData($iniValidade) . "'" : NULL;
    
    $xml = new ESocial($id_master,$iniValidade);
    $evento = $_REQUEST['evento'];
    $listaTrab = $_REQUEST["listaFunc"];
    $listaTrab = str_replace("\n",",", $listaTrab);
    $listaTrab = preg_replace('/[^[:digit:]|^,]/', '', $listaTrab);
   
    $id_Trab = explode(',', $listaTrab);
   
    if($evento == 's1050'){
        $s1050 = $xml->consultas1050();
        while($row = mysql_fetch_assoc($s1050)){
            if((!$xml->validaHora($row['horaEntrada'])) && (!$xml->validaHora($row['horaSaida']))){
                $msg = $row['nome'].' - HORÁRIO INVÁLIDO!';
                $xml->montaMatrizErro($row['id_curso'],utf8_encode($msg));
            }
        }
    }
    if($evento == 's2220' || $evento == 's2200'){
        if ($evento == 's2220'){
            $evt = $xml->consultas2220($listaTrab);
        }else{
            $regiao = $_REQUEST['regiao'];
            $projeto = $_REQUEST['projeto'];
            $evt = $xml->consultas2200($projeto,$regiao);
        }
        while($row = mysql_fetch_assoc($evt)){
            if(empty($row['cod_pais_nasc'])){
                $msg .= $row['nomeTrab'].' - PAÍS DE NASCIMENTO INVÁLIDO!<BR/>';
            }
            if(empty($row['cod_pais_nacionalidade'])){
                $msg .= $row['nomeTrab'].' - PAÍS DE NACIONALIDADE INVÁLIDO!<BR/>';
            }
            if(empty($row['nrCtps']) || empty($row['serie_ctps']) || empty($row['uf_ctps'])){
                $msg .= $row['nomeTrab'].' - CTPS OU UF INVÁLIDA!<BR/>';
            }
            if(empty($row['rg']) || empty($row['orgao']) || $row['data_emissao'] == '0000-00-00'){
                $msg .= $row['nomeTrab'].' - RG, ORGÃO OU DATA DE EMISSÃO INVÁLIDA!<BR/>';
            }
            if(empty($row['endereco'])){
                $msg .= $row['nomeTrab'].' - ENDEREÇO INVÁLIDO!<BR/>';   
            }
            if(empty($row['codMunicipioEnd'])){
                $msg .= $row['nomeTrab'].' - CÓDIGO DO MUNICÍPIO DE ENDEREÇO INVÁLIDO!<BR/>';   
            }
            if(empty($row['ufEnd'])){
                $msg .= $row['nomeTrab'].' - UF DO ENDEREÇO INVÁLIDA!<BR/>';   
            }
            if (!empty($row['cod_pais_nasc']) && $row['cod_pais_nasc'] != '001' && $row['dtChegadaPais'] == '0000-00-00') {
                $msg .= $row['nomeTrab'].' - DATA DE CHEGADA AO PAÍS INVÁLIDA!<BR/>';   
            }
//            if (!empty($row['cod_pais_nasc']) && $row['cod_pais_nasc'] != '001' && $row['dtChegadaPais'] == '0000-00-00') {
                $msg .= $row['nomeTrab'].' - INDICATIVO DE APOSENTADORIA S OU N!<BR/>';   
//            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }
    }
    if($evento == 's2320'){
        $s2320 = $xml->consultas2320($listaTrab);
        while($row = mysql_fetch_assoc($s2320)){
            if(($row['data'] == '0000-00-00') || ($row['data'] <= $row['data_entrada'])){
                 $msg = $row['nome'].' - DATA DE AFASTAMENTO INVÁLIDA!<BR>';
            }
            $msg .= $row['nome'].' - EMITENTE INVÁLIDO!';
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }
    }
    if($evento == 's2325'){
        $s2325 = $xml->consultas2325($listaTrab);
        while($row = mysql_fetch_assoc($s2325)){
            if(($row['data_mod'] == '0000-00-00') || ($row['data_mod'] <= $row['data_de'])){
                 $msg = $row['nome'].' - DATA DE ALTERAÇÃO INVÁLIDA!';
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }
    }
    if($evento == 's2330'){
        $s2330 = $xml->consultas2330($listaTrab);
        while($row = mysql_fetch_assoc($s2330)){
            if(($row['data_retorno'] == '0000-00-00') || ($row['data_retorno'] <= $row['data'])){
                 $msg = $row['nome'].' - DATA DE RETORNO INVÁLIDA!';
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }
    }
    if($evento == 's2360'){
        $s2360 = $xml->consultas2360($listaTrab);
        while($row = mysql_fetch_assoc($s2360)){
            if(($row['data_entrada'] == '0000-00-00')){
                 $msg = $row['nome'].' - DATA DE INÍCIO DA CONDIÇÃO INVÁLIDA!';
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }
    }
    if($evento == 's2365'){
        if(!empty($iniValidade)){
            $s2365 = $xml->consultas2365($listaTrab);
            while($row = mysql_fetch_assoc($s2365)){
                if(($row['data_proc'] == '0000-00-00') || ($row['data_proc'] < $row['data_entrada'])){
                     $msg = $row['nome'].' - DATA DE TÉRMINO DA CONDIÇÃO INVÁLIDA!<BR/>';
                }
                if(!empty($row['tpCondicaoPer_para']) || !empty($row['tpCondicaoIns_para'])){
                    $msg .= $row['nome'].' - O FUNCIONÁRIO AINDA SE ENCONTRA EM CONDIÇÕES DIFERENCIADA DE TRABALHO!<BR/>';
                }
                if(!empty($msg)){
                    $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                    unset($msg); 
                }
            }
        }else{
            $xml->montaMatrizErro('',utf8_encode('É NECESSÁRIO INFORMAR A DATA INICIAL DO EVENTO.<BR/>'));
        }
    }
    if($evento == 's2400'){
        if(!empty($iniValidade)){
            for($i=0; $i < count($id_Trab); $i++){
        
                $s2400 = $xml->consultas2400($id_Trab[$i]);
                $row = mysql_fetch_assoc($s2400);
              
                if(!empty($row)){
                    if($row['data_aviso'] == '0000-00-00' || $row['data_aviso'] < $row['data_entrada']){
                        $msg = $row['nome'].' - DATA DE AVISO PRÉVIO INVÁLIDA!';
                    }
                    if($row['data_demi'] == '0000-00-00' || $row['data_demi'] < $row['data_aviso']){
                        $msg = $row['nome'].' - DATA PREVISTA PARA DESLIGAMENTO INVÁLIDA!<BR/>';
                    }
                    if(empty($row['codAvicoPre'])){
                        $msg = $row['nome'].' - CÓDIGO DE AVISO PRÉVIO INVÁLIDO!<BR/>';
                    }
                }else{
                    $xml->montaMatrizErro($id_Trab[$i],utf8_encode('- NÃO FOI ENCOTRADO NENHUM REGISTRO PARA ESTE FUNCIONÁRIO.<BR/>'));
                }
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }  else {
            $xml->montaMatrizErro('',utf8_encode('É NECESSÁRIO INFORMAR A DATA INICIAL DO EVENTO.<BR/>'));
        }
    }   
    if($evento == 's2405'){
        if(!empty($iniValidade)){
            for($i=0; $i < count($id_Trab); $i++){
                $s2405 = $xml->consultas2405($id_Trab[$i]);
                $row = mysql_fetch_assoc($s2405);
                if(!empty($row)){
                    if($row['data_proc'] == '0000-00-00' || $row['data_proc'] < $row['data_aviso']){
                        $msg = $row['nome'].' - DATA DE CANCELAMENTO DO AVISO PRÉVIO INVÁLIDA!<BR/>';
                    }
                    if(empty($row['codAvicoPre'])){
                        $msg = $row['nome'].' - CÓDIGO DE CANCELAMENTO DO AVISO PRÉVIO INVÁLIDO!<BR/>';
                    }    
                }else{
                    $xml->montaMatrizErro($id_Trab[$i],utf8_encode('- NÃO FOI ENCOTRADO NENHUM REGISTRO PARA ESTE FUNCIONÁRIO.<BR/>'));
                }
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg); 
            }
        }  else {
            $xml->montaMatrizErro('',utf8_encode('É NECESSÁRIO INFORMAR A DATA INICIAL DO EVENTO.<BR/>'));
        }
    }       
    if($evento == 's2600'){
        $s2600 =  $xml->consultas2600();
        while ($row = mysql_fetch_assoc($s2600)){
            if(empty($row['cpf'])){
                $msg = $row['nome'].' - CPF INVÁLIDO!<BR/>';
            }
            if(empty($row['pis'])){
                $msg .= $row['nome'].' - NIS INVÁLIDO!<BR/>';
            }
            if(empty($row['nome'])){
                $msg .= $row['nome'].' - NOME INVÁLIDO!<BR/>';
            }
            if(empty($row['racaCor'])){
                $msg .= $row['nome'].' - RAÇA E COR DO TRABALHADOR INVÁLIDA!<BR/>';
            }
            if(empty($row['cod_estado_civil'])){
                $msg .= $row['nome'].' - ESTADO CIVIL INVÁLIDO!<BR/>';
            }
            if(empty($row['grauInstrucao'])){
                $msg .= $row['nome'].' - GRAU DE INSTRUÇÃO INVÁLIDO!<BR/>';
            }
            if(empty($row['data_nasci'])){
                $msg .= $row['nome'].' - DATA DE NASCIMENTO INVÁLIDA!<BR/>';
            }
//            if(empty($row[''])){
//                $msg .= $row['nome'].' - PAIS DE NASCIMENTO INVÁLIDO!<BR/>';
//            }
//            if(empty($row[''])){
//                $msg .= $row['nome'].' - PAIS DE NACIONALIDADE INVÁLIDO!<BR/>';
//            }
            if(empty($row['nrCtps']) || empty($row['serie_ctps']) || empty($row['uf_ctps'])){
                $msg .= $row['nome'].' - CTPS OU UF INVÁLIDA!<BR/>';
            }
            if(empty($row['rg']) || empty($row['orgao']) || $row['data_emissao'] == '0000-00-00'){
                $msg .= $row['nome'].' - RG, ORGÃO OU DATA DE EMISSÃO INVÁLIDA!<BR/>';
            }
//            if(empty($row['tipo_logradouro'])){
//                $msg .= $row['nome'].' - TIPO DE LOGRADOURO INVÁLIDO!';
//            }
            if(empty($row['endereco'])){
                $msg .= $row['nome'].' - ENDERECO INVÁLIDO!<BR/>';
            }
            if(empty($row['cep'])){
                $msg .= $row['nome'].' - CEP INVÁLIDO!<BR/>';
            }
//            if(empty($row[''])){
//                $msg .= $row['nome'].' - CÓDIGO DE MUNICÍPIO INVÁLIDO!<BR/>';
//            }
//            if(empty($row[''])){
//                $msg .= $row['nome'].' - PAIS DE NACIONALIDADE INVÁLIDO!<BR/>';
//            }
            if(empty($row['ufEnd'])){
                $msg .= $row['nome'].' - UF INVÁLIDA!<BR/>';
            }
            if (!empty($row['cod_pais_nasc']) && $row['cod_pais_nasc'] != '001' && $row['dtChegadaPais'] == '0000-00-00') {
                $msg .= $row['nome'].' - DATA DE CHEGADA AO PAÍS INVÁLIDA!<BR/>';   
            }
//            if (!empty($row['cod_pais_nasc']) && $row['cod_pais_nasc'] != '001' && empty($row['casadoBr'])){
//                $msg .= $row['nome'].' - CONDIÇÃO DE CASADO COM BRASILEIRO(A) INVÁLIDO!';
//            }
//            if (!empty($row['cod_pais_nasc']) && $row['cod_pais_nasc'] != '001' && empty($row['filhosBr'])){
//                $msg .= $row['nome'].' - INDICADOR DE FILHOS COM BRASILEIRO(A) INVÁLIDO!';
//            }
            if(empty($row['codCateg'])){
                $msg .= $row['nome'].' - CÓDIGO DA CATEGORIA INVÁLIDA!<BR/>';
            }
            if(empty($row['codCbo'])){
                $msg .= $row['nome'].' - CBO INVÁLIDO!<BR/>';
            }
            
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_autonomo'],utf8_encode($msg));
                unset($msg); 
            }
        }
        
    }
    if($evento == 's2620'){
        $s2620=$xml->consultas2620($listaTrab);
        while ($row = mysql_fetch_assoc($s2620)){
            if(empty($row['codCateg'])){
                $msg = $row['nome'].' - CÓDIGO DE CATEGORIA INVÁLIDO!<BR/>';
            }
             if(empty($row['id_curso'])){
                $msg = $row['nome'].' - CÓDIGO DO CURSO INVÁLIDO!<BR/>';
            }
             if(empty($row['codCateg'])){
                $msg = $row['nome'].' - CÓDIGO DE CATEGORIA INVÁLIDO!<BR/>';
            }
             if(empty($row['codCbo'])){
                $msg = $row['nome'].' - CBO INVÁLIDO!<BR/>';
            }
             if(empty($row['salario']) || $row['salario']<=0){
                $msg = $row['nome'].' - SALARIO INVÁLIDO!<BR/>';
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_autonomo'],utf8_encode($msg));
                unset($msg); 
            }
        }
    }
    if($evento == 's2680'){
        $s2680 = $xml->consultas2680($listaTrab);
        while($row = mysql_fetch_assoc($s2680)){
            if(empty($row['cpf'])){
                $msg = $row['nome'].' - CPF INVÁLIDO!<BR/>';
            }
            if(empty($row['codCateg'])){
                $msg .= $row['nome'].' - CÓDIGO DE CATEGORIA INVÁLIDO!<BR/>';
            }
            if(empty($row['data_saida']) || $row['data_saida'] == '0000-00-00' || $row['data_saida'] < $row['data_entrada']){
                $msg .= $row['nome'].' - DATA DE SAÍDA INVÁLIDA!<BR/>';
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_autonomo'],utf8_encode($msg));
                unset($msg);
            }
        }
    }
    if($evento == 's2800'){
        $s2800P1 = $xml->consultas2800P1($listaTrab);
        while ($row = mysql_fetch_assoc($s2800P1)) {
            if(empty($row['cpf'])){
                $msg .= $row['nome'].' - CPF INVÁLIDO!<BR/>';
            }
            if(empty($row['pis'])){
                $msg .= $row['nome'].' - PIS INVÁLIDO!<BR/>';
            }
            if(!empty($msg)){
                $xml->montaMatrizErro($row['id_clt'],utf8_encode($msg));
                unset($msg);
            }
        }
    }
    echo json_encode($xml->getMatriz());
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "admTrab"){
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $html = "";
    $qr = "SELECT id_clt,nome, data_entrada FROM rh_clt WHERE id_regiao = $regiao AND id_projeto = $projeto AND staus < 60;";
    $result = mysql_query($qr);
    $row_cnt = mysql_num_rows($result);
    if($row_cnt > 0){
        $html="<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
        while($row = mysql_fetch_assoc($result)){
            $html.="<tr>
                        <td class='txcenter'>{$row['id_clt']}</td>
                        <td>{$row['nome']}</td>
                    </tr>";
        }
        $html.="</table>";
    }else{
        $html.="<div id = 'message-box' class = 'message-red'> Nenhum registro encontrado para o filtro selecionado </div>";
    }
    echo utf8_encode($html);
    exit;
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "buscaTodosTrab"){
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $evento = $_REQUEST['evento'];
    if($evento == 's2600' || $evento == 's2620' || $evento == 's2680'){
        if($evento == 's2680'){
            $status = 0;
        }else{
            $status = 1;
        }
         $qr = "SELECT id_autonomo AS id,nome FROM autonomo WHERE id_regiao = $regiao AND id_projeto = $projeto AND `status` = $status;";
    }else{    
        if ($evento == 's2400' || $evento == 's2405' || $evento == 's2800'){
            $condicao = "AND status > 60 ";
            if($evento == 's2800'){
                $condicao .= "AND status <> 200
                        UNION
                        SELECT A.id_clt AS id , A.nome
                        FROM rh_clt AS A
                        RIGHT JOIN rh_transferencias AS B ON (A.id_clt = B.id_clt)
                        WHERE A.`status` = 10 AND A.id_regiao = $regiao AND A.id_projeto = $projeto
                        GROUP BY B.id_clt;";
            }
        }else{
            $condicao = 'AND status < 60';
        }
        $qr = "SELECT id_clt AS id,nome FROM rh_clt WHERE id_regiao = $regiao AND id_projeto = $projeto $condicao;";
    }
    $result = mysql_query($qr);
    $row_cnt = mysql_num_rows($result);
    $array = array();
    while ($row = mysql_fetch_assoc($result)) {
        $teste = $row['id'].'-'.$row['nome'];
        $array['trab'][] = utf8_encode($teste);
    }

    echo json_encode($array);
    exit();
}

if (isset($_REQUEST["gerar_arquivo"]) && $_REQUEST["gerar_arquivo"] == "Gerar arquivo") {
    
   $qr1 = mysql_query(" SELECT  A.id_clt, A.nome AS nome_funcionario, A.id_unidade,
                        IF( (SELECT @var_id_unidade:=id_unidade_de
                        FROM rh_transferencias
                        WHERE id_clt=A.id_clt
                        ORDER BY rh_transferencias.id_transferencia ASC
                        LIMIT 1) IS NOT NULL,@var_id_unidade, D.id_unidade) AS id_unidade,
                        A.data_entrada AS data_proc
                        FROM rh_clt AS A
                        LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
                        LEFT JOIN unidade AS D ON(D.id_unidade= A.id_unidade)
                        WHERE  A.id_unidade='420';");
   
  
   
   $qr2 = mysql_query(" SELECT B.id_clt,B.nome, A.id_unidade_de, A.data_proc, A.id_unidade_para, B.id_unidade, '1' AS q
                        FROM rh_transferencias AS A
                        inner JOIN rh_clt AS B ON (A.id_clt = B.id_clt AND A.id_unidade_de != B.id_unidade)
                        WHERE A.id_unidade_de = 420 AND A.data_proc >= '2013-09-01'
                        UNION
                        SELECT B.id_clt,B.nome, A.id_unidade_de, A.data_proc, A.id_unidade_para, B.id_unidade, '2' AS q
                        FROM rh_transferencias AS A
                        inner JOIN rh_clt AS B ON (A.id_clt = B.id_clt AND A.id_unidade_para = B.id_unidade)
                        WHERE id_unidade_para = 420 AND A.data_proc < '2013-09-01' AND id_unidade_para != id_unidade_de;");
   
    
    
//    $teste = 'minaaaa dooo condommmminiooo';
//    $resp = regexCaracterIgualConsecutivo($teste,3);
//    print_r($intervalo); exit;
    
    $listaTrab = $_REQUEST["listaFunc"];
    $listaTrab = str_replace("\n",",", $listaTrab);
    $listaTrab = preg_replace('/[^[:digit:]|^,]/', '', $listaTrab);
   
    $id_Trab = explode(',', $listaTrab);
   
    $id_master = $_REQUEST["idMaster"];
    $idUsuario = $_REQUEST["idUsuario"];
    $tpevento = $_REQUEST["tpevento"];
    $evento = $_REQUEST["evento"];
    $nrRecibo = $_REQUEST["recibo"];
    $regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
 
    if (!empty($evento)) {
        $iniValidade = $_REQUEST['iniValidade'];
        $iniValidade = (!empty($iniValidade)) ? "'" . ConverteData($iniValidade) . "'" : null;
        $fimValidade = $_REQUEST['fimValidade'];
        $fimValidade = (!empty($fimValidade)) ? "'" . ConverteData($fimValidade) . "'" : null;
        
        if ($tpevento == "alteracao") {
            $iniValidadeN = $_REQUEST['iniValidadeN'];
            $iniValidadeN = (!empty($iniValidadeN)) ? "'" . ConverteData($iniValidadeN) . "'" : null;
            $fimValidadeN = $_REQUEST['fimValidadeN'];
            $fimValidadeN = (!empty($fimValidadeN)) ? "'" . ConverteData($fimValidadeN) . "'" : null;    
        }

        $regra = new RegrasESocial($id_master, $iniValidade, $fimValidade, $tpevento);
        $calc = new calculos();
        $folha = new Folha();
        
        //Array de tipos de creditos
        $creditos[] = " - ";
        $mov_credito = $folha->getMovCredito();
        while ($linha = mysql_fetch_assoc($mov_credito)) {
            $creditos[] = $linha["cod"];
        }
        
        //Array de tipos de debitos
        $mov_debito = $folha->getMovDebito();
        while ($linha = mysql_fetch_assoc($mov_debito)) {
            $debitos[] = $linha["cod"];
        }
        
        $xml = new ESocial($id_master, $iniValidade, $fimValidade, $tpevento, $iniValidadeN, $fimValidadeN);
        $nomeFile = normalizaNometoFile("eSocial_" . $tpevento . "_" . $xml->limpaData((str_replace("'", "", $iniValidade))) . ".xml");

        $arquivo = fopen($nomeFile, "w");
        $s1000 = $xml->consultas1000();
        $row_s1000 = mysql_fetch_assoc($s1000); // INFRORMAÇÕES DO EMPREGRADOR
        
        if (in_array("s1000", $evento)) {
            if ($tpevento == "inclusao" || $tpevento == "alteracao") {
                $result = mysql_num_rows($regra->regraInfoEmpPeriodoConflitante());
                if ($result > 0) {
                    $_SESSION['msg'][] = "<h3>O evento Informações do Empregador/Contribuinte já foi informado neste período.</h3>";
                    header('Location: zindex.php');
                }
            } else {
                $result = mysql_num_rows($regra->regraInfoEmpPermiteExclusao());
                if ($result > 0) {
                    $_SESSION['msg'][] = "<h3>Não é possível excluir o evento Informações do Empregador/Contribuinte, pois existe outros eventos relacionados ao mesmo.</h3>";
                    header('Location: zindex.php');
                }
            }
            $result = mysql_num_rows($regra->regraInfoEmpValidaDtInicial());
            if ($result == 0) {
                $row = mysql_fetch_assoc($regra->regraInfoEmpValidaDtInicial());
                $_SESSION['msg'][] = "<h3>O período inicial indicado para este evento é inferior a data de início ({$row['inicio']}) das atividades do Empregador.</h3>";
                header('Location: zindex.php');
            }

            //REGRA_INFO_EMP_VALIDA_CLASSTRIB_BASE_ALCANTARA
            if (($row_s1000["classTrib"] == 70) && ($row_s1000["tpInscricao"] != 1 || substr($row_s1000["nrInscricao"], 0, -7) != "07752497")) {
                echo "<h3>Classificação tributária inválida!</h3>";
                return false;
            }
            
            $regra->regraInfoEmpValidaEndEstabs();

            $ratFap = $xml->consultaCnaeRatFap($xml->formataCnae($row_s1000["cnae"]));
            $row_ratFap = mysql_fetch_assoc($ratFap);
            $aliquota = $row_ratFap["aliquota_rat"];
            $percentNovo = ((int) $row_ratFap["percentual_fap"] / 100);
            
            $sHouse = $xml->consultaSoftwareHouse();
            $row_sHouse = mysql_fetch_assoc($sHouse);
            $xml->montas1000($arquivo, $row_s1000, $aliquota, $percentNovo, $row_sHouse);
            $xml->reiniciaSequencial();
        } else {
            $result = mysql_num_rows($regra->regraExisteInfoEmpregador());
            if ($result == 0) {
                echo "<h3>Impossível gerar este(s) evento(s). O cadastro do empregador ainda não foi enviado.</h3>";
                return false;
            }
            $cont = 0;
            foreach ($evento as $codEvento) {
                if ($tpevento == "exclusao" || $tpevento == "alteracao") {
                    $result = mysql_num_rows($regra->regraTabGeralExisteRegistroExcAlter($codEvento, $fimValidadeN,$iniValidadeN));
                    if ($result == 0) {
                        echo "<h3>O evento " . $codEvento . " ainda não foi incluido ou já foi excluido.</h3>";
                        $cont++;
                    }
//                    if ($tpevento == "alteracao" && !empty($iniValidadeN)) {
//                        $retult = mysql_query($regra->regraTabGeralAlteracaoPeriodoConflitante($codEvento,$fimValidadeN));
//                        if ($result > 0) {
//                            echo "<h3>O novo período informado para o evento " . $codEvento . " já existe, portanto não é possível fazer esta alteração.</h3>";
//                            $cont++;
//                        }
//                    }
                } else {
                    $result = mysql_num_rows($regra->regraTabGeralInclusaoPeriodoConflitante($codEvento, $projeto));
                    if ($result > 0) {
                        echo "<h3>O evento " . $codEvento . " já foi incluido.</h3>";
                        $cont++;
                    }
                }
            }
            if ($cont > 0) {
                return false;
            }
        }
       
        if(in_array("s1010", $evento)){// TABELA DE RUBRICAS
            $s1010 = $xml->consultas1010(); 
            while ($row_s1010 = mysql_fetch_assoc($s1010)){
                $incidencia = $xml->consultaIncidencia($row_s1010["cod"]);
                while ($row_incidencia = mysql_fetch_assoc($incidencia)){
                    if($row_s1010["cod_rubrica"]== 4003 && $row_incidencia["cod_incid"]!=0 && $row_incidencia["tipo"] == "INSS"){ // REGRA_TABRUBRICA_COMPAT_CODINDCIDCP 
                        echo "<h3>A classificação tributária da rubrica ".$row_s1010["cod"]." - ".$row_s1010["descicao"]." está incorreta, este tipo de rubrica não pode ser classificada como base de calculo da Previdência</h3>";         
                        return false;
                    }
                    $xml->montaMatrizIncid($row_incidencia);
                }
                $matrizIncid = $xml->getMatriz();
                $xml->montas1010($arquivo,$row_s1000, $row_s1010, $matrizIncid);     
                $xml->zeraMatriz();
            }
            $xml->reiniciaSequencial();
        }
        
        if (in_array("s1020", $evento)) {// TABELA DE LOTAÇÕES
            $s1020 = $xml->consultas1020();

            while ($row_s1020 = mysql_fetch_assoc($s1020)) {
                $xml->montas1020($arquivo, $row_s1000, $row_s1020);
            }
            $xml->reiniciaSequencial();
        }

        if (in_array("s1030", $evento)) {// TABELA DE CARGOS
            $s1030 = $xml->consultas1030();
            while ($row_s1030 = mysql_fetch_assoc($s1030)) {
                $xml->montas1030($arquivo, $row_s1000, $row_s1030);
            }
            $xml->reiniciaSequencial();
        }

        if (in_array("s1050", $evento)) {// TABELA DE HORÁRIOS/TURNS DE TRABALHO
            $s1050 = $xml->consultas1050();
            while ($row_s1050 = mysql_fetch_assoc($s1050)) {
                if(($xml->validaHora($row_s1050['horaEntrada'])) && ($xml->validaHora($row_s1050['horaSaida']))){
                    $xml->montas1050($arquivo, $row_s1000, $row_s1050);
                }          
            }
            $xml->reiniciaSequencial();
        }

        if (in_array("s1060", $evento)) {// TABELA DE ESTABELECIMENTOS
            $s1060 = $xml->consultas1060();
            while ($row_s1060 = mysql_fetch_array($s1060)) {
                $ratFapEstab = $xml->consultaCnaeRatFap($xml->formataCnae($row_s1060["cnae"]));
                $row_ratFapEstab = mysql_fetch_assoc($ratFapEstab);
                $aliquotaEstab = $row_ratFapEstab["aliquota_rat"];
                $percentNovoEstab = ((int) $row_ratFapEstab["percentual_fap"] / 100);

                $xml->montas1060($arquivo, $row_s1000, $row_s1060, $aliquotaEstab, $percentNovoEstab);
            }
            $xml->reiniciaSequencial();
        }
                
        if (in_array("s1070", $evento)) {// TABELA DE PROCESSOS
            $s1070 = $xml->consultas1070();
            while ($row_s1070 = mysql_fetch_assoc($s1070)) {
                $xml->montas1070($arquivo, $row_s1000, $row_s1070);
            }
            $xml->reiniciaSequencial();
        }

        if (in_array("s1100", $evento)) {
            $s1100 = $xml->consultas1100();
            while ($row_s1100 = mysql_fetch_assoc($s1100)) {
                $xml->montas1100($arquivo, $row_s1000, $row_s1100,$nrRecibo);
            }
            $xml->reiniciaSequencial();
        }

        if (in_array("s1200", $evento)) {
            $data = $iniValidade . '-01';
            $xml->limpaData((str_replace("'", "", $iniValidade)));
            $s1200 = $xml->consultas1200();
            while ($row_s1200 = mysql_fetch_array($s1200)) {
                $calc->MostraINSS($row_s1200["salario"], $data);
                $teto = $calc->teto;
                $calc->Salariofamilia($row_s1200["salario"], $row_s1200["id_clt"], $row_s1200["id_projeto"], $row_s1200["data_entrada"]);
                $calc->MostraIRRF($row_s1200["salario"], $row_s1200["id_clt"], $row_s1200["id_projeto"], $row_s1200["data_entrada"]);
                $valBase = mysql_fetch_assoc($folha->getValoresBase($row_s1200["id_clt"], $row_s1200["id_folha"]));
                $qtdDepSF = $calc->filhos_menores;
                $qtdDepIRRF = $calc->total_filhos_menor_21;
                $folha->getFichaFinanceira($row_s1200["id_clt"], $xml->ano, $xml->mes);
                $fichaFinanceira = $folha->getDadosFicha();
                $descCP = 0;
                foreach ($fichaFinanceira as $codMov => $arrayMov) {
                    $incidencia = $xml->consultaIncidencia($codMov, 'INSS');
                    while ($row_incidencia = mysql_fetch_assoc($incidencia)) {
                        if ($row_incidencia["cod_incid"] == 31 || $row_incidencia["cod_incid"] == 32) {
                            $descCP = $descCP + $arrayMov[$xml->mes];
                        }
                    }
                    if(in_array($codMov, $creditos)){
                        $vlrProventos = $vlrProventos + $arrayMov[$xml->mes];
                    }
                    if(in_array($codMov, $debitos)){
                        $vlrDescontos = $vlrDescontos + $arrayMov[$xml->mes];
                    }
                }
                $vlrLiquido = $vlrProventos - $vlrDescontos;
                
                $xml->montas1200($arquivo,$row_s1000,$row_s1200,$qtdDepSF,$qtdDepIRRF,$teto,$valBase,$descCP,$vlrProventos,$vlrDescontos,$vlrLiquido,$nrRecibo,$fichaFinanceira);
                
                unset($vlrLiquido,$vlrProventos,$vlrDescontos,$descCP,$fichaFinanceira);
            }
            $xml->reiniciaSequencial();
        }
        
        if (in_array("s2100", $evento)) {
             $id_projeto = $_REQUEST['projeto'];
             $id_regiao = $_REQUEST['regiao'];
             $s2100 = $xml->consultas2100($id_projeto, $id_regiao);
             $evt = "s2100";
             while ($row_s2100 = mysql_fetch_assoc($s2100)) {
                 $dependentes = $xml->consultaDependentes($row_s2100['id_clt'],$row_s2100['id_projeto']);
                 $tpContrato = $xml->consultaTpContrato($row_s2100['id_clt']);
                 $transferencia = $xml->consultaTransferencia($row_s2100['id_clt']);
                 $row_dependente = mysql_fetch_assoc($dependentes);
                 $row_tpContrato = mysql_fetch_assoc($tpContrato);
                 $row_statusTranf = mysql_fetch_assoc($transferencia);
                 
                 $xml->montas2100a2240($arquivo, $row_s1000, $row_s2100, $row_dependente, $row_tpContrato, $row_statusTranf, $nrRecibo, $evt);
             }
            $xml->reiniciaSequencial();  
         }
         
        if(in_array("s2200", $evento)){
             $s2200 = $xml->consultas2200();
             $evt = "s2200";
             while ($row_s2200 = mysql_fetch_assoc($s2200)) {
                 $dependentes = $xml->consultaDependentes($row_s2200['id_clt'],$row_s2200['id_projeto']);
                 $tpContrato = $xml->consultaTpContrato($row_s2200['id_clt']);
                 $transferencia = $xml->consultaTransferencia($row_s2200['id_clt']);
                 $row_dependente = mysql_fetch_assoc($dependentes);
                 $row_tpContrato = mysql_fetch_assoc($tpContrato);
                 $row_statusTranf = mysql_fetch_assoc($transferencia);
                 
                 $xml->montas2100a2240($arquivo, $row_s1000, $row_s2200, $row_dependente, $row_tpContrato, $row_statusTranf, $nrRecibo, $evt);
             }
              $xml->reiniciaSequencial();
         }
         
        if(in_array("s2220", $evento)){
             $s2220 = $xml->consultas2220($listaTrab);
             $evt = "s2220";
             while ($row_s2220 = mysql_fetch_assoc($s2220)) {
                 $dependentes = $xml->consultaDependentes($row_s2220['id_clt'],$row_s2220['id_projeto']);
                 $row_dependente = mysql_fetch_assoc($dependentes);
                 $row_tpContrato = null;
                 $row_statusTranf = null;
                 
                 $xml->montas2100a2240($arquivo, $row_s1000, $row_s2220, $row_dependente, $row_tpContrato, $row_statusTranf, $nrRecibo, $evt);
             }
              $xml->reiniciaSequencial();
         }
         
        if(in_array("s2240", $evento)){
             $evt = "s2240";
             $s2240 = $xml->consultas2240($listaTrab);
             while ($row_s2240 = mysql_fetch_assoc($s2240)) {
                 $tpContrato = $xml->consultaTpContrato($row_s2240['id_clt']);
                 $transferencia = $xml->consultaTransferencia($row_s2240['id_clt']);
                 $dependentes = null;
                 $row_dependente = null;
                 $row_tpContrato = mysql_fetch_assoc($tpContrato);
                 $row_statusTranf = mysql_fetch_assoc($transferencia);
                 
                 $xml->montas2100a2240($arquivo, $row_s1000, $row_s2240, $row_dependente, $row_tpContrato, $row_statusTranf, $nrRecibo, $evt);
             }
              $xml->reiniciaSequencial();
         }
                
//        O SISTEMA NÃO CONTROLA
//        if(in_array("s2260", $evento)){
//            $s2260 = $xml->consultas2260($listaTrab);
//            while ($row_s2260 = mysql_fetch_assoc($s2260)) {
//                $xml->montas2260($arquivo, $row_s1000, $row_s2240, $nrRecibo, $cat, $testemunha);
//                
//            }
//             $xml->reiniciaSequencial();
//        }
         
//         if(in_array("s2280", $evento)){
//            $s2280 = $xml->consultas2280($listaTrab);
//            while ($row_s2280 = mysql_fetch_assoc($s2280)) {
//                $xml->montas2280($arquivo, $row_s1000, $row_s2280,  $aso, $resultMonitoracao);
//            }
//             $xml->reiniciaSequencial(); 
//         }
         
        if(in_array("s2320", $evento)){
            $evt = "2320";
            $s2320 = $xml->consultas2320($listaTrab);
            $estabilidade = null;
            while ($row_s2320 = mysql_fetch_assoc($s2320)) {
                $xml->montas2320a2345($arquivo, $row_s1000, $row_s2320, $nrRecibo,$evt, $estabilidade);
            }
             $xml->reiniciaSequencial(); 
         }
         
        if(in_array("s2325", $evento)){
            $evt = "2325";
            $s2325 = $xml->consultas2325($listaTrab);
            $estabilidade = null;
            $row_s2325 = mysql_fetch_array($s2325);
            while ($row_s2325 = mysql_fetch_assoc($s2325)) {
                $xml->montas2320a2345($arquivo, $row_s1000, $row_s2325, $nrRecibo,$evt,$estabilidade);
            } 
             $xml->reiniciaSequencial();
         }
         
        if(in_array("s2330", $evento)){
            $evt = "2330";
            $estabilidade = null;
            $s2330 = $xml->consultas2330($listaTrab);
            while ($row_s2330 = mysql_fetch_assoc($s2330)) {
                $xml->montas2320a2345($arquivo, $row_s1000, $row_s2330, $nrRecibo,$evt, $estabilidade);
            } 
             $xml->reiniciaSequencial();
         }
//         if(in_array("s2340", $evento)){
//            $evt = "s2340";
//            $afastamento = null;
//            $s2340 = $xml->consultas2340($listaTrab);
//            while ($row_s2340 = mysql_fetch_assoc($s2340)) {
//                $xml->montas2320a2345($arquivo, $row_s1000, $row_s2340, $nrRecibo,$evt, $estabilidade);
//            } 
//             $xml->reiniciaSequencial();
//         }
//         if(in_array("s2345", $evento)){
//            $evt = "s2345";
//            $afastamento = null;
//            $s2345 = $xml->consultas2345($listaTrab);
//            while ($row_s2345 = mysql_fetch_assoc($s2345)) {
//                $xml->montas2320a2345($arquivo, $row_s1000, $row_s2345, $nrRecibo,$evt, $estabilidade);
//            } 
//             $xml->reiniciaSequencial();
//         }   
         
        if(in_array("s2360", $evento)){
            $evt = "2360";
            $s2360 = $xml->consultas2360($listaTrab);
            while ($row_s2360 = mysql_fetch_assoc($s2360)) {
                $xml->montaMatrizCondDif($row_s2360, $evt);
            } 
            $arrays2360 = $xml->getMatriz();
//            echo '<pre>';
//            print_r($arrays2360);
//            echo '</pre>';
//            exit;
            foreach ($arrays2360 as $id_clt => $arrayDados) {
                foreach ($arrayDados as $tpCondicao => $arrayCondicao) {
                    foreach ($arrayCondicao as $tpCond => $trab) {
                        $xml->montas2360a2365($arquivo, $row_s1000, $trab, $tpCond, $risco, $nrRecibo, $evt);
                    }
                }
            }
            $xml->reiniciaSequencial();
         }
         
        if(in_array("s2365", $evento)){
            $evt = "2365";
            $s2365 = $xml->consultas2365($listaTrab);
            while ($row_s2365 = mysql_fetch_assoc($s2365)) {
                $xml->montaMatrizCondDif($row_s2365, $evt);
            } 
            $arrays2365 = $xml->getMatriz();
//            echo '<pre>';
//            print_r($arrays2365);
//            echo '</pre>';
//            exit;
            foreach ($arrays2365 as $id_clt => $arrayDados) {
                foreach ($arrayDados as $tpCondicao => $arrayCondicao) {
                    foreach ($arrayCondicao as $tpCond => $trab) {
                        $xml->montas2360a2365($arquivo, $row_s1000, $trab, $tpCond, $risco, $nrRecibo, $evt);
                    }
                }
            }
            $xml->reiniciaSequencial();
         }
         
        if(in_array("s2400", $evento)){
            $evt = "2400";
            for ($i=0; $i<count($id_Trab); $i++){
                $s2400 = $xml->consultas2400($id_Trab[$i]);
                $row_s2400 = mysql_fetch_assoc($s2400);
                $xml->montas2400e2405($arquivo, $row_s1000, $row_s2400, $nrRecibo,$evt); 
            }
//            $s2400 = $xml->consultas2400($id_clts);
//            while ($row_s2400 = mysql_fetch_assoc($s2400)){
//                $xml->montas2400($arquivo, $row_s1000, $row_s2400, $nrRecibo,$evt); 
//            }
             $xml->reiniciaSequencial();
         }     
         
        if(in_array("s2405", $evento)){
            $evt = "2405";
            for ($i = 0; $i<count($id_Trab); $i++){          
                $s2405 = $xml->consultas2405($id_Trab[$i]);
                $row_s2405 = mysql_fetch_assoc($s2405);
                $xml->montas2400e2405($arquivo, $row_s1000, $row_s2405, $nrRecibo,$evt);        
            }
             $xml->reiniciaSequencial();
         } 
         
        if(in_array("s2600", $evento)){
             $evt = "2600";
             $s2600 =  $xml->consultas2600();
             while ($row_s2600 = mysql_fetch_assoc($s2600)) {
                 $dependentes = $xml->consultaDependentes($row_s2600['id_autonomo'],$row_s2600['id_projeto']);
                 $row_dependente = mysql_fetch_assoc($dependentes);
                 $xml->montas2600($arquivo, $row_s1000, $row_s2600, $row_dependente, $nrRecibo, $evt);
             }
              $xml->reiniciaSequencial();
         }
         
        if(in_array("s2620", $evento)){
            $evt = "2620";
             $s2620 =  $xml->consultas2620($listaTrab);
             while ($row_s2620 = mysql_fetch_assoc($s2620)) {
                 $row_dependente = null;
                 $xml->montas2600($arquivo, $row_s1000, $row_s2620, $row_dependente, $nrRecibo, $evt);
             }
              $xml->reiniciaSequencial();
         }
         
        if(in_array("s2680", $evento)){
             $evt = "2680";
             $s2680 = $xml->consultas2680($listaTrab);
             while ($row_s2680 = mysql_fetch_assoc($s2680)){
                 $xml->montas2680($arquivo, $row_s1000, $row_s2680, $nrRecibo);
             }
             $xml->reiniciaSequencial();
         }
         
        if(in_array("s2800", $evento)){          
            $evt = "2800";
            $statusSaida = NULL;
            $row_s2800P3 = NULL;
            
            $s2800P1 = $xml->consultas2800P1($listaTrab); // DADOS TRAB
            while ($row_s2800P1 = mysql_fetch_assoc($s2800P1)) {
                $folha->getFichaFinanceira($row_s2800P1["id_clt"], $xml->ano, $xml->mes);
                $fichaFinanceira = $folha->getDadosFicha();
                if(!empty($fichaFinanceira)){
                    $idFolha = $xml->consultaIdFolha($row_s2800P1["id_clt"]);
                    $nidFolha = mysql_num_rows($idFolha);
                    if($nidFolha > 0){
                      $rowIdFolha = mysql_fetch_assoc($idFolha);
                      $rowStatus = $xml->consultaStatusSaida($rowIdFolha['id_folha']);
                      $nStatus = mysql_num_rows($rowStatus);
                      if($nStatus > 0){
                          $statusSaida = $rowStatus['status'];
                      }
                    } 
                }
                if($statusSaida == 1 || empty($statusSaida)){
                    $bcFgtsAnt = $xml->consultaFGTSAnt($row_s2800P1['id_clt']);
                    $nbCFgtsAnt = mysql_num_rows($bcFgtsAnt);
                    if($nbCFgtsAnt > 0){
                       $rowBcFgtsAnt = mysql_fetch_assoc($bcFgtsAnt); 
                       $bcFgtsAnt = $rowBcFgtsAnt['bcFgtsMesAnt'];
                    }else{
                        $bcFgtsAnt = '0';
                    }                        
                }  
                $s2800P2 = $xml->consultas2800P2($row_s2800P1['id_clt']); // TRANSFERENCIA
                $nP2 = mysql_num_rows($s2800P2);
                if($nP2 > 0){
                    $row_s2800P2 = mysql_fetch_assoc($s2800P2);
                    $xml->montas2800($arquivo, $empregador, $row_s2800P1, $row_s2800P2, $bcFgtsAnt, $statusSaida, $fichaFinanceira, $row_s2800P3, $evt, $nrRecibo);
                }
                
                $s2800P3 = $xml->consultas2800P3($row_s2800P1["id_clt"]); // RESCISAO
                $nP3 = mysql_num_rows($s2800P3);
                if($nP3 > 0){
                    $row_s2800P2 = NULL;
                    $row_s2800P3 = mysql_fetch_assoc($s2800P3);
                    $xml->montas2800($arquivo, $empregador, $row_s2800P1, $row_s2800P2, $bcFgtsAnt, $statusSaida, $fichaFinanceira, $row_s2800P3, $evt,$nrRecibo);
                }       
            }       
        }
       
//        if(in_array("s2820", $evento)){
//            $s2820 = $xml->consultas2820($listaTrab);
//            $fichaFinanceira = null;
//            $evt = '2820';
//            while ($row_s2820 = mysql_fetch_assoc($s2820)) {
//                $xml->montas2800($arquivo, $row_s1000, $row_s2820, $nrRecibo, $fichaFinanceira, $evt);
//            }
//        }
        
        if(in_array("s2900", $evento)){
            $evt = RemoveLetras(RemoveCaracteresGeral(RemoveEspacos($_REQUEST["evt"])));
            if(($evt >= 2100 && $evt <=2820) || ($evt == 1200)){
                $s2900 = $xml->consultas2820($listaTrab);
                $indApuracao = $perApuracao = null;
                while ($row_s2900 = mysql_fetch_array($s2900)) {
                    $xml->montas2900($arquivo, $row_s1000, $row_s2900, $nrRecibo, $evt, $indApuracao, $perApuracao);
                }
            }else{
                $row_s2900 = null;
                $indApuracao = $_REQUEST['indApuracao'];
                $perApuracao = $_REQUEST['perApuracao'];
                if($indApuracao == 1){
                    $perApuracao = substr((!empty($perApuracao)) ? ConverteData($perApuracao): 'null', 0,7);
                }  else {
                    $perApuracao = substr((!empty($perApuracao)) ? ConverteData($perApuracao): 'null', 0,4);
                }
                $xml->montas2900($arquivo, $row_s1000, $row_s2900, $nrRecibo, $evt, $indApuracao, $perApuracao);
            }
        }
        
        if(in_array("s1300", $evento)){
            $id_pro = $_REQUEST['projeto'];
            $ferias = new Ferias();
            $rescisao = new Rescisao();
            $row_beneficiario = $rows_salario = $row_benePJ = $rowPresConTer = NULL;
//            $indApuracao = $_REQUEST['indApuracao'];
//            $perApuracao = $_REQUEST['perApuracao']; 
            
//            if($indApuracao == 1){
//                $perApuracao = substr((!empty($perApuracao)) ? ConverteData($perApuracao): 'null', 0,7);
//            }  else {
//                $perApuracao = substr((!empty($perApuracao)) ? ConverteData($perApuracao): 'null', 0,4);
//            }
            $benePJ = $xml->consultaBeneficiarioPJ($id_pro);
            while ($row_benePJ = mysql_fetch_assoc($benePJ)) {
                $presConTer = $xml->BuscaPrestContaTerceiro($row_benePJ['id_prestador'], $row_benePJ['mes_competencia'], $row_benePJ['ano_competencia'], $id_pro);
                $rowPresConTer = mysql_fetch_assoc($presConTer);
                $xml->montas1300($arquivo, $row_s1000, $row_beneficiario, $rows_salario, $row_benePJ, $rowPresConTer);  
            }
//            
            $arraySalario = array();
            $codRes = $xml->consultaCodRescisao();
            $beneficiario = $xml->consultaBeneficiario($id_pro);
            while ($row_beneficiario = mysql_fetch_assoc($beneficiario)){
//                $duplicidade = $xml->verificaDuplicidade(RemoveCaracteres($row_beneficiario['nrInscricaoBeneficiario']));
//                $totalCpfs = $duplicidade['total'];
//                if ($totalCpfs > 1) {
//                    foreach ($duplicidade['rs'] as $val) {
//                        $ids_trab .= $val['id_clt'] . ",";
//                        $projetos .= $val['projeto'].",";
//                    }
//                    $ids_clts = substr($ids_clts, 0, -1);
//                    $projetos = substr($projetos, 0, -1);
//                } else {
//                    $ids_clts = $row_beneficiario['id_clt'];
//                    $projetos = $row_beneficiario['id_projeto'];
//                }
                $xml->mostraFolhasDIRFF($row_beneficiario['projeto']);
              
                foreach ($xml->folhasEnvolvidas AS $folhasEnv){
                    $salario = $xml->consultaSalario($row_beneficiario['id_trab'],$folhasEnv,$codRes);
                    while ($row_salario = mysql_fetch_assoc($salario)){
                        $deducoesDep = $xml->consultaDeducoesDep($row_salario['id_clt'], $row_salario['ids_movimentos_estatisticas']);
                        $row_deducoesDep = mysql_fetch_assoc($deducoesDep);
                        $xml->montaMatrizDetDeducoes($row_salario,$row_deducoesDep);
//                        $rows_salario[$row_salario['ano']][$row_salario['mes']]['B1'] = $row_salario;
                        $rows_salario[$row_salario['ano']][$row_salario['mes']]['B2'] = $xml->getMatriz();
                        $xml->zeraMatriz();
                        $ajudaCusto = $xml->consultaAjudaDeCusto($row_salario['id_clt'], $row_salario['ids_movimentos_estatisticas']);
                        $row_ajudaCusto = mysql_fetch_assoc($ajudaCusto);
                        $dadosFerias = $ferias->getPeriodoFerias($row_salario['id_clt'], $row_salario['mes'], $row_salario['ano']);
                        $row_ferias = mysql_fetch_assoc($dadosFerias);
                        $dadosRescisao = $rescisao->getRescisaoByClt($row_salario['id_clt'], $row_salario['mes'], $row_salario['ano']);
                        $row_rescisao = mysql_fetch_assoc($dadosRescisao);
                        $xml->montaMatrizRendIsento($row_ajudaCusto, $row_rescisao, $row_ferias);    
                        if($xml->getMatriz() != NULL){
                            $rows_salario[$row_salario['ano']][$row_salario['mes']]['B3'] = $xml->getMatriz();
                        }  
                    }
                }
                $xml->montas1300($arquivo, $row_s1000, $row_beneficiario, $rows_salario);            
            }
            
           
        } 
        
        if(in_array("s1330", $evento)){
            $s1330 = $xml->consultas1330($projeto);
            while ($row = mysql_fetch_assoc($s1330)) {
                $xml->montas1330($arquivo, $empregador, $row,$nrRecibo);
            }
        }
        
        if(in_array("s1340", $evento)){
            
        }
 
        fclose($arquivo);
           
        $xml->gravaLog($idUsuario, $evento,$regiao,$projeto);
        
//        BAIXA O ARQUIVO
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
//        if($xml->getMatriz() != NULL){
//            foreach ($xml->getMatriz() as $key => $value) {
//                echo '<pre>';
//                print_r($key);
//                echo '</pre>';
//            }
//        }
        
    } else {
        echo "<h3>Selecione pelo menos um evento.</h3>";
        return false;
    }
}
?>