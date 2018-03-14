<fieldset>
    <legend>Contrato e Anexos</legend>
    <p>
        <label class="first" >Projeto</label>
        <?= $prestador['nome_projeto']; ?>
    </p>
    <p>
        <label class="first" >Prestador</label>
        <?= $prestador['nome_fantasia']; ?>
    </p>
    <p>
        <label class="first" >Status</label>
        <?php if($prestador['imprimir']>0){ ?>
        <span style="color: green">ABERTO</span>
        <?php }else{ ?>
        <span style="color: red">FECHADO</span>
        <?php } ?>
    </p>
    <p>
        <label class="first" >Ações</label>
        <?php if($prestador['imprimir']>0){ ?>
        <input type="button" value="Fechar Processo" onclick="window.location.href='encerramento/?prestador=<?= $id_prestador; ?>'" />
        <?php }else{ ?>
        <input type="button" value="Abrir Processo" onclick="window.location.href='abertura/?id=<?= $id_prestador; ?>'" />
        <?php } ?>
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>