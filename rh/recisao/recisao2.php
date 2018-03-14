<?php
/*
 * provisao_de_gastos
 * 
 * 00-00-0000
 * 
 * Rotina para processamento de provisão de gastos em lote
 * 
 * Versão: 1.1.0000 - 31/07/2015 - Jacques - Implementação de tabela temporária para geração de provisão de gastos com compatibilidade retroativa
 * Versão: 1.2.1505 - 18/08/2015 - Jacques - Reativando a codificação da tabela temporária para geração de provisão de gastos sem compatibilidade retroativa
 * Versão: 1.3.1671 - 25/08/2015 - Jacques - Correção de bug para variáveis que recebem POST como string passadas com cast forçado para (int) quando necessário
 * Versão: 1.3.2050 - 08/09/2015 - Jacques - Correção de bug no INSERT da rh_recisao que erradamente passei a executala apôs o if que determina a tabela rh_recisao ou rh_recisao_provisao_de_gastos. 
 *                                           A query de inserção do rescisao individual é feita através de um arquivo .txt na execução da tela 4. Adicionado
 *                                           o footer de controle de versão.
 * Versão: 1.3.2615 - 30/09/2015 - Jacques - Caso as férias proporcionais sejam de 12/12 avos, então as férias proporcionais deverão ser consideradas vencidas.
 *                                           Obs: Essa operação afeta apenas a exibição dos valores no formulário de rescisão.   
 * Versão: 1.3.2683 - 02/10/2015 - Jacques - $total_rendi adicionado ao calculo da variável $fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $total_rendi) / $qnt_dias_mes) * $qnt_dias_fp; //AKI
 * Versão: 1.3.3356 - 21/10/2015 - Jacques - $objMovimento->setAno(2014) alterado para date("Y") que estava setando o ano$saldo_de_salario com valor fixo e estava sendo feito uma verificação do vetor $verfica_movimento para inclusão de periculosidade sobre uma função que 
 *                                           sempre retorna um vetor em qualquer condição de consulta. Alterado para a condição para especificação de campo no vetro como $verfica_movimento['id_movimento']
 * Versão: 1.3.4018 - 11/11/2015 - Jacques - Adicionado opção de passar parâmetro sem usar a variável enc para depuração de processamento.
 * Versão: 1.3.5040 - 16/12/2015 - Jacques - Adicionado condição específica para processamento de rescisão do Clt 6846 que não teve direito a férias no período e a proporcional deve ser 06/12
 * Versão: 1.3.5872 - 26/01/2016 - Ramon   - Adicionado funcionalidade de gerar a previa de rescisão somente passando parametros pelo Browser
 * Versão: 1.3.6125 - 02/02/2016 - Jacques - Acerto no calculo para encontrar a diferença em anos para aplicação da lei 12.506
 * Versão: 1.3.6151 - 03/02/2016 - Jacques - Adicionado o valor da insalubridade integral para o calculo do valor do artigo 480 e 479 = $valor_art_480_479 = (($salario_base_limpo+$valor_insalubridade_integral) / 30) * ($dias_restantes / 2); Instrução de Jeferson via Skype no dia 03/02/2016
 * Versão: 1.3.6153 - 03/02/2016 - Jacques - Para o tipo de dispensa 63 o valor também para calculo geral da rescisão deverá ser proporcional $valor_insalubridade = $insalubridade['valor_proporcional']; 
 * Versão: 1.3.6394 - 15/02/2016 - Jacques - Adicionado uma condição específica para o clt 4811 no if($dias_total_evento > 180 && $id_clt != 4811){ pois o evento do mesmo não deveria suspender suas férias proporcionais segundo Jeferson
 * Versão: 1.3.???? - 06/05/2016 - Ramon   - IABAS - Carregar dados pré salvos na tabela rh_rescisao_clt_conf no CASE 2
 *  
 *  
 * @author Não definido
 * 
 * 
 */

$programadores = array(179, 158, 260, 257, 258, 275, 353, 349);

include('../../conn.php');
if ($_REQUEST['recisao_coletiva'] != 1) {
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=true';</script>";
        exit;
    }
}

include('../../classes/funcionario.php');
include('../../classes/curso.php');
include('../../classes/clt.php');
include('../../classes/projeto.php');
include('../../classes/calculos.php');
include('../../classes/global.php');

include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');

include('../../classes_permissoes/acoes.class.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/RescisaoClass.php');
include('../../classes/CalculoFolhaClass.php');
include('../../classes/CalculoRescisaoClass.php');
include_once "../../classes/LogClass.php";
include('../../classes/MovimentoClass.php');
include('../../classes/CltClass.php');

include('../../classes/EventoClass.php');

//include("../../classes/RhClass.php");
//$rh = new RhClass();
//$rh->AddClassExt('Clt'); 
//if($_COOKIE['logado'] == 179){
//    exit("Até aqui nos ajudou o Senhor ...");
//}


/*
 * Leonardo - 2017-03-06
 * conforme convenção sindical:
 * Para os trabalhadores com mais de 45 anos de idade e mais de um ano de casa, 
 * será concedido aviso prévio de 45 dias, sem prejuizo do disposto no item acima, 
 * limitando a soma total do período de aviso previo a 90 dias.
 */
function aviso_previo_convencao($id_sindicato, $data_nascimento, $data_entrada) {


    // verificando se faz parte dos sindicatos
    $array_sindicato = array(26, 27, 34, 9);
    $verifica_sindicato = in_array($id_sindicato, $array_sindicato);

    // verificar idade +45
    $data1 = new DateTime();
    $data2 = new DateTime($data_nascimento);
    $ddd = $data1->diff($data2);
    $idade = $ddd->y;
    $verifica_idade = $idade >= 45;

    // verfica tempo +1 ano
    $data1 = new DateTime();
    $data2 = new DateTime($data_entrada);
    $ddd = $data1->diff($data2);
    $tempo_servico = $ddd->y;
    $verifica_tempo_servico = $tempo_servico >= 1;

    if ($verifica_sindicato && $verifica_idade && $verifica_tempo_servico) {
        $dias_aviso = 45;
    } else {
        $dias_aviso = 30;
    }
    if ($_COOKIE['logado'] == 349) {
        echo "<pre>";
        echo "REGRA DE 45 dias de aviso<br>";
        echo "id_sindicato = {$id_sindicato}<br>";
        echo "idade = $idade<br>";
        echo "tempo_servico = $tempo_servico<br>";
        echo "dias_aviso = $dias_aviso<br>";
        echo "</pre>";
    }
    return $dias_aviso;
}

if ($_REQUEST['recisao_coletiva']) {

    ob_start();

    $table_rh_recisao = 'rh_recisao_provisao_de_gastos';
} else {
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=true';</script>";
        exit;
    }

    $table_rh_recisao = 'rh_recisao';
}


$usuario = carregaUsuario();
$rescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();
$objCalcRescisao = new Calculo_Rescisao();
$dadosClt = new CltClass();
$mov = new Movimentos();
$Trab = new proporcional();

function verificaRecisao($id_clt) {
    /*
     * Verifica se já foi realizada rescisão para o funcionário
     */
//    $retorno = montaQuery($table_rh_recisao, 'id_clt,nome', "id_clt = '{$id_clt}' AND status = 1");
//    $clt_status = montaQuery('rh_clt', 'status', "id_clt='{$id_clt}'");
//    $clt_status = $clt_status[1]['status'];
//    if (isset($retorno[1]['id_clt']) && !empty($retorno[1]['id_clt']) && isset($clt_status) && !empty($clt_status)) {
    ?>
    <!--        <script type="text/javascript">
                alert('A rescisão deste funcionário já foi realizada.\nNome: ' + '//<?php echo $retorno[1]['nome'] ?>');
                window.history.back();
            </script>-->
    <?php
//        exit();
//    }
}

$Fun = new funcionario();
$Fun->MostraUser(0);
$user = $Fun->id_funcionario;
$ACOES = new Acoes();
$regiao = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$Calc = new calculos();
$Curso = new tabcurso();
$Clt = new clt();
$ClasPro = new projeto();
$obj_recisao = new Rescisao();

$eventos = new Eventos();

//echo '<pre>';
//print_r($_REQUEST);
//echo '</pre>';

$optTiposDispensa = $rescisao->listTiposRescisao("array");
$optFator = array("empregado" => "empregado", "empregador" => "empregador");
$optSelFator = array('1' => "empregado", '2' => "empregador");
$optAviso = array("indenizado" => "indenizado", "trabalhado" => "trabalhado", "aus/dispen" => "Ausencia/Dispensa");
$optSelAviso = array('1' => "indenizado", '2' => "trabalhado");

if (empty($_REQUEST['tela'])) {
    $tela = 1;
} else {
    $tela = $_REQUEST['tela'];
}

if (isset($_POST['desprocFerias'])) {
    $id_ferias = $_POST['id'];

    $sql = "UPDATE rh_ferias SET status = 0, desprocessado_recisao = 1, dt_desproc_rescisao = NOW(), id_funcionario_desproc_rescisao = '{$_COOKIE['logado']}' WHERE id_ferias = $id_ferias LIMIT 1;";
    if (mysql_query($sql)) {
        echo true;
    }
    exit;
}

if (isset($_POST['desprocFeriasProg'])) {
    $id_ferias = $_POST['id'];

    $sql = "UPDATE rh_ferias_programadas SET status = 0 WHERE id_ferias_programadas = $id_ferias LIMIT 1;";
    if (mysql_query($sql)) {
        echo true;
    }
    exit;
}


if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {

    if ($_REQUEST['method'] == "desprocessar_recisao") {
        /**
         * ATUALIZANDO A TABELA DE RH_CLT
         * COM A DATA ATUAL DA AÇÃO DE 
         * FINALIZAR A FOLHA
         */
//        $rh->Clt->setDefault()->setIdClt($_REQUEST['id_clt'])->onUpdate();
        //onUpdate($_REQUEST['id_clt']);

        $retorno = array("status" => false);
        $dados = $obj_recisao->verificaSaidaPagaDeRecisao($_REQUEST['id_rescisao'], $_REQUEST['id_regiao'], $_REQUEST['id_clt'], $_REQUEST['tpCanAvisoPr'], $_REQUEST['obs']);
        return $dados;
    }
}

if ($_GET['voltar_aguardando'] == true) {

    /**
     * ATUALIZANDO A TABELA DE RH_CLT
     * COM A DATA ATUAL DA AÇÃO DE 
     * FINALIZAR A FOLHA
     */
//    $rh->Clt->setDefault()->setIdClt($_REQUEST['id_clt'])->onUpdate();
    //onUpdate($_REQUEST['id_clt']);

    $rsclt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '" . $_GET['id_clt'] . "'");
    $rowClt = mysql_fetch_assoc($rsclt);

    //dados para gravar log
    $local = "Desprocessar Aguardando Demissão";
    $ip = $_SERVER['REMOTE_ADDR'];
    $acao = "{$usuario['nome']} desprocessou o clt {$_GET['id_clt']}";
    $id_usuario = $usuario['id_funcionario'];
    $tipo_usuario = $usuario['tipo_usuario'];
    $grupo_usuario = $usuario['grupo_usuario'];
    $regiao_usuario = $usuario['id_regiao'];

    $rsEvent = mysql_query("SELECT id_evento FROM rh_eventos WHERE id_clt = '" . $_GET['id_clt'] . "' AND cod_status = '991' AND status = 1"); //SELECIONANDO O EVENTO DE AGUARDANDO DEMISSÃO
    $arrEventos = array();
    while ($row = mysql_fetch_assoc($rsEvent)) {
        $arrEventos[] = $row['id_evento'];
    }

    $sql1 = "UPDATE rh_clt SET status = '10', data_saida = '', data_aviso = '', data_demi = '',  status_demi = '' WHERE id_clt = '" . $_GET['id_clt'] . "' LIMIT 1";
    $sql2 = "UPDATE rh_eventos SET status = '0' WHERE id_evento IN (" . implode(",", $arrEventos) . ")";
    $sql3 = "INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')";

    mysql_query($sql1);
    mysql_query($sql2);
    mysql_query($sql3);

    header("Location: recisao2.php?regiao={$_GET['regiao']}");
}

$sqlBanco = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$_GET['regiao']} ORDER BY id_banco");
while ($rowBanco = mysql_fetch_array($sqlBanco)) {
    $optionBanco .= "<option value='{$rowBanco['id_banco']}'>{$rowBanco['razao']}({$rowBanco['nome']})</option>";
}

require_once('../../classes/ArquivoTxtBancoClass.php');
$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();
$arrayArquivos = $ArquivoTxtBancoClass->getRegistros('r');
if (isset($_REQUEST['arqRescisao']) AND ! empty($_REQUEST['arqRescisao'])) {
    $ArquivoTxtBancoClass->gerarTxtBanco('RESCISAO', $_REQUEST['banco'], $_REQUEST['data'], $_REQUEST['arqRescisao']);
    header("Location: arquivo_banco_rescisao.php");
}

// verifica se há session iniciada
//if(isset($_SESSION['projeto'])){
//    $projetoR = $_SESSION['projeto'];
//    session_destroy();
//}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Intranet :: Rescis&atilde;o</title>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="../../net1.css" rel="stylesheet" type="text/css">

            <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
            <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">

                <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
                <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
                <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
                <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />

                <!--<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>-->
                <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
                <script type="text/javascript" src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
                <script type="text/javascript" src="../../js/ramon.js"></script>
                <script type="text/javascript" src="../../js/global.js"></script>
                <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
                <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
                <script src="../../js/highslide-with-html.js" type="text/javascript"></script>

                <script src="../../resources/js/bootstrap.min.js"></script>
                <script src="../../resources/js/tooltip.js"></script>
                <script src="../../resources/js/main.js"></script>

<!--<script type="text/javascript" src="../../js/jquery.validationEngine-2.6.js"></script>-->
<!--<script type="text/javascript" src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>-->
                <script type="text/javascript">
                    hs.graphicsDir = '../../images-box/graphics/';
                    hs.outlineType = 'rounded-white';

                    $(function () {
                        //                $("#form1").validationEngine();
                        //COMENTANDO CONFIGURAÇÕES AUTOMATICAS AQUI, POIS JA FORAM VALIDADAS NA TELA DE PRE-RESCISÃO    
                        /*$('#dispensa').change(function() {
                         
                         var dispensa = parseInt($(this).val());
                         //                    console.log(dispensa);
                         
                         switch (dispensa) {
                         case 64:
                         case 66:
                         case 61:
                         $('#fator').val('empregador').css('background-color', '#eeeeee');
                         break;
                         
                         case 63:
                         case 65:
                         $('#fator').val('empregado').css('background-color', '#eeeeee');
                         break;
                         
                         default:
                         $('#fator').val('empregador').css('background-color', '#eeeeee');
                         
                         }
                         
                         if (dispensa == 61 || dispensa == 62 || dispensa == 65) {
                         
                         $('#aviso').css('background-color', '#ffffff').attr('disabled', false);
                         $('#previo').css('background-color', '#ffffff').attr('disabled', false);
                         $('#data_aviso').css('background-color', '#ffffff').attr('disabled', false);
                         
                         } else {
                         
                         $('#aviso').val('').css('background-color', '#eeeeee').attr('disabled', true);
                         $('#previo').css('background-color', '#eeeeee').attr('disabled', true);
                         $('#data_aviso').css('background-color', '#eeeeee').attr('disabled', true);
                         
                         }
                         
                         //                    if(dispensa !== 61){
                         //                        $('#tpAvisoPre').attr('disabled', true);
                         //                        $('#obs').attr('disabled', true);
                         //                        $('#tpAvisoPre').val('');
                         //                        $('#obs').val('');
                         //                    }
                         
                         });*/


                        //$('#dispensa').change();

                        $('#data_aviso').datepicker({
                            changeMonth: true,
                            changeYear: true

                        });


                        $('#desprocessaFerias').click(function () {
                            var id_ferias = $(this).data('key');

                            if (confirm('Tem certeza que quer desprocessar as férias?')) {
                                $.post('recisao2.php', {tela: 2, desprocFerias: 1, id: id_ferias}, function (data) {
                                    if (data == 1) {
                                        alert('As férias foi desprocessada.');
                                        $('.linha_ferias').fadeOut('slow');
                                    }

                                }, 'html');

                            }

                            return false;
                        });

                        $('#desprocessaFeriasProg').click(function () {
                            var id_ferias = $(this).data('key');

                            if (confirm('Tem certeza que quer desprocessar as férias?')) {
                                $.post('recisao2.php', {tela: 2, desprocFeriasProg: 1, id: id_ferias}, function (data) {
                                    if (data == 1) {
                                        alert('As férias foi desprocessada.');
                                        $('.linha_ferias').fadeOut('slow');
                                    }

                                }, 'html');

                            }

                            return false;
                        })




                        $('#gerar').click(function () {

                            var regiao = $('#regiao').val();
                            var data_escolhida = $('#data_aviso').val();

                            $.ajax({
                                url: 'action.verifica_folha.php?data=' + data_escolhida + '&regiao=' + regiao,
                                type: 'GET',
                                dataType: 'json',
                                success: function (resposta) {

                                    if (parseInt(resposta.verifica) == 0) {

                                        alert('A data escolhida ultrapassou o prazo de 30 dias após a última folha finalizada \n\n Data da última folha: ' + resposta.data_ult_folha + '.');
                                        $('#data_aviso').val('');

                                        return false;
                                    } else {

                                        $('.form').submit();
                                    }
                                }
                            });

                        });


                        $(".remove_recisao").click(function () {
                            $("#CancelAviso").show();
                            thickBoxModal("Desprocessar Recisão", "#CancelAviso", 350, 400);
                            $("#idCanRescisao").val($(this).attr("data-recisao"));
                            $("#idCanRegiao").val($(this).attr("data-regiao"));
                            $("#idCanClt").val($(this).attr("data-clt"));

                        });

                        $(".btn").click(function () {
                            if ($(this).val() == 'Sim') {
                                var id_rescisao = $("#idCanRescisao").val();
                                var id_regiao = $("#idCanRegiao").val();
                                var id_clt = $("#idCanClt").val();
                                var tpCanAvisoPr = $("#tpCancelAvisoPre").val();
                                var obs = $("#obsCancel").val();
                                $.ajax({
                                    url: "recisao2.php",
                                    type: "POST",
                                    dataType: "json",
                                    data: {
                                        tpCanAvisoPr: tpCanAvisoPr,
                                        obs: obs,
                                        id_rescisao: id_rescisao,
                                        id_regiao: id_regiao,
                                        id_clt: id_clt,
                                        method: "desprocessar_recisao"
                                    },
                                    success: function (data) {
                                        if (!data.status) {
                                            $(data.dados).each(function (k, v) {
                                                $(".data_demissao").html(v.data_demissao);
                                                $(".data_pagamento").html(v.data_pg);
                                                $(".nome").html(v.nome_clt);
                                                $(".status").html(v.status_saida);
                                                $(".valor").html(v.valor);
                                            });
                                            $("#mensagens").show();
                                            thickBoxModal("Desprocessar Recisão", "#mensagens", "350", "450");
                                        } else {
                                            history.go(0);
                                        }
                                    }
                                });
                            }

                            //history.go(0);
                        });

                        $(".detalhe_rendimentos").click(function () {
                            thickBoxModal("Total de rendimentos", ".div_detalhe_rendimentos", "350", "450");
                        });

                        // AMANDA

                        //                    $('#aviso').change(function() {
                        //                        var motivo = parseInt($('#dispensa').val());
                        //                        if ($(this).val() === 'trabalhado' && motivo === 61) {
                        //                            $('#tpAvisoPre').attr('disabled', false);
                        //                            $('#obs').attr('disabled', false);
                        //                        } else {
                        //                            $('#tpAvisoPre').val('');
                        //                            $('#obs').val('');
                        //                            $('#tpAvisoPre').attr('disabled', true);
                        //                            $('#obs').attr('disabled', true);
                        //                        }
                        //                    });

                    });

                </script>
                <style>
                    body {
                        background-color:#FAFAFA; text-align:center; margin:0px;
                    }
                    /*                p {
                                        margin:0px;
                                    }*/
                    #corpo {
                        width:90%; background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
                    }

                    .gerar_rel{
                        background-color: #E8E8E8;
                        display: block;
                        margin: 0;
                        text-decoration: none;
                        font-size: 14px;
                        font-weight: 200;
                        text-align: center;
                        color: #000;
                        padding: 2px;
                        border: 1px solid #E6E6E6;
                        width: 48%;
                        float: left;
                    }

                    .gerar_rel2{
                        background-color: #E8E8E8;
                        display: block;
                        margin: 0;
                        text-decoration: none;
                        font-size: 14px;
                        font-weight: 200;
                        text-align: center;
                        color: #000;
                        padding: 2px;
                        border: 1px solid #E6E6E6;
                        width: 47%;
                        float: right;
                    }

                    .gerar_rel:hover{
                        background-color:   #999;
                    }

                    .gerar_rel2:hover{
                        background-color:   #999;
                    }

                    #movimentos{
                        border-collapse: collapse;
                    }       

                    #movimentos tr{ border: 1px  #E8E8E8 solid;}        
                    #movimentos td{ border: 1px #E8E8E8  solid;}        
                    #movimentos thead{ font-weight: bold; text-align: center;}     

                    /*form com filtro da consulta*/
                    form.filtro{
                        margin: 10px auto;
                        width: 95%;
                    }
                    #mensagens{
                        display: none;
                    }
                    #mensagens h3{
                        text-align: center;
                        text-transform: uppercase;
                        font-size: 12px;
                        color: red;
                        text-align: left;
                    }
                    #mensagens p{
                        font-size: 12px;
                        color: #333;
                        margin: 0px;
                        padding: 0px;
                        text-align: left;
                    }

                    .div_detalhe_rendimentos{
                        display:none;
                    }

                </style>
                </head>
                <body class='novaintra'>
                    
                    <div id="corpo">
                        <div id="mensagens">
                            <h3>Erro ao Desprocessar</h3>
                            <p>Não é possível desprocessar essa rescisão, pois existe uma saida paga para a mesma.</p>
                            <br />
                            <p>Data demissão: <span class="data_demissao"></span></p>
                            <p>Data pagamento: <span class="data_pagamento"></span></p>
                            <p>Nome: <span class="nome"></span></p>
                            <p>Status: <span class="status"></span></p>
                            <p>Valor: <span class="valor"></span></p>
                        </div>
                        <div id="CancelAviso" style="display: none;">
                            <p>
                                <input type="hidden" id="idCanRescisao"/>
                                <input type="hidden" id="idCanRegiao"/>
                                <input type="hidden" id="idCanClt"/>
                            </p>
                            <p>Motivo do Cancelamento do Aviso Previo:</p>
                            <p><select id="tpCancelAvisoPre" name="tpCancelAvisoPre" class="validate[required]">
                                    <option value="">Selecione...</option>
<?php

$qr_canAvisoPre = mysql_query("SELECT id_tipoCanAvisoPre, descricao FROM tipo_cancelamento_aviso_previo;");
while ($rowAvisoPre = mysql_fetch_assoc($qr_canAvisoPre)) {
    ?>
                                        <option value="<?= $rowAvisoPre['id_tipoCanAvisoPre'] ?>"><?= $rowAvisoPre['descricao'] ?></option>
                                    <?php } ?>
                                </select>
                            </p>
                            <p>Observação:</p>
                            <p><textarea id="obsCancel" name="obsCancel" cols="30" rows="5"></textarea></p>
                            <p class="controls">
                                <input type="button"  class="btn" value="Sim"/>
                            </p>
                        </div>    

<?php

switch ($tela) {
    
    case 1:
        
        // tela de pesquisa
        // criar filtro para pesquisa
        if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
            if ($_REQUEST['projeto'] != '-1') {
                $filtroProjeto = "AND id_projeto = {$_REQUEST['projeto']}";
                $filtroProjetoJoin = "AND c.id_projeto = {$_REQUEST['projeto']}";
            }
            $projetoR = $_REQUEST['projeto'];
        } else {
            $filtroProjeto = '';
            $filtroProjetoJoin = '';
        }
        ?>
                                <div style="float:right; margin-right:20px;">
                                <?php include('../../reportar_erro.php'); ?>      
                                </div>

                                <div style="clear:right;"></div>

                                <div id="topo" style="width:95%; margin:0px auto;">
                                    <div style="float:left; width:25%;">
                                        <a href="../../principalrh.php?regiao=<?= $regiao ?>">
                                            <img src="../../imagens/voltar.gif">
                                        </a>
                                    </div>
                                    <div style="float:left; width:50%; text-align:center; font-family:Arial; font-size:24px; font-weight:bold; color:#000;">
                                        RESCIS&Atilde;O
                                    </div>
                                    <div style="float:right; width:25%; text-align:right; font-family:Arial; font-size:12px; color:#333;">
                                        <br><b>Data:</b> <?= date('d/m/Y') ?>&nbsp;
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
        <?php
        // Encriptografando a variável
        $link = str_replace('+', '--', encrypt("$regiao"));
        ?>

                                <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                                    <tr bgcolor="#999999">
                                        <td colspan="4" class="show" style="display:table-cell !important;">
                                            <span style="color:#F90; font-size:32px;">&#8250;</span> Relatório das rescisões
                                        </td>
                                        <td class="show"  style="display:table-cell !important;">
                                            <a href="../../relatorios/provisao_de_gastos.php?regiao=<?php echo $regiao; ?>" class="gerar_rel">Relatório de Rescisão em Lote</a>
                                            <a href="recisao_mes.php?regiao=<?php echo $regiao; ?>" class="gerar_rel2"> Relatório por Mês</a>
                                        </td>
                                    </tr>
                                </table>

                                <form action="" method="post" class="filtro">
                                    <fieldset>
                                        <legend>Filtro</legend>
                                        <input type="hidden" name="filtro" value="1" />
                                        <p>
                                            <label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?>
                                            <label class="first">Competencia:</label>
                                            <select name="txt_mes" id="txt_mes">
                                                <option value="0">---</option>
                                                <option value="01">Janeiro</option>
                                                <option value="02">Fevereiro</option>
                                                <option value="03">Março</option>
                                                <option value="04">Abril</option>
                                                <option value="05">Maio</option>
                                                <option value="06">Junho</option>
                                                <option value="07">Julho</option>
                                                <option value="08">Agosto</option>
                                                <option value="09">Setembro</option>
                                                <option value="10">Outubro</option>
                                                <option value="11">Novembro</option>
                                                <option value="12">Dezembro</option>
                                            </select>/
                                            <select name="txt_ano" id="txt_ano">
                                                <option value="0">---</option>
                                                <option value="2010">2010</option>
                                                <option value="2011">2011</option>
                                                <option value="2012">2012</option>
                                                <option value="2013">2013</option>
                                                <option value="2014">2014</option>
                                                <option value="2015">2015</option>
                                                <option value="2016">2016</option>
                                                <option value="2017">2017</option>
                                                <option value="2018">2018</option>
                                            </select>
                                        </p>
                                        <p><label class="first"></label><input type="text" name="pesquisa" placeholder="Nome, Matricula, CPF" value="<?php echo $_REQUEST['pesquisa']; ?>"></p>
                                        <p class="controls"><input type="submit" value="Consultar" class="button" name="consultar" /></p>
                                    </fieldset>
                                </form>



        <?php
        if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {

            if (!empty($_REQUEST['pesquisa'])) {
                $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
                foreach ($valorPesquisa as $valuePesquisa) {
                    $pesquisa[] .= "nome LIKE '%" . $valuePesquisa . "%'";
                }
                $pesquisa = implode(' AND ', $pesquisa);
                $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
            }

            // Consulta de Clts Aguardando Demissão
            $sql = "SELECT * FROM rh_clt WHERE status = '200' AND id_regiao = '$regiao' $filtroProjeto $auxPesquisa ORDER BY nome ASC";
            $qr_aguardo = mysql_query($sql);
            $total_aguardo = mysql_num_rows($qr_aguardo);

            if (!empty($total_aguardo)) {
                ?>

                                        <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                                            <tr bgcolor="#999999">
                                                <td colspan="6" class="show"  style="display:table-cell !important;">
                                                    <span style="color:#F90; font-size:32px;">&#8250;</span> Participantes aguardando a Rescis&atilde;o
                                                </td>
                                            </tr>
                                            <tr class="novo_tr">
                                                <td width="6%">COD</td>
                                                <td width="35%">NOME</td>
                                                <td width="20%">PROJETO</td>
                                                <td width="20%">UNIDADE</td>
                                                <td width="19%">CARGO</td>
                                                <td>AÇÃO</td>	
                                            </tr>

                <?php
                while ($row_aguardo = mysql_fetch_array($qr_aguardo)) {

                    $Curso->MostraCurso($row_aguardo['id_curso']);
                    $NomeCurso = $Curso->nome;

                    $ClasPro->MostraProjeto($row_aguardo['id_projeto']);
                    $NomeProjeto = $ClasPro->nome;

                    // Encriptografando a variável
                    $link = str_replace('+', '--', encrypt("$regiao&$row_aguardo[0]"));
                    ?>

                                                <tr style="background-color:<?php
                            if ($cor++ % 2 != 0) {
                                echo '#F0F0F0';
                            } else {
                                echo '#FDFDFD';
                            }
                    ?>">
                                                    <td><?= $row_aguardo['campo3'] ?></td>
                                                    <td>
                    <?php
                    if (trava_estabilidade($row_aguardo['id_clt'])) {
                        ?>
                                                            <a href="javascript:void(0)" title="Em estabilidade temporária"><?= $row_aguardo['nome'] ?></a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <a href="recisao2.php?tela=2&enc=<?= $link ?>"><?= $row_aguardo['nome'] ?></a>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $NomeProjeto ?></td>
                                                    <td><?= $row_aguardo['locacao'] ?></td>
                                                    <td><?= $NomeCurso ?></td>
                                                    <td>
                    <?php
                    if ($ACOES->verifica_permissoes(82)) {
                        ?>

                                                            <a href="recisao2.php?voltar_aguardando=true&id=<?php echo $row_aguardo[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_aguardo[0]; ?>" title="Desprocessar Aguardando Demissão" onclick="return window.confirm('Você tem certeza que quer desprocessar aguardando demissão?');"><img src="../imagensrh/deletar.gif" /></a>
                    <?php } ?>
                                                    </td>
                                                </tr>

                <?php } ?>

                                        </table>

            <?php } ?>
                                    <form action='' method="post">
                                        <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                                            <tr bgcolor="#999999">
                                                <td colspan="10" class="show" style="display:table-cell !important;">
                                                    <span class="seta" style="color:#F90; font-size:32px;">&#8250;</span> Participantes Desativados
                                                </td>
                                            </tr>
                                            <tr class="novo_tr">
                                                <td><input type="checkbox" id="txt_sel" onclick="sel_all();" /></td>
                                                <td width="6%">COD</td>
                                                <td width="32%">NOME</td>
                                                <td width="22%">PROJETO</td>
                                                <td width="20%" align="center">DATA</td>
                                                <td width="6%" align="center">RESCIS&Atilde;O</td>
                                                <td width="7%" align="center">COMPLEMENTAR</td>
                                                <td width="7%" align="center">ADD</td>
                                                <td>VALOR</td>
                                                <td>&nbsp;</td>
                                            </tr>

            <?php
            // Consulta de Clts que foram demitidos

            $competencia = trim($_REQUEST['txt_mes']) . trim($_REQUEST['txt_ano']);
            if ($_REQUEST['txt_ano'] !== '0' && $_REQUEST['txt_mes'] !== '0') {
                $data = "and date_format(r.data_demi, '%m%Y') = '{$competencia}'";
            } else {
                $data = "";
            }

            /*
              $sql_demissao = "SELECT
             * , date_format(data_saida, '%d/%m/%Y') AS data_saida2 
              FROM rh_clt
              WHERE status IN ('60','61','62','63','64','65','66','80','81','101') AND
              id_regiao = '$regiao' {$data} $filtroProjeto $auxPesquisa
              ORDER BY nome ASC";
             */
            $sql_demissao = "SELECT 
                                                    c.*, 
                                                    date_format(c.data_saida, '%d/%m/%Y') AS data_saida2
                                            FROM 
                                                    rh_clt c inner join rh_recisao r on c.id_clt = r.id_clt
                                            WHERE 
                                                    c.status IN ('60','61','62','63','64','65','66','80','81','101') AND 
                                                    r.`status` = 1 and
                                                    r.rescisao_complementar = 0 and
                                                    c.id_regiao = '{$regiao}' {$data} $filtroProjetoJoin $auxPesquisa
                                            ORDER BY c.nome ASC;";


            if ($_COOKIE['logado'] == 258) {
                echo "sql_demissao = [{$sql_demissao}]<br/>\n";
            }

            $qr_demissao = mysql_query($sql_demissao);

            while ($row_demissao = mysql_fetch_array($qr_demissao)) {

                $Curso->MostraCurso($row_demissao['id_curso']);
                $NomeCurso = $Curso->nome;

                $ClasPro->MostraProjeto($row_demissao['id_projeto']);
                $NomeProjeto = $ClasPro->nome;

                $qr_rescisao = mysql_query("SELECT *, date_format(data_demi, '%d/%m/%Y') AS data_demi2 FROM rh_recisao WHERE id_clt = '$row_demissao[0]' AND rescisao_complementar != 1   AND status = '1'");
                $row_rescisao = mysql_fetch_array($qr_rescisao);
                $total_rescisao = mysql_num_rows($qr_rescisao);

                $sql_rescisao_complementar = "SELECT * FROM rh_recisao  WHERE vinculo_id_rescisao = '$row_rescisao[0]' AND rescisao_complementar = 1  AND status = 1";
//                                echo $sql_rescisao_complementar;



                $qr_rescisao_complementar = mysql_query($sql_rescisao_complementar);
                $total_rescisao_complementar = mysql_num_rows($qr_rescisao_complementar);
                $arr_complementar = array();
                while ($row_rescisao_complementar = mysql_fetch_array($qr_rescisao_complementar)) {
                    $arr_complementar[] = $row_rescisao_complementar;
                }



                $link = str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao[0]"));

                if (substr($row_rescisao['data_proc'], 0, 10) >= '2013-04-04') {
                    $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                } else {
                    $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                }
                ?>

                                                <tr style="background-color:<?php
                                if ($cor++ % 2 != 0) {
                                    echo '#F0F0F0';
                                } else {
                                    echo '#FDFDFD';
                                }
                ?>">
                                                    <td>
                                                    <?php if ($row_demissao['conta'] == '' OR $row_demissao['conta'] == '000000' OR $row_demissao['tipo_conta'] == '') { ?>
                                                            SEM CONTA
                                                        <?php } else if ($row_rescisao['total_liquido'] == 0.00) { ?>
                                                            VALOR ZERADO
                                                        <?php } else if (!array_key_exists($row_rescisao['id_recisao'], $arrayArquivos)) { ?>
                                                            <input type='checkbox' name="arqRescisao[]" class="check" checked value="<?php echo $row_rescisao['id_recisao']; ?>" />
                                                        <?php } ?>
                                                    </td>
                                                    <td><?= $row_demissao['campo3'] ?></td>
                                                    <td><?= $row_demissao['nome'] ?></td>
                                                    <td><?= $NomeProjeto ?></td>
                                                    <td align="center"><?= $row_rescisao['data_demi2'] ?></td>
                                                    <td align="center">


                <?php if (empty($total_rescisao)) { ?>
                                                            <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)">
                                                        <?php } else { ?>
                                                                <a href="<?= $link_nova_rescisao; ?>" class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a>
                                                            <?php } ?>
                                                    </td>

                                                    <td align="center">
                <?php if (empty($total_rescisao_complementar)) { ?>
                                                            <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)" />

                    <?php
                } else {

                    foreach ($arr_complementar as $row_rescisao_complementar) {
                        $link_2 = str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao_complementar[0]"));
                        $link_resc_complementar = "nova_rescisao_2.php?enc=$link_2";
                        ?>

                                                                <a href="<?= $link_resc_complementar; ?>" class="link" target="_blank" title="Visualizar Rescisão Complementar"><img src="../../imagens/pdf.gif" border="0"></a>
                    <?php }
                }
                ?>
                                                    </td>
                                                    <td align="center">
                                                        <a href="form_rescisao_complementar.php?id_clt=<?= $row_demissao['id_clt']; ?>&id_rescisao=<?= $row_rescisao['id_recisao']; ?>" title="Adicionar Complementar"><img alt="Adionar Complementar" src="../../imagens/icones/icon-add.png" border="0" /></a>
                                                    </td>

                                                    <td>R$ <?php
                $total_recisao = $row_rescisao['total_liquido'];
                echo number_format($total_recisao, 2, ',', '.');
                $totalizador_recisao += $total_recisao;
                ?>
                                                    </td>
                                                    <td align="center">
                                                        <?php if ($ACOES->verifica_permissoes(82)) { ?> 
                                                                    <!--<a href="recisao2.php?deletar=true&id=<?php echo $row_rescisao[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_demissao[0]; ?>" title="Desprocessar Rescisão" onclick="return window.confirm('Você tem certeza que quer desprocessar esta rescisão?');"><img src="../imagensrh/deletar.gif" /></a>-->
                                                            <a href="javascript:;" title="Desprocessar Rescisão" data-recisao="<?php echo $row_rescisao[0]; ?>" data-regiao="<?php echo $_GET['regiao']; ?>" data-clt="<?php echo $row_demissao[0]; ?>" class="remove_recisao"><img src="../imagensrh/deletar.gif" /></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td align="right">TOTAL : </td>
                                                <td>R$<?php echo number_format($totalizador_recisao, 2, ',', '.'); ?></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="10">
                                                    Banco: <select name="banco"><?php echo $optionBanco; ?></select>
                                                    Data: <input type="text" name="data" >

                                                        <input type="submit" value="Gerar Arquivo de Banco">&nbsp;&nbsp;&nbsp;&nbsp;<a href="arquivo_banco_rescisao.php" target="_blank">Gerenciar Arquivos</a>
                                                            </td>
                                                            </tr>
                                                            </table>
                                                            </form>
                                                            <form name="acao_recisao" id="acao_recisao" action="recisao2.php">
                                                                <input type="hidden" name="id_recisao" id="id_recisao" value="" />     
                                                                <input type="hidden" name="id_regiao" id="id_regiao" value="" />     
                                                                <input type="hidden" name="id_clt" id="id_clt" value="" />  

                                                            </form>

                                                            <?php
                                                        }
                                                        break;
                                                    case 2:
                                                        
                                                        /* FITO POR RAMON, PARA PODER VISUALIZAR UMA PREVIA SEM PRECISAR COLOCAR STATUS 200 NO CLT */
                                                        if (empty($_REQUEST['enc'])) {
                                                            $regiao = $_REQUEST['regiao'];
                                                            $id_clt = $_REQUEST['id_clt'];
                                                        } else {
                                                            list($regiao, $id_clt) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
                                                        }

                                                        $dados_contratacao = $dadosClt->getDadosContratacao($id_clt);
                                                        verificaRecisao($id_clt);

                                                        $Clt->MostraClt($id_clt);

                                                        /* FITO POR RAMON, PARA PODER VISUALIZAR UMA PREVIA SEM PRECISAR COLOCAR STATUS 200 NO CLT */
                                                        if (empty($_REQUEST['enc'])) {
                                                            $Clt->data_demi = $_REQUEST['data_demi'];
                                                            $Clt->data_aviso = $_REQUEST['data_demi'];
                                                            if (isset($_REQUEST['data_aviso'])) {
                                                                $Clt->data_aviso = $_REQUEST['data_aviso'];
                                                            }
                                                        }

                                                        $nome = $Clt->nome;
                                                        $codigo = $Clt->campo3;
                                                        $data_demissao = $Clt->data_demi;
                                                        $contratacao = $Clt->tipo_contratacao;
                                                        $data_aviso_previo = $Clt->data_aviso;
                                                        $data_demissaoF = $Fun->ConverteData($data_demissao);

                                                        // Faltas no Mês
                                                        list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_demissao);

                                                        //INSTACIANDO OBJETO MOVIMENTO CLASS
                                                        $mov->carregaMovimentos($ano_demissao);

                                                        $qr_faltas = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN('62','456') AND (status = '1' OR status = '5') AND mes_mov = '" . $mes_demissao . "' AND ano_mov = '" . $ano_demissao . "'");
                                                        $faltas = mysql_result($qr_faltas, 0);


                                                        if ($dia_demissao > 30) {
                                                            $dias_trabalhados = 30;
                                                        } else {
                                                            $dias_trabalhados = $dia_demissao;
                                                        }

                                                        // verifica se há férias agendadas 
                                                        $query = "SELECT id_ferias, 0 AS programado, DATE_FORMAT(data_ini, '%d/%m/%Y') as data_iniBR, DATE_FORMAT(data_fim, '%d/%m/%Y') as data_fimBR
                            FROM rh_ferias where data_ini > NOW() AND `status` = '1' AND id_clt = '{$id_clt}' /*ORDER BY id_ferias DESC*/
                            UNION
                            SELECT id_ferias_programadas AS id_ferias, 1 AS programado, DATE_FORMAT(inicio, '%d/%m/%Y') as data_iniBR, DATE_FORMAT(fim, '%d/%m/%Y') as data_fimBR
                            FROM rh_ferias_programadas where inicio > NOW() AND `status` = '1' AND id_clt = '{$id_clt}' ORDER BY id_ferias DESC";
                                                        $query = mysql_query($query);
                                                        $row_feriasAgendadas = mysql_fetch_assoc($query);
                                                        $numFeriasAgendadas = mysql_num_rows($query);


                                                        // Calculando Saldo FGTS
                                                        $qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
                                                        $fgts = number_format(mysql_result($qr_liquido, 0) * 0.08, 2, ',', '.');


                                                        //VERIFICA CONFIGURAÇÕES SELECIONADAS ANTES
                                                        $dadosPreRescisao = $rescisao->getDadosRescisaoCltConf($id_clt);
                                                        ?>

                                                        <form action="recisao2.php" name="form1" id="form1" method="post" onsubmit="return validaForm()">
                                                            <table cellpadding="4" cellspacing="0" style="width:80%; margin:0px auto; border:0; line-height:30px;">
                                                                <tr>
                                                                    <td colspan="2" class="show" align="center" style="display:table-cell !important;"><div class="cols-lg-12"><?= $id_clt . ' - ' . $nome ?></div></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="38%" class="secao">Tipo de Dispensa:</td>
                                                                    <td width="62%">
                                                                        <?php
                                                                        // a variável indica se o funcionário pode ou não ser rescindido, deacordo com a regra da licença maternidade
                                                                        $indResPosMaternidade = $eventos->rescisaoPosMaternidade($id_clt);
                                                                        if ($indResPosMaternidade['indicativo'] == 'N') {
                                                                            unset($optTiposDispensa);
                                                                            $opt = array();
                                                                            $opt['65'] = "65 - Pedido de Dispensa";
                                                                            $optTiposDispensa = $opt;
                                                                        }

                                                                        echo montaSelect($optTiposDispensa, $dadosPreRescisao['tipo'], "id='dispensa' name='dispensa'");
                                                                        ?>

                <!--                                    <select name="dispensa" id="dispensa">
                <option value="">Selecione...</option>
                                                                        <?php
                                                                        $qr_dispensa = mysql_query("SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY CAST(codigo AS SIGNED)");
                                                                        while ($row_dispensa = mysql_fetch_array($qr_dispensa)) {
                                                                            ?>
                            <option value="<?= $row_dispensa['codigo'] ?>"> <?= $row_dispensa['codigo'] ?> - <?= $row_dispensa['especifica'] ?></option>
                                                                        <?php } ?>
                </select>-->
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Fator:</td>
                                                                    <td>
                                                                        <?php echo montaSelect($optFator, $optSelFator[$dadosPreRescisao['fator']], "id='fator' name='fator'"); ?>
                                                                        <!--select id="fator" name="fator">
                                                                            <option value="empregado">empregado</option>
                                                                            <option value="empregador">empregador</option>
                                                                        </select-->
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Dias de Saldo do Sal&aacute;rio:</td>
                                                                    <td><input name="diastrab" type="text" id="diastrab" value="<?= abs($dias_trabalhados) ?>" size="1" maxlength="2"> dias (data para demissão: <?= $data_demissaoF ?>)</td>
                                                                </tr>
                                                                <!--tr>
                                                                    <td class="secao">Remunera&ccedil;&atilde;o para Fins Rescis&oacute;rios:</td>
                                                                    <td><input name="valor" type="text" id="valor" onkeydown="FormataValor(this, event, 17, 2)" value="0,00" size="6"/></td>
                                                                </tr-->
                                                                <!--tr>
                                                                    <td class="secao">Quantidade de Faltas:</td>
                                                                    <td><input name="faltas" type="text" id="faltas" value="<?= $faltas ?>" size="2"/></td>
                                                                </tr-->
                                                                <tr>
                                                                    <td class="secao" >Aviso pr&eacute;vio:</td>
                                                                    <td>
                                                                        <?php echo montaSelect($optAviso, $optSelAviso[$dadosPreRescisao['aviso']], "id='aviso' name='aviso'"); ?>
                                                                        <!--select id="aviso" name="aviso" disabled="disabled">
                                                                            <option value=""></option>
                                                                            <option value="indenizado">Indenizado</option>
                                                                            <option value="trabalhado">Trabalhado</option>
                                                                        </select-->

                                                                        <input name="previo" type="text" id="previo" size="1" maxlength="2" /> 
                                                                        dias de indeniza&ccedil;&atilde;o ou dias de trabalho
                                                                    </td>
                                                                </tr>
                                    <!--                            <tr>
                                                                    <td class="secao" >Tipo de aviso pr&eacute;vio:</td>
                                                                    <td><select id="tpAvisoPre" name="tpAvisoPre" disabled="disabled" class="validate[required]" >
                                                                            <option value="">Selecione...</option>
                                                                <?php
