<?php
require_once('ContabilLoteClass.php');
require_once('ContabilContasSaldoClass.php');
//include('LogClass.php');
class c_planodecontasClass {

    public $conta;
    public $descricao;
    public $debug = FALSE; // true para exibir querys

    public function retorna($conta, $descricao, $projeto = null) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxProjeto = (!empty($projeto)) ? " AND id_projeto IN(0,'{$projeto}') " : $projeto;
        $qry = "SELECT *, classificador, descricao, tipo FROM contabil_planodecontas WHERE REPLACE(classificador, '.', '') LIKE '{$conta}%' AND status = 1 $auxProjeto ORDER BY classificador";
    
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function retornarConta($conta, $descricao, $projeto = null) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxProjeto = (!empty($projeto)) ? " AND id_projeto IN(0,'{$projeto}') " : $projeto;
        $qry = "SELECT *, classificador, descricao, tipo FROM contabil_planodecontas WHERE REPLACE(classificador, '.', '') LIKE '{$conta}%' AND status = 1 $auxProjeto ORDER BY classificador";
    
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
     
    public function retornaConta($projeto = null) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxProjeto = (!empty($projeto)) ? " AND id_projeto IN('{$projeto}') " : $projeto;
        $qry = "SELECT * FROM contabil_planodecontas WHERE id_projeto = '{$projeto}' AND cta_encerramento = 1";
   
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function retorna_conta_pai($conta, $projeto = null) {
//        print_array($conta . ' - ' . $projeto);    
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $qry = "SELECT id_conta, classificador FROM contabil_planodecontas WHERE classificador LIKE '{$conta}' AND status = 1 ORDER BY classificador";
       
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function retornaplano($conta, $descricao, $projeto = null) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxProjeto = (!empty($projeto)) ? " AND id_projeto = $projeto " : $projeto;
        $qry = "SELECT * FROM contabil_planodecontas WHERE nivel IN (1,2,3) and sped = 1 OR id_projeto = '{$projeto}' ORDER BY classificador";
        
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function Projetos(){
        $sql = mysql_query("SELECT * FROM projeto WHERE id_regiao IN(45,44,48,49,69) AND status_reg = '1'");
        $projetos = array("-1" => "« Selecione »");
            while ($rst = mysql_fetch_assoc($sql)) {
                $projetos[$rst['id_projeto']] = $rst['id_projeto'] . " - " . $rst['nome'];
        }
        return $projetos;
    }
    
    public function contasSaldo($id_projeto, $arraySaldoContas) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxContas = (!empty($arraySaldoContas)) ? " AND id_conta IN (".implode(', ', $arraySaldoContas).")" : null;
        $qry = "SELECT *, A.saldo AS saldo,  B.nome AS nomeprojeto, B.id_projeto AS projeto, A.descricao AS descricao 
                FROM contabil_planodecontas A 
                INNER JOIN projeto B ON(B.id_projeto = A.id_projeto) 
                WHERE A.id_projeto = $id_projeto AND A.classificador < 3 AND A.status = 1 
                ORDER BY A.classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function contasImplantarSaldo($id_projeto, $arrayContas) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxContas = (!empty($arrayContas)) ? " AND id_conta IN (".implode(', ', $arrayContas).")" : null;
        $qry = "SELECT * FROM contabil_planodecontas WHERE id_projeto = $id_projeto  AND status = 1 $auxContas ORDER BY classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function contasProjeto($id_projeto, $arrayContas) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxContas = (!empty($arrayContas)) ? " AND id_conta IN (".implode(', ', $arrayContas).")" : null;
        $qry = "SELECT * FROM contabil_planodecontas WHERE id_projeto = $id_projeto  AND status = 1 $auxContas ORDER BY classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function ImplantarContasSPED($id_projeto, $arrayContas) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxContas = (!empty($arrayContas)) ? " AND id_conta IN (".implode(', ', $arrayContas).")" : null;
        $qry = "SELECT * FROM contabil_planodecontas WHERE id_projeto = $id_projeto  AND status = 1 $auxContas ORDER BY classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function retornoContaSaldo($conta, $projeto) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $qry = "SELECT *, A.saldo AS saldo, B.nome AS nomeprojeto, B.id_projeto AS projeto, A.descricao AS descricao
                FROM contabil_planodecontas A
                INNER JOIN projeto B ON(B.id_projeto = A.id_projeto)
                WHERE A.id_projeto = $projeto AND id_conta = $conta AND A.classificador < 3 AND status = 1 
                ORDER BY A.classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
        
    }
        
