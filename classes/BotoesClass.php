<?php

/**
 * Description of BotoesClass
 *
 * @author Ramon Lima
 */
class BotoesClass
{

    public $classModulos;
    public $iconsModulos;
    private $defaultPath;
    private $fullPath;
    private $fmail = true;

    public function __construct($path = null, $fullPath = null)
    {
        $this->classModulos = array(
            "1" => "box-principal",
            "2" => "box-admin",
            "3" => "box-rh",
            "4" => "box-financeiro",
            //"5"=>"box-contabil",
            "6" => "box-sistema",
            "24" => "box-juridico",
            "35" => "box-compras",
            "36" => "box-contas",
            "38" => "box-contabil",
            "40" => "box-acesso",
            "41" => "box-estoque",
            "42" => "box-fiscal"
        );

        $this->iconsModulos = array(
            "1" => "<span class=\"glyphicon glyphicon-home\"></span>",
            "2" => "<span class=\"glyphicon glyphicon-cog\"></span>",
            "3" => "<span class=\"fa fa-users\"></span>",
            "4" => "<span class=\"glyphicon glyphicon-usd\"></span>",
            //"5"=>"<span class=\"glyphicon glyphicon-dashboard\"></span>",
            "6" => "<span class=\"glyphicon glyphicon-phone\"></span>",
            "24" => "<span class=\"glyphicon glyphicon-briefcase\"></span>",
            "35" => "<span class=\"glyphicon glyphicon-shopping-cart\"></span>",
            "36" => "<span class=\"glyphicon glyphicon-list-alt\"></span>",
            "38" => "<span class=\"fa fa-bar-chart\"></span>",
            "40" => "<span class=\"fa fa-lock\"></span>",
            "41" => "<span class=\"fa fa-archive\"></span>",
            "42" => "<span class=\"fa fa-arrows-alt\"></span>"
        );

        $this->defaultPath = $path;
        $this->fullPath = $fullPath;
    }

    /**
     * Busca os módulos que o usuário logado tem permissão.
     * Vai na tabela de permissões por botão, busca todas as permissões e agrupa pelo Módulo
     * Trazendo apenas o nescessário para o saber quis modulos a pessoa tem acesso
     * @param type $user não é obrigatório, pegando o cookie
     * @return array
     */
    public function getModulos($user = null, $pagina = 1)
    {
        if ($user === null)
        {
            $usuario = carregaUsuario();
        } else
        {
            $usuario['id_funcionario'] = $user;
        }

        $qrModulos = "SELECT C.botoes_menu_id,C.botoes_menu_nome FROM botoes_assoc AS A
                        INNER JOIN botoes AS B ON (A.botoes_id = B.botoes_id)
                        INNER JOIN botoes_menu AS C ON (B.botoes_menu = C.botoes_menu_id)
                        WHERE A.id_funcionario = {$usuario['id_funcionario']} AND C.botoes_pagina = {$pagina} 
                        GROUP BY B.botoes_menu";
        $rsModulos = mysql_query($qrModulos);
        $modulos = array();
        while ($row = mysql_fetch_assoc($rsModulos))
        {
            $modulos[$row['botoes_menu_id']] = $row['botoes_menu_nome'];
        }
        return $modulos;
    }

    public function getBotoesModulo($key, $user = null)
    {
        if ($user === null)
        {
            $usuario = carregaUsuario();
        } else
        {
            $usuario['id_funcionario'] = $user;
        }

        $qrModulos = "SELECT B.botoes_nome,B.botoes_id,B.botoes_link,B.botoes_img,B.nova_url,B.novo_ico,C.botoes_pagina,B.botoes_link,B.nova_aba FROM botoes_assoc AS A
                    INNER JOIN botoes AS B ON (A.botoes_id = B.botoes_id)
                    INNER JOIN botoes_menu AS C ON (B.botoes_menu = C.botoes_menu_id)
                    WHERE A.id_funcionario = {$usuario['id_funcionario']} AND B.botoes_menu = {$key} AND B.status = 1 ORDER BY B.botoes_nome";
        $rsModulos = mysql_query($qrModulos);
        $modulos = array();
        while ($row = mysql_fetch_assoc($rsModulos))
        {
            $modulos[] = $row;
        }
        return $modulos;
    }

