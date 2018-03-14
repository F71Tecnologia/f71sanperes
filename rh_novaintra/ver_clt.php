<?php
 //if (!defined('BASEPATH')) exit(''); 

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include("../classes/BotoesClass.php");
include("../classes/LogClass.php");
include('../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();

$ACOES = new Acoes();
$LOG = new Log();

//PEGANDO O ID DO CADASTRO
$id_clt = empty($_REQUEST['clt']) ? $_REQUEST['id_clt'] : $_REQUEST['clt'];

/**
 * LISTANDO ANEXOS DOS DOCUMENTOS
 */
if($_REQUEST['action'] == 'ver_anexo') {
    $sql_anexos = mysql_query("SELECT * FROM documento_clt_anexo WHERE id_clt = '{$id_clt}' AND id_upload = '{$_REQUEST['id_upload']}' AND anexo_status = 1 ORDER BY ordem") or die('ERRO $sql_anexos: ' . mysql_error());
    while($row_anexos = mysql_fetch_assoc($sql_anexos)) { 
        $thumbnail = (!file_exists("../rh/documentos/{$row_anexos['anexo_nome']}.{$row_anexos['anexo_extensao']}")) ? 'tr-bg-danger' : null;
        $img = (!file_exists("../rh/documentos/{$row_anexos['anexo_nome']}.{$row_anexos['anexo_extensao']}")) ? '<i class="h-100 fa fa-file" style="font-size: 100px!important;"></i>' : '<img class="h-100" src="/intranet/rh/documentos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao'].'">';
        if($row_anexos['anexo_extensao'] == 'pdf'){
            $img = '<i class="h-100 fa fa-file-pdf-o text-danger" style="font-size: 100px!important;"></i>';
        }
        
        
        echo '
        <div class="col-xs-3 margin_b5">
            <div class="thumbnail text-center '.$thumbnail.'">
                <a href="/intranet/rh/documentos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao'].'" target="_blank">
                    '.$img.'
                </a>
                <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteDoc" style="width: 100%;" data-key="'.$row_anexos['anexo_id'].'"> Deletar</span>
            </div>
        </div>';
    }
    echo '<div class="clear"></div>';
//    print_array($array_anexos);
    exit;
}

/**
 * DELETAR ANEXOS DOS DOCUMENTOS
 */
if($_REQUEST['action'] == 'deleteDoc') {
    $sql_anexos = mysql_query("UPDATE documento_clt_anexo SET anexo_status = 0 WHERE anexo_id = {$_REQUEST['id']} LIMIT 1;") or die('ERRO deleteDoc $sql_anexos: ' . mysql_error());
    exit;
}

/**
 * ATUALIZANDO ANEXOS DOS DOCUMENTOS
 */
if($_REQUEST['action'] == 'fim_upload_doc') {
    $sql_anexos = mysql_query("SELECT id_upload, data_cad FROM documento_clt_anexo WHERE id_clt = '{$id_clt}' AND id_upload = '{$_REQUEST['id_doc']}' AND anexo_status = 1 ORDER BY data_cad DESC LIMIT 1") or die('ERRO $sql_anexos: ' . mysql_error());
    $row_anexos = mysql_fetch_assoc($sql_anexos);
    echo implode('/', array_reverse(explode('-',  explode(' ',  $row_anexos['data_cad'])[0])));
    exit;
}

if ($_POST['action'] == 'ver_foto') {
    $sql = mysql_query("SELECT id_clt, id_projeto, id_regiao, ext_foto FROM rh_clt A WHERE A.id_clt = '{$_POST['id_clt']}'") or die('ERRO $sql: ' . mysql_error());
    $row = mysql_fetch_assoc($sql);
    $ext_foto = ($row['ext_foto']) ? $row['ext_foto'] : 'jpg';
    echo '<img id="img_foto" class="h-100" src="../fotosclt/'.$row['id_regiao'] . '_' . $row['id_projeto'] . '_' . $row['id_clt'] . '.' . $ext_foto . '">'; exit;
}

if ($_POST['action'] == 'del_foto') {
    $sql = mysql_query("SELECT id_clt, id_projeto, id_regiao, ext_foto FROM rh_clt A WHERE A.id_clt = '{$_POST['id_clt']}'") or die('ERRO $sql: ' . mysql_error());
    $row = mysql_fetch_assoc($sql);
    if(file_exists("../fotosclt/{$row['id_regiao']}_{$row['id_projeto']}_{$row['id_clt']}.{$row['ext_foto']}")){
        unlink("../fotosclt/{$row['id_regiao']}_{$row['id_projeto']}_{$row['id_clt']}.{$row['ext_foto']}");
    } 
    
    mysql_query("UPDATE rh_clt SET foto = 0 WHERE id_clt = {$_POST['id_clt']} LIMIT 1;");
    exit;
}

/**
 * ARRAY COD RESCIOES
 */
