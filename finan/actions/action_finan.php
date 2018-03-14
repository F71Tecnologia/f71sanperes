<?php

include "../../conn.php";
require("../../wfunction.php");
require("../../funcoes.php");
require("../../classes/FinaceiroClass.php");
require("../../classes/EntradaClass.php");
require("../../classes/SaidaClass.php");
require("../../classes/CaixinhaClass.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();

$objFinanceiro = new Financeiro();
$objCaixinha = new CaixinhaClass();

$objSaida = new Saida();

$objEntrada = new Entrada();

switch ($action) {
    case 'load_table_financeiro' :
        
        $condicao[] = ($_REQUEST['data_ini']) ? "A.data_vencimento >= '" . implode('-',array_reverse(explode('/', $_REQUEST['data_ini']))) . "'" : '';
        $condicao[] = ($_REQUEST['data_fim']) ? "A.data_vencimento <= '" . implode('-',array_reverse(explode('/', $_REQUEST['data_fim']))) . "'" : '';
        $condicao[] = ($_REQUEST['tipo'] && $_REQUEST['tipo'] != 't') ? "A.tipo = '{$_REQUEST['tipo']}'" : '';
        $condicao = array_filter($condicao);
        
//        print_array($condicao); exit;
        
        function link_editar_saida($tipo_saida, $id_saida, $link_enc) {
            ////ESTE ARRAY CONTÉM OS TIPOS DE SAÌDA QUE SÒ PODEM SER EDITADO A DATA DE VENCIMENTO Ex: RESCISÔES QUE VEM DOS "PAGAMENTOS" NA GESTÃO DE RH
//            $arraySaidaRh = array(168, 167, 169, 170, 156, 154, 29, 30, 31, 32, 260, 171); //array(29, 30, 31, 32, 51, 76, 154, 156, 167, 168, 169, 170, 171, 175, 260);
//            $array_nao_editaveis = array(218, 219,221,222,223,226,227); //array(29, 30, 31, 32, 51, 76, 154, 156, 167, 168, 169, 170, 171, 175, 260);
            //$array_nao_editaveis = array(167, 175, 168, 169, 260);
//            if ($tipo_saida > 4 ) { // descomentar dps
//                if (!in_array($tipo_saida, $array_nao_editaveis)) {
                    $classe = ""; $action = "editar_saida"; $href='form_saida.php?id_saida='.$id_saida;
//                } else {
//                    $classe = "editar_saida"; $action = "editar_saida_data"; $url='actions/action.saida.php'; $href='javascript:void(0);';
//                }
//            } else {
//                $classe = "editar_saida"; $action = "editar_saida_n_paga"; $url='actions/action.saida.php'; $href='javascript:void(0);';$ico = "<i class='fa fa-ban'></i>";
//            }// descomentar dps
            $editar = "<a class=\"btn btn-xs btn-warning $classe btnAcoes\" href='$href' data-action='$action' data-url='$url' data-key='$id_saida' data-toggle='tooltip' title='EDITAR SAIDA'><i class='fa fa-pencil'></i>$ico</a>";
            return $editar;
        }
        $dados = $objFinanceiro->getSaidaEntradaBanco(null, $_REQUEST['id_banco'],false, $condicao);
        //if($_REQUEST['id_banco'] == 168){ print_array($dados); } ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <?php if (count($dados['1']) == 0 && count($dados['2']) == 0 && count($dados['3']) == 0 && count($dados['4']) == 0){ ?>
            <div class="alert alert-warning">Nenhuma Saída Encontrada!</div>
        <?php } else { ?>
            <table class="table table-condensed table-bordered text-sm">
                <thead>
                    <tr class="bg-primary valign-middle">
                        <th colspan="2" width="15%"></th>
                        <th width="5%">Cod.</th>
                        <th width="35%">Nome</th>
                        <th width="5%">Nº</th>
                        <th class="text-center" width="10%">Data vencimento</th>
                        <th class="text-center" width="10%"> Valor</th>
                        <?php if(!$_COOKIE['acelerar']) { ?>
                        <th class="text-center" width="8%">Pagar</th>
                        <th class="text-center" width="5%">Deletar</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dados as $chave => $itens){ 
                        switch ($chave) {
                            case '1': $cor = 'warning'; $chaveTexto = 'SAÍDAS COM VENCIMENTO HOJE.'; break;
                            case '2': $cor = 'danger'; $chaveTexto = 'SAÍDAS VENCIDAS.'; break;
                            case '3': $cor = 'info'; $chaveTexto = 'PRÓXIMAS SAIDAS.'; break;
                            case '4': $cor = 'success'; $chaveTexto = 'ENTRADAS.'; break;
                        } ?>
                        <tr class="<?=$cor?>">
                            <td colspan="7"><?=$chaveTexto?></td>
                            <?php if(!$_COOKIE['acelerar']) { ?>
                            <td colspan="2"></td>
                            <?php } ?>
                        </tr>
                        <?php foreach ($itens as $row_saida){ 
                            $total[$chave] += str_replace(',','.',$row_saida['valor']);
                            $id = ($chave == 4) ? $row_saida['id_entrada'] : $row_saida['id_saida'];
                            $tipo = ($chave == 4) ? 'entrada' : 'saida' ;
                            $totalizador_individual += $row_saida['total']; ?>
                            <tr class="valign-middle trsaida <?=$tipo.$id?> <?=$row_saida['tipo']?>">
                                <td class="text-center"><input type="checkbox" class="<?=$tipo?>s_check" name="<?=$tipo?>s[]" value="<?=$id?>" data-id="<?=$id?>" data-nome="<?=$row_saida['nome']?>" data-valor="R$ <?= number_format($row_saida['total'], 2, ',', '.') ?>" /></td>
                                <td class="text-center">
                                    <?php if(!$_COOKIE['acelerar']) { ?>
                                    <?php if ($chave != 4){ ?>
                                        <button type="button" class="btn btn-xs btn-default duplicarSaida" title="Duplicar saida" data-key="<?=$id?>" data-toggle="tooltip">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php if ($tipo == 'saida') { ?>
                                        <?php if($row_saida['anexos_pg'] > 0) { ?>
                                            <button type="button" class="btn btn-xs btn-primary verComprovante" title="Ver Comprovante de Pagamento" data-key="<?=$row_saida['id_saida']?>" data-toggle="tooltip">
                                                <i class="fa fa-money"></i>
                                            </button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-xs btn-info anexarSaida btnAcoes" title="Gerenciar Anexos" data-key="<?=$row_saida['id_saida']?>" data-toggle="tooltip">
                                            <?php if(($row_saida['anexos'] + $row_saida['anexo_rescisao']) > 0){ ?><span class="bg-warning testesapn text-primary"><?= ($row_saida['anexos'] + $row_saida['anexo_rescisao']) ?></span><?php } ?>
                                            <i class="fa fa-paperclip"></i>
                                        </button>
                                    <?php } elseif ($tipo == 'entrada') { ?>
                                        <?php if ($row_saida['id_notas']){ ?>
                                            <button type="button" class="btn btn-xs btn-primary verNotas" data-key="<?=$row_saida['id_notas']?>" title="Ver Notas" data-toggle="tooltip">
                                                <i class="fa fa-file"></i>
                                            </button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-xs btn-info anexarEntrada btnAcoes" title="Gerenciar Anexos" data-key="<?=$row_saida['id_entrada']?>" data-toggle="tooltip">
                                            <?php if($row_saida['anexos'] > 0){ ?><span class="bg-warning testesapn text-primary"><?= $row_saida['anexos'] ?></span><?php } ?>
                                            <i class="fa fa-paperclip"></i>
                                        </button>
                                    <?php } ?>
                                    <?php if(!$_COOKIE['acelerar']) { ?>
                                        <?php if (!empty($row_saida['id_saida'])) { ?>
                                            <a class="btn btn-xs btn-warning btnAcoes" href='form_saida.php?id_saida=<?php echo $row_saida['id_saida'] ?>' target="_blank" data-action='editar_saida' data-url='' data-key='<?php echo $row_saida['id_saida'] ?>' data-toggle='tooltip' title='EDITAR SAIDA'><i class='fa fa-pencil'></i></a>
                                        <?php } else if ($chave == 4) { ?>	
                                            <button type="button" class="btn btn-xs btn-warning editar_entrada" id="e<?= $row_saida['id_entrada'] ?>" data-id="<?= $row_saida['id_entrada'] ?>" data-tipo="entrada" data-toggle="tooltip" title="Editar Entrada"><i class="fa fa-pencil"></i></button>
                                        <?php } ?>
                                    <?php } ?>
                                    <button type="button" class="btn btn-xs btn-primary detalheSaida"  data-id="<?=$id?>" data-tipo="<?=$tipo?>" title="Detalhes" data-toggle="tooltip">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <?php if(!$_COOKIE['acelerar']) { ?>
                                    <a href="nota_debito.php?id=<?php echo $row_saida['id_saida'] ?>" target="_blank" class="btn btn-xs btn-default" title="Nota de Debito" data-toggle="tooltip">
                                        <i class="fa fa-print"></i>
                                    </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center"><?= $id ?></td>
                                <td class="text-left"><?= utf8_decode($row_saida['nome']) ?></td>
                                <td class="text-center"><?= $row_saida['n_documento'] ?></td>
                                <td class="text-center"><?= ConverteData($row_saida['data_vencimento'], 'd/m/Y') ?></td>
                                <td class="text-right">R$ <?= number_format(str_replace(',','.',$row_saida['valor']), 2, ',', '.') ?></td>
                                <?php if(!$_COOKIE['acelerar']) { ?>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-success pagar<?=(!empty($row_saida['id_entrada'])) ? "Entrada" : "Saida"?>" data-toggle="tooltip" title="<?=(!empty($row_saida['id_entrada'])) ? "Confirmar Entrada" : "Pagar Saida"?>" data-key="<?=$id?>" data-periodo="<?= $row_saida['data_vencimento'] ?>">
                                        <i class="fa fa-plus" alt="Editar" border="0"></i>
                                    </button>
                                    <?php if(!$row_saida['caixinha']) { ?>
                                    <button type="button" class="btn btn-xs btn-info pagarPeloCaixinha" data-toggle="tooltip" title="<?=(!empty($row_saida['id_entrada'])) ? "Confirmar Entrada Pelo Caixinha" : "Pagar Saida Pelo Caixinha"?>" data-key="<?=$id?>" data-tipo="<?=(!empty($row_saida['id_entrada'])) ? 2 : 1 ?>" data-periodo="<?= $row_saida['data_vencimento'] ?>">
                                        <i class="fa fa-money" alt="" border="0"></i>
                                    </button>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-xs btn-danger deletar<?=(!empty($row_saida['id_entrada'])) ? "Entrada" : "Saida"?>" data-toggle="tooltip" title="Deletar <?=(!empty($row_saida['id_entrada'])) ? "Entrada" : "Saida"?>" data-key="<?=$id?>">
                                        <i class="fa fa-trash-o" border="0"></i>
                                    </button>
                                </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <?php if(count($itens) > 0){ ?>
                            <tr class="text-bold <?=$cor?>">
                                <td colspan="6" class="text-right">TOTAL <?= $chaveTexto ?></td>
                                <td class="text-right">R$ <?= number_format($total[$chave], 2, ',', '.') ?></td>
                                <?php if(!$_COOKIE['acelerar']) { ?>
                                <td colspan="2"></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        <?php } 
    break;
    
    case 'pagarPeloCaixinha' :
        
//        print_array($_REQUEST);
        $sql = "SELECT * FROM saida WHERE id_saida = '{$_REQUEST['id']}' LIMIT 1;";
        $row = mysql_fetch_assoc(mysql_query($sql));
        $valor = str_replace(',','.',$row['valor']);
        
        $objCaixinha->setIdProjeto($row['id_projeto']);
        $saldo = $objCaixinha->getSaldoCaixinhasByMes();
        
        if($_REQUEST['tipo'] == 2 || $saldo >= $valor) {
        
            $objCaixinha->setData(date('Y-m-d'));
            $objCaixinha->setTipo($_REQUEST['tipo']);
            $objCaixinha->setDescricao("PAGAMENTO DA SAIDA: {$_REQUEST['id']}");
            $objCaixinha->setSaldo($valor);
            $objCaixinha->setDataCad(date('Y-m-d'));
            $objCaixinha->setUserCad($usuario['id_funcionario']);
            $objCaixinha->setStatus(1);
            $objCaixinha->insert();

            mysql_query("UPDATE saida SET status = 2 AND pago_pelo_caixinha = 1 WHERE id_saida = '{$_REQUEST['id']}' LIMIT 1;") or die(mysql_error());
            
            $array = ['status' => 1, 'msg' => "Saida paga em caixinha com sucesso!"];
            
        } else {
            $array = ['status' => 0, 'msg' => "Saldo Caixinha Insuficiente! Saldo Caixinha atual: R$ " . number_format($saldo, 2, ',', '.')];
        }
        echo json_encode($array);
        
    break;
    
    case 'formConciliar' :
        
        $rowSaida = $objSaida->getSaidaID($_POST['id']);
        
        $arrayAdiantamento = $objSaida->getAdiantamentoByPrestador($rowSaida['id_prestador']); ?>
        <table class="table table-condensed table-bordered">
            <thead><input type="hidden" id="adiantamento-saida" value="<?php echo $_POST['id'] ?>"></thead>
            <tbody>
            <?php foreach ($arrayAdiantamento as $key => $value) { ?>
                <tr>
                    <td><input type="checkbox" class="radioConciliar" data-saida="<?php echo $_POST['id'] ?>" value="<?php echo $value['id_saida'] ?>" name="adiantamento"></td>
                    <td><?php echo $value['id_saida'] ?></td>
                    <td><?php echo $value['nome'] ?></td>
                    <td><?php echo implode('/', array_reverse(explode('-', $value['data_vencimento']))) ?></td>
                    <td><?php echo $value['n_documento'] ?></td>
                    <td><?php echo number_format($value['valor'], 2, ',', '.') ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php 
    break;
    
    case 'conciliar' :
        
        $rowSaida = $objSaida->getSaidaByID($_POST['saida']);
        
        if(count($_POST['adiantamento']) > 0) { 
            foreach ($_POST['adiantamento'] as $key => $id_saida_adiantamento) { 
        
                /** * Colocando status 0 para a saida da nota */
                mysql_query("UPDATE saida SET status = 0 WHERE id_saida = '{$_POST['saida']}' LIMIT 1;");
                
                $rowAdiantamento = $objSaida->getSaidaByID($id_saida_adiantamento);

                /** * Copia do adiantamentopara a tabela saida_conciliacao */
                mysql_query('INSERT INTO saida_conciliacao (' . implode(', ', array_keys($rowAdiantamento)) . ') VALUES ("' . implode('","', array_values($rowAdiantamento)) . '")');

                /** * Colocando as informaçã */
                unset($rowSaida['id_saida'], $rowSaida['id_banco'], $rowSaida['status'], $rowSaida['data_pg'], $rowSaida['hora_pg'], $rowSaida['valor'], $rowSaida['valor_bruto']);
                foreach ($rowSaida as $key => $value) {
                    $array[] = "$key = '" . addslashes($value) . "'";
                } 
                $array[] = "id_user_conciliar = '{$usuario['id_funcionario']}'";
                $array[] = "data_conciliar = NOW()";
                
                mysql_query("UPDATE saida SET " . implode(', ', $array) . " WHERE id_saida = '{$id_saida_adiantamento}' LIMIT 1;");

                /** * Pegar os anexos da saida */
                $sqlAnexos = "SELECT * FROM saida_files WHERE id_saida = '{$_POST['saida']}'";
                $qryAnexos = mysql_query($sqlAnexos);
                while($rowAnexos = mysql_fetch_assoc($qryAnexos)){
                    $caminho[1] = "../../comprovantes/";
                    $caminho[2] = "../comprovantes/saida/";
                    $nome_arquivo = "{$rowAnexos['id_saida_file']}.{$rowAnexos['id_saida']}{$rowAnexos['tipo_saida_file']}";

                    $criar = 0;
                    if(file_exists("{$caminho[1]}{$nome_arquivo}")){
                        $criar = 1;
                    } else if(file_exists("{$caminho[2]}{$nome_arquivo}")){
                        $criar = 2;
                    }
                    if($criar > 0){
                        mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('{$value}', '{$rowAnexos['tipo_saida_file']}')");
                        $idNovoAnexo = mysql_insert_id();
                        copy("{$caminho[$criar]}{$nome_arquivo}", "{$caminho[$criar]}{$idNovoAnexo}.{$value}{$rowAnexos['tipo_saida_file']}");
                    }
                }
            }
            echo json_encode(['status' => 1, 'msg' => 'Saida conciliada com sucesso!']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Selecione pelo menos uma saida!']);
        }
        
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}