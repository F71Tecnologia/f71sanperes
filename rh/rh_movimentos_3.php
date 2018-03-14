<?php
/*
 * PHP-DOC  
 * 
 * ??-??-???? 
 * 
 * Procedimentos para lançamento de movimentos
 * 
 * Versão: 3.0.4394 - 18/12/2015 - Jacques - Adicionado + 1 a montagem do array do select de ano para permitir lançamento no ano seguinte
 * Versão: 3.0.5140 - 11/01/2016 - Ramon - Alterando validação de movimento Faltas, para igualar ao IDR
 * 
 * @Não Definido
 */

if ($_COOKIE['logado'] == 179) {
//    echo "<pre>";
//        print_r($_REQUEST);
//    echo "</pre>";
}

/* ALLMX */
$cookie = false;
//if($_COOKIE['logado'] == 179){
//    $cookie = true;
//}

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

session_start();
include "../conn.php";
include "../funcoes.php";
include "../classes/funcionario.php";
include "../classes/calculos.php";
include "../classes/global.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/CalculoFolhaClass.php';
include '../classes/MovimentoClass.php';
include("../wfunction.php");
include("../classes/RhClass.php");

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Gerenciar Movimentos");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php", "Movimentos" => "rh_movimentos1.php");

$global = new GlobalClass();
$calculos = new calculos();

error_reporting(0);
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();
$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));

$arrDatasHoras = array(232,236,293);
$arrRefPorc = array(523,680,692,693);

if (isset($_POST['excluir'])) {

    /**
     * ATUALIZANDO A TABELA DE RH_CLT
     * COM A DATA ATUAL DA AÇÃO DE
     * FINALIZAR A FOLHA
     */
    $qryClt = "SELECT A.id_clt FROM rh_movimentos_clt AS A WHERE A.id_movimento = '{$_POST['id_movimento']}'";
    $sqlClt = mysql_query($qryClt) or die('erro ao verificar id_clt');
    $idClt = '';
    while ($row = mysql_fetch_assoc($sqlClt)) {
        $idClt = $row['id_clt'];
    }

    /**
     * ATUALIZANDO A TABELA DE RH_CLT
     * COM A DATA ATUAL DA AÇÃO DE
     * FINALIZAR A FOLHA
     */
    onUpdate($idClt);

    $id_movimento = $_POST['id_movimento'];
    mysql_query("UPDATE rh_movimentos_clt SET status = '0' WHERE id_movimento = '$id_movimento' LIMIT 1");
    exit;
}

// Recebendo a variável criptografada
$enc = $_REQUEST['enc'];
$encPagina = $enc;
$enc = str_replace("--", "+", $enc);
$link1 = decrypt($enc);

$teste = explode("&", $link1);
//$regiao = $teste[0];

$regiao = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

$clt = $_REQUEST['clt'];
$projeto = $teste[1];
$pagina_atual = $_REQUEST['pg'];
///MOVIMENTOS DE Débito
$array_horistas = array("5425", "5426", "5512");
//DEFININDO IDS DOS MOVIMENTOS DE FALTAS
$arrayFaltas = array(232, 236, 293, 321, 630);

$objMovimento = new Movimentos();
$movLancado = $objMovimento->getMovimentosLancadosPorClt($clt);
$objMovimento->carregaMovimentos(date('Y'));

/* ALLMX */
if ($cookie) {
    //$calc = new calculos();    
}

/*
  echo $pagina_atual;
  $qr_paginacao = mysql_query("SELECT *
  FROM rh_clt as A
  WHERE A.id_projeto = '$projeto'
  AND (A.status < '60' OR A.status = '200');");
 */


$qr_clt = mysql_query("SELECT A.*,B.nome as funcao, B.id_curso, B.salario ,B.cbo_codigo, F.horas_mes, D.regiao as nome_regiao,E.nome as nome_projeto,A.id_projeto, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                       B.periculosidade_30, F.adicional_noturno, F.horas_noturnas, F.nome as nome_horario, F.id_horario
                        FROM rh_clt as A 
                       LEFT JOIN curso as B ON A.id_curso = B.id_curso
                       LEFT JOIN rhstatus as C ON C.codigo = A.status
                       LEFT JOIN regioes as D ON D.id_regiao = A.id_regiao
                       LEFT JOIN projeto as E ON E.id_projeto = A.id_projeto
                       LEFT JOIN rh_horarios as F ON F.id_horario = A.rh_horario
                       WHERE A.id_clt = $clt");
$row_clt = mysql_fetch_assoc($qr_clt);

if ($row_clt['periculosidade_30']) {
    $periculosidade = $objCalcFolha->getPericulosidade($row_clt['salario'], 30, 12);
}

$insalubridade = $objCalcFolha->getInsalubridade(30, $row_clt['tipo_insalubridade'], $row_clt['qnt_salminimo_insalu'], date('Y'));

//echo $row_clt['adicional_noturno'];

if ($row_clt['adicional_noturno']) {
    $baseCalcAdiconal = $row_clt['salario'] + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
    $adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $row_clt['horas_mes'], $row_clt['horas_noturnas']);
    $dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
}


