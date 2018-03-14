<?php

// ##############################################################################################
// Mudar antes de subir

include("../conn.php");
include("../wfunction.php");

//include("conn.php");
//include("wfunction.php");

// ##############################################################################################

// Arquivo sinc novo, passando a máquina em que o ponto é batido.

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
    
    $strInsert = "INSERT INTO `terceiro_ponto` (`id_projeto`, `id_regiao`, `id_terceirizado`,`id_autonomo`,`id_funcionario`, `id_cracha_temporario`, `data_completa`, `data`, `hora`, `imagem`, `pis`, `flag`, `tipo`, `maquina`) \nVALUES ".$sqlIn."\n";
    mysql_query($strInsert);
}

// Pegando parâmetros do cliente.

$id_projeto = validatePost('projeto');
$id_regiao = validatePost('regiao');
$maquina = validatePost('maquina');
$maquina_id = validatePost('maquina_id');

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
$ultimo_cra = validatePost('ultimo_cra');
$ultimo_pla = validatePost('ultimo_pla');
$ultimo_ass = validatePost('ultimo_ass');
$ultimo_del = validatePost('ultimo_del');
$data = validatePost('data');

$ultimo_ter = (empty($ultimo_ter)) ? 0 : $ultimo_ter;
$ultimo_clt = (empty($ultimo_clt)) ? 0 : $ultimo_clt;
$ultimo_aut = (empty($ultimo_aut)) ? 0 : $ultimo_aut;
$ultimo_cra = (empty($ultimo_cra)) ? 0 : $ultimo_cra;
$ultimo_pla = (empty($ultimo_pla)) ? 0 : $ultimo_pla;
$ultimo_ass = (empty($ultimo_ass)) ? 0 : $ultimo_ass;
$ultimo_del = (empty($ultimo_del)) ? 0 : $ultimo_del;

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

$sql4 = "select id_cracha_temporario from cracha_temporario where id_cracha_temporario > {$ultimo_cra} and id_projeto = {$id_projeto}";

$rs4 = mysql_query($sql4);
$total4 = mysql_num_rows($rs4);

$sql5 = "select 
			acesso_plantao_id, maquina_id, data_cadastro, 
			data_inicio, data_fim, hora_entrada, hora_saida, 
			mes_vigencia, projeto_id, regiao_id, `update`
		from acesso_plantao
		where
			projeto_id = {$id_projeto} and maquina_id = {$maquina_id} and (acesso_plantao_id > {$ultimo_pla} or `update` = 'S')";
			
//echo "sql5 = [{$sql5}]<br/>\n";
			
$rs5 = mysql_query($sql5);
$total5 = mysql_num_rows($rs5);

$sql_u = "update acesso_plantao set `update` ='N' where projeto_id = {$id_projeto} and maquina_id = {$maquina_id} and `update` = 'S'";
mysql_query($sql_u);

$sql6 = "select 
			a.acesso_assoc_id, a.acesso_plantao_id, a.colaborador_id, a.tipo_colaborador
		from 
			acesso_assoc a inner join acesso_plantao p on a.acesso_plantao_id = p.acesso_plantao_id
		where 
			p.projeto_id = {$id_projeto} and p.maquina_id = {$maquina_id} and a.acesso_assoc_id > {$ultimo_ass}";

$rs6 = mysql_query($sql6);
$total6 = mysql_num_rows($rs6);

$sql7 = "select * from acesso_delete where maquina_id = {$maquina_id} and id > {$ultimo_del}";
$rs7 = mysql_query($sql7);
$total7 = mysql_num_rows($rs7);

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
if ($total3 > 0){
    unset($linha);
    while ($val = mysql_fetch_assoc($rs3)) {
        $linha = $dom->createElement("autonomos");
        $linha->setAttribute("id_autonomo", $val['id_autonomo']);
        $linha->setAttribute("nome", utf8_encode($val['nome']));
        $linha->setAttribute("campo3", utf8_encode($val['campo3']));
        $root->appendChild($linha);
    }
}
if ($total4 > 0){
    unset($linha);
    while ($val = mysql_fetch_assoc($rs4)) {
        $linha = $dom->createElement('crachas');
        $linha->setAttribute("id_cracha_temporario", $val['id_cracha_temporario']);
        $root->appendChild($linha);
    }
}
if ($total5 > 0){
    unset($linha);
    while ($val = mysql_fetch_assoc($rs5)) {
        $linha = $dom->createElement('acesso_plantao');
        $linha->setAttribute("acesso_plantao_id", $val['acesso_plantao_id']);
		$linha->setAttribute("maquina_id", $val['maquina_id']);
		$linha->setAttribute("data_cadastro", $val['data_cadastro']);
		$linha->setAttribute("data_inicio", $val['data_inicio']);
		$linha->setAttribute("data_fim", $val['data_fim']);
		$linha->setAttribute("hora_entrada", $val['hora_entrada']);
		$linha->setAttribute("hora_saida", $val['hora_saida']);
		$linha->setAttribute("mes_vigencia", $val['mes_vigencia']);
		$linha->setAttribute("projeto_id", $val['projeto_id']);
		$linha->setAttribute("regiao_id", $val['regiao_id']);
		$linha->setAttribute("update", $val['update']);
        $root->appendChild($linha);
    }
}
if ($total6 > 0)
{
    unset($linha);
    while ($val = mysql_fetch_assoc($rs6)) 
	{
        $linha = $dom->createElement('acesso_assoc');
        $linha->setAttribute("acesso_assoc_id", $val['acesso_assoc_id']);
		$linha->setAttribute("acesso_plantao_id", $val['acesso_plantao_id']);
		$linha->setAttribute("colaborador_id", $val['colaborador_id']);
		$linha->setAttribute("tipo_colaborador", $val['tipo_colaborador']);
        $root->appendChild($linha);
    }
}

if ($total7 >0)
{
    unset($linha);
    while ($val = mysql_fetch_assoc($rs7))
	{
        $linha = $dom->createElement('acesso_delete');
		$linha->setAttribute("id", $val['id']);
		$linha->setAttribute("maquina_id", $val['maquina_id']);
		$linha->setAttribute("acesso_plantao_id", $val['acesso_plantao_id']);
		$linha->setAttribute("data", $val['data']);
        $root->appendChild($linha);
    }
}

$dom->appendChild($root);

$xml = $dom->saveXML();
header("Content-Type: text/xml");
print $xml;

function iLog($linha)
{
	$fp = fopen(getcwd()."/log.txt", "a");
	fwrite($fp, $linha . "\r\n");
	fclose($fp);
}
