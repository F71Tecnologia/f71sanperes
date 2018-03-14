<?php
include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/EmpresaClass.php");
//include_once("../../classes/FolhaClass.php");
//include_once("../../classes/ContraChequeClass.php");
include_once("../fpdf/fpdf.php");
include_once("../../funcoes.php");

class Folha {

    private $dados;
    private $cabecalho;
    private $sqlInjection;
    private $decimo_terceiro;
    private $id_pagina = 60;
    
    /**
     * COMECEI AQUI
     * NOVOS ATRIBUTOS PARA 
     * FUNCIONAMENTO DA NOVA FOLHA
     * DE PAGAMENTOS
     * 
     */
    private $folha;
    private $participantesParaAtualizar;
    private $mes;
    private $ano;
    private $InicioFolha;
    private $finalFolha;    
    private $projeto;
    private $regiao;
    private $arrayLicencas;
    private $arrayRescisao;
    private $arrayFerias;
    private $objFerias; 
    private $inssFerias;
    private $irrfFerias;


    
    /**
     * MÉTODO PARA GERA FICHA FINANCEIRA
     */
    public function getDadosClt($clt, $mes = null, $ano = null) {
        $sql = mysql_query("SELECT A.id_clt, A.nome,CONCAT(A.endereco,A.numero,', ',A.bairro,', ',A.cidade,' - ',A.uf) as endereco,
            DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
            DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada,
            DATE_FORMAT(A.data_demi,'%d/%m/%Y') as data_demi,
            A.nacionalidade,A.naturalidade,A.tipo_conta,
            A.agencia, A.conta,C.nome as nome_banco, C.razao,
            A.matricula, IF(D.unidade_de != '', D.unidade_de, A.locacao) AS locacao,
            A.cpf,A.rg,A.titulo,A.campo1 as ctps, A.serie_ctps, A.pis,
            B.nome as nome_curso, FORMAT(B.salario,2) as salario
            FROM rh_clt as A 
            LEFT JOIN curso as B ON (B.id_curso = A.id_curso)
            LEFT JOIN bancos as C ON (A.banco = C.id_banco)
            LEFT JOIN (SELECT id_clt, unidade_para, unidade_de FROM rh_transferencias WHERE id_clt = $clt AND LAST_DAY(data_proc) > LAST_DAY('$ano-$mes-01') ORDER BY id_transferencia DESC LIMIT 1) as D ON (D.id_clt = A.id_clt)
            WHERE A.id_clt = $clt") or die(mysql_error());

        return $sql;
    }
    /**
     * MÉTODO QUE RETORNA DADOS DO CLT EM UM ARRAY
     * @param type $clt
     * @param type $ano
     * @param type $meses
     */
    public function getFichaFinanceira($clt, $ano, $meses = null, $terceiro = null) {
        
        //MONTA CABEÇALHO
        $this->getCabecalho();
        $criteria = "";
        
        if(!empty($terceiro)){
            $criteria .= "B.terceiro = " . $terceiro . " AND "; 
        }
        
        if (empty($meses)) {
            $criteria .= "A.id_clt = '{$clt}' AND A.ano = '{$ano}'";
            $ferias = "LEFT JOIN rh_ferias AS D ON(A.id_clt = D.id_clt AND A.ano = D.ano AND A.mes = LPAD(D.mes,2,0) AND D.status = 1)";
        } else {
            $criteria .= "A.id_clt = '{$clt}' AND A.ano = '{$ano}' AND A.mes = '{$meses}'";
            $ferias = "LEFT JOIN rh_ferias AS D ON(A.id_clt = D.id_clt AND LPAD(D.mes,2,0) = '{$meses}' AND D.ano = '{$ano}' AND D.status = 1)";
            $this->zeraArray();
        }

        $sqls = "SELECT A.id_folha, B.terceiro, B.tipo_terceiro, A.id_clt, A.mes, A.ano, C.nome,
                B.ids_movimentos_estatisticas,
                IF(((MONTH(C.data_entrada) = A.mes AND YEAR(C.data_entrada) = A.ano) OR (A.a6005 != 0.00) OR (C.status > 60)) && (A.sallimpo_real = 0.00 OR A.sallimpo_real IS NULL), A.sallimpo, A.sallimpo_real) AS salario_base, A.sallimpo_real,
                A.salbase, A.salliquido,
                A.dias_trab AS dias_trabalhadas,
                A.a6005 AS salario_maternidade,
                A.a5012 AS diferenca_salarial, A.a5029 AS decimo_terceiro, A.inss_dt, A.ir_dt, A.inss, A.t_inss, A.imprenda, A.a5019 AS contribuicao_sindical, 
                A.a7001 AS vale_transporte, 
                A.a8080 AS hora_extra,
                A.a5049 AS DDIR,
                A.a5035,
                A.a5037 AS desconto_ferias,
                A.a9500 AS desconto, A.a5030 AS ir_sobre_decimo, A.a5031 AS inss_sobre_decimo,
                A.salfamilia AS salario_familia,
                B.terceiro, A.rend, A.status_clt, A.valor_rescisao, A.valor_pago_rescisao, E.total_deducao,
                A.inss_rescisao, A.a5037 AS ferias, 
                /*D.dias_ferias, D.total_liquido AS valor_ferias,
                D.ir AS ir_ferias, D.inss AS inss_ferias, A.valor_pago_ferias,*/
                D.dias_ferias, D.total_remuneracoes, D.total_liquido AS valor_ferias,
                D.ir AS ir_ferias, D.inss AS inss_ferias, A.valor_pago_ferias,
                A.t_imprenda, D.pensao_alimenticia
                FROM rh_folha_proc AS A
                LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha)
                LEFT JOIN rh_clt   AS C ON(A.id_clt = C.id_clt)
                LEFT JOIN rh_recisao AS E ON(A.id_clt = E.id_clt AND A.mes = date_format(E.data_demi,'%m') AND E.`status` = 1)
                {$ferias}
                WHERE {$criteria} AND A.status = 3
                ORDER BY A.mes";
        if($_COOKIE['debug'] == 666){
            print_array('////////////////////////////////$sqls////////////////////////////////');
            print_array($sqls);
        }
        $sql = mysql_query($sqls) or die("Erro ao selecionar dados do clt");

        $count = 1;
        while ($rows = mysql_fetch_assoc($sql)) {
            if($_COOKIE['debug'] == 666){
                print_array('////////////////////////////////ARRAY: $sqls////////////////////////////////');
                print_array($rows);
            }
            $mes = sprintf("%02d", $rows['mes']);

            if (!empty($rows['salario_base'])) {
                $this->dados["0001"]['nome'] = "SALARIO";
                $this->dados["0001"]['ref'] = $rows['dias_trabalhadas'];
                if ($rows['salario_base'] != 0.00 ) {
                    if ($rows['status_clt'] > 60) {
                        
                        $tpStatusClt = (!in_array($rows['status_clt'], [69]))?"(Rescisão)":"(Licença Sem Vencimentos)";
                        $valorCltDemitido = (!in_array($rows['status_clt'], [69, 68, 67]))?$rows['salario_base']:0;
                        $this->dados["0001"][$mes] = $valorCltDemitido . " {$tpStatusClt}";
                        
                    } else {
                        $this->dados["0001"][$mes] = $rows['sallimpo_real'];
                    }
                } else {
                    $this->dados["0001"][$mes] = 0.00;
                }
                unset($tpStatusClt,$valorCltDemitido);
            }
            
            //SALÁRIO FAMÍLIA 
            if (!empty($rows['salario_familia']) && $rows['salario_familia'] != 0.00) {
                $this->dados["5022"]['nome'] = "SALÁRIO FAMÍLIA";
                $this->dados["5022"][$mes] = $rows['salario_familia'];
            }
            if (!empty($rows['total_remuneracoes'])) {
                $this->dados["5037"]['nome'] = "FÉRIAS NO MÊS";
                $this->dados["5037"][$mes] = $rows['total_remuneracoes'];
            }
            
            if (!empty($rows['pensao_alimenticia'])) {
                $this->dados["80026"]['nome'] = "PENSÃO ALIMENTÍCIA SOBRE FÉRIAS";
                $this->dados["80026"][$mes] = $rows['pensao_alimenticia'];
            }

            //SALARIO MATERNIDADE
            if (!empty($rows['salario_maternidade']) && $rows['salario_maternidade'] != 0.00) {
                $this->dados["6005"]['nome'] = "SALARIO MATERNIDADE";
                $this->dados["6005"][$mes] = $rows['salario_maternidade'];
            }

            //CONTRIBUIÇÃO SINDICAL
            if (!empty($rows['contribuicao_sindical']) && $rows['contribuicao_sindical'] != 0.00) {
                $this->dados["5019"]['nome'] = "CONTRIBUIÇÃO SINDICAL";
                $this->dados["5019"][$mes] = $rows['contribuicao_sindical'];
            }

            //HORA EXTRA
            if (!empty($rows['hora_extra']) && $rows['hora_extra'] != 0.00) {
                $this->dados["8080"]['nome'] = "HORA EXTRA";
                $this->dados["8080"][$mes] = $rows['hora_extra'];
            }

            //VERIFICA VALOR 13°
            if ($rows['terceiro'] == 1) {
                if($rows['tipo_terceiro'] == 3) { 
                    $this->dados["5029"]['nome'] = "DECIMO TERCEIRO SALARIO";
                    $this->dados["5029"][$mes] = $rows['salbase'];
                } else if ($rows['mes'] == 11) {
                    $this->dados["5029"]['nome'] = "DECIMO TERCEIRO SALARIO 1ª PARCELA";
                    $this->dados["5029"][$mes] = $rows['salliquido'];
                } else {
                    $this->dados["5029"]['nome'] = "DECIMO TERCEIRO SALARIO";
                    $this->dados["5029"][$mes] = $rows['decimo_terceiro'];
                }
            }

            //IR SOBRE 13°
            if ($rows['terceiro'] == 1 && $rows['ir_sobre_decimo'] != 0.00) {
                $this->dados["5030"]['nome'] = "IR SOBRE 13°";
                $this->dados["5030"][$mes] = $rows['ir_sobre_decimo'];
            }

            //INSS SOBRE 13°
            if ($rows['terceiro'] == 1 && $rows['inss_sobre_decimo'] != 0.00) {
                $this->dados["5031"]['nome'] = "INSS SOBRE 13°";
                $this->dados["5031"][$mes] = $rows['inss_sobre_decimo'];
            }

            //VERIFICA VALOR INSS
            if (!empty($rows['inss']) && $rows['inss'] != 0.00) {
                $this->dados["5020"]['nome'] = "INSS";
                $this->dados["5020"]['ref'] = $rows['t_inss'] * 100 . "%";
                $this->dados["5020"][$mes] = $rows['inss'];
            } else if (!empty($rows['valor_ferias']) && $rows['valor_ferias'] != 0.00) {
                $this->dados["5035"]['nome'] = "INSS SOBRE FÉRIAS";
                $this->dados["5035"][$mes] = $rows['a5035'];
            }

            if ($rows['status_clt'] > 60) {
                if (!empty($rows['inss_rescisao'])) {
                    $this->dados["5020"]['nome'] = "INSS";
                    $this->dados["5020"][$mes] = $rows['inss_rescisao'];
                }
                if (!empty($rows['desconto'])) {
                    $this->dados["9500"]['nome'] = "DESCONTO";
                    $this->dados["9500"][$mes] = $rows['total_deducao'] - $rows['inss_rescisao'];
                }
                if (!empty($rows['rend'])) {
                    $this->dados[" - "]['nome'] = "RENDIMENTOS";
                    $this->dados[" - "][$mes] = $rows['rend'];
                }
            }

            //VERIFICA DESCONTO
            if (!empty($rows['desconto']) && $rows['desconto'] != 0.00) {
                $this->dados["9500"]['nome'] = "DESCONTO";
                $this->dados["9500"][$mes] = $rows['desconto'];
            }
            
            //VALE TRANSPORTE
            if (!empty($rows['vale_transporte']) && $rows['vale_transporte'] != 0.00) {
                $this->dados["7001"]['nome'] = "DESCONTO VALE TRANSPORTE";
                $this->dados["7001"]['ref'] = "6%";
                $this->dados["7001"][$mes] = $rows['vale_transporte'];
            }

            //IMPOSTO DE RENDA
            if (!empty($rows['imprenda']) && $rows['imprenda'] != 0.00) {
                $this->dados["5021"]['nome'] = "IMPOSTO DE RENDA";
                $this->dados["5021"]['ref'] = $rows['t_imprenda'] * 100 . "%";
                $this->dados["5021"][$mes] = $rows['imprenda'];
            }

            // EDITADO RENATO 04/03/2016
            if($rows['terceiro'] == 2){
                //IMPOSTO DE RENDA SOBRE FÉRIAS
                if (!empty($rows['ir_ferias']) && $rows['ir_ferias'] != 0.00) {
                    $this->dados["5036"]['nome'] = "IR SOBRE FÉRIAS";
                    $this->dados["5036"][$mes] = $rows['ir_ferias'];
                }

                //INSS SOBRE FÉRIAS
                if (!empty($rows['inss_ferias'])) {
                    $this->dados["5035"]['nome'] = "INSS SOBRE FÉRIAS";
                    $this->dados["5035"][$mes] = $rows['inss_ferias'];
                }
            }

            //FÉRIAS
            if (!empty($rows['valor_ferias'])) {
//                $this->dados["50255"]['nome'] = "ADIANTAMENTO DE FÉRIAS";
//                $this->dados["50255"][$mes] = $rows['valor_ferias'];    
                $this->dados["80020"]['nome'] = "ADIANTAMENTO DE FÉRIAS";
                $this->dados["80020"][$mes] = $rows['valor_ferias'];    
            }
            

            //VERIFICA MOVIMENTOS
            $sql_movs = mysql_query("SELECT A.tipo_qnt,A.qnt, A.mes_mov, A.ano_mov, A.lancamento, A.id_folha, 
                    A.cod_movimento, A.tipo_movimento, A.nome_movimento, A.valor_movimento, 
                    if(A.tipo_qnt = 1,A.qnt_horas,B.percentual) as percentual
                    FROM rh_movimentos_clt AS A
                    LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)    
                    -- WHERE A.id_folha IN(2711) AND id_clt = '{$clt}'
                    WHERE id_movimento IN(" . $rows['ids_movimentos_estatisticas'] . ") AND id_clt = '{$clt}' ");
                    
            while ($d = mysql_fetch_assoc($sql_movs)) {
            
                $ref = "";
                $tipagem = "";
                //GAMBIARRAS PARA FUNCIONAR RÁPIDO
                if($d['cod_movimento'] == '9997'){
                    $ref = "5";
                }else if(($d['cod_movimento'] == '50249' || $d['cod_movimento'] == '50227' || $d['cod_movimento'] == '50252')){
                    if($d['tipo_qnt'] == 1){
                       $d['percentual'] =  substr($d['percentual'], 0, -3);
                       $tipagem = "Horas";
                    }else{
                       $tipagem = "Dias"; 
                    }
                    if(!empty($d['percentual']) && isset($d['percentual']) ){
                        $ref = $d['percentual'] . " " . $tipagem;
                    }
                    
                }else if($d['cod_movimento'] == '10011' || $d['cod_movimento'] == '10010'){
                    $ref = $d['qnt'];
                }else{
                    $ref = $d['percentual'];
                    if($ref != 0){
                        $ref = ($ref * 100) . "%";
                    }
                }
                
                if($ref == "(NULL)" && $ref == 0){
                    $ref = "";
                }
                        
                
                if ($d['lancamento'] == '2') {
                    $this->dados[$d['cod_movimento']]['nome'] = $d['nome_movimento'];
                    $this->dados[$d['cod_movimento']]['ref'] = $ref;
                    if (!empty($meses)) {
                        $this->dados[$d['cod_movimento']][sprintf("%02d", $meses)] += $d['valor_movimento'];
                    } else {
                        $this->dados[$d['cod_movimento']][sprintf("%02d", $mes)] += $d['valor_movimento']; //$count
                    }
                } else {
                    
                    $this->dados[$d['cod_movimento']]['nome'] = $d['nome_movimento'];
                    $this->dados[$d['cod_movimento']]['ref'] = $ref;
                    $this->dados[$d['cod_movimento']][sprintf("%02d", $mes)] += $d['valor_movimento'];
                }
            }
            $count++;
        }
//           unset($this->dados[5036]);
    }


