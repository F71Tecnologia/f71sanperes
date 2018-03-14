<?php
include("../conn.php");
include("../classes/EntradaClass.php");
include("../wfunction.php");

$id_nota = $_REQUEST['id'];
$id_entrada = $_REQUEST['id_entrada'];
$entrada = new Entrada();
$result = $entrada->getArquivos($id_nota,$id_entrada);

if($id_entrada != ""){
    $caminho = "../";
}else{
    $caminho = "";
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
            
            <div class="row">
                <?php while($row = mysql_fetch_assoc($result)){ ?>
                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo $caminho; ?>../adm/adm_notas/notas/<?php echo "{$row['id_file']}.{$row['tipo']}"; ?>" target="_blank">
                        <div class="panel panel-primary border-finan">
                            <div class="panel-heading" id="quad1">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-file-text-o fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-center">
                                <span>Nº <?php echo ($id_entrada == "") ? $row['id_file'] : $row['id_notas']; ?></span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
            
        </form>
        <script src="../resources/js/financeiro/prestador.js"></script>
    </body>
</html>