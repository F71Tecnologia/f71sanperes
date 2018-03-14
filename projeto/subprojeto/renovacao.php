<?php
include('include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');
include ('../../classes/c_planodecontasClass.php');

$id_projeto = $_GET['id'];
$id_regiao = $_GET['regiao'];
$tipo_renovacao = $_GET['tp'];

$query = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row = mysql_fetch_assoc($query);

$objLancamento = new c_planodecontasClass();

if (isset($_POST['cadastrar'])) {

    if ($_POST['tipo_contratacao'] != 0) {
        $tipo = array();
        foreach ($_POST['tipo_contratacao'] as $tipo_aux) {
            $tipo[] = $tipo_aux;
        }

        $tipo_contratacao = implode(',', $tipo);
    }

    $inicio = implode('-', array_reverse(explode('/', $_POST['inicio'])));
    $termino = implode('-', array_reverse(explode('/', $_POST['termino'])));
    $total_participantes = (int) $_POST['total_participantes_clt'] . ' / ' . (int) $_POST['total_participantes_autonomo'] . ' / ' . (int) $_POST['total_participantes_cooperado'] . ' / ' . (int) $_POST['total_participantes_autonomo_pj'];
    $tipo_renovacao = htmlspecialchars($_GET['tp']);

    $programa_trabalho = mysql_real_escape_string($_POST['programa_trabalho']);
    $estado = $_POST['estado'];
    $numero_contrato = trim(mysql_real_escape_string($_POST['numero_contrato']));

    if ($_POST['tipo_aditivo'] == 1) {
        $tipo_termo_aditivo = $_POST['tipo_aditivo'];
    } else {
        $tipo_termo_aditivo = $_POST['tipo_aditivo'];
    }
    $data_assinatura = implode('-', array_reverse(explode('/', $_POST['data_assinatura'])));

    $inserir = mysql_query("INSERT INTO subprojeto (id_subprojeto, id_projeto, id_master, id_usuario, id_regiao,  tipo_contrato,numero_contrato, inicio, termino, tipo_contratacao, descricao, total_participantes,  verba_destinada, verba_periodo, taxa_adm, taxa_parceiro, id_parceiro, taxa_outra1, id_parceiro1, taxa_outra2, id_parceiro2, provisao_encargos,data, tipo_subprojeto,subprojeto_estado,data_assinatura,tipo_termo_aditivo)
		VALUES
		('', '$id_projeto','$Master', '$_COOKIE[logado]', '$id_regiao','$_POST[tipo_contrato]','$numero_contrato','$inicio', '$termino', '$tipo_contratacao','$programa_trabalho','$total_participantes','$_POST[verba_destinada]', '$_POST[mensal]', '$_POST[taxa_adm]','$_POST[taxa_parceiro]','$_POST[id_parceiro]','$_POST[taxa_outra1]', '$_POST[id_parceiro1]', '$_POST[taxa_outra2]','$_POST[id_parceiro2]', '$_POST[provisao_encargos]',NOW(),'$tipo_renovacao','$estado', '$data_assinatura','$tipo_termo_aditivo')") or die(mysql_error());

    $subprojeto = mysql_insert_id();
    
    $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
    registrar_log('ADMINISTRAÇÃO - CADASTRO DE SUBPROJETO', $nome_funcionario . ' cadastrou o subprojeto: ' . 'id_subprojeto:(' . $subprojeto . ') - ' . $_POST['tipo_contrato']);
    
    //contabil 

    $valor = (float) $_POST[verba_destinada];
    
    $qry_contabil = "SELECT * FROM contabil_lancamento WHERE MONTH(data_lancamento) = MONTH('$inicio') AND YEAR(data_lancamento) = YEAR('$termino') AND id_projeto = '{$id_projeto}'";
    $result = mysql_query($qry_contabil)or die('' . mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $trava = $row['trava_contabil'];
    }   
        
    if($trava == 0 ) {
            
        switch ($_POST[mensal]) {
            case 'Anual':

                for($data = $inicio; $data < $termino; $data = date('Y-m-d', strtotime("+12 month", strtotime($data)))) {
                    $array_lancamento = array('id_projeto' => $id_projeto, 'id_usuario' => $_COOKIE[logado], 'data_lancamento' => $data, 'historico' => $tipo_renovacao.' - '.$numero_contrato, 'contabil' => 1);
                    $id_lancamento = $objLancamento->inserirLancamento($array_lancamento);
                    $array_itens = array();
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 2);
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 1);
                    $objLancamento->inserirItensLancamento($array_itens);
                }                    
                break;
                
            case 'Mensal':
                for($data = $inicio; $data < $termino; $data = date('Y-m-d', strtotime("+1 month", strtotime($data)))) {
                    $array_lancamento = array('id_projeto' => $id_projeto, 'id_usuario' => $_COOKIE[logado], 'data_lancamento' => $data, 'historico' => $tipo_renovacao.' - '.$numero_contrato, 'contabil' => 1);
                    $id_lancamento = $objLancamento->inserirLancamento($array_lancamento);
                    $array_itens = array();
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 2);
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 1);
                    $objLancamento->inserirItensLancamento($array_itens);
                }                    
                break;
                
            case 'Trimestral':
                for($data = $inicio; $data < $termino; $data = date('Y-m-d', strtotime("+3 month", strtotime($data)))) {
                    $array_lancamento = array('id_projeto' => $id_projeto, 'id_usuario' => $_COOKIE[logado], 'data_lancamento' => $data, 'historico' => $tipo_renovacao.' - '.$numero_contrato, 'contabil' => 1);
                    $id_lancamento = $objLancamento->inserirLancamento($array_lancamento);
                    $array_itens = array();
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 2);
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 1);
                    $objLancamento->inserirItensLancamento($array_itens);
                } 

                break;
                
            case 'Semestral':
                for($data = $inicio; $data < $termino; $data = date('Y-m-d', strtotime("+6 month", strtotime($data)))) {
                    $array_lancamento = array('id_projeto' => $id_projeto, 'id_usuario' => $_COOKIE[logado], 'data_lancamento' => $data, 'historico' => $tipo_renovacao.' - '.$numero_contrato, 'contabil' => 1);
                    $id_lancamento = $objLancamento->inserirLancamento($array_lancamento);
                    $array_itens = array();
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 2);
                    $array_itens[] = array('id_lancamento' => $id_lancamento, 'valor' => $valor, 'documento' => $numero_contrato, 'tipo' => 1);
                    $objLancamento->inserirItensLancamento($array_itens);
                }                    
                break;
            }
        }
        
        header("Location: renovacao2.php?m=$link_master&regiao=$id_regiao&id=$subprojeto");
}

