<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FeriadosClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$id_regiao = $usuario['id_regiao'];

$objFeriado = new FeriadosClass();


$feriados = $objFeriado->getFeriadoAll();
//$total_feriado = mysql_num_rows($result);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['regiao'])) {
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['regiao'])) {
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['regiao_select'])) {
    $regiaoR = $_SESSION['regiao_select'];
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administração de Feriados</title>
        <link rel="shortcut icon" href="../../favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
        <style>
            .bt-image{
                width: 18px;
                cursor: pointer;
            }            
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-lg-12">
                    <form action="#" method="post" name="form1" id="form1" enctype="multipart/form-data" >

                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>

                        <h3>Administração geral dos Feriados</h3>


                        <!--resposta de algum metodo realizado-->
                        <?php if (isset($_SESSION['MESSAGE'])) { ?>
                            <div class="alert <?php echo $_SESSION['MESSAGE_COLOR']; ?> alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <?php
                                echo $_SESSION['MESSAGE'];
                                session_destroy();
                                ?>
                            </div>
                        <?php } ?>

                        <input type="hidden" id="feriado" name="feriado" value="" />

                        <div class="panel panel-default">
                            <div class="panel-body">                        
                                <!--<div class="text-right"><input type="submit" class="btn btn-primary" value="Novo Feriado" name="novo" id="novoFeriado" /></div>-->
                                <div class="text-right"><button type="submit" class="btn btn-primary" name="novo" id="novoFeriado" ><i class="fa fa-plus"></i> Novo Feriado</button></div>
                            </div>
                        </div>
                        <?php if (count($feriados) > 0) { ?>
                            <br/>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Cód.</th>
                                        <th>Data</th>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Móvel</th>
                                        <th>Região</th>
                                        <th colspan="2">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feriados as $row) { ?>
                                        <tr  id="<?php echo $row['id_feriado']; ?>">
                                            <td><?php echo $row['id_feriado']; ?></td>
                                            <td><?php echo $row['data_m']; ?></td>
                                            <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                            <td><?php echo $row['tipo']; ?></td>
                                            <td><?php echo ($row['movel'] == 0) ? $movel = 'Não' : $movel = 'Sim'; ?></td>
                                            <td><?php echo ($row['nome_regiao'] != '') ? $regiao_f = $row['nome_regiao'] : $regiao_f = 'Federal'; ?></td>                                
                                            <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_feriado']; ?>" alt="" /></td>
                                            <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-type="excluir" data-key="<?php echo $row['id_feriado']; ?>" alt="" /></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <br/>
                            <div id='message-box' class='message-yellow'>
                                <p>Nenhum registro encontrado</p>
                            </div>
                        <?php } ?>
                    </form>

                </div><!-- /.col-lg-12 -->

            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js" type="text/javascript"></script>

        <script src="../../resources/js/rh/feriados/index2.js"></script>

    </body>
</html>