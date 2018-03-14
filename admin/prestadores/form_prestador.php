<?php
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("./PrestadorServicoClass.php");
include("./PrestadorMedidaClass.php");
include("./PrestadorSocioClass.php");
include("./PrestadorDependenteClass.php");
include("./ImpostoAssocClass.php");
include("./MunicipiosClass.php");

include("../../classes/ContabilImpostosClass.php");
include("../../classes/ContabilEmpresaClass.php");
include("../../classes/CnaeClass.php");

$usuario = carregaUsuario();

$objPrestador = new PrestadorServicoClass();
$objMunicipio = new MunicipiosClass();
$objMedida = new MedidaClass();
$objSocio = new SocioClass();
$objDependente = new PrestadorDependenteClass();

$objImposto = new ContabilImposto();
$objImpostoAssoc = new ImpostoAssoc();
$objEmpresa = new ContabilEmpresa();
$objCNAE = new Cnae();


$cnae = $objCNAE->geraSelect();

$empresaSELECT = $objEmpresa->consultar(array(), 'ORDER BY razao');
$arrayEmpresas['0'] = '-- Selecione --';
foreach ($empresaSELECT as $key => $value) {
    $arrayEmpresas[$key] = mascara_string($mask_cnpj, $value['cnpj']) . ' - ' . $value['razao'];
}

$empresa = $objEmpresa->consultar(array(), 'ORDER BY razao');
$arrayEmpresas['-1'] = '-- Selecione --';
foreach ($empresa as $key => $value) {
    $arrayEmpresas[$key] = mascara_string('##.###.###/####-##', $value['cnpj']) . ' - ' . $value['razao'];
}

$dadosMaster = masterId($usuario['id_master']);

$grauParentesco = montaQuery("grau_parentesco");
$optParentesco = array('' => "« Selecione o Parentesco »");
foreach ($grauParentesco as $value) {
    $optParentesco[$value['id_grau']] = $value['nome'];
}


if (!empty($_REQUEST['id_prestador'])) {
    $objPrestador->setId_prestador($_REQUEST['id_prestador']);
    if ($objPrestador->getPrestador()) {
        $objPrestador->getRowPrestador();
    } else {
        echo $objPrestador->getError();
        exit;
    }
    $action = array('editar_prestador', 'Editar Prestador');
} else {
    $action = array('cadastrar_prestador', 'Cadastrar Prestador');
}
//print_array($objPrestador);
//Array com os tipos de contrato
$arrTipos = array(
    '-1' => "-- Selecione --",
    "1" => "Pessoa Jurídica",
    "2" => "Pessoa Jurídica - Cooperativa",
    "3" => "Pessoa Física",
    "4" => "Pessoa Jurídica - Prestador de Serviço",
    "5" => "Pessoa Jurídica - Administradora",
    "6" => "Pessoa Jurídica - Publicidade",
    "7" => "Pessoa Jurídica Sem Retenção",
    "9" => "Pessoa Jurídica - Médico");


$optImpostos['-1'] = '« Selecione o Imposto »';
$objImposto->getImpostos();
while ($objImposto->getRowImposto()) {
    $optImpostos[$objImposto->getIdImposto()] = $objImposto->getSigla();
}

$objMedida->getMedidas();
while ($objMedida->getRowMedida()) {
    $arrMedida[$objMedida->getIdMedida()] = $objMedida->getMedida();
}

//Array com os possíveis estados civis
$arrEstadoCivil = array(0 => "« Selecione um Estado Civil »", 1 => "Solteiro(a)", 2 => "Casado(a)", 3 => "Divorciado(a)", 4 => "Viúvo(a)");


