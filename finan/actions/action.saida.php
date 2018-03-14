<?php
include "../../conn.php";
include "../../classes/uploadfile.php";
require("../../wfunction.php");
require("../../classes/SaidaClass.php");
require("../../classes/LogClass.php");
include "../../funcoes.php";
include("../../classes/global.php");

//$charset = mysql_set_charset('utf8');

$qr_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_func);

$log = new Log();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$grupo = isset($_REQUEST['grupo']) ? $_REQUEST['grupo'] : NULL;
$regiao = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : NULL;
$projeto = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : NULL;
$master = isset($_REQUEST['id_master']) ? $_REQUEST['id_master'] : NULL;
$subgrupo = isset($_REQUEST['subgrupo']) ? $_REQUEST['subgrupo'] : NULL;
$opt = isset($_REQUEST['opt']) ? $_REQUEST['opt'] : NULL;
$usuario = carregaUsuario();
switch ($action) {

    case 'calculaIR' :
        $objSaida = new Saida();
        $array = $objSaida->calculaIR($_POST['valor'], $_POST['id']);
        echo json_encode($array);
        break;

    case 'load_projeto' :
        $rs = montaQuery("projeto", "*", "status_reg = 1 AND id_regiao = {$regiao}");
        $option = '<option value="" >Todos os projetos</option>';
        foreach ($rs as $value) {
            $selected = ($value['id_projeto'] == $projeto) ? ' selected="selected" ' : ' ';
            $option .= '<option value="' . $value['id_projeto'] . '"  ' . $selected . '  >' . $value['nome'] . ' </option>';
        }
        echo $option;
        break;
    case 'load_subgrupo':
        $rs = montaQuery("entradaesaida_subgrupo", "*", "entradaesaida_grupo = {$grupo}");
        if ($opt != NULL) {
            $option = '<option value="-1" selected="selected" >' . $opt . '</option>';
        } else {
            $option = '<option value="" selected="selected" >Todos os Subgrupos</option>';
        }

        foreach ($rs as $value) {
            $option .= '<option value="' . $value['id'] . '"  >' . utf8_encode($value['id_subgrupo'] . ' - ' . $value['nome']) . ' </option>';
        }
        if (empty($rs)) {
            echo '<option value="" selected="selected" >Selecione o grupo</option>';
        } else {
            echo $option;
        }
        break;
    case 'load_tipo':
//        $rs = montaQuery("entradaesaida_subgrupo", "*", "id = {$subgrupo}");

        if ($opt != NULL) {
            $option = '<option value="-1" selected="selected" >' . $opt . '</option>';
        } else {
            $option = '<option value=""  selected="selected">Todos os Tipos</option>';
        }

        $sel_subgrupo = mysql_query("SELECT id_subgrupo FROM entradaesaida_subgrupo WHERE id = {$subgrupo}");
        $res_subgrupo = mysql_fetch_assoc($sel_subgrupo);

        $sub = $res_subgrupo['id_subgrupo'];

        if ($sub != "") {
            $query = mysql_query("SELECT id_entradasaida, cod, nome, id_entradasaida, faturamento FROM entradaesaida WHERE cod LIKE '$sub%'");
            while ($row = mysql_fetch_assoc($query)) {
                $option .= '<option value="' . $row['id_entradasaida'] . '" data-faturamento="' . $row['faturamento'] . '">' . utf8_encode($row['cod'] . ' - ' . $row['nome']) . ' </option>';
            }
        }
        echo $option;
        break;

    case 'verAnexos':
        $id_saida = $_REQUEST['id'];
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <script>
            $(function () {
                Dropzone.autoDiscover = false;

                $("#dropzone").dropzone({
                    url: "actions/action.saida.php?action=upload_anexo&id_saida=<?= $id_saida ?>&tipo_anexo=1",
                    //addRemoveLinks : true,
                    maxFilesize: 30,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 30MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                            //                , sending: function(file, xhr, formData) {
                            //                    formData.append("tipo_anexo", $('#tipo_anexo').val()); // Append all the additional input data of your form here!
                            //                }
                            //                , success: function(file, responseText){
                            //                    console.log(responseText);
                            //                    //$('.close').trigger('click');
                            //                }
                });
            });
        </script>
        <!--<div class="form-group">
            <select name="tipo_anexo" id="tipo_anexo" class="form-control"><option value="1">Anexo</option><option value="2">Comprovante</option></select>
        </div>-->
        <div class="form-group">
            <div id="dropzone" class="dropzone margin_b15" style="min-height: 150px;"></div>
        </div>
        <?php
        $objSaida = new Saida();
        $dadosSaida = $objSaida->getSaidaID($id_saida);
        $dadosSaidaFile = $objSaida->getSaidaFile($id_saida);
        while ($row_files = mysql_fetch_assoc($dadosSaidaFile)) {
            if (file_exists("../../comprovantes/$row_files[id_saida_file].$id_saida$row_files[tipo_saida_file]")) {
                ?>
                <div class="col-xs-3 margin_b5 <?= $row_files['id_saida_file'] ?>">
                    <div class="thumbnail">
                        <a href="../comprovantes/<?= $row_files['id_saida_file'] ?>.<?= $id_saida . $row_files['tipo_saida_file'] ?>" target="_blank">
                            <img class="h-100" src="../imagens/icons/att-<?= str_replace('.', '', $row_files['tipo_saida_file']) ?>.png">
                        </a>
                        <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?= $row_files['id_saida_file'] ?>"> Deletar</span>
                    </div>
                </div>
                <?php
            } else {
                $rescisao = $objSaida->verificaSaidaRescisao($id_saida);
                if (!empty($rescisao)) {
                    ?>
                    <div class="col-xs-3 margin_b5 <?= $row_files[id_saida_file] ?>">
                        <div class="thumbnail">
                            <a href="/intranet/rh/recisao/<?= $rescisao ?>" target="_blank">
                                <img class="h-100" src="../imagens/icons/att-<?= str_replace('.', '', $row_files['tipo_saida_file']) ?>.png">
                            </a>
                            <!--<span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?= $row_files['id_saida_file'] ?>"> Deletar</span>-->
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-xs-3 margin_b5 <?= $row_files['id_saida_file'] ?>">
                        <div class="thumbnail tr-bg-danger">
                            <a href="../comprovantes/<?= $row_files['id_saida_file'] ?>.<?= $id_saida . $row_files['tipo_saida_file'] ?>" target="_blank">
                                <img class="h-100" src="../imagens/icons/att-<?= str_replace('.', '', $row_files['tipo_saida_file']) ?>.png">
                            </a>
                            <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?= $row_files['id_saida_file'] ?>"> Deletar</span>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <div class="clear"></div><?php
        break;

    case 'verComprovante':
        $id_saida = $_REQUEST['id'];
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <?php
        $objSaida = new Saida();
        $dadosSaidaPg = $objSaida->getSaidaFilePg($id_saida);
        $countFiles = 0;
        while ($row_files = mysql_fetch_assoc($dadosSaidaPg)) {
            if (file_exists("../../comprovantes/{$row_files['id_pg']}.{$id_saida}_pg{$row_files['tipo_pg']}")) {
                ?>
                <div class="col-xs-3 margin_b5 <?= $row_files['id_pg'] ?>">
                    <div class="thumbnail">
                        <a href="../comprovantes/<?= $row_files['id_pg'] ?>.<?= $id_saida ?>_pg<?= $row_files['tipo_pg'] ?>" target="_blank">
                            <img src="../imagens/icons/att-<?= str_replace('.', '', $row_files['tipo_pg']) ?>.png">
                        </a>
                        <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?= $row_files['id_pg'] ?>"> Deletar</span>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-xs-3 margin_b5 <?= $row_files[id_pg] ?>">
                    <div class="thumbnail">
                        <a href="comprovantes/saida/<?= $row_files['id_pg'] ?>.<?= $id_saida ?>_pg<?= $row_files['tipo_pg'] ?>" target="_blank">
                            <img src="../imagens/icons/att-<?= str_replace('.', '', $row_files['tipo_pg']) ?>.png">
                        </a>
                        <span class="btn btn-sm btn-danger fa fa-trash-o margin_t5 deleteAnexoSaida" style="width: 100%;" data-key="<?= $row_files['id_pg'] ?>"> Deletar</span>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <div class="clear"></div><?php
        break;

    case 'editar_saida_data':
        $id_saida = $_REQUEST['id'];
        $objSaida = new Saida();
        $row_saida = $objSaida->getSaidaID($id_saida);

        $qry_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$row_saida['id_projeto']}' AND status_reg = 1 ORDER BY nome;");
        while ($row_bancos = mysql_fetch_assoc($qry_bancos)) {
            $arrayBancos[$row_bancos['id_banco']] = "{$row_bancos['id_banco']} - {$row_bancos['nome']}";
        }
        ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <script>
            $(".valor").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});
            $('select[name=estorno]').on('change', function () {
                if ($(this).attr('checked') == false) {
                    $('.descricao_estorno').fadeOut();
                    $('.valor_estorno_parcial').fadeOut();
                } else if ($(this).val() == 1) {
                    $('.descricao_estorno').fadeIn();
                    $('.valor_estorno_parcial').fadeOut();
                } else if ($(this).val() == 2) {
                    $('.descricao_estorno').fadeIn();
                    $('.valor_estorno_parcial').fadeIn();
                } else if ($(this).val() == '') {
                    $('.descricao_estorno').fadeOut();
                    $('.valor_estorno_parcial').fadeOut();
                }
            });
            $('.data').datepicker({
                dateFormat: 'dd/mm/yy',
                beforeShow: function () {
                    setTimeout(function () {
                        $('.ui-datepicker').css('z-index', 5010);
                    }, 0);
                }
            });

            $(function () {
                Dropzone.autoDiscover = false;

                $("#dropzone").dropzone({
                    url: "actions/action.saida.php?action=upload_anexo&id_saida=<?= $id_saida ?>&tipo_anexo=2",
                    addRemoveLinks: true,
                    maxFilesize: 30,
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 30MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                            //                , sending: function(file, xhr, formData) {
                            //                    formData.append("tipo_anexo", $('#tipo_anexo').val()); // Append all the additional input data of your form here!
                            //                }
                            //                , success: function(file, responseText){
                            //                    console.log(responseText);
                            //                    //$('.close').trigger('click');
                            //                }
                });
            });
        </script>
        <!--div class="form-group">
            <select name="tipo_anexo" id="tipo_anexo" class="form-control"><option value="1">Anexo</option><option value="2">Comprovante</option></select>
        </div-->
        <form action="" method="post" id="form_editar_saida_data">
            <table align="center" class="table table-condensed table-bordered text-sm">
                <tr class="valign-middle">
                    <td colspan="2"><h4><?= $row_saida['nome'] ?></h4></td>
                </tr>
                <tr class="valign-middle">
                    <td>DATA DE VENCIMENTO:</td>
                    <td><input type="text" name="data_vencimento" class="form-control dt_vencimento data" value="<?= implode('/', array_reverse(explode('-', $row_saida['data_vencimento']))) ?>"/></td>
                </tr>
                <tr class="valign-middle">
                    <td>DESCRIÇÃO:</td>
                    <td><input type="text" name="descricao" class="form-control descricao" value="<?= $row_saida['especifica'] ?>" /></td>
                </tr>
                <tr class="valign-middle">
                    <td>Banco:</td>
                    <td><?= montaSelect($arrayBancos, $row_saida['id_banco'], 'id="banco" name="banco" class="form-control"') ?></td>
                </tr>
                <?php if ($row_saida['status'] == 2) { ?>
                    <tr class="valign-middle">
                        <td>ESTORNO</td>
                        <td align="left">
                            <select name="estorno" class="form-control">
                                <option value="">Selecione...</option>
                                <option value=""></option>
                                <option value="1" <?= ($row_saida['estorno'] == 1) ? 'selected="selected"' : ''; ?>>INTEGRAL</option>
                                <option value="2" <?= ($row_saida['estorno'] == 2) ? 'selected="selected"' : ''; ?>>PARCIAL</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="valor_estorno_parcial valign-middle" style="display:<?= ($row_saida['estorno'] == 2) ? '' : 'none'; ?>">
                        <td>Valor do estorno:</td> 
                        <td align="left">
                            <input type="text" name="valor_estorno_parcial" class="form-control valor" id="valor_estorno_parcial" value="<?= number_format($row_saida['valor_estorno_parcial'], 2, ',', '.') ?>" onKeyDown="FormataValor(this, event, 17, 2)"/>
                        </td>
                    </tr>
                    <tr class="descricao_estorno valign-middle" style="display:<?= ($row_saida['estorno'] != 0) ? '' : 'none' ?>">
                        <td valign="top">DESCRIÇÃO DO ESTORNO:</td>
                        <td><textarea name="descricao_estorno" class="form-control" cols="30" rows="5" ><?= trim($row_saida['estorno_obs']) ?></textarea></td>
                    </tr>
                <?php } ?>
            </table>
            <h4>Comprovante de Pagamento</h4>
            <div class="form-group">
                <div id="dropzone" class="dropzone margin_b15" style="min-height: 150px;"></div>
            </div>
            <input type="hidden" name="action" value="editar_saida_data_atualizar">
            <input type="hidden" name="id_saida" value="<?= $id_saida ?>">
        </form><?php
        break;

    case 'editar_saida_data_atualizar':

        $data_vencimento = implode('-', array_reverse(explode('/', $_POST['data_vencimento'])));
        $especifica = $_POST['descricao'];
        $id_saida = $_POST['id_saida'];
        $estorno = $_POST['estorno'];
        $banco = $_POST['banco'];
        $descricao_estorno = $_POST['descricao_estorno'];
        $valor_estorno_parcial = str_replace(",", ".", str_replace('.', '', $_POST['valor_estorno_parcial']));

        $sql = "UPDATE saida SET data_vencimento = '$data_vencimento', id_banco = '{$banco}',  especifica = '$especifica', estorno = '$estorno', estorno_obs = '$descricao_estorno', valor_estorno_parcial = '$valor_estorno_parcial'  WHERE id_saida = '$id_saida' LIMIT 1";
        if (mysql_query($sql)) {
            $log->gravaLog('Editar Saida', 'Edição saida ' . $id_saida);
            echo 1;
        } else {
            die(mysql_errno());
        }

        break;

    case 'pagar_bordero':
        $objSaida = new Saida();
        echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';


        $id_saida = $_POST[id];

        $numero_cheque = utf8_decode($_REQUEST['numero_cheque']);
        $campo_livre = utf8_decode($_REQUEST['campo_livre']);
        $descricao = utf8_decode($_REQUEST['descricao']);
        $data_compensar = implode('-', array_reverse(explode('/', $_REQUEST['data_compensar'])));


        $countSaida = count($id_saida);
//        print_array($id_saida);
        $sqlB = "UPDATE bordero SET numero_cheque = '{$numero_cheque}', pago = 1, campo_livre = '{$campo_livre}', descricao = '{$descricao}', data_compensar = '{$data_compensar}' WHERE id = '{$_REQUEST['id_bordero']}';";
//        print_array($_REQUEST);
//        print_array($sqlB);exit;
        mysql_query($sqlB) or die(mysql_error());

//        for($i=0; $i < $countSaida; $i++){
        foreach ($id_saida as $key => $id) {

            $row = mysql_fetch_assoc(mysql_query("SELECT * FROM saida WHERE id_saida = '$id' LIMIT 1"));
            $row_bancos = mysql_fetch_assoc(mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[id_banco]' LIMIT 1"));

            $valor = str_replace(",", ".", $row[valor]);
            $adicional = str_replace(",", ".", $row[adicional]);
            $juros = str_replace(",", ".", $row[valor_juros]);
            $multa = str_replace(",", ".", $row[valor_multa]);
            $valor_banco = str_replace(",", ".", $row_bancos[saldo]);

            $valor_final = $valor + $adicional + $juros + $multa;
            $saldo_banco_final = $valor_banco - $valor_final;

            $valor_f = number_format($valor_final, 2, ",", ".");
            $saldo_banco_final_f = number_format($saldo_banco_final, 2, ",", ".");
            $saldo_banco_final_banco = number_format($saldo_banco_final, 2, ",", "");
//            print_array("SELECT * FROM saida WHERE id_saida = '$id' LIMIT 1");
            if ($row['status'] == "1") {

//                $periodo = substr($pagamento, 0, 4).''.substr($pagamento, 5, 2);
//                $rowTrava = mysql_fetch_array(mysql_query("SELECT * FROM contabil_trava WHERE id_projeto = $projeto_id AND periodo = '$periodo' LIMIT 1"));
//                if(empty($rowTrava['periodo'] && $rowTrava['id_projeto'])) {

                mysql_query("UPDATE saida SET status = '2', data_pg =  NOW(), id_userpg = '{$_COOKIE[logado]}', hora_pg = NOW(), saldo_anterior = '{$valor_banco}', data_vencimento = '{$data_compensar}' WHERE id_saida = '$id'");
                mysql_query("UPDATE bancos SET saldo = '$saldo_banco_final_banco' WHERE id_banco = '$row[id_banco]'");

                //LANÇAMETO CONTABIL
                if($row_bancos['adiantamento'] == 0) {
                    $rowLancamento = mysql_fetch_assoc(mysql_query("SELECT * FROM contabil_lancamento WHERE id_saida = {$id}"));
                    $objSaida->updateLancamentoContabil($rowLancamento['id_lancamento']);

                    $array_itens = array();
                    $array_itens[] = array('id_lancamento' => $rowLancamento['id_lancamento'], 'id_conta' => $row['tipo'], 'valor' => $valor, 'documento' => $row['n_documento'], 'tipo' => 2, 'historico' => '', 'id_projeto' => $projeto_id, 'fornecedor' => $prestador);
                    $array_itens[] = array('id_lancamento' => $rowLancamento['id_lancamento'], 'id_banco' => $row_bancos['id_banco'], 'valor' => $valor, 'documento' => $row['n_documento'], 'tipo' => 1, 'historico' => '', 'id_projeto' => $projeto_id);
                    $objSaida->inserirItensLancamento($array_itens);
                }
                if ($row['tipo'] == "66") {
                    mysql_query("UPDATE compra SET acompanhamento = '6' WHERE id_compra = '$row[id_compra]'");
                }

                $log->gravaLog('Pagar Saida', 'Pagameto saída ' . $id . ' saldo banco De: ' . $valor_banco . ' Para: ' . $saldo_banco_final);

//                    echo "<pre>";
//                    echo "Nº da Saída: $id_saida[$i]<br>";
//                    echo "Valor da Conta: R$ ".number_format($valor,2,",",".")."<br>";
//                    echo "Adicional: R$ ".number_format($adicional,2,",",".")."<br>";
//                    if($multa > 0){ echo "Multa : R$ ".number_format($multa,2,",",".")."<br>"; } 
//                    if($juros > 0){ echo "Juros : R$ ".number_format($juros,2,",",".")."<br>"; }
//                    echo "Total a pagar: R$ $valor_f<br>";
//                    echo "Valor no Banco: R$ ".number_format($valor_banco,2,",",".")."<br>";
//                    echo "Saldo atualizado do Banco: <strong>R$ $saldo_banco_final_f</strong>";
//                    echo "</pre>";
//                } else {
//                    echo "<pre>";
//                    echo "DATA SELECIONADA COM TRAVA !<br><br>";
//                    echo "FAVOR ENTRAR EM CONTATO COM O SETOR CONTABIL.<br>";
//                    echo "</pre>";
//                }
            }
        }
        break;

    case 'pagar':
        $objSaida = new Saida();
        echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';

        if (is_array($_POST[id])) {
            foreach ($_POST[id] as $key => $value) {
                $id_saida[] = $value[value];
            }
        } else {
            $id_saida[] = $_POST[id];
        }
        $countSaida = count($id_saida);

        for ($i = 0; $i < $countSaida; $i++) {

            $row = mysql_fetch_array(mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida[$i]' LIMIT 1"));
            $row_bancos = mysql_fetch_array(mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[id_banco]' LIMIT 1"));

            $pagamento = $row['data_vencimento'];
            $projeto_id = $row['id_projeto'];

            $valor = str_replace(",", ".", $row[valor]);
            $adicional = str_replace(",", ".", $row[adicional]);
            $juros = str_replace(",", ".", $row[valor_juros]);
            $multa = str_replace(",", ".", $row[valor_multa]);
            $valor_banco = str_replace(",", ".", $row_bancos[saldo]);

            $valor_final = $valor + $adicional + $juros + $multa;
            $saldo_banco_final = $valor_banco - $valor_final;

            $valor_f = number_format($valor_final, 2, ",", ".");
            $saldo_banco_final_f = number_format($saldo_banco_final, 2, ",", ".");
            $saldo_banco_final_banco = number_format($saldo_banco_final, 2, ",", "");

            if ($row['status'] == "1") {
                if (!empty($row['id_prestador'])) {
                    $prestador = $row['id_prestador'];
                } elseif (!empty($row['id_fornecedor'])) {
                    $prestador = $row['id_fornecedor'];
                } else {
                    $prestador = '';
                }

                if (!empty($_REQUEST['new_date'])) {
                    $new_date = implode('-', array_reverse(explode('/', $_REQUEST['new_date'])));
                    $pagamento = $new_date;
                    $auxDate = " , data_vencimento = '{$new_date}'";

                    $log->gravaLog('Pagar Saida', 'Edição da data_vencimento da saída: ' . $id_saida[$i] . '. De: ' . $row['data_vencimento'] . ' Para: ' . $new_date);
                }

                $periodo = substr($pagamento, 0, 4) . '' . substr($pagamento, 5, 2);
                $rowTrava = mysql_fetch_array(mysql_query("SELECT * FROM contabil_trava WHERE id_projeto = $projeto_id AND periodo = '$periodo' LIMIT 1"));
                if (empty($rowTrava['periodo'] && $rowTrava['id_projeto'])) {

                    mysql_query("UPDATE saida SET status = '2', valor_bruto = {$valor_final}, data_pg =  NOW(), campo3 = '1', id_userpg = '{$_COOKIE[logado]}', hora_pg = NOW(), saldo_anterior = '{$valor_banco}' $auxDate WHERE id_saida = '$id_saida[$i]'");
                    mysql_query("UPDATE bancos SET saldo = '$saldo_banco_final_banco' WHERE id_banco = '$row[id_banco]'");

                    //LANÇAMETO CONTABIL
                    if($row_bancos['adiantamento'] == 0) {
                        $rowLancamento = mysql_fetch_assoc(mysql_query("SELECT * FROM contabil_lancamento WHERE id_saida = {$id_saida[$i]}"));
                        $objSaida->updateLancamentoContabil($rowLancamento['id_lancamento']);

                        $array_itens = array();
                        $array_itens[] = array('id_lancamento' => $rowLancamento['id_lancamento'], 'id_conta' => $row['tipo'], 'valor' => $valor, 'documento' => $row['n_documento'], 'tipo' => 2, 'historico' => '', 'id_projeto' => $projeto_id, 'fornecedor' => $prestador);
                        $array_itens[] = array('id_lancamento' => $rowLancamento['id_lancamento'], 'id_banco' => $row_bancos['id_banco'], 'valor' => $valor, 'documento' => $row['n_documento'], 'tipo' => 1, 'historico' => '', 'id_projeto' => $projeto_id);
                        $objSaida->inserirItensLancamento($array_itens);
                    }
                    if ($row['tipo'] == "66") {
                        mysql_query("UPDATE compra SET acompanhamento = '6' WHERE id_compra = '$row[id_compra]'");
                    }

                    $log->gravaLog('Pagar Saida', 'Pagameto saída ' . $id_saida[$i] . ' saldo banco De: ' . $valor_banco . ' Para: ' . $saldo_banco_final);

                    echo "<pre>";
                    echo "Nº da Saída: $id_saida[$i]<br>";
                    echo "Valor da Conta: R$ " . number_format($valor, 2, ",", ".") . "<br>";
                    echo "Adicional: R$ " . number_format($adicional, 2, ",", ".") . "<br>";
                    if ($multa > 0) {
                        echo "Multa : R$ " . number_format($multa, 2, ",", ".") . "<br>";
                    }
                    if ($juros > 0) {
                        echo "Juros : R$ " . number_format($juros, 2, ",", ".") . "<br>";
                    }
                    echo "Total a pagar: R$ $valor_f<br>";
                    echo "Valor no Banco: R$ " . number_format($valor_banco, 2, ",", ".") . "<br>";
                    echo "Saldo atualizado do Banco: <strong>R$ $saldo_banco_final_f</strong>";
                    echo "</pre>";
                } else {
                    echo "<pre>";
                    echo "DATA SELECIONADA COM TRAVA !<br><br>";
                    echo "FAVOR ENTRAR EM CONTATO COM O SETOR CONTABIL.<br>";
                    echo "</pre>";
                }
            }
        }
        break;

    case 'deletar':

        if (is_array($_POST[id])) {
            foreach ($_POST[id] as $key => $value) {
                $id_saida[] = $value[value];
            }
        } else {
            $id_saida[] = $_POST[id];
        }

        for ($i = 0; $i < count($id_saida); $i++) {

            $result = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida[$i]' LIMIT 1");
            $row = mysql_fetch_array($result);

            if ($row['status'] == "1") {
                mysql_query("UPDATE saida SET status = '0', id_deletado = $_COOKIE[logado], data_deletado = NOW() WHERE id_saida = '$id_saida[$i]' LIMIT 1");
                $log->gravaLog('Excluir Saída', 'Exclusão saída ' . $id_saida[$i]);
            }
        }
        break;

    case 'form_duplicar':

        $id_saida = $_POST[id];

        $result = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida' LIMIT 1");
        $row = mysql_fetch_array($result);
        ?>
        <script>
            $(function () {
                $('.qtdCopias').keyup(function () {
                    if ($(this).val() <= 100) {
                        if ($('.datasDiferentes').prop('checked')) {
                            $('.datasDiferentes').change();
                        }
                    } else {
                        $(this).val(0);
                        alert('A quantidade de saidas deve ser menor que 100;');
                    }
                });
                $('.datasDiferentes').on('change', adicionar_campos);
                function adicionar_campos() {
                    if ($(this).prop('checked')) {
                        $('.campos_add').remove();
                        var data = null;
                        var data_emissao = null;
                        for (i = 1; i <= $('.qtdCopias').val(); i++) {
                            data = null;
                            data = ('<?php echo $row['data_vencimento'] ?>').split('-');
                            data = Date.parse(data[1] + '-' + data[0] + '-' + data[2]).add({month: i}).toString('dd/MM/yyyy');
                            //                        
                            data_emissao = null;
                            data_emissaoAr = ('<?php echo ($row['dt_emissao_nf'] != '0000-00-00') ? $row['dt_emissao_nf'] : date('Y-m-d') ?>').split('-');
                            data_emissao = Date.parse(data_emissaoAr[1] + '-' + data_emissaoAr[0] + '-' + data_emissaoAr[2]).add({month: i}).toString('dd/MM/yyyy');
                            mes_competencia = Date.parse(data_emissaoAr[1] + '-' + data_emissaoAr[0] + '-' + data_emissaoAr[2]).add({month: i}).toString('MM');
                            ano_competencia = Date.parse(data_emissaoAr[1] + '-' + data_emissaoAr[0] + '-' + data_emissaoAr[2]).add({month: i}).toString('yyyy');

                            $('#tabelaCopia').append(
                                    $('<tr>', {class: 'campos_add valign-middle'}).append(
                                    $('<td>', {html: 'Data Vencimento ' + i}).append($('<input>', {type: 'text', class: "data form-control", name: "campos[" + i + "][vencimento]", value: data})),
                                    $('<td>', {html: 'Data Emiss&atilde;o ' + i}).append($('<input>', {type: 'text', class: "data form-control", name: "campos[" + i + "][emissao]", value: data_emissao})),
                                    $('<td>', {html: 'Compet&ecirc;ncia ' + i}).append(
                                    $('<div>', {class: 'input-group'}).append(
                                    $('<input>', {type: 'text', class: "form-control", name: "campos[" + i + "][mes]", value: mes_competencia}),
                                    $('<div>', {class: 'input-group-addon', html: '/'}),
                                    $('<input>', {type: 'text', class: "form-control", name: "campos[" + i + "][ano]", value: ano_competencia}),
                                    )

                                    ),
                                    )
                                    );
                            $('.data').datepicker({dateFormat: 'dd/mm/yy'});
                        }
                    } else {
                        $('.campos_add').remove();
                    }
                }
            });
        </script>
        <form name="form_duplicar" id="form_duplicar" method="POST" action="">
            <table class="table table-bordered table-condensed text-sm" id="tabelaCopia">
                <tr class="valign-middle">
                    <td>Quantidade</td>
                    <td><input type="text" value="0" class="form-control qtdCopias" name="quant"/></td>
                    <td><input type="checkbox" name="datas" class="datasDiferentes"/> Criar saidas com datas diferentes</td>
                </tr>
                <tr>
                <input type="hidden" name="id_saida" id="id_saida" value="<?= $id_saida ?>" />
                <input type="hidden" name="action" value="duplicar_saida" />
                <!--<td align="center"><input type="submit" value="DUPLICAR"/></td>-->
                </tr>
            </table>
        </form>
        <?php
        break;

    case 'duplicar_saida':
        $objSaida = new Saida();

        function duplica_saida($array_colunas, $array_valores, $vencimento = FALSE, $emissao = FALSE, $mes = FALSE, $ano = FALSE) {
            if ($vencimento != FALSE) {
                $array_valores['data_vencimento'] = $vencimento;
            }
            if ($emissao != FALSE) {
                $array_valores['dt_emissao_nf'] = $emissao;
            }
            if ($mes != FALSE) {
                $array_valores['mes_competencia'] = $mes;
            }
            if ($ano != FALSE) {
                $array_valores['ano_competencia'] = $ano;
            }

            $new_array = array_map(
                    create_function('$key, $value', 'return $key." = \'".$value."\' ";'), array_keys($array_valores), array_values($array_valores)
            );

            return 'INSERT INTO saida SET ' . implode(' , ', $new_array);
        }

        $id_saida = $_POST['id_saida'];
        $quant = $_POST['quant'];
        $campos = $_POST['campos'];
        // Pegando as colunas
        $qr_colunas = mysql_query("DESCRIBE saida");
        while ($row_colunas = mysql_fetch_assoc($qr_colunas)) {
            $array_colunas[] = $row_colunas['Field'];
        }

        // Pegando os dados da saida
        $qr_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida'");
        $row_saida = mysql_fetch_assoc($qr_saida);

        // retirando o id_saida do array
        $row_saida['id_saida'] = NULL;
        // amanda 
        $row_saida['id_user'] = $_COOKIE['logado'];
        $row_saida['data_proc'] = date("Y-m-d H:i:s");
        $row_saida['duplicada'] = 1;
//        $sql = array();

        if ($_POST['datas'] == 'on' and ! empty($campos)) {
            foreach ($campos as $campo) {
                $sql = duplica_saida($array_colunas, $row_saida, implode('-', array_reverse(explode('/', $campo['vencimento']))), implode('-', array_reverse(explode('/', $campo['emissao']))), $campo['mes'], $campo['ano']);
//                print_array($sql);exit;
                mysql_query($sql) or die(mysql_error());
                $novaSaida = mysql_insert_id();
                $log->gravaLog('Duplicar Saída', 'Saída ' . $id_saida . ' duplicada nova saida id: ' . $novaSaida);

                //LANÇAMETO CONTABIL
                list($dia, $mes, $ano) = explode('/', $data);
                $array_lancamento = array('id_saida' => $novaSaida, 'id_projeto' => $row_saida['id_projeto'], 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => $row_saida['data_vencimento'], 'historico' => $row_saida['nome']);
                $id_lancamento = $objSaida->inserirLancamento($array_lancamento, $mes, $ano);

//                $array_itens = array();
//                $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_tipo' => $row_saida['tipo'], 'valor' => $row_saida['valor'], 'documento' => $row_saida['n_documento'], 'tipo' => 2, 'id_projeto' => $row_saida['id_projeto']);
//                $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_banco' => $row_saida['id_banco'], 'valor' => $row_saida['valor'], 'documento' => $row_saida['n_documento'], 'tipo' => 1);
//                $objSaida->inserirItensLancamento($array_itens);
            }
        } else {
            for ($i = 0; $i < $quant; $i++) {
                $sql = duplica_saida($array_colunas, $row_saida);
                //echo $sql.'<br>';
                mysql_query($sql) or die(mysql_error());
                $novaSaida = mysql_insert_id();
                $log->gravaLog('Duplicar Saída', 'Saída ' . $id_saida . ' duplicada nova saida id: ' . $novaSaida);

                //LANÇAMETO CONTABIL
                list($ano, $mes, $dia) = explode('-', $row_saida['data_vencimento']);
                $array_lancamento = array('id_saida' => $novaSaida, 'id_projeto' => $row_saida['id_projeto'], 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => $row_saida['data_proc'], 'historico' => $row_saida['nome']);
                $id_lancamento = $objSaida->inserirLancamento($array_lancamento, $mes, $ano);

//                $array_itens = array();
//                $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_tipo' => $row_saida['tipo'], 'valor' => $row_saida['valor'], 'documento' => $row_saida['n_documento'], 'tipo' => 2, 'id_projeto' => $row_saida['id_projeto']);
//                $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_banco' => $row_saida['id_banco'], 'valor' => $row_saida['valor'], 'documento' => $row_saida['n_documento'], 'tipo' => 1);
//                $objSaida->inserirItensLancamento($array_itens);
            }
        }
//        print_array($sql);
//        foreach($sql as $qr){
//            mysql_query($qr) or die(mysql_error());
//            $novaSaida = mysql_insert_id();
//            $log->gravaLog('Duplicar Saída', 'Saída '.$id_saida.' duplicada nova saida id: '.$novaSaida);
//        }
        break;

    case 'upload_anexo':
//        print_array($_REQUEST);print_r($_FILES);exit;
        $id_saida = explode(',', $_REQUEST['id_saida']);

        $cod_barras = str_replace(' ', '', $_REQUEST['cod_barras']);

//        $diretorio = "../comprovantes/saida";
        $diretorio = "../../comprovantes";

        for ($i = 0; $i < count($id_saida); $i++) {

            $upload = new UploadFile($diretorio, array('jpg', 'gif', 'png', 'pdf', 'JPG', 'GIF', 'PNG', 'PDF'));
            $upload->arquivo($_FILES[file]);
            $upload->verificaFile();

            if (empty($copia)) {

                $id_saida[$i] = str_replace(" ", "", $id_saida[$i]);

                if ($_REQUEST['tipo_anexo'] == 1) {
                    $insert = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida[$i]','.$upload->extensao');";
                    $tipo_anexo = 'Anexo';
                } else if ($_REQUEST['tipo_anexo'] == 2) {
                    $insert = "INSERT INTO saida_files_pg (id_saida, tipo_pg) VALUES ('$id_saida[$i]','.$upload->extensao');";
                    $tipo_anexo = 'Comprovante';
                    $auxNome = "_pg";
                    mysql_query("UPDATE saida SET comprovante = 2 WHERE id_saida = {$id_saida[$i]} LIMIT 1;");
                }
//                $insert = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida[$i]','.$upload->extensao');";
//                $tipo_anexo = 'Anexo';
                mysql_query($insert)or die(mysql_error());
                $id = mysql_insert_id();

//                print_array($id_saida[$i]); 
                $upload->NomeiaFile("$id.$id_saida[$i]$auxNome");
                $upload->Envia();
                $log->gravaLog('Anexo Saída', "$tipo_anexo $id inserido na Saída $id_saida[$i]");

                $copia = "$diretorio/$id.$id_saida[$i]$auxNome.$upload->extensao";
                $extensao = $upload->extensao;

                if (!empty($cod_barras)) {
                    $upS = "UPDATE saida SET cod_barra_consumo = '{$cod_barras}', tipo_boleto = 1 WHERE id_saida = '{$id_saida[$i]}' LIMIT 1;";
                    mysql_query($upS) or die(mysql_error());
                }
            } else {

                $id_saida[$i] = str_replace(" ", "", $id_saida[$i]);

                if ($_REQUEST['tipo_anexo'] == 1) {
                    $insert = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida[$i]','.$upload->extensao');";
                    $tipo_anexo = 'Anexo';
                } else if ($_REQUEST['tipo_anexo'] == 2) {
                    $insert = "INSERT INTO saida_files_pg (id_saida, tipo_pg) VALUES ('$id_saida[$i]','.$upload->extensao');";
                    $tipo_anexo = 'Comprovante';
                    $auxNome = "_pg";
                    mysql_query("UPDATE saida SET comprovante = 2 WHERE id_saida = {$id_saida[$i]} LIMIT 1;");
                }

                mysql_query($insert)or die(mysql_error());
                $id = mysql_insert_id();

                $newfile = "$diretorio/$id.$id_saida[$i]$auxNome.$extensao";

                $log->gravaLog('Anexo Saída', "$tipo_anexo $id inserido na Saída $id_saida[$i]");
                if (!copy($copia, $newfile)) {
                    $errors = error_get_last();
                    echo "COPY ERROR: " . $errors['type'];
                    echo "<br />\n" . $errors['message'];
                }

                if (!empty($cod_barras)) {
                    $upS = "UPDATE saida SET cod_barra_consumo = '{$cod_barras}', tipo_boleto = 1 WHERE id_saida = '{$id_saida[$i]}' LIMIT 1;";
                    mysql_query($upS) or die(mysql_error());
                }
            }
        }
        break;

    case 'deleteAnexoSaida':

        $id_files = $_REQUEST['id'];
        //nao tem status
        if (mysql_query("UPDATE saida_files SET id_saida = 0 WHERE id_saida_file = $id_files LIMIT 1;")) {
            $log->gravaLog('Excluir Anexo Saída', 'Anexo ' . $id . ' excluido');
            echo "Anexo excluido com sucesso!";
        } else {
            echo "Erro ao excluir o anexo!";
        }
        break;

    case 'deleteComprovanteSaida':

        $id_files = $_REQUEST['id'];

        $id_saida = mysql_result(mysql_query("SELECT id_saida FROM saida_files_pg WHERE id_pg = $id_files LIMIT 1"), 0);

        //nao tem status
        if (mysql_query("UPDATE saida_files_pg SET id_saida = 0 WHERE id_pg = $id_files LIMIT 1;")) {
            mysql_query("UPDATE saida SET comprovante = 0 WHERE id_saida = $id_saida LIMIT 1;");
            $log->gravaLog('Excluir Anexo Saída', 'Anexo ' . $id . ' excluido');
            echo "Anexo excluido com sucesso!";
        } else {
            echo "Erro ao excluir o anexo!";
        }
        break;

    case 'cadastrar_reembolso_saida':

        $id_reembolso = $_REQUEST['reembolso'];
        mysql_query("UPDATE fr_reembolso SET data_apro=NOW(), user_apro='{$_COOKIE['logado']}', status='2' WHERE id_reembolso = '$id_reembolso' LIMIT 1") or die(mysql_error());
        $log->gravaLog('Aprovar Reembolso', 'Reembolso ' . $id_reembolso . ' aprovado');

        $objSaida = new Saida();
        $id_saida = $objSaida->cadReembolsoSaida();
        $log->gravaLog('Cadastrar Saída', 'Cadastro Saída ' . $id_saida);

        echo "Saida cadastrada com sucesso!";
        break;

    case 'carregaNomes':

        $tipo_nome = $_REQUEST['tipo_nome'];
        $regiao = $_REQUEST['regiao'];
        $tipo = $_REQUEST['tipo'];
        if ($tipo_nome == 'clt') {
            $qry = mysql_query("SELECT id_clt, nome FROM rh_clt WHERE id_regiao = {$regiao} ORDER BY nome");
            while ($row_nome = mysql_fetch_assoc($qry)) {
                $rs[$row_nome['id_clt']] = $row_nome['nome'];
            }
        } else if ($tipo_nome == 'cooperado') {
            $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE id_regiao = {$regiao} AND tipo_contratacao = '3' ORDER BY nome");
            while ($row_nome = mysql_fetch_assoc($qry)) {
                $rs[$row_nome['id_autonomo']] = $row_nome['nome'];
            }
        } else if ($tipo_nome == 'autonomo') {
            $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE id_regiao = {$regiao} AND tipo_contratacao = '1' ORDER BY nome");
            while ($row_nome = mysql_fetch_assoc($qry)) {
                $rs[$row_nome['id_autonomo']] = $row_nome['nome'];
            }
        } else if ($tipo_nome == 'pj') {
            $qry = mysql_query("SELECT id_autonomo, nome FROM autonomo WHERE id_regiao = {$regiao} AND tipo_contratacao = '4' ORDER BY nome");
            while ($row_nome = mysql_fetch_assoc($qry)) {
                $rs[$row_nome['id_autonomo']] = $row_nome['nome'];
            }
        } else if ($tipo_nome == 'outro') {
//            $rs = GlobalClass::carregaNomesByTipo($tipo, $defaults[$opt]);
            $rs = GlobalClass::carregaNomes($defaults[$opt]);
        }

        $nome = "";
        foreach ($rs as $k => $val) {
            $nome .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
        }
        echo $nome;

        break;

    case 'getSaidaDados':
        $objSaida = new Saida();
        $id_saida = $_REQUEST['id'];
        $dadosSaida = $objSaida->getSaidaID($id_saida);
        foreach ($dadosSaida as $key => $value) {
            $novo[$key] = utf8_encode($value);
        }
        echo json_encode($novo);
        break;

    case 'gerarParcelas':
//        print_array($_REQUEST);

        $objSaida = new Saida();

        // pegando os request
        $id_saida = $_REQUEST['id_saida'];
//        $valor_1 = str_replace('.', '', $_REQUEST['valor'][0]);
        $valor_1 = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor'][0]));
        $data_1 = converteData($_REQUEST['data'][0], 'Y-m-d');
        $n_parcelas = $_REQUEST['n_parcelas'];

        // pegando a saida
        $qq = "SELECT * FROM saida WHERE id_saida = $id_saida;";
        $rr = mysql_query($qq) or die($qq . ' - ' . mysql_error());
        $dadosSaida = mysql_fetch_assoc($rr);

        // pegando as files
        $query_file = "SELECT * FROM saida_files WHERE id_saida = $id_saida";
        $rs = mysql_query($query_file) or die($query_file . ' - ' . mysql_error());
        while ($row1 = mysql_fetch_assoc($rs)) {
            $file[] = $row1;
        }

        // update da saida (setando como parcela)
        $q1 = "UPDATE saida SET nome = '{$dadosSaida['nome']} - TOTAL: {$dadosSaida['valor']} - Parcela 1/$n_parcelas', valor = '{$valor_1}', valor_bruto = '{$valor_1}', data_vencimento = '$data_1', parcela = 1 WHERE id_saida = '$id_saida'";
        mysql_query($q1) or die($q1 . ' - ' . mysql_error());

        unset($dadosSaida['id_saida']);
        for ($i = 2; $i <= $n_parcelas; $i++) {
            $arr_x = $dadosSaida;

            $arr_x['nome'] = $dadosSaida['nome'] . " - TOTAL: {$dadosSaida['valor']} - Parcela $i/$n_parcelas";
            $arr_x['valor'] = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor'][$i - 1]));
            $arr_x['valor_bruto'] = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor'][$i - 1]));
            $arr_x['data_vencimento'] = converteData($_REQUEST['data'][$i - 1], 'Y-m-d');
            $arr_x['parcela'] = $i;

            $colunas = implode(', ', array_keys($arr_x));
            $valores = implode("', '", array_values($arr_x));

            $q = "INSERT INTO saida ($colunas) VALUES ('$valores')";
            mysql_query($q) or die($q . ' - ' . mysql_error());

            $last_id = mysql_insert_id();

            foreach ($file as $f) {
                $arquivo_1 = "../../comprovantes/{$f['id_saida_file']}.$id_saida.pdf";
                if(file_exists($arquivo_1)) {
                    $query_file = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('{$last_id}','{$f['tipo_saida_file']}');";
                    if (mysql_query($query_file)) {
                        $id_files = mysql_insert_id();
                        $arquivo_1 = "../../comprovantes/{$f['id_saida_file']}.$id_saida.pdf";
                        $arquivo_2 = "../../comprovantes/{$id_files}.{$last_id}.pdf";
                        $confirmacao = copy($arquivo_1, $arquivo_2);
                        if (!$confirmacao) {
                            die("erro ao copiar arquivo (id_nfse = {$arquivo['id_nfse']} - id_saida = {$id_saida})");
    //                            exit("erro ao copiar arquivo (id_nfse = {$arquivo['id_nfse']} - id_saida = {$id_saida})");
                        }
                    } else {
                        die($query_file . ' - ' . mysql_error());
                    }
                }
            }
        }

        echo json_encode(['status' => TRUE]);

    break;
    
    case 'estornar':
        
        $objSaida = new Saida();
        $result = $objSaida->gerarEstorno($_POST['id']);
        echo $result;
        
    break;
        
    case 'verSaida': 
        $objSaida = new Saida();
        $saida = $objSaida->getSaidaID($_POST['id']);
        $saida2 = ($saida['id_saida_estorno']) ? $objSaida->getSaidaID($saida['id_saida_estorno']) : null;
//        print_array($saida); ?>
        <form class="form-horizontal">
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="text-bold">Nome:</div>
                    <div class=""><?php echo utf8_encode($saida['nome']) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="text-bold">Descri&ccedil;&atilde;o:</div>
                    <div class=""><?php echo utf8_encode($saida['especifica']) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Grupo:</div>
                    <div class=""><?php echo utf8_encode($saida['nomeGrupo']) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">Subgrupo:</div>
                    <div class=""><?php echo utf8_encode("{$saida['subgrupo']} | {$saida['nomeSubGrupo']}") ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Tipo:</div>
                    <div class=""><?php echo utf8_encode($saida['nomeTipo']) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">Data Vencimento:</div>
                    <div class=""><?php echo implode('/', array_reverse(explode('-', $saida['data_vencimento']))) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Data Emiss&atilde;o:</div>
                    <div class=""><?php echo implode('/', array_reverse(explode('-', $saida['dt_emissao_nf']))) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">N&ordm; Documento:</div>
                    <div class=""><?php echo $saida['n_documento'] ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3">
                    <div class="text-bold">Valor Bruto:</div>
                    <div class=""><?php echo number_format($saida['valor_bruto'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Valor Multa:</div>
                    <div class=""><?php echo number_format($saida['valor_multa'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Valor Juros:</div>
                    <div class=""><?php echo number_format($saida['valor_juros'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Desconto:</div>
                    <div class=""><?php echo number_format(str_replace(',', '.', $saida['desconto']), 2, ',', '.') ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3">
                    <div class="text-bold">Valor Liquido:</div>
                    <div class=""><?php echo number_format(str_replace(',', '.', $saida['valor']), 2, ',', '.') ?></div>
                </div>
            </div>
            <?php if($saida2) { ?>
            <hr><strong>ESTORNADA DA SAÍDA ID <?php echo $saida2['id_saida_ertorno'] ?> - <?php echo ($saida2['status'] == 1) ? '<i class="text-warning">À PAGAR</i>' : (($saida2['status'] == 2) ? '<i class="text-success">PAGA</i>' : '<i class="text-danger">EXCLUÍDA</i>') ?></strong><hr>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="text-bold">Nome:</div>
                    <div class=""><?php echo utf8_encode($saida2['nome']) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="text-bold">Descri&ccedil;&atilde;o:</div>
                    <div class=""><?php echo utf8_encode($saida2['especifica']) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Grupo:</div>
                    <div class=""><?php echo utf8_encode($saida2['nomeGrupo']) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">Subgrupo:</div>
                    <div class=""><?php echo utf8_encode("{$saida2['subgrupo']} | {$saida2['nomeSubGrupo']}") ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Tipo:</div>
                    <div class=""><?php echo utf8_encode($saida2['nomeTipo']) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">Data Vencimento:</div>
                    <div class=""><?php echo implode('/', array_reverse(explode('-', $saida2['data_vencimento']))) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Data Emiss&atilde;o:</div>
                    <div class=""><?php echo implode('/', array_reverse(explode('-', $saida2['dt_emissao_nf']))) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">N&ordm; Documento:</div>
                    <div class=""><?php echo $saida2['n_documento'] ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3">
                    <div class="text-bold">Valor Bruto:</div>
                    <div class=""><?php echo number_format($saida2['valor_bruto'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Valor Multa:</div>
                    <div class=""><?php echo number_format($saida2['valor_multa'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Valor Juros:</div>
                    <div class=""><?php echo number_format($saida2['valor_juros'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Desconto:</div>
                    <div class=""><?php echo number_format(str_replace(',', '.', $saida2['desconto']), 2, ',', '.') ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3">
                    <div class="text-bold">Valor Liquido:</div>
                    <div class=""><?php echo number_format(str_replace(',', '.', $saida2['valor']), 2, ',', '.') ?></div>
                </div>
            </div>
            </div>
            <?php } ?>
            <?php if($saida['agrupada']) { ?>
            <?php $agrupada = $objSaida->getSaidaID($saida['id_saida_agrupamento']); ?>
            <hr><strong>AGRUPADA NA SAÍDA ID <?php echo $saida['id_saida_agrupamento'] ?> - <?php echo ($agrupada['status'] == 1) ? '<i class="text-warning">À PAGAR</i>' : (($agrupada['status'] == 2) ? '<i class="text-success">PAGA</i>' : '<i class="text-danger">EXCLUÍDA</i>') ?></strong><hr>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="text-bold">Nome:</div>
                    <div class=""><?php echo utf8_encode($agrupada['nome']) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="text-bold">Descri&ccedil;&atilde;o:</div>
                    <div class=""><?php echo utf8_encode($agrupada['especifica']) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Grupo:</div>
                    <div class=""><?php echo utf8_encode($agrupada['nomeGrupo']) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">Subgrupo:</div>
                    <div class=""><?php echo utf8_encode("{$agrupada['subgrupo']} | {$agrupada['nomeSubGrupo']}") ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Tipo:</div>
                    <div class=""><?php echo utf8_encode($agrupada['nomeTipo']) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">Data Vencimento:</div>
                    <div class=""><?php echo implode('/', array_reverse(explode('-', $agrupada['data_vencimento']))) ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6">
                    <div class="text-bold">Data Emiss&atilde;o:</div>
                    <div class=""><?php echo implode('/', array_reverse(explode('-', $agrupada['dt_emissao_nf']))) ?></div>
                </div>
                <div class="col-xs-6">
                    <div class="text-bold">N&ordm; Documento:</div>
                    <div class=""><?php echo $agrupada['n_documento'] ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3">
                    <div class="text-bold">Valor Bruto:</div>
                    <div class=""><?php echo number_format($agrupada['valor_bruto'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Valor Multa:</div>
                    <div class=""><?php echo number_format($agrupada['valor_multa'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Valor Juros:</div>
                    <div class=""><?php echo number_format($agrupada['valor_juros'], 2, ',', '.') ?></div>
                </div>
                <div class="col-xs-3">
                    <div class="text-bold">Desconto:</div>
                    <div class=""><?php echo number_format(str_replace(',', '.', $agrupada['desconto']), 2, ',', '.') ?></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3">
                    <div class="text-bold">Valor Liquido:</div>
                    <div class=""><?php echo number_format(str_replace(',', '.', $agrupada['valor']), 2, ',', '.') ?></div>
                </div>
            </div>
            </div>
            <?php } ?>
        </form>
        

        <?php
        break;

    default:
        echo 'action: ' . $action;
        break;
}