//                                        $qr_tpAvisoPre = mysql_query("SELECT id_avisoPre, descricaoAvisoPre FROM tipo_aviso_previo;");
//                                        while ($rowAvisoPre = mysql_fetch_assoc($qr_tpAvisoPre)) {
                                                                ?>
                                                                                <option value="<?= $rowAvisoPre['id_avisoPre'] ?>"><?= $rowAvisoPre['descricaoAvisoPre'] ?></option>
                                                                <?php // }
                                                                ?>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Observação:</td>
                                                                    <td><textarea id="obs" name="obs" cols="60" rows="5" disabled="disabled"></textarea></td>
                                                                </tr>-->
                                                                <tr>
                                                                    <td class="secao">Data do Aviso:</td>
                                                                    <td>
                                                                        <input type="text" id="data_aviso" name="data_aviso" size="8" value="<?= formato_brasileiro($data_aviso_previo); ?>" onkeyup="mascara_data(this);
                                                                                        pula(10, this.id, devolucao.id)" />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Desconsiderar Média:</td>
                                                                    <td>
                                                                        <select name="desconsidera_media">
                                                                            <option value="0">Não</option>
                                                                            <option value="1">Sim</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Desconsiderar Base de IRRF da Folha:</td>
                                                                    <td>
                                                                        <select name="desconsiderar_ir_folha">
                                                                            <option value="0">Não</option>
                                                                            <option value="1">Sim</option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido:</td>
                                                                    <td><input name="devolucao" id="devolucao" size="6" onkeydown="FormataValor(this, event, 17, 2)" /></td>
                                                                </tr>
                                                                <?php
                                                                if ($_REQUEST['recisao_coletiva'] == 0 and $numFeriasAgendadas != 0) {
                                                                    ?> 
                                                                    <tr height='90' valign="top" align='center' class='linha_ferias'>
                                                                        <td colspan="2" style='color: #ce1a1a; font-weight: bold;'>Foi identificado que existe férias agendadas para este CLT no período de gozo de <?php echo $row_feriasAgendadas['data_iniBR']; ?>
                                                                            a <?php echo $row_feriasAgendadas['data_fimBR']; ?>. Este pode ter influência no cálculo da rescisão.
                                                                            <br/> Deseja desprocessar essas férias?
                                                                            <br>
                                                                                <?php if ($row_feriasAgendadas['programado']) { ?>
                                                                                    <input type='button' id='desprocessaFeriasProg' value='Desprocessar' data-key='<?php echo $row_feriasAgendadas['id_ferias'] ?>'/>
                                                                                <?php } else { ?>
                                                                                    <input type='button' id='desprocessaFerias' value='Desprocessar' data-key='<?php echo $row_feriasAgendadas['id_ferias'] ?>'/>
                                                                                <?php } ?>
                                                                        </td>
                                                                    </tr> 
                                                                    <?php
                                                                }
                                                                if (!empty($Clt->observacao)) {
                                                                    ?>
                                                                    <tr>
                                                                        <td class="cor-4" align="right">
                                                                            <h4 style="margin-left:15px; margin-right:15px;">Observações:</h4>
                                                                        </td>
                                                                        <td class="cor-4">
                                                                            <p style="margin-left:15px; margin-right:15px;"><?= $Clt->observacao ?></p>
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                                }
                                                                if (!empty($dados_contratacao)) {
                                                                    ?>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td><h3 style="text-align: left">HISTÓRICO DO FUNCIONÁRIO</h3></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td>
                                                                            <?php if ($dados_contratacao["contratacao"] != "TIPO NÃO CADASTRADO") { 
                                                                                
                                                                                if($id_clt == 3){
//                                                                                    $dados_contratacao["contratacao"] = "60 + 30";
//                                                                                    $dados_contratacao["data_primeiro"] = "29/08/2017";
//                                                                                    $dados_contratacao["data_segundo"] = "28/09/2017";
                                                                                }
                                                                                
                                                                                ?>
                                                                                <div class="box-periodico">
                                                                                    <p style="margin: 0px; padding: 0px;"><span style="font-weight: bold">Data Entrada:</span> <?php echo $dados_contratacao["data_entrada"]; ?></p>
                                                                                    <p style="margin: 0px; padding: 0px;"><span style="font-weight: bold">Tipo de Contrato:</span> <?php echo $dados_contratacao["contratacao"]; ?></p>
                                                                                    <p style="margin: 0px; padding: 0px;">O primeiro período de experiência termina em <span style="font-weight: bold"> <?php echo $dados_contratacao["data_primeiro"]; ?></span>, podendo se prorrogar até <span style="font-weight: bold"><?php echo $dados_contratacao["data_segundo"]; ?></span></p>
                                                                                </div>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php }
                                                                ?>                            
                                                                <tr>
                                                                    <td colspan="2" align="center">
                                                                        <table width="50%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td><input type="submit" value="Avançar"  class="botao" /></td>
                                                                                <td><input type="button" value="Cancelar" class="botao" onclick="javascript:location.href = 'recisao.php?tela=1&regiao=<?= $regiao ?>'"/></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <input type="hidden" name="tela" id="tela" value="3" />
                                                            <input type="hidden" name="idclt" id="idclt" value="<?= $id_clt ?>" />
                                                            <input type="hidden" name="regiao" id="regiao" value="<?= $regiao ?>" />
                                                        </form>

                                                        <script language="javascript">
                                                            function validaForm() {
                                                                d = document.form1;
                                                                if (d.valor.value == "") {
                                                                    alert("O campo Valor deve ser preenchido!");
                                                                    d.valor.focus();
                                                                    return false;
                                                                }
                                                                return true;
                                                            }
                                                        </script>

                                                        <?php
                                                        break;
                                                    case 3:
                                                        
                                                        // tela de contabilização da rescisão
                                                        //ESCONDENDO NOSSOS DEBUGS
                                                        
//                                                        if (in_array($_COOKIE['logado'], $programadores)) {
//                                                            echo '<button data-toggle="collapse" data-target=".demo" class="btn btn-info btn-xs"><i class="fa fa-arrow-down"></i></button>';
//                                                        }
                                                        
//                                                        echo '<div class="collapse demo">';
                                                        
                                                        $id_clt = (int) $_REQUEST['idclt'];
                                                        $regiao = $_REQUEST['regiao'];
                                                        $fator = $_REQUEST['fator'];
                                                        $dispensa = $_REQUEST['dispensa'];
                                                        $faltas = $_REQUEST['faltas'];
                                                        $dias_trabalhados = $_REQUEST['diastrab'];
                                                        $aviso = $_REQUEST['aviso'];
                                                        $previo = $_REQUEST['previo'];
                                                        $valor = $_REQUEST['valor'];
                                                        $data_aviso = implode('-', array_reverse(explode('/', $_REQUEST['data_aviso'])));
                                                        $devolucao = str_replace(',', '.', str_replace('.', '', $_REQUEST['devolucao']));


                                                        /**
                                                         * MOVIMENTOS DE ADIANTAMENTO
                                                         * DE 13º SALARIO NAS FÉRIAS
                                                         * 21/11/2016 
                                                         */
                                                        $verificaAdiantamentoEmFerias = "SELECT *
                                                    FROM rh_movimentos_clt AS A
                                                    WHERE A.cod_movimento IN(80030,5027) AND A.id_regiao = '{$regiao}' AND 
                                                                    A.id_clt = '{$id_clt}' AND A.mes_mov = 17  AND A.ano_mov = 2017  AND A.status = 1";
                                                        $sqlVerificaAdiantamentoEmFerias = mysql_query($verificaAdiantamentoEmFerias);
                                                        $valorAdiantamento = 0;
                                                        while ($rowsAdiantamento = mysql_fetch_assoc($sqlVerificaAdiantamentoEmFerias)) {
                                                            $valorAdiantamento += $rowsAdiantamento['valor_movimento'];
                                                        }



                                                        /*                                                         * ************************************************************************************************************** */

                                                        if ($_REQUEST['recisao_coletiva'] != 1) {
                                                            verificaRecisao($id_clt);
                                                        }


                                                        //////DADOS DO CLT
                                                        $ano_atual = date('Y');
                                                        if ($_REQUEST['recisao_coletiva'] == 1) {
                                                            $data_demi = "'" . $_REQUEST['data_demi'] . "'";
                                                        } else {
                                                            $data_demi = "data_demi";
                                                        }


                                                        $queryPrazoExperiencia = "SELECT prazoexp FROM rh_clt AS A WHERE A.id_clt = '{$id_clt}'";

                                                        $sqlPrazoExperiencia = mysql_query($queryPrazoExperiencia) or die("Erro ao selecionar periodo de experiência");
                                                        $dados = mysql_fetch_assoc($sqlPrazoExperiencia);
                                                        $periodoExp = $dados['prazoexp'];

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>queryPrazoExperiencia = [{$queryPrazoExperiencia}]<br/>\n";
                                                            echo "periodoExp = [{$periodoExp}]<br/>\n</pre>";
                                                        }

                                                        $dadosPrimeiroPeriodo = 0;
                                                        $dadosSegundoPeriodo = 0;

                                                        switch ($periodoExp) {
                                                            case 1:
                                                                $dadosPrimeiroPeriodo = 29;
                                                                $dadosSegundoPeriodo = 89;
                                                                break;

                                                            case 2:
                                                                $dadosPrimeiroPeriodo = 44;
                                                                $dadosSegundoPeriodo = 89;
                                                                break;
                                                            case 3:
                                                                $dadosPrimeiroPeriodo = 59;
                                                                $dadosSegundoPeriodo = 89;
                                                                break;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo '<br>******************************************************************************************<br>';

                                                            echo "SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,A.rh_sindicato,A.data_nasci,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
                        DATE_FORMAT({$data_demi}, '%d/%m/%Y') as data_demiF, 
                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
                        A.salario_outra_empresa, A.desconto_outra_empresa,                     
                        IF(DATEDIFF({$data_demi}, data_entrada) >= 365, 1, 0) as um_ano, 
                        B.salario, B.nome as nome_funcao,
                        /*Verifica se o clt recebeu DT*/
                        (SELECT Count(a.id_clt) FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = $id_clt AND a.ano = YEAR(A.data_demi) AND a.status = '3' AND b.terceiro = 1) as verifica_dt,
                        ROUND( DATEDIFF({$data_demi}, data_entrada) / 30) as meses_dt,
                        

                        /*CALCULO PARA O ART. 479 E ART. 480 */
                            IF( ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + {$dadosPrimeiroPeriodo} DAY)) OR ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + {$dadosSegundoPeriodo} DAY)),0,
                            IF({$data_demi} <= DATE_ADD(A.data_entrada, INTERVAL + {$dadosPrimeiroPeriodo} DAY), DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + {$dadosPrimeiroPeriodo} DAY),{$data_demi}),
                            DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + {$dadosSegundoPeriodo} DAY),{$data_demi}))
                        ) 
                        AS dias_restantes,
                        
                        /*MESES TRABALHADOS*/
                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
                              FROM 
                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
                                CONCAT(YEAR({$data_demi}), SUBSTR({$data_demi},6,2) ) as demissao,
                                data_entrada, data_demi
                                FROM rh_clt WHERE id_clt = $id_clt) as folha
                        ) as meses_trabalhados,
                        B.periculosidade_30

                        FROM rh_clt as A 
                        INNER JOIN curso as B
                        ON B.id_curso = A.id_curso
                        WHERE id_clt = '$id_clt' ";

                                                            echo '<br>******************************************************************************************<br>';
                                                        }

                                                        $sql = "SELECT A.nome, A.sexo, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso, A.rh_horario,A.rh_sindicato,A.data_nasci,
                        A.quantidade_plantao, A.valor_fixo_plantao, A.valor_hora, A.quantidade_horas,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
                        DATE_FORMAT({$data_demi}, '%d/%m/%Y') as data_demiF, 
                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
                        A.salario_outra_empresa, A.desconto_outra_empresa,                     
                        IF(DATEDIFF({$data_demi}, data_entrada) >= 365, 1, 0) as um_ano, 
                        B.salario, B.nome as nome_funcao,
                        /*Verifica se o clt recebeu DT*/
                        (SELECT Count(a.id_clt) FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = {$id_clt} AND a.ano = YEAR(A.data_demi) AND a.status = '3' AND b.terceiro = 1) as verifica_dt,
                        ROUND( DATEDIFF({$data_demi}, data_entrada) / 30) as meses_dt,
                        

                        /*CALCULO PARA O ART. 479 E ART. 480 */
                            IF( ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + {$dadosPrimeiroPeriodo} DAY)) OR ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + {$dadosSegundoPeriodo} DAY)),0,
                            IF({$data_demi} <= DATE_ADD(A.data_entrada, INTERVAL + {$dadosPrimeiroPeriodo} DAY), DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + {$dadosPrimeiroPeriodo} DAY),{$data_demi}),
                            DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + {$dadosSegundoPeriodo} DAY),{$data_demi}))
                        ) 
                        AS dias_restantes,
                        
                        /*MESES TRABALHADOS*/
                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
                              FROM 
                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
                                CONCAT(YEAR({$data_demi}), SUBSTR({$data_demi},6,2) ) as demissao,
                                data_entrada, data_demi
                                FROM rh_clt WHERE id_clt = {$id_clt}) as folha
                        ) as meses_trabalhados,
                        B.periculosidade_30 

                        FROM rh_clt as A 
                        INNER JOIN curso as B
                        ON B.id_curso = A.id_curso
                        WHERE id_clt = {$id_clt} ";
                                                        if ($_COOKIE['logado'] == 179) {
                                                            echo $sql;
                                                        }

                                                        $qr_clt = mysql_query($sql) or die(mysql_error());


                                                        if ($_REQUEST['recisao_coletiva'] == 1) {
//                        echo "SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,
//                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
//                        DATE_FORMAT({$data_demi}, '%d/%m/%Y') as data_demiF, 
//                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
//                        A.salario_outra_empresa, A.desconto_outra_empresa,                     
//                        IF(DATEDIFF({$data_demi}, data_entrada) >= 365, 1, 0) as um_ano, 
//                        B.salario, B.nome as nome_funcao,
//                        /*Verifica se o clt recebeu DT*/
//                        (SELECT Count(a.id_clt) FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = $id_clt AND a.ano = YEAR(A.data_demi) AND a.status = '3' AND b.terceiro = 1) as verifica_dt,
//                        ROUND( DATEDIFF({$data_demi}, data_entrada) / 30) as meses_dt,
//                        
//
//                        /*CALCULO PARA O ART. 479 E ART. 480 */
//                        IF( ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + 44 DAY)) OR ({$data_demi} = DATE_ADD(A.data_entrada, INTERVAL + 89 DAY)),0,
//                                IF({$data_demi} <= DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),
//                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),{$data_demi}),
//                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 89 DAY),{$data_demi}))
//                        ) 
//                        AS dias_restantes,
//                        
//                  /*MESES TRABALHADOS*/
//                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
//                              FROM 
//                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
//                                CONCAT(YEAR({$data_demi}), SUBSTR({$data_demi},6,2) ) as demissao,
//                                data_entrada, data_demi
//                                FROM rh_clt WHERE id_clt = $id_clt) as folha
//                        ) as meses_trabalhados,
//                        B.periculosidade_30
//
//                        FROM rh_clt as A 
//                        INNER JOIN curso as B
//                        ON B.id_curso = A.id_curso
//                        WHERE id_clt = '$id_clt' ";
                                                        }


                                                        $row_clt = mysql_fetch_assoc($qr_clt);
                                                        $Curso->MostraCurso($row_clt['id_curso']);
                                                        $nome = $row_clt['nome'];
                                                        $codigo = $row_clt['campo3'];
                                                        $data_demissao = ($_REQUEST['recisao_coletiva'] != 1) ? $row_clt['data_demi'] : $_REQUEST['data_demi'];
                                                        $data_entrada = $row_clt['data_entrada'];
                                                        $idprojeto = $row_clt['id_projeto'];
                                                        $idcurso = $row_clt['id_curso'];
                                                        $idregiao = $row_clt['id_regiao'];
                                                        $data_demissaoF = ($_REQUEST['recisao_coletiva'] == 1) ? date("d/m/Y", str_replace("-", "/", strtotime($_REQUEST['data_demi']))) : $row_clt['data_demiF'];
                                                        $data_entradaF = $row_clt['data_entradaF'];
                                                        $clt_insalubridade = $row_clt['insalubridade'];
                                                        $um_ano = ($dispensa == 63 or $dispensa == 64 or $dispensa == 66) ? 2 : $row_clt['um_ano'];
                                                        /**
                                                         * By Ramon 11-08-16
                                                         * Variavel $um_ano parece ser uma GAMBI bem das ferradas que muda todo o calculo para esses tipo de dispensa
                                                         * O problema q essa vairiavel acaba gravando 2 para os tipos acima, mesmo se a pessoa tenha menos d 1 ano
                                                         * e da problema na impressão do TRCT, pois la verifica a qnt de anos para dizer se é QUITAÇÃO OU HOMOLOGAÇÃO
                                                         */
                                                        $um_ano_real = $row_clt['um_ano'];

                                                        $dias_restantes = $row_clt['dias_restantes']; //USADO NO CALCULO DO ART. 479 e 480
                                                        //
              
//                                                        $dias_restantes = $dias_restantes + 1;
                                                        
                    ///////////////////////////////
                                                        ////////CONFIG////////////////
                                                        //////////////////////////////
                                                        $restatus = mysql_query("SELECT A.especifica, A.codigo_saque, B.* FROM rhstatus as A
                            INNER JOIN rescisao_config as B 
                            ON A.codigo = B.tipo
                            WHERE A.codigo = '$dispensa' AND ano = '$um_ano'");

                                                        if ($_COOKIE['logado'] == 260) {
                                                            echo "QUERY CONFIG:::: SELECT A.especifica, A.codigo_saque, B.* FROM rhstatus as A
                            INNER JOIN rescisao_config as B 
                            ON A.codigo = B.tipo
                            WHERE A.codigo = '$dispensa' AND ano = '$um_ano'";
                                                        }

                                                        $row_status = mysql_fetch_assoc($restatus);
                                                        
                                                        if ($_COOKIE['logado'] == 260) {
                                                            echo "<br><br>MOTIVO<br><br>";
                                                            print_array($row_status);
                                                        }

                                                        $t_ss = $row_status['saldodesalario']; // SALDO SALARIO
                                                        $t_ap = $row_status['avisoprevio']; // AVISO PREVIO
                                                        $t_fv = $row_status['feriasvencidas']; //NÃO TA ENTRANDO AQUI GORDO
                                                        // FERIAS VENCIDAS
                                                        $t_fp = $row_status['feriasproporcionais']; // FERIAS PROPORCIONAIS
                                                        $t_fa = $row_status['adicionaldeferias']; // FERIAS 1/3 ADICIONAL
                                                        $t_13 = $row_status['13salario']; // DECIMO TERCEIRO
                                                        $t_familia = $row_status['salariofamilia'];
                                                        $t_f8 = 0; // FGTS 8
                                                        $t_f4 = 0; // FGTS MULTA 40
                                                        $t_479 = $row_status['indenizacao479']; //INDENIZACAO ART. 479
                                                        $t_480 = $row_status['indenizacao480']; //INDENIZACAO ART. 480

                                                        $cod_saque_fgts = $row_status['codigo_saque'];

                                                        /**
                                                         * METODO QUE RETORNA 
                                                         * TIPOS DE MOVIMENTAÇÕES
                                                         */
                                                        $dadosMovimentacao = $rescisao->getCodigosMovimentacao($dispensa, $um_ano, $id_clt);
                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>Codigo Movimentação: <br>";
                                                            print_r($dadosMovimentacao);
                                                            echo "</pre>";
                                                        }

//                    switch ($dispensa) {
//                        case 60: $cod_mov_fgts = 'H';
//                            $cod_saque_fgts = '00';
//                            break;
//
//                        case 61: $cod_mov_fgts = '11';
//                            $cod_saque_fgts = '01';
//                            break;
//
//                        case 62:
//                        case 81: $cod_mov_fgts = '11';
//                            break;
//
//                        case 63: $cod_mov_fgts = '01';
//                            break;
//
//                        case 64:
//                            $cod_mov_fgts = '01';
//                            $cod_saque_fgts = '04';
//                            break;
//
//                        case 65:
//                            $cod_mov_fgts = '01';
//                            break;
//
//                        case 66: $cod_mov_fgts = '01';
//                            break;
//
//                        case 101: $cod_mov_fgts = '01';
//                            break;
//                    }
                                                        // Trabalhando com as Datas
                                                        $qnt_dias_mes = 30;
                                                        $data_hoje = date('Y-m-d');
                                                        $data_exp = explode('-', $data_demissao);
                                                        $data_adm = explode('-', $data_entrada);

                                                        $dia_demissao = (int) $data_exp[2];
                                                        $mes_demissao = (int) $data_exp[1];
                                                        $ano_demissao = (int) $data_exp[0];

                                                        $dia_admissao = (int) $data_adm[2];
                                                        $mes_admissao = (int) $data_adm[1];
                                                        $ano_admissao = (int) $data_adm[0];

                                                        $data_demissao_seg = mktime(0, 0, 0, $mes_demissao, $dia_demissao, $ano_demissao);
                                                        $data_admissao_seg = mktime(0, 0, 0, $mes_admissao, $dia_admissao, $ano_admissao);


                                                        ///////INSTANCIANDO O OBJETO  DE MOVIMENTOS
                                                        $mov->carregaMovimentos($ano_demissao);
                                                        
                                                        /**
                                                        * 07/08/2017
                                                        * VERIFICANDO EVENTOS 
                                                        * NO PERIODO DO 13°
                                                        */
                                                        $dataInicioPeriodoDt = "{$ano_demissao}-01-01";
                                                        $dataFinalPeriodoDt = $data_demissao;
                                                        $mesesTotalEmEventoDt = $eventos->verificaPeriodoDeEvento($dataInicioPeriodoDt, $dataFinalPeriodoDt, $id_clt);

                                                        if($mesesTotalEmEventoDt >= 180){ 
                                                            $meses_ativo_dt = 0;
                                                        }

                                                        //ULTIMO EVENTO DO PARTICIPANTE
                                                        $ultimo_event = $eventos->lastEventoFolha($_REQUEST['idclt']);
                                                        $count_evento = 0;
//                                                        $m_eventos = 0;
                                                        
                                                        /*
                                                        * 17/07/2017
                                                        * CALCULA MESES EM EVENTO
                                                        */
                                                        $m_eventos = $eventos->mesesEmEvento($ultimo_event);

                                                        /**
                                                         * 21/11/2016
                                                         */
//                    if($_COOKIE['logado'] == 179){
//                        
//                        foreach($ultimo_event['dados'] as $key => $dadosEventos){
//                            $data_inicial_evento = $dadosEventos['data'];
//                            $data_final_evento   = $dadosEventos['data_retorno'];
//                            
//                            $begin = new DateTime($data_inicial_evento);
//                            $end = new DateTime($data_final_evento);
//                            $end = $end->modify( '+1 day' ); 
//
//                            $interval = new DateInterval('P1D');
//                            $daterange = new DatePeriod($begin, $interval ,$end);
//                            
//                            foreach($daterange as $date_evento){
//                                $count_evento++;
//                                
//                                if($mes_atual != $date_evento->format("m")){
//                                    $mes_atual = $date_evento->format("m");
//                                    $count_evento = 0;
//                                }
//                                
//                                if($count == 14){
//                                    $m_eventos++;
//                                }
//                            }
//                            
//                        }
//                        
//                        
//                        echo "Meses em Evento:" . $m_eventos;
//                    }
//                    if(isset($ultimo_event) && $ultimo_event != ""){
//                        //DATAS DEMISSAO
//                        $mes_demissao = date("m",strtotime(str_replace("/", "-", $data_demissao)));
//                        $ano_demissao = date("Y",strtotime(str_replace("/", "-", $data_demissao)));
//                        //DATAS EVENTO
//                        $mes_final_evento = date("m",strtotime(str_replace("/", "-", $ultimo_event['dados']['data_retorno'])));
//                        $ano_final_evento = date("Y",strtotime(str_replace("/", "-", $ultimo_event['dados']['data_retorno'])));
//                        if($mes_demissao == $mes_final_evento && $ano_demissao == $ano_final_evento){
//                            $dias_em_evento =  (int) date("d",strtotime(str_replace("/", "-", $ultimo_event['dados']['data_retorno']))) - 1;
//                        }
//                    }                                        

                                                        /* VERIFICAR FALTAS LANÇADAS PARA RESCISÃO, PARA DESCONTAR DOS DIAS TRABALHADOS NO MES */
                                                        $sql = "SELECT id_clt,id_mov,cod_movimento,nome_movimento,valor_movimento,qnt,MONTH(data_movimento) AS mes_movimento FROM rh_movimentos_clt 
                                WHERE id_clt = {$id_clt} and mes_mov = 16 AND status = 1 AND cod_movimento IN (50249,50252,90073) AND ano_mov = {$ano_demissao};";

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            print_array('query faltas: ' . $sql);
                                                        }

                                                        $result = mysql_query($sql);
                                                        while ($row_faltas = mysql_fetch_assoc($result)) {
                                                            $faltas += $row_faltas['qnt'];
                                                            $faltas_lancadas_valor += $row_faltas['valor_movimento'];
                                                            $mes_mov_falta = $row_faltas['mes_movimento'];
                                                            //$faltas_nojo += $row_faltas['qnt'];
                                                        }
                                                        
                                                        if(in_array($_COOKIE['logado'], $programadores)){
                                                            echo "faltas: ".$faltas;
                                                        }
                                                        

                                                        /////////////////////////////////
                                                        //// DIAS TRABALHADOS  ///////////
                                                        /////////////////////////////////
                                                        if ($mes_admissao == $mes_demissao and $ano_demissao == $ano_admissao) {

                                                            $dias_trabalhados = (int) (($data_demissao_seg - $data_admissao_seg) / 86400) + 1;
                                                            $dias_trabalhados_nojo = $dias_trabalhados;                                 //PARA EXIBIR O VALOR
                                                            $dias_trabalhados = $dias_trabalhados - $faltas - $dias_em_evento;
                                                        } else {

                                                            if ((int) $mes_demissao == 2 and $dia_demissao >= 28) {

                                                                $dias_trabalhados = 30;
                                                            } else {

                                                                $dias_trabalhados = ($dia_demissao == 31) ? 30 : $dia_demissao;

                                                                if ($ultimo_event['dados']['cod_status'] == '50') {
                                                                    $dias_trabalhados = $dias_trabalhados - $faltas;
                                                                } else {
                                                                    $dias_trabalhados_nojo = $dias_trabalhados;                                 //PARA EXIBIR O VALOR
                                                                    $dias_trabalhados = $dias_trabalhados - $faltas - $dias_em_evento;
                                                                }
                                                            }
                                                        }

                                                        if (($row_clt['id_curso'] == 6894) || ($row_clt['id_curso'] == 6580)) {
                                                            $dias_trabalhados = 30;
                                                        }

//        if($_COOKIE['logado'] == 353){
                                                        if ($id_clt == 4779) {
                                                            $dias_trabalhados = 28;
                                                        }
//        }
                                                        ////////////////
                                                        /////////////////////
                                                        ////SALARIO BASE  ///
                                                        /////////////////////
                                                        if (($valor == '0,00') || ($valor == '')) {
                                                            $salario_base_limpo = $row_clt['salario'];
                                                        } else {
                                                            $valor = str_replace(',', '.', str_replace('.', '', $valor));
                                                            $salario_base_limpo = $valor;
                                                        }

                                                        if ($salario_base_limpo < 0) {
                                                            $salario_base_limpo = 0;
                                                        }

                                                        if ($id_clt == 29) {
                                                            $salario_base_limpo += 3750.00;
                                                        }


                                                        if ($id_clt == 1) {
                                                            $salario_base_limpo += 8351.50;
                                                        }

                                                        /*
                                                         * situações em que o Italo informou que os funcionários devem ser rescindidos com a incorporação do dissidio no salário
                                                         */
                                                        // 26 - PATRICIA CARVALHO SILVA
                                                        if ($id_clt == 26) {
                                                            $salario_base_limpo = 12492.48;
                                                        }
                                                        // 234 - PATRICIA RINALDI SILVESTRE
                                                        if ($id_clt == 234) {
                                                            $salario_base_limpo =  4759.04;
                                                        }
                                                        // 734 - LUANA MARÇON BOTTEON
                                                        if ($id_clt == 734) {
                                                            $salario_base_limpo = 9980.71;
                                                        }
                                                        // 2494 - CLAUDIO BARBOSA ALVES
                                                        if ($id_clt == 2494) {
                                                            $salario_base_limpo = 2525.45;
                                                        }
                                                        // 793 - ANA CLAUDIA DOS SANTOS
                                                        if ($id_clt == 793) {
                                                            $salario_base_limpo = 7602.05;
                                                        }
                                                        // 2067 - CRISTIANE MORALEZ MENDES
                                                        if ($id_clt == 2067) {
                                                            $salario_base_limpo = 2963.57;
                                                        }
                                                        // 2501 - DAIANE PIRES MARTINS
                                                        if ($id_clt == 2501) {
                                                            $salario_base_limpo = 9980.71;
                                                        }

                                                        /**
                                                         * CRIANDO SALARIO DE HOSRISTA 
                                                         * 
                                                         */
                                                        if ($_COOKIE['logado'] == 179) {
                                                            echo "<pre>";
                                                            echo "VASCO VASCO";
                                                            print_r($row_clt);
                                                            echo "</pre>";
                                                        }
                                                        if ($row_clt['id_curso'] == 6580) {
                                                            $qnt_horas_clt = ($row_clt['quantidade_horas'] > 0) ? $row_clt['quantidade_horas'] : 48;
                                                            $valor_hora_especial = $row_clt['valor_hora'];
                                                            $quantidade_horas_especialista = $qnt_horas_clt;
                                                            $salario_limpo = $valor_hora_especial * $quantidade_horas_especialista;
                                                            $salario_base_limpo = $salario_limpo;
                                                        }

                                                        // EXCLUIR, SÓ PARA TESTE