    /**
     * MÉTODO QUE MONTA CABEÇALHO
     * @return type
     */
    public function getCabecalho() {
        $this->cabecalho = array(
            "01" => "JAN",
            "02" => "FEV",
            "03" => "MAR",
            "04" => "ABR",
            "05" => "MAIO",
            "06" => "JUN",
            "07" => "JUL",
            "08" => "AGO",
            "09" => "SET",
            "10" => "OUT",
            "11" => "NOV",
            "12" => "DEZ");
        //,"13" => "1 PARC. 13°", "14" => "2 PARC. 13°", "15" => "INTE. 13°"
        return $this->cabecalho;
    }

    /**
     * 
     * @return type
     */
    public function getDadosFicha() {
        return $this->dados;
    }
    
    /**
     * 
     * @param type $folha
     * @return type
     */
    public function getDadosFolhaById($folha, $status = null, $clt = null) {
        
        $_status = (!empty($status)) ? " AND A.status = {$status}" : ""; 
        $_clt = (!empty($clt)) ? " AND A.id_clt = {$clt}" : "";
        
        $query = "SELECT A.id_clt, A.id_projeto, A.nome, A.salbase, A.sallimpo, A.base_inss, A.valor_ferias, A.rend, A.base_irrf, if(dias_trab < 30, A.sallimpo_real,A.sallimpo) AS salario, C.nome AS funcao, DATE_FORMAT(B.data_entrada,'%d/%m/%Y') AS data_admissao, A.mes AS mes_proc, A.ano AS ano_proc,
                    A.a50222 AS DEP_SALARIO_FAMILIA, A.a50492 AS DEP_IRRF, A.inss, A.fgts, A.imprenda, A.imprenda, CONCAT(A.mes,'/',A.ano) AS mes_competencia, B.id_centro_custo, B.id_regiao
                    FROM rh_folha_proc AS A
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt )
                    LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
                    WHERE id_folha = '{$folha}' {$_status} {$_clt}";
        
