<?php if (isset($list_estabilidades) && !empty($list_estabilidades)) { ?>
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Hist&oacute;rico</strong></div>
        <div class="panel-body">
            <table class="table table-hover table-striped" id="tb-estabilidade">
                <thead>
                    <tr>
                        <th>#</th>
                        <!--<th>Projeto</th>-->
                        <!--<th>Funcion&aacute;rio</th>-->
                        <th>Tipo</th>
                        <th>In&iacute;cio</th>
                        <th>Fim</th>
                        <th>Observa&ccedil;&atilde;o</th>
                        <th>Usu&aacute;rio</th>
                        <th>Processamento</th>
                        <th>&emsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list_estabilidades as $value) { ?>

                        <tr id="tr-<?= $value['id_estabilidade'] ?>">
                            <td><?= $value['id_estabilidade'] ?></td>
                            <!--<td><?= ($method == 'refresh_table') ? utf8_encode($value['nome_projeto']) : $value['nome_projeto'] ?></td>-->
                            <!--<td><?= ($method == 'refresh_table') ? utf8_encode($value['nome']) : $value['nome'] ?></td>-->
                            <td><?= ($method == 'refresh_table') ? utf8_encode($value['tipo']) : $value['tipo'] ?></td>
                            <td><?= $value['data_ini_br'] ?></td>
                            <td><?= $value['data_fim_br'] ?></td>
                            <td><?= ($method == 'refresh_table') ? utf8_encode($value['obs']) : $value['obs'] ?></td>
                            <td><?= $value['nome_usuario'] ?></td>
                            <td><?= $value['data_proc_br'] ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-info btn-xs editar" data-id="<?= $value['id_estabilidade'] ?>" onclick="editar(this);"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-danger btn-xs excluir" data-id="<?= $value['id_estabilidade'] ?>" onclick="excluir(this);"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>    
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <form action="#" method="post" id="form_editar">
        <input type="hidden" name="method" id="method" value="editar">
        <input type="hidden" name="id_estabilidade" id="id_estabilidade" value="">
    </form>
<?php } ?>

