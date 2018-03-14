<?php

if (empty($_COOKIE['logado']))
{
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
// teste 5
include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$sql = "SELECT id_saida FROM saida A WHERE A.data_vencimento BETWEEN '2012-11-01' AND '2012-11-30' AND A.id_projeto = 3309 and A.comprovante = 2;";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result))
{
    $list .= '*'.$row['id_saida'].'*; ';
    
}

$list = substr($list, 0, strlen($list)-2);

echo "[$list]";