        $clts = mysql_query($query) or die("Error ao seleciona clts por folha");
        return $clts;
    }

    /**
     * 
     * @return type
     */
    public function getMovCredito() {
        $query = "SELECT cod FROM rh_movimentos WHERE categoria = 'CREDITO'";
        $movimento = mysql_query($query) or die(mysql_error("erro de query"));
        return $movimento;
    }

    /**
     * 
     * @return type
     */
    public function getMovDebito() {
        $query = "SELECT cod FROM rh_movimentos WHERE categoria IN('DEBITO','DESCONTO', 'ENCARGOS')";
        $movimento = mysql_query($query) or die(mysql_error("erro de query"));
        return $movimento;
    }

    public function zeraArray() {
        $this->dados = array();
    }
    
    /**
     * NOVA FOLHA
     * METODO A FOLHA RH_FOLHA
     * @param type $id_folha
     * @param type $tipoRetorno 0 => resorce, 1 => array
     * @return type
     */
    public function getFolhaById($folha = null, $campos = array('*'),$tipoRetorno = 0){
        
        if($folha == null){
            $folha = $this->getFolha();
        }
        
        if(is_array($folha) && !empty($folha)){
            $id_folha = implode(",", $folha);
        }else{
            $f = array($folha);
            $id_folha = implode(",", $f);
        }
        
        try{
            $query = "SELECT " . implode(",", $campos) . " 
                FROM rh_folha AS A 
                LEFT JOIN projeto AS B ON(A.projeto = B.id_projeto)
                LEFT JOIN funcionario AS C ON(A.user = C.id_funcionario)
                LEFT JOIN regioes AS D ON(A.regiao = D.id_regiao)
                WHERE id_folha IN({$id_folha})";

            $sql = mysql_query($query) or die("Erro ao selecionar folha");

            if($tipoRetorno == 0){
                return $sql;
            }else{
                $array = array();
                while($rows = mysql_fetch_assoc($sql)){
                    $array = $rows;
                }
                return $array;
            }   
        }catch(Exception $e){
           echo $e->getMessage(); 
        } 
        
    }
    
    /**
     * 
     * @param type $folha
     * @param type $campos
     * @return type
     */
    public function getCltsByIdFolha($id_folha, $id_clt = null, $ini = null, $id_funcao = null){
        $auxClt = (!empty($id_clt) && $id_clt != 'todos') ? " AND id_clt = $id_clt " : '';
        $auxIni = (!empty($ini)) ? " LIMIT $ini, 50 " : '';
        $auxFuncao = (!empty($id_funcao)) ? " AND id_curso = $id_funcao " : '';
        
        try{
            $query = "SELECT id_clt FROM rh_folha_proc WHERE id_folha IN({$id_folha}) $auxClt $auxFuncao AND cpf != '760.835.868-87' AND status_clt NOT IN (SELECT codigo FROM rhstatus WHERE tipo = 'recisao') ORDER BY nome $auxIni";
            $sql = mysql_query($query) or die("Erro ao selecionar folha");
            while($row = mysql_fetch_assoc($sql)){
                $array[] = $row['id_clt'];
            }
        }catch(Exception $e){
           echo $e->getMessage(); 
        } 
        
        return $array;
    }
}

