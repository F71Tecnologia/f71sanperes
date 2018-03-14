<?php 
if(empty($arr_cls)){
    $alert['message'] = 'Nenhum resultado para esta consulta';
    include 'box_message.php'; 
}else{
?>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
    <thead>
        <tr>
            <th colspan="9">Projeto <?= $projetos[$post_projeto] . ' (CNPJ DO ARQUIVO ' . $post_cnpj . ') '; ?></th>
        </tr>
        <tr>
            <th>Id</th>
            <th>Matricula</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Data Entrada</th>
            <th>Solicitou Vale Transporte</th>
            <th>Tipo Cartão</th>
            <th>Número Cartão</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($arr_cls)) {
            $cont = 1;
            foreach ($arr_cls as $clt) {
                $class = (($cont % 2) == 0) ? 'even' : 'odd';
                ?>
                <tr class="<?= $class; ?>">
                    <td class="center"><?= $clt['id_clt']; ?></td>
                    <td class="center"><?= $clt['matricula']; ?></td>
                    <td><?= $clt['nome']; ?></td>
                    <td><?= $clt['cpf']; ?></td>
                    <td><?= $clt['data_entrada_f']; ?></td>
                    <td class="center"><?= ($clt['transporte'] == '1') ? 'SIM' : 'NAO'; ?></td>
                    <td class="center"><?= $tipos_cartao[$clt['tipo_cartao']]; ?></td>
                    <td class="center"><?= $clt['cartao1'] ?></td>                                     
                </tr>
                <?php
                $cont++;
            }
        }else{
        ?>
                <tr>
                    <td colspan="7" style="padding: 30px; font-weight: bold">Nenhum registro encontrado</td>
                </tr>   
        <?php } ?>
    </tbody>
    <?php if (!empty($arr_cls)) { ?>
    <tfoot>
        <tr>
            <td style="text-align: right" colspan="10">
                <br>
                <h5><?= count($arr_cls); ?> registros encontrado<?php (count($arr_cls)>1) ? 's' : ''; ?></h5>
                <br>
                <div id="content_download_txt" style="text-align: right">
                </div>
                <input type="hidden" id="post_cnpj_2" value="<?= $post_cnpj; ?>" />
                <input type="hidden" id="post_projeto_2" value="<?= $post_projeto ?>" />
                <input type="hidden" id="post_transporte_2" value="<?= $post_transporte ?>" />
                <input type="hidden" id="post_matricula_2" value="<?= $post_matricula ?>" />
                <input type="hidden" id="post_cpf_2" value="<?= $post_cpf ?>" />
                <input type="hidden" id="post_nome_2" value="<?= $post_nome ?>" />
                <input type="hidden" id="post_tipo_registro_2" value="<?= $post_tipo_registro ?>" />
                <p id="baixar_txt_2"></p>
                <input type="button" onclick="exportar_clts()" value="Criar Arquivo de Exportação" />
            </td>
        </tr>
    </tfoot>
    <?php } ?>
</table>
<?php } ?>