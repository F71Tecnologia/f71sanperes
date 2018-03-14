<?php
/* 
 * PHP-DOC - Framework RH 
 * 
 * 09/07/2015
 * 
 * Módulo de instância de FrameWork Main para o RH do sistema da F71 
 * 
 * Arquivos que Fazem parte do framework FwClass (Classes Mãe)
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
 * Padronização de acesso as classes do Framework. De um modo geral esses são os procedimentos padrões de acesso as classes e manipulação do framework
 *
 * fw->setDefault()                         Define valores Padrões para inicar todas as classes do framework
 *  
 * fw->obj->setDefault()                    Define valores Padrões para iniciar operações na classe
 * fw->obj->set[nome do método]()           Define valores em elementos da classe, nunca uma operação de calculos ou procedimentos. Usar apenas para obter valores primários.
 * fw->obj->setCalc[nome do método]()       Calcula e define valores em elementos da classe
 * fw->obj->setField[nome do campo]()       Define a inclusão de um campo extra em um método select da classe
 * 
 * fw->obj->select()                        Seleciona um conjunto de registros de uma classe que será consultada com getRow() 
 * fw->obj->selectExt()                     Seleciona um conjunto de registros de acordo com as condições definidas nesse método extendido da classe
 * fw->obj->select[nome do método]()        Seleciona um conjunto de registros de um método de forma agrupada em conjunto de dados ou array de dados
 * 
 * fw->obj->getRow()                        Carrega os valores de registros de uma classe que foi selecionada com select() ou select[nome do método]
 * fw->obj->getRowExt()                     Carrega os valores de campos extendidos criados para as propriedades da classe
 * fw->obj->get[nome do método]()           Obtem o valor de um elemento da classe ou array, nunca uma operação de calculos ou procedimentos. Usar apenas para obter valores primários.
 * fw->obj->getCalc[nome do método]()       Calcula e retorna um valor de resultado ou array
 * 
 * fw->obj->onUpdate()                      Gerador de evento na classe e todos registros relacionas (insert, update, delete)
 * 
 * fw->obj->chk[nome do método]()           Verifica alguma coisa e retorna verdadeiro ou falso
 * 
 * fw->obj->isOk()                          Verifica o status de execução do último método executado na classe
 * 
 * Obs.: Todos os métodos com excessão do obj->get[nome do método] podem ser encadeados e o status de sua situação pode ser verificada por obj->isOk()
 *       As exceções não interrompem o fluxo de execução dos dados e podem ser verificadas em $fw->obj->getAllMsgCode() ou passando um código específico para retorno
 *  
 * $this->db->
 * 
 * $this->error->
 * 
 * 1. Ao sefinir um elementro chave de uma consulta (ex: rh->Clt->setIdClt(5009)) todas as classes que possuem chave estrangeira relacionada a ele
 *    irão levar essa chave em consideração ao serem executados seus métodos (ex: rh->Ferias->setCalcInssFgtsIrrf()).
 * 2. Evite o uso de variáveis dentro das classes, procurando sempre usar a propriedade da classe para evitar inconsistência de informação e centralização
 *    dos valores.
 * 3. O deploy deverá sempre propagar as atualizações de classes para todos os clientes
 * 
 * Versão: 3.0.0000 - 28/09/2015 - Jacques - Adicionado array com todos os domínio que precisam de processamento específico na execução do framework
 * Versão: 3.0.0000 - 28/09/2015 - Jacques - Adicionado método setDefault para aplicar em todas as classes instanciadas no framework
 * Versão: 3.0.0000 - 28/10/2015 - Jacques - Adicionado método select para carga de recordset de todas as classes do framework
 * Versão: 3.0.0000 - 28/10/2015 - Jacques - Adicionado método getRow para carga de tuple de todas as classes do framework
 * Versão: 3.0.5189 - 23/12/2015 - Jacques - Adicionado um espaço na localização da string 'class ' a fim de eliminar todo conteúdo antes da declaração da classe
 * Versão: 3.0.5250 - 30/12/2015 - Jacques - Adicionado método de controle de erro de execução eval na carga das classes do framework
 * versão: 3.0.6451 - 15/01/2016 - Jacques - Adicionado o método getDominio e o constructClass
 * Versão: 3.0.6451 - 15/01/2016 - Jacques - Adicionado mais uma URL des.lagos.net para identificação do cliente em execução local
 * Versão: 3.0.6634 - 18/01/2016 - Jacques - Adicionado os métodos chkInCode e getAllMsgCode como macros de consulta as classes de erros instânciadas no framework
 * Versão: 3.0.6839 - 22/02/2016 - Jacques - Alterado no método constructClass ao invéz de utilizar na variável $dominio o domínio corrente apenas o localhost para evitar
 *                                           erros quando os DNS forem fictícios 
 * Versão: 3.0.6839 - 22/02/2016 - Jacques - O Framework foi divido em core FwClass e Variação de Framework RhClass para permitir o seu uso em diversas camadas de frames
 * Versão: 3.0.9067 - 29/04/2016 - Jacques - Adicionado método extendido para carga de nome de arquivo
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
    
    protected   $master = array(    // Vetor que define os domínios do master e seu respecitivos IDs para mapeamento da camâda específica de funções
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
    
    protected $table = array(     // Vetor que define as classes com instânciamento dinâmico
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

                        ); // Falta rhempresa porque tem dois campos com o mesmo nome no instânciamento dinâmico  

    protected function getUriExt(){
        
        return PATH_CLASS;        
            
    }
    
    protected function getFileNameExt($value){
        
        return str_replace('<value>', $value, FILE_NAME);        
            
    }
    
    
}