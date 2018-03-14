<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include "../../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();

$sql = "SELECT id_mov, 'rh_movimentos' tabela, descicao, campo_rescisao campo, categoria
        FROM rh_movimentos
        WHERE campo_rescisao != 0
        ORDER BY categoria ASC, campo ASC;";
$query = mysql_query($sql) or die(mysql_error());

while ($row = mysql_fetch_assoc($query)) {
    $arr[$row['categoria']][] = $row;
}

//MOVIMENTOS DE CRÉDITO QUE NÃO ESTÃO NA TABELA RH_MOVIMENTOS
$arr['CREDITO'][] = array('coluna' => 'a477', 'descicao' => 'Multa Art. 477, § 8º/CLT', 'campo' => '60', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'a479', 'descicao' => 'Multa Art. 479/CLT', 'campo' => '61', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'sal_familia', 'descicao' => 'Salário-Família', 'campo' => '62', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => '', 'descicao' => '13º Salário Exercício 0/12 avos', 'campo' => '64', 'categoria' => 'CREDITO', 'tabela' => '');
$arr['CREDITO'][] = array('coluna' => 'terceiro_ss', 'descicao' => '13º Salário (Aviso-Prévio Indenizado 0/12 avos)', 'campo' => '70', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'ferias_aviso_indenizado', 'descicao' => 'Férias (Aviso-Prévio Indenizado 0/12 avos', 'campo' => '71', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'fv_dobro', 'descicao' => 'Férias em Dobro', 'campo' => '72', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'um_terco_ferias_dobro', 'descicao' => '1/3 Férias em Dobro', 'campo' => '73', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'umterco_ferias_aviso_indenizado', 'descicao' => '1/3 Férias (Aviso Prévio Indenizado)', 'campo' => '75', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'lei_12_506', 'descicao' => 'Lei 12.506 0 dias', 'campo' => '95', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');
$arr['CREDITO'][] = array('coluna' => 'arredondamento_positivo', 'descicao' => 'Ajuste do Saldo Devedor', 'campo' => '99', 'categoria' => 'CREDITO', 'tabela' => 'rh_recisao');

//MOVIMENTOS DE DÉBITO QUE NÃO ESTÃO NA TABELA RH_MOVIMENTOS
$arr['DEBITO'][] = array('coluna' => 'adiantamento', 'descicao' => 'Adiantamento Salarial', 'campo' => '101', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('id_mov' => '292', 'descicao' => 'Adiantamento de 13º Salário', 'campo' => '102', 'categoria' => 'DEBITO', 'tabela' => 'rh_movimentos');
$arr['DEBITO'][] = array('coluna' => 'aviso_valor', 'descicao' => 'Aviso-Prévio Indenizado', 'campo' => '103', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('coluna' => 'a480', 'descicao' => 'Multa Art. 480/CLT', 'campo' => '104', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('coluna' => '', 'descicao' => 'Empréstimo em Consignação', 'campo' => '105', 'categoria' => 'DEBITO', 'tabela' => '');
$arr['DEBITO'][] = array('coluna' => 'inss_ss', 'descicao' => 'Previdência Social', 'campo' => '112.1', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('coluna' => 'inss_dt', 'descicao' => 'Previdência Social - 13º Salário', 'campo' => '112.2', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('coluna' => 'ir_ss', 'descicao' => 'IRRF', 'campo' => '114.1', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('coluna' => 'ir_dt', 'descicao' => 'IRRF sobre 13º Salário', 'campo' => '114.2', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');
$arr['DEBITO'][] = array('coluna' => 'ir_ferias', 'descicao' => 'IRRF Férias', 'campo' => '116', 'categoria' => 'DEBITO', 'tabela' => 'rh_recisao');

//echo '<pre>';
//print_r($arr);
//echo '</pre>';

//sort($array['CREDITO']);

?>

<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Posições dos Movimentos</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />

        <style>
            .categoria {
                text-align: center;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Posições dos Movimentos</h2></div>
            <div id="tabela">
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                    <thead>
                        <tr class="titulo">
                            <th>DESCRIÇÃO</th>
                            <th>POSIÇÃO</th>
                            <th>CATEGORIA</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach ($arr AS $categoria => $movimentos) { ?>
                            <?php foreach ($movimentos AS $key => $values) { ?>
                                <tr>
                                    <td><?= strtoupper($values['descicao']) ?></td>
                                    <td><?= $values['campo'] ?></td>
                                    <td><?= $values['categoria'] ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <div class="clear"></div>
    </div>
    <script src="../../js/jquery-1.10.2.min.js"></script>
    <script src="../../resources/js/bootstrap.min.js"></script>
    <script src="../../resources/js/tooltip.js"></script>
    <script src="../../resources/js/main.js"></script>
    <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
    <script src="../../js/global.js" type="text/javascript"></script>
</body>
</html>