
<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
include('../../conn.php');
include('../../empresa.php');
include('../../wfunction.php');
include('../../classes/regiao.php');
include('../../classes/LogClass.php');
include('../../classes/CltClassObj.php');

$log = new Log();
$img = new empresa();
$objRegiao = new regiao();
$objClt = new CltClassObj();
$usuario = carregaUsuario();

$id_clt = $_REQUEST['id_clt'];
$data_aviso = $_REQUEST['data_aviso'];
$tipo_aviso = $_REQUEST['tipo_aviso'];
$data_demissao = $_REQUEST['data_demissao'];
$tipo_rescisao = $_REQUEST['tipo_rescisao'];
$motivo_justa_causa = $_REQUEST['motivo_justa_causa'];

$data_aviso_banco = implode('-', array_reverse(explode('/', $data_aviso)));
$data_demissao_banco = implode('-', array_reverse(explode('/', $data_demissao)));

/*********************Grava na tabela de documentos Gerados*********************/
$verifica = mysql_num_rows(mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '12' AND id_clt = '$id_clt'"));
if(empty($num_row_verifica)) {
    //mysql_query("INSERT INTO rh_doc_status(tipo, id_clt, data, id_user) VALUES ('12', '$id_clt', NOW(), '{$usuario['id_funcionario']}')");
} else {
    //mysql_query("UPDATE rh_doc_status SET data = NOW(), id_user = '{$usuario['id_funcionario']}' WHERE id_clt = '$id_clt' AND tipo = '12'");
}

//Atualiza os dados na tabela rh_clt
//GRAVANDO NA RH_CLT A DATA DO PEDIDO DE EMISSÃO
$objClt->setDefault();
$objClt->setIdClt($id_clt);
$objClt->setDataAviso($data_aviso_banco);
$objClt->setDataDemi($data_demissao_banco); 
$objClt->setDataSaida($data_demissao_banco);
$objClt->setStatus(200);
//$objClt->update();
	
//$log->gravaLog("Aguardando Demissão", "Envia o clt $id_clt para aguardando Demissão!");


/*********************Montagem do Comunicado de Dispensa*********************/
$data_homologacao = new DateTime(implode('-', array_reverse(explode('/', $data_aviso))));
$data = explode('/', $data_demissao);
$dia = $data[0];
$mes = $data[1];
$ano = $data[2];
$data_homologacao = $data_homologacao->modify('+10 day')->format('d/m/Y');

$objClt->setDefault();
$objClt->setIdClt($id_clt);
if($objClt->select($id_clt)){
    $objClt->getRow();
    $objClt->selectCurso();
    $objClt->getCursoRow();
}
else{
    echo $objClt->getError();
    exit;
}

$dadosEmpresa = (object) mysql_fetch_assoc(mysql_query("SELECT * FROM rhempresa WHERE id_regiao = {$objClt->getIdRegiao()} LIMIT 1"));
//print_array($dadosEmpresa);
$objRegiao->MostraRegiao($objClt->getIdRegiao());

//---------------TITULO---------------//
$arrayImpressao[991]['titulo'] = array(
    2 => array("NOTIFICAÇÃO DE DEMISSÃO SEM JUSTA CAUSA"),
    4 => array("AVISO PRÉVIO $tipo_aviso")
);
$arrayImpressao[992]['titulo'] = array(
    2 => array("NOTIFICAÇÃO DE DEMISSÃO COM JUSTA CAUSA")
);
$arrayImpressao[993]['titulo'] = array(
    2 => array("NOTIFICAÇÃO POR TÉRMINO DE CONTRATO")
);
$arrayImpressao[994]['titulo'] = array(
    2 => array("NOTIFICAÇÃO POR TÉRMINO DE EXPERIÊNCIA")
);
$arrayImpressao[995]['titulo'] = array(
    2 => array("PEDIDO DE DISPENSA")
);

//---------------CORPO---------------//
if($tipo_aviso == 'trabalhado'){
    $arrayImpressao[991]['corpo'] = array(
        "Sr.(a) <strong>{$objClt->getNome()}</strong>",
        "Por não mais convir a esta empresa mantê-lo em nosso quadro de funcionários, vimos comunicar-lhe que seu Contrato de Trabalho será rescindido em $data_demissao. A partir de $data_aviso, haverá redução no seu horário normal de trabalho, sem prejuíjo do salário integral, sendo-lhe facultada, de acordo com as disposições vigentes, a opção por uma das seguintes alternativas:",
        "<span class='text-bold margin-l10'>1 - Trabalhar os 30 dias com redução de 2 horas por dia.</span>",
        "<span class='text-bold margin-l10'>2 - Trabalhar 23 dias e folgar os 7 últimos dias.</span>",
        "De acordo com as disposições legais vigentes, declaro, para todos os fins de direito, que nesta data opto pela alternativa de redução de horário de trabalho n.____(&nbsp;&nbsp;) acima descrita.",
        "<strong>Rio de janeiro, $dia de ".mesesArray($mes)." de $ano</strong>"
    );
} else if($tipo_aviso == 'indenizado'){
    $arrayImpressao[991]['corpo'] = array(
        "Sr.(a) <strong>{$objClt->getNome()}</strong>",
        "Por não mais convir a esta empresa mantê-lo em nosso quadro de funcionários, vimos comunicar-lhe que seu Contrato de Trabalho será rescindido em $data_demissao. Seu aviso prévio será indenizado na forma da lei, e suas verbas rescisórias serão pagas em 10 dias",
        "<strong>Rio de janeiro, $dia de ".mesesArray($mes)." de $ano</strong>"
    );
}

$arrayImpressao[992]['corpo'] = array(
    "À <strong>{$objClt->getNome()}</strong>",
    "Ref.: Dispensa por Justa Causa",
    "Comunicamos que a partir desta data declaramos rescindido seu contrato de trabalho, por justa causa, nos termos do artigo 482 da CLT, pelo motivo de $motivo_justa_causa, sendo sua conduta inapropriada com e totalmente em desacordo com as políticas da Empresa e os valores pela mesma impetrados em nosso estatuto e apresentados a todos os nossos colaboradores.",
    "Comunicamos ainda que o senhor deverá comparecer em nossa empresa na data de <strong>$data_homologacao</strong>, para homologação do termo de rescisão do contrato de trabalho, na forma da lei.",
    "Atenciosamente,",
    "<strong>Rio de janeiro, $dia de ".mesesArray($mes)." de $ano</strong>"
);
$arrayImpressao[993]['corpo'] = array(
    "Sr.(a) <strong>{$objClt->getNome()}</strong>",
    "Portador(a) da Carteira de Trabalho <strong>Número: {$objClt->getNumeroCtps()} / Série: {$objClt->getSerieCtps()} / UF: {$objClt->getUfCtps()}</strong>",
    "Comunicamos a V.S&ordf; que seu contrato de trabalho se extingue no dia <span class='text-danger'><b>$data_aviso</b></span>, solicitamos V.S&ordf; compare&ccedil;a ao Departamento de Pessoal do(a) <strong>$dadosEmpresa->razao</strong> <u>no dia <span class='text-danger'><b>$data_homologacao</b></span></u> munido de sua CTPS."
);
$arrayImpressao[994]['corpo'] = array(
    "Sr.(a) <strong>{$objClt->getNome()}</strong>",
    "Portador(a) da Carteira de Trabalho <strong>Número: {$objClt->getNumeroCtps()} / Série: {$objClt->getSerieCtps()} / UF: {$objClt->getUfCtps()}</strong>",
    "Comunicamos a V.S&ordf; que seu contrato de experi&ecirc;ncia se extingue no dia <span class='text-danger'><b>$data_aviso</b></span> e, inexistindo interesse de nossa parte na  continuidade do contrato de trabalho, solicitamos V.S&ordf; compare&ccedil;a ao Departamento  de Pessoal do(a) <strong>$dadosEmpresa->razao</strong> <u>no dia <span class='text-danger'><b>$data_homologacao</b></span></u> munido de sua CTPS."
);
if($tipo_aviso == 'trabalhado'){
    $arrayImpressao[995]['corpo'] = array(
        "Eu <strong>{$objClt->getNome()}</strong> portador(a) da Carteira de Trabalho <strong>Número: {$objClt->getNumeroCtps()} / Série: {$objClt->getSerieCtps()} / UF: {$objClt->getUfCtps()}</strong>, exercendo a função de <strong>{$objClt->getCursoNome()}</strong>, venho na forma da legislação vigente, comunicar a minha intenção de deixar o emprego, <strong>trinta</strong> dias a partir da entrega deste aviso, por minha livre e espontânea vontade.",
        "<span class='text-bold margin-l10'>1 - Trabalhar os 30 dias com redução de 2 horas por dia.</span>",
        "<span class='text-bold margin-l10'>2 - Trabalhar 23 dias e folgar os 7 últimos dias.</span>",
        "De acordo com as disposições legais vigentes, declaro, para todos os fins de direito, que nesta data opto pela alternativa de redução de horário de trabalho n.____(&nbsp;&nbsp;) acima descrita.",
        "Atenciosamente,",
        "<strong>Rio de janeiro, $dia de ".mesesArray($mes)." de $ano</strong>"
    );
} else if($tipo_aviso == 'indenizado'){
    $arrayImpressao[995]['corpo'] = array(
        "Eu <strong>{$objClt->getNome()}</strong> portador(a) da Carteira de Trabalho <strong>Número: {$objClt->getNumeroCtps()} / Série: {$objClt->getSerieCtps()} / UF: {$objClt->getUfCtps()}</strong>, exercendo a função de <strong>{$objClt->getCursoNome()}</strong>, venho na forma da legislação vigente, comunicar a minha intenção de deixar o emprego, <strong>trinta</strong> dias a partir da entrega deste aviso, por minha livre e espontânea vontade. Aproveito a oportunidade e peço a dispensa da minha obrigação em cumprir o aviso prévio.",
        "Atenciosamente,",
        "<strong>Rio de janeiro, $dia de ".mesesArray($mes)." de $ano</strong>"
    );
}
//---------------ASSINATURA---------------//
$arrayImpressao[991]['assinatura'] = $arrayImpressao[992]['assinatura'] = $arrayImpressao[993]['assinatura'] = $arrayImpressao[994]['assinatura'] = array(
    "ASSINATURA E NOME DO EMPREGADO" => array($objClt->getNome(),true),
    "ASSINATURA E NOME DO RESPONSÁVEL" =>  array($dadosEmpresa->razao,false)
);
$arrayImpressao[995]['assinatura'] = array(
    "ASSINATURA E NOME DO EMPREGADO" => array($objClt->getNome(),false),
    "ASSINATURA E NOME DO RESPONSÁVEL" =>  array($dadosEmpresa->razao,true)
);

//---------------RODAPE---------------//
$arrayImpressao[991]['rodape'] = $arrayImpressao[992]['rodape'] = $arrayImpressao[993]['rodape'] = $arrayImpressao[994]['rodape'] = $arrayImpressao[995]['rodape'] = array(
    4 => array($dadosEmpresa->razao),
    6 => array(
        "<small>$dadosEmpresa->endereco</small>",
        "<small>$dadosEmpresa->cnpj</small>",
        "<small>$dadosEmpresa->tel</small>"
    )
); 

//---------------ESPAÇAMENTO---------------//
if($tipo_aviso == 'trabalhado'){
    $arrayImpressao[991]['espacamento'] = 65;//
} else if($tipo_aviso == 'indenizado'){
    $arrayImpressao[991]['espacamento'] = 175;//
}
$arrayImpressao[992]['espacamento'] = 95;//
$arrayImpressao[993]['espacamento'] = 200;//
$arrayImpressao[994]['espacamento'] = 200;
if($tipo_aviso == 'trabalhado'){
    $arrayImpressao[995]['espacamento'] = 95;//
} else if($tipo_aviso == 'indenizado'){
    $arrayImpressao[995]['espacamento'] = 140;//
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../../favicon.png" rel="shortcut icon">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/style-print.css" rel="stylesheet" media="all">
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
                <img class="" src="../../imagens/logomaster<?=$dadosEmpresa->id_master?>.gif" alt="log" width="110" height="79">
            </div>

            <?php foreach ($arrayImpressao[$tipo_rescisao]['titulo'] as $h => $arrayTitulo) { 
                foreach ($arrayTitulo as $titulo) { ?>
                    <h<?= $h ?> class="text-center text-uppercase"><?= $titulo ?></h<?= $h ?>>
                <?php }
            } ?>
            <hr>
            <br>
            <?php foreach ($arrayImpressao[$tipo_rescisao]['corpo'] as $corpo) { ?>
                <p class="text-justify"><?= $corpo ?></p>
            <?php } ?>
            <hr>
            <?php foreach ($arrayImpressao[$tipo_rescisao]['assinatura'] as $key => $assinatura) { ?>
                <p class="text-center text-bold"><?= $key ?></p>
                <p class="text-center">________________________________________</p>
                <p class="text-center text-bold text-uppercase" style="font-family: 'Courier New', Courier, monospace;">(<?=str_replace(' ','&nbsp;',str_pad($assinatura[0], 40, ' ', STR_PAD_BOTH))?>)</p>
                <?php if($assinatura[1]){ ?><br><p class="text-left text-bold">Ciente em ____/____/____</p><?php } ?>
                <hr>
            <?php } ?>
            <br>
            <footer style="margin-top: <?=$arrayImpressao[$tipo_rescisao]['espacamento']?>px;">
            <hr>
            <?php foreach ($arrayImpressao[$tipo_rescisao]['rodape'] as $h => $arrayRodape) { 
                foreach ($arrayRodape as $rodape) { ?>
                    <h<?= $h ?> class="text-center"><?= $rodape ?></h<?= $h ?>>
                <?php }
            } ?>
            </footer>
        </div>
        <!-- javascript aqui -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
