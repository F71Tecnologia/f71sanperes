<?php

include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/SaidaClass.php");

$usuario = carregaUsuario();

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "agrupar"){
    $re = array('status'=>"1");
    $idsSaida = array();

    foreach($_REQUEST['id'] as $saida){
        //echo $saida['value']."<br/>";
        $idsSaida[] = $saida['value'];
    }

    //VALIDAÇÃO, POIS SÓ PODE GERAR AGRUPAMENTO SE TODAS AS SAÍDAS TIVEREM O MESMO TIPO
    $qrValidacaoTipo = "SELECT tipo FROM saida WHERE id_saida IN (".implode(",",$idsSaida).") GROUP BY tipo";
    $reValidacaoTipo = mysql_query($qrValidacaoTipo);
    $total = mysql_num_rows($reValidacaoTipo);
    if($total > 1){
        while($rowValidacaoTipo = mysql_fetch_assoc($reValidacaoTipo)){
            $tipo = (in_array($rowValidacaoTipo['tipo'], [346, 351, 398])) ? 346 : $rowValidacaoTipo['tipo'];
            $arrayTipo[$tipo] = $tipo;
        }
        if(count($arrayTipo) > 1) {
            $re['status'] = "0";
            $re['msg'] = utf8_encode("Só pode gerar agrupamentos com saídas do mesmo tipo.");
            echo json_encode($re);exit;
        }
    }

    //DADOS DAS SAIDAS
    $qrDadosSaida = "SELECT *,CAST( REPLACE(valor, ',', '.') as decimal(13,2)) as valdecimal FROM saida WHERE id_saida IN (".implode(",",$idsSaida).")";
    $reDadosSaida = mysql_query($qrDadosSaida);
    $saidaMae = null;
    $saidaAssoc = null;
    $valorTotal = 0;
    $nDocumento = null;
    while($row = mysql_fetch_assoc($reDadosSaida)){
        if($saidaMae === null){
            $saidaMae = $row;
        }
        $saidaAssoc[$row['id_saida']]['id_saida'] = $row['id_saida'];
        $saidaAssoc[$row['id_saida']]['valor'] = $row['valdecimal'];
        $saidaAssoc[$row['id_saida']]['criado_em'] = date('Y-m-d H:i:s');
        $saidaAssoc[$row['id_saida']]['criado_por'] = $usuario['id_funcionario'];

        $nDocumento += $row['n_documento'].",";
        $valorTotal += $row['valdecimal'];
    }
    $nDocumento = substr($nDocumento, 0, -1);


    //ORGANIZANDO DADOS PARA A NOVA SAÍDA
    unset($saidaMae['id_saida'],$saidaMae['valdecimal']);
//    $saidaMae['valor']      = str_replace(".",",",$valorTotal);
    $saidaMae['valor']      = $valorTotal;
    $saidaMae['id_user']    = $usuario['id_funcionario'];
    $saidaMae['especifica'] = addslashes($_REQUEST['desc'])." Documentos: {$nDocumento}";
    $saidaMae['nome']       = addslashes($_REQUEST['desc']);
    $saidaMae['data_proc']  = date('Y-m-d H:i:s');
    $saidaMae['data_vencimento'] = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['vencimento'])));
    $saidaMae['status']     = 1;
    $saidaMae['agrupada']   = 2;

    //CRIANDO A NOVA SAIDA
    $keys = array_keys($saidaMae);
    $idMae = sqlInsert("saida",$keys,$saidaMae);
    $UltimoAgrupa= mysql_insert_id();
    //$idMae = 99;

    //INSERINDO TUDO NA TABELA DE ASSOC
    $insetInto = "INSERT INTO saida_agrupamento_assoc (id_saida_pai, id_saida, valor, criado_em, criado_por) VALUES ";
    $in = null;
    foreach($saidaAssoc as $k => $saidaGroup){
        $v = str_replace(",",".",$saidaGroup['valor']);
        $in .= "('{$idMae}', '{$saidaGroup['id_saida']}', '{$v}', '{$saidaGroup['criado_em']}', '{$saidaGroup['criado_por']}'),";
    }
    $in2 = substr($in, 0, -1);

    mysql_query($insetInto.$in2);
    
    print $UltimoAgrupa;

    //ATUALIZANDO AS SAIDAS AGRUPADAS
    $update = "UPDATE saida SET status='0', agrupada='1' WHERE id_saida IN (".implode(",",$idsSaida).")";
    mysql_query($update);

    //echo json_encode($re);
    exit;
    
} else if(isset($_REQUEST['method']) && $_REQUEST['method'] == "desagrupar"){
    
    

}