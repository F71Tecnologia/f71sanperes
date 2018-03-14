<?php
    
//$servidor = 'localhost';
//$usuario = 'ispv_netsorr';
//$senha = 'F71#0138@10_lagos';
//$banco = 'ispv_netsorrindo';
$projeto = "3309";

 //Criando Conexão
//$conn = mysql_connect($servidor, $usuario, $senha) or die('Nao pude conectar ao banco de dados');

// Selecionando o Banco de Dados
//mysql_select_db($banco) or die('Nao pude selecionar o banco de dados');

//$query_remove = "DELETE FROM rh_recisao WHERE recisao_provisao_de_calculo = 1 AND id_projeto = '{$projeto}' ";
//$sql_remove = mysql_query($query_remove) or die("Erro ao remover recisoes");

//$query = "SELECT id_clt, nome FROM rh_clt WHERE id_projeto = '{$projeto}' AND (status < 60 || status = 200)";
//$sql = mysql_query($query) or die("Erro ao selecionar participantes");

//while($linha = mysql_fetch_assoc($sql)){

    // Inicia o cURL acessando uma URL
    $URL = 'http://f71lagos.com/intranet/rh/recisao/recisao2.php';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $URL);
    
    $dados = array(
        "dispensa" => 61, 
        "fator" => "empregador",
        "diastrab" => 0,
        "valor" => "0,00",
        "faltas" => 0,
        "aviso" => "trabalhado",//indenizado
        "data_aviso" => "01/12/2015",//19/03/2015
        "tela" => 3,
        "idclt" =>  6765,//$linha['id_clt'],
        "regiao" => 44,
        "logado" => 00,
        "data_demi" => "2016-01-01", //2014-07-31
        "recisao_coletiva" => 1
    );    

    curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
    $response = curl_exec($ch);
    $errorMsg = curl_error($ch);
    $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    
    print_r($response);
    //echo $linha['id_clt'] . " - "  . $linha['nome'] . "<br />";
   
//}
    
?>


