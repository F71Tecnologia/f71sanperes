<?php
/*
 * provisao_de_gastos
 * 
 * 00-00-0000
 * 
 * Rotina para processamento de provisão de gastos em lote
 * 
 * Versão: 1.1.0000 - 31/07/2015 - Jacques - Implementação de tabela temporária para geração de provisão de gastos com compatibilidade retroativa
 * Versão: 1.2.1505 - 18/08/2015 - Jacques - Reativando a codificação da tabela temporária para geração de provisão de gastos sem compatibilidade retroativa
 * Versão: 1.3.1671 - 25/08/2015 - Jacques - Correção de bug para variáveis que recebem POST como string passadas com cast forçado para (int) quando necessário
 * Versão: 1.3.2050 - 08/09/2015 - Jacques - Correção de bug no INSERT da rh_recisao que erradamente passei a executala apôs o if que determina a tabela rh_recisao ou rh_recisao_provisao_de_gastos. 
 *                                           A query de inserção do rescisao individual é feita através de um arquivo .txt na execução da tela 4. Adicionado
 *                                           o footer de controle de versão.
 * Versão: 1.3.2615 - 30/09/2015 - Jacques - Caso as férias proporcionais sejam de 12/12 avos, então as férias proporcionais deverão ser consideradas vencidas.
 *                                           Obs: Essa operação afeta apenas a exibição dos valores no formulário de rescisão.   
 * Versão: 1.3.2683 - 02/10/2015 - Jacques - $total_rendi adicionado ao calculo da variável $fp_valor_mes = (($salario_base_limpo + $valor_insalubridade_integral + $periculosidade_30_integral_dt + $total_rendi) / $qnt_dias_mes) * $qnt_dias_fp; //AKI
 * Versão: 1.3.3356 - 21/10/2015 - Jacques - $objMovimento->setAno(2014) alterado para date("Y") que estava setando o ano com valor fixo e estava sendo feito uma verificação do vetor $verfica_movimento para inclusão de periculosidade sobre uma função que 
 *                                           sempre retorna um vetor em qualquer condição de consulta. Alterado para a condição para especificação de campo no vetro como $verfica_movimento['id_movimento']
 * Versão: 1.3.3356 - 17/05/2017 - Jacques - Ativado o link para provisão de gastos a pedido de Sinésio
 * 
 * @author Não definido
 * 
 * 
 */

$programadores = array(179, 158, 260, 258, 275, 379);

/**
 * VERIFICAÇÃO DE SESSÃO
 */
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

/**
 * INCLUDES
 */
require_once('../../conn.php');
require_once('../../classes/global.php');
require_once('../../funcoes.php');
require_once('../../wfunction.php');

require_once('../../classes/funcionario.php');
require_once('../../classes/curso.php');
require_once('../../classes/clt.php');
require_once('../../classes/projeto.php');
require_once('../../classes/calculos.php');
require_once('../../classes/abreviacao.php');
require_once('../../classes/formato_valor.php');
require_once('../../classes/formato_data.php');
require_once('../../classes/valor_proporcional.php');
require_once('../../classes_permissoes/acoes.class.php');
require_once('../../classes/RescisaoClass.php');
require_once('../../classes/CalculoFolhaClass.php');
require_once('../../classes/CalculoRescisaoClass.php');
include_once "../../classes/LogClass.php";
require_once('../../classes/MovimentoClass.php');
require_once('../../classes/CltClass.php');
require_once('../../classes/EventoClass.php');
require_once('../../classes/ArquivoTxtBancoClass.php');
if (!include_once(ROOT_CLASS . 'RhClass.php'))
    die('Não foi possível incluir ' . ROOT_CLASS . 'RhClass.php a partir de ' . __FILE__);

/**
 * OBJETOS
 */
$usuario = carregaUsuario();
$rescisao = new Rescisao();
$objCalcFolha = new Calculo_Folha();
$objCalcRescisao = new Calculo_Rescisao();
$dadosClt = new CltClass();
$mov = new Movimentos();

$Fun = new funcionario();
$Fun->MostraUser(0);
$user = $Fun->id_funcionario;

$ACOES = new Acoes();
$Calc = new calculos();
$Curso = new tabcurso();
$Clt = new clt();
$ClasPro = new projeto();
$obj_recisao = new Rescisao();
$eventos = new Eventos();
$hoje = new DateTime();
//$rh = new RhClass();
//$rh->AddClassExt('Clt'); 

/**
 * VERIFICAÇÕES E FUNÇÕES NOJENTAS PRA CARALHU
 */