$baseCalc = $row_clt['salario'] + $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
$valor_diario = ($baseCalc) / 30;
$valor_hora = ($baseCalc) / $row_clt['horas_mes'];

$baseCalcAtraso = $row_clt['salario'] + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
$valor_diarioAtraso = ($baseCalcAtraso) / 30;
$valor_horaAtraso = ($baseCalcAtraso) / $row_clt['horas_mes'];

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

/*
 * Verificação se há folha aberta. caso haja, o mês selecionado será o mês da folha.
 * Alteração feita pq o pessoal do RH estava se confundindo quando o mês virava.
 */
$query = "SELECT mes,ano FROM rh_folha WHERE projeto = '{$row_clt['id_projeto']}' AND status = 2";
$mes_folha_aberta = mysql_query($query);
$data_folha = mysql_fetch_assoc($mes_folha_aberta);
$mesSel = "";
if (mysql_num_rows($mes_folha_aberta) > 0) {
    $mesSel = $data_folha['mes'];
} else {
    $mesSel = date('m');
}

//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y') + 1; $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');


///REGIÕES
$regioes = montaQuery('ano_meses', "num_mes,nome_mes", "1");
$optMes = array();
foreach ($regioes as $valor) {
    $optMes[$valor['num_mes']] = $valor['num_mes'] . ' - ' . $valor['nome_mes'];
}
$optMes[13] = '13º Primeira Parcela';
$optMes[14] = '13º Segunda Parcela';
$optMes[15] = '13º Integral';
$optMes[16] = 'Rescisão';
$optMes[17] = 'Férias - Primeira Parcela';
$optMes[18] = 'Férias - Segunda Parcela';

/**
 * FEITO POR: SINÉSIO LUIZ
 * 17/06/2015
 * CADASTRO DE MOVIMENTO DE REREMBOLSO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] != "") {
    if ($_REQUEST['method'] == "cadMovReembolso") {

        /**
         * ATUALIZANDO A TABELA DE RH_CLT
         * COM A DATA ATUAL DA AÇÃO DE
         * FINALIZAR A FOLHA
         */
        onUpdate($_REQUEST['clt']);

        $return = array("status" => 0);
        $valor = number_format($_REQUEST['valor'], '2', '.', '');
        $query = "INSERT INTO rh_movimentos_clt (
                        id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento, nome_movimento,
                        data_movimento,user_cad,valor_movimento,lancamento,incidencia,qnt,status,status_folha,status_ferias,
                        status_reg,qnt_horas) VALUES ( 
                        '{$_REQUEST['clt']}','{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','229','50244','CREDITO','REEMBOLSO DE FALTAS',
                        NOW(),'{$_COOKIE['logado']}','{$valor}','1','5020,5021,5023','{$_REQUEST['qnt']}','1','0','1','1','')";
        if (mysql_query($query)) {
            $return = array("status" => 1);
        }

        echo json_encode($return);
        exit();
    }
}