class ContraCheque extends FPDF{
     
    private $duplicado;
    private $tipo;
    private $pdf;
    private $dados;
        
    /**
     * 
     * @param type $dados
     * @param type $tipo define o tipo de retorno (pdf, csv, txt)
     */
    public function __construct($duplicado = false, $tipo = array("pdf")) {
        
        //VERIFICA A ORIENTAÇÃO DA PÁGINA
        $orientacao = ($duplicado) ? "L" : "P";
        $tamanho_folha = ($duplicado) ? "A4" : "A5";
        
        //OBJETO PDF
        $this->pdf = new FPDF($orientacao, "cm", $tamanho_folha);
        $this->setDuplicado($duplicado);
        $this->setTipo($tipo);
//        $this->setDados($dados);
//        echo "<pre>";
//            print_r($dados);
//        echo "</pre>";
    }
    
    public function getAnosFolha($regiao) {
        $qry = "SELECT MIN(A.ano) AS primeiro_ano, MAX(A.ano) AS ultimo_ano
            FROM rh_folha AS A
            WHERE A.regiao = '{$regiao}' AND A.status = 3";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        $this->ano_ini = $res['primeiro_ano'];
        $this->ano_fim = $res['ultimo_ano'];
    } 
    
    public function getFolhaCC($projeto, $ano){
        $qry = "SELECT *, DATE_FORMAT(A.data_inicio, '%d/%m/%Y') AS data_inicio, DATE_FORMAT(A.data_fim, '%d/%m/%Y') AS data_fim
            FROM rh_folha AS A
            WHERE A.projeto = '{$projeto}' AND A.status = '3' AND A.ano = '{$ano}'
            ORDER BY A.projeto, A.ano";
        $RE = mysql_query($qry) or die(mysql_error($qry));
        
        return $RE;
    }
    
    public function listaTodos($id_folha) {
        $RE = mysql_query("SELECT * FROM rh_folha where id_folha = '$id_folha' and status = '3' ");
        $Row = mysql_fetch_array($RE);

        $max = 50;
        $pedaco = ceil($Row['clts'] / $max);

        $a = 1;
        $maxini = $maxfim = 0;

        for ($i = 1; $i <= $pedaco; $i ++) {
            $maxfim = $maxfim + $max;
            if ($i != 1) {
                $maxini = $maxini + $max;
            }

            if ($pedaco == $i) {
                $maxfim = $Row['clts'];
            }

            $array[$i] = array(
                'maxini' => $maxini,
                'maxfim' => $maxfim
            );
        }
        return $array;
    }
    
