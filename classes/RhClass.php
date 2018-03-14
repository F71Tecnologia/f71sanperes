<?php
/* 
 * PHP-DOC - Framework RH 
 * 
 * 09/07/2015
 * 
 * M�dulo de inst�ncia de FrameWork Main para o RH do sistema da F71 
 * 
 * Arquivos que Fazem parte do framework FwClass (Classes M�e)
 * 
 *  -> MySqlClass.php
 *  -> ErrorClass.php
 *  -> DateClass.php
 *  -> WebClass.php
 *  -> EncryptClass.php
 *  -> LibClass.php
 *  -> ConstructClass.php
 *  -> FileClass.php
 * 
 *  -> RhClass.php
 *      -> RhAutonomoClass.php
 *      -> RhBancosClass.php
 *      -> RhCltClass.php
 *      -> RhCursoClass.php
 *      -> RhDocumentosClass.php
 *      -> RhEmpresaClass.php
 *      -> RhEventosClass.php
 *      -> RhFeriasClass.php
 *      -> RhFeriasCompClass.php
 *      -> RhFeriasProgramadasClass.php
 *      -> RhFeriasItensClass.php
 *      -> RhFolhaClass.php
 *      -> RhFolhaProcClass.php
 *      -> RhFuncionarioClass.php
 *      -> RhGestaoCltClass.php
 *      -> RhMetodosClass.php
 *      -> RhMovimentosClass.php
 *      -> RhMovimentosCltClass.php
 *      -> RhMovimentosRescisaoClass.php
 *      -> RhPagamentosClass.php
 *      -> RhProcessosInternoClass.php
 *      -> RhProjetoClass.php
 *      -> RhRescisaoClass.php
 *      -> RhRescisaoConfig.php
 *      -> RhSaidaClass.php
 *      -> RhStatusClass.php
 *      -> RhTipoPgClass.php
 *      -> RhUnidadeClass.php
 *      -> RhUploadClass.php
 * 
 * Arquivos portados que chamam a classe RhClass
 *  -> intranet\rh_novaintra\ctps.php
 *  -> intranet\rh_novaintra\clt.php
 *  -> intranet\rh_novaintra\ver_clt_new.php
 *  -> intranet\rh_novaintra\bolsista.php
 *  -> intranet\relatorios\relatorio_gerencial_new.php
 *  -> (webClass) intranet\rh_novaintra\ferias\processa_ferias.php
 *  -> (webClass) intranet\rh\folha\relatorio_rescisao_2.php
 * 
 * @tutorial
 * 
 * Padroniza��o de acesso as classes do Framework. De um modo geral esses s�o os procedimentos padr�es de acesso as classes e manipula��o do framework
 *
 * fw->setDefault()                         Define valores Padr�es para inicar todas as classes do framework
 *  
 * fw->obj->setDefault()                    Define valores Padr�es para iniciar opera��es na classe
 * fw->obj->set[nome do m�todo]()           Define valores em elementos da classe, nunca uma opera��o de calculos ou procedimentos. Usar apenas para obter valores prim�rios.
 * fw->obj->setCalc[nome do m�todo]()       Calcula e define valores em elementos da classe
 * fw->obj->setField[nome do campo]()       Define a inclus�o de um campo extra em um m�todo select da classe
 * 
 * fw->obj->select()                        Seleciona um conjunto de registros de uma classe que ser� consultada com getRow() 
 * fw->obj->selectExt()                     Seleciona um conjunto de registros de acordo com as condi��es definidas nesse m�todo extendido da classe
 * fw->obj->select[nome do m�todo]()        Seleciona um conjunto de registros de um m�todo de forma agrupada em conjunto de dados ou array de dados
 * 
 * fw->obj->getRow()                        Carrega os valores de registros de uma classe que foi selecionada com select() ou select[nome do m�todo]
 * fw->obj->getRowExt()                     Carrega os valores de campos extendidos criados para as propriedades da classe
 * fw->obj->get[nome do m�todo]()           Obtem o valor de um elemento da classe ou array, nunca uma opera��o de calculos ou procedimentos. Usar apenas para obter valores prim�rios.
 * fw->obj->getCalc[nome do m�todo]()       Calcula e retorna um valor de resultado ou array
 * 
 * fw->obj->onUpdate()                      Gerador de evento na classe e todos registros relacionas (insert, update, delete)
 * 
 * fw->obj->chk[nome do m�todo]()           Verifica alguma coisa e retorna verdadeiro ou falso
 * 
 * fw->obj->isOk()                          Verifica o status de execu��o do �ltimo m�todo executado na classe
 * 
 * Obs.: Todos os m�todos com excess�o do obj->get[nome do m�todo] podem ser encadeados e o status de sua situa��o pode ser verificada por obj->isOk()
 *       As exce��es n�o interrompem o fluxo de execu��o dos dados e podem ser verificadas em $fw->obj->getAllMsgCode() ou passando um c�digo espec�fico para retorno
 *  
 * $this->db->
 * 
 * $this->error->
 * 
 * 1. Ao sefinir um elementro chave de uma consulta (ex: rh->Clt->setIdClt(5009)) todas as classes que possuem chave estrangeira relacionada a ele
 *    ir�o levar essa chave em considera��o ao serem executados seus m�todos (ex: rh->Ferias->setCalcInssFgtsIrrf()).
 * 2. Evite o uso de vari�veis dentro das classes, procurando sempre usar a propriedade da classe para evitar inconsist�ncia de informa��o e centraliza��o
 *    dos valores.
 * 3. O deploy dever� sempre propagar as atualiza��es de classes para todos os clientes
 * 
 * Vers�o: 3.0.0000 - 28/09/2015 - Jacques - Adicionado array com todos os dom�nio que precisam de processamento espec�fico na execu��o do framework
 * Vers�o: 3.0.0000 - 28/09/2015 - Jacques - Adicionado m�todo setDefault para aplicar em todas as classes instanciadas no framework
 * Vers�o: 3.0.0000 - 28/10/2015 - Jacques - Adicionado m�todo select para carga de recordset de todas as classes do framework
 * Vers�o: 3.0.0000 - 28/10/2015 - Jacques - Adicionado m�todo getRow para carga de tuple de todas as classes do framework
 * Vers�o: 3.0.5189 - 23/12/2015 - Jacques - Adicionado um espa�o na localiza��o da string 'class ' a fim de eliminar todo conte�do antes da declara��o da classe
 * Vers�o: 3.0.5250 - 30/12/2015 - Jacques - Adicionado m�todo de controle de erro de execu��o eval na carga das classes do framework
 * vers�o: 3.0.6451 - 15/01/2016 - Jacques - Adicionado o m�todo getDominio e o constructClass
 * Vers�o: 3.0.6451 - 15/01/2016 - Jacques - Adicionado mais uma URL des.lagos.net para identifica��o do cliente em execu��o local
 * Vers�o: 3.0.6634 - 18/01/2016 - Jacques - Adicionado os m�todos chkInCode e getAllMsgCode como macros de consulta as classes de erros inst�nciadas no framework
 * Vers�o: 3.0.6839 - 22/02/2016 - Jacques - Alterado no m�todo constructClass ao inv�z de utilizar na vari�vel $dominio o dom�nio corrente apenas o localhost para evitar
 *                                           erros quando os DNS forem fict�cios 
 * Vers�o: 3.0.6839 - 22/02/2016 - Jacques - O Framework foi divido em core FwClass e Varia��o de Framework RhClass para permitir o seu uso em diversas camadas de frames
 * Vers�o: 3.0.9067 - 29/04/2016 - Jacques - Adicionado m�todo extendido para carga de nome de arquivo
 * 
 * @Jacques
 *  
 * 
 */

