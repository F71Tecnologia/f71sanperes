<?php if(isset($relacao_clt_movimento)){ ?>
<div class="row margin_b10">
    <div class="col-xs-6 text-left"><a href="javascript:;" onclick="voltar_lista_pedido()" class="btn btn-default"><i class="fa fa-reply"></i> Voltar</a></div>
    <div class="col-xs-6 text-right"><button type="button" onclick="tableToExcel('tab0.1', 'Relatorio')" value="Exportar para Excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button></div>
</div>
<?php } ?>
<?php if (count($relacao_funcionarios) > 0) { ?>
    <table class="table table-condensed table-hover table-bordered table-striped text-sm" id="tab0.1">
        <thead>
            <tr class="bg-primary valign-middle">
                <th colspan="9">Relação de Funcionários</th>
            </tr>
            <tr class="bg-info valign-middle">
                <th>Código</th>
                <th>Nome</th>
                <th>Dias Úteis</th>
                <th>Valor Diário</th>
                <th>Recarga</th>
                <?php if(isset($relacao_clt_movimento)){ ?>
<!--                <th>Salário</th>
                <th>Salário Porcentagem</th>
                <th>Desconto Movimento</th>-->
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($relacao_funcionarios as $funcionario){ ?>
            <tr class="valign-middle">
                <td class="center"><?= $funcionario['id_clt']; ?></td>
                <td><?= $funcionario['nome_funcionario']; ?></td>
                <td class="center"><?= $funcionario['dias_uteis']; ?></td>
                <td class="center">R$ <?= $funcionario['valor_diario']; ?></td>
                <td class="center">R$ <?= $funcionario['valor_recarga']; ?></td>
                <?php if(isset($relacao_clt_movimento)){ ?>
<!--                <td class="center">R$ <?php //= number_format($funcionario['salario'],2,',','.'); ?></td>
                <td class="center">R$ <?php //= number_format($funcionario['salario_porcentagem'],2,',','.'); ?></td>
                <td class="center">R$ <?php //= number_format($funcionario['desconto_movimento'],2,',','.'); ?></td>-->
                <?php } ?>                
            </tr>
            <?php } ?>
        </tbody>       
        <input type="hidden" name="projeto" id="projeto_pedido" value="<?= $projeto; ?>" />
        <input type="hidden" name="ano" id="ano_pedido" value="<?= $ano; ?>" />
        <input type="hidden" name="mes" id="mes_pedido" value="<?= $mes; ?>" />
        <input type="hidden" name="data_inicial" id="data_inicial_pedido" value="<?= $data_inicial; ?>" />
        <input type="hidden" name="data_final" id="data_final_pedido" value="<?= $data_final; ?>" />
        <input type="hidden" name="dias_uteis" id="dias_uteis_pedido" value="<?= $dias_uteis; ?>" />         
    </table>
    <?php if(isset($relacao_clt_movimento)){ ?>
    <br><br>
    <div class="txright">
        <!--<input type="button" value="Lançar Movimentos"  onclick="lancar_movimentos(<?//= $funcionario['id_clt']; ?>)" />-->
        <input type="button" value="Criar Arquivo Exportação AELO" class="btn btn-success" onclick="arquivo_aelo(<?= $id_pedido; ?>)" id="bt_arquivo_aelo" />
    </div>
    <?php }else{ ?>
    <p class="txright"><?= 'Valor total do pedido: R$ '.number_format($valor_total_pedido,2,',','.'); ?></p>
    <p class="txright"><?= (count($relacao_funcionarios) - $registros_descartados). ' selecionados de '.count($relacao_funcionarios).' registros.'; ?></p>
    <div class="txright">
        <input type="button" class="btn btn-primary" value="Fechar Pedido"  onclick="fechar_pedido()" />
    </div>
    <?php } ?>
<?php } else { ?>
    <div id="message-box" class="alert alert-warning">
        <p>Não há registros de pedidos realizados.</p>
    </div>
<?php } ?>
<div  id="table_fake"></div>