/////////////////////////////////////////////////////////      
/////////////// GRAVAÇÃO NO BANCO DE DADOS///////////////      
/////////////////////////////////////////////////////////      
if (isset($_POST['confirmar'])) {


    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $id_regiao = $_POST['regiao'];

    if ($_COOKIE['logado'] == 179) {
//        echo "<pre>";
//            print_r($_REQUEST);
//        echo "</pre>";
//        exit();
    }

    $id_projeto = $_POST['projeto'];
    $id_clt = $_POST['clt'];
    $movimentos = $_POST['mov_valor'];
    $movimentos_sempre = $_POST['mov_sempre'];
    $quant = $_POST['mov_qtd'];
    $tipos = $_POST['tipo_quantidade'];
    $parcela = $_POST['mov_parc'];
    $datas_falta_atraso = $_REQUEST['datas_falta_atraso'];
    $horas_falta_atraso = $_REQUEST['horas_falta_atraso'];
    
//    print_array([$datas_falta_atraso, $horas_falta_atraso]);
    
    
    /* ALLMX */
    if ($cookie) {
//        $mes_final = $mes;
//        $calculo = $calc->Calc_qnt_meses_13_ferias('2015-'.$mes.'-01', '2015-'.$mes_final.'-30');
//        
//        echo "<pre>";
//        print_r($calculo);
//        echo "</pre>";                   
    }

    $qr_funcao = mysql_query("SELECT B.salario FROM rh_clt as A 
                              INNER JOIN curso as B
                              ON A.id_curso = B.id_curso 
                              WHERE A.id_clt = '$id_clt'");
    $row_funcao = mysql_fetch_assoc($qr_funcao);


    ///PEGANDO AS INFORMAÇÔES DOS MOVIMENTOS

    $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1");
    while ($row_mov = mysql_fetch_assoc($qr_mov)) {
        $codigo_movimento[$row_mov['id_mov']] = $row_mov['cod'];
        $tipo_movimento[$row_mov['id_mov']] = ($row_mov['categoria'] == 'DESCONTO' or $row_mov['categoria'] == 'DEBITO') ? 1 : 2;
        $nome_movimento[$row_mov['id_mov']] = $row_mov['descicao'];
        $percentual_movimento[$row_mov['id_mov']] = $row_mov['percentual'];
        $percentual_movimento2[$row_mov['id_mov']] = $row_mov['percentual2'];
        $incide_dsr[$row_mov['id_mov']] = $row_mov['incide_dsr'];
        $incidencia_inss[$row_mov['id_mov']] = $row_mov['incidencia_inss'];
        $incidencia_irrf[$row_mov['id_mov']] = $row_mov['incidencia_irrf'];
        $incidencia_fgts[$row_mov['id_mov']] = $row_mov['incidencia_fgts'];
    }


    foreach ($movimentos as $id_mov => $valor) {

        //echo $mes = $_POST['mes']; die();


        $incidencia = array();
        if (!empty($valor) and $valor != '0,00') {


            $desc_obs = "obs_" . $id_mov;
            $obs = $_REQUEST[$desc_obs];

            $lancamento = ($movimentos_sempre[$id_mov] == 2) ? 2 : 1;
            $incidencia[0] = ($incidencia_inss[$id_mov] == 1) ? '5020' : '';
            $incidencia[1] = ($incidencia_irrf[$id_mov] == 1) ? '5021' : '';
            $incidencia[2] = ($incidencia_fgts[$id_mov] == 1) ? '5023' : '';
            $incidencia = implode(',', $incidencia);
            $valorf = str_replace(',', '.', str_replace('.', '', $valor));
            $tipo_mov = ($tipo_movimento[$id_mov] == 1) ? 'DEBITO' : 'CREDITO';
//      echo "<pre>
//            SELECT * FROM rh_movimentos_clt 
//            WHERE 
//            ((mes_mov = $mes AND ano_mov = $ano) OR (lancamento = 2 AND mes_mov NOT IN(13,14,15,16)))
//            AND status = 1 AND id_clt = $id_clt AND id_mov = $id_mov</pre>";
            ////VERIFICA MOVIMENTO LANÇADO
            $qr_verifica = "SELECT * FROM rh_movimentos_clt WHERE ((mes_mov = '{$mes}' AND ano_mov = '{$ano}') OR (lancamento = '2' AND mes_mov NOT IN(13,14,15,16))) AND status = '1' AND id_clt = '{$id_clt}' AND id_mov = '{$id_mov}'";
            mysql_query($qr_verifica) or die("Erro ao selecionar movimentos para o Clt");

            if ($_COOKIE['logado'] == 179) {
//                echo "<pre>";
//                    print_r($qr_verifica);
//                echo "</pre>";
            }

            if (mysql_num_rows($qr_verifica) != 0) {
                $row_verifica = mysql_fetch_assoc($qr_verifica);
                $mov_cadastrados[$id_mov] = $row_verifica['nome_movimento'];
            } else {


                // VERIFICANDO SE O VALOR DA AJUDA DE CUSTO PASSA DE 50% DO SALARIO DO CARA, PARA COLOCAR INCIDENCIA EM INSS,IRRF,FGTS
                if ($id_mov == 13) {
                    $metade = $row_funcao['salario'] / 2;
                    if ($valor > $metade) {
                        $incidencia = "5020,5021,5023";
                    }
                }


                $tp = (isset($tipos[$id_mov])) ? $tipos[$id_mov] : "(NULL)";

                if (isset($quant[$id_mov])) {

                    if ($tp == 1) {
                        $qnt_horas = $quant[$id_mov];
                        $qnt = '';
                    } else {
                        $qnt = $quant[$id_mov];
                        $qnt_horas = '';
                    }
                } else {
                    $qnt = "(NULL)";
                }
                
                if (in_array($id_mov, $arrRefPorc)) {
                    $tp = 3;
                }
                
                $sql_mov = "INSERT INTO 
                            rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
                            data_movimento,user_cad,valor_movimento,percent_movimento,percent_movimento2,lancamento,incidencia,qnt,tipo_qnt, qnt_horas, obs, incide_dsr) 
                            VALUES 
                            ('$id_clt','$id_regiao','$id_projeto','$mes','$ano','$id_mov','" . $codigo_movimento[$id_mov] . "',
                            '$tipo_mov','" . $nome_movimento[$id_mov] . "',NOW(),'$_COOKIE[logado]','$valorf','" . $percentual_movimento[$id_mov] . "','{$percentual_movimento2[$id_mov]}',
                            '$lancamento','$incidencia','$qnt','$tp', '$qnt_horas', '{$obs}', '{$incide_dsr[$id_mov]}')";
                $query_mov = mysql_query($sql_mov);
                $insertId = mysql_insert_id();
                
                if (in_array($id_mov, $arrDatasHoras)) {
                    
                    Movimentos::addDatasHorasFaltasAtrasos($id_clt, $id_mov, $insertId, $mes, $ano, $datas_falta_atraso[$id_mov], $horas_falta_atraso[$id_mov]);
                    
                }
                
                unset($qnt);
                unset($qnt_horas);
            }
        }
        unset($incidencia);
    }

    /* ALLMX */
//    if($cookie){
//        exit();
//    }

    if (sizeof($sql_mov) > 0) {

        /**
         * ATUALIZANDO A TABELA DE RH_CLT
         * COM A DATA ATUAL DA AÇÃO DE
         * FINALIZAR A FOLHA
         */
        onUpdate($id_clt);

        $_SESSION['mov_cadastrados'] = $mov_cadastrados;
//        $sql_mov = implode(',', $sql_mov);
//
//        mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
//                        data_movimento,user_cad,valor_movimento,percent_movimento,percent_movimento2,lancamento,incidencia,qnt,tipo_qnt, qnt_horas, obs, incide_dsr) VALUES
//                        $sql_mov");
    }
    
    header('Location: rh_movimentos_3.php?tela=2&pg=0&clt=' . $id_clt . '&enc=' . $encPagina);
    exit;
}

