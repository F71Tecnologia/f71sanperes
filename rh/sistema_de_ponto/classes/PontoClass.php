<?php

class Ponto {

    private $total_participantes;
    public $unidade;
    public $competencia;
    public $movimentos = array();
    public $pis = array();
    
    public function __construct() {
        $this->setMovimentos();
    }
    
    public function setMovimentos(){
        //TEM QUE MELHORAR ISSO
        $dados[] = array("id_mov" => 232, "cod" => 50249, "nome" => "FALTAS", "tipo" => "DEBITO");
        $dados[] = array("id_mov" => 66, "cod" => 9000, "nome" => "ADICIONAL NOTURNO", "tipo" => "CREDITO");
        $dados[] = array("id_mov" => 152, "cod" => 8080, "nome" => "HORA EXTRA", "tipo" => "CREDITO");
        $dados[] = array("id_mov" => 236, "cod" => 50252, "nome" => "ATRASO", "tipo" => "DEBITO");
        
        $this->movimentos = $dados;
    }
    
    public function validaArquivo($tmp, $regiao, $projeto) {
        
        $erro = array();
        $corpo = array();
        $returno = array();
        
        //LENDO O ARQUIVO AINDA NA MEMÓRIA
        $dados_arquivo = file($tmp);

        //CONTADOR DE LINHAS NO ARQUIVO
        $linhas = 0;
        foreach ($dados_arquivo as $values) {
            $linhas++;
        }
        $this->total_participantes = $linhas - 3;

        //TRATANDO CABEÇALHOS
        $cabecalho = explode(";", $dados_arquivo[1]);
        $unidade = $cabecalho[0];
        $cnpj_unidade = $cabecalho[1];
        $data_inicio = date("Y-m-d", strtotime(str_replace("/", "-", $cabecalho[2])));
        $data_final = date("Y-m-d", strtotime(str_replace("/", "-", $cabecalho[3])));
        $competencia = $cabecalho[4];
        $cod_ses = $cabecalho[5];
        
        //VARIÁVEIS PUBLICAS 
        $this->unidade = str_replace(" ", "_", $cabecalho[0]);
        $this->competencia = $cabecalho[4];

        
        //VALIDANDO COD_SES
        if ($projeto == "-1") {
            $erro[] = utf8_encode("Selecione um projeto");
        }
               
        //VERIFICA SE JÁ EXISTE IMPORTAÇÃO
        $existe = $this->verificaExistencia($unidade, $competencia);
        if($existe['status']){
            $erro[] = utf8_encode("Já existe importação para esse projeto e competência");
        }
        
        

        //GRAVANDO CABEÇALHO
        if (count($erro) == 0) {
            $id_cabecalho = $this->gravaCabecalho($unidade, $projeto, $cnpj_unidade, $data_inicio, $data_final, $competencia, $cod_ses);
            
            //print_r($this->total_participantes);exit();
            //TRATANDO O CORPO DO ARQUIVO        
            for ($i = 2; $i <= $this->total_participantes + 1; $i++) {
                $itens[] = explode(";", $dados_arquivo[$i]);
            }
            
            for ($y = 0; $y <= $this->total_participantes; $y++) {
                $corpo[$y]['pis'] = $itens[$y][0];
                $corpo[$y]['hora_a_trabalhadar'] = $itens[$y][1];
                $corpo[$y]['hora_trabalhadas'] = $itens[$y][2];
                $corpo[$y]['hora_extra'] = $itens[$y][3];
                $corpo[$y]['dsrs'] = $itens[$y][4];
                $corpo[$y]['atrasos'] = $itens[$y][5];
                $corpo[$y]['horas_justificadas'] = $itens[$y][6];
                $corpo[$y]['banco_de_horas'] = $itens[$y][7];
                $corpo[$y]['banco_do_pedido'] = $itens[$y][8];
                $corpo[$y]['faltas_em_dias'] = $itens[$y][9];
                $corpo[$y]['salario_normal'] = $itens[$y][10];
                $corpo[$y]['hora_extra_50'] = $itens[$y][11];
                $corpo[$y]['adicional_noturno'] = $itens[$y][12];
                $corpo[$y]['perc_adic_noturno'] = $itens[$y][13];
            }
            
            //GRAVANDO OS ITENS DO ARQUIVO
            $this->gravaCorpo($corpo, $id_cabecalho);
        }
        
        //LIMPANDOA A VARIAVEL
        unset($dados_arquivo);
        
        return $erro;
    }

