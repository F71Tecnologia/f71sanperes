<?php
// Incluindo biblioteca PHPExcel
require_once('phpexcel/Classes/PHPExcel/IOFactory.php');

// Envio de arquivo
$arquivo_nome = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/intranet/finan/relatorios/uploads/').basename($_FILES['arquivo']['name']);
move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivo_nome);

// Abrindo arquivo
try {
	$arquivo_tipo = PHPExcel_IOFactory::identify($arquivo_nome);
	$objeto = PHPExcel_IOFactory::createReader($arquivo_tipo)->load($arquivo_nome);
} catch(Exception $erro) {
	die('Error loading file "'.pathinfo($arquivo_nome, PATHINFO_BASENAME).'": '.$erro->getMessage());
}

// Variáveis de suporte
$planilhaNomes = $objeto->getSheetNames();
$colunas = range('A', 'Z');

// Criando arrays de descrições e valores
$codigos = [];
$descricoes = [];
$unidades = [];
$valores = [];

// Percorrendo todas as planilhas
foreach($objeto->getAllSheets() as $chave_planilha => $planilha) {

	// Nome da planilha
	$planilhaNome = $planilhaNomes[$chave_planilha];

	// Verificando se planilha é de unidade
	if(preg_match("/^Unid|Unid |Unidade|Unidade [0-9]+$/", $planilhaNome)) {

		// Criando arrays da planilha
		$codigos[$chave_planilha] = [];
		$descricoes[$chave_planilha] = [];
		$valores[$chave_planilha] = [];
	
		// Definindo variáveis para percorrer toda a planilha
		$maiorLinha = $planilha->getHighestRow();
		$maiorColuna = $planilha->getHighestColumn();
	
		// Percorrendo planilha
		for($linha=3; $linha<=$maiorLinha; $linha++) {
	
			// Definindo variável para percorrer todas as linhas da planilha
			$dados = $planilha->rangeToArray('A'.$linha.':'.$maiorColuna.$linha, null, true, false);
		
			// Percorrendo linha
			foreach($dados[0] as $coluna => $dado) {
			
				// Qual coluna e valor dela
				@$coluna = $colunas[$coluna];
				$dado = htmlentities($dado);
			
				// Interromper loop corrente
				if($dado == '#REF!' or
				   strstr($dado, htmlentities('TOTAL')) or
				   strstr($dado, htmlentities('São Paulo'))
				) break;
			
				// Inserindo unidade
				if(isset($coluna_unidade) and $coluna_unidade == $linha.$coluna) $unidades[$chave_planilha] = $dado;
			
				// Inserindo descrições
				if(isset($coluna_descricao) and $coluna == $coluna_descricao) $descricoes[$chave_planilha][] = preg_replace('/^[0-9]+\.?([0-9]+)(\. | - )/', '', $dado);
			
				// Inserindo códigos
				if(isset($coluna_descricao) and $coluna == $coluna_descricao) {
					preg_match('/^[0-9]+\.?[0-9]+/', $dado, $codigo);
					if($codigo) $codigos[$chave_planilha][] = $codigo[0];
				}
			
				// Pegando última chave criada na array descricoes
				end($descricoes[$chave_planilha]);
				$ultima_chave_descricao = key($descricoes[$chave_planilha]);
			
				// Inserindo valores
				if(isset($coluna_meses[$coluna]) and $coluna != $coluna_descricao) $valores[$chave_planilha][$ultima_chave_descricao][$coluna_meses[$coluna]] = number_format((int) $dado, 2, ',', '.');
			
				// Conhecendo coluna de unidade
				if($dado == htmlentities('UNIDADE:')) $coluna_unidade = $linha.$colunas[array_search($coluna, $colunas)+2];
			
				// Conhecendo coluna de descrições
				if($dado == htmlentities('DESCRIÇÃO')) $coluna_descricao = $coluna;
			
				// Conhecendo coluna de cada mês
				if(strstr($dado, htmlentities('Mês '))) {
					$mes = str_replace(htmlentities('Mês '), '', $dado);
					$coluna_meses[$coluna] = $mes;
				}
			
			}
		
		}
	
	}

	// Zerando valores para próximo loop
	$codigo = null;
	$coluna_descricao = null;
	$coluna_unidade = null;
	$coluna_meses = [];

}
