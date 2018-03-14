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
            <button type="button" class="close" data-dismiss="alert">�</button>
            <h4>Aten��o!</h4>
            <p>N�o h� NFe para esse projeto.</p>
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
            <th class="text-center">N�mero</th>
            <th class="text-center">Vers�o XML</th>
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
            <li role="presentation"><a href="#destinatario1" aria-controls="destinatario1" role="tab" data-toggle="tab" class="estoque">Destinat�rio</a></li>
            <li role="presentation"><a href="#prodservico1" aria-controls="prodservico1" role="tab" data-toggle="tab" class="estoque">Produtos e Servi�os</a></li>
            <li role="presentation"><a href="#totais1" aria-controls="totais1" role="tab" data-toggle="tab" class="estoque">Totais</a></li>
            <li role="presentation"><a href="#transporte1" aria-controls="transporte1" role="tab" data-toggle="tab" class="estoque">Transporte</a></li>
            <li role="presentation"><a href="#cobranca1" aria-controls="cobranca1" role="tab" data-toggle="tab" class="estoque">Cobran�a</a></li>
            <li role="presentation"><a href="#informacao1" aria-controls="informacao1" role="tab" data-toggle="tab" class="estoque">Informa��es Adicionais</a></li>
        </ul>
        <hr>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="NFe1"> <!-- NFe -->

                <table cellpadding="20" class="table table-bordered table-striped"> <!-- dados da NFe -->
                    <thead>
                    <th colspan="6" class="text-uppercase success">Dados da NF-e</th>
                    <tr class="legends">
                        <th class="text-center">Modelo</th>
                        <th class="text-center">S�rie</th>
                        <th class="text-center">N�mero</th>
                        <th class="text-center">Data de Emiss�o</th>
                        <th class="text-center">Data de Sa�da/Entrada</th>
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
                            <th>Nome / Raz�o Social</th>
                            <th>Inscri��o Estadual</th>
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
                <table class="table table-bordered table-striped" style="table-layout: fixed"> <!-- dados do Destinat�rio-->
                    <thead>
                        <tr><td colspan="4" class="text-uppercase success">Destinat�rio&emsp;</td></tr>
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
                            <th>Nome / Raz�o Social&emsp;</td>
                            <th class="text-center">Inscri��o Estadual&emsp;</td>
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
                            <th colspan="2">Destino da opera��o</th>
                            <td>Consumidor final&emsp;</td>
                            <td>Presen�a do Comprador&emsp;</td>
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
                <table class="table table-bordered table-striped"> <!-- dados da Emiss�o -->
                    <thead>
                        <tr>
                            <td colspan="4" class="text-uppercase success">Emiss�o&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>Processo&emsp;</td>
                            <td>Vers�o do Processo&emsp;</td>
                            <td>Tipo de Emiss�o&emsp;</td>
                            <td>Finalidade&emsp;</td>
                        </tr>
                    </thead>
                    <tbody> 
                        <tr>
                            <td>
                                <?php
                                if ($lista['procEmi'] == 0) {
                                    echo '0 - emiss�o de NF-e com aplicativo do contribuinte';
                                }
                                ?>
                                <?php
                                if ($lista['procEmi'] == 1) {
                                    echo '1 - emiss�o de NF-e avulsa pelo Fisco';
                                }
                                ?>
                                <?php
                                if ($lista['procEmi'] == 2) {
                                    echo '2 - emiss�o de NF-e avulsa, pelo contribuinte com seu certificado digital, atrav�s do site do Fisco';
                                }
                                ?>
                                <?php
                                if ($lista['procEmi'] == 3) {
                                    echo '3 - emiss�o NF-e pelo contribuinte com aplicativo fornecido pelo Fisco';
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
                                    echo '2 - Conting�ncia FS';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 3) {
                                    echo '3 - Conting�ncia SCAN';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 4) {
                                    echo '4 - Conting�ncia DPEC';
                                }
                                ?>
                                <?php
                                if ($lista['tpEmis'] == 5) {
                                    echo '5 - Conting�ncia FS-DA';
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
                            <td>Natureza da Opera��o&emsp;</td>
                            <td>Tipo da Opera��o&emsp;</td>
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
                                    echo '1 - Sa�da';
                                }
                                ?>
                                &emsp;</td>
                            <td class="active">
                                <?php
                                if ($lista['indPag'] == 0) {
                                    echo '0 - � Vista';
                                } if ($lista['indPag'] == 1) {
                                    echo '1 - � Prazo';
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
                            <td width="50%">Nome / Raz�o Social&emsp;</td>
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
                            <td>Endere�o&emsp;</td>
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
                            <td>Munic�pio&emsp;</td>
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
                            <td>Pa�s&emsp;</td>
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
                            <td>Inscri��o Estadual&emsp;</td>
                            <td>Inscri��o Estadual do Subistituto Tribut�rio&emsp;</td>
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
                            <td>Inscri��o Municipal&emsp;</td>
                            <td>Inscri��o da Ocorr�ncia do Fato Gerador do ICMS&emsp;</td>
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
                            <td>C�digo de Regime Tribut�rio&emsp;</td>
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
                            <td class="success" colspan="3"> Dados do Destinat�rio&emsp;</td>
                        </tr>
                    </thead>
                    <thead>
                        <tr class="legends">
                            <td>Nome / Raz�o Social&emsp;</td>
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
                            <td colspan="2">Endere�o&emsp;</td>
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
                            <td>Munic�pio&emsp;</td>
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
                            <td>Pa�s&emsp;</td>
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
                            <td>Inscri��o Estadual&emsp;</td>
                            <td>Inscri��o SUFRAMA&emsp;</td>
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
                            <td>Munic�pio&emsp;</td>
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
                            <td colspan="5" class="text-success success">Dados dos Produtos e/ou Servi�os&emsp;</td>
                        </tr>
                        <tr>
                            <td>Item&emsp;</td>
                            <td>Descri��o&emsp;</td>
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
                            <td>Base de C�lculo ICMS&emsp;</td>
                            <td>Valor do ICMS&emsp;</td>
                            <td>Valor do ICMS Desonerado&emsp;</td>
                            <td>Base de C�lculo ICMS ST&emsp;</td>
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
                            <td>Valor ICMS Substitui��o&emsp;</td>
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
                            <td>Outras Despesas Acess�rias&emsp;</td>
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
                                    echo '1 - Por Conta do Destinat�rio';
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
                            <td colspan="5" class="text-success success">Informa��es Adicionais&emsp;</td>
                        </tr>
                        <tr class="legends">
                            <td>Formato de Impress�o DANFE&emsp;</td>
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
    $nfe_dados = $nfe_dados[$id_nfe]; // sim isso � gamby
    
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