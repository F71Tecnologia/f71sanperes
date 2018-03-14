<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

header("Content-Type:text/html; charset=ISO-8859-1", true);
include('../conn.php');
include('../classes/funcionario.php');
include('../wfunction.php');

$usuario = carregaUsuario();

////////////////////////////////////////////////////////////////////////////
/////////////////////// gravando log de relatorios /////////////////////////
////////////////////////////////////////////////////////////////////////////
if (isset($_REQUEST['par']) && $_REQUEST['par'] == TRUE) {
    $url = explode("?", $_REQUEST['url']);
    $date = date("Y-m-d H:i:s");
    $idUsuario = $_REQUEST['id'];
    $query = "INSERT INTO relatorios_log (nome_arquivo,data_acesso,id_usuario) VALUES ('$url[0]','$date','$idUsuario');";
    echo $query;
    $result = mysql_query($query);
    echo ($result) ? TRUE : FALSE;
} else {
    $Fun = new funcionario();
    $Fun->MostraUser(0);
    $Master = $Fun->id_master;
    $Id = $Fun->id_funcionario;

    $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$Id'");
    $funcionario = mysql_fetch_array($qr_funcionario);

    $projeto = $_REQUEST['projeto'];
    $regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

    if($regiao == ''){
        $regiao = $usuario['id_regiao'];
    }
    
    if (empty($projeto)) {
        $nomePagina = "Lista Projetos";
    } else {
        $breadcrumb_pages = array("Lista Projetos" => "ver.php");
        $nomePagina = "Visualizar Projeto";
    }
    
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"$nomePagina");
    
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>:: Intranet :: <?=$nomePagina?></title>
            <link href="../favicon.png" rel="shortcut icon" />

            <!-- Bootstrap -->
            <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
            <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
            <link href="../resources/css/main.css" rel="stylesheet" media="screen">
            <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
            <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
            <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
            <link href="../css/progress.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/add-ons.min.css" rel="stylesheet">
            <style>
                .listRelatorios{
                    list-style: none;
                }
                .listRelatorios li{
                    display: inline-block;
                    padding: 5px;
                }
            </style>
        </head>
        <body>
        <?php include("../template/navbar_default.php"); ?>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?=$nomePagina?></small></h2></div>
                    </div>
                </div>
                <form id="form1" method="post">
                    <input type="hidden" name="home" id="home" value="">
                    <input type="hidden" name="projeto" id="projeto" value="">
                </form>
                <?php
                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
                $row_regiao = mysql_fetch_assoc($qr_regiao);

                // Tela 1
                if (empty($projeto)) { ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <?php
                            $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
                            $row_func = mysql_fetch_assoc($qr_funcionario);

                            $status = array(1 => 'Local ativo ', 0 => 'Local inativo');
                            foreach ($status as $status_reg => $tipo) {
                                if ($row_func['tipo_usuario'] == 6) { //BLOQUEIO USUÁRIO CADASTRADOR ITABORAÍ
                                    $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' and status_reg = '$status_reg' AND id_projeto = '3295' ORDER BY status_reg DESC");
                                    $qr_projetos2 = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' and status_reg = '$status_reg' AND id_projeto = '3295' ORDER BY status_reg DESC");
                                } else {
                                    $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' and status_reg = '$status_reg' ORDER BY status_reg DESC");
                                    $qr_projetos2 = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' and status_reg = '$status_reg' ORDER BY status_reg DESC");
                                }
                                $verifica_projetos = mysql_num_rows($qr_projetos);
                                
                                if ($status_reg == 1) {
                                    $classe = "info"; $title = "LISTA DE PROJETOS ATIVOS";
                                } else {
                                    $classe = "danger";$title = "LISTA DE PROJETOS INATIVOS";
                                }
                                
                                if (!empty($verifica_projetos)) { ?>
                                    <!--div class="panel panel-<?=$classe?>">
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><?=$title?></h3>
                                        </div>
                                        <div class="panel-body"-->
                                        <?php $i=1; while ($row = mysql_fetch_array($qr_projetos)) { $class = ($i++ % 2 == 0) ? 'rh1':'rh2'; ?>
                                            <div class="col-lg-4 col-md-6 col-sm-6">
                                                <div class="info-box <?php echo $class ?> pointer no-padding-hr" data-proj="<?=$row['id_projeto']?>">
                                                    <i class="w-55 fa fa-home text-lg no-padding-hr"></i>
                                                    <div class="h-100 display-table">
                                                        <div class="text-lg vcenter">
                                                            <div><?=$row['id_projeto']?></div>
                                                            <div><?=$row['nome']?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <!--/div>
                                    </div-->
                                <?php }
                            } ?>
                        </div>
                    </div>
                <?php 
                } else {
                    // Tela 2
                    $qr_projetos = mysql_query("SELECT *, date_format(inicio, '%d/%m/%Y') AS data_ini, date_format(termino, '%d/%m/%Y') AS data_fim FROM projeto WHERE id_projeto = '$projeto' AND status_reg IN ('1','0')");
                    $row = mysql_fetch_assoc($qr_projetos);

                    $qr_cooperativas = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$regiao' AND status_reg = '1'");
                    $numero_cooperativas = mysql_num_rows($qr_cooperativas);

                    // Participantes Ativos
                    $qr_clt_ativos = mysql_query("SELECT * FROM rh_clt WHERE (status < '60' OR status = '200') AND id_regiao ='$regiao' AND tipo_contratacao = '2' AND id_projeto = '$projeto'");
                    $num_clt_ativos = mysql_num_rows($qr_clt_ativos);
                    $qr_cooperado_ativos = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND tipo_contratacao = '3' AND id_regiao ='$regiao' AND id_projeto = '$projeto'");
                    $num_cooperado_ativos = mysql_num_rows($qr_cooperado_ativos);
                    $qr_autonomo_ativos = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND tipo_contratacao = '1' AND id_regiao ='$regiao' AND id_projeto = '$projeto'");
                    $num_autonomo_ativos = mysql_num_rows($qr_autonomo_ativos);
                    $qr_autonomo_pj_ativos = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND tipo_contratacao = '4' AND id_regiao ='$regiao' AND id_projeto = '$projeto'");
                    $num_autonomo_pj_ativos = mysql_num_rows($qr_autonomo_pj_ativos);

                    // Participantes Inativos
                    $qr_clt_inativos = mysql_query("SELECT * FROM rh_clt WHERE (status >= '60' AND status != '200') AND tipo_contratacao = '2' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                    $num_clt_inativos = mysql_num_rows($qr_clt_inativos);
                    $qr_cooperado_inativos = mysql_query("SELECT * FROM autonomo WHERE status != '1' AND tipo_contratacao = '3' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                    $num_cooperado_inativos = mysql_num_rows($qr_cooperado_inativos);
                    $qr_autonomo_inativos = mysql_query("SELECT * FROM autonomo WHERE status != '1' AND tipo_contratacao = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                    $num_autonomo_inativos = mysql_num_rows($qr_autonomo_inativos);
                    $qr_autonomo_pj_inativos = mysql_query("SELECT * FROM autonomo WHERE status != '1' AND tipo_contratacao ='4' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
                    $num_autonomo_pj_inativos = mysql_num_rows($qr_autonomo_pj_inativos);

                    // Total de Participantes
                    $total_ativos = $num_clt_ativos + $num_cooperado_ativos + $num_autonomo_ativos + $num_autonomo_pj_ativos;
                    $total_inativos = $num_clt_inativos + $num_cooperado_inativos + $num_autonomo_inativos + $num_autonomo_pj_inativos;
                    $total = $total_ativos + $total_inativos;

                    // Tela para Busca AvanÃ§ada
                    if (empty($_REQUEST['id'])) { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="note"><?=$row['id_projeto']?> - <?=$row['nome']?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <ul class="nav nav-tabs margin_b10">
                                    <!--li class="active"><a href=".projEstatistica" data-toggle="tab">Estatística</a></li-->
                                    <li class="active"><a href=".projParticipante" data-toggle="tab">Participantes</a></li>
                                    <li class=""><a href=".projRelatorio" data-toggle="tab">Relatórios</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active projParticipante">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 btn-group btn-group-vertical">
                                        <h4 class="text-danger">EDIÇÃO DE PARTICIPANTES</h4>
                                        <hr class="hr-danger">
                                        <div class="">
                                            
                                            <a href="../bolsista.php?regiao=<?=$regiao?>&projeto=<?=$projeto?>" class="btn btn-lg btn-block btn-primary margin_b5" target="_blank">Visualizar Participantes</a>
                                            <a href="../folha_ponto.php?regiao=<?=$regiao?>&pro=<?=$projeto?>&id=1&caminho=1" class="btn btn-lg btn-block btn-default margin_b5" target="_blank">Gerar Apontamento</a>
                                            
                                            <div class="clear"></div>
                                        </div>
                                        <br/><br/>
                                        <h4 class="text-danger">CADASTRO</h4>
                                        <hr class="hr-danger">
                                        <div>
                                            <?php if ($row['status_reg'] == 1) { ?>
                                            <div class="col-lg-4 col-md-6 center">
                                                <a href="../rh/cadastroclt.php?regiao=<?=$regiao?>&projeto=<?=$projeto?>" class="btn btn-lg btn-block btn-warning margin_b5" target="_blank">Cadastrar CLT</a>
                                            </div>
                                            <div class="col-lg-4 col-md-6 center">
                                                <a href="../cooperativas/cadcooperado.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=3" class="btn btn-lg btn-block btn-info margin_b5" target="_blank">Cadastrar Cooperado</a>
                                            </div>
                                            <div class="col-lg-4 col-md-6 center">
                                                <a href="../cadastro_bolsista.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>" class="btn btn-lg btn-block btn-success margin_b5" target="_blank">Cadastrar Aut&ocirc;nomo</a>
                                            </div>
                                            <div class="col-lg-4 col-md-6 center">
                                                <a href="../cooperativas/cadcooperado.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=4" class="btn btn-lg btn-block btn-success margin_b5" target="_blank">Cadastrar Aut&ocirc;nomo / PJ</a>
                                            </div>
                                            <div class="col-lg-4 col-md-6 center">
                                                <a href="../terceirizado/cadterceiro.php?regiao=<?= $regiao ?>&pro=<?= $projeto ?>&tipo=4" class="btn btn-lg btn-block btn-success margin_b5" target="_blank">Cadastrar Terceirizado</a>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <h4 class="text-danger">ESTAT&Iacute;STICA PARTICIPANTES</h4>
                                        <hr class="hr-danger">
                                        <div class="stat-panel">
                                            <a href="javascript:void(0);" class="stat-cell col-xs-5 bg-success bordered no-border-vr no-border-l no-padding valign-middle text-center text-lg">
                                                Ativos<br/>
                                                <i class="fa fa-users"></i>&nbsp;&nbsp;<strong><?=$total_ativos?></strong>
                                            </a> <!-- /.stat-cell -->
                                            <div class="stat-cell col-xs-7 no-padding valign-middle">
                                                <div class="stat-rows">
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-success padding-sm valign-middle">
                                                            <?=$num_clt_ativos?> CLTs
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-success darken padding-sm valign-middle">
                                                            <?=$num_autonomo_ativos?> Aut&ocirc;nomos
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-success darker padding-sm valign-middle">
                                                            <?=$num_cooperado_ativos?> Cooperados
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-success darker padding-sm valign-middle">
                                                            <?=$num_autonomo_pj_ativos?> Aut&ocirc;nomos / PJ
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                </div> <!-- /.stat-rows -->
                                            </div> <!-- /.stat-cell -->
                                        </div>
                                        <div class="stat-panel">
                                            <a href="javascript:void(0);" class="stat-cell col-xs-5 bg-danger bordered no-border-vr no-border-l no-padding valign-middle text-center text-lg">
                                                Inativos<br/>
                                                <i class="fa fa-users"></i>&nbsp;&nbsp;<strong><?=$total_inativos?></strong>
                                            </a> <!-- /.stat-cell -->
                                            <div class="stat-cell col-xs-7 no-padding valign-middle">
                                                <div class="stat-rows">
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-danger padding-sm valign-middle">
                                                            <?=$num_clt_inativos?> CLTs
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-danger darken padding-sm valign-middle">
                                                            <?=$num_autonomo_inativos?> Aut&ocirc;nomos
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-danger darker padding-sm valign-middle">
                                                            <?=$num_cooperado_inativos?> Cooperados
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                    <div class="stat-row">
                                                        <a href="javascript:void(0);" class="stat-cell bg-danger darker padding-sm valign-middle">
                                                            <?=$num_autonomo_pj_inativos?> Aut&ocirc;nomos / PJ
                                                            <!--i class="fa fa-users pull-right"></i-->
                                                        </a>
                                                    </div>
                                                </div> <!-- /.stat-rows -->
                                            </div> <!-- /.stat-cell -->
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($numero_cooperativas)) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="col-lg-12 note">
                                                <?php
                                                while ($cooperativa = mysql_fetch_assoc($qr_cooperativas)) {
                                                    $qr_coop_ativos = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao IN ('3','4') AND id_cooperativa = '$cooperativa[id_coop]' AND id_projeto = '$projeto' AND id_regiao = '$regiao' AND status = '1'");
                                                    $numero_coop_ativos = mysql_num_rows($qr_coop_ativos);
                                                    $qr_coop_inativos = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao IN ('3','4') AND id_cooperativa = '$cooperativa[id_coop]' AND id_projeto = '$projeto' AND id_regiao = '$regiao' AND status != '1'");
                                                    $numero_coop_inativos = mysql_num_rows($qr_coop_inativos);
                                                    $total_coop = $numero_coop_ativos + $numero_coop_inativos;
                                                    //if (empty($total_coop)) { ?>
                                                        <div class="col-lg-12">
                                                            <?=$cooperativa[fantasia]?>: <b><?=$total_coop?></b> <i>(<?=$numero_coop_ativos?> ativos / <?=$numero_coop_inativos?> inativos)</i>
                                                        </div>
                                                    <?php //}
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="tab-pane projRelatorio">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4 class="text-danger">RELATÓRIOS</h4>
                                        <hr class="hr-danger">
                                    </div>
                                </div>
                                <?php if(file_exists('relatorios.php'))
                                    require('relatorios.php'); 
                                else 
                                    echo "Arquivo não Encontrado!"; ?>
                            </div>
                        </div>
                    <?php } else {

                        $id = $_REQUEST['id'];

                        switch ($id) {
                            case 1:

                                $recebi = $_REQUEST['procura'];
                                $pro = $_REQUEST['projeto'];
                                $reg = $_REQUEST['regiao'];

                                $qr_busca = mysql_query("SELECT id_autonomo, nome, id_regiao, id_projeto, tipo_contratacao FROM autonomo WHERE status = '1' AND id_regiao = '$reg' AND id_projeto = '$pro' AND nome LIKE '%$recebi%' UNION SELECT id_clt, nome, id_regiao, id_projeto, tipo_contratacao FROM rh_clt WHERE status < '60' AND id_regiao = '$reg' AND id_projeto = '$pro' AND nome LIKE '%$recebi%'");
                                $total_busca = mysql_num_rows($qr_busca);

                                if (empty($total_busca)) {
                                    $Devolver = '<a href="#" style="color:#C30; text-decoration:none; display:block; padding:3px; padding-left:5px;">Sua busca n&atilde;o retornou resultado</a>';
                                } else {
                                    while ($busca = mysql_fetch_array($qr_busca)) {
                                        if ($busca['tipo_contratacao'] == "2") {
                                            $li = "<a class=\"busca\"   
                            href='rh/ver_clt.php?reg=$busca[id_regiao]&clt=$busca[0]&pro=$busca[id_projeto]&pagina=bol'
                        onCLick=\"document.all.ttdiv.style.display='none'; 
                        document.all.username.value='" . $busca['nome'] . "' \">";
                                        } else {
                                            $li = "<a class=\"busca\" 
                            href='ver_bolsista.php?reg=$busca[id_regiao]&bol=$busca[0]&pro=$busca[id_projeto]'
                        onCLick=\"document.all.ttdiv.style.display='none';
                        document.all.username.value='" . $busca['nome'] . "' \">";
                                        }
                                        $Devolver .= "$li" . $busca['nome'] . "</a>";
                                    }
                                }

                                echo $Devolver;

                                break;
                            case 2:
                        }
                    }
                    ?>

                <?php } ?>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script type="text/javascript">
            // jquery para os relatórios
            $(document).ready(function() {
                $(".smallstat").click(function() {
                    var url = $(this).data('url');
                    var relatorio = $(this).data("id");
                    $.post('../methods.php', {url: url, id_relatorio: relatorio, id_usuario:<?= $usuario['id_funcionario'] ?>, method: 'logRelatorio'}, function(data) {
                        if (data === true) {
                            //windows.open(url, "_blank");
                        }
                    });
                });
                
                $('.info-box').click(function(){
                    var projeto = $(this).data('proj');
                    $("#projeto").val(projeto);
                    $("#form1").attr('action','ver.php');
                    $("#form1").submit();
                });
                
                /*$('.smallstat').click(function(){
                    var url = $(this).data('url');
                    $("#form1").attr('action',url);
                    $("#form1").submit();
                });*/
            });
            
            function exibe() {
                if (document.getElementById("localizacao").style.display == "none") {
                    document.getElementById("localizacao").style.display = "block";
                }
            }

            function oculta() {
                if (document.getElementById("localizacao").style.display == "block") {
                    document.getElementById("localizacao").style.display = "none";
                }
            }

            function getPosicaoElemento() {
                elemID = "username";
                var offsetTrail = document.getElementById(elemID);
                var offsetLeft = 0;
                var offsetTop = 0;
                while (offsetTrail) {
                    offsetLeft += offsetTrail.offsetLeft;
                    offsetTop += offsetTrail.offsetTop;
                    offsetTrail = offsetTrail.offsetParent;
                }
                if (navigator.userAgent.indexOf("Mac") != -1 &&
                        typeof document.body.leftMargin != "undefined") {
                    offsetLeft += document.body.leftMargin;
                    offsetTop += document.body.topMargin;
                }
                offsetTop = offsetTop + 22;
                document.all.Flutuante.style.left = offsetLeft + "px";
                document.all.Flutuante.style.top = offsetTop + "px";
            }

            function ajaxFunction() {
                var xmlHttp;
                try
                {
                    xmlHttp = new XMLHttpRequest();
                }
                catch (e)
                {
                    try
                    {
                        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
                    }
                    catch (e)
                    {
                        try
                        {
                            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                        catch (e)
                        {
                            alert("Your browser does not support AJAX!");
                            return false;
                        }
                    }
                }
                xmlHttp.onreadystatechange = function() {
                    if (document.getElementById('username').value == '') {
                        document.all.ttdiv.style.display = "none";
                    } else {
                        document.all.ttdiv.style.display = "";
                        if (xmlHttp.readyState == 3) {
                            document.all.spantt.innerHTML = "<div align='center' style='background-color:#5C7E59'><img src='imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
                        } else if (xmlHttp.readyState == 4) {
                            document.all.spantt.innerHTML = xmlHttp.responseText;
                        }
                    }
                }

                var enviando = escape(document.getElementById('username').value);
                xmlHttp.open("GET", 'ver.php?procura=' + enviando + '&id=1&projeto=<?= $projeto ?>&regiao=<?= $regiao ?>', true);
                xmlHttp.send(null);

            }
            </script>
        </body>
    </html>
<?php } ?>