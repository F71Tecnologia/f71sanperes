<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
//error_reporting(E_ALL);

include('../../conn.php');
include("../../funcoes.php");
include("../../wfunction.php");
$lista = false;
$usuario = carregaUsuario();

//VERIFICA INFORMAÇÃO DE POST
if (validate($_REQUEST['filtrar'])) {
    $lista = true;
    $mes = str_pad($_REQUEST['mes'], 2, "0", STR_PAD_LEFT);
    $ano = $_REQUEST['ano'];

    $regiao = $usuario['id_regiao'];

    $tipoPagamento = $_REQUEST['tipo_pagamento'];
    $tipoContratacao = $_REQUEST['tipo_contrato'];

    switch ($tipoPagamento) {

        case 1: //"1"=>"Pagamentos Folha"

            switch ($tipoContratacao) {

                case 1: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '1' ";
                    break;

                case 2: $tabela_folha = 'rh_folha';
                    $contratacao = " ";
                    $tipo_contrato_pg = 1;
                    break;

                case 3: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '3' ";
                    $tipo_contrato_pg = 2;
                    break;

                case 4: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '4' ";
                    break;
            }

            if ($_COOKIE['logado'] != 257) {

                $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                            FROM $tabela_folha f
                            INNER JOIN projeto p ON f.projeto = p.id_projeto
                            WHERE (f.status = '3' OR f.status = '2') AND 
                                 p.id_master = {$usuario['id_master']} AND 
                                 f.mes = '{$mes}' AND 
                                 f.ano = '{$ano}' AND 
                                 p.id_regiao != 36 AND
                                 p.id_regiao  = '{$regiao}'
                                 $contratacao
                                    ORDER BY f.projeto";
            } else {
                $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                            FROM $tabela_folha f
                            INNER JOIN projeto p ON f.projeto = p.id_projeto
                            WHERE (f.status = '3' OR f.status = '2') AND 
                                 p.id_master = {$usuario['id_master']} AND 
                                 f.mes = '{$mes}' AND 
                                 f.ano = '{$ano}' AND 
                                 p.id_regiao  = '{$regiao}'
                                 $contratacao
                                    ORDER BY f.projeto";
            }

            break;

        case 2: //"2" => "Pagamentos Rescisão"
            $query = "SELECT A.id_recisao, A.id_clt as clt, B.nome AS nome_clt,D.nome AS nprojeto, A.total_liquido, C.id_regiao, C.regiao, D.id_projeto, D.nome, E.*, F.*,F.status as status_saida, lek.*
                        FROM rh_recisao AS A
                        LEFT JOIN rh_clt AS B ON B.id_clt = A.id_clt
                        LEFT JOIN regioes AS C ON A.id_regiao = C.id_regiao
                        LEFT JOIN projeto AS D ON A.id_projeto = D.id_projeto
                        LEFT JOIN pagamentos_especifico AS E ON (E.id_clt = A.id_clt)
                        LEFT JOIN saida AS F ON (E.id_saida = F.id_saida)
                        LEFT JOIN (
                        SELECT BB.`status` as status_multa,BB.id_clt FROM saida_files as AA 
                        INNER JOIN saida as BB
                        ON AA.id_saida = BB.id_saida
                        WHERE BB.tipo = '167' AND AA.multa_rescisao = 1) as lek ON (lek.id_clt = A.id_clt)
                        WHERE 
                            MONTH(A.data_demi) = '$mes'
                            AND YEAR(A.data_demi) = '$ano'
                            AND A.status = '1' 
                            AND A.id_regiao = '{$regiao}'
                            AND D.id_master = {$usuario['id_master']}
                                GROUP BY B.id_clt
                                ORDER BY C.id_regiao,D.nome,B.nome";

            break;

        case 3: //"3" => "Pagamentos Férias"
            $query = "SELECT A.*,B.regiao as nome_regiao, C.nome as nome_projeto,  D.nome as nome_clt
                    FROM rh_ferias as A
                    INNER JOIN regioes as B
                    ON B.id_regiao = A.regiao
                    INNER JOIN projeto as C
                    ON C.id_projeto = A.projeto
                    INNER JOIN rh_clt as D
                    ON A.id_clt = D.id_clt
                    WHERE A.status = 1 AND B.id_master = $usuario[id_master] AND A.mes = $mes AND A.ano = $ano AND B.id_regiao = '{$regiao}' ORDER BY projeto, D.nome;";

            break;

        case 4: // "4" => pagamento RPA
            //A PEDIDO DA DILEANE, HOJE DIA 12/09/2014 ALTEREI A CONDIÇÃO PARA LISTAR O PAGAMENTO DE RPA.
            //APARTIR DE HOJE INVES DE LISTAR DE ACORDO COM O MES E O ANO DA DATA DE GERAÇAO (MONTH(data_geracao) = '$mes' AND YEAR(data_geracao) = '$ano')
            //SERÁ LISTADO DE ACORDO COM O MES E O ANO DA COMPETENCIA ---- AMANDA
            $query = "SELECT A.*, B.nome,  C.nome as nome_projeto,C.id_projeto, B.banco, B.conta, B.agencia, B.tipo_pagamento,B.nome_banco, B.cpf,B.pis,E.nome as funcao
                        FROM  rpa_autonomo as A
                        LEFT JOIN autonomo as B ON (A.id_autonomo = B.id_autonomo)
                        LEFT JOIN projeto as C ON (C.id_projeto = B.id_projeto)
                        LEFT JOIN regioes as D ON (D.id_regiao = B.id_regiao)
                        LEFT JOIN curso AS E ON (B.id_curso = E.id_curso)
                        LEFT JOIN rpa_saida_assoc AS F ON (F.id_rpa = A.id_rpa)
                        LEFT JOIN saida AS G ON (G.id_saida = F.id_saida)
                        LEFT JOIN saida_files AS H ON (G.id_saida = H.id_saida)
                        WHERE A.mes_competencia = '$mes' AND A.ano_competencia = '$ano' AND D.id_regiao = '{$regiao}'  AND F.tipo_vinculo = '1'
                        GROUP BY B.id_autonomo
                        ORDER BY B.id_projeto,B.nome";
            break;

        case 5:
            // SQL VT
            $query = "SELECT A.id_projeto, A.nome AS nome_projeto, B.id_regiao, B.regiao AS nome_regiao FROM projeto AS A
                        LEFT JOIN regioes AS B ON(A.id_regiao=B.id_regiao)
                        WHERE A.id_regiao='$usuario[id_regiao]' AND A.status_reg=1 AND B.`status`=1 AND A.status_reg=1;";
//            echo '<br>'.$query.'<br>';
            //botar pra pegar o vr também.
            break;
        case 6:
            switch ($tipoContratacao) {

                case 1: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '1' ";
                    break;

                case 2: $tabela_folha = 'rh_folha';
                    $contratacao = " ";
                    $tipo_contrato_pg = 1;
                    break;

                case 3: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '3' ";
                    $tipo_contrato_pg = 2;
                    break;

                case 4: $tabela_folha = 'folhas';
                    $contratacao = " AND f.contratacao = '4' ";
                    break;
            }
            $query = "SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                            FROM $tabela_folha f
                            INNER JOIN projeto p ON f.projeto = p.id_projeto
                            WHERE (f.status = '3' OR f.status = '2') AND 
                                 p.id_master = {$usuario['id_master']} AND 
                                 f.mes = '{$mes}' AND 
                                 f.ano = '{$ano}' AND 
                                 p.id_regiao  = '{$regiao}'
                                 $contratacao
                                    ORDER BY f.projeto";
            break;
    }

    echo '<!-- ** ';
    print_r($query);
    $result = mysql_query($query);
    echo ' -->';
}

