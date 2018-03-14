<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/EduEventoClass.php');

echo $evento = $_REQUEST['evento'];

//$eventosClass = new EduEventosClass();

$usuario = carregaUsuario();
$row_eventos = getListEvento($evento);
?>
<html>
    <head>
        <title>:: Intranet :: Evento</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="feriado.css" rel="stylesheet" type="text/css" /> 
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css' />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#editarEvento").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#evento").val(key);
                        $("#form1").attr('action','form_evento.php');
                        $("#form1").submit();
                    }
                });                                
            });
        </script>
        
        <style>
            .novaintra #content form fieldset{
                margin-top: 10px;
                padding: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }           
            .flut{
                float: right;
                width: 380px;   
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 1000px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Evento <?php echo $row['nome']; ?></h2>
                    </div>
                </div>
                           
                <input type="hidden" id="evento" name="evento" value="" />
                <fieldset>
                    <legend>Dados da Escola</legend>
                    <div class="colunaEsq">
                        <p><label class='first'>Regiao:</label> <?php echo "{$row['id_regiao']} - {$row['nome_regiao']}"; ?></p>
                        <p><label class='first'>Projeto:</label> <?php echo "{$row['id_projeto']} - {$row['nome_projeto']}"; ?></p>
                        <p><label class='first'>Nome Fantasia:</label> <?php echo $row['nome']; ?></p>
                        <p><label class='first'>Razão Social :</label> <span class="flut"><?php echo $row['razao']; ?></span></p>
                        <div class="clear"></div>
                        <p><label class='first'>Endereço:</label> <span class="flut"><?php echo $row['endereco']; ?></span></p>
                        <div class="clear"></div>
                        <p><label class='first'>Inscrição Municipal:</label> <?php echo $row['im']; ?></p>
                        <p><label class='first'>Inscriçao Estadual:</label> <?php echo $row['ie']; ?></p>
                        <p><label class='first'>CNPJ:</label> <?php echo $row['cnpj']; ?></p>
                        <p><label class='first'>Tipo de CNPJ:</label> <?php echo $row['tipo_cnpj']; ?></p>                        
                        <p><label class='first'>Telefone:</label> <?php echo $row['tel']; ?></p>
                        <p><label class='first'>Fax:</label> <?php echo $row['fax']; ?></p>
                        <p><label class='first'>Email:</label> <?php echo $row['email']; ?></p>
                        <p><label class='first'>Site:</label> <a href="<?php echo $row['site']; ?>" target="_blank"><?php echo $row['site']; ?></a></p>
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>Responsável:</label> <?php echo $row['responsavel']; ?></p>
                        <p><label class='first'>CPF:</label> <?php echo $row['cpf'];; ?></p>
                        <p><label class='first'>Cód. Acidentes de Trabalho:</label> <?php echo $row['acid_trabalho']; ?></p>
                        <p><label class='first'>Atividade:</label> <?php echo $row['atividade']; ?></p>
                        <p><label class='first'>Grupo:</label> <?php echo $row['grupo']; ?></p>
                        <p><label class='first'>Proprietários:</label> <?php echo $row['proprietarios']; ?></p>
                        <p><label class='first'>Familiares:</label> <?php echo $row['familiares']; ?></p>
                        <p><label class='first'>Tipo de Pagamento:</label> <?php echo $row['tipo_pg']; ?></p>                        
                        <p><label class='first'>Ano do 1º Exercício:</label> <?php echo $row['ano']; ?></p>
                    </div>
                </fieldset>                                
                
                <p class="controls">
                    <input type="submit" class="button bt-image" value="Editar" name="editarEvento" id="editarEvento" data-type="editar" data-key="<?php echo $row['id_evento']; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
            </form>
        </div>
    </body>
</html>