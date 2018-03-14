<?php
/**
 * Classe dos registros de saída
 * 
 * @file      FinaceiroClass.php
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link      
 * @copyright 2016 F71
 * @author    
 * @package   Financeiro
 * @access    public  
 * @version:  3.0.0000I - ??/??/???? - N/D     - Versão Inicial 
 * @version:  3.0.2646I - 03/06/2016 - Jacques - Adicionada categoria de saída 10 que representa as saídas que tiveram remessas geradas
 * @version:  3.0.6886I - 16/08/2016 - Jacques - Alterada a categoria de saída de 10 para 11 e adicionado as categorias 12,13 e 14
 * @todo 
 * @example:  
 * 
 * 
 */
class Financeiro{
    
    /**
     * Retorna um vetor com agrupamento de registro por categoria baseado no vencimento e remessa eletrônica
     * 
     * @access protected
     * @method getSaidaEntradaBanco
     * @param  $id_regiao = null
     * @param  $id_banco
     * @param  $totais = true
     * 
     * @return $arrSaidas
     */       
    public function getSaidaEntradaBanco($id_regiao = null, $id_banco, $totais = true, $condicao = null) {
        
        $auxCondicao = ($condicao) ? " AND " . implode(' AND ', $condicao) : null;
//        print_array($auxCondicao);
        if($id_regiao) $aux = " A.id_regiao in ({$id_regiao}) AND ";
//        $qrSaidas = "
//        SELECT id_saida, n_documento, valor, adicional, caixinha, tipo, nome, data_vencimento, impresso, user_impresso, data_impresso, 1 indice FROM saida WHERE $aux status = 1 AND flag_remessa=0 AND data_vencimento = CURDATE() AND id_banco = {$id_banco}
//        UNION
//        SELECT id_saida, n_documento, valor, adicional, caixinha, tipo, nome, data_vencimento, impresso, user_impresso, data_impresso, 2 indice FROM saida WHERE $aux status = 1 AND flag_remessa=0 AND data_vencimento < CURDATE() AND data_vencimento != '0000-00-00' AND (YEAR(data_vencimento) = YEAR(CURDATE()) OR YEAR(data_vencimento) = (YEAR(CURDATE()) - 1)) AND id_banco = {$id_banco}
//        UNION
//        SELECT id_saida, n_documento, valor, adicional, caixinha, tipo, nome, data_vencimento, impresso, user_impresso, data_impresso, 3 indice FROM saida WHERE $aux status = 1 AND flag_remessa=0 AND data_vencimento > CURDATE() AND id_banco = {$id_banco}
//        UNION
//        SELECT id_saida, n_documento, valor, adicional, 0 AS caixinha, tipo, nome, data_vencimento, impresso, user_impresso, data_impresso, (CASE flag_remessa WHEN 1 THEN '11' WHEN 2 THEN '12' WHEN 3 THEN '13' END) indice FROM saida WHERE $aux status = 1 AND flag_remessa IN (1,2,3,4) AND id_banco = {$id_banco}
//        ORDER BY indice, data_vencimento ASC, id_saida
//        ";
        $qrSaidas = "
        SELECT A.id_saida, A.valor, A.adicional, A.caixinha, A.tipo, A.data_vencimento, A.impresso, A.user_impresso, A.data_impresso, A.nome,
        IF(A.tipo = 327, CONCAT(TIMESTAMPDIFF(MONTH, F.contratado_em, A.data_vencimento), '/', TIMESTAMPDIFF(MONTH, F.contratado_em,F.encerrado_em)),A.n_documento) AS n_documento, 
        COUNT(B.id_pg) AS anexos_pg, COUNT(C.id_saida_file) AS anexos, D.anexo_rescisao, A.flag_remessa, E.id_nfse,
        CASE 
            WHEN A.data_vencimento = CURDATE() AND A.flag_remessa = 0 THEN 1
            WHEN A.data_vencimento < CURDATE() AND A.flag_remessa = 0 THEN 2
            WHEN A.data_vencimento > CURDATE() AND A.flag_remessa = 0 THEN 3
            WHEN A.flag_remessa = 1 THEN 11
            WHEN A.flag_remessa = 2 THEN 12
            WHEN A.flag_remessa = 3 THEN 13
            ELSE 99 
        END AS indice, 
        CONCAT(timestampdiff(month, F.contratado_em, A.data_vencimento), '/', timestampdiff(month, F.contratado_em,F.encerrado_em)) parcela
        FROM saida A
        LEFT JOIN saida_files_pg B ON (B.id_saida = A.id_saida)
        LEFT JOIN saida_files AS C ON (C.id_saida = A.id_saida)
        LEFT JOIN (
            SELECT AA.id_saida, COUNT(AA.id_especifico) AS anexo_rescisao
            FROM pagamentos_especifico AS AA 
            INNER JOIN rh_recisao AS AB ON (AA.id_rescisao = AB.id_recisao AND AB.`status` = 1)
            GROUP BY AA.id_saida
        ) AS D ON (D.id_saida = A.id_saida)
        LEFT JOIN nfse_saidas AS E ON (E.id_saida = A.id_saida)
        LEFT JOIN prestadorservico F ON (A.id_prestador = F.id_prestador)
        WHERE  A.status = 1 AND A.data_vencimento != '0000-00-00' AND A.id_banco = {$id_banco} 
        $auxCondicao
        GROUP BY A.id_saida
        ORDER BY A.data_vencimento ASC, A.id_saida";
//        print_array($qrSaidas);exit;
        $qrSaidas = mysql_query($qrSaidas) or die("ERRO SELEÇÃO DE SAIDAS: " . mysql_error());
        
        while($rowSaidas = mysql_fetch_assoc($qrSaidas)){
            $arrSaidas[$rowSaidas['indice']][$rowSaidas['id_saida']] = $rowSaidas;
            $arrSaidas[$rowSaidas['indice']][$rowSaidas['id_saida']]['total'] = str_replace(',', '.', $rowSaidas['valor']) + str_replace(',', '.', $rowSaidas['adicional']);
            if($totais)
                $arrSaidas['totalizador_saida'] += str_replace(',', '.', $rowSaidas['valor']) + str_replace(',', '.', $rowSaidas['adicional']);
        }
        
        $sqlEntradas = "SELECT A.id_entrada, A.valor, A.adicional, A.tipo, A.nome, A.data_vencimento,
        COUNT(B.id_files) AS anexos, C.id_notas
        FROM entrada A
        LEFT JOIN entrada_files B ON (B.id_entrada = A.id_entrada AND B.status = '1')
        LEFT JOIN notas_assoc C ON(C.id_entrada = A.id_entrada)
        WHERE $aux A.id_banco = {$id_banco} AND A.status = 1
        $auxCondicao
        GROUP BY A.id_entrada
        ORDER BY A.data_vencimento, A.id_entrada";
        $qryEntradas = mysql_query($sqlEntradas) or die("ERRO SELEÇÃO DE ENTRADA: " . mysql_error()); 
        
        while($rowEntrada = mysql_fetch_assoc($qryEntradas)){
            $arrSaidas['4'][$rowEntrada['id_entrada']] = $rowEntrada;
            $arrSaidas['4'][$rowEntrada['id_entrada']]['total'] = str_replace(',', '.', $rowEntrada['valor']) + str_replace(',', '.', $rowEntrada['adicional']);
            if($totais)
                $arrSaidas['totalizador_entrada'] += str_replace(',', '.', $rowEntrada['valor']) + str_replace(',', '.', $rowEntrada['adicional']);
        }
        
        return $arrSaidas;
    }
    
    function getTiposFiltro() {
        $sql = "SELECT * FROM entradaesaida WHERE id_entradasaida IN (SELECT tipo FROM saida WHERE status = 1) ORDER BY nome";
        $qry = mysql_query($sql);
        $array['t'] = 'TODOS';
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_entradasaida']] = $row['nome'];
        }
        return $array;
    }
    
}
?>
