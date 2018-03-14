<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
//if ($_COOKIE['logado']!=39) {
//    echo '<script>alert("SISTEMA EM MANUTENÇÃO!! POR FAVOR AGUARDE OU ENTRE EM CONTATO COM A F71!")</script>';
//    
//}

if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $_REQUEST['download'];    
    $name_file_download = isset($_REQUEST['name_file']) ? $_REQUEST['name_file'] : $_REQUEST['download'];
    header("Content-Type: application/save");
    header("Content-Length:" . filesize($file));
    header('Content-Disposition: attachment; filename="' . $name_file_download . '"');
    header("Content-Transfer-Encoding: binary");
    header('Expires: 0');
    header('Pragma: no-cache');
    $fp = fopen("$file", "r");
    fpassthru($fp);
    fclose($fp);
    exit();
}

include('../../conn.php');
include('../../wfunction.php');
include('define.php');
include '../../classes/construcaoTXT.php';
include('classes/SupportVT.class.php');
include('classes/IDao.class.php');
include('classes/Dao.class.php');
include('../../classes/CalendarioClass.php');




// TRUE para deixar como padrão o cnpj do master (checkbox "Usar o CNPJ do projeto" não fica selecionado nos formulários) 
$cnpj_master_por_padrao = TRUE; 

$usuario = carregaUsuario();
$usuario['id_projeto'] = $usuario['id_regiao']; // from hell!!!

$meses = mesesArray();
$anos = anosArray();
//$anos = array('2014' => '2014', '2015' => '2015', '2016' => '2016');

$dao = new Dao();

$arr_paginas = $dao->arrayPaginas();
$pagina_ativa = '0';










//
//function calcula_datas($data_inicial, $data_final = FALSE) {
//    $calendario = new Calendario($data_inicial, $data_final);
//    $arr['data_inicial'] = $data_inicial;
//    $arr['data_final'] = $calendario->getDataFinal();
//    $arr['dias_uteis'] = count($calendario->diasUteis);
//    return $arr;
//}



