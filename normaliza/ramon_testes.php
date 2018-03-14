<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
    exit;
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/FuncionarioClass.php");
include("../classes/FeriadosClass.php");
include("../classes/SuporteClass.php");

$usuario = carregaUsuario();

$regioes = getRegioes();
$masters = getMasters();

$regiaoSelected = $regioes[$usuario['id_regiao']];
$masterSelected = $masters[$usuario['id_master']];

unset($regioes[$usuario['id_regiao']]);
unset($regioes['-1']);

unset($masters[$usuario['id_master']]);
unset($masters['-1']);

$botoes = new BotoesClass("../");
$classDefaults = $botoes->classModulos;
$modulos = $botoes->getModulos();

$funcionariosf71 = montaQuery("funcionario", "id_funcionario,nome,nome1", "nome LIKE '%f71%' and status_reg = 1");
echo "<pre>";
print_r($funcionariosf71);
echo "</pre>";

$funcPagina = array();

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <div class="navbar navbar-default navbar-fixed-top hide">
            <div class="container">

                <div class="navbar-header top-header">

                    <a href="index.php" class="navbar-brand">
                        <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" width="130" height="80" class="logo-border">
                    </a>

                    <div class="header-info">
                        <p class="text-primary">Olá <strong><?php echo $usuario['nome1'] ?></strong></p>
                        <p class="text-primary">Data: <?php echo date("d/m/Y") ?></p>
                    </div>

                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="navbar-collapse collapse navbar-right" id="navbar-main">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-regioes">Regiões <span class="caret"></span></a>
                            <ul class="dropdown-menu" aria-labelledby="drp-regioes">
                                <li><a href="javascript:;" id="regiao-ativa" data-key="<?php echo $usuario['id_regiao'] ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $regiaoSelected; ?></a></li>
                                <?php echo (count($regioes) > 0) ? '<li class="divider"></li>' : ""; ?>
                                <?php foreach ($regioes as $k => $regiao) { ?>
                                    <li><a href="javascript:;" data-key="<?php echo $k ?>" class="bt-troca-regiao"><?php echo $regiao ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-master">Empresa <span class="caret"></span></a>
                            <ul class="dropdown-menu" aria-labelledby="drp-master">
                                <li><a href="javascript:;" id="master-ativo" data-key="<?php echo $usuario['id_master'] ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $masterSelected; ?></a></li>
                                <?php echo (count($masters) > 0) ? '<li class="divider"></li>' : ""; ?>
                                <?php foreach ($masters as $k => $regiao) { ?>
                                    <li><a href="javascript:;" data-key="<?php echo $k ?>" class="bt-troca-master"><?php echo $regiao ?></a></li>
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

        <div class="container container-top">
            <div id="menu-padrao" class="hide">
                <div class="row">
                    <?php
                    $contMod = 1;
                    foreach ($modulos as $k => $modulo) {
                        ?>
                        <div class="col-lg-4">
                            <div class="box-metro <?php echo $classDefaults[$k] ?>">
                                <div class="box-content">
                                    <a href="javascript:;" data-key="<?php echo $k ?>" class="bt-box"><div class="box-titulo"><?php echo $modulo ?></div></a>
                                    <div class="box-info">
                                        <?php echo $botoes->getHtmlBoxInfo($k); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?
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
                    <div class="row" name="modulo_<?php echo $k ?>">
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

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>
