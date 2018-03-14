<?php
$programadores = array(179,158,260);
include('../../conn.php');
if($_REQUEST['recisao_coletiva'] != 1){
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
include('../../classes/MovimentoClass.php');
include('../../classes/CltClass.php');
  
$usuario = carregaUsuario();
$rescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();
$objCalcRescisao= new Calculo_Rescisao();
$dadosClt = new CltClass();

function verificaRecisao($id_clt) {
    /*Verifica se já foi realizada rescisão para o funcionário*/
    $retorno = montaQuery('rh_recisao', 'id_clt,nome', "id_clt = '{$id_clt}' AND status = 1");
    $clt_status = montaQuery('rh_clt', 'status', "id_clt='{$id_clt}'");
    $clt_status = $clt_status[1]['status'];
    if (isset($retorno[1]['id_clt']) && !empty($retorno[1]['id_clt']) && isset($clt_status) && !empty($clt_status)) { ?>
        <script type="text/javascript">
            alert('A rescisão deste funcionário já foi realizada.\nNome: ' + '<?=$retorno[1]['nome'] ?>');
            window.history.back();
        </script>
        <?php
        exit();
    }
}

$Fun = new funcionario();
$Fun->MostraUser(0);
$user = $Fun->id_funcionario;
$ACOES = new Acoes();
$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$Calc = new calculos();
$Curso = new tabcurso();
$Clt = new clt();
$ClasPro = new projeto();
$obj_recisao = new Rescisao();
$optTiposDispensa = $rescisao->listTiposRescisao("array");

if (empty($_REQUEST['tela'])) { $tela = 1;
} else { $tela = $_REQUEST['tela']; }

$sqlBanco = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$_GET['regiao']} ORDER BY id_banco");
while($rowBanco = mysql_fetch_array($sqlBanco)){
    $optionBanco .= "<option value='{$rowBanco['id_banco']}'>{$rowBanco['razao']}({$rowBanco['nome']})</option>";
}

require_once('../../classes/ArquivoTxtBancoClass.php');
$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();
$arrayArquivos = $ArquivoTxtBancoClass->getRegistros('r');
if(isset($_REQUEST['arqRescisao']) AND !empty($_REQUEST['arqRescisao'])){
    $ArquivoTxtBancoClass->gerarTxtBanco('RESCISAO',$_REQUEST['banco'], $_REQUEST['data'], $_REQUEST['arqRescisao']);
    header("Location: arquivo_banco_rescisao.php");
}


