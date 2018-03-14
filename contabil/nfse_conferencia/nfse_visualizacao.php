<?php
/*
 * Para quem um dia for mexer nessa tela:
 * Está tudo em tabela porque o Milton queria que as informações da NFSe aparecesse EXATAMENTE como na versão impressa
 * Att,
 * Leonardo
 */
?>
<form action="nfse_conferencia_controle.php" method="post" id="form_conferencia">
    <input type="hidden" name="id_nfse" value="<?= $nfse_arr['nfse']['id_nfse'] ?>" id="id_nfse">
    <input type="hidden" name="id_projeto" value="<?= $nfse_arr['nfse']['id_projeto'] ?>" id="id_nfse">
    <input type="hidden" name="PrestadorServico" value="<?= $nfse_arr['nfse']['PrestadorServico'] ?>" id="id_nfse">
    <input type="hidden" name="Competencia" value="<?= $nfse_arr['nfse']['Competencia'] ?>" id="id_nfse">
    <input type="hidden" name="Numero" value="<?= $nfse_arr['nfse']['Numero'] ?>" id="id_nfse">

    <?php
    if (isset($nfse_arr['retencao'])) {
        //echo "Retenção SETADA";
        foreach ($nfse_arr['retencao'] as $key => $value) {
            ?>

            <input type="hidden" name="<?= $key . "_retencao" ?>" id="<?= $key . "_retencao" ?>" value="<?= $value ?>">            
            <?php
        }
        // comentado pq não vai trazer as retenções da nota
        //} else{
        //echo "Retenção NÃO SETADA";
        //foreach($nfse_arr['retencao_padrao'] as $key => $value){ 
        ?>

            <!--<input type="hidden" name="<?= $key . "_retencao" ?>" id="<?= $key . "_retencao" ?>" value="<?= $value ?>">-->

        <?php
        //}
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
                    <input type="hidden" value="<?= $nfse_arr['nfse']['Numero'] ?>" id="numeronf_link">
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
                    <input type="hidden" value="<?= str_replace("-", "", $nfse_arr['nfse']['CodigoVerificacao']) ?>" id="codverifica_link">
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
                    <input type="hidden" value="<?= $inscricao_estadual ?>" id="inscricao_link">
                </td>
            </tr>
            <tr>
                <td colspan="3"><strong>Nome/ Razão Social:</strong> <?= $nfse_arr['nfse']['prestador_razao'] ?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>Nome Fantasia:</strong><?= $nfse_arr['nfse']['prestador_fantasia'] ?></td>
            </tr>
            <tr>
                <td colspan="3"><strong>Endereço:</strong> <?= $nfse_arr['nfse']['endereco_prestador'] ?></td>
            </tr>
            <tr>
                <td><strong>Município:</strong> <?= $nfse_arr['nfse'][''] ?></td>
                <td><strong>UF:</strong> <?= $nfse_arr['nfse']['uf_prestador'] ?></td>
            <td><strong>E-mail:</strong> <?= $nfse_arr['nfse']['email_prestador'] ?></td>
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
                <td colspan="3"><strong>Endereço:</strong> <?= $nfse_arr['nfse']['endereco_empresa'] ?></td>
            </tr>
            <tr>
                <td><strong>Município:</strong> <?= $nfse_arr['nfse']['municipio_empresa'] ?></td>
                <td><strong>UF:</strong> <?= $nfse_arr['nfse']['uf_empresa'] ?></td>
                <td><strong>E-mail:</strong> <?= $nfse_arr['nfse']['email_empresa'] ?></td>
            </tr>
        </tbody>
    </table>
    <table class="nfse">
        <tbody>
            <tr class="text-bold text-center"><td colspan="6">Discriminação dos Serviços</td></tr>
            <tr class="text-justify">
                <td colspan="6">
                    <textarea class="form-control" name="Discriminacao" id="Discriminacao" rows="5"><?= $nfse_arr['nfse']['Discriminacao'] ?></textarea>

                </td>
            </tr>
            <tr class="text-bold text-center"><td colspan="6">Valor da Nota = R$ <input type="text" name="ValorServicos" id="ValorServicos" class="form-control text-center money validate[required]" value="<?= number_format((float) $nfse_arr['nfse']['ValorServicos'], 2, ',', '.') ?>"></td></tr>
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
                <?php $disabled = $key !== 'nfse' ? 'disabled' : '' ?>
                <?php $class = $key === 'nfse' ? 'impostos_deducao' : '' ?>
                <tr class="<?= $key === 'nfse' ? 'text-warning warning' : 'text-danger danger' ?>">
                    <td><strong><?= $key === 'nfse' ? 'NFSe' : 'Calculado' ?></strong></td>
                    <td><input <?= $disabled ?> type="text" name="COFINS" id="COFINS_VALOR" class="form-control money <?= $class ?>" value="<?= number_format((float) $values['COFINS'], 2, ',', '.') ?>"></td>
                    <td><input <?= $disabled ?> type="text" name="CSLL" id="CSLL_VALOR" class="form-control money <?= $class ?>" value="<?= number_format((float) $values['CSLL'], 2, ',', '.') ?>"></td>
                    <td><input <?= $disabled ?> type="text" name="INSS" id="INSS_VALOR" class="form-control money <?= $class ?>" value="<?= number_format((float) $values['INSS'], 2, ',', '.') ?>"></td>
                    <td><input <?= $disabled ?> type="text" name="IRRF" id="IRRF_VALOR" class="form-control money <?= $class ?>" value="<?= number_format((float) $values['IRRF'], 2, ',', '.') ?>"></td>
                    <td><input <?= $disabled ?> type="text" name="PIS" id="PIS_VALOR" class="form-control money <?= $class ?>" value="<?= number_format((float) $values['PIS'], 2, ',', '.') ?>"></td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    Deduções (R$) <br>
                    <input type="text" name="ValorDeducao" id="ValorDeducao" class="form-control money" value="<?= number_format((float) $nfse_arr['nfse']['ValorDeducao'], 2, ',', '.') ?>">
                </td>
                <td>
                    Dsconto Incond. (R$) <br>
                    <input type="text" name="ValorDesconto" id="ValorDesconto" class="form-control money" value="<?= number_format((float) $nfse_arr['nfse']['ValorDesconto'], 2, ',', '.') ?>">
                </td>
                <td>
                    Base de Cálculo (R$) <br>
                    <input type="text" name="BaseCalculo" id="BaseCalculo" class="form-control money" value="<?= number_format((float) $nfse_arr['nfse']['BaseCalculo'], 2, ',', '.') ?>">
                </td>
                <td>
                    Aliquota (%) <br>
                    <?php //$aliquota = (float) ($nfse_arr['nfse']['Aliquota'] == 0.0) ? $nfse_arr['retencao']['ISS'] : $nfse_arr['nfse']['Aliquota'] ?>
                    <?php $aliquota = (float) $nfse_arr['retencao']['ISS'] ?>
                    <input type="text" name="Aliquota" id="Aliquota" class="form-control money" value="<?= number_format($aliquota, 2, ',', '.') ?>">
                </td>
                <td>
                    Valor do ISS (R$) <br>
                    <input type="text" name="ValorIss" id="ValorIss" class="form-control money impostos_deducao" value="<?= number_format((float) $nfse_arr['nfse']['ValorIss'], 2, ',', '.') ?>">
                </td>
                <td>
                    Crédito Gerado (R$) <br>
                    <input type="text" name="Credito" id="Credito" class="form-control money" value="<?= number_format((float) $nfse_arr['nfse']['Credito'], 2, ',', '.') ?>">
                </td>
            </tr>
            <tr class="text-bold text-center">
                <td colspan="6">
                    Valor Liquido da Nota = R$ 
                    <input type="text" name="ValorLiquidoNfse" id="ValorLiquidoNfse" readonly class="form-control text-center money" value="<?= number_format((float) $nfse_arr['nfse']['ValorLiquidoNfse'], 2, ',', '.') ?>">
                </td>
            </tr>
            <tr class="text-bold text-center"><td colspan="6">Outra Informações</td></tr>
            <tr class="text-justify">
                <td colspan="6">
                    <textarea class="form-control" name="OutrasInformacoes" id="OutrasInformacoes" rows="5"><?= $nfse_arr['nfse']['OutrasInformacoes'] ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</form>