    /**
     * MÉTODO PARA RETORNO DOS DADOS DO CLT 
     * @param type $clt
     */
    public function getContraCheque(){
//        echo "<table>";
        
        //VERIFICA CARGO ATUAL
        $cargo = $this->getCargoPeriodo($this->dados['id'], $this->dados['ano'], $this->dados['mes'], $this->dados['cargo']);
        
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->SetTopMargin(1);
        
        //EMPRESA
        $add = 0;
        $contador = ($this->duplicado) ? 2 : 1;
        for($i = 1; $i <= $contador; $i++){
            
            $totalDesconto = 0;
            $totalLiquido = 0;
            
            if($i == $this->duplicado){
               $this->pdf->SetY(1.0);  
            }
            
            //PREDEFINIÇÕES
            $altura_celula = 0.5;
            $largura_celula = 13;
            $distancia = 1.7;
            
            if($this->duplicado){
                if($i != $contador){
                    $add = 0; 
                }else{
                    $add = $largura_celula + $distancia;
                }
            }
            
            //INFORMAÇÕES DA EMPRESA
            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }
                        
            $this->pdf->Cell($largura_celula, 2.1, null, 1, '0', 'C');
            $this->pdf->Text(3.5 + $add, 1.6, $this->dados['empresa']);
            $this->pdf->Text(3.5 + $add, 2, "CNPJ: " . $this->dados['cnpj']);
            $this->pdf->SetFont('Arial', '', 7.7);
            $this->pdf->Text(3.5 + $add, 2.4, $this->dados['endereco']);
            $this->pdf->Text(3.5 + $add, 2.8, "CEP: " . $this->dados['cep']);
            $this->pdf->Text(5.8 + $add, 2.8, "TEL: " . $this->dados['telefone']);
            $this->pdf->Image($this->dados['logo'], 1.1 + $add, 1.3, 2.3, 1.4, 'gif');
            
            $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
            $this->pdf->Ln(2.1);
            
            //PREDEFINIÇÕES
            $altura_atual = 2.8;
            $altura_celula_n = $altura_celula + 0.3;
            
            //INFORMAÇÕES DE DADOS DO FUNCIONARIO
            //LINHA 2
            $this->pdf->SetFont('Arial', '', 7);
            $this->pdf->Ln($altura_celula);
            
            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }
            
            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
                        
            $this->pdf->Line(2.9 + $add, $altura_atual + 0.8, 2.9 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, $altura_atual + 0.8, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Mês: ');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->getMes($this->dados['mes']) . '/' . $this->dados['ano']);
            $this->pdf->Text(3.1 + $add, 1.1 + $altura_atual, 'Nome:');
            $this->pdf->Text(3.1 + $add, 1.5 + $altura_atual, $this->dados['nome']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Cod Funcionário: ');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['cod_funcionario']);
            
            //LINHA 3
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }           
            
            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
           
            $this->pdf->Line(11.7 + $add, $altura_atual + 0.8, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Cargo:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $cargo);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Data de Admissão:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['data_admissao']);

            //LINHA 4
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);
            
            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }
            
            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
            
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Unidade:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['unidade']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'PIS:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['pis']);

            //LINHA 5
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);
            
            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }
            
            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
            
            $this->pdf->Line(3.7 + $add, 0.8 + $altura_atual, 3.7 + $add, $altura_atual + 1.6);
            $this->pdf->Line(7.3 + $add, 0.8 + $altura_atual, 7.3 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'CPF:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['cpf']);
            $this->pdf->Text(4 + $add, 1.1 + $altura_atual, 'RG:');
            $this->pdf->Text(4 + $add, 1.5 + $altura_atual, $this->dados['rg']);
            $this->pdf->Text(7.5 + $add, 1.1 + $altura_atual, 'Carteira de Trabalho:');
            $this->pdf->Text(7.5 + $add, 1.5 + $altura_atual, $this->dados['carteira_trabalho']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Série:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['serie_carteira_trabalho']);

            //LINHA 6
            $altura_atual += 0.8;
            $this->pdf->Ln($altura_celula + 0.3);

            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }
            
            $this->pdf->Cell($largura_celula, $altura_celula_n, '', 1, '0', 'L');
            
            $this->pdf->Line(3.7 + $add, 0.8 + $altura_atual, 3.7 + $add, $altura_atual + 1.6);
            $this->pdf->Line(11.7 + $add, 0.8 + $altura_atual, 11.7 + $add, $altura_atual + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_atual, 'Banco:');
            $this->pdf->Text(1.2 + $add, 1.5 + $altura_atual, $this->dados['banco']);
            $this->pdf->Text(4 + $add, 1.1 + $altura_atual, 'Agência:');
            $this->pdf->Text(4 + $add, 1.5 + $altura_atual, $this->dados['agencia']);
            $this->pdf->Text(11.8 + $add, 1.1 + $altura_atual, 'Conta corrente:');
            $this->pdf->Text(11.8 + $add, 1.5 + $altura_atual, $this->dados['conta_corrente']);

            //LINHA 7 
            $this->pdf->Ln($altura_celula + 0.3);
            $this->pdf->SetFont('Arial', 'B', 6);

            $this->pdf->Line(2.3 + $add, 1.6 + $altura_atual, 2.3 + $add, $altura_atual + 2);
            $this->pdf->Line(8.5 + $add, 1.6 + $altura_atual, 8.5 + $add, $altura_atual + 2);
            $this->pdf->Line(10 + $add, 1.6 + $altura_atual, 10 + $add, $altura_atual + 2);
            $this->pdf->Line(12 + $add, 1.6 + $altura_atual, 12 + $add, $altura_atual + 2);
            
            if($i == 2){
                $this->pdf->SetX(15.7);
            }else{
                $this->pdf->SetX(1);
            }
            
            $this->pdf->Cell($largura_celula, $altura_celula_n - 0.4, '', 1, '0', 'L');
            
            $this->pdf->Text(1.3 + $add, 1.9 + $altura_atual, 'Código');
            $this->pdf->Text(4.6 + $add, 1.9 + $altura_atual, 'Descrição');
            $this->pdf->Text(8.7 + $add, 1.9 + $altura_atual, 'Frequência');
            $this->pdf->Text(10.4 + $add, 1.9 + $altura_atual, 'Vencimento');
            $this->pdf->Text(12.4 + $add, 1.9 + $altura_atual, 'Descontos');
            
            //MOVIMENTOS
            $this->pdf->Ln(0.6);
            
