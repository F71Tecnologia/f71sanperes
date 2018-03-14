<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/PrestadorServicoClass.php');

$usuario = carregaUsuario();

$prestador = PrestadorServico::getPrestador($_REQUEST['prestador']);
$projeto = montaQueryFirst("projeto", "*" , "id_projeto={$prestador['id_projeto']}");
$regiao = montaQueryFirst("regioes", "*" , "id_regiao={$prestador['id_regiao']}");
$id_prestador = $prestador['id_prestador'];

$socios = montaQuery("prestador_socio","*","id_prestador = {$id_prestador}");
$num_socios = count($socios);

$dependentes = montaQuery("prestador_dependente","*","prestador_id = {$id_prestador}");
$num_dependentes = count($dependentes);
$grauParentesco = montaQuery("grau_parentesco");
$optParentesco = array(0 => "« Selecione o Grau de Parentesco »");
//Montar um array com os tipos de graus de parentesco possiveis,
//retornados da tabela grau_parentesco
foreach($grauParentesco as $value) {
    $optParentesco[$value['id_grau']] = $value['nome'];
}

$_SESSION['voltarPrestador']['id_regiao'] = $prestador['id_regiao'];
$_SESSION['voltarPrestador']['id_projeto'] = $prestador['id_projeto'];

switch ($prestador['prestador_tipo']) {
    case 1:
        $tipoContrato = "Pessoa Jurídica";
        break;
    case 2:
        $tipoContrato = "Pessoa Jurídica - Cooperativa";
        break;
    case 3:
        $tipoContrato = "Pessoa Física";
        break;
    case 4:
        $tipoContrato = "Pessoa Jurídica - Prestador de Serviço";
        break;
    case 5:
        $tipoContrato = "Pessoa Jurídica - Administradora";
        break;
    case 6:
        $tipoContrato = "Pessoa Jurídica - Publicidade";
        break;
    case 7:
        $tipoContrato = "Pessoa Jurídica Sem Retenção";
        break;
}

$contrato = ($prestador['prestacao_contas'] == 1)?'Sim':'Não';
$medida = PrestadorServico::getUnidadeMedida($prestador['id_medida']);

