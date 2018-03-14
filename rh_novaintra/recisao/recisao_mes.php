<?php
include('../../conn.php');
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/curso.php');
include('../../classes/projeto.php');
include('../../classes/rescisao.php');

$Curso = new tabcurso();
$ClasPro = new projeto();

$regiao = $_REQUEST['regiao'];

$usuario = carregaUsuario();

$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$anoR = (isset($_REQUEST['ano_referente'])) ? $_REQUEST['ano_referente'] : date('Y');
$mesR = (isset($_REQUEST['mes_referente'])) ? $_REQUEST['mes_referente'] : date('m');

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Rescisão por Competência");
$breadcrumb_pages = array("Gestão de RH" => "../", "Rescisão" => "index.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Rescisão por Competência</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form name="form" action="" method="post" id="form1" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Rescisão por Competência</small></h2></div>
                </div>
            </div>
            <form action="" method="post" class="filtro form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <input type="hidden" name="filtro" value="1" />
                        <div class="form-group">
                            <label class="col-xs-1 control-label">Projeto:</label>
                            <div class="col-xs-5"><?=montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='form-control required[custom[select]]'")?></div>
                            <label class="col-xs-1 control-label">Competência:</label>
                            <div class="col-xs-5">
                                <div class="input-group">
                                    <?=montaSelect(mesesArray(),$mesR,"id='mes_referente' name='mes_referente' class='form-control'"); ?>
                                    <div class="input-group-addon">/</div>
                                    <?=montaSelect(anosArray(),$anoR,"id='ano_referente' name='ano_referente' class='form-control'"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" value="Consultar" class="btn btn-primary" name="consultar" />
                    </div>
                </div>
            </form>
            <?php
            if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                // Consulta de clts Aguardando Demissão
                $qr_aguardo = getCLTAguardando($regiao, $projetoR, $mesR, $anoR);
                $total_aguardo = mysql_num_rows($qr_aguardo);
                if (!empty($total_aguardo)) { ?>
                    <table class="table table-bordered table-condensed table-hover text-sm">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th colspan="6">
                                    <span style="color:#F90; font-size:16px;">&#8250;</span> Participantes aguardando a Rescis&atilde;o
                                </th>
                            </tr>
                            <tr class="info valign-middle">
                                <th>COD</th>
                                <th>NOME</th>
                                <th>FUNÇÃO</th>
                                <th>DATA DE ENTRADA</th>
                                <th>DATA DE SAÍDA</th>
                            </tr>
                        </thead>
                        <?php
                        while ($row_aguardo = mysql_fetch_array($qr_aguardo)) {                                                   
                            // Encriptografando a variável
                            $link = str_replace('+', '--', encrypt("{$regiao}&{$row_aguardo['id_clt']}")); ?>

                            <tr class="valign-middle">
                                <td><?= $row_aguardo['id_clt']; ?></td>
                                <td><a target="_blank" href="recisao2.php?tela=2&enc=<?= $link; ?>"><?= $row_aguardo['nome']; ?></a></td>
                                <td><?= $row_aguardo['nome_curso']; ?></td>
                                <td><?= $row_aguardo['data_admissao']; ?></td>
                                <td><?= $row_aguardo['data_demissao'];; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
                <table class="table table-bordered table-condensed table-hover text-sm">
                    <thead>
                        <tr class="bg-primary valign-middle">
                            <th colspan="9">
                                <span class="text-warning" style="font-size:16px;">&#8250;</span> Participantes Desativados
                            </th>
                        </tr>
                        <tr class="info valign-middle">
                            <th>COD</th>
                            <th>NOME</th>
                            <th>FUNÇÃO</th>
                            <th class="text-center">DATA DE ENTRADA</th>
                            <th class="text-center">DATA DE SAÍDA</th>
                            <th class="text-center">RESCIS&Atilde;O</th>
                            <th>VALOR</th>
                            <th class="text-center">COMPLEMENTAR</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>
                    <?php
                    // Consulta de Clts que foram demitidos
                    $qr_demissao = getCLTDemitidos($regiao, $projetoR, $mesR, $anoR);
                    $total_rescisao = mysql_num_rows($qr_demissao);
                    $total_geral = getTotalRescisao($regiao, $projetoR, $mesR, $anoR);
                    
                    while ($row_demissao = mysql_fetch_array($qr_demissao)) {
                        
                        $id_clt = $row_demissao['id_clt'];
                        $id_rescisao = $row_demissao['id_recisao'];
                        $id_complementar = $row_demissao['id_complementar'];                        
                        $valor_complementar = $row_demissao['valor_complementar'];
                        
                        if($valor_complementar == ''){
                            $valor_complementar = '';
                        }else{                            
                            $valor_complementar = "R$ ".number_format($row_demissao['valor_complementar'], 2, ',', '.');
                        }
                        
                        // encriptografando a variável
                        $link = str_replace('+', '--', encrypt("{$regiao}&{$id_clt}&{$id_rescisao}"));    
                        $link2 = str_replace('+', '--', encrypt("{$regiao}&{$id_clt}&{$id_complementar}"));    
                        
                        if (substr($row_demissao['data_proc'], 0, 10) >= '2013-04-04') {
                            $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                            $link_complementar = "nova_rescisao_2.php?enc=$link2";
                        } else {
                            $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                            $link_complementar = "nova_rescisao.php?enc=$link2";
                        } ?>
                        <tr class="valign-middle">
                            <td><?= $row_demissao['id_clt']; ?></td>
                            <td><?= $row_demissao['nome']; ?></td>
                            <td><?= $row_demissao['nome_curso']; ?></td>
                            <td class="text-center"><?= $row_demissao['data_admissao'] ?></td>
                            <td class="text-center"><?= $row_demissao['data_demissao'] ?></td>
                            <td class="text-center">
                                <?php if (empty($total_rescisao)) { ?>
                                <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)" />
                                <?php } else { ?>
                                    <a href="<?= $link_nova_rescisao; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a>
                                <?php } ?>
                            </td>
                            <td> R$ <?= number_format($row_demissao['total_liquido'], 2, ',', '.'); ?> </td>
                            <td class="text-center">
                                <?php if($row_demissao['id_complementar'] != '') { ?>
                                    <a href="<?= $link_complementar; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a>
                                <?php } ?>
                            </td>
                            <td><?=$valor_complementar?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="7" class="tot text-right">TOTAL: </td>
                        <td colspan="2" class="text-right">R$ <?= number_format($total_geral['total_geral'], 2, ',', '.'); ?></td>
                    </tr>
                </table>
            <?php } ?>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/thickbox/thickbox.js"></script>
    </body>
</html>