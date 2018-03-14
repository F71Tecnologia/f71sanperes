<?php
header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFeClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/global.php");
//require_once ("../../classes/pdf/dompdf_config.inc.php");

$nfe = new NFe(); // instancia obj nfe

//$dompdf = new DOMPDF();
//
//$dompdf->load_html('<!doctype html>');
//
//$dompdf->render();
//
//$dompdf->stream(
//    "Pedido.pdf", array(
//        "Attachment" => false
//    )
//);

        

//error_reporting(E_ALL);

// consulta cnpj para ver se há projeto cadastrado
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultacnpjcpf') {
    $prestador = $nfe->consultaPrestador($_REQUEST['projeto'], $_REQUEST['cnpjcpf']);
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
            'cnpjcpf' => utf8_encode($prestador['c_cnpj'])
        );
    }
    echo json_encode($retorno);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultapedidos') {
    $pedido = new pedidosClass();
    $consultapedido = $pedido->enviandoPedido($_REQUEST['regiao'],$_REQUEST['´projeto'],$_REQUEST['prestador']);
    echo '<option value="">Selecione</option>';
    foreach ($consultapedido as $key => $value){
        $data = converteData($value['data'],'d/m/Y');
        $total = number_format($value['total'], 2,',','.');
        echo "<option value = '$key'>Pedido Número  $key - Data  $data - Total R$ $total</option>";
    }
    exit(); 
}

$nfe->load($_FILES['nfe']['tmp_name']); // carrega xml para objeto
// trazer item para cadastro manual
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'itemIncluir') {
    $produtos = $nfe->consultarProduto($_REQUEST['id_prod']); ?>
    <tr id="tr-item-<?= $produtos['id_prod'] ?>" >
<!--        <td>
            <?= $produtos['id_prod'] ?>
            <input type="hidden" name="id_prod[]" value="<?= $produtos['id_prod'] ?> ">
            <input type="hidden" name="id_prestador[]" value="<?= $produtos['id_prestador'] ?> ">
             estao aqui apenas para casos futuros 
            <input type="hidden" name="cProd[]" value="<?= $produtos['cProd'] ?> ">
            <input type="hidden" name="xProd[]" value="<?= $produtos['xProd'] ?> ">
            <input type="hidden" name="cEAN[]" value="<?= $produtos['cEAN'] ?> ">
            <input type="hidden" name="NCM[]" value="<?= $produtos['NCM'] ?> ">
            <input type="hidden" name="EXTIPI[]" value="<?= $produtos['EXTIPI'] ?> ">
            <input type="hidden" name="uCom[]" value="<?= $produtos['uCom'] ?> ">
        </td>-->
        <td><?= $produtos['id_prod']."-".$produtos['xProd'] ?></td>
        <td><?= $produtos['NCM'] ?></td>
        <td><?= $produtos['uCom'] ?></td>
        <td><?= number_format($produtos['vUnCom'], 2, ',', '.') ?><input type="hidden" name="vUnCom[]" id="vUnCom-<?= $produtos['id_prod'] ?>" class="form-control money validate[required]" value="<?= number_format($produtos['vUnCom'], 2, ',', '.') ?>"></td>
        <td><input type="text" name="nLote[]" id="nLote-<?= $produtos['id_prod'] ?>" class="form-control"></td>
        <td><input type="text" name="dVal[]" id="dVal-<?= $produtos['id_prod'] ?>" class="form-control text-center data hasdatepicker"></td>
        <td><input type="text" class="form-control qtd-item validate[required]" name="qCom[]" data-id="<?= $produtos['id_prod'] ?>"></td>
        <td><input type="text" class="form-control" name="vProd[]" id="vProd-<?= $produtos['id_prod'] ?>" readonly=""></td>
        <td>
            <button type="button" class="btn btn-danger item-excluir" data-id="<?= $produtos['id_prod'] ?>">
                <i class="fa fa-times"></i> Exlcuir
            </buttom>
        </td>
    </tr>
    <?php
    exit();
}