$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "Administrativo", "id_form" => "form1", "ativo" => $action[1]);
$breadcrumb_pages = array("Principal" => "../index.php", "Gestão de Prestadores" => "index.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $action[1] ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - <?= $action[1] ?></small></h2></div>
                </div>
            </div>
            <form action="../actions/action_prestadores.php" method="post" id="form_prestador" class="form-horizontal top-margin1" enctype="multipart/form-data">
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-sm-2">Projeto</label>
                                <div class="col-sm-4"><?php echo montaSelect(getProjetos($usuario['id_regiao']), $objPrestador->getId_projeto(), "name='id_projeto' id='id_projeto' class='form-control input-sm validate[custom[select]]'") ?></div>
                                <label class="control-label col-sm-1">Periodo</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" name="contratado_em" value="<?= $objPrestador->getContratado_em("d/m/Y") ?>" class="data form-control input-sm no-padding-hr text-center validate[required]">
                                        <div class="input-group-addon">até</div>
                                        <input type="text" name="encerrado_em" value="<?= $objPrestador->getEncerrado_em("d/m/Y") ?>" class="data form-control input-sm no-padding-hr text-center">
                                    </div>
                                </div>
                            </div>

                            <h3><small>Dados do Contratante</small></h3><hr>

                            <div class="form-group">
                                <label class="control-label col-sm-2">Contratante</label>
                                <div class="col-sm-4"><input name="contratante" id="contratante" value="<?= $dadosMaster['razao'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                                <label class="control-label col-sm-1">CNPJ</label>
                                <div class="col-sm-4"><input name="cnpj" id="cnpj" value="<?= $dadosMaster['cnpj'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Endere&ccedil;o</label>
                                <div class="col-sm-9"><input name="endereco" id="endereco" value="<?= $dadosMaster['endereco'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">Responsável</label>
                                <div class="col-sm-4"><input name="responsavel" id="responsavel" value="<?= $dadosMaster['responsavel'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                                <label class="control-label col-sm-1 text-sm">Nacionalidade</label>
                                <div class="col-sm-4"><input name="nacionalidade" id="nacionalidade" value="<?= $dadosMaster['nacionalidade'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2">RG</label>
                                <div class="col-sm-4"><input name="rg" id="rg" value="<?= $dadosMaster['rg'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                                <label class="control-label col-sm-1">CPF</label>
                                <div class="col-sm-4"><input name="cpf" id="cpf" value="<?= $dadosMaster['cpf'] ?>" class="form-control input-sm validate[required]" readonly="true"></div>
                            </div>

                            <h3><small>Dados da Empresa Contratada</small></h3><hr>
                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Tipo de Contrato</label>
                                <div class="col-sm-4"><?php echo montaSelect($arrTipos, $objPrestador->getPrestador_tipo(), "id='prestador_tipo' name='prestador_tipo' class='form-control text-center input-sm validate[required]'") ?></div>
                            </div>

                            <?php $class1 = ($objPrestador->getPrestador_tipo() != 3 && $objPrestador->getNumRowsPrestador() > 0) ? '' : 'hidden' ?>
                            <div id="empresa" class="<?= $class1 ?>">
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Empresa Contratada</label>
                                    <div class="col-sm-7"><?= montaSelect($arrayEmpresas, $objPrestador->getIdContabilEmpresa(), "id='id_empresa' name='id_empresa' class='form-control text-center input-sm validate[required]'"); ?></div>
                                    <div class="col-sm-2">
                                        <?php if (empty($objPrestador->getIdContabilEmpresa())) { ?>
                                            <button type="button" class="btn btn-info btn-sm btn-block" id="add_empresa"><i class="fa fa-plus"></i> Nova Empresa</button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Especialidade (CNAE)</label>
                                    <div class="col-sm-9">
                                        <select class="form-control input-sm validate[required]" name="cnae" id="cnae">
                                            <option value="-1">-- Selecione --</option>
                                            <optgroup label="Mais Usados">
                                                <?php foreach ($cnae[1] as $key1 => $value1) { ?>
                                                    <option value="<?= $key1 ?>" <?= ($objPrestador->getId_cnae() == $key1) ? 'selected' : '' ?>><?= $value1 ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <optgroup label="Outros">
                                                <?php foreach ($cnae[0] as $key2 => $value2) { ?>
                                                    <option value="<?= $key2 ?>" <?= ($objPrestador->getId_cnae() == $key2) ? 'selected' : '' ?>><?= $value2 ?></option>
                                                <?php } ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php $class2 = ($objPrestador->getPrestador_tipo() == 3 && $objPrestador->getNumRowsPrestador() > 0) ? '' : 'hidden' ?>
                            <div id="pfisica" class="<?= $class2 ?>">
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Especialidade</label>
                                    <div class="col-sm-4"><input type="text" class="form-control input-sm validate[required]"></div>
                                    <!--<div class="col-sm-4"><?php echo montaSelect($optEspecialidades, $prestador['especialidade'], " name='c_especialidade' id='c_especialidade' class='form-control text-center input-sm validate[required]' ") ?></div>-->
                                    <!--<div class="col-sm-4"><?php echo montaSelect(array('' => 'OUTRO', 'AMBULATORIAL' => 'Ambulatorial', 'HOSPITALAR' => 'Hospitalar'), $prestador['especialidade'], " name='c_especialidade' id='c_especialidade' class='form-control text-center input-sm validate[required]' ") ?></div>-->
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm">Nome Fantasia</label>
                                    <div class="col-sm-4"><input name="c_fantasia" id="c_fantasia" value="<?= $objPrestador->getC_fantasia() ?>" class="form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">Raz&atilde;o Social</label>
                                    <div class="col-sm-4"><input name="c_razao" id="c_razao" value="<?= $objPrestador->getC_razao() ?>" class="form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label col-sm-2 text-sm">CEP</label>
                                    <div class="col-sm-1"><input name="c_cep" id="c_cep" value="<?= $objPrestador->getC_cep() ?>" class="form-control input-sm no-padding-hr validate[required] cep"></div>
                                    <label class="control-label col-sm-1 no-padding-l">Bairro</label>
                                    <div class="col-sm-2"><input name="c_bairro" id="c_bairro" value="<?= $objPrestador->getC_bairro() ?>" readonly class="form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm">Endere&ccedil;o</label>
                                    <div class="col-sm-4"><input name="c_endereco" id="c_endereco" value="<?= $objPrestador->getC_endereco() ?>" readonly class="form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm">UF</label>
                                    <div class="col-sm-1">
                                        <?php $objMunicipio->getAllUf(); ?>
                                        <select name="c_uf" id="c_uf" readonly class="form-control text-center input-sm validate[required]">
                                            <option value=""></option>
                                            <?php
                                            while ($objMunicipio->getRowMunicipio()) {
                                                $selected = ($objPrestador->getC_uf() == $objMunicipio->getSigla()) ? " selected " : "";
                                                echo '<option value="' . $objMunicipio->getSigla() . '" ' . $selected . '>' . $objMunicipio->getSigla() . '</option>';
                                            }
                                            ?>  
                                        </select>
                                    </div>
                                    <label class="control-label col-sm-1 text-sm">Cidade</label>
                                    <?php
                                    $objMunicipio->setIdMunicipio($objPrestador->getC_cod_cidade());
                                    $objMunicipio->getAllMunicipios();
                                    $objMunicipio->getRowMunicipio();
                                    ?>
                                    <div class="col-sm-2"><input name="c_cidade" id="c_cidade" value="<?= $objMunicipio->getMunicipio() ?>" readonly class="form-control input-sm validate[required]"><input type="hidden" name="c_cod_cidade" id="c_cod_cidade" value="<?= $objMunicipio->getIdMunicipio() ?>"></div>
                                    <label class="control-label col-sm-1 text-sm">Nº</label>
                                    <div class="col-sm-1"><input name="c_numero" id="c_numero" value="<?= $objPrestador->getC_numero() ?>" class="form-control text-center input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm">Complemento</label>
                                    <div class="col-sm-2"><input name="c_complemento" id="c_complemento" value="<?= $objPrestador->getC_complemento() ?>" class="form-control input-sm"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">CNPJ</label>
                                    <div class="col-sm-4"><input name="c_cnpj" id="c_cnpj" value="<?= $objPrestador->getC_cnpj() ?>" class="form-control input-sm validate[required] cnpj"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">Telefone</label>
                                    <div class="col-sm-4"><input name="c_tel" id="c_tel" value="<?= $objPrestador->getC_tel() ?>" class="telefone form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">IE</label>
                                    <div class="col-sm-4"><input name="c_ie" id="c_ie" value="<?= $objPrestador->getC_ie() ?>" class="form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">IM</label>
                                    <div class="col-sm-4"><input name="c_im" id="c_im" value="<?= $objPrestador->getC_im() ?>" class="form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Fax</label>
                                    <div class="col-sm-4"><input name="c_fax" id="c_fax" value="<?= $objPrestador->getC_fax() ?>" class="telefone form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">E-mail</label>
                                    <div class="col-sm-4"><input name="c_email" id="c_email" value="<?= $objPrestador->getC_email() ?>" class="form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Responsavel</label>
                                    <div class="col-sm-4"><input name="c_responsavel" id="c_responsavel" value="<?= $objPrestador->getC_responsavel() ?>" class="form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">Estado Civil</label>
                                    <div class="col-sm-4"><?php echo montaSelect($arrEstadoCivil, $objPrestador->getC_civil(), "name='c_civil' id='c_civil' class='form-control input-sm validate[required]'") ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Nacionalidade</label>
                                    <div class="col-sm-4"><input name="c_nacionalidade" id="c_nacionalidade" value="<?= $objPrestador->getC_nacionalidade() ?>" class="form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">Forma&ccedil;&atilde;o</label>
                                    <div class="col-sm-4"><input name="c_formacao" id="c_formacao" value="<?= $objPrestador->getC_formacao() ?>" class="form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">RG</label>
                                    <div class="col-sm-4"><input name="c_rg" id="c_rg" value="<?= $objPrestador->getC_rg() ?>" class="form-control input-sm validate[required]"></div>
                                    <label class="control-label col-sm-1 text-sm no-padding-l">CPF</label>
                                    <div class="col-sm-4"><input name="c_cpf" id="c_cpf" value="<?= $objPrestador->getC_cpf() ?>" class=" cpf form-control input-sm validate[required]"></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-sm no-padding-l">Site</label>
                                    <div class="col-sm-9"><input name="c_site" id="c_site" value="<?= $objPrestador->getC_site() ?>" class="form-control input-sm validate[required]"></div>
                                </div>
                            </div>


                            <h3><small>Dados da Pessoa de Contato na Contratada</small></h3><hr>

                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Nome</label>
                                <div class="col-sm-4"><input name="co_responsavel" id="co_responsavel" value="<?= $objPrestador->getCo_responsavel() ?>" class="form-control input-sm validate[required]"></div>
                                <label class="control-label col-sm-1 text-sm no-padding-l">E-mail</label>
                                <div class="col-sm-4"><input name="co_email" id="co_email" value="<?= $objPrestador->getCo_email() ?>" class="form-control input-sm validate[required]"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Telefone</label>
                                <div class="col-sm-4"><input name="co_tel" id="co_tel" value="<?= $objPrestador->getCo_tel() ?>" class="telefone form-control input-sm validate[required]"></div>
                                <label class="control-label col-sm-1 text-sm no-padding-l">Fax</label>
                                <div class="col-sm-4"><input name="co_fax" id="co_fax" value="<?= $objPrestador->getCo_fax() ?>" class="telefone form-control input-sm validate[required]"></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Estado Civil</label>
                                <div class="col-sm-4"><?php echo montaSelect($arrEstadoCivil, $objPrestador->getCo_civil(), "name='co_civil' id='co_civil' class='form-control input-sm validate[required]'") ?></div>
                                <label class="control-label col-sm-1 text-sm no-padding-l">Nacionalidade</label>
                                <div class="col-sm-4"><input name="co_nacionalidade" id="co_nacionalidade" value="<?= $objPrestador->getCo_nacionalidade() ?>" class="form-control input-sm validate[required]"></div>
                            </div>

                            <h3><small>Dados do Contrato</small></h3><hr>

                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Tem Contrato?</label>
                                <div class="col-sm-2"><?php echo montaSelect(array("1" => "SIM", "0" => "NÃO"), $objPrestador->getPrestacao_contas(), "name='prestacao_contas' id='prestacao_contas' class='form-control input-sm validate[required]'") ?></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Objeto</label>
                                <div class="col-sm-9"><textarea name="objeto" id="objeto" class="form-control input-sm"><?= $objPrestador->getObjeto() ?></textarea></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Unidade Medida</label>
                                <div class="col-sm-4"><?php echo montaSelect($arrMedida, $objPrestador->getId_medida(), "name='id_medida' id='id_medida' class='form-control input-sm'") ?></div>
                                <label class="control-label col-sm-1 text-sm no-padding-l">Valor</label>
                                <div class="col-sm-4"><input name="valor" id="valor" value="<?= $objPrestador->getValor() ?>" class="valor form-control input-sm validate[required]"></div>
                            </div>

                            <h3><small>Dados Bancários</small></h3><hr>

                            <div class="form-group">
                                <label class="control-label col-sm-2 text-sm no-padding-l">Banco</label>
                                <div class="col-sm-3"><input name="nome_banco" id="nome_banco" value="<?= $objPrestador->getNome_banco() ?>" class="form-control input-sm validate[required]"></div>
                                <label class="control-label col-sm-1 text-sm no-padding-l">Agência</label>
                                <div class="col-sm-2"><input name="agencia" id="agencia" value="<?= $objPrestador->getAgencia() ?>" class="form-control input-sm validate[required]"></div>
                                <label class="control-label col-sm-1 text-sm no-padding-l">Conta</label>
                                <div class="col-sm-2"><input name="conta" id="conta" value="<?= $objPrestador->getConta() ?>" class="form-control input-sm validate[required]"></div>
                            </div>

                            <h3><small>Impostos</small></h3><hr>
                            <div class="form-group">
                                <div class="col-sm-12"><button type="button" class="btn btn-xs btn-success adicionar_imposto"><i class="fa fa-plus-circle"></i> Adicionar Imposto</button></div>
                            </div>
                            <table class="table table-bordered table-condensed text-sm valign-middle">
                                <thead>
                                    <tr class="tr-bg-info">
                                        <th>Imposto</th>
                                        <th>Al&iacute;quota</th>
                                        <th>A&ccedil;&otilde;es</th>
                                    </tr>
                                </thead>
                                <tbody class="body_imposto">
                                    <?php
                                    $objImpostoAssoc->setIdContrato($objPrestador->getId_prestador());
                                    $objImpostoAssoc->getImpostoAssocs();
                                    if ($objImpostoAssoc->getNumRowImpostoAssoc() > 0 && !empty($objImpostoAssoc->getIdContrato())) {
                                        while ($objImpostoAssoc->getRowImpostoAssoc()) {
                                            ?>
                                            <tr id="tr_imposto">
                                                <td style="width: 50%;"><input type="hidden" name="imposto[id_assoc][]" value="<?=$objImpostoAssoc->getIdAssoc()?>"><?= montaSelect($optImpostos, $objImpostoAssoc->getIdImposto(), "name='imposto[id_imposto][]' class='form-control'") ?></td>
                                                <td style="width: 30%;">
                                                    <div class="input-group">
                                                        <input type="number" max="99.99" step="any" name="imposto[aliquota][]" class="form-control" value="<?= $objImpostoAssoc->getAliquota() ?>">
                                                        <div class="input-group-addon">%</div>
                                                    </div>
                                                </td>
                                                <td class="text-center" style="width:100px">
                                                    <input type="hidden" name="imposto[id_assoc][]" value="<?= $objImpostoAssoc->getIdAssoc() ?>">
                                                    <button type="button" class="btn-remove-imposto btn btn-danger"  title="Excluir" data-id="<?= $objImpostoAssoc->getIdAssoc() ?>"><i class="fa fa-times"></i></button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr id="tr_imposto">
                                            <td><?= montaSelect($optImpostos, null, "name='imposto[id_imposto][]' class='form-control'") ?></td>
                                            <td style="width: 30%;">
                                                <div class="input-group">
                                                    <input type="text" name="imposto[aliquota][]" class="form-control">
                                                    <div class="input-group-addon">%</div>
                                                </div>
                                            </td>
                                            <td class="text-center" style="width:90px">
                                                <input type="hidden" name="imposto[id_assoc][]" value="">
                                                <button type="button" class="btn-remove-dependente btn btn-danger" title="Excluir" ><i class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer text-right">
                            <input type="hidden" name="id_prestador" id="id_prestador" value="<?= $objPrestador->getId_prestador() ?>">
                            <input type="hidden" name="id_regiao" id="id_regiao" value="<?= $usuario['id_regiao'] ?>">
                            <button type="submit" class="btn btn-primary" name="action" value="<?= $action[0] ?>"><i class="fa fa-save"></i> Salvar</button>
                        </div>
                    </div>
                </fieldset>
            </form>
            <?php include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/js/administrativo/form_prestador.js"></script>
    </body>
</html>