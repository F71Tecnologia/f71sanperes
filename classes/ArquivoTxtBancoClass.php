<?php
class ArquivoTxtBancoClass {
    
    private $DD;
    private $MM;
    private $ANO;
    private $PASTA;
    private $CAMINHO;
    private $maxArq;
    public $CONSTANTE;
    //public $TIPO;
    public $numeroSequencial; 
    public $arrayValorTotal;
    
    public function __construct() {
        $this->DD = date('d');
        $this->MM = date('m');
        $this->ANO= date('Y');
    }
   
    //CORPO DO ARQUIVO TXT
    public function bodyTxtBanco($row_master, $row_banco, $ids, $dataPagamento){
        if($this->PASTA == 'FERIAS'){
            $sqlClt = "SELECT A.id_clt, B.id_ferias registro, A.nome, A.campo3, B.total_liquido, A.conta, A.agencia, A.tipo_conta FROM rh_clt A, rh_ferias B WHERE A.id_clt = B.id_clt AND B.id_ferias IN(".implode(',',$ids).") AND B.status = '1' AND A.conta NOT IN('', '000000') AND A.tipo_conta != '' AND B.total_liquido != 0.00 ORDER BY A.nome ASC;";
        }else if($this->PASTA == 'RESCISAO'){
            $sqlClt = "SELECT A.id_clt, B.id_recisao registro, A.nome, A.campo3, B.total_liquido, A.conta, A.agencia, A.tipo_conta FROM rh_clt A, rh_recisao B WHERE A.id_clt = B.id_clt AND B.id_recisao IN(".implode(',',$ids).") AND B.status = '1' AND A.conta NOT IN('', '000000') AND A.tipo_conta != '' AND B.total_liquido != 0.00 ORDER BY A.nome ASC;";
        }
        $sqlClt = mysql_query($sqlClt);
        while($row_clt = mysql_fetch_assoc($sqlClt)){
            $nomeArquivo = '';
            if($row_banco['id_nacional'] == '237'){//237 = folha/BANCOS/BRADESCO/
                include('../folha/BANCOS/BRADESCO/detalhes_bradesco_rescisao.php');
                $nomeArquivo = 'BRADESCO';
            }else if($row_banco['id_nacional'] == '356'){//356 = BANCOS/REAL/
                include('../folha/BANCOS/REAL/detalhes_real_rescisao.php');
                $nomeArquivo = 'REAL';
            }else if($row_banco['id_nacional'] == '033'){//033 = BANCOS/SANTANDER/
                include('../folha/BANCOS/SANTANDER/detalhes_santander_rescisao.php');
                $nomeArquivo = 'SANTANDER';
            }else if($row_banco['id_nacional'] == '341'){//341 = BANCOS/ITAU/
                include('../folha/BANCOS/ITAU/detalhes_itau_rescisao.php');
                $nomeArquivo = 'ITAU';
            }else if($row_banco['id_nacional'] == '001'){//001 = BANCOS/BRASIL/
                include('../folha/BANCOS/BRASIL/detalhes_brasil_rescisao.php');
                $nomeArquivo = 'BRASIL';
            }
            
            $nomeArquivo .= "/{$this->CAMINHO}_".strtoupper($row_clt['tipo_conta'][0])."";
            $this->insereRegistro($row_banco['id_banco'], $row_clt['registro'], $dataPagamento, strtolower($this->PASTA[0]), $row_clt['tipo_conta'][0], $nomeArquivo, $this->maxArq);
        }
    }
    
    //CABEÇALHO DO ARQUIVO TXT
    public function headerTxtBanco($row_master, $row_banco, $dataPagamento){
        list($d,$m,$a) = explode('/', $dataPagamento);
        
        if($row_banco['id_nacional'] == '237'){
            include('../folha/BANCOS/BRADESCO/header_bradesco_rescisao.php');
        }else if($row_banco['id_nacional'] == '356'){
            include('../folha/BANCOS/REAL/header_real_rescisao.php');
        }else if($row_banco['id_nacional'] == '033'){
            include('../folha/BANCOS/SANTANDER/header_santander_rescisao.php');
        }else if($row_banco['id_nacional'] == '341'){
            include('../folha/BANCOS/ITAU/header_itau_rescisao.php');
        }else if($row_banco['id_nacional'] == '001'){
            include('../folha/BANCOS/BRASIL/header_brasil_rescisao.php');
        }
    }
    
    //RODAPE DO ARQUIVO TXT
    public function footerTxtBanco($row_master, $row_banco){
        if($row_banco['id_nacional'] == '237'){
            include('../folha/BANCOS/BRADESCO/trailler_bradesco_rescisao.php');
        }else if($row_banco['id_nacional'] == '356'){
            include('../folha/BANCOS/REAL/trailler_real_rescisao.php');
        }else if($row_banco['id_nacional'] == '033'){
            include('../folha/BANCOS/SANTANDER/trailler_santander_rescisao.php');
        }else if($row_banco['id_nacional'] == '341'){
            include('../folha/BANCOS/ITAU/trailler_itau_rescisao.php');
        }else if($row_banco['id_nacional'] == '001'){
            include('../folha/BANCOS/BRASIL/trailler_brasil_rescisao.php');
        }
    }
    
