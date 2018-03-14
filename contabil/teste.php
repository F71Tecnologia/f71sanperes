<div id="container">
<div id="form">
  
<?php
include("../conn.php");
include("../wfunction.php");
include("../classes/ContabilLoteClass.php");
//$deleterecords = "TRUNCATE TABLE nome-da-tabela"; //Esvaziar a tabela
//mysql_query($deleterecords);
  
//Transferir o arquivo
//if (isset($_POST['submit'])) {
//  
//    if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
//        echo "<h1>" . "File ". $_FILES['filename']['name'] ." transferido com sucesso ." . "</h1>";
//        echo "<h2>Exibindo o conteúdo:</h2>";
//        //readfile($_FILES['filename']['tmp_name']);
//    }
//  
//    //Importar o arquivo transferido para o banco de dados
//    $handle = fopen($_FILES['filename']['tmp_name'], "r");
//    
//    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//        $data[2] = implode('-', array_reverse(explode('/', $data[2])));
//        $data[3] = implode('-', array_reverse(explode('/', $data[3])));
//        $nivel = count(explode('.', $data[0]));
//        if(!empty($data[0]))
//            $import[] = "('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$nivel')";
////        echo $import.'<br>';
//    }
////    echo "INSERT into planodecontas(classificador,descricao,data_ini,data_fim,tipo,nivel)values".  implode(', ', $import);
//    mysql_query("INSERT into planodecontas(classificador,descricao,data_ini,data_fim,tipo,nivel)values". implode(', ', $import)) or die(mysql_error());
//    fclose($handle);
//  
//    print "Importação feita.";
//  
////Visualizar formulário de transferência
//} else {
//  
//    print "Transferir novos arquivos CSV selecionando o arquivo e clicando no botão Upload<br />\n";
//  
//    print "<form enctype='multipart/form-data' action='teste.php' method='post'>";
//  
//    print "Nome do arquivo para importar:<br />\n";
//  
//    print "<input size='50' type='file' name='filename'><br />\n";
//  
//    print "<input type='submit' name='submit' value='Upload'></form>";
//  
//}
  
//$sql = "SELECT * FROM projeto A WHERE A.status_reg = 1";
//$qry = mysql_query($sql);
//while($row = mysql_fetch_assoc($qry)){
//    for ($i=1; $i<=12; $i++) {
//        $mes = sprintf("%02d",$i);
//        $ano = date('Y');
//        $ins[] = "({$row['id_projeto']}, '{$row['nome']} $mes/$ano', NOW(), 257, '$ano', '$mes')";
//    }
//}
//echo $insert = "INSERT INTO `contabil_lote` (`id_projeto`, `lote_numero`, `data_criacao`, `usuario_criacao`, `ano`, `mes`) VALUES ".  implode(', ', $ins).";";
//mysql_query($insert);


require_once ('../classes/ContabilContasSaldoClass.php');

$objContas = new ContabilContasSaldoClass();

$sqlArray = "SELECT A.id_entradasaida tipo, A.id_conta FROM contabil_contas_assoc A WHERE A.id_conta IN (SELECT id_conta FROM planodecontas WHERE id_projeto = 3313 AND status = 1)";
$qryArray = mysql_query($sqlArray);
while($rowArray = mysql_fetch_assoc($qryArray)){
    $array[$rowArray['tipo']] = $rowArray['id_conta'];
}

$sqlArray = "SELECT A.id_banco, A.id_conta FROM contabil_contas_assoc_banco A WHERE A.id_conta IN (SELECT id_conta FROM planodecontas WHERE id_projeto = 3313 AND status = 1)";
$qryArray = mysql_query($sqlArray);
while($rowArray = mysql_fetch_assoc($qryArray)){
    $arrayB[$rowArray['id_banco']] = $rowArray['id_conta'];
}

