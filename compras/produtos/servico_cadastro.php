<!--<div class="bs-component">-->
<ul class="nav nav-tabs" style="margin-bottom: 15px;">
    <li class="active"><a href="#formulario" data-toggle="tab">Cadastro NFe</a></li>
    <li><a href="#arquivoXML" data-toggle="tab">Arquivo XML</a></li>
    <li><a href="#visualizaXML" data-toggle="tab">Visualizar NFe</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <!-- Cadastro manual da NFe -->
    <div class="tab-pane fade in active" id="formulario"> 
        <form action="NFe_atualiza_tab.php" method="post" name="form1" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" >
            <input type="hidden" name="home" id="home">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="regiao" class="col-lg-2 control-label">Região</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao1' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto1'"); ?>
                        </div>
                        <label for="projeto" class="col-lg-1 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo $projeto1; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label" onblur="">Prestador</label>
                        <div class="col-lg-9">
                            <?= montaSelect(array('-1' => '« Selecione o Projeto »'), NULL, 'class="col-lg-2 form-control" name="prestador" id="prestador1"'); ?>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="chaveacesso" class="col-lg-2 control-label">Chave de Acesso</label>
                        <div class="col-lg-6">
                            <input id="chaveacesso" name="chaveacesso" method="post" type="text" class="form-control text-center" 
                                   onkeypress="formata_mascara(this, '#### #### #### #### #### #### #### #### #### #### ####')" 
                                   placeholder="Chave de Acesso">
                        </div>
                        <label for="numeronf" class="col-lg-1 control-label">NF</label>
                        <div class="col-lg-2">
                            <input id="numeronf" name="numeronf" type="text" class="form-control text-center validate[required]" maxlength="18">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Natureza da Operação</label>
                        <div class="col-lg-5">
                            <select class="col-lg-4 form-control" name="cfop">
                                <option value="">
                                    Natureza de Operação
                                </option>
                                <?php
                                while ($escolha = mysql_fetch_array($sqlcfop)) {
                                    $selected = ($escolha['id_cfop'] == $_REQUEST['cfop']) ? 'selected="selected"' : '';
                                    ?>
                                    <option value="<?php echo $escolha['id_cfop'] ?>" <?php echo $selected; ?>>
                                    <?php echo $escolha['id_cfop'] . " - " . $escolha['descricao'] ?>
                                    </option>
<?php } ?> 
                            </select>
                        </div>
                        <label for="" class="col-lg-2 control-label">Data Emissão</label>
                        <div class="col-lg-2">
                            <div class="input-group">
                                <input type="text" class="form-control text-center data" name="dt_emissao_nf" id="dt_emissao_nf">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="autocomplete" class="col-lg-2 control-label">Produto / Serviço</label>
                        <div class="col-lg-6">
                            <input id="cod-item" name="cod-item" type="hidden">
                            <input id="item" name="item" class="form-control text-left">
                        </div>
                        <div class=" ">
                            <button type="button" id="item-incluir" class="btn btn-success"><i class="fa fa-plus"></i> Incluir Produto</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                        <th>NCM/ST</th>
                                        <th>Und</th>
                                        <th>Valor Untário</th>
                                        <th>Qtd</th>
                                        <th>Valor (R$)</th>
                                    </tr>
                                </thead>
                                <tbody id="tb-itens">

                                </tbody>
                            </table>
                        </div><!-- /.col-lg-12 -->
                    </div><!-- /.row -->
                </div><!-- /.panel-body -->
                <div class="panel-footer text-right">
                    <input type="submit" value="Salvar" name="cadastro-salvar" class="btn btn-primary" id="cadastro-salvar">
                </div>
            </div><!-- /.panel-default -->
        </form>
        <div id="resp_form_cad" class="loading"></div>
    </div><!-- fim do cadastro manual da NFe -->

    <!-- inicio da importação do XML -->
    <div class="tab-pane fade" id="arquivoXML">
        <form action="NFe_atualiza_tab.php" id="form2" method="post" class="form-horizontal" enctype="multipart/form-data">
            <fieldset>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-lg-5">
                                <input type="file" name="nfe" id="nfe" class="form-control filestyle" data-buttonText=" Selecione Arquivo">
                            </div>
                            <div class="col-lg-5">
                                <input type="hidden" name="aba" id="aba" value="importar">
                                <input type="submit" value="Importar" name="importar" class="btn btn-primary btn-tm" id="import">
                            </div>
                        </div>
                    </div>
                </div>
                <div id="habilitar" class="panel panel-default hidden">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="regiao" class="col-lg-1 control-label">Região</label>
                            <div class="col-lg-4">
<?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao2' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto2'"); ?>
                            </div>
                            <label for="projeto" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
<?php echo $projeto2; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-lg-1 control-label" onblur="">Prestador</label>
                            <div class="col-lg-10">
                                <select class="col-lg-2 form-control" name="prestador" id="prestador2">
                                    <option>Selecione Pestador</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="reset" value="Cancelar" name="cancela" class="btn btn-warning btn-tm" id="limpa">
                        <input type="submit" value="Salvar" name="salvar" class="btn btn-success btn-tm" id="salvar" disabled="">
                    </div>
                </div>
            </fieldset>
            <!--                                    <div id="tabela" class="loading">
            
                                                </div>-->
        </form>
    </div><!-- fim da importação do XML -->

    <!-- VISUALIZAR NFe -->
    <div class="tab-pane fade" id="visualizaXML"> 
        <form action="visualizar_XML.php" id="form3" method="post" class="form-horizontal" enctype="multipart/form-data">
            <fieldset>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="regiao" class="col-lg-1 control-label">Região</label>
                            <div class="col-lg-4">
<?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao3' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto3'"); ?>
                            </div>
                            <label for="projeto" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
<?php echo $projeto3; ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="aba" id="aba" value="visualizar">
                        <input type="submit" value="Visualizar" name="visualiza" class="btn btn-success btn-tm" id="visualiz">
                    </div>
                </div>
            </fieldset>
        </form>

        <div id="visualizar-NFe" class="loading"></div>
    </div>
    <!-- FIM DO VISUALIZAR XML -->
</div>
<!--</div>-->
