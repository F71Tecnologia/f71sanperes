<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include ("../../conn.php");
include ("../../wfunction.php");
include ("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$rowUser = montaQueryFirst("funcionario", "id_master", "id_funcionario = '{$_COOKIE['logado']}'");
$currentUser = current($rowUser);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$usuario = carregaUsuario();
$master = $usuario['id_master'];

if (isset($_REQUEST['relatorio']) && $_REQUEST['relatorio'] == 'Listar') {
//    exit(print_array($_REQUEST));
    $projeto = $_REQUEST['prosoft_relatorio']; 
}


$qrAssoc = "SELECT DISTINCT D.nome AS nomeprojeto, A.cod AS cod, C.acesso AS classificador, C.nome AS nomePC, A.descicao AS nomeES  	
            FROM rh_movimentos A
            INNER JOIN contabil_folha_prosoft B ON(B.id_codigo = A.cod AND B.id_projeto = $projeto)
            INNER JOIN plano_de_contas C ON (C.id_plano_contas = B.id_plano_de_conta )
            INNER JOIN projeto D ON (D.id_projeto = $projeto)
            UNION
            SELECT D.nome AS nomeprojeto, A.id_entradasaida AS cod, C.acesso AS classificador, C.nome AS nomePC, A.nome AS nomeES 
            FROM entradaesaida AS A
            LEFT JOIN entradaesaida_plano_contas_assoc AS B ON (B.id_entradasaida = A.id_entradasaida AND B.id_projeto = $projeto)
            LEFT JOIN plano_de_contas AS C ON(C.id_plano_contas = B.id_plano_contas)
            LEFT JOIN projeto AS D ON (D.id_projeto = $projeto)
            WHERE B.id_projeto = $projeto
            ORDER BY nomePC ASC";

$assoc = mysql_query($qrAssoc);

while ($row = mysql_fetch_assoc($assoc)) {
    $lista[$row['id_projeto']." ".$row['nomeprojeto']][] = $row;
    //$lista[$row['id_projeto']][nome] = $row['id_projeto']." - ".$row['nomeprojeto'];
}
//    echo '<pre>';
//  print_r($lista);
//    echo '</pre>';

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "36", "area" => "Gestão Contabil", "ativo" => "Exportação de Arquivos", "id_form" => "form1");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: FINANCEIRO - RECURSOS HUMANOS</title>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Contabil</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css///dist/css/bootstrap3/bootstrap-switch.css" rel="stylesheet">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
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
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contas-header">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Gestão Contabil <small> - Relatório do Relacionamento do Plano de Contas</small></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form action="" method="post" name="form1" id="form1">
                        <?php if (!empty($lista)) {
                        foreach ($lista as $empresa => $conta) { ?>    
                        <table class="table table-striped table-hover table-condensed text-sm"> 
                                <thead>
                                    <tr>
                                        <th colspan="4" class="warning"><?= $empresa ?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-sm">SISTEMA</th>
                                        <th colspan="2" class="text-sm">PROSOFT</th>
                                    </tr>
                                    <tr>
                                        <th width="10%">Código</th>
                                        <th width="25%">Descrição</th>
                                        <th width="10%">Acesso</th>
                                        <th width="25%">Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>    
                                    <?php foreach ($conta as $contas) { ?>
                                    <tr>
                                        <td><?= $contas['cod'] ?></td>
                                        <td><?= strtoupper($contas['nomeES']) ?></td>
                                        <td><?= $contas['classificador'] ?></td>
                                        <td><?= strtoupper($contas['nomePC']) ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <hr>
                            <?php } 
                        } ?>
                        <input type="button" class="btn btn-default" value="Voltar" name="voltar" id="voltar" />
                    </form>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>    
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>

    </body>
    
</html>