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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "ativo"=>"SEFIP","id_form"=>"form1");

$filtro = false;

$sefip = new SefipClass();
$sefip->getAnosSefip($master);

$primeiro_ano = $sefip->ano_ini;
$ultimo_ano = $sefip->ano_fim;

$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : $ultimo_ano;

$qry = $sefip->getListaSefip($anoR, $master);
$total = mysql_num_rows($qry);

$qry_decimo = $sefip->getListaSefip($anoR, $master, 1);
$res_decimo = mysql_fetch_assoc($qry_decimo);
$total_decimo = mysql_num_rows($qry_decimo);

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
                
//                $(".bt-image").click(function(){
//                    var action = $(this).data("action");
//                    var key = $(this).data("key");
//                    
//                    if(action === "gerar"){
//                        $.ajax({
//                            type: 'POST',
//                            dataType: "json",
//                            url: "controle.php",
//                            data: {
//                                folha: key,
//                                method: "gerar"
//                            }
//                        });
//                    }
//                });
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
                        <?php while($res = mysql_fetch_assoc($qry)){ ?>
                        <div class="panel sanf acord_sefip">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-example" href="#mes_<?php echo $res['mes']; ?>">
                                <div class="panel-heading">                                
                                        <?php echo mesesArray($res['mes']); ?>                                    
                                    <div class="pull-right">
                                        <?php echo $res['tot_participantes']; ?> Participantes
                                    </div>
                                </div>
                            </a>
                            <div id="mes_<?php echo $res['mes']; ?>" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
                                    <table class='table table-hover table-striped'>
                                        <thead>
                                            <tr>
                                                <th>Projeto</th>
                                                <th>Qtd. de Participantes</th>
                                                <th colspan="3">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_sefip = $sefip->getListaSefipIndividual($anoR, $master, $res['mes']);
                                            
                                            while($res_sefip = mysql_fetch_assoc($sql_sefip)){
                                            ?>
                                            <tr>
                                                <td><?php echo $res_sefip['projeto_nome']; ?></td>
                                                <td><?php echo $res_sefip['qtd_participantes']; ?></td>
                                                <td><a href="controle.php?folha=<?php echo $res_sefip['id_folha'] ?>"><img src="../../imagens/icones/icon-new.gif" title="Gerar" class="bt-image acoes_sefip" data-action="gerar"></a></td>
<!--                                                <td><img src="../../imagens/icones/icon-download.png" title="Baixar" class="bt-image acoes_sefip"  data-action="visualizar" data-key="<?php // echo $res_sefip['id_folha']; ?>" data-projeto="<?php // echo $res_sefip['id_projeto']; ?>" data-mes="<?php // echo $res_sefip['mes']; ?>" data-terceiro="<?php // echo $res_sefip['folha_terceiro']; ?>"></td>
                                                <td><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image acoes_sefip"  data-action="excluir" data-key="<?php // echo $res_sefip['id_folha']; ?>" data-projeto="<?php // echo $res_sefip['id_projeto']; ?>" data-mes="<?php // echo $res_sefip['mes']; ?>" data-terceiro="<?php // echo $res_sefip['folha_terceiro']; ?>"></td>-->
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php if($total_decimo > 0){ ?>
                        <div class="panel sanf acord_sefip">
                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion-example" href="#mes_13">
                                <div class="panel-heading">                                
                                        Décimo Terceiro
                                    <div class="pull-right">
                                        <?php echo $res_decimo['tot_participantes']; ?> Participantes
                                    </div>
                                </div>
                            </a>
                            <div id="mes_13" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
                                    <table class='table table-hover table-striped'>
                                        <thead>
                                            <tr>                                                
                                                <th>Projeto</th>                                    
                                                <th>Qtd. de Participantes</th>                                                            
                                                <th colspan="3">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql_sefip_decimo = $sefip->getListaSefipIndividual($anoR, $master, $res_decimo['mes'], 1);
                                            
                                            while($res_sefip_decimo = mysql_fetch_assoc($sql_sefip_decimo)){
                                            ?>
                                            <tr>
                                                <td><?php echo $res_sefip_decimo['projeto_nome']; ?></td>
                                                <td><?php echo $res_sefip_decimo['qtd_participantes']; ?></td>
                                                <td><a href="controle.php?folha=<?php echo $res_sefip_decimo['id_folha'] ?>"><img src="../../imagens/icones/icon-new.gif" title="Gerar" class="bt-image acoes_sefip" data-action="gerar"></a></td>
<!--                                                <td><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image acoes_sefip" data-action="visualizar" data-key="<?php // echo $res_sefip['id_folha']; ?>" data-projeto="<?php // echo $res_sefip['id_projeto']; ?>" data-mes="<?php // echo $res_sefip['mes']; ?>" data-terceiro="<?php // echo $res_sefip['folha_terceiro']; ?>"></td>
                                                <td><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image acoes_sefip" data-action="excluir" data-key="<?php // echo $res_sefip['id_folha']; ?>" data-projeto="<?php // echo $res_sefip['id_projeto']; ?>" data-mes="<?php // echo $res_sefip['mes']; ?>" data-terceiro="<?php // echo $res_sefip['folha_terceiro']; ?>"></td>-->
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