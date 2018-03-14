<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if ((isset($_REQUEST['voltar']) && $_REQUEST['voltar'] == "voltar") || empty($_REQUEST['horario'])) {
    header('Location: index.php');
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$id_horario = $_REQUEST['horario'];

$usuario = carregaUsuario();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        
$horarioQuery = montaQuery("rh_horarios
    LEFT JOIN tipo_jornada ON (rh_horarios.tpJornada = tipo_jornada.id_jornada)
    LEFT JOIN tempo_parcial ON (rh_horarios.tmpParc = tempo_parcial.id_tmpParc)
    ", "id_horario, rh_horarios.nome, tpJornada, dscTpJorn, tmpParc, perHorFlexivel, obs, entrada_1, saida_1, entrada_2, saida_2, horas_semanais, horas_mes, dias_semana, dias_mes, folga, adicional_noturno, horas_noturnas, porcentagem_adicional, tipo_jornada.nome as tipoJornada, tempo_parcial.nome as tmpParcNome, plantonista", "id_horario = {$id_horario} AND status_reg = 1", 'id_horario ASC', null, '', false);
$horario = mysql_fetch_assoc($horarioQuery);
if (!isset($horario)) {
    $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar dados do horário. Contate um administrador.");
    header('Location: index.php');
    exit;
}
$cltHorarioQuery = montaQuery("curso_clt_horario", "entrada1, saida1, entrada2, saida2, folga, dia_semana, variavel, tpInterv", "id_horario = '{$id_horario}'", "dia_semana ASC", null, '', true);
$horarioSemanas = mysql_num_rows($cltHorarioQuery);
while ($cltHorarioWhile = mysql_fetch_assoc($cltHorarioQuery)) {
    foreach ($cltHorarioWhile as $key => $value) {
        // DÁ SPLIT NOS HORÁRIOS SE O CAMPO FOR DE HORÁRIO
        $horario['horarios_alt'][$cltHorarioWhile['dia_semana']][$key] = (in_array($key,array('entrada1','entrada2','saida1','saida2'))) ? substr(split(' ', $value)[1], 0, 5) : $value;
    }
}

session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Horário ".$horario['nome']);
$breadcrumb_pages = array(/*"Gestão de RH"=>"../../rh", */"Gestão de Horário"=>"index.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Horário <?= $horario['nome']?></title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Horário <?= $horario['nome']; ?></small></h2></div>
                </div>
            </div>
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Dados de Horário
                        <input type="hidden" id="horario" name="horario" value="" />
                    </div>
                    <div class="panel-body">
                        <div class="panel-body">
                            <div class="col-xs-6 no-padding-l border-r">
                                <p><label>Nome do Horário:</label> <?= $horario['nome']; ?></p>
                                <p><label>Tipo de Jornada:</label> <?= $horario['tipoJornada'].' - '.$horario['tpJornada']; ?></p>
                                <?= ($horario['tpJornada'] == 9) ? '<p><label>Descrição da jornada:</label> '.$horario['dscTpJorn'].'</p>' : ''; ?>
                                <p><label>Tipo de contrato em tempo parcial:</label> <?= $horario['tmpParcNome'].' - '.$horario['tmpParc']; ?></p>
                                <p><label>Permite flexibilidade:</label> <?= (!empty($horario['perHorFlexivel'])) ? 'Sim' : 'Não'; ?></p>
                                <p><label>Observações:</label> <?= (!empty($horario['obs'])) ? $horario['obs'] : "Não possui"; ?></p> 
                            </div>
                            <div class="col-xs-6 no-padding-r">
                                <p><label>Horas Semanais:</label> <?= $horario['horas_semanais']; ?></p>
                                <p><label>Horas Mensais:</label> <?= $horario['horas_mes']; ?></p>
                                <p><label>Dias Semanais:</label> <?= $horario['dias_semana']; ?></p>
                                <p><label>Dias Mensais:</label> <?= $horario['dias_mes']; ?></p>
                                <p><label>Adicional Noturno:</label> <?= (!empty($horario['adicional_noturno'])) ? $horario['horas_noturnas']." horas" : 'Não possui'; ?></p>
                                <?= (!empty($horario['adicional_noturno'])) ? '<p><label>Percentual Noturno:</label> '.($horario['porcentagem_adicional'] * 100).'%</p>' : ''; ?>
                                <p><label>Plantonista:</label> <?= (!empty($horario['plantonista'])) ? 'Sim' : 'Não'; ?></p>
                            </div>
                            <legend>Horas</legend>
                            <div class="col-xs-12 no-padding-hr">
                                <!-- imprime horário padrão -->
                                <p>
                                    <label>Horário Padrão:</label>
                                    De <span class="tr-bg-active"><?= substr($horario['entrada_1'], 0, 5); ?></span> às <span class="tr-bg-active"><?= substr($horario['saida_1'], 0, 5); ?></span>.
                                    De <span class="tr-bg-active"><?= substr($horario['entrada_2'], 0, 5); ?></span> às <span class="tr-bg-active"><?= substr($horario['saida_2'], 0, 5); ?></span>.
                                </p>
                                <!-- imprime horários -->
                                <?php
                                if ($horarioSemanas < 7) {
                                ?>
                                <p>
                                    <label>
                                        Nenhum dado de horário semanal cadastrado.
                                    </label>
                                </p>
                                <?php
                                }
                                else {
                                    for ($contador = 1; $contador <= 7; ++$contador) { ?>
                                <p>
                                    <label><?= (!empty($horario['horarios_alt'][$contador]['variavel']) && !empty($horario['perHorFlexivel'])) ? "Variável" : ucfirst(strftime('%A', strtotime('Sunday +'.$contador.' day'))); ?>:</label>
                                    De <span class="tr-bg-active"><?= substr($horario['horarios_alt'][$contador]['entrada1'], 0, 5); ?></span> às <span class="tr-bg-active"><?= substr($horario['horarios_alt'][$contador]['saida1'], 0, 5); ?></span>.
                                    De <span class="tr-bg-active"><?= substr($horario['horarios_alt'][$contador]['entrada2'], 0, 5); ?></span> às <span class="tr-bg-active"><?= substr($horario['horarios_alt'][$contador]['saida2'], 0, 5); ?></span>.
                                </p>
                                <?php } } ?>
                            </div>
                        </div>
                    <div class="panel-footer text-right">
                        <button type="button" class="btn btn-danger" name="voltar" id="voltar" value="voltar"><span class="fa fa-step-backward"></span> Voltar</button>
                        <button type="submit" class="btn btn-primary" value="edicao" name="form" id="editarHorario" data-horario="<?= $id_horario ?>"><span class="fa fa-pencil"></span> Editar</button>
                    </div>
                </div>
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#editarHorario").click(function(e){
                    var horario = $(this).data("horario");
                    
                    if (horario.length === 0 || horario < 1){
                        bootAlert("ID do horário não encontrado. Contate um administrador.", "ERRO", null, 'warning');
                        e.preventDefault();
                    }
                    else {
                        $("#horario").val(horario);
                        $("#form1").attr('action','form_horario.php');
                        $("#form1").submit();
                    }
                });
                
                // BOTÃO DE VOLTAR PÁGINA
                $("#voltar").click(function () {
                    $("#form1").attr('action', 'index.php').submit();
                });
            });
        </script>
    </body>
</html>