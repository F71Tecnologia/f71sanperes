<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/SefipClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "ativo"=>"SEFIP","id_form"=>"form1");
$breadcrumb_pages = array("Gest�o de RH"=>"/intranet/rh/principalrh.php");

$filtro = false;

$sefip = new SefipClass();
$sefip->getAnosSefip($master);

$primeiro_ano = $sefip->ano_ini;
$ultimo_ano = $sefip->ano_fim;

$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : $ultimo_ano;

$qry = $sefip->getListaSefip($anoR, $master);
$total = mysql_num_rows($qry);
//$qry = $sefip->getListaSefipByCnpj($anoR, $master);
//$total = mysql_num_rows($qry);

//$qry_decimo = $sefip->getListaSefip($anoR, $master, 1);
//$res_decimo = mysql_fetch_assoc($qry_decimo);
//$total_decimo = mysql_num_rows($qry_decimo);

while($rr_13 = mysql_fetch_array($qry_decimo)){
    $total_participantes += $rr_13['tot_participantes']; 
    $mes_13 = $rr_13['mes'];
}

if(isset($_REQUEST['filtrar'])){
    $filtro = true;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: SEFIP</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.png" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script>
            $(function(){
                $("#ano").change(function(){
                    $("#filt").click();
                });
                
                BootstrapDialog.confirm = function(message, callback) {
                    new BootstrapDialog({
                        title: 'Confirma��o de Exclus�o',
                        message: message,
                        closable: false,
                        data: {
                            'callback': callback
                        },
                        buttons: [{
                                label: 'Cancelar',
                                action: function(dialog) {
                                    typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                                    dialog.close();
                                }
                            }, {
                                label: 'OK',
                                cssClass: 'btn-primary',
                                action: function(dialog) {
                                    typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                                    dialog.close();
                                }
                            }]
                    }).open();
                };
                
                $(".bt-image").click(function(){
                    var action = $(this).data("action");
                    var key = $(this).data("key");
                    
                    if(action == "excluir"){
                        BootstrapDialog.confirm('Deseja realmente excluir esse sefip?', function(result) {
                            if (result) {
                                $.ajax({
                                    url:"delete_sefip.php",
                                    type:"POST",
                                    dataType:"json",
                                    data:{
                                        id:key,
                                        method:"exclui_sefip"
                                    },
                                    success:function(data){
                                        if(data.status == 1){
                                            $("#"+key+"_download_sefip").hide();
                                            $("#"+key+"_exclui_sefip").hide();
                                            $("#"+key+" #gera_sefip").removeClass('hide');
                                        }
                                    }
                                });
                            }
                        });
                    }
//                    
//                    if(action === "gerar"){
//                        $.ajax({
//                            type: 'POST',
//                            dataType: "json",
//                            url: "controle.php",
//                            data: {
//                                folha: key,
//                                method: "gerar"
//                            },
//                            success: function(data) {
//                                if(data.status == 1){
//                                    load('arquivos/download.php?file='+data.file);
//                                }
//                            }
//                        });
//                    }
                });
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Recursos Humanos</h2></div>
        
        <div id="content">
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">                
                <input type="hidden" name="home" id="home" value="" />
                <input type="hidden" name="ano_sel" id="ano_sel" value="<?php echo $anoR; ?>" />
                
                <fieldset>
                    <legend>SEFIP</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Ano</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(AnosArray($primeiro_ano, $ultimo_ano), $anoR, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?> 
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right margin_r20">
                            <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary hide" />
                        </div>
                    </div>
                </fieldset>                                
            </form>
            
            <?php            
            if ($total > 0) {
            ?>
            
                <div class="col-md-12">
                    <div class="panel-group" id="accordion-example">
                        <?php 
                        while ($res = mysql_fetch_assoc($qry)) { ?>
                        <div class="panel sanf acord_sefip">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-example" href="#mes_<?php echo $res['mes']; ?>">
                                <div class="panel-heading">
                                        <?= ($res['terceiro'] == 2) ? mesesArray($res['mes']) : 'D�CIMO TERCEIRO' ?>
                                        <!-- <?php if($res['terceiro'] == 1) { if($res['tipo_terceiro'] == 1) { echo '1� PARCELA'; } else if($res['tipo_terceiro'] == 2) { echo '2� PARCELA'; } else { echo 'INTEGRAL'; } }?>-->
                                    <div class="pull-right">
                                        <?= $res['tot_participantes']; ?> Participantes
                                    </div>
                                </div>
                            </a>
                            <div id="mes_<?php echo $res['mes']; ?>" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
                                    <table class='table table-striped table-hover table-condensed table-bordered'>
                                        <thead>
                                            <tr class="bg-primary valign-middle">
                                                <th>INSTITUI��O</th>
                                                <th>QTD. DE PARTICIPANTES</th>
                                                <th class="text-center">
                                                    <a href="controle2.php?mes=<?= $res['mes'] ?>&ano=<?= $anoR ?>&lote=true"><i data-type="visualizar" class="tooo btn btn-xs btn-warning fa fa-sign-in bt-image acoes_sefip" title="Gerar" data-action="gerar" data-ano="<?php echo $res_sefip['ano']; ?>" data-mes="<?php echo $res_sefip['mes']; ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar"></i></a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_sefip = $sefip->getListaSefipByCnpj($anoR, $master, $res['mes'], $res['terceiro']);
//                                            
                                            while($res_sefip = mysql_fetch_assoc($sql_sefip)){
//                                                $tot_sefip = mysql_num_rows($sefip->getSefip($res_sefip['id_folha']));
//                                                $dir_sefip = "arquivos/";
//                                                $file_sefip = "SEFIP_{$res_sefip['id_projeto']}_{$res_sefip['mes']}_{$res_sefip['ano']}.RE";
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= "{$res_sefip['cnpj']} - {$res_sefip['projetos']}"; ?>
                                                </td>
                                                <td>
                                                    <?php echo $res_sefip['tot_participantes']; ?>
                                                </td>
                                                <td class="text-center">                                                                                                                                                            
                                                    <a href="controle2.php?mes=<?= $res['mes'] ?>&ano=<?= $anoR ?>&terceiro=<?= $res['terceiro'] ?>&cnpj=<?= $res_sefip['cnpj'] ?>"><i data-type="visualizar" class="tooo btn btn-xs btn-warning fa fa-sign-in bt-image acoes_sefip" title="Gerar" data-action="gerar" data-ano="<?php echo $res_sefip['ano']; ?>" data-mes="<?php echo $res_sefip['mes']; ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if($anoR < date('Y') || date('m') == 12){ ?>
                        <div class="panel sanf acord_sefip">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-example" href="#mes_13">
                                <div class="panel-heading">                                
                                        D�cimo Terceiro
                                    <div class="pull-right">
                                        <?php echo $total_participantes; ?> Participantes
                                    </div>
                                </div>
                            </a>
                            <div id="mes_13" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">                                    
                                    <table class='table table-striped table-hover table-condensed table-bordered'>
                                        <thead>                                            
                                            <tr class="bg-primary valign-middle">
                                                <th>PROJETO</th>
                                                <th>QTD. DE PARTICIPANTES</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_sefip_decimo = $sefip->getListaSefipByCnpj($anoR, $master, '11, 12', 1);
                                            
                                            while($res_sefip_decimo = mysql_fetch_assoc($sql_sefip_decimo)){
//                                                $tot_sefip_decimo = mysql_num_rows($sefip->getSefip($res_sefip_decimo['id_folha']));
//                                                $dir_sefip_decimo = "arquivos/";
//                                                $file_sefip_decimo = "SEFIP_{$res_sefip_decimo['id_projeto']}_{$res_sefip_decimo['mes']}_{$res_sefip_decimo['ano']}_DT.RE";
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= "{$res_sefip_decimo['cnpj']} - {$res_sefip_decimo['projetos']}"; ?>
                                                </td>
                                                <td>
                                                    <?php echo $res_sefip_decimo['tot_participantes']; ?>
                                                </td>
                                                <td class="text-center">                                                                                                                                                            
                                                    <a href="controle2.php?mes=13&ano=<?= $anoR ?>&terceiro=1&cnpj=<?= $res_sefip_decimo['cnpj'] ?>"><i data-type="visualizar" class="tooo btn btn-xs btn-warning fa fa-sign-in bt-image acoes_sefip" title="Gerar" data-action="gerar" data-ano="<?php echo $res_sefip_decimo['ano']; ?>" data-mes="<?php echo $res_sefip_decimo['mes']; ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="Gerar"></i></a>
                                                </td>                                             
                                            </tr>
                                            <?php } ?>
                                        </tbody>                                                                                
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                </div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php } ?>
            
            <div class="clear"></div>
            
            <?php include_once '../../template/footer.php'; ?>
            
        </div>
        </div>
    </body>
</html>