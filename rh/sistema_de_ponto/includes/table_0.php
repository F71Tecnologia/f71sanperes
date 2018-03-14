<?php if (count($lista) > 0) { ?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="tab0">
        <thead>
            <tr style="background: #ddd">
                <th colspan="9">RELAÇÃO DE PEDIDOS</th>
            </tr>
            <tr style="background: #ddd">
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">ID</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Nome</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">CNPJ</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Competência</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Periodo</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Total de participantes</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Status</th>
                <th style="border: 1px solid #C5C5C5; font-size: 11px; text-transform: uppercase; color: #666">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach($lista as $dados){ 
                $mes_competencia  = substr($dados['competencia'], 0, -4);
                $ano_competencia  = substr($dados['competencia'], 1);
            ?>
            <tr>
                <td class="center"><?php echo $dados['id_projeto']; ?></td>
                <td class="center"><?php echo $dados['nome']; ?></td>
                <td class="center"><?php echo $dados['cnpj']; ?></td>
                <td class="center"><?php echo $mes_competencia ."/". $ano_competencia ; ?></td>
                <td class="center"><?php echo $dados['periodo']; ?></td>
                <td class="center"><?php echo $dados['total_registros']; ?></td>
                <td class="center" style='position: relative'><?php echo ($dados['status'] == "2") ? "Aguardando Finalização" : "Finalizado"; ?></td>
                <td class="center">
                    <?php if($dados['status'] == "2"){ ?>
                        <a href="javascript:;" title="Visualizar Relação" class="listarPonto" data-key="<?php echo $dados['id']; ?>" ><img src="../../imagens/file.gif" width="16" height="16" border="0" alt="Visualizar Relação" title="Visualizar Relação" ></a>
                        <a href="javascript:;" title="Remover relação" class="removerPontoNaoFinalizados" data-key="<?php echo $dados['id']; ?>" ><img src="imagens/icone_x.gif" width="14" height="14" border="0" alt="Remover Relação" title="Remover Relação" ></a>
                    <?php }else{ ?>
                        <a href="javascript:;" title="Desprocessar" class="desprocessarPonto" data-key="<?php echo $dados['id']; ?>" ><img src="imagens/icone_x.gif" width="16" height="16" border="0" alt="Desprocessar" title="Desprocessar" ></a>        
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de pontos realizados.</p>
    </div>
<?php } ?>
<div id="lista_pontos"></div>