$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Rescisão");
$breadcrumb_pages = array("Gestão de RH" => "../");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Rescisão</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Rescisão</small></h2></div>
                </div>
            </div>
            <div id="mensagens" style="display: none;">
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
                        }
                        $projetoR = $_REQUEST['projeto'];
                    } else {
                        $filtroProjeto = '';
                    } ?>
                    <?php
                    // Encriptografando a variável
                    $link = str_replace('+', '--', encrypt("$regiao")); ?>
                    <form action="" method="post" class="filtro">
                        <div class="panel panel-default">
                            <div class="panel-heading">Filtro</div>
                            <div class="panel-body">
                                <label class="col-xs-1 control-label">Projeto:</label>
                                <div class="col-xs-5"><?=montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='form-control required[custom[select]]'") ?></div>
                                <div class="col-xs-6"><input type="text" name="pesquisa" placeholder="Nome, Matricula, CPF" class="form-control" value="<?=$_REQUEST['pesquisa']; ?>"></div>
                            </div>
                            <div class="panel-footer">
                                <div class="col-xs-6 text-left">
                                    <a href="../../relatorios/provisao_de_gastos.php?regiao=<?=$regiao; ?>" class="gerar_rel btn btn-default">Relatório de Rescisão em Lote</a>
                                    <a href="recisao_mes.php?regiao=<?=$regiao; ?>" class="gerar_rel2 btn btn-default">Relatório por Mês</a>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <input type="hidden" name="filtro" value="1" />
                                    <button type="submit" value="Consultar" class="btn btn-primary" name="consultar">Consultar</button>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </form>
                    
                    <?php
                    if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                        
                        if(!empty($_REQUEST['pesquisa'])){
                            $valorPesquisa = explode(' ',$_REQUEST['pesquisa']);
                            foreach ($valorPesquisa as $valuePesquisa) {
                                $pesquisa[] .= "nome LIKE '%".$valuePesquisa."%'";
                            }
                            $pesquisa = implode(' AND ',$pesquisa);
                            $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
                        }
                        
                        // Consulta de Clts Aguardando Demissão
                        $qr_aguardo = mysql_query("SELECT * FROM rh_clt WHERE status = '200' AND id_regiao = '$regiao' $filtroProjeto $auxPesquisa ORDER BY nome ASC");
                        $total_aguardo = mysql_num_rows($qr_aguardo);

                        if (!empty($total_aguardo)) { ?>
                            <table class="table table-condensed table-hover table-bordered text-sm">
                                <thead>
                                    <tr class="bg-danger valign-middle">
                                        <th colspan="6"><span class="fa fa-arrow-right text-warning"></span> Participantes Aguardando Rescis&atilde;o</th>
                                    </tr>
                                    <tr class="danger valign-middle">
                                        <th width="3%">COD</th>
                                        <th width="29%">NOME</th>
                                        <th width="20%">PROJETO</th>
                                        <th width="20%">UNIDADE</th>
                                        <th width="25%">CARGO</th>
                                        <th width="3%">AÇÃO</th>	
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row_aguardo = mysql_fetch_array($qr_aguardo)) {

                                        $Curso->MostraCurso($row_aguardo['id_curso']);
                                        $NomeCurso = $Curso->nome;

                                        $ClasPro->MostraProjeto($row_aguardo['id_projeto']);
                                        $NomeProjeto = $ClasPro->nome;

                                        // Encriptografando a variável
                                        $link = str_replace('+', '--', encrypt("$regiao&$row_aguardo[0]")); ?>

                                        <tr class="valign-middle">
                                            <td><?= $row_aguardo['campo3'] ?></td>
                                            <td><a href="recisao2.php?tela=2&enc=<?= $link ?>"><?= $row_aguardo['nome'] ?></a></td>
                                            <td><?= $NomeProjeto ?></td>
                                            <td><?= $row_aguardo['locacao'] ?></td>
                                            <td><?= $NomeCurso ?></td>
                                            <td class="text-center">
                                                <?php if ($ACOES->verifica_permissoes(82)) { ?>
                                                <a class="btn btn-xs btn-danger" href="recisao2.php?voltar_aguardando=true&id=<?=$row_aguardo[0]; ?>&regiao=<?=$_GET['regiao']; ?>&id_clt=<?=$row_aguardo[0]; ?>" title="Desprocessar Aguardando Demissão" onclick="return window.confirm('Você tem certeza que quer desprocessar aguardando demissão?');"><i class="fa fa-ban"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        <?php } ?>
                        <form action='' method="post" class="form-horizontal">
                            <table class="table table-condensed table-hover table-bordered text-sm">
                                <thead>
                                    <tr class="bg-primary valign-middle">
                                        <th colspan="10"><span class="fa fa-arrow-right text-warning"></span> Participantes Desativados</th>
                                    </tr>
                                    <tr class="info valign-middle">
                                        <th></th>
                                        <th width="3%">COD</th>
                                        <th width="30%">NOME</th>
                                        <th width="29%">PROJETO</th>
                                        <th width="10%" class="text-center">DATA</th>
                                        <th width="6%" class="text-center">RESCIS&Atilde;O</th>
                                        <th width="6%" class="text-center">COMPLEMENTAR</th>
                                        <th width="3%" class="text-center">ADD</th>
                                        <th width="10%" class="text-center">VALOR</th>
                                        <th width="3%" class="text-center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consulta de Clts que foram demitidos
                                    $sql_demissao = "SELECT *, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE status IN ('60','61','62','63','64','65','66','80','81','101') AND id_regiao = '$regiao' $filtroProjeto $auxPesquisa ORDER BY nome ASC";
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
                                        $qr_rescisao_complementar = mysql_query($sql_rescisao_complementar);
                                        $total_rescisao_complementar = mysql_num_rows($qr_rescisao_complementar);
                                        $arr_complementar = array();
                                        while($row_rescisao_complementar = mysql_fetch_array($qr_rescisao_complementar)){
                                            $arr_complementar[] = $row_rescisao_complementar;
                                        }
                                        $link = str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao[0]"));

                                        if (substr($row_rescisao['data_proc'], 0, 10) >= '2013-04-04') {
                                            $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                                        } else {
                                            $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                                        } ?>

                                        <tr class="valign-middle">
                                            <td class="text-center">
                                            <?php if($row_demissao['conta'] == '' OR $row_demissao['conta'] == '000000' OR $row_demissao['tipo_conta'] == ''){ ?>
                                                SEM CONTA
                                            <?php }else if($row_rescisao['total_liquido'] == 0.00){ ?>
                                                VALOR ZERADO
                                            <?php }else if(!array_key_exists($row_rescisao['id_recisao'],$arrayArquivos)){ ?>
                                                <input type='checkbox' name="arqRescisao[]" checked value="<?=$row_rescisao['id_recisao']; ?>" />
                                            <?php } ?>
                                            </td>
                                            <td><?= $row_demissao['campo3'] ?></td>
                                            <td><?= $row_demissao['nome'] ?></td>
                                            <td><?= $NomeProjeto ?></td>
                                            <td class="text-center"><?= $row_rescisao['data_demi2'] ?></td>
                                            <td class="text-center">
                                                <?php 
                                                if (empty($total_rescisao)) { ?>
                                                    <!--img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)"-->
                                                    <a class="btn btn-xs btn-default disabled"><i class="fa fa-file-pdf-o text-danger"></i></a>
                                                <?php } else { ?>
                                                    <!--a  class="link" target="_blank" title="Visualizar Rescisão"><img src="../../imagens/pdf.gif" border="0"></a-->
                                                    <a href="<?= $link_nova_rescisao; ?>"  target="_blank" title="Visualizar Rescisão" class="link btn btn-xs btn-default"><i class="fa fa-file-pdf-o text-danger"></i></a>
                                                <?php } ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if (empty($total_rescisao_complementar)) { ?>
                                                    <!--img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)" /-->
                                                    <a class="btn btn-xs btn-default disabled"><i class="fa fa-file-pdf-o text-danger"></i></a>
                                                <?php } else {                                            
                                                    foreach($arr_complementar as $row_rescisao_complementar){
                                                        $link_2                 =  str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao_complementar[0]"));
                                                        $link_resc_complementar =  "nova_rescisao_2.php?enc=$link_2"; ?>
                                                        <!--a href="<?= $link_resc_complementar; ?>" class="link" target="_blank" title="Visualizar Rescisão Complementar"><img src="../../imagens/pdf.gif" border="0"></a-->
                                                        <a href="<?= $link_resc_complementar; ?>" target="_blank" title="Visualizar Rescisão Complementar" class="link btn btn-xs btn-default disabled"><i class="fa fa-file-pdf-o text-danger"></i></a>
                                                    <?php } 
                                                } ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="form_rescisao_complementar.php?id_clt=<?= $row_demissao['id_clt']; ?>&id_rescisao=<?= $row_rescisao['id_recisao']; ?>" title="Adicionar Complementar" class="btn btn-xs btn-success"><i class="fa fa-plus-circle" alt="Adionar Complementar"></i></a>
                                            </td>

                                            <td class="text-right">R$ <?php
                                                $total_recisao = $row_rescisao['total_liquido'];
                                                echo number_format($total_recisao, 2, ',', '.');
                                                $totalizador_recisao += $total_recisao;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($ACOES->verifica_permissoes(82)){ ?> 
                                                    <a href="javascript:;" title="Desprocessar Rescisão" data-recisao="<?=$row_rescisao[0]; ?>" data-regiao="<?=$_GET['regiao']; ?>" data-clt="<?=$row_demissao[0]; ?>" class="remove_recisao btn btn-xs btn-danger"><i class="fa fa-ban"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr class="active">
                                        <td colspan="7" class="text-right">TOTAL : </td>
                                        <td colspan="3" class="text-right">R$<?=number_format($totalizador_recisao, 2, ',', '.'); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="10">
                                            <label class="col-xs-1 control-label">Banco:</label>
                                            <div class="col-xs-3"><select name="banco" class="form-control"><?=$optionBanco; ?></select></div>
                                            <label class="col-xs-1 control-label">Data:</label>
                                            <div class="col-xs-3"><input type="text" name="data" class="data form-control"></div>

                                            <button type="submit" value="Gerar Arquivo de Banco" class="btn btn-default"><i class="fa fa-file-text"></i> Gerar Arquivo de Banco</button>
                                            <a href="arquivo_banco_rescisao.php" target="_blank" class="btn btn-warning"><i class="fa fa-gear"></i> Gerenciar Arquivos</a>
                                        </td>
                                    </tr>
                                </tbody>
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
            } ?>
            <?php include_once '../../template/footer.php'; ?>
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
        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';
            $(function() {
                $('#dispensa').change(function() {
                    var dispensa = parseInt($(this).val());
                    switch (dispensa) {
                        case 64:
                        case 66:
                        case 61: $('#fator').val('empregador').css('background-color', '#eeeeee'); break;
                        case 63:
                        case 65: $('#fator').val('empregado').css('background-color', '#eeeeee'); break;
                        default: $('#fator').val('empregador').css('background-color', '#eeeeee');
                    }

                    if (dispensa == 61 || dispensa == 62 || dispensa == 65) {
                        $('#aviso').val('indenizado').css('background-color', '#ffffff').attr('disabled', false);
                        $('#previo').css('background-color', '#ffffff').attr('disabled', false);
                        $('#data_aviso').css('background-color', '#ffffff').attr('disabled', false);
                    } else {
                        $('#aviso').val('').css('background-color', '#eeeeee').attr('disabled', true);
                        $('#previo').css('background-color', '#eeeeee').attr('disabled', true);
                        $('#data_aviso').css('background-color', '#eeeeee').attr('disabled', true);
                    }
                });

                $('#dispensa').change();
                $('#data_aviso').datepicker({
                    changeMonth: true,
                    changeYear: true
                });
                
                $('#desprocessaFerias').click(function(){
                   var id_ferias = $(this).data('key');
                   
                   if(confirm('Tem certeza que quer desprocessar as férias?')){                       
                       $.post('recisao2.php',{tela: 2, desprocFerias:1, id:id_ferias},  function(data){
                           if(data == 1){
                                alert('As férias foi desprocessada.');
                                $('.linha_ferias').fadeOut('slow');
                            }
                       },'html');
                   }
                    return false;
                })
                
                $('#gerar').click(function() {

                    var regiao = $('#regiao').val();
                    var data_escolhida = $('#data_aviso').val();

                    $.ajax({
                        url: 'action.verifica_folha.php?data=' + data_escolhida + '&regiao=' + regiao,
                        type: 'GET',
                        dataType: 'json',
                        success: function(resposta) {

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

                $(".remove_recisao").click(function() {
                    $("#CancelAviso").show();
                    thickBoxModal("Desprocessar Recisão", "#CancelAviso", 350, 400);
                    $("#idCanRescisao").val($(this).attr("data-recisao"));
                    $("#idCanRegiao").val($(this).attr("data-regiao"));
                    $("#idCanClt").val($(this).attr("data-clt"));
                });
                
                $(".btn").click(function (){
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
                            success: function(data) {
                                if(!data.status){
                                    $(data.dados).each(function(k, v) {
                                        $(".data_demissao").html(v.data_demissao);
                                        $(".data_pagamento").html(v.data_pg);
                                        $(".nome").html(v.nome_clt);
                                        $(".status").html(v.status_saida);
                                        $(".valor").html(v.valor);
                                    });
                                    $("#mensagens").show();
                                    thickBoxModal("Desprocessar Recisão", "#mensagens", "350", "450");
                                }else{
                                    history.go(0);
                                }
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>