?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        

        <script>
            $(function() {

            });
        </script>
        <style>
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
            }
            .colDir{ 
                width: auto;
                min-width: 0px;
                margin-left: 0px;
                min-height: 0px;
                border: 0px solid #ccc;
                padding: 0px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 1000px;">
            <form action="form_prestador.php" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Prestador de Serviço</h2>
                    </div>
                </div>

                <fieldset>
                    <legend>Dados do Projeto</legend>
                    <div class="colEsq">
                        <p><label class='first'>Regiao:</label> <?php echo $regiao['regiao']?></p>
                        <p><label class='first'>Projeto:</label> <?php echo $projeto['nome']?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Data Inicio:</label><?php echo $prestador['contratado_embr']?></p>
                        <p><label class='first'>Data Final:</label><?php echo $prestador['encerrado_embr']?></p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Dados do Contratante</legend>
                    <p><label class='first'>Contratante:</label><?php echo $prestador['contratante']?></p>
                    <p><label class='first'>Endereço:</label><?php echo $prestador['endereco']?></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><?php echo $prestador['cnpj']?></p>
                        <p><label class='first'>Responsavel:</label><?php echo $prestador['responsavel']?></p>
                        <p><label class='first'>Nascionalidade:</label><?php echo $prestador['nacionalidade']?></p>
                        <p><label class='first'>RG:</label><?php echo $prestador['rg']?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><?php echo $prestador['civil']?></p>
                        <p><label class='first'>Formação:</label><?php echo $prestador['formacao']?></p>
                        <p><label class='first'>CPF:</label><?php echo $prestador['cpf']?></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados da Empresa Contratada</legend>
                    <p><label class='first'>Nome Fantasia:</label><?php echo $prestador['c_fantasia']?></p>
                    <p><label class='first'>Razão Social:</label><?php echo $prestador['c_razao']?></p>
                    <p><label class='first'>Endereço:</label><?php echo $prestador['c_endereco']?></p>
                    <p><label class='first'>Tipo de contrato:</label><?php echo $tipoContrato?></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><?php echo $prestador['c_cnpj']?></p>
                        <p><label class='first'>IM:</label><?php echo $prestador['c_im']?></p>
                        <p><label class='first'>Fax:</label><?php echo $prestador['c_fax']?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>IE:</label><?php echo $prestador['c_ie']?></p>
                        <p><label class='first'>Telefone:</label><?php echo $prestador['c_tel']?></p>
                    </div>
                    <p class="clear"><label class='first'>E-mail:</label><?php echo $prestador['c_email']?></p>
                    
                    <div class="colEsq">
                        <p><label class='first'>Responsavel:</label><?php echo $prestador['c_responsavel']?></p>
                        <p><label class='first'>Nascionalidade:</label><?php echo $prestador['c_nacionalidade']?></p>
                        <p><label class='first'>RG:</label><?php echo $prestador['c_rg']?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><?php echo $prestador['c_civil']?></p>
                        <p><label class='first'>Formação:</label><?php echo $prestador['c_formacao']?></p>
                        <p><label class='first'>CPF:</label><?php echo $prestador['c_cpf']?></p>
                    </div>
                    
                    <p class="clear"><label class='first'>Site:</label><?php echo $prestador['c_site']?></p>
                </fieldset>

                <fieldset>
                    <legend>Dados da pessoa de contato na contratada</legend>
                    <p><label class='first'>Nome Completo:</label><?php echo $prestador['co_responsavel']?></p>
                    <p><label class='first'>Email:</label><?php echo $prestador['co_email']?></p>
                    <div class="colEsq">
                        <p><label class='first'>Telefone:</label><?php echo $prestador['co_tel']?></p>
                        <p><label class='first'>Estado Civil:</label><?php echo $prestador['co_civil']?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Fax:</label><?php echo $prestador['co_fax']?></p>
                        <p><label class='first'>Nacionalidade:</label><?php echo $prestador['co_nacionalidade']?></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados do contrato</legend>
                    <p><label class='first-2'>Tem contrato?</label><?php echo $contrato ?></p>
                    <p><label class='first-2'>Assunto:</label><?php echo $prestador['assunto']?></p>
                    <p><label class='first-2'>Objeto:</label><?php echo $prestador['objeto']?></p>
                    <p><label class='first-2'>Especificação:</label><?php echo $prestador['especificacao']?></p>
                    <p><label class='first-2'>Município onde será<br>executado o serviço:</label><?php echo $prestador['co_municipio']?></p>
                    <div class="colEsq">
                        <p><label class='first-2'>Unidade de Medida:</label><?php echo $medida['medida']?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Valor:</label><?php if($prestador['valor'] > 0) {echo number_format($prestador['valor'],2,",",".");}else{echo "0";}?></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados Bancários</legend>
                    <p><label class='first'>Banco:</label><?php echo $prestador['nome_banco'] ?></p>
                    <p><label class='first'>Agência:</label><?php echo $prestador['agencia']?></p>
                    <p><label class='first'>Conta:</label><?php echo $prestador['conta']?></p>
                </fieldset>
                
                <fieldset>
                    <legend>Sócios</legend>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>CPF</th>
                            </tr>
                        </thead>
                        <tbody id="socios">
                            <?php //Enquanto houver s[ocios no array retornado
                            //irá criar e adicionar campos com as informações do dependente
                            for($cont = 1; $cont <= $num_socios; $cont++) {?>
                            <tr id="socio<?php echo $cont; ?>">
                                <td><?php echo $socios[$cont]['nome']; ?></td>
                                <td><?php echo $socios[$cont]['tel']; ?></td>
                                <td><?php echo $socios[$cont]['cpf']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>Dependentes</legend>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Grau Parentesco</th>
                            </tr>
                        </thead>
                        <tbody id="dependentes">
                            <?php //Enquanto houver dependentes no array retornado
                            //irá criar e adicionar campos com as informações do dependente
                            for($cont = 1; $cont <= $num_dependentes; $cont++) {?>
                            <tr id="dependente<?php echo $cont; ?>">
                                <td><?php echo $dependentes[$cont]['prestador_dep_nome']; ?></td>
                                <td><?php echo $dependentes[$cont]['prestador_dep_tel']; ?></td>
                                <td><?php echo $optParentesco[$dependentes[$cont]['prestador_dep_parentesco']];?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </fieldset>
                <input type="hidden" name="prestador" value="<?php echo $id_prestador; ?>" />
                <p class="controls"> <input type="submit" name="submit" id="submit" value="Editar" /> <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /> </p>
            </form>
        </div>
    </body>
</html>