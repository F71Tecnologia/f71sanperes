<?php
include('../conn.php');
require('../rh/fpdf/fpdf.php');
require('../classes/InformeRendimentoClass.php');
include('../wfunction.php');

$usuario = carregaUsuario();
$msg = null;
$inf = new InformeRendimentoClass($usuario['id_master']);
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$bloqueado = false;     //TIRANDO O BLOQUEIO, A PARTIR DE HJ PODEM GERAR 29/02/2016

if (validate($_REQUEST['gerar']) || validate($_REQUEST['gerar2'])) {
    
    if(isset($_REQUEST['gerar2']) && !empty($_REQUEST['gerar2'])){
        $_REQUEST['tipo'] = 1;
        //echo $_REQUEST['id'];exit;
    }
    
    $inf->setAnoBase($_REQUEST['ano']);
    $inf->setTipo($_REQUEST['tipo']);
    $inf->iniciaFpdf();
    $qr_participante = $inf->getParticipante($_REQUEST['id']);
    
    while ($participante = mysql_fetch_assoc($qr_participante)) {
        $meses = array();
        $valorMes = array();
        $multVinculos['total'] = null;
        $nome = $participante['nome'];
        $nomeFile = "Informe_".normalizaNometoFile($nome);
        $multVinculos = $inf->verificaDuplicidade(str_replace(array(".","-"),"",$participante['cpf']),$participante['tipo_contratacao']);
        
        if($_REQUEST['tipo'] == 2){
            $campoID = "id_clt";
        }else{
            $campoID = "id_autonomo";
        }
        
        if($multVinculos['total'] > 1){
            $ARidClt = null;
            foreach($multVinculos['rs'] as $multClt){
                $ARidClt[] = $multClt[$campoID];
            }
            $id = implode(",",$ARidClt);
        }else{
            $id = $participante['id_clt'];
        }
        
        $inf->setParticipante($participante);
        
        //RODA A QUERY E PEGA OS VALORES E COLOCA NAS VARIAVEIS PUBLICAS
        $inf->getDadosFolhas($id);
        $inf->getDadosDecimoTerceiro($id);
        
        //SO PROCURA RESCISÃO E FERIAS SE FOR DO TIPO CLT
        if ($_REQUEST['tipo'] == '2') {
            $inf->getDadosFerias($id);      //PROCURA FÉRIAS PARA POPULAR AS VARIAVEIS PUBLICAS
            $inf->getDadosExtra($id);
            //$inf->getDadosPlanoSaude($id); LAGOS NÃO TEM PLANO DE SAUDE
            
           
//            echo "<pre>";
//                print_r($inf);
//            echo "</pre>";
//            exit("index dentro do gerar");
            $inf->getDadosPensaoAlimenticia($id);
                         
            if($inf->anoBase >= 2015 || $id == 6938){
                $inf->getDadosRescisao2015($id);
            }else{
                $inf->getDadosRescisao($id);    //PROCURA RESCISAO PARA POPULAR AS VARIAVEIS PUBLICAS
            }
        }
                
        //NORMALIZA VALORES, REMOVENDO VALORES NEGATIVOS
        $inf->normalizaValores();
        
        //VALIDA GERAÇÃO DO PDF, CASO TODOS OS VALORES SEJAM VAZIOS, NÃO GERA PDF PARA TAL PESSOA
        $geraPDF = $inf->validaValores();
        
        if ($geraPDF) {
            $inf->setFileName($nomeFile.".pdf");
            $inf->geraPdf();
            $inf->finalizaPdf();
        }

        $inf->limpaVariaveis();
        
    }
    
    if ($geraPDF) {
        $inf->downloadFile();
        exit;
    }else{
        $msg = "Não foi encontrado valores para o Funcionário ({$nome}) no ano escolhido ({$_REQUEST['ano']})";
    }
}

//DADOS PARA A PRIMEIRA TELA, ANTES DO POST
$optAnos = $inf->montaOptionsAnos();
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date("Y") - 1;

