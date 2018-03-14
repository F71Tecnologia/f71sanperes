<?php
include('../../conn.php');
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
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

function verificaRecisao($id_clt) {
    /*
     * Verifica se j� foi realizada rescis�o para o funcion�rio
     */
    $retorno = montaQuery('rh_recisao', 'id_clt,nome', "id_clt = '{$id_clt}' AND status = 1");
    $clt_status = montaQuery('rh_clt', 'status', "id_clt='{$id_clt}'");
    $clt_status = $clt_status[1]['status'];
    if (isset($retorno[1]['id_clt']) && !empty($retorno[1]['id_clt']) && isset($clt_status) && !empty($clt_status)) {
        ?>
        <script type="text/javascript">
            alert('A rescis�o deste funcion�rio j� foi realizada.\nNome: ' + '<?php echo $retorno[1]['nome'] ?>');
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
$regiao = $_REQUEST['regiao'];
$Calc = new calculos();
$Curso = new tabcurso();
$Clt = new clt();
$ClasPro = new projeto();




if (empty($_REQUEST['tela'])) {
    $tela = 1;
} else {
    $tela = $_REQUEST['tela'];
}

if ($_GET['deletar'] == true) {
    $id_rescisao = $_GET['id'];
    //$movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_recisao WHERE id_recisao = '".$_GET['id']."' LIMIT 1"),0);
    //$total_movimentos = (int)count(explode(',',$movimentos));
    //mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '1' WHERE id_movimento IN('".$movimentos."') LIMIT ".$total_movimentos."");

    mysql_query("DELETE FROM rh_movimentos_rescisao WHERE id_clt = '" . $_GET['id_clt'] . "' ") or die(mysql_error());
    mysql_query("UPDATE rh_recisao SET status = '0' WHERE id_recisao = '$id_rescisao' LIMIT 1");
    mysql_query("UPDATE rh_clt SET status = '200', data_saida = '', status_demi = '' WHERE id_clt = '" . $_GET['id_clt'] . "' LIMIT 1");
}

// verifica se h� session iniciada
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

            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>

            <link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
            <link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>

            <script type="text/javascript" src="../../js/ramon.js"></script>
            <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
            <script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>

            <script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';

    $(function() {


        $('#dispensa').change(function() {

            var dispensa = parseInt($(this).val());

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


        $('#gerar').click(function() {

            var regiao = $('#regiao').val();
            var data_escolhida = $('#data_aviso').val();

            $.ajax({
                url: 'action.verifica_folha.php?data=' + data_escolhida + '&regiao=' + regiao,
                type: 'GET',
                dataType: 'json',
                success: function(resposta) {

                    if (parseInt(resposta.verifica) == 0) {

                        alert('A data escolhida ultrapassou o prazo de 30 dias ap�s a �ltima folha finalizada \n\n Data da �ltima folha: ' + resposta.data_ult_folha + '.');
                        $('#data_aviso').val('');

                        return false;
                    } else {

                        $('.form').submit();
                    }
                }
            });

        });


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

            </style>
    </head>
    <body class='novaintra'>
        <div id="corpo">

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
// Encriptografando a vari�vel
                    $link = str_replace('+', '--', encrypt("$regiao"));
                    ?>

                    <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                        <tr bgcolor="#999999">
                            <td colspan="4" class="show">
                                <span style="color:#F90; font-size:32px;">&#8250;</span> Relat�rio das rescis�es
                            </td>
                            <td class="show">
                                <a href="rel_rescisao_1.php?regiao=<?php echo $regiao; ?>" class="gerar_rel"> Gerar Relat�rio</a>
                                <a href="recisao_mes.php?regiao=<?php echo $regiao; ?>" class="gerar_rel2"> Relat�rio por M�s</a>
                            </td>
                        </tr>
                    </table>

                    <form action="" method="post" class="filtro">
                        <fieldset>
                            <legend>Filtro</legend>
                            <input type="hidden" name="filtro" value="1" />
                            <p><label class="first">Projeto:</label> <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($regiao, array("-1" => "Todos")), $projetoR, "id='projeto' name='projeto' class='required[custom[select]]'") ?></p>
                            <p class="controls"><input type="submit" value="Consultar" class="button" name="consultar" /></p>
                        </fieldset>
                    </form>



                    <?php
                    if (isset($_REQUEST['filtro']) && !empty($_REQUEST['filtro'])) {
                        // Consulta de Clts Aguardando Demiss�o
                        $qr_aguardo = mysql_query("SELECT * FROM rh_clt WHERE status = '200' AND id_regiao = '$regiao' $filtroProjeto ORDER BY nome ASC");
                        $total_aguardo = mysql_num_rows($qr_aguardo);

                        if (!empty($total_aguardo)) {
                            ?>

                            <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                                <tr bgcolor="#999999">
                                    <td colspan="6" class="show">
                                        <span style="color:#F90; font-size:32px;">&#8250;</span> Participantes aguardando a Rescis&atilde;o
                                    </td>
                                </tr>
                                <tr class="novo_tr">
                                    <td width="6%">COD</td>
                                    <td width="35%">NOME</td>
                                    <td width="20%">PROJETO</td>
                                    <td width="20%">UNIDADE</td>
                                    <td width="19%">CARGO</td>
                                </tr>

                                <?php
                                while ($row_aguardo = mysql_fetch_array($qr_aguardo)) {

                                    $Curso->MostraCurso($row_aguardo['id_curso']);
                                    $NomeCurso = $Curso->nome;

                                    $ClasPro->MostraProjeto($row_aguardo['id_projeto']);
                                    $NomeProjeto = $ClasPro->nome;

                                    // Encriptografando a vari�vel
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
                                        <td><a href="recisao2.php?tela=2&enc=<?= $link ?>"><?= $row_aguardo['nome'] ?></a></td>
                                        <td><?= $NomeProjeto ?></td>
                                        <td><?= $row_aguardo['locacao'] ?></td>
                                        <td><?= $NomeCurso ?></td>
                                    </tr>

                                <?php } ?>

                            </table>

                        <?php } ?>

                        <table width="95%" align="center" border="0" cellpadding="8" cellspacing="0" style="margin-top:20px;">
                            <tr bgcolor="#999999">
                                <td colspan="8" class="show">
                                    <span class="seta" style="color:#F90; font-size:32px;">&#8250;</span> Participantes Desativados
                                </td>
                            </tr>
                            <tr class="novo_tr">
                                <td width="6%">COD</td>
                                <td width="32%">NOME</td>
                                <td width="22%">PROJETO</td>
                                <td width="20%" align="center">DATA</td>
                                <td width="6%" align="center">RESCIS&Atilde;O</td>
                                <!--<td width="7%" align="center">COMPLEMENTAR</td>-->
                                <td>VALOR</td>
                                <td>&nbsp;</td>
                            </tr>

                            <?php
                            // Consulta de Clts que foram demitidos
                            $qr_demissao = mysql_query("SELECT *, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE status IN ('60','61','62','63','64','65','66','80','101') AND id_regiao = '$regiao' $filtroProjeto ORDER BY nome ASC");

                            while ($row_demissao = mysql_fetch_array($qr_demissao)) {

                                $Curso->MostraCurso($row_demissao['id_curso']);
                                $NomeCurso = $Curso->nome;

                                $ClasPro->MostraProjeto($row_demissao['id_projeto']);
                                $NomeProjeto = $ClasPro->nome;

                                $qr_rescisao = mysql_query("SELECT *, date_format(data_demi, '%d/%m/%Y') AS data_demi2 FROM rh_recisao WHERE id_clt = '$row_demissao[0]' AND status = '1'");
                                $row_rescisao = mysql_fetch_array($qr_rescisao);
                                $total_rescisao = mysql_num_rows($qr_rescisao);


                                $qr_rescisao_complementar = mysql_query("SELECT * FROM rh_rescisao_complementar WHERE rescisao_rescisao = '$row_rescisao[0]'");
                                $row_rescisao_complementar = mysql_fetch_array($qr_rescisao_complementar);
                                $total_rescisao_complementar = mysql_num_rows($qr_rescisao_complementar);

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
                                    <td><?= $row_demissao['campo3'] ?></td>
                                    <td><?= $row_demissao['nome'] ?></td>
                                    <td><?= $NomeProjeto ?></td>
                                    <td align="center"><?= $row_rescisao['data_demi2'] ?></td>
                                    <td align="center">


                                        <?php if (empty($total_rescisao)) { ?>
                                            <img src="../../imagens/pdf.gif" border="0" style="opacity:0.2; filter:alpha(opacity=20)">
                                            <?php } else { ?>
                                                <a href="<?= $link_nova_rescisao; ?>" class="link" target="_blank" title="Visualizar Rescis�o"><img src="../../imagens/pdf.gif" border="0"></a>
                                            <?php } ?>
                                    </td>
                                    <!--
                                    <td align="center">
                                    <?php if (!empty($total_rescisao_complementar)) { ?>
                                                                                                        <a href="../arquivos/recisaopdf/rescisao_<?= $row_demissao[0] ?>_1.pdf" class="link" target="_blank" title="Visualizar Rescis�o Complementar"><img src="../../imagens/pdf.gif" border="0"></a>
                                        <?php
                                    } else {
                                        $link = str_replace('+', '--', encrypt("$regiao&$row_demissao[0]&$row_rescisao[0]"));
                                        ?>
                                                                                                        <a href="recisao_complementar.php?enc=<?= $link ?>" class="link" target="_blank" title="Gerar Rescis�o Complementar"><img src="../../imagens/pdf2.gif" border="0"></a>
                                    <?php } ?>
                                    </td>
                                    -->
                                    <td>R$ <?php
                                        $total_recisao = $row_rescisao['total_liquido'];
                                        echo number_format($total_recisao, 2, ',', '.');
                                        $totalizador_recisao += $total_recisao;
                                        ?>
                                    </td>
                                    <td align="center">
                                        <?php if ($ACOES->verifica_permissoes(82)) { ?>
                                            <a href="recisao2.php?deletar=true&id=<?php echo $row_rescisao[0]; ?>&regiao=<?php echo $_GET['regiao']; ?>&id_clt=<?php echo $row_demissao[0]; ?>" title="Desprocessar Rescis�o" onclick="return window.confirm('Voc� tem certeza que quer desprocessar esta rescis�o?');"><img src="../imagensrh/deletar.gif" /></a>
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
                        </table>

                        <?php
                    }
                    break;
                case 2:
                    // tela de rescisao

                    list($regiao, $id_clt) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

                    verificaRecisao($id_clt);

                    $Clt->MostraClt($id_clt);
                    $nome = $Clt->nome;
                    $codigo = $Clt->campo3;
                    $data_demissao = $Clt->data_demi;
                    $contratacao = $Clt->tipo_contratacao;
                    $data_aviso_previo = $Clt->data_aviso;
                    $data_demissaoF = $Fun->ConverteData($data_demissao);


// Faltas no M�s
                    list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_demissao);

                    $qr_faltas = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '62' AND (status = '1' OR status = '5') AND mes_mov = '" . $mes_demissao . "' AND ano_mov = '" . $ano_demissao . "'");
                    $faltas = @mysql_result($qr_faltas, 0);




                    if ($dia_demissao > 30) {
                        $dias_trabalhados = 30;
                    } else {
                        $dias_trabalhados = $dia_demissao;
                    }

// Calculando Saldo FGTS
                    $qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
                    $fgts = number_format(mysql_result($qr_liquido, 0) * 0.08, 2, ',', '.');
                    ?>

                    <form action="recisao2.php" name="form1" method="post" onsubmit="return validaForm()">
                        <table cellpadding="4" cellspacing="0" style="width:80%; margin:0px auto; border:0; line-height:30px;">
                            <tr>
                                <td colspan="2" class="show" align="center"><?= $id_clt . ' - ' . $nome ?></td>
                            </tr>
                            <tr>
                                <td width="38%" class="secao">Tipo de Dispensa:</td>
                                <td width="62%">
                                    <select name="dispensa" id="dispensa">
                                        <option value="">Selecione...</option>
                                        <?php
                                        $qr_dispensa = mysql_query("SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY codigo ASC");
                                        while ($row_dispensa = mysql_fetch_array($qr_dispensa)) {
                                            ?>
                                            <option value="<?= $row_dispensa['codigo'] ?>">	<?= $row_dispensa['codigo'] ?>-<?= $row_dispensa['especifica'] ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Fator:</td>
                                <td>
                                    <select id="fator" name="fator" id="fator">
                                        <option value="empregado">empregado</option>
                                        <option value="empregador">empregador</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Dias de Saldo do Sal&aacute;rio:</td>
                                <td><input name="diastrab" type="text" id="diastrab" value="<?= abs($dias_trabalhados) ?>" size="1" maxlength="2"> dias (data para demiss�o: <?= $data_demissaoF ?>)</td>
                            </tr>
                            <tr>
                                <td class="secao">Remunera&ccedil;&atilde;o para Fins Rescis&oacute;rios:</td>
                                <td><input name="valor" type="text" id="valor" onkeydown="FormataValor(this, event, 17, 2)" value="0,00" size="6"/></td>
                            </tr>
                            <tr>
                                <td class="secao">Quantidade de Faltas:</td>
                                <td><input name="faltas" type="text" id="faltas" value="<?= $faltas ?>" size="2"/></td>
                            </tr>
                            <tr>
                                <td class="secao" >Aviso pr&eacute;vio:</td>
                                <td><select id="aviso" name="aviso" disabled="disabled">
                                        <option value=""></option>
                                        <option value="indenizado">Indenizado</option>
                                        <option value="trabalhado">Trabalhado</option>
                                    </select>
                                    <input name="previo" type="text" id="previo" size="1" maxlength="2" disabled="disabled"/> 
                                    dias de indeniza&ccedil;&atilde;o ou dias de trabalho
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Data do Aviso:</td>
                                <td><input type="text" id="data_aviso" name="data_aviso" size="8"
                                           onkeyup="mascara_data(this);
                                                   pula(10, this.id, devolucao.id)" disabled="disabled"/></td>
                            </tr>
                            <tr>
                                <td class="secao">Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido:</td>
                                <td><input name="devolucao" id="devolucao" size="6" onkeydown="FormataValor(this, event, 17, 2)" /></td>
                            </tr>

                            <tr>
                                <td colspan="2" align="center">
                                    <table width="50%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td><input type="submit" value="Avan�ar"  class="botao" /></td>
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
                    // tela de contabiliza��o da rescis�o

                    $id_clt = $_REQUEST['idclt'];
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

                    verificaRecisao($id_clt);

//////DADOS DO CLT
                    $ano_atual = date('Y');
                    $qr_clt = mysql_query("SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
                        DATE_FORMAT(data_demi, '%d/%m/%Y') as data_demiF, 
                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
                        A.salario_outra_empresa, A.desconto_outra_empresa,                     

                        IF(DATEDIFF(data_demi, data_entrada) >= 365, 1, 0) as um_ano,                      
                        B.salario, B.nome as nome_funcao,
                        /*Verifica se o clt recebeu DT*/
                        (SELECT Count(a.id_clt) FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = 5192 AND a.ano = YEAR(A.data_demi) AND a.status = '3' AND b.terceiro = 1) as verifica_dt,
                        ROUND( DATEDIFF(data_demi, data_entrada) / 30) as meses_dt,
                        

                        /*CALCULO PARA O ART. 479 E ART. 480 */
                        IF( (A.data_demi = DATE_ADD(A.data_entrada, INTERVAL + 44 DAY)) OR (A.data_demi = DATE_ADD(A.data_entrada, INTERVAL + 89 DAY)),0,
                                IF(A.data_demi <= DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),
                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 44 DAY),data_demi),
                                 DATEDIFF (DATE_ADD(A.data_entrada, INTERVAL + 89 DAY),data_demi))
                        ) 
                        AS dias_restantes,
                        
                  /*MESES TRABALHADOS*/
                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
                              FROM 
                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
                                CONCAT(YEAR(data_demi), SUBSTR(data_demi,6,2) ) as demissao,
                                data_entrada, data_demi
                                FROM rh_clt WHERE id_clt = $id_clt) as folha
                        ) as meses_trabalhados
                        

                        FROM rh_clt as A 
                        INNER JOIN curso as B
                        ON B.id_curso = A.id_curso
                        WHERE id_clt = '$id_clt' ") or die(mysql_error());
                    $row_clt = mysql_fetch_assoc($qr_clt);


                    $Curso->MostraCurso($row_clt['id_curso']);
                    $nome = $row_clt['nome'];
                    $codigo = $row_clt['campo3'];
                    $data_demissao = $row_clt['data_demi'];
                    $data_entrada = $row_clt['data_entrada'];
                    $idprojeto = $row_clt['id_projeto'];
                    $idcurso = $row_clt['id_curso'];
                    $idregiao = $row_clt['id_regiao'];
                    $data_demissaoF = $row_clt['data_demiF'];
                    $data_entradaF = $row_clt['data_entradaF'];
                    $clt_insalubridade = $row_clt['insalubridade'];
                    $um_ano = ($dispensa == 63 or $dispensa == 64 or $dispensa == 66) ? 2 : $row_clt['um_ano'];
                    $dias_restantes = $row_clt['dias_restantes']; //USADO NO CALCULO DO ART. 479 e 480
                    //
                
///////////////////////////////
////////CONFIG////////////////
//////////////////////////////
                    $restatus = mysql_query("SELECT A.especifica, A.codigo_saque, B.* FROM rhstatus as A
                            INNER JOIN rescisao_config as B 
                            ON A.codigo = B.tipo
                            WHERE A.codigo = '$dispensa' AND ano = '$um_ano'");
                    $row_status = mysql_fetch_assoc($restatus);

                    $t_ss = $row_status['saldodesalario']; // SALDO SALARIO
                    $t_ap = $row_status['avisoprevio']; // AVISO PREVIO
                    $t_fv = $row_status['feriasvencidas'];
                    ; // FERIAS VENCIDAS
                    $t_fp = $row_status['feriasproporcionais']; // FERIAS PROPORCIONAIS
                    $t_fa = $row_status['adicionaldeferias']; // FERIAS 1/3 ADICIONAL
                    $t_13 = $row_status['13salario']; // DECIMO TERCEIRO
                    $t_familia = $row_status['salariofamilia'];
                    $t_f8 = 0; // FGTS 8
                    $t_f4 = 0; // FGTS MULTA 40
                    $t_479 = $row_status['indenizacao479']; //INDENIZACAO ART. 479
                    $t_480 = $row_status['indenizacao480']; //INDENIZACAO ART. 480







                    $cod_saque_fgts = $row_status['codigo_saque'];

                    switch ($dispensa) {
                        case 60: $cod_mov_fgts = 'H';
                            $cod_saque_fgts = '00';
                            break;

                        case 61: $cod_mov_fgts = '11';
                            $cod_saque_fgts = '01';
                            break;

                        case 62:
                        case 81: $cod_mov_fgts = '11';
                            break;

                        case 63: $cod_mov_fgts = '01';
                            break;

                        case 64:
                            $cod_mov_fgts = '01';
                            $cod_saque_fgts = '04';
                            break;

                        case 65:
                            $cod_mov_fgts = '01';
                            break;

                        case 66: $cod_mov_fgts = '01';
                            break;

                        case 101: $cod_mov_fgts = '01';
                            break;
                    }



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

/////////////////////////////////
//// DIAS TRABALHADOS  ///////////
/////////////////////////////////
                    if ($mes_admissao == $mes_demissao and $ano_demissao == $ano_admissao) {

                        $dias_trabalhados = (int) (($data_demissao_seg - $data_admissao_seg) / 86400) + 1;
                        $dias_trabalhados = $dias_trabalhados - $faltas;
                    } else {
                        if ((int) $mes_demissao == 2 and $dia_demissao >= 28) {

                            $dias_trabalhados = 30;
                        } else {

                            $dias_trabalhados = ($dia_demissao == 31) ? 30 : $dia_demissao;
                            $dias_trabalhados = $dias_trabalhados - $faltas;
                        }
                    }



////////////////
/////////////////////
////SALARIO BASE  ///
/////////////////////
                    if ($valor == '0,00') {
                        $salario_base_limpo = $row_clt['salario'];
                    } else {
                        $valor = str_replace(',', '.', str_replace('.', '', $valor));
                        $salario_base_limpo = $valor;
                    }



                    $valor_faltas = ($salario_base_limpo / $qnt_dias_mes) * $faltas;



///////////////////////
///INSALUBRIDADE/////
//////////////////////

                

                    $verifica_insalu_prop = date('Y-m-d', mktime(0, 0, 0, $mes_admissao, $dia_admissao + 30, $ano_admissao));
                    if ($clt_insalubridade == 1) {

                        if ($Curso->tipo_insalubridade == 1) {
                            $qr_mov = mysql_query("SELECT (fixo * 0.20) as integral, ((fixo * 0.20)/30) as valor_diario FROM rh_movimentos WHERE cod = '0001' AND anobase = '$ano_demissao'") or die(mysql_error());
                            $row_mov = mysql_fetch_assoc($qr_mov);
                            $valor_insalubridade_integral = $row_mov['integral'];
                            $valor_insalubridade = $row_mov['valor_diario'] * ($dias_trabalhados);
                        } elseif ($Curso->tipo_insalubridade == 2) {

                            $qnt_salario = $Curso->qnt_salminimo_insalu;
                            $qr_mov = mysql_query("SELECT ((fixo * $qnt_salario)* 0.40) as integral, (((fixo * $qnt_salario)* 0.40) /30) as valor_diario FROM rh_movimentos WHERE cod = '0001' AND anobase = '$ano_demissao'") or die(mysql_error());
                            $row_mov = mysql_fetch_assoc($qr_mov);
                            $valor_insalubridade_integral = $row_mov['integral'];
                            $valor_insalubridade = $row_mov['valor_diario'] * ($dias_trabalhados);  
                        }
                    }


/////////////////////
// MOVIMENTOS FIXOS /////
///////////////////

                    $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

                    while ($row_folha = mysql_fetch_assoc($qr_folha)) {

                        if (!empty($row_folha[ids_movimentos_estatisticas])) {

                            $qr_movimentos = mysql_query("SELECT *
                                       FROM rh_movimentos_clt
                                       WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = $id_clt AND id_mov NOT IN(56,200) ");
                            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento'];
                            }
                        }
                    }

                    if (sizeof($movimentos) > 0) {
                        $total_rendi = (array_sum($movimentos) / 12);
                    } else {
                        $total_rendi = 0;
                    }

                    if ($id_clt == 4765) {
                        $total_rendi = 144.80;
                    }
                    if ($id_clt == 6938) {
                        $total_rendi = 0;
                    }
/////////////////////
// FIM MOVIMENTOS FIXOS /////



                    if ($array_valores_rendimentos == '') {
                        $array_valores_rendimentos[] = '0';
                    }


//////////////////////////////
////SAL�RIO FAM�LIA ///////
///////////////////////////
                    if ($t_familia == 1) {

                        $Calc->Salariofamilia($salario_base_limpo, $id_clt, $idprojeto, $data_demissao, '2');
                        $valor_sal_familia = (($Calc->valor) / $qnt_dias_mes) * $dias_trabalhados;
                        if ($valor_sal_familia > 0) {
                            $TOTAL_MENOR = $Calc->filhos_menores;
                        }
                    }


///ARTIGO 479 e 480 PARA RESCIS�O ANTECIPADA 
                    $valor_art_480_479 = (($salario_base_limpo) / 30) * ($dias_restantes / 2);

                    $total_dias = ($data_demissao_seg - $data_admissao_seg) / 86400;




                    if ($t_479 == 1) {


                        $art_479 = $valor_art_480_479;
                        $art_480 = NULL;
                        $to_rendimentos += $art_479;
                    } elseif ($t_480 == 1) {


                        $art_479 = NULL;
                        $art_480 = $valor_art_480_479;
                        $to_descontos += $art_480;
                    }


//////////////////////////////////////
//////////////SALDO DE SAL�RIO///////
/////////////////////////////////////
                    if ($t_ss == 1) {
                        $valor_salario_dia = $salario_base_limpo / $qnt_dias_mes;
                        $saldo_de_salario = $valor_salario_dia * $dias_trabalhados;
                    }



// D�cimo Terceiro (DT)
///Verifica se  a pesssoa recebeu d�cimo terceiro no ano
                    $qr_verifica_13_folha = mysql_query("SELECT a.id_clt,SUM(a.salliquido) as sal_liquido,b.data_fim,tipo_terceiro
                                    FROM rh_folha_proc a
                                    INNER JOIN rh_folha b ON a.id_folha = b.id_folha
                                    WHERE a.id_clt = $id_clt AND a.ano = " . date('Y') . " AND a.status = '3' AND b.terceiro = 1
                                    ORDER BY b.tipo_terceiro DESC") or die(mysql_error());
                    $row_veri_decimo = mysql_fetch_assoc($qr_verifica_13_folha);
                    $verifica_13_folha = mysql_num_rows($qr_verifica_13_folha);


///Verifica se  a pesssoa recebeu d�cimo terceiro em novembro
                    if ($t_13 == 1) {

                        if ($row_veri_decimo['tipo_terceiro'] == 1) {
                            $valor_decimo_folha = $row_veri_decimo['sal_liquido'];
                        } else {
                            $valor_decimo_folha = 0;
                        }


                        $dt_entrada_calc = (($ano_demissao == $ano_admissao) or $verifica_13_folha == 0 ) ? $data_entrada : $ano_demissao . '-01-01';
                        //Quantidade de mese
                        $Calc->Calc_qnt_meses_13_ferias_rescisao($dt_entrada_calc, $data_demissao);
                        $meses_ativo_dt = $Calc->meses_ativos;

                        if ($aviso == 'indenizado') {

                            // D�cimo Terceiro Saldo de Sal�rio (Indenizado)
                            $qnt_13_indenizado = 1;
                            $valor_13_indenizado = ($salario_base_limpo + $valor_insalubridade_integral + $total_rendi) / 12;

                            if ($dispensa == 65) {
                                $total_avos_13_indenizado = "0";
                                $total_valor_13_indenizado = NULL;
                                $valor_13_indenizado = 0;
                            } else {
                                $total_avos_13_indenizado = $qnt_13_indenizado;
                                $total_valor_13_indenizado = $valor_13_indenizado;
                            }
                        }



                        $valor_td = (($salario_base_limpo + $valor_insalubridade_integral + $total_rendi) / 12) * $meses_ativo_dt;
                        $BASE_CALC_INSS_13 = $valor_td + $valor_13_indenizado;
                        // Calculando INSS sobre DT
                        $Calc->MostraINSS($BASE_CALC_INSS_13, $data_demissao);
                        $valor_td_inss = $Calc->valor;
                        $PERCENTUAL_INSS_13 = $Calc->percentual;

                        // Calculando IRRF sobre DT
                        $BASE_CALC_IRRF_13 = $BASE_CALC_INSS_13 - $valor_td_inss;
                        $Calc->MostraIRRF($BASE_CALC_IRRF_13, $id_clt, $idprojeto, $data_demissao);
                        $valor_td_irrf = $Calc->valor;

                        if ($valor_td_irrf > 0) {
                            $PERCENTUAL_IRRF_13 = $Calc->percentual;
                            $QNT_DEPENDENTES_IRRF_13 = $Calc->total_filhos_menor_21;
                            $VALOR_DDIR_13 = $Calc->valor_deducao_ir_total;
                            $PARCELA_DEDUCAO_IR_13 = $Calc->valor_fixo_ir;
                        } else {
                            $BASE_CALC_IRRF_13 = 0;
                        }

                        // Valor do DT
                        $total_dt = $valor_td - $valor_td_inss - $valor_td_irrf;
                        $to_descontos = $to_descontos + $valor_td_inss + $valor_td_irrf;
                        $to_rendimentos = $to_rendimentos + $valor_td;
                    } else {

                        $total_dt = 0;
                        $meses_ativo_dt = 0;
                    }
// Fim de D�cimo Terceiro (DT)
////////////////////
////  F�RIAS  /////
//////////////////

                   

// Verificando Direito de F�rias
                    $qr_verifica_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = 1 ORDER BY id_ferias DESC");
                    $total_verifica_ferias = mysql_num_rows($qr_verifica_ferias);

                    if (empty($total_verifica_ferias)) {

                        $aquisitivo_ini = $data_entrada;
                        $aquisitivo_end = date('Y-m-d', strtotime("" . $data_entrada . " +1 year"));
                    } else {

                        $aquisitivo_ini = date('Y-m-d', strtotime("" . $data_entrada . " + " . $total_verifica_ferias . " year"));
                        $aquisitivo_end = date('Y-m-d', strtotime("" . $data_entrada . " + " . ($total_verifica_ferias + 1) . " year"));
                    }



// Verificando Per�odos Gozados
                    while ($periodos = mysql_fetch_assoc($qr_verifica_ferias)) {


                        $periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
                     
                    }


// Verificando Per�odos Aquisitivos, Per�odos Vencidos e Per�odo Proporcional

                    $quantidade_anos = (date('Y') - $ano_admissao) + 1;

                    for ($a = 0; $a < $quantidade_anos; $a++) {

                        $aquisitivo_inicio = date('Y-m-d', strtotime("$data_entrada + $a year"));
                        $aquisitivo_final = date('Y-m-d', mktime('0', '0', '0', $mes_admissao, $dia_admissao - 1, $ano_admissao + $a + 1));

                        if ($aquisitivo_final > $data_demissao) {

                            $periodo_aquisitivo = $aquisitivo_inicio . '/' . $data_demissao;
                            $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $data_demissao;
                        } else {

                            $periodo_aquisitivo = $aquisitivo_inicio . '/' . $aquisitivo_final;
                            $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                        }

                        if (@!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final < $data_demissao) {

                            $periodos_vencidos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                        } elseif ($aquisitivo_final >= $data_demissao and $aquisitivo_inicio < $data_demissao) {

                            $periodo_proporcional[] = $aquisitivo_inicio . '/' . $data_demissao;
                        }
                    }


// Buscando Faltas
                    include('faltas_rescisao.php');
// Fim da Verifica��o de F�rias
// F�rias Vencidas
                    
                    if($id_clt == 5051 ){
                        
                        $t_fv = 0;
                        $t_fp =0;
                    }
                    if ($t_fv == 1) {

                        $total_periodos_vencidos = count($periodos_vencidos);
                     
                        if (empty($total_periodos_vencidos)) {

                            $ferias_vencidas = 'n�o';
                            $fv_valor_base = 0;
                            $fv_um_terco = 0;
                        } elseif ($total_periodos_vencidos == 1) {

                            $ferias_vencidas = 'sim';
                            $fv_valor_base = (($salario_base_limpo + $valor_insalubridade + $total_rendi) / $qnt_dias_mes) * $qnt_dias_fv;
                            $fv_um_terco = $fv_valor_base / 3;
                            $fv_total = $fv_valor_base + $fv_um_terco;
                        } elseif ($total_periodos_vencidos > 1) {

                            $ferias_vencidas = 'sim';
                            $fv_valor_base = ((($salario_base_limpo - $valor_insalubridade + $total_rendi) / $qnt_dias_mes) * $qnt_dias_fv );
                            $fv_um_terco = $fv_valor_base / 3;

                            $fv_um_terco_dobro = ($fv_valor_base / 3) * $total_periodos_vencidos;
                            $multa_fv = ((($salario_base_limpo + $valor_insalubridade) / $qnt_dias_mes) * $qnt_dias_fv) * $total_periodos_vencidos;

                            $fv_total = $fv_valor_base + $fv_um_terco + $fv_um_terco_dobro;
                        }
                    } else {

                        $fv_total = 0;
                        $fv_valor_base = 0;
                        $fv_um_terco = 0;
                    }
// Fim de F�rias Vencidas
//////////////////////////////
//F�RIAS PROPORCIONAIS /////
///////////////////////////////
                    if ($t_fp == 1) {
                       

                        list($periodo_proporcional_inicio, $periodo_proporcional_final) = explode('/', $periodo_proporcional[0]);

                        $Calc->Calc_qnt_meses_13_ferias_rescisao($periodo_proporcional_inicio, $periodo_proporcional_final);
                        $meses_ativo_fp = $Calc->meses_ativos;


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

                        $fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $total_rendi) / $qnt_dias_mes) * $qnt_dias_fp;
                        $fp_valor_total = ($fp_valor_mes / 12) * $meses_ativo_fp;
                       

                        ///F�rias (aviso_indenizado)
                        if ($aviso == 'indenizado' and $total_meses != 12 and $dispensa != 65 and $dispensa != 63 and $dispensa != 64 and $dispensa != 66) {

                            $ferias_aviso_indenizado = $fp_valor_mes / 12;
                        }


                        if ($t_fa == 1) {
                            $fp_um_terco = $fp_valor_total / 3;
                            $fp_total = $fp_valor_total + $fp_um_terco;
                        } else {
                            $fp_total = $fp_valor_total;
                        }
                    } else {
                        $fp_total = 0;
                    }


// C�lculo de F�rias
                    $ferias_total = $fp_total + $fv_total + $ferias_aviso_indenizado;
                    $to_rendimentos = $to_rendimentos + $fv_valor_base + $fp_valor_total + $fp_um_terco + $fv_um_terco + $fv_um_terco_dobro + $multa_fv + $ferias_aviso_indenizado;
                    $to_descontos = $to_descontos;
// Fim de F�rias
// Fim de F�rias Proporcionais
//////ACERTANDO A PARTIR DAQUI (AVISO PR�VIO)
                    if ($t_ap == 1 and $aviso == 'indenizado') {

                        $valor_aviso_previo = $salario_base_limpo + $valor_insalubridade_integral;

                        if ($dispensa == 65) {
                            $aviso = "PAGO pelo funcion�rio";
                            $valor_ap_pago_trab = $valor_aviso_previo;
                        } else {
                            $valor_ap_recebido_trab = $valor_aviso_previo;

                            ///NOVA REGRA DO AVISO PR�VIO 
                            $diferenca_anos = ($data_demissao_seg - $data_admissao_seg) / 31536000;
                            for ($d = 1; $d <= (int) $diferenca_anos; $d++) {
                                $lei_12_506 += ($valor_aviso_previo / $qnt_dias_mes) * 3;
                            }
                        }
                    } elseif ($aviso == 'trabalhado' or $t_ap == 0) {
                        $valor_aviso_previo = NULL;
                        $total_avos_13_indenizado = "0";
                        $total_valor_13_indenizado = NULL;
                        $valor_ap_recebido_trab = NULL;
                        $valor_ap_pago_trab = NULL;
                    }




                    $to_descontos = $to_descontos + $valor_ap_pago_trab;
                    $to_rendimentos = $to_rendimentos + $valor_ap_recebido_trab + $total_valor_13_indenizado;
// Fim Aviso Pr�vio
// Atraso no Pagamento da Rescis�o
                    $data_aviso_previo_1 = date('Y-m-d', strtotime("$data_demissao +1 days"));
                    $data_aviso_previo_10 = date('Y-m-d', strtotime("$data_demissao +10 days"));


               
                    
                    
                    /* ANTES
                     *  ($data_hoje > $data_aviso_previo_1 and $dispensa == 66)  or
                      ($t_ap == 1 and $aviso == 'trabalhado') or
                      ($data_hoje > $data_aviso_previo_10 and $t_ap == 1 and $aviso == 'indenizado')
                     */
                    
                    if (
                            ($data_hoje >= $data_aviso_previo_1 and $dispensa == 66) or ( $data_hoje >= $data_aviso_previo_10 and $t_ap == 1 )
                    ) {
                        $valor_atraso = $salario_base_limpo;
                    }




///OUTROS EVENTOS
                        $result_total_evento = mysql_query("SELECT *,IF(lancamento = 1,'LAN�ADO','') as tipo_lancamento                                     
                                     FROM rh_movimentos_clt
                                    WHERE (mes_mov = '16'  AND id_mov NOT IN(56)AND status = '1' AND id_clt = '$id_clt')                                   
                                    and id_mov NOT IN (56)
                                    ORDER BY nome_movimento;") or die(mysql_error());
                        $total_result = mysql_num_rows($result_total_evento);

                        while ($row_total_evento = mysql_fetch_array($result_total_evento)) {

                            $cor = ($i++ % 2 == 0) ? '#eeeeee' : '#f4f4f4';

                            $movimentos[] = $row_total_evento['id_movimento'];  //usado para gravar os movimentos da rescis�o na tabela rh_movimentos_recisao

                            if ($row_total_evento['tipo_movimento'] == 'CREDITO') {

                                $mov_cod[] = $row_total_evento['cod_movimento'];
                                $mov_nome[] = $row_total_evento['nome_movimento'];
                                $mov_tipo[] = $row_total_evento['tipo_lancamento'];
                                $mov_incidencia[] = ($row_total_evento['incidencia'] != ',,') ? 'INSS, IRRF, FGTS' : '';
                                $mov_rend[] = $row_total_evento['valor_movimento'];
                                $mov_desc[] = NULL;
                                $to_mov_rendimentos += $row_total_evento['valor_movimento'];

                                if ($row_total_evento['incidencia'] != ',,') {
                                    $total_mov_lancado += $row_total_evento['valor_movimento'];
                                }
                            } elseif ($row_total_evento['tipo_movimento'] == 'DESCONTO' or $row_total_evento['tipo_movimento'] == 'DEBITO') {
                                $mov_cod[] = $row_total_evento['cod_movimento'];
                                $mov_incidencia[] = ($row_total_evento['incidencia'] != ',,') ? 'INSS, IRRF, FGTS' : '';
                                $mov_nome[] = $row_total_evento['nome_movimento'];
                                $mov_tipo[] = $row_total_evento['tipo_lancamento'];
                                $mov_desc[] = $row_total_evento['valor_movimento'];
                                $mov_rend[] = NULL;
                                $to_mov_descontos += $row_total_evento['valor_movimento'];

                                if ($row_total_evento['incidencia'] != ',,') {
                                    $total_mov_lancado -= $row_total_evento['valor_movimento'];
                                }
                            }
                        }
                    
///////////////////////////////////////////////
////////// C�LCULO DE INSS E IRRF /////////////
///////////////////////////////////////////////



                    $BASE_CALC_INSS_SALDO_SALARIO = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado; // + $valor_ap_recebido_trab+ $lei_12_506;
                    $BASE_CALC_IRRF_SS = $saldo_de_salario + $valor_insalubridade + $total_mov_lancado;


                    $Calc->MostraINSS($BASE_CALC_INSS_SALDO_SALARIO, implode('-', $data_exp));
                    $inss_saldo_salario = $Calc->valor;
                    $PERCENTUAL_INSS_SS = $Calc->percentual;

                    if ($row_clt['desconto_outra_empresa'] + $inss_saldo_salario > $Calc->teto) {

                        $inss_saldo_salario = ($Calc->teto - $row_clt['desconto_outra_empresa'] );
                    }





                    $BASE_CALC_IRRF_SALDO_SALARIO = $BASE_CALC_IRRF_SS - $inss_saldo_salario;
                    $Calc->MostraIRRF($BASE_CALC_IRRF_SALDO_SALARIO, $id_clt, $idprojeto, $data_demissao);
                    $irrf_saldo_salario = $Calc->valor;
                    if ($irrf_saldo_salario > 0) {
                        $PERCENTUAL_IRRF_SS = $Calc->percentual;
                        $QNT_DEPENDENTES_IRRF_SS = $Calc->total_filhos_menor_21;
                        $VALOR_DDIR_SS = $Calc->valor_deducao_ir_total;
                        $PARCELA_DEDUCAO_IR_SS = $Calc->valor_fixo_ir;
                    } else {
                        $BASE_CALC_IRRF_SALDO_SALARIO = 0;
                    }


//////////////
////TOTAIS ///
/////////////
                    $TOTAL_SALDO_SALARIO = $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario;
                    $TOTAL_DECIMO_PROPORCIONAL = ($valor_td + $valor_13_indenizado) - $valor_td_inss - $valor_td_irrf - $valor_decimo_folha;
                    $TOTAL_FERIAS = $fv_valor_base + $fv_um_terco + $fp_valor_total + $fp_um_terco + $multa_fv + $fv_um_terco_dobro + $ferias_aviso_indenizado;
                    $TOTAL_OUTROS_VENCIMENTOS = $valor_sal_familia + $valor_atraso + $valor_outro + $valor_ap_recebido_trab + $valor_insalubridade + $lei_12_506;
                    $TOTAL_OUTROS_DESCONTOS = $total_outros_descontos;


                    $to_descontos = $valor_faltas + $inss_saldo_salario + $irrf_saldo_salario + $valor_td_inss + $valor_td_irrf + $total_outros_descontos + $art_480 + $valor_ap_pago_trab + $to_mov_descontos + $valor_decimo_folha;
                    $to_rendimentos = $saldo_de_salario + $valor_td + $valor_13_indenizado + $TOTAL_FERIAS + $TOTAL_OUTROS_VENCIMENTOS + $to_mov_rendimentos + $art_479;



//////////////////////
///VALOR FINAL//////
/////////////////////
                    $valor_rescisao_final = $to_rendimentos - $to_descontos;

                    if ($valor_rescisao_final < 0) {

                        $arredondamento_positivo = abs($valor_rescisao_final);

                        if ($dispensa == 60) {
                            $valor_rescisao_final = $aviso_previo_valor_d;
                        } else {
                            $valor_rescisao_final = NULL;
                        }
                        $to_rendimentos = $to_rendimentos + $arredondamento_positivo;
                        $valor_rescisao_final = NULL;
                    } else {
                        $arredondamento_positivo = NULL;
                        $valor_rescisao_final = $to_rendimentos - $to_descontos;
                    }
                    ?>

                    <form action="acao.php" method="post" name="Form" id="Form">
                        <table cellpadding="0" cellspacing="0" style="background-color:#FFF; margin:0px auto; width:80%; border:0; line-height:24px;">
                            <tr>
                                <td colspan="4" class="show" align="center"><?= $id_clt . ' - ' . $nome ?></td>
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
                                <td class="secao">M�dia dos movimentos fixos:</td>
                                <td>R$ <?= formato_real($total_rendi) ?> 
                                    <a href="action.ver_rendimentos_1.php?clt=<?php echo $id_clt; ?>&m_trab=<?php echo $row_clt['meses_trabalhados']; ?>" id="ver_rend" onClick="return hs.htmlExpand(this, {objectType: 'iframe', width: 400, height: 300})" title="M�dia dos movimentos">
                                        <img src="../../imagensmenu2/visualizar.gif" width="13" height="13"/>
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
                                    RENDIMENTOS: R$ <?= formato_real($to_rendimentos); ?><br />
                                    DESCONTOS: R$ <?= formato_real($to_descontos) ?></td>
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
                                    Saldo de sal&aacute;rio (<?= $dias_trabalhados ?>/<?php echo $qnt_dias_mes; ?>):          

                                </td>
                                <td>
                                    R$ <?= formato_real($saldo_de_salario) ?> <?php
                                    if (!empty($faltas)) {
                                        echo '(' . $faltas . ' faltas)';
                                    }
                                    ?>
                                </td>
                                <td class="secao">INSS:</td>
                                <td>R$ <?= formato_real($inss_saldo_salario) ?> </td>
                            </tr>
                            <tr>    
                                <td colspan="2" align="center">  <?php
                                    if ($row_clt['desconto_inss'] == 1) {
                                        echo '<br><strong>**Possui desconto de INSS em outra empresa</strong>';
                                        echo '<br><strong>Sal�rio na outra empresa: </strong> R$ ' . formato_real($row_clt['salario_outra_empresa']);
                                        echo '<br><strong>INSS na outra empresa: </strong> R$ ' . formato_real($row_clt['desconto_outra_empresa']);
                                    }
                                    ?> </td>

                                <td class="secao">IRRF:</td>
                                <td colspan="3">R$ <?= formato_real($irrf_saldo_salario) ?></td>        
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
                                                    <td align="center">INCID�NCIA</td>
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
                                <td colspan="4" class="divisor">D�cimo terceiro</td>
                            </tr>
                            <tr>
                                <td class="secao">D�cimo terceiro proporcional (<?= $meses_ativo_dt ?>/12):</td>
                                <td>R$ <?php echo formato_real($valor_td) ?></td>
                                <td class="secao">13&ordm; Saldo Indenizado (<?= $total_avos_13_indenizado; ?>/12):</span></td>
                                <td>R$ <?= formato_real($total_valor_13_indenizado); ?></td>       
                            </tr>
                            <tr>
                                <td class="secao">INSS:</td>
                                <td>R$ <?php echo formato_real($valor_td_inss); ?></td>
                                <td class="secao">IRRF:</td>
                                <td colspan="3">R$ <?php echo number_format($valor_td_irrf, 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <?php if (!empty($valor_decimo_folha)) { ?>
                                    <td class="secao">ADIANTAMENTO DE 13�:</td>
                                    <td>R$ <?= formato_real($valor_decimo_folha) ?></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td colspan="4" align="center">
                                    <span style="font-size:14px; font-weight:bold;">R$ <?= formato_real($TOTAL_DECIMO_PROPORCIONAL) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" align="left"><div class="divisor">F�rias</div></td>
                            </tr>
                            <tr <?= $style_fv ?>>
                                <td class="secao">F�rias vencidas:</td>
                                <td>R$ <?= formato_real($fv_valor_base) ?></td>
                                <td class="secao">1/3 sobre f�rias vencidas:</td>
                                <td>R$ <?= formato_real($fv_um_terco) ?></td>
                            </tr>
                            <tr <?= $style_fp ?>>
                                <td class="secao">F�rias proporcionais (<?= $meses_ativo_fp ?>/12): </td>
                                <td>R$ <?= formato_real($fp_valor_total) ?></td>
                                <td class="secao">1/3 sobre f�rias proporcionais:</td>
                                <td>R$ <?= formato_real($fp_um_terco) ?></td>
                            </tr>
                            <tr>
                                <td class="secao">F&eacute;rias em Dobro:</td>
                                <td>R$ <?= formato_real($multa_fv) ?></td>
                                <td class="secao"> 1/3 sobre f&eacute;rias em Dobro:</td>
                                <td>R$ <?= formato_real($fv_um_terco_dobro) ?></td>       
                            </tr> 
                            <tr>
                                <tr>
                                    <td class="secao"> F�rias Aviso Indenizado (1/12):</td>
                                    <td>R$ <?= formato_real($ferias_aviso_indenizado) ?></td>

                                </tr>


                                <?php if ($_COOKIE['logado'] == 87) { ?>
                                                                                                                                                                        <!--<tr>
                                                                                                                                                                            <td class="secao">Faltas no per&iacute;odo de f&eacute;rias proporcionais:</td>
                                                                                                                                                                            <td><?= $faltas_ferias ?></td>
                                                                                                                                                                        <td class="secao">Dias de f&eacute;rias recebido:</td>
                                                                                                                                                                        <td><?= $qnt_ferias ?> dias</td>
                                                                                                                                                                        </tr>-->
                                <?php } ?>
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
                                    <td class="secao">Insalubridade / Periculosidade:</td>
                                    <td>R$ <?= formato_real($valor_insalubridade) ?></td>
                                </tr>
                                <tr>
                                    <td class="secao">Aviso Pr�vio:</td>
                                    <td>R$ <?= formato_real($valor_ap_recebido_trab) ?></td>
                                    <td class="secao">Lei n� 12.506:</td>
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
                                    <td class="secao">Aviso Pr�vio pago pelo Funcion&aacute;rio:</td>
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
                                    <td class="secao">C�digo de Saque:</td>
                                    <td><?= $cod_saque_fgts ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="center">
                                        <p>&nbsp;</p>

                                        <?php
                                        $CAMPOS_INSERT['id_clt'] = $id_clt;
//$campos_insert['ajuda_custo']       =  $ajuda_custo;
                                        $CAMPOS_INSERT['nome'] = $nome;
                                        $CAMPOS_INSERT['id_regiao'] = $idregiao;
                                        $CAMPOS_INSERT['id_projeto'] = $idprojeto;
                                        $CAMPOS_INSERT['id_curso'] = $idcurso;
                                        $CAMPOS_INSERT['data_adm'] = $data_entrada;
                                        $CAMPOS_INSERT['data_demi'] = $data_demissao;
                                        $CAMPOS_INSERT['data_proc'] = date('Y-m-d');
                                        $CAMPOS_INSERT['dias_saldo'] = $dias_trabalhados;
                                        $CAMPOS_INSERT['um_ano'] = $um_ano;
//$campos_insert['meses_ativo']       =  $meses_ativo;
                                        $CAMPOS_INSERT['motivo'] = $dispensa;
                                        $CAMPOS_INSERT['fator'] = $fator;
                                        $CAMPOS_INSERT['aviso'] = $aviso;
                                        $CAMPOS_INSERT['aviso_valor'] = $valor_aviso_previo;
                                        $CAMPOS_INSERT['dias_aviso'] = $previo;
                                        $CAMPOS_INSERT['data_fim_aviso'] = $data_fim_avprevio;
                                        $CAMPOS_INSERT['fgts8'] = $fgts8_totalT;
                                        $CAMPOS_INSERT['fgts40'] = $fgts4_totalT;
                                        $CAMPOS_INSERT['fgts_anterior'] = $anterior;
                                        $CAMPOS_INSERT['fgts_cod'] = $cod_mov_fgts;
                                        $CAMPOS_INSERT['fgts_saque'] = $cod_saque_fgts;
                                        $CAMPOS_INSERT['sal_base'] = $salario_base_limpo;
                                        $CAMPOS_INSERT['saldo_salario'] = $saldo_de_salario;
                                        $CAMPOS_INSERT['inss_ss'] = $inss_saldo_salario;
                                        $CAMPOS_INSERT['previdencia_ss'] = $inss_saldo_salario;
                                        $CAMPOS_INSERT['ir_ss'] = $irrf_saldo_salario;
                                        $CAMPOS_INSERT['terceiro_ss'] = $valor_13_indenizado;
                                        $CAMPOS_INSERT['dt_salario'] = $valor_td;
                                        $CAMPOS_INSERT['inss_dt'] = $valor_td_inss;
                                        $CAMPOS_INSERT['previdencia_dt'] = $valor_td_inss;
                                        $CAMPOS_INSERT['ir_dt'] = $valor_td_irrf;
                                        $CAMPOS_INSERT['ferias_vencidas'] = $fv_valor_base;
                                        $CAMPOS_INSERT['umterco_fv'] = $fv_um_terco;
                                        $CAMPOS_INSERT['ferias_pr'] = $fp_valor_total;
                                        $CAMPOS_INSERT['umterco_fp'] = $fp_um_terco;
                                        $CAMPOS_INSERT['sal_familia'] = $valor_sal_familia;
                                        $CAMPOS_INSERT['to_sal_fami'] = ($valor_sal_familia + $sal_familia_anterior);
//$campos_insert['ad_noturno']            =  $valor_adnoturnoT;
                                        $CAMPOS_INSERT['insalubridade'] = $valor_insalubridade;
//$campos_insert['vale_refeicao']         =  $vale_refeicaoT;
//$campos_insert['debito_vale_refeicao']  =  $debito_vale_refeicaoT;
                                        $CAMPOS_INSERT['a480'] = $art_480;
                                        $CAMPOS_INSERT['a479'] = $art_479;
                                        $CAMPOS_INSERT['a477'] = $valor_atraso;
                                        $CAMPOS_INSERT['lei_12_506'] = $lei_12_506;
//$campos_insert['comissao']              =  $valor_comissaoT;
//$campos_insert['gratificacao']          =  $valor_grativicacao;
//$campos_insert['extra']                 =  $hora_extra;
//$campos_insert['outros']                =  $valor_outroT;
//$campos_insert['movimentos']            =  $a_rendimentos;
//$campos_insert['valor_movimentos']      =  $a_rendimentos;
                                        $CAMPOS_INSERT['total_rendimento'] = $to_rendimentos;
                                        $CAMPOS_INSERT['total_deducao'] = $to_descontos;
                                        $CAMPOS_INSERT['total_liquido'] = $valor_rescisao_final;
                                        $CAMPOS_INSERT['arredondamento_positivo'] = $arredondamento_positivo;
                                        $CAMPOS_INSERT['avos_dt'] = $meses_ativo_dt;
                                        $CAMPOS_INSERT['avos_fp'] = $meses_ativo_fp;
                                        $CAMPOS_INSERT['data_aviso'] = $data_aviso;
                                        $CAMPOS_INSERT['devolucao'] = $devolucao;
                                        $CAMPOS_INSERT['faltas'] = $faltas;
                                        $CAMPOS_INSERT['valor_faltas'] = $valor_faltas;
                                        $CAMPOS_INSERT['user'] = $user;
                                        $CAMPOS_INSERT['ferias_aviso_indenizado'] = $ferias_aviso_indenizado;
                                        $CAMPOS_INSERT['adiantamento_13'] = $valor_decimo_folha;
//$campos_insert['folha']                     =  '0';
//$campos_insert['adicional_noturno']         =  $adicional_noturno;
//$campos_insert['dsr']                       =  $dsr;
//$campos_insert['desc_auxilio_distancia']    =  $desc_auxilio_distancia;
                                        $CAMPOS_INSERT['um_terco_ferias_dobro']     =  $fv_um_terco_dobro;
                                        $CAMPOS_INSERT['fv_dobro']                  =  $multa_fv;
                                        $CAMPOS_INSERT['fp_data_ini'] = $periodo_proporcional_inicio;
                                        $CAMPOS_INSERT['fp_data_fim'] = $periodo_proporcional_final;
                                        $CAMPOS_INSERT['qnt_dependente_salfamilia'] = $TOTAL_MENOR;
                                        $CAMPOS_INSERT['base_inss_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
                                        $CAMPOS_INSERT['percentual_inss_ss'] = $PERCENTUAL_INSS_SS;
                                        $CAMPOS_INSERT['base_irrf_ss'] = $BASE_CALC_IRRF_SALDO_SALARIO;
                                        $CAMPOS_INSERT['percentual_irrf_ss'] = $PERCENTUAL_IRRF_SS;
                                        $CAMPOS_INSERT['parcela_deducao_irrf_ss'] = $PARCELA_DEDUCAO_IR_SS;
                                        $CAMPOS_INSERT['qnt_dependente_irrf_ss'] = $QNT_DEPENDENTES_IRRF_SS;
                                        $CAMPOS_INSERT['valor_ddir_ss'] = $VALOR_DDIR_SS;
                                        $CAMPOS_INSERT['base_fgts_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
                                        $CAMPOS_INSERT['base_inss_13'] = $BASE_CALC_INSS_13;
                                        $CAMPOS_INSERT['percentual_inss_13'] = $PERCENTUAL_INSS_13;
                                        $CAMPOS_INSERT['base_irrf_13'] = $BASE_CALC_IRRF_13;
                                        $CAMPOS_INSERT['percentual_irrf_13'] = $PERCENTUAL_IRRF_13;
                                        $CAMPOS_INSERT['parcela_deducao_irrf_13'] = $PARCELA_DEDUCAO_IR_13;
                                        $CAMPOS_INSERT['base_fgts_13'] = $BASE_CALC_INSS_13;
                                        $CAMPOS_INSERT['qnt_dependente_irrf_13'] = $QNT_DEPENDENTES_IRRF_13;
                                        $CAMPOS_INSERT['valor_ddir_13'] = $VALOR_DDIR_13;
                                        $CAMPOS_INSERT['desconto_inss'] = $row_clt['desconto_inss'];
                                        $CAMPOS_INSERT['salario_outra_empresa'] = $row_clt['salario_outra_empresa'];
                                        $CAMPOS_INSERT['desconto_inss_outra_empresa'] = $row_clt['desconto_outra_empresa'];







                                        foreach ($CAMPOS_INSERT as $campo => $valor) {
                                            $campos[] = $campo;
                                            $valores[] = "'$valor'";
                                        }
                                        $campos = implode(',', $campos);
                                        $valores = implode(',', $valores);


// Arquivo TXT
                                        $conteudo = "INSERT INTO rh_recisao($campos ) VALUES ( $valores);\r\n";



                                        $conteudo .= "UPDATE rh_clt SET status = '$dispensa', data_saida = '$data_demissao', status_demi = '1' WHERE id_clt = '$id_clt' LIMIT 1;\r\n";


// AKI O PROBLEMA
//$conteudo .= "INSERT INTO rh_eventos(id_clt, id_regiao, id_projeto, nome_status, cod_status, id_status, data, status) VALUES ('$id_clt', '$idregiao', '$idprojeto', '$row_evento[especifica]', '$dispensa', '$row_evento[0]', '$data_demissao', '1');\r\n";

                                        $nome_arquivo = 'recisaoteste_' . $id_clt . '_' . date('dmY') . '.txt';
                                        $arquivo = '../arquivos/' . $nome_arquivo;



                                        if (sizeof($movimentos) > 0) {
                                            $ids_movimentos = implode(',', $movimentos);

                                            $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
                                            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                                $conteudo .= "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, nome_movimento, valor, incidencia ) VALUES (ultimo_id_rescisao,'$row_mov[id_mov]', '$row_mov[id_clt]', '$row_mov[nome_movimento]', '$row_mov[valor_movimento]',  '$row_mov[incidencia]' ); \r\n";
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

// Encriptografando a vari�vel
                                        $linkvolt = str_replace('+', '--', encrypt("$regiao&$id_clt"));
                                        $linkir = str_replace('+', '--', encrypt("$regiao&$id_clt&$nome_arquivo"));
                                        ?>
                                        <table width="50%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td><a href="recisao2.php?tela=4&enc=<?= $linkir ?>" class="botao">Processar Rescis&atilde;o</a></td>
                                                <td><a href="recisao2.php?tela=2&enc=<?= $linkvolt ?>" class="botao">Voltar</a></td>
                                            </tr>
                                        </table>
                                        <p>&nbsp;</p>
                                    </td>
                                </tr>
                        </table>
                    </form>
                    <?php
                    break;
                case 4:
                    // executando a rescis�o
                    // Recebendo a vari�vel criptografada
                    list($regiao, $id_clt, $arquivo) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
                    $file = '../arquivos/' . $arquivo;
                    $fp = file($file);
                    $i = '0';

                    verificaRecisao($id_clt);

                    foreach ($fp as $linha) {
                        $linha = str_replace('ultimo_id_rescisao', $idi[0], $linha);
                        mysql_query($linha) or die(mysql_error());
                        $i++;
                        $idi[] = mysql_insert_id();
                    }

                    // Encriptografando a vari�vel
                    $link = str_replace('+', '--', encrypt("$regiao&$id_clt&$idi[0]"));
                    echo '<script>location.href="nova_rescisao_2.php?enc=' . $link . '"</script>';
                    exit();

                    break;
            }
            ?>
        </div>
    </body>
</html>