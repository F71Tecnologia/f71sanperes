<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if (!isset($_REQUEST['id_suspensao'])) { 
    header("Location: /intranet/rh/ver.php");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$clt = montaQuery('rh_suspensao', "*", "id_suspensao = {$_REQUEST['id_suspensao']}");
$id_clt = $clt[1]['id_clt'];

$arrClt = montaQuery('rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade LEFT JOIN projeto D ON A.id_projeto = D.id_projeto', "A.*, B.nome curso, D.nome projeto, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') data_entrada, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade, C.cidade as unidade_cidade, C.*", "id_clt = $id_clt");

$arrClt[1]['unidade'] = ucwords($arrClt[1]['unidade']);
$arrClt[1]['projeto'] = ucwords($arrClt[1]['projeto']);
$arrClt[1]['curso'] = ucwords($arrClt[1]['curso']);
$nome = $arrClt[1]['nome'];
$motivo = $_REQUEST['motivo'];
$alinea = str_replace(',',', ', $clt[1]['alinea']);
$data = explode('-', $clt[1]['data']);
$data_inicio = date('d/m/Y', strtotime(implode('-', array_reverse(explode('/', $clt[1]['data'])))));
$data_final = date('d/m/Y', strtotime(-1 .' day', strtotime(implode('-', array_reverse(explode('/', $clt[1]['data_retorno']))))));
$data_retorno = date('d/m/Y', strtotime(implode('-', array_reverse(explode('/', $clt[1]['data_retorno'])))));
$mes = mesesArray($data[1]);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Carta de Suspensão Disciplinar</title>
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
                <h4 class="text-center"><strong>SUSPENSÃO DISCIPLINAR</strong></h4>
            </div>
            <div class="borda">
                <p class="text-justify">
                    IImo(a) Sr(a): <?= $nome ?> - <?= $arrClt[1]['unidade'] ?><br />
                    Referente a: <strong>Suspensão #<?= $clt[1]['id_suspensao'] ?></strong>
                </p><br />
                <p class="text-justify paragrafo">
                    Tem a presente o fim especial de comunicar-lhe que, em atenção a falta grave, praticada por V. Sª., conforme o dispositivo legal da(s) letra(s) "<?= $alinea ?>" do Artigo 482 da CLT - Consolidação das Leis do Trabalho, como medida disciplinar deliberamos aplicar-lhe uma suspensão de <?= $clt[1]['dias'] ?> (<?= numero_extenso($clt[1]['dias'],0); ?>) dias, que tem início no dia <?= $data_inicio ?> e término no dia <?= $data_final ?>, devendo V. Sª. reiniciar suas atividades no dia <?= $data_retorno ?>.
                </p>
                <p class="text-center">
                    <strong>Descrição da suspensão:</strong> O colaborador está sendo suspenso por <?= $clt[1]['motivo'] ?>.
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
                        Art. 482 - Constituem justa causa para rescisão do contrato de trabalho pelo empregador:<br />
                        a) ato de improbidade (desonestidade, fraude, mau caráter);<br />
                        b) incontinência de conduta ou mau procedimento (conduta incabível);<br />
                        c) negociação habitual por conta própria ou alheia sem permissão do empregador, quando constituir ato de concorrência á empresa para a qual trabalha o empregado, ou for prejudicial ao serviço;<br />
                        d) condenação criminal do empregado, passada em julgado, caso não tenha havido suspensão da execução da pena;<br />
                        e) desídia no desempenho das respectivas funções;<br />
                        f) embriaguez habitual ou em serviço;<br />
                        g) violação de segredo da empresa;<br />
                        h) ato de indisciplina ou de insubordinação;<br />
                        i) abandono de emprego;<br />
                        j) ato lesivo da honra ou da boa fama praticado no serviço contra qualquer pessoa, ou ofensas físicas, nas mesmas condições, salvo em caso de legitima defesa, própria ou de outrem;<br />
                        k) ato lesivo da honra ou da boa fama ou ofensas físicas praticadas contra o empregador e superiores hierárquicos, salvo em caso de legitima defesa, própria ou de outrem;<br />
                        l) pratica constante de jogos de azar.<br />
                        <strong>Parágrafo único</strong>- Constitui igualmente justa causa para dispensa de emprego a pratica, devidamente comprovada em inquérito administrativo, de atos atentatórios contra a segurança nacional.
                    </small>
                </p>
            </div>
        </div>
    </body>
</html>