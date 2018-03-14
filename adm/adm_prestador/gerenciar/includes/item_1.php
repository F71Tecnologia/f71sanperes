<fieldset>
    <?php
    if($prestador['imprimir']>0){
    
    ?>
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
        <label class="first">Valor</label>
        <input type="text" name="valor" id="valor">
    </p>
    <p>
        <label class="first" >Gerar Contrato</label>
<!--        <input type="button" value="Gerar Contrato Mensal" onclick="window.location.href='contrato/?id=<?= $id_prestador; ?>'" />
        <input type="button" value="Gerar Contrato Horista" onclick="window.location.href='contrato/?id=<?= $id_prestador; ?>&horista=1'" />-->
        
        <?php
        $qry_layout = "SELECT * FROM prestador_layout_contrato AS A WHERE A.id_cnae = '{$prestador['id_cnae']}'";
        $sql_layout = mysql_query($qry_layout) or die(mysql_error());
        
        if(mysql_num_rows($sql_layout) > 0){
        ?>
        <input type="button" value="Gerar Contrato" data-key="<?= $prestador['id_prestador']; ?>" id="gera_contrato" />
        <?php }else{ ?>        
            Nenhum contrato disponível para esse prestador
        <?php } ?>
        
<!--        <input type="button" value="Gerar Contrato Mensal" onclick="enviar(0)" />
        <input type="button" value="Gerar Contrato Horista" onclick="enviar(1)" />-->
        
    </p>
<!--    <p>
        <label class="first" >Anexo I</label>
        <input type="button" value="Gerar Anexo I"  onclick="window.location.href='contrato/?id=<?php //= $id_prestador; ?>&tipo=2&anexo=1'"  />
    </p>
    <p>
        <label class="first" >Anexo II</label>
        <input type="button" value="Gerar Anexo II"  onclick="window.   location.href='contrato/?id=<?php //= $id_prestador; ?>&tipo=2&anexo=2'"  />
    </p>
    <p>
        <label class="first" >Anexo III</label>
        <input type="button" value="Gerar Anexo III"  onclick="window.location.href='contrato/?id=<?php //= $id_prestador; ?>&tipo=2&anexo=3'"  />
    </p>
    <p>
        <label class="first" >Anexo IV</label>
        <input type="button" value="Gerar Anexo IV"   onclick="window.location.href='contrato/?id=<?php //= $id_prestador; ?>&tipo=2&anexo=4'"  />
    </p>-->
    <?php }else{ ?>
    <div id="message-box" class="message-yellow">
        <p>Você precisa abrir o processo antes de gerar contrato</p>
    </div>
    <?php } ?>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>
<script>
    function enviar(horista){
        var valor = '&valor='+$("#valor").val();
        if(horista == 1){
            horista = 'horista=1';
        }else{
            horista = 'horista=0';
        }
        window.location.href='contrato/?id=<?= $id_prestador; ?>'+'&'+horista+valor;
    }
    
    $(function(){
        $("#gera_contrato").on("click", function(){
            var key = $(this).data("key");
            
            $("input[name=id]").val(key);
            $("#page_controller").attr('action', '../contratos/contrato.php');
            $("#page_controller").submit();
            
        });
    });
</script>