<?php
include('include/restricoes.php');
include('../conn.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../adm/include/criptografia.php');

include("../classes_permissoes/regioes.class.php");

$regiao_class = new Regioes();


if (isset($_POST['update'])) {
    extract($_POST);

    $nulos = array(
        'O Nome' => $nome, 'O Tema' => $tema, 'A Área' => $area, 'O Local' => $local,
        'O Início' => $inicio, 'O Término' => $termino, 'O Tipo de Contratação' => $tipo_contratacao, 
        'O Plano de Trabalho' => $programa_trabalho);

    foreach ($nulos as $descricao_nulo => $nulo) {
        if (empty($nulo)) {
            $erros[] = '<b>' . $descricao_nulo . '</b> não pode ficar em branco';
        }
    }

    $area = @implode(' / ', $area);
    $inicio = formato_americano($inicio);
    $termino = formato_americano($termino);
    $prazo_renovacao = formato_americano($prazo_renovacao);
    $tipo_contratacao = @implode(' / ', $tipo_contratacao);
    $total_participantes = (int) $total_participantes_clt . ' / ' . (int) $total_participantes_autonomo . ' / ' . (int) $total_participantes_cooperado . ' / ' . (int) $total_participantes_autonomo_pj;
    $verba_destinada = str_replace(',', '.', str_replace('.', '', $verba_destinada));
    $provisao_encargos = str_replace(',', '.', str_replace('.', '', $provisao_encargos));
    $verba_periodo = $_POST['verba_periodo'];
    $numero_contrato = $_POST['numero_contrato'];
    $data_assinatura = formato_americano($_POST['data_assinatura']);
    $tipo_folha = $_POST['tipo_folha'];

    list($id_regiao, $regiao_nome) = explode('-', $_POST['regiao']);
    
    $id_regiao = (empty($id_regiao)) ? $regiao_nome : $id_regiao;
    
    //Verifica se a região está desativada
    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master='$Master' AND id_regiao = '$id_regiao' AND (status = 0 OR status_reg = 0) ");
    $verifica = mysql_num_rows($qr_regiao);
    if ($verifica == 0) {
        $status_reg = 1;
    } else {
        $status_reg = 0;
    }


    $estado = $_POST['estado'];
    if (empty($erros)) {
        $sqlInsert = "INSERT INTO projeto
                        (id_master,
                        id_usuario,
                        nome,
                        tema,
                        area,
                        local, 
                        endereco,
                        complemento,
                        bairro,
                        cidade,
                        cep,
                        id_regiao,
                        regiao,										
                        tipo_contrato,
                        numero_contrato,
                        inicio, 
                        termino,  
                        tipo_contratacao, 
                        descricao, 
                        total_participantes, 
                        verba_destinada,
                        verba_periodo, 
                        taxa_adm,
                        taxa_parceiro,	
                        id_parceiro,
                        taxa_outra1,
                        id_parceiro1,
                        taxa_outra2,
                        id_parceiro2, 
                        provisao_encargos,
                        status_reg,	
                        data,
                        estado,
                        data_assinatura,
                        tipo_folha)

                        VALUES 

                       ( '$Master',
                       '$_COOKIE[logado]',
                       '$nome', 
                       '$tema', 
                       '$area', 
                       '$local',
                       '$endereco',
                       '$complemento',
                       '$bairro',
                       '$cidade',
                       '$cep',
                       '$id_regiao',
                       '$regiao_nome',	
                       '$tipo_contrato',							
                       '$numero_contrato', 
                       '$inicio', 
                       '$termino',						  
                       '$tipo_contratacao', 					
                       '$programa_trabalho',
                       '$total_participantes',
                       '$verba_destinada', 
                       '$verba_periodo', 
                       '$taxa_adm', 
                       '$taxa_parceiro',
                       '$id_parceiro', 
                       '$taxa_outra1', 
                       '$id_parceiro1', 
                       '$taxa_outra2',
                       '$id_parceiro2', 
                       '$provisao_encargos','$status_reg',
                        NOW(),
                       '$estado',
                       '$data_assinatura',
                       '$tipo_folha')";
        mysql_query($sqlInsert) or die(mysql_error());
        
        /*echo "<pre>";
        print_r($sqlInsert);
        echo "</pre>";
        exit;*/
        $projeto = mysql_insert_id();

        $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
        registrar_log('ADMINISTRAÇÃO - CADASTRO DE PROJETO', $nome_funcionario . ' cadastrou o projeto: ' . 'id_projeto:(' . $projeto . ') -  ' . $nome);

        header("Location: cadastro_projeto_2.php?m=$link_master&regiao=$id_regiao&projeto=$projeto");
    }
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
        <title>:: Intranet :: Cadastro de Projeto</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="../js/ramon.js"></script>
        <script type="text/javascript" src="../js/jquery-1.3.2.js"></script>

        <script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
        <script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine.js" ></script>
        <link href="../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
        <script type="text/javascript" src="../jquery/priceFormat.js" ></script>

        <script type="text/javascript" src="../js/highslide-with-html.js"></script> 
        <link rel="stylesheet" type="text/css" href="../js/highslide.css" /> 

        <script type="text/javascript">


            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';


            $(function() {
                $('.formata_valor').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'

                });

                $('#form1').validationEngine();

                var tipoVerifica = 0;
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
                $("#data_assinatura").mask('99/99/9999');
                $("#inicio").mask('99/99/9999');
                $("#termino").mask('99/99/9999');

            });
        </script>
    </head>
    <body>
        <div id="corpo">
            <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                <tr>
                    <td>
                        <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
                            <h2 style="float:left; font-size:18px;margin-top:40px;">
                                CADASTRAR <span class="projeto">PROJETO</span>
                            </h2>


                            <p style="float:right;margin-top:40px;">
                                <a href="../adm/adm_projeto/index.php?m=<?= $link_master ?>">&laquo; Voltar</a>
                            </p>

                            <p style="float:right;margin-left:15px;background-color:transparent;">
                                <?php include('../reportar_erro.php'); ?>   		
                            </p>
                            <div class="clear"></div>
                        </div>

                        <?php
                        if (!empty($erros)) {
                            $erros = implode('<br>', $erros);
                            echo '<p style="background-color:#C30; padding:4px; color:#FFF;">' . $erros . '</p><p>&nbsp;</p>';
                        }
                        ?>

                        <form action="<?php echo $_SERVER['PHP_SELF'] . '?m=' . $link_master . '&regiao=' . $regiao_origem ?>" method="post" name="form1" 
                              id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td colspan="5" class="secao_pai" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
                                </tr>
                                <tr>
                                    <td class="secao" width="20%">Nome:</td>
                                    <td colspan="4"><input name="nome" size="50" type="text" id="nome" value="<?php echo $nome; ?>" class="validate[required]" /></td>
                                </tr>
                                <td class="secao">Tema:</td>
                                <td colspan="4"><input name="tema" size="50" type="text" id="tema" value="<?php echo $tema; ?>" class="validate[required]"/></td>
                                </tr>
                                <tr>
                                    <td class="secao">&Aacute;rea:</td>
                                    <td colspan="4">
                                        <?php while ($row_area = mysql_fetch_assoc($qr_areas)) { ?>
                                            <label style="width:190px; display:block; float:left;">
                                                <input name="area[]" type="checkbox" class="area reset" value="<?php echo $row_area['area_nome']; ?>" <?php if (in_array($row_area['area_nome'], $area)) {
                                            echo 'checked';
                                        } ?>> <?php echo $row_area['area_nome']; ?>
                                            </label>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">Regi&atilde;o:</td>
                                    <td colspan="4">
                                        <select name="regiao" id="regiao" class="validate[required]">
                                            <?php
                                            $regiao_class->Preenhe_select_por_master($Master);
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">Local:</td>
                                    <td colspan="4"><input name="local" size="50" type="text" id="local" value="<?php echo $local; ?>" class="validate[required]"/></td>
                                </tr>
                                <tr>
                                    <td class="secao">Endereço:</td>
                                    <td colspan="4"><input name="endereco" size="50" type="text" id="endereco" value="<?php echo $endereco; ?>" class="validate[required]"/></td>
                                </tr>
                                <tr>
                                    <td class="secao">Bairro:</td>
                                    <td colspan="4"><input name="bairro" size="50" type="text" id="bairro" value="<?php echo $bairro; ?>" class="validate[required]"/></td>
                                </tr>
                                <tr>
                                    <td class="secao">Cidade:</td>
                                    <td><input name="cidade" size="20" type="text" id="cidade" value="<?php echo $cidade; ?>" class="validate[required]"/></td>
                                    <td class="secao">Estado:</td>
                                    <td colspan="2">
                                        <select name="estado" id="estado" class="validate[required]">  
                                            <option value="">UF..</option>
                                            <option value="AC">AC</option>  
                                            <option value="AL">AL</option>  
                                            <option value="AM">AM</option>  
                                            <option value="AP">AP</option>  
                                            <option value="BA">BA</option>  
                                            <option value="CE">CE</option>  
                                            <option value="DF">DF</option>  
                                            <option value="ES">ES</option>  
                                            <option value="GO">GO</option>  
                                            <option value="MA">MA</option>  
                                            <option value="MG">MG</option>  
                                            <option value="MS">MS</option>  
                                            <option value="MT">MT</option>  
                                            <option value="PA">PA</option>  
                                            <option value="PB">PB</option>  
                                            <option value="PE">PE</option>  
                                            <option value="PI">PI</option>  
                                            <option value="PR">PR</option>  
                                            <option value="RJ">RJ</option>  
                                            <option value="RN">RN</option>  
                                            <option value="RO">RO</option>  
                                            <option value="RR">RR</option>  
                                            <option value="RS">RS</option>  
                                            <option value="SC">SC</option>  
                                            <option value="SE">SE</option>  
                                            <option value="SP">SP</option>  
                                            <option value="TO">TO</option>  
                                        </select>  
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">CEP:</td>
                                    <td colspan="4"><input name="cep" size="14" type="text" id="cep" value="<?php echo $cep; ?>"/></td>
                                </tr>
                                <tr>
                                    <td class="secao">Complemento:</td>
                                    <td colspan="4"><input name="complemento" size="50" type="text" id="complemento" value="<?php echo $complemento; ?>"/></td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="4">PER&Iacute;ODO DO PROJETO</td>
                                </tr>
                                <tr>
                                    <td class="secao">Tipo de contrato:</td>
                                    <td colspan="3">
                                        <select name="tipo_contrato" id="tipo_contrato">
                                            <option value="Termo de Parceria" <?php if ($tipo_contrato == 'Termo de Parceria') {
                                                echo 'selected="selected"';
                                            } ?>>Termo de Parceria</option>
                                            <option value="Contratação Direta" <?php if ($tipo_contrato == 'Contratação Direta') {
                                                echo 'selected="selected"';
                                            } ?>>Contratação Direta</option>
                                            <option value="Convênio" <?php if ($tipo_contrato == 'Convênio') {
                                                echo 'selected="selected"';
                                            } ?>>Convênio</option>
                                            <option value="Contrato de gestão" <?php if ($tipo_contrato == 'Contrato de gestão') {
                                                echo 'selected="selected"';
                                            } ?>>Contrato de gestão</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">N&uacute;mero do contrato:</td>
                                    <td colspan="3"><label for="numero_contrato"></label>
                                        <input type="text" name="numero_contrato" id="numero_contrato"></td>
                                </tr>
                                <tr>
                                    <td class="secao">Data de assinatura:</td>
                                    <td colspan="3"><input name="data_assinatura" id="data_assinatura" type="text"/> </td>
                                </tr>
                                <tr>
                                    <td class="secao">In&iacute;cio:</td>
                                    <td>
                                        <input name="inicio" type="text" id="inicio" size="15" maxlength="10" onKeyUp="mascara_data(this);" value="<?php echo formato_brasileiro($inicio); ?>" />
                                    </td>
                                    <td class="secao">T&eacute;rmino:</td>
                                    <td>
                                        <input name="termino" type="text" id="termino" size="15" maxlength="10" onKeyUp="mascara_data(this);" value="<?php echo formato_brasileiro($termino); ?>" />
                                    </td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="6">OBJETOS DO CONTRATO</td>
                                </tr>

                                <tr>
                                    <td class="secao" >Tipo de folha de pagamento:</td>
                                    <td colspan="5">
                                        <input type="radio" name="tipo_folha" value="0" checked/> Mensalista<br>
                                        <input type="radio" name="tipo_folha" value="1" > Horista
                                    </td>
                                </tr>


                                <tr>
                                    <td class="secao" style="text-align:left; padding:5px;">Tipo de contratação</td>
                                    <td class="secao" style="text-align:left; padding:5px;">Total de participantes</td>
                                    <td class="secao" style="text-align:left; padding:5px;" colspan="3">Objeto do contrato</td>
                                </tr>
                                <tr>
                                    <td valign="top">  
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="CLT" <?php if (in_array('CLT', $tipo_contratacao)) {
                                                echo 'checked';
                                            } ?>> CLT</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Autônomo" <?php if (in_array('Autônomo', $tipo_contratacao)) {
                                                echo 'checked';
                                            } ?>> Autônomo</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Cooperado" <?php if (in_array('Cooperado', $tipo_contratacao)) {
                                                echo 'checked';
                                            } ?>> Cooperado</label><br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Autônomo PJ" <?php if (in_array('Autônomo PJ', $tipo_contratacao)) {
                                                echo 'checked';
                                            } ?>> Autônomo PJ</label>
                                        <br>
                                        <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Nenhum" <?php if (in_array('Nenhum', $tipo_contratacao)) {
                                                echo 'checked';
                                            } ?>> Nenhum </label>
                                    </td>
                                    <td valign="top">
                                        <input name="total_participantes_clt" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[0]; ?>" /><br>
                                        <input name="total_participantes_autonomo" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[1]; ?>" /><br>
                                        <input name="total_participantes_cooperado" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[2]; ?>" /><br>
                                        <input name="total_participantes_autonomo_pj" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[3]; ?>" />
                                    </td>
                                    <td colspan="5"><textarea name="programa_trabalho" type="text" id="programa_trabalho" cols="70" rows="20"><?php echo $programa_trabalho; ?></textarea></td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="1" class="secao">
                                <tr>
                                    <td class="secao_pai" colspan="6">DADOS FINANCEIROS</td>
                                </tr>
                                <tr>
                                    <td class="secao" width="30%">Verba destinada:</td>
                                    <td colspan="5">
                                        <input name="verba_destinada" type="text" id="verba_destinada" size="15" class="formata_valor" value="<?php echo formato_real($verba_destinada); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">Per&iacute;odo da verba:</td>
                                    <td colspan="5">
                                        <select name="verba_periodo" id="verba_periodo">
                                            <option value="Mensal" <?php if ($verba_periodo == 'Mensal') {
                                                echo 'selected="selected"';
                                            } ?>>Mensal</option>
                                            <option value="Trimestral" <?php if ($verba_periodo == 'Trimestral') {
                                                echo 'selected="selected"';
                                            } ?>>Trimestral</option>
                                            <option value="Semestral" <?php if ($verba_periodo == 'Semestral') {
                                                echo 'selected="selected"';
                                            } ?>>Semestral</option>
                                            <option value="Anual" <?php if ($verba_periodo != 'Mensal' and $verba_periodo != 'Semestral') {
                                                echo 'selected="selected"';
                                            } ?>>Anual</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao">Taxas</td>
                                    <td colspan="5" style="line-height:28px;">
                                        Percentual ou Valor Apurado da Taxa do Projeto: 
                                        <input name="taxa_adm" type="text" id="taxa_adm" size="15" value="" />
                                        (% ou fixo)<br>
                                        Parceiro Operacional: 
                                        <input name="taxa_parceiro" type="text" id="taxa_parceiro" size="15" value="<?php echo $taxa_parceiro; ?>" />
                                        (% ou fixo)
                                        <select name="id_parceiro" id="id_parceiro">
                                            <option value="0">Selecione um parceiro</option>
                                            <?php
                                            $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                                            while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {
                                                if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
                                                    continue; // condição para não mostrar as regiões 15,36,37
                                                echo '<option value="' . $row_parceiro['parceiro_id'] . '"';
                                                if ($id_parceiro == $row_parceiro['parceiro_id']) {
                                                    echo 'selected="selected"';
                                                };
                                                echo '>' . $row_parceiro['parceiro_nome'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        Parceiro Operacional 2: 
                                        <input name="taxa_outra1" type="text" id="taxa_outra1" size="15" value="<?php echo $taxa_outra1; ?>" />
                                        (% ou fixo)
                                        <select name="id_parceiro1" id="id_parceiro1">
                                            <option value="0">Selecione um parceiro</option>
<?php
$qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {
    if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
        continue; // condição para não mostrar as regiões 15,36,37 
    echo '<option value="' . $row_parceiro['parceiro_id'] . '"';
    if ($id_parceiro1 == $row_parceiro['parceiro_id']) {
        echo 'selected="selected"';
    };
    echo '>' . $row_parceiro['parceiro_nome'] . '</option>';
}
?>
                                        </select>
                                        <br>
                                        Parceiro Operacional 3: 
                                        <input name="taxa_outra2" type="text" id="taxa_outra2" size="15" value="<?php echo $taxa_outra2; ?>" />
                                        (% ou fixo)
                                        <select name="id_parceiro2" id="id_parceiro2">
                                            <option value="0">Selecione um parceiro</option>
<?php
$qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
while ($row_parceiro = mysql_fetch_assoc($qr_parceiros)) {
    if ($row_parceiro['id_regiao'] == '15' or $row_parceiro['id_regiao'] == '36' or $row_parceiro['id_regiao'] == '37')
        continue; // condição para não mostrar as regiões 15,36,37
    echo '<option value="' . $row_parceiro['parceiro_id'] . '"';
    if ($id_parceiro2 == $row_parceiro['parceiro_id']) {
        echo 'selected="selected"';
    };
    echo '>' . $row_parceiro['parceiro_nome'] . '</option>';
}
?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="secao" width="25%">Provis&atilde;o de encargos trabalhistas:</td>
                                    <td colspan="5">
                                        <input name="provisao_encargos" type="text" id="provisao_encargos" size="15" class="formata_valor" value="<?php echo formato_real($provisao_encargos); ?>" />
                                    </td>
                                </tr>
                            </table>

                            <div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                            <div align="center">
                                <div id="continuando" style="display:none;">  Continuando... <br> <img src="../imagens/1-carregando.gif"/></div>
                                <input type="submit" name="Submit" id="continuar" value="CONTINUAR" class="botao" />
                            </div> 
                            <input type="hidden" name="usuario" value="<?= $id_user ?>" />
                            <input type="hidden" name="master" value="<?= $id_master ?>" />
                            <input type="hidden" name="update" value="1" />
                        </form>
                    </td>
                </tr>
            </table>
            <center> <div id="rodape"><?php include('include/rodape.php'); ?></div>
            </center>
        </div>
    </body>
</html>