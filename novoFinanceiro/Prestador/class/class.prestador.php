<?php 

//include '../../../conn.php';

/**
 * @author james
 *
 */
class Prestador {	
	
	private $mes, $ano;
	
	function __construct($mes,$ano){
		$this->mes = $mes;
		$this->ano = $ano;
	}
	
	function getPrestador($id_prestador = FALSE){
		
		if($id_prestador){
			$sql = "SELECT * FROM prestadorservico WHERE id_pretador = '$id_prestador' LIMIT 1;";
		}else{
			$sql = "SELECT * FROM prestadorservico";
		}
		
		$qr = mysql_query($sql);
		
		$arr = array();
		
		while($row = mysql_fetch_assoc($sql)):
			$arr[] = $row;
		endwhile;
		
		
		return $arr;
	}
	
	function getTotalPagamento ($id_prestador){
		
		$sql = "SELECT 
					saida.*,
					CAST(REPLACE(saida.valor,',','.') AS DECIMAL(10,2)) AS valor_saida
					 FROM 
					(prestadorservico INNER JOIN prestador_pg USING(id_prestador) )
					INNER JOIN saida USING(id_saida) 
					WHERE saida.status = '2'
					AND MONTH(saida.data_pg) = '{$this->mes}'
					AND YEAR(saida.data_pg) = '{$this->ano}'
					AND prestadorservico.id_prestador = '$id_prestador'
					ORDER BY prestadorservico.id_prestador ASC";
		$qr = mysql_query($sql);
		
		$arr = array();
		
		while($row = mysql_fetch_assoc($qr)):
			$arr[] = $row;
		endwhile;
		
		return $arr;
		
		
	}
	
	
	function getPrestadorPorPagamento(){	
		
			$sql = "SELECT 
					prestadorservico.id_prestador,
					prestadorservico.c_fantasia
					 FROM 
					(prestadorservico INNER JOIN prestador_pg USING(id_prestador) )
					INNER JOIN saida USING(id_saida) 
					WHERE saida.status = '2'
					AND MONTH(saida.data_pg) = '{$this->mes}'
					AND YEAR(saida.data_pg) = '{$this->ano}'
					GROUP BY prestadorservico.id_prestador
					ORDER BY prestadorservico.id_prestador ASC";
					
		$qr = mysql_query($sql);
		
		$arr = array();
		
		while($row = mysql_fetch_assoc($qr)):
			$arr[] = $row;
		endwhile;
		
		
		return $arr;
	}
	
	
	
	
}


?>