<?php
//CLASSE update 05.08.2009
class update{

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


}
/* ARQUIVOS EXECUTANDO ESTA ROTINA
- COOPERATIVAS/EDITCOOPERATIVA.PHP

*/
?>