//CARREGA TIPOS DE PAGAMENTOS PARA SELECT
$tiposPg = array("1" => "Pagamentos Folha", "2" => "Pagamentos Rescisão", "3" => "Pagamentos Férias", "4" => "Pagamentos RPA", "5" => "VALES ( VT / VR )", "6" => "Pagamento de Sindicatos");

//CARREGA TIPOS DE CONTRATAÇÃO PARA SELECT
$rsTiposCont = montaQuery('tipo_contratacao', "tipo_contratacao_id,tipo_contratacao_nome");
$tiposCont = array();
foreach ($rsTiposCont as $valor) {
    $tiposCont[$valor['tipo_contratacao_id']] = $valor['tipo_contratacao_nome'];
}

//MONTA SELECT PARA MES
$optMes = array();
for ($i = 1; $i <= 12; $i++) {
    $optMes[$i] = mesesArray($i);
}

//MONTA SELECT PARA ANOS
$optAnos = array();
for ($i = 2009; $i <= date('Y') + 1; $i++) {
    $optAnos[$i] = $i;
}

//SETANDO VARIAVIES DE RETORNO DOS SELECTS
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$mesSel = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$tipoContSel = (isset($_REQUEST['tipo_contrato'])) ? $_REQUEST['tipo_contrato'] : "2";
$ttiposPgSel = (isset($_REQUEST['tipo_pagamento'])) ? $_REQUEST['tipo_pagamento'] : "";


