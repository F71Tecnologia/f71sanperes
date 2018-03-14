<?php
if(!defined('ROOT_DIR')) DEFINE("ROOT_DIR", str_replace('public_html/intranet', 'framework/', __DIR__));
if(!defined('ROOT_OLD_DIR')) DEFINE("ROOT_OLD_DIR", str_replace('framework', 'public_html', ROOT_DIR));
include(ROOT_DIR.'/lib/const.php');
//$class = str_replace('/', '\\',$_REQUEST['class']);
$class = $_REQUEST['class'];
if(isset($class))
{
    //Loader do Composer
    //require $path.'vendor/autoload.php';
    //echo $path.$_REQUEST['class'].'.class.php'."<br/>\n";
    if($class=='construct') {
        include(ROOT_LIB.$class.'.class.php');
    }
    else {
        include(ROOT_APP_CONTROLLER.$class.'.class.php');
    }
    exit();
}

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
include("classes/ComprasChamados.php");
include("classes/ProcessosJuridicosClass.php");
include('classes_permissoes/acoes.class.php');
include('./classes/ProcessoJuridicoClass2.php');

$objetojuridico = new ProcessoJuridicoClass();
$arrayProcessos2 = $objetojuridico -> getProcessCalendario();


//print_r($arrayProcessos2);
//exit;
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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$sqlPrestadorSemAssociassao = "
SELECT B.nome, B.id_projeto, COUNT(*) qtd
FROM prestadorservico A
INNER JOIN projeto B ON (A.id_projeto = B.id_projeto AND B.id_master = {$usuario['id_master']})
WHERE 
(SELECT COUNT(*) FROM contabil_contas_assoc_prestador WHERE id_prestador = A.id_prestador AND STATUS = 1) = 0
AND A.prestador_tipo = 4 AND (A.encerrado_em > CURDATE() OR A.encerrado_em IS NULL)
GROUP BY A.id_projeto";
$qryPrestadorSemAssociassao = mysql_query($sqlPrestadorSemAssociassao);
while($rowPrestadorSemAssociassao = mysql_fetch_assoc($qryPrestadorSemAssociassao)){
    $arrayPrestadorSemAssociassao[$rowPrestadorSemAssociassao['id_projeto']] = $rowPrestadorSemAssociassao;
}

$acessoBtnPrestadorSemAssociassao = mysql_num_rows(mysql_query("SELECT botoes_assoc_id FROM botoes_assoc A WHERE A.id_funcionario = {$usuario['id_funcionario']} AND A.botoes_id = 260;"));

//$botoes = new BotoesClass();
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$classDefaults = $botoes->classModulos;
$modulos = $botoes->getModulos();

