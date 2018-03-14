<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InformeRendimentoClass
 *
 * @author Ramon
 */
class InformeRendimentoClass {
    
    public $tipo;       //TIPOS ('clt','aut','coop')
    public $anoBase;
    public $anoCalendario;
    public $tipoCampo = array(1 => "id_autonomo", 2 => "id_clt", 3 => "id_autonomo");
    //VARIAVEIS DE IMPRESSAO
    public $salario;
    public $inss;
    public $ir;
    public $salario13;
    public $salario13Informe;
    public $inssdt;
    public $irdt;
    public $rtdp;
    public $outros_rendimentos;
    public $pensao_alimenticia;
    public $ajuda_custo;
    public $abono_pecuniario;
    public $rescisao;
    public $rescisao_ferias;
    public $valoPlanoSaude;
    //VARIAVIES DE CONTROLE INTERNO
    public $meses;
    public $valorMes;
    public $fileName;
    public $empresa;
    public $empresacoop;
    public $participante;
    public $decimoAntigo;
    public $folhaDezembro;
    public $ids_movimentos_folhas;
    public $anoConsultaMovi;
    public $arrayPensaoAlimenticia;
    public $fpdf;
    public $sqlFolha;
    public $isConsolidado;
    public $whereDezembro;
    public $folhasEnvolvidasGeral=array();
    public $projetos=array();
    public $codigosRes;
    public $valorMesFerias;
    public $valorRescisaoMes;
    public $feriasFolhaProcMes;
    public $pensao_alimenticiaDT;
    public $rescisao_indenizacao;
    public $inssFerias;
    
    public $idsEstatisticas;
    public $idsEstatisticasMes;
    public $folhasEnvolvidas=array();

