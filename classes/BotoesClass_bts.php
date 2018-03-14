<?php
/**
 * Description of BotoesClass
 *
 * @author Ramon Lima
 */
class BotoesClass {
    
    public $classModulos;
    public $iconsModulos;
    private $defaultPath;
    
    public function __construct($path=null) {
        $this->classModulos = array(
            "1"=>"box-principal",
            "2"=>"box-admin",
            "3"=>"box-rh",
            "4"=>"box-financeiro",
            "5"=>"box-contabil",
            "6"=>"box-sistema",
            "24"=>"box-juridico",
            "35"=>"box-compras",
            "36"=>"box-contas"
            );
        
        $this->iconsModulos = array(
            "1"=>"<span class=\"glyphicon glyphicon-home\"></span>",
            "2"=>"<span class=\"glyphicon glyphicon-cog\"></span>",
            "3"=>"<span class=\"fa fa-users\"></span>",
            "4"=>"<span class=\"glyphicon glyphicon-usd\"></span>",
            "5"=>"<span class=\"glyphicon glyphicon-dashboard\"></span>",
            "6"=>"<span class=\"glyphicon glyphicon-phone\"></span>",
            "24"=>"<span class=\"glyphicon glyphicon-briefcase\"></span>",
            "35"=>"<span class=\"glyphicon glyphicon-shopping-cart\"></span>",
            "36"=>"<span class=\"glyphicon glyphicon-list-alt\"></span>"
            );
        
        $this->defaultPath = $path;
    }


    /**
     * Busca os módulos que o usuário logado tem permissão.
     * Vai na tabela de permissões por botão, busca todas as permissões e agrupa pelo Módulo
     * Trazendo apenas o nescessário para o saber quis modulos a pessoa tem acesso
     * @param type $user não é obrigatório, pegando o cookie
     * @return array
     */
    public function getModulos($user = null, $pagina=1) {
        if ($user === null) {
            $usuario = carregaUsuario();
        } else {
            $usuario['id_funcionario'] = $user;
        }
        
        $qrModulos = "SELECT C.botoes_menu_id,C.botoes_menu_nome FROM botoes_assoc AS A
                        INNER JOIN botoes AS B ON (A.botoes_id = B.botoes_id)
                        INNER JOIN botoes_menu AS C ON (B.botoes_menu = C.botoes_menu_id)
                        WHERE A.id_funcionario = {$usuario['id_funcionario']} AND C.botoes_pagina = {$pagina}
                        GROUP BY B.botoes_menu";
        $rsModulos = mysql_query($qrModulos);
        $modulos = array();
        while ($row = mysql_fetch_assoc($rsModulos)) {
            $modulos[$row['botoes_menu_id']] = $row['botoes_menu_nome'];
        }
        return $modulos;
    }
    
    public function getBotoesModulo($key, $user=null){
        if ($user === null) {
            $usuario = carregaUsuario();
        } else {
            $usuario['id_funcionario'] = $user;
        }
        
        $qrModulos = "SELECT B.botoes_nome,B.botoes_id,B.botoes_link,B.botoes_img,B.nova_url,C.botoes_pagina,B.botoes_link,B.nova_aba FROM botoes_assoc AS A
                    INNER JOIN botoes AS B ON (A.botoes_id = B.botoes_id)
                    INNER JOIN botoes_menu AS C ON (B.botoes_menu = C.botoes_menu_id)
                    WHERE A.id_funcionario = {$usuario['id_funcionario']} AND B.botoes_menu = {$key} AND B.status = 1 ORDER BY B.botoes_nome";
        $rsModulos = mysql_query($qrModulos);
        $modulos = array();
        while ($row = mysql_fetch_assoc($rsModulos)) {
            $modulos[] = $row;
        }
        return $modulos;
    }
    
    public function getBotoesMenuModulo($key){
        return $this->getModulos(null, $key);
    }
    
