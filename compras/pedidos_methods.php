<?php

header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../classes/pedidosClass.php');
include('../../wfunction.php');

// MÉTODO PARA RETORNAR JSON COM OS ITENS DA TABELA nfeprodutos
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaItem') {
    $query = "SELECT xProd, id_prod FROM nfe_produtos WHERE id_prestador = '{$_REQUEST['prestador']}' AND status = 1";
//    echo $query; exit();
    $result = mysql_query($query);
//    echo mysql_num_rows($result);
    while ($row = mysql_fetch_assoc($result)) {
        $array['prods'][] = $row['id_prod'] . ' - ' . utf8_encode($row['xProd']);
        $array['id_prods'][] = $row['id_prod'];
    }
    echo json_encode($array);
    exit();
}

if (isset($_REQUEST['buscarprodutoS']) && $_REQUEST['buscarprodutoS'] == 'Visualizar Produtos') {
    $id_projeto1 = $_REQUEST['projeto1'];
    $id_regiao1 = $_REQUEST['regiao1'];
    $id_prestador1 = $_REQUEST['prestador1'];

    $qry = mysql_query("SELECT A.*, B.* FROM prestadorservico AS A
        LEFT JOIN nfe_produtos AS B ON (B.id_prestador = A.id_prestador)
        WHERE B.status = '1' AND A.id_regiao = '$id_regiao1' AND A.id_projeto = '$id_projeto1' AND A.id_prestador = '$id_prestador1' ORDER BY B.xProd ASC");
    
    $total = mysql_num_rows($qry);
    
    while ($row_produtos = mysql_fetch_assoc($qry)) {
        $retorno_produtos[] = array(
            'id_prod' => $row_produtos['id_prod'],
            'cProd' => $row_produtos['cProd'],
            'xProd' => utf8_encode($row_produtos['xProd']),
            'uCom' => $row_produtos['uCom'],
            'vUnCom' => $row_produtos['vUnCom'],
        );
    }
    echo (json_encode( array('itens' =>$retorno_produtos)));
    exit();
}

if (isset($_REQUEST['gerarpedido']) && $_REQUEST['gerarpedido'] == 'Gerar Pedido') {

    $dados = array ( 
        'id_projeto1'   => $_REQUEST['projeto1'],
        'id_regiao1'    => $_REQUEST['regiao1'],
        'id_prestador1' => $_REQUEST['prestador1'],
        'idProd'        => $_REQUEST['idProd'],
        'vUnCom'        => $_REQUEST['vUnCom'],
        'qtde'          => $_REQUEST['qtde'],
    );
    
    $sql_prestador = "SELECT A.id_prestador, B.c_razao, B.c_cnpj, B.c_endereco
                      FROM nfe_produtos AS A
                      LEFT JOIN prestadorservico AS B ON(B.id_prestador = A.id_prestador)
                      WHERE A.id_prestador = {$dados['id_prestador']} AND A.id_projeto = {$dados['id_projeto1']} AND A.id_regiao = {$dados['id_regiao1']} ";

    $dados_prestador = mysql_fetch_assoc($sql_prestador);
    $sql_produtos = "SELECT * FROM nfe_produtos WHERE id_prod = {$dados['idProd']}";
    $dados_produtos = mysql_fetch_assoc($sql_produtos);
    $result = mysql_query($query_updt);
    return ($result) ? array('status' => TRUE, 'id_produto' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar produto!');
    
    exit();
}

// SALVAR CADASTRO  PEDIDO -----------------------------------------------------
if (isset($_REQUEST['salvar-pedido'])) {
    $dados = array(
        'id_prod'=>$_REQUEST['id_prod'],
        'id_projeto' => $_REQUEST['id_projeto'],
        'id_prestador' => $_REQUEST['id_prestador'],
        'qtde_ped' => $_REQUEST['qtde_ped'],
    );

    $result = $pedido->salvarPedido ($dados, $_REQUEST['prestador']);

    if ($result['status']) { ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p>Pedido Cadastrado...</p>
        </div>
        <?php
    } else { ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
            <p><?= $result['msg'] ?></p>
        </div>
        <?php
    }
    exit();
}
    