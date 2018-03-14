<?php 
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";

$id = $_REQUEST['id'];
$grupo = $_REQUEST['grupo'];
$tipo = $_REQUEST['tipo'];
$nome = $_REQUEST['nome'];
$regiao = $_REQUEST['regiao'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$tabela = $_REQUEST['tabela'];

$pk = 'id_'.$tabela;


$campos = array($tabela.'.'.$pk,
$tabela.'.nome',
$tabela.'.valor',
$tabela.'.adicional',
$tabela.'.especifica',
$tabela.'.data_pg',
$tabela.'.id_user',
$tabela.'.id_userpg',
$tabela.'.data_vencimento',
'entradaesaida_grupo.id_grupo',
'entradaesaida_grupo.nome_grupo',
$tabela.'.id_banco',
$tabela.'.id_regiao',
'regioes.regiao',
'regioes.id_master',
$tabela.'.id_projeto',
'projeto.nome AS nome_projeto',
$tabela.'.comprovante'
);

$dados = implode(',',$campos);


$sql_base = "SELECT $dados FROM
((($tabela INNER JOIN entradaesaida ON $tabela.tipo = entradaesaida.id_entradasaida)
LEFT JOIN entradaesaida_grupo ON entradaesaida.grupo = entradaesaida_grupo.id_grupo)
LEFT JOIN regioes ON regioes.id_regiao = $tabela.id_regiao)
LEFT JOIN projeto ON projeto.id_projeto = $tabela.id_projeto";

// Variavel que vai armazenar o estruçãao sql para ser usada para visualizar a impressão
$sql_relatorio = "";

// ORDENANDO POR ORDEM DE PAGAMENTO, COMO SEI ISSO PODE MUDAR UM DIA ESTA AKI A VARIAVEL!
$ordem = "ORDER BY $tabela.data_vencimento ASC";

$charset = mysql_set_charset('utf8');

// EXEÇÔES PARA REGIOES DE ADMINISTRAÇÃO
$array_regioes = array('15','37');
if(!in_array($regiao,$array_regioes)){
	$regiao_where = "$tabela.id_regiao = '$regiao'";
	$and_regiao = ' AND ';
}

// SE tiver digitado o id busca so pelo id
if(!empty($id)):
	$sql_relatorio = "$sql_base WHERE $pk = '$id' AND $tabela.status = '2'  $and_regiao $regiao_where $ordem";
	$query_id = mysql_query($sql_relatorio);
	$num_id = mysql_num_rows($query_id);
	if(empty($num_id)){
		// SE o id digitado voltar vazio e porquer não foi o id completo. então busca por like para ver se consegue encontrar.
		$sql_relatorio = "$sql_base WHERE $pk LIKE '$id%' AND $tabela.status = '2'  $and_regiao $regiao_where $ordem";
		$query_id = mysql_query($sql_relatorio);
	}else{
		// SE o id digitado retornou alguma coisa e porque e o id exato, enão ja monta o JSON e da um exit.
		$row = mysql_fetch_assoc($query_id);
		$row['valor'] = 'R$ '.$row['valor'];
		$row['data_vencimento'] = implode('/',array_reverse(explode('-',$row['data_vencimento'])));
		$row['data_pg'] = implode('/',array_reverse(explode('-',$row['data_pg'])));
		
		#Buscando comprovantes e adicionando no json
		$qr_quant = mysql_query("SELECT * FROM saida_files WHERE $pk = '$row[$pk]'");
		$quanti = mysql_num_rows($qr_quant);
		$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE $pk = '$row[$pk]'");
		$quanti2 = mysql_num_rows($qr_quant2);
		$link_encryptado = encrypt('ID='.$row[$pk].'&tipo=0');
		$link_encryptado_pg = encrypt('ID='.$row[$pk].'&tipo=1');
		if(!empty($quanti)){
			$comprovante1 = $link_encryptado;
			$row['comprovante1'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$comprovante1.'" target="_blank">ver</a>';
		}else{
			$row['comprovante1'] = ' ';
		}
		if(!empty($quanti2)){
			$comprovante2 = $link_encryptado_pg;
			$row['comprovante2'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$comprovante2.'" target="_blank">ver</a>';
		}else{
			$row['comprovante2'] = ' ';
		}		
		if($row['comprovante'] == '1'){
			$comprovante1 = $link_encryptado;
			$row['comprovante1'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'" target="_blank">Ver</a>';
		}else{
			$row['comprovante1'] = ' ';
		}
		# FIM Buscando comprovantes e adicionando no json
		//$JSON['sql'][] = encrypt('sql='.$sql_relatorio);
		$JSON['sql'][] = $sql_relatorio;
		$JSON['dados'][] = $row;
		echo json_encode($JSON);
		exit;
	}
else:
$sql_busca = "$sql_base
WHERE ";
// ARGUMENTOS PARA PESQUIZA
if(!empty($grupo)) { $where[] = "entradaesaida.grupo=$grupo"; }
if(!empty($tipo)) { $where[] = "$tabela.tipo=$tabela"; }
if(!empty($nome)) { $where[] = "(($tabela.nome LIKE '$nome%' OR $tabela.nome LIKE '%$nome%' OR $tabela.nome LIKE '%$nome') OR ($tabela.especifica LIKE '$nome%' OR $tabela.especifica LIKE '%$nome%' OR $tabela.especifica = '%$nome'))"; }
if(!empty($mes)) {	$where[] = "MONTH($tabela.data_vencimento) = '$mes'"; }
if(!empty($ano)) {	$where[] = "YEAR($tabela.data_vencimento) = '$ano'"; }

$where[] = "$tabela.status = '2'";
if(!empty($regiao_where)) {$where[] = $regiao_where;}
$sql_busca .= implode(' AND ',$where);

$sql_busca .= ' '.$ordem;
$sql_relatorio =  $sql_busca;
/*echo $sql_busca;
exit;
*/
//if($tabela == 'entrada'){
//    echo json_encode(array('sql'=>$sql_busca));
//    exit;
//}

$query_saida = mysql_query($sql_busca);
while($row_saida = mysql_fetch_assoc($query_saida)){
	
	
    if($tabela == 'saida') {
	$row_saida['valor'] = 'R$ '.$row_saida['valor'];
	$row_saida['data_vencimento'] = implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])));
	$row_saida['data_pg'] = implode('/',array_reverse(explode('-',$row_saida['data_pg'])));
	#Buscando comprovantes e adicionando no json
	$qr_quant = mysql_query("SELECT * FROM saida_files WHERE $pk = '$row_saida[$pk]'");
	$quanti = mysql_num_rows($qr_quant);
	$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE $pk = '$row_saida[$pk]'");
	$quanti2 = mysql_num_rows($qr_quant2);
	$link_encryptado = encrypt('ID='.$row_saida[$pk].'&tipo=0');
	$link_encryptado_pg = encrypt('ID='.$row_saida[$pk].'&tipo=1');
	
	if(!empty($quanti)){
		$comprovante1 = $link_encryptado;
		$row_saida['comprovante1'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$comprovante1.'" target="_blank">ver</a>';
	}else{
		$row_saida['comprovante1'] = ' ';
	}
	if(!empty($quanti2)){
		$comprovante2 = $link_encryptado_pg;
		$row_saida['comprovante2'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$comprovante2.'" target="_blank">ver</a>';
	}else{
		$row_saida['comprovante2'] = ' ';
	}
	if($row_saida['comprovante'] == '1'){
		$row_saida['comprovante1'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'" target="_blank">Ver</a>';
	}else{
		$row_saida['comprovante1'] = ' ';
	}
	
	# FIM Buscando comprovantes e adicionando no json
    }else{
            $row_saida['comprovante1'] = '';
            $row_saida['comprovante2'] = '';
        }
	
	if($tabela == 'entrada'){
		// buscando anexos em entradas do tipo repasse
		
			$qr_notas = mysql_query("SELECT * FROM notas_assoc WHERE id_entrada = '$row_saida[id_entrada]'");
			$num_notas = mysql_num_rows($qr_notas);
			$row_notas = mysql_fetch_assoc($qr_notas);
			if(!empty($num_notas)) {
				$row_saida['comprovante1'] = '<a href="http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_notas/visializa_files.php?id_nota='. $row_notas['id_notas'].'" target="_blank">ver anexo</a>';
			}
			
	}
	
	$JSON['dados'][] = $row_saida;
}
endif;

if(isset($query_id)):
while($row_id = mysql_fetch_assoc($query_id)){
	$row_id['valor'] = 'R$ '.$row_id['valor'];
	$row_id['data_vencimento'] = implode('/',array_reverse(explode('-',$row_id['data_vencimento'])));
	$row_id['data_pg'] = implode('/',array_reverse(explode('-',$row_id['data_pg'])));
	#Buscando comprovantes e adicionando no json
	$qr_quant = mysql_query("SELECT * FROM saida_files WHERE $pk = '$row_id[$pk]'");
	$quanti = mysql_num_rows($qr_quant);
	$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE $pk = '$row_id[$pk]'");
	$quanti2 = mysql_num_rows($qr_quant2);
	$link_encryptado = encrypt('ID='.$row_id[$pk].'&tipo=0');
	$link_encryptado_pg = encrypt('ID='.$row_id[$pk].'&tipo=1');
	
	if(!empty($quanti)){
		$comprovante1 = $link_encryptado;
		$row_id['comprovante1'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$comprovante1.'" target="_blank">ver</a>';
	}else{
		$row_id['comprovante1'] = ' ';
	}
	if(!empty($quanti2)){
		$comprovante2 = $link_encryptado_pg;
		$row_id['comprovante2'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$comprovante2.'" target="_blank">ver</a>';
	}else{
		$row_id['comprovante2'] = ' ';
	}	
	if($row_id['comprovante'] == '1'){
		$row_id['comprovante1'] = '<a href="../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'" target="_blank">Ver</a>';
	}else{
		$row_id['comprovante1'] = ' ';
	}
	# FIM Buscando comprovantes e adicionando no json
	$JSON['dados'][] = $row_id;
}
endif;
if(!empty($JSON['dados'])){
	//$JSON['sql'][] = encrypt('sql='.$sql_relatorio);
	$JSON['sql'][] = $sql_relatorio;
	echo json_encode($JSON);
}else{
	$json_erro['erro'] = array('Saida não encontrada'); 
echo json_encode($json_erro);
}

?>