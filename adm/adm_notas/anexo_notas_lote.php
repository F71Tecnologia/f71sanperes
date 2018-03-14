<?php
if (!isset($_REQUEST['check'])) {
    echo "Nenhuma nota selecionada, impossivel continuar <a href='javascript:;' onclick='javascript:history.go(-1);' >voltar</a>";
    exit;
}
include("../../conn.php");

$notas = implode(",", $_REQUEST['check']);
$result = mysql_query("SELECT * FROM notas_files WHERE id_notas IN ({$notas})");
?>
<html>
    <head>
        <title>Administração de Notas Fiscais</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../css/estrutura.css" rel="stylesheet" type="text/css">
        <style>
            li{list-style: none outside none; font-size: 14px; padding-bottom: 5px;}
        </style>
    </head>
    <body>
        <div id="corpo">
            <div id="menu" class="nota">
                <?php include "include/menu.php"; ?>
            </div>
            <div id="conteudo" style="text-transform:uppercase;">
                <br><br>
                <h2>Anexos das notas selecionadas</h2>
                <br>
                <ul>
                    <?php while ($row_anexo = mysql_fetch_assoc($result)) { ?>
                        <li><a href="<?php echo "notas/" . $row_anexo['id_file'] . '.' . $row_anexo['tipo'] ?>" target="_blank" >cod da nota: <?php echo $row_anexo['id_notas'] ?></a></li>
                    <?php } ?>
                </ul>
                <br/>
                <br/>
                <a href='javascript:;' onclick='javascript:history.go(-1);' >voltar</a>
            </div>
            <div id="rodape">
                <?php include('../include/rodape.php'); ?>
            </div>
        </div>
    </body>
</html>