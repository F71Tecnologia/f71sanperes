<?php
include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$clt = montaQuery('rh_suspensao', "*", "id_suspensao = {$_REQUEST['id_advertencia']}");

$id_clt = $clt[1]['id_clt'];

$arrClt = montaQuery('rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade LEFT JOIN projeto D ON A.id_projeto = D.id_projeto', "A.*, B.nome curso, D.nome projeto, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') data_entrada, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade", "id_clt = $id_clt");

$arrClt[1]['unidade'] = ucwords($arrClt[1]['unidade']);
$arrClt[1]['projeto'] = ucwords($arrClt[1]['projeto']);
$arrClt[1]['curso'] = ucwords($arrClt[1]['curso']);

$nome = $arrClt[1]['nome'];
$ctps = "{$arrClt[1]['campo1']}/{$arrClt[1]['serie_ctps']}-{$arrClt[1]['uf_ctps']}";

if (!empty($arrClt[1]['data_demi'])) {
    $txt = "foi nosso funcionário na {$arrClt[1]['unidade']}, de {$arrClt[1]['data_entrada']} até {$arrClt[1]['data_demi']}, exercendo como cargo a função de {$arrClt[1]['curso']}.";
} else {
    $txt = "é nosso funcionário na {$arrClt[1]['unidade']}, de {$arrClt[1]['data_entrada']} até o presente momento, exercendo como cargo a função de {$arrClt[1]['curso']}.";
}

$motivo = $_REQUEST['motivo'];

$mes = mesesArray(date('m'));
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Carta de Advertência Disciplinar</title>
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
            <h4 class="text-left">CARTA DE ADVERTÊNCIA DISCIPLINAR</h4>
            <br>
            <p class="text-justify">
                <strong>Ao(À)</strong>
            </p>
            <p class="text-justify">
                <strong>Sr.(a) <?= $nome ?></strong>
            </p>
            <p class="text-justify">
                <strong><?= $arrClt[1]['curso'] ?></strong>
            </p>
            <p class="text-justify">
                <strong><?= $arrClt[1]['projeto'] ?></strong>
            </p>
            <p class="text-justify">
                <strong>CTPS: <?= $ctps ?></strong>
            </p>
            <p class="text-justify">
                <strong>MATRÍCULA: <?= $arrClt[1]['matricula'] ?></strong>
            </p>
            <p class="text-justify">
                <strong>Ocorrência: Advertência</strong>
            </p>
            <p class="text-justify">
                Prezado(a) Senhor(a),<br>
                Venho através da presente, informar que o Senhor(a) apresentou comportamento não condizente com as normas internas da empresa, sobretudo <?=$clt[1]['motivo']?>. Sendo assim,
                resolvemos aplicar-lhe como medida disciplinar a presente <strong>CARTA</strong>, com o intuito de evitar a reincidência ou o cometido de outra(s) falta(s) de
                qualquer natureza prevista em lei que nos obrigará a tomar outras medidas cabíveis de acordo com a legislação em vigor.
            </p>
            <p class="text-justify">
                Sem mais,
            </p>
            <br>
            <p class="text-justify">
                Rio de Janeiro, ________/________/_____________
            </p>
            <p class="text-justify">
                <br>
                _______________________________________________
                <br>
                <span>
                    Departamento de Recursos Humanos
                </span>
            </p>
            <p class="text-justify">
                <br>
                _______________________________________________
                <br>
                <span>
                    (Assinatura do Superior Imediato)
                </span>
            </p>
            <br>
            <p class="text-right">Ciente em, ____/____/________. </p>
            <br>
            <div class="row">
                <div class="text-center">
                    <br>
                    _______________________________________________
                    <br>
                    <span>
                        <strong><?= $nome ?></strong>
                    </span>
                </div>
            </div>
            <br>
            <p>
            <div style="float:left" class="text-left">
                <span class="text-sm">
                    Testemunha:
                </span>
                <br>
                <br>
                <br>
                ___________________________________
                <br>
            </div>
            <div style="float:right" class="text-left">
                <span class="text-sm">
                    Testemunha:
                </span>
                <br>
                <br>
                <br>
                ___________________________________
                <br>
            </div>
        </p>
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