<?php

include("../conn.php");
include("../wfunction.php");
include('../classes/global.php');


//METODO PARA RETORNAR UM HTML COM OS DADOS DA SAIDA
 if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaSaida") {
    $id_saida = $_REQUEST['idSaida'];
    $carregaSaida = mysql_query("SELECT A.id_saida, A.id_projeto, B.nome AS nome_projeto, A.id_banco, C.nome AS nome_banco, A.nome AS nome_saida, A.especifica, A.valor, A.data_vencimento, if(A.status = 1, 'A PAGAR', if(A.status = 2, 'PAGA', 'EXCLUIDA')) AS status
                            FROM saida AS A
                            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                            LEFT JOIN bancos AS C ON (A.id_banco = C.id_banco)
                            WHERE id_saida = $id_saida");
 
    $dados = array();
    while ($dadosSaida = mysql_fetch_assoc($carregaSaida)) { 
         $dados[] = array("id_saida" => $dadosSaida['id_saida'],"nome_saida" => $dadosSaida['nome_saida'],"especifica" => $dadosSaida['especifica'],"status" => $dadosSaida['status'],"id_projeto" => $dadosSaida['id_projeto'],"nome_projeto" => $dadosSaida['nome_projeto'],"id_banco" => $dadosSaida['id_banco'],"nome_banco" => $dadosSaida['nome_banco']);
    }
    echo json_encode($dados);
    exit;
}
?>