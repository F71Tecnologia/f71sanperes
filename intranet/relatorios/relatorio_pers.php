<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/LogClass.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();
$ACOES = new Acoes();

$Tabelas = array(
    'acompanhamento_compra' => 'Acompanhamento Compra',
    'advogados' => 'Advogados',
    'andamento_saida_assoc' => 'Andamento Saída',
    'ano' => 'Ano',
    'apolice' => 'Apolice',
    'areas' => 'Areas',
    'atividade' => 'Atividade',
    'autonomo' => 'Autonomo',
    'avisos' => 'Avisos',
    'c_contas' => 'Contas',
    'c_despesas_gerais' => 'Despesas Gerais',
    'c_grupos' => 'c_grupos',
    'c_subgrupos' => 'c_subgrupos',
    'c_subtipos' => 'c_subtipos',
    'c_tipos' => 'c_tipos',
    'caged' => 'Caged',
    'caged_clt' => 'Caged Clt',
    'caixa' => 'Caixa',
    'caixinha' => 'Caixinha',
    'categorias' => 'Categorias',
    'cod_pais_rais' => 'Cod Pais Rais',
    'compra' => 'Compra',
    'compra_saida_assoc' => 'Compra Saida Assoc',
    'controlectps' => 'Controlectps',
    'cooperativas' => 'Cooperativas',
    'curso' => 'Função',
    'deficiencias' => 'Deficiencias',
    'dependentes' => 'Dependentes',
    'dirf' => 'DIRF',
    'doc_responsaveis' => 'Doc Responsaveis',
    'doc_tecnico_acesso' => 'Doc Tecnico Acesso',
    'documentos' => 'Documentos',
    'escala' => 'Escala',
    'escala_proc' => 'Escala Proc',
    'escolaridade' => 'Escolaridade',
    'etnias' => 'Etnias',
    'folha_autonomo' => 'Folha Autonomo',
    'folha_cooperado' => 'Folha Cooperado',
    'folhas' => 'Folhas',
    'fornecedor_site' => 'Fornecedor Site',
    'fornecedores' => 'Fornecedores',
    'fr_carro' => 'Fr Carro',
    'fr_combustivel' => 'Fr Combustivel',
    'fr_combustivel_to' => 'Fr Combustivel To',
    'fr_multa' => 'Fr Multa',
    'fr_reembolso' => 'Fr Reembolso',
    'fr_rota' => 'Fr Rota',
    'funcionario' => 'Funcionario',
    'grau_parentesco' => 'Grau Parentesco',
    'grrf' => 'grrf',
    'grupo' => 'Grupo',
    'invasores' => 'Invasores',
    'ir' => 'IR',
    'log' => 'Log',
    'log_folha' => 'Log Folha',
    'master' => 'Master',
    'modelo_documento_anexos' => 'Modelo Documento Anexos',
    'modelo_documentos' => 'Modelo Documentos',
    'municipios' => 'Municipios',
    'n_processos' => 'N Processos',
    'notas' => 'Notas',
    'notas_assoc' => 'Notas Assoc',
    'notific_doc_assoc' => 'Notific Doc Assoc',
    'notific_responsavel_assoc' => 'Notific Responsavel Assoc',
    'notificacoes' => 'Notificacoes',
    'obrigacoes' => 'Obrigacoes',
    'obrigacoes_conc_bancaria' => 'Obrigacoes Conc Bancaria',
    'obrigacoes_entregues' => 'Obrigacoes Entregues',
    'obrigacoes_modelos' => 'Obrigacoes Modelos',
    'obrigacoes_oscip' => 'Obrigacoes Oscip',
    'pagamentos' => 'Pagamentos',
    'pagamentos_especifico' => 'Pagamentos Especifico',
    'parceiros' => 'Parceiros',
    'patrimonio' => 'Patrimonio',
    'pis' => 'Pis',
    'ponto' => 'Ponto',
    'prepostos' => 'Prepostos',
    'proc_trab_andamento' => 'Proc Trab Andamento',
    'proc_trab_movimentos' => 'Proc Trab Movimentos',
    'processo' => 'Processo',
    'processo_status' => 'Processo Status',
    'processo_tipo' => 'Processo Tipo',
    'processos_interno' => 'Processos Interno',
    'processos_interno_autonomo' => 'Processos Interno Autonomo',
    'provisao' => 'Provisao',
    'rais' => 'Rais',
    'rel_comissao_avaliacao' => 'Comissao Avaliacao',
    'rescisao_config' => 'Rescisao Config',
    'rh_avaliacao' => 'Avaliacao',
    'rh_avaliacao_clt' => 'Avaliacao CLT',
    'rh_cbo' => 'CBO',
    'rh_clt' => 'CLT',
    'rh_concessionarias' => 'Concessionarias',
    'rh_documentos' => 'Documentos',
    'rh_eventos' => 'Eventos',
    'rh_ferias' => 'Ferias',
    'rh_folha' => 'Folha',
    'rh_folha_proc' => 'Folha Proc',
    'rh_horarios' => 'Horarios',
    'rh_movimentos' => 'Movimentos',
    'rh_movimentos_clt' => 'Movimentos CLT',
    'rh_movimentos_clt_efetuados' => 'Movimentos CLT Efetuados',
    'rh_movimentos_rescisao' => 'Movimentos Rescisao',
    'rh_recisao' => 'Recisao',
    'rh_rescisao_complementar' => 'Rescisao Complementar',
    'rh_rpa' => 'RPA',
    'rh_salario' => 'Salario',
    'rh_tarifas' => 'Tarifas',
    'rh_transferencias' => 'Transferencias',
    'rh_vale' => 'Vale',
    'rh_vale_protocolo' => 'Vale Protocolo',
    'rh_vale_r_relatorio' => 'Vale R Relatorio',
    'rh_vale_relatorio' => 'Vale Relatorio',
    'rh_vale_rio_card' => 'Vale Rio Card',
    'rh_vale_rio_card_recarga' => 'Vale Rio Card Recarga',
    'rh_vt_dias_uteis' => 'VT Dias Uteis',
    'rh_vt_matricula' => 'VT Matricula',
    'rh_vt_pedido' => 'VT Pedido',
    'rh_vt_relatorio' => 'VT Relatorio',
    'rh_vt_tipo_concessionaria' => 'VT Tipo Concessionaria',
    'rhempresa' => 'Empresa',
    'rhferiados' => 'Feriados',
    'rhsindicato' => 'Sindicato',
    'rhtaxas' => 'Taxas',
    'rpa_autonomo' => 'RPA Autonomo',
    'sefip' => 'Sefip',
    'tarefa' => 'Tarefa',
    'tipo_boleto' => 'Tipo Boleto',
    'tipo_contratacao' => 'Tipo Contratação',
    'tipo_doc_oscip' => 'Tipo Doc Oscip',
    'tipo_sanguineo' => 'Tipo Sanguineo',
    'tipopg' => 'Tipo Pg',
    'tipos' => 'Tipos',
    'tipos_bens' => 'Tipos Bens',
    'tipos_impostos' => 'Tipos Impostos',
    'tipos_impostos_assoc' => 'Tipos Impostos Assoc',
    'tipos_notificacoes' => 'Tipos Notificacoes',
    'tipos_pag_saida' => 'Tipos Pag Saída',
    'tipos_referencia' => 'Tipos Referência',
    'uf' => 'UF',
    'unidade' => 'Unidade',
    'upload' => 'Upload',
    'vale' => 'Vale',
    'setor' => 'Setor'
);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
function printArr($arr){
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function gravaRelatorio($nome, $id_funcionario){
    $nome = addslashes($nome);
    $insert = mysql_query("INSERT INTO relatorio_personalizado (nome, id_funcionario) VALUE ('$nome', $id_funcionario);");
    return mysql_insert_id();
    //return 0;
}

function gravaRelatorioCampos($id_relatorio_personalizado=0,$tipo,$array=array()){
    foreach ($array as $value) {
        $value = addslashes($value);
        $insert = mysql_query("INSERT INTO relatorio_personalizado_campos (id_relatorio_personalizado, tipo, valor) VALUE ($id_relatorio_personalizado,'$tipo','$value');");
    }
}

/*
 * Nome Status Admi
 */
$sqlStatusAdmi = mysql_query('SELECT especifica, codigo FROM rhstatus_admi');
while($rowStatusAdmi = mysql_fetch_assoc($sqlStatusAdmi)) {
    $arrayStatusAdmi[$rowStatusAdmi['codigo']]=$rowStatusAdmi['especifica'];
}
/*
 * Nome Status
 */
$sqlStatus = mysql_query('SELECT especifica, codigo FROM rhstatus');
while($rowStatus = mysql_fetch_assoc($sqlStatus)) {
    $arrayStatus[$rowStatus['codigo']]=$rowStatus['especifica'];
}

/*
 * Nome Escolaridade
 */
$sqlEscolaridade = mysql_query('SELECT nome, id FROM escolaridade');
while($rowEscolaridade = mysql_fetch_assoc($sqlEscolaridade)) {
    $arrayEscolaridade[$rowEscolaridade['id']] = $rowEscolaridade['nome'];
}
    
$log = new Log();

//DELETAR RELATORIO
if(isset($_REQUEST['deletar']) AND !empty($_REQUEST['deletar'])){
    if(mysql_query("DELETE FROM relatorio_personalizado WHERE id_relatorio_personalizado = {$_REQUEST['idRelatorio']} AND id_funcionario = {$usuario['id_funcionario']}")){
        mysql_query("DELETE FROM relatorio_personalizado_campos WHERE id_relatorio_personalizado = {$_REQUEST['idRelatorio']}");
    }
    exit;
}

//SALVA OS DADOS DO RELATORIO NO BANCO E REDIRECIONA PARA O RELATORIO E ZERA O ARRAY NA SESSAO
if(isset($_REQUEST['salvar'])){
    $id_relatorio = gravaRelatorio($_REQUEST['nomeRelatorio'], $usuario['id_funcionario']);
    gravaRelatorioCampos($id_relatorio, 'select', $_SESSION['dados']['select']);
    gravaRelatorioCampos($id_relatorio, 'from', $_SESSION['dados']['from']);
    gravaRelatorioCampos($id_relatorio, 'where', $_SESSION['dados']['where']);
    gravaRelatorioCampos($id_relatorio, 'order', $_SESSION['dados']['order']);
    unset($_SESSION['dados']);
    header("Location: relatorio_pers.php?etapa=3&id=".$id_relatorio);
}

//LISTA OS RELATORIOS DO USUARIO
$sqlLista = mysql_query("SELECT * FROM relatorio_personalizado WHERE id_funcionario = {$usuario['id_funcionario']} ORDER BY nome")or die(mysql_error());
$nRelatCadastrado = mysql_num_rows($sqlLista);
if($nRelatCadastrado > 0){
    $lista .= '
        <fieldset style="margin-top: 20px;">
        <legend>Relatório Personalizado Cadastrado</legend>';
    while($rowLista = mysql_fetch_assoc($sqlLista)){
        $lista .= '
        <span>
        <a class="left" style="width: 95%;" href="relatorio_pers.php?etapa=3&id='.$rowLista['id_relatorio_personalizado'].'">
            <p class="form-control controls botaoRelat link" style="text-align: left; border: 1px solid #E6E0E0; padding-left: 20px; margin: 5px 1px 5px 5px;">'.$rowLista['nome'].'</p>
        </a>
        <a class="right" style="width: 5%;" href="#">
            <p class="form-control controls botaoRelat del" style="text-align: left; border: 1px solid #E6E0E0; padding-left: 35%; padding-right: 35%; margin: 5px 5px 5px 1px;">X</p><input type="hidden" value="'.$rowLista['id_relatorio_personalizado'].'">
        </a>
        </span>';
    }
    $lista .= '
    </fieldset>';
}

//MONTA OS CHEKBOXS COM OS PROJETOS
if (!empty($_REQUEST['arq'])) {
    $count = 0;
    $selProjetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '{$_REQUEST['id']}'");
    $optProjeto = '<tr>';
    while($rowProjetos = mysql_fetch_assoc($selProjetos)){
        if($count == 6){$optProjeto .= '</tr><tr>'; $count = 0;}
        $optProjeto .= '<td class="checkbox"><input class="checkbox validate[minCheckbox[1]]" type="checkbox" name="projetosSelecionados[]" value="'.$rowProjetos[id_projeto].'"> '.utf8_encode($rowProjetos[nome]).'</td>';
        $count++;
    }
    $optProjeto .= '</tr>';
    die($optProjeto);
}

//MONTA O SELECT COM AS REGIOES
$optRegiao = getRegioes(null, null, "");

if (isset($_REQUEST['gerar']) OR isset($_REQUEST['id'])) {
    //printArr($_REQUEST);
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $infoPrincipal = $_REQUEST['infoPrincipal'];
    $tbSelecionada = $_REQUEST['tbSelecionada'];
    
    if($_REQUEST['etapa'] == 1){
        unset($Tabelas[$infoPrincipal]);
        
        /*PEGA AS TABELAS SECUNDARIAS BASEADA NOS CAMPOS ID DA TABELA PRINCIPAL*/
        $sql = mysql_query("describe $infoPrincipal");
        while($row = mysql_fetch_assoc($sql)){
            $explode = explode('_',$row[Field]); 
//            print_r($explode);
//            echo '<br>';  
            if(in_array('id', $explode)){
                $tbSelecionada[] = $row[Field];
            } 
            if($row[Field] == 'rh_horario'){
                $tbSelecionada[] = $row[Field];
            }
            if($row[Field] == 'rh_sindicato'){
                $tbSelecionada[] = $row[Field];
            }
        }
//        print_array($Tabelas);
        $count = 0;
        /*MONTA OS CHECKBOX DAS TABELAS SECUNDARIAS*/
        foreach ($Tabelas as $key => $value) {
//            echo $value . '<br>';
            $true = false;
            $sql2 = mysql_query("describe $key") or die(mysql_error());
            //while($row2 = mysql_fetch_assoc($sql2)){
            $row2 = mysql_fetch_assoc($sql2);
                //echo $key.'<br>';
                foreach ($tbSelecionada as $key2 => $value2) {
//                    print_r($tbSelecionada);
//                    echo '<br>';
//                    echo $value2.'<br>';
                    if($row2[Field] == $value2){
                        $true = true;
                    } else if($row2[Field] == 'id_horario' && $value2 == 'rh_horario') {
                        $true = true;
                    } else if($row2[Field] == 'id_sindicato' && $value2 == 'rh_sindicato') {
                        $true = true;
                    }
                }
            //}
            if($true){
                if($count == 6){$checkbox .= '</tr><tr>'; $count = 0;}
                $checkbox .= "
                    <td class='check'>
                        <div class='input-group'>
                            <label class='input-group-addon pointer' for='c$key'><input class='checkboxI' id='c$key' type='checkbox' name='tbSelecionada[]' value='$key'></label>
                            <label class='form-control pointer' for='c$key'>$value</label>
                        </div>
                    </td>";
                $count++;
            }
        }
    //MONTA AS CONDIÇÕES E OS CAMPOS DO RELATORIO
    }elseif($_REQUEST['etapa'] == 2){
        /*ARRAY COM NOME DOS COMPOS A SEREM ESCOLHIDOS*/
        //DEFINIR OS CAMPOS POR TIPO DE FUNCIONARIO
        
        $nomesColunas = array(
            'nome','nome1','nome2','nome3','nome4','nome5','nome6','tel','cpf','rg','data','data1','data2','data3','data4',
            'data5','data6','razao','contrato','apolice','gerente','area','descricao','cnpj','localidade','conta','agencia',
            'endereco','saldo','valor','rendi','descricao','estado','civil','idade','pis','uf','estado','matricula','ctps',
            'entrada','saida','semana','horas','folga','especifica','unidade','local', 'mes', 'ano', 'salbase', 'sallimpo', 
            'sallimpo_real', 'rend', 'desco', 'inss', 'imprenda', 'fgts', 'salfamilia', 'salliquido', 'mae', 'pai', 'sexo',
            'websaass', 'creche', 'noturno', 'contato', 'codigo', 'sindical', 'admi', 'clt', 'cep', 'numero', 'letra', 'curso',
            'escolaridade','instituicao', 'status', 'conta'
        );
        
        /*PEGA OS CAMPOS DAS TABELAS SELECIONADAS E VERIFICA COM O ARRAY*/
        foreach ($tbSelecionada as $key => $value) {
            if($value == 'rh_clt'){
                $radio = "
                    <div class='col-sm-3'>
                        <div class='input-group'>
                            <label class='input-group-addon pointer' for='sTodos'><input id='sTodos' type='checkbox' name='statusClt' checked value=''></label>
                            <label class='form-control input-sm pointer' for='sTodos'>Todos</label>
                        </div>
                    </div>
                    <div class='col-sm-9' style='opacity: 0;'>
                        <div class='input-group'>
                            <label class='input-group-addon pointer' for=''>&nbsp;</label>
                            <label class='form-control input-sm' for=''>&nbsp;</label>
                        </div>
                    </div>";
                foreach ($arrayStatus as $cod => $nomeStatus) {
                    $radio .= "
                        <div class='col-sm-3'>
                            <div class='input-group'>
                                <label class='input-group-addon pointer' for='s$cod'><input id='s$cod' type='checkbox' name='statusClt[]' value='$cod'></label>
                                <label class='form-control input-sm pointer' for='s$cod'>".substr($nomeStatus, 0, 27)."</label>
                            </div>
                        </div>";
                }
                
//                $radio = '
//                <input type="radio" name="statusClt" value="" checked> Todos
//                <input type="radio" name="statusClt" value="a"> Ativos
//                <input type="radio" name="statusClt" value="i"> Inativos';
            }
            $from .= '<input type="hidden" name="from[]" value="'."$value key$key".'">';
            $selectCampos .= '<optgroup class="option" style="background: #fff" label="Informações '.ucwords(str_replace('_',' ',str_replace(array('rh_','rh'),'',str_replace('curso','Função',$value)))).'">';
            $sql2 = mysql_query("describe $value");
            while($row2 = mysql_fetch_assoc($sql2)){
                
                //condição para nao pegar os campos rh_status e status_reg
                if($row2[Field] == 'rh_status' || $row2[Field] == 'status_reg'){continue;}
                
                $explode = explode('_',$row2[Field]);
                foreach ($explode as $key3 => $value3) {
//                    echo $value3.'<br>';
                    if(in_array($value3, $nomesColunas)){
                        if($value == 'projeto' AND $value3 == 'valor'){break;}
                        if($value == 'rh_clt' AND $value3 == 'unidade'){break;}
                        if($value != 'rh_clt' AND $value3 == 'status'){break;}
                        if($row2[Field] == 'id_unidade'){break;}
                        $nomeCampo = ($key != 0) ? ucwords(str_replace('_',' ',str_replace('vo_h','vô',str_replace('vo_m','vó',str_replace('curso','Função',$row2[Field]))))) : ucwords(str_replace('_',' ',str_replace('vo_h','vô',str_replace('vo_m','vó',$row2[Field]))));
                        $data_type = ($row2[Type] == 'date') ? "data-type='data'" : null;
                        $selectCampos .= '<option value="'."key$key.$row2[Field]".'" '.$data_type.'>'.$nomeCampo.'</option>';
                        $nomeTabelas .= '<input type="hidden" name="nomeTabelas[key'.$key.']" value="'.ucwords(str_replace('_',' ',$value)).'">';
                        
                        /*ADICIONANDO OPÇÃO DE MÊS E ANO A TODOS OS CAMPOS QUE POSSUEM DATE*/
                        
                       if(strpos($row2[Field], 'data_') !== false){
//                            echo $row2[Field].' ok<br>';
                            $selectCampos .= '<option value="'."key$key.$row2[Field]_mes".'">'.$nomeCampo.' Mês</option>';
                            $selectCampos .= '<option value="'."key$key.$row2[Field]_ano".'">'.$nomeCampo.' Ano</option>';
                        }
                        break;
                    }
                    
                    /*PEGA OS COMPS ID'S ENTRE AS TABELAS SELECIONADAS*/
                    if($value3 == 'id' OR $value3 == 'projeto' OR $value3 == 'regiao' OR $row2[Field] == 'rh_horario' OR $row2[Field] == 'rh_sindicato'){
                        $where .= '<input type="hidden" name="where[key'.$key.'][]" value="'.$row2[Field].'">';
                    }
                }
            }
            $selectCampos .= '</optgroup>';
        }
    //MONTA A QUERY E MONTA O RELATORIO
    }elseif($_REQUEST['etapa'] == 3){
        if(!isset($_REQUEST['id'])){
            if(!isset($_REQUEST['select'])){
                echo '<script>alert("Selecione os campos!");window.history.back();</script>';
            }
            $nomeTabelas = $_REQUEST['nomeTabelas'];
            $whereArr = $_REQUEST['where'];
//            print_array($whereArr);
            /*MONTA A CLAUSULA WHERE COM OS ID'S*/
            for($i=1; $i < count($whereArr); $i++){
//                echo "{$whereArr['key'.$i][0]} == 'id_sindicato' && {$whereArr['key0'][0]} == 'rh_horario'<br>";
//                print_array($whereArr['key0']);
                if(in_array($whereArr['key'.$i][0], $whereArr['key0'])){
                    $whereOn[$i] = "key0.".$whereArr['key'.$i][0]." = key$i.".$whereArr['key'.$i][0];
                } else if($whereArr['key'.$i][0] == 'id_horario' && in_array('rh_horario', $whereArr['key0'])) {
                    $whereOn[$i] = "key0.rh_horario = key$i.".$whereArr['key'.$i][0];
                } else if($whereArr['key'.$i][0] == 'id_sindicato' && in_array('rh_sindicato', $whereArr['key0'])) {
                    $whereOn[$i] = "key0.rh_sindicato = key$i.".$whereArr['key'.$i][0];
                }
            }
            /*MONTA A CONDIÇÃO DE REGIAO*/
            if(in_array('id_regiao', $whereArr['key0']) AND $_REQUEST[regiao] > 0){
                $where[] = "key0.id_regiao = ".$_REQUEST['regiao'];
            }else if(in_array('regiao', $whereArr['key0']) AND $_REQUEST[regiao] > 0){
                $where[] = "key0.regiao = ".$_REQUEST['regiao'];
            }
            /*PEGANDO APENAS RECISÃO COM STATUS 1*/
            if(in_array('Rh Recisao',$nomeTabelas)){
                $where[] = array_search('Rh Recisao',$nomeTabelas).'.status = 1';
            }
            /*PEGANDO APENAS EVENTOS COM STATUS 1*/
            if(in_array('Rh Eventos',$nomeTabelas)){
                $where[] = array_search('Rh Eventos',$nomeTabelas).'.status = 1';
            }
            /*PEGANDO APENAS FOLHA COM STATUS 3*/
            if(in_array('Rh Folha',$nomeTabelas)){
                $where[] = array_search('Rh Folha',$nomeTabelas).'.status = 3';
            }
            /*PEGANDO APENAS FOLHA COM STATUS 3*/
            if(in_array('Rh Ferias',$nomeTabelas)){
                $where[] = array_search('Rh Ferias',$nomeTabelas).'.status = 1';
            }
            /*VERIFICANDO STATUS DO CLT*/
            if(in_array('Rh Clt',$nomeTabelas)){
//                if($_REQUEST['statusClt'] == 'a'){
//                    $where[] = '('.array_search('Rh Clt',$nomeTabelas).'.status < 60 OR '.array_search('Rh Clt',$nomeTabelas).'.status = 70 OR '.array_search('Rh Clt',$nomeTabelas).'.status = 200)';
//                }elseif($_REQUEST['statusClt'] == 'i'){
//                    $where[] = '('.array_search('Rh Clt',$nomeTabelas).'.status >= 60 OR '.array_search('Rh Clt',$nomeTabelas).'.status = 200 OR '.array_search('Rh Clt',$nomeTabelas).'.status != 70)';
////                    $where[] = array_search('Rh Clt',$nomeTabelas).'.status NOT IN(60,61,61,63,64,65,66)';
//                }
                if(is_array($_REQUEST['statusClt'])){
                    if(implode(',', $_REQUEST['statusClt'])){
                        $where[] = array_search('Rh Clt',$nomeTabelas).'.status IN ('.implode(',', $_REQUEST['statusClt']).')';
                    }
                }
            }
            /*MONTA OS CAMPOS E O ORDER DO SELECT*/
            $order[] = 1;
            foreach ($_REQUEST['select'] as $key => $value) {

                /*PEGA O MÊS E ANO DA DATA EM FUNÇÃO DA DATA COMPLETA SALVA NO BANCO DE DADOS.*/
                if (strpos($value, '_mes') !== false) {
                    $auxDat = true;
                    $value = str_replace('_mes', '', $value);
                    //$select[$key] = "DATE_FORMAT($value, '%m') " . str_replace('key0.', '', $value) . '_mes';
                    $select[$key] = "DATE_FORMAT($value, '%m') " . str_replace('.', '_', $value) . '_mes';
                } elseif (strpos($value, '_ano') !== false) {
                    $auxDat = true;
                    $value = str_replace('_ano', '', $value);
//                    $select[$key] = "DATE_FORMAT($value, '%Y') " . str_replace('key0.', '', $value) . '_ano';                   
                    $select[$key] = "DATE_FORMAT($value, '%Y') " . str_replace('.', '_', $value) . '_ano';                   
                } elseif (strripos($value, 'data')) {
                    $select[$key] = "DATE_FORMAT($value, '%d/%m/%Y') " . str_replace('.', '_', $value);
                } else {
                    $select[$key] = $value . ' ' . str_replace('.', '_', $value);
                }
                
                if(!$aux) {
                    $order[] = $value;
                    
                }
//                echo '<pre>';
//                print_r($order);
//                echo '</pre>';
            }
//            echo '<pre>';
//                print_r($select);
//                echo '</pre>';
//            exit;
//            if(in_array('Curso',$nomeTabelas)){
//                $where[] = array_search('Curso',$nomeTabelas).".campo3 IN(". implode(', ', $_REQUEST[projetosSelecionados]).")";
//            }
            
            /*MONTA AS CONDIÇÕES ESCOLHIDAS PELO USUARIO*/
            for($i = 1; $i < count($_REQUEST['condicao']['campo']); $i++) {
                if(strripos($_REQUEST['condicao']['campo'][$i], 'valor') OR strripos($_REQUEST['condicao']['campo'][$i], 'rendi') OR strripos($_REQUEST['condicao']['campo'][$i], 'saldo')){
                    $_REQUEST['condicao']['valor'][$i] = number_format($_REQUEST['condicao']['valor'][$i], 2, '.', '');
                }
                if(strripos($_REQUEST['condicao']['campo'][$i], 'data')){
                    if(/*$_REQUEST['condicao']['valor'][$i] == '' OR */str_replace('-','',str_replace('/','',$_REQUEST['condicao']['valor'][$i])) == '00000000'){
                        $_REQUEST['condicao']['valor'][$i] = '0000-00-00';
                    }elseif($_REQUEST['condicao']['valor'][$i] != ''){
                        $_REQUEST['condicao']['valor'][$i] = converteData($_REQUEST['condicao']['valor'][$i]);
                    }
                }
                if(strripos($_REQUEST['condicao']['campo'][$i], 'mes')){
                    if(strlen($_REQUEST['condicao']['valor'][$i]) == 1){$_REQUEST['condicao']['valor'][$i] = "0{$_REQUEST['condicao']['valor'][$i]}";}
                }
                $valorCondicao = null;
                if($_REQUEST['condicao']['sinal'][$i] == '%LIKE%'){
                    $valorCondicao = explode(' ',$_REQUEST['condicao']['valor'][$i]);
                    foreach ($valorCondicao as $valueCondicao) {
                        $where[] = $_REQUEST['condicao']['campo'][$i]." LIKE '%".$valueCondicao."%'";
                    }
                }else{
                    $where[] = $_REQUEST['condicao']['campo'][$i]." ".$_REQUEST['condicao']['sinal'][$i]." '".$_REQUEST['condicao']['valor'][$i]."'";
                }
            }
            
            /*MONTA A CONDIÇÃO DE PROJETO*/
            if(in_array('id_projeto', $whereArr['key0'])){
                $select[] = "key0.id_projeto key0_id_projeto";
                $order[0] = "key0.id_projeto";
                if(is_array($_REQUEST[projetosSelecionados])){
                    $where[] = "key0.id_projeto IN(". implode(', ', $_REQUEST[projetosSelecionados]).")";
                }
                $projetoIdNome = 'id_projeto';
            }else if(in_array('projeto', $whereArr['key0']) AND is_array($_REQUEST[projetosSelecionados])){
                $select[] = "key0.projeto key0_projeto";
                $order[0] = "key0.projeto";
                if(is_array($_REQUEST[projetosSelecionados])){
                    $where[] = "key0.projeto IN(". implode(', ', $_REQUEST[projetosSelecionados]).")";
                }
                $projetoIdNome = 'projeto';
            }

            //MONTA A CLAUSULA ON DO LEFT JOIN
            foreach($whereOn AS $key4 => $value4){
                if(empty($where["key$key4"])){$_REQUEST['from'][$key4] .= " ON $value4";}
                else{$_REQUEST['from'][$key4] .= " ON $value4 AND {$where["key$key4"]}";}

            }
            
            $from = $_REQUEST['from'];
            
            //DEIXA APENAS OS 5 PRIMEIRO
            for($i=count($order)-1; $i>=5;$i--){
                unset($order[$i]);
            }
            
            //MONTA O ARRAY PARA SALVAR NO BANCO
            $arrayInsert['select'] = $select;
            $arrayInsert['from'] = $from;
            $arrayInsert['where'] = $where;
            $arrayInsert['order'] = $order;
            
            //GRAVA O ARRAY NA SESSAO
            $_SESSION['dados'] = $arrayInsert;
        } else {
            //PEGA OS DADOS DO RELATORIO NO BANCO PARA MANTA A QUERY
            $sqlRelat = mysql_query("SELECT A.* FROM relatorio_personalizado_campos A, relatorio_personalizado B WHERE A.id_relatorio_personalizado = B.id_relatorio_personalizado AND B.id_relatorio_personalizado = {$_REQUEST['id']} /*AND B.id_funcionario = {$usuario['id_funcionario']}*/ ORDER BY A.tipo, A.id_relatorio_personalizado_campos")or die(mysql_error());
            if(mysql_num_rows($sqlRelat) > 0){
                while($rowRelat = mysql_fetch_assoc($sqlRelat)){
                    if($rowRelat['tipo'] == 'select'){
                        $select[] = $rowRelat['valor'];
                    }
                    if($rowRelat['tipo'] == 'from'){
                        $from[] = $rowRelat['valor'];
                        $auxNomeTabela = explode(' ',$rowRelat['valor']);
                        $nomeTabelas[$auxNomeTabela[1]] = ucwords(str_replace('_',' ',$auxNomeTabela[0]));
                    }
                    if($rowRelat['tipo'] == 'where'){
                        $where[] = $rowRelat['valor'];
                    }
                    if($rowRelat['tipo'] == 'order'){
                        $order[] = $rowRelat['valor'];
                    }
                }
                
                //REMOVE O CAMPO DO ID DO PROJETO DO SELECT
                $_REQUEST['select'] = $select;
                $_REQUEST['select'][count($_REQUEST['select'])-1];
                $projetoIdNome = explode(' ',$_REQUEST['select'][count($_REQUEST['select'])-1]);
                $projetoIdNome = explode('.',$projetoIdNome[0]);
                $projetoIdNome = $projetoIdNome[1];
                unset($_REQUEST['select'][count($_REQUEST['select'])-1]);
            }  else {
                die("Relatório não encontrado!");
            }
        }
        
        /*JUNTA OS CAMPOS*/
        $select = implode(', ', $select);        
        $from = implode(' LEFT JOIN ', $from);
        $where = implode(' AND ', $where);
        //$where = $where["key0"];
        $order = implode(', ', $order);
        
        $where = ($where == '') ? '' : "WHERE {$where}" ;
        
        $sql = "SELECT $select FROM $from $where ORDER BY $order";
        
//        echo '<pre>'.$sql.'</pre>';
//        echo '<pre>';print_r($select);echo '</pre>';
        
        $qr_relatorio = mysql_query($sql)or die(mysql_error());
        $log->gravaLog('Relatório', "Relatorio Personalizado Gerado");
        $qtd_linhas = mysql_num_rows($qr_relatorio);
        if($qtd_linhas > 0){
            $head = '<tr>';
            
            foreach($_REQUEST['select'] as $value){
                $explodeTh = explode(' ',$value);
                
                //CASO O TH SEJA UMA COLUNA DATA MES OU ANO, FAZ O EXPLODE PARA PEGAR A COLUNA DINÂMICA.
                if(strpos($explodeTh[2], '_mes') !== false || strpos($explodeTh[2], '_ano') !== false) {
                    $explodeTh = explode('key0_',$explodeTh[2]);
                } else {
                    $explodeTh = explode('.',$explodeTh[0]);
                }
                
                $explodeTh[1] = str_replace('vo_h','vô',$explodeTh[1]);
                $explodeTh[1] = str_replace('vo_m','vó',$explodeTh[1]);
                $explodeTh[1] = ($explodeTh[0] != 'key0') ? str_replace('curso','Função',$explodeTh[1]) : $explodeTh[1];
                $nomeTabelas[$explodeTh[0]] = str_replace('Curso','Função',$nomeTabelas[$explodeTh[0]]);
                $nomeTabelas[$explodeTh[0]] = str_replace(array('Rh ', 'Rh'),'',$nomeTabelas[$explodeTh[0]]);
                $head .= '<th>'.ucwords(str_replace("'",'',str_replace(',','',str_replace('_',' ',$nomeTabelas[$explodeTh[0]].' '.$explodeTh[1])))).'</th>';
            } 
//            echo $head . '<br>';
            $head .= '</tr>';
            $projetoIdNome = 'id_projeto';
            while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                if($row_rel["key0_$projetoIdNome"]){
                    if($auxProjeto != $row_rel["key0_$projetoIdNome"]){
                        $sqlProjeto = mysql_fetch_assoc(mysql_query("SELECT nome FROM projeto WHERE id_projeto = ".$row_rel["key0_$projetoIdNome"]." LIMIT 1"));
                        $tableTd .= '<thead><tr><th colspan="'.count($_REQUEST['select']).'" style="text-align: left;">' .$sqlProjeto['nome'].'</tr></th>'.$head.'</thead>';
                        $auxProjeto = $row_rel["key0_$projetoIdNome"];
                    }
                }
                $tableTd .= '<tr>';
                foreach($row_rel as $key => $value){
//                    echo $key.'-'.$value.'<br>';
                    if($key != "key0_$projetoIdNome"){
                        if(strripos($key, 'valor')){
                            $tableTd .= '<td>'.number_format($value,2,',','.').'</td>';
                        } else if(strripos($key, 'status_admi')){
                            $tableTd .= '<td>'.$arrayStatusAdmi[$value].'</td>';
                        } else if(strripos($key, 'status') && strripos($key, 'ey0')){
                            $tableTd .= '<td>'.$arrayStatus[$value].'</td>';
                        } else if(strripos($key, 'escolaridade')){
                            $tableTd .= '<td>'.$arrayEscolaridade[$value].'</td>';
                        } else {
                            if($value == 'M') $value = 'Masculino';
                            else if($value == 'F') $value = 'Feminino';
                            else $value;
                            $tableTd .= '<td>'.$value.'</td>';
                        }
                    }
                }
                $tableTd .= '</tr>';
            }
        } else {
            $tableTd .= '<tr><td colspan="'.count($_REQUEST['select']).'">Nenhum Resultado Encontrado</tr></td>';
        }
        $sqlRegiao = mysql_fetch_assoc(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = $usuario[id_regiao] LIMIT 1"));
    }
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
}
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Personalizado</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <script>
            $(function() {
                $("#form").validationEngine();
                $('#regiao').change(function(){
                    $.post("relatorio_pers.php", {bugger:Math.random(), arq:1, id:$('#regiao').val()}, function(resultado){
                        if(resultado == '<tr></tr>'){$(".todosProjetos").hide();}else{$(".todosProjetos").show();}
                        $("#projeto").html(resultado);
                    });
                });
                $(".todos").on("click", function () {  
                    $(".checkboxI").prop("checked", this.checked);
                });
                $("#mover1").click(function(){
                    $('#select option').each(function(){
                        if($(this).prop('selected') == true){
                            //console.log($(this).parent().prop('label').split(' ').pop());
                            if($(this).parent().prop('label')){
                                $(this).html($(this).parent().prop('label').split(' ').pop()+' '+$(this).html());
                                $(this).appendTo("#select2");
                            }else{
                                $(this).appendTo("#select2");
                            }
                        }
                    });
                });
                $("#mover2").click(function(){
                    $('#select2 option').each(function(){
                        if($(this).prop('selected') == true){
                            $(this).appendTo("#select");
                        }
                    });
                });
                $("#select2 option").dblclick(function(){
                    $(this).appendTo("#select");
                });
                $("#select option").dblclick(function(){
                    if($(this).parent().prop('label')){
                        $(this).html($(this).parent().prop('label').split(' ').pop()+' '+$(this).html());
                        $(this).appendTo("#select2");
                    }else{
                        $(this).appendTo("#select2");
                    }
                });
                $("#botao").click(function(){
                    $("#condicoes").append($("#maisCond").html()+'<br>');
                });
                $('.date').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
                <?php if($_REQUEST['etapa'] == 2){ ?>
                $("#form").submit(function(){
                    $('#select2 option').each(function(){
                        $(this).prop('selected', true);
                    });
                });
                <?php } ?>
                $('.updown').click(function(){
                    var $op = $('#select2 option:selected'),
                        $this = $(this);
                    if($op.length){
                        ($this.data("key") == 'Up') ? 
                            $op.first().prev().before($op) : 
                            $op.last().next().after($op);
                    }
                });
                
                $(".del").click(function(){
                    var id = $(this).next("input").val();
                    thickBoxConfirm('Deletar Relatório','Deseja realmente deletar este relatório?','auto','auto',function(data){
                        if(data == true){
                            $.post("relatorio_pers.php", {bugger:Math.random(), deletar:'deletar', idRelatorio:id}, function(resultado){
                                //alert(resultado);
                                alert("Relatório Deletado!");
                                window.location.reload();
                            });
                        }
                    });
                });
            });
        </script>
        <style>
        @media print{
            fieldset { display: none; }
            #head { display: none; }
            img { display: none; }
            #message-box { display: none; }
            #excel { display: none; }
        }
        @page {
            margin: 1% ;
        }
        .check { width: 16.6%; }
        .condTd { width: 25%; }
        .input { width: 70px; }
        
        fieldset a:link, fieldset a:visited, fieldset a:hover, fieldset a:active { text-decoration: none; color: #000; }
        fieldset a .del:hover, fieldset a .del:active, fieldset a .del:active, fieldset a .del:visited { text-decoration: none; background-color: #F00 !important; color: #FFF; font-weight: bold; }
        
        .left { float: left; }
        .right { float: right; }
        .botaoRelat { 
            -moz-border-radius: 20px; /* Para Firefox */ 
            -webkit-border-radius: 20px; /*Para Safari e Chrome */ 
            border-radius: 20px; /* Para Opera 10.5+*/ 
        }
        .link { 
            -moz-border-top-right-radius: 0px; /* Para Firefox */ 
            -moz-border-radius-bottomright: 0px; /* Para Firefox */ 
            -webkit-border-top-right-radius: 0px; /*Para Safari e Chrome */ 
            -webkit-border-bottom-right-radius: 0px; /*Para Safari e Chrome */ 
            border-top-right-radius: 0px; /* Para Opera 10.5+*/ 
            border-bottom-right-radius: 0px; /* Para Opera 10.5+*/ 
        }
        .del {
            -moz-border-top-left-radius: 0px; /* Para Firefox */ 
            -moz-border-radius-bottomleft: 0px; /* Para Firefox */ 
            -webkit-border-top-left-radius: 0px; /*Para Safari e Chrome */ 
            -webkit-border-bottom-left-radius: 0px; /*Para Safari e Chrome */ 
            border-top-left-radius: 0px; /* Para Opera 10.5+*/ 
            border-bottom-left-radius: 0px; /* Para Opera 10.5+*/ 
            background: none !important;
            background: #FFB1B1 !important;
            color: #FFF;
        }
        body.novaintra table.grid tbody tr:nth-child(even) td { background: #ECECEC; }
        body.novaintra table.grid tbody tr:nth-child(odd) td { background: #FFFFFF; }
        .option { font-weight: bold; background-color: #ECECEC; font-size: 14px; }
        </style>
        
    </head>
    <body class="novaintra">
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="head page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório Personalizado</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                        <fieldset class="" style="display:<?php echo (!isset($_REQUEST['etapa'])) ? 'block' : 'none' ?>">
                    <legend>Relatório Personalizado</legend>
                    <?php if(!isset($_REQUEST['etapa'])){ ?>
                    <p>
                        <label for="select" class="first col-sm-2 control-label hidden-print">Informação Principal:</label> 
                        <div class="col-sm-3">
                        <select style="margin-bottom: 20px;" id="relatorio" name="infoPrincipal" class="form-control validate[required]">
                            <option value="">« Selecione a Informação Principal do Relatório »</option>
                            <option value="autonomo">Autonomo</option>
                            <option value="rh_clt">CLT</option>
                            <option value="curso">Função</option>
                            <option value="rh_eventos">Eventos</option>
                            <option value="rh_ferias">Férias</option>
                            <!--<option value="rh_folha">Folha</option>
                            <option value="rh_folha_proc">Folha Processada</option>-->
                            <option value="rh_recisao">Rescisão</option>
                            <option value="rhsindicato">Sindicatos</option>
                        </select> 
                        </div>
                    </p>
                    <?php }elseif($_REQUEST['etapa'] == 1){ ?>
                        <input type="hidden" name="tbSelecionada[]" value="<?php echo $infoPrincipal; ?>">
                    <?php }elseif($_REQUEST['etapa'] == 2){
                        echo $from.$where.$nomeTabelas; 
                    } ?>
                </fieldset>
                <fieldset class="noprint" style="display:<?php echo (isset($_REQUEST['etapa']) AND $_REQUEST['etapa'] < 3) ? 'block' : 'none' ?>">
                    <legend>Informações Secundárias</legend>
                    <?php if($_REQUEST['etapa'] == 1){ ?><p style="margin-left: 10px; color: #F90;">Selecione abaixo as informaçoes secundarias para o relatório!</p><?php } ?>
                    <table style="width: <?php echo ($_REQUEST['etapa'] == 1) ? 100 : 70; ?>%">
                    <?php if($_REQUEST['etapa'] == 1){ ?>
                    <tr>
                        <td colspan="8" class="">
                            <div class="input-group">
                                <label class="input-group-addon pointer" for="todos"><input class="todos" type="checkbox" id="todos"></label>
                                <label class="form-control pointer" for="todos">Todos</label>
                            </div>
                        </td>
                    </tr>
                    <tr><td colspan="8" class="">&nbsp;</td></tr>
                    <tr>
                        <?php echo $checkbox; ?>
                    </tr>
                    <tr><td colspan="8" class="">&nbsp;</td></tr>
                    <?php }elseif($_REQUEST['etapa'] == 2){ ?>
                    <tr>
                        <td>
                            <label style="color: #F90;">Selecione abaixo as condições de filtro para o relatório:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>Condições: <input type="button" id="botao" value="+" class="btn btn-primary"></div><br>
                            <div id="maisCond" style="display: none;">
                                <div class="col-sm-3">
                                    <select class="form-control condicao" name="condicao[campo][]"><?php echo $selectCampos; ?></select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-control" name="condicao[sinal][]">
                                        <option value="!=">DIFERENTE DE</option>
                                        <option value="=">IGUAL A</option>
                                        <option value=">">MAIOR QUE</option>
                                        <option value="<">MENOR QUE</option>
                                        <option value=">=">MAIOR IGUAL QUE</option>
                                        <option value="<=">MENOR IGUAL QUE</option>
                                        <option value="%LIKE%">PARECIDO COM</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" name="condicao[valor][]" type="text">
                                </div><br>
                            </div>
                            <div id="condicoes" style="text-align: left;">
                            </div>
                        </td>
                    </tr>
                    <tr><th>&nbsp;</th></tr>
                    <tr>
                        <td>
                            <label style="color: #F90;">Selecione abaixo a região do relatório:</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="col-sm-1 control-label hidden-print">Região:</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optRegiao, '', "id='regiao' name='regiao' class='form-control'") ?>
                            </div>
                        </td>
                    </tr>
                    <tr class="todosProjetos" style="display: none;">
                        <th>&nbsp;</th>
                    </tr>
                    <tr class="todosProjetos" style="display: none;">
                        <td>
                            <label style="color: #F90;">Selecione abaixo os projetos do relatório:</label>
                        </td>
                    </tr>
                    <tr class="todosProjetos" style="display: none;">
                        <td colspan="8"><input class="todos" type="checkbox" name="" value=""> Todos</td>
                    </tr>
                    </table>
                    <table id="projeto"></table>
                    <table>
                    <tr><th>&nbsp;</th></tr>
                    <?php if(in_array('rh_clt',$_REQUEST['tbSelecionada'])){ ?>
                    <tr>
                        <td colspan="3">
                            <label class="col-sm-12">Status Clt:</label>
                            <?php echo $radio; ?>
                        </td>
                    </tr>
                    <tr><th>&nbsp;</th></tr>
                    <?php } ?>
                    <tr>
                        <td colspan="3" class="text-bold" style="text-align: left; color: #F90;">Selecione abaixo os campos e a ordem com que eles serão mostrdos e ordenados</td>
                    </tr>
                    <tr>
                        <td style="width: 1%;"><button type="button" class="btn btn-sm btn-info" style="opacity: 0;"><i class="fa fa-dashboard"></i></button><select class="form-control" multiple="multiple" name="select" id="select" style="height: 400px; width: 250px;"><?php echo $selectCampos; ?></select></td>
                        <td style="width: 50px;">
                            <button type="button" id="mover1" class="btn btn-sm btn-info"><i class="fa fa-arrow-right"></i></button>
                            <button type="button" id="mover2" class="btn btn-sm btn-info"><i class="fa fa-arrow-left"></i></button>
<!--                            <label id="mover1" style="cursor: pointer;"><img src="../img_menu_principal/2rightarrow.png" style="width: 50px;"></label><br><br>
                            <label id="mover2" style="cursor: pointer;"><img src="../img_menu_principal/2leftarrow.png" style="width: 50px;"></label>-->
                        </td>
                        <td style="width: 98%;">
                            <button type="button" class="updown btn btn-sm btn-info" data-key="Up"><i class="fa fa-arrow-up"></i></button>
                            <button type="button" class="updown btn btn-sm btn-info" data-key="Down"><i class="fa fa-arrow-down"></i></button>
<!--                            <img class="updown" alt="Up" border="0" src="../img_menu_principal/1uparrow.png" style="width: 20px; cursor:pointer;" >&nbsp;
                            <img class="updown" alt="Down" border="0" src="../img_menu_principal/1downarrow.png" style="width: 20px; cursor:pointer;" ><br>-->
                            <select class="form-control" multiple="multiple" name="select[]" id="select2" class="validate[required]" style="height: 400px; width: 250px;"></select>
                        </td>
                    </tr>
                    <?php } ?>
                    </table>
                </fieldset>
                        <div class="panel-footer text-right hidden-print controls">
                            <fieldset class="">
                <p class="controls" >
                <div class="col-sm-1">
                    <button type="button" value="Voltar" onClick="history.go(-1)" class="btn btn-warning">Voltar</button>
                </div>
                <?php if($_REQUEST['etapa'] != 3){ ?>
                    <input type="hidden" name="etapa" value="<?php echo ( empty($_REQUEST['etapa']) ? 1 : $_REQUEST['etapa']+1 ); ?>"/>
                    <button type="submit" name="gerar" value="Próximo" id="gerar" class="btn btn-primary">Próximo</button>
                <?php }elseif($_REQUEST['etapa'] == 3){ ?>
                    <?php if(!isset($_REQUEST['id']) AND $nRelatCadastrado < 5){ ?><label class="col-sm-1 control-label hidden-print" for="input">Nome:</label> <div class="col-sm-3"><input type="text" name="nomeRelatorio" class="form-control validate[required]"></div><div class="col-sm-1"><button type="submit" name="salvar" value="Salvar Relatório" class="btn btn-success">Salvar Relatório</button></div><?php } ?>
                    <?php if(!isset($_REQUEST['id']) AND $nRelatCadastrado >= 5){ ?>Só pode ter no máximo 5 relatórios salvos<?php } ?>
                    <a href="relatorio_pers.php"><button type="button" value="Novo Relatório" class="btn btn-primary">Novo Relatório</button></a>
                <?php } ?>
                </p>
                </fieldset>
                            
                        </div>
                       </div> 
               </div>
                <?php if(!isset($_REQUEST['etapa']) AND empty($_REQUEST['id'])){ echo $lista; } ?>
                <?php if ((!empty($qr_relatorio) && isset($_REQUEST['gerar'])) OR isset($_REQUEST['id'])) { ?>
                <p id="excel" style="text-align: right; margin-top: 20px"><input class="btn btn-success" type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="table table-striped table-hover text-sm valign-middle table-bordered" width="100%" style="page-break-after:auto;"> 
                    <tbody>
                        <?php 
                        if($auxProjeto == null){$tableTd .= "<thead>$head</thead>";}
                        echo $tableTd; 
                        ?>
                    </tbody>
                </table>
                <?php } ?>
                        <?php include('../template/footer.php'); ?>
                    </div>

                    
            </form>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
         <script>
            $(function() {
                $("#form").validationEngine();
                $('#regiao').change(function(){
                    $.post("relatorio_pers.php", {bugger:Math.random(), arq:1, id:$('#regiao').val()}, function(resultado){
                        if(resultado == '<tr></tr>'){$(".todosProjetos").hide();}else{$(".todosProjetos").show();}
                        $("#projeto").html(resultado);
                    });
                });
                $(".todos").on("click", function () {  
                    $(".checkboxI").prop("checked", this.checked);
                });
                
                $('body').on('change', '.condicao', function(){
                    var $this = $(this);
                    if($this.find(':selected').data('type') == 'data'){
                        $this.parent().next().next().find('input').addClass('data').datepicker({
                            dateFormat: 'dd/mm/yy',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '2005:c+1',
                            beforeShow: function () {
                                setTimeout(function () {
                                    $('.ui-datepicker').css('z-index', 5010);
                                }, 0);
                            }
                        }).mask("99/99/9999", {placeholder: ""});
                    } else {
                        $(this).parent().next().next().find('input').removeClass('data');
                    }
                });
                
                $("#mover1").click(function(){
                    $('#select option').each(function(){
                        if($(this).prop('selected') == true){
                            //console.log($(this).parent().prop('label').split(' ').pop());
                            if($(this).parent().prop('label')){
                                $(this).html($(this).parent().prop('label').split(' ').pop()+' '+$(this).html());
                                $(this).appendTo("#select2");
                            }else{
                                $(this).appendTo("#select2");
                            }
                        }
                    });
                });
                $("#mover2").click(function(){
                    $('#select2 option').each(function(){
                        if($(this).prop('selected') == true){
                            $(this).appendTo("#select");
                        }
                    });
                });
                $("#select2 option").dblclick(function(){
                    $(this).appendTo("#select");
                });
                $("#select option").dblclick(function(){
                    if($(this).parent().prop('label')){
                        $(this).html($(this).parent().prop('label').split(' ').pop()+' '+$(this).html());
                        $(this).appendTo("#select2");
                    }else{
                        $(this).appendTo("#select2");
                    }
                });
                $("#botao").click(function(){
                    $("#condicoes").append($("#maisCond").html()+'<br>');
                });
                $('.date').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
                <?php if($_REQUEST['etapa'] == 2){ ?>
                $("#form").submit(function(){
                    $('#select2 option').each(function(){
                        $(this).prop('selected', true);
                    });
                });
                <?php } ?>
                $('.updown').click(function(){
                    var $op = $('#select2 option:selected'),
                        $this = $(this);
                    if($op.length){
                        ($this.data("key") == 'Up') ? 
                            $op.first().prev().before($op) : 
                            $op.last().next().after($op);
                    }
                });
                
                $(".del").click(function(){
                    var id = $(this).next("input").val();
                    thickBoxConfirm('Deletar Relatório','Deseja realmente deletar este relatório?','auto','auto',function(data){
                        if(data == true){
                            $.post("relatorio_pers.php", {bugger:Math.random(), deletar:'deletar', idRelatorio:id}, function(resultado){
                                //alert(resultado);
                                alert("Relatório Deletado!");
                                window.location.reload();
                            });
                        }
                    });
                });
            });
        </script>

    </body>
</html>
<!-- A -->