    //GERA O ARQUIVO TXT
    public function gerarTxtBanco($pasta, $idBanco, $dataPagamento, $ids){
        $dataPagamento = $_REQUEST['data'];
        $this->PASTA = $pasta;
        
        if($this->verificaRegistro($ids) == 0){
            
            $sqlBanco = mysql_fetch_assoc(mysql_query("SELECT * FROM bancos WHERE id_banco = {$idBanco} LIMIT 1"));
            //print_r($sqlBanco);exit;
            //$sqlRegiao = mysql_fetch_assoc(mysql_query("SELECT * FROM regioes WHERE id_regiao = {$sqlBanco['id_regiao']} LIMIT 1"));
            $sqlMaster = mysql_fetch_assoc(mysql_query("SELECT m.* FROM master m, regioes r WHERE m.id_master = r.id_master AND r.id_regiao = {$sqlBanco['id_regiao']} LIMIT 1"));
            $sqlEmpresa = mysql_fetch_assoc(mysql_query("SELECT * FROM rhempresa WHERE id_regiao = {$sqlBanco['id_regiao']} LIMIT 1"));
            
            if($sqlBanco['id_nacional'] == '237'){
                $this->CONSTANTE = "BRADESCO";
            }else if($sqlBanco['id_nacional'] == '356'){
                $this->CONSTANTE = "REAL";
            }else if($sqlBanco['id_nacional'] == '033'){
                $this->CONSTANTE = "SANTANDER";
            }else if($sqlBanco['id_nacional'] == '341'){
                $this->CONSTANTE = "ITAU";
            }else if($sqlBanco['id_nacional'] == '001'){
                $this->CONSTANTE = "BRASIL";
            }
            $this->maxArq = $this->maxArq();
            if(empty($this->maxArq)){$this->maxArq = '1';}else{$this->maxArq = $this->maxArq + 1;}
            //$this->maxArq;
            $this->CAMINHO = "{$this->PASTA}/{$this->ANO}_{$this->MM}_{$this->DD}_{$this->CONSTANTE}_{$this->maxArq}";
            
            $this->headerTxtBanco($sqlMaster, $sqlBanco, $dataPagamento);
            $this->bodyTxtBanco($sqlMaster, $sqlBanco, $ids, $dataPagamento);
            $this->footerTxtBanco($sqlMaster, $sqlBanco);
            
            if($this->numeroSequencial[c] > 2){
                $this->gravaLog("Criação do Arquivo Banco", "{$this->CAMINHO}_C");
            }
            if($this->numeroSequencial[s] > 2){
                $this->gravaLog("Criação do Arquivo Banco", "{$this->CAMINHO}_S");
            }
        }
    }
    
    //PEGA O NUMERO DO ULTIMO ARQUIVO GERADO NO DIA
    public function maxArq(){
        return $query = mysql_result(mysql_query("SELECT MAX(numero_arq) FROM bancos_arq_pag WHERE DATE(data_gerado) = DATE(NOW())"),0);
    }
    
    //GRAVA NO BANCO
    public function insereRegistro($idBanco, $idRegistro, $dataPagamento, $tipo, $tipoConta, $nomeArquivo, $maxArq){
        $dataPg = explode('/',$dataPagamento);
        $dataPg = "{$dataPg[2]}-{$dataPg[1]}-{$dataPg[0]}";
        $now = date("Y-m-d H:i:s");
        $sql = "INSERT INTO bancos_arq_pag (`id_banco`, `id_registro`, `data_pg`, `tipo`, `tipo_conta`, `data_gerado`, `nome_arquivo`, `numero_arq`) VALUES ($idBanco, $idRegistro, '$dataPg', '$tipo', '$tipoConta', '$now', '$nomeArquivo', $maxArq);";
        mysql_query($sql) or die("Erro ao inserir na tabela");
    }
    
    //MUDA STATUS PARA 'i' PARA GUARDAR HISTORICO
    public function deletarRegistro($nomeArq){
        $sql = "UPDATE bancos_arq_pag SET status = 'i' WHERE MD5(nome_arquivo) = '{$nomeArq}';";
        mysql_query($sql) or die("Erro ao excluir na tabela");
        $query = mysql_fetch_array(mysql_query("SELECT nome_arquivo FROM bancos_arq_pag WHERE MD5(nome_arquivo) = '{$nomeArq}';"));
        $acao = explode('/', $query['nome_arquivo']);
        $this->gravaLog("Exclusão do Arquivo Banco", $acao[2]);
    }
    
