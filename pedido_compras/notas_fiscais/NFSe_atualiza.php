<?php
header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFSeClass.php");
include("../../classes/global.php");

$nfse = new NFSe(); // instancia obj nfe
$nfse->load($_FILES['nfe']['tmp_name']); // carrega xml para objeto

// consulta cnpj para ver se há projeto cadastrado
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultacnpjcpf') {
    $prestador = $nfse->consultarePrestador($_REQUEST['projeto'], NULL, $_REQUEST['cnpjcpf'] );
    if (count($prestador) == 0) {
        $retorno = array(
            'status' => FALSE,
            'msg' => utf8_encode('Não há cadastro do Prestador ou fornecedor. Cadastro não poderé ser salvo.')
        );
    } else {
        $retorno = array(
            'status' => TRUE,
            'nome' => utf8_encode($prestador['c_razao']),
            'endereco' => utf8_encode($prestador['c_endereco']),
            'cnpj' => utf8_encode($prestador['c_cnpj'])
        );
    }
    echo json_encode($retorno);
    exit();
}

if (isset($_REQUEST['importare']) && $_REQUEST['importare'] == 'Importar') {
    if (isset($nfse->PrestadorServico)) { ?>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped text-center">
                    <thead class="text-danger">
                        <tr>
                            <td>Número da Nota</td>
                            <td>Data e Hora de Emissão</td>
                            <td>Código de Verificação</td>
                        </tr>
                    </thead>
                    <tbody class="text-semibold">
                        <tr>
                            <td><?= $nfse->InfNfse->Numero ?></td>
                            <td><?= $nfse->InfNfse->DataEmissao ?></td>
                            <td><?= $nfse->InfNfse->CodigoVerificacao ?></td>
                        </tr>
                    </tbody>    
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead class="text-danger text-sm">
                        <tr>
                            <td style="width: 40%;">Nome Fantasia</td>
                            <td style="width: 40%;">Razao Social</td>
                            <td style="width: 20%;">CNPJ/CPF</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="active">
                            <td><?= $nfse->PrestadorServico->NomeFantasia ?></td>
                            <td><?= $nfse->PrestadorServico->RazaoSocial ?></td>
                            <td><?= mascara_string("##.###.###/####-##", $nfse->PrestadorServico->IdentificacaoPrestador->Cnpj ) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php }
    if (isset($nfse->Servico)) { ?>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead class="text-danger text-sm">
                        <tr>
                            <td>Discriminação do Serviço</td>
                        </tr>
                    </thead>
                    <tbody class="text-uppercase active">
                        <tr>
                            <td><?= to_iso_8859_1($nfse->Servico->Discriminacao) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped"> 
                    <thead>
                        <tr class="text text-sm text-danger">
                            <td style="width: 17%;">Retenção de COFINS</td>
                            <td style="width: 17%;">Retenção de CSLL</td>
                            <td style="width: 17%;">Retenção de INSS</td>
                            <td style="width: 17%;">Retenção de IRPJ</td>
                            <td style="width: 17%;">Retenção de PIS</td>
                            <td style="width: 15%;">Outras Retenções</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($nfse->Servico->Valores as $valores) { ?>
                        <tr class="active">
                            <td>R$ <?= number_format((float)$valores->ValorCofins ,2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorCsll, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorInss, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorIr, 2, ',', '.') ?></td>
                            <td>R$ <?= number_format((float)$valores->ValorPis, 2, ',', '.') ?></td>
                            <td>R$</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <label class="text text-danger text-sm">VALOR DA NOTA </label>
                <label> R$ <?= number_format((float)$valores->ValorServicos, 2, ',', '.') ?></label>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead class="text text-danger text-sm">
                        <tr><td>Serviço Prestado</td></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfse->Servico->CodigoTributacaoMunicipio ?></td>
                            <td> Banco de dados serviços prestados (criar)</td> <!-- buscar banco de dados serviços prestados -->                        
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead class="text text-danger text-sm">
                        <tr>
                            <td>Deduções (R$)</td>
                            <td>Desconto Incond(R$)</td>
                            <td>Base de Cálculo (R$)</td>
                            <td>Alíquota (%)</td>
                            <td>Valor do ISS (R$)</td>
                            <td>Crédito Gerado (R$)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float)$valores->ValorDeducao ,2, ',', '.') ?></td>
                            <td><?= number_format((float)$valores->ValorDesconto ,2, ',', '.') ?></td>
                            <td><?= number_format((float)$valores->BaseCalculo ,2, ',', '.') ?></td>
                            <td><?= number_format((float)$valores->Aliquota, 2, '.', ',' )*100 ?></td>
                            <td><?= number_format((float)$valores->ValorIss ,2, ',', '.') ?></td>
                            <td><?= number_format((float)$valores->Credito ,2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-right">
                <label class="text-danger text-sm">Valor Líquido a Pagar</label>
                <label> R$ <?= number_format((float)$valores->ValorLiquidoNfse, 2, ',', '.') ?></label>
            </div>
        </div>
        <input type="hidden" name="cnpj-nf" id="cnpj-nf" value="<?= mascara_string("##.###.###/####-##", $nfse->PrestadorServico->IdentificacaoPrestador->Cnpj )?>">
    <?php }
    exit();
}

