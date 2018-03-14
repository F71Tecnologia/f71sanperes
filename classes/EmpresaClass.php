<?php

require_once('LogClass.php');
require_once('ApiClass.php');

/**
 * PRECISEI CRIAR ESSA 
 * CLASSE PARA O 
 * WEBSERVECE
 */
class Empresa{
    
    private $api;
    
    /**
     * 
     */
    public function __construct() {
        $this->api = new ApiClass();
    }
    
    /**
     * METODO DO WEBSERVECE
     */
    public function getEntidadesAux(){
        
        $entidadesAux = array();
        
        try{
            
            /**
            * TIPOS PERMITIDOS 
            * PARA VISUALIZAÇÃO DO 
            * WEBSERVECE
            */
           if(WEBSERVICE === true){
               $campos = "A.id_empresa as ID_PontoVistoria, '' as ID_lote, A.nome as PontoDeVistoria, A.cidade as NomeCidade, A.uf, A.cnpj, A.cep, A.cod_municipio, A.im as Inscricao_municipal, A.endereco, A.complemento ";
           }else{
               $campos = " * ";
           }
            
            $query = "SELECT {$campos} FROM rhempresa AS A WHERE A.status = 1";
            $sql = mysql_query($query) or die("Erro ao selecioanar Entidades Auxiliares.");
            $entidadesAux = $this->api->montaRetorno($sql);

        }  catch (Exception $e){
            echo $e->getMessage('Erro ao selecionar Entidades Auxiliares');
        }
        
        return $entidadesAux;
    }
    
}

function getEmpresa($id_regiao, $empresa = null) {
    $criterio = "";
    if(!empty($empresa) && isset($empresa)){
        $criterio = " AND id_projeto = '{$empresa}' "; 
    }
    
    $sql = "SELECT *
            FROM rhempresa
            WHERE id_regiao = '{$id_regiao}' $criterio          
            AND status = 1
            ORDER BY nome";
    $empresa = mysql_query($sql) or die(mysql_error());
    return $empresa;
}

function getEmpresaID($id_empresa) {
    $sql = "SELECT A.*, DATE_FORMAT(A.data_inicio, '%d/%m/%Y')as data_inicio, B.regiao AS nome_regiao, C.nome AS nome_projeto
            FROM rhempresa AS A
            LEFT JOIN regioes AS B ON B.id_regiao = A.id_regiao
            LEFT JOIN projeto AS C ON C.id_projeto = A.id_projeto
            WHERE A.id_empresa = '{$id_empresa}'";
    $empresa = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($empresa);
    return $row;
}

function getEmpresaTotal($id_regiao, $id_projeto, $nome){
    $sql = "SELECT *
            FROM rhempresa
            WHERE id_regiao = '{$id_regiao}'
            AND id_projeto = '{$id_projeto}'
            AND nome = '{$nome}'
            AND status = 1
            ORDER BY nome";
    $empresa = mysql_query($sql) or die(mysql_error());
    return $empresa;
}

function getRhProjeto($id_projeto) {
    $sql = "SELECT * FROM projeto WHERE id_projeto = {$id_projeto}";
    $clt = mysql_query($sql);
    $tot = mysql_num_rows($clt);
    return $tot;
}

function alteraStatusEmpresa($id_empresa, $usuario) {
    $log = new Log();
    
    $antigo = $log->getLinha('rhempresa',$id_empresa);
    $sql = "UPDATE rhempresa SET status = 0 WHERE id_empresa = {$id_empresa}";
    $qry = mysql_query($sql);
    $novo = $log->getLinha('rhempresa',$id_empresa);
    
    $log->log('2',"Empresa ID $id_empresa excluida", 'rhempresa', $antigo, $novo);
    $res = mysql_fetch_assoc($qry);
    
    return $res;
    
}

