<?php

include "../../../conn.php";
require("../../../funcoes.php");
require("../../../wfunction.php");
include("../../../classes/LogClass.php");
include("../../../classes/uploadfile.php");
require("../../../classes/ObrigacoesClass.php");
require("../../../classes/FuncionarioClass.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();

$log = new Log();
$objObrigacoes = new ObrigacoesClass();
$objFuncionario = new FuncionarioClass();
$id_permitidos = array(5, 9, 24, 87, 204, 255, 257);
//Atualizar campo nas obrigações
//UPDATE obrigacoes_oscip A SET A.id_tipo_oscip = (SELECT tipo_id FROM tipo_doc_oscip WHERE tipo_nome = A.tipo_oscip)
switch ($action) {
    case 'ver_anexos' :
        $id_obrigacao = $_REQUEST['id'];?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <?php $countFiles = 0;
        $dadosObrigacoes = $objObrigacoes->getObrigacoesAnexos($id_obrigacao,null,"extensao, tipo_anexo, id_anexo");
        foreach($dadosObrigacoes AS $rowAnexos){ 
            $caminho = (file_exists("../obrigacoes/anexos_oscip/".$rowAnexos['id_anexo'].'.'.$rowAnexos['extensao']))
                ? "anexos_oscip/".$rowAnexos['id_anexo'].'.'.$rowAnexos['extensao']
                : "../../../adm/adm_contratos/anexos_oscip/".$rowAnexos['id_anexo'].'.'.$rowAnexos['extensao'];
            
            if($rowAnexos['extensao'] == 'pdf' || $rowAnexos['extensao'] == 'PDF' && $auxTipo != 'pdf'){
                echo '<div class="col-xs-12 bg-danger text-sm margin_b5 '.$rowAnexos['id_anexo'].'"><h5>PDF</h5></div>';
                $auxTipo = 'pdf';
            } else if($rowAnexos['tipo_anexo'] == 1 && $auxTipo != 1){
                echo '<div class="col-xs-12 bg-danger text-sm margin_b5 '.$rowAnexos['id_anexo'].'"><h5>PUBLICAÇÕES</h5></div>';
                $auxTipo = 1;
            } else if($rowAnexos['tipo_anexo'] == 2 && $auxTipo != 2){
                echo '<div class="col-xs-12 bg-danger text-sm margin_b5 '.$rowAnexos['id_anexo'].'"><h5>DOCUMENTOS</h5></div>';
                $auxTipo = 2;
            } ?>
            <div class="col-xs-3 margin_b5 <?=$rowAnexos['id_anexo']?>">
                <div class="thumbnail">
                    <a href="<?=$caminho?>" target="_blank">
                        <img src="../../../imagens/icons/att-<?=str_replace('.','',$rowAnexos['extensao'])?>.png">
                    </a>
                </div>
            </div>
        
        <?php } ?>
        <div class="clear"></div><?php
    break;
    
    case 'get_resposta' :
        $tipo_obrigacao = $_REQUEST['id'];
        $dadosObrigacoes = $objObrigacoes->getObrigacoes("tipo_oscip = '$tipo_obrigacao'");
        echo '<option value="">Selecione</option>';
        foreach($dadosObrigacoes AS $rowResposta){ 
            echo '<option value="'.$rowResposta['id_oscip'].'">(COD: '.$rowResposta['id_oscip'].') '.$rowResposta['numero_oscip'].'</option>';
        } 
    break;
    
    case 'cadastrar_obrigacao' :
        $dados = $_REQUEST;
        unset($dados['action'],$dados['logado'],$dados['PHPSESSID'],$dados['timezone']);
        $tipoObrigacao = $objObrigacoes->getTipoObrigacoes(" tipo_id = {$dados['id_tipo_oscip']} ", null, 1);
        $tipoObrigacao = $tipoObrigacao[0];
        $dados['tipo_oscip'] = $tipoObrigacao['tipo_nome'];
        $dados['data_publicacao'] = implode('-', array_reverse(explode('/',$dados['data_publicacao'])));
        $dados['oscip_data_inicio'] = implode('-', array_reverse(explode('/',$dados['oscip_data_inicio'])));
        $dados['oscip_data_termino'] = implode('-', array_reverse(explode('/',$dados['oscip_data_termino'])));
        $dados['id_master'] = $usuario['id_master'];
        $dados['usuario'] = $usuario['id_funcionario'];
	$dados['data_usuario'] = date("Y-m-d");
	$dados['status'] = 1;
        
        $id_obrigacao = $objObrigacoes->cadastrarObrigacao($dados);
        $charset = mysql_set_charset('latin1');
        $log->gravaLog('Cadastro de Obrigação', 'Obrigação '.$id_obrigacao.' cadastrada');
        echo $id_obrigacao;
        //print_array($dados);
    break;
    
    case 'editar_obrigacao' :
        $dados = $_REQUEST;
        $id_obrigacao = $dados['id_oscip'];
        unset($dados['action'],$dados['id_oscip']);
        $tipoObrigacao = $objObrigacoes->getTipoObrigacoes(" tipo_id = {$dados['id_tipo_oscip']} ", null, 1);
        $tipoObrigacao = $tipoObrigacao[0];
        $dados['tipo_oscip'] = $tipoObrigacao['tipo_nome'];
        $dados['data_publicacao'] = implode('-', array_reverse(explode('/',$dados['data_publicacao'])));
        $dados['oscip_data_inicio'] = implode('-', array_reverse(explode('/',$dados['oscip_data_inicio'])));
        $dados['oscip_data_termino'] = implode('-', array_reverse(explode('/',$dados['oscip_data_termino'])));
        $dados['id_master'] = $usuario['id_master'];
        $dados['usuario_atualizacao'] = $usuario['id_funcionario'];
	$dados['data_atualizacao'] = date("Y-m-d");
        
        $objObrigacoes->editarObrigacao($dados, $id_obrigacao);
        $charset = mysql_set_charset('latin1');
        $log->gravaLog('Edição de Obrigação', 'Obrigação '.$id_obrigacao.' editada');
        echo $id_obrigacao;
        //print_array($dados);
    break;
    
    case 'renovar_obrigacao' :
        $dados = $_REQUEST;
        unset($dados['action'],$dados['id_oscip']);
        $tipoObrigacao = $objObrigacoes->getTipoObrigacoes(" tipo_id = {$dados['id_tipo_oscip']} ", null, 1);
        $tipoObrigacao = $tipoObrigacao[0];
        $dados['tipo_oscip'] = $tipoObrigacao['tipo_nome'];
        $dados['data_publicacao'] = implode('-', array_reverse(explode('/',$dados['data_publicacao'])));
        $dados['oscip_data_inicio'] = implode('-', array_reverse(explode('/',$dados['oscip_data_inicio'])));
        $dados['oscip_data_termino'] = implode('-', array_reverse(explode('/',$dados['oscip_data_termino'])));
        $dados['id_master'] = $usuario['id_master'];
        $dados['usuario_atualizacao'] = $usuario['id_funcionario'];
	$dados['data_atualizacao'] = date("Y-m-d");
        
        $id_obrigacao = $objObrigacoes->cadastrarObrigacao($dados);
        $charset = mysql_set_charset('latin1');
        $log->gravaLog('Renovação de Obrigação', 'Obrigação '.$id_obrigacao.' renovada');
        echo $id_obrigacao;
        //print_array($dados);
    break;
    
    case 'excluir_oscip' :
        $id_obrigacao = $_REQUEST['id'];
        $objObrigacoes->excluirObrigacao($id_obrigacao);
        $charset = mysql_set_charset('latin1');
        $log->gravaLog('Exclusão de Obrigação', 'Obrigação '.$id_obrigacao.' excluida');
        echo $id_obrigacao;
    break;
    
    case 'upload_anexo' : 
        //echo $_SERVER ['REQUEST_URI'];
        //print_r($_REQUEST);print_r($_FILES);exit;
        $id_obrigacao = $_REQUEST['id_obrigacao'];
        $tipo_anexo = $_REQUEST['tipo'];
        $diretorio = "../obrigacoes/anexos_oscip";

        $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
        $upload->arquivo($_FILES[file]);
        $upload->verificaFile();
        
        mysql_query("INSERT INTO obrigacoes_oscip_anexos (id_oscip, extensao, status, tipo_anexo) VALUES ('$id_obrigacao','$upload->extensao', '1','$tipo_anexo')") or die(mysql_error());
        $id = mysql_insert_id();

        $upload->NomeiaFile($id);
        $upload->Envia();
        
        $charset = mysql_set_charset('latin1');
        $log->gravaLog('Anexo Obrigação', 'Anexo '.$id.' inserido na Obrigação '.$id_obrigacao);
        echo $diretorio.'/'.$id.'.'.$upload->extensao; exit;
    break;
    
    case 'show_obrigacoes_inst' :
        $tipo_obrigacao = $_REQUEST['id'];
        //echo utf8_decode($tipo_obrigacao['tipo_nome']) .' == Ofícios Enviados';
        $oficio = ($tipo_obrigacao == 13 || $tipo_obrigacao == 14) ? TRUE : FALSE;
        $aux_qr_oscip = utf8_encode(" AND (
            (periodo = 'Período' AND (oscip_data_termino >= NOW())) OR 
            (periodo = 'Dias' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY) >= NOW())) OR 
            (periodo = 'Meses' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH) >= NOW())) OR 
            (periodo = 'Anos' AND (DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR) >= NOW())) OR (PERIODO = 'INDETERMINADO') OR (periodo = 'Indeterminado')
        ) ");
        $dadosObrigacoes = $objObrigacoes->getObrigacoes("id_tipo_oscip = '{$tipo_obrigacao}' $aux_qr_oscip", 'data_publicacao DESC');
        //print_array($dadosObrigacoes); exit;
        if(count($dadosObrigacoes) > 0){ ?>
            <table class="table table-condensed table-bordered table-hover text-sm valign-middle">
                <thead>
                    <tr class="bg-primary text-uppercase">
                        <?php if ($oficio) { ?>
                        <th class="">Cod</th>
                        <?php } ?>
                        <th class="">Documento</th>
                        <th class="text-center hidden-print">A&ccedil;&otilde;es</th>
                        <th class="text-center">Data Publica&ccedil;&atilde;o</th>
                        <?php if ($oficio) { ?>
                        <th class="text-center">N&ordm; Doc</th>
                        <?php } else { ?>
                        <th class="text-center">Validade</th>
                        <?php } ?>
                        <th class="">Descri&ccedil;&atilde;o</th>
                        <th class="text-center">Status</th>
                        <th class="">&Uacute;ltima Edi&ccedil;&atilde;o</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dadosObrigacoes as $row_obrigacao) { 
                    $ultima_edicao = 
                        ($row_obrigacao['data_atualizacao'] != '0000-00-00') 
                            ? 'Editado por: ' . $objFuncionario->getFuncionarioNome($row_obrigacao['usuario_atualizacao'],TRUE) . ' (' . implode('/', array_reverse(explode('-', $row_obrigacao['data_atualizacao']))).')' 
                            : 'Cadastrado por: ' . $objFuncionario->getFuncionarioNome($row_obrigacao['usuario'],TRUE) . ' (' . implode('/', array_reverse(explode('-', $row_obrigacao['data_usuario']))).')' ?>
                    <tr>
                        <?php if ($oficio) { ?>
                        <td class=""><?=$row_obrigacao['id_oscip']?></td>
                        <?php } ?>
                        <td class=""><?=utf8_encode($row_obrigacao['tipo_oscip'])?></td>
                        <td class="text-center hidden-print" width="117px">
                            <!--<button type="button" class="btn btn-xs btn-warning editar_oscip" data-toggle="tooltip" data-original-title="Editar Obriga&ccedil;&atilde;o" data-key="<?=$row_obrigacao['id_oscip']?>"><i class="fa fa-edit"></i></button>-->
                            <!--<a href="../edicao_oscip.php?m=<?= $link_master ?>&id=<?= $row_obrigacao['id_oscip']?>"><img src="../../imagens/editar_projeto.png"/></a>-->
                            <a href="../edicao_oscip.php?m=<?= $link_master ?>&id=<?= $row_obrigacao['id_oscip']?>" class="btn btn-warning btn-xs"><span class="fa fa-edit"></span></a>
                            <?php if (in_array($_COOKIE['logado'], $id_permitidos)) { ?>
                            <button type="button" class="btn btn-xs btn-danger excluir_oscip" data-toggle="tooltip" data-original-title="Excluir Obriga&ccedil;&atilde;o" data-key="<?=$row_obrigacao['id_oscip']?>"><i class="fa fa-trash-o"></i></button>
                            <?php } ?>
                            <?php if ($row_obrigacao['periodo'] != 'Indeterminado') { ?>
                            <button type="button" class="btn btn-xs btn-default renovar_oscip" data-toggle="tooltip" data-original-title="Renovar Obriga&ccedil;&atilde;o" data-key="<?=$row_obrigacao['id_oscip']?>"><i class="fa fa-history text-primary"></i></button>
                            <?php } ?>
                            <button type="button" class="btn btn-xs btn-info ver_anexos" data-toggle="tooltip" data-original-title="Anexos da Obriga&ccedil;&atilde;o" data-key="<?=$row_obrigacao['id_oscip']?>"><i class="fa fa-paperclip"></i></button>
                        </td>
                        <td class="text-center"><?=implode('/', array_reverse(explode('-', $row_obrigacao['data_publicacao'])))?></td>
                        <?php if ($oficio) { ?>
                        <td class="text-center"><?=$row_obrigacao['numero_oscip']?></td>
                        <?php } else { ?>
                        <td class="text-center"><?=$objObrigacoes->getValidadeObrigacao($row_obrigacao['id_oscip'])?></td>
                        <?php } ?>
                        <td class=""><?=utf8_encode($row_obrigacao['descricao'])?></td>
                        <td class="text-center">
                            <?=$objObrigacoes->getStatusObrigacao($row_obrigacao['periodo'], $row_obrigacao['data_publicacao'], $row_obrigacao['numero_periodo'], $row_obrigacao['oscip_data_termino'])?>
                            <?= ($row_obrigacao['resp_env_rec'] > 0) ? " Resposta de {$row_obrigacao['resp_env_rec']}" : ''; ?>
                        </td>
                        <td class=""><?=utf8_encode($ultima_edicao)?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-warning">Nenhuma Obriga&ccedil;&atilde;o Encontrada!</div>
        <?php }
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}