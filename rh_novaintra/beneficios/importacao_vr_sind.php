<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
?>
<!DOCTYPE html>
<html>
    <body>
        <form method="post" action="#" enctype="multipart/form-data">
            <input type="file" name="arquivo">
            <input type="submit" name="salvar" value="Salvar">
        </form>
        <?php
        $target_file = $target_dir . basename($_FILES["arquivo"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
        if (isset($_POST["salvar"])) {

            echo "<br><br>#{$_FILES["arquivo"]["name"]}<br><br>";

            $row = 1;
            $ids_sind = array();
            $$sinds = array();
            if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 99999, ";")) !== FALSE) {
                    $vr = $data[2];
                    $va = $data[1];
                    $nome = $data[0];
                   
                    $select = "SELECT a.nome nome_curso,a.id_sindicato,b.nome nome_sindicato FROM curso a INNER JOIN rhsindicato b ON a.id_sindicato = b.id_sindicato WHERE a.nome LIKE '{$nome}' GROUP BY a.nome";
                    $result = mysql_query($select);
                    $num_rows = mysql_num_rows($result);
                    if ($row > 1) {

                        while ($x = mysql_fetch_assoc($result)) {
//                            echo "# funcao: $x[nome_curso] - sindicato: $x[id_sindicato] $x[nome_sindicato] - va $va - vr $vr <br><br>";
                            
                            if (!in_array($x['id_sindicato'], $ids_sind)) {
                                echo "UPDATE rhsindicato SET valor_refeicao = '$vr' WHERE id_sindicato = {$x['id_sindicato']}; <br><br>";
                                $ids_sind[] = $x['id_sindicato'];
                            }
                            $sinds[] = $x['id_sindicato'];
                        }
                    }
                    $row++;
                    
                }
                fclose($handle);
                print_r($ids_sind);
                echo '<br>';
                print_r(array_unique($sinds));
            } else {
                echo 'erro!!';
            }
            echo "<br><br>\n";
        }
        ?>
    </body>
</html>
