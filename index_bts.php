<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}
//session_start();

include("conn.php");
include("wfunction.php");
include("classes/BotoesClass.php");
include("classes/FuncionarioClass.php");
include("classes/FeriadosClass.php");
include("classes/SuporteClass.php");
include("classes/SaidaClass.php");
include("classes/ObrigacoesClass.php");
include("classes/ProcessosJuridicosClass.php");
include('classes_permissoes/acoes.class.php');

$ACOES = new Acoes();

$usuario = carregaUsuario();

$regioes = getRegioes();
$masters = getMasters();

$regiaoSelected = $regioes[$usuario['id_regiao']];
$masterSelected = $masters[$usuario['id_master']];

unset($regioes[$usuario['id_regiao']]);
unset($regioes['-1']);

unset($masters[$usuario['id_master']]);
unset($masters['-1']);

$botoes = new BotoesClass();
$classDefaults = $botoes->classModulos;
$modulos = $botoes->getModulos();

//SESSION PARA EMAIL
$objFun = new FuncionarioClass();
$dadosEmail = $objFun->getDadosEmail();
$_SESSION['email']    = $dadosEmail['email'];
$_SESSION['password'] = $dadosEmail['senha'];
$_SESSION['webmail_host'] = $dadosEmail['webmail_host'];

$dia = date('d');
$diaSemana = date('N');

//aniversariante de hoje
$niver_hj = $objFun->getAniversariantesHoje();
$tot_niver = count($niver_hj);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="favicon.png" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                
                <div class="navbar-header top-header">
                    <a href="index.php" class="navbar-brand">
                        <img src="imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" width="130" height="80" class="logo-border">
                    </a>
                    
                    <div class="header-info">
                        <p class="text-primary">Olá <strong><?php echo $usuario['nome1'] ?></strong></p>
                        <p class="text-primary">Data: <?php echo date("d/m/Y") ?></p>
                        <input type="hidden" name="h-email" id="h-email" value="<?php echo $dadosEmail['email']; ?>" />
                    </div>
                    
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                
                <?php if ($ACOES->verifica_permissoes(94)) { ?>
                <div class="navbar-header top-header">
                    <a href="lib.php" class="btn btn-xs btn-success btn-outline m_10"><span class="fa fa-book"></span>&nbsp;&nbsp;Biblioteca</a>                    
                </div>
                <?php } ?>                                
                
<!--                <div class="navbar-right text-primary">
                    <div class="btn fa fa-chevron-down"></div>
                </div>-->
                
                <div class="navbar-collapse collapse navbar-right" id="navbar-main">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-regioes">Regiões <span class="caret"></span></a>
                            <ul class="dropdown-menu" aria-labelledby="drp-regioes">
                                <li><a href="javascript:;" id="regiao-ativa" data-key="<?php echo $usuario['id_regiao'] ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $regiaoSelected; ?></a></li>
                                <?php echo (count($regioes) > 0) ? '<li class="divider"></li>' : ""; ?>
                                <?php foreach ($regioes as $k => $regiao) { ?>
                                    <li><a href="javascript:;" data-key="<?php echo $k ?>" class="bt-troca-regiao" data-base-url=""><?php echo $regiao ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-master">Empresa <span class="caret"></span></a>
                            <ul class="dropdown-menu" aria-labelledby="drp-master">
                                <li><a href="javascript:;" id="master-ativo" data-key="<?php echo $usuario['id_master'] ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $masterSelected; ?></a></li>
                                <?php echo (count($masters) > 0) ? '<li class="divider"></li>' : ""; ?>
                                <?php foreach ($masters as $k => $regiao) { ?>
                                    <li><a href="javascript:;" data-key="<?php echo $k ?>" class="bt-troca-master" data-base-url=""><?php echo $regiao ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li>
                            <a href="logof.php">Sair</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!--traz key do menu, para fazer a interação breadcrumb-->
        <input type="hidden" name="home" id="home" value="<?php echo $_REQUEST['home']; ?>" />
        
        <div class="container container-top">
            <div id="menu-padrao">
                
                <!--Provisório, vai vir td do BD-->
                <?php if($tot_niver > 0){ ?>
                <div class="row">
                    <div class="col-lg-12 col-md-6">
                        <div class="panel border-niver">
                            <div class="panel-heading" id="back_niver">
                                <div class="row text-default">
                                    <div class="col-xs-12 text-left">
                                        <div class="huge"><?php echo date('d/m/Y'); ?></div>
                                        <?php if($tot_niver == 1){ ?>
                                        <div>Parabéns a(o) funcionário(a): 
                                            <?php foreach ($niver_hj as $funchj){ ?>
                                            <strong class="text-lg initialism"><?php echo $funchj['nome1']; ?></strong>
                                            <?php } ?>
                                        </div>
                                        <?php }else{ ?>
                                        <div>Parabéns aos funcionários:
                                            <?php foreach ($niver_hj as $funchj){ ?>
                                            <strong class="text-lg initialism"> <?php echo $funchj['nome1']; ?> | </strong>  
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                
                <div class="row">
                    <?php
                    $contMod = 1;
                    foreach ($modulos as $k => $modulo) {                                                
                        
                        ?>
                        <div class="col-lg-4">
                            <div class="box-metro <?php echo $classDefaults[$k] ?>">
                                <div class="box-content">
                                    <a href="javascript:;" data-key="<?php echo $k ?>" class="bt-box"><div class="box-titulo"><?php echo $modulo ?></div></a>
                                    <?php if($k==1){?>
                                    <div class="box-principal-now"><span><?php echo diasSemanaArray($diaSemana) ?></span><br/><div><?php echo $dia ?></div></div>
                                    <?php }?>
                                    <div class="box-info">
                                        <?php echo $botoes->getHtmlBoxInfo($k); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        echo ($contMod++ % 3 == 0) ? "</div><div class=\"row\">" : "";
                    }
                    ?>
                </div>
            </div>
            
            <div id="low-menu" class="hide">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-pills nav-pers"> 
                            <?php foreach ($modulos as $k => $modulo) { ?>
                                <li><a href="javascript:;" class="bt-box <?php echo $classDefaults[$k] ?>-min" data-key="<?php echo $k ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $modulo ?>"><?php echo $botoes->iconsModulos[$k] ?></a></li>
                            <?php } ?>
                            <li><button type="button" id="volta-principal" class="btn btn-link">Voltar</button></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="conteudo_fixo">
                <?php foreach ($modulos as $k => $modulo) { ?>
                    <div class="row hide" name="modulo_<?php echo $k ?>">
                        <div class="page-header <?php echo $classDefaults[$k] ?>-header"><h2><?php echo $botoes->iconsModulos[$k] . " - " . $modulo ?></h2></div>
                        <div class="detalhes-modulo">
                            <?php echo $botoes->getHtmlBotoesModulo($k) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>                        
            
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>
        
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/tooltip.js"></script>
        <script src="resources/js/main_bts.js"></script>
        <script src="js/global.js"></script>
        
        <script>
            $(function(){                                
                verifica();
                var timer;
                timer = setInterval(verifica(), 1000);                                
            });
            
            var verifica = function() {
                    $.post('http://f71lagos.com/intranet/webmail/inc/boxcount.php',{email:$("#h-email").val()},function(data){
                        data = data[0];
                        $("#email-unread").html(data.unread);
                    },'json');
                }
        </script>
        
    </body>
</html>