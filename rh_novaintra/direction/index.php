<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('class.php');

$get_clt = $_REQUEST['clt'];

$usuario = carregaUsuario();
$row = getGeral($get_clt);

$valida_unidade = false;
$valida_funcionario = false;

//valida funcionario
if(($row['endereco_clt'] != '') && ($row['cidade_clt'] != '') && ($row['uf_clt'] != '')){
    $valida_funcionario = true;
    $partida = acentoMaiusculo(removeGeral($row['endereco_clt']) . ", {$row['bairro_clt']}, {$row['cidade_clt']} - {$row['uf_clt']}");
}

// valida unidade
if(($row['endereco_uni'] != '') && ($row['cidade_uni'] != '') && ($row['uf_uni'] != '')){
    $valida_unidade = true;
    $destino = acentoMaiusculo(removeGeral($row['endereco_uni']) . ", {$row['bairro_uni']}, {$row['cidade_uni']} - {$row['uf_uni']}");
}

//echo "{$partida}<br />";
//echo "{$destino}<br />";

//trata foto do funcionário
if ($row['foto_clt'] == '1') {
    $nome_imagem = $row['id_regiao'] . '_' . $row['id_projeto'] . '_' . $row['id_clt'] . '.gif';
} else {
    $nome_imagem = '../imagens/semFoto.png';
}

