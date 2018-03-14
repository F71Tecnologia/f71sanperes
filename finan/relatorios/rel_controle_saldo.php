<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$banco = new Banco();

$id_regiao = $usuario['id_regiao'];
$user_logado = $usuario['id_funcionario'];
$user_permitido = array(64,87,5,9,27,77,75);

$result = $banco->getControleSaldo($id_regiao);
$total_controle = mysql_num_rows($result);

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Controle de Saldos");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Controle de Saldos</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Controle de Saldos</small></h2></div>
            <?php if ($total_controle > 0) { ?>
            
            <table class='table table-hover table-striped table-condensed table-bordered text-sm valign-middle'>
                <thead>
                    <tr class="bg-primary">
                        <th>COD</th>
                        <th>Banco</th>
                        <th>Agência</th>
                        <th>Conta</th>
                        <th>Projeto</th>
                        <th>Saldo Parcial</th>
                        <?php if(in_array($user_logado, $user_permitido)){ ?>
                        <th>Qtd. Saídas Hoje</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysql_fetch_assoc($result)) {
                        $saidas_hj = mysql_num_rows($banco->getSaidaBanco($row['id_banco']));
                        
                        if(in_array($user_logado, $user_permitido)){ ?>
                            <tr>
                                <td><?php echo $row['id_banco']; ?></td>
                                <td><?php echo acentoMaiusculo($row['nome_banco']); ?></td>
                                <td><?php echo $row['agencia']; ?></td>
                                <td><?php echo $row['conta']; ?></td>
                                <td><?php echo $row['nome_projeto']; ?></td>
                                <td><?php echo formataMoeda(str_replace(",", ".", $row['saldo'])); ?></td>
                                <td><?php echo $saidas_hj; ?></td>
                            </tr>
                            
                    <?php }else{ ?>
                            <tr>
                                <td><?php echo $row['id_banco']; ?></td>
                                <td><?php echo acentoMaiusculo($row['nome_banco']); ?></td>
                                <td><?php echo $row['agencia']; ?></td>
                                <td><?php echo $row['conta']; ?></td>
                                <td><?php echo $row['nome_projeto']; ?></td>
                                <td><?php echo formataMoeda(str_replace(",", ".", $row['saldo'])); ?></td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php
            } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/reembolso.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>