function verificaRecisao($id_clt) {
    /*
     * Verifica se já foi realizada rescisão para o funcionário
     */
    $retorno = montaQuery($table_rh_recisao, 'id_clt,nome', "id_clt = '{$id_clt}' AND status = 1");
    $clt_status = montaQuery('rh_clt', 'status', "id_clt='{$id_clt}'");
    $clt_status = $clt_status[1]['status'];
    if (isset($retorno[1]['id_clt']) && !empty($retorno[1]['id_clt']) && isset($clt_status) && !empty($clt_status)) {
        ?>
        <script type="text/javascript">
            alert('A rescisão deste funcionário já foi realizada.\nNome: ' + '<?php echo $retorno[1]['nome'] ?>');
            window.history.back();
        </script>
        <?php
        exit();
    }
}

$optTiposDispensa = $rescisao->listTiposRescisao("array");

if (empty($_REQUEST['tela'])) {
    $tela = 1;
} else {
    $tela = $_REQUEST['tela'];
}

if (isset($_REQUEST['desprocFerias'])) {
    $id_ferias = $_REQUEST['id'];

    $sql = "UPDATE rh_ferias SET status = 0, desprocessado_recisao = 1, dt_desproc_rescisao = NOW(), id_funcionario_desproc_rescisao = '{$_COOKIE['logado']}' WHERE id_ferias = $id_ferias LIMIT 1;";
    if (mysql_query($sql)) {
        echo true;
    }
    exit;
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {

    /**
     * LISTAR PARTICIPANTES AGUARDANDO DEMISSÃO DO PROJETO
     */
    if ($_REQUEST['method'] == "listaParticipantesAguardando") {

        $filtroProjeto = "";
        $filtroProjetoJoin = "";
        if ($_REQUEST['projeto'] != '-1') {
            $filtroProjeto = "AND id_projeto = {$_REQUEST['projeto']}";
            $filtroProjetoJoin = "AND A.id_projeto = {$_REQUEST['projeto']}";
        }

        if (!empty($_REQUEST['pesquisa'])) {
            $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
            foreach ($valorPesquisa as $valuePesquisa) {
                $pesquisa[] .= "nome LIKE '%" . $valuePesquisa . "%'";
            }
            $pesquisa = implode(' AND ', $pesquisa);
            $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
        }

        /**
         * AGUARDANDO DEMISSÃO
         */
        $sql = "SELECT A.id_clt, A.nome,B.nome AS projeto, A.locacao, C.nome as funcao
            FROM rh_clt AS A

            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN curso AS C ON(A.id_curso = C.id_curso)

            WHERE A.status = '200' 
            AND A.id_regiao = '{$_REQUEST['regiao']}' 
            $filtroProjetoJoin $auxPesquisa 
            ORDER BY A.nome ASC";

        $qr_aguardo = mysql_query($sql);
        if (mysql_num_rows($qr_aguardo) > 0) {
            $array_aguard = array();
            while ($rows_aguard = mysql_fetch_assoc($qr_aguardo)) {

                $regiao = $_REQUEST['regiao'];
                $clt = $rows_aguard['id_clt'];

                // Encriptografando a variável
                $link = str_replace('+', '--', encrypt("$regiao&$clt"));

                $array_aguard[$rows_aguard['id_clt']]['nome'] = utf8_encode($rows_aguard['nome']);
                $array_aguard[$rows_aguard['id_clt']]['projeto'] = utf8_encode($rows_aguard['projeto']);
                $array_aguard[$rows_aguard['id_clt']]['unidade'] = utf8_encode($rows_aguard['locacao']);
                $array_aguard[$rows_aguard['id_clt']]['funcao'] = utf8_encode($rows_aguard['funcao']);
                //$array_aguard[$rows_aguard['id_clt']]['link'] = "../../rh/recisao/recisao2.php?tela=2&enc=" . $link;
                $array_aguard[$rows_aguard['id_clt']]['link'] = "/intranet/?class=rescisao/processar&id_clt={$clt}";
                $array_aguard[$rows_aguard['id_clt']]['link2'] = "nova_rescisao.php?method=voltar_aguardando&id={$clt}&regiao={$regiao}&id_clt={$clt}";
            }
        }


        echo json_encode($array_aguard);
        exit();
    }

    /**
     * LISTAR PARTICIPANTES DESATIVADOS DO PROJETO
     */
    if ($_REQUEST['method'] == "listaParticipantes") {

        $filtroProjeto = "";
        $filtroProjetoJoin = "";
        if ($_REQUEST['projeto'] != '-1') {
            $filtroProjeto = "AND id_projeto = {$_REQUEST['projeto']}";
            $filtroProjetoJoin = "AND A.id_projeto = {$_REQUEST['projeto']}";
        }

        if (!empty($_REQUEST['pesquisa'])) {
            $valorPesquisa = explode(' ', $_REQUEST['pesquisa']);
            foreach ($valorPesquisa as $valuePesquisa) {
                $pesquisa[] .= "A.nome LIKE '%" . $valuePesquisa . "%'";
            }
            $pesquisa = implode(' AND ', $pesquisa);
            $auxPesquisa = " AND (($pesquisa) OR (CAST(matricula AS CHAR) = '{$_REQUEST['pesquisa']}') OR (REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$_REQUEST['pesquisa']}' OR cpf = '{$_REQUEST['pesquisa']}'))";
        }

        /**
         * CLTS DEMITIDOS
         */
        $competencia = trim($_REQUEST['txt_mes']) . trim($_REQUEST['txt_ano']);
        if ($_REQUEST['txt_ano'] !== '0' && $_REQUEST['txt_mes'] !== '0') {
            $data = "and date_format(B.data_demi, '%m%Y') = '{$competencia}'";
        }

        /**
         * TODOS OS MÊSES
         */
        if ($_REQUEST['txt_mes'] == 13) {
            $competencia = $_REQUEST['txt_ano'];
            $data = "and date_format(B.data_demi, '%Y') = '{$competencia}'";
        } else {
            $competencia = $_REQUEST['txt_ano'];
            $competencia_mes = sprintf("%02d", $_REQUEST['txt_mes']);
            $data = "and date_format(B.data_demi, '%Y') = '{$competencia}' and date_format(B.data_demi, '%m') = '{$competencia_mes}'";
        }

        $sql_demissao = " SELECT A.id_clt, A.nome, D.nome AS funcao, C.nome AS projeto, 
            DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demi,
            B.id_recisao, B.total_liquido

            FROM rh_clt AS A
            LEFT JOIN rh_recisao AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN projeto AS C ON(A.id_projeto = C.id_projeto)
            LEFT JOIN curso AS D ON(A.id_curso = D.id_curso)

            WHERE A.status IN ('60','61','62','63','64','65','66','80','81','101') 
            AND B.`status` = 1 AND B.rescisao_complementar = 0 
            AND A.id_projeto = '{$_REQUEST['projeto']}' {$data} $filtroProjetoJoin $auxPesquisa
                ORDER BY A.nome ASC ";

        $qr_demissao = mysql_query($sql_demissao);

        $array_rescisao = array();
        while ($row_demissao = mysql_fetch_array($qr_demissao)) {

            $regiao = $_REQUEST['regiao'];
            $clt = $row_demissao['id_clt'];
            $rescisao = $row_demissao['id_recisao'];

            /**
             * RESCISÃO COMPLEMENTAR
             */
            $sql_rescisao_complementar = "SELECT * FROM rh_recisao  WHERE vinculo_id_rescisao = '{$row_demissao['id_recisao']}' AND rescisao_complementar = 1  AND status = 1";
            $qr_rescisao_complementar = mysql_query($sql_rescisao_complementar);
            $total_rescisao_complementar = mysql_num_rows($qr_rescisao_complementar);
            $arr_complementar = array();
            while ($row_rescisao_complementar = mysql_fetch_array($qr_rescisao_complementar)) {
                $rescisao_compl = $row_rescisao_complementar['id_recisao'];
                $link_2 = str_replace('+', '--', encrypt("$regiao&$clt&$rescisao_compl"));
                $link_resc_complementar = "../../rh/recisao/nova_rescisao_2.php?enc=$link_2";
                $arr_complementar[] = $link_resc_complementar;
            }

            /**
             * RESCISÃO PRINCIPAL
             */
            $link = str_replace('+', '--', encrypt("$regiao&$clt&$rescisao"));
            if (substr($row_rescisao['data_proc'], 0, 10) >= '2013-04-04') {
                $link_nova_rescisao = "../../rh/recisao/nova_rescisao_2.php?enc=$link";
            } else {
                $link_nova_rescisao = "../../rh/recisao/nova_rescisao_2.php?enc=$link";
            }

            /**
             * ADD COMPLEMENTAR
             */
            $link_add_complementar = "../../rh/recisao/form_rescisao_complementar.php?id_clt={$clt}&id_rescisao={$rescisao}";

            /**
             * EDIÇÃO DE RESCISÃO
             */
            $link_rescisao_edicao = "../../rh/recisao/rescisao_edicao.php?enc=" . str_replace('+', '--', encrypt("$regiao&$clt&$rescisao"));

            /**
             * MATRIZ DE RETORNO
             */
            $array_rescisao[$row_demissao['id_clt']]["nome"] = utf8_encode($row_demissao['nome']);
            $array_rescisao[$row_demissao['id_clt']]["projeto"] = utf8_encode($row_demissao['projeto']);
            $array_rescisao[$row_demissao['id_clt']]["data"] = $row_demissao['data_demi'];
            $array_rescisao[$row_demissao['id_clt']]["rescisao"] = $row_demissao['id_recisao'];
            $array_rescisao[$row_demissao['id_clt']]["liquido"] = "R$ " . number_format($row_demissao['total_liquido'], 2, ',', '.');
            $array_rescisao[$row_demissao['id_clt']]["link"] = $link_nova_rescisao;
            $array_rescisao[$row_demissao['id_clt']]["link_edicao"] = $link_rescisao_edicao;
            $array_rescisao[$row_demissao['id_clt']]["complementa"] = $arr_complementar;
            $array_rescisao[$row_demissao['id_clt']]["add_complementa"] = $link_add_complementar;
            $array_rescisao[$row_demissao['id_clt']]["ano"] = $_REQUEST['txt_ano'];
        }

        echo json_encode($array_rescisao);
        exit();
    }

    /**
     * DESPROCESSAR RESCISÃO
     */
    if ($_REQUEST['method'] == "desprocessar_recisao") {

        /**
         * ATUALIZANDO A TABELA DE RH_CLT
         * COM A DATA ATUAL DA AÇÃO DE 
         * FINALIZAR A FOLHA
         */
        //$rh->Clt->setDefault()->setIdClt($_REQUEST['id_clt'])->onUpdate();
        //$rh->Clt->setDefault()->setIdClt($_REQUEST['id_clt'])->onUpdate();
        onUpdate($_REQUEST['id_clt']);

        $retorno = array("status" => false);
        $dados = $obj_recisao->verificaSaidaPagaDeRecisao($_REQUEST['id_rescisao'], $_REQUEST['id_regiao'], $_REQUEST['id_clt'], $_REQUEST['tpCanAvisoPr'], $_REQUEST['obs']);
        return $dados;
    }

    /**
     * VOLTAR AGUARDANDO DEMISSÃO
     */
    if ($_REQUEST['method'] == "voltar_aguardando") {

        /**
         * ATUALIZANDO A TABELA DE RH_CLT
         * COM A DATA ATUAL DA AÇÃO DE 
         * FINALIZAR A FOLHA
         */
//        $rh->Clt->setDefault()->setIdClt($_REQUEST['id_clt'])->onUpdate();
        onUpdate($_REQUEST['id_clt']);

        $retorno = array("status" => 0);
        $rsclt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '" . $_REQUEST['id_clt'] . "'");
        $rowClt = mysql_fetch_assoc($rsclt);

        /**
         * LOG
         */
        $local = "Desprocessar Aguardando Demissão";
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "{$usuario['nome']} desprocessou o clt {$_REQUEST['id_clt']}";
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];

        $sql1 = "UPDATE rh_clt SET status = '10', data_saida = '', data_aviso = '', data_demi = '',  status_demi = '' WHERE id_clt = '" . $_REQUEST['id_clt'] . "' LIMIT 1";
        $sql2 = "INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')";

        if (mysql_query($sql1) && mysql_query($sql2)) {
            $retorno = array("status" => 1);
        }

        echo json_encode($retorno);
        exit();
    }
}