function cadEmpresa($id_regiao){
    $log = new Log();
    $usuario = carregaUsuario();
    $projeto = $_REQUEST['projeto'];
    $id_regiao = ($_REQUEST['regiao_selecionada'] != '') ? $_REQUEST['regiao_selecionada'] : $_SESSION['regiao'];
    $id_usuario = $usuario['id_funcionario'];
    $data_cad = date('Y-m-d');
    $nome = acentoMaiusculo($_REQUEST['nome_fantasia']);
    $razao = acentoMaiusculo($_REQUEST['razao_social']);
    $endereco = acentoMaiusculo($_REQUEST['endereco']);
    $im = ($_REQUEST['im'] != '') ? $_REQUEST['im'] : 'ISENTO';
    $ie = ($_REQUEST['ie'] != '') ? $_REQUEST['ie'] : 'ISENTO';
    $cnpj = $_REQUEST['cnpj'];
    $tipo_cnpj = $_REQUEST['tipo_cnpj'];
    $tel = $_REQUEST['tel'];
    $fax = $_REQUEST['fax'];
    $cep = $_REQUEST['cep'];
    $email = $_REQUEST['email'];
    $site = $_REQUEST['site'];
    $responsavel = acentoMaiusculo($_REQUEST['responsavel']);
    $cpf = $_REQUEST['cpf'];
    $acid_trabalho = $_REQUEST['acid_trabalho'];
    $atividade = $_REQUEST['atividade'];
    $grupo = $_REQUEST['grupo'];
    $proprietarios = $_REQUEST['proprietarios'];
    $familiares = $_REQUEST['familiares'];
    $tipo_pg = $_REQUEST['tipo_pg'];
    $ano = $_REQUEST['ano'];    
    $cnpj_matriz = $_REQUEST['cnpj_matriz'];
    $banco = $_REQUEST['banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $fpas = $_REQUEST['fpas'];
    $tipo_fpas = $_REQUEST['tipo_fpas'];
    $sistema_controle_ponto = $_REQUEST['sistema_controle_ponto'];
    $porte = $_REQUEST['porte'];
    $natureza = str_replace("-", "", $_REQUEST['natureza']);
    $capital = str_replace(',', '.', str_replace(".", "", $_REQUEST['capital']));
    $data_inicio = ($_REQUEST['data_inicio'] != '') ? implode('-',array_reverse(explode('/',$_REQUEST['data_inicio']))) : '';
    $simples = $_REQUEST['simples'];
    $pat = ($_REQUEST['pat'] != '') ? $_REQUEST['pat'] : '0';
    $p_empresa = str_replace(',','.',str_replace('%','',$_REQUEST['p_empresa'])) / 100;
    $p_acid_trabalho = str_replace(',','.',str_replace('%','',$_REQUEST['p_acid_trabalho'])) / 100;
    $p_prolabora = str_replace(',','.',str_replace('%','',$_REQUEST['p_prolabora'])) / 100;
    $p_terceiros = str_replace(',','.',str_replace('%','',$_REQUEST['p_terceiros'])) / 100;
    $p_filantropicas = str_replace(',','.',str_replace('%','',$_REQUEST['p_filantropicas'])) / 100;
    $terceiros = $_REQUEST['terceiros'];
    $cod_municipio = $_REQUEST['cod_municipio'];
    $cnae = str_replace('-', '', str_replace("/", "", $_REQUEST['cnae']));
    $bairro = acentoMaiusculo($_REQUEST['bairro']);
    $cidade = acentoMaiusculo($_REQUEST['cidade']);
    $uf = $_REQUEST['uf'];
    $aliq_rat = str_replace(',','.',str_replace('%','',$_REQUEST['aliquotaRat'])) / 100;
    $outras_entidades = str_replace(',','.',$_REQUEST['outras_entidades']);
    $fap = str_replace(',','.',str_replace('%','',$_REQUEST['aliquotaFap'])) / 100;
    $ses_cnes = $_REQUEST['ses_cnes'];
    
    $arquivo = $_FILES['arquivo'];
    $nome_arquivo = $arquivo['name'];
    $ext_arquivo = strrchr($nome_arquivo, '.');
    
    $empresa = getEmpresaTotal($id_regiao, $projeto, $nome);
    $total_empresa = mysql_num_rows($empresa);
    
    $cpf_val = validaCPF($cpf);
    $cnpj_val = validarCNPJ($cnpj);        
    
    if($ano != ''){
        if(($ano < '1980') || ($ano > date('Y'))){
            $_SESSION['MESSAGE'] = 'Ano inválido!';
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
        }
    }
    
    if ($total_empresa != 0) {
        $_SESSION['MESSAGE'] = 'Já Existe uma Empresa '.$nome.' cadastrada nessa Região e Projeto!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
        
    }elseif($cpf_val == false){
        $_SESSION['MESSAGE'] = 'Número de CPF inválido!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
        
    }elseif($cnpj_val == false){
        $_SESSION['MESSAGE'] = 'Número de CNPJ inválido!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;            
    
    }else{
        $query = "
                    INSERT INTO rhempresa (id_projeto, id_regiao, id_user_cad, data_cad, nome, razao, endereco, im, ie, cnpj, tipo_cnpj, tel, fax, cep, email, site, responsavel, cpf, acid_trabalho, atividade, grupo,
                    proprietarios, familiares, tipo_pg, ano, logo, cnpj_matriz, banco, agencia, conta, fpas, tipo_fpas, porte, natureza, capital, data_inicio, simples, pat, p_empresa, p_acid_trabalho,
                    p_prolabora, terceiros, p_terceiros, p_filantropicas, cod_municipio, cnae, ses_cnes, nat_juridica, bairro, cidade, uf, aliquota_rat, aliquotaRat, outras_entidades, fap, sistema_controle_ponto) VALUES (
                    '{$projeto}','{$id_regiao}','{$id_usuario}','{$data_cad}','{$nome}','{$razao}','{$endereco}','{$im}','{$ie}','{$cnpj}','{$tipo_cnpj}','{$tel}','{$fax}','{$cep}','{$email}','{$site}', 
                    '{$responsavel}','{$cpf}','{$acid_trabalho}','{$atividade}','{$grupo}','{$proprietarios}','{$familiares}','{$tipo_pg}','{$ano}','{$ext_arquivo}','{$cnpj_matriz}','{$banco}','{$agencia}',
                    '{$conta}','{$fpas}','{$tipo_fpas}','{$porte}','{$natureza}','{$capital}','{$data_inicio}','{$simples}','{$pat}','{$p_empresa}','{$p_acid_trabalho}','{$p_prolabora}','{$terceiros}', 
                    '{$p_terceiros}', '{$p_filantropicas}', '{$cod_municipio}', '{$cnae}','{$ses_cnes}','{$natureza}','{$bairro}','$cidade', '$uf', '$aliq_rat', '$aliq_rat', '$outras_entidades', '$fap', '{$sistema_controle_ponto}')
                    ";
        $insere_empresa = mysql_query($query) or die (mysql_error());                    
        $id_empresa = mysql_insert_id();
        
        if($nome_arquivo != ''){
            $diretorio = "../../rh/logo/";
            $nome_tmp = "{$id_regiao}logo{$id_empresa}{$ext_arquivo}";
            $nome_arquivo = $diretorio.$nome_tmp;
            move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
        }
        
        $log->log('2',"Empresa ID $id_empresa cadastrada", 'rhempresa');
        
        if ($insere_empresa) {
            $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';
            $_SESSION['regiao'] = $id_regiao;
            session_write_close();
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao cadastrar a empresa '.$nome;
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
            session_write_close();
            header('Location: index.php');
        }
    }
}

function alteraEmpresa($id_regiao) { 
    $log = new Log();
    
    $usuario = carregaUsuario();    
    $id_empresa = $_REQUEST['empresa'];
    $id_usuario = $usuario['id_funcionario'];
    $data_alter = date('Y-m-d');
    $nome = acentoMaiusculo($_REQUEST['nome_fantasia']);
    $razao = acentoMaiusculo($_REQUEST['razao_social']);
    $endereco = acentoMaiusculo($_REQUEST['endereco']);
    $im = ($_REQUEST['im'] != '') ? $_REQUEST['im'] : 'ISENTO';
    $ie = ($_REQUEST['ie'] != '') ? $_REQUEST['ie'] : 'ISENTO';
    $cnpj = $_REQUEST['cnpj'];
    $tipo_cnpj = $_REQUEST['tipo_cnpj'];
    $tel = $_REQUEST['tel'];
    $fax = $_REQUEST['fax'];
    $cep = $_REQUEST['cep'];
    $email = $_REQUEST['email'];
    $site = $_REQUEST['site'];
    $responsavel = acentoMaiusculo($_REQUEST['responsavel']);
    $cpf = $_REQUEST['cpf'];
    $acid_trabalho = $_REQUEST['acid_trabalho'];
    $atividade = $_REQUEST['atividade'];
    $grupo = $_REQUEST['grupo'];
    $proprietarios = $_REQUEST['proprietarios'];
    $familiares = $_REQUEST['familiares'];
    $tipo_pg = $_REQUEST['tipo_pg'];
    $ano = $_REQUEST['ano'];
    $cnpj_matriz = $_REQUEST['cnpj_matriz'];
    $banco = $_REQUEST['banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $fpas = $_REQUEST['fpas'];
    $tipo_fpas = $_REQUEST['tipo_fpas'];
    $sistema_controle_ponto = $_REQUEST['sistema_controle_ponto'];
    $porte = $_REQUEST['porte'];
    $natureza = str_replace("-", "", $_REQUEST['natureza']);
    $capital = str_replace(',', '.', str_replace(".", "", $_REQUEST['capital']));
    $data_inicio = ($_REQUEST['data_inicio'] != '') ? implode('-',array_reverse(explode('/',$_REQUEST['data_inicio']))) : '';
    $simples = $_REQUEST['simples'];
    $pat = ($_REQUEST['pat'] != '') ? $_REQUEST['pat'] : '0';
    $p_empresa = str_replace(',','.',str_replace('%','',$_REQUEST['p_empresa'])) / 100;
    $p_acid_trabalho = str_replace(',','.',str_replace('%','',$_REQUEST['p_acid_trabalho'])) / 100;
    $p_prolabora = str_replace(',','.',str_replace('%','',$_REQUEST['p_prolabora'])) / 100;
    $terceiros = $_REQUEST['terceiros'];
    $p_terceiros = str_replace(',','.',str_replace('%','',$_REQUEST['p_terceiros'])) / 100;
    $p_filantropicas = str_replace(',','.',str_replace('%','',$_REQUEST['p_filantropicas'])) / 100;
    $cod_municipio = $_REQUEST['cod_municipio'];
    $cnae = str_replace('-', '', str_replace("/", "", $_REQUEST['cnae']));
    $ses_cnes = $_REQUEST['ses_cnes']; 
    $bairro = acentoMaiusculo($_REQUEST['bairro']);
    $cidade = acentoMaiusculo($_REQUEST['cidade']);
    $uf = $_REQUEST['uf'];
    $aliq_rat = str_replace(',','.',str_replace('%','',$_REQUEST['aliquotaRat'])) / 100;
    $outras_entidades = str_replace(',','.',$_REQUEST['outras_entidades']);
    $fap = str_replace(',','.',str_replace('%','',$_REQUEST['aliquotaFap'])) / 100;
    
    //dados usuario para cadastro de log
    $local = "Alteração de Empresa";
    $ip = $_SERVER['REMOTE_ADDR'];
    $acao = "{$usuario['nome']} alterou a empresa " . $id_empresa;    
    $tipo_usuario = $usuario['tipo_usuario'];
    $grupo_usuario = $usuario['grupo_usuario'];
    $regiao = $usuario['id_regiao'];
    
    $cpf_val = validaCPF($cpf);
    $cnpj_val = validarCNPJ($cnpj);
    
    
    if($ano != ''){
        if(($ano < '1980') || ($ano > date('Y'))){
            $_SESSION['MESSAGE'] = 'Ano inválido!';
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
        }
    }
    
    if($cpf_val == false){
        $_SESSION['MESSAGE'] = 'Número de CPF inválido!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
        
    }elseif($cnpj_val == false){
        $_SESSION['MESSAGE'] = 'Número de CNPJ inválido!';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;            
    
    }else{
        
        $antigo = $log->getLinha('rhempresa',$id_empresa);
        
        $altera_empresa = mysql_query("UPDATE rhempresa SET id_user_alter = '{$id_usuario}', data_alter = '{$data_alter}', nome = '{$nome}', razao = '{$razao}', endereco = '{$endereco}', im = '{$im}', ie = '{$ie}', cnpj = '{$cnpj}', tipo_cnpj = '{$tipo_cnpj}', tel = '{$tel}', fax = '{$fax}', cep = '{$cep}', email = '{$email}', 
                site = '{$site}', responsavel = '{$responsavel}', cpf = '{$cpf}', acid_trabalho = '{$acid_trabalho}', atividade = '{$atividade}', grupo = '{$grupo}', proprietarios = '{$proprietarios}', familiares = '{$familiares}', tipo_pg = '{$tipo_pg}', 
                ano = '{$ano}', cnpj_matriz = '{$cnpj_matriz}', banco = '{$banco}', agencia = '{$agencia}', conta = '{$conta}', fpas = '{$fpas}', tipo_fpas = '{$tipo_fpas}', porte = '{$porte}', natureza = '{$natureza}', capital = '{$capital}', 
                data_inicio = '{$data_inicio}', simples = '{$simples}', pat = '{$pat}', p_empresa = '{$p_empresa}', p_acid_trabalho = '{$p_acid_trabalho}', p_prolabora = '{$p_prolabora}', terceiros = '{$terceiros}', p_terceiros = '{$p_terceiros}', 
                p_filantropicas = '{$p_filantropicas}', cod_municipio = '{$cod_municipio}', cnae = '{$cnae}',  ses_cnes = '{$ses_cnes}', nat_juridica = '{$natureza}', bairro = '{$bairro}', cidade = '{$cidade}', uf = '{$uf}', aliquotaRat = '{$aliq_rat}', aliquota_rat = '{$aliq_rat}', outras_entidades = '{$outras_entidades}', fap = '{$fap}', sistema_controle_ponto = '{$sistema_controle_ponto}' WHERE id_empresa = '{$id_empresa}'") or die(mysql_error());
        $novo = $log->getLinha('rhempresa',$id_empresa);
        
        $log->log('2',"Empresa ID $id_empresa atualizada", 'rhempresa',$antigo,$novo);
        
        if ($altera_empresa) {
            $_SESSION['MESSAGE'] = 'Informações alteradas com sucesso!'.$id_regiao;
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';
            $_SESSION['regiao'] = $id_regiao;
            header('Location: index.php');
            
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao atualizar a empresa';
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
        }
    }
}

//Carrega os fpas
function carregaFpas(){
    $sql = "SELECT * FROM fpas order by id";
    $fpas = mysql_query($sql) or die(mysql_error());
    $array = "";
    $array[''] = "SELECIONE O FPAS";
    while ($row = mysql_fetch_assoc($fpas)) {
        $array[$row['id']] = $row['codigo'];
    }
    return $array;
}
?>