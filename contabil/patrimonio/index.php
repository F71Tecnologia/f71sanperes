<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
 
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$nome_pagina = 'Controle de Patrimônio';
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>$nome_pagina);
//$breadcrumb_pages = array("CONTROLE DE PATRIMÔNIO" => "contabil/patrimonio/");

$result_local = mysql_query("SELECT id_regiao FROM regioes");
$row_local = mysql_fetch_array($result_local);

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
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-contabil-header"><h2><span class="fa fa-bar-chart"></span> - Contabilidade<small> - <?= $nome_pagina ?></small></h2></div>
            <div class="col-sm-12 no-padding">
                <a href="form_patrimonio.php">
                    <div class="col-lg-4 col-sm-6 pointer stat" >
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-primary darker"><!-- Success darker background -->
                                    <i class="fa fa-desktop bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Cadastrar Patrimônio</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <a href="rel_patrimonio.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-success darker"><!-- Success darker background -->
                                    <i class="fa fa-file-excel-o bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Relatório de Patrimônio</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <a href="rel_patrimonioFoto.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-warning darker"><!-- Success darker background -->
                                    <i class="fa fa-image bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Relatório de Patrimônio (Foto)</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <div class="clear"></div>
            </div>
            
            <div class="col-sm-12 no-padding">
                <a href="resp_setor_patrimonio.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell darker" style="background-color: #928EB1 ; color: #fff;"><!-- Success darker background -->
                                    <i class="fa fa-user-plus bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Cadastrar Responsáveis por Setor</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
<!--                <a href="#">
                    <div class="col-lg-4 col-sm-6 pointer stat" target="_blank">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-danger darker"> Success darker background 
                                    <i class="fa fa-book bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i> Stat panel bg icon 
                                    <span class="text-bg">Relatório Patrimônio Detalhado</span><br> Big text 
                                    <span class="text-sm"></span> Small text 
                                </div>
                            </div>  /.stat-row 
                        </div>
                    </div>
                </a>-->
                <a href="setor_patrimonio.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-danger darker"><!-- Success darker background -->
                                    <i class="fa fa-archive bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Cadastrar Setores</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <a href="cat_patrimonio.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div style="background-color: #16A085 ; color: #fff;"class="stat-cell  darker"><!-- Success darker background -->
                                    <i class="fa fa-bars bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Cadastrar Categoria</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
            </div>
            
             <div class="col-sm-12 no-padding">
<!--                <a href="../../respo_setor_patrimonio.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell darker" style="background-color: #928EB1 ; color: #fff;"> Success darker background 
                                    <i class="fa fa-user-plus bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i> Stat panel bg icon 
                                    <span class="text-bg">Cadastrar Responsáveis por Setor</span><br> Big text 
                                    <span class="text-sm"></span> Small text 
                                </div>
                            </div>  /.stat-row 
                        </div>
                    </div>
                </a>-->
             </div>
            
            <div class="clear"></div>
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../resources/js/patrimonio.js"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>