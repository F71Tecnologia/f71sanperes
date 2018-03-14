<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

header('Content-Type: text/html; charset=ISO-8859-1');

include("../conn.php");
include("../wfunction.php");
include("../classes/SaidaClass.php");

$saida = new Saida();

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == 'exclui'){
        $saida->excluiEntradaSaida();
        
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

if($tipo == "entrada"){
    $saldo_novo = $saldo_antigo - $valor_antigo;
}else{
    $saldo_novo = $saldo_antigo + $valor_antigo;
}
?>

<form action="" method="post" class="form-horizontal top-margin1" name="form2" id="form2" autocomplete="off">
    <input type="hidden" name="saldo_dec" id="saldo_dec" value="<?php echo number_format($row['saldo'], 2, '.', ''); ?>" />
    <input type="hidden" name="valor_dec" id="valor_dec" value="<?php echo number_format($row['valor'], 2, '.', ''); ?>" />
    <input type="hidden" name="tipo_" id="tipo_" value="<?php echo $tipo; ?>" />
    <input type="hidden" name="id_lancamento" id="id_lancamento" value="<?php echo $id; ?>" />
    <input type="hidden" name="banco_lanc" id="banco_lanc" value="<?php echo $row['id_banco']; ?>" />
    <input type="hidden" name="status_lancamento" id="status_lancamento" value="<?php echo $row['status']; ?>" />
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label for="valor_antes" class="col-sm-3 control-label">Valor <?php echo $tipo; ?></label>
                <div class="col-sm-3">
                    <input type="text" name="valor_antigo" id="valor_antigo" class="form-control" disabled="disabled" value="<?php echo formataMoeda($valor_antigo, 1); ?>" />
                </div>
                <label for="valor_depois" class="col-sm-3 control-label">Banco</label>
                <div class="col-sm-3">
                    <input type="text" name="banco" id="banco" class="form-control" disabled="disabled" value="<?php echo $row['id_banco']; ?>" />                    
                </div>
            </div>            
            <div class="form-group">
                <label for="valor_antes" class="col-sm-3 control-label">Saldo banco</label>
                <div class="col-sm-3">
                    <input type="text" name="saldo_antigo" id="saldo_antigo" class="form-control" disabled="disabled" value="<?php echo formataMoeda($saldo_antigo, 1); ?>" />
                </div>
                <label for="valor_depois" class="col-sm-3 control-label">Novo Saldo</label>
                <div class="col-sm-3">
                    <input type="text" name="saldo_novo" id="saldo_novo" class="form-control" disabled="disabled" value="<?php echo formataMoeda($saldo_novo, 1); ?>" />
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <input type="button" name="confirmar" id="confirmar" value="Confirmar Exclusão" class="btn btn-danger" />
        </div>
    </div>
</form>

<script src="../js/jquery.maskMoney.js"></script>
<script>
    $(function() {
        $("#confirmar").click(function(){
            var valor = $("#valor_antigo").val();            
            var saldo_novo = $("#saldo_novo").val();
            var saldo_antigo = $("#saldo_antigo").val();
            var tipo_lancamento = $("#tipo_").val();
            var id_lancamento = $("#id_lancamento").val();
            var status_lancamento = $("#status_lancamento").val();
            var banco = $("#banco_lanc").val();
            
            $.ajax({
                url: "gestao_adm_delete.php",
                type: "POST",
                dataType: "json",
                data: {
                    method: "exclui",
                    id_lancamento: id_lancamento,
                    tipo_lancamento: tipo_lancamento,
                    status_lancamento: status_lancamento,
                    valor: valor,                    
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
        });
    });
</script>