const PATH_CLASS = "/intranet/classes/"; 
const FILE_NAME = "Rh<value>Class";

const DIA = 0;
const MES = 1;
const ANO = 2; 

const DATA_CONTRATACAO = 0;
const DATA_CONTRATACAO_FMT = 1;
const DATA_CONTRATACAO_DIA = 2;
const DATA_CONTRATACAO_MES = 3;
const DATA_CONTRATACAO_ANO = 4;
const DATA_CONTRATACAO_STATUS = 5;

const VENCIDOS = 0;
const NA_DATA = 1;
const A_VENCER = 2;

include_once('FwClass.php'); 

class RhClass extends FwClass {
    
    protected   $master = array(    // Vetor que define os dom�nios do master e seu respecitivos IDs para mapeamento da cam�da espec�fica de fun��es
                        'des.lagos.net' => 1,
                        'des.f71lagos.com' => 1,
                        'tes.lagos.net' => 1,
                        'des.lagos.net' => 1,
                        'www.f71lagos.com' => 1,
                        'f71lagos.com' => 1,
                        'des.f71idr.com' => 2,
                        'tes.f71idr.com' => 2,
                        'www.f71idr.com' => 2,
                        'f71idr.com' => 2,
                        'www.f71iabassp.com' => 3,
                        'des.f71iabassp.com' => 3,
                        'tes.iabassp.net' => 3,
                        'f71iabassp.com' => 3
                        );
    
    protected $table = array(     // Vetor que define as classes com inst�nciamento din�mico
                        'Bancos' => 'bancos',
                        'Curso' => 'curso',
                        'Clt' => 'rh_clt',
                        'Documentos' => 'rh_documentos',
                        'DocStatus' => 'rh_doc_status',
                        'Empresa' => 'rhempresa',
                        'Eventos' => 'rh_eventos',
                        'Ferias' => 'rh_ferias',
                        'FeriasItens' => 'rh_ferias_itens',
                        'FeriasProgramadas' => 'rh_ferias_programadas',
                        'Folha' => 'rh_folha',
                        'FolhaProc' => 'rh_folha_proc',
                        'Funcionario' => 'funcionario',
                        'Movimentos' => 'rh_movimentos',
                        'MovimentosClt' => 'rh_movimentos_clt',
                        'Projeto' => 'projeto',
                        'Rescisao' => 'rh_recisao',
                        'RescisaoConfig' => 'rescisao_config',
                        'Status' => 'rhstatus',
                        'TipoPg' => 'tipopg',
                        'Upload' => 'upload'

                        ); // Falta rhempresa porque tem dois campos com o mesmo nome no inst�nciamento din�mico  

    protected function getUriExt(){
        
        return PATH_CLASS;        
            
    }
    
    protected function getFileNameExt($value){
        
        return str_replace('<value>', $value, FILE_NAME);        
            
    }
    
    
}