<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
        <script>
            function copiar()
            {
                $("#env").val("1");
                $("#frm").submit();
            }
            function renomear()
            {
                $("#env").val("2");
                $("#frm").submit();
            }
        </script>
    </head>
    <body>
        <form name="frm" id="frm" action="ren.php" method="post">
            <input type="hidden" id="env" name="env" value="1">
            <textarea name="arquivo" style="width: 600px; height: 300px"></textarea><br/>
            <input onclick="copiar();" type="button" value="Copiar"/>
            <input onclick="renomear();" type="button" value="Renomear"/>
        </form>
    </body>
</html>

<?php
if ($_REQUEST['env'] == '1' && trim($_REQUEST['arquivo'] !== ''))
{
    $arquivos = explode(chr(10), $_REQUEST['arquivo']);
    foreach ($arquivos as $arquivo)
    {
        $arq = explode('/', trim($arquivo));
        echo "cp ../../comprovantes/" . trim($arq[0]) . " ../../comprovantes/" . trim($arq[1]) . "<br/>\n";
        exec("cp ../../comprovantes/" . trim($arq[0]) . " ../../comprovantes/" . trim($arq[1]));
    }
} elseif ($_REQUEST['env'] == '2' && trim($_REQUEST['arquivo'] !== ''))
{
    $arquivos = explode(chr(10), $_REQUEST['arquivo']);
    foreach ($arquivos as $arquivo)
    {
        $arq = explode('/', trim($arquivo));
        echo "mv ../../comprovantes/" . trim($arq[0]) . " ../../comprovantes/" . trim($arq[1]) . "<br/>\n";
        exec("mv ../../comprovantes/" . trim($arq[0]) . " ../../comprovantes/" . trim($arq[1]));
    }
}


