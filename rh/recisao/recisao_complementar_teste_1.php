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


$Fun = new funcionario();
$Fun->MostraUser(0);
$user = $Fun->id_funcionario;
$ACOES = new Acoes();
$regiao = $_REQUEST['regiao'];
$Calc = new calculos();
$Curso = new tabcurso();
$Clt = new clt();
$ClasPro = new projeto();
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

                        alert('A data escolhida ultrapassou o prazo de 30 dias após a última folha finalizada \n\n Data da última folha: ' + resposta.data_ult_folha + '.');
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
                    background-color:  #E8E8E8; 
                    display:block;
                    margin:0;
                    text-decoration:none;
                    font-size:14px;
                    font-weight:200;	
                    text-align:center;
                    color:#000;
                    padding:2px;
                    border: 1px solid #E6E6E6
                }

                .gerar_rel:hover{
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

            <?php  // tela de rescisao

                    list($regiao, $id_clt) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
                    $Clt->MostraClt($id_clt);
                    $nome = $Clt->nome;
                    $codigo = $Clt->campo3;
                    $data_demissao = $Clt->data_demi;
                    $contratacao = $Clt->tipo_contratacao;
                    $data_aviso_previo = $Clt->data_aviso;
                    $data_demissaoF = $Fun->ConverteData($data_demissao);


// Faltas no Mês
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
                                <td><input name="diastrab" type="text" id="diastrab" value="<?= abs($dias_trabalhados) ?>" size="1" maxlength="2"> dias (data para demissão: <?= $data_demissaoF ?>)</td>
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

        </div>
    </body>
</html>