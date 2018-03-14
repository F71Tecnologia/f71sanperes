<?php

include "../../conn.php";
include "../../classes/uploadfile.php";
require("../../wfunction.php");
require("../../classes/LogClass.php");
require("../../classes/ViagemClass.php");
require("../../classes/CaixinhaClass.php");
require("../../classes/global.php");

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();
$log = new Log();
$objViagem = new ViagemClass();
$objGlobal = new GlobalClass();

$objCaixinha = new CaixinhaClass();
$arrayItens = $objCaixinha->getItensDespesas();

//PREPARANDO TODAS AS REGIÕES QUE A PESSOA TEM ACESSO
$regioesLogado = getRegioes();
$idsRegioes = null;
foreach($regioesLogado as $k => $val){
    $idsRegioes[] = $k;
}
array_splice($idsRegioes, 0, 1); //REMOVENDO O '-1'


switch ($action) {
    case 'ver' :
        
        $id_viagem = $_REQUEST['id'];
        //$qry = mysql_query("SELECT *,date_format(data, '%d/%m/%Y') as data,  FROM viagem WHERE id_viagem = '$id_viagem'");
        $row = $objViagem->getViagemById($id_viagem);
        $arraySaidas = $objViagem->getSaidasByIdViagem($row['id_viagem']);
        /**
        * LISTA DE DESPESAS 
        */
        $sqlItem = "
        SELECT A.*, B.nome 
        FROM viagem_itens_assoc A 
        LEFT JOIN itens_despesas B ON (B.id = A.id_item)
        WHERE id_viagem = '{$id_viagem}'";
        $qryItem = mysql_query($sqlItem) or die(mysql_error());
       
        $codigo = sprintf("%05d",$row['0']);
        
        $obs = "Banco: {$row['banco']} AG: {$row['agencia']} CC: {$row['conta']} Favorecido: {$row['favorecido']} cpf: {$row['cpf']}";

        $valor = number_format($row['valor'],2,",","."); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style>
            ol { 
                -webkit-padding-start: 20px;
                -webkit-margin-after: 0em;
            }
        </style>
        <table class="table table-condensed table-bordered text-sm">
            <tr>
                <td class="text-bold">Nome:</td>
                <td colspan="3"><?=utf8_encode($row['nome'])?></td>
            </tr>
            <tr>
                <td class="text-bold">Valor:</td>
                <td><?=$valor?></td>
                <td class="text-bold">Data:</td>
                <td><?=$row['data']?></td>
            </tr>
            <tr>
                <td class="text-bold">Destino:</td>
                <td><?=utf8_encode($row['destino'])?></td>
                <td class="text-bold">Per&iacute;odo:</td>
                <td><?="{$row['data_ini']} at&eacute; {$row['data_fim']}"?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-bold">Trajeto:</td>
            </tr>
            <tr>
                <td colspan="4"><?=utf8_encode($row['trajeto'])?></td>
            </tr>
            <tr>
                <td colspan="4" class="text-bold">Motivo da Viagem:</td>
            </tr>
            <tr>
                <td colspan="4"><?=utf8_encode($row['descricao'])?></td>
            </tr>
            <?php if(mysql_num_rows($qryItem) > 0) { ?>
            <tr>
                <td colspan="4">
                    <ol>
                    <?php while($rowItem = mysql_fetch_assoc($qryItem)) { ?>
                        <li><?php echo utf8_encode($rowItem['nome'])." (x{$rowItem['qtd']}) = " . number_format($rowItem['valor'], 2, ',', '.') ?></li>
                    <?php } ?>
                    </ol>
                </td>
            </tr>
            <?php } ?>
            
            <?php if($row['id_acerto']) { ?>
            <tr>
                <td colspan="4" class="text-bold">Acerto:</td>
            </tr>
                <?php 
//                $itensAcerto = $objViagem->getItensAcerto($row['id_acerto']);
//                print_array($itensAcerto);
                if(count($itensAcerto) > 0) { 
                $anexoViagem = $objViagem->getAnexosAcertoByIdViagem($row['id_viagem']);// print_array($anexoViagem); ?>
                <tr>
                    <td colspan="4">
                        <ol>
                        <?php foreach ($itensAcerto as $key => $value) { ?>
                            <li>
                                <?php echo "{$value['nome']} = " . number_format($value['valor'], 2, ',', '.') ?>
                                <?php foreach ($anexoViagem[$key] as $keyA => $valueA) { ?><a class="" href="/intranet/finan/viagem/anexo/<?php echo "{$row['id_viagem']}/{$valueA['id']}.{$valueA['extensao']}" ?>" target="_blank"><i class="btn btn-xs btn-default fa fa-print pointer"></i></a><?php } ?>
                            </li>
                        <?php } ?>
                        </ol>
                    </td>
                </tr>
                <?php } ?>
            <?php if(count($arraySaidas) > 0) { ?>
                <tr>
                    <th>ID</th>
                    <th>NOME</th>
                    <th>N&ordm; DOC</th>
                    <th>VALOR</th>
                </tr>
                <?php foreach ($arraySaidas as $key => $value) { $valorTotalItens += $value['valor'] ?>
                <tr>
                    <td><?php echo $value['id_saida'] ?></td>
                    <td><?php echo utf8_encode($value['nome']) ?></td>
                    <td><?php echo $value['n_documento'] ?></td>
                    <td><?php echo number_format($value['valor'], 2, ',', '.') ?></td>
                </tr>
                <?php } ?>
            <?php } ?>
            <tr>
                <td colspan="2" class="text-bold">Total Acerto:</td>
                <td colspan="2"><?php echo number_format($row['totalAcerto'], 2, ',', '.') ?></td>
            </tr>
            <tr>
                <td class="text-bold">Pagar:</td>
                <td><?php echo number_format($row['valor_pagar'], 2, ',', '.') ?></td>
                <td class="text-bold">Devolver:</td>
                <td><?php echo number_format($row['valor_devolver'], 2, ',', '.') ?></td>
            </tr>
            <?php } ?>
            
            <tr>
                <td colspan="4" class="text-bold">Dados para o Dep&oacute;sito</td>
            </tr>
            <tr>
                <td class="text-bold">Banco:</td>
                <td colspan="3"><?=utf8_encode($row['banco'])?></td>
            </tr>
            <tr>
                <td class="text-bold">Agencia:</td>
                <td><?=$row['agencia']?></td>
                <td class="text-bold">Conta:</td>
                <td><?=$row['conta']?></td>
            </tr>
            <tr>
                <td class="text-bold">Favorecido:</td>
                <td><?=$row['favorecido']?></td>
                <td class="text-bold">CPF:</td>
                <td><?=$row['cpf']?></td>
            </tr>
            <?php if(in_array($row['status'], [1,3])) { ?>
            <tr>
                <td colspan="2">
                    <?php $arrayBancosViagem = $objGlobal->carregaBancosByMaster($usuario['id_master']); $arrayBancosViagem['caixinha'] = 'CAIXINHA'; unset($arrayBancosViagem[-1]); ?>
                    <?php echo montaSelect($arrayBancosViagem, 3, "class='form-control input-sm' name='id_banco_viagem' id='id_banco_viagem'") ?>
                </td>
                <td colspan="2">
                    <input type="text" class="form-control input-sm data" id="data_pag_viagem" id="data_pag_viagem" value="<?php echo date('d/m/Y') ?>">
                </td>
            </tr>
            <?php } ?>
            <tr>
                <?php if($row['status'] == 1) { ?>
                    <td colspan="2" class="text-center"><input class="btn btn-success btn-xs AprovarViagem" type="button" value="Aprovar" data-key="<?=$id_viagem?>"></td>
                    <td colspan="2" class="text-center"><input class="btn btn-danger btn-xs RecusarViagem" type="button" value="Recusar" data-key="<?=$id_viagem?>"></td>
                <?php } else if($row['status'] == 2) { ?>
                    <td colspan="4" class="text-center"><input class="btn btn-warning btn-xs acertoViagem" type="button" value="Realizar Acerto" data-key="<?=$id_viagem?>"></td>
                <?php } else if($row['status'] == 3) { ?>
                    <td colspan="2" class="text-center"><input class="btn btn-success btn-xs aprovarAcerto" type="button" value="Aprovar Acerto" data-key="<?=$id_viagem?>"></td>
                    <td colspan="2" class="text-center"><input class="btn btn-danger btn-xs recusarAcerto" type="button" value="Recusar Acerto" data-key="<?=$id_viagem?>"></td>
                <?php } ?>
            </tr>
        </table>
        <script>
        $(function(){
            $('#data_credito').datepicker();
        });
        </script><?php
    break;
    
    case 'aprovarAcerto':
        $valida = 0;
	$row = $objViagem->getViagemById($_REQUEST['id']); 
        $objCaixinha->setIdProjeto($row['id_projeto']);
        $saldo = $objCaixinha->getSaldoCaixinhasByMes();
        if($row['valor_pagar'] > 0) {
            if((($saldo - $row['valor_pagar']) > 0 && $_REQUEST['id_banco'] == 'caixinha') || $_REQUEST['id_banco'] != 'caixinha'){
                $valida = 1;
                $tipo = 1;
                $valor = $row['valor_pagar'];
            }
        } else if($row['valor_devolver'] > 0) {
            $valida = 1;
            $tipo = 2;
            $valor = $row['valor_devolver'];
        } else if($row['valor'] == $row['totalAcerto']) {
            $valida = 1;
            $valor = 0;
        } else if(!$row['id_acerto']) {
            $array = ['status' => 0, 'msg' => "Viagem sem Acerto!"];
            echo json_encode($array);exit;
        }
        if(in_array($_COOKIE['debug'], [666,667])){
            if($_COOKIE['debug'] == 667){
                echo "A"; 
                print_array($row);
            }
            print_array("B".$valida);
            print_array("C".$tipo);
            print_array("D".$valor);
            exit;
        }
        if($valida) {
            $qryU = mysql_query("UPDATE viagem SET status = '4' WHERE id_viagem = '{$_REQUEST['id']}'")or die(mysql_error());
            if($valor > 0.00) {
                if($qryU){ 
                    if($_REQUEST['id_banco'] == 'caixinha'){
                        $objCaixinha->setData(date('Y-m-d'));
                        $objCaixinha->setTipo($tipo);
                        $objCaixinha->setDescricao("ACERTO DE VIAGEM: {$_REQUEST['id']}");
                        $objCaixinha->setSaldo($valor);
                        $objCaixinha->setDataCad(date('Y-m-d'));
                        $objCaixinha->setUserCad($usuario['id_funcionario']);
                        $objCaixinha->setStatus(1);
                        $objCaixinha->insert();
                    } else {
                        $sqlBanco = "SELECT * FROM bancos WHERE id_banco = '{$_REQUEST['id_banco']}' LIMIT 1;";
                        $rowBanco = mysql_fetch_assoc(mysql_query($sqlBanco));
                        
                        if($tipo == 1) {
                            $data_vencimento = explode('/', $_REQUEST['data_vencimento']);
                            $arraySaida['data_vencimento'] = "{$data_vencimento[2]}-{$data_vencimento[1]}-{$data_vencimento[0]}";
                            $arraySaida['mes_competencia'] = $data_vencimento[1];
                            $arraySaida['ano_competencia'] = $data_vencimento[2];
                            $arraySaida['id_projeto'] = $row['id_projeto'];
                            $arraySaida['id_regiao'] = $row['id_regiao'];
                            $arraySaida['id_banco'] = $_REQUEST['id_banco'];
                            $arraySaida['data_proc'] = date('Y-m-d H:i:s');
                            $arraySaida['id_user'] = $usuario['id_funcionario'];
                            $arraySaida['valor'] = $valor;
                            $arraySaida['valor_bruto'] = $valor;
                            $arraySaida['tipo'] = 433;
                            $arraySaida['entradaesaida_subgrupo_id'] = 22;
                            $arraySaida['status'] = 1;
                            $arraySaida['id_viagem'] = $_REQUEST['id'];
                            $arraySaida['n_documento'] = $_REQUEST['id'];
                            $arraySaida['comprovante'] = 2;
                            $arraySaida['nome'] = "{$row['nome']} - ACERTO DE VIAGEM";
                            $arraySaida['especifica'] = "{$row['nome']}({$row['cpf']}) - ACERTO DE VIAGEM - BANCO: {$row['banco']} | AG: {$row['agencia']} | CONTA: {$row['conta']}";
                            $arraySaida['saldo_anterior'] = str_replace(',', '.', $rowBanco['saldo']);

                            $keys = implode(', ', array_keys($arraySaida));
                            $values = implode("' , '", $arraySaida);
                            $insert = "INSERT INTO saida ($keys) VALUES ('$values');";
                            mysql_query($insert) or die("<pre>ERRO CADASTRO DE saida: <br>" . $insert . "<br>" . mysql_error() . '</pre>');
                        } else {
                            $arrayEntrada['data_vencimento'] = implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento'])));
//                            $arrayEntrada['mes_competencia'] = date('m');
//                            $arrayEntrada['ano_competencia'] = date('Y');
                            $arrayEntrada['id_projeto'] = $row['id_projeto'];
                            $arrayEntrada['id_regiao'] = $row['id_regiao'];
                            $arrayEntrada['id_banco'] = $_REQUEST['id_banco'];
                            $arrayEntrada['data_proc'] = date('Y-m-d H:i:s');
                            $arrayEntrada['id_user'] = $usuario['id_funcionario'];
                            $arrayEntrada['valor'] = $valor;
                            $arrayEntrada['numero_doc'] = $_REQUEST['id'];
//                            $arrayEntrada['valor_bruto'] = $valor;
                            $arrayEntrada['tipo'] = 129;
//                            $arrayEntrada['entradaesaida_subgrupo_id'] = 106;
                            $arrayEntrada['status'] = 1;
//                            $arrayEntrada['comprovante'] = 2;
                            $arrayEntrada['nome'] = "ACERTO DE VIAGEM {$row['nome']} - Outras Despesas c/ Viagem";
                            $arrayEntrada['especifica'] = "ACERTO DE VIAGEM {$row['nome']}({$row['cpf']}) - Outras Despesas c/ Viagem - BANCO: {$row['banco']} | AG: {$row['agencia']} | CONTA: {$row['conta']}";
                            $arrayEntrada['saldo_anterior'] = str_replace(',', '.', $rowBanco['saldo']);

                            $keys = implode(', ', array_keys($arrayEntrada));
                            $values = implode("' , '", $arrayEntrada);
                            $insert = "INSERT INTO entrada ($keys) VALUES ('$values');";
                            mysql_query($insert) or die("<pre>ERRO CADASTRO DE entrada: <br>" . $insert . "<br>" . mysql_error() . '</pre>');
                        }
                    }
                    
                    $log->gravaLog('Aprovar Acerto de Viagem', 'Acerto de Viagem '.$_REQUEST['id'].' aprovado');
                    $array = ['status' => 1, 'msg' => 'Acerto de Viagem aprovado'];
                }
            } else {
                $array = ['status' => 1, 'msg' => 'Acerto de Viagem aprovado'];
            }
        } else {
            $array = ['status' => 0, 'msg' => "Saldo Caixinha Insuficiente! Saldo Caixinha atual: R$ " . number_format($saldo, 2, ',', '.')];
        }
        echo json_encode($array);
    break;
    
    case 'recusar':
	$qry = mysql_query("UPDATE viagem SET status = '0' WHERE id_viagem = '{$_REQUEST['id']}'");
        if($qry){ 
            $log->gravaLog('Recusar Viagem', 'Viagem '.$_REQUEST['id'].' recusado');
            $array = ['status' => 1, 'msg' => "Viagem recusada com sucesso!"];
            echo json_encode($array);
        }
    break;
    
    case 'recusarAcerto':
	$qry = mysql_query("UPDATE viagem SET status = '5' WHERE id_viagem = '{$_REQUEST['id']}'");
        if($qry){ 
            $log->gravaLog('Recusar Acerto Viagem', 'Acerto de Viagem '.$_REQUEST['id'].' recusado');
            $array = ['status' => 1, 'msg' => "Acerto recusado com sucesso!"];
            echo json_encode($array);
        }
    break;
    
//    case 'acerto':
//	$qry = mysql_query("UPDATE viagem SET status = '3' WHERE id_viagem = '{$_REQUEST['id']}'");
//        if($qry){ 
//            $log->gravaLog('Retorno de Viagem', 'Viagem '.$_REQUEST['id'].' retornada');
//            echo "Viagem recusada com sucesso!";
//        }
//    break;
    
    case 'formAcerto':
	$row = $objViagem->getViagemById($_REQUEST['id']);
        $itens = $objViagem->getItensByIdViagem($_REQUEST['id']);
        $anexos = $objViagem->getAnexosByIdViagem($row['id_viagem'], [2]);
        $arraySaidas = $objViagem->getSaidasByIdViagem($row['id_viagem']);
//        print_array($anexos); ?>
        <form method="post" action="actions/action_viagem.php" id="formAcerto">
            <div class="panel panel-default panel-itens">
                <div class="panel-heading">SA&Iacute;DAS DA VIAGEM</div>
                <div class="panel-body">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOME</th>
                                <th>N&ordm; DOC</th>
                                <th>VALOR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arraySaidas as $key => $value) { $valorTotalItens += $value['valor'] ?>
                            <tr>
                                <td><?php echo $value['id_saida'] ?></td>
                                <td><?php echo utf8_encode($value['nome']) ?></td>
                                <td><?php echo $value['n_documento'] ?></td>
                                <td><?php echo number_format($value['valor'], 2, ',', '.') ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-default no-margin-b">
                <div class="panel-footer">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <label class="control-label">Valor Solicitado</label>
                            <input type="hidden" name="id_viagem" id="id_viagem" value="<?php echo $row['id_viagem'] ?>">
                            <input type="hidden" id="tipo_anexo" value="2">
                            <input type="text" class="form-control input-sm input-sm valores" readonly="true" id="valorSolicitado" name="valorSolicitado"  value="<?php echo number_format($row['valor'], 2, ',', '.') ?>">
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Valor Total</label>
                            <input type="text" class="form-control input-sm input-sm valores" readonly="true" id="valor" name="valor"  value="<?php echo number_format($valorTotalItens, 2, ',', '.') ?>">
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Devolver</label>
                            <input type="text" class="form-control input-sm input-sm valores" readonly="true" id="devolver" name="devolver" value="<?php echo number_format((($row['valor'] - $valorTotalItens) > 0) ? abs($row['valor'] - $valorTotalItens) : 0, 2, ',', '.') ?>">
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Pagar</label>
                            <input type="text" class="form-control input-sm input-sm valores" readonly="true" id="pagar" name="pagar" value="<?php echo number_format((($row['valor'] - $valorTotalItens) < 0) ? abs($row['valor'] - $valorTotalItens) : 0, 2, ',', '.') ?>">
                        </div>
                        <div class="clearfix"><button type="submit" name="action" id="cadAcerto" value="cadAcerto" class="hide"></button></div>
                    </div>
                </div>
            </div>
        </form>
    <?php     
    break;
    
    case 'cadAcerto':
//        print_array($_POST);exit;
        $total = str_replace(',', '.', str_replace('.', '', $_POST['valor']));
        $devolver = str_replace(',', '.', str_replace('.', '', $_POST['devolver']));
        $pagar = str_replace(',', '.', str_replace('.', '', $_POST['pagar']));
        
        $insAcerto = "INSERT INTO viagem_acerto (`id_viagem`, `total`, `valor_pagar`, `valor_devolver`) VALUES ('{$_POST['id_viagem']}', '{$total}', '{$pagar}', '{$devolver}');";
        if(in_array($_COOKIE['debug'], [666,667])){
            if($_COOKIE['debug'] == 667){
                print_array("INSERT: ".$insAcerto);
            }
            print_array("TOTAL: ".$total);
            print_array("DEVOLVER: ".$devolver);
            print_array("PAGER: ".$pagar);
            exit;
        }
        mysql_query($insAcerto) or die(mysql_error());
        $id_acerto = mysql_insert_id();
//        print_array($_POST['item']);exit;
//        foreach ($_POST['item'] as $value) {
//            $valor = str_replace(',', '.', str_replace('.', '', $value['valor']));
//            $insAcertoItem = "INSERT INTO viagem_acerto_itens (`id_viagem_acerto`, `id_item`, `valor`, `nota_fiscal`) VALUES ('{$id_acerto}', '{$value['id_item']}', '{$valor}', '{$value['nota_fiscal']}');";
//            mysql_query($insAcertoItem);
//            $id_acerto_item = mysql_insert_id();
//            
//            $up = "UPDATE viagem_anexos SET id_acerto_item = '{$id_acerto_item}' WHERE token = '{$value['token']}'";
//            mysql_query($up);
//        }
        
        $upViagem = "UPDATE viagem SET status = '3' WHERE id_viagem = '{$_POST['id_viagem']}' LIMIT 1;";
        mysql_query($upViagem);
        
        header("Location: /intranet/finan");
    break;
    
//    case 'retornar':
//	$qry = mysql_query("UPDATE viagem SET status = '3' WHERE id_viagem = '{$_REQUEST['id']}'");
//        if($qry){ 
//            $log->gravaLog('Retorno de viagem', "Retorno de viagem: {$_REQUEST['id']}");
//            echo "Retorno de viagem";
//        }
//    break;
    
    case 'aprovar':
        
        $row = $objViagem->getViagemById($_REQUEST['id']);        
        $objCaixinha->setIdProjeto($row['id_projeto']);
        $saldo = $objCaixinha->getSaldoCaixinhasByMes();
//        echo ($saldo - $row['valor']); exit;
//        if(($saldo - $row['valor']) > 0){
        if((($saldo - $row['valor']) > 0 && $_REQUEST['id_banco'] == 'caixinha') || $_REQUEST['id_banco'] != 'caixinha'){
            $qryU = mysql_query("UPDATE viagem SET status = '2' WHERE id_viagem = '{$_REQUEST['id']}'");
            if($qryU){ 
                if($_REQUEST['id_banco'] == 'caixinha') {
    //                $objCaixinha->setData($row['data']);
                    $objCaixinha->setData(date('Y-m-d'));
                    $objCaixinha->setTipo(1);
                    $objCaixinha->setDescricao("SOLICITAÇÃO DE VIAGEM: {$_REQUEST['id']}");
                    $objCaixinha->setSaldo($row['valor']);
                    $objCaixinha->setDataCad(date('Y-m-d'));
                    $objCaixinha->setUserCad($usuario['id_funcionario']);
                    $objCaixinha->setStatus(1);
        //            $objCaixinha->setIdItem($_REQUEST['id_item']);
                    $objCaixinha->insert();
                } else {
                    $sqlBanco = "SELECT * FROM bancos WHERE id_banco = '{$_REQUEST['id_banco']}' LIMIT 1;";
                    $rowBanco = mysql_fetch_assoc(mysql_query($sqlBanco));
                    
                    $data_vencimento = explode('/', $_REQUEST['data_vencimento']);
                    $arraySaida['data_vencimento'] = "{$data_vencimento[2]}-{$data_vencimento[1]}-{$data_vencimento[0]}";
                    $arraySaida['mes_competencia'] = $data_vencimento[1];
                    $arraySaida['ano_competencia'] = $data_vencimento[2];
                    $arraySaida['id_projeto'] = $row['id_projeto'];
                    $arraySaida['id_regiao'] = $row['id_regiao'];
                    $arraySaida['id_banco'] = $_REQUEST['id_banco'];
                    $arraySaida['data_proc'] = date('Y-m-d H:i:s');
                    $arraySaida['id_user'] = $usuario['id_funcionario'];
                    $arraySaida['valor'] = $row['valor'];
                    $arraySaida['valor_bruto'] = $row['valor'];
                    $arraySaida['tipo'] = 432;
                    $arraySaida['entradaesaida_subgrupo_id'] = 22;
                    $arraySaida['status'] = 2;
                    $arraySaida['comprovante'] = 2;
                    $arraySaida['id_viagem'] = $_REQUEST['id'];
                    $arraySaida['n_documento'] = $_REQUEST['id'];
                    $arraySaida['nome'] = "{$row['nome']} - DESPESAS COM VIAGEM";
                    $arraySaida['especifica'] = "{$row['nome']}({$row['cpf']}) - DESPESAS COM VIAGEM - BANCO: {$row['banco']} | AG: {$row['agencia']} | CONTA: {$row['conta']}";
                    $arraySaida['saldo_anterior'] = str_replace(',', '.', $rowBanco['saldo']);

                    $keys = implode(', ', array_keys($arraySaida));
                    $values = implode("' , '", $arraySaida);
                    $insert = "INSERT INTO saida ($keys) VALUES ('$values');";
                    mysql_query($insert) or die("<pre>ERRO CADASTRO DE saida: <br>" . $insert . "<br>" . mysql_error() . '</pre>');
                }
                $log->gravaLog('Aprovar Viagem', 'Viagem '.$_REQUEST['id'].' aprovado');
                $array = ['status' => 1, 'msg' => 'Viagem Aprovada'];
//                echo "Viagem aprovada com sucesso!";
            }
        } else {
            $array = ['status' => 0, 'msg' => "Saldo Caixinha Insuficiente! Saldo Caixinha atual: R$ " . number_format($saldo, 2, ',', '.')];
        }
        echo json_encode($array);
    break;
    
    case 'load_bancos':
        
	$projeto = $_REQUEST['projeto'];
        $banco = $_REQUEST['banco'];
	
        $result_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto IN($projeto) AND status_reg = 1");
	while($row_banco = mysql_fetch_array($result_banco)){
            if($banco == $row_banco['id_banco']){
                echo "<option value='{$row_banco['id_banco']}' SELECTED >{$row_banco['id_banco']} - ".utf8_decode($row_banco['nome'])." - {$row_banco['agencia']} / {$row_banco['conta']}</option>";
            } else {
                echo "<option value='{$row_banco['id_banco']}'>{$row_banco['id_banco']} - ".utf8_decode($row_banco['nome'])." - {$row_banco['agencia']} / {$row_banco['conta']}</option>";
            }
	}
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}