//                    if($id_clt == 4264){
//                        $row_clt['quantidade_plantao'] = 17;
//                    }

                                                        /**
                                                         * CRIANDO SALARIO DE PLANTONISTAS
                                                         */
                                                        if ($row_clt['id_curso'] == 6894) {
                                                            $qnt_plantao_clt = ($row_clt['quantidade_plantao'] > 0) ? $row_clt['quantidade_plantao'] : 5;
                                                            $valor_fixo_plantao = $row_clt['valor_fixo_plantao'];
                                                            $quantidade_plantao = $qnt_plantao_clt;
                                                            $salario_limpo = $valor_fixo_plantao * $quantidade_plantao;
                                                            $salario_base_limpo = $salario_limpo;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $salario_base_limpo = 19239.92;
                                                        }


                                                        $valor_faltas = ($salario_base_limpo / $qnt_dias_mes) * $faltas;

                                                        //carrega os movimentos para serem usados na classe
                                                        $objCalcFolha->CarregaTabelas($ano_demissao);



                                                        /**
                                                         * INSALUBRIDADE
                                                         */
                                                        $flagSindicato = $objCalcFolha->getAdNoturnoEmSindicato($id_clt);

                                                        /**
                                                         * ESSAS FUNÇÕES PAGAM A INSALUBRIDADE 40% SOBRE O SALÁRIO BASE. 
                                                         * DIFERENTE DAS OUTRAS QUE PAGAM SOBRE SALÁRIO MÍNIMO 
                                                         */
                                                        if ($Curso->nome == "SUPERVISOR DE APLICAÇÃO TECNICA RADIOLOGICA" ||
                                                                $Curso->nome == "SUPERVISOR DE APLICAÇÃO TÉCNICA RADIOLÓGICA" ||
                                                                $Curso->nome == "TÉCNICO DE RAIO-X") {
                                                            $curso_especiais[] = $Curso->id_curso;
                                                        }

                                                        $insalSobreSalBase = 0;
                                                        if (in_array($Curso->id_curso, $curso_especiais)) {
                                                            $insalSobreSalBase = 1;
                                                        }
                                                        
                                                        /*
                                                         * Leonardo 2017-04-18
                                                         * alterei a parte do código onde ficava esse calculo de data projetada pois a 
                                                         * funcao abaixo precisa disso mas esse calculo era lá pela linha 2600
                                                         */
                                                        
                                                        /*
                                                         * Leonardo 2017-03-06
                                                         * conforme convenção sindical:
                                                         * Para os trabalhadores com mais de 45 anos de idade e mais de um ano de casa, 
                                                         * será concedido aviso prévio de 45 dias, sem prejuizo do disposto no item acima, 
                                                         * limitando a soma total do período de aviso previo a 90 dias.
                                                         */
                                                        $dias_aviso_convencao = aviso_previo_convencao($row_clt['rh_sindicato'], $row_clt['data_nasci'], $row_clt['data_entrada']);

                                                        // comentado Leonardo 2017-03-06
//        $dias_proj = $dias_avisoA + $qnt_dias_mes;
                                                        $dias_proj = $dias_avisoA + $dias_aviso_convencao;
                                                        $data_proj = date('Y-m-d', strtotime($data_demissao . " + {$dias_proj} days"));

                                                        if(in_array($_COOKIE['logado'],$programadores)){
                                                            echo "dias_proj = $dias_proj";
                                                            echo "data_proj = $data_proj";
                                                        }
                                                        
                                                        /**
                                                         * CRIANDO REGRA DE DATA BASE
                                                         * SEMPRE QUE O FUNCIONÁRIO SEM JUSTA CAUSA COM 
                                                         * 30 DIAS QUE ANTECEDE O DISSIDIO, 
                                                         * O MESMO TEM DIREITO A 1 SALARIO MENSAL
                                                         */
                                                        $mesDissidio = str_pad($flagSindicato['mes_dissidio'], 2, '0', STR_PAD_LEFT);
                                                        $newDataMesDissidio = $ano_demissao . '-' . $mesDissidio . '-01';
                                                        $dataBaseParaCalculo = date('Y-m-d', strtotime('-30 days', strtotime($newDataMesDissidio)));

                                                        /**
                                                         * VERIFICANDO SE A DATA DE DEMISSÃO 
                                                         * ESTA ENTRE A DATA DE DISSIDIO  E
                                                         * 1 MES POSTERIOR
                                                         * 
                                                         * FATOR: EMPREGADOR
                                                         * DISPENSA DIFERENTE DE JUSTA CAUSA (60)
                                                         */
                                                        if ($_COOKIE['logado'] == 349) {
                                                            echo "<pre>";
                                                            echo "Data Demissão: " . $data_demissao . "<br>";
                                                            echo "Data projetada: " . $data_proj . "<br>";
                                                            echo "Data Base: " . $dataBaseParaCalculo . "<br>";
                                                            echo "Data Base: " . $newDataMesDissidio . "<br>";
                                                            echo "Fator: " . $fator . "<br>";
                                                            echo "Dispensa: " . $dispensa . "<br>";
                                                            echo "</pre>";
                                                        }
                                                        
                                                        /*
                                                         * Leonardo 2017-04-18
                                                         * alterei $data_demissao para $data_proj a pedido do Italo
                                                         */
                                                        if (($data_proj >= $dataBaseParaCalculo) && ($data_proj <= $newDataMesDissidio) && ($fator == 'empregador') && ($dispensa != 60)) {
                                                            $mov->setIdClt($id_clt);
                                                            $mov->setMes(16);
                                                            $mov->setAno($ano_demissao);
                                                            $mov->setIdRegiao($regiao);
                                                            $mov->setIdProjeto($idprojeto);
                                                            $mov->setIdMov(464);
                                                            $mov->setCodMov(90081);
                                                            $mov->setLancadoPelaFolha(1);
                                                            $verifica = $mov->verificaInsereAtualizaFolha($salario_base_limpo, '1,2');
                                                        } else {
                                                            $mov->removeMovimento($id_clt, 464);
                                                        }
                                                        
                                                        /*
                                                         * 2017-04-10 - Leonardo
                                                         * Italo disse que esse  clt nao possui esse movimento
                                                         */
                                                        if(in_array($id_clt,[4221,2520,110])){
                                                            $mov->removeMovimento($id_clt, 464);
                                                        }
                                                        

                                                        /**
                                                         * LANÇAMENTO DE CONTRIBUIÇOES AUTOMATICAS
                                                         * CONTRIBUIÇÃO SINDICAL - 5019
                                                         * FAZER ESSA PORRA PARA MES QUE VEZ (ABRIL)
                                                         * !empty($row_clt['rh_sindicato']) && $flagSindicato[''] && ($dias_evento != 30 || $eventoCodStatus == 50)
                                                         */
                                                        $dataExplode = explode("-", $row_clt['data_entrada']);
                                                        $anoEntrada = $dataExplode[2];
                                                        $mesEntrada = $dataExplode[1];
                                                        $diaEntrada = $dataExplode[0];
                                                        if ($mesEntrada == '03' && $row_clt['ano_contribuicao'] != '2016') {
                                                            $valor_dia = $salario_base_limpo / 30;
                                                            if ($valor_dia > 0) {

                                                                //AUXILIO CRECHES
                                                                $mov->setIdClt($id_clt);
                                                                $mov->setMes(16);
                                                                $mov->setAno($ano_demissao);
                                                                $mov->setIdRegiao($regiao);
                                                                $mov->setIdProjeto($idprojeto);
                                                                $mov->setIdMov(21);
                                                                $mov->setCodMov(5019);
                                                                $mov->setLancadoPelaFolha(0);
                                                                //$verifica = $mov->verificaInsereAtualizaFolha($valor_dia,'1,2');
                                                            } else {
                                                                $mov->removeMovimento($id_clt, 21);
                                                            }
                                                        } else {
                                                            //$mov->removeMovimento($id_clt,21);
                                                        }

                                                        /**
                                                         * CALCULO DE CONTRIBUIÇÃO 
                                                         * ASSISTENCIAL
                                                         * FEITO EM: 29/03/2016
                                                         */
                                                        if ($flagSindicato['contribuicaoAssistencial'] == 1) {
                                                            $baseCalculo = $salario_base_limpo * 0.01;
                                                            if ($baseCalculo > 0) {
                                                                //CONTRIBUICAO ASSISTENCIAL
                                                                $mov->setIdClt($id_clt);
                                                                $mov->setMes(16);
                                                                $mov->setAno($ano_demissao);
                                                                $mov->setIdRegiao($regiao);
                                                                $mov->setIdProjeto($idprojeto);
                                                                $mov->setIdMov(242);
                                                                $mov->setCodMov(50254);
                                                                $mov->setLancadoPelaFolha(1);
                                                                //if($_COOKIE['logado'] == 179){
                                                                $verifica = $mov->verificaInsereAtualizaFolha($baseCalculo, '1,2');
                                                                //}
                                                            } else {
                                                                $mov->removeMovimento($id_clt, 242);
                                                            }
                                                        }

                                                        /**
                                                         * ***********************************************************************************************
                                                         * ***********************************************************************************************
                                                         */
                                                        /**
                                                         * INSALUBRIDADE
                                                         * 29/02/2016
                                                         * PEDIDO DA MARIA DO RH DA LAGOS 
                                                         * ADICIONEI A OPÇÃO DE CALCULO DE INSALUBRIDADE  PARA PESSOAS COM 
                                                         * LICENÇA MATERNIDADE
                                                         * 
                                                         *  and $row_curso['tipo_insalubridade'] != 0 and 
                                                         *  && ($dias - $dias_faltas > 0) || (($dias_evento > 0 && $eventoCodStatus == 50))
                                                         */
                                                        if (($flagSindicato['insalubridade'] == 1) || $Curso->id_curso == 4063) {

                                                            $diasInsa = $dias_trabalhados;
                                                            $Trab->calculo_proporcional($salario_base_limpo, $diasInsa);
                                                            $salarioIns = $Trab->valor_proporcional;
                                                            $qnt_salInsalu = 1;
                                                            $tipo_insalubr = 1;

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "===================================INSALUBRIDADE PARTE 1=================================";
                                                                echo "<pre><BR>{$diasInsa} | {$tipo_insalubr} | {$qnt_salInsalu} | {$ano_demissao} | {$insalSobreSalBase} | {$salarioIns}<br></pre>";
                                                            }

                                                            $insalubridade = $objCalcFolha->getInsalubridade($diasInsa, $tipo_insalubr, $qnt_salInsalu, $ano_demissao, null, $insalSobreSalBase, $salarioIns);

                                                            $valorInsalubridade = $insalubridade['valor_proporcional'];
                                                            $mov->setIdClt($id_clt);
                                                            $mov->setMes(16);
                                                            $mov->setAno($ano_demissao);
                                                            $mov->setIdRegiao($regiao);
                                                            $mov->setIdProjeto($idprojeto);
                                                            $mov->setIdMov($insalubridade['id_mov']);
                                                            $mov->setTipoQuantidade(2);
                                                            $mov->setQuantidade($diasInsa);
                                                            $mov->setCodMov($insalubridade['cod_mov']);
                                                            $mov->setLancadoPelaFolha(1);

                                                            $verifica = $mov->verificaInsereAtualizaFolha($valorInsalubridade);

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "===================================INSALUBRIDADE=================================";
                                                                echo "<pre>";
                                                                print_r($insalubridade);
                                                                echo "</pre>";
                                                                echo "=================================================================================<br>";
                                                            }
                                                        } else {

                                                            $qr_verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                            WHERE id_clt = '$clt' 
                                                                            AND id_mov IN (235,200) AND lancamento = 1 AND ano_mov = '$ano'
                                                                            AND mes_mov = '$mes' AND status = 1 ");
                                                            $row_insalubridade = mysql_fetch_assoc($qr_verifica_insalubridade);
                                                            $verifica_insalubridade = mysql_num_rows($qr_verifica_insalubridade);
                                                            if ($verifica_insalubridade != 0) {
                                                                mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
                                                            }
                                                        }


                                                        /////////////////////////
                                                        /// PERICULOSIDADE /////
                                                        ////////////////////////
                                                        //
//            if($row_clt['periculosidade_30'] == 1){ 
//                $calPericulosidade                = $objCalcFolha->getPericulosidade($salario_base_limpo, $dias_trabalhados);
//                $periculosidade_30_integral       = $calPericulosidade['valor_integral'];
//                $periculosidade_30                = $calPericulosidade['valor_proporcional'];           
//                $periculosidade_30_integral_dt    = $salario_base_limpo * 0.30;
//
//                if($_COOKIE['logado'] == 158){
//                    echo "<pre>";
//                    echo "Periculidade Integral    : " . $periculosidade_30_integral . "<br / >";
//                    echo "Periculidade Proporcional: " . $periculosidade_30 . "<br / >";
//                    echo "</pre>";
//
//                }
//              
//               
//                $objMovimento->setIdRegiao($regiao);
//                $objMovimento->setIdProjeto($id_projeto);
//                $objMovimento->setIdClt($id_clt);
//                $objMovimento->setIdMov(57);
//                $objMovimento->setCodMov(6007);
//                $objMovimento->setMes(16);
//                $objMovimento->setAno(date("Y"));
//                $valor_mov = $periculosidade_30;
//
//                $verfica_movimento = $objMovimento->verificaMovimento();
//                
//                if(empty($verfica_movimento['id_movimento'])){
//                    $insere = $objMovimento->insereMovimento($valor_mov);   
//                }
//                else {
//
//                    if($verfica_movimento['valor_movimento'] != number_format($valor_mov,2,'.','')){
//                        $objMovimento->updateValorPorId($verfica_movimento['id_movimento'], $valor_mov);
//                    }
//
//                }
//            }

                                                        /**
                                                         * ESSA VARIÁVEL VAI SER
                                                         * PARAMETRO PARA VARIOS 
                                                         * CALCULOS JÁ QUE NO 
                                                         * IABAS SP TUDO É FLEGADO
                                                         * NO SINDICATO 
                                                         */
                                                        /**
                                                         * AUXILIO CRECHE
                                                         */
                                                        $countFilhos = 0;
                                                        $valorAuxCreche = 0;
                                                        $sqlVerDependente = null;
                                                        $filhos = null;


                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<BR>-----------------------------AUXILIIO CRECHE--------------------------------<BR>";
                                                            echo "<pre>";
                                                            print_r($flagSindicato);
                                                            print_r($row_clt);
                                                            echo "</pre>";
                                                            echo "<BR>-----------------------------AUXILIIO CRECHE--------------------------------<BR>";
                                                        }

                                                        if ($flagSindicato['creche'] == 1 && $row_clt['sexo'] == 'F') {

                                                            $valorFixoAuxCreche = $flagSindicato['creche_base'];
                                                            $porcentagemAuxCreche = $flagSindicato['creche_percentual'];
                                                            $idadeAuxCreche = $flagSindicato['creche_idade'];
                                                            $piso = $flagSindicato['piso'];

                                                            $queryVerDependente = "SELECT * FROM dependentes AS A WHERE
                                            A.id_bolsista = '{$id_clt}' AND A.id_projeto = '{$row_clt['id_projeto']}'";

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>";
                                                                echo $queryVerDependente;
                                                                echo "</pre>";
                                                            }

                                                            $sqlVerDependente = mysql_query($queryVerDependente) or die("Erro ao selecionar participantes");
                                                            if (mysql_num_rows($sqlVerDependente) > 0) {
                                                                $filhos = array();
                                                                while ($rowsDependentes = mysql_fetch_assoc($sqlVerDependente)) {
                                                                    $dataAtual = date("Y-m-d");
                                                                    /**
                                                                     * FILHO 1 
                                                                     */
                                                                    if (!empty($rowsDependentes['data1'])) {
                                                                        $dias1 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data1'])) / (60 * 60 * 24));
                                                                    } else {
                                                                        $dias1 = 0;
                                                                    }

                                                                    $filhos[1]["nome"] = $rowsDependentes['nome1'];
                                                                    $filhos[1]["nascimento"] = $rowsDependentes['data1'];
                                                                    $filhos[1]["dias"] = $dias1;
                                                                    $filhos[1]["idade"] = $dias1 / 365;

                                                                    /**
                                                                     * FILHO 2 
                                                                     */
                                                                    if (!empty($rowsDependentes['data2'])) {
                                                                        $dias2 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data2'])) / (60 * 60 * 24));
                                                                    } else {
                                                                        $dias2 = 0;
                                                                    }

                                                                    $filhos[2]["nome"] = $rowsDependentes['nome2'];
                                                                    $filhos[2]["nascimento"] = $rowsDependentes['data2'];
                                                                    $filhos[2]["dias"] = $dias2;
                                                                    $filhos[2]["idade"] = $dias2 / 365;

                                                                    /**
                                                                     * FILHO 3 
                                                                     */
                                                                    if (!empty($rowsDependentes['data3'])) {
                                                                        $dias3 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data3'])) / (60 * 60 * 24));
                                                                    } else {
                                                                        $dias3 = 0;
                                                                    }

                                                                    $filhos[3]["nome"] = $rowsDependentes['nome3'];
                                                                    $filhos[3]["nascimento"] = $rowsDependentes['data3'];
                                                                    $filhos[3]["dias"] = $dias3;
                                                                    $filhos[3]["idade"] = $dias3 / 365;

                                                                    /**
                                                                     * FILHO 4 
                                                                     */
                                                                    if (!empty($rowsDependentes['data4'])) {
                                                                        $dias4 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data4'])) / (60 * 60 * 24));
                                                                    } else {
                                                                        $dias4 = 0;
                                                                    }

                                                                    $filhos[4]["nome"] = $rowsDependentes['nome4'];
                                                                    $filhos[4]["nascimento"] = $rowsDependentes['data4'];
                                                                    $filhos[4]["dias"] = $dias4;
                                                                    $filhos[4]["idade"] = $dias4 / 365;

                                                                    /**
                                                                     * FILHO 5 
                                                                     */
                                                                    if (!empty($rowsDependentes['data5'])) {
                                                                        $dias5 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data5'])) / (60 * 60 * 24));
                                                                    } else {
                                                                        $dias5 = 0;
                                                                    }

                                                                    $filhos[5]["nome"] = $rowsDependentes['nome5'];
                                                                    $filhos[5]["nascimento"] = $rowsDependentes['data5'];
                                                                    $filhos[5]["dias"] = $dias5;
                                                                    $filhos[5]["idade"] = $dias5 / 365;

                                                                    /**
                                                                     * FILHO 6 
                                                                     */
                                                                    if (!empty($rowsDependentes['data6'])) {
                                                                        $dias6 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data6'])) / (60 * 60 * 24));
                                                                    } else {
                                                                        $dias6 = 0;
                                                                    }

                                                                    $filhos[6]["nome"] = $rowsDependentes['nome6'];
                                                                    $filhos[6]["nascimento"] = $rowsDependentes['data6'];
                                                                    $filhos[6]["dias"] = $dias6;
                                                                    $filhos[6]["idade"] = $dias6 / 365;
                                                                }
                                                            }

                                                            /**
                                                             * PELA MILESIMA VEZ ... 
                                                             * ESTOU ALTERANDO A REGRA DE 
                                                             * AUXILIO CRECHE 
                                                             * REMOVENDO O  + 1
                                                             * EM: 22/08/2016
                                                             * Por: SINESIO LUIZ
                                                             */
                                                            for ($i = 1; $i <= 6; $i++) {
                                                                if ($filhos[$i]["idade"] < ($idadeAuxCreche) && $filhos[$i]["idade"] != 0) {
                                                                    $countFilhos++;
                                                                }
                                                            }

                                                            if ($countFilhos > 0) {
                                                                if (!empty($valorFixoAuxCreche) && $valorFixoAuxCreche != '0.00' && $valorFixoAuxCreche > 0) {
                                                                    $valorAuxCreche = $valorFixoAuxCreche * $countFilhos;
                                                                } else {
                                                                    $valorAuxCreche = ($piso * $porcentagemAuxCreche) * $countFilhos;
                                                                }



                                                                $valorAuxCreche = ($valorAuxCreche / 30) * $dias_trabalhados;

                                                                if ($_COOKIE['logado'] == 179) {
                                                                    echo "Numero Filhos: " . $countFilhos . "<br>";
                                                                    echo "Valor Auxilio: " . $valorAuxCreche . "<br>";
                                                                }



                                                                if ($valorAuxCreche > 0) {

                                                                    //AUXILIO CRECHES    
                                                                    $mov->setIdClt($id_clt);
                                                                    $mov->setMes(16);
                                                                    $mov->setAno($ano_demissao);
                                                                    $mov->setIdRegiao($regiao);
                                                                    $mov->setIdProjeto($idprojeto);
                                                                    $mov->setIdMov(369);
                                                                    $mov->setCodMov(90016);
                                                                    $mov->setLancadoPelaFolha(1);
                                                                    $arrayNaoAuxilioCreche = array(4564);
                                                                    if (!in_array($id_clt, $arrayNaoAuxilioCreche)) {
                                                                        $verifica = $mov->verificaInsereAtualizaFolha($valorAuxCreche, '1,2');
                                                                    }
                                                                }
                                                            } else {
                                                                $mov->removeMovimento($id_clt, 369);
                                                            }
                                                        } else {
                                                            $mov->removeMovimento($id_clt, 369);
                                                        }


                                                        if ($periculosidade_30_integral == 0) {
                                                            $query_verifica_mov_periculosidade_30 = "SELECT * FROM rh_movimentos_clt AS A 
                                      WHERE A.id_clt = '{$id_clt}' AND A.`status`= 1 
                                      AND A.id_mov = 57 AND A.mes_mov = 16";
                                                            $sql_verifica_mov_periculosidade_30 = mysql_query($query_verifica_mov_periculosidade_30) or die("erro em verificar periculosidade");
                                                            $valor_mov_periculosidade_30 = mysql_fetch_assoc($sql_verifica_mov_periculosidade_30);
                                                            $periculosidade_30_integral = $valor_mov_periculosidade_30['valor_movimento'];
                                                        }


                                                        /////////////////////
                                                        // MOVIMENTOS FIXOS //
                                                        ///////////////////
                                                        //aki
                                                        /* $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                                                          FROM rh_folha as A
                                                          INNER JOIN rh_folha_proc as B
                                                          ON A. id_folha = B.id_folha
                                                          WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2
                                                          AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

                                                          while ($row_folha = mysql_fetch_assoc($qr_folha)) {
                                                          if (!empty($row_folha[ids_movimentos_estatisticas])) {
                                                          /**
                                                         * SINÉSIO LUIZ - 01/07/2015
                                                         * 
                                                         */
                                                        //$qr_movimentos = mysql_query("SELECT *
                                                        //           FROM rh_movimentos_clt
                                                        //           WHERE (id_movimento IN($row_folha[ids_movimentos_estatisticas]) OR (mes_mov = 16 AND status = 1)) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt /*AND id_mov NOT IN(56,200,235,57,279,370)*/");  ///A PEDIDO DA REJANE, COM BASE NO EMAIL ESTOU REMOVENDO O MOVIMENTO DE DIFERENÇA SALARIAL PARA O CALCULO DAS MÉDIAS 13/11/2014



                                                        /* while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                                          if(in_array($_COOKIE['logado'], $programadores)){
                                                          echo "<BR>-------------MOVIMENTOS MÉDIA--------------<br><pre> ";
                                                          print_r($row_mov);
                                                          echo "</pre>";
                                                          }
                                                          $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento'];
                                                          }
                                                          }
                                                          }



                                                          if($_COOKIE['logado'] == 179){
                                                          echo "<pre>";
                                                          print_r("Meses para Médiass: " . $row_clt['meses_trabalhados'] . "<br>");
                                                          echo "</pre>";
                                                          }

                                                          if (sizeof($movimentos) > 0) {
                                                          //                        $total_rendi = (array_sum($movimentos) / $row_clt['meses_trabalhados']);
                                                          $total_rendi = (array_sum($movimentos) / 12);
                                                          $total_rendi = number_format($total_rendi, 2,'.','');
                                                          } else {
                                                          $total_rendi = 0;
                                                          } */

                                                        //BUSCANDO A MÉDIA DA CLASSE
                                                        $rs_movi_media = $rescisao->getMovimentosFixoParaMedia($id_clt, 12, $data_demissao);
                                                        $total_rendi = $rs_movi_media['total_rendi'];

                                                        $total_movi_fixo_para_media_ferias = $rs_movi_media['total_movi'];

                                                        if ($id_clt == 1) {
                                                            $total_rendi = 0;
                                                        }

                                                        if ($id_clt == 2340) {
                                                            $total_rendi = 1203.51;
                                                        }

                                                        //VERIFICA SE NÃO QUEREM Q O SISTEMA CALCULE AUTOMATICAMENTE
                                                        $flag_media_dt = $_REQUEST['desconsidera_media'];
                                                        if ($flag_media_dt) {
                                                            $total_rendi = 0;
                                                            $total_movi_fixo_para_media_ferias = 0;
                                                        }
                                                        
                                                        // 4262 - VIVIANE SOBRAL ORSI
                                                        // Leonardo 2017-04-26
                                                        // as médias são de outro período aquisitivo
                                                        // depois veirificar como fazer para verificar o periodo aquisitivo das médias
                                                        if($id_clt == 4262){
                                                            $total_movi_fixo_para_media_ferias = 0;
                                                        }
                                                        

                                                        /////////////////////
                                                        // FIM MOVIMENTOS FIXOS /////



                                                        if ($array_valores_rendimentos == '') {
                                                            $array_valores_rendimentos[] = '0';
                                                        }

                                                       
                                                        //////////////////////////////
                                                        ////SALÁRIO FAMÍLIA ///////
                                                        ///////////////////////////
                                                        if ($t_familia == 1) {

                                                            $Calc->Salariofamilia($salario_base_limpo, $id_clt, $idprojeto, $data_demissao, '2');
                                                            $valor_sal_familia = (($Calc->valor) / $qnt_dias_mes) * $dias_trabalhados;
                                                            if ($valor_sal_familia > 0) {
                                                                $TOTAL_MENOR = $Calc->filhos_menores;
                                                            }
                                                            if($valor_sal_familia <0){
                                                                $valor_sal_familia = 0;
                                                            }
                                                        }
                                                        

                                                        ///ARTIGO 479 e 480 PARA RESCISÃO ANTECIPADA 

                                                        $valor_insalubridade_integral = $insalubridade['valor_integral'];


                                                        /**
                                                         * REMOVENDO INSALUBRIDADE 
                                                         * A PEDIDO DO ITALO 
                                                         * 09/01/2017
                                                         * 
                                                         * +$valor_insalubridade_integral
                                                         */
                                                        $valor_art_480_479 = (($salario_base_limpo) / 30) * ($dias_restantes / 2);

                                                        $total_dias = ($data_demissao_seg - $data_admissao_seg) / 86400;

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br><pre>";
                                                            echo "((salario_base_limpo [{$salario_base_limpo}]+[{$valor_insalubridade_integral}]) / 30) * (dias_restantes [{$dias_restantes}] / 2)<br/>\n";
                                                            echo "valor_art_480_479 = $valor_art_480_479<br/>\n";
                                                            echo "total_dias = (data_demissao_seg [{$data_demissao_seg}] - data_admissao_seg [{$data_admissao_seg}]) / 86400;<br/>\n";
                                                            echo "total_dias = $total_dias<br/>\n</pre>";
                                                        }


                                                        if ($t_479 == 1) {


                                                            $art_479 = $valor_art_480_479;
                                                            $art_480 = NULL;
                                                            $to_rendimentos += $art_479;
                                                        } elseif ($t_480 == 1) {


                                                            $art_479 = NULL;
                                                            $art_480 = $valor_art_480_479;
                                                            $to_descontos += $art_480;
                                                        }

                                                        if ($art_479 < 0) {
                                                            $art_479 = 0;
                                                        }

                                                        /**
                                                         * 
                                                         */
                                                        if ($id_clt == 4640) {
                                                            $art_480 = 0;
                                                        }

                                                        //////////////////////////////////////
                                                        //////////////SALDO DE SALÁRIO///////
                                                        /////////////////////////////////////

                                                        if ($t_ss == 1) {
                                                            $valor_salario_dia = $salario_base_limpo / $qnt_dias_mes;

                                                            /**
                                                             * CASO ROMEU
                                                             */
//                        if($id_clt == 2625){
//                            $dias_trabalhados = 15;                            
//                        }

                                                            $saldo_de_salario = $valor_salario_dia * $dias_trabalhados;
                                                        }

                                                        //if($dias_trabalhados == 0){
                                                        if ($faltas > 0) {
                                                            $saldo_de_salario_nojo = $valor_salario_dia * $dias_trabalhados_nojo;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            print_array("com faltas: $saldo_de_salario");
                                                        }


                                                        if ($saldo_de_salario < 0) {
                                                            $saldo_de_salario = 0;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            print_array("com faltas: $saldo_de_salario");
                                                        }

                                                        if ($saldo_de_salario_nojo < 0) {
                                                            $saldo_de_salario_nojo = 0;
                                                        }


                                                        if ($row_clt['id_curso'] == 6580) {
                                                            $saldo_de_salario = $salario_base_limpo;
                                                        }

                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 07/02/2017
                                                         * A PEDIDO DO ÍTALO
                                                         */
                                                        if ($id_clt == 4276) {
                                                            $saldo_de_salario = 1077.56;
                                                        }

                                                        if ($id_clt == 4352) {
                                                            $saldo_de_salario = 5292.00;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $saldo_de_salario = 6790.56;
                                                        }
                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $saldo_de_salario = 1131.76;
                                                        }
                                                        // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                        if ($id_clt == 4146) {
                                                            $saldo_de_salario = 5658.80;
                                                        }
                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $saldo_de_salario = 1131.76;
                                                        }
                                                        // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $saldo_de_salario = 1131.76;
                                                        }

                                                        /*
                                                         *  2017-03-14 - Leonardo
                                                         * Mais rescisoes de plantonistas
                                                         */
                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $saldo_de_salario = 3395.28;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $saldo_de_salario = 4527.04;
                                                        }

                                                        // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                        if ($id_clt == 4203) {
                                                            $saldo_de_salario = 3969.00;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $saldo_de_salario = 4527.04;
                                                        }

                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $saldo_de_salario = 4527.04;
                                                        }

                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $saldo_de_salario = 3395.28;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $saldo_de_salario = 2263.52;
                                                        }

                                                        // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                        if ($id_clt == 4160) {
                                                            $saldo_de_salario = 1131.76;
                                                        }

                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $saldo_de_salario = 4702.08 ;
                                                        }

                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $saldo_de_salario = 7938.00 ;
                                                        }




                                                        // Décimo Terceiro (DT)
                                                        ///Verifica se  a pesssoa recebeu décimo terceiro no ano
                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br><br>******SINESIO QUERY DECIMO******<br><br>";
                                                            echo "SELECT a.id_clt,a.salliquido,a.base_inss,a.a5031,a.ir_dt,b.data_fim,tipo_terceiro
                                FROM rh_folha_proc AS a 
                                LEFT JOIN rh_folha AS b ON(a.id_folha = b.id_folha) 
                                WHERE a.id_clt = '{$id_clt}' AND a.ano = " . date('Y') . " AND a.status = '3' AND b.terceiro = 1
                                AND a.id_clt IS NOT NULL<br><br>";
                                                        }
                                                        $qr_verifica_13_folha = mysql_query("SELECT a.id_clt,a.salliquido,a.base_inss,a.a5029,a.a5031,a.rend,a.ir_dt,b.data_fim,tipo_terceiro
                                                FROM rh_folha_proc AS a 
                                                LEFT JOIN rh_folha AS b ON(a.id_folha = b.id_folha) 
                                                WHERE a.id_clt = '{$id_clt}' AND a.ano = " . date('Y') . " AND a.status = '3' AND b.terceiro = 1
                                                AND a.id_clt IS NOT NULL") or die(mysql_error());


                                                        $verifica_13_folha = mysql_num_rows($qr_verifica_13_folha);
                                                        $array_parcelas_decimo = array();
                                                        $valor_decimo_folha = 0;
                                                        $valor_decimo_folhaInss = 0;
                                                        $valor_decimo_folhaIRRF = 0;
                                                        $flag_recalcula_desconto_inss_irrf_dt = 0;
                                                        while ($row_veri_decimo = mysql_fetch_assoc($qr_verifica_13_folha)) {
                                                            $array_parcelas_decimo[] = $row_veri_decimo['tipo_terceiro'];

                                                            /**
                                                             * By Ramon 19/12/2016
                                                             * comentando o IF. para somar todos os valores de 13 pagos para o funcionário..
                                                             */
                                                            //if ($row_veri_decimo['tipo_terceiro'] == 1) {
                                                            $valor_decimo_folha += $row_veri_decimo['a5029'];
                                                            $valor_decimo_folha += $row_veri_decimo['rend'];
                                                            $valor_decimo_folhaInss += $row_veri_decimo['a5031'];
                                                            $valor_decimo_folhaIRRF += $row_veri_decimo['ir_dt'];
                                                            //}

                                                            /**
                                                             * By Ramon 19/12/2016
                                                             * A pedido do Ítalo vamos colocar o valor de 13º para Transitar na RESCISÃO (entrando e saindo)
                                                             * Somente o valor, sem os INSS e IRRF
                                                             * 
                                                             * **** ATUALIZANDO ****
                                                             * 
                                                             * Vou comentar pois o Italo não quer q fique assim como MOVIMENTOS... ele diz q tem q ser recalculado
                                                             * Pois tem funcionario q saiu dia 13... e não deveria ter recebido 8 avos... conforme foi em folha
                                                             * e Sim 7 avos... a rescisão tem q fazer corretamete.
                                                             */
                                                            //VERIFICA FOLHA 2 PARCELA OU INTEGRAL
                                                            /* if ($row_veri_decimo['tipo_terceiro'] == 2 OR $row_veri_decimo['tipo_terceiro'] == 3) {
                                                              //CRÉDITO
                                                              $mov->setIdRegiao($regiao);
                                                              $mov->setIdProjeto($id_projeto);
                                                              $mov->setIdClt($id_clt);
                                                              $mov->setIdMov(468);
                                                              $mov->setCodMov(90082);
                                                              $mov->setMes(16);
                                                              $mov->setAno($ano_demissao);
                                                              $mov->setLancadoPelaFolha(1);

                                                              $valor_mov = $valor_decimo_folha + $row_veri_decimo['salliquido'];
                                                              $verifica = $mov->verificaInsereAtualizaFolha($valor_mov,'1,2');

                                                              //DÉBITO
                                                              $mov->setIdMov(469);
                                                              $mov->setCodMov(90083);
                                                              $mov->setLancadoPelaFolha(1);
                                                              $verifica = $mov->verificaInsereAtualizaFolha($valor_mov,'1,2');

                                                              $valor_decimo_folha = 0;
                                                              } */

                                                            //FLAG PARA NAO DESCONTAR IFFR E INSS DE 13º pois ja foi pago na folha
                                                            if ($row_veri_decimo['tipo_terceiro'] == 2 OR $row_veri_decimo['tipo_terceiro'] == 3) {
                                                                $flag_recalcula_desconto_inss_irrf_dt = 1;
                                                            }


                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                print_r($row_veri_decimo);
                                                                echo "<br><br>TIPO TERCEIRO VASCO: " . $valor_decimo_folha . "<br><br>";
                                                            }
                                                        }



                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br><br>====================================Participou do Decimo Terceiro======================================== <br><pre>";
                                                            echo "1 => Primeira Parcela, 2 => Segunda Parcela, 3 => Integral <br>";
                                                            print_r($array_parcelas_decimo);
                                                            echo "<br></pre>================================================================================================== <br>";
                                                        }

                                                        /**
                                                         * FEITO POR SINESIO
                                                         * ADICIONANDO A MÉDIA PARA BASE DE CALCULO DA 12506
                                                         */
                                                        if ($dispensa != 65) {

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<br>" . $dispensa . "<br>";
                                                            }

                                                            if ($dispensa == 63) {
                                                                $baseCalcAviso = $salario_base_limpo + $valor_insalubridade + $periculosidade_30_integral_dt + $total_rendi;
                                                            } else {

                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "<br>BASE INDENIZADO<br>";
                                                                    echo "Salario Base: " . $salario_base_limpo . "<br>";
                                                                    echo "Insalubridade: " . $valor_insalubridade_integral . "<br>";
                                                                    echo "Periculosidade: " . $periculosidade_30_integral_dt . "<br>";
                                                                    echo "Medias: " . $total_rendi . "<br>";
//                                exit();
                                                                }

                                                                $baseCalcAviso = $salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $total_rendi;
                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "<pre>calculo<br>";
                                                                    echo " $baseCalcAviso = $salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $total_rendi;";
                                                                    echo "</pre>";
                                                                }
                                                            }
                                                        } else {
                                                            /*
                                                             * Leonardo
                                                             * 2017-01-13 15:39h
                                                             * A pedido da Alana: Por gentileza verificar o desconto de avis prévio do 
                                                             * colaborador Richard Rigolino, está considerando a insalubridade como base 
                                                             * mas para caso de desconto do aviso considerar apenas o salário
                                                             */
                                                            // $baseCalcAviso =  $salario_base_limpo + $valor_insalubridade_integral +  $periculosidade_30_integral_dt   ; //   + $total_rendi    + $total_rendi
                                                            $baseCalcAviso = $salario_base_limpo; //+ $valor_insalubridade_integral +  $periculosidade_30_integral_dt   ; //   + $total_rendi    + $total_rendi                                
                                                        }

                                                        /**
                                                         * 
                                                         * BASE DE CALCULO 
                                                         * 13 AVISO PREVIO INDENIZADO
                                                         * 09/01/2017
                                                         */
                                                        // faltou verificar a flag de desconsiderar medias
                                                        if ($_REQUEST['desconsidera_media'] == 1) {
                                                            $mediaParaBase13Aviso = 0;
                                                        } else {

                                                            /**
                                                             * 23/01/2017
                                                             * Movimentos de Media de 13
                                                             * fiz isso para pegar apenas os movimentos que foram lançados dentro do ano 
                                                             * de corrente da data de dispensa.
                                                             */
                                                            $total_rendi_13_medias = $rescisao->getMovimentosFixoParaMedia13($id_clt, 12, $data_demissao);

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "media 13<br>";
                                                                print_array($total_rendi_13_medias);
                                                            }

                                                            $total_rendi_13 = $total_rendi_13_medias['total_rendi'];
                                                            $mediaParaBase13Aviso = $total_rendi_13_medias['total_rendi'];
                                                        }


                                                        $base13AvisoPrevio = ($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $mediaParaBase13Aviso) / 12;

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>
                BASE DE CALCULO 13 AVISO PREVIO INDENIZADO
                
                salario_base_limpo: $salario_base_limpo
                valor_insalubridade_integral: $valor_insalubridade_integral
                periculosidade_30_integral_dt: $periculosidade_30_integral_dt
                mediaParaBase13Aviso: $mediaParaBase13Aviso
                
                base13AvisoPrevio = (salario_base_limpo + valor_insalubridade_integral + periculosidade_30_integral_dt + mediaParaBase13Aviso) / 12;
                base13AvisoPrevio: $base13AvisoPrevio = ($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $mediaParaBase13Aviso) / 12;
                </pre>";
                                                        }


                                                        $avos13AvisoPrevioIndenizado = 1;

                                                        ///NOVA REGRA DO AVISO PRÉVIO 
                                                        //$diferenca_anos = ($data_demissao_seg - $data_admissao_seg) / 31536000;

                                                        if ($fator == "empregador" && $aviso == 'indenizado') {

                                                            // 2017-03-08 leonardo leva em consideracao os dias de aviso
                                                            $dias_aviso_convencao = aviso_previo_convencao($row_clt['rh_sindicato'], $row_clt['data_nasci'], $row_clt['data_entrada']);

                                                            $data_demissao_indenizado = date('Y-m-d', strtotime("+{$dias_aviso_convencao} days", strtotime($data_demissao)));
                                                        } else {
                                                            $data_demissao_indenizado = $data_demissao;
                                                        }

                                                        $data_demissao_obj = new DateTime($data_demissao_indenizado);
                                                        $data_entrada_obj = new DateTime($data_entrada);

                                                        $interval = $data_demissao_obj->diff($data_entrada_obj);

                                                        $diferenca_anos = (int) $interval->format('%Y');
                                                        
                                                        // 2067 - CRISTIANE MORALEZ MENDES
                                                        if($id_clt== 2067){
                                                            $diferenca_anos =12;
                                                        }

                                                        // 2017-03-06 italo e alana reclamaram que a lei está tirando 3 dias das pessoas vou ver se isso resolve. falar com a michele amanha