    //PEGA OS ARQUIVOS DE RESCISAO E OS CLTS PERTENCENTES
    public function getRegistrosRescisao($idRegiao){
        $query = mysql_query("
        SELECT A.id_bancos_arq_pag, A.nome_arquivo, A.tipo_conta, date_format(A.data_pg, '%d/%m/%Y') data_pgBR,
        B.nome, C.total_liquido, D.razao
        FROM 
            bancos_arq_pag A 
            LEFT JOIN rh_recisao C ON (A.id_registro = C.id_recisao)
            LEFT JOIN rh_clt B ON (B.id_clt = C.id_clt)
            LEFT JOIN bancos D ON (A.id_banco = D.id_banco)
        WHERE
            A.tipo = 'r' AND A.status = 'a' AND B.id_regiao = {$idRegiao}
        ORDER BY A.data_gerado, A.tipo_conta, B.nome");
        while($row = mysql_fetch_assoc($query)){
            $array[$row['id_bancos_arq_pag']]['nome'] = $row['nome'];
            $array[$row['id_bancos_arq_pag']]['razao'] = $row['razao'];
            $array[$row['id_bancos_arq_pag']]['data'] = $row['data_pgBR'];
            $array[$row['id_bancos_arq_pag']]['tipo_conta'] = $row['tipo_conta'];
            $array[$row['id_bancos_arq_pag']]['nome_arquivo'] = $row['nome_arquivo'];
            $array[$row['id_bancos_arq_pag']]['total_liquido'] = $row['total_liquido'];
        }
        return $array;
    }
    
    //PEGA OS ARQUIVOS DE FERIAS E OS CLTS PERTENCENTES
    public function getRegistrosFerias($idRegiao){
        $query = mysql_query("
        SELECT A.id_bancos_arq_pag, A.nome_arquivo, A.tipo_conta, date_format(A.data_pg, '%d/%m/%Y') data_pgBR,
        C.mes, C.ano, B.nome, C.total_liquido, D.razao
        FROM 
            bancos_arq_pag A 
            LEFT JOIN rh_ferias C ON (A.id_registro = C.id_ferias)
            LEFT JOIN rh_clt B ON (B.id_clt = C.id_clt)
            LEFT JOIN bancos D ON (A.id_banco = D.id_banco)
        WHERE
            A.tipo = 'f' AND A.status = 'a' AND B.id_regiao = {$idRegiao}
        ORDER BY A.data_gerado, A.tipo_conta, B.nome");
        while($row = mysql_fetch_assoc($query)){
            $array[$row['id_bancos_arq_pag']]['mes'] = $row['mes'];
            $array[$row['id_bancos_arq_pag']]['ano'] = $row['ano'];
            $array[$row['id_bancos_arq_pag']]['nome'] = $row['nome'];
            $array[$row['id_bancos_arq_pag']]['razao'] = $row['razao'];
            $array[$row['id_bancos_arq_pag']]['data'] = $row['data_pgBR'];
            $array[$row['id_bancos_arq_pag']]['tipo_conta'] = $row['tipo_conta'];
            $array[$row['id_bancos_arq_pag']]['nome_arquivo'] = $row['nome_arquivo'];
            $array[$row['id_bancos_arq_pag']]['total_liquido'] = $row['total_liquido'];
        }
        return $array;
    }
    
    //PEGA OS CLTS QUE JA ESTAO CADASTRADOS
    public function getRegistros($tipo){
        $query = mysql_query("SELECT id_registro FROM bancos_arq_pag WHERE tipo = '{$tipo}' AND status = 'a'");
        while($row = mysql_fetch_assoc($query)){
            $array[$row['id_registro']] = 1;
        }
        return $array;
    }
    
    //VERIFICAR CLT PARA EVITAR DUPLICIDADE
    public function verificaRegistro($ids){
        $tipo = (strtolower($this->PASTA[0]));
        $query = mysql_query("SELECT id_registro FROM bancos_arq_pag WHERE DATE(data_gerado) = DATE(NOW()) AND tipo = '$tipo' AND id_registro IN(".implode(',',$ids).") AND status = 'a'");
        return mysql_num_rows($query);
    }
    
    //DOWNLOAD DO ARQUIVO
    public function downloadArquivo($idArq){
        $query = mysql_fetch_assoc(mysql_query("SELECT nome_arquivo FROM bancos_arq_pag WHERE md5(id_bancos_arq_pag) = '{$idArq}'"));
        $path = "../folha/BANCOS/{$query['nome_arquivo']}.txt";
        if(file_exists($path)){
            $fileName = basename($path);
            header("Content-Type: application/force-download");
            header("Content-type: application/octet-stream;");
            header("Content-Length: ".filesize($path));
            header("Content-disposition: attachment; filename=".$fileName);
            header("Pragma: no-cache");
            header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
            header("Expires: 0");
            readfile($path);
            flush();
            exit;
        }
        return false;
    }
    
    //GRAVA LOG DE CRIACAO E EXCLUSAO DOS ARQUIVOS
    public function gravaLog($local, $acao) {
        $f = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "{$local}: {$acao}";
        $now = date("Y-m-d H:i:s");
        $sqlLog = "INSERT INTO log 
        (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
        VALUES 
        ('$f[id_funcionario]', '$f[id_regiao]', '$f[tipo_usuario]', '$f[grupo_usuario]', '$local', '$now', '$ip', '$acao')";
        mysql_query($sqlLog);
    }
}