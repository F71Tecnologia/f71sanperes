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
                    .rel-novo{
                        display: inline-block;
                        background-color: #0099ff;
                        color: white;
                        padding: 2px 3px;
                        margin: 2px;
                        border: 1px solid #0088ee;
                        border-radius: 3px;
                        -webkit-border-radius: 3px;
                        -moz-border-radius: 3px;
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
if($_COOKIE['logado'] == 204 OR $_COOKIE['logado'] == 5 OR $_COOKIE['logado'] == 82 OR $_COOKIE['logado'] == 178 OR $_COOKIE['logado'] == 209){
    echo '<h3><a name="12">Relatório Personalizado</a></h3>
    <table style="font-size:13px;" class="tb-relatorios">
        <thead style="background-color: #dddddd;">
            <tr>
                <th width="75%" style="text-align: left"><strong>NOME DO RELATÓRIO</strong></th>
                <th width="25%" align="center"><strong>GERAR DOCUMENTO</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr class="linha_um">
                <td style="padding:5px;"> Relatório Personalizado</td>
                <td align="center"><a href="/intranet/relatorios/relatorio_pers.php" class="link-relatorio" target="_blank" ><img src="imagens/ver_relatorio.gif" alt=""></a></td>
            </tr>
        </tbody>
    </table>
    <p class="right"><a href="#corpo">Subir ao topo</a></p>';
}
foreach ($arr_grupo as $grupo) {
    $arr_rel = $relatorios->carregaRelatorios($grupo['id_grupo']);
    if ($arr_rel != null) {
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
                foreach ($arr_rel as $relatorio) {
                    ?>
                    <tr class="linha_<?= ($classe++ % 2 == 0) ? "um" : "dois"; ?>">
                        <td style="padding:5px;"><?= $relatorios->relatorioNovo($relatorio['id_relatorio']); ?> <?= $relatorio['nome'] ?></td>
                        <td align="center"><a href="<?= $relatorio['url'] ?>" class="link-relatorio" target="_blank" data-id="<?= $relatorio['id_relatorio'] ?>"><img src="imagens/ver_relatorio.gif" alt="" /></a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <p class="right"><a href="#corpo">Subir ao topo</a></p>
    <?php
    }
}
?>

