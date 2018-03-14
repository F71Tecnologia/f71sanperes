<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if (!isset($_REQUEST['id_advertencia'])) { 
    header("Location: /intranet/rh/ver.php");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$clt = montaQuery('rh_suspensao', "*", "id_suspensao = {$_REQUEST['id_advertencia']}");

$id_clt = $clt[1]['id_clt'];

$arrClt = montaQuery('rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade LEFT JOIN projeto D ON A.id_projeto = D.id_projeto', "A.*, B.nome curso, D.nome projeto, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') data_entrada, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade, C.cidade as unidade_cidade, C.*", "id_clt = $id_clt");

$arrClt[1]['unidade'] = ucwords($arrClt[1]['unidade']);
$arrClt[1]['projeto'] = ucwords($arrClt[1]['projeto']);
$arrClt[1]['curso'] = ucwords($arrClt[1]['curso']);
$nome = $arrClt[1]['nome'];
$motivo = $_REQUEST['motivo'];
$alinea = str_replace(',',', ', $clt[1]['alinea']);
$data = explode('-', $clt[1]['data']);
$mes = mesesArray($data[1]);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Carta de Advert�ncia Disciplinar</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <style>
            * {
                margin: 0;
                padding: 0;
            }
            body {
                line-height: 120% !important;
            }
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
            <div class="text-center masterDiv">
                <img src="/intranet/imagens/logo.png" alt="logo" class="master_logo">
            </div><br />
            <div class="borda padding">
                <h4 class="text-center"><strong>CARTA DE ADVERT�NCIA DISCIPLINAR</strong></h4>
            </div>
            <div class="borda">
                <p class="text-justify">
                    IImo(a) Sr(a): <?= $nome ?> - <?= $arrClt[1]['unidade'] ?><br />
                    Referente a: <strong>Advert�ncia #<?= $clt[1]['id_suspensao'] ?></strong>
                </p><br />
                <p class="text-justify paragrafo">
                    Tendo em vista V. Sra. ter cometido o(s) ato(s) de indisciplina e infringido o dispositivo legal das letras "<?= $alinea ?>" do Artigo 482 da CLT - Consolida��o das Leis do Trabalho, resolvemos aplicar-lhe como medida disciplinar a presente <strong>CARTA DE ADVERT�NCIA</strong>, com o intuito de evitar a reincid�ncia ou o cometido de outra(s) falta(s) de qualquer natureza prevista em lei que nos obrigar� a tomar outras medidas cab�veis de acordo com a legisla��o em vigor.
                </p>
                <p class="text-center">
                    <strong>Descri��o da advert�ncia:</strong> O colaborador est� sendo advertido por <?= $clt[1]['motivo'] ?>.
                </p><br />
                <p class="text-center">
                    <?= ucwords(strtolower($arrClt[1]['unidade_cidade'])) . ', ' . $data[2] . ' de ' . $mes . ' de ' . $data[0]; ?>.
                </p><br />
                <p class="text-center">
                    _______________________________________________<br />
                    Assinatura do(a) Empregador(a)/Superior Imediato
                </p>
            </div>
            <div class="borda">
                <p class="text-left"><strong>Ciente do(a) Empregado(a):</strong></p>
                <p class="text-justify">Em: ____/_____/_____</p>
                <div class="text-right">
                    _______________________________________________<br />
                    Assinatura do(a) Empregado(a)
                </div><br /><br />
                <section>
                    <div class="um">
                        ___________________________________<br />
                        <span class="text-center">
                            Testemunha 1
                        </span>
                    </div>
                    <div class="dois">
                        ___________________________________<br />
                        <span class="text-center">
                            Testemunha 2
                        </span>
                    </div><br /><br /><br />
                </section>
            </div>
            <div class="borda">
                <p class="text-justify artigo482">
                    <strong>Para seu conhecimento, transcrevemos abaixo o Artigo 482 da CLT:</strong><br />
                    <small>
                        Art. 482 - Constituem justa causa para rescis�o do contrato de trabalho pelo empregador:<br />
                        a) ato de improbidade (desonestidade, fraude, mau car�ter);<br />
                        b) incontin�ncia de conduta ou mau procedimento (conduta incab�vel);<br />
                        c) negocia��o habitual por conta pr�pria ou alheia sem permiss�o do empregador, quando constituir ato de concorr�ncia � empresa para a qual trabalha o empregado, ou for prejudicial ao servi�o;<br />
                        d) condena��o criminal do empregado, passada em julgado, caso n�o tenha havido suspens�o da execu��o da pena;<br />
                        e) des�dia no desempenho das respectivas fun��es;<br />
                        f) embriaguez habitual ou em servi�o;<br />
                        g) viola��o de segredo da empresa;<br />
                        h) ato de indisciplina ou de insubordina��o;<br />
                        i) abandono de emprego;<br />
                        j) ato lesivo da honra ou da boa fama praticado no servi�o contra qualquer pessoa, ou ofensas f�sicas, nas mesmas condi��es, salvo em caso de legitima defesa, pr�pria ou de outrem;<br />
                        k) ato lesivo da honra ou da boa fama ou ofensas f�sicas praticadas contra o empregador e superiores hier�rquicos, salvo em caso de legitima defesa, pr�pria ou de outrem;<br />
                        l) pratica constante de jogos de azar.<br />
                        <strong>Par�grafo �nico</strong>- Constitui igualmente justa causa para dispensa de emprego a pratica, devidamente comprovada em inqu�rito administrativo, de atos atentat�rios contra a seguran�a nacional.
                    </small>
                </p>
            </div>
        </div>
    </body>
</html>