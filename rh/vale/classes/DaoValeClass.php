<?php


abstract class DaoValeClass implements IDaoValeClass{
    
    public static $id_user;
    private $vale_nome;
    public static  $id_tipo;
    public static  $cat;
    public static  $cat_nome;
    
    function __construct(Array $arr) {
        
        if (isset($_COOKIE['logado'])) {
            self::$id_user = $_COOKIE['logado'];
            $this->setValeNome($arr['nome_tipo']);
            $this->setIdTipo($arr['id_va_tipos']);
            $this->setCatVale($arr['id_va_categoria']);
            $this->setCatNomeVale($arr['nome_categoria']);
        } else {
            echo 'Erro! Você precisa estar logado para essa operação!';
            exit();
        }
    }
    
    public function setCatVale($cat){
        self::$cat = $cat;
    }
    
    public function getCatVale(){
        return self::$cat;
    }
    public function setCatNomeVale($cat_nome){
        self::$cat_nome = $cat_nome;
    }
    
    public function getCatNomeVale(){
        return self::$cat_nome;
    }
    public function setIdTipo($id){
        self::$id_tipo = $id;
    }
    
    public function getIdTipo(){
        return self::$id_tipo;
    }
    
    public function setValeNome($vale_nome){
        $this->vale_nome = $vale_nome;
    }
    
    public function getValeNome(){
        return $this->vale_nome;
    }
    
    public function getItensMenu(){
        return array('Lista de Pedidos', 'Gerar Pedido', 'Gerenciar Funcionários', 'Valores Diários'); //,'Exportar Funcionários'
    }
    
    public function salvaDiasUteis($dados) {
        
        $sql_update = $sql = "UPDATE rh_va_dias_uteis SET `status`=0, `editado_por`='".self::$id_user."' WHERE mes='$dados[mes]' AND ano='$dados[ano]' AND id_clt='$dados[id_clt]'  AND tipo_vale={$this->getIdTipo()} AND categoria_vale={$this->getCatVale()};";
        
//        exit($sql_update);
        mysql_query($sql_update);
        
        $sql = "INSERT INTO rh_va_dias_uteis(`categoria_vale`,`tipo_vale`, `id_clt`, `dias_uteis`, `mes`, `ano`, `user`, `criado_em`, `status`) "
                        . "VALUES('{$this->getCatVale()}','{$this->getIdTipo()}', '$dados[id_clt]','$dados[dias_uteis]','$dados[mes]','$dados[ano]','".self::$id_user."', NOW(), 1)";
        $result = mysql_query($sql);
        
        return (mysql_insert_id()<=0) ? FALSE : TRUE;
    }
    
    public function getRegioesFuncionario($usuario) {
        $sql = 'SELECT * FROM regioes WHERE id_master = ' . $usuario['id_master'] . ' AND status=1 AND status_reg=1';
        $query = mysql_query($sql);
        $regiao = array('-1'=>'&nbsp;&nbsp;&nbsp; SELECIONE');
        while ($row = mysql_fetch_array($query)) {
            $regiao[$row['id_regiao']] = $row['id_regiao'] . ' - ' . $row['regiao'];
        }
        return $regiao;
    }
    
    public function getProjetos($id_regiao=FALSE, $id_projeto = FALSE, $encode = FALSE) { // item 1, item 2, item 3, item 4, item 5
        
        $and = ($id_regiao) ? ' A.id_regiao="'.$id_regiao.'" ' : '';        
        
        $and .= (!empty($and) && ($id_projeto)) ? ' AND ' : '';
        
        $and .= ($id_projeto) ? ' A.id_projeto="'.$id_projeto.'" ' : ' ';        
        
        $sql = "SELECT  A.id_projeto, A.nome AS nome_projeto, B.id_empresa, B.nome AS nome_empresa,  B.cnpj AS cnpj_empresa, A.cnpj  
                FROM projeto AS A 
                INNER JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto) 
                WHERE  $and";
        
