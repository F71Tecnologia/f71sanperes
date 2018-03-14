<form action="nfe_controle.php" id="frm-arquivo-xml" method="post" class="form-horizontal" enctype="multipart/form-data">
    <fieldset>
        <?php if (count($lista) > 0) { ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <a role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample" class="btn-show-colapsed">
                        <strong>Nota(s) Importadas para o Pedido</strong> <span class="badge"><?= count($lista) ?> </span>
                        <span class="pull-right">
                            <i class="fa fa-chevron-down "></i>
                        </span>
                    </a>
                </div>

                <div class="collapse" id="collapseExample">
                    <table class="table table-striped table-hover table-condensed no-margin-b">
                        <thead>
                            <tr>
                                <th style="width: 5%;">NF</th>
                                <th style="width: 40%;">Emitente</th>
                                <th style="width: 15%;">CNPJ/CPF</th>
                                <th style="width: 15%;" class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista as $value) { ?>
                                <tr id="tr-<?= $value['id_nfe'] ?>">
                                    <td class="text-center"><?= $value['nNF'] ?></td>
                                    <td><?= $value['emit_xNome'] ?></td>
                                    <td><?= $value['emit_CNPJ'] ?></td>
                                    <td class="text-right">R$ <?= number_format($value['vNF'], 2, ",", '.') ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group no-margin-b">
                    <label for= "pedidoss" class="col-lg-1 control-label" onblur="">Pedido</label>
                    <div class= "col-lg-2">
                        <input type="text" name="pedidoss" id="pedidoss" class="form-control" value="<?= $_REQUEST['id_pedido'] ?>" readonly>
                        <input type="hidden" name="fornecedor" id="id_fornecedor" value="<?= $id_fornecedor ?>" readonly>
                        <input type="hidden" name="projeto" id="id_projeto" value="<?= $id_projeto ?>" readonly>
                    </div>

                    <label for="nfe" class="col-lg-2 control-label" onblur="">Nota Fiscal</label>
                    <div class="col-lg-5">
                        <input type="file" name="nfe" id="nfe" class="form-control" data-buttonText=" Arquivo.xml ">
                    </div>
                    <div class="col-lg-2">
                        <input type="hidden" name="aba" id="aba1" value="importar">
                        <button type="submit" value="Visualizar" name="importar" class="btn btn-default btn-block btn-info" id="import"><i class="fa fa-search"></i> Visualizar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default hidden habilitar">
            <table class="table table-condensed table-striped ">
                <thead>
                    <tr>
                        <th colspan="5" class="bg-default text-info"> DADOS DA NFe </th>
                    </tr>
                    <tr class="text-sm text-light-gray">
                        <th>Fornecedor</th>
                        <th class="text-center">CNPJ</th>
                        <th class="text-center">Data</th>
                        <th class="text-center">Número NFe</th>
                        <th class="text-right">Valor R$</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-sm text-semibold text-uppercase">
                        <td id="fornecedor"></td>
                        <td id="cnpj_forn" class="text-center"></td>
                        <td id="emissao" class="text-center"></td>
                        <td id="nf_nr" class="text-center"></td>
                        <td id="valor_nf" class="text-right"></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th colspan="5" class="bg-default text-info"> DADOS DO PEDIDO </th>
                    </tr>
                    <tr class="text-sm text-light-gray">
                        <th>Cliente</th>
                        <th>CNPJ</th>
                        <th>Endere&ccedil;o</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-semibold text-uppercase">
                    <tr>
                        <td id="cliente"></td>
                        <td id="cnpj_clie"></td>
                        <td id="end_cliente" colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div><!--/.panel -->

        <div id="tabela"></div>

        <div class="panel panel-default hidden habilitar no-margin-b">
            <div class="panel-body text-right right">
            <!--<input type="reset" value="Cancelar" name="cancela" class="btn btn-warning btn-tm" id="limpa"> -->
            <!--<input type="submit" value="Aceitar NFe" name="salvar" class="btn btn-success btn-tm" id="salvar"> -->
                <button type="reset" value="Cancelar" name="cancela" class="btn btn-default btn-tm" id="limpa"><i class="fa fa-reply"></i> Sair</button>
                <button type="submit" value="Aceitar NFe" name="salvar" class="btn btn-success btn-tm" id="salvar"><i class="fa fa-check"></i> Aceitar NFe</button>
            </div>
        </div>

    </fieldset>
</form>



