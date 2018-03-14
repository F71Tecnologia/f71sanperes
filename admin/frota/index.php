<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/regiao.php");
include("../../classes/BotoesClass.php");
include("../../classes/ProjetoClass.php");
include("../../classes/EntradaClass.php");
include("../../classes/ObrigacoesClass.php");
include("../../classes_permissoes/acoes.class.php");

$ACOES = new Acoes();

$usuario = carregaUsuario();

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];

$RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_regiao = '{$usuario['id_regiao']}' AND status = 1");
$RE_combustivel = mysql_query("
SELECT A.*, date_format(A.data, '%d/%m/%Y') as data, B.marca, B.modelo, B.placa placa_carro, C.nome nome_funcionario, C.rg rg_funcionario
FROM fr_combustivel A
LEFT JOIN fr_carro B ON (A.id_carro = B.id_carro)
LEFT JOIN funcionario C ON (A.id_user = C.id_funcionario)
WHERE A.id_regiao = '{$usuario['id_regiao']}' AND A.status_reg = 1 
ORDER BY A.id_combustivel DESC");

$RE_rota = mysql_query("
SELECT A.id_rota, B.modelo, B.placa, A.destino, A.kmini, A.kmfim, date_format(A.data, '%d/%m/%Y') as data, C.regiao, A.status_reg
FROM fr_rota A 
LEFT JOIN fr_carro B ON B.id_carro = A.id_carro
LEFT JOIN regioes C ON C.id_regiao = B.id_regiao
WHERE A.id_regiao = '{$usuario['id_regiao']}' AND /*A.id_user = '{$usuario['id_funcionario']}' AND */ A.status_reg IN (1,2)
ORDER BY A.status_reg ASC, A.data DESC, A.id_rota DESC");
while($RowRota = mysql_fetch_array($RE_rota)){
    $arrayRotas[$RowRota['status_reg']][] = $RowRota;
}

$RE_multa = mysql_query("
SELECT A.id_multa, B.modelo, B.placa, B.marca, A.tipo, A.local, C.regiao, D.nome nome1
FROM fr_multa A 
LEFT JOIN fr_carro B ON B.id_carro = A.id_carro
LEFT JOIN regioes C ON C.id_regiao = B.id_regiao
LEFT JOIN funcionario D ON D.id_funcionario = A.id_user
WHERE A.id_regiao = '{$usuario['id_regiao']}' AND /*A.id_user = '{$usuario['id_funcionario']}' AND */ A.status_reg IN (1,2)
ORDER BY A.data DESC");

$nome_pagina = "Gestão da Frota";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$nome_pagina); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><?=$icon[2]?> - ADMINISTRATIVO - <small><?= $nome_pagina ?></small></h2></div>
                    <?php if(!empty($_SESSION['error'])) { ?><div class="alert alert-<?= $_SESSION['error']['tipo'] ?>"><?= $_SESSION['error']['msg'] ?></div><?php unset($_SESSION['error']); } ?>
                    <div class="col-sm-12 no-padding">
                        <div class="col-lg-3 col-sm-6">
                            <div class="stat-panel">
                                <div class="stat-row">
                                    <div class="stat-cell bg-info darker"><!-- Success darker background -->
                                        <i class="fa fa-car bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Cadastro de Veículo</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-info no-border-b no-padding text-center">
                                        <div class="stat-cell col-xs-12 padding-sm no-padding-hr pointer"><!-- Small padding, without horizontal padding -->
                                            <div class="col-sm-6 text-center text-sm stat border-r" data-key="1"><i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR</div>
                                            <a href="cad_carro.php"  class="col-sm-6 text-center text-sm border-l"><i class="fa fa-plus"></i> CADASTRAR</a>
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="stat-panel">
                                <div class="stat-row">
                                    <div class="stat-cell bg-success darker"><!-- Success darker background -->
                                        <i class="fa fa-battery-quarter bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Solicitação de Combustível</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-success no-border-b no-padding text-center">
                                        <div class="stat-cell col-xs-12 padding-sm no-padding-hr pointer"><!-- Small padding, without horizontal padding -->
                                            <div class="col-sm-6 text-center text-sm stat border-r" data-key="2"><i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR</div>
                                            <a href="cad_combustivel.php"  class="col-sm-6 text-center text-sm border-l"><i class="fa fa-plus"></i> CADASTRAR</a>
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="stat-panel">
                                <div class="stat-row">
                                    <div class="stat-cell bg-warning darker"><!-- Success darker background -->
                                        <i class="fa fa-refresh bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Controle de Rotas</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-warning no-border-b no-padding text-center">
                                        <div class="stat-cell col-xs-12 padding-sm no-padding-hr pointer"><!-- Small padding, without horizontal padding -->
                                            <div class="col-sm-6 text-center text-sm stat border-r" data-key="3"><i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR</div>
                                            <a href="cad_rota.php"  class="col-sm-6 text-center text-sm border-l"><i class="fa fa-plus"></i> CADASTRAR</a>
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="stat-panel">
                                <div class="stat-row">
                                    <div class="stat-cell bg-danger darker"><!-- Success darker background -->
                                        <i class="fa fa-cab bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Controle de Multas</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-danger no-border-b no-padding text-center">
                                        <div class="stat-cell col-xs-12 padding-sm no-padding-hr pointer"><!-- Small padding, without horizontal padding -->
                                            <div class="col-sm-6 text-center text-sm stat border-r" data-key="4"><i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR</div>
                                            <a href="cad_multa.php" class="col-sm-6 text-center text-sm border-l"><i class="fa fa-plus"></i> CADASTRAR</a>
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 panel-1" style="display: none;">
                            <?php if(mysql_num_rows($RE_carros) > 0) { ?>
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Veículos Cadastros</h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-hover table-condensed table-striped text-sm valign-middle">
                                        <thead>
                                            <tr>
                                                <td class="text-center text-bold">Modelo</td>
                                                <td class="text-center text-bold">Ano</td>
                                                <td class="text-center text-bold">Placa</td>
                                                <td class="text-center text-bold">Ap&oacute;lice</td>
                                                <td class="text-center text-bold">Tel. Seguro</td>
                                                <td class="text-center text-bold">Imagem</td>
                                                <td class="text-center text-bold">Multas</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($Row_carros = mysql_fetch_array($RE_carros)){
                                            $img = ($Row_carros['foto'] != '0') ? "<a target='blank' href='fotos/carro{$Row_carros['id_carro']}{$Row_carros['foto']}'><img src='fotos/carro{$Row_carros['id_carro']}"."{$Row_carros['foto']}' width='25' height='25'></a>" : "S/Imagem"; ?>
                                            <tr>
                                              <td class="text-center"><?= $Row_carros['modelo'] ?></td>
                                              <td class="text-center"><?= $Row_carros['ano'] ?></td>
                                              <td class="text-center"><?= $Row_carros['placa'] ?></td>
                                              <td class="text-center"><?= $Row_carros['apolice'] ?></td>
                                              <td class="text-center"><?= $Row_carros['telefone'] ?></td>
                                              <td class="text-center"><?= $img ?></td>
                                              <td class="text-center"><?= mysql_num_rows(mysql_query("SELECT id_multa FROM fr_multa WHERE id_carro = {$Row_carros['id_carro']}")) ?></td>
                                            </tr>       
                                            <?php } ?>  
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-info">Nenhum veículo cadastrado!</div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 panel-2" style="display: none;">
                            <?php if(mysql_num_rows($RE_combustivel) > 0) { ?>
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Solicitações de Combustivel</h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-hover table-condensed table-striped text-sm valign-middle">
                                        <thead>
                                            <tr>
                                                <td class="text-left text-bold">Carro</td>
                                                <td class="text-left text-bold">Placa</td>
                                                <td class="text-left text-bold">Responsável</td>
                                                <td class="text-center text-bold">Data</td>
                                                <td class="text-center text-bold">Valor</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($Row_combustivel = mysql_fetch_array($RE_combustivel)){ ?>
                                            <tr>
                                                <td class="text-left"><?= ($Row_combustivel['id_carro']) ? "{$Row_combustivel['marca']} - {$Row_combustivel['modelo']}" : $Row_combustivel['carro'] ?></td>
                                                <td class="text-left"><?= ($Row_combustivel['id_carro']) ? $Row_combustivel['placa_carro'] : $Row_combustivel['placa'] ?></td>
                                                <td class="text-left"><?= ($Row_combustivel['id_user']) ? "{$Row_combustivel['nome_funcionario']} - {$Row_combustivel['rg_funcionario']}" : "{$Row_combustivel['nome']} - {$Row_combustivel['rg']}" ?></td>
                                                <td class="text-center"><?= $Row_combustivel['data'] ?></td>
                                                <td class="text-center"><?= $Row_combustivel['valor'] ?></td>
                                            </tr>       
                                            <?php } ?>  
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-success">Nenhuma solicitação de combustível!</div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 panel-3" style="display: none;">
                            <?php if(count($arrayRotas) > 0) { ?>
                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Controle dos Veículos</h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-hover table-condensed text-sm valign-middle">
                                        <tbody>
                                            <?php foreach ($arrayRotas as $key => $value) { ?>
                                                <?php if($key == 1) { ?>
                                                    <tr class="active"><td colspan="7" class="text-center primary">HIST&Oacute;RICO DE RETIRADA</tr>
                                                    <tr>
                                                        <td class="text-center text-bold">Ve&iacute;culo</td>
                                                        <td class="text-center text-bold">Placa</td>
                                                        <td class="text-center text-bold">Local de Origem</td>
                                                        <td class="text-center text-bold">Destino</td>
                                                        <td class="text-center text-bold">Data</td>
                                                        <td class="text-center text-bold">KM</td>
                                                        <td class="text-center text-bold">Entregar</td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr class="active"><td colspan="7" class="text-center primary">HIST&Oacute;RICO DE ENTREGA</tr>
                                                    <tr>
                                                        <td class="text-center text-bold">Ve&iacute;culo</td>
                                                        <td class="text-center text-bold">Placa</td>
                                                        <td class="text-center text-bold">Local de Origem</td>
                                                        <td class="text-center text-bold">Destino</td>
                                                        <td class="text-center text-bold">Data</td>
                                                        <td class="text-center text-bold">KM</td>
                                                        <td class="text-center text-bold">KM entrega</td>
                                                    </tr>
                                                <?php } ?>
                                                <?php foreach ($value as $RowRota) { ?>
                                                     <tr>
                                                        <td class="text-center"><?= $RowRota['modelo'] ?></td>
                                                        <td class="text-center"><?= $RowRota['placa'] ?></td>
                                                        <td class="text-center"><?= $RowRota['regiao'] ?></td>
                                                        <td class="text-center"><?= $RowRota['destino'] ?></td>
                                                        <td class="text-center"><?= $RowRota['data'] ?></td>
                                                        <td class="text-center"><?= $RowRota['kmini'] ?></td>
                                                        <?php if($key == 1) { ?>
                                                        <td class="text-center"><button type="button" class="entregar btn btn-xs btn-info" data-rota="<?= $RowRota['id_rota'] ?>">OK</button></td>
                                                        <?php } else { ?>
                                                        <td class="text-center"><?= $RowRota['kmfim'] ?></td>
                                                        <?php } ?>
                                                    </tr>  
                                                <?php } ?>
                                             <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-warning">Nenhuma rota encontrado!</div>
                            <?php } ?>
                        </div>
                        <div class="col-xs-12 panel-4" style="display: none;">
                            <?php if(mysql_num_rows($RE_multa) > 0) { ?>
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Controle de Multas</h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-hover table-condensed table-striped text-sm valign-middle">
                                        <thead>
                                            <tr>
                                                <td class="text-center text-bold">Modelo</td>
                                                <td class="text-center text-bold">Placa</td>
                                                <td class="text-center text-bold">Tipo Infra&ccedil;&atilde;o</td>
                                                <td class="text-center text-bold">Localiza&ccedil;&atilde;o da Infra&ccedil;&atilde;o</td>
                                                <td class="text-center text-bold">Real Infrator</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($RowMulta = mysql_fetch_array($RE_multa)){ ?>
                                            <tr>
                                                <td class="text-center"><?= $RowMulta['marca'] . '.' . $RowMulta['modelo'] ?></td>
                                                <td class="text-center"><?= $RowMulta['placa'] ?></td>
                                                <td class="text-center"><?= $RowMulta['tipo'] ?></td>
                                                <td class="text-center"><?= $RowMulta['local'] ?></td>
                                                <td class="text-center"><?= $RowMulta['nome1'] ?></td>
                                            </tr>       
                                            <?php } ?>  
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="alert alert-danger">Nenhuma multa cadastrada!</div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <?php include("../../template/footer.php"); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('body').on('click', '.stat', function(){
                    var $this = $(this);
                    var key = $this.data('key');
//                    
//                    $(".panel-1, .panel-2, .panel-3, .panel-4").addClass('hide');
//                    $(".panel-"+key).removeClass('hide');
                
                    $(".panel-1, .panel-2, .panel-3, .panel-4").slideUp();
                    $( ".seta" ).removeClass( "fa-arrow-circle-up" ).addClass( "fa-arrow-circle-down" );
                    if($('.panel-'+key).css('display') == 'none'){
                        $('.panel-'+key).slideDown();
                        $this.find( ".seta" ).removeClass( "fa-arrow-circle-down" ).addClass( "fa-arrow-circle-up" );
                    } else {
                        $this.find( ".seta" ).removeClass( "fa-arrow-circle-up" ).addClass( "fa-arrow-circle-down" );
                    }
                });
                
                $('body').on('click', '.entregar', function(){
                    var rota = $(this).data('rota');
                    
                    var html = 
                        $('<form>', { class: 'form-horizontal', id: 'form-entregar', method: 'post', action: 'cad_rota.php' }).append(
                            $('<div>', { class: 'col-md-offset-1 col-md-10' }).append(
                                $('<div>', { class: 'form-group' }).append(
                                    $('<label>', { text: 'Km Final' }),
                                    $('<input>', { type: 'text', class: 'form-control', name: 'km' }),
                                    $('<input>', { type: 'hidden', name: 'enviar4' }),
                                    $('<input>', { type: 'hidden', name: 'rota', value: rota })
                                )
                            ),
                            $('<div>', { class: 'clear' })
                        )
                    
                    bootConfirm(
                        html, 
                        'Entregar', 
                        function(data){
                            if(data == true){
                                $('#form-entregar').submit();
                            }
                        }, 
                        'info'
                    );
                });
            });
        </script>
    </body>
</html>