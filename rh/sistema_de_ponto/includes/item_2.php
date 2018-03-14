<?php 
    if (count($lista_proj_finalizado) > 0) { ?>
    <!--<p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tab0.1', 'Relatorio')" value="Exportar para Excel" class="exportarExcel"></p>-->
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="tab0.1">
        <thead>
            <tr style="background: #ddd; border: 1px solid #999; font-size: 13px; text-transform: uppercase; color: #666">
                <th colspan="9">Movimentos lançados pelo ponto</th>
            </tr>
            <tr style="background: #ddd;">
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">ID</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">PROJETO</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">CNPJ</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">COMPETÊNCIA</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">TOTAL DE PARTICIPANTES</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lista_proj_finalizado as $projetos_finalizado){ ?>
           <tr>
                <td class="center"><?= $projetos_finalizado['id_projeto']; ?></td>
                <td><?= $projetos_finalizado['nome_projeto']; ?></td>
                <td class="center"><?= $projetos_finalizado['cnpj_projeto']; ?></td>
                <td class="center"><?= $projetos_finalizado['mes']  ."/". $projetos_finalizado['ano'] ; ?></td>
                <td class="center"><?= $projetos_finalizado['total_participante']; ?></td>
                <td class="center">
                    <?php if($dados['status'] == "3"){ ?><a href="javascript:;" title="Visualizar Relação" class="listarMovimentos" data-key="<?php echo $projetos_finalizado['id_header']; ?>" data-projeto="<?php echo $projetos_finalizado['id_projeto']; ?>"><img src="../../imagens/file.gif" width="16" height="16" border="0" alt="Visualizar Relação" title="Visualizar Relação" ></a><?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>       
    </table>
    
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há movimentos lançados pelo sistema de ponto.</p>
    </div>
<?php
} ?>
<div  id="resultado">
    
</div>