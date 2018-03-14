<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');

if (isset($_REQUEST['altera_padroes'])) {
    $query = "SELECT * FROM cursos_padroes WHERE nome NOT LIKE '%h'";
    $x = mysql_query($query);
    while ($row = mysql_fetch_assoc($x)) {
        echo "# {$row['nome']} <br>\r\n";
        echo "UPDATE cursos_padroes SET nome = '{$row['nome']} 40h' WHERE nome = '{$row['nome']}';";
        echo "<br><br>\r\n";
    }
    exit();
}

if(isset($_REQUEST['atualiza_funcoes'])){
    $query = "SELECT id_curso_padrao,nome FROM cursos_padroes;";
    $xxx = mysql_query($query);
    while($row = mysql_fetch_assoc($xxx)){
        $nome = substr($row['nome'], 0,-1);
        $q2 = "SELECT GROUP_CONCAT(id_curso) AS ids,nome FROM curso WHERE nome LIKE '%$nome%';";
        $y = mysql_query($q2);
        while($rr = mysql_fetch_assoc($y)){
            if(!empty($rr['ids'])){
                echo "UPDATE curso SET id_curso_padrao = {$row['id_curso_padrao']} WHERE id_curso IN({$rr['ids']}); # $nome - {$row['nome']}<br><br>\n";
            }
        }                
    }
    exit();
}

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
            if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $nome = trim($data[1]) . ' ' . trim($data[2]) . 'h';
                    $select = "SELECT * FROM cursos_padroes WHERE nome LIKE '{$nome}'";
                    $result = mysql_query($select);
                    $x = mysql_fetch_assoc($result);
                    $num_rows = mysql_num_rows($result);
                    $id_curso_padrao = $x['id_curso_padrao'];
                    if ($row > 1) {
                        echo " # $nome <br />\n";
                        echo "INSERT INTO cursos_unidades_assoc (id_curso_padrao,qtd_maxima,qtd_sms,qtd_minima,id_unidade) VALUES ('$id_curso_padrao','$data[3]','$data[4]','$data[5]','$data[6]'); ";
                        if ($num_rows == 0) {
                            $arr[] = "INSERT INTO cursos_padroes (nome,data_cad) VALUES ('$nome',NOW());<br />\n";
                        }
                        echo "<br /><br />\n";
                    }


                    $row++;
                }
                fclose($handle);
            }
            echo "<br><br>\n";
            foreach ($arr as $value) {
                echo $value;
            }
        }
        ?>
    </body>
</html>