//        if($interval->format('%m') == 11 && $interval->format('%d') >= 15){
//            $diferenca_anos += 1;
//        }
                                                        if(in_array($_COOKIE['logado'], $programadores)){
                                                            echo "<pre> data_demissao_indenizado = $data_demissao_indenizado <br> data_entrada = $data_entrada</pre>";
                                                        }
                                                        

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>base: {$baseCalcAviso} | qnt dias: {$qnt_dias_mes} | dif anos: {$diferenca_anos} | data demi atualizada: {$data_demissao_}</pre>";
                                                        }

                                                        for ($d = 1; $d <= (int) $diferenca_anos; $d++) {
                                                            $valorLei += ($baseCalcAviso / $qnt_dias_mes) * 3;

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo $valorLei . '<br>';
                                                            }

                                                            $dias_avisoA += 3;
                                                        }
                                                        
                                                        
                                                        if($id_clt == 2520){
                                                            $valorLei= 6986.28;
                                                        }

                                                        /*
                                                         * 24/10/16
                                                         * by: Max
                                                         * CALCULO DE AVOS DE 13º e FERIAS
                                                         * SOBRE AVISO PRÉVIO INDENIZADO
                                                         */

                                                        
                                                        $dt_inicialProj = $data_demissao;
                                                        $dt_finalProj = $data_proj;

                                                        $beginProj = new DateTime($dt_inicialProj);
                                                        $endProj = new DateTime($dt_finalProj);
                                                        $endProj = $endProj->modify('+1 day');

                                                        $intervalProj = new DateInterval('P1D');
                                                        $daterangeProj = new DatePeriod($beginProj, $intervalProj, $endProj);

                                                        $mes_atualProj = 0;
                                                        $countProj = 0;
                                                        $avosProj = 0;

                                                        if (date('d', strtotime($data_demissao)) >= 15) {
                                                            $avosProj = 1;

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<br><br>bel: dt demi >= 15<br><br>";
                                                            }
                                                        }

                                                        //DEBUG
                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br>===================================DATAS PARA CALCULO DE AVOS DE FÉRIAS E 13º PROJETADOS=========================================<br>";
                                                        }

                                                        foreach ($daterangeProj as $dateProj) {
                                                            $countProj++;

                                                            if ($mes_atualProj != $dateProj->format("m")) {
                                                                $mes_atualProj = $dateProj->format("m");
                                                                $countProj = 0;
                                                            }

                                                            //DEBUG    
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo $dateProj->format("d-m-Y") . "<br>";
                                                            }

                                                            if ($countProj == 14) {
                                                                //DEBUG        
                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "+ 1 avo...<br>";
                                                                }
                                                                $avosProj++;
                                                            }
                                                        }

                                                        if ($dias_proj == 30) {
                                                            $avosProj = 1;
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<br><br>bel: dias_proj = 30<br><br>";
                                                            }
                                                        }

                                                        if ($id_clt == 843) {
                                                            $avosProj = 2;
                                                        }
                                                        
                                                        if($id_clt == 3067){
                                                            $avosProj = 2;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br> Avos: " . $avosProj . "<br><br>";
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "====================================LEI 12,506======================================== <br><pre>";
                                                            echo "anos: $diferenca_anos; <br>";
                                                            echo "$valorLei += ($baseCalcAviso / $qnt_dias_mes) * 3; <br>";
                                                            echo "<br></pre>================================================================================================== <br>";
                                                        }
                                                        


                                                        //CASO Luciana Santos (rescisão com projeção)
                                                        if ($id_clt == 2190) {
                                                            $valorLei = 658.35;
                                                        }

                                                        if ($id_clt == 3111) {
                                                            $valorLei = 1173.80;
                                                        }

                                                        //ACACIO COUTINHO - foda-se
                                                        if ($id_clt == 81) {
                                                            $valorLei = 378.53;
                                                            $lei_12_506 = $valorLei;
                                                        }

                                                        ///Verifica se  a pesssoa recebeu décimo terceiro em novembro

                                                        /**
                                                         * By Ramon 19/12/2016
                                                         * Italo pediu para calcular o 13 mesmo se a pessoa ja recebeu o valor na folha
                                                         * Pois a rescisão tem q mostrar o valor entrando e saindo
                                                         * vou zerar a variavel $array_parcelas_decimo para não entrar nesse primeiro IF...
                                                         * e sempre calcular o 13 
                                                         */
                                                        unset($array_parcelas_decimo);
                                                        $array_parcelas_decimo = array();
                                                        if ((in_array(1, $array_parcelas_decimo) && in_array(2, $array_parcelas_decimo)) || in_array(3, $array_parcelas_decimo)) {

                                                            $total_dt = 0;
                                                            $meses_ativo_dt = 0;
                                                        } else {
                                                            $primeiro_dia_ano = date('Y') . "-01-01";

                                                            //if($verifica_13_folha == 0 && $data_entrada < $primeiro_dia_ano){ 
                                                            if ($data_entrada < $primeiro_dia_ano) {
                                                                $dt_entrada_calc = $primeiro_dia_ano;
                                                            } else {
                                                                $dt_entrada_calc = $data_entrada;
                                                            }

                                                            /*
                                                             * leonardo
                                                             * 2017-01-16
                                                             * essa rescisão foi processada com data de demição errada e após o pagamento o Italo teve que desprocessar.. por isso estou forçando a data como sendo do ano passado
                                                             */
                                                            if ($id_clt == 4120) {
                                                                $dt_entrada_calc = $data_entrada;
                                                            }



                                                            //Quantidade de mese
                                                            $Calc->Calc_qnt_meses_13_ferias_rescisao($dt_entrada_calc, $data_demissao, $faltas);
                                                            if ($dispensa == 60) {
                                                                $meses_ativo_dt = 0;
                                                            } else {
                                                                $meses_ativo_dt = $Calc->meses_ativos_dt;
                                                                
                                                                $meses_ativo_dt -= $m_eventos;
                                                            }
                                                            
                                                            /**
                                                            * SINESIO LUIZ 
                                                            * 15/02/2017
                                                            * VERIFICA SE 
                                                            * MAIS DE 6 MESES EM EVENTO
                                                            * CASO SIM, PERDE O DIREITO AO 13 PROPORCIONAL 
                                                            */
                                                            if($mesesTotalEmEventoDt >= 180){ 
                                                                $meses_ativo_dt = 0;
                                                            }

                                                            $dias_mesDemi = cal_days_in_month(CAL_GREGORIAN, $mes_demissao, $ano_demissao);
                                                            $dias_mesDemiD = $dias_mesDemi / 2;

                                                            $d_t = $dias_trabalhados_nojo - $faltas;

                                                            /*
                                                             * 01/03/2016
                                                             * BY: MAX
                                                             * COMENTANDO POIS ESTAVA DANDO PROBLEMA
                                                             * EM VÁRIAS RESCISÕES
                                                             * VERIFICAR COM A MICHELE QUAL O CORRETO
                                                             */
//            if($meses_ativo_dt == 2){
//                if($d_t < $dias_mesDemiD){
//                    $meses_ativo_dt -= 1;
//                }
//            }

                                                            if ($_COOKIE['debug'] == 666) {
                                                                echo "<br><br>";
                                                                echo "$$$$$$$$$$$$$$$$$$$ CALCULO PARA AVOS DE 13º (COM FALTAS) $$$$$$$$$$$$$$$$$$$";
                                                                echo "<br>DIAS NO MÊS DA DEMISSÃO: {$dias_mesDemi}";
                                                                echo "<br>DIAS NO MÊS DA DEMISSÃO / 2: {$dias_mesDemiD}";
                                                                echo "<br>MES DAS FALTAS: {$mes_mov_falta}";
                                                                echo "<br>DIAS TRABALHADOS(MÊS DA DEMISSAO): {$dias_trabalhados_nojo}";
                                                                echo "<br>FALTAS: {$faltas}";
                                                                echo "<br>DIAS TRABALHADOS(MÊS DA DEMISSAO) - FALTAS: {$d_t}";
                                                                echo "<br><br>";
                                                            }

                                                            if ($_COOKIE['debug'] == 666) {
                                                                echo "<br><br> MESES_ATIVO_DT: ";
                                                                echo $meses_ativo_dt;
                                                                echo "<br><br>";
                                                            }

                                                            if ($_COOKIE['logado'] == 179) {
                                                                echo "´Meses em eventos: " . $m_eventos;
                                                                /**
                                                                 * 21/11/2016
                                                                 */
                                                                $meses_ativo_dt -= $m_eventos;
                                                            }

                                                            if ($id_clt == 194) {
                                                                $meses_ativo_dt = 6;
                                                            }

                                                            /**
                                                             * CASO ROMEU
                                                             */
                                                            if ($id_clt == 2757) {
                                                                $meses_ativo_dt = 0;
                                                            }

                                                            if ($id_clt == 235) {
                                                                $meses_ativo_dt = 6;
                                                            }

                                                            if ($id_clt == 144) {
                                                                $meses_ativo_dt = 4;
                                                            }

                                                            if ($id_clt == 2668) {
                                                                $meses_ativo_dt = 6;
                                                            }

                                                            if ($id_clt == 2595) {
                                                                $meses_ativo_dt = 9;
                                                            }

                                                            if ($id_clt == 4670) {
                                                                $meses_ativo_dt = 3;
                                                            }
                                                            if ($id_clt == 4639) {
                                                                $meses_ativo_dt = 0;
                                                            }
                                                            if ($id_clt == 2166) {
                                                                $meses_ativo_dt = 1;
                                                            }
                                                            if ($id_clt == 4779) {
                                                                $meses_ativo_dt = 2;
                                                            }
                                                            if ($id_clt == 2816) {
                                                                $meses_ativo_dt = 2;
                                                            }
                                                            if ($id_clt == 819) {
                                                                $meses_ativo_dt = 2;
                                                            }
                                                            if ($id_clt == 4873) {
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            /*
                                                             * 2017-03-14 - Leonardo
                                                             * a pessoa teve uma falta. ficando com 14 dias no mes da rescisao entao perde um avo.
                                                             * 1040 - MARINA ROSA PAIS
                                                             */
                                                            if ($id_clt == 1040) {
                                                                $meses_ativo_dt = 2;
                                                            }


                                                            /*
                                                             * 2017-03-21 Leonardo
                                                             * a pedido da alana
                                                             */
                                                            if ($id_clt == 2344) {
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            if ($id_clt == 4911) {
                                                                $meses_ativo_dt = 1;
                                                            }
                                                            
                                                            if ($id_clt == 2819) {
                                                                $meses_ativo_dt = 3;
                                                            }
                                                            
                                                            if ($id_clt == 969) {
                                                                $meses_ativo_dt = 3;
                                                            }
                                                            
                                                            // 230 - CATHERINE AMORIM KRON
                                                            if ($id_clt == 230) {
                                                                $meses_ativo_dt = 4;
                                                            }
                                                            
                                                            // 935 - ADRIANA DOS SANTOS MARCELINO
                                                            if ($id_clt == 935) {
                                                                $meses_ativo_dt = 4;
                                                            }
                                                            
                                                            // 4529 - CAMILA GARCIA
                                                            if ($id_clt == 4529) {
                                                                $meses_ativo_dt = 4;
                                                            }
                                                            
                                                            // 987 - ELISABETE MARIANO DIAMANTINO
                                                            if ($id_clt == 987) {
                                                                $meses_ativo_dt = 4;
                                                            }
                                                            
                                                            
                                                            if ($aviso == 'indenizado') {
                                                                if($id_clt == 4227){
                                                                    $avosProj = 1; 
                                                                }

                                                                // Décimo Terceiro Saldo de Salário (Indenizado)
                                                                $qnt_13_indenizado = $avosProj;

                                                                if ($id_clt == 1952) {
                                                                    $qnt_13_indenizado = 3;
                                                                }
                                                                if ($id_clt == 2520) {
                                                                    $qnt_13_indenizado = 3;
                                                                }  
                                                                // 2067 - CRISTIANE MORALEZ MENDES
                                                                if ($id_clt == 2067) {
                                                                    $qnt_13_indenizado = 2;
                                                                }                                                               

                                                                /**
                                                                 * By ramon
                                                                 * Forçando qnd de avos de aviso previo para funcionaria com projeção (CLT - 1967)
                                                                 * Sabino abriu uma tarefa para Donato verificar qual é a regra dessa projeção, para parar de por isso na mão
                                                                 * Adicionando no mesmo dia o CLT (2023)
                                                                 */
                                                                if ($id_clt == 1967 || $id_clt == 2023 || $id_clt == 3111) {
                                                                    $qnt_13_indenizado = 2;
                                                                }
                                                                if ($id_clt == 4038) {
                                                                    $qnt_13_indenizado = 1;
                                                                    $avosProj = 1;
                                                                }
                                                                if ($id_clt == 2606) {
                                                                    $qnt_13_indenizado = 1;
                                                                    $avosProj = 1;
                                                                }
                                                                // 3752 - THALITA DE OLIVEIRA BARRETO
                                                                if ($id_clt == 3752) {
                                                                    $qnt_13_indenizado = 2;
                                                                    $avosProj = 2;
                                                                }
                                                                // 3054 - JOSE PEDRO FERRAZ DOS SANTOS
                                                                if ($id_clt == 3054 ) {
                                                                    $qnt_13_indenizado = 2;
                                                                }

                                                                /**
                                                                 * by ramon 19/07/2016
                                                                 * Forçamos o valor para fechar a rescisão da Luciana mais agora precisamos acertar no código
                                                                 * o valor do 13 indeniazado Sabino falou q é somente o aviso previo devidido por 12
                                                                 * A vairiavel de aviso precio só é instaciada la no final, porem verifiquei q é utilizado essa variavel somente para instaciar $baseCalcAviso
                                                                 */
                                                                                                                //$valor_13_indenizado = ($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral_dt + $valorLei) / 12;


                                                                $valor_13_indenizado = $base13AvisoPrevio * $avos13AvisoPrevioIndenizado;
                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "$valor_13_indenizado = $base13AvisoPrevio * $avos13AvisoPrevioIndenizado;";
                                                                    echo '<br>$valor_13_indenizado = $base13AvisoPrevio * $avos13AvisoPrevioIndenizado;';
                                                                    echo "<br> Valor 13 indenizado: " . $valor_13_indenizado . "<br>";
                                                                    //exit();
                                                                }

                                                                // Leonardo em 2017-05-16
//                                                                if ($avosProj > 1) {
//                                                                    $valor_13_indenizado = $valor_13_indenizado * $avosProj;
//                                                                }
                                                                if ($qnt_13_indenizado > 1) {
                                                                    $valor_13_indenizado = $valor_13_indenizado * $qnt_13_indenizado;
                                                                }

                                                                /**
                                                                 * By Ramon - 15/07/2016
                                                                 * Adicionando para ZERAR O INSS caso ele seja ISENTO nessa empresa
                                                                 */
                                                                if ($row_clt['tipo_desconto_inss'] == "isento") {
                                                                    $valor_13_indenizado = 0;
                                                                    $qnt_13_indenizado = 0;
                                                                }

                                                                if ($dispensa == 65) {
                                                                    $total_avos_13_indenizado = "0";
                                                                    /**
                                                                     * COMENTANDO A PEDIDO DO ITALO 
                                                                     * 09/01/2017
                                                                     * FEITO POR SINESIO
                                                                     */
                                                                    //$total_valor_13_indenizado = NULL;
                                                                    $valor_13_indenizado = 0;
                                                                } else {
                                                                    if ($_COOKIE['logado'] == 179) {
                                                                        print_r("Quant: " . $qnt_13_indenizado);
                                                                        echo "<br>";
                                                                        print_r("Valor: " . $valor_13_indenizado);
                                                                        echo "<br>";
                                                                        print_r($t_ap);
                                                                        //exit();
                                                                    }
                                                                    $total_avos_13_indenizado = $qnt_13_indenizado;
                                                                    $total_valor_13_indenizado = $valor_13_indenizado;
                                                                }
                                                            }

                                                            if ($id_clt == 4264) {
                                                                $total_avos_13_indenizado = 1;
                                                            }

                                                            /**
                                                             * BY RAMON 20/07/2016
                                                             * NÃO ENTENDI MAIS VOU TESTAR ESSE IF AQUI, POIS ESSE IF ESTÁ LA PRA BAIXO, E ZERA VALOR DE 13 INDENIZADO E AVOS INDENIZADO
                                                             * POREM AQUI EM CIMA CALCULA BASE DE INSS, Q UTILIZA VALOR CALCULADO PARA 13 INDENIZADO
                                                             * RESUMO: AQUI TEM VALOR, E LA EM BAIXO ZERA ESSE VALOR, AQUI UTILIZA VALOR PARA CALCULAR 13 MAIS ANTES DE JOGAR ESSE VALOR NA TELA
                                                             * ELE É ZERADO LA EM BAIXO
                                                             * 
                                                             */
                                                            if ($t_ap == 0) {
                                                                $qnt_13_indenizado = 0;
                                                                $valor_13_indenizado = 0;
                                                                $total_avos_13_indenizado = 0;
                                                                $total_valor_13_indenizado = 0;
                                                            }

                                                            /*                                                             * ***********************ISSO AQUI É O SEGUINTE : ************************************************************ */
                                                            /*                                                             * ******SE O CLT JA RECEBE A 1° PARCELA, 2° PARCELA OU ATE MESMO O 13º INTEGRAL ****************************** */
                                                            /*                                                             * ******NÃO PODE DESCONTAR NOVAMENTE NA RESCISÃO ************************************************************* */
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>====================================Composição da Variável VALOR_TD===================================== <br>";
                                                                echo "(+) Salario Base: " . formato_real($salario_base_limpo) . "<br>";
                                                                echo "(+) Insalubridade: " . formato_real($valor_insalubridade_integral) . "<br>";
                                                                echo "(+) Total de Rendimento: " . formato_real($total_rendi) . "<br>";
                                                                echo "(+) Perioculosidade: " . formato_real($periculosidade_30_integral) . "<br>";
                                                                echo "(-) Adiantamento de 13° Salário: " . formato_real($valor_decimo_folha) . "<br>";
                                                                echo "(=) Total: " . formato_real($valor_decimo_folha) . "<br>";
                                                                echo "================================================================================================== <br></pre>";
                                                            }

                                                            if ($id_clt == 4264) {
                                                                $total_valor_13_indenizado = 1113.53;
                                                            }

                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $total_valor_13_indenizado = 958.75;
                                                            }
                                                            // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                            if ($id_clt == 4146) {
                                                                $total_valor_13_indenizado = 440.13;
                                                            }
                                                            // 4161 - GABRIELA HAE YOUNG OH
                                                            if ($id_clt == 4161) {
                                                                $total_valor_13_indenizado = 172.81;
                                                                $total_avos_13_indenizado = 1;
                                                            }
                                                            // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                            if ($id_clt == 4243) {
                                                                $total_valor_13_indenizado = 235.68;
                                                                $total_avos_13_indenizado = 1;
                                                            }
                                                            // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                            if ($id_clt == 4261) {
                                                                $total_valor_13_indenizado = 109.93;
                                                                $total_avos_13_indenizado = 1;
                                                            }

                                                            // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                            if ($id_clt == 4251) {
                                                                $total_valor_13_indenizado = 785.74;
                                                                $total_avos_13_indenizado = 2;
                                                            }

                                                            // 4244 - JOSE MARCOS THALENBERG
                                                            if ($id_clt == 4244) {
                                                                $total_valor_13_indenizado = 424.31;
                                                                $total_avos_13_indenizado = 1;
                                                            }

                                                            // 4159 - FRANCISCO DA SILVA GONINI
                                                            if ($id_clt == 4159) {
                                                                $total_valor_13_indenizado = 817.38;
                                                                $total_avos_13_indenizado = 2;
                                                            }

                                                            // 4148 - CAMILA ANGELO ROSA
                                                            if ($id_clt == 4148) {
                                                                $total_valor_13_indenizado = 534.28;
                                                                $total_avos_13_indenizado = 2;
                                                            }

                                                            // 4156 - EDUARDO CARDOSO PEREIRA
                                                            if ($id_clt == 4156) {
                                                                $total_valor_13_indenizado = 534.23;
                                                                $total_avos_13_indenizado = 2;
                                                            }
                                                            // 4255 - PAULO SNG MAN YOO
                                                            if ($id_clt == 4255) {
                                                                $total_valor_13_indenizado = 274.98;
                                                                $total_avos_13_indenizado = 1;
                                                            }
                                                            
                                                            // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                            if ($id_clt == 4160) {
                                                                $total_valor_13_indenizado = 655.98 ;
                                                                $total_avos_13_indenizado = 1;
                                                            }
                                                            
                                                            if ($id_clt == 2606) {
                                                                $total_valor_13_indenizado = 1341.87;
                                                                $total_avos_13_indenizado = 1;
                                                            }
                                                            
                                                            // 4196 - KELEN RAMOS OVIL
                                                            if ($id_clt == 4196) {
                                                                $total_valor_13_indenizado = 595.35;
                                                                $total_avos_13_indenizado = 1;
                                                            }
                                                            
                                                            /**
                                                            * SINESIO LUIZ 
                                                            * 15/02/2017
                                                            * VERIFICA SE 
                                                            * MAIS DE 6 MESES EM EVENTO
                                                            * CASO SIM, PERDE O DIREITO AO 13 
                                                            * SALDO INDENIZADO
                                                            */
                                                            if($mesesTotalEmEventoDt >= 180){ 
                                                                $total_avos_13_indenizado = "0";
                                                                $total_valor_13_indenizado = NULL;
                                                                $valor_13_indenizado = 0;
                                                            }

                                                            ///ALTERAÇÃO FEITA PARA NÃO PAGAR INSS E IR SOBRE 13° QUANDO O MESMO JA FOI DESCONTADO.
//                                          
//                        $qr_verifica_13 = "SELECT * FROM rh_movimentos_clt WHERE (mes_mov = '16' AND STATUS = '1' AND id_clt = '{$id_clt}') AND id_mov IN(292) ORDER BY nome_movimento";
//                        $query_adiantamento_13 = mysql_query($qr_verifica_13) or die('Erro ao selecionar movimento de adiantamente de 13°');
//                        $movimento_adiantamento_13 = array();
//                        while($rows_mov_adiantamento_13 = mysql_fetch_assoc($query_adiantamento_13)){
//                            $movimento_adiantamento_13 = $rows_mov_adiantamento_13['valor_movimento'];
//                        }
                                                            //echo $valor_td - $valor_decimo_folha;
                                                            $dias_trabalhados_mes = date("d", strtotime(str_replace("/", "-", $data_demissao)));
                                                            $valor_13_se_mais_de_quinze_dias = 0;
                                                            if ($dias_trabalhados_mes >= 15 && $aviso != 'indenizado') {
                                                                $valor_13_se_mais_de_quinze_dias = $valor_13_indenizado;
                                                            } else {
                                                                $valor_13_se_mais_de_quinze_dias = 0;
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>====================================Composição da Variável VALOR_TD===================================== <br>";
                                                                echo "Valor Salarioi base: " . $salario_base_limpo . "<br>";
                                                                echo "Valor Insalubridade: " . $valor_insalubridade_integral . "<br>";
                                                                echo "Valor total rendimento: " . $total_rendi . "<br>";
                                                                echo "Periculosidade: " . $periculosidade_30_integral_dt . "<br>";
                                                                echo "======================================================================================================== <br></pre>";
                                                                var_dump($meses_ativo_dt);
                                                            }



                                                            if ($_COOKIE['logado'] == 349) {
                                                                echo "<br>********************MEDIAS ***************************<br>";
                                                                echo "<pre>";
                                                                print_r($total_rendi_13);
                                                                echo "</pre>";
                                                            }

                                                            $total_rendi_proporcional_dt = ($total_rendi_13) * $meses_ativo_dt;

                                                            if ($id_clt == 1048) {
                                                                $total_rendi_proporcional_dt = 42.06;
                                                            }

                                                            if ($id_clt == 828) {
                                                                $total_rendi_proporcional_dt = 117.33;
                                                            }

                                                            if ($id_clt == 246) {
                                                                $total_rendi_proporcional_dt = 29.09;
                                                            }

                                                            if ($id_clt == 4873) {
                                                                $total_rendi_proporcional_dt = 31.68;
                                                            }

                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $total_rendi_proporcional_dt = 103.30;
                                                            }
                                                            
                                                            if($id_clt ==4882){
                                                                $total_rendi_proporcional_dt = 142.60;
                                                            }



                                                            $valor_td = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 12) * $meses_ativo_dt + $total_rendi_proporcional_dt; //aki
                                                            //$media_td = (($total_rendi) / 12) * $meses_ativo_dt;
                                                            // $media_td = 0; leonardo 2017-01-16 nao entendi pq isso esta aqui sem if

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<br><br><br><br>";
                                                                echo "@@@@@@@@@@@@ COMPOSIÇÃO valor_td @@@@@@@@@@@@<br>";
                                                                echo "valor_td = ((salario_base_limpo({$salario_base_limpo}) + valor_insalubridade_integral({$valor_insalubridade_integral}) + periculosidade_30_integral_dt({$periculosidade_30_integral_dt})) / 12) * meses_ativo_dt({$meses_ativo_dt}) + total_rendi_proporcional_dt({$total_rendi_proporcional_dt})";
                                                                echo "<br>@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@";
                                                                echo "<br><br><br><br>";
                                                            }

                                                            if ($valor_td < 0) {
                                                                $valor_td = 0;
                                                            }

                                                            /**
                                                             * TEM QUE CRIAR UMA ROTINA 
                                                             * PARA PEGAR O VALOR
                                                             * DE ADIANTAMENTO DE 13°
                                                             */
                                                            if ($id_clt == 81) {
                                                                $valor_td -= 775.02;
                                                            }

                                                            if ($id_clt == 2595) {
                                                                $valor_td = 910.62;
                                                            }

                                                            if ($id_clt == 4670) {
                                                                $valor_td = 1011.36;
                                                            }

                                                            if ($id_clt == 4264) {
                                                                $valor_td = 12349.82;
                                                            }

                                                            /*
                                                             * leonardo
                                                             * 2017-01-16
                                                             * a pedido do italo
                                                             */
                                                            if ($id_clt == 4120) {
                                                                $valor_td = 5773.02;
                                                            }

                                                            /**
                                                             * FEITO POR SINESIO 
                                                             * 07/02/2017
                                                             * A PEDIDO DO ÍTALO
                                                             */
                                                            if ($id_clt == 4276) {
                                                                $valor_td = 489.80;
                                                            }

                                                            if ($id_clt == 4352) {
                                                                $valor_td = 913.23;
                                                            }

                                                            /**
                                                             * FEITO POR MAX 
                                                             * 21/02/2017
                                                             * A PEDIDO DO ÍTALO
                                                             */
                                                            if ($id_clt == 4834) {
                                                                $valor_td = 709.42;
                                                            }

                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $valor_td = 1917.50;
                                                            }

                                                            // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                            if ($id_clt == 4146) {
                                                                $valor_td = 880.26;
                                                            }
                                                            // 4161 - GABRIELA HAE YOUNG OH
                                                            if ($id_clt == 4161) {
                                                                $valor_td = 345.61;
                                                            }
                                                            // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                            if ($id_clt == 4243) {
                                                                $valor_td = 471.36;
                                                            }

                                                            // 4819 - ANAMADA BARROS CARVALHO
                                                            // 2017-03-10 - Leonardo - A pedido do Italo, pois funcionaria possui faltas
                                                            if ($id_clt == 4819) {
                                                                $valor_td = 575.09;
                                                                $meses_ativo_dt = 1;
                                                            }
                                                            // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                            if ($id_clt == 4261) {
                                                                $valor_td = 219.86;
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                            if ($id_clt == 4251) {
                                                                $valor_td = 785.74;
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            // 4244 - JOSE MARCOS THALENBERG
                                                            if ($id_clt == 4244) {
                                                                $valor_td = 848.62;
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                            if ($id_clt == 4244) {
                                                                $valor_td = 917.45;
                                                                $meses_ativo_dt = 3;
                                                            }
                                                            // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                            if ($id_clt == 4203) {
                                                                $valor_td = 917.45;
                                                                $meses_ativo_dt = 3;
                                                            }

                                                            // 4159 - FRANCISCO DA SILVA GONINI
                                                            if ($id_clt == 4159) {
                                                                $valor_td = 817.38;
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            // 4148 - CAMILA ANGELO ROSA
                                                            if ($id_clt == 4148) {
                                                                $valor_td = 534.28;
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            // 4156 - EDUARDO CARDOSO PEREIRA
                                                            if ($id_clt == 4156) {
                                                                $valor_td = 534.23;
                                                                $meses_ativo_dt = 2;
                                                            }

                                                            // 4255 - PAULO SNG MAN YOO
                                                            if ($id_clt == 4255) {
                                                                $valor_td = 824.94;
                                                                $meses_ativo_dt = 3;
                                                            }

                                                            // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                            if ($id_clt == 4160) {
                                                                $valor_td = 1967.95;
                                                                $meses_ativo_dt = 3;
                                                            }

                                                            // 4295 - MARIA LAURA MARIANO DE MATOS
                                                            if ($id_clt == 4295) {
                                                                $valor_td = 2003.53 ;
                                                                $meses_ativo_dt = 4;
                                                            }

                                                            // 4196 - KELEN RAMOS OVIL
                                                            if ($id_clt == 4196) {
                                                                $valor_td = 2381.40 ;
                                                                $meses_ativo_dt = 4;
                                                            }

                                                            /**
                                                             * 21/11/2016
                                                             */
                                                            $BASE_CALC_INSS_13 = $valor_td + $valor_13_se_mais_de_quinze_dias; // - $valor_decimo_folha - $valorAdiantamento; // $media_td

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                print_array("calc BASE_CALC_INSS_13 = $BASE_CALC_INSS_13 = $valor_td + $valor_13_se_mais_de_quinze_dias;");
                                                            }

                                                            $verificaMovMedia13 = "SELECT * FROM rh_movimentos_clt AS A 
                                                 LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                                                 WHERE A.mes_mov = 16 AND A.id_clt = {$id_clt} AND A.`status`  = 1";
                                                            $sqlVerificaMov = mysql_query($verificaMovMedia13) or die(mysql_error());
                                                            $valorMedia13 = 0;
                                                            while ($rowsMedia13 = mysql_fetch_assoc($sqlVerificaMov)) {
                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "<pre>====================================Composição da Variável VALOR_TD EM TEMPO DE EXECUÇÃO===================================== <br>";
                                                                    echo "(+) VALOR DO MOVIMENTO PARA Média Dt: " . $rowsMedia13['valor_movimento'] . "<br>";
                                                                    echo "================================================================================================== <br></pre>";
                                                                }
                                                                $valorMedia13 += $rowsMedia13['valor_movimento'];
                                                            }


                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>====================================Composição da Variável VALOR_TD===================================== <br>";
                                                                echo "(+) Média Dt: " . formato_real($valorMedia13) . "<br>";
                                                                echo "================================================================================================== <br></pre>";
                                                            }

                                                            /**
                                                             * VOLTANDO MEDIA DE 13 PARA O CALCULO DE INSS 
                                                             * EM 27/06/2016 A PEDIDO DO ITALO
                                                             */
                                                            /**
                                                             * By Ramon - 05/06/2016
                                                             * Adicionando para calculo de INSS e IRRF de 13 proporcional o valor do aviso indenizado
                                                             */
                                                            /*
                                                             * subtraindo o valor das faltas da base de INSS
                                                             * Leonardo - 2017-03-14
                                                             */

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>====================================BASE_CALC_INSS_13===================================== <br>";
                                                                echo "(+) Média Dt: $BASE_CALC_INSS_13 + $valorMedia13 + $valor_13_indenizado<br>";
                                                                echo "================================================================================================== <br></pre>";
                                                            }
                                                            
                                                            
                                                            $BASE_CALC_INSS_13 = $BASE_CALC_INSS_13 + $valorMedia13 + $valor_13_indenizado;

                                                            if ($id_clt == 4264) {
                                                                $BASE_CALC_INSS_13 = 13816.25;
                                                                $valor_13_indenizado = 1113.53;
                                                            }

                                                            /*
                                                             * leonardo
                                                             * 2017-01-17
                                                             */
                                                            if ($id_clt == 4120) {
                                                                $BASE_CALC_INSS_13 = 0;
                                                            }

                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $valor_13_indenizado = 958.75;
                                                            }
                                                            if ($id_clt == 4146) {
                                                                $valor_13_indenizado = 440.13;
                                                            }
                                                            // 4161 - GABRIELA HAE YOUNG OH
                                                            if ($id_clt == 4161) {
                                                                $valor_13_indenizado = 172.81;
                                                            }
                                                            // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                            if ($id_clt == 4243) {
                                                                $valor_13_indenizado = 235.68;
                                                            }

                                                            // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                            if ($id_clt == 4261) {
                                                                $valor_13_indenizado = 109.93;
                                                            }

                                                            // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                            if ($id_clt == 4251) {
                                                                $valor_13_indenizado = 785.74;
                                                            }

                                                            // 4244 - JOSE MARCOS THALENBERG
                                                            if ($id_clt == 4244) {
                                                                $valor_13_indenizado = 424.31;
                                                            }

                                                            // 4159 - FRANCISCO DA SILVA GONINI
                                                            if ($id_clt == 4159) {
                                                                $valor_13_indenizado = 817.38;
                                                            }

                                                            // 4148 - CAMILA ANGELO ROSA
                                                            if ($id_clt == 4148) {
                                                                $valor_13_indenizado = 534.28;
                                                            }

                                                            // 4156 - EDUARDO CARDOSO PEREIRA
                                                            if ($id_clt == 4156) {
                                                                $valor_13_indenizado = 534.23;
                                                            }

                                                            // 4255 - PAULO SNG MAN YOO
                                                            if ($id_clt == 4255) {
                                                                $valor_13_indenizado = 274.98;
                                                            }

                                                            // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                            if ($id_clt == 4160) {
                                                                $valor_13_indenizado = 655.98;
                                                            }

                                                            // 4196 - KELEN RAMOS OVIL
                                                            if ($id_clt == 4196) {
                                                                $valor_13_indenizado = 595.35;
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>====================================Composição da Variável VALOR_TD===================================== <br>";
                                                                echo "(+) Valor Dt: " . formato_real($valor_td) . "<br>";
                                                                echo "(+) Valor media : " . formato_real($media_td) . "<br>";
                                                                echo "(+) Valor Decimo Indenizado: " . formato_real($valor_13_indenizado) . "<br>";
                                                                echo "(-) Valor Decimo Na folha: " . formato_real($valor_decimo_folha) . "<br>";
                                                                echo "(-) Valor se MAIS de 15 dias: " . formato_real($valor_13_se_mais_de_quinze_dias) . "<br>";
                                                                echo "(-) valtas_lancadas_valor: " . formato_real($faltas_lancadas_valor) . "<br>";
                                                                echo "(=) BASE CALC INSS 13: " . formato_real($BASE_CALC_INSS_13) . "<br>";
                                                                echo '(») LINHA DE CALCULO DA BASE INSS DE 13: $BASE_CALC_INSS_13 = $BASE_CALC_INSS_13 + $valorMedia13 + $valor_13_indenizado;<br>';
                                                                echo "(+) <br>";
                                                                echo "(+) MEDIA: " . $valorMedia13 . "<br>";
                                                                echo "(+) VALOR 13_INDENIZADO: " . $valor_13_indenizado . "<br>";
                                                                echo "================================================================================================== <br></pre>";
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>";
                                                                echo "aki:::: Base INSS 13° :::<br />";
                                                                print_r("Base: " . $BASE_CALC_INSS_13 . "<br>");
                                                                print_r("MEDIA: " . $valorMedia13 . "<br>");
                                                                print_r("VALOR 13_INDENIZADO: " . $valor_13_indenizado . "<br>");
                                                                echo "</pre>";
                                                            }

                                                            // Calculando INSS sobre DT
                                                            $Calc->MostraINSS($BASE_CALC_INSS_13, $data_demissao);
                                                            $valor_td_inss = $Calc->valor;
                                                            $PERCENTUAL_INSS_13 = $Calc->percentual;
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>";
                                                                echo "aki:::: INSS 13° :::<br />";
                                                                print_r($Calc);
                                                                echo "</pre>";
                                                            }



                                                            if ($id_clt == 1047) {
                                                                $valor_td_inss = $valor_td_inss + 132.33;
                                                            }

                                                            if ($id_clt == 1048) {
                                                                $valor_td_inss = 0;
                                                            }

                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $valor_td_inss = 333.43;
                                                            }

                                                            // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                            if ($id_clt == 4146) {
                                                                $valor_td_inss = 118.98;
                                                            }
                                                            // 4161 - GABRIELA HAE YOUNG OH
                                                            if ($id_clt == 4161) {
                                                                $valor_td_inss = 43.40;
                                                            }

                                                            // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                            if ($id_clt == 4243) {
                                                                $valor_td_inss = 61.14;
                                                            }

                                                            // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                            if ($id_clt == 4261) {
                                                                $valor_td_inss = 27.66;
                                                            }

                                                            // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                            if ($id_clt == 4251) {
                                                                $valor_td_inss = 151.39;
                                                            }

                                                            // 4244 - JOSE MARCOS THALENBERG
                                                            if ($id_clt == 4244) {
                                                                $valor_td_inss = 109.73;
                                                            }

                                                            // 4203 - MAIRA MACIEL CAMPOMIZZIf
                                                            if ($id_clt == 4203) {
                                                                $valor_td_inss = 73.40;
                                                            }

                                                            // 4159 - FRANCISCO DA SILVA GONINI
                                                            if ($id_clt == 4159) {
                                                                $valor_td_inss = 159.16;
                                                            }

                                                            // 4148 - CAMILA ANGELO ROSA
                                                            if ($id_clt == 4148) {
                                                                $valor_td_inss = 90.98;
                                                            }

                                                            // 4156 - EDUARDO CARDOSO PEREIRA
                                                            if ($id_clt == 4156) {
                                                                $valor_td_inss = 90.93;
                                                            }

                                                            // 4255 - PAULO SNG MAN YOO
                                                            if ($id_clt == 4255) {
                                                                $valor_td_inss = 94.13;
                                                            }
                                                            
                                                            // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                            if ($id_clt == 4160) {
                                                                $valor_td_inss = 240.93;
                                                            }
                                                            
                                                            // 4295 - MARIA LAURA MARIANO DE MATOS
                                                            if ($id_clt == 4295) {
                                                                $valor_td_inss = 180.32;
                                                            }
                                                            
                                                            // 4196 - KELEN RAMOS OVIL
                                                            if ($id_clt == 4196) {
                                                                $valor_td_inss = 214.33;
                                                            }

                                                            // Calculando IRRF sobre DT

                                                            if ($row_clt['desconto_outra_empresa']) {

                                                                if ($row_clt['desconto_outra_empresa'] <= $teto_inss) {

                                                                    if ($row_clt['desconto_outra_empresa'] > $valor_td_inss) {
                                                                        $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - ($row_clt['desconto_outra_empresa'] - $valor_td_inss);
                                                                    } else {
                                                                        $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - ($valor_td_inss - $row_clt['desconto_outra_empresa']);
                                                                    }
                                                                } else {
                                                                    $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13;
                                                                }
                                                            } else {
                                                                $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - $valor_td_inss;
                                                            }

                                                            if ($id_clt == 4264) {
                                                                $BASE_CALC_IRRF_13 = 15932.74;
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>====================================Composição da Variável BASE_CALC_IRRF_13 ===================================== <br>";
                                                                echo "(?) DESCONTO OUTRA EMPRESA: " . $row_clt['desconto_outra_empresa'] . "<br>";
                                                                echo "(?) TETO INSS: " . $teto_inss . "<br>";
                                                                echo '(?) SE: $row_clt[desconto_outra_empresa] > $valor_td_inss <br>';
                                                                echo '(?) ENTÃO: $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - ($row_clt["desconto_outra_empresa"] - $valor_td_inss); <br>';
                                                                echo '(?) seENÃO: $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - ($valor_td_inss - $row_clt["desconto_outra_empresa"]); <br>';
                                                                echo "<br><br>";
                                                                echo "(+) BASE_CALC_INSS_13: " . formato_real($BASE_CALC_INSS_13) . "<br>";
                                                                echo "(-) valor inss 13 : " . formato_real($valor_td_inss) . "<br>";
                                                                echo "(=) BASE CALC INSS 13: " . formato_real($BASE_CALC_IRRF_13) . "<br>";
                                                                echo '(») LINHA DE CALCULO DA BASE IRRF DE 13: $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - $valor_td_inss;<br>';
                                                            }

                                                            /**
                                                             * By Ramon 20/08/2016
                                                             * Conforme explicado pelo DONATO a regra de retensão de IR abaixo de 10,00 se aplica para a empresa, ou seja:
                                                             * A empresa não pode gerar uma DARF a baixo de 10,00, se isso acontecer a empresa espera o mes sequinte para gerar uma darf com valor maior 
                                                             * acumulando essa de 10,00 do mes anterior.
                                                             * Porem a retensão deve ser feita SEMPRE
                                                             * --
                                                             * No método espera a string 'clt' para zerar a retensão... passei outra string para não zerar
                                                             */
                                                            $Calc->MostraIRRF($BASE_CALC_IRRF_13, $id_clt, $idprojeto, $data_demissao, "clt_rescisao");


                                                            $valor_td_irrf = $Calc->valor;

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "(+) VALOR IRRF 13: " . formato_real($valor_td_irrf) . "<br>";
                                                                echo "================================================================================================== <br></pre>";
                                                            }

                                                            if ($valor_td_irrf < 0) {
                                                                $valor_td_irrf = 0;
                                                            }

                                                            /**
                                                             * CASO ROMEU
                                                             */
                                                            if ($id_clt == 2625) {
                                                                $valor_td_irrf = 371.42;
                                                            }

                                                            /**
                                                             * 26102016
                                                             */
                                                            if ($id_clt == 3081) {
                                                                $valor_td_irrf = 105.16;
                                                            }


                                                            if ($id_clt == 13) {
                                                                $valor_td_irrf = 1291.24;
                                                            }

