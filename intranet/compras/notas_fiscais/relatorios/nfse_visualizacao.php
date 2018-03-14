<?php
header('Content-Type: text/html; charset=iso-8859-1');
include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/NFSeClass.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$nfse = new NFSe(); // instancia obj nfe 

$nfse_arr['nfse'] = $nfse->exibirNota($_REQUEST['id']);


$query = "SELECT ValorCofins,ValorCsll,ValorInss,ValorIr,ValorPis,ValorServicos,ValorLiquidoNfse,PrestadorServico FROM nfse WHERE id_nfse = {$_REQUEST['id']};";
$return = mysql_query($query);

$row = mysql_fetch_assoc($return);
$nfse_arr['val']['nfse']['CSLL'] = $row['ValorCsll'];
$nfse_arr['val']['nfse']['COFINS'] = $row['ValorCofins'];
$nfse_arr['val']['nfse']['INSS'] = $row['ValorInss'];
$nfse_arr['val']['nfse']['IRRF'] = $row['ValorIr'];
$nfse_arr['val']['nfse']['PIS'] = $row['ValorPis'];

$valor_servico = $row['ValorServicos'];
$valor_liquido = $row['ValorLiquidoNfse'];
$id_prestador = $row['PrestadorServico'];

$inscricao_estadual = mysql_result(mysql_query("SELECT c_ie FROM prestadorservico WHERE id_prestador = '{$id_prestador}' limit 1"), 0);

$query = "SELECT * 
                FROM contabil_impostos_assoc AS a
                INNER JOIN contabil_impostos AS b ON a.id_imposto = b.id_imposto
                WHERE id_contrato = $id_prestador";
$return = mysql_query($query);

