<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include("../../classes/ProjetoClass.php");
include("../../classes/ContraChequeClass.php");
include "../../classes/clt.php";
include("../../funcoes.php");
include('../../wfunction.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$contraObj = new Contracheque();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objProjeto = new ProjetoClass();
$ClassCLT = new clt();
// lista de projetos (usado no menu esquerdo)
$projetosList = $objProjeto->getProjetos($id_regiao);

// define qual o projeto será exibido em tela
$projeto_atual = $projetosList[0]['id_projeto'];

//$id_regiao 	= $_REQUEST['id_regiao'];
$id_clt = $_REQUEST['id_clt'];
$id_folha = $_REQUEST['id_folha'];
//print_r($_REQUEST);


$ListaParticipantes = $contraObj->listaParticipantesContra($id_folha);

// tudo que está aqui deve ir para uma classe ----------------------------------

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-geracontra", "ativo"=>"Contracheque Individual");
$breadcrumb_pages = array("Gestão de RH"=>"../", "Contracheque"=>"../contracheque/solicita2.php");
?>
<!DOCTYPE html>
<html>
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
            .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus{
                background-color: #F58634;
            }
            .nav-pills a{
                color: #F58634;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">                    
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Recibos de Pagamentos</small></h2></div>
                    <!--<h3>Contracheques <small>Recibos de Pagamentos</small></h3>-->
                    <!--p><a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a></p-->
                    
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Cod</th>
                                <th>Nome</th>
                                <th>Atividade</th>
                                <th>Valor</th>
                                <th class="text-center">Gerar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ListaParticipantes as $participantes) { ?>
                                <tr class="novalinha <?= $classcor ?>">
                                    <td><?= $participantes['cod'] ?></td>
                                    <td><?= $participantes['nome'] ?></td>
                                    <td><?= $participantes['nome_curso'] ?></td>
                                    <td>R$ <span class="pull-right"><?= number_format($participantes['salliquido'],2,",",".") ?></span></td>
                                    <td class="text-center">
                                        <?php if($participantes['demitido'] == 1){ echo '<i class="text-default fa fa-minus disabled" alt="Rescisao" title="Rescisao"></i>'; }else{ ?>
                                        <?php // if($participantes['status_clt'] >= 60){ echo '<i class="text-default fa fa-minus disabled" alt="Rescisao" title="Rescisao"></i>'; }else{ ?>
                                        <a href="contra_cheque_oo.php?enc=<?= str_replace("+", "--", encrypt("{$participantes['id_regiao']}&{$participantes['id_clt']}&{$id_folha}")) ?>" target="_blank" title="Gerar PDF" class="gera-contra btn btn-default btn-xs">                                            
                                            <i class="text-danger fa fa-file-pdf-o" alt="Gerar PDF" title="Gerar PDF"></i>
                                        </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <form action="#" method="post" id="form-geracontra">
                        <input type="hidden" name="enc" id="enc" />
                        <input type="hidden" name="home" id="home" value="" />
                    </form>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/contracheque/listaIndividual.php.js"></script>
    </body>
</html>
