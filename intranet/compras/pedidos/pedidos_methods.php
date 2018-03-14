<?php
error_reporting(E_ALL);
header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/pedidosClass.php');
include('../../wfunction.php');
include('../../classes/PHPExcel/PHPExcel.php');

$usuario = carregaUsuario();

$pedido = new pedidosClass();

// MÉTODO PARA RETORNAR JSON COM OS ITENS DA TABELA nfeprodutos
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaItem') {

    $query = "SELECT xProd, id_prod 
        FROM nfe_produtos AS a 
        INNER JOIN produto_fornecedor_assoc AS b ON a.id_prod = b.id_produto 
        WHERE b.id_fornecedor = '{$_REQUEST['prestador']}' AND status = 1";
    $result = mysql_query($query);

    while ($row = mysql_fetch_assoc($result)) {
        $array['prods'][] = $row['id_prod'] . ' - ' . utf8_encode($row['xProd']);
        $array['id_prods'][] = $row['id_prod'];
    }

    echo json_encode($array);
    exit();
}

if (isset($_REQUEST['buscarprods']) && $_REQUEST['buscarprods'] == 'Produtos') {

    if (empty($_REQUEST['regiao']) && empty($_REQUEST['projeto']) && empty($_REQUEST['fornecedor'])) {
        exit('Erro : Parâmetros insuficientes.');
    }

// REQUESTs do POST
    $id_projeto1 = $_REQUEST['projeto'];
    $id_regiao1 = $_REQUEST['regiao'];
    $id_fornecedor = $_REQUEST['id_prestador'];
    $filtra_tipo = $_REQUEST['filtra_tipo'];


// impostacao do excel -----------------------------------------------------
    if ($_REQUEST['importacao'] == 's' && isset($_FILES)) {
        $uploadfile = 'arquivo.' . substr(basename($_FILES['arquivo_excel']['name']), -3); // nome do arquivo excel salvo
        if (move_uploaded_file($_FILES['arquivo_excel']['tmp_name'], $uploadfile)) {
            $file_csv = excel_to_csv($uploadfile); // converte excel em csv (para poder trabalhar)

            $row = 1;
            if (($handle = fopen($file_csv, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if ($row > 5) {
                        for ($i = 0; $i < count($data); $i++) {
                            $data[$i] = utf8_decode(trim(preg_replace('/\s+/', ' ', $data[$i])));
//                            $data[$i] = preg_replace('/\s*$/', '', trim($data[$i]));
                        }
                        $arr_lista[] = $data;
                    }
                    $row++;
                }
            }
// exclui arquivos (sao temporarios)
        }
    }
    
    //print_array($arr_lista);

// fim importacao do excel -------------------------------------------------

    $condicao = (!empty($filtra_tipo)) ? " AND a.tipo = '$filtra_tipo' " : "";

    $query = "SELECT *,b.valor_produto AS vUnCom 
        FROM nfe_produtos AS a 
        INNER JOIN produto_fornecedor_assoc AS b ON (a.id_prod = b.id_produto) 
        WHERE b.id_fornecedor = '$id_fornecedor' $condicao and a.status = 1;";

    $qry = mysql_query($query);

    while ($row_produtos = mysql_fetch_assoc($qry)) {
        $valor = getValor($row_produtos['xProd'], $arr_lista, $filtra_tipo);
        $retorno_produtos[] = array(
            'id_prod' => $row_produtos['id_prod'],
            'cProd' => $row_produtos['cProd'],
            'xProd' => utf8_encode($row_produtos['xProd']),
            'uCom' => utf8_encode($row_produtos['uCom']),
            'vUnCom' => $row_produtos['vUnCom'],
            'qCom' => $valor,
            'total' => $valor * $row_produtos['vUnCom'],
        );
    }
    echo (json_encode(array('itens' => $retorno_produtos)));
//    unlink($uploadfile);
//    unlink($file_csv);
    exit();
}

// SALVAR CADASTRO  PEDIDO -----------------------------------------------------
if (isset($_REQUEST['gerarpedido'])) {

    $id_projeto1 = $_REQUEST['projeto'];
    $id_regiao1 = $usuario['id_regiao'];
    $id_prestador1 = $_REQUEST['id_prestador'];
    $observacao = $_REQUEST['observacao'];
    $tipo = $_REQUEST['filtra_tipo'];
    $itens_pedido = $pedido->preparaArrayItens($_REQUEST['qtde'], $_REQUEST['vProd'], $_REQUEST['idProd']);

    $result = $pedido->solicitacaoPedido($id_regiao1, $id_projeto1, $id_prestador1, 1, $observacao, $_COOKIE['logado'], $itens_pedido, $tipo);

    $dados = array(
        'id_projeto' => $id_projeto1,
        'id_prestador' => $id_prestador1,
        'id_pedido' => $result['id_pedido']
    );

    $novo_pedido = $pedido->consultaPedido($dados, FALSE);
// retorna o array em utf8 encoding
    array_walk_recursive($novo_pedido, function(&$item, $key) {
        if (!mb_detect_encoding($item, 'utf-8', true)) {
            $item = utf8_encode($item);
        }
    });
    $x = array_keys($novo_pedido);
    $novo_pedido = $novo_pedido[$x[0]];
    if ($result['status']) {
        $msg = utf8_encode('Pedido Cadastrado.');
        echo json_encode(array('msg' => $msg, 'status' => TRUE, 'novo_pedido' => $novo_pedido));
    } else {
        $msg = utf8_encode($result['msg']);
        echo json_encode(array('msg' => $msg, 'status' => FALSE));
    }
    exit();
}

if (isset($_REQUEST['confirmarOK'])) {
    $itens_pedido = $pedido->preparaArrayItens($_REQUEST['qCom'], $_REQUEST['vProd'], $_REQUEST['idProd']);

    $result = $pedido->confirmaOk($_REQUEST['id'], $itens_pedido, $usuario['id_funcionario']);
    if ($result['status']) {
        $pedido->iniciaFpdf();

        $pedido->setFileName("PED" . $_REQUEST['id'] . ".pdf");
        $pedido->geraPdf();
        $pedido->finalizaPdf();
        $pedido->limpaVariaveis();
        echo json_encode(array('nomeFile' => $pedido->nomeFile, 'status' => TRUE));

        exit();
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => $result['msg']));
    }
    exit();
}


