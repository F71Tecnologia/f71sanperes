<?php
include("../../conn.php");
include("../../classes/SaidaClass.php");
include("../../wfunction.php");
include("../../funcoes.php");

$saida_class = new Saida();

$codigo = $_REQUEST['id'];
$saida = $saida_class->dataVencimento($codigo);
$anexo = $saida_class->getSaidaFile($codigo);

$darf = false;
$pai = "";

if(validate($_REQUEST['darf'])){
    $darf = true;
    $pai = $saida['id_saida_pai'];
    $prestador = $saida['id_prestador'];
    $rsSaidas = montaQuery("saida","*","id_prestador={$prestador} AND darf IS NULL");
    $saidas = array();
    foreach ($rsSaidas as $rssaida){
        $saidas[$rssaida['id_saida']] = $rssaida['id_saida']." - ".$rssaida['especifica']." - R$ ".$rssaida['valor'];
    }
}

$tiposDarf = array("1708" => "1708", "5902" => "5902");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">            
            <div class="panel panel-default">
                <div class="panel-heading">Dados</div>
                <div class="panel-body">                                
                    <div class="row">
                        <div class="form-group">
                            <label for="data_pgt" class="col-lg-4 control-label">Data Pagamento:</label>
                            <div class="col-lg-8 valign-middle">
                                <?php echo $saida['data_vencimentobr']; ?>
                            </div>                                                
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="data_pgt" class="col-lg-4 control-label">Valor Pago:</label>
                            <div class="col-lg-8 valign-middle">
                                <?php echo formataMoeda(str_replace(",", ".", $saida['valor'])); ?>
                            </div>                                                
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label for="mensagem" class="col-lg-4 control-label">Descrição:</label>
                            <div class="col-lg-8 valign-middle">
                                <?php echo $saida['especifica']; ?>
                            </div>                                                
                        </div>
                    </div>
                </div>
            </div>

            <?php if($darf){ ?>
            <div class="alert alert-dismissable alert-info occ">
                <button type="button" class="close" data-dismiss="alert">×</button>
                DARF Vinculada com sucesso
            </div>
            <div class="panel panel-default darf">
                <div class="panel-heading">Vincular DARF</div>
                <div class="panel-body">                                                                
                    <div class="form-group">
                        <label for="data_pgt" class="col-lg-4 control-label">Saída:</label>
                        <div class="col-lg-8 valign-middle">
                            <?php echo montaSelect($saidas, $pai, "id='id_saida_pai' name='id_saida_pai' class='form-control'"); ?>
                        </div>                                                
                    </div>                                
                    <div class="form-group">
                        <label for="data_pgt" class="col-lg-4 control-label">Tipo da DARF:</label>
                        <div class="col-lg-8 valign-middle">
                            <?php echo montaSelect($tiposDarf, null, "id='tp_darf' name='tp_darf' class='form-control'"); ?>
                        </div>
                    </div>                    
                </div>
                <div class="panel-footer text-right">                    
                    <a href="javascript:;" class="btn btn-sm btn-primary" id="vinc" data-saida='<?php echo $codigo; ?>'>Vincular</a>
                </div>
            </div>                
            <?php } ?>

            <div class="panel panel-default">
                <div class="panel-heading">Anexos</div>
                <div class="panel-body">
                    <?php
                    while($row = mysql_fetch_assoc($anexo)){
                        $nome = $row['id_saida_file'].".".$row['id_saida'].$row['tipo_saida_file'];
                        if($saida['tipo'] != 170){
                    ?>
                        <div class="row">                            
                            <div class="col-lg-12 valign-middle">
                                <a href='../../comprovantes/<?php echo $nome; ?>' target='_blank'><?php echo $nome; ?></a>
                            </div>                            
                        </div>
                    <?php
                        }else{
                            $link = encrypt('ID='.$row['id_saida'].'&tipo=0');
                    ?>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-lg-12 valign-middle">
                                    <a href='../../novoFinanceiro/view/comprovantes.php?<?php echo $link; ?>' target='_blank'><?php echo $nome; ?></a>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </form>
    </body>
</html>