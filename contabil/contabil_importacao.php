<div class="tab-content">
    <div class="tab-pane active" id="lancamentos">
        <form action="contabil_importacao.php" method="post" name="form_importacao" id="form_importacao" class="form-horizontal top-margin" enctype="multipart/form-data">
            <input type="hidden" name="importacoes" value="Salvar">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-4">
                        <label>Lote</label>
                        <p><input type="text" value="<?= $_REQUEST['lote']?>" name="lote" id="lote" maxlength="10" class="form-control" disabled="true"></p>
                    </div>
                    <div class="col-lg-8">
                        <label>Projeto</label>
                        <p><input class="form-control" disabled="true" type="text" id="id_projeto" value="<?= $_REQUEST['nome_projeto'] ?>"></p>
                   </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-block btn-warning btn-xs" id="multi"><i class="fa fa-sort-amount-asc"></i> Folha de Pagamento</button>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-block btn-info btn-xs" id="simpli"><i class="fa fa-exchange"></i> Fiscal</button>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-block btn-info btn-xs" id="simpli"><i class="fa fa-exchange"></i> Fnanceiro</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default" id="div_simples" style="display: none">
                <div class="panel-body">
                    <div class="col-lg-4">
                        <label>Data</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="glyphicon glyphicon-calendar"></label>
                            </span>
                            <input type="text" class="form-control text-center hasdatepicker datalancamento" name=""/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Documento</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="glyphicon glyphicon-briefcase"></label>
                            </span>
                            <input type="text" value="" name="documento" id="documento" maxlength="10" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Valor do lançamento</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="glyphicon glyphicon-usd"></label>
                            </span>
                            <input type="text" value="" name="valor" id="valor" maxlength="14" class="form-control text-right"/>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Conta Devedora</label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="text" class="form-control devedora" id="devedora" name="devedora">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-search"></label>
                                </span>
                            </div>                                    
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Conta Credora</label>
                        <div  class="col-lg-3">                              
                            <div class="input-group">
                                <input type="text" value="" name="credora" id="credora" maxlength="13" class="form-control credora">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-search"></label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contbprojeto2" class="col-lg-2 control-label">Histórico</label>
                        <div class="col-lg-9">
                            <textarea rows="3" id="historico" name="historico" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button type="" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Salvar</button>
                </div>
            </div>
            <div class="panel panel-default" id="div_multiplos" style="display:none;">
                <div class="panel-body">
                    <div class="col-lg-4">
                        <label>Data</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="glyphicon glyphicon-calendar"></label>
                            </span>
                            <input type="text" class="form-control text-center hasdatepicker datalancamento" name=""/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Documento</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="glyphicon glyphicon-briefcase"></label>
                            </span>
                            <input type="text" value="" name="documento" id="documento" maxlength="10" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label>Valor do lançamento</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <label class="glyphicon glyphicon-usd"></label>
                            </span>
                            <input type="text" value="" name="valor" id="valor" maxlength="14" class="form-control text-right"/>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Conta Devedora</label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="text" class="form-control devedora" id="devedora" name="devedora">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-search"></label>
                                </span>
                                <button class="btn btn-block bg-info form-control">Incluir</button>
                            </div>                                    
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Conta Credora</label>
                        <div  class="col-lg-4">                              
                            <div class="input-group">
                                <input type="text" value="" name="credora" id="credora" maxlength="13" class="form-control credora">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-search"></label>
                                </span>
                                <button class="btn btn-block bg-info form-control">Incluir</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Histórico</label>
                        <div class="col-lg-9">
                            <textarea rows="3" id="historico" name="historico" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>Conta</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="panel-footer text-right">
                    <button type="" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>