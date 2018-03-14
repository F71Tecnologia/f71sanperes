<?php
require_once ("control_horario.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?= $nome_pagina ?></title>
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
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <link href="../../rh_novaintra/horario/control_horario.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?= $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
            <?php if (!empty($_SESSION['MESSAGE'])) { ?>
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="alert alert-dismissable alert-warning">
                    <?= $_SESSION['MESSAGE']; session_destroy(); ?>
                </div>
            <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">Horário</div>
                    <div class="panel-body fieldsets horario">
                        <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao; ?>" />
                        <input type="hidden" name="horario" id="horario" value="<?= $id_horario; ?>" />
                        <fieldset class="horario">
                            <div class="form-group">
                                <label for="nome" class="col-xs-2 control-label">Nome do Horário:</label>
                                <div class="col-xs-10">
                                    <input type="text" name="nome" id="nome" class="form-control validate[required] " value="<?= $horario['nome']; ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tpJornada" class="col-xs-2 control-label">Tipo da Jornada:</label>
                                <div class="col-xs-10">
                                    <?= montaSelect($arrJornadas, $horario['tpJornada'], "name='tpJornada' id='tpJornada' class='validate[required,custom[select]] form-control'") ?>
                                </div>
                            </div>
                            <div class="form-group div_jornada <?= ($horario['tpJornada'] != 9) ? "hide" : ""; ?>">
                                <label for="dscTpJorn" class="col-xs-2 control-label">Descrição da Jornada:</label>
                                <div class="col-xs-10">
                                    <input type="text" name="dscTpJorn" id="dscTpJorn" maxlength="100" class="form-control <?= ($horario['tpJornada'] == 9) ? "validate[required]" : ""; ?>" value="<?= (!empty($horario['dscTpJorn'])) ? $horario['dscTpJorn'] : ''; ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tmpParc" class="col-xs-2 control-label">Tipo de contrato em tempo parcial:</label>
                                <div class="col-xs-10">
                                    <?= montaSelect($arrTmpParc, $horario['tmpParc'], "name='tmpParc' id='tmpParc' class='validate[required,custom[select]] form-control'") ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="f_sim" class="col-xs-2 control-label">Permite flexibilidade?</label>
                                <div class="col-xs-2">
                                    <div class="radio">
                                        <label><input type="radio" name="perHorFlexivel" value="1" id="f_sim" class="flexibilidade" <?= (!empty($horario['perHorFlexivel'])) ? 'checked="checked"' : ''; ?> /> Sim</label>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="radio">
                                        <label><input type="radio" name="perHorFlexivel" value="0" id="f_nao"" class="flexibilidade" <?= (empty($horario['perHorFlexivel']) OR !isset($horario['perHorFlexivel'])) ? 'checked="checked"' : ''; ?> /> Não</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="obs" class="col-xs-2 control-label">Observações:</label>
                                <div class="col-xs-10">
                                    <input type="text" name="obs" id="obs" class="form-control" value="<?= $horario['obs']; ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-2 control-label"></label>
                                <div class="col-xs-1 text-center text-bold">Entrada</div>
                                <div class="col-xs-1 text-center text-bold">Início Intervalo</div>
                                <div class="col-xs-1 text-center text-bold">Término Intervalo</div>
                                <div class="col-xs-1 text-center text-bold">Saída</div>
                                <div class="col-xs-1 text-center text-bold">Folga?</div>
                                <div class="col-xs-1 text-center text-bold">Horário Variável?</div>
                                <div class="col-xs-1 text-center text-bold">Intervalo Variável?</div>
                            </div>
                            <div class="form-group">
                                <label for="entrada_1" class="col-xs-2 control-label">Horário Padrão:</label>
                                <div class="col-xs-1"><input type="text" name="entrada_1" id="entrada_1" class="form-control validate[required]" placeholder="00:00" value="<?= substr($horario['entrada_1'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"><input type="text" name="saida_1" id="saida_1" class="form-control validate[required]" placeholder="00:00" value="<?= substr($horario['saida_1'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"><input type="text" name="entrada_2" id="entrada_2" class="form-control validate[required]" placeholder="00:00" value="<?= substr($horario['entrada_2'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"><input type="text" name="saida_2" id="saida_2" class="form-control validate[required]" placeholder="00:00" value="<?= substr($horario['saida_2'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"></div>
                                <div class="col-xs-1"></div>
                            </div>
                            <!-- horários diferenciados -->
                            <?php for ($contador = 1; $contador <= 7; ++$contador) { ?>
                            <div class="form-group" data-day="<?= $contador ?>">
                                <label for="horarios_alt[<?= $contador  ?>][entrada1]" data-day="<?= $contador ?>" class="col-xs-2 control-label"><?= (!empty($horario['horarios_alt'][$contador]['variavel']) && !empty($horario['perHorFlexivel'])) ? "Variável" : ucfirst(split("-", strftime('%A', strtotime('Sunday +'.$contador.' day')))[0]); ?>:</label>
                                <div class="col-xs-1"><input type="text" name="horarios_alt[<?= $contador  ?>][entrada1]" id="horarios_alt[<?= $contador  ?>][entrada1]" class="form-control preenchimento" data-day="<?= $contador ?>" placeholder="00:00" value="<?= (strpos($horario['horarios_alt'][$contador]['entrada1'], " ") !== false) ? substr(split(' ', $horario['horarios_alt'][$contador]['entrada1'])[1], 0, 5) : substr($horario['horarios_alt'][$contador]['entrada1'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"><input type="text" name="horarios_alt[<?= $contador ?>][saida1]" id="horarios_alt[<?= $contador ?>][saida1]" class="form-control preenchimento" data-day="<?= $contador ?>" placeholder="00:00" value="<?= (strpos($horario['horarios_alt'][$contador]['entrada1'], " ") !== false) ? substr(split(' ', $horario['horarios_alt'][$contador]['saida1'])[1], 0, 5) : substr($horario['horarios_alt'][$contador]['saida1'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"><input type="text" name="horarios_alt[<?= $contador ?>][entrada2]" id="horarios_alt[<?= $contador ?>][entrada2]" class="form-control preenchimento" data-day="<?= $contador ?>" placeholder="00:00" value="<?= (strpos($horario['horarios_alt'][$contador]['entrada1'], " ") !== false) ? substr(split(' ', $horario['horarios_alt'][$contador]['entrada2'])[1], 0, 5) : substr($horario['horarios_alt'][$contador]['entrada2'], 0, 5); ?>" /></div>
                                <div class="col-xs-1"><input type="text" name="horarios_alt[<?= $contador ?>][saida2]" id="horarios_alt[<?= $contador ?>][saida2]" class="form-control preenchimento" data-day="<?= $contador ?>" placeholder="00:00" value="<?= (strpos($horario['horarios_alt'][$contador]['entrada1'], " ") !== false) ? substr(split(' ', $horario['horarios_alt'][$contador]['saida2'])[1], 0, 5) : substr($horario['horarios_alt'][$contador]['saida2'], 0, 5); ?>" /></div>
                                <div class="col-xs-1 text-center"><input type="checkbox" name="horarios_alt[<?= $contador ?>][folga]" id="horarios_alt[<?= $contador ?>][folga]" value="1" class="folga_new" <?= $horario['horarios_alt'][$contador]['folga'] == 1 ? 'checked="checked"' : "" ?> /></div>
                                <div class="col-xs-1 text-center"><input type="checkbox" name="horarios_alt[<?= $contador ?>][variavel]" id="horarios_alt[<?= $contador ?>][variavel]" value="1" class="variavel" <?= (!empty($horario['horarios_alt'][$contador]['variavel']) && !empty($horario['perHorFlexivel'])) ? 'checked="checked"' : ""; ?> <?= empty($horario['perHorFlexivel']) ? 'disabled="disabled"' : '' ?> /></div>
                                <div class="col-xs-1 text-center"><input type="checkbox" name="horarios_alt[<?= $contador ?>][tpInterv]" id="horarios_alt[<?= $contador ?>][tpInterv]" value="2" class="IntervVariavel" <?= $horario['horarios_alt'][$contador]['tpInterv'] == 2 ? 'checked="checked"' : "" ?> /></div>
                            </div>
                            <?php } ?>
                            <div class="form-group">
                                <label for="horas_semanais" class="col-xs-2 control-label">Horas Semanais:</label>
                                <div class="col-xs-3">
                                    <input type="text" name="horas_semanais" id="horas_semanais" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $horario['horas_semanais']; ?>" />
                                </div>
                                <label for="horas_mes" class="col-xs-2 control-label">Horas Mensais:</label>
                                <div class="col-xs-3">
                                    <input type="text" name="horas_mes" id="horas_mes" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $horario['horas_mes']; ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dias_semana" class="col-xs-2 control-label">Dias Semanais:</label>
                                <div class="col-xs-3">
                                    <input type="text" name="dias_semana" id="dias_semana" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $horario['dias_semana']; ?>" />
                                </div>
                                <label for="dias_mes" class="col-xs-2 control-label">Dias Mensais:</label>
                                <div class="col-xs-3">
                                    <input type="text" name="dias_mes" id="dias_mes" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $horario['dias_mes']; ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-2 control-label">Folgas:</label>
                                <div class="col-xs-2">
                                    <div class="checkbox">
                                        <label><input class="folga_old" type="checkbox" name="folgaOldSabado" id="folgaOldSabado" data-day="6" value="1" <?= in_array($horario['folga'], [1,3]) ? 'checked="checked"' : ''; ?> /> Sábado</label>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="checkbox">
                                        <label><input class="folga_old" type="checkbox" name="folgaOldDomingo" id="folgaOldDomingo" data-day="7" value="2" <?= in_array($horario['folga'], [2,3]) ? 'checked="checked"' : ''; ?> /> Domingo</label>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="checkbox">
                                        <label><input class="folga_old" type="checkbox" name="plantonista" id="plantonista" data-day="8" value="5" <?= $horario['folga'] == 5 ? 'checked="checked"' : ''; ?> /> Plantonista</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="n_sim" class="col-xs-2 control-label">Adicional Noturno:</label>
                                <div class="col-xs-2">
                                    <div class="radio">
                                        <label><input type="radio" name="adicional_noturno" value="1" id="n_sim" class="adicional_noturno" <?= !empty($horario['adicional_noturno']) ? 'checked="checked"' : ''; ?> /> Sim</label>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <div class="radio">
                                        <label><input type="radio" name="adicional_noturno" value="0" id="n_nao" class="adicional_noturno" <?= (empty($horario['adicional_noturno']) OR !isset($horario['adicional_noturno'])) ? 'checked="checked"' : ''; ?> /> Não</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group horas_noturnas <?= (empty($horario['horas_noturnas'])) ? "hide" : ""; ?>">
                                <label for="horas_noturnas" class="col-xs-2 control-label">Horas Noturno:</label>
                                <div class="col-xs-3">
                                    <input type="text" name="horas_noturnas" id="horas_noturnas" maxlength="4" class="form-control <?= (!empty($horario['horas_noturnas'])) ? 'validate[required,custom[onlyNumberSp]]' : null ?>" value="<?= (!empty($horario['horas_noturnas'])) ? $horario['horas_noturnas'] : ''; ?>" />
                                </div>
                                <label for="porcentagem_adicional" class="col-xs-2 control-label">Porcentagem:</label>
                                <div class="col-xs-2">
                                    <?= montaSelect(['-1'=>"« Selecione »",'0.2'=>"20%",'0.3'=>"30%",'0.35'=>"35%",'0.4'=>"40%",'0.5'=>"50%"],$horario['porcentagem_adicional'],"id='porcentagem_adicional' class='form-control validate[required,custom[select]]' name='porcentagem_adicional'"); ?>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="button" class="btn btn-danger" name="voltar" id="voltar" value="Voltar">
                        <button type="submit" class="btn btn-primary" name="post" value="<?= $form_post ?>"><i class="fa fa-save"></i> SALVAR</button>
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
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        <script src="../../rh_novaintra/horario/control_horario.js" type="text/javascript"></script>
    </body>
</html>