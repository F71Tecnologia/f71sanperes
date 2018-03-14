<?php

/**
 * 
 */
class NotaFiscal{
    
    private $debug;

    public function __construct($debug = false, $logado = null) {
        if($debug && $logado == $_COOKIE['logado'])
            $this->debug = true;
    }
    
    /**
     * LISTAGEM DE NOTAS FISCAIS
     * @return type
     */
    public function getNotas($nota = null, $regiao = null, $projeto = null, $prestador = null, $mes_comp = null, $ano_comp = null){
        $criteria = "";
        if(!empty($nota)){
            $criteria .= " AND A.id_nota = '{$nota}' ";
        }
        if(!empty($regiao)){
            $criteria .= " AND A.id_regiao = '{$regiao}' ";
        }
        if(!empty($projeto)){
            $criteria .= " AND A.id_projeto = '{$projeto}' ";
        }
        if(!empty($prestador)){
            $criteria .= " AND A.id_prestador = '{$prestador}' ";
        }
        if(!empty($mes_comp)){
            $criteria .= " AND A.mes_competencia = '{$mes_comp}' ";
        }
        if(!empty($ano_comp)){
            $criteria .= " AND A.ano_competencia = '{$ano_comp}' ";
        }
        
        
        $dados = array();
        $lista = "SELECT E.c_razao AS prestador, A.id_nota, A.id_regiao, A.id_projeto,A.id_grupo,A.id_subgrupo,A.cod_tipo, 
            A.id_regiao_prestador, A.id_projeto_prestador, A.tipo_empresa, A.status_prestador, A.id_prestador as id_prestador_cad, A.descricao,
            A.numero_documento,A.serie_documento, data_emissao_nf,A.mes_competencia,A.ano_competencia,A.valor_bruto_nf, 
            B.nome AS projeto,C.nome_grupo AS grupo, D.nome AS subgrupo, 
            DATE_FORMAT(A.data_emissao_nf,'%d/%m/%Y') AS data_emissao,
            A.mes_competencia, A.ano_competencia, A.valor_bruto_nf
            FROM notas_fiscais AS A
            LEFT JOIN (SELECT id_projeto,nome FROM projeto) AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN (SELECT * FROM entradaesaida_grupo) AS C ON(A.id_grupo = C.id_grupo)
            LEFT JOIN (SELECT * FROM entradaesaida_subgrupo) AS D ON(A.id_subgrupo = D.id_subgrupo)
            LEFT JOIN prestadorservico AS E ON(A.id_prestador = E.id_prestador)
            WHERE A.status = '1' {$criteria} 
        ORDER BY id_nota DESC";
            
          
         
        //DEBUG    
        if($this->debug)
            $this->debug($lista,"SELEÇÃO DE NOTAS");   
            
        try{
            $sql_lista = mysql_query($lista);
            if(mysql_num_rows($sql_lista) > 0){
                while($linhas = mysql_fetch_assoc($sql_lista)){
                    $dados[] = array(
                        "nota" => $linhas['id_nota'], 
                        "id_regiao" => $linhas['id_regiao'],
                        "id_projeto" => $linhas['id_projeto'],
                        "id_grupo" => $linhas['id_grupo'],
                        "id_subgrupo" => $linhas['id_subgrupo'],
                        "cod_tipo" => $linhas['cod_tipo'],
                        "id_regiao_prestador" => $linhas['id_regiao_prestador'],
                        "id_projeto_prestador" => $linhas['id_projeto_prestador'],
                        "tipo_empresa" => $linhas['tipo_empresa'],
                        "status_prestador" => $linhas['status_prestador'],
                        "id_prestador" => $linhas['id_prestador_cad'],
                        "nome_prestador" => $linhas['prestador'],
                        "descricao" => $linhas['descricao'],
                        "projeto" => $linhas['projeto'],
                        "grupo" => $linhas['grupo'],
                        "subgrupo" => $linhas['subgrupo'],
                        "num_doc" => $linhas['numero_documento'],
                        "serie_doc" => $linhas['serie_documento'],
                        "data_emissao" => $linhas['data_emissao'],
                        "mes_competencia" => $linhas['mes_competencia'],
                        "ano_competencia" => $linhas['ano_competencia'],
                        "valor_bruto" => $linhas['valor_bruto_nf']
                     );
                }
            }
        }catch(Exception $e) {
            echo $e->getMessage("Error ao selecionar notas fiscais");
        }
        
        return $dados;
    }
    
    
    /**
     * MÉTODO DE CADASTRO DE NOTAS
     * @param type $nota
     */
    public function cadNotas($dados){
        $retorno = false;
        $valor_bruto = str_replace(".", "", $dados['valor_bruto']);
        $valor_bruto = str_replace(",", ".", $valor_bruto);
        $data_emissao = date("Y-m-d", strtotime(str_replace("/", "-", $dados['data_emissao'])));
        
        $query = "INSERT INTO notas_fiscais (id_regiao,id_projeto,id_grupo,id_subgrupo,cod_tipo,id_regiao_prestador,id_projeto_prestador,tipo_empresa,status_prestador,id_prestador,descricao,numero_documento,serie_documento,data_emissao_nf,mes_competencia,ano_competencia,valor_bruto_nf,criado_por) VALUES  
        ('{$dados['regiao']}','{$dados['cad_projeto']}','{$dados['cad_grupo']}','{$dados['cad_subgrupo']}','{$dados['cad_tipo']}','{$dados['cad_regiao_prestador']}','{$dados['cad_projeto_prestador']}','{$dados['tipo_empresa']}','{$dados['status']}','{$dados['cad_prestador']}','{$dados['descricao']}','{$dados['num_documento']}','{$dados['serie_documento']}','{$data_emissao}','{$dados['cad_mes']}','{$dados['cad_ano']}','{$valor_bruto}','{$_COOKIE['logado']}')";
        
           
        //DEBUG    
        if($this->debug)
            $this->debug($query,"CADASTRO DE NOTAS");   
        
        try{
            $sql = mysql_query($query);
            if($sql){
                $retorno = true;
            }
        }  catch (Exception $e){
            echo $e->getMessage("Erro ao inserir dados da nota");
        }
        
        return $retorno;
    }
    
