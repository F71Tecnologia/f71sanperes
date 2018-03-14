<!--<a href="javascript:;" onclick="get_table_0();" title="Voltar"><img src="../../imagens/seta_esquerda.jpg" alt="Voltar"><span style="position: absolute; line-height: 44px;margin-left: 8px;">Voltar</span></a>-->
<br><br>
<?php
print_r($info);
?>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="tab0">
    <thead>
        <tr>
            <th colspan="2">PED. <?= $info['id_va_pedido'] . ' - ' . $info['projeto'][$info['id_projeto']];   ; ?> - Competência:<?= $info['mes'] . '/' . $info['ano']; ?></th>
        </tr>
        <tr>
            <th colspan="2">Foram gerado(s) <?= count($arquivos); ?> arquivo(s) para download(s)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($arquivos as $arquivo) { ?>
            <tr>
                <td>
                    <a href="?tipo=<?= $arquivo['tipo']; ?>&download=<?= $arquivo['download']; ?>&name_file=<?= $arquivo['name_file']; ?>" title="Baixar">BAIXAR ARQUIVO <?= ($k + 1); ?> : <?= $arquivo['name_file']; ?></a>
                    <a href="?tipo=<?= $arquivo['tipo']; ?>&download=<?= $arquivo['download']; ?>'&name_file=<?= $arquivo['name_file']; ?>" title="Baixar"> <img src="/intranet/imagens/icones/icon-download.png" alt="Baixar"></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>