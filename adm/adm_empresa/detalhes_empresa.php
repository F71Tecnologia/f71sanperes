<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/EmpresaClass.php');

$empresa = $_REQUEST['empresa'];

$usuario = carregaUsuario();
$row = getEmpresaID($empresa);

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();
?>
<html>
    <head>
        <title>:: Intranet :: Empresa</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="empresa.css" rel="stylesheet" type="text/css" /> 
        <link href='http://fonts.googleapis.com/css?family=Exo+2' rel='stylesheet' type='text/css' />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $("#editarEmpresa").click(function(){
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    
                    if (action === "editar") {
                        $("#empresa").val(key);
                        $("#form1").attr('action','form_empresa.php');
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
                        <h2>Empresa <?php echo $row['nome']; ?></h2>
                    </div>
                </div>
                           
                <input type="hidden" id="empresa" name="empresa" value="" />
                <fieldset>
                    <legend>Dados da Empresa</legend>
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
                
                <fieldset>
                    <legend>Dados do FGTS</legend>
                    <div class="colunaEsq">
                        <p><label class='first'>CNPJ Matriz:</label> <?php echo $row['cnpj_matriz']; ?></p>
                        <p><label class='first'>Banco:</label> <?php echo $row['banco']; ?></p>
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>Agência:</label> <?php echo $row['agencia']; ?></p>
                        <p><label class='first'>Conta:</label> <?php echo $row['conta']; ?></p>
                    </div>
                </fieldset>
                
                <fieldset>
                    <legend>Dados do INSS</legend>
                    <div class="colunaEsq">
                        <p><label class='first'>FPAS:</label> <?php echo $row['fpas']; ?></p>
                        <p><label class='first'>Tipo:</label> <?php echo $row['tipo_fpas']; ?></p>
                        <p><label class='first'>Porte:</label> <?php echo $row['porte']; ?></p>
                        <p><label class='first'>Natureza Jurídica:</label> <?php echo $row['natureza']; ?></p>
                        <p><label class='first'>Capital Social:</label> <?php echo formataMoeda($row['capital']); ?></p>
                        <p><label class='first'>Início das Atividades:</label> <?php echo $row['data_inicio']; ?></p>
                        <p><label class='first'>Simples:</label> <?php echo $row['simples']; ?></p>
                    </div>
                    <div class="colunaDir">
                        <p><label class='first'>PAT:</label> <?php echo $row['pat']; ?></p>
                        <p><label class='first'>% Empresa:</label> <?php echo $row['p_empresa'] * 100 . "%"; ?></p>
                        <p><label class='first'>% Acidente de Trabalho:</label> <?php echo $row['p_acid_trabalho'] * 100 . "%"; ?></p>
                        <p><label class='first'>% Prolabora / Autônomo:</label> <?php echo $row['p_prolabora'] * 100 . "%"; ?></p>
                        <p><label class='first'>% Terceiros:</label> <?php echo $row['p_terceiros'] * 100 . "%"; ?></p>
                        <p><label class='first'>Cód. Terceiros:</label> <?php echo $row['terceiros']; ?></p>
                        <p><label class='first'>% Isen. Emp. Filantrópicas:</label> <?php echo $row['p_filantropicas'] * 100 . "%"; ?></p>
                    </div>
                </fieldset>
                
                <p class="controls">
                    <input type="submit" class="button bt-image" value="Editar" name="editarEmpresa" id="editarEmpresa" data-type="editar" data-key="<?php echo $row['id_empresa']; ?>" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
            </form>
        </div>
    </body>
</html>