<?php
include("../conn.php");
include("../wfunction.php");

//if($_REQUEST['projeto'] == 3331){
//    print_r($_REQUEST);
//    print_r($_FILES);
//    exit;
//}

//LENDO XML DOS PONTOS BATIDOS LOCAL
if(isset($_REQUEST['in']) && !empty($_REQUEST['in'])){
    $sqlIn = $_REQUEST['in'];
    $sqlIn = str_replace("\\","",$sqlIn);
    $sqlIn = str_replace("DROP","",$sqlIn);
    $sqlIn = str_replace("DELETE","",$sqlIn);
    $sqlIn = str_replace("TRUNCATE","",$sqlIn);
    $sqlIn = str_replace("ALTER","",$sqlIn);
    
    $strInsert = "INSERT INTO `terceiro_ponto` (`id_projeto`, `id_regiao`, `id_terceirizado`,`id_autonomo`,`id_funcionario`, `data_completa`, `data`, `hora`, `imagem`, `pis`) VALUES ".$sqlIn;

    mysql_query($strInsert);
}

$id_projeto = validatePost('projeto');
$id_regiao = validatePost('regiao');

if(isset($_FILES)){
    foreach($_FILES as $files){
        $fileName = $files["name"];
        $fileTmpLoc = $files["tmp_name"];
        //$pathAndName = "../fotos/".$fileName;
	
	if(!file_exists("../fotos/" . $id_projeto . "/".$id_regiao))
	{
	    mkdir("../fotos/" . $id_projeto . "/".$id_regiao);
	}

        $moveResult = move_uploaded_file($fileTmpLoc, "../fotos/" . $id_projeto . "/".$id_regiao."/".$fileName);
    }
}

$ultimo_ter = validatePost('ultimo_ter');
$ultimo_clt = validatePost('ultimo_clt');
$ultimo_aut = validatePost('ultimo_aut');

$ultimo_ter = (empty($ultimo_ter)) ? 0 : $ultimo_ter;
$ultimo_clt = (empty($ultimo_clt)) ? 0 : $ultimo_clt;
$ultimo_aut = (empty($ultimo_aut)) ? 0 : $ultimo_aut;

$sql1 = "SELECT id_terceirizado,nome 
            FROM terceirizado
            WHERE id_terceirizado > {$ultimo_ter} AND id_projeto = {$id_projeto}";
$rs1 = mysql_query($sql1);
$total1 = mysql_num_rows($rs1);

$sql2 = "SELECT id_clt,nome,pis
            FROM rh_clt
            WHERE id_clt > {$ultimo_clt} AND id_projeto = {$id_projeto}";
$rs2 = mysql_query($sql2);
$total2 = mysql_num_rows($rs2);

$sql3 = "SELECT id_autonomo,nome, campo3
            FROM autonomo
            WHERE id_autonomo > {$ultimo_aut} AND id_projeto = {$id_projeto}";
$rs3 = mysql_query($sql3);
$total3 = mysql_num_rows($rs3);

$dom = new DOMDocument("1.0", "ISO-8859-1");
#retirar os espacos em branco
$dom->preserveWhiteSpace = false;
#gerar o codigo
$dom->formatOutput = true;
#cria os elementos e inclui os atributos
$root = $dom->createElement("terceirizado");
if ($total1 > 0) {
    while ($val = mysql_fetch_assoc($rs1)) {
        $linha = $dom->createElement("terceiros");
        $linha->setAttribute("id_terceirizado", $val['id_terceirizado']);
        $linha->setAttribute("nome", utf8_encode($val['nome']));
        $root->appendChild($linha);
    }
}
if ($total2 > 0) {
    unset($linha);
    while ($val = mysql_fetch_assoc($rs2)) {
        $linha = $dom->createElement("clts");
        $linha->setAttribute("id_clt", $val['id_clt']);
        $linha->setAttribute("nome", utf8_encode($val['nome']));
        $linha->setAttribute("pis", $val['pis']);
        $root->appendChild($linha);
    }
}
if ($total3 > 0) {
    unset($linha);
    while ($val = mysql_fetch_assoc($rs3)) {
        $linha = $dom->createElement("autonomos");
        $linha->setAttribute("id_autonomo", $val['id_autonomo']);
        $linha->setAttribute("nome", utf8_encode($val['nome']));
	$linha->setAttribute("campo3", utf8_encode($val['campo3']));
        $root->appendChild($linha);
    }
}
$dom->appendChild($root);

$xml = $dom->saveXML();
header("Content-Type: text/xml");
print $xml;
