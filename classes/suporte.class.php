<?php
include('conn.php');
class Suporte{
	
		
		private $id_regiao;
		private $id_projeto;		
		private $exibicao	 = 1;
		private $tipo        = 5;
		private $prioridade  = 4;
		private $assunto;
		private $menssagem;
		private $data_cad;
		private $id_user_cad;
		private $pagina;
		private $status;
		private $status_reg;
		private $tipo_arquivo;
		private $sql;
		
		
		function __construct($regiao,$projeto, $assunto, $menssagem,$id_usuario, $pagina, $arquivo) {
		
			
			
			
			$this->id_regiao 		= $regiao;
			$this->id_projeto 		= $projeto;
			$this->assunto 			= $assunto;
			$this->menssagem 		= $menssagem;
			$this->id_user_cad 		= $id_usuario;
			$this->pagina 			= $pagina;
			$this->id_master 		= $master;
			$this->data_cad   		= date('Y-m-d H:i:s');
			$this->tipo_arquivo 	= $arquivo;
					
			
		}
		
		

	

		function inserir() {
			
			//mudar o status para 1
		$this->sql = mysql_query("INSERT INTO suporte  (user_cad, data_cad, id_regiao, exibicao,  tipo, prioridade, assunto, mensagem, arquivo, status, status_reg,suporte_pagina)
					 VALUES	('$this->id_user_cad ','$this->data_cad','$this->id_regiao', '$this->exibicao', '$this->tipo','$this->prioridade', '$this->assunto', '$this->menssagem', '$this->tipo_arquivo','1','1','$this->pagina');") or die(mysql_error());
					 
					 
			
		}



	
}


?>