//                        if($id_clt == 1045){
//                            $valor_td_irrf = 169.38;
//                        }


                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $valor_td_irrf = 59.53;
                                                            }
                                                            // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                            if ($id_clt == 4146) {
                                                                $valor_td_irrf = 0;
                                                            }
                                                            // 4159 - FRANCISCO DA SILVA GONINI
                                                            if ($id_clt == 4159) {
                                                                $valor_td_irrf = 0;
                                                            }
                                                            // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                            if ($id_clt == 4160) {
                                                                $valor_td_irrf = 39.91;
                                                            }
                                                            // 4196 - KELEN RAMOS OVIL
                                                            if ($id_clt == 4196) {
                                                                $valor_td_irrf = 19.73;
                                                            }

                                                            /**
                                                             * By Ramon - 19/12/2016
                                                             * Adicionando para RECALCULAR base de INSS e IRRF Caso o valor de agora seja maior q da folha
                                                             */
                                                            if ($flag_recalcula_desconto_inss_irrf_dt == 1) {
                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "<br><br><pre>********************************** RECALCULAR INSS E IRRF 13 *******************************<br>";
                                                                    echo "$ valor_td_inss = $valor_td_inss<br>";
                                                                    echo "$ valor_decimo_folhaInss = $valor_decimo_folhaInss<br>";
                                                                    echo "$ valor_td_inss NOVO = $valor_td_inss - $valor_decimo_folhaInss<br><br>";

                                                                    echo "$ valor_td_irrf = $valor_td_irrf<br>";
                                                                    echo "$ valor_decimo_folhaIRRF = $valor_decimo_folhaIRRF<br>";
                                                                    echo "$ valor_td_irrf NOVO = $valor_td_irrf - $valor_decimo_folhaIRRF<br>";

                                                                    echo "================================================================================================== <br></pre>";
                                                                }

                                                                $soma_indenizado_proporcial_13 = $total_valor_13_indenizado + $valor_td;

                                                                $Calc->MostraINSS($soma_indenizado_proporcial_13, $data_demissao);
                                                                $aliquota_soma_13 = $Calc->percentual;

                                                                $difer_soma_13 = $soma_indenizado_proporcial_13 - $valor_decimo_folha;

                                                                if ($valor_td_inss > $valor_decimo_folhaInss) {
                                                                    $valor_td_inss = $valor_td_inss - $valor_decimo_folhaInss;
//                                $valor_td_inss = $difer_soma_13 * $aliquota_soma_13;
                                                                } else {
                                                                    $valor_td_inss = 0;
                                                                }

                                                                if ($row_clt['desconto_inss']) {
                                                                    if ($row_clt['desconto_outra_empresa'] >= $teto_inss) {
                                                                        $valor_td_inss = 0;
                                                                    }
                                                                }

                                                                if ($valor_td_irrf > $valor_decimo_folhaIRRF) {
                                                                    $valor_td_irrf = $valor_td_irrf - $valor_decimo_folhaIRRF;
                                                                } else {
                                                                    $valor_td_irrf = 0;
                                                                }

                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "<br><br><pre>********************************** INSS E IRRF 13 NEW *******************************<br>";
                                                                    echo "13º Saldo Indenizado = $total_valor_13_indenizado<br>";
                                                                    echo "Décimo terceiro proporcional = $valor_td<br>";
                                                                    echo "Desconto Adiantamento 13° = $valor_decimo_folha<br>";
                                                                    echo "Soma (Décimo terceiro proporcional + Décimo terceiro proporcional) = $soma_indenizado_proporcial_13<br>";
                                                                    echo "% (Décimo terceiro proporcional + Décimo terceiro proporcional) = $aliquota_soma_13<br>";
                                                                    echo "Diferença 13º (Soma - Desconto Adiantamento 13°) = $difer_soma_13<br>";
                                                                    echo "INSS = $difer_soma_13<br>";
                                                                    echo "================================================================================================== <br></pre>";
                                                                }
                                                            }


                                                            if ($valor_td_irrf > 0) {
                                                                $PERCENTUAL_IRRF_13 = $Calc->percentual;
                                                                $QNT_DEPENDENTES_IRRF_13 = $Calc->total_filhos_menor_21;
                                                                $VALOR_DDIR_13 = $Calc->valor_deducao_ir_total;
                                                                $PARCELA_DEDUCAO_IR_13 = $Calc->valor_fixo_ir;
                                                            } else {
                                                                $BASE_CALC_IRRF_13 = 0;
                                                            }

                                                            $valor_td = number_format($valor_td, 2, '.', '');
                                                            $media_td = number_format($media_td, 2, '.', '');
                                                            $valor_td_inss = number_format($valor_td_inss, 2, '.', '');
                                                            $valor_13_indenizado = number_format($valor_13_indenizado, 2, '.', '');
                                                            $valor_td_irrf = number_format($valor_td_irrf, 2, '.', '');

                                                            // Valor do DT
                                                            $total_dt = $valor_td - $valor_td_inss - $valor_td_irrf;
                                                            $to_descontos = $to_descontos + $valor_td_inss + $valor_td_irrf;
                                                            $to_rendimentos = $to_rendimentos + $BASE_CALC_INSS_13;
                                                        }



                                                        ///COISAS DA REJANE
                                                        if ($movimento_adiantamento_13 == $valor_td) {
                                                            $valor_td_inss = 0;
                                                            $valor_td_irrf = 0;
                                                        }

                                                        if ($id_clt == 2424) {
                                                            $valor_td_irrf = 4.71;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $valor_td_irrf = 1142.29;
                                                        }



                                                        ///SOLICITADO PELA SHIRLEY POR EMAIL
                                                        $teto_inss = $mov->getTetoInss();

                                                        if ($row_clt['desconto_outra_empresa'] >= $teto_inss) {
                                                            $valor_td_inss = 0;
                                                        }

                                                        // Fim de Décimo Terceiro (DT)
                                                        // ferias
                                                        // verifica se há férias agendadas
                                                        $qr_verifica_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = 1 ORDER BY id_ferias DESC");
                                                        $total_verifica_ferias = mysql_num_rows($qr_verifica_ferias);

                                                        if (empty($total_verifica_ferias)) {
                                                            $aquisitivo_ini = $data_entrada;
                                                            $aquisitivo_end = date('Y-m-d', strtotime("" . $data_entrada . " +1 year"));
                                                        } else {
                                                            $aquisitivo_ini = date('Y-m-d', strtotime("" . $data_entrada . " + " . $total_verifica_ferias . " year"));
                                                            $aquisitivo_end = date('Y-m-d', strtotime("" . $data_entrada . " + " . ($total_verifica_ferias + 1) . " year"));
                                                        }



                                                        // Verificando Períodos Gozados
                                                        while ($periodos = mysql_fetch_assoc($qr_verifica_ferias)) {


                                                            $periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
                                                        }

                                                        // Verificando Períodos Aquisitivos, Períodos Vencidos e Período Proporcional
                                                        $quantidade_anos = (date('Y') - $ano_admissao) + 1;

                                                        for ($a = 0; $a < $quantidade_anos; $a++) {
                                                            $aquisitivo_inicio_ferias = date('Y-m-d', strtotime("$data_entrada + $a year"));
                                                            $aquisitivo_final_ferias = date('Y-m-d', mktime('0', '0', '0', $mes_admissao, $dia_admissao - 1, $ano_admissao + $a + 1));
                                                            break;
                                                        }



                                                        for ($a = 0; $a < $quantidade_anos; $a++) {

                                                            $aquisitivo_inicio = date('Y-m-d', strtotime("$data_entrada + $a year"));
                                                            $aquisitivo_final = date('Y-m-d', mktime('0', '0', '0', $mes_admissao, $dia_admissao - 1, $ano_admissao + $a + 1));


                                                            if ($_REQUEST['recisao_coletiva'] == 1) {
//                            echo "<br>";
//                            print_r($aquisitivo_final) . "<br>"; 
//                            print_r($data_demissao) . "<br>"; 
                                                            }


                                                            if ($aquisitivo_final > $data_demissao) {
                                                                $periodo_aquisitivo = $aquisitivo_inicio . '/' . $data_demissao;
                                                                $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $data_demissao;
                                                            } else {

                                                                $periodo_aquisitivo = $aquisitivo_inicio . '/' . $aquisitivo_final;
                                                                $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                                                            }

                                                            if ($_REQUEST['recisao_coletiva'] == 1) {
//                            print_r($periodo_aquisitivo) . "<br>"; 
//                            print_r($periodos_aquisitivos) . "<br>"; 
                                                            }

//                        echo "<pre>";
//                        echo '$data_demissao = '.$data_demissao.'<br>';
//                        echo 'aquisitivo_inicio = '.$aquisitivo_inicio.'<br>';
//                        echo "aquisitivo_final date('Y-m-d',strtotime('$aquisitivo_final -1 month')) = ".date('Y-m-d',strtotime("$aquisitivo_final -1 month")).'<br>';
//                        echo "</pre>";

                                                            /**
                                                             * By Ramon: 18-07-2016
                                                             * Para validar se o periodo era vencido o sistema jogava -1 mes... alterei para validar se a data final do periodo
                                                             * é menor que a data de demissão, sendo assim será periodo VENCIDO
                                                             */
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
//                echo "mengo";
//                print_array($periodo_aquisitivo);
//                print_array($periodos_gozados);

                                                                $qry_180dias_evento = "SELECT DATE_FORMAT(IF(data_retorno='0000-00-00', DATE(NOW()),data_retorno),'%Y-%m-%d') AS data_aquisitivo_ini, 
                                DATE_FORMAT(DATE_SUB(DATE_ADD(IF(data_retorno='0000-00-00', DATE(NOW()),data_retorno), INTERVAL 1 YEAR), INTERVAL 1 DAY),'%Y-%m-%d') AS data_aquisitivo_fim, 
                                DATEDIFF(IF(IF(data_retorno='0000-00-00', DATE(NOW()),data_retorno) > '$aquisitivo_final', '$aquisitivo_final', IF(data_retorno='0000-00-00', DATE(NOW()),data_retorno)), IF(DATA < '$aquisitivo_inicio','$aquisitivo_inicio', DATA)) AS soma_eventos_mais_180
                                FROM rh_eventos
                                WHERE STATUS AND id_clt = {$id_clt} AND status_reg AND (cod_status IN (20, 21, 70, 80, 90) OR (cod_status IN (20, 21, 70, 80, 90) AND data_retorno='0000-00-00')) 
                                AND (IF(DATA < '$aquisitivo_inicio','$aquisitivo_inicio', DATA) BETWEEN '$aquisitivo_inicio' AND '$aquisitivo_final' OR IF(data_retorno > '$aquisitivo_final','$aquisitivo_final',data_retorno) BETWEEN '$aquisitivo_inicio' AND '$aquisitivo_final') 
                                AND DATEDIFF(IF(IF(data_retorno='0000-00-00', DATE(NOW()),data_retorno) > '$aquisitivo_final','$aquisitivo_final', IF(data_retorno='0000-00-00', DATE(NOW()),data_retorno)), IF(DATA < '$aquisitivo_inicio','$aquisitivo_inicio', DATA)) >= 180";
                                                                $sql_180dias_evento = mysql_query($qry_180dias_evento) or die(mysql_error());
                                                                $res_180dias_evento = mysql_fetch_assoc($sql_180dias_evento);
                                                                $_dias_evento = $res_180dias_evento['soma_eventos_mais_180'];
                                                            }

                                                            $data1 = new DateTime($data_demissao);
                                                            $data2 = new DateTime($aquisitivo_final);
                                                            $ddd = $data1->diff($data2);
                                                            $dias_calc = $ddd->format('%d');


                                                            if (@!in_array($periodo_aquisitivo, $periodos_gozados) && ($aquisitivo_final < $data_demissao)) {
                                                                $periodos_vencidos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
//                            if($_dias_evento > 180){
//                                $periodos_vencidos[] = "";
//                            }

                                                                echo "<br><br><pre>";
                                                                var_dump($ddd);
                                                                echo '</pre>';
                                                                echo "periodo_aquisitivo: ";
                                                                print_array($periodo_aquisitivo);
                                                                echo "periodos_gozados: ";
                                                                print_array($periodos_gozados);
                                                                echo "periodos_vencidos: ";
                                                                print_array($periodos_vencidos);
                                                                echo "aquisitivo_final: ";
                                                                print_array($aquisitivo_final);
                                                                echo "data_demissao: ";
                                                                print_array($data_demissao);
                                                                echo "<br><br>";
                                                            }

                                                            /**
                                                             * By Ramon: 18-07-2016
                                                             * Se o final do periodo for maior que a data de DEMISSÂO, e o Inicio do periodo for menor que a data de DEmissão
                                                             * quer dizer q o periodo ainda não terminou e será proporcional
                                                             */
                                                            if ($aquisitivo_final >= $data_demissao and $aquisitivo_inicio < $data_demissao) {
                                                                $periodo_proporcional[] = $aquisitivo_inicio . '/' . $data_demissao;
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre><br>============-------------- VALIDANDO FERIAS ---------------------======================<br>";
                                                                echo "AqusitivoFinal: {$aquisitivo_final}<br>";
                                                                echo "Data Demissão: {$data_demissao}<br>";
                                                                echo "AqusitivoInicial: {$aquisitivo_inicio}<br>";
                                                                echo "<br>============-------------- VALIDANDO FERIAS ---------------------======================<br></pre>";
                                                            }
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>Períodos Gozados";
                                                            print_array($periodos_gozados);
                                                            echo "Período Proporcional";
                                                            print_array($periodo_proporcional);
                                                            echo "Período Vencidos";
                                                            print_array($periodos_vencidos);
                                                            echo "</pre>";
                                                        }

                                                        if ($_REQUEST['recisao_coletiva'] == 1) {
//                        echo "<br> - vasco";
//                        print_r($periodos_vencidos); 
                                                        }

                                                        list($periodo_proporcional_inicio, $periodo_proporcional_final) = explode('/', $periodo_proporcional[0]);

                                                        $Calc->Calc_qnt_meses_13_ferias($periodo_proporcional_inicio, $periodo_proporcional_final, NULL, $data_entrada, $data_demissao);

                                                        $meses_ativo_fp = $Calc->meses_ativos;

                                                        if ($_COOKIE['debug'] == 666) {
                                                            echo "*meses_ativo_fp: {$meses_ativo_fp} - {$periodo_proporcional_inicio} | {$periodo_proporcional_final} | {$data_entrada} | {$data_demissao}";
                                                        }

                                                        if ($id_clt == 4106) {
                                                            $meses_ativo_fp = 9;
                                                        }

                                                        if ($id_clt == 4254) {
                                                            $meses_ativo_fp = 8;
                                                        }

                                                        if ($id_clt == 4155) {
                                                            $meses_ativo_fp = 6;
                                                        }

                                                        /*
                                                         * 2017-03-20 - Leonardo
                                                         * 
                                                         */
                                                        if ($id_clt == 2380) {
                                                            $meses_ativo_fp = 9;
                                                        }
                                                        if ($id_clt == 1077) {
                                                            $meses_ativo_fp = 10;
                                                        }
                                                        if ($id_clt == 4599) {
                                                            $meses_ativo_fp = 6;
                                                        }
                                                        if ($id_clt == 4442) {
                                                            $meses_ativo_fp = 7;
                                                        }
                                                        
                                                        
                                                            // 2808 - TAMIRIS DE LIMA VITOR
                                                            if ($id_clt == 2808) {
                                                                $meses_ativo_fp = 10;
                                                            }

                                                            // 4880 - ALAN ANTUNES PEREIRA
                                                            if ($id_clt == 4880) {
                                                                $meses_ativo_fp = 3;
                                                            }
                                                            
                                                            // 3398 - MARCELO BERTOLLO
                                                            if ($id_clt == 3398) {
                                                                $meses_ativo_fp = 9;
                                                            }
                                                            
                                                            // 4909 - RAFAELA MARIA DE LIRA
                                                            if ($id_clt == 4909) {
                                                                $meses_ativo_fp = 2;
                                                            }
                                                            
                                                            // 4108 - JOAO ALBERTO PELLA DO IMPERIO
                                                            if ($id_clt == 4108) {
                                                                $meses_ativo_fp = 4;
                                                            }
                                                            
                                                            // 230 - CATHERINE AMORIM KRON
                                                            if ($id_clt == 230) {
                                                                $meses_ativo_fp = 1;
                                                            }
                                                            
                                                            // 3805 - CIBELLY BEMBEM CHAVES
                                                            if ($id_clt == 3805) {
                                                                $meses_ativo_fp = 10;
                                                            }
                                                            
                                                            // 2071 - ADELINE RISSUTO MENDES
                                                            if ($id_clt == 2071) {
                                                                $meses_ativo_fp = 4;
                                                            }
                                                            
                                                            // 4963 - BRUNO DE OLIVEIRA TREVISAN
                                                            if ($id_clt == 4963) {
                                                                $meses_ativo_fp = 2;
                                                            }
                                                            
                                                            // 4675 - DOUGLAS FELIPE LESSA
                                                            if ($id_clt == 4675) {
                                                                $meses_ativo_fp = 7;
                                                            }

                                                            /// $meses_ativo_fp :: avos de féias na mão antes desse include... senão não vai influenciar nada no cálculo
                                                            
                                                            
                                                        
                                                        // Buscando Faltas
                                                        include('faltas_rescisao.php');
                                                        // Fim da Verificação de Férias
                                                        // Férias Vencidas
                                                        if ($t_fv == 1) {


                                                            //CRIADO POR SINESIO - PROBLEMA DE PERIODO EM FERIAS VENCIDAS.
                                                            $periodos_vencidos = end($periodos_vencidos);
                                                            $periodos_venc = explode("/", $periodos_vencidos);
                                                            $periodo_venc_inicio = $periodos_venc[0];
                                                            $periodo_venc_final = $periodos_venc[1];
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>";
                                                                echo "=========================================PERIODOS VENCIDOS=============================<br><br>";
                                                                print_r($periodo_venc_inicio . " * " . $periodo_venc_final);
                                                                echo "</pre>";
                                                            }

                                                            $total_periodos_vencidos = count($periodos_vencidos);

                                                            if (empty($total_periodos_vencidos)) {

                                                                $ferias_vencidas = 'não';
                                                                $fv_valor_base = 0;
                                                                $fv_um_terco = 0;
                                                            } elseif ($total_periodos_vencidos == 1) {

//                            if($_COOKIE['logado'] == 179){
//                                echo "<pre>";
//                                    print_r("Periodo Aquisitivo: " . $aquisitivo_inicio . '/' . $aquisitivo_final . "<br />");
//                                    print_r("Salário Base: " . $salario_base_limpo . "<br />");
//                                    print_r("Valor Insalubridade: " . $valor_insalubridade_integral . "<br />");
//                                    print_r("Total Rendimentos: " . $total_rendi . "<br />");
//                                    print_r("Valor Periculosidade: " . $periculosidade_30_integral . "<br />");
//                                echo "</pre>";
//                            }
                                                                $ferias_vencidas = 'sim';
                                                                $fv_valor_base = (($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral) / $qnt_dias_mes) * $qnt_dias_fv;


                                                                $fv_um_terco = $fv_valor_base / 3;

                                                                $fv_valor_base = number_format($fv_valor_base, 2, '.', '');
                                                                $fv_um_terco = number_format($fv_um_terco, 2, '.', '');

                                                                $fv_total = $fv_valor_base + $fv_um_terco;
                                                            } elseif ($total_periodos_vencidos > 1) {

                                                                $ferias_vencidas = 'sim';
                                                                $fv_valor_base = ((($salario_base_limpo - $valor_insalubridade_integral + $total_rendi + $periculosidade_30) / $qnt_dias_mes) * $qnt_dias_fv );
                                                                $fv_um_terco = $fv_valor_base / 3;

                                                                $fv_um_terco_dobro = ($fv_valor_base / 3) * $total_periodos_vencidos;
                                                                $multa_fv = ((($salario_base_limpo + $valor_insalubridade + $periculosidade_30) / $qnt_dias_mes) * $qnt_dias_fv) * $total_periodos_vencidos;


                                                                $fv_valor_base = number_format($fv_valor_base, 2, '.', '');



                                                                $fv_um_terco = number_format($fv_um_terco, 2, '.', '');
                                                                $fv_um_terco_dobro = number_format($fv_um_terco_dobro, 2, '.', '');
                                                                $multa_fv = number_format($multa_fv, 2, '.', '');

                                                                $fv_total = $fv_valor_base + $fv_um_terco + $fv_um_terco_dobro;
                                                            }
                                                        } else {

                                                            $fv_total = 0;
                                                            $fv_valor_base = 0;
                                                            $fv_um_terco = 0;
                                                        }

                                                        if ($id_clt == 3033) {
                                                            $fv_total = 0;
                                                            $fv_valor_base = 0;
                                                            $fv_um_terco = 0;
                                                        }

                                                        if ($id_clt == 4305) {
                                                            $fv_total = 5660.54;
                                                            $fv_valor_base = 5660.54;
                                                            $fv_um_terco = 1886.85;
                                                        }

                                                        if ($id_clt == 3081) {
                                                            $fv_total = 4034.11;
                                                            $fv_valor_base = 4034.11;
                                                            $fv_um_terco = 1344.70;
                                                        }

                                                        if ($id_clt == 3111) {
                                                            $periodo_venc_inicio = "2015-10-15";
                                                            $periodo_venc_final = "2016-10-14";
                                                        }

                                                        // Fim de Férias Vencidas
                                                        //////////////////////////////
                                                        //FÉRIAS PROPORCIONAIS /////
                                                        ///////////////////////////////
                                                        if ($t_fp == 1) {


                                                            if ($id_clt == 34) {
                                                                $meses_ativo_fp = 3;
                                                            }

                                                            //BY RAMON 21/07/2016
                                                            /**
                                                             * CASO DA FUNCIONARIA QUE É AVISO PREVIO INDENIZADO COM PROJEÇÃO
                                                             * A PROJEÇÃO FAZ COM Q ELA GANHE MAIS 1 MES DE FERIAS PROPORCIONAL
                                                             */
                                                            if ($id_clt == 2190) {
                                                                $meses_ativo_fp = 12;
                                                                $qnt_dias_fp = 30;
                                                            }

                                                            if ($id_clt == 2676) {
                                                                $meses_ativo_fp = 6;
                                                                $qnt_dias_fp = 15;
                                                            }

                                                            if ($id_clt == 16 || $id_clt == 19 || $id_clt == 4 || $id_clt == 15) {
                                                                $meses_ativo_fp = 6;
                                                                $qnt_dias_fp = 15;
                                                            }

                                                            if ($id_clt == 15) {
                                                                $meses_ativo_fp = 6;
                                                                $qnt_dias_fp = 12;
                                                            }


                                                            if ($id_clt == 889) {
                                                                $meses_ativo_fp = 5;
                                                                $qnt_dias_fp = 12.5;
                                                            }

                                                            if ($id_clt == 4106) {
                                                                $meses_ativo_fp = 9;
                                                            }

                                                            if ($id_clt == 235) {
                                                                $meses_ativo_fp = 6;
                                                                $qnt_dias_fp = 15;
                                                            }

                                                            if ($id_clt == 144) {
                                                                $meses_ativo_fp = 0;
                                                                $qnt_dias_fp = 0;
                                                            }

                                                            if ($id_clt == 2207) {
                                                                $meses_ativo_fp = 10;
                                                                $qnt_dias_fp = 25;
                                                            }

                                                            if ($id_clt == 4192) {
                                                                $meses_ativo_fp = 2;
                                                                $qnt_dias_fp = 5;
                                                            }
                                                            
                                                            if ($id_clt == 4879 ) {
                                                                $meses_ativo_fp = 3;
                                                                $qnt_dias_fp = 7.5;
                                                            }

                                                            if ($id_clt == 4658) {
                                                                $meses_ativo_fp = 3;
                                                            }

                                                            //EQUIVALE A UM PERIODO MAIOR QUE 6 MESES DENTRO DE EVENTO
                                                            if ($dias_total_evento > 180 && $id_clt != 4811) {

                                                                $meses_ativo_fp = 0;
                                                            }

                                                            if ($meses_ativo_fp < 0) {

                                                                $meses_ativo_fp = 0;
                                                            }

                                                            if ($id_clt == 3111) {
                                                                $meses_ativo_fp = 4;
                                                            }

                                                            if ($id_clt == 727) {
                                                                $meses_ativo_fp = 10;
                                                            }

                                                            if ($id_clt == 4580) {
                                                                $meses_ativo_fp = 3;
                                                            }


//            if ($id_clt == 4849) {
//                $meses_ativo_fp = 1;
//            }
                                                            
                                                            if($id_clt == 2606){
                                                                $meses_ativo_fp = 3;
                                                            }

                                                            /*
                                                              if($_COOKIE['logado'] == 87){

                                                              $qr_faltas_ferias = mysql_query("SELECT SUM(qnt) as faltas from rh_movimentos_clt as A
                                                              INNER JOIN rh_folha as B
                                                              ON A.id_folha = B.id_folha
                                                              WHERE A.id_clt = '4762' AND id_mov = 62
                                                              AND B.data_inicio > '$periodo_proporcional_inicio' AND B.data_fim <= '$periodo_proporcional_final'
                                                              UNION
                                                              SELECT SUM(qnt) as faltas FROM rh_movimentos_clt WHERE id_clt = '4762' AND id_mov = 62 AND mes_mov = 16");
                                                              while($row_faltas = mysql_fetch_assoc($qr_faltas_ferias)){
                                                              $faltas_ferias +=  $row_faltas['faltas'];
                                                              }

                                                              if($faltas_ferias > 0 and $faltas_ferias <=5){             $qnt_ferias = $meses_ativo_fp * 2.5;
                                                              }elseif($faltas_ferias >5 and $faltas_ferias <=14){        $qnt_ferias = $meses_ativo_fp * 2;
                                                              }elseif($faltas_ferias > 14 and $faltas_ferias <=23 ){     $qnt_ferias = $meses_ativo_fp * 1.5;
                                                              }elseif (($faltas_ferias >24 and $faltas_ferias <= 32)) {  $qnt_ferias = $meses_ativo_fp * 1;
                                                              }elseif($faltas_ferias >32){                               $qnt_ferias = 0;   }




                                                              if($qnt_ferias != 0){


                                                              $fp_mes          = ($salario_base_limpo + $valor_insalubridade_integral)/12 ;
                                                              $fp_valor_total  = ($fp_mes/30) * $qnt_ferias;
                                                              echo $salario_base_limpo + $valor_insalubridade_integral;


                                                              } elseif($qnt_ferias == 0)  { $meses_ativo_fp  = 0;    }
                                                              else {

                                                              $fp_valor_total = ($fp_valor_mes  / 12) * $meses_ativo_fp;
                                                              }

                                                              }
                                                             */
                                                            /**
                                                             * REMOVENDO $total_rendi A PEDIDO DA MICHELE DO RH
                                                             * 05-03-2015 -> REJANE PEDIU PARA COLOCAR AS MÉDIAS PARA CALCULO DO AVISO DE FÉRIAS INDENIZADO.. ELA FALOU Q A MICHELE MANDOU REMOVER AS MÉDIAS DO CALCULO 
                                                             * DE FÉRIAS PROPORCIONAIS, ADICIONEI NOVAMENTE O $total_rendi NO CALCULO. COMO SÃO 2 BASES DIFERENTE, VOU CRIAR OUTRA VARIAVEL PARA BASE DE CALCULO DAS
                                                             * AVISO INDENIZADO NAS FERIAS. (RAMON)
                                                             */
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>*********************BASE DE FERIAS PROPORCIONAL***********************";
                                                                echo "<br>Salario: " . $salario_base_limpo . "<br>";
                                                                echo "Insalubridade: " . $valor_insalubridade_integral . "<br>";
                                                                echo "Periculosidade: " . $periculosidade_30_integral . "<br>";
                                                                echo "***********************************************************************</pre>";
                                                            }

//            if($_COOKIE['logado'] == 353){
                                                            if ($dispensa == 66) {
                                                                if ($mes_demissao == 02) {
                                                                    $dias_trabalhados = 28;
                                                                    $qnt_dias_mes = 28;
                                                                }
                                                            }
