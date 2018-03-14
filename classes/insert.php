<?php
//CLASSE insert 07.08.2009
class insert{

	public function __construct() {
		$user = $_COOKIE['logado'];
		
		if(!empty($HTTP_GET_VARS)) {
			while(list($xxxname, $value) = each($HTTP_GET_VARS)) {
				$$xxxname = $value;
			}
		}
		if(!empty($HTTP_POST_VARS)){
			while(list($xxxname, $value) = each($HTTP_POST_VARS)) {
				$$xxxname = $value;
			}
		}
		if(!empty($HTTP_POST_FILES)) {
			while(list($xxxname, $value) = each($HTTP_POST_FILES)) {
				$$xxxname = $value['tmp_name'];
			}
		}
		
	}

	function capturar_campos($array,$campos_reservados) {
		global $txt_msg,$CFG,$csv_header;
		
		if (count($array))
		{
			while (list($key, $val) = each($array))
			{
				$reservado_violado = 0;
				for ($i=0; $i<count($campos_reservados); $i++)
				{
					if ($key == $campos_reservados[$i])
					{
						$reservado_violado = 1;
					}
				}
				if ($reservado_violado != 1)
				{
					if (is_array($val))
					{
						for ($z=0;$z<count($val);$z++)
						{
							$csv_header .= ($CFG['csv_style']==1)? $key.$CFG['csv_delimiter'] : '';
							$conteudo .= ($CFG['csv_style']==1)? $val[$z].$CFG['csv_delimiter'] : "$key = '$val[$z]', \r\n";
						}
					}else{
						$csv_header .= ($CFG['csv_style']==1)? $key.$CFG['csv_delimiter'] : '';
						$conteudo .= ($CFG['csv_style']==1)? $val.$CFG['csv_delimiter'] : "$key = '$val', \r\n";
					}
				}
			}
		}
		return $conteudo;
	}
	
	
	function campos_insert($array,$campos_reservados) {
		global $txt_msg,$CFG,$csv_header;
		
		if (count($array)){
			
			while (list($key, $val) = each($array))	{
				$reservado_violado = 0;
				
				for ($i=0; $i<count($campos_reservados); $i++){
					
					if ($key == $campos_reservados[$i])	{
						$reservado_violado = 1;
					}
				}
				
				if ($reservado_violado != 1){
					
					if (is_array($val)){
						
						for ($z=0;$z<count($val);$z++){
							$csv_header .= ($CFG['csv_style']==1)? $key.$CFG['csv_delimiter'] : '';
							$camposW .= ($CFG['csv_style']==1)? $val[$z].$CFG['csv_delimiter'] : "$key,";
							$conteuW .= ($CFG['csv_style']==1)? $val[$z].$CFG['csv_delimiter'] : "'$val[$z]',";
							//$conteudos .= ($CFG['csv_style']==1)? $val[$z].$CFG['csv_delimiter'] : "$key = '$val[$z]', \r\n";
						}
					
					}else{
						$csv_header .= ($CFG['csv_style']==1)? $key.$CFG['csv_delimiter'] : '';
						$camposW .= ($CFG['csv_style']==1)? $val[$z].$CFG['csv_delimiter'] : "$key,";
						$conteuW .= ($CFG['csv_style']==1)? $val[$z].$CFG['csv_delimiter'] : "'$val',";
						//$conteudos .= ($CFG['csv_style']==1)? $val.$CFG['csv_delimiter'] : "$key = '$val', \r\n";
					}
				}
			}
		}
		
		$this->campos= $camposW;
		$this->valores= $conteuW;
		
		
	}

	function InsertDinamico($tabela,$campos,$valores){			
		
		$NumCampos = count($campos);
		$nomecampos = "";
		$vaorcampos = "";
		
		for($a=0 ; $a < $NumCampos ; $a ++){
			if(($NumCampos - 1) == $a){
				$nomecampos .= $campos[$a];
				$vaorcampos .= "'".$valores[$a]."'";
			}else{
				$nomecampos .= $campos[$a].",";
				$vaorcampos .= "'".$valores[$a]."',";
			}
		}
		
		mysql_query("INSERT INTO $tabela ($nomecampos) VALUES ($vaorcampos)");
		
		$this -> retorno = mysql_insert_id();
		
		//$this -> retorno = "INSERT INTO $tabela ($nomecampos) VALUES ($vaorcampos)";
		
	}

	function UpdateDinamico(){			
		
		
		
	}

}

/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP



-------------------------------------
/*CÓDIGO PARA IMPLANTAR NA PÁGINA
$campos_reservados[] = 'fundo';
$campos_reservados[] = 'taxa';
$campos_reservados[] = 'button';
$campos_reservados[] = 'coop';
$campos_reservados[] = 'update';

$conteudo = new insert();
$retorno = $conteudo -> capturar_campos($HTTP_POST_VARS,$campos_reservados);

//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
$numero = strlen($retorno);						//CONTANDO A QUANTIDADE DE CARACTERS
$numero = $numero - 4;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
$retorno = str_split($retorno, $numero);		//EXPLODINDO D VARIAVEL, JA SEM A VIRGULA

// TRABALHANDO SEPARADAMENTE ESSES CAMPOS, POIS NESCESSITAM SEREM FORMATADOS ANTES DO UPDATE
$fundo = $_REQUEST['fundo'];
$taxa = $_REQUEST['taxa'] / 100;

$fundo = str_replace(".","",$fundo);
$fundo = str_replace(",",".",$fundo);

//MONTAGEM DO UPDATE
$update = "UPDATE cooperativas SET taxa = '$taxa', fundo = '$fundo', ".$retorno[0]."  WHERE id_coop = '".$coop."' " ;

mysql_query($update) or die ("Erro no update <br><br>".mysql_error());

--------------------------------------------------------------------------------------------------------------------------------------

	$campos_reservados[] = 'Enviar';
	$campos_reservados[] = 'cadastro';

	
	$conteudo = new insert();
	$conteudo -> campos_insert($HTTP_POST_VARS,$campos_reservados);
	
	$Campos = $conteudo -> campos;
	$Valores = $conteudo -> valores;
	
	
	//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
	$n_camp = strlen($Campos);						//CONTANDO A QUANTIDADE DE CARACTERS
	$n_camp = $n_camp - 1;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
	$Campos = str_split($Campos, $n_camp);		    //EXPLODINDO D VARIAVEL, JA SEM A VIRGULA
	
	//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
	$n_val = strlen($Valores);						//CONTANDO A QUANTIDADE DE CARACTERS
	$n_val = $n_val - 1;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
	$Valores = str_split($Valores, $n_val);		    //EXPLODINDO D VARIAVEL, JA SEM A VIRGULA
	
	$Query = "INSERT INTO escola ($Campos[0]) VALUES ($Valores[0])";
	
	echo $Query ;

*/
?>