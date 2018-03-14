<?php 
$mes_atual = (isset($_GET['mes'])) ? sprintf("%02d",$_GET['mes']) : date('m');
$ano_atual  = (isset($_GET['ano'])) ? sprintf("%02d",$_GET['ano']) :date('Y');


$sql_row = "
SELECT prestadorservico.id_prestador,
		prestadorservico.c_fantasia,
		prestadorservico.id_regiao,
		prestadorservico.id_projeto,
		saida.id_saida,
		saida.valor_bruto AS valor_saida,
		prestador_saidas.id_pagamento
 FROM (prestadorservico INNER JOIN prestador_pg USING(id_prestador) ) 
INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida 
LEFT JOIN prestador_saidas ON saida.id_saida = prestador_saidas.id_saida
WHERE MONTH(saida.data_pg) = '$mes_atual'
AND YEAR(saida.data_pg) = '$ano_atual'
AND saida.status = '2'
AND prestadorservico.id_prestador = '$id_prestador'
AND prestador_saidas.id_pagamento IS NULL
ORDER BY prestadorservico.id_prestador";


// PEGANDO AS SAIDAS PARA FAZER O NOT IN 
$query_saida = mysql_query("SELECT * FROM prestador_pagamento WHERE mes = '$mes_atual' AND ano = '$ano_atual'");
$array_saidas = array();
while($row_saidas = mysql_fetch_assoc($query_saida)){
	$array_saidas[] = $row_saidas['id_saida'];
}


if(!empty($array_saidas)){
	$str_saidas = 'AND saida.id_saida NOT IN (' . implode(',',$array_saidas) . ')';
}

$sql = "
SELECT prestadorservico.id_prestador,
		prestadorservico.c_fantasia,
		prestadorservico.id_regiao,
		prestadorservico.id_projeto,
		prestadorservico.prestador_tipo,
		saida.id_saida,
		saida.valor_bruto AS TOTAL
 FROM (prestadorservico INNER JOIN prestador_pg USING(id_prestador) ) 
INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida 
WHERE MONTH(saida.data_pg) = '$mes_atual'
AND YEAR(saida.data_pg) = '$ano_atual'
AND saida.status = '2'
ORDER BY prestadorservico.id_prestador ASC
";
/*
$sql_moster = "SELECT 
prestadorservico.id_prestador, 
prestadorservico.c_fantasia, 
prestadorservico.id_regiao, 
prestadorservico.id_projeto,
prestadorservico.prestador_tipo,
id_pagamento,
SUM(REPLACE(saida.valor,',','.')) AS parcial
FROM (prestadorservico INNER JOIN prestador_pg USING(id_prestador))
INNER JOIN saida USING(id_saida) 

LEFT JOIN (
	SELECT prestadorservico.id_prestador as id_prestador_pagamento,
	prestador_pagamento.id_pagamento,
	prestador_saidas.id_saida AS id_saida2
	
	FROM (prestadorservico LEFT JOIN prestador_pagamento USING (id_prestador))
	INNER JOIN prestador_saidas ON prestador_pagamento.id_pagamento = prestador_saidas.id_pagamento
	INNER JOIN saida ON prestador_saidas.id_saida = saida.id_saida
	WHERE prestador_pagamento.status_pagamaneto = '1'
	GROUP BY prestadorservico.id_prestador
) AS nomequalquer ON prestadorservico.id_prestador = id_prestador_pagamento


WHERE saida.status = '2'
AND MONTH(saida.data_pg) = '{$mes_atual}'
AND YEAR(saida.data_pg) = '{$ano_atual}'
AND prestador_pg.status_reg = '1'
GROUP BY prestadorservico.id_prestador, id_pagamento
ORDER BY prestadorservico.id_prestador ASC";*/

//echo $sql_moster;

$sql_moster = "
SELECT prestadorservico.id_prestador, prestadorservico.id_regiao, prestadorservico.id_projeto, prestadorservico.c_fantasia ,
	saida.id_saida, saida.valor, id_pagamento, SUM(saida.valor_bruto) AS bruto, SUM(REPLACE(saida.valor,',','.') + REPLACE(saida.adicional,',','.')) AS parcial,
	prestadorservico.prestador_tipo
       FROM 
(prestadorservico INNER JOIN prestador_pg ON prestadorservico.id_prestador = prestador_pg.id_prestador)
INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida

LEFT JOIN ( 
	SELECT prestador_saidas.id_saida AS id_saida2,  prestador_pagamento.id_pagamento  FROM 
	prestadorservico LEFT JOIN prestador_pagamento 
	ON prestadorservico.id_prestador = prestador_pagamento.id_prestador
	INNER JOIN prestador_saidas 
	ON prestador_saidas.id_pagamento = prestador_pagamento.id_pagamento
) AS b
ON prestador_pg.id_saida = id_saida2
WHERE MONTH(saida.data_pg) = '{$mes_atual}' 
AND YEAR(saida.data_pg) = '{$ano_atual}' 
AND saida.status = '2' 
Group by prestadorservico.id_prestador, id_pagamento
ORDER BY prestadorservico.id_prestador ASC";

/*
$sql = "
SELECT prestadorservico.id_prestador,
		prestadorservico.c_fantasia,
		prestadorservico.id_regiao,
		prestadorservico.id_projeto,
		prestadorservico.prestador_tipo,
		SUM(REPLACE(saida.valor,',','.')) AS TOTAL
 FROM (prestadorservico INNER JOIN prestador_pg USING(id_prestador) ) 
INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida 
WHERE MONTH(saida.data_pg) = '$mes_atual'
AND saida.status = '2'
GROUP BY prestadorservico.id_prestador
";*/
//SUM(REPLACE(saida.valor,',','.')) AS TOTAL
//GROUP BY prestadorservico.id_prestador




?>