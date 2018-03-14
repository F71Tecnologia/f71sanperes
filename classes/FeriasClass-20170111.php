<?php
/**
 * Classe de processamento de férias
 * 
 * @file      FeriasClass.php
 * @license
 * @link      
 * @copyright 2016 F71
 * @author    Não definido
 * @package   FeriasClass
 * @access    public    
 * 
 * @version: 3.0.04667 - 07/12/2015 - Jacques - Foi suprimida a condição "...AND (status < 60 OR status = 200)" na query do método getFuncionarioFerias por não estár 
 *                                              aparecendo na listagem [Lista de Funcionários] alegado por Gimenes. 
 * @version: 3.0.04965 - 15/12/2015 - Jacques - Revisão .4667 disfeita por solicitação de Junior à Ramon "...AND (status < 60 OR status = 200)" na query do método getFuncionarioFerias 
 *                                              O que houve nessa situação foi um equivo na interpretação quando Gimenes alegou que os funcionários com evento deveriam aparecer na listagem
 *                                              quando na verdade eles estavam saindo de evento mas não estavam tendo seu status alterado para atividade normal.
 * @version: 3.0.06315 - 11/02/2016 - Jacques - Incluído no método getFuncionarioFerias o campo last_id_ferias que guardo o ID da última férias válida do Clt para impressão em lote.
 * @version: 3.0.07828 - 14/03/2016 - Ramon   - Adicionando valor default para quarto parametro do metodo listaFuncionariosFerias
 * @version: 3.0.08416 - 21/03/2016 - Jacques - Adicionado a verificação de exclusão de registro na filtragem de clts de férias
 * @version: 3.0.10826 - 08/09/2016 - Jacques - Adicionado no método relatorioFerias o uso do framework para consulta de periodos aquisitivos pendentes e gozados
 * @version: 3.0.10837 - 09/09/2016 - Jacques - Alterado para 10 meses a pedido de Gimenez a data limite para agendamento de férias
 * @version: 3.0.00007 - 21/11/2016 - Jacques - Retirado no método relatorioFerias as quebras de linha na inclusão da concatenção dos períodos
 * @version: 3.0.00209 - 05/12/2016 - Rafael  - Adicionando campos conta e tipo_conta na query em getFuncionarioFerias
 * @version: 3.0.00209 - 05/01/2017 - Jacques - Adicionado a listagem de todos os clts na saída do método relatorioFerias() mesmo que não tenh período aquisitivo e fix de finalização de </span> em limiteAgendamento
 * 
 */  


include_once $_SERVER['DOCUMENT_ROOT'] . "/intranet/classes/CalculoFeriasClass.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/intranet/classes/calculos.php";
if(!include_once(ROOT_CLASS.'RhClass.php')) die ('Não foi possível incluir '.ROOT_CLASS.'RhClass.php'); 

class Ferias {

    private $dados = array();
    public $calcFerias;
    public $calculos;

    public function __construct() {
        $this->calcFerias = new Calculo_Ferias();
        $this->calculos = new calculos();
    }

    /**
     * 
     * @param type $mes
     * @param type $ano
     * @param type $projeto
     * @param type $bases
     * @return type
     */
    public function getDadosFerias($mes, $ano, $projeto, $bases = false) {
        $data_ref = $ano . "-" . $mes;
        $query = "SELECT * FROM (SELECT *, LEFT(data_ini, 7) AS mes_ref FROM rh_ferias) AS tmp  WHERE mes_ref = '{$data_ref}' AND projeto = '{$projeto}' AND status = '1'";
        $dados_ferias = mysql_query($query) or die("erro ao selecionar ferias");
        return $dados_ferias;
    }

    /**
     * 
     * @param type $mes
     * @param type $ano
     * @param type $projeto
     * @param type $debug
     */
    public function montaArrayFerias($mes, $ano, $projeto, $debug = false) {
        $dados_ferias = $this->getDadosFerias($mes, $ano, $projeto);
        while ($d = mysql_fetch_assoc($dados_ferias)) {
            //MODO DEBUG
            if ($debug) {
                $this->getDebug($d);
            }
            if (!empty($d['nome'])) {
                $this->dados[$d['id_clt']]['nome'] = $d['nome'];
            }
            if (!empty($d['salario']) && $d['salario'] != 0.00) {
                $this->dados[$d['id_clt']]['salario']['nome'] = "SALARIO";
                $this->dados[$d['id_clt']]['salario']['valor'] = $d['salario'];
            }
            if (!empty($d['salario_variavel']) && $d['salario_variavel'] != 0.00) {
                $this->dados[$d['id_clt']]['salario_variavel']['nome'] = "SALARIO VARIAVEL";
                $this->dados[$d['id_clt']]['salario_variavel']['valor'] = $d['salario_variavel'];
            }
            if (!empty($d['umterco']) && $d['umterco'] != 0.00) {
                $this->dados[$d['id_clt']]['terco_ferias']['nome'] = "1/3 DE FÉRIAS";
                $this->dados[$d['id_clt']]['terco_ferias']['valor'] = $d['umterco'];
            }
            if (!empty($d['pensao_alimenticia']) && $d['pensao_alimenticia'] != 0.00) {
                $this->dados[$d['id_clt']]['pensao_alimenticia']['nome'] = "PENSÃO ALIMENTICIAS";
                $this->dados[$d['id_clt']]['pensao_alimenticia']['valor'] = $d['pensao_alimenticia'];
            }
            if (!empty($d['inss']) && $d['inss'] != 0.00) {
                $this->dados[$d['id_clt']]['inss']['nome'] = "INSS";
                $this->dados[$d['id_clt']]['inss']['valor'] = $d['inss'];
            }
            if (!empty($d['ir']) && $d['ir'] != 0.00) {
                $this->dados[$d['id_clt']]['ir']['nome'] = "IR";
                $this->dados[$d['id_clt']]['ir']['valor'] = $d['ir'];
            }
            if (!empty($d['fgts']) && $d['fgts'] != 0.00) {
                $this->dados[$d['id_clt']]['fgts']['nome'] = "FGTS";
                $this->dados[$d['id_clt']]['fgts']['valor'] = $d['fgts'];
            }
            if (!empty($d['valor_total_ferias1']) && $d['valor_total_ferias1'] != 0.00) {
                $this->dados[$d['id_clt']]['valor_total_ferias_1']['nome'] = "VALOR TOTAL DE FERIAS 1° PARCELA";
                $this->dados[$d['id_clt']]['valor_total_ferias_1']['valor'] = $d['valor_total_ferias1'];
            }
            if (!empty($d['valor_total_ferias2']) && $d['valor_total_ferias2'] != 0.00) {
                $this->dados[$d['id_clt']]['valor_total_ferias_2']['nome'] = "VALOR TOTAL DE FERIAS 2° PARCELA";
                $this->dados[$d['id_clt']]['valor_total_ferias_2']['valor'] = $d['valor_total_ferias2'];
            }
            if (!empty($d['acrescimo_constitucional1']) && $d['acrescimo_constitucional1'] != 0.00) {
                $this->dados[$d['id_clt']]['acrescimo_constitucional_1']['nome'] = "ACRESCIMO CONSTITUCIONAL 1° PARCELA";
                $this->dados[$d['id_clt']]['acrescimo_constitucional_1']['valor'] = $d['acrescimo_constitucional1'];
            }
            if (!empty($d['acrescimo_constitucional2']) && $d['acrescimo_constitucional2'] != 0.00) {
                $this->dados[$d['id_clt']]['acrescimo_constitucional_2']['nome'] = "ACRESCIMO CONSTITUCIONAL 2° PARCELA";
                $this->dados[$d['id_clt']]['acrescimo_constitucional_2']['valor'] = $d['acrescimo_constitucional2'];
            }
        }

        //MODO DEBUG
        $this->getDebug($this->dados);
    }