    public function getBotoesMenuModulo($key)
    {
        return $this->getModulos(null, $key);
    }

    public function getHtmlBotoesModulo($key, $keyMaster = null)
    {
        $btsModulo = $this->getBotoesModulo($key);
        /* A principal mostra itens diferentes */

        if (array_key_exists($key, $this->classModulos))
        {
            $classModulos = $this->classModulos[$key];
            $iconsModulos = $this->iconsModulos[$key];
        } else
        {
            $classModulos = $this->classModulos[$keyMaster];
        }

        if ($key == 1)
        {
            if ($this->fmail)
            {
                if (trim($_SESSION['password']) == "")
                {
                    $id_user = $_COOKIE['logado'];
                    $sql = "select * from funcionario f inner join funcionario_email_assoc e on f.id_funcionario = e.id_funcionario where f.id_funcionario = '$id_user' and e.email <> ''";

                    //var_dump($sql);
                    $result_user = mysql_query($sql);
                    //$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
                    $row_user = mysql_fetch_array($result_user);

                    $_SESSION['email'] = trim($row_user['email']);
                    $_SESSION['password'] = trim($row_user['senha']);
                    $_SESSION['flavor'] = $row_user['flavor'];
                }

                $param = $_SESSION['webmail_host'] . "&" . $_SESSION['email'];

                $host_mail = $_SESSION['webmail_host'] == 'mail.f71.com.br' ? '2' : '';

                $param.= "&" . urlencode($_SESSION['password']) . "&" . $_SESSION['hostref'];
                $param.= "&" . $_SESSION['hostname'] . "&" . $_SESSION['id_regiao'];
                $param.= "&" . $_SESSION['id_master'] . "&" . $_SESSION['id_user'];
                $param.= "&" . $_SESSION['flavor'];
                $param = base64_encode($param);

                // Em ambiente de produção
                if (trim($_SESSION['email']) !== '')
                {
                    if ($_SESSION['flavor'] === 'RAIN')
                    {
                        $html = '<div>
                                <iframe id="iframemail" src="http://netsorrindo.com/rainloop' . $host_mail . '/index2.php?box=Inbox&boxfull=INBOX&param=' . $param . '" width="100%" height="700" frameborder="0"></iframe>
                            </div>';
                    }
                }
                // Em ambiente de desenvolvimento
                /*
                  if($_SESSION['flavor'] === 'RAIN')
                  {
                  $html = '<div>
                  <iframe id="iframemail" src="http://localhost/rainloop/index2.php?box=Inbox&boxfull=INBOX&param=' . $param . '" width="100%" height="700" frameborder="0"></iframe>
                  </div>';
                  }else
                  {
                  $html = '<div>
                  <iframe id="iframemail" src="http://des.lagos.net/intranet/webmail/index.php?box=Inbox&boxfull=INBOX&param=' . $param . '" width="100%" height="700" frameborder="0"></iframe>
                  </div>';
                  }
                 * 
                 */
            } else
            {
                $html = '';
            }

            $html .= "<div class=\"col-lg-4\">
					 <div class=\"bs-glyphicons\">
					 <div class=\"bs-glyphicons-list {$classModulos}-list\">";
        } elseif ($key == 24)
        {

            $html .= " 
                    <script>
        
           
           $(document).ready(function(){                        
            
        });
           
           </script>
           
                <div class='col-md-6 col-sm-12 col-xs-12 text-center'>
                   
                        <!-- Responsive calendar - START -->
                               <div class='responsive-calendar'>
                               <div class='controls '>
                                   <a class='pull-left' data-go='prev'><div  id='voltar' class='btn btn-success'>Voltar</div></a>
                                   <h4><span data-head-year></span>&nbsp;<span data-head-month></span></h4>
                                   <a class='pull-right' data-go='next'><div id='ir' class='btn btn-success'>Próximo</div></a>
                                   
                               
                               <hr/>
                              <!-- <div style='text-align:center;'>
                                    <select id='mes' class='col-xs-12 col-sm-12'>
                                        <option value=''>Mês</option>
                                        <option value='01'>Janeiro</option>
                                        <option value='02'>Fevereiro</option>
                                        <option value='03'>Março</option>
                                        <option value='04'>Abril</option>
                                        <option value='05'>Maio</option>
                                        <option value='06'>Junho</option>
                                        <option value='07'>Julho</option>
                                        <option value='08'>Agosto</option>
                                        <option value='09'>Setembro</option>
                                        <option value='10'>Outubro</option>
                                        <option value='11'>Novembro</option>
                                        <option value='12'>Dezembro</option>
                                    </select>
                                    <select id ='ano' class='col-xs-12 col-sm-12'>
                                        <option value=''>Ano</option>";
            for ($i = date('Y') + 2; $i >= 2000; $i--)
            {
                $html .= "<option value='$i'>" . $i . "</option>";
            }
            $html .= " 
                                    </select><br>
                                </div> -->   
                                
                               <div class='day-headers'>
                                 <div class='day header'>S</div>
                                 <div class='day header'>T</div>
                                 <div class='day header'>Q</div>
                                 <div class='day header'>Q</div>
                                 <div class='day header'>S</div>
                                 <div class='day header'>S</div>
                                 <div class='day header'>D</div>
                               </div>
                               <div id='data' class='days data' data-group='days'>

                               </div>
                             </div> 
                             </div>
                             <!-- Responsive calendar - END -->
                    
                     
                </div>
                   
                    
              
                <div class='col-md-6 col-sm-12 col-xs-12 text-center'>
                    ";
            $html .= "
                        <div class=\"bs-glyphicons\">
                        <div class=\"bs-glyphicons-list {$classModulos}-list\">";
        } else
        {
            $html = "<div class=\"bs-glyphicons\">
                     <div class=\"{$classModulos}-list\">";
        }

        foreach ($btsModulo as $bt)
        {

            if (!empty($this->fullPath))
            {
                $url = "http://" . $this->fullPath . $bt['botoes_link'];
            } else
            {
                $url = $bt['botoes_link'];
            }

            $novaAba = ($bt['nova_aba'] == 1) ? "target='_blank'" : "";
            $iconsModulos = "<img src='{$this->defaultPath}{$bt['botoes_img']}' />";

            if (!empty($bt['nova_url']))
            {
                $url = "http://" . $this->fullPath . $bt['nova_url'];
            }

            if ($key == 1 or $key == 24)
            {
                if (!empty($bt['novo_ico']))
                {
                    $html .= "
                    <div class='col-md-4 col-sm-4 col-xs-6 text-center'>
                        <a href='{$url}' $novaAba class='text-center no-padding-vr'>
                            <div class='novo_ico thumbnail' style='height: 110px;'>
                                <div class='fa {$bt['novo_ico']}'></div>
                                <div class='display-table-cem'>
                                    <div class='text-bold text-center valign-middle vcenter text-uppercase' style='height: 50px;'>{$bt['botoes_nome']}</div>
                                </div>
                            </div>
                        </a>
                    </div>";
                } else
                {
                    $html .= "<a href='{$url}' $novaAba class=\"bot_nv col-lg-4 col-md-4 col-sm-4 col-xs-6\">
                                {$iconsModulos}
                                <span class=\"glyphicon-class\">{$bt['botoes_nome']}</span>
                          </a>";
                }
            } else
            {
                if (!empty($bt['novo_ico']))
                {
                    $html .= "
                    <div class='col-lg-2 col-md-4 col-sm-4 col-xs-6 text-center'>
                        <a href='{$url}' $novaAba class='text-center no-padding-vr'>
                            <div class='novo_ico thumbnail' style='height: 110px;'>
                                <div class='fa {$bt['novo_ico']}'></div>
                                <div class='display-table-cem'>
                                    <div class='text-bold text-center valign-middle vcenter text-uppercase' style='height: 50px;'>{$bt['botoes_nome']}</div>
                                </div>
                            </div>
                        </a>
                    </div>";
                } else
                {
                    $html .= "<a href='{$url}' $novaAba class=\"bot_nv col-lg-2 col-md-4 col-sm-4 col-xs-6\">
                                {$iconsModulos} 
                                <span class=\"glyphicon-class\">{$bt['botoes_nome']}</span>
                          </a>";
                }
            }
        }

        if ($key == 24)
        {

            $html .= "</div></div></div>
        
            ";
        } else
        {

            $html .= "</div></div>";
        }

        /* A principal mostra itens diferentes */
        if ($key == 1)
        {
            $html .= "</div><div class=\"col-lg-4\"><h3>Aniversariantes</h3><p>(Usuários do Sistema)</p><div class=\"list-group\">";
            $funcionario = new FuncionarioClass();
            $feriados = new FeriadosClass();
            $nivers = $funcionario->getAniversariantes();
            foreach ($nivers as $func)
            {
                $html .= "<div class=\"list-group-item\">
                            <span class=\"pull-right glyphicon glyphicon-gift\"></span>
                            <p class=\"list-group-item-text\"><strong>{$func['data']}</strong> - " . normalizaNomeToView($func['nome1']) . "</p>
                          </div>";
            }
            $html .= "</div>
                
                <a href='aniversariantes_projeto.php' class='btn btn-primary'>Aniversariantes do Projeto</a></div>";

            $html .= "<div class=\"col-lg-4\"><h3>Feriados</h3><div class=\"list-group\">";
            $rsferiados = $feriados->getFeriados();
            foreach ($rsferiados as $feriado)
            {
                $html .= "<div class=\"list-group-item\">
                            <span class=\"pull-right fa fa-calendar-o\"></span>
                            <p class=\"list-group-item-text\"><strong>{$feriado['dataBr']}</strong> - {$feriado['nome']}</p>
                          </div>";
            }
            $html .= "</div></div>";
        } elseif ($key == 9 AND date('Y') == 2015)
        { //em 2016 não precisamos mais avisar a nova organização
            $html .= "<div class=\"col-lg-12\"><div class=\"alert alert-dismissable alert-success\"><p><strong>Atenção</strong>! Para uma melhor experiencia em organização, alteramos o local de alguns botões. Agora para visualizar relatórios e impostos foi criada uma nova ABA chamada <strong>Relatórios e Impostos</strong>.</p></div></div>";
        } elseif ($key == 15 AND date('Y') == 2015)
        { //em 2016 não precisamos mais avisar a nova organização
            $html .= "<div class=\"col-lg-12\"><div class=\"alert alert-dismissable alert-danger\"><p><strong>Atenção!</strong> Essa seção será movida para dentro do módulo <strong>COMPRAS E CONTRATAÇÃO</strong>.</p></div></div>";
        }
        return $html;
    }

