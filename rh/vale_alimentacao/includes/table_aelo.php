<?php if (count($relacao_funcionarios) > 0) { ?>
<ul>
    <li>NOME DO USUÁRIO (40 carac)</li>
    <li>CPF só numeros</li>
    <li>Data nascimento dd/mm/aaaa</li>
    <li>Tipo de local de entrega (FI para filial, PT para posto de trabalho, UT para unidade de terceiros e AF para área funcional) </li>
    <li>Código do tipo de local de entrega: ? Para filial (FI), insira o número do CNPJ da filial, exemplo: para CNPJ 00.000.000/1234-56 inserir no campo código, o número 123456. ? Para posto de trabalho (PT), indique o código do posto de trabalho, informado pelo interlocutor durante o cadastro. ? Para unidades de terceiro (UT), insira o código da unidade de terceiro, informado pelo interlocutor durante o cadastro. ? Para área funcional (AF), insira o código da área funcional, informado pelo interlocutor durante o cadastro.</li>
    <li>Matrícula do funcionário. Este campo é opcional e pode permanecer vazio caso não se aplique à sua empresa.</li>
    <li>Para finalizar, deve-se "Salvar Como" o arquivo e escolher a terminação "CSV (separado por vírgulas)". O nome do arquivo é irrelevante.</li>
</ul>
<br>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_aelo">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>NOME DO USUÁRIO</th>
                <th>CPF</th>
                <th>DATA DE NASCIMENTO</th>
                <th>CÓDIGO DE SEXO</th>
                <th>VALOR (com virgulas para centavos e sem pontos ex: 1000,20</th>
                <th>TIPO DE LOCAL ENTREGA</th>
                <th>LOCAL DE ENTREGA</th>
                <th>MATRÍCULA</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($relacao_funcionarios as $funcionario){ ?>
            <tr>
                 <td class="center">%</td>
                 <td><?= $funcionario['nome_funcionario']; ?></td>
                 <td class="center"><?= $funcionario['cpf_limpo']; ?></td>
                 <td class="center"><?= $funcionario['data_nascimento']; ?></td>
                 <td class="center"><?= $funcionario['sexo']; ?></td>
                 <td class="center"><?= $funcionario['valor_recarga']; ?></td>
                 <td class="center">PT</td>
                 <td class="center">0001</td>
                 <td class="center"></td>
                 <td class="center">%</td>
             </tr>
            <?php } ?>
        </tbody> 
    </table>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de pedidos realizados.</p>
    </div>
<?php } ?>