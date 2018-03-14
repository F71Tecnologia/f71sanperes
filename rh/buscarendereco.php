<?
$consulta = 'http://viavirtual.com.br/webservicecep.php?cep='.$_GET['cep'];
$consulta = file($consulta);
$consulta = explode('||',$consulta[0]);
// Caso seja necessário poderá salvar os dados em SESSION

$json['endereco'] = utf8_encode($consulta[0]);
$json ['bairro']  = utf8_encode($consulta[1]);
$json['cidade']   = utf8_encode($consulta[2]);
$json['uf']		  = $consulta[4];

echo json_encode($json);
?>