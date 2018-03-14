<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include ("../conn.php");
include ("../wfunction.php");
include ("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$rowUser = montaQueryFirst("funcionario", "id_master", "id_funcionario = '{$_COOKIE['logado']}'");
$currentUser = current($rowUser);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$qrAssoc = "SELECT A.id_entradasaida, B.cod, B.nome AS nomeES, A.id_plano_contas, C.classificador, C.nome AS nomePC, BC.nome AS agencia, PR.nome AS nomeprojeto
FROM entradaesaida_plano_contas_assoc AS A
LEFT JOIN entradaesaida AS B ON (A.id_entradasaida = B.id_entradasaida)
LEFT JOIN plano_de_contas AS C ON (A.id_plano_contas = C.id_plano_contas)
LEFT JOIN bancos AS BC ON (A.id_projeto = BC.id_projeto)
LEFT JOIN projeto AS PR ON (PR.id_projeto = A.id_projeto)

ORDER BY A.id_entradasaida ,A.id_plano_contas ASC;";

$assoc = mysql_query($qrAssoc);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "35", "area" => "Gestão Contabil", "ativo" => "Exportação de Arquivos", "id_form" => "form1");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: FINANCEIRO - RECURSOS HUMANOS</title>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Contabil</title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<!--        <script src="../jquery/jquery-1.4.2.min.js"></script>-->
        <script>
            $(function() {
                $("#voltar").click(function() {
                    window.history.go(-1);
                });
            });
        </script>
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contas-header">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Gestão Contabil <small> - Relatório do Relacionamento do Plano de Contas</small></h2>
                    </div>
                    <div class="row">
                        <form action="" method="post" name="form1" id="form1">
                            <table class="table table-condensed table-striped" id="tabela">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($rowAssoc = mysql_fetch_assoc($assoc)){ 
                                        if ($codESant != $rowAssoc['id_entradasaida']) { ?>
                                            <tr>
                                                <td><?php echo $rowAssoc['id_entradasaida'] ?></td>
                                                <td><?php echo strtoupper($rowAssoc['nomeES']); ?></td>
                                            </tr>
                                            <?php
                                                $codESant = $rowAssoc['id_entradasaida'];
                                        } ?>
                                        <tr>
                                            <td><?php echo $rowAssoc['classificador'] ?></td>
                                            <td><?php echo strtoupper($rowAssoc['nomePC']."  ".$rowAssoc['nomeprojeto']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <input type="button" class="button" value="Voltar" name="voltar" id="voltar" />
                        </form>
                    </div>
                    <div class="row"/>
                    </div>
                    <?php include_once '../template/footer.php'; ?>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

    </body>
    
</html>