    public function getHtmlBotoesModulo($key, $keyMaster = null){
        $btsModulo = $this->getBotoesModulo($key);
        /*A principal mostra itens diferentes*/
        
        if(array_key_exists($key, $this->classModulos)){
            $classModulos = $this->classModulos[$key];
            $iconsModulos = $this->iconsModulos[$key];
        }else{
            $classModulos = $this->classModulos[$keyMaster];
        }
        
        if($key == 1){
            //e-mail
	
	    $param = "webmail_host=".$_SESSION['webmail_host']."&email=".$_SESSION['email'];
	    $param.= "&password=".urlencode($_SESSION['password'])."&hostref=".$_SESSION['hostref'];
	    $param.= "&hostname=".$_SESSION['hostname']."&id_regiao=".$_SESSION['id_regiao'];
	    $param.= "&id_master=".$_SESSION['id_master']."&id_user=".$_SESSION['id_user'];

            $html = '<div>
			<iframe id="iframemail" src="http://www.netsorrindo.com/intranet_2014_11_19/webmail/index.php?box=Inbox&boxfull=INBOX&'.$param.'" width="100%" height="700" frameborder="0"></iframe>
                    </div>';
            
            $html .= "<div class=\"col-lg-4\">
                     <div class=\"bs-glyphicons row\">
                     <div class=\"bs-glyphicons-list {$classModulos}-list\">";
        }else{
            $html = "<div class=\"bs-glyphicons  row\">
                     <div class=\"{$classModulos}-list\">";
        }
        
        foreach($btsModulo as $bt){
            $url = $bt['botoes_link'];
            //$novaAba = ($bt['nova_aba']==1) ? "target='_blank'":"";
//            if(!array_key_exists($key, $this->classModulos)){
                $iconsModulos = "<img src='{$this->defaultPath}{$bt['botoes_img']}' />";
                if(empty($bt['botoes_link'])){
                    $url = current(explode("?",$bt['botoes_link']));
                    $url = str_replace("rh/", "", $url);
                }
//            }
            
            $novaAba = "target='_blank'";
            
            if($bt['botoes_id'] == 4){
                $url = $bt['nova_url'];
                $novaAba = "";
            }
            
            
            if($key == 1){
                $html .= "<a href='{$url}' $novaAba class=\"bot_nv col-lg-4 col-md-4 col-sm-4 col-xs-6\">
                                {$iconsModulos}
                                <span class=\"glyphicon-class\">{$bt['botoes_nome']}</span>
                          </a>";
            }else{
                $html .= "<a href='{$url}' $novaAba class=\"bot_nv col-lg-2 col-md-4 col-sm-4 col-xs-6\">
                                {$iconsModulos}
                                <span class=\"glyphicon-class\">{$bt['botoes_nome']}</span>
                          </a>";
            }
        }
        $html .= "</div></div>";
        
        /*A principal mostra itens diferentes*/
        if($key == 1){
            $html .= "</div><div class=\"col-lg-4\"><h3>Aniversariantes</h3><div class=\"list-group\">";
            $funcionario = new FuncionarioClass();
            $feriados = new FeriadosClass();
            $nivers = $funcionario->getAniversariantes();
            foreach($nivers as $func){
                $html .= "<div class=\"list-group-item\">
                            <span class=\"pull-right glyphicon glyphicon-gift\"></span>
                            <p class=\"list-group-item-text\"><strong>{$func['data']}</strong> - {$func['nome1']}</p>
                          </div>";
            }
            $html .= "</div></div>";
            
            $html .= "<div class=\"col-lg-4\"><h3>Feriados</h3><div class=\"list-group\">";
            $rsferiados = $feriados->getFeriados();
            foreach($rsferiados as $feriado){
                $html .= "<div class=\"list-group-item\">
                            <span class=\"pull-right fa fa-calendar-o\"></span>
                            <p class=\"list-group-item-text\"><strong>{$feriado['dataBr']}</strong> - {$feriado['nome']}</p>
                          </div>";
            }
            $html .= "</div></div>";
            
        }
        
        return $html;
    }


