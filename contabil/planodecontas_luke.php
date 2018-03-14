<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');

$opt = [
    -1 => 'Selencione',
    1 => 'contabil_planodecontas (insert) (cod_reduzido, tipo, classificador, descricao, nivel)',
    2 => 'contabil_planodecontas (update) (id_projeto)',
    3 => 'entradaesaida_subgrupo (insert) (id_subgrupo, entradaesaida_grupo, nome)',
    4 => 'entradaesaida (insert) (cod, nome, grupo,id_subgrupo)'
];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: Importaçao</title>

        <!-- Bootstrap -->
        <link href="/intranet/resources/css/bootstrap.min.css" rel="stylesheet">
        <link href="/intranet/resources/css/main.css" rel="stylesheet">
    </head>
    <body>
        <div class="container margin_t20">
            <div class="panel panel-default">
                <div class="panel-heading">Geração de INSERT</div>
                <div class="panel-body">
                    <form method="post" action="#" class="form-horizontal" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="col-sm-10">
                                <?= montaSelect($opt, null, 'name="tipo" class="form-control"'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="arquivo">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-10">
                                <input type="submit" class="btn btn-primary" name="salvar" value="Gerar">
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <?php
// Check if image file is a actual image or fake image
            if (isset($_POST["salvar"])) {
                echo '<pre>';
                switch ($_REQUEST['tipo']) {
                    case 1:
                        $row = 1;
                        $insert = 1;
                        $count = 1;
                        if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                if ($row > 1) {
                                    if ($count == 1000) {
                                        $insert++;
                                        $count = 1;
                                    } else {
                                        $count++;
                                    }
                                    $tipo = empty($data[1]) ? 'A' : $data[1];
                                    $descricao = addslashes($data[3]);
                                    $arr[$insert][] = "('{$data[0]}', '{$tipo}', '{$data[2]}', '{$descricao}', '{$data[4]}')";
                                }
                                $row++;
                            }
                            fclose($handle);
                            foreach ($arr as $value) {
                                echo "INSERT INTO contabil_planodecontas (cod_reduzido, tipo, classificador, descricao, nivel) VALUES <br>";
                                echo implode(',<br>', $value) . ';<br><br><br>';
                            }
                        }

                        break;

                    case 2:
                        $row = 1;
                        if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                if ($row > 1) {
                                    $arr = array_filter([$data[3], $data[4], $data[5]]);
                                    foreach ($arr as $value) {
                                        $arr2[] = "classificador LIKE '{$value}%'";
                                    }
                                    echo "UPDATE contabil_planodecontas SET id_projeto = {$data[6]} WHERE " . implode(' OR ', $arr2) . ";<br>";
                                    unset($arr, $arr2);
                                }
                                $row++;
                            }
                        }
                        break;
                        
                    case 3:
                        $row = 1;
                        if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                if ($row > 1) {
                                    echo "INSERT INTO entradaesaida_subgrupo (id_subgrupo, entradaesaida_grupo, nome) VALUES ('{$data[1]}','{$data[0]}','{$data[2]}');<br>";
                                    unset($arr, $arr2);
                                }
                                $row++;
                            }
                        }
                        break;
                        
                    case 4:
                        $row = 1;
                        if (($handle = fopen($_FILES["arquivo"]["tmp_name"], "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                                if ($row > 1) {
                                    echo "INSERT INTO entradaesaida (cod, nome, grupo,id_subgrupo) VALUES ('{$data[2]}','{$data[3]}','{$data[0]}','{$data[1]}');<br>";
                                    unset($arr, $arr2);
                                }
                                $row++;
                            }
                        }
                        break;

                    default:
                        break;
                }
                echo '</pre>';
            }
            ?>
        </div>
    </body>
</html>