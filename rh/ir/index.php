<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit();
}

include("../../conn.php");
include("../../classes/funcionario.php");
include("../../wfunction.php");
include('../../classes/global.php');

$meses = mesesArray();
$anos = anosArray();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];

if(isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])){
    $ano = $_REQUEST['ano'];
    
    //VERIFICA SE EXISTE FOLHA DE CLT
    $qr_folha = mysql_query("SELECT A.*,B.nome as projeto FROM rh_folha AS A 
                                LEFT JOIN projeto AS B ON (A.projeto = B.id_projeto)
                                WHERE A.status = '3' AND A.regiao = '$id_regiao' AND A.ano = $ano ORDER BY A.projeto,A.ano,A.mes ASC");
    
    $numero_folha = mysql_num_rows($qr_folha);
    //VERIFICA SE EXISTE FOLHA DE COOPERADO
    $qr_folha_cooperado = mysql_query("SELECT * FROM folhas AS A 
                                        LEFT JOIN projeto AS B ON (A.projeto = B.id_projeto)
                                        WHEREA A.status = '3' AND A.regiao = '$id_regiao' AND A.contratacao = '3' AND A.ano = $ano ORDER BY A.projeto,A.ano,A.mes ASC");
    $numero_folha_cooperado = mysql_num_rows($qr_folha_cooperado);

    $breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "IRRF");
    $breadcrumb_pages = array("Principal RH" => "../../rh/principalrh.php");
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: DARF IRRF</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="glyphicon glyphicon-user"></span> - Recusos Humanos<small> - DARF IRRF</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading">Visualizar as DARF IRRF</div>
                    <div class="panel-body">
                        <div class="form-group datas">
                            <label for="select" class="col-lg-2 control-label">Ano: </label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($anos, date('Y'), array('name' => "ano", 'id' => 'ano', 'class' => 'input form-control')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" name="filtrar" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>
            </form>
            
            <?php if (!empty($numero_folha)) { ?>
                <table class='table table-hover' id="tbRelatorio">
                    <thead>
                        <tr>
                            <th colspan="6">FOLHAS CLT</th>
                        </tr>
                        <tr>
                            <th>Folha - Projeto</th>
                            <th>Processamento</th>
                            <th>Competência</th>
                            <th>Inicio/Fim</th>
                            <th>Qnt Clts</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  while ($folha = mysql_fetch_assoc($qr_folha)) { ?>
                        <tr>
                            <td><?php echo $folha['id_folha'] . " - " . $folha['projeto']; ?></td>
                            <td><?php echo implode("/", array_reverse(explode("-", $folha['data_proc']))); ?></td>
                            <td><?php echo $meses[(int)$folha['mes']]; ?></td>
                            <td><?php echo implode("/", array_reverse(explode("-", $folha['data_inicio']))); ?> até <?php echo implode("/", array_reverse(explode("-", $folha['data_fim']))); ?></td>
                            <td align="center"><?= $folha['clts'] ?></td>
                            <td align="center"><a href="ir.php?regiao=<?= $usuario['id_regiao'] ?>&folha=<?= $folha['id_folha'] ?>&tipo=2" target="_blank" title="Gerar IRRF"><img src="imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>

            <?php if (!empty($numero_folha_cooperado)) { ?>
                <table class='table table-hover' id="tbRelatorio">
                    <thead>
                        <tr>
                            <th colspan="6">FOLHAS COOPERADOS</th>
                        </tr>
                        <tr>
                            <th>Folha - Projeto</th>
                            <th>Processamento</th>
                            <th>Competência</th>
                            <th>Inicio/Fim</th>
                            <th>Qnt Clts</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  while ($folha_cooperado = mysql_fetch_assoc($qr_folha_cooperado)) { ?>
                            <tr>
                                <td><?php echo $folha_cooperado['id_folha'] . " - " . $folha_cooperado['projeto']; ?></td>
                                <td><?php echo implode("/", array_reverse(explode("-", $folha_cooperado['data_proc']))); ?></td>
                                <td><?php echo $meses[(int) $folha_cooperado['mes']]; ?></td>
                                <td><?php echo implode("/", array_reverse(explode("-", $folha_cooperado['data_inicio']))); ?> até <?php echo implode("/", array_reverse(explode("-", $folha_cooperado['data_fim']))); ?></td>
                                <td align="center"><?= $folha_cooperado['participantes'] ?></td>
                                <td align="center"><a href="ir.php?regiao=<?= $usuario['id_regiao'] ?>&folha=<?= $folha_cooperado['id_folha'] ?>&tipo=3" target="_blank" title="Gerar IRRF"><img src="imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
                            </tr>
                        <?php } ?>
                </table>
            <?php } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/financeiro/entrada.js"></script>
        <script src="../../js/global.js"></script> 
    </body>
</html>