//            echo "<pre>";
//                print_r($this->dados["movimentos"]);
//            echo "</pre>";
            
            foreach($this->dados["movimentos"] as $tipo => $movimentos){
                foreach ($movimentos as $mov => $dados){
                    if($dados[$this->dados["mes"]] != "0.00"){
                        $this->pdf->Ln(0.35);

                        if($i != 2){
                            $this->pdf->SetX(1.02);
                        }else{
                            $this->pdf->SetX(15.74);
                        }    
                    
                        $this->pdf->Cell(1.3, $altura_mov, $mov, $borda, '0', 'C');
                        $this->pdf->Cell(6.2, $altura_mov, $dados["nome"], $borda, '0', 'L');
                        $this->pdf->Cell(1.5, $altura_mov, $dados["ref"], $borda, '0', 'C');
                        if($tipo == "credito"){
                            $this->pdf->Cell(2, $altura_mov, number_format($dados[$this->dados["mes"]],2,",","."), $borda, '0', 'R');
                            $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
                            $totalLiquido += $dados[$this->dados["mes"]];
                            
                            /**
                            * FEITO POR SINESIO LUIZ 
                            * PARA SITUAÇÃO DE CONTRACHEQUE DE FERIAS 
                            * 5037 - FÉRIAS NO MÊS 
                            */
//                           if($mov == 5037){
//                               $totalDesconto += $dados[$this->dados["mes"]];
//                           }
                           
                           /**
                            * FEITO POR SINESIO LUIZ 
                            * PARA SITUAÇÃO DE CONTRACHEQUE DE FERIAS 
                            * 5037 - FÉRIAS NO MÊS 
                            */
//                            if($mov == 5037){
//                                $this->pdf->Cell(1.3, $altura_mov, $mov, $borda, '0', 'C');
//                                $this->pdf->Cell(6.2, $altura_mov, $dados["nome"], $borda, '0', 'L');
//                                $this->pdf->Cell(1.5, $altura_mov, $dados["ref"], $borda, '0', 'C');
//                                $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
//                                $this->pdf->Cell(2, $altura_mov, $dados[$this->dados["mes"]], $borda, '0', 'R');
//                                $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
//                            }
                            
                        }else{
                            
                            $this->pdf->Cell(2, $altura_mov, "", $borda, '0', 'R');
                            //$this->pdf->Cell(2, $altura_mov, $dados[$this->dados["mes"]], $borda, '0', 'R');
                            $this->pdf->Cell(2, $altura_mov, number_format($dados[$this->dados["mes"]],2,",","."), $borda, '0', 'R');
                            $totalDesconto += $dados[$this->dados["mes"]];
                                                    
                            
                        }
                    }
                    
                    $this->pdf->Cell($distancia, $altura_celula, '', 0, '0', 'C');
                    
                    
                }
            }
            
            
            //DESENHANDO FUNDO DA TABELA DE MOVIMENTOS
            $altura_fundo = 6.7;
            $altura_atual += 2;
            
            if($i != $contador){
                $this->pdf->SetY(0);
                $this->pdf->Rect(15.7, $altura_atual, 1.3, $altura_fundo);
                $this->pdf->Rect(17, $altura_atual, 6.2, $altura_fundo);
                $this->pdf->Rect(23.2, $altura_atual, 1.5, $altura_fundo);
                $this->pdf->Rect(24.70, $altura_atual, 2, $altura_fundo);
                $this->pdf->Rect(26.70, $altura_atual, 2, $altura_fundo);
            }else{
                $this->pdf->Rect(1, $altura_atual, 1.3, $altura_fundo);
                $this->pdf->Rect(2.29, $altura_atual, 6.2, $altura_fundo);
                $this->pdf->Rect(8.5, $altura_atual, 1.5, $altura_fundo);
                $this->pdf->Rect(10, $altura_atual, 2, $altura_fundo);
                $this->pdf->Rect(12, $altura_atual, 2, $altura_fundo);
            }
            
            
            //LIINHA TOTAIS
            $altura_ag = 13.9;
            
            $this->pdf->Rect(1, 14.7, 13, 0.8);
            $this->pdf->Rect(15.7, 14.7, 13, 0.8);
            
            $this->pdf->Line(8.5 + $add, 0.8 + $altura_ag, 8.5 + $add, $altura_ag + 1.6);
            $this->pdf->Line(12 + $add, 0.8 + $altura_ag, 12 + $add, $altura_ag + 1.6);
            $this->pdf->Text(1.2 + $add, 1.1 + $altura_ag, 'Valor Bruto:');
            $this->pdf->Text(3.4 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalLiquido, 2, ',', '.')); //$this->dados["valor_bruto"] + $this->dados["rend"]
            $this->pdf->Text(8.7 + $add, 1.1 + $altura_ag, 'Total dos Descontos:');
            $this->pdf->Text(9.5 + $add, 1.5 + $altura_ag, "R$ " . number_format($totalDesconto, 2, ',', '.'));
            $this->pdf->Text(12.1 + $add, 1.1 + $altura_ag, 'Valor Líquido:');
            $this->pdf->Text(12.2 + $add, 1.5 + $altura_ag, "R$ " . number_format(($totalLiquido - $totalDesconto > 0) ? $totalLiquido - $totalDesconto : 0.00, 2, ",", "."));
