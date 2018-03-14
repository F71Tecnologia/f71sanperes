<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');

if(isset($_REQUEST['altera_padroes'])){
    $query = "SELECT * FROM cursos_padroes WHERE nome NOT LIKE '%h'";
    $x = mysql_query($query);
    while($row = mysql_fetch_assoc($x)){
        echo "# {$row['nome']} <br>\r\n";
        echo "UPDATE cursos_padroes SET nome = '{$row['nome']} 40h' WHERE nome = '{$row['nome']}';";
        echo "<br><br>\r\n";
    }
    exit();
}

?>
<!DOCTYPE html>
<html>
    <body>
        <form method="post" action="#" enctype="multipart/form-data">
            <div>    
                <input type="file" name="arquivo"><br><br>
                <input type="text" name="projeto"><br><br>
                <input type="submit" name="salvar" value="Salvar">
            </div>
        </form>
        <?php
        $target_file = $target_dir . basename($_FILES["arquivo"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (isset($_POST["salvar"]))  {
            
            
            $row = 1;
            if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $qry = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, data_lancamento, historico, status)
                            VALUE ('{$_REQUEST['projeto']}','9999','2016-01-01','{$data[1]}',1)";
                    mysql_query($qry) or die(mysql_error());
                    $lancamento = mysql_insert_id();
//                    $row = mysql_fetch_assoc($qry);
//                    $lancamento = $row['id_lancamento'];

                    $qry_item1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, tipo, fornecedor, status) 
                                VALUES ('{$lancamento}', '{$data[11]}', '{$data[7]}', 2,'$data[0]', 1)";
                    mysql_query($qry_item1) or die($query.mysql_error());

                    $qry_item2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, tipo, fornecedor, status) 
                                VALUES ('{$lancamento}', '{$data[12]}', '{$data[7]}', 1,'$data[0]', 1)";
                    mysql_query($qry_item2) or die($query.mysql_error());
                }
                    $row++;
            }
            fclose($handle);
        } ?>
    </body>
</html>