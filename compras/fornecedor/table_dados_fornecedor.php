<table class="table table-bordered">
    <tbody>
        <tr>
            <td class="text-bold text-right">Razão Social</td>
            <td colspan="5"><?= (isset($cad_fornecedor['razao'])) ? $cad_fornecedor['razao'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Nome Fantasia</td>
            <td colspan="5"><?= (isset($cad_fornecedor['fantasia'])) ? $cad_fornecedor['fantasia'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">CNPJ</td>
            <td ><?= (isset($cad_fornecedor['cnpj'])) ? mascara_string($mask_cnpj, $cad_fornecedor['cnpj']) : 'N/I' ?></td>

            <td class="text-bold text-right">CNAE</td>
            <td colspan="3"><?= (isset($cad_fornecedor['cnae_descricao'])) ? $cad_fornecedor['cnae_descricao'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Inscrição Estadual</td>
            <td colspan="2"><?= (isset($cad_fornecedor['ie'])) ? $cad_fornecedor['ie'] : 'N/I' ?></td>

            <td class="text-bold text-right">Inscrição Municipal</td>
            <td colspan="2"><?= (isset($cad_fornecedor['im'])) ? $cad_fornecedor['im'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right" style="width: 16.667%;">CEP</td>
            <td style="width: 16.667%;"><?= (isset($cad_fornecedor['cep'])) ? mascara_string($mask_cep, $cad_fornecedor['cep']) : '' ?></td>

            <td class="text-bold text-right" style="width: 16.667%;">Estado</td>
            <td style="width: 16.667%;"><?= (isset($cad_fornecedor['uf_nome'])) ? $cad_fornecedor['uf_nome'] : '' ?></td>

            <td class="text-bold text-right" style="width: 16.667%;">Município</td>
            <td style="width: 16.667%;"><?= (isset($cad_fornecedor['mun'])) ? $cad_fornecedor['mun'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Endereço</td>
            <td colspan="3"><?= (isset($cad_fornecedor['endereco'])) ? $cad_fornecedor['endereco'].", ".$cad_fornecedor['num']." ".$cad_fornecedor['complemento']." - ".$cad_fornecedor['bairro'] : 'N/I' ?></td>

            <td class="text-bold text-right">Código IBGE</td>
            <td><?= (isset($cad_fornecedor['cod_ibge'])) ? $cad_fornecedor['cod_ibge'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Telefone</td>
            <td><?= (isset($cad_fornecedor['tel'])) ? mascara_stringTel($cad_fornecedor['tel']) : 'N/I' ?></td>
            <td class="text-bold text-right">Telefone 2</td>
            <td><?= (isset($cad_fornecedor['tel2'])) ? mascara_stringTel($cad_fornecedor['tel2']) : 'N/I' ?></td>
            <td class="text-bold text-right">Telefone 3</td>
            <td><?= (isset($cad_fornecedor['tel3'])) ? mascara_stringTel($cad_fornecedor['tel3']) : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">E-mail</td>
            <td colspan="2"><?= (isset($cad_fornecedor['email'])) ? $cad_fornecedor['email'] : 'N/I' ?></td>
            <td class="text-bold text-right">Site</td>
            <td colspan="2"><?= (isset($cad_fornecedor['site'])) ? $cad_fornecedor['site'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Contato</td>
            <td colspan="5"><?= (isset($cad_fornecedor['contato'])) ? $cad_fornecedor['contato'] : 'N/I' ?></td>
        </tr>
        <tr>
            <td class="text-bold text-right">Observação</td>
            <td colspan="5"><?= (isset($cad_fornecedor['obs'])) ? $cad_fornecedor['obs'] : '' ?></td>
        </tr>
    </tbody>
</table>