$sql = "
SELECT A.tipo cd, A.id_lancamento_itens, A.id_conta, C.tipo id_entradasaida, D.id_banco, 'saida' es, B.contabil, C.data_vencimento, B.id_lancamento, B.data_lancamento, B.id_projeto
FROM contabil_lancamento_itens A
INNER JOIN contabil_lancamento B ON (A.id_lancamento = B.id_lancamento AND B.id_projeto = 3313 AND B.status = 1)
INNER JOIN saida C ON (C.id_saida = B.id_saida)
INNER JOIN bancos D ON (C.id_banco = D.id_banco)
WHERE A.status = 1
UNION
SELECT A.tipo cd, A.id_lancamento_itens, A.id_conta, C.tipo id_entradasaida, D.id_banco, 'entrada' es, B.contabil, C.data_vencimento, B.id_lancamento, B.data_lancamento, B.id_projeto
FROM contabil_lancamento_itens A
INNER JOIN contabil_lancamento B ON (A.id_lancamento = B.id_lancamento AND B.id_projeto = 3313 AND B.status = 1)
INNER JOIN entrada C ON (C.id_entrada = B.id_entrada)
INNER JOIN bancos D ON (C.id_banco = D.id_banco)
WHERE A.status = 1
ORDER BY data_vencimento
;";
//Rogerio disse para usar a data de pagamento (18/09/2015 09:17:37)
//Ramon disse para usar a data de vencimento (18/09/2015 11:30:00)
$qry = mysql_query($sql);
while($row = mysql_fetch_assoc($qry)){
    echo $row['cd'].'<br>';
    $data_vencimento = explode('-',$row['data_vencimento']);
    $ano_vencimento = $data_vencimento[0];
    $mes_vencimento = $data_vencimento[1];
    
    $objLote = new ContabilLoteClass();
    $objLote->setIdProjeto($row['id_projeto']);
    $objLote->setMes($mes_vencimento);
    $objLote->setAno($ano_vencimento);
    $objLote->setStatus(1);
    $objLote->setUsuarioCriacao($_COOKIE['logado']);
    $objLote->setDataCriacao(date('Y-m-d H:i:s'));
    $objLote->setLoteNumero("$nomeProjeto ".sprintf("%02d",$mes_vencimento)."/{$ano_vencimento} - FINANCEIRO");
    $objLote->setTipo(6);
    $objLote->verificaLote();
    //$id_lote = mysql_result(mysql_query("SELECT id_lote FROM contabil_lote WHERE id_lote != 5 AND id_projeto = {$row['id_projeto']} AND tipo = 6 AND ano = YEAR('{$row['data_vencimento']}') AND mes = MONTH('{$row['data_vencimento']}') AND status = 1"),0);
    $updateData = "
    UPDATE contabil_lancamento SET data_lancamento = '{$row['data_vencimento']}', id_lote = {$objLote->getIdLote()} WHERE id_lancamento = {$row['id_lancamento']} LIMIT 1;";
    echo $updateData.'<br>';
    $updateData = mysql_query($updateData)or die(mysql_error());
    if($row['es'] == 'saida'){
        if($row['cd'] == 2){
            if($array[$row['id_entradasaida']]){
                $update = "UPDATE contabil_lancamento_itens SET id_conta = {$array[$row['id_entradasaida']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                echo $update.'<br>';
                mysql_query($update) or die(mysql_error());
                if($row['contabil'] == 1) { $objContas->verificaSaldo($row['id_lancamento_itens']); $objContas->verificaSaldo2($row['id_lancamento_itens']); }
            } else {
                $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                mysql_query($update) or die(mysql_error());
            }
        } else if($row['cd'] == 1){
            if($arrayB[$row['id_banco']]){
                $update = "UPDATE contabil_lancamento_itens SET id_conta = {$arrayB[$row['id_banco']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                echo $update.'<br>';
                mysql_query($update) or die(mysql_error());
                if($row['contabil'] == 1) { $objContas->verificaSaldo($row['id_lancamento_itens']); $objContas->verificaSaldo2($row['id_lancamento_itens']); }
            } else {
                $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                mysql_query($update) or die(mysql_error());
            }
        }
    } else if($row['es'] == 'entrada') {
        if($row['cd'] == 1){
            if($array[$row['id_entradasaida']]){
                $update = "UPDATE contabil_lancamento_itens SET id_conta = {$array[$row['id_entradasaida']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                echo $update.'<br>';
                mysql_query($update) or die(mysql_error());
                if($row['contabil'] == 1) { $objContas->verificaSaldo($row['id_lancamento_itens']); $objContas->verificaSaldo2($row['id_lancamento_itens']); }
            } else {
                $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                mysql_query($update) or die(mysql_error());
            }
        } else if($row['cd'] == 2){
            if($arrayB[$row['id_banco']]){
                $update = "UPDATE contabil_lancamento_itens SET id_conta = {$arrayB[$row['id_banco']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                echo $update.'<br>';
                mysql_query($update) or die(mysql_error());
                if($row['contabil'] == 1) { $objContas->verificaSaldo($row['id_lancamento_itens']); $objContas->verificaSaldo2($row['id_lancamento_itens']); }
            } else {
                $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']} LIMIT 1;";
                mysql_query($update) or die(mysql_error());
            }
        }
    } else {
        echo 'foo<br>';
    }
}

$sql = "SELECT A.id_lancamento_itens, B.contabil
FROM contabil_lancamento_itens A
INNER JOIN contabil_lancamento B ON (A.id_lancamento = B.id_lancamento) AND A.status = 1 AND id_entrada = 0 AND id_saida = 0";
$qry = mysql_query($sql);
while($row = mysql_fetch_assoc($qry)){
    if($row['contabil'] == 1) { $objContas->verificaSaldo($row['id_lancamento_itens']); $objContas->verificaSaldo2($row['id_lancamento_itens']); }
}
?>
  
</div>
</div>
</body>