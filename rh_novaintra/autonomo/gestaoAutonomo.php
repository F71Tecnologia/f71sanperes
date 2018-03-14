<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../classes/WorldClass.php');
include('actions/GestaoAutonomo.php');

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Autônomos - <?= $gestao ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="resources/gestaoAutonomo.css" rel="stylesheet">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Gestão de Autônomos - <?= $gestao ?></small></h2></div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-archive"></i> - DADOS DO PROJETO</span>
                </div>
                <div class="panel-body">
                    <div class="col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon">Código</span>
                            <input type="text" id="campo3" name="campo3" class="form-control" value="<?= $codigo ?>" readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Atividade</span>
                            <?= $wFunction->montaSelect([6579 => "6579 - Autônomo"], 6579, "id='id_curso' name='id_curso' class='form-control'"); ?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Horas Semanais</span>
                            <input type="text" disabled="disabled" class="form-control">
                            <span title="Editar" class="pointer input-group-addon"><i class="fa fa-edit"></i></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Horas Mensais</span>
                            <input type="text" disabled="disabled" class="form-control">
                            <span title="Editar" class="pointer input-group-addon"><i class="fa fa-edit"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-id-card"></i> - PROJETOS E UNIDADES</span>
                </div>
                <div class="panel-body">
                    <div class="boxProjeto">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">Projeto</span>
                                <?= $wFunction->montaSelect($arrProjetos, null, "class='id_projeto form-control' name='id_projeto[]'"); ?>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon">Unidade</span>
                                <?= $wFunction->montaSelect($arrUnidades, null, "class='id_unidade form-control' name='id_unidade[]'"); ?>
                                <span class="unidade_loading input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span>
                                <span title="Adicionar Unidade" class="addProjeto pointer input-group-addon"><i class="icon-success fa fa-plus-circle"></i></span>
                                <span class="input-group-addon"><i class="fa fa-minus-circle"></i></span>
                            </div>
                        </div>
                        <div style="clear:both"></div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-cubes"></i> - GRUPO</span>
                </div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Grupo</span>
                            <?= $wFunction->montaSelect([40 => '4 - SERVIÇOS DE TERCEIROS'], null, "class='form-control' id='grupo' name='grupo'"); ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Subgrupos</span>
                            <?= $wFunction->montaSelect(array('-1' => '-- Selecione o Grupo --'), null, "class='form-control' id='subgrupo' name='subgrupo'"); ?>
                            <span id="subgrupo_loading" class="input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Tipos</span>
                            <?= $wFunction->montaSelect(array('-1' => '-- Selecione o Subgrupo --'), null, "class='form-control' id='tipo' name='tipo'"); ?>
                            <span id="tipo_loading" class="input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-id-card"></i> - DADOS PESSOAIS</span>
                </div>
                <div class="panel-body">
                    <div class="margin-bottom col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon">Nome</span>
                            <input type="text" id="nome" name="nome" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Nascimento</span>
                            <input type="text" id="data_nasci" name="data_nasci" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Sexo</span>
                            <?= $wFunction->montaSelect(['-1' => '-- Selecione --', 'M' => "Masculino", 'F' => "Feminino"], null, "class='form-control' id='sexo' name='sexo'"); ?>
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Tipo Sanguíneo</span>
                            <?= $wFunction->montaSelect($arrTipoSang, null, "class='form-control' id='tipo_sanguineo' name='tipo_sanguineo'"); ?>
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Estado Civil</span>
                            <?= $wFunction->montaSelect($arrEstadoCivil, null, "class='form-control' id='id_estado_civil' name='id_estado_civil'"); ?>
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Nacionalidade</span>
                            <input type="text" id="nacionalidade" name="nacionalidade" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Naturalidade</span>
                            <input type="text" id="naturalidade" name="naturalidade" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Telefone Fixo</span>
                            <input type="text" id="tel_fixo" name="tel_fixo" class="telMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Tel. Celular</span>
                            <input type="text" id="tel_cel" name="tel_cel" class="celMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Tel. Recado</span>
                            <input type="text" id="tel_rec" name="tel_rec" class="telMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="margin-bottom col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon">CEP</span>
                            <input type="text" id="cep" name="cep" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon">Endereço</span>
                            <input type="text" id="endereco" name="endereco" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon">Número</span>
                            <input type="text" id="numero" name="numero" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Bairro</span>
                            <input type="text" id="bairro" name="bairro" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Cidade</span>
                            <input type="text" id="cidade" name="cidade" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon">UF</span>
                            <input type="text" id="uf" name="uf" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Complemento</span>
                            <input type="text" id="bairro" name="bairro" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Escolaridade</span>
                            <?= $wFunction->montaSelect($arrEscolaridade, null, "class='form-control' id='escolaridade' name='escolaridade'"); ?>
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Estuda Atualmente</span>
                            <?= $wFunction->montaSelect(['-1' => '-- Selecione --', 'sim' => "Sim", 'nao' => "Não"], null, "class='form-control' id='estuda' name='estuda'"); ?>
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-5">
                        <div class="input-group">
                            <span class="input-group-addon">Instituição</span>
                            <input type="text" id="instituicao" name="instituicao" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Curso</span>
                            <input type="text" id="curso" name="curso" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Término em</span>
                            <input type="text" id="data_escola" name="data_escola" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-group"></i> - DADOS DA FAMÍLIA</span>
                </div>
                <div class="panel-body">
                    <div class="margin-bottom col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon">Filiação - Pai</span>
                            <input type="text" id="pai" name="pai" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Nacionalidade - Pai</span>
                            <input type="text" id="nacionalidade_pai" name="nacionalidade_pai" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon">Filiação - Mãe</span>
                            <input type="text" id="mae" name="mae" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Nacionalidade - Mãe</span>
                            <input type="text" id="nacionalidade_mae" name="nacionalidade_mae" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="boxDependentes">
                        <div class="margin-bottom col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">Nome</span>
                                <input type="text" name="nome[]" class="upperCase form-control">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">CPF</span>
                                <input type="text" name="cpf_dependente[]" class="cpfMask form-control">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-2">
                            <div class="input-group">
                                <span class="input-group-addon">Nasc.</span>
                                <input type="text" name="data_nasc[]" class="data dataMask form-control">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Tipo</span>
                            <?= $wFunction->montaSelect($arrTipoDependente, null, "class='form-control' id='id_tipo_dependente' name='id_tipo_dependente'"); ?>
                        </div>
                    </div>
                        <div class="margin-bottom col-sm-2">
                            <div class="input-group">
                                <label class="form-control checkLabel" for="nao_ir1">Não Incide IR</label>
                                <label class="input-group-addon checkInp" for="nao_ir1"><input id="nao_ir1" class="checkboxI" type="checkbox" name="nao_ir[]" value="1"></label>
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-2">
                            <div class="input-group">
                                <label class="form-control checkLabel" for="deficiente1">Deficiente</label>
                                <label class="input-group-addon checkInp" for="deficiente1"><input id="deficiente1" class="checkboxI" type="checkbox" name="deficiente[]" value="1"></label>
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-4">
                            <div class="input-group">
                                <label class="form-control checkLabel" for="fac_tec1">Cursando Faculdade ou Escola Técnica</label>
                                <label class="input-group-addon checkInp" for="fac_tec1"><input id="fac_tec1" class="checkboxI" type="checkbox" name="fac_tec[]" value="1"></label>
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-3">
                            <div class="input-group">
                                <label class="form-control checkLabel" for="possui_guarda1">Possui a Guarda</label>
                                <label class="input-group-addon checkInp" for="possui_guarda1"><input id="possui_guarda1" class="checkboxI" type="checkbox" name="possui_guarda[]" value="1"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-file"></i> - DOCUMENTAÇÃO</span>
                </div>
                <div class="panel-body">
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">CPF</span>
                            <input type="text" id="cpf" name="cpf" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Certificado de Reservista</span>
                            <input type="text" id="reservista" name="reservista" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Nº do Conselho</span>
                            <input type="text" id="rg" name="rg" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Orgão</span>
                            <input type="text" id="orgao" name="orgao" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon">UF</span>
                            <input type="text" id="uf_rg" name="uf_rg" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Expedição</span>
                            <input type="text" id="data_rg" name="data_rg" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Nº da Carteira de Trabalho</span>
                            <input type="text" id="campo1" name="campo1" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Série</span>
                            <input type="text" id="serie_ctps" name="serie_ctps" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-2">
                        <div class="input-group">
                            <span class="input-group-addon">UF</span>
                            <input type="text" id="uf_ctps" name="uf_ctps" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Expedição</span>
                            <input type="text" id="data_ctps" name="data_ctps" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Nº do Titulo de Eleitor</span>
                            <input type="text" id="titulo" name="titulo" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Zona</span>
                            <input type="text" id="zona" name="zona" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Seção</span>
                            <input type="text" id="secao" name="secao" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <hr />
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">PIS</span>
                            <input type="text" id="pis" name="pis" class="upperCase form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Expedição</span>
                            <input type="text" id="data_pis" name="data_pis" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">FGTS</span>
                            <input type="text" id="fgts" name="fgts" class="upperCase form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-institution"></i> - DADOS BANCÁRIOS</span>
                </div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <div class="margin-bottom col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">Banco</span>
                                <?= $wFunction->montaSelect($arrBanco, null, "id='banco' name='banco' class='form-control'"); ?>
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-8">
                            <div class="input-group">
                                <span class="input-group-addon">Nome do Banco (caso não esteja na lista ao lado)</span>
                                <?= $wFunction->montaSelect($banc, null, "disabled id='nome_banco' name='nome_banco' class='form-control'"); ?>
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">Agência</span>
                                <input type="text" disabled id="agencia" name="agencia" class="form-control">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-2">
                            <div class="input-group">
                                <span class="input-group-addon">DV</span>
                                <input type="text" disabled id="agencia_dv" name="agencia_dv" class="form-control" maxlength="1">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">Agência sem Digito</span>
                                <?= $wFunction->montaSelect([0 => 'Não', 1 => 'Sim'], null, " disabled id='chk_agencia' class='form-control'"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="margin-bottom col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">Conta</span>
                                <input type="text" disabled id="conta" name="conta" class="form-control">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-2">
                            <div class="input-group">
                                <span class="input-group-addon">DV</span>
                                <input type="text" disabled id="conta_dv" name="conta_dv" class="form-control" maxlength="2">
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">Tipo</span>
                                <?= $wFunction->montaSelect(['salario' => 'Conta Salário', 'poupanca' => 'Conta Poupança', 'corrente' => 'Conta Corrente'], null, " disabled id='tipo_conta' name='tipo_conta' class='form-control'"); ?>
                            </div>
                        </div>
                        <div class="margin-bottom col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">Nome do Banco</span>
                                <?= $wFunction->montaSelect($arrListaBancos, null, "id='nome_banco' name='nome_banco' class='form-control'"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span><i class="fa fa-dollar"></i> - DADOS FINANCEIROS E DE CONTRATO</span>
                </div>
                <div class="panel-body">
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Data de Entrada</span>
                            <input type="text" id="data_entrada" name="data_entrada" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon">Exame Admissional</span>
                            <input type="text" id="data_exame" name="data_exame" class="data dataMask form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                    </div>
                    <div class="margin-bottom col-sm-4">
                        <div class="input-group">
                            <span class="input-group-addon">Local de Pagamento</span>
                            <input type="text" id="localpagamento" name="localpagamento" class="form-control">
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-6">
                        <div class="input-group">
                            <span class="input-group-addon">Tipo de Pagamento</span>
                            <?= $wFunction->montaSelect($arrTipoPgto, $row['tipo_pagamento'], "id='tipo_pagamento' name='tipo_pagamento' class='form-control'"); ?>
                        </div>
                    </div>
                    <div class="margin-bottom col-sm-12">
                    </div>
                    <div class="margin-bottom col-sm-12">
                        <div class="input-group">
                            <span class="input-group-addon">Observações</span>
                            <textarea id="observacao" class="form-control" name="observacao" rows="4"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="resources/gestaoAutonomo.js" type="text/javascript"></script>
    </body>
</html>
