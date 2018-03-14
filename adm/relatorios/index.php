<?php
if(!isset($_COOKIE['logado'])){
   header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
   exit;
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");

$usuario = carregaUsuario();

$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$icon = $botoes->iconsModulos;

$nome_pagina = "Relatórios de Gestão";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$nome_pagina); 

//$qr = "SELECT * FROM acoes AS A
//        LEFT JOIN funcionario_acoes_assoc AS B ON (A.acoes_id = B.acoes_id AND id_regiao = {$usuario['id_regiao']})
//        LEFT JOIN botoes AS C ON (C.botoes_menu = A.botoes_id)
//        WHERE B.id_funcionario = {$usuario['id_funcionario']} AND A.botoes_id = 23";
//
//$botoes = execQuery($qr);
/* echo "<pre>";
  print_r($botoes);
  echo "</pre>"; */

$arrayCookieBotoesGO = array(158,9);
$arrayDadosBotoesGO = array(
    'DEMONSTRATIVO_DESPESAS_PAGAMENTOS.pdf' => "DEMONSTRATIVO DE EXECUÇÃO DAS DESPESAS",
    'DEMONSTRATIVO_DESPESAS_RELACAO_PAGAMENTOS_OVG.pdf' => "DEMONSTRATIVO DE EXECUÇÃO DAS DESPESAS OVG",
    'DEMONSTRATIVO_MOVIMENTACAO_FINANCEIRA.pdf' => "DEMONSTRATIVO DA MOVIMENTAÇÃO FINANCEIRA NO PERÍODO",
    'DEMONSTRATIVO_MOVIMENTACAO_FINANCEIRA_OVG.pdf' => "DEMONSTRATIVO DA MOVIMENTAO FINANCEIRA NO PERODO OVG",
    'DEMONSTRATIVO_RECEITAS_DESPESAS.pdf' => "DEMONSTRATIVO DE EXECUÇÃO DAS RECEITAS E DESPESAS",
    'DEMONSTRATIVO_RECEITAS_RELACAO_RECEBIMENTOS.pdf' => "DEMONSTRATIVO DE EXECUÇÃO DAS RECEITAS",
    'DEMONSTRATIVO_RECEITAS_RELACAO_RECEBIMENTOS_OVG.pdf' => "DEMONSTRATIVO DE EXECUÇÃO DAS RECEITAS OVG"
);

?>
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
                    <div class="col-sm-12 no-padding">
                        
                        <div class="col-lg-4 col-sm-12">
                            <a href='painel_rh.php' class="stat-panel" style="text-decoration: none;">
                                <div class="stat-row">
                                    <div class="stat-cell bg-warning darker"><!-- Success darker background -->
                                        <i class="fa fa-users bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Painel do RH</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-warning no-border-b no-padding text-center">
                                        <div class="col-sm-12 stat-cell padding-sm no-padding-hr text-sm">
                                            <i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-lg-4 col-sm-12">
                            <a href='painel_financeiro.php' class="stat-panel" style="text-decoration: none;">
                                <div class="stat-row">
                                    <div class="stat-cell bg-pa-purple darker"><!-- Success darker background -->
                                        <i class="fa fa-line-chart bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Painel do Financeiro</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-pa-purple no-border-b no-padding text-center">
                                        <div class="col-sm-12 stat-cell padding-sm no-padding-hr text-sm">
                                            <i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-lg-4 col-sm-12">
                            <a href='painel_juridico.php' class="stat-panel" style="text-decoration: none;">
                                <div class="stat-row">
                                    <div class="stat-cell bg-success darker"><!-- Success darker background -->
                                        <i class="fa fa-balance-scale bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Painel do Jurídico</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-success no-border-b no-padding text-center">
                                        <div class="col-sm-12 stat-cell padding-sm no-padding-hr text-sm">
                                            <i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-lg-4 col-sm-12">
                            <a href='rel_comparativo.php' class="stat-panel" style="text-decoration: none;">
                                <div class="stat-row">
                                    <div class="stat-cell bg-default darker"><!-- Success darker background -->
                                        <i class="fa fa-file-text bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Relatório Comparativo</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-default no-border-b no-padding text-center">
                                        <div class="col-sm-12 stat-cell padding-sm no-padding-hr text-sm">
                                            <i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-lg-4 col-sm-12">
                            <a href='rel_proposta.php' class="stat-panel" style="text-decoration: none;">
                                <div class="stat-row">
                                    <div class="stat-cell bg-default darker"><!-- Success darker background -->
                                        <i class="fa fa-file-excel-o bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                        <span class="text-bg">Relatório de Proposta</span><br><!-- Big text -->
                                        <span class="text-sm"></span><!-- Small text -->
                                    </div>
                                </div> <!-- /.stat-row -->
                                <div class="stat-row">
                                    <div class="stat-counters bg-default no-border-b no-padding text-center">
                                        <div class="col-sm-12 stat-cell padding-sm no-padding-hr text-sm">
                                            <i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR
                                        </div>
                                    </div> <!-- /.stat-counters -->
                                </div>
                            </a>
                        </div>
                        
                        <?php if(in_array($_COOKIE['logado'],$arrayCookieBotoesGO)){
                            
                            foreach($arrayDadosBotoesGO as $link => $relatorio){?>
                            
                            <div class="col-lg-4 col-sm-12">
                                <a href='../../contabil/rel/<?php echo $link; ?>' class="stat-panel" style="text-decoration: none;">
                                    <div class="stat-row">
                                        <div class="stat-cell bg-default darker"><!-- Success darker background -->
                                            <i class="fa fa-file-excel-o bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                            <span class="text-bg"></span><br><!-- Big text -->
                                            <span class="text-sm"><?php echo $relatorio ?></span><!-- Small text -->
                                        </div>
                                    </div> <!-- /.stat-row -->
                                    <div class="stat-row">
                                        <div class="stat-counters bg-default no-border-b no-padding text-center">
                                            <div class="col-sm-12 stat-cell padding-sm no-padding-hr text-sm">
                                                <i class="seta fa fa-arrow-circle-down"></i> VISUALIZAR
                                            </div>
                                        </div> <!-- /.stat-counters -->
                                    </div>
                                </a>
                            </div>
                        
                        <?php } } ?>
                        <!--li><a href='rel_comparativo.php'><img src='../../img_menu_principal/relatorios_gestao.png' border='0' align='absmiddle'><br>RELATÓRIO COMPARATIVO</a></li>
                        <li><a href='rel_proposta.php'><img src='../../img_menu_principal/relatorios_gestao.png' border='0' align='absmiddle'><br>RELATÓRIO DE PROPOSTA FINANCEIRA</a></li-->
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
    </body>
</html>