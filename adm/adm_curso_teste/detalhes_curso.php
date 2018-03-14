<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();

$sql = FuncoesClass::getRhHorario($_REQUEST['curso']);

$row = FuncoesClass::getCursosID($_REQUEST['curso']);
$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

//trata tipo de insalubridade
if($row['tipo_insalubridade'] == 1){
    $insalubridade = "20%";
}elseif($row['tipo_insalubridade'] == 2){
    $insalubridade = "40%";
}

//trata mes abono
if($row['mes_abono'] == 0){
    $mes_abono = '';
}else{
    $mes_abono = mesesArray($row['mes_abono']);
}

//trata cbo
if($row['nome_cbo'] == ''){
    $cbo = '';
}else{
    $cbo = $row['nome_cbo'] . " - " . $row['cod'];
}

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

?>
<html>
    <head>
        <title>:: Intranet :: Fun��es</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="cursos.css" rel="stylesheet" type="text/css" /> 
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#editarFuncao").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#curso").val(key);
                        $("#form1").attr('action','edit_curso.php');
                        $("#form1").submit();
                    }
                });                                
            });
        </script>
        
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
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Fun��o <?php echo $row['nome_funcao']; ?></h2>
                    </div>
                </div>
                
                <input type="hidden" id="curso" name="curso" value="" />
                <fieldset>
                    <legend>Dados da Fun��o</legend>
                    <div class="colEsq">
                        <p><label class='first'>Regiao:</label> <?php echo $row['regiao']; ?></p>
                        <p><label class='first'>Projeto:</label> <?php echo $row['nome_projeto']; ?></p>
                        <p><label class='first'>Tipo de Contrata��o:</label> <?php echo $row['tipo_contratacao_nome']; ?></p>
                        <p><label class='first'>�rea:</label> <?php echo $row['area']; ?></p>
                        <p><label class='first'>CBO:</label> <?php echo $cbo; ?></p>
                        <p><label class='first'>Local:</label> <?php echo $row['local']; ?></p>
                        <p><label class='first'>In�cio:</label> <?php echo $row['data_ini']; ?></p>
                        <p><label class='first'>Final:</label> <?php echo $row['data_fim']; ?></p>
                        <p><label class='first'>Sal�rio:</label> <?php echo formataMoeda($row['salario']); ?></p>                        
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>M�s Abono:</label> <?php echo $mes_abono; ?></p>
                        <p><label class='first'>Insalubridade:</label> <?php echo $insalubridade; ?></p>
                        <p><label class='first'>Quantidade de Sal�rios:</label> <?php echo $row['qnt_salminimo_insalu']; ?></p>
                        <p><label class='first'>Valor:</label> <?php echo formataMoeda($row['valor']); ?></p>
                        <p><label class='first'>Parcelas:</label> <?php echo $row['parcelas']; ?></p>
                        <p><label class='first'>Quota:</label> <?php echo formataMoeda($row['quota']); ?></p>
                        <p><label class='first'>Parcela das Quotas:</label> <?php echo $row['num_quota']; ?></p>
                        <p><label class='first'>Qtd. M�xima de Contrata��o:</label> <?php echo $row['qnt_maxima']; ?></p>
                        <p><label class='first'>Descri��o:</label> <?php echo $row['descricao']; ?></p>
                    </div>
                </fieldset>
                
                <?php                 
                while($rst = mysql_fetch_array($sql)){
                    
                    //trata folga
                    if ($rst['folga'] == "3") {
                        $folga = "FINAL DE SEMANA";
                    } elseif ($rst['folga'] == "1") {
                        $folga = "FOLGA NO S�BADO";
                    } elseif ($rst['folga'] == "2") {
                        $folga = "FOLGA NO DOMINGO";
                    } elseif ($rst['folga'] == "5") {
                        $folga = "PLANTONISTA";
                    } else {
                        $folga = "SEM FOLGAS";
                    }
                ?>
                
                <fieldset class="horario">
                    <legend>Dados do Hor�rio</legend>
                                           
                    <div id="esquerda">
                        <p><label class='first'>Nome do Hor�rio:</label> <?php echo $rst['nome']; ?></p>
                        <p><label class='first'>Observa��es:</label> <?php echo $rst['obs']; ?></p> 
                        <p><label class='first'>Horas M�s:</label><?php echo $rst['horas_mes']; ?></p>
                    </div>

                    <div id="colunaDir">                        
                        <p><label class='first'>Dias M�s:</label><?php echo $rst['dias_mes']; ?></p>
                        <p><label class='first'>Dias Semana:</label><?php echo $rst['dias_semana']; ?></p>
                        <p><label class='first'>Folgas:</label><?php echo $folga; ?></p>
                    </div>
                        
                    <p>
                        <label class='first'>Preenchimento:</label>
                        <span class="preenche"><?php echo $rst['entrada_1']; ?></span>
                        <span class="preenche"><?php echo $rst['saida_1']; ?></span>
                        <span class="preenche"><?php echo $rst['entrada_2']; ?></span>
                        <span class="preenche"><?php echo $rst['saida_2']; ?></span>
                    </p>
                </fieldset>
                
                <?php } ?>
                
                <p class="controls">
                    <input type="submit" class="button bt-image" value="Editar" name="editarFuncao" id="editarFuncao" data-type="editar" data-key="<?php echo $row['id_curso']; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
            </form>
        </div>
    </body>
</html>