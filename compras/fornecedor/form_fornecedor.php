<form action="index.php" method="post" class="form-horizontal" id="form-fornecedor">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= (isset($cad_fornecedor)) ? "Editar" : "Cadastro de novo" ?> Fornecedor
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="razao" class="col-sm-2 control-label">Razão Social</label>
                <div class="col-sm-10">
                    <input type="text" name="razao" class="form-control validate[required]" id="razao" placeholder="Razão Social" value="<?= (isset($cad_fornecedor['razao'])) ? $cad_fornecedor['razao'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="fantasia" class="col-sm-2 control-label">Nome Fantasia</label>
                <div class="col-sm-10">
                    <input type="text" name="fantasia" class="form-control validate[required]" id="fantasia" placeholder="Nome Fantasia" value="<?= (isset($cad_fornecedor['fantasia'])) ? $cad_fornecedor['fantasia'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="cnpj" class="col-sm-2 control-label">CNPJ</label>
                <div class="col-sm-3">
                    <input type="text" name="cnpj" class="form-control validate[required]" id="cnpj" placeholder="99.999.999/9999-99" value="<?= (isset($cad_fornecedor['cnpj'])) ? $cad_fornecedor['cnpj'] : '' ?>">
                </div>
                <label for="cnae" class="col-sm-1 control-label">CNAE</label>
                <div class="col-sm-6">
                    <?= montaSelect($listaCNAE, ((isset($cad_fornecedor['cnae'])) ? $cad_fornecedor['cnae'] : NULL), 'name="cnae" id="cnae" class="form-control"'); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="ie" class="col-sm-2 control-label">Inscrição Estadual</label>
                <div class="col-sm-3">
                    <input type="text" name="ie" class="form-control" id="ie" placeholder="" value="<?= (isset($cad_fornecedor['ie'])) ? $cad_fornecedor['ie'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="im" class="col-sm-2 control-label">Inscrição Municipal</label>
                <div class="col-sm-3">
                    <input type="text" name="im" class="form-control" id="im" placeholder="" value="<?= (isset($cad_fornecedor['im'])) ? $cad_fornecedor['im'] : '' ?>">
                </div>
            </div>
            <hr>
            <div class="form-group has-feedback">
                <label for="cep" class="col-sm-2 control-label">CEP</label>
                <div class="col-sm-2">
                    <input type="text" name="cep" class="form-control validate[required]" id="cep" placeholder="99999-999" value="<?= (isset($cad_fornecedor['cep'])) ? $cad_fornecedor['cep'] : '' ?>">
                </div>

            </div>
            <div class="form-group">
                <label for="uf" class="col-sm-2 control-label">Estado</label>
                <div class="col-sm-3">
                    <?= selectUF(((isset($cad_fornecedor['uf'])) ? $cad_fornecedor['uf'] : ''), 'name="uf" class="form-control validate[required,custom[select]]" id="uf"'); ?>
                </div>
                <label for="mun" class="col-sm-1 control-label">Município</label>
                <div class="col-sm-4">
                    <input type="text" name="mun" class="form-control" id="mun" placeholder="Município" value="<?= (isset($cad_fornecedor['mun'])) ? $cad_fornecedor['mun'] : '' ?>">
                </div>
                <div class="col-sm-2">
                    <input type="text" name="cod_ibge" class="form-control" id="cod_ibge" placeholder="Cód. Municipal IBGE" value="<?= (isset($cad_fornecedor['cod_ibge'])) ? $cad_fornecedor['cod_ibge'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="bairro" class="col-sm-2 control-label">Bairro</label>
                <div class="col-sm-4">
                    <input type="text" name="bairro" class="form-control" id="bairro" placeholder="Bairro" value="<?= (isset($cad_fornecedor['bairro'])) ? $cad_fornecedor['bairro'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="endereco" class="col-sm-2 control-label">Endereço</label>
                <div class="col-sm-10">
                    <input type="text" name="endereco" class="form-control validate[required]" id="endereco" placeholder="Rua, Avenida..." value="<?= (isset($cad_fornecedor['endereco'])) ? $cad_fornecedor['endereco'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="numero" class="col-sm-2 control-label">Número</label>
                <div class="col-sm-2">
                    <input type="text" name="numero" class="form-control validate[required]" id="numero" placeholder="" value="<?= (isset($cad_fornecedor['num'])) ? $cad_fornecedor['num'] : '' ?>">
                </div>
                <label for="complemento" class="col-sm-2 control-label">Complemento</label>
                <div class="col-sm-2">
                    <input type="text" name="complemento" class="form-control" id="complemento" placeholder="" value="<?= (isset($cad_fornecedor['complemento'])) ? $cad_fornecedor['complemento'] : '' ?>">
                </div>
            </div>
            <hr>
            <div class="form-group">
                <label for="tel" class="col-sm-2 control-label">Telefone</label>
                <div class="col-sm-2">
                    <input type="text" name="tel" class="form-control tel" id="tel" placeholder="(99) 9999-9999" value="<?= (isset($cad_fornecedor['tel'])) ? $cad_fornecedor['tel'] : '' ?>">
                </div>
                <label for="tel2" class="col-sm-2 control-label">Telefone 2</label>
                <div class="col-sm-2">
                    <input type="text" name="tel2" class="form-control tel" id="tel2" placeholder="(99) 9999-9999" value="<?= (isset($cad_fornecedor['tel2'])) ? $cad_fornecedor['tel2'] : '' ?>">
                </div>
                <label for="tel3" class="col-sm-2 control-label">Telefone 3</label>
                <div class="col-sm-2">
                    <input type="text" name="tel3" class="form-control tel" id="tel3" placeholder="(99) 9999-9999" value="<?= (isset($cad_fornecedor['tel3'])) ? $cad_fornecedor['tel3'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">E-mail</label>
                <div class="col-sm-4">
                    <input type="email" name="email" class="form-control" id="email" placeholder="exemplo@email.com" value="<?= (isset($cad_fornecedor['email'])) ? $cad_fornecedor['email'] : '' ?>">
                </div>
                <!--            </div>
                            <div class="form-group">-->
                <label for="site" class="col-sm-2 control-label">Site</label>
                <div class="col-sm-4">
                    <input type="url" name="site" class="form-control" id="site" placeholder="www.empresa.com.br" value="<?= (isset($cad_fornecedor['site'])) ? $cad_fornecedor['site'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="contato" class="col-sm-2 control-label">Contato</label>
                <div class="col-sm-10">
                    <input type="contato" name="contato" class="form-control" id="contato" placeholder="nome do contato da empresa" value="<?= (isset($cad_fornecedor['contato'])) ? $cad_fornecedor['contato'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="contato" class="col-sm-2 control-label">Observação</label>
                <div class="col-sm-10">
                    <textarea name="obs" class="form-control" id="obs" rows="3"><?= (isset($cad_fornecedor['obs'])) ? $cad_fornecedor['obs'] : '' ?></textarea>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <?php if (isset($cad_fornecedor['id_fornecedor'])) { ?>
                <input type="hidden" name="id_fornecedor" id="id_fornecedor_form" value="<?= $cad_fornecedor['id_fornecedor'] ?>">
            <?php } ?>
            <a href="index.php" class="btn btn-default"><i class="fa fa-reply"></i> Voltar</a>
            <input type="reset" value="Limpar" class="btn btn-default">
            <input type="submit" value="Salvar" name="method" class="btn btn-primary">
        </div>
    </div>
</form>