    /**
     * MÉTODO PARA GRAVAÇÃO DO CABEÇALHO DO ARQUIVO
     * @param type $unidade
     * @param type $cnpj_unidade
     * @param type $data_inicio
     * @param type $data_final
     * @param type $competencia
     * @param type $cod_ses
     * @return type
     */
    public function gravaCabecalho($unidade,$id_projeto,$cnpj_unidade,$data_inicio,$data_final,$competencia,$cod_ses) {
        $query = "INSERT INTO importacao_header_ponto (nome_projeto,id_projeto,cnpj_projeto,data_inicio_arquivo,data_fim_arquivo,competencia,codigo_ses,status) VALUES ('{$unidade}','{$id_projeto}','{$cnpj_unidade}','{$data_inicio}','{$data_final}','{$competencia}','{$cod_ses}','1')";
        $sql = mysql_query($query) or die("Erro ao cadastrar cabeçalho de importação");
        $ultimo_id = mysql_insert_id();
        return $ultimo_id;
    }

    public function gravaCorpo($dados, $id_cabecalho) {
        $valores = "";
        $total_registro = 0;
        foreach ($dados as $values) {
            $total_registro++;
            //TRATANDO DADOS 
            $hora_a_trabalhada = ($values['hora_a_trabalhadar'] != 0) ? $this->formata_hora_especial($values['hora_a_trabalhadar']) : "000:00:00";
            $hora_trabalhada = ($values['hora_trabalhadas'] != 0) ? $this->formata_hora_especial($values['hora_trabalhadas']) : "000:00:00";
            $hora_extra = ($values['hora_extra'] != 0) ? $this->formata_hora_especial($values['hora_extra']) : "000:00:00";
            $dsrs = ($values['dsrs'] != 0) ? $this->formata_hora_especial($values['dsrs']) : "000:00:00";
            $atrasos = ($values['atrasos'] != 0) ? $this->formata_hora_especial($values['atrasos']) : "000:00:00";
            $horas_justificadas = ($values['horas_justificadas'] != 0) ? $this->formata_hora_especial($values['horas_justificadas']) : "000:00:00";
            $banco_de_horas = ($values['banco_de_horas'] != 0) ? $this->formata_hora_especial($values['banco_de_horas']) : "000:00:00";
            $banco_do_pedido = ($values['banco_do_pedido'] != 0) ? $this->formata_hora_especial($values['banco_do_pedido']) : "000:00:00";
            $hora_extra_50 = ($values['hora_extra_50'] != 0) ? $this->formata_hora_especial($values['hora_extra_50']) : "000:00:00";
            $adicional_noturno = ($values['adicional_noturno'] != 0) ? $this->formata_hora_especial($values['adicional_noturno']) : "000:00:00";

            //MONTANDO LINHA DE DADOS 
            if($values['pis'] != 0){
            $valores .= "('{$id_cabecalho}',
                          '{$values['pis']}',
                          '{$hora_a_trabalhada}',
                          '{$hora_trabalhada}',
                          '{$hora_extra}',
                          '{$dsrs}',
                          '{$atrasos}',
                          '{$horas_justificadas}',
                          '{$banco_de_horas}',
                          '{$banco_do_pedido}',
                          '{$values['faltas_em_dias']}',
                          '{$values['salario_normal']}',
                          '{$hora_extra_50}',
                          '{$adicional_noturno}',
                          '{$values['perc_adic_noturno']}'),";
            }
        }
        