$tipo = "";
$id_colaborador = "";
if(validate($_REQUEST['bol'])){
    $tipo = 1;
    $id_colaborador = $_REQUEST['bol'];
    $table = "autonomo";
    $keyTable = "id_autonomo";
}elseif(validate($_REQUEST['clt'])){
    $tipo = 2;
    $id_colaborador = $_REQUEST['clt'];
    $table = "rh_clt";
    $keyTable = "id_clt";
}else{
    $tipo = 3;
    $id_colaborador = $_REQUEST['bol'];
    $table = "autonomo";
    $keyTable = "id_autonomo";
}

if(isset($_REQUEST['cpf'])){
    $cpf = $_REQUEST['cpf'];
    $tipo = 2;
    $id_colaborador = $_REQUEST['clt'];
    $table = "rh_clt";
    $keyTable = "id_clt";
    $rowClt['cpf'] = $cpf;
}else{
    $rowClt = montaQueryFirst($table, "{$keyTable},cpf", "{$keyTable} = {$id_colaborador}");
}


//VERIFICA DUPLICIDADE
$rowDuploClt = montaQuery($table, "{$keyTable},cpf,id_projeto", "cpf = '{$rowClt['cpf']}'");
$rowDuploAut = montaQuery('autonomo', "id_autonomo,cpf,id_projeto", "cpf = '{$rowClt['cpf']}'");
$projetos = montaQuery("projeto", "id_projeto,nome");
$rowpro = array();
foreach($projetos as $pro){
    $rowpro[$pro['id_projeto']] = $pro['nome'];
}
    
$roMaster = montaQueryFirst("master", "nome", "id_master = {$usuario['id_master']}");
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Informe de Rendimentos em Lote");
$breadcrumb_pages = array("Gestão de RH" => "../rh/principalrh.php");

if($_COOKIE['logado'] == 179 || $_COOKIE['logado'] == 158 || isset($_REQUEST['dirf'])){
    $bloqueado = false;
}

$filename = "{$rowClt['cpf']}.pdf";
$path = "2016/";
$arquivoDown = "";
if(is_file($path.$filename)){
    $arquivoDown = "<a href='{$path}{$filename}' target='_blank' class='btn btn-info'><i class='fa fa-save'></i> Gerar Informe</a>";
}

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Informe de Rendimentos</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Informe de Rendimentos individual</small></h2></div>
            <form action="" method="post" name="form1" id="form1"  class="form-horizontal">
                <input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo ?>" />
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print">Ano Base/Calend</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optAnos, $anoSel, "id='ano' name='ano' class='form-control'") ?>
                            </div>
                        </div>
                        <?php if(count($rowDuploClt) > 1){ ?>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print">Multimplo vinculo detectado:</label>
                            <div class="col-sm-4">
                                <?php foreach($rowDuploClt as $clt){
                                    echo "<p> <input type='radio' id='id' name='id' value='{$clt[$keyTable]}'/> ".$rowpro[$clt['id_projeto']]."</p>";
                                }?>
                            </div>
                        </div>
                        <?php }else{ ?>
                        <input type="hidden" name="id" id="id" value="<?php echo $id_colaborador ?>" />
                        <?php } ?>
                        
                        <?php if(count($rowDuploAut) > 1){ ?>
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print">Vinculo de Autonomo detectado:</label>
                            <div class="col-sm-4">
                            <?php foreach($rowDuploAut as $clt){
                                echo "<p> <input type='radio' id='id' name='id' value='{$clt['id_autonomo']}'/> ".$rowpro[$clt['id_projeto']]."</p>";
                            }?>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php echo $arquivoDown ?>
                        <?php if($bloqueado){ ?>
                            <div class="alert alert-warning">Informes em análise</div>
                        <?php }else{ ?>
<!--                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                            <button type="submit" name="gerar2" id="gerar2" value="Gerar2" class="btn btn-info"><span class="fa fa-file-pdf-o"></span> Gerar PDF Autonomo</button>-->
                        <?php } ?>
                    </div>
                </div>
            </form>

            <?php 
            if($msg!==null){
                echo "<div id='message-box' class='message-yellow'><p>{$msg}</p></div>";
            }?>

        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>