<?php
/**
 * Procedimentos para lançamento de férias
 * 
 * @file      index.php
 * @license   
 * @link      http://www.f71lagos.com/intranet/rh_novaintra/index.php
 * @copyright 2016 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   
 * @access    public  
 * 
 * @version: 3.0.0000L - ??/??/???? - Não Definido - Versão Inicial 
 * @version: 3.0.0320L - 08/12/2016 - Jacques      - Correção do redirecionamento errado quando $_COOKIE not setado
 * 
 */

$domain = $_SERVER['HTTP_HOST'];

if (!isset($_COOKIE['logado'])) {
    header("Location: http://{$domain}/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php'); 
include('../../classes/global.php');
include('../../funcoes.php');
include("../../classes/FeriasClass.php");
include('../../wfunction.php');
include('../../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$hoje = new DateTime(); 
 
$feriasObj = new Ferias();
//$arrayAvisos = $feriasObj->getAvisosFerias($usuario['id_master'], $id_regiao);
$arrayDiasAviso = array(1 => array(10,30,40),2 => array(40,60,90));
$arrayAvisos = array_filter($feriasObj->getAvisosFerias($usuario['id_master'], $arrayDiasAviso, $usuario['id_regiao'], true)); 
$dadosAvisos = $arrayAvisos[$usuario['id_regiao']];
$qtdConcessivel = $qtdAquisitivo = 0; 
foreach ($arrayDiasAviso[2] as $value) {
    $qtdConcessivel += count($dadosAvisos[2][$value]);
} 
foreach ($arrayDiasAviso[1] as $value) {
    $qtdAquisitivo += count($dadosAvisos[1][$value]);
}

        
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$ferias_vencidas = $feriasObj->listaFeriasVencidas($id_regiao);
$ferias_vencer = $feriasObj->listaFeriasVencer($id_regiao);

$count_vencidas = count($ferias_vencidas);
$count_vencer = count($ferias_vencer);

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Férias");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: RH Férias</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->

        <!-- Estilo para Widget de calendário no padrão Boostrap -->
        <link href="/intranet/js/bootstrap-datepicker-1.4.0-dist/css/bootstrap-datepicker.css?8181" rel="stylesheet" media="all">
        <link href="/intranet/js/bootstrap-datepicker-1.4.0-dist/css/bootstrap-datepicker.min.css?8181" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Férias</small></h2></div>

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs hidden-print" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active "><a href="#avisos" role="tab" data-toggle="tab">Avisos</a></li>
                        <li role="presentation"><a href="#agendamento" role="tab" data-toggle="tab">Férias Agendadas</a></li>
                        <li role="presentation"><a href="#lista" role="tab" data-toggle="tab">Lista de Funcionários</a></li>
                        <li role="presentation"><a href="#relatorio" role="tab" data-toggle="tab">Relatório de Férias</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="avisos">
                            
                            <div class="row">
                                <!-- Segundo periodo de Vencimento -->
                                <?php if((count($dadosAvisos['expirado']['expirado']) + $qtdConcessivel) > 0) { ?>
                                <div class="col-lg-12">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading"><span class="badge"><?= (count($dadosAvisos['expirado']['expirado']) + $qtdConcessivel) ?></span> Férias no Limite do Periodo Concessivo</div>
                                        <div class="panel-body overflow">
                                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading pointer" role="tab" id="headingOne" data-toggle="collapse" data-parent="#expirado" href="#expirado">
                                                        <span class="badge"><?= count($dadosAvisos['expirado']['expirado']) ?></span> Expirado
                                                    </div>
                                                    <div id="expirado" class="panel-collapse collapse" role="tabpanel">
                                                        <table class="table table-hover table-striped text-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Nome</th>
                                                                    <th>Projeto</th>
                                                                    <th class="text-center">Entrada</th>
                                                                    <th class="text-center">Periodo Concessivel</th>
                                                                    <!--th class="text-center">Limite Concessivel</th-->
                                                                    <!--th style="width: 125px;">Status</th-->
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($dadosAvisos['expirado']['expirado'] as $value) { ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?= $value['id_clt'].' - '.$value['nome'] ?>
                                                                            <?php if($value['dias_licenca'] > 0){ ?>
                                                                                <span class="btn-success badge pull-right"><span class="fa fa-user-md"> <?=$value['dias_licenca']?></span></span>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td><?= $value['nomeProjeto'] ?></td>
                                                                        <td class="text-center"><?= implode('/', array_reverse(explode('-',$value['data_entrada']))) ?></td>
                                                                        <td class="text-center"><?= $value['termino_segundo_periodo']->format("d/m/Y") ?></td>
                                                                        <!--td class="text-center"><?= $value['limite_concessivo']->format("d/m/Y") ?></td-->
                                                                        <!--td><span class="text-info">Vence em <?= (int) $value['dias'] ?> dias</span></td-->
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <?php ksort($dadosAvisos[2]); foreach ($dadosAvisos[2] as $dia => $clts) { ?>
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading pointer" role="tab" id="headingOne" data-toggle="collapse" data-parent="#<?="2_".$dia?>" href="#<?="2_".$dia?>">
                                                            <span class="badge"><?= count($dadosAvisos[2][$dia]) ?></span> Em até <?= $dia ?> dias
                                                        </div>
                                                        <div id="<?="2_".$dia?>" class="panel-collapse collapse" role="tabpanel">
                                                            <table class="table table-hover table-striped text-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Nome</th>
                                                                        <th>Projeto</th>
                                                                        <th class="text-center">Entrada</th>
                                                                        <th class="text-center">Periodo Concessivel</th>
                                                                        <th class="text-center">Limite Concessivel</th>
                                                                        <th style="width: 125px;">Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php foreach ($clts as $value) { ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?= $value['id_clt'].' - '.$value['nome'] ?>
                                                                            <?php if($value['dias_licenca'] > 0){ ?>
                                                                                <span class="btn-success badge pull-right"><span class="fa fa-user-md"> <?=$value['dias_licenca']?></span></span>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td><?= $value['nomeProjeto'] ?></td>
                                                                        <td class="text-center"><?= implode('/', array_reverse(explode('-',$value['data_entrada']))) ?></td>
                                                                        <td class="text-center"><?= $value['termino_segundo_periodo']->format("d/m/Y") ?></td>
                                                                        <td class="text-center"><?= $value['limite_concessivo']->format("d/m/Y") ?></td>
                                                                        <td><span class="text-info">Vence em <?= (int) $value['dias'] ?> dias</span></td>
                                                                    </tr>
                                                                <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                
                                                <?php } ?>
                                                
                                            </div>
                                        </div><!-- /.panel-body -->
                                    </div><!-- /.panel-primary -->
                                </div><!-- /.col-lg-6 -->
                                <?php } ?>
                                <!-- Primeiro preiodo de Vencimento -->
                                <?php if($qtdAquisitivo > 0) { ?>
                                <div class="col-lg-12">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading"><span class="badge"><?= $qtdAquisitivo ?></span> Férias no Limite do Periodo Aquisitivo</div>
                                        <div class="panel-body overflow">
                                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                                <?php ksort($dadosAvisos[1]); foreach ($dadosAvisos[1] as $dia => $clts) { ?>
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading pointer" role="tab" id="headingOne" data-toggle="collapse" data-parent="#<?="1_".$dia?>" href="#<?="1_".$dia?>">
                                                            <span class="badge"><?= count($dadosAvisos[1][$dia]) ?></span> Em até <?= $dia ?> dias
                                                        </div>
                                                        <div id="<?="1_".$dia?>" class="panel-collapse collapse" role="tabpanel">
                                                            <table class="table table-hover table-striped text-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Nome</th>
                                                                        <th>Projeto</th>
                                                                        <th class="text-center">Entrada</th>
                                                                        <th class="text-center">Periodo Aquisitivo</th>
                                                                        <th style="width: 125px;">Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($clts as $value) { ?>
                                                                        <tr>
                                                                            <td>
                                                                                <?= $value['id_clt'].' - '.$value['nome'] ?>
                                                                                <?php if($value['dias_licenca'] > 0){ ?>
                                                                                    <span class="btn-success badge pull-right"><span class="fa fa-user-md"> <?=$value['dias_licenca']?></span></span>
                                                                                <?php } ?>
                                                                            </td>
                                                                            <td><?= $value['nomeProjeto'] ?></td>
                                                                            <td><?= implode('/', array_reverse(explode('-',$value['data_entrada']))) ?></td>
                                                                            <td class="text-center"><?= $value['termino_primeiro_periodo']->format("d/m/Y") ?></td>
                                                                            <td><span class="text-info">Vence em <?= (int) $value['dias'] ?> dias</span></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                
                                                <?php } ?>
                                                
                                            </div>
                                        </div><!-- /.panel-body -->
                                    </div><!-- /.panel-primary -->
                                </div><!-- /.col-lg-6 -->
                                <?php } ?>
                                
<!--                                
                                <div class="col-lg-6">
                                    <div class="panel panel-info">
                                        <div class="panel-heading"><span class="badge"><?= $count_vencer ?></span> Férias a Vencer</div>
                                        <div class="panel-body overflow" style="max-height: 250px;">
                                            <?php if ($count_vencer > 0) { ?>
                                                <table class="table table-hover table-striped text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nome</th>
                                                            <th>Projeto</th>
                                                            <th>Data de vencimento</th>
                                                            <th style="width: 125px;">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($ferias_vencer as $value) { ?>
                                                            <tr>
                                                                <td><?= $value['id_clt'] ?></td>
                                                                <td><?= $value['nome'] ?></td>
                                                                <td><?= $value['nomeProjeto'] ?></td>
                                                                <td><?= date('d/m/Y', $value['data_vencimento']) ?></td>
                                                                <td><span class="text-info">Vence em <?= (int) $value['vencendo'] ?> dias</span></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            <?php }else { ?>
                                                <div class="bs-callout bs-callout-info">
                                                    <p class="text-info"><i class="fa fa-info-circle"></i> Não há CLT com férias a vencer.</p>
                                                </div>
                                            <?php } ?>
                                        </div> /.panel-body 
                                    </div> /.panel-primary 
                                </div> /.col-lg-6 

                                <div class="col-lg-6">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading"><span class="badge"><?= $count_vencidas ?></span> Férias Vencidas</div>
                                        <div class="panel-body overflow" style="max-height: 250px;">
                                            <?php if ($count_vencidas > 0) { ?>
                                                <table class="table table-hover table-striped text-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Nome</th>
                                                            <th>Projeto</th>
                                                            <th>Data de vencimento</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($ferias_vencidas as $value) { ?>
                                                            <tr>
                                                                <td><?= $value['id_clt'] ?></td>
                                                                <td><?= $value['nome'] ?></td>
                                                                <td><?= $value['nomeProjeto'] ?></td>
                                                                <td><?= date('d/m/Y', $value['data_vencimento']) ?></td>
                                                                <td><span class="text-danger">Vencida</span></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            <?php } else { ?>
                                                <div class="bs-callout bs-callout-danger">
                                                    <p class="text-danger"><i class="fa fa-info-circle"></i> Não há CLT com férias vencidas.</p>
                                                </div>
                                            <?php } ?>
                                        </div> /.panel-body 
                                    </div> /.panel-primary 
                                </div> /.col-lg-6 -->

                            </div><!-- /.row -->
                        </div><!-- /#avisos -->
                        <div role="tabpanel" class="tab-pane" id="agendamento">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="calendario-container"></div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="lista">

                            <form class="form-horizontal" role="form" id="form-lista" method="post">
                                <input type="hidden" name="home" id="home" value="" />
                                <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                                <input type="hidden" name="id_clt" id="id_clt" value="">
                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <select name="projeto" id="projeto_lista" class="form-control projeto">
                                                    <option>-- Todos os Projetos --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nome_clt" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-6">
                                                <div>
                                                    <input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do CLT, CPF, Matrícula">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div>
                                                    <div id="data_ini" class='input-group date'>
                                                        <input type='text' id="data_ini_fmt" name="data_ini_fmt" class="form-control span2" placeholder="Início das Férias" onKeyUp="mascara_data(this)" onChange="$('#btn_calcular_ferias').prop('disabled', false);" readonly="true" />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar">
                                                            </span>
                                                        </span>
                                                    </div>                                    
                                                </div>                                    
                                            </div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">                                       
                                        <!--<a href="../../rh/ferias/ferias_em_lote.php" target="_blank" class="btn btn-warning">Férias Coletivas</a>-->
                                        <input type="button" value="Consultar" id="submit-lista" class="btn btn-primary">                                        
                                    </div>

                                </div><!-- /.panel -->
                                
                                <div id="retorno-lista"></div>
                                
                            </form>

                        </div><!-- /#lista -->

                        <div role="tabpanel" class="tab-pane" id="relatorio">
                            <form class="form-horizontal" role="form" id="form-rel" method="post">
                                <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_rel" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <select name="projeto" id="projeto_rel" class="projeto form-control">
                                                    <option>-- Todos os Projetos --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="mes_ini" class="col-lg-2 control-label">Início:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <?= montaSelect(mesesArray(), null, array('name' => 'mes_ini', 'id' => 'mes_ini', 'class' => 'form-control')) ?>
                                                    <div class="input-group-addon"></div>
                                                    <?= montaSelect(anosArray(date('Y') - 5, date('Y') + 1, array('-1' => '« Selecione o ano »')), null, array('name' => 'ano_ini', 'id' => 'ano_ini', 'class' => 'form-control')) ?>
                                                </div>
                                            </div>
                                            <label for="mes_ini" class="col-lg-1 control-label">Fim:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <?= montaSelect(mesesArray(), null, array('name' => 'mes_fim', 'id' => 'mes_fim', 'class' => 'form-control')) ?>
                                                    <div class="input-group-addon"></div>
                                                    <?= montaSelect(anosArray(date('Y') - 5, date('Y') + 1, array('-1' => '« Selecione o ano »')), null, array('name' => 'ano_fim', 'id' => 'ano_fim', 'class' => 'form-control')) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="funcao" class="col-lg-2 control-label">Unidade:</label>
                                            <div class="col-lg-9"><select name="funcao" id="funcao" class="form-control"><option value="">« Selecione »</option></select></div>
                                        </div>
                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">
                                         <a href="relatorio_ferias_por_unidade.php" class="btn btn-success">Relatório de Férias por Unidade</a>
                                        <input type="button" value="Consultar" id="submit-relatorio" class="btn btn-primary">
                                    </div>

                                </div><!-- /.panel -->
                                
                                <div id="retorno-relatorio"></div>
                            </form>
                        </div><!-- /#relatorio -->
                        
                        <div role="tabpanel" class="tab-pane" id="solicitacao">
                            <form class="form-horizontal" role="form" id="form-solicitacao" method="post">
                                <br>
                                <!--<h4>Solicitações de Férias com Pedido de Alteração</h4>-->
                                <hr>
<!--                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <select name="projeto" id="projeto_solicita" class="form-control projeto">
                                                    <option>-- Todos os Projetos --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="mes_ini" class="col-lg-2 control-label">Competência:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <?= montaSelect(mesesArray(), $mesSel, array('name' => 'mes_ini', 'id' => 'mes_ini_solicita', 'class' => 'form-control')) ?>
                                                    <div class="input-group-addon"></div>
                                                    <?= montaSelect(anosArray(date('Y') - 5, date('Y') + 1, array('-1' => '« Selecione o ano »')), $anoSel, array('name' => 'ano_ini', 'id' => 'ano_ini_solicita', 'class' => 'form-control')) ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>  /.panel-body 

                                    <div class="panel-footer text-right">                                       
                                        <button type="button" value="Consultar" id="busca-solicitacoes" class="btn btn-primary"><i class="fa fa-filter"></i> Consultar</button>
                                    </div>

                                </div>  /.panel -->
                                
                                <div id="retorno-solicitacoes"></div>
                                
                            </form>
                        </div><!-- /#solicitacao -->
                    </div>


                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->


            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <!--<script src="../../js/global.js"></script>-->
        <script src="../../resources/js/print.js" type="text/javascript"></script>
        
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.js?515"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js?515"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.pt-BR.min.js?515"></script>
        <script src="../../resources/js/rh/ferias/index2.js?515"></script>
       
        <script>
    $('body').on('click',".detalhamento_ferias",function () {   
        var url = 'detalhamento_ferias_agendada.php';
        var id_ferias_programadas = $(this).data('key');
        $.post(url,{id_ferias_programadas:id_ferias_programadas},function(data){
            bootDialog(data,'Detalhamento de Férias Agendadas'); 
//            $("[data-toggle='tooltip']").tooltip(); 
        });
    });    
    
    
    var tableToExcel = (function () {
    var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="Content-Tipe" content="application/excel; charset=UTF-8"></head><body><table>{table}</table></body></html>'
            , base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }
    , format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) {
            return c[p];
        })
    }
    , removeImage = function (val) {
        var t = val.replace(/<img([^>]+)>/g, "");
        var n = t.replace(/<a([^>]+)>/g, "");
        return n
    }
    return function (table, name) {
        if (!table.nodeType)
            table = document.getElementById(table)
        //var copia = table.clone();
        //copia.remove(".hidden");
        //table.find("tr.hidden").remove();
        //console.log(table);
        var ctx = {
            worksheet: name || 'Worksheet',
            table: removeImage(table.innerHTML)
        }
        window.location.href = uri + base64(format(template, ctx))
    }
})();    
        </script>
       
        
    </body>
</html>
