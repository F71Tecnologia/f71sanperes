<?php
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../classes/regiao.php');

if(!empty($_REQUEST['uf'])){
    $uf = $_REQUEST['uf'];
    $qr_municipios = mysql_query("SELECT * FROM municipios WHERE sigla = '$uf' ORDER BY municipio");
    while($row_municipios = mysql_fetch_array($qr_municipios)){
        $retorno .= '<option value="'.utf8_encode($row_municipios['municipio']).'">'.utf8_encode($row_municipios['municipio']).'</option>';
    }
    echo $retorno; exit;
}

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$REG = new regiao();

if(empty($_REQUEST['update'])) {
    
    include('../wfunction.php');
    
    $id_regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['pro'];
    $resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30, MAX(campo3) FROM autonomo WHERE id_regiao= '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo30 ASC");
    $row_maior = mysql_num_rows($resut_maior);
    $codigo = $row_maior + 1;

    // Bloqueio Administração
    echo bloqueio_administracao($id_regiao);

    $usuario = carregaUsuario();
    
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $breadcrumb_config = array("nivel"=>"", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cadastrar Autônomo");
    $breadcrumb_pages = array("Lista Projetos" => "ver.php", "Visualizar Projeto" => "ver.php?projeto=$projeto"); 
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>:: Intranet :: Cadastrar Autônomo</title>
            <link href="../favicon.png" rel="shortcut icon" />

            <!-- Bootstrap -->
            <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
            <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
            <link href="../resources/css/main.css" rel="stylesheet" media="screen">
            <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
            <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
            <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
            <link href="../css/progress.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/add-ons.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
            <style>
                .none { display: none; }
            </style>
        </head>
        <body>
            <?php include("../template/navbar_default.php"); ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Cadastrar Autônomo</small></h2></div>
                    </div>
                </div>
                <form action="<?=$_SERVER['PHP_SELF']?>" class="form-horizontal" method="post" name="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">DADOS DO PROJETO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">C&oacute;digo:</label>
                            <label class="col-xs-1 control-label"><?=$codigo?></label>
                            <div class="col-xs-3"><input name="codigo" class="form-control" type="text" id="codigo" value="<?=$codigo?>" /></div>
                            <label class="col-xs-2 control-label">Projeto:</label>
                            <label class="col-xs-4 control-label text-left"><?php $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'"); echo $projeto.' - '.mysql_result($qr_projeto, 0); ?></label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Atividade:</label>
                            <?php $sql = "SELECT * FROM curso WHERE id_regiao = '$id_regiao' AND campo3 = '$projeto' AND tipo = '1' ORDER BY nome ASC";
                            $qr_curso = mysql_query($sql);
                            $verifica_curso = mysql_num_rows($qr_curso);
                            if(!empty($verifica_curso)) { ?>
                                <div class='col-xs-4'>
                                    <select name='idcurso' id='idcurso' class='form-control'>
                                        <option selected disabled>--Selecione--</option>
                                        <?php while($row_curso = mysql_fetch_array($qr_curso)) {
                                            $salario = number_format($row_curso['salario'],2,',','.'); ?>
                                            <option value='<?=$row_curso[0]?>' ><?=$row_curso[0]?> - <?=$row_curso[campo2]?> (Valor: <?=$salario?>)</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } else { ?>
                                <label class="col-xs-4 control-label text-left">Nenhum Curso Cadastrado para o Projeto</label>
                            <?php } ?>
                            <label class="col-xs-2 control-label">Unidade:</label>
                            <?php $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
                            $verifica_unidade = mysql_num_rows($qr_unidade);
                            if(!empty($verifica_unidade)) { ?>
                                <div class='col-xs-4'>
                                    <select name="locacao" id="locacao" class="form-control">
                                        <option value="">Selecione</option>
                                        <?php while($row_unidade = mysql_fetch_array($qr_unidade)) { ?>
                                            <option value='<?=$row_unidade[unidade]?>' ><?=$row_unidade[id_unidade]?> - <?=$row_unidade[unidade]?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            <?php } else { ?>
                                <label class="col-xs-4 control-label text-left">Nenhum Curso Cadastrado para o Projeto</label>
                            <?php } ?>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label class="col-xs-2 control-label">Tipo Contrata&ccedil;&atilde;o:</label>
                            <label class="col-xs-4 control-label text-left"><input name="contratacao" type="radio" class="reset" id="contratacao" value="1" checked="checked"/> Autônomo</label>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t">DADOS PESSOAIS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-2"><input name="nome" type="text" id="nome" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                            <label class="col-xs-2 control-label">Data de Nascimento:</label>
                            <div class="col-xs-2"><input name="data_nasci" type="text" id="data_nasci" class="form-control" maxlength="10" onkeyup="mascara_data(this);" /></div>
                            <label class="col-xs-2 control-label">Tipo Sanguíneo:</label>
                            <div class="col-xs-2">
                                <select name="tiposanguineo" class="form-control">
                                    <option value="">Selecione</option>
                                    <?php 
                                    $query = "select * from tipo_sanguineo";
                                    $rsquery = mysql_query($query);
                                    while ($i = mysql_fetch_assoc($rsquery)) { ?>
                                        <option value="<?=$i["nome"] ?>"><?=$i["nome"] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Estado Civil:</label>
                            <div class="col-xs-2">
                                <select name="civil" id="civil" class="form-control">
                                    <?php $qr_estCivil = mysql_query("SELECT * FROM estado_civil");
                                    while ($row_estCivil = mysql_fetch_assoc($qr_estCivil)) {
                                        echo '<option value="' . $row_estCivil['id_estado_civil'] . '|'.$row_estCivil['nome_estado_civil'].'">' . $row_estCivil['nome_estado_civil'] . '</option>';
                                    } ?>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Sexo:</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="sexo" value="M" checked="checked" /> Masc.</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="sexo" value="F" /> Fem.</label>
                            <label class="col-xs-2 control-label">Nacionalidade:</label>
                            <div class="col-xs-2"><input name="nacionalidade" type="text" id="nacionalidade" class="form-control" /></div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Endereço:</label>
                            <div class="col-xs-2"><input name="endereco" type="text" id="endereco" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                            <label class="col-xs-2 control-label">Bairro:</label>
                            <div class="col-xs-2"><input name="bairro" type="text" id="bairro" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            <label class="col-xs-2 control-label">Naturalidade:</label>
                            <div class="col-xs-2"><input name="naturalidade" type="text" id="naturalidade" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">UF:</label>
                            <div class="col-xs-2"><?=$REG->SelectUFajax('uf','class="form-control"')?></div>
                            <label class="col-xs-2 control-label">Cidade:</label>
                            <div class="col-xs-2"><select name="cidade" id="cidade" class="form-control"></select></div>
                            <label class="col-xs-2 control-label">CEP:</label>
                            <div class="col-xs-2"><input name="cep" type="text" id="cep" class="form-control" maxlength="9" OnKeyPress="formatar('#####-###', this)" onKeyUp="pula(9,this.id,naturalidade.id)" /></div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Estuda Atualmente?</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="estuda" value="sim" class="reset" checked="checked" /> SIM</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="estuda" value="não" class="reset" /> NÃO</label>
                            <label class="col-xs-2 control-label">Término em:</label>
                            <div class="col-xs-2"><input name="data_escola" type="text" id="data_escola" class="form-control" maxlength="10" onKeyUp="mascara_data(this); pula(10,this.id,escolaridade.id)"></div>
                            <label class="col-xs-2 control-label">Escolaridade:</label>
                            <div class="col-xs-2">
                                <select name="escolaridade" class="form-control">
                                    <?php $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
                                    while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) { ?>
                                        <option value="<?=$escolaridade['id']?>"><?=$escolaridade['nome']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Curso:</label>
                            <div class="col-xs-2"><input name="curso" type="text" id="zona" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                            <label class="col-xs-2 control-label">Instituição:</label>
                            <div class="col-xs-2"><input name="instituicao" type="text" id="titulo" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                            <label class="col-xs-2 control-label">Telefone Fixo:</label>
                            <div class="col-xs-2"><input name="tel_fixo" type="text" id="tel_fixo" class="form-control" onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,tel_cel.id)"></div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Celular:</label>
                            <div class="col-xs-2"><input name="tel_cel" type="text" id="tel_cel" class="form-control" onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,tel_rec.id)" /></div>
                            <label class="col-xs-2 control-label">Recado:</label>
                            <div class="col-xs-2"><input name="tel_rec" type="text" id="tel_rec" class="form-control" onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,data_nasci.id)" /></div>
                            <label class="col-xs-2 control-label">E-mail:</label>
                            <div class="col-xs-2"><input name="email" type="text" id="email" class="form-control" /></div>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t none">DADOS DA FAMÍLIA</div>
                    <div class="panel-body none">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Filiação - Pai:</label>
                            <div class="col-xs-4"><input name="pai" type="text" id="pai" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                            <label class="col-xs-2 control-label">Nacionalidade Pai:</label>
                            <div class="col-xs-4"><input name="nacionalidade_pai" type="text" id="nacionalidade_pai" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Número de Filhos:</label>
                            <div class="col-xs-4"><input name="filhos" type="text" id="filhos" class="form-control" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-4"><input name="filho_1" type="text" id="filho_1" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            <label class="col-xs-2 control-label">Nascimento:</label>
                            <div class="col-xs-4"><input name="data_filho_1" type="text" class="form-control" maxlength="10" id="data_filho_1" onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-4"><input name="filho_2" type="text" id="filho_2" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            <label class="col-xs-2 control-label">Nascimento:</label>
                            <div class="col-xs-4"><input name="data_filho_2" type="text" class="form-control" maxlength="10" id="data_filho_2" onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-4"><input name="filho_3" type="text" id="filho_3" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            <label class="col-xs-2 control-label">Nascimento:</label>
                            <div class="col-xs-4"><input name="data_filho_3" type="text" class="form-control" maxlength="10" id="data_filho_3" onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-4"><input name="filho_4" type="text" id="filho_4" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            <label class="col-xs-2 control-label">Nascimento:</label>
                            <div class="col-xs-4"><input name="data_filho_4" type="text" class="form-control" maxlength="10" id="data_filho_4" onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome:</label>
                            <div class="col-xs-4"><input name="filho_5" type="text" id="filho_5" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            <label class="col-xs-2 control-label">Nascimento:</label>
                            <div class="col-xs-4"><input name="data_filho_5" type="text" class="form-control" maxlength="10" id="data_filho_5" onKeyUp="mascara_data(this);" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t none">APARÊNCIA</div>
                    <div class="panel-body none">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Cabelos:</label>
                            <div class="col-xs-2">
                                <select name="cabelos" id="cabelos" class="form-control">
                                    <option value="">Não informado</option>
                                    <option>Loiro</option>
                                    <option>Castanho Claro</option>
                                    <option>Castanho Escuro</option>
                                    <option>Ruivo</option>
                                    <option>Pretos</option>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Olhos:</label>
                            <div class="col-xs-2">
                                <select name="olhos" id="olhos" class="form-control">
                                    <option value="">Não informado</option>
                                    <option>Castanho Claro</option>
                                    <option>Castanho Escuro</option>
                                    <option>Verde</option>
                                    <option>Azul</option>
                                    <option>Mel</option>
                                    <option>Preto</option>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Peso:</label>
                            <div class="col-xs-2"><input name="peso" type="text" id="peso" class="form-control" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Altura:</label>
                            <div class="col-xs-2"><input name="altura" type="text" id="altura" class="form-control" /></div>
                            <label class="col-xs-2 control-label">Etnia:</label>
                            <div class="col-xs-2">
                                <select name="etnia" class="form-control">
                                    <option value="6">Não informado</option>
                                    <?php $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
                                    while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
                                        <option value="<?=$etnia['id']?>"><?=$etnia['nome']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Marcas ou Cicatriz:</label>
                            <div class="col-xs-2"><input name="defeito" type="text" id="defeito" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Deficiências:</label>
                            <div class="col-xs-2">
                                <select name="deficiencia" class="form-control">
                                    <option value="">Não é portador de deficiência</option>
                                    <?php $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
                                    while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
                                        <option value="<?=$deficiencia['id']?>"><?=$deficiencia['nome']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Enviar Foto:</label>
                            <label class="col-xs-1 control-label text-left"><input name="foto" type="checkbox" id="foto" onClick="document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none' ;" value="1" /></label>
                            <div class="col-xs-5"><input name="arquivo" type="file" id="arquivo" class="form-control" style="display:none;" /></div>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t">DOCUMENTAÇÃO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nº do Conselho:</label>
                            <div class="col-xs-2"><input name="rg" type="text" id="rg" class="form-control" maxlength="14"></div>
                            <label class="col-xs-2 control-label">Orgão Regulamentador:</label>
                            <div class="col-xs-2"><input name="orgao" type="text" id="orgao" class="form-control" onChange="this.value=this.value.toUpperCase()"></div>
                            <label class="col-xs-2 control-label">UF:</label>
                            <div class="col-xs-2"><input name="uf_rg" type="text" id="uf_rg" class="form-control" maxlength="2" onKeyUp="pula(2,this.id,data_rg.id)" onChange="this.value=this.value.toUpperCase()"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Data Expedição:</label>
                            <div class="col-xs-2"><input name="data_rg" type="text" class="form-control" maxlength="10" id="data_rg" onkeyup="mascara_data(this); pula(10,this.id,cpf.id)"></div>
                            <label class="col-xs-2 control-label">CPF:</label>
                            <div class="col-xs-2"><input name="cpf" type="text" id="cpf" class="form-control" maxlength="14" OnKeyPress="formatar('###.###.###-##', this)" onkeyup="pula(14,this.id,reservista.id)"></div>
                            <label class="col-xs-2 control-label">Certificado de Reservista:</label>
                            <div class="col-xs-2"><input name="reservista" type="text" id="reservista" class="form-control"></div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Nº Carteira de Trabalho:</label>
                            <div class="col-xs-2"><input name="trabalho" type="text" id="trabalho" class="form-control" /></div>
                            <label class="col-xs-2 control-label">Série:</label>
                            <div class="col-xs-2"><input name="serie_ctps" type="text" id="serie_ctps" class="form-control" /></div>
                            <label class="col-xs-2 control-label">UF:</label>
                            <div class="col-xs-2"><input name="uf_ctps" type="text" id="uf_ctps" class="form-control" maxlength="2" onKeyUp="pula(2,this.id,data_ctps.id)" onChange="this.value=this.value.toUpperCase()"></div>
                        </div>
                        <div class="form-group none">
                            <label class="col-xs-2 control-label">Data carteira de Trabalho:</label>
                            <div class="col-xs-2"><input name="data_ctps" type="text" class="form-control" maxlength="10" id="data_ctps" onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nº Título de Eleitor:</label>
                            <div class="col-xs-2"><input name="titulo" type="text" id="titulo2" class="form-control"></div>
                            <label class="col-xs-2 control-label">Zona:</label>
                            <div class="col-xs-2"><input name="zona" type="text" id="zona2" class="form-control"></div>
                            <label class="col-xs-2 control-label">Seção:</label>
                            <div class="col-xs-2"><input name="secao" type="text" id="secao" class="form-control"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">PIS:</label>
                            <div class="col-xs-2"><input name="pis" type="text" id="pis" class="form-control" maxlength="11" onkeyup="pula(11,this.id,data_pis.id)"></div>
                            <label class="col-xs-2 control-label">Data Pis:</label>
                            <div class="col-xs-2"><input name="data_pis" type="text" class="form-control" maxlength="10" id="data_pis" onkeyup="mascara_data(this); pula(10,this.id,fgts.id)"></div>
                            <label class="col-xs-2 control-label">FGTS:</label>
                            <div class="col-xs-2"><input name="fgts" type="text" id="fgts" class="form-control"></div>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t none">BENEFÍCIOS</div>
                    <div class="panel-body none">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Assistência Médica:</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="medica" value="1"> Sim</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="medica" value="0" checked="checked"> Não</label>
                            <label class="col-xs-2 control-label">Tipo de Plano:</label>
                            <div class="col-xs-2">
                                <select name="plano_medico" id="plano_medico" class="form-control">
                                    <option value="1">Familiar</option>
                                    <option value="2" selected>Individual</option>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Seguro, Apólice:</label>
                            <div class="col-xs-2">
                                <select name="apolice" id="apolice" class="form-control">
                                    <option value="0">Não Possui</option>
                                    <?php $result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $id_regiao");
                                    while($row_ap = mysql_fetch_array($result_ap)) {
                                        echo "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Dependente:</label>
                            <div class="col-xs-2"><input name="dependente" type="text" id="dependente" class="form-control" onChange="this.value=this.value.toUpperCase()"></div>
                            <label class="col-xs-2 control-label">Insalubridade:</label>
                            <label class="col-xs-1 control-label text-left"><input name="insalubridade" type="checkbox" id="insalubridade2" value="1"/></label>
                            <label class="col-xs-2 control-label">Adicional Noturno:</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="ad_noturno" value="1"> Sim</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="ad_noturno" value="0" checked> N&atilde;o</label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Integrante do CIPA:</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="cipa" value="1"> Sim</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" name="cipa" value="0" checked> N&atilde;o</label>
                            <label class="col-xs-2 control-label">Vale Transporte:</label>
                            <label class="col-xs-1 control-label text-left"><input name="transporte" type="checkbox" id="transporte2" value="1"/></label>
                            <label class="col-xs-2 control-label">Tipo de Vale:</label>
                            <div class="col-xs-2">
                                <select name="tipo_vale" class="form-control">
                                    <option value="1">Cart&atilde;o</option>
                                    <option value="2">Papel</option>
                                    <option value="3">Ambos</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Cartão 1:</label>
                            <div class="col-xs-2"><input name="num_cartao" type="text" id="num_cartao" class="form-control"></div>
                            <label class="col-xs-2 control-label">Valor Total 1:</label>
                            <div class="col-xs-2"><input name="valor_cartao" type="text" id="valor_cartao" class="form-control" onkeydown="FormataValor(this,event,20,2)" /></div>
                            <label class="col-xs-2 control-label">Tipo Cartão 1:</label>
                            <div class="col-xs-2"><input name="tipo_cartao_1" type="text" id="tipo_cartao_1" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Cartão 2:</label>
                            <div class="col-xs-2"><input name="num_cartao2" type="text" id="num_cartao2" class="form-control" /></div>
                            <label class="col-xs-2 control-label">Valor Total 2:</label>
                            <div class="col-xs-2"><input name="valor_cartao2" type="text" id="valor_cartao2" class="form-control" onkeydown="FormataValor(this,event,20,2)" /></div>
                            <label class="col-xs-2 control-label">Tipo Cartão 2:</label>
                            <div class="col-xs-2"><input name="tipo_cartao_2" type="text" id="tipo_cartao_2" class="form-control" onChange="this.value=this.value.toUpperCase()" /></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">(Papel) Quantidade 1:</label>
                            <div class="col-xs-2"><input name="vale_qnt_1" type="text" id="vale_qnt_1" class="form-control"/></div>
                            <label class="col-xs-2 control-label">Valor 1:</label>
                            <div class="col-xs-2"><input name="vale_valor_1" type="text" id="vale_valor_1" class="form-control" onkeydown="FormataValor(this,event,20,2)" /></div>
                            <label class="col-xs-2 control-label">Tipo Vale 1:</label>
                            <div class="col-xs-2"><input name="tipo1" type="text" id="tipo1" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">(Papel) Quantidade 2:</label>
                            <div class="col-xs-2"><input name="vale_qnt_2" type="text" id="vale_qnt_2" class="form-control" /></div>
                            <label class="col-xs-2 control-label">Valor 2:</label>
                            <div class="col-xs-2"><input name="vale_valor_2" type="text" id="vale_valor_2" class="form-control" onkeydown="FormataValor(this,event,20,2)"></div>
                            <label class="col-xs-2 control-label">Tipo Vale 2:</label>
                            <div class="col-xs-2"><input name="tipo2" type="text" id="tipo2" class="form-control" onChange="this.value=this.value.toUpperCase()"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">(Papel) Quantidade 3:</label>
                            <div class="col-xs-2"><input name="vale_qnt_3" type="text" id="vale_qnt_3" class="form-control"></div>
                            <label class="col-xs-2 control-label">Valor 3:</label>
                            <div class="col-xs-2"><input name="vale_valor_3" type="text" id="vale_valor_3" class="form-control" onkeydown="FormataValor(this,event,20,2)"></div>
                            <label class="col-xs-2 control-label">Tipo Vale 3:</label>
                            <div class="col-xs-2"><input name="tipo3" type="text" id="tipo3" class="form-control" onChange="this.value=this.value.toUpperCase()"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">(Papel) Quantidade 4:</label>
                            <div class="col-xs-2"><input name="vale_qnt_4" type="text" id="vale_qnt_4" class="form-control"></div>
                            <label class="col-xs-2 control-label">Valor 4:</label>
                            <div class="col-xs-2"><input name="vale_valor_4" type="text" id="vale_valor_4" class="form-control" onkeydown="FormataValor(this,event,20,2)"></div>
                            <label class="col-xs-2 control-label">Tipo Vale 4:</label>
                            <div class="col-xs-2"><input name="tipo4" type="text" id="tipo4" class="form-control" onChange="this.value=this.value.toUpperCase()"></div>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t">DADOS BANCÁRIOS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Banco:</label>
                            <div class="col-xs-4">
                                <select name="banco" id="banco" class="form-control">
                                    <option value="0">Sem Banco</option>
                                        <?php $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$projeto' AND status_reg = '1'");
                                        while($row_banco = mysql_fetch_array($qr_banco)) {
                                            echo "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
                                        } ?>
                                    <option value="9999">Outro Banco</option>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Agência:</label>
                            <div class="col-xs-4"><input name="agencia" type="text" id="agencia" class="form-control"/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Conta:</label>
                            <div class="col-xs-4"><input name="conta" type="text" id="conta" class="form-control"></div>
                            <label class="col-xs-2 col-xs-offset-1 control-label text-left"><input type="radio" name="radio_tipo_conta" value="salario"> Conta Salário</label>
                            <label class="col-xs-2 control-label text-left"><input type="radio" name="radio_tipo_conta" value="corrente"> Conta Corrente</label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Nome do Banco:</label>
                            <div class="col-xs-10"><input name="nome_banco" type="text" id="nome_banco" class="form-control" class="campotexto" /></div>
                            <label class="col-xs-12 control-label text-left text-warning">(caso não esteja na lista acima)</label>
                        </div>
                    </div>
                    <div class="panel-heading text-bold border-t">DADOS FINANCEIROS E DE CONTRATO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Data de Entrada:</label>
                            <div class="col-xs-2"><input name="data_entrada" type="text" class="form-control" maxlength="10" id="data_entrada" onkeyup="mascara_data(this); pula(10,this.id,data_exame.id)"></div>
                            <label class="col-xs-2 control-label">Data do Exame Admissional:</label>
                            <div class="col-xs-2"><input name="data_exame" type="text" class="form-control" maxlength="10" id="data_exame" onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)"></div>
                            <label class="col-xs-2 control-label">Local de Pagamento:</label>
                            <div class="col-xs-2"><input name="localpagamento" type="text" id="localpagamento" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-2 control-label">Tipo de Pagamento:</label>
                            <div class="col-xs-2">
                                <select name="tipopg" id="tipopg" class="form-control">
                                    <?php $RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$projeto' AND campo1 = '1'");
                                    $Row_pg_dep = mysql_fetch_array($RE_pg_dep);

                                    $RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$projeto' AND campo1 = '2'");
                                    $Row_pg_che = mysql_fetch_array($RE_pg_che);

                                    $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$projeto'");
                                    while($row_pg = mysql_fetch_array($result_pg)) {
                                        echo "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
                                    } ?>
                                </select>
                            </div>
                            <label class="col-xs-2 control-label">Observações:</label>
                            <div class="col-xs-6"><textarea name="observacoes" id="observacoes" rows="4" class="form-control" onChange="this.value=this.value.toUpperCase()"></textarea></div>
                        </div>
                    </div>
                    <div class="panel-footer text-center" id="finalizacao">
                        <div class="form-group">
                            <label class="col-xs-6 control-label">O Contrato foi Assinado?</label>
                            <label class="col-xs-1 control-label text-left"><input name="impressos2" type="checkbox" id="impressos2" value="1" /></label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-6 control-label">O Distrato foi Assinado?</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" id="assinatura3" name="assinatura3" value="1" <?=$selected_ass_sim2; ?> > Sim</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" id="assinatura3" name="assinatura3" value="0" <?=$selected_ass_nao2;?> > Não</label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-6 control-label">Outros Documentos foram Assinados?</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" id="assinatura" name="assinatura" value="1" <?=$selected_ass_sim3?>> Sim</label>
                            <label class="col-xs-1 control-label text-left"><input type="radio" id="assinatura" name="assinatura" value="0" <?=$selected_ass_nao3?>> Não</label>
                        </div>
                        <?=$mensagem_ass?>
                    </div>
                    <div class="panel-footer text-center">
                        <input type="hidden" name="regiao" value="<?=$id_regiao?>"/>
                        <input type="hidden" name="id_projeto" value="<?=$projeto?>">
                        <input type="hidden" name="user" value="<?=$id_user?>">
                        <input type="hidden" name="update" value="1" />
                        <div class="alert alert-warning">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                        <input type="submit" name="Submit" value="CADASTRAR" class="btn btn-primary" />
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
            <script src="../resources/js/main.js"></script>
            <script src="../js/global.js"></script>
            <script src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
            <script src="../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
            <script src="../jquery/priceFormat.js" type="text/javascript"></script>
            <script type="text/javascript" src="../js/valida_documento.js"></script>
            <script language="javascript" src="../js/ramon.js"></script>
            <script type="text/javascript" src="../js/valida_documento.js"></script>
            <script type="text/javascript">
            $(function(){
                $( "#data_entrada" ).datepicker({ minDate: new Date(2009, 1 - 1, 1) });
                $( "#data_entrada" ).datepicker({ showMonthAfterYear: true });
                    
                $('#uf').on('change', function(){
                    if($(this).val() != ''){
                        $.post("cadastro_bolsista.php", {bugger:Math.random(), uf:$(this).val()}, function(resultado){
                            $("#cidade").html(resultado);
                        });
                    }
                });
            });
            </script>
            <script language="javascript">
            function validaForm() {

            d = document.form1;
            deposito = "<?=$Row_pg_dep[0]?>";
            cheque = "<?=$Row_pg_che[0]?>";

            if (d.locacao.value == "") {
                    alert("O campo Unidade deve ser preenchido!");
                    d.locacao.focus();
                    return false;
            }
            if (d.nome.value == "") {
                    alert("O campo Nome deve ser preenchido!");
                    d.nome.focus();
                    return false;
            }
            if (d.rg.value == "") {
                    alert("O campo RG deve ser preenchido!");
                    d.rg.focus();
                    return false;
            }
            if (d.cpf.value == "") {
                    alert("O campo CPF deve ser preenchido!");
                    d.cpf.focus();
                    return false;
            }

            $(function() {
                    $('#data_nasci').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });

                    $('#data_escola').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_filho_1').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_filho_2').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_filho_3').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_filho_4').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_filho_5').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_rg').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_ctps').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_pis').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_entrada').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    $('#data_exame').datepicker({
                            changeMonth: true,
                        changeYear: true
                    });
                    var cpf = $('#cpf').val().replace('.','').replace('.','').replace('-','');             

                        if(!VerificaCPF(cpf)){
                           alert('Cpf Inválido');
                            return false;
                        }
            });
            };
            </script>
    </body>
