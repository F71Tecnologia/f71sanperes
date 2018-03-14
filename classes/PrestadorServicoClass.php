<?php

require_once("ApiClass.php");

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PrestadorServicoClass
 *
 * @author Ramon
 */
class PrestadorServico {

    private $api;
    private $idMaster = 1;

    /**
     * 
     */
    public function __construct() {
        $this->api = new ApiClass();
    }
    
    public function listaPrestadoresByProjetoWeb($id_projeto, $competencia = 'NOW()') {
        $competencia = ($competencia != 'NOW()') ? "'$competencia-01'" : $competencia;
//        print_array($_REQUEST);
        $retur = "";
//        $query = "SELECT B.cnpj,A.c_razao,A.numero_contrato,A.objeto,A.contratado_em,A.encerrado_em,A.valor,'' as valorpago,'' npar
//            FROM prestadorservico AS A
//            LEFT JOIN rhempresa AS B ON (A.id_regiao = B.id_regiao)
//            WHERE A.id_projeto  = {$this->idMaster}
//            ORDER BY A.c_razao";
        $query = "SELECT 1200 AS cod, 
        B.c_cnpj AS cnpj, B.c_razao AS razao, IF(B.numero>0, B.numero, CONCAT(LPAD(B.id_projeto, 4, 0), LPAD(B.id_prestador, 6, 0))) AS contrato, B.objeto, 
        DATE_FORMAT(B.contratado_em, '%d/%m/%Y') AS DataInicioVigencia, DATE_FORMAT(B.encerrado_em, '%d/%m/%Y') AS DataFimVigencia, REPLACE(B.valor, ',', '.') AS valor_contrato,
        -- SUM(REPLACE(A.valor, ',', '.')) AS valor_parcela, 
        REPLACE(A.valor, ',', '.') AS valor_parcela, 
        CONCAT(timestampdiff(month, B.contratado_em, A.data_vencimento), '/', timestampdiff(month, B.contratado_em,B.encerrado_em)) parcela
        FROM saida A
        INNER JOIN prestadorservico B ON (A.id_prestador = B.id_prestador)
        WHERE A.status = 2 AND A.id_projeto = '{$id_projeto}' AND DATE_FORMAT(A.data_vencimento, '%m%Y') = DATE_FORMAT($competencia, '%m%Y') AND B.`status` = 1 AND B.prestador_tipo != 3 AND B.prestacao_contas = 1
        -- GROUP BY A.id_prestador
        ORDER BY A.data_vencimento ASC, A.id_saida";
        if($_COOKIE['debug'] == 666){
            print_array($query);exit;
        }
        $sql = mysql_query($query) or die(mysql_error());
        $retur = $this->api->montaRetorno($sql);
        
        return $retur;
    }
    
    public static function listaPrestadoresByProjeto($id_projeto = null, $campos = "*") {
        
        $rs = montaQuery("prestadorservico", "{$campos},DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr", "status = 1 AND id_projeto = {$id_projeto} AND encerrado_em >= CURRENT_DATE()", "c_razao");
        
        return $rs;
    }
    
    public static function getTipoDoc($item) {
        //PEGA O TIPO DE DOCUMENTO SOLICITADO
        $qr = montaQueryFirst("prestador_tipo_doc", "*", "prestador_tipo_doc_id = {$item}");
        return $qr;
    }
    
    public static function listarDocs($empresa, $documento, $ordem = null, $limit = null, $return = "array", $debug = false) {
        //PEGA OS DOCUMENTOS DE DETERMINADO PRESTADOR
        $qr = montaQuery("prestador_documentos", "*, DATE_FORMAT(data_vencimento, '%d/%m/%Y') as data_vencimentobr", "prestador_tipo_doc_id = {$documento} AND id_prestador = {$empresa}", $ordem, $limit, $return, $debug);
        return $qr;
    }
    
    public static function getStatusList($id_prestador) {
        $qr = "SELECT COUNT(B.prestador_documento_id) as qnt FROM prestador_tipo_doc AS A
            LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id AND B.id_prestador = {$id_prestador})
            WHERE B.id_prestador IS NOT NULL
            GROUP BY B.`status`
            ORDER BY A.ordem";
        
