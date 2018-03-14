<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/ViagemClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objViagem = new ViagemClass();

$result = $objViagem->getViagem();
$total = mysql_num_rows($result);

$nome_pagina = "Módulo Viagem";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo" => $nome_pagina);
//$breadcrumb_pages = $breadcrumb_pages_array[$caminho];
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?php echo $nome_pagina ?></title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?php echo $nome_pagina ?></small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default">
                    <div class="panel-body text-right">
                        <a href="rel_despesas.php" class="button btn btn-sm btn-warning">Relatório de Despesas</a>
                        <a href="form.php" class="button btn btn-sm btn-success"><span class="fa fa-plus-circle"></span> Solicitar Viagem</a>
                    </div>
                </div>
            </form>
            
            <?php if ($total > 0) { ?>
            
            <table class='table table-hover table-striped table-bordered table-condensed text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>COD</th>
                        <th>Nome</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Período</th>
                        <th>Trajeto</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysql_fetch_assoc($result)) {
                        $nome = ($row['funcionario'] == 1) ? $row['nome_fun'] : $row['nome_ree'];
                        $codigo = sprintf("%05d",$row['id_viagem']);
                        $print = [];
                        
                        if($row['status'] == 1) {
                            $status = 'Aguardando aprovação';
                            $back_status = 'primary';
//                            $print = "<button type='button' class='btn btn-xs bt-image' title='Visualizar' data-type='gerar_doc' data-key='{$row['id_viagem']}'><i class='fa fa-print'></i></button>";
//                            $print = "<a href='doc_solicitacao.php?id={$row['id_viagem']}' target='_blank' class='btn btn-default btn-xs' title='Formulário Solicitação'><i class='fa fa-print'></i></a>";
                        } elseif($row['status'] == 2) {
                            $status = 'Gerar Acerto';
                            $back_status = 'warning';
                            $print = '';
                        } elseif($row['status'] == 3) {
                            $status = 'Aguardando aprovação acerto';
                            $back_status = 'info';
//                            $print = "<a href='doc_acerto.php?id={$row['id_viagem']}' target='_blank' class='btn btn-default btn-xs' title='Formulário Acerto'><i class='fa fa-print'></i></a>";
                        } elseif($row['status'] == 4) {
                            $status = 'Acertado';
                            $back_status = 'success';
                            $print = '';
                        } elseif($row['status'] == 5) {
                            $status = 'Acertado Recusado';
                            $back_status = 'danger';
                            $print = '';
                        } else {
                            $status = 'Recusado';
                            $back_status = 'danger';
                            $cod = $codigo;
                            $print = '';
                        }
                        
                        $print[] = "<a href='doc_solicitacao.php?id={$row['id_viagem']}' target='_blank' class='btn btn-default btn-xs' title='Formulário Solicitação'><i class='fa fa-print'></i></a>";
                        if($row['status'] > 2) { $print[] = "<a href='doc_acerto.php?id={$row['id_viagem']}' target='_blank' class='btn btn-info btn-xs' title='Formulário Acerto'><i class='fa fa-print'></i></a>"; }
                    ?>
                    <tr id="<?php echo $row['id_viagem']; ?>">
                        <td><?php echo $codigo; ?></td>
                        <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                        <td><?php echo formataMoeda($row['valor']); ?></td>
                        <td><?php echo $row['data']; ?></td>
                        <td><?php echo "{$row['data_ini']} até {$row['data_fim']}" ?></td>
                        <td><?php echo $row['trajeto'] ?></td>
                        <td><span class="label label-<?php echo $back_status; ?>"><?php echo $status; ?></span></td>
                        <td class="text-center">
                            <button type="button" class='btn btn-xs btn-primary verViagem' data-id="<?= $row['id_viagem'] ?>" data-toggle="tooltip" title="Detalhe">
                                <i class="fa fa-search"></i>
                            </button>
                            <?php echo implode('&nbsp;',$print); ?>
                            <?php if($row['status'] == 1) { ?><a href="form.php?i=<?php echo $row['id_viagem'] ?>" class="btn btn-xs btn-warning editarViagem" data-key="<?php echo $row['id_viagem'] ?>"><i class="fa fa-pencil"></i></a><?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php } ?>
            <?php include("../../template/footer.php"); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/financeiro/reembolso.js"></script>
        <script src="../../resources/js/financeiro/index.js?<?=date("Ymd")?>"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({ promptPosition : "topRight" });
            });
        </script>
    </body>
</html>