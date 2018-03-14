<?php
include("../conn.php");
include("../classes/SaidaClass.php");
include("../wfunction.php");

$id_saida = $_REQUEST['id_saida'];
$tipo = $_REQUEST['id'];

//comprovantes
if($tipo == 1){
    $saida = new Saida();
    $result = $saida->getSaidaFile($id_saida);
    $id_file = "id_saida_file";
    $tipo_file = "tipo_saida_file";
}

//comprovantes de pgt
if($tipo == 2){
    $saida = new Saida();
    $result = $saida->getSaidaFilePg($id_saida);
    $id_file = "id_pg";
    $tipo_file = "tipo_pg";
    $ext_file = "_pg";
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
            
            <div class="row">
                <?php while($row = mysql_fetch_assoc($result)){ ?>
                <div class="col-lg-3 col-md-6">
                    <a href="../../comprovantes/<?php echo "{$row[$id_file]}.{$row['id_saida']}{$ext_file}{$row[$tipo_file]}"; ?>" target="_blank">
                        <div class="panel panel-primary border-finan">
                            <div class="panel-heading" id="quad1">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-file-text-o fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-center">
                                <span>Nº <?php echo $row[$id_file]; ?></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
        
        </form>        
    </body>
</html>