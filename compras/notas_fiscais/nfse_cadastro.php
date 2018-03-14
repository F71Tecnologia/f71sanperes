<div class="tab-pane fade in active" id="form-servico"> 
    <form action="nfse_atualiza.php" method="post" name="form-serv" id="form-serv" class="form-horizontal top-margin1" enctype="multipart/form-data" >
        <?php if (isset($nfse_arr['id_nfse'])) { ?>
            <input type="hidden" name="id_nfse" id="id_nfse" value="<?= $nfse_arr['id_nfse'] ?>">
            <input type="hidden" name="status" id="status" value="<?= $nfse_arr['status'] ?>">
        <?php }// ?>
        <input type="hidden" name="home" id="home">
        <div class="panel panel-default">
            <div class="panel-body"> 
                <div class="form-group">
                    <label for="regiao1" class="col-lg-2 control-label">Região</label>
                    <div class="col-lg-4">
                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $nfse_arr['id_regiao'], "id='regiao1' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto1'"); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="projeto1" class="col-lg-2 control-label">Projeto</label>
                    <div class="col-lg-4">
                        <?php echo $projeto1; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="situacao_prestador" class="col-lg-2 control-label" onblur="">Situação do Prestador</label>
                    <div class="col-lg-7">
                        <div class="radio">
                            <label><input type="radio" name="situacao_prestador" class="situacao_prestador" value="1" <?= ($nfse_arr['status_prestador'] == 1) ? 'checked' : '' ?>> Ativo</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="situacao_prestador" class="situacao_prestador" value="2" <?= ($nfse_arr['status_prestador'] == 2) ? 'checked' : '' ?>> Inativo</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="situacao_prestador" class="situacao_prestador" value="3" <?= ($nfse_arr['status_prestador'] == 3) ? 'checked' : '' ?>> Outros</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="prestador1" class="col-lg-2 control-label" onblur="">Prestador de Serviço</label>
                    <div class="col-lg-7">
                        <?= montaSelect($op_prestadores, $nfse_arr['PrestadorServico'], 'class="form-control col-lg-2 validate[required]" name="prestador" id="prestador1"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="numeros" class="col-lg-2 control-label">Número da Nota</label>
                    <div class="col-lg-3">
                        <input id="numeros" name="numero" type="text" class="form-control text-center validate[required]" maxlength="18" value="<?= $nfse_arr['Numero'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="codigoverificacao" class="col-lg-2 control-label">Código de Verificação</label>
                    <div class="col-lg-3">
                        <input id="codigoverificacao" name="codigoverificacao" method="post" type="text" class="form-control text-uppercase text-center" value="<?= $nfse_arr['CodigoVerificacao'] ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="emissao" class="col-lg-2 control-label">Emissão</label>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input type="text" class="form-control text-center data validate[required]" name="emissao" id="emissao" value="<?= (!empty($nfse_arr['DataEmissao'])) ? ConverteData($nfse_arr['DataEmissao'], 'd/m/Y') : "" ?>">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="competencia" class="col-lg-2 control-label">Competência</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <?php
                            $mes = ($nfse_arr['Competencia']) ? ConverteData($nfse_arr['Competencia'], 'm') : date('m');
                            echo montaSelect(mesesArray(), $mes, 'class="form-control text-center validate[required,custom[select]]" name="mes_competencia" id="mes_competencia"');
                            ?>
                            <span class="input-group-addon">/</span>
                            <?php
                            $ano = ($nfse_arr['Competencia']) ? ConverteData($nfse_arr['Competencia'], 'Y') : date('Y');
                            $anos = array($ano - 2 => $ano - 2, $ano - 1 => $ano - 1, $ano => $ano);
                            echo montaSelect($anos, $ano, 'class="form-control text-center validate[required]" name="ano_competencia" id="ano_competencia"');
                            ?>

                        </div>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label for="CodigoTributacaoMunicipio" class="col-lg-2 control-label">Serviço Prestado</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                        <input type="text" class="form-control validate[required]" name="CodigoTributacaoMunicipio" id="CodigoTributacaoMunicipio" placeholder="Código do Serviço" value="<?= $nfse_arr['CodigoTributacaoMunicipio'] ?>">
                            <span class="input-group-btn">
                                <a class="btn btn-info" href="http://sped.rfb.gov.br/pagina/show/1601" target="_blank">Lista Sped <i class="fa fa-external-link"></i></a>
                            </span>
                        </div><!-- /input-group -->
                        <span class="help-block">Apenas números.</span>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" class="form-control validate[required]" name="txt_servico" id="txt_servico" readonly value="<?= $nfse_arr['descricao_cod_servico'] ?>">
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="nfe_pdf_cm" class="col-lg-2 control-label">Arquivo PDF</label>
                    <div class="col-lg-5">
                        <input type="file" accept="application/pdf" name="nfe_pdf_cm" id="nfe_pdf_cm" class="form-control filestyle" data-buttonText="&emsp;PDF">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Arquivo XML</label>
                    <div class="col-lg-5">
                        <input type="file" accept="text/xml" name="nfe_xml" id="nfe_xml" class="form-control filestyle" data-buttonText="&emsp;XML">
                    </div>
                </div>
                <?php
                if (!empty($anexos)) {
                    ?>
                    <div class="row">
                        <div class="col-lg-10 <?= $key == 0 ? 'col-lg-offset-2' : '' ?>">
                            <?php
                            foreach ($anexos as $key => $anex) {
                                $icon = $anex['extencao'] == 'pdf' ? 'fa-file-pdf-o text-danger' : 'fa-file-code-o text-info';
                                ?>
                                <input type="hidden"  name="id_anexo" id="id_anexo" value="<?= $anex['id_anexo'] ?>">
                                <a href="nfse_anexos/<?= $anex['id_projeto'] ?>/<?= $anex['arquivo_pdf'] ?>" class="btn btn-default btn-lg" target="_blank"><i class="fa <?= $icon ?>"></i> Arquivo PDF</a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="panel-footer text-right">
                <button type="submit" name="salvar-nfse-manual" value="Salvar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
            </div>
        </div>
    </form>
    <div id="resp_form_cad" class="loading"></div>
</div>