// importar XML ----------------------------------------------------------------
if (isset($_REQUEST['importar']) && $_REQUEST['importar'] == 'Visualizar NFe') { ?>

    <div class="alert alert-dismissable alert-danger">
        <p class="text text-info text-semibold">Observações Complementares</p>
        <p><?= $nfe->infAdic->infCpl ?></p>        
    </div>
    <div class="alert alert-dismissable alert-danger">
        <table class="table table-condensed">
            <thead>
                <tr class="text text-sm text-default">
                    <th colspan="2">Fornecedor</th>
                    <th class="text-center">Número Nota Fiscal</th>
                    <th class="text-right">Valor R$</th>
                </tr>
            </thead>
            <tbody class="text text-semibold text-danger">
                <tr>
                    <td><?= $nfe->emit->xNome ?> </td>
                    <td><?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?></td>
                    <td class="text-center"><?= $nfe->ide->nNF ?></td>
                    <td class="text-right"><?= number_format((float) $nfe->total->ICMSTot->vNF, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td><?= $nfe->emit->enderEmit->xLgr . ", " . $nfe->emit->enderEmit->nro . " " . $nfe->emit->enderEmit->xCpl ?> </td>
                </tr>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="nameFile" value="<?= $nfe->savedFile ?>"/>
<!--               
    <table class="table table-striped table-bordered">
        <h4>Dados Gerais</h4> 
        <tr class="danger">
            <th>Chave de acesso</th>
            <th class="text-center">Número</th>
            <th class="text-center">Versão XML</th>
        </tr>
        <tr>
            <td><?=
                substr($nfe->xml->NFe->infNFe->attributes()->Id, 3, 4) . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 7, 4)
                . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 11, 4) . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 15, 4)
                . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 19, 4) . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 23, 4)
                . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 27, 4) . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 31, 4)
                . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 35, 4) . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 39, 4)
                . " " . substr($nfe->xml->NFe->infNFe->attributes()->Id, 43, 4)
                ?>
            </td>
            <td class="text-center"><?= $nfe->ide->nNF ?></td>
            <td class="text-center"><?= $nfe->xml->NFe->infNFe->attributes()->versao ?></td>
        </tr> 
    </table>
    <div role="tabpanel">
        <ul class="margim nav nav-pills" role="tablist">
            <li role="presentation" class="active"><a href="#NFe" aria-controls="NFe" role="tab" data-toggle="tab" class="compras">NF-e</a></li>
            <li role="presentation"><a href="#emitente" aria-controls="emitente" role="tab" data-toggle="tab" class="compras">Emitente</a></li>
            <li role="presentation"><a href="#destinatario" aria-controls="destinatario" role="tab" data-toggle="tab" class="compras">Destinatário</a></li>
            <li role="presentation"><a href="#prodservico" aria-controls="prodservico" role="tab" data-toggle="tab" class="compras">Produtos e Serviços</a></li>
            <li role="presentation"><a href="#totais" aria-controls="totais" role="tab" data-toggle="tab" class="compras">Totais</a></li>
            <li role="presentation"><a href="#transporte" aria-controls="transporte" role="tab" data-toggle="tab" class="compras">Transporte</a></li>
            <li role="presentation"><a href="#cobranca" aria-controls="cobranca" role="tab" data-toggle="tab" class="compras">Cobrança</a></li>
            <li role="presentation"><a href="#informacao" aria-controls="informacao" role="tab" data-toggle="tab" class="compras">Informações Adicionais</a></li>
        </ul>
        <hr>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="NFe">  NFe 

                <table cellpadding="20" class="table table-striped">  dados da NFe 
                    <thead>
                    <td colspan="6" class="text-uppercase danger">Dados da NF-e</td>
                    <tr class="legends">
                        <td class="text-center">Modelo</td>
                        <td class="text-center">Série</td>
                        <td class="text-center">Número</td>
                        <td class="text-center">Data de Emissão</td>
                        <td class="text-center">Data de Saída/Entrada</td>
                        <td class="text-right">Valor Total da Nota Fiscal</td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="active text-center"><?= $nfe->ide->mod ?></td>
                            <td class="active text-center"><?= $nfe->ide->serie ?></td>
                            <td class="active text-center"><?= $nfe->ide->nNF ?></td>
                            <td class="active text-center"><?php echo implode('/', array_reverse(explode('-', $nfe->ide->dEmi))); ?></td>
                            <td class="active text-center"><?php echo implode('/', array_reverse(explode('-', $nfe->ide->dSaiEnt))); ?></td>
                            <td class="active text-right"><?= number_format((float) $nfe->total->ICMSTot->vNF, 2, ',', '.'); ?></td>
                        </tr>
                    <tbody>
                </table>
                <table class="table table-striped">  dados da Emitente 
                    <thead>
                        <tr><td colspan="4" class="text-uppercase danger">Emitente</td></tr>
                        <tr class="legends">
                            <td>CNPJ</td>
                            <td>Nome / Razão Social</td>
                            <td>Inscrição Estadual</td>
                            <td class="text-center">UF</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?></td>
                            <td><?= $nfe->emit->xNome ?></td>
                            <td><?= $nfe->emit->IE ?></td>
                            <td class="active text-center"><?= $nfe->emit->enderEmit->UF ?></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped" style="table-layout: fixed">  dados do Destinatário
                    <thead>
                        <tr><td colspan="4" class="text-uppercase danger">Destinatário</td></tr>
                        <tr class="legends">
                            <td>
                                <?php
                                if ($nfe->dest->CPF) {
                                    echo 'CPF';
                                }
                                ?>
                                <?php
                                if ($nfe->dest->CNPJ) {
                                    echo 'CNPJ';
                                }
                                ?>
                            </td>
                            <td>Nome / Razão Social</td>
                            <td class="text-center">Inscrição Estadual</td>
                            <td class="text-center">UF</td>                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if (isset($nfe->dest->CPF)) {
                                    echo mascara_string("###.###.###-##", $nfe->dest->CPF);
                                } else {
                                    echo mascara_string("##.###.###/####-##", $nfe->dest->CNPJ);
                                }
                                ?>
                            </td>
                            <td class="active text-center"><?= $nfe->dest->xNome ?></td>
                            <td class="active text-center"><?= $nfe->dest->IE ?></td>
                            <td class="active text-center"><?= $nfe->dest->enderDest->UF ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td colspan="2">Destino da operação</td>
                            <td>Consumidor final</td>
                            <td>Presença do Comprador</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2"></td>
                            <td class="active"></td>
                            <td class="active"></td>
                        </tr>
                    </tbody>
                </table> 
                <table class="table table-striped">  dados da Emissão 
                    <thead>
                        <tr>
                            <td colspan="4" class="text-uppercase danger">Emissão</td>
                        </tr>
                        <tr class="legends">
                            <td>Processo</td>
                            <td>Versão do Processo</td>
                            <td>Tipo de Emissão</td>
                            <td>Finalidade</td>
                        </tr>
                    </thead>
                    <tbody> 
                        <tr>
                            <td>
                                <?php
                                if ($nfe->ide->procEmi == 0) {
                                    echo '0 - emissão de NF-e com aplicativo do contribuinte';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->procEmi == 1) {
                                    echo '1 - emissão de NF-e avulsa pelo Fisco';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->procEmi == 2) {
                                    echo '2 - emissão de NF-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->procEmi == 3) {
                                    echo '3 - emissão NF-e pelo contribuinte com aplicativo fornecido pelo Fisco';
                                }
                                ?> 
                            </td>
                            <td class="text-center"><?= $nfe->ide->verProc ?></td>
                            <td>
                                <?php
                                if ($nfe->ide->tpEmis == 1) {
                                    echo '1 - Normal';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->tpEmis == 2) {
                                    echo '2 - Contingência FS';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->tpEmis == 3) {
                                    echo '3 - Contingência SCAN';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->tpEmis == 4) {
                                    echo '4 - Contingência DPEC';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->tpEmis == 5) {
                                    echo '5 - Contingência FS-DA';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($nfe->ide->finNFe == 1) {
                                    echo '1 - Normal';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->finNFe == 2) {
                                    echo '2 - Complementar';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->finNFe == 3) {
                                    echo '3 - de Ajuste';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Natureza da Operação</td>
                            <td>Tipo da Operação</td>
                            <td>Forma de Pagamento</td>
                            <td>Digest Value da NF-e</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="active"><?= $nfe->ide->natOp ?></td>
                            <td class="active">
                                <?php
                                if ($nfe->ide->tpNF == 0) {
                                    echo '0 - Entrada';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->tpNF == 1) {
                                    echo '1 - Saída';
                                }
                                ?>
                            </td>
                            <td class="active">
                                <?php
                                if ($nfe->ide->indPag == 0) {
                                    echo '0 - à Vista';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->indPag == 1) {
                                    echo '1 - à Prazo';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->indPag == 2) {
                                    echo '2 - Outros';
                                }
                                ?>
                            </td>
                            <td class="active"><?= $nfe->xml->protNFe->infProt->digVal ?></td>
                        </tr>
                    <tbody>
                </table>
            </div>  FIM (NFe) 

            <div role="tabpanel" class="tab-pane" id="emitente">  EMITENTE 
                <table class="table table-striped">
                    <thead>
                        <tr class="danger">
                            <td colspan="2" class="text-uppercase info">Dados do Emitente</td>
                        </tr>
                        <tr class="legends">
                            <td width="50%">Nome / Razão Social</td>
                            <td>Nome Fantasia</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->xNome ?></td>
                            <td><?= $nfe->emit->xFant ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>CNPJ</td>
                            <td>Endereço</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?></td>
                            <td><?= $nfe->emit->enderEmit->xLgr . ", " . $nfe->emit->enderEmit->nro . " " . $nfe->emit->enderEmit->xCpl ?> </td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Bairro / Distrito</td>
                            <td>CEP</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->enderEmit->xBairro ?></td>
                            <td><?= mascara_string("#####-###", $nfe->emit->enderEmit->CEP) ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Município</td>
                            <td>Telefone</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->enderEmit->xMun ?></td>
                            <td><?= (strlen($nfe->dest->enderEmit->fone) >= 8) ? mascara_stringTel($nfe->dest->enderEmit->fone) : mascara_string("####-####", $nfe->dest->enderEmit->fone); ?></td>
                        </tr>
                    </tbody>
                    <thead>                                                                                                        
                        <tr class="legends">
                            <td>UF</td>
                            <td>País</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->enderEmit->UF ?></td>
                            <td><?= $nfe->emit->enderEmit->xPais ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Inscrição Estadual</td>
                            <td>Inscrição Estadual do Subistituto Tributário</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->IE ?></td>
                            <td> - </td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Inscrição Municipal</td>
                            <td>Inscrição da Ocorrência do Fato Gerador do ICMS</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->IM ?></td>
                            <td><?= $nfe->ide->cMunFG ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>CNAE Fiscal</td>
                            <td>Código de Regime Tributário</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->CNAE ?></td>
                            <td><?php
                                if ($nfe->emit->CRT == 1) {
                                    echo '1 - Simples Nacional';
                                }
                                ?>
                                <?php
                                if ($nfe->emit->CRT == 2) {
                                    echo '2 - Simples Nacional - excesso de sublimite de receita bruta';
                                }
                                ?>
                                <?php
                                if ($nfe->emit->CRT == 3) {
                                    echo '3 - Regime Normal';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>   ** FIM ** 

            <div role="tabpanel" class="tab-pane" id="destinatario">  DESTINATRIO 
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td class="danger" colspan="3"> Dados do Destinatário</td>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="legends">
                            <td>Nome / Razão Social</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3"><?= $nfe->dest->xNome ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td width="50%">
                                <?php
                                if ($nfe->dest->CPF) {
                                    echo 'CPF';
                                }
                                ?>
                                <?php
                                if ($nfe->dest->CNPJ) {
                                    echo 'CNPJ';
                                }
                                ?>
                            <td colspan="2">Endereço</td>
                        </tr>
                    </thead>    
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if (isset($nfe->dest->CPF)) {
                                    echo mascara_string("###.###.###-##", $nfe->dest->CPF);
                                } else {
                                    echo mascara_string("##.###.###/####-##", $nfe->dest->CNPJ);
                                }
                                ?>
                            <td colspan="2"><?= $nfe->dest->enderDest->xLgr . ", " . $nfe->dest->enderDest->nro ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Bairro / Distrito</td>
                            <td></td>
                            <td>CEP</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->dest->enderDest->xBairro ?></td>
                            <td></td>
                            <td><?= mascara_string("#####-###", $nfe->dest->enderDest->CEP) ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Município</td>
                            <td></td>
                            <td>Telefone</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->dest->enderDest->cMun . " - " . $nfe->dest->enderDest->xMun ?></td>
                            <td></td>
                            <td><?= (strlen($nfe->dest->enderDest->fone) >= 10) ? mascara_stringTel($nfe->dest->enderDest->fone) : mascara_string("####-####", $nfe->dest->enderDest->fone); ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>UF</td>
                            <td></td>
                            <td>País</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->dest->enderDest->UF ?></td>
                            <td></td>
                            <td><?= $nfe->dest->enderDest->cPais . " - " . $nfe->dest->enderDest->xPais ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Indicador IE</td>
                            <td>Inscrição Estadual</td>
                            <td>Inscrição SUFRAMA</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->dest->enderDest->D ?></td>
                            <td><?= $nfe->dest->enderDest->IE ?></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>IM</td>
                            <td colspan="2">e-Mail</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td colspan="3" class="danger text-center">Local de Entrega</td>
                        </tr>
                        <tr class="legends">
                            <td>CPF</td>
                            <td>Logradouro</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Bairro</td>
                            <td>Município</td>
                            <td>UF</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>  ** FIM ** 
            <div role="tabpanel" class="tab-pane" id="prodservico">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-info danger">Dados dos Produtos e/ou Serviços</td>
                        </tr>
                        <tr>
                            <td>Item</td>
                            <td>Descrição</td>
                            <td>Qtd</td>
                            <td>Unid</td>
                            <td>Valor(R$)</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nfe->det as $det) { ?>
                            <tr>
                                <td class="text text-right"><?= $det->attributes()->nItem ?></td>
                                <td><?= utf8_encode($det->prod->xProd) ?></td>
                                <td class="text text-right"><?= $det->prod->qCom ?></td>
                                <td class="text text-center"><?= $det->prod->uCom ?></td>
                                <td class="text text-right"><?= number_format((float) $det->prod->vProd, 2, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>   ** FIM ** 

            <div role="tabpanel" class="tab-pane" id="totais">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-info danger">Totais</td>
                        </tr>
                        <tr class="legends">
                            <td>Base de Cálculo ICMS</td>
                            <td>Valor do ICMS</td>
                            <td>Valor do ICMS Desonerado</td>
                            <td>Base de Cálculo ICMS ST</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vBC, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vICMS, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->v, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vST, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Valor ICMS Substituição</td>
                            <td>Valor Total dos Produtos</td>
                            <td>Valor do Frete</td>
                            <td>Valor do Seguro</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $nfe->total->ICMSTot->v, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vProd, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vFrete, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vSeg, 2, ',', '.'); ?></td>
                        </tr>                            
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Outras Despesas Acessórias</td>
                            <td>Valor Total do IPI</td>
                            <td>Valor Total da NFe</td>
                            <td>Valor Total dos Descontos</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vOutro, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vIPI, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vNF, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vDesc, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Valor Total do II</td>
                            <td>Valor do PIS</td>
                            <td>Valor da COFINS</td>
                            <td>Valor Aproximado dos Tributos</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vII, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vPIS, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->vCOFINS, 2, ',', '.'); ?></td>
                            <td><?= number_format((float) $nfe->total->ICMSTot->v, 2, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>   ** FIM ** 
            <div role="tabpanel" class="tab-pane" id="transporte">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td class="text-info danger">Dados do Transporte</td>
                        </tr>
                        <tr class="legends">
                            <td>Modalidade</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if ($nfe->transp->modFrete == 0) {
                                    echo '0 - Por Conta do Emitente';
                                }
                                ?>
                                <?php
                                if ($nfe->transp->modFrete == 1) {
                                    echo '1 - Por Conta do Destinatário';
                                }
                                ?>
                                <?php
                                if ($nfe->transp->modFrete == 2) {
                                    echo '2 - Por Conta de Terceiros';
                                }
                                ?>
                                <?php
                                if ($nfe->transp->modFrete == 9) {
                                    echo '9 - Sem Frete';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="cobranca">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-info danger"></td>
                        </tr>
                    <td></td>
                    </thead>
                    <tbody>
                    <td></td>
                    </tbody>
                </table>
            </div>   ** FIM ** 

            <div role="tabpanel" class="tab-pane" id="informacao">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-info danger">Informações Adicionais</td>
                        </tr>
                        <tr class="legends">
                            <td>Formato de Impressão DANFE</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if ($nfe->ide->tpImp == 1) {
                                    echo '1-Retrato';
                                }
                                ?>
                                <?php
                                if ($nfe->ide->tpImp == 2) {
                                    echo '2-Paisagem';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Informações Adicionais de Interesse do Fisco</td>
                        </tr>                        
                    </thead>
                    <tbody>
                        <tr><td><?= $nfe->infAdic->infAdFisco ?></td></tr>
                        <tr><td><?= $nfe->infAdic->infCpl ?></td></tr>
                    </tbody>
                </table>
            </div>   ** FIM ** -->

        </div>
    </div>
    <input type="hidden" name="cnpj-nfe" id="cnpj-nfe" value="<?= mascara_string("##.###.###/####-##", $nfe->emit->CNPJ) ?>">
    <?php
    exit();
}

// SALVAR XML NO BANCO DE DADOS ------------------------------------------------
if (isset($_REQUEST['salvar']) && $_REQUEST['salvar'] == 'Salvar') {

    $array = $nfe->nfe_xml_to_array();
   
    $resp = $nfe->salvarNFe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array);

    if ($resp['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal Salva com Sucesso.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp['msg'] ?>
        </div>
        <?php
    }
}

if (isset($_REQUEST['conferirPedido']) && $_REQUEST['conferirPedido'] == 'Conferir com Pedido') {

    $array = $confereNFe->nfe_xml_to_array();
   
    $resp = $confereNFe->salvarNFe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array);

    if ($resp['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal Salva com Sucesso.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp['msg'] ?>
        </div>
        <?php
    }
}

// SALVAR CADASTRO MANUAL NO BANCO DE DADOS ------------------------------------
if (isset($_REQUEST['cadastro-salvar']) && $_REQUEST['cadastro-salvar'] == 'Salvar') {
   
    $prestador = $nfe->consultaPrestador($_REQUEST['projeto'],$_REQUEST['prestador']);
    
    $array = array( // array com os dados da nota inseridos pelo usuário
        'id_regiao' => $_REQUEST['regiao'],
        'id_projeto' => $_REQUEST['projeto'],
        'Id' => $_REQUEST['chaveacesso'],
        'nNF' => $_REQUEST['numeronf'],
//        'emit_CNPJ' => $_REQUEST['cnpjcpf'],
        'emit_CNPJ' => str_replace('/', '', str_replace('-', '', str_replace('.', '',$prestador['c_cnpj']))),
        'dEmi' => converteData($_REQUEST['dt_emissao_nf']),
        'natOp' => $_REQUEST['cfop'],
        'vNF' => 0
    );
    
    // recupera todos os itens para o array
    for ($i = 0; $i < count($_REQUEST['id_prod']); $i++) {
        $array['det'][$i] = array(
            'nItem' => $i+1,
            'vUnCom' => str_replace(',', '.', str_replace('.', '', $_REQUEST['vUnCom'][$i])),
            'qCom' => str_replace(',', '.', str_replace('.', '', $_REQUEST['qCom'][$i])),
            'id_prod' => $_REQUEST['id_prod'][$i],
            'cProd' => $_REQUEST['cProd'][$i],
            'nLote' => $_REQUEST['nLote'][$i],
            'dVal' => converteData($_REQUEST['dVal'][$i]),
            'vProd' => str_replace(',', '.', str_replace('.', '', $_REQUEST['vProd'][$i])),
            'id_prestador' => $_REQUEST['id_prestador']
        );
        $array['vNF'] += str_replace(',', '.', str_replace('.', '', $_REQUEST['vProd'][$i]));
    }

    $resp = $nfe->salvarNFe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array,TRUE);

    if ($resp['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal Salva com Sucesso.</p>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp['msg'] ?>
        </div>
        <?php
    }
}
