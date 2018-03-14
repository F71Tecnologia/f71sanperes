<?php

session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/SindicatoClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $usuario['id_funcionario'];

if($_REQUEST['sindicato'] != ''){
    $sindicato = $_REQUEST['sindicato'];
}elseif($_SESSION['sindicato'] != ''){
    $sindicato = $_SESSION['sindicato'];
}

$row = getSindicatoID($sindicato);

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    cadSindicato();
}

//update
if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    alteraSindicato();
}

$projeto_selecionado = $_REQUEST['hide_projeto'];
$regiao_edita = $row['id_regiao'];
$projeto_edita = $row['id_projeto'];

//trata insert/update
if($sindicato == ''){
    $regiao = $regiao_selecionada;
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $projeto = montaSelect(getProjetos($regiao),null, "id='projeto' name='projeto'");
}else{
    $regiao = $regiao_edita;
    $acao = 'Edição';
    $botao = 'Atualizar';
    $projeto = $row['id_projeto'] . " - " . $row['nome_projeto'];
}

//trazer todos os ufs
$qr_uf = mysql_query("SELECT * FROM uf");

//dados para voltar no index com select preenchido
$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

if($regiao_selecionada == ''){
    $_SESSION['regiao_select'];
    $_SESSION['projeto_select'];
    session_write_close();
}else{
    $_SESSION['regiao_select'] = $regiao_selecionada;
    $_SESSION['projeto_select'] = $projeto_selecionado;
    session_write_close();
}
?>

<html>
    <head>
        <title>:: Intranet :: Administração de Sindicatos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="sindicato.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                //mascara                
                $("#tel, #cel, #fax").mask("(99)9999-9999?9");
                $("#cnpj").mask("99.999.999/9999-99");                
                $("#piso").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                                
                //validation engine
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
        
        <style>
            .data{
                width: 80px;
            }
            .colEsq{
                float: left;
                width: 57%;
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
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
            .aliq{
                text-align: left;
                padding: 0 0 0 27px;
            }
        </style>
        
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="" method="post" name="form1" id="form1" autocomplete="off" enctype="multipart/form-data">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2><?php echo $acao; ?> de Sindicato</h2>
                    </div>
                </div>
                
                <input type="hidden" id="sindicato" name="sindicato" value="<?php echo $row['id_sindicato']; ?>" />
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                
                <div class="form_funcoes">
                    
                    <fieldset id="func1">
                        <legend>Dados do Sindicato</legend>
                        <p>
                            <label class='first'>Nome</label>
                            <input type="text" name="nome" id="nome" size="108" class="validate[required]" value="<?php echo $row['nome']; ?>" />
                        </p>
                        <p>
                            <label class='first'>Endereço</label>
                            <input type="text" name="endereco" id="endereco" size="108" class="validate[required]" value="<?php echo $row['endereco']; ?>" />
                        </p>
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>CNPJ:</label>
                                <input type="text" name="cnpj" id="cnpj" size="30" class="validate[required]" value="<?php echo $row['cnpj']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Fax:</label>
                                <input type="text" name="fax" id="fax" size="30" value="<?php echo $row['fax']; ?>" />
                            </p>                            
                            <p>
                                <label class='first'>Celular:</label>
                                <input type="text" name="cel" id="cel" size="30" value="<?php echo $row['cel']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Telefone:</label>
                                <input type="text" name="tel" id="tel" size="30" value="<?php echo $row['tel']; ?>" />
                            </p>
                        </div>
                        
                        <div id="direita">                                                                                    
                            <p>
                                <label class='first'>Contato:</label>
                                <input type="text" name="contato" id="contato" class="validate[required]" size="30" value="<?php echo $row['contato']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Email:</label>
                                <input type="text" name="email" id="email" class="validate[custom[email]]" size="30" value="<?php echo $row['email']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Site:</label>
                                <input type="text" name="site" id="site" size="30" value="<?php echo $row['site']; ?>" />
                            </p>
                        </div>
                    </fieldset>
                    
                    <fieldset id="func1">
                        <legend>Dados da Categoria</legend>                        
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>Mês de desconto:</label>
                                <?php echo montaSelect(mesesArray(),$row['mes_desconto'],"id='mes_desconto' name='mes_desconto'"); ?>
                            </p>
                            <p>
                                <label class='first'>Féria(meses):</label>
                                <input type="text" name="ferias" id="ferias" size="30" value="<?php echo $row['ferias']; ?>" />
                            </p>
                            <p>
                                <label class='first'>13(meses):</label>
                                <input type="text" name="decimo_terceiro" id="decimo_terceiro" size="30" value="<?php echo $row['decimo_terceiro']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Evento Relacionado:</label>
                                <select name="evento" id="evento">
                                    <option value="5019" <?php echo selected('5019', $row['evento']); ?>>CONTRIBUIÇÃO SINDICAL</option>
                                </select>
                            </p>
                            <p>
                                <label class='first'>Mês de dissídio:</label>
                                <?php echo montaSelect(mesesArray(),$row['mes_dissidio'],"id='mes_dissidio' name='mes_dissidio'"); ?>
                            </p>
                            <p>
                                <label class='first'>Patronal:</label>
                                <select name="pratonal" id="pratonal">
                                    <option value="1" <?php echo selected('1', $row['pratonal']); ?>>SIM</option>
                                    <option value="2" <?php echo selected('2', $row['pratonal']); ?>>NÃO</option>
                                </select>
                            </p>
                        </div>
                        
                        <div id="direita">
                            <p>
                                <label class='first'>Piso Salarial:</label>
                                <input type="text" name="piso" id="piso" size="30" value="<?php echo number_format($row['piso'], 2, ',', '.'); ?>" />
                            </p>
                            <p>
                                <label class='first'>Entidade Sindical:</label>
                                <input type="text" name="entidade" id="entidade" size="30" placeholder="código" value="<?php echo $row['entidade']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Multa de FGTS:</label>
                                <input type="text" name="multa" id="multa" size="30" placeholder="%" value="<?php echo $row['multa']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Fração:</label>
                                <input type="text" name="fracao" id="fracao" size="30" value="<?php echo $row['fracao']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Recisão:</label>
                                <input type="text" name="recisao" id="recisao" size="30" value="<?php echo $row['recisao']; ?>" />
                            </p>
                        </div>
                    </fieldset>
                    
                <p class="controls">
                    <input type="submit" name="<?php echo strtolower($botao); ?>" id="<?php echo strtolower($botao); ?>" value="<?php echo $botao; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
                
                </div><!--form_funcoes-->                             
                
            </form>
        </div>
    </body>
</html>