$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Pagamentos");
$breadcrumb_pages = array("Gestão de RH" => "../../principalrh.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Pagamentos</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form name="form" action="" method="post" id="form1" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Pagamentos</small></h2></div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Tipo de Pagamento:</label>
                        <div class="col-md-10">
                            <?php echo montaSelect($tiposPg, $ttiposPgSel, array('name' => 'tipo_pagamento', 'id' => 'tipo_pagamento', 'class' => 'form-control')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Tipo de Contratação:</label>
                        <div class="col-md-4">
                            <?php echo montaSelect($tiposCont, $tipoContSel, array('name' => 'tipo_contrato', 'id' => 'tipo_contrato', 'class' => 'form-control')); ?>
                        </div>
                        <label class="col-md-2 control-label">Data de Referencia:</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <?php echo montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'form-control')); ?>
                                <div class="input-group-addon"></div>
                                <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-md-3">
                       <div class="tr-bg-active"><span class="btn-label bg-danger fa fa-file bordered"></span>Aguardando Pagamento</div>
                    </div>
                    <div class="col-md-2">
                       <div class="tr-bg-active"><span class="btn-label bg-success fa fa-file bordered"></span>Pago</div>
                    </div>
                    <div class="col-md-3">
                       <div class="tr-bg-active"><span class="btn-label bg-panel fa fa-file bordered"></span>Pagamento Não Gerado</div>
                    </div>
                    <div class="col-md-2">
                       <div class="tr-bg-active"><span class="btn-label bg-info fa fa-file bordered"></span>Saída Apagada</div>
                    </div>
                    <div class="col-md-2 no-padding-r">
                       <div class="tr-bg-active"><span class="btn-label bg-warning fa fa-file bordered"></span>Saída Estornada</div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <!--img src="imagens/status2.gif" /-->
                    <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                    <input type="submit" class="btn btn-primary" name="filtrar" value="Filtrar"/>
                </div>
            </div>
            <?php if ($lista) {
                if (mysql_num_rows($result) == 0) { ?>
                    <div id='message-box' class='alert alert-danger'>Nenhum registro encontrado para o filtro selecionado.</div>
                <?php } else {
                    //RESULTADO PRARA FOLHA DE PAGAMENTO CLT OU COOPERADO
                    if ($tipoPagamento == 1) { ?>
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr class="bg-primary">
                                    <th>ID folha</th>
                                    <th>Região</th>
                                    <th>Projeto</th>
                                    <?php if ($_COOKIE['logado'] == 204) { ?>
                                        <th>VT</th>
                                    <?php } ?>
                                    <th>GPS</th>
                                    <th>FGTS</th>
                                    <th>PIS</th>
                                    <th>IR</th>
                                    <th>IR de Férias</th>
                                    <!--<th class="separa">&nbsp</th>
                                    <th>TRANSPORTE</th>
                                    <th>ALIMENTAÇÃO</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cor = 0;
                                while ($row_folha = mysql_fetch_assoc($result)) {
                                    // verificação de 13ª salario.
                                    if ($row_folha['terceiro'] == '1') {
                                        if ($row_folha['tipo_terceiro'] == 3) {
                                            $decimo3 = " - 13ª integral";
                                        } else {
                                            $decimo3 = " - 13ª ($row_folha[tipo_terceiro]ª) Parcela";
                                        }
                                    }

                                    $sql = "
                                        SELECT (
                                        SELECT IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                        LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                        WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 1 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as gps,

                                        (SELECT IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                        LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                        WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 2 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as fgts,

                                        (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                        LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                        WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 3 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as pis,

                                        (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                        LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                        WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 4 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as ir,

                                        (SELECT IF(B.estorno != 0,'estorno', B.status) FROM pagamentos AS A
                                        LEFT JOIN saida AS B ON(A.id_saida=B.id_saida)
                                        WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 7 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) AS irDeFerias
                                ";

//                                            (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
//                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
//                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 5 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as transporte,
//                                            
//                                            (SELECT  IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
//                                            LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
//                                            WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.id_folha = {$row_folha['id_folha']} AND A.tipo_pg = 6 AND A.tipo_contrato_pg = $tipo_contrato_pg ORDER BY data_proc DESC LIMIT 1) as sodexo,


                                    $query_controle = mysql_query($sql);
                                    //echo '<!-- ' . $sql . ' -->';
                                    $row_controle = mysql_fetch_assoc($query_controle);
                                    $tipos = array("1" => "gps", "2" => "fgts", "3" => "pis", "4" => "ir", "5" => "irDeFerias");
                                    ?>

                                    <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                        <td><span class="dados"><?= $row_folha['id_folha'] ?></span></td>
                                        <td><span class="dados"><?= $row_folha['id_regiao'] . " - " . $row_folha['regiao']; ?></span></td>
                                        <td><span class="dados"><?= $row_folha['nome'] . $decimo3 ?></span></td>
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            switch ($row_controle[$tipos[$i]]) {

                                                case '0':
                                                    $color[$i] = "bg-info";
                                                    $link_guias[$i] = 'cadastro_1_novaintra.php';
                                                    break;
                                                case 1:
                                                    $color[$i] = "bg-danger";
                                                    $link_guias[$i] = 'visualizar_guias_saidas_novaintra.php';

                                                    break;
                                                case 2:
                                                    $color[$i] = "bg-success";
                                                    $link_guias[$i] = 'visualizar_guias_saidas_novaintra.php';
                                                    break;

                                                case 'estorno': $color[$i] = 'bg-warning';
                                                    $link_guias[$i] = 'visualizar_guias_saidas_novaintra.php';
                                                    break;

                                                default: $color[$i] = '';
                                                    $link_guias[$i] = 'cadastro_1_novaintra.php';
                                            }
                                        }
                                        ?> 
                                        <?php if ($_COOKIE['logado'] == 204) { ?>
                                            <td align="center" class="<?= $color[1] ?>">
                                                <a href="form_guia.php?id_folha=<?= $row_folha['id_folha'] ?>&tipo_contrato=<?= $tipoContratacao; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                    <img src="../imagensrh/gps.jpg" />
                                                </a>                                      
                                            </td>
                                        <?php } ?>
                                        <!-----    GPS    ----------------------->
                                        <td align="center" class="<?= $color[1] ?>">
                                            <a href="<?php echo $link_guias[1]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=1&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&regiao=<?php echo $regiao; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                <img src="../imagensrh/gps.jpg" />
                                            </a>                                      
                                        </td>
                                        <td align="center" class="<?= $color[2] ?>">
                                            <a href="<?php echo $link_guias[2]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=2&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&regiao=<?php echo $regiao; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                <img src="../imagensrh/log_fgts.jpg" />
                                            </a>  
                                        </td>
                                        <td align="center" class="<?= $color[3] ?>">
                                            <a href="<?php echo $link_guias[3]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=3&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                <img src="../imagensrh/pis.jpg" />
                                            </a>  
                                        </td>                                           
                                        <td align="center" class="<?= $color[4] ?>">
                                            <a href="<?php echo $link_guias[4]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=4&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                <img src="../imagensrh/ir.jpg" />
                                            </a>  
                                        </td>
                                        <td align="center" class="<?= $color[5] ?>">
                                            <a href="<?php echo $link_guias[5]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=7&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                <img src="../imagensrh/ir.jpg" />
                                            </a>  
                                        </td>
                                        <!--<td class="separa">&nbsp</td>-->
                                        <!--
                                                                                    <td align="center" class="<?= $color[5] ?>">
                                                                                        <a href="<?php echo $link_guias[5]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=5&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                                                            <img src="imagens/transporte.png" width="50px" style="cursor: pointer" />
                                                                                        </a>  
                                                                                    </td>

                                                                                    <td align="center" class="<?= $color[6] ?>">
                                                                                        <a href="<?php echo $link_guias[6]; ?>?id_folha=<?= $row_folha['id_folha'] ?>&tipo_guia=6&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                                                            <img src="imagens/sodexo.jpg" width="60px" style="cursor: pointer" />
                                                                                        </a>  
                                                                                    </td>-->
                                    </tr>
                                    <?php
                                    unset($color, $decimo3);
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php } elseif ($tipoPagamento == 2) { ?>
                        <table class="table table-condensed table-hover table-bordered">
                            <thead>
                                <tr class="bg-primary">
                                        <th>Projeto</th>
                                        <th>ID CLT</th>
                                        <th>Nome</th>
                                        <th>Valor</th>
                                        <th>Rescisão</th>
                                        <th>Multa</th>
                                        <th style=" width: 10%; ">Rescisão Complementar</th>
                                        <th style=" width: 10%; ">Multa Complementar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cor = 0;
                                    while ($row_resci = mysql_fetch_assoc($result)) {

                                        $qr_verifica_recisao = mysql_query("SELECT MAX(B.status) as status 
                                                                            FROM pagamentos_especifico as A
                                                                            INNER JOIN saida as B ON (B.id_saida = A.id_saida)
                                                                            WHERE A.id_clt = '{$row_resci['clt']}' AND B.tipo = 170;");
                                        $row_verifica = mysql_fetch_assoc($qr_verifica_recisao);
                                        
                                        $query_saida_multa = mysql_query("SELECT b.status FROM saida_files as a 
                                                                                INNER JOIN saida as b
                                                                                ON a.id_saida = b.id_saida
                                                                                WHERE b.id_clt = '{$row_resci['clt']}' AND b.tipo IN(167,170) AND a.multa_rescisao = 1 ORDER BY b.status DESC;");
                                        $row_saida_multa = mysql_fetch_assoc($query_saida_multa);
                                        
                                        $resc = mysql_query("SELECT id_recisao FROM rh_recisao WHERE id_clt = {$row_resci['clt']} AND status = 1 AND vinculo_id_rescisao IS NOT NULL;");

                                        while ($row1 = mysql_fetch_array($resc)) {
                                            $sql_resci = "SELECT B.id_rescisao, C.vinculo_id_rescisao, A.`status`
                                                        FROM saida AS A
                                                        LEFT JOIN pagamentos_especifico AS B ON (A.id_saida = B.id_saida)
                                                        LEFT JOIN rh_recisao AS C ON (B.id_rescisao = C.id_recisao)
                                                        LEFT JOIN saida_files AS D ON (D.id_saida = A.id_saida)
                                                        WHERE A.id_clt = {$row_resci['clt']} AND C.`status` = 1 AND C.vinculo_id_rescisao IS NOT NULL AND B.id_rescisao = {$row1['id_recisao']} AND D.multa_rescisao <> 2;";
                                                        

                                           $sql_mul_compl = "SELECT B.id_rescisao, C.vinculo_id_rescisao, A.`status`
                                                        FROM saida AS A
                                                        LEFT JOIN pagamentos_especifico AS B ON (A.id_saida = B.id_saida)
                                                        LEFT JOIN rh_recisao AS C ON (B.id_rescisao = C.id_recisao)
                                                        LEFT JOIN saida_files AS D ON (D.id_saida = A.id_saida)
                                                        WHERE A.id_clt = {$row_resci['clt']} AND C.`status` = 1 AND C.vinculo_id_rescisao IS NOT NULL AND B.id_rescisao = {$row1['id_recisao']} AND D.multa_rescisao = 2;";                                                               
                                           
                                            $query_resci_complementar = mysql_query($sql_resci);
                                            $rescisao[$row1['id_recisao']] = mysql_fetch_assoc($query_resci_complementar);
                                            
                                            $query_mul_compl = mysql_query($sql_mul_compl);
                                            $multaCompl[$row1['id_recisao']] = mysql_fetch_assoc($query_mul_compl);
                                            
                                        }
                                      
                                        
                                        switch ($row_verifica['status']) {
                                            case 1:
                                                $color['re'] = "cor-1";
                                                break;
                                            case 2:
                                                $color['re'] = "cor-3";
                                                break;
                                            default: $color['re'] = '';
                                        }

                                        switch ($row_saida_multa['status']) {
                                            case 1:
                                                $color['mu'] = "cor-1";
                                                break;
                                            case 2:
                                                $color['mu'] = "cor-3";
                                                break;
                                            default: $color['mu'] = '';
                                        }

                                        foreach ($rescisao as $idResciao => $dados) {
                                            switch ($dados['status']) {
                                                case 1:
                                                    $color['resci_complementar'] = "cor-1";
                                                    break;
                                                case 2:
                                                    $color['resci_complementar'] = "cor-3";
                                                    break;
                                                default: $color['resci_complementar'] = '';
                                            }
                                            $arrayR[$row_resci['clt']][$idResciao] = $color['resci_complementar'];
                                        }
                                        foreach ($multaCompl as $idRescisao2 => $dadosMC) {
                                            switch ($dadosMC['status']) {
                                                case 1:
                                                    $color['mulC'] = "cor-1";
                                                    break;
                                                case 2:
                                                    $color['mulC'] = "cor-3";
                                                    break;
                                                default: $color['mulC'] = '';
                                            }
                                            $arrayMC[$row_resci['clt']][$idRescisao2] = $color['mulC'];
                                            unset($multaCompl);
                                        }

                                        ?>
                            <!--         e                           <tr>
                                                        <td colspan="7"><? //= $sql_resci;   ?></td>
                                                    </tr>-->
                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>"> 
                                            <td><?php echo $row_resci['nprojeto'] ?></td>
                                            <td class="center"><?php echo $row_resci['clt'] ?></td>
                                            <td><?php echo $row_resci['nome_clt'] ?></td>
                                            <td>R$ <?php echo number_format($row_resci['total_liquido'], 2, ",", ".") ?></td>
                                            <td align="center" class="<?= $color['re'] ?>">
                                                <?php
                                                if ($color['re'] == "") {
                                                    ?>

                                                    <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&tipo=2&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&regiao=<?php echo $regiao; ?>&tipo=2&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                        <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                    </a>
                                                <?php } ?>
                                            </td>
                                            <td align="center" class="<?= $color['mu'] ?>">
                                                <?php if ($color['mu'] == "") { ?>

                                                    <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&tipo=3&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                    </a>

                                                <?php } else { ?>

                                                    <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $row_resci['id_recisao'] ?>&regiao=<?php echo $regiao; ?>&tipo=3&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                        <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                    </a>
                                                <?php } ?>

                                            </td>
                                            <!--Parte de Vinculo de rescisão-->    
                                            <td>
                                                <div>
                                                    <?php
                                                    foreach ($arrayR as $idClt => $rescisao) {
                                                        foreach ($rescisao as $idRescisao => $cores) {
                                                            ?>
                                                            <div class="<?= $cores; ?>" style="float:left; width:100%" align="center">
                                                                <?php if ($cores == '') {
                                                                    ?>
                                                                    <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&tipo=4&TB_iframe=true&width=1100" class="thickbox" > 
                                                                        <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                                    </a>

                                                                <?php } else { ?>

                                                                    <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&regiao=<?php echo $regiao; ?>&tipo=4&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                                        <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                                    </a>

                                                                <?php }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    unset($arrayR);
                                                    unset($rescisao);
                                                    ?> 
                                                </div>          
                                            </td>
                                            <!--Parte de Vinculo de rescisão-->    
                                            <td>
                                                <div>
                                                    <?php
                                                                                                        
                                                    foreach ($arrayMC as $idClt => $multaComplementar) {
                                                        foreach ($multaComplementar as $idRescisao => $cores) {
                                                            ?>
                                                            <div class="<?= $cores; ?>" style="float:left; width:100%" align="center">
                                                                <?php if ($cores == '') {
                                                                    ?>
                                                                    <a href="detalhes_novo.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&tipo=5&TB_iframe=true&width=1100" class="thickbox" > 
                                                                        <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                                    </a>

                                                                <?php } else { ?>

                                                                    <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row_resci['clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_rescisao=<?php echo $idRescisao; ?>&regiao=<?php echo $regiao; ?>&tipo=5&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                                        <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                                    </a>

                                                                <?php }
                                                                ?>
                                                            </div>
                                                            <?php
                                                            
                                                        }
                                                    }
                                                    unset($arrayMC);
                                                    unset($multaComplementar);
                                                    
                                                    ?> 
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    unset($cor);
                                    unset($color);
                                    ?>
                                </tbody>   
                            </table>
                            <?php
                        } elseif ($tipoPagamento == 3) {
                            $cor = 0;
                            ?>        
                            <table class="table table-condensed table-hover table-bordered">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>Projeto</th>
                                        <th>ID CLT</th>
                                        <th>Nome</th>
                                        <th>Valor</th>
                                        <th></th>
                                    </tr>
                                </thead>      
                                <tbody>
                                    <?php
                                    while ($row = mysql_fetch_assoc($result)) {


                                        $qr_verifica_saida = mysql_query("SELECT MAX(B.status) as status FROM pagamentos_especifico as A 
                                                                                            INNER JOIN saida as B
                                                                                            ON B.id_saida = A.id_saida
                                                                                            WHERE A.id_clt = '{$row['id_clt']}' AND (B.tipo = 76 OR B.tipo = 156) AND A.ano = $ano AND A.mes = $mes;");
                                        $row_verifica = mysql_fetch_assoc($qr_verifica_saida);

                                        switch ($row_verifica['status']) {
                                            case 1:
                                                $color['ferias'] = "cor-1";
                                                break;
                                            case 2:
                                                $color['ferias'] = "cor-3";
                                                break;
                                            default: $color['ferias'] = '';
                                        }
                                        ?>
                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                            <td><?php echo $row['nome_projeto'] ?></td>
                                            <td><?php echo $row['id_clt'] ?></td>
                                            <td><?php echo $row['nome_clt'] ?></td>
                                            <td><?php echo number_format($row['total_liquido'], 2, ',', '.') ?></td>
                                            <td align="center" class="<?= $color['ferias'] ?>">
                                                <?php if ($color['ferias'] == "") { ?>

                                                    <a href="detalhes_novo.php?id_clt=<?php echo $row['id_clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_ferias=<?php echo $row['id_ferias']; ?>&tipo=1&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img border="0px" src="imagens/saida-32.png" width="18" height="18">
                                                    </a>  

                                                <?php } else { ?>                                            
                                                    <a href="visualizar_resc_ferias.php?id_clt=<?php echo $row['id_clt'] ?>&mes=<?php echo $mes; ?>&ano=<?php echo $ano; ?>&id_ferias=<?php echo $row['id_ferias']; ?>&regiao=<?php echo $regiao; ?>&tipo=1&keepThis=true&TB_iframe=true&width=930" class="thickbox" > 
                                                        <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" />
                                                    </a>

                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } unset($cor);
                                    unset($color);
                                    ?>    
                                </tbody>  
                            </table>

                        <?php
                        } elseif ($tipoPagamento == 4) {
                            $cor = 0;
                            ?>
                            <div style="margin-bottom: 20px;"><a href="<?php printf("rel_rpa_analitico_2.php?mes=%d&ano=%d", $mes, $ano) ?>" class="bt-rel_analitico" target="_blank">Ver RPA Analítico</a></div> 
                            <table class="table table-condensed table-hover table-bordered">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>Projeto</th>
                                        <th>ID AUTONOMO</th>
                                        <th>Nome</th>
                                        <th>Função</th>
                                        <th>CPF</th>
                                        <th>PIS</th>
                                        <th>Banco</th>
                                        <th>Agência</th>
                                        <th>Conta</th>
                                        <th>Valor</th>
                                        <th>RPA</th>
                                        <th>GPS</th>
                                        <th>IR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysql_fetch_assoc($result)) {
                                        $qr_verifica = mysql_query("SELECT  
                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa = '{$row[id_rpa]}' AND tipo_vinculo = 1  ORDER BY data_proc DESC LIMIT 1) as rpa_normal,

                                                                    (SELECT IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa =  '{$row[id_rpa]}' AND tipo_vinculo = 2  ORDER BY data_proc DESC LIMIT 1) as rpa_gps,

                                                                    (SELECT  IF(B.estorno != 0 ,'estorno', B.status)
                                                                    FROM rpa_saida_assoc as A
                                                                    INNER JOIN saida as B 
                                                                    ON A.id_saida = B.id_saida
                                                                    WHERE id_rpa =  '{$row[id_rpa]}' AND tipo_vinculo = 3  ORDER BY data_proc DESC LIMIT 1) as rpa_ir") or die(mysql_error());
                                        $row_verifica = mysql_fetch_assoc($qr_verifica);

                                        switch ($row_verifica['rpa_normal']) {
                                            case 1: $color['rpa_normal'] = 'cor-1';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 2: $color['rpa_normal'] = 'cor-3';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 'estorno': $color['rpa_normal'] = 'cor-4';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            case '0': $color['rpa_normal'] = 'cor-5';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            default: $color['rpa_normal'] = '';
                                                $pagina_rpa_normal = 'cadastro_rpa_guias.php';
                                        }

                                        switch ($row_verifica['rpa_gps']) {
                                            case 1: $color['rpa_gps'] = 'cor-1';
                                                $pagina_gps = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 2: $color['rpa_gps'] = 'cor-3';
                                                $pagina_gps = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 'estorno': $color['rpa_gps'] = 'cor-4';
                                                $pagina_gps = 'visualizar_rpa_saidas.php';
                                                break;

                                            case '0': $color['rpa_gps'] = 'cor-5';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            default: $color['rpa_gps'] = '';
                                                $pagina_gps = 'cadastro_rpa_guias.php';
                                        }

                                        switch ($row_verifica['rpa_ir']) {
                                            case 1: $color['rpa_ir'] = 'cor-1';
                                                $pagina_ir = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 2: $color['rpa_ir'] = 'cor-3';
                                                $pagina_ir = 'visualizar_rpa_saidas.php';
                                                break;

                                            case 'estorno': $color['rpa_ir'] = 'cor-4';
                                                $pagina_ir = 'visualizar_rpa_saidas.php';
                                                break;

                                            case '0': $color['rpa_ir'] = 'cor-5';
                                                $pagina_rpa_normal = 'visualizar_rpa_saidas.php';
                                                break;

                                            default: $color['rpa_ir'] = '';
                                                $pagina_ir = 'cadastro_rpa_guias.php';
                                        }

                                        if ($row['id_projeto'] != $projetoAnt and ! empty($projetoAnt)) {

                                            echo'<tr height="40"  style="background-color: #c8ebf9"><td colspan="9" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                            $totalizador_projeto = 0;
                                        }
                                        ?>
                                        <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                            <td><?php echo $row['nome_projeto']; ?></td>
                                            <td align="center"><?php echo $row['id_autonomo'] ?></td>
                                            <td><?php echo $row['nome']; ?></td>
                                            <td><?php echo $row['funcao']; ?></td>
                                            <td><?php echo $row['cpf'] ?></td>
                                            <td><?php echo $row['pis'] ?></td>
                                            <td><?php echo $row['nome_banco'] ?></td>
                                            <td><?php echo $row['agencia'] ?></td>
                                            <td><?php echo $row['conta'] ?></td>
                                            <td><?php echo number_format($row['valor_liquido'], 2, ',', '.'); ?></td>
                                            <td align="center" class="<?php echo $color['rpa_normal']; ?>"> 

                                                <a href="<?php echo $pagina_rpa_normal; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=1&id_autonomo=<?php echo $row['id_autonomo']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                            </td>
                                            <td align="center" class="<?php echo $color['rpa_gps']; ?>">                                    
                                                <a href="<?php echo $pagina_gps; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=2&id_autonomo=<?php echo $row['id_autonomo']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" ><img src="../imagensrh/gps.jpg"  ></a>
                                            </td>
                                            <td align="center" class="<?php echo $color['rpa_ir']; ?>">
                                                <a href="<?php echo $pagina_ir; ?>?id_rpa=<?php echo $row['id_rpa'] ?>&tipo_guia=3&id_autonomo=<?php echo $row['id_autonomo']; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&TB_iframe=true&width=1100" class="thickbox" ><img src="../imagensrh/ir.jpg" /></a>
                                            </td>
                                        </tr>
                                        <?php
                                        $totalizador_projeto +=$row[valor_liquido];
                                        $total_regiao += $row['valor_liquido'];
                                        $projetoAnt = $row['id_projeto'];
                                    }
                                    echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="8" align="right" style="font-weight:bold;">SUBTOTAL:</td><td colspan="4"> R$ ' . number_format($totalizador_projeto, 2, ',', '.') . '</td></tr>';
                                    echo'<tr height="40" style="background-color: #c8ebf9"><td colspan="8" align="right" style="font-weight:bold;">TOTAL: </td><td colspan="4"> R$ ' . number_format($total_regiao, 2, ',', '.') . '</td></tr>';
                                    unset($cor);
                                    unset($color);
                                } elseif ($tipoPagamento == 5) {
                                    $cor = 0;
                                    ?>
                                    <!-- TABLE VT -->
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>ID Projeto</th>
                                            <th>Nome Projeto</th>
                                            <th>ID Região</th>
                                            <th>Região</th>
                                            <th>VT</th>
                                            <th>VR</th>
                                            <th>VA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysql_fetch_array($result)) { ?>
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?> even">
                                                <td class="center"><?= $row['id_projeto']; ?></td>
                                                <td><?= $row['nome_projeto'] ?></td>
                                                <td class="center"><?= $row['id_regiao']; ?></td>
                                                <td><?= $row['nome_regiao'] ?></td>
                                                <td align="center" class="<?= '' ?>">
                                                    <a href="form_guia.php?tela=5&id_projeto=<?= $row['id_projeto'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img src="/intranet/imagens/icones/icon-doc.gif" />
                                                    </a>                                      
                                                </td>
                                                <td align="center" class="<?= '' ?>">
                                                    <a href="form_guia.php?tela=6&id_projeto=<?= $row['id_projeto'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img src="../imagensrh/gps.jpg" />
                                                    </a>                                      
                                                </td>
                                                <td align="center" class="<?= '' ?>">
                                                    <a href="form_guia.php?tela=7&id_projeto=<?= $row['id_projeto'] ?>&mes=<?= $mes ?>&ano=<?= $ano ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img src="/intranet/imagens/icones/icon-doc.gif" />
                                                    </a>                                      
                                                </td>
                                            </tr>
                                        <?php } unset($cor) ?>
                                    </tbody>
                                </table>
                            <?php } else if ($tipoPagamento == 6) { ?>
                                <table width="100%" cellspacing="0" cellpadding="0" class="grid">
                                    <thead>
                                        <tr>
                                            <th>ID folha</th>
                                            <th>Região</th>
                                            <th>Projeto</th>
                                            <th>PG. SINDICATO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysql_fetch_array($result)) {
                                            $sql = "SELECT (SELECT IF(B.estorno != 0 ,'estorno', B.status) FROM pagamentos AS A 
                                                    LEFT JOIN saida AS B ON(A.id_saida=B.id_saida) 
                                                    WHERE A.mes_pg='{$mes}' AND A.ano_pg = '{$ano}' AND A.tipo_pg = 8 AND A.id_folha = {$row['id_folha']} AND A.tipo_contrato_pg = 1 ORDER BY data_proc DESC LIMIT 1) AS sindicato";
                                            $query_controle = mysql_query($sql);
                                            echo '<!-- ' . $sql . ' -->';
                                            $row_controle = mysql_fetch_assoc($query_controle);
//                                            echo mysql_num_rows($query_controle);
                                            switch ($row_controle['sindicato']) {
                                                case '0':
                                                    $color = "cor-2";
                                                    $link_guias = 'cadastro_1_novaintra.php'; // depois alterar o nome para cadastro_1_novaintra.php
                                                    break;
                                                case 1:
                                                    $color = "cor-1";
                                                    $link_guias = 'visualizar_guias_saidas_novaintra.php';

                                                    break;
                                                case 2:
                                                    $color = "cor-3";
                                                    $link_guias = 'visualizar_guias_saidas_novaintra.php';
                                                    break;
                                                case 'estorno': $color[$i] = 'cor-4';
                                                    $link_guias = 'visualizar_guias_saidas_novaintra.php';
                                                    break;

                                                default: $color = '';
                                                    $link_guias = 'cadastro_1_novaintra.php'; // depois alterar o nome para cadastro_1_novaintra.php
                                            }
                                            ?>
                                            <tr class="<?php echo ($cor++ % 2 == 0) ? "even" : "odd"; ?>">
                                                <td><span class="dados"><?= $row['id_folha'] ?></span></td>
                                                <td><span class="dados"><?= $row['id_regiao'] . " - " . $row['regiao']; ?></span></td>
                                                <td><span class="dados"><?= $row['nome'] . $decimo3 ?></span></td>
                                                <td align="center" class="<?= $color ?>">
                                                    <a href="<?php echo $link_guias; ?>?id_folha=<?= $row['id_folha'] ?>&tipo_guia=8&tipo_contrato=<?php echo $tipoContratacao; ?>&mes_consulta=<?php echo $mes; ?>&ano_consulta=<?php echo $ano; ?>&regiao=<?php echo $regiao; ?>&TB_iframe=true&width=1100" class="thickbox" > 
                                                        <img src="../folha/imagens/verfolha.gif" />
                                                    </a>                                      
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        unset($cor);
                                        ?>           
                                    </tbody>
                                </table>
                            <?php } ?>
                        <?php } ?>
<?php } ?>
            <?php include_once '../../template/footer.php'; ?>
            </form>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../jquery/thickbox/thickbox.js"></script>
        <script>
        $(function(){
            $(".bt,.bt-rpa").css('cursor', 'pointer');
                $(".bt-ver").css('cursor', 'pointer');
                $(".bt-criar").css('cursor', 'pointer');
                $('#tipo_pagamento').change(function(){
                    if ($(this).val() == 4){
                        $('#tipo_contrato').val(1);
                    } else{
                        $('#tipo_contrato').val(2);
                    };
                });
                $(".bt").click(function(){
                    var botao = $(this);
                    var id = botao.data('key');
                    var type = botao.data('type');
                    var title = botao.data('title');
                    var classe = botao.parent().attr('class');
                    var tipo_contrato = botao.data('tipo_contrato');
                    if (classe != ""){
                        //ja existe saída
                        thickBoxIframe("Detalhes " + title, "index_popup.php", {id: id, tipo: type, tipo_contrato: tipo_contrato}, 1100, 450);
                    } else{
                        thickBoxIframe("Detalhes " + title, "cadastro_1_novaintra.php", {id: id, tipo: type, contratacao: $("#tipo_contrato").val()}, 1100, 450, null, false);
                        callFunctionsCad();
                    }
                });
                $(".bt-ver").click(function(){
                    var botao = $(this)
                    var id = botao.data('key');
                    var type = botao.data('tipo');
                    var mes = botao.data('mes');
                    var ano = botao.data('ano');
                    thickBoxIframe("Rescisão", "popup_comprovante.php", {id_clt: id, tipo: type, mes: mes, ano: ano}, 1100, 450);
                });
                $(".bt-criar").click(function(){
                    var botao = $(this)
                    var id = botao.data('key');
                    var type = botao.data('tipo');
                    var mes = botao.data('mes');
                    var ano = botao.data('ano');
                    thickBoxIframe("Rescisão", "popup_cadresci.php", {id_clt: id, tipo: type, mes: $("#mes").val(), ano: $("#ano").val()}, 1100, 450);
                });
                /*****FORMULÁRIO DE CADASTRO VT E VR ****/

                $(".mostrarNome").hide();
                $('#valor').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });
                var id_saida = 0;
                $('#data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
                function reseta(){
                    $("input[type*='text']").each(function(){
                        $(this).val('');
                    });
                }

                $('input[name=cod_barra]').change(function(){
                    if ($(this).val() == 1){
                        $('.campo_codigo_gerais').show();
                    } else{
                        $('.campo_codigo_gerais').hide();
                        $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val('');
                    }
                })
                $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5').keyup(function(){ limita_caractere($(this), 5, 1) });
                $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
                $('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });
                $('#campo_codigo_gerais8').keyup(function(){
                    if ($(this).val().length >= 14){
                        $(this).blur();
                        var valor = $(this).val().substr(0, limite);
                        $(this).val(valor);
                    }
                });
                function limita_caractere(campo, limite, muda_campo){
                    var tamanho = campo.val().length;
                    if (tamanho >= limite){
                        campo.next().focus();
                        var valor = campo.val().substr(0, limite);
                        campo.val(valor)
                    }
                }

                //////VALIDANDO
                $('#arquivo1,#arquivo2').change(function(){
                    var aviso = $('.aviso');
                    var arquivo = $(this);
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();
                    if (arquivo.val() != '' && extensao_arquivo == '.pdf'){
                        arquivo.css('background-color', '#51b566')
                        .css('color', '#FFF');
                        aviso.html('');
                    }

                    if (extensao_arquivo != '.pdf') {
                    arquivo.css('background-color', ' #f96a6a')
                        .css('color', '#FFF');
                        aviso.html('Este arquivo não é um PDF.');
                    }
                });
                $('form').submit(function(){
                    var aviso = $('.aviso');
                    var arquivo = $('#arquivo1');
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();
                    var arquivo2 = $('#arquivo2');
                    var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();
                    var count = 0;
                    $(".j_verifica:checked").each(function(i, value){
                        count++;
                    });
                    if ($('#tipo_guia').val() == 5 || $('#tipo_guia').val() == 6){
                        if (count < 1){
                            aviso.html('Selecione um tipo de pagamento');
                            return false;
                        }
                    }

                    if ($('#valor').val() == ''){
                        aviso.html('Digite o valor.');
                        return false;
                    }

                    if ($('#data').val() == ''){
                        aviso.html('Digite a data.');
                        return false;
                    }

                    if ($('#bancos').val() == ''){
                        aviso.html('Selecione o banco.');
                        return false;
                    }

                    if (arquivo.val() == ''){
                        aviso.html('O arquivo não foi anexado');
                        return false;
                    }

                    if ($('#tipo_guia').val() == 2){
                        if (arquivo2.val() == ''){
                            aviso.html('O arquivo não foi anexado');
                            return false;
                        }
                    }

                    if (extensao_arquivo != '.pdf'){
                        aviso.html('Este arquivo não é um PDF.');
                        return false;
                    }

                });
                /**JQUERY GORDO*/
                $(".j_tipo_pgto").click(function(){
                    $(this).each(function(i, value){
                        if ($(this).val() == "1"){
                            $(".mostrarNome").show();
                            $(".textRemove").hide();
                            $("#nomeTitulo").val("");
                            $(".viewNome").text("").show();
                        } else if ($(this).val() == "2"){
                            $(".mostrarNome").hide();
                            $(".textRemove").show();
                            $(".viewNome").hide();
                        }
                    });
                });
                $("#nomeTitulo").keyup(function(){
                    var nome = ($(this).val());
                    $(".viewNome").css({textTransform:"uppercase"}).html(' - ' + nome + ' - ');
                    $("#nome").val($(".nomeCompleto").text());
                });
                /*******/
            });
            var callFunctionsCad = function(){
            $('#valor').priceFormat({
            prefix: '',
                centsSeparator: ',',
                thousandsSeparator: '.'
            });
            var id_saida = 0;
            $('#data').mask('99/99/9999');
            $('#data').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true
            });
            $('p.botao').click(function(){
                var valor = parseInt($('#valor').val());
                if ($('#progressBar').html() != "" || valor > 0){
                    var cod1 = $('#campo_codigo_gerais1').val();
                    var cod2 = $('#campo_codigo_gerais2').val();
                    var cod3 = $('#campo_codigo_gerais3').val();
                    var cod4 = $('#campo_codigo_gerais4').val();
                    var cod5 = $('#campo_codigo_gerais5').val();
                    var cod6 = $('#campo_codigo_gerais6').val();
                    var cod7 = $('#campo_codigo_gerais7').val();
                    var cod8 = $('#campo_codigo_gerais8').val();
                    var cod_barra_gerais = cod1 + cod2 + cod3 + cod4 + cod5 + cod6 + cod7 + cod8;
                    $.post('actions/cadastra.php', {
                        id_folha    : $("#id_folha").val(),
                        tipo_contrato : $("#tipo_folha").val(),
                        tipo 	: $("#tipo").val(),
                        subgrupo    : $("#subgrupo").val(),
                        nome 	: $('#nome').val(),
                        valor 	: $('#valor').val(),
                        data 	: $('#data').val(),
                        regiao 	: $('#fregiao').val(),
                        projeto     : $('#fprojeto').val(),
                        banco       : $('#bancos').val(),
                        mes_pg      : $('#mes').val(),
                        ano_pg      : $('#ano').val(),
                        tipo_pg     : $('#tipo_pg').val(),
                        folha_regiao: $('#folha_regiao').val(),
                        folha_projeto: $('#folha_projeto').val(),
                        cod_barra_gerais: cod_barra_gerais
                    },
                    function(result){
                        id_saida = result;
                        $('#arquivo').uploadifySettings('scriptData', {'id_saida'   : id_saida <?php if ($texto == "GPS") { ?>, tipo_gps: 1 <?php } ?>});
                        $('#arquivo').uploadifyUpload();
                        $('#arquivo2').uploadifySettings('scriptData', {'id_saida'   : id_saida <?php if ($texto == "GPS") { ?>, tipo_gps: 2 <?php } ?> });
                        $('#arquivo2').uploadifyUpload();
                    });
                    reseta();
                } else{
                    alert('Por favor anexe um arquivo');
                }
            });
            var Parametros = {
                'uploader'  : '../../uploadfy/scripts/uploadify.swf',
                'script'    : 'actions/upload.php',
                'cancelImg' : '../../uploadfy/cancel.png',
                'auto'      : false,
                'buttonText': 'Anexar PDF',
                'folder'    : '../comprovantes',
                'queueID'   : 'progressBar',
                'scriptData': {'id_saida'   : id_saida <?php if ($texto == "GPS") { ?>, tipo_gps: 1 <?php } ?> },
                'fileDesc'  : 'Somente arquivos PDF',
                'fileExt'   : '*.pdf;',
                'onComplete': function(a, b, c, d){
                alert('Concluido com sucesso!');
                    location.reload();
                },
                'onAllComplete': function(){}
            }

            //USADO SOMENTE NA GPS                                                  
            var Parametros2 = {
                'uploader'  : '../../uploadfy/scripts/uploadify.swf',
                'script'    : 'actions/upload.php',
                'cancelImg' : '../../uploadfy/cancel.png',
                'auto'      : false,
                'buttonText': 'Anexar PDF',
                'folder'    : '../comprovantes',
                'queueID'   : 'progressBar',
                'scriptData': {'id_saida'   : id_saida, tipo_gps: 2 },
                'fileDesc'  : 'Somente arquivos PDF',
                'fileExt'   : '*.pdf;',
                'onComplete': function(a, b, c, d){
                alert('Concluido com sucesso!');
                    location.reload();
                },
                'onAllComplete': function(){ }
            }

            $('#arquivo').uploadify(Parametros);
                $('#arquivo2').uploadify(Parametros2);
                function reseta(){
                    $("input[type*='text']").each(function(){
                        $(this).val('');
                    });
                }

                $('input[name=cod_barra]').change(function(){
                    if ($(this).val() == 1){
                        $('.campo_codigo_gerais').show();
                    } else{
                        $('.campo_codigo_gerais').hide();
                        $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val('');
                    }
                });
                $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5').keyup(function(){ limita_caractere($(this), 5, 1) });
                $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
                $('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });
                $('#campo_codigo_gerais8').keyup(function(){
                    if ($(this).val().length >= 14){
                        $(this).blur();
                        var valor = $(this).val().substr(0, limite);
                        $(this).val(valor);
                    }
                });
                function limita_caractere(campo, limite, muda_campo){
                    var tamanho = campo.val().length;
                    if (tamanho >= limite){
                        campo.next().focus();
                        var valor = campo.val().substr(0, limite);
                        campo.val(valor);
                    }
                }
            }
        </script>
    </body>
</html>
        