    public function getHtmlBoxInfo($key, $user = null)
    {
        if ($user === null)
        {
            $usuario = carregaUsuario();
        } else
        {
            $usuario['id_funcionario'] = $user;
        }

        $html = "";
        switch ($key)
        {
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
                        <p>{$audiencia_fim_mes} até o final do mês</p><!--teste--> ";


                break;
            case 35:
                //compras
                $chamadosCompras = ComprasChamados::getCountChamados();
                $html = "<p class=\"box-p-grande\">Chamados(s)</p>
                         <p class=\"box-p-medio\">{$chamadosCompras} aberto(s)</p>";
                break;
            case 36:

                //prest. contas
                $hoje = date('d/m/Y');
                $data1 = date('Y-m') . '-01';
                $dec = somarUteis($data1, 15);

                if ($hoje < $dec)
                {
                    $faltam = $dec - $hoje - 1;
                    $desc_dt = "faltam";
                } elseif ($hoje > $dec)
                {
                    $faltam = $hoje - $dec;
                    $desc_dt = "em atraso";
                } elseif ($hoje == $dec)
                {
                    $faltam = 0;
                    $desc_dt = "vence hoje";
                }

                $html = "<p class=\"box-p-grande\">{$faltam} Dia(s)</p>
                        <p class=\"box-p-medio\">{$desc_dt}</p>";
                break;
            case 40:

                $html = "<p class=\"box-p-grande\">Escala</p>
                        <p class=\"box-p-medio\">Total de maquinas </p>";
                break;
            case 41:

                $html = "<p class=\"box-p-grande\">Pedidos</p>
                        <p class=\"box-p-medio\">Total de pedidos </p>";
                break;
            case 42:

                $html = "<p class=\"box-p-grande\">Pendentes</p>
                        <p class=\"box-p-medio\">Total de Pendencias</p>";
                break;
        }
        return $html;
    }

}

?>