$sqlBanco = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$_REQUEST['id_clt']} ORDER BY id_banco");
while ($rowBanco = mysql_fetch_array($sqlBanco)) {
    $optionBanco .= "<option value='{$rowBanco['id_banco']}'>{$rowBanco['razao']}({$rowBanco['nome']})</option>";
}


$ArquivoTxtBancoClass = new ArquivoTxtBancoClass();
$arrayArquivos = $ArquivoTxtBancoClass->getRegistros('r');
if (isset($_REQUEST['arqRescisao']) AND ! empty($_REQUEST['arqRescisao'])) {
    $ArquivoTxtBancoClass->gerarTxtBanco('RESCISAO', $_REQUEST['banco'], $_REQUEST['data'], $_REQUEST['arqRescisao']);
    header("Location: arquivo_banco_rescisao.php");
}

/**
 * PREPARA VARIAVEIS PARA FUNCIONAMENTO DO 
 * CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
 */
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

/**
 * CAMINHO NO TOPO DA PÁGINA
 */
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Rescisão");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php");

// Solicitações de Demissão do Portal
$qr_solicitacoes = mysql_query("SELECT c.id_clt AS cod,
                                        c.nome AS nome,
                                        c.data_entrada AS data_admissao,
                                        u.unidade AS unidade_nome
                             FROM portal_rescisao_solicitacoes AS p
                             INNER JOIN rh_clt AS c
                                     ON p.id_clt = c.id_clt
                             INNER JOIN unidade AS u
                                     ON c.id_unidade = u.id_unidade
                             WHERE status_id = 1");
$total_solicitacoes = mysql_num_rows($qr_solicitacoes);

// Solicitações Negadas
$qr_solicitacoes_negadas = mysql_query("SELECT c.id_clt AS cod,
                                            c.nome AS nome,
                                            c.data_entrada AS data_admissao,
                                            u.unidade AS unidade_nome
                                         FROM portal_rescisao_solicitacoes AS p
                                         INNER JOIN rh_clt AS c
                                                 ON p.id_clt = c.id_clt
                                         INNER JOIN unidade AS u
                                                 ON c.id_unidade = u.id_unidade
                                         WHERE status_id = 2");
$total_solicitacoes_negadas = mysql_num_rows($qr_solicitacoes_negadas);

// Solicitações Aceitas
$qr_solicitacoes_aceitas = mysql_query("SELECT c.id_clt AS cod,
                                            c.nome AS nome,
                                            c.data_entrada AS data_admissao,
                                            u.unidade AS unidade_nome
                                         FROM portal_rescisao_solicitacoes AS p
                                         INNER JOIN rh_clt AS c
                                                 ON p.id_clt = c.id_clt
                                         INNER JOIN unidade AS u
                                                 ON c.id_unidade = u.id_unidade
                                         WHERE status_id = 3");
$total_solicitacoes_aceitas = mysql_num_rows($qr_solicitacoes_aceitas);

//aki
$id_regiao = (!empty(($_REQUEST['regiao']))) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSEL = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $usuario['id_regiao'];
$mesSEL = 13;
$anoSEL = date('Y');

$optProjetos = getProjetos($id_regiao);
$optMeses = mesesArray(null, "13", "« Todos os Meses »");
$optAnos = anosArray();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Rescisão</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Rescisão</small></h2></div>

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active">
                            <a href="#lista" role="tab" data-toggle="tab">Lista de Funcionários</a>
                        </li>
                        <li role="presentation">
                            <a href="/intranet/rh/recisao/estagiario/lista_restagiarios_rescindidos.php">Rescisão de Estagiários</a>
                        </li>
                        <!--                        <li role="presentation">
                                                        <a href="#rescisoes_solicitadas" role="tab" data-toggle="tab">Rescisões Solicitadas</a>
                                                </li>
                                                <li role="presentation">
                                                        <a href="#solicitacoes_negadas" role="tab" data-toggle="tab">Solicitações Negadas</a>
                                                </li>
                                                <li role="presentation">
                                                        <a href="#solicitacoes_aceitas" role="tab" data-toggle="tab">Solicitações Aceitas</a>
                                                </li>-->
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="lista">

                            <form class="form-horizontal" role="form" id="form-lista" method="post">
                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <?php echo montaSelect($optProjetos, $projetoSEL, "id='projeto_lista' name='projeto' class='form-control projeto'") ?>
                                                <!--select name="projeto" id="projeto_lista" class="form-control projeto">
                                                    <option>-- Todos os Projetos --</option>
                                                </select-->
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Competência:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">

                                                    <?php echo montaSelect($optMeses, $mesSEL, "id='txt_mes' name='txt_mes' class='form-control'") ?>
                                                    <!--select name="txt_mes" id="txt_mes" class="form-control">
                                                        <option>« Selecione o Mês »</option>
                                                        <option value="13">« Todos »</option>
                                                        <option value="01">Janeiro</option>
                                                        <option value="02">Fevereiro</option>
                                                        <option value="03">Março</option>
                                                        <option value="04">Abril</option>
                                                        <option value="05">Maio</option>
                                                        <option value="06">Junho</option>
                                                        <option value="07">Julho</option>
                                                        <option value="08">Agosto</option>
                                                        <option value="09">Setembro</option>
                                                        <option value="10">Outubro</option>
                                                        <option value="11">Novembro</option>
                                                        <option value="12">Dezembro</option>
                                                    </select-->
                                                    <div class="input-group-addon"> ano </div>
                                                    <?php echo montaSelect($optAnos, $anoSEL, "id='txt_ano' name='txt_ano' class='form-control'") ?>
                                                    <!--select name="txt_ano" id="txt_ano" class="form-control">
                                                        <option>« Selecione o Ano »</option>
                                                        <option value="2010">2010</option>
                                                        <option value="2011">2011</option>
                                                        <option value="2012">2012</option>
                                                        <option value="2013">2013</option>
                                                        <option value="2014">2014</option>
                                                        <option value="2015">2015</option>
                                                        <option value="2016">2016</option>
                                                        <option value="2017">2017</option>
                                                        <option value="2018">2018</option>
                                                    </select-->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nome_clt" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-9"><input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do CLT, CPF, Matrícula"></div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">                                       
                                        <a href="../../relatorios/provisao_de_gastos.php?regiao=<?php echo $id_regiao; ?>" target="_blank" class="btn btn-warning">Rescisão em Lote</a>
                                        <input type="button" value="Consultar" class="btn btn-primary">                                        
                                        <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                                    </div>

                                </div><!-- /.panel -->
                                <div id="retorno-lista-aguardando-demissao">

                                </div>
                                <div id="retorno-lista">

                                </div>
                            </form>

                        </div><!-- /#lista -->

                        <div role="tabpanel" class="tab-pane" id="rescisoes_solicitadas">
                            <?php if ($total_solicitacoes) { ?>
                                <table class="table table-condensed table-striped table-bordered">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th width="6%">COD</th>
                                            <th width="34%">NOME</th>
                                            <th width="17%">DATA DE ADMISSÃO</th>
                                            <th width="31%">UNIDADE</th>
                                            <th width="13%">AÇÕES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($solicitacao = mysql_fetch_assoc($qr_solicitacoes)) { ?>
                                            <tr>
                                                <td><?php echo $solicitacao['cod']; ?></td>
                                                <td><?php echo $solicitacao['nome']; ?></td>
                                                <td><?php echo implode('/', array_reverse(explode('-', $solicitacao['data_admissao']))); ?></td>
                                                <td><?php echo $solicitacao['unidade_nome']; ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-xs" href="../../rh/docs/rescisao_configuracao.php?clt=<?php echo $solicitacao['cod']; ?>">
                                                        Prosseguir
                                                    </a>
                                                    <a class="btn btn-danger btn-xs" href="negar_solicitacao.php?clt=<?php echo $solicitacao['cod']; ?>">
                                                        Negar
                                                    </a>
                                                </td>
                                            </tr>     
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p>Nenhuma solicitação encontrada.</p>
                            <?php } ?>
                        </div><!-- /#solicitacoes -->

                        <div role="tabpanel" class="tab-pane" id="solicitacoes_negadas">
                            <?php if ($total_solicitacoes_negadas) { ?>
                                <table class="table table-condensed table-striped table-bordered">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th width="6%">COD</th>
                                            <th width="34%">NOME</th>
                                            <th width="17%">DATA DE ADMISSÃO</th>
                                            <th width="31%">UNIDADE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($solicitacao = mysql_fetch_assoc($qr_solicitacoes_negadas)) { ?>
                                            <tr>
                                                <td><?php echo $solicitacao['cod']; ?></td>
                                                <td><?php echo $solicitacao['nome']; ?></td>
                                                <td><?php echo implode('/', array_reverse(explode('-', $solicitacao['data_admissao']))); ?></td>
                                                <td><?php echo $solicitacao['unidade_nome']; ?></td>
                                            </tr>     
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p>Nenhuma solicitação encontrada.</p>
                            <?php } ?>
                        </div><!-- /#solicitacoes -->

                        <div role="tabpanel" class="tab-pane" id="solicitacoes_aceitas">
                            <?php if ($total_solicitacoes_aceitas) { ?>
                                <table class="table table-condensed table-striped table-bordered">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th width="6%">COD</th>
                                            <th width="34%">NOME</th>
                                            <th width="17%">DATA DE ADMISSÃO</th>
                                            <th width="31%">UNIDADE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($solicitacao = mysql_fetch_assoc($qr_solicitacoes_aceitas)) { ?>
                                            <tr>
                                                <td><?php echo $solicitacao['cod']; ?></td>
                                                <td><?php echo $solicitacao['nome']; ?></td>
                                                <td><?php echo implode('/', array_reverse(explode('-', $solicitacao['data_admissao']))); ?></td>
                                                <td><?php echo $solicitacao['unidade_nome']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p>Nenhuma solicitação encontrada.</p>
                            <?php } ?>
                        </div><!-- /#solicitacoes -->

                    </div>
                </div><!-- /.col-lg-12 -->
                <div id="CancelAviso" style="display: none;">
                    <p>
                        <input type="hidden" id="idCanRescisao"/>
                        <input type="hidden" id="idCanRegiao"/>
                        <input type="hidden" id="idCanClt"/>
                    </p>
                    <p>Motivo do Cancelamento do Aviso Previo:</p>
                    <p><select id="tpCancelAvisoPre" name="tpCancelAvisoPre" class="validate[required]">
                            <option value="">Selecione...</option>
                            <?php
                            $qr_canAvisoPre = mysql_query("SELECT id_tipoCanAvisoPre, descricao FROM tipo_cancelamento_aviso_previo;");
                            while ($rowAvisoPre = mysql_fetch_assoc($qr_canAvisoPre)) {
                                ?>
                                <option value="<?= $rowAvisoPre['id_tipoCanAvisoPre'] ?>"><?= $rowAvisoPre['descricao'] ?></option>
                            <?php } ?>
                        </select>
                    </p>
                    <p>Observação:</p>
                    <p><textarea id="obsCancel" name="obsCancel" cols="30" rows="5"></textarea></p>
                    <p class="controls">
                        <input type="button"  class="btn" value="Sim"/>
                    </p>
                </div>
            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>

    $(function () {

        /**
         * NEGAR SOLICITAÇÃO DE DEMISSÃO DO PORTAL
         */
        $('.negar-solicitacao').click(function () {
            console.log($(this).data('id'));
        });

        /**
         * SELECT DE PROJETOS
         * @type @call;$@call;val
         */
        /*var regiao = $("#regiao").val();
         $.post('../../methods.php', {method: 'carregaProjetos', regiao: regiao}, function (data) {
         $(".projeto").html(data);
         });*/

        /**
         * EXCLUIR RESCISÃO
         * @param {type} param
         */
        $("body").on("click", ".remove_recisao", function () {
            $("#CancelAviso").show();
            thickBoxModal("Desprocessar Rescisão", "#CancelAviso", 500, 600);
            $("#idCanRescisao").val($(this).attr("data-recisao"));
            $("#idCanRegiao").val($(this).attr("data-regiao"));
            $("#idCanClt").val($(this).attr("data-clt"));

        });

        $(".btn").click(function () {
            if ($(this).val() == 'Sim') {
                var id_rescisao = $("#idCanRescisao").val();
                var id_regiao = $("#idCanRegiao").val();
                var id_clt = $("#idCanClt").val();
                var tpCanAvisoPr = $("#tpCancelAvisoPre").val();
                var obs = $("#obsCancel").val();
                $.ajax({
                    url: "",
                    type: "POST",
                    dataType: "json",
                    data: {
                        tpCanAvisoPr: tpCanAvisoPr,
                        obs: obs,
                        id_rescisao: id_rescisao,
                        id_regiao: id_regiao,
                        id_clt: id_clt,
                        method: "desprocessar_recisao"
                    },
                    success: function (data) {
                        if (!data.status) {
                            $(data.dados).each(function (k, v) {
                                $(".data_demissao").html(v.data_demissao);
                                $(".data_pagamento").html(v.data_pg);
                                $(".nome").html(v.nome_clt);
                                $(".status").html(v.status_saida);
                                $(".valor").html(v.valor);
                            });
                            $("#mensagens").show();
                            thickBoxModal("Desprocessar Recisão", "#mensagens", "500", "600");
                        } else {
                            history.go(0);
                        }
                    }
                });
            }

        });

        /**
         * CONSULTA
         * @param {type} param1
         * @param {type} param2
         * @param {type} param3
         */
        $("body").on("click", "input[value='Consultar']", function () {

            var regiao = $("#regiao").val();
            var projeto = $("#projeto_lista").val();
            var mes = $("#txt_mes").val();
            var ano = $("#txt_ano").val();
            var pesquisa = $("#pesquisa").val();

            /**
             * LISTA DE PARTICIPANTES AGUARDANDO
             * DEMISSÃO DO PROJETO
             */
            $.ajax({
                url: "",
                type: "post",
                dataType: "json",
                data: {
                    regiao: regiao,
                    projeto: projeto,
                    txt_mes: mes,
                    txt_ano: ano,
                    pesquisa: pesquisa,
                    method: "listaParticipantesAguardando"
                },
                success: function (data) {

                    if (data != null && data !== undefined) {
                        var html = "";

                        html += "<h4 class='valign-middle'>";
                        html += "<i class='fa fa-chevron-right'></i>";
                        html += " PARTICIPANTES AGUARDANDO DEMISSÃO";
                        html += "</h4>";

                        html += "<table class='table table-striped table-hover table-condensed table-bordered' id='tbRelatorio'>";
                        html += "<thead>";

                        html += "<tr class='bg-primary valign-middle'>";
                        html += "<th class='text-center' style='width:4%;'>COD</th>";
                        html += "<th style='width:25%;'>NOME</th>";
                        html += "<th class='text-center' style='width:15%;'>PROJETO</th>";
                        html += "<th class='text-center' style='width:10%;'>UNIDADE</th>";
                        html += "<th class='text-center' style='width:15%;'>CARGO</th>";
                        <?php if($_COOKIE['logado'] != 395) { ?>
                            html += "<th class='text-center' style='width:5%;'>AÇÃO</th>";
                        <?php } ?>
                        html += "</tr>";

                        html += "</thead>";
                        html += "<tbody>";

                        $.each(data, function (key, dados) {
                            html += "<tr class='valign-middle' data-key='" + key + "'>";
                            html += "<td>" + key + "</td>";
                            <?php if($_COOKIE['logado'] != 395) { ?>
                                html += "<td><a href='" + dados.link + "'>" + dados.nome + "</a></td>";
                            <?php } else { ?>
                                html += "<td><a>" + dados.nome + "</a></td>";
                            <?php } ?>
                            html += "<td>" + dados.projeto + "</td>";
                            html += "<td>" + dados.unidade + "</td>";
                            html += "<td>" + dados.funcao + "</td>";
                            <?php if($_COOKIE['logado'] != 395) { ?>
                                html += "<td class='text-center'><a class='btn btn-danger btn-xs  excluir_aguardando' href='javascript:;' data-url='" + dados.link2 + "' data-key='" + key + "'><i class='bt-image glyphicon glyphicon-chevron-left tooo' data-original-title='Desprocessar Aguardando Rescisão' data-toggle='tooltip' data-placement='top'></i></a></td>";
                            <?php } ?>
                            html += "</tr>";
                        });

                        html += "</tbody>";
                        html += "</table>";

                        $("#retorno-lista-aguardando-demissao").html(html);
                    }
                }
            });

            /**
             * LISTA DE PARTICIPANTES DESATIVADOS
             **/
            $.ajax({
                url: "",
                type: "post",
                dataType: "json",
                data: {
                    regiao: regiao,
                    projeto: projeto,
                    txt_mes: mes,
                    txt_ano: ano,
                    pesquisa: pesquisa,
                    method: "listaParticipantes"
                },
                success: function (data) {

                    console.log(data);

                    if (data != null && data !== undefined) {

                        var html = "";
                        var disabled = "";

                        html += "<h4 class='valign-middle'>";
                        html += "<i class='fa fa-chevron-right'></i>";
                        html += " PARTICIPANTES DESATIVADOS";
                        html += "</h4>";

                        html += "<table class='table table-striped table-hover table-condensed table-bordered' id='tbRelatorio'>";
                        html += "<thead>";

                        html += "<tr class='bg-primary valign-middle'>";
                        html += "<th class='text-center' style='width:5%;'>COD</th>";
                        html += "<th style='width:31%;'>NOME</th>";
                        html += "<th style='width:18%;'>PROJETO</th>";
                        html += "<th class='text-center' style='width:8%;'>DATA</th>";
                        html += "<th class='text-center' style='width:8%;'>RESCISÃO</th>";
                        html += "<th class='text-center' style='width:6%;'>COMPLEM.</th>";
                        <?php if($_COOKIE['logado'] != 395) { ?>
                            html += "<th class='text-center' style='width:6%;'>ADD</th>";
                        <?php } ?>
                        html += "<th style='width:10%;'>VALOR</th>";
                        <?php if($_COOKIE['logado'] != 395) { ?>
                            html += "<th class='text-center' style='width:8%;'>AÇÃO</th>";
                        <?php } ?>
                        html += "</tr>";

                        html += "</thead>";
                        html += "<tbody>";

                        $.each(data, function (key, dados) {
                            html += "<tr class='valign-middle'>";
                            html += "<td class='text-center'>" + key + "</td>";
                            html += "<td>" + dados.nome + "</td>";
                            html += "<td>" + dados.projeto + "</td>";
                            html += "<td class='text-center'>" + dados.data + "</td>";
                            html += "<td class='text-center'><a href='" + dados.link + "' class='btn btn-default btn-xs' target='_blank'><i class='text-danger fa fa-file-pdf-o' alt='Ver PDF'></i></a></td>";
                            html += "<td class='text-center'>";
                            $.each(dados.complementa, function (k, linkComplementar) {
                                html += "<a href='" + linkComplementar + " 'class='btn btn-default btn-xs' target='_blank'><i class='text-danger fa fa-file-pdf-o' alt='Ver PDF'></i></a>";
                            })
                            html += "</td>";
                            <?php if($_COOKIE['logado'] != 395) { ?>html += "<td class='text-center'><a class='btn btn-default btn-xs' href='" + dados.add_complementa + "' title='Adicionar Complementar'><i class='fa fa-plus'></i></a></td>";<?php } ?>
                            html += "<td>" + dados.liquido + "</td>";

                            /*
                             * 13/02/2017
                             * by: Max
                             * A PEDIDO DO SABINO, por conta da DIRF                                 
                             */
                            if (dados.ano == 2016) {
                                disabled = "disabled";
                            } else {
                                disabled = "";
                            }
                            <?php if($_COOKIE['logado'] != 395) { ?>
                            html += "<td class='text-center'><a class='btn btn-danger btn-xs remove_recisao " + disabled + "' href='javascript:;' title='Desprocessar Rescisão' data-clt='" + key + "' data-recisao='" + dados.rescisao + "'><i class='bt-image glyphicon glyphicon-chevron-left tooo' data-original-title='Desprocessar Rescisão' data-toggle='tooltip' data-placement='top'></i></a> <a class='btn btn-info btn-xs " + disabled + "' title='Modificar Rescisão' href='" + dados.link_edicao + "'><i class='bt-image glyphicon glyphicon-edit tooo' data-original-title='Desprocessar Rescisão' data-toggle='tooltip' data-placement='top'></i></a></td>";
                            <?php } ?>
                            html += "</tr>";
                        });

                        html += "</tbody>";
                        html += "</table>";

                        $("#retorno-lista").html(html);
                    }
                }
            });

        });

        /**
         * EXCLUIR AGUARDANDO DEMISSÃO
         */
        $("body").on("click", ".excluir_aguardando", function () {
            var url = $(this).data("url");
            var key = $(this).data("key");

            BootstrapDialog.confirm('Remover Participante de Aguardando Demissão?', 'Confirmação', function (result) {
                if (result) {
                    $.ajax({
                        url: url,
                        type: 'get',
                        dataType: 'json',
                        success: function (data) {
                            if (data.status) {
                                $("tr[data-key='" + key + "']").hide();
                            }
                        }
                    });
                }
            });

        });

    });
        </script>
    </body>
</html>
