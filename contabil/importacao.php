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
                <select name="projeto" id="projeto">
                    <option value="-1">Selecionar Projeto</option>
                    <option value="3302">Itaborai</option>
                    <option value="3303">SG I</option>
                    <option value="3304">SG II</option>
                    <option value="3315">Bangu</option>
                    <option value="3316">M Hermes</option>
                    <option value="3317">Realengo</option>
                    <option value="3318">Ricardo</option>
                    <option value="3319">São Pedro</option>
                    <option value="3320">Niteroi</option>
                    <option value="3338">Campos</option>
                    <option value="3331">Bebedouro</option>
                    <option value="3353">UPA Bebedouro</option>
                    <option value="3309">ADM</option>
                </select>
                <input type="date" name="data" id="data">
                <input type="file" name="arquivo">
                <input type="submit" name="salvar" value="Salvar">
            </div>
        </form>
        <?php
        $target_file = $target_dir . basename($_FILES["arquivo"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
        if (isset($_POST["salvar"])) {
            if (isset($_REQUEST['projeto']) && $_REQUEST['projeto'] > 0) {
                
                $query_lancamento = "INSERT INTO contabil_lancamento (id_projeto,data_lancamento,historico,status) VALUES ('{$_REQUEST['projeto']}','{$_REQUEST['data']}','SALDO INICIAL',1)";
                $result_1 = mysql_query($query_lancamento) or die($query_lancamento.mysql_error());
                $lancamento = mysql_fetch_assoc($result_1);
                $id_lancamento = mysql_insert_id();
                echo "INSERT INTO contabil_lancamento (id_projeto,data_lancamento,historico,status) VALUES ('{$_REQUEST['projeto']}','{$_REQUEST['data']}','SALDO INICIAL',1)</br>";
                $row = 1;
//              mysql_query($query_lancamento) or die($query_lancamento.mysql_error());
                if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $id_acesso = str_pad($data[0], 6, "0", STR_PAD_LEFT); 
                        $select = "SELECT id_conta FROM contabil_planodecontas WHERE cod_reduzido = '$id_acesso';";
                        $x = mysql_fetch_assoc(mysql_query($select));
                        $id_conta = $x['id_conta']; 
                        if ($row > 1) {
                            $query = "INSERT INTO contabil_lancamento_itens (id_conta,valor,tipo,id_lancamento,status) VALUES ('{$x['id_conta']}','{$data[2]}','{$data[1]}','{$id_lancamento}',1);";
                            mysql_query($query) or die($query.mysql_error());
                            echo "INSERT INTO contabil_lancamento_itens (id_conta,valor,tipo,id_lancamento,status) VALUES ('{$x['id_conta']}','{$data[2]}','{$data[1]}','{$id_lancamento}',1)</br>";
                        }
                        $row++;
                    }
                    fclose($handle);
                }
            }
        }
        ?>
    </body>
</html>