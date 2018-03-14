<?php
header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFeClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/global.php");

$nfe = new NFe();
$pedidos = new pedidosClass();

// importar XML ---------------------------------------------------------------- 
if (isset($_REQUEST['visualiza']) && $_REQUEST['visualiza'] == 'Visualizar') {
    $dados = array(
//    'id_regiao' => $_REQUEST['regiao'], 
        'id_projeto' => $_REQUEST['projeto']
    );

    $lista = $nfe->consultaNFe($dados);

    if (count($lista) > 0) {
        ?>
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr class="text-success">
                    <th class="text-center" style="width: 5%;">NF</th>
                    <th style="width: 46%;">Emitente</th>
                    <th style="width: 15%;">CNPJ/CPF</th>
                    <th style="width: 15%;">Total</th>
                    <!--<th style="width: 6%;"></th>-->
                    <th style="width: 7%;"></th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $value) { ?>
                    <tr id="tr-<?= $value['id_nfe'] ?>">
                        <td class="text-center"><?= $value['nNF'] ?></td>
                        <td><?= $value['emit_xNome'] ?>&emsp;</td>
                        <td><?= $value['emit_CNPJ'] ?>&emsp;</td>
                        <td>R$<span class="pull-right"><?= number_format($value['vNF'], 2, ",", '.') ?></span>&emsp;</td>
                        <!--<td>&emsp;</td>-->
                        <td class="text-center">
                            <a href="#" class="btn btn-info btn-xs nfe-detalhes" data-id="<?= $value['id_nfe'] ?>"><i class="fa fa-external-link-square"></i> Detalhar</a>
                        </td>
                        <td class="text-center">
                            <a href="#" class="btn btn-danger btn-xs nfe-cancelar" data-id="<?= $value['id_nfe'] ?>"><i class="fa fa-times"></i> Cancelar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else {
        ?>
        <div class="alert alert-dismissable alert-warning">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <h4>Atenção!</h4>
            <p>Não há NFe para esse projeto.</p>
        </div>
        <?php
    }
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'Detalhes') {
    $dados = array(
        'id_regiao' => $_REQUEST['regiao'],
        'id_projeto' => $_REQUEST['projeto'],
        'id_nfe' => $_REQUEST['id_nfe']
    );

    $lista = $nfe->consultaNFe($dados, TRUE);
    $lista = $lista[key($lista)]; // remove o indice 
    ?>

    <table class="table table-bordered table-striped table-bordered">
        <h4>Dados Gerais</h4> 
        <tr class="success">
            <th>Chave de acesso</th>
            <th class="text-center">Número</th>
            <th class="text-center">Versão XML</th>
        </tr>
        <tr>
            <td><?= mascara_string("#### #### #### #### #### #### #### #### #### #### ####", $lista['Id']) ?>&emsp;</td>
            <td class="text-center"><?= $lista['nNF'] ?>&emsp;</td>
            <td class="text-center"><?= $lista['versao'] ?>&emsp;</td>
        </tr> 
    </table>
    <div role="tabpanel">
        <ul class="margim nav nav-pills" role="tablist">
            <li role="presentation" class="active"><a href="#NFe1" aria-controls="NFe1" role="tab" data-toggle="tab" class="estoque">NF-e</a></li>
            <li role="presentation"><a href="#emitente1" aria-controls="emitente1" role="tab" data-toggle="tab" class="estoque">Emitente</a></li>
            <li role="presentation"><a href="#destinatario1" aria-controls="destinatario1" role="tab" data-toggle="tab" class="estoque">Destinatário</a></li>
            <li role="presentation"><a href="#prodservico1" aria-controls="prodservico1" role="tab" data-toggle="tab" class="estoque">Produtos e Serviços</a></li>
            <li role="presentation"><a href="#totais1" aria-controls="totais1" role="tab" data-toggle="tab" class="estoque">Totais</a></li>
            <li role="presentation"><a href="#transporte1" aria-controls="transporte1" role="tab" data-toggle="tab" class="estoque">Transporte</a></li>
            <li role="presentation"><a href="#cobranca1" aria-controls="cobranca1" role="tab" data-toggle="tab" class="estoque">Cobrança</a></li>
            <li role="presentation"><a href="#informacao1" aria-controls="informacao1" role="tab" data-toggle="tab" class="estoque">Informações Adicionais</a></li>
        </ul>
        <hr>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="NFe1"> <!-- NFe -->

                <table cellpadding="20" class="table table-bordered table-striped"> <!-- dados da NFe -->
                    <thead>
                    <th colspan="6" class="text-uppercase success">Dados da NF-e</th>
                    <tr class="legends">
                        <th class="text-center">Modelo</th>
                        <th class="text-center">Série</th>
                        <th class="text-center">Número</th>
                        <th class="text-center">Data de Emissão</th>
                        <th class="text-center">Data de Saída/Entrada</th>
                        <th class="text-right">Valor Total da Nota Fiscal</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="active text-center"><?= $lista['mod'] ?>&emsp;</td>
                            <td class="active text-center"><?= $lista['serie'] ?>&emsp;</td>
                            <td class="active text-center"><?= $lista['nNF'] ?>&emsp;</td>
                            <td class="active text-center"><?php echo implode('/', array_reverse(explode('-', $lista['dEmi']))); ?>&emsp;</td>
                            <td class="active text-center"><?php echo implode('/', array_reverse(explode('-', $lista['dSaiEnt']))); ?>&emsp;</td>
                            <td class="active text-right"><?= number_format((float) $lista['vNF'], 2, ',', '.'); ?>&emsp;</td>
                        </tr>
                    <tbody>
                </table>
                <table class="table table-bordered table-striped"> <!-- dados da Emitente -->
                    <thead>
                        <tr><td colspan="4" class="text-uppercase success">Emitente&emsp;</td></tr>
                        <tr class="legends">
                            <th>CNPJ</th>
                            <th>Nome / Razão Social</th>
                            <th>Inscrição Estadual</th>
                            <th class="text-center">UF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $lista['emit_CNPJ'] //mascara_string('##.###.###/####-##', $lista['emit_CNPJ'])      ?>&emsp;</td>
                            <td><?= $lista['emit_xNome'] ?>&emsp;</td>
                            <td><?= $lista['IE'] ?>&emsp;</td>
                            <td class="active text-center"><?= $lista['emit_UF'] ?>&emsp;</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-striped" style="table-layout: fixed"> <!-- dados do Destinatário-->
                    <thead>
                        <tr><td colspan="4" class="text-uppercase success">Destinatário&emsp;</td></tr>
                        <tr class="legends">
                            <th>
                                <?php
                                if ($lista['dest_CPF']) {
                                    echo 'CPF';
                                }
                                ?>
                                <?php
                                if ($lista['dest_CNPJ']) {
                                    echo 'CNPJ';
                                }
                                ?>
                            </th>
                            <th>Nome / Razão Social&emsp;</td>
                            <th class="text-center">Inscrição Estadual&emsp;</td>
                            <th class="text-center">UF&emsp;</td>              
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if (isset($nfe->dest->CPF)) {
                                    echo $lista['dest_CPF'];
                                } else {
                                    echo $lista['dest_CNPJ'];
                                }
                                ?>
                                &emsp;</td>
                            <td class="active text-center"><?= $lista['dest_xNome'] ?>&emsp;</td>
                            <td class="active text-center"><?= $nfe->dest->IE ?>&emsp;</td>
                            <td class="active text-center"><?= $nfe->dest->enderDest->UF ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <th colspan="2">Destino da operação</th>
                            <td>Consumidor final&emsp;</td>
                            <td>Presença do Comprador&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2">&emsp;</td>
                            <td class="active">&emsp;</td>
                            <td class="active">&emsp;</td>
                        </tr>
                    </tbody>
                </table> 
                <table class="table table-bordered table-striped"> <!-- dados da Emissão -->
                    <thead>
                        <tr>
                            <td colspan="4" class="text-uppercase success">Emissão&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>Processo&emsp;</td>
                            <td>Versão do Processo&emsp;</td>
                            <td>Tipo de Emissão&emsp;</td>
                            <td>Finalidade&emsp;</td>
                        </tr>
                    </thead>
                    <tbody> 
                        <tr>
                            <td>
                                <?php
                                if ($lista['procEmi'] == 0) {
                                    echo '0 - emissão de NF-e com aplicativo do contribuinte';
                                }
                                ?>
                                <?php
                                if ($lista['procEmi'] == 1) {
                                    echo '1 - emissão de NF-e avulsa pelo Fisco';
                                }
                                ?>
                                <?php
                                if ($lista['procEmi'] == 2) {
                                    echo '2 - emissão de NF-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco';
                                }
                                ?>
                                <?php
                                if ($lista['procEmi'] == 3) {
                                    echo '3 - emissão NF-e pelo contribuinte com aplicativo fornecido pelo Fisco';
                                }
                                ?> 
                                &emsp;</td>
                            <td class="text-center">
                                <?= $nfe->ide->verProc ?>
                                &emsp;</td>
                            <td>
                                <?php
                                if ($lista['tpEmis'] == 1) {
                                    echo '1 - Normal';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 2) {
                                    echo '2 - Contingência FS';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 3) {
                                    echo '3 - Contingência SCAN';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 4) {
                                    echo '4 - Contingência DPEC';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 5) {
                                    echo '5 - Contingência FS-DA';
                                }
                                ?>
                                &emsp;</td>
                            <td>
                                <?php
                                if ($lista['tpEmis'] == 1) {
                                    echo '1 - Normal';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 2) {
                                    echo '2 - Complementar';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 3) {
                                    echo '3 - de Ajuste';
                                }
                                ?>
                                &emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Natureza da Operação&emsp;</td>
                            <td>Tipo da Operação&emsp;</td>
                            <td>Forma de Pagamento&emsp;</td>
                            <td>Digest Value da NF-e&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="active">
                                <?= $nfe->ide->natOp ?>
                                &emsp;</td>
                            <td class="active">
                                <?php
                                if ($lsita['tpNF'] == 0) {
                                    echo '0 - Entrada';
                                } else {
                                    echo '1 - Saída';
                                }
                                ?>
                                &emsp;</td>
                            <td class="active">
                                <?php
                                if ($lista['indPag'] == 0) {
                                    echo '0 - à Vista';
                                } if ($lista['indPag'] == 1) {
                                    echo '1 - à Prazo';
                                } if ($lista['indPag'] == 2) {
                                    echo '2 - Outros';
                                }
                                ?>
                                &emsp;</td>
                            <td class="active"><?= $nfe->xml->protNFe->infProt->digVal ?>&emsp;</td>
                        </tr>
                    <tbody>
                </table>
            </div> <!-- FIM (NFe) -->

            <div role="tabpanel" class="tab-pane" id="emitente1"> <!-- EMITENTE -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="success">
                            <td colspan="2" class="text-uppercase success">Dados do Emitente&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td width="50%">Nome / Razão Social&emsp;</td>
                            <td>Nome Fantasia&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $lista['emit_xNome'] ?>&emsp;</td>
                            <td><?= $lista['emit_xFant'] ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>CNPJ&emsp;</td>
                            <td>Endereço&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $lista['emit_CNPJ'] ?>&emsp;</td>
                            <td><?= $nfe->emit->enderEmit->xLgr . ", " . $nfe->emit->enderEmit->nro . " " . $nfe->emit->enderEmit->xCpl ?> &emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Bairro / Distrito&emsp;</td>
                            <td>CEP&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->enderEmit->xBairro ?>&emsp;</td>
                            <td><?= $nfe->emit->enderEmit->CEP ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Município&emsp;</td>
                            <td>Telefone&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->enderEmit->xMun ?>&emsp;</td>
                            <td><?= (strlen($nfe->dest->enderEmit->fone) >= 10) ? mascara_stringTel($nfe->dest->enderEmit->fone) : mascara_string("####-####", $nfe->dest->enderEmit->fone); ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>                                                    
                        <tr class="legends">
                            <td>UF&emsp;</td>
                            <td>País&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->enderEmit->UF ?>&emsp;</td>
                            <td><?= $nfe->emit->enderEmit->xPais ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <td>Inscrição Estadual&emsp;</td>
                            <td>Inscrição Estadual do Subistituto Tributário&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->IE ?>&emsp;</td>
                            <td> - &emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <td>Inscrição Municipal&emsp;</td>
                            <td>Inscrição da Ocorrência do Fato Gerador do ICMS&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->IM ?>&emsp;</td>
                            <td><?= $nfe->ide->cMunFG ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>CNAE Fiscal&emsp;</td>
                            <td>Código de Regime Tributário&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->emit->CNAE ?>&emsp;</td>
                            <td>
                                <?php
                                if ($nfe->emit->CRT == 1) {
                                    echo '1 - Simples Nacional';
                                } if ($nfe->emit->CRT == 2) {
                                    echo '2 - Simples Nacional - excesso de sublimite de receita bruta';
                                } if ($nfe->emit->CRT == 3) {
                                    echo '3 - Regime Normal';
                                }
                                ?>
                                &emsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- ** FIM ** -->

            <div role="tabpanel" class="tab-pane" id="destinatario1"> <!-- DESTINATRIO -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td class="success" colspan="3"> Dados do Destinatário&emsp;</td>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="legends">
                            <td>Nome / Razão Social&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3"><?= $lista['dest_xNome'] ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td width="50%">
                                <?php
                                if ($nfe->dest->CPF) {
                                    echo 'CPF';
                                } if ($lista['dest_cnpj']) {
                                    echo 'CNPJ';
                                }
                                ?>
                            <td colspan="2">Endereço&emsp;</td>
                        </tr>
                    </thead>  
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if (isset($lista['dest_cnpj'])) {
                                    echo $lista['dest_cnpj'];
                                } else {
//                  echo substr($nfe->dest->CNPJ, 0, 2) . "." . substr($nfe->dest->CNPJ, 2, 3) . "." . substr($nfe->dest->CNPJ, 5, 3) . "/" . substr($nfe->dest->CNPJ, 8, 4) . "-" . substr($nfe->dest->CNPJ, 12, 2);
                                }
                                ?>
                            <td colspan="2"><?= $lista['dest_nro'] ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Bairro / Distrito&emsp;</td>
                            <td>&emsp;</td>
                            <td>CEP&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $lista['dest_xBairro'] ?>&emsp;</td>
                            <td>&emsp;</td>
                            <td><?= $lista['dest_CEP'] ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Município&emsp;</td>
                            <td>&emsp;</td>
                            <td>Telefone&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $lista['dest_cMun'] . " - " . $lista['dest_xMun'] ?>&emsp;</td>
                            <td>&emsp;</td>
                            <td><?= (strlen($lista['dest_fone']) >= 10) ? mascara_stringTel($lista['dest_fone']) : mascara_string("####-####", $lista['dest_fone']); ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>UF&emsp;</td>
                            <td>&emsp;</td>
                            <td>País&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $lista['dest_UF'] ?>&emsp;</td>
                            <td>&emsp;</td>
                            <td><?= $lista['dest_cPais'] . " - " . $lista['dest_xPais'] ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Indicador IE&emsp;</td>
                            <td>Inscrição Estadual&emsp;</td>
                            <td>Inscrição SUFRAMA&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfe->dest->enderDest->D ?>&emsp;</td>
                            <td><?= $nfe->dest->enderDest->IE ?>&emsp;</td>
                            <td>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>IM&emsp;</td>
                            <td colspan="2">e-Mail&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>&emsp;</td>
                            <td colspan="2">&emsp;</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td colspan="3" class="success text-center">Local de Entrega&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>CPF&emsp;</td>
                            <td colspan="2">Logradouro&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Bairro&emsp;</td>
                            <td>Município&emsp;</td>
                            <td>UF&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3">&emsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- ** FIM ** -->
            <div role="tabpanel" class="tab-pane" id="prodservico1">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-success success">Dados dos Produtos e/ou Serviços&emsp;</td>
                        </tr>
                        <tr>
                            <td>Item&emsp;</td>
                            <td>Descrição&emsp;</td>
                            <td>Qtd&emsp;</td>
                            <td>Unid&emsp;</td>
                            <td>Valor(R$)&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista['itens'] as $det) { ?>            
                            <tr>
                                <td class="text text-right"><?= $det['nItem'] ?>&emsp;</td>
                                <td><?= $det['xProd'] ?>&emsp;</td>
                                <td class="text text-right"><?= $det['qCom'] ?>&emsp;</td>
                                <td class="text text-center"><?= $det['uCom'] ?>&emsp;</td>
                                <td class="text text-right"><?= number_format((float) $det['vProd'], 2, ',', '.'); ?>&emsp;</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div> <!-- ** FIM ** -->

            <div role="tabpanel" class="tab-pane" id="totais1">
                <table class="table table-bordered table-striped text text-center">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-success success">Totais&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>Base de Cálculo ICMS&emsp;</td>
                            <td>Valor do ICMS&emsp;</td>
                            <td>Valor do ICMS Desonerado&emsp;</td>
                            <td>Base de Cálculo ICMS ST&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $lista['vBC'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vICMS'], 2, ',', '.'); ?>&emsp;</td>
                            <td>&emsp;</td>
                            <td><?= number_format((float) $lista['vST'], 2, ',', '.'); ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Valor ICMS Substituição&emsp;</td>
                            <td>Valor Total dos Produtos&emsp;</td>
                            <td>Valor do Frete&emsp;</td>
                            <td>Valor do Seguro&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>&emsp;</td>
                            <td><?= number_format((float) $lista['vProd'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vFrete'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vSeg'], 2, ',', '.'); ?>&emsp;</td>
                        </tr>              
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Outras Despesas Acessórias&emsp;</td>
                            <td>Valor Total do IPI&emsp;</td>
                            <td>Valor Total da NFe&emsp;</td>
                            <td>Valor Total dos Descontos&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $lista['vOutro'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vIPI'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vNF'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vDesc'], 2, ',', '.'); ?>&emsp;</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr class="legends">
                            <td>Valor Total do II&emsp;</td>
                            <td>Valor do PIS&emsp;</td>
                            <td>Valor da COFINS&emsp;</td>
                            <td>Valor Aproximado dos Tributos&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format((float) $lista['vII'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vPIS'], 2, ',', '.'); ?>&emsp;</td>
                            <td><?= number_format((float) $lista['vCOFINS'], 2, ',', '.'); ?>&emsp;</td>
                            <td>&emsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- ** FIM ** -->
            <div role="tabpanel" class="tab-pane" id="transporte1">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td class="text-success success">Dados do Transporte&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>Modalidade&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                if ($lista['modFrete'] == 0) {
                                    echo '0 - Por Conta do Emitente';
                                } if ($lista['modFrete'] == 1) {
                                    echo '1 - Por Conta do Destinatário';
                                } if ($lista['modFrete'] == 2) {
                                    echo '2 - Por Conta de Terceiros';
                                } if ($lista['modFrete'] == 9) {
                                    echo '9 - Sem Frete';
                                }
                                ?>
                                &emsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div role="tabpanel" class="tab-pane" id="cobranca1">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-success success">&emsp;</td>
                        </tr>
                    <td>&emsp;</td>
                    </thead>
                    <tbody>
                    <td>&emsp;</td>
                    </tbody>
                </table>
            </div> <!-- ** FIM ** -->
            <div role="tabpanel" class="tab-pane" id="informacao1">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <td colspan="5" class="text-success success">Informações Adicionais&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>Formato de Impressão DANFE&emsp;</td>
                        </tr>
                    </thead>
                    <tbody>
                    <td>&emsp;</td>
                    </tbody>
                </table>
            </div> <!-- ** FIM ** -->

        </div>
    </div>  
    <?php
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cancelar') {

    $id_nfe = $_REQUEST['id'];

    // consulta id_pedido da nfe
    // consulta itens da nota
    $nfe_dados = $nfe->consultaNFe(array('id_nfe' => $id_nfe), TRUE);
    $nfe_dados = $nfe_dados[$id_nfe]; // sim isso é gamby
    
    // loop para descontar valor de cada item do pedido
    foreach ($nfe_dados['itens'] as $itens) {
        $pedidos->decrementaQtdRecebida($nfe_dados['id_pedido'], $itens['id_produto'], $itens['qCom']);
    }

    // atualiza status do pedido
    $pedidos->atualizaStatus($nfe_dados['id_pedido']);

    // excluir
    $status = $nfe->cancelar_NFe($_REQUEST['id']);

    echo json_encode(array('status' => $status));
}