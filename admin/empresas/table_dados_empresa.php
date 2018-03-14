<table class="table table-bordered">
    <tbody>
        <tr>
            <td class="text-bold text-right">Razão Social</td>
            <td colspan="5"><?= (isset($cad_empresa['razao'])) ? $cad_empresa['razao'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Nome Fantasia</td>
            <td colspan="5"><?= (isset($cad_empresa['fantasia'])) ? $cad_empresa['fantasia'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">CNPJ</td>
            <td ><?= (isset($cad_empresa['cnpj'])) ? mascara_string($mask_cnpj, $cad_empresa['cnpj']) : 'N/I' ?></td>

            <td class="text-bold text-right">CNAE</td>
            <td colspan="3"><?= (isset($cad_empresa['cnae_descricao'])) ? $cad_empresa['cnae_descricao'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Inscrição Estadual</td>
            <td colspan="2"><?= (isset($cad_empresa['ie'])) ? $cad_empresa['ie'] : 'N/I' ?></td>

            <td class="text-bold text-right">Inscrição Municipal</td>
            <td colspan="2"><?= (isset($cad_empresa['im'])) ? $cad_empresa['im'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right" style="width: 16.667%;">CEP</td>
            <td style="width: 16.667%;"><?= (isset($cad_empresa['cep'])) ? mascara_string($mask_cep, $cad_empresa['cep']) : '' ?></td>

            <td class="text-bold text-right" style="width: 16.667%;">Estado</td>
            <td style="width: 16.667%;"><?= (isset($cad_empresa['uf_nome'])) ? $cad_empresa['uf_nome'] : '' ?></td>

            <td class="text-bold text-right" style="width: 16.667%;">Município</td>
            <td style="width: 16.667%;"><?= (isset($cad_empresa['mun'])) ? $cad_empresa['mun'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Endereço</td>
            <td colspan="3"><?= (isset($cad_empresa['endereco'])) ? $cad_empresa['endereco'].", ".$cad_empresa['num']." ".$cad_empresa['complemento']." - ".$cad_empresa['bairro'] : 'N/I' ?></td>

            <td class="text-bold text-right">Código IBGE</td>
            <td><?= (isset($cad_empresa['cod_ibge'])) ? $cad_empresa['cod_ibge'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Telefone</td>
            <td><?= (isset($cad_empresa['tel'])) ? mascara_stringTel($cad_empresa['tel']) : 'N/I' ?></td>
            <td class="text-bold text-right">Telefone 2</td>
            <td><?= (isset($cad_empresa['tel2'])) ? mascara_stringTel($cad_empresa['tel2']) : 'N/I' ?></td>
            <td class="text-bold text-right">Telefone 3</td>
            <td><?= (isset($cad_empresa['tel3'])) ? mascara_stringTel($cad_empresa['tel3']) : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">E-mail</td>
            <td colspan="2"><?= (isset($cad_empresa['email'])) ? $cad_empresa['email'] : 'N/I' ?></td>
            <td class="text-bold text-right">Site</td>
            <td colspan="2"><?= (isset($cad_empresa['site'])) ? $cad_empresa['site'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Contato</td>
            <td colspan="5"><?= (isset($cad_empresa['contato'])) ? $cad_empresa['contato'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Observação</td>
            <td colspan="5"><?= (isset($cad_empresa['obs'])) ? $cad_empresa['obs'] : '' ?></td>
        </tr>
    </tbody>
</table>