    /**
     * 
     * @param type $clt
     * @param type $mes
     * @param type $ano
     * @return type
     */
    public function getPeriodoFerias($clt, $mes, $ano) {
        $mes_dois = sprintf("%02d", $mes);
        $data_referente = $ano . "-" . $mes_dois;
        $query = "SELECT DATE_FORMAT(data_ini,'%d/%m/%Y') AS data_inicio, DATE_FORMAT(data_fim,'%d/%m/%Y') AS data_final, DATEDIFF(data_fim,data_ini) AS diferenca_dias FROM (
                    SELECT *, LEFT(data_ini, 7) AS mes_ref FROM rh_ferias ) AS tmp  
                  WHERE mes_ref = '{$data_referente}' AND id_clt = '{$clt}'";
        $dados_periodo_ferias = mysql_query($query) or die("erro ao selecionar periodo de ferias do clt");
        return $dados_periodo_ferias;
    }

    public function getFeriasByMesAno($data_ini, $data_fim, $projeto = null, $campos = array(" * ")) {
        $mes_dois = sprintf("%02d", $mes);

        if (!empty($projeto)) {
            $projetos = " AND A.projeto = '{$projeto}'";
        } else {
            $projetos = "";
        }
        $query = "SELECT " . implode(",", $campos) . "
            FROM rh_ferias AS A
            LEFT JOIN (SELECT * FROM saida WHERE tipo = 156 AND status = 2 AND id_projeto = '{$projeto}') AS B ON(A.id_clt = B.id_clt AND DATE_FORMAT(B.data_vencimento, '%Y') = A.ano)
            LEFT JOIN saida_files_pg AS C ON(B.id_saida = C.id_saida)
            WHERE DATE_FORMAT(A.data_ini, '%Y-%m') BETWEEN '{$data_ini}'  AND '{$data_fim}' AND A.status = 1 {$projetos} ORDER BY A.data_ini";

        $dados_ferias = mysql_query($query) or die("erro ao selecionar ferias");
        return $dados_ferias;
    }

    /**
     * 
     * @param type $clt
     * @param type $mes
     * @param type $ano
     * @return type
     */
    public function getFeriasByClt($clt, $mes, $ano) {
        $mes_dois = sprintf("%02d", $mes);
        $query = "SELECT *
        FROM rh_ferias AS A
        WHERE id_clt = '{$clt}' AND 
        '{$ano}-{$mes_dois}' BETWEEN DATE_FORMAT(A.data_ini, '%Y-%m') AND DATE_FORMAT(A.data_fim, '%Y-%m')";
        $dados_ferias = mysql_query($query) or die("erro ao selecionar ferias do clt");
        return $dados_ferias;
    }

    /**
     * MÉTODO DE DEBUG
     * @param type $objet
     */
    public function getDebug($objet) {
        echo "<pre>";
        print_r($objet);
        echo "</pre>";
    }

    /**
     * USADO PARA PEGAR O VALORES NA FOLHA
     * @param type $id_clt
     * @param type $data_inicio
     * @param type $data_fim
     * @return boolean
     */
     public function getFeriasFolha($id_clt, $data_inicio, $data_fim) {

        $data = explode('-', $data_inicio);
        $dt_referencia = $data[0] . '-' . $data[1];
        
//        if($_COOKIE['logado'] == 179){
//            echo "SELECT  * FROM rh_ferias WHERE id_clt = $id_clt  AND '{$dt_referencia}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND status = 1 ORDER BY id_ferias DESC";
//        }
        
        $qr_ferias = mysql_query("SELECT  * FROM rh_ferias WHERE id_clt = $id_clt  AND '{$dt_referencia}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND status = 1 ORDER BY id_ferias DESC");
        $row_ferias = mysql_fetch_array($qr_ferias);
        $num_ferias = mysql_num_rows($qr_ferias);
        
        //PARA CALCULOS TRABALHISTAS DEVE SEMPRE CALCULAR 30 DIAS
        //DATA DA FOLHA DEVE ESTAR SEMPRE COM 30 DIAS, PARA NÃO DAR ERRO NO CALCULO
        $arDtFimFolha = explode("-", $data_fim);
        if($arDtFimFolha[2] > 30){
            $arDtFimFolha[2] = 30;
            $data_fim = $arDtFimFolha[0]."-".$arDtFimFolha[1]."-".$arDtFimFolha[2];
        }
        $arDtFimFerias = explode("-", $row_ferias['data_retorno']);
        if($arDtFimFerias[2] > 30){
            $arDtFimFerias[2] = 30;
            $row_ferias['data_retorno'] = $arDtFimFerias[0]."-".$arDtFimFerias[1]."-".$arDtFimFerias[2];
        }
        
        if($_COOKIE['logado'] == 158){
//            echo "<br/> -- CLT {$id_clt}";
//            echo "Dados Folha: INI {$data_inicio} - FIM {$data_fim}<br/>";
//            echo "Dados CLT: INI {$row_ferias['data_ini']} - FIM {$row_ferias['data_retorno']}<br/>";
        }
        
        if (!empty($num_ferias)) {
            // InÃ­cio das FÃ©rias entre o InÃ­cio e Fim da Folha
            if ($row_ferias['data_ini'] >= $data_inicio and $row_ferias['data_ini'] <= $data_fim) {
                
                $inicio = $row_ferias['data_ini'];
                // Se o Fim das FÃ©rias for antes do Fim da Folha
                $fim = ($row_ferias['data_retorno'] < $data_fim) ? $row_ferias['data_retorno'] : $data_fim; //aki + 1 day
                //ALTERANDO NOVAMENTE ISSO AQUI, 03-11-2014
                $inicioTraba = $data_inicio;
                $fimTraba = $row_ferias['data_ini'];

                $ferias = true;
                $fini = true;
                
                // Fim das FÃ©rias entre o InÃ­cio e Fim da Folha
            } elseif ($row_ferias['data_fim'] >= $data_inicio and $row_ferias['data_fim'] <= $data_fim) {
                
                // Se o InÃ­cio das FÃ©rias for depois do InÃ­cio da Folha
                $inicio = ($row_ferias['data_ini'] > $data_inicio) ? $row_ferias['data_ini'] : $data_inicio;
                $fim = $row_ferias['data_retorno'];
                
                $inicioTraba = $fim;
                $fimTraba = $data_fim;
                
                $ferias = true;
            }
            
            // Tem FÃ©rias
            if (isset($ferias)) {
                
                
                if($_COOKIE['logado'] == 158){
//                    echo "CONVERSAO {$inicio} - $fim<br/>";
                }
                
                $mesIni = date('m', strtotime($row_ferias['data_ini']));
                $mesFim = date('m', strtotime($row_ferias['data_fim']));
                
                
                if($row_ferias['data_ini'] == $data_inicio && $row_ferias['data_fim'] == $data_fim){
                    $dias_ferias = 30;
                }else{
                    // Calcula a diferenÃ§a de dias
                    $dias_ferias = abs((int) floor((strtotime($inicio) - strtotime($fim)) / 86400));
                    $dias_ferias = ($dias_ferias > 30) ? 30 : $dias_ferias;
                    //echo "Inicio: " . $inicio . " - " . $fim;
                    
                    if(isset($fini) && $mesIni != $mesFim){
                        $dias_ferias++;
                    }
                    
                }
                
                //ALTERANDO NOVAMENTE ISSO AQUI, 03-11-2014
                $dias_trab_ferias = abs((int) floor((strtotime($inicioTraba) - strtotime($fimTraba)) / 86400));
                
                list($nulo, $mes_inicio, $nulo) = explode('-', $row_ferias['data_ini']);
                list($nulo, $mes_fim, $nulo) = explode('-', $row_ferias['data_fim']);
                //$inss_porcentagem = $row_ferias['inss_porcentagem']/100;     
                $fgts_ferias = $base_fgts_ferias * 0.08;

                //Definindo INSS, IRRF e FGTS referente ao mÃªs das fÃ©rias
                if ((int) $mes_inicio == (int) $mes_fim) {
                    $base_inss_ferias   = $row_ferias['total_remuneracoes'];
                    $base_fgts_ferias   = $row_ferias['total_remuneracoes'];
                    $inss_ferias        = $row_ferias['inss'];
                    $irrf_ferias        = $row_ferias['ir'];
                    $fgts_ferias        = $row_ferias['fgts'];
                    $valor_ferias       = $row_ferias['total_remuneracoes'];
                    $desconto_ferias    = $row_ferias['total_liquido'];
                    $mes_ferias         = $row_ferias['mes'];   
                    $ano_ferias         = $row_ferias['ano'];
                } else {
                    
                    $base_inss_ferias   = $row_ferias['total_remuneracoes'];
                    $base_fgts_ferias   = $row_ferias['total_remuneracoes'];
                    $inss_ferias        = $row_ferias['inss'];
                    $irrf_ferias        = $row_ferias['ir'];
                    $fgts_ferias        = $row_ferias['fgts'];
                    $valor_ferias       = $row_ferias['total_remuneracoes'];
                    $desconto_ferias    = $row_ferias['total_liquido'];
                    $mes_ferias         = $row_ferias['mes'];   
                    $ano_ferias         = $row_ferias['ano'];
                    
//                    if ($data[1] == $mes_inicio) {
//                        $base_inss_ferias   = $row_ferias['base_inss'];
//                        $base_fgts_ferias   = $row_ferias['base_inss'];
//                        $inss_ferias        = ($row_ferias['inss'] );
//                        $irrf_ferias        = ($row_ferias['ir'] );
//                        $fgts_ferias        = ($row_ferias['fgts']);
//                        $valor_ferias       = $row_ferias['total_remuneracoes1'];
//                        $desconto_ferias    = $row_ferias['valor_total_ferias1'] + $row_ferias['acrescimo_constitucional1'] + $row_ferias['abono_pecuniario'] + $row_ferias['umterco_abono_pecuniario'] - $irrf_ferias - $inss_ferias;
//                    } else {
//                        $base_inss_ferias   = $row_ferias['base_inss'];
//                        $base_fgts_ferias   = $row_ferias['base_inss'];
//                        $inss_ferias        = 0;
//                        $irrf_ferias        = 0;
//                        $fgts_ferias        = 0;
//                        $valor_ferias       = $row_ferias['total_remuneracoes2'];
//                        $desconto_ferias    = $row_ferias['total_remuneracoes2'];
//                    }
                }
            }
        }
        
        if($_COOKIE['logado'] == 158){
//            echo " - DIAS dias_ferias {$dias_ferias}<br/>";
        }
        
        $resultado['id_clt'] = $id_clt;
        $resultado['mes'] = $data[1];
        $resultado['ano'] = $data[0];
        
        $resultado['mes_ferias'] = $mes_ferias;
        $resultado['ano_ferias'] = $ano_ferias;
        
        $resultado['base_inss'] = $base_inss_ferias;
        $resultado['base_fgts'] = $base_fgts_ferias;
        $resultado['inss'] = $inss_ferias;
        $resultado['irrf'] = $irrf_ferias;
        $resultado['fgts'] = $fgts_ferias;
        $resultado['valor_ferias'] = $valor_ferias;
        $resultado['desconto_ferias'] = $desconto_ferias;
        $resultado['ferias'] = $ferias;
        $resultado['dias_ferias'] = $dias_ferias;
        $resultado['dias_trabalhandos'] = $dias_trab_ferias;
        $resultado['aliquota'] = (int) $row_ferias['inss_porcentagem'];
        $resultado['pensao_ferias'] = $row_ferias['pensao_alimenticia'];

        return $resultado;
    }

    /**
     * 
     * @param type $folha
     * @return type
     */
    public function getTotalFeriasByFolha($folha) {

        $totaisFerias = array();

        if (is_array($folha)) {
            $itensFolha = implode(",", $folha);
        }

        //VERIFICA DATAS DE INICIO E TERMINO DA FOLHA
        $dados_folha = array();
        $query_folha = "SELECT A.id_folha, A.data_inicio, A.data_fim, A.projeto
                    FROM rh_folha AS A
                    WHERE A.id_folha IN({$itensFolha})";


        $sql_folha = mysql_query($query_folha) or die("Erro ao selecionar folha");
        if ($sql_folha) {
            while ($rows = mysql_fetch_assoc($sql_folha)) {
                $dados_folha[$rows["id_folha"]]["data_inicio"] = $rows['data_inicio'];
                $dados_folha[$rows["id_folha"]]["data_fim"] = $rows['data_fim'];
                $dados_folha[$rows["id_folha"]]["projeto"] = $rows['projeto'];
            }
        }

        foreach ($dados_folha as $folha => $dados) {

            //VERIFICA PARTICIPANTES DAS FERIAS NO MÊS DA FOLHA
            $mes = date("m", strtotime(str_replace("/", "-", $dados["data_inicio"])));
            $ano = date("Y", strtotime(str_replace("/", "-", $dados["data_inicio"])));

            $query_clt_ferias = "SELECT A.id_clt
                FROM rh_ferias AS A
                WHERE (MONTH(data_ini) = '{$mes}' || MONTH(data_fim) = '{$mes}') 
                AND YEAR(data_ini) = '{$ano}' AND status = 1 AND projeto = '{$dados['projeto']}'";

            $sql_ferias = mysql_query($query_clt_ferias) or die("Erro ao selecionar participantes das férias no mês");
            while ($rows = mysql_fetch_assoc($sql_ferias)) {
                $ferias_dados = $this->getFeriasFolha($rows['id_clt'], $dados["data_inicio"], $dados["data_fim"]);
                $totaisFerias[$ferias_dados['ano']][(int) $ferias_dados['mes']] += $ferias_dados['desconto_ferias'];
            }
        }

        return $totaisFerias;
    }

    // lista ferias vencidas
    public function listaFeriasVencidas($id_regiao) {
        $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $vencidas = $this->getFuncionarioFerias($id_regiao);
        foreach ($vencidas as $clt) {
            if ($data_hoje > $clt['data_vencimento'] && $data_hoje > $clt['prazo_vencimento']) {
                $clt['vencida'] = ($clt['data_vencimento'] - $data_hoje) / 86400;
                $retorno[] = $clt;
            }
        }
        return $retorno;
    }

    // lista ferias a vencer
    public function listaFeriasVencer($id_regiao) {
        $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $vencer = $this->getFuncionarioFerias($id_regiao);
        foreach ($vencer as $clt) {
            if ($data_hoje < $clt['data_vencimento'] && $data_hoje > $clt['prazo_vencimento']) {
                $clt['vencendo'] = ($clt['data_vencimento'] - $data_hoje) / 86400;
                $retorno[] = $clt;
            }
        }
        return $retorno;
    }

    public function listaFuncionariosFerias($id_regiao, $id_projeto = null, $pesquisa = NULL, $data_ini_fmt = NULL) {
        $filtroProjeto = (!empty($id_projeto) && $id_projeto != '-1') ? "AND A.id_projeto = {$id_projeto}" : $filtroProjeto = '';
        
        $query_projeto = "SELECT A.*, B.cnpj FROM projeto as A
            LEFT JOIN rhempresa as B 
            ON (A.id_regiao = B.id_regiao AND B.id_projeto = A.id_projeto)
            WHERE A.id_regiao = '$id_regiao' $filtroProjeto AND A.status_reg = '1' OR A.status_reg = '0' ORDER BY A.nome ASC";
        
       //echo $query_projeto . "<br>";
        
        $qr_projetos = mysql_query($query_projeto);

        $array_clt = $this->getFuncionarioFerias($id_regiao, $id_projeto, $pesquisa, TRUE);

        while ($row = mysql_fetch_assoc($qr_projetos)) {
            $return[$row['id_projeto']]['clt'] = $array_clt[$row['id_projeto']];
            $return[$row['id_projeto']]['dados'] = $row;
        }

        return $return;
    }

    /*
     * Pega todos os funcionarios e verifica a data das proximas ferias
     */

    public function getFuncionarioFerias($id_regiao, $id_projeto = null, $pesquisa = NULL, $type_id_projeto = FALSE) {
        
        $cond_projeto = (!empty($id_projeto) && $id_projeto != '-1') ? " AND p.id_projeto = {$id_projeto} " : "";

        if (!empty($pesquisa)) {
            
            $value = explode(';', $pesquisa);
            
            $string = $value[0];
            
            $valorPesquisa = explode(' ', $string);
            
            foreach ($valorPesquisa as $valuePesquisa) {
                
                $ar_pesquisa[] .= "r.nome LIKE '%" . $valuePesquisa . "%'";
                
            }
            
            if(count($value) > 1){
                
                $data_ini_fmt = $value[1];
                
            }
            
            $nome_pesquisa = implode(' AND ', $ar_pesquisa);
            
            $auxPesquisa = " AND (($nome_pesquisa) OR (CAST(matricula AS CHAR) = '{$string}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$string}' OR cpf = '{$string}')) ";
            
            $filtro_data = (!empty($data_ini_fmt)) ? " AND r.id_clt IN (SELECT id_clt FROM rh_ferias WHERE status AND DATE_FORMAT(data_ini,'%d/%m/%Y')='{$data_ini_fmt}')" : '';
            
        }
        
        $query = "
                SELECT 
                    r.id_clt,
                    r.nome,
                    r.data_entrada,
                    r.status,
                    r.id_projeto,
                    r.conta,
                    r.tipo_conta,
                    p.nome AS projeto_nome,
                    c.nome AS curso_nome,
                    DATE_FORMAT(r.data_entrada,'%d/%m/%Y') AS data_entrada_br,
                    (SELECT DATE_FORMAT(MAX(data_aviso),'%d/%m/%Y') FROM rh_recisao WHERE id_clt=r.id_clt) AS data_ini,
                    (SELECT MAX(id_ferias) FROM rh_ferias WHERE id_clt=r.id_clt AND status) AS last_id_ferias,
                    s.especifica
                FROM rh_clt r INNER JOIN projeto p ON r.id_projeto=p.id_projeto
                              INNER JOIN curso c ON r.id_curso=c.id_curso
                              LEFT JOIN rhstatus AS s ON(r.status = s.codigo)
                WHERE (r.status < 60 OR r.status = 200) AND r.id_regiao = {$id_regiao} $cond_projeto $auxPesquisa $filtro_data
                ORDER BY p.nome,r.nome
                ";
                
        $REClts = mysql_query($query) or die($query);
        
        if (mysql_num_rows($REClts) > 0) {
            while ($row_clt = mysql_fetch_assoc($REClts)) {
                $query = "SELECT data_ini,data_fim,total_liquido FROM rh_ferias WHERE id_clt = ' {$row_clt['id_clt']}' AND status = '1'";
                $qr_ferias = mysql_query($query);
                $row_clt['qtd_ferias'] = mysql_num_rows($qr_ferias);
                $ferias = mysql_fetch_assoc($qr_ferias);
                if (empty($ferias['data_ini'])) {
                    $DataEntrada = $row_clt['data_entrada_br'];
                } else {
                    $preview1 = explode('-', $ferias['data_fim']);
                    $preview2 = $preview1[0];
                    $preview3 = explode('/', $row_clt['data_entrada_br']);
                    $DataEntrada = "{$preview3[0]}/{$preview3[1]}/$preview2";
                }

                $row_clt['total_liquido'] = $ferias['total_liquido'];

                $DataEntrada = explode('/', $DataEntrada);

                $F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
                $F_ini_E = explode('/', $F_ini);

                $F_fim = date('d/m/Y', mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1));

                $row_clt['data_aquisicao_ini'] = $F_ini;
                $row_clt['data_aquisicao_fim'] = $F_fim;

                $row_clt['data_vencimento'] = mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1); //DATA DE VENCIMENTO DAS FÉRIAS
                $row_clt['prazo_vencimento'] = mktime(0, 0, 0, $F_ini_E[1] - 3, $F_ini_E[0] - 1, $F_ini_E[2] + 1);

                $retorno1[] = $row_clt;
                $retorno2[$row_clt['id_projeto']][$row_clt['id_clt']] = $row_clt;
            }
        }

        return ($type_id_projeto) ? $retorno2 : $retorno1;
    }

    /**
     * Método para listar todos os clts com seus respectivos períodos aquisitivos, vencidos, não gozados e gozados
     * 
     * @access public
     * @method relatorioFerias
     * @param  $id_regiao
     *         $id_projeto
     *         $funcao
     *         $mesIni
     *         $mesFim
     *         $anoIni
     *         $anoFim
     *         $mesIniAg
     *         $mesFimAg
     *         $anoIniAg
     *         $anoFimAg
     * 
     * @return array
     */      
    public function relatorioFerias($id_regiao, $id_projeto = null, $funcao = null, $mesIni = null, $mesFim = null, $anoIni = null, $anoFim = null , $mesIniAg = null, $mesFimAg = null, $anoIniAg = null, $anoFimAg = null) {
        
        $this->rh = new RhClass();
        
        $this->rh
        ->AddClassExt('Clt') 
        ->AddClassExt('Status')
        ->AddClassExt('Curso')
        ->AddClassExt('Projeto')
        ->AddClassExt('Empresa')
        ->AddClassExt('Bancos')
        ->AddClassExt('Eventos')
        ->AddClassExt('Folha')
        ->AddClassExt('FolhaProc')
        ->AddClassExt('Movimentos')
        ->AddClassExt('MovimentosClt')
        ->AddClassExt('Ferias')
        ->AddClassExt('FeriasItens')
        ->AddClassExt('Unidade')
        ->AddClassExt('Funcionario');
        
        
        $projetos = $this->rh->Projeto->setDefault()->select()->db->getCollection('id_projeto');
        
        $cursos = $this->rh->Curso->setDefault()->select()->db->getCollection('id_curso');
        
        $status = $this->rh->Status->setDefault()->select()->db->getCollection('codigo');
        
        $this->rh
        ->Clt
        ->setDefault() 
        ->setIdRegiao($id_regiao==-1 ? 0 : $id_regiao)        
        ->setIdCurso($funcao==-1 ? 0 : $funcao)        
        ->setIdProjeto($id_projeto==-1 ? 0 : $id_projeto)
        ->db
        ->setQuery(WHERE,"(status < '60' OR status = '200') AND ")        
        ->setQuery(ORDER,"nome ASC");
        
        
        $clts = $this->rh->Clt->select()->db->getCollection('id_clt'); 
        
        foreach ($clts['dados'] as $key => $value) {
            
            $id_clts .= ",{$key}";
            
        }
        
        $this->rh->Clt->setIdClt("{$id_clts},");
        
        //$time = microtime(1);
            
        //echo 'Tempo: ', number_format((microtime(1) - $time),3), "ms\n";
        
        $todos_periodos = $this->rh->Ferias->getTodosPeriodosAquisitivos();
        
        
        if (count($clts) > 0) {
            
            $i = 0;
            foreach ($clts['dados'] as $key_id_clt => $row_clt) {
                
                $i++;


                $arrayPeriodos = $periodoPendente = $periodoGozado = $periodoVencido = $periodoNaoGozado = $dataAgendamento = '';
                $limiteAgendamento = '';

                foreach ($todos_periodos[$key_id_clt] as $key_periodo => $row_periodo) {
                    

                    if($row_periodo['gozado']){

                        $periodoGozado .= "<span style='white-space:nowrap;'>".$row_periodo['data_aquisitivo_ini']->get('d/m/Y')->val().'-'.$row_periodo['data_aquisitivo_fim']->get('d/m/Y')->val()."</span> ";

                    }
                    else {

                        if($row_periodo['vencido']){

                            $periodoVencido .= "<span style='white-space:nowrap;'>".$row_periodo['data_aquisitivo_ini']->get('d/m/Y')->val().'-'.$row_periodo['data_aquisitivo_fim']->get('d/m/Y')->val()."</span> ";
                            
                            $limiteAgendamento .= "<span style='white-space:nowrap;'>".$row_periodo['data_limite_agendamento']->get('d/m/Y')->val()."</span> ";

                        }
                        else {

                            $periodoNaoGozado .= "<span style='white-space:nowrap;'>".$row_periodo['data_aquisitivo_ini']->get('d/m/Y')->val().'-'.$row_periodo['data_aquisitivo_fim']->get('d/m/Y')->val()."</span> ";

                        }


                    }
                    
                    
                    if($row_periodo['data_agendamento']->get('Ymd')->val()!=='19700101') $dataAgendamento = "<span style='white-space:nowrap;'>".$row_periodo['data_agendamento']->get('d/m/Y')->val()."</span> ";
                    
                }


                if (empty($periodoVencido)) {
                    $periodoVencido = '---';
                }
                if (empty($periodoGozado)) {
                    $periodoGozado = '---';
                }
                if (empty($periodoNaoGozado)) {
                    $periodoNaoGozado = '---';
                }
                if (empty($limiteAgendamento)) {
                    $limiteAgendamento = '---';
                }
                
                $retorno[] = array(
                    'projeto_nome' => $projetos['dados'][$clts['dados'][$key_id_clt]['id_projeto']]['nome'],
                    'id_clt' => $key_id_clt,
                    'clt_nome' => $clts['dados'][$key_id_clt]['nome'].($clts['dados'][$key_id_clt]['status_real_time']==10 ? '' :'&nbsp;<span style="color:#069; font-weight:bold;">('.$status['dados'][$clts['dados'][$key_id_clt]['status_real_time']]['especifica'].')</span>'),
                    'funcao' => $cursos['dados'][$clts['dados'][$key_id_clt]['id_curso']]['nome'],
                    'salario' => number_format($clts['dados'][$key_id_clt]['salario'], 2, ',', '.'),
                    'data_entrada' => $this->rh->date->set($clts['dados'][$key_id_clt]['data_entrada'])->get('d/m/Y')->val(),
                    'periodoGozado' => $periodoGozado,
                    'periodoVencido' => $periodoVencido,
                    'periodoNaoGozado' => $periodoNaoGozado,
                    'limiteAgendamento' => $limiteAgendamento,
                    'data_agendamento' => $dataAgendamento
                );


            }
        }
        
        return $retorno;
    }

    public function calc_data($id_clt, $data_entrada, $periodo_aquisitivo) {

// Verificando o Período Aquisitivo
        $aquisitivo_ini = $periodo_aquisitivo[0];
        $aquisitivo_end = $periodo_aquisitivo[1];


        $this->calcFerias->setIdClt($id_clt);
//        $periodo_concessivo = $this->calcFerias->getPeriodoConcessivo($aquisitivo_end);
//
//        $data_limite = date('d/m/Y', strtotime($periodo_concessivo['fim']));
//        $data_dobrada = date('d/m/Y', strtotime($periodo_concessivo['fim']));
        $data_corrente_real = implode('/', array_reverse(explode('-', $aquisitivo_end)));

        $faltas = $this->calcFerias->getFaltasNoPeriodo($aquisitivo_ini, $aquisitivo_end);

        if (!empty($_GET['data_inicio'])) {
            $data_corrente = $_GET['data_inicio'];
        } else {
            $data_corrente = implode('/', array_reverse(explode('-', $aquisitivo_end)));
        }

// Buscando Faltas
        $falta_aquisitivo_ini = explode('-', $aquisitivo_ini);
        $falta_aquisitivo_end = explode('-', $aquisitivo_end);

        if ($falta_aquisitivo_ini[1] == 12) {
            $limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
        } else {
            $limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
        }

        if ($falta_aquisitivo_end[1] == 1) {
            $limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
        } else {
            $limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
        }

        $qr_faltas1 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
        $row_faltas1 = mysql_fetch_array($qr_faltas1);

        $qr_faltas2 = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
        $row_faltas2 = mysql_fetch_array($qr_faltas2);

        if (!isset($_GET['despreza_faltas'])) {

            $faltas = $row_faltas1['faltas'] + $row_faltas2['faltas'];
            $faltas_real = $row_faltas1['faltas'] + $row_faltas2['faltas'];

            if ($faltas <= 5) {
                $qnt_dias = 30;
            } elseif ($faltas >= 6 and $faltas <= 14) {
                $qnt_dias = 24;
            } elseif ($faltas >= 15 and $faltas <= 23) {
                $qnt_dias = 18;
            } elseif ($faltas >= 24 and $faltas <= 32) {
                $qnt_dias = 12;
            } elseif ($faltas > 32) {
                $qnt_dias = 0;
            }
        } else {

            $faltas = 0;
            $faltas_real = $row_faltas1['faltas'] + $row_faltas2['faltas'];
            $qnt_dias = 30;
        }

        $update_movimentos_clt = '0';

        $qr_novo_faltas1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
        while ($row_novo_faltas1 = mysql_fetch_assoc($qr_novo_faltas1)) {
            $update_movimentos_clt .= ',' . $row_novo_faltas1['id_movimento'];
        }

        $qr_novo_faltas2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov = '62' AND status = '1' AND status_ferias = '1' AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
        while ($row_novo_faltas2 = mysql_fetch_assoc($qr_novo_faltas2)) {
            $update_movimentos_clt .= ',' . $row_novo_faltas2['id_movimento'];
        }

        $array = array(
            'direito_dias' => $qnt_dias,
            'faltas' => $faltas,
            'faltas_real' => $faltas_real,
            'update_movimentos_clt' => $update_movimentos_clt,
            'qnt_dias' => $qnt_dias,
            'data_corrente' => $data_corrente,
        );

        return $array;
    }

    public function calc_ferias($dados) {

// Verificando se Desprezou Faltas
        if (!empty($dados['despreza_faltas'])) {
            $despreza_faltas = "&despreza_faltas=true";
        } else {
            $despreza_faltas = NULL;
        }

// Chamando a Variável de Updates de Movimentos
        $update_movimentos_clt = $dados['update_movimentos_clt'];

// Formatando a Data de Início de Férias
        $data_inicio = implode('-', array_reverse(explode('/', $dados['data_inicio'])));

// Calculando o Fim e Retorno de Férias
        $quantidade_dias = $dados['quantidade_dias'];
        $dataE = explode('-', $data_inicio);
        $anoE = $dataE[0];
        $mesE = $dataE[1];
        $diaE = $dataE[2];
        $data_fim = date("Y-m-d", mktime(0, 0, 0, $mesE, $diaE + $quantidade_dias - 1, $anoE));
        $data_retorno = date("Y-m-d", mktime(0, 0, 0, $mesE, $diaE + $quantidade_dias, $anoE));

// Selecionando os Dados do CLT
        $qr_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM rh_clt WHERE id_clt = '{$dados['id_clt']}'");
        $row_clt = mysql_fetch_array($qr_clt);

// Selecionando o Curso
        $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
        $row_curso = mysql_fetch_array($qr_curso);

// Definindo o Período Aquisitivo
        $periodo_aquisitivo = explode('/', $dados['periodo_aquisitivo']);
        $aquisitivo_ini = $periodo_aquisitivo[0];
        $aquisitivo_end = $periodo_aquisitivo[1];

// Verificando Férias Dobradas e Definindo Salário Base
        $preview = explode('-', $aquisitivo_end);
        $retorno['verifica_dobrado'] = date('Y-m-d', mktime(0, 0, 0, $preview[1], $preview[2], $preview[0] + 1));

        if ($retorno['verifica_dobrado'] <= $data_inicio) {
            $salario_base = $row_curso['salario'] * 2;
            $ferias_dobradas = "sim";
            $link_dobradas = "&dobradas=true";
        } else {
            $salario_base = $row_curso['salario'];
            $ferias_dobradas = "nao";
            $link_dobradas = NULL;
        }

// Definindo Salário Variável
        $variavel_aquisitivo_ini = explode('-', $aquisitivo_ini);
        $variavel_aquisitivo_end = explode('-', $aquisitivo_end);

        if ($variavel_aquisitivo_ini[1] == 12) {
            $limite_variavel1 = "mes_mov = '$variavel_aquisitivo_ini[1]'";
        } else {
            $limite_variavel1 = "mes_mov >= '$variavel_aquisitivo_ini[1]'";
        }

        if ($variavel_aquisitivo_end[1] == 1) {
            $limite_variavel2 = "mes_mov = '$variavel_aquisitivo_ini[1]'";
        } else {
            $limite_variavel2 = "mes_mov <= '$variavel_aquisitivo_ini[1]}'";
        }

// Lançamentos
        $qr_variavel1 = mysql_query("SELECT SUM(valor_movimento) AS credito FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]' AND lancamento != 2");
        $row_variavel1 = mysql_fetch_array($qr_variavel1);

        $qr_variavel2 = mysql_query("SELECT SUM(valor_movimento) AS credito FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND tipo_movimento = 'CREDITO' AND id_mov != '151' AND id_mov != '14' AND id_mov != '94' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]' AND lancamento != 2");
        $row_variavel2 = mysql_fetch_array($qr_variavel2);

        $variavel = $row_variavel1['credito'] + $row_variavel2['credito'];
//


        //////////////////////////////////////////////////////////////
        /////CALCULANDO A MÉDIAS DE RENDIMENTOS DOS ÚLTIMOS 6 MESES
        ///////////////////////////////////////////////////////////////
        $dt_referencia = $anoE . '-' . $mesE . '-01';
        $qr_folha = mysql_query("select A.* FROM rh_folha as A
                                INNER JOIN rh_folha_proc as B
                                ON A.id_folha = B.id_folha
                                WHERE A.regiao = '$regiao' AND A.status=3 
                                AND B.status = 3 AND A.terceiro != 1
                             /*    AND A.data_inicio BETWEEN DATE_SUB('$dt_referencia', INTERVAL 12 MONTH) AND NOW()*/
                                AND B.id_clt = '{$dados['id_clt']}'                                
                                ORDER BY A.ano DESC, A.mes DESC LIMIT 12;    
                                    ;") or die(mysql_error());
        while ($row_folha = mysql_fetch_assoc($qr_folha)) {

            $ids_mov = $row_folha['ids_movimentos_estatisticas'];

            $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND id_movimento IN($ids_mov) AND tipo_movimento = 'CREDITO'");
            while ($row_mov = mysql_fetch_assoc($qr_movimento)) {

                //POG para acertar a insalubridade do tipo sempre
                if ($row_mov['id_mov'] == 56 AND $row_folha['ano'] == 2012 AND $row_mov['valor_movimento'] == '135.60') {

                    $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += 124.40;
                    $movimentos[$row_mov['nome_movimento']] += 124.40;
                } else {
                    $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += $row_mov['valor_movimento'];
                    $movimentos[$row_mov['nome_movimento']] += $row_mov['valor_movimento'];
                }
            }
        }

        foreach ($movimentos as $nome_mov => $valor) {
            $salario_variavel += ($valor / 12);
        }

        /**
         * ESSA PORRA É PARA RESOLVER A PICA DAS FÉRIAS
         * POR FAVOR REMOVER DEPOIS QUE A PICA AMOLECER
         */
        if (in_array($_COOKIE['logado'], $func_f71)) {
            $salario_variavel = $_REQUEST['sal_variavel'];
        }

//////FIM CALCULO DA MÉDIA



        $qr_novo_variavel1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND tipo_movimento = 'CREDITO' AND status = '1' AND status_ferias = '1' AND $limite_variavel1 AND ano_mov = '$variavel_aquisitivo_ini[0]'");
        while ($row_novo_variavel1 = mysql_fetch_assoc($qr_novo_variavel1)) {
            $update_movimentos_clt .= ',' . $row_novo_variavel1['id_movimento'];
        }

        $qr_novo_variavel2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND tipo_movimento = 'CREDITO' AND status = '1' AND status_ferias = '1' AND $limite_variavel2 AND ano_mov = '$variavel_aquisitivo_end[0]}'");
        while ($row_novo_variavel2 = mysql_fetch_assoc($qr_novo_variavel2)) {
            $update_movimentos_clt .= ',' . $row_novo_variavel2['id_movimento'];
        }

// Definindo Variáveis
        $salario_contratual = number_format($row_curso['salario'], 2, ".", "");
        $quantidade_dias_calc = 30;
// $quantidade_dias_calc = cal_days_in_month(CAL_GREGORIAN, $mesE, $anoE);
        $salario = ($salario_base / $quantidade_dias_calc) * $quantidade_dias;
        $valor_dia = ($salario_base + $salario_variavel) / $quantidade_dias_calc;
        $valor_total = $valor_dia * $quantidade_dias;
// $um_terco = ((($salario_base + $salario_variavel) / 30) * $quantidade_dias) / 3;
// $um_terco = $valor_total / 3;
        $um_terco = ($salario + $salario_variavel) / 3;
        $remuneracao_calc = $valor_total + $um_terco;

// Base para INSS / IRRF / FGTS
        $calc_inss_irrf_fgts = ((($row_curso['salario'] + $salario_variavel) / $quantidade_dias_calc) * $quantidade_dias) + (((($row_curso['salario'] / $quantidade_dias_calc) * $quantidade_dias) + $salario_variavel) / 3);

// Verificando Faltas
        if (!empty($dados['faltas'])) {
            $faltas = $dados['faltas'];
            $link_faltas = "&faltas=$dados[faltas]";
        } else {
            $faltas = 0;
            $link_faltas = "&faltas=$dados[faltas_real]";
        }

// Verificando Abono Pecuniário (Venda de Dias)
        if (isset($dados['periodo_abono'])) {
            $dias_abono_pecuniario = $dados['direito_dias'] - $dados['quantidade_dias'];
            $link_abono = "&periodo_abono=$dados[periodo_abono]";
        } else {
            $dias_abono_pecuniario = 0;
            $link_abono = "&periodo_abono=0";
        }

        if (isset($dados['periodo_abono']) and ! empty($dias_abono_pecuniario)) {
            $abono_pecuniario = $valor_dia * $dias_abono_pecuniario;
            $umterco_abono_pecuniario = $abono_pecuniario / 3;
        }

// Verificando a Data de Início de Férias
        if (empty($dados['data_inicio'])) {
            echo "<script language='JavaScript'>location.href='index.php?enc=$link&periodo_aquisitivo=$dados[periodo_aquisitivo]&data_inicio=$dados[data_inicio]&quantidade_dias=$dados[quantidade_dias]$link_abono$link_dobradas&data=nulo';
		  </script>";
            exit;
        }


        
        $data_calc = date('Y') . '-01-01'; //USADA PARA CÁLCULO DE INSS E IRRF DE ACORDO COM O ANO DE PORCESSAMENTO DAS FÉRIAS
//ESSA CONDIÇÂO PARA QUANDO O PERÍODO DE FÉRIAS FOR NO ANO SEGUINTE, CALCULAR COM A TABELA DO ANO VIGENTE
        $BASE_INSS = $calc_inss_irrf_fgts;
        if ($row_clt['desconto_inss'] != 1) {
// Calculando INSS
            $this->calculos->MostraINSS($BASE_INSS, $data_calc);
            $inss = $this->calculos->valor;
            $porcentagem_inss = $this->calculos->percentual;
        }

// Calculando IR
        $BASE_IRRF = $BASE_INSS - $inss;
        $this->calculos->MostraIRRF($BASE_IRRF, $dados['id_clt'], $regiao, $data_calc);
        $ir = $this->calculos->valor;

        if ($ir != 0) {
            $PERCENTUAL_IRRF = $this->calculos->percentual;
            $VALOR_DDIR = $this->calculos->valor_deducao_ir_total;
            $QNT_DEPENDENTES_IRRF = $this->calculos->total_filhos_menor_21;
            $PARCELA_DEDUCAO_IRRF = $this->calculos->valor_fixo_ir;
        } else {
            $BASE_IRRF = 0;
        }



// Calculando FGTS
        $fgts = $calc_inss_irrf_fgts * 0.08;

// Buscando Pensão Alimenticia
        $pensao_aquisitivo_ini = explode('-', $aquisitivo_ini);
        $pensao_aquisitivo_end = explode('-', $aquisitivo_end);

        if ($pensao_aquisitivo_ini[1] == 12) {
            $limite_pensao1 = "mes_mov = '$pensao_aquisitivo_ini[1]'";
        } else {
            $limite_pensao1 = "mes_mov >= '$pensao_aquisitivo_ini[1]'";
        }

        if ($pensao_aquisitivo_end[1] == 1) {
            $limite_pensao2 = "mes_mov = '$pensao_aquisitivo_ini[1]'";
        } else {
            $limite_pensao2 = "mes_mov <= '$pensao_aquisitivo_ini[1]'";
        }

        $qr_pensao1 = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = " . date('Y') . " AND id_mov IN('54','63', 223) ORDER BY id_movimento DESC");
        $row_pensao1 = mysql_fetch_array($qr_pensao1);
        $numero_pensao1 = mysql_num_rows($qr_pensao1);

        $qr_pensao2 = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = " . date('Y') . " AND id_mov IN('54','63', 223) ORDER BY id_movimento DESC");
        $row_pensao2 = mysql_fetch_array($qr_pensao2);
        $numero_pensao2 = mysql_num_rows($qr_pensao2);

        $numero_pensao = $numero_pensao1 + $numero_pensao2;

        if (!empty($numero_pensao)) {

            if (!empty($numero_pensao2)) {
                $tipo_pensao = $row_pensao2['id_mov'];
            } else {
                $tipo_pensao = $row_pensao1['id_mov'];
            }

            if ($tipo_pensao == "54") {
                $ps = 0.15;
            } elseif ($tipo_pensao == "223") {
                $ps = 0.20;
            } elseif ($tipo_pensao == "63") {
                $ps = 0.30;
            }

            $pensao_alimenticia = number_format((($salario + $salario_variavel + $um_terco) - ($inss + $ir)) * $ps, 2, ".", "");
        }

        $qr_novo_pensao1 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND status = '1' AND status_ferias = '1' AND $limite_pensao1 AND ano_mov = '$pensao_aquisitivo_ini[0]' AND id_mov IN('54','63')");
        while ($row_novo_pensao1 = mysql_fetch_assoc($qr_novo_pensao1)) {
            $update_movimentos_clt .= ',' . $row_novo_pensao1['id_movimento'];
        }

        $qr_novo_pensao2 = mysql_query("SELECT id_movimento FROM rh_movimentos_clt WHERE id_clt = '{$dados['id_clt']}' AND status = '1' AND status_ferias = '1' AND $limite_pensao2 AND ano_mov = '$pensao_aquisitivo_end[0]' AND id_mov IN('54','63')");
        while ($row_novo_pensao2 = mysql_fetch_assoc($qr_novo_pensao2)) {
            $update_movimentos_clt .= ',' . $row_novo_pensao2['id_movimento'];
        }

// Calculando Variáveis
        $remuneracao_base = number_format($salario + $salario_variavel + $abono_pecuniario, 2, ".", "");
        $total_remuneracoes = number_format($valor_total + $um_terco + $abono_pecuniario + $umterco_abono_pecuniario, 2, ".", "");
        $total_descontos = number_format($pensao_alimenticia + $inss + $ir, 2, ".", "");
        $total_liquido = number_format($total_remuneracoes - $total_descontos, 2, ".", "");

// Calculando Meses Diferentes
        $dias_mes = cal_days_in_month(CAL_GREGORIAN, $mesE, $anoE);
        $dias_ferias1 = $dias_mes - $diaE + 1;
        $dias_ferias2 = $quantidade_dias - $dias_ferias1;

        $valor_total1 = $dias_ferias1 * $valor_dia;
        $acrescimo_constitucional1 = $valor_total1 / 3;
        $total_remuneracoes1 = $valor_total1 + $acrescimo_constitucional1 + $abono_pecuniario + $umterco_abono_pecuniario;

        $valor_total2 = $dias_ferias2 * $valor_dia;
        $acrescimo_constitucional2 = $valor_total2 / 3;
        $total_remuneracoes2 = $valor_total2 + $acrescimo_constitucional2;

// Formatação para exibição
        $retorno['aquisitivo_iniT'] = implode('/', array_reverse(explode('-', $aquisitivo_ini)));
        $retorno['aquisitivo_endT'] = implode('/', array_reverse(explode('-', $aquisitivo_end)));
        $retorno['data_inicioT'] = implode('/', array_reverse(explode('-', $data_inicio)));
        $retorno['data_fimT'] = implode('/', array_reverse(explode('-', $data_fim)));
        $retorno['data_retornoT'] = implode('/', array_reverse(explode('-', $data_retorno)));
        $retorno['salario_contratualT'] = number_format($salario_contratual, 2, ",", "");
        $retorno['salarioT'] = number_format($salario, 2, ",", "");
        $retorno['salario_variavelT'] = number_format($salario_variavel, 2, ",", "");
        $retorno['remuneracao_baseT'] = number_format($remuneracao_base, 2, ",", "");
        $retorno['um_tercoT'] = number_format($um_terco, 2, ",", "");
        $retorno['valor_diaT'] = number_format($valor_dia, 2, ",", "");
        $retorno['valor_totalT'] = number_format($valor_total, 2, ",", "");
        $retorno['inssT'] = number_format($inss, 2, ",", "");
        $retorno['irT'] = number_format($ir, 2, ",", "");
        $retorno['fgtsT'] = number_format($fgts, 2, ",", "");
        $retorno['pensao_alimenticiaT'] = number_format($pensao_alimenticia, 2, ",", "");
        $retorno['total_remuneracoesT'] = number_format($total_remuneracoes, 2, ",", "");
        $retorno['total_descontosT'] = number_format($total_descontos, 2, ",", "");
        $retorno['total_liquidoT'] = number_format($total_liquido, 2, ",", "");
        $retorno['abono_pecuniarioT'] = number_format($abono_pecuniario, 2, ",", "");
        $retorno['umterco_abono_pecuniarioT'] = number_format($umterco_abono_pecuniario, 2, ",", "");
        
        // outras varíváveis de retorno
        $retorno['data_inicio'] = $data_inicio;
        $retorno['faltas'] = $faltas;
        
        return $retorno;
    }
    
    
    /**
     * MÉTODO DE VERIFICAÇÃO DE PERIODO AQUISITIVOS JÁ UTILIZADOS E A VENCER
     * @param type $clt
     */
    public function getPeriodosFerias($clt = null){
        $dados      = array();    
        $criterio   = "";
        if(!empty($clt) && $clt != ""){
            $criterio = " AND A.id_clt = '{$clt}' ";
        }
        
        try{
            $query = "SELECT A.id_clt, A.nome, B.data_aquisitivo_ini, B.data_aquisitivo_fim, 
                            DATE_ADD(B.data_aquisitivo_ini,INTERVAL 1 YEAR) AS prazo_primeiro_periodo, 
                            DATE_ADD(B.data_aquisitivo_ini,INTERVAL 2 YEAR) AS prazo_segundo_periodo, 
                            DATE_SUB(DATE_ADD(B.data_aquisitivo_ini,INTERVAL 2 YEAR),INTERVAL 30 DAY) AS datataaa,
                            IF(DATE_SUB(DATE_ADD(B.data_aquisitivo_ini,INTERVAL 2 YEAR),INTERVAL 30 DAY) >= DATE_ADD(B.data_aquisitivo_ini,INTERVAL 2 YEAR),1,0) AS data_aviso,
                    B.data_ini, B.data_fim, B.total_liquido, B.status AS status_ferias  FROM rh_clt AS A 
                    LEFT JOIN rh_ferias AS B ON(A.id_clt = B.id_clt)
                    LEFT JOIN (SELECT id_saida,tipo,valor,status FROM saida WHERE tipo = '156' AND status = '2') AS C ON(B.total_liquido = C.valor)
                    WHERE (A.`status` < 60 AND A.`status` != 200) {$criterio}";
            $sql = mysql_query($query) or die("Erro ao selecionar dados");        
            
            while($rows = mysql_fetch_assoc($sql)){
                $dados[$rows['id_clt']] = array(
                    "nome"                      => $rows['nome'],
                    "aquisitivo_inicio"         => $rows['data_aquisitivo_ini'],
                    "aquiditivo_final"          => $rows['data_aquisitivo_fim'],
//                    "prazo_primerio_periodo"    => 
                );
            }
            
        }  catch (Exception $e){
            echo $e->getMessage("Não foi possível executar método (getPeriodoFerias)");
        }
        
        return $dados;
    }
    
    /**
     * $tipoPeriodo
     * null => ambos
     * A => apenas periodo Aquisitivo
     * C => apenas periodo Concessivo
     */
    public function getAvisosFerias($id_master, $arrayDiasAviso, $id_regiao = null, $diasLicenca = true, $tipoPeriodo = null){
        
        $hoje = new DateTime(date("Y-m-d"));
        //$hoje->modify("20 DAY");
        $auxRegiao = (!empty($id_regiao)) ? " AND A.id_regiao = '$id_regiao' " : null;
        $sqlClt = mysql_query("
        SELECT A.id_clt, A.id_regiao, A.nome, A.data_entrada, B.nome nomeProjeto, C.regiao nomeRegiao
        FROM rh_clt A LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto) LEFT JOIN regioes C ON (A.id_regiao = C.id_regiao)
        WHERE (A.status < 60 OR A.status = 200) AND DATE_ADD(A.data_entrada,INTERVAL 11 MONTH) < DATE(NOW()) AND B.id_master = $id_master $auxRegiao ORDER BY A.id_regiao, A.nome;") or die(mysql_error());
        while($rowClt = mysql_fetch_assoc($sqlClt)){
            $data = $index = $dias_licenca = null;
            $data_aquisitivo_ini = $data = $rowClt['data_entrada'];
            
            $sqlFerias = mysql_query("SELECT data_aquisitivo_ini, data_aquisitivo_fim FROM rh_ferias WHERE status = 1 AND id_clt = {$rowClt['id_clt']} ORDER BY id_ferias DESC LIMIT 1;");
            while($rowFerias = mysql_fetch_assoc($sqlFerias)){
                $data = $rowFerias['data_aquisitivo_fim'];
                $data_aquisitivo_ini = $rowFerias['data_aquisitivo_ini'];
            }
            
            $data_aquisitivo_fim = new DateTime($data);
            $data_aquisitivo_fim->modify("1 YEAR");
            
            $termino_primeiro_periodo = new DateTime($data_aquisitivo_fim->format('Y-m-d'));
            $data_aquisitivo_fim->modify("1 YEAR");
            //$data_aquisitivo_fim->modify("+1 DAY");
            $termino_segundo_periodo = new DateTime($data_aquisitivo_fim->format('Y-m-d'));
            $limite_concessivo = new DateTime($termino_segundo_periodo->format("Y-m-d"));
            //$limite_concessivo->modify('-30 DAY');
            
            if($termino_primeiro_periodo > $hoje && ($tipoPeriodo == 'A' || $tipoPeriodo == null)){
                $diff = $termino_primeiro_periodo->diff($hoje);
                $dias = $diff->days;
                     if($diff->days <= 10 && in_array(10, $arrayDiasAviso[1])) {$index = 1; $dia = 10;}
                else if($diff->days <= 20 && in_array(20, $arrayDiasAviso[1])) {$index = 1; $dia = 20;}
                else if($diff->days <= 30 && in_array(30, $arrayDiasAviso[1])) {$index = 1; $dia = 30;}
                else if($diff->days <= 40 && in_array(40, $arrayDiasAviso[1])) {$index = 1; $dia = 40;}
                else if($diff->days <= 50 && in_array(50, $arrayDiasAviso[1])) {$index = 1; $dia = 50;}
                else if($diff->days <= 60 && in_array(60, $arrayDiasAviso[1])) {$index = 1; $dia = 60;}
                else if($diff->days <= 70 && in_array(70, $arrayDiasAviso[1])) {$index = 1; $dia = 70;}
                else if($diff->days <= 80 && in_array(80, $arrayDiasAviso[1])) {$index = 1; $dia = 80;}
                else if($diff->days <= 90 && in_array(90, $arrayDiasAviso[1])) {$index = 1; $dia = 90;}
                else if($diff->days <= 100 && in_array(100, $arrayDiasAviso[1])) {$index = 1; $dia = 100;}
                //else if($diff->days > 40)  $index = '>1_40';
            } else if($limite_concessivo >= $hoje && ($tipoPeriodo == 'C' || $tipoPeriodo == null)){
                $diff = $limite_concessivo->diff($hoje);
                $dias = $diff->days;
                //print_array($arrayDiasAviso);
                     if($diff->days <= 10 && in_array(10, $arrayDiasAviso[2])) {$index = 2; $dia = 10;}
                else if($diff->days <= 20 && in_array(20, $arrayDiasAviso[2])) {$index = 2; $dia = 20;}
                else if($diff->days <= 30 && in_array(30, $arrayDiasAviso[2])) {$index = 2; $dia = 30;}
                else if($diff->days <= 40 && in_array(40, $arrayDiasAviso[2])) {$index = 2; $dia = 40;}
                else if($diff->days <= 50 && in_array(50, $arrayDiasAviso[2])) {$index = 2; $dia = 50;}
                else if($diff->days <= 60 && in_array(60, $arrayDiasAviso[2])) {$index = 2; $dia = 60;}
                else if($diff->days <= 70 && in_array(70, $arrayDiasAviso[2])) {$index = 2; $dia = 70;}
                else if($diff->days <= 80 && in_array(80, $arrayDiasAviso[2])) {$index = 2; $dia = 80;}
                else if($diff->days <= 90 && in_array(90, $arrayDiasAviso[2])) {$index = 2; $dia = 90;}
                else if($diff->days <= 100 && in_array(100, $arrayDiasAviso[2])) {$index = 2; $dia = 100;}
                //else if($diff->days > 40)  $index = '>2_40';
            } else if($limite_concessivo < $hoje && ($tipoPeriodo == 'C' || $tipoPeriodo == null)){
                $index = $dia = 'expirado';
            }
            if(!empty($index)){
                $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']] = $rowClt;
                $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']]['dias'] = $diff->days;
                $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']]['termino_primeiro_periodo'] = $termino_primeiro_periodo;
                $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']]['termino_segundo_periodo'] = $termino_segundo_periodo;
                $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']]['data_aquisitivo_ini'] = $data_aquisitivo_ini;
                if($diasLicenca /*&& ($index == 2 || $index == 'expirado')*/){
                    $dias_licenca = $this->getDiasLicenca($data, $termino_primeiro_periodo->format('Y-m-d'), $rowClt['id_clt']);
                    $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']]['dias_licenca'] = $dias_licenca;
                }
                $array[$rowClt['id_regiao']][$index][$dia][$rowClt['id_clt']]['limite_concessivo'] = $limite_concessivo;
                $array[$rowClt['id_regiao']]['nome_regiao'] = $rowClt['nomeRegiao'];
            }
        }
        //print_array($array); 
        return $array;
    }
    
    public function getDiasLicenca($dataInicio, $dataFinal, $id_clt = null){
        
        $dataIni = new DateTime($dataInicio);
        $dataFim = new DateTime($dataFinal);
        $auxClt = (!empty($id_clt)) ? " AND id_clt = '$id_clt' " : $id_clt;
        $sql = "SELECT data, data_retorno, dias
        FROM rh_eventos
        WHERE status = 1 AND (data >= '$dataInicio' AND data <= '$dataFinal' OR data_retorno <= '$dataInicio' AND data_retorno >= '$dataFinal') $auxClt";
//        echo $sql."<br><br>";
        $sql = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($sql)){

            $dataIniEvento = new DateTime($row['data']);
            $dataFimEvento = new DateTime($row['data_retorno']);
            
            $diasTemp = $row['dias'];
            
            if($dataIniEvento < $dataIni){
                $diff = $dataIniEvento->diff($dataIni);
                $diasTemp -= $diff->days;
            } 
            if($dataFimEvento > $dataFim){
                $diff = $dataFimEvento->diff($dataFim);
                $diasTemp -= $diff->days;
            } 
            
            $dias += $diasTemp;
        }
        return $dias;
    }
    
    public function getLimiteConcessivo($id_clt, $format = "d/m/Y"){
        $sqlClt = mysql_query("SELECT A.id_clt, A.id_regiao, A.nome, A.data_entrada FROM rh_clt A WHERE A.id_clt = $id_clt;") or die(mysql_error());
        $rowClt = mysql_fetch_assoc($sqlClt);
        $data = $index = $dias_licenca = null;
        $data_aquisitivo_ini = $data = $rowClt['data_entrada'];
            
        $sqlFerias = mysql_query("SELECT data_aquisitivo_ini, data_aquisitivo_fim FROM rh_ferias WHERE status = 1 AND id_clt = {$rowClt['id_clt']} ORDER BY id_ferias DESC LIMIT 1;");
        while($rowFerias = mysql_fetch_assoc($sqlFerias)){
            $data = $rowFerias['data_aquisitivo_fim'];
            $data_aquisitivo_ini = $rowFerias['data_aquisitivo_ini'];
        }
        
        $limite_concessivo = new DateTime($data);
        $limite_concessivo->modify("22 MONTH");
        
        return $limite_concessivo->format($format);
    }
    
}

?>
