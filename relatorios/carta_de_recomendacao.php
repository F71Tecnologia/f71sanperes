<?php
include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$id_clt = ($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : '';

$arrClt = montaQuery('rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade', "A.*, B.nome curso, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') data_entrada, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade", "id_clt = $id_clt");

$arrClt[1]['unidade'] = ucwords($arrClt[1]['unidade']);
$arrClt[1]['curso'] = ucwords($arrClt[1]['curso']);

$nome = $arrClt[1]['nome'];
$ctps = "{$arrClt[1]['campo1']} série {$arrClt[1]['serie_ctps']} {$arrClt[1]['uf_ctps']}";

if (!empty($arrClt[1]['data_demi'])) {
    $txt = "foi nosso funcionário na {$arrClt[1]['unidade']}, de {$arrClt[1]['data_entrada']} até {$arrClt[1]['data_demi']}, exercendo como cargo a função de {$arrClt[1]['curso']}.";
} else {
    $txt = "é nosso funcionário na {$arrClt[1]['unidade']}, de {$arrClt[1]['data_entrada']} até o presente momento, exercendo como cargo a função de {$arrClt[1]['curso']}.";
}

$mes = mesesArray(date('m'));
//$ano = anosArray(date('Y'));
//exit();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Carta de Recomendação</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <style>
            * { margin: 0; padding: 0; }
        </style>
        <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/style-print.css" rel="stylesheet">
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>

    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="text-center"> 
                    <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                    <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
                </div>
            </div>
        </nav>
        <div class="pagina">
            <br>
            <br>
            <div class="text-center">
                <img src="../imagens/logomaster6.gif" alt="logo"><br>
            </div>
            <br>
            <br>
            <h4 class="text-center">DECLARAÇÃO</h4>
            <br>
            <br>
            <br>
            <br>
            <p class="text-justify">
                Declaramos para devidos fins, que o(a) Sr(a) <strong><?= $nome ?></strong>, portador(a) da CTPS <?= $ctps ?>, <?= $txt ?>
            </p>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p class="text-justify">
                Por ser verdade, assinamos a presente declaração. 
            </p>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p class="text-right">Rio de Janeiro, <?= date("d") ?> de <?= $mes ?> de  <?= date('Y') ?>. </p>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div class="row">
                <div class="col-xs-7 text-center">
                    <br>
                    _______________________________________________
                    <br>
                    <span class="text-sm">
                        Assinatura e identificação do responsável pela empresa
                    </span>
                </div>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <div class="row">
                <div class="col-xs-12 text-sm">
                    <strong>Instituto dos Lagos - Rio</strong><br>
                    Rua do Carmo, 9 - 10º Andar - CEP: 20011-020 - Centro - Rio de Janeiro - RJ<br>
                    Organização Social - CNPJ: 07.813.739/0001-61<br>
                    Fone: (21)2725-5602 | www.institutolagosrio.com.br<br>
                </div>
            </div>
        </div>
    </body>
</html>