//            }
                                                            //COMENTADO EM 17-06-2016
                                                            //$fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / $qnt_dias_mes) * $qnt_dias_fp; //AKI
                                                            //$fp_valor_mes_inde = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $total_rendi) / $qnt_dias_mes) * $qnt_dias_fp; //AKI
                                                            //$fp_valor_total = ($fp_valor_mes / 12) * $meses_ativo_fp;
                                                            //EM 21-06-2016 ALANA QUESTINOU O CÓDIGO, DIZENDO Q ESTÁ ERRADO
                                                            //[13:58:19] Alana Amaral: Ramon estava falando com o Italo e a base de cálculo pra férias pra média é diferente
                                                            //[13:59:28] Alana Amaral: Ex. Valor total da média (43,02/12)*3
                                                            //[13:59:44] Alana Amaral: três é a quantidade de avos que ele tem de férias
                                                            //[13:25:12] Alana Amaral: (Salário/12)*3 = médias
                                                            ////--**//**
                                                            //$fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp + $total_rendi; //AKI
                                                            //$fp_valor_mes_inde = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp + $total_rendi; //AKI
                                                            //$fp_valor_total = $fp_valor_mes;

                                                            $media_mov_fixos_para_ferias = ($total_movi_fixo_para_media_ferias / 12) / 12 * $meses_ativo_fp;

                                                            if ($id_clt == 1) {
                                                                $media_mov_fixos_para_ferias = 0;
                                                            }

                                                            if ($id_clt == 2340) {
                                                                $media_mov_fixos_para_ferias = 702.05;
                                                            }

                                                            /*
                                                             * 2017-03-09 - Leonardo
                                                             * Rescisao da 4163 - GISELE MARIA AMARAL
                                                             * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                             */
                                                            if ($id_clt == 4163) {
                                                                $media_mov_fixos_para_ferias = 3640.53;
                                                            }

                                                            $media_mov_fixos_para_um_terco_ferias = $media_mov_fixos_para_ferias / 3;

                                                            if ($id_clt == 144) {
                                                                $media_mov_fixos_para_um_terco_ferias = 0;
                                                            }

                                                            $fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp + $media_mov_fixos_para_ferias; //AKI
                                                            $fp_valor_mes_semMedia = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp; //PARA O CALCULO DO 1/3  
                                                            $fp_valor_mes_inde = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp + $media_mov_fixos_para_ferias; //AKI
                                                            $fp_valor_total = $fp_valor_mes;


                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre><br><br>*******------------------------------ Calc Ferias Prop --------------------------------------------------<br>
                                (1) $fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp + $media_mov_fixos_para_ferias; <br>
                                (2) $fp_valor_mes_semMedia = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp;  <br>
                                (3) $fp_valor_mes_inde = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt) / 30) * $qnt_dias_fp + $media_mov_fixos_para_ferias; <br>
                                (4) $fp_valor_total = $fp_valor_mes; <br></pre>
                                ";
                                                            }


                                                            ///Férias (aviso_indenizado)
                                                            if ($aviso == 'indenizado' and $total_meses != 12 and $dispensa != 65 and $dispensa != 63 and $dispensa != 64 and $dispensa != 66) {

                                                                //BY RAMON - 05/07/2016
                                                                //comentando:
                                                                //$ferias_aviso_indenizado         = $fp_valor_mes_inde / 12;

                                                                /**
                                                                 * By Ramon 17/10/2016
                                                                 * Para facilitar e padronizar, vou criar a variavel de avos de aviso de ferias indenizado
                                                                 * Antes era sempre 1/12 porem com o IABAS começou a aparecer 2.. 3...
                                                                 * Vou colocar a variavel até o Donato definir o calculo para achar esse valor.
                                                                 * até lá irá iniciar com 1 avo
                                                                 */
                                                                $avos_aviso_ferias_indenizado = $avosProj;

                                                                if ($id_clt == 1952 || $id_clt == 1967 || $id_clt == 2023 || $id_clt == 3111) {
                                                                    $avos_aviso_ferias_indenizado = 2;
                                                                }

                                                                if ($id_clt == 4038) {
                                                                    $avos_aviso_ferias_indenizado = 1;
                                                                }

                                                                if ($id_clt == 843) {
                                                                    $avos_aviso_ferias_indenizado = 1;
                                                                }
                                                                if ($id_clt == 4245) {
                                                                    $avos_aviso_ferias_indenizado = 1;
                                                                }
                                                                /*
                                                                 * 2017-03-20 - Leonardo
                                                                 */
                                                                if ($id_clt == 2380) {
                                                                    $avos_aviso_ferias_indenizado = 2;
                                                                }
                                                                if ($id_clt == 4221) {
                                                                    $avos_aviso_ferias_indenizado = 1;
                                                                }
                                                                
                                                                if ($id_clt == 2520) {
                                                                    $avos_aviso_ferias_indenizado = $avos_aviso_ferias_indenizado -1;
                                                                }
                                                                
                                                                if($id_clt == 2606 ){
                                                                    $avos_aviso_ferias_indenizado = 1;
                                                                }
                                                                
                                                                // 3752 - THALITA DE OLIVEIRA BARRETO
                                                                if($id_clt == 3752 ){
                                                                    $avos_aviso_ferias_indenizado = 1;
                                                                }
                                                                
                                                                // 2067 - CRISTIANE MORALEZ MENDES
                                                                if($id_clt == 2067 ){
                                                                    $avos_aviso_ferias_indenizado = 2;
                                                                }


                                                                //MESMO CALCULO UTILIZADO NO INDENIZADO DO 13
                                                                /**
                                                                 * By ramon 19/07/2016
                                                                 * Modificamos o calculo do 13 indenizado
                                                                 * para não calcular novamente vou utilizsar a variavel q esta la no 13 saldo indenizado e atribuir aqui = foda-se
                                                                 */
                                                                //$ferias_aviso_indenizado         = ($salario_base_limpo + $valor_insalubridade_integral + $total_rendi + $periculosidade_30_integral_dt + $valorLei) / 12;
                                                                $ferias_aviso_indenizado = ($baseCalcAviso / 12);
                                                                if ($avos_aviso_ferias_indenizado > 1) {
                                                                    $ferias_aviso_indenizado = $ferias_aviso_indenizado * $avos_aviso_ferias_indenizado;
                                                                }

                                                                $umterco_ferias_aviso_indenizado = $ferias_aviso_indenizado / 3;

                                                                $ferias_aviso_indenizado = number_format($ferias_aviso_indenizado, 2, '.', '');
                                                                $umterco_ferias_aviso_indenizado = number_format($umterco_ferias_aviso_indenizado, 2, '.', '');

                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "<pre>";
                                                                    echo "========================================= FERIAS (AVISO_INDENIZADO) Dentro do IF =============================<br><br>";
                                                                    echo "-- if ($aviso == 'indenizado' and $total_meses != 12 and $dispensa != 65 and $dispensa != 63 and $dispensa != 64 and $dispensa != 66) {<br>";
                                                                    echo "= avos_aviso_ferias_indenizado = {$avos_aviso_ferias_indenizado}<br>";
                                                                    echo "= valor_13_indenizado = {$valor_13_indenizado}<br>";
                                                                    print_r($fp_valor_mes_inde . " - " . $ferias_aviso_indenizado);
                                                                    echo "</pre>";
                                                                }
                                                            }


                                                            $umterco_ferias_aviso_indenizado = number_format($umterco_ferias_aviso_indenizado, 2, '.', '');
                                                            $fp_valor_mes_semMedia = number_format($fp_valor_mes_semMedia, 2, '.', '');

                                                            if ($t_fa == 1) {
                                                                $fp_um_terco = $fp_valor_mes_semMedia / 3 + $media_mov_fixos_para_um_terco_ferias; //1/3 da valor da ferias sem a media + a média do 1/3
                                                                $fp_um_terco = number_format($fp_um_terco, 2, '.', '');
                                                                $fp_total = $fp_valor_total + $fp_um_terco;
                                                            } else {
                                                                $fp_total = $fp_valor_total;
                                                            }
                                                        } else {
                                                            $fp_total = 0;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $ferias_aviso_indenizado = 1113.53;
                                                            $umterco_ferias_aviso_indenizado = 371.18;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $ferias_aviso_indenizado = 1316.51;
                                                            $umterco_ferias_aviso_indenizado = 438.84;
                                                        }
                                                        if ($id_clt == 4146) {
                                                            $ferias_aviso_indenizado = 810.65;
                                                            $umterco_ferias_aviso_indenizado = 270.22;
                                                        }
                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $ferias_aviso_indenizado = 315.24;
                                                            $umterco_ferias_aviso_indenizado = 105.08;
                                                        }

                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $ferias_aviso_indenizado = 395.47;
                                                            $umterco_ferias_aviso_indenizado = 131.82;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $ferias_aviso_indenizado = 346.26;
                                                            $umterco_ferias_aviso_indenizado = 115.42;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                            $fp_valor_total = 0;
                                                            $fp_um_terco = 0;
                                                        }

                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $ferias_aviso_indenizado = 936.64;
                                                            $umterco_ferias_aviso_indenizado = 312.21;
                                                            $avos_aviso_ferias_indenizado = 2;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $ferias_aviso_indenizado = 424.31;
                                                            $umterco_ferias_aviso_indenizado = 141.44;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $ferias_aviso_indenizado = 412.09;
                                                            $umterco_ferias_aviso_indenizado = 137.36;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $ferias_aviso_indenizado = 516.32;
                                                            $umterco_ferias_aviso_indenizado = 172.11;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $ferias_aviso_indenizado = 818.80;
                                                            $umterco_ferias_aviso_indenizado = 272.93;
                                                            $avos_aviso_ferias_indenizado = 2;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $ferias_aviso_indenizado = 361.43;
                                                            $umterco_ferias_aviso_indenizado = 120.48;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                        if ($id_clt == 4160) {
                                                            $ferias_aviso_indenizado = 586.50;
                                                            $umterco_ferias_aviso_indenizado = 195.50;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $ferias_aviso_indenizado = 801.82;
                                                            $umterco_ferias_aviso_indenizado = 267.27;
                                                            $avos_aviso_ferias_indenizado = 1;
                                                        }

                                                        // Cálculo de Férias
                                                        $ferias_total = $fp_total + $fv_total + $ferias_aviso_indenizado + $umterco_ferias_aviso_indenizado;

                                                        /**
                                                         * CASO ROMEUF
                                                         */
                                                        if ($id_clt == 2625) {
                                                            $fp_valor_total = 8247.17;
                                                            $fp_um_terco = 2749.06;
                                                        }

                                                        if ($id_clt == 4352) {
                                                            $fp_valor_total = 7019.62;
                                                            $fp_um_terco = 2339.87;
                                                        }

//        if ($id_clt == 4849) {
//            $fp_valor_total = 1067.42;
//            $fp_um_terco = 533.71;
//        }

                                                        if ($id_clt == 3449) {
                                                            $fp_valor_total = 826.63;
                                                            $fp_um_terco = 275.54;
                                                        }

                                                        if ($id_clt == 3111) {
                                                            $fp_valor_total = 978.17;
                                                            $fp_um_terco = 326.06;
                                                        }

                                                        if ($id_clt == 727) {
                                                            $fp_valor_total = 1189.78;
                                                            $fp_um_terco = 396.59;
                                                        }

                                                        if ($id_clt == 4580) {
                                                            $fp_valor_total = 550.87;
                                                            $fp_um_terco = 183.62;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $fp_valor_total = 12248.86;
                                                            $fp_um_terco = 4082.95;
                                                        }
                                                        if ($id_clt == 4658) {
                                                            $fp_valor_total = 527.47;
                                                            $fp_um_terco = 175.82;
                                                        }

                                                        /*
                                                         * leonardo
                                                         * 2017-01-16
                                                         * a pedido do italo
                                                         */
                                                        if ($id_clt == 4120) {
                                                            $fp_valor_total = 6597.74;
                                                            $fp_um_terco = 2199.25;
                                                        }

                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 07/02/2017
                                                         * A PEDIDO DO ÍTALO
                                                         */
                                                        if ($id_clt == 4276) {
                                                            $fp_valor_total = 0;
                                                            $fp_um_terco = 0;
                                                            $fv_valor_base = 5935.11;
                                                            $fv_um_terco = 1978.37;
                                                        }

                                                        /*
                                                         * 2017-03-13 - Leonardo
                                                         * fazendo férias de plantonista pq o sistema não calcula.
                                                         */
                                                        //4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $fv_valor_base = 4155.16;
                                                            $fv_um_terco = 1385.05;
                                                        }
                                                        //4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $fv_valor_base = 4335.32;
                                                            $fv_um_terco = 1445.11;
                                                        }
                                                        // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                        if ($id_clt == 4203) {
                                                            $fv_valor_base = 5566.98;
                                                            $fv_um_terco = 1855.66;
                                                        }
                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $fv_valor_base = 6195.83;
                                                            $fv_um_terco = 2065.28;
                                                            $fp_valor_total = 0;
                                                            $fp_um_terco = 0;
                                                            $meses_ativo_fp = 0;
                                                        }


                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $fv_valor_base = 4912.82;
                                                            $fv_um_terco = 1637.61;
                                                            $fp_valor_total = 0;
                                                            $fp_um_terco = 0;
                                                            $meses_ativo_fp = 0;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $fv_valor_base = 6321.43;
                                                            $fv_um_terco = 2107.14;
                                                            $fp_valor_total = 1807.161111;
                                                            $fp_um_terco = 602.387037;
                                                            $meses_ativo_fp = 5;
                                                        }
                                                        
                                                        if ($id_clt == 969) {
                                                            $fv_valor_base = 1859.45;
                                                            $fv_um_terco = 619.82;
                                                        }
                                                        
                                                        
                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $fv_valor_base = 6718.66;
                                                            $fv_um_terco = 2239.55;
                                                            $fp_valor_total = 4582.60;
                                                            $fp_um_terco = 1527.53;
                                                            $meses_ativo_fp = 9;
                                                        }
                                                        
                                                        
                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 21/02/2017
                                                         * A PEDIDO DO ÍTALO
                                                         */
                                                        if ($id_clt == 4834) {
                                                            $fp_valor_total = 354.71;
                                                            $fp_um_terco = 118.24;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $fp_valor_total = 14481.63;
                                                            $fp_um_terco = 4827.21;
                                                        }
                                                        if ($id_clt == 4146) {
                                                            $fp_valor_total = 7295.83;
                                                            $fp_um_terco = 2431.94;
                                                        }
                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $fp_valor_total = 3467.66;
                                                            $fp_um_terco = 1155.89;
                                                        }
                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $fp_valor_total = 4350.19;
                                                            $fp_um_terco = 1450.06;
                                                            $meses_ativo_fp = 11;
                                                        }

                                                        // 4819 - ANAMADA BARROS CARVALHO
                                                        if ($id_clt == 4819) {
                                                            $fp_valor_total = 690.11;
                                                            $fp_um_terco = 230.04;
                                                            $meses_ativo_fp = 3;
                                                        }

                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $fp_valor_total = 2341.60;
                                                            $fp_um_terco = 780.53;
                                                            $meses_ativo_fp = 5;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $fp_valor_total = 848.62;
                                                            $fp_um_terco = 282.87;
                                                            $meses_ativo_fp = 2;
                                                        }

                                                        // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                        if ($id_clt == 4203) {
                                                            $fp_valor_total = 1769.51;
                                                            $fp_um_terco = 589.84;
                                                            $meses_ativo_fp = 4;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $fp_valor_total = 4532.97;
                                                            $fp_um_terco = 1510.99;
                                                            $meses_ativo_fp = 11;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $fp_valor_total = 1807.161111;
                                                            $fp_um_terco = 602.387037;
                                                            $meses_ativo_fp = 5;
                                                        }
                                                        
                                                        // 1022 - NAIMA ROMERO VILELA FAVORITO
                                                        if ($id_clt == 1022) {
                                                            $fp_valor_total = 17032.68;
                                                            $fp_um_terco = 5677.56;
                                                        }
                                                        
                                                        // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                        if ($id_clt == 4160) {
                                                            $fp_valor_total = 1172.99;
                                                            $fp_um_terco = 391.00;
                                                            $meses_ativo_fp = 3;
                                                        }
                                                        
                                                        if ($id_clt == 2606) {
                                                            $fp_valor_total = 4025.61;
                                                            $fp_um_terco = 1341.87;
                                                            $meses_ativo_fp = 3;
                                                        }
                                                        
                                                        if ($id_clt == 4878) {
                                                            $fp_valor_total = 1725.27;
                                                            $fp_um_terco = 575.09;
                                                            $meses_ativo_fp = 3;
                                                        }

                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $fp_valor_total = 8018.18;
                                                            $fp_um_terco = 2672.73;
                                                            $meses_ativo_fp = 10;
                                                        }
                                                        
                                                        /*
                                                         * Leonardo - 2017-05-22
                                                         * chamado 2862
                                                         * 987 - ELISABETE MARIANO DIAMANTINO
                                                         */
                                                        if ($id_clt == 987) {
                                                            $fp_valor_total = 107.68;
                                                            $fp_um_terco = 35.89;
                                                        }


                                                        /*
                                                         * 2017-03-07 - Leonardo
                                                         * ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO ATENÇÂO
                                                         * gambiarra para feriaz com 12/12 avos serem contadas como vencidas.
                                                         * se der merda é aqui
                                                         * FERIAS VENCIDAS
                                                         * FÈRIAS VENCIDAS
                                                         */
                                                        if ($meses_ativo_fp == 12) {

                                                            $meses_ativo_fp = 0;
                                                            /* DATAS */
                                                            $periodo_venc_inicio = $periodo_proporcional_inicio;
                                                            $periodo_venc_final = $periodo_proporcional_final;
                                                            /* VALOR */
                                                            $fv_valor_base = $fp_valor_total;
                                                            $fv_um_terco = $fp_um_terco;

                                                            $periodo_proporcional_inicio = null;
                                                            $periodo_proporcional_final = null;
                                                            $fp_valor_total = null;
                                                            $fp_um_terco = null;
                                                        }


                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>" . $to_rendimentos . "<br>";
                                                            echo "Valor Base: " . $fv_valor_base . "<br>";
                                                            echo "Férias Proporcional: " . $fp_valor_total . "<br>";
                                                            echo "Terço Férias Proporcional: " . $fp_um_terco . "<br>";
                                                            echo "Férias Vencidas: " . $fv_um_terco . "<br>";
                                                            echo "1/3 Férias Vencidas dobro: " . $fv_um_terco_dobro . "<br>";
                                                            echo "Multa Ferias Vencida: " . $multa_fv . "<br>";
                                                            echo "Ferias Aviso Indenizado: " . $ferias_aviso_indenizado . "<br>";
                                                            echo "1/3 Ferias Aviso Indenizado: " . $umterco_ferias_aviso_indenizado . "<br></pre>";
                                                        }

                                                        $to_rendimentos = $to_rendimentos + $fv_valor_base + $fp_valor_total + $fp_um_terco + $fv_um_terco + $fv_um_terco_dobro + $multa_fv + $ferias_aviso_indenizado + $umterco_ferias_aviso_indenizado;
                                                        $to_descontos = $to_descontos;


                                                        // Fim de Férias
                                                        // Fim de Férias Proporcionais
                                                        //////ACERTANDO A PARTIR DAQUI (AVISO PRÉVIO)
                                                        $valor_de_media_ap = 0;
                                                        if ($fator == "empregador" && $aviso == "indenizado") {
                                                            $valor_de_media_ap = $total_rendi;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>==========================BASE AVISO PREVIO=====================<br>";
                                                            echo "salario limpo: " . $salario_base_limpo . "<br>";
                                                            echo "Insalubridade: " . $valor_insalubridade_integral . "<br>";
                                                            echo "Periculosidade: " . $periculosidade_30_integral_dt . "<br>";
                                                            echo "Total rendimento: " . $total_rendi . "<br>";
                                                            echo "Lei 12.506: " . $lei_12_506 . "<br>";
                                                            echo "baseCalcAviso: " . $baseCalcAviso . "<br>";
                                                            echo "===============================================================</pre>";
                                                        }

                                                        if ($um_ano == 0 && $dispensa != 65) {
                                                            $baseCalcAviso = (($baseCalcAviso - $total_rendi) + ($total_rendi / 12) * $meses_ativo_fp);
                                                        }


                                                        $baseCalcAviso = number_format($baseCalcAviso, 2, '.', '');
                                                        $valorLei = number_format($valorLei, 2, '.', '');

                                                        //echo "Base de calculo do Aviso: " . $baseCalcAviso. "<br>";

                                                        /*                                                         * ***********CONDIÇÃO PARA LEI 12.506************** */
                                                        /**
                                                         * 64 E 66 NÃO ENTRA LEI 12.506 POIS É TERMINO DE CONTRATO DE EXPERIENCIA E ANTECIPAÇÃO DE CONTRATO DE EXPERIENCIA RESPECTIVAMENTE
                                                         */
                                                        $array_despensa = array(61);
                                                        if ($fator == "empregador" AND in_array($dispensa, $array_despensa)) {
                                                            $lei_12_506 = $valorLei;

                                                            /*
                                                             * 21/08/2015
                                                             * by: MAX | chamado: 749
                                                             * FOI SOLICITADO PELA REJANE
                                                             */
                                                            //$baseCalcAviso += $total_rendi;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br><br>--------------------------------------- VERIFICANDO INSALUBRIDADE PELA CONF DA RESCISÃO ----------------------------------<br><pre>";
                                                            echo "[ ] - var t_ap = {$t_ap}<br>";
                                                            echo "[ ] - var aviso = {$aviso}<br>";
                                                            echo "[ ] - var dispensa = {$dispensa}<br>";
                                                            echo "[ ] - var fator = {$fator}<br>";
                                                            echo "[ ] - lei_12_506 = {$lei_12_506}<br>";
                                                            echo "--------------------------------------- VERIFICANDO INSALUBRIDADE PELA CONF DA RESCISÃO ----------------------------------</pre>";
                                                        }

                                                        /**
                                                         * By ramon - 05/07/2016
                                                         * Insalubridade deve ser pago independente do tipo de demissão, se é trabalhado ou não, empregador ou empregado.
                                                         */
                                                        $valor_insalubridade = $insalubridade['valor_proporcional'];

                                                        if ($t_ap == 1 and $aviso == 'indenizado') {

                                                            $dias_aviso = aviso_previo_convencao($row_clt['rh_sindicato'], $row_clt['data_nasci'], $row_clt['data_entrada']);

                                                            if(in_array($_COOKIE['logado'], $programadores)){
                                                                echo "<pre>llll dias de aviso: $dias_aviso</pre>";
                                                            }

                                                            
                                                            // comentado em 2017-03-06
//            $valor_aviso_previo = $baseCalcAviso;
                                                            $valor_aviso_previo = ($baseCalcAviso / 30) * $dias_aviso;

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo '<pre>================= dias aviso previo ==================<br>';
                                                                echo "dias_aviso = $dias_aviso<br>";
                                                                echo "$valor_aviso_previo = ($baseCalcAviso/30)*$dias_aviso;<br>";
                                                                echo '================= dias aviso previo ==================</pre>';
                                                            }


                                                            if ($dispensa == 65) {
                                                                $aviso = "PAGO pelo funcionário";
                                                                $valor_ap_pago_trab = $valor_aviso_previo;
//                            $valor_insalubridade  = $insalubridade['valor_integral']; 
                                                                $valor_insalubridade = $insalubridade['valor_proporcional'];
                                                            } else {
                                                                $valor_insalubridade = $insalubridade['valor_proporcional'];
                                                                $valor_ap_recebido_trab = $valor_aviso_previo;
                                                                $lei_12_506 = $valorLei;
                                                            }
                                                        } elseif ($aviso == 'trabalhado' and $t_ap == 1 and $fator != "empregado") {

                                                            $lei_12_506 = $valorLei;
                                                        } elseif ($t_ap == 0) {


                                                            $valor_insalubridade = $insalubridade['valor_proporcional'];
//                       
                                                            $valor_aviso_previo = NULL;
                                                            $total_avos_13_indenizado = "0";
                                                            $total_valor_13_indenizado = NULL;
                                                            $valor_ap_recebido_trab = NULL;
                                                            $valor_ap_pago_trab = NULL;
                                                        }

                                                        /*                                                         * *
                                                         * Sinesio luiz
                                                         * LANÇANDO INSALUBRIDADE
                                                         * 04/01/2017
                                                         */
                                                        $mov->setIdClt($id_clt);
                                                        $mov->setMes(16);
                                                        $mov->setAno($ano_demissao);
                                                        $mov->setIdRegiao($regiao);
                                                        $mov->setIdProjeto($idprojeto);
                                                        $mov->setIdMov(56);
                                                        $mov->setTipoQuantidade(2);
                                                        $mov->setQuantidade($diasInsa);
                                                        $mov->setCodMov(6006);
                                                        $mov->setLancadoPelaFolha(1);
                                                        $verifica = $mov->verificaInsereAtualizaFolha($valor_insalubridade);

                                                        if ($id_clt == 4564) {
                                                            $valor_insalubridade = 0;
                                                        }
                                                        if ($id_clt == 2676) {
                                                            $valor_insalubridade = 0; //46,93
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $valor_insalubridade = 123.20;
                                                        }

                                                        if ($id_clt == 4639) {
                                                            $valor_insalubridade = 5.87;
                                                        }


                                                        if ($id_clt == 4120) {
                                                            $valor_insalubridade = 134.93;
                                                        }

                                                        if ($id_clt == 4352) {
                                                            $valor_insalubridade = 106.19;
                                                        }

                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 07/02/2017
                                                         * A PEDIDO DO ÍTALO
                                                         */
                                                        if ($id_clt == 4276) {
                                                            $valor_insalubridade = 6.25;
                                                        }

                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 21/02/2017
                                                         * A PEDIDO DO ÍTALO
                                                         */
                                                        if ($id_clt == 4834) {
                                                            $valor_insalubridade = 124.93;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $lei_12_506 = 2672.48;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $valor_insalubridade = 12.49;
                                                        }
                                                        // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                        if ($id_clt == 4146) {
                                                            $valor_insalubridade = 12.49;
                                                        }
                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $valor_insalubridade = 18.74;
                                                        }
                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $valor_insalubridade = 18.74;
                                                        }

                                                        // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $valor_insalubridade = 31.17;
                                                        }

                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $valor_insalubridade = 37.48;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $valor_insalubridade = 37.48;
                                                        }

                                                        // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                        if ($id_clt == 4203) {
                                                            $valor_insalubridade = 93.70;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $valor_insalubridade = 43.73;
                                                        }

                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $valor_insalubridade = 43.73;
                                                        }

                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $valor_insalubridade = 49.97;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $valor_insalubridade = 156.17;
                                                        }
                                                        
                                                        // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                        if ($id_clt == 4160) {
                                                            $valor_insalubridade = 25.01;
                                                        }
                                                        
                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $valor_insalubridade = 31.23;
                                                        }
                                                        
                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $valor_insalubridade = 56.22;
                                                        }

                                                        //GAMBI
                                                        //$valor_aviso_previo = 0;
                                                        if ($id_clt == 24) {
                                                            $valor_aviso_previo = 2531.01;
                                                            $valor_ap_recebido_trab = 2531.01;
                                                        }
                                                        if ($id_clt == 250) {
                                                            $valor_aviso_previo = 2411.60;
                                                            $valor_ap_recebido_trab = 2411.60;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $valor_aviso_previo = 13362.39;
                                                            $valor_ap_recebido_trab = 13362.39;
                                                        }
                                                        if ($id_clt == 4352) {
                                                            $valor_aviso_previo = 5993.49;
                                                            $valor_ap_pago_trab = 5993.49;
                                                            //$valor_ap_recebido_trab = 5993.49;
                                                        }
                                                        if ($id_clt == 4163) {
                                                            $valor_aviso_previo = 13689.79;
                                                            //$valor_ap_pago_trab = 13689.79 ;
                                                            //$valor_ap_recebido_trab = 5993.49;
                                                        }

                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 07/02/2017
                                                         * A PEDIDO DO ÍTALO
                                                         */
                                                        if ($id_clt == 4276) {
                                                            $valor_ap_pago_trab = 5747.71;
                                                        }
                                                        
                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $valor_ap_pago_trab = 0;
                                                            $valor_aviso_previo = 0;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $valor_aviso_previo = 13689.79;
                                                            $valor_ap_recebido_trab = 13689.79;
                                                            $lei_12_506 = 5475.92;
                                                        }
                                                        // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                        if ($id_clt == 4146) {
                                                            $valor_aviso_previo = 13206.05;
                                                            $valor_ap_recebido_trab = 13206.05;
                                                            $lei_12_506 = 0;
                                                        }
                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $valor_aviso_previo = 3320.09;
                                                            $valor_ap_recebido_trab = 3320.09;
                                                            $lei_12_506 = 1328.04;
                                                        }
                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $valor_aviso_previo = 4030.95;
                                                            $valor_ap_recebido_trab = 4030.95;
                                                            $lei_12_506 = 1612.38;
                                                        }
                                                        // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $valor_aviso_previo = 3906.14;
                                                            $valor_ap_recebido_trab = 3906.14;
                                                            $lei_12_506 = 781.23;
                                                        }

                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $valor_aviso_previo = 5027.38;
                                                            $valor_ap_recebido_trab = 5027.38;
                                                            $lei_12_506 = 2010.95;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $valor_aviso_previo = 4617.22;
                                                            $valor_ap_recebido_trab = 4617.22;
                                                            $lei_12_506 = 1385.17;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $valor_aviso_previo = 6428.58;
                                                            $valor_ap_recebido_trab = 6428.58;
                                                            $lei_12_506 = 0;
                                                        }

                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $valor_aviso_previo = 6143.88;
                                                            $valor_ap_recebido_trab = 6143.88;
                                                            $lei_12_506 = 3071.94;
                                                        }

                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $valor_aviso_previo = 5066.48;
                                                            $valor_ap_recebido_trab = 5066.48;
                                                            $lei_12_506 = 1519.94;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $valor_aviso_previo = 4766.92;
                                                            $valor_ap_recebido_trab = 4766.92;
                                                            $lei_12_506 = 476.69;
                                                        }

                                                        // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                        if ($id_clt == 4160) {
                                                            $valor_aviso_previo = 10572.10;
                                                            $valor_ap_recebido_trab = 10572.10;
                                                            $lei_12_506 = 1057.21;
                                                        }
                                                        
                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $valor_aviso_previo = 0;
                                                            $valor_ap_recebido_trab = 0;
                                                            $lei_12_506 = 0;
                                                        }
                                                        
                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $valor_aviso_previo = 11120.60;
                                                            $valor_ap_recebido_trab = 11120.60;
                                                            $lei_12_506 = 0;
                                                        }

                                                        $to_descontos = $to_descontos + $valor_ap_pago_trab;
                                                        $to_rendimentos = $to_rendimentos + $valor_ap_recebido_trab + $total_valor_13_indenizado;

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
//                        echo "Valor Recebido Trabalhado: " . $valor_ap_recebido_trab . "<br>";
//                        echo "Total 13 Indenizado: " . $total_valor_13_indenizado . "<br>";
                                                        }

                                                        // Fim Aviso Prévio
                                                        // Atraso no Pagamento da Rescisão
                                                        $data_aviso_previo_1 = date('Y-m-d', strtotime("$data_demissao + 3 days"));
                                                        $data_aviso_previo_10 = date('Y-m-d', strtotime("$data_demissao + 10 days"));
                                                        $data_aviso_previo_24h = date('Y-m-d', strtotime("$data_demissao + 1 days"));

                                                        /* ANTES
                                                         *  ($data_hoje > $data_aviso_previo_1 and $dispensa == 66)  or
                                                          ($t_ap == 1 and $aviso == 'trabalhado') or
                                                          ($data_hoje > $data_aviso_previo_10 and $t_ap == 1 and $aviso == 'indenizado')
                                                         */

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre><br>===================================Multa 477=====================================================<br>";
                                                            echo "Data Hoje: " . $data_hoje . "<br>";
                                                            echo "Data Aviso 1: " . $data_aviso_previo_1 . "<br>";
                                                            echo "Data Aviso 10: " . $data_aviso_previo_10 . "<br>";
                                                            echo "Tipo Aviso Previo: " . $t_ap . "<br>";
                                                            echo "Aviso Previo: " . $aviso . "<br>";
                                                            echo "Dispensa : " . $dispensa . "<br>";
                                                            echo "=====================================================================================================<br></pre>";
                                                        }
                                                        //($data_hoje > $data_aviso_previo_1 and $dispensa == 66) || ($data_hoje >= $data_aviso_previo_10 and $dispensa == 65 and $aviso == 'indenizado') ||
                                                        /**
                                                         * FEITO POR : SINESIO LUIZ
                                                         * 01/08/2016
                                                         * JUNTO AO SABINO
                                                         * REMOVENDO A VERIFICAÇÃO DE TIPO DE DISPENSA
                                                         */
                                                        if (($data_hoje > $data_aviso_previo_24h and $dispensa == 66) || /*                                                                 * * */
                                                                ($data_hoje > $data_aviso_previo_10 and $aviso == 'indenizado' and $dispensa == 65 ) || /*                                                                 * ** */
                                                                ($data_hoje > $data_aviso_previo_10 && $t_ap == 1) ||
                                                                ($aviso == 'trabalhado' && $fator == "empregador" && $data_hoje >= $data_aviso_previo_1) ||
                                                                ($data_hoje > $data_aviso_previo_10 and $dispensa == 63 )) { /*                                                         * ** */
//                        echo "<br><br>Vasco<br><br>";
                                                            $valor_atraso = $salario_base_limpo;
                                                        }

                                                        if ($id_clt == 210) {
                                                            $valor_atraso = 0;
                                                        }

                                                        if ($id_clt == 2639) {
                                                            $valor_atraso = $salario_base_limpo;
                                                        }

                                                        if ($id_clt == 2784) {
                                                            $valor_atraso = 0;
                                                        }

                                                        /*
                                                         * leonardo
                                                         * 2017-01-16
                                                         * a pedido do italo
                                                         */
                                                        if ($id_clt == 4120) {
                                                            $valor_atraso = 0;
                                                        }
                                                        
                                                        if ($id_clt == 4878) {
                                                            $valor_atraso = 0;
                                                        }
                                                        if($id_clt == 4879){
                                                            $valor_atraso =0;
                                                        }
                                                        
                                                        // 3410 - MARIA DE NAZARE COSTA DOS SANTOS
                                                        if($id_clt == 3410){
                                                            $valor_atraso = 0;
                                                        }


                                                        /* by Ramon 05/07/2016
                                                         * ADICIONANDO MOVIMENTOS FUTUROS PARA PEGAR NA RESCISÃO
                                                         */
                                                        $array_movimentos_futuros = array(90043);

                                                        ///OUTROS MOVIMENTOS
                                                        $sql_get_movimentos = "SELECT A.*, IF(A.lancamento = 1, 'LANÇADO', '') AS tipo_lancamento
                                                FROM rh_movimentos_clt AS A
                                                WHERE 
                                                (
                                                        (A.mes_mov = '16' AND A.status = '1') 
                                                        OR 
                                                        (A.ano_mov >= {$ano_demissao} AND A.mes_mov >= {$mes_demissao} AND A.`status` = 1 AND A.cod_movimento IN (" . implode(',', $array_movimentos_futuros) . "))
                                                )

                                                AND A.id_mov NOT IN(56)
                                                AND A.id_clt = '{$id_clt}'
                                                AND A.ano_mov = YEAR(NOW())
                                                ORDER BY A.nome_movimento";

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<br>------------------QUERY DE MOVIMENTOS PARA CALCULO DE IRRF -----------------<br>";
                                                            echo $sql_get_movimentos;
                                                            echo "<br>----------------------------------------------------------------------------<br>";
                                                        }

                                                        $result_total_evento = mysql_query($sql_get_movimentos) or die(mysql_error()); ///O MOVIMENTO COM O CODIGO 292 É ADIANTAMENTO DE 13° NÃO PODENDO ENTRAR NOVAMENTES PARA DEDUÇÃO  AND id_mov NOT IN(292)
                                                        $total_result = mysql_num_rows($result_total_evento);


                                                        while ($row_total_evento = mysql_fetch_array($result_total_evento)) {

                                                            $cor = ($i++ % 2 == 0) ? '#eeeeee' : '#f4f4f4';

                                                            $movimentos[] = $row_total_evento['id_movimento'];  //usado para gravar os movimentos da rescisão na tabela rh_movimentos_recisao

                                                            if ($row_total_evento['tipo_movimento'] == 'CREDITO') {

                                                                $mov_cod[] = $row_total_evento['cod_movimento'];
                                                                $mov_nome[] = $row_total_evento['nome_movimento'];
                                                                $mov_tipo[] = $row_total_evento['tipo_lancamento'];
                                                                $mov_incidencia[] = ($row_total_evento['incidencia'] != ',,') ? 'INSS, IRRF, FGTS' : '';
                                                                $mov_rend[] = $row_total_evento['valor_movimento'];
                                                                $mov_desc[] = NULL;
                                                                $to_mov_rendimentos += $row_total_evento['valor_movimento'];

                                                                if ($row_total_evento['incidencia'] != ',,') {
                                                                    /*
                                                                     * leonardo
                                                                     * 2017-01-17
                                                                     * esse movimento so entra no 13
                                                                     */
                                                                    if ($row_total_evento['cod_movimento'] != 90031) {
                                                                        $total_mov_lancado += $row_total_evento['valor_movimento'];

                                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                                            echo "<br>------------------MOVIMENTOS CREDITO PARA CALCULO DE IRRF -----------------<br>";
                                                                            echo $row_total_evento['cod_movimento'] . '<br>';
                                                                            echo $row_total_evento['valor_movimento'];
                                                                            echo "<br>----------------------------------------------------------------------------<br>";
                                                                        }
                                                                    }
                                                                }
                                                            } elseif ($row_total_evento['tipo_movimento'] == 'DESCONTO' or $row_total_evento['tipo_movimento'] == 'DEBITO') {
                                                                $mov_cod[] = $row_total_evento['cod_movimento'];
                                                                $mov_incidencia[] = ($row_total_evento['incidencia'] != ',,') ? 'INSS, IRRF, FGTS' : '';
                                                                $mov_nome[] = $row_total_evento['nome_movimento'];
                                                                $mov_tipo[] = $row_total_evento['tipo_lancamento'];
                                                                $mov_desc[] = $row_total_evento['valor_movimento'];
                                                                $mov_rend[] = NULL;
                                                                $to_mov_descontos += $row_total_evento['valor_movimento'];
                                                                /*
                                                                 * leonardo
                                                                 * 2017-01-17
                                                                 * esse movimento so entra no 13
                                                                 */

                                                                echo 'leo aqui:<br>';
                                                                echo $total_mov_lancado . '<br>';
                                                                echo $row_total_evento['cod_movimento'] . '<br>';

                                                                if ($row_total_evento['cod_movimento'] != "90091" && $row_total_evento['cod_movimento'] != "90088") {

                                                                    if ($row_total_evento['incidencia'] != ',,') { //80030 && $row_total_evento['cod_movimento'] != 50249
                                                                        $total_mov_lancado -= $row_total_evento['valor_movimento'];

                                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                                            echo "<br>------------------MOVIMENTOS DEBITO PARA CALCULO DE IRRF -----------------<br>";
                                                                            echo $row_total_evento['cod_movimento'] . '<br>';
                                                                            echo $row_total_evento['valor_movimento'];
                                                                            echo "<br>----------------------------------------------------------------------------<br>";
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        ///////////////////////CONTRIBUIÇÃO SINDICAL///////////////////////
                                                        $mesDemissao = date("m", mktime(strtotime($data_demi)));
                                                        $anoDemissao = date("Y", mktime(strtotime($data_demi)));
                                                        if ($_REQUEST['recisao_coletiva'] == 1 && $mesDemissao == 3) {
                                                            $contribuicao_sindical = ($salario_base_limpo / 30);

                                                            $ano = $anoDemissao;
                                                            $verifica_mov = "SELECT * FROM rh_movimentos_clt WHERE mes_mov = '16' AND ano_mov = '{$ano}' AND cod_movimento = '5019' AND id_clt = '{$id_clt}' AND status = 1";
                                                            $sql_verifica_mov = mysql_query($verifica_mov);

                                                            $data_lancamento = date('Y-m-d');
                                                            //echo mysql_num_rows($sql_verifica_mov);
                                                            if (mysql_num_rows($sql_verifica_mov) == 0) {
                                                                $sql_insert_mov = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,id_folha,mes_mov,ano_mov,id_mov,
                                                        cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,lancamento,incidencia,
                                                        qnt,dt,status,status_folha,status_ferias,status_reg) VALUES ('{$id_clt}','{$row_clt['id_regiao']}','{$row_clt['id_projeto']}','0',
                                                        '16','{$ano}','21','5019','DEBITO','CONTRIBUIÇÃO SINDICAL','{$data_lancamento}','54','{$contribuicao_sindical}','1',',,',
                                                        '0','0','1','1','1','1')";

                                                                mysql_query($sql_insert_mov);

                                                                $mov_cod[] = "5019";
                                                                $mov_incidencia[] = '';
                                                                $mov_nome[] = "CONTRIBUIÇÃO SINDICAL";
                                                                $mov_tipo[] = "LANÇADO";
                                                                $mov_desc[] = $contribuicao_sindical;
                                                                $mov_rend[] = NULL;
                                                                $to_mov_descontos += $contribuicao_sindical;
                                                            }
                                                        }

                                                        ///////////////////////////////////////////////
                                                        ////////// CÁLCULO DE INSS E IRRF /////////////
                                                        ///////////////////////////////////////////////

                                                        $total_mov_lancado = number_format($total_mov_lancado, 2, '.', '');
                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>Saldo de Salário: " . $saldo_de_salario . "<br> Insalubridade: " . $valor_insalubridade . "<br> Movimentos lançados: " . $total_mov_lancado . "<br> Recebido Trabalhado: " . $valor_ap_recebido_trab . "<br> Lei: " . $lei_12_506 . "</pre>";
                                                            echo "******************MOVS***************<br>";
                                                            echo "<pre>";
                                                            print_r($mov_rend);
                                                            echo "</pre>";
                                                        }

                                                        /* if($total_mov_lancado < 0){
                                                          $total_mov_lancado = 0;
                                                          } */

                                                        /* By Ramon 19-09-2016 
                                                         * A pedido da Alana, vou remover o movimento 90030 pois o mesmo deve entrar para calculo do INSS
                                                         */

                                                        /* By Max 09/01/2017
                                                         * A pedido da Alana, vou remover o movimento 80044 pois o mesmo não deve entrar para base de IRRF
                                                         */

                                                        /* NOVOS MOVIMENTOS CRIADOS QUE NÃO DEVEM INCIDIR NO CALCULO DE INSS DO SALDO DE SALARIO VOU REMOVER AGORA */
                                                        // 90032 - Média de Férias
                                                        // 90033 - estava subtraindo esse valor mesmo sem ele ser somado lá em cima Leonardo em 2017-03-20
                                                        //$mov_nao_incide_saldo_salario = array("90032", "90033", "50249", "90057", "80045", "80044");
//                                                        $mov_nao_incide_saldo_salario = array("90032", "50249", "90057", "80045", "80044"); // removendo o 80044 por deu pau aqui.. só para teste 2017-04-10
                                                        $mov_nao_incide_saldo_salario = array("90032", "50249", "90057", "80045");
                                                        $total_mov_lancado_incide_ss = $total_mov_lancado;      //nova variavel para ser utilizada somente no Saldo de Salario

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo '----- movimentos ----- <br>';
                                                            print_array($mov_cod);
                                                            print_array($mov_rend);
                                                        }


                                                        //TEM Q SER FOR, POIS CRIARAM O ARRAY DE MOVIMENTOS SEM CHAVEAMENTO... PQP
                                                        for ($i = 0; $i <= count($mov_cod); $i++) {
                                                            if (in_array($mov_cod[$i], $mov_nao_incide_saldo_salario)) {
                                                                
                                                                $total_mov_lancado_incide_ss -= $mov_rend[$i];
                                                                if (in_array($_COOKIE['logado'], $programadores)) {
                                                                    echo "movimenstos fora media:  -{$mov_rend[$i]}<br>";
                                                                    echo "total_mov_lancado_incide_ss: {$total_mov_lancado_incide_ss}<br>";
                                                                }
                                                            }
                                                        }

                                                        /**
                                                         * By Ramon 30/08/2016
                                                         * Foi lançado 21 faltas para a funcionaria q só tem 2 dias de saldo de salario
                                                         * O sistema deve descontar as faltas de todos os movimentos laçados que incidem inss
                                                         * farei isso agora
                                                         * OBS: Como saldo de salario ja chega aqui zerado, somo o saldo de salario NOJO q está com o valor para exibição
                                                         * SOMO com os movimentos lançados q incidem INSS... para remover de uma só vez o valor de desconto de FALTAS
                                                         */