if (isset($_REQUEST['acao']) && !empty($_REQUEST['acao'])) {
    header('Content-type: text/html; charset=iso-8859-1');
//    echo 'ACAO =>' . $_REQUEST['acao'] . '<br>';
    $acao = $_REQUEST['acao'];
    switch ($acao) {
        case 'get_projetos' :
            $id_regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $projetos = $dao->getProjetos($id_regiao, TRUE);
            echo json_encode($projetos);
            exit();
            break;
        case 'deletar_pedido':
            $id_vt_pedido = isset($_POST['id_vt_pedido']) ? $_POST['id_vt_pedido'] : NULL;
            echo json_encode(array('id' => $id_vt_pedido, 'status' => $dao->deletarPedido($id_vt_pedido)));
            exit();
            break;
        case 'relacao_pedido' :
            $id_vt_pedido = isset($_POST['id_vt_pedido']) ? $_POST['id_vt_pedido'] : NULL;
            $relacao = $dao->getRelacaoPedido($id_vt_pedido);
            include_once 'includes/table_0.2.php';
            exit();
            break;
        case 'relacao_folha' :
            $id_vt_pedido = isset($_POST['id_vt_pedido']) ? $_POST['id_vt_pedido'] : NULL;
            
            $res_movimento = $dao->getMovimentoVt();
            
            $relacao = $dao->getRelacaoPedido($id_vt_pedido, TRUE);
            
//            echo '<pre>';
//            print_r($relacao);
//            echo '</pre>';
            
            include_once 'includes/table_0.3.php';
            exit();
            break;
        case 'lancar_movimentos' :
            
            $id_vt_pedido = isset($_POST['id_vt_pedido']) ? $_POST['id_vt_pedido'] : NULL;
            
            $sobreescrever = isset($_POST['sobreescrever']) ? $_POST['sobreescrever'] : FALSE;
            $movimentos_lancados = $dao->checarMovimentosVtLancados($id_vt_pedido, $sobreescrever);
            
            
            /* se existir algum movimento de vt para algum clt da lista, irá printar a relação com a opção de sobreescrever */
            if(!empty($movimentos_lancados)){
                echo '<div id="box_confirmar_sobreescrita"><h3>Já existem movimentos de VALE TRANSPORTE lançados nesta competência para os seguintes funcionários! Clique em "confirmar sobrescrita" no final na relação para prosseguir e lançar os novos descontos.</h3>';
                foreach($movimentos_lancados as $row){
                    $alert['message'] = $row['id_clt'].' - '.$row['nome_clt'].' - #'.$row['id_movimento'].' - LANÇADO POR '.$row['lancado_por'].' em '.$row['lancado_em'] .' - VALOR: '.number_format($row['valor_movimento'],2,',','.');
                    include 'includes/box_message.php';
                }
                echo '<div style="text-align: right;"><input type="button" id="bt_cancelar_sobreescrever_movimentos" value="Cancelar" onclick="$(\'#box_confirmar_sobreescrita\').remove(); $(\'#bts_controller_03\').show();">';
                echo '<input type="button" id="bt_sobreescrever_movimentos" value="Confirmar Sobrescrita" onclick="if(confirm(\'Deseja realmente sobreescrever os movimentos já lançados para os descontos do pedido atual?\')){sobreescrever_movimentos('.$id_vt_pedido.');}else{return false;}"></div>';
                exit();
            }else{
                $res_insert = $dao->lancarMovimentosVt($id_vt_pedido);
                if($res_insert>0){
                    $alert['color'] = 'blue';
                    $alert['message'] = 'Movimentos Lançados com sucesso!';
                }else{
                    $alert['color'] = 'red';
                    $alert['message'] = 'Erro em Lançar Movimentos!';
                }
                include 'includes/box_message.php';
            }
            
            exit();
            break;
        case 'gerar_arquivo' :
            $id_vt_pedido = isset($_POST['id_vt_pedido']) ? $_POST['id_vt_pedido'] : NULL;
            
            $relacao = $dao->getRelacaoPedido($id_vt_pedido, TRUE);            
            $arr = $dao->gerarArquivo($relacao); 
            $box['tipo'] = 1;
            include 'includes/box_exportacao.php';
            exit();
            
            break;
        case 'table_0' :
            $id_projeto = isset($_POST['id_projeto']) ? $_POST['id_projeto'] : NULL;
            $pedidos = $dao->getPedidosAtivos($id_projeto);
            include_once 'includes/table_0.php';
            exit();
            break;
        case 'form_1' :

            $post_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : FALSE;
            $post_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
            $post_sobrescreve_cnpj = isset($_POST['sobrescreve_cnpj']) ? $_POST['sobrescreve_cnpj'] : FALSE;
            $post_ano_base = isset($_POST['ano_base']) ? $_POST['ano_base'] : FALSE;
            $post_mes_base = isset($_POST['mes_base']) ? $_POST['mes_base'] : FALSE;
            $post_data_inicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : FALSE;
            $post_data_final = isset($_POST['data_final']) ? $_POST['data_final'] : FALSE;
            $post_dias_uteis = isset($_POST['dias_uteis']) ? $_POST['dias_uteis'] : FALSE;
            
            
            if(!empty($post_projeto) && $post_projeto>0){
                
                $empresa = $dao->getEmpresaByProjeto($post_projeto);
                $post_cnpj = ($post_sobrescreve_cnpj=='true') ? $empresa['cnpj_empresa'] : $empresa['cnpj_master'];                

                $dados = array('projeto' => $post_projeto,
                    'dias_uteis' => $post_dias_uteis,
                    'transporte' => 1,
                    'mes'=>$post_mes_base,
                    'ano'=>$post_ano_base,
                    'calcular_dias'=>1);

                $arr_cls = $dao->filtrarClts($dados);

    //            echo '<pre>';
    //            print_r($arr_cls);
    //            echo '</pre>';
            }else{
                $alert['message'] = 'Nenhum projeto selecionado! Selecione um projeto!';
            }
            include_once 'includes/table_1.php';

            exit();

            break;
        case 'calcula_datas':
            
            $dia_base = isset($_POST['dia_base']) ? $_POST['dia_base'] : '01';
            $mes_base = isset($_POST['mes_base']) ? $_POST['mes_base'] : NULL;
            $ano_base = isset($_POST['ano_base']) ? $_POST['ano_base'] : NULL;
            
            $ultimo_dia = CalendarioClass::getUltimoDiaMes($mes_base,$ano_base);        
            
            $dia_base_final = isset($_POST['dia_base_final']) ? $_POST['dia_base_final'] : $ultimo_dia;
            $mes_base_final = isset($_POST['mes_base_final']) ? $_POST['mes_base_final'] : $mes_base;
            $ano_base_final = isset($_POST['ano_base_final']) ? $_POST['ano_base_final'] : $ano_base;
            
            /* CALENDÁRIO */  
            $data_calendario['inicial'] = array('ano'=> $ano_base,'mes'=>str_pad($mes_base,2,'0',STR_PAD_LEFT),'dia'=>$dia_base);
            
            $data_calendario['final'] = array('ano'=> $ano_base_final,'mes'=>str_pad($mes_base_final,2,'0',STR_PAD_LEFT),'dia'=>$dia_base_final);
            
            $dias_uteis = CalendarioClass::getDias($data_calendario['inicial'],$data_calendario['final'], TRUE);
            
            $data_calendario['total_dias_uteis'] = count($dias_uteis);     
            
            echo json_encode($data_calendario);
            exit();
            break;
        case 'reprocessar_pedido':
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $dao->reprocessarPedido($id_pedido);
            exit();
            break;
        case 'fechar_pedido' :

            $post_regiao = isset($_POST['regiao']) ? $_POST['regiao'] : FALSE;
            $post_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
            $post_cnpj = isset($_POST['cnpj']) ? $_POST['cnpj'] : FALSE;
            $post_ano_base = isset($_POST['ano_base']) ? $_POST['ano_base'] : FALSE;
            $post_mes_base = isset($_POST['mes_base']) ? $_POST['mes_base'] : FALSE;
            $post_data_inicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : FALSE;
            $post_data_final = isset($_POST['data_final']) ? $_POST['data_final'] : FALSE;
            $post_dias_uteis = isset($_POST['dias_uteis']) ? $_POST['dias_uteis'] : FALSE;


            $dados = array('projeto' => $post_projeto,
                'dias_uteis' => $post_dias_uteis,
                'transporte' => 1,
                'cnpj' => $post_cnpj,
                'ano' => $post_ano_base,
                'mes' => $post_mes_base,
                'data_inicial' => $post_data_inicial,
                'data_final' => $post_data_final,
                'calcular_dias'=>1);
            
            
            $dados['arr_cls'] = $dao->filtrarClts($dados);
            $dao->finalizarPedido($dados);

            $regioes = $dao->getRegioesFuncionario();
            
//            var_dump($post_regiao);
//            echo "\n";
//            var_dump($post_projeto);
//            echo "\n";
//            var_dump($usuario['id_regiao']);
//            exit();
            
            $projetos = $dao->getProjetos($post_regiao);
            
            $pedidos = $dao->getPedidosAtivos($post_projeto);
            $key = '0';
            include_once 'includes/item_0.php';
            
            exit();

            break;

        case 'form_2' :

            $post_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
            $post_tipo_registro = isset($_POST['tipo_registro']) ? $_POST['tipo_registro'] : FALSE;
            $post_matricula = isset($_POST['matricula']) ? $_POST['matricula'] : FALSE;
            $post_cpf = isset($_POST['cpf']) ? $_POST['cpf'] : FALSE;
            $post_nome = isset($_POST['nome']) ? $_POST['nome'] : FALSE;
            $post_data_entrada = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : FALSE;
            $post_sobrescreve_cnpj = isset($_POST['sobrescreve_cnpj']) ? $_POST['sobrescreve_cnpj'] : FALSE;
            
            $empresa = $dao->getEmpresaByProjeto($post_projeto);
            $post_cnpj = ($post_sobrescreve_cnpj=='true') ? $empresa['cnpj_empresa'] : $empresa['cnpj_master'];  

            $post_transporte = ($_POST['transporte'] === 'true') ? 1 : FALSE;

            $dias_uteis = '01';

            $dados = array('projeto' => $post_projeto,
                'transporte' => $post_transporte,
                'matricula' => $post_matricula,
                'cpf' => $post_cpf,
                'nome' => $post_nome,
                'data_entrada' => $post_data_entrada);


            $arr_cls = $dao->filtrarClts($dados);

            include_once 'includes/table_2.php';
            exit();

            break;
        case 'exportar_clts' :
            $dados['cnpj'] = isset($_POST['cnpj']) ? $_POST['cnpj'] : NULL;
            $dados['projeto'] = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $dados['transporte'] = isset($_POST['transporte']) ? $_POST['transporte'] : NULL;
            $dados['cpf'] = isset($_POST['cpf']) ? $_POST['cpf'] : NULL;
            $dados['nome'] = isset($_POST['nome']) ? $_POST['nome'] : NULL;
            $dados['matricula'] = isset($_POST['matricula']) ? $_POST['matricula'] : NULL;
            $dados['tipo_registro'] = isset($_POST['tipo_registro']) ? $_POST['tipo_registro'] : NULL;
            $dados['data_entrada'] = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : FALSE;

            $arr = $dao->exportarClts($dados, $dados['cnpj']);
            
            
            foreach($arr['arquivos'] as $k=>$row){
                $alert['message'] = '<a href="?download=' . $row['download'] . '&name_file='.$row['name_file'].'" target="_blank" >Baixa arquivo '.($k+1).'</a>';
                include 'includes/box_message.php';
            }
//            echo '<a href="?download=' . $txtfile . '" target="_blank" >Baixa arquivo</a>';
            exit();

            break;
        case 'form_3' :
            $post_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
            $post_matricula = isset($_POST['matricula']) ? $_POST['matricula'] : FALSE;
            $post_cpf = isset($_POST['cpf']) ? $_POST['cpf'] : FALSE;
            $post_nome = isset($_POST['nome']) ? $_POST['nome'] : FALSE;
            $post_data_entrada = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : FALSE;

            if ($_POST['sobrescreve_cnpj'] === 'true') {
                $empresa = $dao->getEmpresaByProjeto($post_projeto);
                $post_cnpj = $empresa['cnpj_empresa'];
            } else {
                $master = $dao->getMaster();
                $post_cnpj = $master['cnpj'];
            }

            $post_transporte = ($_POST['transporte'] === 'true') ? 1 : FALSE;

            $dados = array('projeto' => $post_projeto,
                'dias_uteis' => $dias_uteis,
                'transporte' => $post_transporte,
                'matricula' => $post_matricula,
                'cpf' => $post_cpf,
                'nome' => $post_nome,
                'data_entrada' => $post_data_entrada);

            if ($_POST['somente_novos'] === 'true') {
                $dados['somente_novos'] = TRUE;
            }

            $arr_cls = $dao->filtrarClts($dados);

            include_once 'includes/table_3.php';
            exit();

            break;
        case 'get_cursos' :
            
            if(isset($_POST['id_cbo'])){                
                echo json_encode($dao->getCursosByCBO($_POST['id_cbo'], TRUE));
                exit();
            }else{
                $id_regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : array();
                $res = $dao->getCursosByRegiao($id_regiao, TRUE);
                
            }
            
            
            $arr = array();
            foreach ($res as $key => $value) {
                $arr[] = array('chave' => $key, 'valor' => $value);
            }
            echo json_encode(array('cursos' => $arr));
            exit();
            break;
        case 'get_horarios' :
            $id_curso = isset($_POST['id_curso']) ? $_POST['id_curso'] : array();
            $res = $dao->getHorariosByCurso($id_curso, TRUE);
            $arr = array();
            foreach ($res as $key => $value) {
                $arr[] = array('chave' => $key, 'valor' => $value);
            }
            echo json_encode(array('horarios' => $arr));
            exit();
            break;
        case 'atualizar_clts' :
            $dados = isset($_POST['data']) ? $_POST['data'] : array();
            $res = $dao->atualizarClts($dados);
            echo json_encode(array('status' => $res));
            exit();
            break;
        case 'form_4' :
            $dados['id_regiao'] = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $dados['itinerario'] = isset($_POST['itinerario']) ? $_POST['itinerario'] : NULL;
            $dados['descricao'] = isset($_POST['descricao']) ? $_POST['descricao'] : NULL;
            $dados['id_concessionaria'] = isset($_POST['concessionaria']) ? $_POST['concessionaria'] : NULL;
            $dados['linha'] = isset($_POST['linha']) ? $_POST['linha'] : NULL;
            $dados['valor'] = isset($_POST['valor']) ? $_POST['valor'] : NULL;
            $res = $dao->salvarTarifa($dados);
            if ($res <= 0) {
                $alert['color'] = 'red';
                $alert['message'] = 'Erro ao cadastrar tarifa';
            } else {
                $lista_tarifas = $dao->getTarifas($dados);
                $linhas = $dao->getLinhas();
                
                $alert['color'] = 'blue';
                $alert['message'] = 'Tarifa cadastrada com sucesso!';
            }
            include_once 'includes/table_4.php';

            exit();
            break;
        case 'table_4' :
            $dados['id_regiao'] = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            
            $linhas = $dao->getLinhas();
            
            $lista_tarifas = $dao->getTarifas($dados);
            
            $lista_concessionarias = $dao->getConcessionarias($dados);
            include_once 'includes/table_4.php';
            exit();
            break;
        case 'get_cbo' :
            $id_regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : $dao->getIdRegiao();
            $arr_cbo = $dao->getCBO($id_regiao, TRUE);
            echo json_encode(array('arr_cbo' => $arr_cbo));
            exit();
            break;
        case 'get_concessionarias' :
            $id_regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : $dao->getIdRegiao();
            $dados = array('id_regiao' => $id_regiao);
            $concessionarias = $dao->arrayConcessionarias($dados);
            echo json_encode(array('concessionarias' => $concessionarias));
            exit();
            break;
        case 'atualizar_tarifas' :
            $id_regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : $dao->getIdRegiao();
            $dados = array('id_regiao' => $id_regiao);
            $concessionarias = $dao->arrayConcessionarias($dados);

            $dados = isset($_POST['data']) ? $_POST['data'] : NULL;
            $res = $dao->atualizarTarifa($dados);
            echo json_encode(array('status' => $res, 'concessionarias' => $concessionarias));
            exit();
            break;
        case 'deletar_tarifa' :
            $id_tarifa = isset($_POST['id_tarifa']) ? $_POST['id_tarifa'] : NULL;
            $res = $dao->deletarTarifa($id_tarifa);
            echo json_encode(array('status' => $res));
            exit();
            break;
        case 'form_5' :
            $dados['id_regiao'] = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $dados['tipo'] = isset($_POST['tipo']) ? $_POST['tipo'] : NULL;
            $dados['nome'] = isset($_POST['nome']) ? $_POST['nome'] : NULL;
            $res = $dao->salvarConcessionaria($dados);
            
            $arr = array();
            
            if ($res <= 0) {
                $arr['alert']['color'] = 'red';
                $arr['alert']['message'] = 'Erro ao cadastrar tarifa.';
            } else {
                $arr['alert']['color'] = 'blue';
                $arr['alert']['message'] = 'Cadastro realizado com sucesso!';
            }
            $lista_concessionarias = $dao->getConcessionarias($dados);
            
            $concessionarias = array();
            foreach($lista_concessionarias as $k=>$c){
                $concessionarias[$c['id_concessionaria']] = $c;
                $concessionarias[$c['id_concessionaria']]['tipo_concessionaria'] = utf8_encode($c['tipo_concessionaria']);
                $concessionarias[$c['id_concessionaria']]['nome_regiao'] = utf8_encode($c['nome_regiao']);
            }
            $arr['lista_concessionarias'] = $concessionarias;
            $file = 'includes/table_5.php';
            $tt = SupportVT::includeFileAjax($file, $arr);
            echo json_encode(array('table_5'=>$tt,'obj_concessionarias'=>$concessionarias));
            exit();
            
            break;
        case 'table_5' : 
            $dados['id_regiao'] = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $lista_concessionarias = $dao->getConcessionarias($dados);
            $concessionarias = array();
            foreach($lista_concessionarias as $k=>$c){
                $concessionarias[$c['id_concessionaria']] = $c;
                $concessionarias[$c['id_concessionaria']]['tipo_concessionaria'] = utf8_encode($c['tipo_concessionaria']);
                $concessionarias[$c['id_concessionaria']]['nome_regiao'] = utf8_encode($c['nome_regiao']);
            }
            $lista_concessionarias = $concessionarias;
            include_once 'includes/table_5.php';
            exit();            
            break;
        case 'deletar_concessionaria' :
            $id_concessionaria = isset($_POST['id_concessionaria']) ? $_POST['id_concessionaria'] : NULL;
            $res = $dao->deletarConcessionaria($id_concessionaria);
            echo json_encode(array('status' => $res));
            exit();
            break;
        case 'get_tipos_concessionarias' :
            $tipos_concessionarias = $dao->getTiposConcessionarias(TRUE);
            echo json_encode(array('tipos_concessionarias' => $tipos_concessionarias));
            exit();
            break;
        case 'atualizar_concessionaria' :
            $dados = isset($_POST['data']) ? $_POST['data'] : array();
            $res = $dao->atualizarConcessionarias($dados);
            $tipos_concessionarias = $dao->getTiposConcessionarias(TRUE);
            echo json_encode(array('status' => $res, 'tipos_concessionarias' => $tipos_concessionarias));
            exit();
            break;
        case 'form_6' :
            $dados_cursos['cursos'] = isset($_POST['cursos']) ? $_POST['cursos'] : array();
            $regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $cbo = isset($_POST['cbo']) ? $_POST['cbo'] : NULL;
            $curso = isset($_POST['curso']) ? $_POST['curso'] : NULL;
            $horario = isset($_POST['horario']) ? $_POST['horario'] : NULL;
            $mes = isset($_POST['mes']) ? $_POST['mes'] : NULL;
            $ano = isset($_POST['ano']) ? $_POST['ano'] : NULL;
            $dias_uteis = isset($_POST['dias_uteis']) ? $_POST['dias_uteis'] : NULL;
            $sempre = isset($_POST['sempre']) ? $_POST['sempre'] : '0';
            
            $sobreescrever = isset($_POST['sobreescrever']) ? $_POST['sobreescrever'] : '0';
            
            
            $res = array();
            
//            var_dump($dados_cursos);
            
            if($sobreescrever<1 && !empty($dados_cursos['cursos'])){
                $res = $dao->checkDiasUteis($dados_cursos, $mes, $ano, $sempre);
            }
            
            
            $dados = array();
            
            if(empty($res)){
                
                if(empty($curso)){
                
                    foreach($dados_cursos['cursos'] as $curso){
    //                    $dados[$curso]['id_regiao'] = $regiao;
                        $dados[$curso]['id_regiao'] = '0';
                        $dados[$curso]['curso'] = $curso;
                        $dados[$curso]['horario'] = $horario;
                        $dados[$curso]['mes'] = $mes;
                        $dados[$curso]['ano'] = $ano;
                        $dados[$curso]['dias_uteis'] = $dias_uteis;
                        $dados[$curso]['sempre'] = $sempre;
                    }
                }else{
                    $dados[0]['id_regiao'] = $regiao;
                    $dados[0]['curso'] = $curso;
                    $dados[0]['horario'] = $horario;
                    $dados[0]['mes'] = $mes;
                    $dados[0]['ano'] = $ano;
                    $dados[0]['dias_uteis'] = $dias_uteis;
                    $dados[0]['sempre'] = $sempre;
                }
                
                $res = $dao->salvarDiasUteis($dados);
                
                if($res){
                    $alert['color'] = 'blue';
                    $alert['message'] = 'Cadastro realizado com sucesso!';
//                    include 'includes/box_message.php';
                    
                    $arr['id_regiao'] = $regiao;
                    $lista_dias_uteis = $dao->getDiasUteisAtivos($arr);
                    include_once 'includes/table_6.php';
                    exit();
                }
            }else{
                $msg = '';
                foreach($res as $row){
                    $comp = ($row['sempre']=='1') ? ' para entrar SEMPRE. ' : ' na competência de '.str_pad($row['mes'],2,'0',STR_PAD_LEFT).'/'.$row['ano'];
                    $msg .= 'Já existem '.$row['dias_uteis'].' dias úteis lançado por '.$row['criado_por'].' em '.$row['criado_em_f'].' para a função '.$row['id_curso'].' - '.$row['nome_curso'].' '.$comp.'<br><br>';
                }
                echo '<h4>Clique no botão "Sobreescrever Dias úteis" no final da listagem para confirmar a sobreescrita.</h4>';
                $alert['color'] = 'yellow';
                $alert['message'] = $msg;
                include 'includes/box_message.php';
                echo '<input type="button" onclick="if(confirm(\'Deseja realmente sobreescrever os dias úteis?\')){form6('.str_replace('"', '\'', json_encode($dados_cursos['cursos'])).')} else{ return false; }" value="Sobreescrever Dias Úteis" />';
            }
            exit();
            
            break;
        case 'deletar_dias_uteis' :
            
            $id_dias_uteis = isset($_POST['id_dias_uteis']) ? $_POST['id_dias_uteis'] : FALSE;
            
            if($id_dias_uteis){
                $res = $dao->deletarDiasUteis($id_dias_uteis);
            }else{
                $id_clt = isset($_POST['id_clt']) ? $_POST['id_clt'] : NULL;
                $res = $dao->deletarDiasUteisClt($id_clt);
            }
            
            
           
            echo json_encode(array('status' => $res));
            exit();
            break;
        case 'table_6' :
            $dados['id_regiao'] = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $lista_dias_uteis = $dao->getDiasUteisAtivos($dados);
            include_once 'includes/table_6.php';
            exit();
            break;
        case 'form_7' :
            $post_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
            $post_matricula = isset($_POST['matricula']) ? $_POST['matricula'] : FALSE;
            $post_cpf = isset($_POST['cpf']) ? $_POST['cpf'] : FALSE;
            $post_nome = isset($_POST['nome']) ? $_POST['nome'] : FALSE;
            $post_data_entrada = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : FALSE;

            if ($_POST['sobrescreve_cnpj'] === 'true') {
                $empresa = $dao->getEmpresaByProjeto($post_projeto);
                $post_cnpj = $empresa['cnpj_empresa'];
            } else {
                $master = $dao->getMaster();
                $post_cnpj = $master['cnpj'];
            }

            $post_transporte = ($_POST['transporte'] === 'true') ? 1 : FALSE;

            $dados = array('projeto' => $post_projeto,
                'dias_uteis' => '-1',
                'transporte' => $post_transporte,
                'matricula' => $post_matricula,
                'cpf' => $post_cpf,
                'nome' => $post_nome,
                'data_entrada' => $post_data_entrada,
                'calcular_dias'=>1);

            if ($_POST['somente_novos'] === 'true') {
                $dados['somente_novos'] = TRUE;
            }
            

            $arr_cls = $dao->filtrarClts($dados, 'clt');

            include_once 'includes/table_7.php';
            exit();

            break;
        case 'salvar_dias_clt' :
            
            
            $arr = isset($_POST['data']) ? $_POST['data'] : array();
            $res = FALSE;
//            foreach($arr as $dados){
//                $dados['id_clt'] = isset($dados['id_clt']) ? $dados['id_clt'] : NULL;
//                $dados['dias_uteis'] = isset($dados['dias_uteis']) ? $dados['dias_uteis'] : NULL;
//            }
            $res = $dao->salvarDiasUteis($arr);
            
            if($res){
                $alert['color'] = 'blue';
                $alert['message'] = 'Cadastro realizado com sucesso!';
            }else{
                $alert['color'] = 'red';
                $alert['message'] = 'Erro ao cadastrar. Já existem dias úteis para este CURSO, MÊS e ANO.';
            }
            
            
            
            $post_projeto = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;
            $post_matricula = isset($_POST['matricula']) ? $_POST['matricula'] : FALSE;
            $post_cpf = isset($_POST['cpf']) ? $_POST['cpf'] : FALSE;
            $post_nome = isset($_POST['nome']) ? $_POST['nome'] : FALSE;
            $post_data_entrada = isset($_POST['data_entrada']) ? $_POST['data_entrada'] : FALSE;

            if ($_POST['sobrescreve_cnpj'] === 'true') {
              $empresa = $dao->getEmpresaByProjeto($post_projeto);
              $post_cnpj = $empresa['cnpj_empresa'];
            } else {
              $master = $dao->getMaster();
              $post_cnpj = $master['cnpj'];
            }

            $post_transporte = ($_POST['transporte'] === 'true') ? 1 : FALSE;

            $dados = array('projeto' => $post_projeto,
              'dias_uteis' => '-1',
              'transporte' => $post_transporte,
              'matricula' => $post_matricula,
              'cpf' => $post_cpf,
              'nome' => $post_nome,
              'data_entrada' => $post_data_entrada,
              'calcular_dias'=>1);

            if ($_POST['somente_novos'] === 'true') {
              $dados['somente_novos'] = TRUE;
            }

            $arr_cls = $dao->filtrarClts($dados, 'clt');

            include_once 'includes/table_7.php';
              exit();
            break;
        default:

            break;
    }
} else {
    
    $master = $dao->getMaster($usuario['id_master']);
    
    $linhas = $dao->getLinhas();
    $itinerarios = $dao->getItinerarios();

    $lista_tarifas = $dao->getTarifas();
    $tipos_cartao = $dao->getTipoCartao();
    $array_tipos_registros = $dao->getTipoRegistro();
    
    $regiao_ativa = $dao->getIdRegiao();
    
    $regioes = $dao->getRegioesFuncionario();
    $projetos = $dao->getProjetos($usuario['id_regiao']);
    
    
    //ITEM 5
    
    $lista_concessionarias = $dao->getConcessionarias();
    
    $concessionarias = array();
    foreach($lista_concessionarias as $arr_concessionarias){
        $concessionarias[$arr_concessionarias['id_concessionaria']] = $arr_concessionarias['nome'];
    }
    
    
    $tipos_concenssionarias = $dao->getTiposConcessionarias();
    
//    $pedidos = $dao->getPedidosAtivos($usuario['id_projeto']);
    $pedidos = array(); // carregado com ajax
    
//    exit('3');
    
    /* CALENDÁRIO */
    $dia_base = '01';
    $mes_base = str_pad((date('m') + 1),2,'0',STR_PAD_LEFT); //corrigir para virada de ano
    $ano_base = date('Y');    
    $ultimo_dia = CalendarioClass::getUltimoDiaMes($mes_base,$ano_base);    
    $data_calendario['inicial'] = array('ano'=> $ano_base,'mes'=>str_pad($mes_base,2,'0',STR_PAD_LEFT),'dia'=>$dia_base);
    $data_calendario['final'] = array('ano'=> $ano_base,'mes'=>str_pad($mes_base,2,'0',STR_PAD_LEFT),'dia'=>$ultimo_dia);    
    $data_calendario['total_dias_uteis'] = count(CalendarioClass::getDias($data_calendario['inicial'],$data_calendario['final'], TRUE));    
    
    
//    $arr_cbo = array();
    
    $arr_cbo = $dao->getCBO($regiao_ativa);
    
//    if(isset($_GET['dev'])){
//        echo '<pre>';
//        print_r($arr_cbo);
//        echo '</pre>';
//    }
    
    
    $cursos = $dao->getCursosByRegiao($usuario['id_regiao']);
    $chaves_cursos = array_keys($cursos);
    
    $horarios = $dao->getHorariosByCurso($chaves_cursos[1]);
    $horarios = array();
    
    
    $dados['id_regiao'] = $usuario['id_regiao'];
//    $lista_dias_uteis = $dao->getDiasUteisAtivos($dados);
    
}

