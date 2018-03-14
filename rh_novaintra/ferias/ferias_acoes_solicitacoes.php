<?php
/*
 * PHP-DOC - ferias-acoes-solicitacoes.php
 * 
 * @Sinesio Luiz 
 * 
 */
header("Content-Type: text/html; charset=ISO-8859-1", true);

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/global.php');
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

/*
 * AGENDAMENTO DE FERIAS
 */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == 'agendarFerias') {

    $retorno = array("status" => 0);

    if (isset($_REQUEST['id_ferias_programada']) && !empty($_REQUEST['id_ferias_programada'])) {
        $queryGrava = "UPDATE rh_ferias_programadas SET inicio = '{$_REQUEST['inicio']}', fim = '{$_REQUEST['fim']}' WHERE id_ferias_programadas = '{$_REQUEST['id_ferias_programada']}'";
    } else {
        $queryGrava = "INSERT INTO rh_ferias_programadas (id_clt,inicio,fim,id_funcionario,decimo_terceiro) VALUES ('{$_REQUEST['id_clt']}','{$_REQUEST['inicio']}','{$_REQUEST['fim']}','{$_REQUEST['logado']}','{$_REQUEST['adiantamento_13']}')";
    }
    
    $sqlGrava = mysql_query($queryGrava) or die("Erro ao criar agendamento de ferias");
    //$sqlGrava = true;
    if ($sqlGrava) {
        $queryUpdate = "UPDATE rh_ferias_solicitacao SET status = '3' WHERE id_clt = '{$_REQUEST['id_clt']}' AND id_solicitacao = '{$_REQUEST['id_solicitacao']}'";
        $mysqlUpdate = mysql_query($queryUpdate) or die("Erro ao fazer update");
        //$mysqlUpdate = true;
        if ($mysqlUpdate) {
            $retorno = array("status" => 1);
        }
    }

    echo json_encode($retorno);
    exit();
}
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == 'negarFerias') {

    $retorno = array("status" => 0);

    $queryGrava = "INSERT INTO rh_ferias_programadas (id_clt,inicio,fim,id_funcionario,decimo_terceiro) VALUES ('{$_REQUEST['id_clt']}','{$_REQUEST['inicio']}','{$_REQUEST['fim']}','{$_REQUEST['logado']}','{$_REQUEST['adiantamento_13']}')";
    $sqlGrava = mysql_query($queryGrava) or die("Erro ao criar agendamento de ferias");
    //$sqlGrava = true;
    if ($sqlGrava) {
        $queryUpdate = "UPDATE rh_ferias_solicitacao SET status = '2' WHERE id_clt = '{$_REQUEST['id_clt']}' AND id_solicitacao = '{$_REQUEST['id_solicitacao']}'";
        $mysqlUpdate = mysql_query($queryUpdate) or die("Erro ao fazer update");
        //$mysqlUpdate = true;
        if ($mysqlUpdate) {
            $retorno = array("status" => 1);
        }
    }

    echo json_encode($retorno);
    exit();
}

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$clt = $_REQUEST['id_clt'];
$projeto = $_REQUEST['projeto'];
$mes = str_pad($_REQUEST['mes'], 2, 0, STR_PAD_LEFT);
$ano = $_REQUEST['ano'];


$feriasObj = new Ferias();
/**
 * RESOURCE
 */
$reClts = $feriasObj->listaSolicitacoesFerias($projeto, $mes, $ano, $clt);

/**
 * ARRAY DE DADOS 
 */
$arrayClt = array();
while ($rows = mysql_fetch_assoc($reClts)) {
    $arrayClt = $rows;
}

//print_r($arrayClt);
?>

<br>
<span class="pull-right"></span>
<h4 class="valign-middle"></h4>

<div class="panel panel-default">
    <div class="panel-heading">
        Solicitação de Férias
    </div>
    <div class="panel-body">
        <form action="">
            <div class="col-lg-12">
                <label for="funcionario(a)">Funcionário(a):</label>
                <span><?php echo $arrayClt['nome']; ?></span><br />
            </div>

            <div class="col-lg-12">
                <label for="aquisitivo">Periodo Aquisitivo:</label>
                <span><?php echo $arrayClt['aquisitivo_iniBR'] . " à " . $arrayClt['aquisitivo_fimBR']; ?></span><br />
            </div>

            <?php if ($arrayClt['status_solicitacao'] == 4) { ?>
                <div class="col-lg-12">
                    <label for="solicitado">Periodo Solicitado:</label>
                    <span style="text-decoration: line-through; color: #ccc"><?php echo $arrayClt['ferias_iniBR'] . " à " . $arrayClt['ferias_fimBR']; ?></span><br />
                </div>
                <div class="col-lg-12">
                    <label for="solicitado">Novo Periodo Solicitado:</label>
                    <span><?php echo $arrayClt['ferias_ini_altBR'] . " à " . $arrayClt['ferias_fim_altBR']; ?></span><br />
                </div>
            <?php } else { ?>
                <div class="col-lg-12">
                    <label for="solicitado">Periodo Solicitado:</label>
                    <span><?php echo $arrayClt['ferias_iniBR'] . " à " . $arrayClt['ferias_fimBR']; ?></span><br />
                </div>
            <?php } ?>

            <div class="col-lg-12">
                <label for="solicitado">Adiantamento de 13°:</label>
                <span><?php if ($arrayClt['adiantamento_13'] == 1) {
                echo "Sim";
            } else {
                echo "Não";
            } ?></span><br />
            </div>

            <div class="col-lg-12">
                <label for="observicao">Observação:</label><br /> 
                <span><?php echo $arrayClt['obs']; ?></span><br /><br />
            </div>

<?php if ($arrayClt['status_solicitacao'] != 3) { ?>
                <div class="col-lg-12">
                    <label for="solicitado">Deseja conceder essas férias para a funcionário ?</label><br />
                    <input type="button" class="btn btn-success agendarFerias" name="sim" value="SIM" />
                    <input type="button" class="btn btn-danger negarFerias" name="nao" value="Não" />
                </div>           
<?php } ?>

            <input type="hidden" name="decimo_13" id="decimo_13" value="<?php echo $arrayClt['adiantamento_13']; ?>" />
            <input type="hidden" name="id_solicitacao" id="id_solicitacao" value="<?php echo $arrayClt['id_solicitacao'] ?>" />
            <input type="hidden" name="clt" id="clt" value="<?php echo $arrayClt['id_clt'] ?>" />

            <?php
            $feriasInicio = "";
            $feriasFinal = "";
            if ($arrayClt['status_solicitacao'] == 4) {
                $feriasInicio = $arrayClt['ferias_ini_alt'];
                $feriasFinal = $arrayClt['ferias_fim_alt'];
            } else {
                $feriasInicio = $arrayClt['ferias_ini'];
                $feriasFinal = $arrayClt['ferias_fim'];
            }
            ?>

            <input type="hidden" name="inicio" id="inicio" value="<?php echo $feriasInicio; ?>" />
            <input type="hidden" name="fim" id="fim" value="<?php echo $feriasFinal; ?>" />
            <input type="hidden" name="id_ferias_programada" id="id_ferias_programada" value="<?= $arrayClt['id_ferias_programada'] ?>">
        </form>
    </div>
</div>
