<div class="tab-content">
    <div class="tab-pane active" id="lancamentos">
        <div class="panel panel-group">
                <div class="col-lg-6">
                    <button type="button" class="btn btn-block btn-default btn-xs" id="multi">
                        <i class="fa fa-sort-amount-asc"></i><strong> Mult&iacute;plo</strong>
                    </button></div>
                <div class="col-lg-6">
                    <button type="button" class="btn btn-block btn-default btn-xs" id="simpli">
                        <i class="fa fa-exchange"></i><strong> Simples</strong>
                    </button></div>
         </div>
        <br>            
        <div class="text text-sm panel panel-default" id="div_simples" style="display: none">
            <form action="classificacao_controle.php" method="post" name="form_lancamento_simples" id="form_lancamento_simples" class="w form-horizontal top-margin" enctype="multipart/form-data">
                <input type="hidden" id="lotes" name="lotes" value="<?= $_REQUEST['nrlote'] ?>">
                <input type="hidden" id="projetos" name="projetos" value="<?= $_REQUEST['nrprojeto'] ?>">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Data</label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-calendar"></label>
                                </span>
                                <input type="text" style="z-index: 100000" id="data_lancaments" name="data_lancaments" class="form-control text-center datalancamento"/>
                            </div>
                        </div>
                        <label class="col-lg-6 control-label text-bold text-success">Simples</label>
                    </div> 
                    <hr>
                    <br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Documento</label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-briefcase"></label>
                                </span>
                                <input type="text" name="documento" id="documento_s" maxlength="10" class="form-control"/>
                            </div>
                        </div>
                        <label class="col-lg-1 control-label">Valor</label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-usd"></label>
                                </span>
                                <input type="text" name="valor" id="valor_s" maxlength="14" class="form-control text-right money"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Conta Devedora</label>
                        <div class="col-lg-3">
                            <input class="form-control text-center" id="s_devedora" name="s_devedora" style="z-index: 50000;" data-conta="cod_contad" data-conta_id="contad_id">
                        </div> 
                        <div class="col-lg-6">
                            <input id="tipo_contad" name="tipo_2" value="2" type="hidden">
                            <input id="contad_id" name="contad_id" type="hidden">
                            <input id="cod_contad" name="cod_contad" type="text" readonly class="form-control">
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Conta Credora</label>
                        <div  class="col-lg-3">
                            <input class="form-control text-center" id="s_credora" name="s_credora" style="z-index: 50000" data-conta="cod_contac" data-conta_id="contac_id">
                        </div>
                        <div class="col-lg-6">
                            <input id="tipo_contac" name="tipo_1" value="1" type="hidden">
                            <input id="contac_id" name="contac_id" type="hidden">
                            <input id="cod_contac" name="cod_contac" type="text" readonly class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-lg-2 control-label">Histórico</label>
                        <div class="col-lg-9">
                            <textarea rows="3" id="historico" name="historico" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="hidden" name="save_simples" value="1">
                    <button type="button" id="btn_save_simples" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Salvar </button>
                </div>
            </form>
        </div>

        <div class="text text-sm panel panel-default" id="div_multiplos" style="display:none;">
            <form action="classificacao_controle.php" method="post" name="form_lancamento_multiplos" id="form_lancamento_multiplos" class="form-horizontal top-margin" enctype="multipart/form-data">
                <input type="hidden" id="lotem" name="lotem" value="<?= $_REQUEST['nrlote'] ?>">
                <input type="hidden" id="projetom" name="projetom" value="<?= $_REQUEST['nrprojeto'] ?>">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Data</label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-calendar"></label>
                                </span>
                                <input type="text" style="z-index: 100000" id="data_lancamentm" name="data_lancamentm" class="validate[required] form-control text-center datalancamento"/>
                            </div>
                        </div>
                        <label class="col-lg-6 control-label text-bold text-warning">Mult&iacute;plo</label>
                    </div> 
                    <hr>
                    <br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Documento</label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-briefcase"></label>
                                </span>
                                <input type="text" name="documentom" id="documentom" maxlength="12" class="form-control"/>
                            </div>
                        </div>
                        <label class="col-lg-1 control-label">Valor</label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <label class="glyphicon glyphicon-usd"></label>
                                </span>
                                <input type="text" value="" name="valor_m" id="valor_m" maxlength="14" class="form-control text-right money validate[required]"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Conta</label>
                        <div class="col-lg-3">
                            <input class="form-control text-center validate[required]" id="cod_conta" name="cod_conta" style="z-index: 50000;" data-conta="codconta" data-conta_id="contam_id">
                        </div>
                        <div class="col-lg-6">
                            <input id="contam_id" name="contam_id" type="hidden">
                            <input id="codconta" name="codconta" type="text" readonly class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"></label>
                        <div class="col-lg-4">
                            <label class="radio-inline text-bold text-sm"><input type="radio" id="conta_tipo" name="conta_tipo" value="2">Credora</label>
                            <label class="radio-inline text-bold text-sm"><input type="radio" id="conta_tipo" name="conta_tipo" value="1" checked>Devedora</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Histórico</label>
                        <div class="col-lg-9">
                            <textarea rows="3" id="historicom" name="historicom" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"></label>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-block btn-default btn-xs btnIncluir" id="incluir_conta">
                                <i class="fa fa-sort-amount-asc"></i> Gravar
                            </button>
                        </div>
                    </div>
                    <table class="table table-condensed table-striped" id="tbl_multiplos">
                    <thead>
                        <tr>
                            <th>Conta</th>
                            <th colspan="2">Descri&ccedil;&atilde;o</th>
                            <th>Valor</th>
                            <th style="width: 3%"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                <div class="panel-footer">
                <table class="table table-condensed text-sm">
                    <thead>
                        <tr>
                            <th>Diferença</th>
                            <th>Conta Credora</th>
                            <th>Conta Devedora</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" readonly class="form-control money" id="diferenca" value="0,00"></td>
                            <td><input type="text" readonly class="form-control money" id="somacredora" value="0,00"></td>
                            <td><input type="text" readonly class="form-control money" id="somadevedora" value="0,00"></td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <div class="panel-footer text-right">
                    <input type="hidden" name="save_multiplos" value="1">
                    <button type="button" class="btn btn-primary btn-sm " id="btn_save_multiplos"><i class="fa fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
        <div id="resp"> </div>
    </div>
</div>