</html>
<?php } else { 

     $dataEntrada = $_REQUEST['data_entrada'];
     $ano_entrada = date("Y", strtotime(str_replace("/", "-", $dataEntrada)));
        
        if ($ano_entrada < 2009) {
            
              print "<html>
                     <head>
                     <title>:: Intranet ::</title>
                     </head>
                     <body>
                     <script type='text/javascript'>
                     alert('Digite uma data de entrada Valida');
                     history.back();
                     </script>
                     </body>
                     </html>";
                exit;
        }

// CADASTRO DE AUTÔNOMO
$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$user = $_REQUEST['user'];
// Dados de Contratação
$tipo_contratacao = $_REQUEST['contratacao'];
$id_curso = $_REQUEST['idcurso'];

//trata unidade
$locacao = explode("//", $_REQUEST['locacao']);
$locacao_nome = $locacao[0];
$locacao_id = $locacao[1];

$cooperativa = '0';
// Dados Pessoais
$nome = mysql_real_escape_string(trim($_REQUEST['nome']));
$sexo = $_REQUEST['sexo'];
$endereco =mysql_real_escape_string(trim($_REQUEST['endereco']));
$bairro = mysql_real_escape_string(trim($_REQUEST['bairro']));
$cidade = mysql_real_escape_string(trim($_REQUEST['cidade']));
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];
$data_nasci = $_REQUEST['data_nasci'];
$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];
$tipo_sanguineo = $_REQUEST['tiposanguineo'];
// Documentação
$rg = $_REQUEST['rg'];
$uf_rg = $_REQUEST['uf_rg'];
$secao = $_REQUEST['secao'];
$data_rg = $_REQUEST['data_rg'];
$cpf = $_REQUEST['cpf'];
$titulo = $_REQUEST['titulo'];
$zona = $_REQUEST['zona'];
$orgao = $_REQUEST['orgao'];
// Mais
$pai = $_REQUEST['pai'];
$mae = $_REQUEST['mae'];
$nacionalidade_pai = mysql_real_escape_string(trim($_REQUEST['nacionalidade_pai']));
$nacionalidade_mae = mysql_real_escape_string(trim($_REQUEST['nacionalidade_mae']));
$estuda = $_REQUEST['estuda'];
$data_escola = $_REQUEST['data_escola'];
$escolaridade = $_REQUEST['escolaridade'];
$instituicao = $_REQUEST['instituicao'];
$curso = $_REQUEST['curso'];
// Dados Financeiros
$data_entrada = $_REQUEST['data_entrada'];
$banco = $_REQUEST['banco'];
$agencia = $_REQUEST['agencia'];
$conta = $_REQUEST['conta'];
$nomebanco = $_REQUEST['nomebanco'];
$tipoDeConta = $_REQUEST['radio_tipo_conta'];
$localpagamento = $_REQUEST['localpagamento'];
$apolice = $_REQUEST['apolice'];
$campo1 = $_REQUEST['trabalho'];
$campo2 = $_REQUEST['dependente'];
$campo3 = $_REQUEST['codigo'];
$data_cadastro = date('Y-m-d');
$nome_banco = $_REQUEST['nome_banco'];
$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacoes = $_REQUEST['observacoes'];
$medica = $_REQUEST['medica'];
$assinatura2 = $_REQUEST['assinatura2'];
$assinatura3 = $_REQUEST['assinatura3'];
if(empty($_REQUEST['insalubridade'])) {
   $insalubridade = '0';
} else {
   $insalubridade = $_REQUEST['insalubridade'];
}
if(empty($_REQUEST['transporte'])) {
   $transporte = '0';
} else {
   $transporte = $_REQUEST['transporte'];
}
if(empty($_REQUEST['impressos2'])){
   $impressos = '0';
} else {
   $impressos = $_REQUEST['impressos2'];
}
$plano_medico = $_REQUEST['plano_medico'];
$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];
$pis_data = $_REQUEST['data_pis'];
$tipo_vale = $_REQUEST['tipo_vale'];
$num_cartao = $_REQUEST['num_cartao'];
$valor_cartao = $_REQUEST['valor_cartao'];
$tipo_cartao_1 = $_REQUEST['tipo_cartao_1'];
$num_cartao2 = $_REQUEST['num_cartao2'];
$valor_cartao2 = $_REQUEST['valor_cartao2'];
$tipo_cartao_2 = $_REQUEST['tipo_cartao_2'];
$vale_qnt_1 = $_REQUEST['vale_qnt_1'];
$vale_valor_1 = $_REQUEST['vale_valor_1'];
$tipo1 = $_REQUEST['tipo1'];
$vale_qnt_2 = $_REQUEST['vale_qnt_2'];
$vale_valor_2 = $_REQUEST['vale_valor_2'];
$tipo2 = $_REQUEST['tipo2'];
$vale_qnt_3 = $_REQUEST['vale_qnt_3'];
$vale_valor_3 = $_REQUEST['vale_valor_3'];
$tipo3 = $_REQUEST['tipo3'];
$vale_qnt_4 = $_REQUEST['vale_qnt_4'];
$vale_valor_4 = $_REQUEST['vale_valor_4'];
$tipo4 = $_REQUEST['tipo4'];
$ad_noturno = $_REQUEST['ad_noturno'];
$exame_data = $_REQUEST['data_exame'];
$trabalho_data = $_REQUEST['data_ctps'];
$reservista = $_REQUEST['reservista'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$deficiencia = $_REQUEST['deficiencia'];
$cipa = $_REQUEST['cipa'];
$etnia = $_REQUEST['etnia'];
$filho_1 = mysql_real_escape_string(trim($_REQUEST['filho_1']));
$filho_2 = mysql_real_escape_string(trim($_REQUEST['filho_2']));
$filho_3 = mysql_real_escape_string(trim($_REQUEST['filho_3']));
$filho_4 = mysql_real_escape_string(trim($_REQUEST['filho_4']));
$filho_5 = mysql_real_escape_string(trim($_REQUEST['filho_5']));
$data_filho_1 = $_REQUEST['data_filho_1'];
$data_filho_2 = $_REQUEST['data_filho_2'];
$data_filho_3 = $_REQUEST['data_filho_3'];
$data_filho_4 = $_REQUEST['data_filho_4'];
$data_filho_5 = $_REQUEST['data_filho_5'];

$email = $_POST['email'];

//Inicio Verificador CPF
$qrCpf = mysql_query("SELECT COUNT(id_autonomo) AS total FROM autonomo WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND id_regiao = '$regiao' AND tipo_contratacao = 1");
$rsCpf = mysql_fetch_assoc($qrCpf);
$totalCpf = $rsCpf['total'];
if($totalCpf > 0){ ?>

<script type="text/javascript">
        alert("Esse CPF já existe para esse projeto");
        window.history.back();
</script>

<?php exit(); }
//Fim verificador CPF

//Inicio verificador PIS
    if(strlen($pis) != 11) {
    ?>
        <script type="text/javascript">
            alert("PIS Inválido!");
            window.history.back();
        </script>
    <?php
        exit();
    }
//Fim verificador PIS

if(empty($_REQUEST['foto'])) {
	$foto = '0';
} else {
	$foto = $_REQUEST['foto'];
}
if($foto == "1") {
    $foto_banco = '1';
    $foto_up = '1';
} else {
    $foto_banco = '0';
    $foto_up = '0';
}  
/* Função para converter a data */
function ConverteData($data) {
   if(strstr($data, '/')) {
       $nova_data = implode('-', array_reverse(explode('/', $data)));
 	   return $nova_data;
   } elseif(strstr($data, '-')) {
       $nova_data = implode('/', array_reverse(explode('-', $data)));
       return $nova_data;
   } else {
       return '';
   }
}
$data_filho_1  = ConverteData($data_filho_1);
$data_filho_2  = ConverteData($data_filho_2);
$data_filho_3  = ConverteData($data_filho_3);
$data_filho_4  = ConverteData($data_filho_4);
$data_filho_5  = ConverteData($data_filho_5);
$data_nasci    = ConverteData($data_nasci);
$data_rg       = ConverteData($data_rg);
$data_escola   = ConverteData($data_escola);
$data_entrada  = ConverteData($data_entrada);
$pis_data      = ConverteData($pis_data);
$exame_data    = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);
// VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA AUTONOMO
$qr_verificando_autonomo = mysql_query("SELECT nome FROM autonomo where nome = '$nome' AND data_nasci = '$data_nasci' AND rg = '$rg' AND status = '1' AND id_projeto = $id_projeto");
$verificando_autonomo = mysql_num_rows($qr_verificando_autonomo);
if (!empty($verificando_autonomo)) {
print "
<html>
<head>
<title>:: Intranet ::</title>
</head>
<body>
ESTE PARTICIPANTE JA ESTÁ CADASTRADO: <b>$nome</b>
</body>
</html>
";
exit; 
} else { // CASO O FUNCIONÁRIO NÃO ESTEJA CADASTRADO VAI RODAR O INSERT
$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'");
$row_projeto = mysql_fetch_array($result_projeto);
$data_cadastro = date('Y-m-d');
$civil = explode('|', $civil);
$estCivilId = $civil[0];
$estCivilNome = $civil[1];

mysql_query ("INSERT INTO autonomo
(id_projeto, id_regiao, localpagamento, locacao, id_unidade, nome, sexo, 
 endereco, bairro, cidade, uf, cep, 
 tel_fixo, tel_cel, tel_rec, data_nasci, naturalidade, nacionalidade, 
 civil, rg, orgao, data_rg, cpf, titulo, zona, secao,
 pai, nacionalidade_pai, mae, nacionalidade_mae, 
 estuda, data_escola, escolaridade, instituicao, curso, 
 tipo_contratacao, banco, agencia, conta, tipo_conta, 
 id_curso, apolice, data_entrada, campo1, campo2, campo3, data_exame, 
 reservista, etnia, cabelos, altura, olhos, peso, defeito, deficiencia, 
 cipa, ad_noturno, plano, assinatura, distrato, outros,
 pis, dada_pis, data_ctps, serie_ctps, uf_ctps, uf_rg, fgts,
 insalubridade, transporte, medica, tipo_pagamento,  
 nome_banco, num_filhos, observacao, impressos, sis_user, data_cad, foto, id_cooperativa, 
 rh_vinculo, rh_status, rh_horario, rh_sindicato, rh_cbo, email, tipo_sanguineo, id_estado_civil)
    VALUES
('$id_projeto', '$regiao', '$localpagamento', '$locacao_nome','$locacao_id','$nome', '$sexo', 
 '$endereco', '$bairro', '$cidade', '$uf', '$cep',
 '$tel_fixo', '$tel_cel', '$tel_rec', '$data_nasci', '$naturalidade', '$nacionalidade',
 '$estCivilNome', '$rg', '$orgao', '$data_rg', '$cpf', '$titulo', '$zona', '$secao', 
 '$pai', '$nacionalidade_pai', '$mae', '$nacionalidade_mae', 
 '$estuda', '$data_escola', '$escolaridade', '$instituicao', '$curso',
 '$tipo_contratacao', '$banco', '$agencia', '$conta', '$tipoDeConta',
 '$id_curso', '$apolice', '$data_entrada', '$campo1', '$campo2', '$campo3', '$exame_data',
 '$reservista', '$etnia', '$cabelos', '$altura', '$olhos', '$peso', '$defeito', '$deficiencia', 
 '$cipa', '$ad_noturno', '$plano_medico', '$impressos', '$assinatura2', '$assinatura3',
 '$pis', '$pis_data', '$trabalho_data', '$serie_ctps', '$uf_ctps', '$uf_rg', '$fgts', 
 '$insalubridade', '$transporte', '$medica', '$tipopg',
 '$nome_banco', '$filhos', '$observacoes', '$impressos', '$user', '$data_cadastro', '$foto_banco', '$cooperativa',
 '$rh_vinculo', '$rh_status', '$rh_horario', '$rh_sindicato', '$rh_cbo', '$email', '$tipo_sanguineo', '$estCivilId')") or die (mysql_error());
 $row_id_participante = mysql_insert_id();
}
// Vale Transporte
if($transporte == '1') {
mysql_query ("INSERT INTO vale 
			 (id_regiao,id_projeto,id_bolsista,nome,cpf,tipo_vale,
numero_cartao,valor_cartao,quantidade,qnt1,valor1,qnt2,valor2,qnt3,valor3,qnt4,valor4,tipo1,tipo2,tipo3,tipo4,
tipo_cartao_1,tipo_cartao_2,numero_cartao2,valor_cartao2,status_vale) 
			  VALUES 
			  ('$regiao','$id_projeto','$row_id_participante','$nome','$cpf','$tipo_vale','$num_cartao','$valor_cartao',
'','$vale_qnt_1','$vale_valor_1','$vale_qnt_2','$vale_valor_2','$vale_qnt_3','$vale_valor_3',
'$vale_qnt_4','$vale_valor_4','$tipo1','$tipo2','$tipo3','$tipo4','$tipo_cartao_1','$tipo_cartao_2','$num_cartao2',
'$valor_cartao2','$transporte')") 
    or die (mysql_error());
}
//
// Dependentes
if(!empty($filhos)) {
	mysql_query("INSERT INTO dependentes (id_regiao, id_projeto, id_bolsista, contratacao, nome, data1, nome1, data2, nome2, data3, nome3, data4, nome4, data5, nome5) VALUES ('$regiao', '$id_projeto', '$row_id_participante', '$tipo_contratacao', '$nome', '$data_filho_1', '$filho_1', '$data_filho_2', '$filho_2', '$data_filho_3', '$filho_3', '$data_filho_4', '$filho_4', '$data_filho_5', '$filho_5')") or die(mysql_error());
}
//
// TV SORRINDO (Senha Aleatória)
	$n_id_curso = sprintf("%04d", $id_curso);
	$n_regiao = sprintf("%04d", $regiao);
	$n_id_bolsista = sprintf("%04d", $row_id_participante);
	
	$target = "%%%%%%";
    $senha = "";
	$dig = "";
    $consoantes = "bcdfghjkmnpqrstvwxyz1234567890bcdfghjkmnpqrstvwxyz123456789";
    $vogais = "aeiou";
    $numeros = "123456789bcdfghjkmnpqrstvwxyzaeiou";
    $a = strlen($consoantes)-1;
    $b = strlen($vogais)-1;
    $c = strlen($numeros)-1;
    for($x=0;$x<=strlen($target)-1;$x++) {
        if(substr($target,$x,1) == "@") {
            $rand = mt_rand(0,$c);
            $senha .= substr($numeros,$rand,1);
        } elseif(substr($target,$x,1) == "%") {
            $rand = mt_rand(0,$a);
            $senha .= substr($consoantes,$rand,1);
        } elseif(substr($target,$x,1) == "&") {
            $rand = mt_rand(0,$b);
            $senha .= substr($vogais,$rand,1);
        } else { 
            die("<b>Erro!</b><br><i>$target</i> é uma expressão inválida!<br><i>".substr($target,$x,1)."</i> é um caractér inválido.<br>");
        }
    }
$matricula = "$n_id_curso.$n_regiao.$n_id_bolsista-00";
mysql_query ("INSERT INTO tvsorrindo(id_bolsista,id_projeto,nome,cpf,matricula,senha,inicio) VALUES
('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die (mysql_error());
//
// Upload da Foto
$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
if($foto_up == "1") {
	if(!$arquivo) {
    	$mensagem = "Não acesse esse arquivo diretamente!";
	} else {
		$nome_arq = str_replace(" ", "_", $nome);	
		$tipo_arquivo = ".gif";
		$diretorio = "fotos/";
		$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
		$nome_arquivo = "$diretorio$nome_tmp" ;
		
		move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die ("Erro ao enviar o Arquivo: $nome_arquivo");
	}
}
//
header("Location: ver_bolsista.php?reg=$regiao&bol=$row_id_participante&pro=$id_projeto&sucesso=cadastro");
exit;
} ?>