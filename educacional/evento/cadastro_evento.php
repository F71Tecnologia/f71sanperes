<?php


/**
 * @author Juarez Garritano
 * @criacao Criação da página solicitada por Sabino Junior.
 * @conteudo Página referente ao "Módulo Educacional.
 */

if(empty($_COOKIE['logado'])){
   header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
   exit;
} 

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/regiao.php');
include "../../classes_permissoes/regioes.class.php";
include('../../classes/EduEventosClass.php');
require_once "../../classes/LogClass.php";


$REG = new Regioes();
$LOG = new Log();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$eventosClass = new EduEventosClass();

if(isset($_REQUEST['enviar'])){

     //DADOS DO EVENTO
        $id_evento = $_REQUEST['id_evento'];
           
        $arrayDados = array(
        'id_usuario_cad' => $usuario['id_funcionario'],
        'data_cad' => date('Y-m-d'),
        'nome' => $_REQUEST['nome'],
        'data_evento' => implode("-", array_reverse(explode("/", $_REQUEST['data_evento'])))
    );
        
//        
//    echo "<pre>";
//    print_r($arrayDados);
//    echo "</pre>";
//    
        
        //CADASTRO DOS DADOS DA EVENTO
    if($_REQUEST['procedimento'] == "CADASTRAR"){
        
        //INSERINDO A EVENTO NO BANCO
        $eventosClass->insereEvento($arrayDados);

        header('Location: visualizar_eventos.php');
    }
    
        //EDIÇÃO DOS DADOS DA EVENTO
    if($_REQUEST['procedimento'] == "EDITAR"){
        
        //EDITANDO OS DADOS DA EVENTO NO BANCO
        $eventosClass->editaEvento($id_eventos, $arrayDados);
        
        header('Location: visualizar_eventos.php');
    }
    
}

//RESGATANDO OS DADOS PARA SEREM VISUALIZADOS ASSIM QUE ABRIR A PÁGINA DE CADASTRO/EDIÇÃO
if (isset($_REQUEST['id_evento'])) {
    $id_evento = $_REQUEST['id_evento'];
    $row_eventos = $eventosClass->verEvento($id_evento);
    
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Cadastro/Edição de Eventos", "id_form" => "form1");
//$breadcrumb_pages = array("Unidade Escolar" => "../unidade_escolar.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <!--<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css"/>-->

        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all"/>
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen"/>
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen"/>
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
        <!--<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen"/>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>

        <title>::Intranet:: Cadastro/Edição de Eventos</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Cadastro/Edição de Eventos</small></h2></div>
            <form action="cadastro_evento.php" method="post" name="form1" id="form1" class="form-horizontal top-margin1"
                  enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastrar/Editar</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nome do Evento</label>
                            <div class="col-sm-4">
                                <input type="text" name="nome" id="nome" class="form-control" value="<?php echo $row_eventos[1]['nome'] ?>"/>
                            </div>
                        
                            <label class="col-sm-1 control-label hidden-print" > Data</label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" name="data_evento" id="data" class="form-control data" value="<?php echo implode("/", array_reverse(explode("-", $row_eventos[1]['data_evento']))) ?>"/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                        
                    <div class="panel-footer text-right hidden-print controls">
                        <input type="hidden" name="id_evento" id="id_evento" value="<?php echo $_REQUEST['id_evento'] ?>" />
                        <?php if(isset($_REQUEST['id_evento'])) { ?>
                            <input type="hidden" name="procedimento" value="EDITAR"/>
                        <?php } else { ?>
                            <input type="hidden" name="procedimento" value="CADASTRAR"/>
                        <?php } ?>

                        <button type="submit" name="enviar" id="enviar" value="ENVIAR" class="btn btn-primary"><span class="fa fa-save"></span> Salvar</button>
                    </div>
                </div>
               
            </form>

            <div class="clear"></div>
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.8.3.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main_bts.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../jquery/mascara/jquery-maskedinput-1.4.1.js"></script>
        
        <script type="text/javascript">
               //Início function
                $(function () {
                });
                //fim function
        </script>
    </body>
</html>