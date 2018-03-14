<?php
require_once ("control_curso.php");
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
        <link href="../../rh_novaintra/curso/control_curso.css" rel="stylesheet" type="text/css">
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
                    <div class="panel-heading text-bold">Dados da Função</div>
                    <div class="panel-body">
                        <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao; ?>" />
                        <input type="hidden" name="projeto" id="projeto" value="<?= $id_projeto; ?>" />
                        <input type="hidden" name="curso" id="curso" value="<?= $id_curso; ?>" />
                        <fieldset id="func1">
                            <div class="form-group">
                                <label for="nome" class="col-xs-2 control-label">Nome da Função:</label>
                                <div class="col-xs-10">
                                    <input type="text" name="nome" id="nome" class="form-control validate[required]" value="<?= $curso['nome'] ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipo" class="col-xs-2 control-label">Tipo de Contratação:</label>
                                <div class="col-xs-10">
                                    <select name="tipo" id="tipo" class="validate[required,custom[select]] form-control" <?= ($form_post == 'edicao') ? 'readonly="readonly"' : ''?>>
                                        <option value="-1">Selecione</option>
                                        <option value="1" <?= $curso['tipo'] == 1 ? 'selected="selected"' : ''; ?>>Autônomo</option>
                                        <option value="2" <?= $curso['tipo'] == 2 ? 'selected="selected"' : ''; ?>>CLT</option>
                                        <option value="3" <?= $curso['tipo'] == 3 ? 'selected="selected"' : ''; ?>>Cooperado / Terceirizado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label for="id_sindicato" class="col-xs-2 control-label">Sindicato:</label>
                                <div class="col-xs-10">
                                    <?= montaSelect($arrSindicatos, $curso['id_sindicato'], 'name="id_sindicato" id="id_sindicato" class="validate[required,custom[select]] form-control '.  $tipoBlock1 . '"') ?>
                                </div>
                            </div>
                            <div class="form-group">                                
                                <label for="horista_plantonista" class="col-xs-2 control-label">Função para Horista:</label>
                                <div class="col-xs-2">
                                    <select name="horista_plantonista" id="horista_plantonista" class="validate[required,custom[select]] form-control">
                                        <option value="0" <?= $curso['horista_plantonista'] == 0 ? 'selected="selected"' : ''; ?>>Não</option>
                                        <option value="1" <?= $curso['horista_plantonista'] == 1 ? 'selected="selected"' : ''; ?>>Sim</option>
                                    </select>
                                </div>
                                <label for="valor_hora" class="col-xs-2 control-label horista_plantonista  <?= empty($curso['horista_plantonista']) ? "hide" : "" ?>">Valor/Hora:</label>
                                <div class="col-xs-2 horista_plantonista  <?= empty($curso['horista_plantonista']) ? "hide" : "" ?>">
                                    <input type="text" name="valor_hora" id="valor_hora" class="form-control decimal <?= ($curso['horista_plantonista'] == 1) ? "validate[required]" : "" ?>" maxlength="17" placeholder="0,00" value="<?= (!empty($curso['valor_hora'])) ? number_format($curso['valor_hora'],2,',','.') : '' ?>" />
                                </div>
                                <label for="fracao_dsr_horista" class="col-xs-3 control-label horista_plantonista <?= empty($curso['horista_plantonista']) ? "hide" : "" ?>">Fração do Cálc. de DSR:</label>
                                <div class="col-xs-1 horista_plantonista <?= empty($curso['horista_plantonista']) ? "hide" : "" ?>">
                                    <input type="text" name="fracao_dsr_horista" id="fracao_dsr_horista" class="form-control <?= ($curso['horista_plantonista'] == 1) ? "validate[required,custom[onlyNumberSp]]" : "" ?>" maxlength="4" value="<?= ($curso['horista_plantonista'] == 1) ? $curso['fracao_dsr_horista'] : null ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="area" class="col-xs-2 control-label">Área:</label>
                                <div class="col-xs-10">
                                    <input type="text" name="area" id="area" class="form-control validate[required]" value="<?= $curso['area'] ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="id_departamento" class="col-xs-2 control-label">Departamento:</label>
                                <div class="col-xs-10">
                                    <?= montaSelect($arrDepartamentos, $curso['id_departamento'], 'name="id_departamento" id="id_departamento" class="validate[required,custom[select]] form-control departamento"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cbo" class="col-xs-2 control-label">Nome do CBO:</label>
                                <div class="col-xs-10">
                                    <input type="text" name="cbo" id="cbo" class="form-control validate[required]" placeholder="Ex: Assistente administrativo  - 4110.10" value="<?= (!empty($curso['cbo_nome'])) ? $curso['cbo_nome']." * ".$curso['cod'] : ""; ?>" />
                                    <span id="selection"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="salario" id="salario_label" class="col-xs-2 control-label"><?= (empty($curso['horista_plantonista'])) ? 'Salário' : 'Salário/Hora' ?>:</label>
                                <div class="col-xs-4">
                                    <?php
                                    if($form_post == 'cadastro'){
                                    ?>
                                    <input type="text" name="salario" id="salario" class="form-control decimal validate[required,min[<?= $valorSalMin[1]['v_fim'] ?>]]" maxlength="17" placeholder="0,00" value="<?= (!empty($curso['salario'])) ? formataMoeda($curso['salario'],1) : ''; ?>" />
                                    <?php
                                    }
                                    else {
                                    ?>
                                        <input type="hidden" name="salario" id="salario" value="<?= formataMoeda($curso['salario'],1); ?>" />
                                        <span id='textVal'><?= formataMoeda($curso['salario']) ?></span>
                                    <?php
                                    if($acoes->verifica_permissoes(84)){
                                    ?>
                                        <img src="../../imagens/icones/icon-edit.gif" title="Editar Valor" class="edita_valor bt-image" data-type="salario" data-key="<?= $id_curso ?>" data-toggle="modal" data-target="#box_salario" />
                                    <?php
                                    }
                                    ?>
                                        <span id='textSuccess'></span>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <label for="mes_abono" class="col-xs-2 control-label hidden1 display1 <?= $tipoHidden1; ?>">Mês Abono:</label>
                                <div class="col-xs-4 hidden1 display1 <?= $tipoHidden1; ?>">
                                    <?= montaSelect(mesesArray(), $curso['mes_abono'], "id='mes_abono' name='mes_abono' class='form-control $tipoBlock1'"); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="undSalFixo" class="col-xs-2 control-label">Unidade de pagamento:</label>
                                <div class="col-xs-4">
                                    <?= montaSelect($arrPagamentos, $curso['undSalFixo'], "id='undSalFixo' name='undSalFixo' class='validate[required,custom[select]] form-control'"); ?>
                                </div>
                                <label for="dscSalVar" class="col-xs-2 control-label">Descrição do salário:</label>
                                <div class="col-xs-4">
                                    <input type="text" name="dscSalVar" id="dscSalVar" maxlength="255" class="form-control <?= (in_array($curso['undSalFixo'], [6,7])) ? 'validate[required]' : '' ?>" value="<?= $curso['dscSalVar'] ?>" />
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 hidden2 display2 <?= $tipoHidden1.$tipoHidden2; ?>">
                                <label for="parcelas" class="col-xs-2 control-label">Parcelas:</label>
                                <div class="col-xs-4">
                                    <input type="text" name="parcelas" id="parcelas" maxlength="4" class="form-control" value="<?= $curso['parcelas'] ?>" <?= $tipoBlock1.$tipoBlock2 ?> />
                                </div>
                                <label for="quota" class="col-xs-2 control-label">Quota:</label>
                                <div class="col-xs-4">
                                    <input type="text" name="quota" id="quota" class="form-control decimal" maxlength="17" placeholder="0,00" value="<?= $curso['quota'] ?>" <?= $tipoBlock1.$tipoBlock2 ?> />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="num_quota" class="col-xs-2 control-label hidden1 display1 hidden2 display2 <?= $tipoHidden1.$tipoHidden2; ?>">Parcela das Quotas:</label>
                                <div class="col-xs-4 hidden1 display1 hidden2 display2 <?= $tipoHidden1.$tipoHidden2; ?>">
                                    <input type="text" name="num_quota" id="num_quota" maxlength="4" class="form-control" value="<?= $curso['num_quota'] ?>" <?= $tipoBlock1.$tipoBlock2 ?> />
                                </div>
                                <label for="qnt_maxima" class="col-xs-2 control-label">Qtd. Máxima de Contratação:</label>
                                <div class="col-xs-4">
                                    <input type="text" name="qnt_maxima" id="qnt_maxima" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $curso['qnt_maxima'] ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="hora_semana" class="col-xs-2 control-label">Horas Semanais:</label>
                                <div class="col-xs-2">
                                    <input type="text" name="hora_semana" id="hora_semana" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $curso['hora_semana'] ?>" />
                                </div>
                                <label for="hora_mes" class="col-xs-2 control-label">Horas Mensais:</label>
                                <div class="col-xs-2">
                                    <input type="text" name="hora_mes" id="hora_mes" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $curso['hora_mes'] ?>" />
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label class="col-xs-2 control-label">Adicionais:</label>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="insal" value="1" id="insal" <?= $curso['tipo_insalubridade'] >= 1 ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> <label for="insal" class="control-label">Insalubridade</label>
                                </div>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="periculosidade_30" value="1" id="periculosidade_30" <?= !empty($curso['periculosidade_30']) ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> <label for="periculosidade_30" class="control-label">Periculosidade 30%</label>
                                </div>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="risco_vida" value="1" id="risco_vida" <?= !empty($curso['risco_vida']) ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> <label for="risco_vida" class="control-label">Risco de Vida 30%</label>
                                </div>
                                <div class="col-xs-2">
                                    <input type="checkbox" name="penos" value="1" id="penos" <?= $curso['penosidade'] >= 1 ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> <label for="penos" class="control-label">Penosidade</label>
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label for="tipo_insalubridade" class="col-xs-2 control-label insalubridade <?= ($curso['tipo'] == 1 || $curso['tipo_insalubridade'] <= 0) ? "hide" : ""; ?>">Insalubridade:</label>
                                <div class="col-xs-3 insalubridade <?= ($curso['tipo'] == 1 || $curso['tipo_insalubridade'] <= 0) ? "hide" : ""; ?>">
                                    <select name="tipo_insalubridade" id="tipo_insalubridade" class="form-control validate[custom[select]] <?= $tipoBlock1 ?>">
                                        <option value="-1">« Selecione »</option>
                                        <option value="1" <?= $curso['tipo_insalubridade'] == 1 ? 'selected="selected"' : ''; ?>>Insalubridade 20%</option>
                                        <option value="2" <?= $curso['tipo_insalubridade'] == 2 ? 'selected="selected"' : ''; ?>>Insalubridade 40%</option>
                                    </select>
                                </div>
                                <label for="penosidade" class="col-xs-2 control-label penosidade <?= ($curso['tipo'] == 1 || $curso['penosidade'] <= 0) ? "hide" : ""; ?>">Penosidade:</label>
                                <div class="col-xs-3 penosidade <?= ($curso['tipo'] == 1 || $curso['penosidade'] <= 0) ? "hide" : ""; ?>">
                                    <select name="penosidade" id="penosidade" class="form-control validate[custom[select]] <?= $tipoBlock1 ?>">
                                        <option value="-1">« Selecione »</option>
                                        <option value="1" <?= $curso['penosidade'] == 1 ? 'selected="selected"' : ''; ?>>Penosidade 20%</option>
                                        <option value="2" <?= $curso['penosidade'] == 2 ? 'selected="selected"' : ''; ?>>Penosidade 40%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group hidden1 insalubridade <?= ($curso['tipo'] == 1 || $curso['tipo_insalubridade'] <= 0) ? "hide" : ""; ?>">
                                <label for="qnt_salminimo_insalu" class="col-xs-2 control-label">Quantidade de Salários:</label>
                                <div class="col-xs-2">
                                    <input type="text" name="qnt_salminimo_insalu" id="qnt_salminimo_insalu" class="form-control validate[required,custom[onlyNumberSp]]" maxlength="4" value="<?= $curso['qnt_salminimo_insalu'] ?>" <?= $tipoBlock1 ?> />
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label for="gratificacao_funcao" class="col-xs-2 control-label">Gratificação por Função:</label>
                                <div class="col-xs-2">
                                    <input type="text" name="gratificacao_funcao" id="gratificacao_funcao" class="form-control decimal" maxlength="17" value="<?= number_format($curso['gratificacao_funcao'],2,',','.'); ?>" placeholder="0,00" <?= $tipoBlock1 ?> />
                                </div>
                                <label for="quebra_caixa" class="col-xs-2 control-label">Quebra de Caixa:</label>
                                <div class="col-xs-2">
                                    <input type="text" name="quebra_caixa" id="quebra_caixa" maxlength="17" class="form-control decimal" value="<?= number_format($curso['quebra_caixa'],2,',','.'); ?>" placeholder="0,00" <?= $tipoBlock1 ?> />
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label for="cargo_nenhum" class="col-xs-2 control-label">Adicional por Cargo de Confiança:</label>
                                <div class="col-xs-2">
                                    <div class="radio">
                                        <label><input type="radio" name="tipo_ad_cargo_confianca" value="0" id="cargo_nenhum" <?= (empty(floatval($curso['valor_ad_cargo_confianca'])) && empty(floatval($curso['percentual_ad_cargo_confianca']))) ? 'checked="checked"' : '' ?> <?= $tipoBlock1 ?> /> Nenhum</label><br />
                                        <label><input type="radio" name="tipo_ad_cargo_confianca" value="1" id="cargo_valor" <?= !empty(floatval($curso['valor_ad_cargo_confianca'])) ? 'checked="checked"' : '' ?> <?= $tipoBlock1 ?> /> Valor</label><br />
                                        <label><input type="radio" name="tipo_ad_cargo_confianca" value="2" id="cargo_percentual" <?= !empty(floatval($curso['percentual_ad_cargo_confianca'])) ? 'checked="checked"' : '' ?> <?= $tipoBlock1 ?> /> Percentual</label>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <input type="text" id="valor_ad_cargo_confianca" name="valor_ad_cargo_confianca" maxlength="17" class="form-control decimal <?= empty(floatval($curso['valor_ad_cargo_confianca'])) ? "hide" : '' ?>" placeholder="0,00" value="<?= !empty($curso['valor_ad_cargo_confianca']) ? number_format($curso['valor_ad_cargo_confianca'],2,',','.') : ''; ?>" />
                                    <input type="text" id="percentual_ad_cargo_confianca" name="percentual_ad_cargo_confianca" maxlength="6" class="form-control decimal <?= empty(floatval($curso['percentual_ad_cargo_confianca'])) ? "hide" : '' ?>" placeholder="0,00" value="<?= !empty($curso['percentual_ad_cargo_confianca']) ? number_format($curso['percentual_ad_cargo_confianca'] * 100,2,',','') : ''; ?>" />
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label for="s_sim" class="col-xs-2 text-right no-margin-b">Sobreaviso</label>
                                <div class="col-xs-1">
                                    <div class="radio">
                                        <label><input type="radio" name="sobre_aviso" id="s_sim" value="1" class="validate[required]" <?= !empty($curso['sobre_aviso']) ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> Sim</label>
                                    </div>
                                </div>
                                <div class="col-xs-1">
                                    <div class="radio">
                                        <label><input type="radio" name="sobre_aviso" id="s_nao" value="0" class="validate[required]" <?= (empty($curso['sobre_aviso']) OR !isset($curso['sobre_aviso'])) ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> Não</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group hidden1 display1 <?= $tipoHidden1; ?>">
                                <label for="p_sim" class="col-xs-2 text-right no-margin-b">Prontidão</label>
                                <div class="col-xs-1">
                                    <div class="radio">
                                        <label><input type="radio" name="prontidao" id="p_sim" value="1" class="validate[required]" <?= !empty($curso['prontidao']) ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> Sim</label>
                                    </div>
                                </div>
                                <div class="col-xs-1">
                                    <div class="radio">
                                        <label><input type="radio" name="prontidao" id="p_nao" value="0" class="validate[required]" <?= (empty($curso['prontidao']) OR !isset($curso['prontidao'])) ? 'checked="checked"' : ''; ?> <?= $tipoBlock1 ?> /> Não</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="descricao" class="col-xs-2 control-label">Descrição da função:</label>
                                <div class="col-xs-10">
                                    <textarea name="descricao" id="descricao" class="form-control"><?= $curso['descricao'] ?></textarea>
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
            <div class="modal fade" id="box_salario" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div name="form2" id="form2" class="form-horizontal">
                        <div class="modal-content">
                            <div class="modal-header bg-primary" id="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Alteração Salarial</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-xs-3">Salário Antigo:</label>
                                    <div class="col-xs-9" id="salarioAntigo">
                                        <?=formataMoeda($curso['salario'])?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label text-left">Salário Novo:</label>
                                    <div class="col-xs-1 control-label">R$</div>
                                    <div class="col-xs-4">
                                        <div class="input-group">
                                            <input type="text" class="form-control decimal" name="salario_novo" id="salario_novo" maxlength="17" placeholder="0,00" autocomplete="off">
                                            <span class="input-group-addon pointer"><i class="fa fa-calculator"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3">Diferença:</label>
                                    <div class="col-xs-9">
                                        R$: <strong id="diferenca"></strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3">Motivo:</label>
                                    <div class="col-xs-9">
                                        <textarea class="form-control" name="motivo" id="motivo" autocomplete="off"></textarea>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <h4 class="col-xs-12 text-bold" id="erro2"></h4>
                                </div>
                            </div>
                            
                            <div class="modal-footer">
                                <input type="hidden" name="curso" id="curso" value="<?= $id_curso ?>" />
                                <input type="hidden" name="salario_antigo" id="salario_antigo" value="<?= $curso['salario'] ?>" />
                                <input type="hidden" name="salario_new" id="salario_new" value="" />
                                <input type="hidden" name="difere" id="difere" value="" />
                                <input type="button" class="btn btn-primary" name="altera_salario" id="altera_salario" value="Atualizar" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        <script src="../../rh_novaintra/curso/control_curso.js" type="text/javascript"></script>
    </body>
</html>