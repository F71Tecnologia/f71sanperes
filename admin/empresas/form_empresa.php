<form action="index.php?method=salvar" method="post" class="form-horizontal" id="form_empresa">
    <?php if ($painel) { ?>
        <div class="panel panel-default">
            <div class="panel-body">
            <?php } ?>
            <div class="form-group">
                <label class="control-label col-sm-2 text-sm no-padding-l">Especialidade (CNAE)</label>
                <div class="col-sm-9">
                    <select class="form-control input-sm validate[required]" name="cnae" id="cnae">
                        <option value="-1">-- Selecione --</option>
                        <optgroup label="Mais Usados">
                            <?php foreach ($cnae[1] as $key1 => $value1) { ?>
                                <option value="<?= $key1 ?>" <?= (isset($empresa['cnae']) && $empresa['cnae'] == $key1)?'selected':'' ?>><?= $value1 ?></option>
                            <?php } ?>
                        </optgroup>
                        <optgroup label="Outros">
                            <?php foreach ($cnae[0] as $key2 => $value2) { ?>
                                <option value="<?= $key2 ?>" <?= (isset($empresa['cnae']) && $empresa['cnae'] == $key2)?'selected':'' ?>><?= $value2 ?></option>
                            <?php } ?>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-sm-offset-2">
                    <div class="radio">
                        <label>
                            <input type="radio" name="matriz_filial" id="matriz" value="1" <?= (isset($empresa['id_matriz']) && $empresa['id_matriz'] == 0)?'checked':'' ?>>
                            Matriz
                        </label>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="radio">
                        <label>
                            <input type="radio" name="matriz_filial" id="filial" value="0" <?= (isset($empresa['id_matriz']) && $empresa['id_matriz'] != 0)?'checked':'' ?>>
                            Filial
                        </label>
                    </div>
                </div>
                <label class="control-label col-sm-1 text-sm <?= ($empresa['id_matriz'] == 0)?'hidden':'' ?>" id="label_matriz">Matriz</label>
                <div class="col-sm-4">
                    <?= montaSelect($arrayEmpresas, $empresa['id_matriz'], "id='id_matriz' name='id_matriz' class='form-control text-center input-sm validate[required] hidden'"); ?></div>

            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 text-sm">Nome Fantasia</label>
                <div class="col-sm-4"><input name="fantasia" id="fantasia" value="<?= $empresa['fantasia'] ?>" class="form-control input-sm validate[required]"></div>
                <label class="control-label col-sm-1 text-sm no-padding-l">Raz&atilde;o Social</label>
                <div class="col-sm-4"><input name="razao" id="razao" value="<?= $empresa['razao'] ?>" class="form-control input-sm validate[required]"></div>
            </div>
            <div class="form-group  has-feedback">
                <label class="control-label col-sm-2 text-sm">CEP</label>
                <div class="col-sm-2"><input name="cep" id="cep" value="<?= $empresa['cep'] ?>" class="form-control input-sm validate[required] cep"></div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 text-sm">Endere&ccedil;o</label>
                <div class="col-sm-4"><input name="endereco" id="endereco" value="<?= $empresa['endereco'] ?>" readonly class="form-control input-sm validate[required]"></div>
                <label class="control-label col-sm-1 text-sm">Nº</label>
                <div class="col-sm-1"><input name="numero" id="numero" value="<?= $empresa['num'] ?>" class="form-control text-center input-sm validate[required]"></div>
                <label class="control-label col-sm-1 text-sm">Complem.</label>
                <div class="col-sm-2"><input name="complemento" id="c_complemento" value="<?= $empresa['complemento'] ?>" class="form-control input-sm"></div>

            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 no-padding-l text-sm">Bairro</label>
                <div class="col-sm-2"><input name="bairro" id="c_bairro" value="<?= $empresa['bairro'] ?>" readonly class="form-control input-sm validate[required]"></div>
                <label class="control-label col-sm-1 text-sm col-sm-offset-2">Cidade</label>
                <?php
                if (!empty($empresa['fantasia'])) {
                    $objMunicipio->setIdMunicipio($empresa['id_municipio']);
                    $objMunicipio->getAllMunicipios();
                    $objMunicipio->getRowMunicipio();
                }
                ?>
                <div class="col-sm-2">
                    <input name="cidade" id="cidade" value="<?= $objMunicipio->getMunicipio() ?>" readonly class="form-control input-sm validate[required]">
                    <input type="hidden" name="cod_cidade" id="cod_cidade" value="<?= $objMunicipio->getIdMunicipio() ?>">
                </div>
                <label class="control-label col-sm-1 text-sm">UF</label>
                <div class="col-sm-1">
                    <?php $objMunicipio->setSigla(''); ?>
                    <?php $objMunicipio->getAllUf(); ?>
                    <select name="uf" id="uf" readonly class="form-control text-center input-sm validate[required]">
                        <option value=""></option>
                        <?php
                        while ($objMunicipio->getRowMunicipio()) {
                            $selected = ($empresa['uf'] == $objMunicipio->getSigla()) ? " selected " : "";
                            echo '<option value="' . $objMunicipio->getSigla() . '" ' . $selected . '>' . $objMunicipio->getSigla() . '</option>';
                        }
                        ?>  
                    </select>
                </div>

            </div>

            <div class="form-group">
                <label class="control-label col-sm-2 text-sm no-padding-l">CNPJ</label>
                <div class="col-sm-4"><input name="cnpj" id="c_cnpj" value="<?= $empresa['cnpj'] ?>" class="form-control input-sm validate[required] cnpj"></div>
                <label class="control-label col-sm-1 text-sm no-padding-l">Telefone</label>
                <div class="col-sm-4"><input name="tel" id="c_tel" value="<?= $empresa['tel'] ?>" class="telefone form-control input-sm validate[required]"></div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 text-sm no-padding-l">IE</label>
                <div class="col-sm-4"><input name="ie" id="ie" value="<?= $empresa['ie'] ?>" class="form-control input-sm "></div>
                <label class="control-label col-sm-1 text-sm no-padding-l">IM</label>
                <div class="col-sm-4"><input name="im" id="im" value="<?= $empresa['im'] ?>" class="form-control input-sm "></div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 text-sm no-padding-l">Tel / Fax</label>
                <div class="col-sm-4"><input name="tel2" id="tel2" value="<?= $empresa['tel2'] ?>" class="telefone form-control input-sm "></div>
                <label class="control-label col-sm-1 text-sm no-padding-l">E-mail</label>
                <div class="col-sm-4"><input name="email" id="email" value="<?= $empresa['email'] ?>" class="form-control input-sm "></div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2 text-sm no-padding-l">Site</label>
                <div class="col-sm-9"><input name="site" id="site" value="<?= $empresa['site'] ?>" class="form-control input-sm "></div>
            </div>
            <h3><small>Sócios</small></h3><hr>

            <div class="form-group">
                <div class="col-sm-12"><button type="button" class="btn btn-xs btn-success adicionar_socio"><i class="fa fa-plus-circle"></i> Adicionar Sócio</button></div>
            </div>
            <table class="table table-bordered table-condensed text-sm valign-middle">
                <thead>
                    <tr class="tr-bg-info">
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>CPF</th>
                        <th>A&ccedil;&otilde;es</th>
                    </tr>
                </thead>
                <tbody class="body_socio">
                    <?php
                    $objSocio->setIdContabilEmpresa($empresa['id_empresa']);
                    $objSocio->getSocios();
                    if ($objSocio->getNumRowSocio() > 0 && !empty($objSocio->getIdContabilEmpresa())) {
                        while ($objSocio->getRowSocio()) {
                            ?>
                            <tr id="tr_socio">
                                <td><input type="hidden" name="socio[id_socio][]" class="form-control" value="<?= $objSocio->getIdSocio() ?>"><input type="text" name="socio[nome][]" id="nome_socio" class="form-control" value="<?= $objSocio->getNome() ?>"></td>
                                <td><input type="text" name="socio[tel][]" id="tel_socio" class="telefone form-control" value="<?= $objSocio->getTel() ?>"></td>
                                <td><input type="text" name="socio[cpf][]" id="cpf_socio" class="form-control cpf" value="<?= $objSocio->getcpf() ?>"></td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-socio btn btn-danger" title="Excluir" data-id="<?= $objSocio->getIdSocio() ?>"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else { ?>
                        <tr id="tr_socio">
                            <td><input type="hidden" name="socio[id_socio][]" value="" class="form-control"><input type="text" name="socio[nome][]" id="nome_socio" class="form-control"></td>
                            <td><input type="text" name="socio[tel][]" id="tel_socio" class="telefone form-control"></td>
                            <td><input type="text" name="socio[cpf][]" id="cpf_socio" class="form-control cpf"></td>
                            <td class="text-center">
                                <button type="button" class="btn-remove-socio btn btn-danger" title="Excluir"><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h3><small>Dependentes</small></h3><hr>

            <div class="form-group">
                <div class="col-sm-12"><button type="button" class="btn btn-xs btn-success adicionar_dependente"><i class="fa fa-plus-circle"></i> Adicionar Dependente</button></div>
            </div>
            <table class="table table-bordered table-condensed text-sm valign-middle">
                <thead>
                    <tr class="tr-bg-info">
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Grau Parentesco</th>
                        <th>A&ccedil;&otilde;es</th>
                    </tr>
                </thead>
                <tbody class="body_dependente">
                    <?php
                    $objDependente->setIdContabilEmpresa($empresa['id_empresa']);
                    $objDependente->getPrestadorDependentes();
                    if ($objDependente->getNumRowPrestadorDependente() > 0 && !empty($objDependente->getIdContabilEmpresa())) {
                        while ($objDependente->getRowPrestadorDependente()) {
                            ?>
                            <tr id="tr_dependente">
                                <td><input type="hidden" name="dependente[id_dependente][]" class="form-control" value="<?= $objDependente->getIdDependente() ?>"><input type="text" name="dependente[nome][]" id="nome_dependente" class="form-control" value="<?= $objDependente->getNome() ?>"></td>
                                <td><input type="text" name="dependente[tel][]" id="tel_dependente" class="telefone form-control" value="<?= $objDependente->getTel() ?>"></td>
                                <td><?= montaSelect($optParentesco, $objDependente->getParentesco(), "id='parentesco_dependente' name='dependente[parentesco][]' class='form-control'") ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn-remove-dependente btn btn-danger" title="Excluir" data-id="<?= $objDependente->getIdDependente() ?>"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr id="tr_dependente">
                            <td><input type="hidden" name="dependente[id_dependente][]" class="form-control" value=""><input type="text" name="dependente[nome][]" id="nome_dependente" class="form-control"></td>
                            <td><input type="text" name="dependente[tel][]" id="tel_dependente" class="telefone form-control"></td>
                            <td><?= montaSelect($optParentesco, null, "id='parentesco_dependente' name='dependente[parentesco][]' class='form-control'") ?></td>
                            <td class="text-center">
                                <button type="button" class="btn-remove-dependente btn btn-danger" title="Excluir" ><i class="fa fa-times"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <input type="hidden" name="id_empresa" value="<?= $empresa['id_empresa'] ?>">
        <input type="hidden" value="salvar" name="salvar">
         <?php if ($painel) { ?>
        <div class="panel-footer text-right">
            <button type="submit" value="salvar" name="salvar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
        </div>
    </div>
         <?php } ?>
</form>
