<?php

class ProcessoJuridicoClass { 

    protected $proc_id;
    protected $id_projeto;
    protected $id_regiao;
    protected $id_clt;
    protected $id_autonomo;
    protected $adv_id;
    protected $preposto_id;
    protected $proc_tipo_id;
    protected $proc_tipo_contratacao;
    protected $proc_nome;
    protected $proc_nome1;
    protected $proc_nome2;
    protected $proc_cpf;
    protected $proc_rg;
    protected $proc_data_nasc;
    protected $proc_atividade;
    protected $proc_unidade;
    protected $proc_data_entrada;
    protected $proc_data_saida;
    protected $proc_numero_processo;
    protected $proc_vara_uf;
    protected $proc_valor_pedido;
    protected $proc_local;
    protected $proc_numero_vara;
    protected $uf_id;
    protected $pedido_acao;
    protected $adv_id_principal;
    protected $n_oficio;
    protected $proc_data_cad;
    protected $usuario_cad;
    protected $data_atualizacao;
    protected $usuario_atualizacao;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' processos_juridicos ';
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rs;
    protected $row;
    protected $numRows;

    function __construct() { 
        
    }

    //GET's DA CLASSE
    function getProcId() {
        return $this->proc_id;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getIdClt() {
        return $this->id_clt;
    }

    function getIdAutonomo() {
        return $this->id_autonomo;
    }

    function getAdvId() {
        return $this->adv_id;
    }

    function getPrepostoId() {
        return $this->preposto_id;
    }

    function getProcTipoId() {
        return $this->proc_tipo_id;
    }

    function getProcTipoContratacao() {
        return $this->proc_tipo_contratacao;
    }

    function getProcNome() {
        return $this->proc_nome;
    }

    function getProcNome1() {
        return $this->proc_nome1;
    }

    function getProcNome2() {
        return $this->proc_nome2;
    }

    function getProcCpf($limpo = false) {
        return ($limpo) ? str_replace(array('.','-','/'), '' ,$this->proc_cpf) : $this->proc_cpf;
    }

    function getProcRg() {
        return $this->proc_rg;
    }

    function getProcDataNasc($formato = null) {
        if (empty($this->proc_data_nasc) || $this->proc_data_nasc == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->proc_data_nasc), $formato) : $this->proc_data_nasc;
        }
    }

    function getProcAtividade() {
        return $this->proc_atividade;
    }

    function getProcUnidade() {
        return $this->proc_unidade;
    }

    function getProcDataEntrada($formato = null) {
        if (empty($this->proc_data_entrada) || $this->proc_data_entrada == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->proc_data_entrada), $formato) : $this->proc_data_entrada;
        }
    }

    function getProcDataSaida($formato = null) {
        if (empty($this->proc_data_saida) || $this->proc_data_saida == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->proc_data_saida), $formato) : $this->proc_data_saida;
        }
    }

    function getProcNumeroProcesso() {
        return $this->proc_numero_processo;
    }

    function getProcVaraUf() {
        return $this->proc_vara_uf;
    }

    function getProcValorPedido() {
        return $this->proc_valor_pedido;
    }

    function getProcLocal() {
        return $this->proc_local;
    }

    function getProcNumeroVara() {
        return $this->proc_numero_vara;
    }

    function getUfId() {
        return $this->uf_id;
    }

    function getPedidoAcao() {
        return $this->pedido_acao;
    }

    function getAdvIdPrincipal() {
        return $this->adv_id_principal;
    }

    function getNOficio() {
        return $this->n_oficio;
    }

    function getProcDataCad($formato = null) {
        if (empty($this->proc_data_cad) || $this->proc_data_cad == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->proc_data_cad), $formato) : $this->proc_data_cad;
        }
    }

    function getUsuarioCad() {
        return $this->usuario_cad;
    }

    function getDataAtualizacao($formato = null) {
        if (empty($this->data_atualizacao) || $this->data_atualizacao == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_atualizacao), $formato) : $this->data_atualizacao;
        }
    }

    function getUsuarioAtualizacao() {
        return $this->usuario_atualizacao;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setProcId($proc_id) {
        $this->proc_id = $proc_id;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setIdClt($id_clt) {
        $this->id_clt = $id_clt;
    }

    function setIdAutonomo($id_autonomo) {
        $this->id_autonomo = $id_autonomo;
    }

    function setAdvId($adv_id) {
        $this->adv_id = $adv_id;
    }

    function setPrepostoId($preposto_id) {
        $this->preposto_id = $preposto_id;
    }

    function setProcTipoId($proc_tipo_id) {
        $this->proc_tipo_id = $proc_tipo_id;
    }

    function setProcTipoContratacao($proc_tipo_contratacao) {
        $this->proc_tipo_contratacao = $proc_tipo_contratacao;
    }

    function setProcNome($proc_nome) {
        $this->proc_nome = $proc_nome;
    }

    function setProcNome1($proc_nome1) {
        $this->proc_nome1 = $proc_nome1;
    }

    function setProcNome2($proc_nome2) {
        $this->proc_nome2 = $proc_nome2;
    }

    function setProcCpf($proc_cpf) {
        $this->proc_cpf = $proc_cpf;
    }

    function setProcRg($proc_rg) {
        $this->proc_rg = $proc_rg;
    }

    function setProcDataNasc($proc_data_nasc) {
        $this->proc_data_nasc = $proc_data_nasc;
    }

    function setProcAtividade($proc_atividade) {
        $this->proc_atividade = $proc_atividade;
    }

    function setProcUnidade($proc_unidade) {
        $this->proc_unidade = $proc_unidade;
    }

    function setProcDataEntrada($proc_data_entrada) {
        $this->proc_data_entrada = $proc_data_entrada;
    }

    function setProcDataSaida($proc_data_saida) {
        $this->proc_data_saida = $proc_data_saida;
    }

    function setProcNumeroProcesso($proc_numero_processo) {
        $this->proc_numero_processo = $proc_numero_processo;
    }

    function setProcVaraUf($proc_vara_uf) {
        $this->proc_vara_uf = $proc_vara_uf;
    }

    function setProcValorPedido($proc_valor_pedido) {
        $this->proc_valor_pedido = $proc_valor_pedido;
    }

    function setProcLocal($proc_local) {
        $this->proc_local = $proc_local;
    }

    function setProcNumeroVara($proc_numero_vara) {
        $this->proc_numero_vara = $proc_numero_vara;
    }

    function setUfId($uf_id) {
        $this->uf_id = $uf_id;
    }

    function setPedidoAcao($pedido_acao) {
        $this->pedido_acao = $pedido_acao;
    }

    function setAdvIdPrincipal($adv_id_principal) {
        $this->adv_id_principal = $adv_id_principal;
    }

    function setNOficio($n_oficio) {
        $this->n_oficio = $n_oficio;
    }

    function setProcDataCad($proc_data_cad) {
        $this->proc_data_cad = $proc_data_cad;
    }

    function setUsuarioCad($usuario_cad) {
        $this->usuario_cad = $usuario_cad;
    }

    function setDataAtualizacao($data_atualizacao) {
        $this->data_atualizacao = $data_atualizacao;
    }

    function setUsuarioAtualizacao($usuario_atualizacao) {
        $this->usuario_atualizacao = $usuario_atualizacao;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    protected function setQUERY($QUERY) {
        $this->QUERY = $QUERY;
    }

    protected function setSELECT($SELECT) {
        $this->SELECT = $SELECT;
    }

    protected function setFROM($FROM) {
        $this->FROM = $FROM;
    }

    protected function setWHERE($WHERE) {
        $this->WHERE = $WHERE;
    }

    protected function setGROUP($GROUP) {
        $this->GROUP = $GROUP;
    }

    protected function setORDER($ORDER) {
        $this->ORDER = $ORDER;
    }

    protected function setLIMIT($LIMIT) {
        $this->LIMIT = $LIMIT;
    }

    protected function setHAVING($HAVING) {
        $this->HAVING = $HAVING;
    }

    //SET DEFAULT
    function setDefault() {
        $this->proc_id = null;
        $this->id_projeto = null;
        $this->id_regiao = null;
        $this->id_clt = null;
        $this->id_autonomo = null;
        $this->adv_id = null;
        $this->preposto_id = null;
        $this->proc_tipo_id = null;
        $this->proc_tipo_contratacao = null;
        $this->proc_nome = null;
        $this->proc_nome1 = null;
        $this->proc_nome2 = null;
        $this->proc_cpf = null;
        $this->proc_rg = null;
        $this->proc_data_nasc = null;
        $this->proc_atividade = null;
        $this->proc_unidade = null;
        $this->proc_data_entrada = null;
        $this->proc_data_saida = null;
        $this->proc_numero_processo = null;
        $this->proc_vara_uf = null;
        $this->proc_valor_pedido = null;
        $this->proc_local = null;
        $this->proc_numero_vara = null;
        $this->uf_id = null;
        $this->pedido_acao = null;
        $this->adv_id_principal = null;
        $this->n_oficio = null;
        $this->proc_data_cad = null;
        $this->usuario_cad = null;
        $this->data_atualizacao = null;
        $this->usuario_atualizacao = null;
        $this->status = null;
    }

    protected function setRs() {
        if (!empty($this->QUERY)) {
            $sql = $this->QUERY;
        } else {
            $auxWhere = (!empty($this->WHERE)) ? " WHERE $this->WHERE " : null;
            $auxGroup = (!empty($this->GROUP)) ? " GROUP BY $this->GROUP " : null;
            $auxHaving = (!empty($this->HAVING)) ? " HAVING $this->HAVING " : null;
            $auxOrder = (!empty($this->ORDER)) ? " ORDER BY $this->ORDER " : null;
            $auxLimit = (!empty($this->LIMIT)) ? " LIMIT $this->LIMIT " : null;

            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }

        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' processos_juridicos ');
        $this->setWHERE('');
        $this->setGROUP('');
        $this->setHAVING('');
        $this->setORDER('');
        $this->setLIMIT('');
    }

    public function getNumRows() {
        return $this->numRows;
    }

    protected function setRow($valor) {
        return $this->row = mysql_fetch_assoc($valor);
    }

    //RECUPERANDO INFO DO BANCO
    public function getRow() {

        if ($this->setRow($this->rs)) {
            $this->setProcId($this->row['proc_id']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdClt($this->row['id_clt']);
            $this->setIdAutonomo($this->row['id_autonomo']);
            $this->setAdvId($this->row['adv_id']);
            $this->setPrepostoId($this->row['preposto_id']);
            $this->setProcTipoId($this->row['proc_tipo_id']);
            $this->setProcTipoContratacao($this->row['proc_tipo_contratacao']);
            $this->setProcNome($this->row['proc_nome']);
            $this->setProcNome1($this->row['proc_nome1']);
            $this->setProcNome2($this->row['proc_nome2']);
            $this->setProcCpf($this->row['proc_cpf']);
            $this->setProcRg($this->row['proc_rg']);
            $this->setProcDataNasc($this->row['proc_data_nasc']);
            $this->setProcAtividade($this->row['proc_atividade']);
            $this->setProcUnidade($this->row['proc_unidade']);
            $this->setProcDataEntrada($this->row['proc_data_entrada']);
            $this->setProcDataSaida($this->row['proc_data_saida']);
            $this->setProcNumeroProcesso($this->row['proc_numero_processo']);
            $this->setProcVaraUf($this->row['proc_vara_uf']);
            $this->setProcValorPedido($this->row['proc_valor_pedido']);
            $this->setProcLocal($this->row['proc_local']);
            $this->setProcNumeroVara($this->row['proc_numero_vara']);
            $this->setUfId($this->row['uf_id']);
            $this->setPedidoAcao($this->row['pedido_acao']);
            $this->setAdvIdPrincipal($this->row['adv_id_principal']);
            $this->setNOficio($this->row['n_oficio']);
            $this->setProcDataCad($this->row['proc_data_cad']);
            $this->setUsuarioCad($this->row['usuario_cad']);
            $this->setDataAtualizacao($this->row['data_atualizacao']);
            $this->setUsuarioAtualizacao($this->row['usuario_atualizacao']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_projeto' => addslashes($this->getIdProjeto()),
            'id_regiao' => addslashes($this->getIdRegiao()),
            'id_clt' => addslashes($this->getIdClt()),
            'id_autonomo' => addslashes($this->getIdAutonomo()),
            'adv_id' => addslashes($this->getAdvId()),
            'preposto_id' => addslashes($this->getPrepostoId()),
            'proc_tipo_id' => addslashes($this->getProcTipoId()),
            'proc_tipo_contratacao' => addslashes($this->getProcTipoContratacao()),
            'proc_nome' => addslashes($this->getProcNome()),
            'proc_nome1' => addslashes($this->getProcNome1()),
            'proc_nome2' => addslashes($this->getProcNome2()),
            'proc_cpf' => addslashes($this->getProcCpf()),
            'proc_rg' => addslashes($this->getProcRg()),
            'proc_data_nasc' => addslashes($this->getProcDataNasc()),
            'proc_atividade' => addslashes($this->getProcAtividade()),
            'proc_unidade' => addslashes($this->getProcUnidade()),
            'proc_data_entrada' => addslashes($this->getProcDataEntrada()),
            'proc_data_saida' => addslashes($this->getProcDataSaida()),
            'proc_numero_processo' => addslashes($this->getProcNumeroProcesso()),
            'proc_vara_uf' => addslashes($this->getProcVaraUf()),
            'proc_valor_pedido' => addslashes($this->getProcValorPedido()),
            'proc_local' => addslashes($this->getProcLocal()),
            'proc_numero_vara' => addslashes($this->getProcNumeroVara()),
            'uf_id' => addslashes($this->getUfId()),
            'pedido_acao' => addslashes($this->getPedidoAcao()),
            'adv_id_principal' => addslashes($this->getAdvIdPrincipal()),
            'n_oficio' => addslashes($this->getNOficio()),
            'proc_data_cad' => addslashes($this->getProcDataCad()),
            'usuario_cad' => addslashes($this->getUsuarioCad()),
            'data_atualizacao' => addslashes($this->getDataAtualizacao()),
            'usuario_atualizacao' => addslashes($this->getUsuarioAtualizacao()),
            'status' => addslashes($this->getStatus()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE processos_juridicos SET " . implode(", ", ($camposUpdate)) . " WHERE proc_id = {$this->getProcId()} LIMIT 1;");

        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function insert() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);

        $this->setQUERY("INSERT INTO processos_juridicos ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setProcId(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE processos_juridicos SET status = 0 WHERE proc_id = {$this->getProcId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM processos_juridicos WHERE proc_id = {$this->getProcId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function getProccess(){
        $this->limpaQuery();
        
        if(!empty($this -> getProcDataCad())){
          $auxiliawhere = "AND proc_data_cad = '".$this -> getProcDataCad()."' "; 
        }
        
        $this->setQUERY("SELECT * FROM processos_juridicos WHERE status = 1 $auxiliawhere ");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function getProcessCalendario($andamento_data_movi=null){
        $this->limpaQuery();
        
        if(!empty($andamento_data_movi)){
          $auxiliawhere = "AND B.andamento_data_movi = '".$andamento_data_movi."' "; 
          $auxiliawhere2 = "AND CASE periodo 
            WHEN 'dias' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY)
            WHEN 'meses' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH)
            WHEN 'anos' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR)
            WHEN 'periodo' THEN oscip_data_termino
            ELSE '' END = '".$andamento_data_movi."' "; 
        } else {
            $auxiliawhere2 = "AND CASE periodo 
            WHEN 'dias' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY)
            WHEN 'meses' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH)
            WHEN 'anos' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR)
            WHEN 'periodo' THEN oscip_data_termino
            ELSE '' END  > DATE_ADD(NOW(), INTERVAL -4 MONTH) "; 
        }
        
        $this->setQUERY("SELECT  B.* , andamento_realizado, A.proc_nome, A.proc_numero_processo,A.proc_tipo_id, reg.regiao, P.proc_status_nome,
            IF(andamento_data_movi = (SELECT MAX(andamento_data_movi) FROM proc_trab_andamento WHERE proc_id = A.proc_id),'1', '0') as ultimo_andamento,
            MONTH(andamento_data_movi) as mes,
             YEAR(andamento_data_movi) as ano
            FROM processos_juridicos AS A
            INNER JOIN proc_trab_andamento as B ON A.proc_id = B.proc_id AND B.andamento_data_movi > DATE_ADD(NOW(), INTERVAL -4 MONTH) 
            INNER JOIN regioes AS reg ON A.id_regiao = reg.id_regiao
            INNER JOIN processo_status AS P ON B.proc_status_id = P.proc_status_id 
            where A.status = 1 AND andamento_status = 1 AND B.proc_status_id NOT IN(1,2) $auxiliawhere
            ORDER BY B.andamento_data_movi DESC;");
        if ($this->setRs()) {
            while($row_processo = mysql_fetch_assoc($this->rs)){ 
                if($row_processo['ultimo_andamento'] == 1 and  $row_processo['andamento_realizado'] == 0){


                        if(($row_processo['proc_status_id'] == 8 or $row_processo['proc_status_id'] == 9 or $row_processo['proc_status_id'] == 11 or
                                $row_processo['proc_status_id'] == 12 or $row_processo['proc_status_id'] == 22 or $row_processo['proc_status_id'] == 25) )

                            {                    continue;   }


                        $dt_hoje_segundos 	   = mktime(0,0,0,date('m'), date('d'), date('Y'));	
                        $dt_andamento_segundos = explode('-',$row_processo['andamento_data_movi']);

                        $dt_vencimento 		   = mktime(0,0,0,$dt_andamento_segundos[1],$dt_andamento_segundos[2],$dt_andamento_segundos[0]);
                        $dt_andamento_segundos = mktime(0,0,0,$dt_andamento_segundos[1],$dt_andamento_segundos[2]-$dias_aviso,$dt_andamento_segundos[0]);


                        if($dt_hoje_segundos > $dt_vencimento and  $row_processo['proc_status_id'] != 1 ) {

                                $andamento_aviso_email['proc_id_expirados'][]    = $row_processo['proc_id'];	
                                $andamento_aviso_email['andamentos_expirados'][] = $row_processo['andamento_id'];		  	
                        }



                        //if(($row_processo['mes'] == (int)$mes) and ($row_processo['ano'] == $ano)) {

                                        if($dt_hoje_segundos >= $dt_andamento_segundos and $dt_vencimento >= $dt_hoje_segundos and  $row_processo['proc_status_id'] != 1 ){

                                                $andamento_aviso_email['proc_id'][]   = $row_processo['proc_id'];
                                                $andamento_aviso_email['andamento'][] = $row_processo['andamento_id'];
                                        } 
                                        
//                                        $arrayX["andamento"][$row_processo['andamento_data_movi']][]  = $row_processo;
                                        $arrayX[$row_processo['andamento_data_movi']][] = $row_processo;
                        //}
                }
            }
        } 
        
        $this->setQUERY("SELECT id_oscip,tipo_oscip, descricao, data_publicacao, numero_periodo, periodo, oscip_data_termino,
            CASE periodo 
            WHEN 'dias' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY)
            WHEN 'meses' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH)
            WHEN 'anos' THEN DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR)
            WHEN 'periodo' THEN oscip_data_termino
            ELSE '' END AS cPeriodo
            FROM obrigacoes_oscip 
            WHERE tipo_oscip = 'oficios recebidos' AND periodo != 'indeterminado' $auxiliawhere2 AND status = 1 ;");
        if ($this->setRs()) {
            while($row_obrigacoesOscip = mysql_fetch_assoc($this->rs)){ 
               
               
            $arrayX[$row_obrigacoesOscip['cPeriodo']][] = $row_obrigacoesOscip;
            }
        }
//        echo "<pre>"; print_r($arrayX); exit;
        return $arrayX;
    }
}

?>