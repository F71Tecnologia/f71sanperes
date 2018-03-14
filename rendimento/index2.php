<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/InformeRendimentoClass.php');

$usuario = carregaUsuario();
$inf = new InformeRendimentoClass($usuario['id_master']);
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$bloqueado = false;     //TIRANDO O BLOQUEIO, A PARTIR DE HJ PODEM GERAR 29/02/2016
$down = false;

$regiao = (!empty($_REQUEST['id_reg'])) ? $_REQUEST['id_reg'] : $usuario['id_regiao'];
$projeto = $_REQUEST['pro'];
$ano_base = date('Y') - 1;

$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_array($result_user);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_array($result_master);

$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]' AND status = '1'");
$empresa = mysql_fetch_assoc($qr_empresa);

$qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");
$arProjetos = array("-1" => "« Selecione »");
while ($projeto = mysql_fetch_assoc($qr_projetos)) {
    $arProjetos[$projeto['id_projeto']] = $projeto['nome'];
}

$roMaster = montaQueryFirst("master", "nome", "id_master = {$usuario['id_master']}");

$projetos = getProjetos($regiao);

$optAnos = $inf->montaOptionsAnos();
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y") - 1;

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Informe de Rendimentos em Lote");
$breadcrumb_pages = array("Gestão de RH" => "../rh/principalrh.php");

if(isset($_REQUEST['gerar'])){
    $ano = $_REQUEST['ano'];
    $pro = $_REQUEST['pro'];
    $down = true;
    
    $path = "{$ano}/";
    
    $cond = array("id_projeto"=>$pro);
    
    $rs = montaQuery("rh_clt","id_clt,cpf",$cond);
    
    $nameZip = "{$ano}_{$pro}.zip";
    if(is_file($nameZip)){
        unlink($nameZip);
    }
    
    $zip = new ZipArchive();
    $zip->open($nameZip, ZIPARCHIVE::CREATE);
    
    foreach($rs as $clt){
        
        $filename = "{$clt['cpf']}.pdf";
        
        //if(is_file($path.$filename)){
            $zip->addFile($path.$filename,$filename);
        //}else{
        //    $arrayErros[] = "arquivo não encontrado ".$path.$filename;
        //}    
        
    }
    
    $zip->close();
    
    $msg .= "<div class=\"alert alert-dismissable alert-success\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>
                <strong>Arquivo gerado com sucesso! </strong> <a href='{$nameZip}'>Download do ZIP  {$projetos[$pro]}</a>.
            </div>";
    
    if(count($arrayErros) > 0){
        $msg .= "<br/>";
        $msg .= "<!--div class=\"note note-warning\">
                    <h4 class=\"note-title\">Aviso</h4>
                    Alguns cpfs não foram encontrados. (Provavelmente são novas contratações)";
        foreach($arrayErros as $msgErr){
            $msg .= "<p>{$msgErr}</p>";
        }
        $msg .= "</div-->";
    }
}

if($_COOKIE['logado'] == 179 || $_COOKIE['logado'] == 158 || $_REQUEST['dirf'] == 2015){
    $bloqueado = false;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Informe de Rendimentos</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Informe de Rendimentos em Lote</small></h2></div>
            <!--<form action="lote.php" method="post" name="form1" id="form1"  class="form-horizontal">-->
            <form action="" method="post" name="form1" id="form1"  class="form-horizontal">
                <input type="hidden" name="id_reg" value="<?= $regiao ?>">
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print">Ano Base/Calend</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optAnos, $anoSel, "id='ano' name='ano' class='form-control'") ?>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($arProjetos, $anoSel, "id='pro' name='pro' class='form-control required[custom[select]]'") ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php if($bloqueado){ ?>
                        <div class="alert alert-warning">Informes em análise</div>
                        <?php }else{ ?>
                        <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar Informes em Lote</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
            
            <?php if($down){ echo $msg; }?>
            
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
    </body>
</html>