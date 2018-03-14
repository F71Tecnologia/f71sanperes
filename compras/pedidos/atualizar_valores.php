<?php
error_reporting(E_ALL);
header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/pedidosClass.php');
include('../../wfunction.php');
include('../../classes/PHPExcel/PHPExcel.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>
    </head>
    <body>
        <h1>UPLOAD para atualização dos valores dos produtos (para medicamentos)</h1>
        <form method="post" action="#" enctype="multipart/form-data">
            <input type="text" name="id_regiao" placeholder="id_regiao" value="45"><br>
            <input type="text" name="cnpj" placeholder="cnpj" value="00085822000112"><br>
            <input type="file" name="arquivo"><br>
            <button type="submit" name="gerar_querys" value="1">gerar querys</button>
            <?php
            if (isset($_FILES) && isset($_REQUEST['gerar_querys'])) {
                $id_regiao = addslashes($_REQUEST['id_regiao']);
                $cnpj = addslashes($_REQUEST['cnpj']);

                $query = "SELECT * FROM prestadorservico WHERE REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'-',''),'/','') = '$cnpj' AND id_regiao = '$id_regiao';";
                $result = mysql_query($query) or die(sql_error());
                while ($row = mysql_fetch_assoc($result)) {
                    $prestadorservico[] = $row['id_prestador'];
                }

                $uploadfile = substr(basename($_FILES['arquivo']['name']), -3); // nome do arquivo excel salvo
                if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadfile)) {
                    $file_csv = $uploadfile; // converte excel em csv (para poder trabalhar)

                    $row = 1;
                    if (($handle = fopen($file_csv, "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            if ($row > 5) {
                                if (!empty($data[4])) {
                                    $data[1] = (trim(preg_replace('/\s+/', ' ', $data[1])));

                                    $query = "SELECT id_prod FROM nfe_produtos WHERE xProd LIKE '%{$data[1]}%' AND emit_cnpj = '$cnpj';";
                                    $result = mysql_query($query) or die(mysql_error());
                                    $prod = mysql_fetch_assoc($result);

                                    $arr_lista[] = array('id_prod' => $prod['id_prod'], 'valor' => $data[4]);
                                }
                            }
                            $row++;
                        }
                    }
                    echo '<pre style="border: 1px solid #000; padding: 10px; margin: 10px">';

                    $fp = fopen("importacao.sql", "w");
                    fwrite($fp, "# compliado em: " . date('Y-m-d H:i:s') . "\r\n\r\n");
                    foreach ($prestadorservico as $prest) {
                        foreach ($arr_lista as $prod) {
                            $query = "UPDATE produto_fornecedor_assoc SET valor_produto = " . str_replace(',', '', $prod['valor']) . " WHERE id_produto = {$prod['id_prod']} AND id_fornecedor = {$prest};";
                            echo $query . '<br>';
                            fwrite($fp, $query . "\r\n");
                        }
                    }
                    fclose($fp);
                    echo '</pre>';


// exclui arquivos (sao temporarios)
                }
                //print_array($arr_lista);
                ?>
                <div style="border: 1px solid #000; padding: 10px; margin: 10px">
                    Deseja atualizar os valores com os dados acima?<br>
                    <button type="submit" name="executar_querys" value="1">Atualizar informações com os dados acima</button>
                </div>
                <?php
            }

            if (isset($_REQUEST['executar_querys'])) {

                if (file_exists("importacao.sql")) {

                    // fazer backup
                    $fp = fopen("backup_produto_fornecedor_assoc" . date('Y-m-d_H-i-s') . ".sql", "w");
                    fwrite($fp, "# backup compliado em: " . date('Y-m-d H:i:s') . "\r\n\r\n");

                    // pega o nome das culunas
                    $query_columns = "SHOW COLUMNS FROM produto_fornecedor_assoc;";
                    $result_columns = mysql_query($query_columns);
                    $arr_columns = [];
                    while ($row1 = mysql_fetch_assoc($result_columns)) {
                        $arr_columns[] = $row1['Field'];
                    }
                    $str_columns = implode(',', $arr_columns);

                    $query_back = "SELECT * FROM produto_fornecedor_assoc";
                    $result_back = mysql_query($query_back);

                    while ($row2 = mysql_fetch_assoc($result_back)) {
                        $dados = implode("','", $row2);
                        fwrite($fp, "INSERT INTO produto_fornecedor_assoc ($str_columns) VALUES ('$dados');\r\n");
                    }
                    fclose($fp);

                    echo '<pre style="border: 1px solid #000; padding: 10px; margin: 10px">';
                    // executar arquivo
                    $ponteiro = fopen("importacao.sql", "r");
                    while (!feof($ponteiro)) {
                        $linha = fgets($ponteiro, 4096);
                        if (!empty($linha) && substr($linha, 0, 1) != '#') {
                            if(mysql_query($linha)){
                                echo $linha.'<br>SUCESSO!!<br>';
                            }else{
                                echo $linha . "<br>" . mysql_error().'<br><br>';
                            }
                            
                        }
                    }
                    echo '</pre>';
                    fclose($ponteiro);
                } else {
                    echo 'arquivo nao existe';
                }
            }
            ?>
        </form>
    </body>
</html>