//            print_array($this->dados);exit;
//            if($aux != $this->dados['id']) {
//            echo "<tr><td>{$this->dados['id']}</td><td>".number_format($totalLiquido - $totalDesconto, 2,',','.')."</td></tr>";
//            $aux = $this->dados['id'];
//            }
            
            //BASES
            $this->pdf->Ln($altura_celula + 0.7);
            $altura_ag += 1.2;

            $this->pdf->Rect(1, 15.7, 13, 0.8);
            $this->pdf->Rect(15.7, 15.7, 13, 0.8);
            
            $this->pdf->Line(4, 0.6 + $altura_ag, 4, $altura_ag + 1.4);
            $this->pdf->Line(8.5 + $add, 0.6 + $altura_ag, 8.5 + $add, $altura_ag + 1.4);
            $this->pdf->Line(11.3 + $add, 0.6 + $altura_ag, 11.3 + $add, $altura_ag + 1.4);
            $this->pdf->Text(1.2 + $add, 0.9 + $altura_ag, 'Salário Base:');
            $this->pdf->Text(1.5 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["valor_bruto"], 2, ',', '.'));
            $this->pdf->Text(4.3 + $add, 0.9 + $altura_ag, 'Salário:');
            $this->pdf->Text(4.6 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["salario_base"], 2, ',', '.'));
            $this->pdf->Text(8.7 + $add, 0.9 + $altura_ag, 'FGTS:');
            $this->pdf->Text(9.2 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["fgts"], 2, ',', '.'));
            $this->pdf->Text(11.5 + $add, 0.9 + $altura_ag, 'Base Calculo IRRF:');
            $this->pdf->Text(11.8 + $add, 1.3 + $altura_ag, "R$ " . number_format($this->dados["base_irrf"], 2, ",", ".") . (!empty($this->dados["dependentes"]) ? " (".$this->dados["dependentes"]." Dep.)" : ""));
            
            $this->pdf->Ln($altura_celula + 0.7);
            
            //DATA E ASSINATURA
            $this->pdf->Rect(1, 16.8, 13, 1.9);
            $this->pdf->Rect(15.7, 16.8, 13, 1.9);

            $this->pdf->SetFontSize('9');

            $y = 18.2;
            
            if($i == $contador){
                $this->pdf->Text(1.3, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
                $this->pdf->Text(1.3, $y, '____/____/_____');
                $this->pdf->Text(2, $y + 0.4, 'Data');

                $this->pdf->Text(6, $y, '__________________________________________');
                $this->pdf->Text(7.6, $y + 0.4, 'Assinatura do funcionário');
            }else{    
                $this->pdf->Text(16.2, 17.2, 'Declaro ter recebido a importância líquida discriminada acima');
                $this->pdf->Text(16.3, $y, '____/____/_____');
                $this->pdf->Text(17, $y + 0.4, 'Data');

                $this->pdf->Text(21, $y, '__________________________________________');
                $this->pdf->Text(23, $y + 0.4, 'Assinatura do funcionário');
            }
            
            
            
            
            
            //$obs = $this->getObsDeFaltaNoContraCheque($this->dados['id']);
            $this->pdf->SetFontSize('6');
            $this->pdf->Rect(1, 19, 13, 0.8);
            $this->pdf->Rect(15.7, 19, 13, 0.8);
            $this->pdf->Text(1.3, 19.5, $saudacao  . $obs);
            $this->pdf->Text(16.3, 19.5, $saudacao . $obs);
                
      }
      
//      $this->pdf->Output('as.pdf', 'I');
//        echo "</table>";
    }
    
    /**
     * Fecha o PDF
     * @param type $mes
     * @return string
     */
    public function closePdf(){
        $this->pdf->Output('as.pdf', 'I');
    }
    
    /**
     * 
     * @param type $mes
     * @return string
     */
    public function getMes($mes){
        $mes_ar = array("01" => "jan","02" => "fev","03" => "mar","04" => "abr","05" => "mai","06" => "jun","07" => "jul","08" => "ago","09" => "set","10" => "out","11" => "nov","12" => "dez");
        return $mes_ar[$mes];
    }
    
    public function getObsDeFaltaNoContraCheque($clt){
        $retorno = "";
        $query = "SELECT A.obs
                    FROM rh_movimentos_clt AS A
                    WHERE A.id_clt = '{$clt}' AND A.cod_movimento = 50249
                    AND A.id_mov = 232 AND A.`status` = 5";
        $sql = mysql_query($query);                    
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $retorno = "Faltas no(s) dia(s): " . $rows['obs'];
            }
        }
        
        return $retorno;
    }
    
    public function getCargoPeriodo($clt, $anoFolha, $meFolha, $cargoAtual){
        $query = "SELECT A.id_curso_para, A.id_curso_de, B.nome
            FROM rh_transferencias AS A
            LEFT JOIN curso AS B ON(A.id_curso_de = B.id_curso)
            WHERE id_clt = '{$clt}' 
            AND LAST_DAY(data_proc) >= LAST_DAY('$anoFolha-$meFolha-01')
            ORDER BY data_proc ASC LIMIT 1";
        
        $rows = mysql_fetch_assoc(mysql_query($query));
        
        if(!empty($rows['id_curso_de'])){
            $cargo = $rows['nome'];
        }else{
            $cargo = $cargoAtual;
        }
            
        return $cargo;
    }
        
    /**
     * 
     * @return type
     */
    public function getDuplicado() {
        return $this->duplicado;
    }
    
    /**
     * 
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * 
     * @param type $duplicado
     */
    public function setDuplicado($duplicado) {
        $this->duplicado = $duplicado;
    }
    
    /**
     * 
     * @param type $tipo
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    /**
     * 
     * @return type
     */
    public function getPdf() {
        return $this->pdf;
    }
    
    /**
     * 
     * @return type
     */
    public function getDados() {
        return $this->dados;
    }
    
    /**
     * 
     * @param type $pdf
     */
    public function setPdf($pdf) {
        $this->pdf = $pdf;
    }

    /**
     * 
     * @param type $dados
     */    
    public function setDados($dados) {
        $this->dados = $dados;
    }
    
    public function listaParticipantesContra($id_folha) {
        $query = "SELECT a.cod,a.id_folha,a.id_folha_proc,b.id_clt,b.nome,a.salliquido,a.ano,a.mes,a.id_regiao,
                (SELECT nome FROM curso WHERE id_curso = b.id_curso) AS nome_curso
                FROM rh_folha_proc AS a
                INNER JOIN rh_clt AS b ON (a.id_clt = b.id_clt)
                WHERE a.id_folha = '$id_folha' and a.status = '3' ORDER BY b.nome;";
        echo "<!-- $query -->";
        $resp = mysql_query($query) or die("Erro na query: " . $query . "\n" . mysql_error());

        while ($Row = mysql_fetch_assoc($resp)) {

            $lista[$Row['id_clt']] = $Row;
//        $ClassCLT->MostraClt($Row['id_clt']);
//        //PEGA A CURSO DO PERIODO
            $sql_transf = $this->checkCurso($Row['id_clt'], $Row['ano'], $Row['mes']);
            if (!empty($sql_transf['id_curso_de'])) {
                $lista[$Row['id_clt']]['id_curso'] = $sql_transf['id_curso_de'];
                $lista[$Row['id_clt']]['nome_curso'] = $sql_transf['curso_de'];
            }
        }
        return $lista;
    }

    protected function checkCurso($id_clt, $ano, $mes) {
        $sql_transf = "SELECT id_curso_para, id_curso_de,
                        (SELECT nome FROM curso WHERE id_curso = id_curso_de) AS curso_de,
                        (SELECT nome FROM curso WHERE id_curso = id_curso_para) AS curso_para
                        FROM rh_transferencias 
                        WHERE id_clt = $id_clt
                        AND LAST_DAY(data_proc) >= LAST_DAY('$ano-$mes-01')
                        ORDER BY data_proc ASC LIMIT 1";

        $transf = mysql_fetch_assoc(mysql_query($sql_transf));
        return $transf;
    }

}
    





