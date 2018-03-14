<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
//include('../include/criptografia.php');
include('../../classes_permissoes/regioes.class.php');
include('../../classes_permissoes/projeto.class.php');


$REGIOES = new Regioes();
$PROJETO = new Projeto();

$usuario = carregaUsuario();
$Master = $usuario['id_master'];


$nota = $_GET['id'];
$qr_notas = mysql_query("SELECT * FROM notas WHERE id_notas = '$nota' ");
$row_notas = mysql_fetch_assoc($qr_notas);


if (isset($_POST['enviar'])) {

    $nota = trim(mysql_real_escape_string($_POST['nota']));
    $n_nota = trim(mysql_real_escape_string($_POST['n_nota']));
    $id_parceiro = $_POST['parceiro'];
    $data_emissao = implode('-', array_reverse(explode('/', $_POST['data_emissao'])));
    $descricao = mysql_real_escape_string($_POST['descricao']);
    $valor = str_replace(',', '.', str_replace('.', '', $_POST['valor']));
    $tipo = $_POST['tipo'];
    $usuario_id = $_COOKIE['logado'];
    $anexo = $_REQUEST['id_file'];
    $id_notas_files = $_POST['nota'];
    $id_projeto = $_POST['projeto'];
    $ano_competencia = $_POST['ano_competencia'];
    list ($tipo_contrato2, $tipo_contrato) = explode('_', $_POST['contrato']);


    $update_nota = mysql_query("UPDATE notas SET numero='$_POST[n_nota]', id_parceiro='$_POST[parceiro]', data_emissao='$data_emissao', descricao='$_POST[descricao]', valor='$valor', tipo='$tipo', ultima_edicao=NOW(), editado_por='$_COOKIE[logado]', status='1', id_projeto='$id_projeto', tipo_contrato='$tipo_contrato',tipo_contrato2='$tipo_contrato2', nota_ano_competencia = '$ano_competencia' WHERE id_notas = '$nota' LIMIT 1");

    if (!empty($anexo)) {
        mysql_query("UPDATE notas_files SET status = '0' AND id_notas = '0' WHERE id_notas = '$nota' LIMIT 5");
        mysql_query("UPDATE notas_files SET id_notas = '$nota' WHERE id_file = '$anexo'");
    }



    $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
    registrar_log('ADMINISTRAï¿½ï¿½O - EDIï¿½ï¿½O DE NOTAS FISCAIS', $nome_funcionario . ' editou a nota: ' . 'id_nota:(' . $nota . ') - Nï¿½mero: ' . $n_nota);



//    header("Location: cadastro_2.php?m=" . $link_master . '&id=' . $nota);
    header("Location: index.php");
}
?>
<html>
    <head>
        <title>Administra&ccedil;&atilde;o de Notas</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../css/estrutura.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
        <script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
        <link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
        <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
        <script type="text/javascript" src="../../jquery/priceFormat.js" ></script>





        <script type="text/javascript" >
            $(function () {
                $("#anexo").uploadify({
                    'uploader': '../../uploadfy/scripts/uploadify.swf',
                    'script': 'upload2.php?upload=<?=$nota?>',
                    'buttonText': 'Alterar',
                    'queueID': 'barra_processo',
                    'cancelImg': '../../uploadfy/cancel.png',
                    'auto': true,
                    'method': 'post',
                    'multi': false,
                    'fileDesc': 'gif jpg pdf',
                    'fileExt': '*.gif;*.jpg;*.pdf;',
                    
                    'onComplete': function (event, ID, fileObj, response, data) {
                        eval('var resposta = ' + response);
                        console.log(resposta);
                        if (resposta.erro) {
                            alert('Ocorreu um erro no envio do arquivo');
                        } else {
                            $("#anexo").hide();
                            $('#id_file').val(resposta.ID);
                            $('#img_nota').html('');
                            $('#img_nota').html('<a href="notas/'+resposta.ID+'.pdf">Visualizar pdf</a>');
                            $('#enviar').attr('disabled', false);
                        }
                    }
                });

                $("#cadastro").validationEngine();
                $('#data_emissao').mask('99/99/9999');
                $('#valor').priceFormat();



                $('#projeto').change(function () {

                    var id_projeto = $(this).val();

                    $('#contrato').html('<option value="">Carregando...</option>');

                    $.ajax({
                        'url': 'actions/combo.subprojeto.json.php',
                        'data': {'id_projeto': id_projeto, 'contrato': '<?php echo $row_notas['tipo_contrato']; ?>'},
                        'success': function (resposta) {

                            //console.log(resposta);

                            $('#contrato').html('');



                            $.each(resposta, function (i, valor) {

                                $('#contrato').append('<optgroup label="' + i + '">');

                                $.each(valor, function (chave, registro) {

                                    var selected = '';
                                    if (registro.selecionado == '1') {
                                        selected = 'selected="selected"';
                                    }

                                    $('#contrato').append('<option ' + selected + ' value="' + registro.tipo + '_' + registro.id_subprojeto + '" > Contrato Nº: <b>' + registro.numero_contrato + '</b> Inicio : ' + registro.inicio + ' - Fim : ' + registro.termino + '</option>');
                                });

                                $('#contrato').append('</optgroup>');

                            });



                        },
                        'dataType': 'json'
                    });
                });

                $('#projeto').trigger("change");

            });
        </script>
    </head>
    <body>
        <div id="corpo">
            <div id="menu" class="nota">
                <?php include('include/menu.php'); ?>
            </div>
            <div id="conteudo">   
                <h1><span>Editar de Notas Fiscais</span></h1>
                <form name="cadastro" id="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?= $link_master ?>">
                    <table width="818">
                        <tr>
                            <td width="244" class="secao">N&ordm; do nota:</td>
                            <td  width="189"><label for="n_nota"></label>
                                <input name="n_nota" type="text" class="validate[required]" id="n_nota" value="<?php echo $row_notas['numero']; ?>"></td>

                            <td width="221" class="secao">Parceiro Operacional:</td>

                            <td  width="144"><label for="parceiro3"></label>

                                <select name="parceiro" size="1" id="parceiro3" class="validate[required]">


                                    <option value="">

                                    </option>

                                    <?php
                                    $sql_regiao = mysql_query("SELECT regioes.id_regiao, regioes.regiao FROM parceiros INNER JOIN regioes ON parceiros.id_regiao = regioes.id_regiao
						  WHERE regioes.id_master = '$Master'
						  ")or die("Erro");
                                    while ($row_regiao = mysql_fetch_assoc($sql_regiao)):
                                        ?>
                                        <optgroup label="<?= $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao']; ?>" >
                                            <?php
                                            $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$row_regiao[id_regiao]'");
                                            while ($row_parceiros = mysql_fetch_assoc($qr_parceiros)):

                                                $selected = ($row_notas['id_parceiro'] == $row_parceiros['parceiro_id']) ? 'selected="selected"' : '';
                                                ?>
                                                <option value="<?= $row_parceiros['parceiro_id'] ?>" <?= $selected ?>> <?= $row_parceiros['parceiro_nome'] ?></option>
                                        <?php endwhile; ?>
                                        </optgroup>
                                        <?php endwhile;
                                    ?>
                                    <?php
                                    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master='$Master' ORDER BY regiao");

                                    $row_projeto = mysql_fetch_assoc($qr_projeto);




                                    $qr_nota_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto='$row_notas[id_projeto]'");
                                    $row_nota_projeto = mysql_fetch_assoc($qr_nota_projeto);
                                    ?>                
                                </select> </td>


                        </tr>
                        <tr>
                            <td width="244" class="secao">Projeto:</td>
                            <td colspan="3"><select name="projeto" id="projeto" class="validate[required]">
                                    <option value=""> </option>
                                    <?php
                                    $PROJETO->Preenhe_select_por_master($Master, $row_notas['id_projeto']);
                                    ?>                   

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="secao">Tipo de contrato:</td>
                            <td colspan="3"><label for="projeto"></label>
                                <select name="contrato" id="contrato">


                                </select></td>
                        </tr>
                        <tr>
                            <td class="secao">Descri&ccedil;&atilde;o:</td>
                            <td colspan="5"><label for="descricao"></label>
                                <input name="descricao" type="text" id="descricao" size="50" value="<?php echo $row_notas['descricao']; ?>"></td>
                        </tr>
                        <tr>
                            <td class="secao">Data de emiss&atilde;o:</td>
                            <td><input name="data_emissao" type="text" class="validate[required]"  id="data_emissao" value="<?php echo implode('/', array_reverse(explode('-', $row_notas['data_emissao']))); ?>" size="10"></td>

                            <td class="secao">Ano da Compet&ecirc;ncia</td>
                            <td colspan="3"><label for="ano_competencia"> </label>
                                <select name="ano_competencia"  class="validate[required]" id="data_emissao">

                                    <option value="">Selecione o ano..</option>
                                    <?php
                                    for ($ano = 2005; $ano <= date('Y'); $ano++):

                                        $selected = ($ano == $row_notas['nota_ano_competencia']) ? 'selected="selected"' : '';
                                        ?>
                                        <option value="<?php echo $ano; ?>" <?php echo $selected; ?> ><?php echo $ano; ?></option>   
                                        <?php
                                    endfor;
                                    ?>
                                </select>
                            </td>

                        </tr>
                        <tr>
                            <td class="secao">Valor:</td>
                            <td colspan="5"><label for="valor"></label>
                                <input type="text" name="valor" id="valor" class="validate[required]" value="<?php echo number_format($row_notas['valor'], 2, ',', '.'); ?>" ></td>
                        </tr>
                        <tr>
                            <td class="secao">Tipo:</td>
                            <td colspan="5"><label for="tipo"></label>
                                <select name="tipo" id="tipo">
                                    <option value=""></option>
                                    <option value="1" <?php if ($row_notas['tipo'] == '1') {
                                        echo 'selected="selected"';
                                    } ?>>1- Nota</option>
                                    <option value="2" <?php if ($row_notas['tipo'] == '2') {
                                        echo 'selected="selected"';
                                    } ?>>2 - Carta de medi&ccedil;&atilde;o</option>
                                </select></td>
                        </tr>
                        <tr>
                            <?php $img_nota = mysql_query("SELECT notas.id_notas, notas_files.id_flies, notas_files.tipo, notas_files.status FROM notas INNER JOIN notas_files ON notas.id_notas=notas_file.id_notas");
                            ?>
                            <td class="secao">Anexo:</td>
                            <td colspan="5"><input type="file" name="anexo" id="anexo" />
                                <div id="barra_processo"></div>



                                <?php
                                $qr_imagem = mysql_query("SELECT *
FROM notas_files WHERE id_notas = '$nota' AND status = 1");

                                $num_imagem = mysql_num_rows($qr_imagem);
                                ?>



                            </td>
                        </tr>


                        <tr>
                            <td class="secao">
                                <a href="visializa_files.php?id_nota=<?php echo $nota; ?>" target="_blank" >                     
                                    <img src="../../imagens/print.png" alt="imprimir" width="50" height="38" />
                                </a>
                            </td>

                            <td colspan="6" rowspan="2" id="img_nota">
                                <?php if (!empty($num_imagem)): ?>
                                    <?php while ($row_imagem = mysql_fetch_assoc($qr_imagem)): ?>
                                        <?php if ($row_imagem['tipo'] == 'pdf'): ?>
                                            <a href="notas/<?php echo $row_imagem['id_file']; ?>.<?= $row_imagem['tipo']; ?>">Visualizar pdf</a>
                                        <?php else: ?>
                                            <img src="notas/<?php echo $row_imagem['id_file']; ?>.<?= $row_imagem['tipo']; ?>" width="200" heidth="250"/>
                                        <?php endif; ?>

                                    <?php endwhile; ?>
                                <?php endif; ?>                      	
                            </td>
                        </tr>
                        <tr>
                            <td class="secao">&nbsp;</td>
                        </tr>

                        <tr>
                            <td colspan="6" class="secao">
                                <input type="hidden" name="nota" id="nota" value="<?= $nota ?>" />
                                <input type="hidden" name="id_file" id="id_file" value="<?php echo $row_imagem['id_file']; ?>" />



                                <input name="pronto" value="edicao" type="hidden" />
                                <input type="submit" name="enviar" id="enviar" value="Atualizar" class="botao" >
                            </td>
                        </tr>
                    </table>
                </form>

            </div>
            <div id="rodape">
                <?php include('../include/rodape.php'); ?>
            </div>
        </div>
    </body>
</html>