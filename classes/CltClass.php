<?php
/**
 * Description of CltClass
 *
 * @author Ramon Lima
 */
class CltClass {
    
    /**
     * MÉTODO PARA LISTAR TODOS OS CLTS ATIVOS (STATUS 10)
     * @author Lucas Praxedes - 22/11/2016
     * @param int $regiao
     */
    public function carregaClts() {
        $sqlClts = "SELECT id_clt, nome 
                    FROM rh_clt
                    WHERE status = 10
                    ORDER BY nome";

        $queryClts = mysql_query($sqlClts) or die(mysql_error());
        $arrClts['-1'] = '-- Selecione o Clt --';
        while ($rowClt = mysql_fetch_object($queryClts)) {
            $arrClts[$rowClt->id_clt] = $rowClt->nome;
        }

        return $arrClts;
    }
    
    /**
     * MÉTODO PARA LISTAR CLT
     * @param type $objeto
     * @return type
     */
    public function getListaCLT($objeto){
        $arr[] = montaCriteriaSimples($objeto->id_clt, "A.ano");
        $arr[] = montaCriteriaSimples($objeto->id_projeto, "A.id_projeto");
        $arr[] = montaCriteriaSimples($objeto->id_regiao, "A.id_regiao");
        $arr[] = montaCriteriaSimples($objeto->id_curso, "A.id_curso");
        $arr[] = montaCriteriaSimples($objeto->id_unidade, "A.id_unidade");
        $arr[] = montaCriteriaSimples($objeto->status, "A.status");
        
        $_campos = "A.*, 
                        DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nasciBR,
                        DATE_FORMAT(A.data_rg, '%d/%m/%Y') AS data_rgBR,
                        DATE_FORMAT(A.data_escola, '%d/%m/%Y') AS data_escolaBR,
                        DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entradaBR,
                        DATE_FORMAT(A.data_saida, '%d/%m/%Y') AS data_saidaBR,
                        DATE_FORMAT(A.data_exame, '%d/%m/%Y') AS data_exameBR,
                        DATE_FORMAT(A.dada_pis, '%d/%m/%Y') AS data_pisBR,
                        DATE_FORMAT(A.data_ctps, '%d/%m/%Y') AS data_ctpsBR,
                        DATE_FORMAT(A.data_cad, '%d/%m/%Y') AS data_cadBR,
                        DATE_FORMAT(A.dataalter, '%d/%m/%Y') AS dataalterBR,
                        DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demiBR";
        $_order = "ORDER BY A.nome";
        
        $arrJoins[] = (isset($objeto->join_projeto)) ? "LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)" : null;
        $arrJoins[] = (isset($objeto->join_regiao)) ? "LEFT JOIN regioes AS C ON (A.id_regiao = C.id_regiao)" : null;
        $arrJoins[] = (isset($objeto->join_unidade)) ? "LEFT JOIN unidade AS D ON (A.id_unidade = D.id_unidade)" : null;
        $arrJoins[] = (isset($objeto->join_escolaridade)) ? "LEFT JOIN escolaridade AS E ON (A.id_unidade = E.id)" : null;
        $arrJoins[] = (isset($objeto->join_curso)) ? "LEFT JOIN curso AS F ON (A.id_curso = F.id_curso)" : null;
        $arrJoins[] = (isset($objeto->join_etnia)) ? "LEFT JOIN etnias AS G ON (A.etnia = G.id)" : null;
        $arrJoins[] = (isset($objeto->join_pagamento)) ? "LEFT JOIN pagamentos AS H ON (A.tipo_pagamento = H.id_pg)" : null;
        $arrJoins[] = (isset($objeto->join_usuarioCriado)) ? "LEFT JOIN funcionario AS I ON (A.sis_user = I.id_funcionario)" : null;
        $arrJoins[] = (isset($objeto->join_usuarioAlterado)) ? "LEFT JOIN funcionario AS J ON (A.useralter = J.id_funcionario)" : null;
        $arrJoins[] = (isset($objeto->join_sindicato)) ? "LEFT JOIN rhsindicato AS K ON (A.rh_sindicato = K.id_sindicato)" : null;
        
        
        if(isset($objeto->campos) && !empty($objeto->campos)){
            $_campos = $objeto->campos;
        }
        if(isset($objeto->order) && !empty($objeto->order)){
            $_order = $objeto->order;
        }
        
        $criteria = (array_filter($arr)); //REMOVE ARRAYS VAZIOS
        $joins = (array_filter($arrJoins)); //REMOVE ARRAYS VAZIOS
        
        $query = "SELECT {$_campos}
                        FROM rh_clt AS A
                        ".implode("\r\n",$joins)."
                        WHERE ".implode(" AND ",$criteria)."
                        {$_order}";
        
        $sql = mysql_query($query) or die("Erro ao selecionar dados de clt");
        
        while($linha = mysql_fetch_assoc($sql)){
                $dados[$linha['id_folha']] = $linha;
            }
        
        return $dados;
        
    }
    
