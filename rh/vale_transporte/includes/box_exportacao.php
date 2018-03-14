<?php if(isset($box['tipo']) && $box['tipo']==1){ ?>
<a href="javascript:;" onclick="get_table_0();" title="Voltar"><img src="../../imagens/seta_esquerda.jpg" alt="Voltar"><span style="position: absolute; line-height: 44px;margin-left: 8px;">Voltar</span></a>
<br><br>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="tab0">
    <thead>
        <tr>
            <th colspan="2">PED.<?= $arr['info']['id_pedido'].' - '.$arr['info']['id_projeto'].' - '.$arr['info']['nome_projeto'].' - Competência: '.$arr['info']['mes'].'/'.$arr['info']['ano']; ?></th>
        </tr>
        <tr>
            <th colspan="2">Foram gerado(s) <?= count($arr['arquivos']); ?> arquivo(s) para download(s)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arr['arquivos'] as $arquivo){ ?>
        <tr>
            <td>
                <a href="?download=<?= $arquivo['download']; ?>&name_file=<?= $arquivo['name_file']; ?>" title="Baixar">BAIXAR ARQUIVO <?= ($k+1); ?> : <?= $arquivo['name_file'];?></a>
                <a href="?download=<?= $arquivo['download']; ?>'&name_file=<?= $arquivo['name_file']; ?>" title="Baixar"> <img src="/intranet/imagens/icones/icon-download.png" alt="Baixar"></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>