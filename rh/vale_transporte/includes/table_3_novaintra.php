<?php 
if(empty($arr_cls)){ ?>
    <div class="alert alert-warning">Nenhum resultado para esta consulta</div>
<?php }else{ ?>

<table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th colspan="9">Projeto <?= $projetos[$post_projeto] . ' (CNPJ DO ARQUIVO ' . $post_cnpj . ') '; ?></th>
        </tr>
        <tr class="bg-primary valign-middle">
            <th>Editar</th>
            <th>Id</th>
            <th>Matricula</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Data Entrada</th>
            <th>Solicitou Vale Transporte</th>
            <th>Tipo Cart�o</th>
            <th>N�mero Cart�o</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cont = 1;
        foreach ($arr_cls as $clt) {
            $resumo_tipo_cartoes[$clt['tipo_cartao']][] = $clt['id_clt'];
            $class = (($cont % 2) == 0) ? 'even' : 'odd';
            ?>
            <tr class="<?= $class; ?>" id="tr_<?= $clt['id_clt']; ?>" >
                <td class="center"><input type="checkbox" onclick="editao_vt_clt(<?= $clt['id_clt']; ?>)" data-value="<?= $clt['id_clt']; ?>"></td>
                <td class="center"><?= $clt['id_clt']; ?></td>
                <td class="center" data-value="<?= $clt['matricula']; ?>"><?= $clt['matricula']; ?></td>
                <td><?= $clt['nome']; ?></td>
                <td><?= $clt['cpf']; ?></td>
                <td><?= $clt['data_entrada_f']; ?></td>
                <td class="center" data-value="<?= ($clt['transporte'] == '1') ? 'SIM' : 'NAO'; ?>"><?= ($clt['transporte'] == '1') ? 'SIM' : 'NAO'; ?></td>
                <td class="center" data-value="<?= $tipos_cartao[$clt['tipo_cartao']]; ?>"><?= $tipos_cartao[$clt['tipo_cartao']]; ?></td>
                <td class="center" data-value="<?= $clt['cartao1']; ?>" ><?= $clt['cartao1'] ?></td>                                     
            </tr>
            <?php $cont++;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td style="text-align: right" colspan="9">
                <br>
                <h5><?= count($arr_cls); ?> registros encontrados</h5>
                <h5>Resumo de cart�es</h5>
                <?php
                    $tipos_cartao_key = array_keys($resumo_tipo_cartoes);
                    foreach($tipos_cartao_key as $k =>$v){
                        $tipo = ($v!='-1') ? $tipos_cartao[$v] : 'N�o especificado';
                        echo '<p>'.count($resumo_tipo_cartoes[$v]).' encontrado(s). Tipo - '.$tipo .'</p>';
                    }
                ?>
                <br>
                <input type="hidden" name="cnpj" value="" />
                <input type="hidden" name="projeto_post" value="" />
                <div id="box_btns">
                    <!--<input type="button" onclick="" value="Criar Arquivo de Exporta��o" />-->
                </div>
                <div id="box_btns_edicao" style="display: none">
                    <input type="button" value="Cancelar" data-flag="0" onclick="clear_edicao()" />
                    <input type="button" value="Salvar Edi��o" data-flag="0" id="contador_flag" onclick="if(confirm('Clique em ok para concluir a opera��o')){salvar_edicao_funcionarios()}else{return false}" />
                </div>
            </td>
        </tr>
    </tfoot>
</table>
<div id="bts_controller_3" data-flag="0" style="display:none; text-align: right;">
    <input type="button" value="Cancelar" id="bt_limpar_edicao_clts"  onclick="limpar_edicao_clts()" />
    <input type="button" value="Salvar" id="bt_salvar_edicao_clt" onclick="salvar_edicao_clts()" />
</div>

<?php } ?>