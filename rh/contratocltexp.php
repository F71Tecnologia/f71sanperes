<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include ('../conn.php');
include('../wfunction.php');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

$clt = $_REQUEST['clt'];
$id_regiao = $_REQUEST['id_reg'];

/*
master WHERE id_master = '$row_reg[id_master]' 
rhempresa where id_empresa = '$row_clt[rh_vinculo]
*/
$row_clt = montaQuery(
        "rh_clt AS CLT
        LEFT JOIN rh_transferencias AS T ON T.id_transferencia = 
            (SELECT id_transferencia 
            FROM rh_transferencias AS T2
            WHERE T2.id_clt = CLT.id_clt
            ORDER BY T2.data_proc,T2.criado_em DESC
            LIMIT 1)
        LEFT JOIN curso AS C ON C.id_curso = IF(T.id_curso_para,T.id_curso_para,CLT.id_curso)
        LEFT JOIN regioes AS R ON R.id_regiao = 
        (SELECT id_regiao
            FROM regioes AS R2
            WHERE R2.id_regiao = IF('{$id_regiao}','{$id_regiao}',CLT.id_regiao) AND status = '1'
            ORDER BY R2.id_regiao DESC
            LIMIT 1)
        LEFT JOIN master AS M ON M.id_master = R.id_master
        LEFT JOIN rhempresa AS E ON E.id_projeto = IF(T.id_projeto_para,T.id_projeto_para,CLT.id_projeto)
        LEFT JOIN rh_horarios AS H ON (CLT.rh_horario = H.id_horario)
        LEFT JOIN unidade AS U ON U.id_unidade = IF(T.id_unidade_para,T.id_unidade_para,CLT.id_unidade)
        LEFT JOIN rh_cbo AS CBO ON (C.cbo_codigo = CBO.id_cbo)
        LEFT JOIN rh_salario AS S ON S.id_salario = 
            (SELECT SAL.id_salario
            FROM rh_salario AS SAL
            WHERE SAL.id_curso = C.id_curso
            ORDER BY SAL.data, SAL.id_salario DESC
            LIMIT 1)", "CASE WHEN LENGTH(T.id_regiao_para) = 0 OR T.id_regiao_para IS NULL THEN R.id_regiao ELSE T.id_regiao_para END as id_regiao, CLT.*,M.*, CLT.nome as nnome,date_format(CLT.data_entrada, '%d/%m/%Y')as data_entrada, T.id_curso_para, C.nome AS curso_nome, E.nome as e_nome, E.razao as e_razao, E.cnpj as e_cnpj, E.endereco as e_endereco, E.numero as e_numero, E.complemento as e_complemento, E.bairro as e_bairro, E.cidade as e_cidade, E.uf as e_uf, IF(S.salario_novo IS NULL or S.salario_novo = '', C.salario, S.salario_novo) AS salario, S.salario_antigo", "CLT.id_clt = '$clt'", NULL,1);



if ($row_clt[1]['prazoexp'] == 1) {
    $prazoExp = 30;
    $prazoExp2 = 60;
} else if ($row_clt[1]['prazoexp'] == 2 OR $row_clt[1]['prazoexp'] == '') {
    $prazoExp = 45;
    $prazoExp2 = 45;
} else if ($row_clt[1]['prazoexp'] == 3) {
    $prazoExp = 60;
    $prazoExp2 = 30;
} else if ($row_clt[1]['prazoexp'] == 4) {
    $prazoExp = 30;
} else if ($row_clt[1]['prazoexp'] == 5) {
    $prazoExp = 45;
} else if ($row_clt[1]['prazoexp'] == 6) {
    $prazoExp = 60;
}

$data_entrada = strftime('%d de %B de %Y', strtotime($row_clt[1]['data_entrada']));
$data_final = strftime('%d de %B de %Y', strtotime($prazoExp-1 ." day", strtotime($row_clt[1]['data_entrada'])));
$data_final_pro = strftime("%d de %B de %Y", strtotime($prazoExp + $prazoExp2-1 ." day", strtotime($row_clt[1]['data_entrada'])));

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
if ($_COOKIE['logado'] != 87 and $row_clt[1]['status'] == 10) {
    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '65' and id_clt = '$clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);
    if (empty($num_row_verifica)) {
        mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('65','$clt','".date('Y-m-d')."', '{$_COOKIE['logado']}')");
    } else {
        mysql_query("UPDATE rh_doc_status SET data = '".date('Y-m-d')."', id_user = '{$_COOKIE['logado']}' WHERE id_clt = '$clt' and tipo = '65'");
    }
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
?>
<HTML>
    <HEAD>
        <meta charset="ISO-8859-9">
        <TITLE>:: Intranet :: CONTRATO DE EXPERIÊNCIA</TITLE>
        <link href="../favicon.png" rel="shortcut icon">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/style-print.css" rel="stylesheet" media="all">
        <style>
            body {
                font-family: times;
            }
        </style>
    </HEAD>
    <BODY class="font9">
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="pagina2">
            <p class="text-center"><strong><em><u>CONTRATO DE TRABALHO A TÍTULO DE EXPERIÊNCIA</u></em></strong></p>
            <p class="text-justify">
                Entre a empresa <strong><?= $row_clt[1]['e_razao'] ?></strong>, situada na <?= $row_clt[1]['e_endereco'] ?><?= (!empty($row_clt[1]['e_numero'])) ? ', '.$row_clt[1]['e_numero'] : ''  ?><?= (!empty($row_clt[1]['e_complemento'])) ? ', '.$row_clt[1]['e_complemento'] : ''  ?><?= (!empty($row_clt[1]['e_bairro'])) ? ', '.$row_clt[1]['e_bairro'] : ''  ?><?= (!empty($row_clt[1]['e_cidade'])) ? ', '.$row_clt[1]['e_cidade'] : ''  ?><?= (!empty($row_clt[1]['e_uf'])) ? ' - '.$row_clt[1]['e_uf'] : ''  ?>, inscrita no CNPJ de nº <?= $row_clt[1]['e_cnpj'] ?>, denominada a seguir EMPREGADORA e <strong><?= $row_clt[1]['nnome'] ?></strong>, portador (a) da CTPS de <strong>n. <?= $row_clt[1]['campo1'] ?></strong> série <strong><?= $row_clt[1]['serie_ctps'] ?> - <?= $row_clt[1]['uf_ctps'] ?></strong>, doravante designado (a) EMPREGADO, celebram o presente CONTRATO INDIVIDUAL DE TRABALHO, conforme legislação trabalhista em vigor, regido pelas cláusulas abaixo e demais disposições legais vigentes:
            </p>
            <div class="row">
                <div class="col-xs-1">
                    <p class="text-center">
                        1-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        O EMPREGADO trabalhará para o EMPREGADOR na função de <strong><?= $row_clt[1]['curso_nome'] ?></strong>, e demais funções que vierem a ser objeto de ordens verbais, cartas ou avisos, segundo as necessidades da EMPREGADORA, desde que compatíveis com suas atividades. 
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        2-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        O EMPREGADO receberá a remuneração de <strong><?= formataMoeda($row_clt[1]['salario']); ?></strong> (<strong><?= valor_extenso($row_clt[1]['salario']); ?></strong>) por <?= (!empty($row_curso['horista_plantonista'])) ? 'hora' : 'mês' ?>.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        3-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        O horário a ser obedecido será de segunda-feira a sexta-feira das 08:00 as 18:00 com intervalo de 1 hora e 12 minutos para alimentação com repouso semanal no Sábado e Domingo. Na medida da necessidade da EMPREGADORA, o EMPREGADO poderá ter o seu horário alterado permanente ou temporariamente, o que será comprovado em folha de controle de ponto. O mesmo se compromete a trabalhar em regime de compensação e prorrogação de horas, inclusive no período noturno ou em fins de semana ou feriados sempre que se fizer necessário, observando sempre as formalidades legais.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        4-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        Em caso de dano causado pelo EMPREGADO, fica a EMPREGADORA autorizada a efetivar o desconto da importância correspondente ao prejuízo, o qual fará com fundamento no § 1º do art. 462 da CLT.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        5-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        Fica ajustado nos termos do que dispõe o § 1º do artigo 469 da CLT que o EMPREGADO acatará ordem emanada da EMPREGADORA para a prestação de serviços tanto na localidade de celebração do Contrato de Trabalho, como em qualquer outra Cidade, Capital ou Vila do Território Nacional, quer essa transferência seja transitória ou definitiva.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        6-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        No ato da assinatura deste contrato, o EMPREGADO recebe o Regulamento Interno da Empresa cujas clausulas fazem parte do Contrato de Trabalho, e a violação de qualquer delas implicara em sanção, cuja graduação dependera da gravidade da mesma, culminando com a rescisão do contrato.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        7-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        O EMPREGADO fica obrigado a usar todos os equipamentos para sua segurança pessoal determinados pela EMPREGADORA.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        8-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        O EMPREGADO não poderá se recusar a submeter-se aos exames médicos exigidos pela empresa, determinados conforme o PCMSO (programa medico de saúde ocupacional) da EMPREGADORA.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        9-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        O EMPREGADO que se encontrar de atestado medico não poderá trabalhar enquanto este perdurar. O presente contrato vigorará pelo período de <?= $prazoexp ?> (<?= numero_extenso($prazoexp,0); ?>) dias, podendo ser prorrogado uma vez, desde que o total não ultrapasse a 90 (noventa) dias, sendo o mesmo celebrado para as partes verificarem reciprocamente a conveniência ou não de se vincularem em caráter definitivo a um contrato de trabalho. A empresa passando a conhecer as aptidões do EMPREGADO e suas qualidades pessoais e morais; o EMPREGADO, verificando se o ambiente e os métodos de trabalho atendem à sua conveniência.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        10-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        Na hipótese de rescisão do presente contrato, sem justa causa, no curso do prazo de experiência, observa-se quanto ao pagamento da indenização correspondente, o disposto nos artigos 479 e 480 da CLT aplicando-se no que couber.
                    </p>
                </div>
                <div class="col-xs-1">
                    <p class="text-center">
                        11-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        Findando o período de experiência, este instrumento transforma-se em contrato por prazo indeterminado e continuarão em vigor as cláusulas constantes deste contrato.
                    </p>
                </div>
                <p class="text-justify">
                    E estando de pleno acordo, as partes contratantes assinam o presente, em duas vias, ficando uma em poder do empregado e outra do empregador.
                </p>
                <p class="text-center">
                    <?= ucfirst(strtolower($row_clt[1]['e_cidade']))." - ".$row_clt[1]['e_uf'] ?>, <?= $data_entrada; ?>.
                </p>
                <div class="row">
                    <div class="col-xs-6">
                        <p class="text-center">
                            <strong>________________________________________________<br />
                            TESTEMUNHA</strong>
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="text-center">
                            <strong>________________________________________________<br />
                            <?= $row_clt[1]['e_razao']."<br />CNPJ: ".$row_clt[1]['e_cnpj']; ?></strong>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <p class="text-center">
                            <strong>________________________________________________<br />
                            TESTEMUNHA</strong>
                        </p>
                    </div>
                    <div class="col-xs-6">
                        <p class="text-center">
                            <strong>________________________________________________<br />
                            <?= $row_clt[1]['nnome']."<br />CTPS: ".$row_clt[1]['campo1']." / ".$row_clt[1]['serie_ctps']." - ".$row_clt[1]['uf_ctps']; ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="pagina2">
            <br />
            <br />
            <p class="text-center"><strong><em><u>TERMO DE PRORROGAÇÃO</u></em></strong></p>
            <div class="row">
                <div class="col-xs-1">
                    <p class="text-center">
                        1-
                    </p>
                </div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        Como este contrato não foi rescindido por qualquer das partes no vencimento do dia <?= $data_final ?>, fica automaticamente prorrogado por mais <?= $prazoExp2." (".numero_extenso($prazoExp2,0).")" ?> dias, com vencimento para <?= $data_final_pro ?>.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-left">
                        <?= ucfirst(strtolower($row_clt[1]['e_cidade']))." - ".$row_clt[1]['e_uf'] ?>, <?= $data_final ?>.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <p class="text-center">
                        <strong>________________________________________________<br />
                        TESTEMUNHA</strong>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="text-center">
                        <strong>________________________________________________<br />
                        <?= $row_clt[1]['e_razao']."<br />CNPJ: ".$row_clt[1]['e_cnpj']; ?></strong>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <p class="text-center">
                        <strong>________________________________________________<br />
                        TESTEMUNHA</strong>
                    </p>
                </div>
                <div class="col-xs-6">
                    <p class="text-center">
                        <strong>________________________________________________<br />
                        <?= $row_clt[1]['nnome']."<br />CTPS: ".$row_clt[1]['campo1']." / ".$row_clt[1]['serie_ctps']." - ".$row_clt[1]['uf_ctps']; ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </BODY>
</HTML>