    /**
     * MÉTODO DE EDIÇÃO DE NOTAS
     * @param type $nota
     */
    public function editNotas($dados){
        
        $retorno = false;
        $valor_bruto = str_replace(".", "", $dados['valor_bruto']);
        $valor_bruto = str_replace(",", ".", $valor_bruto);
        $data_emissao = date("Y-m-d", strtotime(str_replace("/", "-", $dados['data_emissao'])));
        
        $query = "UPDATE notas_fiscais SET
                id_regiao = '{$dados['regiao']}',
                id_projeto = '{$dados['cad_projeto']}',
                id_grupo = '{$dados['cad_grupo']}',
                id_subgrupo = '{$dados['cad_subgrupo']}',
                cod_tipo = '{$dados['cad_tipo']}',
                id_regiao_prestador = '{$dados['cad_regiao_prestador']}',
                id_projeto_prestador = '{$dados['cad_projeto_prestador']}',
                tipo_empresa = '{$dados['tipo_empresa']}',
                status_prestador = '{$dados['status']}',
                id_prestador = '{$dados['cad_prestador']}',
                descricao = '{$dados['descricao']}',
                numero_documento = '{$dados['num_documento']}',
                serie_documento = '{$dados['serie_documento']}',
                data_emissao_nf = '{$data_emissao}',
                mes_competencia = '{$dados['cad_mes']}',
                ano_competencia = '{$dados['cad_ano']}',
                valor_bruto_nf = '{$valor_bruto}'
                WHERE id_nota = '{$dados['nota']}'";
            
        //DEBUG    
        if($this->debug)
            $this->debug($query,"EDIÇÃO DE NOTAS");   
                        
        try{
            $sql = mysql_query($query);
            if($sql){
                $retorno = true;
            }
        }catch(Exception $e){
            echo $e->getMessage("Erro ao alterar dados da nota");
        }
        
        return $retorno;
    }
    
    /**
     * MÉDOTO PARA REMOVER NOTAS
     * @param type $nota
     */
    public function removeNotas($nota){
        $retorno = false;
        $query = "UPDATE notas_fiscais SET status = '0' WHERE id_nota = '{$nota}'";
        
        //DEBUG    
        if($this->debug)
            $this->debug($query,"DELETE DE NOTAS");   
        
        try{
            $sql = mysql_query($query);
            if($sql){
                $retorno = true;
            }
        }catch(Exception $e){
            echo $e->getMessage("Erro ao remover nota");
        }
        
        return $retorno;
    }

    /**
     * MÉTODO DEBUG
     */
    public function debug($data, $titulo = NULL){
        echo "<pre>";
            if($titulo != NULL)
                echo "<p>«««««««««««««««««"
                . "««««««««««««««« {$titulo} »»»»»»»»»»»»»»»»»»»"
                . "»»»»»»»»»»»»»»»</p>";
            print_r($data);
        echo "</pre>";
    }
    
}

?>