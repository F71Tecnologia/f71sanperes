<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

$id_obrigacao = (!empty($_POST['id_obrigacao'])) ? $_POST['id_obrigacao'] : NULL;
$renovar = (!empty($_POST['renovar'])) ? $_POST['renovar'] : NULL;

if(!empty($id_obrigacao)){
    if(!empty($renovar)){
        $action = array('renovar_obrigacao','Renovar Obrigação');
    } else { 
        $action = array('editar_obrigacao','Editar Obrigação');
    }
} else { 
    $action = array('cadastrar_obrigacao','Cadastrar Obrigação');
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/regiao.php");
include("../../classes/ObrigacoesClass.php");

$usuario = carregaUsuario();

$objObrigacoes = new ObrigacoesClass();
$tiposObrigacoes = $objObrigacoes->getTipoObrigacoes();
$dadosObrigacao = $objObrigacoes->getObrigacoes("id_oscip = '$id_obrigacao'");
$dadosObrigacao = $dadosObrigacao[0];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//print_array($dadosObrigacao);

$div_periodo = $div_dias = $div_oscip_endereco = $div_resp_env_rec = 'display:none;';

if(!empty($id_obrigacao)){
    if($dadosObrigacao['periodo'] == 'Dias' || $dadosObrigacao['periodo'] == 'Meses' || $dadosObrigacao['periodo'] == 'Anos'){
        $div_dias = '';
    } else if($dadosObrigacao['periodo'] == 'Período'){
        $div_periodo = '';
    }
    if($dadosObrigacao['id_tipo_oscip'] == 12){
        $div_oscip_endereco = '';
    } else if($dadosObrigacao['id_tipo_oscip'] == 14){
        $div_resp_env_rec = '';
        $dadosRespostas = $objObrigacoes->getObrigacoes("id_tipo_oscip = '13'");
        $resposta = '<option value="">Selecione</option>';
        foreach($dadosRespostas AS $rowResposta){ 
            $selected = ($dadosObrigacao['resp_env_rec'] == $rowResposta['id_oscip']) ? 'SELECTED' : NULL;
            $resposta .= '<option value="'.$rowResposta['id_oscip'].'" '.$selected.'>(COD: '.$rowResposta['id_oscip'].') '.$rowResposta['numero_oscip'].'</option>';
        } 
    } else if($dadosObrigacao['id_tipo_oscip'] == 13){
        $div_resp_env_rec = '';
        $dadosRespostas = $objObrigacoes->getObrigacoes("id_tipo_oscip = '14'");
        $resposta = '<option value="">Selecione</option>';
        foreach($dadosRespostas AS $rowResposta){ 
            $selected = ($dadosObrigacao['resp_env_rec'] == $rowResposta['id_oscip']) ? 'SELECTED' : NULL;
            $resposta .= '<option value="'.$rowResposta['id_oscip'].'" '.$selected.'>(COD: '.$rowResposta['id_oscip'].') '.$rowResposta['numero_oscip'].'</option>';
        } 
    }
} 

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$action[1]);
$breadcrumb_pages = array("Principal" => "../index.php", "Gestão de Obrigações"=>"index.php"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?=$action[1]?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - <?=$action[1]?></small></h2></div>
                </div>
            </div>
            <form action="" method="post" id="form_obrigacoes" class="form-horizontal top-margin1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-body bloco_obrigacoes">
                        <div class="form-group">
                            <label class="control-label col-sm-2">Tipo Obrigação:</label>
                            <div class="col-sm-10">
                                <select name="id_tipo_oscip" id="id_tipo_oscip" class="form-control validate[required]">
                                    <option value="">Selecione</option>
                                    <?php foreach ($tiposObrigacoes as $tipoObrigacao) { ?>
                                    <option value="<?=$tipoObrigacao['tipo_id']?>" <?=($dadosObrigacao['id_tipo_oscip'] == $tipoObrigacao['tipo_id'])?'SELECTED':''?> ><?=$tipoObrigacao['tipo_nome']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Nº do Documento:</label>
                            <div class="col-sm-4"><input name="numero_oscip" id="numero_oscip" type="text" class="form-control validate[required]" value="<?=$dadosObrigacao['numero_oscip']?>"></div>
                            <label class="control-label col-sm-2 text-sm">Data da Publicação:</label>
                            <div class="col-sm-4"><input name="data_publicacao" id="data_publicacao" type="text" class="form-control data validate[required]" value="<?=implode('/', array_reverse(explode('-',$dadosObrigacao['data_publicacao'])))?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Período:</label>
                            <div class="col-sm-4">
                                <select name="periodo" id="periodo" class="form-control validate[required]">
                                    <option value="">Selecione um período..</option>
                                    <option value="Dias" <?=($dadosObrigacao['periodo'] == 'Dias')?'SELECTED':''?>>Dias</option>
                                    <option value="Meses" <?=($dadosObrigacao['periodo'] == 'Meses')?'SELECTED':''?>>Meses</option>
                                    <option value="Anos" <?=($dadosObrigacao['periodo'] == 'Anos')?'SELECTED':''?>>Anos</option>
                                    <option value="Indeterminado" <?=($dadosObrigacao['periodo'] == 'Indeterminado')?'SELECTED':''?>>Indeterminado</option>
                                    <option value="Período" <?=($dadosObrigacao['periodo'] == 'Período')?'SELECTED':''?>>Período</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-2 validade" style="<?=$div_periodo?>">Validade:</label>
                            <div class="col-sm-4" id="div_dias" style="<?=$div_dias?>"><input name="numero_periodo" id="numero_periodo" type="text" placeholder="Digite o número" class="form-control" value="<?=$dadosObrigacao['numero_periodo']?>"></div>
                            <div class="col-sm-4" id="div_periodo" style="<?=$div_periodo?>">
                                <div class="input-group">
                                    <input name="oscip_data_inicio" id="oscip_data_inicio" type="text" placeholder="Data de Início" class="form-control data" value="<?=implode('/', array_reverse(explode('-',$dadosObrigacao['oscip_data_inicio'])))?>">
                                    <div class="input-group-addon">até</div>
                                    <input name="oscip_data_termino" id="oscip_data_termino" type="text" placeholder="Data de Termino" class="form-control data" value="<?=implode('/', array_reverse(explode('-',$dadosObrigacao['oscip_data_termino'])))?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">Descrição:</label>
                            <div class="col-sm-10"><input name="descricao" id="descricao" type="text" class="form-control validate[required]" value="<?=$dadosObrigacao['descricao']?>"></div>
                        </div>
                        <div class="form-group" id="div_resp_env_rec" style="<?=$div_resp_env_rec?>">
                            <label class="control-label col-sm-2">Resposta:</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="resp_env_rec" id="resp_env_rec"><?=$resposta?></select>
                            </div>
                        </div>
                        <div class="form-group" id="div_oscip_endereco" style="<?=$div_oscip_endereco?>">
                            <label class="control-label col-sm-2">Endereço:</label>
                            <div class="col-sm-10"><input type="text" name="oscip_endereco" id="oscip_endereco" class="form-control" value="<?=$dadosObrigacao['oscip_endereco']?>"></div>
                        </div>
                    </div>
                    <!--div class="panel-footer text-right bloco_obrigacoes">
                        <button type="button" class="btn btn-primary next_back">Avançar <i class="fa fa-angle-double-right"></i></button>
                    </div-->
                    <div class="panel-footer bloco_obrigacoes">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <h4>Anexar Publicação</h4>
                                <hr>
                                <div id="anexo_publicacao" class="dropzone" style="min-height: 250px!important;"></div>
                            </div>
                            <div class="col-sm-6">
                                <h4>Anexar Documento</h4>
                                <hr>
                                <div id="anexo_documento" class="dropzone" style="min-height: 250px!important;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer no-padding-hr bloco_obrigacoes">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="btn btn-primary botaoSubmit"><i class="fa fa-save"></i> Salvar</button>
                            <?=(!empty($id_obrigacao))?'<input type="hidden" name="id_oscip" value="'.$id_obrigacao.'" />':''?>
                            <input type="hidden" name="action" value="<?=$action[0]?>" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
            <?php include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/administrativo/form_obrigacoes.js"></script>
    </body>
</html>