$sql_cod_rescisoes = mysql_query("SELECT codigo FROM rhstatus WHERE tipo = 'recisao'") or die('ERRO $sql_cod_rescisoes: ' . mysql_error());
while($row_cod_rescisoes = mysql_fetch_assoc($sql_cod_rescisoes)) {
    $array_cod_rescisoes[$row_cod_rescisoes['codigo']] = $row_cod_rescisoes['codigo'];
}
//print_array($array_cod_rescisoes);

/**
 * DADOS CLT
 */
$sql = mysql_query("SELECT * FROM rh_clt A WHERE A.id_clt = '$id_clt'") or die('ERRO $sql: ' . mysql_error());
$row = mysql_fetch_assoc($sql);
//print_array($row);
if(!$row){
    echo 'CLT NÃO ENCONTRADO!'; exit;
}

/**
 * DADOS ÚLTIMO FUNCIONÁRIO QUE EDITOU O CLT
 */
$sql_useralter = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '{$row['useralter']}'") or die('ERRO $sql_useralter: ' . mysql_error());
$row_useralter = mysql_fetch_assoc($sql_useralter);

/**
 * DADOS PROJETO
 */
$sql_projeto = mysql_query(" SELECT * FROM projeto WHERE id_projeto = '{$row['id_projeto']}'") or die('ERRO $sql_projeto: ' . mysql_error());
$row_projeto = mysql_fetch_assoc($sql_projeto);

/**
 * DADOS STATUS
 */
$sql_status = mysql_query(" SELECT * FROM rhstatus WHERE codigo = '{$row['status']}'") or die('ERRO $sql_status: ' . mysql_error());
$row_status = mysql_fetch_assoc($sql_status);

/**
 * DADOS CURSO
 */
$sql_curso = mysql_query(" SELECT * FROM curso WHERE id_curso = '{$row['id_curso']}'") or die('ERRO $sql_curso: ' . mysql_error());
$row_curso = mysql_fetch_assoc($sql_curso);

/**
 * DADOS TIPO PG
 */
$sql_tipopg = mysql_query(" SELECT * FROM tipopg WHERE id_tipopg = '{$row['tipo_pagamento']}' AND status_reg = 1") or die('ERRO $sql_tipopg: ' . mysql_error());
$row_tipopg = mysql_fetch_assoc($sql_tipopg);

/**
 * DADOS BANCO
 */
$sql_banco = mysql_query(" SELECT * FROM bancos WHERE id_banco = '{$row['banco']}'") or die('ERRO $sql_banco: ' . mysql_error());
$row_banco = mysql_fetch_assoc($sql_banco);

/**
 * DADOS CTPS
 */
$sql_ctps = mysql_query(" SELECT * FROM controlectps WHERE nome = '{$row['nome']}'") or die('ERRO $sql_ctps: ' . mysql_error());
$row_ctps = mysql_fetch_assoc($sql_ctps);


/**
 * VERIFICA SE CLT ESTA RESCINDIDO
 */
$sql_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '{$row['id_clt']}' AND status = 1") or die('ERRO $sql_rescisao: ' . mysql_error());
$row_rescisao = mysql_fetch_assoc($sql_rescisao);

$ativo = ($row_rescisao['id_recisao']) ? 2 : 1;

/**
 * DADOS EVENTOS
 */
