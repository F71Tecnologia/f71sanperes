<?php
    
    if (!isset($_COOKIE['logado'])) {
        header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
        exit;
    }

    include('../conn.php');
    include('../wfunction.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Financeira</title>
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
    </head>
    <body>
        <form method="post" action="#" enctype="multipart/form-data">
            <div class="panel panel-body">    
                <input type="file" name="arquivo" class="form-control"><br><br>
                <input type="text" name="projeto"><br><br>
                <input type="submit" name="salvar" value="Salvar">
            </div>
        </form>
        <?php
        $target_file = $target_dir . basename($_FILES["arquivo"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (isset($_POST["salvar"])) {
            
            $row = 1;
            if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                while (($retorno = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $qry = "INSERT INTO financeiro_banco_retorno (data, dcto, conta_corrente, lancamento, credito, debito, saldo, id_projeto, status)
                            VALUE ('".convertedata($retorno[0], 'Y-m-d')."','{$retorno[2]}','','{$retorno[1]}','".str_replace(',','.',str_replace('.', '', $retorno[3]))."','".abs(str_replace(',','.',str_replace('.', '', $retorno[4])))."','".str_replace(',','.',str_replace('.', '', $retorno[5]))."','{$_REQUEST['projeto']}',1)";
                    mysql_query($qry) or die(mysql_error());
                }
                $row++;
            }
            fclose($handle);
        } ?>
    </body>
</html>