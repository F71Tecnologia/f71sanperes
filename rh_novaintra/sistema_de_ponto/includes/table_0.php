<?php if (count($lista) > 0) { ?>
    <table class="table table-condensed table-hover table-bordered table-striped text-sm" id="tab0">
        <thead>
            <tr class="bg-primary valign-middle">
                <th colspan="9">RELA��O DE PEDIDOS</th>
            </tr>
            <tr class="bg-info valign-middle">
                <th class="text-center">ID</th>
                <th class="text-center">Nome</th>
                <th class="text-center">CNPJ</th>
                <th class="text-center">Compet�ncia</th>
                <th class="text-center">Periodo</th>
                <th class="text-center">Total de participantes</th>
                <th class="text-center">Status</th>
                <th class="text-center">A��es</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach($lista as $dados){ 
                $mes_competencia  = substr($dados['competencia'], 0, -4);
                $ano_competencia  = substr($dados['competencia'], 1);
            ?>
            <tr class="valign-middle">
                <td class="text-center"><?=$dados['id_projeto'] ?></td>
                <td class="text-center"><?=$dados['nome'] ?></td>
                <td class="text-center"><?=$dados['cnpj'] ?></td>
                <td class="text-center"><?=$mes_competencia ."/". $ano_competencia ?></td>
                <td class="text-center"><?=$dados['periodo'] ?></td>
                <td class="text-center"><?=$dados['total_registros'] ?></td>
                <td class="text-center" style='position: relative'><?=($dados['status'] == "2") ? "Aguardando Finaliza��o" : "Finalizado" ?></td>
                <td class="text-center">
                    <?php if($dados['status'] == "2"){ ?>
                        <a href="javascript:;" title="Visualizar Rela��o" class="btn btn-xs btn-default listarPonto" data-key="<?=$dados['id']?>" ><i class="fa fa-search" alt="Visualizar Rela��o" title="Visualizar Rela��o" ></i></a>
                        <a href="javascript:;" title="Remover rela��o" class="btn btn-xs btn-danger removerPontoNaoFinalizados" data-key="<?=$dados['id']; ?>" ><i class="fa fa-trash-o" alt="Remover Rela��o" title="Remover Rela��o" ></i></a>
                    <?php }else{ ?>
                        <a href="javascript:;" title="Desprocessar" class="btn btn-xs btn-danger desprocessarPonto" data-key="<?=$dados['id']; ?>" ><i class="fa fa-ban" alt="Desprocessar" title="Desprocessar" ></i></a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div id="message-box" class="alert alert-warning">
        <p>N�o h� registros de pontos realizados.</p>
    </div>
<?php } ?>
<br>
<div id="lista_pontos"></div>