/**
 * 
 */



//VARIÁVEIS DE AMBIENTE
$dadosEmpresa   = array();
$dadosFolha     = array();
$dadosClt       = array();
$dadosFolhaClt  = array();

$id_folha = 2711;
//$idClt    = 5833;

$REFolha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$RowFolha = mysql_fetch_array($REFolha);
$idfolha    = $id_folha;
$regiao = $RowFolha['regiao'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto= '$RowFolha[projeto]'");
$row_projeto= mysql_fetch_assoc($qr_projeto);
$projeto        = $row_projeto['id_projeto'];

//INSTÂNCIAS
$folha = new Folha();

//DADOS DA EMPRESA
$resourceEmpresa = getEmpresa($regiao, $projeto);
while($rowsEmpresas = mysql_fetch_assoc($resourceEmpresa)){
    $dadosEmpresa = $rowsEmpresas;
}
//
//echo "<pre>";
//    print_r($dadosEmpresa);
//echo "</pre>";
 
//DADOS DA FOLHA
$resource_folha = $folha->getFolhaById($idfolha, array('id_folha','mes','ano','terceiro','tipo_terceiro'));
while($rowsFolha = mysql_fetch_assoc($resource_folha)){
    $dadosFolha = $rowsFolha;
}

$clts = $folha->getCltsByIdFolha($idfolha, $idClt, $ini, $id_funcao);

if(count($clts) > 0){
    $contracheque = new ContraCheque(true);
}

foreach ($clts as $clt) {
    
    $dadosClt       = array();
    $dadosFolhaClt  = array();
 
    //DADOS DO CLTs
    $resource_clt = $folha->getDadosClt($clt, $dadosFolha['mes'], $dadosFolha['ano']);
    while($rowClt = mysql_fetch_assoc($resource_clt)){
        $dadosClt = $rowClt;
    } 

    //DADOS DA FOLHA PROCESSADA
    $resource_dados_folha = $folha->getDadosFolhaById($idfolha, null, $clt);
    while($rowDadosFolha = mysql_fetch_assoc($resource_dados_folha)){
        $dadosFolhaClt = $rowDadosFolha;
    }

    //DADOS FINANCEIRO
    $folha->getFichaFinanceira($clt, $dadosFolha['ano'], $dadosFolha['mes'], $dadosFolha['terceiro']);
    $movimentos = $folha->getDadosFicha();

    //MOVIMENTOS DE CRÉDITO
//    $mov_credito = array(80045);
    $mov_c = $folha->getMovCredito();
    while($rows_credito = mysql_fetch_assoc($mov_c)){
        $mov_credito[] = $rows_credito['cod'];
    }

    //MOVIMENTOS DE DEBITO
    $mov_d = $folha->getMovDebito();
    while($rows_debito = mysql_fetch_assoc($mov_d)){
        $mov_debito[] = $rows_debito['cod'];
    }

    $mov = array();
//    unset($movimentos[80020]);
//    print_array($movimentos);
    foreach ($movimentos as $key => $values){
        if(in_array($key, $mov_credito)){
            $mov["credito"][$key] = $values;
        }else if(in_array($key,$mov_debito)){
            $mov["debito"][$key] = $values;
        }
    }

    //CRIANDO UM ARRAY MAIS LIMPO
    $dados = array(
        "logo"              => "../../imagens/logomaster6.gif",
        "empresa"           => $dadosEmpresa['razao'],
        "cnpj"              => $dadosEmpresa['cnpj'],
        "endereco"          => $dadosEmpresa['endereco'],
        "cep"               => $dadosEmpresa['cep'],
        "telefone"          => $dadosEmpresa['tel'],
        "fax"               => $dadosEmpresa['fax'],
        "mes"               => $dadosFolha['mes'],
        "ano"               => $dadosFolha['ano'],
        "nome"              => $dadosClt['nome'],
        "id"                => $dadosClt['id_clt'],
        "cod_funcionario"   => $dadosClt['matricula'],
        "cargo"             => $dadosClt['nome_curso'],
        "data_admissao"     => $dadosClt['data_entrada'],
        "unidade"           => $dadosClt['locacao'],
        "pis"               => $dadosClt['pis'],
        "cpf"               => $dadosClt['cpf'],
        "rg"                => $dadosClt['rg'],
        "carteira_trabalho" => $dadosClt['ctps'],
        "serie_carteira_trabalho" => $dadosClt['serie_ctps'],
        "banco"             => $dadosClt['razao'],
        "agencia"           => $dadosClt['agencia'],
        "conta_corrente"    => $dadosClt['conta'],
        "valor_ferias"      => $dadosFolhaClt['valor_ferias'],
        "valor_bruto"       => $dadosFolhaClt['salbase'],
        "rend"              => $dadosFolhaClt['rend'],
        "base_inss"         => $dadosFolhaClt['base_inss'],
        "base_irrf"         => $dadosFolhaClt['base_irrf'],
        "dependentes"       => $dadosFolhaClt['DEP_IRRF'],
        "salario_base"      => $dadosFolhaClt['sallimpo'],
        "fgts"              => $dadosFolhaClt['fgts'],
        "movimentos"        => $mov
     );

    $contracheque->setDados($dados);
    $contracheque->getContraCheque();
}

if(count($clts) > 0){
    $contracheque->closePdf();
} else if(!empty ($id_funcao)){
    echo 'Nenhum clt para esta função';
}