    /**
     * MÉTODO TRAZ PERIODO DE CONTRATAÇÃO E TIPO (30x60) (45x45) (60x30)
     * @param type $clt
     * @return type
     */
    public function getDadosContratacao($clt){
        
        $dados = array();
        $query = "SELECT *,
		DATE_ADD(data_primeiro_periodo,INTERVAL segundo_periodo DAY) AS data_segundo_periodo, DATE_FORMAT(DATE_ADD(data_primeiro_periodo,INTERVAL segundo_periodo DAY), '%d/%m/%Y') AS data_segundo_periodo_br FROM (
                        SELECT *, DATE_ADD(data_entrada,INTERVAL primeiro_periodo DAY) AS data_primeiro_periodo, DATE_FORMAT(DATE_ADD(data_entrada,INTERVAL primeiro_periodo DAY), '%d/%m/%Y') AS data_primeiro_periodo_br FROM(
                                SELECT A.id_clt, A.prazoexp, A.data_entrada, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_entrada_br, 
                                        IF(A.prazoexp = 1, '30 + 60', 
                                                IF(A.prazoexp = 2, '45 + 45', 
                                                        IF(A.prazoexp = 3,'60 + 60', 'TIPO NÃO CADASTRADO'))) AS contratacao,
                                        IF(prazoexp = 1, '30', IF(prazoexp = 2, '45', IF(prazoexp = 3,'60',''))) AS primeiro_periodo,
                                        IF(prazoexp = 1, '60', IF(prazoexp = 2, '45', IF(prazoexp = 3,'60',''))) AS segundo_periodo
                                FROM rh_clt AS A WHERE A.id_clt = '{$clt}'
                        ) AS tmp 
                ) AS tmp2";
        $sql = mysql_query($query) or die("Erro ao selecionar dados de contratação");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados = array("data_entrada" => $rows['data_entrada_br'], "contratacao" => $rows['contratacao'], "data_primeiro" => $rows['data_primeiro_periodo_br'], "data_segundo" => $rows['data_segundo_periodo_br']);
            }
        }
        
        return $dados;
    }
    
    
    /**
     * Método para pegar os dados do CLT para a Rescisão
     * @param type $id_clt
     * @param type $motivo_rescisao  - Tipo da rescisao 
     * @return type
     */
    public function getDadosCltRescisao($id_clt, $motivo_rescisao){        
         $qr_clt = mysql_query("SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
                        DATE_FORMAT(data_demi, '%d/%m/%Y') as data_demiF, 
                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
                        A.salario_outra_empresa, A.desconto_outra_empresa,  
                        IF(DATEDIFF(data_demi, data_entrada) >= 365, 1, 0) as um_ano,                        
                        B.salario, B.nome as nome_funcao, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                        B.periculosidade_30,
                        C.especifica as tipo_rescisao                        
                        FROM rh_clt as A 
                        INNER JOIN curso as B
                        ON B.id_curso = A.id_curso
                        LEFT JOIN rhstatus as C
                        ON C.codigo = {$motivo_rescisao}
                        WHERE id_clt = '{$id_clt}' ") or die(mysql_error());
         return   $row_clt = mysql_fetch_assoc($qr_clt);
    }
    
    /* INICIO PV */
    
    
    /**
     * Método para carregar os dados do CLT nas propriedades do objeto
     * @param type $id_clt
     * @return type
     */
    public function carregaClt($id_clt) {
        //echo '<br>';
        $sql = "SELECT A.id_clt, A.nome,CONCAT(A.endereco,A.numero,', ',A.bairro,', ',A.cidade,' - ',A.uf) as endereco,
                        D.id_projeto, D.nome AS nome_projeto,
                        DATE_FORMAT(A.data_nasci,'%d/%m/%Y') as data_nasci,
                        DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_entrada,
                        DATE_FORMAT(A.data_demi,'%d/%m/%Y') as data_demi,
                        DATE_ADD(A.data_entrada, INTERVAL '89' DAY) AS data_fim_experiencia,
                        A.nacionalidade,A.naturalidade,A.tipo_conta,
                        A.agencia, A.conta,C.nome as nome_banco, C.razao,
                        A.matricula, A.locacao,
                        A.cpf,A.rg,A.titulo,A.campo1 as ctps, A.serie_ctps, A.pis,
                        B.nome as nome_curso, B.id_curso, FORMAT(B.salario,2) as salario,
                        -- CONCAT(B.letra,B.numero) as letranumero,
                        CONCAT('fotosclt/' ,A.id_regiao ,'_' ,A.id_projeto,'_' ,A.id_clt ,'.gif') AS foto,
                        E.id_sindicato,
                        E.nome as sindicato,
                        E.cnpj,
                        E.insalubridade,
                        E.periculosidade,
                        E.adNoturno,
                        E.hr_noturna,
                        E.prcentagem_add_noturno,
                        E.creche,
                        E.creche_base,
                        E.creche_percentual,
                        E.creche_idade,
                        E.contribuicao_assistencial,
                        F.unidade
                        
            FROM rh_clt as A 
            LEFT JOIN curso as B ON (B.id_curso = A.id_curso)
            LEFT JOIN bancos as C ON (A.banco = C.id_banco)
            LEFT JOIN projeto AS D ON(A.id_projeto=D.id_projeto)
            LEFT JOIN rhsindicato AS E ON (A.rh_sindicato = E.id_sindicato)
            LEFT JOIN unidade AS F ON (A.id_unidade = F.id_unidade)
            WHERE A.id_clt =$id_clt;";
        //echo $sql.'<br>';
        $re = mysql_query($sql) or die("CltClass|carregaClt: ".mysql_error());
        $arr = mysql_fetch_assoc($re);
        return $this->setDados($arr);
        
    }
    
    public function getClt($id_clt) {
        $sql = mysql_query("SELECT *
            FROM rh_clt
            WHERE id_clt = '{$id_clt}'") or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        return $res;
    }
    
    public function getFoto($url=''){
        return is_file($url.'fotosclt/'.$this->foto) ? $url.$this->foto : $url.'fotosclt/semimagem.gif';
    }
    
    private function setDados(Array $arr){
        $arr_keys = array_keys($arr);
        $arr_values = array_values($arr);
        
        for($x=0; $x<count($arr_keys); $x++){
            $campo = $arr_keys[$x];
            $this->$campo = $arr_values[$x];
        }
        return $this;
    }
    /* FIM PV */
    
    /* inicio leonardo */
    public function getCltsParaSuporte($id_projeto) {
        $query = "SELECT a.id_clt,a.nome AS clt_nome, d.nome AS funcao, a.status
                    FROM rh_clt AS a
                    INNER JOIN curso AS d ON (a.id_curso = d.id_curso)
                    WHERE a.id_projeto = $id_projeto AND (a.status IN(10,200))
                    ORDER BY d.nome,a.nome;";
        $resp = mysql_query($query);
        
        while ($row = mysql_fetch_assoc($resp)) {
            $retorno[] = $row;
        }
        
        return $retorno;
    }
    /* fim leonardo */
    
    /**
     * Coloca status 200 no clt
     * @param type $id_clt - ID do clt
     * @param type $data_demi - Formato Brasileiro 01/01/2016
     * @param type $data_aviso - Formato Brasileiro 01/01/2016
     * @return boolean true 
     */
    public function setCltAguardandoDemissao($id_clt,$data_demi,$data_aviso){
        $_data_demi = date("Y-m-d", strtotime(str_replace("/", "-", $data_demi)));
        $_data_aviso = date("Y-m-d", strtotime(str_replace("/", "-", $data_aviso)));
        
        mysql_query("UPDATE rh_clt SET data_demi = '$_data_demi', data_aviso = '$_data_aviso', status = '200' WHERE id_clt = '$id_clt' LIMIT 1");
        return true;
    }
}
