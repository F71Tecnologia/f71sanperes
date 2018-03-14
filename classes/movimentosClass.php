<?php

/**
 * Classe movimentos
 *
 * @author Leonardo
 * @version 1.0
 */
class movimentos {

    private $dados; // dados de retorno da query
    public $mesIni;
    public $mesFim;
    public $anoIni;
    public $anoFim;
    public $dataIni;
    public $dataFim;
    private $mesCorrente;
    private $anoCorrente;
    public $idProjeto;
    public $idCLT;

    public function __construct($idProjeto, $idCLT, $mesIni, $anoIni) {
        $this->idProjeto = $idProjeto;
        $this->idCLT = $idCLT;
        
        $this->mesIni = $mesIni;
        $this->anoIni = $anoIni;
        $this->dataIni = "{$this->anoIni}-{$this->mesIni}";
        
        $this->dataFim = date("Y-m", mktime(0, 0, 0, $this->mesIni + 12, 01, $this->anoIni));
        $this->anoFim = date("Y", mktime(0, 0, 0, $this->mesIni + 12, 01, $this->anoIni));
        $this->mesFim = date("m", mktime(0, 0, 0, $this->mesIni + 12, 01, $this->anoIni));
    }

    public function gerarRelatorio() {
        $query1 = "SELECT A.id_projeto,B.id_folha,B.mes,B.ano,B.ids_movimentos_estatisticas,B.terceiro,B.tipo_terceiro,A.valor_dt,A.inss_dt,A.ir_dt,A.a7001 AS vale_transporte,A.rend
            FROM rh_folha_proc AS A
            LEFT JOIN rh_folha AS B ON (A.id_folha = B.id_folha)
            WHERE A.id_projeto = {$this->idProjeto} AND A.id_clt = {$this->idCLT} 
            AND concat(A.ano,'-',A.mes) BETWEEN '" . $this->dataIni . "' 
            AND '" . $this->dataFim . "' 
            AND A.status = 3 ORDER BY A.ano,A.mes;";
//        echo $query1 . "<br>\n";
        $result1 = mysql_query($query1);
        while ($row1 = mysql_fetch_assoc($result1)) {
            $this->anoCorrente = $row1['ano'];
            $this->mesCorrente = $row1['mes'];
            // desconto de vale transporte
            if (!empty($row1['vale_transporte']) && $row1['vale_transporte'] != 0.00) {
                $this->dados[$this->anoCorrente][$this->mesCorrente]["7001"]['nome'] = "DESCONTO VALE TRANSPORTE";
                $this->dados[$this->anoCorrente][$this->mesCorrente]["7001"]['tipo'] = "DESCONTO";
                $this->dados[$this->anoCorrente][$this->mesCorrente]["7001"]['valor'] = $row1['vale_transporte'];
            }
            $this->getMovimentos($row1['ids_movimentos_estatisticas'], $row1['terceiro'], $row1['tipo_terceiro']);
        }
        if (empty($this->dados)) { // se $dados estiver vazia, retorna um array vazio
            return array();
        }
        return $this->dados;
    }

    private function getMovimentos($ids_movimentos_estatisticas, $terc, $tipo_terc) {
        $query2 = "SELECT * FROM rh_movimentos_clt WHERE id_clt = {$this->idCLT} AND id_movimento IN ({$ids_movimentos_estatisticas});";
//        echo $query2 . "<br>\n";
        $result2 = mysql_query($query2);
        while ($row2 = mysql_fetch_assoc($result2)) {
            $mesAtual = sprintf("%02d", $row2['mes_mov']);
            if ($terc == 1) { // verifica se é 13º salário
                if ($tipo_terc == 1) { // verifica se é 1ª parcela
                    $this->dados[$this->anoCorrente][13][$row2['cod_movimento']]['valor'] = $row2['valor_movimento'];
                }
                if ($tipo_terc == 2) { // verifica se é 2ª parcela
                    $this->dados[$this->anoCorrente][13][$row2['cod_movimento']]['valor'] += $row2['valor_movimento'];
                }
                $this->dados[$this->anoCorrente][13][$row2['cod_movimento']]['nome'] = $row2['nome_movimento'];
                $this->dados[$this->anoCorrente][13][$row2['cod_movimento']]['tipo'] = $row2['tipo_movimento'];
            } else {
                if ($row2['valor_movimento'] != 0.0) {
                    if ($row2['lancamento'] == '2') {
                        $this->dados[$this->anoCorrente][$this->mesCorrente][$row2['cod_movimento']]['nome'] = $row2['nome_movimento'];
                        $this->dados[$this->anoCorrente][$this->mesCorrente][$row2['cod_movimento']]['tipo'] = $row2['tipo_movimento'];
                        $this->dados[$this->anoCorrente][$this->mesCorrente][$row2['cod_movimento']]['valor'] = $row2['valor_movimento'];
                    } else {
                        $this->dados[$this->anoCorrente][$mesAtual][$row2['cod_movimento']]['nome'] = $row2['nome_movimento'];
                        $this->dados[$this->anoCorrente][$mesAtual][$row2['cod_movimento']]['tipo'] = $row2['tipo_movimento'];
                        $this->dados[$this->anoCorrente][$mesAtual][$row2['cod_movimento']]['valor'] = $row2['valor_movimento'];
                    }
                }
            }
        }
    }

}
