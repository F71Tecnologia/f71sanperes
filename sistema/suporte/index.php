<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SuporteClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$botoes = new BotoesClass();
$classDefaults = $botoes->classModulos;
$modulos = $botoes->getModulos();
$objSuporte = new SuporteClass();

//CARREGANDO AS TAREFAS
$objCriteria = new stdClass();
$objCriteria->criado_por = $usuario['id_funcionario'];
$status = null;
if(validate($_REQUEST['filtrar'])){
    $status = validatePost('status');
    if($status == 1){
        $objCriteria->status = array(1,2,3);
    }else{
        $objCriteria->status = 4;
    }
}else{
    $objCriteria->status = array(1,2,3);
}

$lista = $objSuporte->getListSuporte($objCriteria);

$optionStatus = array(1 => "Abertos", 2 => "Fechados");
$statusSelect = (!empty($status)) ? $status : 1;

//echo $_SESSION['voltar'];

if(isset($_SESSION['voltar'])){
    $statusSelect = $_SESSION['voltar'];
    session_destroy();
}elseif(isset($_REQUEST['status'])){
    $statusSelect = $_REQUEST['status'];
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Suporte</title>
        
        <link rel="shortcut icon" href="../../favicon.png" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <form action="" method="post" id="form1" name="form1" class="form-horizontal top-margin1">
            <div class="container">
                <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema</h2></div>
                
                <input type="hidden" name="home" id="home" value="" />
                
                <ul class="breadcrumb">
                    <li><a href="../../">Home</a></li>
                    <li><a href="javascript:;" data-key="1" data-nivel="../../" class="return_principal">Principal</a></li>
                    <li class="active">Suporte</li>
                </ul>
                
                <fieldset>
                    <legend>Suporte</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Status</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect($optionStatus, $statusSelect, "id='projetoFinal' name='status' id='status' class='form-control'") ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" name="filtrar" id="filtrar" value="Filtrar" class="btn btn-primary">
                            <input type="hidden" name="id_suporte" id="id_suporte" value="" />
                            <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['voltar']; ?>" />
                            
                            <a href="form_suporte.php" class="btn btn-success">Novo Suporte</a>
                        </div>
                    </div>
                </fieldset>
                
                <table class='table table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Criado EM</th>
                            <th>Título</th>
                            <th>Ultima alteração</th>
                            <th>Respondido Por</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th colspan="2">Açoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($lista as $k => $suporte){ $fechado = ($suporte['status']!=4)?"":"-disabled"; ?>
                        <tr class="<?php echo SuporteClass::convertPrioridadeClass($suporte['prioridade']); ?>" id="<?php echo $k; ?>">
                            <td><?php echo $k; ?></td>
                            <td><?php echo $suporte['criado_em']; ?></td>
                            <td><?php echo $suporte['assunto']; ?></td>
                            <td><?php echo $suporte['alterado_em']; ?></td>
                            <td><?php echo $suporte['respondido_por']; ?></td>
                            <td><?php echo SuporteClass::convertPrioridade($suporte['prioridade']); ?></td>
                            <td><?php echo SuporteClass::convertStatus($suporte['status']); ?></td>
                            <td><img src="../../imagens/icones/icon-view.gif" title="Visualizar" class="bt-image center-block" data-type="ver" data-key="<?php echo $k; ?>"></td>
                            <td><img src="../../imagens/icones/icon-delete<?php echo $fechado?>.gif" title="Fechar" class="<?php echo ($suporte['status']!=4) ? "bt-image" : "";?> center-block" data-type="fechar" data-key="<?php echo $k; ?>"></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <footer>
                    <div class="row">
                        <div class="page-header"></div>
                        <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                        <div class="col-lg-12">
                            <p>Pay All Fast 3.0</p>
                            <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                        </div>
                    </div>
                </footer>
            </div>
        
        </form>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/sistema/suporte.js"></script>        
    </body>
</html>