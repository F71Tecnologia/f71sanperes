<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/FolhaClass.php");
include_once("../../classes/BotoesClass.php");
include_once("../../funcoes.php");
include_once("../../classes/RescisaoClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Contratos de Serviços Terceirizados", "id_form"=>"form1");
$breadcrumb_pages = array("Page1"=>"teste1.php", "Page2"=>"teste2.php");

list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

//OBJ RESCISÃO
$rescisao = new Rescisao();
$folhas = new Folha();
$resource_folha = $folhas->getDadosFolhaById($folha);
$dados_folha = array();
while($rows = mysql_fetch_assoc($resource_folha)){
  $dados_folha['projeto'] = $rows['id_projeto'];
  $dados_folha['mes_referente'] = $rows['mes_competencia'];
}
$dados_rescisao = $rescisao->getTotalizadorRescisaoByFolha($dados_folha['projeto'],$dados_folha['mes_referente']);
echo "<pre>";
    print_r($dados_rescisao);
echo "</pre>";


?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><?php echo $icon['3'] ?> - GESTÃO DE RH</h2></div>
                    <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                        <input type="hidden" name="home" id="home" value="" />

                        <h3>Totalizadores de Folha</h3>
                        
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr class="info text-center">
                                    <th colspan="2">RESCISÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-bold">
                                    <td colspan="2"><span class="artificio2"></span>» PROVENTOS</td>
                                </tr>
                                <?php foreach ($dados_rescisao["CREDITO"] as $key => $values){ ?>
                                    <?php $total_rescisao += $values["valor"]; ?>
                                    <?php if($values["valor"] != 0){ ?>
                                        <tr>
                                            <td><span class="artificio2"></span> » <?php echo $values["nome"]; ?></td>
                                            <td><?php echo "R$ " . number_format($values["valor"],2,",","."); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php  } ?>
                            </tbody>
                            <thead>
                                <tr class="info text-left text-bold">
                                    <td>» TOTAL </td>
                                    <td><?php echo "R$ " . number_format($total_rescisao,2,",","."); ?></td>
                                </tr>
                            </thead>
                        </table>
                        
                    </form>
                </div>
            </div>
            
            <?php include_once '../../template/footer.php'; ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>