        $valores = substr($valores, 0, -1);
        $query = "INSERT INTO importacao_campos_ponto (id_header,pis,horas_a_trabalhar,horas_trabalhadas,horas_extras,dsrs,atrasos,horas_justificadas,banco_de_horas,banco_do_periodo,faltas_em_dias,salario_normal,hora_extra,adicional_nortuno,percentual_adic_noturno ) VALUES $valores";
        $sql = mysql_query($query) or die("erro ao cadastrar ponto");

        if ($sql) {
            $total_registro -= 1; 
            $query = "UPDATE importacao_header_ponto SET status = '2', total_de_registro = '{$total_registro}' WHERE id_header = '{$id_cabecalho}'";
            $sql = mysql_query($query) or die("erro ao atualizar status e numero de participantes");
        }
    }

    
    /**
     * 
     * @param type $unidade
     * @return type
     */
    public function getIdProjeto($unidade){
        $query = "SELECT * FROM projeto WHERE nome = '{$unidade}'";
        $sql = mysql_query($query) or die('Erro ao seleciona id do projeto');
        while($linha = mysql_fetch_assoc($sql)){
            $id = $linha['id_projeto'];
        }
        
        return $id;
    }
    
    /**
     * MÉTODO QUE VERIFICA SE JÁ EXISTE IMPORTAÇÃO PARA A MESMA UNIDADE E COMPETENCIA.
     * @param type $unidade
     * @param type $competencia
     * @return int
     */
    public function verificaExistencia($unidade, $competencia){
        $retorno = array("status" => 0);
        
        $query = "SELECT * FROM importacao_header_ponto WHERE nome_projeto = '{$unidade}' AND competencia = '{$competencia}' AND status IN('2','3')";
        $sql = mysql_query($query) or die('Erro ao verificar importações duplicada');
        if(mysql_num_rows($sql) > 0){
            $retorno = array("status" => 1);
        }
        
        return $retorno;
        
    }
    /**
     * 
     * @return type
     */
    public function listaPontosImportado(){
        $dados = array();
        $query = "SELECT *, CONCAT(DATE_FORMAT(data_inicio_arquivo, '%d/%m/%Y'), ' até ', DATE_FORMAT(data_fim_arquivo, '%d/%m/%Y')) AS periodo FROM importacao_header_ponto WHERE status IN('2','3') ORDER BY id_header DESC";
        $sql = mysql_query($query) or die("Erro os selecionar lista de pontos importados");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[]  = array("id_projeto" => $rows['id_projeto'],
                                  "id" => $rows['id_header'],
                                  "nome" => $rows['nome_projeto'], 
                                  "cnpj"  => $rows['cnpj_projeto'],
                                  "periodo"  => $rows['periodo'],
                                  "competencia" => $rows['competencia'],
                                  "status" => $rows['status'],
                                  "total_registros"  => $rows['total_de_registro']);
            }
        }
        
        return $dados;
        
    }
    /**
     * 
     * @param type $id_ponto
     * @return type
     */
    public function listaDadosPontoById($id_ponto){
        
        $dados = array();
        $query = "SELECT C.nome, A.*
                    FROM importacao_campos_ponto AS A
                    LEFT JOIN importacao_header_ponto AS B ON(A.id_header = B.id_header)
                    INNER JOIN rh_clt AS C ON(A.pis = C.pis) -- AND C.id_projeto = B.id_projeto --
                    WHERE A.id_header = '{$id_ponto}' AND A.pis != 0;";
                    
        $sql = mysql_query($query) or die("Erro os selecionar dados do pontos");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                
                $nome = (!empty($rows['nome'])) ? $rows['nome'] : "Sem nome";
                $dados[]  = array("pis" => $rows['pis'],
                                  "nome" => utf8_encode($nome),  
                                  "horas_extras" => $rows['horas_extras'], 
                                  "horas_atrasos"  => $rows['atrasos'],
                                  "horas_trabalhadas" => $rows['horas_trabalhadas'],
                                  "salario_normal"  => $rows['salario_normal']);
            }
        }
        
        return $dados;
        
    }
    
    
    /**
     * 
     * @param type $pis
     * @return type
     */
    public function listaDetalhesDeFuncionarioByPis($pis){
        $dados = array();
        $query = "SELECT *, CAST(valor_hora * drs_horas_minutos AS decimal(13,2)) AS valor_desconto_drs, 
                            CAST(valor_hora * atrasos_horas_minutos AS decimal(13,2)) AS valor_desconto_atraso, 	
                            CAST(CAST(valor_hora + (valor_hora * 50) / 100 AS decimal(13,2))  * extra_horas_minutos AS decimal(13,2)) AS valor_hora_extra,
                            CAST(CAST((valor_hora * percentual_adic_noturno) / 100 AS decimal(13,2)) * adicional_horas_minutos AS decimal(13,2)) AS valor_adicional
                          FROM (
                            SELECT C.id_clt, C.id_regiao, C.id_projeto, substr(B.competencia,1,1) AS mes, substr(B.competencia,2,4) AS ano, B.id_header AS id_cabecalho, A.*, 
                                    CAST(DATE_FORMAT(dsrs,'%H') + (DATE_FORMAT(dsrs,'%i') / 60) AS decimal(13,2)) AS drs_horas_minutos,
                                    CAST(DATE_FORMAT(atrasos,'%H') + (DATE_FORMAT(atrasos,'%i') / 60) AS decimal(13,2)) AS atrasos_horas_minutos,
                                    CAST(DATE_FORMAT(horas_extras,'%H') + (DATE_FORMAT(horas_extras,'%i') / 60) AS decimal(13,2)) AS extra_horas_minutos,
                                    CAST(DATE_FORMAT(adicional_nortuno,'%H') + (DATE_FORMAT(adicional_nortuno,'%i') / 60) AS decimal(13,2)) AS adicional_horas_minutos,
                                    CAST((salario_normal/DATE_FORMAT(horas_a_trabalhar, '%H')) AS decimal(13,2)) AS valor_hora,
                                    CAST((salario_normal/30) * faltas_em_dias AS decimal(13,2)) AS desconto_faltas
                                    FROM importacao_campos_ponto AS A
                                    LEFT JOIN importacao_header_ponto AS B ON(A.id_header = B.id_header)
                                    INNER JOIN rh_clt AS C ON(A.pis = C.pis AND C.id_projeto = B.id_projeto)
			WHERE A.pis = '{$pis}' AND C.id_clt IS NOT NULL
	) AS tmp";
        $sql = mysql_query($query) or die("Erro os selecionar detalhes do funcionario");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[]  = array("pis" => $rows['pis'],
                                  "horas_a_trabalhar" => $rows['horas_a_trabalhar'],
                                  "horas_trabalhadas" => $rows['horas_trabalhadas'],
                                  "horas_extras" => $rows['horas_extras'], 
                                  "dsrs" => $rows['dsrs'],
                                  "horas_atrasos"  => $rows['atrasos'],
                                  "horas_justificadas" => $rows['horas_justificadas'],
                                  "banco_de_horas" => $rows['banco_de_horas'],
                                  "banco_de_perido" => $rows['banco_de_periodo'],
                                  "faltas_em_dias" => $rows['faltas_em_dias'],
                                  "salario_normal"  => $rows['salario_normal'],
                                  "adicional_noturno" => $rows['adicional_nortuno'],
                                  "percentual_adic_noturno" => $rows['percentual_adic_noturno'],
                                  "valor_hora" => $rows['valor_hora'],
                                  "valor_faltas" => $rows['desconto_faltas'],
                                  "valor_drs" => $rows['valor_desconto_drs'],
                                  "valor_atraso" => $rows['valor_desconto_atraso'],
                                  "valor_hora_extra" => $rows['valor_hora_extra'],
                                  "valor_adicional" => $rows['valor_adicional'],
                                  "id_clt" => $rows['id_clt'],
                                  "id_regiao" => $rows['id_regiao'],
                                  "id_projeto" => $rows['id_projeto'],
                                  "id_header" => $rows['id_cabecalho'],
                                  "mes" => $rows['mes'],
                                  "ano" => $rows['ano']
                                );  
                                          
            }
        }
        
        return $dados;
    }
    
    public function lancarMovimentosDoPonto($pis){
        $data = date("Y-m-d");
        $header = "";
        $query = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,id_folha,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,qnt,tipo_qnt,dt,sistema_ponto,id_header_ponto,status,status_folha,status_ferias,status_reg) VALUES ";
        $dados = $this->listaDetalhesDeFuncionarioByPis($pis);
        $valores = array();
        foreach ($dados as $linha){
            $header = $linha['id_header'];
            $valores = array("232" => $linha['valor_faltas'], "66" => $linha['valor_adicional'], "152" => $linha['valor_hora_extra'], "236" => $linha['valor_atraso']);
            foreach ($this->movimentos as $key => $mov){
                $qnt = ($mov['id_mov'] == 232) ? $linha['faltas_em_dias'] : null;
                $tipo_qnt = ($mov['id_mov'] == 232) ? "2" : null;
                if(!empty($valores[$mov['id_mov']]) && $valores[$mov['id_mov']] != 0.00){
                    $query .= "('{$linha['id_clt']}','{$linha['id_regiao']}','{$linha['id_projeto']}','0','{$linha['mes']}','{$linha['ano']}','{$mov['id_mov']}','{$mov['cod']}','{$mov['tipo']}','{$mov['nome']}','{$data}','{$_COOKIE['logado']}','{$valores[$mov['id_mov']]}','','1','5020,5021,5023','{$qnt}','{$tipo_qnt}','0','1','{$linha['id_header']}','1','0','1','1'),";
                }
            }
        }
        $query = substr($query, 0, -1);   
        $sql = mysql_query($query) or die("Erro ao cadastrar movimento");
        //$sql = true;
        if($sql){
            return array("status" => true, "header" => $header);
        }else{
            return array("status" => false);
        }
        
    }
    
    public function listaMovimentoPonto(){
        $dados = array();
        $query = "SELECT A.*,DATE_FORMAT(data_inicio_arquivo, '%d/%m/%Y') AS data_inicio, DATE_FORMAT(data_fim_arquivo, '%d/%m/%Y') AS data_fim, substr(A.competencia,1,1) AS mes, substr(A.competencia,2,4) AS ano
                    FROM importacao_header_ponto AS A
                    LEFT JOIN rh_movimentos_clt AS B ON(A.id_header = B.id_header_ponto)
                    LEFT JOIN projeto AS C ON(B.id_projeto = C.id_projeto)
                    WHERE A.status = 3 GROUP BY A.id_header";
        $sql = mysql_query($query) or die("Erro ao selecionar projetos finalizados");
        while($linha = mysql_fetch_assoc($sql)){
            $dados[] = array("id_header" => $linha['id_header'],
                            "id_projeto" => $linha['id_projeto'],
                            "nome_projeto" => $linha['nome_projeto'],
                            "mes" => $linha['mes'],
                            "ano" => $linha['ano'],
                            "cnpj" => $linha['cnpj_projeto'],
                            "ses" => $linha['codigo_ses'],
                            "total_participante" => $linha['total_de_registro']);
        }
        
        return $dados;
    }
    
    public function listaDetalhesMovimentoPonto($id_header){
        
        $dados = array();
        $query = "SELECT A.*, B.nome AS nome_projeto, DATE_FORMAT(A.data_movimento,'%d/%m/%Y') AS data_lancamento, C.nome AS nome_clt
                FROM rh_movimentos_clt AS A
                LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                LEFT JOIN rh_clt AS C ON(A.id_clt= C.id_clt)
                WHERE sistema_ponto = '1' AND A.id_header_ponto = '{$id_header}'";
                
        $sql = mysql_query($query) or die("Erro ao selecionar movimentos");
        while($linha = mysql_fetch_assoc($sql)){
            $dados[] = array(
                            "id_mov" => $linha['id_movimento'],
                            "id_clt" => $linha['id_clt'],
                            "nome_clt" => $linha['nome_clt'],
                            "nome_projeto" => $linha['nome_projeto'],
                            "mes" => $linha['mes_mov'],
                            "ano" => $linha['ano_mov'],
                            "tipo_mov" => $linha['tipo_movimento'],
                            "nome_mov" => $linha['nome_movimento'],
                            "data_lanc" => $linha['data_lancamento'],
                            "valor_mov" => $linha['valor_movimento']);
        }
        
        
        return $dados;
    }
    
    /**
     * 
     * @param type $header
     */
    public function removePonto($header){
        $retorno = array("status" => 0);
        /******************REMOVE OS CAMPOS COM O ID PASSADO*******************/
        $query_corpo = "DELETE FROM importacao_header_ponto WHERE id_header = '{$header}'";
            $remove_campo = mysql_query($query_corpo) or die("Erro ao remover corpo do ponto");
            if ($remove_campo) {
              $retorno = array("status" => 1);
            }
        
        return $retorno;
    }
    
    /**
     * 
     * @param type $header
     */
    public function removePontoFinalizado($header){
        $retorno = array("status" => 0);
        /***REMOVE OS MOVIMENTOS DO PONTO COM O HEADER PASSADO POR PARAMETRO***/
        $query_movimento = "DELETE FROM rh_movimentos_clt WHERE id_header_ponto = '{$header}'";
        $remove_movimentos = mysql_query($query_movimento) or die("erro ao remover movimento do ponto");

        if ($remove_movimentos) {
            /*             * *****************REMOVE OS ITENS DO PONTO COM O HEADER PASSADO POR PARAMETRO***************** */
            $query_corpo = "DELETE FROM importacao_campos_ponto WHERE id_header = '{$header}'";
            $remove_campo = mysql_query($query_corpo) or die("Erro ao remover corpo do ponto");

            if ($remove_campo) {
                /*                 * *************REMOVE O HEADER COM O ID PASSADO******************************************** */
                $query_header = "DELETE FROM importacao_header_ponto WHERE id_header = '{$header}'";
                $remove_header = mysql_query($query_header) or die('Erro ao remover cabeçalho do ponto');

                if ($remove_header) {
                    $retorno = array("status" => 1);
                }
            }
        }
        
        return $retorno;
    }
    
    public function updateHeaderPontoParaFinalizado($header){
        $retorno = false;
        $query = "UPDATE importacao_header_ponto SET status = '3' WHERE id_header = '{$header}'";
        $sql = mysql_query($query) or die("Erro ao atualizar status do cabeçalho");
        if($sql){
            $retorno = true;
        }
        
        return $retorno;
    }

    /**
     * 
     * @param type $valor
     * @return string
     */
    public function formata_hora_especial($valor) {
        //HORA
        $hora = substr($valor, 0, -4);
        //MINUTO
        $minuto = substr($valor, -4, -2);
        //SEGUNDO
        $segundo = substr($valor, -2);

        $valor_tratado = $hora . ":" . $minuto . ":" . $segundo;

        return $valor_tratado;
    }

    /**
     * 
     * @param type $tmp
     * @param type $caminho
     * @return boolean
     */
    public function uploadFile($tmp, $caminho) {
        $return = false;
        if (move_uploaded_file($tmp, $caminho))
            $return = true;

        return $return;
    }

}
