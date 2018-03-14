<?php
include("../../conn.php");
include("../../wfunction.php");
include('../../classes/global.php');

header('Content-Type: text/html; charset=ISO-8859-1');

$regiao = $_REQUEST['regiao'];
$clt = $_REQUEST['clt'];

//QND FOR EDIÇÃO
$centrocusto = $_REQUEST['cc'];

if(!empty($centrocusto)){
    $btn = "editar";
}else{
    $btn = "salvar";
}

$query_cc = "SELECT * FROM centro_custo WHERE id_regiao = '{$regiao}'";
$result_cc = mysql_query($query_cc) or die(mysql_error());

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == "cadastrar_cc"){
        $id_cc = $_REQUEST['id_cc'];
        $id_clt = $_REQUEST['id_clt'];
        
        $cc = mysql_query("SELECT * FROM centro_custo WHERE id_centro_custo = {$id_cc}") or die(mysql_error());
        $cc_ = mysql_fetch_assoc($cc);
        
        $cc_nome = $cc_['nome'];
        
        $qry_upd_cc = "UPDATE rh_clt SET id_centro_custo = {$id_cc} WHERE id_clt = '{$id_clt}'";
        $sql_upd_cc = mysql_query($qry_upd_cc);
        
        if($sql_upd_cc){
            $retorno = array("status" => 1, "cc_nome" => utf8_encode($cc_nome), "id_clt" => $id_clt);
        }
        
    }elseif($_REQUEST['method'] == "editar_cc"){
        $id_cc_novo = $_REQUEST['id_cc_novo'];
        $id_cc_antigo = $_REQUEST['id_cc_antigo'];
        $id_clt = $_REQUEST['id_clt'];
        
        $cc = mysql_query("SELECT * FROM centro_custo WHERE id_centro_custo = {$id_cc_novo}") or die(mysql_error());
        $cc_ = mysql_fetch_assoc($cc);
        
        $cc_nome = $cc_['nome'];
        
        $qry_log_centrocusto = "INSERT INTO centro_custo_log (id_clt, id_cc_de, id_cc_para, data_proc, id_user)
                                VALUES ({$id_clt}, {$id_cc_antigo}, {$id_cc_novo}, NOW(), {$_COOKIE['logado']})";
        $sql_log_centrocusto = mysql_query($qry_log_centrocusto) or die('ERRO ao inserir LOG do CENTRO DE CUSTO');

        $qry_upd_cc = "UPDATE rh_clt SET id_centro_custo = {$id_cc_novo} WHERE id_clt = '{$id_clt}'";
        $sql_upd_cc = mysql_query($qry_upd_cc);

        if($sql_log_centrocusto && $sql_upd_cc){
            $retorno = array("status" => 1, "cc_nome" => utf8_encode($cc_nome), "id_clt" => $id_clt);
        }
    }
    
    echo json_encode($retorno);
    exit();
}
?>
<script>
    $(function(){        
        $("#salvar_cc").on("click", function(){
            var id_cc = $("#centrocusto").val();
            var id_clt = $("#id_clt").val();                                                
            
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "action_folha_analitica.php",
                data: {
                    id_cc: id_cc,
                    id_clt: id_clt,
                    method: "cadastrar_cc"
                },
                success: function(data) {
                    if(data.status == 1){
                        $("#centro_custo_"+data.id_clt).html(data.cc_nome);
                        $("#centro_custo_"+data.id_clt).addClass('no-estilo');
                        $("#centro_custo_"+data.id_clt).removeClass('no-print');
                        $(".ui-icon-closethick").click();
                    }
                }
            });
        });
        
        $("#editar_cc").on("click", function(){
            var id_cc_novo = $("#centrocusto").val();
            var id_cc_antigo = $("#id_cc").val();
            var id_clt = $("#id_clt").val();                        
            
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "action_folha_analitica.php",
                data: {
                    id_cc_novo: id_cc_novo,
                    id_cc_antigo: id_cc_antigo,
                    id_clt: id_clt,
                    method: "editar_cc"
                },
                success: function(data) {                    
                    if(data.status == 1){
                        $("#centro_custo"+data.id_clt).html(data.cc_nome);
                        $(".ui-icon-closethick").click();
                    }
                }
            });
        });
    });
</script>

<form id="form_centrocusto" name="form_centrocusto" action="" method="POST">
    <select name="centrocusto" id="centrocusto" class="validate[required]">
        <?php if(!isset($centrocusto)){ ?>
        <option value=""><< Selecione >></option>
        
        <?php
        }
        
        while ($row_cc = mysql_fetch_array($result_cc)) {
            print "<option value='{$row_cc['id_centro_custo']}'" . selected($row_cc['id_centro_custo'], $centrocusto) . ">{$row_cc['nome']}</option>";
        }
        ?>
    </select>
    
    <input type="hidden" name="id_clt" id="id_clt" value="<?php echo $clt; ?>" />
    <input type="hidden" name="id_cc" id="id_cc" value="<?php echo $centrocusto; ?>" />
    <input type="button" name="<?php echo $btn; ?>_cc" id="<?php echo $btn; ?>_cc" value="<?php echo $btn; ?>" />
</form>