        $rs = mysql_query($qr);
        $row = mysql_fetch_assoc($rs);
        $qnt = ($row['qnt'] > 0) ? $row['qnt'] : 0;
        return $qnt;
    }
    
    public static function getDocsVencidos($id_prestador) {
        $qr = "SELECT *,IF(qntDocs IS NOT NULL AND qntDocsVen IS NULL,1,0) as resultado FROM (
                 SELECT A.prestador_tipo_doc_id,A.prestador_tipo_doc_nome,A.ordem,B.*,C.* FROM prestador_tipo_doc AS A
                 LEFT JOIN 
                  (
                   SELECT prestador_tipo_doc_id as qntDocsVen FROM prestador_documentos
                    WHERE id_prestador = {$id_prestador} AND data_vencimento > CURDATE()
                    ORDER BY data_vencimento
                  ) 
                  AS B ON (A.prestador_tipo_doc_id = B.qntDocsVen)
                 LEFT JOIN 
                  (
                   SELECT prestador_tipo_doc_id AS qntDocs FROM prestador_documentos
                    WHERE id_prestador = {$id_prestador}
                    ORDER BY data_vencimento
                  ) 
                 AS C ON (A.prestador_tipo_doc_id = C.qntDocs)
                 ORDER BY A.ordem
                ) AS temp
                HAVING resultado = 1";
        
        $rs = mysql_query($qr);
        $row = mysql_num_rows($rs);
        return $row;
    }
    
    public static function getPrestador($id_prestador) {
        $qr = "SELECT A.*, DATE_FORMAT(A.contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(A.encerrado_em, '%d/%m/%Y') AS encerrado_embr, B.municipio AS c_cidade
            FROM prestadorservico AS A
            LEFT JOIN municipios AS B ON (A.c_cod_cidade = B.id_municipio)
            WHERE id_prestador = {$id_prestador};";
        $result = mysql_query($qr);
//        $qr = montaQueryFirst("prestadorservico", "*,DATE_FORMAT(contratado_em, '%d/%m/%Y') AS contratado_embr, DATE_FORMAT(encerrado_em, '%d/%m/%Y') AS encerrado_embr", "id_prestador = {$id_prestador}");
//        return $qr;
        return $result;
    }
    
    public static function getUnidadeMedida($item) {
        $qr = montaQueryFirst("prestador_medida", "*", "id_medida = $item");
        return $qr;
    }
    
    public static function listMedidas() {
        $qr = montaQuery("prestador_medida", "*");
        return $qr;
    }
    
    public static function listMedidasForSelect() {
        $rs = PrestadorServico::listMedidas();
        $retorno = array("-1" => "« Selecione »");
        foreach ($rs as $val) {
            $retorno[$val['id_medida']] = $val['medida'];
        }
        return $retorno;
    }
    
    public static function getPrestadorFinanceiro($id_master) {
        $ano = $_REQUEST['ano'];
        
        $result = "SELECT A.id_prestador,B.c_razao,B.c_cnpj
            FROM saida AS A
            LEFT JOIN prestadorservico AS B ON (A.id_prestador = B.id_prestador)
            LEFT JOIN projeto AS C ON (C.id_projeto = A.id_projeto)
            WHERE A.id_prestador != 0 AND YEAR(A.data_vencimento) = {$ano} AND
            A.estorno = 0 AND A.status = 2 AND C.id_master = {$id_master}
            GROUP BY A.id_prestador
            ORDER BY A.data_vencimento";
        
        $prest = mysql_query($result) or die(mysql_error());
        return $prest;
    }
    
    public static function getSaidasPrestador($id_prestador) {
        $ano = $_REQUEST['ano'];                      

        $result = "SELECT B.id_saida, MONTH(A.data) AS mes, B.qntd, B.valor
            FROM ano AS A
            LEFT JOIN (
            SELECT id_saida, SUM(CAST(
            REPLACE(valor,',','.') AS DECIMAL(10,2))) AS valor, COUNT(id_saida) AS qntd, MONTH(data_vencimento) AS vencimento
            FROM saida
            WHERE id_prestador = {$id_prestador} AND YEAR(data_vencimento) = {$ano} AND STATUS = 2
            GROUP BY MONTH(data_vencimento)) AS B ON (B.vencimento = MONTH(A.data))
            WHERE YEAR(A.data) = {$ano}
            GROUP BY MONTH(DATA)";
        
        $saida = mysql_query($result) or die(mysql_error());
        return $saida;
    }
    
    public static function getPrestadorFinanceiroSimplificado($id_master) {
        $ano = $_REQUEST['ano'];
        
        $result = "SELECT A.id_prestador, B.c_razao, B.c_cnpj,
            A.id_saida, A.nome, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS vencimento, TRIM(A.especifica) as bug, CAST(
           REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS cvalor, A.comprovante, A.estorno, A.darf
           FROM saida AS A
           LEFT JOIN prestadorservico AS B ON (A.id_prestador = B.id_prestador)
           LEFT JOIN projeto AS C ON (C.id_projeto = A.id_projeto)
           WHERE A.id_prestador != 0 AND YEAR(A.data_vencimento) = '{$ano}' AND 
            A.estorno = 0 AND A.status = 2 AND C.id_master = '{$id_master}'
           ORDER BY A.id_prestador, A.data_vencimento /*LIMIT 325,25*/";
        
        $prest = mysql_query($result) or die(mysql_error());
        return $prest;
    }
    
    public static function getSaidasEspecifica($id_prestador, $mes) {
        $ano = $_REQUEST['ano'];
        
        $result = "SELECT id_saida,nome, DATE_FORMAT(data_vencimento, '%d/%m/%Y') AS vencimento,especifica, CAST(
            REPLACE(valor, ',', '.') AS DECIMAL(13,2)) AS cvalor,comprovante,estorno,darf
            FROM saida
            WHERE id_prestador = {$id_prestador} AND MONTH(data_vencimento) = {$mes} AND YEAR(data_vencimento) = {$ano}";
        
        $saida = mysql_query($result) or die(mysql_error());
        return $saida;
    }
    
    public function cadastraPrestadorBasico($dados) {
        
        $usu = carregaUsuario();
        
        $dadosPrestador['id_regiao'] = $usu['id_regiao'];
        $dadosPrestador['id_projeto'] = $usu['id_regiao'];
        $dadosPrestador['id_medida'] = 0;
        $dadosPrestador['aberto_por'] = $usu['id_funcionario'];
        $dadosPrestador['aberto_em'] = date('Y-m-d');
        
        $dadosPrestador['contratado_em'] = date('Y') . "-01-01";
        $dadosPrestador['encerrado_em'] = (date('Y') + 1) . "-01-01";
        
        $dadosPrestador['c_razao'] = utf8_decode($dados['c_razao']);
        $dadosPrestador['c_fantasia'] = utf8_decode($dados['c_razao']);
        $dadosPrestador['c_cnpj'] = $dados['cnpj'];
        
        $dadosPrestador['c_responsavel'] = "Cadastro Direto";
        $dadosPrestador['co_responsavel'] = "Cadastro Direto";
        
        $dadosPrestador['co_municipio'] = "Goiânia";
        
        $dadosPrestador['assunto'] = $dados['especifica'];
        $dadosPrestador['objeto'] = $dados['especifica'];
        
        $dadosPrestador['valor'] = 100.00;
        $dadosPrestador['status'] = 1;
        $dadosPrestador['prestador_tipo'] = 1;
        
        return $this->inserePrestador($dadosPrestador);
    }
    
    private function inserePrestador($arrayDados) {
        
        /* echo "<pre>";
        print_r($arrayDados);
          echo "</pre>";exit; */
        
        $campos = array_keys($arrayDados);
        return sqlInsert("prestadorservico", $campos, $arrayDados);
    }
        
    public static function getLayoutContratos($id = null, $id_servico = null) {
        $and = "";
        
        if(!is_null($id)){
            $and = "AND A.id_layout_contrato = {$id}";
    }
        
        if(!is_null($id_servico)){
            $and = "AND A.id_cnae = {$id_servico}";
}
        
        $sql = "SELECT A.*, B.descricao AS tipo_servico
            FROM prestador_layout_contrato AS A
            LEFT JOIN cnae AS B ON(A.id_cnae = B.id_cnae)
            WHERE A.status = 1 {$and}";
        
        $result = mysql_query($sql) or die(mysql_error());
        
        if($and != ""){
            $dados = mysql_fetch_assoc($result);
            
            return $dados;
        }else{
            return $result;
        }
    }
    
    public static function getListaServicos() {
        $sql = "SELECT *
            FROM cnae AS A
            WHERE A.status = 1
            ORDER BY A.mais_usados DESC, A.descricao";
        
        $query = mysql_query($sql) or die(mysql_error());
        
        $dados = array("" => "Selecione um serviço");
        
        while ($rst = mysql_fetch_assoc($query)) {
            $dados[$rst['id_cnae']] = $rst['descricao'];
        }
        
        return $dados;
    }       

    public static function cadLayoutContrato() {
//        print_array($_REQUEST); exit();
        
        $nome = $_REQUEST['nome_contrato'];
        $tipo_servico = $_REQUEST['tipo_servico'];
        $conteudo = $_REQUEST['editor1'];
        
        // VERIFICA SE JÁ EXISTE LAYOUT CADASTRADO PARA O SERVIÇO
        $_sql = "SELECT * FROM prestador_layout_contrato AS A WHERE A.id_cnae = '{$tipo_servico}'";
        $_qry = mysql_query($_sql) or die(mysql_error());
        
        if(($tipo_servico == "") || ($conteudo == "")){
            $_SESSION['MESSAGE'] = 'Erro ao salvar dados, Todos os dados são obrigatórios!';
            $_SESSION['MESSAGE_COLOR'] = 'danger';
            
        }else{
        
            if(mysql_num_rows($_qry) > 0){
                $_SESSION['MESSAGE'] = 'Erro ao salvar dados, Já existe um layout para esse tipo de serviço!';
                $_SESSION['MESSAGE_COLOR'] = 'danger';

            }else{
                $sql = "INSERT INTO prestador_layout_contrato (nome, conteudo, id_cnae) VALUES ('{$nome}', '{$conteudo}', '{$tipo_servico}')";
                $qry = mysql_query($sql) or die(mysql_error());

                if($qry){
                    $qry_cnae_mais_usados = "UPDATE cnae SET mais_usados = 1 WHERE id_cnae = '{$tipo_servico}'";
                    mysql_query($qry_cnae_mais_usados) or die(mysql_error());

                    $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
                    $_SESSION['MESSAGE_COLOR'] = 'success';
                }else{
                    $_SESSION['MESSAGE'] = 'Erro ao salvar dados!';
                    $_SESSION['MESSAGE_COLOR'] = 'danger';
                }
            }
        }
    }
    
    public static function editLayoutContrato($id) {
        $nome = $_REQUEST['nome_contrato'];
        $tipo_servico = $_REQUEST['tipo_servico'];
        $conteudo = $_REQUEST['editor1'];                
        
        if(($tipo_servico == "") || ($conteudo == "")){
            $_SESSION['MESSAGE'] = 'Erro ao atualizar dados, Todos os dados são obrigatórios!';
            $_SESSION['MESSAGE_COLOR'] = 'danger';
            
        }else{
            $sql = "UPDATE prestador_layout_contrato SET nome = '{$nome}', conteudo = '{$conteudo}', id_cnae = '{$tipo_servico}' WHERE id_layout_contrato = '{$id}'";
            $qry = mysql_query($sql) or die(mysql_error());
            
            if($qry){
                $qry_cnae_mais_usados = "UPDATE cnae SET mais_usados = 1 WHERE id_cnae = {$tipo_servico}";
                mysql_query($qry_cnae_mais_usados) or die(mysql_error());

                $_SESSION['MESSAGE'] = 'Informações atualizadas com sucesso!';
                $_SESSION['MESSAGE_COLOR'] = 'success';
            }else{
                 $_SESSION['MESSAGE'] = 'Erro ao atualizar dados!';
                    $_SESSION['MESSAGE_COLOR'] = 'danger';
            }
        }
    }
    
    public static function delLayoutContrato($id) {
        $sql = sqlUpdate("prestador_layout_contrato", " status = 0", "id_layout_contrato = {$id}", false);
        
        return $sql;
    }
    
    public static function getCamposContrato() {
        $campos = array (
            "'Contratante'",
            "'CNPJ do Contratante'",
            "'Endereço do Contratante'",
            "'Bairro do Contratante'",
            "'Cidade do Contratante'",
            "'Estado do Contratante'",
            "'Nome fantasia da contratada'",
            "'CNPJ da contratada'",
            "'Endereço da contratada'",
            "'Município onde será executado o serviço'",
            "'Valor do contrato'",
            "'Banco da contratada'",
            "'Agência da contratada'",
            "'Conta da contratada'",
            "'Cidade'",
            "'Estado'",
            "'Data inicial do contrato'",
            "'Data atual'"
        );
        
        return $campos;
    }
}