if (isset($_REQUEST['filtrar_finalizados'])) {
    $dadosConsultaPed = " A.tipo = {$_REQUEST['tipo_finalizado']} AND A.status IN(3,4,5) ";
    $mes = str_pad($_REQUEST['mes'], 2, '0', STR_PAD_LEFT);
    $ano = str_pad($_REQUEST['ano'], 4, '0', STR_PAD_LEFT);
    $data = $_REQUEST['mes'] > 0 ? " AND DATE_FORMAT(A.datadopedido,'%m-%Y') = '{$mes}-{$ano}' ":"";
    $listaEnviados = $pedido->consultaPedido($dadosConsultaPed.$data); //pedidos que já foram enviados
    foreach ($listaEnviados as $value) {
        ?>
        <tr id="tr-<?= $value['id_pedido'] ?>">
            <td class="text-center"><?= $value['id_pedido'] ?></td>
            <td><?= converteData($value['dtpedido'], "d/m/Y") ?></td>
            <td><?= $value['upa'] ?></td>
            <td>
                <?php
                if ($value['tipo'] == 2) {
                    echo 'Medicamentos';
                } else if ($value['tipo'] == 1) {
                    echo 'Material';
                }
                ?>
            </td>
            <td><?= $value['razao'] ?></td>
            <td class="text-right">
                <a href="pdf/PED<?= $value['id_pedido'] ?>.pdf" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a>
                <button type="button" class="btn btn-info btn-xs conferencia" data-id="<?= $value['id_pedido'] ?>"><i class="fa fa-list"></i> Conferência</button>
                <button type="button" class="btn btn-danger btn-xs excluir" data-id="<?= $value['id_pedido'] ?>"><i class="fa fa-times"></i> Excluir</button>
            </td>
        </tr>
        <?php
    }
}


if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'conferencia') {
    $array = $pedido->conferencia($_REQUEST['id']);
    if ($_REQUEST['print']) {
        include_once('conferencia_print.php');
    } else {
        include_once('conferencia.php');
    }
    exit();
}

// -----------------------------------------------------------------------------


function excel_to_csv($file) {
    /**
      Convert excel file to csv
     */
    $xxx = explode('.', $file);
    
//Various excel formats supported by PHPExcel library
//    $excel_readers = array(
//        'Excel5',
//        'Excel2003XML',
//        'Excel2007'
//    );
    $format = $xxx == 'xls' ? 'Excel5' : 'Excel2007';

//    $reader = PHPExcel_IOFactory::createReader('Excel5');
    $reader = PHPExcel_IOFactory::createReader($format);
    $reader->setReadDataOnly(true);

    $path = $file;
    $excel = $reader->load($path);

    $writer = PHPExcel_IOFactory::createWriter($excel, 'CSV');
    $file_csv = "file.csv";
    $writer->save($file_csv);
    return $file_csv;
}

/*
 * @text = nome do produto
 * @arr  = array do excel
 */

function getValor($text, $arr, $tipo) {
    $indice = ($tipo == 1) ? 5 : 3;
    for ($i = 0; $i < count($arr); $i++) {
        if ($text == $arr[$i][1]) {
            return $arr[$i][$indice];
        }
    }
}
