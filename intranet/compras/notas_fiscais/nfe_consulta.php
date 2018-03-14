<!-- VISUALIZAR NFe -->
<div class="tab-pane fade" id="visualizaXML"> 
    <form action="visualizar_xml.php" id="form3" method="post" class="form-horizontal" enctype="multipart/form-data">
        <fieldset>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="regiao" class="col-lg-1 control-label">Região</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto'"); ?>
                        </div>
                        <label for="projeto" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo $projeto; ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <input type="hidden" name="aba" id="aba3" value="visualizar">
                    <input type="submit" value="Visualizar" name="visualiza" class="btn btn-success btn-tm" id="visualiz">
                </div>
            </div>
        </fieldset>
    </form>

    <div id="visualizar-NFe" class="loading"> </div>
</div>    
<!-- FIM DO VISUALIZAR XML --> 