    public function __construct($id_master, $tipo = 'master') {
        if ($tipo == "master") {
            $qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$id_master' AND status = '1'");
            $qr_rhempresa = mysql_query("SELECT contador_nome FROM rhempresa WHERE id_master = '$id_master' AND status = '1' AND contador_nome != '' LIMIT 1 ");
            $this->empresa = mysql_fetch_assoc($qr_empresa);
            $rowEmpresa = mysql_fetch_assoc($qr_rhempresa);
            
            $this->empresa['contador_nome'] = $rowEmpresa['contador_nome'];
        } else {
            $qr_empresa = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$id_master' AND status_reg = '1'");
            $this->empresacoop = mysql_fetch_assoc($qr_empresa);
        }
        $this->decimoAntigo  = false;
	$this->folhaDezembro = false; 		//POR PADRÃO FALSE PARA PEGAR A FOLHA DE DEZEMBRO DO ANO ANTERIOR
        
        //ALIMENTANDO O ARRAY DE PENSAO ALIMENTICIA
        $this->arrayPensaoAlimenticia = array('6004','7009','50222','80026','7010','7011','7012','90019');
        $this->isConsolidado = false;
        
        //PEGANDO OS PROJETOS
        $rsEmpresas = montaQuery("rhempresa", "*", "id_master = {$id_master}");
        //$proNomes = array();
        foreach ($rsEmpresas as $emp) {
            $projetos[] = $emp['id_projeto'];
            //$proNomes[$emp['id_projeto']] = $pros[$emp['id_projeto']];
    }
        $this->projetos = $projetos;

        $qr_codigos = $this->getStatusRescisao(true);
        $rs_codigos = mysql_query($qr_codigos) or die("ln 290: " . mysql_error());
        $codigosRes = "";
        while($rowCodigos = mysql_fetch_assoc($rs_codigos)){
            $codigosRes .= $rowCodigos['codigo'].",";
        }
        $codigosRes = substr($codigosRes, 0, -1);
        $this->codigosRes = $codigosRes;
        
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function setAnoBase($anoBase) {
        $this->anoBase = $anoBase;
        $this->anoCalendario = $this->anoBase + 1;
        
        if ($this->folhaDezembro) {
            $this->whereDezembro = " AND A.ano = {$this->anoBase} ";
            $this->anoConsultaMovi = " AND ano_mov = '{$this->anoBase}' ";
        } else {
            $ano_ini = $this->anoBase - 1;
            $this->whereDezembro = "  AND ( (A.mes = 12 AND A.ano = {$ano_ini} AND A.terceiro = 2) OR ((A.mes <> 12 AND A.ano = {$this->anoBase}) OR (A.mes = 12 AND A.ano = {$this->anoBase} AND A.terceiro = 1)) AND YEAR(A.data_proc) = {$this->anoBase})";
            $this->anoConsultaMovi = "  AND ( (mes_mov = 12 AND ano_mov = {$ano_ini}) OR (mes_mov NOT IN(12,16,17) AND ano_mov = {$this->anoBase}) )"; //MES_MOV 12(DEZEMBRO) 16 E 17 (RESCISAO)
    }
        $this->getFolhasEnvolvidas();
    }

    public function setParticipante($participante) {
        $this->participante = $participante;
        if($participante['tipo_contratacao'] == 3){
            $qr_coop = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '{$participante['id_cooperativa']}' AND status_reg = '1'");
            $this->empresacoop = mysql_fetch_assoc($qr_coop);
        }
    }

    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function setTipoDecimo($value) {
        $this->decimoAntigo = $value;
    }
    
    public function getPensaoAlimenticia() {
        return $this->pensao_alimenticia;
    }

    public function getAjudaCusto() {
        return $this->ajuda_custo;
    }

    public function getAbonoPecuniario() {
        return $this->abono_pecuniario;
    }
    
    public function setFolhaDezembro($folhaDezembro) {
        $this->folhaDezembro = $folhaDezembro;
    }
    
    public function getParticipante($id) {
        //$sqlPart = "SELECT id_clt, nome, cpf, tipo_contratacao FROM rh_clt WHERE tipo_contratacao = '2' UNION SELECT id_autonomo, nome, cpf, tipo_contratacao FROM autonomo WHERE {$this->tipoCampo[$this->tipo]} = {$id}";
        switch ($this->tipo) {
            case 1:
            case 3:
                $sqlPart = "SELECT id_autonomo AS id_clt, nome, cpf, tipo_contratacao, id_cooperativa FROM autonomo WHERE id_autonomo = {$id}";
                break;
            case 2:
                $sqlPart = "SELECT id_clt, nome, cpf, tipo_contratacao, '0' AS id_cooperativa FROM rh_clt WHERE id_clt = {$id}";
                break;
        }
        //echo $sqlPart;
        return mysql_query($sqlPart);
    }
    
    /**
     * 
     * @param type $cpf
     * @param type $id_master
     * @return type
     */
    public function getParticipanteConsolidado($cpf, $id_master) {
        
        $sqlPart = "SELECT A.id_clt, A.nome, A.cpf, A.tipo_contratacao, '0' AS id_cooperativa FROM rh_clt AS A 
                        INNER JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                        WHERE REPLACE(REPLACE(A.cpf,'.',''),'-','') = '{$cpf}' AND 
                        B.id_master = $id_master

                        UNION

                        SELECT A.id_autonomo AS id_clt, A.nome, A.cpf, A.tipo_contratacao, A.id_cooperativa FROM autonomo AS A 
                        INNER JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                        WHERE REPLACE(REPLACE(A.cpf,'.',''),'-','') = '{$cpf}' AND 
                        B.id_master = $id_master AND A.tipo_contratacao = 1";
        
        //echo $sqlPart;
        $this->isConsolidado = true;
        return mysql_query($sqlPart);
    }
    
    /**
     * RECUPERA PARTICIPANTES
     * @param type $projeto
     * @param type $regiao
     * @return type
     */
    public function getParticipantes($projeto, $regiao) {
        //$qr_participantes = mysql_query("SELECT id_clt, nome, cpf, tipo_contratacao, '0' as id_cooperativa FROM rh_clt WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND tipo_contratacao = '2' UNION SELECT id_autonomo, nome, cpf, tipo_contratacao, id_cooperativa FROM autonomo WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND tipo_contratacao IN (1,3) ORDER BY nome ASC");
        //SELECT id_clt, nome, cpf, tipo_contratacao, '0' as id_cooperativa FROM rh_clt WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND tipo_contratacao = '2' UNION SELECT id_autonomo, nome, cpf, tipo_contratacao, id_cooperativa FROM autonomo WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND tipo_contratacao IN (1,3) ORDER BY nome ASC
        if ($this->folhaDezembro) {
            $whereAnoA = "AND B.ano = 2015";
            $whereAnoB = "D.ano = 2015";
        } else {
            $ano_anterior = $this->anoBase - 1;
            $ano_ini = $ano_anterior;
            $ano_fim = $this->anoBase;
            $whereAnoA = " AND (B.ano = {$ano_ini} AND B.mes = 12 OR B.ano = {$ano_fim} AND B.mes != 12)";
            $whereAnoB = "(D.ano = {$ano_ini} AND D.mes = 12 OR D.ano = {$ano_fim} AND D.mes != 12)";
        }
        
        $qr_participantes = mysql_query("
                        SELECT A.id_clt, A.nome, A.cpf, A.tipo_contratacao, '0' as id_cooperativa FROM rh_clt AS A
                            LEFT JOIN rh_folha_proc AS B ON (A.id_clt = B.id_clt)
                            WHERE A.id_projeto = '{$projeto}' AND A.id_regiao = '{$regiao}' AND A.tipo_contratacao = '2' {$whereAnoA} GROUP BY A.cpf
                            UNION 
                            SELECT C.id_autonomo, C.nome, C.cpf, C.tipo_contratacao, C.id_cooperativa FROM autonomo AS C
                            LEFT JOIN folha_cooperado AS D ON (C.id_autonomo = D.id_autonomo)
                            WHERE C.id_projeto = '{$projeto}' AND C.id_regiao = '{$regiao}' AND C.status = '1' AND  
                            (({$whereAnoB}  AND C.tipo_contratacao = 3 AND D.`status` = 3) OR C.tipo_contratacao = 1)
                            ORDER BY nome ASC;");
        return $qr_participantes;
    }
    
    
    /**
     * METODO PARA SELECIONAR CLTs 
     * @param type $projetos
     * @param type $limitado
     * @param type $dirf
     * @return string
     */
    public function getClts($projetos,$limitado,$whereDezembro,$dirf = false,$id_clt=false){
        $addWhwew = "";
        if($id_clt!==false){
            $addWhwew = " AND D.id_clt = {$id_clt}";
        }
        $sql_clts = "SELECT REPLACE(REPLACE(D.cpf,'-',''), '.','') as cpf, D.nome, D.id_clt, D.id_projeto
                                FROM rh_folha as A
                                INNER JOIN regioes as B ON (B.id_regiao = A.regiao)
                                INNER JOIN rh_folha_proc AS C ON (C.id_folha = A.id_folha)
                                INNER JOIN rh_clt as D ON (D.id_clt = C.id_clt)
                                WHERE A.projeto IN (" . implode(",", $projetos) . ") AND A.status = 3 AND C.status = 3 {$addWhwew} 
                                $whereDezembro
                                GROUP BY REPLACE(REPLACE(D.cpf,'-',''), '.','')
                                ORDER BY REPLACE(REPLACE(D.cpf,'-',''), '.','') ASC {$limitado}";
                                //-- AND D.id_clt = 7622 
                                
        if($dirf){
            return $sql_clts;
        }                        
        
    }
    
    
    /**
     * METODO COM AS FOLHAS ENVOLVIDAS PARA DIRF
     * @param type $projetos
     * @param type $whereDezembro
     * @param type $dirf
     * @return string
     */
    public function getFolhas($projetos,$whereDezembro,$dirf = false){
        $sql_folha = "SELECT id_folha,mes,ano,ids_movimentos_estatisticas,terceiro,projeto
                        FROM rh_folha as A 
                        WHERE A.projeto IN (" . implode(",", $projetos) . ") AND A.status = 3 
                          $whereDezembro";
        
        if($dirf){
            return $sql_folha;
        }
    }
    
    /**
     * METODO PARA RETORNAR OS REGISTROS DE VALORES 
     * DO MASTER PARA O HEADER DO ARQUIVO TXT
     * @param type $master
     * @param type $dirf
     * @return type
     */
    public function getMaster($master, $dirf = false){
        $sql_master = "SELECT B.nome, REPLACE(REPLACE(B.cpf,'.',''),'-','') as cpf,
            REPLACE(REPLACE(REPLACE(B.cnpj,'.',''),'/',''),'-','') as cnpj,
            B.responsavel, B.razao,
            SUBSTR(REPLACE(REPLACE(B.tel, '(',''),')',''),1,2) as ddd,
            REPLACE(SUBSTR(B.tel,5),'-','') as telefone,
            REPLACE(REPLACE(B.contador_cpf,'.',''),'-','') as contador_cpf, 
            B.contador_nome, B.contador_tel_ddd,B.contador_tel_num, B.contador_fax, B.contador_email 
            FROM regioes as A
            INNER JOIN rhempresa as B ON (A.id_regiao = B.id_regiao)
            WHERE A.id_master = '{$master}' AND A.sigla = 'AD'";
            
        if($dirf){
            return $sql_master;
        }
    }
    
    /**
     * RETORNA OS TIPOS DE STATUS DE RESICSAO
     */
    public function getStatusRescisao($dirf = false){
        $sql_status = "SELECT codigo FROM rhstatus WHERE tipo = 'recisao'";
        if($dirf){
            return $sql_status;
        }
    }

    /**
     * ESSE METODO VERIFICA AUTONOMO, CLT E COOPERADO
     * @param type $ids_clts
     * @param type $ano_anterior
     * @param type $folhasEnvolvidas
     * @param type $codigosRes
     * @param type $dirf
     * @return type
     */
    public function getDadosFolhas($ids_clts,$dirf = false) {
        $ano_anterior = $this->anoBase - 1;
        
        if ($this->folhaDezembro) {
            $whereDezembro = " AND A.ano = {$this->anoBase} ";
            $this->anoConsultaMovi = " AND ano_mov = '{$this->anoBase}' ";
        } else {
            $ano_ini = $ano_anterior;
            $ano_fim = $this->anoBase;
            
            $data_roc = ($this->tipo==3) ? "" : "YEAR(A.data_proc) = {$ano_fim} AND";
            
            $whereDezembro = "  AND ((A.mes = 12 AND A.ano = {$ano_ini}) OR ({$data_roc} A.ano = {$ano_fim} AND (A.mes <> 12 OR (A.mes = 12 AND A.terceiro = 1)))) ";
            $this->anoConsultaMovi = "  AND ( (mes_mov = 12 AND ano_mov = {$ano_ini}) OR (mes_mov NOT IN(12,16,17) AND ano_mov = {$ano_fim}) )"; //MES_MOV 12(DEZEMBRO) 16 E 17 (RESCISAO)
        }

        $_whereAuto = " AND A.id_autonomo IN ({$ids_clts})";
        if(empty($ids_clts)){
            $_whereAuto = null;
        }
        
        $_whereEsta = " AND A.id_estagiario IN ({$ids_clts})";
        if(empty($ids_clts)){
            $_whereEsta = null;
        }

        switch ($this->tipo) {
            case 1:
                //AUTONOMO
                $sqlFol = "SELECT
                            A.id_rpa,A.id_autonomo,A.data_geracao,
                            SUM(A.valor) AS valor,
                            SUM(A.valor_inss) AS valor_inss,
                            SUM(A.valor_ir) AS valor_ir,
                            SUM(A.valor_liquido) AS valor_liquido,
                            DATE_FORMAT(A.data_geracao, '%m') as mes,
                            B.nome,B.cpf
                            FROM rpa_autonomo AS A
                            INNER JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
                            LEFT JOIN rpa_saida_assoc AS C ON (C.id_rpa = A.id_rpa AND C.tipo_vinculo = 1)
                            LEFT JOIN saida AS D ON (D.id_saida = C.id_saida)
                            WHERE YEAR( A.data_geracao ) = {$this->anoBase} AND B.id_projeto IN (" . implode(",", $this->projetos) . ") AND D.`status` IN (1,2) {$_whereAuto}
                            GROUP BY REPLACE(REPLACE(cpf,'-',''), '.',''),mes
                            ORDER BY REPLACE(REPLACE(B.cpf,'-',''), '.','') ASC, A.data_geracao DESC";
                break;
            case 1.1:
                $sqlFol = "SELECT
                                A.id_rpa,A.id_autonomo,A.data_geracao,
                                SUM(A.valor) AS valor,
                                SUM(A.valor_inss) AS valor_inss,
                                SUM(A.valor_ir) AS valor_ir,
                                SUM(A.valor_liquido) AS valor_liquido,
                                DATE_FORMAT(A.data_geracao, '%m') as mes,
                                B.nome,B.cpf
                                FROM rpa_autonomo AS A
                                INNER JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
                                WHERE YEAR( A.data_geracao ) = {$this->anoBase} {$_whereAuto}
                                GROUP BY REPLACE(REPLACE(B.cpf,'-',''), '.',''),mes ";
                break;
            case 2:
                
                //CLT
                $sqlFol = "SELECT *,IF(status_clt=50, (sallimpo+salbase),salbase) AS salbaseCorreto,
                        base_inss AS salbaseCorretoBinss,
                        IF(id_ferias>0, 1, 0) AS feriasMes,
                        IF(id_ferias>0, (base_inss - valor_ferias), base_inss) AS base_inss_edit 
                   FROM  (
                   SELECT	A.id_folha,A.mes,A.ano,A.projeto,A.terceiro,B.id_clt,B.status_clt,B.valor_ferias,
                           IF(A.ano={$ano_anterior},0,CAST(A.mes AS signed)) as mesEdit,
                           SUM(sallimpo) AS sallimpo,
                           SUM(sallimpo_real) AS sallimpo_real,
                           SUM(salbase) AS salbase,
                           SUM(B.inss) AS inss,
                           SUM(B.a5049) AS a5049,
                           SUM(B.a5021) AS a5021,
                           SUM(B.a5036) AS a5036,
                           SUM(B.a5020) AS a5020,
                           SUM(B.a5035) AS a5035,
                           SUM(B.base_inss) AS base_inss,
                           SUM(B.dias_trab) AS dias_trab,
                           A.ids_movimentos_estatisticas,
                           C.id_ferias

                       FROM  rh_folha as A
                 INNER JOIN  rh_folha_proc as B ON (B.id_folha = A.id_folha)
                 LEFT JOIN   rh_ferias AS C ON (B.id_clt = C.id_clt AND C.`status` = 1 AND C.mes = B.mes AND B.ano = C.ano)
                      WHERE  B.id_clt IN ({$ids_clts}) AND A.status = 3 
                        AND  A.id_folha IN (" . implode(",", $this->folhasEnvolvidasGeral) . ") 
                        AND  A.terceiro = 2 AND B.status = 3 AND B.status_clt NOT IN ({$this->codigosRes})
                   GROUP BY  A.mes
                        ) AS temp";                   
                    
                break;
            case 3:
                //COOPERADO
                $sqlFol = "SELECT B.*, A.terceiro AS terceiro ,
                            (CAST(A.adicional AS decimal(12,2)) + A.salario) AS valor,
                            A.inss AS valor_inss, 
                            A.irrf AS valor_ir
                            FROM folha_cooperado as A 
                            INNER JOIN  folhas as B ON (A.id_folha = B.id_folha)
                            WHERE  A.id_autonomo IN({$ids_clts}) $whereDezembro AND A.status = 3 AND B.status = 3"; //REMOVIDO DEVIDO A TRANSFERENCIAS (AND B.coop = {$this->participante['id_cooperativa']} )
                break;
            case 4:
                
                //ESTAGIARIO
                $sqlFol = "SELECT
                                A.id_rpa_estagiario,A.id_estagiario,A.data_geracao,
                                SUM(A.valor) AS valor,
                                SUM(A.valor_inss) AS valor_inss,
                                SUM(A.valor_ir) AS valor_ir,
                                SUM(A.valor_liquido) AS valor_liquido,
                                DATE_FORMAT(A.data_geracao, '%m') as mes,
                                B.nome,B.cpf
                                FROM rpa_estagiario AS A
                                LEFT JOIN estagiario AS B ON (A.id_estagiario = B.id_estagiario)
                                WHERE YEAR( A.data_geracao ) = {$this->anoBase} $_whereEsta
                                GROUP BY REPLACE(REPLACE(B.cpf,'-',''), '.',''),mes ";
                
                break;
        }
        
        /**
         * FEITO POR: SINÉSIO LUIZ 
         * FAZ O RETORNO DA QUERY PARA O METODO
         */
        if($dirf){
            return $sqlFol;
        }
        
        //exit($sqlFol);
        $this->sqlFolha = $sqlFol;
        $qr_folha = mysql_query($sqlFol);
        $ids_movimentos_folhas = "";
        $this->feriasFolhaProcMes = null;

        while ($folha_individual = mysql_fetch_assoc($qr_folha)) {

            $ids_movimentos_folhas .= $folha_individual['ids_movimentos_estatisticas'] . ",";

            $mesF = intval($folha_individual['mes']);
            switch ($this->tipo) {
                case 1:
                case 3:
                    if(!$this->isConsolidado){
                        $this->salario13 = 0;
                        $this->inssdt = 0;
                        $this->irdt = 0;
                        $this->rtdp = 0;
                        $this->outros_rendimentos = 0;
                        $this->pensao_alimenticia = 0;
                        $this->ajuda_custo = 0;
                    }
                    $this->salario += $folha_individual['valor'];
                    $this->valorMes[$mesF]['salario'] = $folha_individual['valor'];
                    
                    $this->inss += $folha_individual['valor_inss'];
                    $this->ir += $folha_individual['valor_ir'];
                    //$fgts   = $fgts + $folha_individual['valor_inss'];
                    break;
                case 2:
                    
                    $mesEdit = intval($folha_individual['mesEdit']);
                    if($folha_individual['dias_trab'] == 0 && $folha_individual['feriasMes'] == 1){
                        $salb = 0;
                    }else{
                        $salb = $folha_individual['base_inss_edit'];
                        $salb = ($salb < 0) ? 0 : $salb;
                    }
                    
                    /*RESOLVENDO CASO DE FERIAS QUEBRADA DA WANDA GUEDES*/
                    if($folha_individual['feriasMes'] == 1){
                        $this->feriasFolhaProcMes = $mesEdit;
                    }
                    //TALVEZ O MESEDIT ESTEJA VINDO VALOR DIFERENTE DO INDEX Q RODA A DIRF
                    if(($mesEdit-1) === $this->feriasFolhaProcMes){
                        $salb = $folha_individual['salbase'];
                        $salb = ($salb < 0) ? 0 : $salb;
                    }
                    
                    $this->salario += $salb;
                    if ($salb > 0) {
                        $this->valorMes[$mesEdit]['salario'] = $salb;
                        $this->valorMes[$mesEdit]['ir'] = $folha_individual['a5021'];
                        $this->valorMes[$mesEdit]['inss'] = $folha_individual['inss'];
                        //TIREI LA DE BAIXO E COLOQUEI AQUI PARA TESTAR - 27/02/2016 FUNCIONOU !!
                        $this->inss += $folha_individual['inss'];
                        $this->ir += $folha_individual['a5021'];
                    }
                    
                    $this->meses[] = $mesEdit;
                    $this->valorMes[$mesEdit]['id_folha'] = $folha_individual['id_folha'];
                    $this->valorMes[$mesEdit]['id_clt'] = $folha_individual['id_clt'];
                    
                    //PARA IGUALAR COM INDEX QUE RODOU A DIRF, VOU REMOVER O IF
                    //echo $mesEdit."-";
                    //if($folha_individual['feriasMes'] == 0 || $folha_individual['inss'] > 0){
                        //echo $folha_individual['inss']."<br>";
                        //$this->inss += $folha_individual['inss'];
                        //$this->ir += $folha_individual['a5021'];
                    //}

                    //$this->pensao_alimenticia += $folha_individual['a6004'] + $folha_individual['a7009'];
                    //$this->ajuda_custo += $folha_individual['5011'];
                    break;
            }
            
            $this->idsEstatisticas[$folha_individual['id_folha']] = $folha_individual['ids_movimentos_estatisticas'];
            $this->idsEstatisticasMes[$folha_individual['id_folha']]['mes'] = $folha_individual['mes'];
            $this->idsEstatisticasMes[$folha_individual['id_folha']]['flag'] = $folha_individual['terceiro'];
            $this->folhasEnvolvidas[] = $folha_individual['id_folha'];
            unset($salb);
        }
        
        $this->folhasEnvolvidas = array_filter($this->folhasEnvolvidas);        //REMOVE VAZIOS E NULLOS
        
        $this->ids_movimentos_folhas = $this->normalizaIdsEstatisticas($ids_movimentos_folhas);
    }
    
    /**
     * ESSE METODO NAO ESTA SENDO UTILIZADO NO MOMENTO PARA A DIRF
     * @param type $id
     */
    public function getDadosRescisao($id) {
        //"SELECT * FROM rh_recisao WHERE id_clt = '$id' AND year(data_demi) = '$ano_base' AND motivo IN (60,61,62,80,81,100)"
        $sql_rescisa = "SELECT *,total_liquido,mes_demissao,id_clt,ferias,dt_salario,
                                IF(total_liquido = 0 , 0, (total_rendimento - ferias - aviso_valor) )AS rendimentos,
                                (inss_ferias + previdencia_ss) AS total_inss,
                                (inss_ferias + inss_ss) AS total_inss2,
                                (ir_ss + ir_ferias) AS total_ir,
                                aviso_valor + ferias AS outros 
                                FROM (
                                        SELECT 
                                        total_liquido,saldo_salario,total_rendimento,dt_salario,
                                        IF(motivo!=65,aviso_valor,0)aviso_valor,
                                        IF(inss_ss IS NULL,0.00,inss_ss) AS inss_ss, 
                                        IF(inss_dt IS NULL,0.00,inss_dt) AS inss_dt,  
                                        IF(inss_ferias IS NULL,0.00,inss_ferias) AS inss_ferias,
                                        IF(previdencia_ss IS NULL,0.00,previdencia_ss) AS previdencia_ss, 
                                        IF(previdencia_dt IS NULL,0.00,previdencia_dt) AS previdencia_dt,
                                        IF(ir_ss IS NULL,0.00,ir_ss) AS ir_ss, 
                                        IF(ir_dt IS NULL,0.00,ir_dt) AS ir_dt,
                                        IF(ir_ferias IS NULL,0.00,ir_ferias) AS ir_ferias,
                                        (ferias_vencidas+umterco_fv+ferias_pr+umterco_fp+um_terco_ferias_dobro+fv_dobro+ferias_aviso_indenizado) as ferias,
                                        MONTH(data_demi) AS mes_demissao, 
                                        data_demi, 
                                        id_clt
                                        FROM rh_recisao
                                        WHERE id_clt = $id AND YEAR(data_demi) = {$this->anoBase} AND STATUS = 1
                                 ) AS temp";
        
        $this->sqlRescisao = $sql_rescisa;
        $qr_rescisao = mysql_query($sql_rescisa);
        $total_rescisao = mysql_num_rows($qr_rescisao);
        $this->rescisao = 0;
        if (!empty($total_rescisao)) {
            $row_rescisao = mysql_fetch_assoc($qr_rescisao);
            //MESMO CASO QUE ACONTECEU NA TABELA CLT COM O CMAPO DE DATA DE DEMISSAO
            //TEM 2 CAMPOS DE INSS 13 E INSS SALDO DE SALARIO
            //NÃO SEI PQ MAS AS VEZES UM VEM E O OUTRO NÃO
            //E AS VEZES VEM VALOR NOS 2 CAMPOS
            if ($row_rescisao['total_inss'] > 0) {
                $inssResci = $row_rescisao['total_inss'];
            } else {
                $inssResci = $row_rescisao['total_inss2'];
            }
            
            $this->rescisao = $row_rescisao['rendimentos'];
            $this->rescisao_ferias = $row_rescisao['outros']; //AVISO PREVIO INDENIZADO TBM ENTRA COMO OUTROS
            $this->outros_rendimentos = 0;
            $irResci = $row_rescisao['total_ir'];
            
            if($this->rescisao <= 0){
                if(!$this->isConsolidado){
                    $this->rescisao_ferias = 0;
                    $inssResci = 0;
                    $irResci = 0;
                    $this->rescisao = 0;
                }
            }
            //$outros_rendimentos = $row_rescisao['saldo_salario'] - $inssResci;
            //EM UMA FOLHA DO IDR O CARA ENTROU EM UMA FOLHA DPS DA RESCISAO
            //E TAMBEM NA FOLHA DO MES DA RESCISAO
            //POR ISSO VERIFICO SE NO MES DA RESCISAO TEM VALOR DE FOLHA
            //SE SIM EU DIMINUO O VALOR TOTAL
            /*$mesResci = intval($row_rescisao['mes_demissao']);
            if (in_array($mesResci, $this->meses)) {
                $this->salario -= $this->valorMes[$mesResci]['salario'];
                $this->ir -= $this->valorMes[$mesResci]['ir'];
            }*/
            $this->inss += $inssResci;
            $this->ir += $irResci;
            $this->salario += $this->rescisao;
            
            $valDt = $row_rescisao['dt_salario'] - $row_rescisao['inss_dt'] - $row_rescisao['ir_dt'];
            $this->salario13 += $valDt;
            $this->inssdt += $row_rescisao['inss_dt'];
            $this->irdt += $row_rescisao['ir_dt'];
            $this->rtdp += 0;
        }
    }
    
    /**
     * FEITO EM CIMA DA ESPECIFICAÇÃO DO CONTADOR, QUE SÓ CONSEGUIMOS DESENVOLVER APÓS A CONTRATAÇÃO DO CONTADOR DA F71
     * MÉTODO CRIADO EM 01/04/2015 POR RAMON
     * @param type $id
     * @autor Ramon Lima
     * @date 01/04/2015
     */
    public function getDadosRescisao2015($id, $dirf = false){
                
        $sql_rescisa = "SELECT *,mes_demissao,id_clt,ferias,dt_salario, 
                        IF(total_liquido = 0 , 0, total_rendimento) AS rendimentos,
                        (inss_ferias + previdencia_ss) AS total_inss, 
                        (inss_ferias + inss_ss) AS total_inss2, 
                        (ir_ss + ir_ferias) AS total_ir,
                        (valor_multa_indenizado + aviso_valor + indenizado) as valor_indenizado

                        FROM (
                                SELECT id_recisao,total_liquido,saldo_salario,total_rendimento,dt_salario, base_inss_13, rescisao_complementar, terceiro_ss,
                                IF(motivo!=65,aviso_valor,0) aviso_valor, 
                                IF(motivo =64,a479,0) valor_multa_indenizado, 
                                IF(motivo = 61 AND aviso = 'indenizado',(base_inss_ss - aviso_valor),base_inss_ss) AS base_inss_ss, 
                                IF(inss_ss IS NULL,0.00,inss_ss) AS inss_ss, 
                                IF(inss_dt IS NULL,0.00,inss_dt) AS inss_dt, 
                                IF(inss_ferias IS NULL,0.00,inss_ferias) AS inss_ferias, 
                                IF(previdencia_ss IS NULL,0.00,previdencia_ss) AS previdencia_ss, 
                                IF(previdencia_dt IS NULL,0.00,previdencia_dt) AS previdencia_dt, 
                                IF(ir_ss IS NULL,0.00,ir_ss) AS ir_ss, 
                                IF(ir_dt IS NULL,0.00,ir_dt) AS ir_dt, 
                                IF(ir_ferias IS NULL,0.00,ir_ferias) AS ir_ferias, 
                                IF(adiantamento_13 IS NULL, 0.00,adiantamento_13) AS adiantamento_13, 
                                (ferias_vencidas+umterco_fv+ferias_pr+umterco_fp+um_terco_ferias_dobro+fv_dobro) AS ferias, 
                                (ferias_aviso_indenizado + umterco_ferias_aviso_indenizado + a477 + lei_12_506) AS indenizado,
                                sal_familia, MONTH(data_demi) AS mes_demissao, data_demi, id_clt
                                FROM rh_recisao
                                WHERE id_clt IN ({$id}) AND YEAR(data_demi) = {$this->anoBase} AND status = 1 -- AND rescisao_complementar = 0
                             ) AS temp";
        
        /**
         * FEITO POR: SINÉSIO LUIZ 
         * FAZ O RETORNO DA QUERY PARA O METODO
         */                        
        if($dirf){
            return $sql_rescisa;
        }
        
        /*echo "<pre>";
        print_r($sql_rescisa);
        echo "</pre>";*/
        
        $qr_rescisao = mysql_query($sql_rescisa);
        $total_rescisao = mysql_num_rows($qr_rescisao);
        $this->rescisao = 0;
        if (!empty($total_rescisao)) {
            while($row_rescisao = mysql_fetch_assoc($qr_rescisao)){
            
                $inssResci = $row_rescisao['inss_ss'];// + $row_rescisao['inss_dt'];
                $irResci = $row_rescisao['ir_ss'];
                $this->outros_rendimentos = 0;
                
                //SÓ BUSCA UMA VEZ, SE TIVER RESCISAO COMPLEMENTAR NÃO BUSCA NOVAMENTE
                if($row_rescisao['rescisao_complementar'] == 0){
                    $sql_movs_MediasSubRogoRescisao = $this->getRendimentosMediasSubRogoRescisao($id,true);
                    $rs_movs_MediasSubRogoRescisao = mysql_query($sql_movs_MediasSubRogoRescisao);
                    $arrMovsMediasSubRogoRescisao = array();
                    if(mysql_num_rows($rs_movs_MediasSubRogoRescisao) > 0){

                        while($row_movs_indenizados = mysql_fetch_assoc($rs_movs_MediasSubRogoRescisao)){
                            $arrMovsMediasSubRogoRescisao[$row_movs_indenizados['cod_movimento']] = $row_movs_indenizados;
                        }
                    }

                    //MÉDIAS INDENIZADAS
                    $arr_movsMediasIndenizados = array("90030","90055","90056","90057");
                    $valorMovsMediasInden = 0;
                    foreach($arr_movsMediasIndenizados as $movs){
                        if(array_key_exists($movs,$arrMovsMediasSubRogoRescisao)){
                            $valorMovsMediasInden += $arrMovsMediasSubRogoRescisao[$movs]['valor_movimento'];
                        }
                    }

                    //MÉDIAS DE 13
                    $arr_movsMediasDT = array("90086");
                    $valorMovsMediasDT = 0;
                    foreach($arr_movsMediasDT as $movs){
                        if(array_key_exists($movs,$arrMovsMediasSubRogoRescisao)){
                            $valorMovsMediasDT += $arrMovsMediasSubRogoRescisao[$movs]['valor_movimento'];
                        }
                    }

                    //MÉDIAS FERIAS
                    $arr_movsMediasFerias = array("90034","90035"); 
                    $valorMovsMediasFerias = 0;
                    foreach($arr_movsMediasFerias as $movs){
                        if(array_key_exists($movs,$arrMovsMediasSubRogoRescisao)){
                            $valorMovsMediasFerias += $arrMovsMediasSubRogoRescisao[$movs]['valor_movimento'];
                        }
                    }
                }

                if($row_rescisao['rendimentos'] <= 0){
                    $inssResci = 0;
                    $irResci = 0;
                    $this->rescisao = 0;
                }else{
                    //RENDIMENTO TRIBUTAVEL É TODO O RENDIMENTO BRUTO - VALORES DE INDENIZAÇÃO - 13 SALARIO - MOVIMENTOS DE AJUDA DE CUSTO , VT E VT REEMBOLSO
                    //NESSE CASO JA PEGA VALORES DE SALDO DE SALARIO, FÉRIAS ENTRE OUTROS MOVIMENTOS JA CALCULADOS COMO RENDIMENTO...

                    $movimentosSemRT = 0; //PEGAR MOVIMENTOS ESPECIFICOS, (74,82,107) AJUDA DE CUSTO , VT E VT REEMBOLSO
                    //$this->rescisao = $row_rescisao['rendimentos'] - $row_rescisao['valor_indenizado'] - $row_rescisao['dt_salario'] - $movimentosSemRT;
                    $this->rescisao = $row_rescisao['base_inss_ss'];// + $row_rescisao['ferias'] + $valorMovsMediasFerias; 
                }
                
                /**
                 * By Ramon 02/02/2017
                 * Dia tenso, não estou achando o valor q preciso tirar da parte de indenizados
                 * para liberar logo o Sena vou por isso na mão, se algum dia eu tiver tempo eu procuro melhor
                 * mais ja estou a 2 dias para liberar essa joça... fé em Deus...
                 */
                
                /*if($id == "4264"){
                    $row_rescisao['valor_indenizado'] = $row_rescisao['valor_indenizado'] - 1466.43;
                }*/
                
                //$RESC_indenizado     = $row_rescisao['valor_indenizado'] + $valorMovsMediasInden;
                $this->rescisao_indenizacao = $row_rescisao['valor_indenizado'] + $valorMovsMediasInden + $row_rescisao['ferias'] + $valorMovsMediasFerias;
                
                /*
                $RESC_rendimentos    = $row_rescisao['base_inss_ss'];// + $row_rescisao['ferias'] + $valorMovsMediasFerias; 
                $RESC_indenizado     = $row_rescisao['valor_indenizado'] + $valorMovsMediasInden + $row_rescisao['ferias'] + $valorMovsMediasFerias; 
                $RESC_inss           = $row_rescisao['inss_ss'];
                $RESC_ir             = $row_rescisao['ir_ss'];*/
                
                $this->inss += $inssResci;
                $this->ir += $irResci;
                $this->salario += $this->rescisao;
                $this->valorRescisaoMes[$row_rescisao['mes_demissao']] = $this->rescisao;
                //$this->valorMesFerias[$row_rescisao['mes_demissao']] = $row_rescisao['ferias'];

                //APÓS GERAR O INFORME PELO PROGRAMA DA DIRF, VERIFIQUEI QUE O VALOR MOSTRADO É ESSE CALCULO
                //$valDt = $row_rescisao['dt_salario'] + $valorMovsMediasDT - $row_rescisao['inss_dt'] - $row_rescisao['ir_dt'];
                //$valDt = $row_rescisao['dt_salario'] + $valorMovsMediasDT - $row_rescisao['adiantamento_13'];
                $valDt = $row_rescisao['base_inss_13'] + $valorMovsMediasDT - $row_rescisao['adiantamento_13'];
                $this->salario13 += $valDt;
                $this->inssdt += $row_rescisao['inss_dt'];
                $this->irdt += $row_rescisao['ir_dt'];
                $this->rtdp += 0;
                //$this->salario13Informe += $valDt;  //PARA 2016 UTILIZAR ESSA ATRIBUIÇÃO
                //GAMBIARRA PARA INFORMES DE 2015... DESCONTAR 2 VEZES, POIS A BASE FOI PARA A DIRF JÁ FOI DESCONTADA IFFR E INSS
                //$this->salario13Informe += $valDt - $this->inssdt - $this->irdt;
                
                /*if($_COOKIE['logado'] == 158){
                    echo "Dados 13 na rescisao<br>";
                    echo "<pre>";
                        print_r($this->salario13);
                        echo "<br>";
                        print_r($this->inssdt); 
                        echo "<br>";
                        print_r($this->rtdp);
                        echo "<br>";
                        print_r($this->irdt);
                        echo "<br>";
                        print_r($this->salario13Informe);
                    echo "</pre>";
                }*/
            }
        }
    }
    
    /**
     * METODO DE FERIAS
     * @param type $id
     * @param type $dirf
     * @return type
     */
    public function getDadosFerias($id, $dirf=false) {
        
        $sql_ferias = "SELECT *,
                            IF(total_remuneracoes > adiantamento_mov,(total_remuneracoes - adiantamento_mov),total_remuneracoes) as valor_ferias 
                            FROM (
                            SELECT A.id_ferias,(A.total_remuneracoes - A.umterco_abono_pecuniario) as total_remuneracoes, A.inss, A.ir, A.id_clt, A.abono_pecuniario, A.pensao_alimenticia, 
                                if(MONTH(C.data_vencimento) IS NOT NULL, LPAD(MONTH(C.data_vencimento),2,0), A.mes) AS mes,
                                (SELECT SUM(valor_movimento) FROM rh_movimentos_clt WHERE id_clt IN({$id}) AND mes_mov = 17 AND cod_movimento = '80030') as adiantamento_mov,
                                A.umterco_abono_pecuniario, (A.abono_pecuniario + A.umterco_abono_pecuniario) as total_abono
                            FROM rh_ferias AS A
                            LEFT JOIN pagamentos_especifico AS B ON(A.mes = B.mes AND A.ano = B.ano AND B.id_clt = A.id_clt)
                            LEFT JOIN saida AS C ON(B.id_saida = C.id_saida AND C.`status` = 2)
                            WHERE A.id_clt IN({$id}) AND A.ano = {$this->anoBase} AND A.status = '1') AS temp";
        
        /**
        * FEITO POR: SINÉSIO LUIZ 
        * FAZ O RETORNO DA QUERY PARA O METODO
        */
        if($dirf){
            return $sql_ferias;
        }                    
                            
        $qr_ferias = mysql_query($sql_ferias) or die("getDadosFerias: " . mysql_error()."<!-- SQL: ".$sql_ferias." -->");
        
        //echo "<br/>INI FE: ".$this->inss."<br/>";
        //echo $sql_ferias;
        if (mysql_num_rows($qr_ferias) != 0) {
            $legendaPensaoFerias = 8;
            
            while ($row_ferias = mysql_fetch_assoc($qr_ferias)) {
                $intMesFerias = (int)$row_ferias['mes'];
                $this->inss += $row_ferias['inss'];
                $this->ir += $row_ferias['ir'];
                $this->salario += $row_ferias['total_remuneracoes'];
                $this->valorMesFerias[$intMesFerias] = $row_ferias['total_remuneracoes'];
                $this->inssFerias += $row_ferias['inss'];
                
                //BUSCAR PENSÃO NA TABELA DE FERIAS_ITENS
                $sqlPensaoFerias = $this->getPensaoEmFerias($row_ferias['id_clt'], $row_ferias['id_ferias'], $legendaPensaoFerias, true);

                $qr_p_ferias = mysql_query($sqlPensaoFerias) or die("ln pensao ferias 580: " . mysql_error());
                $valorPensaoFerias = 0;
                if (mysql_num_rows($qr_p_ferias) != 0) {
                    while ($row_pferias = mysql_fetch_assoc($qr_p_ferias)) {
                        $valorPensaoFerias += $row_pferias['valor'];
                    }
                }
                
                //---------- RIAP - ABONO PECUNIÁRIO ----------
                $this->abono_pecuniario += $row_ferias['abono_pecuniario'];
                $this->pensao_alimenticia += $valorPensaoFerias;
                
            }
            
            $this->outros_rendimentos = $this->abono_pecuniario;
            
        }
        //echo "<br/>FIM FE: ".$this->inss."<br/>";
    }
    
    /**
     * METODO DE 13° TERCEIRO SALÁRIO
     * @param type $ids_clts
     * @param type $ano_calendario
     * @param type $whereDecimo
     * @param type $dirf
     * @return type
     */
    public function getDadosDecimoTerceiro($ids_clts, $dirf = false){
        
        if ($this->decimoAntigo) {
            $whereDecimo = "1,2,3";
        } else {
            $whereDecimo = "2,3";
        }
        
        $qr_decimo = "SELECT A.id_folha,B.id_clt,A.mes,A.terceiro,
            B.salbase,
            B.valor_dt,
            (B.salliquido + B.inss_dt + B.ir_dt + B.a5049) AS valor_rt_dt,
            (B.salliquido + B.desco + B.inss_dt + B.ir_dt + B.a5049) AS val_ultimo,
            (B.salbase - B.ir_dt) as brutoMenosIr,
            B.base_inss,
            B.salliquido,
            B.rend,
            B.inss_dt,
            B.a5049,
            B.ir_dt,
            B.ano,
            A.ids_movimentos_estatisticas
            FROM rh_folha as A
            INNER JOIN rh_folha_proc as B
            ON B.id_folha = A.id_folha
            WHERE B.id_clt IN ({$ids_clts}) 
            AND A.status = 3 AND A.ano = '{$this->anoBase}'  AND A.terceiro = 1 
            AND B.status = 3 AND A.tipo_terceiro IN ({$whereDecimo})";
            
        
        /**
         * FEITO POR: SINÉSIO LUIZ 
         * FAZ O RETORNO DA QUERY PARA O METODO
         */
        if($dirf){
            return $qr_decimo;
        }
        
        $qr_valores_dt = mysql_query($qr_decimo) or die("getDadosDecimoTerceiro: " . mysql_error()."<!-- SQL: ".$qr_decimo." -->");
        while ($row_valor_dt = mysql_fetch_assoc($qr_valores_dt)) {
            $idDecimoTerceiro = array();
            $idDecimoTerceiro[$row_valor_dt['id_clt']] = $row_valor_dt['id_clt'];
            
            $valor_decimo   = $row_valor_dt['base_inss']; //1 - $row_mov['total'] // 2 - $row_valor_dt['valor_rt_dt'] // TIREI 'salbase' 12.02.2015
            $valor_RTRT     += $valor_decimo;
            $valor_RTPO     += $row_valor_dt['inss_dt'];
            $valor_RTDP     += ($row_valor_dt['ir_dt'] > 0) ? $row_valor_dt['a5049'] : '';
            $valor_RTIRF    += $row_valor_dt['ir_dt'];
            
            $this->idsEstatisticas[$row_valor_dt['id_folha']] = $row_valor_dt['ids_movimentos_estatisticas'];
            $this->idsEstatisticasMes[$row_valor_dt['id_folha']]['mes'] = 13;
            $this->idsEstatisticasMes[$row_valor_dt['id_folha']]['flag'] = $row_valor_dt['terceiro'];
            $this->folhasEnvolvidas[] = $row_valor_dt['id_folha'];
            
        }
        
        $this->inssdt = $valor_RTPO;
        $this->rtdp = $valor_RTDP;
        $this->irdt = $valor_RTIRF;
        
        $this->salario13 += $valor_RTRT;
        //SUBTRAINDO VAOR DT COM INSS_DT e IR_DT (IGUAL O INFORME GERADO PELA PROGRAMA DA DIRF)
        //NO INFORME DE RENDIMENTOS DA DIRV, TAMBÉM É DESCONTADO O RTDP (DESCONTO POR DEPENDENTES)
        
        /*$this->salario13Informe = $this->salario13 - $valor_RTPO - $valor_RTDP - $valor_RTIRF;
        if($_COOKIE['logado'] == 158){
            echo "Dados da folha de 13<br>";
            echo "<pre>";
                print_r($this->salario13);
                echo "<br>";
                print_r($valor_RTPO); 
                echo "<br>";
                print_r($valor_RTDP);
                echo "<br>";
                print_r($valor_RTIRF);
                echo "<br>";
                print_r($this->salario13Informe);
            echo "</pre>";
        }*/
        
    }

    /**
     * 
     * @param type $id
     */
    public function getDadosExtra($id) {
        
        if (!empty($this->ids_movimentos_folhas)) {
            //---------- RIDAC Rendimentos Isentos - Diária e Ajuda de Custo (parcela única não incide) ----------
            $sql_ajuda = "SELECT valor_movimento,mes_mov
                            FROM rh_movimentos_clt 
                            WHERE cod_movimento = '50111' AND id_clt IN ({$id})
                            AND id_movimento IN ({$this->ids_movimentos_folhas})";
            $qr_ajuda = mysql_query($sql_ajuda) or die("Erro getDadosExtra: " . mysql_error()." <!-- QRY: ".$sql_ajuda." -->");

            if (mysql_num_rows($qr_ajuda) > 0) {
                while ($row_ajuda = mysql_fetch_assoc($qr_ajuda)) {
                    
                    $idMovP = $row_ajuda['id_movimento'];
                    foreach($this->folhasEnvolvidas as $foLHAS){
                        if(strstr($this->idsEstatisticas[$foLHAS], $idMovP)){
                            $m = $this->idsEstatisticasMes[$foLHAS]['mes'];
                            $mesMov = ($folhaDezembro) ? intval($m) - 1 : intval($m);
                            
                            if ($this->idsEstatisticasMes[$foLHAS]['flag'] == 2) {
                                $this->ajuda_custo += $row_ajuda['valor_movimento'];
                            }
                        }
                    }
                    
                    //$this->ajuda_custo += $row_ajuda['valor_movimento'];
                }
            }
        }
    }
    
    /**
     * METOD DE RETORNO DE PLANO DE SAUDE
     * @param type $id
     */
    public function getDadosPlanoSaude($id) {
        $sql_plano = "SELECT SUM(a8002) as plano FROM rh_folha_proc
                        WHERE id_clt IN ({$id}) AND id_folha IN (".implode(",",$this->folhasEnvolvidas).") AND status = 3";
                        //exit($sql_plano);
        $qr_plano = mysql_query($sql_plano) or die("getDadosPlanoSaude: " . mysql_error()."<!-- SQL: ".$sql_plano." -->");
        if(mysql_num_rows($qr_plano) > 0){
            $row_plano = mysql_fetch_assoc($qr_plano);
            $this->valoPlanoSaude = $row_plano['plano'];
        }
    }
    
    /**
     * RIDAC RENDIMENTOS ISENTOS - DIÁRIA E AJUDA DE CUSTO (PARCELA UNICA NÃO INCIDE)
     * @param type $ids_clts
     * @param type $idsMovimentosRowClt
     * @param type $dirf
     */
    public function getRendimentosIsentos($ids_clts,$idsMovimentosRowClt, $dirf = false){
        $sqlRendimentosIsentos = "SELECT id_movimento,valor_movimento
            FROM rh_movimentos_clt 
            WHERE cod_movimento = '50111' AND id_clt IN ({$ids_clts}) 
            AND id_movimento IN ($idsMovimentosRowClt)";
        
       if($dirf){
           return $sqlRendimentosIsentos;
       }     
    }
    
    
        
    /**
     * METODO DE RETORNO DE PENSAO ALIMENTICIA
     * @param type $ids_clts
     * @param type $arrayPensaoAlimenticia
     * @param type $idsMovimentosRowClt
     * @param type $dirf
     * @return string
     */
    public function getDadosPensaoAlimenticia($ids_clts,$arrayPensaoAlimenticia=null,$idsMovimentosRowClt=null, $dirf = false){
        
        //TRAZENDO SOMENTE AS ESTATISTICAS DAS FOLHAS QUE O CLT ESTÁ ENVOLVIDO
        if($idsMovimentosRowClt === null){
            $estatisticasenvolvidos = array();
            foreach($this->folhasEnvolvidas as $v){
                $estatisticasenvolvidos[$v] = $this->idsEstatisticas[$v];
            }
            $idsMovimentosRowClts = implode(",",$estatisticasenvolvidos);
            
            $axplodeIdsMovimentosRowClt = explode(",", $idsMovimentosRowClts);

            for($i=0;$i < count($axplodeIdsMovimentosRowClt); $i++){
                if(strlen($axplodeIdsMovimentosRowClt[$i]) > 0){
                    $ArrayidsMovimentosRowClt[] = $axplodeIdsMovimentosRowClt[$i];  
                }
            }
            
            $idsMovimentosRowClt = implode(",", $ArrayidsMovimentosRowClt);
        }        
        
        if($arrayPensaoAlimenticia === null){
            $arrayPensaoAlimenticia = $this->arrayPensaoAlimenticia;
        }
        
        $sql_pensao = "SELECT id_movimento,mes_mov,valor_movimento FROM rh_movimentos_clt WHERE id_clt IN ({$ids_clts}) 
                        AND cod_movimento IN (".implode(",", $arrayPensaoAlimenticia).") AND id_movimento IN ($idsMovimentosRowClt)";
        
        if($dirf){
            return $sql_pensao;
        }
        
        if($idsMovimentosRowClt == ""){
            return;
        }
        
        $qr_pensao = mysql_query($sql_pensao) or die("getDadosPensaoAlimenticia: " . mysql_error()."<!-- SQL: ".$sql_pensao." -->");
        
        if (mysql_num_rows($qr_pensao) > 0) {
            while ($row_pensao = mysql_fetch_assoc($qr_pensao)) {
                //ATENÇÃO, O MES DO MOVIMENTO NÃO VEM DA TABELA DE MOVIMENTOS, POIS PODE HAVER UM MOVIMENTO SEMPRE, 
                //ENTÃO, PARA SABER REALMENTE O MES DO MOVIMENTO, TEM Q VERIFICAR EM QUAL MES AGENTE ACHA O ID_MOVIMENTO_ESTATISTICA DELE
                $idMovP = $row_pensao['id_movimento'];
                
                foreach($this->folhasEnvolvidas as $foLHAS){
                    if(strstr($this->idsEstatisticas[$foLHAS], $idMovP)){
                        
                        $m = $this->idsEstatisticasMes[$foLHAS]['mes'];
                        /**
                         * CAMPO TERCEIRO DA FOLHA
                         */
                        if ($this->idsEstatisticasMes[$foLHAS]['flag'] == 2) {
                            if($m == 12){
                                $mesMov = 0;
                            }else{
                                $mesMov = ($this->folhaDezembro) ? intval($m) - 1 : intval($m);
                            }
                            $this->pensao_alimenticia += $row_pensao['valor_movimento'];
                        } else {
                            $this->pensao_alimenticiaDT += $row_pensao['valor_movimento'];
                            $this->salario13Informe -= $this->pensao_alimenticiaDT;              //Abatendo o valor da pensão do liquido final do informe
                        }
                    }
                }
            }
            unset($row_pensao);
        }
        //if($ids_clts == '5072'){echo "<pre>";print_r($this->folhasEnvolvidas);echo "<br><br>";var_dump($this->pensao_alimenticia); exit();}
        
    }

    /**
     * VALIDA OS VALORES IMPORTANTES PARA O INFORME, 
     * CASO O VALOR SEJA NEGATIVO ELE É MODIFICADO PARA ZERO
     */
    public function normalizaValores() {

        if ($this->salario < 0) {
            $this->salario = 0;
        }
        if ($this->ir < 0) {
            $this->ir = 0;
        }
        if ($this->inss < 0) {
            $this->inss = 0;
        }
        if ($this->salario13 < 0) {
            $this->salario13 = 0;
        }
        if ($this->inssdt < 0) {
            $this->inssdt = 0;
        }
        if ($this->irdt < 0) {
            $this->irdt = 0;
        }
        if ($this->rtdp < 0) {
            $this->rtdp = 0;
        }
        if ($this->outros_rendimentos < 0) {
            $this->outros_rendimentos = 0;
        }
        if ($this->pensao_alimenticia < 0) {
            $this->pensao_alimenticia = 0;
        }
        if ($this->ajuda_custo < 0) {
            $this->ajuda_custo = 0;
        }
        if ($this->rescisao_ferias < 0) {
            $this->rescisao_ferias = 0;
        }
        if ($this->salario13Informe < 0){
            $this->salario13Informe = 0;
        }
        if ($this->rescisao_indenizacao < 0){
            $this->rescisao_indenizacao = 0;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function validaValores() {
        $return = true;

        if ($this->salario == 0 && $this->ir == 0 && $this->inss == 0 && $this->salario13 == 0 && $this->outros_rendimentos == 0 && $this->pensao_alimenticia == 0 && $this->ajuda_custo == 0 && $this->rescisao_ferias == 0 && $this->rescisao_indenizacao == 0) {
            $return = false;
        }
        
        if($this->salario == 0){
            $return = false;
        }
        return $return;
    }
    
    /**
     * INICIOALIZADOR DO FPDF
     */
    public function iniciaFpdf() {
        define('FPDF_FONTPATH', '../rh/fpdf/font/');
        $this->fpdf = new FPDF("P", "cm", "A4");
        $this->fpdf->SetAutoPageBreak(true, 0.0);
        $this->fpdf->Open();
    }
    
    /**
     * GERADOR DE PFP
     */
    public function geraPdf() {
        
        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->Cell(10, 30, " ");
        $this->fpdf->Image('imagens/fundo_rendimento_2015_p1.gif', 0.5, 0.3, 20, 29, 'gif');

        $this->fpdf->SetXY(6.85, 2.1);
        $this->fpdf->Cell(0, 0, $this->anoCalendario, 0, 0, 'L'); //ANO CALENDÁRIO E ANO BASE, SÃO A MESMA COISA, O ANO EXERCICIO QUE SIGNIFICA O ANO DE ENTREGA DO INFORME
        
        $this->fpdf->SetXY(15.05, 2.2);
        $this->fpdf->Cell(0, 0, $this->anoBase, 0, 0, 'L'); //ANO CALENDÁRIO E ANO BASE, SÃO A MESMA COISA, O ANO EXERCICIO QUE SIGNIFICA O ANO DE ENTREGA DO INFORME

        ($this->tipo!=3) ? $this->fpdf->SetFont('Arial', 'B', 10) : $this->fpdf->SetFont('Arial', 'B', 6);
        $this->fpdf->SetXY(1.6, 3.70);
        $this->fpdf->Cell(0, 0, ($this->tipo!=3) ? $this->empresa['razao'] : $this->empresacoop['nome'], 0, 0, 'L');
        
        if($this->tipo==3){ $this->fpdf->SetFont('Arial', 'B', 10); }
        $this->fpdf->SetXY(14.8, 3.70);
        $this->fpdf->Cell(0, 0, ($this->tipo!=3) ? $this->empresa['cnpj'] : $this->empresacoop['cnpj'], 0, 0, 'L');

        $this->fpdf->SetXY(6.05, 5.20);
        $this->fpdf->Cell(0, 0, $this->participante['nome'], 0, 0, 'L');

        $this->fpdf->SetXY(1.6, 5.20);
        $cpf = preg_replace('/[^[:digit:]]/', '', $this->participante['cpf']);
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})/i', '$1.$2.$3-$4', $cpf);
        $this->fpdf->Cell(0, 0, $cpf, 0, 0, 'L');

        $this->fpdf->SetXY(1.6, 6.10);
        switch ($this->participante['tipo_contratacao']) {
            case 2:
                $this->fpdf->Cell(0, 0, "Rendimentos do trabalho assalariado", 0, 0, 'L');
                break;
            case 1:
            case 3:
                $this->fpdf->Cell(0, 0, "Rendimentos do trabalho sem vínculo empregatício", 0, 0, 'L');
                break;
        }

        $this->fpdf->SetXY(14.8, 7.50); //9.06 : 1,56
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->salario, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 8.44);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->inss, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 9.36);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 10.29);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->pensao_alimenticia, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 11.24);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->ir, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 12.85); //14.75 : 1,9
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 13.77);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->ajuda_custo, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 14.72);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 15.62);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 16.55);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 17.5);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->rescisao_indenizacao, 2, ",", ".") . "", 0, 0, 'L');
        
        //ABONO PECUNIARIO EM OUTROS RENDIMENTOS
        if($this->outros_rendimentos > 0){
            if($this->abono_pecuniario > 0){
                $this->fpdf->SetFont('Arial', 'B', 8);
                $this->fpdf->SetXY(5, 18.2);
                $this->fpdf->Cell(0, 0, "ABONO PECUNIARIO", 0, 0, 'L');
                $this->fpdf->SetFont('Arial', 'B', 10);
            }
        }
        
        $this->fpdf->SetXY(14.8, 18.5);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->outros_rendimentos, 2, ",", ".") . "", 0, 0, 'L');
        
        //SETANDO NA IMPRESSÃO O VALOR CORRETO DO 13 NO INFORME
        $this->salario13Informe = $this->salario13 - $this->irdt - $this->inssdt;
        
        $this->fpdf->SetXY(14.8, 20); //22.1 : 2.1
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->salario13Informe, 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 20.9);
        $this->fpdf->Cell(0, 0, "R$ " . number_format($this->irdt, 2, ",", ".") . "", 0, 0, 'L');

        $this->fpdf->SetXY(14.8, 21.8);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        
        $this->fpdf->SetXY(13.2, 23.6);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 25.6);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 26.18);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 26.75);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 27.33);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 27.89);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        $this->fpdf->SetXY(14.8, 28.49);
        $this->fpdf->Cell(0, 0, "R$ " . number_format('0,00', 2, ",", ".") . "", 0, 0, 'L');
        
        // NovaPagina
        $this->fpdf->SetAutoPageBreak(true, 0.0);
        
        $this->fpdf->SetFont('Arial', 'B', 9);
        $this->fpdf->Cell(10, 30, " ");
        $this->fpdf->Image('imagens/fundo_rendimento_2015_p2.gif', 0.5, 0.3, 20, 29, 'gif');
        
        $this->fpdf->SetXY(6.85, 2.1);
        $this->fpdf->Cell(0, 0, $this->anoCalendario, 0, 0, 'L'); //ANO CALENDÁRIO E ANO BASE, SÃO A MESMA COISA, O ANO EXERCICIO QUE SIGNIFICA O ANO DE ENTREGA DO INFORME
        
        $this->fpdf->SetXY(15.05, 2.2);
        $this->fpdf->Cell(0, 0, $this->anoBase, 0, 0, 'L'); //ANO CALENDÁRIO E ANO BASE, SÃO A MESMA COISA, O ANO EXERCICIO QUE SIGNIFICA O ANO DE ENTREGA DO INFORME
        
        //PLANO DE SAUDE :5
        if($this->valoPlanoSaude > 0){
            $this->fpdf->SetFont('Arial', 'B', 6);
            
            $this->fpdf->SetXY(1.55, 4.5);
            $this->fpdf->Cell(0, 0, "Pagamentos a plano de saúde:", 0, 0, 'L');
            
            $this->fpdf->SetXY(1.7, 4.8);
            $this->fpdf->Cell(0, 0, "Operadora: ", 0, 0, 'L');
            
            $this->fpdf->SetXY(1.7, 5.1);
            $this->fpdf->Cell(0, 0, "Valor pago no ano referente ao titular: R$ " . number_format($this->valoPlanoSaude, 2, ",", ".") . "", 0, 0, 'L');
            $this->fpdf->SetFont('Arial', 'B', 9);
        }
        
        $this->fpdf->SetXY(1.55, 6.7);
        $this->fpdf->Cell(0, 0, ($this->tipo!=3) ? $this->empresa['contador_nome'] : $this->empresacoop['contador_nome'], 0, 0, 'L');

        $this->fpdf->SetXY(9.1, 6.7);
        $this->fpdf->Cell(0, 0, date("d/m/Y"), 0, 0, 'L');
        
    }
    
    /*
     * 
     */
    public function finalizaPdf(){
        unset($this->rescisao_ferias);
        $this->fpdf->Output($this->fileName);
        $this->fpdf->Close();
    }
    
    /**
     * 
     */
    public function limpaVariaveis() {
        unset($this->outros_rendimentos);
        unset($this->rescisao_ferias);
        unset($this->salario13);
        unset($this->irdt);
        unset($this->inssdt);
        unset($this->rtdp);
        unset($this->salario);
        unset($this->inss);
        unset($this->ir);
        unset($this->pensao_alimenticia);
        unset($this->ajuda_custo);
        unset($this->rescisao);
        unset($this->abono_pecuniario);
        unset($this->pensao_alimenticiaDT);
        unset($this->salario13Informe);
        unset($this->folhasEnvolvidas);
        unset($this->feriasFolhaProcMes);
        unset($this->rescisao_indenizacao);
    }
    
    /**
     * 
     */
    public function downloadFile() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload");
        header("Content-Length: " . filesize($this->fileName));
        header("Content-Disposition: attachment; filename={$this->fileName}");
        flush();

        readfile($this->fileName);
    }

    /**
     * 
     * @param type $ids
     * @return type
     */
    public function normalizaIdsEstatisticas($ids) {
        $ids_mo = "";
        $ids_mo = str_replace(',,,', ',', $ids);
        $ids_mo = str_replace(',,', ',', $ids_mo);

        if (substr($ids_mo, -1) == ",") {
            $ids_mo = substr($ids_mo, 0, -1);
        }
        if (substr($ids_mo, 0, 1) == ",") {
            $ids_mo = substr($ids_mo, 1);
        }
        return $ids_mo;
    }
    
    /**
     * 
     * @param type $ini
     * @return string
     */
    public function montaOptionsAnos($ini=2012){
        $anos = array();
        
        for ($i = $ini; $i <= date("Y")+1; $i++) {
            $anos[$i] = $i." / ".($i+1);
        }
        
        return $anos;
    }
    
    /**
     * 
     */
    public function debugaValores(){
        echo "<pre>";
        echo "Folha Dezembro: ";
        var_dump($this->folhaDezembro)."\r\r";
                
        echo "Variavel Rescisao: ";
        var_dump($this->rescisao);
        
        echo "Valores mensais: \r\r";
        print_r($this->valorMes);
        
        echo " -----------  QUERYS ----------- \r\r";
        
        echo "Folha: \r\r";
        print_r($this->sqlFolha);
        
        echo "\r\rRescisão: \r\r";
        print_r($this->sqlRescisao);
        
        exit;
    }
    
    /**
     * RETORNA TODOS OS IDS DO MESMO CPF
     * @param type $cpf
     * @param type $projetos
     * @return type
     */
    public function  verificaDuplicidade($cpf,$tipo=2) {
        $result = array();
        if($tipo == 1){
            $result['rs'] = montaQuery("autonomo", "id_autonomo,id_projeto", "REPLACE(REPLACE(cpf,'.',''),'-','')  = '{$cpf}' AND id_projeto IN (".implode(",",$this->projetos).") AND tipo_contratacao = 1");
        }elseif($tipo == 2){
            $result['rs'] = montaQuery("rh_clt", "id_clt,id_projeto", "REPLACE(REPLACE(cpf,'.',''),'-','')  = '{$cpf}' AND id_projeto IN (".implode(",",$this->projetos).")");
        }else{
            $result['rs'] = montaQuery("autonomo", "id_autonomo,id_projeto", "REPLACE(REPLACE(cpf,'.',''),'-','')  = '{$cpf}' AND id_projeto IN (".implode(",",$this->projetos).") AND tipo_contratacao = 3");
        }
        
        $result['total'] = count($result['rs']);
        return $result;
    }
    
    /**
     * FOLHAS ENVOLVIDAS NA DIRF
     */
    public function getFolhasEnvolvidas(){
        
        $qr_folhas = "SELECT id_folha,mes,ano,ids_movimentos_estatisticas,terceiro,projeto
                            FROM rh_folha as A 
                            WHERE A.projeto IN (" . implode(",", $this->projetos) . ") AND A.status = 3 
                              $this->whereDezembro";
        //exit($qr_folhas);
        $rs_folhas = mysql_query($qr_folhas) or die("ln 186: " . mysql_error());
        while ($row_folha = mysql_fetch_assoc($rs_folhas)) {
            //$ids_movimentos_folhas .= $row_folha['ids_movimentos_estatisticas'] . ",";
            //$idsEstatisticas[$row_folha['id_folha']] = $row_folha['ids_movimentos_estatisticas'];
            //$idsEstatisticasMes[$row_folha['id_folha']]['mes'] = $row_folha['mes'];
            //$idsEstatisticasMes[$row_folha['id_folha']]['flag'] = $row_folha['terceiro'];
            $folhasEnvolvidas[] = $row_folha['id_folha'];
            $folhasEnvPorProjeto[$row_folha['id_projeto']][] = $row_folha['id_folha'];
            //$idsEstatisticasPro[$row_folha['projeto']][] = explode(',',$row_folha['ids_movimentos_estatisticas']);
        }
        
        //echo "<pre>";print_r($this->$folhasEnvolvidas);echo $qr_folhas;echo "<br><br>";var_dump($this->projetos); exit();
        $this->folhasEnvPorProjeto = $folhasEnvPorProjeto;
        $this->folhasEnvolvidasGeral = $folhasEnvolvidas;
    }
    
    public function getRendimentosMediasSubRogoRescisao($ids_clts,$codigos,$dirf = false){
        //$codigos = array("90030","90035","90056","90057","90055","90034","90086","70011");
        $sql_mov = "SELECT id_movimento,id_clt,mes_mov,ano_mov,id_mov,cod_movimento,nome_movimento,valor_movimento,percent_movimento,incidencia,qnt 
                        FROM rh_movimentos_clt 
                        WHERE id_clt IN ($ids_clts) AND 
                            cod_movimento IN ('".implode("','",$codigos)."') AND 
                                mes_mov = 16 AND status = 1";
        
        /*$sql_mov = "SELECT 
                    A.id_movimento,A.id_clt,A.mes_mov,A.ano_mov,A.id_mov,B.cod_movimento,A.nome_movimento,
                    A.valor AS valor_movimento,A.incidencia,A.qnt 
                    FROM rh_movimentos_rescisao AS A
                    LEFT JOIN rh_movimentos_clt AS B ON (A.id_movimento = B.id_movimento)
                    WHERE A.id_clt IN ($ids_clts) AND B.cod_movimento IN ('".implode("','",$codigos)."');";*/
        if($dirf){
            return $sql_mov;
        }
        
        $qr_mov = mysql_query($sql_mov) or die("ln 186: " . mysql_error());
        
    }
    
    public function getDadosPensaoAlimenticiaNovaTabela($ids_clt,$ids_folha,$arrCodPensao,$idsMovimentos){
        $sql_pensao = "SELECT A.id_clt, A.nome, C.cod_mov, C.nome_mov AS nome, C.base, C.valor_mov AS valor_movimento, C.percent AS aliquota , 
                                CAST(D.mes AS signed) as mesEdit,D.terceiro,
                                B.nome_dependente, DATE_FORMAT(B.data_nasc_dependente, '%Y%m%d') AS data_nasc_dependente,
                                IF(B.cpf_dependente IS NULL,'000',REPLACE(REPLACE(B.cpf_dependente,'-',''),'.','')) AS cpf_dependente, 
                                B.id_tipo_dependente,E.cod_receita
                    FROM rh_clt AS A
                    LEFT JOIN favorecido_pensao_assoc AS B ON(A.id_clt = B.id_clt)
                    LEFT JOIN itens_pensao_para_contracheque AS C ON(C.cpf_favorecido =  REPLACE(REPLACE(B.cpf,'.',''),'-','') AND C.`status` = 1)
                    LEFT JOIN rh_folha AS D ON(C.id_folha = D.id_folha)
                    LEFT JOIN tipo_dependente AS E ON (E.id_tipo_dependente = B.id_tipo_dependente)
                    WHERE A.id_clt IN ({$ids_clt}) AND C.id_folha IN (".implode(",",$ids_folha).")
                    
                    UNION                 
                 
                    SELECT A.id_clt,C.nome,A.cod_movimento AS cod_mov,A.nome_movimento AS nome,'' AS base,A.valor_movimento,'' as aliquota,
                            IF(A.mes_mov <= 12,CAST(A.mes_mov AS signed),12) as mesEdit, IF(A.mes_mov > 12,1,2) as terceiro,
                            B.nome_dependente, DATE_FORMAT(B.data_nasc_dependente, '%Y%m%d') AS data_nasc_dependente,
                            IF(B.cpf_dependente IS NULL,'000',REPLACE(REPLACE(B.cpf_dependente,'-',''),'.','')) AS cpf_dependente, 
                            B.id_tipo_dependente,E.cod_receita
                    FROM rh_movimentos_clt AS A
                    LEFT JOIN favorecido_pensao_assoc AS B ON (A.id_clt = B.id_clt)
                    LEFT JOIN rh_clt AS C ON (A.id_clt = C.id_clt)
                    LEFT JOIN tipo_dependente AS E ON (E.id_tipo_dependente = B.id_tipo_dependente)
                    WHERE A.id_clt IN ({$ids_clt}) AND A.cod_movimento IN (".implode(",",$arrCodPensao).") AND id_movimento IN ($idsMovimentos)
                    ORDER BY REPLACE(REPLACE(cpf_dependente,'-',''),'.','') ";
        
        $qr_pensao = mysql_query($sql_pensao) or die("getDadosPensaoAlimenticiaNovaTabela: $sql_pensao <br>" . mysql_error());
        $dadosPensao = array();
        $IDENTIFICADOR_INFPA = array();
        $DEPEN = array();
        $infoDepe = "";
        if (mysql_num_rows($qr_pensao) > 0) {
            $valor_pensao = null;
            $valor_pensaoD = null;
            while ($row_pensao = mysql_fetch_assoc($qr_pensao)) {
                //ATENÇÃO, O MES DO MOVIMENTO NÃO VEM DA TABELA DE MOVIMENTOS, POIS PODE HAVER UM MOVIMENTO SEMPRE, 
                //ENTÃO, PARA SABER REALMENTE O MES DO MOVIMENTO, TEM Q VERIFICAR EM QUAL MES AGENTE ACHA O ID_MOVIMENTO_ESTATISTICA DELE
                
                if($row_pensao['mesEdit'] == 12 && $row_pensao['terceiro'] == 1){
                    $valor_pensaoD[$row_pensao['cpf_dependente']] += $row_pensao['valor_movimento'];
                }else{
                    $valor_pensao[$row_pensao['cpf_dependente']][$row_pensao['mesEdit']] += $row_pensao['valor_movimento'];
                }
                
                if($infoDepe != $row_pensao['cpf_dependente']){
                    $IDENTIFICADOR_INFPA[$row_pensao['cpf_dependente']]['ID_REGISTRO'] = 'INFPA';
                    $IDENTIFICADOR_INFPA[$row_pensao['cpf_dependente']]['CPF'] = $row_pensao['cpf_dependente'];
                    $IDENTIFICADOR_INFPA[$row_pensao['cpf_dependente']]['DATA_NASCIMENTO'] = $row_pensao['data_nasc_dependente'];
                    $IDENTIFICADOR_INFPA[$row_pensao['cpf_dependente']]['NOME'] = trim(normalizaNome($row_pensao['nome_dependente']));
                    $IDENTIFICADOR_INFPA[$row_pensao['cpf_dependente']]['RELACAO_DEPE'] = $row_pensao['cod_receita'];
                    $DEPEN[] = $row_pensao['cpf_dependente'];
                    $infoDepe = $row_pensao['cpf_dependente'];
                }
            }
            
            if(isset($_REQUEST['debug'])){
                echo "<br>**pensao**<div style='overflow:auto; max-width:500px;'><pre>";
                echo "{$sql_pensao}<br>arr pens<br>";
                print_r($valor_pensao);
                echo "</pre></div><br>pensao 13: {$valor_pensaoD}<br>";
            }
            
            $dadosPensao['dt'] = $valor_pensaoD;
            $dadosPensao['mensal'] = $valor_pensao;
            $dadosPensao['info'] = $IDENTIFICADOR_INFPA;
            $dadosPensao['dependentes'] = $DEPEN;
        }else{
            $dadosPensao['dependentes'] = array();
        }
        
        return $dadosPensao;
    }
    
    public function getPensaoEmFerias($id_clt,$id_ferias,$id_legenda,$dirf = false){
        $sqlPensaoFerias = "SELECT * FROM rh_ferias_itens WHERE 
                                id_legenda = {$id_legenda} AND 
                                id_ferias = {$id_ferias} AND id_clt = {$id_clt}";
        if($dirf){
            return $sqlPensaoFerias;
        }
        
        $qr_pensao = mysql_query($sqlPensaoFerias);
        while($row = mysql_fetch_assoc($qr_pensao)){
            $pensao[] = $row;
        }
    }
    
    public function getInformacoesFavorecidosPensao($id_clt,$dirf = false){
        $sqlInfoFavo = "SELECT 
                            A.id,A.id_clt,REPLACE(REPLACE(A.cpf,'.',''),'-','') as cpf,A.favorecido,A.nome_dep,
                            B.tipo_dependente,B.cod_receita
                        FROM favorecido_pensao_assoc AS A
                        LEFT JOIN tipo_dependente AS B ON (A.id_tipo_dependente = B.id_tipo_dependente)
                        WHERE id_clt IN ({$id_clt});";
        if($dirf){
            return $sqlInfoFavo;
        }
        
        $qr_infoFavo = mysql_query($sqlInfoFavo);
        while($row = mysql_fetch_assoc($qr_infoFavo)){
            $pensao[] = $row;
        }
    }
    
    public function getMovimentosRescisaoComplementarRT($id_rescisao){
        $sqlMov = "SELECT * FROM rh_movimentos_rescisao WHERE id_rescisao = '{$id_rescisao}' AND complementar = 1 AND id_mov IN (SELECT id_mov FROM rh_movimentos WHERE incidencia_irrf);";
        $qrMov = mysql_query($sqlMov);
        $dados = array();
        $total = 0;
        $c=0;
        while($row = mysql_fetch_assoc($qrMov)){
            $dados['detalhado'][$c]['id_mov'] = $row['id_mov'];
            $dados['detalhado'][$c]['nome_movimento'] = $row['nome_movimento'];
            $dados['detalhado'][$c]['valor'] = $row['valor'];
            $total += $row['valor'];
            $c++;
        }
        $dados['total'] = $total;
        
        return $dados;
    }
    
    public function getMovimentosRescisaoComplementar($id_rescisao){
        $sqlMov = "SELECT * FROM rh_movimentos_rescisao WHERE id_rescisao = '{$id_rescisao}' AND complementar = 1 AND id_mov NOT IN (SELECT id_mov FROM rh_movimentos WHERE incidencia_irrf);";
        $qrMov = mysql_query($sqlMov);
        $dados = array();
        $total = 0;
        $c=0;
        while($row = mysql_fetch_assoc($qrMov)){
            $dados['detalhado'][$c]['id_mov'] = $row['id_mov'];
            $dados['detalhado'][$c]['nome_movimento'] = $row['nome_movimento'];
            $dados['detalhado'][$c]['valor'] = $row['valor'];
            $total += $row['valor'];
            $c++;
        }
        $dados['total'] = $total;
        
        return $dados;
    }
    

}