//$eventos = new Eventos();
//$dadosEventos = $eventos->getTerminandoEventos(date("Y-m-d"), $row['id_regiao'], $row['id_projeto'], $row['id_clt']);
$sql_historico = mysql_query("
SELECT * FROM (
    SELECT CONCAT(B.especifica, ' (', A.cod_status, ')') AS texto, A.`data` AS data_inicio, A.data_retorno AS data_fim, A.dias AS dias
    FROM rh_eventos A 
    LEFT JOIN rhstatus B ON (A.cod_status = B.codigo)
    WHERE A.status = 1 AND A.cod_status NOT IN (10) AND A.id_clt = '{$row['id_clt']}'

    UNION

    SELECT 'FÉRIAS' AS texto, A.data_ini AS data_inicio, A.data_fim, A.dias_ferias
    FROM rh_ferias A 
    WHERE A.status = 1 AND A.id_clt = '{$row['id_clt']}'

    UNION

    SELECT 'ADMISSÃO' AS texto, A.data_entrada AS data_inicio, '' AS data_fim, '' AS dias 
    FROM rh_clt A
    WHERE A.id_clt = '{$row['id_clt']}'

    UNION

    SELECT CONCAT(B.especifica, ' (', A.motivo, ')') AS texto, A.data_demi AS data_inicio, '' AS data_fim, '' AS dias 
    FROM rh_recisao A
    LEFT JOIN rhstatus B ON (A.motivo = B.codigo)
    WHERE A.status = 1 AND A.id_clt = '{$row['id_clt']}'
) AS tot
ORDER BY data_inicio") or die('ERRO $sql_historico: ' . mysql_error());
while($row_historico = mysql_fetch_assoc($sql_historico)){
    $array_historico[] = $row_historico;
}

/**
 * DADOS FERIAS
 */
$sql_ferias = mysql_query("
SELECT id_ferias, data_aquisitivo_ini, data_aquisitivo_fim, data_ini, data_fim, dias_ferias
FROM rh_ferias A 
WHERE A.status = 1 AND A.id_clt = '{$row['id_clt']}'
ORDER BY A.data_aquisitivo_ini DESC") or die('ERRO $sql_ferias: ' . mysql_error());
while($row_ferias = mysql_fetch_assoc($sql_ferias)){
    $array_ferias[] = $row_ferias;
}

/**
 * DOCUMENTOS STATUS
 */
$sql_doc_status = mysql_query("
SELECT A.documento, B.data 
FROM rh_documentos A 
LEFT JOIN rh_doc_status B ON (A.id_doc = B.tipo AND B.id_clt = '{$row['id_clt']}' AND B.status_reg = 1)
WHERE A.tipo_contratacao = 2 AND A.status_reg = 1 ORDER BY A.documento") or die('ERRO $sql_doc_status: ' . mysql_error());
while($row_doc_status = mysql_fetch_assoc($sql_doc_status)){
    $array_doc_status[] = $row_doc_status;
}
//print_array($array_doc_status);

/**
 * DOCUMENTOS CLT
 */
$sql_doc_clt = mysql_query("
SELECT A.id_upload, A.arquivo, B.data_cad
FROM upload A 
LEFT JOIN (SELECT id_upload, MAX(data_cad) data_cad FROM documento_clt_anexo WHERE id_clt = '{$row['id_clt']}' AND anexo_status = 1 GROUP BY id_upload) B ON (A.id_upload = B.id_upload)
WHERE A.status_reg = 1 ORDER BY A.ordem") or die('ERRO $sql_doc_clt: ' . mysql_error());
while($row_doc_clt = mysql_fetch_assoc($sql_doc_clt)){
    $array_doc_clt[] = $row_doc_clt;
}
//print_array($array_doc_clt);

/**
 * OUTROS PROJETOS
 */
$sql_outros_projetos = mysql_query("
SELECT A.id_clt, B.nome 
FROM rh_clt A 
LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto) 
WHERE A.cpf = '{$row['cpf']}' AND A.id_clt NOT IN ({$row['id_clt']})
AND (A.status < 60 OR A.status = 70 OR A.status = 80 OR A.status = 200);") or die('ERRO $sql_outros_projetos: ' . mysql_error());
while($row_outros_projetos = mysql_fetch_assoc($sql_outros_projetos)){
    $array_outros_projetos[$row_outros_projetos['id_clt']] = $row_outros_projetos['nome'];
}
//print_array($array_outros_projetos);

/**
 * FOTO CLT
 */
$nome_para_arquivo =  $row['id_antigo'];

if ($row['foto'] == '1') {
    
    $ext_foto = ($row['ext_foto']) ? $row['ext_foto'] : 'gif';
    $foto_clt = '
    <div class="thumbnail text-center no-margin" id="foto">
        <img id="img_foto" class="h-100" src="../fotosclt/'.$row['id_regiao'] . '_' . $row['id_projeto'] . '_' . $row['id_clt'] . '.' . $ext_foto . '">
        <span class="btn btn-sm btn-primary fa fa-upload margin_t15" id="add_foto" style="width: 100%;"> Alterar Foto</span>
        <span id="del_foto" class="btn btn-sm btn-danger fa fa-trash-o margin_t5" style="width: 100%;"> Deletar</span>
    </div>';
} else {
    $foto_clt = '
    <div class="thumbnail text-center no-margin" id="foto">
        <i id="ico_foto" class="h-100 fa fa-user" style="font-size: 90px!important;"></i>
        <span class="btn btn-sm btn-primary fa fa-upload margin_t15" id="add_foto" style="width: 100%;"> Adicionar Foto</span>
        <span id="del_foto" class="btn btn-sm btn-danger fa fa-trash-o margin_t5 disabled" style="width: 100%;"> Deletar</span>
    </div>';
}


$qr_processo = mysql_query("SELECT proc_interno_id FROM processos_interno WHERE id_clt = '{$row['id_clt']}' AND proc_interno_status = 1");
$row_processo = mysql_fetch_assoc($qr_processo);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$botoesMenu = $botoes->getBotoesModulo(56);
//print_array($botoesMenu);

$caminho = (!empty($_REQUEST['caminho'])) ? $_REQUEST['caminho'] : 0;
$breadcrumb_caminhos[0] = array("Lista Projetos" => "/intranet/rh/ver.php", "Visualizar Projeto" => "/intranet/rh/ver.php?projeto={$row['id_projeto']}", "Lista Participantes" => "/intranet/rh_novaintra/bolsista.php?projeto={$row['id_projeto']}");
$breadcrumb_caminhos[1] = array("Gestão de RH"=>"index.php", "Edição de Participantes"=>"clt.php");
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Visualizar Participante");
$breadcrumb_pages = $breadcrumb_caminhos[$caminho];

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Visualizar Participante</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >-->
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <!--<link href="../css/progress.css" rel="stylesheet" type="text/css">-->
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <!--<link href="../resources/css/add-ons.min.css" rel="stylesheet">-->
        <link href="../resources/css/add-ons.min.css" rel="stylesheet">
        <link href="../resources/css/dropzone.css" rel="stylesheet">
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <form id="form1" method="post"></form>
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Visualizar Participante</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs margin_b10">
                        <!--li class="active"><a href=".projEstatistica" data-toggle="tab">Estatística</a></li-->
                        <li class="active"><a href=".cltResumo" data-toggle="tab">Resumo</a></li>
                        <!--li class=""><a href=".cltEdicao" data-toggle="tab">Edição</a></li-->
                        <li class=""><a href=".cltDocumentos" data-toggle="tab">Documentos</a></li>
                        <li class=""><a href=".cltEventos" data-toggle="tab">Eventos</a></li>
                        <?php if ($ativo && $ACOES->verifica_permissoes(63)) { ?><li class=""><a href=".cltConta" data-toggle="tab">Conta</a></li><?php } ?>
                    </ul>
                </div>
            </div>
            <div id="fileQueue"></div>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane cltResumo active">
                    <div class="row">
                        <div class="col-xs-12">
                            <?php if ($_REQUEST['sucesso'] == 'cadastro') { ?>
                                <div class="alert alert-dismissable alert-success avisos_eventos">
                                    <strong>Participante cadastrado com sucesso!</strong>
                                </div>
                            <?php } ?>
                            <h4><strong>MATRÍCULA: <?= sprintf("%05s", $row['matricula']); ?></strong></h4>
                            <div class="panel panel-default no-padding-l">
                                <div class="stat-cell col-xs-2 no-border-vr no-border-l no-padding valign-middle text-center hidden-xs">
                                    <div class="col-xs-offset-1 col-xs-10 no-padding">
                                        <?= $foto_clt ?>
<!--                                        <input type="file" id="bt_enviar" name="bt_enviar"/>
                                        <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../imagens/excluir_foto.gif"></a>-->
                                    </div> <!-- /.stat-cell -->
                                </div> <!-- /.stat-cell -->
                                <div class="stat-cell col-xs-10 no-padding valign-middle bordered no-border" style="border-left: 1px solid!important;">
                                    <div class="stat-rows">
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong><?= /*$rh->Clt->getMatriculaPorProjeto()*/ sprintf("%05s", $row['matricula']) . ' - ' . $row['nome'] ?></strong>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Nº do processo:</strong> <?= sprintf("%05s", $row['n_processo']) . ' / ' . sprintf("%05s", $row['matricula']) ?>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>CPF:</strong> <?= $row['cpf'] ?>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Data de Entrada:</strong> <?= implode('/', array_reverse(explode('-', $row['data_entrada']))) ?>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Data de Experiência:</strong>
                                                <?php
                                                if($row['prazoexp'] == 2){
                                                    $prazo = 'P45D';
                                                    $prazo2 = 'P45D';
                                                }else if($row['prazoexp'] == 3){
                                                    $prazo = 'P60D';
                                                    $prazo2 = 'P30D';
                                                }else if($row['prazoexp'] == 1){
                                                    $prazo = 'P30D';
                                                    $prazo2 = 'P60D';
                                                }
                                                else if($row['prazoexp'] == 6){
                                                    $prazo = 'P60D';
                                                }
                                                else if($row['prazoexp'] == 5){
                                                    $prazo = 'P45D';
                                                }
                                                else if($row['prazoexp'] == 4){
                                                    $prazo = 'P30D';
                                                }

                                                $date = new DateTime($row['data_entrada']);
                                                $periodo = $date->add(new DateInterval($prazo));
                                                echo $periodo->format('d/m/Y');
                                                ?>
                                            </div>
                                            <?php if(isset($prazo2)){ ?>
                                                <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                    <strong>Data de Experiência: </strong>
                                                    <?php
                                                    $periodo2 = $periodo->add(new DateInterval($prazo2));
                                                    echo $periodo2->format('d/m/Y');
                                                    ?>
                                                </div>
                                            <?php }?>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Projeto:</strong> <?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] ?>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle text-danger text-bold">
                                                <?= (in_array($row['status'], $array_cod_rescisoes)) ? "Data de saída: " . implode('/',array_reverse(explode('-',$row['data_demi']))) : null; ?>&nbsp;
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-12 stat-cell padding-sm valign-middle text-bold <?= (in_array($row['status'], $array_cod_rescisoes) ? 'text-danger' : '') ?>">
                                                <?= $row_status['especifica'] ?>&nbsp;
                                            </div>
                                        </div>
                                        <?php 
//                                        if($row['orgao']) {
//                                            if ($row['verifica_orgao']) {
//                                                $msg_orgao = '<span> Orgão regulamentador verificado. </span>';
//                                                $cor_orgao = 'success';
//                                            } else {
//                                                $msg_orgao = '<span>Orgão regulamentador não verificado.</span>';
//                                                $cor_orgao = 'danger';
//                                            }
//                                        } 
                                        ?>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle tr-bg-warning">
                                                <?php
                                                if($row_useralter['id_funcionario']){
                                                    echo "Ultima Alteração feita por <strong>{$row_useralter['nome']}</strong></br>Em " . implode('/',array_reverse(explode('-',$row['dataalter'])));
                                                }
                                                else {
                                                    echo "Nenhuma Alteração de Cadastro";
                                                }
                                                ?>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle tr-bg-<?=$cor_orgao?> text-<?=$cor_orgao?>">
                                                <?=$msg_orgao?>
                                            </div>
                                        </div>
                                    </div> <!-- /.stat-rows -->
                                </div> <!-- /.stat-cell -->
                            </div>
                            <div class="col-xs-12 no-padding valign-middle">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <div data-toggle="collapse" data-parent="#accordion" data-target="#collapseThree" class="collapsed pointer">
                                                <i class="fa fa-sort"></i> Mais Informações
                                            </div>
                                        </h4>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
                                        <div class="panel-body tr-bg-warning">
                                            <p>
                                                <strong>Atividade:</strong> <?= $row_curso['id_curso'] . ' - ' . $row_curso['nome'] ?> 
                                                <?php if ($row_curso['cbo_codigo']) { ?>
                                                    (<?= $row_curso['cbo_codigo'] ?>)
                                                <?php }?>
                                            </p>
                                            <p><strong>Unidade:</strong> <?= $row['locacao'] ?></p>
                                            <p>
                                                <strong>Salário:</strong>
                                                <?php if (!empty($row_curso['salario'])) { ?>
                                                R$ <?= number_format($row_curso['salario'],2, ',', '.') ?>
                                                <?php } else { ?>
                                                    <i>Não informado</i>
                                                <?php } ?>
                                                    
                                                <?php if($row_curso['horista_plantonista']){ ?>
                                                    <?php if(!empty($row_curso['hora_mes'])){ ?>    
                                                        <strong>Salario Hora:</strong>    
                                                        R$ <?= number_format($row_curso['salario']/$row_curso['hora_mes'],2, ',', '.'); ?>
                                                    <?php }else{ ?>
                                                        <strong>Salario Hora:</strong>    
                                                        <b>(Função Sem horário mês)</b>
                                                    <?php } ?>
                                                <?php } ?>
                                                    
                                                <strong class="margin_l20">Tipo de Pagamento:</strong> 
                                                <?php 
                                                if (empty($row_tipopg['tipopg'])) {
                                                    echo "<i>Não informado</i>";
                                                } else { 
                                                    echo $row_tipopg['tipopg'];
                                                } ?>
                                            </p>
                                            <p>
                                                <strong>Agência:</strong> 
                                                <?php if (empty($row_banco['agencia'])) {
                                                    echo "<i>Não informado</i>";
                                                } else {
                                                    echo $row_banco['agencia'];
                                                } ?>
                                                <strong class="margin_l20">Conta:</strong> 
                                                <?php if (empty($row_banco['conta'])) {
                                                    echo "<i>Não informado</i>";
                                                } else {
                                                    echo $row_banco['conta'];
                                                } ?>
                                                <strong class="margin_l20">Banco:</strong>
                                                <?php if (empty($row_banco['nome'])) {
                                                    echo "<i>Não informado</i>";
                                                } else {
                                                    echo $row_banco['nome'];
                                                } ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($row['observacao'])) { ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="note note-warning">
                                    <h4>Observações:</strong></h4>
                                    <p><?php echo $row['observacao'] ?></p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(count($array_outros_projetos) > 0) { ?>
                    <div class="row">
                            <div class="col-xs-12">
                                <div class="note note-success">
                                    <h4>O CLT também trabalha no(s) seguinte(s) projeto(s): </h4>
                                    <ul>
                                    <?php foreach ($array_outros_projetos as $key => $value) { ?>
                                        <li><?php echo $key ?> - <?php echo $value ?></li>
                                    <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class='panel panel-default'>
                                <div class='panel-heading text-bold'><i class="fa fa-edit"></i> MENU DE EDIÇÃO</div>
                                <div class='panel-body'>
                                    <?php
                                    if ($row_ctps['id_controle']){ 
                                        $id_funcionario = $row_ctps['id_user_ent'];
                                        $target = 'target="_blank"';
                                        $link_ctps = "ctps_entregar.php?case=1&id_regiaoiao=$id_reg&id=$id_funcionario";
                                    } else {
                                        $link_ctps = "ver_clt.php?id_regiao=$id_reg&id_clt=$id_clt&ant=$id_ant&id_projeto=$id_pro&pagina=bol&entregaCTPS=0";
                                        $target = '';
                                    }

                                    if (!empty($row['pis'])) {
                                        $statusBotao = 'none';
                                        $emissao = true;
                                    } else {
                                        $statusBotao = 'inline';
                                        $emissao = false;
                                    } 
                                    ?>
                                    
                                    <?php foreach ($botoesMenu as $key => $value) { ?>
                                        <?php if (in_array($value['status_clt'], [0, $ativo])) { 
                                            $data_link = "
                                                data-id_clt='{$row['id_clt']}' 
                                                data-id_projeto='{$row['id_projeto']}' 
                                                data-id_regiao='{$row['id_regiao']}' 
                                                    
                                                data-clt='{$row['id_clt']}' 
                                                data-id_pro='{$row['id_projeto']}' 
                                                data-id_reg='{$row['id_regiao']}' 
                                                
                                                data-id='{$row['id_clt']}' 

                                                data-pro='{$row['id_projeto']}' 
                                                data-reg='{$row['id_regiao']}' 
                                                    
                                                data-mes=".date('m')."
                                                data-ano=".date('Y')."
                                                ";
                                            
                                            ?>
                                    
                                            <div class="col-xs-12 col-md-3 col-sm-6 margin_b20">
                                                <button 
                                                    type="button" 
                                                    data-url="<?php echo $value['nova_url'] ?>" 
                                                    data-target="<?php echo ($value['nova_aba']) ? "_blank" : '' ?>" 
                                                    <?php echo $data_link ?>
                                                    class="col-xs-12 btn text-sm <?php echo (in_array($value['botoes_id'], [326, 327, 350, 351])) ? "btn-primary" : 'btn-default' ?> link-sem-get"
                                                    style="text-transform: uppercase;"
                                                >
                                                    <?php echo ($value['novo_ico']) ? "<i class='fa {$value['novo_ico']}'></i> " : ''; echo $value['botoes_nome']; ?>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
                <div class="tab-pane cltConta">
                    <?php
                    if ($ativo && $ACOES->verifica_permissoes(63)) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading"><i class="fa fa-send-o"></i> ENCAMINHAMENTO DE CONTA</div>
                        <div class="panel-body">
                            <form action="/intranet/relatorios/gerar_relatorio.php" method="post" name="form1" target="_blank" class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-xs-4">
                                        <label class="control-label">Escolha o Banco:</label>
                                        <select name="banco" id="banco" class="form-control">
                                            <?php
                                            $bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = {$row['id_projeto']} AND status_reg = 1");
                                            while ($rb = mysql_fetch_assoc($bancos)) {
                                                echo "<option value='{$rb['id_banco']}'>{$rb['nome']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-4">
                                        <label class="control-label">&nbsp;</label><br>
                                        <button type="submit" class="btn btn-primary" value="Gerar Encaminhamento de Conta">
                                            <i class='fa fa-send-o'></i> Gerar Encaminhamento de Conta </button>
                                        <input type="hidden" name="tipo" id="tipo" value="2">
                                        <input type="hidden" name="documento" id="documento" value="encaminhamento_banco">
                                        <input type="hidden" name="clt" id="clt" value="<?= $row['id_clt'] ?>">

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>      
                    <?php } ?>
                </div>
                <div class="tab-pane cltEventos">
                    <div class="panel panel-default">
                        <div class="panel-heading"><i class="fa fa-ambulance"></i> CONTROLE DE EVENTOS</div>
                        <div class="panel-body">
                            <?php if(count($array_historico) > 0) { ?>
                            <table class="table table-striped table-condensed table-hover table-bordered valign-middle">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Data de Retorno</th>
                                        <th class="text-center">Dias</th>
                                    </tr>
                                </thead>
                                <?php foreach ($array_historico as $key => $value) { ?>
                                <tr>
                                    <td class="text-uppercase"><?= $value['texto'] ?></td>
                                    <td class='text-center'><?= implode('/', array_reverse(explode('-',$value['data_inicio']))) ?></td>
                                    <td class='text-center'><?= ($value['data_retorno'] != '0000-00-00') ? implode('/', array_reverse(explode('-',$value['data_fim']))) : 'Não Informado' ?></td>
                                    <td class='text-center'><?= $value['dias'] ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            <?php } else { ?>
                                <div class="alert alert-info">NENHUM EVENTO ENCONTRADO</div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading"><i class="fa fa-plane"></i> CONTROLE DE FÉRIAS</div>
                        <div class="panel-body">
                            <?php if(count($array_ferias) > 0) { ?>
                            <table class="table table-striped table-condensed table-hover table-bordered valign-middle">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Período Aquisitivo</th>
                                        <th class="text-center">Inicio</th>
                                        <th class="text-center">Fim</th>
                                        <th class="text-center">Dias</th>
                                    </tr>
                                </thead>
                                <?php foreach ($array_ferias as $key => $value) { ?>
                                <tr>
                                    <td class='text-center'><a href="/intranet/?class=ferias/processar&method=gerarPdf&id_ferias=<?= $value['id_ferias'] ?>&value=pdf" target="_blank" class="btn btn-xs btn-danger"><i class="fa fa-file-pdf-o"></i></a></td>
                                    <td class=''><?= implode('/', array_reverse(explode('-',$value['data_aquisitivo_ini']))) . ' - ' . implode('/', array_reverse(explode('-',$value['data_aquisitivo_fim']))) ?></td>
                                    <td class='text-center'><?= implode('/', array_reverse(explode('-',$value['data_ini']))) ?></td>
                                    <td class='text-center'><?= implode('/', array_reverse(explode('-',$value['data_fim']))) ?></td>
                                    <td class='text-center'><?= $value['dias_ferias'] ?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            <?php } else { ?>
                                <div class="alert alert-info">NENHUMA FÉRIAS ENCONTRADO</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane cltDocumentos clearfix">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading text-bold">
                                <i class="fa fa-folder"></i> UPLOAD DOCUMENTOS CLT
                            </div>
                            <div class="panel-body">
                                <input type="hidden" id="upload_documento">
                                <input type="hidden" id="upload_foto">
                                <table class="table table-condensed table-striped table-hover valign-middle">
                                    <thead>
                                        <tr class="bg-dark-gray">
                                            <th class="text-center"><strong>DOCUMENTO</strong></th>
                                            <th class="text-center"><strong>STATUS</strong></th>
                                            <th class="text-center"><strong>DATA</strong></th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($array_doc_clt as $key => $value) { ?>
                                        <tr>
                                            <td><?= $value['arquivo'] ?></td>
                                            <td class='text-center' id="trdoc<?= $value['id_upload'] ?>">
                                                <?php if(!$row_processo['proc_interno_id'] && $value['id_upload'] != 14) { ?>
                                                    <?php if($value['data_cad']) { ?>
                                                        <button type="button" class="btn btn-xs btn-info ver_anexo" data-clt="<?= $row['id_clt'] ?>" data-upload="<?= $value['id_upload'] ?>"><i class="fa fa-search"></i></button>
                                                    <?php } ?>
                                                        <button type="button" class="btn btn-xs btn-success doc_upload" data-id="<?= $value['id_upload'] ?>"><i class="fa fa-upload"></i></button>
                                                <?php } else { ?>
                                                    <a type="button" class="btn btn-xs btn-info ver_anexo" href="../rh/ver_abertura_proc.php?clt=<?= $row['id_clt'] ?>"><i class="fa fa-search"></i></a>
                                                <?php } ?>
                                            </td>
                                            <td class='text-center' id="trdocdata<?= $value['id_upload'] ?>"><?= ($row_processo['proc_interno_id']) ? $row_processo['proc_interno_id'] : implode('/', array_reverse(explode('-',  explode(' ',  $value['data_cad'])[0]))) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading text-bold">
                                <i class="fa fa-folder-o"></i> CONTROLE DE DOCUMENTOS
                            </div>
                            <div class="panel-body">
                                <table class="table table-condensed table-striped table-hover valign-middle">
                                    <thead>
                                        <tr class="bg-dark-gray">
                                            <th class="text-center"><strong>DOCUMENTO</strong></th>
                                            <th class="text-center"><strong>STATUS</strong></th>
                                            <th class="text-center"><strong>DATA</strong></th>
                                        </tr>
                                    </thead>
                                    <?php foreach ($array_doc_status as $key => $value) { ?>
                                        <tr>
                                            <td><?= $value['documento'] ?></td>
                                            <td class='text-center'><button type="button" class="disabled btn btn-xs btn-<?= ($value['data']) ? 'success' : 'danger' ?>"><i class="fa fa-thumbs-o-<?= ($value['data']) ? 'up' : 'down' ?>"></i></button></td>
                                            <td class='text-center'><?= implode('/', array_reverse(explode('-', $value['data']))) ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form enctype="multipart/form-data" id="drop"></form>

            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/dropzone.js" type="text/javascript"></script>
        <!--<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>-->
        <!--<script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>-->
        <script language="javascript">
            Dropzone.autoDiscover = false;
            
            /**
             * 
             * @type Dropzone
             * UPALOAD DA FOTO
             */
            Dropzone.options.myAwesomeDropzone = false;
            var myDropzoneFoto = new Dropzone('#upload_foto', { // Make the whole body a dropzone
                url: "../uploadfy/scripts/uploadify.php", // Set the url
                maxFiles: 1,
                acceptedFiles: ".jpg,.jpeg,.png,.gif",
                autoQueue: true, // Make sure the files aren't queued until manually added
                clickable: "#add_foto", // Define the element that should be used as click trigger to select files.
                sending: function(file, xhr, formData) {
                    formData.append("clt", <?= $row['id_clt'] ?>); // Append all the additional input data of your form here!
                    formData.append("projeto", <?= $row['id_projeto'] ?>); // Append all the additional input data of your form here!
                    formData.append("regiao", <?= $row['id_regiao'] ?>); // Append all the additional input data of your form here!
                    formData.append("action", 'upload_foto'); // Append all the additional input data of your form here!
                },
                complete: function(progress) {
                    $.post('', { action: 'ver_foto', id_clt: <?= $row['id_clt'] ?> }, function (data){
                        $('#ico_foto, #img_foto').remove();
                        $('#foto').prepend(data);
                        $('#del_foto').removeClass('disabled');
                        $('#add_foto').html(' Alterar Foto');
                    });
                }
            });
            
            /**
             * 
             * @type Dropzone
             * UPALOAD DOS DOCUMENTOS
             */
            var myDropzoneDocumentos = new Dropzone('#upload_documento', { // Make the whole body a dropzone
                url: "../uploadfy/scripts/uploadify.php", // Set the url
//                maxFiles: 1,
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf",
                autoQueue: true, // Make sure the files aren't queued until manually added
                clickable: '#upload_documento', // Define the element that should be used as click trigger to select files.
                init: function() {
                    DropZone = this;
                    $("#removeAllImages").click(function(){DropZone.removeAllFiles();})
                },
                sending: function(file, xhr, formData) {
                    formData.append("id_clt", <?= $row['id_clt'] ?>); // Append all the additional input data of your form here!
                    formData.append("action", 'upload_documentos'); // Append all the additional input data of your form here!
                }
            });
            
            
            $('body').on('click', '.doc_upload', function(){
                $this = $(this);
                
                myDropzoneDocumentos.on('sending',function(file, xhr, formData) {
                    formData.append("id_documento", $this.data('id')); // Append all the additional input data of your form here!
                });
                
                myDropzoneDocumentos.on('complete',function(progress) {
                    $.post('', { action: 'fim_upload_doc', id_clt: <?= $row['id_clt'] ?>, id_doc: $this.data('id') }, function (data){
                        $('#trdoc'+$this.data('id')).find('.ver_anexo').remove();
                        $('#trdoc'+$this.data('id')).prepend('<button type="button" class="btn btn-xs btn-info ver_anexo" data-clt="<?= $row['id_clt'] ?>" data-upload="'+$this.data('id')+'"><i class="fa fa-search"></i></button>');
                        $('#trdocdata'+$this.data('id')).html(data);
                    });
                });
                
                $('#upload_documento').trigger('click');
            });
            
            /**
             * 
             * ABRIR DIALOG DOS ANEXOS DE DOCUMENTOS
             */
            $('body').on('click', '.ver_anexo', function(){
                $this = $(this);
                $.post('', { id_clt: $this.data('clt'), id_upload: $this.data('upload'), action: 'ver_anexo' }, function(data){
                    bootAlert(data, 'Ver Documentos', null, 'info');
                });
            });
            
            /**
             * 
             * DELETAR FOTO
             */
            $('body').on('click', '#del_foto', function(){
                bootConfirm('Deseja realmente excluir a foto?', 'Confirmação', function(data){
                    if(data === true){
                        $.post('', { action: 'del_foto', id_clt: <?= $row['id_clt'] ?> }, function(data){
                            $('#img_foto').remove();
                            $('#foto').prepend('<i id= "ico_foto" class="h-100 fa fa-user" style="font-size: 90px!important;"></i>');
                            $('#del_foto').addClass('disabled');
                            $('#add_foto').html(' Adicionar Foto');
                        });
                    }
                }, 'warning');
                    
            });
            
            /**
             * 
             * DELETAR DOCUMENTO
             */
            $('body').on('click', '.deleteDoc', function(){
                $this = $(this);
                bootConfirm('Deseja realmente excluir o anexo?', 'Confirmação', function(data){
                    if(data === true) {
                        $.post('', { id: $this.data('key'), action: 'deleteDoc' }, function(teste){
                            $this.parent().parent().remove();
                        });
                    }
                }, 'warning');
            });
            
            
//
//                $("#BtnCtpsReceber").click(function(){
//                     $("#frmCtpsReceber").attr("action","\ctps.php");
//                     $("#frmCtpsReceber").submit();
//                 });                
//
//                $("#BtnCtpsEntregar").click(function(){
//                     $("#frmCtpsEntregar").attr("action","\ctps.php");
//                     $("#frmCtpsEntregar").submit();
//                 });                
//
//                $('#Upar').click(function() {
//                    if ($('#select_doc').val() != '') {
//                        $('#uploadDoc').uploadifySettings('scriptData', {'tipo_documento': $('#select_doc').val()});
//                        $('#uploadDoc').uploadifyUpload();
//                        $('#BarUploadDoc').slideDown('slow');
//
//
//                    } else {
//                        alert('Selecione um tipo de documento');
//                    }
//                });
//            });
        </script>
    </body>
</html>