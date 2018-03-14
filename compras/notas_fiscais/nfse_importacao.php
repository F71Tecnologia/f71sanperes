<div class="tab-pane fade" id="ler_arquivo_xml">
    <form action="nfse_atualiza.php" id="form_ler_arquivo_xml" method="post" class="form-horizontal" enctype="multipart/form-data">
        <fieldset>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">                            
                        <label class="col-lg-2 control-label">Prefeitura</label>
                        <div class="col-lg-5">
                            <?php echo $nfse->selectPrefeituras("name='prefeitura' class='form-control'") ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Arquivo</label>
                        <div class="col-lg-5">
                            <input type="file" accept="text/xml" name="nfe" id="nfe" class="form-control filestyle" data-buttonText=" .XML">
                        </div>
                        <div class="col-lg-3">
                            <input type="hidden" name="aba" id="aba" value="importare">
                            <input type="submit" value="Importar" name="importare" class="btn btn-primary btn-tm" id="importare">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <div id="tabela" class="loading"></div>
        <hr>
        <fieldset>
            <div class="panel panel-default hidden" id="habilitar">
                <div class="panel-body text-right">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Arquivo</label>
                        <div class="col-lg-5">
                            <input type="file" accept="application/pdf" name="nfe_pdf" id="nfe_pdf" class="form-control filestyle" data-buttonText=" .PDF">
                        </div>
                        <div class="col-lg-1">
                            <input type="submit" value="Anexar" name="anexar_pdf" class="btn btn-primary btn-tm" id="anexar_pdf">
                        </div>
                        <div class="col-lg-4">
                            <input type="submit" value="Aceitar NFs" name="salvare" class="btn btn-success btn-tm" id="salvare" disabled="">
                            <input type="reset" value="Cancelar" name="cancela" class="btn btn-warning btn-tm" id="limpa">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
