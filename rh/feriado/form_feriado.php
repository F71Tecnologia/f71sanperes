<?php

session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FeriadoClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];  

if($_REQUEST['feriado'] != ''){
    $feriado = $_REQUEST['feriado'];
}elseif($_SESSION['feriado'] != ''){
    $feriado = $_SESSION['feriado'];
}

$row = getFeriadoID($feriado);

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    cadFeriado();
    $regiao_selecionada = $_SESSION['regiao'];
}else{
    $regiao_selecionada = $_REQUEST['hide_regiao'];
}

//update
if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    alteraFeriado();
}

$projeto_selecionado = $_REQUEST['hide_projeto'];
$regiao_edita = $row['id_regiao'];
$projeto_edita = $row['id_projeto'];

//trata insert/update
if($feriado == ''){
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
        <title>:: Intranet :: Feriado</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="feriado.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                //mascara                
                $("#data_feriado").mask("99/99/9999");
                                
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
            <form action="#" method="post" name="form1" id="form1" autocomplete="off" enctype="multipart/form-data">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2><?php echo $acao; ?> de Feriado</h2>
                    </div>
                </div>
                
                <input type="hidden" id="feriado" name="feriado" value="<?php echo $row['id_feriado']; ?>" />
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                
                <div class="form_funcoes">
                    
                    <fieldset id="func1">
                        <legend>Dados do Feriado</legend>                        
                        <p>
                            <label class='first'>Nome do Feriado:</label>
                            <input type="text" name="nome_feriado" id="nome_feriado" size="108" class="validate[required]" value="<?php echo $row['nome']; ?>" />
                        </p>
                        <p>
                            <label class='first'>Data:</label>
                            <input type="text" name="data_feriado" id="data_feriado" size="30" value="<?php echo ($row['data'] != '') ? date('d/m/Y', strtotime($row['data'])) : ""; ?>" class="validate[required,custom[dateBr]]" />
                        </p>
                        <p>
                            <label class='first'>Tipo:</label>
                            <input type="radio" name="tipo_feriado" id="tipo_feriado" value="Federal"
                            <?php 
                            if($row['tipo'] == 'Federal'){
                                echo "checked";
                            }
                            ?> />
                            Federal
                            <input type="radio" name="tipo_feriado" id="tipo_feriado" value="Regional"
                            <?php 
                            if($row['tipo'] == 'Regional'){
                                echo "checked";
                            }
                            ?> />
                            Regional
                        </p>
                        <p>
                            <label class='first'>Festa móvel:</label>
                            <input type="checkbox" name="movel" id="movel" value="1" <?php echo ($row['movel'] == '1') ? 'checked' : ''; ?> />
                        </p>
                        <p>
                            <label class='first'>Região:</label>
                            <select name="regiao_feriado">
                                <option value="">Selecione</option>
                                <?php 
                                $res = getRegiao(); 
                                while($row_s = mysql_fetch_array($res)){
                                ?>                                                                        
                                    <option value="<?php echo $row_s['id_regiao']; ?>" <?php echo selected($row_s['id_regiao'], $row['id_regiao']); ?>><?php echo $row_s['regiao']." - ".$row_s['sigla']; ?></option>
                                <?php } ?>
                            </select>
                        </p>
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