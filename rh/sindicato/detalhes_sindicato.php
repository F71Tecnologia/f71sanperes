<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/SindicatoClass.php');

$sindicato = $_REQUEST['sindicato'];

$usuario = carregaUsuario();
$row = getSindicatoID($sindicato);

session_write_close();
?>
<html>
    <head>
        <title>:: Intranet :: Sindicatos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="sindicato.css" rel="stylesheet" type="text/css" /> 
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css'>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#editarSindicato").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#sindicato").val(key);
                        $("#form1").attr('action','form_sindicato.php');
                        $("#form1").submit();
                    }
                });
            });
        </script>
        
        <style>
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .colunaDir{
                width: auto;
                min-width: 800px;
                margin-left: 210px;                
                padding: 10px;
            }            
            .colunaEsq{
                width: 200px;
                float: left;
                width: 50%;
                /*margin-top: -10px;*/
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 1000px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Detalhes de Sindicato</h2>
                    </div>
                </div>
                
                <input type="hidden" id="sindicato" name="sindicato" value="" />
                <fieldset>
                    <legend>Dados do Sindicato</legend>
                    <p><label class='first'>Regiao:</label> <?php echo "{$row['reg_id']} - {$row['nome_regiao']}"; ?></p>
                    <p><label class='first'>Nome:</label><?php echo $row['nome']; ?></p>
                    <p><label class='first'>Endereço:</label> <?php echo $row['endereco']; ?></p>
                    <div class="colunaEsq">
                        <p><label class='first'>CNPJ:</label> <?php echo mascara_string("##.###.###/####-##", $row['cnpj']); ?></p>
                        <p><label class='first'>Fax:</label> <?php echo mascara_stringTel($row['fax']); ?></p>
                        <p><label class='first'>Celular:</label> <?php echo mascara_stringTel($row['cel']); ?></p>
                        <p><label class='first'>Telefone:</label> <?php echo mascara_stringTel($row['tel']); ?></p>
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>Contato:</label> <?php echo $row['contato']; ?></p>
                        <p><label class='first'>Email:</label> <?php echo $row['email']; ?></p>
                        <p><label class='first'>Site:</label> <a href="http://<?php echo $row['site']; ?>" target="_blank"><?php echo $row['site']; ?></a></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados da Categoria</legend>
                    <div class="colunaEsq">
                        <p><label class='first'>Mês de desconto:</label> <?php echo mesesArray($row['mes_desconto']); ?></p>
                        <p><label class='first'>Piso Salarial:</label> <?php echo ($row['piso'] != '') ? formataMoeda($row['piso']) : ''; ?></p>
                        <p><label class='first'>Férias (meses):</label> <?php echo $row['ferias']; ?></p>
                        <p><label class='first'>13 (meses):</label> <?php echo $row['decimo_terceiro']; ?></p>
                        <p><label class='first'>Patronal:</label> <?php echo ($row['pratonal'] == 1) ? 'SIM' : 'NÃO'; ?></p>
                        <p><label class='first'>Entidade Sindical:</label> <?php echo $row['entidade']; ?></p>
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>Mês de Dissídio:</label> <?php echo mesesArray($row['mes_dissidio']); ?></p>
                        <p><label class='first'>Multa do FGTS:</label> <?php echo ($row['multa'] != '') ? "{$row['multa']}%" : ""; ?></p>
                        <p><label class='first'>Fração:</label> <?php echo $row['fracao']; ?></p>
                        <p><label class='first'>Recisão:</label> <?php echo $row['recisao']; ?></p>
                        <p><label class='first'>Evento Relacionado:</label> <?php echo ($row['evento'] == '5019') ? 'CONTRIBUIÇÃO SINDICAL' : ''; ?></p>
                    </div>
                </fieldset>
                
                <p class="controls">
                    <input type="submit" class="button bt-image" value="Editar" name="editarSindicato" id="editarSindicato" data-type="editar" data-key="<?php echo $row['id_sindicato']; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
            </form>
        </div>
    </body>
</html>