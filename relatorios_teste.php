<!-- 

--------------------- JQUERY e CSS noe HEAD do ver.php -------------------------

<script type="text/javascript">
    // jquery para os relatórios
    $(document).ready(function() {
        $(".link-relatorio").click(function() {
            var url = $(this).attr('href');
            var relatorio = $(this).data("id");
            alert("url: " + url + "relatorio: " + relatorio);
            $.post('methods.php', {url: url, id_relatorio: relatorio, id_usuario:<?= $usuario['id_funcionario'] ?>, method: 'logRelatorio'}, function(data) {
                if (data === true) {
                    windows.open(url);
                }
            });
        });
    });
</script>
<style>
    /* css para relatorios */
    .listRelatorios{
        list-style: none;
    }
    .listRelatorios li{
        display: inline-block;
        padding: 5px;
    }
    .tb-relatorios{
        font-size: 10px;
        width: 100%;
        border-collapse: collapse;
    }
    /* fim css para relatorios */
</style>

-->
<?php
include_once('classes/RelatorioClass.php');
$relatorios = new Relatorio();
?>
<ul class="listRelatorios">
    <?php
    $arr_grupo = $relatorios->carregaGrupos();
    foreach ($arr_grupo as $grupo) {
        $style = (strlen($grupo['nome']) > 22) ? 'style="font-size:11px;"' : '';
        $style = (strlen($grupo['nome']) > 24) ? 'style="font-size:10px;"' : $style;
        $style = (strlen($grupo['nome']) > 28) ? 'style="font-size:9px;"' : $style;
        ?>
        <li><a href="#<?= $grupo['id_grupo'] ?>" class="botao" <?= $style ?>><?= $grupo['nome'] ?></a></li>
    <?php } ?>
</ul>
<?php
    foreach ($arr_grupo as $grupo) {
    ?>
    <h3><a name="<?= $grupo['id_grupo'] ?>"><?= $grupo['nome'] ?></a></h3>
    <table style="font-size:13px;" class="tb-relatorios">
        <thead style="background-color: #dddddd;">
            <tr>
                <th width="75%" style="text-align: left"><strong>NOME DO RELAT&Oacute;RIO</strong></th>
                <th width="25%" align="center"><strong>GERAR DOCUMENTO</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $arr_rel = $relatorios->carregaRelatorios($grupo['id_grupo']);
            foreach ($arr_rel as $relatorio) {
                ?>
                <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                    <td><?= $relatorios->relatorioNovo($relatorio['id_relatorio']); ?> <?= $relatorio['nome'] ?></td>
                    <td align="center"><a href="<?= $relatorio['url'] ?>" class="link-relatorio" target="_blank" data-id="<?= $relatorio['id_relatorio'] ?>"><img src="imagens/ver_relatorio.gif" alt="" /></a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