// SALVAR NFSe BANCO DE DADOS (XML) ------------------------------------------------
if (isset($_REQUEST['salvare']) && $_REQUEST['salvare'] == 'Salvar') {
    $array = $nfse->nfse_xml_to_array();
    $resp1 = $nfse->salvarNFSe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array);
    if ($resp1['status']) { ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal de Serviço Salva com Sucesso.</p>
            
        </div>
        <?php
    } 
    else { ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp1['msg'] ?>
        </div>
        <?php
    }
}

// SALVAR NOTA FISCAL DE SERVIÇO MANUALMENTE NO DB ------------------------------------
if (isset($_REQUEST['salvar-nfse-manual']) && $_REQUEST['salvar-nfse-manual'] == 'Salvar') {
    $array = array( 
        'id_regiao' => $_REQUEST['regiao'],
        'id_projeto' => $_REQUEST['projeto'],
        'Numero' => $_REQUEST['numero'],
        'CodigoVerificacao' => $_REQUEST['Codigoverificacao'],
        'Cnpj' => $_REQUEST['cnpj'],
        'DataEmissao' => converteData($_REQUEST['emissao']),
        'NaturezaOperacao' => $_REQUEST['naturezanperacao'],
        'OptanteSimplesNacional' => $_REQUEST['optante'],
        'IncentivadorCultural' => $_REQUEST['incentivo'],
        'Competencia' => $_REQUEST['competencia'],
        'ValorServicos' => str_replace(',', '.', str_replace('.', '', $_REQUEST['vlr_bruto'])),
        'ValorPis' => str_replace(',', '.', str_replace('.', '', $_REQUEST['pis'])),
        'ValorCofins' => str_replace(',', '.', str_replace('.', '', $_REQUEST['cofins'])),
        'ValorInss' => str_replace(',', '.', str_replace('.', '', $_REQUEST['inss'])),
        'ValorIr' => str_replace(',', '.', str_replace('.', '', $_REQUEST['ir'])),
        'ValorCsll' => str_replace(',', '.', str_replace('.', '', $_REQUEST['csll'])),
//        'IssRetido' => $_REQUEST['competencia']$_REQUEST['iss'])),
        'ValorIss' => str_replace(',', '.', str_replace('.', '', $_REQUEST['iss'])), 
        'BaseCalculo' => str_replace(',', '.', str_replace('.', '', $_REQUEST['basecalculo'])),
        'Aliquota' => str_replace(',', '.', str_replace('.', '', $_REQUEST['aliquota'])),
        'ValorLiquidoNfse' => str_replace(',', '.', str_replace('.', '', $_REQUEST['vlr_liquido'])), 
        'CodigoTributacaoMunicipio' => $_REQUEST['cofins'],
        'Discriminacao' => $_REQUEST['discriminacao'],
    );
    
    $resp1 = $nfse->salvarNFSe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array, TRUE);

    if ($resp1['status']) { ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal de Serviço Salva com Sucesso.</p>
        </div>
        <?php
    } else { ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp1['msg'] ?>
        </div>
        <?php
    }
}