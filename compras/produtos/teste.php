<?php

error_reporting(E_ALL);

header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

function str_to_float($string) {
    return str_replace('@', ',', str_replace(',', '.', str_replace('.', '@', $string)));
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ProjetoClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/NFeClass.php");
include("../../classes/ContabilFornecedorClass.php");
include("../../wfunction.php");
include("../../classes/ProdutosClass.php");
include("../../classes/EstoqueClass.php");
include("../../classes/EstoqueEntradaClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/ProdutoFornecedorAssocClass.php");




//$query ="SELECT id_prod,xProd FROM nfe_produtos WHERE xProd LIKE '%  %';";
//$result = mysql_query($query);
//while ($row = mysql_fetch_array($result)) {
//    $nome = trim(preg_replace('/\s+/',' ',$row['xProd']));
//    $query2 = "UPDATE nfe_produtos SET xProd = '$nome' WHERE id_prod = '{$row['id_prod']}'";
//    mysql_query($query2);
//    mysql_error();
//}
//
//exit();


?>

<html>
    <head>
        <title>Teste</title>
    </head>
    <body>
        <form action="#" method="post"  enctype="multipart/form-data">
            <input type="file" name="file">
            <input type="submit" name="submit" value="submit">
        </form>
        <?php
        if (isset($_REQUEST['submit'])) {
            $objProduto = new ProdutosClass();
            $objProdFornecedor = new ProdutoFornecedorAssocClass();


//            $uploaddir = '/intranet/compras/produtos/';
            $uploaddir = '';
            $uploadfile = $uploaddir . basename($_FILES['file']['name']);

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                $row = 1;
                if (($handle = fopen($uploadfile, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                        if ($row > 5) {
                            echo '<pre>';
                            print_r($data);
                            echo '</pre>';

                            $objProduto->setXProd(addslashes(preg_replace('/\s*$/', '', trim($data[0]))));
                            $objProduto->setEmitCnpj('00085822000112');
                            $objProduto->setUCom($data[2]);
                            $objProduto->setStatus(1);
                            $objProduto->setTipo(1);
                            
                            $result = $objProduto->salvar();

                            $prestadores = array(490,608,609,611,612,613,614,1108);

                            if ($result === 1) {
                                echo "Salvo {$data[1]}<br><br>";
                                foreach ($prestadores as $key => $value) { // para todos os ids
                                    $objProdFornecedor->setIdFornecedor($value);
                                    $objProdFornecedor->setIdProduto($objProduto->getIdProd());
                                    $objProdFornecedor->setValorProduto(str_to_float($data[5]));
                                    $objProdFornecedor->setStatus(1);
                                    $objProdFornecedor->salvar(); // salva
                                    $objProdFornecedor->setDefault();
                                }
                            }else{
                                echo "Erro ao Gravar!!<br><br>";
                            }
                        }
                        $row++;
                        $objProduto->setDefault();
                    }
                    fclose($handle);
                }
            } else {
                echo "Possível ataque de upload de arquivo!\n";
            }
        }
        ?>
    </body>
</html>