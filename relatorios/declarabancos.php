<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
elseif (!isset($_REQUEST['clt'])) { 
    if (isset($_REQUEST['id_projeto'])) { 
        header("Location: /intranet/rh_novaintra/bolsista.php?projeto=$_REQUEST[id_projeto]");
        exit;
    }
    else { 
        header("Location: /intranet/rh/ver.php");
        exit;
    }
}
elseif (!isset($_REQUEST['banco'])) { 
    header("Location: /intranet/rh_novaintra/ver_clt.php?id_clt=$_REQUEST[clt]");
    exit;
}

include('../classes/global.php');
include('../wfunction.php');

//banco	(banco)
//tipo (clt ou autonomo)
//documento
//clt (id_clt0
if ($_REQUEST['tipo'] != "2") {
    $table = "autonomo";
    $field = "id_autonomo";
}
else {
    $table = "rh_clt";
    $field = "id_clt";
}
$arrClt = montaQuery("$table A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade LEFT JOIN projeto D ON A.id_projeto = D.id_projeto", "A.*, B.nome as curso, B.salario as curso_salario, D.nome as projeto, D.cidade as projeto_cidade, DATE_FORMAT(A.data_entrada,'%d/%m/%Y') as data_admi, DATE_FORMAT(A.data_demi,'%d/%m/%Y') data_demi, C.unidade, C.cidade as unidade_cidade", "$field = {$_REQUEST['clt']}");
$arrBanco = montaQuery('bancos', "*", "id_banco = {$_REQUEST['banco']}");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Carta de Advertência Disciplinar</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <style>
            * {
                margin: 0;
                padding: 0;
            }
            body {
                line-height: 120% !important;
                font-family: times !important;
                font-size: 12pt !important;
            }
        </style>
        <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/style-print.css" rel="stylesheet">
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
        <style>
            .text-cont {
                font-family:times !important;
            }
        </style>
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
        <div class="pagina2">
            <div class="borda2 padding2">
                <div class="text-center font22 margin"><strong>COMPETENT TERCERIZAÇÕES</strong></div>
                <br />
                <br />
                <br />
                <p class="text-justify padding2">
                    Ao<br />
                    <?= $arrBanco[1]['razao'] ?>
                </p><br />
                <p class="text-justify padding2">
                    <strong>Ref:</strong> Autorização para Abertura de Conta
                </p>
                <p class="text-justify padding2">
                    A COMPETENT TERCERIZACOES LTDA - EPP, devidamente inscrita no cadastro Nacional de Pessoa Jurídica - CNPJ/MF sob o nº 12.902.244/0001-49, com sede administrativa na RUA 5, 691, Q.C4 L.16E S.814 ED.THE PRIME TAMAND OFFIC - SETOR OESTE - GOIANIA-GO - CEP: 74115-060.
                </p>
                <p class="text-justify padding2">
                    Declara para fins de abertura de conta junto ao <?= (empty(preg_match('/^(banco)/i', $arrBanco[1]['razao']))) ? 'Banco' : '' ?>  <?= $arrBanco[1]['razao'] ?>, que <?= ($arrClt[1]['sexo'] == "M") ? 'o' : 'a' ?> <strong><?= ($arrClt[1]['sexo'] == "M") ? 'Sr' : 'Sra' ?>. <?= $arrClt[1]['nome'] ?>, residente e domiciliad<?= ($arrClt[1]['sexo'] == "M") ? 'o' : 'a' ?> na <?= ucwords(strtolower($arrClt[1]['endereco'])) ?><?= ($arrClt[1]['numero']) ? ', nº '.$arrClt[1]['numero'] : ''; ?><?= ($arrClt[1]['complemento']) ? ', '.$arrClt[1]['complemento'] : '' ?>, <?= ucwords(strtolower($arrClt[1]['bairro'])) ?>, <?= ucwords(strtolower($arrClt[1]['cidade'])) ?>/<?= $arrClt[1]['uf'] ?>, CEP: <?= $arrClt[1]['cep'] ?>,</strong> faz parte do quadro de empregados desta empresa, desde o dia <?= $arrClt[1]['data_admi'] ?>, exercendo a função de <?= $arrClt[1]['curso'] ?>, perfazendo um salário bruto mensal de R$ <?= formataMoeda($arrClt[1]['curso_salario'],1) ?> (<?= numero_extenso($arrClt[1]['curso_salario']) ?>).
                </p><br />
                <p class="text-justify padding2">
                    Assim sendo, <strong>solicitamos abertura de conta corrente individual para a mesma e com vistas ao enquadramento nas condições de abertura da conta bancária, pedimos a inclusão do incentivo de conta para crédito salário, com isenção de tarifas.</strong>
                </p><br />
                <p class="text-justify padding2">
                    E para que surta os efeitos legais assinamos o presente.
                </p><br />
                <br />
                <br />
                <p class="text-center">
                    Atenciosamente,<br />
                    <br />
                    <br />
                    <?= ucfirst(strtolower($arrClt[1]['projeto_cidade'])) ?>, <?= date('d') ?> de <?= mesesArray(date('m')) ?> de  <?= date('Y'); ?>.<br />
                    <br />
                    <br />
                    _______________________________________________<br />
                    SOLICITANTE<br />
                    <br />
                    Competent Tercerizações LTDA - EPP
                </p>
                <br />
                <br />
                <p class="text-justify padding2">
                    Obs.: apresentar este documento juntamente com cópia do RG, CPF e comprovante de endereço, na agência do <?= (empty(preg_match('/^(banco)/i', $arrBanco[1]['razao']))) ? 'Banco' : '' ?>  <?= $arrBanco[1]['razao'] ?>.
                </p>
                <br />
                <br />
                <br />
                <br />
                <br />
                <p class="text-center artigo482">
                    <small><strong>
                    COMPETENT TERCERIZACOES LTDA - EPP<br />
                    Rua 5, 691, Q.C4 L.16e S.814 Ed.The Prime Tamand Offic - Setor Oeste - Goiania/Go<br />
                    Fone: 62 3924-0973
                    </strong></small>
                </p>
                <br />
            </div>
        </div>
    </body>
</html>