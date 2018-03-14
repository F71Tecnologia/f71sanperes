<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../conn.php");
include("../wfunction.php");
include("../classes/FolhaClass.php");
include("../classes/BotoesClass.php");
include("../classes/EventoClass.php");
include("../classes/FeriasClass.php");
include("../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$feriasObj = new Ferias();
//print_array($dadosAvisos);exit;

if($objAcoes->verifica_permissoes(57)){
    if(isset($_REQUEST['action']) AND $_REQUEST['action'] == 'aviso_visto') {
        mysql_query("INSERT INTO rh_ferias_aviso (`id_funcionario`, `data`) VALUES ({$usuario['id_funcionario']}, NOW());");
        echo mysql_insert_id();
        exit;
    }
    if(isset($_REQUEST['action']) AND $_REQUEST['action'] == 'aviso') {
        
        if(mysql_num_rows(mysql_query("SELECT * FROM rh_ferias_aviso WHERE id_funcionario = {$usuario['id_funcionario']} AND DATE(data) = DATE(NOW())")) == 0) { 
            $arrayDiasAviso = 
                array(
                    1 => array(10,30,40),
                    2 => array(40,60,90)
                );
            $arrayAvisos = array_filter($feriasObj->getAvisosFerias($usuario['id_master'], $arrayDiasAviso, null, false)); 
            foreach ($arrayAvisos as $regiao_aviso => $dadosAvisos) { 
                $qtdConcessivel = $qtdAquisitivo = 0; 
                foreach ($arrayDiasAviso[2] as $value) {
                    $qtdConcessivel += count($dadosAvisos[2][$value]);
                } 
                foreach ($arrayDiasAviso[1] as $value) {
                    $qtdAquisitivo += count($dadosAvisos[1][$value]);
                } ?>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                <div class="panel panel-info">
                    <div class="panel-heading"><?=$regiao_aviso . ' - ' . $dadosAvisos['nome_regiao']?></div>
                    <div class="panel-body">
                        <input type="hidden" id="open_dialog" value="1<?=((count($dadosAvisos['expirado']['expirado']) + $qtdConcessivel + $qtdAquisitivo) > 0) ? true : false?>">
                        <div class="row">
                            <!-- Segundo periodo de Vencimento -->
                            <?php if((count($dadosAvisos['expirado']['expirado']) + $qtdConcessivel) > 0) { ?>
                            <div class="col-sm-12">
                                <div class="panel panel-danger">
                                    <div class="panel-heading"><span class="badge badge-danger"><?= (count($dadosAvisos['expirado']['expirado']) + $qtdConcessivel) ?></span> Férias no Limite do Periodo Concessivo</div>
                                    <div class="panel-body overflow">
                                        <div class="col-sm-3 <?=((count($dadosAvisos['expirado']['expirado']) == 0) ? 'hide' : null)?>">
                                            <div class="tr-bg-active"><span class="btn-label bg-danger bordered w-55 text-bold text-center"><?= count($dadosAvisos['expirado']['expirado']) ?></span>Expirados</div>
                                        </div>
                                        <?php ksort($dadosAvisos[2]); foreach ($dadosAvisos[2] as $key => $value) { ?>
                                            <div class="col-sm-<?=(12/(count($dadosAvisos[2]) + 1))?> <?=((count($dadosAvisos[2][$key]) == 0) ? 'hide' : null)?>">
                                                <div class="tr-bg-active"><span class="btn-label bg-danger bordered w-55 text-bold text-center"><?= count($dadosAvisos[2][$key]) ?></span>Em até <?= $key ?> dias</div>
                                            </div>
                                        <?php } ?>
                                    </div><!-- /.panel-body -->
                                </div><!-- /.panel-danger -->
                            </div><!-- /.col-sm-12 -->
                            <?php } ?>
                            <!-- Primeiro preiodo de Vencimento -->
                            <?php if($qtdAquisitivo > 0) { ?>
                            <div class="col-sm-12">
                                <div class="panel panel-warning">
                                    <div class="panel-heading"><span class="badge badge-warning"><?= $qtdAquisitivo ?></span> Férias a Vencer no Periodo Aquisitivo</div>
                                    <div class="panel-body overflow">
                                        <?php ksort($dadosAvisos[1]); foreach ($dadosAvisos[1] as $key => $value) { ?>
                                            <div class="col-sm-<?=(12/count($dadosAvisos[1]))?> <?=((count($dadosAvisos[1][$key]) == 0) ? 'hide' : null)?>">
                                                <div class="tr-bg-active"><span class="btn-label bg-warning bordered w-55 text-bold text-center"><?= count($dadosAvisos[1][$key]) ?></span>Em até <?= $key ?> dias</div>
                                            </div>
                                        <?php } ?>
                                    </div><!-- /.panel-body -->
                                </div><!-- /.panel-warning -->
                            </div><!-- /.col-sm-12 -->
                            <?php } ?>
                        </div><!-- /.row -->
                    </div><!-- /.panel-body -->
                </div><!-- /.panel -->
            <?php 
            } 
        } exit; 
    } 
}

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$botoesMenu = $botoes->getBotoesMenuModulo(2);

//LISTA DE EVENTOS
$objEvento = new Eventos();
$listaEventos = $objEvento->listaEventos();
$dadosEventos = $objEvento->getTerminandoEventos(date("Y-m-d"), $usuario['id_regiao'], null, null, 10);
$dadosArrayEventos = $objEvento->array_dados;
$status = $objEvento->getStatus();

if(isset($_REQUEST['action']) AND $_REQUEST['action'] == 'ver_clt_licenca'){
    $participantes = $objEvento->listarCltEmEvento($usuario['id_regiao'], $id_projeto, $_REQUEST['id']); 
    foreach ($participantes as $key1 => $arrayParticipantes) { 
        foreach ($arrayParticipantes as $key => $value) { ?>
            <tr><td><?=utf8_encode($value['nome'])?></td></tr>
        <?php } 
    } exit;
}

//echo "<!--";
//print_r($dadosArrayEventos);
//echo "-->";

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Gestão de RH");
//$breadcrumb_pages = array("Gestão de RH"=>"../", "Seguro Desemprego"=>"index2.php");


$array_icons = array(
    '10'=>"fa fa-child",
    '20'=>"fa fa-bed",
    '30'=>"fa fa-fighter-jet",
    '40'=>"fa fa-plane",
    '50'=>"fa fa-female",
    '51'=>"fa fa-futbol-o",
    '52'=>"fa fa-paper-plane",
    '60'=>"fa fa-sign-out",
    '61'=>"fa fa-times",
    '62'=>"fa fa-user-times",
    '70'=>"fa fa-ambulance",
    '80'=>"fa fa-wheelchair",
    '81'=>"fa fa-frown-o",
    '90'=>"fa fa-thumbs-o-down",
    '100'=>"fa fa-gavel",
    '101'=>"fa fa-heartbeat",
    '110'=>"fa fa-ban",
    '200'=>"fa fa-times-circle-o",
    '63'=>"fa fa-flag",
    '64'=>"fa fa-user-times",
    '66'=>"fa fa-sign-out",
    '65'=>"fa fa-times-circle-o",
    '53'=>"fa fa-venus-mars",
    '54'=>"fa fa-dot-circle-o",
    '56'=>"fa fa-puzzle-piece"
    );

$array_colors = array(
    '10'=>"green",
    '20'=>"blue",
    '30'=>"brown",
    '40'=>"orange",
    '50'=>"grey",
    '51'=>"purple",
    '52'=>"blue2",
    '60'=>"red",
    '61'=>"green2",
    '62'=>"pink",
    '70'=>"brown",
    '80'=>"blue",
    '90'=>"orange2",
    '100'=>"green2",
    '110'=>"green",
    '200'=>"grey",
    '63'=>"purple",
    '101'=>"brown",
    '64'=>"blue",
    '66'=>"red",
    '65'=>"green",
    '53'=>"orange",
    '54'=>"grey",
    '56'=>"purple",
    '55'=>"orange2"
    );
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#avisos" data-toggle="tab">AVISOS</a></li>
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <li><a href="#<?php echo $k ?>" data-toggle="tab"><?php echo $btMenu ?></a></li>
                            <?php } ?>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="avisos">
                                <div>
                                    <?php foreach ($listaEventos as $val) { 
                                        $qtdParticipantesEvento = $objEvento->contaCltPorEvento($val['codigo'], $usuario['id_regiao']); ?>
                                    <div class="infobox infobox-<?php echo (array_key_exists($val['codigo'], $array_colors)) ? $array_colors[$val['codigo']] : "green" ?> col-xs-12 col-sm-12 col-md-6 col-sm-4 <?=($qtdParticipantesEvento > 0)?'pointer':''?>" data-key='<?=$val['codigo']?>' data-qtd="<?=$qtdParticipantesEvento?>">
                                        <div class="infobox-icon">
                                            <i class="ace-icon <?php echo (array_key_exists($val['codigo'], $array_icons)) ? $array_icons[$val['codigo']] : "fa fa-comments" ?>"></i>
                                        </div>

                                        <div class="infobox-data">
                                            <span class="infobox-data-number"><?=$qtdParticipantesEvento?></span>
                                            <div class="infobox-content"><p><?php echo nl2br($val['codigo'] . " - " . $val['especifica']); ?></p></div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <?php foreach ($botoesMenu as $k => $btMenu) { ?>
                                <div class="tab-pane fade" id="<?php echo $k ?>">
                                    <div class="detalhes-modulo">
                                        <?php echo $botoes->getHtmlBotoesModulo($k, 3) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.container -->
        <!-- MODAL DE PRORROGACAO DE EVENTOS -->
        <form name="eventos" id="form1" action="">
            <input type="hidden" name="home" id="home" value="" />
            <input type="hidden" name="id_evento" id="id_evento" value="" />
            <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
            <input type="hidden" name="data_retorno" id="data_retorno" value="" />
            <div id="modal_motivo" class="minus">
                <p style="text-align: left;">
                    <label for="dias" style=" font-weight: bold;" >Quantidade de dias (a partir da data atual de retorno):</label><br />
                    <input type="number" name="dias"  min="0"  id="dias" style="width: 4em; height: 30px; margin: 3px 0px;" > <button type="button" id="calc-data">Calcular Prorrogação</button>
                </p>
                <p>
                    <label for="data_prorrogada" style="float: left; font-weight: bold;" >Data de Prorrogação:</label><br />
                    <input style="width: 425px; height: 30px; margin: 3px 0px;" type="text" name="data_prorrogada" id="data_prorrogada" value="" />
                </p>
                <p>
                    <label for="motivo" style="float: left; font-weight: bold;">Digite o motivo:</label><br />
                    <textarea name="motivo" style="width: 425px; height: 80px; margin: 3px 0px;"></textarea>
                </p>
                <input type="submit" name="finalizar" id="finalizar" style="float: right" value="Finalizar" />
                <div id="message_erro"></div>
            </div>
        </form>
        <!-- FIM DO MODAL DE PRORROGACAO DE EVENTOS -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../resources/js/rh/index.js" type="text/javascript"></script>
    </body>
</html>