<?php if (!empty($listaEmpresas)) { ?>
    <div class="panel panel-primary">
        <div class="panel-heading">Empresas Ativas</div>
        <table class="table table-striped table-hover text-sm valign-middle">
            <thead>
                <tr class="admin">
                    <th style="width:5%" class="text-center">#</th>
                    <th style="width:15%">Nome Fantasia</th>
                    <th style="width:40%">Raz&atilde;o Social</th>
                    <th style="width:10%">CNPJ</th>
                    <th style="width:10%">&emsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaEmpresas as $key => $value) { ?>
                    <tr id="row-<?= $value['id_empresa'] ?>">
                        <td class="text-center"><?= $value['id_empresa'] ?></td>
                        <td><?= $value['fantasia'] ?></td>
                        <td><?= $value['razao'] ?></td>
                        <td><?= mascara_string($mask_cnpj, $value['cnpj']) ?></td>
                        <td class="text-center">
                            <button type="button" data-id="<?= $value['id_empresa'] ?>" class="btn btn-warning btn-xs editar" title="Editar"><i class="fa fa-edit"></i></button>
                            <button type="button" data-id="<?= $value['id_empresa'] ?>" class="btn btn-primary btn-xs visualizar" title="Detalhes"><i class="fa fa-search"></i></button>
                            <button type="button" data-id="<?= $value['id_empresa'] ?>" class="btn btn-danger btn-xs excluir" title="Excluir"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <form id="form1" method="post">
        <input name="id_empresa" type="hidden" id="id_empresa" value="">
        <input name="method" type="hidden" id="method" value="">
    </form>
<?php } else { ?>
    <div class="note note-info">
        <h4 class="note-title">Não há Empresas cadastradas</h4>
        <p class="text-justified text-info">Para cadastrar empresa clique na aba Cadastro, preencha o formulário e clique em Salvar.</p>
    </div>
<?php } ?>