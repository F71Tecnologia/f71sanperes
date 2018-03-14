<fieldset>
    <legend>Imposto Retido</legend>
    <p>
        <label class="first" >Projeto</label>
        <?= $prestador['nome_projeto']; ?>
    </p>
    <p>
        <label class="first" >Prestador</label>
        <?= $prestador['nome_fantasia']; ?>
    </p>  
</fieldset>
<br><br>
<div>
    <?php if(isset($impostoRetido) && !empty($impostoRetido) && count($impostoRetido) > 0){ ?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
        <thead>
            <tr>
                <th colspan="7">Histórico de Lançamentos</th>
            </tr>
            <tr>
                <th>Número</th>
                <th>Competência</th>
                <th>Nome</th>
                <th>Valor</th>
                <th>Comprovante <br>de Pagamento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($impostoRetido as $ir) { ?>
            <tr>
                <td><?= $ir['id_saida'] ?></td>
                <td><?php echo sprintf("%02d",$ir['mes_competencia']).'/'.$ir['ano_competencia'] ?></td>
                <td><?= $ir['especifica'] ?></td>
                <td>R$ <?= $ir['valor'] ?></td>
                <td></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <?php } ?>
</div>
