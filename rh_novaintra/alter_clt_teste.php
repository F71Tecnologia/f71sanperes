<?php
require_once ("clt/control_clt_final.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="clt/control_clt_final.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <!--v2.0-->
        <div id="carregando" class="modal fade in" style="display: block;" aria-hidden="false"><div class="modal-dialog text-center no-margin-t" style="width: 100%; height:100%; margin-top: 0!important; padding-top: 25%;"><img src="/intranet/imagens/loading2.gif" style="height: 100px;"></div></div>
        <div class="modal-backdrop fade in"></div>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?= $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <form action="" class="form-horizontal" method="post" id="form_clt" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">DADOS DO PROJETO</div>
                    <select name="rh_vinculo" id="vinculo" style="display:none;">
                        <?php
                        $result_vinculo = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$id_regiao'");
                        while ($row_vinculo = mysql_fetch_array($result_vinculo)) {
                            print "<option value='$row_vinculo[0]'>$row_vinculo[id_empresa] - $row_vinculo[razao]</option>";
                        }
                        ?>
                    </select>

                    <div class="panel-body"> 
                        <div class="form-group">
                            <div class="col-sm-1">
                                <div class="text-bold">Matrícula:</div>
                                <div class=""><?= $matricula ?></div>
                            </div>
                            <div class="col-sm-5">
                                <div class="text-bold">Projeto:</div>
                                <div class=""><?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] ?></div>
                                <input type="hidden" name="id_projeto" value="<?php echo $row_projeto['id_projeto'] ?>">
                                <input type="hidden" name="id_regiao" value="<?php echo $row_projeto['id_regiao'] ?>">
                            </div>
                            <div id="div_sindicato" class="col-sm-6">
                                <div class="text-bold">Sindicato:</div>
                                <div class=""><?= montaSelect($arraySindicatos, $rowClt['rh_sindicato'], "class='form-control input-sm validate[required]' name='rh_sindicato' id='rh_sindicato'") ?></div>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo Admissão:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoAdm, $rowClt['status_admi'], "class='form-control input-sm validate[required]' name='status_admi' id='status_admi'") ?>
                                </div>                                
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo de Regime Previdênciario:</div>
                                <div class=""><?= montaSelect($arrTipoRegPrev, $rowClt['tipo_regime_previdenciario'], "class='form-control input-sm validate[required]' name='tipo_regime_previdenciario' id='tipo_regime_previdenciario'") ?></div>                                
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo Admissão (E-Social):</div>
                                <div class="">
                                    <?= montaSelect($arrTipoAdmiEso, $rowClt['tipo_admissao_esocial'], "class='form-control input-sm validate[required]' name='tipo_admissao_esocial' id='tipo_admissao_esocial'") ?>
                                </div>                                
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Indicativo de Admissão:</div>
                                <div class=""><?= montaSelect($arrIndAdmi, $rowClt['tipo_indicativo_admissao'], "class='form-control input-sm validate[required]' name='tipo_indicativo_admissao' id='tipo_indicativo_admissao'") ?></div>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo de Regime de Jornada:</div>
                                <div class="">
                                    <?= montaSelect($arrTiposRegJor, $rowClt['tipo_regime_jornada'], "class='form-control input-sm validate[required]' name='tipo_regime_jornada' id='tipo_regime_jornada'") ?>
                                </div>                                
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Natureza da Atividade:</div>
                                <div class=""><?= montaSelect($arrNatAtiv, $rowClt['natureza_atividade'], "class='form-control input-sm validate[required]' name='natureza_atividade' id='natureza_atividade'") ?></div>                                
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Setor: </div>
                                <?= (count($arraySetor) <= 1) ? '<select class="form-control input-sm" id="id_setor"  name="id_setor" disabled="disabled"><option value="">Nenhum setor encontrado</option></select>' : montaSelect($arraySetor, $rowClt['id_setor'], "name='id_setor' id='id_setor' class=' form-control input-sm validate[required]'") ?>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Unidade:</div>
                                <div class="">
                                    <?= montaSelect($arrayUnidades, $rowClt['id_unidade'], "class='form-control input-sm validate[required]' name='id_unidade' id='id_unidade1'") ?>                                    
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">Função:</div>
                                <div class="">
                                    <?php $disabled = (empty($id_clt)) ? '' : 'readonly="readonly" tabindex="-1"'; ?>
                                    <?= montaSelect($arrayFuncoes, $rowClt['id_curso'], "class='form-control input-sm validate[required]' name='id_curso' id='id_curso' data-horario='{$rowClt['rh_horario']}' $disabled") ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Horário:</div>
                                <div id="div_horario" class=""><?= (empty($id_clt)) ? '<select class="form-control input-sm" id="rh_horario"  name="rh_horario" disabled="disabled"><option value="">Selecione uma Função</option></select>' : '' ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Forma de Contratação:</div>
                                <?php echo montaSelect(["" => 'Selecione uma forma de contratação', 1 => 'Processo Seletivo', 2 => 'Cargo de Confiança', 3 => 'Proposta Técnica/Conselho Diretivo', 4 => 'Outro'], $rowClt['forma_contratacao'], "class='form-control input-sm validate[required]' name='forma_contratacao' id='forma_contratacao'") ?>
                            </div>
                            <div class="col-sm-3" id="processo_sel">
                                <div class="text-bold">Nº do Processo:</div>
                                <input type="text" class="form-control input-sm" name="num_processo_seletivo" id="num_processo_seletivo" value="<?php echo $rowClt['num_processo_seletivo'] ?>">
                            </div>
                            <div class="col-sm-3" id="outras_formas" style="display:none;">
                                <div class="text-bold">Outro:</div>
                                <input type="text" class="form-control input-sm" name="outros_processo" id="outros_processo" value="<?php echo $rowClt['outros_processo'] ?>">
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Isento de Contribuição:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="ano_contribuicao_sim" name="radio_contribuicao" type="radio" value="sim" <?= ($rowClt['ano_contribuicao'] > 0) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="ano_contribuicao_sim">SIM</label>
                                        <div class="input-group-addon"><input id="ano_contribuicao_nao" name="radio_contribuicao" type="radio" value="nao" <?= (empty($rowClt['ano_contribuicao'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="ano_contribuicao_nao">NÃO</label>
                                        <div class="input-group-addon">/</div>
                                        <?= montaSelect($arrayAnosContrib, $rowClt['ano_contribuicao'], "class='form-control input-sm no-padding' name='ano_contribuicao' id='ano_contribuicao'") ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="contrato_medico" id="contrato_medico" value="1" <?= ($rowClt['contrato_medico']) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="contrato_medico">Contrato para Médicos</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="isento_sindical_confederativa" id="isento_sindical_confederativa" value="1" <?= ($rowClt['isento_sindical_confederativa']) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="isento_sindical_confederativa">Isento Contribuição Sindical</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="isento_sindical_assistencial" id="isento_sindical_assistencial" value="1" <?= ($rowClt['isento_sindical_assistencial']) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="isento_sindical_assistencial">Isento Contribuição Assistencial</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="isento_sindical_associativa" id="isento_sindical_associativa" value="1" <?= ($rowClt['isento_sindical_associativa']) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="isento_sindical_associativa">Isento Contribuição Associativa</label>
                                </div>
                            </div>
                        </div>
<!--                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Valor da Hora:</div>
                                <input type="text" class="form-control input-sm valor" id="valor_hora" name="valor_hora" value="<?php echo number_format($rowClt['valor_hora'], 2, ',', '.') ?>" size="15">
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="tipo_valor_hora" id="tipo_valor_hora" value="1" <?= ($rowClt['tipo_valor_hora']) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="tipo_valor_hora">Calcular DSR</label>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">&nbsp;</div>
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="radio" class="" name="tipo_quantidade_horas" id="hora_limpa" value="0" <?= ($rowClt['tipo_quantidade_horas'] == 0) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="hora_limpa">Hora Normal</label>
                                    <div class="input-group-addon"><input type="radio" class="" name="tipo_quantidade_horas" id="hora_composta" value="1" <?= ($rowClt['tipo_quantidade_horas'] == 1) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="hora_composta">Hora para Cálculo</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Quantidade de Horas:</div>
                                <input type="text" class="form-control input-sm valor <?= ($rowClt['tipo_quantidade_horas'] == 0) ? 'hide' : null ?>" id="quantidade_horas_proporcional" name="quantidade_horas_proporcional" value="<?php echo number_format($rowClt['quantidade_horas_proporcional'], 2, ',', '.') ?>" size="15">
                                <input type="text" class="form-control input-sm hora <?= ($rowClt['tipo_quantidade_horas'] == 1) ? 'hide' : null ?>" id="quantidade_horas" name="quantidade_horas" value="<?php echo $rowClt['quantidade_horas'] ?>" size="15">
                            </div>
                        </div>-->
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS PESSOAIS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm validate[required]" name="nome" id="nome nmTrab" value="<?= $rowClt['nome'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Nome Social:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="nome_social" id="nmSoc nome_social" value="<?= $rowClt['nome_social'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Estado Civil:</div>
                                <div class="">
                                    <?= montaSelect($arrayEstadoCivil, $rowClt['id_estado_civil'], "class='form-control input-sm validate[required]' name='id_estado_civil' id='civil estCiv'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Tipo Sanguíneo:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoSangue, $rowClt['tipo_sanguineo'], 'class="form-control input-sm" id="tipo_sanguineo" name="tipo_sanguineo"') ?>
                                </div>
                            </div>    
                            <div class="col-sm-2">
                                <div class="text-bold">Telefone Fixo:</div>
                                <div class="">
                                    <input type="text" class="tel form-control input-sm" name="tel_fixo" id="tel_fixo" value="<?= $rowClt['tel_fixo'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Telefone Celular:</div>
                                <div class="">
                                    <input type="text" class="tel form-control input-sm" name="tel_cel" id="tel_cel" value="<?= $rowClt['tel_cel'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Recado:</div>
                                <div class="">
                                    <input type="text" class="tel form-control input-sm" name="tel_rec" id="tel_rec" value="<?= $rowClt['tel_rec'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nasc.:</div>
                                <div class="">
                                    <input type="text" class="dtformat form-control input-sm validate[required] datemask" name="data_nasci" id="dtNascto data_nasci" value="<?= implode('/', array_reverse(explode('-', $rowClt['data_nasci']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">País Nasc.:</div>
                                <div class="">
                                    <div class="">
                                        <?= montaSelect($arrayPaises, $rowClt['id_pais_nasc'], "class='form-control input-sm validate[required]' name='id_pais_nasc' id='id_pais_nasc'") ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">UF Nasc.:</div>
                                <div class="">
                                    <?= montaSelect($arrayUfs, $rowClt['uf_nasc'], "$ufNascDis class='form-control input-sm validate[required]' name='uf_nasc' id='uf_nasc'") ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Municipio Nasc.:</div>
                                <div class="input-group">
                                    <?= montaSelect($arrMunNasc, $rowClt['id_municipio_nasc'], 'type="text" class="form-control input-sm validate[required]" name="municipio_nasc" id="municipio_nasc"'); ?>
                                    <div class="input-group-addon" id="id_municipio_nasc_text"><?php echo $rowClt['id_municipio_nasc'] ?></div>
                                </div>
                                <input type="hidden" readonly="readonly" tabindex="-1" class="form-control input-sm validate[required]" id="id_municipio_nasc" value="<?= $rowClt['id_municipio_nasc'] ?>">
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Sexo:</div>
                                <div class="">
                                    <div id="sexo" class="input-group">
                                        <div class="input-group-addon"><input id="sexo_m" name="sexo" type="radio" class="validate[required]" value="M" <?php echo ($rowClt['sexo'] == 'M') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="sexo_m">Masculino</label>
                                        <div class="input-group-addon"><input id="sexo_f" name="sexo" type="radio" class="validate[required]" value="F" <?php echo ($rowClt['sexo'] == 'F') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="sexo_f">Feminino</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="text-bold">E-mail:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="email" id="email" value="<?= $rowClt['email'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">E-mail alternativo:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="email2" id="email2" value="<?= $rowClt['email2'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Curso (Aprendiz):</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="curso_aprendiz" id="curso_aprendiz" value="<?= $rowClt['curso_aprendiz'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-sm-2">
                                <div class="text-bold">Nacionalidade:</div>
                                <div class="">
                                    <?= montaSelect($arrayNacionalidades, isset($rowClt['nacionalidade']) ? $rowClt['nacionalidade'] : 'Brasileiro', "class='form-control input-sm validate[required]' name='nacionalidade' id='nacionalidade'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">País de Nacionalidade:</div>
                                <div class="">
                                    <?= montaSelect($arrayPaises, $rowClt['id_pais_nacionalidade'], "class='form-control input-sm validate[required]' name='id_pais_nacionalidade' id='id_pais_nacionalidade'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data de chegada ao país:</div>
                                <div class="">
                                    <input type="text" <?= $dtChegada ?> class='dtformat form-control input-sm datemask' name='dtChegadaPais' id='dtChegadaPais' value="<?= implode('/', array_reverse(explode('-', $rowClt['dtChegadaPais']))) ?>" >
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Condição de Ingresso do Trabalhador Estrangeiro:</div>
                                <div class="">
                                    <?= montaSelect($array_cond_trab, $rowClt['condicao_estrangeiro'], "$condEstrangDis class='form-control input-sm validate[required]' name='condicao_estrangeiro' id='condicao_estrangeiro'") ?> 
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <!--<div class="text-bold">&nbsp;</div>-->
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input <?= $casBrasDis ?> type="checkbox" class="" name="casado_brasileiro" id="casado_brasileiro" value="1" <?php echo $rowClt['casado_brasileiro'] == 1 ? 'checked' : "" ?>></div>
                                        <label class="form-control input-sm text-default" for="casado_brasileiro">Casado(a) com Brasileiro(a)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <!--<div class="text-bold">&nbsp;</div>-->
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input <?= $temFilDis ?> type="checkbox" class="" name="filhos_br" id="filhos_br" value="1" <?php echo $rowClt['filhos_br'] == 1 ? 'checked' : "" ?> ></div>
                                        <label class="form-control input-sm text-default" for="filhos_br">Tem Filhos Brasileiros</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">País de Residência:</div>
                                <div class="">
                                    <?= montaSelect($arrayPaises, $rowClt['id_pais_residencia'], "class='form-control input-sm validate[required]' name='id_pais_residencia' id='id_pais_residencia'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2 residente_brasil">
                                <div class="text-bold">CEP:</div>
                                <div class="">
                                    <input type="text" id="cep" name="cep" maxlength="9" class="cep form-control input-sm validate[required]" value="<?= $rowClt['cep'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2 residente_extrangeiro">
                                <div class="text-bold">Codigo Postal:</div>
                                <div class="">
                                    <input type="text" id="codigo_enderecamento_postal" name="codigo_enderecamento_postal" maxlength="9" class="form-control input-sm " value="<?= $rowClt['codigo_enderecamento_postal'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2 residente_brasil">
                                <div class="text-bold">Tipo de Logradouro:</div>
                                <div class="">
                                    <?= montaSelect($arrTipoLogr, $rowClt['tipo_de_logradouro'], 'class="form-control input-sm validate[required]" required name="tipo_de_logradouro" id="tipo_de_logradouro"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Endereço:</div>
                                <div class="">
                                    <input name="tipo_endereco" id="tipo_endereco" type="hidden" value="<?= $rowClt['tipo_endereco'] ?>"  />
                                    <input type="text" class='form-control input-sm validate[required]' name='endereco' id='endereco' value="<?= $rowClt['endereco'] ?>" >
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon">Nº.</div>
                                        <input type="text" name="numero" id="numero" class="form-control input-sm validate[required]" style="width: 70px;" value="<?= $rowClt['numero'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-1 residente_brasil">
                                <div class="text-bold">UF:</div>
                                <div class="">
                                    <?= montaSelect($arrayUfs, $rowClt['uf'], "class='form-control input-sm validate[required]' name='uf' id='uf' data-tipo='cidade'") ?>
                                </div>
                            </div>
                            <div class="col-sm-4 residente_brasil">
                                <div class="text-bold">Cidade:</div>
                                <div class="">
                                    <div class="input-group">
                                        <?= montaSelect($arrMunNasc, $rowClt['id_municipio_end'], 'type="text" class="form-control input-sm validate[required]" name="id_municipio_end" id="municipio_end"'); ?>
                                        <!--<input type="text" class="form-control input-sm validate[required]" name="cidade" id="cidade" value="<?= $rowClt['cidade'] ?>">-->
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" readonly="readonly" tabindex="-1" class="form-control input-sm validate[required]" id="id_municipio_end" value="<?= $rowClt['id_municipio_end'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 residente_extrangeiro">
                                <div class="text-bold">Cidade:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="cidade" id="cidade" value="<?= $rowClt['cidade'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Bairro:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm validate[required]" name="bairro" id="bairro" value="<?= $rowClt['bairro'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Complemento:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="complemento" id="complemento" value="<?= $rowClt['complemento'] ?>">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Estuda Atualmente?</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="estuda_sim" name="estuda" type="radio" class="reset validate[required]" value="sim" <?= (($rowClt['estuda']) == 'sim') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="estuda_sim">Sim</label>
                                        <div class="input-group-addon"><input id="estuda_nao" name="estuda" type="radio" class="reset validate[required]" value="nao" <?= (($rowClt['estuda']) == 'nao') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="estuda_nao">Não</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div id="termino_em" <?= ($rowClt['data_escola'] == '0000-00-00') ? 'style="display: none;"' : null ?>>
                                    <div class="text-bold">Término em:</div>   
                                    <div class="">
                                        <input id="termino_em_input" type="text" class="dtformat form-control input-sm datemask" name="data_escola" id="data_escola" value="<?= ($rowClt['data_escola'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_escola']))); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Escolaridade:</div>
                                <div class="">
                                    <?= montaSelect($arrayEscolaridades, $rowClt['escolaridade'], 'class="form-control input-sm validate[required] " id="escolaridade grauInstr" name="escolaridade"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Curso:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="curso" id="curso" value="<?= $rowClt['curso'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Instituição:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="instituicao" id="instituicao" value="<?= $rowClt['instituicao'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS DA FAMÍLIA</div>
                    <div class="panel-body">
                        <legend>Filiação Pai</legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class='form-control input-sm'  name='pai' id='pai' value="<?= $rowClt['pai'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Nacionalidade:</div>
                                <div class="">
                                    <?= montaSelect($arrayNacionalidades, $rowClt['nacionalidade_pai'], "class='form-control input-sm' name='nacionalidade_pai' id='nacionalidade_pai'") ?>                                   
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class='dtformat form-control input-sm datemask' name='data_nasc_pai' id='data_nasc_pai' value="<?= ($rowClt['data_nasc_pai'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_pai']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">CPF:</div>
                                <div class="">                                        
                                    <input id="cpfTrab" type="text" name="cpf_pai" value ="<?= $rowClt['cpf_pai'] ?> " class='cpf form-control input-sm validate[custom[cpf]]'>
                                </div>
                            </div>
                        </div> <!-- form group -->
                        <!--                        <div class="form-group">
                                                    <div class="col-sm-3">
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><input type="checkbox" class="" name="incapaz_trab_pai" id="incapaz_trab_pai" <?= ($arrayDependentes['incapaz_trab_pai']) ? 'checked' : null ?> value="1"></div>
                                                            <label class="form-control input-sm text-default" for="incapaz_trab_pai">Incapaz de Trabalhar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="">
                                                            <div class="input-group">
                                                                <div class="input-group-addon"><input type="checkbox" class="" name="ddir_pai" id="ddir_pai" <?= ($arrayDependentes['ddir_pai']) ? 'checked' : null ?> value="1"></div>
                                                                <label class="form-control input-sm text-default" for="ddir_pai">Dependente de IRRF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  form group -->
                        <legend>Filiação Mãe</legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class='form-control input-sm' name='mae' id='mae' value="<?= $rowClt['mae'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Nacionalidade:</div>

                                <div class="">
                                    <?= montaSelect($arrayNacionalidades, $rowClt['nacionalidade_mae'], "class='form-control input-sm' name='nacionalidade_mae' id='nacionalidade_mae'") ?>
                                </div>                                
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class='dtformat form-control input-sm datemask' name='data_nasc_mae' id='data_nasc_mae' value="<?= ($rowClt['data_nasc_mae'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_mae']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">CPF:</div>
                                <div class="">
                                    <input type="text" value ="<?= $rowClt['cpf_mae'] ?> " name="cpf_mae" class='cpf form-control input-sm validate[custom[cpf]]'?>
                                </div>
                            </div>
                        </div> <!--form-group -->
                        <!--                        <div class="form-group">
                                                    <div class="col-sm-3">
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><input type="checkbox" class="" name="incapaz_trab_mae" id="incapaz_trab_mae" <?= ($arrayDependentes['incapaz_trab_mae']) ? 'checked' : null ?> value="1"></div>
                                                            <label class="form-control input-sm text-default" for="incapaz_trab_mae">Incapaz de Trabalhar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="">
                                                            <div class="input-group">
                                                                <div class="input-group-addon"><input type="checkbox" class="" name="ddir_mae" id="ddir_mae" <?= ($arrayDependentes['ddir_mae']) ? 'checked' : null ?> value="1"></div>
                                                                <label class="form-control input-sm text-default" for="ddir_mae">Dependente de IRRF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> form-group -->
                        <legend>Cônjuge</legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class='form-control input-sm' name='nome_conjuge' id='nome_conjuge' value="<?= $rowClt['nome_conjuge'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class='dtformat form-control input-sm datemask' name='data_nasc_conjuge' id='data_nasc_conjuge' value="<?= ($rowClt['data_nasc_conjuge'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_conjuge']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">CPF:</div>
                                <div class="">
                                    <input type="text" value ="<?= $rowClt['cpf_conjuge'] ?> " name="cpf_conjuge" class='cpf form-control input-sm validate[custom[cpf]]'/>
                                </div>
                            </div>
                        </div>
                        <!--                        <div class="form-group">
                                                    <div class="col-sm-3">
                                                        <div class="input-group">
                                                            <div class="input-group-addon"><input type="checkbox" class="" name="incapaz_trab_conjuge" id="incapaz_trab_conjuge" <?= ($arrayDependentes['incapaz_trab_conjuge']) ? 'checked' : null ?> value="1"></div>
                                                            <label class="form-control input-sm text-default" for="incapaz_trab_conjuge">Incapaz de Trabalhar</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="">
                                                            <div class="input-group">
                                                                <div class="input-group-addon"><input type="checkbox" class="" name="ddir_conjuge" id="ddir_conjuge" <?= ($arrayDependentes['ddir_conjuge']) ? 'checked' : null ?> value="1"></div>
                                                                <label class="form-control input-sm text-default" for="ddir_conjuge">Dependente de IRRF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>-->
                    </div>
                    <div class="panel-heading border-t text-bold">FILHOS</div>
                    <div class="panel-body">
                        <input type="hidden" class='form-control input-sm' name='id_dependentes' value="<?= $arrayDependentes['id_dependentes'] ?>">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Número de filhos:</div>
                                <div class="">
                                    <input type="number" id="num_filhos" name="num_filhos" class="form-control input-sm" value="<?= $rowClt['num_filhos'] ?>">
                                </div>
                            </div>
                        </div>
                        <?php for ($i = 0; $i <= 5; $i++) { ?>
                            <div class="container_filho">
                                <input type="hidden" name='dependente[<?= $i ?>][id_dependente]' id='id_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['id_dependente'] ?>">
                                <div id='painel-filhos<?= $i ?>'>	                        
                                    <div class="form-group" >                                        
                                        <div class="col-sm-4">
                                            <div class="text-bold">Nome:</div>
                                            <div class="">
                                                <input type="text" class='form-control input-sm nomeFavorecido' data-key='<?= $i ?>' name='dependente[<?= $i ?>][nome]' id='nome_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['nome'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">CPF:</div>
                                            <div class="">
                                                <input type="text" class='cpf form-control input-sm validate[custom[cpf]] cpfFavorecido' name='dependente[<?= $i ?>][cpf]' id='cpf_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['cpf'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">&nbsp;</div>
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][nao_ir_filho]" id="nao_ir_filho<?= $i ?>" <?= ($arrayDependentes[$i]['nao_ir_filho']) ? 'checked' : null ?> value="1"></div>
                                                    <label class="form-control input-sm text-default" for="nao_ir_filho<?= $i ?>">Não deduzir no IR</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">Data de Nascimento:</div>
                                            <div class="">
                                                <input type="text" class='dtformat form-control input-sm datemask data_nasc_filho' data-key='<?= $i ?>' name='dependente[<?= $i ?>][data_nasc]' id='data_nasc_filho<?= $i ?>' value="<?= implode('/', array_reverse(explode('-', $arrayDependentes[$i]['data_nasc']))) ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="text-bold">&nbsp;</div>
                                            <button type="button" class="del_dados_filhos btn btn-danger" data-id="<?= $arrayDependentes[$i]['nome'] ?>" data-posicao_dependente="<?= $i ?>"><i class="fa fa-trash-o"></i></button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">                                    
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][salario_familia]" id="salario_familia_filho<?= $i ?>" <?= ($arrayDependentes[$i]['salario_familia']) ? 'checked' : null ?> value="1"></div>
                                                <label class="form-control input-sm text-default" for="salario_familia_filho<?= $i ?>">Salário Família</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][dep_plano_saude]" id="dep_plano_saude_filho<?= $i ?>" <?= ($arrayDependentes[$i]['dep_plano_saude']) ? 'checked' : null ?> value="1"></div>
                                                <label class="form-control input-sm text-default" for="dep_plano_saude_filho<?= $i ?>">Plano Privado de Saúde</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][incapaz_trab]" id="incapaz_trab<?= $i ?>" <?= ($arrayDependentes[$i]['incapaz_trab']) ? 'checked' : null ?> value="1"></div>
                                                <label class="form-control input-sm text-default" for="incapaz_trab<?= $i ?>">Incapaz de Trabalhar</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][possui_guarda]" id="possui_guarda<?= $i ?>" <?= ($arrayDependentes[$i]['possui_guarda']) ? 'checked' : null ?> value="1"></div>
                                                <label class="form-control input-sm text-default" for="possui_guarda<?= $i ?>">Possui Guarda</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">                           
                                        <div class="col-sm-3">
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][deficiencia]" id="deficiencia_filho<?= $i ?>" <?= ($arrayDependentes[$i]['deficiencia']) ? 'checked' : null ?> value="1"></div>
                                                    <label class="form-control input-sm text-default" for="deficiencia_filho<?= $i ?>">Portador de deficiência</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][fac_tec]" id="fac_tec_filho<?= $i ?>" <?= ($arrayDependentes[$i]['fac_tec']) ? 'checked' : null ?> value="1"></div>
                                                    <label class="form-control input-sm text-default" for="fac_tec_filho<?= $i ?>">Cursando escola técnica ou faculdade</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="">
                                                <div class="input-group">   
                                                    <div class="input-group-addon"><input type="checkbox" class="pensao" name="dependente[<?= $i ?>][pensao_alimenticia]" data-target="filho<?= $i ?>" data-i="<?php echo $i ?>" id="pensao<?= $i ?>"  value="1" <?= (isset($arrayFav[$i]['pensao'])) ? 'checked' : null ?> ></div>
                                                    <label class="form-control input-sm text-default" for="pensao<?= $i ?>">Pensão Alimentícia</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="div_favorecido bg-warning" id="pensao_alimenticia<?= $i ?>">
                                        <div class="form-group">                                                        
                                            <input type="hidden" name="dependente[<?= $i ?>][favorecidos_pensao][id]" class="form-control input-sm" value="<?= $arrayFav[$i]['id'] ?>">
                                            <input type="hidden" name="dependente[<?= $i ?>][favorecidos_pensao][nome_dependente]" data-parent="pensao<?= $i ?>" id="favorecidos_nome_filho<?= $i ?>" class="form-control input-sm" value="<?= $arrayFavorecidos[$i]['nome_dependente'] ?>">   
                                            <input type="hidden" name="dependente[<?= $i ?>][favorecidos_pensao][cpf]" data-parent="pensao<?= $i ?>" id="favorecidos_cpf_filho<?= $i ?>" class="form-control input-sm" value="<?= $arrayFavorecidos[$i]['cpf'] ?>">

                                            <div class="col-sm-3">
                                                <div class="text-bold">Base de Cálculo:</div>
                                                <div class="">
                                                    <?= montaSelect($arr_base_pensao, $arrayFav[$i]['base_pensao'], "name='dependente[$i][favorecidos_pensao][base_pensao]' class='input-sm base_pensao form-control'"); ?>
                                                </div>
                                            </div>
                                            <div class="aliquota col-sm-3 <?= $hide_aliquota ?>">
                                                <div class="text-bold">Aliquota:</div>
                                                <div class="">
                                                    <input type="text" maxlength="6" class="valor form-control input-sm" name="dependente[<?= $i ?>][favorecidos_pensao][aliquota]"  value="<?php echo str_replace('.', ',', $arrayFav[$i]['aliquota']) ?>">
                                                </div>
                                            </div>
                                            <div class="valorfixo col-sm-3 <?= $hide_valor ?>">
                                                <div class="text-bold">Valor:</div>
                                                <div class="">
                                                    <input type="text" class="valor form-control input-sm" name="dependente[<?= $i ?>][favorecidos_pensao][valorfixo]"  value="<?php echo str_replace('.', ',', $arrayFav[$i]['valorfixo']) ?>">
                                                </div>
                                            </div>
                                            <div class="quantSalMinimo col-sm-3 <?= $hide_qnt_sal_min ?>">
                                                <div class="text-bold">Quantidade de Salário Mínimos:</div>
                                                <div class="">
                                                    <input type="number" class="form-control input-sm" min="0" name="dependente[<?= $i ?>][favorecidos_pensao][quantSalMinimo]"  value="<?php echo str_replace('.', ',', $arrayFav[$i]['quantSalMinimo']) ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">  
                                            <div class="col-sm-4">
                                                <div class="text-bold">Favorecido:</div>
                                                <div class="">
                                                    <input type="text" name="dependente[<?= $i ?>][favorecidos_pensao][favorecido]" class="form-control input-sm" value="<?= $arrayFav[$i]['favorecido'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="text-bold">CPF do Favorecido:</div>
                                                <div class="">
                                                    <input type="text" class='cpf form-control input-sm validate[custom[cpf]]' name="dependente[<?= $i ?>][favorecidos_pensao][cpf]"  value="<?= $arrayFav[$i]['cpf'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="text-bold">Agência:</div>
                                                <div class="input-group">
                                                    <input type="text" name="dependente[<?= $i ?>][favorecidos_pensao][agencia]" class="form-control input-sm" value="<?= $arrayFav[$i]['agencia'] ?>">
                                                    <div class="input-group-addon">DV</div>
                                                    <input maxlength="1" name="dependente[<?= $i ?>][favorecidos_pensao][agencia_dv]" type="text" class="form-control input-sm" value="<?= $arrayFav[$i]['agencia_dv'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="text-bold">Conta:</div>
                                                <div class="input-group">
                                                    <input type="text" name="dependente[<?= $i ?>][favorecidos_pensao][conta]" class="form-control input-sm" value="<?= $arrayFav[$i]['conta'] ?>">
                                                    <div class="input-group-addon">DV</div>
                                                    <input maxlength="1" name="dependente[<?= $i ?>][favorecidos_pensao][conta_dv]" type="text" class="form-control input-sm" value="<?= $arrayFav[$i]['conta_dv'] ?>">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-2">
                                                <div class="text-bold">Oficio:</div>
                                                <div class="">
                                                    <input type="text" name="dependente[<?= $i ?>][favorecidos_pensao][oficio]" class="form-control input-sm" value="<?= $arrayFav[$i]['oficio'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="text-bold">Incide Em:</div>
                                                <div class="input-group">
                                                    <label class="input-group-addon" for="incide_ferias<?= $i ?>"><input type="checkbox" id="incide_ferias<?= $i ?>" name="dependente[<?= $i ?>][favorecidos_pensao][incide_ferias]" <?php echo ($arrayFav[$i]['incide_ferias']) ? 'CHECKED' : null ?> value="1"></label>
                                                    <label class="form-control input-sm" for="incide_ferias<?= $i ?>">Férias</label>
                                                    <label class="input-group-addon" for="incide_rescisao<?= $i ?>"><input type="checkbox" id="incide_rescisao<?= $i ?>" name="dependente[<?= $i ?>][favorecidos_pensao][incide_rescisao]" <?php echo ($arrayFav[$i]['incide_rescisao']) ? 'CHECKED' : null ?> value="1"></label>
                                                    <label class="form-control input-sm" for="incide_rescisao<?= $i ?>">Rescisão</label>
                                                    <label class="input-group-addon" for="incide_13<?= $i ?>"><input type="checkbox" id="incide_13<?= $i ?>" name="dependente[<?= $i ?>][favorecidos_pensao][incide_13]" <?php echo ($arrayFav[$i]['incide_13']) ? 'CHECKED' : null ?> value="1"></label>
                                                    <label class="form-control input-sm" for="incide_13<?= $i ?>">Décimo Terceiro</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="text-bold">Banco:</div>
                                                <div class="">
                                                    <?= montaSelect($arrayBancosFav, $arrayFav[$i]['id_lista_banco'], 'class="form-control input-sm" name="dependente[' . $i . '][favorecidos_pensao][id_lista_banco]"') ?>
                                                </div>
                                            </div>

                                            <div class="col-sm-2">
                                                <div class="text-bold">Tipo de Conta:</div>
                                                <div class="">
                                                    <?= montaSelect($arrayTipoContaFav, $arrayFav[$i]['tipo_conta'], 'class="form-control input-sm" name="dependente[' . $i . '][favorecidos_pensao][tipo_conta]" ') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                    <hr>
                                </div>
                            </div>
                        <?php } ?>
                        <button id='add_filho' type='button' class="btn btn-success"> <i class="fa fa-plus"></i> </button>
                    </div>
                    <div class="panel-heading border-t text-bold">OUTROS DEPENDENTES</div>
                    <div class="panel-body container_outros_dependentes">	
                        <?php if (count($array_outros_dependentes) == 0) {
                            ?>
                            <div class="div_outros_dependentes div_outros_dependentes_princ">
                                <div class="form-group" >                                        
                                    <div class="col-sm-4">
                                        <div class="text-bold">Nome:</div>
                                        <div class="">
                                            <input type="text" class='form-control input-sm' name='outro_dependente[0][nome]' id='' value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">CPF:</div>
                                        <div class="">
                                            <input type="text" class='cpf form-control input-sm validate[custom[cpf]]' name='outro_dependente[0][cpf]' id='' value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Tipo de Dependente:</div>
                                        <div class="">
                                            <?= montaSelect($arrayTiposDependente, '', 'class="form-control input-sm" id="tipo_dependente" name="outro_dependente[0][parentesco]"') ?>
                                        </div>
                                    </div>                             
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data de Nascimento:</div>
                                        <div class="">
                                            <input type="text" class='dtformat form-control input-sm datemask' name='outro_dependente[0][data_nasc]'  id='' value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="text-bold">&nbsp;</div>
                                        <button type="button" class="del_dados_outros_dependentes btn btn-danger" data-id="<?= $arrayDependentes[$i]['nome'] ?>" data-posicao_dependente="<?= $i ?>"><i class="fa fa-trash-o"></i></button>
                                    </div>
                                </div>
                                <div class="form-group">                        	                           
                                    <div class="col-sm-3">                                    
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" class="" name='outro_dependente[0][salario_familia]' id="outro_dependente[0][salario_familia]" value="1"></div>
                                            <label class="form-control input-sm text-default" for="outro_dependente[0][salario_familia]">Salário Família</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">                                                             
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" class="" name='outro_dependente[0][nao_ir_filho]' id="outro_dependente[0][nao_ir]" value="1"></div>
                                            <label class="form-control input-sm text-default" for="outro_dependente[0][nao_ir]">Não deduzir no IR</label>
                                        </div>                                
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" class="" name='outro_dependente[0][dep_plano_saude]' id="outro_dependente[0][plano_privado_de_saude]"  value="1"></div>
                                            <label class="form-control input-sm text-default" for="outro_dependente[0][plano_privado_de_saude]">Plano Privado de Saúde</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" class="" name="outro_dependente[0][incapaz_trab]" id="outro_dependente[0][incapaz_trab]" value="1"></div>
                                            <label class="form-control input-sm text-default" for="outro_dependente[0][incapaz_trab]">Incapaz de Trabalhar</label>
                                        </div>
                                    </div>

                                </div>
                                <hr>                              
                            </div>
                            <?php
                        } else {
                            $cd = 0;
                            foreach ($array_outros_dependentes as $key => $value) {
                                ?>
                                <div class="div_outros_dependentes">
                                    <input type="hidden" name='outro_dependente[<?= $cd ?>][id_dependente]' id='' value="<?= $value['id_dependente'] ?>">
                                    <div class="form-group" >                                        
                                        <div class="col-sm-4">
                                            <div class="text-bold">Nome:</div>
                                            <div class="">
                                                <input type="text" class='form-control input-sm' name='outro_dependente[<?= $cd ?>][nome]' id='' value="<?= $value[nome] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">CPF:</div>
                                            <div class="">
                                                <input type="text" class='cpf form-control input-sm validate[custom[cpf]]' name='outro_dependente[<?= $cd ?>][cpf]' id='' value="<?= $value[cpf] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">Tipo de Dependente:</div>
                                            <div class="">

                                                <?= montaSelect($arrayTiposDependente, $value['parentesco'], "class='form-control input-sm' id='tipo_dependente' name='outro_dependente[{$cd}][parentesco]'") ?>
                                            </div>
                                        </div>                             
                                        <div class="col-sm-2">
                                            <div class="text-bold">Data de Nascimento:</div>
                                            <div class="">
                                                <input type="text" class='dtformat form-control input-sm datemask' name='outro_dependente[<?= $cd ?>][data_nasc]'  id='' value="<?= $value['data_formatada'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <div class="text-bold">&nbsp;</div>
                                            <button type="button" class="del_dados_outros_dependentes btn btn-danger" data-id="<?= $value[id_dependente] ?>"><i class="fa fa-trash-o"></i></button>
                                        </div>
                                    </div>
                                    <div class="form-group">                        	                           
                                        <div class="col-sm-3">                                    
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name='outro_dependente[<?= $cd ?>][salario_familia]' id="outro_dependente[<?= $cd ?>][salario_familia]" value="1" <?php echo $value['salario_familia'] ? 'checked' : '' ?> ></div>
                                                <label class="form-control input-sm text-default" for="outro_dependente[<?= $cd ?>][salario_familia]">Salário Família</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">                                                             
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name='outro_dependente[<?= $cd ?>][nao_ir_filho]' id="outro_dependente[<?= $cd ?>][nao_ir]" value="1" <?php echo $value['nao_ir_filho'] ? 'checked' : '' ?> ></div>
                                                <label class="form-control input-sm text-default" for="outro_dependente[<?= $cd ?>][nao_ir]">Não deduzir no IR</label>
                                            </div>                                
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name='outro_dependente[<?= $cd ?>][dep_plano_saude]' id="outro_dependente[<?= $cd ?>][plano_privado_de_saude]"  value="1"  <?php echo $value['dep_plano_saude'] ? 'checked' : '' ?>   ></div>
                                                <label class="form-control input-sm text-default" for="outro_dependente[<?= $cd ?>][plano_privado_de_saude]">Plano Privado de Saúde</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="checkbox" class="" name="outro_dependente[<?= $cd ?>][incapaz_trab]" id="outro_dependente[<?= $cd ?>][incapaz_trab]" <?= ($value['incapaz_trab']) ? 'checked' : null ?> value="1"></div>
                                                <label class="form-control input-sm text-default" for="outro_dependente[<?= $cd ?>][incapaz_trab]">Incapaz de Trabalhar</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>

                                <?php
                                $cd++;
                            }
                            ?>
                        <?php } ?>
                    </div>                   
                    <button style="margin-bottom: 20px; margin-left: 20px" type='button' class="btn btn-success add_outro_dependente"><i class="fa fa-plus"></i> </button>                    

                    <div class="panel-heading border-t text-bold">APARÊNCIA</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Cabelos:</div>
                                <div class="">
                                    <?= montaSelect($arrayOlhosCabelos[1], $rowClt['cabelos'], 'class="form-control input-sm" id="cabelos" name="cabelos"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Olhos:</div>
                                <div class="">
                                    <?= montaSelect($arrayOlhosCabelos[2], $rowClt['olhos'], 'class="form-control input-sm" id="olhos" name="olhos"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Peso:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="peso" id="peso" value="<?= $rowClt['peso'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Altura:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="altura" id="altura" value="<?= $rowClt['altura'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Marcas ou Cicatriz:</div>
                                <div class="">
                                    <input type="text" class="form-control input-sm" name="defeito" id="defeito" value="<?= $rowClt['defeito'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Etnia:</div>
                                <div class="">
                                    <?= montaSelect($arrayEtinia, $rowClt['etnia'], 'class="form-control input-sm" id="etnia racaCor" name="etnia"') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-sm-3">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="deficiencia" id="deficiencia" <?= ($rowClt['deficiencia']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="deficiencia">Portador de deficiência</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="reabReadap" id="reabReadap" <?= ($rowClt['reabReadap']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="reabReadap">Trabalhador reabilitado</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="preenche_cota" id="preenche_cota" <?= ($rowClt['preenche_cota']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="preenche_cota">Preenche Cota de Trabalhador com Deficiência</label>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="text-bold">Observações quanto a deficiência:</div>
                                <div class="">
                                    <textarea class="form-control input-sm" id="obs_deficiencia" name="obs_deficiencia"><?= $rowClt['obs_deficiencia'] ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2 portador_deficiencia">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="deficiencia_visual" id="deficiencia_visual" <?= ($rowClt['deficiencia_visual']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="deficiencia_visual">Visual</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 portador_deficiencia">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="deficiencia_mental" id="deficiencia_mental" <?= ($rowClt['deficiencia_mental']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="deficiencia_mental">Mental</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 portador_deficiencia">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="deficiencia_auditiva" id="deficiencia_auditiva" <?= ($rowClt['deficiencia_auditiva']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="deficiencia_auditiva">Auditiva</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 portador_deficiencia">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="deficiencia_fisica" id="deficiencia_fisica" <?= ($rowClt['deficiencia_fisica']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="deficiencia_fisica">Física</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 portador_deficiencia">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="deficiencia_intelectual" id="deficiencia_intelectual" <?= ($rowClt['deficiencia_visual']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control input-sm text-default" for="deficiencia_intelectual">Intelectual</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DOCUMENTAÇÃO</div>                    
                    <div class="panel-body">
                        <div class="panel panel-default">
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-2 div_nacionalidade">
                                        <div class="text-bold">Nº do RNE:</div>
                                        <div class="">
                                            <input type="text" name="nrRne" id="nrRne" class="form-control input-sm" value="<?= $rowClt['nrRne'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2 div_nacionalidade">
                                        <div class="text-bold">Órgão Emissor:</div>
                                        <div class="">
                                            <input type="text" name="orgaoEmissorRNE" id="orgaoEmissorRNE" class="form-control input-sm" value="<?= $rowClt['orgaoEmissorRNE'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2 div_nacionalidade">
                                        <div class="text-bold">Data Expedição RNE:</div>
                                        <div class="">
                                            <input type="text" name="dtExpedRNE" id="dtExpedRNE" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['dtExpedRNE'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['dtExpedRNE']))) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-2">
                                        <div class="text-bold">Nº do RG:</div>
                                        <div class="">
                                            <input type="text" name="rg" id="rg" class="form-control input-sm validate[required]" value="<?= $rowClt['rg'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Orgão Expedidor:</div>
                                        <div class="">
                                            <input type="text" name="orgao" id="orgao" class="form-control input-sm validate[required]" value="<?= $rowClt['orgao'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">UF:</div>
                                        <div class="">
                                            <?= montaSelect($arrayUfs, $rowClt['uf_rg'], 'class="form-control input-sm validate[required]" id="uf_rg" name="uf_rg"') ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data Expedição:</div>
                                        <div class="">
                                            <input type="text" name="data_rg" id="data_rg" class="dtformat form-control input-sm validate[required] datemask" value="<?= ($rowClt['data_rg'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_rg']))) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Inscrição no Órgão de Classe:</div>
                                        <div class="">
                                            <input type="text" name="nrOC" id="nrOC" class="form-control input-sm" value="<?= $rowClt['nrOC'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Órgão Regulamentador:</div>
                                        <div class="">
                                            <input type="text" name="conselho" id="conselho" class="form-control input-sm" value="<?= $rowClt['conselho'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data de emissão:</div>
                                        <div class="">
                                            <input type="text" name="data_emissao" id="data_emissao" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['data_emissao'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_emissao']))) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data de validade:</div>
                                        <div class="">
                                            <input type="text" name="data_validade_oc" id="data_validade_oc" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['data_validade_oc'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_validade_oc']))) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="">&nbsp;</div>
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" class="" name="verifica_orgao" id="verifica_orgao" value="1" <?= ($rowClt['verifica_orgao']) ? 'checked' : null ?> ></div>
                                            <label class="form-control input-sm text-default" for="verifica_orgao">Verificado</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Número do RIC:</div>
                                        <div class="">
                                            <input type="text" name="nrRic" id="nrRic" class="form-control input-sm" value="<?= $rowClt['nrRic'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Órgão Regulamentador:</div>
                                        <div class="">
                                            <input type="text" name="orgNrRic" id="orgNrRic" class="form-control input-sm" value="<?= $rowClt['orgNrRic'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data de emissão:</div>
                                        <div class="">
                                            <input type="text" name="dtExpNrRic" id="dtExpNrRic" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['dtExpNrRic'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['dtExpNrRic']))) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Número do CRT:</div>
                                        <div class="">
                                            <input type="text" name="num_crt" id="num_crt" class="form-control input-sm" value="<?= $rowClt['num_crt'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data de emissão:</div>
                                        <div class="">
                                            <input type="text" name="emissao_crt" id="emissao_crt" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['emissao_crt'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['emissao_crt']))) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data de validade:</div>
                                        <div class="">
                                            <input type="text" name="validade_crt" id="validade_crt" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['validade_crt'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['validade_crt']))) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-2">
                                        <div class="text-bold">Número da CNH:</div>
                                        <div class="">
                                            <input type="text" name="cnh_registro" id="cnh_registro" class="form-control input-sm" value="<?= $rowClt['cnh_registro'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data da Expedição:</div>
                                        <div class="">
                                            <input type="text" name="data_exp_cnh" id="data_exp_cnh" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['data_exp_cnh'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_exp_cnh']))) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">UF:</div>
                                        <div class="">
                                            <?= montaSelect($arrayUfs, $rowClt['uf_cnh'], 'class="form-control input-sm" id="uf_cnh" name="uf_cnh"') ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data da Validade:</div>
                                        <div class="">
                                            <input type="text" name="cnh_validade" id="cnh_validade" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['cnh_validade'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['cnh_validade']))) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Primeira Habilitação:</div>
                                        <div class="">
                                            <input type="text" name="cnh_1_habilitacao" id="cnh_1_habilitacao" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['cnh_1_habilitacao'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['cnh_1_habilitacao']))) ?>">
                                        </div>
                                    </div>
                                    <?php $arrayCatCNH = array("-1" => "-- Selecione --", "A" => "A", "B" => "B", "C" => "C", "D" => "D", "E" => "E", "AB" => "AB", "AC" => "AC", "AD" => "AD", "AE" => "AE"); ?>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Categoria CNH:</div>
                                        <div class="">
                                            <?= montaSelect($arrayCatCNH, $rowClt['cnh_categoria'], 'class="form-control input-sm validate[required]" id="cnh_categoria" name="cnh_categoria"') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Nº Carteira de Trabalho:</div>
                                        <div class="">
                                            <input type="text" name="campo1" id="campo1" class="form-control input-sm validate[required]" value="<?= $rowClt['campo1'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Série:</div>
                                        <div class="">
                                            <input type="text" name="serie_ctps" id="serie_ctps" class="form-control input-sm validate[required]" value="<?= $rowClt['serie_ctps'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">UF:</div>
                                        <div class="">
                                            <?= montaSelect($arrayUfs, $rowClt['uf_ctps'], 'class="form-control input-sm validate[required]" id="uf_ctps" name="uf_ctps"') ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Data carteira de Trabalho:</div>
                                        <div class="">
                                            <input type="text" name="data_ctps" id="data_ctps" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['data_ctps'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_ctps']))) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-2">
                                        <div class="text-bold">Nº Título de Eleitor:</div>
                                        <div class="">
                                            <input type="text" name="titulo" id="titulo" class="form-control input-sm" value="<?= $rowClt['titulo'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Zona:</div>
                                        <div class="">
                                            <input type="text" name="zona" id="zona" class="form-control input-sm" value="<?= $rowClt['zona'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Seção:</div>
                                        <div class="">
                                            <input type="text" name="secao" id="secao" class="form-control input-sm " value="<?= $rowClt['secao'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">CPF:</div>
                                        <div class="">
                                            <input type="text" name="cpf" id="cpf" class="cpf form-control input-sm validate[required,custom[cpf]] verificaCpfFunDemitidos" value="<?= $rowClt['cpf'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="">&nbsp;</div>
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" class="" name="documentos_entregues" id="documentos_entregues" value="1" <?= ($rowClt['documentos_entregues']) ? 'checked' : null ?> ></div>
                                            <label class="form-control input-sm text-default" for="documentos_entregues">Documentos para salario familia entregues?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="form-group no-margin-b">
                                    <div class="col-sm-2">
                                        <div class="text-bold">PIS:</div>
                                        <div class="">
                                            <input type="text" name="pis" id="pis nmTrab" class="form-control input-sm <?= ($rowClt['pis'] != '') ? 'validate[required,custom[pis]]' : null ?>" value="<?= $rowClt['pis'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Data Pis:</div>
                                        <div class="">
                                            <input type="text" name="dada_pis" id="dada_pis" class="dtformat form-control input-sm validate[condRequired[pis]] datemask" value="<?= ($rowClt['dada_pis'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['dada_pis']))) ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">FGTS:</div>
                                        <div class="">
                                            <input type="text" name="fgts" id="fgts" class="form-control input-sm" value="<?= $rowClt['fgts'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Certificado de Reservista:</div>
                                        <div class="">
                                            <input type="text" name="reservista" id="reservista" class="form-control input-sm" value="<?= $rowClt['reservista'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Carteira do SUS:</div>
                                        <div class="">
                                            <input type="text" name="carteira_sus" id="carteira_sus" class="form-control input-sm" value="<?= $rowClt['carteira_sus'] ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">BENEFÍCIOS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="desconto_inss" id="desconto_inss" value="1" <?= ($rowClt['desconto_inss'] == 1) ? 'checked' : null ?> ></div>
                                        <label class="form-control input-sm text-default" for="desconto_inss">Proporcionalidade INSS</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 div_desconto_inss">
                                <div class="text-bold">Tipo de Desconto:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="tipo_desconto_inss1" name="tipo_desconto_inss" type="radio" class="reset" value="isento" <?= ($rowClt['tipo_desconto_inss'] == 'isento') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="tipo_desconto_inss1">Suspensão de Recolhimento</label>
                                        <div class="input-group-addon"><input id="tipo_desconto_inss2" name="tipo_desconto_inss" type="radio" class="reset" value="parcial" <?= ($rowClt['tipo_desconto_inss'] == 'parcial') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="tipo_desconto_inss2">Parcial</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 div_desconto_inss">
                                <div class="text-bold">Trabalha em outra empresa?</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="trabalha_outra_empresa_sim" name="trabalha_outra_empresa" type="radio" class="reset" value="sim" <?= ($rowClt['trabalha_outra_empresa'] == 'sim') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="trabalha_outra_empresa_sim">SIM</label>
                                        <div class="input-group-addon"><input id="trabalha_outra_empresa_nao" name="trabalha_outra_empresa" type="radio" class="reset" value="0" <?= ($rowClt['trabalha_outra_empresa'] == '0' || empty($rowClt['trabalha_outra_empresa'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="trabalha_outra_empresa_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 div_trabalha_outra_empresa" <?= ($rowClt['trabalha_outra_empresa'] != 'sim') ? 'style="display: none;"' : null ?>>
                                <div class="text-bold">&nbsp;</div>
                                <button type="button" class="btn btn-success" id="add_dados_outra_empresa"><i class="fa fa-plus"></i></button>
                            </div>
                            <!--div_trabalha_outra_empresa-->
                        </div>
                        <div id="div_dados_outra_empresa" class="div_trabalha_outra_empresa">
                            <?php if ($objInssOutrasEmpresas->getNumRows() > 0) { ?>
                                <?php while ($objInssOutrasEmpresas->getRow()) { ?>
                                    <div class="form-group dados_outra_empresa">
                                        <div class="col-sm-2">
                                            <div class="text-bold">Salário outra empresa:</div>
                                            <div class="">
                                                <input type="hidden" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][id_inss]" value='<?= $objInssOutrasEmpresas->getIdInss() ?>'>
                                                <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][salario]" class="valor form-control input-sm" value='<?= number_format($objInssOutrasEmpresas->getSalario(), 2, ',', '.') ?>'>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">Desconto da outra empresa:</div>
                                            <div class="">
                                                <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][desconto]" class="valor form-control input-sm" value='<?= number_format($objInssOutrasEmpresas->getDesconto(), 2, ',', '.') ?>'>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">Início:</div>
                                            <div class="">
                                                <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][inicio]" class="dtformatinicio data form-control input-sm datemask" value='<?= $objInssOutrasEmpresas->getInicio('d/m/Y') ?>'>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">Fim:</div>
                                            <div class="">
                                                <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][fim]" class="dtformatfim data form-control input-sm datemask" value='<?= $objInssOutrasEmpresas->getFim('d/m/Y') ?>'>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">CNPJ:</div>
                                            <div class="">
                                                <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][cnpj_outro_vinculo]" class="form-control input-sm cnpj_mask" value='<?= $objInssOutrasEmpresas->getCnpjOutroVinculo() ?>'>
                                            </div>
                                        </div>
                                        <?php if ($objInssOutrasEmpresas->getFim('Y-m') > $last_folha['last_folha']) { ?>
                                            <div class="col-sm-1">
                                                <div class="text-bold">&nbsp;</div>
                                                <button type="button" class="del_dados_empresa btn btn-danger" data-id="<?= $objInssOutrasEmpresas->getIdInss() ?>"><i class="fa fa-trash-o"></i></button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php
                                    $countInssOutrasEmpresas++;
                                }
                                ?>
                            <?php } else { ?>
                                <div class="form-group dados_outra_empresa">
                                    <div class="col-sm-2">
                                        <div class="text-bold">Salário outra empresa:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][salario]" class="valor form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Desconto da outra empresa:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][desconto]" class="valor form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Início:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][inicio]" class="dtformatinicio data form-control input-sm datemask">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Fim:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][fim]" class="dtformatfim data form-control input-sm datemask">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">CNPJ:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][cnpj_outro_vinculo]" class="form-control input-sm cnpj_mask">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="text-bold">&nbsp;</div>
                                        <button type="button" class="del_dados_empresa btn btn-danger" data-id=""><i class="fa fa-trash-o"></i></button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Integrante do CIPA:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="cipa_sim" name="cipa" type="radio" value="1" <?= ($rowClt['cipa'] == 1) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="cipa_sim">SIM</label>
                                        <div class="input-group-addon"><input id="cipa_nao" name="cipa" type="radio" value="0" <?= (empty($rowClt['cipa'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="cipa_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="seguro_desemprego" id="seguro_desemprego" value="1" <?= ($rowClt['seguro_desemprego'] == 1) ? 'checked' : null ?> ></div>
                                        <label class="form-control input-sm text-default" for="seguro_desemprego">Recebendo Seguro Desemprego?</label>
                                    </div>
                                </div>
                            </div>
                            <?php foreach ($arrayRefAli as $nomeCampo => $value) { ?>
                                <div class="col-sm-2">
                                    <div class="text-bold">Vale <?= $arrayRefAliNome[$nomeCampo] ?>:</div>
                                    <div class="">
                                        <?= montaSelect($value, $rowClt[$nomeCampo], 'class="form-control input-sm" id="' . $nomeCampo . '" name="' . $nomeCampo . '"') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Assistência Médica:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="medica_sim" name="medica" type="radio" value="1" <?= ($rowClt['medica'] == 1) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="medica_sim">SIM</label>
                                        <div class="input-group-addon"><input id="medica_nao" name="medica" type="radio" value="0" <?= (empty($rowClt['medica'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="medica_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="div_medica">
                                    <div class="text-bold">Plano de Saúde:</div>
                                    <div class="">
                                        <?= montaSelect($arrayPlanoSaude, $rowClt['id_plano_saude'], 'class="form-control input-sm" id="id_plano_saude" name="id_plano_saude"') ?>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo de Plano:</div>
                                <div class="">
                                    <?= montaSelect(array(1 => 'Familiar', 2 => 'Individual'), $rowClt['plano'], 'class="form-control input-sm" id="plano" name="plano"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Produtividade Percentual Fixo:</div>
                                <input type="text" name="produtividade_percentual_fixo" id="produtividade_percentual_fixo" class="form-control input-sm valor" value='<?php echo number_format($rowClt['produtividade_percentual_fixo'], 2, ',', '.') ?>'>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Ad. Transferência:</div>
                                <div class="">
                                    <?= montaSelect(array(0 => 'Selecione', 1 => 'Valor Fixo', 2 => '25% Sobre Salário Base', 3 => '35% Sobre Salário Base'), $rowClt['ad_transferencia_tipo'], 'class="form-control input-sm" id="ad_transferencia_tipo" name="ad_transferencia_tipo"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3" id="ad_transferencia_valor_div">
                                <div class="text-bold">Valor Ad. Transferência:</div>
                                <input type="text" name="ad_transferencia_valor" id="ad_transferencia_valor" class="form-control input-sm valor" value='<?php echo number_format($rowClt['ad_transferencia_valor'], 2, ',', '.') ?>'>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Gratificação:</div>
                                <input type="text" name="gratificacao" id="gratificacao" class="form-control input-sm valor" value='<?php echo number_format($rowClt['gratificacao'], 2, ',', '.') ?>'>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Gratificação por Complexidade:</div>
                                <input type="text" name="gratificacao_complexidade" id="gratificacao_complexidade" class="form-control input-sm valor" value='<?php echo number_format($rowClt['gratificacao_complexidade'], 2, ',', '.') ?>'>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="transporte" id="transporte" value="1" <?= ($rowClt['transporte']) ? 'checked' : null ?> ></div>
                                        <label class="form-control input-sm text-default" for="transporte">Vale Transporte</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="ad_unidocencia" id="ad_unidocencia" value="1" <?= ($rowClt['ad_unidocencia']) ? 'checked' : null ?> ></div>
                                    <label class="form-control input-sm text-default" for="ad_unidocencia">Adicional de Unidocência</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="insalubridade" id="insalubridade" value="1" <?= ($rowClt['insalubridade']) ? 'checked' : null ?> ></div>
                                        <label class="form-control input-sm text-default" for="insalubridade">Insalubridade</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="trabAposent" id="trabAposent" value="1" <?= ($rowClt['trabAposent']) ? 'checked' : null ?> ></div>
                                        <label class="form-control input-sm text-default" for="trabAposent">Trabalhador já recebe aposentadoria</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div_transporte <?= $hide ?>">
                            <input name="vt[id_vale]" type="hidden" id="id_vale" class="form-control input-sm" value="<?= $row_vale['id_vale'] ?>" />
                            <legend>VALE TRANSPORTE</legend>
                            <?php if (!$rowConfig['vale_transporte']) { ?>
                                <div class="form-group">
                                    <?php for ($i = 1; $i <= 6; $i++) { ?>
                                        <div class="col-sm-2">
                                            <label class="control-label">Selecione <?= $i ?>:</label>
                                            <?php echo montaSelect($vale, $row_vale['id_tarifa' . $i], "name='vt[id_tarifa$i]' id='id_tarifa$i' class='form-control input-sm'"); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label">Numero Cartão 1:</label>
                                        <input name="vt[cartao1]" type="text" id="cartao1" class="form-control input-sm" value="<?= $row_vale['cartao1'] ?>" onChange="this.value = this.value.toUpperCase()"/>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label">Numero Cartão 2:</label>
                                        <input name="vt[cartao2]" type="text" id="cartao2" class="form-control input-sm" value="<?= $row_vale['cartao2'] ?>" onChange="this.value = this.value.toUpperCase()"/>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label">Tipo de Desconto:</label>
                                        <?php echo montaSelect([-1 => "« Selecione »", 1 => "Diário", 2 => "Fixo"], $rowClt['vt_tipo_valor'], 'id="vt_tipo_valor" name="vt_tipo_valor" class="form-control input-sm"') ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label">Valor:</label>
                                        <input name="vt_valor_diario" id="vt_valor_diario" type="text" size="12" class="valor form-control input-sm" value="<?php echo number_format($rowClt['vt_valor_diario'], 2, ',', '.') ?>">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS BANCÁRIOS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Banco:</div>
                                <div class="">
                                    <?= montaSelect($arrayBancosProjeto, $rowClt['banco'], 'class="form-control input-sm" id="banco" name="banco"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Tipo de Conta:</div>
                                <div class="input-group">
                                    <!--<input type="text" name="conta_dv" id="conta_dv" maxlength="1" size="2" class="form-control"  value="<?= $rowClt['conta_dv'] ?>" />-->
                                    <div class="input-group-addon"><input id="conta_salario" name="tipo_conta" type="radio" class="reset" value="salario" <?php echo ($rowClt['tipo_conta'] == 'salario') ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm" for="conta_salario"><!--Conta -->Salário</label>
                                    <div class="input-group-addon"><input id="conta_corrente" name="tipo_conta" type="radio" class="reset" value="corrente" <?php echo ($rowClt['tipo_conta'] == 'corrente') ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm" for="conta_corrente"><!--Conta -->Corrente</label>
                                    <div class="input-group-addon"><input id="conta_poupanca" name="tipo_conta" type="radio" class="reset" value="poupanca" <?php echo ($rowClt['tipo_conta'] == 'poupanca') ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control input-sm" for="conta_poupanca"><!--Conta -->Poupança</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Agência:</div>
                                <div class="input-group">
                                    <input type="text" name="agencia" id="agencia" maxlength="5" class="form-control input-sm" value="<?= $rowClt['agencia']; ?>">
                                    <div class="input-group-addon text-bold">DV</div>
                                    <input maxlength="1" type="text" size="5" name="agencia_dv" class="form-control input-sm" value="<?= $rowClt['agencia_dv']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Conta:</div>
                                <div class="input-group">
                                    <input type="text" name="conta" id="conta" class="form-control input-sm" value="<?php echo $rowClt['conta'] ?>">
                                    <div class="input-group-addon text-bold">DV</div>
                                    <input maxlength="1" type="text" size="5" name="conta_dv" class="form-control input-sm" value="<?php echo $rowClt['conta_dv'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Nome do Banco:<i class="text-danger"> (caso fora da lista acima)</i></div>
                                <div class="">
                                    <?= montaSelect($arrayBancos, $rowClt['nome_banco'], 'class="form-control input-sm" id="nome_banco" name="nome_banco"') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS FINANCEIROS E DE CONTRATO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Entrada:</div>
                                <div class="">
                                    <input type="text" name="data_entrada" id="data_entrada" class="dtformat form-control input-sm validate[required] datemask" value="<?= ($rowClt['data_entrada'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_entrada']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data Exame Admissional:</div>
                                <div class="">
                                    <input type="text" name="data_exame" id="data_exame" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['data_exame'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_exame']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Optante pelo FGTS:</div>
                                <div class="">
                                    <?= montaSelect($arrOptFgts, $rowClt['opt_fgts'], $readOnlyOptFgts . ' class="form-control input-sm validate[required]" id="opt_fgts" name="opt_fgts"') ?>                                    
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data de Opção do Trabalhador pelo FGTS:</div>
                                <div class="">
                                    <input type="text" <?= $readOnlyDateOptFgts ?> name="data_opcao_fgts" id="data_opcao_fgts" class="dtformat form-control input-sm datemask" value="<?= $rowClt['data_opcao_fgts'] ?>">
                                </div>
                            </div>

                        </div>
                        <hr />
                        <div class="form-group">
                            <div class="col-sm-3">
                                <!--<div class="text-bold">&nbsp;</div>-->
                                <div class="input-group">
                                    <div class="input-group-addon"><input <?= ($rowClt['trab_temporario'] == 1) ? 'checked' : '' ?> type="checkbox" class="" name="trab_temporario" id="trab_temporario" value="1" <?= ($rowClt['trab_temporario']) ? 'checked' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="trab_temporario">Trabalhador Temporário</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <!--<div class="text-bold">&nbsp;</div>-->
                                <div class="input-group">
                                    <div class="input-group-addon"><input <?= ($rowClt['jovem_aprendiz'] == 1) ? 'checked' : null ?> type="checkbox" class="" name="jovem_aprendiz" id="jovem_aprendiz" value="1"></div>
                                    <label class="form-control input-sm text-default" for="jovem_aprendiz">Jovem Aprendiz</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <!--<div class="text-bold">&nbsp;</div>-->
                                <div class="input-group">
                                    <div class="input-group-addon"><input <?= ($rowClt['estatutario'] == 1) ? 'checked' : null ?> type="checkbox" class="" name="estatutario" id="estatutario" value="1"></div>
                                    <label class="form-control input-sm text-default" for="estatutario">Estatutário</label>
                                </div>
                            </div>
                        </div>
                        <div id="boxTrabEstatutario">
                            <div class="form-group" >
                                <div class="col-sm-4">
                                    <div class="text-bold">Indicador de Provimento:</div>
                                    <div class="">
                                        <?= montaSelect($arrIndProv, $rowClt['estat_ind_provimento'], 'class="form-control input-sm" id="estat_ind_provimento" name="estat_ind_provimento"') ?>                                    
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Tipo de Provimento:</div>
                                    <div class="">
                                        <?= montaSelect($arrTpProv, $rowClt['estat_tp_provimento'], 'class="form-control input-sm" id="estat_tp_provimento" name="estat_tp_provimento"') ?>                                    
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Tipo de Segregação da Massa:</div>
                                    <div class="">
                                        <?= montaSelect($arrTpSegreg, $rowClt['estat_tipo_segreg'], 'class="form-control input-sm" id="estat_tipo_segreg" name="estat_tipo_segreg"') ?>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <div class="text-bold text-sm" style="margin-bottom: 3px;">Data da Nomeação:</div>
                                    <div class="">
                                        <input type="text" name="estat_dt_nomeacao" id="estat_dt_nomeacao" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['estat_dt_nomeacao'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['estat_dt_nomeacao']))) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold text-sm" style="margin-bottom: 3px;">Data da Posse:</div>
                                    <div class="">
                                        <input type="text" name="estat_dt_posse" id="estat_dt_posse" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['estat_dt_posse'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['estat_dt_posse']))) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold text-sm" style="margin-bottom: 3px;">Data da Exercício:</div>
                                    <div class="">
                                        <input type="text" name="estat_dt_exercicio" id="estat_dt_exercicio" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['estat_dt_exercicio'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['estat_dt_exercicio']))) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold text-sm" style="margin-bottom: 3px;">Número do Processo Judicial:</div>
                                    <div class="">
                                        <input type="text" name="estat_num_dec_judicial" id="estat_num_dec_judicial" class="form-control input-sm" value="<?= $rowClt['estat_num_dec_judicial'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="boxTrabTemp"> 
                            <div class="hide form-group">
                                <div class="col-sm-6">
                                    <div class="text-bold">Empresa Contratante onde será Alocado</div>
                                    <div class="">
                                        <?php $empContr = (!empty($rowClt['emp_contr']) ? $rowClt['emp_contr'] : $usuario['id_master']); ?>
                                        <?= montaSelect($masters, $empContr, 'readonly="readonly" tabindex="-1" class="form-control input-sm" id="emp_contr" name="emp_contr" style="pointer-events: none;touch-action: none;cursor: not-allowed;background-color: #eee;opacity: 1;"') ?>                                    
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="text-bold">Estabelecimento ao qual o Trabalhador Temporário está Vinculado</div>
                                    <div class="">
                                        <?php $trabTempEmpAloc = (!empty($rowClt['trab_temp_emp_alocado']) ? $rowClt['trab_temp_emp_alocado'] : $usuario['id_master']); ?>
                                        <?= montaSelect($masters, $trabTempEmpAloc, 'readonly="readonly" tabindex="-1" class="form-control input-sm" id="trab_temp_emp_alocado" name="trab_temp_emp_alocado" style="pointer-events: none;touch-action: none;cursor: not-allowed;background-color: #eee;opacity: 1;"') ?>                                                                   
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" >
                                <div class="col-sm-6">
                                    <div class="text-bold">Hipótese Legal para Contratação do Trabalhador Temporário:</div>
                                    <div class="">
                                        <?= montaSelect($arrHipLeg, $rowClt['hip_legal_trab_temp'], 'class="form-control input-sm" id="hip_legal_trab_temp" name="hip_legal_trab_temp"') ?>                                    
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Tipo de Inclusão de Contrato:</div>
                                    <div class="">
                                        <?= montaSelect($arrTpIncCont, $rowClt['tipo_inc_contr_trab_temp'], 'class="form-control input-sm" id="tipo_inc_contr_trab_temp" name="tipo_inc_contr_trab_temp"') ?>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <div class="text-bold">Justificativa da Hipótese Legal:</div>
                                    <div class="">
                                        <textarea class="form-control input-sm" id="just_hip_legal_trab_temp" name="just_hip_legal_trab_temp"><?= $rowClt['just_hip_legal_trab_temp'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="text-bold">CPF do Trabalhor Substituido:</div>
                                    <div class="">
                                        <input type="text" name="cpf_trab_substituido" id="cpf_trab_substituido" class="cpf form-control input-sm" value="<?= ($rowClt['cpf_trab_substituido'] > 0) ? $rowClt['cpf_trab_substituido'] : null ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <div class="col-sm-4">
                                <!--<div class="text-bold">&nbsp;</div>-->
                                <div class="input-group">
                                    <div class="input-group-addon"><input <?= ($rowClt['suc_vinc_trab'] == 1) ? 'checked' : '' ?> type="checkbox" class="" name="suc_vinc_trab" id="suc_vinc_trab" value="1" <?= ($rowClt['suc_vinc_trab']) ? 'checked' : null ?>></div>
                                    <label class="form-control input-sm text-default" for="suc_vinc_trab">Possui Sucessão de Vínculo</label>
                                </div>
                            </div>
                        </div>
                        <div id="box_suc_vinc_trab" class="<?= ($rowClt['suc_vinc_trab'] == 1) ? null : 'hide' ?>">
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="text-bold">Cnpj do Empregador Anterior:</div>
                                    <div class="">
                                        <input type="text" name="cnpj_empreg_ant" id="cnpj_empreg_ant" class="cnpj_mask form-control input-sm" value="<?= ($rowClt['cnpj_empreg_ant'] > 0) ? $rowClt['cnpj_empreg_ant'] : null ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Matrícula Anterior:</div>
                                    <div class="">
                                        <input type="text" name="matricula_anterior" id="matricula_anterior" class="form-control input-sm" value="<?= ($rowClt['matricula_anterior'] > 0) ? $rowClt['matricula_anterior'] : null ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Data do Início do Vínculo:</div>
                                    <div class="">
                                        <input type="text" class="dtformat form-control input-sm datemask" name="dt_ini_vinc_suc" id="dtNascto dt_ini_vinc_suc" value="<?= ($rowClt['dt_ini_vinc_suc'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_escola']))); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="text-bold">Observações sobre o Vínculo:</div>
                                    <div class="">
                                        <textarea class="form-control input-sm" cols="55" rows="4" name="suc_vinc_observacao" id="suc_vinc_observacao"><?= $rowClt['suc_vinc_observacao'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Local de Pagamento:</div>
                                <div class="">
                                    <input type="text" name="localpagamento" id="localpagamento" class="form-control input-sm validate[required]" value="<?= $rowClt['localpagamento'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo de Pagamento:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoPagamento, $rowClt['tipo_pagamento'], 'class="form-control input-sm validate[required]" id="tipo_pagamento" name="tipo_pagamento"') ?>                                    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Prazo de Experiência:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="30" name="prazoExp" type="radio" class="reset" value="4" <?= ($rowClt['prazoexp'] == '4') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="30">30</label>
                                        <div class="input-group-addon"><input id="45" name="prazoExp" type="radio" class="reset" value="5" <?= ($rowClt['prazoexp'] == '5') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="45">45</label>
                                        <div class="input-group-addon"><input id="60" name="prazoExp" type="radio" class="reset" value="6" <?= ($rowClt['prazoexp'] == '6') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="60">60</label>
                                        <div class="input-group-addon"><input id="3060" name="prazoExp" type="radio" class="reset" value="1" <?= ($rowClt['prazoexp'] == '1') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="3060">30 + 60</label>
                                        <div class="input-group-addon"><input id="4545" name="prazoExp" type="radio" class="reset" value="2" <?= ($rowClt['prazoexp'] == '2' || $rowClt['prazoexp'] == '') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="4545">45 + 45</label>
                                        <div class="input-group-addon"><input id="6030" name="prazoExp" type="radio" class="reset" value="3" <?= ($rowClt['prazoexp'] == '3') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control input-sm" for="6030">60 + 30</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="text-bold">Tipo de Contrato:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoContratacao, $rowClt['tipo_contrato'], 'class="form-control input-sm validate[required]" id="tipo_contrato" name="tipo_contrato" data-classificacao="' . $rowClt['classificacao_tributaria'] . '" data-lotacao="' . $rowClt['tipo_lotacao_tributaria'] . '"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Classificação Tributária:</div>
                                <div class="">
                                    <div id="classif_trib_div">
                                        <?= (!empty($rowClt['tipo_lotacao_tributaria'])) ? montaSelect('', $rowClt['classificacao_tributaria'], 'class="form-control input-sm" id="classificacao_tributaria"  name="classificacao_tributaria"') : '<select class="form-control input-sm" id="classificacao_tributaria"  name="classificacao_tributaria" disabled="disabled"><option value="">Selecione o Tipo de Contrato</option></select>'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Tipo de Lotação Tributária:</div>
                                <div class="">
                                    <div id="tipo_lotacao_div">
                                        <?= (!empty($rowClt['tipo_lotacao_tributaria'])) ? montaSelect("", $rowClt['tipo_lotacao_tributaria'], 'class="form-control input-sm" id="tipo_lotacao_tributaria"  name="tipo_lotacao_tributaria" ') : '<select class="form-control input-sm" id="tipo_lotacao_tributaria"  name="tipo_lotacao_tributaria" disabled="disabled"><option value="">Selecione o Tipo de Contrato</option></select>'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo de Prazo do Contrato:</div>
                                <div class="">
                                    <?= montaSelect($arrTipoContrato, $rowClt['tipo_prazo_contrato'], 'class="form-control input-sm" id="tipo_prazo_contrato"  name="tipo_prazo_contrato"') ?>
                                </div>
                            </div>
                            <div class="box_prazo_contrato hide">
                                <div class="col-sm-2">
                                    <div class="text-bold">Prazo:</div>
                                    <div class="">
                                        <input type="text" name="prazo_contrato" id="prazo_contrato" class="dtformat form-control input-sm datemask" value="<?= ($rowClt['prazo_contrato'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['prazo_contrato']))) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Possui Cláusula Asseguratória?</div>
                                    <div class="">
                                        <?= montaSelect($posClauAsseg, $rowClt['possui_clausula_asseguratoria'], 'class="form-control input-sm" id="possui_clausula_asseguratoria"  name="possui_clausula_asseguratoria"') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="text-bold">Observações:</div>
                                <div class="">
                                    <textarea class="form-control input-sm" cols="55" rows="4" name="obs" id="observacoes"><?= $rowClt['obs'] ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php if ($id_clt) { ?><input name="id_clt" type="hidden" value="<?= $id_clt ?>"><?php } ?>
                        <?php if (!$id_clt) { ?><input name="campo3" type="hidden" value="<?= $matricula ?>"><?php } ?>
                        <?php if (!$id_clt) { ?><input name="matricula" type="hidden" value="<?= $matricula ?>"><?php } ?>
                        <button type="type" class="btn btn-primary" name="<?= $action ?>"><i class="fa fa-save"></i> SALVAR</button>
                    </div>
                </div>
            </form>
            <?php include_once '../template/footer.php'; ?>
        </div> /.content 

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>

        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>

        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="clt/control_clt_final.js"></script>
    </body>
</html>