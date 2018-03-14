<?php if (count($relacao_funcionarios) > 0) { ?>
<ul>
    <li>NOME DO USU�RIO (40 carac)</li>
    <li>CPF s� numeros</li>
    <li>Data nascimento dd/mm/aaaa</li>
    <li>Tipo de local de entrega (FI para filial, PT para posto de trabalho, UT para unidade de terceiros e AF para �rea funcional) </li>
    <li>C�digo do tipo de local de entrega: ? Para filial (FI), insira o n�mero do CNPJ da filial, exemplo: para CNPJ 00.000.000/1234-56 inserir no campo c�digo, o n�mero 123456. ? Para posto de trabalho (PT), indique o c�digo do posto de trabalho, informado pelo interlocutor durante o cadastro. ? Para unidades de terceiro (UT), insira o c�digo da unidade de terceiro, informado pelo interlocutor durante o cadastro. ? Para �rea funcional (AF), insira o c�digo da �rea funcional, informado pelo interlocutor durante o cadastro.</li>
    <li>Matr�cula do funcion�rio. Este campo � opcional e pode permanecer vazio caso n�o se aplique � sua empresa.</li>
    <li>Para finalizar, deve-se "Salvar Como" o arquivo e escolher a termina��o "CSV (separado por v�rgulas)". O nome do arquivo � irrelevante.</li>
</ul>
<br>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_aelo">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>NOME DO USU�RIO</th>
                <th>CPF</th>
                <th>DATA DE NASCIMENTO</th>
                <th>C�DIGO DE SEXO</th>
                <th>VALOR (com virgulas para centavos e sem pontos ex: 1000,20</th>
                <th>TIPO DE LOCAL ENTREGA</th>
                <th>LOCAL DE ENTREGA</th>
                <th>MATR�CULA</th>
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
        <p>N�o h� registros de pedidos realizados.</p>
    </div>
<?php } ?>