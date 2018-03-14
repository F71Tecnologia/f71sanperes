<?php 
    if (count($lista_proj_finalizado) > 0) { ?>
    <!--<p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tab0.1', 'Relatorio')" value="Exportar para Excel" class="exportarExcel"></p>-->
    <table class="table table-condensed table-hover table-bordered table-striped text-sm" id="tab0.1">
        <thead>
            <tr class="bg-primary valign-middle">
                <th colspan="9">Movimentos lançados pelo ponto</th>
            </tr>
            <tr class="bg-info valign-middle">
                <th>ID</th>
                <th>PROJETO</th>
                <th>CNPJ</th>
                <th>COMPETÊNCIA</th>
                <th>TOTAL DE PARTICIPANTES</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lista_proj_finalizado as $projetos_finalizado){ ?>
           <tr class="valign-middle">
                <td class="center"><?= $projetos_finalizado['id_projeto']; ?></td>
                <td><?= $projetos_finalizado['nome_projeto']; ?></td>
                <td class="center"><?= $projetos_finalizado['cnpj_projeto']; ?></td>
                <td class="center"><?= $projetos_finalizado['mes']  ."/". $projetos_finalizado['ano'] ; ?></td>
                <td class="center"><?= $projetos_finalizado['total_participante']; ?></td>
                <td class="center">
                    <?php if($dados['status'] == "3"){ ?><a href="javascript:;" title="Visualizar Relação" class="btn btn-xs btn-primary listarMovimentos" data-key="<?php echo $projetos_finalizado['id_header']; ?>" data-projeto="<?php echo $projetos_finalizado['id_projeto']; ?>"><i class="fa fa-search" alt="Visualizar Relação" title="Visualizar Relação" ></i></a><?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>       
    </table>
    
<?php } else { ?>
    <div id="message-box" class="alert alert-warning">
        <p>Não há movimentos lançados pelo sistema de ponto.</p>
    </div>
<?php
} ?>
<div  id="resultado">
    
</div>