    public function retorno($conta, $projeto) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
       $qry = "SELECT *, D3.nome AS nomeprojeto, D3.id_projeto AS projeto, A.descricao AS descricao,
            (SELECT SUM(IF(A2.tipo = 2, A2.valor, -A2.valor))
            FROM contabil_contas_saldo_dia A2
            INNER JOIN contabil_lancamento_itens B2 ON (A2.id_lancamento_itens = B2.id_lancamento_itens AND B2.status = 1)
            INNER JOIN contabil_lancamento C2 ON (B2.id_lancamento = C2.id_lancamento AND C2.status = 1 AND C2.contabil = 1)
            INNER JOIN contabil_lote D2 ON (C2.id_lote = D2.id_lote AND D2.status = 1)
            WHERE A2.id_conta = A.id_conta AND A2.status = 1) saldo
            FROM contabil_planodecontas A
            INNER JOIN projeto D3 ON (D3.id_projeto = $projeto) 
            WHERE A.status = 1 AND A.id_conta = '{$conta}' AND A.id_projeto = $projeto ORDER BY A.classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function retornaLancamento($conta, $projeto) {        
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $qry = "SELECT MAX(id_lancamento) FROM contabil_lancamento WHERE id_projeto = $projeto"; 
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function InplantarNovaConta($codigo, $conta_pai, $classificador, $tipo, $descricao, $natureza, $projeto) {
        $descricao = utf8_decode($descricao);
        $verificar = $this->verificarConta($classificador, $projeto);
//        $sped = $this->verificar_sped($classificador);
        if ($verificar) {
            return json_encode(array('status' => FALSE, 'msg' => 'Conta Cadastrada!'));
        } else {
            if ($codigo == null) {
                $sql_ultimo = "SELECT MAX(id_conta) FROM contabil_planodecontas";
                $result = mysql_query($sql_ultimo) or die('Erro' . mysql_error());
                $dado = mysql_fetch_array($result);
                $codigo = $dado[0] + 1;
            }
            $nivel = count(explode('.', $classificador));
            $qry_novaCta = "INSERT INTO contabil_planodecontas (cod_reduzido, conta_superior, classificador, descricao, tipo, natureza, id_projeto, nivel, sped)
                        VALUES ('{$codigo}','{$conta_pai}','{$classificador}','{$descricao}','{$tipo}','{$natureza}','{$projeto}','{$nivel}','0')";

            $result = mysql_query($qry_novaCta) or die(mysql_error());
            
            $id_conta = mysql_insert_id();

            $data = new DateTime(date('Y').sprintf('%02d',date('m'))."-01");
            return ($result) ? json_encode(array('status' => TRUE, 'id_conta' => $id_conta, 'msg' => 'Conta salva!')) : json_encode(array('status' => FALSE, 'msg' => 'Erro ao salvar Conta!'));
        }
    }
    
    public function novaconta($codigo, $conta_pai, $classificador, $tipo, $descricao, $natureza, $projeto) {
        $log = new Log();
        $descricao = utf8_decode($descricao);
        $verificar = $this->verificarConta($classificador, $projeto);
        $sped = $this->verificar_sped($classificador);
        if ($verificar) {
            return json_encode(array('status' => FALSE, 'msg' => 'Conta Ja Existente...!'));
        } else {
            if ($codigo == null) { 
                $sql_ultimo = "SELECT MAX(id_conta) FROM contabil_planodecontas";
                $result = mysql_query($sql_ultimo) or die('Erro' . mysql_error());
                $dado = mysql_fetch_array($result);
                $codigo = $dado[0] + 1;
            }
            $nivel = count(explode('.', $classificador));
            $qry_novaCta = "INSERT INTO contabil_planodecontas (cod_reduzido, conta_superior, classificador, descricao, tipo, natureza, id_projeto, nivel, sped)
                        VALUES ('{$codigo}','{$conta_pai}','{$classificador}','{$descricao}','{$tipo}','{$natureza}','{$projeto}','{$nivel}','0')";

            $result = mysql_query($qry_novaCta) or die(mysql_error());
            
            $id_conta = mysql_insert_id();
            
            $log->gravaLog('Plano de Contas', "Nova Conta Cadastrada: ID{$id_conta}");
            
            $data = new DateTime(date('Y').sprintf('%02d',date('m'))."-01");
            //print_array($periodoAnterior);
//            $data->modify("-1 MONTH");
//            $objContaSaldo = new ContabilContasSaldoClass();
//            $objContaSaldo->setAno($data->format('Y'));
//            $objContaSaldo->setMes($data->format('m'));
//            $objContaSaldo->setIdConta($id_conta);
//            $objContaSaldo->setValor($valor);
//            $objContaSaldo->setStatus(1);
//            $objContaSaldo->insert();
            
            return ($result) ? json_encode(array('status' => TRUE, 'id_conta' => $id_conta, 'msg' => 'Conta salva!')) : json_encode(array('status' => FALSE, 'msg' => 'Erro ao salvar Conta!'));
        }
    }

// verificar se conta é SPED
    public function verificar_sped($conta) {

        $query = "SELECT count(id_conta) as sped FROM contabil_planodecontas WHERE classificador = '{$conta}' AND sped = 1";

        $teste = mysql_fetch_assoc(mysql_query($query));
        if ($teste['sped'] == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

// verificar de conta já foi cadastrada na EMPRESA.
    public function verificarConta($conta, $empresa) {

        $query = "SELECT count(id_conta) as c FROM contabil_planodecontas WHERE classificador = '{$conta}' AND id_projeto = '{$empresa}'";

        $teste = mysql_fetch_assoc(mysql_query($query));
        if ($teste['c'] == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function niveisContas($inProjetos) {
        $sql = "SELECT classificador, descricao, nivel FROM contabil_planodecontas WHERE sped = '1' AND id_projeto IN($inProjetos) ORDER BY classificador ASC";
        $result = mysql_query($sql) or die('Erro: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
//        print_array($return);
        return $return;
    }

    public function inserirLancamento($array) {
        
//        if(array_key_exists('id_saida', $array)){
//            $array['historico'] = mysql_result(mysql_query("SELECT B.nome FROM saida A LEFT JOIN entradaesaida B ON (A.tipo = B.id_entradasaida) WHERE A.id_saida = {$array['id_saida']} LIMIT 1"), 0);
//        } else {
//            $array['historico'] = mysql_result(mysql_query("SELECT B.nome FROM entrada A LEFT JOIN entradaesaida B ON (A.tipo = B.id_entradasaida) WHERE A.id_entrada = {$array['id_entrada']} LIMIT 1"), 0);
//        }
        
        $keys = implode(',', array_keys($array));
        $values = implode("', '", $array);

        $insert = "INSERT INTO contabil_lancamento ($keys, contabil) VALUES ('" . $values . "', '0');";
//        echo $insert."<br>";
        mysql_query($insert) or die('Erro' . mysql_error());
        return mysql_insert_id();
    }

    public function updateLancamentoContabil($id_lancamento) {
        
        //Rogerio disse para usar a data de pagamento (18/09/2015 09:17:37)
        //Ramon disse para usar a data de vencimento (18/09/2015 11:30:00)
        
        $sqlSaida = mysql_fetch_assoc(mysql_query("SELECT *, MONTH(data_vencimento) mes_vencimento, YEAR(data_vencimento) ano_vencimento FROM saida A INNER JOIN contabil_lancamento B ON (A.id_saida = B.id_saida AND B.id_lancamento = {$id_lancamento}) LIMIT 1;"));
        $sqlEntrada = mysql_fetch_assoc(mysql_query("SELECT *, MONTH(data_vencimento) mes_vencimento, YEAR(data_vencimento) ano_vencimento FROM entrada A INNER JOIN contabil_lancamento B ON (A.id_entrada = B.id_entrada AND B.id_lancamento = {$id_lancamento}) LIMIT 1;"));
        
        if(is_array($sqlSaida)){
            $array = $sqlSaida;
        } else if(is_array($sqlEntrada)){
            $array = $sqlEntrada;
        }
        $nomeProjeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = {$array['id_projeto']} LIMIT 1"), 0);
        
        $objLote = new ContabilLoteClass();
        $objLote->setIdProjeto($array['id_projeto']);
        $objLote->setMes($array['mes_vencimento']);
        $objLote->setAno($array['ano_vencimento']);
        $objLote->setStatus(1);
        $objLote->setUsuarioCriacao($_COOKIE['logado']);
        $objLote->setDataCriacao(date('Y-m-d H:i:s'));
        $objLote->setLoteNumero("$nomeProjeto ".sprintf("%02d",$array['mes_vencimento'])."/{$array['ano_vencimento']} - FINANCEIRO");
        $objLote->setTipo(6);
        $objLote->verificaLote();
        
        $update = "UPDATE contabil_lancamento SET contabil = 1, data_lancamento = '{$array['data_vencimento']}', id_lote = '{$objLote->getIdLote()}' WHERE id_lancamento = '$id_lancamento' LIMIT 1;";
        mysql_query($update) or die('Erro' . mysql_error());
    }
 
    public function inserirItensLancamento($array) {
        foreach ($array as $itens) {
            
            if($itens['id_banco']){
                $sql =  "SELECT *, id_conta AS conta FROM contabil_contas_assoc_banco WHERE id_banco = {$itens['id_banco']} AND status = 1 LIMIT 1" ;
            } elseif($itens['fornecedor']){
                $sql = "SELECT A.*, A.id_contabil AS conta FROM contabil_contas_assoc A INNER JOIN contabil_planodecontas B ON (A.id_contabil = B.id_conta AND B.id_projeto = {$itens['id_projeto']}) WHERE A.id_fornecedor = {$itens['fornecedor']} AND A.status = 1 LIMIT 1";
            } elseif($itens['tipo']){
                $sql = "SELECT A.*, A.id_contabil AS conta FROM contabil_contas_assoc A INNER JOIN contabil_planodecontas B ON (A.id_contabil = B.id_conta AND B.id_projeto = {$itens['id_projeto']}) WHERE A.id_conta = {$itens['id_conta']} AND A.status = 1 LIMIT 1";
            }
            
            $rowConta = mysql_fetch_assoc(mysql_query($sql));
            
            unset($itens['id_banco'],$itens['id_conta'],$itens['fornecedor'],$itens['id_projeto']);
            
            $keys = implode(',', array_keys($itens));
            $values = "('" . implode("' , '", $itens) . "', '{$rowConta['conta']}')";
            
            $insert = "INSERT INTO contabil_lancamento_itens ($keys, id_conta) VALUES $values;";
            $result = mysql_query($insert) or die('Erro' . mysql_error());
            
        }
        return ($result) ? $id_lancamento_itens : FALSE;
    }

    public function novoSaldo($projeto, $conta, $valor){

        $qry_novoSaldo = "UPDATE contabil_planodecontas SET saldo = '".str_replace(',','.',str_replace('.','',$valor))."' WHERE id_conta = '{$conta}' AND id_projeto = '{$projeto}' LIMIT 1";

        $result = mysql_query($qry_novoSaldo) or die(mysql_error());
        return $result;
//        return ($result) ? json_encode(array('status' => TRUE, 'id_saldo' => $id_saldo, 'msg' => 'Ajuste de Saldo OK...!')) : json_encode(array('status' => FALSE, 'msg' => 'Erro ao salvar Saldo!'));
    }
        
    public function updateImportacaoLote($id_lote) {

        $update = "UPDATE contabil_lote SET importacao = 1 WHERE id_lote = '{$id_lote}' LIMIT 1;";
        $result = mysql_query($update) or die('Erro' . mysql_error());
        return $result;
    }

//    $alterarcao_cota = $planodecontas->alteracao]);
    public function alteracao($conta, $classificador, $pai, $reduzido, $nivel, $descricao, $natureza, $tipo, $id_historico) {
        $log = new Log();
        $descricao = utf8_decode($descricao);
            $update = "UPDATE contabil_planodecontas
                SET classificador = '{$classificador}', conta_superior = '{$pai}', cod_reduzido = '{$reduzido}', nivel = '{$nivel}',
                natureza = '{$natureza}', tipo = '{$tipo}', descricao = '{$descricao}', id_historico = '$id_historico'
                WHERE id_conta = '{$conta}' LIMIT 1";
        $result = mysql_query($update) or die('Erro' . mysql_error());
        $log->gravaLog('Plano de Conta', "Conta Alterada: ID{$conta}");
        return $result;
    }

    public function cancelar($conta, $motivo) {
        $log = new Log();
        $update = "UPDATE contabil_planodecontas SET status = 0, observacao = '{$motivo}' WHERE id_conta = '{$conta}' LIMIT 1";
        $result = mysql_query($update) or die('Erro' . mysql_error());
        $log->gravaLog('Plano de Conta', "Conta Excluida: ID{$conta}");
        return $result;
    }

    public function getPlanoFull($projeto) {
        $sql = "SELECT * FROM contabil_planodecontas A WHERE ( nivel <= 3 AND sped = 1 AND (SELECT COUNT(*) FROM contabil_planodecontas WHERE classificador LIKE CONCAT(A.classificador,'%') AND id_projeto IN ($projeto) AND status = 1) > 0 OR id_projeto IN ($projeto) ) AND status = 1 ORDER BY classificador ASC";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['classificador']] = $row;
        }

        return $array;
    }
    public function getPlanoFull_iabas() {
         $sql = "SELECT * 
                FROM iabas_plano_de_contas A 
                WHERE status = 1 ORDER BY classificador ASC";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['classificador']] = $row;
        }

        return $array;
    }
    
        public function getPlanoAcesso($projeto) {
        $sql = "SELECT * FROM contabil_planodecontas A WHERE A.id_projeto IN(0,{$_REQUEST['projeto']}) AND A.`status` = 1 AND A.classificacao = 'A' ORDER BY classificador"; 
        $array[" "] = " ";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_conta']] = $row['classificador'].' - '.$row['descricao'];        
        }

        return $array;
    }

}
