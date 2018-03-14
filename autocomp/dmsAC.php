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
* Função de auxilio para exemplo, ela filtra o array
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

//Criar elementos Raíz do XML
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
	
	
	$ostring = "O cuidado em identificar pontos críticos na_revolução dos costumes nos obriga à análise_dos conhecimentos estratégicos para atingir_a excelência. Acima de tudo, é fundamental ressaltar que o_acompanhamento das preferências de consumo cumpre_um papel essencial na formulação_dos procedimentos normalmente adotados. Podemos já vislumbrar o modo pelo qual a estrutura atual da organização agrega valor ao_estabelecimento dos métodos utilizados na avaliação de resultados. 
			  No entanto, não podemos_esquecer que a competitividade nas transações comerciais possibilita uma melhor visão global das diretrizes de desenvolvimento para_o futuro. Por conseguinte, a adoção de políticas descentralizadoras maximiza as possibilidades por conta das direções preferenciais no_sentido do progresso. Nunca é demais lembrar o peso e o significado destes problemas, uma vez que o entendimento das metas propostas_causa impacto indireto na reavaliação do retorno esperado a longo prazo. A prática cotidiana prova que a percepção das dificuldades_assume importantes posições no estabelecimento do fluxo de informações. É claro que a execução dos pontos do programa obstaculiza_a apreciação da importância do investimento em reciclagem técnica. 
			  Não obstante, a consulta_aos diversos militantes oferece uma interessante oportunidade para verificação do remanejamento dos quadros funcionais._Neste sentido, a determinação clara de objetivos acarreta um processo de reformulação e modernização das condições inegavelmente_apropriadas. A certificação de metodologias que nos auxiliam a lidar com o desenvolvimento contínuo de distintas_formas de atuação pode nos levar a considerar a reestruturação dos relacionamentos verticais entre as hierarquias. 
			  Gostaria_de enfatizar que o consenso sobre a necessidade de qualificação representa uma abertura para a melhoria do orçamento setorial._Pensando mais a longo prazo, o fenômeno da Internet estimula a padronização dos paradigmas corporativos. O que temos que ter sempre_em mente é que o desafiador cenário globalizado talvez venha a ressaltar a relatividade do impacto na agilidade decisória. 
			  Assim_mesmo, a expansão dos mercados mundiais aponta para a melhoria de todos os recursos funcionais envolvidos. O incentivo ao avanço tecnológico,_assim como o julgamento imparcial das eventualidades faz parte de um processo de gerenciamento dos níveis de motivação departamental._Todavia, o novo modelo estrutural_aqui preconizado desafia a capacidade de equalização da gestão inovadora da qual fazemos parte. É_importante questionar o_quanto a valorização de fatores subjetivos garante a contribuição de um grupo importante na determinação dos índices pretendidos._O empenho em analisar a contínua expansão de nossa atividade auxilia a preparação e a composição das formas_de ação. ";

$sub = "CAPACITANDO EM AGENTE AMBIENTAL DE SAÚDE_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS I_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS II_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS III_
CAPACITANDO EM TECNICAS ADMINISTRATIVAS IV_
CAPACITANDO EM tecnicas ADMINISTRATIVAS V_
CAPACITANDO EM ASSISTÊNCIA SOCIAL_
CAPACITANDO EM AUXILIAR DE CONSULTORIO DENTARIO 10_
CAPACITANDO EM AUXILIAR DE CONSULTORIO DENTARIO 40_
CAPACITANDO EM AUXILIAR DE ENFERMAGEM DIARISTA_
CAPACITANDO EM AUXILIAR DE ENFERMAGEM PSF_
CAPACITANDO EM SERVIÇOS GERAIS_
CAPACITANDO EM AUXILIAR EDUCACIONAL_
CAPACITANDO EM COORDENAÇÃO I_
CAPACITANDO EM COORDENAÇÃO II_
CAPACITANDO EM COORDENAÇÃO III_
CAPACITANDO EM COORDENAÇÃO DE PSF_
CAPACITANDO EM COORDENAÇÃO TÉCNICA I_
CAPACITANDO EM COORDENAÇÃO TÉCNICA II_
CAPACITANDO EM COORDENAÇÃO TÉCNICA III_
CAPACITANDO EM ATENDIMENTO DENTÁRIO 40 PSF_
CAPACITANDO EM COORDENAÇÃO DE ENERMAGEM_
CAPACITANDO EM TÉCNICAS EDUCACIONAIS_
CAPACITANDO EM ATENDIMENTO DENTÁRIO 20 PSF_
CAPACITANDO EM ATENDIMENTO DENTÁRIO 20_
CAPACITANDO EM ATENDIMENTO DENTÁRIO 40_
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