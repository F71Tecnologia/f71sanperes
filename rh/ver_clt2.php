<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('../upload/classes.php');
include('../classes/funcionario.php');
include('../classes/formato_data.php');
include('../classes/formato_valor.php');
include('../classes/EventoClass.php');
include('../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$ACOES = new Acoes();

//PEGANDO O ID DO CADASTRO

$id = 1;
$id_clt = $_REQUEST['clt'];
$id_ant = $_REQUEST['ant'];
//$id_pro = $_REQUEST['pro'];
//$id_reg = $id_reg;
$id_reg = $usuario['id_regiao'];
$id_user = $_COOKIE['logado'];
$pagina = $_REQUEST['pagina'];
$data = date("Y-m-d");
$eventos = new Eventos();
$dadosEventos = $eventos->getTerminandoEventos($data, $id_reg, $id_pro, $id_clt);

$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM rh_clt WHERE id_clt = $id_clt");
$row = mysql_fetch_array($result);
$id_pro = $row['id_projeto'];

$result_data_entrada = mysql_query("
SELECT data_entrada, DATE_ADD(data_entrada, INTERVAL '90' DAY) AS data_contratacao, CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <= CURDATE() THEN 'Em experiência até ' ELSE 'Aguardando' END AS status_contratacao FROM rh_clt WHERE id_clt = '$id_clt'") or die(mysql_error());
$row2 = mysql_fetch_assoc($result_data_entrada);

$data_contratacao = implode('/', array_reverse(explode('-', $row2['data_contratacao'])));
$status_contratacao = $row2['status_contratacao'];

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro'");
$row_pro = mysql_fetch_array($result_pro);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' AND id_projeto = '$id_pro'");

if ($row['status'] == '62') {
    $texto = "<strong>Data de saída:</strong> $row[data_saida2]";
} else {
    $texto = NULL;
}

$nome_para_arquivo = $row['1'];

if ($row['foto'] == '1') {
    $nome_imagem = $id_reg . '_' . $id_pro . '_' . $row['0'] . '.gif';
} else {
    $nome_imagem = 'semimagem.gif';
}

$qr_status = mysql_query("SELECT tipo FROM rhstatus WHERE codigo = '$row[status]'");
$ativo = (mysql_result($qr_status, 0) == "recisao") ? false : true;

$sql_qtd_clt = mysql_query("SELECT A.*, B.nome AS nome_projeto
                            FROM rh_clt AS A
                            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                            WHERE A.nome = '{$row['nome']}' AND A.cpf = '{$row['cpf']}' AND A.pis = '{$row['pis']}' ORDER BY B.nome") or die(mysql_error());
$tot_clt = mysql_num_rows($sql_qtd_clt);

/*
 *  para trazer as licensas médicas com mais de 15 dias
 */
if ($row['status'] == 20) {
    $licenca = $eventos->getEventosSeguidos($id_clt, 20);
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Visualizar Participante");
$breadcrumb_pages = array("Lista Projetos" => "../ver2.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);");
$breadcrumb_attr = array(
    "Visualizar Projeto" => "class='link-sem-get' data-projeto='$id_pro' data-form='form1' data-url='../ver2.php'",
    "Lista Participantes" => "class='link-sem-get' data-projeto='$id_pro' data-form='form1' data-url='../bolsista2.php'"
);
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
            .icon-anexo{
                width: 20px;
                height: 20px;
            }
            .disable, .disable:hover{
                opacity: .3;
                background-color: transparent;
                -webkit-box-shadow: none;
                -moz-box-shadow:    none;
                box-shadow:         none;
            }
        </style>
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <form id="form1" method="post"></form>
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Visualizar Participante</small></h2></div>
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
                        <li class=""><a href=".cltConta" data-toggle="tab">Conta</a></li>
                    </ul>
                </div>
            </div>
            <div id="fileQueue"></div>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane cltResumo active">
                    <?php if ($licenca['soma'] > 15) { ?>
                        <div class="alert alert-dismissable alert-danger avisos_eventos">
                            <img src="../imagens/icones/icon-exclamation.gif" title="Atenção">
                            <strong>Atenção:</strong> Este funcionário possui licença médica com mais de <strong>15 dias</strong>. É nesserário marcar perícia.
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php if ($_REQUEST['sucesso'] == 'cadastro') { ?>
                                <div class="alert alert-dismissable alert-success avisos_eventos">
                                    <strong>Participante cadastrado com sucesso!</strong>
                                </div>
                            <?php } ?>
                            <h4><strong>MATRÍCULA: <?=formato_matricula($row['matricula'])?></strong></h4>
                            <div class="stat-panel">
                                <div class="stat-cell col-xs-2 no-border-vr no-border-l no-padding valign-middle text-center tr-bg-active hidden-xs">
                                    <img src="../fotosclt/<?= $nome_imagem ?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;"/>
                                    <input type="file" id="bt_enviar" name="bt_enviar"/>
                                    <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../imagens/excluir_foto.gif"></a>
                                </div> <!-- /.stat-cell -->
                                <div class="stat-cell col-xs-10 no-padding valign-middle bordered no-border-vr tr-bg-active">
                                    <div class="stat-rows">
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong><?=$row['campo3']?> - <?=$row['nome']?></strong>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Nº do processo:</strong> <?=formato_num_processo($row['n_processo'])?> / <?=formato_matricula($row['matricula'])?>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>CPF:</strong> <?=$row['cpf']?>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Data de Entrada:</strong> <?=$row['nova_data']?>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                                <strong>Projeto:</strong> <?= $row_pro['id_projeto'] ?> - <?= $row_pro['nome'] ?>
                                            </div>
                                            <?php
                                            if ($row['status'] == 200) {
                                                $nome_qr_status1 = 'Aguardando Demissão';
                                                $cor_qr_status1 = 'danger';
                                            } else {
                                                if ($status_contratacao == 'Contratado') {
                                                    $nome_qr_status1 = $status_contratacao;
                                                    $cor_qr_status1 = 'primary';
                                                } elseif ($status_contratacao == 'Em experiência até ') {
                                                    $nome_qr_status1 = $status_contratacao . ' ' . $data_contratacao;
                                                    $cor_qr_status1 = 'danger';
                                                } elseif ($status_contratacao == 'Aguardando') {
                                                    $nome_qr_status1 = $status_contratacao;
                                                    $cor_qr_status1 = 'black';
                                                }
                                                $qr_status = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$row[status]'");
                                                if ($row['status'] != 10) {
                                                    $nome_qr_status2 = mysql_result($qr_status, 0);
                                                    $cor_qr_status2 = 'danger';
                                                } else {
                                                    $nome_qr_status2 = mysql_result($qr_status, 0);
                                                    $cor_qr_status2 = 'primary';
                                                }
                                            } ?>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle text-<?=$cor_qr_status1?>">
                                                <?=$nome_qr_status1?>&nbsp;
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle text-<?=$cor_qr_status2?>">
                                                <?=$nome_qr_status2?>&nbsp;
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle text-danger">
                                                <?=$texto?>&nbsp;
                                            </div>
                                        </div>
                                        <?php if (!empty($row['orgao'])) {
                                            if (!empty($row['verifica_orgao'])) {
                                                $msg_orgao = '<span> Orgão regulamentador verificado. </span>';
                                                $cor_orgao = 'success';
                                            } else {
                                                $msg_orgao = '<span>Orgão regulamentador não verificado.</span>';
                                                $cor_orgao = 'danger';
                                            }
                                        } ?>
                                        <div class="stat-row">
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle tr-bg-warning">
                                                <?='Ultima Alteração feita por <strong>'.$row_user2['nome1'].'</strong> na data '.$row['dataalter2']?>
                                            </div>
                                            <div class="col-xs-6 stat-cell padding-sm valign-middle text-<?=$cor_orgao?>">
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
                                            <p data-toggle="collapse" data-parent="#accordion" data-target="#collapseThree" class="collapsed pointer">
                                                <i class="fa fa-sort"></i> Mais Informações
                                            </p>
                                        </h4>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
                                        <div class="panel-body tr-bg-warning">
                                            <?php
                                            $get_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
                                            $atividade = mysql_fetch_assoc($get_atividade);
                                            $get_pg = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$row[tipo_pagamento]'");
                                            $pg = mysql_fetch_assoc($get_pg);

                                            if ($row['banco'] == '9999') {
                                                $nome_banco = $row['nome_banco'];
                                            } else {
                                                $get_banco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[banco]'");
                                                $row_banco = mysql_fetch_array($get_banco);
                                                $nome_banco = $row_banco[0];
                                            } ?>
                                            <p>
                                                <strong>Atividade:</strong> <?= $atividade['id_curso'] ?> - <?= $atividade['nome'] ?> 
                                                <?php if (!empty($atividade['cbo_codigo'])) { ?>
                                                    (<?=$atividade['cbo_codigo']?>);
                                                <?php } ?>
                                            </p>
                                            <p><strong>Unidade:</strong> <?= $row['locacao'] ?></p>
                                            <p>
                                                <strong>Salário:</strong>
                                                <?php if (!empty($atividade['salario'])) { ?>
                                                    R$ <?=number_format($atividade['salario'], 2, ',', '.')?>
                                                <?php } else { ?>
                                                    <i>Não informado</i>
                                                <?php } ?>
                                                <strong class="margin_l20">Tipo de Pagamento:</strong> 
                                                <?php if (!empty($pg['tipopg'])) {
                                                    echo $pg['tipopg'];
                                                } else { 
                                                    echo "<i>Não informado</i>";
                                                } ?>
                                            </p>
                                            <p>
                                                <strong>Agência:</strong> 
                                                <?php if (!empty($row['agencia'])) {
                                                    echo $row['agencia'];
                                                } else {
                                                    echo "<i>Não informado</i>";
                                                } ?>
                                                <strong class="margin_l20">Conta:</strong> 
                                                <?php if (!empty($row['conta'])) {
                                                    echo $row['conta'];
                                                } else {
                                                    echo "<i>Não informado</i>";
                                                } ?>
                                                <strong class="margin_l20">Banco:</strong>
                                                <?php if (!empty($nome_banco)) {
                                                    echo $nome_banco;
                                                } else {
                                                    echo "<i>Não informado</i>";
                                                } ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($row['observacao'])) { ?>
                                <div class="col-xs-12 note note-warning">
                                    <h4>Observações:</strong></h4>
                                    <p><?=$row['observacao']?></p>
                                </div>
                            <?php } 
                            $data = date("d/m/Y");
                            if(count($dadosEventos) > 0) { ?>
                                <div class="col-xs-12 note note-danger">
                                    <?php foreach ($dadosEventos as $eventos) { ?>
                                        <?php $tipo = ($eventos['dias_restantes'] != 0) ? "danger" : "success"; ?>
                                        <div class="col-xs-6 text-<?=$tipo?>">
                                            <p><?="<strong>{$eventos['data_retorno']}</strong> termino do evento {$eventos['status_de']}, restando {$eventos['dias_restantes']} dias para o evento"?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } 
                            if ($tot_clt > 1) { ?>
                                <div class="col-xs-12 note note-danger">
                                    <h4>Colaborador trabalha em mais de uma unidade:</h4>
                                    <?php while ($row_clt = mysql_fetch_assoc($sql_qtd_clt)) { ?>
                                        <p class="col-xs-4"><?=$row_clt['nome_projeto']?></p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h3>MENU DE EDIÇÃO</h3>
                            <hr>
                            <?php
                            // Consulta para Links
                            $result_entregar = mysql_query("SELECT * FROM controlectps WHERE nome = '$row[nome]'");
                            $num_row_entregar = mysql_num_rows($result_entregar);
                            if ($num_row_entregar != "0") {
                                $row_entregar = mysql_fetch_array($result_entregar);
                                $target = 'target="_blank"';
                                $link_ctps = "../ctps_entregar2.php?case=1&regiao=$id_reg&id=$row_entregar[0]";
                            } else {
                                $link_ctps = "ver_clt2.php?reg=$id_reg&clt=$id_clt&ant=$id_ant&pro=$id_pro&pagina=bol&entregaCTPS=0";
                                $target = '';
                            }

                            if (!empty($row['pis'])) {
                                $statusBotao = 'none';
                                $emissao = true;
                            } else {
                                $statusBotao = 'inline';
                                $emissao = false;
                            } ?>
                            <!--
                            <?php if ($ACOES->verifica_permissoes(72) && $ativo) { ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="abertura_processo2.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-pagina="<?= $pagina ?>" data-reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Abertura de Processo
                                    </div>
                                </div>
                            <?php }
                            if ($ACOES->verifica_permissoes(14)) { ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="alter_clt.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-pagina="<?= $pagina ?>" class="col-xs-12 btn btn-default">
                                        Editar
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="formulario_dependentes_ir.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Dependentes IR
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="direction/index2.php" data-clt="<?= $row['0'] ?>" class="col-xs-12 btn btn-default">
                                        Mapa de Deslocamento
                                    </div>
                                </div>
                            <?php }
                            //VERIFICA SE O PROJETO ESTÁ DESATIVADO
                            if ($row_pro['status_reg'] == 1) {
                                if ($ACOES->verifica_permissoes(15) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../tvsorrindo.php" data-bol="<?= $row['id_antigo'] ?>" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-tipo="2" class="col-xs-12 btn btn-default">
                                            TV Sorrindo
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(78) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="salariofamilia/safami.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Cad. do Salário Família
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(16)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../rendimento/index.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Informe de Rendimento
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(17) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../ctps2.php" data-clt="<?= $row['0'] ?>" data-id="1" data-regiao="<?= $id_reg ?>" data-caminho="1" class="col-xs-12 btn btn-default">
                                            Receber CTPS
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(18)) { ?> 
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="<?= $link_ctps ?>" data-clt="<?= $row['0'] ?>" data-id="1" data-regiao="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Entregar CTPS
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(61)) { ?>       
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="solicitacaopis.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Cadastro PIS
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(19) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="admissional_clt.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Exame Admissional
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(20)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="gerarPonto.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-regiao="<?= $id_reg ?>" data-id="<?= $id_user ?>" class="col-xs-12 btn btn-default">
                                            Gerar Apontamento
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="contratoclt.php" data-clt="<?= $row['0'] ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Contrato de Trabalho
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="contratocltexp.php" data-clt="<?= $row['0'] ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Contrato de Experiência
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(80) && $ativo) { ?>  
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="rh_transferencia2.php" data-clt="<?= $row['0'] ?>" class="col-xs-12 btn btn-default">
                                            Transferência de Unidade
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="../registrodeempregado.php" data-clt="<?= $row['0'] ?>" data-bol="<?= $row['id_antigo'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Registro de Empregado
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="../registrodeempregado_pordata.php" data-clt="<?= $row['0'] ?>" data-bol="<?= $row['id_antigo'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" data-tela="1" class="col-xs-12 btn btn-default">
                                        Registro de Empregado Por Data
                                    </div>
                                </div>
                                <?php
                                if ($ACOES->verifica_permissoes(21)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../fichadecadastroclt.php" data-clt="<?= $row['0'] ?>" data-bol="<?= $row['id_antigo'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" data-tela="1" class="col-xs-12 btn btn-default">
                                            Ficha de Cadastro
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(22) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="salariofamilia/safami.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Benefícios
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(23) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="vt/vt.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Vale Transporte
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(24) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cartadereferencia.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Carta de Referência
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(25) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../rh/notifica/advertencia.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Medidas Disciplinares
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(26) && $ativo) { ?>
                                    <!--div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../rh/notifica/form_suspencao.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" data-tab="bolsista<?= $id_pro ?>" class="col-xs-12 btn btn-default">
                                            Suspensão
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(27)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="../relatorios/fichafinanceira_clt2.php" data-id="<?= $row['0'] ?>" data-tipo="2" data-pro="<?= $id_pro ?>" data-reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Ficha Financeira
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(28)) { ?>  
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="docs/dispensa.php" data-clt="<?= $row['0'] ?>" data-tab="bolsista<?= $id_pro ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Dispensa
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="docs/demissao.php" data-clt="<?= $row['0'] ?>" data-tab="bolsista<?= $id_pro ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Demissão
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(30)) { ?>  
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="demissionalclt.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Exame Demissional
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="declaracao_jornada_semanal.php" data-clt="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Declaração de Jornada Semanal
                                    </div>
                                </div>
                            <?php } 
                            if ($ACOES->verifica_permissoes(90) && ($row['status'] >= 60 && $row['status'] != 200)) { ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="cadastroclt.php" data-id="<?= $row['0'] ?>" data-projeto="<?= $id_pro ?>" data-regiao="<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Recadastrar
                                    </div>
                                </div>
                            <?php } ?>
                            -->
                            <?php
                                if ($ACOES->verifica_permissoes(15) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../tvsorrindo.php?bol=<?= $row['id_antigo'] ?>&clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&tipo=2" class="col-xs-12 btn btn-default">
                                            TV Sorrindo
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(78) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="salariofamilia/safami.php&clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Cad. do Salário Família
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(16)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../rendimento/index.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Informe de Rendimento
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(17) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../ctps2.php?clt=<?= $row['0'] ?>&id=1&regiao=<?= $id_reg ?>&caminho=1" class="col-xs-12 btn btn-default">
                                            Receber CTPS
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(18)) { ?> 
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="<?= $link_ctps ?>?clt=<?= $row['0'] ?>&id=1&regiao=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Entregar CTPS
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(61)) { ?>       
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="solicitacaopis.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Cadastro PIS
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(19) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="admissional_clt.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Exame Admissional
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(20)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="gerarPonto.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&regiao=<?= $id_reg ?>&id=<?= $id_user ?>" class="col-xs-12 btn btn-default">
                                            Gerar Apontamento
                                        </a>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="contratoclt.php?clt=<?= $row['0'] ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Contrato de Trabalho
                                        </a>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="contratocltexp.php?clt=<?= $row['0'] ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Contrato de Experiência
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(80) && $ativo) { ?>  
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="rh_transferencia2.php?clt=<?= $row['0'] ?>" class="col-xs-12 btn btn-default">
                                            Transferência de Unidade
                                        </a>
                                    </div>
                                <?php } ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <a href="../registrodeempregado.php?clt=<?= $row['0'] ?>&bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Registro de Empregado
                                    </a>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <a href="../registrodeempregado_pordata.php?clt=<?= $row['0'] ?>&bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&tela=1" class="col-xs-12 btn btn-default">
                                        Registro de Empregado Por Data
                                    </a>
                                </div>
                                <?php
                                if ($ACOES->verifica_permissoes(21)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../fichadecadastroclt.php?clt=<?= $row['0'] ?>&bol=<?= $row['id_antigo'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&tela=1" class="col-xs-12 btn btn-default">
                                            Ficha de Cadastro
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(22) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="salariofamilia/safami.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Benefícios
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(23) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="vt/vt2.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Vale Transporte
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(24) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="cartadereferencia.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Carta de Referência
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(25) && $ativo) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../rh/notifica/advertencia.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Medidas Disciplinares
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(26) && $ativo) { ?>
                                    <!--div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../rh/notifica/form_suspencao.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>&tab=bolsista<?= $id_pro ?>" class="col-xs-12 btn btn-default">
                                            Suspensão
                                        </div>
                                    </div>-->
                                <?php }
                                if ($ACOES->verifica_permissoes(27)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="../relatorios/fichafinanceira_clt2.php?id=<?= $row['0'] ?>&tipo=2&pro=<?= $id_pro ?>&reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Ficha Financeira
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(28)) { ?>  
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="docs/dispensa.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Dispensa
                                        </a>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="docs/demissao.php?clt=<?= $row['0'] ?>&tab=bolsista<?= $id_pro ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Demissão
                                        </a>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(30)) { ?>  
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <a href="demissionalclt.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&id_reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                            Exame Demissional
                                        </a>
                                    </div>
                                <?php } ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <a href="declaracao_jornada_semanal.php?clt=<?= $row['0'] ?>&pro=<?= $id_pro ?>&reg=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Declaração de Jornada Semanal
                                    </a>
                                </div>
                            <?php if ($ACOES->verifica_permissoes(90) && ($row['status'] >= 60 && $row['status'] != 200)) { ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <a href="cadastroclt.php?id=<?= $row['0'] ?>&projeto=<?= $id_pro ?>&regiao=<?= $id_reg ?>" class="col-xs-12 btn btn-default">
                                        Recadastrar
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane cltDocumentos">
                    <?php if ($ACOES->verifica_permissoes(62)) { ?>  
                        <h3 id="ancora_documentos" class=" hidden-xs">UPLOAD DE DOCUMENTOS</h3>
                        <hr class=" hidden-xs">
                        <div class="row hidden-xs">
                            <div class="col-xs-12" id="fotosDocumentos">
                                <?php
                                // Exclusão do Documento
                                if (isset($_REQUEST['deleta_documento'])) {
                                    if (file_exists($_REQUEST['deleta_documento'])) {
                                        unlink($_REQUEST['deleta_documento']);
                                        echo 'Documento deletado com sucesso!<br>';
                                    }
                                }

                                $diretorio_padrao = $_SERVER["DOCUMENT_ROOT"] . "/";
                                $diretorio_padrao .= "intranet/documentos/";
                                $dirInternet = "../documentos/";
                                $DeldirInternet = "documentos/";

                                $regiao = sprintf("%03d", $id_reg);
                                $projeto = sprintf("%03d", $id_pro);

                                $Dir = $regiao . "/" . $projeto . "/"; // O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
                                $novoDir = $row['tipo_contratacao'] . "_" . $row[0]; // O NOME DA PASTA DO USUARIO
                                $DirCom = $Dir . $novoDir;

                                $dir = $diretorio_padrao . $DirCom;
                                $dirInternet .= $DirCom;
                                $DeldirInternet .= $DirCom;
                                // Abre um diretorio conhecido, e faz a leitura de seu conteudo
                                if (is_dir($dir)) {
                                    if ($dh = opendir($dir)) {
                                        while (($file = readdir($dh)) !== false) {
                                            if ($file == "." or $file == "..") {
                                                $nada;
                                            } else {
                                                $tipoArquivo = explode("_", $file);
                                                $tipoArquivo = explode(".", $tipoArquivo[2]);

                                                $select = new upload();
                                                $TIPO = $select->mostraTipo($tipoArquivo[0]);

                                                $DirFinal = $dirInternet . "/" . $file;
                                                $DelDirFinal = $DeldirInternet . "/" . $file;

                                                // Renomeia o arquivo se estiver sem extensão
                                                if (!strstr($DirFinal, '.jpg') and ! strstr($DirFinal, '.gif') and ! strstr($DirFinal, '.png')) {
                                                    $de = $DirFinal;
                                                    $para = $DirFinal . '.jpg';
                                                    rename($de, $para);
                                                    $DirFinal .= '.jpg';
                                                }

                                                // Criando Array para Options no Select
                                                $ja_documentos[] = $file;

                                                echo "<div class='documentos'>";
                                                echo "<a class='documento' href='" . $DirFinal . "' rel='shadowbox[documentos]' title='Visualizar $TIPO'>";
                                                echo "<img src='" . $DirFinal . "' width='75' height='75' border='0' alt='$TIPO'></a>";
                                                echo "<a href='$_SERVER[PHP_SELF]?$_SERVER[QUERY_STRING]&deleta_documento=$DirFinal#ancora_documentos'>deletar</a>";
                                                echo "</div>";
                                            }
                                        }
                                        closedir($dh);
                                    }
                                }

                                // Criando Array para Options no Select
                                if (!empty($ja_documentos)) {
                                    foreach ($ja_documentos as $documento) {
                                        $documento = explode('_', $documento);
                                        $tipo_documento = explode('.', $documento[2]);
                                        $tipo_documento = $tipo_documento[0];
                                        $tipos_ja_documentos[] = $tipo_documento;
                                    }
                                } ?>
                            </div>
                        </div>
                        <div class="row hidden-xs">
                            <div class="col-xs-12" id="foto">
                                <input type="file" name="uploadDoc" id="uploadDoc">
                            </div>
                        </div>
                        <?php if (count($tipos_ja_documentos) != 5) { ?>
                            <div class="row hidden-xs">
                                <div class="col-xs-12" id="upload_documentos" style="display: none;">
                                    <div id="BarUploadDoc" style="margin-bottom:10px; "></div>
                                    <strong>Tipo de Documento:</strong>&nbsp;&nbsp;
                                    <select name="select" id="select_doc" >
                                        <option  selected value="">Escolha um tipo abaixo</option>
                                        <?php
                                        $qr_documentos = mysql_query("SELECT * FROM  upload	 WHERE status_reg = '1'");
                                        while ($documento = mysql_fetch_assoc($qr_documentos)) {
                                            if (!in_array($documento['id_upload'], $tipos_ja_documentos)) { ?>
                                                <option value="<?=$documento['id_upload']?>"><?=$documento['arquivo']?></option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <a class="btn btn-default" id="Upar">Enviar Documento</a>
                                </div>
                            </div>
                        <?php } ?>
                        
                        <hr class="hidden-xs">
                    <?php } ?>
                    <table class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr class="bg-primary">
                                <th width="70%">DOCUMENTOS</th>
                                <th colspan="2" width="11%"></th>
                                <th class="center" width="8%">STATUS</th>
                                <th class="center" width="11%">DATA</th>
                            </tr>
                        </thead>
                        <?php
                        $qr_documentos = mysql_query("SELECT * FROM upload ORDER BY ordem") or die(mysql_error());
                        while ($row_documentos = mysql_fetch_assoc($qr_documentos)):
                            $verifica_anexo = mysql_num_rows(mysql_query("SELECT * FROM documento_clt_anexo WHERE id_upload = '$row_documentos[id_upload]' AND id_clt = '$row[0]' AND anexo_status = 1"));
                            if ($row_documentos['id_upload'] == 13 and $row['contrato_medico'] == 0)
                                continue;
                            if ($row_documentos['id_upload'] != 14) {
                                $onclick = "OnClick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"";
                                $visualizar = ($verifica_anexo != 0) ? '<a href="ver_documentos.php?id=' . $row_documentos['id_upload'] . '&clt=' . $id_clt . '" ' . $onclick . ' target="_blank" title="VISUALIZAR"><img src="../imagens/ver_anexo.gif" width="20" height="20" /></a>' : '';
                                $status = ($verifica_anexo == 0) ? '<img src="../imagens/naoassinado.gif" />' : '<img src="../imagens/assinado.gif" />';
                                $anexar = '<a href="anexar_documento.php?clt=' . $row['0'] . '&id=' . $row_documentos['id_upload'] . '" title="ANEXAR/EDITAR"> <img src="../img_menu_principal/anexo.png" class="icon-anexo" /></a>';
                                $data = @mysql_result(mysql_query("SELECT date_format(data_cad, '%d/%m/%Y') as data FROM documento_clt_anexo WHERE id_clt = '$id_clt' AND id_upload = '$row_documentos[id_upload]'  ORDER BY data_cad DESC"), 0);
                                if ($row_documentos['id_upload'] == 13) {
                                    $visualizar = '<a href="contrato_medico.php?clt=' . $row[0] . '" target="_blank" title="VISUALIZAR"><img src="../imagens/ver_anexo.gif" width="20" height="20" /></a>';
                                    $anexar = '';
                                    $status = '<img src="../imagens/assinado.gif" />';
                                }
                                //BRUNO CRITÉRIOS DE AVALIAÇÃO
                                if ($row_documentos['id_upload'] == 19) {
                                    $verifica_linha = mysql_num_rows(mysql_query("SELECT * FROM rh_avaliacao_clt WHERE clt_id = " . $row[0]));
                                    $visualizar = ($verifica_linha == 0) ? '' : '<a href="ver_avaliacao_clt.php?clt=' . $row[0] . ' target="_blank""><img src="../imagens/ver_anexo.gif" width="20" height="20" /></a>';
                                    $anexar = ($verifica_linha == 0) ? '<a href="avaliacao_clt.php?clt=' . $row[0] . '&reg=' . $id_reg . '&pro=' . $id_pro . '"><img src="../img_menu_principal/anexo.png" class="icon-anexo" /></a>' : '';
                                    $status = ($verifica_linha == 0) ? '<img src="../imagens/naoassinado.gif" />' : '<img src="../imagens/assinado.gif" />';
                                    $data = @mysql_result(mysql_query("SELECT date_format(data_cadastro, '%d/%m/%Y') as data FROM rh_avaliacao_clt WHERE clt_id = '$row[0]'"), 0);
                                }
                                // FIM CRITÉRIOS DE AVALIAÇÃO
                            } else {
                                $qr_processo = mysql_query("SELECT *,DATE_FORMAT(data_cad, '%d/%m/%Y') as data FROM processos_interno WHERE id_clt = '$id_clt' AND proc_interno_status = 1");
                                $row_processo = mysql_fetch_assoc($qr_processo);
                                $verifica_processo = mysql_num_rows($qr_processo);
                                $status = ($verifica_processo == 0) ? '<img src="../imagens/naoassinado.gif" />' : '<img src="../imagens/assinado.gif" />';
                                $data = $row_processo['data'];
                                $visualizar = '<a href="ver_abertura_proc.php?clt=' . $row[0] . '" target="_blank" title="VISUALIZAR"><img src="../imagens/ver_anexo.gif" width="20" height="20" /></a>';
                            }?>
                            <tr height="25">
                                <td ><?php echo $row_documentos['arquivo'] ?></td>
                                <td align="center"><?php echo $anexar; ?></td>
                                <td align="center"><?php echo $visualizar; ?></td>
                                <td align="center"><?php echo $status; ?></td>  
                                <td align="center"><?php echo $data; ?></td>      
                            </tr>
                            <?php
                        endwhile; ?>
                    </table>
                    <hr>
                    <h3>
                        CONTROLE DE DOCUMENTOS 
                        <small class="pull-right">
                            <img src="../imagens/assinado.gif" width="15" height="17" align="absmiddle"> Emitido  
                            <img src="../imagens/naoassinado.gif" width="15" height="17" align="absmiddle"> N&atilde;o Emitido
                        </small>
                    </h3>
                    <table class="table table-condensed table-striped table-hover">
                        <thead>
                        <tr class="bg-dark-gray">
                            <th width="70%"><strong>DOCUMENTO</strong></th>
                            <th width="15%" class="center"><strong>STATUS</strong></th>
                            <th width="15%" class="center"><strong>DATA</strong></th>
                        </tr>
                        </thead>
                        <?php
                        $tipo_contratacao = '2';
                        $result_docs = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$tipo_contratacao' ORDER BY documento");
                        while ($row_docs = mysql_fetch_array($result_docs)) {
                            $result_verifica = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM rh_doc_status WHERE tipo = '$row_docs[0]' and id_clt = '$row[0]'");
                            $num_row_verifica = mysql_num_rows($result_verifica);
                            $row_verifica_doc = mysql_fetch_array($result_verifica);

                            if ($num_row_verifica != "0") {
                                $img = "<img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
                                $data = $row_verifica_doc['data'];
                            } else {
                                $img = "<img src='../imagens/naoassinado.gif' width='15' height='17' align='absmiddle'>";
                                $data = "";
                            } ?>
                            <tr>
                            <td><?=$row_docs[documento]?></td>
                            <!--//echo "<td class='linha' align='center'>$img</td>";-->
                            <?php if (($row_docs['documento'] == 'Inscrição no PIS') and ( $emissao == true)) { ?>
                                <td class='linha' align='center'><img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'></td>
                            <?php } elseif (($row_docs['documento'] != 'Inscrição no PIS') or ( $emissao == false)) { ?>
                                <td class='linha' align='center'><?=$img?></td>
                            <?php } ?>
                            <td align='center'><?=$data?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="tab-pane cltEventos">
                    <h3>CONTROLE DE EVENTOS</h3>
                    <hr>
                    <table class="table table-striped table-condensed table-hover table-bordered">
                        <thead>
                            <tr>
                                <th width="%">Evento</th>
                                <th class="center" width="11%">Data</th>
                                <th class="center" width="11%">Data de retorno</th>
                                <th class="center" width="4%">Dias</th>
                                <th class="center" width="15%">Anexar Documento</th>
                                <th class="center" width="11%">Ver Documento</th>
                            </tr>
                        </thead>
                        <?php
                        $qr_historico_eventos = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND nome_status!='' AND status = '1' ") or die(mysql_error());
                        $qr_historico_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND regiao = '$id_reg' AND projeto = '$id_pro' AND status = '1' ") or die(mysql_error());
                        $qr_historico_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status = '1' ") or die(mysql_error());
                        $qr_historico_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status!=0") or die(mysql_error());
                        while ($row_clt = mysql_fetch_assoc($qr_historico_clt)):
                            $historico[] = array(
                                'nome' => 'Admissão',
                                'inicio' => $row_clt['data_entrada'],
                                'fim' => '',
                                'duracao' => '',
                                'id_evento' => '',
                                'status' => '',
                            );
                        endwhile;
                        while ($row_evento = mysql_fetch_assoc($qr_historico_eventos)):
                            $historico[] = array(
                                'nome' => $row_evento['nome_status'],
                                'inicio' => $row_evento['data'],
                                'fim' => $row_evento['data_retorno'],
                                'duracao' => $row_evento['dias'],
                                'id_evento' => $row_evento['id_evento'],
                                'status' => $row_evento['cod_status'],
                            );
                        endwhile;
                        while ($row_ferias = mysql_fetch_assoc($qr_historico_ferias)):
                            $historico[] = array(
                                'nome' => 'Férias',
                                'inicio' => $row_ferias['data_ini'],
                                'fim' => $row_ferias['data_fim'],
                                'duracao' => ($row_ferias['data_fim'] - $row_ferias['data_ini']),
                                'id_evento' => '',
                                'status' => '',
                            );

                        endwhile;
                        while ($row_recisao = mysql_fetch_assoc($qr_historico_rescisao)):
                            $historico[] = array(
                                'nome' => 'Rescisão',
                                'inicio' => $row_recisao['data_demi'],
                                'fim' => '',
                                'duracao' => '',
                                'id_evento' => '',
                                'status' => '',
                            );
                        endwhile;
                        $cod_status = array(20, 50, 51);
                        foreach ($historico as $chave => $inicio) { ?>
                            <tr>
                                <td><?php echo $historico[$chave]['nome']; ?></td>
                                <td><?php echo formato_brasileiro($historico[$chave]['inicio']); ?></td>
                                <td>
                                    <?php if ($historico[$chave]['fim'] != '0000-00-00') {
                                        echo formato_brasileiro($historico[$chave]['fim']);
                                    } ?>
                                </td>
                                <td><?php if (!empty($historico[$chave]['duracao'])) echo $historico[$chave]['duracao']; ?></td>
                                <td style="text-align: center;">
                                    <?php if (!empty($historico[$chave]['status']) && in_array($historico[$chave]['status'], $cod_status)) { ?>
                                        <a href="#eventos" data-id="<?= $historico[$chave]['id_evento'] ?>" class="anexar-atestado" data-click="1"><img src="../img_menu_principal/anexo.png" class="icon-anexo"></a>
                                    <?php } else { ?>
                                        <img src="../img_menu_principal/anexo.png" class="icon-anexo disable">
                                    <?php } ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (!empty($historico[$chave]['status']) && in_array($historico[$chave]['status'], $cod_status)) { ?>
                                        <a href="lista_AnexoEventos.php?id=<?= $historico[$chave]['id_evento'] ?>"><img src="../imagens/ver_anexo.gif" class="icon-anexo"></a>
                                    <?php } else { ?>
                                        <img src="../imagens/ver_anexo.gif" class="icon-anexo disable">
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                    <form action="../include/upload_atestado.php" method="post" id="form_up_evento" class="hidden" enctype="multipart/form-data">
                        <div style="margin: .5em 0;">
                            <input type="file" name="atestado" id="atestado" class="validate[required,custom[docsType]]">
                            <input type="hidden" name="id_evento" id="id_evento" value="">
                            <input type="hidden" name="reg" id="reg" value="<?= sprintf('%03d', $id_reg); ?>">
                            <input type="hidden" name="projeto" id="projeto" value="<?= sprintf('%03d', $id_pro); ?>">
                            <input type="hidden" name="ID_participante" id="id_participante" value="<?= sprintf('%03d', $id_clt); ?>">
                            <input type="hidden" name="tipo_contratacao" id="tipo_contratacao" value="2">
                            <input type="submit" value="Salvar">
                        </div>

                        <progress max="100" value="0">
                            <!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
                            <div class="progress-bar">
                                <span style="width:0%"></span>
                            </div>
                        </progress>
                        <div id="status" class="hidden back-green"></div>
                    </form>
                </div>
                <div class="tab-pane cltConta">
                    <?php
                    if ($ACOES->verifica_permissoes(63)) {
                        if ($ativo) { ?>
                            <h3>ENCAMINHAMENTO DE CONTA</h3>
                            <hr>
                            <form action="../declarabancos.php" method="post" name="form1" target="_blank" class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-xs-2">
                                        <label class="control-label">Escolha o Banco:</label>
                                    </div>
                                    <div class="col-xs-4">
                                        <select name="banco" id="banco" class="form-control">
                                            <?php while ($row_ban = mysql_fetch_array($result_ban)) {
                                                echo "<option value='$row_ban[id_banco]'>$row_ban[nome]</option>";
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-4">
                                        <input type="submit" class="btn btn-default" value="Gerar Encaminhamento de Conta">
                                        <input type="hidden" name="tipo" id="tipo" value="2">
                                        <input type="hidden" name="bolsista" id="bolsista" value="<?= $row['0'] ?>">
                                        <input type="hidden" name="regiao" id="regiao" value="<?= $id_reg ?>">
                                    </div>
                                </div>
                            </form>
                                
                        <?php }
                    }
                    /*if ($ACOES->verifica_permissoes(14)) { ?>
                        <tr>
                            <td colspan="2"><h1><span>CONTROLE DE MOVIMENTOS</span></h1></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>
                    <?php } */ ?>
                </div>
            </div>
                
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
        <script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
        <script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
        <script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
        <script type="text/javascript">
            $().ready(function() {
                <?php if ($row['foto'] == '1') { ?>
                    $("#bt_deletar").show();
                <?php } ?>

                <?php if (isset($_REQUEST['entregaCTPS']) && $_REQUEST['entregaCTPS'] == 0) { ?>
                    alert('ATENÇÃO: Não há registro de entrada de CTPS para este CLT.');
                <?php } ?>

                $("#fileQueue").hide();
                $("#bt_deletar").click(function() {
                    $.post('../include/excluir_foto.php',
                            {nome: '<?= $id_reg ?>_<?= $id_pro ?>_<?= $_REQUEST['clt'] ?>.gif', clt: '<?= $_REQUEST['clt'] ?>'},
                    function() {
                        $("#imgFile").attr('src', '../fotos/semimagem.gif');
                        $("#bt_deletar").hide();
                        $('#bt_enviar').uploadifySettings('buttonText', 'Adicionar foto');
                    }

                    );
                });

                $("#bt_enviar").uploadify({
                    'uploader': '../uploadfy/scripts/uploadify.swf',
                    'script': '../uploadfy/scripts/uploadify.php',
                    'folder': '../../../fotos',
                    'buttonText': '<?php if ($row['foto'] == '1') { ?>Alterar<?php } else { ?>Adicionar<?php } ?> foto',
                    'queueID': 'fileQueue',
                    'cancelImg': '../uploadfy/cancel.png',
                    'auto': true,
                    'method': 'post',
                    'multi': false,
                    'fileDesc': 'Gif',
                    'fileExt': '*.gif;*.jpg;',
                    'onOpen': function() {
                        $("#fileQueue").show();
                    },
                    'onAllComplete': function() {
                        $("#bt_deletar").show('slow');
                        $('#imgFile').attr('src', '../fotosclt/<?= $id_reg ?>_<?= $id_pro ?>_<?= $_REQUEST['clt'] ?>.gif');
                        $("#fileQueue").hide('slow');
                        $('#bt_enviar').uploadifySettings('buttonText', 'Alterar foto');
                    },
                    'scriptData': {'regiao': <?= $id_reg ?>, 'projeto': <?= $id_pro ?>, 'clt': <?= $_REQUEST['clt'] ?>}
                });

                // UPLOAD DO ARQUIVO DE EVENTO
                $(".anexar-atestado").click(function() {
                    var evento = $(this).data("id");
                    //var click = $(this).data("click");
                    $("#id_evento").val(evento); // muda o val do input #id_evento
                    //$("#form_up_evento").removeClass('hidden'); // exibe o form de upload
                    $("#form_up_evento").show('fast'); // exibe o form de upload
                });

                var bar = $('.bar');
                var percent = $('.percent');
                var status = $('#status');

                $('#form_up_evento').validationEngine({promptPosition: "topLeft"});
                $('#form_up_evento').ajaxForm({
                    clearForm: true,
                    beforeSend: function() {
                        status.empty();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        $('progress').attr('value', percentComplete);
                        $(".progress-bar span").css("width", percentComplete + "%");
                        percent.html(percentVal);
                    },
                    success: function() {
                        var percentVal = '100%';
                        $('progress').attr('value', '100');
                        $(".progress-bar span").css("width", "100%");
                        percent.html(percentVal);
                    },
                    complete: function(xhr) {
                        status.html(xhr.responseText);
                        status.removeClass("hidden");
                    }
                });

                // FIM DO UPLOAD DO ARQUIVO DE EVENTO

            });
        </script>
        <script language="javascript">
            $().ready(function() {
                var tipo_contratacao = '<?= $row['tipo_contratacao'] ?>';
                var regiao = '<?= sprintf('%03d', $id_reg); ?>';
                var projeto = '<?= sprintf('%03d', $id_pro); ?>';
                var id_participante = '<?= sprintf('%03d', $id_clt); ?>';

                $("#uploadDoc").uploadify({
                    'uploader': '../uploadfy/scripts/uploadify.swf',
                    'script': '../include/upload_doc.php',
                    'buttonImg': '../imagens/botao_upload.jpg',
                    'buttonText': '',
                    'cancelImg': '../uploadfy/cancel.png',
                    'width': '156',
                    'height': '46',
                    'fileDesc': 'Jpg, Gif, Png',
                    'fileExt': '*.gif;*.jpg;*.png',
                    'auto': false,
                    'method': 'post',
                    'multi': false,
                    'queueID': 'BarUploadDoc',
                    'onSelect': function() {
                        $("#upload_documentos").show();

                    },
                    'onComplete': function(event, queueID, fileObj, response, data) {

                        if (response != 1) {
                            $("#upload_documentos").hide();

                            $.post('../include/fotos_documentos.php', {
                                'id_regiao': regiao,
                                'id_projeto': projeto,
                                'tipo_contratacao': tipo_contratacao,
                                'id_participante': id_participante
                            }, function(dados) {
                                $("#fotosDocumentos").html(dados);
                            });
                        } else {
                            alert('Erro ao enviar o arquivo!');
                        }
                    },
                    'scriptData': {'reg': regiao,
                        'projeto': projeto,
                        'ID_participante': id_participante,
                        'tipo_contratacao': tipo_contratacao

                    }
                });



                $('#Upar').click(function() {
                    if ($('#select_doc').val() != '') {
                        $('#uploadDoc').uploadifySettings('scriptData', {'tipo_documento': $('#select_doc').val()});
                        $('#uploadDoc').uploadifyUpload();
                        $('#BarUploadDoc').slideDown('slow');


                    } else {
                        alert('Selecione um tipo de documento');
                    }
                });
            });

            function Confirm(a) {
                var arquivo = a;
                input_box = confirm("Deseja realmente excluir o documento?");
                if (input_box == true) {
                    location.href = "<?= $_SERVER['PHP_SELF'] ?>?<?= $_SERVER['QUERY_STRING'] ?>&foto=deletado#ancora_documentos";
                }
            }
        </script>
    </body>
</html>