//SESSION PARA EMAIL
$objFun = new FuncionarioClass();
$dadosEmail = $objFun->getDadosEmail();
$_SESSION['email']    = $dadosEmail['email'];
$_SESSION['password'] = $dadosEmail['senha'];
$_SESSION['webmail_host'] = $dadosEmail['webmail_host'];
$_SESSION['flavor'] = $dadosEmail['flavor'];

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
        <link href='classes/responsive-calendar/0.9/css/responsive-calendar.css' rel='stylesheet'>
        <link rel='stylesheet' href='resources/css/bootstrap-dialog.min.css'>
    </head>
    <body>
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                
                <div class="navbar-header top-header">
                    <a href="index.php" class="navbar-brand">
                        <img src="imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" width="130" height="80" class="logo-border">
                    </a>
                    
                    <div class="header-info">
                        <p class="text-primary" id='bug'>Ol� <strong><?php echo $usuario['nome1'] ?></strong></p>
                        <p class="text-primary">Data: <?php echo date("d/m/Y") ?></p>
                        <?php if ($ACOES->verifica_permissoes(94)) { ?> <a href="lib.php" class="btn btn-xs btn-success" target="_blank"><span class="fa fa-book"></span> Biblioteca</a> <?php } ?>
                        <input type="hidden" name="h-email" id="h-email" value="<?php echo $dadosEmail['email']; ?>" />
                    </div>
                    
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                
<!--                <div class="navbar-right text-primary">
                    <div class="btn fa fa-chevron-down"></div>
                </div>-->
                
                <div class="navbar-collapse collapse navbar-right" id="navbar-main">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-regioes">Regi�es <span class="caret"></span></a>
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
        
        <!--traz key do menu, para fazer a intera��o breadcrumb-->
        <input type="hidden" name="home" id="home" value="<?php echo $_REQUEST['home']; ?>" />
        
        <div class="container container-top">
            <?php 
            //$array_pegadinha = array(158,179,9,257,202,260,259,199,256,258,87,255,5,71);
            $array_pegadinha = array(0);
            if(in_array($_COOKIE['logado'], $array_pegadinha)) { ?>
            <div id="menu-padrao">
                
                <audio controls loop autoplay style="display: none;" id='myVideo'>
                    <source src="http://patrick.welfringer.lu/sos/waves/starwars/jabba_n_crumb_laugh.wav" type="audio/wav">
                    Your browser does not support the audio element.
                  </audio>
                
                <div class="col-lg-12 center"> <img src='imagens/caveira.gif' class="center" /> </div>
                
            </div>
            <?php }else{ ?>
            <div id="menu-padrao">
                
                <?php if($tot_niver > 0){ ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel border-niver">
                            <div class="panel-heading" id="back_niver">
                                <div class="row text-default">
                                    <div class="col-xs-12 text-left">
                                        <div class="huge"><?php echo date('d/m/Y'); ?></div>
                                        <?php if($tot_niver == 1){ ?>
                                        <div>Parab�ns a(o) funcion�rio(a): 
                                            <?php foreach ($niver_hj as $funchj){ ?>
                                            <strong class="text-lg initialism"><?php echo $funchj['nome1']; ?></strong>
                                            <?php } ?>
                                        </div>
                                        <?php }else{ ?>
                                        <div>Parab�ns aos funcion�rios:
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
            
            <?php } ?>
            
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
                            <?php if($k == 38 && count($arrayPrestadorSemAssociassao) > 0 && $acessoBtnPrestadorSemAssociassao) { ?>
<!--                            <div class="alert alert-warning">
                                <label>Prestadores sem associa��o nos projetos:</label>
                                <ul>
                                <?php foreach ($arrayPrestadorSemAssociassao as $key => $value) { ?>
                                    <li><?= $value['nome'] ?> - <?= $value['qtd'] ?></li>
                                <?php } ?>
                                </ul>
                            </div>-->
                            <?php } ?>
                            <?php echo $botoes->getHtmlBotoesModulo($k) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>                        
            
            <?php include("template/footer.php") ?>
           
            <!--footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer-->
        </div>
        
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src='js/jquery-ui-1.9.2.custom.min.js'></script>
        <script src='resources/js/bootstrap-dialog.min.js'></script>
        <script src="resources/js/tooltip.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src='classes/responsive-calendar/0.9/js/responsive-calendar.js'></script>
        
        
        <script>
            $(function(){
                verifica();
                var timer;
                timer = setInterval(verifica(), 1000);
                var arr = new Array(); 
                
                                
                $("#bug").click(function(){
                    pauseVid();
                });
                $('.responsive-calendar').responsiveCalendar({
                    time: "",
                    events: {
                       <?php 
                        foreach($arrayProcessos2 as $key => $value){
                            echo "'$key': {'number': ".count($value).", },"; 
                        }
                       ?> 
                    }, 

                    translateMonths: ["Janeiro", "Fevereiro", "Mar�o", "Abril", "Maio", "junho", "Jullho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                    onMonthChange: function(){ //Quando o m�s(bot�o de PREV for clidado) mudado.
                        var mescorrente = $(this)[0].currentMonth + 1; //Variavel reagata mes corrente do calendario soma mais um ao mes para dezembro ser 12 e n�o 11.
                        var anocorrente = $(this)[0].currentYear; //Variavel reagata ano corrente do calendario 
                        var dataCalendario = new Date(mescorrente + "/" + "01" + "/" + anocorrente); //Variavel onde cria uma nova data juntando mes do calendario, mais o primeiro dia e o ano do calendario.
                        var data = new Date(); //Variavel criando uma nova data
                        var newData  = new Date(data.getTime() - 7948800000); //Variavel onde pega o tempo real( data e mes e ano) e diminui 3 meses em milisegundos
                        
                        if(dataCalendario < newData){ //se a data do calendario for menor que a data real
                            $("#voltar").hide(); //esconde o botao de previous
                        }else{ //senao
                            $("#voltar").show(); //volta com o bot�o
                        }   
                    },
                    onActiveDayClick: function() { //Ao clicar nos dias abre o Popup com informa��es dos procesos.
                        var dia = $(this).data('day');
                        var mes = $(this).data('month');
                        var ano = $(this).data('year');

                        $.ajax({url: "/intranet/classes/responsive-calendar/0.9/example/popup.php?dia="+dia+"&mes="+mes+"&ano="+ano, success: function(result){
                            
                            new BootstrapDialog({  
                                nl2br: false,
                                size: BootstrapDialog.SIZE_WIDE,
                                type: 'type-success',
                                title: '',
                                message: result,
                                closable: true,
                                buttons:
                                [{
                                    label: 'Fechar',
                                    action: function (dialog) {
                                        dialog.close();
                                        //window.location.reload();
                                    }
                                }]
                            }).open();
                        }});
                    }
                });        
                $("body").on("click","#realizado",function(){ //Quando clicado em Realizar inicia est� confirma��o
                    var id = $(this).data("realizado");
    //            return confirm("Definir como realizado?");
                   bootConfirm("DESEJA DEFINIR COMO REALIZADO?","VERIFICANDO",function(data){
                       if(data == true){
                       location.href="/intranet/gestao_juridica/action.realizado.php?id=" + id;
                    }

                   },"success");

                });
                
                $("body").on("change", "#mes, #ano", function(){ //Setando o M�s e o Ano manualmente do calendario de processos
                    var mes = $("#mes").val();
                    var ano = $("#ano").val();
                                        
                    if(mes != "" && ano != "" ){
                        $('.responsive-calendar').responsiveCalendar(ano+"-"+mes);
                    }   
                });
                
            });

            function pauseVid(){ 
                var vid = document.getElementById("myVideo");
                vid.pause(); 
            }
            
            var verifica = function() {
		$.post('http://www.netsorrindo.com/intranet_2014_11_19/webmail/inc/boxcount.php',{email:$("#h-email").val()},function(data){
                        data = data[0];
                        $("#email-unread").html(data.unread);
                    },'json');
            }
        </script>
        
    </body>
</html>
