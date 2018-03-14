<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}

include_once("../conn.php");
include_once("../wfunction.php");
include_once("../classes/c_planodecontasClass.php");

$usuario = carregaUsuario();
$objPlanoContas = new c_planodecontasClass();

$id_lote = $_REQUEST['nrlote'];
$documento = $_REQUEST['documento'];
 
if (!empty($id_lote)) {
    $sql = "
    SELECT A.*, C.id_conta, D.id_projeto
    FROM 
        (SELECT id_entrada id, id_projeto, tipo, CAST(SUM(REPLACE(REPLACE(valor,'.',''),',','.')) AS DECIMAL(15,2)) valor, 'e' es FROM entrada A WHERE A.status = 2 AND A.data_vencimento BETWEEN '2015-01-01' AND LAST_DAY('2015-01-01') GROUP BY id_projeto, tipo
        UNION
        SELECT id_saida id, id_projeto, tipo, CAST(SUM(REPLACE(REPLACE(valor,'.',''),',','.')) AS DECIMAL(15,2)) valor, 's' es FROM saida A WHERE A.status = 2 AND A.data_vencimento BETWEEN '2015-01-01' AND LAST_DAY('2015-01-01') GROUP BY id_projeto, tipo) AS A
        INNER JOIN contabil_contas_assoc B ON(A.tipo = B.id_entradasaida AND B.status = 1)
        INNER JOIN planodecontas C ON(B.id_conta = C.id_conta AND A.id_projeto = C.id_projeto)
        INNER JOIN contabil_lote D ON(A.id_projeto = D.id_projeto)
    WHERE D.id_lote = $id_lote AND D.status = 1
    ORDER BY D.id_projeto, A.tipo";
    $qry = mysql_query($sql) or die(mysql_error());

    if (mysql_num_rows($qry) == 0) {
        echo json_encode(array('msg' => utf8_encode('Não foi possível importar.'), 'status' => 'danger'));
        exit();
    }
    while ($row = mysql_fetch_assoc($qry)) {
        $array_lancamento = $array_itens = array();
        $id_saida = ($row['es'] == 's') ? $row['id'] : null;
        $id_entrada = ($row['es'] == 'e') ? $row['id'] : null;
        $array_lancamento = array('id_saida' => $id_saida, 'id_entrada' => $id_entrada, 'id_lote' => $id_lote, 'id_projeto' => $row['id_projeto'], 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => date("Y-m-d"));
        $id_lancamento = $objPlanoContas->inserirLancamento($array_lancamento);

        $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_conta' => $row['id_cota'], 'valor' => $row['valor'], 'documento' => $documento, 'tipo' => 2);
        $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_conta' => $row['id_conta_banco'], 'valor' => $row['valor'], 'documento' => $documento, 'tipo' => 1);
        $return[] = $objPlanoContas->inserirItensLancamento($array_itens);
        if (!in_array(FALSE, $return)) {
            $objPlanoContas->updateImportacaoLote($id_lote);
            echo json_encode(array('msg' => 'Importado com Sucesso!', 'status' => 'success'));
        } else {
            echo json_encode(array('msg' => utf8_encode('Não foi possível importar. Tente novamente.'), 'status' => 'danger'));
            echo "Informação faltando<br>";
            print_array($_REQUEST);
            //header("Location: ")
        }
    }
}
    