$valor = 0;
foreach ($movLancado['CREDITO'] as $mov) {
    if ($mov['incide_dsr'] == 1) {
        $valor += $mov['valor'];
    }
}

$valorDsr = $calculos->getDsr($valor, 30, $mesSel, $anoSel, $row_clt['id_projeto']);
?>
<html>
    <head>
        <title>Movimentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">

        <script src="../js/jquery-1.8.3.min.js"></script>
        <script src="../js/highslide-with-html.js" type="text/javascript"></script>
        <script src="../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>

        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/rh/rh_movimentos_3.js"></script>
        <style>
            .span-dsr {
                cursor: pointer;
            }

            fieldset.mov {
                border: 1px solid #666;
                width: 300px;
                float: left;
                margin-left: 10px;
                margin-top: 15px;
                padding-left: 20px !important;
            }

            h3.credito {
                text-align: center;
                background-color: #c8e5f8;
            }

            h3.debito {
                text-align: center;
                background-color: #e3d8c5;
            }

            .botao_enviar {
                text-align: center;
                border-top: 1px solid #edeae4;
                border-bottom: 1px solid #edeae4;
                padding-top: 5px;
                padding-bottom: 5px;
            }

            input[name="mov_desc_qnt"] {
                width: 50px;
            }

            .grid {
                float: left;
                margin-top: 30px;
                margin-bottom: 30px;
            }

            .right {
                float: right;
            }

            a.tooltip {
                outline: none;
                text-align: left
            }

            a.tooltip strong {
                line-height: 30px;
            }

            a.tooltip:hover {
                text-decoration: none;
            }

            a.tooltip span {
                z-index: 10;
                display: none;
                padding: 14px 20px;
                margin-top: -30px;
                margin-left: 28px;
                width: 200px;
                line-height: 16px;
            }

            a.tooltip:hover span {
                display: inline;
                position: absolute;
                color: #111;
                border: 1px solid #DCA;
                background: #fffAF0;
            }

            .callout {
                z-index: 20;
                position: absolute;
                top: 30px;
                border: 0;
                left: -12px;
            }

            /*CSS3 extras*/
            a.tooltip span {
                border-radius: 4px;
                box-shadow: 5px 5px 8px #CCC;
            }

            #modal_reembolso_faltas {
                display: none;
                text-align: left;
            }
            
            small {
                font-size: 75%;
            }
        </style>

    </head>
    <body>

        <?php include("../template/navbar_default.php"); ?>

        <form name="form" action="" method="post" id="form">

            <input type="hidden" name="salario_base" id="salario_base" value="<?php echo $row_clt['salario']; ?>"/>
            <input type="hidden" name="valor_hora" id="valor_hora" value="<?php echo $valor_hora; ?>"/>
            <input type="hidden" name="valor_dia" id="valor_dia" value="<?php echo $valor_diario; ?>"/>
            <input type="hidden" name="valor_horaAtraso" id="valor_horaAtraso" value="<?php echo $valor_horaAtraso; ?>"/>
            <input type="hidden" name="valor_diaAtraso" id="valor_diaAtraso" value="<?php echo $valor_diarioAtraso; ?>"/>

            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header box-rh-header">
                            <h2><span class="fa fa-users"></span> - RECURSOS HUMANOS
                                <small> - Gerenciar Movimentos de crédito e desconto</small>
                            </h2>
                        </div>

                        <div class="tab-content">
                            <div class="col-lg-12 note">
                                <div class="col-lg-4">
                                    <p><strong>NOME: </strong> <?php echo $row_clt['nome'] ?></p>
                                    <p><strong>FUNÇÃO: </strong> <?php echo $row_clt['id_curso'] . ' - ' . $row_clt['funcao'] ?>
                                    </p>
                                </div>
                                <div class="col-lg-4">
                                    <p>
                                        <strong>HORÁRIO: </strong> <?php echo $row_clt['id_horario'] . ' - ' . $row_clt['nome_horario'] ?>
                                    </p>
                                    <p><strong>REGIÃO: </strong> <?php echo $row_clt['nome_regiao'] ?></p>
                                </div>
                                <div class="col-lg-4">
                                    <p><strong>PROJETO: </strong> <?php echo $row_clt['nome_projeto'] ?></p>
                                </div>
                            </div>
                        </div>

                        <!--resposta de algum metodo realizado-->
                        <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default hidden-print">
                            <div class="panel-heading text-bold">Competência</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="select" class="col-sm-offset-1 col-sm-1 control-label">Mês</label>
                                    <div class="col-sm-4">
                                        <?php echo montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes', 'class' => 'mesMovCredito form-control')); ?>
                                    </div>
                                    <label for="select" class="col-sm-1 control-label">Ano</label>
                                    <div class="col-sm-4">
                                        <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'anoMovCredito form-control')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#credito" data-toggle="tab">CRÉDITO</a></li>
                            <li><a href="#debito" data-toggle="tab">DÉBITO</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <?php
                            if (sizeof($_SESSION['mov_cadastrados']) > 0) {
                                echo '<div class="alert alert-warning"><p>O(s) movimento(s) ' . implode(', ', $_SESSION['mov_cadastrados']) . ' não foram cadastrados pois
                                já existe para esta competência!</p></div>';
                                $_SESSION['mov_cadastrados'] = null;
                            }

                            /* ALLMX */
