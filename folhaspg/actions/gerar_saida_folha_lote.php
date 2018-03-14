<?php
include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../wfunction.php');
include('../../classes_permissoes/regioes.class.php');
include('../../classes/c_planodecontasClass.php');

$objPlanoContas = new c_planodecontasClass();
$usuario = carregaUsuario();

$REGIAO = new Regioes();

if (isset($_POST['confirmar'])) {
    $enc = $_POST['enc'];
    $id_banco = $_POST['banco'];
    $id_folha = $_POST['id_folha'];
    $id_regiao = $_POST['regiao'];
    $id_projeto = $_POST['projeto'];
    $data_vencimento = implode('-', array_reverse(explode('/', $_REQUEST['data_vencimento'])));
    $sql = array();

    $qr_folha = mysql_query("SELECT * FROM folhas WHERE id_folha = '$id_folha'");
    $row_folha = mysql_fetch_assoc($qr_folha);
    
    /*SELECIONA O PROJETO PARA COLOCAR NO CAMPO DE ESPECIFICAÇÃO DA SAIDA*/
    $qr_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '{$row_folha['projeto']}'");
    $row_projeto = mysql_fetch_assoc($qr_pro);
    $projeto = $row_projeto['nome'];
    
    $ids_trabalhadores = implode(',', $_REQUEST['trabalhador']);

    $especifica = 'COMP. ' . $row_folha['mes'] . '/' . $row_folha['ano'] . " PROJETO: {$projeto}";


    switch ($row_folha['contratacao']) {

        case 1: $tipo = 30;
            $result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$id_folha'  AND id_folha_pro IN($ids_trabalhadores) AND vinculo_financeiro != 1  AND status IN('3','4')  ORDER BY banco, nome");
            $tabela_folha = 'folha_autonomo';
            break;
        case 3: $tipo = 32;
            $result_folha_pro = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha = '$id_folha'  AND id_folha_pro IN($ids_trabalhadores)  AND vinculo_financeiro != 1  AND status IN('3','4') ORDER BY banco, nome");
            $tabela_folha = 'folha_cooperado';
            break;
    }
    
    

    while ($row_folha_proc = mysql_fetch_assoc($result_folha_pro)):
//        $valor = str_replace('.', ',', $row_folha_proc['salario_liq']);
        $valor = str_replace(',', '.', str_replace('.', '', $row_folha_proc['salario_liq']));

        $sql = "('$id_regiao', '$id_projeto', '$id_banco', '$_COOKIE[logado]', '$row_folha_proc[nome]',  '$especifica', '$tipo', '$valor',NOW(), '$data_vencimento',  '1', '0')";
        $qr_insert = mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,  especifica, tipo, valor, data_proc, data_vencimento, status,comprovante) VALUES $sql");
        $id_saida = mysql_insert_id();
        
        //LANÇAMETO CONTABIL
        $array_lancamento = array('id_saida' => $id_saida, 'id_projeto' => $id_projeto, 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => date("Y-m-d"), 'historico' => $especifica);
        $id_lancamento = $objPlanoContas->inserirLancamento($array_lancamento,$row_folha['mes'],$row_folha['ano']);

//        $array_itens = array();
//        $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_tipo' => $tipo, 'valor' => $row_folha_proc['salario_liq'], 'documento' => $n_documento, 'tipo' => 2, 'id_projeto' => $id_projeto);
//        $array_itens[] = array('id_lancamento' => $id_lancamento, 'id_banco' => $id_banco, 'valor' => $row_folha_proc['salario_liq'], 'documento' => $n_documento, 'tipo' => 1);
//        $objPlanoContas->inserirItensLancamento($array_itens);
        $id_folha_proc[] = $row_folha_proc['id_folha_pro'];
    endwhile;

//    $sql = implode(',', $sql);
    $id_folha_proc = implode(',', $id_folha_proc);

//    $qr_insert = mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome,  especifica, tipo, valor, data_proc, data_vencimento, status,comprovante) VALUES $sql");
    if ($qr_insert) {
        mysql_query("UPDATE $tabela_folha SET vinculo_financeiro = 1 WHERE id_folha_pro IN($id_folha_proc) ");
    }


    header("Location: ../pg_lote_1.php?enc=$enc");
}
?>