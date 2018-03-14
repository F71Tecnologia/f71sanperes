<?php
/**
 * Description of InformeRendimentoClass
 *
 * @author Renato
 */
class Login {
    
    /*private $login;
    private $senha;*/
    private $erro;
    private $dados;
    private $usuario = array(
        'id_funcionario' => 0,
        'id_master' => 0,
        'id_regiao' => 0,
        'tipo_usuario' => 0,
        'grupo_usuario' => 0,
        'horario_inicio' => '',
        'horario_fim' => '',
        'acesso_dias' => '',
        'nome' => '',
        'salario' => '',
        'regiao' => '',
        'funcao' => '',
        'locacao' => '',
        'endereco' => '',
        'bairro' => '',
        'cidade' => '',
        'uf' => '',
        'cep' => '',
        'tel_fixo' => '',
        'tel_rec' => '',
        'data_nasci' => '',
        'naturalidade' => '',
        'nacionalidade' => '',
        'cicil' => '',
        'ctps' => '',
        ''
        );
    
    /**
     * MÉTODO PARA PEGAR O ACESSO
     * @param $login
     * @param $senha
     */
    public function getAcesso($login, $senha){
        $sql = mysql_query("SELECT * FROM funcionario WHERE login = '$login' AND senha = '$senha' AND status_reg = '1' LIMIT 1");
        if(mysql_num_rows($sql) == 0){
            $this->erro = 'Login ou senha incorreto!';
        }else{
            $this->dados = mysql_fetch_assoc($sql);
        }
    }
    
    /**
     * MÉTODO PARA PEGAR O ACESSO
     * @param $id
     */
    public function getAcessoById($id){
        $sql = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id' LIMIT 1");
        if(mysql_num_rows($sql) == 0){
            $this->erro = 'Login ou senha incorreto!';
        }else{
            $this->dados = mysql_fetch_assoc($sql);
        }
    }
    

    /**
     * MÉTODO PARA PEGAR O ACESSO
     * @param $f array()
     */
    public function getRegiaoByFuncionario($f){
        $row = null;
        $sql = mysql_query("SELECT * FROM funcionario_regiao_assoc WHERE id_funcionario = {$f['id_funcionario']} AND id_master = {$f['id_master']} AND id_regiao = {$f['id_regiao']} LIMIT 1");
        if(mysql_num_rows($sql) == 0){
            $row = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario_regiao_assoc WHERE id_funcionario = {$f['id_funcionario']} ORDER BY id_regiao LIMIT 1"));
        }
        return $row;
    }
    
    /**
     * MÉTODO PARA MUDAR PARA UMA REGIAO E MASTER QUE O USUARIO TENHA ACESSO
     * @param $array
     */
    public function mudaRegiaoMasterFuncionario($array){
        $sql = "UPDATE funcionario SET id_regiao = {$array['id_regiao']}, id_master = {$array['id_master']} WHERE id_funcionario = {$array['id_funcionario']} LIMIT 1";
        mysql_query($sql);
    }
    
    /**
     * MÉTODO PARA VERIFICAR A SENHA NOVA COM A ANTIGA
     * @param $login
     * @param $senha
     * @param $senhaAntiga
     */
    public function verificaSenha($login, $senha, $senhaAntiga){
        $sql = mysql_query("SELECT * FROM funcionario WHERE login = '$login' AND senha = '$senhaAntiga' AND status_reg = '1' AND alt_senha = 1 LIMIT 1");
        if(mysql_num_rows($sql) == 0){
            $this->erro = 'Senha antiga incorreta!';
        }elseif($senhaAntiga == $senha){
            $this->erro = 'A senha nova NÃO pode ser IGUAL a senha antiga!';
        }elseif($senha == '' OR $senha == ' '){
            $this->erro = 'A senha nova NÃO pode ser VAZIA!';
        }
    }
    
    /**
     * MÉTODO PARA ATUALIZAR A SENHA
     * @param $senha
     * @param $idFuncionario
     */
    public function atualizaSenha($senha, $idFuncionario) {
        mysql_query("UPDATE funcionario SET senha = '$senha', alt_senha = '0' WHERE id_funcionario = '$idFuncionario' LIMIT 1") or die("Erro " . mysql_error());
    }
    
    /**
     * MÉTODO PARA VERIFICAR SE O USUARIO PODE ACESSAR NESSE DIA E HORA
     * @param $acesso_dias
     * @param $horario_inicio
     * @param $horario_fim
     */
    public function getAcessoDias($acesso_dias, $horario_inicio, $horario_fim) {
        if($acesso_dias != 7) {
            $horario_inicio = str_replace(':','',$horario_inicio);
            $horario_fim = str_replace(':','',$horario_fim);
            $horaAtual = date('His');
            $dias_semana = array('1', '2', '3', '4', '5');
            
            if (!in_array(date('w'), $dias_semana) OR ($horario_inicio >= $horaAtual OR $horario_fim <= $horaAtual)) {
                //$this->erro = 'Seu IP foi gravado!<br>Você não possui autorização para acessos fora de seu horário de trabalho.';
                $this->erro = 'Fora do seu hor&aacute;rio de trabalho.';
            }
        }
    }
    
    /**
     * MÉTODO PARA GRAVAR A SESSAO
     * @param $funcionario
     */
    public function gravaSessao($funcionario) {
        $_SESSION['id_regiao'] = $funcionario['id_regiao'];
        $_SESSION['id_master'] = $funcionario['id_master'];
        $_SESSION['id_user'] = $funcionario['id_funcionario'];
    }
    
    /**
     * MÉTODO PARA GRAVAR A LOG
     * @param $funcionario
     */
    public function gravaLog($funcionario) {
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = date("d/m/Y H:i");
        $cabecalho = "($funcionario[id_funcionario]) $funcionario[nome] às " . $data . "h (ip: $ip)";
        $local = "Login Principal";
        $acao = "Efetuando o Login na Intranet";

        mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
        VALUES ('$funcionario[id_funcionario]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local', NOW(), '$ip', '$acao')") or die("Erro Inesperado<br><br>" . mysql_error());

        $arquivo = fopen("log/" . $funcionario[id_funcionario] . ".txt", "a");
        fwrite($arquivo, "$cabecalho");
        fwrite($arquivo, "\r\n");
        fwrite($arquivo, "$local");
        fwrite($arquivo, "\r\n");
        fwrite($arquivo, "$acao");
        fwrite($arquivo, "\r\n");
        fwrite($arquivo, "\r\n");
        fwrite($arquivo, "---------------------------------------------------------------");
        fwrite($arquivo, "\r\n");
        fwrite($arquivo, "\r\n");
        fclose($arquivo);
    }
    
    public function getErro() {
        return $this->erro;
    }
    
    public function setErro($erro) {
        $this->erro = $erro;
    }
    
    public function getDados() {
        return $this->dados;
    }
}