//                    if($faltas > 0 && $saldo_de_salario == 0){
//                        $total_base_inss_movs_saldo = $total_mov_lancado_incide_ss + $saldo_de_salario_nojo;
//                        $total_mov_lancado_incide_ss = $total_base_inss_movs_saldo - $faltas_lancadas_valor;
//                        $total_mov_lancado_incide_ss = ($total_mov_lancado_incide_ss<0)?0:$total_mov_lancado_incide_ss;
//                    }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>TOTAL DE MOVIMENTOS QUE INCIDEM INSS -> ***Depois: " . $total_mov_lancado_incide_ss . "</pre><br>";
                                                        }


                                                        /* By Ramon 11/07/2016
                                                         * A pedido da Alana adicioando AVISO PRÉVIO INDENIZADO PARA CALCULO DO INSS (SOMENTE INSS)
                                                         */

                                                        /**
                                                         * FEITO POR SINESIO 
                                                         * 27/07/2016
                                                         * REMOVENDO + $lei_12_506 A PEDIDO DA ALANA POR QUE 
                                                         * PRECISA FECHAR A RESCISAO ... NAO ME RESPONSABILIZO POR ESSA PORRA
                                                         */
                                                        /**
                                                         * RAMON 18/08/16
                                                         * VOLTANDO A $lei_12_506 A PEDIDO DO ITALO
                                                         * FODA-SE - esse tira e bota uma hora vai gozar
                                                         */
                                                        if ($faltas > 0) {
                                                            $saldo_de_salario = $saldo_de_salario_nojo;
                                                        }
//                    if($id_clt == 4487){
//                        $total_mov_lancado_incide_ss = '-318.93';
//                    }


                                                        $BASE_CALC_INSS_SALDO_SALARIO = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado_incide_ss + $valor_ap_recebido_trab + $lei_12_506;

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            print_array("base de calculo INSS/IRRF: $BASE_CALC_INSS_SALDO_SALARIO = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado_incide_ss + $valor_ap_recebido_trab + $lei_12_506");
                                                        }



                                                        /**
                                                         * MAX 18/08/16
                                                         * A PEDIDO DO SABINO
                                                         * ELE VERIFICOU NA INTERNET ESSA CONDIÇÃO
                                                         * 
                                                         * MAX 17/04/17
                                                         * A PEDIDO DA ALANA E DO ITALO REMOVENDO A LEI 12506
                                                         * DA BASE DE IRRF
                                                         */
                                                        if ($aviso == 'trabalhado') {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO_ = $BASE_CALC_INSS_SALDO_SALARIO;
                                                            
                                                            if($lei_12_506 > 0){
                                                                $BASE_CALC_IRRF_SALDO_SALARIO_ -= $lei_12_506;
                                                            }
                                                        } else {
                                                            /*
                                                             * 2017-03-14 - Leonardo
                                                             * subtraindo faltas da base de salario do inss
                                                             */
                                                            
                                                            if($_COOKIE['debug'] == 666){
                                                                echo "<pre><br>===================================== COMPOSIÇÂO VAR BASE_CALC_IRRF_SALDO_SALARIO_<br>";
                                                                echo "BASE_CALC_IRRF_SALDO_SALARIO_($BASE_CALC_IRRF_SALDO_SALARIO_): saldo_de_salario({$saldo_de_salario}) + valor_insalubridade({$valor_insalubridade}) + total_mov_lancado_incide_ss({$total_mov_lancado_incide_ss}) + valor_ap_recebido_trab({$valor_ap_recebido_trab})<br>";
                                                            }
                                                            
                                                            $BASE_CALC_IRRF_SALDO_SALARIO_ = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado_incide_ss + $valor_ap_recebido_trab;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre><br>===================================== BASE INSS E IRRF SALDO DE SALARIO<br>";
                                                            echo "<br>===================================== COMPOSIÇÃO DA VARIAVEI BASE_CALC_INSS_SALDO_SALARIO<br>";
                                                            echo "SALDO DE SALARIO: " . $saldo_de_salario . "<br>";
                                                            echo "INSALUBRIDADE: " . $valor_insalubridade . "<br>";
                                                            echo "MOVIMENTOS: " . $total_mov_lancado_incide_ss . "<br>";
                                                            echo "RECEBIDO TRABALHO: " . $valor_ap_recebido_trab . "<br>";
                                                            echo "LEI 12506 : " . $lei_12_506 . "<br>";
                                                            echo "(=) BASE IRRF: $BASE_CALC_INSS_SALDO_SALARIO<br>";
                                                            echo "TOTAL MOV LAN: $total_mov_lancado<br>";
                                                            echo "(») LINHA BASE INSS: $BASE_CALC_INSS_SALDO_SALARIO = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado_incide_ss + $valor_ap_recebido_trab + $lei_12_506;<br></pre>";
                                                        }

//                    if($id_clt == 4639){
//                       $BASE_CALC_INSS_SALDO_SALARIO  = 516.46;
//                    }

                                                        if ($BASE_CALC_INSS_SALDO_SALARIO > 0) {

                                                            $Calc->MostraINSS($BASE_CALC_INSS_SALDO_SALARIO, implode('-', $data_exp));

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>";
                                                                echo "Base INSS: " . $BASE_CALC_INSS_SALDO_SALARIO . "<br>";
                                                                echo "INSS OUTRA EMPRESA: " . $row_clt['desconto_inss'] . "<br>";
                                                                echo "VALOR OUTRA EMPRESA: " . $row_clt['desconto_outra_empresa'] . "<br>";
                                                                print_r($Calc);
                                                                echo "</pre>";
                                                            }

                                                            $inss_saldo_salario = $Calc->valor;
                                                            $PERCENTUAL_INSS_SS = $Calc->percentual;

                                                            $queryDescontoOutraEmpresaNovo = "SELECT * FROM rh_inss_outras_empresas AS A 
                                                                     WHERE A.id_clt = '{$id_clt}' AND '{$data_demissao}' 
                                                                     BETWEEN A.inicio AND A.fim AND A.status = 1";

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "CARTA INSS : $queryDescontoOutraEmpresaNovo";
                                                            }
                                                            
                                                            $sqlDescontoOutraEmpresaNovo = mysql_query($queryDescontoOutraEmpresaNovo);
                                                            $rowsDescontoOutraEmpresaNovo = mysql_fetch_assoc($sqlDescontoOutraEmpresaNovo);

                                                            if ($row_clt['desconto_inss'] == 1) {
                                                                if ($rowsDescontoOutraEmpresaNovo['desconto'] + $inss_saldo_salario > $Calc->teto) {
                                                                    $inss_saldo_salario = ($Calc->teto - $rowsDescontoOutraEmpresaNovo['desconto'] );
                                                                } else {
                                                                    $inss_saldo_salario = $inss_saldo_salario - $rowsDescontoOutraEmpresaNovo['desconto'];
                                                                }
                                                            }

                                                            if ($inss_saldo_salario < 0) {
                                                                $inss_saldo_salario = '0.00';
                                                            }
                                                        } else {
                                                            $BASE_CALC_INSS_SALDO_SALARIO = 0;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $BASE_CALC_INSS_SALDO_SALARIO = 23007.00;
                                                        }

                                                        /**
                                                         * By Ramon - 15/07/2016
                                                         * Adicionando para ZERAR O INSS caso ele seja ISENTO nessa empresa
                                                         */
//                    if($row_clt['tipo_desconto_inss'] == "isento"){
//                        $inss_saldo_salario = 0;
//                    }


                                                        if ($id_clt == 735) {
                                                            $inss_saldo_salario = 0;
                                                        }
                                                        if ($id_clt == 2717) {
                                                            $inss_saldo_salario = 449.08;
                                                        }
                                                        if ($id_clt == 3081) {
                                                            $inss_saldo_salario = 0;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                        if ($id_clt == 4146) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                        if ($id_clt == 4203) {
                                                            $inss_saldo_salario = 446.90;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $inss_saldo_salario = 608.44;
                                                        }

                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $inss_saldo_salario = 608.44;
                                                        }


                                                        /**
                                                         * Ramon 19-09-2016
                                                         * Remover alguns movimentos que entraram para calculo de INSS, que não devem entrar no calculo de IRRF
                                                         * (ALANA: aviso e média de aviso não entra pra desconto de IRRF)
                                                         */
                                                        $mov_nao_incide_IRRF_saldo_salario = array("90030");
                                                        $valor_movimentos_nao_incide_IRRF = 0;

                                                        //TEM Q SER FOR, POIS CRIARAM O ARRAY DE MOVIMENTOS SEM CHAVEAMENTO... PQP
                                                        for ($i = 0; $i <= count($mov_cod); $i++) {
                                                            if (in_array($mov_cod[$i], $mov_nao_incide_IRRF_saldo_salario)) {
                                                                $valor_movimentos_nao_incide_IRRF += $mov_rend[$i];
                                                            }
                                                        }


                                                        $BASE_CALC_IRRF_SS = $BASE_CALC_IRRF_SALDO_SALARIO_ - $valor_ap_recebido_trab - $valor_movimentos_nao_incide_IRRF;

                                                        $BASE_CALC_IRRF_SALDO_SALARIO = $BASE_CALC_IRRF_SS - $inss_saldo_salario; //+ $valor_ap_recebido_trab + $lei_12_506;

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>"; //akiHJ
                                                            echo "<br>===================================== COMPOSIÇÃO DA VARIAVEI BASE_CALC_IRRF_SALDO_SALARIO<br>";
                                                            echo "SALDO DE SALARIO: " . $saldo_de_salario . "<br>";
                                                            echo "INSALUBRIDADE: " . $valor_insalubridade . "<br>";
                                                            echo "TOT MOVIMENTOS INCIDE SS: " . $total_mov_lancado_incide_ss . "<br>";
                                                            echo "AVISO PREVIO RECEBIDO TRABALHO/INDE: " . $valor_ap_recebido_trab . "<br>";
                                                            echo "LEI 12506 : " . $lei_12_506 . "<br>";
                                                            echo "if($aviso == 'trabalhado'){ SE AVISO FOR INDENIZADO A LEI NÂO ENTRA - ($lei_12_506) <br>";
                                                            echo "=BASE INSS CALCULADO: $BASE_CALC_INSS_SALDO_SALARIO<br>";
                                                            echo "=BASE INSS CALCULADO_: $BASE_CALC_IRRF_SALDO_SALARIO_<br>";


                                                            echo "**** APÓS CALCULAR O INSS REMOVEMOS ALGUNS VALORES DESCRITOS ABAIXO *****<br>";
                                                            echo "- AVISO PREVIO RECEBIDO TRABALHO/INDE: " . $valor_ap_recebido_trab . "<br>";
                                                            echo "- MOVIMENTOS QUE NÃO INCIDEM IFFR: " . $valor_movimentos_nao_incide_IRRF . "<br>";
                                                            echo "- INSS: " . $inss_saldo_salario . "<br>";

                                                            echo "(=) BASE DO IRRF SALDO SALARIO: $BASE_CALC_IRRF_SALDO_SALARIO<br></pre>";
                                                        }

                                                        /* 03/06/2016
                                                         * MUITO BEM AGORA FERROU - NO DIA DO MEU NIVER... :(
                                                         * O IRRF É POR REGIME DE CAIXA E NÃO COMPETENCIA, PRECISO VERIFICAR SE A FOLHA DO MES PASSADO QUE JA ESTÁ FECHADA E FOI PAGA NO MES DA RESCISÃO
                                                         * PARA PEGAR A BASE, E O Q JÁ FOI DESCONTADO, PARA CALCULAR O Q RESTA A DESCONTAR AQUI NA RESCISÃO
                                                         */

                                                        $mesParaFolha = $mesDemissao - 1;
                                                        $foo = date("Y-m", mktime(strtotime($data_demi)));
                                                        $compFolha = date("Y-m", strtotime('-1 month', strtotime($foo)));
                                                        $sql_busca_folha_fechada = "SELECT 
                                                A.id_folha,A.mes,A.ano,
                                                B.id_clt,B.base_irrf, B.imprenda, B.t_imprenda
                                                FROM rh_folha AS A
                                                LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha AND B.id_clt = {$id_clt})
                                                WHERE CONCAT(A.ano,'-',A.mes) = '{$compFolha}' AND A.projeto = {$row_clt['id_projeto']} AND A.status = 3 AND A.terceiro = 2 LIMIT 1";
                                                        $result_folha_fechada = mysql_query($sql_busca_folha_fechada);
                                                        $row_folha_fechada = mysql_fetch_assoc($result_folha_fechada);

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>DEBUG NOW<br>";
                                                            echo "foo: {$foo}<br>";
                                                            echo "<pre>DEBUG NOW<br>";
                                                        }

                                                        //INSTANCIANDO VARIAVEL ZERADA
                                                        $BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO = 0;
                                                        if (isset($row_folha_fechada['id_clt']) && !empty($row_folha_fechada['id_clt'])) {

                                                            if ($_COOKIE['debug'] == "rescisao") {
                                                                echo "IF1_IRRF";
                                                            }

                                                            //COM O VALOR EM MÃOS, VAMOS AO CALCULO
                                                            //O VALOR DE BASE CALCULADO AGORA + O VALOR DA BASE CALCULADO NA FOLHA
                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>VERIFICANDO BASE DE IRRF SALDO DE SALARIO::: ";
                                                                print_r($BASE_CALC_IRRF_SALDO_SALARIO . "<br>");
                                                                print_r($row_folha_fechada['base_irrf']);
                                                                echo "</pre>";
                                                            }

                                                            /*
                                                             * 2017-03-29
                                                             * Leonardo
                                                             * Para casos em que não se deve usar a base de ir da folha
                                                             */
                                                            if ($_REQUEST['desconsiderar_ir_folha'] == 1) {
                                                                $xxx = "entrou if (desconsiderar 1 sim)";
                                                                $BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO = $BASE_CALC_IRRF_SALDO_SALARIO;
                                                            } else {
                                                                $xxx = "entrou else (desconsiderar 0 nao)";
                                                                $BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO = $BASE_CALC_IRRF_SALDO_SALARIO + $row_folha_fechada['base_irrf'];
                                                            }

                                                            /*
                                                             * 2017-03-29
                                                             * Leonardo
                                                             * Para casos em que não se deve usar a base de ir da folha, substituido pela linha de cima
                                                             */
//            $BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO = $BASE_CALC_IRRF_SALDO_SALARIO + $row_folha_fechada['base_irrf'];

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "MostraIRRF: {$BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO}, {$id_clt}, {$idprojeto}, {$data_demissao}... $xxx<br>";
                                                            }



                                                            $Calc->MostraIRRF($BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO, $id_clt, $idprojeto, $data_demissao);

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "Calc->valor: {$Calc->valor}<br>";
                                                                echo "imprenda: {$row_folha_fechada['imprenda']}<br>";
                                                                print_array($Calc->MostraIRRF);
                                                            }

                                                            /*
                                                             * 2017-03-29
                                                             * Leonardo
                                                             * Para casos em que não se deve usar a base de ir da folha
                                                             */
                                                            if ($_REQUEST['desconsiderar_ir_folha'] == 1) {
                                                                $irrf_saldo_salario = $Calc->valor;
                                                            } else {
                                                                $irrf_saldo_salario = $Calc->valor - $row_folha_fechada['imprenda'];
                                                            }



                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>VERIFICANDO BASE DE IRRF NA FOLHA::: ";
                                                                print_r($row_folha_fechada);
                                                                echo '<br>';
                                                                print_r($irrf_saldo_salario);
                                                                echo '<br>';
                                                                echo $Calc->valor;
                                                                echo "</pre>";
                                                            }
                                                        } else {

                                                            if ($_COOKIE['debug'] == "rescisao") {
                                                                echo "IF2_IRRF";
                                                            }

                                                            //SE NÃO RETORNAR A ULTIMA FOLHA VAI FAZER NORMAL
                                                            $Calc->MostraIRRF($BASE_CALC_IRRF_SALDO_SALARIO, $id_clt, $idprojeto, $data_demissao);
                                                            $irrf_saldo_salario = $Calc->valor;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            //echo $BASE_CALC_IRRF_SS . " - " .  $inss_saldo_salario . "<br>";
                                                        }

                                                        if ($id_clt == 4665) {
                                                            $irrf_saldo_salario = 1.29;
                                                        }

                                                        if ($id_clt == 4654) {
                                                            $irrf_saldo_salario = 112.16;
                                                        }

                                                        if ($id_clt == 2717) {
                                                            $irrf_saldo_salario = 98.05;
                                                        }

                                                        if ($id_clt == 3081) {
                                                            $irrf_saldo_salario = 990.78;
                                                        }


                                                        if ($id_clt == 1045) {
                                                            $irrf_saldo_salario = 169.38;
                                                        }

                                                        if ($id_clt == 4305) {
                                                            $irrf_saldo_salario = 3313.62;
                                                        }

                                                        if ($id_clt == 804) {
                                                            $irrf_saldo_salario = 39.69;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $irrf_saldo_salario = 5133.99;
                                                        }

                                                        if ($id_clt == 2784) {
                                                            $irrf_saldo_salario = 654.52;
                                                        }

                                                        if ($id_clt == 3352) {
                                                            $irrf_saldo_salario = 955.65;
                                                        }

                                                        if ($id_clt == 4788) {
                                                            $irrf_saldo_salario = 35.39;
                                                        }

                                                        if ($id_clt == 3352) {
                                                            $irrf_saldo_salario = 1112.64;
                                                        }

                                                        if ($id_clt == 2166) {
                                                            $irrf_saldo_salario = 108.74;
                                                        }

                                                        if ($id_clt == 4781) {
                                                            $irrf_saldo_salario = 9.07;
                                                        }

                                                        if ($id_clt == 4804) {
                                                            $irrf_saldo_salario = 0;
                                                        }

                                                        if ($id_clt == 4855) {
                                                            $irrf_saldo_salario = 1854.28;
                                                        }
                                                        if ($id_clt == 4168) {
                                                            $irrf_saldo_salario = 35.5184;
                                                        }

                                                        /*
                                                         * 2017-03-09 - Leonardo
                                                         * Rescisao da 4163 - GISELE MARIA AMARAL
                                                         * A pedido da Alana pois o sistema nao calcula rescisao de plantonista
                                                         */
                                                        if ($id_clt == 4163) {
                                                            $irrf_saldo_salario = 2290.24;
                                                        }
                                                        // 4146  - BRUNA FERNANDA FERNANDES FERREIRA
                                                        if ($id_clt == 4146) {
                                                            $irrf_saldo_salario = 2007.79;
                                                        }
                                                        // 4161 - GABRIELA HAE YOUNG OH
                                                        if ($id_clt == 4161) {
                                                            $irrf_saldo_salario = 223.14;
                                                        }
                                                        // 4243 - JOSE DONIZETI COSTA JUNIOR
                                                        if ($id_clt == 4243) {
                                                            $irrf_saldo_salario = 250.39;
                                                        }

                                                        // 4261 - SUHEYLA POLYANA PEREIRA RIBEIRO
                                                        if ($id_clt == 4261) {
                                                            $irrf_saldo_salario = 15.42;
                                                        }

                                                        // 4251 - MARIA CRISTINA NUNEZ SEIWALD
                                                        if ($id_clt == 4251) {
                                                            $irrf_saldo_salario = 786.54;
                                                        }

                                                        // 4244 - JOSE MARCOS THALENBERG
                                                        if ($id_clt == 4244) {
                                                            $irrf_saldo_salario = 1457.29;
                                                        }

                                                        // 4203 - MAIRA MACIEL CAMPOMIZZI
                                                        if ($id_clt == 4203) {
                                                            $irrf_saldo_salario = 994.35;
                                                        }

                                                        // 4159 - FRANCISCO DA SILVA GONINI
                                                        if ($id_clt == 4159) {
                                                            $irrf_saldo_salario = 1458.32;
                                                        }

                                                        // 4148 - CAMILA ANGELO ROSA
                                                        if ($id_clt == 4148) {
                                                            $irrf_saldo_salario = 921.72;
                                                        }

                                                        // 4156 - EDUARDO CARDOSO PEREIRA
                                                        if ($id_clt == 4156) {
                                                            $irrf_saldo_salario = 1026.34;
                                                        }

                                                        // 4255 - PAULO SNG MAN YOO
                                                        if ($id_clt == 4255) {
                                                            $irrf_saldo_salario = 26.60;
                                                        }

                                                        // 4160 - GABRIEL HENRIQUE RESEGUE ANGELIERI
                                                        if ($id_clt == 4160) {
                                                            $irrf_saldo_salario = 318.11;
                                                        }

                                                        // 4295 - MARIA LAURA MARIANO DE MATOS
                                                        if ($id_clt == 4295) {
                                                            $irrf_saldo_salario = 1351.33;
                                                        }

                                                        // 4196 - KELEN RAMOS OVIL
                                                        if ($id_clt == 4196) {
                                                            $irrf_saldo_salario = 1978.93;
                                                        }


                                                        if ($irrf_saldo_salario < 0) {
                                                            $irrf_saldo_salario = 0;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>";
                                                            echo "Jacques/Ramon/Sinésio</br>";
                                                            echo "Base Calculo RESCISÃO: {$BASE_CALC_IRRF_SALDO_SALARIO}</br>";
                                                            echo "Base Calculo FOLHA: {$row_folha_fechada['base_irrf']}</br>";
                                                            echo "Base Calculo Acumulado: {$BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO}</br>";
                                                            echo "IR NA FOLHA: {$row_folha_fechada['imprenda']}</br>";
                                                            echo "IF DA RESCISAO: {$Calc->valor}</br>";
                                                            echo "IF +++++++++: {$sql_busca_folha_fechada}</br>";
                                                            //print_r($Calc);
                                                            echo "</pre>";
                                                        }

                                                        if ($irrf_saldo_salario > 0) {
                                                            $PERCENTUAL_IRRF_SS = $Calc->percentual;
                                                            $QNT_DEPENDENTES_IRRF_SS = $Calc->total_filhos_menor_21;
                                                            $VALOR_DDIR_SS = $Calc->valor_deducao_ir_total;
                                                            $PARCELA_DEDUCAO_IR_SS = $Calc->valor_fixo_ir;
                                                        } else {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 0;
                                                        }

                                                        $inss_saldo_salario = number_format($inss_saldo_salario, 2, '.', '');
                                                        $irrf_saldo_salario = number_format($irrf_saldo_salario, 2, '.', '');


                                                        if ($BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO > 0) {
                                                            //igualando para gravar a base correta no BF... para montar a DIRF do ano seguinte
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = $BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO;
                                                        }

                                                        if ($id_clt == 4264) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 42041.13;
                                                        }

                                                        if ($id_clt == 3352) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 18356.94;
                                                        }

                                                        if ($id_clt == 4788) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 2375.89;
                                                        }

                                                        if ($id_clt == 3352) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 18927.82;
                                                        }

                                                        if ($id_clt == 2166) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 3394.49;
                                                        }
                                                        if ($id_clt == 4781) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 2024.91;
                                                        }
                                                        if ($id_clt == 4804) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 1533.47;
                                                        }
                                                        if ($id_clt == 4855) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 10283.34;
                                                        }
                                                        if ($id_clt == 4168) {
                                                            $BASE_CALC_IRRF_SALDO_SALARIO = 172.0384 + 3512.256;
                                                        }
//////////////
////TOTAIS ///
/////////////       
//                    echo "TOTAL_SALDO_SALARIO = $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario";

                                                        /* if($id_clt == 1005 ){
                                                          $fp_valor_total = 8.87;
                                                          $fp_um_terco = 2.95;
                                                          } */

                                                        $TOTAL_SALDO_SALARIO = $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario;
                                                        $TOTAL_DECIMO_PROPORCIONAL = ($valor_td + $valor_13_indenizado) - $valor_td_inss - $valor_td_irrf - $valor_decimo_folha - $valorAdiantamento;
                                                        $TOTAL_FERIAS = $fv_valor_base + $fv_um_terco + $fp_valor_total + $fp_um_terco + $multa_fv + $fv_um_terco_dobro + $ferias_aviso_indenizado + $umterco_ferias_aviso_indenizado;
                                                        $TOTAL_OUTROS_VENCIMENTOS = $valor_sal_familia + $valor_atraso + $valor_outro + $valor_ap_recebido_trab + $valor_insalubridade + $lei_12_506;
                                                        $TOTAL_OUTROS_DESCONTOS = $total_outros_descontos;

                                                        if ($id_clt == 239) {
                                                            $art_480 = 0;
                                                        }

                                                        //$TOTAL_OUTROS_VENCIMENTOS = 0;

                                                        
                                                        // 2017-05-05 Leonardo - Removi $valorAdiantamento de 13 dos descontos pois este já está sendo descontado no total_decimo_proporcional
//                                                        $to_descontos = $inss_saldo_salario + $irrf_saldo_salario + $valor_td_inss + $valor_td_irrf + $total_outros_descontos + $art_480 + $valor_ap_pago_trab + $to_mov_descontos + $valor_decimo_folha + $valorAdiantamento; //REMOVI VARIAVEL FALTA ($valor_faltas)
                                                        $to_descontos = $inss_saldo_salario + $irrf_saldo_salario + $valor_td_inss + $valor_td_irrf + $total_outros_descontos + $art_480 + $valor_ap_pago_trab + $to_mov_descontos + $valor_decimo_folha ; //REMOVI VARIAVEL FALTA ($valor_faltas)

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "outros vencimentos:<br>";
                                                            echo "valor_sal_familia:$valor_sal_familia <br> valor_atraso:$valor_atraso <br> valor_outro:$valor_outro <br> valor_ap_recebido_trab:$valor_ap_recebido_trab <br> valor_insalubridade:$valor_insalubridade <br> lei_12_506:$lei_12_506";
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo '*********************Composição da variavel desconto******************<br>';
                                                            echo "Saldo de Salario: " . $inss_saldo_salario . "<br>";
                                                            echo "IRRF Saldo de salario : " . $irrf_saldo_salario . "<br>";
                                                            echo "INSS DT: " . $valor_td_inss . "<br>";
                                                            echo "IR Dt: " . $valor_td_irrf . "<br>";
                                                            echo "Outros Descontos: " . $total_outros_descontos . "<br>";
                                                            echo "480: " . $art_480 . "<br>";
                                                            echo "Aviso Previo: " . $valor_ap_pago_trab . "<br>";
                                                            echo "Total Movimento desconto: " . $to_mov_descontos . "<br>";
                                                            echo "Decimo Folha" . $valor_decimo_folha . "<br>";
                                                            echo "Adiantamento: " . $valorAdiantamento . "<br>";
                                                            echo '***********************************************<br>';
                                                        }

                                                        if ($id_clt == 4639) {
                                                            $to_descontos = 615.46;
                                                        }

                                                        if ($faltas > 0) {
                                                            $to_rendimentos = round($saldo_de_salario_nojo, 2) + round($valor_td, 2) + round($media_13, 2) + round($valor_13_indenizado, 2) + round($TOTAL_FERIAS, 2) + round($TOTAL_OUTROS_VENCIMENTOS, 2) + round($to_mov_rendimentos, 2) + round($art_479, 2);
                                                        } else {
                                                            $to_rendimentos = round($saldo_de_salario, 2) + round($valor_td, 2) + round($media_13, 2) + round($valor_13_indenizado, 2) + round($TOTAL_FERIAS, 2) + round($TOTAL_OUTROS_VENCIMENTOS, 2) + round($to_mov_rendimentos, 2) + round($art_479, 2);
                                                        }

                                                        if ($id_clt == 2489) {
                                                            $to_rendimentos += 0.01;
                                                        }

                                                        // 4146 - BRUNA FERNANDA FERNANDES FERREIRA
                                                        if ($id_clt == 4146) {
                                                            $to_rendimentos += 0.01;
                                                        }

                                                        if ($id_clt == 1954) {
                                                            $to_rendimentos = 6461.73;
                                                        }

                                                        if ($id_clt == 4168) { // isso é só por causa de um centavo
                                                            $to_rendimentos = 14053.65;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {

                                                            echo "<pre>====================================Composição do DESCONTOS======================================== <br>";
                                                            echo "(+) Valor Faltas: " . formato_real($valor_faltas) . "<br>";
                                                            echo "(+) INSS sobre saldo de salario: " . formato_real($inss_saldo_salario) . "<br>";
                                                            echo "(+) IRRF sobre Saldo de Salário: " . formato_real($irrf_saldo_salario) . "<br>";
                                                            echo "(+) INSS sobre 13°: " . formato_real($valor_td_inss) . "<br>";
                                                            echo "(+) IRRF sobre 13°: " . formato_real($valor_td_irrf) . "<br>";
                                                            echo "(+) Tottal descontos: " . formato_real($total_outros_descontos) . "<br>";
                                                            echo "(+) Art 480: " . formato_real($art_480) . "<br>";
                                                            echo "(+) Aviso Previo trabalhado: " . formato_real($valor_ap_pago_trab) . "<br>";
                                                            echo "(+) Total Movimentos descontos: " . formato_real($to_mov_descontos) . "<br>";
                                                            echo "(+) Adiantamento de Decimo terceiro: " . formato_real($valor_decimo_folha) . "<br>";
                                                            echo "(=) Total: " . formato_real($to_descontos) . "<br>";
                                                            echo "================================================================================================== <br><br></pre>";

                                                            echo "<pre>====================================Composição do RENDIMENTOS====================================== <br>";
                                                            echo "(+) Saldo de Salário: " . formato_real($saldo_de_salario) . "<br>";
                                                            echo "(+) Valor td: " . formato_real($valor_td) . "<br>";
                                                            echo "(+) Aviso Prévio: " . formato_real($valor_ap_recebido_trab) . "<br>";
                                                            echo "(+) 13 indenizado: " . formato_real($valor_13_indenizado) . "<br>";
                                                            echo "(+) Total férias: " . formato_real($TOTAL_FERIAS) . "<br>";
                                                            echo "(+) Outros Vencimentos: " . formato_real($TOTAL_OUTROS_VENCIMENTOS) . "<br>";
                                                            echo "(+) Total movimentos rendimentos:  " . formato_real($to_mov_rendimentos) . "<br>";
                                                            echo "(+) Art 479: " . formato_real($art_479) . "<br>";
                                                            echo "(+) Lei 12.506: " . formato_real($lei_12_506) . "<br>";
                                                            echo "(=) Total: " . formato_real($to_rendimentos) . "<br>";
                                                            echo "================================================================================================== <br><br></pre>";
                                                        }

                                                        //////////////////////
                                                        ///VALOR FINAL//////
                                                        /////////////////////

                                                        $valor_rescisao_final = $to_rendimentos - $to_descontos;

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>====================================VALOR FINAL====================================== <br>";
                                                            echo "??? Dispensa: {$dispensa}<br>";
                                                            echo "(+) REAL REND MOJO: " . formato_real($to_rendimentos_nojo) . "<br>";
                                                            echo "(+) Total Rend: " . formato_real($to_rendimentos) . "<br>";
                                                            echo "(+) Total Desc: " . formato_real($to_descontos) . "<br>";
                                                            echo "(=) valor_rescisao_final: " . formato_real($valor_rescisao_final) . "<br></pre>";
                                                        }

                                                        if ($valor_rescisao_final < 0) {

                                                            $arredondamento_positivo = abs($valor_rescisao_final);

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>(=) Arredondamento Positivo 1:" . formato_real($arredondamento_positivo) . "<br></pre>";
                                                            }

                                                            if ($dispensa == 60) {
                                                                $valor_rescisao_final = $aviso_previo_valor_d;
                                                            } else {
                                                                $valor_rescisao_final = NULL;
                                                            }

                                                            $to_rendimentos = $to_rendimentos + $arredondamento_positivo;
                                                            $valor_rescisao_final = NULL;

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>(=) Total Rendimentos alterado:" . formato_real($to_rendimentos) . "<br></pre>";
                                                            }

                                                            if ($faltas > 0) {
                                                                //$arredondamento_positivo -= $saldo_de_salario_nojo;
                                                            }

                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                echo "<pre>(=) SALDO DE SALARIO NOJO:" . formato_real($saldo_de_salario_nojo) . "<br>";
                                                                echo "(=) SALDO DE SALARIO:" . formato_real($saldo_de_salario) . "<br>";
                                                                echo "(=) Arredondamento Positivo 2 (dps das faltas):" . formato_real($arredondamento_positivo) . "<br></pre>";
                                                            }

                                                            if ($arredondamento_positivo < 0) {
                                                                $arredondamento_positivo = 0;
                                                            }

                                                            /*
                                                             * REMOVENDO ESSA CONSULTA A PEDIDO DO ITALO QUE 
                                                             * A 1 DIA ATRAS PEDIO PARA CRIAR 
                                                             * ESSA MESMA CONSULTA
                                                             */
//                        $query = mysql_query("select A.valor_movimento FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$id_clt}' AND A.`status` = 1 AND A.id_mov IN(367,204,263)");
                                                            $valorNaoDEsconta = 0;
