<?php
//CLASSE curso 30.07.2009
class tabcurso{

public   $id_curso;
public  $nome;
public  $area;
public  $id_regiao;
public  $local;
public  $inicio;
public  $termino;
public  $descricao;
public  $valor;
public  $parcelas;
public  $campo1;
public  $campo2;
public  $campo3;
public  $cbo_nome;
public  $cbo_codigo;
public  $id_horario;
public  $salario;
public  $ir;
public  $mes_abono;
public  $id_user ;
public  $data_cad;
public  $tipo;
public  $hora_semana;
public  $hora_mes;
public  $status ;
public  $status_reg;
public  $qnt_maxima;
public  $tipo_insalubridade;
public  $qnt_salminimo_insalu;
public  $hora_folga;
    
    
    
	function MostraCurso($cursos){
	
		$RE = mysql_query("SELECT * FROM curso WHERE id_curso = '$cursos'");
		$Row = mysql_fetch_array($RE);
		
		$this->id_curso		= $Row['id_curso'];
		$this->nome 		= $Row['nome'];
		$this->area 		= $Row['area'];
		$this->id_regiao 	= $Row['id_regiao'];
		$this->local 		= $Row['local'];
		$this->inicio 		= $Row['inicio'];
		$this->termino 		= $Row['termino'];
		$this->descricao 	= $Row['descricao'];
		$this->valor 		= $Row['valor'];
		$this->parcelas 	= $Row['parcelas'];
		$this->campo1 		= $Row['campo1'];
		$this->campo2 		= $Row['campo2'];
		$this->campo3 		= $Row['campo3'];
		$this->cbo_nome 	= $Row['cbo_nome'];
		$this->cbo_codigo 	= $Row['cbo_codigo'];
		$this->id_horario 	= $Row['id_horario'];
		$this->salario 		= $Row['salario'];
		$this->ir 			= $Row['ir'];
		$this->mes_abono 	= $Row['mes_abono'];
		$this->id_user 		= $Row['id_user'];
		$this->data_cad 	= $Row['data_cad'];
		$this->tipo 		= $Row['tipo'];
		$this->hora_semana	= $Row['hora_semana'];
		$this->hora_mes		= $Row['hora_mes'];
		$this->hora_folga	= $Row['hora_folga'];
		$this->status 		= $Row['status'];
		$this->status_reg	= $Row['status_reg'];
		$this->qnt_maxima       = $Row['qnt_maxima'];
                $this->tipo_insalubridade = $Row['tipo_insalubridade'];
                $this->insalubridade    = $Row['insalubridade'];
                $this->qnt_salminimo_insalu = $Row['qnt_salminimo_insalu'];
		
		#NOME DA ATIVIDADE RESUMIDO
		$cargo1 			= str_replace("CAPACITANDO EM ","CAP. EM ", $Row['campo2']);
		$cargo1 			= str_replace("TÉCNICO ","TEC. ", $cargo1);
		$this->cargo 		= str_replace("TECNICO ","TEC. ", $cargo1);

	}

function SelectCursos(){
	
	//CRIANDO UM ARRAY COM TODAS AS REGIÕES COM SEUS RESPECTIVOS ID's
	$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao' and status = '$master'");
	$row_local = mysql_fetch_array($result_local);
	
	$REReg = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE status = 1 and id_master = '$master'");
	while ($row_regiao = mysql_fetch_array($REReg)){
		$idReg = $row_regiao['0'];
		$REGIOES[$idReg] = $row_regiao['1'];
	}
}
}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
- VER_TUDO.PHP 
- COOPERATIVAS/DISTRATO.PHP
*/
?>