// NUMERO DE PARCELAS
                            if ($cookie) {
                                $colspan = 6;
                            } else {
                                $colspan = 5;
                            }
                            ?>
                            <div class="tab-pane fade active in" id="credito">
                                <table class="table table-striped table-hover table-condensed table-bordered"
                                       style="font-size: 14px; margin-top: 20px;">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <th>Movimento</th>
                                            <th>Incidência</th>
                                            <th>Valor</th>
                                            <th>Sempre</th>
                                            <th>Tipo</th>
                                            <th>Quantidade</th>
                                            <?php if ($cookie) { /* ALLMX */ ?>
                                                <th>Nº Parcelas</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
///MOVIMENTOS DE CRÉDITO

                                        $sqlMovSind = "SELECT id_mov FROM rh_movimentos_sindicatos_assoc WHERE id_sindicato = {$row_clt['rh_sindicato']}";
                                        $queryMovSind = mysql_query($sqlMovSind);
                                        while ($rowMovSind = mysql_fetch_assoc($queryMovSind)) {
                                            $arrMovSind[] = $rowMovSind['id_mov'];
                                        }

                                        $arrMovSind = implode(",", $arrMovSind);
//                                        $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'CREDITO' AND mov_lancavel = 1 AND cod NOT IN (9000) AND id_mov IN ($arrMovSind) ORDER BY descicao");
                                        $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'CREDITO' AND mov_lancavel = 1 AND cod NOT IN (9000) AND ignorar_rubrica = 0 ORDER BY descicao");
                                        while ($row_mov = mysql_fetch_assoc($qr_mov)) {
                                            $infoCalc = 0;
                                            if ($row_mov['tipo_qnt_lancavel']) {
                                                $infoCalc = 1;
                                            }

                                            if ($row_mov['incidencia_inss']) {
                                                $incid[] = "<span class='label label-success'>INSS</span>";
                                            }
                                            if ($row_mov['incidencia_irrf']) {
                                                $incid[] = '<span class="label label-warning">IRRF</span>';
                                            }
                                            if ($row_mov['incidencia_fgts']) {
                                                $incid[] = '<span class="label label-info">FGTS</span>';
                                            }

                                            if ($row_mov['incide_dsr']) {
                                                $incid[] = '<span class="label label-default span-dsr">DSR</span>';
                                            }

                                            $incid = implode(" ", $incid);

                                            // $classAuxDistancia = ($row_mov[id_mov] == 193)?"validate[funcCall[auxDistancia]]" :'' ;

                                            $modalReembolsoFaltas = 0;
                                            if ($row_mov['id_mov'] == 229) {
                                                $modalReembolsoFaltas = 1;
                                            }

                                            if ($row_mov['tipo_qnt_lancavel']) {
                                                $valorReadOnly = 'readonly'; // 2017-06-22 - leonardo - a pedido do jeferson liberar pra imputação manual
                                                $i++;
                                                $checked1 = 'checked="checked"';
                                                $campoTipoQnt = "<select name='tipo_quantidade[{$row_mov['id_mov']}]' class='tipo_qnt form-control'>
                                                                    <option value='1'>Horas</option>
                                                                    <option value='2'>Dias</option>
                                                                 </select>  ";
                                                $campoQnt = '<input type="text" name="mov_qtd[' . $row_mov['id_mov'] . ']" class="calculo hora_mask form-control" data-key="' . $row_mov['id_mov'] . '" style="width: 100px;" />';
                                            } else {
                                                $campoTipoQnt = '';
                                                $campoQnt = '';
                                            }

                                            if ($row_mov['parcelamento']) {
                                                $campoParcelamento = '<input type="text" name="mov_parc[' . $row_mov['id_mov'] . ']" class="sonumeros form-control" data-key="' . $row_mov['id_mov'] . '" style="width: 100px;" />';
                                            } else {
                                                $campoParcelamento = '';
                                            }
                                            ?>
                                            <tr class="valign-middle">
                                                <td><?php echo $row_mov['cod'] . ' -  ' . $row_mov['descicao']; ?></td>
                                                <td style="width:180px" class="text-center"><?= $incid ?></td>
                                                <td>
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="sizing-addon1">R$</span>
                                                        <input type='text' size="5" <?= $valorReadOnly ?>
                                                               name='mov_valor[<?php echo $row_mov['id_mov'] ?>]'
                                                               id='mov_valor[<?php echo $row_mov[id_mov] ?>]'
                                                               class='form-control cred <?php echo $classAuxDistancia ?> result_<?= $row_mov[id_mov] ?>'
                                                               rel='<?php echo $row_mov[id_mov] ?>'
                                                               data-modal='<?php echo $modalReembolsoFaltas; ?>'/>
                                                               <?php if ($infoCalc) { ?>
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-info open_info" type="button"
                                                                        data-key="<?= $row_mov['id_mov'] ?>">&emsp14;<i
                                                                        class="fa fa-info-circle"></i>&emsp14;</button>
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                                <td class="text-center"><input type='checkbox'
                                                                               name='mov_sempre[<?php echo $row_mov['id_mov'] ?>]'
                                                                               value='2' rel='<?php echo $row_mov[id_mov] ?>'/></td>
                                                <td><?php echo $campoTipoQnt; ?></td>
                                                <td><?php echo $campoQnt; ?></td>
                                                <?php if ($cookie) { /* ALLMX */ ?>
                                                    <td><?php echo $campoParcelamento; ?></td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            unset($valorReadOnly);
                                            unset($incid);
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <table class="table table-striped table-hover table-condensed table-bordered essatb"
                                       style="font-size: 14px; margin-top: 20px;">
                                    <thead>
                                        <tr class="bg-primary valign-middle">
                                            <td>COD.</td>
                                            <td>NOME</td>
                                            <td>QUANT.</td>
                                            <td>VALOR</td>
                                            <td>COMPETÊNCIA</td>
                                            <td>INCIDÊNCIA</td>
                                            <td>DELETAR</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($movLancado['CREDITO'] as $movimentos) {
                                            $cor = ($i++ % 2 == 0) ? '#f6f5f5' : '#ecebea';
                                            ?>
                                            <tr class="valign-middle">
                                                <td><?php echo $movimentos['id_movimento']; ?></td>
                                                <td><?php echo $movimentos['nome']; ?></td>
                                                <td><?php echo $movimentos['qnt'] . ' ' . $movimentos['tipo_qnt']; ?></td>
                                                <td> R$ <?php echo number_format($movimentos['valor'], 2, ',', '.'); ?></td>
                                                <td><?php echo $movimentos['competencia']; ?></td>
                                                <td><?php echo $movimentos['incidencia']; ?></td>
                                                <td class="text-center">
                                                    <a href="#" rel="<?php echo $movimentos['id_movimento']; ?>"
                                                       data-clt="<?php echo $clt; ?>" class="excluir btn btn-xs btn-danger">
                                                        <i title="Excluir" class="bt-image fa fa-trash-o"></i>
                                                    </a>
                                                </td>
                                            <tr>
                                            <?php }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="debito">
                                <table class="table table-striped table-hover table-condensed table-bordered"
                                       style="font-size: 14px; margin-top: 20px;">
                                    <thead>
                                        <tr class="bg-danger valign-middle">
                                            <th>Movimento</th>
                                            <th>Incidência</th>
                                            <th>Valor</th>
                                            <th>Sempre</th>
                                            <th>Tipo</th>
                                            <th>Quantidade</th>
                                            <?php if ($cookie) { /* ALLMX */ ?>
                                                <th>Nº Parcelas</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
//                                        $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE (categoria = 'DEBITO' || categoria = 'DESCONTO') AND mov_lancavel = 1 AND id_mov IN ($arrMovSind) ORDER BY descicao");
                                        $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE (categoria = 'DEBITO' || categoria = 'DESCONTO') AND mov_lancavel = 1 AND ignorar_rubrica = 0 ORDER BY descicao");
                                        while ($row_mov = mysql_fetch_assoc($qr_mov)) {

                                            if ($row_mov['incidencia_inss']) {
                                                $incid[] = "<span class='label label-success'>INSS</span>";
                                            }
                                            if ($row_mov['incidencia_irrf']) {
                                                $incid[] = '<span class="label label-warning">IRRF</span>';
                                            }
                                            if ($row_mov['incidencia_fgts']) {
                                                $incid[] = '<span class="label label-info">FGTS</span>';
                                            }

                                            if ($row_mov['incide_dsr']) {
                                                $incid[] = '<span class="label label-default">DSR</span>';
                                            }

                                            $incid = implode(" ", $incid);

                                            if ($row_mov['tipo_qnt_lancavel']) {
                                                $valorReadOnly = 'readonly'; // 2017-06-22 - leonardo - a pedido do jeferson liberar pra imputação manual
                                                $tpQtReadOnly = 'style="pointer-events: none; touch-action: none;" readonly tabindex="-1"';
                                                $qntReadOnly = 'readonly';
                                                $hora_mask = 'hora_mask';
                                                
                                                if ($row_mov['id_mov'] == 236) {
                                                    $selectHoras = 'selected';
                                                    $selectDias = null;
                                                } else if ($row_mov['id_mov'] == 232 || $row_mov['id_mov'] == 293) {
                                                    $selectDias = 'selected';
                                                    $selectHoras = null;
                                                    $hora_mask = '';
                                                } else {
                                                    $qntReadOnly = null;
                                                    $tpQtReadOnly = null;
                                                    $selectDias = null;
                                                    $selectHoras = null;
                                                }
                                                
                                                $i++;
                                                $checked1 = 'checked="checked"';
                                                $campoTipoQnt = "<select $tpQtReadOnly name='tipo_quantidade[{$row_mov['id_mov']}]' class='tipo_qnt form-control'>
                                                                    <option value='1' $selectHoras>Horas</option>
                                                                    <option value='2' $selectDias>Dias</option>
                                                                 </select>  ";
                                                $campoQnt = '<input '.$qntReadOnly.' type="text" name="mov_qtd[' . $row_mov['id_mov'] . ']" class="' . $row_mov['id_mov'] . ' calculo '.$hora_mask.' form-control" data-key="' . $row_mov['id_mov'] . '" style="width: 100px;" />';
                                            } else {
                                                $campoTipoQnt = '';
                                                $campoQnt = '';
                                            }

                                            if ($row_mov['parcelamento']) {
                                                $campoParcelamento = '<input type="text" name="mov_parc[' . $row_mov['id_mov'] . ']" id="mov_parc[' . $row_mov[id_mov] . ']" class="sonumeros form-control" maxlength="2" data-key="' . $row_mov['id_mov'] . '" style="width: 50px;" />';
                                            } else {
                                                $campoParcelamento = '';
                                            }
                                            ?>
                                            <tr class="valign-middle">
                                                <td><?php echo $row_mov['cod'] . ' -  ' . $row_mov['descicao']; ?></td>
                                                <td class="text-center" style="width:180px"><?= $incid ?></td>
                                                <td align="center">
                                                    <?php if ($row_mov['id_mov'] == 60 && 1 == 2) { ?>
                                                        <a href="#" class="tooltip">
                                                            <input <?= $valorReadOnly ?> type='text' size="5"
                                                                                         name='mov_valor[<?php echo $row_mov['id_mov'] ?>]'
                                                                                         id='mov_valor[<?php echo $row_mov[id_mov] ?>]'
                                                                                         class='desc form-control result_<?php echo $row_mov[id_mov] ?>'
                                                                                         rel='<?php echo $row_mov[id_mov] ?>'/>
                                                            <span>
                                                                <img class="callout" src="../imagens/callout.gif"/>
                                                                <strong>Sugerimos que o valor da parcela</strong><br/>
                                                                Não ultrapasse 30% do valor total
                                                            </span>
                                                        </a>
                                                    <?php } else { ?>
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="sizing-addon1">R$</span>
                                                            <input <?= $valorReadOnly ?> type='text' size="5"
                                                                                         name='mov_valor[<?php echo $row_mov['id_mov'] ?>]'
                                                                                         id='mov_valor[<?php echo $row_mov[id_mov] ?>]'
                                                                                         class='desc form-control result_<?php echo $row_mov[id_mov] ?>'
                                                                                         rel='<?php echo $row_mov[id_mov] ?>'/>
                                                                                         <?php if (in_array($row_mov['id_mov'], $arrayFaltas)) { ?>
                                                                <span data-key="<?php echo $row_mov['id_mov'] ?>" class="danger input-group-addon pointer open_calendar"><i class="fa fa-calendar"></i></span>

                                                                                                            <!--                                                        <input <?= $valorReadOnly ?> type='text' size="5"
                                                                                                                                         name='mov_valor[<?php echo $row_mov['id_mov'] ?>]'
                                                                                                                                         id='mov_valor[<?php echo $row_mov[id_mov] ?>]'
                                                                                                                                         class='desc form-control result_<?php echo $row_mov[id_mov] ?>'
                                                                                                                                         rel='<?php echo $row_mov[id_mov] ?>'/>-->

                                                                <!--                                                            <label for="obs"
                                                                                                                                   style="float: left; margin-left: 16px; margin-top: 10px;">Dias
                                                                                                                                Referentes a Falta / Atraso </label>
                                                                                                                            <textarea name="obs_<?php echo $row_mov['id_mov']; ?>"
                                                                                                                                      class="form-control"></textarea>-->
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td align="center"><input type='checkbox'
                                                                          name='mov_sempre[<?php echo $row_mov['id_mov'] ?>]'
                                                                          value='2' rel='<?php echo $row_mov[id_mov] ?>'/></td>
                                                <td align="center"><?php echo $campoTipoQnt; ?></td>
                                                <td align="center"><?php echo $campoQnt; ?></td>
                                                <?php if ($cookie) { /* ALLMX */ ?>
                                                    <td align="center"><?php echo $campoParcelamento; ?></td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            unset($valorReadOnly);
                                            unset($incid);
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <input type="hidden" name="clt" id="clt" value="<?php echo $clt; ?>"/>
                                <input type="hidden" name="regiao" value="<?php echo $regiao; ?>"/>
                                <input type="hidden" name="projeto" value="<?php echo $row_clt['id_projeto']; ?>"/>

                                <table class="table table-striped table-hover table-condensed table-bordered" id="tabela"
                                       style="font-size: 14px; margin-top: 20px;">
                                    <thead>
                                        <tr class="bg-danger valign-middle">
                                            <td>COD.</td>
                                            <td>NOME</td>
                                            <td>QUANT.</td>
                                            <td>VALOR</td>
                                            <td>COMPETÊNCIA</td>
                                            <td>INCIDÊNCIA</td>
                                            <td>OBS</td>
                                            <td>DELETAR</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($movLancado['DEBITO'] as $movimentos) { ?>
                                            <tr class="valign-middle">
                                                <td><?php echo $movimentos['id_movimento']; ?></td>
                                                <td><?php echo (in_array($movimentos['id_mov'],$arrDatasHoras) ? "<span data-mov='{$movimentos['id_mov']}' data-id='{$movimentos['id_movimento']}' class='danger pointer verDatasHoras'>{$movimentos['nome']}</span>&nbsp<i data-mov='{$movimentos['id_mov']}' data-id='{$movimentos['id_movimento']}' class='pointer fa fa-calendar danger verDatasHoras'></i>" : $movimentos['nome']);?></td>
                                                <td><?php echo $movimentos['qnt'] . ' ' . $movimentos['tipo_qnt']; ?></td>
                                                <td>R$ <?php echo number_format($movimentos['valor'], 2, ',', '.'); ?></td>
                                                <td><?php echo $movimentos['competencia']; ?></td>
                                                <td><?php echo $movimentos['incidencia']; ?></td>
                                                <td><?php echo $movimentos['obs']; ?></td>
                                                <td class="text-center">
                                                    <a href="#" rel="<?php echo $movimentos['id_movimento']; ?>"
                                                       class="excluir btn btn-xs btn-danger">
                                                        <i title="Excluir" class="bt-image fa fa-trash-o"></i>
                                                    </a>
                                                </td>
                                            <tr>
                                            <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <span>Previsão de DSR: R$<?php echo $valorDsr['valor_integral'] ?></span>
                </div>

                <div class="botao_enviar"><input type="submit" name="confirmar" class="btn btn-success" value="Confirmar"/>
                </div>

            </div>
        </form>

        <div id="modal_reembolso_faltas">
            <form action="" name="formCadMov" id="formCadMov" method="post">
                <fieldset style="border: 1px solid #ccc; padding: 20px;">
                    <legend style="font-size: 14px;">MOVIMENTO DE REEMBOLSO DE FALTAS</legend>
                    <p style="margin-bottom: 5px; font-size: 12px;">
                        <label style="display: block; text-transform: uppercase;">Quantidade de dias: </label>
                        <input class="validate[required,custom[number]]" type="text" name="quant_dias_reembolso" value=""
                               style="padding: 5px;"/>
                    </p>
                    <p style="margin-bottom: 5px; font-size: 12px;">
                        <label style="display: block; text-transform: uppercase;">Valor: </label>
                        <input class="validate[required] maskMoney" type="text" name="valor_dias_reembolso" value=""
                               style="padding: 5px;"/>
                    </p>
                    <p style="margin: 10px 0px; font-size: 12px;">
                        <input type="button" name="cadastrar" value="Cadastrar" class='cadValorDiasReembolso'
                               style="padding: 8px 15px; text-transform: uppercase;"/>
                    </p>
                    <input type="hidden" name="regiaoMov" value="<?php echo $regiao; ?>"/>
                    <input type="hidden" id='projetoMov' name="projetoMov" value="<?php echo $row_clt[id_projeto]; ?>"/>
                    <input type="hidden" name="mesMov" id="mesMov" value=""/>
                    <input type="hidden" name="anoMov" id="anoMov" value=""/>
                    <input type="hidden" name="cltSelected" id="cltSelected" value="<?php echo $clt; ?>"/>
                </fieldset>
            </form>
        </div>
    </body>
</html>