while ($row = mysql_fetch_assoc($return)) {
    $nfse_arr['val']['calculo'][$row['sigla']] = ($row['aliquota'] * $valor_servico) / 100.00;
}
?>

    <table class="nfse">
        <tbody>
            <tr>
                <?php
                switch ($nfse_arr['nfse']['CodigoMunicipio']) {
                    case 3304557: //codigo na tabela municipios
                        ?>
                        <td rowspan="3" class="text-bold text-center" style="width: 120px;"><img src="semimagem.gif" alt="sem Imagem" style="height: 120px;"></td>
                        <td rowspan="3" class="text-bold text-center">
                            PREFEITURA DA CIDADE DO RIO DE JANEIRO<br>
                            SECRETARIA MUNICIPAL DE FAZENDA<br>
                            NOTA FISCAL DE SERVIÇO ELETRÔNICA - NFS-e<br>
                        </td>
                        <?php
                        break;
                    case 3304904://codigo na tabela municipios
                        ?>
                        <td rowspan="3" class="text-bold text-center" style="width: 120px;"><img src="semimagem.gif" alt="sem Imagem" style="height: 120px;"></td>
                        <td rowspan="3" class="text-bold text-center">
                            PREFEITURA MUNICIPAL DE SÃO GONÇALO<br>
                            SECRETARIA MUNICIPAL DE FAZENDA<br>
                            NOTA FISCAL DE SERVIÇO ELETRÔNICA - NFS-e<br>
                        </td>
                        <?php
                        break;
                    case 3301900://codigo na tabela municipios
                        ?>
                        <td rowspan="3" class="text-bold text-center" style="width: 120px;"><img src="semimagem.gif" alt="sem Imagem" style="height: 120px;"></td>
                        <td rowspan="3" class="text-bold text-center">
                            PREFEITURA MUNICIPAL DE ITABORAÍ<br>
                            SECRETARIA MUNICIPAL DE FAZENDA<br>
                            NOTA FISCAL DE SERVIÇO ELETRÔNICA - NFS-e<br>
                        </td>
                        <?php
                        break;
                }
                ?>
                <td>
                    <strong>Númedo da Nota</strong><br>
                    <?= $nfse_arr['nfse']['Numero'] ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Data e Hora de Emissão</strong><br>
                    <?= implode('/', array_reverse(explode('-', substr($nfse_arr['nfse']['DataEmissao'], 0, 10)))) . " " . substr($nfse_arr['nfse']['DataEmissao'], 11, 8) ?>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Código de Verificação</strong><br>
                    <?= $nfse_arr['nfse']['CodigoVerificacao'] ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="nfse">
        <tbody>
            <tr><td class="text-bold text-center"  colspan="4">Prestador de Serviço</td></tr>
            <tr>
                <td rowspan="5" class="text-center" style="width: 120px;"><img src="semimagem.gif" alt="sem Imagem" style="height: 120px;"></td>
                <td><strong>CPF/CNPJ:</strong> <?= mascara_string("##.###.###/####-##", $nfse_arr['nfse']['prestador_cnpj']) ?></td>
                <td><strong>Inscrição Municipal:</strong></td>
                <td>
                    <strong>Inscrição Estadual:</strong> <?= $inscricao_estadual ?> 
                </td>
            </tr>
            <tr>
                <td colspan="3"><strong>Nome/ Razão Social:</strong> <?= $nfse_arr['nfse']['prestador_razao'] ?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>Nome Fantasia:</strong><?= $nfse_arr['nfse']['prestador_fantasia'] ?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>Endereço</strong></td>
            </tr>
            <tr>
                <td><strong>Município</strong></td>
                <td><strong>UF:</strong></td>
                <td><strong>E-mail:</strong></td>
            </tr>
        </tbody>
    </table>
    <table class="nfse">
        <tbody>
            <tr class="text-bold text-center"><td colspan="3">Tomador de Serviços</td></tr>
            <tr>
                <td><strong>CPF/CNPJ:</strong> <?= mascara_string("##.###.###/####-##", $nfse_arr['nfse']['projeto_cnpj']) ?></td>
                <td><strong>Inscrição Municipal:</strong> <?= $nfse_arr['nfse']['projeto_im'] ?></td>
                <td><strong>Inscrição Estadual:</strong> </td>
            </tr>
            <tr>
                <td colspan="3"><strong>Nome/ Razão Social:</strong> <?= $nfse_arr['nfse']['projeto_razao'] ?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>Endereço</strong> </td>
            </tr>
            <tr>
                <td><strong>Município</strong></td>
                <td><strong>UF:</strong></td>
                <td><strong>E-mail:</strong></td>
            </tr>
        </tbody>
    </table>
    <table class="nfse">
        <tbody>
            <tr class="text-bold text-center"><td colspan="6">Discriminação dos Serviços</td></tr>
            <tr class="text-justify">
                <td colspan="6">
                    <p class="text-justify"><?= $nfse_arr['nfse']['Discriminacao'] ?></p>

                </td>
            </tr>
            <tr class="text-bold text-center"><td colspan="6">Valor da Nota = R$ <?= number_format((float) $nfse_arr['nfse']['ValorServicos'], 2, ',', '.') ?></td></tr>
            <tr>
                <td colspan="6">
                    <strong>Serviço Prestado</strong><br>
                    <?= $nfse_arr['nfse']['CodigoTributacaoMunicipio'] ?> - <?= $nfse_arr['nfse']['descricao_cod_servico'] ?>
                </td>
            </tr>
            <tr>
                <td>&emsp;</td>
                <td>COFINS (R$)</td>
                <td>CSLL (R$)</td>
                <td>INSS (R$)</td>
                <td>IRPJ (R$)</td>
                <td>PIS (R$)</td>


            </tr>
            <?php foreach ($nfse_arr['val'] as $key => $values) { ?>
                <tr class="<?= $key === 'nfse' ? 'text-warning warning' : 'text-danger danger' ?>">
                    <td><strong><?= $key === 'nfse' ? 'NFSe' : 'Calculado' ?></strong></td>
                    <td><?= number_format((float) $values['COFINS'], 2, ',', '.') ?></td>
                    <td><?= number_format((float) $values['CSLL'], 2, ',', '.') ?></td>
                    <td><?= number_format((float) $values['INSS'], 2, ',', '.') ?></td>
                    <td><?= number_format((float) $values['IRRF'], 2, ',', '.') ?></td>
                    <td><?= number_format((float) $values['PIS'], 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    Deduções (R$) <br>
                    <?= number_format((float) $nfse_arr['nfse']['ValorDeducao'], 2, ',', '.') ?>
                </td>
                <td>
                    Dsconto Incond. (R$) <br>
                    <?= number_format((float) $nfse_arr['nfse']['ValorDesconto'], 2, ',', '.') ?>
                </td>
                <td>
                    Base de Cálculo (R$) <br>
                    <?= number_format((float) $nfse_arr['nfse']['BaseCalculo'], 2, ',', '.') ?>
                </td>
                <td>
                    Aliquota (%) <br>
                    <?= number_format((float) $nfse_arr['nfse']['Aliquota'], 2, '.', ',') ?>
                </td>
                <td>
                    Valor do ISS (R$) <br>
                    <?= number_format((float) $nfse_arr['nfse']['ValorIss'], 2, ',', '.') ?>
                </td>
                <td>
                    Crédito Gerado (R$) <br>
                    <?= number_format((float) $nfse_arr['nfse']['Credito'], 2, ',', '.') ?>
                </td>
            </tr>
            <tr class="text-bold text-center">
                <td colspan="6">
                    Valor Liquido da Nota = R$ 
                    <?= number_format((float) $nfse_arr['nfse']['ValorLiquidoNfse'], 2, ',', '.') ?>
                </td>
            </tr>
            <tr class="text-bold text-center"><td colspan="6">Outra Informações</td></tr>
            <tr class="text-justify">
                <td colspan="6">
                    <p class="text-justify"><?= $nfse_arr['nfse']['OutrasInformacoes'] ?></p>
                </td>
            </tr>
        </tbody>
    </table>
</form>