    public function getHtmlBoxInfo($key, $user = null){
        if ($user === null) {
            $usuario = carregaUsuario();
        } else {
            $usuario['id_funcionario'] = $user;
        }
        
        $html = "";
        switch ($key){
            case 1:
                //principal/suporte
                $chamados = SuporteClass::getQntChamadosByUsuario($usuario['id_funcionario']);
                $aniversariantes = FuncionarioClass::getTotalAniversariantes();
                
                $html = "<p class=\"box-p-grande\"><span id='email-unread'></span> Email(s)</p>
                        <p class=\"box-p-medio\">{$aniversariantes} Aniversariante(s)</p>
                        <p class=\"box-p-medio\">{$chamados} Chamados(s)</p>";
                break;
            case 2:
                //adm
                $expira_hoje = ObrigacoesClass::getExpiraHoje();
                $vai_expirar = ObrigacoesClass::getIraExpirar();
                $expirado = ObrigacoesClass::getExpirado(); 
                $total_adm = $expira_hoje + $vai_expirar + $expirado;
                
                $html = "<p class=\"box-p-grande\">{$total_adm} Iten(s)</p>
                        <p class=\"box-p-medio\">obrigações</p>
                        <p>{$expira_hoje} expira hoje</p>
                        <p>{$vai_expirar} irá expirar</p>
                        <p>{$expirado} expirado(s)</p>";
                break;
            case 3:
                //rh
                $ferias = FuncionarioClass::getFuncionariosEmFerias();
                $licenca = FuncionarioClass::getFuncionariosEmLicenca();
                $demissao = FuncionarioClass::getFuncionariosAguardandoDemissao();
                $total_rh = FuncionarioClass::getFuncionariosAtividadeNormal();
                
                $html = "<p class=\"box-p-grande\">{$total_rh} Clt(s)</p>
                        <p>{$ferias} em férias</p>
                        <p>{$licenca} sob licença</p>
                        <p>{$demissao} aguardando demissão</p>";
                break;
            case 4:
                //financeiro
                $vence_hoje = Saida::getSaidasHoje();
                $vence_amanha = Saida::getSaidasAmanha();
                $vence_mes = Saida::getSaidasMes();
                $total_finan = $vence_hoje + $vence_amanha + $vence_mes;
                
                $html = "<p class=\"box-p-grande\">{$total_finan} Contas(s)</p>
                        <p class=\"box-p-medio\">pendentes</p>
                        <p>{$vence_hoje} hoje</p>
                        <p>{$vence_amanha} amanhã</p>
                        <p>{$vence_mes} até o fim do mês</p>";
                break;
            case 5:
                $html = "<p class=\"box-p-grande\">Sem pendências</p>";
                break;
            case 6:
                $chamado_pendente = SuporteClass::getQntChamadosMaster();
                $html = "<p class=\"box-p-grande\">{$chamado_pendente} Iten(s)</p>
                        <p class=\"box-p-medio\">Chamados pendentes</p>";
                break;
            case 24:
                //juridico
                $audiencia_hoje = ProcessosJuridicosClass::getAudienciaHoje();
                $audiencia_amanha = ProcessosJuridicosClass::getAudienciaAmanha();
                $audiencia_fim_mes = ProcessosJuridicosClass::getAudienciaMes();
                $total_audiencia = $audiencia_hoje + $audiencia_amanha + $audiencia_fim_mes;
                
                $html = "<p class=\"box-p-grande\">{$total_audiencia} Audiências(s)</p>
                        <p class=\"box-p-medio\">este mês</p>
                        <p>{$audiencia_hoje} hoje</p>
                        <p>{$audiencia_amanha} amanhã</p>
                        <p>{$audiencia_fim_mes} até o final do mês</p>";
                break;
            case 35:
                $html = "<p class=\"box-p-grande\">0 Iten(s)</p>";
                break;
            case 36:
                
                //prest. contas
                $hoje = date('d/m/Y');        
                $data1 = date('Y-m').'-01';
                $dec = somarUteis($data1,15);

                if($hoje < $dec){
                    $faltam = $dec - $hoje - 1;
                    $desc_dt = "faltam";
                }elseif ($hoje > $dec) {
                    $faltam = $hoje - $dec;
                    $desc_dt = "em atraso";
                }elseif ($hoje == $dec) {
                    $faltam = 0;
                    $desc_dt = "vence hoje";
                }
                
                $html = "<p class=\"box-p-grande\">{$faltam} Dia(s)</p>
                        <p class=\"box-p-medio\">{$desc_dt}</p>";
                break;
        }
        return $html;
    }

}

?>