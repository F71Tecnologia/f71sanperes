<?php
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFSeClass.php");
include("../../classes/NFSeSolicitacaoCorrecaoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$nfse = new NFSe(); // instancia obj nfe 
$nfse->load($_FILES['nfe']['tmp_name'], $_REQUEST['prefeitura']); // carrega xml para objeto
// consulta cnpj para ver se há projeto cadastrado
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'consultacnpjcpf') {
    $prestador = $nfse->consultarePrestador($_REQUEST['projeto'], NULL, $_REQUEST['cnpjcpf']);
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
    header('Content-Type: text/html; charset=iso-8859-1');
    if (isset($nfse->PrestadorServico)) {
        ?>
        <div class="row"> 
            <div class="text-light-gray text-sm col-lg-4">
                <label>Informações da Nota Fiscal de Serviço</label>
            </div>
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead class="text-danger text-sm  text-center">
                        <tr>
                            <td>Número</td>
                            <td>Data e Hora de Emissão</td>
                            <td>Código de Verificação</td>
                        </tr>
                    </thead>
                    <tbody class="text-semibold  text-center">
                        <tr>
                            <td><?= $nfse->InfNfse->Numero ?></td>
                            <td><?= implode('/', array_reverse(explode('-', substr($nfse->InfNfse->DataEmissao, 0, 10)))) . " " . substr($nfse->InfNfse->DataEmissao, 11, 8) ?></td>
                            <td><?= $nfse->InfNfse->CodigoVerificacao ?></td>
                        </tr> 
                    </tbody>    
                    <thead class="text-danger text-sm">
                        <tr>
                            <td style="width: 30%;">Nome Fantasia</td>
                            <td style="width: 50%;">Razao Social</td>
                            <td style="width: 20%;">CNPJ</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="active">
                            <td><?= to_iso_8859_1($nfse->PrestadorServico->NomeFantasia) ?></td>
                            <td><?= $nfse->PrestadorServico->RazaoSocial ?></td>
                            <td>
                                <?= mascara_string("##.###.###/####-##", $nfse->PrestadorServico->IdentificacaoPrestador->Cnpj) ?>
                                <input type="hidden" id="prestador_cnpj" name="prestador_cnpj" value="<?= $nfse->PrestadorServico->IdentificacaoPrestador->Cnpj ?>">
                                <input type="hidden" id="inscricao_mun" name="inscricao_mun" value="<?= $nfse->PrestadorServico->IdentificacaoPrestador->InscricaoMunicipal ?>">
                            </td>
                        </tr>
                    </tbody>
                </table> 
            </div>
        </div>
        <?php
    }
    if (isset($nfse->Tomador->IdentificacaoTomador)) {
        ?>
        <div class="row"> 
            <div class="text-light-gray text-sm col-lg-2">
                <label>Tomador do Serviço</label>
            </div>
            <div class="col-lg-12">
                <table class="table table-striped">
                    <thead class="text-danger text-sm">
                        <tr>
                            <td>Razão Social</td>
                            <td>CNPJ</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $nfse->Tomador->RazaoSocial ?></td>
                            <td>
                                <?= mascara_string("##.###.###/####-##", $nfse->Tomador->IdentificacaoTomador->CpfCnpj->Cnpj) ?>
                                <input type="hidden" name="projeto_cnpj" id="projeto_cnpj" value="<?= $nfse->Tomador->CpfCnpj->Cnpj ?>" class="onchange_cnpj" onchange="">
                            </td>
                            <td><?= $nfse->TomadorServico->InscricaoMunicipal ?></td>
                        </tr>
                        <tr>
                            <td colspan="3"><?= $nfse->TomadorServico->Endereco->Endereco . " - " . $nfse->TomadorServico->Endereco->Bairro ?> </td>
                        </tr>
                    </tbody>    
                </table>
            </div>
        </div>
        <?php
    }
    if (isset($nfse->Servico)) {
        ?>
        <div class="row">
            <div class="text-light-gray text-sm col-lg-2">
                <label>Serviço(s) Prestado(s)</label>
            </div>
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
                                <td>R$ <?= number_format((float) $valores->ValorCofins, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float) $valores->ValorCsll, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float) $valores->ValorInss, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float) $valores->ValorIr, 2, ',', '.') ?></td>
                                <td>R$ <?= number_format((float) $valores->ValorPis, 2, ',', '.') ?></td>
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
                <label> R$ <?= number_format((float) $valores->ValorServicos, 2, ',', '.') ?></label>
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
                            <td> </td>                        
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
                            <td><?= number_format((float) $valores->ValorDeducao, 2, ',', '.') ?></td>
                            <td><?= number_format((float) $valores->ValorDesconto, 2, ',', '.') ?></td>
                            <td><?= number_format((float) $valores->BaseCalculo, 2, ',', '.') ?></td>
                            <td><?= number_format((float) $valores->Aliquota, 2, '.', ',') * 100 ?></td>
                            <td><?= number_format((float) $valores->ValorIss, 2, ',', '.') ?></td>
                            <td><?= number_format((float) $valores->Credito, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-right">
                <label class="text-danger text-sm">Valor Líquido a Pagar</label>
                <label> R$ <?= number_format((float) $valores->ValorLiquidoNfse, 2, ',', '.') ?></label>
            </div>
        </div>
        <?php
    }
    exit();
}

