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

//----- CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;
    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = $row['id_banco']." - ".utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}

// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if(isset($_REQUEST['projeto'])){        
    
    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $rsProjeto = mysql_query("SELECT id_regiao FROM projeto WHERE id_projeto = {$id_projeto}");
    $rowProjeto = mysql_fetch_assoc($rsProjeto);
    $regiao = $rowProjeto['id_regiao'];
    $bancoSave = $_REQUEST['banco'];
    $mes2d = sprintf("%02d",$_REQUEST['mes']); //mes com 2 digitos
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    
    $historico = false;
    
    if ( (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) ||  (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) ) {
        
        /*RECUPERANDO OS PROJETOS JA FINALIZADOS*/
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
        $qr_verifica = PrestacaoContas::getQueryVerifica("despesa", $dataMesRef, $dataMesIni, $usuario['id_master']);
//        echo "<!-- $qr_verifica -->";
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
        
        if($btfinalizar)
            $btexportar = false;

        $proj_faltantes = count($projetosFaltante);
        
    }
    /* QUERY DOS PROJETOS */
    // CADA QUERY VAI SER ESPECIFICA, INFELIZMENTE
    //QUERY FILTRO E FINALIZAR
    if(isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])){
        $mesShow = mesesArray($_REQUEST['mes']) . "/" .$_REQUEST['ano'];
        //FILTRAR SEM HISTÓRICO, PEGANDO DA TABELA SAÍDA
        if($historico === false){
            $whereData = "month(data_vencimento) = {$_REQUEST['mes']} AND year(data_vencimento) = {$_REQUEST['ano']}";
            $completeWhere = $whereData." AND id_banco={$_REQUEST['banco']} AND `status` = 2 AND estorno IN (0,2) AND entradaesaida_subgrupo_id != 59  ";
            
            $qrBase = "SELECT A.id_grupo,B.id as idsub,A.nome_grupo,B.id_subgrupo,B.nome AS subgrupo,C.cod,C.nome,C.id_entradasaida,
                    COUNT(D.id_saida) AS qnt,
                    SUM(CAST( REPLACE(D.valor, ',', '.') as decimal(13,2))) as total
                    FROM entradaesaida_grupo AS A
                    LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
                    LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
                    LEFT JOIN (SELECT id_saida,tipo,
                                        IF(estorno = 2, CAST((REPLACE(valor, ',', '.') - REPLACE(valor_estorno_parcial, ',', '.')) as DECIMAL(13,2)) , valor) as valor 
                                        FROM saida WHERE $completeWhere) AS D ON (D.tipo=C.id_entradasaida)
                    WHERE C.tipo = 1 AND C.grupo >= 5 AND C.cod != \"06.03.01\" AND C.cod != \"06.04.01\"";    
                    /*WHERE C.id_entradasaida >= 154 AND C.cod != '06.03.01'"; */

            $qr = $qrBase." GROUP BY C.id_entradasaida ORDER BY C.cod";
            
        }else{
            //QUERY HISTÓRICO, BUSCANDO DA TABELA PRESTACOES_CONTAS_DESP, QUE JA ESTÁ FINALIZADA
            $qrBase = "
                SELECT A.id_grupo,B.id as idsub,A.nome_grupo,B.id_subgrupo,B.nome AS subgrupo,C.cod,C.nome,C.id_entradasaida,
                    COUNT(D.id_saida) AS qnt,
                    SUM(D.valor) as total
                    FROM entradaesaida_grupo AS A
                    LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
                    LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
                    LEFT JOIN (SELECT id_saida,id_tipo,valor 
                                    FROM prestacoes_contas_desp WHERE id_prestacao = {$historico}) AS D ON (D.id_tipo=C.id_entradasaida)
                    WHERE C.tipo = 1 AND C.grupo >= 5 AND C.cod != \"06.03.01\" AND C.cod != \"06.04.01\"";                    
                    /* WHERE C.id_entradasaida >= 154 AND C.cod != '06.03.01'"; */
            $qr = $qrBase." GROUP BY C.id_entradasaida ORDER BY C.cod";
        }
    }elseif(isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])){
        // SE FOR FINALIZAR VAI PEGAR CADA SAÍDA PARA GUARDAR NO HISTÓRICO
        
        // D.cod 06.04.01 não entra no finalizar a pedido da Daniela em 02/06/2017
        $where = "A.id_banco={$_REQUEST['banco']} AND A.`status` = 2 AND D.cod != \"06.03.01\" AND D.cod != \"06.04.01\" AND A.estorno IN (0,2)";
        $whereData = "month(A.data_vencimento) = {$_REQUEST['mes']} AND year(A.data_vencimento) = {$_REQUEST['ano']}";
        $qr = "SELECT 
                    IF( A.estorno != 2, 
                        CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)),
                        CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) - CAST(REPLACE(A.valor_estorno_parcial, ',', '.') AS DECIMAL(13,2))) AS valor2,
                    H.id_regiao,
                    A.id_saida,A.tipo,A.valor,A.data_proc,A.data_vencimento,A.id_projeto,A.id_autonomo,
                    A.data_pg,A.id_tipo_pag_saida,A.nota_impressa,A.id_prestador,A.id_nome,A.id_clt,A.n_documento,A.link_nfe,
                    A.comprovante,A.nosso_numero,A.id_banco,
                    D.cod,
                    IF(E.codigo IS NULL,'OUTROS',E.codigo) AS codigo,
                    F.nome,F.agencia,F.conta,F.id_nacional,
                    G.descricao as tipo_despesa,
                    H.cod_sesrj,H.cod_contrato
                    FROM saida AS A
                    LEFT JOIN tipos_impostos_assoc AS B ON (IF(A.tipo=170 || A.tipo=260,A.tipo=B.id_entrada_saida, A.tipo=B.id_entrada_saida OR A.id_tipo_pag_saida=B.id_tipo_pag_saida))
                    LEFT JOIN tipos_pag_saida AS C ON (C.id_tipo_pag=B.id_tipo_pag_saida)
                    LEFT JOIN entradaesaida AS D ON (D.id_entradasaida=A.tipo)
                    LEFT JOIN tipos_impostos AS E ON (E.id_imposto=B.id_tipo_imposto)
                    LEFT JOIN bancos AS F ON (F.id_banco=A.id_banco)
                    LEFT JOIN tipos_pag_saida AS G ON (G.id_tipo_pag=A.id_tipo_pag_saida)
                    LEFT JOIN projeto AS H ON (H.id_projeto=A.id_projeto)
                WHERE $whereData AND $where
                GROUP BY A.id_saida
                ORDER BY A.id_projeto,A.id_saida";
    }else{
        //SOBROU O EXPORTAR, PEGANDO TODOS OS QUE ESTÃO FINALIZADOS
        $qr = "SELECT A.*,B.comprovante,A.tipo AS nometipo, A.id_tipo AS Atipo FROM prestacoes_contas_desp AS A
                    LEFT JOIN saida AS B ON (A.id_saida = B.id_saida)
                    WHERE A.id_prestacao IN (" . implode(",", $finalizados) . ")";
    }
    
    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);
}else{
    $regiao = $usuario['id_regiao'];
}