if($row['num_clt'] != ''){
    $num_clt = ", {$row['num_clt']}";    
}
if($row['complem_clt'] != ''){
    $complemento_clt = ", {$row['complem_clt']}";
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Mapa de Deslocamento Funcional");
$breadcrumb_pages = array("Lista Projetos" => "../ver.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);", "Visualizar Participante" => "javascript:void(0);");
$breadcrumb_attr = array(
    "Visualizar Projeto" => "class='link-sem-get' data-projeto='{$row['id_projeto']}' data-form='form1' data-url='../ver.php'",
    "Lista Participantes" => "class='link-sem-get' data-projeto='{$row['id_projeto']}' data-form='form1' data-url='../bolsista.php'",
    "Visualizar Participante" => "class='link-sem-get' data-pro='{$row['id_projeto']}' data-clt='$get_clt' data-form='form1' data-url='../ver_clt.php'"
); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Mapa de Deslocamento Funcional</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <style>
        #titulo_mapa3{
            background: #D5D3D3 url('../../imagens/icones/icon-maps.png') no-repeat left;
            padding: 10px 0 6px 40px;
            font-size: 16px;
            font-weight: bold;
        }

        #mapa{
            height: 400px;
            box-shadow: -2px -2px 3px #999, 2px 2px 3px #999;
        }

        #field_unidade, #field_unidade2{
            border: 0;
        }

        #geo_mapa{
            height: 380px;
            //width: 270px;
            //float: right;    
            border: 1px solid #D5D3D3;
            min-height: 638px;
            overflow: auto;
            padding: 5px;
        }
        
        .rota, .rota2{
            display: none;    
        }
        </style>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Mapa de Deslocamento Funcional</small></h2></div>
                </div>
            </div>
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <?php if($get_clt == ''){ ?>
                    <div class="alert alert-dismissable alert-danger">
                        <strong>Sem resultados, pois nenhum CLT foi encontrado. Vá em Visualizar Participantes e selecione o Funcionário desejado.</strong>
                    </div>
                <?php }elseif($valida_funcionario && $valida_unidade){ ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?="{$row["razao_empresa"]} - CNPJ: {$row["cnpj_empresa"]}"?>
                        </div>
                        <div class="panel-body">
                            <div class="stat-panel">
                                <div class="stat-cell col-md-2 no-padding valign-middle text-center bordered">
                                    <img src="../../fotosclt/<?=$nome_imagem?>" class="col-md-12">
                                </div> <!-- /.stat-cell -->
                                <div class="stat-cell col-md-10 no-padding valign-middle">
                                    <div class="stat-rows">
                                        <div class="stat-row">
                                            <div class="stat-cell padding-sm valign-middle bg-success">
                                                <strong>Funcionário: </strong><?=$row["nome_clt"]?>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="stat-cell padding-sm valign-middle bg-success">
                                                <strong>Endereço: </strong>
                                                <?=$row["endereco_clt"].$num_clt.$complemento_clt.", ".$row["bairro_clt"].", ".$row["cidade_clt"]." - ".$row["uf_clt"].", CEP: ".$row["cep_clt"]?>
                                                <a href="../alter_clt.php?clt=<?=$row['id_clt']?>&pro=<?=$row['id_projeto']?>&pagina=/intranet/rh/ver_clt.php" target="_blank">Editar</a>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="stat-cell padding-sm valign-middle">
                                                &nbsp;
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="stat-cell padding-sm valign-middle bg-danger">
                                                <strong>Unidade: </strong><?=$row["nome_uni"]?>
                                            </div>
                                        </div>
                                        <div class="stat-row">
                                            <div class="stat-cell padding-sm valign-middle bg-danger">
                                                <strong>Endereço: </strong>
                                                <?=$row["endereco_uni"].", ".$row["bairro_uni"].", ".$row["cidade_uni"]." - ".$row["uf_uni"].", CEP: ".$row["cep_uni"]?>
                                                <a href="../../adm/adm_unidade/form_unidade.php" target="_blank">Editar</a>
                                                <?php $_SESSION['unidade'] = $row['id_uni']; ?>
                                            </div>
                                        </div>
                                    </div> <!-- /.stat-rows -->
                                </div> <!-- /.stat-cell -->
                            </div>
                        </div>
                        <div class="panel-footer">
                            <input type="hidden" id="partida" value="<?=$partida?>" />
                            <input type="hidden" id="destino" value="<?=$destino?>" />
                            
                            <div class="col-md-7 no-padding-hr">
                                <div class="alert alert-dismissable alert-danger rota">
                                    <strong>Rota de Transporte Público não encontrada, <br />deseja visualizar a rota de Carro? <a href="javascript:;" id="aqui">Clique aqui</a></strong>
                                </div>

                                <div id="dados_mapa">
                                    <div id="mapa" class="col-md-12 no-padding">
                                        <script>
                                            $(document).ready(function(){
                                                console.log("OK");
                                                var partida = $("#partida").val();
                                                var destino = $("#destino").val();
                                                getRota(partida, destino);
                                                $("#aqui").click(function(){                                            
                                                    getRota(partida, destino,"D");
                                                    $(".rota").hide();
                                                    $("#dados_mapa").show();
                                                });
                                            });
                                        </script>
                                    </div>
                                </div><!--dados_mapa-->

                                <div class="alert alert-dismissable alert-danger rota2">
                                    <strong>Rota não encontrada.</strong>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div id="geo_mapa">
                                    <div class="bg-default padding-sm"><i class="fa fa-map-marker"></i> Dados do percurso</div>  
                                    <div id="trajeto"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    
                <?php }elseif(!$valida_funcionario){ ?>
                    <div class="alert alert-dismissable alert-danger rota2">
                        <strong>O mapa não pode ser exibido pois falta alguma informação relacionada ao endereço do Funcionário, <a href="../alter_clt.php?clt=<?=$row['id_clt']?>&pro=<?=$row['id_projeto']?>&pagina=/intranet/rh/ver_clt.php" target="_blank">Clique aqui</a> para editar</strong>
                        <?php $_SESSION['clt'] = $row['id_clt']; ?>
                    </div>
                <?php }elseif(!$valida_unidade){ ?>
                    <div class="alert alert-dismissable alert-danger rota2">
                        <strong>O mapa não pode ser exibido pois falta alguma informação relacionada ao endereço da Unidade, <a href="../../adm/adm_unidade/form_unidade.php" target="_blank">Clique aqui</a> para editar</strong>
                        <?php $_SESSION['unidade'] = $row['id_uni']; ?>
                    </div>
                <?php } ?>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAl3O3sodwtm6xisPvh6EM0PrTlqPZ7M_s&sensor=false"></script>
        <script src="js/mapa.js"></script>
    </body>
</html>