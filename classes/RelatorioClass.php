<?php

/**
 * Description of RelatorioClass
 *
 * @author Leonardo Mendonça
 */
class Relatorio {

    public $dados;

    /*
     * inserir a partir de um array
     */
    public function inserir($dados) {
        $colunas = implode(',', array_keys($dados));
        foreach ($dados as $key => $value) {
            $dados[$key] = "'{$value}'";
        }
        $valores = implode(",", $dados);
        $query = "INSERT INTO relatorios ($colunas,data_cad) VALUES ($valores,NOW())";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        return ($result) ? mysql_insert_id() : false;
    }

    /*
     * editar a partir de um array e um id
     */
    public function editar($dados, $id) {
        foreach ($dados as $key => $value) {
            $up[] = "$key='$value'";
        }
        $updates = implode(',', $up);
        $query = "UPDATE relatorios SET $updates,data_cad=NOW() WHERE id_relatorio=$id";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        return ($result) ? $id : false;
    }

    /*
     * salva se estiver setado um id ou insere
     */
    public function salvar($dados) {
        if (isset($dados['id_relatorio']) && !empty($dados['id_relatorio'])) {
            return $this->editar($dados, $dados['id_relatorio']);
        } else {
            return $this->inserir($dados);
        }
    }

    /*
     * função para carregar os relatorios
     */

    public function carregaRelatorios($id_grupo = null, $id_modulo = null, $id_relatorio = null) {
        $retorno = null; // define nulo se não vier linha da tabela
        $condicaoId = (!empty($id_relatorio)) ? " AND id_relatorio = '{$id_relatorio}' " : "";
        $condicaoModulo = (!empty($id_modulo)) ? " AND id_modulo = '{$id_modulo}' " : "";
        $condicaoIdGrupo = (!empty($id_grupo)) ? " AND id_grupo = '{$id_grupo}'" : '';
        $query = "SELECT * FROM relatorios WHERE  status=1 {$condicaoIdGrupo} {$condicaoId} {$condicaoModulo} ORDER BY nome";
//        echo $query;
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $retorno[] = $row;
        }
        return $retorno;
    }

    /*
     * função para carregar os grupos de relatorios
     */

    public function carregaGrupos($id_grupo = null) {
        $condicao = (!empty($id_grupo)) ? " AND id_grupo = '{$id_grupo}' " : "";
        $query = "SELECT * FROM grupo_relatorio WHERE status = 1 {$condicao} ORDER BY nome";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $retorno[] = $row;
        }
        return $retorno;
    }

    /*
     * função para criar log de relatorios
     */

    public function criaLog($url, $id_relatorio, $id_usuario) {
        $url = explode("?", $url);
        $query = "INSERT INTO relatorios_log (nome_arquivo,data_acesso,id_usuario,id_relatorio) VALUES ('{$url[0]}',NOW(),'{$id_usuario}','{$id_relatorio}');";
        //echo $query;
        $result = mysql_query($query);
        return ($result) ? TRUE : FALSE;
    }

    /*
     * função para exibir tag de relatório novo
     */

    public function relatorioNovo($id_relatorio) {
        $this->dados = $this->carregaRelatorios(null, null, $id_relatorio);
        $arr_date = explode(' ', $this->dados[0]['data_cad']);
        $arr_date = explode('-', $arr_date[0]);
        $data = mktime(0, 0, 0, $arr_date[1], $arr_date[2], $arr_date[0]);
        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $qtd_dias = $hoje - $data;
        $qtd_dias = (int) floor($qtd_dias / (60 * 60 * 24));
        $this->dados = null;
        return ($qtd_dias <= 14) ? '<span class="rel-novo">Novo!</span>' : '';
    }

    /*
     * metodo que retorna os grupos para um select
     */
    public function arrayGrupoSelect(){
        $grupos = $this->carregaGrupos();
        $array[''] = '-- Selecione --';
        foreach ($grupos as $value) {
            $array[$value['id_grupo']] = $value['nome']; 
        }
        return $array;
    }
    
    /*
     * metodo para exluir 
     */
    public function excluir($id){
        $query = "DELETE FROM relatorios WHERE id_relatorio={$id}";
        return mysql_query($query) or die(mysql_error());
    }
    /*
     * metodo para exluir grupo
     */
    public function excluirGrupo($id){
        $query = "DELETE FROM grupo_relatorio WHERE id_grupo={$id}";
        return mysql_query($query) or die(mysql_error());
    }
    
    /*
     * inserir grupo a partir de um array
     */
    public function inserirGrupo($dados) {
        $colunas = implode(',', array_keys($dados));
        foreach ($dados as $key => $value) {
            $dados[$key] = "'{$value}'";
        }
        $valores = implode(",", $dados);
        $query = "INSERT INTO grupo_relatorio ($colunas) VALUES ($valores)";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        return ($result) ? mysql_insert_id() : false;
    }

    /*
     * editar grupo a partir de um array e um id
     */
    public function editarGrupo($dados, $id) {
        foreach ($dados as $key => $value) {
            $up[] = "$key='$value'";
        }
        $updates = implode(',', $up);
        $query = "UPDATE grupo_relatorio SET $updates WHERE id_grupo=$id";
        //echo $query;
        $result = mysql_query($query) or die(mysql_error());
        return ($result) ? $id : false;
    }

    /*
     * salva grupo se estiver setado um id ou insere
     */
    public function salvarGrupo($dados) {
        if (isset($dados['id_grupo']) && !empty($dados['id_grupo'])) {
            return $this->editarGrupo($dados, $dados['id_grupo']);
        } else {
            return $this->inserirGrupo($dados);
        }
    }
    
    /**
     * @author Lucas Praxedes
     * @param type $tabela
     * @param type $regiao
     * @param type $tipo_contratacao
     * @param type $statusFunc
     * @param type $projeto
     * @param type $auxAllProj
     * @return type
     */
    public function relParticipantes ($tabela, $regiao, $tipo_contratacao, $statusFunc, $projeto, $auxAllProj = null) {
        
        if($auxAllProj) {
            
            $sqlProj = "SELECT id_projeto FROM projeto WHERE id_regiao = $regiao;";
            $queryProj = mysql_query($sqlProj);
            $projeto = []; 
            
            while($arrProj = mysql_fetch_assoc($queryProj)) {
                $projeto[] = $arrProj['id_projeto'];
            }
            
            $projeto = implode(',', $projeto);
            $auxProj = " AND id_projeto IN ($projeto) ";
            
        } else {
            
            $auxProj = " AND id_projeto IN ($projeto) ";
            
        }

        $str_qr_relatorio = "SELECT *, date_format(data_ctps, '%d/%m/%Y') AS data_ctps
                                FROM $tabela
                                WHERE id_regiao = '$regiao' 
                                    AND tipo_contratacao = '$tipo_contratacao'
                                    $statusFunc
                                    $auxProj
                                ORDER BY nome";

        $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
        
        while($arrParticipantes = mysql_fetch_object($qr_relatorio)){
            $participantes[] = $arrParticipantes;
        }        
        return $participantes;
    }
}
