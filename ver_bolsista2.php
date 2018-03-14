<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('conn.php');
include('upload/classes.php');
include('classes/funcionario.php');
include('classes_permissoes/acoes.class.php');
include('wfunction.php');

$usuario = carregaUsuario();
$ACOES = new Acoes();
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;

// Obtendo o id do cadastro

$id = 1;
$id_bol = $_REQUEST['bol'];
$id_pro = $_REQUEST['pro'];
//$id_reg = $_REQUEST['reg'];
$id_reg = $usuario['id_regiao'];
$id_user = $_COOKIE['logado'];


$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM autonomo WHERE id_autonomo = '$id_bol'");
$row = mysql_fetch_array($result);

$result_tab = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro' AND status_reg = '1'");
$row_tab = mysql_fetch_array($result_tab);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$sql_cooperativa = mysql_query("SELECT fantasia FROM cooperativas WHERE id_coop = $row[id_cooperativa]");
$row_cooperativa = mysql_fetch_array($sql_cooperativa);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' and id_projeto = '$id_pro'");

if ($row['status'] == '0') {
    $texto = "Data de saída: $row[data_saida2]";
} else {
    $texto = NULL;
}

$nome_arq = str_replace(' ', '_', $row['nome']);

$ano_cad = substr($row['data_cad'], 0, 4);

if ($ano_cad <= '2008') {
    $coluna_foto = $row['id_bolsista'];
} else {
    $coluna_foto = $row['0'];
}

if ($row['foto'] == "1") {
    $nome_imagem = $id_reg . "_" . $id_pro . "_" . $coluna_foto . ".gif";
} else {
    $nome_imagem = "semimagem.gif";
}