//                        while($linhaNaoDesconta = mysql_fetch_assoc($query)){
//                            $valorNaoDEsconta += $linhaNaoDesconta['valor_movimento'];
//                        }

                                                            $valor_rescisao_final = $valorNaoDEsconta;
                                                        } else {
                                                            $arredondamento_positivo = NULL;
                                                            $valor_rescisao_final = $to_rendimentos - $to_descontos;
                                                        }

                                                        if (in_array($_COOKIE['logado'], $programadores)) {
                                                            echo "<pre>(=) Valor Rescisao Final: " . formato_real($valor_rescisao_final) . "<br></pre>";
                                                        }

                                                        echo "</div>";
                                                        ?>

                                                        <form action="acao.php" method="post" name="Form" id="Form">
                                                            <input type="hidden" name="recisao_coletiva" id="recisao_coletiva" value="<?php echo $_REQUEST['recisao_coletiva']; ?>" />
                                                            <table cellpadding="0" cellspacing="0" style="background-color:#FFF; margin:0px auto; width:80%; border:0; line-height:24px;">
                                                                <tr>
                                                                    <td colspan="4" class="show" align="center" style="display:table-cell !important;"><a href="../../rh_novaintra/recisao/nova_rescisao.php" class="btn btn-info btn-xs">voltar</a><br/><?= $id_clt . ' - ' . $nome ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="25%" class="secao">Data de Admiss&atilde;o:</td>
                                                                    <td width="25%"><?= $data_entradaF ?></td>
                                                                    <td width="25%" class="secao">Data de Demiss&atilde;o:</td>
                                                                    <td width="25%"><?= $data_demissaoF ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Motivo do Afastamento:</td>
                                                                    <td><?= $row_status['especifica'] ?></td>
                                                                    <td class="secao">Salario base de c&aacute;lculo:</td>
                                                                    <td>R$ <?= number_format(($salario_base_limpo), 2, ',', '.') ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2"></td>
                                                                    <td class="secao">Média dos movimentos fixos:</td>
                                                                    <td>R$ <?= formato_real($total_rendi) ?> 
                                                                        <a href="action.ver_rendimentos_1.php?clt=<?php echo $id_clt; ?>&m_trab=<?php echo $row_clt['meses_trabalhados']; ?>" id="ver_rend" onClick="return hs.htmlExpand(this, {objectType: 'iframe', width: 400, height: 300})" title="Média dos movimentos" class="btn btn-info btn-sm">
                                                                            <i class="fa fa-search"></i>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Fator:</td>
                                                                    <td><?= $fator ?></td>
                                                                    <td class="secao">Aviso pr&eacute;vio:</td>
                                                                    <td><?= $aviso; ?></td>
                                                                </tr>
                                                                <tr style="font-weight:bold;">
                                                                    <td colspan="2" align="center">
                                                                        <p>RENDIMENTOS:<a href="javascript:;" class="detalhe_rendimentos link_pers"> R$ <?= formato_real($to_rendimentos); ?></a></p>
                                                                        <p>DESCONTOS: <a href="javascript:;" class="detalhe_descontos link_pers">R$ <?= formato_real($to_descontos) ?></a></p>
                                                                    </td>
                                                                    <td colspan="2" style="font-size:14px; text-align:center;">
                                                                        Total a ser pago: <?= formato_real($valor_rescisao_final) ?><br />
        <?php
        if (!empty($arredondamento_positivo)) {
            echo 'Arredondamento Positivo: ' . formato_real($arredondamento_positivo) . '';
        }
        ?>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td colspan="4" class="divisor">Sal&aacute;rios</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">
        <?php
        if (($id_curso == 6894) || ($id_curso == 6580)) {
            $dias_trabalhados = 30;
        }
        ?>
                                                                        <?php //if($id_clt == 3355){$dias_trabalhados_nojo = 22;}  ?>
                                                                        <?php
                                                                        if ($faltas > 0) {
                                                                            echo "Saldo de sal&aacute;rio ($dias_trabalhados_nojo / $qnt_dias_mes)";
                                                                        } else {
                                                                            ?>
                                                                            Saldo de sal&aacute;rio <?php if (($row_clt['id_curso'] != 6580) && ($row_clt['id_curso'] != 6894)) {
                                                                    echo "(" . $dias_trabalhados
                                                                                ?>/<?php echo $qnt_dias_mes . "): ";
                                                            }
                                                                            ?>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td>
        <?php //if($id_clt == 1005){ $saldo_de_salario = 738.70; }  ?>
                                                                        <a href="javascript:;" class="detalhe_saldo_salario link_pers">
                                                                        <?php
                                                                        if ($faltas > 0) {
                                                                            echo "R$ " . formato_real($saldo_de_salario_nojo);
                                                                        } else {
                                                                            ?>
                                                                                R$ <?php echo formato_real($saldo_de_salario); ?> 
                                                                            <?php } ?>
                                                                        </a>        
                                                                            <?php
                                                                            if (!empty($faltas)) {
                                                                                echo '<a href="javascript:;" class="detalhe_faltas link_pers">(' . $faltas . ' faltas)</a>';
                                                                            }
                                                                            ?>
                                                                    </td>
                                                                    <td class="secao">INSS:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_inss link_pers">
                                                                            R$ 
        <?php echo formato_real($inss_saldo_salario); ?> 
                                                                        </a>

        <?php
        if ($inss_saldo_salario > 0) {
            echo " <small>(base " . formato_real($BASE_CALC_INSS_SALDO_SALARIO) . ")</small>";
        }

        if ($row_clt['desconto_inss'] == 1 && $row_clt['tipo_desconto_inss'] == "isento") {
            //                                            echo ' <strong style="color:red;">**ISENTO DE INSS</strong>';
        }
        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>    
                                                                    <td colspan="2" align="center">  <?php
                                                                if ($row_clt['desconto_inss'] == 1 && $row_clt['desconto_outra_empresa'] > 0) {
                                                                    echo '<br><strong>**Possui desconto de INSS em outra empresa</strong>';
                                                                    echo '<br><strong>Salário na outra empresa: </strong> R$ ' . formato_real($row_clt['salario_outra_empresa']);
                                                                    echo '<br><strong>INSS na outra empresa: </strong> R$ ' . formato_real($row_clt['desconto_outra_empresa']);
                                                                }
        ?> </td>

                                                                    <td class="secao">IRRF:</td>
                                                                    <td colspan="3">
                                                                        <a href="javascript:;" class="detalhe_ir link_pers">
                                                                            R$ <?php echo formato_real($irrf_saldo_salario); ?>
                                                                        </a>

        <?php
        //                                    if($id_clt == 4639){
        //                                        $irrf_saldo_salario = 0;
        //                                    }

        if ($irrf_saldo_salario > 0) {
            echo " <small> (base " . formato_real($BASE_CALC_IRRF_SALDO_SALARIO) . ")";
            if ($BASE_CALC_IRRF_SALDO_SALARIO_ACUMULADO > 0) {
                //echo "**acumulado";
            }
            echo "</small>";
        }
        ?>

                                                                    </td>        
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" align="center">
                                                                        <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_SALDO_SALARIO) ?></span>
                                                                    </td>
                                                                </tr>
        <?php if ($total_result != 0) { ?>
                                                                    <tr>
                                                                        <td colspan="4" class="divisor">Outros eventos</td>
                                                                    </tr> 
                                                                    <tr>
                                                                        <td colspan="4">


                                                                            <table width="100%" border="0" id="movimentos">
                                                                                <thead>
                                                                                    <tr style="background-color:  #f2f1f1;">
                                                                                        <td>COD</td>
                                                                                        <td align="left">NOME</td>     
                                                                                        <td align="center">INCIDÊNCIA</td>
                                                                                        <td align="center"> TIPO </td>
                                                                                        <td align="right">RENDIMENTO</td>                     
                                                                                        <td align="right">DESCONTO</td>                     
                                                                                    </tr>
                                                                                </thead>

            <?php foreach ($mov_cod as $chave => $cod) { ?>

                                                                                    <tr style="background-color:<?php echo $cor; ?>">
                                                                                        <td align="center"><?php echo $cod; ?></td>
                                                                                        <td align="left"><?php echo $mov_nome[$chave]; ?></td>
                                                                                        <td align="center"><?php echo $mov_incidencia[$chave]; ?></td>
                                                                                        <td align="center"><?php echo $mov_tipo[$chave]; ?></td>
                                                                                        <td align="right"> <?php echo (!empty($mov_rend[$chave])) ? '' . formato_real($mov_rend[$chave]) : ''; ?></td>
                                                                                        <td align="right"> <?php echo (!empty($mov_desc[$chave])) ? '' . formato_real($mov_desc[$chave]) : ''; ?></td>
                                                                                    </tr>                    
            <?php } ?>
                                                                                <tr style="font-weight:bold;">
                                                                                    <td colspan="4" align="right">TOTAIS:</td>
                                                                                    <td align="right"><?php echo formato_real($to_mov_rendimentos); ?></td>
                                                                                    <td align="right"><?php echo formato_real($to_mov_descontos); ?></td>
                                                                                </tr> 
                                                                                <tr><td colspan="6">&nbsp;</td></tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
        <?php } ?>
                                                                <tr>
                                                                    <td colspan="4" class="divisor">Décimo terceiro</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Décimo terceiro proporcional <?php
        if ($meses_ativo_dt > 12) {
            echo "";
        } else {
            echo "(" . $meses_ativo_dt . "/12)";
        }
        ?>:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_valor_dt link_pers">
                                                                            R$ <?php echo formato_real($valor_td) ?>
                                                                        </a>
                                                                        <?php
                                                                        if ($total_rendi_proporcional_dt > 0) {
                                                                            echo " <small> (média de 13º = R$ " . number_format($total_rendi_proporcional_dt, 2, ",", ".") . ")</small>";
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td class="secao">13&ordm; Saldo Indenizado 
                                                                        <?php
                                                                        if ($total_avos_13_indenizado > 12) {
                                                                            echo "";
                                                                        } else {
                                                                            echo "(" . $total_avos_13_indenizado . "/12)";
                                                                        }
                                                                        ?>:
                                                                    </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_valor_dt_indenizado link_pers">
                                                                            R$ <?= formato_real($total_valor_13_indenizado); ?>
                                                                        </a>
                                                                    </td>       
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">INSS:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_valor_inss_dt link_pers">
                                                                            R$ <?php echo formato_real($valor_td_inss); ?>
                                                                        </a>
                                                                        <small>(base: <?php echo formato_real($BASE_CALC_INSS_13) ?>)</small>
                                                                    </td>
                                                                    <td class="secao">IRRF:</td>
                                                                    <td colspan="3">
                                                                        <a href="javascript:;" class="detalhe_valor_irrf_dt link_pers">
                                                                            <?php
                                                                            if ($id_clt == 34) {
                                                                                $valor_td_irrf = 410.85;
                                                                            }
                                                                            ?>
                                                                            R$ <?php echo number_format($valor_td_irrf, 2, ',', '.') ?>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <!--tr>
                                                                    <td class="secao">Média:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_valor_inss_dt link_pers">
                                                                            R$ <?php echo formato_real($media_td); ?>
                                                                        </a>
                                                                    </td>                                
                                                                </tr-->
                                                                <tr>
                                                                    <?php $valor_decimo_folha = 0;
                                                                    if (!empty($valor_decimo_folha)) {
                                                                        ?>
                                                                        <td class="secao">Desconto Adiantamento 13°</td>
                                                                        <td><?php echo "R$ " . formato_real($valor_decimo_folha); ?></td>
        <?php } ?>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" align="center">
                                                                        <span style="font-size:14px; font-weight:bold;"></span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" align="left"><div class="divisor">Férias</div></td>
                                                                </tr>
                                                                <tr <?= $style_fv ?>>
                                                                    <td class="secao">Férias vencidas: 
        <?php
        if (!empty($periodo_venc_inicio) && !empty($periodo_venc_final)) {
            echo "<br />(" . date("d/m/Y", strtotime(str_replace("/", "-", $periodo_venc_inicio))) . ' à ' . date("d/m/Y", strtotime(str_replace("/", "-", $periodo_venc_final))) . ")";
        }
        ?>
                                                                    </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_ferias_vencidas link_pers">
                                                                            R$ 
                                                                            <?php
                                                                            /*
                                                                             * @jacques - 30/09/2015
                                                                             * 
                                                                             * Caso as férias proporcionais sejam de 12/12 avos, então as (férias proporcionais )deverão
                                                                             * ser consideradas vencidas.
                                                                             * 
                                                                             * Obs: Essa operação afeta apenas a exibição dos valores no formulário de rescisão.
                                                                             */
//                                            if($meses_ativo_fp == 12){
//                                                
//                                                $meses_ativo_fp = 1;
//                                                
//                                                $fv_valor_base+=$fp_valor_total;
//                                                $fv_um_terco += $fp_um_terco;
//                                                
//                                                $fp_valor_total = 0;
//                                                $fp_um_terco = 0;
//                                                
//                                            }

                                                                            echo formato_real($fv_valor_base);
                                                                            ?>
                                                                        </a>
                                                                    </td>
                                                                    <td class="secao">1/3 sobre férias vencidas:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_um_terco_ferias_vencidas link_pers">
                                                                            R$ <?= formato_real($fv_um_terco) ?>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr <?= $style_fp ?>>
                                                                    <td class="secao">Férias proporcionais (<?= $meses_ativo_fp ?>/12): </td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_ferias_propor link_pers">
                                                                            R$ <?= formato_real($fp_valor_total) ?>
                                                                        </a>
        <?php
        if ($media_mov_fixos_para_ferias > 0) {
            echo "<small> (média de férias = R$ " . number_format($media_mov_fixos_para_ferias, 2, ",", ".") . ")</small>";
        }
        ?>

                                                                    </td>
                                                                    <td class="secao">1/3 sobre férias proporcionais:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_um_terco_ferias_propor link_pers">
                                                                            R$ <?= formato_real($fp_um_terco) ?>
                                                                        </a>
        <?php
        if ($media_mov_fixos_para_um_terco_ferias > 0) {
            echo "<small> (média de 1/3 = R$ " . number_format($media_mov_fixos_para_um_terco_ferias, 2, ",", ".") . ")</small>";
        }
        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">F&eacute;rias em Dobro:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_ferias_dobro link_pers">
                                                                            R$ <?= formato_real($multa_fv) ?>
                                                                        </a>
                                                                    </td>
                                                                    <td class="secao"> 1/3 sobre f&eacute;rias em Dobro:</td>
                                                                    <td>
                                                                        <a href="javascript:;" class="detalhe_um_terco_ferias_dobro link_pers">
                                                                            R$ <?= formato_real($fv_um_terco_dobro) ?>
                                                                        </a>
                                                                    </td>       
                                                                </tr> 
                                                                <tr>
                                                                    <tr>
                                                                        <td class="secao"> Férias Aviso Indenizado (<?php echo $avos_aviso_ferias_indenizado ?>/12):</td>
                                                                        <td>
                                                                            <a href="javascript:;" class="detalhe_ferias_aviso_indenizado link_pers">
                                                                                R$ <?= formato_real($ferias_aviso_indenizado) ?>
                                                                            </a>
                                                                        </td>
                                                                        <td class="secao"> 1/3 sobre férias Aviso Indenizado:</td>
                                                                        <td>
                                                                            <a href="javascript:;" class="detalhe_um_terco_ferias_aviso_indenizado link_pers">
                                                                                R$ <?= formato_real($umterco_ferias_aviso_indenizado); ?>
                                                                            </a>
                                                                        </td>

                                                                    </tr>

                                            <!--<tr>
                                                     <td class="secao">Faltas no per&iacute;odo de f&eacute;rias proporcionais:</td>
                                                     <td><?= $faltas_ferias ?></td>
                                                 <td class="secao">Dias de f&eacute;rias recebido:</td>
                                                 <td><?= $qnt_ferias ?> dias</td>
                                                 </tr>-->

                                                                    <tr>
                                                                        <td colspan="4" align="center">
                                                                            <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_FERIAS) ?></span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4" class="divisor">Outros vencimentos</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="secao">Sal&aacute;rio familia:</td>
                                                                        <td>R$ <?= formato_real($valor_sal_familia) ?></td> 
                                                                        <td class="secao">Insalubridade</td>
                                                                        <td>R$ <?= formato_real($valor_insalubridade) ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="secao">Aviso Prévio:</td>
                                                                        <td>R$ <?= formato_real($valor_ap_recebido_trab) ?></td>
                                                                        <td class="secao">Lei nº 12.506:</td>
                                                                        <td>R$ <?= formato_real($lei_12_506) ?></td>         
                                                                    </tr>    

                                                                    <tr>
                                                                        <td class="secao">Atraso de Rescis&atilde;o (477):</td>
                                                                        <td>R$ <?= formato_real($valor_atraso); ?></td>       
                                                                        <td align="right"><span class="secao">Indeniza&ccedil;&atilde;o Artigo 479:</span></td>
                                                                        <td align="left">R$ <?php echo formato_real($art_479, 2, ',', '.'); ?>							
                                                                        </td>
                                                                    </tr>                                 

                                                                    <tr>
                                                                        <td colspan="4" style="font-size:14px; text-align:center; font-weight:bold;">
                                                                            R$ <?= number_format($TOTAL_OUTROS_VENCIMENTOS, 2, ',', '.'); ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4"><div class="divisor">Outros descontos</div></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="secao">Aviso Prévio pago pelo Funcion&aacute;rio:</td>
                                                                        <td>R$ <?= formato_real($valor_ap_pago_trab) ?></td>
                                                                        <td class="secao">Devolu&ccedil;&atilde;o:</td>
                                                                        <td>R$ <?= formato_real($devolucao) ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="secao">Indeniza&ccedil;&atilde;o Artigo 480:</td>
                                                                        <td colspan="3">R$  <?php echo formato_real($art_480); ?>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4" style="font-size:14px; font-weight:bold; text-align:center;">
                                                                            R$ <?= formato_real($TOTAL_OUTROS_DESCONTOS) ?>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td colspan="4"><div class="divisor">FGTS</div></td>
                                                                    </tr>
                                                                    <tr style="display:none;">
                                                                        <td class="secao">FGTS 8%:</td>
                                                                        <td>R$ <?= $fgts8_totalF ?> (<?= $mensagem_fgts8 ?>)</td>
                                                                        <td class="secao">FGTS 40%:</td>
                                                                        <td>R$ <?= $fgts4_totalF ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="secao">Código de Saque:</td>
                                                                        <td><?= $cod_saque_fgts ?></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="4" align="center">
                                                                            <p>&nbsp;</p>

                                                                            <?php
                                                                            $valor_td = $valor_td + $media_td;

                                                                            if ($faltas > 0) {
                                                                                $saldo_de_salario = $saldo_de_salario_nojo;
                                                                                $dias_trabalhados = $dias_trabalhados_nojo;
                                                                            }

                                                                            /**
                                                                             * By Ramon 19/07/2016
                                                                             * A Alana lançou médias de ferias proporcionais, porem a pessoa teve 12 avos e na hora de GRAVAR a rescisão 
                                                                             * os valores q antes apareciam como proporcionais, terão q aparecer como férias VENCIDAS por conta dos 12 avos...
                                                                             * Então as médias q foram lançadas para calculo como ferias proporcionais agora vão pertencer as férias Vencidas...
                                                                             * Vamos a GAMBIARRA DA PROJEÇÃO
                                                                             * (MEDIA FERIAS PROJEÇAO AVISO PREVIO)
                                                                             */
                                                                            //VERIFICA SE AS FERIAS PROPORCINAL É 12 AVOS
                                                                            // comentado por Leonardo em 2017-03-07. coloquei o mesmo código mais acima para aparecer na memoria de calculo
//        if ($meses_ativo_fp == 12) {
//            $meses_ativo_fp = 0;
//            /* DATAS */
//            $periodo_venc_inicio = $periodo_proporcional_inicio;
//            $periodo_venc_final = $periodo_proporcional_final;
//            /* VALOR */
//            $fv_valor_base = $fp_valor_total;
//            $fv_um_terco = $fp_um_terco;
//
//            $periodo_proporcional_inicio = null;
//            $periodo_proporcional_final = null;
//            $fp_valor_total = null;
//            $fp_um_terco = null;
//        }

                                                                            $campos_insert['id_clt'] = $id_clt;
                                                                            //$campos_insert['ajuda_custo']       =  $ajuda_custo;
                                                                            $campos_insert['nome'] = $nome;
                                                                            $campos_insert['id_regiao'] = $idregiao;
                                                                            $campos_insert['id_projeto'] = $idprojeto;
                                                                            $campos_insert['id_curso'] = $idcurso;
                                                                            $campos_insert['data_adm'] = $data_entrada;
                                                                            $campos_insert['data_demi'] = $data_demissao;
                                                                            $campos_insert['data_proc'] = date('Y-m-d');
                                                                            $campos_insert['dias_saldo'] = $dias_trabalhados;
                                                                            $campos_insert['um_ano'] = ($um_ano > $um_ano_real) ? $um_ano_real : $um_ano;
                                                                            //$campos_insert['meses_ativo']       =  $meses_ativo;
                                                                            $campos_insert['motivo'] = $dispensa;
                                                                            $campos_insert['fator'] = $fator;
                                                                            $campos_insert['aviso'] = $aviso;
                                                                            $campos_insert['aviso_valor'] = $valor_aviso_previo;
                                                                            $campos_insert['dias_aviso'] = $previo;
                                                                            $campos_insert['data_fim_aviso'] = $data_fim_avprevio;
                                                                            $campos_insert['fgts8'] = $fgts8_totalT;
                                                                            $campos_insert['fgts40'] = $fgts4_totalT;
                                                                            $campos_insert['fgts_anterior'] = $anterior;
                                                                            $campos_insert['fgts_cod'] = $cod_mov_fgts;
                                                                            $campos_insert['fgts_saque'] = $cod_saque_fgts;
                                                                            $campos_insert['sal_base'] = $salario_base_limpo;
                                                                            $campos_insert['saldo_salario'] = $saldo_de_salario;
                                                                            $campos_insert['inss_ss'] = $inss_saldo_salario;
                                                                            $campos_insert['previdencia_ss'] = $inss_saldo_salario;
                                                                            $campos_insert['ir_ss'] = $irrf_saldo_salario;
                                                                            $campos_insert['terceiro_ss'] = $valor_13_indenizado;
                                                                            $campos_insert['dt_salario'] = $valor_td;
                                                                            $campos_insert['inss_dt'] = $valor_td_inss;
                                                                            $campos_insert['previdencia_dt'] = $valor_td_inss;
                                                                            $campos_insert['ir_dt'] = $valor_td_irrf;
                                                                            $campos_insert['ferias_vencidas'] = $fv_valor_base;
                                                                            $campos_insert['umterco_fv'] = $fv_um_terco;
                                                                            $campos_insert['ferias_pr'] = $fp_valor_total;
                                                                            $campos_insert['umterco_fp'] = $fp_um_terco;
                                                                            $campos_insert['sal_familia'] = $valor_sal_familia;
                                                                            $campos_insert['to_sal_fami'] = ($valor_sal_familia + $sal_familia_anterior);
                                                                            //$campos_insert['ad_noturno']            =  $valor_adnoturnoT;
                                                                            $campos_insert['insalubridade'] = $valor_insalubridade;
                                                                            //$campos_insert['vale_refeicao']         =  $vale_refeicaoT;
                                                                            //$campos_insert['debito_vale_refeicao']  =  $debito_vale_refeicaoT;
                                                                            $campos_insert['a480'] = $art_480;
                                                                            $campos_insert['a479'] = $art_479;
                                                                            $campos_insert['a477'] = $valor_atraso;
                                                                            $campos_insert['lei_12_506'] = $lei_12_506;
                                                                            //$campos_insert['comissao']              =  $valor_comissaoT;
                                                                            //$campos_insert['gratificacao']          =  $valor_grativicacao;
                                                                            //$campos_insert['extra']                 =  $hora_extra;
                                                                            //$campos_insert['outros']                =  $valor_outroT;
                                                                            //$campos_insert['movimentos']            =  $a_rendimentos;
                                                                            //$campos_insert['valor_movimentos']      =  $a_rendimentos;
                                                                            $campos_insert['total_rendimento'] = round($to_rendimentos, 2);
                                                                            $campos_insert['total_deducao'] = round($to_descontos, 2);
                                                                            $campos_insert['total_liquido'] = round($valor_rescisao_final, 2);
                                                                            $campos_insert['arredondamento_positivo'] = $arredondamento_positivo;
                                                                            $campos_insert['avos_dt'] = $meses_ativo_dt;
                                                                            $campos_insert['avos_fp'] = $meses_ativo_fp;
                                                                            $campos_insert['data_aviso'] = $data_aviso;
                                                                            $campos_insert['devolucao'] = $devolucao;
                                                                            $campos_insert['faltas'] = $faltas;
                                                                            $campos_insert['valor_faltas'] = $valor_faltas;
                                                                            $campos_insert['user'] = $user;
                                                                            $campos_insert['ferias_aviso_indenizado'] = $ferias_aviso_indenizado;
                                                                            $campos_insert['umterco_ferias_aviso_indenizado'] = $umterco_ferias_aviso_indenizado;
                                                                            $campos_insert['adiantamento_13'] = $valor_decimo_folha;
                                                                            //$campos_insert['folha']                     =  '0';
                                                                            //$campos_insert['adicional_noturno']         =  $adicional_noturno;
                                                                            //$campos_insert['dsr']                       =  $dsr;
                                                                            //$campos_insert['desc_auxilio_distancia']    =  $desc_auxilio_distancia;
                                                                            $campos_insert['um_terco_ferias_dobro'] = $fv_um_terco_dobro;
                                                                            $campos_insert['fv_dobro'] = $multa_fv;
                                                                            $campos_insert['fp_data_ini'] = $periodo_proporcional_inicio;
                                                                            $campos_insert['fp_data_fim'] = $periodo_proporcional_final;
                                                                            $campos_insert['fv_data_ini'] = $periodo_venc_inicio;
                                                                            $campos_insert['fv_data_fim'] = $periodo_venc_final;
                                                                            $campos_insert['qnt_dependente_salfamilia'] = $TOTAL_MENOR;
                                                                            $campos_insert['base_inss_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
                                                                            $campos_insert['percentual_inss_ss'] = $PERCENTUAL_INSS_SS;
                                                                            $campos_insert['base_irrf_ss'] = $BASE_CALC_IRRF_SALDO_SALARIO;
                                                                            $campos_insert['percentual_irrf_ss'] = $PERCENTUAL_IRRF_SS;
                                                                            $campos_insert['parcela_deducao_irrf_ss'] = $PARCELA_DEDUCAO_IR_SS;
                                                                            $campos_insert['qnt_dependente_irrf_ss'] = $QNT_DEPENDENTES_IRRF_SS;
                                                                            $campos_insert['valor_ddir_ss'] = $VALOR_DDIR_SS;
                                                                            $campos_insert['base_fgts_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
                                                                            $campos_insert['base_inss_13'] = $BASE_CALC_INSS_13;
                                                                            $campos_insert['percentual_inss_13'] = $PERCENTUAL_INSS_13;
                                                                            $campos_insert['base_irrf_13'] = $BASE_CALC_IRRF_13;
                                                                            $campos_insert['percentual_irrf_13'] = $PERCENTUAL_IRRF_13;
                                                                            $campos_insert['parcela_deducao_irrf_13'] = $PARCELA_DEDUCAO_IR_13;
                                                                            $campos_insert['base_fgts_13'] = $BASE_CALC_INSS_13;
                                                                            $campos_insert['qnt_dependente_irrf_13'] = $QNT_DEPENDENTES_IRRF_13;
                                                                            $campos_insert['valor_ddir_13'] = $VALOR_DDIR_13;
                                                                            $campos_insert['desconto_inss'] = $row_clt['desconto_inss'];
                                                                            $campos_insert['salario_outra_empresa'] = $row_clt['salario_outra_empresa'];
                                                                            $campos_insert['desconto_inss_outra_empresa'] = $row_clt['desconto_outra_empresa'];
                                                                            $campos_insert['cod_saque'] = $dadosMovimentacao['codigo_saque'];
                                                                            $campos_insert['cod_movimentacao'] = $dadosMovimentacao['cod_movimentacao'];
                                                                            $campos_insert['avos_projetado'] = $avosProj;


                                                                            if ($_REQUEST['recisao_coletiva'] == 1) {
                                                                                $campos_insert['recisao_provisao_de_calculo'] = 1;
                                                                                $campos_insert['status'] = 0;
                                                                                $campos_insert['id_recisao_lote'] = $_REQUEST['id_header'];
                                                                            }

                                                                            if (in_array($_COOKIE['logado'], $programadores)) {
                                                                                echo '<div class="collapse demo"><pre>';
                                                                                print_r($campos_insert);
                                                                                echo "</pre></div>";
                                                                            }


                                                                            foreach ($campos_insert as $campo => $valor) {
                                                                                $campos[] = $campo;
                                                                                $valores[] = "'$valor'";
                                                                            }

                                                                            $campos = implode(',', $campos);
                                                                            $valores = implode(',', $valores);


                                                                            if ($_REQUEST['recisao_coletiva'] == 1) {

                                                                                $id_header = $_REQUEST['id_header'];

                                                                                $return = array("status" => 1, "total" => 0);

                                                                                $query = "SELECT IFNULL(id_clt,0) id_clt FROM rh_recisao_provisao_de_gastos WHERE id_recisao_lote = {$id_header} AND id_clt = {$id_clt}";

                                                                                $rs = mysql_query($query);

                                                                                $row = mysql_fetch_assoc($rs);

                                                                                if ($row['id_clt'] == 0) {

                                                                                    $query = "INSERT INTO rh_recisao_provisao_de_gastos ($campos) VALUES ($valores)";

                                                                                    mysql_query($query);
                                                                                }


                                                                                $query = "SELECT IFNULL(COUNT(id_recisao),0) total FROM rh_recisao_provisao_de_gastos WHERE id_recisao_lote={$id_header}";

                                                                                $rs = mysql_query($query);

                                                                                $row = mysql_fetch_assoc($rs);


                                                                                if (empty(mysql_error())) {

                                                                                    $return = array("status" => 1, "id_header" => $id_header, "total" => (int) $row['total']);
                                                                                } else {

                                                                                    $return = array("status" => 0, "msg" => mysql_error() . " - {$query}");
                                                                                }

                                                                                ob_end_clean();

                                                                                exit(json_encode($return));
                                                                            } else {

                                                                                $conteudo = "INSERT INTO rh_recisao ($campos) VALUES ($valores); \r\n";
                                                                            }

                                                                            $handle = fopen("a_log.txt", "a");
                                                                            fwrite($handle, $conteudo);
                                                                            fclose($handle);


                                                                            $ultimo_rescisao_lote = mysql_insert_id();

                                                                            // Relaciona a rescisão ao tipo de aviso prévio -- AMANDA
//                                        if ($aviso == 'trabalhado' && $dispensa == 61) {
//                                            $id_tpAvisoPre = $_REQUEST['tpAvisoPre'];
//                                            $obs = $_REQUEST['obs'];
//                                            $conteudo .= "INSERT INTO rescisao_avisoPrevio_assoc (id_tpAvisoPre,obs,status,id_rescisao) VALUES ($id_tpAvisoPre,'$obs',1, ultimo_id_rescisao);\r\n";
//                                        }
                                                                            /**
                                                                             * status = '{$dispensa}',
                                                                             */
                                                                            $conteudo .= "UPDATE rh_clt SET status = '{$dispensa}', data_saida = '{$data_demissao}', status_demi = '1' WHERE id_clt = '{$id_clt}' LIMIT 1; \r\n";

                                                                            // FIM 
                                                                            // AKI O PROBLEMA
                                                                            //$conteudo .= "INSERT INTO rh_eventos(id_clt, id_regiao, id_projeto, nome_status, cod_status, id_status, data, status) VALUES ('$id_clt', '$idregiao', '$idprojeto', '$row_evento[especifica]', '$dispensa', '$row_evento[0]', '$data_demissao', '1');\r\n";

                                                                            $nome_arquivo = 'recisaoteste_' . $id_clt . '_' . date('dmY') . '.txt';
                                                                            $arquivo = '../arquivos/' . $nome_arquivo;

                                                                            //BY RAMON
                                                                            //NA HORA DE GRAVAR OS MOVIMENTOS NA TABELA DE RESCISÃO MODIFICAR OS MOCIMENTOS CLT QUE SÃO FUTUROS PARA O MES DA RESCISAO
                                                                            if (sizeof($movimentos) > 0) {
                                                                                $ids_movimentos = implode(',', $movimentos);

                                                                                $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
                                                                                $flag_mov_futuro = 0;
                                                                                while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                                                                    if ($row_mov['mes_mov'] != 16 && in_array($row_mov['cod_movimento'], $array_movimentos_futuros)) {
                                                                                        $flag_mov_futuro = 1;
                                                                                    }

                                                                                    $conteudo .= "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, id_movimento, mes_mov, ano_mov, nome_movimento, valor, incidencia, tipo_qnt, qnt, mov_futuro ) VALUES (ultimo_id_rescisao,'$row_mov[id_mov]', '$row_mov[id_clt]','$row_mov[id_movimento]', '$row_mov[mes_mov]', '$row_mov[ano_mov]' ,'$row_mov[nome_movimento]', '$row_mov[valor_movimento]',  '$row_mov[incidencia]', '$row_mov[tipo_qnt]', '$row_mov[qnt]', {$flag_mov_futuro} ); \r\n";

                                                                                    //VERIFICANDO SE É MOVIMENTO FUTURO
                                                                                    if ($flag_mov_futuro) {
                                                                                        $conteudo .= "UPDATE rh_movimentos_clt SET mes_mov=16 WHERE  id_movimento={$row_mov['id_movimento']}; \r\n";
                                                                                    }
                                                                                }
                                                                            }


                                                                            // Tenta abrir o arquivo TXT            
                                                                            if (!$abrir = fopen($arquivo, "wa+")) {
                                                                                echo "Erro abrindo arquivo ($arquivo)";
                                                                                exit;
                                                                            }

                                                                            // Escreve no arquivo TXT
                                                                            if (!fwrite($abrir, $conteudo)) {
                                                                                print "Erro escrevendo no arquivo ($arquivo)";
                                                                                exit;
                                                                            }


                                                                            // Fecha o arquivo
                                                                            fclose($abrir);

                                                                            // Encriptografando a variável
                                                                            $linkir = str_replace('+', '--', encrypt("$regiao&$id_clt&$nome_arquivo"));
                                                                            $linkvolt = str_replace('+', '--', encrypt("$regiao&$id_clt"));
                                                                            ?>
                                                                            <table width="50%" border="0" cellspacing="0" cellpadding="0">                                      

                                                                                <tr>
        <?php echo "<!--a  href='{$arquivo}' target='blanck'>{$arquivo}</a-->"; ?>
                                                                                    <td><a href="recisao2.php?tela=4&enc=<?= $linkir ?>" class="botao recisao_lote">Processar Rescis&atilde;o</a></td>
                                                                                    <td><a href="recisao2.php?tela=4&editar=1&enc=<?= $linkir ?>" class="botao">Forçar Valores</a></td>
                                                                                    <td><a href="recisao2.php?tela=2&enc=<?= $linkvolt ?>" class="botao">Voltar</a></td>
                                                                                </tr>
                                                                            </table>
                                                                            <div class="div_detalhe_rendimentos"></div>
                                                                            <div class="div_detalhe_descontos"></div>
                                                                            <div class="div_detalhe_saldo_salario"></div>
                                                                            <div class="div_detalhe_inss"></div>
                                                                            <div class="div_detalhe_ir"></div>
                                                                            <div class="div_detalhe_valor_dt"></div>
                                                                            <div class="div_detalhe_valor_inss_dt"></div>
                                                                            <div class="div_detalhe_valor_dt_indenizado"></div>
                                                                            <div class="div_detalhe_valor_irrf_dt"></div>
                                                                            <div class="div_detalhe_ferias_vencidas"></div>
                                                                            <div class="div_detalhe_ferias_propor"></div>
                                                                            <div class="div_detalhe_ferias_dobro"></div>
                                                                            <div class="div_detalhe_ferias_aviso_indenizado"></div>
                                                                            <div class="div_detalhe_um_terco_ferias_vencidas"></div>
                                                                            <div class="div_detalhe_um_terco_ferias_propor"></div>
                                                                            <div class="div_detalhe_um_terco_ferias_dobro"></div>
                                                                            <div class="div_detalhe_um_terco_ferias_aviso_indenizado"></div>
                                                                            <p>&nbsp;</p>
                                                                        </td>
                                                                    </tr>
                                                            </table>
                                                        </form>
                                                        <?php
                                                        break;

                                                    //-------------------------------------------------------    
                                                    case 4:
                                                        // executando a rescisão
                                                        // Recebendo a variável criptografada
                                                        error_reporting(E_ALL);


                                                        list($regiao, $id_clt, $arquivo) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

                                                        $file = '../arquivos/' . $arquivo;
                                                        $fp = file($file);
                                                        $i = '0';

                                                        verificaRecisao($id_clt);

                                                        /**
                                                         * ATUALIZANDO A TABELA DE RH_CLT
                                                         * COM A DATA ATUAL DA AÇÃO DE 
                                                         * FINALIZAR A FOLHA
                                                         */
//                    $rh->Clt->setDefault()->setIdClt($id_clt)->onUpdate();
                                                        onUpdate($id_clt);

                                                        foreach ($fp as $linha) {
                                                            $linha = str_replace('ultimo_id_rescisao', $idi[0], $linha);
                                                            mysql_query($linha) or die(mysql_error());
                                                            $i++;
                                                            $idi[] = mysql_insert_id();
                                                        }

                                                        //CRIANDO LOG DE RESCISÃO
                                                        $query = "SELECT * FROM rh_clt WHERE id_clt = '{$id_clt}'";
                                                        $sql = mysql_query($query) or die("Erro ao selecionar clt");
                                                        $dados_clt = mysql_fetch_assoc($sql);
                                                        $data_cad = date("Y-m-d H:i:s");
                                                        $rescisao->criaLog($id_clt, $dados_clt['id_regiao'], $dados_clt['id_projeto'], $_COOKIE['logado'], $data_cad, 1, 1);

                                                        // Encriptografando a variável
                                                        $link = str_replace('+', '--', encrypt("$regiao&$id_clt&$idi[0]"));

                                                        if ($_GET['editar']) {
                                                            echo '<script>location.href="rescisao_edicao.php?enc=' . $link . '"</script>';
                                                        } else {
                                                            echo '<script>location.href="nova_rescisao_2.php?enc=' . $link . '"</script>';
                                                        }

                                                        exit();

                                                        break;
                                                }
                                                ?>
                                                </div>
                                                <script>
                                                    $(document).ready(function () {
                                                        $("#txt_ano").val('<?= $_REQUEST['txt_ano'] ?>');
                                                        $("#txt_mes").val('<?= $_REQUEST['txt_mes'] ?>');
                                                    });
                                                    function sel_all()
                                                    {
                                                        if ($('#txt_sel').is(":checked"))
                                                        {
                                                            $(".check").attr("checked", true);
                                                        } else
                                                        {
                                                            $(".check").attr("checked", false);
                                                        }
                                                    }

                                                </script>
                                                <footer>
                                                    <div>
                                                        <p>Pay All Fast 3.0 build 8321 - <?= date('d/m/Y - H:i') ?></p>
                                                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                                                    </div>
                                                </footer>         
                                                </body>
                                                </html>
                                                <?php

                                                function trava_estabilidade($id) {
                                                    $sql = "select * from rh_estabilidade_provisoria p where now() BETWEEN p.data_ini and p.data_fim and p.id_clt = {$id} and p.status = 1;";

                                                    if ($_COOKIE['logado'] == 258) {
                                                        echo "trava_estabilidade = [{$sql}]<br/>\n";
                                                    }

                                                    $con = mysql_query($sql);
                                                    $total = mysql_num_rows($con);

                                                    if ($total > 0) {
                                                        return TRUE;
                                                    } else {
                                                        return FALSE;
                                                    }
                                                }