//FINALIZANDO A PRESTAÇÃO DESSE PROJETO
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    
    echo "<!-- ".$qr." -->";
    
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = $linhas + 5;
    $qrT = "SELECT SUM(valor2) AS total
                FROM (" . $qr . ") AS tab";

    echo "<!-- ".$qrT." -->";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $val_total = $rowT['total'];
    
    $referencia = "{$_REQUEST['ano']}-{$_REQUEST['mes']}-01";
    
    //CASO TENHA PRESTAÇÃO DE CONTAS FINALIZADA COM ERRO PARA O PROJETO E O MES SELECIONADO, VAMOS ATUALIZAR
    $rsVerificaPrest = mysql_query("SELECT * FROM prestacoes_contas WHERE tipo = 'despesa' AND data_referencia = '{$referencia}' AND id_projeto = {$_REQUEST['projeto']} AND erros > 0 AND status = 1");
    $rowVerificaPrest = mysql_fetch_assoc($rsVerificaPrest);
    
    if(mysql_num_rows($rsVerificaPrest) > 0){
        $id = $rowVerificaPrest['id_prestacao'];
    }else{
        
        $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
        $valoress = array(
                $_REQUEST['projeto'],
                $regiao,
                $bancoSave,
                "despesa",
                $referencia,
                date("Y-m-d H:i:s"),
                $usuario['id_funcionario'],
                $linhas,
                "0",
                $val_total,
                "1");

        sqlInsert("prestacoes_contas",$campos,$valoress);
        $id = mysql_insert_id();
    }
    
    $matriz = array();
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
        
        $id_saida = $row['id_saida'];
        
        //COMEÇANDO PELOS CAMPOS OBRIGATORIOS, CASO FALTE ALGO, NAO GERO NENHUM DPF
        /* VERIFICA SE É PESSOA FISICA PRA PUPULAR NOME E CPF */
        $cpf = "";
        $nome = "";
        $cnpj = "";
        $razao = "";
        $descricao = "";
        if ($row['id_clt'] != "" && $row['id_clt'] != "0") {
            $qrClt = mysql_query("SELECT cpf,nome FROM rh_clt WHERE id_clt = {$row['id_clt']}");
            $rowClt = mysql_fetch_assoc($qrClt);
            $cpf = preg_replace('/[^[:digit:]]/', '', $rowClt['cpf']);
            $nome = $rowClt['nome'];
        } elseif ($row['id_nome'] != "" && $row['id_nome'] != "0") {
            $qrClt = mysql_query("SELECT TRIM(cpfcnpj) as cpfcnpj,nome FROM entradaesaida_nomes WHERE id_nome = {$row['id_nome']}");
            $rowClt = mysql_fetch_assoc($qrClt);
            
            $cpfCnpj = preg_replace('/[^[:digit:]]/', '', $rowClt['cpfcnpj']);
            
            if (strlen($cpfCnpj) > 11) {
                $cnpj = $cpfCnpj;
                $razao = $rowClt['nome'];
            } else {
                $cpf = $cpfCnpj;
                $nome = $rowClt['nome'];
            }
        } elseif ($row['id_autonomo'] != "" && $row['id_autonomo'] != "0") {
            $qrAutonomo = mysql_query("SELECT cpf,nome FROM autonomo WHERE id_autonomo = {$row['id_autonomo']}");
            $rowAuto = mysql_fetch_assoc($qrAutonomo);
            $cpf = preg_replace('/[^[:digit:]]/', '', $rowAuto['cpf']);
            $nome = $rowAuto['nome'];
        }
        
        /* VERIFICA SE É PRESTADOR PRA POPULAR *CNPJ E RAZAO* */
        if ($row['id_prestador'] != "" && $row['id_prestador'] != "0" ) {
            $qrPrest = mysql_query("SELECT c_cnpj,c_razao FROM prestadorservico WHERE id_prestador = {$row['id_prestador']}");
            $rowPrest = mysql_fetch_assoc($qrPrest);
            //EXISTE A POSSIBILIDADE DE O PRESTADOR SER UM CPF
            $cnpjLimpo = preg_replace('/[^[:digit:]]/', '',$rowPrest['c_cnpj']);
            if(strlen($cnpjLimpo)==14){
                $cnpj = $cnpjLimpo;
                $razao = $rowPrest['c_razao'];
            }else{
                $cpf = $cnpjLimpo;
                $nome = $rowPrest['c_razao'];
            }
        }
        
        /* SE O TIPO FOR TARIFA, INFORMAR O CNPJ E A RAZÃO DO BANCO */
        if ($row['tipo'] == '243') {
            $qrBan = mysql_query("SELECT cnpj,razao FROM bancos WHERE id_banco = {$row['id_banco']}");
            $rowBan = mysql_fetch_assoc($qrBan);
            $cnpj = preg_replace('/[^[:digit:]]/', '',$rowBan['cnpj']);
            $razao = $rowBan['razao'];
            $cpf = "";
            $nome = "";
        }
        
        if ($cpf == "" && $cnpj == "" || str_replace(array(".","-"),"",$cpf) == "00000000000") {

            $arrayErros[] = "Saída ".$id_saida.". Empresa/CNPJ ou Funcionário/CPF contemplado pela saída errado (CNPJ: {$cnpj}- RAZAO: {$razao}) (CPF: {$cpf}- NOME: {$nome}) (ID_CLT: {$row['id_clt']}-ID NOME: {$row['id_nome']}-ID PRESTADOR: {$row['id_prestador']}- ID_BANCO: {$row['id_banco']})";
        }else{
            $cpf = formatCPFCNPJ($cpf);
            $cnpj = formatCPFCNPJ($cnpj);
        }
        $codigo = "";//$id_saida; #SEMPRE VAZIO POIS NÃO EH OBRIGATÓRIO (SABINO 05/12/2012)
        $tipo = ($row['codigo'] == null || $row['codigo'] == "") ? "OUTROS" : $row['codigo'];


        /* SE FOR BOLETO OU NOTA FISCAL */
        $numdoc = "";
        $serie = "";
        if ($tipo == "BOLETO" || $tipo == "NF") {
            if ($tipo == "BOLETO") {
                $numdoc = $row['nosso_numero'];
            } else {
                $numdoc = $row['n_documento'];
                if($row['link_nfe']!="" && $descricao=="")
                    $descricao = $row['link_nfe'];
            }
        } else {
            $numdoc = ""; //$id_saida;
        }

        
        /* SOMENTE **DARF OU GPS** */
        $dt_apuracao = "";
        if ($tipo == "DARF" || $tipo == "GPS") {
            $dt_apuracao = $_REQUEST['ano'] . "-" . mesesArray($_REQUEST['mes'] - 1) . "-01";
        }

        $rubrica = "01";
                
        //BANCO E PROJETO (CODIGO SES E CONTRATO)
        $qrBan = mysql_query("SELECT A.agencia,A.conta,B.id_projeto,B.cod_sesrj,B.cod_contrato FROM bancos AS A
                                    LEFT JOIN projeto AS B ON (A.id_projeto=B.id_projeto)
                                    WHERE id_banco = {$row['id_banco']}");
        $rowBan = mysql_fetch_assoc($qrBan);
        
        $conta = preg_replace('/[^[:digit:]]/', '',$rowBan['conta']);
        if (strpos($rowBan['agencia'], "-") !== false) {
            $agencia = explode("-", $rowBan['agencia']);
            $agencia = current($agencia);
        } else {
            $agencia = $rowBan['agencia'];
        }
        
        $cod_sesrj = $rowBan['cod_sesrj'];
        $cod_contrato = $rowBan['cod_contrato'];
        //FIM
        
        $data_proc = converteData($row['data_proc'], null);
        $data_venc = converteData($row['data_vencimento'], null);
        $data_pg = converteData($row['data_vencimento'], null);
        $valor = $row['valor2'];
        
        if($descricao=="") $descricao = "semarquivo.pdf";
        
        //GAMBI, NOJO
        //SE FOR RPA, POREM SEM O NOME NA VERDADE É OUTROS
        if($tipo == "RPA" && $cnpj != "" && $razao != ""){
            $tipo = "OUTROS";
        }
        //SE FOR BOLETO, NÃO PODE SER CPF E NOME
        if($tipo == "BOLETO" && $cpf != "" && $nome != ""){
            $tipo = "OUTROS";
        }
        $numErros = count($arrayErros);
        
        if($numErros == 0){
            $matriz[$count][] = $id;
            $matriz[$count][] = $row['id_saida'];
            $matriz[$count][] = $roMaster['cod_os'];
            $matriz[$count][] = $cod_sesrj;
            $matriz[$count][] = $cod_contrato;
            $matriz[$count][] = $anoMesReferencia."-01";
            $matriz[$count][] = $tipo;
            $matriz[$count][] = $row['tipo'];
            $matriz[$count][] = $codigo;
            $matriz[$count][] = $cnpj;
            $matriz[$count][] = $razao;
            $matriz[$count][] = $cpf;
            $matriz[$count][] = $nome;
            $matriz[$count][] = $numdoc;
            $matriz[$count][] = $serie;
            $matriz[$count][] = $descricao;
            $matriz[$count][] = $data_proc;
            $matriz[$count][] = $data_venc;
            $matriz[$count][] = $data_pg;
            $matriz[$count][] = $dt_apuracao;
            $matriz[$count][] = $valor;
            $matriz[$count][] = $row['cod'];
            $matriz[$count][] = $rubrica;
            $matriz[$count][] = $row['id_nacional'];
            $matriz[$count][] = $agencia;
            $matriz[$count][] = $conta;
            $matriz[$count][] = 1;
            $matriz[$count][] = 1;
            $matriz[$count][] = 1;
        }
         
        /*fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_sesrj']};{$row['cod_contrato']};{$anoMesReferencia};{$tipo};{$codigo};{$cnpj};{$razao};");
        fwrite($handle, "{$cpf};{$nome};{$numdoc};{$serie};{$descricao};{$data_proc};{$data_venc};{$data_pg};{$dt_apuracao};");
        fwrite($handle, "{$valor};{$valor};{$row['cod']};{$rubrica};{$row['id_nacional']};{$agencia};{$conta};1;1\r\n");*/
        unset($descricao);
        $count++;
    }
    
    if($numErros == 0){
        //LIMPO O HISTÓRICO CASO O ARQUIVO TENHA VOLTADO, E FOI REPROCESSADO
        mysql_query("DELETE FROM prestacoes_contas_desp WHERE id_prestacao = {$id}");
        
        $campos = array(
            "id_prestacao",
            "id_saida",
            "cod_os",
            "cod_unidade",
            "cod_contrato",
            "ano_mes_ref",
            "tipo",
            "id_tipo",
            "codigo",
            "cnpj",
            "razao",
            "cpf",
            "nome",
            "num_doc",
            "serie",
            "descricao",
            "data_emissao",
            "data_vencimento",
            "data_pagamento",
            "data_apuracao",
            "valor",
            "despesa",
            "rubrica",
            "banco",
            "agencia",
            "conta",
            "pmt_paga",
            "qtde_pmt",
            "status"
        );
        sqlInsert("prestacoes_contas_desp", $campos, $matriz);
        $count++; //para gravar a quantidade certa de linhas
        
        mysql_query("UPDATE prestacoes_contas SET erros = {$numErros},gerado_em = '".date("Y-m-d H:i:s")."', gerado_por = {$usuario['id_funcionario']}, linhas = {$count}, valor_total = '{$val_total}' WHERE id_prestacao = $id") or die("erro: ".  mysql_errno());
        
        echo "<script>location.href='finan_despesas.php'</script>";
    }else{
        echo "Erros encontrados:<br/>";
        foreach($arrayErros as $v){
            echo "<p>".$v."<p/>";
        }
        mysql_query("UPDATE prestacoes_contas SET erros = {$numErros} WHERE id_prestacao = $id");
    }
    exit;
}

