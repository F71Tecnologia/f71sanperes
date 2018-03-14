<?php

include('../conn.php');
include('../funcoes.php');
include('../classes/abreviacao.php');
include('../classes/ICalculosDatasClass.php');
include('../classes/CalculosDatasClass.php');
include('../classes/EventoClass.php');
include('../wfunction.php');

$usuario = carregaUsuario(); // carrega dados do usuário
$eventos = new Eventos();

$dados_evento['cod_status'] = isset($_REQUEST['ocorrencia']) ? $_REQUEST['ocorrencia'] : NULL;
$dados_evento['dias'] = isset($_REQUEST['qt_dias']) ? $_REQUEST['qt_dias'] : NULL;
$dados_evento['data'] = isset($_REQUEST['dataAlt']) ? implode('-',array_reverse(explode('/',$_REQUEST['dataAlt']))) : NULL;
$dados_evento['data_retorno'] = isset($_REQUEST['dataRet']) ? implode('-',array_reverse(explode('/',$_REQUEST['dataRet']))) : NULL;
$dados_evento['obs'] = isset($_REQUEST['obs']) ? $_REQUEST['obs'] : NULL;
$id_evento = isset($_REQUEST['id_evento']) ? $_REQUEST['id_evento'] : NULL;


$calculosDatas = new CalculosDatas();
$dif = $calculosDatas->diferencaDias($dados_evento['data'], $dados_evento['data_retorno'], TRUE);

if(isset($_POST['acao']) && ($_POST['acao']=='calcular_datas')){
    echo $dif;
    exit();
}
if(isset($_POST['acao']) && ($_POST['acao']=='atualizar_evento')){
    if ($dados_evento['dias'] != $dif) {
        echo json_encode(array('status'=>false,'resp'=> 'Favor verificar as data informadas'));
        exit();
    }else{
        $update = $eventos->editarEvento($id_evento,$dados_evento);      
        echo json_encode(array('status'=>true,'resp'=> 'Dados gravados com sucesso.'));
        exit();
    }
}
