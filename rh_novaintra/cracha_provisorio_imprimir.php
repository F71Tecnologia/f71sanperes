
<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
include('../conn.php');
include('../empresa.php');
include('../wfunction.php');
include('../classes/regiao.php');
include('../classes/LogClass.php');
//include('../classes/RhCltClassOld.php');

$log = new Log();
$img = new empresa();
$objRegiao = new regiao();
$fw = new \hub\fwClass();
$objClt = $fw->Clt->setDefault();
$usuario = carregaUsuario();

$array_clt = (is_array($_REQUEST['id_clt']))? $_REQUEST['id_clt'] : array($_REQUEST['id_clt']);


//print_array($objClt);
//echo "SELECT * FROM rhempresa WHERE id_regiao = {$objClt->getIdRegiao()} LIMIT 1";
//$dadosEmpresa = (object) mysql_fetch_assoc(mysql_query("SELECT * FROM rhempresa WHERE id_regiao = {$objClt->getIdRegiao()} LIMIT 1"));


?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../favicon.png" rel="shortcut icon">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/style-print.css" rel="stylesheet" media="all">
        <style>
            div.pagina
            {
              page-break-after: always;
              page-break-inside: avoid;
            }
        </style>
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            
            <?php foreach ($array_clt as $key => $id_clt) {
                $objClt->setDefault();
                $objClt->setIdClt($id_clt);

                if($objClt->select()){
                    $objClt->getRow();
                    $dadosCurso = (object) mysql_fetch_assoc(mysql_query("SELECT * FROM curso WHERE id_curso = {$objClt->getIdCurso()} LIMIT 1"));
                    $objProjeto = (object) projetosId($objClt->getIdProjeto()); 
                } else {
                    echo $objClt->getError();
                    exit("</br>Houve um erro ao selecionar o CLT ({$id_clt})");
                } ?>
                <?= ((($key+1)%2) == 0) ? '<div class="col-xs-1 margin_b20"></div>' : '' ?>
                <div class="col-xs-5 bordered margin_b20" style="height: 378px;">
                    <div class="col-xs-12 text-center margin_t20">
                        <?php if(file_exists("../fotosclt/{$objClt->getIdRegiao()}_{$objClt->getIdProjeto()}_{$objClt->getIdClt()}.gif")){ ?>
                        <img src="../fotosclt/<?="{$objClt->getIdRegiao()}_{$objClt->getIdProjeto()}_{$objClt->getIdClt()}"?>.gif" class="img-thumbnail" style="width: 100px; height: 130px;">
                        <?php } else { ?>
                        <img src="../fotosclt/semimagem.gif" class="img-thumbnail" style="width: 100px; height: 130px;">
                        <?php } ?>
                        <hr>
                    </div>
                    <h3 class="text-center" style="margin-bottom: -20px;"><small><?=$objProjeto->nome?></small></h3>
                    <h3 class="text-center" style="margin-bottom: -10px;"><small><?=$dadosCurso->nome?></small></h3>
                    <h3 class="text-center" style="margin-bottom: -15px;"><?=$objClt->getNome()?></h3>
                    <h3 class="text-center" style="margin-bottom: 8px;"><small><?=$objClt->getPis()?></small></h3>
                    <div><?=gera_barras("*".$objClt->getPis()."*")?></div>
                    
                    <!--<div class="col-xs-12 text-center margin_t20" style="font-size: 12px;"><?=$objProjeto->nome?></div>
                    <div class="col-xs-12 text-center margin_t20" style="font-size: 16px;"><?=$dadosCurso->nome?></div>
                    <div class="col-xs-12 text-center text-lg text-bold margin_t20"><?=$objClt->getNome()?></div>-->
                </div>
                <?= ((($key+1)%2) == 1) ? '<div class="col-xs-1 margin_b20"></div>' : '' ?>
        <?= ((($key+1)%4) == 0) ? '<br/></div><div class="pagina">' : '' ?>
            <?php } ?>

<?php
function gera_barras($id)
{
    $resp = array();
    $resp[0] = "zero.jpg";
    $resp[1] = "um.jpg";
    $resp[2] = "dois.jpg";
    $resp[3] = "tres.jpg";
    $resp[4] = "quatro.jpg";
    $resp[5] = "cinco.jpg";
    $resp[6] = "seis.jpg";
    $resp[7] = "sete.jpg";
    $resp[8] = "oito.jpg";
    $resp[9] = "nove.jpg";
    $resp['*'] = "asterisco.jpg";
    
    $ret = "";
    for($i = 0; $i < strlen($id); $i++)
    {
	// Tam original width:22px; height:83px;
	$ret .= "<img src = '../barcode/" . $resp[$id[$i]] . "' style = 'width:14px; height:55px; margin: 0; padding: 0'/>\n";
    }
    return $ret;
}
?>
            
        </div>
        <!-- javascript aqui -->
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