switch ($row['tipo_contratacao']) {
    case 1:
        $nome_contratacao = 'Autônomo';
        break;
    case 3:
        $nome_contratacao = 'Cooperado';
        break;
    case 4:
        $nome_contratacao = 'AUTÔNOMO / PJ';
        break;
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Visualizar $nome_contratacao");
$breadcrumb_pages = array("Lista Projetos" => "ver2.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);");
$breadcrumb_attr = array(
    "Visualizar Projeto" => "class='link-sem-get' data-projeto='$id_pro' data-form='form1' data-url='ver2.php'",
    "Lista Participantes" => "class='link-sem-get' data-projeto='$id_pro' data-form='form1' data-url='bolsista2.php'"
);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Visualizar <?=$nome_contratacao?></title>
        <link href="favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="css/progress.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="resources/css/add-ons.min.css" rel="stylesheet">
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
    <?php include("template/navbar_default.php"); ?>
        <div class="container">
            <form id="form1" method="post"></form>
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Visualizar <?=$nome_contratacao?></small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?php if ($_REQUEST['sucesso'] == "cadastro") { ?>
                        <div class="alert alert-dismissable alert-success avisos_eventos">
                            <strong>Participante cadastrado com sucesso!</strong>
                        </div>
                    <?php } ?>
                    <h4><strong>MATRÍCULA: <?=$row['matricula']?></strong></h4>
                    <div class="stat-panel">
                        <div class="stat-cell col-xs-2 no-border-vr no-border-l no-padding valign-middle text-center tr-bg-active hidden-xs">
                            <img src="fotos/<?=$nome_imagem?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;">
                            <input type="file" id="bt_enviar" name="bt_enviar"/>
                            <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="imagens/excluir_foto.gif"></a>
                        </div> <!-- /.stat-cell -->
                        <div class="stat-cell col-xs-10 no-padding valign-middle bordered no-border-vr tr-bg-active">
                            <div class="stat-rows">
                                <div class="stat-row">
                                    <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                        <strong><?= $row['campo3'] . ' - ' . $row['nome'] ?></strong>&nbsp;
                                    </div>
                                    <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                        <strong>CPF:</strong> <?=$row['cpf']?>&nbsp;
                                    </div>
                                </div>
                                <div class="stat-row">
                                    <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                        <strong>Data de Entrada:</strong> <?=$row['nova_data']?>&nbsp;
                                    </div>
                                    <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                        <strong>Projeto:</strong> <?= $row_tab['id_projeto'] . ' - ' . $row_tab['nome'] ?>&nbsp;
                                    </div>
                                </div>
                                <div class="stat-row">
                                    <div class="col-xs-6 stat-cell padding-sm valign-middle">
                                        <?php if($row['tipo_contratacao']==3){ ?><strong>Cooperativa: </strong> <?php echo (!empty($row['id_cooperativa'])) ? $row['id_cooperativa']." - ".$row_cooperativa['fantasia'] : "<span style='colo: red; font-weight: bold;'>Cooperativa não vinculada</span>";?> <br><?php } ?>&nbsp;
                                    </div>
                                    <div class="col-xs-6 stat-cell padding-sm valign-middle text-danger">
                                        <?=$texto?>&nbsp;
                                    </div>
                                </div>
                                <div class="stat-row">
                                    <div class="col-xs-12 stat-cell padding-sm valign-middle tr-bg-warning">
                                        <?='Última Alteração feita por <strong>' . $row_user2['nome1'] . '</strong> na data ' . $row['dataalter2']?>
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
                                        <?php if (!empty($atividade['cbo_codigo'])) {
                                            echo '(' . $atividade['cbo_codigo'] . ')';
                                        } ?>    
                                    </p>
                                    <p><strong>Unidade:</strong> <?= $row['locacao'] ?></p>
                                    <p>
                                        <strong>Salário:</strong>
                                        <?php if (!empty($atividade['salario'])) {
                                            echo "R$ " . number_format($atividade['salario'], 2, ',', '.');
                                        } else {
                                            echo "<i>Não informado</i>";
                                        } ?>
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
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <h3 class="text-danger">MENU DE EDIÇÃO</h3>
                    <hr class="hr-danger">
                    <?php
                    if ($row_user['grupo_usuario'] == '3') {
                        $botao_editar = NULL;
                    } else {
                        if ($row['tipo_contratacao'] == '1') {
                            $botao_editar = '
                            <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                <div data-form="form1" data-url="alter_bolsista.php" data-bol="'.$row['0'].'" data-pro="'.$id_pro.'" class="col-xs-12 btn btn-default link-sem-get">
                                    Editar Cadastro
                                </div>
                            </div>';
                        } elseif ($row['tipo_contratacao'] == '3') {
                            $botao_editar = '
                            <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                <div data-form="form1" data-url="cooperativas/altercoop.php" data-coop="'.$row['0'].'" data-tipo="3" class="col-xs-12 btn btn-default link-sem-get">
                                    Editar Cadastro
                                </div>
                            </div>';
                        } elseif ($row['tipo_contratacao'] == '4') {
                            $botao_editar = '
                            <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                <div data-form="form1" data-url="cooperativas/altercoop.php" data-coop="'.$row['0'].'" data-tipo="4" class="col-xs-12 btn btn-default link-sem-get">
                                    Editar Cadastro
                                </div>
                            </div>';
                        }
                    }

                    if (!empty($row['pis'])) {
                        $statusBotao = 'none';
                        $emissao = true;
                    } else {
                        $statusBotao = 'inline';
                        $emissao = false;
                    }
                    
                    switch ($row['tipo_contratacao']) {
                        // Links para Autonomos
                        case 1: ?>

                            <?php if ($ACOES->verifica_permissoes(41)) { ?>
                                <!-- linha 1 -->
                                <?= $botao_editar ?>
                            <?php }
                            //verifica se o projeto está desativado
                            if ($row_tab['status_reg'] == 1) {
                                if ($ACOES->verifica_permissoes(48)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="rendimento/index.php" data-bol="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Informe de Rendimento
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                            <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                <div data-form="form1" data-url="autonomo/rpa_autonomo.php" data-aut="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                    RPA - Autônomo
                                </div>
                            </div>
                            <?php break;
                        case 3:
                            if ($ACOES->verifica_permissoes(31)) {
                                echo $botao_editar;
                            }

                            //verifica se o projeto está desativado
                            if ($row_tab['status_reg'] == 1 or $_COOKIE['logado'] == 87) {
                                if ($ACOES->verifica_permissoes(32)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/tvsorrindo.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            TV Sorrindo
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(33)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/contratos/contrato<?= $row["id_cooperativa"] ?>.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Adesão
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(34)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/quotas.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Quotas
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(35)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/fichadecadastro.php" data-bol="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Ver Ficha
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(36)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/distrato.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Desligamento
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(37)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="rh/solicitapis_pdf.php" data-bol="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Gerar PIS
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(38)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/devolucao_quotas.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Devolu&ccedil;&atilde;o de Quotas
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(39)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="rendimento/informe_coop.php" data-coop="<?= $row['0'] ?>" data-cooperativa="<?= $row['id_cooperativa'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Informe de Rendimento
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(40)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/recibocoop_individual_pdf.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Recibos de Pagamento
                                        </div>
                                    </div>
                                <?php }
                            } 
                            break;
                        case 4:
                            if ($ACOES->verifica_permissoes(73)) { ?>
                                <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                    <div data-form="form1" data-url="abertura_processo.php" data-autonomo="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                        Abertura de Processo
                                    </div>
                                </div>
                            <?php }
                            if ($ACOES->verifica_permissoes(49)) { 
                                echo $botao_editar;
                            }
                            if ($row_tab['status_reg'] == 1) {
                                if ($ACOES->verifica_permissoes(44)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/tvsorrindo.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            TV Sorrindo
                                        </div>
                                    </div>
                                <?php }
                                if ($ACOES->verifica_permissoes(51)) { ?>
                                    <div class="col-xs-12 col-sm-6 col-md-3 margin_b20">
                                        <div data-form="form1" data-url="cooperativas/fichadecadastro.php" data-coop="<?= $row['0'] ?>" data-pro="<?= $id_pro ?>" data-id_reg="<?= $id_reg ?>" class="col-xs-12 btn btn-default link-sem-get">
                                            Ver Ficha
                                        </div>
                                    </div>
                                <?php }
                            }
                    } ?>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    switch ($row['tipo_contratacao']) {
                        case 1: if ($ACOES->verifica_permissoes(54)) { $mostra_upload = true; } else { $mostra_upload = false; } break;
                        case 3: if ($ACOES->verifica_permissoes(53)) { $mostra_upload = true; } else { $mostra_upload = false; } break;
                        case 4: if ($ACOES->verifica_permissoes(52)) { $mostra_upload = true; } else { $mostra_upload = false; } break;
                    } 
                    if ($mostra_upload) { ?>
                        <table class="table table-condensed table-hover table-striped">
                            <thead>
                                <tr class="bg-primary">
                                    <th width="50%"><strong>Documentação do trabalhador(a)</strong></th>
                                    <th colspan="2"></th>
                                    <th class="center" width="10%"><strong>Status</strong></th>
                                    <th class="center" width="10%"><strong>Data</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qr_documentos = mysql_query("SELECT * FROM upload ORDER BY ordem") or die(mysql_error());
                                while ($row_documentos = mysql_fetch_assoc($qr_documentos)):
                                    switch ($row['tipo_contratacao']) {
                                        case 1: $documento_necessarios = array(1, 2, 5); break;
                                        case 3: $documento_necessarios = array(1, 2, 10, 5, 3, 9, 4, 22, 23); break;
                                    }

                                    if ($row['tipo_contratacao'] == 1 or $row['tipo_contratacao'] == 3) {
                                        if (!in_array($row_documentos['id_upload'], $documento_necessarios))
                                            continue;
                                    }

                                    $verifica_anexo = mysql_num_rows(mysql_query("SELECT * FROM documento_autonomo_anexo WHERE id_upload = '$row_documentos[id_upload]' AND id_autonomo = '$row[0]' AND anexo_status = 1"));

                                    if ($row_documentos['id_upload'] == 13 and $row['contrato_medico'] == 0)
                                        continue;

                                    if ($row_documentos['id_upload'] != 14) {
                                        $onclick = "OnClick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"";
                                        $visualizar = ($verifica_anexo != 0) ? '<a href="ver_documentos.php?id=' . $row_documentos['id_upload'] . '&autonomo=' . $row[0] . '" ' . $onclick . ' title="VISUALIZAR">
                                                                                                     <img src="imagens/ver_anexo.gif" width="20" height="20" />	  	
                                                                                              </a>' : '';
                                        $status = ($verifica_anexo == 0) ? '<img src="imagens/naoassinado.gif" />' : '<img src="imagens/assinado.gif" />';
                                        $anexar = '<a href="anexar_documento.php?autonomo=' . $row['0'] . '&id=' . $row_documentos['id_upload'] . '" title="ANEXAR/EDITAR"> <img src="img_menu_principal/anexo.png" width="20" height="20"/></a>';
                                        $data = @mysql_result(mysql_query("SELECT date_format(data_cad, '%d/%m/%Y') as data FROM documento_autonomo_anexo WHERE id_autonomo = '$row[0]' AND id_upload = '$row_documentos[id_upload]'  ORDER BY data_cad DESC"), 0);

                                        if ($row_documentos['id_upload'] == 13) {
                                            $visualizar = '<a href="rh/contrato_medico.php?autonomo=' . $row[0] . '"> 
                                                            <img src="imagens/ver_anexo.gif" width="20" height="20" />	  	
                                                    </a>';
                                            $anexar = '';
                                            $status = '<img src="imagens/assinado.gif" />';
                                        }

                                        //BRUNO CRITÉRIOS DE AVALIAÇÃO
                                        if ($row_documentos['id_upload'] == 19) {
                                            $verifica_linha = mysql_num_rows(mysql_query("SELECT * FROM rh_avaliacao WHERE autonomo_id = " . $row[0]));

                                            $visualizar = ($verifica_linha == 0) ? '' : '<a href="rh/ver_avaliacao.php?autonomo=' . $row[0] . '"><img src="imagens/ver_anexo.gif" width="20" height="20" /></a>';
                                            $anexar = ($verifica_linha == 0) ? '<a href="rh/avaliacao.php?autonomo=' . $row[0] . '&reg=' . $_REQUEST["reg"] . '&pro=' . $_REQUEST["pro"] . '"><img src="img_menu_principal/anexo.png" width="20" height="20" /></a>' : '';

                                            $status = ($verifica_linha == 0) ? '<img src="imagens/naoassinado.gif" />' : '<img src="imagens/assinado.gif" />';
                                            $data = @mysql_result(mysql_query("SELECT date_format(data_cadastro, '%d/%m/%Y') as data FROM rh_avaliacao WHERE autonomo_id = '$row[0]'"), 0);
                                        }
                                        // FIM CRITÉRIOS DE AVALIAÇÃO
                                    } else {
                                        $qr_processo = mysql_query("SELECT *,DATE_FORMAT(data_cad, '%d/%m/%Y') as data FROM processos_interno_autonomo WHERE id_autonomo = '$row[0]' AND proc_interno_status = 1");
                                        $row_tabcesso = mysql_fetch_assoc($qr_processo);
                                        $verifica_processo = mysql_num_rows($qr_processo);

                                        $status = ($verifica_processo == 0) ? '<img src="imagens/naoassinado.gif" />' : '<img src="imagens/assinado.gif" />';
                                        $data = $row_tabcesso['data'];
                                        $visualizar = '<a href="rh/ver_abertura_proc.php?autonomo=' . $row[0] . '"> 
                                            <img src="imagens/ver_anexo.gif" width="20" height="20" />	  	
                                        </a>';
                                        $anexar = '';
                                    } ?>
                                    <tr height="25">
                                        <td><?php echo $row_documentos['arquivo'] ?></td>
                                        <td class="center"><?php echo $anexar; ?></td>
                                        <td class="center"><?php echo $visualizar; ?></td>
                                        <td class="center"><?php echo $status; ?></td>  
                                        <td class="center"><?php echo $data; ?></td>      
                                    </tr>

                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    switch ($row['tipo_contratacao']) {
                        case 1: if ($ACOES->verifica_permissoes(55)) { $mostra_conta = true; } else { $mostra_conta = false; } break;
                        case 3: if ($ACOES->verifica_permissoes(56)) { $mostra_conta = true; } else { $mostra_conta = false; } break;
                        case 4: if ($ACOES->verifica_permissoes(57)) { $mostra_conta = true; } else { $mostra_conta = false; } break;
                    }

                    if ($mostra_conta) { ?>
                        <h3 class="text-danger">ENCAMINHAMENTO DE CONTA</h3>
                        <hr class="hr-danger">
                        <form action="declarabancos.php" class="form-horizontal" method="post" name="form1" target="_blank">
                            <div class="form-group">
                                <label class="control-label col-md-2">Escolha o Banco:</label>
                                <div class="col-md-4">
                                    <select name="banco" id="banco" class="form-control">
                                    <?php
                                    while ($row_ban = mysql_fetch_array($result_ban)) {
                                        print "<option value='$row_ban[id_banco]'>$row_ban[nome]</option>";
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="submit" class="btn btn-default" value="Gerar Encaminhamento de Conta">
                                    <input type="hidden" name="tipo" id="tipo" value="1">
                                    <input type="hidden" name="bolsista" id="bolsista" value="<?= $id_bol ?>">
                                    <input type="hidden" name="regiao" id="regiao" value="<?= $id_reg ?>">
                                </div>
                            </div>
                        </form> 
                    <?php } 
                    switch ($row['tipo_contratacao']) {
                        case 1: if ($ACOES->verifica_permissoes(58)) { $mostra_ctr = true; } else { $mostra_ctr = false; } break;
                        case 3: if ($ACOES->verifica_permissoes(59)) { $mostra_ctr = true; } else { $mostra_ctr = false; } break;
                        case 4: if ($ACOES->verifica_permissoes(60)) { $mostra_ctr = true; } else { $mostra_ctr = false; } break;
                    }

                    if ($mostra_ctr) { ?>
                        <h3 class="text-danger">CONTROLE DE DOCUMENTOS</h3>
                        <hr class="hr-danger">
                        <table class="table table-condensed table-hover table-striped">
                            <thead>
                                <tr class="active">
                                    <th width="70%">DOCUMENTO</th>
                                    <th width="15%" class="center">STATUS</th>
                                    <th width="15%" class="center">DATA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qr_documentos = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$row[tipo_contratacao]'");
                                while ($row_documento = mysql_fetch_array($qr_documentos)) {
                                    $qr_verificacao = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status WHERE tipo = '$row_documento[0]' AND id_clt = '$row[0]'");
                                    $row_verificacao = mysql_fetch_array($qr_verificacao);
                                    $num_verificacao = mysql_num_rows($qr_verificacao);

                                    if (!empty($num_verificacao) or ($row_documento['documento'] == 'PIS' and $emissao == true)) {
                                        $status = 'imagens/assinado.gif';
                                    } else {
                                        $status = 'imagens/naoassinado.gif';
                                    }?>

                                    <tr>	  	
                                        <td><?=$row_documento['documento']?></td>
                                        <td class="center"><img src="<?=$status?>" width="15" height="17"></td>
                                        <td class="center"><?=$row_verificacao['data']?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="center" class="linha" style="font-size:16px;">
                                        <img src="imagens/assinado.gif" width="15" height="17" align="absmiddle"> Emitido  
                                        <img src="imagens/naoassinado.gif" width="15" height="17" align="absmiddle"> N&atilde;o Emitido
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <?php include_once 'template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="js/jquery.validationEngine-2.6.js"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="js/jquery.maskedinput-1.3.1.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
        <script type="text/javascript" src="uploadfy/scripts/swfobject.js"></script>
        <script type="text/javascript" src="uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
        <script type="text/javascript">
            $().ready(function() {
            <?php if ($row['foto'] == '1') { ?>
                    $("#bt_deletar").show();
            <?php } ?>

                $("#bt_deletar").click(function() {
                    $.post('include/excluir_foto.php',
                            {nome: '<?= $_REQUEST['reg'] ?>_<?= $_REQUEST['pro'] ?>_<?= $_REQUEST['bol'] ?>.gif', ID: '<?= $_REQUEST['bol'] ?>'},
                    function() {
                        $("#imgFile").attr('src', 'fotos/semimagem.gif');
                        $("#bt_deletar").hide();
                        $('#bt_enviar').uploadifySettings('buttonText', 'Adicionar foto');
                    }

                    );
                });

                $("#bt_enviar").uploadify({
                    'uploader': 'uploadfy/scripts/uploadify.swf',
                    'script': 'uploadfy/scripts/uploadify.php',
                    'folder': 'fotos',
                    'buttonText': '<?php if ($row['foto'] == '1') { ?>Alterar<?php } else { ?>Adicionar<?php } ?> foto',
                    'queueID': 'fileQueue',
                    'cancelImg': 'uploadfy/cancel.png',
                    'auto': true,
                    'method': 'post',
                    'multi': false,
                    'fileDesc': 'Gif',
                    'fileExt': '*.gif;',
                    'onOpen': function() {
                        $("#fileQueue").show('slow');
                    },
                    'onAllComplete': function() {
                        $("#bt_deletar").show('slow');
                        $('#imgFile').attr('src', 'fotos/<?= $_REQUEST['reg'] ?>_<?= $_REQUEST['pro'] ?>_<?= $_REQUEST['bol'] ?>.gif');
                        $("#fileQueue").hide('slow');
                        $('#bt_enviar').uploadifySettings('buttonText', 'Alterar foto');
                    },
                    'scriptData': {'regiao': <?= $_REQUEST['reg'] ?>, 'projeto': <?= $_REQUEST['pro'] ?>, 'id_participantes': <?= $_REQUEST['bol'] ?>}
                });
            });
        </script>
    </body>
</html>
                            
                            
                            
                            
                            