        $qr = mysql_query($sql);
        $projetos = array();
        while ($row = mysql_fetch_array($qr)) {
            $nome = ($encode) ? ($row['id_projeto'].' - '.utf8_encode($row['nome_projeto']) . ' ' . $row['cnpj_empresa']) : $row['id_projeto'].' - '.$row['nome_projeto'] . ' ' . $row['cnpj_empresa'];
            $projetos[$row['id_projeto']] = $nome;
        }
        return $projetos;
    }
    
    public function getValoresDiarios($regiao, $json=FALSE) {
        $sql = "SELECT A.id_va_valor_diario, A.regiao AS nome_regiao, valor_diario,"
                . "(SELECT COUNT(*) FROM rh_va_clt_valor_diario WHERE status=1 AND id_valor_diario=A.id_va_valor_diario) AS vinculos FROM rh_va_valor_diario AS A LEFT JOIN regioes AS B ON (A.regiao=B.id_regiao) WHERE A.regiao='$regiao' AND A.tipo_vale='".$this->getIdTipo()."'  AND A.`status`=1 AND A.categoria_vale={$this->getCatVale()}";
//    echo $sql."<br>";
        $result = mysql_query($sql);
        
        $relacao_tarifas = ($json) ? array('0'=>'R$ 0,00') : array();
        
        while ($row = mysql_fetch_array($result)) {
            if($json){
                $relacao_tarifas[$row['id_va_valor_diario']] = 'R$ '.number_format($row['valor_diario'],2,',','.');
            }else{
                $relacao_tarifas[] = array('id_va_valor_diario' => $row['id_va_valor_diario'], 'nome_regiao' => $row['nome_regiao'], 'valor_diario' => number_format($row['valor_diario'],2,',','.'), 'vinculos' => $row['vinculos'] );
            }
        }
        return ($json) ? str_replace('"', '\'', json_encode($relacao_tarifas)) : $relacao_tarifas;
    }
    
    public function salvaValorDiario($dados) {
        $sql = "INSERT INTO rh_va_valor_diario(`regiao`,`valor_diario`,`categoria_vale`,`tipo_vale`,`criado_por`,`criado_em`,`status`) "
                        . "VALUES('$dados[regiao]','$dados[valor]','".$this->getCatVale()."','".$this->getIdTipo()."','".self::$id_user."', NOW(),1 )";
//        exit($sql);
        $result = mysql_query($sql);
        return (mysql_insert_id()<=0) ? FALSE : TRUE;
    }
    
    public function atualizaValorDiario($dados) {
        
        foreach($dados as $dado){
            $sql = "UPDATE rh_va_valor_diario SET `valor_diario`= '$dado[valor]', atualizado_por='".self::$id_user."' WHERE id_va_valor_diario='$dado[id]';";
            mysql_query($sql);
        }
        
        return TRUE;
    }
    
    public function excluiValorDiario($id) {        
        $sql = "UPDATE rh_va_valor_diario SET `status`= '0', atualizado_por='".self::$id_user."' WHERE id_va_valor_diario='$id' LIMIT 1;";
        return mysql_query($sql);
    }    
    
    public function salvaCltValorDiario($dados) {
        
        $sql = "INSERT INTO rh_va_clt_valor_diario(`categoria_vale`,`tipo_vale`,`id_clt`,`id_valor_diario`,`criado_por`,`criado_em`,`status`) VALUES";
        
        $arr_clt = array();
        foreach ($dados as $id_clt=>$id_valor) {
            $sql .= "('{$this->getCatVale()}','{$this->getIdTipo()}','$id_clt','$id_valor',".self::$id_user.", NOW(),1 ),";
            $arr_clt[$id_clt] = $id_clt;
        }
        
        $ids_clt = implode(',',$arr_clt);
        
        $sql_update = "UPDATE rh_va_clt_valor_diario SET `status`='0', `atualizado_por`='".self::$id_user."' WHERE id_clt IN($ids_clt) AND categoria_vale='{$this->getCatVale()}' AND tipo_vale='{$this->getIdTipo()}';";
        mysql_query($sql_update);
        
        $sql = substr($sql, 0, -1).';';
        
        mysql_query($sql);
        
        return (mysql_insert_id()>0) ? TRUE : FALSE;
    }
    public function salvaCltMatricula($dados) {
        
        $sql = "INSERT INTO rh_va_matricula(`categoria_vale`,`tipo_vale`,`id_clt`, `matricula`,`criado_por`,`criado_em`,`status`) VALUES";
        
        $arr_clt = array();
        foreach ($dados as $id_clt=>$matricula) {
            $sql .= "('{$this->getCatVale()}','{$this->getIdTipo()}','$id_clt','$matricula',".self::$id_user.", NOW(),1 ),";
            $arr_clt[$id_clt] = $id_clt;
        }
        
        $ids_clt = implode(',',$arr_clt);
        
        $sql_update = "UPDATE rh_va_matricula SET `status`='0', `atualizado_por`='".self::$id_user."' WHERE id_clt IN($ids_clt) AND categoria_vale='{$this->getCatVale()}' AND tipo_vale='{$this->getIdTipo()}';";
        
//        exit($sql_update);
        mysql_query($sql_update);
        
        $sql = substr($sql, 0, -1).';';
        
        mysql_query($sql);
        
        return (mysql_insert_id()>0) ? TRUE : FALSE;
    }

    public function getFuncionariosByProjeto(Array $dados) {
        
        $projeto = isset($dados['projeto']) ? $dados['projeto'] : '';
        $cpf = (isset($dados['cpf']) && !empty($dados['cpf'])) ? " AND A.cpf =  '" . $dados['cpf'] . "'" : '';
        $nome = (isset($dados['nome']) && !empty($dados['nome'])) ? " AND A.nome LIKE  '" . $dados['nome'] . "%'" : '';
        
        if($dados['data_entrada']=='true'){
            $mes = " AND MONTH(A.data_entrada) =  " . $dados['mes'] . " ";
            $ano = " AND YEAR(A.data_entrada) =  '" . $dados['ano'] . "'";
        }else{
            $mes = '';
            $ano = '';
        }
        
        $w_tp = '';
        if($this->getCatVale()==1){
            $w_tp = ' AND A.vale_refeicao ='.$dados['tipo_vale'].' ';
        }elseif($this->getCatVale()==2){
            $w_tp = ' AND A.vale_alimentacao ='.$dados['tipo_vale'].' ';
        }
        

        $sql = "SELECT A.id_clt, IF((F.matricula IS NOT NULL OR CHAR_LENGTH(F.matricula)>0),F.matricula,A.id_clt ) AS matricula, A.nome AS nome_funcionario,A.cpf, DATE_FORMAT(data_entrada,'%d/%m/%Y') AS data_entrada_f, IF(A.vale_alimentacao=1,'SIM','NÃO') AS solicitou_vale_alimentacao, E.valor_diario,
             IF(E.id_va_valor_diario IS NULL,'0',E.id_va_valor_diario) AS id_va_valor_diario  FROM rh_clt AS A
	LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
        LEFT JOIN rh_va_clt_valor_diario AS C ON(A.id_clt=C.id_clt AND C.`status`=1 AND categoria_vale={$this->getCatVale()} AND tipo_vale={$this->getIdTipo()})
        LEFT JOIN rh_va_valor_diario AS E ON(E.id_va_valor_diario = C.id_valor_diario AND E.`status`=1)
        LEFT JOIN rh_va_matricula AS F ON(A.id_clt=F.id_clt AND F.`status`=1)
	WHERE A.id_projeto = '$projeto'  $w_tp $cpf $nome $mes $ano  AND (A.status = '10' OR A.status = '40') AND (A.status_demi=0 OR A.status_demi IS NULL) GROUP BY A.id_clt;";
//        exit($sql);
//        echo $sql.'<br>';
//        echo "<!-- ".$sql." -->";
        
        $result = mysql_query($sql);
        $relacao_funcionarios = array();
        while ($resp = mysql_fetch_array($result)) {
            $relacao_funcionarios[] = $resp;
        }
        
        
        return $relacao_funcionarios;
    }

    public function getPedidos($projeto=FALSE, $status = 1) {
        $status = ($status) ? " AND A.`status`='$status' " : '';
        $projeto = ($projeto) ? " AND A.`projeto`='$projeto' " : '';
//    id_va_pedido mes ano projeto user
    $sql = "SELECT A.id_va_pedido, A.mes, A.ano, B.nome AS projeto, C.nome AS nome_usuario, C.id_funcionario, (SELECT SUM( va_valor_diario * dias_uteis) AS valor FROM `rh_va_relatorio` WHERE id_va_pedido=A.id_va_pedido) AS valor_pedido FROM rh_va_pedido AS A LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto) LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario)  WHERE  A.tipo_vale=".$this->getIdTipo()." AND A.categoria_vale=".$this->getCatVale()." $projeto $status ORDER BY A.id_va_pedido DESC;";
//        echo "<!--" . $sql . "-->";
        $resp = mysql_query($sql);
        $pedidos = array();
        while ($row = mysql_fetch_array($resp)) {
            $pedidos[] = $row;
        }
        return $pedidos;
    }

    public function geraRelacaoCltPedido(Array $dados, $gravar=FALSE) {

        $w_tp = '';
        if($this->getCatVale()==1){
            $w_tp = ' AND A.vale_refeicao ='.$this->getIdTipo().' ';
        }elseif($this->getCatVale()==2){
            $w_tp = ' AND A.vale_alimentacao ='.$this->getIdTipo().' ';
        }
        
        $sql = "SELECT A.id_clt, A.nome AS nome_funcionario, B.nome AS nome_projeto, D.valor_diario FROM rh_clt AS A LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto) 
            LEFT JOIN rh_va_clt_valor_diario AS C ON( (A.`id_clt`=C.id_clt) AND C.`status`=1 AND C.categoria_vale={$this->getCatVale()} AND C.tipo_vale={$this->getIdTipo()} )
            LEFT JOIN rh_va_valor_diario AS D ON( (C.id_valor_diario=D.id_va_valor_diario) AND D.status=1 )
            WHERE A.id_projeto=$dados[projeto] $w_tp  AND (A.status = '10' OR A.status= '40') AND (A.status_demi=0 OR A.status_demi IS NULL) GROUP BY A.id_clt";
//        echo $sql."\n";
        
        
        $result = mysql_query($sql);
        $relacao_funcionarios = array();

        $arr_clt = array();
        
        while ($resp = mysql_fetch_array($result)) { 
            $arr_clt[$resp['id_clt']] = $resp;
        }
        
        $ids_clt = implode(',',array_keys($arr_clt));
        
        $sql_d = "SELECT id_clt, dias_uteis FROM rh_va_dias_uteis WHERE id_clt IN($ids_clt) AND ((mes='$dados[mes]' AND ano='$dados[ano]') OR (sempre=1)) AND status=1 AND tipo_vale={$this->getIdTipo()} AND categoria_vale={$this->getCatVale()};";
        $result_d = mysql_query($sql_d);
        $arr_dias_uteis = array();
        while($resp_d = mysql_fetch_array($result_d)){
            $arr_dias_uteis[$resp_d['id_clt']] = $resp_d['dias_uteis'];
        }
        
        foreach($arr_clt as $resp){
            $relacao_funcionarios[$resp['id_clt']]['id_clt'] = $resp['id_clt'];
            $relacao_funcionarios[$resp['id_clt']]['nome_funcionario'] = $resp['nome_funcionario'];
            
            $dias_uteis = (isset($arr_dias_uteis[$resp['id_clt']]) && !empty($arr_dias_uteis[$resp['id_clt']])) ? $arr_dias_uteis[$resp['id_clt']] : $dados['dias_uteis'];
            
            $relacao_funcionarios[$resp['id_clt']]['dias_uteis'] = $dias_uteis;
            $relacao_funcionarios[$resp['id_clt']]['valor_diario'] = $resp['valor_diario'];
            $valor_clt = ($dias_uteis* $resp['valor_diario']);
            $relacao_funcionarios[$resp['id_clt']]['valor_recarga'] = $valor_clt;
        }
        if($gravar){
            return $this->gravaPedido($dados, $relacao_funcionarios);
        }else{
            return $relacao_funcionarios;
        }
    }
    
    private function gravaPedido($form_data, Array $relacao){  
        $sql = "INSERT INTO rh_va_pedido(`mes`,`ano`,`projeto`,`data_inicial`,`data_final`, `user`,`data`, `categoria_vale`, `tipo_vale`, `status`) VALUES('$form_data[mes]','$form_data[ano]','$form_data[projeto]','$form_data[dataini]','$form_data[datafim]','".self::$id_user."',NOW(),'".$this->getCatVale()."','".$this->getIdTipo()."', '0');";
//        exit($sql);
        $res_pedido = mysql_query($sql);
        $id_pedido = mysql_insert_id();        
        if($id_pedido>0){        
            $sql = 'INSERT INTO rh_va_relatorio(`id_va_pedido`,`id_clt`, `dias_uteis`,`va_valor_diario`) VALUES';
            foreach ($relacao as $resp){
                if($resp['valor_recarga']>0){
                    $sql .= "('$id_pedido', '$resp[id_clt]', '$resp[dias_uteis]', '$resp[valor_diario]'),";
                }
            }
            $sql = substr($sql, 0, -1).';';
            mysql_query($sql);
            if (mysql_insert_id()>0) {
                mysql_query("UPDATE rh_va_pedido SET `status`='1' WHERE id_va_pedido=$id_pedido LIMIT 1;");
                $result = mysql_query("SELECT * FROM rh_va_pedido WHERE `status`='1' AND id_va_pedido=$id_pedido;");
                if(mysql_numrows($result)>0){
                    return $id_pedido;
                }
            }
        }else{
            return FALSE;
        }
    }
    
    public function verRelacaoCltPedido($id_pedido) {
        $sql = "SELECT G.nome AS nome_empresa, G.razao AS razao_social, G.cnpj, G.cnpj_matriz, 
                D.id_clt, IF((I.matricula IS NOT NULL OR CHAR_LENGTH(I.matricula)>0),I.matricula, D.id_clt ) AS matricula, A.id_va_pedido, A.mes, A.ano, B.id_projeto, B.nome AS projeto, C.nome AS usuario, 
                E.nome AS nome_funcionario, REPLACE(
                REPLACE(E.cpf,'.',''),'-','') AS cpf_limpo, DATE_FORMAT(E.data_nasci,'%d/%m/%Y') AS data_nascimento,REPLACE(REPLACE(E.rg,'.',''),'-','') AS rg_limpo, E.uf_rg, 
                DATE_FORMAT(E.data_emissao,'%d/%m/%Y') AS emissao_rg, E.orgao AS orgao_rg, E.mae, E.sexo, E.civil, E.email, E.tel_fixo,
                F.nome AS cargo,  H.descricao_tp_logradouro AS tp_logradouro_dp, G.logradouro AS logradouro_dp, G.numero AS numero_dp,  G.complemento AS complemento_dp,
                G.bairro AS bairro_dp, G.cidade AS cidade_dp, G.uf AS uf_dp, G.cep AS cep_dp,

                D.dias_uteis, D.va_valor_diario AS valor_diario, (D.dias_uteis*D.va_valor_diario) AS valor_recarga, F.salario, (F.salario*0.20) AS salario_porcentagem, IF((F.salario*0.20)>=(D.dias_uteis*D.va_valor_diario),(D.dias_uteis*D.va_valor_diario),(F.salario*0.20)) AS desconto_movimento
                 FROM rh_va_pedido AS A "
                . "LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto) "
                . "LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario) "
                . "LEFT JOIN rh_va_relatorio AS D ON(A.id_va_pedido=D.id_va_pedido) "
                . "LEFT JOIN rh_clt AS E ON(E.id_clt=D.id_clt) "
                . "LEFT JOIN curso AS F ON(E.id_curso=F.id_curso) "
                . "LEFT JOIN rhempresa AS G ON(B.id_projeto=G.id_projeto) "
                . "LEFT JOIN tipos_de_logradouro AS H ON(G.id_tp_logradouro=H.id_tp_logradouro) "
                . "LEFT JOIN rh_va_matricula AS I ON(E.id_clt=I.id_clt AND I.`status`=1) "
                . "WHERE A.`status`='1' AND A.id_va_pedido='$id_pedido' ";
//        exit($sql);
        $resp = mysql_query($sql);
        $relacao_pedido = array();
        while ($row = mysql_fetch_array($resp)) {
            $relacao_pedido[$row['id_clt']] = $row;
            $relacao_pedido[$row['id_clt']]['valor_diario'] = $row['valor_diario'];
            $relacao_pedido[$row['id_clt']]['valor_recarga'] = $row['valor_recarga'];
        }
        return $relacao_pedido;
    }
    function deletarPedido($id_pedido){
        mysql_query("UPDATE rh_va_pedido SET `status`='0' WHERE id_va_pedido=$id_pedido LIMIT 1");
        $result = mysql_query("SELECT * FROM rh_va_pedido WHERE `status`='0' AND id_va_pedido=$id_pedido");
        return (mysql_num_rows($result)>0) ? TRUE : FALSE;
    }
    
}