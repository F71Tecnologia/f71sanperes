<?php 
include ("../../include/restricoes.php");
include ('../../classes/LogClass.php');
include "../../conn.php";

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
$id_folha = $_REQUEST['folha'];
$id_clt = $_REQUEST['id_clt'];
$id_tipo_pg = $_REQUEST['tipo_pg'];
$update = $_REQUEST['update'];


if(isset($update)){
    
    $log = new Log();
    
    
    $query = "
            UPDATE rh_folha_proc 
            SET tipo_pg = $id_tipo_pg 
            WHERE 
                id_regiao=$id_regiao 
                and id_projeto=$id_projeto 
                and id_folha=$id_folha 
                and id_clt=$id_clt
            "; 

    mysql_query($query) OR die('0');
    
    $log->gravaLog("Folha","Alteração do tipo de pagamento do CLT ($id_clt) - $query");
    
    exit('1');
    
}
else {
    
    $query = "SELECT id_tipopg, tipopg FROM tipopg WHERE id_regiao=$id_regiao and id_projeto=$id_projeto "; 

    $rs = mysql_query($query);
}

$query_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida' LIMIT 1");
$row_saida = mysql_fetch_assoc($query_saida);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Atualizar Forma de Pagamento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js"></script>

<script type="text/javascript">
function fecha(){
    
    parent.window.location.reload();
    
    if (parent.window.hs) {
        
        var exp = parent.window.hs.getExpander();
        
        if (exp) {
            exp.close();
        }
        
    }
    
}
    
$(function(){

    $('#form').submit(function(){

        $('.submit-go').attr('disabled',true);
        
        var dados = $(this).serialize();
        
        $.post('alt_tipo_pag.php',
            dados,
            function(retorno){
        
                //console.log(retorno);
                //return false;
                
                if(!retorno) { 
                    alert('Erro ao atualizar a base de dados');
                }
                
                fecha();

            }
        );
    });
 });
</script>
</head>
<body>  
<form name="form" id="form" method="POST" action="alt_tipo_pag.php" onsubmit="return false">
    <table width="100%">
        <tr>
            <td colspan="2">
                Forma de Pagamento
            </td>
        </tr>
        <tr>
            <td class="conteudo">
                <select  type="text" name="tipo_pg" id="tipo_pg" class="campotexto" >
                    <?
                    while ($row = mysql_fetch_array($rs)) {
                    ?>
                        <option value="<?=$row['id_tipopg']?>" <?=$row['id_tipopg']==$id_tipo_pg ? 'selected' : ''?>><?=$row['tipopg']?></option>
                    <?
                    }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    <table  width="100%">
        <tr>
            <td align="center">
                <input type="hidden" name="regiao" id="regiao" value="<?=$id_regiao?>" />
                <input type="hidden" name="projeto" id="projeto" value="<?=$id_projeto?>" />
                <input type="hidden" name="folha" id="folha" value="<?=$id_folha?>" />
                <input type="hidden" name="id_clt" id="id_clt" value="<?=$id_clt?>" />
                <input type="hidden" name="update" id="update" value="1">
                <input type="submit" name="btn_update" id="btn_update" value="  ATUALIZAR  " class="submit-go">                
                <div id="progressBar"></div>
            </td>
        </tr>
    </table>
</form>
</body>
</html>