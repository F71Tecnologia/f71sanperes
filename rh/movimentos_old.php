<link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../js/jquery.validationEngine_2.6.2.js" type="text/javascript"></script>
<script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
<style>
    body{ margin: 50px;}
</style>
<script>
    $(function(){
        $('#form').validationEngine('validate');
    });
    
    function auxDistancia(fiel, rules, i, options){
        return options.allrules.auxDistancia.alertText;
    }

</script>
<form name="form" id="form" method="post" action="">
        
    <input type="text" name="auxDistance"  id="auxDistance" class="validate[required,funcCall[auxDistancia]]" />
    <input type="submit" name="enviar" id="enviar" value="Enviar" />
    
</form>