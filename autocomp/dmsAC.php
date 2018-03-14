<?
/**
 * Arquivo de Exemplo da classe AJAX/PHP AutoComplete
 * Example file for AJAX Powered PHP auto-complete
 *
 * @author Rafael Dohms <rafael at rafaeldohms dot com dot br>
 * @package dmsAutoComplete
 * @version 1.0
 */
 
/**
* Fun��o de auxilio para exemplo, ela filtra o array
* retornando apenas as entradas que se iniciam com
* a string recebida
* 
* Filter function used in example, it filters an array
* returning only entries starting with the given string
*
* @param item
*/
function arrfilter(&$item){
	return preg_match('/^'.$_POST['string'].'/',$item);
}

//Criar documento XML atraves de DOM
//Create XML Doc through DOM
$xmlDoc = new DOMDocument('1.0', 'utf-8');
$xmlDoc->formatOutput = true;

//Criar elementos Ra�z do XML
//Create root XML element
$root = $xmlDoc->createElement('root');
$root = $xmlDoc->appendChild($root);

/**
 * :pt-br:
 * Definir Lista (itens) a ser mostrada.
 * 
 * Neste passo podemos realizar buscas em banco de dados, filtrar arrays
 * Ou qualquer outra tarefa que retorne um resultado baseado no string
 * recebido
 * 
 * :en:
 * Define list to be returned
 * 
 * In this step we could do a database search, filter arryas or perform
 * other actions which would return a resultig list based on an input
 * string
 */
if ($_POST['string'] != ''){
	//Fazer filtro ou busca
	//Filter ou search
	//SQL, Array, etc...
	
	//$nome = $_POST['string'];
	
	//include "conn.php";
	
	//$result = mysql_query(" SELECT nome FROM curso WHERE nome LIKE '%capacitando%' LIMIT 0 , 30 ");
	//while($row = mysql_fetch_array($result)){
	//	$sub = $sub." $row[0] ";
	//}
	
	
	$ostring = "O cuidado em identificar pontos cr�ticos na_revolu��o dos costumes nos obriga � an�lise_dos conhecimentos estrat�gicos para atingir_a excel�ncia. Acima de tudo, � fundamental ressaltar que o_acompanhamento das prefer�ncias de consumo cumpre_um papel essencial na formula��o_dos procedimentos normalmente adotados. Podemos j� vislumbrar o modo pelo qual a estrutura atual da organiza��o agrega valor ao_estabelecimento dos m�todos utilizados na avalia��o de resultados. 
			  No entanto, n�o podemos_esquecer que a competitividade nas transa��es comerciais possibilita uma melhor vis�o global das diretrizes de desenvolvimento para_o futuro. Por conseguinte, a ado��o de pol�ticas descentralizadoras maximiza as possibilidades por conta das dire��es preferenciais no_sentido do progresso. Nunca � demais lembrar o peso e o significado destes problemas, uma vez que o entendimento das metas propostas_causa impacto indireto na reavalia��o do retorno esperado a longo prazo. A pr�tica cotidiana prova que a percep��o das dificuldades_assume importantes posi��es no estabelecimento do fluxo de informa��es. � claro que a execu��o dos pontos do programa obstaculiza_a aprecia��o da import�ncia do investimento em reciclagem t�cnica. 
			  N�o obstante, a consulta_aos diversos militantes oferece uma interessante oportunidade para verifica��o do remanejamento dos quadros funcionais._Neste sentido, a determina��o clara de objetivos acarreta um processo de reformula��o e moderniza��o das condi��es inegavelmente_apropriadas. A certifica��o de metodologias que nos auxiliam a lidar com o desenvolvimento cont�nuo de distintas_formas de atua��o pode nos levar a considerar a reestrutura��o dos relacionamentos verticais entre as hierarquias. 
			  Gostaria_de enfatizar que o consenso sobre a necessidade de qualifica��o representa uma abertura para a melhoria do or�amento setorial._Pensando mais a longo prazo, o fen�meno da Internet estimula a padroniza��o dos paradigmas corporativos. O que temos que ter sempre_em mente � que o desafiador cen�rio globalizado talvez venha a ressaltar a relatividade do impacto na agilidade decis�ria. 
			  Assim_mesmo, a expans�o dos mercados mundiais aponta para a melhoria de todos os recursos funcionais envolvidos. O incentivo ao avan�o tecnol�gico,_assim como o julgamento imparcial das eventualidades faz parte de um processo de gerenciamento dos n�veis de motiva��o departamental._Todavia, o novo modelo estrutural_aqui preconizado desafia a capacidade de equaliza��o da gest�o inovadora da qual fazemos parte. �_importante questionar o_quanto a valoriza��o de fatores subjetivos garante a contribui��o de um grupo importante na determina��o dos �ndices pretendidos._O empenho em analisar a cont�nua expans�o de nossa atividade auxilia a prepara��o e a composi��o das formas_de a��o. ";

$sub = "CAPACITANDO EM AGENTE AMBIENTAL DE SA�DE_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS I_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS II_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS III_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS IV_
CAPACITANDO EM tecnicas ADMINISTRATIVAS V_
CAPACITANDO EM ASSIST�NCIA SOCIAL_
CAPACITANDO EM AUXILIAR DE CONSULTORIO DENTARIO 10_
CAPACITANDO EM AUXILIAR DE CONSULTORIO DENTARIO 40_
CAPACITANDO EM AUXILIAR DE ENFERMAGEM DIARISTA_
CAPACITANDO EM AUXILIAR DE ENFERMAGEM PSF_
CAPACITANDO EM SERVI�OS GERAIS_
CAPACITANDO EM AUXILIAR EDUCACIONAL_
CAPACITANDO EM COORDENA��O I_
CAPACITANDO EM COORDENA��O II_
CAPACITANDO EM COORDENA��O III_
CAPACITANDO EM COORDENA��O DE PSF_
CAPACITANDO EM COORDENA��O T�CNICA I_
CAPACITANDO EM COORDENA��O T�CNICA II_
CAPACITANDO EM COORDENA��O T�CNICA III_
CAPACITANDO EM ATENDIMENTO DENT�RIO 40 PSF_
CAPACITANDO EM COORDENA��O DE ENERMAGEM_
CAPACITANDO EM T�CNICAS EDUCACIONAIS_
CAPACITANDO EM ATENDIMENTO DENT�RIO 20 PSF_
CAPACITANDO EM ATENDIMENTO DENT�RIO 20_
CAPACITANDO EM ATENDIMENTO DENT�RIO 40_
CAPACITANDO EM TECNICAS DE ENFERMAGEM DIARISTA_
CAPACITANDO EM TECNICAS DE ENFERMAGEM PSF_";

//$ostring = array_change_key_case($sub, CASE_LOWER);

    //$available = array_unique(explode("_",$ostring));
	
	$available = array_unique(explode("_",$sub));
	
	$results = array_filter($available,'arrfilter');
	
	//Construir elementos ITEM
	//built ITEM elements
	foreach($results as $key=>$label){
		//Cadastrar na lista
		//Add to list
		$item = $xmlDoc->createElement('item');
		$item = $root->appendChild($item);
		$item->setAttribute('id',$key);
		$item->setAttribute('label',rawurlencode($label)); 
		//rawurlencode evita problemas de charset
		//rawurlencode avoids charset problems
	}
}


//Retornar XML de resultado para AJAX
//Return XML code for AJAX Request
header("Content-type:application/xml; charset=utf-8");
echo $xmlDoc->saveXML();
?>