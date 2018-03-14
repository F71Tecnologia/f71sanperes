<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/SaidaClass.php");

$saida = new Saida();

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == 'edita'){
        $saida->editValorEntradaSaida();
        
        $retorno = array(
            "status" => 1
        );
    }
    
    echo json_encode($retorno);
    exit();
}

$id = $_REQUEST['id'];
$tipo = $_REQUEST['tipo'];

$row = $saida->getEntradaSaidaId($id, $tipo);

$valor_antigo = $row['valor_f'];
$saldo_antigo = $row['saldo_f'];
?>

<form action="" method="post" class="form-horizontal top-margin1" name="form2" id="form2" autocomplete="off">
    <input type="hidden" name="saldo_dec" id="saldo_dec" value="<?php echo number_format($row['saldo_f'], 2, '.', ''); ?>" />
    <input type="hidden" name="valor_dec" id="valor_dec" value="<?php echo number_format($row['valor_f'], 2, '.', ''); ?>" />
    <input type="hidden" name="tipo_" id="tipo_" value="<?php echo $tipo; ?>" />
    <input type="hidden" name="id_lancamento" id="id_lancamento" value="<?php echo $id; ?>" />
    <input type="hidden" name="banco_lanc" id="banco_lanc" value="<?php echo $row['id_banco']; ?>" />
    <input type="hidden" name="status_lancamento" id="status_lancamento" value="<?php echo $row['status']; ?>" />
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label for="valor_antes" class="col-sm-2 control-label">Antigo</label>
                <div class="col-sm-4">
                    <input type="text" name="valor_antigo" id="valor_antigo" class="form-control" disabled="disabled" value="<?php echo formataMoeda($valor_antigo, 1); ?>" />
                </div>
                <label for="valor_depois" class="col-sm-2 control-label">Novo</label>
                <div class="col-sm-4">
                    <input type="text" name="valor_novo" id="valor_novo" class="form-control validate[required]" />
                </div>
            </div>
            <?php if($row['status'] == 2){ ?>
            <div class="form-group">
                <label for="valor_antes" class="col-sm-2 control-label">Saldo Atual</label>
                <div class="col-sm-4">
                    <input type="text" name="saldo_antigo" id="saldo_antigo" class="form-control" disabled="disabled" value="<?php echo formataMoeda($saldo_antigo, 1); ?>" />
                </div>
                <label for="valor_depois" class="col-sm-2 control-label">Novo Saldo</label>
                <div class="col-sm-4">
                    <input type="text" name="saldo_novo" id="saldo_novo" class="form-control" disabled="disabled" value="" />
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="panel-footer text-right">
            <input type="button" name="atualizar" id="atualizar" value="Atualizar" class="btn btn-primary" />
        </div>
    </div>
    <div class="alert alert-dismissible hidden" id="msg-edit">        
        <span></span>
    </div>
</form>

<script src="../js/jquery.maskMoney_3.0.2.js"></script>
<script>
    $(function() {
        $("#form2").validationEngine({promptPosition : "topRight"});
        
        $("#valor_novo").maskMoney({
//            prefix:'R$ ', 
            allowNegative: true, 
            thousands:'.', 
            decimal:','
        });
        
        $("#valor_novo").blur(function(){
            var status_lancamento = $("#status_lancamento").val();
            
            if(status_lancamento == 2){
                var valor_novo = parseFloat($("#valor_novo").maskMoney('unmasked')[0]);
                var valor_antigo = parseFloat($("#valor_dec").val());
                var saldo_atual = parseFloat($("#saldo_dec").val());
                var tipo = $("#tipo_").val();
                var diferenca_valor = valor_novo - valor_antigo;
                var saldo_novo = 0;
                
                /*console.log(tipo, 'tipo');
                console.log(valor_novo, 'V:novo');
                console.log(valor_antigo, 'V:antigo');
                console.log(saldo_atual, 'S:atual');
                console.log(diferenca_valor, 'V:diferenca');*/
                
                if(tipo == 'saida'){
                    saldo_novo = number_format(saldo_atual - diferenca_valor, 2, ',', '.');
                }else if(tipo == 'entrada'){
                    saldo_novo = number_format(saldo_atual + diferenca_valor, 2, ',', '.');
                }
                
                if(valor_novo != ''){
                    $('#saldo_novo').val(saldo_novo);
                }
            }
        });
        
        $("#atualizar").click(function(){
            if($("#form2").validationEngine('validate')){
                var valor_novo = $("#valor_novo").val();
                var valor_antigo = $("#valor_antigo").val();
                var saldo_novo = $("#saldo_novo").val();
                var saldo_antigo = $("#saldo_antigo").val();
                var tipo_lancamento = $("#tipo_").val();
                var id_lancamento = $("#id_lancamento").val();
                var status_lancamento = $("#status_lancamento").val();
                var banco = $("#banco_lanc").val();
                
                $.ajax({
                    url: "gestao_adm_edit.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        method: "edita",
                        id_lancamento: id_lancamento,
                        tipo_lancamento: tipo_lancamento,
                        status_lancamento: status_lancamento,
                        valor_novo: valor_novo,
                        valor_antigo: valor_antigo,
                        saldo_novo: saldo_novo,
                        saldo_antigo: saldo_antigo,
                        banco: banco
                    },
                    success: function(data) {
                        if(data.status == "1"){
                            $(".close").click();
                            $("#filt").click();
                        }
                    }
                });
            }
        });
    });
</script>