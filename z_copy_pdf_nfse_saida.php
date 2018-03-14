<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = 'login.php?entre=true';</script>";
}

include("conn.php");
include("wfunction.php");
include("classes/BotoesClass.php");
include("classes/NFSeClass.php");
include("classes/global.php");


//if (isset($_REQUEST['copy'])) {
//    $query = "SELECT 
//                a.id_nfse,
//                a.id_projeto,
//                b.id_saida,
//                MAX(e.arquivo_pdf) AS arquivo_pdf
//        FROM nfse AS a
//        INNER JOIN nfse_saidas AS b ON a.id_nfse = b.id_nfse
//        INNER JOIN saida AS c ON b.id_saida = c.id_saida AND c.`status` >0
//        LEFT JOIN nfse_anexos AS e ON a.Numero = e.numero_nota AND a.CodigoVerificacao = e.codigo_verificador
//        WHERE a.`status` > 1
//        GROUP BY a.id_nfse
//        ORDER BY a.id_nfse;";
//    $result = mysql_query($query);
//    while ($row = mysql_fetch_assoc($result)) {
//        $arr_arquivo = explode('.', $row['arquivo_pdf']);
//        $query_file = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('{$row['id_saida']}','.{$arr_arquivo[1]}');";
//        if (mysql_query($query_file)) {
//            $id_files = mysql_insert_id();
//            $arquivo_1 = "compras/notas_fiscais/nfse_anexos/{$row['id_projeto']}/{$row['arquivo_pdf']}";
//            $arquivo_2 = "comprovantes/{$id_files}.{$row['id_saida']}.pdf";
//            $arr[] = copy($arquivo_1, $arquivo_2) ? "Nota anexo da nota {$row['id_nfse']} copiado para saída {$row['id_saida']}" : "Erro ao copiar nota {$row['id_nfse']} para saída {$_row['id_saida']}";
//        } else {
//            $arr[] = "Erro ao inserir na tabela saida_files";
//        }
//    }
//}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <title>F71 :: Script de Copia do pdf de nfse para Saída</title>
    </head>
    <body>
        <form method="post" action="#">
            <button type="submit" name="copy" value="1">Fazer cópia</button>
        </form>
        <?php foreach ($arr as $key => $value) { ?>
            <?= $key . " - " . $value ?><br>
        <?php } ?>
    </body>
</html>