// ACEITAR NFSe ÁPOS CONFIMAÇÃO DO SERVIÇO REALIZADO ------------------------------------------------
if (isset($_REQUEST['salvare']) && $_REQUEST['salvare'] == 'Aceitar NFs') {
    header('Content-Type: text/html; charset=iso-8859-1');
    $array = $nfse->nfse_xml_to_array();
    $resp1 = $nfse->salvarNFSe($_REQUEST['projeto_cnpj'], $_REQUEST['prestador_cnpj'], $array);
    if ($resp1['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal de Serviço Salva com Sucesso.</p>

        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção!</strong> <?= $resp1['msg'] ?>
        </div>
        <?php
    }
}

// DOWNLOAD DO ARQUIVO PDF DA NOTA FISCAL DE SERVIÇO ------------------------------------------------
if (isset($_REQUEST['anexar_pdf']) && $_REQUEST['anexar_pdf'] == 'Anexar') {
    header('Content-Type: text/html; charset=iso-8859-1');
    $array = $nfse->nfse_xml_to_array();
    $id_prestador = mysql_result(mysql_query("SELECT id_prestador, c_cnpj, id_projeto FROM prestadorservico WHERE REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'/',''),'-','') = '{$_REQUEST['prestador']}' AND id_projeto = '{$id_projeto}'"), 0);
    $id_projeto = mysql_result(mysql_query("SELECT id_projeto, cnpj, nome FROM projeto WHERE REPLACE(REPLACE(REPLACE(cnpj,'.',''),'/',''),'-','') = '{$_REQUEST['projeto_cnpj']}'"), 0);
    echo $id_prestador . " - " . $id_projeto . " - ";

    exit;
    $resp1 = $nfse->salvarNFSe($_REQUEST['projeto_cnpj'], $_REQUEST['prestador_cnpj'], $array);
    if ($resp1['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal de Serviço Salva com Sucesso.</p>

        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp1['msg'] ?>
        </div>
        <?php
    }
}

// VALIDAR NFs  ------------------------------------------------
if (isset($_REQUEST['validar']) && $_REQUEST['validar'] == 'Validar NFs') {
    header('Content-Type: text/html; charset=iso-8859-1');
    $array = $nfse->nfse_xml_to_array();
    $retorno1 = $nfse->salvarNFSe($_REQUEST['regiao'], $_REQUEST['projeto'], $_REQUEST['prestador'], $array);
    if ($retorno1['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal de Serviço Salva com Sucesso.</p>

        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção ...!</strong> <?= $resp1['msg'] ?>
        </div>
        <?php
    }
}

// SALVAR NOTA FISCAL DE SERVIÇO MANUALMENTE NO DB ------------------------------------
if (isset($_REQUEST['salvar-nfse-manual']) && $_REQUEST['salvar-nfse-manual'] == 'Salvar') {
    header('Content-Type: text/html; charset=iso-8859-1');
//    $arr_dt = explode('-', converteData($_REQUEST['competencia']));

    $nfse_arr = array(
        'id_regiao' => $_REQUEST['regiao'],
        'id_projeto' => $_REQUEST['projeto'],
//        'Numero' => $arr_dt[0] . str_pad($_REQUEST['numero'], 11, 0, STR_PAD_LEFT),
        'Numero' => str_pad($_REQUEST['numero'], 11, 0, STR_PAD_LEFT),
        'PrestadorServico' => $_REQUEST['prestador'],
        'InscricaoMunicipal' => $_REQUEST['inscricao_municipal'],
        'CodigoVerificacao' => strtoupper(str_replace('-', '', $_REQUEST['codigoverificacao'])),
//        'Cnpj' => $_REQUEST['cnpj'],
        'DataEmissao' => converteData($_REQUEST['emissao']),
        'NaturezaOperacao' => $_REQUEST['naturezanperacao'],
        'OptanteSimplesNacional' => $_REQUEST['optante'],
        'IncentivadorCultural' => $_REQUEST['incentivo'],
//        'Competencia' => converteData($_REQUEST['competencia']),
        'Competencia' => $_REQUEST['ano_competencia'] . '-' . $_REQUEST['mes_competencia'] . '-01',
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
        'Discriminacao' => utf8_decode($_REQUEST['discriminacao']),
        'CodigoTributacaoMunicipio' => $_REQUEST['CodigoTributacaoMunicipio'],
        'status' => 2
    );

    if (isset($_REQUEST['id_nfse'])) {
        $resp1 = $nfse->update($_REQUEST['id_nfse'], $nfse_arr);
    } else {
        $resp1 = $nfse->inserir($nfse_arr);
    }

    if ($resp1['status']) {
        ?>
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>Nota Fiscal de Serviço Salva com Sucesso.</p>
        </div>
        <?php
// upload do arquivo -------------------------------------------------------
        if (isset($_FILES['nfe_pdf_cm'])) {
            $array_anexo = array(
                'id_projeto' => $_REQUEST['projeto'],
//        'numero_nota' => $arr_dt[0] . str_pad($_REQUEST['numero'], 11, 0, STR_PAD_LEFT),
                'numero_nota' => str_pad($_REQUEST['numero'], 11, 0, STR_PAD_LEFT),
                'codigo_verificador' => strtoupper(str_replace('-', '', $_REQUEST['codigoverificacao'])),
                'id_prestador' => $_REQUEST['prestador'],
                'arquivo_pdf' => $_FILES['nfe_pdf_cm'],
                'id_nfse' => $resp1['id_nfse']
            );
            if ($_REQUEST['id_anexo']) {
                $array_anexo['id'] = $_REQUEST['id_anexo'];
            }
            $resp2 = $nfse->anexar($array_anexo);

            if ($resp2['status']) {
                ?>
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p>Arquivo PDF Anexado.</p>
                </div>
            <?php } else {
                ?>
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Atenção!</strong> Erro ao anexar.
                </div>
                <?php
            }
        }
        if (isset($_FILES['nfe_xml'])) {
            $array_anexo = array(
                'id_projeto' => $_REQUEST['projeto'],
//        'numero_nota' => $arr_dt[0] . str_pad($_REQUEST['numero'], 11, 0, STR_PAD_LEFT),
                'numero_nota' => str_pad($_REQUEST['numero'], 11, 0, STR_PAD_LEFT),
                'codigo_verificador' => strtoupper(str_replace('-', '', $_REQUEST['codigoverificacao'])),
                'id_prestador' => $_REQUEST['prestador'],
                'arquivo_pdf' => $_FILES['nfe_xml'],
                'id_nfse' => $resp1['id_nfse']
            );
            if ($_REQUEST['id_anexo']) {
                $array_anexo['id'] = $_REQUEST['id_anexo'];
            }
            $resp2 = $nfse->anexar($array_anexo);

            if ($resp2['status']) {
                ?>
                <div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p>Arquivo XML Anexado.</p>
                </div>
            <?php } else {
                ?>
                <div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Atenção!</strong> Erro ao anexar.
                </div>
                <?php
            }
        }
    } else {
        ?>
        <div class="alert alert-dismissable alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Atenção!</strong> <?= utf8_encode($resp1['msg']) ?>
        </div>
        <?php
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'cosulta_cod_servico') {
    header('Content-Type: application/json; charset=utf8');
    $cod = addslashes(str_replace('.', '', $_REQUEST['cod']));
    $query = "SELECT * FROM nfse_codigo_servico WHERE REPLACE(codigo,'.','') LIKE '%{$cod}%'";
    $result = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $sem_pontos = str_replace('.', '', $row['codigo']);
        $servicos['servicos'][] = utf8_encode("{$row['codigo']} :: {$row['descricao']} ($sem_pontos)");
    }
    echo json_encode($servicos);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'verifica_nfe_correcao') {
    header('Content-Type: application/json; charset=utf8');
    $objCorrecao = new NFSseSolicitacaoCorrecaoClass();
    $objCorrecao->setStatus(1);
    $objCorrecao->setIdRegiao($id_regiao);
    $array = $objCorrecao->listarJoinNFSe();
    foreach ($array as $i => $row) {

        foreach ($row as $key => $value) {
            $new_arr[$i][$key] = utf8_encode($value);
        }
    }
    if (count($array) > 0) {
        echo json_encode(array('status' => TRUE, 'lista' => $new_arr));
    } else {
        echo json_encode(array('status' => FALSE));
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carrega_cod_trib') {
    header('Content-Type: application/json; charset=utf8');
    $id_prestador = (int) $_REQUEST['id_prestador'];
    $query = "SELECT b.codigo,b.descricao
                FROM nfse_codigo_servico_assoc a
                INNER JOIN nfse_codigo_servico b ON a.id_codigo_servico = b.id
                WHERE id_prestador = $id_prestador;";
    $result = mysql_query($query);
    $arr = mysql_fetch_assoc($result);
    if (count($arr) > 0) {
        echo json_encode(array('status' => TRUE, 'cod' => utf8_encode("{$arr['codigo']} :: {$arr['descricao']}")));
    } else {
        echo json_encode(array('status' => FALSE));
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carrega_prestadores'){
    $id_projeto = addslashes($_REQUEST[projeto]);
    switch ($_REQUEST['status']) {
        case 'carregaPrestadoresInativos':
            $rs = GlobalClass::carregaPrestadorInativoByProjetoByProjeto($id_projeto);
            break;
        case 'carregaPrestadoresOutros':
            $rs = GlobalClass::carregaPrestadorOutrosByProjeto($id_projeto);
            break;
        case 'carregaPrestadores':
        default:
            $rs = GlobalClass::carregaPrestadorByProjeto($id_projeto,null,true);
            break;
    }
        
    foreach ($rs as $key => $value) {
        $arr[] = utf8_encode($value);
    }
    
    echo json_encode($arr);
    exit();
}