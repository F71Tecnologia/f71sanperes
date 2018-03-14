<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

error_reporting(E_ALL);



if (isset($_REQUEST['download']) && !empty($_REQUEST['download'])) {
    $tipo = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : $_REQUEST['tipo'];
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'arquivos' . DIRECTORY_SEPARATOR . $tipo . DIRECTORY_SEPARATOR . $_REQUEST['download'];
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

include('../../classes/CalendarioClass.php');
include('classes/SupportValeClass.php');
include('classes/IDaoValeClass.php');
include('classes/DaoValeClass.php');

require_once '../../classes/Spreadsheet/Excel/Writer.php';
require_once '../../classes/Spreadsheet/Excel/Reader/OLERead.php';
include('../../classes/ExcelClass.php');

require_once '../../classes/PHPExcel/PHPExcel.php';
include '../../classes/PHPExcel/PHPExcel/IOFactory.php';

$usuario = carregaUsuario();
$usuario['id_projeto'] = $usuario['id_regiao']; // from hell!!!

$tipo_vale = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : 1; // MUDAR PARA POST;


$dao = SupportValeClass::factoryVale($tipo_vale);



if (isset($_POST['acao'])) {
    header('Content-type: text/html; charset=iso-8859-1');

    $form_data = SupportValeClass::formData();

    switch ($_POST['acao']) {

        case 'salvar_dias_uteis_clt' :
            $dados['id_clt'] = isset($_POST['id_clt']) ? $_POST['id_clt'] : NULL;
            $dados['dias_uteis'] = isset($_POST['dias']) ? $_POST['dias'] : NULL;
            $competencia = isset($_POST['competencia']) ? $_POST['competencia'] : NULL;
            $data_form = isset($_POST['data_form']) ? $_POST['data_form'] : NULL;

            $arr_comp = explode('_', $competencia);

            $dados['mes'] = $arr_comp[0];
            $dados['ano'] = $arr_comp[1];

            $status = $dao->salvaDiasUteis($dados);

            if ($status) {
                $data['relacao_funcionarios'] = $dao->geraRelacaoCltPedido($form_data, FALSE);

                $data['obj_form_data'] = SupportValeClass::arrayToData($form_data);
                $data['competencia'] = $form_data['mes'] . '/' . $form_data['ano'];
                $data['data_ini_fim'] = $form_data['dataini'] . ' a ' . $form_data['datafim'];

                $file = SupportValeClass::includeFileAjax('includes/table_gerar_pedido.php', $data);
            } else {
                $file = '';
            }

            echo json_encode(array('id_clt' => $dados['id_clt'], 'dias_uteis' => $dados['dias_uteis'], 'html' => $file, 'status' => $status));
            exit();

            break;

        case 'gerar_relacao_clt_pedido' :


            $data['relacao_funcionarios'] = $dao->geraRelacaoCltPedido($form_data, FALSE);

            $data['obj_form_data'] = SupportValeClass::arrayToData($form_data);
            $data['competencia'] = $form_data['mes'] . '/' . $form_data['ano'];
            $data['data_ini_fim'] = $form_data['dataini'] . ' a ' . $form_data['datafim'];

            $file = SupportValeClass::includeFileAjax('includes/table_gerar_pedido.php', $data);
            echo json_encode(array('html' => $file));
            exit();

            break;
        case 'cria_pedido' :

            $form_data['dataini'] = SupportValeClass::dateBrToDb($form_data['dataini']);
            $form_data['datafim'] = SupportValeClass::dateBrToDb($form_data['datafim']);


            $id_pedido = $dao->geraRelacaoCltPedido($form_data, TRUE);

            $projetos = $dao->getProjetos($form_data['regiao'], FALSE, TRUE);

            if ($id_pedido > 0) {
                $data['relacao_pedidos'] = $dao->getPedidos($form_data['projeto']);
                $file = SupportValeClass::includeFileAjax('includes/table_pedidos.php', $data);
            } else {
                $file = '';
            }
            echo json_encode(array('pedido' => $id_pedido, 'projetos' => $projetos, 'regiao' => $form_data['regiao'], 'id_projeto' => $form_data['projeto'], 'html' => $file));
            exit();

            break;
        case 'carrega_pedidos' :
            $data['relacao_pedidos'] = $dao->getPedidos($form_data['projeto']);
            $file = SupportValeClass::includeFileAjax('includes/table_pedidos.php', $data);
            echo json_encode(array('html' => $file));
            exit();
            break;
        case 'relacao_clt_pedido' :

            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;

            $dados['relacao'] = $dao->verRelacaoCltPedido($id_pedido);

            $dados['info'] = SupportValeClass::getInfoPedidoByRelacao($dados['relacao']);

            $exportar_pedido = isset($_POST['exportar_pedido']) ? $_POST['exportar_pedido'] : FALSE;

            if ($exportar_pedido) {


                $dados['data_entrega'] = isset($_POST['data_entrega']) ? $_POST['data_entrega'] : FALSE;
                $dados['data_credito'] = isset($_POST['data_credito']) ? $_POST['data_credito'] : FALSE;

//                $dados = array();
//                $dados['num'] = DaoValeSodexoClass::campoNumerico('1', 99, 0);
//                $dados['str'] = DaoValeSodexoClass::campoTexto('A', '*', 40);
//                $dados['arquivos'][] = $dao->exportaPedido($dados['relacao']);

                $file_d = $dao->exportaPedido($dados);


                $file = array();
                $file['tipo'] = $file_d['tipo'];
                $file['download'] = $file_d['download'];
                $file['name_file'] = $file_d['name_file'];


//                $file =  SupportValeClass::debugPrint($dados,1);
            } else {
                $file = SupportValeClass::includeFileAjax('includes/table_clt_pedido.php', $dados);
            }

//            $file = utf8_encode($file);

            echo json_encode(array('html' => $file));
            exit();
            break;
        case 'deletar_pedido' :
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $status = $dao->deletarPedido($id_pedido);
            echo json_encode(array('status' => $status));
            exit();
            break;


        case 'form2' :

            $data['obj_form_data'] = SupportValeClass::arrayToData($form_data);
            $data['relacao_funcionarios'] = $dao->getFuncionariosByProjeto($form_data);
            $data['obj_relacao_tarifas'] = $dao->getValoresDiarios($form_data['regiao'], TRUE);

            $data['debug'] = SupportValeClass::debugPrint($data['relacao_funcionarios']);

            $file = SupportValeClass::includeFileAjax('includes/table_gerenciar_funcionario.php', $data);
            echo json_encode(array('html' => $file));

//            include_once 'includes/table_2.php';
            exit();
            break;
        case 'form4' :

            $relacao_funcionarios = $dao->getFuncionariosByProjeto($form_data);
            $obj_relacao_tarifas = $dao->getValoresDiarios($form_data['regiao'], TRUE);
            include_once 'includes/table_4.php';
            exit();
            break;

        case 'atualiza_clt_tarifa' :

            $status = ($dao->salvaCltValorDiario($form_data['tarifas']) && $dao->salvaCltMatricula($form_data['matriculas'])) ? TRUE : FALSE;

            if ($status) {
                $alert['color'] = 'green';
                $alert['message'] = 'Dados atualizados com sucesso!';
            } else {
                $alert['color'] = 'red';
                $alert['message'] = 'Erro na atualização.';
            }

            echo json_encode(array('status' => $status));

            exit();
            break;


        case 'get_projetos' :
            $id_regiao = isset($_POST['id_regiao']) ? $_POST['id_regiao'] : NULL;
            $projetos = $dao->getProjetos($id_regiao, FALSE, TRUE);
            echo json_encode($projetos);
            exit();
            break;
        case 'calcula_datas':

            $dia_base = isset($_POST['dia_base']) ? str_pad($_POST['dia_base'], 2, '0', STR_PAD_LEFT) : '01';
            $mes_base = isset($_POST['mes_base']) ? str_pad($_POST['mes_base'], 2, '0', STR_PAD_LEFT) : NULL;
            $ano_base = isset($_POST['ano_base']) ? $_POST['ano_base'] : NULL;

            $dia_base_final = isset($_POST['dia_base_final']) ? str_pad($_POST['dia_base_final'], 2, '0', STR_PAD_LEFT) : CalendarioClass::getUltimoDiaMes($mes_base, $ano_base);
            $mes_base_final = isset($_POST['mes_base_final']) ? str_pad($_POST['mes_base_final'], 2, '0', STR_PAD_LEFT) : $mes_base;
            $ano_base_final = isset($_POST['ano_base_final']) ? $_POST['ano_base_final'] : $ano_base;

            $arr_data_conf = array('dia' => $dia_base, 'mes' => $mes_base, 'ano' => $ano_base);
            $arr_data_final_conf = array('dia' => $dia_base_final, 'mes' => $mes_base_final, 'ano' => $ano_base_final);


            CalendarioClass::carrega($arr_data_conf, $arr_data_final_conf, TRUE);

            /* CALENDÁRIO */
            $data_calendario['inicial'] = CalendarioClass::getDataInicial();

            $data_calendario['final'] = CalendarioClass::getDataFinal();

            $data_calendario['total_dias_uteis'] = CalendarioClass::getTotalDiasUteis();

            echo json_encode($data_calendario);
            exit();
            break;
        case 'get_valores_diarios' : //get_table_3
            $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $relacao_tarifas = $dao->getValoresDiarios($regiao);
            include_once 'includes/table_3.php';
            exit();
            break;
        case 'cadastrar_valor_diario' :
            $form_data['valor'] = SupportValeClass::limpaMascaraMoney($form_data['valor']);

            $form_data['regiao'] = isset($_POST['regiao']) ? $_POST['regiao'] : FALSE;


            $resp = $dao->salvaValorDiario($form_data);

            $relacao_tarifas = $dao->getValoresDiarios($form_data['regiao']);
            include_once 'includes/table_3.php';

            exit();
            break;
        case 'atualizar_valor_diario' :

            $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;

            $form_data_valores = array();

//             foreach($form_data as $k=>$valor){
//                 $form_data_valores[$k]['valor'] = SupportValeClass::limpaMascaraMoney($valor);
//                 $form_data_valores[$k]['id'] = $k;
//             }
            foreach ($form_data['tarifas'] as $k => $valor) {
                $form_data_valores[$k]['valor'] = SupportValeClass::limpaMascaraMoney($valor);
                $form_data_valores[$k]['id'] = $k;
                $resp_status = $dao->atualizaValorDiario($form_data_valores);
            }

//            $resp_status = $dao->atualizaValorDiario($form_data_valores);

            $relacao_tarifas = $dao->getValoresDiarios($regiao);

            $dados = array();
            $dados['relacao_tarifas'] = $relacao_tarifas;

            $relacao = SupportValeClass::includeFileAjax('includes/table_3.php', $dados);


            $dados = array();
            if ($resp_status) {
                $dados['alert']['color'] = 'blue';
                $dados['alert']['message'] = 'Dados atualizados com sucesso!';
            } else {
                $dados['alert']['color'] = 'red';
                $dados['alert']['message'] = 'Erro na atualização.';
            }

            $msg = SupportValeClass::includeFileAjax('includes/box_message.php', $dados);



            echo json_encode(array('status' => TRUE, 'relacao' => $relacao . '<br>' . $msg));

            exit();
            break;

        case 'exclui_valor_diario' :
            $id = isset($_POST['id']) ? $_POST['id'] : NULL;
            echo json_encode(array('status' => $dao->excluiValorDiario($id)));
            exit();
            break;

        case 'fechar_pedido' :

            $dados['projeto'] = isset($_POST['id_projeto']) ? $_POST['id_projeto'] : NULL;
            $dados['ano'] = isset($_POST['ano_pedido']) ? $_POST['ano_pedido'] : NULL;
            $dados['mes'] = isset($_POST['mes_pedido']) ? $_POST['mes_pedido'] : NULL;
            $dados['data_inicial'] = isset($_POST['data_inicial_pedido']) ? $_POST['data_inicial_pedido'] : NULL;
            $dados['data_final'] = isset($_POST['data_final_pedido']) ? $_POST['data_final_pedido'] : NULL;
            $dados['dias_uteis'] = isset($_POST['dias_uteis_pedido']) ? $_POST['dias_uteis_pedido'] : NULL;
            $resp = relacaoCltPedido($dados, TRUE);
            $relacao_pedidos = getPedidos();
            include_once 'includes/table_pedidos.php';
            exit();
            break;
            ;

        case 'visualizar_pedido' :
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $relacao_funcionarios = get_relacaoCltPedido($id_pedido);
            $relacao_clt_movimento = TRUE;
            include_once 'includes/table_1.php';
            exit();
            break;
        case 'arquivo_aelo' :
            $id_pedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : NULL;
            $relacao_funcionarios = get_relacaoCltPedido($id_pedido);

            $arquivo = criar_csv_aelo($relacao_funcionarios);
            echo $arquivo;
//            $relacao_clt_movimento = TRUE;
//            include_once 'includes/table_aelo.php';
            exit();
            break;

        case 'salva_2' :

            $dados = isset($_POST['dados']) ? $_POST['dados'] : array();


            if ($dao->salvaCltValorDiario($dados)) {
                $alert['color'] = 'green';
                $alert['message'] = 'Dados atualizados com sucesso!';
            } else {
                $alert['color'] = 'red';
                $alert['message'] = 'Erro na atualização.';
            }
            include 'includes/box_message.php';
            exit();
            break;


        case 'del_3' :
            $id = isset($_POST['id']) ? $_POST['id'] : NULL;
            $id_usuario = $usuario['id_funcionario'];
            $sql = "UPDATE rh_va_valor_diario SET `status`='0', `atualizado_por`='$id_usuario' WHERE id_va_valor_diario='$id' LIMIT 1";
            mysql_query($sql);
            echo json_encode(array('status' => TRUE));
            exit();
            break;

        case 'salva_3' :
            $id = isset($_POST['id']) ? $_POST['id'] : NULL;
            $regiao = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $dados = isset($_POST['dados']) ? $_POST['dados'] : NULL;
            $id_usuario = $usuario['id_funcionario'];
            foreach ($dados as $dado) {
                $valor = isset($dado['valor']) ? str_replace('R$ ', '', str_replace(',', '.', $dado['valor'])) : NULL;
                $sql = "UPDATE rh_va_valor_diario SET `valor_diario`='$valor', `atualizado_por`='$id_usuario' WHERE id_va_valor_diario='$dado[id]' LIMIT 1";
                mysql_query($sql);
            }
            $alert['message'] = 'Dados atualizados com sucesso!';
            $relacao_tarifas = getValoresDiarios($regiao);
            include_once 'includes/table_3.php';
            exit();
            break;
        case 'form4' :
            $dados['regiao'] = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $dados['projeto'] = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $dados['matricula'] = isset($_POST['matricula']) ? $_POST['matricula'] : NULL;
            $dados['cpf'] = isset($_POST['cpf']) ? $_POST['cpf'] : NULL;
            $dados['nome'] = isset($_POST['nome']) ? $_POST['nome'] : NULL;
            $dados['alimentacao'] = isset($_POST['alimentacao']) ? $_POST['alimentacao'] : NULL;
            $dados['mes'] = isset($_POST['mes']) ? $_POST['mes'] : NULL;
            $dados['ano'] = isset($_POST['ano']) ? $_POST['ano'] : NULL;

            $regiao = $dados['projeto']; // consertar


            $relacao_funcionarios = $dao->getFuncionariosByProjeto($dados);
            include_once 'includes/table_4.php';
            exit();
            break;
        case 'exportar_clt' :
            $dados['regiao'] = isset($_POST['regiao']) ? $_POST['regiao'] : NULL;
            $dados['projeto'] = isset($_POST['projeto']) ? $_POST['projeto'] : NULL;
            $dados['matricula'] = isset($_POST['matricula']) ? $_POST['matricula'] : NULL;
            $dados['cpf'] = isset($_POST['cpf']) ? $_POST['cpf'] : NULL;
            $dados['nome'] = isset($_POST['nome']) ? $_POST['nome'] : NULL;
            $dados['alimentacao'] = isset($_POST['alimentacao']) ? $_POST['alimentacao'] : NULL;
            $dados['mes'] = isset($_POST['mes']) ? $_POST['mes'] : NULL;
            $dados['ano'] = isset($_POST['ano']) ? $_POST['ano'] : NULL;

            $regiao = $dados['projeto']; // consertar


            $relacao_funcionarios = $dao->getFuncionariosByProjeto($dados);

            print_r($relacao_funcionarios);
            exit();
            break;

        default:
            break;
    }
} else {
    $ano = isset($_POST['ano']) ? $_POST['ano'] : date('Y');
    $mes = str_pad(isset($_POST['mes']) ? $_POST['mes'] : date('m'), 2, "0", STR_PAD_LEFT);


    $arr_data_conf = array('dia' => '01', 'mes' => str_pad($mes, 2, '0', STR_PAD_LEFT), 'ano' => date('Y'));
    CalendarioClass::carrega($arr_data_conf, FALSE, TRUE);

// carregando dados do wfunction
    $projetos = $dao->getProjetos($usuario['id_regiao']);
    $projetos_keys = array_keys($projetos);

    $meses = mesesArray();
    $anos = anosArray();

// carregando dados do DAO
    $arr_paginas = $dao->getItensMenu();
    $relacao_pedidos = $dao->getPedidos($projetos_keys[0]);
    $relacao_tarifas = $dao->getValoresDiarios($usuario['id_regiao']);
    $regioes = $dao->getRegioesFuncionario($usuario);
}
?>
<html>
    <head>
        <title>:: Intranet :: VALE <?php $dao->getValeNome(); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../favicon.ico" rel="shortcut icon"/>
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/jquery.price_format.2.0.min.js"></script>

        <link href="../../js/jquery-ui-1.11.2.custom/jquery-ui.css" rel="stylesheet">
        <script src="../../js/jquery-ui-1.11.2.custom/jquery-ui.js"></script>


        <link href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet">
        <!--<script src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>-->

        <script src="js/vale_alimentacao.js?<?= rand(); ?>" type="text/javascript"></script>
    </head>
    <body class="novaintra" data-type="adm">
        <form method="post" id="page_controller">
            <input type="hidden" name="abashow" value="0" id="abashow" />
            <div id="content">
                <div id="geral">
                    <div id="topo">
                        <div class="conteudoTopo">
                            <div class="imgTopo">
                                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                            </div>

<?php
$arr_cat = array('1' => 'REFEIÇÃO', '2' => 'ALIMENTAÇÃO');
?>

                            <h2 class="text-upper">VALE <?= $dao->getCatNomeVale() . ' ' . $dao->getValeNome(); ?></h2>
                        </div> 
                    </div>
                    <div id="conteudo">
                        <div class="colEsq">
                            <div class="titleEsq" style="height: 33px;">Menu</div>
                            <ul id="nav">                                
<?php foreach ($arr_paginas as $key => $pagina) { ?>
                                    <li><a href="javascript:;" onclick="$('#abashow').val(<?= $key ?>)" data-item="<?= $key ?>" class="bt-menu <?= ($pagina_ativa == $key) ? ' aselected ' : ''; ?>"><?= $pagina; ?></a></li>
<?php } ?>
                            </ul>
                        </div>
                        <div class="colDir" id="teste1">
                            <div>processando os dados...</div>
                            <div style="background: url(../../imagens/carregando/loading.gif) no-repeat; width: 220px; height:19px;"></div>
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
            <input type="hidden" id="tipo_vale" value="<?= $dao->getIdTipo(); ?>" />
            <input type="hidden" id="cat_vale" value="<?= $dao->getCatVale(); ?>" />
        </form>
        <!-- Small modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" id="modalContent">
                    <form class="form-horizontal" role="form" id="formDiasUteis" style="margin: 40px;" >
                        <!-- form_dias_uteis -->
                        <div class="form-group form_dias_uteis">
                            <label for="inputNome" class="col-sm-2 control-label">Nome</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputNome" disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group form_dias_uteis">
                            <label for="inputDias" class="col-sm-2 control-label">Dias Úteis</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputDias" >
                            </div>
                        </div>
                        <!-- form_exportar_pedido -->
                        <div class="form-group form_exportar_pedido_1" style="display: none;">
                            <label for="inputDataEntrega" class="col-sm-5 control-label">Data para Entrega</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control date_exp" id="inputDataEntrega" >
                            </div>
                        </div>
                        <div class="form-group form_exportar_pedido_1" style="display: none;">
                            <label for="inputDataCredito" class="col-sm-5 control-label">Data para Crédito</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control date_exp" id="inputDataCredito" >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="hidden" id="inputIdClt" value="">
                                <input type="hidden" id="inputCompetencia" value="">
                                <input type="button"  class="btn btn-default  form_dias_uteis" value="Gravar" onclick="gravarDiasUteis()" >
                                <input type="hidden" id="inputIdPedido" value="">
                                <input type="button"  class="btn btn-default  form_exportar_pedido_1" value="Gerar Arquivo de Exportação" onclick="exportarPedido()" >
                            </div>
                        </div>
                        <div id="din_modal" style="text-align: center;"></div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>