/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    #error_reporting(E_ALL);
    $gerarZip = (isset($_REQUEST['zip']) && $_REQUEST['zip'] == "1");
    if (!extension_loaded('zip')) {
        echo "Nao esta habilitado php_zip.dll";
        exit;   
    }
    
    $linkDownload = array();
    $arrayArquivos = array();
    $arrayFilesRemove = array();
    $recisoes = array();
    $totalComprovante = 0;
    
    $dirComprovantes = dirname(dirname(__FILE__)) . "/comprovantes/";
    $msgErros = array();

    echo "<!-- ".$qr." -->";
    
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = $linhas + 5;
    $qrT = "SELECT SUM(valor) AS total
                FROM (" . $qr . ") AS tab";

    echo "<!-- ".$qrT." -->";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $val_total = number_format($rowT['total'], 2, ",", "");

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_DESP_" . date("Ymd") . "_{$mes2d}{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    $handle = fopen($filename, "w+");
    /* ESCREVENDO NO ARQUIVO */
    /* HEADER */
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};DESP;3.1;01.01.01.01\r\n");

    /* DETAIL */
    fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;TIPO;CODIGO;CNPJ;RAZAO;CPF;NOME;NUM_DOCUMENTO;SERIE;");
    fwrite($handle, "DESCRICAO;DATA_EMISSAO;DATA_VENCIMENTO;DATA_PAGAMENTO;DATA_APURACAO;VALOR_DOCUMENTO;VALOR_PAGO;DESPESA;RUBRICA;BANCO;AGENCIA;CONTA_CORRENTE;PMT_PAGA;QTDE_PMT\r\n");
    $matriz = array();
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $arquivosConcat = array();
        $descricao = "";
        $id_saida = $row['id_saida'];
        
        /* VERIFICA SE TEM COMPROVANTE */
        if ($row['comprovante'] == 2) {
            $convertPDF = false;

            /* COMPROVANTE É DE RESCISÃO => GERAR PDF A PARTIR DE UM HTML */
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
                    $arrayFilesRemove[] = $saveAS;
                    
                    //SE NÃO FOR MARCADO GERAR ZIP... EU PRECISO DO CAMPO DESCRIÇÃO, MAS NÃO PRECISO GERAR O PDF
                    if($gerarZip){
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
                    }
                    
                    $recisoes[$id_saida] = $url;
                }  else {
                    //GUIA DE MULTA RESCISÓRIA.
                    //RESOLVENDO O ANEXO DA GUIA DA MULTA RESCISÓRIA
                    
                    $query_anexo = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$id_saida'");
                    $row_anexo = mysql_fetch_assoc($query_anexo);
                    $descricao = $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file'];
                    $linkDownload[$id_saida][] = $descricao;
                    unset($query_anexo);
                    unset($row_anexo);
                }
            } else {
                #GERAR PDF APARTIR DE IMAGEM OU NÃO GERAR POIS O ANEXO PODE SER UM PDF#
                $imgPdf = new imageToPdf();
                
                if(in_array($id_saida, $saida_rpas)){
                    $query_anexo = mysql_query("SELECT A.id_pg AS id_saida_file, A.id_saida, A.tipo_pg AS tipo_saida_file FROM saida_files_pg AS A WHERE id_saida =  '$id_saida'");
                }else{
                    $query_anexo = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$id_saida'");
                }
                
                /* QUANTIDADE DE ANEXOS DA SAIDA
                 * SE A SAÍDA TIVER MAIS DE 1 ANEXO */
                if (mysql_num_rows($query_anexo) > 1) {
                    //RODANDO CADA COMPROVANTE DESSA SAÍDA
                    while ($row_anexo = mysql_fetch_assoc($query_anexo)) {
                        //SE O COMPROVANTE NÃO FOR PDF, VAMOS ENTRAR PRA GERAR A PARTIR DA IMAGEM
                        if ($row_anexo['tipo_saida_file'] != ".pdf" && $row_anexo['tipo_saida_file'] != "" && $row_anexo['tipo_saida_file'] != ".") {
                            //SE NÃO FOR MARCADO GERAR ZIP... EU PRECISO DO CAMPO DESCRIÇÃO, MAS NÃO PRECISO GERAR O PDF
                            if($gerarZip){
                                if(in_array($id_saida, $saida_rpas)){
                                    $imgPdf->addFile($dirComprovantes . $row_anexo['id_saida_file'] . '.' . $id_saida . "_pg" . $row_anexo['tipo_saida_file']);
                                }else{
                                    $imgPdf->addFile($dirComprovantes . $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file']);
                                }
                            }
                            $convertPDF = true;
                        } else {
                            //VARIOS COMPROVANTES PDF SEPARADOS
                            if(in_array($id_saida, $saida_rpas)){
                                $arquivosConcat[] = "../comprovantes/".$row_anexo['id_saida_file'] . '.' . $id_saida . "_pg" . $row_anexo['tipo_saida_file'];
                            }else{
                                $arquivosConcat[] = "../comprovantes/".$row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file'];
                            }
                            $arquivo = $id_saida."_v.pdf";
                            $linkDownload[$id_saida][1] = $arquivo;
                        }
                    }
                } else {
                    // A SAÍDA TEM APENAS 1 COMPROVANTE
                    $row_anexo = mysql_fetch_assoc($query_anexo);
                    /* APENAS 1 E NÃO EH PDF */
                    if ($row_anexo['tipo_saida_file'] != ".pdf" && $row_anexo['tipo_saida_file'] != "" && $row_anexo['tipo_saida_file'] != ".") {
                        //SE NÃO FOR MARCADO GERAR ZIP... EU PRECISO DO CAMPO DESCRIÇÃO, MAS NÃO PRECISO GERAR O PDF
                        if($gerarZip){
                            if(in_array($id_saida, $saida_rpas)){
                                $imgPdf->addFile($dirComprovantes . $row_anexo['id_saida_file'] . '.' . $id_saida . "_pg" . $row_anexo['tipo_saida_file']);
                            }else{
                                $imgPdf->addFile($dirComprovantes . $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file']);
                            }
                        }
                        $convertPDF = true;
                    } else {
                        if(in_array($id_saida, $saida_rpas)){
                            $descricao = $row_anexo['id_saida_file'] . '.' . $id_saida . "_pg" . $row_anexo['tipo_saida_file'];
                        }else{
                            $descricao = $row_anexo['id_saida_file'] . '.' . $id_saida . $row_anexo['tipo_saida_file'];
                        }
                        $linkDownload[$id_saida][] = $descricao;
                    }
                }

                //CONVERTENDO IMAGEM PARA PDF
                if ($convertPDF && $gerarZip) {
                    $saveAS = dirname(__FILE__) . "/arquivos/{$id_saida}.pdf";
                    if ($imgPdf->generatePdf($saveAS)) {
                        $linkDownload[$id_saida][] = $id_saida . ".pdf";
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
                }

                //CONCATENANDO VÁRIOS ARQUIVOS PDF EM APENAS 1
                
                if(count($arquivosConcat) > 1 && $gerarZip){
                    $pdf = new concat_pdf();
                    $pdf->setFiles($arquivosConcat);
                    $pdf->concat();
                    $pdf->Output(dirname(__FILE__) . "/arquivos/".$arquivo, 'F');//F
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
            $arrayArquivos[$id_saida] = "sem comprovante";
            $arrayErros[] = $id_saida." sem comprovante";
        }
        
        //$log .= $id_saida."-".$valor."<br/>";
        $tipo = $row['nometipo'];
        $codigo = $row['codigo'];
        $cnpj = $row['cnpj'];
        $razao = $row['razao'];
        $cpf = $row['cpf'];
        $nome = $row['nome'];
        
        $descricao = ($descricao=="")?"semarquivo.pdf":$descricao;
        $dt_apuracao = "";
        if($row['data_apuracao']!="" && $row['data_apuracao']!="0000-00-00")
            $dt_apuracao = date("Y-m", strtotime($row['data_apuracao']));
        
        $valor = number_format($row['valor'], 2, ",", "");
        
        fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$anoMesReferencia};{$tipo};{$codigo};{$cnpj};{$razao};");
        fwrite($handle, "{$cpf};{$nome};{$row['num_doc']};{$row['serie']};{$descricao};{$row['data_emissao']};{$row['data_vencimento']};{$row['data_pagamento']};{$dt_apuracao};");
        fwrite($handle, "{$valor};{$valor};{$row['despesa']};{$row['rubrica']};{$row['banco']};{$row['agencia']};{$row['conta']};1;1\r\n");
        unset($descricao);
        
    }
    fwrite($handle, "T;QUANTIDADE_REGISTROS;VALOR_TOTAL\r\n");
    fwrite($handle, "T;{$linhas};{$val_total}");

    /* ------------- */
    fclose($handle);
    
    $numErros = count($arrayErros);
    $referencia = "{$_REQUEST['ano']}-{$_REQUEST['mes']}-01";
    
    if($numErros == 0 && $gerarZip){
        
        echo "Referencia: $referencia<br/>";
        echo "<a href='arquivos/{$fname}'>Download do arquivo CSV</a>";
        echo "<br/><hr/><br/>";
        
        $nameZip = "arquivos/DESP_{$mes2d}-{$_REQUEST['ano']}.zip";
        
        if(is_file($nameZip)){
            unlink($nameZip);
        }
        
        $zip = new ZipArchive();
        $zip->open($nameZip, ZIPARCHIVE::CREATE);
        foreach($linkDownload as $k => $down){
            $d = "arquivos/";
            if (substr_count(current($down), ".") >= 2) $d = "../comprovantes/";
            if(is_file($d.current($down)))
                $zip->addFile($d.current($down),current($down));
            else
                $arrayErros[] = "arquivo não encontrado ".$d.current($down);
            //$zip->addFile($folder.$val,$val);
        }
        $zip->close();
        
        //REMOVENDO ARQUIVOS GERADOS TEMPORARIOMENTE, POIS JA ESTÃO NO ZIP
        foreach($arrayFilesRemove as $remove){
            if(!unlink($remove)){
                $arrayErros[] = "impossivel remover o arquivo ".$remove;
            }
        }
        
        echo "<a href='{$nameZip}'>Download do ZIP</a><br/>";

        echo "Total de saídas com comprante: {$totalComprovante}<br/><pre>";
    }else{
        
        echo "Referencia: $referencia<br/>";
        echo "<a href='arquivos/{$fname}'>Download do arquivo CSV</a>";
        echo "<br/><hr/><br/>";
        
        echo "Arquivo gerado, porém contem erros descritos abaixo:<br/><br/>";
    }
    
    if(count($arrayErros)> 0 ){
        foreach($arrayErros as $v){
            echo "<p>".$v."<p/>";
        }
    }
    //echo "<br/>".$log;
    exit;
}

/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    
    
    $result = mysql_query($qr);
    
    echo "<!-- ESSE {$qr} -->\r\n";
    
    $qr_totais = $qrBase." GROUP BY A.id_grupo";
    $result_totais = mysql_query($qr_totais);
    $totais = array();
    while ($row_total = mysql_fetch_assoc($result_totais)) {
        $totais[$row_total['id_grupo']] = $row_total['total'];
    }
    
    $qr_subtotais = $qrBase." GROUP BY B.id";
    $result_subtotais = mysql_query($qr_subtotais);
    $subtotais = array();
    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
        $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
    }
    
    $qt_totalfinal = "SELECT SUM(CAST(
            REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
            FROM ({$qrBase}) as q";
            
    $result_totalfinal = mysql_query($qt_totalfinal);
    $row_totalfinal = mysql_fetch_assoc($result_totalfinal);

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);
    $id_projeto = $_REQUEST['projeto'];

    $qr_master = mysql_query("SELECT nome FROM master WHERE id_master = {$projeto['id_master']}");
    $masters = mysql_fetch_assoc($qr_master);
    $masterNome = $masters['nome'];
    
    echo "<!--QUERY: " . $qr . "-->\n\r";
    echo "<!--TOTAIS: " . $qr_totais . "-->\n\r";
    echo "<!--SUBTOTAL: " . $qr_subtotais . "-->\n\r";
    echo "<!--TOTALFIN: " . $qt_totalfinal . "-->\n\r";
    echo "<!--VERIFICA: " . $qr_verifica . "-->\n\r";
    
}
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '{$regiao}'");
$row_regiao = mysql_fetch_assoc($qr_regiao);
$id_master = $row_regiao['id_master'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$id_master' AND status_reg = 1 AND prestacontas = 1");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$meses = mesesArray(null);
$anos = anosArray(null, null,array("-1"=>"« Selecione »"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

?>
<html>
    <head>
        <title>:: Intranet :: DESPESAS REALIZADAS</title>
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
                
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        showLoading($this,"../");
                        $.post('finan_despesas.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
                            removeLoading();
                            if(data.status==1){
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options){
                                    selected = "";
                                    if(i==$("#bancSel").val()){
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        },"json");
                    }
                }).trigger("change");
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <h2>DESPESAS REALIZADAS</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(PrestacaoContas::carregaProjetos($master), $projetoR, "id='projeto' name='projeto' class='validate[custom[select]]'") ?></p> 
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Todos »"), null, "id='banco' name='banco'") ?></p>
                    <p id="mensal" ><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mês de pagamento)</p>

                    <p class="controls">
                        <input type="submit" class="button" value="Filtrar" name="filtrar" />
                    </p>
                </fieldset>

                    <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <br/>
                    <p style="text-align: right;"><input type="button" onclick="tableToExcel('tabela', 'Despesas')" value="Exportar para Excel" class="exportarExcel"></p>
                    <table id="tabela" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th colspan="2">UNIDADE GERENCIADA: <?php echo $projeto['nome'] ?></th>
                                <th><?php echo $mesShow ?></th>
                            </tr>
                            <tr>
                                <th colspan="3">O RESPONSÁVEL: <?php echo $masterNome ?></th>
                            </tr>
                            <tr>
                                <th colspan="3">DESPESAS REALIZADAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="titulo">
                                <td>Código</td>
                                <td>Despesa</td>
                                <td>Valor(R$)</td>
                            </tr>
                            <?php
                            $antesProjeto = "";
                            $antesGrupo = "";
                            $antesSubGrupo = "";
                            while ($row = mysql_fetch_assoc($result)) {
                                
                                if($antesProjeto != $row['id_projeto']){
                                    $antesProjeto = $row['id_projeto'];
                                    echo "<tr class=\"subtitulo\"><td colspan='100%' style='text-align: center;'>Projeto: {$projetos[$row['id_projeto']]}</td></tr>";
                                }
                                
                                if ($antesGrupo != $row['id_grupo']) {
                                    $antesGrupo = $row['id_grupo'];
                                    echo "<tr class=\"subtitulo\"><td>0" . str_replace("0", "", $row['id_grupo']) . "</td><td>" . $row['nome_grupo'] . "</td><td class='txright'>" . number_format($totais[$row['id_grupo']], 2, ",", ".") . "</td></tr>";
                                }
                                if ($antesSubGrupo != $row['id_subgrupo']) {
                                    $antesSubGrupo = $row['id_subgrupo'];
                                    echo "<tr class=\"subtitulo\"><td><span class='artificio1'></span>" . $row['id_subgrupo'] . "</td><td>" . $row['subgrupo'] . "</td><td class='txright'>" . number_format($subtotais[$row['idsub']], 2, ",", ".") . "</td></tr>";
                                }

                                echo "<tr><td><span class='artificio2'></span>" . $row['cod'] . "</td><td>" . $row['nome'] . "</td><td class='txright'>" . number_format($row['total'], 2, ",", ".") . "</td></tr>";
                            } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="txright">Total:</td>
                                <td class="txright"><?php echo number_format($row_totalfinal['total'], 2, ",", "."); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php }else{ ?>
                        <?php if ($projetoR!==null) { ?>
                        <br/>
                        <div id='message-box' class='message-green'>
                            <p>Nenhum resultado</p>
                        </div>
                        <?php } ?>
                    <?php } ?>
                        
                    <?php if ($projetoR!==null) { ?>
                        <?php if ($btexportar) { ?>
                            <p class="controls">
                                <label for="zip" style="margin-right: 15px;"><input type="checkbox" name="zip" id="zip" value="1" />Gerar arquivo ZIP</label><input type="submit" class="button" value="Exportar" name="exportar" />
                            </p>
                        <?php } ?>
                        
                        <br/>
                        <?php if ($btfinalizar) { ?>
                            <?php if ($erros==0) { ?>
                            <p class="controls"> 
                                <input type="submit" class="button" value="Finalizar Prestação" name="finalizar" />
                            </p>
                            <?php } else { ?>
                            <div id='message-box' class='message-yellow'>
                                <p><?php echo $msgErro." "; echo (count($idsErros)>0) ? implode(", ",$idsErros):""; ?></p>
                            </div>
                            <?php } ?>
                        <?php }else{ ?>
                            <div id='message-box' class='message-yellow'>
                                <p>Prestação finalizada.</p>
                            </div>
                        <?php } ?>
                        
                            
                        <?php if($proj_faltantes > 0){ ?>
                        <div id='message-box' class='message-blue'>
                            <p>Foi verificado a existencia de <?php echo $contErro?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                            <ul>
                            <?php foreach($projetosFaltante as $val){
                                echo "<li>".$val['nome'].$val['banco']."</li>";
                            }
                            ?>
                            </ul>
                        </div>
                        <?php } ?>
                    <?php } ?>
            </form>
        </div>
    </body>
</html>