?>
<html>
    <head>
        <title>:: Intranet :: VALE TRASNPORTE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../favicon.ico" rel="shortcut icon"/>
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        
        <link href="../../resources/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="/intranet/jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
        <link href="css/vale_transporte.css" rel="stylesheet" type="text/css" />
        <script src="/intranet/js/jquery-1.8.3.min.js" type="text/javascript"></script>
        
        <script src="../../resources/js/bootstrap.min.js" type="text/javascript"></script>
        
        <script src="/intranet/js/jquery.maskedinput.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/jquery.price_format.2.0.min.js"></script>
        <script src="/intranet/jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
        <script src="js/vale_transporte.js" type="text/javascript"></script>
       
    </head>
    
    <body class="novaintra" data-type="adm">
        <form method="post" id="page_controller">
            <input type="hidden" name="abashow" value="<?= $pagina_ativa ?>" id="abashow" />
            <div id="content">
                <div id="geral">
                    <div id="topo">
                        <div class="conteudoTopo">
                            <div class="imgTopo">
                                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                            </div>
                            <h2>Administração de Vale Transporte</h2>
                        </div> 
                    </div>

                    <div id="conteudo">
                        <div class="colEsq">
                            <div class="titleEsq" style="height: 33px;">Menu</div>
                            <ul>                                
                                <?php foreach ($arr_paginas as $key => $pagina) { ?>
                                    <li><a href="javascript:;" onclick="$('#abashow').val(<?= $key ?>)" data-item="<?= $key ?>" class="bt-menu <?= ($pagina_ativa == $key) ? ' aselected ' : ''; ?>"><?= $pagina; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="colDir">
                            <div>processando os dados...</div>
                            <div style="background: url(../../imagens/carregando/loading.gif) no-repeat; width: 220px; height:19px;"></div>
                            <fieldset>
                                <legend>Você está como:</legend>
                                <p>
                                    <label class="first" for="cpf_3">Empresa:</label>
                                    <input style="width: 350px;" type="text" value="<?= $master['id_master'].' - '.$master['nome'].' - '.$master['cnpj'] ?>" disabled="disabled">
                                </p>
                                <p><label class="first">Região:</label> 
                                    <?php echo montaSelect($regioes, $regiao_ativa, " id='regiao' ") ?> 
                                </p>
                            </fieldset>
                            <br>
                            <?php foreach ($arr_paginas as $key => $value) { ?>
                                <div id="item<?= $key ?>" style="display: none;" >
                                    <?php
                                    $file = 'includes/item_' . $key . '.php';
                                    if (is_file($file)) {
                                        include_once $file;
                                    } else {
                                        echo 'Erro 404. Página não encontrada!';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </body>
</html>