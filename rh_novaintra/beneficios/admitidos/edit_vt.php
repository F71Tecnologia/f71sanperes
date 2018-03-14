<?php
include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/ValeTransporteClass.php");
include "../../../classes/LogClass.php";
$log = new Log();

$clt = $_REQUEST['id'];

$objTransporte = new ValeTransporteClass();

$dadosClt = $objTransporte->getListaClts(null, $clt, true);

//print_array($dadosClt);

if($_REQUEST['method'] == "edita_valeT"){
    $retorno = array("status" => "0");
    
    $cltVal = $_REQUEST['id'];
    $flag_transporte = $_REQUEST['transporte'];
    
    if($flag_transporte){
        
        // VALORES/QTD DE VT
        $vt_valor1 = $_REQUEST['vt_valor1'];
        $vt_valor2 = $_REQUEST['vt_valor2'];
        $vt_valor3 = $_REQUEST['vt_valor3'];
        $vt_qtd1 = $_REQUEST['vt_qtd1'];
        $vt_qtd2 = $_REQUEST['vt_qtd2'];
        $vt_qtd3 = $_REQUEST['vt_qtd3'];        
        
        // VALE TRANSPORTE
        $result_cont_vale = mysql_query("SELECT * FROM rh_vt_valores_assoc WHERE id_clt = '$cltVal' AND status_reg = 1");
        $row_cont_vale = mysql_num_rows($result_cont_vale);
        
        if (empty($row_cont_vale)) {
            $insere_valores_assoc = mysql_query("INSERT INTO rh_vt_valores_assoc(id_clt, id_valor1, id_valor2, id_valor3, qtd1, qtd2, qtd3) VALUES
                ('$cltVal','$vt_valor1','$vt_valor2','$vt_valor3','$vt_qtd1','$vt_qtd2','$vt_qtd3')") or die("Erro de digitação no INSERT dos vales query: " . mysql_error());
            
            if($insere_valores_assoc){
                $insere_vt_clt = mysql_query("UPDATE rh_clt SET transporte = 1 WHERE id_clt = '$cltVal'") or die("Erro de digitação no UPDATE do clt VT: " . mysql_error());
                
                if($insere_vt_clt){
                    $log->gravaLog('Benefícios - Admitidos', "Alteração no Usuário: ID{$clt}");
                    $retorno = array("status" => "1");
                }
            }
        }
    }
    
    echo json_encode($retorno);
    exit;
}
?>

<script>
    $(function(){        
        $("#transporte").click(function(){
            if($("#transporte").prop("checked")){
                $(".valores_vt_").removeClass("hidden");
            }else{
                $(".valores_vt_").addClass("hidden");
                $("#vt_qtd1, #vt_qtd2, #vt_qtd3, #vt_valor1, #vt_valor2, #vt_valor3").val("");
            }
        });
        
        $(".save_vt").on("click", function() {
            var key = $("#cod_clt").val();
            var transporte = $("#transporte").prop("checked");
            var vt_valor1 = $("#vt_valor1").val();
            var vt_valor2 = $("#vt_valor2").val();
            var vt_valor3 = $("#vt_valor3").val();
            var vt_qtd1 = $("#vt_qtd1").val();
            var vt_qtd2 = $("#vt_qtd2").val();
            var vt_qtd3 = $("#vt_qtd3").val();
            
            $.ajax({
                type: "post",
                url: "edit_vt.php",
                dataType: "json",
                data: {
                    id: key,
                    transporte: transporte,
                    vt_valor1: vt_valor1,
                    vt_valor2: vt_valor2,
                    vt_valor3: vt_valor3,
                    vt_qtd1: vt_qtd1,
                    vt_qtd2: vt_qtd2,
                    vt_qtd3: vt_qtd3,
                    method: "edita_valeT"
                },
                success: function(data) {
                    if(data.status == "1"){
                        $("#participante_"+key).fadeOut();
                        $(".close").click();
                    }
                }
            });
        });
    });
</script>

<input type="hidden" name="cod_clt" id="cod_clt" value="<?php echo $clt; ?>" />

<div class="panel panel-default">
    <div class="panel-heading"><?php echo $dadosClt['nome']; ?></div>
    <div class="panel-body">
        <div class="row m-bottom25">
            <div class="col-lg-12">
                <div class="form-group">
                    <label class="col-lg-4 control-label">Vale Transporte:</label>
                    <div class="col-lg-8">
                        <input name="transporte" type="checkbox" class="reset" id="transporte" value="1">
                    </div>
                </div>
            </div>
        </div>
        <div class="valores_vt_ hidden">
            <?php for($qtd_val = 1; $qtd_val <= 3; $qtd_val++){ ?>                        
            <div class="row m-bottom25">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Valor <?php echo $qtd_val; ?>:</label>
                        <div class="col-lg-5">
                            <select name="vt_valor<?php echo $qtd_val; ?>" id="vt_valor<?php echo $qtd_val; ?>" class="form-control">
                                <option value="">Nao Tem</option>
                                
                                <?php
                                // TRAZ VALORES DE VT
                                $qry_vt_valores = "SELECT *
                                                FROM rh_vt_valores AS A
                                                WHERE A.status_reg = 1";
                                $sql_vt_valores = mysql_query($qry_vt_valores) or die("ERRO qry_vt_valores");
                                
                                while($res_vt_valores = mysql_fetch_assoc($sql_vt_valores)){
                                ?>
                                <option value="<?php echo $res_vt_valores['id_valor']; ?>"><?php echo $res_vt_valores['valor']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <input name="vt_qtd<?php echo $qtd_val; ?>" type="text" id="vt_qtd<?php echo $qtd_val; ?>" size="20" placeholder="Qtd" class="form-control sonumeros" />
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="panel-footer text-right">
        <button type="submit" name="save_vt" value="salvar" class="btn btn-success save_vt"> Salvar</button>
    </div>
</div>