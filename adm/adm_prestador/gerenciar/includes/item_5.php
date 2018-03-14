<fieldset>
    <legend>Ficha de Cadastro</legend>
    <p>
        <label class="first" >Projeto</label>
        <?= $prestador['nome_projeto']; ?>
    </p>
    <p>
        <label class="first" >Prestador</label>
        <?= $prestador['nome_fantasia']; ?>
    </p>
    <p>
        <label class="first" >Gerar Ficha</label>
        <input type="button" value="Gerar" onclick=" window.open('fichacadastro/?id=<?= $id_prestador; ?>&pro=<?= $prestador['id_projeto'] ?>&reg=<?= $prestador['id_regiao'] ?>', '_blank');" />
    </p>
</fieldset>
<br><br>

