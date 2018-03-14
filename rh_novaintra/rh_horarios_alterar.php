<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> "; exit;
}

include("../conn.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$horario = $_REQUEST['horario'];
$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$result_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario= '$horario'");
$row_alt_horario = mysql_fetch_assoc($result_horario);

if (!empty($_REQUEST['nome'])) {
    $regiao = $_REQUEST['regiao'];
    $horario = $_REQUEST['horario'];
    $nome = $_REQUEST['nome'];
    $obs = $_REQUEST['obs'];
    $salario = $_REQUEST['salario'];
    $entrada1 = $_REQUEST['entrada1'];
    $saida1 = $_REQUEST['saida1'];
    $entrada2 = $_REQUEST['entrada2'];
    $saida2 = $_REQUEST['saida2'];
    $hora_mes = $_REQUEST['hora_mes'];
    $horas_semanais = $_REQUEST['horas_semanais'];
    $dias_semana = $_REQUEST['dias_semana'];
    $dias_mes = $_REQUEST['dias_mes'];

    $folga1 = $_REQUEST['folga1'];
    $folga2 = $_REQUEST['folga2'];
    $folga3 = $_REQUEST['folga3'];

    if ($folga1 == "1" and $folga2 == "2") {// SEGUNDA A SEXTA
        $folga = "3";
    } elseif ($folga1 == "1") {// FOLGA NO SABADO
        $folga = "1";
    } elseif ($folga2 == "2") {// FOLGA NO DOMINGO
        $folga = "2";
    } elseif ($folga3 == "5") {// PLANTONISTA
        $folga = "5";
    } else {
        $folga = "0";  //SEM FOLGAS ( SEGUNDA À SEGUNDA )
    }

    $id_user = $_COOKIE['logado'];
    $data_cad = date('Y-m-d');

    $salario = explode("-", $salario);

    //-- INICIANDO O CALCULO DO SALARIO PARA RETIRAR O VALOR DIARIO E O VALOR HORA

    $diaria = $salario[0] / 30;
    $hora = $diaria / 8;

    $diaria = str_replace(",", ".", $diaria);
    $diaria_f = number_format($diaria, 2, ",", ".");

    $hora = str_replace(",", ".", $hora);
    $hora_f = number_format($hora, 2, ",", ".");

    mysql_query("
    UPDATE rh_horarios 
    SET nome='$nome', obs='$obs', entrada_1='$entrada1', saida_1='$saida1', entrada_2='$entrada2', saida_2='$saida2',horas_semanais='$horas_semanais',dias_semana='$dias_semana',horas_mes= $hora_mes, salario='$salario[0]',funcao='$salario[1]',valor_dia='$diaria_f',valor_hora='$hora_f',folga='$folga', dias_mes='$dias_mes'
    WHERE id_horario = $horario");

    print "
    <script>
        alert (\"Informações Alteradas com sucesso\");
        location.href=\"rh_horarios.php?regiao=$regiao\"
    </script>";
    exit;
} 

if ($row_alt_horario['folga'] == "1") {
    $folgaCheck1 = 'checked';
} elseif ($row_alt_horario['folga'] == "2") {
    $folgaCheck2 = 'checked';
} elseif ($row_alt_horario['folga'] == "3") {
    $folgaCheck1 = $folgaCheck2 = 'checked';
} elseif ($row_alt_horario['folga'] == "5") {
    $folgaCheck3 = 'checked';
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Edição de Horário");
$breadcrumb_pages = array("Gestão de RH"=>"index.php", "Controle de Horários"=>"rh_horarios.php"); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Edição de Horário</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Edição de Horário</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <form action="" method="post" name="form1" id="form1" class="form-horizontal">
                <div class="panel panel-warning">
                    <div class="panel-heading">DADOS DO HOR&Aacute;RIO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-4">
                                <input name="nome" type="text" id="nome" class="form-control" onChange="this.value=this.value.toUpperCase()" value="<?= $row_alt_horario['nome'] ?>">
                            </div>
                            <label class="col-xs-2 control-label">Atividade:</label>
                            <div class="col-xs-4">
                                <select name="salario" id='salario' class='form-control'>
                                    <option value=0>Selecione uma Atividade</option>
                                    <?php
                                    $sql_curso = "select * from curso where id_regiao = '$regiao' and tipo = '2'";
                                    $sql_result_curso = mysql_query($sql_curso);
                                    while ($dados_curso = mysql_fetch_array($sql_result_curso)) {
                                        $curso_id = $dados_curso["id_curso"];
                                        $curso = $dados_curso["nome"];
                                        if ($curso_id == $row_alt_horario["funcao"]) { ?>
                                            <option selected value='<?= $dados_curso['salario'] . "-" . $dados_curso[0] ?>' > <?= $dados_curso['id_curso'] . " - " . $dados_curso['campo2'] . " / " . $dados_curso['salario'] ?></option>
                                        <?php } else { ?>
                                            <option value='<?= $dados_curso['salario'] . "-" . $dados_curso[0] ?>' > <?= $dados_curso['id_curso'] . " - " . $dados_curso['campo2'] . " / " . $dados_curso['salario'] ?></option>
                                        <?php }
                                    } ?> 
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Observações:</label>
                            <div class="col-xs-10">
                                <input name="obs" type="text" id="obs" class="form-control" value="<?= $row_alt_horario['obs'] ?>" onChange="this.value = this.value.toUpperCase()">
                            </div>
                        </div>
                        <div class="form-group valign-middle">
                            <label class="col-xs-2 control-label">Preenchimento:</label>
                            <div class="col-xs-2">
                                <div class="input-group">
                                    <div class="input-group-addon">Entrada</div>
                                    <input name="entrada1" type="text" id="entrada1" class="form-control no-padding-hr text-center" OnKeyUP="formatar('##:##:##', this)" value="<?= $row_alt_horario['entrada_1'] ?>" maxlength="8" data-key="<?=$row_alt_horario['id_horario']?>" />
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <div class="input-group-addon">Sa&iacute;da Almo&ccedil;o</div>
                                    <input name="saida1" type="text" id="saida1" class="form-control no-padding-hr text-center" OnKeyUP="formatar('##:##:##', this);" value="<?= $row_alt_horario['saida_1'] ?>" maxlength="8" data-key="<?php echo $row_alt_horario['id_horario']; ?>" />
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="input-group">
                                    <div class="input-group-addon">Retorno Almo&ccedil;o</div>
                                    <input name="entrada2" type="text" id="entrada2" class="form-control no-padding-hr text-center" OnKeyUP="formatar('##:##:##', this)" value="<?= $row_alt_horario['entrada_2'] ?>" maxlength="8" data-key="<?=$row_alt_horario['id_horario']?>" />
                                </div>
                            </div>
                            <div class="col-xs-2">
                                <div class="input-group">
                                    <div class="input-group-addon">Sa&iacute;da</div>
                                    <input name="saida2" type="text" id="saida2" class="form-control no-padding-hr text-center" OnKeyUP="formatar('##:##:##', this)" value="<?= $row_alt_horario['saida_2'] ?>" maxlength="8" data-key="<?=$row_alt_horario['id_horario']?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Horas M&ecirc;s:</label>
                            <div class="col-xs-1">
                                <input name="hora_mes" type="text" id="hora_mes" class="form-control" value="<?= $row_alt_horario['horas_mes'] ?>" data-key="<?= $row_alt_horario['id_horario'] ?>" />
                            </div>
                            <label class="col-xs-2 control-label">Horas Semanais:</label>
                            <div class="col-xs-1">
                                <input name="horas_semanais" type="text" id="horas_semanais" class="form-control" value="<?= $row_alt_horario['horas_semanais'] ?>" data-key="<?= $row_alt_horario['id_horario'] ?>" />
                            </div>
                            <label class="col-xs-2 control-label">Dias Mês:</label>
                            <div class="col-xs-1">
                                <input name="dias_mes" type="text" id="dias_mes" class="form-control" value="<?= $row_alt_horario['dias_mes'] ?>" data-key="<?= $row_alt_horario['id_horario'] ?>" />
                            </div>
                            <label class="col-xs-2 control-label">Dias Semana:</label>
                            <div class="col-xs-1">
                                <input name="dias_semana" type="text" id="dias_semana" class="form-control" value="<?= $row_alt_horario['dias_semana'] ?>" data-key="<?= $row_alt_horario['id_horario'] ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Folgas:</label>
                            <div class="col-xs-10">
                                <div class="col-xs-2 checkbox"><label><input name='folga1' <?=$folgaCheck1?> type='checkbox' id='folga1' value='1'>S&aacute;bado</label></div>
                                <div class="col-xs-2 checkbox"><label><input name='folga2' <?=$folgaCheck2?> type='checkbox' id='folga2' value='2'>Domingo</label></div>
                                <div class="col-xs-2 checkbox"><label><input name='folga3' <?=$folgaCheck3?> type='checkbox' id='folga3' value='5'>Plantonista</label></div>
                            </div>
                        </div>
                        <div class="form-group hidden">
                            <label class="col-xs-2 control-label">SELECIONE:</label>
                            <div class="col-xs-10">
                                <input name="arquivo" class="form-control no-border" type="file" id="arquivo" />
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-center">
                        <input type="hidden" value="<?= $regiao ?>" name="regiao">
                        <input type="hidden" value="<?= $horario ?>" name="horario">
                        <input type="submit" class="btn btn-primary" name="gerar" id="gerar" value="ALTERAR HOR&Aacute;RIO">
                    </div>
                </div>
            </form>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../jquery/priceFormat.js" type="text/javascript"></script>
        <script type="text/javascript">
            function formatar(mascara, documento) {
                var i = documento.value.length;
                var saida = mascara.substring(0, 1);
                var texto = mascara.substring(i)

                if (texto.substring(0, 1) != saida) {
                    documento.value += texto.substring(0, 1);
                }
            }
        </script> 
    </body>
</html>