$regiao_origem = $_GET['regiao'];
$id_user = $_COOKIE['logado'];

$qr_areas = mysql_query("SELECT area_nome FROM areas WHERE area_status = '1' ORDER BY area_nome ASC");

$area = explode(' / ', $area);
$tipo_contratacao = explode(' / ', $tipo_contratacao);
$total_participantes = explode(' / ', $total_participantes);
?>
<html>
    <head>
        <title>:: Intranet :: Edi&ccedil;&atilde;o de Projeto</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="../../../js/ramon.js"></script>
        <script type="text/javascript" src="../../jquery-1.3.2.js"></script>
        <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
        <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
        <script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

        <script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
        <link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 

        <script type="text/javascript">

            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';

            $(function() {

                $("#form1").validationEngine();
                $('#inicio').mask('99/99/9999');
                $('#termino').mask('99/99/9999');
                $('#prazo_renovacao').mask('99/99/9999');
                $("#verba_destinada").priceFormat();
                $("#provisao_encargos").priceFormat();
                $("#data_assinatura").mask('99/99/9999');

                $('input[class=total_participantes][value=""]').attr('disabled', true);
                $('input[class=total_participantes][value="0"]').attr('disabled', true);
                $('.tipo_contratacao').change(function() {
                    if ($(this).val() == 'CLT') {
                        if ($('.total_participantes').eq(0).attr('disabled')) {
                            $('.total_participantes').eq(0).attr('disabled', false).css('margin-bottom', '1px');
                        } else {
                            $('.total_participantes').eq(0).attr('disabled', true);
                        }
                    } else if ($(this).val() == 'Autônomo') {
                        if ($('.total_participantes').eq(1).attr('disabled')) {
                            $('.total_participantes').eq(1).attr('disabled', false).css('margin-bottom', '1px');
                        } else {
                            $('.total_participantes').eq(1).attr('disabled', true);
                        }
                    } else if ($(this).val() == 'Cooperado') {
                        if ($('.total_participantes').eq(2).attr('disabled')) {
                            $('.total_participantes').eq(2).attr('disabled', false).css('margin-bottom', '1px');
                        } else {
                            $('.total_participantes').eq(2).attr('disabled', true);
                        }
                    } else if ($(this).val() == 'Autônomo PJ') {
                        if ($('.total_participantes').eq(3).attr('disabled')) {
                            $('.total_participantes').eq(3).attr('disabled', false).css('margin-bottom', '1px');
                        } else {
                            $('.total_participantes').eq(3).attr('disabled', true);
                        }
                    }
                });


                $('.tipo_aditivo').change(function() {

                    if ($(this).val() != 1) {
                        $('#termo_aditivo').fadeOut();
                        return false;
                    }
                    if ($(this).attr('checked') == true) {
                        $('#termo_aditivo').fadeIn();
                    } else {
                        $('#termo_aditivo').fadeOut();
                        $('#termo_aditivo2').fadeIn();
                    }

                });


            })


        </script>
    </head>
    <body>
        <div id="corpo">
            <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                <tr>
                    <td>
                        <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
                            <h2 style="float:left; font-size:18px;">
                                RENOVA&Ccedil;&Atilde;O - <span class="projeto">
                                <?php echo $tipo_renovacao; ?>
                                </span>
                            </h2>
                            <p style="float:right;margin-left:15px;background-color:transparent;">
                                    <?php include('../../reportar_erro.php'); ?> 
                            </p>
                            <p style="float:right;">
                                <a href="../../adm/adm_projeto/index.php?m=<?= $link_master ?>">&laquo; Voltar</a>
                            </p>
                            <div class="clear"></div>
                        </div>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?= $link_master ?>&regiao=<?= $id_regiao; ?>&id=<?= $id_projeto; ?>&tp=<?= $tipo_renovacao ?>" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="6">PER&Iacute;ODO DO PROJETO</td>
                                </tr>
                                <tr>
                                    <td class="secao">Tipo de contrato:</td>
                                    <td colspan="6">
                                        <select name="tipo_contrato" id="tipo_contrato" class="validate[required]">
                                            <option value="" >Selecione o tipo de contrato..</option>
                                            <option value="Termo de Parceria" >Termo de Parceria</option>
                                            <option value="Convênio">Convênio</option>
                                            <option value="Contrato de Gestão">Contrato de Gestão</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">N&uacute;mero do contrato:</td>
                                    <td colspan="6"><label for="numero_contrato"></label>
                                        <input type="text" name="numero_contrato" id="numero_contrato"></td>
                                </tr>
                                <tr>
                                    <td class="secao" >
                                        Data de Assinatura:
                                    </td>
                                    <td colspan="3">
                                        <input name="data_assinatura" type="text" id="data_assinatura" size="15" maxlength="10" class="validate[required]"/>
                                    </td>
                                </tr>
                                <?php
                                //rotina para o termo aditivo
                                if ($tipo_renovacao == 'TERMO ADITIVO' or $tipo_renovacao == 'APOSTILAMENTO' or $tipo_renovacao == 'CONTRATO DE GESTÃO') {
                                    ?>
                                    <td class="secao">
                                    <?php
                                    if ($tipo_renovacao == 'TERMO ADITIVO') {
                                        echo 'Tipo do Termo Aditivo:';
                                    } elseif ($tipo_renovacao == 'APOSTILAMENTO') {
                                        echo 'Tipo do Apostilamento:';
                                    } elseif ($tipo_renovacao == 'CONTRATO DE GESTÃO') {
                                        echo 'Tipo do Contrato de Gestão:';
                                    }
                                    ?>
                                    </td>
                                    <td colspan="5">
                                        <label>
                                            <input type="radio" name="tipo_aditivo" class="tipo_aditivo" value="1"> Prorrogação
                                        </label>
                                        <label>
                                            <input type="radio" name="tipo_aditivo" class="tipo_aditivo" value="2">Alteração Contratual
                                        </label>

                                    </td>
                                    <tr  id="termo_aditivo" style="display:none">


                                        <td class="secao">In&iacute;cio:</td>

                                        <td>
                                            <input name="inicio" type="text" id="inicio" size="15" maxlength="10" />
                                        </td>
                                        <td class="secao">T&eacute;rmino:</td>
                                        <td>
                                            <input name="termino" type="text" id="termino" size="15" maxlength="10" />
                                        </td>
                                    </tr>
                         <?php } else { ?>
                                    <tr>
                                        <td class="secao">In&iacute;cio:</td>
                                        <td>
                                            <input name="inicio" type="text" id="inicio" size="15" maxlength="10" class="validate[required]"/>
                                        </td>
                                        <td class="secao">T&eacute;rmino:</td>
                                        <td>
                                            <input name="termino" type="text" id="termino" size="15" maxlength="10"  class="validate[required]"/>
                                        </td>
                                    </tr>
                                <?php
                                }
                                //fim condição termo aditivo
                                ?>
                            </table>
                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="7">OBJETOS DO CONTRATO</td>
                                </tr>
                                <tr>
                                    <td class="secao" style="text-align:left; padding:5px;">Tipo de contratação</td>
                                    <td class="secao" style="text-align:left; padding:5px;">Total de participantes</td>
                                    <td class="secao" style="text-align:left; padding:5px;" colspan="5">
                                        <?php 
                                        if ($tipo_renovacao == 'TERMO ADITIVO') {
                                            echo 'Motivo do aditivo';
                                        } else {
                                            echo 'Objeto do contrato';
                                        } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">  
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="CLT"> CLT</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Autônomo" > Autônomo</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Cooperado" > Cooperado</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Autônomo PJ"> Autônomo PJ</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Nenhum">Nenhum</label>
                                    </td>
                                    <td valign="top">
                                        <input name="total_participantes_clt" type="text" class="total_participantes" size="10" disabled/><br>
                                        <input name="total_participantes_autonomo" type="text" class="total_participantes" size="10" disabled/><br>
                                        <input name="total_participantes_cooperado" type="text" class="total_participantes" size="10" disabled /><br>
                                        <input name="total_participantes_autonomo_pj" type="text" class="total_participantes" size="10" disabled/>
                                    </td>
                                    <td colspan="5"><textarea name="programa_trabalho" type="text" id="programa_trabalho" cols="70" rows="20"></textarea></td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="6">DADOS FINANCEIROS</td>
                                </tr>
                                <tr>
                                    <td class="secao" width="30%">Verba destinada:</td>
                                    <td colspan="5">
                                        <input name="verba_destinada" type="text" id="verba_destinada" size="15" class="validate[required]"  value="0,00"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">Per&iacute;odo da verba:</td>
                                    <td colspan="5">
                                        <select name="mensal" id="mensal" class="validate[required]" >
                                            <option value="">Selecione um per&iacute;odo..</option>
                                            <option value="Mensal">Mensal</option>
                                            <option value="Trimestral">Trimestral</option>
                                            <option value="Semestral" >Semestral</option>
                                            <option value="Anual">Anual</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td rowspan="8" class="secao">Taxas</td>
                                    <td colspan="5" style="line-height:28px;"><br>
                                        <br>
                                        <br></td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;">Percentual ou Valor Apurado da Taxa do Projeto:
                                        <input name="taxa_adm" type="text" id="taxa_adm" size="15" class="validate[required]"/>
                                        (% ou fixo)</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;">Parceiro Operacional: </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;"><select name="id_parceiro" id="id_parceiro">
                                            <option value="">Selecione um parceiro</option>
                                                <?php
                                                $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                                                while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {
                                                    if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
                                                        continue; // condição para não mostrar as regiões 15,36,37
                                                    ?>
                                                <option value="<?= $row_parceiro['parceiro_id']; ?>">
                                                <?= $row_parceiro['parceiro_nome']; ?>
                                                </option>
                                                <?php } ?>
                                        </select>
                                        (% ou fixo) 
                                        <input name="taxa_parceiro" type="text" id="taxa_parceiro" size="15" /></td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;">Parceiro Operacional 2: </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;"><select name="id_parceiro1" id="id_parceiro1">
                                            <option value="0">Selecione um parceiro</option>
                                                <?php
                                                $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                                                while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {

                                                    if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
                                                        continue; // condição para não mostrar as regiões 15,36,37
                                                    ?>
                                                <option value="<?= $row_parceiro['parceiro_id']; ?>">
                                                    <?= $row_parceiro['parceiro_nome']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        (% ou fixo)
                                        <input name="taxa_outra1" type="text" id="taxa_outra1" size="15" /></td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;">Parceiro Operacional 3:</td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="line-height:28px;"><select name="id_parceiro2" id="id_parceiro2">
                                            <option value="0">Selecione um parceiro</option>
                                            <?php
                                            $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                                            while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {

                                                if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
                                                    continue; // condição para não mostrar as regiões 15,36,37
                                                ?>
                                                <option value="<?= $row_parceiro['parceiro_id']; ?>">
                                                <?= $row_parceiro['parceiro_nome']; ?>
                                                </option>
                                        <?php } ?>
                                        </select>
                                        (% ou fixo) 
                                        <input name="taxa_outra2" type="text" id="taxa_outra2" size="15" /></td>
                                </tr>
                                <tr>
                                    <td class="secao" width="25%">Provis&atilde;o de encargos trabalhistas:</td>
                                    <td colspan="5">
                                        <input name="provisao_encargos" type="text" id="provisao_encargos" size="15" class="validate[required]" value="0,00"/>
                                    </td>
                                </tr>
                            </table>

                            <div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                            <div align="center">
                                <input type="submit" name="cadastrar" value="CADASTRAR" class="botao" />
                            </div> 
                            <input type="hidden" name="projeto" value="<?= $projeto ?>" />
                            <input type="hidden" name="usuario" value="<?= $id_user ?>" />
                            <input type="hidden" name="master" value="<?= $id_master ?>" />
                            <input type="hidden" name="update" value="1" />
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>