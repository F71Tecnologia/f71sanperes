<?php
include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$id_clt = ($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : '';

$arrClt = montaQuery('rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade LEFT JOIN projeto D ON A.id_projeto = D.id_projeto', "A.*, B.nome curso, D.nome projeto, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') data_entrada, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade", "id_clt = $id_clt");

$arrClt[1]['unidade'] = ucwords($arrClt[1]['unidade']);
$arrClt[1]['projeto'] = ucwords($arrClt[1]['projeto']);
$arrClt[1]['curso'] = ucwords($arrClt[1]['curso']);

$nome = $arrClt[1]['nome'];
$cpf = $arrClt[1]['cpf'];
$ctps = "{$arrClt[1]['campo1']}/{$arrClt[1]['serie_ctps']}-{$arrClt[1]['uf_ctps']}";

if (!empty($arrClt[1]['data_demi'])) {
    $txt = "foi nosso funcionário na {$arrClt[1]['unidade']}, de {$arrClt[1]['data_entrada']} até {$arrClt[1]['data_demi']}, exercendo como cargo a função de {$arrClt[1]['curso']}.";
} else {
    $txt = "é nosso funcionário na {$arrClt[1]['unidade']}, de {$arrClt[1]['data_entrada']} até o presente momento, exercendo como cargo a função de {$arrClt[1]['curso']}.";
}

$motivo = $_REQUEST['motivo'];

$mes = mesesArray(date('m'));
//$ano = anosArray(date('Y'));
//exit();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Carta de Declaração de INSS</title>
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
            <p class="text-justify">
                Rio de Janeiro, <?= date("d") ?>/<?= $mes ?>/<?= date('Y') ?>
            </p>
            <br>
            <br>
            <h4 class="text-center"><strong><u>DECLARAÇÃO</u></strong></h4>
            <br>
            <p class="text-justify">
                Considerando o dispositivo da legislação Previdenciária Social Lei nº 10.666, Decreto nº3.048 e Instrução Normativa MPS/SRO nº 3 de julho de 2005 - DOU 15/07/05 que
                regularizavam a contribuição do segurado que exerce sua atividade em mais de uma empresa, DECLARAMOS que (o) abaixo identificado (a) é nosso <?= $arrClt[1]['curso'] ?>
                em regime de contratação CLT.
                <br>
                <br>
                <?= $motivo ?>
                <br>
                <br>
                NOME: <?= $nome ?>
                <br>
                CPF: <?= $cpf ?>
                <br>
                <br>
                Ocorrendo quaisquer alterações na nossa situação de trabalho, caberá ao funcionário a responsabilidade de comunicar tal fato aos interessados.
                <br>
                Outrossim, de acordo com o art. 24, item II, parágrafos 1º e 2º da IN 89/03, na hipótese de produçao do funcionário, em qualquer mês, não 
                atingir o limite máximo de contribuição previsto na legislação previdenciária (5.531,31), o mesmo se compromete a recolher a diferença, 
                por vias próprias, ficando as outras fontes dispensadas de efetuar tal retenção.

            </p>
            <p class="text-right">
                <strong>VÁLIDO POR 6 MESES</strong>
            </p>
            <p class="text-left">
                Atenciosamente,
            </p>
            <p class="text-justify">
                <br>
                _______________________________________________
                <br>
                <span>
                    Instituto dos Lagos Rio
                </span>
                <br>
                <br>
                CNPJ: 07.813.739/0009-19
            </p>
            <br>
            <p class="text-justify">
                <br>
                _______________________________________________
                <br>
                <span>
                    <?= $nome ?>
                </span>
            </p>
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