<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');
include('../classes/cooperativa.php');
$usuario = carregaUsuario();
$cooperativa = cooperativa::getCoop($_REQUEST['cooperativa']);
$regiao = montaQueryFirst("regioes", "*", "id_regiao={$cooperativa['id_regiao']}");
$_SESSION['voltarCooperativa']['id_regiao'] = $cooperativa['id_regiao'];


if ($cooperativa['tipo'] == '1')
    $cooperativa['tipo'] = 'COOPERATIVA';
if ($cooperativa['tipo'] == '2')
    $cooperativa['tipo'] = 'PESSOA JURÍDICA';
?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="cooperativa.css" rel="stylesheet" type="text/css" />
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <style>
            .colEsq{
                float: left;
                width: 50%;
                margin-top: -10px;
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
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>CADASTRO DE COOPERATIVA DE TRABALHO E PESSOA JURÍDICA</h2>
                    </div>
                </div>

                <fieldset>
                    <legend>Dados da Empresa Contratada</legend>
                    <p>
                        <label class='first' for="tipo">Tipo:</label>
                        <?php echo $cooperativa['tipo'] ?>
                    </p>
                    <p>
                        <label class='first' for="nome">Razão Social:</label>
                        <?php echo $cooperativa['nome'] ?>   
                    </p>
                    <p>
                        <label class='first' for="fantasia">Nome Fantasia:</label>
                        <?php echo $cooperativa['fantasia'] ?>
                    </p>
                    <p>
                        <label class='first'>Endereço:</label>
                        <?php echo $cooperativa['endereco'] ?>
                    </p>
                    <div class="colEsq">
                        <p>
                            <label class='first'>Bairro:</label>
                            <?php echo $cooperativa['bairro'] ?>
                        </p>
                        <p>
                            <label class='first'>CEP:</label>
                            <?php echo $cooperativa['cooperativa_cep'] ?>
                        </p>
                        <p>
                            <label class='first'>CNPJ:</label>
                            <?php echo $cooperativa['cnpj'] ?>
                        </p>
                        <p>
                            <label class='first'>CNAE:</label>
                            <?php echo $cooperativa['cooperativa_cnae'] ?>
                        </p>
                        <p>
                            <label class='first'>Contato:</label>
                            <?php echo $cooperativa['contato'] ?>
                        </p>
                        <p>
                            <label class='first'>Cel:</label>
                            <?php echo $cooperativa['cel'] ?>
                        </p>
                    </div>
                    <div class="colDir">
                        <p>
                            <label class='first'>Cidade:</label>
                            <?php echo $cooperativa['cidade'] ?>
                        </p>
                        <p>
                            <label class='first'>UF:</label>
                            <?php echo $cooperativa['cooperativa_uf'] ?>
                        </p>
                        <p>
                            <label class='first'>FPAS:</label>
                            <?php echo $cooperativa['cooperativa_fpas'] ?>
                        </p>
                        <p class="first">&nbsp;</p>
                        <p>
                            <label class='first'>Telefone:</label>
                            <?php echo $cooperativa['tel'] ?>
                        </p>
                        <p>
                            <label class='first'>Fax:</label>
                            <?php echo $cooperativa['fax'] ?>
                        </p>
                    </div>

                    <p class="clear">
                        <label class='first'>E-mail:</label>
                        <?php echo $cooperativa['email'] ?>
                    </p>

                    <p>
                        <label class='first'>Site:</label>
                        <?php echo $cooperativa['site'] ?>
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Dados dos Administradores</legend>

                    <fieldset>
                        <legend>Presidente</legend>
                        <div class="colEsq">
                            <p>
                                <label class="first" for="presidente">Nome:</label>
                                <?php echo $cooperativa['presidente'] ?>
                            </p>
                            <p>
                                <label class="first" for="rgp">RG:</label>
                                <?php echo $cooperativa['rgp'] ?>
                            </p>
                        </div>
                        <div class="colDir">
                            <p>
                                <label class="first" for="matriculap">Matricula:</label>
                                <?php echo $cooperativa['matriculap'] ?>
                            </p>
                            <p>
                                <label class="first" for="cpfp">CPF:</label>
                                <?php echo $cooperativa['cpfp'] ?>
                            </p>
                        </div>
                        <p class="clear">
                            <label class="first" for="enderecop">Endereço:</label>
                            <?php echo $cooperativa['enderecop'] ?>
                        </p>
                    </fieldset>

                    <fieldset>
                        <legend>Diretor</legend>
                        <div class="colEsq">
                            <p>
                                <label class="first" for="Diretor">Nome:</label>
                                <?php echo $cooperativa['diretor'] ?>
                            </p>
                            <p>
                                <label class="first" for="rgd">RG:</label>
                                <?php echo $cooperativa['rgd'] ?>
                            </p>
                        </div>
                        <div class="colDir">
                            <p>
                                <label class="first" for="matriculad">Matricula:</label>
                                <?php echo $cooperativa['matriculad'] ?>
                            </p>
                            <p>
                                <label class="first" for="cpfd">CPF:</label>
                                <?php echo $cooperativa['cpfd'] ?>
                            </p>
                        </div>
                        <p class="clear">
                            <label class="first" for="enderecod">Endereço:</label>
                            <?php echo $cooperativa['enderecod'] ?>
                        </p>
                    </fieldset>

                    <p>
                        <label class="first" for="entidade">Entidade Sindical Vinculada:</label>
                        <?php echo $cooperativa['entidade'] ?>
                    </p>

                    <div class="colEsq">
                        <p>
                            <label class="first" for="fundo">Fundo reserva:</label>
                            <?php echo $cooperativa['fundo'] ?>
                        </p>
                        <p>
                            <label class="first" for="taxa">Taxa Administrativa:</label>
                            <?php echo $cooperativa['taxa'] ?>
                        </p>
                    </div>

                    <div class="colDir">
                        <p>
                            <label class="first" for="parcela">Quantidade de Parcelas:</label>
                            <?php echo $cooperativa['parcelas'] ?>
                        </p>
                        <p>
                            <label class="first" for="bonificacao">Bonificação:</label>
                            <?php echo $cooperativa['bonificacao'] ?>
                        </p>
                    </div>
                    <p class="clear">
                        <label class="first">Logo:</label>
                        <?php
                        if (isset($cooperativa['foto']) && !empty($cooperativa['foto'])) {
                            ?>
                            <img src="<?php echo "logos/coop_" . $cooperativa['id_coop'] . $cooperativa['foto']; ?>" title="Logo da Cooperativa" alt="Logo da Cooperativa" style="min-width:100px; min-height: 100px;">
                            <?php
                        }else{
                            ?>
                            Não disponível.
                                <?php
                        }
                        ?>
                    </p>
                    <p>
                        <label for="cursos" class="first">Realizador do Curso de Cooperativismo:</label>
                        <?php echo $cooperativa['cursos'] ?>
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Dados Bancários</legend>
                    <p>
                        <label class="first" class="banco">Banco:</label>
                            <?php
                            $result_banco = mysql_query("SELECT id_banco,nome FROM bancos where id_regiao = '$regiao'");
                            while ($row_banco = mysql_fetch_array($result_banco)) {
                                if($cooperativa['id_banco'] == $row_banco['id_banco']){
                                    echo $row_banco['id_banco'].' - '.$row_banco['nome'];
                                }
                            }
                            ?>
                    </p>
                </fieldset>

                <p class="controls"> <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'cooperativa_nova.php';" /> </p>
            </form>
        </div>
    </body>
</html>